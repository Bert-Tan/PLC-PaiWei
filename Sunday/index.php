<?php
/**********************************************************
 *               Sunday QiFu / HuiXiang Page              *
 *                  admin/Sunday/index.php                *
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
			'featSun' => array (
				SESS_LANG_CHN => "早課<br/>祈福回向",
				SESS_LANG_ENG => "Sunday Chanting<br/>Merit Dedication" ),
			'featFuture' => array (
				SESS_LANG_CHN => "其他未來會提供的功能",
				SESS_LANG_ENG => "Future<br/>Functions" ),
			'logOut' => array (
				SESS_LANG_CHN => "用戶<br/>撤出",
				SESS_LANG_ENG => "User<br/>Logout" ),
			'qifuTitle' => array (
				SESS_LANG_CHN => "週日早課<br/>申請祈福與回向",
				SESS_LANG_ENG => "Sunday Chanting<br/>Application for Well-wishing&nbsp;&amp;&nbsp;Merit Dedication" ),
			'ruleTab' => array (
				SESS_LANG_CHN => "申請要求與辦法",
				SESS_LANG_ENG => "Application Requirements" ),
			'qifuTab' => array (
				SESS_LANG_CHN => "祈福申請表",
				SESS_LANG_ENG => "Well-wishing Request Form" ),
			'meritTab' => array (
				SESS_LANG_CHN => "回向申請表",
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
			'present' => array (
				SESS_LANG_CHN => "**** 申請人務必親自、或有指定代表出席參加，否則恕不受理 ****",
				SESS_LANG_ENG => "**** The Requestor or a Delegate Must Be Present ****" ),
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
<link rel="stylesheet" type="text/css" href="./sundayRules.css">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script src="../futureAlert.js"></script>
<script src="../UsrPortal/UsrCommon.js"></script>
<script src="./Sunday.js"></script>
<script src="./sundayMgr.js"></script>
<script type="text/javascript">
$(document).ready(function() {
    $(".future").on( 'click', futureAlert );
    $("th[data-urlIdx]").on( 'click', function() {
        location.replace( _url2Go[ $(this).attr( "data-urlIdx" ) ]);
    });
    readSundayParam();
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
		height: 58px;
		line-height: 1.2em;
	}

	table.dataRows tr td:not(:last) {
		text-align: left;		
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

	input {
		font-size: 1.0em;
	}
	input[type=button] {
		font-size: 1.0em;
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
</style>

</head>
<body>
	<?php require_once("../UsrPortal/UsrPgHeader.php");?>
	<table class="tabMenu">
		<thead>
			<tr>
				<?php    if ( $sessType == SESS_TYP_USR ) {    ?>
				<th data-table="sundayRule"><?php echo xLate( 'ruleTab' ); ?></th>
				<?php    } else {    ?>
				<th data-table="sundayParam"><?php echo xLate( 'setDueTime' ); ?></th>
				<th data-table="dnldPrint"><?php echo xLate( 'dnldPrint' ); ?></th>
				<?php    }    ?>

				<th data-table="sundayQifu"><?php echo xLate( 'qifuTab' ); ?></th>
				<th data-table="sundayMerit"><?php echo xLate( 'meritTab' ); ?></th>
				<!-- <th class="future" data-table="sundayGongDeZhu"><?php echo xLate( 'gongDeZhuTab' ); ?></th> -->
			</tr>
		</thead>
	</table>
	<div class="dataArea">
		<h2 class="dataTitle" style="letter-spacing: <?php echo $ltrSpacing; ?>; margin-top: <?php echo $h2TopMargin; ?>;"><?php echo xLate( 'qifuTitle' ); ?></h2>
		<h2 style="color: darkred; margin-top: <?php echo $h2TopMargin; ?>;"><?php echo xLate( 'present' ); ?></h2>
		<div id="tabDataFrame">
			<!-- Frame to load Tab Data -->				
		</div><!-- tabDataFrame -->	
	</div><!-- dataArea -->
</body>
</html>