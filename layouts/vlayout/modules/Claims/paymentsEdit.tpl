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
                <td colspan="6">
                    <button type="button" class="addClaimPayment">+</button>
                    <button type="button" class="addClaimPayment" style="clear:right;float:right">+</button>
                </td>
            </tr>
            <tr class="fieldLabel">
                <td style="text-align:center;margin:auto;width:10%;"> </td>
                <td style="text-align:center;margin:auto;width:30%;">Fees</td>
                <td style="text-align:center;margin:auto;width:30%;">Date Requested</td>
                <td style="text-align:center;margin:auto;width:30%;">Amount</td>
            </tr>
            <tr style="text-align:center;margin:auto"class="defaultPayment paymentRow hide">
                <td class="fieldValue" style="text-align:center;margin:auto">
                    <i title="Delete" class="icon-trash removePayment"></i>
                    <input type="hidden" class="default" name="paymentId" value="none" />
                </td>
                <td class="fieldValue typeCell" style="text-align:center;margin:auto">
                    <select style="text-align:left" class="select default validate" name="paymentFees">
                        <option value="-">{vtranslate('LBL_SELECT_OPTION','Vtiger')}</option>
                        <option value="Inspection">Inspection</option>
                        <option value="Cancelled Appointment">Cancelled Appointment</option>
                        <option value="Arbitration Filing">Arbitration Filing</option>
                        <option value="Appraisal">Appraisal</option>
                    </select>
                </td>
                <td class="fieldValue" style="text-align:center;margin:auto;padding-left:1%">
                    <div class="input-append row-fluid">
                        <div class="span8 row-fluid date">
                            <input type="text" class="span6 dateField default" name="feesDate" data-date-format="{$CURRENT_USER_MODEL->get('date_format')}" value="{$DATE}">
                            <span class="add-on"><i class="icon-calendar"></i></span>
                        </div>
                    </div>
                </td>
                <td class="fieldValue" style="text-align:center;margin:auto">
                    <input type="text" value="" class="default feesAmount" name="feesAmount">
                </td>
            </tr>
        {assign var="tamount" value=0}
        {foreach key=ROW_NUM item=PAYMENT from=$PAYMENT_LIST}
            <tr style="text-align:center;margin:auto" class="paymentRow{$ROW_NUM+1} paymentRow">
                <td class="fieldValue" style="text-align:center;margin:auto">
                    <i title="Delete" class="icon-trash removePayment"></i>
                    <input type="hidden" name="paymentId_{$ROW_NUM+1}" value="{$PAYMENT['paymentId']}" />
                </td>
                <td class="fieldValue typeCell" style="text-align:center;margin:auto">
                    <select class="chzn-select" name="paymentFees{$ROW_NUM+1}" data-selected-value="{$PAYMENT['fees']}">
                        <option value="-">{vtranslate('LBL_SELECT_OPTION','Vtiger')}</option>
                        <option value="Inspection">Inspection</option>
                        <option value="Cancelled Appointment">Cancelled Appointment</option>
                        <option value="Arbitration Filing">Arbitration Filing</option>
                        <option value="Appraisal">Appraisal</option>
                    </select>
                </td>
                <td class="fieldValue" style="text-align:center;margin:auto;padding-left:1%">
                    <div class="input-append row-fluid">
                        <div class="span8 row-fluid date">
                            <input type="text" class="span6 dateField" name="feesDate{$ROW_NUM+1}" data-date-format="{$CURRENT_USER_MODEL->get('date_format')}" value="{if $PAYMENT['date']}{$PAYMENT['date']}{else}{$DATE}{/if}">
                            <span class="add-on"><i class="icon-calendar"></i></span>
                        </div>
                    </div>
                </td>
                <td class="fieldValue" style="text-align:center;margin:auto">
                    <input type="text" value="{$PAYMENT['amount']}" name="feesAmount{$ROW_NUM+1}" class="feesAmount">
                </td>
            </tr>
            {assign var="tamount" value=$tamount+$PAYMENT['amount']}
        {/foreach}
        <tr style="text-align:center;margin:auto" class="totalsRow">
            <td colspan="3" style="text-align:center;margin:auto"> </td>
            <td style="text-align:center;margin:auto"> <input type="hidden" id="tamount" value="{$tamount}"> <span class="value tamount"> <b>Amount Total: {$tamount}</b> </span>
            </td>
        </tr>
        </tbody>
    </table>
{/strip}