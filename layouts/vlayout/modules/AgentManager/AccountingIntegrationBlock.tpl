{strip}
<table class="table table-bordered blockContainer showInlineTable" id="accountingIntegrationTable">
    <thead>
            <tr>
                <th class="blockHeader" colspan="5">
                    <img class="cursorPointer alignMiddle blockToggle {if !($IS_HIDDEN)} hide {/if} "  src="{vimage_path('arrowRight.png')}" data-mode="hide" data-id="accounting_integration">
                    <img class="cursorPointer alignMiddle blockToggle {if ($IS_HIDDEN)} hide {/if}"  src="{vimage_path('arrowDown.png')}" data-mode="show" data-id="accounting_integration">
                    {vtranslate('LBL_ACCOUNTING_INTEGRATION', $MODULE)}
                </th>
            </tr>
        </thead>
    <tbody>
        <tr>
            <td id="qboConnectTD">
                {if $CONNECTED_TO_QBO}
                    {vtranslate('LBL_ACCOUNTING_INTEGRATION_CONNECTED_TO_QBO', $MODULE)}
                {else}
                    <ipp:connectToIntuit></ipp:connectToIntuit>
                {/if}
            </td>
        </tr>
    </tbody>
</table>
    <br/>

<script src="https://js.appcenter.intuit.com/Content/IA/intuit.ipp.anywhere-1.3.3.js" type="text/javascript"></script>
{/strip}