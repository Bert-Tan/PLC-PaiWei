<!-- BEGIN dashboardBody -->
<tbody>
    <!-- BEGIN dashboardRow -->
    <tr>
        <td data-icoName="{icoName}">{icoName}</td>
        <!-- BEGIN dashboardCell -->
        <td data-tblN="{tblName}" pw-ct="{pwCt}" pw-valid-ct="{pwValidCt}" pw-invalid-ct="{pwInvalidCt}">{pwCt}</td>
        <!-- END dashboardCell -->
        <!-- BEGIN icoSum -->
        <td class="icoTotal" pw-ct="{pwCt}" pw-valid-ct="{pwValidCt}" pw-invalid-ct="{pwInvalidCt}">{pwCt}</td>
        <!-- END icoSum -->
        <!-- BEGIN notifyBtn -->
        <td><input class="notifyBtn" type="button" value="告知" {notifyDisableStr}></td>
        <!-- END notifyBtn -->
    </tr>
    <!-- END dashboardRow -->
    <!-- BEGIN sumRow -->
    <tr>
        <td>總&nbsp;&nbsp;&nbsp;&nbsp;計【張 數】</td>
        <!-- BEGIN sumCell -->
        <td pw-ct="{pwCt}" pw-valid-ct="{pwValidCt}" pw-invalid-ct="{pwInvalidCt}" pw-sht="{pwSht}" pw-valid-sht="{pwValidSht}" pw-invalid-sht="{pwInvalidSht}">{pwCt} 【{pwSht}】</td>
        <!-- END sumCell -->
        <!-- BEGIN grandSumCell -->
        <td pw-ct="{pwCt}" pw-valid-ct="{pwValidCt}" pw-invalid-ct="{pwInvalidCt}" pw-sht="{pwSht}" pw-valid-sht="{pwValidSht}" pw-invalid-sht="{pwInvalidSht}">{pwCt} 【{pwSht}】</td> 
        <!-- END grandSumCell -->
        <!-- BEGIN notifyAllBtn -->
        <td><input id="notifyAllBtn" type="button" value="全部告知" {notifyDisableStr}></td>
        <!-- END notifyAllBtn -->
    </tr>
    <!-- END sumRow -->
</tbody>
<!-- END dashboardBody -->