{strip}
    <table name="paymentsTable" class="table table-bordered blockContainer showInlineTable" style="margin-top:1%;">
        <thead>
            <tr>
                <input type="hidden" name="numPayments" value="{($PAYMENT_LIST|@count)}"/>
                <th class="blockHeader" colspan="6">{vtranslate('LBL_PAYMENTS', 'Claims')}</th>
            </tr>
        </thead>
        <tbody>
        <tr class="fieldLabel">
            <td style="text-align:center;margin:auto;width:30%;">Fees</td>
            <td style="text-align:center;margin:auto;width:30%;">Date Requested</td>
            <td style="text-align:center;margin:auto;width:30%;">Amount</td>
        </tr> 
        {assign var="tamount" value=0}
        {foreach key=ROW_NUM item=PAYMENT from=$PAYMENT_LIST}
            <tr style="text-align:center;margin:auto" class="paymentRow{$ROW_NUM} paymentRow">
                <td class="fieldValue typeCell" style="text-align:center;margin:auto">{$PAYMENT['fees']}</td>
                <td class="fieldValue" style="text-align:center;margin:auto;padding-left:1%">{if $PAYMENT['date']}{$PAYMENT['date']}{else}{$DATE}{/if}</td>
                <td class="fieldValue" style="text-align:center;margin:auto">{$PAYMENT['amount']}</td>
            </tr>
            {assign var="tamount" value=$tamount+$PAYMENT['amount']}
        {/foreach}
        <tr style="text-align:center;margin:auto" class="totalsRow">
            <td colspan="2" style="text-align:center;margin:auto"></td>
            <td style="text-align:center;margin:auto">
                <span class="value"> <b> Total Payments: {$tamount}</b> </span>
            </td>
        </tr>
        </tbody>
    </table>
{/strip}