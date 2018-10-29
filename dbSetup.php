<?php
/*
 * File location:		http://www.amitabhalibrary.org/admPages/DBsetup.php
 */

	if ( ! isset( $_db ) ) {
		require_once ( 'dbConstants.php' ); /* in the same directory */
		
		$_db = new mysqli ( DBHOST, DBUSER, DBPASS, DBNAME );
		if ( $_db->errno ) {
			die( "MySQL Database connection failed; $db->errno <br/><br/>" );
		}

		$_db->query("SET NAMES 'UTF8'"); /* to query on UTF-8 encoded Chinese characters */
		$_db->query("SET character_set_results = 'utf8', character_set_client = 'utf8',
							character_set_connection = 'utf8', character_set_database = 'utf8',
							character_set_server = 'utf8', default_storage_engine='INNODB'");
		if ( $_db->errno ) {
			die( "Setting connection Character Set & Collation failed; $db->errno <br/><br/>" );		
		}
	}
	
?>
