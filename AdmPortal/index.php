<?php
/**********************************************************
 *                 Admin Portal Main Page                 *
 **********************************************************/
 
	require_once( '../pgConstants.php' );
	require_once( 'dbSetup.php' );
	require_once( 'ChkTimeOut.php' );

//	session_start(); // create or retrieve
	$hdrLoc = "location: " . URL_ROOT . "/admin/index.php";
 	if ( !isset( $_SESSION[ 'usrName' ] ) ) {
		header( $hdrLoc );
	}

?>

<!DOCTYPE html>
<html>
<head>
<title>淨土念佛堂管理用戶主頁</title>
<meta http-equiv="content-type" content="text/html; charset=UTF-8">
<link rel="stylesheet" type="text/css" href="../master.css"><!-- test only; real path: ../master.css -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script type="text/javascript" src="../futureAlert.js"></script>
<script type="text/javascript" src="./AdmCommon.js"></script>
<script type="text/javascript">
$(document).ready(function() {
	pgMenu_rdy();
})
</script>
</head>
<body>
	<?php require_once("./AdmPgHeader.htm"); ?>
	<div class="dataArea">
		<div class="dataTitle centerMe" style="font-size: 2.0em;">請點選管理功能</div>
	</div>
</body>
</html>