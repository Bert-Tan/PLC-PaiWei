<?php
	require_once("../pgConstants.php");
	require_once("dbSetup.php");
	require_once("PaiWei_DBfuncs.php");

	session_start(); // create or retrieve
	$sessLang = $_SESSION[ 'sessLang' ];
	
	$paiweiTable = $_POST [ 'dbTblName' ]; //PaiWei type
	
	//pdf configuration settings
	$pageSize = 'LEGAL'; $unit = 'in'; //inch
	$pageOrientation = ''; $topMargin = 0; $leftMargin = 0; $rightMargin = 0;
	$pdfTitle = '';
	//paiwei image
	$imgPath = ''; $imgWidth = 0; $imgHeight = 0; $imgType=''; $imgAlign = '';//default: right align at current line
	$paiweiNumPerPage = 0; //paiwei number on each page	
	
	//common PaiWei information (string)
	$prefixPaiwei = ''; $suffixPaiwei = ''; $commonStrPaiwei = ''; //PaiWei's prefix/suffix information and common string
	$prefixReq = '陽上'; $suffixReq = ''; //requester's prefix/suffix information
	
	//font settings
	$ChineseFont = 'cid0csv'; $EnglishFont = 'times'; 	
	$fontStylePaiwei = ''; $fontStyleReq = ''; $fontStyleAddress = ''; //font style of PaiWei, requester, and DiJiZhu (Address)
	$fontSizePaiwei = 0; $fontSizeReq = 0; $fontSizeAddress = 0; //font size of PaiWei, requester, and DiJiZhu (Address)
	$fontSizePrefixPaiwei = 0; $fontSizePrefixReq = 0; //font size of the prefix/suffix of PaiWei and requesters
	
	//English character rotate settings
	$rotateDegree = 270;
	$rotateXadjustPaiwei = 0; //X position adjustment of rotation for PaiWei's string (value related to font size)
	$textXadjustPaiwei = 0; //X position adjustment of English PaiWei text after rotation
	$rotateXadjustReq = 0; //X position adjustment of rotation for Requester's string (value related to font size)
	$textXadjustReq = 0; //X position adjustment of English requester text after rotation
	$rotateXadjustAddress = 0; //X position adjustment of rotation for DiJiZhu address's string (value related to font size)
	$textXadjustAddress = 0; //X position adjustment of English DiJiZhu address requester text after rotation
	
	//PaiWei information position settings
	$xIniPaiwei = 0; $xStepPaiwei = 0; //X position of the first (initial) paiwei, step of X position (for each Xiaopaiwei)
	$yPaiweiCommon = 0; $yPrefixPaiwei = 0; $ySuffixPaiwei = 0; //Y position of PaiWei (common string), prefix and suffix
	$yTopPaiwei = 0; $yBottomPaiwei = 0; //the top-most and bottom-most Y position of PaiWei information
	
	//DiJiZhu (address) information position settings
	$xIniAddress = 0; $xStepAddress = 0; //X position of the first (initial) DiJiZhu address, step of X position (for each DiJiZhu address)
	$yTopAddress = 0; $yBottomAddress = 0; //the top-most and bottom-most Y position of DiJiZhu address
	
	//requester information position settings
	$xIniReq = 0; $xStepReq = 0; //X position of the first (initial) paiwei requester, step of X position (for each Xiaopaiwei requester)
	$yPrefixReq = 0; $ySuffixReq = 0; //Y position of requester's prefix and suffix
	$yTopReq = 0; $yBottomReq = 0; //the top-most and bottom-most Y position of requester information

	//multiple lines X position adjust settings
	$mulLineXadjustPaiwei = 0; $mulLineXadjustReq = 0; $mulLineXadjustAddress = 0;	
	
	//set PDF page orientation
	switch ($paiweiTable) {
		case 'DaPaiWei':
			$pageOrientation='P';
			break;	
		default:
			$pageOrientation='L';
			break;
	}
	
	//create new PDF document
	$pdf = new TCPDF($pageOrientation, $unit, $pageSize, true, 'UTF-8', false);
	
	//set PaiWei configurations
	setConfigure();

	//set PDF document information
	$pdf->SetTitle($pdfTitle);
	$pdf->SetCreator('淨土念佛堂、圖書館');
	$pdf->SetAuthor('淨土念佛堂、圖書館');
	$pdf->SetSubject($pdfTitle);
	//remove default header/footer
	$pdf->setPrintHeader(false);
	$pdf->setPrintFooter(false);
	//set margins
	$pdf->SetMargins($leftMargin, $topMargin, $rightMargin, true);
	
	//data array
	$paiweiArray = array(); //PaiWei data
	$reqArray = array(); //requester data
	$reqSuffixArray = array(); //requester suffix ('叩薦'or'敬薦') data
	$addressArray = array(); //DiJiZhu address data

	if($_POST[ 'dnldUsrName' ] == 'BLANK') { // print BLANK PaiWei sheet
		printBlankPaiweiSheet();
	}
	else {
		getData(); //get data
	
		if(count($paiweiArray)==0 && count($reqArray)==0) { // NO PaiWei data to print 
			$pdf->SetMargins(1,1);
			$pdf->AddPage(); //add a page

			if($sessLang == SESS_LANG_CHN) {
				$pdf->SetFont('cid0csh', 'B', 18);
				$pdf->Write(0, '沒有（驗證過的）'.xLate($paiweiTable).'！', '', 0, 'L', true, 0, false, false, 0);
			}
			else {
				$pdf->SetFont('times', 'B', 18);
				$pdf->Write(0, 'There is NO (validated) '.xLate($paiweiTable).'!', '', 0, 'L', true, 0, false, false, 0);
			}		
		}
		else { // HAVE PaiWei data to print
			$paiweiCount = ( count($paiweiArray)==0 ) ? count($reqArray) : count($paiweiArray);
		
			for($i=0; $i<ceil($paiweiCount/$paiweiNumPerPage); ++$i) {
				$pdf->AddPage(); //add a page
				//put PaiWei images
				for ($j=0; $j<$paiweiNumPerPage; ++$j) {
					$pdf->Image($imgPath, '', '', $imgWidth, $imgHeight, $imgType, '', 'T', true, 300, $imgAlign, false, false, 0, false, false, false);
				}
				//reset positions
				$pdf->SetAbsXY(0, 0, false);
				$xPaiwei = $xIniPaiwei;
				$xReq = $xIniReq;
				$xAddress = $xIniAddress;

				for($j=0; $j<$paiweiNumPerPage; ++$j) {
					//print prefix and suffix of PaiWei
					$pdf->SetFont($ChineseFont, $fontStylePaiwei, $fontSizePrefixPaiwei);
					$pdf->Text($xPaiwei, $yPrefixPaiwei, $prefixPaiwei, false, false, true, 0, 1, 'L', false, '', 0, false, 'T', 'M', false);
					$pdf->Text($xPaiwei, $ySuffixPaiwei, $suffixPaiwei, false, false, true, 0, 1, 'L', false, '', 0, false, 'T', 'M', false);			

					$index = $i*$paiweiNumPerPage + $j; // data index in the array
					$dataType = '';

					//print PaiWei data
					if(count($paiweiArray) > 0) {
						$dataType = 'PaiWei';
						if($index < $paiweiCount) //some PaiWei images maybe empty (no PaiWei information)
							printStr($paiweiArray[$index], $fontStylePaiwei, $fontSizePaiwei, $xPaiwei, $yTopPaiwei, $yBottomPaiwei, $rotateXadjustPaiwei, $textXadjustPaiwei, $mulLineXadjustPaiwei, $dataType);
						else { //empty PaiWei data, just print common PaiWei string (i.e., '氏歷代祖先')	
							printCommonPaiweiData($xPaiwei);			
						}
					}
					else { //paiWei data are common (i.e., '地基主' and '累劫冤親債主')
						printCommonPaiweiData($xPaiwei);			
					}
				
					//print requester data
					if(count($reqArray) > 0) {
						$dataType = 'Req';
						if($index < $paiweiCount) { //some PaiWei images maybe empty (no requester information)
							$lineNum = printStr($reqArray[$index], $fontStyleReq, $fontSizeReq, $xReq, $yTopReq, $yBottomReq, $rotateXadjustReq, $textXadjustReq, $mulLineXadjustReq, $dataType);

							//adjusted requester's prefix/suffix X position (center-aligned, multiple lines of requester information)
							$xReqAdjusted = $xReq - ($lineNum-1)*$mulLineXadjustReq;

							//print requester's prefix and suffix
							$pdf->SetFont($ChineseFont, $fontStyleReq, $fontSizePrefixReq);
							$pdf->Text($xReqAdjusted, $yPrefixReq, $prefixReq, false, false, true, 0, 1, 'L', false, '', 0, false, 'T', 'M', false);
							$pdf->Text($xReqAdjusted, $ySuffixReq, $reqSuffixArray[$index], false, false, true, 0, 1, 'L', false, '', 0, false, 'T', 'M', false);
						}
						else { //empty requester data, just print requester's common prefix and suffix
							printCommonRequesterData($xReq);
						}
					}			
			
					//print DiJiZhu address data
					if(count($addressArray) > 0) {
						$dataType = 'Address';
						if($index < $paiweiCount) //some PaiWei images maybe empty (no DiJiZhu address information)
							printStr($addressArray[$index], $fontStyleAddress, $fontSizeAddress, $xAddress, $yTopAddress, $yBottomAddress, $rotateXadjustAddress, $textXadjustAddress, $mulLineXadjustAddress, $dataType);
					}

					//calculate X position for the next PaiWei
					$xPaiwei = $xPaiwei - $xStepPaiwei;
					$xReq = $xReq - $xStepReq;
					$xAddress = $xAddress - $xStepAddress;
				}	
			}		
		}
	}	


	//Close and output PDF document
	$pdf->Output($pdfTitle.'.pdf', 'I');




	
	//set pdf configuratons
	function setConfigure() {
		
		global $paiweiTable, $pdf, $ChineseFont, $EnglishFont;
		global $topMargin, $leftMargin, $rightMargin;
		global $pdfTitle, $imgPath, $imgWidth, $imgHeight, $imgType, $imgAlign, $paiweiNumPerPage;
		global $prefixPaiwei, $suffixPaiwei, $commonStrPaiwei, $suffixReq;
		global $fontStylePaiwei, $fontStyleReq, $fontStyleAddress; 
		global $fontSizePaiwei, $fontSizeReq, $fontSizeAddress, $fontSizePrefixPaiwei, $fontSizePrefixReq;
		global $rotateXadjustPaiwei, $textXadjustPaiwei, $rotateXadjustReq;
		global $textXadjustReq, $rotateXadjustAddress, $textXadjustAddress;
		global $xIniPaiwei, $xStepPaiwei, $yPaiweiCommon, $yPrefixPaiwei, $ySuffixPaiwei, $yTopPaiwei, $yBottomPaiwei;
		global $xIniAddress, $xStepAddress, $yTopAddress, $yBottomAddress;
		global $xIniReq, $xStepReq, $yPrefixReq, $ySuffixReq, $yTopReq, $yBottomReq;
		global $mulLineXadjustPaiwei, $mulLineXadjustReq, $mulLineXadjustAddress;
		
		switch ($paiweiTable) {
			case 'C001A':
				$topMargin=0.7; $leftMargin=0.2; $rightMargin=0.2; $pdfTitle='祈福消災牌位';
				$imgPath='img/XiaoPaiWei.png'; $imgWidth=2.42; $imgHeight=7.5; $imgType='PNG';
				$paiweiNumPerPage=6; $prefixPaiwei='佛光注照'; $suffixPaiwei='長生祿位';
				$fontSizePaiwei=18; $fontSizePrefixPaiwei=20; $fontStylePaiwei='B';
				$rotateXadjustPaiwei=1.05*$pdf->GetStringWidth('G', $EnglishFont, $fontStylePaiwei, $fontSizePaiwei, false);
				$textXadjustPaiwei=2.2*$pdf->GetStringWidth('G', $EnglishFont, $fontStylePaiwei, $fontSizePaiwei, false);
				$xIniPaiwei=12.64; $xStepPaiwei=2.265; $yPrefixPaiwei=2.0; $ySuffixPaiwei=5.5; $yTopPaiwei=3.2; $yBottomPaiwei=5.45;
				$mulLineXadjustPaiwei=0.13;	
				break;			
			case 'D001A':
				$topMargin=0.7; $leftMargin=0.2; $rightMargin=0.2; $pdfTitle='地基主蓮位';
				$imgPath='img/XiaoPaiWei.png'; $imgWidth=2.42; $imgHeight=7.5; $imgType='PNG';
				$paiweiNumPerPage=6; $prefixPaiwei='佛力超薦'; $suffixPaiwei='往生蓮位'; $commonStrPaiwei='地基主'; $suffixReq='敬薦';
				$fontSizePaiwei=18; $fontSizePrefixPaiwei=20; $fontSizeReq=14; $fontSizePrefixReq=16; $fontSizeAddress=12;
				$fontStylePaiwei='B'; $fontStyleReq='B'; $fontStyleAddress='';
				$rotateXadjustPaiwei=1.05*$pdf->GetStringWidth('G', $EnglishFont, $fontStylePaiwei, $fontSizePaiwei, false);
				$textXadjustPaiwei=2.2*$pdf->GetStringWidth('G', $EnglishFont, $fontStylePaiwei, $fontSizePaiwei, false);
				$rotateXadjustReq=1.1*$pdf->GetStringWidth('G', $EnglishFont, $fontStyleReq, $fontSizeReq, false);
				$textXadjustReq=2.2*$pdf->GetStringWidth('G', $EnglishFont, $fontStyleReq, $fontSizeReq, false);
				$rotateXadjustAddress=1.2*$pdf->GetStringWidth('G', $EnglishFont, $fontStyleAddress, $fontSizeAddress, false);
				$textXadjustAddress=2.3*$pdf->GetStringWidth('G', $EnglishFont, $fontStyleAddress, $fontSizeAddress, false);
				$xIniPaiwei=12.64; $xStepPaiwei=2.265; $yPrefixPaiwei=2.0; $ySuffixPaiwei=5.5; $yPaiweiCommon=3.95;
				$xIniReq=11.725; $xStepReq=2.265; $yPrefixReq=3.2; $ySuffixReq=6.0; $yTopReq=3.75; $yBottomReq=5.95;
				$xIniAddress=13.125; $xStepAddress=2.265; $yTopAddress=2.05; $yBottomAddress=6.35;
				$mulLineXadjustPaiwei=0.13; $mulLineXadjustReq=0.1; $mulLineXadjustAddress=0.08;
				break;
			case 'L001A':
				$topMargin=0.7; $leftMargin=0.2; $rightMargin=0.2; $pdfTitle='歷代祖先蓮位';
				$imgPath='img/XiaoPaiWei.png'; $imgWidth=2.42; $imgHeight=7.5; $imgType='PNG';
				$paiweiNumPerPage=6;
				$prefixPaiwei='佛力超薦'; $suffixPaiwei='往生蓮位'; $commonStrPaiwei='氏歷代祖先'; $suffixReq='叩薦';
				$fontSizePaiwei=18; $fontSizePrefixPaiwei=20; $fontSizeReq=14; $fontSizePrefixReq=16;
				$fontStylePaiwei='B'; $fontStyleReq='B';
				$rotateXadjustPaiwei=1.05*$pdf->GetStringWidth('G', $EnglishFont, $fontStylePaiwei, $fontSizePaiwei, false);
				$textXadjustPaiwei=2.2*$pdf->GetStringWidth('G', $EnglishFont, $fontStylePaiwei, $fontSizePaiwei, false);
				$rotateXadjustReq=1.1*$pdf->GetStringWidth('G', $EnglishFont, $fontStyleReq, $fontSizeReq, false);
				$textXadjustReq=2.2*$pdf->GetStringWidth('G', $EnglishFont, $fontStyleReq, $fontSizeReq, false);
				$xIniPaiwei=12.64; $xStepPaiwei=2.265; $yPrefixPaiwei=2.0; $ySuffixPaiwei=5.5; $yTopPaiwei=3.2; $yBottomPaiwei=5.45; $yPaiweiCommon=3.85;	
				$xIniReq=11.725; $xStepReq=2.265; $yPrefixReq=3.2; $ySuffixReq=6.0; $yTopReq=3.75; $yBottomReq=5.95;
				$mulLineXadjustPaiwei=0.13; $mulLineXadjustReq=0.1;
				break;
			case 'Y001A':
				$topMargin=0.7; $leftMargin=0.2; $rightMargin=0.2; $pdfTitle='累劫冤親債主蓮位'; 
				$imgPath='img/XiaoPaiWei.png'; $imgWidth=2.42; $imgHeight=7.5; $imgType='PNG';
				$paiweiNumPerPage=6;
				$prefixPaiwei='佛力超薦'; $suffixPaiwei='往生蓮位'; $commonStrPaiwei='累劫冤親債主'; $suffixReq='敬薦';
				$fontSizePaiwei=18; $fontSizePrefixPaiwei=20; $fontSizeReq=14; $fontSizePrefixReq=16;
				$fontStylePaiwei='B'; $fontStyleReq='B';
				$rotateXadjustPaiwei=1.05*$pdf->GetStringWidth('G', $EnglishFont, $fontStylePaiwei, $fontSizePaiwei, false);
				$textXadjustPaiwei=2.2*$pdf->GetStringWidth('G', $EnglishFont, $fontStylePaiwei, $fontSizePaiwei, false);
				$rotateXadjustReq=1.1*$pdf->GetStringWidth('G', $EnglishFont, $fontStyleReq, $fontSizeReq, false);
				$textXadjustReq=2.2*$pdf->GetStringWidth('G', $EnglishFont, $fontStyleReq, $fontSizeReq, false);
				$xIniPaiwei=12.645; $xStepPaiwei=2.265; $yPrefixPaiwei=2.0; $ySuffixPaiwei=5.5; $yPaiweiCommon=3.575;
				$xIniReq=11.725; $xStepReq=2.265; $yPrefixReq=3.2; $ySuffixReq=6.0; $yTopReq=3.75; $yBottomReq=5.95;
				$mulLineXadjustReq=0.1;
				break;
			case 'W001A_4':
				$topMargin=0.7; $leftMargin=0.2; $rightMargin=0.2; $pdfTitle='往生者蓮位';
				$imgPath='img/XiaoPaiWei.png'; $imgWidth=2.42; $imgHeight=7.5; $imgType='PNG';
				$paiweiNumPerPage=6; $prefixPaiwei='佛力超薦'; $suffixPaiwei='往生蓮位'; $suffixReq='叩薦';
				$fontSizePaiwei=18; $fontSizePrefixPaiwei=20; $fontSizeReq=14; $fontSizePrefixReq=16;
				$fontStylePaiwei='B'; $fontStyleReq='B';
				$rotateXadjustPaiwei=1.05*$pdf->GetStringWidth('G', $EnglishFont, $fontStylePaiwei, $fontSizePaiwei, false);
				$textXadjustPaiwei=2.2*$pdf->GetStringWidth('G', $EnglishFont, $fontStylePaiwei, $fontSizePaiwei, false);
				$rotateXadjustReq=1.1*$pdf->GetStringWidth('G', $EnglishFont, $fontStyleReq, $fontSizeReq, false);
				$textXadjustReq=2.2*$pdf->GetStringWidth('G', $EnglishFont, $fontStyleReq, $fontSizeReq, false);
				$xIniPaiwei=12.64; $xStepPaiwei=2.265; $yPrefixPaiwei=2.0; $ySuffixPaiwei=5.5; $yTopPaiwei=3.2; $yBottomPaiwei=5.45;
				$xIniReq=11.725; $xStepReq=2.265; $yPrefixReq=3.2; $ySuffixReq=6.0; $yTopReq=3.75; $yBottomReq=5.95;
				$mulLineXadjustPaiwei=0.13; $mulLineXadjustReq=0.1;				
				break;
			case 'DaPaiWei':
				$topMargin=0.5; $leftMargin=0.0; $rightMargin=0.0; $pdfTitle='大牌位'; 
				$imgPath='img/DaPaiWei.jpg'; $paiweiNumPerPage=1; $imgType='JPG'; $imgAlign='C';//center align
				$imgWidth=0; $imgHeight=0; //TCPDF calculate image width and height automatically
				$fontStylePaiwei='B'; $fontStyleReq='B'; $suffixReq='叩薦';
				$fontSizePaiwei=24; $fontSizeReq=18; $fontSizePrefixReq=20;
				$rotateXadjustPaiwei=1.0*$pdf->GetStringWidth('G', $EnglishFont, $fontStylePaiwei, $fontSizePaiwei, false);
				$textXadjustPaiwei=2.2*$pdf->GetStringWidth('G', $EnglishFont, $fontStylePaiwei, $fontSizePaiwei, false);
				$rotateXadjustReq=1.05*$pdf->GetStringWidth('G', $EnglishFont, $fontStyleReq, $fontSizeReq, false);
				$textXadjustReq=2.2*$pdf->GetStringWidth('G', $EnglishFont, $fontStyleReq, $fontSizeReq, false);			
				$xIniPaiwei=4.27; $xStepPaiwei=0; $yTopPaiwei=4.65; $yBottomPaiwei=9.4;							
				$xIniReq=2.75; $xStepReq=0; $yPrefixReq=6.2; $ySuffixReq=10.4; $yTopReq=6.85; $yBottomReq=10.35;
				$mulLineXadjustPaiwei=0.19; $mulLineXadjustReq=0.14;
				break;	
		}		
	}
	
	//search database to get data
	function getData() {	
		global $_db, $paiweiTable;
		global $paiweiArray, $reqArray, $reqSuffixArray, $addressArray;
		global $_SESSION;		

		$_blank = '(空白|BLANK)'; // regExp to blank out the blank data field	
		$KouJian = '(叩薦|Sincerely Recommend)'; //regExp to filter '叩薦'
		$JingJian = '(敬薦|Recommend)'; //regExp to filter '敬薦'
		$_tblFlds = getpaiweiTblFlds( $paiweiTable ); // $_tblFlds[0] contains ID which is not needed
		
		//the field/column of PaiWei, requester, and DiJiZhu address in Database Tables
		$paiweiFlds = ''; $reqFlds = ''; $addressFlds = '';
		switch ($paiweiTable) {
			case 'C001A':
				$paiweiFlds = "ifnull($_tblFlds[1], '') AS $_tblFlds[1]";
				break;			
			case 'D001A':
				$reqFlds = "ifnull($_tblFlds[2], '') AS $_tblFlds[2]";
				$addressFlds = "ifnull($_tblFlds[1], '') AS $_tblFlds[1]";
				break;
			case 'L001A':
				$paiweiFlds = "concat( ifnull($_tblFlds[1], ''), '氏歷代祖先') AS $_tblFlds[1]";
				$reqFlds = "ifnull($_tblFlds[2], '') AS $_tblFlds[2]";
				break;
			case 'Y001A':				
				$reqFlds = "ifnull($_tblFlds[1], '') AS $_tblFlds[1]";
				break;
			case 'W001A_4':
				$paiweiFlds = "concat( ifnull($_tblFlds[1], ''), ' ', ifnull($_tblFlds[2], '') ) AS $_tblFlds[2]";
				$reqFlds = "concat( ifnull($_tblFlds[3], ''), ' ', ifnull($_tblFlds[4], '') ) AS $_tblFlds[4]";
				break;
			case 'DaPaiWei':
				$paiweiFlds = "concat( ifnull($_tblFlds[1], ''), ' ', ifnull($_tblFlds[2], '') ) AS $_tblFlds[2]";
				$reqFlds = "concat( ifnull($_tblFlds[4], ''), ' ', ifnull($_tblFlds[5], '') ) AS $_tblFlds[5]";
				break;
		}

		//session_start();
		$lastRtrtDate = $_SESSION[ 'lastRtrtDate' ];
		if(! isset($lastRtrtDate)) {
			$rslt = $_db->query("SELECT * FROM `pwParam`;");
			$lastRtrtDate = $rslt->fetch_all(MYSQLI_ASSOC)[0][ 'lastRtrtDate' ];
		}
				
		//PaiWei data
		if($paiweiFlds != '') {
			$_selFlds = $paiweiFlds;
			$_sql = getSQL($_selFlds, $lastRtrtDate);
			$_rslt = $_db->query( $_sql );
			$_Rows = $_rslt->fetch_all ( MYSQLI_NUM );	

			foreach ( $_Rows as $_Row ) {
				$str = implode('', $_Row );
				$str = preg_replace( $_blank, '', $str ); //blank out the blank data field
				array_push($paiweiArray, trim($str, ' '));
			}
		}
		
		//requester data
		if($reqFlds != '') {
			$_selFlds = $reqFlds;
			$_sql = getSQL($_selFlds, $lastRtrtDate);
			$_rslt = $_db->query( $_sql );
			$_Rows = $_rslt->fetch_all ( MYSQLI_NUM );

			foreach ( $_Rows as $_Row ) {
				$str = implode('', $_Row );				
				$str = preg_replace( $_blank, '', $str ); //blank out the blank data field
				//blank out the requester's suffix
				preg_match($KouJian, $str, $matches);
				if(count($matches) > 0) {
					$str = preg_replace( $KouJian, '', $str );
					array_push($reqSuffixArray, '叩薦');
				}
				else {
					$str = preg_replace( $JingJian, '', $str );
					array_push($reqSuffixArray, '敬薦');
				}
				array_push($reqArray, trim($str, ' '));
			}
		}
		
		//DiJiZhu address data
		if($addressFlds != '') {
			$_selFlds = $addressFlds;
			$_sql = getSQL($_selFlds, $lastRtrtDate);
			$_rslt = $_db->query( $_sql );
			$_Rows = $_rslt->fetch_all ( MYSQLI_NUM );
			
			foreach ( $_Rows as $_Row ) {
				$str = implode('', $_Row );
				array_push($addressArray, trim($str, ' '));
			}
		}

		$_db->close();		
	}	
	
	//get query SQL statement
	function getSQL($_selFlds, $lastRtrtDate) {
		global $paiweiTable;
		if ( $_POST[ 'dnldUsrName' ] == 'ALL' ) {
			//group PaiWei data by pwUsrName
			$_sql = "SELECT $_selFlds FROM {$paiweiTable} LEFT JOIN pw2Usr "
				  . "ON (ID = pwID AND TblName = \"{$paiweiTable}\") "
				  . "WHERE timestamp > \"{$lastRtrtDate}\" "
				  . "ORDER BY pwUsrName, ID;";
		} else {
			$_dnldUsrName = $_POST[ 'dnldUsrName' ];
			$_sql	= "SELECT {$_selFlds} FROM {$paiweiTable} WHERE ID IN "
					. "(SELECT pwID FROM pw2Usr WHERE TblName = \"{$paiweiTable}\" AND pwUsrName = \"{$_dnldUsrName}\") "
					. "AND timestamp > \"{$lastRtrtDate}\" "
					. "ORDER BY ID;";
		}
		return $_sql;
	}
	
	//print data
	function printStr($str, $fontStyle, $fontSize, $x, $yTop, $yBottom, $rotateXadjust, $textXadjust, $mulLineXadjust, $dataType) {
		global $pdf, $ChineseFont, $EnglishFont, $rotateDegree;
		$subStrsArray = Array();
				
		$strings = splitWithChinese($str); //split into Chinese and English substrings
		$strWidths = calWidths($strings, $fontSize, $fontStyle); //calculate widths of each substring	
		$totalWidth = array_sum($strWidths); //total width of the string
		$maxWidth = $yBottom - $yTop; //maximal line width
		$EnglishSplitSymbol = ( $dataType=='Address' ) ? ',' : '\s'; //multiple lines: split Address and PaiWei/requester English string with commas(',') and whitespaces('\s') respectively
		if($totalWidth > $maxWidth)
			$subStrsArray = splitWithWidth($strings, $strWidths, $totalWidth, $maxWidth, $fontSize, $fontStyle, $EnglishSplitSymbol);
		else
			array_push($subStrsArray, $strings); //2-D array, be consist with multiple lines data structure
		
		//calculate initial X position (i.e., the first line)
		//PaiWei data: lines center-aligned (based on PaiWei's prefix/suffix string)
		//requester/address data: lines right-aligned (based on pre-defined requester/address prefix/suffix string position)
		$x = ( $dataType=='PaiWei' ) ? $x + (count($subStrsArray)-1)*$mulLineXadjust : $x;
		
		//print each PaiWei/requester/address line
		for($j=0; $j<count($subStrsArray); ++$j) {
			$subStrs = $subStrsArray[$j];
			$widths = calWidths($subStrs, $fontSize, $fontStyle); //calculate widths of each substring			
			$isChinese = checkChinese($subStrs); //check whether each substring is Chinese or English
			$y = calYposition($widths, $yTop, $yBottom); //calculate initial Y position
			
			//print each substring
			for($i=0; $i<count($subStrs); ++$i) {
				if($isChinese[$i]) { //Chinese
					$pdf->SetFont($ChineseFont, $fontStyle, $fontSize);
					$pdf->Text($x, $y, $subStrs[$i], false, false, true, 0, 1, 'L', false, '', 0, false, 'T', 'M', false);
				}
				else { //English
					$pdf->SetFont($EnglishFont, $fontStyle, $fontSize);
					//Rotate English
					$pdf->StartTransform();				
					$pdf->Rotate($rotateDegree, $x+$rotateXadjust, $y);
					$pdf->Text($x+$textXadjust, $y, $subStrs[$i], false, false, true, 0, 1, 'L', false, '', 0, false, 'T', 'M', false);
					$pdf->StopTransform();
				}
			
				//calculate Y position for the next substring
				$y = $y + $widths[$i];
			}
			
			//calculate X position for the next line
			$x = $x - 2*$mulLineXadjust;
		}	
		
		//return line count, to adjust requester's prefix/suffix string center-aligned
		return count($subStrsArray);
	}
	
	//split a string into substrings only containing Chinese and non-Chinese characters
	function splitWithChinese($str) {
		//English (Latin) characters, numbers (digits), whitespaces, and English symbols ('&' and ',' and '.' and '#' and '-')
		$regExp = '/(?<=[^\p{Latin}0-9\&\,\.\#\-\s])(?=[\p{Latin}0-9\&\,\.\#\-\s])|(?<=[\p{Latin}0-9\&\,\.\#\-\s])(?=[^\p{Latin}0-9\&\,\.\#\-\s])/u';
		$subStrings = preg_split($regExp, $str, -1);
		return $subStrings;
	}
	
	//calculate the Width of each string based on corresponding font
	function calWidths($strs, $fontSize, $fontStyle) {
		global $pdf, $ChineseFont, $EnglishFont;
		$widths = array();
		foreach($strs as $str) {
			$width = 0;
			preg_match('/[\p{Han}、，]+/u', $str, $matches); //Chinese characters and Chinese symbols ('、' and '，')
			if(count($matches) > 0) //Chinese string
				$width = $pdf->GetStringWidth($str, $ChineseFont, $fontStyle, $fontSize, false);
			else
				$width = $pdf->GetStringWidth($str, $EnglishFont, $fontStyle, $fontSize, false);
			array_push($widths, $width);
		}
		return $widths;
	}
	
	//check string is Chinese or English
	function checkChinese($strs) {
		$isChinese = array();
		foreach($strs as $str) {
			preg_match('/[\p{Han}、，]+/u', $str, $matches); //Chinese characters and Chinese symbols ('、' and '，')
			if(count($matches) > 0) //Chinese string
				array_push($isChinese, true);
			else
				array_push($isChinese, false);
		}
		return $isChinese;
	}
	
	//calculate the Y position (center alignment) of the first character of Xiaopaiwei
	function calYposition($widths, $yTop, $yBottom) {
		$totalWidth = array_sum($widths);
		$y = $yTop + ($yBottom-$yTop-$totalWidth)/2;
		return $y;
	}
	
	//split a string set into multiple string sets based on width (multiple print lines)
	function splitWithWidth($strs, $strWidths, $totalWidth, $maxWidth, $fontSize, $fontStyle, $EnglishSplitSymbol) {
		$newStrsArray = array(); //2-D array, each element is one print line
		$newStrs = array(); //1-D array, element of $newStrsArray
		$temWidth = 0;
				
		$lineCount = ceil($totalWidth / $maxWidth); //line count
		$lineWidth = $totalWidth / $lineCount; //minimal line width (for all line, the current line is SHORTER than its previous line)
		$isChinese = checkChinese($strs); //check whether each string is Chinese or English
		
		for($i=0; $i<count($strs); ++$i) {
			$temWidth = $temWidth + $strWidths[$i];
			
			if($temWidth > $lineWidth) {
				$temWidth = $temWidth - $strWidths[$i];
				
				if($isChinese[$i]) {
					$splitRegExp = '//u';
					$concatSymbol = '';
				}
				else {
					$splitRegExp = '/[' . $EnglishSplitSymbol . ']+/u';
					$concatSymbol = ( $EnglishSplitSymbol=='\s' ) ? ' ' : $EnglishSplitSymbol;
				}
				
				$pieces = preg_split($splitRegExp, $strs[$i]);
				$pieceWidths = calWidths($pieces, $fontSize, $fontStyle); //calculate widths of each piece (string)
				
				$currentLine = ''; 
				for($j=0; $j<count($pieces); ++$j) {
					$temWidth = $temWidth + $pieceWidths[$j];
					
					if($temWidth >= $lineWidth) {
						if($temWidth <= $maxWidth)	{							
							$currentLine = ($j == count($pieces)-1)? $currentLine.$pieces[$j] : $currentLine.$pieces[$j].$concatSymbol;
							
							array_push($newStrs, trim($currentLine, ' '));
							array_push($newStrsArray, $newStrs);
							
							$newStrs = array();	
							$currentLine = '';							
							$temWidth = 0;
						}
						else {							
							array_push($newStrs, trim($currentLine, ' '));
							array_push($newStrsArray, $newStrs);
							
							$newStrs = array();							
							$currentLine = ($j == count($pieces)-1)? $pieces[$j] : $pieces[$j].$concatSymbol;								
							$temWidth = $pieceWidths[$j];							
						}
													
					}
					else {
						$currentLine = ($j == count($pieces)-1)? $currentLine.$pieces[$j] : $currentLine.$pieces[$j].$concatSymbol;
					}				
				}
				
				if(trim($currentLine, ' ') != '') {
					array_push($newStrs, trim($currentLine, ' '));
					if($i == count($strs)-1) array_push($newStrsArray, $newStrs);					
				}
				
				
			}
			elseif($temWidth == $lineWidth) {
				array_push($newStrs, $strs[$i]);
				array_push($newStrsArray, $newStrs);
				
				$newStrs = array();
				$temWidth = 0;
			}
			else {
				array_push($newStrs, $strs[$i]);
				if($i == count($strs)-1) array_push($newStrsArray, $newStrs);
			}						
		}	
		
		return $newStrsArray;
	}


	//print BLANK PaiWei sheet
	function printBlankPaiweiSheet() {	
		//?????
		global $paiweiTable;
		global $pdf, $ChineseFont, $fontStylePaiwei, $fontSizePrefixPaiwei;	
		global $paiweiNumPerPage, $imgPath, $imgWidth, $imgHeight, $imgType, $imgAlign;
		global $xIniPaiwei, $xIniReq, $xStepPaiwei, $xStepReq;
		global $yPrefixPaiwei, $prefixPaiwei, $ySuffixPaiwei, $suffixPaiwei;

		
		$pdf->AddPage(); //add a page		
		//put PaiWei images
		for ($j=0; $j<$paiweiNumPerPage; ++$j) {
			$pdf->Image($imgPath, '', '', $imgWidth, $imgHeight, $imgType, '', 'T', true, 300, $imgAlign, false, false, 0, false, false, false);
		}
		//reset positions
		$pdf->SetAbsXY(0, 0, false);
		$xPaiwei = $xIniPaiwei;
		$xReq = $xIniReq;
			
		for($j=0; $j<$paiweiNumPerPage; ++$j) {
			//print prefix and suffix of PaiWei
			$pdf->SetFont($ChineseFont, $fontStylePaiwei, $fontSizePrefixPaiwei);
			$pdf->Text($xPaiwei, $yPrefixPaiwei, $prefixPaiwei, false, false, true, 0, 1, 'L', false, '', 0, false, 'T', 'M', false);
			$pdf->Text($xPaiwei, $ySuffixPaiwei, $suffixPaiwei, false, false, true, 0, 1, 'L', false, '', 0, false, 'T', 'M', false);			
	
			switch ($paiweiTable) {
				case 'C001A':					
					break;			
				case 'D001A':
					printCommonPaiweiData($xPaiwei);
					printCommonRequesterData($xReq);
					break;
				case 'L001A':
					printCommonPaiweiData($xPaiwei);
					printCommonRequesterData($xReq);
					break;
				case 'Y001A':	
					printCommonPaiweiData($xPaiwei);
					printCommonRequesterData($xReq);
					break;
				case 'W001A_4':					
					printCommonRequesterData($xReq);					
					break;
				case 'DaPaiWei':					
					printCommonRequesterData($xReq);
					break;
			}	
			
			//calculate X position for the next PaiWei
			$xPaiwei = $xPaiwei - $xStepPaiwei;
			$xReq = $xReq - $xStepReq;
		}	
	}

	// print common PaiWei string (i.e., '氏歷代祖先', '地基主' and '累劫冤親債主')
	function printCommonPaiweiData($xPaiwei) {		
		global $pdf, $ChineseFont, $fontStylePaiwei, $fontSizePaiwei;
		global $yPaiweiCommon, $commonStrPaiwei;

		$pdf->SetFont($ChineseFont, $fontStylePaiwei, $fontSizePaiwei);
		$pdf->Text($xPaiwei, $yPaiweiCommon, $commonStrPaiwei, false, false, true, 0, 1, 'L', false, '', 0, false, 'T', 'M', false);			

	}

	// print requester's common prefix and suffix
	function printCommonRequesterData($xReq) {		
		global $pdf, $ChineseFont, $fontStyleReq, $fontSizePrefixReq;
		global $yPrefixReq, $prefixReq, $ySuffixReq, $suffixReq;
		
		$pdf->SetFont($ChineseFont, $fontStyleReq, $fontSizePrefixReq);
		$pdf->Text($xReq, $yPrefixReq, $prefixReq, false, false, true, 0, 1, 'L', false, '', 0, false, 'T', 'M', false);
		$pdf->Text($xReq, $ySuffixReq, $suffixReq, false, false, true, 0, 1, 'L', false, '', 0, false, 'T', 'M', false);
	}


	function xLate( $what ) {
		global $sessLang;
		$htmlNames = array (			
			'C001A' => array (
				SESS_LANG_CHN => "祈福消災牌位",
				SESS_LANG_ENG => "Well Blessing name plaque" ),
			'D001A' => array (
				SESS_LANG_CHN => "地基主蓮位",
				SESS_LANG_ENG => "Site Guardians name plaque" ),
			'L001A' => array (
				SESS_LANG_CHN => "歷代祖先蓮位",
				SESS_LANG_ENG => "Ancestors name plaque" ),
			'W001A_4' => array (
				SESS_LANG_CHN => "往生者蓮位",
				SESS_LANG_ENG => "Deceased name plaque" ),
			'Y001A' => array (
				SESS_LANG_CHN => "累劫冤親債主蓮位",
				SESS_LANG_ENG => "Karmic Creditors name plaque" ),
			'DaPaiWei' => array (
				SESS_LANG_CHN => "一年內往生者大牌位",
				SESS_LANG_ENG => "Recently Deceased name plaque" )			
		);
		return $htmlNames[ $what ][ $sessLang ];
	} // function xLate();
?>