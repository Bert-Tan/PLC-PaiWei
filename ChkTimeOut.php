<?php
	/*
	 * Included after pgConstants.php
	 */
	$now = $_SERVER[ 'REQUEST_TIME' ]; // when the browser sent the request
	session_start(); // Retrieve the existing session; or create a new session
	if ( !isset( $_SESSION[ 'usrName' ] ) ) {
		/*
		 * Because this is an included script, the only way to exit nicely is to use goto
		 */
		goto EndOfChkTimeOut;
	}
	
	if ( ( $now - $_SESSION[ 'LAST_ACTIVITY' ] ) > IDLE_THRESHOLD ) {
		$hdrURL = URL_ROOT . "/admin/index.php?r=e"; // login reason: session expiration
		/*
		 * User session expired; must re-login
		 */
		$_inclFile = basename( $_SERVER[ 'PHP_SELF' ] );
		switch ( $_inclFile ) {
		case "ajax-pwDB.php":
		case "upldPaiWeiForm.php":  // including script is serving AJAX
			// Tell Ajax to force re-login
			$rpt[ 'URL' ] = $hdrURL;
			echo json_encode(  $rpt , JSON_UNESCAPED_UNICODE );
			$_db->close();
			exit;
		case "UG.php": // do not perform timeout force relogin
			goto EndOfChkTimeOut;
		} // switch
		// Other including scripts
    	session_destroy();
    	header( "location: " . $hdrURL );
	}

EndOfChkTimeOut:
	$_SESSION[ 'LAST_ACTIVITY' ] = $now;	// Record this time as the last activity timestamp
?>