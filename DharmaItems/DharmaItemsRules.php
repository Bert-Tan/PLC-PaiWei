<?php
/**********************************************************
 *    Sunday Qifu & Merit Dedication Request Rules        *
 **********************************************************/

	require_once( '../pgConstants.php' );
	require_once( 'dbSetup.php' );
	require_once( 'ChkTimeOut.php' );

	function xLate( $what ) {
		global $sessLang;
		$htmlNames = array (
			'htmlTitle' => array (
				SESS_LANG_CHN => "淨土念佛堂結緣法寶申請主頁",
				SESS_LANG_ENG => "Dharma Items Application Page" )
		);
		return $htmlNames[ $what ][ $sessLang ];
	} // xLate()

	$sessLang = SESS_LANG_CHN; // default
	if ( isset( $_SESSION[ 'sessLang' ] ) ) {
		$sessLang = $_SESSION[ 'sessLang' ];
	}	
	$_SESSION[ 'sessLang' ] = $sessLang;

	$hdrURL = URL_ROOT . "/admin/index.php";
	$useChn = ( $sessLang == SESS_LANG_CHN );

	if ( (! isset( $_SESSION[ 'byPass' ] )) || $_SESSION[ 'byPass' ] == false) {
		if ( !isset( $_SESSION[ 'usrName' ] ) ) {
			header( "location: " . $hdrURL );
		} // redirect
	}
?>

<!DOCTYPE html>
<html>
<head>
<title><?php echo xLate( 'htmlTitle' ); ?></title>
<meta http-equiv="content-type" content="text/html; charset=UTF-8">
<link rel="stylesheet" type="text/css" href="../master.css">
<link rel="stylesheet" type="text/css" href="./sundayRules.css">
</head>
<body>
	<div class="dataArea" style="overflow-y: auto;">
		<div id="ruleText"><!-- BEGIN for loading into the #tabDataFrame in sunday/index.php -->
<?php
	if ( $useChn ) { // Chinese version
?>
		<?php if ( (! isset( $_SESSION[ 'byPass' ] )) || $_SESSION[ 'byPass' ] == false) { ?>
		<!-- h2>結緣法寶申請辦法</h2 -->
		<?php } ?>
		<div class="dharmaItemsRuleTxt">
			中文規則在此陳述
		</div>
   
<?php
	} else { // English version
?>
		<?php if ( (! isset( $_SESSION[ 'byPass' ] )) || $_SESSION[ 'byPass' ] == false) { ?>
		<!-- h2 style="margin-top: 0px; text-align: center; letter-spacing: normal; color: blue;">
			Dharma Items Application Procedures
		</h2 -->
		<?php } ?>
		<div class="dharmaItemsRuleTxt">
			English Rules text comes here
		</div>
    
<?php
	} // English version
?>
        </div><!-- END 'ruleText' for loading into the #tabDataFrame in DharmaItems/index.php -->
    </div><!-- DataArea -->
</body>
</html>