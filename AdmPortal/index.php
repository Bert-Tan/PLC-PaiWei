<?php
/**********************************************************
 *                 Admin Portal Main Page                 *
 **********************************************************/
 
	require_once( '../pgConstants.php' );
	require_once( 'dbSetup.php' );
	require_once( 'ChkTimeOut.php' );

	function xLate( $what ) {
		global $sessLang;
		$htmlNames = array (
			'htmlTitle' => array (
				SESS_LANG_CHN => "淨土念佛堂管理用戶主頁",
				SESS_LANG_ENG => "Pure Land Center Admin User Main Page" ),
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
				SESS_LANG_CHN => "請點選管理功能",
				SESS_LANG_ENG => "Please Select an Administrative Function" )
			);
		return $htmlNames[ $what ][ $sessLang ];
	} // function xLate();

//	session_start(); // create or retrieve
	$sessLang = SESS_LANG_CHN;
	if ( isset( $_SESSION[ 'sessLang' ] ) ) {
		$sessLang = $_SESSION[ 'sessLang' ];
	} else {
		$_SESSION[ 'sessLang' ] = $sessLang;
	}

	$hdrLoc = "location: " . URL_ROOT . "/admin/index.php";
	$rtrtMgrUrl = "../PaiWei/rtMgr.php";	// relative;
	$pwMgrUrl = "../PaiWei/inCareOfMgr.php";	// relative;
	$useChn = ( $sessLang == SESS_LANG_CHN );

 	if ( !isset( $_SESSION[ 'usrName' ] ) ) {
		header( $hdrLoc );
	}

?>

<!DOCTYPE html>
<html>
<head>
<title><?php echo xLate( 'htmlTitle' ); ?></title>
<meta http-equiv="content-type" content="text/html; charset=UTF-8">
<link rel="stylesheet" type="text/css" href="https://www.amitabhalibrary.org/css/base.css">
<link rel="stylesheet" type="text/css" href="../css/admin.css">
<link rel="stylesheet" type="text/css" href="../css/menu.css">
<link rel="stylesheet" type="text/css" href="../UsrPortal/UsrPortal.css">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script src="../futureAlert.js"></script>
<script type="text/javascript">
	$(document).ready(function() {
		$(".future").on( 'click', futureAlert );
		$(".soon").on( 'click', soonAlert );
	})
</script>
<style>
#myMenuTbl {
	table-layout: fixed;
}
#myMenuTbl th {
	line-height: 2.9em;
}
</style>
</head>
<body>
	<div class="hdrRibbon">
		<img src="https://www.amitabhalibrary.org/pic/PLC_logo_TR.png" alt="">
		<div id="pgTitle" class="centerMeV">
			<span style="letter-spacing: 1px;">淨土念佛堂管理用戶主頁</span><br/>
			<span class="engClass">Pure Land Center Admin Portal</span>
		</div>
		<table id="myMenuTbl" class="centerMeV">	
			<thead>
				<tr>
					<th><a href="<?php echo $rtrtMgrUrl; ?>" class="myLinkButton"><?php echo xLate( 'rtrtMgr' ); ?></a></th>
					<th><a href="<?php echo $pwMgrUrl; ?>" class="myLinkButton"><?php echo xLate( 'pwMgr' ); ?></th>
					<th class="future">處理週日迴向申請</th>
				</tr>
			</thead>
		</table>
		<div id="pgLogOut" class="centerMeV"><a href="../Login/Logout.php"><?php echo xLate( 'logOut' ); ?></a></div>
	</div>
	<div class="dataArea">
		<h1 class="centerMe" id="myDataTitle" style="<?php if ( !$useChn ) echo "letter-spacing: normal;"; ?>"><?php echo xLate( 'h1Title' ); ?></h1>
	</div>
</body>
</html>