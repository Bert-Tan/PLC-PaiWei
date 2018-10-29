<!--
 **********************************************************
 *										 Admin Main Page										*
 **********************************************************
-->
<!DOCTYPE html>
<html>
<head>
<title>淨土念佛堂牌用戶主頁</title>
<meta http-equiv="content-type" content="text/html; charset=UTF-8">
<link rel="stylesheet" href="https://www.amitabhalibrary.org/css/base.css">
<link rel="stylesheet" href="./css/admin.css">
<link rel="stylesheet" href="./css/menu.css">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script src="./futureAlert.js"></script>
<script type="text/javascript">
	$(document).ready(function() {
		$(".future").on( 'click', futureAlert );
		$(".soon").on( 'click', soonAlert );
	})
</script>

<style>
.engClass {
	font-size: 0.85em;
}

.dataArea table {
	border-collapse: separate;
	border-spacing: 10px 10px;
}

.dataArea th {
	height: 8vh;
	padding: 0px 5px;
	font-size: 1.2em;
}

</style>

</head>
<body>
	<div class="hdrRibbon">
		<img src="https://www.amitabhalibrary.org/pic/PLC_logo_TR.png" alt="">
		<div id="pgTitle" class="centerMeV">
			<span style="letter-spacing: 10px;">淨土念佛堂用戶主頁</span><br/>
			<span class="engClass">Pure Land Center User Portal</span>
		</div>
		<table id="myMenuTbl" class="centerMeV" style="table-layout: fixed;">
			<thead>
				<tr>
					<th><a href="./Login/Login.php?l=c" class="myLinkButton" style="line-height: 1.3em;">一般用戶登錄<br/>(中文)</a></th>
					<th><a href="./UsrPortal/UsrGuide.php" class="myLinkButton soon" style="line-height: 1.3em;">用戶指南<br/>(中文)</a></th>
					<th><a href="./Login/Login.php?l=e" class="myLinkButton" style="line-height: 1.3em;">User Login<br/>(English)</a></th>
					<th><a href="./Login/eUsrGuide.php" class="myLinkButton future" style="line-height: 1.3em;">User Guide<br/>(English)</a></th>
					<th><a href="./Login/aLogin.php" class="myLinkButton future" style="line-height: 2.6em;">管理員登錄</a></th>
				</tr>
			</thead>
		</table>
		<div id="pgLogOut" class="centerMeV"><a href="./Login/Logout.php" class="myLinkButton">用戶<br/>撤出</a></div>	
	</div>
	<div class="dataArea">
<?php
	if ( isset($_GET[ 'r' ]) ) {
?>
		<div class="msgBox" style="width: 30%; border-color: red;">
			登&nbsp;錄&nbsp;時&nbsp;段&nbsp;已&nbsp;過&nbsp;期！<br/>
			Session has expired!		
		</div>
<?php
	} else {
?>
		<div class="msgBox" style="width: 40%; border-color: #00b300;">
			歡迎您到淨土念佛堂牌用戶主頁！<br/>
			Welcome to the Pure Land Center User Portal!		
		</div>
<?php
	}
?>
		<div class="centerMe" id="myDataTitle"
			style="font-size: 2em; font-weight: bold; text-align: center;">
			請&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;登&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;錄
			<br/>Please Login In
		</div>
	</div>
</body>
</html>
