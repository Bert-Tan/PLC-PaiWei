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
				SESS_LANG_ENG => "Please Select Name Plaque Type<br/>You Want to Apply for" ),
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
				SESS_LANG_ENG => "User Guide" )
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
	$dnldUrl = URL_ROOT . "/admin/PaiWei/dnldPaiWeiForm.php";
?>

<!DOCTYPE html>
<html>
<head>
<title><?php echo xLate( 'htmlTitle' ); ?></title>
<meta http-equiv="content-type" content="text/html; charset=UTF-8">
<link rel="stylesheet" type="text/css" href="https://www.amitabhalibrary.org/css/base.css">
<link rel="stylesheet" type="text/css" href="../css/admin.css">
<link rel="stylesheet" type="text/css" href="../css/menu.css">
<link rel="stylesheet" type="text/css" href="./PaiWei.css">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script src="../futureAlert.js"></script>
<script src="./PaiWei.js"></script>

<!-- The following CSS is for the upload Form which will be loaded from the other HTML/PHP file. -->
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
</style>

</head>
<body>
	<div class="hdrRibbon">
		<img src="https://www.amitabhalibrary.org/pic/PLC_logo_TR.png" alt="">
		<div id="pgTitle" class="centerMeV">
			<span style="letter-spacing: 1px;">淨土念佛堂牌法會牌位申請主頁</span><br/>
			<span class="engClass">Retreat Merit Dedication Application Page</span>
		</div>
		<table id="myMenuTbl" class="centerMeV">	
			<thead>
				<tr>
					<th class="pwTbl" data-tbl="W001A_4"><?php echo xLate( 'pwW' ); ?></th>
					<th class="pwTbl" data-tbl="L001A"><?php echo xLate( 'pwL' ); ?></th>
					<th class="pwTbl" data-tbl="Y001A"><?php echo xLate( 'pwY' ); ?></th>
					<th class="ugld"><?php echo xLate( 'pwUG' ); ?></th>
				</tr>
				<tr>
					<th class="pwTbl" data-tbl="DaPaiWei"><?php echo xLate( 'pwBIG' ); ?></th>
					<th class="pwTbl" data-tbl="C001A"><?php echo xLate( 'pwC' ); ?></th>
					<th class="pwTbl" data-tbl="D001A"><?php echo xLate( 'pwD' ); ?></th>
					<th id="upld"><?php echo xLate( 'pwUpld' ); ?></th>
				</tr>
			</thead>
		</table>
		<div id="pgLogOut" class="centerMeV"><a href="../Login/Logout.php" class="myLinkButton"><?php echo xLate('logOut');?></a></div>
	</div>
	<div class="dataArea">
		<h1 class="centerMe" id="myDataTitle" style="<?php if ( !$useChn ) echo "letter-spacing: normal;"; ?>"><?php echo xLate( 'h1Title' ); ?></h1>
	</div>
</body>
</html>