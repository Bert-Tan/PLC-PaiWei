<?php
// The including PHP script already called pgConstants.php and dbSetup.php
	require_once( 'Login_Constants.php' );

	$_errCount = 0; $_errRec = array();

	function mmddyyyy2Chn( $MMddYYYY ) {
		$pattern = '%^(?<MM>[\w]*)\s(?<dd>[0-9]{1,2}),\s(?<YYYY>[0-9]{2,4})$%';
		$MM2Chn = array( 'January' => '1', 'February' => '2', 'March' => '3', 'April' => '4', 'May' => '5',
			'June' => '6', 'July' => '7', 'August' => '8', 'September' => '9', 'October' => '10',
			'November' => '11', 'December' =>	'12'
		);
		preg_match( $pattern, $MMddYYYY, $_scratch, PREG_OFFSET_CAPTURE );
		if ( !isset( $_scratch[ 'MM' ] ) ) {
			echo __FUNCTION__ . "() Line\t" . __LINE__ . ":\tError:\Month Format Error\n"; exit;
		}
		if ( !isset( $_scratch[ 'dd' ] ) ) {
			echo __FUNCTION__ . "() Line\t" . __LINE__ . ":\tError:\Day Format Error\n"; exit;
		}			
		if ( !isset( $_scratch[ 'YYYY' ] ) ) {
			echo __FUNCTION__ . "() Line\t" . __LINE__ . ":\tError:\Year Format Error\n"; exit;
		}
		$MM_in_Chn = $MM2Chn[ $_scratch[ 'MM' ][0] ];
		return "{$_scratch[ 'YYYY' ][0]} 年 " . "{$MM_in_Chn} 月 " . "{$_scratch[ 'dd' ][0]} 日";
	} // MMddyyyy2Chn()

	function usersDropdown( $admFlag, $rspChn ) {
		//
		// Retrieves Users and constructs a drop-down list
		//
		global $_db, $_errCount, $_errRec;

		if ( (boolean)$admFlag == true ) {
			$sql = "SELECT ID, usrName FROM Usr WHERE ID IN ( SELECT ID FROM admUsr ) ORDER BY ID;";
		} else {
			$sql = "SELECT ID, usrName FROM Usr WHERE ID NOT IN ( SELECT ID FROM admUsr ) ORDER BY ID;";
		}
		$rslt = $_db->query( $sql );
		if ( $_db->errno ) {
			$errMsg = ( $rspChn ) ? "資料庫內部錯誤!" : "DB internal error!";
			$_errCount++;
			if (!DEBUG) {
				$_errRec[] = $errMsg;
			} else {
				$_errRec[] = __FUNCTION__ . '() ' . __LINE__ . ":&nbsp;$_db->error;&nbsp;{$errMsg}";
			}
			return false;
		}
		$num_Usrs = $rslt->num_rows;
		$rows = $rslt->fetch_all( MYSQLI_ASSOC );
		$rslt->free();
		$tpl = new HTML_Template_IT("./Templates");
		$tpl->loadTemplatefile("UsersDropdown.tpl", true, true);
		foreach ( $rows as $row ) { 
			$tpl->setCurrentBlock("selOption");  
	  		$tpl->setVariable( "usrName" , $row[ 'usrName' ] );
	  		$tpl->setVariable( "ID" , $row[ 'ID' ] );
	  		$tpl->parse("selOption");
	  	}
		return $tpl->get();
	} // usersDropdown()
	
	function updPass( $usrID, $usrEmail, $usrPass, $rspChn ) {
		global $_db, $_errCount, $_errRec;
		
		$escEmail = $_db->real_escape_string( $usrEmail );
		$escPass = $_db->real_escape_string( $usrPass );
		$hashed_pass = password_hash( $escPass, PASSWORD_BCRYPT, [ 'cost' => HASH_COST ] );
		$sql = "SELECT * FROM Usr WHERE `ID` = \"{$usrID}\" AND `UsrEmail` = \"{$escEmail}\";";
		$rslt = $_db->query( $sql );
		if ( $_db->errno ) {
			$errMsg = ( $rspChn ) ? "資料庫內部錯誤!" : "DB internal error!";
			$_errCount++;
			if (!DEBUG) {
				$_errRec[] = $errMsg;
			} else {
				$_errRec[] = __FUNCTION__ . '() ' . __LINE__ . ":&nbsp;$_db->error;&nbsp;{$errMsg}";
			}
			return false;
		}
		if ( $rslt->num_rows == 0 ) {
			$errMsg = ( $rspChn ) ? "資料庫可能損壞了；沒找到登錄資料！" : "Possible DB Corruption; No Record Found!";
			$_errCount++;
			$_errRec[] = $errMsg;
			return false;
		}
		$row = $rslt->fetch_all( MYSQLI_ASSOC )[0];
		$usrName = $row[ 'UsrName' ];
		$sql = "UPDATE Usr SET `UsrPass` = \"{$hashed_pass}\" WHERE `ID` = {$usrID};";
		$rslt = $_db->query( $sql );
		if ( $_db->errno ) {
			$errMsg = ( $rspChn ) ? "資料庫內部錯誤!" : "DB internal error!";
			$_errCount++;
			if (!DEBUG) {
				$_errRec[] = $errMsg;
			} else {
				$_errRec[] = __FUNCTION__ . '() ' . __LINE__ . ":&nbsp;$_db->error;&nbsp;{$errMsg}";
			}
			return false;
		}
		if ( $_db->affected_rows == 0 ) {
			$errMsg = ( $rspChn ) ? "資料庫可能損壞了；無法更新登錄密碼！" : "Possible DB Corruption; Failed Updating Password!";
			$_errCount++;
			$_errRec[] = $errMsg;
			return false;
		}

		// update password successfully, delete password reset token
		$sql = "DELETE FROM UsrRst WHERE `ID` = \"{$usrID}\";";
		$rslt = $_db->query( $sql );
		if ( $_db->errno ) {
			$errMsg = ( $rspChn ) ? "資料庫內部錯誤!" : "DB internal error!";
			$_errCount++;
			if (!DEBUG) {
				$_errRec[] = $errMsg;
			} else {
				$_errRec[] = __FUNCTION__ . '() ' . __LINE__ . ":&nbsp;$_db->error;&nbsp;{$errMsg}";
			}
			return false;
		}

		return true;
	} // updPass()

	function validateToken( $usrID, $usrToken, &$rtnV, $rspChn ) {
		global $_db, $_errCount, $_errRec;
		
		$sql = "SELECT Expires FROM UsrRst WHERE `ID` = \"{$usrID}\" AND `Token` = \"{$usrToken}\" ;";
		$rslt = $_db->query( $sql );
		if ( $_db->errno ) {
			$errMsg = ( $rspChn ) ? "資料庫內部錯誤!" : "DB internal error!";
			$_errCount++;
			if (!DEBUG) {
				$_errRec[] = $errMsg;
			} else {
				$_errRec[] = __FUNCTION__ . '() ' . __LINE__ . ":&nbsp;$_db->error;&nbsp;{$errMsg}";
			}
			return false;
		}
		if ( $rslt->num_rows == 0 ) {
			$errMsg = ( $rspChn ) ? "沒找到恢復密碼的請求！" : "No Password Reset Record Found!";
			$_errCount++;
			$_errRec[] = $errMsg;
			return false;
		}
		$row = $rslt->fetch_all( MYSQLI_ASSOC )[0];
		$dbExpires = $row[ 'Expires' ];
		
		if ( time() > strtotime( $dbExpires ) ) {
			$errMsg = ( $rspChn ) ? "恢復密碼的請求已過期！請重新請求。" : "Reset Timer Expired! Please Re-Submit Reset Request!";
			$_errCount++;
			$_errRec[] = $errMsg;
			$sql = "DELETE FROM `UsrRst` WHERE `ID` = \"{$usrID}\"; ";
			$_db->query( $sql ); /* Delete obsolete one */
			return false;
		}
		$sql = "SELECT * FROM Usr WHERE `ID` = \"{$usrID}\" ;";
		$rslt = $_db->query( $sql );
		if ( $_db->errno ) {
			$errMsg = ( $rspChn ) ? "資料庫內部錯誤!" : "DB internal error!";
			$_errCount++;
			if (!DEBUG) {
				$_errRec[] = $errMsg;
			} else {
				$_errRec[] = __FUNCTION__ . '() ' . __LINE__ . ":&nbsp;$_db->error;&nbsp;{$errMsg}";
			}
			return false;
		}
		if ( $rslt->num_rows == 0 ) {
			$errMsg = ( $rspChn ) ? "資料庫可能損壞了；找到恢復密碼的請求，但沒有找到您登錄資料"
								  : "Possible DB Corruption - CANNOT Find Requested Login Record!";
			$_errCount++;
			$_errRec[] = $errMsg;
			return false;
		}	
		$row = $rslt->fetch_all( MYSQLI_ASSOC )[0];
		$rtnV[ 'ID' ] = $row[ 'ID'];
		$rtnV[ 'usrName' ] = $row[ 'UsrName' ];
		$rtnV[ 'usrEmail' ] = $row[ 'UsrEmail' ];
		return true;
	} // validateToken()

	function validateEmail( $usrEmail, &$rtnV, $rspChn ) {
		global $_db, $_errCount, $_errRec;
		$rtnV = null;

		$escEmail = $_db->real_escape_string( $usrEmail );		
		$sql = "SELECT ID FROM Usr WHERE `UsrEmail` = \"{$escEmail}\";";
		$rslt = $_db->query( $sql );
		if ( $_db->errno ) {
			$errMsg = ( $rspChn ) ? "資料庫內部錯誤!" : "DB internal error!";
			$_errCount++;
			if (!DEBUG) {
				$_errRec[] = $errMsg;
			} else {
				$_errRec[] = __FUNCTION__ . '() ' . __LINE__ . ":&nbsp;$_db->error;&nbsp;{$errMsg}";
			}
			return false;
		}

		if ( $rslt->num_rows == 0 ) {
			$errMsg = ( $rspChn ) ? "沒找到郵箱地址!" : "Entered Email Address Not Found!";
			$_errCount++;
			$_errRec[] = $errMsg;
			return false;
		}

		$row = $rslt->fetch_all( MYSQLI_ASSOC )[0];
		$myID = $row [ 'ID' ];		
		$sql = "SELECT `Token` FROM UsrRst WHERE `ID` = {$myID};";
		$rslt = $_db->query( $sql );
		if ( $_db->errno ) {
			$errMsg = ( $rspChn ) ? "資料庫內部錯誤!" : "DB internal error!";
			$_errCount++;
			if (!DEBUG) {
				$_errRec[] = $errMsg;
			} else {
				$_errRec[] = __FUNCTION__ . '() ' . __LINE__ . ":&nbsp;$_db->error;&nbsp;{$errMsg}";
			}
			return false;
		}

		$myToken = null;
		$myExpiration = date( DateFormatSQL, strtotime( ' + 30 minutes' ) );
		if ( $rslt->num_rows == 0 ) { // no existing token, insert
			$myToken = password_hash( date(DateFormatSQL, time() ), PASSWORD_BCRYPT, [ 'cost' => HASH_COST ] );
			$sql = "INSERT INTO UsrRst ( ID, Token, Expires ) VALUES ( {$myID}, \"{$myToken}\", \"{$myExpiration}\" );";
		}
		else { // exist a token, update expire time
			$myToken = $rslt->fetch_all( MYSQLI_ASSOC )[0]['Token'];
			$sql = "UPDATE UsrRst SET Expires = \"{$myExpiration}\" WHERE `ID` = {$myID};";
		}		

		$rslt = $_db->query( $sql );
		if ( $_db->errno ) {
			$errMsg = ( $rspChn ) ? "資料庫內部錯誤!" : "DB internal error!";
			$_errCount++;
			if (!DEBUG) {
				$_errRec[] = $errMsg;
			} else {
				$_errRec[] = __FUNCTION__ . '() ' . __LINE__ . ":&nbsp;$_db->error;&nbsp;{$errMsg}";
			}
			return false;
		}
		$rtnV [ 'ID' ] = $myID;
		$rtnV [ 'Token' ] = $myToken;
		return true;
	} // validateEmail()

	function validateUser( $usrName, $usrPass, $sessType, &$rtnV, $rspChn ) {
		global $_db, $_errCount, $_errRec;

		$escName = $_db->real_escape_string( $usrName );
		$escPass = $_db->real_escape_string( $usrPass );
	
		if ( $sessType == SESS_TYP_USR ) {
			$sql = "SELECT * FROM Usr WHERE `UsrName` = BINARY \"{$escName}\";";
		} else {
			$sql = "SELECT * FROM Usr WHERE `UsrName` = BINARY \"{$escName}\" AND ID IN "
				 . "( SELECT ID FROM admUsr WHERE `SessTyp` = \"{$sessType}\" );";
		}

		$rslt = $_db->query( $sql );
		if ( $_db->errno ) {
			$errMsg = ( $rspChn ) ? "登錄失敗，資料庫內部錯誤" : "Login Failed; DB internal error";
			$_errCount++;
			if ( ! DEBUG ) {
				$_errRec[] = $errMsg;
			} else {
				$_errRec[] = __FUNCTION__ . '() ' . __LINE__ . ":&nbsp;$_db->error;&nbsp;{$errMsg}";
			}
			return false;
		}
		if ( $rslt->num_rows == 0 ) {
			switch ( $sessType ) {
			case SESS_TYP_MGR:
				$xtra = ( $rspChn ) ? "管理員" : " as an administrator";
				break;
			case SESS_TYP_WEBMASTER:
				$xtra = ( $rspChn ) ? "網站管理員" : " as a webmaster";
				break;
			default:
				$xtra = '';
			}
			$errMsg = ( $rspChn ) ? "沒有" . $xtra . " '{$escName}' 的登錄資料！"
								  : "No Record Found for '{$escName}'" . $xtra . "!";
			$_errCount++;
			$_errRec[] = $errMsg;
			return false;
		}
		$row = $rslt->fetch_all( MYSQLI_ASSOC )[0];
		if ( !password_verify( $escPass, $row[ 'UsrPass' ] ) ) {
			$errMsg = ( $rspChn ) ? "登錄失敗，密碼不合！" : "Login Failed; Password Mismatch!";
			$_errCount++;
			$_errRec[] = $errMsg;
			return false;
		}
		$rtnV[ 'usrEmail' ]  = $row[ 'UsrEmail' ];

		// login successfully, delete password reset token
		$sql = "DELETE FROM UsrRst WHERE `ID` = \"{$row[ 'ID' ]}\";";
		$rslt = $_db->query( $sql );
		if ( $_db->errno ) {
			$errMsg = ( $rspChn ) ? "資料庫內部錯誤!" : "DB internal error!";
			$_errCount++;
			if (!DEBUG) {
				$_errRec[] = $errMsg;
			} else {
				$_errRec[] = __FUNCTION__ . '() ' . __LINE__ . ":&nbsp;$_db->error;&nbsp;{$errMsg}";
			}
			return false;
		}

		return true;
	} // validateUser()

	function registerUser( $usrName, $usrPass, $usrEmail, $rspChn ) {
		global $_db, $_errCount, $_errRec;
		$escName = $_db->real_escape_string( $usrName );
		$escPass = $_db->real_escape_string( $usrPass );
		$escEmail = $_db->real_escape_string( $usrEmail );
		$hashed_pswd = password_hash( $escPass , PASSWORD_BCRYPT, [ 'cost' => HASH_COST ] );
		$_db->query("LOCK TABLES `Usr` WRITE, `inCareOf` WRITE;");
		$sql = "INSERT INTO Usr ( `UsrName`, `UsrPass`, `UsrEmail` ) " .
				"VALUES ( \"{$escName}\", \"{$hashed_pswd}\", \"{$escEmail}\" );";
		$_db->query( $sql );
		if ( $_db->errno ) {
			$_db->query("UNLOCK TABLES;");
			$errMsg = ( $rspChn ) ? "資料庫內部錯誤，<a href=\"mailto:library@amitabhalibrary.org\">請告知本館</a>"
								  : "DB Internal Error; <a href=\"mailto:library@amitabhalibrary.org\">Please Report</a>";
			$_errCount++;
			if ( !DEBUG ) {
				$_errRec[] = ( $rspChn ) ? "新用戶註冊失敗！" : "New user registration failed!";
			} else {
				$_errRec[] = __FUNCTION__ . '() ' . __LINE__ . ":&nbsp;$_db->error;&nbsp;{$errMsg}";
			}	
			return false;
		}
		$_db->query("DELETE FROM `inCareOf` WHERE `UsrName` = \"${escName}\";");
		$_db->query("UNLOCK TABLES;");
		return true;
	} // registerUser()
/*
	function authenticateSession( $rspChn ) { // session_start() has created or retrieved the Session
		// $_SESSION, $_COOKIE, and $_POST are the places where the login information exists
		$rtnV = array();
		if ( !isset( $_SESSION[ 'usrName' ] ) && !isset( $_COOKIE[ 'usrName' ] ) ) { // New Session
			return false;
		}
		if ( isset( $_SESSION[ 'usrName' ] ) && isset( $_COOKIE[ 'usrName' ] ) ) { // In-prog Session
			$myLogin = $_SESSION[ 'usrName' ];
			$myPass = $_SESSION[ 'usrPass' ];
			$sessType = $_SESSION[ 'sessType' ];
			return	( ( $_SESSION[ 'usrName' ] == $_COOKIE[ 'usrName' ] ) &&
								( $_SESSION[ 'usrPass' ] == $_COOKIE[ 'usrPass' ] ) &&
								validateUser( $myLogin, $myPass, $sessType, $rtnV, $rspChn ) );
		}
		if ( !isset( $_SESSION[ 'usrName' ] ) && isset( $_COOKIE[ 'usrName' ] ) ) { // LeftOver Session
			$myLogin = $_COOKIE[ 'usrName' ];
			$myPass = $_COOKIE[ 'usrPass' ];
			$sessType = $_COOKIE[ 'sessType' ];
			if ( ! validateUser( $myLogin, $myPass, $sessType, $rtnV, $rspChn ) ) {
				return false;
			}
			$_SESSION[ 'usrName' ] = $myLogin;
			$_SESSION[ 'usrPass' ] = $myPass;
			$_SESSION[ 'usrEmail' ] = $_COOKIE[ 'UsrEmail' ];
			$_SESSION[ 'sessType' ] = $sessType;
			return true;
		}
		if ( isset( $_SESSION[ 'usrName' ] ) && !isset( $_COOKIE[ 'usrName' ] ) ) { // ERROR
			return false;
		}
	} // authenticateSession()
*/
?>
