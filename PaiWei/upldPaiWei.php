<?php
/*
 * Special note for the uploaded CSV file processing:
 *
 * The PaiWei CSV files produced from MS EXCEL have the following properties:
 *	(1) Fields not enclosed by double quotes: if the data does not have ',' in it; so, ',' delimits well
 *	(2) Fields enclosed by double quotes:	if the data itself has ',' in it; therefore, ',' can delimit
 *
 * For the above reason, the PHP's csv functions won't work well because they require a consistent
 * delimiting / enclosing character.
 *
 * The function parseCSV_XLS() is written for this purpose; it parse each line field-by-field
 * according to the above characteristics.
 */

	require_once( '../pgConstants.php' );
	require_once( 'dbSetup.php' );
	require_once( 'PaiWei_DBfuncs.php');
	require_once( 'chkDeceasedDate.php' );
		
	$_rpt = array();
	$_totCount = 0;
	$_blnkCount = 0;
	$_retreatDate = "2019-03-31"; // Can be read from the DB
	$_retreatDateMinus1Yr = date("Y-m-d", strtotime( $_retreatDate . " -1 year" ) );
	
	function removeBOM( $str="" ) { 
	  if(substr($str, 0, 3) == pack("C*", 0xef,0xbb,0xbf)) { 
	    $str=substr($str, 3); 
	  } 
	  return $str;
	}

	function parseCSV_XLS ( $line, &$rtnV ) {
		global $_errCount, $_errRec, $_rpt;
		$scratch = array();
		$remainder = '';
		
		// Use named RegEx Capturing Group;
		// Pattern 1: Part 1 is enclosed by '"', followed by ',' or EOS '$'
		$pattern1 = '%^"(?<part1>[^"]*?)"(,|$)(?<remainder>.*)$%';
		// Pattern 2: Part 1 is delimited by ',' or EOS '$'
		$pattern2 = '%^(?<part1>.*?)(,|$)(?<remainder>.*)$%';

		$line = preg_replace( '%(\r\n|\r|\n)%', '', removeBOM ( $line ) ); // rmv BOM & EOL
		if ( preg_match( '%,,%', $line ) === 1 ) { // null fields
			$line = preg_replace( '%,,%', ',NULLFLD,', $line );	
		}
		if ( preg_match( '%^,%', $line ) === 1) { // null field at the beginning of the line
			$line = preg_replace( '%^,%', 'NULLFLD,', $line );
		}
		if ( preg_match( '%,$%', $line ) === 1) { // null field at the end of the line
			$line = preg_replace( '%,$%', ',NULLFLD', $line );
		}
		
		$remainder = $line;
		while ( strlen( $remainder ) > 0 ) {
			if ( strpos( $remainder, '"' ) === 0 ) {
				preg_match( $pattern1, $remainder, $scratch, PREG_OFFSET_CAPTURE );
			} else {
				preg_match( $pattern2, $remainder, $scratch, PREG_OFFSET_CAPTURE );
			}
		
			if ( !isset( $scratch[ 'part1' ] ) ) {
				$_errCount++;
				$_errRec[] = __FUNCTION__ . "() Line " . __LINE__ . ":\tCSV Format Error on Record {$_totCount}; content:\t\"{$line}\"\n";
				return false;
			}
			
			$x = $scratch[ 'part1' ][0];
			$rtnV[] = ( preg_match( '%NULLFLD%', $x ) === 1 ) ? '' : $x;
			if ( !isset( $scratch[ 'remainder' ] ) || ( strlen( $scratch[ 'remainder' ][0] ) == 0 ) )	{
				break; // nothing remains
			}
			$remainder = $scratch[ 'remainder' ][0];
		} // while
		return true;
	} // parseLineCSV_XLS()

  $_pgTime = time(); // NOW!
  $_pgDate = Date( DateFormatArchive );
	$_myDir = dirname ( __FILE__ );
	session_start();
	
	if ( !isset( $_SESSION[ 'usrName' ] ) ) {
		header( "location: " . '../Login/Login.php' );
		exit;
	}

	$_sessType = $_SESSION[ 'sessType' ];
	$_sessLang = $_SESSION[ 'sessLang' ];
	$_sessUsr = $_SESSION[ 'usrName' ];

	$_tblName = $_POST[ 'dbTblName' ];
	$_ajaxRpt = isset($_POST[ 'ajaxRpt' ] ) ? true : false;
	$_tmpDataFile = $_FILES["upldedFiles"]["tmp_name"];
	$_archiveDir = ARCHIVEDIR . "/uploads";
	$_pathAttr = pathinfo( $_FILES["upldedFiles"]["name"] );
	$_fileBase = $_pathAttr [ 'filename' ];
	$_fileExt = $_pathAttr [ 'extension' ];
	$_fileDIR = $_pathAttr [ 'dirname' ];
	$_archiveName = $_archiveDir . "/" . $_fileBase . '.' . $_fileExt . '_' . $_pgDate . '_for_' . $_tblName;

	if ( ( $_fileExt !== 'csv' && $_fileExt !== 'CSV' ) || 
			 ( mb_check_encoding (file_get_contents( $_tmpDataFile ), 'UTF-8') == false ) ) {
		$_errCount++;
		$_errRec[] = "Only CSV (Comma Separately Values) File encoded in UTF-8 is supported!";
		exit();
	}

	ini_set("auto_detect_line_endings", true); // CRLF (Windows), CR (Mac), or LF (Linux)
	$_fh = fopen ( $_tmpDataFile, "r");
	
	fgets( $_fh ); // skip the first line in file, which is the column titles in Chinese
	unset( $_tupFldNs ); $_tupFldNs = array();
	$line = fgets( $_fh );	// tuple attribute names
	if ( ! parseCSV_XLS( $line, $_tupFldNs ) ) {
		goto EndRpt;
	}
	$_attrErr = false;
	while ( ! feof ( $_fh ) ) { // for each line / tuple
		$line = fgets( $_fh );
		$_totCount++;
		if ( preg_match( "%^[\s,]*$%", $line ) == 1 ) {
			$_blnkCount++;
			continue; // skip a blank line
		}
		unset( $_tupFldVs ); $_tupFldVs = array();
		unset( $_tupAttrNVs ); $_tupAttrNVs = array(); // ( Name, Value) pairs in an associative array format
		if ( !parseCSV_XLS( $line, $_tupFldVs ) ) {
			$_errCount++;
			$_errRec[] = __FUNCTION__ . "() Line " . __LINE__ . ":\tCSV Format Error on Record {$_totCount}; content:\t\"{$line}\"\n";
			continue; // skip the line
		}
		for ( $i = 0; $i < sizeof( $_tupFldVs ); $i++ ) {
			$_attrN = trim($_tupFldNs[$i]);
			$_attrV = trim($_tupFldVs[$i]);
			if ( ( $_attrN == 'deceasedDate' ) && !chkDeceasedDate( $_retreatDate, $_attrV ) ) {
				$_errCount++;
				$_errRec[] = __FUNCTION__ . "() Line " . __LINE__ . ":\tError on Record {$_totCount}; Deceased Date must be after {$_retreatDateMinus1Yr}!";
				$_attrErr = true;
				break;
			}
			$_tupAttrNVs[ $_attrN ] = $_attrV; // this particular attribute's (Name, Value)
		} // formulate tuple attribute's (Name, Value) pairs in associative array format
		if ( $_attrErr ) {
			$_attrErr = false; // reset it
			continue; // skip the line
		}
		$_db->autocommit(false);
		$_db->query("LOCK TABLES $_tblName WRITE;");
		$_db->begin_transaction(MYSQLI_TRANS_START_WITH_CONSISTENT_SNAPSHOT);
		if ( ! insertPaiWeiTuple( $_tblName, $_tupAttrNVs, $_sessUsr, $_totCount ) ) {
			$_db->rollback();
			$_db->query("UNLOCK TABLES;");			
			continue; // error or dup conditions, skip to the next tuple
		}
		$_db->commit();
		$_db->query("UNLOCK TABLES;");	
		$_db->autocommit(true);
	} // each input data line / tuple

EndRpt:	
	$_rpt [ 'upldStat' ] = "\tTotal Upload Request: {$_totCount} entries;\tTotal Inserted: {$_insCount}\n";
	if ( $_blnkCount ) {
		$_rpt [ 'blnkCount' ] = "\t{$_blnkCount} Records are blank and NOT inserted\n";
	}
	if ( $_dupCount ) {
		$_rpt [ 'dupStat' ] = "\t{$_dupCount} Records are duplicates and NOT inserted\n";
		$_rpt [ 'dupRec' ] = $_dupRec;		
	}
	if ( $_errCount ) {
		$_rpt [ 'errStat' ] = "\t{$_errCount} Records encountered errors and were skipped\n";
		$_rpt [ 'errRec' ] = $_errRec;		
	}
	print_r ( $_rpt );

	/*
	 * Archiving the uploaded data file
  if ( ( ! file_exists ( $_archiveDir ) ) &&  ( ! mkdir ( $_archiveDir ) ) ) {
  			die ( "Error archiving uploaded file - CANNOT create archive folder: $_archiveDir <br/>" );
  }
  
  if ( ! move_uploaded_file( $_tmpDataFile, $_archiveName ) ) {
  			die ( "Error archiving uploaded file - CANNOT create archive folder: $_archiveDir <br/>" );
  }
  
  echo "The file " . basename( $_FILES[0]["upldFile"]["name"]). " has been archived.<br/>";
	 */
?>