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
	$tblNames = array(	'C001A', 'W001A_4', 'DaPaiWei', 'L001A', 'Y001A', 'D001A', 'DaPaiWeiRed' );
	$pwTotal = array(	'C001A' => 0, 'W001A_4' => 0, 'DaPaiWei' => 0,
						'L001A' => 0, 'Y001A' => 0, 'D001A' => 0, 'DaPaiWeiRed' => 0, 'grandTotal' => 0 );
	$pwSheets = array(	'C001A' => 0, 'W001A_4' => 0, 'DaPaiWei' => 0,
						'L001A' => 0, 'Y001A' => 0, 'D001A' => 0, 'DaPaiWeiRed' => 0, 'grandTotal' => 0 );
	
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

		$_db->query("LOCK TABLES `pw2Usr` READ;");
		$rslt = $_db->query("SELECT `TblName` FROM `pw2Usr` WHERE `pwUsrName` = \"${icoName}\";");
		$_db->query("UNLOCK TABLES;");

		$tpl->setCurrentBlock("dashboardRow");
		$tpl->setVariable( "icoName", $icoName );
		$tpl->setVariable( "icoTotal", $rslt->num_rows );
		$pwTotal[ 'grandTotal' ] += $rslt->num_rows;

		foreach ( $tblNames as $tblName ) {
			$_db->query("LOCK TABLES `pw2Usr` READ;");
			$sql = "SELECT `TblName` FROM `pw2Usr` WHERE `pwUsrName` = \"${icoName}\" AND `TblName` = \"${tblName}\";";
			$rslt = $_db->query($sql);
			$_db->query("UNLOCK TABLES;");	
			$pwTotal[ "${tblName}" ] += $rslt->num_rows;
			$tpl->setCurrentBlock("dashboardCell");
			$tpl->setVariable("tblName", $tblName );
			$tpl->setVariable("tblTotal", $rslt->num_rows );
			$tpl->parse("dashboardCell");
		} // each TblName		
		$tpl->parse("dashboardRow");
	} // loop over all Names to construct row data		
	
	foreach( $tblNames as $tblName ) {
		$pwCounts = $pwTotal[ "${tblName}" ];
		$pwSheets[ "$tblName" ] = (int)( $pwCounts / 6 ) + ( ( $pwCounts % 6 ) > 0 );
		if ( $tblName == "DaPaiWei" || $tblName == "DaPaiWeiRed" ) { $pwSheets[ "$tblName" ] = $pwCounts; }
		$pwSheets[ 'grandTotal' ] += $pwSheets[ "$tblName" ];
	}
	/* now the Summary Row */
	$tpl->setCurrentBlock( "sumRow" );
	$tpl->setVariable("grandTotal", $pwTotal[ 'grandTotal' ]);
	$tpl->setVariable("grandSheets", $pwSheets[ 'grandTotal' ]);
	foreach( $tblNames as $tblName ) {
		$tpl->setCurrentBlock("sumCell");
		$tpl->setVariable("pwSum", $pwTotal[ "${tblName}" ]);
		$tpl->setVariable("pwSht", $pwSheets[ "${tblName}" ]);
		$tpl->parse("sumCell");
	} // each tblName
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
		$sql = "INSERT INTO `pwParam` ( `rtrtDate`, `pwExpires`, `rtEvent`, `rtReason`,  `annivYear`) VALUE "
			 . "( \"{$dbInfo['rtrtDate']}\", \"{$dbInfo['pwExpires']}\", \"{$dbInfo['rtEvent']}\", \"{$dbInfo['rtReason']}\", \"{$dbInfo['annivYear']}\");";
	} else {
		$tupID = $dbInfo[ 'ID' ];
		$sql = "UPDATE `pwParam` SET `pwExpires` = \"{$dbInfo[ 'pwExpires' ]}\", `rtrtDate` = \"{$dbInfo[ 'rtrtDate' ]}\", "
			 . "`rtEvent` = \"{$dbInfo[ 'rtEvent' ]}\", `rtReason` = \"{$dbInfo['rtReason']}\", `annivYear` = \"{$dbInfo['annivYear']}\" ";
		// update "lastRtrtDate" field
		if ($dbInfo[ 'rtrtDate' ] != $dbInfo[ 'lastRtrtDate' ]) {
			$sql = $sql . ", `lastRtrtDate` = \"{$dbInfo[ 'lastRtrtDate' ]}\" ";	
		}
		$sql = $sql . "WHERE `ID` = \"{$tupID}\";";
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
			$rpt[ 'rtReason' ] = "請輸入法會因緣";
			$rpt[ 'annivYear' ] = "請輸入週年年數";
			$rpt[ 'lastRtrtDate' ] = "";
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
	global $_db;
	// first check existence in the Usr table
	$_db->query("LOCK TABLES `Usr` READ;");
	$rslt = $_db->query("SELECT * FROM `Usr` WHERE `UsrName` = \"{$icoName}\";");
	$_db->query("UNLOCK TABLES;");
	if ( $rslt->num_rows > 0 ) return; // nothing to do
	// Add it into inCareOf table, if not existent
	$sql = "INSERT INTO `inCareOf` ( `UsrName` ) VALUE ( \"{$icoName}\" ) "
		 . "ON DUPLICATE KEY UPDATE `UsrName` = \"{$icoName}\";";
	$_db->query("LOCK TABLES `inCareOf` WRITE;");
	$_db->query( $sql );
	$_db->query("UNLOCK TABLES;");	
} // function setIcoName()

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
			break;
		case 'icoSelected':
			//unset( $_SESSION[ 'tblName' ] );
			$_SESSION[ 'tblName' ] = 'C001A';
			break;
		case 'icoInput':
			//unset( $_SESSION[ 'tblName' ] );
			$_SESSION[ 'tblName' ] = 'C001A';
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
