<?php
/**********************************************************
 *           User Pai Wei User Guide - Chinese            *
 **********************************************************/
/*
 * To fit into the PaiWei SW architecture, the '#ugDesc' <div> will be loaded by the PaiWei.js
 */ 
	require_once( '../pgConstants.php' );
	require_once( 'dbSetup.php' );
	require_once( 'ChkTimeOut.php' );

	function xLate( $what ) {
		global $sessLang;
		$htmlNames = array (
			'htmlTitle' => array (
				SESS_LANG_CHN => "淨土念佛堂法會牌位申請主頁",
				SESS_LANG_ENG => "Retreat Merit Dedication Application Page" )
		);
		return $htmlNames[ $what ][ $sessLang ];
	} // xLate()

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
	} // redirect
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

</head>
<body>
	<div class="dataArea" style="overflow-y: auto;">
		<div id="ugDesc"><!-- BEGIN for loading into the DataArea in PaiWei.php -->
<?php
	if ( $useChn ) { // Chinese version
 ?>
		<h2 style="margin-top: 4vh; text-align: center; letter-spacing: 1px;">
			法會牌位申請用戶指南 (*** 還未定稿 ***)
		</h2>
        <span style="display: block; width: 95%; margin: auto; padding-top: 2vh; font-size: 1.3em;
			font-weight: bold; line-height: 1.4em;"><!-- Intro phrase -->
            用戶將可經由此法會牌位申請主頁選擇所要申請設立的牌位：祈福消災、往生蓮位、歷代祖先、地基主蓮位、
            累劫冤親債主連位、牌位上載 等。
        </span>
        <div style="width: 95%; margin:auto; font-size: 1.3em;"><!-- Step-by-step Guide -->
            <ol>
                <li>
					當您在用戶功能選項的主頁點擊『法會牌位申請』之後，您即可看到法會牌位申請的主頁如下：<br/><br/>
					<img src="./img/pwMain.png" width="85%" alt="" style="border: 1px solid black;">
				</li>
            </ol>
        </div><!-- END Step-by-step Guide -->
<?php
	} else { // English version
?>
		<h2 style="margin-top: 4vh; text-align: center; letter-spacing: normal;">
			User Guide to Request for Merit Dedication Name Plaques during Retreats (*** In Prog. ***) 
		</h2>
        <span style="display: block; width: 95%; margin: auto; padding-top: 2vh; font-size: 1.3em;
			font-weight: bold; line-height: 1.1em;"><!-- Intro phrase -->
            Users can apply for specific Merit Dedication Name Plaques during Retreats from this page:
			well-blessing, deceased and recently deceased beloved ones, ancestors, site guardians,
			karmic creditors, etc. You can also upload name plaque data.
        </span>
        <div style="width: 95%; margin:auto; font-size: 1.3em;"><!-- BEGIN Step-by-step Guide -->
            <ol>
                <li>
					When you click "Name Plaque Application for Merit Dedication in Retreats",
					you will see the "Retreat Merit Dedication Request Page" with selections as below：<br/><br/>
					<img src="./img/epwMain.png" width="85%" alt="Image Missing" style="border: 1px solid black;">
				</li>
            </ol>
        </div><!-- END Step-by-step Guide -->
		</div><!-- END for loading into the DataArea in PaiWei.php -->
<?php
	} // English version
?>
    </div><!-- DataArea -->
</body>
</html>