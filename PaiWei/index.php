<?php
/**********************************************************
 *           User Pai Wei Application Main Page           *
 **********************************************************/
 
	require_once( '../pgConstants.php' );
	require_once( 'dbSetup.php' );
	require_once( 'ChkTimeOut.php' );

	function xLate( $what ) {
		global $sessLang;
		$htmlNames = array (
			'htmlTitle' => array (
				SESS_LANG_CHN => "淨土念佛堂法會牌位申請主頁",
				SESS_LANG_ENG => "Retreat Merit Dedication Application Page" ),
			'logOut' => array (
				SESS_LANG_CHN => "用戶<br/>撤出",
				SESS_LANG_ENG => "User<br/>Logout" ),
			'h1Title' => array (
				SESS_LANG_CHN => "請由上列點擊所要<br/>申請的牌位或功能",
				SESS_LANG_ENG => "Please Select Name Plaque Type From<br/>The Above You Want to Apply for" ),
			'pwC' => array (
				SESS_LANG_CHN => "祈福消災牌位",
				SESS_LANG_ENG => "Well Blessing" ),
			'pwD' => array (
				SESS_LANG_CHN => "地基主蓮位",
				SESS_LANG_ENG => "Site Guardians" ),
			'pwL' => array (
				SESS_LANG_CHN => "歷代祖先蓮位",
				SESS_LANG_ENG => "Ancestors" ),
			'pwW' => array (
				SESS_LANG_CHN => "往生者蓮位",
				SESS_LANG_ENG => "Deceased" ),
			'pwY' => array (
				SESS_LANG_CHN => "累劫冤親債主蓮位",
				SESS_LANG_ENG => "Karmic Creditors" ),
			'pwBIG' => array (
				SESS_LANG_CHN => "(一年內)往生者蓮位",
				SESS_LANG_ENG => "Recently Deceased" ),
			'pwUpld' => array (
				SESS_LANG_CHN => "上載牌位檔案",
				SESS_LANG_ENG => "Upload CSV Files" ),
			'pwUG' => array (
				SESS_LANG_CHN => "用戶指南",
				SESS_LANG_ENG => "User Guide" ),
			'alertMsg' => array (
				SESS_LANG_CHN => "除有特殊困難，牌位申請者須本人親自<br/>( 或由指定代表 ) 前來參加法會。",
				SESS_LANG_ENG => "Note: You or your designee shall be present in the retreat unless you have difficulties." )
		);
		return $htmlNames[ $what ][ $sessLang ];
	} // function xLate();
	
	$hdrLoc = "location: " . URL_ROOT . "/admin/index.php";
//	session_start(); // create or retrieve
	if ( !isset( $_SESSION[ 'usrName' ] ) ) {
		header( $hdrLoc );
	}
	$sessLang = $_SESSION[ 'sessLang' ];
	$sessType = $_SESSION[ 'sessType' ];
	$useChn = ( $sessLang == SESS_LANG_CHN );
//	$dnldUrl = URL_ROOT . "/admin/PaiWei/dnldPaiWeiForm.php";
?>

<!DOCTYPE html>
<html>
<head>
<title><?php echo xLate( 'htmlTitle' ); ?></title>
<meta http-equiv="content-type" content="text/html; charset=UTF-8">
<link rel="stylesheet" type="text/css" href="../master.css">
<link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<link rel="stylesheet" type="text/css" href="./toolTip.css">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="../futureAlert.js"></script>
<script src="./PaiWei.js"></script>

<!-- The following CSS is for the upload Form which will be loaded from the other HTML/PHP file. -->
<style type="text/css">
/* localization */
.engClass {
	font-size: 0.7em;
}

table.pgMenu tr th:last-child {
	border-left: 1px solid white;
}

/* for loaded PaiWei Upload Form */
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

/* for loaded User Guide */
.UGsteps {
	font-size: 0.9em;
}

.UGsteps th, td {
	vertical-align: top;
}

.UGsteps th {
	width: 15%;
}

.UGstepImg {
	width: 90%;
	height: auto;
	border: 1px solid black;
}

/* localization for loaded data tables */

table.dataHdr, table.dataRows {
	table-layout: auto;
}

table.dataHdr tr th:last-child, table.dataRows tr td:last-child {
	width: 28%;
}

table.dataHdr th, table.dataRows td {
	height: 22px;
	line-height: 1.2em;
}

table.dataRows tr td:not(:last) {
	text-align: left;
}

/* local specific for Data Input fields */
input {
	font-size: 1.0em;
}

input[type=button] {
  background-color: aqua;
  text-align: center;
  display: inline-block;
  border: 1px solid blue;
  border-radius: 4px;
}

input[type=text] {
	width: 100%;
	box-sizing: border-box;
    -moz-box-sizing: border-box;
    -webkit-box-sizing: border-box;
}

table.dataRows tr:nth-child(odd) input[type=text] {
	background-color: #ffffe6;
}

table.dataRows tr:nth-child(even) input[type=text] {
	background-color: #ffffcc;
}
</style>

</head>
<body>
	<div class="hdrRibbon">
		<img src="https://www.amitabhalibrary.org/pic/PLC_logo_TR.png" class="centerMeV" alt="">
		<div id="pgTitle" class="centerMeV">
			<span style="letter-spacing: 1px;">淨土念佛堂法會牌位申請主頁</span><br/>
			<span class="engClass">Retreat Merit Dedication Application Page</span>
		</div>
		<table class="pgMenu centerMeV" style="<?php if ( $sessType != SESS_TYP_USR ) { echo 'width: 50vw;'; } ?>">
			<thead>
				<tr>
<?php
	if ( $sessType != SESS_TYP_USR ) {
?>		
					<th rowSpan="2" data-urlIdx="urlAdmHome" style="width: 4.2vw;">回到<br/>管理主頁</th>
<?php
	}
?>
					<th class="pwTbl" data-tbl="W001A_4"><?php echo xLate( 'pwW' ); ?></th>
					<th class="pwTbl" data-tbl="L001A"><?php echo xLate( 'pwL' ); ?></th>
					<th class="pwTbl" data-tbl="Y001A"><?php echo xLate( 'pwY' ); ?></th>
<?php
	if ( $sessType == SESS_TYP_USR ) {
?>
					<th class="ugld"><?php echo xLate( 'pwUG' ); ?></th>
<?php
	} else {
?>
					<th id="dnld" data-urlIdx="urlDnld">下載牌位檔案</th>
<?php
	}
?>
					<th rowSpan="2" data-urlIdx="usrLogout"><?php echo xLate('logOut');?></th>
				</tr>
				<tr>
					<th class="pwTbl" data-tbl="DaPaiWei"><?php echo xLate( 'pwBIG' ); ?></th>
					<th class="pwTbl" data-tbl="C001A"><?php echo xLate( 'pwC' ); ?></th>
					<th class="pwTbl" data-tbl="D001A"><?php echo xLate( 'pwD' ); ?></th>
					<th id="upld"><?php echo xLate( 'pwUpld' ); ?></th>
				</tr>
			</thead>
		</table>
	</div>
	<div class="dataArea">
		<div style="width: 60%; margin: auto; margin-top: 20vh; text-align: center; font-size: 2.0em; font-weight: bold;
			letter-spacing: <?php if ( $useChn ) { echo "20px";} else {echo "normal";};?>;">
			<?php echo xLate( 'h1Title' ); ?>
		</div>
<?php
	$txt = '';
	if ( $sessType == SESS_TYP_USR ) { 
		$txt = xLate( 'alertMsg');
	} else {
		if ( isset( $_SESSION[ 'icoName' ] ) ) {
			$txt = "幫助蓮友 '" . $_SESSION[ 'icoName' ] . "' 處理法會牌位"; 
		}
	}
?>
		<div style="width: 45%; margin: auto; margin-top: 5vh; text-align: center; font-size: 1.7em; font-weight: bold;
			color: blue; border: 8px solid #00b300; border-radius: 8px; padding: 2px 5px;
			display:<?php if (sizeof($txt)==0) { echo "none"; } else { echo "block"; }; ?>;">
			<?php echo $txt; ?>
		</div>
	</div>
</body>
</html>