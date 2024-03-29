function getFileName(dbTblName) {
	var useChn = _sessLang == SESS_LANG_CHN;

	switch (dbTblName) {
		case 'C001A':
			return  ( useChn ) ? "祈福消災牌位" : "Well Blessing";
		case 'D001A':
			return  ( useChn ) ? "地基主蓮位" : "Site Guardians";
		case 'L001A':
			return  ( useChn ) ? "歷代祖先蓮位" : "Ancestors";
		case 'Y001A':
			return  ( useChn ) ? "累劫冤親債主蓮位" : "Karmic Creditors";	
		case 'W001A_4':
			return  ( useChn ) ? "往生者蓮位" : "Deceased";
		case 'DaPaiWei':
			return  ( useChn ) ? "(一年內)往生者蓮位" : "Recently Deceased";
		case 'DaPaiWeiRed':
			return  ( useChn ) ? "紅色大牌位" : "RED DaPaiWei";
	}

}

/**********************************************************
 * Event Handler - When the Download CSV Button is clicked *
 * can ONLY select ONE user                                *
 **********************************************************/
function dnldCSVBtnHdlr() {
	var dnldUsrName = null;
	var dbTblName = null;
	var emptyUsrMsg = ( _sessLang == SESS_LANG_CHN ) ? '請選擇申請人！' : 'Please select requestors!';
	var moreUsrMsg = ( _sessLang == SESS_LANG_CHN ) ? '請只選擇一個申請人！' : 'Please selec ONLY ONE requestors!';
	var emptyPwMsg = ( _sessLang == SESS_LANG_CHN ) ? '請選擇牌位！' : 'Please select name plaque type!';

	dbTblName = $("select[name=dbTblName]").val();
	if(dbTblName == "") {
		alert(emptyPwMsg);
		return;
	}

	if(_sessType == SESS_TYP_USR) // user, get from session
		dnldUsrName = [ _sessUsr ]; // make an array to be consistent with multiple user selection
	else // admin user, get from user selection
		dnldUsrName = $("select[id=dnldUsrName]").val();	
	if(dnldUsrName.length == 0) {
		alert(emptyUsrMsg);
		return;
	}
	if(dnldUsrName.length > 1) {
		alert(moreUsrMsg);
		return;
	}	

	dnldUsrName = dnldUsrName[0]; // only one selected user
	_ajaxData = {};
	_ajaxData[ 'dbTblName' ] = dbTblName;
	_ajaxData[ 'dnldUsrName' ] = dnldUsrName;
	$.ajax({
		url: "./dnldPaiWei.php",
		method: 'POST',
		data: _ajaxData,
		xhrFields: {
            responseType: 'blob'
        },		
		success: function ( rsp ) {					
			var tmp = document.createElement('a');
            var url = window.URL.createObjectURL(rsp);
            tmp.href = url;
            tmp.download = getFileName(dbTblName) + ".csv";
            document.body.append(tmp);
            tmp.click();
            tmp.remove();
            window.URL.revokeObjectURL(url);			
    	}, // End of Success Handler 
		error: function (jqXHR, textStatus, errorThrown) {
			alert( "dnldCSVBtnHdlr()\tError Status:\t"+textStatus+"\t\tMessage:\t\t"+errorThrown+"\n" );
		} // End of ERROR Handler		
	}); // AJAX Call	
} // dnldCSVBtnHdlr()

/**********************************************************
 * Event Handler - When the Download PDF Button is clicked *
 **********************************************************/
// SUBMIT button, directly POST to server instead of AJAX
// ONLY for ADMIN users
 function dnldPDFBtnHdlr() {
	var emptyUsrMsg = ( _sessLang == SESS_LANG_CHN ) ? '請選擇申請人！' : 'Please select requestors!';
	var emptyPwMsg = ( _sessLang == SESS_LANG_CHN ) ? '請選擇牌位！' : 'Please select name plaque type!';

	if($("select[name=dbTblName]").val() == "") {
		alert(emptyPwMsg);
		return;
	}
	if($("select[id=dnldUsrName]").val().length == 0) {
		alert(emptyUsrMsg);
		return;
	}	
} // dnldPDFBtnHdlr()