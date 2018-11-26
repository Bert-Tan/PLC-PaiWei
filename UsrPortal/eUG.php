<!--
 **********************************************************
 *           Admin Main Page User Guide - English         *
 **********************************************************
-->
<?php
?>

<!DOCTYPE html>
<html>
<head>
<title>Pure Land Center User Portal Guide</title>
<meta http-equiv="content-type" content="text/html; charset=UTF-8">
<link rel="stylesheet" href="https://www.amitabhalibrary.org/css/base.css">
<link rel="stylesheet" href="../css/admin.css">
<link rel="stylesheet" href="../css/menu.css">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script src="../futureAlert.js"></script>
<script>
	$(document).ready(function() {
		$(".future").on( 'click', futureAlert );
		$(".soon").on( 'click', soonAlert );
	});
</script>

</head>
<body>
	<div class="hdrRibbon">
		<img src="https://www.amitabhalibrary.org/pic/PLC_logo_TR.png" alt="">
		<div id="pgTitle" class="centerMeV">
			<span style="letter-spacing: 10px;">淨土念佛堂一般用戶指南</span><br/>
			<span class="engClass" style="font-size:0.9em;">Pure Land Center User Portal Guide</span>
		</div>
		<table id="myMenuTbl" class="centerMeV">
			<thead>
				<tr>
					<th><a href="../Login/Login.php?l=c" class="myLinkButton" style="line-height: 1.3em;">一般用戶登錄<br/>(中文)</a></th>
					<th><a href="./UG.php" class="myLinkButton" style="line-height: 1.3em;">用戶指南<br/>(中文)</a></th>
					<th><a href="../Login/Login.php?l=e" class="myLinkButton" style="line-height: 1.3em;">User Login<br/>(English)</a></th>
					<th><a href="./eUG.php" class="myLinkButton" style="line-height: 1.3em;">User Guide<br/>(English)</a></th>
					<th><a href="../Login/aLogin.php" class="myLinkButton future" style="line-height: 2.6em;">管理員登錄</a></th>
				</tr>
			</thead>
		</table>
		<div id="pgLogOut" class="centerMeV"><a href="../Login/Logout.php" class="myLinkButton">用戶<br/>撤出</a></div>	
	</div>
	<div class="dataArea" style="overflow-y: auto;">
		<span style="display: block; width: 95%; margin: auto; padding-top: 2vh; font-size: 1.3em; font-weight: bold;
			line-height: 1.4em;">
			Users can access various functions via the Pure Land Center User Portal;
			for example, request for merit dedication and/or well-blessing during
			retreat ceremonies or Sunday chantings, enroll in classes, etc.
			First, though, you must obtain a login ID.
		</span><br/>
		<div style="width: 95%; margin:auto; font-size: 1.3em;">
			<ol>
				<li>To obtain a login ID，please go to the <a href="../index.php">Pure Land Center User Portal</a>
					or on this page, click "<a href="../Login/Login.php?l=e">User Login</a>" as shown below。<br/><br/>
					<!-- img src="https://www.amitabhalibrary.org/admin/UsrPortal/img/Click2Login.png" alt="" -->
					<img src="./img/Click2Login.png" width="85%;"alt="" style="border: 1px solid black;">
				</li><br/>
				<li>After you click the "User Login", you will see the Login Page
					with a login dialog box as shown below: <br/><br/>
					<!-- img src="https://www.amitabhalibrary.org/admin/UsrPortal/img/LoginInput.png" alt="" -->
					<img src="./img/eLoginInput.png" width="85%" alt="" style="border: 1px solid black;">
				</li><br/>
				<li>
					If you already have a login ID and password, please enter them in the respective
					fields, and click "Login".
				</li><br/>
				<li>
					If you are a new user, please check mark the "I am a new user" checkbox,
					and click "Login".
				</li><br/>
				<li>
					If you forgot your credentials, please click "Forgot Password", we will help
					you recover.
				</li><br/>
				<li>
					After you logged in, you will on the main page to select among the available
					user functions.<br/>(Note that at present, the only supported user function is
					the request for merit dedication name plaques durint retreats.
					Other functions will be available in the future.)<br/><br/>
					<img src="./img/euPortalMain.png" width="85%" alt="" style="border: 1px solid black;">
				</li><br/>
			</ol>
		</div>
	</div>
</body>
</html>
