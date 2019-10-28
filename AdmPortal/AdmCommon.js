var _url2Go = {
    'urlAdmHome' : "../AdmPortal/index.php",
    'urlUmgr' : "../AdmPortal/AdmUMgr.php", /* test only; real path: "../AdmPortal/AdmUMgr.php", */
    'urlRtData' : "../PaiWei/rtMgr.php", /* test only; real path: "../PaiWei/rtMgr.php", */
    'urlDnldJiWen' : "../PaiWei/dnldJiWenForm.php",
    'urlDnld' : "../PaiWei/dnldPaiWeiForm.php",
    'url4Others' : "../PaiWei/Dashboard.php",
    'urlSundayMgr' : "../Sunday/sundayMgr.php",
    'usrLogout' : "../Login/Logout.php"
} // anchors where each pgMenu TH points to

function pgMenu_rdy() {
    $(".future").on( 'click', futureAlert );
    $(".soon").on( 'click', soonAlert );
    $("table.pgMenu th:not(.future)").on('click', function() {
        location.replace(  _url2Go [ $(this).attr("data-urlIdx") ] );
    });
} /* pgMenu ready */