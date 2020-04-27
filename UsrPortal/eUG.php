<!--
 **********************************************************
 *           Admin Main Page User Guide - English         *
 **********************************************************
-->

<!DOCTYPE html>
<html>
<head>
<title>Pure Land Center User Portal Guide</title>
<meta http-equiv="content-type" content="text/html; charset=UTF-8">
<link rel="stylesheet" href="../master.css">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script type="text/javascript">
	var _appPlaces = {
		'urlWebsiteHome' : "https://www.amitabhalibrary.org/",
		"uLogin" : "../Login/Login.php?l=c",
		"UG" : "./UG.php",
		"eLogin" : "../Login/Login.php?l=e",
		"eUG" : "./eUG.php",
		"aLogin" : "../Login/aLogin.php",
		"usrLogout" : "../Login/Logout.php"
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
			<span style="letter-spacing: 6px;">淨土念佛堂一般用戶指南</span><br/>
			<span class="engClass">Pure Land Center User Portal Guide</span>
		</div>
		<table class="pgMenu centerMeV">
			<thead>
				<tr>
					<th data-urlIdx="urlWebsiteHome">網站首頁<br/>Homepage</th>	
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
					or, click "<a href="../Login/Login.php?l=e">User Login</a>" on this page as shown below。<br/><br/>
					<!-- img src="https://www.amitabhalibrary.org/admin/UsrPortal/img/Click2Login.png" alt="" -->
					<img src="./img/Click2Login.png" width="85%;"alt="" style="border: 1px solid black;">
				</li><br/>
				<li>After you click "User Login", you will see the Login Page
					with a login dialog box as shown below: <br/><br/>
					<!-- img src="https://www.amitabhalibrary.org/admin/UsrPortal/img/LoginInput.png" alt="" -->
					<img src="./img/eLoginInput.png" width="85%" alt="" style="border: 1px solid black;">
				</li><br/>
				<li>
					If you already have a login ID and password, please enter them in the respective
					fields, and click "Login".
				</li><br/>
				<li>
					If you are a new user, please check the "I am a new user" checkbox,
					and click "Login".
				</li><br/>
				<li>
					If you forgot your credentials, please click "Forgot Password", we will help
					you recover it.
				</li><br/>
				<li>
					After you are logged in, you will be directed to the main page to select among the available
					user functions.<br/>(Note that at present, the only supported user function is
					the request for merit dedication name plaques during retreats.
					Other functions will be available in the future.)<br/><br/>
					<img src="./img/euPortalMain.png" width="85%" alt="" style="border: 1px solid black;">
				</li><br/>
			</ol>
		</div>
	</div>
</body>
</html>
