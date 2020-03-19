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
				SESS_LANG_CHN => "其他未來會提供的功能<br/>(結緣法寶申請，等等。)",
				SESS_LANG_ENG => "Future:<br/>(Dharma Items Request; etc.)" ),			
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
			'pwUpld' => array (
				SESS_LANG_CHN => "上載牌位檔案",
				SESS_LANG_ENG => "Upload CSV Files" ),
			'pwUG' => array (
				SESS_LANG_CHN => "用戶指南",
				SESS_LANG_ENG => "User Guide" ),
			'alertMsg' => array (
				SESS_LANG_CHN => "**** 除有特殊困難，牌位申請者須本人親自( 或由指定代表 ) 前來參加法會 ****",
				SESS_LANG_ENG => "**** Note: You or your designee shall be present in the retreat unless you have difficulties ****" )
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
	$fontSize = ( $useChn ) ? "1.0em" : "0.9em";
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
<script src="../futureAlert.js"></script>
<script src="../UsrPortal/UsrCommon.js"></script>
<script src="./PaiWei.js"></script>

<!-- The following CSS is for the upload Form which will be loaded from the other HTML/PHP file. -->
<style type="text/css">
/* local customization */
	h2 {
   		margin-top: 0px;
   		text-align: center;
    	letter-spacing: 1px;
    	color: blue;
    	text-align: center;
	}
	table.pgMenu {
		table-layout: auto;
	}
	div.dataArea {
		height: 82vh;
		margin-top: 0px;
		border: 2px solid green; /* same as the active tab color */
    	box-sizing: border-box;
    	-moz-box-sizing: border-box;
    	-webkit-box-sizing: border-box;
	}
	table.dataHdr, table.dataRows {
		table-layout: auto;
	}
/*
	table.dataHdr th:last-child, table.dataRows td:last-child {
		padding-left: 1px;
		padding-right: 1px;
	}
 */
	table.dataHdr th, table.dataRows td {
		height: 22px;
		line-height: 1.2em;
	}

	table.dataRows tr td:not(:last) {
		text-align: left;		
	}
/* local only */
	div#tabDataFrame { /* For loading tab data */
		width: 98%;
		height: 75vh;
		margin: auto;
		margin-top: 0px;
		margin-bottom: 0px;
		overflow-y: auto;
	}

	input {
		font-size: 1.0em;
	}
	input[type=button] {
		font-size: 0.8em;
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
				<?php
					if ( $sessType == SESS_TYP_USR ) {
				?>
				<th class="ugld"><?php echo xLate( 'pwUG' ); ?></th>
				<?php
					} else {
				?>
				<th id="dnld" data-urlIdx="urlDnld">下載牌位列印</th>
				<?php
					}
				?>

				<th class="pwTbl" data-tbl="C001A"><?php echo xLate( 'pwC' ); ?></th>
				<th class="pwTbl" data-tbl="W001A_4"><?php echo xLate( 'pwW' ); ?></th>
				<th class="pwTbl" data-tbl="DaPaiWei"><?php echo xLate( 'pwBIG' ); ?></th>
				<th class="pwTbl" data-tbl="L001A"><?php echo xLate( 'pwL' ); ?></th>
				<th class="pwTbl" data-tbl="Y001A"><?php echo xLate( 'pwY' ); ?></th>	
				<th class="pwTbl" data-tbl="D001A"><?php echo xLate( 'pwD' ); ?></th>
				<th id="upld"><?php echo xLate( 'pwUpld' ); ?></th>
			</tr>
		</thead>
	</table>
	<div class="dataArea">		
		<h2 style="color: darkred; margin-top: 5px; margin-bottom: 10px;"><?php echo xLate( 'alertMsg' ); ?></h2>
		<div id="tabDataFrame">
			<!-- Frame to load Tab Data -->				
		</div><!-- tabDataFrame -->	
	</div><!-- dataArea -->	
</body>
</html>


