<?php
/**********************************************************
 *     Administrative User Management Level Assignment    *
 **********************************************************/

	require_once( '../pgConstants.php' );
	require_once( 'dbSetup.php' );
	require_once( 'ChkTimeOut.php' );

	function xLate( $what ) {
		global $sessLang;
		$htmlNames = array (
			'htmlTitle' => array (
				SESS_LANG_CHN => "淨土念佛堂管理用戶主頁",
                SESS_LANG_ENG => "Pure Land Center Admin Portal" ),
            'admUMgr' => array (
                SESS_LANG_CHN => "用戶管理",
                SESS_LANG_ENG => "User Mgmt" ),
			'pwMgr' => array (
				SESS_LANG_CHN => "為蓮友處理法會牌位",
				SESS_LANG_ENG => "Manage Name Plaques for others" ),
			'rtrtMgr' => array (
				SESS_LANG_CHN => "更新法會資料",
				SESS_LANG_ENG => "Manage Retreats" ),
			'logOut' => array (
				SESS_LANG_CHN => "用戶<br/>撤出",
				SESS_LANG_ENG => "User<br/>Logout" ),
			'h1Title' => array (
				SESS_LANG_CHN => "請選擇蓮友為他處理法會牌位",
				SESS_LANG_ENG => "Manage Name Plaques for Others" )
			);
		return $htmlNames[ $what ][ $sessLang ];
    } // function xLate();

	function readUsers2Manage() {
		global $_db;
		$sql = "SELECT `ID`, `UsrName` FROM `Usr` ORDER BY `ID`;";
		$sql2 = "SELECT `ID`, `UsrName` FROM `inCareOf` ORDER BY `ID`;";
		$_db->query("LOCK TABLES `Usr` READ, `inCareOf` READ;");
		$rslt = $_db->query( $sql );
		$rslt2 = $_db->query( $sql2 );
		$_db->query("UNLOCK TABLES;");
		$usrNames = $rslt->fetch_all(MYSQLI_ASSOC);
		$inCareOfNames = $rslt2->fetch_all(MYSQLI_ASSOC);
		$tpl = new HTML_Template_IT("./Templates");
		$tpl->loadTemplatefile("user2MngList.tpl", true, true);
		foreach ( $usrNames as $usrName ) {
			$tpl->setCurrentBlock("usrRow");
			$tpl->setVariable("tblName", "Usr");
			$tpl->setVariable("ID", $usrName['ID']);
			$tpl->setVariable("usrName", $usrName[ 'UsrName' ]);
			$tpl->parse("usrRow");
		}
		foreach ( $inCareOfNames as $usrName ) {
			$tpl->setCurrentBlock("usrRow");
			$tpl->setVariable("tblName", "inCareOf");
			$tpl->setVariable("ID", $usrName['ID']);
			$tpl->setVariable("usrName", $usrName[ 'UsrName' ]);
			$tpl->parse("usrRow");
		}		
		$tmp = preg_replace( "/(^[\r\n]*|[\r\n]+)[\s\t]*[\r\n]+/", "\n", $tpl->get() );
		return preg_replace( "/(^\t*)/", "  ", $tmp );
	} // function readUsers2Manage()

	function setUsrClass( $dbInfo ) { /* ID, UsrName, UsrClass */
		global $_db; global $_SESSION; $rpt = array();
		$uID = $dbInfo[ 'ID' ];
		$uName = $dbInfo[ 'UsrName' ];
		$uClass = $dbInfo[ 'uClass' ];
		if ( $uName == $_SESSION[ 'usrName'] ) {
			$rpt[ 'Err'] = "改變自己的用戶類別是不允許的！";
			return $rpt;
		}
		switch( $uClass ) {
		case 'SESS_TYP_WEBMASTER':
			$uSess = SESS_TYP_WEBMASTER;
			$rpt[ 'uCLass' ] = "網站管理員";
			break;
		case 'SESS_TYP_MGR':
			$uSess = SESS_TYP_MGR;
			$rpt[ 'uCLass' ] = "一般管理員";
			break;
		case 'SESS_TYP_USR':
			$uSess = SESS_TYP_USR;
			$rpt[ 'uCLass' ] = "一般用戶";
			break;
		} // End of translating literal into constants
		$sql = "INSERT INTO `admUsr` ( `ID`, `SessType` ) VALUES ( \"${uID}\", \"${uSess}\" ) "
			 . "ON DUPLICATE KEY UPDATE `SessType` = \"${uSess}\";";
		$_db->query("LOCK TABLES `admUsr` WRITE;");
		$_db->query( $sql );
		$_db->query("UNLOCK TABLES;");
		return( $rpt );
	} // function setUsrClass()

	function delUsrData( $dbInfo ) { // All data related to this user will be deleted
		global $_db; global $_SESSION; $rpt = array();
		$uID = $dbInfo[ 'ID' ];
		$uName = $dbInfo[ 'UsrName' ];
		$uTblName = $dbInfo[ 'tblName' ];
		if ( $uName == $_SESSION[ 'usrName'] ) {
			$rpt[ 'Err'] = "刪除自己的用戶資料是不允許的！";
			return $rpt;
		}
		$sql = "SELECT DISTINCT `TblName` FROM `pw2Usr` WHERE `pwUsrName` = \"${uName}\";";
		$_db->query("LOCK TABLES `pw2Usr` READ;"); 
		$rslt = $_db->query( $sql );
		$_db->query("UNLOCK TABLES;");
		$tables = $rslt->fetch_all(MYSQLI_ASSOC);
		foreach ( $tables as $table ) {
			$tblName = $table[ 'TblName' ];
			$_db->query("LOCK TABLES `pw2Usr` WRITE, `${tblName}` WRITE;");
			$sql = "DELETE FROM `${tblName}` WHERE `ID` IN "
				 . "(SELECT `pwID` FROM `pw2Usr` WHERE `TblName` = \"${tblName}\" AND `pwUsrName` = \"${uName}\");";
			$rslt = $_db->query( $sql );
			$_db->query("DELETE FROM `pw2Usr` WHERE `TblName` = \"${tblName}\" AND `pwUsrName` = \"${uName}\";");
			$_db->query("UNLOCK TABLES");
			$rpt[ "$tblName" ] = "刪除用戶 '${uName}' 在 '${tblName}' 表中的牌位資料。。。";
		} // loop through all tables this user has data
		$_db->query("LOCK TABLES `${uTblName}` WRITE;"); /* either Usr or inCareOf table */
		$rslt = $_db->query("DELETE FROM `${uTblName}` WHERE `UsrName` = \"${uName}\";");
		$_db->query("UNLOCK TABLES;");
		$rpt[ "${uTblName}" ] = "用戶: '${uName}' 已被刪除!\n";
		return $rpt;
	} // function delUsrData()

//	session_start(); // create or retrieve (already called in ChkTimeOut.php )
	$sessLang = SESS_LANG_CHN; // default
	$_SESSION[ 'sessLang' ] = $sessLang;

	$hdrLoc = "location: " . URL_ROOT . "/admin/index.php";
	$admUMgrUrl = "./AdmUMgr.php";
	$rtrtMgrUrl = "../PaiWei/rtMgr.php";	// relative;
	$pwMgrUrl = "../PaiWei/Dashboard.php";	// relative;
	$useChn = ( $sessLang == SESS_LANG_CHN );

 	if ( !isset( $_SESSION[ 'usrName' ] ) ) {
		header( $hdrLoc );
	}
	if (sizeof($_POST) > 0 ) {
		$dbInfo = json_decode( $_POST [ 'dbInfo' ], true );
		switch ( $_POST[ 'dbReq' ] ) {
		case 'dbSetUsrClass':
			echo json_encode( setUsrClass( $dbInfo ), JSON_UNESCAPED_UNICODE );
			exit;
		case 'dbDelUsr':
			echo json_encode( delUsrData( $dbInfo ), JSON_UNESCAPED_UNICODE );
			exit;
		}
	} // serving the AJAX requests
?>

<!DOCTYPE html>
<html>
<head>
<title><?php echo xLate( 'htmlTitle' ); ?></title>
<meta http-equiv="content-type" content="text/html; charset=UTF-8">
<link rel="stylesheet" type="text/css" href="../master.css">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script src="../futureAlert.js"></script>
<script src="./AdmPortal.js"></script>
<style>
/* local overrides */
div.dataBodyWrapper {
	width: 70%;
	margin: auto;
	height: 69vh;
}

table.dataHdr {
	width: 70%;
	margin: auto;
}

/* local specific */
input, select {
	font-size: 1.0em;
	background-color: aqua;
	border: 1px solid blue;
	height: 1.5em;
}

input[type=button] {
	display: inline-block;
    border-radius: 3px;
    text-align: center;   
}
</style>
</head>
<body>
	<div class="hdrRibbon">
		<img src="https://www.amitabhalibrary.org/pic/PLC_logo_TR.png" alt="" class="centerMeV">
		<div id="pgTitle" class="centerMeV">
			<span style="letter-spacing: 1px;">淨土念佛堂管理用戶主頁</span><br/>
			<span class="engClass">Pure Land Center Admin Portal</span>
		</div>
		<table class="pgMenu centerMeV">	
			<thead>
				<tr>
<?php
	if ( $_SESSION[ 'sessType' ] == SESS_TYP_WEBMASTER ) {
?>
					<th><a href="<?php echo $admUMgrUrl; ?>"><?php echo xLate( 'admUMgr' ); ?></a></th>
<?php
	}
?>                
					<th><a href="<?php echo $rtrtMgrUrl; ?>"><?php echo xLate( 'rtrtMgr' ); ?></a></th>
					<th><a href="<?php echo $pwMgrUrl; ?>"><?php echo xLate( 'pwMgr' ); ?></th>
					<th class="future">處理週日迴向申請</th>
					<th><a href="../Login/Logout.php">用戶<br/>撤出</a></th>
				</tr>
			</thead>
		</table><!-- Page Menu -->
	</div><!-- header ribbon -->
	<div class="dataArea">
		<h1 class="dataTitle">淨土念佛堂用戶管理</h1>
		<table class="dataHdr">
			<thead><tr><th>蓮友登錄識別名<br>(用戶表)&nbsp;識別名</th><th>用戶分類</th><th>管理指令</th></tr></thead>
		</table>
		<div class="dataBodyWrapper">
		<table class="dataRows">
			<tbody>
				<?php echo readUsers2Manage(); ?>
			</tbody>
		</table>
		</div>
	</div><!-- dataArea -->
</body>
</html>