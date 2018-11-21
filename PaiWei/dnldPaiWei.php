<?php
	require_once("../pgConstants.php");
	require_once("dbSetup.php");
	require_once("PaiWei_DBfuncs.php");

	$_blankData = "(空白|BLANK)";
	$_blank = "%" . $_blankData . "%"; // regExp to blank out the blank data field		
	$_tblName = $_POST [ 'dbTblName' ];
	
	$_tblFlds = getPaiWeiTblFlds( $_tblName ); // $_tblFlds[0] contains ID which is not needed

	switch ( $_tblName ) {
		case 'W001A_4':
			$_selFlds = "concat( ifnull($_tblFlds[1], ''), ' ', ifnull($_tblFlds[2], '') ) AS $_tblFlds[2], "
								. "concat( ifnull($_tblFlds[3], ''), ' ', ifnull($_tblFlds[4], '') ) AS $_tblFlds[4]  ";
			break;
		case 'DaPaiWei':
			$_selFlds = "concat( ifnull($_tblFlds[1], ''), ' ', ifnull($_tblFlds[2], '') ) AS $_tblFlds[2], "
								. "concat( ifnull($_tblFlds[4], ''), ' ', ifnull($_tblFlds[5], '') ) AS $_tblFlds[5]  ";
			break;	
		default:
			$_selFlds = implode( ', ', array_slice( $_tblFlds, 1 ) );
			break;
	} // switch on $_tblName

	if ( $_POST[ 'dnldUsrName' ] == 'ALL' ) {
		$_sql = "SELECT $_selFlds FROM {$_tblName} ORDER BY ID;";
	} else {
		$_dnldUsrName = $_POST[ 'dnldUsrName' ];
		$_sql	= "SELECT {$_selFlds} FROM {$_tblName} WHERE ID IN "
					.	"(SELECT pwID FROM pw2Usr WHERE TblName = \"{$_tblName}\" AND pwUsrName = \"{$_dnldUsrName}\") "
					. "ORDER BY ID;"
					;
	}

	$_rslt = $_db->query( $_sql );
	$_Rows = $_rslt->fetch_all ( MYSQLI_NUM );
	$_db->close();
	$_lineCount = $_rslt->num_rows;
	chdir( ARCHIVEDIR );
	$_BOM = pack("C*", 0xef,0xbb,0xbf);
	$_FP = fopen( $_tblName . ".txt", "w"); // Temp file handle
	fputs( $_FP, $_BOM ); // write the BOM character which is expected by the PaiWei printing SW
	foreach ( $_Rows as $_Row ) { // write to the file line-by-line
		$_Line = '"' . implode( "\"\t\"", $_Row ) . '"';
		$_Line = preg_replace( $_blank, '', $_Line );
		if ( --$_lineCount > 0 ) $_Line .= "\r\n";
		fputs( $_FP, $_Line );
	}
	fclose( $_FP );
	
	$_DldName = $_tblName . '.txt';
	$_ContentType = "application/octet-stream; charset=utf-8";
	
	// Force download
	header( "Content-Disposition: attachment; filename=\"{$_DldName}\"");
	header( "Content-Type: {$_ContentType}" );// header("Content-Transfer-Encoding: Binary");
	header( "Content-Length: " . filesize( $_DldName ) );
	readfile( $_DldName );

?>