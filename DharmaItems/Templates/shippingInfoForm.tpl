<!-- BEGIN shippingInfoForm -->
<form id="shippingInfo" action="./ajax-DI_shippAddrDB.php">
    <table class="dataRows">
        <thead>
            <tr>
                <th colspan="4">
                    {shippingFormName}
                    <input type="hidden" id="AddrID" name="AddrID" value="{AddrIDV}">
                    <input type="hidden" id="dbTblName" name="tblName" value="UsrAddr">
                </th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td colspan="2">{orgNameLbl}<br/>
                    <input type="text" id="Addressee" name="Addressee" value="{AddresseeV}"></td>
                <td width="20%">{telNoLbl}<br/>
                    <input type="text" id="TelNo" name="TelNo" value="{TelNoV}"></td>
                <td>{emailLbl}<br/>
                    <input type="text" id="Email" name="Email" value="{EmailV}"></td>
            </tr>
            <tr>
                <td colspan="3">{streetNumLbl}<br/>
                    <input type="text" id="StNum" name="StNum" value="{StNumV}"></td>
                <td>{unitNumLbl}<br/>
                    <input type="text" id="Unit" name="Unit" value="{UnitV}"></td>
            </tr>
            <tr>
                <td colspan="2">{cityNameLbl}<br/>
                    <input type="text" id="City" name="City" value="{CityV}"></td>
                <td>{stateNameLbl}<br/>
                    <input type="text" id="US_State" name="US_State" value="{US_StateV}"></td>
                <td>{zipCodeLbl}<br/>
                    <input type="text" id="ZipCode" name="ZipCode" value="{ZipCodeV}"></td>
            </tr>
            <tr>
                <td>
                    <input type="checkbox" id="Prim" name="Prim" value="tbd" {PrimV}>&nbsp;&nbsp;&nbsp;{primLblV}
                </td>
                <td style="text-align: center;"><input type="button" id="ldAltShippingInfo" value="{altInfoV}"></td>
                <td style="text-align: center;"><input type="submit" id="updSaveInfo" value="{updSaveBtnV}"></td>
                <td style="text-align: center;"><input type="button" id="delShippingInfo" value="{delBtnV}"></td>
            </tr>
            <tr>
                <th colspan="4" style="font-size: 1.1em;">
                    <input type="button" id="ldDharmaAppForm" data-table="dharmaItemsReqForm" value="{ldAppFormV}">
                </th>
            </tr>
        </tbody>
    </table>
</form>
<!-- END shippingInfoForm -->
