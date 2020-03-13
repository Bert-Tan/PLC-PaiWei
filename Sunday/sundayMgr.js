var _activeTab = null;

function isJSON( str ) {
    try {
        var x = JSON.parse(str);
        if ( x && typeof x === "object" ) return x;
    } catch (e) { /* do nothing */ }
    return false;
} // isJSON()

function loadSundayDashboard() {
    $("#tabDataFrame").load("./Templates/sundayDashboard.htm", function() {
        var ajaxData = {}, dbInfo = {};
        dbInfo[ 'tblName' ] = 'sundayParam'; // filler; will not be used
        ajaxData[ 'dbReq' ] = 'dbLoadSundayDashboard';
        ajaxData[ 'dbInfo' ] = JSON.stringify( dbInfo );
        $.ajax({
            url: '', method: 'post', data: ajaxData,
            success: function( rsp ) {
                rspX = isJSON( rsp );
                if ( ! rspX ) { alert( rsp ); return false; }
                for ( X in rspX ) {
                    switch ( X ) {
                    case 'URL':
                        location.replace( rspX[X] );
                        return;
                    case 'dashboardBody': // alert( rspX[X] );
                        $("table.dataRows tbody").replaceWith( rspX[X] );
                        break;
                    case 'inCareOfOptions':
                        $("table.dataHdr #toBeReplaced").replaceWith( rspX[X] );
                        break;
                    }
                } // loop over received elements
                // now connect handlers
                $("table.dataRows td[data-tblN]").on( 'click', hdlr_dataCellClick );
                $("#icoInputBtn").on( 'click', hdlr_icoInput );
                $("#icoSelBtn").on( 'click', hdlr_icoSelect );
            }, // SUCCESS handler
            error: function ( jqXHR, textStatus, errorThrown ) {
                alert( "loadSundayDueForm()\tError Status:\t"+textStatus+"\t\tMessage:\t\t"+errorThrown+"\n" );
            } // error handler
        }); // AJAX Call
    });
} // function loadSundayDashboard()

function dashboardRedirect( dbInfo ) {
    var ajaxData = {};
    ajaxData[ 'dbReq' ] = 'dashboardRedirect';
    ajaxData[ 'dbInfo' ] = JSON.stringify( dbInfo );
    $.ajax({
        url: '', method: 'post', data: ajaxData,
        success: function ( rsp ) {
            var rspX = isJSON( rsp );
            if ( ! rspX ) { alert ( rsp ); return false; }
            for ( X in rspX ) {
                switch (X) {
                case 'URL': // session timed out
                case 'redirect':
                //    alert("page will be redirected to: " + rspX[X]);
                    location.replace( rspX[ X ] );
                    return;
                } // switch();
            }
        }, // SUCCESS HANDLER
        error: function ( jqXHR, textStatus, errorThrown ) {
            alert( "hdlr_dataCellClick()\tError Status:\t"+textStatus+"\t\tMessage:\t\t"+errorThrown+"\n" );
        } // error handler
    }); // AJAX Call
} // function dashboardRedirect()

function hdlr_dataCellClick() {
    var thisRow = $(this).closest("tr");
    var dbInfo = {}
    dbInfo[ 'icoName' ] = thisRow.find("td[data-uName]").attr("data-uName");
    dbInfo[ 'icoNameType' ] = 'icoDerived';
    dbInfo[ 'tblName' ] = $(this).attr("data-tblN");
    dashboardRedirect( dbInfo );
    // dashboardRedirect() will not return here;
} // function hdlr_dataCellClick()

function hdlr_icoInput() {
    var dbInfo = {};
    var icoName = $(this).closest("th").find("#icoInput").val();
    if ( icoName == '請輸入蓮友識別名' ) {
        alert( icoName ); return false;
    }
    dbInfo[ 'icoName' ] = icoName;
    dbInfo[ 'icoNameType' ] = 'icoInput';
    dashboardRedirect( dbInfo );
    // dashboardRedirect() will not return here;
} // function hdlr_icoInput()

function hdlr_icoSelect() {
    var dbInfo = {};
    var icoName = $(this).closest("th").find("SELECT OPTION:SELECTED").val();
    if ( icoName.length == 0 ) {
        alert( '請點選蓮友識別名' ); return false;
    }
    dbInfo[ 'icoName' ] = icoName;
    dbInfo[ 'icoNameType' ] = 'icoSelected';
    dashboardRedirect( dbInfo );
    // dashboardRedirect() will not return here;    
} // hdlr_icoSelect()

function loadSundayDueForm() {
    $("#tabDataFrame").load("./Templates/sundayDueForm.htm", function() {
        // The template is loaded; now fill in the data if provisioned; use AJAX call
        var ajaxData = {}, dbInfo = {};
        var tupID = null;
        dbInfo[ 'tblName' ] = 'sundayParam';
        ajaxData[ 'dbReq' ] = 'dbReadSundayDue';
        ajaxData[ 'dbInfo' ] = JSON.stringify( dbInfo );
        $.ajax({
            url: '', method: 'post', data: ajaxData,
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
                    case 'expHH':
                        $("input[name=expHH]").val( rspX[X] );
                        $("input[name=expHH]").attr( 'value', rspX[X] );
                        break;
                    case 'expMM':
                        $("input[name=expMM]").val( rspX[X] );
                        $("input[name=expMM]").attr( 'value', rspX[X] );
                        break;
                    case 'err':
                        alert( rspX[X]);
                        return;
                    } // switch
                } // for loop
                if ( tupID != null ) {
                    $("input[name=expHH]").attr( 'data-oldV', $("input[name=expHH]").attr( 'value' ) );
                    $("input[name=expMM]").attr( 'data-oldV', $("input[name=expMM]").attr( 'value' ) );
                }
                // now connect handlers
                $("form input[type=text]").on( 'focus', hdlr_onFocus );
                $("form input[type=text]").on( 'blur', hdlr_dataChg );
                $("form").on( 'submit', hdlr_formSubmit );
            }, // Success Handler
            error: function ( jqXHR, textStatus, errorThrown ) {
                alert( "loadSundayDueForm()\tError Status:\t"+textStatus+"\t\tMessage:\t\t"+errorThrown+"\n" );
            } // error handler
        }); // AJAX Call
    });
} // function loadSundayDueForm()

function hdlr_onFocus() {
    var newV = $(this).val().trim().replace( /<br>$/gm, '');
    var pmptV = ( $(this).attr("data-pmptV") !== undefined ) ? $(this).attr("data-pmptv").trim() : '';
    if ( pmptV.length > 0 ) return;
    $(this).attr( 'data-pmptV', newV ); // save it before blanking out
	$(this).val( '' ); // blank out the field for input
	return;
} // function hdlr_onFocus()

function hdlr_dataChg() {
    var newV = $(this).val().trim().replace( /<br>$/gm, '');
    var oldV = $(this).attr( "data-oldV" ).trim();
    var pmptV = ( $(this).attr("data-pmptV") !== undefined ) ? $(this).attr("data-pmptv").trim() : '';
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
    if ( !newV.match(/[0-9]{2}/) ) {
        alert( "請輸入兩位數字" );
        $(this).val( x );   if ( x == pmptV ) $(this).attr( 'data-pmptv', '');
        return;
    }
    switch ( fldN ) { // sanity check data value
    case 'expHH':
        if ( Number( newV ) < 8 || Number( newV ) > 9 ) {
            alert( "截止時點應為八點 (08) 或九點 (09)" );
            $(this).val( x );   if ( x == pmptV ) $(this).attr( 'data-pmptv', '');
            return;
        }
        break;
    case 'expMM':
        if ( Number( newV ) != 0 && Number( newV ) != 30 ) {
            alert( "截止分點應為正點時分 (00) 或半點時分 (30)" );
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

function hdlr_formSubmit() {
    var ajaxData = {}, dbInfo = {};
    var sanity = 0;
    if ( $(this).find("input[data-changed=true]").length == 0 ) {
        alert("資料沒有任何更動！");
        return false;
    }
    $("input[type=text]").each( function () {
        if ( $(this).val().match(/[0-9]{2}/) ) sanity++;
    });
    if ( sanity < $("input[type=text]").length ) {
            alert("請輸入正確與完整的資料！"); return false;
    }

    dbInfo[ 'ID' ] = $(this).find("input[name=ID]").val();
    dbInfo[ 'expHH' ] = $(this).find("input[name=expHH]").val();
    dbInfo[ 'expMM' ] = $(this).find("input[name=expMM]").val();
    ajaxData[ 'dbReq' ] = 'dbSetSundayDue';
    ajaxData[ 'dbInfo' ] = JSON.stringify( dbInfo ); 
    ajaxData[ 'dbInfo' ] = JSON.stringify( dbInfo );
    $.ajax({
        url : '',
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
                    $("input[type=text]").each(function() {
                        var x = $(this).val();
                        $(this).attr({ 'data-oldV' : x, 'value' : x } );
                        $(this).removeAttr('data-pmptV');
                        $(this).removeAttr('data-changed');
                    });
                    alert("祈福迴向截止時分設定完畢！");
                    return;
                case 'ERR':
                    alert( rspX[X] );
                    return;
                }
            } // for loop
        }, // Success Handler
        error : function ( jqXHR, textStatus, errorThrown ) {
            alert( "loadSundayDueForm()\tError Status:\t"+textStatus+"\t\tMessage:\t\t"+errorThrown+"\n" );
        } // error handler
    }); // AJAX Call
    return false; // so the HTML submit won't fire
} // function hdlr_formSubmit()

function hdlr_tabClick() {
    var rqTblName = $(this).attr("data-table");
    var dirtyCells = $("tbody input[type=text][data-changed=true]").length;
    if ( rqTblName == _activeTab ) return false; /* nothing to do */
    _activeTab = rqTblName;
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
    switch ( _activeTab ) {
    case 'sundayParam':
        loadSundayDueForm();
        break;
    case 'dnldPrint':
		window.location.href = "dnldPrint.php";
        break
    case 'sundayDash':
        loadSundayDashboard();
        break;
    } // switch()
} // function tabClick()

$(document).ready(function() {
    pgMenu_rdy();
    $(".tabMenu th").on( 'click', hdlr_tabClick );
    $(".tabMenu th.future").unbind().on( 'click', futureAlert );
    $("table.tabMenu th:first-child").trigger( 'click' );
})