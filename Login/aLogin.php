<?php
	require_once( '../pgConstants.php' );
	require_once( 'dbSetup.php' );
	require_once( 'Login_Funcs.php' );
	
	$hdrLoc = "location: " . URL_ROOT . "/admin/AdmPortal/index.php";
	session_start(); // create or retrieve
	if ( isset( $_SESSION[ 'usrName' ] ) ) { // already logged in
		header( $hdrLoc ); // the redirected PHP file will figure out the language
	}

	$sessLang = SESS_LANG_CHN; // set Lang to Chinese - Administrators
	$_SESSION[ 'sessLang' ] = $sessLang;
	
	$useChn = ( $sessLang == SESS_LANG_CHN );
	$hTop = "15vh";
	$msgTxt = '';
	$mbxDisplay = "none";
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
			$msgTxt = "登錄遭遇下列錯誤；請重新登錄或 <a href=\"mailto:library@amitabhalibrary.org\">通知 Bert</a> 。謝謝！";
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
	unset($_POST);
?>

<!DOCTYPE html>
<html>
<head>
<title>淨土念佛堂管理用戶登錄</title>
<meta http-equiv="content-type" content="text/html; charset=UTF-8">
<link rel="stylesheet" type="text/css" href="../master.css">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script src="../futureAlert.js"></script>
<script src="./Login.js"></script>
<style>
	table.pgMenu {
		table-layout: auto;
	}
	table.dialog {
		width: 35%;
	}
	table.dialog td {
		text-align: left;
		font-size: 1.1em;
	}

	input, select {
		position: relative;
		left: 10%;
		width: 80%;
		font-size: 1.1em;
	}
	input[type=submit] {
		left: 0%;
		color: white;
		background-color: #00b300;
		width: 25%;
		line-height: 2.0em;
		border-radius: 8px;
	}
</style>
</head>
<body>
	<div class="hdrRibbon">
		<img src="https://www.amitabhalibrary.org/pic/PLC_logo_TR.png" class="centerMeV" alt="">
		<div id="pgTitle" class="centerMeV">
			<span style="letter-spacing: 2px;">淨土念佛堂管理用戶登錄</span><br/>
			<span class="engClass">Pure Land Center Admin Login</span>
		</div>		
		<table class="pgMenu centerMeV">
			<thead>
				<tr>
					<th data-urlIdx="urlWebsiteHome">回到<br/>網站首頁</th>
					<th data-urlIdx="usrHome">淨土念佛堂<br/>用戶主頁</th>
					<th class="future">其他未來會提供的功能</th>
					<th data-urlIdx="usrLogout">用戶<br/>撤出</th>
				</tr>
			</thead>
		</table>
	</div>
	<div class="dataArea">
		<div class="dataTitle centerMeQ" style="font-size: 2.0em;">管理用戶請登錄</div>
		<div style="width: 46%; margin: auto; border: 7px solid; border-radius: 8px; padding: 2px 3px;
				margin-top: 12%;
				font-size: 1.2em;
				text-align: normal;
				letter-spacing: normal;
				display: <?php echo $mbxDisplay;?>;
				border-color: <?php echo $mbxBC;?>;">
			<?php echo $msgTxt; ?>
		</div>
		<form action="" method="post">
			<table class="dialog centerMe">
				<tbody>
					<tr>
			    		<td>用戶登錄識別：<br/>
							<input type="text" name="usr_Name" id="uName" data-pmptV=""
								data-oldV="請輸入登錄名" value="請輸入登錄名" required>
						</td>
						<td>登錄密碼:<br/>
							<input type="password" name="usr_Pass" id="uPass" data-pmptV=""
								data-oldV="請輸入登錄密碼" value="請輸入登錄密碼" required>
				    	</td>
					</tr>				
			  		<tr>
						<td>管理層次:<br/>
							<select name="sess_Typ">
								<option value="SESS_TYP_MGR">一般管理員</option>
								<option value="SESS_TYP_WEBMASTER">網站管理員</option>
							</select>
						</td>
 						<td style="text-align: center; vertical-align: middle;">
							<input type="submit" id="uLogin" name="usr_Req" class="pushButton" value="登錄">							
				    	</td>
			  		</tr>
				</tbody>
			</table>
		</form>
	</div>
</body>
</html>