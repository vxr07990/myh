{strip}
    <div class="contents-bottomscroll">
        <table name="summaryTable" class="table table-bordered blockContainer showInlineTable" style="margin-top:1%;">
            <thead>
                <tr>
                    <th class="blockHeader" colspan="11">{vtranslate('Summary', 'Claims')}</th>
                </tr>
            </thead>
            <tbody>
                <tr class="fieldLabel">
                    <td style="text-align:center;margin:auto;">Agent Type</td>
                    <td style="text-align:center;margin:auto;">Participant Agent (Agent #)</td>
                    <td style="text-align:center;margin:auto;">Service Provider</td>
                    <td style="text-align:center;margin:auto; width: 80px;">Claim Class</td>
                    <td style="text-align:center;margin:auto; width: 60px;">No of Items</td>
                    <td style="text-align:center;margin:auto;">Resp Amount</td>
                    <td style="text-align:center;margin:auto;">Agent Chrgbk</td>
                    <td style="text-align:center;margin:auto;">Svc Provider Chrgbk</td>
                    <td style="text-align:center;margin:auto;">Eff Date</td>
                    <td style="text-align:center;margin:auto;">Distribution</td>
                    <td style="text-align:center;margin:auto;">Distribution Date</td>
                </tr> 
                {assign var="rows" value=1}
                {foreach key=ROW_NUM1 item=SITEM from=$CLAIM_TYPE_SUMMARY_GRIDS}
                    <tr style="text-align:center;margin:auto" class="summaryRow{$rows}">
                <input type="hidden" name="summary-dbid-{$rows}" value="{$SITEM['dbId']}">
                <input type="hidden" name="summary-claimtypeid-{$rows}" value="{$SITEM['claimTypeID']}">
                <input type="hidden" name="summary-claim-class-{$rows}" value="{$SITEM['claimClass']}">
                <input type="hidden" name="summary-agent-type-{$rows}" value="{$SITEM['AgentType']}">
                <input type="hidden" name="summary-agent-id-{$rows}" value={$SITEM['AgentID']}>
                <input type="hidden" name="summary-serviceprovider-id-{$rows}" value={$SITEM['ServiceProviderID']}>
                


                <td class="fieldValue" style="text-align:center;margin:auto">{if $SITEM['AgentType'] eq ""}-{else}{$SITEM['AgentType']}{/if}</td>
                <td class="fieldValue" style="text-align:center;margin:auto">{if $SITEM['Agent'] eq ""}-{else}{$SITEM['Agent']}{/if}</td>
                <td class="fieldValue" style="text-align:center;margin:auto">{if $SITEM['ServiceProvider'] eq ""}-{else}{$SITEM['ServiceProvider']}{/if}</td>
                <td class="fieldValue" style="text-align:center;margin:auto">{$SITEM['claimClass']}</td>
                <td class="fieldValue" style="text-align:center;margin:auto">{$SITEM['Qty']}</td>
                <td class="fieldValue" style="text-align:center;margin:auto">{$SITEM['Amount']}</td>
                <td class="fieldValue" style="text-align:center;margin:auto">
                    {if $FLAG eq "Edit"}
                        <input type="text" value="{$SITEM['agentChargeBack']}" name="agentChargeBack{$rows}" class="span2 agentChargeBack">
                    {else}
                        {$SITEM['agentChargeBack']}
                    {/if}
                </td>
                <td class="fieldValue" style="text-align:center;margin:auto">
                    {if $FLAG eq "Edit"}
                        <input type="text"  value="{$SITEM['serviceProviderChargeBack']}" name="serviceProviderChargeBack{$rows}" class="span2 serviceProviderChargeBack">
                    {else}
                        {$SITEM['serviceProviderChargeBack']}
                    {/if}
                </td>
                <td class="fieldValue" style="text-align:center;margin:auto; min-width: 130px;">
                    {if $FLAG eq "Edit"}
                        <div class="input-append row-fluid">
                            <div class="row-fluid date">
                                <input type="text" class="dateField" name="effectiveDate{$rows}" data-date-format="{$CURRENT_USER_MODEL->get('date_format')}" value="{$SITEM['effectiveDate']}">
                                <span class="add-on"><i class="icon-calendar"></i></span>
                            </div>
                        </div>
                    {else}
                        {$SITEM['effectiveDate']}
                    {/if}          
                </td>
                <td class="fieldValue" style="text-align:center;margin:auto">
                    {if $FLAG eq "Edit"}
                        <input type="checkbox" class="distribution" value="{$SITEM['distribution']}" name="distribution{$rows}" {if $SITEM['distribution'] eq "yes"}checked{/if}>
                    {else}
                        {$SITEM['distribution']}
                    {/if}
                </td>
                <td class="fieldValue" style="text-align:center;margin:auto; min-width: 130px;">
                    {if $FLAG eq "Edit"}
                        <div class="input-append row-fluid">
                            <div class="row-fluid date">
                                <input type="text" class="dateField" name="distributionDate{$rows}" data-date-format="{$CURRENT_USER_MODEL->get('date_format')}" value="{$SITEM['distributionDate']}">
                                <span class="add-on"><i class="icon-calendar"></i></span>
                            </div>
                        </div>
                    {else}
                        {$SITEM['distributionDate']}
                    {/if}
                </td>
                </tr>
                {assign var="rows" value=$rows+1}
            {/foreach}
            <input type="hidden" value="{$rows}" name="summaryTableRows">
            </tbody>
        </table>
    </div>
{/strip}