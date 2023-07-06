<!-- BEGIN hdr_tbl -->
<table class="dataHdr">
  <thead>
    <tr>
	<th colspan={numCols}>
		{htmlTblName}<br/>[&nbsp;&nbsp;User:&nbsp;&nbsp;{Who}{ico}&nbsp;&nbsp;]<br/>
		<span>{pwFilter}</span>
		<select id="pwStatusSel" name="pwStatus" style="font-size: 1em; background-color: aqua; border: 1px solid blue;">
        	<!-- $pwStatus: ENUM('ALL','VALID', 'INVALID') DEFAULT 'ALL' -->
        	<option value="ALL">{pwStatusAll}</option>
        	<option value="VALID">{pwStatusValid}</option>
        	<option value="INVALID">{pwStatusInvalid}</option>
        </select>
	</th>
	</tr>
    <tr>
		<!-- BEGIN hdr_cell -->
		<th style="width: {cellWidth}">{htmlFldName}</th>
		<!-- END hdr_cell -->
		<!-- BEGIN dataEditCol -->
		<th style="line-height: 2px;">
			<input type="button" id="addRowBtn" value="{addBtnTxt}">&nbsp;
			<input type="button" id="delAllBtn" value="{delAllBtnTxt}"><br><br> <!-- second 'br' add space between button rows -->
			<input type="button" id="srchBtn" value="{srchBtnTxt}">&nbsp;
			<input type="button" id="validAllBtn" value="{validAllBtnTxt}">
		</th>
		<!-- END dataEditCol -->
    </tr>
  </thead>
</table>
<!-- END hdr_tbl -->
