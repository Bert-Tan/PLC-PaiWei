<?php
/**********************************************************
 *          In Care of Others' PaiWei Applicaion          *
 **********************************************************/
 
	require_once( '../pgConstants.php' );
	require_once( 'dbSetup.php' );
	require_once( 'ChkTimeOut.php' );

	function xLate( $what ) {
		global $sessLang;
		$htmlNames = array (
			'htmlTitle' => array (
				SESS_LANG_CHN => "淨土念佛堂管理用戶主頁",
				SESS_LANG_ENG => "Pure Land Center Admin User Main Page" ),
			'pwMgr' => array (
				SESS_LANG_CHN => "為蓮友處理法會牌位",
				SESS_LANG_ENG => "Manage Name Plaques for others" ),
			'rtrtMgr' => array (
				SESS_LANG_CHN => "更新法會資料",
				SESS_LANG_ENG => "Manage Retreats" ),
			'logOut' => array (
				SESS_LANG_CHN => "用戶<br/>撤出",
				SESS_LANG_ENG => "User<br/>Logout" ),
			'h1Title' => array (
				SESS_LANG_CHN => "請選擇蓮友為他處理法會牌位",
				SESS_LANG_ENG => "Manage Name Plaques for Others" )
			);
		return $htmlNames[ $what ][ $sessLang ];
    } // function xLate();
    
    function readInCareOf() { // returns a string reflecting a <select> html element
        global $_db;

		$_db->query( "LOCK TABLES inCareOf READ;" );
		$sql = "SELECT UsrName FROM inCareOf WHERE true;";
		$rslt = $_db->query( $sql );
		$_db->query( "UNLOCK TABLES;" );
		$inCareOfNames = $rslt->fetch_all(MYSQLI_ASSOC);
		$tpl = new HTML_Template_IT("./Templates");
		$tpl->loadTemplatefile("inCareOfChoice.tpl", true, true);
		$tpl->setCurrentBlock("InCareOf");
		foreach ( $inCareOfNames as $inCareOfName ) { // print_r( $inCareOfName );
			$tpl->setCurrentBlock("Option");
			foreach ($inCareOfName as $key => $val ) {
				$tpl->setVariable("fldV", $val );	
			}
			$tpl->parse("Option");
		}
		$tpl->parse("InCareOf");
		$tmp = preg_replace( "/(^[\r\n]*|[\r\n]+)[\s\t]*[\r\n]+/", "\n", $tpl->get() );
		return preg_replace( "/(^\t*)/", "  ", $tmp );
	} // function readInCareOf()
	
	function setInCareOf() {
		global $_db, $_POST, $_SESSION;
		$rpt = array( );
		$_SESSION[ 'icoName' ] = isset($_POST[ 'icoText' ]) ? $_POST[ 'icoText' ] : $_POST[ 'icoSel' ];
		$_db->query( "LOCK TABLES inCareOf READ, WRITE;" );
		$rslt = $_db->query( "SELECT * FROM inCareOf WHERE `UsrName` = \"{$_SESSION[ 'icoName' ]}\";" );
		if ( $rslt->num_rows == 0 ) {
			$rslt = $_db->query( "INSERT INTO inCareOf ( `UsrName` ) VALUE ( \"{$_SESSION[ 'icoName' ]}\" );" );
		}
		$_db->query( "UNLOCK TABLES;" );
		$rpt['icoName'] = $_SESSION['icoName'];
		$rpt[ 'url' ] = URL_ROOT . '/admin/PaiWei/index.php';
		return $rpt;
	} // setInCareOf()

	function putMsg( $bxW, $txtLS, $txtA, $fontW, $xtra ) {
		// style: Width, Letter-spacing, text-alignment, font-weight
		global $_errCount,  $_errRec;

		$msg = ( strlen( $xtra ) <= 0 ) ? '' : $xtra;
		$mbxBC = ( $_errCount > 0 ) ? "red" : "#00b300";
		$lineNbrg = ( $_errCount > 1 );
		for ( $i = 0; $i < $_errCount; $i++ ) {
			$lineBreak = ( strlen( $msg ) > 0 ) ? "<br/>" : '';
			$lineNbr = "[ " . ($i + 1) . " ] ";
			$msg .= $lineBreak . ( $lineNbrg ? $lineNbr : '' ) . $_errRec[ $i ];
		}
		$msgBox =
			"<div class=\"msgBox q_centerMe\" id=\"ackMsg\"
				style=\"display: block; border-color: {$mbxBC}; width: {$bxW};
				text-align: {$txtA}; letter-spacing: {$txtLS}; font-weight: {$fontW};\">
				{$msg}
			 </div>	
			";
		return $msgBox;
	} // putMsg()

//	session_start(); // create or retrieve (already called in ChkTimeOut.php )
	$sessLang = SESS_LANG_CHN; // default
	if ( isset( $_SESSION[ 'sessLang' ] ) ) {
		$sessLang = $_SESSION[ 'sessLang' ];
	}
	$_SESSION[ 'sessLang' ] = $sessLang;

	$hdrLoc = "location: " . URL_ROOT . "/admin/index.php";
	$rtrtMgrUrl = "../PaiWei/rtMgr.php";	// relative;
	$pwMgrUrl = "../PaiWei/inCareOfMgr.php";	// relative;
	$useChn = ( $sessLang == SESS_LANG_CHN );

 	if ( !isset( $_SESSION[ 'usrName' ] ) ) {
		header( $hdrLoc );
	}

	if ( isset( $_POST[ 'icoSkip' ] ) ) {
		$_SESSION[ 'icoSkip' ] = true;
		$rpt[ 'icoSkip' ] = true;
		$rpt[ 'url' ] = URL_ROOT . '/admin/PaiWei/index.php';
		echo json_encode( $rpt, JSON_UNESCAPED_UNICODE );
		exit;
	}
	if ( isset( $_POST[ 'icoText' ] ) || isset( $_POST[ 'icoSel' ] ) ) {
		echo json_encode ( setInCareOf (  ), JSON_UNESCAPED_UNICODE );
		exit;
	}
?>

<!DOCTYPE html>
<html>
<head>
<title><?php echo xLate( 'htmlTitle' ); ?></title>
<meta http-equiv="content-type" content="text/html; charset=UTF-8">
<link rel="stylesheet" type="text/css" href="https://www.amitabhalibrary.org/css/base.css">
<link rel="stylesheet" type="text/css" href="../css/admin.css">
<link rel="stylesheet" type="text/css" href="../css/menu.css">
<link rel="stylesheet" type="text/css" href="./UsrPortal.css">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script src="../futureAlert.js"></script>
<script type="text/javascript">
	var _icoName = '';
	function icoFocus() {
		var newV = $(this).val().trim().replace( /<br>$/gm, '');
		var pmptV = $(this).attr("data-pmptV").trim().replace( /<br>$/gm, '');
		if ( pmptV.length > 0 ) return; // was here before
		$(this).attr('data-pmptV', newV );
		$(this).val('');
		return false;		
	} // icoFocus()
	function icoBlur() {
		var currV = $(this).val().trim().replace( /<br>$/gm, '');
		if ( currV.length == 0 ) {
			$(this).val( $(this).attr("data-pmptV").trim() );
			$(this).attr( 'data-pmptV', '');
		}
		return false;
	} // icoBlur()
	function icoSkip() {
		var myFormData = new FormData(); myFormData.append( 'icoSkip', true );
		var myHdlr = $("form").attr("action");
		$.ajax({
			method: "POST",	url: myHdlr, data: myFormData,
			processData: false, contentType: false, cache: false,
			success: function ( rsp ) {
				rspV = JSON.parse ( rsp );
				_icoSkip = rspV[ 'icoSkip' ];
				alert("略過處理蓮友牌位後，牌位管理功能僅限於下載牌位列印！");
				location.replace( rspV[ 'url' ] );
				return;
			}, // End of Success Handler
			error: function (jqXHR, textStatus, errorThrown) {
				alert( "icoSkip()\tError Status:\t"+textStatus+"\t\tMessage:\t\t"+errorThrown+"\n" );
			} // End of ERROR Handler
		});
		return false;
	} // icoSkip()
	function icoSubmit() {
		var myFormData = new FormData( this );
		var myHdlr = $(this).attr("action");
		var icoText = $("input[type=text]").val(); var icoSel = $("select#icoSel").val();
		if ( icoText == "請輸入蓮友識別名" && icoSel.length == 0 ) {
			alert("請輸入或點選蓮友識別名！"); return false;
		}
		if ( icoText == "請輸入蓮友識別名" ) myFormData.delete('icoText');
		if ( icoSel.length == 0 ) myFormData.delete('icoSel');
		$.ajax({
			method: "POST",	url: myHdlr, data: myFormData,
			processData: false, contentType: false, cache: false,
			success: function ( rsp ) {
				rspV = JSON.parse ( rsp );
				_icoName = rspV[ 'icoName' ];
				location.replace( rspV[ 'url' ] );
				return;
			}, // End of Success Handler 
			error: function (jqXHR, textStatus, errorThrown) {
				alert( "icoSubmit()\tError Status:\t"+textStatus+"\t\tMessage:\t\t"+errorThrown+"\n" );
			} // End of ERROR Handler		
		}); // AJAX Call		
		return false;
	} // icoSubmit()
	$(document).ready(function() {
		$(".future").on( 'click', futureAlert );
		$(".soon").on( 'click', soonAlert );
		$("#icoForm").on( 'submit', icoSubmit );
		$("#icoInput").on( 'focus', icoFocus );
		$("#icoInput").on( 'blur', icoBlur );
		$("#icoSkip").on( 'click', icoSkip );
	})
</script>
<style>
#myMenuTbl {
	table-layout: fixed;
	height: 3.1em;
}

#myIcoTbl {
    position: absolute;
    top: 30vh;
    left: 20%;
    width: 60%;
    margin: auto;
    border: 4px ridge #00b300;;
	font-size: 1.3em;
	table-layout: fixed;
}

#myIcoTbl th, td {
    border: 1px solid #00b300;
	margin: 0;
	padding: 2px 5px;
	height: 8vh;
	line-height: 1.2em;
    vertical-align: middle;
    text-align: center;
}

input {
    font-size: 1.0em;
}
input[type=submit], input[type=button] {
    background-color: aqua;
    text-align: center;
    display: inline-block;
    height: 1.5em;
    border: 1px solid blue;
    border-radius: 3px;
}
</style>
</head>
<body>
	<div class="hdrRibbon">
		<img src="https://www.amitabhalibrary.org/pic/PLC_logo_TR.png" alt="">
		<div id="pgTitle" class="centerMeV">
			<span style="letter-spacing: 1px;">淨土念佛堂管理用戶主頁</span><br/>
			<span class="engClass">Pure Land Center Admin Portal</span>
		</div>
		<table id="myMenuTbl" class="centerMeV">	
			<thead>
				<tr>
					<th><a href="<?php echo $rtrtMgrUrl; ?>" class="myLinkButton"><?php echo xLate( 'rtrtMgr' ); ?></a></th>
					<th><a href="<?php echo $pwMgrUrl; ?>" class="myLinkButton"><?php echo xLate( 'pwMgr' ); ?></th>
					<th class="future">處理週日迴向申請</th>
				</tr>
			</thead>
		</table>
		<div id="pgLogOut" class="centerMeV"><a href="../Login/Logout.php"><?php echo xLate( 'logOut' ); ?></a></div>
	</div>
	<div class="dataArea">
		<h1 class="q_centerMe" id="myDataTitle"
            style="<?php if ( !$useChn ) echo "letter-spacing: normal;"; ?>; margin-top: 0px; top: 15vh;">
            請選擇蓮友為他處理法會牌位
		</h1>
        <form action="inCareOfMgr.php" method="post" id="icoForm">
            <table id="myIcoTbl">
                <thead><tr><th colspan="2">請由下輸入或點選蓮友識別名<br/>(若兩者皆選，以輸入的蓮友名為準)</th></tr></thead>
                <tbody>
                    <tr>
						<td>
							<input	type="text" name="icoText" id="icoInput"
									data-pmptV = "" value="請輸入蓮友識別名">
						</td>
                        <td id="icoList">
							<?php echo readInCareOf(); ?>
						</td>
                    </tr>
                    <tr>
						<td>
							<input type="submit" name="icoSub" value="處理蓮友牌位">
						</td>
						<td>
							<input type="button" name=icoSkip" id="icoSkip" value="略過處理蓮友牌位">
						</td>
					</tr>
                </tbody>
            </table>
        </form>
	</div>
</body>
</html>