<?php
/**********************************************************
 *         Setting / Updating Retreat Information         *
 **********************************************************/
 
	require_once( '../pgConstants.php' );
	require_once( 'dbSetup.php' );
	require_once( 'ChkTimeOut.php' );

    $retreatData = array();
    $_errCount = 0; $_errRec = array();

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
				SESS_LANG_CHN => "請更新法會資料",
				SESS_LANG_ENG => "Please Input Retreat Data" )
			);
		return $htmlNames[ $what ][ $sessLang ];
    } // function xLate();
    
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
			"<div class=\"msgBox q_centerMe\" id=\"ackMsg\"
				style=\"display: block; border-color: {$mbxBC}; width: {$bxW};
				text-align: {$txtA}; letter-spacing: {$txtLS}; font-weight: {$fontW};\">
				{$msg}
			 </div>	
			";
		return $msgBox;
	} // putMsg()

//  session_start(); // create or retrieve (already called in ChkTimeOut.php )
	$sessLang = SESS_LANG_CHN; // default
	if ( isset( $_SESSION[ 'sessLang' ] ) ) {
		$sessLang = $_SESSION[ 'sessLang' ];
	}
	$_SESSION[ 'sessLang' ] = $sessLang;

	$hdrLoc = "location: " . URL_ROOT . "/admin/index.php";
	$rtrtMgrUrl = "../PaiWei/rtMgr.php";	// relative;
	$pwMgrUrl = "../PaiWei/Dashboard.php";	// relative;
	$useChn = ( $sessLang == SESS_LANG_CHN );

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
<title><?php echo xLate( 'htmlTitle' ); ?></title>
<meta http-equiv="content-type" content="text/html; charset=UTF-8">
<link rel="stylesheet" type="text/css" href="https://www.amitabhalibrary.org/css/base.css">
<link rel="stylesheet" type="text/css" href="../css/admin.css">
<link rel="stylesheet" type="text/css" href="../css/menu.css">
<link rel="stylesheet" type="text/css" href="./UsrPortal.css">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script src="../futureAlert.js"></script>
<script type="text/javascript">
	$(document).ready(function() {
		$(".future").on( 'click', futureAlert );
		$(".soon").on( 'click', soonAlert );
	})
</script>
<style>
#myMenuTbl {
	table-layout: fixed;
}

#myMenuTbl th {
	line-height: 2.9em;
}
#myRetreatTbl {
    position: absolute;
    top: 30vh;
    left: 20%;
    width: 60%;
    margin: auto;
    border: 4px ridge #00b300;;
    font-size: 1.3em;
}

#myRetreatTbl th, td {
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
            <?php echo xLate( 'h1Title' ); ?>
        </h1>
<?php
    if ( isset( $_POST[ 'rtUpdData' ] ) ) {
        $xtra = ( $_errCount == 0 ) ? "法會資料更新完畢！" : '';
        echo putMsg( "40%", "normal", "center", "normal", $xtra );
    }
 ?>
        <form action="" method="post" id="retreatUpd">
            <input type="hidden" name="ID" value="<?php echo $retreatData[ 'ID' ]; ?>">
            <table id="myRetreatTbl">
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