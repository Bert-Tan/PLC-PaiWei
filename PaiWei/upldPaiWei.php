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
		
	$_rptMsg = '';
	$_totCount = 0;
	$_blnkCount = 0;
	define ( 'BLANKDATA', "BLANK"); // filler for allowed blank field
	$_rqTitles = array( 'D_Requestor', 'L_Requestor', 'Y_Requestor' ); // 'W_Requestor' is handled specially
	// 'W_Title' to add 叩薦 to 'W_Requestor'
	$_koujianWTitles = array( '先慈父', '先慈母', '先慈公公', '先慈婆婆', '先慈岳父', '先慈岳母', 
							'先祖父', '先祖母', '先外祖父', '先外祖母', '老師', 'Father', 
							'Mother', 'Grandpa', 'Grandma', 'Father-in-Law', 'Mother-in-Law' );
	
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
		header( "location: " . '../index.php' );
		exit;
	}

	$_sessType = $_SESSION[ 'sessType' ];
	$useChn = ( $_SESSION[ 'sessLang' ] == SESS_LANG_CHN );
	$_sessUsr = $_SESSION[ 'usrName' ];
	$_pwOwner = ( isset( $_SESSION[ 'icoName' ] ) ) ? $_SESSION[ 'icoName' ] : $_sessUsr;
	$_tblName = $_POST[ 'dbTblName' ];
	$_ajaxRpt = isset($_POST[ 'ajaxRpt' ] ) ? true : false;
	$_tmpDataFile = $_FILES["upldedFiles"]["tmp_name"];
	$_archiveDir = ARCHIVEDIR . "/uploads";
	$_pathAttr = pathinfo( $_FILES["upldedFiles"]["name"] );
	$_fileBase = $_pathAttr [ 'filename' ];
	$_fileExt = $_pathAttr [ 'extension' ];
	$_fileDIR = $_pathAttr [ 'dirname' ];
	$_archiveName = $_archiveDir . "/" . $_fileBase . '.' . $_fileExt . '_' . $_pgDate . '_for_' . $_tblName;
	$_koujianStr = ( $useChn ) ? " 叩薦" : " Sincerely Recommend";
	$_jingjianStr = ( $useChn ) ? " 敬薦" : " Recommend";

	if ( ( $_fileExt !== 'csv' && $_fileExt !== 'CSV' ) || 
			 ( mb_check_encoding (file_get_contents( $_tmpDataFile ), 'UTF-8') == false ) ) {
		$_errCount++;
		$_errRec[] = ( $useChn) ? "本網站上載只支持用 UTF-8 編碼的檔案！"
								: "Only CSV (Comma Separately Values) File encoded in UTF-8 is supported!";
		goto EndRpt;
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
			$_errRec[] = ( $useChn ) ? "第 {$_totCount} 行：『逗號分隔值』/ (CSV) 資料格式有錯誤；\"{$line}\""
								: ":\tCSV Format Error on Record {$_totCount}; content:\t\"{$line}\"";
			continue; // skip the line
		}
		for ( $i = 0; $i < sizeof( $_tupFldVs ); $i++ ) {
			$_attrN = trim($_tupFldNs[$i]);
			$_attrV = trim($_tupFldVs[$i]);
			if ( $_attrN == 'deceasedDate' ) {
				if ( !chkDate( $_attrV ) ) {
					$_errCount++;
					$_errRec[] = ( $useChn ) ? "第 {$_totCount} 行：往生日必須介於&nbsp;\"{$_SESSION[ 'pwPlqDate' ]}\"&nbsp;與&nbsp;\"{$_SESSION[ 'rtrtDate' ]}\"&nbsp;之間！"
							: ":\tError on Record {$_totCount}; Deceased Date must be a valid date between "
							. "\"{$_SESSION[ 'pwPlqDate' ]}\" and \"{$_SESSION[ 'rtrtDate' ]}\"!";
					$_attrErr = true;
					break;
				}
				else { // convert `deceasedDate` to YYYY-MM-DD format
					$_attrV = date("Y-m-d", strtotime($_attrV));
				}								
			} // attribute is deceasedDate
			if ( preg_match( "%^\s*$%", $_attrV ) == 1 ) { // Field value is empty
				if ( $_attrN != 'W_Title' && $_attrN != 'R_Title' ) {
					$_errCount++;
					$_errRec[] = ( $useChn ) ? "第 {$_totCount} 行：資料不完整！"
											 : "Error on Record {$_totCount}; incomplete data!";
					$_attrErr = true; // no field can be empty except these two
					break;
				} 
				$_attrV = BLANKDATA; // field is either W_Title or R_Title, for which blank is allowed
			} // Field value is empty
			if ( in_array( $_attrN, $_rqTitles ) ) { // make 叩薦 or 敬薦 consistent (except for 'W_Requestor')
				$_toDel = "%\s*(叩薦|Sincerely Recommend|敬薦|Recommend)%u";
				$_attrV = preg_replace( $_toDel, '', $_attrV ); // if they are there, delete it
				switch( $_attrN ) {
				case 'D_Requestor':
				case 'Y_Requestor':
					$_toAdd = $_jingjianStr;
					break;
				case 'L_Requestor':
					$_toAdd = $_koujianStr;	// default
					break;
				} // switch()
				$_attrV = preg_replace( "%$%", $_toAdd, $_attrV );
			} // End of taking care of 叩薦 or 敬薦
			if ( $_attrN == 'W_Requestor' ) {
			// handle 叩薦 or 敬薦 for 'W_Requestor', the logic is below:
			// (1) if 叩薦 or 敬薦 is there, keep it
			// (2) otherwise, add 叩薦 or 敬薦 based on 'W_Title'
				$_toMatch = "%\s*(叩薦|Sincerely Recommend|敬薦|Recommend)%u";
				if ( preg_match( $_toMatch, $_attrV ) != 1 ) { // not found
					if ( in_array( $_tupFldVs[0], $_koujianWTitles ) ) { // $_tupFldVs[0] is the value of 'W_Title'
						$_toAdd = $_koujianStr;
					}
					else {
						$_toAdd = $_jingjianStr;
					}
					$_attrV = preg_replace( "%$%", $_toAdd, $_attrV );
				}
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
		if ( ! insertPaiWeiTuple( $_tblName, $_tupAttrNVs, $_pwOwner, $_totCount ) ) {
			$_db->rollback();
			$_db->query("UNLOCK TABLES;");			
			continue; // error or dup conditions, skip to the next tuple
		}
		$_db->commit();
		$_db->query("UNLOCK TABLES;");	
		$_db->autocommit(true);
	} // each input data line / tuple

EndRpt:
	$_rptMsg = ( $useChn ) 	? "上載 {$_fileBase}.{$_fileExt} 檔案資料匯總報告\n\n"
							: "Status of Uploading {$_fileBase}.{$_fileExt}\n\n";
	$_rptMsg .= ( $useChn ) ? "總共資料行數：{$_totCount}；確實上載資料行數：{$_insCount}"
						  : "Total Upload Request: {$_totCount} entries;\tTotal Inserted: {$_insCount}";
	if ( $_blnkCount > 0 ) {
		$_rptMsg .= "\n\n";
		$_rptMsg .= ( $useChn ) ? "有 {$_blnkCount} 行資料是空白；沒有上載！"
						   : "{$_blnkCount} Records are blank and NOT inserted!";
	} // $_blnkCount > 0
	if ( $_dupCount > 0 ) {
		$_rptMsg .= "\n\n";
		$_rptMsg .= ( $useChn ) ? "下列 {$_dupCount} 行資料為重復；沒有上載！"
							   : "{$_dupCount} Records are duplicates and NOT inserted!";
		$lineNbrg = ($_dupCount > 1);
		for ( $i = 0; $i < $_dupCount; $i++ ) {
			$lineBreak = ( strlen( $_rptMsg ) > 0 ) ? "\n" : '';
			$lineNbr = "[ " . ($i + 1) . " ] ";
			$_rptMsg .= $lineBreak . ( $lineNbrg ? $lineNbr : '' ) . $_dupRec[ $i ];
		}
	} // $_dupCount > 0
	if ( $_errCount > 0 ) {
		$_rptMsg .= "\n\n";
		$_rptMsg .= ( $useChn ) ? "有 {$_errCount} 行資料有錯誤；沒有上載！"
							   : "{$_errCount} Records encountered errors and were skipped!";
		$lineNbrg = ($_errCount > 1);
		for ( $i = 0; $i < $_errCount; $i++ ) {
			$lineBreak = ( strlen( $_rptMsg ) > 0 ) ? "\n" : '';
			$lineNbr = "[ " . ($i + 1) . " ] ";
			$_rptMsg .= $lineBreak . ( $lineNbrg ? $lineNbr : '' ) . $_errRec[ $i ];
		}		
	} // $_errCount > 0
	echo $_rptMsg;
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