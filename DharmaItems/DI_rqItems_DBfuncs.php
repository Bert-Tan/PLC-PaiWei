<?php
$_insCount = 0; $_updCount = 0; $_errCount = 0; $_errRec = array();

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
?>