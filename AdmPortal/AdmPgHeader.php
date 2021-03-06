<!-- Admin Page Common Header to be included by Admin Applications:
    index.php, AdmUMgr.php, paiweiMgr.php, sundayMgr.php
--> 
    <div class="hdrRibbon">
		<img src="https://www.amitabhalibrary.org/pic/PLC_logo_TR.png" class="centerMeV" alt="">
		<div id="pgTitle" class="centerMeV">
			<span style="letter-spacing: 1px;">淨土念佛堂管理用戶主頁</span><br/>
			<span class="engClass">Pure Land Center Admin Portal</span>
		</div>
		<table class="pgMenu centerMeV">	
			<thead>
				<tr>
					<th data-urlIdx="urlWebsiteHome">回到<br/>網站首頁</th>
					<th data-urlIdx="urlAdmHome">回到<br/>管理主頁</th>
<?php
	if ( $_SESSION[ 'sessType' ] == SESS_TYP_WEBMASTER ) {
?>
					<th data-urlIdx="urlUmgr">用戶管理</th>
<?php
	}
?>
					<th data-urlIdx="urlPaiWeiMgr">處理<br/>法會牌位</th>
					<th data-urlIdx="urlSundayMgr">處理<br/>週日迴向申請</th>
					<th data-urlIdx="usrLogout">用戶<br/>撤出</th>
				</tr>
			</thead>
		</table>
	</div>