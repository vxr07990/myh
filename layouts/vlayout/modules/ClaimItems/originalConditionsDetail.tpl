{strip}
    <table name="originalConditionTable" class="table table-bordered blockContainer showInlineTable" style="margin-top:1%;">
        <thead>
            <tr>
                <input type="hidden" name="numOriginalConditions" value="{($ORIGINAL_CONDITION_LIST|@count)}"/>
                <th class="blockHeader" colspan="6">{vtranslate('LBL_ORIGINAL_CONDITIONS', 'ClaimItems')}</th>
            </tr>
        </thead>
        <tbody>
        <tr class="fieldLabel">
            <td style="text-align:center;margin:auto;width:20%;">Inventory #</td>
            <td style="text-align:center;margin:auto;width:20%;">Tag Color</td>
            <td style="text-align:center;margin:auto;width:20%;">Original Condition</td>
            <td style="text-align:center;margin:auto;width:20%;">Exceptions</td>
            <td style="text-align:center;margin:auto;width:20%;">Date Taken</td>
        </tr> 
        {foreach key=ROW_NUM item=ORIGINAL_CONDITION from=$ORIGINAL_CONDITION_LIST}
            <tr style="text-align:center;margin:auto" class="originalConditionRow{$ROW_NUM} originalConditionRow">
                <td class="fieldValue typeCell" style="text-align:center;margin:auto">{$ORIGINAL_CONDITION['inventory_number']}</td>
                <td class="fieldValue" style="text-align:center;margin:auto">{$ORIGINAL_CONDITION['tag_color']}</td>
                <td class="fieldValue" style="text-align:center;margin:auto">{$ORIGINAL_CONDITION['original_conditions']}</td>
                <td class="fieldValue" style="text-align:center;margin:auto">{$ORIGINAL_CONDITION['exceptions']}</td>
                <td class="fieldValue" style="text-align:center;margin:auto">{$ORIGINAL_CONDITION['date_taken']}</td>
            </tr>
        {/foreach}
        </tbody>
    </table>
{/strip}