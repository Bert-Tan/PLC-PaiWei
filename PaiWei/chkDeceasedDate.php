<?php
	/*
	 * Check if the Deceased Date of a peron is within 12 months of the Retreat Date
	 * The including script already had the retreat date information stored in the $_SESSION variables.
	 */
	function chkDate ( $dateString ) { // in YYYY-MM-DD or MM/DD/YYYY
		global $_SESSION;

		// YYYY-MM-DD
		$d = explode( "-", $dateString );
		if ( count( $d ) == 3 && checkdate( $d[1], $d[2], $d[0] ) ) {
			$dateV = strtotime( $dateString );
			return( ( strtotime( $_SESSION[ 'pwPlqDate' ] ) <= $dateV ) &&
							( $dateV < strtotime( $_SESSION[ 'rtrtDate' ] ) ) );
		}

		// MM/DD/YYYY
		$d = explode( "/", $dateString );
		if ( count( $d ) == 3 && checkdate( $d[0], $d[1], $d[2] ) ) {
			$dateV = strtotime( $dateString );
			return( ( strtotime( $_SESSION[ 'pwPlqDate' ] ) <= $dateV ) &&
							( $dateV < strtotime( $_SESSION[ 'rtrtDate' ] ) ) );
		}

		return false;
	}	// function chkDate ()
?>