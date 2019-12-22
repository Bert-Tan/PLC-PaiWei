/*********************************************************
*                    Global variables                    *
**********************************************************/
var SESS_LANG_CHN = 1;	// These variables are used as CONSTANTS
var SESS_MODE_EDIT = 0;
var SESS_MODE_SRCH = 1;
var SESS_TYP_USR = 0;
var SESS_TYP_MGR = 1;
var SESS_TYP_WEBMASTER = 2;

var _sessUsr = null, _sessPass = null, _sessType = null, _sessLang = null;
var _icoName = null;
var _tblName = null, _tblSize = null;
var _pilotRow = null;
var _alertUnsaved = null;
var _now = null, _nowV = null; // set as part of init; _now = _now.getTime()
var _expHH = null, _expMM = null; // set by reading session parameters
// var _deceased49V = null;
var _startingSunday = null, _startingSundayV = null, _startingSundayStr = null;
var _endingSunday = null, _endingSundayV = null, _endingSundayStr = null;

function isJSON( str ) {
    try {
        var x = JSON.parse(str);
        if ( x && typeof x === "object" ) return x;
    } catch (e) { /* do nothing */ }
    return false;
} // isJSON()

function appendUnique( thisStr, objStr ) {
    // utility function to append the thisStr to the end of the ObjStr; the resulting string has unique substring components
    thisPattern = new RegExp( thisStr );
    if ( objStr.length == 0 ) return thisStr;
    if ( objStr.match( thisPattern ) ) return objStr;
    return ( objStr + ', ' + thisStr );
} // function appendUnique()

function leapYr( yr ) {
    return( ( yr % 100 === 0 ) ? ( yr % 400 === 0 ) : ( yr % 4 === 0 ) );
} // function leapYr()

function isValidDate( dateString, chk4Sunday, giveAlert ) {
    var errDate = ( _sessLang == SESS_LANG_CHN) ? "不是一個正確的（年-月-日)！" : "is Not A Valid Date!";
    var notSunday = ( _sessLang == SESS_LANG_CHN) ? "不是一個星期日！" : "is Not A Sunday!";
    if ( ! dateString.match( /^\d{4}[\-\/](0?[1-9]|1[012])[\-\/](0?[1-9]|[12][0-9]|3[01])$/ ) ) {
        if ( giveAlert ) alert( "'" + dateString + "' " + errDate );
        return false;
    }
    d = dateString.split( /[\-\/]/ ); // d[0]: YYYY; d[1]: MM; d[2]: DD
    switch( Number( d[1] ) ) {
        case 2:
            dd = leapYr( Number( d[0] ) ) ? 29 : 28;
            break;
        case 1: case 3: case 5: case 7: case 8: case 10: case 12:
            dd = 31;
            break;
        default:
            dd = 30;
            break;
    } // switch()    
    if ( Number( d[2] ) > dd ) {
        if ( giveAlert ) alert( "'" + dateString + "' " + errDate );
        return false;
    }
    if ( !chk4Sunday ) return true; // done

    // Check for a valid Sunday
    var myDate = new Date( d[0], (d[1]-1), d[2] );
    if ( myDate.getDay() != 0 ) {
        if ( giveAlert ) alert( "'" + dateString + "' " + notSunday );
        return false;
    }
    return true;
} // function isValidDate()

function isFutureDate( dateStr ) {
    var d = dateStr.split( /[\-\/]/ );
    return ( new Date( d[0], (d[1]-1), d[2] ).getTime() > _nowV );
} // function isFutureDate()

function deriveDeceased49V( dateStr, chkSanity ) { // dateStr is a valid date string; chkSanity = true | false 
    var errDeceased = ( _sessLang == SESS_LANG_CHN ) ? "往生日期不應在未來！" : "A future date is entered!"
    var d = dateStr.split( /[\-\/]/ );
    var deceased = new Date( d[0], (d[1]-1), d[2] );
    if ( chkSanity && deceased.getTime() >= _nowV ) { alert( errDeceased ); return null; }
    return( new Date( deceased.setDate( deceased.getDate() + 49 ) ).getTime() );
} // fundtion deriveDeceased49()

function readSundayParam() {
    var ajaxData = {}, dbInfo = {}, rspX = null;
    dbInfo[ 'tblName' ] = 'sundayParam';
    ajaxData[ 'dbReq' ] = 'readSundayParam';
    ajaxData[ 'dbInfo' ] = JSON.stringify ( dbInfo );
    $.ajax({
        url: "./ajax-qifuDB.php",
        method: "post",
        data: ajaxData,
        success: function( rsp ) {
            rspX = isJSON ( rsp );
            if ( !rspX ) {
                alert( rsp ); return false;
            }
            for ( X in rspX ) { // alert ( "readSundayParam(): '" + X + "': '" + rspX[X] + "'");
                switch( X ) {
                case 'URL':
                    location.replace( rspX[X] );
                    return;
                case 'sessLang':
                    _sessLang = rspX[X]; // alert( "_sessLang= " + _sessLang );
                    break;
                case 'sessType':
                    _sessType = rspX[X]; // alert( "_sessType = " + _sessType );
                    break;
                case 'usrName':
                    _sessUsr = rspX[X]; // alert( "_sessUsr= " + _sessUsr );
                    break;
                case 'usrPass':
                    _sessPass = rspX[X]; // alert( "_sessPass= " + _sessPass );
                    break;
                case 'icoName':
                    _icoName = rspX[X]; // alert( "_icoName= " + _icoName );
                    break;
                case 'tblName':
                    _tblName = rspX[X]; // alert( "_tblName= " + _tblName );
                    break;
                case 'expHH':
                    _expHH = rspX[X]; // Request deadline for the upcoming Sunday, HH
                    break;
                case 'expMM':
                    _expMM = rspX[X]; // Request deadline for the upcoming Sunday, MM
                    break;
                } // switch
            } // for loop
            /* done all the startup house-keeping; ready to edit */
            _alertUnsaved = ( _sessLang == SESS_LANG_CHN ) ? '未保存的更動會被丟棄！' : 'Unsaved Data will be LOST!';
            init_done();
        }, // success handler
        error: function ( jqXHR, textStatus, errorThrown ) {
            alert( "readSundayParam()\tError Status:\t"+textStatus+"\t\tMessage:\t\t"+errorThrown+"\n" );
        } // error handler
    }); // ajax call
} // function readSundayParam()

function init_done() {
    var ackStart = ( _sessLang == SESS_LANG_CHN) ? "今天的祈福迴向申請已截止，祈福迴向將從下星期日開始！"
                                                 : "Today's request is overdue; submitted request will begin next Sunday";
    var defaultTbl = "sundayRule";
    if ( _tblName != null )  {
        defaultTbl = _tblName;
        _tblName = null;
    }
    
    $(".tabMenu th").on( 'click', hdlr_tabClick );
    $(".tabMenu th.future").unbind().on( 'click', futureAlert );
    $(".tabMenu th[data-table=" + defaultTbl + "]").trigger( 'click' );

    /* initialize time variables; with _xxx are globals */
    _now = new Date();   _nowV = _now.getTime();
    days2Sunday = ( 7 - _now.getDay() ) % 7;
    _startingSunday = new Date(); _startingSunday.setTime( _nowV + ( days2Sunday * 86400 * 1000 ) ); // tentative
    tY = _startingSunday.getFullYear(); tM = _startingSunday.getMonth();  tD = _startingSunday.getDate();
    expV = ( new Date( tY, tM, tD, _expHH, _expMM ) ).getTime();
    if ( _nowV > expV ) {
        if ( _now.getDay() == 0 ) { alert ( ackStart ); days2Sunday += 7; }   
    }
    _startingSunday.setTime( _nowV + ( days2Sunday * 86400 * 1000 ) ); // recalculate
    tY = _startingSunday.getFullYear(); tM = _startingSunday.getMonth();  tD = _startingSunday.getDate();
    _startingSunday = new Date( tY, tM, tD );
    _startingSundayV = _startingSunday.getTime();
    _startingSundayStr = tY + "-" + (tM+1) + "-" + tD;
} // function init_done() - ready for user request

function rdy_edit() {
    $("#addRowBtn").unbind();   $("#addRowBtn").on( 'click', hdlr_addRow );
    $("#delAllBtn").unbind();   $("#delAllBtn").on( 'click', hdlr_delAll );
    $(".editBtn").unbind();     $(".editBtn").on( 'click', hdlr_editBtn );
    $(".delBtn").unbind();      $(".delBtn").on( 'click', hdlr_delBtn );
} // function rdy_edit() - data loaded and ready for user to edit

function init_pilotRow( pRow ) {
    var pilotInputTxt = ( _sessLang == SESS_LANG_CHN ) ? "請輸入資料" : "Input Data";
    var reqDateTxt = ( _sessLang == SESS_LANG_CHN ) ? "年-月-日；星期日，以逗號分開" : "YYYY-MM-DD comma separated";
    var insBtnTxt = ( _sessLang == SESS_LANG_CHN ) ? "加入" : "Insert";
    var insBtn = $('<input class="insBtn" type="button" value="' + insBtnTxt + '">');
    var lastTd = pRow.find("td:last");

    pRow.find("input[type=text]").attr( { "data-oldv": '', "value": pilotInputTxt, "data-pmptv": '' } );
    pRow.find("input[data-fldn=reqDates]").attr( "value", reqDateTxt );
    pRow.find("input[type=text]").prop( "disabled", false );
    pRow.attr("id", '');
    lastTd.find("*").unbind().remove();
    lastTd.append( insBtn );
} // init_pilotRow()

function loadTblData( tblName, usrName, frameID ) { // alert( "loadTblData - User: " + usrName );
    /* Caller has called $( "#" . dataFrameID ).empty() */
    var dataFrame = $( "#" + frameID );
    var tblHdrWrapper =	$('<div class="dataHdrWrapper"></div>');
    var tblDataWrapper = $('<div class="dataBodyWrapper"></div>');
    var dbInfo = {}, ajaxData = {};
    var rspX = null;

    dbInfo[ 'tblName' ] = tblName;
    dbInfo[ 'rqstr' ] = usrName;
    dbInfo[ 'refDate' ] = _startingSundayStr;
    ajaxData[ 'dbReq' ] = 'dbREAD';
    ajaxData[ 'dbInfo' ] = JSON.stringify ( dbInfo );
    $.ajax({
        url: "./ajax-qifuDB.php",
        method: "post",
        data: ajaxData,
        success: function ( rsp ) {
            rspX = isJSON ( rsp );
            if ( !rspX ) {
                alert ( rsp ); return false;
            }
            for ( var X in rspX ) {
                switch ( X ) {
                case 'URL':
                    location.replace( rspX [ X ] );
                    return;
                case 'myDataHdr':
                    tblHdrWrapper.html( rspX[ X ]);
                    break;
                case 'myData':
                    tblDataWrapper.html( rspX[ X ]);
                    break;
                case 'myDataSize':
                    _tblSize = rspX[ X ];
                    break;
                } // switch()
            } // for loop; parsed JSON string components
            _pilotRow = tblDataWrapper.find("tr:first").clone();
            init_pilotRow( _pilotRow );
            if ( _tblSize == 0 ) {
                tblDataWrapper.find("tr").remove();
            }
            dataFrame.append( tblHdrWrapper, tblDataWrapper );
            rdy_edit();
        }, // success handler
        error: function ( jqXHR, textStatus, errorThrown ) {
            alert( "loadTblData()\tError Status:\t"+textStatus+"\t\tMessage:\t\t"+errorThrown+"\n" );
        } // error handler
    }); // ajax Call
} // function loadTblData() of the Sunday Qifu or Merit tables

function hdlr_onFocus() {
    var newV = $(this).val().trim().replace( /<br>$/gm, '');
	var pmptV = $(this).attr("data-pmptv").trim().replace( /<br>$/gm, '');
	if ( pmptV.length > 0 ) return; // Already done once; user has input data & comes back to it
	$(this).attr( 'data-pmptv', newV ); // save it before blanking out
	$(this).val( '' ); // blank out the field for input
	return;
} // function hdlr_onFocus

function hdlr_dataChg() { // on Blur
    var emptyText = ( _sessLang == SESS_LANG_CHN ) ? "該項資料不應空白！" : "Shall not be empty!";
    var ackLimit3 = ( _sessLang == SESS_LANG_CHN ) ? "消災祈福以三次為限！" : "Well-wishing limited to three times!";
    var ackDateStart = ( _sessLang == SESS_LANG_CHN ) ? "消災祈福開始日期為： " : "Well-wishing or Merit Dedication must be after: ";
    var ackBeyond49 = ( _sessLang == SESS_LANG_CHN ) ? "已過七七之期；功德回向以本週末為限: '"
                                                     : "Deceased > 49 days ago; Merit Dedication limited to this Sunday: '";
    var ignoreBeyond49 = ( _sessLang == SESS_LANG_CHN ) ? "功德回向日期應該在七七之內！" : "Requests should be within 49 days!";
    var errDeceased = ( _sessLang == SESS_LANG_CHN ) ? "往生日期不應在未來！" : "A future date is entered!";
    var errAge = ( _sessLang == SESS_LANG_CHN ) ? "請輸入合理的年齡數字！" : "Please enter a reasonable age!";
    var reqDeceased = ( _sessLang == SESS_LANG_CHN ) ? "請輸入往生日期！" : "Please enter Deceased Date!";
    var newV = $(this).val().trim().replace( /<br>$/gm, '');
    var newVx = '';
    var oldV = $(this).attr("data-oldv").trim();
    var pmptV = ( $(this).attr("data-pmptv") !== undefined ) ? $(this).attr("data-pmptv").trim() : '';
    var x = ( oldV.length > 0 ) ? oldV : pmptV;
    var fldN = $(this).attr("data-fldn");
    
    if ( newV.length == 0 ) {
        if ( oldV.length > 0 ) { // existing data editing; but did not input any data
            alert( emptyText ); // give alert
			$(this).val( oldV ); // put back the existing data
        } else { // new data entry; but did not input any data
            $(this).val( $(this).attr("data-pmptv").trim() );
			$(this).attr( 'data-pmptv', '');
        }
        return;
    }

    if ( fldN == 'Age') {
        if ( ! newV.match(/^\d{1,3}$/) ) {
            alert( errAge );
            $(this).val( x );   if ( x == pmptV ) $(this).attr( 'data-pmptv', '');
            return false;
        }
    }
    if ( fldN == 'Deceased_D' ) { // data change or input in 往生日期 field
        if ( ! isValidDate( newV, false, true ) ) { // an invalid date
            $(this).val( x );   if ( x == pmptV ) $(this).attr( 'data-pmptv', '');
            return;
        }
        if ( isFutureDate( newV ) ) {
            alert( errDeceased );
            $(this).val( x );   if ( x == pmptV ) $(this).attr( 'data-pmptv', '');
            return;
        }
    } // data change or input in 往生日期 field

    if ( fldN == 'reqDates' ) {
        // it could be for 祈福 (max 3 times), or for 迴向 (1 or upto 7 times ); need to validate
        var dateArray = newV.split( /,\s*/ ).sort();
        if ( dateArray[0].length == 0 ) dateArray.shift();
        for ( i = 0; i < dateArray.length; i++ ) {
            if ( ! isValidDate( dateArray[i], true, true ) ) {
                $(this).val( x );   if ( x == pmptV ) $(this).attr( 'data-pmptv', '');
                return;
            }
        }
        if ( _tblName == 'sundayQifu' ) { // for 祈福消災; max 3 times
            if ( dateArray.length > 3 ) {
                alert( ackLimit3 );
                $(this).val( x );   if ( x == pmptV ) $(this).attr( 'data-pmptv', '');
                return;
            }
            for ( i = 0; i < dateArray.length; i++ ) {
                var d = dateArray[i].split( /[\-\/]/ );
                dV = new Date( d[0], (d[1]-1), d[2] ).getTime();
                if ( dV >= _startingSundayV ) {
                    newVx = appendUnique( dateArray[i], newVx ); continue;
                }
                alert( ackDateStart + _startingSundayStr + '!' );
            }
            newV = newVx;
        } // for 祈福消災; max 3 times
        if ( _tblName == 'sundayMerit' ) { // for 功德迴向
            // need to have the Deceased data; either it was just entered, or was being edited          
            var deceasedStr = $(this).closest("tr").find("input[data-fldn=Deceased_D]").val();
            deceased49V = isValidDate( deceasedStr, false, false ) ? deriveDeceased49V( deceasedStr, false ) : null;
            if ( deceased49V == null ) { // still null - user has not entered the data
                alert( reqDeceased );
                $(this).val( x );   if ( x == pmptV ) $(this).attr( 'data-pmptv', '');
                return;
            }
            if ( deceased49V < _startingSundayV ) { // 已過 七七
                alert( ackBeyond49 + _startingSundayStr + "'" );
                newV = _startingSundayStr;
            } else { // 仍在七七之內
                for ( i = 0; i < dateArray.length; i++ ) {
                    var d = dateArray[i].split( /[\-\/]/ );
                    dV = new Date( d[0], (d[1]-1), d[2] ).getTime();
                    if ( ( _startingSundayV <= dV ) && ( dV <= deceased49V ) ) {
                        newVx = appendUnique( dateArray[i], newVx ); continue;
                    }
                    alert( ignoreBeyond49 );
                }
                if (newVx == '') {
                    $(this).val(x); if ( x == pmptV ) $(this).attr( 'data-pmptv', '');
                    return;
                }
                newV = newVx;
            }   // 仍在七七之內
        } // for 功德迴向
    } // data change in 功德迴向日期 field
	if ( newV != oldV ) {
        $(this).val( newV );
        $(this).attr( "data-changed", "true" );
	}
} // function hdlr_dataChg()

function hdlr_tabClick() {
    var rqTblName = $(this).attr("data-table");
    var dirtyCells = $("tbody input[type=text][data-changed=true]").length;
    if ( rqTblName == _tblName ) return false; /* nothing to do */
    if ( ( dirtyCells > 0 ) && ( !confirm( _alertUnsaved ) ) ) return;
    _tblName = rqTblName; /* Global: _tblName, _usrName, _icoName */
    _maxRqSundays = ( _tblName == 'sundayQifu') ? 3 : 7;
    $(".tabMenu th").removeClass("active").css("border", "1px solid white");
    $(this).addClass("active").css("border-bottom", "1px solid green");
    if ( $(this).is(":first-child") ) {
        $(this).css("border-left", "1px solid green");
        $(this).closest("tr").find("th:last-child").css("border-right", "1px solid #00b300");
    } else if ( $(this).is(":last-child") ) {
        $(this).css("border-right", "1px solid green");
        $(this).closest("tr").find("th:first-child").css("border-left", "1px solid #00b300");
    } else {
        $(this).closest("tr").find("th:last-child").css("border-right", "1px solid #00b300");
        $(this).closest("tr").find("th:first-child").css("border-left", "1px solid #00b300");
    }
    /* load / show tab content, sundayQifu, sundayMerit, or Dashboard information here */
    $("#tabDataFrame").find("*").unbind(); $("#tabDataFrame").empty();
    switch ( _tblName ) {
    case 'sundayRule':
        $("#tabDataFrame").load("./sundayRules.php #ruleText");
        break;
    case 'sundayQifu':
    case 'sundayMerit':
        loadTblData( _tblName, ( ( _icoName == null ) ? _sessUsr : _icoName ), "tabDataFrame" );
        break;
    case 'sundayGongDeZhu':
        alert( "Sunday Gong De Zhu data for " + _sessUsr + " will be loaded");
        break;
    } // switch()
} // function tabClick()

function hdlr_addRow() {
    var dataBody = $("table.dataRows tbody");
    var newRow = _pilotRow.clone();
    var newRowDataCells = newRow.find("input[type=text]");

    newRowDataCells.on( 'blur', hdlr_dataChg );
    newRowDataCells.on( 'focus', hdlr_onFocus );
    newRow.find("input.insBtn").on( 'click', hdlr_insBtn );
    dataBody.append( newRow );
} // function hdlr_addRow()

function hdlr_delAll() {
    var delXAlert = ( _sessLang == SESS_LANG_CHN ) ? '刪除的資料將無法恢復，請確認！'
                                                   : 'Deleted data cannot be undone, please confirm！';
    var ajaxData = {}, dbInfo = {};
    if ( !confirm( delXAlert ) ) return;
    dbInfo[ 'tblName' ] = _tblName;
	dbInfo[ 'rqstr' ] = ( _icoName != null ) ? _icoName : _sessUsr;
	ajaxData [ 'dbReq' ] = 'dbDELX';
    ajaxData [ 'dbInfo' ] = JSON.stringify ( dbInfo );
    $.ajax({
        url: "./ajax-qifuDB.php",
		method: 'POST',
        data: ajaxData,
        success: function ( rsp ) {
            rspX = isJSON( rsp );
            if ( !rspX ) {
                alert ( "hdlr_delAll() received:\n" + rsp ); return;
            }
            for ( X in rspX ) {
                switch ( X ) {
                    case 'URL':
                        location.replace( rspX[ X ] );
                        return;
                    case 'delSUCCESS':
                        alert( rspX [ X ] );
                        $("table.dataRows").find("*").unbind();
                        $("table.dataRows").find("tr").remove();
                        return;
                    default: // error conditions, details later
                        alert( 'Delete Error occurred; received: "' + rspX[X] + '"' );
                        return;
                } // switch()
            } // loop over received components
        }, // End of AJAX SUCCESS
        error: function (jqXHR, textStatus, errorThrown) {
			alert( "hdlr_delAll()\tError Status:\t"+textStatus+"\t\tMessage:\t\t"+errorThrown+"\n" );
		} // End of ERROR Handler    
    }); // End of AJAX Call
} // function hdlr_delAll()

function hdlr_editBtn() {
    // when clicked, besides editing, change itself to "upd" & "can" buttons
    var updBtnVal = ( _sessLang == SESS_LANG_CHN ) ? "更新" : "Upd";
    var canBtnVal = ( _sessLang == SESS_LANG_CHN ) ? "取消" : "Can";
    var delBtnVal = ( _sessLang == SESS_LANG_CHN ) ? '刪除' : 'Del';
    var spacer = "<span>&nbsp;&nbsp;</span>";
    var updBtn = $('<input class="updBtn" type="button" value="' + updBtnVal + '">');
    var canBtn = $('<input class="canBtn" type="button" value="' + canBtnVal + '">');
    var delBtn = $('<input class="delBtn" type="button" value="' + delBtnVal + '">');
    var dataCells = $(this).closest("tr").find("input[type=text]");
    var lastTd = $(this).closest("td");
    dataCells.prop( 'disabled', false );
    dataCells.on('blur', hdlr_dataChg );
    lastTd.find("*").unbind();
    lastTd.empty();
    lastTd.append( updBtn, spacer, canBtn, spacer, delBtn );
    lastTd.find(".updBtn").on( 'click', hdlr_updBtn );
    lastTd.find(".canBtn").on( 'click', hdlr_canBtn );
    lastTd.find(".delBtn").on( 'click', hdlr_delBtn );
} // function hdlr_editBtn()

function hdlr_delBtn() {
	var delAlert = ( _sessLang == SESS_LANG_CHN ) ? '刪除的資料將無法恢復，請確認！'
                                                  : 'A deleted row cannot be undone, please confirm！';
    var ajaxData = {}, dbInfo = {}, tblFlds = {};
    var thisRow = $(this).closest("tr");

    if ( ! confirm( delAlert ) ) return;
    tblFlds [ thisRow.attr("data-keyn") ] = thisRow.attr("id");
    dbInfo[ 'tblName' ] = _tblName;
	dbInfo[ 'tblFlds' ] = tblFlds;
	dbInfo[ 'rqstr' ] = ( _icoName != null ) ? _icoName : _sessUsr;
	ajaxData [ 'dbReq' ] = 'dbDEL';
    ajaxData [ 'dbInfo' ] = JSON.stringify ( dbInfo );
    $.ajax({
        url: "./ajax-qifuDB.php",
		method: 'POST',
        data: ajaxData,
        success: function ( rsp ) {
            rspX = isJSON( rsp );
            if ( !rspX ) {
                alert ( "hdlr_delBtn() received:\n" + rsp ); return;
            }
            for ( var X in rspX ) {
                switch ( X ) {
                    case 'URL':
                        location.replace( rspX[ X ] );
                        return;
                    case 'delSUCCESS':
                        alert( rspX[X] );
                        thisRow.find("*").unbind();
                        thisRow.remove();
                        return;
                    default: // Error cases - details later
                        alert( 'Delete Error occurred; received: "' + rspX[X] + '"' );
                        return;
                } // End of switch (X)
            } // loop through rspX elements
        }, // End of AJAX SUCCESS Handler
        error: function (jqXHR, textStatus, errorThrown) {
			alert( "hdlr_delBtn()\tError Status:\t"+textStatus+"\t\tMessage:\t\t"+errorThrown+"\n" );
		} // End of ERROR Handler
    }); // End AJAX Call
} // function hdlr_delBtn()

function hdlr_insBtn() { // alert("hdlr_insBTN() clicked"); alert( $(this).closest("tr").html() );
    var alertText = ( _sessLang == SESS_LANG_CHN ) ? "請輸入完整的資料" : "Please enter complete data";
    var editBtnVal = ( _sessLang == SESS_LANG_CHN ) ? '更改' : 'Edit';
    var delBtnVal = ( _sessLang == SESS_LANG_CHN ) ? '刪除' : 'Del';
    var editBtn = $('<input class="editBtn" type="button" value="' + editBtnVal + '">');
    var delBtn = $('<input class="delBtn" type="button" value="' + delBtnVal + '">');
    var spacer = "<span>&nbsp;&nbsp;</span>";
    var insBtn = $(this);
    var thisRow = $(this).closest("tr");
    var lastTd = thisRow.find("td:last");
    var cellsChanged = thisRow.find("input[data-changed=true]");

    var ajaxData = {}, dbInfo = {}, tblFlds = {};

    if ( cellsChanged.length == 0 ) return;
	if ( cellsChanged.length != thisRow.find("input[type=text]").length ) { // incomplete data input
		alert( alertText );
		return;
    }
    
    cellsChanged.each( function() {
        tblFlds [ $(this).attr("data-fldn") ] = $(this).val();
    });

    dbInfo[ 'tblName' ] = _tblName;
    dbInfo[ 'tblFlds' ] = tblFlds;
    dbInfo[ 'rqstr' ] = ( _icoName != null ) ? _icoName : _sessUsr;
    ajaxData[ 'dbReq'] = 'dbINS';
    ajaxData[ 'dbInfo' ] = JSON.stringify( dbInfo );
    $.ajax({
        url: "./ajax-qifuDB.php",
		method: 'POST',
		data: ajaxData,
        success: function( rsp ) {
            rspX = isJSON( rsp );
            if ( !rspX ) {
                alert ( "hdlr_insBtn() received:\n" + rsp); return;
            }
            for ( var X in rspX ) {
                switch ( X ) {
                    case 'URL':
                        location.replace( rspX[ X ] );
                        return;
                    case 'insSUCCESS': // rspX[ X ] has the tuple ID
                        var ackMsg = ( _sessLang == SESS_LANG_CHN ) ? "祈福迴向資料加入完畢！" : "Record Inserted!";
                        thisRow.attr( 'id', rspX[ X ] );
                        cellsChanged.each( function() {
                            $(this).attr( {"oldv": $(this).val(), "value": $(this).val(), "data-changed": "false"} );
                        });
                        thisRow.find("input[type=text]").prop("disabled", true).removeAttr('data-pmptv');
                        thisRow.find("*").unbind();
                        lastTd.empty().append( editBtn, spacer, delBtn );
                        lastTd.find(".editBtn").on('click', hdlr_editBtn );
                        lastTd.find(".delBtn").on('click', hdlr_delBtn );
                        alert( ackMsg );
                        return;
                    default: // Error cases - details later
                        alert( 'Insert Error occurred; received: "' + rspX[X] + '"' );
                        return;
                } // switch on X
            } // End looping over rspX elements
        }, // End of Success Handler
        error: function (jqXHR, textStatus, errorThrown) {
			alert( "hdlr_insBtn()\tError Status:\t"+textStatus+"\t\tMessage:\t\t"+errorThrown+"\n" );
		} // End of ERROR Handler
    }); // End of AJAX call
} // function hdlr_insBtn()

function hdlr_updBtn() {
    var ackNC = ( _sessLang == SESS_LANG_CHN ) ? "沒有任何更動！" : "Nothing Changed!";
    var ackMsg = ( _sessLang == SESS_LANG_CHN ) ? "祈福迴向資料更新完畢！" : "Update Completed!";
    var errMsg = ( _sessLang == SESS_LANG_CHN ) ? "祈福迴向資料更新發生錯誤！" : "Update Failed!";
    var editBtnVal = ( _sessLang == SESS_LANG_CHN ) ? '更改' : 'Edit';
    var delBtnVal = ( _sessLang == SESS_LANG_CHN ) ? '刪除' : 'Del';
    var editBtn = $('<input class="editBtn" type="button" value="' + editBtnVal + '">');
    var delBtn = $('<input class="delBtn" type="button" value="' + delBtnVal + '">');
    var spacer = "<span>&nbsp;&nbsp;</span>";
    var thisRow = $(this).closest("tr");
    var lastTd = thisRow.find("td:last");
    var cellsChanged = thisRow.find("input[data-changed=true]");
    var tblFlds = {}, ajaxData = {}, dbInfo = {};

    if ( cellsChanged.length == 0 ) {
        alert( ackNC );
        thisRow.find("input[type=text]").prop( "disabled", true ); // disable Edit
        thisRow.find("*").unbind();
        lastTd.empty().append( editBtn, spacer, delBtn );
        lastTd.find(".editBtn").on( 'click', hdlr_editBtn );
        lastTd.find(".delBtn").on( 'click', hdlr_delBtn );
        return;
    }

    tblFlds[ thisRow.attr( 'data-keyn' ) ] = thisRow.attr( 'id' ); // getting tuple (Key Name, Value)
    cellsChanged.each( function () {
        tblFlds [ $(this).attr("data-fldn") ] = $(this).val();
    });
    dbInfo[ 'tblName' ] = _tblName;
    dbInfo[ 'tblFlds' ] = tblFlds;
    dbInfo[ 'rqstr' ] = ( _icoName != null ) ? _icoName : _sessUsr;
    dbInfo[ 'refDate' ] = _startingSundayStr;
    ajaxData[ 'dbReq'] = 'dbUPD';
    ajaxData[ 'dbInfo' ] = JSON.stringify( dbInfo );
    $.ajax({
        url: "./ajax-qifuDB.php",
		method: 'POST',
        data: ajaxData,
        success: function ( rsp ) {
            var rspX = isJSON ( rsp );
            if ( !rspX ) {
                alert( "Line 564 hdlr_updBtn() received:\n" + rsp ); return;
            }
            for ( X in rspX ) {
                switch ( X ) {
                    case 'URL':
                        location.replace( rspX[ X ] );
                        return;
                    case 'updSUCCESS':
                        cellsChanged.each(function(i) {
                            $(this).attr( { "data-oldv": $(this).val(), "value": $(this).val() } ); // remember the current value
                        }); // cellsChanged
                        alert( ackMsg );
                        cellsChanged.attr("data-changed", "false");
            			thisRow.find("input[type=text]").prop( "disabled", true ); // disable Edit
                        thisRow.find("*").unbind();
                        lastTd.empty().append( editBtn, spacer, delBtn );
                        lastTd.find(".editBtn").on( 'click', hdlr_editBtn );
                        lastTd.find(".delBtn").on( 'click', hdlr_delBtn );
                        return;
                    default: // Error cases - details later
                        alert( 'Insert Error occurred; received: "' + rspX[X] + '"' );
                        cellsChanged.each(function(i) {
							$(this).val( $(this).attr( "data-oldv" ) ); // restore its old value
                        }); // cellsChanged
                        alert( errMsg ); 
                        cellsChanged.attr("data-changed", "false");
            			thisRow.find("input[type=text]").prop( "disabled", true ); // disable Edit
                        thisRow.find("*").unbind();
                        lastTd.empty().append( editBtn, spacer, delBtn );
                        lastTd.find(".editBtn").on( 'click', hdlr_editBtn );
                        lastTd.find(".delBtn").on( 'click', hdlr_delBtn );
                        return;
                } // switch()
            } // looping over received components
        }, // End of AJAX SUCCESS Handler
        error: function (jqXHR, textStatus, errorThrown) {
			alert( "hdlr_insBtn()\tError Status:\t"+textStatus+"\t\tMessage:\t\t"+errorThrown+"\n" );
		} // End of ERROR Handler
    }); // End of AJAX Call
} // function hdlr_updBtn()

function hdlr_canBtn() {
    var editBtnVal = ( _sessLang == SESS_LANG_CHN ) ? '更改' : 'Edit';
    var delBtnVal = ( _sessLang == SESS_LANG_CHN ) ? '刪除' : 'Del';
    var editBtn = $('<input class="editBtn" type="button" value="' + editBtnVal + '">');
    var delBtn = $('<input class="delBtn" type="button" value="' + delBtnVal + '">');
    var spacer = "<span>&nbsp;&nbsp;</span>";
    var cells = $(this).closest("tr").find("input[data-changed=true]");
    var td = $(this).closest("td");
	if ( cells.length > 0 ) {
		cells.each( function () { // Restore the old value
			$(this).val( $(this).attr( "data-oldv" ) );
			$(this).attr( "data-changed", "false" );
		}); // forEach
	}
	$(this).closest("tr").find("input[type=text]").prop( "disabled", true );
    td.find("*").unbind(); td.empty();
    td.append( editBtn, spacer, delBtn );
    td.find(".editBtn").on( 'click', hdlr_editBtn );
    td.find(".delBtn").on( 'click', hdlr_delBtn );
} // function hdlr_canBtn()

$(document).ready(function() {
    $(".future").on( 'click', futureAlert );
    $("th[data-urlIdx]").on( 'click', function() {
        location.replace( _url2Go[ $(this).attr( "data-urlIdx" ) ]);
    });
    readSundayParam();
})