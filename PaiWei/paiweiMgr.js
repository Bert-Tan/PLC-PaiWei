/**********************************************************
*                    Global variables                    *
**********************************************************/
var SESS_LANG_CHN = 1;	// These variables are used as CONSTANTS
var SESS_TYP_USR = 0;
var SESS_TYP_MGR = 1;
var SESS_TYP_WEBMASTER = 2;
 
var _sessUsr = null, _sessType = null, _sessLang = null;
var _pwExpires = null, _rtReason = null, _rtEvent = null;

var _activeTab = null;
var _alertUnsaved = '未保存的更動會被丟棄！';

var _pwTblNames = [ "C001A", "W001A_4", "DaPaiWei", "L001A", "Y001A", "D001A", "DaPaiWeiRed" ];

function isJSON( str ) {
	try {
		var x = JSON.parse(str);
		if ( x && typeof x === "object" ) return x;
	} catch (e) { /* do nothing */ }
	return false;
} // isJSON()

function leapYear_mgr( yr ) {
	return( ( yr % 100 === 0 ) ? ( yr % 400 === 0 ) : ( yr % 4 === 0 ) );
} // function leapYear_mgr()

function chkDate_mgr ( dateString, formatOnly ) { // in YYYY-MM-DD format
	var D = new Date(); // current
	var YYYY = D.getFullYear();
	var patString = "^(" + YYYY + "|" + (YYYY+1) + ")-(0?[1-9]|1[0-2])-(0?[1-9]|[12]\\d|3[01])$";
	var pattern = new RegExp( patString );

	if ( !dateString.match( pattern ) ) return false;
	if ( formatOnly ) return true;

	var d = dateString.split( '-' ); // d[0] => yyyy, d[1] => mm, d[2] = dd
	var dd = 0;

	switch ( Number( d[1] ) ) {
		case 2:
			var dd = leapYear_mgr( d[0] ) ? 29 : 28;
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
	} // switch on mm

	var nxtD = new Date( YYYY+1, D.getMonth(), D.getDate(), D.getHours(), D.getMinutes(), D.getSeconds() ); // a year from now
	var rtD = new Date( dateString );
	return ( ( ( 1 <= Number(d[2]) ) && ( Number(d[2]) <= dd ) ) && ( ( D <= rtD ) && ( rtD < nxtD ) ) );
} // function chkDate_mgr()

function readSessionParam() {
	_ajaxData = {}; _dbInfo = {};
	_ajaxData[ 'dbReq' ] = 'readSessParam';
	_ajaxData[ 'dbInfo' ] = JSON.stringify ( _dbInfo );
	$.ajax({
		url: "./ajax-pwMgr.php",
		method: 'POST',
		data: _ajaxData,
		success: function( rsp ) {
			var rspV = JSON.parse ( rsp );			
			for ( var X in rspV ) {
				switch ( X ) {					
					case 'usrName':
						_sessUsr = rspV[X];
						break;
					case 'sessType':
						_sessType = rspV[X];
						break;
					case 'sessLang':
						_sessLang = rspV[X];
						break;					
				} // switch()
			} // for loop
		}, // Success Handler
		error: function (jqXHR, textStatus, errorThrown) {
			alert( "readSessionParam()\tError Status:\t"+textStatus+"\t\tMessage:\t\t"+errorThrown+"\n" );
		} // End of ERROR Handler							
	}); // AJAX call
} // readSessionParam()


function loadPaiWeiDashboard() {
	$("#tabDataFrame").load("./Templates/pwDashboard.htm", function() {
        var ajaxData = {}, dbInfo = {};
        dbInfo[ 'tblName' ] = 'pwParam'; // filler; will not be used
        ajaxData[ 'dbReq' ] = 'dbLoadPaiweiDashboard';
        ajaxData[ 'dbInfo' ] = JSON.stringify( dbInfo );
        $.ajax({
			url: './ajax-pwMgr.php',
			method: 'post',
			data: ajaxData,
            success: function( rsp ) {
                rspX = isJSON( rsp );
                if ( ! rspX ) { alert( rsp ); return false; }
                for ( X in rspX ) {
                    switch ( X ) {
                    case 'URL':
                        location.replace( rspX[X] );
                        return;
                    case 'dashboardBody':
                        $("table.dataRows tbody").replaceWith( rspX[X] );
                        break;
                    case 'inCareOfOptions':
                        $("table.dataHdr #toBeReplaced").replaceWith( rspX[X] );
                        break;
                    }
                } // loop over received elements
				// now connect handlers
				$("table.dataHdr").find("*").unbind();
				$("table.dataRows").find("*").unbind();
                $("table.dataRows td[data-tblN]").on( 'click', hdlr_dataCellClick );
                $("#icoInputBtn").on( 'click', hdlr_icoInput );
				$("#icoSelBtn").on( 'click', hdlr_icoSelect );	
				$("#pwStatusSel").on( 'change', hdlr_pwStatusSelChg ); // bind to the select change handler
				$(".notifyBtn").on( 'click', hdlr_notifyBtn );		
				$("#notifyAllBtn").on( 'click', hdlr_notifyAllBtn );				
            }, // SUCCESS handler
            error: function ( jqXHR, textStatus, errorThrown ) {
                alert( "loadPaiWeiDashboard()\tError Status:\t"+textStatus+"\t\tMessage:\t\t"+errorThrown+"\n" );
            } // error handler
        }); // AJAX Call
    });
} // function loadPaiWeiDashboard()

function dashboardRedirect( dbInfo ) {
    var ajaxData = {};
    ajaxData[ 'dbReq' ] = 'dashboardRedirect';
	ajaxData[ 'dbInfo' ] = JSON.stringify( dbInfo );
    $.ajax({
		url: './ajax-pwMgr.php',
		method: 'post',
		data: ajaxData,
        success: function ( rsp ) {
            var rspX = isJSON( rsp );
            if ( ! rspX ) { alert ( rsp ); return false; }
            for ( X in rspX ) {
                switch (X) {
                case 'URL': // session timed out
                case 'redirect':
                    location.replace( rspX[ X ] );
                    return;
                } // switch();
            }
        }, // SUCCESS HANDLER
        error: function ( jqXHR, textStatus, errorThrown ) {
            alert( "dashboardRedirect()\tError Status:\t"+textStatus+"\t\tMessage:\t\t"+errorThrown+"\n" );
        } // error handler
    }); // AJAX Call
} // function dashboardRedirect()

function hdlr_dataCellClick() {
	var thisRow = $(this).closest("tr");
    var dbInfo = {};
	var icoName = thisRow.find("td[data-icoName]").attr("data-icoName");
	var tblName = $(this).attr("data-tblN");
	dbInfo[ 'icoName' ] = icoName;
    dbInfo[ 'icoNameType' ] = 'icoDerived';
	// only the 'PLC' user can see the RED DaPaiWei tab
	if ( tblName == 'DaPaiWeiRed' && icoName != 'PLC' )
		dbInfo[ 'tblName' ] = 'C001A';
	else
    	dbInfo[ 'tblName' ] = tblName;
    dashboardRedirect( dbInfo ); // dashboardRedirect() will not return here;
} // function hdlr_dataCellClick()

function hdlr_icoInput() {
    var dbInfo = {};
    var icoName = $(this).closest("th").find("#icoInput").val().trim();
    if ( icoName == '請輸入蓮友識別名' || icoName == '' ) {
        alert( '請輸入蓮友識別名' ); return false;
    }
    dbInfo[ 'icoName' ] = icoName;
    dbInfo[ 'icoNameType' ] = 'icoInput';
    dashboardRedirect( dbInfo ); // dashboardRedirect() will not return here;
} // function hdlr_icoInput()

function hdlr_icoSelect() {
    var dbInfo = {};
    var icoName = $(this).closest("th").find("SELECT OPTION:SELECTED").val();
    if ( icoName.length == 0 ) {
        alert( '請點選蓮友識別名' ); return false;
    }
    dbInfo[ 'icoName' ] = icoName;
    dbInfo[ 'icoNameType' ] = 'icoSelected';
    dashboardRedirect( dbInfo ); // dashboardRedirect() will not return here;    
} // hdlr_icoSelect()

/********************************************************************************
 * Event Handler - When the 'pwStatus' filter's selected option is changed      *
 ********************************************************************************/
function hdlr_pwStatusSelChg() {	
	var pwStatus = $( this ).val();
	var dataTbl = $( ".dataRows" );
	var allRows = dataTbl.find( "tr" );
	var dataRows = allRows.not( ":last" ); // except last <tr>
	var noValidRows = dataRows.find( "td.icoTotal[pw-valid-ct='0']" ).parent(); // <tr> with no valid paiwei
	var noInvalidRows = dataRows.find( "td.icoTotal[pw-invalid-ct='0']" ).parent(); // <tr> with no invalid paiwei
	var sumRow = allRows.last(); // the last <tr>

	switch ( pwStatus ) {
		case 'ALL':			
			adjustPaiweiCount( dataRows, sumRow, "pw-ct", "pw-sht" );
			allRows.show();
			break;
		case 'VALID':	
			adjustPaiweiCount( dataRows, sumRow, "pw-valid-ct", "pw-valid-sht" );
			allRows.show();
			noValidRows.hide();
			break;
		case 'INVALID':
			adjustPaiweiCount( dataRows, sumRow, "pw-invalid-ct", "pw-invalid-sht" );
			allRows.show();
			noInvalidRows.hide();
			break;
	}
} // hdlr_pwStatusSelChg()

/*************************************************************
 * Adjust dashboard paiwei count according to the 'pwStatus' *
 *************************************************************/
function adjustPaiweiCount( dataRows, sumRow, pwCtAttr, pwShtAttr ) {
	dataRows.each( function() {
		$( this ).find( "td" ).not( ":first" ).not( ":last" ).each( function() {
			$( this ).text( $( this ).attr( pwCtAttr ) );
		});
	});
	sumRow.find( "td" ).not( ":first" ).not( ":last" ).each( function() {
		$( this ).text( $( this ).attr( pwCtAttr ) + " 【" + $( this ).attr( pwShtAttr ) + "】" );
	});
} // adjustPaiweiCount()

/******************************************************
 * Event Handler - When a 'notifyBtn' is clicked      *
 ******************************************************/
function hdlr_notifyBtn() {
	var current = new Date();
	var year = current.getFullYear();
	var month = `0${current.getMonth() + 1}`.slice(-2);
	var day = `0${current.getDate()}`.slice(-2);
	var today = `${year}-${month}-${day}`;
	if ( today > _pwExpires ) {
		alert ( "牌位申請已過期，\n或本念佛堂近期內沒有法會；\n「告知」功能暫停！" );
		return;
	}

	var thisRow = $( this ).closest( "tr" );
	var icoName = thisRow.find( "td:first" ).attr( "data-icoName" );

	// get pwTbls (1-D array), which have INVALID paiwei, for the user
	var invalidPwTblNamesTheUsr = _pwTblNames.slice();
	// remove pwTbls which don't have INVALID paiwei
	thisRow.find( "td[pw-invalid-ct='0']" ).each( function() {
		invalidPwTblNamesTheUsr.splice( invalidPwTblNamesTheUsr.indexOf( $( this ).attr( "data-tblN" ) ), 1 );
	} );
	
	var ajaxData = {}, dbInfo = {};
	dbInfo[ 'icoName' ] = icoName;
	dbInfo[ 'tblNames' ] = invalidPwTblNamesTheUsr; // pwTbls which have INVALID paiwei	
	dbInfo[ 'pwExpires' ] = _pwExpires;
	dbInfo[ 'rtReason' ] = _rtReason;
	dbInfo[ 'rtEvent' ] = _rtEvent;
    ajaxData[ 'dbReq' ] = 'notifyInvalidPw';
	ajaxData[ 'dbInfo' ] = JSON.stringify( dbInfo );
    $.ajax({
		url: './ajax-pwMgr.php',
		method: 'post',
		data: ajaxData,
        success: function ( rsp ) {
            var rspX = isJSON( rsp );
            if ( ! rspX ) { alert ( rsp ); return false; }
            for ( X in rspX ) {
                switch (X) {
                case 'responseMsg':
                    alert( rspX[X] );
                    return;
				case 'printEmailContent': // print email for test only
					var x = window.open();
					x.document.write( rspX[X] );
					return;
                } // switch();
            }
        }, // SUCCESS HANDLER
        error: function ( jqXHR, textStatus, errorThrown ) {
            alert( "hdlr_notifyBtn()\tError Status:\t"+textStatus+"\t\tMessage:\t\t"+errorThrown+"\n" );
        } // error handler
    }); // AJAX Call
} // hdlr_notifyBtn()

/***********************************************************
 * Event Handler - When the 'notifyAllBtn' is clicked      *
 ***********************************************************/
function hdlr_notifyAllBtn() {
	var current = new Date();
	var year = current.getFullYear();
	var month = `0${current.getMonth() + 1}`.slice(-2);
	var day = `0${current.getDate()}`.slice(-2);
	var today = `${year}-${month}-${day}`;
	if ( today > _pwExpires ) {
		alert ( "牌位申請已過期，\n或本念佛堂近期內沒有法會；\n「全部告知」功能暫停！" );
		return;
	}

	// usrNames who have INVALID paiwei
	var icoNames = [];
	// each element is an array of pwTblNames (with INVALID paiwei) for the coresponding usrName (i.e. element key)
	var invalidPwTblNames = {};

	// fulfill 'icoNames' and 'invalidPwTblNames'
	var pwDataRows = $( this ).closest( "tr" ).siblings();
	var invalidPwDataRows = pwDataRows.find( "input.notifyBtn:enabled" ).closest( "tr" );	
	invalidPwDataRows.each( function() {
		var icoName = $( this ).find( "td:first" ).attr( "data-icoName" );
		var invalidPwTblNamesPerUsr = _pwTblNames.slice();

		// remove pwTbls which don't have INVALID paiwei
		$( this ).find( "td[pw-invalid-ct='0']" ).each( function() {
			invalidPwTblNamesPerUsr.splice( invalidPwTblNamesPerUsr.indexOf( $( this ).attr( "data-tblN" ) ), 1 );
		} );

		icoNames.push( icoName );
		invalidPwTblNames[ icoName ] = invalidPwTblNamesPerUsr;
	} );	

	var ajaxData = {}, dbInfo = {};
	dbInfo[ 'icoNames' ] = icoNames;
	dbInfo[ 'tblNames' ] = invalidPwTblNames;	
	dbInfo[ 'pwExpires' ] = _pwExpires;
	dbInfo[ 'rtReason' ] = _rtReason;
	dbInfo[ 'rtEvent' ] = _rtEvent;
    ajaxData[ 'dbReq' ] = 'notifyAllInvalidPw';
	ajaxData[ 'dbInfo' ] = JSON.stringify( dbInfo );
    $.ajax({
		url: './ajax-pwMgr.php',
		method: 'post',
		data: ajaxData,
        success: function ( rsp ) {
            var rspX = isJSON( rsp );
            if ( ! rspX ) { alert ( rsp ); return false; }
            for ( X in rspX ) {
                switch (X) {
                case 'responseMsg':
                    alert( rspX[X] );
                    return;
                } // switch();
            }
        }, // SUCCESS HANDLER
        error: function ( jqXHR, textStatus, errorThrown ) {
            alert( "hdlr_notifyAllBtn()\tError Status:\t"+textStatus+"\t\tMessage:\t\t"+errorThrown+"\n" );
        } // error handler
    }); // AJAX Call
} // hdlr_notifyAllBtn()


function loadRtMgrForm() {
	$("#tabDataFrame").load("./Templates/rtMgrForm.htm", function() {
		// The template is loaded; now fill in the data if provisioned; use AJAX call
		var ajaxData = {}, dbInfo = {};
		var tupID = null;
		dbInfo[ 'tblName' ] = 'pwParam'; // filler; will not be used
		ajaxData[ 'dbReq' ] = 'dbReadRtData';
		ajaxData[ 'dbInfo' ] = JSON.stringify( dbInfo );
		$.ajax({
			url: './ajax-pwMgr.php',
			method: 'post',
			data: ajaxData,
			success: function( rsp ) {
				rspX = isJSON( rsp );
				if ( !rspX ) { alert( rsp ); return false; }
				for ( X in rspX ) {
					switch( X ) {
					case 'URL':
						location.replace( rspX[X] );
						return;
					case 'ID':
						tupID = rspX[X];
						$("input[name=ID]").val( tupID );
						$("input[name=ID]").attr( 'value', tupID );
						break;
					case 'rtrtDate':
						$("input[name=rtrtDate]").val( rspX[X] );
						$("input[name=rtrtDate]").attr( 'value', rspX[X] );
						break;
					case 'pwExpires':
						$("input[name=pwExpires]").val( rspX[X] );
						$("input[name=pwExpires]").attr( 'value', rspX[X] );
						_pwExpires = rspX[X];
						break;
					case 'rtEvent':
						$("select[name=rtEvent]").val( rspX[X] );
						$("select[name=rtEvent]").attr( 'value', rspX[X] );
						_rtEvent = rspX[X];
						break;
					case 'lastRtrtDate':
						$("input[name=lastRtrtDate]").val( rspX[X] );
						$("input[name=lastRtrtDate]").attr( 'value', rspX[X] );
						break;
					case 'rtTemple':
						$("input[name=rtTemple]").val( rspX[X] );
						$("input[name=rtTemple]").attr( 'value', rspX[X] );
						break;
					case 'rtReason':
						$("input[name=rtReason]").val( rspX[X] );
						$("input[name=rtReason]").attr( 'value', rspX[X] );
						_rtReason = rspX[X];
						break;
					case 'rtVenerable':
						$("input[name=rtVenerable]").val( rspX[X] );
						$("input[name=rtVenerable]").attr( 'value', rspX[X] );
						break;
					case 'rtZhaiZhu':
						$("input[name=rtZhaiZhu]").val( rspX[X] );
						$("input[name=rtZhaiZhu]").attr( 'value', rspX[X] );
						break;
					case 'rtShouDu':
						$("input[name=rtShouDu]").val( rspX[X] );
						$("input[name=rtShouDu]").attr( 'value', rspX[X] );
						break;
					case 'ERR':
						alert( rspX[X]);
						return;
					} // switch
				} // for loop
				if ( tupID != null ) {
					$("input[name=rtrtDate]").attr( 'data-oldV', $("input[name=rtrtDate]").attr( 'value' ) );
					$("input[name=pwExpires]").attr( 'data-oldV', $("input[name=pwExpires]").attr( 'value' ) );
					$("select[name=rtEvent]").attr( 'data-oldV', $("select[name=rtEvent]").attr( 'value' ) );
					$("input[name=lastRtrtDate]").attr( 'data-oldV', $("input[name=lastRtrtDate]").attr( 'value' ) );
					$("input[name=rtTemple]").attr( 'data-oldV', $("input[name=rtTemple]").attr( 'value' ) );
					$("input[name=rtReason]").attr( 'data-oldV', $("input[name=rtReason]").attr( 'value' ) );
					$("input[name=rtVenerable]").attr( 'data-oldV', $("input[name=rtVenerable]").attr( 'value' ) );
					$("input[name=rtZhaiZhu]").attr( 'data-oldV', $("input[name=rtZhaiZhu]").attr( 'value' ) );
					$("input[name=rtShouDu]").attr( 'data-oldV', $("input[name=rtShouDu]").attr( 'value' ) );
					if ( $("select[name=rtEvent]").val() == "ThriceYearning" ) {
						$("input[name=rtZhaiZhu]").prop("disabled", false ); // Allow edit
						$("input[name=rtShouDu]").prop("disabled", false ); // Allow edit
						/* do not want to set value because it could be read from the DB */
					}
					else {
						$("input[name=rtZhaiZhu]").prop("disabled", true ).val("不適用");
						$("input[name=rtShouDu]").prop("disabled", true ).val("不適用");
					}
				}
				// now connect handlers
				$("#retreatUpd").find("*").unbind();
				$("#retreatUpd input[type=text]").on( 'focus', hdlr_onFocus );
				$("#retreatUpd input[type=text]").on( 'blur', hdlr_dataChg );
				$("#retreatUpd select").on( 'change', hdlr_rtEventSelChg );
				$("#retreatUpd input[name=rtUpdData]").on( "click", updRetreatData );
			}, // Success Handler
			error: function ( jqXHR, textStatus, errorThrown ) {
				alert( "loadRtMgrForm()\tError Status:\t"+textStatus+"\t\tMessage:\t\t"+errorThrown+"\n" );
			} // error handler
		}); // AJAX Call
	});
} // function loadRtMgrForm()	

function hdlr_onFocus() {
    var newV = $(this).val().trim().replace( /<br>$/gm, '');
    var pmptV = ( $(this).attr("data-pmptV") != undefined ) ? $(this).attr("data-pmptv").trim() : '';
    if ( pmptV.length > 0 ) return;
    $(this).attr( 'data-pmptV', newV ); // save it before blanking out
	$(this).val( '' ); // blank out the field for input
	return;
} // function hdlr_onFocus()

function hdlr_dataChg() {
    var newV = $(this).val().trim().replace( /<br>$/gm, '');
    var oldV = $(this).attr( "data-oldV" ).trim();
    var pmptV = ( $(this).attr("data-pmptV") != undefined ) ? $(this).attr("data-pmptv").trim() : '';
    var x = ( oldV.length > 0 ) ? oldV : pmptV;
    var fldN = $(this).attr('data-fldN');

    if ( newV.length == 0 && fldN != 'rtVenerable' ) {
        if ( oldV.length > 0 ) { // existing data editing; but did not input any data
			$(this).val( oldV ); // put back the existing data
        } else { // new data entry; but did not input any data
            $(this).val( $(this).attr("data-pmptv").trim() );
			$(this).attr( 'data-pmptv', '');
        }
        return;
	}

	switch ( fldN ) { // sanity check data value
		case 'rtrtDate':
		case 'pwExpires':
			if ( chkDate_mgr( newV, false ) == false ) {
				alert( "法會開始及牌位截止日期必須是在一年之內的有效日期！" );
				$(this).val( x );   if ( x == pmptV ) $(this).attr( 'data-pmptv', '');
				return;
			}
			break;
		case 'lastRtrtDate':
			if ( isNaN(new Date(newV)) ) {
				alert( "上次法會日期必須是一個有效日期!" );
				$(this).val( x );   if ( x == pmptV ) $(this).attr( 'data-pmptv', '');
				return;
			}	
			break;
		case 'rtReason':	
			if ( newV == '請輸入法會因緣' ) {
				alert( "請輸入法會因緣!" );
				$(this).val( x );   if ( x == pmptV ) $(this).attr( 'data-pmptv', '');
				return;
			}	
			break;
		case 'rtVenerable':
			if ( newV == '請輸入法會主法和尚' ) {
				alert( "請確認此次法會沒有主法和尚!" );
				$(this).val( x );   if ( x == pmptV ) $(this).attr( 'data-pmptv', '');
				return;
			}
			break;
		case 'rtZhaiZhu':	
			if ( newV == '請輸入法會齋主' ) {
				alert( "請輸入三時繫念法會齋主!" );
				$(this).val( x );   if ( x == pmptV ) $(this).attr( 'data-pmptv', '');
				return;
			}	
			break;
		case 'rtShouDu':	
			if ( newV == '請輸入法會受度人' ) {
				alert( "請輸入三時繫念法會受度人!" );
				$(this).val( x );   if ( x == pmptV ) $(this).attr( 'data-pmptv', '');
				return;
			}	
			break;
	} // switch()
	
    if ( newV != oldV ) {
        $(this).val( newV );
        $(this).attr( 'data-changed', "true" );
    }
} // function hdlr_dataChg()

/*********************************************************************
 * Event Handler - When the 'rtEvent' selected option is changed     *
 *********************************************************************/
function hdlr_rtEventSelChg() {		
	var chgdTo = $(this).val();
	$(this).attr( 'value', chgdTo);
	$(this).attr( 'data-changed', 'true');

	$("input[name=rtTemple]").val("淨土念佛堂及圖書館");
	$("input[name=rtReason]").val("請輸入法會因緣");
	$("input[name=rtVenerable]").val("請輸入法會主法和尚");

	if ( chgdTo == "ThriceYearning" ) {
		$("input[name=rtZhaiZhu]").prop("disabled", false ).val("請輸入法會齋主");
		$("input[name=rtShouDu]").prop("disabled", false ).val("請輸入法會受度人");
	}
	else {
		$("input[name=rtZhaiZhu]").prop("disabled", true ).val("不適用");
		$("input[name=rtShouDu]").prop("disabled", true ).val("不適用");
	}
} // function hdlr_rtEventSelChg()

function updRetreatData() {
	var dirtyCells = $("#retreatUpd input[data-changed=true]").length + $("#retreatUpd select[data-changed=true]").length;
	if ( dirtyCells == 0 ) { // no data change
		alert("資料沒有任何更動！"); return;
	}

	var tupID = $("input[name=ID]").val();
	var rtDate = $("input[name=rtrtDate]").val(); var rtD = new Date(rtDate);
	var pwDate = $("input[name=pwExpires]").val(); var pwD = new Date(pwDate);	
	var rtEvent = $("select[name=rtEvent]").val();
	var lastRtDate = $("input[name=lastRtrtDate]").val();
	var rtTemple = $("input[name=rtTemple]").val();
	var rtRsn = $("input[name=rtReason]").val();
	var rtVenerable = $("input[name=rtVenerable]").val();	
	var rtZhaiZhu = $("input[name=rtZhaiZhu]").val();
	var rtShouDu = $("input[name=rtShouDu]").val();
	
	if ( rtEvent == "" ) {
		alert( "請選擇法會類別！" ); return;
	}
	if ( chkDate_mgr( rtDate, false ) == false || chkDate_mgr( pwDate, true ) == false ) {
		alert( "法會開始及牌位截止日期必須是在一年之內的有效日期！" ); return;
	}
	if ( rtD <= pwD ) {
		alert("法會牌位申請截止日期必須早於法會開始日期！"); return;
	}
	if ( isNaN(new Date(lastRtDate)) ) {
		alert( "上次法會日期必須是一個有效日期!" ); return;
	}
	if ( rtRsn == '請輸入法會因緣' ) {
		alert( "請輸入法會因緣!" ); return;
	}
	if ( rtVenerable == '' || rtVenerable == '請輸入法會主法和尚' ) {
		if ( rtEvent == "ThriceYearning" ) {
			alert( "請輸入三時繫念法會主法和尚!" ); return;
		}
		else {
			if ( confirm( "請確認此次法會沒有主法和尚!" ) ) {
				$("input[name=rtVenerable]").val("");
				rtVenerable = null;
			}
			else {
				alert( "請輸入法會主法和尚!" ); return;
			}
		}
	}
	if ( rtEvent == "ThriceYearning" && ( rtZhaiZhu == '請輸入法會齋主') ) {
		alert( "請輸入三時繫念法會齋主!" ); return;
	}
	if ( rtEvent == "ThriceYearning" && ( rtShouDu == '請輸入法會受度人') ) {
		alert( "請輸入三時繫念法會受度人!" ); return;
	}	

	if ( rtEvent != "ThriceYearning" ) {		
		rtZhaiZhu = null;
		rtShouDu = null;
	}
	
	var ajaxData = {}, dbInfo = {};
	dbInfo[ 'tblName' ] = 'pwParam'; // filler; will not be used
	dbInfo[ 'ID' ] = tupID;	
    dbInfo[ 'rtrtDate' ] = rtDate;
	dbInfo[ 'pwExpires' ] = pwDate;
	dbInfo[ 'rtEvent' ] = rtEvent;
	dbInfo[ 'rtTemple' ] = rtTemple;
	dbInfo[ 'rtReason' ] = rtRsn;
	dbInfo[ 'rtVenerable' ] = rtVenerable;
	dbInfo[ 'rtZhaiZhu' ] = rtZhaiZhu;
	dbInfo[ 'rtShouDu' ] = rtShouDu;
	dbInfo[ 'lastRtrtDate' ] = lastRtDate;
	ajaxData[ 'dbReq' ] = 'dbUpdRtData';
	ajaxData[ 'dbInfo' ] = JSON.stringify( dbInfo );
	$.ajax({
        url : './ajax-pwMgr.php',
        method : 'post',
        data : ajaxData,
        success: function ( rsp ) {
            var rspX = isJSON( rsp );
            if ( !rspX ) { alert ( rsp ); return; }
            for ( X in rspX ) {
                switch( X ) {
                case 'SUCCESS': // perm the record data
                    $("input[name=ID]").val( rspX[X] );
                    $("input[name=ID]").attr( 'value', rspX[X] );
                    $("#retreatUpd input[type=text]").each(function() {
                        var x = $(this).val();
                        $(this).attr({ 'data-oldV' : x, 'value' : x, 'data-changed' : 'false' } );
					});
					$("#retreatUpd select").each(function() {
                        var x = $(this).val();
                        $(this).attr({ 'data-oldV' : x, 'value' : x, 'data-changed' : 'false' } );
                    });
                    alert("法會資料更新完畢！");
                    return;
                case 'ERR':
                    alert( rspX[X] );
                    return;
                }
            } // for loop
        }, // Success Handler
        error : function ( jqXHR, textStatus, errorThrown ) {
            alert( "updRetreatData()\tError Status:\t"+textStatus+"\t\tMessage:\t\t"+errorThrown+"\n" );
        } // error handler
    }); // AJAX Call
} // function updRetreatData()

function hdlr_tabClick_mgr() {
	// unsaved data
	var dirtyCells = $("#retreatUpd input[data-changed=true]").length + $("#retreatUpd select[data-changed=true]").length;	
	if ( ( dirtyCells > 0 ) && ( !confirm( _alertUnsaved ) ) ) return;  

   	var rqTblName = $(this).attr("data-table");
   	if ( rqTblName == _activeTab ) return false; /* nothing to do */
   	_activeTab = rqTblName;
   	$(".tabMenu th").removeClass("active").css("border", "1px solid white");
   	$(this).addClass("active").css("border-bottom", "1px solid green");
   	
   	/* load / show tab content, rtMgr, dnldJiWenForm, dnldPaiWeiForm, or Dashboard information here */
   	$("#tabDataFrame").find("*").unbind(); $("#tabDataFrame").empty();
   	switch ( _activeTab ) {
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
   	case 'PaiWeiDash':
		loadPaiWeiDashboard();
   	    break;
   	} // switch()
} // function hdlr_tabClick_mgr()
