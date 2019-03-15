<?php
 /**********************************************************
  *                     Admin Main Page                    *
  **********************************************************/

	require_once("./pgConstants.php");
	require_once("Login/Login_Funcs.php");
	$msgTxt = '';
	if ( isset($_GET[ 'r' ]) ) {
		$mbxBC = "red";
		$msgTxt = "登&nbsp;錄&nbsp;時&nbsp;段&nbsp;已&nbsp;過&nbsp;期！<br/>Session has expired!";
	} else {
		$mbxBC= "#00b300";
		$msgTxt = "歡迎您到淨土念佛堂用戶主頁！<br/>Welcome to the Pure Land Center User Portal!";
	}
?>

<!DOCTYPE html>
<html>
<head>
<title>淨土念佛堂牌用戶主頁</title>
<meta http-equiv="content-type" content="text/html; charset=UTF-8">
<link rel="stylesheet" href="./master.css">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script type="text/javascript">
	var _appPlaces = {
		"uLogin" : "./Login/Login.php?l=c",
		"UG" : "./UsrPortal/UG.php",
		"eLogin" : "./Login/Login.php?l=e",
		"eUG" : "./UsrPortal/eUG.php",
		"aLogin" : "./Login/aLogin.php",
		"usrLogout" : "./Login/Logout.php"
	};
	$(document).ready(function () {
		$("th[data-urlIdx]").on( 'click', function() {
			location.replace( _appPlaces[ $(this).attr( "data-urlIdx" ) ]);
		});
	});
</script>
<style>
/* local override */
table.pgMenu th:last-child {
	width: 5vw;
	border-left: 1px;
}
</style>

</head>
<body>
	<div class="hdrRibbon">
		<img src="https://www.amitabhalibrary.org/pic/PLC_logo_TR.png" class="centerMeV" alt="">
		<div id="pgTitle" class="centerMeV">
			<span style="letter-spacing: 7px;">淨土念佛堂用戶主頁</span><br/>
			<span class="engClass">Pure Land Center User Portal</span>
		</div>
		<table class="pgMenu centerMeV">
			<thead>
				<tr>
					<th data-urlIdx="uLogin">一般用戶登錄<br/>(中文)</th>
					<th data-urlIdx="UG">用戶指南<br/>(中文)</th>
					<th data-urlIdx="eLogin">User Login<br/>(English)</a></th>
					<th data-urlIdx="eUG">User Guide<br/>(English)</th>
					<th data-urlIdx="aLogin">管理員登錄</th>
					<th data-urlIdx="usrLogout">用戶撤出<br/>Logout</th>
				</tr>
			</thead>
		</table>
	</div>
	<div class="dataArea">
		<div style="width: 40%; margin: auto; border: 7px solid; border-radius: 8px; padding: 2px 3px;
				font-size: 1.2em; font-weight: bold;
				text-align: center;
				letter-spacing: normal;
				display: block;
				border-color: <?php echo $mbxBC;?>;
				position: relative;
				top: +20%;">
			<?php echo $msgTxt; ?>
		</div>
		<div class="centerMe"
			style="font-size: 2em; font-weight: bold; text-align: center;">
			請&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;登&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;錄
			<br/>Please Login In
		</div>
	</div>
</body>
</html>