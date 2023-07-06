<?php
	require_once( '../pgConstants.php' );
	require_once( 'dbSetup.php' );
	require_once( 'ChkTimeOut.php' );
	
	$_sessType = $_SESSION[ 'sessType' ];
	$_sessLang = $_SESSION[ 'sessLang' ];
	$_sessUsr = $_SESSION[ 'usrName' ];	
	$_icoName = isset($_SESSION[ 'icoName' ]) ? $_SESSION[ 'icoName' ] : null;
	$useChn = ( $_SESSION[ 'sessLang' ] == SESS_LANG_CHN );	


function readInCareOf() { // returns a string reflecting a <select> html element
	global $_db;
	$inCareOfNames = array();
	$usrNames = array();

	$sql1 = "SELECT `UsrName` FROM `inCareOf` WHERE `UsrName` NOT IN "
		  . "(SELECT DISTINCT `pwUsrName` FROM `pw2Usr`);";
	$sql2 = "SELECT `UsrName` FROM `Usr` WHERE `UsrName` NOT IN "
		  . "(SELECT DISTINCT `pwUsrName` FROM `pw2Usr`);";
	  
	$_db->query( "LOCK TABLES `inCareOf` READ，`pw2Usr` READ, `Usr` READ;" );
	$rslt = $_db->query( $sql1 );
	if ( $rslt->num_rows > 0) {
		$inCareOfNames = $rslt->fetch_all(MYSQLI_ASSOC);
	}
	$rslt = $_db->query( $sql2 );
	$_db->query( "UNLOCK TABLES;" );
	if ( $rslt->num_rows > 0 ) {
		$usrNames = $rslt->fetch_all(MYSQLI_ASSOC);
	}

	$tpl = new HTML_Template_IT("./Templates");
	$tpl->loadTemplatefile("inCareOfChoice.tpl", true, true);
	$tpl->setCurrentBlock("InCareOf");

	foreach ( $inCareOfNames as $inCareOfName ) {
		$tpl->setCurrentBlock("Option");
		foreach ($inCareOfName as $key => $val ) {
			$tpl->setVariable("fldV", $val );	
		}
		$tpl->parse("Option");
	} // $inCareOfNames

	foreach ( $usrNames as $usrName ) {
		$tpl->setCurrentBlock("Option");
		foreach ($usrName as $key => $val ) {
			$tpl->setVariable("fldV", $val );	
		}
		$tpl->parse("Option");
	} // $usrNames

	$tpl->parse("InCareOf");
	$tmp = preg_replace( "/(^[\r\n]*|[\r\n]+)[\s\t]*[\r\n]+/", "\n", $tpl->get() );

	return preg_replace( "/(^\t*)/", "  ", $tmp );
} // function readInCareOf()

function readUsrPwRows() { // returns a string reflecting PaiWei dashboard data rows
    global $_db;
	$_lastRtrtDate = $_SESSION[ 'lastRtrtDate' ];

	$tblNames = array(	'C001A', 'W001A_4', 'DaPaiWei', 'L001A', 'Y001A', 'D001A', 'DaPaiWeiRed' );
	$pwTotal = array(	'C001A' => 0, 'W001A_4' => 0, 'DaPaiWei' => 0, 'L001A' => 0, 'Y001A' => 0,
						'D001A' => 0, 'DaPaiWeiRed' => 0, 'grandTotal' => 0 );
	$pwValidTotal = array(	'C001A' => 0, 'W001A_4' => 0, 'DaPaiWei' => 0, 'L001A' => 0, 'Y001A' => 0,
							'D001A' => 0, 'DaPaiWeiRed' => 0, 'grandTotal' => 0 );
	$pwInvalidTotal = array(	'C001A' => 0, 'W001A_4' => 0, 'DaPaiWei' => 0, 'L001A' => 0, 'Y001A' => 0,
								'D001A' => 0, 'DaPaiWeiRed' => 0, 'grandTotal' => 0 );
	$pwCtPerSheet = array(	'C001A' => 6, 'W001A_4' => 6, 'DaPaiWei' => 1, 'L001A' => 6, 'Y001A' => 6,
							'D001A' => 6, 'DaPaiWeiRed' => 1 );
	
	// 'PLC' user: list as the first user
	$sql = "SELECT DISTINCT `pwUsrName` FROM `pw2Usr` WHERE `pwUsrName` <> 'PLC' ORDER BY  `pwUsrName`;";
	$_db->query( "LOCK TABLES `pw2Usr` READ;" );
	$rslt = $_db->query( $sql );
	$usrNames = $rslt->fetch_all(MYSQLI_ASSOC);
	$_db->query("UNLOCK TABLES;");			 
	array_unshift($usrNames , array("pwUsrName" => "PLC"));
	
	$tpl = new HTML_Template_IT("./Templates");
	$tpl->loadTemplatefile("pwDashboard.tpl", true, true);
	$tpl->setCurrentBlock("dashboardBody");

	foreach ( $usrNames as $Name ) {
		$icoName = $Name[ 'pwUsrName' ];
		// paiwei total count of the user
		$icoTotal = 0; $icoValidTotal = 0; $icoInvalidTotal = 0;		

		$tpl->setCurrentBlock("dashboardRow");
		$tpl->setVariable( "icoName", $icoName );
		
		foreach ( $tblNames as $tblName ) {
			// query ALL paiwei count
			$_db->query("LOCK TABLES `pw2Usr` READ;");
			$sql = "SELECT COUNT(*) FROM `pw2Usr` WHERE `pwUsrName` = \"${icoName}\" AND `TblName` = \"${tblName}\";";
			$rslt = $_db->query($sql);
			$_db->query("UNLOCK TABLES;");
			$pwCt = $rslt->fetch_row()[0];			

			// query VALID paiwei count
			$_db->query("LOCK TABLES `pw2Usr` READ, `${tblName}` READ;");
			$sql = "SELECT COUNT(*) FROM `pw2Usr` INNER JOIN `${tblName}` ON `pw2Usr`.`pwID` = `${tblName}`.`ID` "
				 . "WHERE `pw2Usr`.`pwUsrName` = \"${icoName}\" AND `pw2Usr`.`TblName` = \"${tblName}\" "
				 . "AND `${tblName}`.`timestamp` > \"${_lastRtrtDate}\";";
			$rslt = $_db->query($sql);
			$_db->query("UNLOCK TABLES;");
			$pwValidCt = $rslt->fetch_row()[0];

			// calculate INVALID paiwei count: ALL - VALID
			$pwInvalidCt = $pwCt - $pwValidCt;

			// sum for the 'icoTotal' <td> of current <tr>
			$icoTotal += $pwCt;
			$icoValidTotal += $pwValidCt;
			$icoInvalidTotal += $pwInvalidCt;

			// sum for the 'sumRow' <tr>
			$pwTotal[ "${tblName}" ] += $pwCt;
			$pwValidTotal[ "${tblName}" ] += $pwValidCt;
			$pwInvalidTotal[ "${tblName}" ] += $pwInvalidCt;

			$tpl->setCurrentBlock("dashboardCell");
			$tpl->setVariable("tblName", $tblName );
			$tpl->setVariable("pwCt", $pwCt ); // by default, display ALL paiwei count
			$tpl->setVariable("pwValidCt", $pwValidCt );
			$tpl->setVariable("pwInvalidCt", $pwInvalidCt );
			$tpl->parse("dashboardCell");
		} // each TblName

		// sum for the 'grandTotal' <td> of the 'sumRow' <tr>
		$pwTotal[ 'grandTotal' ] += $icoTotal;
		$pwValidTotal[ 'grandTotal' ] += $icoValidTotal;
		$pwInvalidTotal[ 'grandTotal' ] += $icoInvalidTotal;

		$tpl->setCurrentBlock("icoSum");
		$tpl->setVariable("pwCt", $icoTotal );
		$tpl->setVariable("pwValidCt", $icoValidTotal );
		$tpl->setVariable("pwInvalidCt", $icoInvalidTotal );
		$tpl->parse("icoSum");

		$notifyDisableStr = $icoInvalidTotal > 0 ? "" : "disabled";
		$tpl->setCurrentBlock("notifyBtn");
		$tpl->setVariable("notifyDisableStr", $notifyDisableStr);
		$tpl->parse("notifyBtn");
				
		$tpl->parse("dashboardRow");
	} // loop over all Names to construct row data	

	/* now the Summary Row */
	$tpl->setCurrentBlock( "sumRow" );
	// paiwei total sheet
	$pwShtTotal = 0; $pwValidShtTotal = 0; $pwInvalidShtTotal = 0;
	foreach( $tblNames as $tblName ) {
		// calculate paiwei sheets
		$pwSht = ceil( $pwTotal["${tblName}"] / $pwCtPerSheet["${tblName}"] );
		$pwValidSht = ceil( $pwValidTotal["${tblName}"] / $pwCtPerSheet["${tblName}"] );
		$pwInvalidSht = ceil( $pwInvalidTotal["${tblName}"] / $pwCtPerSheet["${tblName}"] );
		
		// sum for the 'grandTotal' <td>
		$pwShtTotal += $pwSht;
		$pwValidShtTotal += $pwValidSht;
		$pwInvalidShtTotal += $pwInvalidSht;

		$tpl->setCurrentBlock("sumCell");
		$tpl->setVariable("pwCt", $pwTotal[ "${tblName}" ] );
		$tpl->setVariable("pwValidCt", $pwValidTotal[ "${tblName}" ] );
		$tpl->setVariable("pwInvalidCt", $pwInvalidTotal[ "${tblName}" ] );
		$tpl->setVariable("pwSht", $pwSht );
		$tpl->setVariable("pwValidSht", $pwValidSht );
		$tpl->setVariable("pwInvalidSht", $pwInvalidSht );		
		$tpl->parse("sumCell");
	} // each tblName

	$tpl->setCurrentBlock("grandSumCell");
	$tpl->setVariable("pwCt", $pwTotal[ 'grandTotal' ] );
	$tpl->setVariable("pwValidCt", $pwValidTotal[ 'grandTotal' ] );
	$tpl->setVariable("pwInvalidCt", $pwInvalidTotal[ 'grandTotal' ] );
	$tpl->setVariable("pwSht", $pwShtTotal );
	$tpl->setVariable("pwValidSht", $pwValidShtTotal );
	$tpl->setVariable("pwInvalidSht", $pwInvalidShtTotal );		
	$tpl->parse("grandSumCell");

	$notifyDisableStr = $pwInvalidTotal[ 'grandTotal' ] > 0 ? "" : "disabled";
	$tpl->setCurrentBlock("notifyAllBtn");
	$tpl->setVariable("notifyDisableStr", $notifyDisableStr);
	$tpl->parse("notifyAllBtn");

	$tpl->parse("sumRow");
	$tpl->parse("dashboardBody");
	
	$tmp = preg_replace( "/(^[\r\n]*|[\r\n]+)[\s\t]*[\r\n]+/", "\n", $tpl->get() );
	return preg_replace( "/(^\t*)/", "  ", $tmp );
} // function readUsrPwRows()

/**********************************************************
 *				For dbLoadPaiweiDashboard			      *
 **********************************************************/
function loadPaiweiDashboard( $dbInfo ) {
	$rpt = array();
	$rpt[ 'inCareOfOptions' ] = readInCareOf();
	$rpt[ 'dashboardBody' ] = readUsrPwRows();	
	return $rpt;
} // function loadPaiweiDashboard()

/**********************************************************
 *				For dbUpdRtData			     			  *
 **********************************************************/
function updRtData( $dbInfo ) {
	global $_db;
	$rpt = array();	
	
    if ( strlen( $dbInfo['ID'] ) == 0 ) {
		$tupID = null;
		$sql = "INSERT INTO `pwParam` ( `rtrtDate`, `pwExpires`, `rtEvent`, `rtTemple`, `rtReason`, `rtVenerable`, `rtZhaiZhu`, `rtShouDu`, `lastRtrtDate`) VALUES "
			 . "( \"{$dbInfo['rtrtDate']}\", \"{$dbInfo['pwExpires']}\", \"{$dbInfo['rtEvent']}\", \"{$dbInfo['rtTemple']}\", \"{$dbInfo['rtReason']}\", \"{$dbInfo['rtVenerable']}\", \"{$dbInfo['rtZhaiZhu']}\", \"{$dbInfo['rtShouDu']}\", \"{$dbInfo['lastRtrtDate']}\");";
	} else {
		$tupID = $dbInfo[ 'ID' ];
		$sql = "UPDATE `pwParam` SET `rtrtDate` = \"{$dbInfo[ 'rtrtDate' ]}\", `pwExpires` = \"{$dbInfo[ 'pwExpires' ]}\", `rtEvent` = \"{$dbInfo[ 'rtEvent' ]}\", "
			 . "`rtTemple` = \"{$dbInfo[ 'rtTemple' ]}\", `rtReason` = \"{$dbInfo['rtReason']}\", `rtVenerable` = \"{$dbInfo['rtVenerable']}\", "
			 . "`rtZhaiZhu` = \"{$dbInfo['rtZhaiZhu']}\", `rtShouDu` = \"{$dbInfo['rtShouDu']}\", `lastRtrtDate` = \"{$dbInfo[ 'lastRtrtDate' ]}\" "
			 . "WHERE `ID` = \"{$tupID}\";";
	}
	
	$_db->query("LOCK TABLES `pwParam`;");
	$rslt = $_db->query( $sql );
	
    if ( $_db->affected_rows != 1 ) {
		$rpt[ 'ERR' ] = "資料庫發生錯誤；無法設定！最後所執行的資料庫指令為：\n {$sql}";
		$_db->query("UNLOCK TABLES;");
		return $rpt;
	}

	if ( $tupID == null ) $tupID = $_db->insert_id;
	$_db->query("UNLOCK TABLES;");
	$rpt[ 'SUCCESS' ] = $tupID;
    return $rpt;
} // function updRtData()

/**********************************************************
 *				For dbReadRtData			   			  *
 **********************************************************/
function readRtData( $dbInfo ) {
	global $_db, $_SESSION;
	$rpt = array ();

	$sql = "SELECT * FROM `pwParam` WHERE true;";
	$_db->query("LOCK TABLES `pwParam`;");
	$rslt = $_db->query( $sql );
	$_db->query("UNLOCK TABLES;");
	switch( $rslt->num_rows ) {
		case 0: // no record found
			$rpt[ 'rtrtDate' ] = "請輸入法會開始日期";
			$rpt[ 'pwExpires' ] = "請輸入牌位申請截止日期";
			$rpt[ 'rtEvent' ] = "";
			$rpt[ 'lastRtrtDate' ] = "請輸入上次法會日期";
			$rpt[ 'rtTemple' ] = "淨土念佛堂及圖書館";
			$rpt[ 'rtReason' ] = "請輸入法會因緣";
			$rpt[ 'rtVenerable' ] = "請輸入法會主法和尚";
			$rpt[ 'rtZhaiZhu' ] = "請輸入法會齋主";
			$rpt[ 'rtShouDu' ] = "請輸入法會受度人";
			return $rpt;
		case 1:
			$rsltArray = $rslt->fetch_all(MYSQLI_ASSOC)[0];
			$_SESSION[ 'lastRtrtDate' ] = $rsltArray[ 'lastRtrtDate' ];
			return $rsltArray;
		default:
			$rpt[ 'ERR' ] = "資料庫發生錯誤；無法讀取法會資料！最後所執行的資料庫指令為：\n {$sql}";
			return $rpt;
	}
} // function readRtData()

function setIcoName( $icoName ) {
	global $_db, $_SESSION;
	$adminEmail = $_SESSION[ 'usrEmail' ];

	// first check existence in the Usr table
	$_db->query("LOCK TABLES `Usr` READ;");
	$rslt = $_db->query("SELECT * FROM `Usr` WHERE `UsrName` = \"{$icoName}\";");
	$_db->query("UNLOCK TABLES;");

	if ( $rslt->num_rows > 0 ) return; // nothing to do

	// Add it into inCareOf table, if not exist; update email if exist
	$sql = "INSERT INTO `inCareOf` ( `UsrName`, `UsrEmail` ) VALUES ( \"{$icoName}\", \"{$adminEmail}\" ) "
		 . "ON DUPLICATE KEY UPDATE `UsrEmail` = \"{$adminEmail}\";";
	$_db->query("LOCK TABLES `inCareOf` WRITE;");
	$_db->query( $sql );
	$_db->query("UNLOCK TABLES;");
} // function setIcoName()

function updateIcoEmail( $icoName ) {
	global $_db, $_SESSION;
	$adminEmail = $_SESSION[ 'usrEmail' ];

	// first check existence in the Usr table
	$_db->query("LOCK TABLES `Usr` READ;");
	$rslt = $_db->query("SELECT * FROM `Usr` WHERE `UsrName` = \"{$icoName}\";");
	$_db->query("UNLOCK TABLES;");

	if ( $rslt->num_rows > 0 ) return; // nothing to do

	// update ico email
	$sql = "UPDATE `inCareOf` SET `UsrEmail` = \"{$adminEmail}\" WHERE `UsrName` = \"{$icoName}\";";
	$_db->query("LOCK TABLES `inCareOf` WRITE;");
	$_db->query( $sql );
	$_db->query("UNLOCK TABLES;");
} // function updateIcoEmail()

/**********************************************************
 *				For dashboardRedirect			   			  *
 **********************************************************/
function dashboardRedirect( $dbInfo ) {
	global $_SESSION;
	$rpt = array ();
	$_SESSION['icoName'] = $dbInfo[ 'icoName' ];
	
	switch( $dbInfo[ 'icoNameType' ] ) {
		case 'icoDerived':
			$_SESSION[ 'tblName' ] = $dbInfo[ 'tblName' ];
			// update the ico's email to the current admin user's email
			updateIcoEmail( $dbInfo['icoName'] );
			break;
		case 'icoSelected':
			$_SESSION[ 'tblName' ] = 'C001A';
			// update the ico's email to the current admin user's email
			updateIcoEmail( $dbInfo['icoName'] );
			break;
		case 'icoInput':
			$_SESSION[ 'tblName' ] = 'C001A';
			// insert ico if not exist
			setIcoName( $dbInfo['icoName'] );
			break;
	} // switch()
	$rpt[ 'redirect' ] = URL_ROOT . '/admin/PaiWei/index.php';
	return $rpt;
} // function dashboardRedirect()

/**********************************************************
 *				For readSessParam			   			  *
 **********************************************************/
function readSessParam( ) {
	$rpt = array();

	$rpt[ 'usrName'] = $_SESSION[ 'usrName' ];
	$rpt[ 'sessType' ] = $_SESSION[ 'sessType' ];
	$rpt[ 'sessLang' ] = $_SESSION[ 'sessLang' ];
	
	return $rpt;	
} // function readSessParam()

/**********************************************************
 *					Main Functional Code		    	   *
 **********************************************************/
$_dbReq = $_POST[ 'dbReq' ];

$_dbInfo = json_decode( $_POST [ 'dbInfo' ], true );
switch ( $_dbReq ) {
	case 'readSessParam':
		echo json_encode( readSessParam( $_dbInfo ), JSON_UNESCAPED_UNICODE );
		break;
	case 'dbReadRtData':
		echo json_encode( readRtData( $_dbInfo ), JSON_UNESCAPED_UNICODE );
		break;
	case 'dbUpdRtData':
		echo json_encode( updRtData( $_dbInfo ), JSON_UNESCAPED_UNICODE );
		break;
	case 'dbLoadPaiweiDashboard':
		echo json_encode( loadPaiweiDashboard( $_dbInfo ), JSON_UNESCAPED_UNICODE );
		break;
	case 'dashboardRedirect':
		echo json_encode( dashboardRedirect( $_dbInfo ), JSON_UNESCAPED_UNICODE );
		exit;
} // switch()

$_db->close();
?>
