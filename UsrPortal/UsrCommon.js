var _url2Go = {
    'urlAdmHome' : "../AdmPortal/index.php",
    'urlUsrHome' : "../UsrPortal/index.php",
    'urlPaiWei' : "../PaiWei/index.php",
    'urlSunday' : "../Sunday/index.php",
    'usrLogout' : "../Login/Logout.php"
} // anchors where each pgMenu TH points to

function pgMenu_rdy() {
    $(".future").on( 'click', futureAlert );
    $(".soon").on( 'click', soonAlert );
    $("table.pgMenu th:not(.future)").on('click', function() {
        location.replace(  _url2Go [ $(this).attr("data-urlIdx") ] );
    });
} /* pgMenu ready */