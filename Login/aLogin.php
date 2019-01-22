<?php
	require_once( '../pgConstants.php' );
	require_once( 'dbSetup.php' ); 
	require_once( 'Login_Funcs.php' );

	function xLate( $what ) {
		global $sessLang;
		$htmlNames = array (
			'htmlTitle' => array (
				SESS_LANG_CHN => "淨土念佛堂用戶主頁",
				SESS_LANG_ENG => "Pure Land Center<br/>User Main Page" ),
			'featPW' => array (
				SESS_LANG_CHN => "法會牌位申請",
				SESS_LANG_ENG => "Name Plaque Application for<br/>Merit Dedication in Retreats" ),
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
				SESS_LANG_ENG => "Please Enter Email Address" ),
			'uNew' => array (
				SESS_LANG_CHN => "&nbsp;&nbsp;我是新用戶",
				SESS_LANG_ENG => "&nbsp;&nbsp;I am a new user" ),
			'uSub' => array (
				SESS_LANG_CHN => "登錄",
				SESS_LANG_ENG => "Login" ),
			'uFgt' => array (
				SESS_LANG_CHN => "忘了登錄密碼",
				SESS_LANG_ENG => "Forgot password" )
		);
		return $htmlNames[ $what ][ $sessLang ];
	} // function xLate()
	
	$hdrLoc = "location: " . URL_ROOT . "/admin/AdmPortal/index.php";
	session_start(); // create or retrieve
	if ( isset( $_SESSION[ 'usrName' ] ) ) { // already logged in
		header( $hdrLoc ); // the redirected PHP file will figure out the language
	}

	$sessLang = SESS_LANG_CHN; // set Lang to English
	if ( !isset($_SESSION[ 'sessLang' ]) ) {
		$_SESSION[ 'sessLang' ] = $sessLang;
	}
	$sessLang = $_SESSION[ 'sessLang' ];
	
	$useChn = ( $sessLang == SESS_LANG_CHN );
	$hLtrS = ( $useChn ) ? "12px" : "normal"; // letter spacing for <h*> element
	$hTop = "15vh";
	$msgTxt = '';
	if (isset($_POST[ 'usr_Req' ])) {
		$myUsrName = $_POST[ 'usr_Name' ];
		$myPass = $_POST[ 'usr_Pass' ];
		$myEmail = ( isset( $_POST[ 'usr_Email' ] ) ) ? $_POST[ 'usr_Email' ] : null ;
		$sessType = ( $_POST[ 'sess_Typ' ] == "SESS_TYP_MGR" ) ? SESS_TYP_MGR : SESS_TYP_WEBMASTER;
		validateUser( $myUsrName, $myPass, $sessType, $rtnV, $useChn );
		if ( $_errCount == 0 ) { // login successful
			$_SESSION[ 'usrName' ] = $myUsrName;
			$_SESSION[ 'usrPass' ] = $myPass;
			$_SESSION[ 'usrEmail' ] = ($myEmail != null) ? $myEmail : $rtnV[ 'usrEmail' ] ;
			$_SESSION[ 'sessType' ] = $sessType;
			$_SESSION[ 'LAST_ACTIVITY' ] = $_SERVER[ 'REQUEST_TIME' ];			
			header( $hdrLoc );
		} else { // formulate message and style
			$hTop = "8vh";
			$msgTxt = ( $useChn ) ?
				"登錄遭遇下列錯誤；請重新登錄或 <a href=\"mailto:library@amitabhalibrary.org\">通知 Bert</a> 。謝謝！" :
				"Error occurred! Please retry or <a href=\"mailto:library@amitabhalibrary.org\">Contact Bert.</a> Thank you!";
		}
	} // user request
	unset($_POST);
?>

<!DOCTYPE html>
<html>
<head>
<title><?php echo xLate( 'htmlTitle' ); ?></title>
<meta http-equiv="content-type" content="text/html; charset=UTF-8">
<link rel="stylesheet" type="text/css" href="https://www.amitabhalibrary.org/css/base.css">
<link rel="stylesheet" type="text/css" href="../css/admin.css">
<link rel="stylesheet" type="text/css" href="../css/menu.css">
<link rel="stylesheet" type="text/css" href="./Login.css">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script src="./Login.js"></script>
</head>
<body>
	<div class="hdrRibbon">
		<img src="https://www.amitabhalibrary.org/pic/PLC_logo_TR.png" alt="">
		<div id="pgTitle" class="centerMeV">
			<span style="letter-spacing: 2px;">淨土念佛堂一般管理用戶登錄</span><br/>
			<span class="engClass">Pure Land Center Administrator Login</span></div>		
		<table id="myMenuTbl" class="centerMeV">
			<thead>
				<tr>
					<th><?php echo xLate( 'htmlTitle' ); ?></th>
					<th class="future"><?php echo xLate( 'featFuture' ); ?></th>
				</tr>
			</thead>
		</table>
		<div id="pgLogOut" class="centerMeV"><a href="./Logout.php"><?php echo xLate( 'logOut' ); ?></a></div>
	</div>
	<div class="dataArea">
		<h2 id="myDataTitle"
			style="margin-top: <?php echo $hTop; ?>; letter-spacing: <?php echo $hLtrS; ?>;">
			<?php echo xLate( 'h2Title' ); ?></h2>
<?php
	if ( strlen( $msgTxt ) > 0 ) {
		echo putMsg( "60%", "normal", "left", "normal", $msgTxt );
	}
?>
		<form action="" method="post">
			<table class="dataTbl centerMe" id="myLoginTbl">
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
						<td style="text-align: left; vertical-align:middle;">管理層次:<br/>
							<select name="sess_Typ" style="width: 90%; font-size: 1.0em;">
								<option value="SESS_TYP_MGR">一般管理員</option>
								<option value="SESS_TYP_WEBMASTER">網站管理員</option>
							</select>
						</td>
 						<td style="text-align: center; vertical-align: middle;">
							<input type="submit" id="uSubLogin" name="usr_Req" class="pushButton" value="<?php echo xLate( 'uSub' ); ?>">							
				    	</td>
			  		</tr>
				</tbody>
			</table>
		</form>
	</div>
</body>
</html>