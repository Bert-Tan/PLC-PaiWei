<!-- User Page Common Header to be included by User Applications:
    UsrPortal/index.php, . . . .
-->    <div class="hdrRibbon">
        <img src="https://www.amitabhalibrary.org/pic/PLC_logo_TR.png" class="centerMeV" alt="">
        <div id="pgTitle" class="centerMeV">
            <span>淨土念佛堂一般用戶主頁</span><br/>
            <span class="engClass">Pure Land Center User Portal</span>
        </div>
        <table class="pgMenu centerMeV">	
            <thead>
                <tr>
                    <th data-urlIdx="urlWebsiteHome"><?php echo xLate( 'WebsiteHome' ); ?></th>
<?php
    if ( $sessType != SESS_TYP_USR ) {
?>		
                    <th data-urlIdx="urlAdmHome">回到<br/>管理主頁</th>
<?php
    } else {
?>
                    <th data-urlIdx="urlUsrHome"><?php echo xLate( 'UsrHome' ); ?></th>
<?php
    }
?>                    
                    <th data-urlIdx="urlPaiWei"><?php echo xLate( 'featPW' ); ?></th>
                    <th data-urlIdx="urlDharmaItems"><?php echo xLate( 'featDharmaItems' ); ?></th>
                    <th data-urlIdx="urlSunday"><?php echo xLate( 'featSun' ); ?></th>
                    <th class="future"><?php echo xLate( 'featFuture' ); ?></th>
                    <th data-urlIdx="usrLogout"><?php echo xLate( 'logOut' ); ?></div>
                </tr>
            </thead>
        </table>		
    </div>
