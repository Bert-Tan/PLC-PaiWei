<?php
$_delCount = 0; $_insCount = 0; $_errCount = 0; $_errRec = array();

function getDBTblFlds( $Table ) {
    /*
     * Return DB Table fields in an array
     */
	global $_db;
	$fldN = array();
	
	$rslt = $_db->query("SHOW COLUMNS FROM `{$Table}`;");
	$rows = $rslt->fetch_all(MYSQLI_ASSOC);
	foreach ( $rows as $row ) {
		$fldN[] = $row [ 'Field' ];
	}	
	return $fldN;
} // getDBTblFlds()

function obtnUsrAddrIDs ( $usrName ) { // tables are locked by caller
	global $_db, $_SESSION, $_errCount, $_errRec, $_useChn;

	$rslt = $_db->query( "SELECT `PrimAddrID` FROM `Usr` WHERE `UsrName` = \"{$usrName}\";" );
	$rows = $rslt->fetch_all( MYSQLI_ASSOC );
	$rslt->free();
	$_SESSION[ 'primAddrID' ] = $rows[0][ 'PrimAddrID' ]; // use $_SESSION variable for data persistency
	$sql = "SELECT `AddrID` FROM `Addr2Usr` 
				WHERE `UsrName` = \"{$usrName}\" AND `AddrID` != \"{$_SESSION[ 'primAddrID' ]}\";";
	$rslt = $_db->query( $sql );
	$_SESSION[ 'altAddrID' ] = ( $rslt->num_rows > 0 ) ? $rslt->fetch_all( MYSQLI_ASSOC )[0]['AddrID'] : null;
	$rslt->free();
	// sanity check
	if ( $_SESSION['primAddrID'] == null && $_SESSION['altAddrID'] != null ) {
		$_errCount++;
		$_errRec[] = ($_useChn) ? "資料庫可能有損壞！" : "Database Corrupted!";
		return false;
	}
	return true;
} // function obtnUsrAddrIDs()

function updPrimAddr ( $usrName, $primAction, $primAddrID ) {
	// Attempt to set the PrimAddrID; caller ensures the $primAddrID is not NULL
	global $_db, $_errRec, $_errCount, $_SESSION, $_useChn;

	switch ( $primAction ) {
	case 'setPrimary':
		$sql = "UPDATE `Usr` SET `PrimAddrID` = \"{$primAddrID}\" WHERE `UsrName` = \"{$usrName}\";";
		$_db->query( $sql );
		if ($_db->errno) {
			$_errCount++;
			$_errRec[] = "updPrimAddr() DB ERROR (" . $_db->errno . ") SQL='" . $sql . ")\n";
			return false;
		}
		return true;
	case 'unsetPrimary':
		if ( $_SESSION['altAddrID'] != null ) {
			$sql = "UPDATE `Usr` SET `PrimAddrID` = \"{$_SESSION['altAddrID']}\" WHERE `UsrName` = \"{$usrName}\";";
			$_db->query( $sql );
			if ($_db->errno) {
				$_errCount++;
				$_errRec[] = "updPrimAddr() DB ERROR (" . $_db->errno . ") SQL='" . $sql . ")\n";
				return false;
			}
			return true;
		}
		$_errCount++;
		$_errRec[] = ( $_useChn ) ? "唯一的地址；必須為主要地址！" : "Cannot set the only address to non-primary!";
		return false;
	} // switch()
} // function updPrimAddr()

function insUsrAddrTuple( $tblName, $tupNVs, $usr ) {
	// tables involved: UsrAddr, Addr2Usr; they are locked by the caller
	global $_db, $_errCount, $_errRec;
	$tupAttrs = null;
	$tupVals = null;
	$tupID = null;

	$qryCond = ""; $i = 0;
	foreach ( $tupNVs as $attrN => $attrV ) { // formulate qry conditions, attribute list, value list
		if ( $attrV == '' ) { continue; } /* this is the AddrID attribute; shall be empty*/
		if ( $i > 0 ) { $qryCond .= " AND "; }
		$qryCond .= " `{$attrN}` = \"{$attrV}\" ";
		$tupAttrX[] = $attrN;
		$tupValX[] = $attrV;
		$i++;
	}
	$tupAttrs = "( " . implode( ', ', $tupAttrX ) . " )";
	$tupVals = "( \"" . implode( "\", \"", $tupValX ) . "\" )";
	// query existence
	$sql = "SELECT `AddrID` FROM `{$tblName}` WHERE {$qryCond};";
	$rslt = $_db->query( $sql );
	if ( $_db->errno ) {
		$_errCount++;
		$_errRec[] = "insUsrAddrTuple(): Errno = '" . $_db->errno . "'; sql = ' " . $sql . " '\n";
		return false;
	}
	if ( $rslt->num_rows ) {
		$tupID = $rslt->fetch_all(MYSQLI_ASSOC)[0]['AddrID'];
	}
	if ( !$tupID ) { // not exist; insert now
		$sql = "INSERT INTO `{$tblName}` {$tupAttrs} VALUE {$tupVals};";
		$rslt = $_db->query( $sql );
		if ( $_db->errno ) {
			$_errCount++;
			$_errRec[] = "insUsrAddrTuple(): Errno = '" . $_db->errno . "'; sql = ' " . $sql . " '\n";
			return false;
		}
		$tupID = $_db->insert_id;
	}

	$sql = "INSERT into `Addr2Usr` (`AddrID`, `UsrName` ) VALUE ( \"{$tupID}\", \"{$usr}\" );";
	$rslt = $_db->query( $sql );
	if ( $_db->errno ) {
		$_errCount++;
		$_errRec[] = "insUsrAddrTuple(): Errno = '" . $_db->errno . "'; sql = ' " . $sql . " '\n";
		return false;
	}
	return $tupID;
} // function insUsrAddrTuple()

function updUsrAddrTuple( $tblName, $tupNVs, $usr ) {
	global $_db, $_errRec, $_errCount;
	$tupAttrN = null; $tupAttrV = null;
	$tupKeyN = null; $tupKeyV = null;
	$otherTblName = '';

	$updCond = ''; $i = 0;
	foreach ( $tupNVs as $tupAttrN => $tupAttrV ) {
		if ( $tupAttrN == 'AddrID' ) {
			$tupKeyN = $tupAttrN; $tupKeyV = $tupAttrV; continue;
		}
		if ( $i > 0 ) { $updCond .= ", "; }
		if ( $tupAttrN != 'AddrID' ) {
			$updCond .= "`{$tupAttrN}` = \"{$tupAttrV}\"";
			$i++;
		}
	} // loop over (name, value) pairs

	if ( $updCond != '' ) { // changes to UsrAddr table
		$sql = "UPDATE `{$tblName}` SET {$updCond} WHERE `AddrID` = \"{$tupKeyV}\";";
		$rslt = $_db->query( $sql );
		if ( $_db->errno ) {
			$_errCount++;
			$_errRec[] = "updUsrAddrTuple(): Errno = '" . $_db->errno . "'; sql = ' " . $sql . " '\n";
			return false;
		}
	}
	return true;
} // function updUsrAddrTuple()

function delUsrAddrTuple( $tblName, $tupNVs, $usr ) {
	global $_db, $_errCount, $_errRec;
	// `Usr`.`PrimAddrID` will be nullified if affected (declared FOREIGN KEY CONSTRAINT)
	// `Addr2Usr` entry will be deleted (FOREIGN KEY CONSTRAINT)

	$tupID = null;	
	if ( array_key_exists( 'AddrID' , $tupNVs ) ) {
		$tupID = $tupNVs[ 'AddrID' ];
	}

	$sql = "DELETE FROM `{$tblName}` WHERE `AddrID` = \"{$tupID}\";";
	$rslt = $_db->query( $sql );
	if ($_db->errno) {
		$_errCount++;
		$_errRec[] = "delUsrAddrTuple(): Errno = '" . $_db->errno . "'; sql = ' " . $sql . " '\n";
		return false;
	}
	return true;
} // function delUsrAddrTuple()

?>