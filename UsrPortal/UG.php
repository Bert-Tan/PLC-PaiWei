<!--
 **********************************************************
 *          Admin Main Page User Guide - Chinese          *
 **********************************************************
-->

<!DOCTYPE html>
<html>
<head>
<title>淨土念佛堂牌一般用戶指南</title>
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
			淨土念佛堂一般用戶將可經由此主頁使用本網頁提供的一些服務功能；譬如法會牌位申請、週日早課祈福及迴向申請、結緣法寶申請、
			課程註冊與報名，等等。但首先，您必須先註冊一個登錄帳戶。
		</span><br/>
		<div style="width: 95%; margin:auto; font-size: 1.3em;">
			<ol>
				<li>要註冊一個登錄帳戶，請到『 <a href="../index.php">淨土念佛堂用戶主頁</a> 』或本頁, 點擊在頁首的『 
					<a href="../Login/Login.php?l=c">一般用戶登錄</a> 』；如下圖。<br/><br/>
					<!-- img src="https://www.amitabhalibrary.org/admin/UsrPortal/img/Click2Login.png" alt="" -->
					<img src="./img/Click2Login.png" width="85%;"alt="" style="border: 1px solid black;">
				</li><br/>
				<li>當您點擊『一般用戶登錄』之後，您會看到登錄主頁，登錄窗口如下圖所示。<br/><br/>
					<!-- img src="https://www.amitabhalibrary.org/admin/UsrPortal/img/LoginInput.png" alt="" -->
					<img src="./img/LoginInput.png" width="85%" alt="" style="border: 1px solid black;">
				</li><br/>
				<li>
					如果您已註冊過登錄名與登錄密碼，請在個別的字段中，輸入登錄名與登錄密碼，然後點擊『登錄』，即可登錄。
				</li><br/>
				<li>
					如果您是新的用戶，請在『我是新用戶』的框中打鉤，並輸入您的電子郵件地址，再點擊『登錄』，即可登錄。
				</li><br/>
				<li>
					如果您不慎忘記了登錄名與登錄資料，請點擊『忘了登錄密碼』；本網頁會幫助您恢復登錄密碼。
				</li><br/>
				<li>
					當您登錄之後，您會看到用戶功能選項的主頁，在此，您即可選擇所要的功能。<br/>
					(目前，本網站僅提供法會牌位申請的功能，其他功能會陸續的提供。)<br/><br/>
					<img src="./img/uPortalMain.png" width="85%" alt="" style="border: 1px solid black;">
				</li>
			</ol>
		</div>
	</div>
</body>
</html>
