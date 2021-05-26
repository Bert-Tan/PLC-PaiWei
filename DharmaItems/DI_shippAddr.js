/***********************************************************
*               Include this aftr DI_common.js             *
************************************************************/

var _myAddrIDs = {};

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
        url: "./ajax-DI_shippAddrDB.php",
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
                    $(".ldDharmaAppForm").bind( 'click', hdlr_go2DharmaAppForm );
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
    var myHdlr = $("form").prop('action'); // "./ajax-DI_shippAddrDB.php";
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