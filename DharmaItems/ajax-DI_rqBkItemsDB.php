<?php
	require_once( '../pgConstants.php' );
	require_once( 'dbSetup.php' );
	require_once( 'DI_shippAddr_DBfuncs.php' );
	require_once( 'ChkTimeOut.php' );
	
	$_sessType = $_SESSION[ 'sessType' ];
	$_sessLang = $_SESSION[ 'sessLang' ];
	$_sessUsr = $_SESSION[ 'usrName' ];
	$_icoName = isset($_SESSION[ 'icoName' ]) ? $_SESSION[ 'icoName' ] : null;
	$_useChn = ( $_SESSION[ 'sessLang' ] == SESS_LANG_CHN );

function _dbName_2_htmlName ( $_dbName ) {
	global $_sessLang;
	$_htmlNames = array (
		'dt_diMOP' =>	array (
			SESS_LANG_CHN => "結緣法寶申請辦法",
			SESS_LANG_ENG => "Dharma Items Application Requirements" ),
		'dt_diAlert' => array (
			SESS_LANG_CHN => "*** 請您仔細閱讀下列注意事項 ***",
			SESS_LANG_ENG => "*** Please read the following carefully ***" ),
		'dt_diShipping' => array (
			SESS_LANG_CHN => "請填具結緣法寶寄送地址",
			SESS_LANG_ENG => "Please Fill Out Dharma Items Shipping Information" ),
		'dt_diAppForm' => array (
			SESS_LANG_CHN => "請填具結緣法寶申請表",
			SESS_LANG_ENG => "Please Fille Out Dharma Item Application Form" ),
		'di_shippingFormName' => array (
			SESS_LANG_CHN => "結緣法寶寄送地址",
			SESS_LANG_ENG => "Dharma Items Shipping Information" ),
		'addr_orgNm' =>	array (
			SESS_LANG_CHN => "個人、組織、或活動的全名",
			SESS_LANG_ENG => "Full Name, Org. Name, or Activity Name" ),
		'addr_telNo' =>	array (
			SESS_LANG_CHN => "聯絡電話",
			SESS_LANG_ENG => "Telphone No." ),
		'addr_Email' =>	array (
			SESS_LANG_CHN => "電郵地址",
			SESS_LANG_ENG => "Email" ),
		'addr_streetNum' =>	array (
			SESS_LANG_CHN => "街道名稱及號碼",
			SESS_LANG_ENG => "Street Name and Number" ),
		'addr_unitNum' =>	array (
			SESS_LANG_CHN => "單位號碼",
			SESS_LANG_ENG => "Unit Number" ),
		'addr_cityName' => array (
			SESS_LANG_CHN => "城市名稱",
			SESS_LANG_ENG => "City" ),
		'addr_stateName' =>	array (
			SESS_LANG_CHN => "(美國)州名",
			SESS_LANG_ENG => "US State Name" ),
		'addr_zipCode' =>	array (
			SESS_LANG_CHN => "郵遞區號",
			SESS_LANG_ENG => "Zip Code" )
	);
	return ( $_htmlNames[ $_dbName ][ $_sessLang ]  );
} // _dbName_2_htmlName()

/**********************************************************
 *				  For dbReadBkList						  *
 **********************************************************/
function readBkList( $dbInfoX ) {
	$tblName = $dbInfoX[ 'tblName' ];
	$usrName = $dbInfoX[ 'usrName' ];
	switch ( $tblName ) {
	case 'INVT_BK_C':
		return readBkList_C( $tblName, $usrName, 1 );
	case 'INVT_BK_E':
		return readBkList_E( $tblName, $usrName );
	}
} // function readBkList()

/**********************************************************
 *	Read/Return Chinese Book List: with $strokes of the first Title Character 						  *
 **********************************************************/
function readBkList_C( $tblName, $usrName, $strokes ) {
	global $_db, $_sessLang, $_SESSION, $_useChn;
	$rpt = array();

	$_db->query("LOCKTABLE `{$tblName}` READ;");
	/* determine range of Strokes */
	$rslt = $_db->sql( "SELECT MAX(`Strokes`) FROM `{$tblName}`;" );
	if ( $_db->errno ) {
		$_errCount++;
		$_errRec[] = ( ( $_useChn ) ? "資料庫有錯誤 (".$_db->errno.")! " : "Database Error (".$_db->errno.")! " )
					. "\n\tExecuting SQL: '" . $sql . "'";
		$_db->query("UNLOCK TABLES;");
		return $rpt;
	}

	$sql = "SELECT `invtID`, `Strokes`, `Title`, `Author` FROM `{$tblName}` ORDER BY 'Strokes';";
	$rslt = $_db->query( $sql );
	if ( $_db->errno ) {
		$_errCount++;
		$_errRec[] = ( ( $_useChn ) ? "資料庫有錯誤 (".$_db->errno.")! " : "Database Error (".$_db->errno.")! " )
					. "\n\tExecuting SQL: '" . $sql . "'";
		$_db->query("UNLOCK TABLES;");
		return $rpt;
	}
	$rows = $rslt->fetch_all( MYSQLI_ASSOC );
	$rslt->free();
	$_db->query("UNLOCK TABLES;");
	$rpt[ 'addrIDs' ] = $addrIDs;
	$rpt[ 'shippingForm' ] = constructShippingForm( $rows[0], $addrIDs[ 'prim' ] );
	return ( $rpt );
} // function readAddrForm()

function constructShippingForm ( $row, $primAddrID ) {
	global $_sessLang, $_useChn;
	
	$tpl = new HTML_Template_IT("./Templates");
	$tpl->loadTemplatefile("shippingInfoForm.tpl", true, true);
	$tpl->setCurrentBlock("shippingInfoForm");
	if ( $row != null ) {
		$tpl->setVariable("AddrIDV", $row[ 'AddrID' ] );
		$tpl->setVariable("AddresseeV", $row[ 'Addressee' ] );
		$tpl->setVariable("TelNoV", $row[ 'TelNo' ] );
		$tpl->setVariable("EmailV", $row[ 'Email' ] );
		$tpl->setVariable("StNumV", $row[ 'StNum' ] );
		$tpl->setVariable("UnitV", $row[ 'Unit' ] );
		$tpl->setVariable("CityV", $row[ 'City' ] );
		$tpl->setVariable("US_StateV", $row[ 'US_State' ] );
		$tpl->setVariable("ZipCodeV", $row[ 'ZipCode' ] );
		$tpl->setVariable("PrimV", ($row[ 'AddrID' ] == $primAddrID ) ? 'checked' : null );
	}
	$tpl->setVariable("shippingFormName", _dbName_2_htmlName( 'di_shippingFormName' ) );
	$tpl->setVariable("orgNameLbl", _dbName_2_htmlName( 'addr_orgNm' ) );
	$tpl->setVariable("telNoLbl", _dbName_2_htmlName( 'addr_telNo' ) );
	$tpl->setVariable("emailLbl", _dbName_2_htmlName( 'addr_Email' ) );
	$tpl->setVariable("streetNumLbl", _dbName_2_htmlName( 'addr_streetNum' ) );
	$tpl->setVariable("unitNumLbl", _dbName_2_htmlName( 'addr_unitNum' ) );
	$tpl->setVariable("cityNameLbl", _dbName_2_htmlName( 'addr_cityName' ) );
	$tpl->setVariable("stateNameLbl", _dbName_2_htmlName( 'addr_stateName' ) );
	$tpl->setVariable("zipCodeLbl", _dbName_2_htmlName( 'addr_zipCode' ) );
	$primLblV = $_useChn ? "設定此為主要地址" : "This is <b>primary</b> address";
	$altInfoV = $_useChn ? "更新/添加 另一寄送地址" : "Update/Add <b>alternative</b> address";
	$delBtnV = $_useChn ? "刪除 此一寄送地址" : "Delete this address";
	$updSaveBtnV = $_useChn ? "保存/更新 寄送地址" : "Save/Update Shipping Address";
	$ldAppFormV = $_useChn ? "繼續：填具法寶申請表" : "Next: Fill Application Form";
	$tpl->setVariable("primLblV", $primLblV );
	$tpl->setVariable("altInfoV", $altInfoV );
	$tpl->setVariable("updSaveBtnV", $updSaveBtnV );
	$tpl->setVariable("delBtnV", $delBtnV );
	$tpl->setVariable("ldAppFormV", $ldAppFormV );
	$tpl->parse("shippingInfoForm");
	return( preg_replace( "/(^[\r\n]*|[\r\n]+)[\s\t]*[\r\n]+/", "\n", $tpl->get() ) );	
} // constructShippingForm()

/**********************************************************
 *								 Main Functional Code										*
 **********************************************************/
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