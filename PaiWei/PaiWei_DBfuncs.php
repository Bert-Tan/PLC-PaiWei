<?php
//
// Pai Wei management low level DB functions.
//
// Transaction Locks are secured by the caller
// $pwTupNVs: array of (AttrName, AttrVal) in associative array format
//
//	In our design / implementation, UPDATE and DELETE must provide the Key
//
$_errRec = array(); $_dupRec = array();
$_errCount = 0; $_dupCount = 0; $_insCount = 0;
$_updCount = 0; $_delCount = 0; $_totCount = 0;
$_srchCount = 0; $_srchRec = array();

function userSelectionList() {
	global $_db, $_errCount, $_errRec;
	global $useChn;
	$sql = "SELECT ID, usrName FROM Usr ORDER BY ID;";
	$sql1 = "SELECT ID, usrName FROM inCareOf ORDER BY ID;";
	$rslt = $_db->query( $sql );
	$rslt1 = $_db->query( $sql1 );
	if ( $_db->errno ) {
		if ( DEBUG ) {
			$_errRec[] = __FUNCTION__ . "()\t" . __LINE__ . ":\t{$_db->error} while executing: {$sql}\n";
		} else {
			$_errRec[] = ( $useChn ) ? "資料庫內部發生錯誤！" : "Database Internal Error!";
		}
		$_errCount++;
		return false;
	}
	$rows1 = $rslt1->fetch_all( MYSQLI_ASSOC );
	$rows = $rslt->fetch_all( MYSQLI_ASSOC );
	$rslt->free(); $rslt1->free();
	$tpl = new HTML_Template_IT("./Templates");
	$tpl->loadTemplatefile("userSelectionList.tpl", true, true);
	foreach( $rows as $row ) {
		$tpl->setCurrentBlock("selOption");  
		$tpl->setVariable( "usrName" , $row[ 'usrName' ] );
		$tpl->setVariable( "ID" , $row[ 'ID' ] );
		$tpl->parse("selOption");
	} // foreach loop
	foreach( $rows1 as $row ) {
		$tpl->setCurrentBlock("selOption");  
		$tpl->setVariable( "usrName" , $row[ 'usrName' ] );
		$tpl->setVariable( "ID" , $row[ 'ID' ] );
		$tpl->parse("selOption");
	} // foreach loop
	return $tpl->get();
} // userSelectionList()

function getPaiWeiTblFlds( $pwTable ) {
	global $_db;
	$fldN = array();
	
	$sql = "SHOW COLUMNS FROM {$pwTable};";
	$rslt = $_db->query($sql);
	$rows = $rslt->fetch_all(MYSQLI_ASSOC);
	foreach ( $rows as $row ){
		$fldN[] = $row [ 'Field' ];
	}	
	return $fldN;
} // getPaiWeiTblFlds() 

function searchPaiWeiTuple( $pwTable, $pwTupNVs, $usr ) {
	global $_db, $_errRec, $_errCount;
	global $_srchCount, $_srchRec;
	global $useChn;
	$srchCond = ""; $i = 0;
	foreach ( $pwTupNVs as $attrN => $attrV ) {
		if ( $i > 0 ) { $srchConds .= " AND "; }
		$srchCond .= "{$attrN} LIKE \"%{$attrV}%\"";
		$i++;
	}	// formulating Search Conditions
	
	$sql	= "SELECT * FROM {$pwTable} WHERE {$srchCond} AND ID IN "
				.	"(SELECT pwID FROM pw2Usr WHERE TblName = \"{$pwTable}\" AND pwUsrName = \"{$usr}\") "
				. "ORDER BY ID;"
				;

	$rslt = $_db->query( $sql );
	if ( $_db->errno ) {
		if ( DEBUG ) {
			$_errRec[] = __FUNCTION__ . "()\t" . __LINE__ . ":\t{$_db->error} while executing: {$sql}\n";
		} else {
			$_errRec[] = ( $useChn ) ? "資料庫內部發生錯誤！" : "Database Internal Error!";
		}
		$_errCount++;
		return false;
	} // error condition
	$_srchCount = $rslt->num_rows;
	if ( $_srchCount ) {
		$_srchRec = $rslt->fetch_all(MYSQLI_ASSOC);
	}
	return true;
} // searchPaiWeiTuple()

function updatePaiWeiTuple( $pwTable, $pwTupNVs, $usr ) {
	global $_db, $_errRec, $_updCount, $_errCount;
	global $useChn;
	$tupID = array_key_exists( 'ID' , $pwTupNVs ) ? $tupID = $pwTupNVs[ 'ID' ] : null;	
	
	if ( $tupID == null ) {
		if ( DEBUG ) {
			$_errRec[] = __FUNCTION__ . "()\t" . __LINE__ . ":\tERROR: Must provide the key for Update\n";
		} else {
			$_errRec[] = ( $useChn ) ? "軟體內部發生錯誤！" : "Software Internal Error!";
		}
		$_errCount++;
		return false;
	} // error condition
	
	$updParams = ""; $i = 0;
	foreach ( $pwTupNVs as $attrN => $attrV ) {
		if ( $i > 0 ) { $updParams .= ", "; }
		if ( $attrN != 'ID' ) {
			$updParams .= "`{$attrN}` = \"{$attrV}\"";
			$i++;
		}
	} // loop through attribute (Name, Value) pairs
	$sql = "UPDATE {$pwTable} SET {$updParams} WHERE ID = \"{$tupID}\";";
	$_db->query( $sql );
	if ( $_db->errno ) {
		if ( DEBUG ) {
			$_errRec[] = __FUNCTION__ . "()\t" . __LINE__ . ":\t{$_db->error} while executing: {$sql}\n";
		} else {
			$_errRec[] = ( $useChn ) ? "資料庫內部發生錯誤！" : "Database Internal Error!";
		}
		$_errCount++;
		return false;
	} // error condition
	// No other tables involved in UPDATE
	$_updCount++;
	return $_updCount;	
} // updatePaiWeiTuple ()

function deletePaiWeiTuple( $pwTable, $pwTupNVs, $usr ) {
	global $_db, $_errCount, $_errRec, $_delCount;
	global $useChn;
	$tupID = null;	
	if ( array_key_exists( 'ID' , $pwTupNVs ) ) {
		$tupID = $pwTupNVs[ 'ID' ];
	}
	if ( !$tupID ) {
		if ( DEBUG ) {
			$_errRec[] = __FUNCTION__ . "()\t" . __LINE__ . ":\tERROR: Must provide the key for deletion\n";
		} else {
			$_errRec[] = ( $useChn ) ? "軟體內部發生錯誤！" : "Software Internal Error!";
		}
		$_errCount++;
		return false;
	} // error condition

	$sql = "DELETE FROM pw2Usr WHERE TblName = \"{$pwTable}\" AND pwID = \"{$tupID}\" AND pwUsrName = \"{$usr}\";";
	$rslt = $_db->query( $sql );
	if ( $_db->errno ) {
		if ( DEBUG ) {
			$_errRec[] = __FUNCTION__ . "()\t" . __LINE__ . ":\t{$_db->error} while executing: {$sql}\n";
		} else {
			$_errRec[] = ( $useChn ) ? "資料庫內部發生錯誤！" : "Database Internal Error!";
		}
		$_errCount++;
		return false;
	} // error condition
	$sql = "DELETE FROM {$pwTable} WHERE `ID` = \"{$tupID}\";";
	$rslt = $_db->query( $sql );
	if ( $_db->errno ) {
		if ( DEBUG ) {
			$_errRec[] = __FUNCTION__ . "()\t" . __LINE__ . ":\t{$_db->error} while executing: {$sql}\n";
		} else {
			$_errRec[] = ( $useChn ) ? "資料庫內部發生錯誤！" : "Database Internal Error!";
		}
		$_errCount++;
		return false;
	} // error condition
	$_delCount++;
	return true;
} // deletePaiWeiTuple ()

function insertPaiWeiTuple( $pwTable, $pwTupNVs, $usr, $recNo ) {
	global $_db, $_insCount, $_dupCount, $_errCount;
	global $_errRec, $_dupRec, $useChn;

	$tupAttrX = array();
	$tupValX = array ();
	$tupID = null;
	if ( $recNo == null ) { $recNo = "<New Tuple>"; }
	
	// formulate PaiWei Table query conditions
	$qryCond = ""; $i = 0;
	foreach ( $pwTupNVs as $attrN => $attrV ) { // formulate qry conditions, attribute list, value list
		if ( $attrV == '' ) { continue; }
		if ( $i > 0 ) { $qryCond .= " AND "; }
		$qryCond .= " `{$attrN}` = \"{$attrV}\" ";
		$tupAttrX[] = $attrN;
		$tupValX[] = $attrV;
		$i++;
	}
	$tupAttrs = "( " . implode( ', ', $tupAttrX ) . " )";
	$tupVal = "( \"" . implode( "\", \"", $tupValX ) . "\" )";

	// check existence in the Pai Wei Table
	$sql = "SELECT `ID` FROM $pwTable WHERE {$qryCond};";
	$rslt = $_db->query( $sql );
	if ( $_db->errno ) {
		if ( DEBUG ) {
			$_errRec[] = __FUNCTION__ . "()\t" . __LINE__ . ":\t{$_db->error} on Rec. No. '{$recNo}' while executing: {$sql}\n";
		} else {
			$_errRec[] = ( $useChn ) ? "資料庫內部發生錯誤！" : "Database Internal Error!";
		}
		$_errCount++;
		return false;
	} // error condition

	switch ( $rslt->num_rows ) { // should be 0 or 1
		case 0;
			break;
		case 1:
			$tupID = $rslt->fetch_all(MYSQLI_ASSOC)[0][ 'ID' ];
			break;
		default:
			if ( DEBUG ) {
				$_errRec[] = __FUNCTION__ . "()\t" . __LINE__ . ":\tERROR: {$rslt->num_rows} found in Table `{$pwTable}`\n";	
			} else {
				$_errRec[] = ( $useChn ) ? "資料庫內部發生錯誤！" : "Database Internal Error!";
			}
			$_errCount++;
			return false;
	} // switch()

	if ( !$tupID ) {
		$sql = "INSERT INTO {$pwTable} {$tupAttrs} VALUE {$tupVal};";
		$rslt = $_db->query( $sql );
		if ( $_db->errno ) {
			if ( DEBUG ) {
				$_errRec[] = __FUNCTION__ . "()\t" . __LINE__ . ":\t{$_db->error} Rec. No. '{$recNo}' while executing: {$sql}\n";				
			} else {
				$_errRec[] = ( $useChn ) ? "資料庫內部發生錯誤！" : "Database Internal Error!";
			}
			$_errCount++;
			return false;
		}
		$tupID = $_db->insert_id;		
	}

	/*
	 * Tuple exists or just inserted in the PaiWei Table; now taking care of the pw2Usr table
	 */
	$sqlCond = "`TblName` = \"{$pwTable}\" AND `pwID` = \"{$tupID}\" AND `pwUsrName` = \"{$usr}\"";
	$sql = "SELECT * FROM pw2Usr WHERE {$sqlCond};";
	$rslt = $_db->query( $sql );
	if ( $_db->errno ) {
		if ( DEBUG ) {
			$_errRec[] = __FUNCTION__ . "()\t" . __LINE__ . ":\t{$_db->error} Rec. No. '{$recNo}' while executing: {$sql}\n";
		} else {
			$_errRec[] = ( $useChn ) ? "資料庫內部發生錯誤！" : "Database Internal Error!";
		}
		$_errCount++;
		return false;
	}
	switch ( $rslt->num_rows ) { // Should be 0 or 1
	case 0: // Not in pw2Usr Table; insert now
		$sql	= "INSERT into pw2Usr ( `TblName`, `pwID`, `pwUsrName` ) "
					.	"VALUE ( \"{$pwTable}\", \"{$tupID}\", \"{$usr}\" );";
		$rslt = $_db->query( $sql );
		if ( $_db->errno ) {
			if ( DEBUG ) {
				$_errRec[] = __FUNCTION__ . "()\t" . __LINE__ . ":\t{$_db->error} Rec. No. '{$recNo}' while executing: {$sql}\n";
			} else {
				$_errRec[] = ( $useChn ) ? "資料庫內部發生錯誤！" : "Database Internal Error!";
			}
			$_errCount++;
			return false;
		}
		$_insCount++;
		return $tupID;
	case 1:
		$_debugMsg = ( DEBUG ) ? __FUNCTION__ . "()\t" . __LINE__ . ": " : '';
		$_dupMsg = ( $useChn )  ? "{$usr} 的牌位資料 {$tupVal} 已經在資料庫中。" 
								: "{$tupVal} for User {$usr} already in the database!";
		$_dupRec[] = $_debugMsg . $_dupMsg;
		$_dupCount++;
		return false;
		break;
	default: // error condition: tuple indexed ==> should only be 0 or 1
		$_debugMsg = ( DEBUG ) ? __FUNCTION__ . "()\t" . __LINE__ . ": " : '';
		$_errMsg = ( $useChn )  ? "資料庫有錯誤！已有 {$rslt->num_rows} 份同樣的牌位資料！"
								: "ERROR! {$rslt->num_rows} records found in the database.";
		$_errRec[] = $_debugMsg . $_errMsg;
		$_errCount++;
		return false;		
	} // switch on num_rows				
}	// insertPaiWeiTuple ()

?>