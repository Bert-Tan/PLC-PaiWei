<?php
	require_once("../pgConstants.php");
	require_once("dbSetup.php");

	/**********************************************************
 	* Generate JiWen/BaiWen and ShuWen for Retreat			  *
	* -- ThriceYearning: BaiWen and ShuWen              	  *
	* 	-- Variable names use 'Jiwen' for BaiWen          	  *
	* -- Other Retreats: JiWen and ShuWen               	  *
 	**********************************************************/
	
	//retreat date, type, reason, and anniversary year	
	$rtEvent = $_POST[ 'rtEvent' ];
	$rtrtDate = ''; $rtTemple;  $rtReason = ''; $rtVenerable = ''; $rtZhaiZhu = ''; $rtShouDu = '';

	//retreat information
	// the value of $rtName could be '清明', '中元', or 'xx週年館慶'
	$rtName = ''; $rtYear = 0; $rtMonth = 0; $rtDay = 0;
	
	//pdf configuration settings
	$pageSizeJiwen = ''; $pageSizeShuwen = '';
	$pageOrientation = 'L'; $pdfTitle = ''; $unit = 'in'; //inch
		
	//JiWen and ShuWen String
	$strJiwen = array(); $strShuwen = array();
	$strTitleJiwen = ''; $strTitleShuwen = '';  //JiWen/ShuWen title
	
	//font settings
	$ChineseFont = 'cid0csv'; $EnglishFont = 'times'; $fontStyle = 'B';
	$fontSizeJiwen = 0; $fontSizeShuwen = 0;
	$fontSizeTitleJiwen = 0; $fontSizeTitleShuwen = 0; //JiWen/ShuWen title font size
	
	//number character rotate settings
	$rotateDegree = 270;
	$rotateXadjustJiwen = 0; $rotateXadjustShuwen = 0; //X position adjustment of rotation (value related to font size)
	$textXadjustJiwen = 0; $textXadjustShuwen = 0; //X position adjustment of number text after rotation
	
	//JiWen position settings
	$xIniJiwen = 0; $xStepJiwen = 0; //X position of first line, step of X position for the next line
	$yTopJiwen = 0; //Y position of each line's beginning
	$xTitleJiwen = 0; //X position of JiWen/ShuWen title

	//ShuWen position settings
	$xIniShuwen = 0; $xStepShuwen = 0; //X position of first line, step of X position for the next line
	$yTopShuwen = 0; //Y position of each line's beginning
	$xTitleShuwen = 0; //X position of JiWen/ShuWen title
	
	//DaQing instrument symbol(image) settings
	$imgDaqing = ''; //image path
	$xAdjustDaqing = 0; $yAdjustDaqing = 0; //image X and Y position adjustment
	$imgWidthDaqing = 0; $imgHeightDaqing = 0; //image width and height
	
	//YaQing instrument symbol(image) settings
	$imgYaqing = ''; //image path
	$xAdjustYaing = 0; $yAdjustYaqing = 0; //image X and Y position adjustment
	$imgWidthYaqing = 0; $imgHeightYaqing = 0; //image width and height	



	//get data from database
	getData();
	
	// NO user selected Retreat Event
	if($rtrtDate == '') {
		switch ($rtEvent) {
			case 'RespectAncestors':
				$rtName = '祭祖';
				break;
			case 'ThriceYearning':
				$rtName = '三時繫念佛事';
				break;
		}

		echo '<script>alert("近期內沒有  '. $rtName. '  法會！")</script>';
		echo '<script>window.close();</script>';
		return;
	}

	
	//create new PDF document
	$pdf = new TCPDF($pageOrientation, $unit, 'LETTER', true, 'UTF-8', false);

	//set pdf configurations
	setConfigure();

	//set PDF document information
	$pdf->SetTitle($pdfTitle);
	$pdf->SetCreator('淨土念佛堂及圖書館');
	$pdf->SetAuthor('淨土念佛堂及圖書館');
	$pdf->SetSubject($pdfTitle);
	//remove default header/footer
	$pdf->setPrintHeader(false);
	$pdf->setPrintFooter(false);
	
	//print JiWen
	if(count($strJiwen) > 0) {
		$pdf->AddPage($pageOrientation, $pageSizeJiwen);
		$pdf->SetTextColor(0, 0, 0, 100); //balck
		//$pdf->SetTextColor(0, 255, 0); //green
		//$pdf->SetTextColor(0, 0, 255); //blue
		$pdf->SetAbsXY(0, 0, false);
	
		//print JiWen title
		$pdf->SetFont($ChineseFont, $fontStyle, $fontSizeTitleJiwen);
		$pdf->Text($xTitleJiwen, $yTopJiwen, $strTitleJiwen, false, false, true, 0, 1, 'L', false, '', 0, false, 'T', 'M', false);
	
		//print JiWen string
		$pdf->SetFont($ChineseFont, $fontStyle, $fontSizeJiwen);
		for($i=0; $i<count($strJiwen); ++$i) {
			$x = $xIniJiwen - $i*$xStepJiwen;				
		
			//print normal string (black text)
			if(is_string($strJiwen[$i]))
				$pdf->Text($x, $yTopJiwen, $strJiwen[$i], false, false, true, 0, 1, 'L', false, '', 0, false, 'T', 'M', false);
			else //print string (array) with colored text or instrument symbol
				printSpecialStr($strJiwen[$i], $x, $yTopJiwen, $fontSizeJiwen, $rotateXadjustJiwen, $textXadjustJiwen);
		}
	}

	//print ShuWen
	if(count($strShuwen) > 0) {
		$pdf->AddPage($pageOrientation, $pageSizeShuwen);
		$pdf->SetTextColor(0, 0, 0, 100); //balck
		//$pdf->SetTextColor(0, 255, 0); //green
		//$pdf->SetTextColor(0, 0, 255); //blue
		$pdf->SetAbsXY(0, 0, false);
	
		//print Shuwen title
		$pdf->SetFont($ChineseFont, $fontStyle, $fontSizeTitleShuwen);
		$pdf->Text($xTitleShuwen, $yTopShuwen, $strTitleShuwen, false, false, true, 0, 1, 'L', false, '', 0, false, 'T', 'M', false);
	
		//print Shuwen string
		$pdf->SetFont($ChineseFont, $fontStyle, $fontSizeShuwen);
		for($i=0; $i<count($strShuwen); ++$i) {
			$x = $xIniShuwen - $i*$xStepShuwen;
		
			//print normal string (black text)
			if(is_string($strShuwen[$i]))
				$pdf->Text($x, $yTopShuwen, $strShuwen[$i], false, false, true, 0, 1, 'L', false, '', 0, false, 'T', 'M', false);
			else //print string (array) with colored text or instrument symbol
				printSpecialStr($strShuwen[$i], $x, $yTopShuwen, $fontSizeShuwen, $rotateXadjustShuwen, $textXadjustShuwen);
		}
	}

	//Close and output PDF document
	$pdf->Output($pdfTitle.'.pdf', 'I');



	//print string (array) with colored text or instrument symbol
	//string format: SpedicalPrintInformation (English) + JiWen/ShuWen Text (Chinese/Number)
	function printSpecialStr($strArray, $x, $y, $fontSize, $rotateXadjust, $textXadjust) {
		global $pdf;
		global $imgDaqing, $imgYaqing;
		global $xAdjustDaqing, $yAdjustDaqing, $imgWidthDaqing, $imgHeightDaqing;
		global $xAdjustYaing, $yAdjustYaqing, $imgWidthYaqing, $imgHeightYaqing;
		
		for($i=0; $i<count($strArray); ++$i) {
			//split a string into English and Chinese/Number substrings
			//English string contains special print information: color or instrument symbol
			$regExp = '/(?<=[^\p{Latin}])(?=[\p{Latin}])|(?<=[\p{Latin}])(?=[^\p{Latin}])/u';
			//$subStrings[0]: SpedicalPrintInformation, color or instrument symbol
			//$subStrings[1]: text to be printed
			$subStrings = preg_split($regExp, $strArray[$i], -1);

			switch ($subStrings[0]) {
				case 'BLACK':
					$y = printColoredText($subStrings[1], $x, $y, $fontSize, $rotateXadjust, $textXadjust);
					break;
				case 'BLUE':
					$pdf->SetTextColor(0, 0, 255); //blue
					$y = printColoredText($subStrings[1], $x, $y, $fontSize, $rotateXadjust, $textXadjust);
					$pdf->SetTextColor(0, 0, 0, 100); //balck
					break;
				case 'GREEN':
					$pdf->SetTextColor(0, 95, 0); //green					
					$y = printColoredText($subStrings[1], $x, $y, $fontSize, $rotateXadjust, $textXadjust);
					$pdf->SetTextColor(0, 0, 0, 100); //balck
					break;
				case 'DAQING':
					$pdf->Image($imgDaqing, $x+$xAdjustDaqing, $y+$yAdjustDaqing, $imgWidthDaqing, $imgHeightDaqing, 'PNG', '', 'T', true, 300, '', false, false, 0, false, false, false);
					break;
				case 'YAQING':
					$pdf->Image($imgYaqing, $x+$xAdjustYaing, $y+$yAdjustYaqing, $imgWidthYaqing, $imgHeightYaqing, 'PNG', '', 'T', true, 300, '', false, false, 0, false, false, false);
					break;
			}
		}		
	}
	
	//print colored text
	function printColoredText($str, $x, $y, $fontSize, $rotateXadjust, $textXadjust) {
		global $pdf, $ChineseFont, $EnglishFont, $fontStyle, $rotateDegree;
				
		preg_match('/[0-9]+/u', $str, $matches); //numbers
		if(count($matches) > 0) { //Number string
			$pdf->SetFont($EnglishFont, $fontStyle, $fontSize);
			
			//Rotate Number string
			$pdf->StartTransform();				
			$pdf->Rotate($rotateDegree, $x+$rotateXadjust, $y);
			$pdf->Text($x+$textXadjust, $y, $str, false, false, true, 0, 1, 'L', false, '', 0, false, 'T', 'M', false);
			$pdf->StopTransform();
			
			$width = $pdf->GetStringWidth($str, $EnglishFont, $fontStyle, $fontSize, false);
			
			$pdf->SetFont($ChineseFont, $fontStyle, $fontSize);
		}
		else { //Chinese string
			$pdf->Text($x, $y, $str, false, false, true, 0, 1, 'L', false, '', 0, false, 'T', 'M', false);
			$width = $pdf->GetStringWidth($str, $ChineseFont, $fontStyle, $fontSize, false);
		}
		
		return $y + $width;
	}
	
	
	


	//set pdf configuratons
	function setConfigure() {		
		global $pdf, $EnglishFont, $fontStyle, $pageSizeJiwen, $pageSizeShuwen;		
		global $rtEvent, $rtName, $pdfTitle;
		global $strTitleJiwen, $strTitleShuwen;
		global $fontSizeJiwen, $fontSizeShuwen;
		global $fontSizeTitleJiwen, $fontSizeTitleShuwen;
		global $rotateXadjustJiwen, $textXadjustJiwen;
		global $rotateXadjustShuwen, $textXadjustShuwen;
		global $xIniJiwen, $xStepJiwen, $yTopJiwen;
		global $xIniShuwen, $xStepShuwen, $yTopShuwen;
		global $xTitleJiwen, $xTitleShuwen;
		global $imgDaqing, $imgYaqing;
		global $xAdjustDaqing, $yAdjustDaqing, $imgWidthDaqing, $imgHeightDaqing;
		global $xAdjustYaing, $yAdjustYaqing, $imgWidthYaqing, $imgHeightYaqing;
		
		$year = date("Y");
		switch ($rtEvent) {
			case 'RespectAncestors':
				$pdfTitle=$year.$rtName.'祭文疏文';
				$strTitleJiwen='   祭  文';
				$strTitleShuwen='   祭 祖 追 薦 疏 文';
				$pageSizeJiwen = 'LETTER'; $pageSizeShuwen = 'LETTER';
				$fontSizeJiwen=24; $fontSizeShuwen=19;
				$fontSizeTitleJiwen=28; $fontSizeTitleShuwen = 22;
				$rotateXadjustJiwen=1.0*$pdf->GetStringWidth('G', $EnglishFont, $fontStyle, $fontSizeJiwen, false);
				$textXadjustJiwen=2.2*$pdf->GetStringWidth('G', $EnglishFont, $fontStyle, $fontSizeJiwen, false);
				$rotateXadjustShuwen=1.05*$pdf->GetStringWidth('G', $EnglishFont, $fontStyle, $fontSizeShuwen, false);
				$textXadjustShuwen=2.2*$pdf->GetStringWidth('G', $EnglishFont, $fontStyle, $fontSizeShuwen, false);
				$xIniJiwen=9; $xStepJiwen=0.5; $yTopJiwen=0.5;
				$xIniShuwen=10.1; $xStepShuwen=0.375; $yTopShuwen=0.45;
				$xTitleJiwen=9.7; $xTitleShuwen=10.55;			
				break;
			case 'ThriceYearning':
				$pdfTitle=$year.'三時繫念白文疏文';
				$strTitleJiwen='  三時繫念法會白文';
				$strTitleShuwen='      一誠上達';
				$pageSizeJiwen = 'LETTER'; $pageSizeShuwen = 'LEGAL';
				$fontSizeJiwen=24; $fontSizeTitleJiwen=28;
				$fontSizeShuwen=16; $fontSizeTitleShuwen = 20;
				$rotateXadjustJiwen=1.05*$pdf->GetStringWidth('G', $EnglishFont, $fontStyle, $fontSizeShuwen, false);
				$textXadjustJiwen=2.2*$pdf->GetStringWidth('G', $EnglishFont, $fontStyle, $fontSizeShuwen, false);
				$rotateXadjustShuwen=1.1*$pdf->GetStringWidth('G', $EnglishFont, $fontStyle, $fontSizeShuwen, false);
				$textXadjustShuwen=2.3*$pdf->GetStringWidth('G', $EnglishFont, $fontStyle, $fontSizeShuwen, false);				
				$xIniJiwen=8.5; $xStepJiwen=0.6; $yTopJiwen=0.5; $xTitleJiwen=9.5;
				$xIniShuwen=13.1; $xStepShuwen=0.3; $yTopShuwen=0.15; $xTitleShuwen=13.4;
				$imgDaqing='img/DaQing.png'; $imgYaqing='img/YaQing.png';
				$xAdjustDaqing=0.13; $yAdjustDaqing=0.06; $imgWidthDaqing=0.13; $imgHeightDaqing=0.13;
				$xAdjustYaing=0.07; $yAdjustYaqing=0.15; $imgWidthYaqing=0.18; $imgHeightYaqing=0.24;		
				break;
		}
	}
	
	//search database to get data
	function getData() {	
		global $_db;
		global $rtrtDate, $rtEvent, $rtTemple, $rtReason, $rtVenerable, $rtZhaiZhu, $rtShouDu;
		global $rtYear, $rtMonth, $rtDay, $rtName;

		$_sql = "SELECT `rtrtDate`, `rtTemple`, `rtReason`, `rtVenerable`, `rtZhaiZhu`, `rtShouDu` FROM `pwParam` "
				. "WHERE `rtEvent` = \"{$rtEvent}\";";

		$_db->query("LOCK TABLES `pwParam` READ;");
		$_rslt = $_db->query( $_sql );
		$_db->query("UNLOCK TABLES;");
		$_db->close();

		// NO user selected Retreat Event
		if($_rslt->num_rows == 0) return;

		$_rtData = $_rslt->fetch_all(MYSQLI_ASSOC)[0];
		//set retreat date, temple, reason, venerable, ZhaiZhu, and ShouDu
		$rtrtDate = $_rtData [ 'rtrtDate' ];
		$rtTemple = $_rtData [ 'rtTemple' ];
		$rtReason = $_rtData [ 'rtReason' ];
		$rtVenerable = $_rtData [ 'rtVenerable' ];
		$rtZhaiZhu = $_rtData [ 'rtZhaiZhu' ];
		$rtShouDu = $_rtData [ 'rtShouDu' ];
		
		// get $rtName by removing '祭祖' and/or '淨土念佛堂及圖書館' from $rtReason
		// the value of $rtName could be '清明', '中元', or 'xx週年館慶'
		$rtName = preg_replace('(祭祖|淨土念佛堂及圖書館)', '', $rtReason);

		// set retreat year, month, and day
		$d = explode( "-", $rtrtDate );
		$rtYear = intval($d[0]);
		$rtMonth = intval($d[1]);
		$rtDay = intval($d[2]);

		// get JiWen/ShuWen content
		switch ($rtEvent) {
			case 'RespectAncestors':		
				setJizuJiwen(); setJizuShuwen();		
				break;
			case 'ThriceYearning':			
				setXinianBaiwen(); setXinianShuwen();		
				break;		
		}			
	}

	//set JiWen string of 祭祖 retreat
	//text with special print information (colored text or instrument symbol) are stroed in array
	//special string format: SpedicalPrintInformation (English) + JiWen/ShuWen Text (Chinese/Number)
	function setJizuJiwen() {		
		global $rtName, $rtTemple, $rtYear, $rtMonth, $rtDay, $strJiwen;

		array_push($strJiwen, '  維');
		
		$str = array();
		array_push($str, 'BLACK西元');
		array_push($str, 'BLUE '.$rtYear.' ');
		array_push($str, 'BLACK年');
		array_push($str, 'BLUE '.$rtMonth.' ');
		array_push($str, 'BLACK月');
		array_push($str, 'BLUE '.$rtDay.' ');
		array_push($str, 'BLACK日 ');
		array_push($str, 'BLUE'.$rtTemple);
		array_push($strJiwen, $str);	
		
		$str = array();
		array_push($str, 'BLUE'.$rtName.'  ');	
		array_push($str, 'BLACK祭祖之日');
		array_push($strJiwen, $str);

		$str = array();
		array_push($str, 'BLUE'.$rtTemple.' ');
		array_push($str, 'BLACK執事、義工及同修等 ');	
		array_push($strJiwen, $str);	
		
		array_push($strJiwen, '四眾弟子 謹以香花蔬果  致祭於');	
		array_push($strJiwen, '先亡尊親眷屬之靈 曰');	
		array_push($strJiwen, '人生幻化 世緣無常  昔為親眷 今別存亡');	
		array_push($strJiwen, '音容在目 懿行難忘  恩德未絕 思之心傷');	
		array_push($strJiwen, '念諸眷親 各有宿因  緣業受報 聖劣攸分');	
		array_push($strJiwen, '或修佛法 念一功純  蒙佛接引 諸善等倫');	
		array_push($strJiwen, '或樂善施 敦品勵身  德積福厚 報生天人');	
		array_push($strJiwen, '亦有癡迷 執著貪瞋  惑深業重 未脫苦津');	
		array_push($strJiwen, '茲開法會 蓮眾同臻  誦經念佛 薦拔是申');	
		array_push($strJiwen, '靈兮有知 應忻應聞  仗佛法力 永免沈淪');	
		array_push($strJiwen, '爐香靄靄 清茗盈樽  鮮花郁郁 青果敷陳');	
		array_push($strJiwen, '掬誠奉奠 來格來歆');	
		array_push($strJiwen, '尚饗');	
	}
	
	//set ShuWen string of 祭祖 retreat
	//text with special print information (colored text or instrument symbol) are stroed in array
	//special string format: SpedicalPrintInformation (English) + JiWen/ShuWen Text (Chinese/Number)
	function setJizuShuwen() {
		global $rtName, $rtTemple, $rtVenerable, $rtYear, $rtMonth, $rtDay, $strShuwen;
				
		array_push($strShuwen, '  一 誠 上 達');
		array_push($strShuwen, '爰有娑婆世界南瞻部洲 美國 伊利諾州 瑞柏市');

		$str = array();
		array_push($str, 'BLUE'.$rtTemple.'  ');
		if ( $rtVenerable != '' ) {
			array_push($str, 'BLACK主修法事沙門  ');
			array_push($str, 'BLUE'.$rtVenerable.' ');
		}
		else {
			array_push($str, 'BLACK執事 義工及同修等 四眾弟子');
		}
		array_push($strShuwen, $str);

		if ( $rtVenerable != '' ) {
			array_push($strShuwen, '暨 執事 義工及同修等 四眾弟子');
		}

		array_push($strShuwen, '  合詞一心 至誠頂禮拜疏');
		array_push($strShuwen, '本師釋迦牟尼佛');
		array_push($strShuwen, '極樂世界阿彌陀佛');
		array_push($strShuwen, '觀世音菩薩');
		array_push($strShuwen, '大勢至菩薩');
		array_push($strShuwen, '清淨大海眾菩薩');
		array_push($strShuwen, '十方常住三寶');
		array_push($strShuwen, '伽藍聖眾');
		array_push($strShuwen, '護法神祇尊前');
		array_push($strShuwen, '竊維大法垂世 十方咸被莊嚴');
		array_push($strShuwen, '慈光涵虛 九有俱蒙攝受 會看濁惡穢土 遍開寶蓮');
		array_push($strShuwen, '能教昏迷眾生 皆覩明炬 弟子眾等 無常幻相');
		array_push($strShuwen, '聞法幸修 罔極之恩 應思追報');
		
		$str = array();	
		array_push($str, 'BLACK茲值本念佛堂  ');
		array_push($str, 'BLUE'.$rtName.'  ');
		array_push($str, 'BLACK祭祖之日');
		if ( $rtVenerable != '' ) {
			array_push($str, 'BLACK 淨掃會徑 廣邀學人');
		}		
		array_push($strShuwen, $str);

		if ( $rtVenerable == '' ) {
		array_push($strShuwen, '淨掃會徑 廣邀學人');
		}
		
		array_push($strShuwen, '恭諷三藏之靈文 虔修百味之供養');
		array_push($strShuwen, '各為過去父母 求超蓮邦 並祈當前眷親 同滅罪障');
		array_push($strShuwen, '八德池上 芳名一例高標 二課誦中 實相全由密證');
		array_push($strShuwen, '久賴智悲護念 定鑒此心 全將性命皈依 必成斯願');
		array_push($strShuwen, '      謹此拜疏 伏乞');
		array_push($strShuwen, '垂恩攝受    時維');

		$str = array();
		array_push($str, 'BLACK公元');
		array_push($str, 'BLUE   '.$rtYear.'   ');
		array_push($str, 'BLACK年');
		array_push($str, 'BLUE   '.$rtMonth.'   ');
		array_push($str, 'BLACK月');
		array_push($str, 'BLUE   '.$rtDay.'   ');
		array_push($str, 'BLACK日   ');
		if ( $rtVenerable != '' ) {
			array_push($str, 'BLACK主修法事沙門  ');
			array_push($str, 'BLUE'.$rtVenerable.' ');
		}
		array_push($strShuwen, $str);

		$str = array();
		array_push($str, 'BLACK修齋弟子 ');
		array_push($str, 'BLUE'.$rtTemple.'  ');
		array_push($str, 'BLACK執事 義工及同修等');
		array_push($strShuwen, $str);

		array_push($strShuwen, '四眾弟子   上叩');
	}

	//set BaiWen string of 三時繫念 retreat
	//text with special print information (colored text or instrument symbol) are stroed in array
	//special string format: SpedicalPrintInformation (English) + JiWen/ShuWen Text (Chinese/Number)
	function setXinianBaiwen() {	
		global $rtName, $rtTemple, $rtZhaiZhu, $rtShouDu, $strJiwen;
		
		array_push($strJiwen, '法王利物。悲智洪深。普徧十方。冥陽靡隔。');
		
		$str = array();
		array_push($str, 'BLACK今蒙齋主 ');
		$subStrArray = explode(' ', $rtZhaiZhu);
		foreach($subStrArray as $subStr) {
			array_push($str, 'BLUE'.$subStr.' ');
		}
		array_push($strJiwen, $str);	

		$str = array();
		array_push($str, 'BLACK恭爲 ');
		array_push($str, 'BLUE'.$rtShouDu);
		array_push($strJiwen, $str);
		
		$str = array();		
		array_push($str, 'BLACK屆逢 ');
		array_push($str, 'BLUE'.$rtTemple.' ');
		array_push($str, 'BLUE'.$rtName.' ');
		array_push($str, 'BLACK之期。');
		array_push($strJiwen, $str);	
		
		array_push($strJiwen, '特請山僧登座。依憑教法。作三時繫念佛事。');	
		array_push($strJiwen, '迺爾神靈。遭此勝緣。自宜嚴肅威儀。');	
		array_push($strJiwen, '來臨座下。恭聆妙法。一心受度。');
	}
	
	//set ShuWen string of 三時繫念 retreat
	//text with special print information (colored text or instrument symbol) are stroed in array
	//special string format: SpedicalPrintInformation (English) + JiWen/ShuWen Text (Chinese/Number)
	function setXinianShuwen() {
		global $rtName, $rtTemple, $rtVenerable, $rtYear, $rtMonth, $rtDay, $strShuwen;
		
		$str = array();
		array_push($str, 'YAQING ');
		array_push($str, 'GREEN大圓滿覺   應跡西乾   心包太虛                           ');
		array_push($str, 'BLUE量週沙');
		array_push($str, 'DAQING ');
		array_push($str, 'BLUE界');
		array_push($strShuwen, $str);
		
		$str = array();
		array_push($str, 'BLACK        ');
		array_push($str, 'YAQING ');
		array_push($str, 'BLACK上來今有啟建超荐道場    所有疏文對');
		array_push($strShuwen, $str);
		
		$str = array();
		array_push($str, 'BLACK佛恭讀       ');
		array_push($str, 'GREEN大圓鏡中    ');
		array_push($str, 'BLUE俯垂朗');
		array_push($str, 'DAQING ');
		array_push($str, 'BLUE鑒');
		array_push($strShuwen, $str);
		
		$str = array();
		array_push($str, 'BLACK    ');
		array_push($str, 'YAQING ');
		array_push($str, 'BLACK爰有 一泗天下 南瞻部洲     ');
		array_push($str, 'BLACK美國伊利諾州瑞柏市東歐登街一一二〇號一〇八室');
		array_push($strShuwen, $str);

		$str = array();
		array_push($str, 'BLUE'.$rtTemple.' 及四眾弟子     ');
		array_push($str, 'BLACK秉');
		array_push($strShuwen, $str);
		
		$str = array();
		array_push($str, 'BLACK釋迦如來遺教奉行主修功德佛事沙門 ');
		array_push($str, 'BLACK'.$rtVenerable.' ');
		array_push($str, 'BLACK今據  ');
		array_push($str, 'BLUE'.$rtTemple.'  ');
		array_push($str, 'BLACK執事、');
		array_push($strShuwen, $str);
		
		array_push($strShuwen, '義工及同修等');
		array_push($strShuwen, '       中華民族萬姓祖先');
		array_push($strShuwen, '       美利堅合眾國各民族百姓祖先');
		array_push($strShuwen, '       往生堂上眾等神靈');
		
		$str = array();
		array_push($str, 'BLACK奉佛修齋  ');
		array_push($str, 'BLACK敬為  ');
		array_push($str, 'BLACK美國及世界各地天災人禍罹難眾生');
		array_push($strShuwen, $str);
		
		array_push($strShuwen, '       十方法界一切無祀孤魂');
		array_push($strShuwen, '       法界苦難眾生 一切蜎飛蠕動之類');
		array_push($strShuwen, '       '.$rtTemple.'執事、義工及同修之累劫冤親債主');
		array_push($strShuwen, '  啟建三時繫念佛事');
		
		$str = array();
		array_push($str, 'BLACK  陽上         ');
		array_push($str, 'BLUE'.$rtTemple.'   ');
		array_push($str, 'BLACK執事、義工及同修領善眷人等 四眾弟子');
		array_push($strShuwen, $str);
		
		array_push($strShuwen, '    是日沐手焚香志心皈叩');
		array_push($strShuwen, '中天調御釋迦文佛 西方接引彌陀如來');
		
		$str = array();
		array_push($str, 'BLACK觀音勢至地藏菩薩    ');
		array_push($str, 'BLUE各寶金蓮座');
		array_push($str, 'DAQING ');
		array_push($str, 'BLUE下    ');
		array_push($str, 'YAQING ');
		array_push($str, 'BLACK具情伏為');
		array_push($strShuwen, $str);
		
		array_push($strShuwen, '       中華民族萬姓祖先');
		array_push($strShuwen, '       往生堂上眾等神靈');
		array_push($strShuwen, '       美國及世界各地天災人禍罹難眾生');
		array_push($strShuwen, '       十方法界一切無祀孤魂');
		array_push($strShuwen, '       法界苦難眾生 一切蜎飛蠕動之類');
		array_push($strShuwen, '       '.$rtTemple.'執事、義工及同修之累劫冤親債主');
		
		array_push($strShuwen, '  切念去世以來生方未卜 慨泉路之茫茫 唯寶筏旋登彼岸');
		array_push($strShuwen, '             嘆夜臺之漠漠 超荐功德 即渡慈航');
		
		$str = array();
		array_push($str, 'BLACK茲屆');
		array_push($str, 'BLUE '.$rtTemple);
		array_push($str, 'BLACK '.$rtName);
		array_push($str, 'BLACK  之期');
		array_push($strShuwen, $str);
		
		array_push($strShuwen, '延請法師 數位 啟建超荐道場一永日');
		array_push($strShuwen, '功德於中  加持讀誦經文、往生神咒  奉修清淨香齋、禪悅酥酡  上供');
		
		$str = array();
		array_push($str, 'BLACK十方三寶');
		array_push($str, 'GREEN    剎海龍天   俯降法筵                           ');
		array_push($str, 'BLUE慈悲納');
		array_push($str, 'DAQING ');
		array_push($str, 'BLUE受');		
		array_push($strShuwen, $str);
		
		$str = array();
		array_push($str, 'YAQING ');
		array_push($str, 'BLACK如上功德 耑為回向');
		array_push($strShuwen, $str);
		
		array_push($strShuwen, '     中華民族萬姓祖先');
		array_push($strShuwen, '     往生堂上眾等神靈');
		array_push($strShuwen, '     美國及世界各地天災人禍罹難眾生');
		array_push($strShuwen, '     十方法界一切無祀孤魂');
		array_push($strShuwen, '     法界苦難眾生 一切蜎飛蠕動之類');
		array_push($strShuwen, '     '.$rtTemple.'執事、義工及同修之累劫冤親債主');
		
		$str = array();
		array_push($str, 'BLACK  仗      ');
		array_push($str, 'BLACK此 ');	
		array_push($str, 'BLACK良 ');
		array_push($str, 'BLACK因 ');	
		array_push($str, 'BLACK早 ');	
		array_push($str, 'BLACK生 ');	
		array_push($str, 'BLACK淨 ');	
		array_push($str, 'BLACK土');	
		array_push($str, 'BLACK  伏願');			
		array_push($strShuwen, $str);
		
		$str = array();
		array_push($str, 'BLACK  遠      ');
		array_push($str, 'BLACK近 ');	
		array_push($str, 'BLACK宗 ');
		array_push($str, 'BLACK親 ');	
		array_push($str, 'BLACK欣 ');	
		array_push($str, 'BLACK共 ');	
		array_push($str, 'BLACK度 ');	
		array_push($str, 'BLACK昭');	
		array_push($str, 'BLACK 穆    ');	
		array_push($str, 'BLACK至 ');	
		array_push($str, 'BLACK戚 ');	
		array_push($str, 'BLACK慶 ');
		array_push($str, 'BLACK同 ');	
		array_push($str, 'BLACK春');	
		array_push($str, 'BLACK   恭祈');	
		array_push($strShuwen, $str);
		
		$str = array();
		array_push($str, 'BLACK  ');
		array_push($str, 'BLACK三寶證明超荐文疏  時維');		
		array_push($strShuwen, $str);
		
		$str = array();
		array_push($str, 'BLACK  ');
		array_push($str, 'BLACK公元');
		array_push($str, 'BLUE  '.$rtYear.'  ');
		array_push($str, 'BLACK年');
		array_push($str, 'BLUE  '.$rtMonth.'  ');
		array_push($str, 'BLACK月');
		array_push($str, 'BLUE  '.$rtDay.'  ');
		array_push($str, 'BLACK日  主修佛事沙門 '.$rtVenerable);		
		array_push($strShuwen, $str);
		
		$str = array();
		array_push($str, 'BLACK修齋弟子  ');
		array_push($str, 'BLUE'.$rtTemple.'  ');
		array_push($str, 'BLACK執事、義工及同修等  ');
		array_push($str, 'BLACK四眾弟子    ');
		array_push($str, 'BLUE百拜具');
		array_push($str, 'DAQING ');
		array_push($str, 'BLUE陳');
		array_push($strShuwen, $str);
	}
?>