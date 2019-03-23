<?php
	require_once( '../pgConstants.php' );
	require_once( 'dbSetup.php' ); 
	require_once( 'Login_Funcs.php' );

	function xLate( $what ) {
		global $sessLang;
		$htmlNames = array (
			'htmlTitle' => array (
				SESS_LANG_CHN => "淨土念佛堂用戶登錄主頁",
				SESS_LANG_ENG => "Pure Land Center User Login Page" ),
			'featPW' => array (
				SESS_LANG_CHN => "淨土念佛堂<br/>用戶主頁",
				SESS_LANG_ENG => "Pure Land Center<br/>User Portal" ),
			'featFuture' => array (
				SESS_LANG_CHN => "其他未來會提供的功能<br/>( 週日早課祈福及迴向申請，結緣法寶申請，等等。)",
				SESS_LANG_ENG => "Future Capabilities<br/>(e.g., Req. for Dharma Items; etc.)" ),
			'logOut' => array (
				SESS_LANG_CHN => "用戶<br/>撤出",
				SESS_LANG_ENG => "User<br/>Logout" ),
			'h2Title' => array (
				SESS_LANG_CHN => "用戶請登錄",
				SESS_LANG_ENG => "Please Login In" ),
			'uName' => array (
				SESS_LANG_CHN => "用戶登錄名:",
				SESS_LANG_ENG => "User Name:" ),
			'uNameVal' => array (
				SESS_LANG_CHN => "請輸入登錄名",
				SESS_LANG_ENG => "Enter User Name" ),
			'uPass' => array (
				SESS_LANG_CHN => "登錄密碼:",
				SESS_LANG_ENG => "Login Password:" ),
			'uPassVal' => array (
				SESS_LANG_CHN => "請輸入登錄密碼",
				SESS_LANG_ENG => "Enter Password" ),
			'uEmail' => array (
				SESS_LANG_CHN => "電子郵箱:",
				SESS_LANG_ENG => "Email:" ),
			'uEmailVal' => array (
				SESS_LANG_CHN => "請輸入郵箱地址",
				SESS_LANG_ENG => "Enter Email Address" ),
			'uNew' => array (
				SESS_LANG_CHN => "我是新用戶",
				SESS_LANG_ENG => "I am a new user" ),
			'uSub' => array (
				SESS_LANG_CHN => "登錄",
				SESS_LANG_ENG => "Login" ),
			'uFgt' => array (
				SESS_LANG_CHN => "忘了登錄密碼",
				SESS_LANG_ENG => "Forgot<br/>Password" ),
			'rUG' => array (
				SESS_LANG_CHN => "回到<br/>用戶指南",
				SESS_LANG_ENG => "Return to<br/>User Guide" ),
			'ugL' => array (
				SESS_LANG_CHN => "c",
				SESS_LANG_ENG => "e" )
		);
		return $htmlNames[ $what ][ $sessLang ];
	} // function xLate()
	
	$hdrLoc = "location: " . URL_ROOT . "/admin/UsrPortal/index.php";
	$rstLink = "./LoginRestore.php?my_Req=fgt_Pass&usr_Req=fgt_Pass";
	session_start(); // create or retrieve
	if ( isset( $_SESSION[ 'usrName' ] ) || ( !isset( $_GET[ 'l' ] ) ) ) {
		// already logged in or Login language not specified
		header( $hdrLoc ); // the redirected PHP file will figure out the language
	}

	$sessLang = ( $_GET[ 'l' ] == 'e' ) ? SESS_LANG_ENG : SESS_LANG_CHN; // set Lang from the CGI parameter
	$_SESSION[ 'sessLang' ] = $sessLang;
	$useChn = ( $sessLang == SESS_LANG_CHN );
	$rstLink .= ( $useChn ) ? "&l=c" : "&l=e";
	$_SESSION[ 'uResetLink' ] = $rstLink; // So can pass it to the client side via Jquery.ajax();
	$hLtrS = ( $useChn ) ? "12px" : "normal"; // letter spacing for <h*> element
	
	$sessType = SESS_TYP_USR;
	$msgTxt = '';
	$mbxDisplay = "none";
	$mbxBC = "#00b300";
	if (isset($_POST[ 'usr_Req' ])) {
		$myUsrName = $_POST[ 'usr_Name' ];
		$myPass = $_POST[ 'usr_Pass' ];
		$myEmail = ( isset( $_POST[ 'usr_Email' ] ) ) ? $_POST[ 'usr_Email' ] : null ;
		if ( !isset( $_POST[ 'usr_New' ] ) ) {
			validateUser( $myUsrName, $myPass, $sessType, $rtnV, $useChn );
		} else {
			registerUser( $myUsrName, $myPass, $myEmail, $useChn );
		}
		if ( $_errCount == 0 ) { // login successful
			$_SESSION[ 'usrName' ] = $myUsrName;
			$_SESSION[ 'usrPass' ] = $myPass;
			$_SESSION[ 'usrEmail' ] = ($myEmail != null) ? $myEmail : $rtnV[ 'usrEmail' ] ;
			$_SESSION[ 'sessType' ] = $sessType;
			$_SESSION[ 'LAST_ACTIVITY' ] = $_SERVER[ 'REQUEST_TIME' ];
			header( $hdrLoc );
		} else { // formulate message
			$msgTxt = ( $useChn ) ?
				"登錄遭遇下列錯誤；請重新登錄或 <a href=\"mailto:library@amitabhalibrary.org\">通知本網站管理員</a> 。謝謝！" :
				"Error occurred! Please retry or <a href=\"mailto:library@amitabhalibrary.org\">Report.</a> Thank you!";
			$lineNbrg = ( $_errCount > 1 );
			for ( $i = 0; $i < $_errCount; $i++ ) {
				$lineBreak = ( strlen( $msgTxt ) > 0 ) ? "<br/>" : '';
				$lineNbr = "[ " . ($i + 1) . " ] ";
				$msgTxt .= $lineBreak . ( $lineNbrg ? $lineNbr : '' ) . $_errRec[ $i ];
			}
			$mbxBC = "red"; // border color
			$mbxDisplay = "block";
		}
	} // user request
	if ( isset( $_POST[ 'dbReq' ] ) ) { // Jquery AJAX request
		switch( $_POST[ 'dbReq' ] ) {
		case "uResetLink":
			$rpt[ "uResetLink" ] = $_SESSION[ 'uResetLink' ];
			echo json_encode( $rpt, JSON_UNESCAPED_UNICODE );
			exit;
		} // switch()
	}
	$hTop = ( strlen( $msgTxt ) ) ? "5vh" : "10vh";
	unset($_POST);
?>

<!DOCTYPE html>
<html>
<head>
<title><?php echo xLate( 'htmlTitle' ); ?></title>
<meta http-equiv="content-type" content="text/html; charset=UTF-8">
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
		left: 36%;
		color: white;
		background-color: #00b300;
		width: 28%;
		line-height: 2.0em;
		border-radius: 8px;
	}
	table.dialog td[data-urlIdx] div {
		position: relative;
		display: blodk;
		color: white;
		background-color: #00b300;
		width: 44%;
		left: 28%;
		border-radius: 8px;
		text-align: center;
		vertical-align: middle;
	}
</style>
</head>
<body>
	<div class="hdrRibbon">
		<img src="https://www.amitabhalibrary.org/pic/PLC_logo_TR.png" class="centerMeV" alt="">
		<div id="pgTitle" class="centerMeV">
			<span>淨土念佛堂一般用戶登錄</span><br/>
			<span class="engClass">Pure Land Center User Login</span></div>		
		<table class="pgMenu centerMeV">
			<thead>
				<tr>
					<th data-urlIdx="usrHome"><?php echo xLate( 'featPW' ); ?></th>
					<th data-urlIdx="rUG" data-ugL="<?php echo xLate( 'ugL' );?>"><?php echo xLate( 'rUG' ); ?></th>
					<th class="future"><?php echo xLate( 'featFuture' ); ?></th>
					<th data-urlIdx="usrLogout"><?php echo xLate( 'logOut' ); ?></th>
				</tr>
			</thead>
		</table>
	</div>
<!-- ***** BEGIN dataArea -->
	<div class="dataArea">
		<h1 class="dataTitle" style="margin-top: <?php echo $hTop;?>; letter-spacing: <?php echo $hLtrS; ?>;">
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
		<div style="width: 50%; margin: auto; border: 7px solid; border-radius: 8px; padding: 2px 3px;
				font-size: 1.2em;
				text-align: <?php echo $mbxTxtA;?>;
				letter-spacing: normal;
				display: <?php echo $mbxDisplay;?>;
				border-color: <?php echo $mbxBC;?>;">
			<?php echo $msgTxt; ?>
		</div>
<!-- ***** END Acknowledgement Area ***** -->
<!-- ***** BEGIN Input Area ***** -->
		<form action="" method="post"><!-- Login Form -->
			<table class="dialog centerMe" style="top: 55%;"><!-- Login Form Table -->
				<tbody>
					<tr>
			    		<td><?php echo xLate( 'uName' ); ?><br/>
							<input type="text" name="usr_Name" id="uName" data-pmptV=""
								data-oldV="<?php echo xLate( 'uNameVal' ); ?>"
								value="<?php echo xLate( 'uNameVal' ); ?>" required>
						</td>
						<td><?php echo xLate( 'uPass' ); ?><br/>
							<input type="password" name="usr_Pass" id="uPass" data-pmptV=""
								data-oldV="<?php echo xLate( 'uPassVal' ); ?>"
								value="<?php echo xLate( 'uPassVal' ); ?>" required>
				    	</td>
					</tr>				
					<tr>
			  			<td><?php echo xLate( 'uEmail' ); ?><br/>
			  				<input type="email" name="usr_Email" id="uEmail" data-pmptV=""
			  					data-oldV="<?php echo xLate( 'uEmailVal' ); ?>"
			  					value="<?php echo xLate( 'uEmailVal' ); ?>" required disabled>
			  			</td>
						<td><?php echo xLate( 'uNew' ); ?><br/>
							<div style="display: inline-block; position: relative; left: 10%;">
								<input type="checkbox" name="usr_New" id="uNew" onClick="enableEmailEntry();">
							</div>
				    	</td>
					</tr>
			  		<tr>
 						<td>
							<input type="submit" id="uLogin" name="usr_Req" value="<?php echo xLate( 'uSub' ); ?>">							
				    	</td>
 						<td data-urlIdx="uResetLink">
							<div style="<?php if ( $useChn ) echo 'line-height: 2.0em;';?>">
								<?php echo xLate( 'uFgt' ); ?>
							</div>
				    	</td>
			  		</tr>
				</tbody>
			</table><!-- Login Form Table -->
		</form><!-- Login Form -->
	</div>
</body>
</html>