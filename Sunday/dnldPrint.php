<?php
	require_once("../pgConstants.php");
	require_once("dbSetup.php");
	require_once("sunday_DBfuncs.php");
	require_once("plcMailerSetup.php");

	//pdf file path and name
	$pdfPath = DOCU_ROOT. "/QifuReqReport/";
	$pdfTitle = "QifuMeritReq-" . date('Y-m-d');
	$pdfName = $pdfTitle . '.pdf';
		
	//pdf configuration settings
	$pageSize = 'LETTER'; $unit = 'in'; //inch
	$pageOrientation = 'L';
	$topMargin = 0.5; $bottomMargin = 0.5;
	$leftMargin = 0.5; $rightMargin = 0.5;
	
	//font settings
	$ChineseFont = 'edukai3'; $EnglishFont = 'courierI'; //$SymbolFont = 'zapfdingbats';
	$fontStyle = 'B'; $fontSize = 18; $fontSizeDate = 18; //$fontSizeDate = 14;
	//$correctMark = TCPDF_FONTS::unichr(52);
		
	//some common information (string)
	$title = '淨土念佛堂、圖書館';
	$qifuTableTitle = '祈  福  申  請  表';
	$meritTableTitle = '回  向  申  請  表';
	$qifuHeaderData = array("申請人\n姓名", "受祈福\n者姓名", "與申請\n人關係", "受祈福人的狀況\n（申請理由）");
	$meritHeaderData = array("申請人\n姓名", "往生者\n姓名", "與申請\n人關係", "往生者年齡", "往生日期", "往生地點", "回向內容");

	//table cell column width
	$qifuCellWidthArray = array(1.5, 1.5, 1.5, 5.5);
	$meritCellWidthArray = array(1.1, 1.1, 1.1, 0.9, 1.3, 1.1, 3.4);

	$totalWidth = 11 - $leftMargin - $rightMargin; //total table width
	$totalHeight = 8.5 - $topMargin - $bottomMargin; //total page height
	$extraHeight = 0.2; // extra height for each table row
	
	//the number of empty rows to be printed for Qifu/Merit table
	$emptyRowNum = 2; $emptyCellHeight = 0.65;
		
	//Qifu/Merit data array
	$qifuDataArray = array();
	$meritDataArray = array();
	
	//Qifu/Merit table cell height array
	$qifuCellHeightArray = array();
	$meritCellHeightArray = array();
	//Qifu/Merit table header height
	$qifuHeaderHeight = 0;
	$meritHeaderHeight = 0;
	
	
	
	
	//create new PDF document
	$pdf = new TCPDF($pageOrientation, $unit, $pageSize, true, 'UTF-8', false);

	//set PDF document information
	$pdf->SetTitle($pdfTitle);
	$pdf->SetCreator($title);
	$pdf->SetAuthor($title);
	$pdf->SetSubject($pdfTitle);
	//remove default header/footer
	$pdf->setPrintHeader(false);
	$pdf->setPrintFooter(false);
	//set margins
	$pdf->SetMargins($leftMargin, $topMargin, $rightMargin, true);
	//set auto page breaks
	$pdf->SetAutoPageBreak(true, $bottomMargin);	
	//set font
	$pdf->SetFont($ChineseFont, $fontStyle, $fontSize);
	// set color for background
	$pdf->SetFillColor(220, 220, 220);
	
	//get data
	getData(); 

	// no request for this coming Sunday
	if ($qifuDataArray == null && $meritDataArray == null) {
		$pdf->AddPage(); //add a page
		//print no Qifu/Merit request msg
		$pdf->Cell($totalWidth, 0, '本週日（'.date('Y-m-d').'）沒有祈福回向申請', 0, 0, 'L', false);
	}
	if ($qifuDataArray != null) {
		//print Sunday Qifu data
		printData($qifuDataArray, $qifuHeaderData, $qifuCellHeightArray, $qifuHeaderHeight, $qifuCellWidthArray, $qifuTableTitle);
	}
	if ($meritDataArray != null) {
		//print Sunday Merit data
		printData($meritDataArray, $meritHeaderData, $meritCellHeightArray, $meritHeaderHeight, $meritCellWidthArray, $meritTableTitle);
	}	
	
	//Close and output PDF document
	if( isset($_GET[ 'view' ]) && $_GET[ 'view' ]=='true' ) { //view in web browser
		$pdf->Output($pdfName, 'I');
	}
	else { //send to PLC printer
		$pdf->Output($pdfPath . $pdfName, 'F');
		
		// no request for this coming Sunday
		if ($qifuDataArray == null && $meritDataArray == null) {
			$msg = "There is NO Qifu/Merit request for this Sunday (" . date('Y-m-d'). ").";
			$attachments = null;
		}
		else {
			$msg = "";
			$attachments = array (
				array (
					'path' => $pdfPath . $pdfName,
					'name' =>  $pdfName
				)
			);
		}

		$libraryTo = array (
			array (				
				'email' => 'library@amitabhalibrary.org',
				'name' => 'Pure Land Center'
			)
		);	
		$subject = $pdfTitle;		

		//send email
		plcSendEmailAttachment( $libraryTo, null, $subject, $msg, null, $attachments, true);
	}
	

	
	
	//print data
	function printData($dataArray, $headerData, $cellHeightArray, $headerHeight, $cellWidthArray, $tableTitle) {
		global $pdf, $title, $totalWidth;
		global $ChineseFont, $fontStyle, $fontSize;
		
		$pageNum = count($dataArray); //number of PDF pages		
		
		for($i = 0; $i < $pageNum; ++$i) {	
			
			$pdf->AddPage(); //add a page
			
			/* print page title */	
			//print Pure Land Center title	
			$pdf->Cell($totalWidth, 0, $title, 0, 0, 'C', false);
			$pdf->Ln(); //new line
			//print report date
			$pdf->Cell($totalWidth, 0, '列印日期：'.date('Y').' 年 '.date('m').' 月 '.date('d').' 日', 0, 0, 'C', false);
			$pdf->Ln(); $pdf->Ln(); //new lines
	
			/* print data table */	
			//print table title
			$pdf->Cell($totalWidth, 0, $tableTitle, 1, 0, 'C', true);
			$pdf->Ln(); //new line
			//print data table
			printTable($dataArray[$i], $headerData, $cellHeightArray[$i], $headerHeight, $cellWidthArray);
		}
	}
	
	
	//print data table
	function printTable($dataArray, $headerData, $cellHeightArray, $headerHeight, $cellWidthArray) {
		global $pdf;
		
		$dataNum = count($dataArray); //number of data records (rows)
		
		//print headers
		printRow($headerData, $headerHeight, $cellWidthArray, true);		
		
		//print data rows
		for($i = 0; $i < $dataNum; ++$i) {
			printRow($dataArray[$i], $cellHeightArray[$i], $cellWidthArray, false);
		}
	}
	
	//print a request data row
	//if $isHeader=true, print header row (special format); otherwise, print request data row
	function printRow($data, $cellHeight, $cellWidthArray, $isHeader) {
		global $pdf;
		global $ChineseFont, $EnglishFont, $fontSize, $fontStyle, $fontSizeDate;
			
		//field number of each request data record
		$colNum = count($data);	

		$fill = false;
		//table header: with background color
		if($isHeader) {
			$fill=true;
		}
		
		//print row
		for($i = 0; $i < $colNum; ++$i) {			
			/*
			//request dates (data cell): smaller font & left alignment & highlight CURRENT Sunday date
			if(!$isHeader && $i == $colNum-1) {
				$pdf->SetFont($ChineseFont, $fontStyle, $fontSizeDate);
				//$rqDate: 'Y-m-d'
				$rqDate = getCurrentNextSundayDate();
				//$rqDateShort: 'm-d'
				$rqDateShort = date('m-d', strtotime($rqDate));
				//CURRENT Sunday date: highlight with underline
				$data[$i] = str_replace($rqDate, '<b><u>'.$rqDate.'</u></b>', $data[$i]);
				$data[$i] = str_replace($rqDateShort, '<b><u>'.$rqDateShort.'</u></b>', $data[$i]);
				
				$x = $pdf->GetX();
				$y = $pdf->GetY();
				//calculate the Y position of the request date string
				//vertical alignment MIDDLE for $pdf->writeHTMLCell()
				$newY = $y + ($cellHeight - $dateStrHeight) / 2;
				
				//print cell borders
				$pdf->MultiCell($cellWidthArray[$i], $cellHeight, '', 1, 'C', $fill, 0, '', '', true, 0, false, true, $cellHeight, 'M');
				//print date string, no borders	
				$pdf->writeHTMLCell($cellWidthArray[$i], $cellHeight-($newY-$y), $x, $newY, $data[$i], 0, 0, false, true, 'L', true);
				
				//reset the Y position for the next cell in the same row
				$pdf->SetY($y, false);
				//reset font (not use fontSizeDate for other cells)
				$pdf->SetFont($ChineseFont, $fontStyle, $fontSize);
			}
			*/
			/*
			//GongDeZhu cell: if true, print correct mark
			if(!$isHeader && $i == $colNum-1) {	
				$pdf->SetFont($EnglishFont, $fontStyle, $fontSize);
				if($data[$i] != 0)
					$pdf->MultiCell($cellWidthArray[$i], $cellHeight, $data[$i], 1, 'C', $fill, 0, '', '', true, 0, false, true, $cellHeight, 'M');
				else
					$pdf->MultiCell($cellWidthArray[$i], $cellHeight, "", 1, 'C', $fill, 0, '', '', true, 0, false, true, $cellHeight, 'M');
					
				//$gongDeZhuMark = '';
				//if($data[$i] == 1)
				//	$gongDeZhuMark = $correctMark;
				//print GongDeZhu mark
				//$pdf->SetFont($SymbolFont, $fontStyle, $fontSize);
				//$pdf->MultiCell($cellWidthArray[$i], $cellHeight, $gongDeZhuMark, 1, 'C', $fill, 0, '', '', true, 0, false, true, $cellHeight, 'M');

				//reset font
				$pdf->SetFont($ChineseFont, $fontStyle, $fontSize);
			}
			*/
			//other data cells
			//else {
				$pdf->MultiCell($cellWidthArray[$i], $cellHeight, $data[$i], 1, 'C', $fill, 0, '', '', true, 0, false, true, $cellHeight, 'M');
			//}	
        }
		
		$pdf->Ln(); //new line
		
		//reset PDF format configures
		$pdf->SetFont($ChineseFont, $fontStyle, $fontSize);
	}		
	
	
	
	//get Sunday Qifu/Merit data
	function getData() {	
		global $_db;
		global $qifuDataArray, $meritDataArray;	
		global $qifuHeaderData, $meritHeaderData;
		global $qifuCellHeightArray, $meritCellHeightArray;
		global $qifuHeaderHeight, $meritHeaderHeight;
		global $qifuCellWidthArray, $meritCellWidthArray;
			
		$rqDate = getCurrentNextSundayDate();
		
		//get Sunday Qifu data
		$sundayTable = 'sundayQifu';	
		$qifuDataArray = queryData($sundayTable, $rqDate);
		
		//get Sunday Merit data
		$sundayTable = 'sundayMerit';	
		$meritDataArray = queryData($sundayTable, $rqDate);

		/*
		//GongDeZhu array ordered by request timestamp
		$gondDeZhuOrderArray = queryGongDeZhuOrder($rqDate);
		
		//assign GongDeZhu order to Qifu/Merit data array based on request timestamp (i.e. order in $gondDeZhuOrderArray)
		assignGongDeZhuOrder($qifuDataArray, $gondDeZhuOrderArray, 'sundayQifu');
		assignGongDeZhuOrder($meritDataArray, $gondDeZhuOrderArray, 'sundayMerit');
		*/
				
		//pre-process data
		if ($qifuDataArray != null) {
			preprocessData($qifuDataArray, $qifuHeaderData, $qifuCellHeightArray, $qifuHeaderHeight, $qifuCellWidthArray);
		}
		if ($meritDataArray != null) {
			preprocessData($meritDataArray, $meritHeaderData, $meritCellHeightArray, $meritHeaderHeight, $meritCellWidthArray);
		}
	}
	
	//data pre-process:
	//  (1) calculate PDF page title height
	//  (2) calculate table header height
	//  (3) calculate table_cell_height for each data record
	//  (4) add empty data rows (reserved for handwriting request) to data_array and height_array
	//  (5) partition request data into multiple sub-arrays, each sub-array is printed on one page
	function preprocessData(&$dataArray, $headerData, &$cellHeightArray, &$headerHeight, $cellWidthArray) {	
		global $pdf, $extraHeight, $emptyRowNum, $totalHeight, $emptyCellHeight;

		//field number of each request data record
		$colNum = count($headerData);
						
		//(1) calculate PDF page title height
		$pdfPageTitleHeight = 4 * $pdf->getStringHeight($totalHeight, "  ", false, true, '', 1);
				
		//(2) calculate table header height
		$headerHeight = calculateCellHeight($headerData, $cellWidthArray, false);
						
		//(3) calculate table_cell_height for each data record
		for($i = 0; $i < count($dataArray); ++$i) {
			
			$data = $dataArray[$i]; //data record			
			
			$cellHeight = calculateCellHeight($data, $cellWidthArray, true);

			array_push($cellHeightArray, $cellHeight);
		}
				
		//(4) add empty data rows (reserved for handwriting request) to data_array and height_array		
		for($i = 0; $i < $emptyRowNum; ++$i) {
			//add data
			$data = array();
			for($j = 0; $j < $colNum; ++$j) {
				array_push($data, "");
			}
			array_push($dataArray, $data);
			
			//add height (use header height)
			array_push($cellHeightArray, $emptyCellHeight);
		}

		//(5) partition request data into multiple sub-arrays, each sub-array is printed on one page
		$partitionedDataArray = array(); //request data after partition
		$partitionedCellHeightArray = array(); //cell heights of request data after partition				
		$temDataArray = array(); //temporary partitioned data (one sub-array)
		$temCellHeightArray = array(); //cell heights of temporary partitioned data (one sub-array)
		$temHeight = $pdfPageTitleHeight + $headerHeight; //height of temporary data

		for($i = 0; $i < count($dataArray); ++$i) {
			//if the current page is full
			if($temHeight+$cellHeightArray[$i]>$totalHeight) {
				array_push($partitionedDataArray, $temDataArray);
				array_push($partitionedCellHeightArray, $temCellHeightArray);
		
				$temDataArray = array();
				$temCellHeightArray = array();
				$temHeight = $pdfPageTitleHeight + $headerHeight;				
			}

			//partition data to the temporary array
			$temHeight = $temHeight + $cellHeightArray[$i];
			array_push($temDataArray, $dataArray[$i]);
			array_push($temCellHeightArray, $cellHeightArray[$i]);
			
			//the last data record
			if($i == count($dataArray)-1) {
				array_push($partitionedDataArray, $temDataArray);
				array_push($partitionedCellHeightArray, $temCellHeightArray);
			}
		}
		
		$dataArray = $partitionedDataArray;
		$cellHeightArray = $partitionedCellHeightArray;
	}
	
	//calculate the (max) cell height of a particular table row
	//$data: data record (string array)
	//$cellWidthArray: table cell heights
	//$addExtraHeight: boolean, whether add extra height for the corresponding row
	function calculateCellHeight($data, $cellWidthArray, $addExtraHeight) {
		global $pdf, $extraHeight;		
		$cellHeights = array();
		
		//field number of each request data record
		$colNum = count($data);
								
		for($i = 0; $i < $colNum; ++$i) {
			array_push($cellHeights, $pdf->getStringHeight($cellWidthArray[$i], $data[$i], false, true, '', 1));
		}
		
		if($addExtraHeight)
			$height = max($cellHeights) + $extraHeight;
		else 
			$height = max($cellHeights);
		
		return $height;
	}

	//calculate the string height of the request dates
	/*
	function calculateDateStrHeight($dateStr, $dateCellWidth) {
		global $pdf;
		global $ChineseFont, $fontStyle, $fontSizeDate, $fontSize;
		
		//request dates: smaller font
		$pdf->SetFont($ChineseFont, $fontStyle, $fontSizeDate);	
		
		$dateStrHeight = $pdf->getStringHeight($dateCellWidth, $dateStr, false, true, '', 1);
		
		//reset PDF font configures
		$pdf->SetFont($ChineseFont, $fontStyle, $fontSize);
		
		return $dateStrHeight;
	}
	*/
	
	//remove the Year in the Requested Dates, except when there are cross years
	//$rqdataArray: Qifu/Merit request records
	//$dateIndex: index of request dates in each Qifu/Merit record array
	/*
	function removeSameYearField($rqDataArray, $dateIndex) {	
		for($i = 0; $i < count($rqDataArray); ++$i) {
			$rqData = $rqDataArray[$i];
			$dateStr = $rqData[$dateIndex];
			$dateArray = explode(', ', $dateStr);
			
			$dateNum = count($dateArray);
			$removeYear = false;
			
			$firstDateYear = date('Y', strtotime($dateArray[0]));
			$lastDateYear = date('Y', strtotime($dateArray[$dateNum-1]));
			
			//all request dates are in the same year, remove the Year field
			if($firstDateYear == $lastDateYear) {
				$newDateStr = ''; //request date string without Year field
				
				for($j = 0; $j < $dateNum; ++$j) {
					$newDateStr = $newDateStr . date('m-d', strtotime($dateArray[$j]));
					if($j != $dateNum-1) {
						$newDateStr = $newDateStr . ', ';
					}
				}
				
				$rqData[$dateIndex] = $newDateStr;	
				$rqDataArray[$i] = $rqData;
			}
		}
		
		return $rqDataArray;
	}
	*/

	/*
	//assign GongDeZhu order to Qifu/Merit data array based on request timestamp (i.e. order in $gondDeZhuOrderArray)
	function assignGongDeZhuOrder(&$dataArray, $gondDeZhuOrderArray, $sundayTable) {
		if(count($dataArray) == 0)
			return;
		else
			$colNum = count($dataArray[0]);			

		foreach($dataArray as &$data) {
			$rqID = $data[0];

			//request to be GongDeZhu
			if($data[$colNum-1] == 1) {
				for($i = 0; $i < count($gondDeZhuOrderArray); ++$i) {
					$gongDeZhu = $gondDeZhuOrderArray[$i];
	
					//assign GongDeZhu order
					if($sundayTable==$gongDeZhu[0] && $rqID==$gongDeZhu[1]) {
						$data[$colNum-1] = $i + 1;
						break;
					}
				}				
			}			
		}
	}
	*/
	
	//query database to get Sunday Qifu/Merit data
	function queryData($sundayTable, $rqDate) {
		global $_db;
		
		$_tblFlds = getDBTblFlds( $sundayTable ); // $_tblFlds[0] contains ID which is not needed
		array_shift($_tblFlds); //remove $_tblFlds[0], i.e., the ID
		$_selFlds = implode(", ", $_tblFlds); //transform to string
		
		//SQL statement: group Sunday Qify/Merit data by rqID and concatenate all rqDate
		//sql to list all request dates
		/*
		$_sql = "SELECT {$_selFlds}, GROUP_CONCAT(rqDate ORDER BY rqDate SEPARATOR \", \"), GongDeZhu FROM {$sundayTable} "
				.	"INNER JOIN sundayRq2Days ON (ID=sundayRq2Days.rqID AND sundayRq2Days.TblName=\"{$sundayTable}\") "
				.	"INNER JOIN sundayRq2GongDeZhu ON (ID=sundayRq2GongDeZhu.rqID AND sundayRq2GongDeZhu.TblName=\"{$sundayTable}\") "
				.	"WHERE ID in (SELECT rqID FROM sundayRq2Days "
				.	"WHERE TblName=\"{$sundayTable}\" AND rqDate=\"{$rqDate}\") "
				.	"GROUP BY ID;";	
		*/
		//sql to ONLY list the current/next Sunday as request date
		/*
		$_sql = "SELECT {$_selFlds}, \"{$rqDate}\" FROM {$sundayTable} "
				.	"INNER JOIN sundayRq2Days ON (ID=rqID AND TblName=\"{$sundayTable}\") "		
				.	"WHERE ID in (SELECT rqID FROM sundayRq2Days "
				.	"WHERE TblName=\"{$sundayTable}\" AND rqDate=\"{$rqDate}\") "
				.	"GROUP BY ID;";
		*/
		//sql: no need to list the current/next Sunday
		$_sql = "SELECT {$_selFlds} FROM {$sundayTable} "
				.	"INNER JOIN sundayRq2Days ON (ID=rqID AND TblName=\"{$sundayTable}\") "		
				.	"WHERE ID in (SELECT rqID FROM sundayRq2Days "
				.	"WHERE TblName=\"{$sundayTable}\" AND rqDate=\"{$rqDate}\") "
				.	"GROUP BY ID;";
		
		//query Sunday Qify/Merit data
		$_rslt = $_db->query( $_sql );			
		$_Rows = $_rslt->fetch_all ( MYSQLI_NUM );	
		
		return $_Rows;
	}

	/*
	//query database to get GongDeZhu order
	function queryGongDeZhuOrder($rqDate) {
		global $_db;
		
		$_sql = "SELECT G.TblName, G.rqID FROM sundayRq2GongDeZhu G "
				.	"INNER JOIN sundayRq2Days D ON (G.TblName=D.TblName AND G.rqID=D.rqID) "
				.	"WHERE G.GongDeZhu=1 AND D.rqDate=\"{$rqDate}\" "
				.	"ORDER BY G.rqTime;";
		
		//query Sunday Qify/Merit data
		$_rslt = $_db->query( $_sql );			
		$_Rows = $_rslt->fetch_all ( MYSQLI_NUM );	
		
		return $_Rows;
	}
	*/
	
	//get the date of current (if NOW is Sunday) or next (if NOW is not Sunday) Sunday
	function getCurrentNextSundayDate() {
		$nextSunday = strtotime('next Sunday');
		$currentWeekNo = date('W');
		$weekNoNextSunday = date('W', $nextSunday);
  
		//NOW is Sunday
		if($currentWeekNo != $weekNoNextSunday)
			$dateStr = date('Y-m-d');
		else //NOW is not Sunday
			$dateStr = date('Y-m-d', $nextSunday);		
		return $dateStr;
	}
	
?>