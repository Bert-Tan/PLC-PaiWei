<?php
/**********************************************************
 *         Setting / Updating Retreat Information         *
 **********************************************************/
 
	require_once( '../pgConstants.php' );
	require_once( 'dbSetup.php' );
	require_once( 'ChkTimeOut.php' );

    $retreatData = array();
    $_errCount = 0; $_errRec = array();

	function evtSelection( $dfltSel ) {
		$tpl = new HTML_Template_IT("./Templates");
		$tpl->loadTemplatefile("rtEvtOpt.tpl", true, true);
		$tpl->setCurrentBlock("rtEvtOpt");
		switch ( $dfltSel ) {
		case "Qingming":
			$tpl->setVariable("dfltQM", "selected");
			break;
		case "Zhongyuan":
			$tpl->setVariable("dfltZY", "selected");
			break;
		case "ThriceYearning":
			$tpl->setVariable("dfltTY", "selected");
			break;
		default: /* unKnown */
			$tpl->setVariable("dflt", "none");
			break;
		} // switch()
		$tpl->parse("rtEvtOpt");
	//	echo "Line: " . __LINE__ . " " . $tpl->get();exit;
		$tmp = preg_replace( "/(^[\r\n]*|[\r\n]+)[\s\t]*[\r\n]+/", "\n", $tpl->get() );
		return preg_replace( "/(^\t*)/", "  ", $tmp );
	} // function evtSelection()

    function readRetreatData() {
        global $_db, $_errCount, $_errRec;

        $sql = "SELECT * FROM pwParam WHERE true;";
		$rslt = $_db->query( $sql );
		if ( $rslt->num_rows == 0 ) {
			$_errRec[ 'rtrtDate' ] = "請輸入法會開始日期";
			$_errRec[ 'pwExpires' ] = "請輸入牌位申請截止日期";
			$_errRec[ 'rtEvent' ] = "unKnown";
			$_errRec[ 'rtReason' ] = "請輸入法會因緣";
			$_errCount = 4;
			return $_errRec;
		}
        if ( $rslt->num_rows > 1 ) { // should be only one tuple
            $_errRec[] = "資料庫發生錯誤；無法讀取法會資料！";
            $_errCount++;
            return $_errRec;
        }
        return ( $rslt->fetch_all(MYSQLI_ASSOC)[0] );  
    } // function getRetreatData()

    function updRetreatData() { // javascript function formSanity() has passed sanity check
		global $_db, $_POST, $_errCount, $_errRec;
		$rtReason = ( $_POST[ 'rtEvent' ] == "ThriceYearning" ) ? $_POST[ 'rtReason' ] : "";

		if ( isset( $_POST[ 'rtNew' ] ) ) {
			$sql = "INSERT INTO `pwParam` ( `rtrtDate`, `pwExpires`, `rtEvent`, `rtReason` ) VALUE "
				 . "( \"{$_POST['rtrtDate']}\", \"{$_POST['pwExpires']}\", \"{$_POST['rtEvent']}\", \"{$rtReason}\");";
		} else {
			$sql = "UPDATE pwParam SET `pwExpires` = \"{$_POST[ 'pwExpires' ]}\", `rtrtDate` = \"{$_POST[ 'rtrtDate' ]}\", "
				 . "`rtEvent` = \"{$_POST[ 'rtEvent' ]}\", `rtReason` = \"{$rtReason}\" "
				 . "WHERE `ID` = \"{$_POST[ 'ID' ]}\";";
		}
        $rslt = $_db->query( $sql );
        if ( $_db->affected_rows > 1 ) {
            $_errRec[] = "資料庫發生錯誤；無法更新！";
            $_errCount++;
        }
        return;
	} // function updRetreatData()

//  session_start(); // create or retrieve (already called in ChkTimeOut.php )
	$hdrLoc = "location: " . URL_ROOT . "/admin/index.php";

 	if ( !isset( $_SESSION[ 'usrName' ] ) ) {
		header( $hdrLoc );
	}

    if ( isset( $_POST[ 'rtUpdData' ] ) ) {
		updRetreatData();
    }   
	$retreatData = readRetreatData();
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
<script type="text/javascript" src="./chkRtDate.js"></script>
<script type="text/javascript">
	function selChange() {
		var chgdTo = $(this).find(":selected").val();
		if ( chgdTo != "ThriceYearning" ) {
			$(".rtRsn").find("input").prop("disabled", true ).val("不適用");
		} else {
			$(".rtRsn").find("input").prop("disabled", false ).val("請輸入法會因緣");
		}
		return false;
	} // function selChange()
	function formSanity() {
		var rtDate = $(this).find("input[name=rtrtDate]").val(); var rtD = new Date(rtDate);
		var pwDate = $(this).find("input[name=pwExpires]").val(); var pwD = new Date(pwDate);
		var rtEvent = $(this).find(":selected").val();
		var rtRsn = $(this).find("input[name=rtReason]").val();
		if ( chkDate( rtDate, false ) == false || chkDate( pwDate, true ) == false ) {
			alert( "法會開始及牌位截止日期必須是在一年之內的有效日期！" ); return false;
		}
		if ( rtD <= pwD ) {
			alert("法會牌位申請截止日期必須早於法會開始日期！"); return false;
		}
		if ( rtEvent == "ThriceYearning" && ( rtRsn == '不適用' || rtRsn == '請輸入法會因緣') ) {
			alert( "請輸入三時繫念法會因緣!" ); return false;
		}
		return true;
	} // function formSanity()
	$(document).ready(function() {
		pgMenu_rdy();
		$("select").on( "change", selChange );
		/* initial Selection ? */
		var iniSel = $("select :selected");
		if ( iniSel.length > 0 ) { // has a default rtEvent selection
			if ( iniSel.val() != "ThriceYearning" ) {
				$(".rtRsn").find("input").prop("disabled", true ).val("不適用");
			} else {
				$(".rtRsn").find("input").prop("disabled", false ); // Allow edit
				/* do not want to set value because it could be read from the DB */
			}
		} // has a default rtEvent selection
		$("form").on( 'submit', formSanity );
	})
</script>
<style>
table.dialog {
	width: 46%;
	left: 27%;
}

input, select {
    font-size: 1.1em;
}

input[type=text] {
	width: 80%;
}

select {
	width: 90%;
}
input[type=submit] {
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
	<?php require_once("../AdmPortal/AdmPgHeader.htm"); ?>
	<div class="dataArea">
		<div class="centerMeQ dataTitle" style="font-size: 2.0em;">請更新法會資料</div>
<?php
	$mbxDisplay = "none";
    if ( isset( $_POST[ 'rtUpdData' ] ) ) {
		$mbxDisplay = "block";
        if ( $_errCount == 0 ) {
			$msgTxt = "法會資料更新完畢！";
			$mbxBC = "#00b300";
			$mbxTxtA = "center";
		} else { // formulate msg
			print_r ( $_errRec );
			$msgTxt = "更新法會資料發生錯誤！";
			$mbxBc = "red";
			$mbxTxtA = "left";
			$lineNbrg = ( $_errCount > 1 );
			for ( $i = 0; $i < $_errCount; $i++ ) { echo __LINE__ . ": " . $i . "<br/><br/><br/>";
				$lineBreak = ( strlen( $msgTxt ) > 0 ) ? "<br/>" : '';
				$lineNbr = "[ " . ($i + 1) . " ] ";
				$msgTxt .= $lineBreak . ( $lineNbrg ? $lineNbr : '' ) . $_errRec[ $i ];
			}
		}
    } // End of Update Ack
?>
		<div style="width: 50%; margin: auto; border: 7px solid; border-radius: 8px; padding: 2px 3px;
				margin-top: 14%;
				font-size: 1.2em;
				text-align: <?php echo $mbxTxtA; ?>;
				letter-spacing: normal;
				display: <?php echo $mbxDisplay; ?>;
				border-color: <?php echo $mbxBC;?>;">
			<?php echo $msgTxt; ?>
		</div>

        <form action="" method="post" id="retreatUpd">
            <input type="hidden" name="ID" value="<?php echo $retreatData[ 'ID' ]; ?>">
            <table class="dialog" style="position: absolute; top: 45%;">
				<thead><tr><th>法會開始日期</th><th>牌位申請截止日期</th><th>法會類別</th></tr></thead>
                <tbody>
                    <tr>
                        <td>
<?php
	if ( !isset( $_POST[ 'rtUpdData' ] ) && ( $_errCount > 0 ) ) { /* reading Retreat Data non-existent */
?>
							<input type="hidden" name="rtNew" value="true">
<?php
	}
?>
							<input type="text" name="rtrtDate" value="<?php echo $retreatData[ 'rtrtDate' ];?>">
						</td>
                        <td>
							<input type="text" name="pwExpires" value="<?php echo $retreatData[ 'pwExpires' ];?>">
						</td>
						<td>
							<?php echo evtSelection( $retreatData[ 'rtEvent' ] ); ?>
						</td>
					</tr>
					<tr>
						<td class="rtRsn" colspan="3" style="text-align: left; padding: 4px 10px;">
							<span style="font-weight: bold;">三時繫念法會因緣：</span><br/>
							<input type="text" name="rtReason" style="display: inline-block; float:right; width: 95%;"
								value="<?php echo $retreatData[ 'rtReason' ]; ?>" disabled>
						</td>
                    </tr>
                    <tr><td colspan="3"><input type="submit" name="rtUpdData" value="更新法會資料"></td></tr>
                </tbody>
            </table>
        </form>
	</div>
</body>
</html>