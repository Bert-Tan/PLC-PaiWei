<?php
/**********************************************************
 *         Setting / Updating Retreat Information         *
 **********************************************************/
 
	require_once( '../pgConstants.php' );
	require_once( 'dbSetup.php' );
	require_once( 'ChkTimeOut.php' );

    $retreatData = array();
    $_errCount = 0; $_errRec = array();

    function readRetreatData() {
        global $_db, $_errCount, $_errRec;

        $sql = "SELECT * FROM pwParam WHERE true;";
        $rslt = $_db->query( $sql );
        if ( $rslt->num_rows != 1 ) { // should be only one tuple
            $_errRec[] = "資料庫發生錯誤；無法讀取法會資料！";
            $_errCount++;
            return $_errRec;
        }
        return ( $rslt->fetch_all(MYSQLI_ASSOC)[0] );  
    } // function getRetreatData()

    function updRetreatData() {
        global $_db, $_POST, $_errCount, $_errRec;
        $sql = "UPDATE pwParam SET `pwExpires` = \"{$_POST[ 'pwExpires' ]}\", `rtrtDate` = \"{$_POST[ 'rtrtDate' ]}\" "
             . "WHERE `ID` = \"{$_POST[ 'ID' ]}\";";
        $rslt = $_db->query( $sql );
        if ( $_db->affected_rows != 1 ) {
            $_errRec[] = "資料庫發生錯誤；無法更新！";
            $_errCount++;
        }
        return;
	} // function updRetreatData()
	
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
			"<div class=\"msgBox centerMeQ\" id=\"ackMsg\"
				style=\"display: block; border-color: {$mbxBC}; width: {$bxW}; top: 37%;
				text-align: {$txtA}; letter-spacing: {$txtLS}; font-weight: {$fontW};\">
				{$msg}
			 </div>	
			";
		return $msgBox;
	} // putMsg()

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
<script type="text/javascript">
	$(document).ready(function() {
		pgMenu_rdy();
	})
</script>
<style>
input {
    font-size: 1.1em;
}

input[type=text] {
	width: 70%;
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
		<div class="centerMeQ dataTitle" style="font-size: 2.0em;">請更新法會資料</div>
<?php
    if ( isset( $_POST[ 'rtUpdData' ] ) ) {
        $xtra = ( $_errCount == 0 ) ? "法會資料更新完畢！" : '';
        echo putMsg( "35%", "normal", "center", "bold", $xtra );
    }
 ?>
        <form action="" method="post" id="retreatUpd">
            <input type="hidden" name="ID" value="<?php echo $retreatData[ 'ID' ]; ?>">
            <table class="dialog" style="position: absolute; top: 45%; left: 30%;">
                <thead><tr><th>法會開始日期</th><th>牌位申請截止日期</th></tr></thead>
                <tbody>
                    <tr>
                        <td><input type="text" name="rtrtDate" value="<?php echo $retreatData[ 'rtrtDate' ];?>"></td>
                        <td><input type="text" name="pwExpires" value="<?php echo $retreatData[ 'pwExpires' ];?>"></td>
                    </tr>
                    <tr><td colspan="2"><input type="submit" name="rtUpdData" value="更新法會資料"></td></tr>
                </tbody>
            </table>
        </form>
	</div>
</body>
</html>