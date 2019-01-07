	function admUser() { // adm User checkbox is clicked; this is a toggle function
		if ( ! $("#uNew").prop( "disabled" ) ) {
			// usr_New checkbox was enabled (default);
			// the admin_User checkbox is clicked, now to disable the new user checkbox
			$("#uNew").prop( "disabled", true ).css( 'opacity', 0.7 );
			$("#uNew").prop( "checked", false );
			$("#uEmail").prop( "disabled", true ).css( 'opacity', 0.7 );
			$("#usr2Help").prop( "disabled", false  ).css( 'opacity', 1.0 );
		} else { // was disabled; now enable
			$("#uNew").prop( "disabled", false ).css( 'opacity', 1.0 );
			$("#usr2Help").prop( "disabled", true  ).css( 'opacity', 0.7 );
		}
	} // admUser()
		
	function enableEmailEntry() { // new_User checkbox is clicked; this is a toggle function
		if ( ! $("#uEmail").prop( "disabled" ) ) { // was enabled; now to disable
			$("#uEmail").prop("disabled", true ).css( 'opacity', 0.6 );
		} else { // was disabled; now to enable
			$("#uEmail").prop("disabled", false ).css( 'opacity', 1.0 ); // enable email entry
		}
	}
	
	function onFocusHdlr() {
		var newV = $(this).val().trim().replace( /<br>$/gm, '');
		var pmptV = $(this).attr("data-pmptV").trim().replace( /<br>$/gm, '');
		if ( pmptV.length > 0 ) return; // was here before
		$(this).attr('data-pmptV', newV );
		$(this).val('');
		return;
	} // onFocusHdlr()

	function onBlurHdlr() {
		var currV = $(this).val().trim().replace( /<br>$/gm, '');
		if ( currV.length == 0 ) {
			$(this).val( $(this).attr("data-pmptV").trim() );
			$(this).attr( 'data-pmptV', '');
		}
	} // onBlurHdlr()

	$(document).ready(function() {
		/*
		 * Normal Login Scenario
		 */
		$("#uSubLogin").click(function() {
			uNamePrompt = $("#uName").attr("data-oldV"); uNameV = $("#uName").val();
			uEmailPrompt = $("#uEmail").attr("data-oldV"); uEmailV = $("#uEmail").val();
			uPassPrompt = $("#uPass").attr("data-oldV"); uPassV = $("#uPass").val();
			uNamePReg = new RegExp ( uNamePrompt ); uNameVReg = new RegExp( uNameV );
			uEmailPReg = new RegExp ( uEmailPrompt ); uEmailVReg = new RegExp( uEmailV );
			uPassPReg = new RegExp ( uPassPrompt ); uPassVReg = new RegExp( uPassV );

			if	( uNamePrompt.match ( uNameVReg ) || uNameV.match ( uNamePReg ) || uNameV.length == 0 )	{
				// CANNOT be partial to each other
				alert( uNamePrompt + "!");
				return false;
			}
			if ( uPassPrompt.match ( uPassVReg ) || uPassV.match ( uPassPReg ) || uPassV.length == 0 ) {
				// CANNOT be partial to each other
				alert( uPassPrompt + "!");
				return false;
			}
			if	( ( document.getElementById("uNew").checked == true ) &&
						( uEmailPrompt.match( uEmailVReg ) || uEmailV.match( uEmailPReg ) ) || uEmailV.length == 0 ) {
				alert( uEmailPrompt );
				return false;
			}
			return true;
		});
		
		/*
		 * Scenario: User enters Email address for Password Reset
		 */
		$("#uSubEmail").click(function() {
			uEmailPrompt = $("#uEmail").attr("data-oldV"); uEmailV = $("#uEmail").val();
			uEmailPReg = new RegExp ( uEmailPrompt ); uEmailVReg = new RegExp( uEmailV );
			if ( uEmailPrompt.match( uEmailVReg ) || uEmailV.match( uEmailPReg ) || uEmailV.length == 0 ) {
				alert( uEmailPrompt );
				return false;
			}
			return true;
		});
		
		/*
		 * Scenario: User submits new password to update
		 */
		$("#uSubUpd").click(function() {
			uPassPrompt = $("#uPass").attr("data-oldV"); uPassV = $("#uPass").val();
			uPassPReg = new RegExp ( uPassPrompt ); uPassVReg = new RegExp( uPassV );
			if ( uPassPrompt.match( uPassVReg ) || uPassV.match( uPassPReg ) || uPassV.length == 0 ) {
				alert( uPassPrompt );
				return false;
			}
			return true;
		});	
		
		$("input[type=text], input[type=password]").on( 'focus', onFocusHdlr );
		$("input[type=text], input[type=password]").on( 'blur', onBlurHdlr );
	});