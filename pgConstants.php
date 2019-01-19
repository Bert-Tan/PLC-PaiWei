<?php
/*
 * Fix PHP include_path to include PEARS, PDF, etc.
 */
  
  if ( php_sapi_name() == 'cli' ) {
  	$_SERVER [ 'DOCUMENT_ROOT' ] = "/Users/berttan/MacPLC";
  }
  
	$_os = strtoupper( substr( PHP_OS, 0, 3 ) ); 
	switch ( $_os ) {
		case 'DAR': /* Mac */
			  error_reporting( E_ALL );
				DEFINE ( 'URL_ROOT', 'http://www.localplc.org' );
				DEFINE ( 'DOCU_ROOT', $_SERVER[ "DOCUMENT_ROOT" ] );
				DEFINE ( 'ADM_ROOT', '/Users/berttan/MacPLC/admin' );
				DEFINE ( 'PEAR_INCL', '/Users/berttan/pear/share/pear' );
				DEFINE ( 'PDF_INCL', '/Library/WebServer/Documents/tcpdf');
				DEFINE ( 'ARCHIVEDIR', '/Users/berttan/MacPLC/Archive' );
				DEFINE ( 'IDLE_THRESHOLD', 3600 ); // Session idle time
				DEFINE ( 'DEBUG', true );
				break;
		case 'LIN': /* Linux - Web Server*/
			  error_reporting( E_ALL & ~E_NOTICE & ~E_STRICT & ~E_DEPRECATED & ~E_WARNING );
				DEFINE ( 'URL_ROOT', 'https://www.amitabhalibrary.org' );
				DEFINE ( 'DOCU_ROOT', $_SERVER[ "DOCUMENT_ROOT" ] );
				DEFINE ( 'ADM_ROOT', '/home/amitabha/public_html/admin' );
				DEFINE ( 'PEAR_INCL', '/home/amitabha/php' );
				DEFINE ( 'PDF_INCL', '/home/amitabha/tcpdf' );
				DEFINE ( 'ARCHIVEDIR', '/home/amitabha/Archive' );
				DEFINE ( 'IDLE_THRESHOLD', 1800 );
				DEFINE ( 'DEBUG', false );
				break;
	}

/*
 * Session Types
 */
	DEFINE ( 'SESS_TYP_USR', 0 );	// General User
	DEFINE ( 'SESS_TYP_MGR', 1 );				// Administrator
	DEFINE ( 'SESS_TYP_WEBMASTER', 2 );	// Webmaster
	DEFINE ( 'SESS_LANG_ENG', 0 );			// Session UI in English
	DEFINE ( 'SESS_LANG_CHN', 1 );			// Session UI in Chinese

/*
 * Set $PATH correctly so all include() and require_once() work smoothly
 */
	set_include_path( get_include_path()
									. ':' . "./Templates"
									. ':' . ADM_ROOT
									. ':' . ADM_ROOT . "/includes"
									. ':' . ADM_ROOT . "/Mailer"
									. ':' . PEAR_INCL
									. ':' . PDF_INCL
									);
									
	require_once ( 'HTML/Template/IT.php' );

/*
 * Set Timezone & Date format: <Month> <Day>, <Year> for writing a letter; YYYY-MM-DD for MySQL
 */
	date_default_timezone_set( 'America/Chicago' );
	DEFINE ( 'DateFormatLtr', "F j\, Y"); // or "F j\, Y h:i:s A"
	DEFINE ( 'DateFormatArchive', "M-d\-Y H\:i\:s" );
	DEFINE ( 'DateFormatSQL', "Y-m-d H:i:s"); // YYYY-MM-DD HH:MM:SS

/*
 * Tell PHP the default Character encoding is UTF-8
 * UTF-8 is also set for the DB data manipulation in the dbSetup.php
 * It is important to be able to handle UTF-8 encoded Chinese characters
 */
	ini_set('default_charset', 'UTF-8');
	
?>
