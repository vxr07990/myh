<table class="table table-bordered listTariffServices">
    <tr>
        <th class="listViewHeaders">Tariff Name</th>
    </tr>
    {foreach from = $TARIFF_SERVICES item = SERVICE key = INDEX}
        <tr class="listViewEntries" data-serviceid="{$SERVICE.tariffsid}"
            data-record-info="{Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($SERVICE))}">
            <td class="listViewEntryValue medium">{$SERVICE['tariff_name']}</td>
        </tr>
    {/foreach}
    {if empty($TARIFF_SERVICES)}
        <tr>
            <td>
                {vtranslate('LBL_NO')} {vtranslate('Tariffs', 'Tariffs')} {vtranslate('LBL_FOUND')}
            </td>
        </tr>
    {/if}
</table>
