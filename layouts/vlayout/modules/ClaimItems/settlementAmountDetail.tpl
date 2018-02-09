{strip}
    <table name="settlementAmountTable" class="table table-bordered blockContainer showInlineTable" style="margin-top:1%;">
        <thead>
            <tr>
                <input type="hidden" name="numSettlementAmount" value="{($SETTLEMENT_AMOUNT_LIST|@count)}"/>
                <th class="blockHeader" colspan="6">{vtranslate('LBL_SETTLEMENT_AMOUNT', 'ClaimItems')}</th>
            </tr>
        </thead>
        <tbody>
        <tr class="fieldLabel">
                <td style="text-align:center;margin:auto;width:25%;">Payment Type</td>
                <td style="text-align:center;margin:auto;width:25%;">Amount</td>
                <td style="text-align:center;margin:auto;width:25%;">Amount Denied</td>
                <td style="text-align:center;margin:auto;width:25%;">Item Ommited</td>
        </tr> 
        {assign var="tamount" value=0}
        {assign var="tamountd" value=0}
        {foreach key=ROW_NUM item=SETTLEMENT_AMOUNT from=$SETTLEMENT_AMOUNT_LIST}
            <tr style="text-align:center;margin:auto" class="settlementAmountRow{$ROW_NUM} settlementAmountRow">
                <td class="fieldValue typeCell" style="text-align:center;margin:auto">{$SETTLEMENT_AMOUNT['paymentType']}</td>
                <td class="fieldValue" style="text-align:center;margin:auto">{$SETTLEMENT_AMOUNT['amount']}</td>
                <td class="fieldValue" style="text-align:center;margin:auto">{$SETTLEMENT_AMOUNT['amountDenied']}</td>
                <td class="fieldValue" style="text-align:center;margin:auto">{$SETTLEMENT_AMOUNT['itemOmitted']}</td>
            </tr>
            {assign var="tamount" value=$tamount+$SETTLEMENT_AMOUNT['amount']}
            {assign var="tamountd" value=$tamountd+$SETTLEMENT_AMOUNT['amountDenied']}
        {/foreach}
        <tr style="text-align:center;margin:auto" class="totalsRow">
            <td style="text-align:center;margin:auto"></td>
            <td style="text-align:center;margin:auto">
                <span class="value"> <b> Total Amount: {$tamount}</b> </span>
            </td>
            <td style="text-align:center;margin:auto">
                <span class="value"> <b> Total Amount Denied: {$tamountd}</b> </span>
            </td>
            <td style="text-align:center;margin:auto"></td>
        </tr>
        </tbody>
    </table>
{/strip}