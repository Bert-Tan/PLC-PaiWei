<?php
/**********************************************************
 *        		  PaiWei Management Page		          *
 **********************************************************/
 
	require_once( '../pgConstants.php' );
	require_once( 'dbSetup.php' );
	require_once( 'ChkTimeOut.php' );

	function xLate( $what ) {
		global $sessLang;
		$htmlNames = array (
			'htmlTitle' => array (
				SESS_LANG_CHN => "淨土念佛堂管理用戶主頁",
				SESS_LANG_ENG => "" ),
			'paiweiTitle' => array (
				SESS_LANG_CHN => "法會牌位申請",
				SESS_LANG_ENG => "Application for Merit Dedication Name Plaques during Retreats" ),
			'setRtData' => array (
				SESS_LANG_CHN => "更新法會資料",
				SESS_LANG_ENG => "" ),
			'DnldJiWen' => array (
				SESS_LANG_CHN => "列印祭文與疏文",
				SESS_LANG_ENG => "" ),
			'DnldPaiWei' => array (
				SESS_LANG_CHN => "下載牌位列印",
				SESS_LANG_ENG => "" ),
			'PaiWeiDash' => array (
				SESS_LANG_CHN => "處理蓮友牌位",
				SESS_LANG_ENG => "" ),			
			'alertMsg' => array (
				SESS_LANG_CHN => "**** 除有特殊困難，牌位申請者須本人親自( 或由指定代表 ) 前來參加法會 ****",
				SESS_LANG_ENG => "**** You or your designee shall be present in the retreat unless you have difficulties ****" )
		);
		return $htmlNames[ $what ][ $sessLang ];
	} // function xLate();

	//	session_start(); // create or retrieve (already called in ChkTimeOut.php )
	$hdrLoc = "location: " . URL_ROOT . "/admin/index.php";
	if ( !isset( $_SESSION[ 'usrName' ] ) ) {
		header( $hdrLoc );
	}

	$sessLang = $_SESSION[ 'sessLang' ];
	$sessType = $_SESSION[ 'sessType' ];
	$useChn = ( $sessLang == SESS_LANG_CHN );
	$ltrSpacing = ( $useChn ) ? "20px" : "normal";	
?>

<!DOCTYPE html>
<html>
<head>
<title><?php echo xLate( 'htmlTitle' ); ?></title>
<meta http-equiv="content-type" content="text/html; charset=UTF-8">
<link rel="stylesheet" type="text/css" href="../master.css">
<link rel="stylesheet" type="text/css" href="../tabmenu-h.css">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script type="text/javascript" src="../futureAlert.js"></script>
<script type="text/javascript" src="../AdmPortal/AdmCommon.js"></script>
<script type="text/javascript" src="./paiweiMgr.js"></script>
<script type="text/javascript">
$(document).ready(function() {
	pgMenu_rdy(); // ../AdmPortal/AdmCommon.js file, javascript for header menu	
	$(".tabMenu th").on( 'click', hdlr_tabClick_mgr );
	$(".tabMenu th.future").unbind().on( 'click', futureAlert );
	$("table.tabMenu th:first-child").trigger( 'click' );
})
</script>
<style type="text/css">
/* local customization */
h2 {
	margin-top: 0px;
	text-align: center;
 	letter-spacing: 1px;
 	color: blue;
 	text-align: center;
}
div.dataArea {
	height: 84vh;
 	margin-top: 0px;
 	border: 2px solid green; /* same as the active tab color */
 	box-sizing: border-box;
 	-moz-box-sizing: border-box;
	-webkit-box-sizing: border-box;
}
div#tabDataFrame { /* For loading tab data */
	width: 100%;
	height: 68vh;
 	margin: auto;
 	margin-top: 0px;
 	margin-bottom: 0px;
}
input {
 	font-size: 1.0em;
}

input[type=text] {
 	width: 100%;
 	box-sizing: border-box;
	-moz-box-sizing: border-box;
	-webkit-box-sizing: border-box;
}

/* for Dialog box */
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

/* for PaiWei dashboard */
div.dataBodyWrapper {
 	height: 56vh;
 	overflow-y: auto;
}
table.dataHdr th, table.dataRows td {
	height: 22px;
	line-height: 1.2em;
}
table.dataRows tr td:not(:last) {
	text-align: left;		
}

table.dataRows td[data-tblN]:hover {
/*	color: no change; */
 	background-color: #ffff80;
 	cursor: pointer;
}
table.dataRows tr:last-child td { /* the Summary Row */
 	color: yellow;
 	background-color: #00b300;
 	font-weight: bold;
}
table.dataHdr th input {
 	font-size: 1.0em;
 	background-color: aqua;
 	border: 1px solid blue;
}
table.dataHdr th input[type=button] {
 	margin-top: 3px;
 	display: inline-block;
 	float: right;
 	border: 1px solid blue;
 	border-radius: 6px;
}
table.dataHdr th select {
 	width: 70%;
 	background-color: aqua;
 	border: 1px solid blue;
}
</style>
</head>
<body>
	<?php require_once("../AdmPortal/AdmPgHeader.htm"); ?>
	<table class="tabMenu">
		<thead>
			<tr>
				<th data-table="RtData"><?php echo xLate( 'setRtData' ); ?></th>
				<th data-table="DnldJiWen"><?php echo xLate( 'DnldJiWen' ); ?></th>
				<th data-table="DnldPaiWei"><?php echo xLate( 'DnldPaiWei' ); ?></th>
				<th data-table="PaiWeiDash"><?php echo xLate( 'PaiWeiDash' ); ?></th>
			</tr>
		</thead>
	</table>
	<div class="dataArea">
		<h2 class="dataTitle" style="letter-spacing: <?php echo $ltrSpacing; ?>;"><?php echo xLate( 'paiweiTitle' ); ?></h2>			
		<div id="tabDataFrame">
			<!-- Frame to load Tab Data -->
		</div><!-- tabDataFrame -->
	</div><!-- dataArea -->

	
</body>
</html>