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
	if ( isset( $_SESSION[ 'sessLang' ] ) ) {
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
            用戶將可經由此法會牌位申請主頁選擇所要申請設立的牌位：往生者蓮位、(一年內)往生者蓮位、歷代祖先蓮位、祈福消災牌位、
			累劫冤親債主蓮位、地基主蓮位 等；您亦可選擇用戶指南或上載牌位檔案。
        </span>
        <div style="width: 95%; margin:auto; font-size: 1.3em;"><!-- Step-by-step Guide -->
            <ol>
                <li>
					當您在用戶功能選項的主頁點擊『法會牌位申請』之後，您即可看到法會牌位申請的主頁如下：<br/><br/>
					<img src="./img/pwMain.png" alt=""><br/><br/>
					<table>
						<tr><th style="width:18%;">往生者蓮位</th>
							<td>
								爲啟請人的往生的父母、親友、師長、朋友，以至子女、晚輩、甚至寵物等申請設立牌位迴向祈福；
						    	若往生者於一年內往生，請選擇『一年內往生者蓮位』。
							</td>
						<tr><th style="padding-bottom: 2px;">(一年內)往生者蓮位</th>
							<td>
								與上同；但（1）請勿爲寵物啟請此類牌位；（2）往生者往生於一年之內。
							</td>
						</tr>
						<tr><th>歷代祖先蓮位</th>
							<td>
								為爲啟請人的歷代祖先申請設立牌位迴向祈福；請註明祖先姓氏（ 如『譚』氏 ）及啟請者的全名。
							</td>
						</tr>
						<tr><th>祈福消災牌位</th>
							<td>
								爲啟請人的父母、親友、師長、朋友，子女、晚輩、甚至本人等申請設立牌位祈福；<br/>
								請註明受益者的全名。
							</td>
						</tr>
						<tr><th>累劫冤親債主蓮位</th>
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
						<tr><th>用戶指南</th>
							<td>
								讓您閱讀此用戶指南。
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
						<tr><th style="18%;">刪除</th>
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
						<tr><th style="18%;">搜尋</th>
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
						<li>檔案名的延伸部份必須為 ".csv"。</li>
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
						<li>完成輸入牌位資料後請於左上角『檔案』的下拉選項中選擇『保存為』(Save As) 如下：<br/>
							<span style="color: blue; font-weight: bold;">
								(若您的微軟 EXCEL 為 2013 年或更早的版本，請繼續依循 <a href="#OLD">(j) 以下的存檔步骤。</a>)
							</span><br/><br/>
							<img src="./img/saveAsSel.png" alt="" style="width: 500px; height: auto;">
						</li><br/>
						<li>點擊『保存為』後，您會看到如下的『對話框』；請於『保存為』(Save As) 資料格中輸入您要的檔案名，
							並於『檔案格式』(File Format) 資料格中選擇 『CSV UTF-8』 為保存檔案的資料格式; 然後點擊右下角的『保存』(Save)。<br/><br/>
							<img src="./img/saveAsCSV.png" alt="" style="width: 700px; height: auto;">
						</li><br/>
						<li>微軟 EXCEL 會提醒您您只能保存當下所選標籤的輸入表為所要的格式，如下：<br/><br/>
							<img src="./img/saveActive.png" alt="" style="width: 400px; height: auto;">
						</li><br/>
						<li>在點擊『確認』/ [OK] 後，您的輸入表即會以『逗號分隔值』/ (CSV) 的資料格式及您所命名的檔案名，
							保存在您以所選擇的目錄中。
						</li>
						<li>對其他的輸入表，重復以上的步驟，將每一個輸入表存為『逗號分隔值』資料格式的檔案。
						</li><br/>
						<li id="OLD">
							<span style="color:blue; font-weight: bold;">
								以下存檔步驟適用於 2013 年及更早的微軟 EXCEL 版本。
							</span>
						</li><br/>
						<li>點擊『保存為』(Save As) 後，您會看到緊接的右邊有一個下拉選項，請點選『其他樣式』(Other Formats)，
							然後您會看到如下的『對話框』(Dialog Box)；請於『檔案名』(File Name) 資料格中輸入您要的檔案名, 
							並於『保存格式』(Save As Type) 資料格的下拉選項中選擇『CSV (MS-DOS) (*.csv)』為保存檔案的資料格式;
							然後點擊右下角的『保存』(Save)。<br/><br/>
							<img src="./img/o_saveAsCSV.png" alt="" style="width: 700px; height: auto;">
						</li><br/>
						<li>請用微軟的 Notepad 開啟您剛才所存的牌位檔案，並於左上角『檔案』的下拉選項中選擇『保存為』(Save As) 後，
							您會看到如下的『對話框』; 請於『檔案名』(File Name) 資料格中確認您要的檔案名, 於『保存格式』(Save As Type)
							資料格中確認格式為『All Files (*.*)』，並於『編碼』(Encoding) 資料格的下拉選項中點選『UTF-8』.
							然後點選在右下角的『保存』(Save)。<br/>
							<span style="color: blue; font-weight: bold;">
								請注意：『保存格式』必須為『All Files (*.*)』，這樣您的檔案名的延伸部份 『 .csv 』 才不會被更動；
								上載才會成功。
							</span><br/><br/>
							<img src="./img/o_SaveAsUTF8.png" alt="" style="width: 700px; height: auto;">
						</li><br/>
						<li>對其他的輸入表，請重復以上對您適用的步驟，將每一個輸入表存為『逗號分隔值』資料格式的檔案。
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
			User Guide to Request for Merit Dedication Name Plaques during Retreats</h2>
        <span style="display: block; width: 95%; margin: auto; padding-top: 2vh; font-size: 1.3em;
			font-weight: bold; line-height: 1.1em;"><!-- Intro phrase -->
            Users can apply for specific Merit Dedication Name Plaques during Retreats from this page:
			well-blessing, deceased and recently deceased beloved ones, ancestors, site guardians,
			karmic creditors, etc. You can also upload name plaque data.
        </span>
        <div style="width: 95%; margin:auto; font-size: 1.3em;"><!-- BEGIN Step-by-step Guide -->
            <ol>
                <li>
					When you click the "Name Plaque Application for Merit Dedication in Retreats",
					you will see the "Retreat Merit Dedication Request Page" with selections as below：<br/><br/>
					<img src="./img/e_pwMain.png" alt=""><br/><br/>
					<table>
						<tr><th>Deceased</th>
							<td>For dedicating merits to the requestor's parents, relatives, teachers, friends, sons, daughters,
								younger generations, even pets. If they deceased within 12 months from the
								retreat date, please use "Recently Deceased."
							</td>
						</tr>
						<tr><th>Recently Deceased</th>
							<td>The same as the above except (1) the deceased passed away within a year;
								(2) this is not for pets.
							</td>
						</tr>
						<tr><th>Ancestors</th>
							<td>For dedicating merits to the requestor's ancentors; you will just need to
								input your ancestor's surname, for example, "Johnson," and your full name.
							</td>
						</tr>
						<tr><th>Well Blessing</th>
							<td>For well blessing to the requestor's parents, relatives, teachers, friends,
								sons, daughters, younger generations, requestor-self, even pets.<br/>
								Please input well-blessing recipient's full name.
							</td>
						</tr>
						<tr><th>Karmic Creditors</th>
							<td>For dedicating merits to the requestor's karmic creditors in all life cycles.<br/>
								Please input the requestor's full name.
							</td>
						</tr>
						<tr><th>Site Gardians</th>
							<td>For dedicating merits to the site guardians (protecting beings) of the sites
								of your concern, e.g., residence, vacation home, work place.<br/>
								Plesae input the address of the sites of your concern.
							</td>
						</tr>
						<tr><th>User Guide</th>
							<td>Allows you to read this user guide.
							</td>
						</tr>
						<tr><th>Upload CSV Files</th>
							<td>To upload pre-edited Name Plaque files in CSV (comma separated values) format.
								The files must meet the format requirements.
							</td>
						</tr>
					</table>
				</li><br/>
				<li>When you click on any of the above name plaque selection, you will be able to edit or delete
					the name plaques of that type already in the system, search for specific ones of the same, or
					input new name plaque requests. We will take the "Recently Deceased" as an illustration example.<br/><br/>
					<img src="./img/e_editDaPaiWei.png" alt=""><br/><br/>
					<table>
						<tr><th>Delete</th>
							<td>Click "Delete": This particular Name Plaque entry from the database。</td>
						</tr>
						<tr><th>Edit</th>
							<td>The Name Plaque entry data is protected. When you click "Edit", every field is
								changeable.	When you done editing, please click "Update" to save changes in the
								database.
							</td>
						</tr>
					</table><br/>
					<img src="./img/e_saveChgDaPaiWei.png" alt=""><br/><br/>
					<table>
						<tr><th>Search</th>
							<td>Click on "Search" allows you to search for the Name Plaques to edit or delete</td>
						</tr>
						<tr><th>AddInputRow</th>
							<td>Click on "AddInputRow", a blank input row will be appended to the end of the
								data table; you may enter new Name Plaque data here.</td>
						</tr>
					</table><br/>
					<img src="./img/e_newDaPaiWei.png" alt=""><br/><br/>
					<table>
						<tr><th></th>
							<td>The "Title of the Deceased" and "Requestor's Title" describe the title and
								relationship between the deceased and the requestor. They are dropdown
								selection lists and have enumerated as exhaustively as possible the possible
								values. If yours is not found, please select BLANK or any value, then,
								use the "Edit" function to change to what fits your situation. In case this happens,
								please also kindly let us know so we can augment the lists.
							</td>
						</tr>
					</table>
				</li><br/>
				<li>If you would like to upload a pre-edited data file, it must comply with the following:<br/>
					<ol type="a">
						<li>The file name extention must be ".csv".</li>
						<li>The file is UTF-8 encoded;</li>
						<li>Every line contains one and only one Name Plaque data entry;</li>
						<li>Data fields in a line must be comma separated, i.e., CSV format;</li>
						<li>Every data field must have only one continuous text (no multiple lines);</li>
						<li>If a data field needs to have commas,
							it must be enclosed by double quotes, ( " ); for example, "text1, text2".</li>
					</ol>
				</li><br/>
				<li>Please follow the steps below to construct a CSV file proper for upload.
					<ol type="a">
						<li><a href="./Templates/e_pwTemplate.xlsx"><b>Download this template</b></a></li>
						<li>This is an MS EXCEL template file; every tab supports a Name Plaque type above.
							Select the tab for the Name Plaque type you want to input the Name Plaque data.<br/><br/>
							<img src="./img/e_inputXLS.png" alt="">
						</li><br/>
						<li>Press and hold the [Ctrl] key (or [cmd] key for Mac), click/select all colored
							illustration arrows as below:<br/><br/>
							<img src="./img/e_allImgXLS.png" alt="">
						</li><br/>
						<li>Click [Del] key to remove all of them; now you have a clean input sheet
							for entering the Name Plaque data as desired.
						</li>
						<li>When complete, click the "File" on the upper-left and hove over "Save As..." in
							the dropdown items, as below:<br/>
							<span style="color: blue; font-weight: bold;">
								If you have an MS EXCEL 2013 or older version, 
								<a href="#OLD_E">please continue with Step (j).</a>
							</span><br/><br/>
							<img src="./img/saveAsSel.png" alt="" style="width: 500px; height: auto;">
						</li><br/>
						<li>Click "Save As..." and you will see the dialog box as below;
							Please enter the filename in the "Save As:" field, and select "CSV UTF-8" from the dropdown
							list of the "File Format:" field; then, click "Save" on the bottom right.<br/><br/>
							<img src="./img/saveAsCSV.png" alt="" style="width: 700px; height: auto;">
						</li><br/>
						<li>MS EXCEL will remind you that only the Active Sheet can be saved in the desired format:<br/><br/>
							<img src="./img/saveActive.png" alt="" style="width: 400px; height: auto;">
						</li><br/>
						<li>Click "OK" and your input sheet is save in the CSV format and in the given filename and folder.
						</li>
						<li>Repeat the above steps across every tab if you have more than one input sheets.</li><br/>
						<li id="OLD_E">
							<span style="color:blue; font-weight:bold;">
								The following steps are applicable to MS EXCEL 2013 and older versions.
							</span>
						</li><br/>
						<li>After clicking "Save As", you will see a dropdown list immediately next to the right,
							click on "Other Formats" and a dialog box will be shown as below. In this dialog box,
							enter your file name in the "File Name" field, select "CSV (MS-DOS) (*.csv)" in the
							"Save As Type" field, and click "Save" button on the lower right bottom of the box.<br/><br/>
							<img src="./img/o_saveAsCSV.png" alt="" style="width: 700px; height: auto;">
						</li><br/>
						<li>Use MS Notepad and open the file you just saved. Click "File" on the upper left corner
							and select "Save As" in the dropdown list, and a dialog box will be shown as below.
							Please confirm the file name in the "File Name" field, select "All Files (*.*)"
							in the "Save As Type" field, and select "UTF-8" in the "Encoding" field. Finally,
							click "Save" on the lower right bottom of the box.<br/>
							<span style="color: blue; font-weight: bold;">
								Note: You must select "All Files (*.*)" in the "Save As Type" field such that
								your filename extension, ".csv", will be kept, which is required by upload.
							</span><br/><br/>
							<img src="./img/o_SaveAsUTF8.png" alt="" style="width: 700px; height: auto;">
						</li><br/>
						<li>Repeat applicable steps above across every tab if you have more than one input sheets.
						</li>
					</ol>
				</li><br/>
				<li>To upload pre-edited Name Plaque data files, please follow the steps below:
					<ol type="a">
						<li>Click "Upload CSV Files" on the upper right corner of the page,
							the following dialog box appears:<br/><br/>
							<img src="./img/e_upldBox.png" alt="" style="width: 600px; height: auto;">
						</li><br/>
						<li>Click "Browse" on the left and surf to the folder and select the file you want to upload.<br/><br/>
							<img src="./img/e_upldFile.png" alt="" style="width: 600px; height: auto;">
						</li><br/>
						<li>Select a Name Plaque type from the dropdown list on the right.<br/><br/>
							<img src="./img/e_upldPaiWei.png" alt="" style="width: 600px; height: auto;">
						</li><br/>
						<li>Click "Upload", and the data will be stored in the database.
						</li>
					</ol>
				</li>
            </ol>
        </div><!-- END Step-by-step Guide -->
		<h2 style="margin-top: 4vh; text-align: center; letter-spacing: 1px;">
			Thanks much for requesting for Merit Dedication Name Plaques. Amituofo!
		</h2>
		</div><!-- END for loading into the DataArea in PaiWei.php -->
<?php
	} // English version
?>
    </div><!-- DataArea -->
</body>
</html>