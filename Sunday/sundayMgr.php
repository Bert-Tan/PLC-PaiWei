<?php
/**********************************************************
 *          Sunday QiFu / HuiXiang Management Page        *
 *                admin/Sunday/sundayMgr.php              *
 **********************************************************/
 
	require_once( '../pgConstants.php' );
	require_once( 'dbSetup.php' );
	require_once( 'ChkTimeOut.php' );

	function xLate( $what ) {
		global $sessLang;
		$htmlNames = array (
			'htmlTitle' => array (
				SESS_LANG_CHN => "淨土念佛堂一般用戶主頁",
				SESS_LANG_ENG => "Pure Land Center User Portal" ),
			'UsrHome' => array (
				SESS_LANG_CHN => "回到<br/>用戶主頁",
				SESS_LANG_ENG => "Back to<br/>UsrPortal" ),
			'featPW' => array (
				SESS_LANG_CHN => "申請<br/>法會牌位",
				SESS_LANG_ENG => "Name Plaque for<br/>Retreat Merit Dedication" ),
			'featSun' => array (
				SESS_LANG_CHN => "早課<br/>祈福迴向",
				SESS_LANG_ENG => "Sunday Chanting<br/>Merit Dedication" ),
			'featFuture' => array (
				SESS_LANG_CHN => "其他未來會提供的功能<br/>(結緣法寶申請，等等。)",
				SESS_LANG_ENG => "Future:<br/>(Dharma Items Request; etc.)" ),
			'logOut' => array (
				SESS_LANG_CHN => "用戶<br/>撤出",
				SESS_LANG_ENG => "User<br/>Logout" ),
			'qifuTitle' => array (
				SESS_LANG_CHN => "週日早課申請<br/>祈福與功德迴向",
				SESS_LANG_ENG => "Sunday Chanting<br/>Well-wishing&nbsp;&amp;&nbsp;Merit Dedication" ),
			'ruleTab' => array (
				SESS_LANG_CHN => "申請要求與辦法",
				SESS_LANG_ENG => "Application Requirements &amp; Procedure" ),
			'qifuTab' => array (
				SESS_LANG_CHN => "祈福申請表",
				SESS_LANG_ENG => "Well-wishing Request Form" ),
			'meritTab' => array (
				SESS_LANG_CHN => "功德迴向申請表",
				SESS_LANG_ENG => "Merit Dedication Request Form" ),
			'gongDeZhuTab' => array (
				SESS_LANG_CHN => "申請做功德主",
                SESS_LANG_ENG => "Request to Serve as<br/>A Ceremony Sponsor" ),
            'setDueTime' => array (
                SESS_LANG_CHN => "設定祈福迴向申請截止時間",
                SESS_LANG_ENG => "" ),  // management function - Chinese only
            'dnldPrint' => array (
                SESS_LANG_CHN => "下載列印祈福迴向啟請資料",
                SESS_LANG_ENG => "" ),  // management function - Chinese only
            'forOthers' => array (
                SESS_LANG_CHN => "處理蓮友祈福迴向申請",
                SESS_LANG_ENG => "" ),  // management function - Chinese only
		);
		return $htmlNames[ $what ][ $sessLang ];
	} // function xLate();

    function setSundayDue( $dbInfo ) { // used by the Sunday Admin capabilities
        global $_db;
		$rpt = array();
        if ( strlen( $dbInfo['ID'] ) == 0 ) {
			$tupID = null;
			$sql = "INSERT INTO `sundayParam` ( `expHH`, `expMM` ) VALUE "
				 . "( \"{$dbInfo['expHH']}\", \"{$dbInfo['expMM']}\" );";
		} else {
			$tupID = $dbInfo[ 'ID' ];
			$sql = "UPDATE `sundayParam` SET `expHH` = \"{$dbInfo[ 'expHH' ]}\", `expMM` = \"{$dbInfo[ 'expMM' ]}\" "
				 . "WHERE `ID` = \"{$tupID}\";";
        }
        $_db->query("LOCK TABLES `sundayParam`;");
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
    } // function setSundayDue()
    
    function readSundayDue() {
		global $_db;
		$rpt = array ();
		$sql = "SELECT * FROM `sundayParam` WHERE true;";
		$_db->query("LOCK TABLES `sundayParam`; ");
		$rslt = $_db->query( $sql );
		$_db->query("UNLOCK TABLES;");
		switch( $rslt->num_rows ) {
			case 0: // no record found
				$rpt[ 'expHH' ] = "請輸入申請截止鐘點";
				$rpt[ 'expMM' ] = "請輸入申請截止分點";
				return $rpt;
			case 1:
				return( $rslt->fetch_all(MYSQLI_ASSOC)[0] );
			default:
				$rpt[ 'err' ] = "資料庫發生錯誤；無法讀取申請截止資料！最後所執行的資料庫指令為：\n {$sql}";
				return $rpt;
		}
	} // function readSundayDue()
	
	function loadSundayDashboard() {
		global $_db;
		$rpt = array();
		$tblNames = array( 'sundayQifu', 'sundayMerit' );
		$sqlUsrs = "SELECT DISTINCT `UsrName` FROM `sundayRq2Usr` WHERE `UsrName` NOT IN "
				 . "(SELECT `UsrName` FROM `inCareOf`) ORDER BY `UsrName`;";
		$sqlInCareOf = "SELECT DISTINCT `UsrName` FROM `sundayRq2Usr` WHERE `UsrName` IN "
					 . "(SELECT `UsrName` FROM `inCareOf`) ORDER BY `UsrName`;";
		$_db->query("LOCK TABLES `sundayRq2Usr` READ, `inCareOf` READ;");
		$rslt = $_db->query( $sqlUsrs );
		$usrNames = $rslt->fetch_all(MYSQLI_ASSOC);
		$rslt = $_db->query( $sqlInCareOf );
		$inCareOfNames = $rslt->fetch_all(MYSQLI_ASSOC);
		$_db->query("UNLOCK TABLES;");			 
		$allNames = array_merge( $inCareOfNames, $usrNames );
		$tpl = new HTML_Template_IT("./Templates");
		$tpl->loadTemplatefile("sundayDashboardRows.tpl", true, true);
		$tpl->setCurrentBlock("dashboardBody");
		foreach ( $allNames as $Name ) {
			$icoName = $Name[ 'UsrName' ];
			$_db->query("LOCK TABLES `sundayRq2Usr` READ;");
			$rslt = $_db->query("SELECT `TblName` FROM `sundayRq2Usr` WHERE `UsrName` = \"{$icoName}\";");
			$_db->query("UNLOCK TABLES;");
			$tpl->setCurrentBlock("dashboardRow");			
			$tpl->setVariable("usrName", $icoName);
			$tpl->setVariable("rowSum", $rslt->num_rows);
			foreach ( $tblNames as $tblName ) {
				$tpl->setCurrentBlock("dashboardCell");
				$_db->query("LOCK TABLES `sundayRq2Usr` READ, `sundayRq2Days` READ;");				
				$now = date("Y-m-d");				
				$sql = "SELECT DISTINCT `sundayRq2Usr`.`TblName`, `sundayRq2Usr`.`rqID` "
					 . "FROM `sundayRq2Usr` INNER JOIN `sundayRq2Days` "
					 . "ON (`sundayRq2Usr`.`rqID` = `sundayRq2Days`.`rqID` AND `sundayRq2Usr`.`TblName` = `sundayRq2Days`.`TblName`) "
					 . "WHERE `sundayRq2Usr`.`TblName` = \"{$tblName}\" AND `sundayRq2Usr`.`UsrName` = \"{$icoName}\" "
					 . "AND `sundayRq2Days`.`rqDate` >= \"{$now}\";";			
				$rslt = $_db->query($sql);				
				$_db->query("UNLOCK TABLES;");
				$tpl->setVariable("tblName", $tblName);
				$tpl->setVariable("usrTblSum", $rslt->num_rows);
				$tpl->parse("dashboardCell");
			} // loop over tables
			$tpl->parse("dashboardRow");
		} // loop over all Names to construct row data
		$tpl->parse("dashboardBody");
		$tmp = preg_replace( "/(^[\r\n]*|[\r\n]+)[\s\t]*[\r\n]+/", "\n", $tpl->get() );
		$rpt[ 'dashboardBody' ] = preg_replace( "/(^\t*)/", "  ", $tmp );
		$rpt[ 'inCareOfOptions' ] = readInCareOf();
		return $rpt;
	} // function loadSundayDashboard()

	function readInCareOf() { // returns a string reflecting a <select> html element
		global $_db;
		$inCareOfNames = array();
		$usrNames = array();

		$sql1 = "SELECT `UsrName` FROM `inCareOf` WHERE `UsrName` NOT IN "
			  . "(SELECT DISTINCT `UsrName` FROM `sundayRq2Usr`);";
		$sql2 = "SELECT `UsrName` FROM `Usr` WHERE `UsrName` NOT IN "
			  . "(SELECT DISTINCT `UsrName` FROM `sundayRq2Usr`);";
		$_db->query( "LOCK TABLES `inCareOf` READ，`sundayRq2Usr` READ, `Usr` READ;" );
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
		$tpl->loadTemplatefile("inCareOfOptions.tpl", true, true);
		$tpl->setCurrentBlock("InCareOf");
		foreach ( $inCareOfNames as $inCareOfName ) {
			$tpl->setCurrentBlock("Option");
			foreach ($inCareOfName as $key => $val ) {
				$tpl->setVariable("optV", $val );	
			}
			$tpl->parse("Option");
		} // $inCareOfNames
		foreach ( $usrNames as $usrName ) {
			$tpl->setCurrentBlock("Option");
			foreach ($usrName as $key => $val ) {
				$tpl->setVariable("optV", $val );	
			}
			$tpl->parse("Option");
		} // $inCareOfNames
		$tpl->parse("InCareOf");
		$tmp = preg_replace( "/(^[\r\n]*|[\r\n]+)[\s\t]*[\r\n]+/", "\n", $tpl->get() );
		return preg_replace( "/(^\t*)/", "  ", $tmp );
	} // function readInCareOf()

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

	function dashboardRedirect( $dbInfo ) {
		global $_SESSION;
		$_SESSION['icoName'] = $dbInfo[ 'icoName' ];
		switch( $dbInfo[ 'icoNameType' ] ) {
		case 'icoDerived':
			$_SESSION[ 'tblName' ] = $dbInfo[ 'tblName' ];
			break;
		case 'icoSelected':
			unset( $_SESSION[ 'tblName' ] );
			break;
		case 'icoInput':
			unset( $_SESSION[ 'tblName' ] );
			setIcoName( $dbInfo['icoName'] );
			break;
		} // switch()
		$rpt[ 'redirect' ] = URL_ROOT . '/admin/Sunday/index.php';
		return $rpt;
	} // function dashboardRedirect()

	$hdrLoc = "location: " . URL_ROOT . "/admin/index.php";
//	session_start(); // create or retrieve
	if ( !isset( $_SESSION[ 'usrName' ] ) ) {
		header( $hdrLoc );
	}
	$sessLang = $_SESSION[ 'sessLang' ];
	$useChn = ( $sessLang == SESS_LANG_CHN );
	$fontSize = ( $useChn ) ? "1.0em;" : "0.9em;";
	$ltrSpacing = ( $useChn ) ? "20px" : "normal";

	if ( ! isset( $_POST[ 'dbReq' ]) ) goto END;
/*
 * Below are the server functions
 */
	$_dbReq = $_POST[ 'dbReq' ];
	$_dbInfo = json_decode( $_POST [ 'dbInfo' ], true );
	switch( $_dbReq ) {
	case 'dbReadSundayDue':
		echo json_encode( readSundayDue( ), JSON_UNESCAPED_UNICODE );
		exit;
	case 'dbSetSundayDue':
		echo json_encode( setSundayDue( $_dbInfo ), JSON_UNESCAPED_UNICODE );
		exit;
	case 'dbLoadSundayDashboard':
		echo json_encode( loadSundayDashboard( ), JSON_UNESCAPED_UNICODE );
		exit;
	case 'dashboardRedirect':
		echo json_encode( dashboardRedirect( $_dbInfo ), JSON_UNESCAPED_UNICODE );
		exit;
	} // switch()
/*
 * Server functions ends here
 */

	END:
?>

<!DOCTYPE html>
<html>
<head>
<title><?php echo xLate( 'htmlTitle' ); ?></title>
<meta http-equiv="content-type" content="text/html; charset=UTF-8">
<link rel="stylesheet" type="text/css" href="../master.css">
<link rel="stylesheet" type="text/css" href="../tabmenu-h.css">
<link rel="stylesheet" type="text/css" href="./sundayRules.css">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script src="../futureAlert.js"></script>
<script src="../AdmPortal/AdmCommon.js"></script>
<!-- script src="./Sunday.js"></script -->
<script src="./sundayMgr.js"></script>
<style type="text/css">
/* local customization */
	table.pgMenu {
		table-layout: auto;
	}
	div.dataArea {
		height: 84vh;
		margin-top: 0px;
		border: 2px solid green; /* same as the active tab color */
    	box-sizing: border-box;
    	-moz-box-sizing: border-box;
    	-webkit-box-sizing: border-box;
	}
	table.dataHdr, table.dataRows {
		table-layout: auto;
	}
/*
	table.dataHdr th:last-child, table.dataRows td:last-child {
		padding-left: 1px;
		padding-right: 1px;
	}
 */
	table.dataHdr th, table.dataRows td {
		height: 22px;
		line-height: 1.2em;
	}	

	table.dataRows tr td:not(:last) {
		text-align: left;
	}
/* local only */
	div#tabDataFrame { /* For loading tab data */
		width: 98%;
		height: 70vh;
		margin: auto;
		margin-top: 0px;
		margin-bottom: 0px;
		overflow-y: auto;
	}

	input {
		font-size: 1.0em;
	}
	input[type=button] {
		font-size: 0.8em;
		background-color: aqua;
		text-align: center;
		display: inline-block;
		border: 1px solid blue;
		border-radius: 4px;
	}
	input[type=text] {
		width: 100%;
		box-sizing: border-box;
    	-moz-box-sizing: border-box;
    	-webkit-box-sizing: border-box;
	}
/* for Admin Dialog box */
	table.dialog {
		width: 46%;
		margin: auto;
	}

	input, select {
		font-size: 1.1em;
	}

	input[type=text] {
		width: 80%;
	}

	select {
		width: 90%;
	}
	input[type=submit] {
		background-color: aqua;
		text-align: center;
		display: inline-block;
		height: 1.5em;
		border: 1px solid blue;
		border-radius: 3px;
	}

/* for dashboard */
	table.dataRows td[data-tblN]:hover {
	/*	color: no change; */
		background-color: #ffff80;
		cursor: pointer;
	}

	table.dataHdr th input {
		font-size: 1.0em;
		background-color: aqua;
		border: 1px solid blue;
	}

	table.dataHdr th input[type=button] {
		margin-top: 3px;
		display: inline-block;
		float: right;
		border: 1px solid blue;
		border-radius: 6px;
	}

	table.dataHdr th select {
		width: 70%;
		background-color: aqua;
		border: 1px solid blue;
	}
</style>

</head>
<body>
	<?php require_once("../AdmPortal/AdmPgHeader.htm");?>
	<table class="tabMenu">
		<thead>
			<tr>
				<th data-table="sundayParam"><?php echo xLate( 'setDueTime' ); ?></th>
				<th data-table="dnldPrint"><?php echo xLate( 'dnldPrint' ); ?></th>
				<th data-table="sundayDash"><?php echo xLate( 'forOthers' ); ?></th>
			</tr>
		</thead>
	</table>
	<div class="dataArea">
		<h2 class="dataTitle" style="letter-spacing: <?php echo $ltrSpacing; ?>;"><?php echo xLate( 'qifuTitle' ); ?></h2>
		<div id="tabDataFrame">
			<!-- Frame to load Tab Data -->
		</div><!-- tabDataFrame -->
	</div><!-- dataArea -->
</body>
</html>