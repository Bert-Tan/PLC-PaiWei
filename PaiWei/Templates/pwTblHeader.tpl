<!-- BEGIN hdr_tbl -->
<table id="myDataHdr">
  <thead>
    <tr><th colspan={numCols}>{htmlTblName}<br/>[&nbsp;&nbsp;User:&nbsp;&nbsp;{Who}{ico}&nbsp;&nbsp;]</th></tr>
    <tr>
			<!-- BEGIN hdr_cell -->
			<th style="width: {cellWidth}">{htmlFldName}</th>
			<!-- END hdr_cell -->
			<!-- BEGIN dataEditCol -->
			<th class="oprCol" style="width: 28%;">
				<input type="button" id="addRowBtn" value="{addBtnTxt}">&nbsp;
				<input type="button" id="srchBtn" value="{srchBtnTxt}">
			</th>
			<!-- END dataEditCol -->
			<!-- BEGIN fillerCol -->
			<th class="oprCol" style="width: 28%;">
			</th>
			<!-- END fillerCol -->
    </tr>
  </thead>
</table>
<!-- END hdr_tbl -->
