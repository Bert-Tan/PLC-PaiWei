<?php
	require_once("../pgConstants.php");
	require_once("dbSetup.php");
	require_once("PaiWei_DBfuncs.php");
		
	$_tblName = $_POST [ 'dbTblName' ];
	$_usrName = $_POST [ 'dnldUsrName' ];

	$_ContentType = "application/octet-stream; charset=utf-8";
	$_fileName = $_tblName . "_" . $_usrName . ".csv";

	$_data = array(); //data: header array + PaiWei array

	//search database to get data
	function searchDB() {	
		global $_db, $_tblName, $_usrName;		
		
		$_tblFlds = getPaiWeiTblFlds( $_tblName ); // $_tblFlds[0] contains ID which is not needed
		array_pop($_tblFlds); // remove last column which is timestamp
		$_selFlds = implode( ', ', array_slice( $_tblFlds, 1 ) );

		if ( $_usrName == 'ALL' ) {
			//group PaiWei data by UsrName
			$_sql = "SELECT {$_selFlds} FROM {$_tblName} LEFT JOIN pw2Usr "
				  . "ON (ID = pwID AND TblName = \"{$_tblName}\") "
				  . "ORDER BY pwUsrName, ID;";
		} else {
			$_sql = "SELECT {$_selFlds} FROM {$_tblName} WHERE ID IN "
				  . "(SELECT pwID FROM pw2Usr WHERE TblName = \"{$_tblName}\" AND pwUsrName = \"{$_usrName}\") "
				  . "ORDER BY ID;";
		}
		
		$_rslt = $_db->query( $_sql );
		$_Rows = $_rslt->fetch_all ( MYSQLI_NUM );
		$_db->close();
		//$_lineCount = $_rslt->num_rows;

		return $_Rows;
	}

	function formHeaderData() {
		global $_data, $_tblName;
		
		switch ($_tblName) {
			case 'C001A':
				array_push( $_data, array("祈福消災受益者姓名        Well-blessing Recipient\'s Name") );
				array_push( $_data, array("C_Name") );
				break;			
			case 'D001A':
				array_push( $_data, array("地 基 主 所 在 地   Site   Address", "陽 上 啟 請 人   Requestor\'s Full Name") );
				array_push( $_data, array("D_Name", "D_Requestor") );
				break;
			case 'L001A':
				array_push( $_data, array("祖 先 姓 氏  Ancestor\'s Lastname", "後 代 子 孫 啟 請 人 姓 名  Decendent\'s Full Name") );
				array_push( $_data, array("L_Name", "L_Requestor") );
				break;
			case 'Y001A':				
				array_push( $_data, array("累劫冤親債主陽 上 有 緣 啟 請 人 Requestor\'s Full Name") );
				array_push( $_data, array("Y_Requestor") );
				break;
			case 'W001A_4':
				array_push( $_data, array("往生親友稱謂  Deceased\'s Title", "往 生 親 友 姓 名 Deceased\'s Full Name", "啟請人稱謂 Requestor\'s Title", "陽 上 啟 請 人 Requestor\'s Full Name") );
				array_push( $_data, array("W_Title", "W_Name", "R_Title", "W_Requestor") );
				break;
			case 'DaPaiWei':
				array_push( $_data, array("往生親友稱謂 Deceased\'s Title", "12個月內往 生 親 友 的姓 名 Deceased\'s Full Name", "往生日期 （西元年-月-日）Deceased Date (YYYY-MM-DD)", "啟請人稱謂 Requestor\'s Title", "陽 上 啟 請 人 Requestor\'s Full Name") );
				array_push( $_data, array("W_Title", "W_Name", "deceasedDate", "R_Title", "W_Requestor") );
				break;
		}
	}

	function formPaiweiData($_Rows) {
		global $_data;

		$_blankData = "(空白|BLANK)";
		$_blank = "%" . $_blankData . "%"; // regExp to blank out the blank data field

		foreach ( $_Rows as $_Row ) {
			array_push( $_data, preg_replace( $_blank, '', $_Row ) );	
		}
	}

	function write() {
		global $_data, $_fileName;
		
		formHeaderData();
		formPaiweiData(searchDB());
			
		$_BOM = pack("C*", 0xef,0xbb,0xbf);
		$_FP = fopen( $_fileName, "w"); // Temp file handle
		fputs( $_FP, $_BOM ); // write the BOM character to fix UTF-8 in Excel
		foreach ($_data as $line) {
			fputcsv( $_FP, $line );
		}
		fclose( $_FP );
	}




	chdir( ARCHIVEDIR );
	write(); // write to temp file	
	
	// Force download
	header( "Content-Disposition: attachment; filename=\"{$_fileName}\"");
	header( "Content-Type: {$_ContentType}" );// header("Content-Transfer-Encoding: Binary");
	header( "Content-Length: " . filesize( $_fileName ) );
	readfile( $_fileName );

?>