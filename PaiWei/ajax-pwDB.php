<?php
	require_once( '../pgConstants.php' );
	require_once( 'dbSetup.php' );
	require_once( 'PaiWei_DBfuncs.php' );
	require_once( 'ChkTimeOut.php' );
	
	$_sessType = $_SESSION[ 'sessType' ];
	$_sessLang = $_SESSION[ 'sessLang' ];
	$_sessUsr = $_SESSION[ 'usrName' ];

function _dbName_2_htmlName ( $_dbName ) {
	global $_sessLang;
	$_htmlNames = array (
		'C001A' =>	array (
			SESS_LANG_CHN =>	"佛&nbsp;光&nbsp;祝&nbsp;照&nbsp;祈&nbsp;福&nbsp;消&nbsp;災",
			SESS_LANG_ENG =>	"For Well-blessing & Lessening Misfortune" ),
		'W001A' =>	array (
			SESS_LANG_CHN =>	"佛&nbsp;力&nbsp;超&nbsp;薦&nbsp;往&nbsp;生&nbsp;親&nbsp;友",
			SESS_LANG_ENG =>	"Dedicate Merit to the Deceased" ),
		'W001A_4' =>	array (
			SESS_LANG_CHN =>	"佛&nbsp;力&nbsp;超&nbsp;薦&nbsp;往&nbsp;生&nbsp;親&nbsp;友",
			SESS_LANG_ENG =>	"Dedicate Merit to the Deceased" ),
		'L001A' =>	array (
			SESS_LANG_CHN =>	"佛&nbsp;力&nbsp;超&nbsp;薦&nbsp;歷&nbsp;代&nbsp;祖&nbsp;先",
			SESS_LANG_ENG =>	"Dedicate Merit to Ancestors" ),
		'Y001A' =>	array (
			SESS_LANG_CHN =>	"佛&nbsp;力&nbsp;超&nbsp;薦&nbsp;累&nbsp;劫&nbsp;冤&nbsp;親&nbsp;債&nbsp;主",
			SESS_LANG_ENG =>	"Dedicate Merit to Karmic Creditors" ),
		'D001A' =>	array (
			SESS_LANG_CHN =>	"佛&nbsp;力&nbsp;超&nbsp;薦&nbsp;地&nbsp;基&nbsp;主",
			SESS_LANG_ENG =>	"Dedicate Merit to Site Guardians" ),
		'DaPaiWei' => array (
			SESS_LANG_CHN =>	"佛&nbsp;力&nbsp;超&nbsp;薦&nbsp;近&nbsp;期&nbsp;"
											.	"<span style=\"color: yellow;\">(12 個月之內)</span>&nbsp;往&nbsp;生&nbsp;親&nbsp;友",
			SESS_LANG_ENG =>	"Dedicate Merit to Recently "
											.	"<span style=\"color: yellow;\">(within 12 months)</span> Deceased" ),
		'C_Name' =>	array (
			SESS_LANG_CHN =>	"佛&nbsp;光&nbsp;祝&nbsp;照&nbsp;受&nbsp;益&nbsp;者",
			SESS_LANG_ENG =>	"Well-blessing Recipient's Name" ),
		'W_Title' =>	array (
			SESS_LANG_CHN =>	"往&nbsp;生&nbsp;親&nbsp;友&nbsp;稱&nbsp;謂",
			SESS_LANG_ENG =>	"Title of the Deceased; e.g., Great Grand xxx" ),
		'W_Name' =>	array (
			SESS_LANG_CHN =>	"往&nbsp;生&nbsp;親&nbsp;友&nbsp;姓&nbsp;名",
			SESS_LANG_ENG =>	"Full Name of the Deceased" ),
		'deceasedDate' => array (
			SESS_LANG_CHN =>	"一年內的往生日期<br/>年(西元)&nbsp;&ndash;&nbsp;月&nbsp;&ndash;&nbsp;日",
			SESS_LANG_ENG =>	"Deceased Date (within 12 months): YYYY-MM-DD"	),
		'R_Title' => array (
			SESS_LANG_CHN =>	"陽&nbsp;上&nbsp;啟&nbsp;請&nbsp;人&nbsp;稱&nbsp;謂",
			SESS_LANG_ENG =>	"Requestor's Title; e.g., Great Grand Nephew" ),
		'W_Requestor' => array (
			SESS_LANG_CHN =>	"陽&nbsp;上&nbsp;啟&nbsp;請&nbsp;人&nbsp;姓&nbsp;名",
			SESS_LANG_ENG =>	"Requestor's Full Name" ),
		'L_Name' => array (
			SESS_LANG_CHN =>	"祖&nbsp;先&nbsp;姓&nbsp;氏",
			SESS_LANG_ENG =>	"Ancestor's Surname; e.g., Johnson" ),
		'L_Requestor' =>	array (
			SESS_LANG_CHN =>	"後&nbsp;代&nbsp;子&nbsp;孫&nbsp;啟&nbsp;請&nbsp;人",
			SESS_LANG_ENG =>	"Decendent Requestor's Full Name" ),
		'Y_Requestor' =>	array (
			SESS_LANG_CHN =>	"陽&nbsp;上&nbsp;有&nbsp;緣&nbsp;啟&nbsp;請&nbsp;人",
			SESS_LANG_ENG =>	"Requestor's Full Name" ),
		'D_Name' =>	array (
			SESS_LANG_CHN =>	"地&nbsp;基&nbsp;主&nbsp;神&nbsp;靈&nbsp;所&nbsp;在&nbsp;地",
			SESS_LANG_ENG =>	"Address of the Site Being Blessed by the Site Guardians" ),
		'D_Requestor' =>	array (
			SESS_LANG_CHN =>	"陽&nbsp;上&nbsp;啟&nbsp;請&nbsp;人",
			SESS_LANG_ENG =>	"Requestor's Full Name" ),
		'Deceased' =>	array (
			SESS_LANG_CHN =>	"親&nbsp;友&nbsp;、&nbsp;往&nbsp;生&nbsp;者&nbsp;或&nbsp;神&nbsp;靈&nbsp;稱&nbsp;謂&nbsp;及&nbsp;姓&nbsp;名",
			SESS_LANG_ENG =>	"Relationship &amp; Name of the Deceased" ),
		'Requestor' =>	array (
			SESS_LANG_CHN =>	"未&nbsp;亡&nbsp;人&nbsp;或&nbsp;陽&nbsp;上&nbsp;啟&nbsp;請&nbsp;人&nbsp;姓&nbsp;名&nbsp;及&nbsp;關&nbsp;係",
			SESS_LANG_ENG =>	"Survivor's / Applicant's / Requestor's Full Name &amp; Relationship" ),
	);
	return ( $_htmlNames[ $_dbName ][ $_sessLang ]  );
} // _dbName_2_htmlName()

function readPwParam( $_dbInfo ) {
	/*
	 * Ajax Receiver switches on 'URL', 'notActive', 'pwPlqDate', 'errCount'
	 */
	global $_db, $_SESSION, $_errCount, $_errRec;
	$rpt = array();
	
	if ( isset($_SESSION[ 'pwPlqDate' ]) ) {
		$rpt[ 'pwPlqDate' ] = $_SESSION[ 'pwPlqDate' ];
		$rpt[ 'rtrtDate' ] = $_SESSION[ 'rtrtDate' ];
		return $rpt;
	}
	$tblName = $_dbInfo[ 'tblName' ];
	$sql = "SELECT * FROM $tblName WHERE `isActive`;";
	$rslt = $_db->query( $sql );
	if ( $_db->errno ) {
		$_errCount++;
		$_errRec[] = __FUNCTION__ . "()\t" . __LINE__
							. ":\t{$_db->error} on reading Retreat Parameters while executing: '{$sql}'\n";
		$rpt[ 'errCount' ] = $_errCount;
		$rpt[ 'errRec' ] = $_errRec;
		return $rpt;
	}
	switch ( $rslt->num_rows ) {
		case 0:
			$rpt[ 'notActive' ] = true;
			return $rpt;
		case 1:
			$rtrtDate = $rslt->fetch_all(MYSQLI_ASSOC)[0][ 'rtrtDate' ];
			$rpt[ 'rtrtDate' ] = date( "Y-m-d", strtotime( $rtrtDate ) );
			$rpt[ 'pwPlqDate' ] = date( "Y-m-d", strtotime( $rtrtDate . " -1 year" ) );
			$_SESSION[ 'pwPlqDate' ] = $rpt[ 'pwPlqDate' ];
			$_SESSION[ 'rtrtDate' ] = $rtrtDate;
			return $rpt;
		default:
			$_errCount++;
			$_errRec[] = __FUNCTION__ . "()\t" . __LINE__
								 . "{$rslt->num_rows} found in Table '{$tblName}'\n";
			$rpt[ 'errCount' ] = $_errCount;
			$rpt[ 'errRec' ] = $_errRec;
			return $rpt;
	} // switch()		
} // function readPwParam()

function constructTblData ( $rows, $dbTblName ) { // $rows =  $mysqlresult->fetch_all( MYSQLI_ASSOC )
	global $_sessLang;
	
	if ( $rows == null ) { // construct an empty data table with an empty row
		$fldN = getPaiWeiTblFlds( $dbTblName );
		foreach( $fldN as $colName ) {
			$row[ $colName ] = '';
		}
		$rows[0] = $row;
	}
	
	$cellWidth = (int)( 76 / ( sizeof($rows[0]) - 1 ) );
	
  $tpl = new HTML_Template_IT("./Templates");
  $tpl->loadTemplatefile("pwTblData.tpl", true, true);
  
  $tpl->setCurrentBlock("data_tbl") ;
  $tpl->setVariable("dbTblName", $dbTblName );
  $rowCount = 0;
  foreach( $rows as $row ) {
  	$rowCount++;
  	$tpl->setCurrentBlock("data_row");
  	$i = 0;
  	foreach ( $row as $key => $val ) {
  		if ( $i == 0 ) {
  			$tpl->setVariable("tupKeyN", $key);
  			$tpl->setVariable("tupKeyV", $val);
  			$i++; continue;
  		}
			$tpl->setCurrentBlock("data_cell");
			if ( $rowCount == 1 ) $tpl->setVariable("cellWidth", "{$cellWidth}%;" ) ;
			$tpl->setVariable("dbFldN", $key); 
			$tpl->setVariable("dbFldV", $val); 
	    $tpl->parse("data_cell");  		
  	} // $row
  	$tpl->setCurrentBlock("dataEditCol");

		if ( $_sessLang == SESS_LANG_CHN ) {
			$tpl->setVariable( "editBtnTxt", "更改");
			$tpl->setVariable( "delBtnTxt", "刪除");
		} else {
			$tpl->setVariable("editBtnTxt", "Edit");
			$tpl->setVariable("delBtnTxt", "Delete");
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
	global $_sessUsr, $_sessLang;
	$fldN = getPaiWeiTblFlds( $dbTblName );
  $tpl = new HTML_Template_IT("./Templates");
  $tpl->loadTemplatefile("pwTblHeader.tpl", true, true);
  $tpl->setCurrentBlock("hdr_tbl") ;
	$tpl->setVariable("numCols", sizeof($fldN) ) ;
  $tpl->setVariable("htmlTblName", _dbName_2_htmlName( $dbTblName ) ) ;
  $tpl->setVariable("Who", $_sessUsr ) ;

	$cellWidth = (int)( 76 / ( sizeof($fldN) - 1) );
	$i = 0;
	foreach ( $fldN as $key ) {
		// first field is the tuple key; not visible to the users
		if ( $i == 0 ) { $i++; continue; }

		$tpl->setCurrentBlock("hdr_cell");
		$tpl->setVariable("cellWidth", "{$cellWidth}%;" ) ;
		$tpl->setVariable("htmlFldName", _dbName_2_htmlName( $key ) ) ;
    $tpl->parse("hdr_cell");	
	}
	
	$tpl->setCurrentBlock("dataEditCol");
	if ( $_sessLang == SESS_LANG_CHN) {
		$tpl->setVariable("addBtnTxt", '加行輸入');
		$tpl->setVariable("srchBtnTxt", '搜尋');
	} else {
		$tpl->setVariable("addBtnTxt", 'AddInputRow');
		$tpl->setVariable("srchBtnTxt", 'Search');
	}	 	
	$tpl->parse("dataEditCol");
  $tpl->parse("hdr_tbl");
  $tmp = preg_replace( "/(^[\r\n]*|[\r\n]+)[\s\t]*[\r\n]+/", "\n", $tpl->get() );
	return preg_replace( "/(^\t*)/", "  ", $tmp );
} // constructTblHeader()

/**********************************************************
 *				For dbREAD																			*
 **********************************************************/
function getTblData ( $dbInfoX ) {
	/*
	 * The receiving AJAX switches on cases: 'URL', 'myDataHdr', 'myData', 'myDataSize'
	 */
	global $_db;
	$rpt = array();
	$tblSize = 0;

	$tblName = $dbInfoX[ 'tblName' ];
	$strtRec = ( $dbInfoX[ 'pgNbr' ] - 1 ) * 30;
	if ( $strtRec < 0 ) { $strtRec = 0; } 
/*	$numRec = $dbInfoX[ 'numRec' ]; */
	$pwRqstr = $dbInfoX[ 'pwRqstr' ];
//	$inclHdr = $dbInfoX[ 'inclHdr' ];

	$sql = "LOCK TABLES {$tblName} READ, pw2Usr READ;";
	$_db->query( $sql );
	$sql = "SELECT * FROM {$tblName} WHERE ID IN "
				.	"(SELECT pwID FROM pw2Usr WHERE TblName = \"{$tblName}\" AND pwUsrName = \"{$pwRqstr}\") "
				. "ORDER BY ID "
//				. "LIMIT $strtRec, $numRec "
				. ";";
	$rslt = $_db->query( $sql );
	$tblSize = $rslt->num_rows;
	$rows = $rslt->fetch_all( MYSQLI_ASSOC );
	$rslt->free();
	$sql = "UNLOCK TABLES;";
	$_db->query( $sql );	

/*	if ( $inclHdr ) { */
		$rpt [ 'myDataHdr' ] = constructTblHeader( $tblName );
/*	} */
  if ( $tblSize == 0 ) {
  	$rows = null;
  }
	$rpt [ 'myData' ] = constructTblData( $rows, $tblName );
	$rpt [ 'myDataSize' ] = $tblSize;
	return $rpt;
} // getTblData()

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
function delTblData( $dbInfo ) {
	/*
	 * The receiving AJAX switch on 'URL', 'delSUCCESS', 'errCount'
	 */
	global $_db, $_errRec, $_errCount, $_delCount;
	$rpt = array();

	$tblName = $dbInfo['tblName'];
	$_db->autocommit(false);
	$_db->begin_transaction(MYSQLI_TRANS_START_WITH_CONSISTENT_SNAPSHOT);
	$sql = "LOCK TABLES {$tblName}, pw2Usr;";
	$_db->query( $sql );	
	if ( ! deletePaiWeiTuple( $dbInfo['tblName'],
														$dbInfo['tblFlds'],
														$dbInfo['pwRqstr' ]) ) {
		$_db->rollback();
		$rpt [ 'errCount' ] = $_errCount;
		$rpt [ 'errRec' ] = $_errRec;
		return $rpt;
	}
	$_db->commit();
	$sql = "UNLOCK TABLES;";
	$_db->query( $sql );
	$_db->autocommit(true);
	$rpt [ 'delSUCCESS' ] = "{$_delCount} records deleted";	
	return $rpt;
} // delTblData()

/**********************************************************
 *				For dbINS																				*
 **********************************************************/
function insTblData( $dbInfo ) {
	/*
	 * The receiving AJAX switches on 'URL', 'insSuccess', 'errCount', 'dupCount'
	 */
	global $_db, $_errRec, $_errCount, $_insCount, $_dupCount, $_dupRec;
	$rpt = array();

	$tblName = $dbInfo['tblName'];
	$_db->autocommit(false);
	$_db->begin_transaction(MYSQLI_TRANS_START_WITH_CONSISTENT_SNAPSHOT);
	$sql = "LOCK TABLES {$tblName}, pw2Usr;";
	$_db->query( $sql );
	$tupID = insertPaiWeiTuple(	$dbInfo['tblName'],
															$dbInfo['tblFlds'],
															$dbInfo['pwRqstr' ],
															null );
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
	$_db->query( $sql );	$_db->autocommit(true);
	$rpt [ 'insSUCCESS' ] = $tupID;
	return $rpt;
} // insTblData()

/**********************************************************
 *				For dbUPD																				*
 **********************************************************/
function updTblData( $dbInfo ) {
	/*
	 * The receiving AJAX switches on 'URL', 'updSUCCESS', 'errCount'
	 */
	global $_db, $_errCount, $_errRec, $_updCount;
	$rpt = array();

	$tblName = $dbInfo['tblName'];
	$_db->autocommit(false);
	$_db->begin_transaction(MYSQLI_TRANS_START_WITH_CONSISTENT_SNAPSHOT);
	$sql = "LOCK TABLES {$tblName}, pw2Usr;";
	$_db->query( $sql );
	if ( ! updatePaiWeiTuple( $dbInfo['tblName'],
														$dbInfo['tblFlds'],
														$dbInfo['pwRqstr' ] ) ) {
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
} // updTblData()

/**********************************************************
 *								 Main Functional Code										*
 **********************************************************/
$_dbReq = $_POST[ 'dbReq' ];

$_dbInfo = json_decode( $_POST [ 'dbInfo' ], true );
switch ( $_dbReq ) {
	case 'dbREADpwParam':
		echo json_encode( readPwParam( $_dbInfo ), JSON_UNESCAPED_UNICODE );
		break;
	case 'dbREAD':
		echo json_encode( getTblData ( $_dbInfo ), JSON_UNESCAPED_UNICODE );				
		break;
	case 'dbSEARCH':
		echo json_encode ( srchTblData( $_dbInfo ), JSON_UNESCAPED_UNICODE );
		break;
	case 'dbINS':
		echo json_encode ( insTblData( $_dbInfo ), JSON_UNESCAPED_UNICODE );
		break;
	case 'dbDEL':
		echo json_encode ( delTblData( $_dbInfo ), JSON_UNESCAPED_UNICODE );
		break;
	case 'dbUPD':
		echo json_encode ( updTblData ( $_dbInfo ), JSON_UNESCAPED_UNICODE );
		break;
} // switch()

$_db->close();
?>
