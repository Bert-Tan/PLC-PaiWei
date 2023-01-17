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
			'WebsiteHome' => array (
				SESS_LANG_CHN => "回到<br/>網站首頁",
				SESS_LANG_ENG => "Back to<br/>Homepage" ),
			'htmlTitle' => array (
				SESS_LANG_CHN => "淨土念佛堂一般用戶主頁",
				SESS_LANG_ENG => "Pure Land Center User Portal" ),
			'logOut' => array (
				SESS_LANG_CHN => "用戶<br/>撤出",
				SESS_LANG_ENG => "User<br/>Logout" ),
			'UsrHome' => array (
				SESS_LANG_CHN => "回到<br/>用戶主頁",
				SESS_LANG_ENG => "Back to<br/>UsrPortal" ),
			'featPW' => array (
				SESS_LANG_CHN => "申請<br/>法會牌位",
				SESS_LANG_ENG => "Name Plaque for<br/>Retreat Merit Dedication" ),
			'featSun' => array (
				SESS_LANG_CHN => "早課<br/>祈福回向",
				SESS_LANG_ENG => "Sunday Chanting<br/>Merit Dedication" ),
			'featFuture' => array (
				SESS_LANG_CHN => "其他未來會提供的功能",
				SESS_LANG_ENG => "Future<br/>Functions" ),			
			'paiweiTitle' => array (
				SESS_LANG_CHN => "法會牌位申請",
				SESS_LANG_ENG => "Application for Merit Dedication Name Plaques during Retreats" ),
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
				SESS_LANG_CHN => "累劫冤親<br>債主蓮位",
				SESS_LANG_ENG => "Karmic Creditors" ),
			'pwBIG' => array (
				SESS_LANG_CHN => "(一年內)<br>往生者蓮位",
				SESS_LANG_ENG => "Recently Deceased" ),
			'pwBIGRED' => array (
				SESS_LANG_CHN => "紅色大牌位",
				SESS_LANG_ENG => "RED DaPaiWei" ),
			'pwUpld' => array (
				SESS_LANG_CHN => "上載牌位檔案",
				SESS_LANG_ENG => "Upload CSV Files" ),
			'pwDnld' => array (
				SESS_LANG_CHN => "下載牌位檔案",
				SESS_LANG_ENG => "Download Name Plaque Data" ),
			'pwUG' => array (
				SESS_LANG_CHN => "用戶指南",
				SESS_LANG_ENG => "User Guide" ),
			'setRtData' => array (
				SESS_LANG_CHN => "更新法會資料",
				SESS_LANG_ENG => "" ),
			'DnldJiWen' => array (
				SESS_LANG_CHN => "列印祭文疏文",
				SESS_LANG_ENG => "" ),
			'DnldPaiWei' => array (
				SESS_LANG_CHN => "下載牌位列印",
				SESS_LANG_ENG => "" ),
			'alertMsg' => array (
				SESS_LANG_CHN => "**** 除有特殊困難，牌位申請者須本人親自( 或由指定代表 ) 前來參加法會 ****",
				SESS_LANG_ENG => "**** You or your designee shall be present in the retreat unless you have difficulties ****" )
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
	$icoName = isset($_SESSION[ 'icoName' ]) ? $_SESSION[ 'icoName' ] : null;
	$fontSize = ( $useChn ) ? "1.0em" : "0.9em";
	$ltrSpacing = ( $useChn ) ? "20px" : "normal";
	$h2TopMargin = ( $useChn ) ? "4px" : "6px";
	unset( $_SESSION[ 'byPass' ] );
?>

<!DOCTYPE html>
<html>
<head>
<title><?php echo xLate( 'htmlTitle' ); ?></title>
<meta http-equiv="content-type" content="text/html; charset=UTF-8">
<link rel="stylesheet" type="text/css" href="../master.css">
<link rel="stylesheet" type="text/css" href="../tabmenu-h.css">
<link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<link rel="stylesheet" type="text/css" href="./toolTip.css">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script type="text/javascript" src="../futureAlert.js"></script>
<script type="text/javascript" src="../UsrPortal/UsrCommon.js"></script>
<script type="text/javascript" src="./PaiWei.js"></script>
<script type="text/javascript" src="./paiweiMgr.js"></script>
<script type="text/javascript" src="./dnldPaiWei.js"></script>
<script type="text/javascript">
$(document).ready(function() {	
	$(".future").on( 'click', futureAlert );
	$(".soon").on( 'click', soonAlert );
	$("th[data-urlIdx]").on('click', function() {
		location.replace( _url2Go[$(this).attr("data-urlIdx")] );
	});

	$(".tabMenu th").on( 'click', hdlr_tabClick );
	$(".tabMenu th[data-tbl=Y001A]").off( 'click' );
	$(".tabMenu th.future").unbind().on( 'click', futureAlert );

	readSessParam(); //active coorresponding tab in readSessParam()
})
</script>
<style type="text/css">
/* localization */
.engClass {
	font-size: 0.7em;
}
table.pgMenu {
		table-layout: auto;
}
table.pgMenu tr th:last {
	border-left: 1px solid white;
}
table.pgMenu th[data-urlIdx=usrLogout] {
	border-left: 1px solid white;
}
table.pgMenu th[data-urlIdx=urlUsrHome] {
	font-size: <?php echo $fontSize; ?>;
}

h2 {
	margin-bottom: 6px;
	text-align: center;
 	letter-spacing: 1px;
 	color: blue;
 	text-align: center;
}
div.dataArea {
	height: 82vh;
 	margin-top: 0px;
 	border: 2px solid green; /* same as the active tab color */
 	box-sizing: border-box;
 	-moz-box-sizing: border-box;
	-webkit-box-sizing: border-box;
	overflow-y: auto;
}
div#tabDataFrame { /* For loading tab data */		
	overflow-y: auto;
	height: 69vh;
	width: 99%;
	margin: auto;
	margin-top: 0px;
	margin-bottom: 0px;
}

table.dialog {
	width: 60%;
}
table.dialog td {
 	height: 6vh;
	font-size: 1.1em;
 	vertical-align: middle;
 	text-align: center;
}
table.dialog input, select {
 	font-size: 1.1em;
}
table.dialog input[type=text] {
 	width: 80%;
 	margin: auto;
}
table.dialog input[type=submit] {
 	background-color: aqua;
 	text-align: center;
 	display: inline-block;
 	height: 1.5em;
 	border: 1px solid blue;
 	border-radius: 3px;
 	font-size: 1.2em;
}
table.dialog input[type=button] {
 	background-color: aqua;
 	text-align: center;
 	display: inline-block;
 	height: 1.5em;
 	border: 1px solid blue;
 	border-radius: 3px;
 	font-size: 1.2em;
}

/* localization for loaded PaiWei Upload Form */
#upldForm td
{
	padding-top: 2px;
	padding-left: 2vw;
	text-align: left;
	vertical-align: top;
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
	width: 14%;
}
table.dataHdr th, table.dataRows td {
	height: 22px;
	line-height: 1.2em;
}
table.dataRows tr td:not(:last) {
	text-align: left;
}
table.dataRows tr:nth-child(odd) input[type=text] {
	background-color: #ffffe6;
}
table.dataRows tr:nth-child(even) input[type=text] {
	background-color: #ffffcc;
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
</style>
</head>
<body>
	<?php require_once("../UsrPortal/UsrPgHeader.php");?>
	<table class="tabMenu">
		<thead>
			<tr>
				<?php    if ( $sessType == SESS_TYP_USR ) {    ?>
				<th data-tbl="ug"><?php echo xLate( 'pwUG' ); ?></th>
				<?php    } else {    ?>
				<th data-tbl="RtData"><?php echo xLate( 'setRtData' ); ?></th>
				<th data-tbl="DnldJiWen"><?php echo xLate( 'DnldJiWen' ); ?></th>
				<th data-tbl="DnldPaiWei"><?php echo xLate( 'DnldPaiWei' ); ?></th>
				<?php    }    ?>

				<th data-tbl="C001A"><?php echo xLate( 'pwC' ); ?></th>
				<th data-tbl="W001A_4"><?php echo xLate( 'pwW' ); ?></th>
				<th data-tbl="DaPaiWei"><?php echo xLate( 'pwBIG' ); ?></th>
				<th data-tbl="L001A"><?php echo xLate( 'pwL' ); ?></th>
				<th data-tbl="Y001A" style="pointer-events:none; color:gray"><?php echo xLate( 'pwY' ); ?></th>
				<th data-tbl="D001A"><?php echo xLate( 'pwD' ); ?></th>
				<?php if ($sessType != SESS_TYP_USR && $icoName == 'PLC') { ?>
				<th data-tbl="DaPaiWeiRed"><?php echo xLate( 'pwBIGRED' ); ?>
				<?php } ?>
				<th data-tbl="upld"><?php echo xLate( 'pwUpld' ); ?></th>
				<?php if ($sessType == SESS_TYP_USR) { ?>
				<th data-tbl='DnldPaiWei'><?php echo xLate( 'pwDnld' ); ?></th>
				<?php } ?>				
			</tr>
		</thead>
	</table>
	<div class="dataArea">
		<h2 class="dataTitle" style="letter-spacing: <?php echo $ltrSpacing; ?>; margin-top: <?php echo $h2TopMargin; ?>;"><?php echo xLate( 'paiweiTitle' ); ?></h2>		
		<h2 style="color: darkred; margin-top: <?php echo $h2TopMargin; ?>;"><?php echo xLate( 'alertMsg' ); ?></h2>
		<div id="tabDataFrame">
			<!-- Frame to load Tab Data -->				
		</div><!-- tabDataFrame -->	
	</div><!-- dataArea -->	
</body>
</html>


