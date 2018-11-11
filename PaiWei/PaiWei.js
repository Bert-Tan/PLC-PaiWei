/**********************************************************
 * Global variables																				*
 **********************************************************/
var SESS_LANG_CHN = 1;	// These variables are used as CONSTANTS
var SESS_MODE_EDIT = 0;
var SESS_MODE_SRCH = 1;

var _sessUsr = null, _sessPass = null, _sessType = null, _sessLang = null;
var _sessMode = SESS_MODE_EDIT; // default
var _dbInfo = {}, _ajaxData = {};
var _tblName = null, _tblSize = 0;
var _pilotDataRow = null;	// to be used for adding rows
var _pwPlqDate = null;
var _rtrtDate = null;

var _delBtns = null;
var _editBtns = null;
var _addRowBtn = null;
var _srchBtn = null;
var _alertUnsaved = null;
var _blankData = "BLANK"; // blank data filler for W_Title & R_Title fields; they can be blank

/**********************************************************
 * Support functions																			*
 **********************************************************/
function readCookie(name) {
  var nameEQ = name + "=";
  var ca = document.cookie.split(';');
  for(var i=0;i < ca.length;i++) {
    var c = ca[i];
    while (c.charAt(0)==' ') c = c.substring(1,c.length);
    if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length);
  }
  return null;
} // readCookie()

function readSessParam() {
	_sessUsr = readCookie( 'usrName' );
	_sessPass = readCookie( 'usrPass' );
	_sessType = readCookie( 'sessType' );
	_sessLang = readCookie( 'sessLang' );
	
	if ( _sessUsr == null ) return false;
	
	_sessUsr = decodeURI( _sessUsr );
	_sessPass = decodeURI( _sessPass );
	_sessType = decodeURI( _sessType );
	_sessLang = decodeURI( _sessLang );
	return true;
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
	var updBtnVal = ( _sessLang == SESS_LANG_CHN ) ? "保存更動" : "Update";
	var canBtnVal = ( _sessLang == SESS_LANG_CHN ) ? "取消更動" : "Cancel";
	var canBtn = editBtn.clone();
	
	editBtn.unbind(); // unbind myself from the Edit Button Handler
	editBtn.attr( "value", updBtnVal ); // change myself to become an 'Update' button
	editBtn.removeClass( 'editBtn' ).addClass( 'updBtn' ); // change my class
	editBtn.on( 'click', updBtnHdlr );
	canBtn.attr( "value", canBtnVal );
	canBtn.removeClass( 'editBtn' ).addClass( 'canBtn' );
	canBtn.on( 'click', canBtnHdlr )
	editBtn.after( canBtn );
	editBtn.siblings(".canBtn").before( "&nbsp;&nbsp;&nbsp;" ); // space it
} // chgEdit2Upd()

function chgUpd2Edit ( updBtn ) {
	var editBtnVal = ( _sessLang == SESS_LANG_CHN ) ? "更改" : "Edit";
	updBtn.unbind();
	updBtn.attr( "value", editBtnVal ); // change my name to 'Edit' button
	updBtn.removeClass( 'updBtn' ).addClass( 'editBtn' );
	updBtn.on( 'click', editBtnHdlr );
	updBtn.siblings(".canBtn").unbind().remove(); // unbind, remove the Cancel Button
	updBtn.get(0).nextSibling.remove(); // remove the inserted blank space
} // chgUpd2Edit()

function isJSON( str ) {
    try {
        JSON.parse(str);
    } catch (e) {
        return false;
    }
    return true;
} // isJSON()

function loadTblData( tblName, pgNbr, numRec, sessUsr ) {	/* dataOnly parameter is eliminated */
	// before introducing page-by-page surfing, the dataonly parameter isn't really needed
	var dataArea = $(".dataArea");
	var tblHdrWrapper =	'<div id="myHdrWrapper"></div>';
	var tblDataWrapper = '<div id="myDataWrapper"></div>';
	var errText = ( _sessLang == SESS_LANG_CHN ) ? '沒有找到所選擇的法會的牌位，請輸入或上載牌位資料。'
																							 : 'No record found! Please input or upload Data';
	var errMsg =	'<H1 class="centerMe errMsg">' + errText + '</h1>';

	_ajaxData = {}; _dbInfo = {};

  dataArea.empty();
  dataArea.append( tblHdrWrapper , tblDataWrapper );
  	
	_dbInfo[ 'tblName' ] = tblName;
	_dbInfo[ 'pgNbr' ] = pgNbr;
/*	_dbInfo[ 'numRec' ] = numRec; */
	_dbInfo[ 'pwRqstr' ] = sessUsr;
/*	_dbInfo[ 'inclHdr' ] = !dataOnly; */
	_ajaxData[ 'dbReq' ] = 'dbREAD';
	_ajaxData[ 'dbInfo' ] = JSON.stringify ( _dbInfo );
	$.ajax({
		url: "./ajax-pwDB.php",
		method: 'POST',
		data:	_ajaxData,
		success: function ( rsp ) { // SUCCESS handler
			var rspV = JSON.parse( rsp );
			for ( var X in rspV ) {
				switch( X ) {
					case 'URL':
						location.replace( rspV [ X ] );
						return;
					case 'myDataHdr':
						$("#myHdrWrapper").find("*").unbind();
						$("#myHdraWrapper").empty();
						$("#myHdrWrapper").html( rspV[ X ] );
						break;
					case 'myData':
						$("#myDataWrapper").find("*").unbind();
						$("#myDataWrapper").empty();
						$("#myDataWrapper").html( rspV[ X ] );
						break;
					case 'myDataSize':
						_tblSize = rspV[ X ];
						break;
				} // switch()
			} // for loop
			_pilotDataRow = $("#myData tbody > tr:first").clone();
			if ( _tblSize == 0 ) {
				$("#myDataWrapper").find("*").unbind();
				$("#myDataWrapper").find("tr").remove();
				$("#myDataWrapper").append( errMsg );
				if ( _sessLang != SESS_LANG_CHN ) {
					$("#myDataWrapper").find("H1").css( "letter-spacing", "normal");
				}
			}
			_sessMode = SESS_MODE_EDIT;
			ready_edit();
		}, // End of SUCCESS Handler
		
		error: function (jqXHR, textStatus, errorThrown) {
			alert( "Line 174\tError Status:\t"+textStatus+"\t\tMessage:\t\t"+errorThrown+"\n" );
		} // End of ERROR Handler
	}); // ajax call	
} // loadTblData()

/**********************************************************
 * Event Handler	- When a Pai Wei menu item is clicked		*
 **********************************************************/
function pwTblHdlr() {
	var dirtyCells = $("tbody span[data-changed=true]").length;

	$(".errMsg").remove();
	_tblName = $(this).attr("data-tbl");	

	if ( ( dirtyCells > 0 ) && ( !confirm( _alertUnsaved ) ) ) return;
	
	$(".pwTbl").removeClass("active");
	$(this).addClass("active");
	
	loadTblData( _tblName, 1, 30, _sessUsr, false );
	
	return;	
} // function pwTblHdlr()

/********************************************************************************
 * Event Handler when the PaiWei Upload Form is submitted												*
 ********************************************************************************/
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
			alert( "Line 217\tError Status:\t"+textStatus+"\t\tMessage:\t\t"+errorThrown+"\n" );
		} // End of ERROR Handler		
	}); // AJAX Call
} // myPaiWeiUpLoad()

/**********************************************************
 * Event Handler	- When the Upload Request is clicked		*
 **********************************************************/
function upldHdlr () { // load the upload form and bind it to the form submit handler
	$(".dataArea").load("./upldPaiWeiForm.php #forUpld", function( rsp ) {
		if ( isJSON( rsp ) ) {
			rspV = JSON.parse( rsp );
			location.replace( rspV['URL']);
			return;
		}
		$("form#upldForm").unbind(); // in case it was bound before
		$("form#upldForm").on( 'submit', myPaiWeiUpLoad );

		$(this).find(".future").on( 'click', futureAlert );
		$(this).find(".soon").on( 'click', soonAlert );
	});
	return false; // so, the hyperlink won't fire
} // upldHdlr()
 
/**********************************************************
 * Event Handler	- When the Add_a_Row Button is clicked	*
 **********************************************************/
function addRowBtnHdlr() {
	var dirtyCells = $("tbody span[data-changed=true]").length;
	var insBtnText = ( _sessLang == SESS_LANG_CHN ) ? "加入" : "Insert";
	var insBtn = '<input class="insBtn" type="button" value="' + insBtnText + '">';
	var tbody = $("#myData tbody");
	var newRow = _pilotDataRow.clone();
	var newRowDataCells = newRow.find("span");
	var lastTd = newRow.find("td:last");
	var cellText = ( _sessLang == SESS_LANG_CHN ) ? "請輸入牌位資料" : "Please Enter Name Plaque Text";
	var	dateText = ( _sessLang == SESS_LANG_CHN ) ? "請輸入 年-月-日" : "Please Enter YYYY-MM-DD";
	
	$(".errMsg").remove();
	if ( ( dirtyCells > 0 ) && ( !confirm( _alertUnsaved ) ) ) return;
	
	newRow.attr( "data-keyN", _pilotDataRow.attr("data-keyN") ); // copy the Key Name
	newRow.attr( "id", '' ) ; // no tuple Key value 
	newRowDataCells.text( cellText );
	newRowDataCells.attr( { 'contenteditable' : 'true', 'data-oldV' : '', 'data-pmptV' : '' } );
	if ( _tblName == 'DaPaiWei' ) {
		newRow.find("span[data-fldN='deceasedDate']").text( dateText );
	}
	newRowDataCells.on( 'blur', dataChgHdlr ); // bind the <td><span> to data change handler
	newRowDataCells.on( 'focus', onFocusHdlr ); // bind the <td><span> to on 'focus' handler
	lastTd.html( insBtn ); // place the 'Insert' button
	lastTd.find("input[type=button]").on( 'click', insBtnHdlr ); // bind to Insert Button click handler
	
	if ( _sessMode == SESS_MODE_SRCH ) {
		$("#myDataWrapper").find("*").unbind()
		tbody.find("tr").remove(); // remove all data rows
		$("#myDataFooter").remove(); // remove the footer
		_sessMode = SESS_MODE_EDIT;
	}

	tbody.append( newRow );
} // addRowBtnHdlr()

/**********************************************************
 * Event Handler	- When the Lookup Button is clicked			*
 **********************************************************/
function lookupBtnHdlr() {
	var inputSrchData = ( _sessLang == SESS_LANG_CHN ) ? '請請輸入查詢資料！' : 'Please enter search pattern!';
	var notFoundText = ( _sessLang == SESS_LANG_CHN ) ? '沒有找到所要找的牌位，請輸入或上載牌位資料。'
																									: 'No record found! Please Input or Upload Data.';
	var notFoundMSG = '<h1 class="centerMe errMsg">' + notFoundText + '</h1>';
	var tblFlds = {};
	var thisRow = $(this).closest("tr");
	var cellsChanged = thisRow.find("span[data-changed=true]");

	_ajaxData = {}; _dbInfo = {};
	if ( cellsChanged.length == 0 ) {
		alert( inputSrchData );
		return;
	}
	
	cellsChanged.each(function(i) {
		tblFlds [ $(this).attr("data-fldN") ] = $(this).text();
	});
	_dbInfo[ 'tblName' ] = _tblName;
	_dbInfo[ 'tblFlds' ] = tblFlds;
	_dbInfo[ 'pwRqstr' ] = _sessUsr;
	_ajaxData[ 'dbReq' ] = 'dbSEARCH';
	_ajaxData[ 'dbInfo' ] = JSON.stringify ( _dbInfo );
	_sessMode = SESS_MODE_EDIT; // Search Mode is over; regardless of the search result
	$("#myDataWrapper").find("*").unbind(); // done with the current data
	$("#myDataWrapper").empty();
	$.ajax({
		url: "./ajax-pwDB.php",
		method: 'POST',
		data: _ajaxData,
		success: function ( rsp ) {
			rspV = JSON.parse ( rsp );
			for ( var X in rspV ) {
				switch ( X ) {
					case 'URL':
						location.replace( rspV[ X ] );
						return;
					case 'myData': // The Server returns a data table
						$("#myDataWrapper").find("*").unbind();
						$("#myDataWrapper").html( rspV[ X ] );
						_pilotDataRow = $("#myData tbody > tr:first").clone();
						break;
					case 'myDataSize':
						_tblSize = rspV [ X ];
						if ( _tblSize == 0 ) { // an empty row was received for the _pilotDataRow; now remove it
							$("#myDataWrapper").find("tr").remove();
							$("#myDataWrapper").append( notFoundMSG );
							if ( _sessLang != SESS_LANG_CHN ) {
								$("#myDataWrapper").find("H1").css( "letter-spacing", "normal");
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
						$("#myDataWrapper").append( errMSG ); // reset to default					
						break;
				} // switch()
			} // for loop
		}, // success handler
		error: function (jqXHR, textStatus, errorThrown) {
			alert( "Line 344\tError Status:\t"+textStatus+"\t\tMessage:\t\t"+errorThrown+"\n" );
		} // End of ERROR Handler							
	}); // AJAX CALL
} // lookupBtnHdlr() 
 
/**********************************************************
 * Event Handler	- When the Search Button is clicked			*
 **********************************************************/
function srchBtnHdlr() {
	var dirtyCells = $("tbody span[data-changed=true]").length;
	var lookupBtnText = ( _sessLang == SESS_LANG_CHN ) ? "查詢" : "Look Up";
	var lookupBtn = '<input class="lookupBtn" type="button" value="' + lookupBtnText + '">';
	var tbody = $("#myData tbody"); 
	var newRow = _pilotDataRow.clone();
	var newRowDataCells = newRow.find("span");
	var lastTd = newRow.find("td:last");
	var cellText = ( _sessLang == SESS_LANG_CHN ) ? "請輸入查詢資料" : "Please Enter Look Up Text";
	var	dateText = ( _sessLang == SESS_LANG_CHN ) ? "請輸入 年-月-日" : "Please Enter YYYY-MM-DD";
	
	$(".errMsg").remove();
	if ( _sessMode == SESS_MODE_SRCH ) {
		var alert_txt = ( _sessLang == SESS_LANG_CHN ) ? "已經在搜索狀態!" : "Already in Search Mode!";
		alert( alert_txt );
		return;
	}
	if ( ( dirtyCells > 0 ) && ( !confirm( _alertUnsaved ) ) ) return;

	newRow.attr( "data-keyN", _pilotDataRow.attr("data-keyN") ); // copy the Key Name
	newRow.attr( "id", '' ) ; // no tuple Key value 
	newRowDataCells.text( cellText );
	newRowDataCells.attr( { 'contenteditable' : 'true', 'data-oldV' : '', 'data-pmptV' : '' } );
	if ( _tblName == 'DaPaiWei' ) {
		newRow.find("span[data-fldN='deceasedDate']").text( dateText );
	}
	newRowDataCells.on( 'blur', dataChgHdlr ); // bind the <td><span> to data change handler
	newRowDataCells.on( 'focus', onFocusHdlr ); // bind the <td><span> to on 'focus' handler
	lastTd.html( lookupBtn ); // place the 'Lookup' button
	lastTd.find("input[type=button]").on( 'click', lookupBtnHdlr ); // bind to Lookup Button click handler
	tbody.find("*").unbind();	
	tbody.find("tr").remove(); // remove all data rows
	$("#myDataFooter").remove(); // remove the footer
	tbody.append( newRow );
	_sessMode = SESS_MODE_SRCH;
} // srchBtnHdlr()

/**********************************************************
 * Event Handler	- When an Insert Button is clicked			*
 **********************************************************/
function insBtnHdlr() {
	var insBtn = $(this);
	var editBtnText = ( _sessLang == SESS_LANG_CHN ) ? '更改' : 'Edit';
	var delBtnText = ( _sessLang == SESS_LANG_CHN ) ? '刪除' : 'Delete';
	var alertText = ( _sessLang == SESS_LANG_CHN ) ? "請輸入完整的牌位資料" : "Please enter complete plaque data";
	var myEditBtns = '<input class="editBtn" type="button" value="' + editBtnText + '">&nbsp;&nbsp;' +
									'<input class="delBtn" type="button" value="' + delBtnText + '">';
	var thisRow = $(this).closest("tr");
	var cellsChanged = thisRow.find("span[data-changed=true]");
	var tblFlds = {};

	if ( cellsChanged.length != thisRow.find("span").length ) { // incomplete data input
		alert( alertText );
		return;
	}
	
	_ajaxData = {}; _dbInfo = {};	
	if ( cellsChanged.length == 0 ) return;
	cellsChanged.each(function(i) { // (name, value) pair
		tblFlds [ $(this).attr("data-fldN") ] = $(this).text();
	});
	_dbInfo[ 'tblName' ] = _tblName;
	_dbInfo[ 'tblFlds' ] = tblFlds;
	_dbInfo[ 'pwRqstr' ] = _sessUsr;
	_ajaxData[ 'dbReq' ] = 'dbINS';
	_ajaxData[ 'dbInfo' ] = JSON.stringify ( _dbInfo );
	$.ajax({
		url: "./ajax-pwDB.php",
		method: 'POST',
		data: _ajaxData,
		success: function( rsp ) {
			rspV = JSON.parse ( rsp );
			for ( var X in rspV ) {
				switch ( X ) {
					case 'URL':
						location.replace( rspV[ X ] );
						return;
					case 'insSUCCESS': // rspV[X] holds the tupID 
						thisRow.attr("data-keyN", 'ID' ); thisRow.attr( 'id', rspV[ X ] );
						thisRow.find("span").attr( "contenteditable", "false" ); // disable edit
						thisRow.find("span").unbind( 'focus' );
						thisRow.find("span").removeAttr('data-pmptV');
						cellsChanged.each(function(i) {
							$(this).attr( "data-oldV", $(this).text() ); // remember the current value
							$(this).attr( "data-changed", "false" );
						}); // each
						lastTd = thisRow.find("td:last"); insBtn.unbind(); insBtn.remove();
						lastTd.html( myEditBtns ); // change to edit & delete buttons
						lastTd.find(".editBtn").on( 'click', editBtnHdlr ); // bind to the edit click handler
						lastTd.find(".delBtn").on( 'click', delBtnHdlr ); // bind to the edit click handler
						alert( "Record Inserted!" );
						return;							
					case 'errCount':
						alert ( rspV [ 'errRec' ] );
						break;
					case 'dupCount':
						alert ( rspV [ 'dupRec' ] );
						break;
				} // switch
			} // for loop
		}, // End of Success Handler
		error: function (jqXHR, textStatus, errorThrown) {
			alert( "Line 447\tError Status:\t"+textStatus+"\t\tMessage:\t\t"+errorThrown+"\n" );
		} // End of ERROR Handler							
	}); // AJAX CALL
} // insBtnHdlr()
 
/**********************************************************
 * Event Handler	- When a Delete Button is clicked				*
 **********************************************************/
function delBtnHdlr() {
	var tblFlds = {};
	var delAlert = ( _sessLang == SESS_LANG_CHN ) ? '刪除的資料將無法恢復，請確認！'
																								: 'A deleted row cannot be undone, please confirm！';
																								
	if ( !confirm( delAlert ) ) return;
																							
	_ajaxData = {}; _dbInfo = {};
	thisRow = $(this).closest("tr");
	tblFlds [ thisRow.attr("data-keyN") ] = thisRow.attr("id");
	_dbInfo[ 'tblName' ] = _tblName;
	_dbInfo[ 'tblFlds' ] = tblFlds;
	_dbInfo[ 'pwRqstr' ] = _sessUsr;
	_ajaxData [ 'dbReq' ] = 'dbDEL';
	_ajaxData [ 'dbInfo' ] = JSON.stringify ( _dbInfo );
	$.ajax({
		url: "./ajax-pwDB.php",
		method: 'POST',
		data:	_ajaxData,
		success: function( rsp ) { // Success Handler
			rspV = JSON.parse ( rsp );
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
			alert( "Line 493\tError Status:\t"+textStatus+"\t\tMessage:\t\t"+errorThrown+"\n" );
		} // End of ERROR Handler	
	});	// AJAX Call
} // delBtnHdlr()

/**********************************************************
 * Event Handler	- When an Edit Button is clicked				*
 **********************************************************/
function editBtnHdlr() {
	var cells = $(this).closest("tr").find("span");
	cells.attr( "contenteditable", "true" );
	cells.on( 'blur', dataChgHdlr );
	chgEdit2Upd( $(this) ); // change myself to become an 'Update' button
} // editBtnHdlr()

/**********************************************************
 * Event Handler	- When a Cancel Edit Button is clicked				*
 **********************************************************/
function canBtnHdlr() {
	var cells = $(this).closest("tr").find("span[data-changed=true]");
	if ( cells.length > 0 ) {
		cells.each( function () { // Restore the old value
			$(this).text( $(this).attr( "data-oldV" ) );
			$(this).attr( "data-changed", "false" );
		}); // forEach
	}
	$(this).closest("tr").find("span").attr( "contenteditable", "false" );
	chgUpd2Edit( $(this).siblings(".updBtn") );
} // canBtnHdlr()

/**********************************************************
 * Event Handler	- When an Update Button is clicked			*
 **********************************************************/
function updBtnHdlr() {
	var tblFlds = {};
	var updBtn = $(this);
	var thisRow = $(this).closest("tr");
	var cellsChanged = thisRow.find("span[data-changed=true]");

	_ajaxData = {}; _dbInfo = {};	
	if ( cellsChanged.length == 0 ) {
		thisRow.find("span").attr( "contenteditable", "false" ); // disable Edit
		thisRow.find("span").unbind();
		chgUpd2Edit( updBtn );
		return;
	}

	tblFlds [ thisRow.attr("data-keyN") ] = thisRow.attr("id");
	cellsChanged.each( function(i) { // get changed field name and value
		tblFlds [ $(this).attr("data-fldN") ] = $(this).text();
	}); // each
	_dbInfo[ 'tblName' ] = _tblName;
	_dbInfo[ 'tblFlds' ] = tblFlds;
	_dbInfo[ 'pwRqstr' ] = _sessUsr;
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
						cellsChanged.each(function(i) {
							$(this).attr( "data-oldV", $(this).text() ); // remember the current value
						}); // cellsChanged
						alert( 'Record Updated!' );
						break;
					case 'errCount':
						cellsChanged.each(function(i) {
							$(this).text( $(this).attr( "data-oldV" ) ); // restore its old value
						}); // cellsChanged
						alert( "Update Failed:\n" + rspV[ 'errRec' ] );
						break;
				} // switch
			} // for loop
			cellsChanged.attr("data-changed", "false");
			thisRow.find("span").attr( "contenteditable", "false" ); // disable edit
			thisRow.find("span").unbind();
			chgUpd2Edit( updBtn );
			return;					
		}, // End of Success Handler
		error: function (jqXHR, textStatus, errorThrown) {
			cellsChanged.each(function(i) {
				$(this).text( $(this).attr( "data-oldV" ) ); // restore its old value
				$(this).attr( "data-changed", "false" );
			});
			thisRow.find("span").attr( "contenteditable", "false" );
			thisRow.find("span").unbind();
			chgUpd2Edit( updBtn );
			alert( "Line 573\tError Status:\t"+textStatus+"\t\tMessage:\t\t"+errorThrown+"\n" );
		} // End of ERROR Handler					
	}); // AJAX Call
} // updBtnHdlr()

/********************************************************************************
 * Event Handler - When a cell data is changed																	*
 ********************************************************************************/
function dataChgHdlr() {	// on 'blur' handler
	var newV = $(this).text().trim().replace( /<br>$/gm, '');
	var oldV = $(this).attr("data-oldV").trim();
	var emptyText = ( _sessLang == SESS_LANG_CHN ) ? "該項牌位資料不應空白！" : "This field shall not be empty!";
	var errText = ( _sessLang == SESS_LANG_CHN ) ? 
								"往生日期（年-月-日）必須屆於 " + _pwPlqDate + "(含) 及 " + _rtrtDate + "(不含) 之間。"  
							: "Deceased Date must be between " + _pwPlqDate + " and " + _rtrtDate + " in YYYY-MM-DD format";
	var fldN = $(this).attr("data-fldN");
	
	if ( newV.length == 0 && ( fldN == 'W_Title' || fldN == 'R_Title' ) ) { // Blank is allowed
		$(this).text( _blankData ); // blank filler
		newV = newV = $(this).text();
	}
	// Allowed blank fields have been taken care of - now the regular logic follows
	if ( newV.length == 0 ) {
		if ( oldV.length > 0 ) { // existing data editing
			alert( emptyText );
			$(this).text( oldV );
		} else {	// new row data entry; user did not input anything
			$(this).text( $(this).attr("data-pmptV").trim() );
				$(this).attr( 'data-pmptV', '');
		}
		return;
	}
	if ( fldN == 'deceasedDate' ) {
		if ( !chkDate( newV ) ) {
			alert( errText );
			if ( oldV.length > 0 ) {
				$(this).text( oldV );
			} else {	// new row data entry; user did not input anything
				$(this).text( $(this).attr( 'data-pmptV' ).trim() );
				$(this).attr( 'data-pmptV', '');
			}
			return;			
		}
	} // DaPaiWei and checking deceased Date

	$(this).text( newV );
	if ( newV != oldV ) {
		$(this).attr("data-changed", "true");
	}	
} // dataChgHdlr()

/********************************************************************************
 * Event Handler - When a data cell gets focused																*
 ********************************************************************************/
function onFocusHdlr() {	// on 'focus' handler
	var newV = $(this).text().trim().replace( /<br>$/gm, '');
	var pmptV = $(this).attr("data-pmptV").trim().replace( /<br>$/gm, '');
	if ( pmptV.length > 0 ) return; // Already done once; user has input data & comes back to it
	$(this).attr( 'data-pmptV', newV ); // save it before blanking out
	$(this).text( '' ); // blank out the field for input
	return;
} // function onFocusHdlr()

/**********************************************************
 * Binders																								*
 **********************************************************/
function ready_edit() {
	if ( _delBtns != null ) _delBtns.unbind(); // unbind the old ones
	if ( _editBtns != null ) _editBtns.unbind();
	if ( _addRowBtn != null ) _addRowBtn.unbind();
	if ( _srchBtn != null ) _srchBtn.unbind();
		
	_delBtns = $(".delBtn");
	_editBtns = $(".editBtn");
	_addRowBtn = $("#addRowBtn");
	_srchBtn = $("#srchBtn");

	_delBtns.on( 'click', delBtnHdlr );
	_editBtns.on( 'click', editBtnHdlr );
	_addRowBtn.on( 'click', addRowBtnHdlr );
	_srchBtn.on( 'click', srchBtnHdlr );
} // ready_edit()

/**********************************************************
 * Document Ready																					*
 **********************************************************/
$(document).ready(function() {
	if ( readSessParam() ) { // A session is established
		_ajaxData = {}; _dbInfo = {};
		_dbInfo[ 'tblName' ] = "pwParam";
		_ajaxData[ 'dbReq' ] = 'dbREADpwParam';
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
						case 'notActive':	// No retreat active; put out msg
							alertMsg = ( _sessLang == SESS_LANG_CHN ) ? '本念佛堂近期內沒有法會！'
																												: 'Currently, there is NO Planned Retreat!';
							alert ( alertMsg );
							return;
						case 'pwPlqDate':
							_pwPlqDate = rspV[ X ]; // alert ( _pwPlqDate );
							_rtrtDate = rspV[ 'rtrtDate' ];
							$("th.pwTbl").on( 'click', pwTblHdlr ); // bind Pai Wei menu items to the click handler
							$("#upld").on( 'click', upldHdlr ); // bind upload anchor to its handler
							_alertUnsaved = ( _sessLang == SESS_LANG_CHN ) ? '未保存的更動會被丟棄！'
																														 : 'Unsaved Data will be LOST!';
							break;					
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
			}, // Success Handler
			error: function (jqXHR, textStatus, errorThrown) {
				alert( "Line 666\tError Status:\t"+textStatus+"\t\tMessage:\t\t"+errorThrown+"\n" );
			} // End of ERROR Handler							
		}); // AJAX call			
		$(".future").on( 'click', futureAlert );
		$(".soon").on( 'click', soonAlert );
	} // readSessParam()

})