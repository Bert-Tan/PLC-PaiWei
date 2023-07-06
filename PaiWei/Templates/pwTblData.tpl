<!-- BEGIN data_tbl -->
<table class="dataRows" data-tblname="{dbTblName}">
  <tbody>
  	<!-- BEGIN data_row -->
    <tr data-keyn="{tupKeyN}" id="{tupKeyV}">
		<!-- BEGIN data_cell -->
		<td style="width: {cellWidth}">
			<input disabled type="text" data-fldn="{dbFldN}" data-oldv="{dbFldV}" value="{dbFldV}">
		</td>
		<!-- END data_cell -->
		<!-- BEGIN dataEditCol -->
		<td style="line-height: 3px;">
			<input class="editBtn" type="button" value="{editBtnTxt}">&nbsp;&nbsp;
			<input class="delBtn" type="button" value="{delBtnTxt}"><br><br> <!-- second 'br' add space between button rows -->				
			<input class="validBtn" type="button" value="{validBtnTxt}" {validDisableStr}>&nbsp;&nbsp;
			<input class="dupBtn" type="button" value="{dupBtnTxt}">			
		</td>
		<!-- END dataEditCol -->
	</tr>
    <!-- END data_row -->
  </tboby>
</table>
<!-- END data_tbl -->
