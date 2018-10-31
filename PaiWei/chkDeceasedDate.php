<?php
	/*
	 * Check if the Deceased Date of a peron is within 12 months of the Retreat Date
	 * The including script already had the retreat date information stored in the $_SESSION variables.
	 */
	function chkDate ( $dateString ) { // in YYYY-MM-DD
		global $_SESSION;
		$d = explode( "-", $dateString );
		if ( checkdate( $d[1], $d[2], $d[0] ) ) {
			$dateV = strtotime( $dateString );
			return( ( strtotime( $_SESSION[ 'pwPlqDate' ] ) <= $dateV ) &&
							( $dateV < strtotime( $_SESSION[ 'rtrtDate' ] ) ) );
		}
	}	// function chkDeceasedDate ()
?>