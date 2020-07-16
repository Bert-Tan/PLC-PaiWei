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

function isJSON( str ) {
    try {
        var x = JSON.parse(str);
        if ( x && typeof x === "object" ) return x;
    } catch (e) { /* do nothing */ }
    return false;
} // isJSON()

function init_done() {
    var defaultTbl = "dharmaItemsRules";
    if ( _tblName != null )  {
        defaultTbl = _tblName;
        _tblName = null;
    }
    
    $(".tabMenu th").on( 'click', hdlr_tabClick );
    $(".tabMenu th[data-table=" + defaultTbl + "]").trigger( 'click' );
} // function init_done() - ready for user request

function hdlr_tabClick() {
    var rqTblName = $(this).attr("data-table");
    var dirtyCells = $("tbody input[type=text][data-changed=true]").length;
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
    /* load / show tab content, sundayQifu, sundayMerit, or Dashboard information here */
    $("#tabDataFrame").find("*").unbind(); $("#tabDataFrame").empty();
    switch ( _tblName ) {
    case 'dharmaItemsRules':
        $("#tabDataFrame").load("./DharmaItemsRules.php #ruleText");
        break;
    case 'dharmaItemsRqrInfo':
        alert( "Will load Requestor Address Form here" );
        break;
    case 'dharmaItemsReqForm':
        alert( "Will load Request Application Form here");
        break;
    } // switch()
} // function tabClick()