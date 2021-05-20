<?php
$_delCount = 0; $_insCount = 0; $_errCount = 0;

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

/*
function getGongDeZhuNum( $rqDate ) {
	global $_db;

	$sql = "LOCK TABLES `sundayRq2GongDeZhu` , `sundayRq2Days`;";
	$_db->query( $sql );
	
	$sql = "SELECT G.TblName, G.rqID FROM sundayRq2GongDeZhu G "
				.	"INNER JOIN sundayRq2Days D ON (G.TblName=D.TblName AND G.rqID=D.rqID) "
				.	"WHERE G.GongDeZhu=1 AND D.rqDate=\"{$rqDate}\" "
				.	"ORDER BY G.rqTime;";	
	$rslt = $_db->query( $sql );
	$num = $rslt->num_rows;
		
	$sql = "UNLOCK TABLES;";
	$_db->query( $sql );
	$rslt->free();

	return $num;
} // function getGongDeZhuNum()
*/

/*
function getGongDeZhu( $tblName, $rqID ) {
	global $_db;
	
	$sql = "SELECT `GongDeZhu` from `sundayRq2GongDeZhu` WHERE `TblName` = \"{$tblName}\" AND `rqID` = \"{$rqID}\"";
	$rslt = $_db->query( $sql );
	if ( $rslt->num_rows == 0 ) return '';
	$gongDeZhuArray = $rslt->fetch_all(MYSQLI_NUM);
	$gongDeZhu = $gongDeZhuArray[0][0];
	return checkboxInt2Str ( $gongDeZhu );
} // function getGongDeZhu()
*/

function getSundayRqDates( $tblName, $rqID, $refDate ) {
	global $_db;
	$rqDates = array();

	$sql = "SELECT `rqDate` from `sundayRq2Days` WHERE `TblName` = \"{$tblName}\" AND `rqID` = \"{$rqID}\"";
	if ( $refDate != null ) $sql .= " AND `rqDate` >= \"{$refDate}\"";
	$sql .= " ORDER BY `rqDate`;";
	$rslt = $_db->query( $sql );
	if ( $rslt->num_rows == 0 ) return '';
	$dates = $rslt->fetch_all(MYSQLI_NUM);
	foreach( $dates as $date ) {
		$rqDates[] = $date[0];
	}
	return implode( ', ', $rqDates );
} // function getSundayRqDates()

function insertSundayTuple( $tblName, $tupNVs, $usr ) {
	// tables involved: sundayQifu / sundayMerit; sundayRq2Days; & sundayRq2Usr ==> Caller locked the tables
	global $_db;
	$tupAttr = array();
	$tupVal = array ();
	$tupID = null;
	$reqDates = array();
	//$gongDeZhu = 0;

	$qryCond = ""; $i = 0;
	foreach ( $tupNVs as $attrN => $attrV ) { // formulate qry conditions, attribute list, value list
		if ( $attrV == '' ) { continue; }
		if ( $attrN == 'reqDates') { $reqDates = preg_split( "/,\s*/", $attrV ); continue; }
		//if ( $attrN == 'GongDeZhu') { $gongDeZhu = checkboxStr2Int( $attrV ); continue; }
		if ( $i > 0 ) { $qryCond .= " AND "; }
		$qryCond .= " `{$attrN}` = \"{$attrV}\" ";
		$tupAttrX[] = $attrN;
		$tupValX[] = $attrV;
		$i++;
	}
	$tupAttrs = "( " . implode( ', ', $tupAttrX ) . " )";
	$tupVals = "( \"" . implode( "\", \"", $tupValX ) . "\" )";
	// query existence
	$sql = "SELECT `ID` FROM `{$tblName}` WHERE {$qryCond};";
	$rslt = $_db->query( $sql );
	if ( $rslt->num_rows ) {
		$tupID = $rslt->fetch_all(MYSQLI_ASSOC)[0]['ID'];
	}
	if ( !$tupID ) { // not exist; insert now
		$sql = "INSERT INTO `{$tblName}` {$tupAttrs} VALUE {$tupVals};";
		$rslt = $_db->query( $sql );
		$tupID = $_db->insert_id;
	}
	// taking care of sundayRq2Dates table now
	$i = 0; $reqDateValues = '';
	foreach ( $reqDates as $reqDate ) {
		if ( $i > 0 && $i != sizeof($reqDates) ) { $reqDateValues .= ', '; }
		$reqDateValues .= "( \"{$tblName}\", \"{$tupID}\", \"{$reqDate}\" )";
		$i++;
	}
	$sql = "INSERT into `sundayRq2Days` (`TblName`, `rqID`, `RqDate`) VALUE {$reqDateValues};";
	$rslt = $_db->query( $sql );
	/*
	// taking care of sundayRq2GongDeZhu table
	$now = date("Y-m-d H:i:s");
	$sql = "INSERT into `sundayRq2GongDeZhu` (`TblName`, `rqID`, `GongDeZhu`, `rqTime`) VALUE (\"{$tblName}\",  \"{$tupID}\", \"{$gongDeZhu}\", \"{$now}\");";
	$rslt = $_db->query( $sql );
	*/
	// taking care of sundayRq2Usr table
	$sql = "INSERT into `sundayRq2Usr` (`TblName`, `rqID`, `UsrName`) VALUE (\"{$tblName}\",  \"{$tupID}\", \"{$usr}\");";
	$rslt = $_db->query( $sql );	

	return $tupID;
} // function insertSundayTuple()

function updateSundayTuple( $tblName, $tupNVs, $usr, $refDate ) {
	global $_db;
	$tupAttr = array();
	$tupVal = array ();
	$tupID = null;
	$reqDates = array();
	/*
	$gongDeZhu = 0;
	$updateGongDeZhu = false;
	*/

	$updCond = ''; $i = 0;
	foreach ( $tupNVs as $attrN => $attrV ) {
		if ( $attrN == 'reqDates') { $reqDates = preg_split( "/,\s*/", $attrV ); continue; }
		//if ( $attrN == 'GongDeZhu') { $gongDeZhu = checkboxStr2Int( $attrV ); $updateGongDeZhu = true; continue; }
		if ( $attrN == 'ID' ) { $tupID = $attrV; continue; }
		if ( $i > 0 ) { $updCond .= ", "; }
		if ( $attrN != 'ID' && $attrN != 'reqDates' ) {
			$updCond .= "`{$attrN}` = \"{$attrV}\"";
			$i++;
		}
	} // loop over (name, value) pairs

	if ( $updCond != '' ) { // changes to the sundayQifu or sundayMerit table
		$sql = "UPDATE `{$tblName}` SET {$updCond} WHERE `ID` = \"{$tupID}\";";
		$rslt = $_db->query( $sql );
	}
	if ( sizeof( $reqDates ) > 0 ) { // changes to the sundayRq2Days table
		// the easiest way: delete all entries for ($tblName, $tupID), & insert the new dates
		$sql = "DELETE FROM `sundayRq2Days` WHERE `TblName` = \"{$tblName}\" AND `rqID` = \"{$tupID}\"";
		if ( $refDate != null ) $sql .= " AND `rqDate` >= \"{$refDate}\"";
		$sql .= ";";
		$rslt = $_db->query( $sql );
		$i = 0; $reqDateValues = '';
		foreach ( $reqDates as $reqDate ) {
			if ( $i > 0 && $i != sizeof($reqDates) ) { $reqDateValues .= ', '; }
			$reqDateValues .= "( \"{$tblName}\", \"{$tupID}\", \"{$reqDate}\" )";
			$i++;
		}
		$sql = "INSERT into `sundayRq2Days` (`TblName`, `rqID`, `RqDate`) VALUE {$reqDateValues};";
		$rslt = $_db->query( $sql );
	}
	/*
	if ( $updateGongDeZhu ) { // changes to the sundayRq2GongDeZhu table
		$now = date("Y-m-d H:i:s");
		$sql = "UPDATE `sundayRq2GongDeZhu` SET `GongDeZhu` = \"{$gongDeZhu}\", `rqTime` = \"{$now}\" WHERE `TblName` = \"{$tblName}\" AND `rqID` = \"{$tupID}\";";
		$rslt = $_db->query( $sql );
	}
	*/
	return true;
} // function updateSundayTuple()

function deleteSundayTuple( $tblName, $tupNVs, $usr ) {
	global $_db;

	$tupID = null;	
	if ( array_key_exists( 'ID' , $tupNVs ) ) {
		$tupID = $tupNVs[ 'ID' ];
	}
	// delete sundayRq2Days entries
	$sql = "DELETE FROM `sundayRq2Days` WHERE `TblName` = \"{$tblName}\" AND `rqID` = \"{$tupID}\";";
	$rslt = $_db->query( $sql );
	/*
	// delete sundayRq2GongDeZhu entries
	$sql = "DELETE FROM `sundayRq2GongDeZhu` WHERE `TblName` = \"{$tblName}\" AND `rqID` = \"{$tupID}\";";
	$rslt = $_db->query( $sql );
	*/
	// delete sundayRq2Usr entries
	$sql = "DELETE FROM `sundayRq2Usr` WHERE `TblName` = \"{$tblName}\" AND `rqID` = \"{$tupID}\" AND `UsrName` = \"{$usr}\";";
	$rslt = $_db->query( $sql );	
	// finally, delete the entry in the sundayQifu or sundayMerit table
	$sql = "DELETE FROM `{$tblName}` WHERE `ID` = \"{$tupID}\";";
	$rslt = $_db->query( $sql );
	return true;
} // function deleteSundayTuple()

function deleteSundayUsrTuple( $tblName, $usr ) {
	global $_db, $_delCount;
	/* MUST BE DONE in the following sequence because `ID` relates all data together which is in `sundayRq2Usr` table */
	// Delete entries in sundayRq2Days table belonging to the $usr
	$sql =	"DELETE FROM `sundayRq2Days` WHERE `TblName` = \"{$tblName}\" AND `rqID` IN " .
			"(SELECT `rqID` FROM `sundayRq2Usr` WHERE `TblName` = \"{$tblName}\" AND `UsrName` = \"{$usr}\")" .
			";";
	$rslt = $_db->query( $sql );
	/*
	// Delete entries in sundayRq2GongDeZhu table belonging to the $usr
	$sql =	"DELETE FROM `sundayRq2GongDeZhu` WHERE `TblName` = \"{$tblName}\" AND `rqID` IN " .
			"(SELECT `rqID` FROM `sundayRq2Usr` WHERE `TblName` = \"{$tblName}\" AND `UsrName` = \"{$usr}\")" .
			";";
	$rslt = $_db->query( $sql );
	*/
	// Delete entries in sundayQifu or sundayMerit table belonging to the $usr
	$sql =	"DELETE FROM `{$tblName}` WHERE `ID` IN " . 
			"(SELECT `rqID` FROM `sundayRq2Usr` WHERE `TblName` = \"{$tblName}\" AND `UsrName` = \"{$usr}\")" .
			";";
	$rslt = $_db->query( $sql );
	// Now, delete entries in sundayRq2Usr belonging to the $usr
	$sql =	"DELETE FROM `sundayRq2Usr` WHERE `TblName` = \"{$tblName}\" AND `UsrName` = \"{$usr}\";";
// echo $sql; exit;
	$rslt = $_db->query( $sql );
	$_delCount = $_db->affected_rows;
	return true;
} // function deleteSundayUsrTuple()

function checkVacantQifuCounts( $tblName, $tupNVs ) {
	global $_db;
	$existingReqDateNum = null;
	$tupID = null;
	$qWhom = null;
	$reqDates = array();

	foreach ( $tupNVs as $attrN => $attrV ) {
		if ( $attrN == 'ID' ) { 
			$tupID = $attrV; 
			continue; 
		}
		if ($attrN == 'qWhom') {
			$qWhom = $attrV;
			continue;
		}
		if ( $attrN == 'reqDates') {
			$reqDates = preg_split( "/,\s*/", $attrV );
			continue;
		}
	}
	$newReqDateNum = sizeof($reqDates);
	$largestReqDate = date('Y-m-d');	
	foreach ( $reqDates as $reqDate ) {		
		if (strtotime($reqDate) > strtotime($largestReqDate)) {
			$largestReqDate = $reqDate;			
		}
	}

	// query existing Qifu counts within three months of the largest new request date
	$sql = "SELECT COUNT(*) FROM `{$tblName}` Q INNER JOIN `sundayRq2Days` D ON Q.ID = D.rqID "
			. "WHERE `qWhom` = '{$qWhom}' AND (`rqDate` BETWEEN SUBDATE('{$largestReqDate}', INTERVAL 3 MONTH) AND '{$largestReqDate}')";
	if ($tupID != null)
		$sql = $sql . " AND `ID` <> {$tupID}";
		$sql = $sql . ";";

	$rslt = $_db->query( $sql );
	if ( $rslt->num_rows ) {
		$existingReqDateNum = $rslt->fetch_all(MYSQLI_NUM)[0][0];
	}

	return array($existingReqDateNum, $newReqDateNum);
} // function checkVacantQifuCounts()

/*
function checkboxInt2Str ( $intVal ) {
	if ( $intVal == 1 )
		return "checked";
	else
		return "";
} // checkboxInt2Str

function checkboxStr2Int ( $checkStr ) {
	if ( $checkStr == "checked" )
		return 1;
	else
		return 0;
} // checkboxStr2Int
*/
?>