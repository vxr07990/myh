{strip}
    <table name="dailyExpenseTable" class="table table-bordered blockContainer showInlineTable" style="margin-top:1%;">
        <thead>
            <tr>
                <input type="hidden" name="numDailyExpenses" value="{($DAILY_EXPENSES_LIST|@count)}"/>
                <th class="blockHeader" colspan="8">{vtranslate('LBL_CLAIMITEMS_DAILYEXPENSES', 'ClaimItems')}</th>
            </tr>
        </thead>
        <tbody>
            <tr class="fieldLabel">
                <td colspan="8">
                    <button type="button" class="addDailyExpense">+</button>
                    <button type="button" class="addDailyExpense" style="clear:right;float:right">+</button>
                </td>
            </tr>
			<tr class="fieldLabel">
				<td colspan="5" style="text-align:center;margin:auto;">&nbsp;</td>
				<td colspan="2" style="text-align:center;margin:auto;">Meals</td>
				<td style="text-align:center;margin:auto;">&nbsp;</td>
			</tr>
            <tr class="fieldLabel">
                <td style="text-align:center;margin:auto;width:10%;">&nbsp;</td>
                <td style="text-align:center;margin:auto;width:20%;">Date of Expense</td>
                <td style="text-align:center;margin:auto;width:15%;">Number of Adults</td>
                <td style="text-align:center;margin:auto;width:15%;">Number of children & Ages</td>
                <td style="text-align:center;margin:auto;width:10%;">Daily Rate</td>
                <td style="text-align:center;margin:auto;width:10%;">Number</td>
                <td style="text-align:center;margin:auto;width:10%;">Total Cost</td>
                <td style="text-align:center;margin:auto;width:10%;">DAILY TOTAL</td>
            </tr>
            <tr style="text-align:center;margin:auto"class="defaultDailyExpense dailyExpenseRow hide">
                <td class="fieldValue" style="text-align:center;margin:auto">
                    <i title="Delete" class="icon-trash removeDailyExpense"></i>
                    <input type="hidden" class="default" name="dailyExpenseId_" value="none" />
                </td>
				<td class="fieldValue" style="text-align:center;margin:auto;padding-left:1%">
                    <div class="input-append row-fluid">
                        <div class="span10 row-fluid date">
                            <input type="text" class="span8 dateField default" name="expenseDate" data-date-format="{$CURRENT_USER_MODEL->get('date_format')}" value="{$EXPENSE_DATE}">
                            <span class="add-on"><i class="icon-calendar"></i></span>
                        </div>
                    </div>
                </td>
                <td class="fieldValue" style="text-align:center;margin:auto">
                    <input type="text" value="" class="nAdults default" name="nAdults" style="width: 80%;">
                </td>
                <td class="fieldValue" style="text-align:center;margin:auto">
                    <input type="text" value="" class="nChildren default" name="nChildren" style="width: 80%;">
                </td>
                <td class="fieldValue" style="text-align:center;margin:auto">
                    <input type="text" value="" class="dailyRate default" name="dailyRate" style="width: 80%;">
                </td>
                <td class="fieldValue" style="text-align:center;margin:auto">
                    <input type="text" value="" class="nMeals default" name="nMeals" style="width: 80%;">
                </td>
                <td class="fieldValue" style="text-align:center;margin:auto">
                    <input type="text" value="" class="tCostMeals default" name="tCostMeals" style="width: 80%;">
                </td>
                <td class="fieldValue" style="text-align:center;margin:auto">
                    <input type="text" value="" class="dailyTotal default" name="dailyTotal" style="width: 80%;">
                </td>
            </tr>
        {assign var="tamount" value=0}
        {foreach key=ROW_NUM item=DAILY_EXPENSE from=$DAILY_EXPENSES_LIST}
            <tr style="text-align:center;margin:auto" class="dailyExpenseRow{$ROW_NUM+1} dailyExpenseRow">
                <td class="fieldValue" style="text-align:center;margin:auto">
                    <i title="Delete" class="icon-trash removeDailyExpense"></i>
                    <input type="hidden" name="dailyExpenseId_{$ROW_NUM+1}" value="{$DAILY_EXPENSE['dailyexpenseid']}" />
					<input type="hidden" class="default" name="dailyExpenseDelete_{$ROW_NUM+1}" value="" />
                </td>
                <td class="fieldValue" style="text-align:center;margin:auto;padding-left:1%">
                    <div class="input-append row-fluid">
                        <div class="span10 row-fluid date">
                            <input type="text" class="span8 dateField default" name="expenseDate{$ROW_NUM+1}" data-date-format="{$CURRENT_USER_MODEL->get('date_format')}" value="{DateTimeField::convertToUserFormat($DAILY_EXPENSE['expense_date'])}">
                            <span class="add-on"><i class="icon-calendar"></i></span>
                        </div>
                    </div>
                </td>
                <td class="fieldValue" style="text-align:center;margin:auto">
                    <input type="text" value="{$DAILY_EXPENSE['no_adults']}" class="nAdults" name="nAdults{$ROW_NUM+1}" style="width: 80%;">
                </td>
                <td class="fieldValue" style="text-align:center;margin:auto">
                    <input type="text" value="{$DAILY_EXPENSE['no_children']}" class="nChildren" name="nChildren{$ROW_NUM+1}" style="width: 80%;">
                </td>
                <td class="fieldValue" style="text-align:center;margin:auto">
                    <input type="text" value="{$DAILY_EXPENSE['daily_rate']}" class="dailyRate" name="dailyRate{$ROW_NUM+1}" style="width: 80%;">
                </td>
                <td class="fieldValue" style="text-align:center;margin:auto">
                    <input type="text" value="{$DAILY_EXPENSE['no_meals']}" class="nMeals" name="nMeals{$ROW_NUM+1}" style="width: 80%;">
                </td>
                <td class="fieldValue" style="text-align:center;margin:auto">
                    <input type="text" value="{$DAILY_EXPENSE['total_cost_meals']}" class="tCostMeals" name="tCostMeals{$ROW_NUM+1}" style="width: 80%;">
                </td>
                <td class="fieldValue" style="text-align:center;margin:auto">
                    <input type="text" value="{$DAILY_EXPENSE['daily_total']}" class="dailyTotal" name="dailyTotal{$ROW_NUM+1}" style="width: 80%;">
                </td>
            </tr>
            {assign var="tamount" value=$tamount+$DAILY_EXPENSE['daily_total']}
        {/foreach}
        <tr style="text-align:center;margin:auto" class="totalsRow">
            <td colspan="7" style="text-align:center;margin:auto"></td>
            <td style="text-align:center;margin:auto">
                <input type="hidden" value="{$tamount}" id="dtamount">
                <span class="value dtamount"> <b> Total Amount: {$tamount}</b> </span>
            </td>
        </tr>
        </tbody>
    </table>
{/strip}