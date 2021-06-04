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
var _icoName = null, _usrName = null;
var _tblName = null, _tblSize = null;
var _pilotRow = null;
var _alertUnsaved = null;

var _tabDataFrameHeight = '69vh';
var _tabDataFrameHeight_noAlert = '74vh';
var _dt_diMOP = null;
var _dt_diAlert = null;
var _dt_diShippingTab = null;
var _dt_diBkTab = null;
var _dt_diStatuesTab = null;
var _dt_diScreensTab = null;
var _dt_diScrollsTab = null;

function isJSON( str ) {
    try {
        var x = JSON.parse(str);
        if ( x && typeof x === "object" ) return x;
    } catch (e) { /* do nothing */ }
    return false;
} // isJSON()

function readDI_Param() {
    var ajaxData = {}, dbInfo = {}, rspX = null;
    dbInfo[ 'tblName' ] = 'di_Param'; // *** Not Used
    ajaxData[ 'dbReq' ] = 'readDI_Param';
    ajaxData[ 'dbInfo' ] = JSON.stringify ( dbInfo );
    $.ajax({
        url: "./ajax-DI_commonDB.php",
        method: "post",
        data: ajaxData,
        success: function( rsp ) {
            rspX = isJSON ( rsp );
            if ( !rspX ) {
                alert( rsp ); return false;
            }
            for ( X in rspX ) {
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
                case 'dt_diMOP':
                    _dt_diMOP = rspX[X];
                    break;
                case 'dt_diAlert':
                    _dt_diAlert = rspX[X];
                    break;
                case 'dt_diShippingTab':
                    _dt_diShipping = rspX[X];
                    break;
                case 'dt_diBkTab':
                    _dt_diBkTab = rspX[X];
                    break;
                case 'dt_diStatuesTab':
                    _dt_diStatuesTab = rspX[X];
                    break;
                case 'dt_diScreensTab':
                    _dt_diScreensTab = rspX[X];
                    break;
                case 'dt_diScrollsTab':
                    _dt_diScrollsTab = rspX[X];
                    break;
                } // switch
            } // for loop
            /* done all the startup house-keeping; ready to edit */
            _alertUnsaved = ( _sessLang == SESS_LANG_CHN ) ? '未保存的更動會被丟棄！' : 'Unsaved Data will be LOST!';
            init_done();
        }, // success handler
        error: function ( jqXHR, textStatus, errorThrown ) {
            alert( "readDI_Param()\tError Status:\t"+textStatus+"\t\tMessage:\t\t"+errorThrown+"\n" );
        } // error handler
    }); // ajax call
} // readDI_Param()

function init_done() {
    var defaultTbl = "dharmaItemsRules";
    if ( _tblName != null )  {
        defaultTbl = _tblName;
        _tblName = null;
    }
    
    $(".tabMenu th").on( 'click', hdlr_tabClick );
    $(".tabMenu th.future").unbind().on( 'click', futureAlert );
    $(".tabMenu th[data-table=" + defaultTbl + "]").trigger( 'click' );
} // init_done() - ready for user request

function hdlr_tabClick() {
    var rqTblName = $(this).attr("data-table");
    var dirtyCells = $("tbody input[data-changed=true]").length;
    if ( rqTblName == _tblName ) return false; /* nothing to do */
    if ( ( dirtyCells > 0 ) && ( !confirm( _alertUnsaved ) ) ) return;
    _tblName = rqTblName; /* Global: _tblName, _usrName, _icoName */
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
    /* load / show tab content */
    $("#tabDataFrame").find("*").unbind(); $("#tabDataFrame").empty();
    switch ( _tblName ) {
    case 'dharmaItemsRules':
        $("#dt").text( _dt_diMOP );
        $("#dtAlert").text( _dt_diAlert );
        $("#tabDataFrame").load("./DharmaItemsRules.php #ruleText");
        $("#tabDataFrame").css({ 'overflow-y': 'auto', 'height': _tabDataFrameHeight });
        break;
    case 'addrInfoTab':
        $("#dt").text( _dt_diShipping );
        $("#dtAlert").text('');
        $("#tabDataFrame").css({ 'overflow-y': 'auto', 'height': _tabDataFrameHeight });
        loadShippingInfoForm( true );
        break;
    case 'INVT_BK_C': // Chinese Book Items Request
    case 'INVT_BK_E': // English Book Items Request
        $("#dt").text( _dt_diBkTab );
        $("#dtAlert").text('');
        $("#tabDataFrame").css({ 'overflow-y': '', 'height': _tabDataFrameHeight_noAlert });
        loadBkRqForm( _tblName, null );    /* this function is in DI_rqBkItems.js */   
        break;
    case 'INVT_STATUES':
        $("#dt").text( _dt_diStatuesTab );
        $("#dtAlert").text('');
        alert( "Will load Statues Application Form here" );
        break;
    case 'INVT_SCREENS':
        $("#dt").text( _dt_diScreensTab );
        $("#dtAlert").text('');
        alert( "Will load Screens Application Form here" );
        break;
    case 'INVT_SCROLLS':
        $("#dt").text( _dt_diScrollsTab );
        $("#dtAlert").text('');
        alert( "Will load Scrolls Application Form here" );
        break;
    } // switch( _tblName )
} // tabClick()