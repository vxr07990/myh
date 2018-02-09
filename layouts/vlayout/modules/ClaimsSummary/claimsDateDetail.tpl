{strip}
    <table name="paymentsTable" class="table table-bordered blockContainer showInlineTable" style="margin-top:1%;">
        <thead>
            <tr>
                <input type="hidden" name="numClaimTypes" value="{($CLAIM_TYPE_LIST|@count)}"/>
                <th class="blockHeader" colspan="6">{vtranslate('LBL_DATE_DETAILS', 'ClaimsSummary')}</th>
            </tr>
        </thead>
        <tbody>
        <tr class="fieldLabel">
            <td style="text-align:center;margin:auto;width:20%;">Claim Type</td>
            <td style="text-align:center;margin:auto;width:20%;">Received Date</td>
            <td style="text-align:center;margin:auto;width:20%;">Closed Date</td>
            <td style="text-align:center;margin:auto;width:20%;">Calendar Days to Settle</td>
            <td style="text-align:center;margin:auto;width:20%;">Business Days to Settle</td>
        </tr> 
        {foreach key=ROW_NUM item=CLAIM_TYPE from=$CLAIM_TYPE_LIST}
            <tr style="text-align:center;margin:auto" class="claimTypeRow{$ROW_NUM} claimTypeRow">
                <td class="fieldValue typeCell" style="text-align:center;margin:auto"><a href="index.php?module=Claims&view=Detail&record={$CLAIM_TYPE['claim_id']}&mode=showDetailViewByMode&requestMode=full"> {$CLAIM_TYPE['claim_type']}</a></td>
                <td class="fieldValue" style="text-align:center;margin:auto;">{$CLAIM_TYPE['received_date']}</td>
                <td class="fieldValue" style="text-align:center;margin:auto">{$CLAIM_TYPE['closed_date']}</td>
                <td class="fieldValue" style="text-align:center;margin:auto;">{$CLAIM_TYPE['calendar_days']}</td>
                <td class="fieldValue" style="text-align:center;margin:auto">{$CLAIM_TYPE['business_days']}</td>  
            </tr>
        {/foreach}
        </tbody>
    </table>
{/strip}