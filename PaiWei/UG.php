<?php
/**********************************************************
 *           User Pai Wei User Guide - Chinese            *
 **********************************************************/
/*
 * To fit into the PaiWei SW architecture, the '#ugDesc' <div> will be loaded by the PaiWei.js
 */ 
	require_once( '../pgConstants.php' );
	require_once( 'dbSetup.php' );
	require_once( 'ChkTimeOut.php' );

	function xLate( $what ) {
		global $sessLang;
		$htmlNames = array (
			'htmlTitle' => array (
				SESS_LANG_CHN => "淨土念佛堂法會牌位申請主頁",
				SESS_LANG_ENG => "Retreat Merit Dedication Application Page" )
		);
		return $htmlNames[ $what ][ $sessLang ];
	} // xLate()

	$sessLang = SESS_LANG_CHN; // default
	if ( isset ( $_GET[ 'l' ] ) ) {
		$sessLang = ( $_GET[ 'l' ] == 'e' ) ? SESS_LANG_ENG : SESS_LANG_CHN;
	} else if ( isset( $_SESSION[ 'sessLang' ] ) ) {
		$sessLang = $_SESSION[ 'sessLang' ];
	}	
	$_SESSION[ 'sessLang' ] = $sessLang;

	$hdrURL = URL_ROOT . "/admin/index.php";
	$useChn = ( $sessLang == SESS_LANG_CHN );
	if ( !isset( $_SESSION[ 'usrName' ] ) ) {
		header( "location: " . $hdrURL );
	} // redirect
?>

<!DOCTYPE html>
<html>
<head>
<title><?php echo xLate( 'htmlTitle' ); ?></title>
<meta http-equiv="content-type" content="text/html; charset=UTF-8">
<link rel="stylesheet" type="text/css" href="https://www.amitabhalibrary.org/css/base.css">
<link rel="stylesheet" type="text/css" href="../css/admin.css">
<link rel="stylesheet" type="text/css" href="../css/menu.css">
<link rel="stylesheet" type="text/css" href="./PaiWei.css">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script src="../futureAlert.js"></script>
<script src="./PaiWei.js"></script>

<style type="text/css">

#myUpldTbl {
	table-layout: fixed;
	width: 60%;
	margin:auto;
	border: 4px ridge #00b300;
}

#myUpldTbl td {
	padding-left: 2vw;
	font-size: 1.2em;
	height: 8vh;
	border: 1px solid #00b300;
}

input[type=submit] {
	margin: auto;
	line-height: 40px;
	text-align:center;
	vertical-align: middle;
	font-size: 1.2em;	
}

.UGsteps {
	font-size: 0.9em;
}

.UGsteps th, td {
	vertical-alignment: top;
}

.UGsteps th {
	width: 15%;
}

.UGstepImg {
	width: 90%;
	height: auto;
	border: 1px solid black;
}
</style>

</head>
<body>
	<div class="dataArea" style="overflow-y: auto;">
		<div id="ugDesc"><!-- BEGIN for loading into the DataArea in PaiWei.php -->
<?php
	if ( $useChn ) { // Chinese version
 ?>
		<h2 style="margin-top: 4vh; text-align: center; letter-spacing: 1px;">
			法會牌位申請用戶指南
		</h2>
        <span style="display: block; width: 95%; margin: auto; padding-top: 2vh; font-size: 1.3em;
			font-weight: bold; line-height: 1.4em;"><!-- Intro phrase -->
            用戶將可經由此法會牌位申請主頁選擇所要申請設立的牌位：祈福消災、往生蓮位、歷代祖先、地基主蓮位、
            累劫冤親債主連位、牌位上載 等。
        </span>
        <div style="width: 95%; margin:auto; font-size: 1.3em;"><!-- Step-by-step Guide -->
            <ol>
                <li>
					當您在用戶功能選項的主頁點擊『法會牌位申請』之後，您即可看到法會牌位申請的主頁如下：<br/><br/>
					<img src="./img/pwMain.png" alt=""><br/><br/>
					<table>
						<tr><th>往生者蓮位</th>
							<td>
								爲啟情人的往生的父母、親友、師長、朋友，以至子女、晚輩、甚至寵物等申請設立牌位迴向祈福；
						    	若往生者於一年內往生，請選擇『一年內往生者蓮位』。
							</td>
						</tr>
						<tr><th>歷代祖先蓮位</th>
							<td>
								為爲啟情人的歷代祖先申請設立牌位迴向祈福；請註明祖先姓氏，如 『譚』氏。
							</td>
						</tr>
						<tr><th>祈福消災牌位</th>
							<td>
								爲啟請人的父母、親友、師長、朋友，子女、晚輩、甚至本人等申請設立牌位祈福；<br/>
								請註明受益者的全名。
							</td>
						</tr>
						<tr><th>累劫冤親債主</th>
							<td>
								爲啟請人本人的累劫冤親債主設立牌位迴向祈福;
								請註明陽上啟請人的全名。
							</td>
						</tr>
						<tr><th>地基主蓮位</th>
							<td>
								爲啟請人所在地 (家居，別宿，工作地等) 的守護神設立牌位迴向祈福;<br/>
								請註明啟請人所在地的地址。
							</td>
						</tr>
						<tr><th>上載牌位檔案</th>
							<td>
								上載已書寫好的牌位資料檔案；檔案資料格式必須符合上載格式的要求。
							</td>
						</tr>
					</table>
				</li><br/>
				<li>當您點選以上任何一種牌位後，您即可更改或刪除已設立的牌位，搜尋您要更改或刪除的牌位，或輸入新的牌位。
					下面僅以『一年內往生者蓮位』為例說明。<br/><br/>
					<img src="./img/editDaPaiWei.png" alt=""><br/><br/>
					<table>
						<tr><th>刪除</th>
							<td>點擊『刪除』，本行牌位資料即會從資料庫中刪除。</td>
						</tr>
						<tr><th>更改</th>
							<td>在點擊『更改』之前，資料格中的資料是無法更動的；
								但您點擊『更改』之後，每一個資料格的資料均可更動。
								當您完成更動之後，請點擊『保存更動』如下所示；資料庫的牌位資料即會更新。
							</td>
						</tr>
					</table><br/>
					<img src="./img/saveChgDaPaiWei.png" alt=""><br/><br/>
					<table>
						<tr><th>搜尋</th>
							<td>點擊『搜尋』，讓您可以找到您所要更改或刪除的牌位。</td>
						</tr>
						<tr><th>加行輸入</th>
							<td>在點擊『加行輸入』之後，在網頁表列牌位資料的最後面，會有一行空白的表列輸入格式；
								在此，您可以直接輸入新的牌位資料。
							</td>
						</tr>
					</table><br/>
					<img src="./img/newDaPaiWei.png" alt=""><br/><br/>
					<table>
						<tr><th></th>
							<td>『往生親友稱謂』 與 『陽上啟請人稱謂』 為 下拉列表選項；
								雖然它們已儘可能將稱謂列出，但如您沒有看到所要的選項，
								請選空白或任選一，然後再用更改的方式更正成您所適合的稱謂，並告知本館。
							</td>
						</tr>
					</table>
				</li><br/>
				<li>如果您要上載牌位資料，上載的牌位資料檔案必須符合下列要求:<br/>
					<ol type="a">
						<li>檔案是用 UTF-8 編碼 (UTF-8 encoded);</li>
						<li>檔案中每一行僅含一個牌位的資料；</li>
						<li>每一行的資料字段間，必須以一個逗號分開 &mdash;『逗號分隔值』/ (CSV) 的資料格式；</li>
						<li>每一個資料字段本身必須是一行連續的文字，不可再有換行;</li>
						<li>若資料字段本身中仍需有標點，則字段本身必須用雙引號 ( “ ) 將其囊括；<br/>
							如 “示範，字段一“，“示範，字段二“。</li>
					</ol>
				</li><br/>
				<li>建議您依下列步驟，建立並保存為逗號分隔值 (CSV) 的檔案。
					<ol type="a">
						<li><a href="./Templates/pwTemplate.xlsx"><b>下載此樣式檔案</b></a></li>
						<li>此樣式檔案是微軟的 EXCEL 檔，每一個標籤反應上面所述的各類牌位種類；
							點擊您所要申請牌位的標籤，即可開始書寫牌位資料；我們仍然以『一年內往生親友蓮位』為例說明。<br/>
							當您點擊『一年內往生親友蓮位』標籤後，您會看到如下的輸入表：<br/><br/>
							<img src="./img/inputXLS.png" alt="">
						</li><br/>
						<li>按住『控制鍵』/[Ctrl] (或『指令鍵』/ [cmd] - 如果是蘋果電腦)，點選所有有色的說明圖案；
							如下圖：<br/><br/>
							<img src="./img/allImgXLS.png" alt="">
						</li><br/>
						<li>按下『刪除鍵』/[Del]，將所有的有色圖案刪除，您就有一個乾淨的輸入表；在此輸入表內完成您所
							要輸入的資料。
						</li>
						<li>完成輸入後請於左上角『檔案』的下拉選項中選擇『保存為』如下：<br/><br/>
							<img src="./img/saveAsSel.png" alt="" style="width: 500px; height: auto;">
						</li><br/>
						<li>點擊『保存為』後，您會看到如下的『對話框』；請於『保存為』資料格中輸入您要的檔案名，
							並於『檔案格式』資料格中選擇 『CSV UTF-8』 為保存檔案的資料格式; 然後點擊右下角的『保存』。<br/><br/>
							<img src="./img/saveAsCSV.png" alt="" style="width: 700px; height: auto;">
						</li><br/>
						<li>微軟 EXCEL 會提醒您您只能保存當下所選標籤的輸入表為所要的格式，如下：<br/><br/>
							<img src="./img/saveActive.png" alt="" style="width: 400px; height: auto;">
						</li><br/>
						<li>在點擊『確認』/ [OK] 後，您的輸入表即會以『逗號分隔值』/ (CSV) 的資料格式及您所命名的檔案名，
							保存在您以所選擇的目錄中。
						</li>
						<li>對其他的輸入表，重復以上的步驟，將每一個輸入表存為『逗號分隔值』資料格式的檔案。
						</li>
					</ol>
				</li><br/>
				<li>上載牌位資料檔案，請按下列的步驟：
					<ol type="a">
						<li>在本頁的右上方，點擊『上載牌位檔案』，在本頁的正下方即會看到如下的『對話框』：<br/><br/>
							<img src="./img/upldBox.png" alt="" style="width: 600px; height: auto;">
						</li><br/>
						<li>點擊在左邊的 『Browse』框，電腦會讓您移到您所存檔案的目錄中選擇您要用的牌位檔案，選擇它。<br/><br/>
							<img src="./img/upldFile.png" alt="" style="width: 600px; height: auto;">
						</li><br/>
						<li>點擊在右邊的 『請選擇牌位用途』框，由下拉列表中選擇牌位的用途；<br/><br/>
							<img src="./img/upldPaiWei.png" alt="" style="width: 600px; height: auto;">
						</li><br/>
						<li>最後點擊『上載』，您的牌位資料即會上載存入資料庫中。
						</li>
					</ol>
				</li>
            </ol>
        </div><!-- END Step-by-step Guide -->
		<h2 style="margin-top: 4vh; text-align: center; letter-spacing: 1px;">
			謝謝您使用本網頁申請法會牌位，阿彌陀佛！
		</h2>
<?php
	} else { // English version
?>
		<h2 style="margin-top: 4vh; text-align: center; letter-spacing: normal;">
			User Guide to Request for Merit Dedication Name Plaques during Retreats (*** Work In Prog. ***) 
		</h2>
        <span style="display: block; width: 95%; margin: auto; padding-top: 2vh; font-size: 1.3em;
			font-weight: bold; line-height: 1.1em;"><!-- Intro phrase -->
            Users can apply for specific Merit Dedication Name Plaques during Retreats from this page:
			well-blessing, deceased and recently deceased beloved ones, ancestors, site guardians,
			karmic creditors, etc. You can also upload name plaque data.
        </span>
        <div style="width: 95%; margin:auto; font-size: 1.3em;"><!-- BEGIN Step-by-step Guide -->
            <ol>
                <li>
					When you click "Name Plaque Application for Merit Dedication in Retreats",
					you will see the "Retreat Merit Dedication Request Page" with selections as below：<br/><br/>
					<img src="./img/epwMain.png" alt="">
				</li>
            </ol>
        </div><!-- END Step-by-step Guide -->
		</div><!-- END for loading into the DataArea in PaiWei.php -->
<?php
	} // English version
?>
    </div><!-- DataArea -->
</body>
</html>