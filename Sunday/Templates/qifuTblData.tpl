<!-- BEGIN data_tbl -->
<table class="dataRows" data-tblname="{dbTblName}">
  <tbody>
  	<!-- BEGIN data_row -->
    <tr data-keyn="{tupKeyN}" id="{tupKeyV}">
		<!-- BEGIN data_cell -->
		<td style="{cellWidth}">
			<input disabled type="text" data-fldn="{dbFldN}" data-oldv="{dbFldV}" value="{dbFldV}">
		</td>
		<!-- END data_cell -->
		<!-- BEGIN reqDateCol -->
		<td style="{dateFldWidth}">
			<input disabled type="text" data-fldn="reqDates" data-oldv="{dateFldV}" value="{dateFldV}">
		</td>
		<!-- END reqDateCol -->
		<!-- BEGIN GongDeZhuCol -->
		<!--
		<td style="{cellWidth}">
			<input disabled type="checkbox" data-fldn="GongDeZhu" data-oldv="{checkStr}" {checkStr}>
		</td>
		-->
		<!-- END GongDeZhuCol -->
		<!-- BEGIN dataEditCol -->
		<td>
			<input class="editBtn" type="button" value="{editBtnTxt}">&nbsp;&nbsp;
			<input class="delBtn" type="button" value="{delBtnTxt}">
		</td>
		<!-- END dataEditCol -->
	</tr>
    <!-- END data_row -->
  </tboby>
</table>
<!-- END data_tbl -->
