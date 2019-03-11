<!-- BEGIN usrRow -->
<tr>
	<td data-tblN="{tblName}" data-key="{ID}">({tblName})&nbsp;&nbsp;&nbsp;{usrName}</td>
	<td class="uClass">
		<select name="SessTyp" style="width: 60%; !important;">
			<option data-fldV="SESS_TYP_USR" selected>一般用戶</option>
			<option data-fldV="SESS_TYP_MGR">一般管理員</option>
			<option data-fldV="SESS_TYP_WEBMASTER">網站管理員</option>
		</select>
	</td>
	<td>
		<input type="button" name="setUsrClass" value="更新">&nbsp;&nbsp;&nbsp;
		<input type="button" name="delUsr" value="刪除">
	</td>
</tr>
<!-- END usrRow -->