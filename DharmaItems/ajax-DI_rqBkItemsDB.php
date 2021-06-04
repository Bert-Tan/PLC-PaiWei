<?php
	require_once( 'ajax-DI_commonDB.php' );
	require_once( 'DI_rqBkItems_DBfuncs.php' );

function _dbName_2_bkTblhtmlName ( $_dbName ) {
	global $_sessLang;
	$_htmlNames = array ( // 'dt' stands for 'data title'
		'bkTblName' =>	array (
			SESS_LANG_CHN => "本館館藏中文結緣書目",
			SESS_LANG_ENG => "English Book Items in Our Collection" ),
		'chChkBox' => array (	// 'ch' stands for 'column header'
			SESS_LANG_CHN => "請點擊<br/>選項",
			SESS_LANG_ENG => "Click to Select" ),
		'chBiHua' => array (
			SESS_LANG_CHN => "書名筆畫",
			SESS_LANG_ENG => "Strokes of the First Character in the Title" ),	
		'chTitle' =>	array (
			SESS_LANG_CHN => "書&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;名",
			SESS_LANG_ENG => "Title" ),	
		'chAuthor' =>	array (
			SESS_LANG_CHN => "作者、翻譯者、編譯者、或出版者",
			SESS_LANG_ENG => "Author, Translator, Collator, Publisher" ),	
	);

	return ( $_htmlNames[ $_dbName ][ $_sessLang ]  );	
} // function _dbName_2_bkTblhtmlName()

/**********************************************************
 *  Helping function: Chinese BiHua (Strokes) Selection	  *
 **********************************************************/
function constructStrokeSelector( $selected ) {
	global $_strkRange;
	$min = $_strkRange[0];
	$max = $_strkRange[1];

	$tpl = new HTML_Template_IT("./Templates");
	$tpl->loadTemplatefile("invtBkChnBiHuaSel.tpl", true, true);
	$tpl->setCurrentBlock("Selection");
	for ( $i = $min; $i <= $max; $i++ ) {
		$tpl->setCurrentBlock("selOption");
		$tpl->setVariable("strkCntV", $i );
		if ( $i == $selected ) {
			$tpl->setVariable("selectedV", 'selected' );
		}
		$tpl->parse("selOption");
	} // for loop
	$tpl->parse("Selection");
	$tmp = preg_replace( "/(^[\r\n]*|[\r\n]+)[\s\t]*[\r\n]+/", "\n", $tpl->get() );
	return preg_replace( "/(^\t*)/", "  ", $tmp );
} // function constructStrokeSelector()

function constructBkListTbl( $tblName ) { // function to use CSS TBODY Scroll trick
	global $_strkRange, $_bkRows;
	$cvChkBox = "<input type=checkbox>"; // cell value: checkbox input

	$fldN = $fldN = getDBTblFlds( $tblName );
	$colS = ( in_array( 'Strokes', $fldN ) ) ? 4 : 3; // determine Column Span; set it at the last action
	$tpl = new HTML_Template_IT("./Templates");
	$tpl->loadTemplatefile("bkListTbl.tpl", true, true);
	$tpl->setCurrentBlock("BL_Tbl");
	$tpl->setVariable("dbTblName", $tblName);
	$tpl->setVariable("htmlTblNameV", _dbName_2_bkTblhtmlName( 'bkTblName' ));
	$tpl->setVariable("colSpan", $colS);
	$tpl->setCurrentBlock("BL_hdrRow");
	foreach ( $fldN as $colName ) { // thru each column
		$tpl->setCurrentBlock("BL_hdrCell");
		switch ( $colName ) {
		case 'invtID': // invisible to the users; use this column for checkbox in the Table Body
			$tpl->setVariable("cellV", _dbName_2_bkTblhtmlName('chChkBox'));
			break;
		case 'Strokes': // constructStrokeSelector() uses $_strkRange Global to build dropdown list
			$cellV = _dbName_2_bkTblhtmlName('chBiHua') . '<br/>' . constructStrokeSelector( null );
			$tpl->setVariable("cellV", $cellV );
			break;
		case 'Title':
			$tpl->setVariable("cellV", _dbName_2_bkTblhtmlName('chTitle'));
			break;
		case 'Author':
			$tpl->setVariable("cellV", _dbName_2_bkTblhtmlName('chAuthor'));
			break;
		default: // ignore the remaining fields
			break;
		}
		$tpl->parse("BL_hdrCell");
	}; // foreach() loop thru columns
	$tpl->parse("BL_hdrRow");
	// now book list data rows; use the data in the $_bkRec Global
	foreach ( $_bkRows as $row ) {
		$tpl->setCurrentBlock("BL_dataRow");
		foreach ( $row as $key => $val ) {
			if ( $key == 'invtID' ) {
				$tpl->setVariable("tupKeyN", $key);
				$tpl->setVariable("tupKeyV", $val);
			}

			$tpl->setCurrentBlock("BL_dataCell");			
			$tpl->setVariable( "cellV", ( $key == 'invtID' ) ? $cvChkBox : $val );
			$tpl->parse("BL_dataCell");
		} // foreach() loop thru data columns
		$tpl->parse("BL_dataRow");
	}
	$tpl->parse("BL_Tbl");
	$tmp = preg_replace( "/(^[\r\n]*|[\r\n]+)[\s\t]*[\r\n]+/", "\n", $tpl->get() );
	return preg_replace( "/(^\t*)/", "  ", $tmp );
} // function constructBkListTbl()

function constructBkRowsOnly() {
	global $_bkRows;

	$tpl = new HTML_Template_IT("./Templates");
	$tpl->loadTemplatefile("bkListTbl.tpl", true, true);
	foreach ( $_bkRows as $row ) {
		$tpl->setCurrentBlock("BL_dataRow");
		foreach ( $row as $key => $val ) {
			if ( $key == 'invtID' ) {
				$tpl->setVariable("tupKeyN", $key);
				$tpl->setVariable("tupKeyV", $val);
			}

			$tpl->setCurrentBlock("BL_dataCell");			
			$tpl->setVariable( "cellV", ( $key == 'invtID' ) ? $cvChkBox : $val );
			$tpl->parse("BL_dataCell");
		} // foreach() loop thru data columns
		$tpl->parse("BL_dataRow");
	}
	$tmp = preg_replace( "/(^[\r\n]*|[\r\n]+)[\s\t]*[\r\n]+/", "\n", $tpl->get() );
	return preg_replace( "/(^\t*)/", "  ", $tmp );
} // function constructBkRowsOnly()

/**********************************************************
 *				  For dbReadBkList						  *
 **********************************************************/
function readBkList( $dbInfoX ) {
	global $_db, $_errCount, $_errRec;
	$rpt = array();

	$tblName = $dbInfoX[ 'tblName' ];
	$usrName = $dbInfoX[ 'usrName' ];
	$stroke = $dbInfoX[ 'stroke' ];
	$_db->query("LOCKTABLE `{$tblName}` READ;");
	if ( ! readInvt_BK( $tblName, $stroke ) ) {
		$_db->query("UNLOCK TABLES;");
		$rpt[ 'errCount' ] = $_errCount;
		$rpt[ 'errRec' ] = $_errRec;
		return $rpt;
	}

	$_db->query("UNLOCK TABLES;");

	$rpt[ 'BkList_Tbl' ] = constructBkListTbl( $tblName );
	return $rpt;
} // function readBkList()

/********************************************************
 *				 Main Functional Code					*
 ********************************************************/
$_dbReq = $_POST[ 'dbReq' ];
$_dbInfo = isset( $_POST[ 'dbInfo' ]) ? json_decode( $_POST [ 'dbInfo' ], true ) : null;

switch ( $_dbReq ) {
	case 'dbReadBkList':
		echo json_encode( readBkList( $_dbInfo ), JSON_UNESCAPED_UNICODE );
		break;
	case 'dbReadAddrForm':
		echo json_encode( readAddrForm( $_dbInfo ), JSON_UNESCAPED_UNICODE );				
		break;
	case 'dbUPD_ShippingAddr':
		echo json_encode ( updShippingAddr( $_dbInfo ), JSON_UNESCAPED_UNICODE );
		break;
	case 'dbINS_ShippingAddr':
		echo json_encode ( insShippingAddr( $_dbInfo ), JSON_UNESCAPED_UNICODE );
		break;
	case 'dbDEL_ShippingAddr':
		echo json_encode ( delShippingAddr( $_dbInfo ), JSON_UNESCAPED_UNICODE );
		break;
} // switch()

$_db->close();
?>