<?php
/**********************************************************
 *          Sunday QiFu / HuiXiang Management Page        *
 *                admin/Sunday/sundayMgr.php              *
 **********************************************************/
 
	require_once( '../pgConstants.php' );
	require_once( 'dbSetup.php' );
	require_once( 'ChkTimeOut.php' );

	function xLate( $what ) {
		global $sessLang;
		$htmlNames = array (
			'htmlTitle' => array (
				SESS_LANG_CHN => "淨土念佛堂一般用戶主頁",
				SESS_LANG_ENG => "Pure Land Center User Portal" ),
			'UsrHome' => array (
				SESS_LANG_CHN => "回到<br/>用戶主頁",
				SESS_LANG_ENG => "Back to<br/>UsrPortal" ),
			'featPW' => array (
				SESS_LANG_CHN => "申請<br/>法會牌位",
				SESS_LANG_ENG => "Name Plaque for<br/>Retreat Merit Dedication" ),
			'featSun' => array (
				SESS_LANG_CHN => "早課<br/>祈福迴向",
				SESS_LANG_ENG => "Sunday Chanting<br/>Merit Dedication" ),
			'featFuture' => array (
				SESS_LANG_CHN => "其他未來會提供的功能<br/>(結緣法寶申請，等等。)",
				SESS_LANG_ENG => "Future:<br/>(Dharma Items Request; etc.)" ),
			'logOut' => array (
				SESS_LANG_CHN => "用戶<br/>撤出",
				SESS_LANG_ENG => "User<br/>Logout" ),
			'qifuTitle' => array (
				SESS_LANG_CHN => "週日早課申請<br/>祈福與功德迴向",
				SESS_LANG_ENG => "Sunday Chanting<br/>Well-wishing&nbsp;&amp;&nbsp;Merit Dedication" ),
			'ruleTab' => array (
				SESS_LANG_CHN => "申請要求與辦法",
				SESS_LANG_ENG => "Application Requirements &amp; Procedure" ),
			'qifuTab' => array (
				SESS_LANG_CHN => "祈福申請表",
				SESS_LANG_ENG => "Well-wishing Request Form" ),
			'meritTab' => array (
				SESS_LANG_CHN => "功德迴向申請表",
				SESS_LANG_ENG => "Merit Dedication Request Form" ),
			'gongDeZhuTab' => array (
				SESS_LANG_CHN => "申請做功德主",
                SESS_LANG_ENG => "Request to Serve as<br/>A Ceremony Sponsor" ),
            'setDueTime' => array (
                SESS_LANG_CHN => "設定祈福迴向申請截止時間",
                SESS_LANG_ENG => "" ),  // management function - Chinese only
            'dnldPrint' => array (
                SESS_LANG_CHN => "下載列印祈福迴向啟請資料",
                SESS_LANG_ENG => "" ),  // management function - Chinese only
            'forOthers' => array (
                SESS_LANG_CHN => "處理蓮友祈福迴向申請",
                SESS_LANG_ENG => "" ),  // management function - Chinese only
		);
		return $htmlNames[ $what ][ $sessLang ];
	} // function xLate();    

	$hdrLoc = "location: " . URL_ROOT . "/admin/index.php";
//	session_start(); // create or retrieve
	if ( !isset( $_SESSION[ 'usrName' ] ) ) {
		header( $hdrLoc );
	}
	$sessLang = $_SESSION[ 'sessLang' ];
	$useChn = ( $sessLang == SESS_LANG_CHN );
	$fontSize = ( $useChn ) ? "1.0em;" : "0.9em;";
	$ltrSpacing = ( $useChn ) ? "20px" : "normal";
?>

<!DOCTYPE html>
<html>
<head>
<title><?php echo xLate( 'htmlTitle' ); ?></title>
<meta http-equiv="content-type" content="text/html; charset=UTF-8">
<link rel="stylesheet" type="text/css" href="../master.css">
<link rel="stylesheet" type="text/css" href="../tabmenu-h.css">
<link rel="stylesheet" type="text/css" href="./sundayRules.css">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script src="../futureAlert.js"></script>
<script src="../AdmPortal/AdmCommon.js"></script>
<script src="./sundayMgr.js"></script>
<script type="text/javascript">
$(document).ready(function() {
    pgMenu_rdy();
    $(".tabMenu th").on( 'click', hdlr_tabClick_mgr );
    $(".tabMenu th.future").unbind().on( 'click', futureAlert );
    $("table.tabMenu th:first-child").trigger( 'click' );
})
</script>

<style type="text/css">
/* local customization */
	div.dataArea {
		height: 84vh;
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
		height: 70vh;
		margin: auto;
		margin-top: 0px;
		margin-bottom: 0px;		
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
/* for Admin Dialog box */
	table.dialog {
		width: 46%;
		margin: auto;
	}

	input, select {
		font-size: 1.1em;
	}

	input[type=text] {
		width: 80%;
	}

	select {
		width: 90%;
	}
	input[type=submit] {
		background-color: aqua;
		text-align: center;
		display: inline-block;
		height: 1.5em;
		border: 1px solid blue;
		border-radius: 3px;
	}

/* for dashboard */
	div.dataBodyWrapper { /* For loading tab data */		
		height: 55vh;
		overflow-y: auto;	
	}
	table.dataRows td[data-tblN]:hover {
	/*	color: no change; */
		background-color: #ffff80;
		cursor: pointer;
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
	<?php require_once("../AdmPortal/AdmPgHeader.htm");?>
	<table class="tabMenu">
		<thead>
			<tr>
				<th data-table="sundayParam"><?php echo xLate( 'setDueTime' ); ?></th>
				<th data-table="dnldPrint"><?php echo xLate( 'dnldPrint' ); ?></th>
				<th data-table="sundayDash"><?php echo xLate( 'forOthers' ); ?></th>
			</tr>
		</thead>
	</table>
	<div class="dataArea">
		<h2 class="dataTitle" style="letter-spacing: <?php echo $ltrSpacing; ?>;"><?php echo xLate( 'qifuTitle' ); ?></h2>
		<div id="tabDataFrame">
			<!-- Frame to load Tab Data -->
		</div><!-- tabDataFrame -->
	</div><!-- dataArea -->
</body>
</html>