function setUsrClass() {
	var thisRow = $(this).closest("tr");
	var dataCell = thisRow.find("td:first-child");
	var tblName = dataCell.attr("data-tblN");
	if ( tblName == 'inCareOf') {
		alert( '必須是自己註冊的蓮友，才可以分派不同的用戶類別！' );
		return false;
	}
	var uID = dataCell.attr("data-key");
	var uName = dataCell.text().trim();
	var _ajaxData = {}; var _dbInfo = {};
	_dbInfo[ 'ID' ] = uID;
	_dbInfo[ 'UsrName' ] = uName;
	_dbInfo[ 'uClass'] = thisRow.find("select option:selected").attr("data-fldV");
	_ajaxData[ 'dbReq' ] = 'dbSetUsrClass';
	_ajaxData[ 'dbInfo' ] = JSON.stringify ( _dbInfo );
	$.ajax({
		method: "POST",	url: "", data: _ajaxData,
		success: function ( rsp ) {
			rspX = JSON.parse( rsp );
			for (var X in rspX ) {
				if ( X == 'Err' ) {
					alert( rspX[ X ] ); return false;
				} else {
					alert( "用戶 [ " + uName + " ] 的用戶類別現在是 [ " + rspX[ X ] + " ]；謝謝！" );
					thisRow.find("td.uClass").empty().text( rspX[ 'uCLass' ] );
					thisRow.find("input[name=setUsrClass]").prop( 'disabled', true );
					return false;
				}
			}
		}, // End of Success Handler
		error: function (jqXHR, textStatus, errorThrown) {
			alert( "UpdUsrLvl()\tError Status:\t"+textStatus+"\t\tMessage:\t\t"+errorThrown+"\n" );
		} // End of ERROR Handler
	});
	return false;
} // setUsrClass()

function delUsrData() {
	if (! confirm("所有關於此用戶的資料都會被刪除，是無法恢復的。請確認！") ) {
		return false;
	}
	var thisRow = $(this).closest("tr");
	var dataCell = thisRow.find("td:first-child");
	var tblName = dataCell.attr("data-tblN");
	var uID = dataCell.attr("data-key");
	var uName = dataCell.text().trim();

	var _ajaxData = {}; var _dbInfo = {};
	_dbInfo[ 'tblName' ] = tblName;
	_dbInfo[ 'ID' ] = uID;
	_dbInfo[ 'UsrName' ] = uName;
	_ajaxData[ 'dbReq' ] = 'dbDelUsr';
	_ajaxData[ 'dbInfo' ] = JSON.stringify ( _dbInfo );
	$.ajax({
		method: "POST",	url: "", data: _ajaxData,
		success: function ( rsp ) {
			rspX = JSON.parse ( rsp );
			for ( var X in rspX ) {
				if ( X == 'Err' ) {
					alert( rspX[ X ] ); return false;
				} else {
					alert( rspX[ X ] );
				}
			}
			thisRow.remove();
			return false;
		}, // End of Success Handler 
		error: function (jqXHR, textStatus, errorThrown) {
			alert( "delUsrData()\tError Status:\t"+textStatus+"\t\tMessage:\t\t"+errorThrown+"\n" );
		} // End of ERROR Handler		
	}); // AJAX Call		
	return false;
} // delUsrData()
$(document).ready(function() {
	$(".future").on( 'click', futureAlert );
	$("input[name=setUsrClass]").on( 'click', setUsrClass );
	$("input[name=delUsr]").on( 'click', delUsrData );
});