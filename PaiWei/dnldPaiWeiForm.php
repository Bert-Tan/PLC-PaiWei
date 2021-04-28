<?php
/**********************************************************
 *			User Pai Wei Download Main Page - Chinese			      *
 **********************************************************/
 
	require_once( '../pgConstants.php' );
	require_once( 'dbSetup.php' );
	require_once("PaiWei_DBfuncs.php");

	function xLate( $what ) {
		global $sessLang;
		$htmlNames = array (
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
			'frameTitle' => array (
				SESS_LANG_CHN => "下載牌位資料檔案",
				SESS_LANG_ENG => "Download Name Plaque Data" ),
			'user' => array (
				SESS_LANG_CHN => "申請人",
				SESS_LANG_ENG => "User" ),
			'pwType' => array (
				SESS_LANG_CHN => "請選擇牌位",
				SESS_LANG_ENG => "Select Name Plaque Type" ),
			'btn' => array (
				SESS_LANG_CHN => "下 載",
				SESS_LANG_ENG => "Download" )
		);
		return $htmlNames[ $what ][ $sessLang ];
	} // function xLate();
	
	session_start(); // create or retrieve

	if ( !isset( $_SESSION[ 'usrName' ] ) ) {
		header( "location: " . '../Login/Login.php' );
		exit;
	}
	$sessLang = $_SESSION[ 'sessLang' ];
	$sessType = $_SESSION[ 'sessType' ];
	$useChn = ( $sessLang == SESS_LANG_CHN );
	$ltrSpacing = ( $useChn ) ? "20px" : "normal";
?>

<!DOCTYPE html>
<HTML>
<HEAD>
<META HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=UTF-8">
<link rel="stylesheet" type="text/css" href="../master.css">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
</script>
</HEAD>
<BODY>
	<div class="dataArea"><!-- style="width: 60%; margin: auto; border: 2px solid #00b300;" -->
		<div id="forDnld"><!-- for download into the main PaiWei Admin Page -->
		<h2 style="margin-top: 4vh; text-align: center; letter-spacing: <?php echo $ltrSpacing; ?>;">
		<?php echo xLate( 'frameTitle' ); ?>
		</h2>
		<form action="dnldPaiWeiPDF" method="post" enctype="multipart/form-data" id="dnldForm"
			style="font-weight:bold; padding: 10px;">
			<table class="dialog">
				<thead><th><?php echo xLate( 'user' ); ?></th><th><?php echo xLate( 'pwType' ); ?></th></thead>
				<tr><!-- Selection Row -->
			    <td>
						<?php 
							if ($sessType == SESS_TYP_USR)
								echo $_SESSION[ 'usrName' ];
							else						
								echo userSelectionList();
						?>
			    </td>
		    	<td>
						<select name="dbTblName" style="font-size: 1.2em;" required>
						<option value="">-- <?php echo xLate( 'pwType' ); ?> --</option>
					  	<option value="C001A"><?php echo xLate( 'pwC' ); ?></option>
						<option value="W001A_4"><?php echo xLate( 'pwW' ); ?></option>
					    <option value="DaPaiWei"><?php echo xLate( 'pwBIG' ); ?></option>
					    <option value="L001A"><?php echo xLate( 'pwL' ); ?></option>
					    <option value="Y001A"><?php echo xLate( 'pwY' ); ?></option>
					    <option value="D001A"><?php echo xLate( 'pwD' ); ?></option>
				  	</select>
			    </td>
			  </tr>
			  <tr><!-- Submit Row -->
			    <td colspan="2" style="text-align: center; vertical-align: middle; padding: 1vh 0px;">
					<input class="dnldCSVBtn" type="button" value="<?php echo xLate( 'btn' ); if ($sessType != SESS_TYP_USR) echo " CSV"; ?>">	
					<?php 
						if ($sessType != SESS_TYP_USR)
							echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input class='dnldPDFBtn' type='submit' value='" . xLate( 'btn' ) . " PDF" . "'>";	
					?>				
			    </td>
		  	</tr>
		  </table>
		</form>
		</div>
	</div>
</BODY>
<HTML>