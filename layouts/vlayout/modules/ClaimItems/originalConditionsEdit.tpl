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
                <td colspan="6">
                    <button type="button" class="addOriginalCondition">+</button>
                    <button type="button" class="addOriginalCondition" style="clear:right;float:right">+</button>
                </td>
            </tr>
            <tr class="fieldLabel">
                <td style="text-align:center;margin:auto;width:10%;"> </td>
                <td style="text-align:center;margin:auto;width:18%;">Inventory #</td>
                <td style="text-align:center;margin:auto;width:18%;">Tag Color</td>
                <td style="text-align:center;margin:auto;width:18%;">Original Condition</td>
                <td style="text-align:center;margin:auto;width:18%;">Exceptions</td>
                <td style="text-align:center;margin:auto;width:18%;">Date Taken</td>
            </tr>
            <tr style="text-align:center;margin:auto"class="defaultOriginalCondition originalConditionRow hide">
                <td class="fieldValue" style="text-align:center;margin:auto">
                    <i title="Delete" class="icon-trash removeOriginalCondition"></i>
                    <input type="hidden" class="default" name="originalConditionId" value="none" />
                </td>
                <td class="fieldValue typeCell" style="text-align:center;margin:auto">
                    <input type="text" value="" class="default" name="inventoryNumber" style="width: 80%;">
                </td>
                <td class="fieldValue" style="text-align:center;margin:auto">
                    <input type="text" value="" class="default" name="tagColor" style="width: 80%;">
                </td>
                <td class="fieldValue" style="text-align:center;margin:auto">
                    <input type="text" value="" class="default" name="originalCondition" style="width: 80%;">
                </td>
                <td class="fieldValue" style="text-align:center;margin:auto">
                    <input type="text" value="" class="default" name="exceptions" style="width: 80%;">
                </td>
                <td class="fieldValue" style="text-align:center;margin:auto;padding-left:1%">
                    <div class="input-append row-fluid">
                        <div class="span10 row-fluid date">
                            <input type="text" class="span8 dateField default" name="dateTaken" data-date-format="{$CURRENT_USER_MODEL->get('date_format')}" value="{$DATE}">
                            <span class="add-on"><i class="icon-calendar"></i></span>
                        </div>
                    </div>
                </td>
            </tr>
        {foreach key=ROW_NUM item=ORIGINAL_CONDITION from=$ORIGINAL_CONDITION_LIST}
            <tr style="text-align:center;margin:auto" class="originalConditionRow{$ROW_NUM+1} originalConditionRow">
                <td class="fieldValue" style="text-align:center;margin:auto">
                    <i title="Delete" class="icon-trash removeOriginalCondition"></i>
                    <input type="hidden" name="originalConditionId_{$ROW_NUM+1}" value="{$ORIGINAL_CONDITION['original_condition_id']}" />
                </td>
                <td class="fieldValue" style="text-align:center;margin:auto">
                    <input type="text" value="{$ORIGINAL_CONDITION['inventory_number']}" name="inventoryNumber{$ROW_NUM+1}" style="width: 80%;">
                </td>
                <td class="fieldValue" style="text-align:center;margin:auto">
                    <input type="text" value="{$ORIGINAL_CONDITION['tag_color']}" name="tagColor{$ROW_NUM+1}" style="width: 80%;">
                </td> 
                <td class="fieldValue" style="text-align:center;margin:auto">
                    <input type="text" value="{$ORIGINAL_CONDITION['original_conditions']}" name="originalCondition{$ROW_NUM+1}" style="width: 80%;">
                </td>
                <td class="fieldValue" style="text-align:center;margin:auto">
                    <input type="text" value="{$ORIGINAL_CONDITION['exceptions']}" name="exceptions{$ROW_NUM+1}" style="width: 80%;">
                </td> 
                <td class="fieldValue" style="text-align:center;margin:auto;padding-left:1%">
                    <div class="input-append row-fluid">
                        <div class="span10 row-fluid date">
                            <input type="text" class="span8 dateField" name="dateTaken{$ROW_NUM+1}" data-date-format="{$CURRENT_USER_MODEL->get('date_format')}" value="{$ORIGINAL_CONDITION['date_taken']}">
                            <span class="add-on"><i class="icon-calendar"></i></span>
                        </div>
                    </div>
                </td>
            </tr>
        {/foreach}
        </tbody>
    </table>
{/strip}