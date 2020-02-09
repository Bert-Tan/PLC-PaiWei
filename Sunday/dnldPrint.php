<?php
	require_once("../pgConstants.php");
	require_once("dbSetup.php");
	require_once("sunday_DBfuncs.php");
	
	//pdf configuration settings
	$pageSize = 'LETTER'; $unit = 'in'; //inch
	$pdfTitle = '祈福回向申請表'; $pageOrientation = 'L';
	$topMargin = 0.5; $bottomMargin = 0.5;
	$leftMargin = 0.5; $rightMargin = 0.5;
	
	//font settings
	$ChineseFont = 'edukai3'; $EnglishFont = 'times'; 	
	$fontStyle = 'B'; $fontSize = 18; $fontSizeDate = 14;
	
	//some common information (string)
	$title = '淨土念佛堂、圖書館';
	$qifuTableTitle = '祈  福  申  請  表';
	$meritTableTitle = '迴  向  申  請  表';
	$qifuHeaderData = array("申請人\n姓名", "受祈福\n者姓名", "與申請\n人關係", "受祈福人的狀況\n（申請理由）", "申請祈福之日期");
	$meritHeaderData = array("申請人\n姓名", "往生者\n姓名", "與申請\n人關係", "往生者年齡", "往生日期", "往生地點", "申請迴向之日期");
	
	//table cell column width
	$qifuCellWidthArray = array(1.1, 1.1, 1.1, 3.4, 3.3);
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
	//Qifu/Merit request dates string height array
	$qifuDateStrHeightArray = array();
	$meritDateStrHeightArray = array();
	
	
	
	
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
	
	//print Sunday Qifu data
	printData($qifuDataArray, $qifuHeaderData, $qifuCellHeightArray, $qifuHeaderHeight, $qifuCellWidthArray, $qifuDateStrHeightArray, $qifuTableTitle);
	
	//print Sunday Merit data
	printData($meritDataArray, $meritHeaderData, $meritCellHeightArray, $meritHeaderHeight, $meritCellWidthArray, $meritDateStrHeightArray, $meritTableTitle);
	
	//Close and output PDF document
	$pdf->Output($pdfTitle.'.pdf', 'I');
	
	
	
	
	
	//print data
	function printData($dataArray, $headerData, $cellHeightArray, $headerHeight, $cellWidthArray, $dateStrHeightArray, $tableTitle) {
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
			printTable($dataArray[$i], $headerData, $cellHeightArray[$i], $headerHeight, $cellWidthArray, $dateStrHeightArray[$i]);
		}
	}
	
	
	//print data table
	function printTable($dataArray, $headerData, $cellHeightArray, $headerHeight, $cellWidthArray, $dateStrHeightArray) {
		global $pdf;
		
		$dataNum = count($dataArray); //number of data records (rows)
		
		//print headers
		printRow($headerData, $headerHeight, 0, $cellWidthArray, true);		
		
		//print data rows
		for($i = 0; $i < $dataNum; ++$i) {
			printRow($dataArray[$i], $cellHeightArray[$i], $dateStrHeightArray[$i], $cellWidthArray, false);
		}
	}
	
	//print a request data row
	//if $isHeader=true, print header row (special format); otherwise, print request data row
	function printRow($data, $cellHeight, $dateStrHeight, $cellWidthArray, $isHeader) {
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
			}
			//other data cells
			else {
				$pdf->MultiCell($cellWidthArray[$i], $cellHeight, $data[$i], 1, 'C', $fill, 0, '', '', true, 0, false, true, $cellHeight, 'M');
			}	
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
		global $qifuDateStrHeightArray, $meritDateStrHeightArray;
			
		$rqDate = getCurrentNextSundayDate();
		
		//get Sunday Qifu data
		$sundayTable = 'sundayQifu';	
		$qifuDataArray = queryData($sundayTable, $rqDate);
		
		//get Sunday Merit data
		$sundayTable = 'sundayMerit';	
		$meritDataArray = queryData($sundayTable, $rqDate);
		
		//pre-process data
		preprocessData($qifuDataArray, $qifuHeaderData, $qifuCellHeightArray, $qifuHeaderHeight, $qifuCellWidthArray, $qifuDateStrHeightArray);
		preprocessData($meritDataArray, $meritHeaderData, $meritCellHeightArray, $meritHeaderHeight, $meritCellWidthArray, $meritDateStrHeightArray);
	}
	
	//data pre-process:
	//	(1) remove Year field in request dateStr
	//  (2) calculate PDF page title height
	//  (3) calculate table header height
	//  (4) calculate table_cell_height and request_date_string_height for each data record
	//  (5) add empty data rows (reserved for handwriting request) to data_array and height_array
	//  (6) partition request data into multiple sub-arrays, each sub-array is printed on one page
	function preprocessData(&$dataArray, $headerData, &$cellHeightArray, &$headerHeight, $cellWidthArray, &$dateStrHeightArray) {	
		global $pdf, $extraHeight, $emptyRowNum, $totalHeight, $emptyCellHeight;

		//field number of each request data record
		$colNum = count($headerData);
		
		
		//(1) remove Year field in request dateStr
		$dataArray = removeSameYearField($dataArray, $colNum-1);
		
		//(2) calculate PDF page title height
		$pdfPageTitleHeight = 4 * $pdf->getStringHeight($totalHeight, "  ", false, true, '', 1);
				
		//(3) calculate table header height
		$headerHeight = calculateCellHeight($headerData, $cellWidthArray, false, 0, false);
				
		//(4) calculate table_cell_height and request_date_string_height for each data record
		for($i = 0; $i < count($dataArray); ++$i) {
			
			$data = $dataArray[$i]; //data record
			
			$dateStrHeight = calculateDateStrHeight($data[$colNum-1], $cellWidthArray[$colNum-1]);
			$cellHeight = calculateCellHeight($data, $cellWidthArray, true, $dateStrHeight, true);
			
			array_push($dateStrHeightArray, $dateStrHeight);
			array_push($cellHeightArray, $cellHeight);
		}
		
		//(5) add empty data rows (reserved for handwriting request) to data_array and height_array		
		for($i = 0; $i < $emptyRowNum; ++$i) {
			//add data
			$data = array();
			for($j = 0; $j < $colNum; ++$j) {
				array_push($data, "");
			}
			array_push($dataArray, $data);
			
			//add height (use header height)
			array_push($dateStrHeightArray, 0);
			array_push($cellHeightArray, $emptyCellHeight);
		}

		//(6) partition request data into multiple sub-arrays, each sub-array is printed on one page
		$partitionedDataArray = array(); //request data after partition
		$partitionedCellHeightArray = array(); //cell heights of request data after partition	
		$partitionedDateStrHeightArray = array(); //date string heights of request data after partition			
		$temDataArray = array(); //temporary partitioned data (one sub-array)
		$temCellHeightArray = array(); //cell heights of temporary partitioned data (one sub-array)
		$temDateStrHeightArray = array(); //date string heights of temporary partitioned data (one sub-array)
		$temHeight = $pdfPageTitleHeight + $headerHeight; //height of temporary data
		for($i = 0; $i < count($dataArray); ++$i) {

			//if the current page is full
			if($temHeight+$cellHeightArray[$i]>$totalHeight) {
				array_push($partitionedDataArray, $temDataArray);
				array_push($partitionedCellHeightArray, $temCellHeightArray);
				array_push($partitionedDateStrHeightArray, $temDateStrHeightArray);
		
				$temDataArray = array();
				$temCellHeightArray = array();
				$temDateStrHeightArray = array();
				$temHeight = $pdfPageTitleHeight + $headerHeight;				
			}

			//partition data to the temporary array
			$temHeight = $temHeight + $cellHeightArray[$i];
			array_push($temDataArray, $dataArray[$i]);
			array_push($temCellHeightArray, $cellHeightArray[$i]);
			array_push($temDateStrHeightArray, $dateStrHeightArray[$i]);
			
			//the last data record
			if($i==count($dataArray)-1) {
				array_push($partitionedDataArray, $temDataArray);
				array_push($partitionedCellHeightArray, $temCellHeightArray);
				array_push($partitionedDateStrHeightArray, $temDateStrHeightArray);
			}
		}
		
		$dataArray = $partitionedDataArray;
		$cellHeightArray = $partitionedCellHeightArray;
		$dateStrHeightArray = $partitionedDateStrHeightArray;
	}
	
	//calculate the (max) cell height of a particular table row
	//$data: data record (string array)
	//$cellWidthArray: table cell heights
	//$addExtraHeight: boolean, whether add extra height for the corresponding row
	//$dateStrHeight: the string height of the request dates
	//$containDate: whether the input data record contain request dates
	function calculateCellHeight($data, $cellWidthArray, $addExtraHeight, $dateStrHeight, $containDate) {
		global $pdf, $extraHeight;		
		$cellHeights = array();
		
		//field number of each request data record
		$colNum = count($data);
							
		for($i = 0; $i < $colNum; ++$i) {
			//request dates: smaller font
			if($i == $colNum-1 && $containDate) {			
				array_push($cellHeights, $dateStrHeight);
			}
			else {
				array_push($cellHeights, $pdf->getStringHeight($cellWidthArray[$i], $data[$i], false, true, '', 1));
			}
		}
		
		if($addExtraHeight)
			$height = max($cellHeights) + $extraHeight;
		else 
			$height = max($cellHeights);
		
		return $height;
	}

	//calculate the string height of the request dates
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
	
	//remove the Year in the Requested Dates, except when there are cross years
	//$rqdataArray: Qifu/Merit request records
	//$dateIndex: index of request dates in each Qifu/Merit record
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
	
	//query database to get Sunday Qifu/Merit data
	function queryData($sundayTable, $rqDate) {
		global $_db;
		
		$_tblFlds = getDBTblFlds( $sundayTable ); // $_tblFlds[0] contains ID which is not needed
		array_shift($_tblFlds); //remove $_tblFlds[0], i.e., the ID
		$_selFlds = implode(", ", $_tblFlds); //transform to string
		
		//SQL statement: group Sunday Qify/Merit data by rqID and concatenate all rqDate
		$_sql = "SELECT {$_selFlds}, GROUP_CONCAT(rqDate ORDER BY rqDate SEPARATOR \", \") FROM {$sundayTable} "
				.	"INNER JOIN sundayRq2Days ON (ID=rqID AND TblName=\"{$sundayTable}\") "
				.	"WHERE ID in (SELECT rqID FROM sundayRq2Days "
				.	"WHERE TblName=\"{$sundayTable}\" AND rqDate=\"{$rqDate}\") "
				.	"GROUP BY ID;";	
		
		//query Sunday Qify/Merit data
		$_rslt = $_db->query( $_sql );			
		$_Rows = $_rslt->fetch_all ( MYSQLI_NUM );	
		
		return $_Rows;
	}
	
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