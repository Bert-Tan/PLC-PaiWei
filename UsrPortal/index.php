<?php
/**********************************************************
 *                  User Portal Main Page                 *
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
			'featPW' => array (
				SESS_LANG_CHN => "法會牌位申請",
				SESS_LANG_ENG => "Name Plaque Application for<br/>Merit Dedication in Retreats" ),
			'featFuture' => array (
				SESS_LANG_CHN => "其他未來會提供的功能<br/>( 週日早課祈福及迴向申請，結緣法寶申請，等等。)",
				SESS_LANG_ENG => "Future Capabilities<br/>(e.g., Req. for Dharma Items; etc.)" ),
			'logOut' => array (
				SESS_LANG_CHN => "用戶<br/>撤出",
				SESS_LANG_ENG => "User<br/>Logout" ),
			'h1Title' => array (
				SESS_LANG_CHN => "請點選功能",
				SESS_LANG_ENG => "Please Select a Function" )
			);
		return $htmlNames[ $what ][ $sessLang ];
	} // function xLate();

	$hdrLoc = "location: " . URL_ROOT . "/admin/index.php";
//	session_start(); // create or retrieve
	if ( !isset( $_SESSION[ 'usrName' ] ) ) {
		header( $hdrLoc );
	}
	$sessLang = $_SESSION[ 'sessLang' ];
	$useChn = ( $sessLang == SESS_LANG_CHN );
	$featPWurl = "../PaiWei/index.php";	// relative path;
?>

<!DOCTYPE html>
<html>
<head>
<title><?php echo xLate( 'htmlTitle' ); ?></title>
<meta http-equiv="content-type" content="text/html; charset=UTF-8">
<link rel="stylesheet" type="text/css" href="https://www.amitabhalibrary.org/css/base.css">
<link rel="stylesheet" type="text/css" href="../css/admin.css">
<link rel="stylesheet" type="text/css" href="../css/menu.css">
<link rel="stylesheet" type="text/css" href="./UsrPortal.css">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script src="../futureAlert.js"></script>
<script type="text/javascript">
	$(document).ready(function() {
		$(".future").on( 'click', futureAlert );
	/*	$(".soon").on( 'click', soonAlert ); */
	})
</script>

</head>
<body>
	<div class="hdrRibbon">
		<img src="https://www.amitabhalibrary.org/pic/PLC_logo_TR.png" alt="">
		<div id="pgTitle" class="centerMeV">
			<span style="letter-spacing: 1px;">淨土念佛堂一般用戶主頁</span><br/>
			<span class="engClass">Pure Land Center User Portal</span>
		</div>
		<table id="myMenuTbl" class="centerMeV">	
			<thead>
				<tr>
					<th><a href="<?php echo $featPWurl; ?>" class="myLinkButton"><?php echo xLate( 'featPW' ); ?></a></th>
					<th class="future"><?php echo xLate( 'featFuture' ); ?></th>
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