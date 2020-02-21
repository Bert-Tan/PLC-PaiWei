<!-- BEGIN hdr_tbl -->
<table class="dataHdr" data-tblName={tblName}>
  <thead>
    <tr><th colspan={numCols}>{htmlTblName}<br/>[&nbsp;&nbsp;User:&nbsp;&nbsp;{Who}{ico}&nbsp;&nbsp;]</th></tr>
    <tr>
		<!-- BEGIN hdr_cell -->
		<th style="{cellWidth}">{htmlFldName}</th>
		<!-- END hdr_cell -->
		<!-- BEGIN reqDateCol -->
		<th style="{dateFldWidth}">{dateFldName}</th>
		<!-- END reqDateCol -->
		<!-- BEGIN GongDeZhuCol -->
		<th style="{cellWidth}">{GongDeZhuFldName}</th>
		<!-- END GongDeZhuCol -->
		<!-- BEGIN dataEditCol -->
		<th>
			<input type="button" id="addRowBtn" value="{addBtnTxt}">&nbsp;
			<input type="button" id="delAllBtn" value="{delAllBtnTxt}">
		</th>
		<!-- END dataEditCol -->
    </tr>
  </thead>
</table>
<!-- END hdr_tbl -->
