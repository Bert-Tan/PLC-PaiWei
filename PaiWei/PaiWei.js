/**********************************************************
 * Global variables																				*
 **********************************************************/
var SESS_LANG_CHN = 1;	// These variables are used as CONSTANTS
var SESS_MODE_EDIT = 0;
var SESS_MODE_INS = 1;
var SESS_MODE_SRCH = 2;

var _sessUsr = null, _sessPass = null, _sessType = null, _sessLang = null;
var _sessMode = SESS_MODE_EDIT; // default
var _dbInfo = {}, _ajaxData = {};
var _tblName = null, _tblSize = 0;
var _pilotDataRow = null;	// to be used for adding rows
var _pwPlqDate = null;

var _delBtns = null;
var _editBtns = null;
var _addRowBtn = null;
var _srchBtn = null;

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

function chgEdit2Upd ( editBtn ) {
	var updBtnVal = ( _sessLang == SESS_LANG_CHN ) ? "更新" : "Update";
	editBtn.unbind(); // unbind myself from the Edit Button Handler
	editBtn.attr( "value", updBtnVal ); // change myself to become an 'Update' button
	editBtn.on( 'click', updBtnHdlr );	
} // chgEdit2Upd()

function chgUpd2Edit ( updBtn ) {
	var editBtnVal = ( _sessLang == SESS_LANG_CHN ) ? "更改" : "Edit";
	updBtn.unbind(); // unbind myself from the Edit Button Handler
	updBtn.attr( "value", editBtnVal ); // change myself to become an 'Edit' button
	updBtn.on( 'click', editBtnHdlr );		
} // chgUpd2Edit()

function isJSON( str ) {
    try {
        JSON.parse(str);
    } catch (e) {
        return false;
    }
    return true;
} // isJSON()

function loadTblData( tblName, pgNbr, numRec, sessUsr, /* dataOnly */ ) {
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
			alert( "Line 133\tError Status:\t"+textStatus+"\t\tMessage:\t\t"+errorThrown+"\n" );
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
/*	if ( $(this).hasClass("active") ) return; */
	if ( ( dirtyCells > 0 ) && ( !confirm( 'Unsaved Data will be LOST!!\n' ) ) ) return;
	
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
			alert( "Line 176\tError Status:\t"+textStatus+"\t\tMessage:\t\t"+errorThrown+"\n" );
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
	var	dateText = ( _sessLang == SESS_LANG_CHN ) ? "請輸入 年-月-日" : "Please Enter Deceased Date";
	
	$(".errMsg").remove();
	if ( ( dirtyCells > 0 ) && ( !confirm( 'Unsaved Data will be LOST!!\n' ) ) ) return;
	
	newRow.attr( "data-keyN", _pilotDataRow.attr("data-keyN") ); // copy the Key Name
	newRow.attr( "id", '' ) ; // no tuple Key value 
	newRowDataCells.text( cellText );
	newRowDataCells.attr( { 'contenteditable' : 'true', 'data-oldV' : cellText } );
	if ( _tblName == 'DaPaiWei' ) {
		newRow.find("span[data-fldN='deceasedDate']").text( dateText );
		newRow.find("span[data-fldN='deceasedDate']").attr( 'data-oldV', dateText );
	}
	newRowDataCells.on( 'blur', dataChgHdlr ); // bind the <td><span> to data change handler
	lastTd.html( insBtn ); // place the 'Insert' button
	lastTd.find("input[type=button]").on( 'click', insBtnHdlr ); // bind to Insert Button click handler
	
	if ( _sessMode != SESS_MODE_INS ) {
		$("#myDataWrapper").find("*").unbind()
		tbody.find("tr").remove(); // remove all data rows
		$("#myDataFooter").remove(); // remove the footer
		_sessMode = SESS_MODE_INS;
	}
	// tbody.find(".lookupBtn").closest("tr").remove();
	tbody.append( newRow );
} // addRowBtnHdlr()

/**********************************************************
 * Event Handler	- When the Lookup Button is clicked			*
 **********************************************************/
function lookupBtnHdlr() {
	var notFoundText = ( _sessLang == SESS_LANG_CHN ) ? '沒有找到所要找的牌位，請輸入或上載牌位資料。'
																									: 'No record found! Please Input or Upload Data.';
	var notFoundMSG = '<h1 class="centerMe errMsg">' + notFoundText + '</h1>';
	var tblFlds = {};
	var thisRow = $(this).closest("tr");
	var cellsChanged = thisRow.find("span[data-changed=true]");

	_ajaxData = {}; _dbInfo = {};
	if ( cellsChanged.length == 0 ) return;
	
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
						rspV[ 'errRec' ].foreach( element => {
							msgText += element;
						} );
						var errMSG = '<h1 class="centerMe errMsg">' + msgText + '</h1>';
						$("#myDataWrapper").append( errMSG ); // reset to default					
						break;
				} // switch()
			} // for loop
		}, // success handler
		error: function (jqXHR, textStatus, errorThrown) {
			alert( "Line 295\tError Status:\t"+textStatus+"\t\tMessage:\t\t"+errorThrown+"\n" );
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

	$(".errMsg").remove();
	if ( _sessMode == SESS_MODE_SRCH ) {
		var alert_txt = ( _sessLang == SESS_LANG_CHN ) ? "已經在搜索狀態!" : "Already in Search Mode!";
		alert( alert_txt );
		return;
	}
	if ( ( dirtyCells > 0 ) && ( !confirm( 'Unsaved Data will be LOST!!\n' ) ) ) return;

	newRow.attr( "data-keyN", _pilotDataRow.attr("data-keyN") ); // copy the Key Name
	newRow.attr( "id", '' ) ; // no tuple Key value 
	newRowDataCells.text( cellText );
	newRowDataCells.attr( { 'contenteditable' : 'true', 'data-oldV' : '' } );
	newRowDataCells.on( 'blur', dataChgHdlr ); // bind the <td><span> to data change handler
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
	var alertText = ( _sessLang == SESS_LANG_CHN ) ? "請輸入牌位資料" : "Please Enter Name Plaque Text";
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
			alert( "Line 392\tError Status:\t"+textStatus+"\t\tMessage:\t\t"+errorThrown+"\n" );
		} // End of ERROR Handler							
	}); // AJAX CALL
} // insBtnHdlr()
 
/**********************************************************
 * Event Handler	- When a Delete Button is clicked				*
 **********************************************************/
function delBtnHdlr() {
	var tblFlds = {};

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
			alert( "Line 438\tError Status:\t"+textStatus+"\t\tMessage:\t\t"+errorThrown+"\n" );
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
			alert( "Line 518\tError Status:\t"+textStatus+"\t\tMessage:\t\t"+errorThrown+"\n" );
		} // End of ERROR Handler					
	}); // AJAX Call
} // updBtnHdlr()

/********************************************************************************
 * Event Handler - When a cell data is changed																	*
 ********************************************************************************/
function dataChgHdlr() {
	var newV = $(this).text().trim().replace( /<br>$/gm, '');
	var oldV = $(this).attr("data-oldV").trim();
	var alertText = ( _sessLang == SESS_LANG_CHN ) ? "請輸入 年-月-日" : "Date Format: YYYY-MM-DD";
	var errText = ( _sessLang == SESS_LANG_CHN ) ? "往生日期必須晚於" + _pwPlqDate
																							 : "Deceased Date must be after " + _pwPlqDate;

	if ( ( _tblName == 'DaPaiWei' ) && ( $(this).attr("data-fldN") == 'deceasedDate' ) ) {
		x = newV.match( /^\d{4}[\/\-](0?[1-9]|1[012])[\/\-](0?[1-9]|[12][0-9]|3[01])$/ );
		if (x == null) {
			alert( alertText );
			$(this).html( oldV );
			return;
		}
		var dx = new Date( newV );
		var dPlq = new Date( _pwPlqDate );
		if ( dx < dPlq ) {
			alert( errText );
			$(this).html( oldV );
			return;			
		}
	} // DaPaiWei and checking deceased Date

	$(this).html( newV );
	if ( newV != oldV ) {
		$(this).attr("data-changed", "true");
	}	
} // dataChgHdlr()

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
			success: function( rsp ) { // alert ( rsp ); // Success Handler 
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
							$("th.pwTbl").on( 'click', pwTblHdlr ); // bind Pai Wei menu items to the click handler
							$("#upld").on( 'click', upldHdlr ); // bind upload anchor to its handler
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
				alert( "Line 438\tError Status:\t"+textStatus+"\t\tMessage:\t\t"+errorThrown+"\n" );
			} // End of ERROR Handler							
		}); // AJAX call			
//		$(".future").on( 'click', futureAlert );
//		$(".soon").on( 'click', soonAlert );
	} // readSessParam()

})