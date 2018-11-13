<!-- BEGIN data_tbl -->
<table id="myData" data-tblname="{dbTblName}">
  <tbody>
  	<!-- BEGIN data_row -->
    <tr data-keyn="{tupKeyN}" id="{tupKeyV}">
			<!-- BEGIN data_cell -->
			<td style="width: {cellWidth}">
			  <input disabled type="text" data-fldn="{dbFldN}" data-oldv="{dbFldV}" value="{dbFldV}">
			</td>
			<!-- END data_cell -->
			<!-- BEGIN dataEditCol -->
			<td class="oprCol" style="width: 28%;">
				<input class="editBtn" type="button" value="{editBtnTxt}">&nbsp;&nbsp;
				<input class="delBtn" type="button" value="{delBtnTxt}">
			</td>
			<!-- END dataEditCol -->
			<!-- BEGIN UsrSelColumn -->
			<td class="oprCol" style="width: 28%;">
				<select name="usr2Manage" ID="usr2Manage" style="left: 1.5vw; height: 25px;" disabled>
					<option value="">--Select User--</OPTION>
					<!-- BEGIN Option -->
					<option value="{usrName}">{usrName}</OPTION>
					<!-- END Option -->
				</SELECT>				
			</td>
			<!-- END UsrSelColumn -->
    </tr>
    <!-- END data_row -->
  </tboby>
</table>
<!-- END data_tbl -->
