<?php
	require_once( '../pgConstants.php' );
	require_once( 'dbSetup.php' );
	require_once( 'PaiWei_DBfuncs.php' );
	require_once( 'ChkTimeOut.php' );
	
	$_sessType = $_SESSION[ 'sessType' ];
	$_sessLang = $_SESSION[ 'sessLang' ];
	$_sessUsr = $_SESSION[ 'usrName' ];
	$_icoName = isset($_SESSION[ 'icoName' ]) ? $_SESSION[ 'icoName' ] : null;
	$useChn = ( $_SESSION[ 'sessLang' ] == SESS_LANG_CHN );

function _dbName_2_htmlName ( $_dbName ) {
	global $_sessLang;
	$_htmlNames = array (
		'C001A' =>	array (
			SESS_LANG_CHN =>	"佛&nbsp;光&nbsp;祝&nbsp;照&nbsp;祈&nbsp;福&nbsp;消&nbsp;災",
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
			SESS_LANG_CHN =>	"佛&nbsp;力&nbsp;超&nbsp;薦&nbsp;累&nbsp;劫&nbsp;冤&nbsp;親&nbsp;債&nbsp;主",
			SESS_LANG_ENG =>	"Dedicate Merit to Karmic Creditors" ),
		'D001A' =>	array (
			SESS_LANG_CHN =>	"佛&nbsp;力&nbsp;超&nbsp;薦&nbsp;地&nbsp;基&nbsp;主",
			SESS_LANG_ENG =>	"Dedicate Merit to Site Guardians" ),
		'DaPaiWei' => array (
			SESS_LANG_CHN =>	"佛&nbsp;力&nbsp;超&nbsp;薦&nbsp;近&nbsp;期&nbsp;"
											.	"<span style=\"color: yellow;\">(12 個月之內)</span>&nbsp;往&nbsp;生&nbsp;親&nbsp;友",
			SESS_LANG_ENG =>	"Dedicate Merit to Recently "
											.	"<span style=\"color: yellow;\">(within 12 months)</span> Deceased" ),
		'DaPaiWeiRed' =>	array (
			SESS_LANG_CHN =>	"紅&nbsp;色&nbsp;大&nbsp;牌&nbsp;位",
			SESS_LANG_ENG =>	"RED DaPaiWei" ),
		'C_Name' =>	array (
			SESS_LANG_CHN =>	"佛&nbsp;光&nbsp;祝&nbsp;照&nbsp;受&nbsp;益&nbsp;者",
			SESS_LANG_ENG =>	"Well-blessing Recipient's Name" ),
		'W_Title' =>	array (
			SESS_LANG_CHN =>	"往&nbsp;生&nbsp;親&nbsp;友&nbsp;稱&nbsp;謂",
			SESS_LANG_ENG =>	"Title of the Deceased;<br>e.g., Grand xxx" ),
		'W_Name' =>	array (
			SESS_LANG_CHN =>	"往&nbsp;生&nbsp;親&nbsp;友&nbsp;姓&nbsp;名",
			SESS_LANG_ENG =>	"Full Name of<br>the Deceased" ),
		'deceasedDate' => array (
			SESS_LANG_CHN =>	"往生日期<br/>(西元 年-月-日<br/>或 月/日/年)",
			SESS_LANG_ENG =>	"Deceased Date<br>(within 12 months)<br>YYYY-MM-DD or MM/DD/YYYY"	),
		'R_Title' => array (
			SESS_LANG_CHN =>	"陽&nbsp;上&nbsp;啟&nbsp;請&nbsp;人&nbsp;稱&nbsp;謂",
			SESS_LANG_ENG =>	"Requestor's Title;<br>e.g., Grand yyy" ),
		'W_Requestor' => array (
			SESS_LANG_CHN =>	"陽&nbsp;上&nbsp;啟&nbsp;請&nbsp;人&nbsp;姓&nbsp;名",
			SESS_LANG_ENG =>	"Requestor's<br>Full Name" ),
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

function readPWTitleList( $tblName ) {
	global $_db, $_sessLang, $_errCount, $_errRec;

	$lang = ( $_sessLang == SESS_LANG_CHN ) ? "Chn" : "Eng";
	$sql = "LOCK TABLES {$tblName} READ;";
	$_db->query( $sql );
	$sql = "SELECT tName FROM {$tblName} WHERE tLang = \"{$lang}\";";
	$rslt = $_db->query( $sql );
	$_db->query( "UNLOCK TABLES;" );
	$tNames = $rslt->fetch_all(MYSQLI_NUM);
	$tpl = new HTML_Template_IT("./Templates");
	$tpl->loadTemplatefile("titleChoice.tpl", true, true);	
	$listClass = ( $tblName == 'pwParam_wtList' ) ? 'wTitle' : 'rTitle';
	$pTitle = ( $tblName == 'pwParam_wtList' ) ? "W_Title" : "R_Title";
	$tpl->setCurrentBlock("Selection");
	$tpl->setVariable("selClass", $listClass );
	$tpl->setVariable("pTitle", $pTitle );
	foreach ( $tNames as $tName ) {
		$tpl->setCurrentBlock("Options");
		$tpl->setVariable("optV", $tName[ 0 ] );
		$tpl->parse("Options");
	}
	$tpl->parse("Selection");
	$tmp = preg_replace( "/(^[\r\n]*|[\r\n]+)[\s\t]*[\r\n]+/", "\n", $tpl->get() );
	return preg_replace( "/(^\t*)/", "  ", $tmp );
} // function readPWTitleList()

function readPwParam( $_dbInfo ) {
	/*
	 * Ajax Receiver switches on 'URL', 'notActive', 'pwPlqDate', 'errCount'
	 */
	global $_db, $_SESSION, $_errCount, $_errRec;
	$rpt = array();

	$rpt[ 'usrName'] = $_SESSION[ 'usrName' ];
	$rpt[ 'usrPass' ] = $_SESSION[ 'usrPass' ];
	$rpt[ 'sessType' ] = $_SESSION[ 'sessType' ];
	$rpt[ 'sessLang' ] = $_SESSION[ 'sessLang' ];
	$rpt[ 'icoName'] = isset($_SESSION[ 'icoName' ]) ? $_SESSION[ 'icoName' ] : null;
	$rpt[ 'tblName'] = isset($_SESSION[ 'tblName' ]) ? $_SESSION[ 'tblName' ] : null;
	if ( isset($_SESSION[ 'pwPlqDate' ]) ) {
		/*
		 * The session is active and the parameters had been read before; no need to access DB
		 */		
		$rpt[ 'pwPlqDate' ] = $_SESSION[ 'pwPlqDate' ];
		$rpt[ 'rtrtDate' ] = $_SESSION[ 'rtrtDate' ];
		$rpt[ 'wtList' ] = $_SESSION[ 'wtList' ];
		$rpt[ 'rtList' ] = $_SESSION[ 'rtList' ];
		return $rpt;
	}
	$tblName = $_dbInfo[ 'tblName' ]; /* pwParam */
	if ( $_SESSION[ 'sessType' ] == SESS_TYP_USR ) {
		$currDate = date("Y-m-d", time() );
		$sql = "SELECT * FROM $tblName WHERE \"{$currDate}\" <= `pwExpires`;";
	} else {
		$sql = "SELECT * FROM $tblName;";
	}	
	$rslt = $_db->query( $sql );	
	if ( $_db->errno ) {
		$_errCount++;
		$_errRec[] = __FUNCTION__ . "()\t" . __LINE__
							. ":\t{$_db->error} on reading Retreat Parameters while executing: '{$sql}'\n";
		$rpt[ 'errCount' ] = $_errCount;
		$rpt[ 'errRec' ] = $_errRec;
		return $rpt;
	}
	if ( $rslt->num_rows == 0 && $_SESSION[ 'sessType' ] == SESS_TYP_USR ) {
		$rpt[ 'notActive' ] = true; /* disable PaiWei Function for general users */
		return $rpt;
	}

	$rsltArray = $rslt->fetch_all(MYSQLI_ASSOC)[0];
	$rtrtDate = $rsltArray['rtrtDate'];	
	$lastRtrtDate = $rsltArray['lastRtrtDate'];	
	if ( ! isset($lastRtrtDate) )	$lastRtrtDate = "1970-01-01";	
	$rpt[ 'rtrtDate' ] = $rtrtDate;
	$rpt[ 'pwPlqDate' ] = date( "Y-m-d", strtotime( $rtrtDate . " -1 year" ) );
	$_SESSION[ 'pwPlqDate' ] = $rpt[ 'pwPlqDate' ];
	$_SESSION[ 'rtrtDate' ] = $rtrtDate;
	$_SESSION[ 'lastRtrtDate' ] = $lastRtrtDate;
	$_SESSION[ 'wtList' ] = readPWTitleList( 'pwParam_wtList' );
	$_SESSION[ 'rtList' ] = readPWTitleList( 'pwParam_rtList' );
	$rpt[ 'wtList' ] = $_SESSION[ 'wtList' ];
	$rpt[ 'rtList' ] = $_SESSION[ 'rtList' ];
	return $rpt;	
} // function readPwParam()

function constructTblData ( $rows, $dbTblName ) { // $rows =  $mysqlresult->fetch_all( MYSQLI_ASSOC )
	global $_sessLang, $_SESSION;
	
	if ( $rows == null ) { // construct an empty data table with an empty row
		$fldN = getPaiWeiTblFlds( $dbTblName );
		foreach( $fldN as $colName ) {
			$row[ $colName ] = '';
		}
		$rows[0] = $row;
	}	

	$lastRtrtDate = $_SESSION[ 'lastRtrtDate' ];
	
	$cellWidth = (int)( 72 / ( sizeof($rows[0]) - 1 ) );
	
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
			// first field is the tuple key
			if ( $i == 0 ) {
  				$tpl->setVariable("tupKeyN", $key);
  				$tpl->setVariable("tupKeyV", $val);
  				$i++; continue;
			}

			// last field is the request/validate date, used to enable/disable "valid" button
			if( $key == "timestamp" ) {
				$tpl->setCurrentBlock("dataEditCol");				
				if ( $_sessLang == SESS_LANG_CHN ) {
					$tpl->setVariable( "editBtnTxt", "更改");
					$tpl->setVariable( "delBtnTxt", "刪除");
					$tpl->setVariable( "dupBtnTxt", "複製");
					$tpl->setVariable( "validBtnTxt", "驗證");
				} else {
					$tpl->setVariable("editBtnTxt", "&nbsp;&nbsp;&nbsp;Edit&nbsp;&nbsp;&nbsp;");
					$tpl->setVariable("delBtnTxt", "&nbsp;&nbsp;Delete&nbsp;&nbsp;");
					$tpl->setVariable( "dupBtnTxt", "Duplicate");
					$tpl->setVariable( "validBtnTxt", "Validate");
				}
				$validStr = $val > $lastRtrtDate ? "disabled" : "";
				$tpl->setVariable("validStr", $validStr);
				$tpl->parse("dataEditCol");
				continue;
			}
			
			$tpl->setCurrentBlock("data_cell");
			if ( $rowCount == 1 ) $tpl->setVariable("cellWidth", "{$cellWidth}%;" ) ;
			$tpl->setVariable("dbFldN", $key); 
			$tpl->setVariable("dbFldV", $val); 
			$tpl->parse("data_cell");  							
		} // $row
	
		$tpl->parse("data_row");	  
	} // $rows
  
 	$tpl->parse("data_tbl");
	$tmp = preg_replace( "/(^[\r\n]*|[\r\n]+)[\s\t]*[\r\n]+/", "\n", $tpl->get() );
	return preg_replace( "/(^\t*)/", "  ", $tmp );
} // constructTblData()

function constructTblHeader( $dbTblName ) {
	global $_sessUsr, $_sessLang, $_icoName;
	$fldN = getPaiWeiTblFlds( $dbTblName );
  	$tpl = new HTML_Template_IT("./Templates");
  	$tpl->loadTemplatefile("pwTblHeader.tpl", true, true);
  	$tpl->setCurrentBlock("hdr_tbl") ;
	$tpl->setVariable("numCols", sizeof($fldN) ) ;
  	$tpl->setVariable("htmlTblName", _dbName_2_htmlName( $dbTblName ) ) ;
  	$tpl->setVariable("Who", $_sessUsr ) ;
  if ( $_icoName != null ) {
	$tpl->setVariable("ico", ";&nbsp;&nbsp;In Care Of:&nbsp;&nbsp;{$_icoName}" ) ;
  }

	$cellWidth = (int)( 72 / ( sizeof($fldN) - 1) );
	$i = 0;
	foreach ( $fldN as $key ) {
		// first field is the tuple key; not visible to the users
		if ( $i == 0 ) { $i++; continue; }
		// last field is the request/validate date, used to enable/disable "valid" button
		if ( $key == "timestamp" )	continue;

		$tpl->setCurrentBlock("hdr_cell");
		$tpl->setVariable("cellWidth", "{$cellWidth}%;" ) ;
		$tpl->setVariable("htmlFldName", _dbName_2_htmlName( $key ) ) ;
    $tpl->parse("hdr_cell");	
	}
	
	$tpl->setCurrentBlock("dataEditCol");
	if ( $_sessLang == SESS_LANG_CHN) {
		$tpl->setVariable("addBtnTxt", '加行輸入');
		$tpl->setVariable("srchBtnTxt", '&nbsp;&nbsp;搜&nbsp;&nbsp;尋&nbsp;&nbsp;');
		$tpl->setVariable("delAllBtnTxt", '全部刪除');
		$tpl->setVariable("validAllBtnTxt", '全部驗證');
	} else {
		$tpl->setVariable("addBtnTxt", 'Add&nbsp;&nbsp;Row');
		$tpl->setVariable("srchBtnTxt", '&nbsp;&nbsp;Search&nbsp;&nbsp;');
		$tpl->setVariable("delAllBtnTxt", '&nbsp;Delete All&nbsp;');
		$tpl->setVariable("validAllBtnTxt", 'Validate All');
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
	global $useChn;
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
	$rpt [ 'delSUCCESS' ] = ( $useChn ) ? "牌位資料刪除完畢！" : "Record Deleted!";	
	return $rpt;
} // delTblData()

/**********************************************************
 *				For dbDELX																			*
 **********************************************************/
function delTblUsrData( $dbInfo ) {
	/*
	 * The receiving AJAX switch on 'URL', 'delSUCCESS', 'errCount'
	 */
	global $_db, $_errRec, $_errCount, $_delCount;
	global $useChn;
	$rpt = array();

	$tblName = $dbInfo['tblName'];
	$_db->autocommit(false);
	$_db->begin_transaction(MYSQLI_TRANS_START_WITH_CONSISTENT_SNAPSHOT);
	$sql = "LOCK TABLES `{$tblName}` WRITE, `pw2Usr` WRITE;";
	$_db->query( $sql );	
	if ( ! deletePaiWeiUsrTuple( $dbInfo['tblName'], $dbInfo['pwRqstr' ] ) ) {
		$_db->rollback();
		$rpt [ 'errCount' ] = $_errCount;
		$rpt [ 'errRec' ] = $_errRec;
		return $rpt;
	}
	$_db->commit();
	$_db->query( "UNLOCK TABLES;" );
	$_db->autocommit(true);
	$rpt [ 'delSUCCESS' ] = ( $useChn ) ? "{$_delCount} 項牌位資料刪除完畢！" : "{$_delCount} Records Deleted!";	
	return $rpt;
} // delTblUsrData()

/**********************************************************
 *						For dbVALIDX					  *
 **********************************************************/
function validTblUsrData( $dbInfo ) {
	/*
	 * The receiving AJAX switch on 'URL', 'delSUCCESS', 'errCount'
	 */

	global $_db, $_errCount, $_errRec, $_validCount;
	global $useChn;
	$rpt = array();

	$tblName = $dbInfo['tblName'];
	$_db->autocommit(false);
	$_db->begin_transaction(MYSQLI_TRANS_START_WITH_CONSISTENT_SNAPSHOT);
	$sql = "LOCK TABLES {$tblName};";
	$_db->query( $sql );
	if ( ! validPaiWeiUsrTuple( $dbInfo['tblName'], $dbInfo['pwRqstr' ] ) ) {
		$_db->rollback();
		if ( $_errCount ) {
			$rpt [ 'errCount' ] = $_errCount;
			$rpt [ 'errRec' ] = $_errRec;
		}
		return $rpt;
	}
	$_db->commit();
	$sql = "UNLOCK TABLES;";
	$_db->query( $sql );
	$_db->autocommit(true);
	$rpt [ 'validSUCCESS' ] = ( $useChn ) ? "{$_validCount} 項牌位資料驗證完畢！" : "{$_validCount} Records Validated!";
	return $rpt;
} // validTblUsrData()

/**********************************************************
 *						For dbINS					*
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
	$_db->query( $sql );
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
	case 'dbDELX': /* delete table data that belongs to a specific user */
		echo json_encode ( delTblUsrData( $_dbInfo ), JSON_UNESCAPED_UNICODE );
		break;
	case 'dbUPD':
		echo json_encode ( updTblData ( $_dbInfo ), JSON_UNESCAPED_UNICODE );
		break;
	case 'dbVALIDX': /* valid PaiWei table data that belongs to a specific user */
		echo json_encode ( validTblUsrData( $_dbInfo ), JSON_UNESCAPED_UNICODE );
		break;
} // switch()

$_db->close();
?>
