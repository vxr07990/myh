{strip}
    <table class="table table-bordered blockContainer showInlineTable equalSplit" name="MenuGroupsTable"
           xmlns="http://www.w3.org/1999/html">
        <thead>
        <tr>
            <th class="blockHeader" colspan="4">
                <img class="cursorPointer alignMiddle blockToggle {if !($IS_HIDDEN)} hide {/if} "  src="{vimage_path('arrowRight.png')}" data-mode="hide" data-id={$MENUGROUPS_ID}>
                <img class="cursorPointer alignMiddle blockToggle {if ($IS_HIDDEN)} hide {/if}"  src="{vimage_path('arrowDown.png')}" data-mode="show" data-id={$MENUGROUPS_ID}>  &nbsp;&nbsp;
                {vtranslate('LBL_MENUGROUPS', 'MenuGroups')}</th>
        </tr>
        </thead>
            {assign var=GROUPNAME_MODEL value=$MENUGROUPS_MODULE_MODEL->getField("group_name")}
            {assign var=GROUPSEQUENCE_MODEL value=$MENUGROUPS_MODULE_MODEL->getField("group_sequence")}
            {assign var=GROUPMODULE_MODEL value=$MENUGROUPS_MODULE_MODEL->getField("group_module")}
    {foreach key=ROW_NUM item=MENU_GROUPS from=$MENUGROUPS_LIST}
        <tr style="margin:auto" class="menugroupsRow{$ROW_NUM+1} menugroupsRow">
            <td class="fieldLabel medium">
                <label class="muted">{vtranslate($GROUPNAME_MODEL->get('label'),'MenuGroups')}</label>
            </td>
            <td class="fieldValue typeCell" style="text-align:center;margin:auto">
                <div class="row-fluid">
                    {assign var=GROUPNAME_MODEL value=$GROUPNAME_MODEL->set('fieldvalue',$MENU_GROUPS->get('group_name'))}
                    <span class="span10">
							{$GROUPNAME_MODEL->getDisplayValue($GROUPNAME_MODEL->get('fieldvalue'))}
                    </span>
                </div>
            </td>
            <td class="fieldLabel medium">
                <label class="muted">{vtranslate($GROUPSEQUENCE_MODEL->get('label'),'MenuGroups')}</label>
            </td>
            <td class="fieldValue typeCell" style="text-align:center;margin:auto">
                <div class="row-fluid">
                    {assign var=GROUPSEQUENCE_MODEL value=$GROUPSEQUENCE_MODEL->set('fieldvalue',$MENU_GROUPS->get('group_sequence'))}
                    <span class="span10">
							{$GROUPSEQUENCE_MODEL->getDisplayValue($GROUPSEQUENCE_MODEL->get('fieldvalue'))}
                    </span>
                </div>
            </td>
        </tr>
        <tr colspan="4">
            <td class="fieldLabel medium" colspan="1">
                <label class="muted">{vtranslate($GROUPMODULE_MODEL->get('label'),'MenuGroups')}</label>
            </td>
            <td class="fieldValue typeCell" style="text-align:center;margin:auto" colspan="3">
                <div class="row-fluid">
                    {assign var=FIELD_MODEL value=$GROUPMODULE_MODEL->set('fieldvalue',$MENU_GROUPS->get('group_module'))}
                    {assign var=FIELD_INFO value=Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($FIELD_MODEL->getFieldInfo()))}
                    {assign var=PICKLIST_VALUES value=$FIELD_MODEL->getPicklistValues()}
                    {assign var=SPECIAL_VALIDATOR value=$FIELD_MODEL->getValidator()}
                    {assign var=FIELD_VALUE_LIST value=explode(' |##| ',$FIELD_MODEL->get('fieldvalue'))}
                    {assign var=ITEM value=[]}
                    {foreach item=MODULE_NAME from=$FIELD_VALUE_LIST}
                        {if $PICKLIST_VALUES[$MODULE_NAME]}
                            {$ITEM[] = $PICKLIST_VALUES[$MODULE_NAME]}
                        {/if}
                    {/foreach}
                    <span class="span10">
                        {$GROUPMODULE_MODEL->getDisplayValue($ITEM)}
                    </span>
                </div>
            </td>
        </tr>
    {/foreach}
    </table>
{/strip}