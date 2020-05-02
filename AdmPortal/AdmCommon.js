var _url2Go = {
    'urlWebsiteHome' : "https://www.amitabhalibrary.org/",
    'urlAdmHome' : "../AdmPortal/index.php",
    'urlUmgr' : "../AdmPortal/AdmUMgr.php", /* test only; real path: "../AdmPortal/AdmUMgr.php", */
    'urlPaiWeiMgr' : "../PaiWei/paiweiMgr.php",
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