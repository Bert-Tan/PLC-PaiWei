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
.smallGray {
	font-size: 0.9em;
	color: gray;
}
</style>
</HEAD>
<BODY> 
	<div style="margin-left: 15px; font-size: 1.1em; font-family: arial;">
	<!-- BEGIN Txt -->
	<div>
	<br/><span class="smallGray">(English readers please scroll down)<br/><br/></span>
	
	淨土念佛堂的網站用戶 <i>{usrName}</i>：<br/><br/>

	本念佛堂即將舉行的 <i>{rtNameChn}</i>，您有<b>尚未驗證</b>的牌位，請見列表於下。<br/><br/>

	本念佛堂法會<span class="boldRed">不會</span>列印供奉<b>未驗證</b>的牌位，請您務必於 <span class="boldRed">{pwExpireDate}</span> 之前登録 <a href="https://www.amitabhalibrary.org/admin/PaiWei/paiweiWrapper.php?l=c" target="_blank">該網站</a> 驗證或修改牌位。<br/><br/>
	
	如有問題，請與本念佛堂的義工 <a href="mailto:{adminUsrEmail}" target="_blank">{adminUsrEmail}</a> 聯絡。<br/><br/>

	謝謝。<br/><br/>

	阿彌陀佛！<br/>
	淨土念佛堂法會牌位組<br/><br/><br/>
	
	Dear Pure Land Center User with login ID <i>{usrName}</i>,<br/><br/>
	
	For the upcoming <i>{rtNameEng}</i>, you still have <b>UNVALIDATED</b> Name Plaques listed below.<br/><br/>
	
	The center will <span class="boldRed">NOT</span> print and offer <b>UNVALIDATED</b> Name Plaques in the retreat. Please use <a href="https://www.amitabhalibrary.org/admin/PaiWei/paiweiWrapper.php?l=e" target="_blank">this link</a> to validate or edit them by <span class="boldRed">{pwExpireDate}</span>.<br/><br/>
		
	Please contact the administrator, email: <a href="mailto:{adminUsrEmail}" target="_blank">{adminUsrEmail}</a>, for any questions.<br/><br/>

	Thank you.<br/><br/>

	Amituofo!<br/>
	Retreat Administration of Pure Land Center<br/><br/><br/>
	</div>
	<!-- END Txt -->

	<span class="smallGray">--------------- 未驗證的牌位 UNVALIDATED Name Plaques ---------------<br/><br/></span>
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
	</div>		
</BODY>
</HTML>
<!-- END msgBlock -->