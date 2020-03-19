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
			'WebsiteHome' => array (
				SESS_LANG_CHN => "回到<br/>網站首頁",
				SESS_LANG_ENG => "Back to<br/>Homepage" ),
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
	$fontSize = ( $useChn ) ? "1.0em;" : "0.9em;";
	$sessType = $_SESSION[ 'sessType' ];
?>

<!DOCTYPE html>
<html>
<head>
<title><?php echo xLate( 'htmlTitle' ); ?></title>
<meta http-equiv="content-type" content="text/html; charset=UTF-8">
<link rel="stylesheet" type="text/css" href="../master.css">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script src="../futureAlert.js"></script>
<script src="./UsrCommon.js"></script>
<script type="text/javascript">
	$(document).ready(function() {
		$(".future").on( 'click', futureAlert );
		$("th[data-urlIdx]").on( 'click', function() {
			location.replace( _url2Go[ $(this).attr( "data-urlIdx" ) ]);
		});
	})
</script>
<style type="text/css">
/* local customization */
	table.pgMenu {
		table-layout: auto;
	}
</style>

</head>
<body>
	<?php require_once("./UsrPgHeader.php");?>
	<div class="dataArea">
		<h1 class="dataTitle centerMe" style="<?php if ( !$useChn ) echo "letter-spacing: normal;"; ?>"><?php echo xLate( 'h1Title' ); ?></h1>
	</div>
</body>
</html>