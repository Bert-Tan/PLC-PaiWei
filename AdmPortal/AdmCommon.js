var _admPagePlace = {
    '用戶管理' : "../AdmPortal/AdmUMgr.php", /* test only; real path: "../AdmPortal/AdmUMgr.php", */
    '更新法會資料' : "../PaiWei/rtMgr.php", /* test only; real path: "../PaiWei/rtMgr.php", */
    '為蓮友處理法會牌位' : "../PaiWei/Dashboard.php", /* test only; real path: "../PaiWei/Dashboard.php", */
    '用戶撤出' : "../Login/Logout.php"
} // anchors where each pgMenu TH points to

function pgMenu_rdy() {
    $(".future").on( 'click', futureAlert );
    $(".soon").on( 'click', soonAlert );
    $("table.pgMenu th:not(.future)").on('click', function() {
        location.replace(  _admPagePlace [ $(this).text() ] );
    });
} /* pgMenu ready */