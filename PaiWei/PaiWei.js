/**********************************************************
 *                    Global variables                    *
 **********************************************************/
var SESS_LANG_CHN = 1;	// These variables are used as CONSTANTS
var SESS_MODE_EDIT = 0;
var SESS_MODE_SRCH = 1;
var SESS_TYP_USR = 0;
var SESS_TYP_MGR = 1;
var SESS_TYP_WEBMASTER = 2;

var _sessUsr = null, _sessPass = null, _sessType = null, _sessLang = null;
var _sessMode = SESS_MODE_EDIT; // default
var _dbInfo = {}, _ajaxData = {};
var _tblName = null, _tblSize = 0;
var _pilotDataRow = null;	// to be used for adding rows
var _wTitleInput = null;	// handling 稱謂
var _rTitleInput = null;
var _pwPlqDate = null;
var _rtrtDate = null;

var _dupBtns = null;
var _delBtns = null;
var _editBtns = null;
var _addRowBtn = null;
var _srchBtn = null;
var _delAllBtn = null;
var _validBtn = null;
var _validAllBtn = null;
var _alertUnsaved = null;
var _blankData = null; // blank data filler for W_Title & R_Title fields; they can be blank
var _wtList = null;	// W_Title & R_Title selection list
var _rtList = null;
var _icoName = null;
var _rqTbls = [ 'D001A', 'L001A', 'Y001A', 'W001A_4', 'DaPaiWei', 'DaPaiWeiRed' ];

/**********************************************************
 *                    Support functions                   *
 **********************************************************/
function readSessParam() {
	_ajaxData = {}; _dbInfo = {};
	_dbInfo[ 'tblName' ] = "pwParam";
	_ajaxData[ 'dbReq' ] = 'dbREADpwParam';
	_ajaxData[ 'dbInfo' ] = JSON.stringify ( _dbInfo );
	$.ajax({
		url: "./ajax-pwDB.php",
		method: 'POST',
		data: _ajaxData,
		success: function( rsp ) {
			var rspV = JSON.parse ( rsp );			
			for ( var X in rspV ) {
				switch ( X ) {
					case 'URL':
						location.replace( rspV[ X ] );
						return false;
					case 'notActive':	// No retreat active; put out msg
						alertMsg = ( _sessLang == SESS_LANG_CHN ) ?
							"牌位申請已過期，\n或本念佛堂近期內沒有法會；\n牌位申請功能暫停！\n\n您將撤出！ 謝謝！"
						  : "Name Plaque Application deadline passed; or,\nthere is NO Planned Retreat!\nFunction deactivated.\n\nYou will logout! Thank you!";
						alert ( alertMsg );
						location.replace( "../Login/Logout.php" );
						return false;
					case 'pwPlqDate':
						_pwPlqDate = rspV[ X ];
						break;
					case 'rtrtDate':
						_rtrtDate = rspV[ X ];
						break;
					case 'wtList':
						_wtList = rspV[ X ];
						break;
					case 'rtList':
						_rtList = rspV[ X ];
						break;
					case 'usrName':
						_sessUsr = rspV[X];
						break;
					case 'usrPass':
						_sessPass = rspV[X];
						break;
					case 'sessType':
						_sessType = rspV[X];
						break;
					case 'sessLang':
						_sessLang = rspV[ 'sessLang' ];
						break;
					case 'icoName':
						_icoName = rspV[X];
						break;
					case 'tblName':
						_tblName = rspV[X];
						break;	
					case 'errCount':
						x = rspV [ X ];
						eMSG = '';
						for ( i=0; i < x; i++ ) {
							eMSG += rspV [ 'errRec' ][i] + "\n";
						}
						alert( eMSG );
						return false;						
				} // switch()
			} // for loop
			_alertUnsaved = ( _sessLang == SESS_LANG_CHN ) ? '未保存的更動會被丟棄！' : 'Unsaved Data will be LOST!';
			_blankData = ( _sessLang == SESS_LANG_CHN ) ? "空白" : "BLANK";
			//a PaiWei table is chosen: display PaiWei date
			if ( _tblName != null ) {
				$(".tabMenu th").removeClass("active").css("border", "1px solid white");
				$(".tabMenu th[data-tbl=\""+_tblName+"\"]").addClass("active").css("border-bottom", "1px solid green");
				$("#tabDataFrame").find("*").unbind(); $("#tabDataFrame").empty();
				loadTblData( _tblName, 1, 30, _icoName, "tabDataFrame" );
				enableTooltip();
			}
			//no PaiWei table is chosen: display PaiWei User Guide
			else {
				$(".tabMenu th[data-tbl=ug]").trigger( 'click' );
			}
		}, // Success Handler
		error: function (jqXHR, textStatus, errorThrown) {
			alert( "readSessParam()\tError Status:\t"+textStatus+"\t\tMessage:\t\t"+errorThrown+"\n" );
		} // End of ERROR Handler							
	}); // AJAX call
} // readSessParam()

function leapYear( yr ) {
	return( ( yr % 100 === 0 ) ? ( yr % 400 === 0 ) : ( yr % 4 === 0 ) );
} // function leapYear()

function chkDate ( dateString ) { // in YYYY-MM-DD format
	var rtD = new Date( _rtrtDate );
	var plqD = new Date( _pwPlqDate );
	var rtYr = rtD.getFullYear();
	var patString = "^(" + (rtYr-1) + "|" + rtYr + ")-(0?[1-9]|1[0-2])-(0?[1-9]|[12]\\d|3[01])$";
	var pattern = new RegExp( patString );

	if ( !dateString.match( pattern ) ) return false;

	var d = dateString.split( '-' ); // d[0] => YYYY, d[1] => MM, d[2] = DD
	var dd = 0;
	var pwD = new Date( d[0], d[1]-1, d[2] );

	if ( ( Number(d[0]) != rtYr) && ( Number(d[0]) != ( rtYr-1 ) ) ) return false;

	switch ( Number( d[1] ) ) {
		case 2:
			var dd = leapYear( d[0] ) ? 29 : 28;
			break;
		case 1:
		case 3:
		case 5:
		case 7:
		case 8:
		case 10:
		case 12:
			dd = 31;
			break;
		case 4:
		case 6:
		case 9:
		case 11:
			dd = 30;
			break;
		default:
			return false;
	} // switch on MM
	return ( ( ( 1 <= Number(d[2]) ) && ( Number(d[2]) <= dd ) ) && ( ( plqD <= pwD ) && ( pwD < rtD ) ) );
} // function chkDate()

function chgEdit2Upd ( editBtn ) {
	var updBtnVal = ( _sessLang == SESS_LANG_CHN ) ? "保存更動" : " Update  ";
	var canBtnVal = ( _sessLang == SESS_LANG_CHN ) ? "取消更動" : "  Cancel  ";
	var canBtn = editBtn.clone();	
	var td = editBtn.parent();
	var validBtn = td.find(".validBtn");
	
	editBtn.unbind(); // unbind Edit Button Handler
	editBtn.attr( "value", updBtnVal ); // change 'Edit' button to become an 'Update' button
	editBtn.removeClass( 'editBtn' ).addClass( 'updBtn' ); // change class
	editBtn.on( 'click', updBtnHdlr );

	canBtn.unbind(); // unbind Edit Button Handler
	canBtn.attr( "value", canBtnVal );
	canBtn.removeClass( 'editBtn' ).addClass( 'canBtn' );
	canBtn.on( 'click', canBtnHdlr );
	validBtn.after( canBtn ); // display 'Cancel' button

	validBtn.hide(); // hide Valid Button	
} // chgEdit2Upd()

function chgUpd2Edit ( updBtn ) {
	var editBtnVal = ( _sessLang == SESS_LANG_CHN ) ? "更改" : "    Edit    ";
	var td = updBtn.parent();

	updBtn.unbind(); // unbind Update Button Handler
	updBtn.attr( "value", editBtnVal ); // change 'Update' button to become an 'Edit' button
	updBtn.removeClass( 'updBtn' ).addClass( 'editBtn' ); // change class
	updBtn.on( 'click', editBtnHdlr );	

	td.find(".canBtn").remove(); // remove 'Cancel' button	
	td.find(".validBtn").show(); // show Valid Button
} // chgUpd2Edit()

function isJSON( str ) {
    try {
        JSON.parse(str);
    } catch (e) {
        return false;
    }
    return true;
} // isJSON()

function loadTblData( tblName, pgNbr, numRec, sessUsr, frameID ) {	/* dataOnly parameter is eliminated */
	// before introducing page-by-page surfing, the dataonly parameter isn't really needed
	var dataArea = $( "#" + frameID );
	var tblHdrWrapper =	'<div class="dataHdrWrapper"></div>';
	var tabDataFrameHeight = "57vh";
	if (tblName == 'DaPaiWei') tabDataFrameHeight = "55vh";
	var tblDataWrapper = '<div class="dataBodyWrapper" style="height: ' + tabDataFrameHeight + '"></div>';
	var errText = ( _sessLang == SESS_LANG_CHN ) ? '沒有找到所選擇的法會的牌位，<br/>請輸入或上載牌位資料。'
												 : 'No record found!<br/>Please input or upload Data';
	var errMsg =	'<h1 class="centerMe errMsg">' + errText + '</h1>';
	_ajaxData = {}; _dbInfo = {};

	dataArea.empty();
	dataArea.append( tblHdrWrapper , tblDataWrapper );

	_dbInfo[ 'tblName' ] = tblName;
	_dbInfo[ 'pgNbr' ] = pgNbr;
	_dbInfo[ 'pwRqstr' ] = sessUsr;
	_ajaxData[ 'dbReq' ] = 'dbREAD';
	_ajaxData[ 'dbInfo' ] = JSON.stringify ( _dbInfo );
	$.ajax({
		url: "./ajax-pwDB.php",
		method: 'POST',
		data:	_ajaxData,
		success: function ( rsp ) { // SUCCESS handler
			var rspV = JSON.parse( rsp );
			$("#tabDataFrame").css("overflow-y", "initial");
			for ( var X in rspV ) {
				switch( X ) {
					case 'URL':
						location.replace( rspV [ X ] );
						return;
					case 'myDataHdr':
						$(".dataHdrWrapper").find("*").unbind();
						$(".dataHdrWrapper").empty();
						$(".dataHdrWrapper").html( rspV[ X ]);
						break;
					case 'myData':
						$(".dataBodyWrapper").find("*").unbind();
						$(".dataBodyWrapper").empty();
						$(".dataBodyWrapper").html( rspV[ X ] );
						break;
					case 'myDataSize':
						_tblSize = rspV[ X ];
						break;
				} // switch()
			} // for loop
			_pilotDataRow = $("table.dataRows tbody > tr:first").clone();
			if ( _tblSize == 0 ) {
				$(".dataBodyWrapper").find("*").unbind();
				$(".dataBodyWrapper").find("tr").remove();
				$(".dataBodyWrapper").append( errMsg );
				if ( _sessLang != SESS_LANG_CHN ) {
					$(".dataBodyWrapper").find("h1").css( "letter-spacing", "normal");
				}

				//if no existing data, add an empty and editable row
				addRowBtnHdlr();
			}
			_sessMode = SESS_MODE_EDIT;
			ready_edit();
		}, // End of SUCCESS Handler
		
		error: function (jqXHR, textStatus, errorThrown) {
			alert( "Line 228\tError Status:\t"+textStatus+"\t\tMessage:\t\t"+errorThrown+"\n" );
		} // End of ERROR Handler
	}); // ajax call	
} // loadTblData()

/************************************************************
 * Event Handler - when the PaiWei Upload Form is submitted *
 ************************************************************/
function myPaiWeiUpLoad ( e ) {
	e.preventDefault();
	var myFormData = new FormData ( this ); // myFormData.append( 'pwUsr', _sessionUsr );
	var myHdlr = $(this).attr("action");
	$.ajax({
		method: "POST",
		url: myHdlr,
		data: myFormData,
		processData: false,
		contentType: false,
		cache: false,
		success: function ( rsp ) {
			alert( rsp );
			return;
    	}, // End of Success Handler 
		error: function (jqXHR, textStatus, errorThrown) {
			alert( "myPaiWeiUpload()\tError Status:\t"+textStatus+"\t\tMessage:\t\t"+errorThrown+"\n" );
		} // End of ERROR Handler		
	}); // AJAX Call
} // myPaiWeiUpLoad()

/**********************************************************
 * Event Handler - When the Add_a_Row Button is clicked   *
 **********************************************************/
function addRowBtnHdlr() {
	var dirtyCells = $("tbody input[type=text][data-changed=true]").length;
	var insBtnText = ( _sessLang == SESS_LANG_CHN ) ? "加入" : "Insert";
	var insBtn = '<input class="insBtn" type="button" value="' + insBtnText + '">';
	var recTxt1 = ( _sessLang == SESS_LANG_CHN ) ? "叩薦" : "Sincerely Recommend";
	var recTxt2 = ( _sessLang == SESS_LANG_CHN ) ? "敬薦" : "Recommend";
	var selEle = "<select class=\"rec\" style=\"float:right; font-size:0.9em;\">" + 
					"<option>" + recTxt1 + "<\/option>" + 
					"<option>" + recTxt2 + "<\/option>" +
				 "<\/select>";
	var tbody = $(".dataRows tbody");
	var newRow = _pilotDataRow.clone();
	var newRowDataCells = newRow.find("input[type=text]");
	var lastTd = newRow.find("td:last");
	var cellText = ( _sessLang == SESS_LANG_CHN ) ? "輸入牌位資料" : "Name Plaque";
	var	dateText = ( _sessLang == SESS_LANG_CHN ) ? "請輸入 年-月-日" : "YYYY-MM-DD";
	
	$(".errMsg").remove();
	if ( ( dirtyCells > 0 ) && ( !confirm( _alertUnsaved ) ) ) return;
	
	newRow.attr( "data-keyn", _pilotDataRow.attr("data-keyn") ); // copy the Key Name
	newRow.attr( "id", '' ) ; // no tuple Key value 
	newRowDataCells.val( cellText );
	newRowDataCells.attr( { 'data-oldv' : '', 'data-pmptv' : '' } );
	newRowDataCells.prop( 'disabled', false );
	newRow.find("input[data-fldn=deceasedDate]").val( dateText );
	newRow.find("input[data-fldn=W_Requestor]").after( selEle );
	/*
	 * Replace W_Title and R_Title input fields with dropdown items selection;
	 * Remember/save the orginal input fields
	 */
	_wTitleInput = newRow.find("input[data-fldn=W_Title]").replaceWith( _wtList );
	_rTitleInput = newRow.find("input[data-fldn=R_Title]").replaceWith( _rtList );
	newRowDataCells.on( 'blur', dataChgHdlr ); // bind to the data change handler
	newRowDataCells.on( 'focus', onFocusHdlr ); // bind to the on 'focus' handler
	lastTd.html( insBtn ); // place the 'Insert' button
	lastTd.find("input[type=button]").on( 'click', insBtnHdlr ); // bind to Insert Button click handler
	
	var wTitleSelect = newRow.find("select[data-fldn=W_Title]");// find wTitle 'select' element
	var rTitleSelect = newRow.find("select[data-fldn=R_Title]");// find rTitle 'select' element
	wTitleSelect.on( 'mouseover', onMouseoverHdlr ); // bind to the on 'mouseover' handler
	rTitleSelect.on( 'mouseover', onMouseoverHdlr ); // bind to the on 'mouseover' handler
	
	if ( _sessMode == SESS_MODE_SRCH ) {
		$(".dataBodyWrapper").find("*").unbind()
		tbody.find("tr").remove(); // remove all data rows
		_sessMode = SESS_MODE_EDIT;
	}
	tbody.append( newRow );
} // addRowBtnHdlr()

function chgSrch2Lookup ( srchBtn ) {
	var exitSrchBtnVal = ( _sessLang == SESS_LANG_CHN ) ? "退出搜尋" : "Exit Search";
	var td = srchBtn.parent();	
	var exitSrchBtn = srchBtn.clone();	

	exitSrchBtn.unbind();
	exitSrchBtn.attr( "value", exitSrchBtnVal );
	exitSrchBtn.attr( "id", "exitSrchBtn" );
	exitSrchBtn.on( 'click', exitSrchBtnHdlr );
	srchBtn.after( exitSrchBtn );

	srchBtn.hide();
	td.find("#delAllBtn").hide();
	td.find("#validAllBtn").hide();
} // chgSrch2Lookup()

function chgLookup2Srch ( exitSrchBtn ) {
	var td = exitSrchBtn.parent();

	exitSrchBtn.remove();
	td.find("#delAllBtn").show();
	td.find("#validAllBtn").show();
	td.find("#srchBtn").show();
} // chgLookup2Srch()

/**********************************************************
 * Event Handler - When a Exit Search Button is clicked   *
 **********************************************************/
function exitSrchBtnHdlr() {
	var dirtyCells = $("tbody input[type=text][data-changed=true]").length;
	if ( ( dirtyCells > 0 ) && ( !confirm( _alertUnsaved ) ) ) return;
	
	loadTblData( _tblName, 1, 30, ( ( _icoName != null ) ? _icoName : _sessUsr ), "tabDataFrame" );
} // exitSrchBtnHdlr()

/**********************************************************
 * Event Handler - When the Lookup Button is clicked      *
 **********************************************************/
function lookupBtnHdlr() {
	var inputSrchData = ( _sessLang == SESS_LANG_CHN ) ? '請請輸入查詢資料！' : 'Please enter search pattern!';
	var notFoundText = ( _sessLang == SESS_LANG_CHN ) ? '沒有找到所要找的牌位，請輸入或上載牌位資料。'
																									: 'No record found! Please Input or Upload Data.';
	var notFoundMSG = '<h1 class="centerMe errMsg">' + notFoundText + '</h1>';
	var tblFlds = {};
	var thisRow = $(this).closest("tr");
	var cellsChanged = thisRow.find("input[type=text][data-changed=true]");

	_ajaxData = {}; _dbInfo = {};
	if ( cellsChanged.length == 0 ) {
		alert( inputSrchData );
		return;
	}
	
	cellsChanged.each(function(i) {
		tblFlds [ $(this).attr("data-fldn") ] = $(this).val();
	});
	_dbInfo[ 'tblName' ] = _tblName;
	_dbInfo[ 'tblFlds' ] = tblFlds;
	_dbInfo[ 'pwRqstr' ] = ( _icoName != null ) ? _icoName : _sessUsr;
	_ajaxData[ 'dbReq' ] = 'dbSEARCH';
	_ajaxData[ 'dbInfo' ] = JSON.stringify ( _dbInfo );
	_sessMode = SESS_MODE_EDIT; // Search Mode is over; regardless of the search result
	$(".dataBodyWrapper").find("*").unbind(); // done with the current data
	$(".dataBodyWrapper").empty();
	$.ajax({
		url: "./ajax-pwDB.php",
		method: 'POST',
		data: _ajaxData,
		success: function ( rsp ) {
			var rspV = JSON.parse ( rsp );
			for ( var X in rspV ) {
				switch ( X ) {
					case 'URL':
						location.replace( rspV[ X ] );
						return;
					case 'myData': // The Server returns a data table
						$(".dataBodyWrapper").find("*").unbind();
						$(".dataBodyWrapper").html( rspV[ X ] );
						_pilotDataRow = $(".dataRows tbody > tr:first").clone();
						break;
					case 'myDataSize':
						_tblSize = rspV [ X ];
						if ( _tblSize == 0 ) { // an empty row was received for the _pilotDataRow; now remove it
							$(".dataBodyWrapper").find("tr").remove();
							$(".dataBodyWrapper").append( notFoundMSG );
							if ( _sessLang != SESS_LANG_CHN ) {
								$(".dataBodyWrapper").find("h1").css( "letter-spacing", "normal");
							}
							return;
						}
						ready_edit();
						break;
					case 'errCount': 
						var msgText = '';
						rspV[ 'errRec' ].forEach( function( element ) {
							msgText += element;
						});
						var errMSG = '<h1 class="centerMe errMsg">' + msgText + '</h1>';
						$(".dataBodyWrapper").append( errMSG ); // reset to default					
						break;
				} // switch()
			} // for loop
		}, // success handler
		error: function (jqXHR, textStatus, errorThrown) {
			alert( "looupBtnHdlr()\tError Status:\t"+textStatus+"\t\tMessage:\t\t"+errorThrown+"\n" );
		} // End of ERROR Handler							
	}); // AJAX CALL
} // lookupBtnHdlr() 
 
/**********************************************************
 * Event Handler - When the Search Button is clicked	  *
 **********************************************************/
function srchBtnHdlr() {	
	var dirtyCells = $("tbody input[type=text][data-changed=true]").length;
	var lookupBtnText = ( _sessLang == SESS_LANG_CHN ) ? "查詢" : "Look Up";
	var lookupBtn = '<input class="lookupBtn" type="button" value="' + lookupBtnText + '">';
	var tbody = $(".dataRows tbody"); 
	var newRow = _pilotDataRow.clone();
	var newRowDataCells = newRow.find("input[type=text]");
	var lastTd = newRow.find("td:last");
	var cellText = ( _sessLang == SESS_LANG_CHN ) ? "請輸入查詢資料" : "Please Enter Look Up Text";
	var	dateText = ( _sessLang == SESS_LANG_CHN ) ? "請輸入 年-月-日" : "Please Enter YYYY-MM-DD";
	
	$(".errMsg").remove();
	if ( _sessMode == SESS_MODE_SRCH ) {
		var alert_txt = ( _sessLang == SESS_LANG_CHN ) ? "已經在搜尋狀態!" : "Already in Search Mode!";
		alert( alert_txt );
		return;
	}
	if ( ( dirtyCells > 0 ) && ( !confirm( _alertUnsaved ) ) ) return;

	newRow.attr( "data-keyn", _pilotDataRow.attr("data-keyn") ); // copy the Key Name
	newRow.attr( "id", '' ) ; // no tuple Key value 
	newRowDataCells.val( cellText );
	newRowDataCells.attr( { 'data-oldv' : '', 'data-pmptv' : '' } );
	newRowDataCells.prop( 'disabled', false );
	if ( _tblName == 'DaPaiWei' ) {
		newRow.find("input[data-fldn='deceasedDate']").val( dateText );
	}
	newRowDataCells.on( 'blur', dataChgHdlr ); // bind to the data change handler
	newRowDataCells.on( 'focus', onFocusHdlr ); // bind to the on 'focus' handler
	lastTd.html( lookupBtn ); // place the 'Lookup' button
	lastTd.find("input[type=button]").on( 'click', lookupBtnHdlr ); // bind to Lookup Button click handler
	tbody.find("*").unbind();	
	tbody.find("tr").remove(); // remove all data rows
	tbody.append( newRow );
	_sessMode = SESS_MODE_SRCH;

	chgSrch2Lookup($(this));
} // srchBtnHdlr()

/**********************************************************
 * Event Handler - When the Del ALL Button is clicked	  *
 **********************************************************/
function delAllBtnHdlr() {
	var delAllAlert = ( _sessLang == SESS_LANG_CHN ) ? '刪除的資料將無法恢復，請確認！'
												  : 'Deleted data cannot be undone, please confirm！';
																								
	if ( !confirm( delAllAlert ) ) return;

	_ajaxData = {}; _dbInfo = {};
	_dbInfo[ 'tblName' ] = _tblName;
	_dbInfo[ 'pwRqstr' ] = ( _icoName != null ) ? _icoName : _sessUsr;
	_ajaxData [ 'dbReq' ] = 'dbDELX';
	_ajaxData [ 'dbInfo' ] = JSON.stringify ( _dbInfo );
	$.ajax({
		url: "./ajax-pwDB.php",
		method: 'POST',
		data:	_ajaxData,
		success: function( rsp ) { // alert ( rsp ); // return; // Success Handler
			var rspV = JSON.parse ( rsp );
			for ( var X in rspV ) {
				switch ( X ) {
				case 'URL':
					location.replace( rspV[ X ] );
					return;
				case 'delSUCCESS':
					alert( rspV [ X ] );
					$("table.dataRows").find("*").unbind();
					$("table.dataRows").find("tr").remove();
					return;
				case 'errCount':
					x = rspV [ X ];
					eMSG = '';
					for ( i=0; i < x; i++ ) {
						eMSG += rspV [ 'errRec' ][i] + "\n";
					}
					alert( eMSG );
					return;
				} // switch()
			} // for loop
		}, // End of Success Handler
		error: function (jqXHR, textStatus, errorThrown) {
			alert( "delAllBtnHdlr()\tError Status:\t"+textStatus+"\t\tMessage:\t\t"+errorThrown+"\n" );
		} // End of ERROR Handler
	}); // AJAX Call
} // delAllBtnHdlr()

/**********************************************************
 * Event Handler - When the Valid ALL Button is clicked	  *
 **********************************************************/
function validAllBtnHdlr() {
	_ajaxData = {}; _dbInfo = {};
	_dbInfo[ 'tblName' ] = _tblName;
	_dbInfo[ 'pwRqstr' ] = ( _icoName != null ) ? _icoName : _sessUsr;
	_ajaxData [ 'dbReq' ] = 'dbVALIDX';
	_ajaxData [ 'dbInfo' ] = JSON.stringify ( _dbInfo );
	$.ajax({
		url: "./ajax-pwDB.php",
		method: 'POST',
		data:	_ajaxData,
		success: function( rsp ) { // alert ( rsp ); // return; // Success Handler
			var rspV = JSON.parse ( rsp );
			for ( var X in rspV ) {
				switch ( X ) {
				case 'URL':
					location.replace( rspV[ X ] );
					return;
				case 'validSUCCESS':
					alert( rspV [ X ] );
					$("table.dataRows").find(".validBtn").prop( "disabled", true ); // disable Valid Button
					return;
				case 'errCount':
					x = rspV [ X ];
					var eMSG = ( _sessLang == SESS_LANG_CHN ) ? "牌位資料驗證失敗！\n" : "Validation Failed!\n";
					for ( i=0; i < x; i++ ) {
						eMSG += rspV [ 'errRec' ][i] + "\n";
					}
					alert( eMSG );
					return;
				} // switch()
			} // for loop
		}, // End of Success Handler
		error: function (jqXHR, textStatus, errorThrown) {
			alert( "validAllBtnHdlr()\tError Status:\t"+textStatus+"\t\tMessage:\t\t"+errorThrown+"\n" );
		} // End of ERROR Handler
	}); // AJAX Call
} // validAllBtnHdlr()

/**********************************************************
 * Event Handler - When an Insert Button is clicked       *
 **********************************************************/
function insBtnHdlr() {
	var insBtn = $(this);
	var editBtnText = ( _sessLang == SESS_LANG_CHN ) ? '更改' : 'Edit';
	var delBtnText = ( _sessLang == SESS_LANG_CHN ) ? '刪除' : 'Del';
	var dupBtnText = ( _sessLang == SESS_LANG_CHN ) ? '複製' : 'Dup';
	var validBtnText = ( _sessLang == SESS_LANG_CHN ) ? '驗證' : 'Validate';
	var alertText = ( _sessLang == SESS_LANG_CHN ) ? "請輸入完整的牌位資料" : "Please enter complete plaque data";
	var myEditBtns = '<input class="editBtn" type="button" value="' + editBtnText + '">&nbsp;&nbsp;&nbsp;' +
					 '<input class="delBtn" type="button" value="' + delBtnText + '"><br><br>' +
					 '<input class="validBtn" type="button" value="' + validBtnText + '" disabled>&nbsp;&nbsp;&nbsp;' + 
					 '<input class="dupBtn" type="button" value="' + dupBtnText + '">';
	var thisRow = $(this).closest("tr");
	var cellsChanged = thisRow.find("input[data-changed=true]");
	var noDropdown = ( thisRow.find("select").length == 0 );
	var recV = null;
	var rName = null;
	var tblFlds = {};

	if ( cellsChanged.length == 0 ) return;
	if ( cellsChanged.length != thisRow.find("input[type=text]").length ) { // incomplete data input
		alert( alertText );
		return;
	}
	
	switch ( _tblName ) { // taking care of 叩薦 or 敬薦; combine it with the Requestor's Name
	case 'W001A_4':
	case 'DaPaiWei':
		/* The row was duplicated for insert; there will be no dropdown selection for rt nor wt */
		if ( noDropdown ) {
			break;
		}
		recV = thisRow.find("select.rec option:selected").val();
		rNameO = thisRow.find("input[data-fldn=W_Requestor]").val();
		thisRow.find("input[data-fldn=W_Requestor]").val( rNameO + ' ' + recV ); // combine 
		// now taking care of the 稱謂 fields; they were input as selections
		wtV = thisRow.find("select.wTitle option:selected").val(); // Title value selected from dropdown
		rtV = thisRow.find("select.rTitle option:selected").val();
		tblFlds[ thisRow.find("select.wTitle").attr('data-fldn') ] = wtV;
		tblFlds[ thisRow.find("select.rTitle").attr('data-fldn') ] = rtV;
		break;
	case 'D001A':
		recV = " 敬薦";
		rNameO = thisRow.find("input[data-fldn=D_Requestor]");
		rName = rNameO.val().trim();
		rName = rName.replace( /\s*敬薦/gu, '' ); /* delete */
		rNameO.val( rName + recV );
		break;
	case 'Y001A':
		recV = " 敬薦";
		rNameO = thisRow.find("input[data-fldn=Y_Requestor]");
		rName = rNameO.val().trim();
		rName = rName.replace( /\s*敬薦/gu, '' ); /* delete */
		rNameO.val( rName + recV );
		break;
	case 'L001A':
		recV = " 叩薦"; targetV = new RegExp( "\s*叩薦", "gu");
		rNameO = thisRow.find("input[data-fldn=L_Requestor]");
		rName = rNameO.val().trim();
		rName = rName.replace( /\s*叩薦/gu, '' ).trim(); /* delete it */
		rNameO.val( rName + recV );
		break;
	} // switch()

	_ajaxData = {}; _dbInfo = {};	
	cellsChanged.each(function(i) { // (name, value) pair
		tblFlds [ $(this).attr("data-fldn") ] = $(this).val();
	});
	_dbInfo[ 'tblName' ] = _tblName;
	_dbInfo[ 'tblFlds' ] = tblFlds;
	_dbInfo[ 'pwRqstr' ] = ( _icoName != null ) ? _icoName : _sessUsr;
	_ajaxData[ 'dbReq' ] = 'dbINS';
	_ajaxData[ 'dbInfo' ] = JSON.stringify ( _dbInfo );
	$.ajax({
		url: "./ajax-pwDB.php",
		method: 'POST',
		data: _ajaxData,
		success: function( rsp ) {
			var rspV = JSON.parse ( rsp );
			for ( var X in rspV ) {
				switch ( X ) {
					case 'URL':
						location.replace( rspV[ X ] );
						return;
					case 'insSUCCESS': // rspV[X] holds the tupID 
						var alertMsg = ( _sessLang == SESS_LANG_CHN ) ? "牌位資料加入完畢！" : "Record Inserted!";
						thisRow.attr("data-keyn", 'ID' ); thisRow.attr( 'id', rspV[ X ] );
						cellsChanged.each(function(i) {
							$(this).attr( "data-oldv", $(this).val() ); // remember the current value
							$(this).attr( "data-changed", "false" );
						}); // each
						// now the 稱謂 fields
						if ( !noDropdown && (_tblName == 'W001A_4' || _tblName == 'DaPaiWei') ) {
							thisRow.find("select.rec").remove(); // 叩薦，敬薦 dropdown; remove it
							_wTitleInput.attr( "data-oldv", wtV ); _wTitleInput.val( wtV );
							_rTitleInput.attr( "data-oldv", rtV ); _rTitleInput.val( rtV );
							thisRow.find("select.wTitle").replaceWith( _wTitleInput );
							thisRow.find("select.rTitle").replaceWith( _rTitleInput );
						}
						thisRow.find("input[type=text]").prop( "disabled", true ); // disable edit
						thisRow.find("input[type=text]").unbind();
						thisRow.find("input[type=text]").removeAttr('data-pmptv');
						lastTd = thisRow.find("td:last"); insBtn.unbind(); insBtn.remove();
						lastTd.html( myEditBtns ); // change to edit & delete buttons
						lastTd.find(".editBtn").on( 'click', editBtnHdlr ); // bind to the Edit click handler
						lastTd.find(".delBtn").on( 'click', delBtnHdlr ); // bind to the Del click handler
						lastTd.find(".dupBtn").on( 'click', dupBtnHdlr ); // bind to the Dup click handler
						lastTd.find(".validBtn").on( 'click', validBtnHdlr ); // bind to the Valid click handler
						alert( alertMsg );
						return;							
					case 'errCount':
					case 'dupCount':
						errX = ( X == 'errCount' ) ? 'errRec' : 'dupRec';
						if ( !noDropdown && (_tblName == 'W001A_4' || _tblName == 'DaPaiWei') ) {
							thisRow.find("input[data-fldn=W_Requestor]").val( rNameO );
						}
						alert ( rspV[ errX ] );
						break;
				} // switch on X
			} // for loop
		}, // End of Success Handler
		error: function (jqXHR, textStatus, errorThrown) {
			alert( "insBtnHdlr()\tError Status:\t"+textStatus+"\t\tMessage:\t\t"+errorThrown+"\n" );
		} // End of ERROR Handler							
	}); // AJAX CALL
} // insBtnHdlr()
 
/**********************************************************
 * Event Handler - When a Duplicate Button is clicked     *
 **********************************************************/
function dupBtnHdlr() {
	var thisRow = $(this).closest("tr");
	var newRow = thisRow.clone();
	var notAllowedTxt = ( _sessLang == SESS_LANG_CHN ) ? "不能複製尚未保存更動的牌位項目！"
													   : "Cannot duplicate an entry with unsaved changes!";
	var insBtnText = ( _sessLang == SESS_LANG_CHN ) ? "加入" : "Insert";
	var insBtn = '<input class="insBtn" type="button" value="' + insBtnText + '">';
	var dirtyCells = thisRow.find("input[data-changed=true]").length;

	if ( dirtyCells > 0 ) { alert( notAllowedTxt ); return; }

	newRow.find("*").unbind();
	newRow.prop( 'id', '' ); // no tuple key value
	newRow.find("input[type=text]").prop( 'disabled', false ).on( 'blur', dataChgHdlr );
	newRow.find("input[type=text]").attr({ 'data-oldv' : '', 'data-pmptv' : '', 'data-changed' : 'true' });
	newRow.find("td:last").html( insBtn );
	newRow.find("td:last .insBtn").on( 'click', insBtnHdlr );
	thisRow.after( newRow );
} // dupBtnHdlr()

/**********************************************************
 * Event Handler - When a Delete Button is clicked        *
 **********************************************************/
function delBtnHdlr() {
	var tblFlds = {};
	var delAlert = ( _sessLang == SESS_LANG_CHN ) ? '刪除的資料將無法恢復，請確認！'
												  : 'A deleted row cannot be undone, please confirm！';
																								
	if ( !confirm( delAlert ) ) return;
																							
	_ajaxData = {}; _dbInfo = {};
	thisRow = $(this).closest("tr");
	tblFlds [ thisRow.attr("data-keyn") ] = thisRow.attr("id");
	_dbInfo[ 'tblName' ] = _tblName;
	_dbInfo[ 'tblFlds' ] = tblFlds;
	_dbInfo[ 'pwRqstr' ] = ( _icoName != null ) ? _icoName : _sessUsr;
	_ajaxData [ 'dbReq' ] = 'dbDEL';
	_ajaxData [ 'dbInfo' ] = JSON.stringify ( _dbInfo );
	$.ajax({
		url: "./ajax-pwDB.php",
		method: 'POST',
		data:	_ajaxData,
		success: function( rsp ) { // Success Handler
			var rspV = JSON.parse ( rsp );
			for ( var X in rspV ) {
				switch ( X ) {
				case 'URL':
					location.replace( rspV[ X ] );
					return;
				case 'delSUCCESS':
					alert( rspV [ X ] );
					thisRow.remove();
					return;
				case 'errCount':
					x = rspV [ X ];
					eMSG = '';
					for ( i=0; i < x; i++ ) {
						eMSG += rspV [ 'errRec' ][i] + "\n";
					}
					alert( eMSG );
					return;
				} // switch()
			} // for loop
		}, // End of Success Handler
		error: function (jqXHR, textStatus, errorThrown) {
			alert( "delBtnHdlr()\tError Status:\t"+textStatus+"\t\tMessage:\t\t"+errorThrown+"\n" );
		} // End of ERROR Handler	
	});	// AJAX Call
} // delBtnHdlr()

/**********************************************************
 * Event Handler - When an Edit Button is clicked         *
 **********************************************************/
function editBtnHdlr() {
	var cells = $(this).closest("tr").find("input[type=text]");
	cells.prop( "disabled", false );
	cells.on( 'blur', dataChgHdlr );
	chgEdit2Upd( $(this) ); // change myself to become an 'Update' button
} // editBtnHdlr()

/**********************************************************
 * Event Handler - When a Cancel Edit Button is clicked   *
 **********************************************************/
function canBtnHdlr() {
	var cells = $(this).closest("tr").find("input[data-changed=true]");
	if ( cells.length > 0 ) {
		cells.each( function () { // Restore the old value
			$(this).val( $(this).attr( "data-oldv" ) );
			$(this).attr( "data-changed", "false" );
		}); // forEach
	}
	$(this).closest("tr").find("input[type=text]").prop( "disabled", true );
	chgUpd2Edit( $(this).siblings(".updBtn") );
} // canBtnHdlr()

/**********************************************************
 * Event Handler - When an Update Button is clicked       *
 **********************************************************/
function updBtnHdlr() {
	var tblFlds = {};
	var updBtn = $(this);
	var thisRow = $(this).closest("tr");
	var cellsChanged = thisRow.find("input[data-changed=true]");

	_ajaxData = {}; _dbInfo = {};	
	if ( cellsChanged.length == 0 ) {
		thisRow.find("input[type=text]").prop( "disabled", true ); // disable Edit
		thisRow.find("input[type=text]").unbind();
		chgUpd2Edit( updBtn );
		return;
	}

	tblFlds [ thisRow.attr("data-keyn") ] = thisRow.attr("id");
	cellsChanged.each( function(i) { // get changed field name and value
		tblFlds [ $(this).attr("data-fldn") ] = $(this).val();
	}); // each
	_dbInfo[ 'tblName' ] = _tblName;
	_dbInfo[ 'tblFlds' ] = tblFlds;
	_dbInfo[ 'pwRqstr' ] = ( _icoName != null ) ? _icoName : _sessUsr;  // actually not used by the DB function)
	_ajaxData[ 'dbReq' ] = 'dbUPD';
	_ajaxData[ 'dbInfo' ] = JSON.stringify ( _dbInfo );
	$.ajax({
		url: "./ajax-pwDB.php",
		method: 'POST',
		data: _ajaxData,
		success: function( rsp ) { // Success Handler 
			var rspV = JSON.parse ( rsp );
			for ( var X in rspV ) {
				switch ( X ) {
					case 'URL':
						location.replace( rspV[ X ] );
						return;
					case 'updSUCCESS':
						var alertMsg = ( _sessLang == SESS_LANG_CHN ) ? "牌位資料更新完畢！" : "Record Updated!";
						cellsChanged.each(function(i) {
							$(this).attr( "data-oldv", $(this).val() ); // remember the current value
						}); // cellsChanged						
						updBtn.parent().find(".validBtn").prop( "disabled", true ); // disable Valid Button
						alert( alertMsg );
						break;
					case 'errCount':
						var alertMsg = ( _sessLang == SESS_LANG_CHN ) ? "牌位資料更新失敗！" : "Update Failed!";
						cellsChanged.each(function(i) {
							$(this).val( $(this).attr( "data-oldv" ) ); // restore its old value
						}); // cellsChanged
						alert( alertMsg + rspV[ 'errRec' ] );
						break;
				} // switch
			} // for loop
			cellsChanged.attr("data-changed", "false");
			thisRow.find("input[type=text]").prop( "disabled", true ); // disable edit
			thisRow.find("input[type=text]").unbind();
			chgUpd2Edit( updBtn );
			return;					
		}, // End of Success Handler
		error: function (jqXHR, textStatus, errorThrown) {
			cellsChanged.each(function(i) {
				$(this).val( $(this).attr( "data-oldv" ) ); // restore its old value
				$(this).attr( "data-changed", "false" );
			});
			thisRow.find("input[type=text]").prop( "disabled", true );
			thisRow.find("input[type=text]").unbind();
			chgUpd2Edit( updBtn );
			alert( "updBtnHdlr()\tError Status:\t"+textStatus+"\t\tMessage:\t\t"+errorThrown+"\n" );
		} // End of ERROR Handler					
	}); // AJAX Call
} // updBtnHdlr()

/**********************************************************
 * Event Handler - When an Valid Button is clicked       *
 **********************************************************/
function validBtnHdlr() {	
	var errText = ( _sessLang == SESS_LANG_CHN ) ? 
								"往生日期（年-月-日）必須屆於 " + _pwPlqDate + "(含) 及 " + _rtrtDate + "(不含) 之間。"  
							: "Deceased Date must be between " + _pwPlqDate + " and " + _rtrtDate + " in YYYY-MM-DD format";

	var tblFlds = {};
	var validBtn = $(this);
	var thisRow = $(this).closest("tr");
	_ajaxData = {}; _dbInfo = {};

	// DaPaiWei and checking deceased Date (within 12 months)
	if (_tblName == "DaPaiWei") {		
		var deceasedDate = thisRow.find("input[data-fldn='deceasedDate']").val();		
		if ( !chkDate( deceasedDate ) ) {
			alert( errText );			
			return;			
		}
	}

	tblFlds [ thisRow.attr("data-keyn") ] = thisRow.attr("id");
	_dbInfo[ 'tblName' ] = _tblName;
	_dbInfo[ 'tblFlds' ] = tblFlds;
	_dbInfo[ 'pwRqstr' ] = ( _icoName != null ) ? _icoName : _sessUsr;  // actually not used by the DB function)
	_ajaxData[ 'dbReq' ] = 'dbUPD';
	_ajaxData[ 'dbInfo' ] = JSON.stringify ( _dbInfo );
	$.ajax({
		url: "./ajax-pwDB.php",
		method: 'POST',
		data: _ajaxData,
		success: function( rsp ) { // Success Handler 
			var rspV = JSON.parse ( rsp );
			for ( var X in rspV ) {
				switch ( X ) {
					case 'URL':
						location.replace( rspV[ X ] );
						return;
					case 'updSUCCESS':
						var alertMsg = ( _sessLang == SESS_LANG_CHN ) ? "牌位資料驗證完畢！" : "Record Validated!";		
						validBtn.prop( "disabled", true ); // disable Valid Button
						alert( alertMsg );
						break;
					case 'errCount':
						var alertMsg = ( _sessLang == SESS_LANG_CHN ) ? "牌位資料驗證失敗！" : "Validation Failed!";						
						alert( alertMsg + rspV[ 'errRec' ] );
						break;
				} // switch
			} // for loop			
			return;					
		}, // End of Success Handler
		error: function (jqXHR, textStatus, errorThrown) {
			alert( "validBtnHdlr()\tError Status:\t"+textStatus+"\t\tMessage:\t\t"+errorThrown+"\n" );
		} // End of ERROR Handler					
	}); // AJAX Call
} // validBtnHdlr()

/**********************************************************
 * Event Handler - When a cell data is changed            *
 **********************************************************/
function dataChgHdlr() {	// on 'blur' handler
	var newV = $(this).val().trim().replace( /<br>$/gm, '');
	var oldV = $(this).attr("data-oldv").trim();
	var emptyText = ( _sessLang == SESS_LANG_CHN ) ? "該項牌位資料不應空白！" : "This field shall not be empty!";
	var errText = ( _sessLang == SESS_LANG_CHN ) ? 
								"往生日期（年-月-日）必須屆於 " + _pwPlqDate + "(含) 及 " + _rtrtDate + "(不含) 之間。"  
							: "Deceased Date must be between " + _pwPlqDate + " and " + _rtrtDate + " in YYYY-MM-DD format";
	var fldN = $(this).attr("data-fldn");
	
	if ( newV.length == 0 && ( fldN == 'W_Title' || fldN == 'R_Title' ) ) { // Blank is allowed
		$(this).val( _blankData ); // blank filler
		newV = $(this).val();
	}
	// Allowed blank fields have been taken care of - now the regular logic follows
	if ( newV.length == 0 ) {
		if ( oldV.length > 0 ) { // existing data editing
			alert( emptyText );
			$(this).val( oldV );
		} else {	// new row data entry; user did not input anything
			$(this).val( $(this).attr("data-pmptv").trim() );
			$(this).attr( 'data-pmptv', '');
		}
		return;
	}
	if ( fldN == 'deceasedDate' ) {
		// convert MM/DD/YYYY to YYYY-MM-DD        
        if( newV.match( /^(0?[1-9]|1[012])[\/\/](0?[1-9]|[12][0-9]|3[01])[\/\/]\d{4}$/) ) { // match MM/DD/YYYY
            var d = newV.split( /[\/\/]/ ); // d[0]: MM; d[1]: DD; d[2]: YYYY
            newV = d[2].concat("-", d[0], "-", d[1]); // YYYY-MM-DD
		}
		
		if ( !chkDate( newV ) ) {
			alert( errText );
			if ( oldV.length > 0 ) {
				$(this).val( oldV );
			} else {	// new row data entry; user did not input anything
				$(this).val( $(this).attr( 'data-pmptv' ).trim() );
				$(this).attr( 'data-pmptV', '');
			}
			return;			
		}
	} // DaPaiWei and checking deceased Date

	$(this).val( newV );
	if ( newV != oldV ) {
		$(this).attr("data-changed", "true");
	}	
} // dataChgHdlr()

/**********************************************************
 * Event Handler - When a data cell gets focused          *
 **********************************************************/
function onFocusHdlr() {	// on 'focus' handler
	var newV = $(this).val().trim().replace( /<br>$/gm, '');
	var pmptV = $(this).attr("data-pmptv").trim().replace( /<br>$/gm, '');
	if ( pmptV.length > 0 ) return; // Already done once; user has input data & comes back to it
	$(this).attr( 'data-pmptv', newV ); // save it before blanking out
	$(this).val( '' ); // blank out the field for input
	return;
} // function onFocusHdlr()

/**********************************************************
 * Event Handler - When mouse hover a TITLE dropdown      *
                     selection list                       *
 **********************************************************/
function onMouseoverHdlr() {	// on 'mouseover' handler
	setTimeout(function(){ $(document).tooltip('disable');}, 10000);
} // function onMouseoverHdlr()

/**********************************************************
 * Binders                                                *
 **********************************************************/
function ready_edit() {
	if ( _delBtns != null ) _delBtns.unbind(); // unbind the old ones
	if ( _editBtns != null ) _editBtns.unbind();
	if ( _dupBtns != null ) _dupBtns.unbind();
	if ( _addRowBtn != null ) _addRowBtn.unbind();
	if ( _srchBtn != null ) _srchBtn.unbind();
	if ( _delAllBtn != null ) _delAllBtn.unbind();
	if ( _validBtn != null ) _validBtn.unbind();
	if ( _validAllBtn != null ) _validAllBtn.unbind();


	_delBtns = $(".delBtn");
	_editBtns = $(".editBtn");
	_dupBtns = $(".dupBtn");
	_addRowBtn = $("#addRowBtn");
	_srchBtn = $("#srchBtn");
	_delAllBtn = $("#delAllBtn");
	_validBtn = $(".validBtn");
	_validAllBtn = $("#validAllBtn");

	_delBtns.on( 'click', delBtnHdlr );
	_editBtns.on( 'click', editBtnHdlr );
	_dupBtns.on( 'click', dupBtnHdlr );
	_addRowBtn.on( 'click', addRowBtnHdlr );
	_srchBtn.on( 'click', srchBtnHdlr );
	_delAllBtn.on( 'click', delAllBtnHdlr );
	_validBtn.on( 'click', validBtnHdlr );
	_validAllBtn.on( 'click', validAllBtnHdlr );

	// disable ValidAll button for DaPaiWei and DaPaiWei_Red
	if ( _tblName == "DaPaiWei" || _tblName == "DaPaiWeiRed" ) {
		_validAllBtn.prop( "disabled", true );
	}
} // ready_edit()

/**********************************************************
 * Event Handler - When the Upload Request is clicked     *
 **********************************************************/
function upldHdlr () { // load the upload form and bind it to the form submit handler
	$("#tabDataFrame").load("./upldPaiWeiForm.php #forUpld", function( rsp ) {
		if ( isJSON( rsp ) ) {
			rspV = JSON.parse( rsp );
			location.replace( rspV['URL']);
			return;
		}
		$("form#upldForm").unbind(); // in case it was bound before
		$("form#upldForm").on( 'submit', myPaiWeiUpLoad );		
	});
	return false; // so, the hyperlink won't fire
} // upldHdlr()

/**********************************************************
 * Event Handler - When the User Guide is requested       *
 **********************************************************/
function ugLoader () { // load the PaiWei User Guide
	$("#tabDataFrame").load("./UG.php #ugDesc", function ( rsp ) {
		$(this).find("table").addClass("UGsteps");
		$(this).find("tr").css("background-color", "transparent");
		$(this).find("img").addClass("UGstepImg");
		return false; // so the link won't fire
	});
	$("#tabDataFrame").css("overflow-y", "auto");
	return false; // so the link won't fire
} // ugLoader()

function hdlr_tabClick() {
	// unsaved data
	var dirtyCells = $("tbody input[type=text][data-changed=true]").length + $("#retreatUpd select[data-changed=true]").length;
	if ( ( dirtyCells > 0 ) && ( !confirm( _alertUnsaved ) ) ) return;

	var rqTblName = $(this).attr("data-tbl");
	if ( rqTblName == _tblName ) return; /* nothing to do */
	_tblName = rqTblName;

	$(".tabMenu th").removeClass("active").css("border", "1px solid white");
	$(this).addClass("active").css("border-bottom", "1px solid green");
	$("#tabDataFrame").find("*").unbind();
	$("#tabDataFrame").empty();

	switch ( _tblName ) {
		case 'C001A':
		case 'W001A_4':
		case 'DaPaiWei':
		case 'DaPaiWeiRed':
		case 'L001A':
		case 'Y001A':
		case 'D001A':
			loadTblData( _tblName, 1, 30, ( ( _icoName != null ) ? _icoName : _sessUsr ), "tabDataFrame" );
			enableTooltip(); // show hover message
			break;	
		case 'ug':
			ugLoader();
			break;
		case 'upld':
			upldHdlr();
			break;
		case 'RtData':
			loadRtMgrForm();
			break;
		case 'DnldJiWen':
			$("#tabDataFrame").load("./dnldJiWenForm.php #forDnld");
			break;
		case 'DnldPaiWei':
			$("#tabDataFrame").load("./dnldPaiWeiForm.php #forDnld", function() {
				$("#dnldCSVBtn").on( 'click', dnldCSVBtnHdlr );
				$("#dnldPDFBtn").on( 'click', dnldPDFBtnHdlr );
			});
			break; 		
	} // switch()
} // function tabClick()

/**********************************************************
 * Enable Tooltip to Show Hover Message                   *
 **********************************************************/
function enableTooltip() {
	var hoverMsg = ( _sessLang == SESS_LANG_CHN ) ? "如果選擇列表中沒有，選擇任何稱謂然後用“更改”的方式去更正！" : "If not found in the dropdown selection list, select any and then use Edit to correct it!";
	$(document).tooltip({content: hoverMsg});
	$(document).tooltip("enable");
} // enableTooltip()