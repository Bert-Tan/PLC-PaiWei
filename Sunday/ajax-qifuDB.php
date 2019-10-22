<?php
	require_once( '../pgConstants.php' );
	require_once( 'dbSetup.php' );
	require_once( 'sunday_DBfuncs.php' );
	require_once( 'ChkTimeOut.php' );
	
	$_sessType = $_SESSION[ 'sessType' ];
	$_sessLang = $_SESSION[ 'sessLang' ];
	$_sessUsr = $_SESSION[ 'usrName' ];
	$_icoName = isset($_SESSION[ 'icoName' ]) ? $_SESSION[ 'icoName' ] : null;
	$useChn = ( $_SESSION[ 'sessLang' ] == SESS_LANG_CHN );

function _dbName_2_htmlName ( $_dbName ) {
	global $_sessLang;
	$_htmlNames = array (
		'sundayQifu' =>	array (
			SESS_LANG_CHN => "祈&nbsp;&nbsp;福&nbsp;&nbsp;申&nbsp;&nbsp;請&nbsp;&nbsp;表",
			SESS_LANG_ENG => "Well-wishing Request Form" ),
		'sundayMerit' => array (
			SESS_LANG_CHN => "功&nbsp;&nbsp;德&nbsp;&nbsp;迴&nbsp;&nbsp;向&nbsp;&nbsp;申&nbsp;&nbsp;請&nbsp;&nbsp;表",
			SESS_LANG_ENG => "Merit Dedication Request Form" ),
		'R_Name' =>	array (
			SESS_LANG_CHN => "申請人姓名",
			SESS_LANG_ENG => "Requestor's<br/>Full Name" ),
		'mWhom' =>	array (
			SESS_LANG_CHN => "往生者全名",
			SESS_LANG_ENG => "Recipient's<br/>Name" ),
		'qWhom' =>	array (
			SESS_LANG_CHN => "受益者全名",
			SESS_LANG_ENG => "Recipient's<br/>Name" ),
		'GuanXi' =>	array (
			SESS_LANG_CHN => "與申請人關係",
			SESS_LANG_ENG => "Relationship<br/>w/ Requestor" ),
		'Rsn' =>	array (
			SESS_LANG_CHN => "祈福申請理由",
			SESS_LANG_ENG => "Request<br/>Reason" ),
		'Age' => array (
			SESS_LANG_CHN => "往生者<br/>年齡",
			SESS_LANG_ENG => "Age Deceased" ),
		'Deceased_D' =>	array (
			SESS_LANG_CHN => "往生日期<br/>(西元)年-月-日",
			SESS_LANG_ENG => "Date Deceased<br/>YYYY-MM-DD" ),
		'Deceased_P' =>	array (
			SESS_LANG_CHN => "往生地點",
			SESS_LANG_ENG => "Place Deceased" ),
		'mDates' => array (
			SESS_LANG_CHN => "功德迴向日期<br/>(星期日，最多七次)",
			SESS_LANG_ENG => "Requested Sundays<br/>(within 49 days)" ),
		'qDates' => array (
			SESS_LANG_CHN => "祈福消災日期<br/>(星期日，最多三次)",
			SESS_LANG_ENG => "Requested Sundays<br/>(up to 3 times)" ),
		'dateInputV' => array (
			SESS_LANG_CHN => "(西元)年-月-日；星期日，以逗號分開",
			SESS_LANG_ENG => "YYYY-MM-DD; Sundays, comma separated" )
	);
	return ( $_htmlNames[ $_dbName ][ $_sessLang ]  );
} // _dbName_2_htmlName()

function setSundayParam( $dbInfo ) { // used by the Sunday Admin capabilities
	global $_db;
	$tblName = $dbInfo[ 'tblName' ];
	$fldNV = $dbInfo[ 'fldNV' ];
	$expHH = $fldNV[ 'expHH' ];
	$expMM = $fldNV[ 'expMM' ];
	$sql = "UPDATE `{$tblName}` SET `expHH` = \"{$expHH}\", `expMM` = \"{$expMM}\" WHERE `ID` = \"1\";";
	$_db->query("LOCK TABLES `{$tblName}`;");
	$rslt = $_db->query( $sql );
	$_db->query("UNLOCK TABLES;");
} // function setSundayParam()

function readSundayParam( $dbInfo ) {
	global $_db;
	/*
	 * Ajax Receiver switches on 'URL' and respective parameters
	 */
	$rpt = array();
	$tblName = $dbInfo[ 'tblName' ];
	$_db->query( "LOCK TABLES `{$tblName}` READ;" );
	$sql = "SELECT `expHH`, `expMM` FROM `{$tblName}`;";
	$rslt = $_db->query( $sql );
	$row = $rslt->fetch_all( MYSQLI_ASSOC )[0];
	$_db->query( "UNLOCK TABLES;" );
	$rslt->free();
	$rpt[ 'usrName'] = $_SESSION[ 'usrName' ];
	$rpt[ 'usrPass' ] = $_SESSION[ 'usrPass' ];
	$rpt[ 'sessType' ] = $_SESSION[ 'sessType' ];
	$rpt[ 'sessLang' ] = $_SESSION[ 'sessLang' ];
	$rpt[ 'icoName' ] = isset($_SESSION[ 'icoName' ]) ? $_SESSION[ 'icoName' ] : null;
	$rpt[ 'tblName' ] = isset($_SESSION[ 'tblName' ]) ? $_SESSION[ 'tblName' ] : null;
	$rpt[ 'expHH' ] = $row[ 'expHH' ]; // "08"; // hard code for now!
	$rpt[ 'expMM' ] = $row[ 'expMM' ]; // "30";
	return $rpt;
} // function readSundayParam()

function cellWidth( $fldN, $tblName ) { // Sunday data table field width (%) mapping
	if ( $tblName == 'sundayQifu' ) {
		return "width: 15.4%;"; // All fields for 祈福 table are the same width
	}
	$x = ''; // 迴向 table fields vary
	switch ( $fldN ) {
		case 'R_Name':
		case 'mWhom':
			$x = 10; break;
		case 'GuanXi':
			$x = 11; break;
		case 'Age':
			$x = 6; break;
		case 'Deceased_D':
			$x = 11; break;
		case 'Deceased_P':
			$x = 11; break;
		case 'mDates': // for 迴向; at most 7 dates
			$x = 24; break;
	} // switch() - End of determining Cell Width
	return "width: " . $x . "%;";
} // cellWidth()

function constructTblData ( $rows, $dbTblName, $refDate ) { // $rows =  $mysqlresult->fetch_all( MYSQLI_ASSOC )
	global $_sessLang, $_db;
	
	$dateFldName = ( $dbTblName == 'sundayQifu' ) ? 'qDates' : 'mDates';
	$dateFldWidth = cellWidth( $dateFldName, $dbTblName );
	$dateFldV = '';

	if ( $rows == null ) { // construct an empty data table with an empty row
		$fldN = getDBTblFlds( $dbTblName );
		$i = 0;
		foreach( $fldN as $colName ) {
			if ( $i == 0 ) { // key field; give it an empty value
				$row[ $colName ] = ''; continue;
			}
			$row[ $colName ] = ( $_sessLang == SESS_LANG_CHN ) ? "請輸入資料" : "Input data";
		}
		$rows[0] = $row;
		$dateFldV = _dbName_2_htmlName( 'dateInputV' ); // default
	}
	
	$tpl = new HTML_Template_IT("./Templates");
	$tpl->loadTemplatefile("qifuTblData.tpl", true, true);

	$tpl->setCurrentBlock("data_tbl") ;
	$tpl->setVariable("dbTblName", $dbTblName );
	$rowCount = 0;
	foreach( $rows as $row ) {
		$rowCount++;
		$sundayRqDates = getSundayRqDates( $dbTblName, $row['ID'], $refDate );
		if ( strlen( $sundayRqDates ) == 0 ) continue;
		$tpl->setCurrentBlock("data_row");
		$i = 0;
		foreach ( $row as $key => $val ) {
			if ( $i == 0 ) { // key field; not visible to user
				$tpl->setVariable("tupKeyN", $key);
				$tpl->setVariable("tupKeyV", $val);
				$i++; continue;
			}
			// all other fields are visible to user
			$tpl->setCurrentBlock("data_cell");
			if ( $rowCount == 1 ) { // only need to set cell width for the first data row
				$tpl->setVariable("cellWidth", cellWidth( $key, $dbTblName  ) );
			}
			$tpl->setVariable("dbFldN", $key);
			$tpl->setVariable("dbFldV", $val);
			$tpl->parse("data_cell");
		} // data fields of a row from sundayQifu or sundayMerit table
	
		$tpl->setCurrentBlock("reqDateCol");
		if ( $rowCount == 1 ) $tpl->setVariable("dateFldWidth", $dateFldWidth );
		if ( $dateFldV == '' ) {
			$dateFldV = $sundayRqDates;
		}
		$tpl->setVariable("dateFldV", $dateFldV ); $dateFldV = '';
		$tpl->parse("reqDateCol");
		$tpl->setCurrentBlock("dataEditCol");
		if ( $_sessLang == SESS_LANG_CHN ) {
			$tpl->setVariable( "editBtnTxt", "更改");
			$tpl->setVariable( "delBtnTxt", "刪除");
		} else {
			$tpl->setVariable("editBtnTxt", "Edit");
			$tpl->setVariable("delBtnTxt", "Del");
		}
		$tpl->parse("dataEditCol");
		/* User assignment selection code goes here */
		$tpl->parse("data_row") ;
	} // $rows
	$tpl->parse("data_tbl");
	$tmp = preg_replace( "/(^[\r\n]*|[\r\n]+)[\s\t]*[\r\n]+/", "\n", $tpl->get() );
	return preg_replace( "/(^\t*)/", "  ", $tmp );
} // constructTblData()

function constructTblHeader( $dbTblName ) {
	global $_sessUsr, $_sessLang, $_icoName;
	$fldN = getDBTblFlds( $dbTblName );
	$tpl = new HTML_Template_IT("./Templates");
	$tpl->loadTemplatefile("qifuTblHeader.tpl", true, true);
	$tpl->setCurrentBlock("hdr_tbl") ;
	$tpl->setVariable("tblName", $dbTblName );
	$tpl->setVariable("numCols", sizeof($fldN) + 1 ) ; // request Date column not in DB Table
	$tpl->setVariable("htmlTblName", _dbName_2_htmlName( $dbTblName ) ) ;
	$tpl->setVariable("Who", $_sessUsr ) ;
	if ( $_icoName != null ) {
		$tpl->setVariable("ico", ";&nbsp;&nbsp;In Care Of:&nbsp;&nbsp;{$_icoName}" ) ;
  	}
	$i = 0;
	foreach ( $fldN as $key ) {
		// first field is the tuple key; not visible to the users
		if ( $i == 0 ) { $i++; continue; }
	
		$tpl->setCurrentBlock("hdr_cell");
		$tpl->setVariable("cellWidth", cellWidth( $key, $dbTblName ) ) ;
		$tpl->setVariable("htmlFldName", _dbName_2_htmlName( $key ) ) ;
    	$tpl->parse("hdr_cell");	
	}
	$tpl->setCurrentBlock("reqDateCol");
	$dateFldName = ( $dbTblName == 'sundayQifu' ) ? 'qDates' : 'mDates';
	$tpl->setVariable("dateFldWidth", cellWidth( $dateFldName, $dbTblName ) );
	$tpl->setVariable("dateFldName", _dbName_2_htmlName( $dateFldName ) );
	$tpl->parse("reqDateCol");
	$tpl->setCurrentBlock("dataEditCol");
	if ( $_sessLang == SESS_LANG_CHN) {
		$tpl->setVariable("addBtnTxt", '加行輸入');
		$tpl->setVariable("delAllBtnTxt", '全部刪除');
	} else {
		$tpl->setVariable("addBtnTxt", 'AddRow');
		$tpl->setVariable("delAllBtnTxt", 'DelAll');
	}	 	
	$tpl->parse("dataEditCol");
	$tpl->parse("hdr_tbl");
	$tmp = preg_replace( "/(^[\r\n]*|[\r\n]+)[\s\t]*[\r\n]+/", "\n", $tpl->get() );
	return preg_replace( "/(^\t*)/", "  ", $tmp );
} // constructTblHeader()

/**********************************************************
 *				For dbREAD																			*
 **********************************************************/
function getSundayTblData ( $dbInfoX ) {
	/*
	 * The receiving AJAX switches on cases: 'URL', 'myDataHdr', 'myData', 'myDataSize'
	 */
	global $_db;
	$rpt = array();
	$tblSize = 0;

	$tblName = $dbInfoX[ 'tblName' ];
	$Rqstr = $dbInfoX[ 'rqstr' ];

	$sql = "LOCK TABLES `{$tblName}` READ, `sundayRq2Usr` READ;";
	$_db->query( $sql );
	$sql = "SELECT * FROM `{$tblName}` WHERE `ID` IN "
		 . "(SELECT `rqID` FROM `sundayRq2Usr` WHERE `TblName` = \"{$tblName}\" AND `UsrName` = \"{$Rqstr}\") "
		 . "ORDER BY `ID`"
		 . ";";
	$rslt = $_db->query( $sql );
	$tblSize = $rslt->num_rows;
	$rows = $rslt->fetch_all( MYSQLI_ASSOC );
	$rslt->free();
	$sql = "UNLOCK TABLES;";
	$_db->query( $sql );
	$rpt [ 'myDataHdr' ] = constructTblHeader( $tblName );
	if ( $tblSize == 0 ) {
  		$rows = null;
  	}
	$rpt [ 'myData' ] = constructTblData( $rows, $tblName, $dbInfoX[ 'refDate' ] );
	$rpt [ 'myDataSize' ] = $tblSize;
	return $rpt;
} // getSundayTblData()

/**********************************************************
 *				For dbSEARCH																		*
 **********************************************************/
 function srchTblData( $dbInfo ) {
	/*
	 * The receiving AJAX switches on 'URL', 'errCount', 'myData', 'myDataSize'
	 */
	global $_db, $_errRec, $_errCount, $_srchCount, $_srchRec;
	$rpt = array();

	$tblName = $dbInfo['tblName'];
	$sql = "LOCK TABLES {$tblName} READ, pw2Usr READ;";
	$_db->query( $sql );
	if ( ! searchPaiWeiTuple( $dbInfo['tblName'],
														$dbInfo['tblFlds'],
														$dbInfo['pwRqstr' ]) ) {
		$rpt [ 'errCount' ] = $_errCount;
		$rpt [ 'errRec' ] = $_errRec;
		return $rpt;
	}
	$sql = "UNLOCK TABLES;";
	$_db->query( $sql );
	if ( $_srchCount == 0 ) {
		$_srchRec = null;
	}
	$rpt [ 'myData' ] = constructTblData ( $_srchRec, $dbInfo[ 'tblName' ] );
	$rpt [ 'myDataSize' ] = $_srchCount;
	return $rpt;	
} // srchTblData()

/**********************************************************
 *				For dbDEL																				*
 **********************************************************/
function delSundayTblData( $dbInfo ) {
	/*
	 * The receiving AJAX switch on 'URL', 'delSUCCESS', 'errCount'
	 */
	global $_db, $_errRec, $_errCount, $_delCount;
	global $useChn;
	$rpt = array();

	$tblName = $dbInfo['tblName'];
	$_db->autocommit(false);
	$_db->begin_transaction(MYSQLI_TRANS_START_WITH_CONSISTENT_SNAPSHOT);
	$sql = "LOCK TABLES {$tblName}, sundayRq2Usr, sundayRq2Days;";
	$_db->query( $sql );	
	if ( ! deleteSundayTuple( $dbInfo['tblName'], $dbInfo['tblFlds'], $dbInfo['rqstr']) ) {
		$_db->rollback();
		$rpt [ 'errCount' ] = $_errCount;
		$rpt [ 'errRec' ] = $_errRec;
		return $rpt;
	}
	$_db->commit();
	$sql = "UNLOCK TABLES;";
	$_db->query( $sql );
	$_db->autocommit(true);
	$rpt [ 'delSUCCESS' ] = ( $useChn ) ? "祈福迴向資料刪除完畢！" : "Record deleted";	
	return $rpt;
} // delSundayTblData()

/**********************************************************
 *				For dbDELX																			*
 **********************************************************/
function delSundayTblUsrData( $dbInfo ) {
	/*
	 * The receiving AJAX switch on 'URL', 'delSUCCESS', 'errCount'
	 */
	global $_db, $_errRec, $_errCount, $_delCount;
	global $useChn;
	$rpt = array();

	$tblName = $dbInfo['tblName'];
	$_db->autocommit(false);
	$_db->begin_transaction(MYSQLI_TRANS_START_WITH_CONSISTENT_SNAPSHOT);
	$sql = "LOCK TABLES `{$tblName}` WRITE, `sundayRq2Usr` WRITE, `sundayRq2Days` WRITE;";
	$_db->query( $sql );	
	if ( ! deleteSundayUsrTuple( $dbInfo['tblName'], $dbInfo['rqstr'] ) ) {
		$_db->rollback();
		$rpt [ 'errCount' ] = $_errCount;
		$rpt [ 'errRec' ] = $_errRec;
		return $rpt;
	}
	$_db->commit();
	$_db->query( "UNLOCK TABLES;" );
	$_db->autocommit(true);
	$rpt [ 'delSUCCESS' ] = ( $useChn ) ? "{$_delCount} 項祈福迴向資料刪除完畢！" : "{$_delCount} records deleted";	
	return $rpt;
} // delTblUsrData()

/**********************************************************
 *				For dbINS																				*
 **********************************************************/
function insSundayTblData( $dbInfo ) {
	/*
	 * The receiving AJAX switches on 'URL', 'insSuccess', 'errCount', 'dupCount'
	 */
	global $_db, $_errRec, $_errCount, $_insCount, $_dupCount, $_dupRec;
	$rpt = array();
	$tblName = $dbInfo['tblName'];
	$_db->autocommit(false);
	$_db->begin_transaction(MYSQLI_TRANS_START_WITH_CONSISTENT_SNAPSHOT);
	$sql = "LOCK TABLES `{$tblName}`, `sundayRq2Usr`, `sundayRq2Days`;";
	$_db->query( $sql );
	$tupID = insertSundayTuple(	$dbInfo['tblName'], $dbInfo['tblFlds'], $dbInfo['rqstr'] );
	if ( ! $tupID ) {
		$_db->rollback();
		if ( $_errCount ) {
			$rpt [ 'errCount' ] = $_errCount;
			$rpt [ 'errRec' ] = $_errRec;
		}
		if ( $_dupCount ) {
			$rpt [ 'dupCount' ] = $_dupCount;
			$rpt [ 'dupRec' ] = $_dupRec;
		}
		return $rpt;
	}
	$_db->commit();
	$sql = "UNLOCK TABLES;";
	$_db->query( $sql ); $_db->autocommit(true);
	$rpt [ 'insSUCCESS' ] = $tupID;
	return $rpt;
} // insSundayTblData()

/**********************************************************
 *				For dbUPD																				*
 **********************************************************/
function updSundayTblData( $dbInfo ) {
	/*
	 * The receiving AJAX switches on 'URL', 'updSUCCESS', 'errCount'
	 */
	global $_db, $_errCount, $_errRec, $_updCount;
	$rpt = array();

	$tblName = $dbInfo['tblName'];
	$_db->autocommit(false);
	$_db->begin_transaction(MYSQLI_TRANS_START_WITH_CONSISTENT_SNAPSHOT);
	$sql = "LOCK TABLES `{$tblName}`, `sundayRq2Usr`, `sundayRq2Days`;";
	$_db->query( $sql );
	if ( ! updateSundayTuple( $dbInfo['tblName'], $dbInfo['tblFlds'], $dbInfo['rqstr'], $dbInfo['refDate'] ) ) {
		$_db->rollback();
		if ( $_errCount ) {
			$rpt [ 'errCount' ] = $_errCount;
			$rpt [ 'errRec' ] = $_errRec;
		}
		return $rpt;
	}
	$_db->commit();
	$sql = "UNLOCK TABLES;";
	$_db->autocommit(true);
	$rpt [ 'updSUCCESS' ] = true;
	return $rpt;													
} // updSundayTblData()

/**********************************************************
 *                      For Dashboard                     *
 **********************************************************/
function dashBoardSetting( $dbInfo ) {
	global $_SESSION;
	$_SESSION['icoName'] = $dbInfo['icoName'];
	$_SESSION['tblName'] = $dbInfo['tblName'];
	$rpt[ 'url' ] = URL_ROOT . '/admin/PaiWei/index.php';
	return $rpt;
} // dashBoardSetting()
/**********************************************************
 *								 Main Functional Code										*
 **********************************************************/
$_dbReq = $_POST[ 'dbReq' ];
$_dbInfo = json_decode( $_POST [ 'dbInfo' ], true );

switch ( $_dbReq ) {
	case 'readSundayParam':
		echo json_encode( readSundayParam( $_dbInfo ), JSON_UNESCAPED_UNICODE );
		break;
	case 'dbREAD':
		echo json_encode( getSundayTblData ( $_dbInfo ), JSON_UNESCAPED_UNICODE );				
		break;
	case 'dbSEARCH':
		echo json_encode ( srchTblData( $_dbInfo ), JSON_UNESCAPED_UNICODE );
		break;
	case 'dbINS':
		echo json_encode ( insSundayTblData( $_dbInfo ), JSON_UNESCAPED_UNICODE );
		break;
	case 'dbDEL':
		echo json_encode ( delSundayTblData( $_dbInfo ), JSON_UNESCAPED_UNICODE );
		break;
	case 'dbDELX': /* delete table data that belongs to a specific user */
		echo json_encode ( delSundayTblUsrData( $_dbInfo ), JSON_UNESCAPED_UNICODE );
		break;
	case 'dbUPD':
		echo json_encode ( updSundayTblData ( $_dbInfo ), JSON_UNESCAPED_UNICODE );
		break;
	case 'pwDashboard':
		echo json_encode ( dashBoardSetting( $_dbInfo ), JSON_UNESCAPED_UNICODE);
		break;
} // switch()

$_db->close();
?>
