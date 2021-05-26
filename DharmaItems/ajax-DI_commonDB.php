<?php
	require_once( '../pgConstants.php' );
	require_once( 'dbSetup.php' );
	require_once( 'ChkTimeOut.php' );
	
	$_sessType = $_SESSION[ 'sessType' ];
	$_sessLang = $_SESSION[ 'sessLang' ];
	$_sessUsr = $_SESSION[ 'usrName' ];
	$_icoName = isset($_SESSION[ 'icoName' ]) ? $_SESSION[ 'icoName' ] : null;
	$_useChn = ( $_SESSION[ 'sessLang' ] == SESS_LANG_CHN );

function _dbName_2_htmlName ( $_dbName ) {
	global $_sessLang;
	$_htmlNames = array ( // 'dt' stands for 'data title'
		'dt_diMOP' =>	array (
			SESS_LANG_CHN => "結緣法寶申請辦法",
			SESS_LANG_ENG => "Dharma Items Application Requirements" ),
		'dt_diAlert' => array (
			SESS_LANG_CHN => "*** 請您仔細閱讀下列注意事項 ***",
			SESS_LANG_ENG => "*** Please read the following carefully ***" ),
		'dt_diShippingTab' => array (
			SESS_LANG_CHN => "請填具結緣法寶寄送地址",
			SESS_LANG_ENG => "Please Fill Out Dharma Items Shipping Information" ),
		'dt_diBkTab' => array (
			SESS_LANG_CHN => "請填具結緣書目申請表",
			SESS_LANG_ENG => "Please Select Book Items From the List" ),
		'dt_diStatuesTab' => array (
			SESS_LANG_CHN => "請選擇佛菩薩聖像",
			SESS_LANG_ENG => "Please Select Buddha Statue to Request" ),
		'dt_diScreensTab' => array (
			SESS_LANG_CHN => "請選擇佛菩薩聖像屏風",
			SESS_LANG_ENG => "Please Select Buddha Image Screen to Request" ),
		'dt_diScrollsTab' => array (
			SESS_LANG_CHN => "請選擇佛菩薩聖像捲軸",
			SESS_LANG_ENG => "Please Select Buddha Image Scroll to Request" ),
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
 *				For readDI_Param						  *
 **********************************************************/
function readDI_Param( $dbInfo ) { // null is passed in
	global $_db, $_SESSION;
	/*
	 * Ajax Receiver switches on 'URL' and respective parameters
	 */
	$rpt = array();

	$rpt[ 'usrName'] = $_SESSION[ 'usrName' ];
	$rpt[ 'usrPass' ] = $_SESSION[ 'usrPass' ];
	$rpt[ 'sessType' ] = $_SESSION[ 'sessType' ];
	$rpt[ 'sessLang' ] = $_SESSION[ 'sessLang' ];
	$rpt[ 'icoName' ] = isset($_SESSION[ 'icoName' ]) ? $_SESSION[ 'icoName' ] : null;
	$rpt[ 'tblName' ] = isset($_SESSION[ 'tblName' ]) ? $_SESSION[ 'tblName' ] : null; unset( $_SESSION[ 'tblName' ] );
	$rpt[ 'dt_diMOP' ] = _dbName_2_htmlName ( 'dt_diMOP' );
	$rpt[ 'dt_diAlert' ] = _dbName_2_htmlName ( 'dt_diAlert' );
	$rpt[ 'dt_diShippingTab' ] = _dbName_2_htmlName ( 'dt_diShippingTab' );
	$rpt[ 'dt_diBkTab' ] = _dbName_2_htmlName ( 'dt_diBkTab' );
	$rpt[ 'dt_diStatuesTab' ] = _dbName_2_htmlName ( 'dt_diStatuesTab' );
	$rpt[ 'dt_diScreensTab' ] = _dbName_2_htmlName ( 'dt_diScreensTab' );
	$rpt[ 'dt_diScrollsTab' ] = _dbName_2_htmlName ( 'dt_diScrollsTab' );
	
	return $rpt;
} // function readDI_Param()

/**********************************************************
 *								 Main Functional Code										*
 **********************************************************/
$_dbReq = $_POST[ 'dbReq' ];
$_dbInfo = isset( $_POST[ 'dbInfo' ]) ? json_decode( $_POST [ 'dbInfo' ], true ) : null;

switch ( $_dbReq ) {
	case 'readDI_Param':
		echo json_encode( readDI_Param( $_dbInfo ), JSON_UNESCAPED_UNICODE );
		break;
} // switch()

?>