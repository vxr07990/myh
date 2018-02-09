{strip}
    <table name="dailyExpenseTable" class="table table-bordered blockContainer showInlineTable" style="margin-top:1%;">
        <thead>
            <tr>
                <input type="hidden" name="numDailyExpenses" value="{($DAILY_EXPENSES_LIST|@count)}"/>
                <th class="blockHeader" colspan="7">{vtranslate('LBL_CLAIMITEMS_DAILYEXPENSES', 'ClaimItems')}</th>
            </tr>
        </thead>
        <tbody>
        <tr class="fieldLabel">
				<td colspan="4" style="text-align:center;margin:auto;">&nbsp;</td>
				<td colspan="2" style="text-align:center;margin:auto;">Meals</td>
				<td style="text-align:center;margin:auto;">&nbsp;</td>
			</tr>
            <tr class="fieldLabel">
                <td style="text-align:center;margin:auto;width:20%;">Date of Expense</td>
                <td style="text-align:center;margin:auto;width:15%;">Number of Adults</td>
                <td style="text-align:center;margin:auto;width:15%;">Number of children & Ages</td>
                <td style="text-align:center;margin:auto;width:15%;">Daily Rate</td>
                <td style="text-align:center;margin:auto;width:10%;">Number</td>
                <td style="text-align:center;margin:auto;width:10%;">Total Cost</td>
                <td style="text-align:center;margin:auto;width:15%;">DAILY TOTAL</td>
            </tr>
        {assign var="tamount" value=0}
        {foreach key=ROW_NUM item=DAILY_EXPENSE from=$DAILY_EXPENSES_LIST}
            <tr style="text-align:center;margin:auto" class=" dailyExpenseRow">
                
                <td class="fieldValue" style="text-align:center;margin:auto">{DateTimeField::convertToUserFormat($DAILY_EXPENSE['expense_date'])}</td>
                <td class="fieldValue" style="text-align:center;margin:auto">{$DAILY_EXPENSE['no_adults']}</td>
                <td class="fieldValue" style="text-align:center;margin:auto">{$DAILY_EXPENSE['no_children']}</td>
                <td class="fieldValue" style="text-align:center;margin:auto">{$DAILY_EXPENSE['daily_rate']}</td>
                <td class="fieldValue" style="text-align:center;margin:auto">{$DAILY_EXPENSE['no_meals']}</td>
                <td class="fieldValue" style="text-align:center;margin:auto">{$DAILY_EXPENSE['total_cost_meals']}</td>
                <td class="fieldValue" style="text-align:center;margin:auto">{$DAILY_EXPENSE['daily_total']}</td>
            </tr>
            {assign var="tamount" value=$tamount+$DAILY_EXPENSE['daily_total']}
        {/foreach}
        <tr style="text-align:center;margin:auto" class="totalsRow">
            <td colspan="6" style="text-align:center;margin:auto"></td>
            <td style="text-align:center;margin:auto">
                <input type="hidden" value="{$tamount}" id="dtamount">
                <span class="value dtamount"> <b> Total Amount: {$tamount}</b> </span>
            </td>
        </tr>
        </tbody>
    </table>
{/strip}