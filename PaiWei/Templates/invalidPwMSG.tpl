<!-- BEGIN msgBlock -->
<!DOCTYPE html>
<HTML>
<HEAD>
<TITLE>驗證牌位 VALIDATE Name Plaques</TITLE>
<META HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=UTF-8">
<link rel="stylesheet" href="https://www.amitabhalibrary.org/css/base.css">
<style>
table, th, td {
  border: 1px solid black;
  border-collapse: collapse;
}
table {
	margin: 3px;
}
th, td {
  padding: 3px;
}
.boldRed {
	font-weight: bold;
	color: red;
}
</style>
</HEAD>
<BODY> 
	<div style="margin-left: 15px; font-size: 1.1em; font-family: arial;">
	<!-- BEGIN Txt -->
	<div>
	淨土念佛堂的網站用戶 <i>{usrName}</i>：<br/>
	Dear Pure Land Center User <i>{usrName}</i>,<br/><br/>

	本念佛堂即將舉行的 <i>{rtNameChn}</i>，您有<b>未驗證</b>的牌位，請見下面的列表。<br/>
	For the incoming <i>{rtNameEng}</i>, you have <b>UNVALIDATED</b> Name Plaques that are listed below.<br/><br/>

	本念佛堂<span class="boldRed">不會</span>列印<b>未驗證</b>的牌位，請您務必于 <span class="boldRed">{pwExpireDate}</span> 前登録 <a href="https://www.amitabhalibrary.org/admin/PaiWei/paiweiWrapper.php?l=c" target="_blank">該網站</a> 驗證或修改牌位。<br/>
	The Center will <span class="boldRed">NOT</span> print <b>UNVALIDATED</b> Name Plaques. Please use <a href="https://www.amitabhalibrary.org/admin/PaiWei/paiweiWrapper.php?l=e" target="_blank">this link</a> to validate or edit name plaques by <span class="boldRed">{pwExpireDate}</span>.<br/><br/>

	如有問題，請與本念佛堂的義工 <a href="mailto:{adminUsrEmail}" target="_blank">{adminUsrEmail}</a> 聯絡。<br/>
	Please contact the name-plaque administrator via <a href="mailto:{adminUsrEmail}" target="_blank">{adminUsrEmail}</a> if having problems.<br/><br/>

	阿彌陀佛！<br/>
	Amituofo!<br/><br/><br/>
	</div>
	<!-- END Txt -->

	<!-- BEGIN PaiWei -->    
	<b>{tblName}:</b><br/>
	<table>      
		<!-- BEGIN hdr_row -->
		<tr>
			<!-- BEGIN hdr_cell -->
			<th>{fldName}</th>
			<!-- END hdr_cell -->
		</tr>
		<!-- END hdr_row -->
		<!-- BEGIN data_row -->
		<tr>
			<!-- BEGIN data_cell -->
			<td>{fldValue}</td>
			<!-- END data_cell -->
		</tr>
		<!-- END data_row -->        
	</table>
	<br/>
	<!-- END PaiWei -->

	
	<br/>
	<span style="color: gray;">
	淨土念佛堂<br/>
	Pure Land Center<br/>
	</span>
	</div>		
</BODY>
</HTML>
<!-- END msgBlock -->