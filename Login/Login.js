var _usrPlace = {
    'usrHome' : "../index.php",
    'usrLogout' : "../Login/Logout.php"
} // anchors where each pgMenu TH points to

function enableEmailEntry() { // new_User checkbox is clicked; this is a toggle function
	if ( ! $("#uEmail").prop( "disabled" ) ) { // was enabled; now to disable
		var pmptV = $("#uEmail").attr("data-oldV");
		$("#uEmail").val( pmptV ); // retore the prompt value
		$("#uEmail").prop("disabled", true ).css( 'opacity', 0.6 );
	} else { // was disabled; now to enable
		$("#uEmail").prop("disabled", false ).css( 'opacity', 1.0 ); // enable email entry
	}
} // functionn enableEmailEntry()

function onFocusHdlr() {
	var newV = $(this).val().trim().replace( /<br>$/gm, '');
	var pmptV = $(this).attr("data-pmptV").trim().replace( /<br>$/gm, '');
	if ( pmptV.length > 0 ) return; // was here before
	$(this).attr('data-pmptV', newV );
	$(this).val('');
	return;
} // function onFocusHdlr()

function onBlurHdlr() {
	var cV = $(this).val().trim().replace( /<br>$/gm, ''); // current input value
	if ( cV.length == 0 ) { // did not input any; restore the prompt value
		$(this).val( $(this).attr("data-pmptV").trim() );
		$(this).attr( 'data-pmptV', '');
		return false;
	}
	// input value and the prompt string cannot be substring to each other
	var cvReg = new RegExp( cV, "gu" );
	var pV = $(this).attr( 'data-oldV' );
	var pvReg = new RegExp( pV, "gu" );
	if ( pV.match( cvReg ) || cV.match( pvReg ) ) {
		alert( pV );
		$(this).val( pV );
		$(this).attr( 'data-pmptV', '');
	}
	return false;
} // function onBlurHdlr()

function getRstLink() {
	$.ajax({
		method: "post",
		url: "",
		data: { 'dbReq' : "uResetLink" },
		success: function ( rsp ) {
			rspV = JSON.parse( rsp );
			_usrPlace [ 'uResetLink' ] = rspV[ 'uResetLink' ];
			$("table.dialog td[data-urlIdx=uResetLink]").on( 'click', function() {
				location.replace(  _usrPlace [ $(this).attr("data-urlIdx") ] );
			});
			return false;
		}, // success scenario
		error: function (jqXHR, textStatus, errorThrown) {
			alert( "getRstLink()\tError Status:\t"+textStatus+"\t\tMessage:\t\t"+errorThrown+"\n" );
		} // End of ERROR Handler
	})
} // function getRstLink()

$(document).ready(function() {
	/*
	 * Normal Login Scenario
	 */
	$("#uLogin").click(function() {
		uNameP = $("#uName").attr("data-oldV"); uNameV = $("#uName").val();
		uPassP = $("#uPass").attr("data-oldV"); uPassV = $("#uPass").val();

		if	( uNameP == uNameV ) {
			alert( uNameP + "!"); return false;
		}
		if ( uPassP == uPassV ) {
			alert( uPassP + "!"); return false;
		}
		/* No need to sanity check the email input because the Browser does */
		return true;
	});

	/*
	 * Scenario: User submits new password to update
	 */
	$("#uUpd").click(function() {
		uPassP = $("#uPass").attr("data-oldV"); uPassV = $("#uPass").val();
		if ( uPassP == uPassV ) {
			alert( uPassP + "!"); return false;
		}
		return true;
	});	
	
	$("input[type=text], input[type=password], input[type=email]").on( 'focus', onFocusHdlr );
	$("input[type=text], input[type=password], input[type=email]").on( 'blur', onBlurHdlr );
	$(".future").on( 'click', futureAlert );
	$("table.pgMenu th:not(.future)").on('click', function() {
		urlIdx = $(this).attr("data-urlIdx"); ugL = $(this).attr("data-ugL");
		if ( urlIdx == 'rUG' ) {
			url = ( ugL == "c" ) ? "../UsrPortal/UG.php" : "../UsrPortal/eUG.php";
		} else {
			url = _usrPlace[ urlIdx ];
		}
//		alert ( "Line 102: " + urlIdx + "; Lang= " + ugL + ";\nurl= " + url ); return;
        location.replace(  url );
	});
	var _resetPresent = $("table.dialog td[data-urlIdx=uResetLink]");
	if ( _resetPresent.length > 0 ) {
		getRstLink();
	}
});