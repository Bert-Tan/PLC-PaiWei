<?php
/**********************************************************
 *						 User Pai Wei Upload Main Page			      	*
 **********************************************************/
 
	require_once( '../pgConstants.php' );
	require_once( 'dbSetup.php' );
	require_once( 'ChkTimeOut.php' );

	$sessLang = SESS_LANG_CHN; // default
	if ( isset ( $_GET[ 'l' ] ) ) {
		$sessLang = ( $_GET[ 'l' ] == 'e' ) ? SESS_LANG_ENG : SESS_LANG_CHN;
	} else if ( isset( $_SESSION[ 'sessLang' ] ) ) {
		$sessLang = $_SESSION[ 'sessLang' ];
	}	
	$_SESSION[ 'sessLang' ] = $sessLang;

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
<link rel="stylesheet" type="text/css" href="https://www.amitabhalibrary.org/css/base.css">
<link rel="stylesheet" type="text/css" href="../css/admin.css">
<link rel="stylesheet" type="text/css" href="../css/menu.css">
<link rel="stylesheet" type="text/css" href="./PaiWei.css">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script src="../futureAlert.js"></script>
<script src="./PaiWei.js"></script>
<style type="text/css">
	
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
		<div id="forUpld"><!-- for upload into the main PaiWei Admin Page -->
<?php
	if ( $useChn ) {
?>
		<h2 style="margin-top: 4vh; text-align: center; letter-spacing: 20px;">
			上載牌位資料檔案
		</h2>
		<img src="./img/stop.png" alt="" style="display: block; height: 60px; width: 60px; margin: auto;"><br/>
		<div style="text-align: left; margin: auto; width: 80%; font-size: 1.2em;">
			<span style="font-weight: bold; color: blue;">本館填寫牌位的軟體可以處理牌位資料上載，但上載的牌位資料檔案必須符合下列要求。</span>
			<ol style="letter-spacing: 1px;">
				<li>檔案是用 UTF-8 編碼 (UTF-8 encoded)；</li>
				<li>檔案中每一行僅含一個牌位的資料；</li>
				<li>每一行的資料字段間，必須以一個逗號分開 (comma separated values, CSV)；</li>			
				<li>每一個資料字段本身必須是一行連續的文字，不可再有換行;</li>
				<li>若資料字段本身中仍需有標點，則字段本身必須用雙引號 ( “ ) 將其囊括；<br/>如 “示範，字段一“，“示範，字段二“。</li>
			</ol>
			最好的辦法是 <a href="./Templates/pwTemplate.xlsx"><b>下載此樣式檔案</b></a>，並參考
			<a href="./Templates/pwTemplate.xlsx" class="soon"><b>用戶指南</b></a>，然後用微軟的 EXCEL 來書寫牌位，存檔，
			再上載資料檔案即可。謝謝！
		</div><br/>
		<form action="upldPaiWei.php" method="post" enctype="multipart/form-data" id="upldForm"
			style="font-weight:bold; padding: 10px;">
			<table id="myUpldTbl">
				<tr>
		    	<td style="">請選擇上載牌位資料檔案:<br/>
		    		<!-- div style="width: 90%; margin: auto;text-align: left;"><br/-->
							<input type="file" name="upldedFiles" id="fileToUpload" style="font-size: 1.0em;">
						<!--/div-->
					</td>
					<td style="">牌位是為了:<br/>
						<select name="dbTblName" style="width: 250px; font-size: 1.2em;" required>
						<option value="">--請選擇牌位用途--</option>
				  	<option value="C001A">祈福消災</option>
					  <option value="W001A_4">超薦往生超過一年的親友</option>
					  <!-- option value="W001A_4">超薦往生超過一年的親友(高雄元亨寺)</option -->
				    <option value="DaPaiWei">超薦一年(12個月)之內往生的親友</option>
				    <option value="L001A">超薦歷代祖先</option>
				    <option value="Y001A">超薦累劫冤親債主</option>
				    <option value="D001A">超薦地基主</option>
			    </td>
			  </tr>
			  <tr>
			    <td colspan=2 style="text-align: center;">
			    	<input type="submit" value="上  載" name="submit">
			    </td>
		  	</tr>
		  </table>
		</form>
<?php
	} else {
?>	
		<h2 style="margin-top: 4vh; text-align: center; letter-spacing: normal;">
			Upload Name Plaque Data Files in CSV Format
		</h2>
		<img src="./img/stop.png" alt="" style="display: block; height: 60px; width: 60px; margin: auto;"><br/>
		<div style="text-align: left; margin: auto; width: 80%; font-size: 1.2em;">
			<span style="font-weight: bold; color: blue;">
				This page supports uploading name plaque data files，but, they must comply with the following requirements.</span>
			<ol style="letter-spacing: 1px;">
				<li>The data file must be UTF-8 encoded;</li>
				<li>Each line must contain ONLY ONE name plaque data;</li>
				<li>Fields in each line must be comma separated, i.e., comma separated values, CSV;</li>			
				<li>Every field must NOT have line breaks;</li>
				<li>A field data must be enclosed by double-quotes (i.e., " ) if it contains other punctuations.
					For example, "field 1 text 1; field 1, text 2".</li>
			</ol>
			The best approach is to <a href="./Templates/pwTemplate.xlsx"><b>download this template</b></a>, refer to
			the <a href="./Templates/pwTemplate.xlsx" class="soon"><b>User Guide</b></a>, to use MS EXCEL to compile and generate
			the CSV files，and then upload them. Thank you.
		</div><br/>
		<form action="upldPaiWei.php" method="post" enctype="multipart/form-data" id="upldForm"
			style="font-weight:bold; padding: 10px;">
			<table id="myUpldTbl">
				<tr>
		    	<td style="">Please Select a File to Upload:<br/>
		    		<!-- div style="width: 90%; margin: auto;text-align: left;"><br/-->
							<input type="file" name="upldedFiles" id="fileToUpload" style="font-size: 1.0em;">
						<!--/div-->
					</td>
					<td style="">The File is for:<br/>
						<select name="dbTblName" style="width: 250px; font-size: 1.2em;" required>
						<option value="">--Please Select a Type--</option>
				  	<option value="C001A">Well Blessing</option>
					  <option value="W001A_4">Deceased</option>
				    <option value="DaPaiWei">Recently Deceased (within 12 months)</option>
				    <option value="L001A">Ancestors</option>
				    <option value="Y001A">Karmic Creditors</option>
				    <option value="D001A">Site Guardians</option>
			    </td>
			  </tr>
			  <tr>
			    <td colspan=2 style="text-align: center;">
			    	<input type="submit" value="Upload" name="submit">
			    </td>
		  	</tr>
		  </table>
		</form>
<?php
	}
?>
		</div>
	</div>
</BODY>
<HTML>
