/****************************************************************
*                    Global variables                           *
* These are not needed because they were in the DI_common.js    *
* included before this JS file                                  *
*****************************************************************/
/*
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

var _dt_diMOP = null;
var _dt_diAlert = null;
var _dt_diShipping = null;
var _dt_diAppForm = null;

var _myAddrIDs = {};

function isJSON( str ) {
    try {
        var x = JSON.parse(str);
        if ( x && typeof x === "object" ) return x;
    } catch (e) { } // do nothing
    return false;
} // isJSON()
*/

/********************************************************************************
 * Function to load the Form for Chinese Book Items Request                     *
 ********************************************************************************/
function loadBkRqForm_C() {
    var ajaxData = {}, dbInfo = {}, tblFldN = {}, rspX = null;
    dbInfo[ 'tblName' ] = 'INVT_BK_C';
    dbInfo[ 'usrName' ] = _sessUsr;
    ajaxData[ 'dbReq' ] = 'dbReadBkList';
    ajaxData[ 'dbInfo' ] = JSON.stringify ( dbInfo );
alert( 'loadBkRqForm_C() formulated request: ' + ajaxData[ 'dbInfo'] ); return false;
    $.ajax({
        url: "./ajax-DI_rqBkItemsDB.php",
        method: "post",
        data: ajaxData,
        success: function( rsp ) { // alert( "Received:\n" + rsp ); return;
            rspX = isJSON ( rsp );
            if ( !rspX ) {
                alert( rsp ); return false;
            }
            for ( X in rspX ) {
                switch( X ) {
                case 'URL':
                    location.replace( rspX[X] );
                    return;
                default: // the Form data in HTML format
                    return;
                } // switch( X )
            } // for()
        }, // success handler
        error: function ( jqXHR, textStatus, errorThrown ) {
            alert( "loadBkRqForm_C()\tError Status:\t"+textStatus+"\t\tMessage:\t\t"+errorThrown+"\n" );
        } // error handler
    }); // ajax call
} // loadBkRqForm_C()

/********************************************************************************
 * Function to load the Form for English Book Items Request                     *
 ********************************************************************************/
 function loadBkRqForm_E() {
    var ajaxData = {}, dbInfo = {}, tblFldN = {}, rspX = null;
    dbInfo[ 'tblName' ] = 'INVT_BK_E';
    dbInfo[ 'usrName' ] = _sessUsr;
    ajaxData[ 'dbReq' ] = 'dbReadBkList';
    ajaxData[ 'dbInfo' ] = JSON.stringify ( dbInfo );
alert( 'loadBkRqForm_E() formulated request: ' + ajaxData[ 'dbInfo'] ); return false;
    $.ajax({
        url: "./ajax-DI_rqBkItemsDB.php",
        method: "post",
        data: ajaxData,
        success: function( rsp ) { // alert( "Received:\n" + rsp ); return;
            rspX = isJSON ( rsp );
            if ( !rspX ) {
                alert( rsp ); return false;
            }
            for ( X in rspX ) {
                switch( X ) {
                case 'URL':
                    location.replace( rspX[X] );
                    return;
                default: // the Form data in HTML format
                    return;
                } // switch( X )
            } // for()
        }, // success handler
        error: function ( jqXHR, textStatus, errorThrown ) {
            alert( "loadBkRqForm_E()\tError Status:\t"+textStatus+"\t\tMessage:\t\t"+errorThrown+"\n" );
        } // error handler
    }); // ajax call
} // loadBkRqForm_E()
