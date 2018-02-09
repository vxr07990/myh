{assign var=WIDTHTYPE value=$USER_MODEL->get('rowheight')}
{assign var=ITEMCODES_MAPPING_MODULE_MODEL value=$ITEMCODES_MAPPING_RECORD_MODEL->getModule()}
{if $NEW_BLOCK}
    {assign var=IS_HIDDEN value=false}
{else}
    {assign var=IS_HIDDEN value=true}
{/if}

<div class="ItemCodesMappingRecords"  data-row-no="{$ROWNO}" data-id = "{if $IS_DUPLICATE != true}{$ITEMCODES_MAPPING_ID}{/if}">
<table class="table table-bordered blockContainer showInlineTable equalSplit">
    <thead>
    <tr>
        <th class="blockHeader" colspan="4">
            &nbsp;&nbsp;
            <img class="cursorPointer alignMiddle blockToggle {if !($IS_HIDDEN)} hide {/if} "  src="{vimage_path('arrowRight.png')}" data-mode="hide" data-id={$ITEMCODES_MAPPING_ID}>
            <img class="cursorPointer alignMiddle blockToggle {if ($IS_HIDDEN)} hide {/if}"  src="{vimage_path('arrowDown.png')}" data-mode="show" data-id={$ITEMCODES_MAPPING_ID}>
            <span class="ItemCodesMappingTitle">&nbsp;&nbsp;
                {if $ITEMCODES_MAPPING_RECORD_MODEL->get('commodities') neq '' && $ITEMCODES_MAPPING_RECORD_MODEL->get('itcmapping_businessline') neq '' && $ITEMCODES_MAPPING_RECORD_MODEL->get('itcmapping_billingtype') neq '' && $ITEMCODES_MAPPING_RECORD_MODEL->get('itcmapping_authority') neq ''}

                    {assign var=BUSINESSLINE_FIELD value=$ITEMCODES_MAPPING_MODULE_MODEL->getField('itcmapping_businessline')}
                    {assign var=COMMODITIES_FIELD value=$ITEMCODES_MAPPING_MODULE_MODEL->getField('commodities')}
                    {assign var=BILLINGTYPE_FIELD value=$ITEMCODES_MAPPING_MODULE_MODEL->getField('itcmapping_billingtype')}
                    {assign var=AUTHORITY_FIELD value=$ITEMCODES_MAPPING_MODULE_MODEL->getField('itcmapping_authority')}
                    {assign var=BUSINESSLINE_PICKLIST_VALUES value=$BUSINESSLINE_FIELD->getPicklistValues()}
                    {assign var=COMMODITIES_PICKLIST_VALUES value=$COMMODITIES_FIELD->getPicklistValues()}
                    {assign var=BILLINGTYPE_PICKLIST_VALUES value=$BILLINGTYPE_FIELD->getPicklistValues()}
                    {assign var=AUTHORITY_PICKLIST_VALUES value=$AUTHORITY_FIELD->getPicklistValues()}
                    {assign var="BUSINESSLINE_VALUE_LIST" value=explode(' |##| ',$ITEMCODES_MAPPING_RECORD_MODEL->get('itcmapping_businessline'))}
                    {assign var="COMMODITIES_VALUE_LIST" value=explode(' |##| ',$ITEMCODES_MAPPING_RECORD_MODEL->get('commodities'))}
                    {assign var="BILLINGTYPE_VALUE_LIST" value=explode(' |##| ',$ITEMCODES_MAPPING_RECORD_MODEL->get('itcmapping_billingtype'))}
                    {assign var="AUTHORITY_VALUE_LIST" value=explode(' |##| ',$ITEMCODES_MAPPING_RECORD_MODEL->get('itcmapping_authority'))}
                    {if $BUSINESSLINE_PICKLIST_VALUES|count eq $BUSINESSLINE_VALUE_LIST|count}
                        {assign var="BUSINESSLINE_VALUE_LIST" value='All'}
                    {else}
                        {assign var="BUSINESSLINE_VALUE_LIST" value=implode(', ',$BUSINESSLINE_VALUE_LIST)}
                    {/if}
                    {if $COMMODITIES_PICKLIST_VALUES|count eq $COMMODITIES_VALUE_LIST|count}
                        {assign var="COMMODITIES_VALUE_LIST" value='All'}
                    {else}
                        {assign var="COMMODITIES_VALUE_LIST" value=implode(', ',$COMMODITIES_VALUE_LIST)}
                    {/if}
                    {if $BILLINGTYPE_PICKLIST_VALUES|count eq $BILLINGTYPE_VALUE_LIST|count}
                        {assign var="BILLINGTYPE_VALUE_LIST" value='All'}
                    {else}
                        {assign var="BILLINGTYPE_VALUE_LIST" value=implode(', ',$BILLINGTYPE_VALUE_LIST)}
                    {/if}
                    {if $AUTHORITY_PICKLIST_VALUES|count eq $AUTHORITY_VALUE_LIST|count}
                        {assign var="AUTHORITY_VALUE_LIST" value='All'}
                    {else}
                        {assign var="AUTHORITY_VALUE_LIST" value=implode(', ',$AUTHORITY_VALUE_LIST)}
                    {/if}
                    {$BUSINESSLINE_VALUE_LIST} / {$COMMODITIES_VALUE_LIST} / {$BILLINGTYPE_VALUE_LIST} / {$AUTHORITY_VALUE_LIST}
                {else}
                    Description of Mapping
                {/if}
            </span>
            <i class="icon-trash pull-right deleteMappingButton" title="Delete"></i>
            <i class="icon-file pull-right copyMappingButton" title="Copy"></i>
            <input type="hidden" name="itemcodesmappingid_{$ROWNO}" value="{if $IS_DUPLICATE != true}{$ITEMCODES_MAPPING_ID}{/if}">
            <input type="hidden" name="mapping_deleted_{$ROWNO}" value="">
        </th>
    </tr>
    </thead>
    <tbody {if $IS_HIDDEN}class="hide"{/if}>
    <tr>
        {assign var=VIEW_NAME value='Edit'}
        {assign var=COUNTER value=0}
        {foreach key=FIELD_NAME item=FIELD_MODEL from=$FIELDS_LIST name=blockfields}
        {if $FIELD_NAME eq 'itcmapping_itemcode'}{continue}{/if}
        {if $ITEMCODES_MAPPING_RECORD_MODEL}
            {assign var=FIELD_MODEL value=$FIELD_MODEL->set('fieldvalue',$ITEMCODES_MAPPING_RECORD_MODEL->get($FIELD_NAME))}
        {/if}
        {assign var=CUSTOM_FIELD_NAME value=$FIELD_NAME|cat:"_"|cat:$ROWNO}
        {assign var=FIELD_MODEL value=$FIELD_MODEL->set('name',$CUSTOM_FIELD_NAME)}
        {assign var=FIELD_MODEL value=$FIELD_MODEL->set('noncustomname',$FIELD_NAME)}


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
                            <select id="{'ItemCodesMapping'}_editView_fieldName_{$FIELD_MODEL->getName()}_dropDown" class="chzn-select referenceModulesList streched" style="width:160px;">
                                <optgroup>
                                    {foreach key=index item=value from=$REFERENCE_LIST}
                                        <option value="{$value}" {if $value eq $REFERENCED_MODULE_NAME} selected {/if}>{vtranslate($value, 'ItemCodesMapping')}</option>
                                    {/foreach}
                                </optgroup>
                            </select>
                                    </span>
                    {else}
                        <label class="muted pull-right marginRight10px">{if $FIELD_MODEL->isMandatory() eq true} <span class="redColor">*</span> {/if}{vtranslate($FIELD_MODEL->get('label'), 'ItemCodesMapping')}</label>
                    {/if}
                {elseif $FIELD_MODEL->get('uitype') eq "83"}
                    {include file=vtemplate_path($FIELD_MODEL->getUITypeModel()->getTemplateName(),'ItemCodesMapping') COUNTER=$COUNTER MODULE='ItemCodesMapping'}
                {else}
                    {vtranslate($FIELD_MODEL->get('label'), 'ItemCodesMapping')}
                {/if}
                {if $isReferenceField neq "reference"}</label>{/if}
        </td>
        {if $FIELD_MODEL->get('uitype') neq "83"}
            <td class="fieldValue {$WIDTHTYPE}" {if $FIELD_MODEL->get('uitype') eq '19' or $FIELD_MODEL->get('uitype') eq '20'} colspan="3" {assign var=COUNTER value=$COUNTER+1} {/if}>
                <div class="row-fluid">
                            <span class="span10">
                                {include file=vtemplate_path($FIELD_MODEL->getUITypeModel()->getTemplateName(),'ItemCodesMapping') BLOCK_FIELDS=$FIELDS_LIST MODULE='ItemCodesMapping' MODULE_MODEL =ITEMCODES_MAPPING_MODULE_MODEL}
                            </span>
                </div>
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
