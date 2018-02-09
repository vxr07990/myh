{assign var=WIDTHTYPE value=$USER_MODEL->get('rowheight')}
{assign var=MENUGROUPS_MODULE_MODEL value=$MENUGROUPS_RECORD_MODEL->getModule()}


<div class="MenuGroupsRecords"  data-row-no="{$ROWNO}" data-id = "{$MENUGROUPS_ID}">
<table class="table table-bordered blockContainer showInlineTable equalSplit">
    <thead>
    <tr>
        <th class="blockHeader" colspan="4">
            &nbsp;&nbsp;
            <img class="cursorPointer alignMiddle blockToggle {if !($IS_HIDDEN)} hide {/if} "  src="{vimage_path('arrowRight.png')}" data-mode="hide" data-id={$MENUGROUPS_ID}>
            <img class="cursorPointer alignMiddle blockToggle {if ($IS_HIDDEN)} hide {/if}"  src="{vimage_path('arrowDown.png')}" data-mode="show" data-id={$MENUGROUPS_ID}>
            <span class="MenuGroupsTitle">&nbsp;&nbsp;
                {if $MENUGROUPS_RECORD_MODEL->get('group_name') neq ''}
                    {$MENUGROUPS_RECORD_MODEL->get('group_name')} {if $MENUGROUPS_RECORD_MODEL->get('group_name') eq 'Menu Shortcuts'} <span class="redColor">*</span> {/if}

                {else}
                    Description of Group
                {/if}
            </span>

            {if $ROWNO neq 1}
                <i class="icon-trash pull-right deleteMappingButton" title="Delete"></i>
            {/if}

            <input type="hidden" name="menugroupid_{$ROWNO}" value="{$MENUGROUPS_ID}">
            <input type="hidden" name="menugroup_deleted_{$ROWNO}" value="">
        </th>
    </tr>
    </thead>
    <tbody>
    <tr>
        {assign var=VIEW_NAME value='Edit'}
        {assign var=COUNTER value=0}
        {foreach key=FIELD_NAME item=FIELD_MODEL from=$FIELDS_LIST name=blockfields}
        {if $FIELD_NAME eq 'menucreator_id'}{continue}{/if}
        {if $MENUGROUPS_RECORD_MODEL}
            {assign var=FIELD_MODEL value=$FIELD_MODEL->set('fieldvalue',$MENUGROUPS_RECORD_MODEL->get($FIELD_NAME))}
        {/if}
        {assign var=CUSTOM_FIELD_NAME value=$FIELD_NAME|cat:"_"|cat:$ROWNO}
        {assign var=FIELD_MODEL value=$FIELD_MODEL->set('name',$CUSTOM_FIELD_NAME)}
        {assign var=FIELD_MODEL value=$FIELD_MODEL->set('noncustomname',$FIELD_NAME)}
        {if $MENUGROUPS_RECORD_MODEL->get('group_name') eq 'Menu Shortcuts'}
			{assign var=FIELD_MODEL value=$FIELD_MODEL->set('typeofdata','V~M')}
		{/if}



        {assign var="isReferenceField" value=$FIELD_MODEL->getFieldDataType()}
        {if $FIELD_MODEL->get('uitype') eq "20" or $FIELD_MODEL->get('uitype') eq "19"}
        {if $COUNTER eq '1'}
        <td class="{$WIDTHTYPE}"></td><td class="{$WIDTHTYPE}"></td>
    </tr>
    <tr>
        {assign var=COUNTER value=0}
        {/if}
        {/if}
        {if $COUNTER eq 2}
    </tr>
    <tr>
        {assign var=COUNTER value=1}
        {else}
        {assign var=COUNTER value=$COUNTER+1}
        {/if}
        {if $ROWNO neq 1}
        <td class="fieldLabel {$WIDTHTYPE}">
            {if $isReferenceField neq "reference"}<label class="muted pull-right marginRight10px">{/if}
                {if $FIELD_MODEL->isMandatory() eq true && $isReferenceField neq "reference"} <span class="redColor">*</span> {/if}
                {if $isReferenceField eq "reference"}
                    {assign var="REFERENCE_LIST" value=$FIELD_MODEL->getReferenceList()}
                    {assign var="REFERENCE_LIST_COUNT" value=count($REFERENCE_LIST)}
                    {if $REFERENCE_LIST_COUNT > 1}
                        {assign var="DISPLAYID" value=$FIELD_MODEL->get('fieldvalue')}
                        {assign var="REFERENCED_MODULE_STRUCT" value=$FIELD_MODEL->getUITypeModel()->getReferenceModule($DISPLAYID)}
                        {if !empty($REFERENCED_MODULE_STRUCT)}
                            {assign var="REFERENCED_MODULE_NAME" value=$REFERENCED_MODULE_STRUCT->get('name')}
                        {/if}
                        <span class="pull-right">
                                        {if $FIELD_MODEL->isMandatory() eq true} <span class="redColor">*</span> {/if}
                            <select id="{'MenuGroups'}_editView_fieldName_{$FIELD_MODEL->getName()}_dropDown" class="chzn-select referenceModulesList streched" style="width:160px;">
                                <optgroup>
                                    {foreach key=index item=value from=$REFERENCE_LIST}
                                        <option value="{$value}" {if $value eq $REFERENCED_MODULE_NAME} selected {/if}>{vtranslate($value, 'MenuGroups')}</option>
                                    {/foreach}
                                </optgroup>
                            </select>
                        </span>
                    {else}
                        <label class="muted pull-right marginRight10px">{if $FIELD_MODEL->isMandatory() eq true} <span class="redColor">*</span> {/if}{vtranslate($FIELD_MODEL->get('label'), 'MenuGroups')}</label>
                    {/if}
                {elseif $FIELD_MODEL->get('uitype') eq "83"}
                    {include file=vtemplate_path($FIELD_MODEL->getUITypeModel()->getTemplateName(),'MenuGroups') COUNTER=$COUNTER MODULE='MenuGroups'}
                {else}
                    {vtranslate($FIELD_MODEL->get('label'), 'MenuGroups')}
                {/if}
                {if $isReferenceField neq "reference"}</label>{/if}
        </td>
        {/if}
        {if $FIELD_MODEL->get('uitype') neq "83"}
            <td class="fieldValue {$WIDTHTYPE}" {if $FIELD_MODEL->get('uitype') eq '19' or $FIELD_MODEL->get('uitype') eq '20' or $FIELD_MODEL->get('noncustomname') eq 'group_module'} colspan="3" {assign var=COUNTER value=$COUNTER+1} {/if}>

                {if $ROWNO eq 1}
                    {if $FIELD_NAME eq 'group_module'}
                        <div class="row-fluid">
                                    <span class="span12">
                                        {include file=vtemplate_path($FIELD_MODEL->getUITypeModel()->getTemplateName(),'MenuGroups') BLOCK_FIELDS=$FIELDS_LIST MODULE='MenuGroups' FIELD_MODEL=$FIELD_MODEL MODULE_MODEL =MENUGROUPS_MODULE_MODEL MODE=$MODE}
                                    </span>
                        </div>
                    {else}
                        <div class="row-fluid" hidden>
                            <span class="span10">
                                {include file=vtemplate_path($FIELD_MODEL->getUITypeModel()->getTemplateName(),'MenuGroups') BLOCK_FIELDS=$FIELDS_LIST MODULE='MenuGroups' FIELD_MODEL=$FIELD_MODEL MODULE_MODEL =MENUGROUPS_MODULE_MODEL}
                            </span>
                        </div>
                    {/if}
                {else}
                    <div class="row-fluid">
                            <span class="span10">
                                {include file=vtemplate_path($FIELD_MODEL->getUITypeModel()->getTemplateName(),'MenuGroups') BLOCK_FIELDS=$FIELDS_LIST MODULE='MenuGroups' FIELD_MODEL=$FIELD_MODEL MODULE_MODEL =MENUGROUPS_MODULE_MODEL}
                            </span>
                    </div>
                {/if}
            </td>
        {/if}
        {if $FIELDS_LIST|@count eq 1 and $FIELD_MODEL->get('uitype') neq "19" and $FIELD_MODEL->get('uitype') neq "20" and $FIELD_MODEL->get('uitype') neq "30" and $FIELD_MODEL->get('name') neq "recurringtype"}
            <td class="{$WIDTHTYPE}"></td><td class="{$WIDTHTYPE}"></td>
        {/if}

        {/foreach}
        {* adding additional column for odd number of fields in a block *}
        {if $FIELDS_LIST|@end eq true and $FIELDS_LIST|@count neq 1 and $COUNTER eq 1}
            <td class="fieldLabel {$WIDTHTYPE}"></td><td class="{$WIDTHTYPE}"></td>
        {/if}
    </tr>
    </tbody>
</table>
</div>