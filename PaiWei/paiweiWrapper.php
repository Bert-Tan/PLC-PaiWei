<?php
/**********************************************************
 *                   PaiWei Wrapper File                  *
 *            When user clicks at the front-page          *
 **********************************************************/
 
	require_once( '../pgConstants.php' );

	function xLate( $what ) {
		global $sessLang;
		$htmlNames = array (
			'htmlTitle' => array (
				SESS_LANG_CHN => "淨土念佛堂一般用戶主頁",
				SESS_LANG_ENG => "Pure Land Center User Portal" ),
			'WebsiteHome' => array (
				SESS_LANG_CHN => "回到<br/>網站首頁",
				SESS_LANG_ENG => "Back to<br/>Homepage" ),					
			'login' => array (
				SESS_LANG_CHN => "登錄申請<br/>法會牌位",
				SESS_LANG_ENG => "Login to<br/>Request" ),			
			'paiweiTitle' => array (
				SESS_LANG_CHN => "法會牌位申請<br/>用戶指南",
				SESS_LANG_ENG => "Application for Merit Dedication Name Plaques during Retreats<br/>User Guide" ),
			'present' => array (
				SESS_LANG_CHN => "**** 除有特殊困難，牌位申請者須本人親自( 或由指定代表 ) 前來參加法會 ****",
				SESS_LANG_ENG => "**** You or your designee shall be present in the retreat unless you have difficulties ****" )
		);
		return $htmlNames[ $what ][ $sessLang ];
	} // function xLate();

	session_start();
	$_SESSION[ 'sessLang' ] = ( (isset($_GET[ 'l' ])) && ($_GET[ 'l' ] == 'e') ) ? SESS_LANG_ENG : SESS_LANG_CHN;
	$sessLang = $_SESSION[ 'sessLang' ];
	$sessType = SESS_TYP_USR;
	$useChn = ( $sessLang == SESS_LANG_CHN );
	$fontSize = ( $useChn ) ? "1.0em;" : "0.9em;";
	$ltrSpacing = ( $useChn ) ? "20px" : "normal";
	$_SESSION[ 'byPass' ] = true;

	$loginURL = URL_ROOT . "/admin/Login/Login.php?l=" . ( ($sessLang == SESS_LANG_CHN) ? "c" : "e" );
?>

<!DOCTYPE html>
<html>
<head>
<title><?php echo xLate( 'htmlTitle' ); ?></title>
<meta http-equiv="content-type" content="text/html; charset=UTF-8">
<link rel="stylesheet" type="text/css" href="../master.css">
<link rel="stylesheet" type="text/css" href="../tabmenu-h.css">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script src="../UsrPortal/UsrCommon.js"></script>
<script type="text/javascript">
var _url2Go = {
    'urlWebsiteHome' : "https://www.amitabhalibrary.org/",
    'urlLogin' : "<?php echo $loginURL; ?>"
}
$(document).ready(function() {
    $(".float-button").on( 'click', function() {
		location.replace( _url2Go[ $(this).attr( "urlIdx" ) ]);
	});	
	$("#tabDataFrame").load("./UG.php #ugDesc");
})
</script>
<style type="text/css">
h2 {
    text-align: center;
    letter-spacing: 1px;
    color: blue;
    text-align: center;
}
/* local customization */
	div.dataArea {
		height: 98vh; /* 84vh;*/
		margin-top: 0px;
		border: 2px solid green; /* same as the active tab color */
    	box-sizing: border-box;
    	-moz-box-sizing: border-box;
    	-webkit-box-sizing: border-box;
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
	div.float-button {		
		margin-top: 20px;
		overflow: auto;  		
		background-color: #00b300;
		width: 12vh;
		border: 1px solid white;
		padding: 2px 5px;
		text-align: center;    
		color: white; /* text color */	
		font-weight: bold;		
	}
	div.float-button:hover {		
		background-color: #009900;  
		border: 1px solid green;
		cursor: pointer; 
		color: yellow;	
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
</style>

</head>
<body>
	<div class="dataArea">		
		<div class="float-button" style="float: left; margin-left: 30px;" urlIdx="urlWebsiteHome">
			<?php echo xLate( 'WebsiteHome' ); ?>
		</div>	
		<div class="float-button" style="float: right; margin-right: 30px;" urlIdx="urlLogin">  
			<?php echo xLate( 'login' ); ?>
		</div>
	<h2 class="dataTitle" style="letter-spacing: <?php echo $ltrSpacing; ?>;">
			<?php echo xLate( 'paiweiTitle' ); ?>
		</h2>
		<h2 style="color: darkred;"><?php echo xLate( 'present' ); ?></h2>
		<div id="tabDataFrame">
			<!-- Frame to load Tab Data -->				
		</div><!-- tabDataFrame -->	
	</div><!-- dataArea -->
</body>
</html>