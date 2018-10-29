<?php
	/*
	 * Check if the Deceased Date of a peron is within 12 months of the Retreat Date
	 */

	function chkDeceasedDate ( $retreatDate, $deceasedDate ) {
		/*
		 * Both parameters are in YYYY-MM-DD format;
		 * For DaPaiWei, the $deceasedDate must be within 12 months
		 */
		if ( strtotime( $deceasedDate . " +1 year" ) >= strtotime( $retreatDate ) ) {
			return true;
		}
		return false;
	}	// function chkDeceasedDate ()
?>