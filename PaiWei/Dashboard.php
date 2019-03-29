<?php
/**********************************************************
 *          In Care of Others' PaiWei Applicaion          *
 **********************************************************/
 
	require_once( '../pgConstants.php' );
	require_once( 'dbSetup.php' );
	require_once( 'ChkTimeOut.php' );

    function readUsrPwRows() { // returns a string reflecting a <select> html element
        global $_db;
		$tblNames = array(	'W001A_4', 'DaPaiWei', 'L001A', 'C001A', 'Y001A', 'D001A' );
		$pwTotal = array(	'W001A_4' => 0, 'DaPaiWei' => 0, 'L001A' => 0,
							'C001A' => 0, 'Y001A' => 0, 'D001A' => 0 , 'grandTotal' => 0 );
		$_db->query( "LOCK TABLES inCareOf READ, pw2Usr READ;" );
		$sql  =	"SELECT DISTINCT `pwUsrName` FROM `pw2Usr` WHERE `pwUsrName` IN "
			  .	"(SELECT `UsrName` FROM `inCareOf`) ORDER BY `pwUsrName`;";
		$rslt = $_db->query( $sql );
		$inCareOfNames = $rslt->fetch_all(MYSQLI_ASSOC);
		$sql  =	"SELECT DISTINCT `pwUsrName` FROM `pw2Usr` WHERE `pwUsrName` NOT IN "
			  .	"(SELECT `UsrName` FROM `inCareOf`) ORDER BY `pwUsrName`;";
		$rslt = $_db->query( $sql );
		$otherNames = $rslt->fetch_all(MYSQLI_ASSOC);
		$_db->query( "UNLOCK TABLES;" );
		$allNames = array_merge( $inCareOfNames, $otherNames );
		$tpl = new HTML_Template_IT("./Templates");
		$tpl->loadTemplatefile("pwDashboard.tpl", true, true);		
		foreach ( $allNames as $Name ) {
			$icoName = $Name['pwUsrName'];
			$_db->query("LOCK TABLES `pw2Usr` READ;");
			$rslt = $_db->query("SELECT `TblName` FROM `pw2Usr` WHERE `pwUsrName` = \"${icoName}\";");
			$_db->query("UNLOCK TABLES;");
			$tpl->setCurrentBlock( "dashboard_row" );
			$tpl->setVariable( "icoName", $icoName );
			$tpl->setVariable( "icoTotal", $rslt->num_rows );
			$pwTotal[ 'grandTotal' ] += $rslt->num_rows;
			foreach ( $tblNames as $tblName ) {
				$_db->query("LOCK TABLES `pw2Usr` READ;");
				$sql = "SELECT `TblName` FROM `pw2Usr` WHERE `pwUsrName` = \"${icoName}\" AND `TblName` = \"${tblName}\";";
				$_db->query("UNLOCK TABLES;");
				$rslt = $_db->query( $sql );
				$pwTotal[ "${tblName}" ] += $rslt->num_rows;
				$tpl->setCurrentBlock("dashboard_cell");
				$tpl->setVariable("tblName", $tblName );
				$tpl->setVariable("tblTotal", $rslt->num_rows );
				$tpl->parse("dashboard_cell");
			} // each TblName
			$tpl->parse("dashboard_row");
		} // $allNames
		/* now the Summary Row */
		$tpl->setCurrentBlock( "sumRow" );
		$tpl->setVariable("grandTotal", $pwTotal[ 'grandTotal' ]);
		foreach( $tblNames as $tblName ) {
			$tpl->setCurrentBlock("sumCell");
			$tpl->setVariable("pwSum", $pwTotal[ "${tblName}" ]);
			$tpl->parse("sumCell");
		} // each tblName
		$tpl->parse("dashboard_row");
		$tmp = preg_replace( "/(^[\r\n]*|[\r\n]+)[\s\t]*[\r\n]+/", "\n", $tpl->get() );
		return preg_replace( "/(^\t*)/", "  ", $tmp );
	} // function readUsrPwRows()

	function readInCareOf() { // returns a string reflecting a <select> html element
		global $_db;
		$inCareOfNames = array();
		$usrNames = array();

		$sql1 = "SELECT `UsrName` FROM `inCareOf` WHERE `UsrName` NOT IN "
			  . "(SELECT DISTINCT `pwUsrName` FROM `pw2Usr`);";
		$sql2 = "SELECT `UsrName` FROM `Usr` WHERE `UsrName` NOT IN "
			  . "(SELECT DISTINCT `pwUsrName` FROM `pw2Usr`);";
		$_db->query( "LOCK TABLES `inCareOf` READ，`pw2Usr` READ, `Usr` READ;" );
		$rslt = $_db->query( $sql1 );
		if ( $rslt->num_rows > 0) {
			$inCareOfNames = $rslt->fetch_all(MYSQLI_ASSOC);
		}
		$rslt = $_db->query( $sql2 );
		$_db->query( "UNLOCK TABLES;" );
		if ( $rslt->num_rows > 0 ) {
			$usrNames = $rslt->fetch_all(MYSQLI_ASSOC);
		}
		$tpl = new HTML_Template_IT("./Templates");
		$tpl->loadTemplatefile("inCareOfChoice.tpl", true, true);
		$tpl->setCurrentBlock("InCareOf");
		foreach ( $inCareOfNames as $inCareOfName ) {
			$tpl->setCurrentBlock("Option");
			foreach ($inCareOfName as $key => $val ) {
				$tpl->setVariable("fldV", $val );	
			}
			$tpl->parse("Option");
		} // $inCareOfNames
		foreach ( $usrNames as $usrName ) {
			$tpl->setCurrentBlock("Option");
			foreach ($usrName as $key => $val ) {
				$tpl->setVariable("fldV", $val );	
			}
			$tpl->parse("Option");
		} // $inCareOfNames
		$tpl->parse("InCareOf");
		$tmp = preg_replace( "/(^[\r\n]*|[\r\n]+)[\s\t]*[\r\n]+/", "\n", $tpl->get() );
		return preg_replace( "/(^\t*)/", "  ", $tmp );
	} // function readInCareOf()
	
	function setInCareOf( $name ) {
		global $_db, $_SESSION;
		$rpt = array();
		$_SESSION[ 'icoName' ] = $name; unset( $_SESSION[ 'tblName' ] );
		$_db->query("LOCK TABLES Usr READ;");
		$rslt = $_db->query("SELECT `UsrName` FROM `Usr` WHERE `UsrName` = \"{$name}\";");
		$_db->query( "UNLOCK TABLES;" );
		if ( $rslt->num_rows == 0 ) { // Not in Usr Table => Insert into inCareOf Table
			$sql = "INSERT INTO `inCareOf` ( `UsrName` ) VALUE ( \"{$name}\" ) "
				 . "ON DUPLICATE KEY UPDATE `UsrName` = \"{$name}\";";
			$_db->query( "LOCK TABLES inCareOf WRITE;" );
			$rslt = $_db->query( $sql );
			$_db->query( "UNLOCK TABLES;" );
		} // End of Not in Usr Table
		$rpt[ 'url' ] = URL_ROOT . '/admin/PaiWei/index.php';
		return $rpt;
	} // function setInCareOf()

//	session_start(); // create or retrieve (already called in ChkTimeOut.php )
	$hdrLoc = "location: " . URL_ROOT . "/admin/index.php";
	if ( !isset( $_SESSION[ 'usrName' ] ) ) {
		header( $hdrLoc );
	}

	if ( sizeof( $_POST ) > 0 ) {
		if ( $_POST[ 'meansEntered' ] == 'byInput' ) {
			echo json_encode ( setInCareOf ( $_POST[ 'icoName' ] ), JSON_UNESCAPED_UNICODE );
			exit;
		}
		$_SESSION[ 'icoName' ] = $_POST[ 'icoName' ];
		unset( $_SESSION[ 'tblName' ] );
		$rpt[ 'url' ] = URL_ROOT . '/admin/PaiWei/index.php';
		echo json_encode ( $rpt, JSON_UNESCAPED_UNICODE );
		exit;
	}
?>

<!DOCTYPE html>
<html>
<head>
<title>淨土念佛堂管理用戶主頁</title>
<meta http-equiv="content-type" content="text/html; charset=UTF-8">
<link rel="stylesheet" type="text/css" href="../master.css">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script type="text/javascript" src="../futureAlert.js"></script>
<script type="text/javascript" src="../AdmPortal/AdmCommon.js"></script>
<script type="text/javascript">
	function icoInputFocus() {
		var newV = $(this).val().trim().replace( /<br>$/gm, '');
		var pmptV = $(this).attr("data-pmptV").trim().replace( /<br>$/gm, '');
		if ( pmptV.length > 0 ) return; // was here before
		$(this).attr('data-pmptV', newV );
		$(this).val('');
		return false;		
	} // icoInputFocus()
	function icoInputBlur() {
		var currV = $(this).val().trim().replace( /<br>$/gm, '');
		if ( currV.length == 0 ) {
			$(this).val( $(this).attr("data-pmptV").trim() );
			$(this).attr( 'data-pmptV', '');
		}
		return false;
	} // icoInputBlur()
	function icoInputSubmit() {
		var icoName = $(this).closest("th").find("input[type=text]").val();
		if ( icoName == "請輸入其他蓮友識別名" ) {
			alert( icoName );
			return false;
		}
		$.ajax({
			method: "POST",	url: "", data: { 'icoName' : icoName, 'meansEntered' : 'byInput' },
			success: function( rsp ) {
				rspV = JSON.parse( rsp );
				location.replace( rspV[ 'url' ] );
				return;
			}, // End of SUCCESS Handler
			error: function (jqXHR, textStatus, errorThrown) {
				alert( "icoSubmit()\tError Status:\t"+textStatus+"\t\tMessage:\t\t"+errorThrown+"\n" );
				return false;
			} // End of ERROR Handler
		});
	} // icoInputSubmit()
	function icoSelectSubmit() {
		var icoName = $(this).closest("th").find("SELECT OPTION:SELECTED").val();
		if ( icoName.length == 0 ) {
			alert( "請點選蓮友識別名" );
			return;
		}
		$.ajax({
			method: "POST",	url: "", data: { 'icoName' : icoName, 'meansEntered' : 'bySel' },
			success: function( rsp ) {
				rspV = JSON.parse( rsp );
				location.replace( rspV[ 'url' ] );
				return;
			}, // End of SUCCESS Handler
			error: function (jqXHR, textStatus, errorThrown) {
				alert( "icoSubmit()\tError Status:\t"+textStatus+"\t\tMessage:\t\t"+errorThrown+"\n" );
				return false;
			} // End of ERROR Handler
		});
		return false;
	} // icoSelectSubmit()
	function dataCellClick() {
		var thisRow = $(this).closest("tr");
		var _ajaxData = {}; var _dbInfo = {};
		_dbInfo[ 'icoName' ] = thisRow.find("td:first-child").text();
		_dbInfo[ 'tblName' ] = $(this).attr("data-tblN");
		_ajaxData[ 'dbReq' ] = 'pwDashboard';
		_ajaxData[ 'dbInfo' ] = JSON.stringify ( _dbInfo );
		$.ajax({
			method: 'POST', url: "./ajax-pwDB.php",
			data: _ajaxData,
			success: function ( rsp ) {
				var rspV = JSON.parse ( rsp );
				location.replace( rspV[ 'url' ] );
				return;
			},
			error: function (jqXHR, textStatus, errorThrown) {
				alert( "icoSubmit()\tError Status:\t"+textStatus+"\t\tMessage:\t\t"+errorThrown+"\n" );
				return false;
			} // End of ERROR Handler
		}); // ajax Call
	} // dataCellClick()
	$(document).ready(function() {
		pgMenu_rdy();
		$("input#icoInput").on( 'focus', icoInputFocus );
		$("input#icoInput").on( 'blur', icoInputBlur );
		$("input#icoInputBtn").on( 'click', icoInputSubmit );
		$("input#icoSelBtn").on( 'click', icoSelectSubmit );
		$("table.dataRows td[data-tblN]").on( 'click', dataCellClick );
	})
</script>
<style>
/* Below are completely local */
table.dataRows tr:last-child td { /* the Summary Row */
	color: yellow;
	background-color: #00b300;
	font-weight: bold;
}

table.dataRows td[data-tblN]:hover {
/*	color: no change; */
	background-color: #ffff80;
	cursor: pointer;
}

table.dataHdr th input {
	font-size: 1.0em;
	background-color: aqua;
	border: 1px solid blue;
}

table.dataHdr th input[type=button] {
	margin-top: 3px;
	display: inline-block;
	float: right;
	border: 1px solid blue;
	border-radius: 6px;
}

table.dataHdr th select {
	width: 70%;
	background-color: aqua;
	border: 1px solid blue;
}

</style>
</head>
<body>
	<div class="hdrRibbon">
		<img src="https://www.amitabhalibrary.org/pic/PLC_logo_TR.png" class="centerMeV" alt="">
		<div id="pgTitle" class="centerMeV">
			<span style="letter-spacing: 1px;">淨土念佛堂管理用戶主頁</span><br/>
			<span class="engClass">Pure Land Center Admin Portal</span>
		</div>
		<table class="pgMenu centerMeV">	
			<thead>
				<tr>
<?php
	if ( $_SESSION[ 'sessType' ] == SESS_TYP_WEBMASTER ) {
?>
					<th>用戶管理</th>
<?php
	}
?>
					<th>更新法會資料</th>
					<th>為蓮友處理法會牌位</th>
					<th class="future">處理週日迴向申請</th>
					<th>用戶<br/>撤出</th>
				</tr>
			</thead>
		</table>
	</div>
	<div class="dataArea">
		<div class="dataHdrWrapper">
			<table class="dataHdr">
				<thead>
					<tr>
						<th colspan="2" style="border-right: none;">
							<input	type="text" name="icoName" style="width: 70%;" id="icoInput"
									data-pmptV = "" value="請輸入其他蓮友識別名"><br/>
							<input type="button" id="icoInputBtn" name="icoSub" value="處理該蓮友牌位">
						</th>
						<th colspan="4"
							style="font-size: 1.3em; letter-spacing: 5px;
								border-left: none; border-right: none;">
							蓮友所申請牌位匯總表<br/>
							<span style="letter-spacing: normal; font-size: 0.8em;">
							(點選任何數字格，即可躍至該蓮友該項牌位的申請表來作處理。)
							</span>
						</th>
						<th colspan="2" style="border-left: none;">
							<?php echo readInCareOf(); ?><br/>
							<input type="button" id="icoSelBtn" name="icoSub" value="處理該蓮友牌位">
						</th>
					</tr>
					<tr>
						<th>蓮友登錄識別</th><th>往生者蓮位</th>
						<th>(一年內)<br/>往生者蓮位</th><th>歷代祖先蓮位</th>
						<th>祈福消災牌位</th><th>累劫冤親債主<br/>蓮位</th><th>地基主蓮位</th><th>總&nbsp;&nbsp;計</th>
					</tr>
				</thead>
			</table>
		</div><!-- data header wrapper -->
		<div class="dataBodyWrapper">
			<table class="dataRows">
				<tbody>
					<?php echo readUsrPwRows(); ?>
				</tbody>
			</table>
		</div><!-- data body wrapper -->
	</div><!-- dataArea -->
</body>
</html>