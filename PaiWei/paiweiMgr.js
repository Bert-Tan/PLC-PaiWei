/**********************************************************
 *                    Global variables                    *
 **********************************************************/
 var SESS_LANG_CHN = 1;	// These variables are used as CONSTANTS
 var SESS_TYP_USR = 0;
 var SESS_TYP_MGR = 1;
 var SESS_TYP_WEBMASTER = 2;
 
 var _sessUsr = null, _sessType = null, _sessLang = null;

var _activeTab = null;
var _alertUnsaved = '未保存的更動會被丟棄！';

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
	dbInfo[ 'icoName' ] = thisRow.find("td[data-icoName]").attr("data-icoName");
    dbInfo[ 'icoNameType' ] = 'icoDerived';
    dbInfo[ 'tblName' ] = $(this).attr("data-tblN");
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
						break;
					case 'rtEvent':
						$("select[name=rtEvent]").val( rspX[X] );
						$("select[name=rtEvent]").attr( 'value', rspX[X] );
						break;
					case 'rtReason':
						$("input[name=rtReason]").val( rspX[X] );
						$("input[name=rtReason]").attr( 'value', rspX[X] );
						break;	
					case 'lastRtrtDate':
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
					$("input[name=rtReason]").attr( 'data-oldV', $("input[name=rtReason]").attr( 'value' ) );
						if ( $("select[name=rtEvent]").val() != "ThriceYearning" ) {
						$("input[name=rtReason]").prop("disabled", true ).val("不適用");
					}
					else {
						$("input[name=rtReason]").prop("disabled", false ); // Allow edit
						/* do not want to set value because it could be read from the DB */
					}
				}
				// now connect handlers
				$("#retreatUpd").find("*").unbind();
				$("#retreatUpd input[type=text]").on( 'focus', hdlr_onFocus );
				$("#retreatUpd input[type=text]").on( 'blur', hdlr_dataChg );
				$("#retreatUpd select").on( "change", selChange );
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

    if ( newV.length == 0 ) {
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
		case 'rtReason':	
			if ( newV == '不適用' || newV == '請輸入法會因緣' ) {
				alert( "請輸入三時繫念法會因緣!" );
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

function selChange() {		
	var chgdTo = $(this).val();
	$(this).attr( 'value', chgdTo);
	$(this).attr( 'data-changed', 'true');
	if ( chgdTo != "ThriceYearning" ) {
		$("input[name=rtReason]").prop("disabled", true ).val("不適用");
	} else {
		$("input[name=rtReason]").prop("disabled", false ).val("請輸入法會因緣");
	}
} // function selChange()

function updRetreatData() {
	var dirtyCells = $("#retreatUpd input[data-changed=true]").length + $("#retreatUpd select[data-changed=true]").length;
	if ( dirtyCells == 0 ) { // no data change
		alert("資料沒有任何更動！"); return;
	}

	var tupID = $("input[name=ID]").val();
	var lastRtDate = $("input[name=rtrtDate]").attr('data-oldV');
	var rtDate = $("input[name=rtrtDate]").val(); var rtD = new Date(rtDate);
	var pwDate = $("input[name=pwExpires]").val(); var pwD = new Date(pwDate);
	var rtEvent = $("select[name=rtEvent]").val();
	var rtRsn = $("input[name=rtReason]").val();

	if ( rtEvent == "" ) {
		alert( "請選擇法會類別！" ); return;
	}
	if ( chkDate_mgr( rtDate, false ) == false || chkDate_mgr( pwDate, true ) == false ) {
		alert( "法會開始及牌位截止日期必須是在一年之內的有效日期！" ); return;
	}
	if ( rtD <= pwD ) {
		alert("法會牌位申請截止日期必須早於法會開始日期！"); return;
	}
	if ( rtEvent == "ThriceYearning" && ( rtRsn == '不適用' || rtRsn == '請輸入法會因緣') ) {
		alert( "請輸入三時繫念法會因緣!" ); return;
	}
	if ( rtEvent != "ThriceYearning" ) {
		rtRsn = "";
	}

	var ajaxData = {}, dbInfo = {};
	dbInfo[ 'tblName' ] = 'pwParam'; // filler; will not be used
	dbInfo[ 'ID' ] = tupID;
	dbInfo[ 'lastRtrtDate' ] = lastRtDate;
    dbInfo[ 'rtrtDate' ] = rtDate;
	dbInfo[ 'pwExpires' ] = pwDate;
	dbInfo[ 'rtEvent' ] = rtEvent;
	dbInfo[ 'rtReason' ] = rtRsn;
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
			$(".dnldCSVBtn").on( 'click', dnldCSVBtnHdlr );
			$(".dnldPDFBtn").on( 'click', dnldPDFBtnHdlr );
		});
   	    break;
   	case 'PaiWeiDash':
		loadPaiWeiDashboard();
   	    break;
   	} // switch()
} // function hdlr_tabClick_mgr()
