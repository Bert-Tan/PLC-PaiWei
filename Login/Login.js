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
	
	$(document).ready(function() {
		/*
		 * Normal Login Scenario
		 */
		$("#uSubLogin").click(function() {
			uNamePrompt = $("#uName").attr("data-oldV"); uNameV = $("#uName").val();
			uEmailPrompt = $("#uEmail").attr("data-oldV"); uEmailV = $("#uEmail").val();
			uPassPrompt = $("#uPass").attr("data-oldV"); uPassV = $("#uPass").val();

			if	( uNamePrompt.includes ( uNameV ) || uNameV.includes ( uNamePrompt ) || uNameV.length == 0 )	{
				// CANNOT be partial to each other
				alert( uNamePrompt + "!");
				return false;
			}
			if ( uPassPrompt.includes ( uPassV ) || uPassV.includes ( uPassPrompt ) || uPassV.length == 0 ) {
				// CANNOT be partial to each other
				alert( uPassPrompt + "!");
				return false;
			}
			if	( ( document.getElementById("uNew").checked == true ) &&
						( uEmailPrompt.includes( uEmailV ) || uEmailV.includes( uEmailPrompt ) ) || uEmailV.length == 0 ) {
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
			if ( uEmailPrompt.includes( uEmailV ) || uEmailV.includes( uEmailPrompt ) || uEmailV.length == 0 ) {
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
			if ( uPassPrompt.includes( uPassV ) || uPassV.includes( uPassPrompt ) || uPassV.length == 0 ) {
				alert( uPassPrompt );
				return false;
			}
			return true;
		});		 
	});