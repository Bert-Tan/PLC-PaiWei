<?php
	require_once( '../pgConstants.php' );
	require_once( 'dbSetup.php' );
	require_once( 'sunday_DBfuncs.php' );
	require_once( 'ChkTimeOut.php' );
	
	$_sessType = $_SESSION[ 'sessType' ];
	$_sessLang = $_SESSION[ 'sessLang' ];
	$_sessUsr = $_SESSION[ 'usrName' ];
	$_icoName = isset($_SESSION[ 'icoName' ]) ? $_SESSION[ 'icoName' ] : null;
	$useChn = ( $_SESSION[ 'sessLang' ] == SESS_LANG_CHN );

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
		$now = date("Y-m-d");
		$sqlUsrs = "SELECT DISTINCT `UsrName` FROM `sundayRq2Usr` INNER JOIN `sundayRq2Days` "
				 . "ON (`sundayRq2Usr`.`rqID` = `sundayRq2Days`.`rqID` AND `sundayRq2Usr`.`TblName` = `sundayRq2Days`.`TblName`) "
				 . "WHERE `UsrName` NOT IN (SELECT `UsrName` FROM `inCareOf`) "
				 . "AND `sundayRq2Days`.`rqDate` >= \"{$now}\" ORDER BY `UsrName`;";
		$sqlInCareOf = "SELECT DISTINCT `UsrName` FROM `sundayRq2Usr` INNER JOIN `sundayRq2Days` "
					 . "ON (`sundayRq2Usr`.`rqID` = `sundayRq2Days`.`rqID` AND `sundayRq2Usr`.`TblName` = `sundayRq2Days`.`TblName`) "
					 . "WHERE `UsrName` IN (SELECT `UsrName` FROM `inCareOf`) "
					 . "AND `sundayRq2Days`.`rqDate` >= \"{$now}\" ORDER BY `UsrName`;";
		$_db->query("LOCK TABLES `sundayRq2Usr` READ, `sundayRq2Days` READ, `inCareOf` READ;");
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
			$tpl->setCurrentBlock("dashboardRow");
			$tpl->setVariable("usrName", $icoName);
			$rowSum = 0;
			foreach ( $tblNames as $tblName ) {
				$tpl->setCurrentBlock("dashboardCell");
				$_db->query("LOCK TABLES `sundayRq2Usr` READ, `sundayRq2Days` READ;");							
				$sql = "SELECT DISTINCT `sundayRq2Usr`.`TblName`, `sundayRq2Usr`.`rqID` "
					 . "FROM `sundayRq2Usr` INNER JOIN `sundayRq2Days` "
					 . "ON (`sundayRq2Usr`.`rqID` = `sundayRq2Days`.`rqID` AND `sundayRq2Usr`.`TblName` = `sundayRq2Days`.`TblName`) "
					 . "WHERE `sundayRq2Usr`.`TblName` = \"{$tblName}\" AND `sundayRq2Usr`.`UsrName` = \"{$icoName}\" "
					 . "AND `sundayRq2Days`.`rqDate` >= \"{$now}\";";			
				$rslt = $_db->query($sql);			
				$_db->query("UNLOCK TABLES;");
				$tpl->setVariable("tblName", $tblName);
				$tpl->setVariable("usrTblSum", $rslt->num_rows);
				$rowSum = $rowSum + $rslt->num_rows;
				$tpl->parse("dashboardCell");
			} // loop over tables
			$tpl->setCurrentBlock("dashboardRow");
			$tpl->setVariable("rowSum", $rowSum);
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

		$now = date("Y-m-d");
		// query all user names which have VALILD Sunday requests (for future Sundays)
		$sqlUsrsRq = "SELECT DISTINCT `UsrName` FROM `sundayRq2Usr` INNER JOIN `sundayRq2Days` "
				   . "ON (`sundayRq2Usr`.`rqID` = `sundayRq2Days`.`rqID` AND `sundayRq2Usr`.`TblName` = `sundayRq2Days`.`TblName`) "
				   . "WHERE `sundayRq2Days`.`rqDate` >= \"{$now}\"";
		$sql1 = "SELECT `UsrName` FROM `inCareOf` WHERE `UsrName` NOT IN (" . $sqlUsrsRq . ");";
		$sql2 = "SELECT `UsrName` FROM `Usr` WHERE `UsrName` NOT IN (" . $sqlUsrsRq . ");";	

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
			//unset( $_SESSION[ 'tblName' ] );
			$_SESSION[ 'tblName' ] = 'sundayQifu';
			break;
		case 'icoInput':
			//unset( $_SESSION[ 'tblName' ] );
			$_SESSION[ 'tblName' ] = 'sundayQifu';
			setIcoName( $dbInfo['icoName'] );
			break;
		} // switch()
		$rpt[ 'redirect' ] = URL_ROOT . '/admin/Sunday/index.php';
		return $rpt;
	} // function dashboardRedirect()

/**********************************************************
 *					 Main Functional Code				  *
 **********************************************************/
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
$_db->close();
?>
