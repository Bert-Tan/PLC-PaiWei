<?php
	require_once( '../pgConstants.php' );
	require_once( 'dbSetup.php' );
	require_once( 'Login_Funcs.php' );
	require_once( 'plcMailerSetup.php' );

	function xLate ( $what ) {
		global $sessLang;
		$htmlNames = array(
			'htmlTitle' => array (
				SESS_LANG_CHN => "淨土念佛堂用戶重新設立密碼主頁",
				SESS_LANG_ENG => "Pure Land Center User Password Reset" ),
			'featPW' => array (
				SESS_LANG_CHN => "淨土念佛堂<br/>用戶主頁",
				SESS_LANG_ENG => "Pure Land Center<br/>User Portal" ),
			'featFuture' => array (
				SESS_LANG_CHN => "其他未來會提供的功能",
				SESS_LANG_ENG => "Future<br>Functions" ),
			'logOut' => array (
				SESS_LANG_CHN => "用戶<br/>撤出",
				SESS_LANG_ENG => "User<br/>Logout" ),
			'h2Title' => array (
				SESS_LANG_CHN => "用戶重新設立密碼",
				SESS_LANG_ENG => "Reset User Password" ),
			'uName' => array (
				SESS_LANG_CHN => "用戶登錄名:",
				SESS_LANG_ENG => "User Name:" ),
			'uPass' => array (
				SESS_LANG_CHN => "登錄密碼:",
				SESS_LANG_ENG => "Login Password:" ),
			'uEmail' => array (
				SESS_LANG_CHN => "電子郵箱:",
				SESS_LANG_ENG => "Email:" ),
			'uregEmail' => array (
				SESS_LANG_CHN => "請輸入註冊過的郵箱地址:",
				SESS_LANG_ENG => "Please Enter Registered Email Address:"	),
			'uRecover' => array (
				SESS_LANG_CHN => "請幫我恢復登錄資料",
				SESS_LANG_ENG => "Reset Password"	),
			'uUpd' => array (
				SESS_LANG_CHN => "更新密碼",
				SESS_LANG_ENG => "Update Password"	),
			'rUG' => array (
				SESS_LANG_CHN => "回到<br/>用戶指南",
				SESS_LANG_ENG => "Return to<br/>User Guide" ),
			'ugLidx' => array ( /* urlIdx value which points to UG URL for Chinese and English respectively */
				SESS_LANG_CHN => "cUG",
				SESS_LANG_ENG => "eUG" )	
		);
		return $htmlNames[ $what ][ $sessLang ];
	} // function xLate()

	$hdrLoc = "location: " . URL_ROOT . "/admin/UsrPortal/index.php";
	session_start();
	if ( isset( $_SESSION[ 'usrName' ] ) || !isset( $_GET[ 'l' ] ) ) {
		// already logged in, or language not specified
		header( $hdrLoc ); // the redirected PHP file will figure out the language
	}

	$sessLang = ( $_GET[ 'l' ] == 'e' ) ? SESS_LANG_ENG : SESS_LANG_CHN; // set Lang from the CGI parameter
	$_SESSION[ 'sessLang' ] = $sessLang;
	$useChn = ( $sessLang == SESS_LANG_CHN );
	$hLtrS = ( $useChn ) ? "12px;" : "normal;"; // letter spacing for <h*> elements

	unset($usrReq);
	$myDir = basename( dirname( __FILE__ ) );
	$myBasename = basename( __FILE__ );
	$resetURLroot = ( $_os == 'DAR' ) ? "http://www.localplc.org/admin" : "https://www.amitabhalibrary.org/admin";
	$href_url = $resetURLroot . "/$myDir" . "/$myBasename";
	$msgTxt = '';
	$mbxTxtA = "";
	$mbxDisplay = "none";
	$mbxBC = "#00b300";
	if (isset($_GET[ 'my_Req' ])) { // $_GET[ 'my_Req' ] may have Chinese characters; use 'usr_Req'!
		unset( $myID ); unset( $myUsrName ); unset( $myPass ); unset( $myEmail );
		unset( $myToken ); $rtnV = array(); $usrReq = $_GET[ 'usr_Req' ];
		$paintReset = false;
		switch ( $usrReq ) {
			case 'fgt_Pass':	// User indicated forgot password; paint the Email Entry Form
				break;
			case 'chk_Email':	// Email was entered; send the user the reset Link;
				$myEmail = $_GET[ 'usr_Email' ];
				if ( validateEmail( $myEmail, $rtnV, $useChn ) ) {
					$myID = $rtnV [ 'ID' ];
					$resetToken = $rtnV [ 'Token' ];
				}
				if ( isset( $myID ) ) {
					$txtBlock = ( $useChn ) ? "ChnTxt" : "EngTxt";
					$dateTxt = date(DateFormatLtr);
					if ( $useChn ) $dateTxt = mmddyyyy2Chn( $dateTxt );
					$msg = new HTML_Template_IT("./Templates");
					$msg->loadTemplatefile("loginResetMSG.tpl", true, true);
					$msg->setCurrentBlock("msgBlock");
					$msg->setCurrentBlock("{$txtBlock}");
					$msg->setVariable("Date", $dateTxt );
					$href_param="my_Req=chk_Token&usr_Req=chk_Token&usr_ID={$myID}&Token={$resetToken}";
					$href_param	.= ( $useChn ) ? "&l=c" : "&l=e";
					$msg->setVariable("href_url", $href_url );
					$msg->setVariable("reset_param", $href_param );
					$msg->parse("{$txtBlock}");
					$msg->parse("msgBlock");
					if ( $_os == 'DAR' ) { // on Mac
						echo $msg->get(); exit;
					} else {
						plcSendMail ( $myEmail, "Login/Password Recovery", $msg->get() );
						$msgTxt = ( $useChn)
							? "恢復密碼的網鍊已經送到您註冊過的郵電地址，請由那網鍊處重新設立密碼。"
							: "A link was sent to your registered email address; please follow it to reset.";
					}
				} // $myID set
				break;
			case 'chk_Token':	// Token submitted; paint Reset Form after validation, otherwise Email Entry Form
				$myID = $_GET[ 'usr_ID' ];
				$myToken = $_GET[ 'Token' ];
				validateToken( $myID, $myToken, $rtnV, $useChn );
				$myUsrName= ( $_errCount == 0 ) ? $rtnV[ 'usrName' ] : '';
				$myEmail = ( $_errCount == 0 ) ? $rtnV[ 'usrEmail' ] : '';
				$paintReset = ( $_errCount == 0 );
				break;
			case 'upd_Pass': // New Password submitted; redirect after successful update
				$myID = $_GET[ 'usr_ID' ];
				$myUsrName= $_GET[ 'usr_Name' ];
				$myEmail = $_GET[ 'usr_Email' ];
				$myPass = $_GET[ 'usr_Pass' ];
				$sessType = SESS_TYP_USR;
				$sessLang = ( $useChn ) ? SESS_LANG_CHN : SESS_LANG_ENG;
				if ( updPass( $myID, $myEmail, $myPass, $useChn ) ) {
					$_SESSION[ 'usrName' ] = $myUsrName;
					$_SESSION[ 'usrPass' ] = $myPass;
					$_SESSION[ 'usrEmail' ] = $myEmail;
					$_SESSION[ 'sessType' ] = $sessType;
					$_SESSION[ 'sessLang' ] = $sessLang;
					$_SESSION[ 'LAST_ACTIVITY' ] = $_SERVER[ 'REQUEST_TIME' ];
					header( $hdrLoc ); // script will not return
				}
				$paintReset = true;
				break;
		} // switch on usr_Req
		if ( $_errCount > 0 ) {
			$msgTxt = ( $useChn ) ?
				"恢復登錄密碼遭遇下列錯誤；請重復或 <a href=\"mailto:library@amitabhalibrary.org\">通知本網站管理員</a> 。謝謝！" :
				"Error occurred! Please retry or <a href=\"mailto:library@amitabhalibrary.org\">Report.</a> Thank you!";
			$lineNbrg = ( $_errCount > 1 );
			for ( $i = 0; $i < $_errCount; $i++ ) {
				$lineBreak = ( strlen( $msgTxt ) > 0 ) ? "<br/>" : '';
				$lineNbr = "[ " . ($i + 1) . " ] ";
				$msgTxt .= $lineBreak . ( $lineNbrg ? $lineNbr : '' ) . $_errRec[ $i ];
			}
			$mbxBC = "red"; // border color
		}
		$hTop = ( strlen( $msgTxt ) > 0 ) ? "5vh" : "10vh";
	} // End of handling Login Recovery
?>

<!DOCTYPE html>
<HTML>
<HEAD>
<TITLE><?php echo xLate( 'htmlTitle' ); ?></TITLE>
<META HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=UTF-8">
<link rel="stylesheet" type="text/css" href="../master.css">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script src="../futureAlert.js"></script>
<script src="./Login.js"></script>
<style>
	table.pgMenu {
		table-layout: auto;
	}
	table.dialog td {
		text-align: left;
		font-size: 1.1em;
	}
	input {
		position: relative;
		left: 10%;
		width: 80%;
		font-size: 1.1em;
	}
	input[type=submit] {
		left: 15%;
		color: white;
		background-color: #00b300;
		width: 70%;
		line-height: 2.0em;
		border-radius: 8px;
	}
</style>
</HEAD>
<body>
	<div class="hdrRibbon">
		<img src="https://www.amitabhalibrary.org/pic/PLC_logo_TR.png" class="centerMeV" alt="">
		<div id="pgTitle" class="centerMeV">
			<span style="letter-spacing: 2px;">淨土念佛堂用戶重新設立密碼主頁</span><br/>
			<span class="engClass">Pure Land Center User Password Reset</span></div>		
		<table class="pgMenu centerMeV">
			<thead>
				<tr>
					<th data-urlIdx="usrHome"><?php echo xLate( 'featPW' ); ?></th>
					<th data-urlIdx="<?php echo xLate('ugLidx');?>"><?php echo xLate( 'rUG' ); ?></th>
					<th class="future"><?php echo xLate( 'featFuture' ); ?></th>
					<th data-urlIdx="usrLogout"><?php echo xLate( 'logOut' ); ?></th>
				</tr>
			</thead>
		</table>
	</div>
<!-- ***** BEGIN dataArea -->
	<div class="dataArea">
		<h1 class="dataTitle" style="margin-top: <?php echo $hTop; ?>>;letter-spacing: <?php echo $hLtrS; ?>;">
			<?php echo xLate( 'h2Title' ); ?>
		</h1>
<!-- ***** BEGIN Acknowledgement Area ***** -->
<?php
	if ( strlen( $msgTxt ) > 0 ) {
		$mbxLtrSP = ( $useChn ) ? "20px" : "normal"; // letter-spacing
		$mbxTxtA = "left"; // text-align
		$mbxFontW = "normal"; // font-weight
		$mbxDisplay = "block";
	}
?>
		<div style="width: 55%; margin: auto; border: 7px solid; border-radius: 8px; padding: 2px 3px;
				font-size: 1.2em;
				text-align: <?php echo $mbxTxtA;?>;
				letter-spacing: normal;
				display: <?php echo $mbxDisplay;?>;
				border-color: <?php echo $mbxBC;?>;">
			<?php echo $msgTxt; ?>
		</div>
<!-- ***** END Acknowledgement Area ***** -->
<!-- ***** BEGIN Input Area ***** -->
		<form action="" method="get">
			<input type="hidden" name="l" value="<?php if ( $useChn ) { echo 'c'; } else { echo 'e'; }?>">
			<table class="dialog centerMe">
				<tbody>
<?php
//	$emailTxt = ( !isset( $myEmail ) ) ? ( ( $useChn) ? '郵箱地址 (英文)' : 'Email Address' ) : "{$myEmail}";
	$emailPrompt = ( $useChn ) ? '請輸入郵箱地址 (英文)' : 'Enter Email Address' ;
	$passPrompt = ( !isset( $myPass ) ) ? 'Please Enter New Password' : "{$myPass}";
	if ( !$paintReset ) { 
?>
					<tr>
			  			<td><?php echo xLate( 'uregEmail' ); ?><br/>
			  				<input type="email" name="usr_Email" id="uEmail" data-pmptV=""
			  						data-oldV="<?php echo $emailPrompt; ?>"
			  						value="<?php echo $emailPrompt; ?>" required>
			  			</td>
			  			<td>
							<input type="hidden" name="usr_Req" value="chk_Email">
							<input type="submit" id="uSubEmail" class="pushButton"
									name="my_Req" value="<?php echo xLate( 'uRecover' ); ?>" >
				    	</td>
			  		</tr>
<?php
	} else {
?>
					<tr>
						<td><?php echo xLate( 'uName' ); ?><br/>
							<input type="text" name="usr_Name" id="uName" value="<?php echo $myUsrName; ?>" readonly>
						</td>
						<td><?php echo xLate( 'uPass' ); ?><br/>
							<input type="password" name="usr_Pass" id="uPass" data-pmptV=""
									data-oldV="<?php echo $passPrompt; ?>"
									value="<?php echo $passPrompt; ?>" required>
				    	</td>
				  	</tr>
				  	<tr>
				  		<td><?php echo xLate( 'uEmail' ); ?><br/>
							<input type="email" name="usr_Email" id="uEmail" value="<?php echo $myEmail; ?>" readonly>
				  		</td>
				  		<td>
				  			<input type="hidden" name="usr_ID" value="<?php echo $myID; ?>">
				  			<input type="hidden" name="usr_Req" value="upd_Pass">
				  			<input type="submit" id="uUpd" class="pushButton"
				  				name="my_Req" value="<?php echo xLate( 'uUpd' ); ?>">
				  		</td>
				  </tr>
<?php
	}
?>
				</tbody>
			</table>
		</form>
<!-- ***** End Input Area ***** -->
	</div>
<!-- ***** End dataArea ***** -->
</BODY>
<HTML>