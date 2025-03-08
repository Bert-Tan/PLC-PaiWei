<?php
	require_once( '../pgConstants.php' );
	require_once( 'dbSetup.php' );
	require_once( 'PaiWei_DBfuncs.php' );
	require_once( 'ChkTimeOut.php' );
	require_once( 'plcMailerSetup.php' );
	
	$_sessType = $_SESSION[ 'sessType' ];
	$_sessLang = $_SESSION[ 'sessLang' ];
	$_sessUsr = $_SESSION[ 'usrName' ];	
	$_icoName = isset($_SESSION[ 'icoName' ]) ? $_SESSION[ 'icoName' ] : null;
	$useChn = ( $_SESSION[ 'sessLang' ] == SESS_LANG_CHN );	

function _dbName_2_emailName ( $_dbName ) { // used to construct email msg (with both Chinese and English)
	$_emailNames = array (
		'C001A' => '祈福消災牌位 Well Blessing Name Plaques',
		'W001A_4' => '往生者蓮位 Deceased Name Plaques',
		'L001A' => '歷代祖先蓮位 Ancestors Name Plaques',
		'Y001A' => '累劫冤親債主蓮位 Karmic Creditors Name Plaques',
		'D001A' => '地基主蓮位 Site Guardians Name Plaques',
		'DaPaiWei' => '(一年內)往生者蓮位 Recently Deceased Name Plaques',
		'DaPaiWeiRed' => '紅色大牌位',
		'C_Name' => '佛光祝照受益者 Well-blessing Recipient\'s Name',
		'W_Title' => '往生親友稱謂 Title of the Deceased',
		'W_Name' => '往生親友姓名 Full Name of the Deceased',
		'deceasedDate' => '往生日期 Deceased Date',
		'R_Title' => '陽上啟請人稱謂 Requestor\'s Title',
		'W_Requestor' => '陽上啟請人姓名 Requestor\'s Full Name',
		'L_Name' => '祖先姓氏 Ancestor\'s Surname',
		'L_Requestor' => '後代子孫啟請人 Decendent Requestor\'s Full Name',
		'Y_Requestor' => '陽上有緣啟請人 Requestor\'s Full Name',
		'D_Name' => '地基主神靈所在地 Address of the Site Being Blessed by the Site Guardians',
		'D_Requestor' => '陽上啟請人 Requestor\'s Full Name'
	);
	return ( $_emailNames[ $_dbName ] );
} // _dbName_2_emailName()


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
	$lastRtrtDate = $_SESSION[ 'lastRtrtDate' ];

	$tblNames = array(	'C001A', 'W001A_4', 'DaPaiWei', 'L001A', 'Y001A', 'D001A', 'DaPaiWeiRed' );
	$pwTotal = array(	'C001A' => 0, 'W001A_4' => 0, 'DaPaiWei' => 0, 'L001A' => 0, 'Y001A' => 0,
						'D001A' => 0, 'DaPaiWeiRed' => 0, 'grandTotal' => 0 );
	$pwValidTotal = array(	'C001A' => 0, 'W001A_4' => 0, 'DaPaiWei' => 0, 'L001A' => 0, 'Y001A' => 0,
							'D001A' => 0, 'DaPaiWeiRed' => 0, 'grandTotal' => 0 );
	$pwInvalidTotal = array(	'C001A' => 0, 'W001A_4' => 0, 'DaPaiWei' => 0, 'L001A' => 0, 'Y001A' => 0,
								'D001A' => 0, 'DaPaiWeiRed' => 0, 'grandTotal' => 0 );
	$pwCtPerSheet = array(	'C001A' => 5, 'W001A_4' => 6, 'DaPaiWei' => 1, 'L001A' => 6, 'Y001A' => 6,
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
				 . "AND `${tblName}`.`timestamp` > \"${lastRtrtDate}\";";
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
	$adminUsrEmail = $_SESSION[ 'usrEmail' ];

	// first check existence in the Usr table
	$_db->query("LOCK TABLES `Usr` READ;");
	$rslt = $_db->query("SELECT * FROM `Usr` WHERE `UsrName` = \"{$icoName}\";");
	$_db->query("UNLOCK TABLES;");

	if ( $rslt->num_rows > 0 ) return; // nothing to do

	// Add it into inCareOf table, if not exist; update email if exist
	$sql = "INSERT INTO `inCareOf` ( `UsrName`, `UsrEmail` ) VALUES ( \"{$icoName}\", \"{$adminUsrEmail}\" ) "
		 . "ON DUPLICATE KEY UPDATE `UsrEmail` = \"{$adminUsrEmail}\";";
	$_db->query("LOCK TABLES `inCareOf` WRITE;");
	$_db->query( $sql );
	$_db->query("UNLOCK TABLES;");
} // function setIcoName()

function updateIcoEmail( $icoName ) {
	global $_db, $_SESSION;
	$adminUsrEmail = $_SESSION[ 'usrEmail' ];

	// first check existence in the Usr table
	$_db->query("LOCK TABLES `Usr` READ;");
	$rslt = $_db->query("SELECT * FROM `Usr` WHERE `UsrName` = \"{$icoName}\";");
	$_db->query("UNLOCK TABLES;");

	if ( $rslt->num_rows > 0 ) return; // nothing to do

	// update ico email
	$sql = "UPDATE `inCareOf` SET `UsrEmail` = \"{$adminUsrEmail}\" WHERE `UsrName` = \"{$icoName}\";";
	$_db->query("LOCK TABLES `inCareOf` WRITE;");
	$_db->query( $sql );
	$_db->query("UNLOCK TABLES;");
} // function updateIcoEmail()

/**********************************************************
 *				For dashboardRedirect			   	  	  *
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


function emailInvalidPw( $icoName, $tblNames, $pwExpires, $rtReason, $rtEvent, &$emailContent, &$responseMsg ) {
	global $_db, $_SESSION;
	$adminUsrEmail = $_SESSION[ 'usrEmail' ];
	$adminUsrName = $_SESSION[ 'usrName' ];
	$lastRtrtDate = $_SESSION[ 'lastRtrtDate' ];

	$msg = new HTML_Template_IT("./Templates");
	$msg->loadTemplatefile("invalidPwMSG.tpl", true, true);
	$msg->setCurrentBlock("msgBlock");
	$msg->setCurrentBlock("Txt");
	$msg->setVariable("usrName", $icoName );
	
	$rtNameChn = ''; $rtNameEng = ''; $year = date('Y');
	if ( $rtEvent == 'RespectAncestors' ) { // 祭祖法會
		if ( strpos($rtReason, '清明') !== false ) {
			$rtNameChn = $year . ' 清明'. '祭祖法會';
			$rtNameEng = $year . ' QingMing Festival'. ' Retreat';
		}
		elseif ( strpos($rtReason, '中元') !== false ) {
			$rtNameChn = $year . ' 中元'. '祭祖法會';
			$rtNameEng = $year . ' ZhongYuan Festival'. ' Retreat';
		}
		else {
			//$rtNameChn = $year . ' sdsd'. '祭祖法會';
			$rtNameChn = $year . ' '. str_replace('祭祖', '', $rtReason). '祭祖法會';
			$rtNameEng = $year . ' Paying Respect to Ancestors'. ' Retreat';
		}			
	}
	elseif ( $rtEvent == 'ThriceYearning' ) {  // 三時繫念法會
		if ( strpos($rtReason, '週年') !== false ) {
			$rtNameChn = $year . ' '. str_replace('淨土念佛堂及圖書館', '', $rtReason). '三時繫念法會';
			$rtNameEng = $year . ' Anniversary Celebration'. ' Thrice Yearning Retreat';
		}
		else {
			$rtNameChn = $year . ' '. str_replace('淨土念佛堂及圖書館', '', $rtReason). '三時繫念法會';
			$rtNameEng = $year . ' Thrice Yearning Retreat';
		}
	}
	$msg->setVariable("rtNameChn", $rtNameChn );

	$msg->setVariable("rtNameEng", $rtNameEng );
	$msg->setVariable("pwExpireDate", $pwExpires );
	$msg->setVariable("adminUsrEmail", $adminUsrEmail );
	$msg->parse("Txt");

	$totalInvalidPwCt = 0;
	foreach ( $tblNames as $tblName ) {
		$fldN = getPaiWeiTblFlds( $tblName );

		// query INVALID paiwei
		$_db->query("LOCK TABLES `pw2Usr` READ, `${tblName}` READ;");
		$sql = "SELECT `${tblName}`.* FROM `pw2Usr` INNER JOIN `${tblName}` ON `pw2Usr`.`pwID` = `${tblName}`.`ID` "
			 . "WHERE `pw2Usr`.`pwUsrName` = \"${icoName}\" AND `pw2Usr`.`TblName` = \"${tblName}\" "
			 . "AND `${tblName}`.`timestamp` <= \"${lastRtrtDate}\";";
		$rslt = $_db->query($sql);		
		$rows = $rslt->fetch_all( MYSQLI_ASSOC );
		$invalidPwCt = $rslt->num_rows;
		$rslt->free();
		$_db->query("UNLOCK TABLES;");

		$totalInvalidPwCt += $invalidPwCt;
		if ( $invalidPwCt == 0 ) continue;

		$msg->setCurrentBlock("PaiWei");
		$msg->setVariable("tblName", _dbName_2_emailName( $tblName ) );

		$msg->setCurrentBlock("hdr_row");
		//$fldN = getPaiWeiTblFlds( $tblName );
		foreach ( $fldN as $key ) {
			// 'ID' and 'timestamp' fields are not visible to the users
			if ( $key == 'ID' || $key == 'timestamp' ) continue;
			
			$msg->setCurrentBlock("hdr_cell");
			$msg->setVariable("fldName", _dbName_2_emailName( $key ) );
    		$msg->parse("hdr_cell");	
		}
		$msg->parse("hdr_row");

		foreach ( $rows as $row ) {
			$msg->setCurrentBlock("data_row");

			foreach ( $row as $key => $val ) {
				// 'ID' and 'timestamp' fields are not visible to the users
				if ( $key == 'ID' || $key == 'timestamp' ) continue;				

				$msg->setCurrentBlock("data_cell");
				$msg->setVariable("fldValue", $val);
				$msg->parse("data_cell");				
			} // row
			$msg->parse("data_row");		  
		} // $rows

		$msg->parse("PaiWei");
	} // tblNames
	$msg->parse("msgBlock");
	
	/*
	// print email for test only
	$emailContent = $msg->get();
	return false;
	*/

	if ( $totalInvalidPwCt == 0 ) {
		$responseMsg = "${icoName} 已驗證 所有 牌位！\n\n請刷新網頁獲取最新的牌位匯總信息。";
		return false;
	}

	/* send email */
	// query icoEmail
	$icoEmail = '';
	$_db->query("LOCK TABLES `Usr` READ;");
	$sql = "SELECT `UsrEmail` FROM `Usr` WHERE `UsrName` = \"${icoName}\";";
	$rslt = $_db->query($sql);
	$_db->query("UNLOCK TABLES;");
	if ( $rslt->num_rows > 0 ) {
		$icoEmail = $rslt->fetch_all( MYSQLI_NUM )[0][0];
	}
	else {
		$_db->query("LOCK TABLES `inCareOf` READ;");
		$sql = "SELECT `UsrEmail` FROM `inCareOf` WHERE `UsrName` = \"${icoName}\";";
		$rslt = $_db->query($sql);
		$_db->query("UNLOCK TABLES;");
		if ( $rslt->num_rows > 0 ) {
			$icoEmail = $rslt->fetch_all( MYSQLI_NUM )[0][0];
		}
	}

	$to = array(
		array (
			'email' => $icoEmail,
			'name'	=> $icoName
		)
	);
	$cc = array(
		array (
			'email' => $adminUsrEmail,
			'name'	=> $adminUsrName
		)
	);
	$replyTo = array(
		array (
			'email' => $adminUsrEmail,
			'name'	=> $adminUsrName
		)
	);
	$subject = '驗證牌位 VALIDATE Name Plaques -- '. $icoName;
	$html_msg = $msg->get();
	
	if ( plcSendEmailAttachment( $to, $cc, null, $replyTo, $subject, $html_msg, null, null, true ) ) {
		return true;
	}
	return false;
} // function emailInvalidPw

/**********************************************************
 *				For notifyInvalidPw			   		   	  *
 **********************************************************/
function notifyInvalidPw( $dbInfo ) {
	$rpt = array (); $emailContent = null; $responseMsg = null;

	if ( emailInvalidPw( $dbInfo[ 'icoName' ], $dbInfo[ 'tblNames' ], $dbInfo[ 'pwExpires' ], $dbInfo[ 'rtReason' ], $dbInfo[ 'rtEvent' ], $emailContent, $responseMsg ) )
		$responseMsg = "告知 " . $dbInfo[ 'icoName' ] . " 郵件發送完畢！";
	else {		
		if ( $responseMsg == null ) {
		/* $responseMsg == null: email sent failed
		 * otherwise: dashboard invalidPWCt out-of-date, NOT "email sent failed"
		*/
			$responseMsg = "告知 " . $dbInfo[ 'icoName' ] . " 失敗！\n\n請重試或通過其它方式告知。";
		}
	}
	
	if ( $emailContent != null ) $rpt[ 'printEmailContent' ] = $emailContent;
	if ( $responseMsg != null ) $rpt[ 'responseMsg' ] = $responseMsg;
	return $rpt;
} // function notifyInvalidPw()

/**********************************************************
 *				For notifyAllInvalidPw			   		  *
 **********************************************************/
function notifyAllInvalidPw( $dbInfo ) {
	$rpt = array (); $emailContent = null; $responseMsg = null;
	$successCount = 0; $outofdateCount = 0; $failedIcoNames = array ();

	// usrNames who have INVALID paiwei
	$icoNames = $dbInfo[ 'icoNames' ];
	// each element is an array of pwTblNames (with INVALID paiwei) for the coresponding usrName (i.e. element key)
	$invalidPwTblNames = $dbInfo[ 'tblNames' ];
	
	foreach ( $icoNames as $icoName ) {
		$emailContent = null; $responseMsg = null;		

		if ( emailInvalidPw( $icoName, $invalidPwTblNames[ $icoName ], $dbInfo[ 'pwExpires' ], $dbInfo[ 'rtReason' ], $dbInfo[ 'rtEvent' ], $emailContent, $responseMsg ) )
			$successCount ++;
		else {
			if ( $responseMsg == null ) {
				/* $responseMsg == null: email sent failed
		 		 * otherwise: dashboard invalidPWCt out-of-date, NOT "email sent failed"
				*/								
				array_push( $failedIcoNames, $icoName);
			}
			else $outofdateCount ++;
		}			
	}

	$emailContent = null; $responseMsg = null;
	if ( $successCount > 0 ) { $responseMsg = "告知 $successCount 位同修郵件發送完畢！"; }
	if ( $outofdateCount > 0 ) {
		$txt = "$outofdateCount 位同修已驗證 所有 牌位！\n請刷新網頁獲取最新的牌位匯總信息。";
		if ( $responseMsg == null ) $responseMsg = $txt;
		else $responseMsg .= "\n\n". $txt;
		
	}
	if ( ! empty ( $failedIcoNames ) ) {
		$txt = "告知以下 ". count( $failedIcoNames ) ." 位同修失敗！請重試或通過其它方式告知。";
		if ( $responseMsg == null ) $responseMsg = $txt;
		else $responseMsg .= "\n\n". $txt;
		foreach ( $failedIcoNames as $failedIcoName ) { $responseMsg .= "\n" . $failedIcoName; }
	}

	if ( $responseMsg == null ) $responseMsg = "請刷新網頁獲取最新的牌位匯總信息！";
	$rpt[ 'responseMsg' ] = $responseMsg;
	return $rpt;
} // function notifyAllInvalidPw()


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
	case 'notifyInvalidPw':
		echo json_encode( notifyInvalidPw( $_dbInfo ), JSON_UNESCAPED_UNICODE );
		break;
	case 'notifyAllInvalidPw':
		echo json_encode( notifyAllInvalidPw( $_dbInfo ), JSON_UNESCAPED_UNICODE );
		break;
} // switch()

$_db->close();
?>
