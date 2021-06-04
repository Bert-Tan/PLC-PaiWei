<?php
$_insCount = 0; $_updCount = 0; $_errCount = 0; $_errRec = array();
$_strkRange = null; $_bkRows = array();

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
} // function getDBTblFlds()

function upldBookItem( $tblName, $tupNVs, $recNo ) {
	global $_db, $_insCount, $_updCount, $_errCount, $_errRec;

	$tupID = null;
	$qryCond = ""; $i = 0;
	$qtyV = null;
	$locV = null;
	foreach ( $tupNVs as $attrN => $attrV ) { // formulate qry conditions, attribute list, value list
		switch ( $attrN ) {
		case 'Loc': // subject to update; remember it
			$LocV = $attrV;
			break;
		case 'Qty': // subject to update; remember it
			$qtyV = $attrV;
			break;
		default:
			if ( $i > 0 ) { $qryCond .= " AND "; }
			$qryCond .= " `{$attrN}` = \"{$attrV}\" ";
			$i++;
			break;
		} // switch()
		$tupAttrX[] = $attrN;
		$tupValX[] = $attrV;
	} // formulating query condition and attribute Name List and Value List (for insertion purpose)

	/* check existance */
	$sql = "SELECT `invtID` FROM `{$tblName}` WHERE " . $qryCond .";";
	$rslt = $_db->query( $sql );
	if ( $_db->errno ) {
		echo "Record No.: " . $recNo . ";<br/>" . "DB Error No.: " . $_db->errno . ";<br/>";
		echo "&nbsp;&nbsp;SQL: '" . $sql . "'<br/><br/>";
		return false;
	}

	switch ( $rslt->num_rows ) { // should be 0 or 1
	case 0:
		break;
	case 1:
		$tupID = $rslt->fetch_all(MYSQLI_ASSOC)[0][ 'invtID' ];
		break;
	default: // error handling code later
		echo "DB Error: " . $rslt->num_rows . "Found!<br/><br/>";
		echo "&nbsp;&nbsp;SQL= '" . $sql . "'<br/><br/>";
		return false;
	}

	if ( $tupID ) { // record exists; update the Location & quantity
		$sql = "UPDATE `{$tblName}` SET `Qty` = \"{$qtyV}\" AND `Loc` = \"{$locV}\" WHERE `invtID` = \"{$tupID}\";";
		$rslt = $_db->query( $sql );
		if ( $_db->errno ) {
			echo "Record No.: " . $recNo . ";<br/>" . "DB Error No.: " . $_db->errno . ";<br/>";
			echo "&nbsp;&nbsp;SQL= '" . $sql . "'<br/><br/>";
			return false;
		}
		$_updCount++;
		return true;
	}
	
	// record not exist; insert it
	$tupAttrList = "( " . implode( ', ', $tupAttrX ) . " )";
	$tupValList = "( \"" . implode( "\", \"", $tupValX ) . "\" )";
	$sql = "INSERT INTO `{$tblName}` " . $tupAttrList . " VALUE " . $tupValList . ";";
	$rslt = $_db->query( $sql );
	if ( $_db->errno ) {
		echo "Record No.: " . $recNo . ";<br/>" . "DB Error No.: " . $_db->errno . ";<br/>";
		echo "&nbsp;&nbsp;SQL: '" . $sql . "'<br/><br/>";
		return false;
	}
	$_insCount++;
	return true;
} // function upldBookItem

function readInvt_BK( $tblName, $stroke ) {
	global $_db, $_errCount, $_errRec, $_strkRange, $_bkRows;
	$rpt = array();
	$sql = null;

	switch ( $tblName ) {
	case 'INVT_BK_C':
		$sql = "SELECT MIN( Strokes ), MAX( Strokes ) FROM `INVT_BK_C`; ";
		$rslt = $_db->query( $sql );
		if ( $_db->errno ) {
			$_errCount++;
			$_errRec[]	= "readInvt_BK_C() DB Error No.: " . $_db->errno . ";<br/>"
						. "&nbsp;&nbsp;SQL: '" . $sql . "'<br/><br/>";
			return false;
		}
		$_strkRange = $rslt->fetch_all(MYSQLI_NUM)[0];
		$strk_min = $_strkRange[0];
		if ( ! $stroke ) $stroke = $strk_min; 
		$sql = "SELECT `invtID`,`Strokes`,`Title`,`Author` FROM `{$tblName}` WHERE `Strokes` = \"{$stroke}\";";
		// Let it fall through
	case 'INVT_BK_E': // below code for both Chinese and English book lists
		if ( ! $sql ) {
			$sql = "SELECT `invtID`,`Title`,`Author` FROM `{$tblName}`;";
		}
		$rslt = $_db->query( $sql );
		if ( $_db->errno ) {
			$_errCount++;
			$_errRec[]	= "readInvt_BK() DB Error No.: " . $_db->errno . ";<br/>"
						. "&nbsp;&nbsp;SQL: '" . $sql . "'<br/><br/>";
			return false;
		}
		$_bkRows = $rslt->fetch_all(MYSQLI_ASSOC);
		break;
	} // end switch()
	return true;
} // function readInvt_BK()

?>