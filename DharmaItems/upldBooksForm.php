<?php
/**********************************************************
 *				    Admin Book Upload Main Page           *
 **********************************************************/
 
	require_once( '../pgConstants.php' );
	require_once( 'dbSetup.php' );
	require_once( 'ChkTimeOut.php' );

	$sessLang = $_SESSION[ 'sessLang' ];
	$hdrURL = URL_ROOT . "/admin/index.php";
	$useChn = ( $sessLang == SESS_LANG_CHN );
	if ( !isset( $_SESSION[ 'usrName' ] ) ) {
		header( "location: " . $hdrURL );
	}

?>

<!DOCTYPE html>
<HTML>
<HEAD>
<TITLE>淨土念佛堂法會牌位上載主頁</TITLE>
<META HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=UTF-8">
<link rel="stylesheet" type="text/css" href="../master.css">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script src="../futureAlert.js"></script>
<script src="./DI_shippAddr.js"></script>
<script src="./DI_applForm.js"></script>
<style type="text/css">
/* localization */
table.dialog {
	width: 50%;
}

input[type=submit] {
	margin: auto;
	line-height: 40px;
	text-align:center;
	vertical-align: middle;
	font-size: 1.1em;
	background-color:aqua;
	border-radius: 10px;
}

</style>
</HEAD>
<BODY>
	<div class="dataArea"><!-- style="width: 60%; margin: auto; border: 2px solid #00b300;" -->
		<div id="forUpld"><!-- for upload into the main Dharma Items Admin Page -->
		<h2 style="margin-top: 4vh; text-align: center; letter-spacing: 20px;">
			上載書目資料檔案
		</h2>
		<form action="upldBooks.php" method="post" enctype="multipart/form-data" id="upldForm"
			style="font-weight:bold; padding: 5px;">
			<table class="dialog">
				<tr>
		    		<td style="width: 150px;">請選擇上載書目資料檔案:<br/>
						<input type="file" name="upldFiles" id="fileToUpload" style="font-size: 1.0em;">
					</td>
					<td style="width: 150px;">請指示檔案所用文字:<br/>
						<select name="dbTblName" style="width: 150px; font-size: 1.1em;" required>
				  		<option value="INVT_BK_C" selected>中文</option>
						<option value="INVT_BK_E">英文</option>
			    	</td>
				</tr>
				<tr>
			    	<td colspan=2 style="text-align: center; vertical-align: middle; padding: 1vh 0px;">
			    		<input type="submit" value="上  載" name="submit">
			    	</td>
				</tr>
		  </table>
		</form>
		</div>
	</div>
</BODY>
<HTML>
