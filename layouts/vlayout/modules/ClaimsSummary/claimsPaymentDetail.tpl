{strip}
    <table name="paymentsTable" class="table table-bordered blockContainer showInlineTable" style="margin-top:1%;">
        <thead>
            <tr>
                <input type="hidden" name="numClaimPayments" value="{($CLAIM_TYPE_PAYMENT_DETAILS_LIST|@count)}"/>
                <th class="blockHeader" colspan="8">{vtranslate('LBL_PAYMENT_DETAILS', 'ClaimsSummary')}</th>
            </tr>
        </thead>
        <tbody>
        <tr class="fieldLabel">
            <td style="text-align:center;margin:auto;width:11%;">Claim Type</td>
            <td style="text-align:center;margin:auto;width:11%;">Cash</td>
            <td style="text-align:center;margin:auto;width:11%;">Repair</td>
            <td style="text-align:center;margin:auto;width:11%;">Inspection</td>
            <td style="text-align:center;margin:auto;width:11%;">Goodwill</td>
            <td style="text-align:center;margin:auto;width:17%;">Error/Ommissions</td>
            <td style="text-align:center;margin:auto;width:11%;">Total</td>
            <td style="text-align:center;margin:auto;width:17%;">Amount Denied</td>
        </tr> 
        {assign var="cash" value=0}
        {assign var="repair" value=0}
        {assign var="inspection" value=0}
        {assign var="goodwill" value=0}
        {assign var="erroromission" value=0}
        {assign var="total" value=0}
        {assign var="amount_denied" value=0}
        {foreach key=ROW_NUM item=CLAIM_TYPE from=$CLAIM_TYPE_PAYMENT_DETAILS_LIST}
            <tr style="text-align:center;margin:auto" class="claimPaymentRow{$ROW_NUM} claimPaymentRow">
                <td class="fieldValue typeCell" style="text-align:center;margin:auto"><a href="index.php?module=Claims&view=Detail&record={$CLAIM_TYPE['claim_id']}&mode=showDetailViewByMode&requestMode=full"> {$CLAIM_TYPE['claim_type']}</a></td>
                <td class="fieldValue" style="text-align:center;margin:auto;"> $ {$CLAIM_TYPE['cash']}</td>
                <td class="fieldValue" style="text-align:center;margin:auto"> $ {$CLAIM_TYPE['repair']}</td>
                <td class="fieldValue" style="text-align:center;margin:auto;"> $ {$CLAIM_TYPE['inspection']}</td>
                <td class="fieldValue" style="text-align:center;margin:auto"> $ {$CLAIM_TYPE['goodwill']}</td> 
                <td class="fieldValue" style="text-align:center;margin:auto"> $ {$CLAIM_TYPE['erroromission']}</td>
                <td class="fieldValue" style="text-align:center;margin:auto;"> $ {$CLAIM_TYPE['total']}</td>
                <td class="fieldValue" style="text-align:center;margin:auto;color:red;"> $ {$CLAIM_TYPE['amount_denied']}</td>  
                {assign var="cash" value=$cash+{$CLAIM_TYPE['cash']}}
                {assign var="repair" value=$repair+{$CLAIM_TYPE['repair']}}
                {assign var="inspection" value=$inspection+{$CLAIM_TYPE['inspection']}}
                {assign var="goodwill" value=$goodwill+{$CLAIM_TYPE['goodwill']}}
                {assign var="erroromission" value=$erroromission+{$CLAIM_TYPE['erroromission']}}
                {assign var="total" value=$total+{$CLAIM_TYPE['total']}}
                {assign var="amount_denied" value=$amount_denied+{$CLAIM_TYPE['amount_denied']}}
            </tr>
        {/foreach}
        <tr style="text-align:center;margin:auto" class="totalsRow">
                <td style="text-align:center;margin:auto"> </td>
                <td style="text-align:center;margin:auto">
                    <span class="value"> <b> $ {$cash}</b> </span>
                </td>
                <td style="text-align:center;margin:auto">
                    <span class="value"> <b> $ {$repair}</b> </span>
                </td>
                <td style="text-align:center;margin:auto">
                    <span class="value"> <b> $ {$inspection}</b> </span>
                </td>
                <td style="text-align:center;margin:auto">
                    <span class="value"> <b> $ {$goodwill}</b> </span>
                </td>
                <td style="text-align:center;margin:auto">
                    <span class="value"> <b> $ {$erroromission}</b> </span>
                </td>
                <td style="text-align:center;margin:auto">
                    <span class="value"> <b> $ {$total}</b> </span>
                </td>
                <td style="text-align:center;margin:auto">
                    <span class="value"> <b style="color:red;"> $ {$amount_denied}</b> </span>
                </td>
        </tr>
        </tbody>
    </table>
{/strip}