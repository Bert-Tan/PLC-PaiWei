function leapYear( yr ) {
	return( ( yr % 100 === 0 ) ? ( yr % 400 === 0 ) : ( yr % 4 === 0 ) );
} // function leapYear()

function chkDate ( dateString, formatOnly ) { // in YYYY-MM-DD format
	var D = new Date(); // current
	var YYYY = D.getFullYear();
	var patString = "^(" + YYYY + "|" + (YYYY+1) + ")-(0?[1-9]|1[0-2])-(0?[1-9]|[12]\\d|3[01])$";
	var pattern = new RegExp( patString );

	if ( !dateString.match( pattern ) ) return false;
	if ( formatOnly ) return true;

	var d = dateString.split( '-' ); // d[0] => yyyy, d[1] => mm, d[2] = dd
	var dd = 0;

	switch ( Number( d[1] ) ) {
		case 2:
			var dd = leapYear( d[0] ) ? 29 : 28;
			break;
		case 1:
		case 3:
		case 5:
		case 7:
		case 8:
		case 10:
		case 12:
			dd = 31;
			break;
		case 4:
		case 6:
		case 9:
		case 11:
			dd = 30;
			break;
		default:
			return false;
	} // switch on mm

	var nxtD = new Date( YYYY+1, D.getMonth(), D.getDate(), D.getHours(), D.getMinutes(), D.getSeconds() ); // a year from now
	var rtD = new Date( dateString );
	return ( ( ( 1 <= Number(d[2]) ) && ( Number(d[2]) <= dd ) ) && ( ( D <= rtD ) && ( rtD < nxtD ) ) );
} // function chkDate()
