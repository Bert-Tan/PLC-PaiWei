/****************************************************************
*                    Global variables                           *
* They not needed because they are in DI_common.js which is     *
* included before this JS file                                  *
*****************************************************************/

function hdlr_chkbox_bkItems() {
    var tr = $(this).closest("tr");
    alert( 'checkbox for ' + tr.attr("data-keyN") + ' = ' + tr.attr("data-keyV") );
} // function hdlr_chkbox_bkItems()

function hdlr_biHua_bkItems() {
    var stroke = $(this).val();
    var tblName = $(this).closest("table").attr("data-dbTblName");
//    alert( 'Bi Hua Selected: ' + stroke + '; database Table = "' + tblName + '"' );

} // function hdlr_biHua_bkItems()
/********************************************************************************
 * Function to load the Form for Chinese Book Items Request                     *
 ********************************************************************************/
function loadBkRqForm( tblname, stroke ) {
    var ajaxData = {}, dbInfo = {}, tblFldN = {}, rspX = null;
    var tabDataFrame = $("#tabDataFrame");

    // house cleaning work first 
    tabDataFrame.find("*").unbind();
    dbInfo[ 'tblName' ] = tblname;
    dbInfo[ 'stroke' ] = stroke;
    dbInfo[ 'usrName' ] = _sessUsr;
    ajaxData[ 'dbReq' ] = 'dbReadBkList';
    ajaxData[ 'dbInfo' ] = JSON.stringify ( dbInfo );
    $.ajax({
        url: "./ajax-DI_rqBkItemsDB.php",
        method: "post",
        data: ajaxData,
        success: function( rsp ) { //alert( "Received:\n" + rsp ); return;
            rspX = isJSON ( rsp );
            if ( !rspX ) {
                alert( rsp ); return false;
            }
            for ( X in rspX ) { // handling each responses
                switch( X ) {
                case 'URL':
                    location.replace( rspX[X] );
                    return;
                case 'BkList_Tbl': // alert( rspX[X] );
                    $("#tabDataFrame").html( rspX[ X ]);
                    break;
                } // switch( X )
            } // for()
            // binding handlers below
            $("input[type=checkbox]").on( 'change', hdlr_chkbox_bkItems );
            $("select").on( 'change', hdlr_biHua_bkItems );
        }, // success handler
        error: function ( jqXHR, textStatus, errorThrown ) {
            alert( "loadBkRqForm()\tError Status:\t"+textStatus+"\t\tMessage:\t\t"+errorThrown+"\n" );
        } // error handler
    }); // ajax call
} // loadBkRqForm()
