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
                <td colspan="6">
                    <button type="button" class="addSettlementAmount">+</button>
                    <button type="button" class="addSettlementAmount" style="clear:right;float:right">+</button>
                </td>
            </tr>
            <tr class="fieldLabel">
                <td style="text-align:center;margin:auto;width:11%;"> </td>
                <td style="text-align:center;margin:auto;width:26%;">Payment Type</td>
                <td style="text-align:center;margin:auto;width:26%;">Amount</td>
                <td style="text-align:center;margin:auto;width:26%;">Amount Denied</td>
                <td style="text-align:center;margin:auto;width:11%;">Item Ommited</td>
            </tr>
            <tr style="text-align:center;margin:auto"class="defaultSettlementAmount settlementAmountRow hide">
                <td class="fieldValue" style="text-align:center;margin:auto">
                    <i title="Delete" class="icon-trash removeSettlementAmount"></i>
                    <input type="hidden" class="default" name="settlementAmountId" value="none" />
                </td>
                <td class="fieldValue typeCell" style="text-align:center;margin:auto">
                    <select style="text-align:left" class="select default validate" name="paymentType">
                        <option value="-">{vtranslate('LBL_SELECT_OPTION','Vtiger')}</option>
                        <option value="Cash">Cash</option>
                        <option value="Repair">Repair</option>
                        <option value="Inspection">Inspection</option>
                        <option value="Goodwill">Goodwill</option>
                        <option value="Error/Omission">Error/Omission</option>
                    </select>
                </td>
                <td class="fieldValue" style="text-align:center;margin:auto">
                    <input type="text" value="" class="sAmount default" name="amount">
                </td>
                <td class="fieldValue" style="text-align:center;margin:auto">
                    <input type="text" value="" class="sAmountD default" name="amountDenied">
                </td>
                <td class="fieldValue" style="text-align:center;margin:auto">
                    <input type="checkbox" value="" name="itemOmitted" class="default">
                </td>
            </tr>
        {assign var="tamount" value=0}
        {assign var="tamountd" value=0}
        {foreach key=ROW_NUM item=SETTLEMENT_AMOUNT from=$SETTLEMENT_AMOUNT_LIST}
            <tr style="text-align:center;margin:auto" class="settlementAmountRow{$ROW_NUM+1} settlementAmountRow">
                <td class="fieldValue" style="text-align:center;margin:auto">
                    <i title="Delete" class="icon-trash removeSettlementAmount"></i>
                    <input type="hidden" name="settlementAmountId_{$ROW_NUM+1}" value="{$SETTLEMENT_AMOUNT['settlementAmountId']}" />
                </td>
                <td class="fieldValue typeCell" style="text-align:center;margin:auto">
                    <select class="chzn-select" name="paymentType{$ROW_NUM+1}" data-selected-value="{$SETTLEMENT_AMOUNT['paymentType']}">
                        <option value="-">{vtranslate('LBL_SELECT_OPTION','Vtiger')}</option>
                        <option value="Cash">Cash</option>
                        <option value="Repair">Repair</option>
                        <option value="Inspection">Inspection</option>
                        <option value="Goodwill">Goodwill</option>
                        <option value="Error/Omission">Error/Omission</option>
                    </select>
                </td>
                <td class="fieldValue" style="text-align:center;margin:auto">
                    <input type="text" value="{$SETTLEMENT_AMOUNT['amount']}" name="amount{$ROW_NUM+1}" class="sAmount">
                </td>
                <td class="fieldValue" style="text-align:center;margin:auto">
                    <input type="text" value="{$SETTLEMENT_AMOUNT['amountDenied']}" name="amountDenied{$ROW_NUM+1}" class="sAmountD">
                </td>
                <td class="fieldValue" style="text-align:center;margin:auto">
                    <input type="checkbox" value="{$SETTLEMENT_AMOUNT['itemOmitted']}" name="itemOmitted{$ROW_NUM+1}" {if $SETTLEMENT_AMOUNT['itemOmitted'] eq "yes"}checked{/if}>
                </td>
            </tr>
            {assign var="tamount" value=$tamount+$SETTLEMENT_AMOUNT['amount']}
            {assign var="tamountd" value=$tamountd+$SETTLEMENT_AMOUNT['amountDenied']}
        {/foreach}
        <tr style="text-align:center;margin:auto" class="totalsRow">
            <td colspan="2" style="text-align:center;margin:auto"></td>
            <td style="text-align:center;margin:auto">
                <input type="hidden" value="{$tamount}" id="tamount">
                <span class="value tamount"> <b> Total Amount: {$tamount}</b> </span>
            </td>
            <td style="text-align:center;margin:auto">
                <input type="hidden" value="{$tamountd}" id="tamountd">
                <span class="value tamountd"> <b> Total Amount Denied: {$tamountd}</b> </span>
            </td>
            <td style="text-align:center;margin:auto"></td>
        </tr>
        </tbody>
    </table>
{/strip}