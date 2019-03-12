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
	<div class="hdrRibbon">
		<img src="https://www.amitabhalibrary.org/pic/PLC_logo_TR.png" class="centerMeV" alt="">
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
					<th>用戶管理</th>
<?php
	}
?>   
					<th>更新法會資料</th>
					<th>為蓮友處理法會牌位</th>
					<th class="future">處理週日迴向申請</th>
					<th>用戶<br/>撤出</th>
				</tr>
			</thead>
		</table>
	</div>
	<div class="dataArea">
		<div class="dataTitle centerMe" style="font-size: 2.0em;">請點選管理功能</div>
	</div>
</body>
</html>