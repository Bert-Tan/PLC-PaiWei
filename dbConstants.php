<?php
/*
 * Database Constants
 */

	$_os = strtoupper( substr( PHP_OS, 0, 3 ) ); 
	switch ( $_os ) {
		case 'DAR': /* Mac */
				DEFINE ( 'DBHOST', 'localhost' );
				DEFINE ( 'DBUSER', 'root' );
				DEFINE ( 'DBPASS', 'bert0624' );
				DEFINE ( 'DBNAME', 'amitabha_adminDB' );
				break;
		case 'LIN': /* Linux - Web Server*/
				DEFINE ( 'DBHOST', 'localhost' );
				DEFINE ( 'DBUSER', 'amitabha_admin' );
				DEFINE ( 'DBPASS', 'admin@9941' );
				DEFINE ( 'DBNAME', 'amitabha_adminDB' );
				break;
	}

?>