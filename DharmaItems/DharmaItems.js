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

var _dt_diMOP = null;
var _dt_diAlert = null;
var _dt_diShipping = null;
var _dt_diAppForm = null;

var _myAddrIDs = {};

function isJSON( str ) {
    try {
        var x = JSON.parse(str);
        if ( x && typeof x === "object" ) return x;
    } catch (e) { /* do nothing */ }
    return false;
} // isJSON()

function chkAddrInfoRqmt( form ) {
    // when insert a new Address Info, this function validates all required fields are filled
    nonBlnkFlds = array ( 'Addressee', 'TelNo', 'Email', 'StNum', 'City', 'US_State', 'ZipCode');
}

function readDI_Param() {
    var ajaxData = {}, dbInfo = {}, rspX = null;
    dbInfo[ 'tblName' ] = 'di_Param'; // *** Not Used
    ajaxData[ 'dbReq' ] = 'readDI_Param';
    ajaxData[ 'dbInfo' ] = JSON.stringify ( dbInfo );
    $.ajax({
        url: "./ajax-DharmaItemsDB.php",
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
                case 'dt_diShipping':
                    _dt_diShipping = rspX[X];
                    break;
                case 'dt_diAppForm':
                    _dt_diAppForm = rspX[X];
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
    $(".tabMenu th[data-table=" + defaultTbl + "]").trigger( 'click' );
} // init_done() - ready for user request

/********************************************************************************
 * Function to load the Shipping Info Form for Dharma Items Delivery            *
 ********************************************************************************/
function loadShippingInfoForm( primary ) { // alert( "Passed in 'Primary' = " + primary );
    var ajaxData = {}, dbInfo = {}, tblFldN = {}, rspX = null;
    dbInfo[ 'tblName' ] = 'UsrAddr';
    dbInfo[ 'usrName' ] = _sessUsr;
    dbInfo[ 'Prim'] = primary;
    ajaxData[ 'dbReq' ] = 'dbReadAddrForm';
    ajaxData[ 'dbInfo' ] = JSON.stringify ( dbInfo );
    $.ajax({
        url: "./ajax-DharmaItemsDB.php",
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
                case 'addrIDs':
                /* debugging during development
                    _myAddrIDs = rspX[X];
                    $.each( _myAddrIDs, function( key, value ) {
                        alert( 'In loadShippingInfoForm() - ' + key + ': ' + value );
                    });
                */
                    break;
                default: // the Form data in HTML format
                    $("#tabDataFrame").html( rspX[X] );
                    $("#shippingInfo").find("*").unbind();
                    $("input[type=text]").bind( 'blur', hdlr_dataChg_Shipping );
                    $("input[type=text]").bind( 'focus', hdlr_onFocus_Shipping );
                    $("input[type=checkbox]").bind('change', hdlr_chkBox_Shipping );
                    $("#ldAltShippingInfo").bind( 'click', hdlr_ldAltShippingInfo );
                    $("#delShippingInfo").bind( 'click', hdlr_shippingInfoDel );
                    $("#ldDharmaAppForm").bind( 'click', hdlr_go2DharmaAppForm );
                    $("form#shippingInfo").on( 'submit', hdlr_shippingInfoSubmit );
                    return;
                } // switch( X )
            } // for()
        }, // success handler
        error: function ( jqXHR, textStatus, errorThrown ) {
            alert( "loadShippingForm()\tError Status:\t"+textStatus+"\t\tMessage:\t\t"+errorThrown+"\n" );
        } // error handler
    }); // ajax call
} // loadShippingInfoForm()

/********************************************************************************
 * Handler: when the checkbox of Primary Shipping address is checked/unchecked  *
 ********************************************************************************/
function hdlr_chkBox_Shipping() {
    var currV = $(this).is(":checked") ? 1 : 0;
    var savedV = $(this).attr("data-savedV");

    if ( savedV === undefined ) { // the checkbox is touched the first time
        savedV = currV ? 0 : 1; // save the original value which is the opposite to the current value
        $(this).attr("data-savedV", savedV );
    }
    if ( currV != savedV ) {
        $(this).attr("data-changed", "true");
        $(this).attr("data-primAction", currV ? 'setPrimary' : 'unsetPrimary' );
    } else {
        $(this).removeAttr("data-changed");
        $(this).removeAttr("data-primAction");
    }
    $(this).attr( 'value', currV );
    $(this).prop( 'checked', currV ? true : false );
    return;
} // hdlr_chkBox_Shipping()

/********************************************************************************
 * Handler: when a non-checkbox input field was traversed - data changed?       *
 ********************************************************************************/
function hdlr_dataChg_Shipping() {
    var newV = $(this).val().trim().replace( /<br>$/gm, '');
    var savedV = $(this).attr("data-savedV");

    if ( newV != savedV ) {
        $(this).attr("data-changed", "true" );
        $(this).val( newV );
    } else {
        $(this).removeAttr("data-changed");
    }
    return;
} // hdlr_dataChg_Shipping()

/********************************************************************************
 * Handler: when a non-checkbox input field was focused (mouse clicked in)      *
 ********************************************************************************/
function hdlr_onFocus_Shipping() {
    var currV = $(this).val().trim().replace( /<br>$/gm, '');

    if ( $(this).attr("data-savedV") === undefined ) { // the cell has never been touched
        if ( currV.length > 0 ) {
            $(this).attr( "data-savedV", currV );
        }
    }
} // hdlr_onFocus_Shipping()

/********************************************************************************
 * Handler: when load the Alternative Shipping Info button is clicked           *
 ********************************************************************************/
function hdlr_ldAltShippingInfo() {
    var dirtyCells = $("tbody input[data-changed]").length;
    if ( ( dirtyCells > 0 ) && ( !confirm( _alertUnsaved ) ) ) return;
    loadShippingInfoForm( ! $("input[type=checkbox]").prop('checked') );
    return;
} // hdlr_ldAltShippingInfo()

/********************************************************************************
 * Handler: when 'Continue to App Form' button is clicked                       *
 ********************************************************************************/
function hdlr_go2DharmaAppForm() {
    var rqTblName = $(this).attr("data-table");
    var dirtyCells = $("tbody input[data-changed]").length;
    if ( rqTblName == _tblName ) return false; /* nothing to do */
    if ( ( dirtyCells > 0 ) && ( !confirm( _alertUnsaved ) ) ) return;
    $(".tabMenu th[data-table=" + rqTblName + "]").trigger( 'click' );
} // hdlr_go2DharmaAppForm()

/********************************************************************************
 * Handler: when 'Save/Update' button is clicked                                *
 ********************************************************************************/
function hdlr_shippingInfoSubmit( e ) {
    e.preventDefault();
    var ajaxData = {}, dbInfo = {}, tblFlds = {}, rspX = null;
    var cellsChanged = $(this).find("tbody input[data-changed=true]");
    var cellsNoBlank = $(this).find("input[type=text]").filter(":not(input[name=Unit])");
    var myHdlr = $(this).prop('action');
    var thisAddrID = $("input[name=AddrID]").val();

    if (cellsChanged.length == 0) return;

    dbInfo[ 'tblName' ] = $("input[name=tblName]").val();
    dbInfo[ 'usrName' ] = _sessUsr;
    tblFlds[ 'AddrID' ] = thisAddrID;
    if (thisAddrID == '') { // will be an Insert
        if (cellsChanged.length < cellsNoBlank.length ) {
            var errMsg = ( _sessLang == SESS_LANG_CHN ) ? "除了單位號碼之外，所有資料必須齊全！"
                                                        : "All fields except 'Unit' must be filled!";
            alert( errMsg ); return false;
        }
    }
    cellsChanged.each( function(i) { // get changed field name and value
        if ($(this).attr("name") == 'Prim' ) {
            dbInfo[ 'primAction' ] = $(this).attr("data-primAction");
            dbInfo[ 'primActAddrID' ] = tblFlds[ 'AddrID' ]; // could be '' - new addr to be inserted
        } else {
            tblFlds [ $(this).attr("name") ] = $(this).val();
        }
    }); // each
    dbInfo[ 'tblFlds' ] = tblFlds;
    ajaxData[ 'dbReq' ] = ( tblFlds[ 'AddrID' ] != '' ) ? 'dbUPD_ShippingAddr' : 'dbINS_ShippingAddr';
    ajaxData[ 'dbInfo' ] = JSON.stringify ( dbInfo );
    $.ajax ({
        url: myHdlr,
        method: "post",
        data: ajaxData,
        success: function ( rsp ) {
            rspX = isJSON ( rsp );
            if ( !rspX ) {
                alert( 'Error Dharma Items JS after isJSON() call: ' + rsp ); return false;
            }
            for ( X in rspX ) {
                switch( X ) {
                case 'URL':
                    location.replace( rspX[X] );
                    return;
                case 'updSUCCESS': // falsify 'data-savedV' and 'data-changed' attribute
                    _myAddrIDs = rspX[X];
                /* help debugging during development
                    $.each( _myAddrIDs, function ( key, val ) {
                        alert("My AddrIDs after UPD: " + key + ' : ' + val );
                    });
                */
                    cellsChanged.each(function(i) {
                        $(this).attr( 'value', $(this).val() ); // make the current value default
                    }); // cellsChanged
                    // remove operational attributes as if it was just freshly loaded
                    cellsChanged.removeAttr('data-savedV').removeAttr('data-changed');
                    cellsChanged.removeAttr('data-primAction');
                    alertMsg = ( _sessLang == SESS_LANG_CHN) ? '寄送資料更新完畢！' : 'Shipping Address updated!';
                    alert( alertMsg );
                    return;
                case 'updFailed':
                    if ( rspX[X] == 'unsetPrimary' ) {
                        $("input[type=checkbox]").prop('checked', true);
                        $("input[type=checkbox]").attr('value', 0 );
                        $("input[type=checkbox]").removeAttr('data-savedV').removeAttr('data-changed');
                        $("input[type=checkbox]").removeAttr('data-primAction');
                    }
                    cnt = rspX['errCount'];
                    for (i = 0; i < cnt; i++ ) {
                        alert( rspX['errRec'][i]);
                    }
                    return;
                case 'insSUCCESS':
                    _myAddrIDs = rspX[X];
                /* help debugging during development
                    $.each( _myAddrIDs, function ( key, val ) {
                        alert("My AddrIDs after Insertion: " + key + ' : ' + val );
                    });
                */
                    tupID = rspX['tupID'];
                    if ( tupID == _myAddrIDs['prim'] ) {
                        $("input[type=checkbox").prop('checked', true );
                        $("input[type=checkbox").attr('value', 1 );
                    }
                    $("input[name=AddrID]").val( rspX['tupID'] );
                    $("input[name=AddrID]").attr( 'value', rspX['tupID']);
                    cellsChanged.each(function(i) {
                        $(this).attr( 'value', $(this).val() ); // make the current value default
                    }); // cellsChanged
                    // remove operational attributes as if it was just freshly loaded
                    cellsChanged.removeAttr('data-savedV').removeAttr('data-changed');
                    cellsChanged.removeAttr('data-primAction');
                    alertMsg = ( _sessLang == SESS_LANG_CHN) ? '寄送資料添加完畢！' : 'Shipping Address Added!';
                    alert( alertMsg );
                    return;
                case 'errCount':
                    errCnt = rspX[X];
                    errMsg = rspX[ 'errRec'];
                    for (i = 0; i < errCnt; i++) {
                        alert( errMsg[ i ] );
                    }
                    return;
                default:
                    alert ( 'Unknown Data Received: ' + rspX [X]);
                    break;
                } // switch (X)
            } // for ()
        }, // success handler
        error: function ( jqXHR, textStatus, errorThrown ) {
            alert( "hdlr_shippingInfoSubmit()\tError Status:\t"+textStatus+"\t\tMessage:\t\t"+errorThrown+"\n" );
        } // error handler
    }); // ajax call
} // hdlr_shippingInfoSubmit()

/***************************************************************************
 * Handler: when 'Delete' button is clicked                                *
 ***************************************************************************/
function hdlr_shippingInfoDel() {
    var alrtMsg = ( _sessLang == SESS_LANG_CHN) ? '此一運送地址尚未註冊！' : 'Unregistered Shipping Address!';
    var cfrmAlt = ( _sessLang == SESS_LANG_CHN) ? "請確定要刪除此一地址？" : "Please confirm to delete!";
    var cfrmPrim = ( _sessLang == SESS_LANG_CHN) ? "請確定要刪除主要地址？" : "Please confirm to delete primary address!";
    var ajaxData = {}, dbInfo = {}, tblFlds = {}, rspX = null;
    var myHdlr = $("form").prop('action'); // "./ajax-DharmaItemsDB.php";
    var thisAddrID = $("input[name=AddrID]").val();
    var cfrmMsg = '';

    if ( thisAddrID == '' ) { alert( alrtMsg ); return; }
    cfrmMsg = ( thisAddrID == _myAddrIDs[ 'prim' ] ) ? cfrmPrim : cfrmAlt;
    if ( !confirm( cfrmMsg ) ) return;

    dbInfo[ 'UsrName' ] = _sessUsr;
    dbInfo[ 'tblName' ] = $("input[name=tblName]").val();
    tblFlds[ 'AddrID' ] = thisAddrID;
    dbInfo[ 'tblFlds' ] = tblFlds;
    ajaxData[ 'dbReq' ] = 'dbDEL_ShippingAddr';
    ajaxData[ 'dbInfo' ] = JSON.stringify ( dbInfo );
    $.ajax ({
        url: myHdlr,
        method: "post",
        data: ajaxData,
        success: function ( rsp ) {
            rspX = isJSON ( rsp );
            if ( !rspX ) {
                alert( 'Error after isJSON() call in Delete Hdlr: ' + rsp ); return false;
            };
            for ( X in rspX ) {
                switch( X ) {
                case 'URL':
                    location.replace( rspX[X] );
                    return;
                case 'delSUCCESS': // returns remaining Address Info, or a blank form
                    _myAddrIDs = rspX[X];
                /* help debug during development
                    $.each( _myAddrIDs, function ( key, val ) {
                        alert("My AddrIDs after Del: " + key + ' : ' + val );
                    });
                /* */
                    delMsg = ( _sessLang == SESS_LANG_CHN ) ? "寄送地址刪除完畢！" : "Address Deleted!";
                    alert( delMsg );
                    $("#shippingInfo").find("*").unbind();
                    loadShippingInfoForm( true );
                    return;
                case 'errCount':
                    count = rspX[X];
                    errRec = rspX['errRec'];
                    for ( i = 0; i < count; i++ ) {
                        alert( recRec[i]);
                    }
                    break;
                }; // switch()
            }; // for loop
        }, // success handler 
        error: function ( jqXHR, textStatus, errorThrown ) {
            alert( "hdlr_shippingInfoDel()\tError Status:\t"+textStatus+"\t\tMessage:\t\t"+errorThrown+"\n" );
        } // error handler
    });
} // function hdlr_shippingInfoDel()

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
        break;
    case 'addrInfoForm':
        $("#dt").text( _dt_diShipping );
        $("#dtAlert").text('');
        loadShippingInfoForm( true );
        break;
    case 'dharmaItemsReqForm':
        $("#dt").text( _dt_diAppForm );
        $("#dtAlert").text('');
        alert( "Will load Request Application Form here" );
        break;
    } // switch( _tblName )
} // tabClick()