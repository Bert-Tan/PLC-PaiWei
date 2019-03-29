<?php
/**********************************************************
 *			User Pai Wei Download Main Page - Chinese			      *
 **********************************************************/
 
	require_once( '../pgConstants.php' );
	require_once( 'dbSetup.php' );
	require_once("PaiWei_DBfuncs.php");
	
	session_start(); // create or retrieve

	if ( !isset( $_SESSION[ 'usrName' ] ) ) {
		header( "location: " . '../Login/Login.php' );
		exit;
	}

?>

<!DOCTYPE html>
<HTML>
<HEAD>
<TITLE>淨土念佛堂法會牌位下載主頁</TITLE>
<META HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=UTF-8">
<link rel="stylesheet" type="text/css" href="../master.css">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<style type="text/css">
table.dialog {
	width: 60%;
}

table.dialog td {
	padding-top: 2px;
	padding-left: 2vw;
	height: 6vh;
	font-size: 1.1em;
	text-align: left;
	vertical-align: top;
}

input[type=submit] {
	margin: auto;
	line-height: 40px;
	text-align:center;
	vertical-align: middle;
	font-size: 1.1em;
	background-color: aqua;
	border: 1px solid blue;
	border-radius: 10px;
}	
#myUpldTbl {
	table-layout: fixed;
	width: 60%;
	margin:auto;
	border: 4px ridge #00b300;
}

#myUpldTbl td {
	padding-left: 2vw;
	font-size: 1.2em;
	height: 8vh;
	border: 1px solid #00b300;
}

input[type=submit] {
	margin: auto;
	line-height: 40px;
	text-align:center;
	vertical-align: middle;
	font-size: 1.2em;	
}

</style>
</HEAD>
<BODY>
	<div class="dataArea"><!-- style="width: 60%; margin: auto; border: 2px solid #00b300;" -->
		<div id="forDnld"><!-- for download into the main PaiWei Admin Page -->
		<h2 style="margin-top: 4vh; text-align: center; letter-spacing: 20px;">
			下載牌位資料檔案
		</h2>		
		<!-- <form action="dnldPaiWei.php" method="post" enctype="multipart/form-data" id="dnldForm" -->
		<form action="dnldPaiWeiPDF.php" method="post" enctype="multipart/form-data" id="dnldForm"
			style="font-weight:bold; padding: 10px;">
			<table class="dialog">
				<tr><!-- Selection Row -->
			    <td>申請人:<br/>
						<?php echo userSelectionList(); ?>
			    </td>
		    	<td style="">請選擇下載牌位資料檔案:<br/>
						<select name="dbTblName" style="width: 230px; font-size: 1.2em;" required>
							<option value="">-- 請選擇牌位 --</option>
					  	<option value="C001A">祈福消災牌位</option>
						  <option value="W001A_4">超薦往生親友蓮位</option>
					    <option value="DaPaiWei">超薦一年內往生親友蓮位</option>
					    <option value="L001A">超薦歷代祖先牌位</option>
					    <option value="Y001A">超薦累劫冤親債主牌位</option>
					    <option value="D001A">超薦地基主牌位</option>
				  	</select>
			    </td>
			  </tr>
			  <tr><!-- Submit Row -->
			    <td colspan="2" style="text-align: center; vertical-align: middle; padding: 1vh 0px;">
			    	<input type="submit" value="下  載" name="submit">
			    </td>
		  	</tr>
		  </table>
		</form>
		</div>
	</div>
</BODY>
<HTML>