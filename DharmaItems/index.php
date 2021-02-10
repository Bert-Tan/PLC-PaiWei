<?php
/**********************************************************
 *                Dharma Items Request Page               *
 *               admin/DharmaItems/index.php              *
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
			'UsrHome' => array (
				SESS_LANG_CHN => "回到<br/>用戶主頁",
				SESS_LANG_ENG => "Back to<br/>UsrPortal" ),
			'featPW' => array (
				SESS_LANG_CHN => "申請<br/>法會牌位",
				SESS_LANG_ENG => "Name Plaque for<br/>Retreat Merit Dedication" ),
			'featDharmaItems' => array (
				SESS_LANG_CHN => "申請<br/>結緣法寶",
				SESS_LANG_ENG => "Request for<br/>Dharma Items" ),
			'featSun' => array (
				SESS_LANG_CHN => "申請早課<br/>祈福回向",
				SESS_LANG_ENG => "Sunday Chanting<br/>Merit Dedication" ),
			'featFuture' => array (
				SESS_LANG_CHN => "未來其他功能",
				SESS_LANG_ENG => "Future<br/>Functions" ),
			'logOut' => array (
				SESS_LANG_CHN => "用戶<br/>撤出",
				SESS_LANG_ENG => "User<br/>Logout" ),
			'dharmaItemsTitle' => array (
				SESS_LANG_CHN => "結緣法寶申請辦法",
				SESS_LANG_ENG => "Dharma Items Application Procedure" ),
			'dharmaItemsAlert' => array (
				SESS_LANG_CHN => "*** 請您仔細閱讀下列注意事項 ***",
				SESS_LANG_ENG => "*** Please carefully read this note before submitting an application ***" ),
			'dharmaItemsRuleTab' => array (
				SESS_LANG_CHN => "結緣法寶申請要求與辦法",
				SESS_LANG_ENG => "Dharma Item Application Requirements" ),
			'dharmaItemsReqTab' => array (
				SESS_LANG_CHN => "結緣法寶申請表",
				SESS_LANG_ENG => "Dharma Item Request Form" ),
			'addrInfoTab' => array (
				SESS_LANG_CHN => "結緣法寶寄送資料",
				SESS_LANG_ENG => "Dharma Item Shipping Information" )
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
	$fontSize = ( $useChn ) ? "1.0em;" : "0.9em;";
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
<link rel="stylesheet" type="text/css" href="./DharmaItemsRules.css">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script src="../futureAlert.js"></script>
<script src="../UsrPortal/UsrCommon.js"></script>
<script src="./DharmaItems.js"></script>
<!-- script src="./sundayMgr.js"></script -->
<script type="text/javascript">
$(document).ready(function() {
    $(".future").on( 'click', futureAlert );
    $("th[data-urlIdx]").on( 'click', function() {
        location.replace( _url2Go[ $(this).attr( "data-urlIdx" ) ]);
	});
	readDI_Param();
})
</script>
<style type="text/css">
/* local customization */
	table.pgMenu {
		table-layout: auto;
	}
	h2 {
		margin-bottom: 6px;
		text-align: center;
 		letter-spacing: 1px;
 		color: blue;
 		text-align: center;
	}
	div.dataArea {
		height: 84vh;
		margin-top: 0px;
		border: 2px solid green; /* same as the active tab color */
/*
    	box-sizing: border-box;
    	-moz-box-sizing: border-box;
		-webkit-box-sizing: border-box;
 */
	}

	table.dataRows {
		width: 85%;
		margin: auto;
		font-size: 1.2em;
	}

	table.dataRows th, table.dataRows td {
		height: 1.5em;
		line-height: 1.2em;
	}

	table.dataRows td {
		padding-left: 1vw;
		padding-right: 1vw;
		padding-bottom: 0.5vw;
		text-align: left;
	}
	
	table.dataRows th {
		color: white;
		background-color: #00b300;
		font-size: 1.25em;
		padding-top: 1vh;
		padding-bottom: 1vh;
	}
	
	table.dataRows tbody tr:last-child td {
		text-align: center;
		height: 2.0em;
	}
/* local only */
	div#tabDataFrame { /* For loading tab data */
		width: 99%;
		height: 67vh;
		margin: auto;
		margin-top: 0px;
		margin-bottom: 0px;
		overflow-y: auto;
	}

	table.dataRows input[type=button] {
		font-size: 1.0em;
		background-color: aqua;
		text-align: center;
		display: inline-block;
		border: 1px solid blue;
		border-radius: 4px;
	}
	table.dataRows input[type=text] {
		width: 100%;
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
</style>

</head>
<body>
	<?php require_once("../UsrPortal/UsrPgHeader.php");?>
	<table class="tabMenu">
		<thead>
			<tr>
			<?php    if ( $sessType == SESS_TYP_USR ) {    ?>
				<th data-table="dharmaItemsRules"><?php echo xLate( 'dharmaItemsRuleTab' ); ?></th>
			<?php    } else {    ?>
				<!-- Some Admin Functions for Dharma Items to be done here -->
			<?php    }    ?>
				<th data-table="addrInfoForm"><?php echo xLate( 'addrInfoTab' ); ?></th>
				<th data-table="dharmaItemsReqForm"><?php echo xLate( 'dharmaItemsReqTab' ); ?></th>
			</tr>
		</thead>
	</table>
	<div class="dataArea">
		<h2 class="dataTitle" id="dt" style="letter-spacing: <?php echo $ltrSpacing; ?>; margin-top: <?php echo $h2TopMargin; ?>;"><?php echo xLate( 'dharmaItemsTitle' ); ?></h2>
		<h2 id="dtAlert" style="color: darkred; margin-top: <?php echo $h2TopMargin; ?>;"><?php echo xLate( 'dharmaItemsAlert' ); ?></h2>
		<div id="tabDataFrame">
			<!-- Frame to load Tab Data -->				
		</div><!-- tabDataFrame -->	
	</div><!-- dataArea -->
</body>
</html>