{strip}
    <table class="table table-bordered blockContainer showInlineTable equalSplit" name="AddressListTable">
        <thead>
        <tr>
            <th class="blockHeader" colspan="4">{vtranslate('LBL_ADDRESSES', 'AddressList')}</th>
        </tr>
        </thead>
    </table>
    <div class="AddressListList" data-rel-module="AddressList">
        {foreach from=$ADDRESSESLIST item=ADDRESS_RECORD name=related_records_block key = ADDRESSLIST_ID}
            {assign var=WIDTHTYPE value=$USER_MODEL->get('rowheight')}
            {assign var=ADDRESSLIST_ID value=$ADDRESS_RECORD['addresslistid']}
            <div class="AddressListRecords"  data-row-no="{$ROWNO}" data-id = "{$ADDRESSLIST_ID}">
                <table class="table table-bordered equalSplit detailview-table">
                    <thead>
                    <tr>
                        <th class="blockHeader" colspan="10">
                            &nbsp;&nbsp;
                            <img class="cursorPointer alignMiddle blockToggle {if !($IS_HIDDEN)} hide {/if} "  src="{vimage_path('arrowRight.png')}" data-mode="hide" data-id={$ADDRESSLIST_ID}>
                            <img class="cursorPointer alignMiddle blockToggle {if ($IS_HIDDEN)} hide {/if}"  src="{vimage_path('arrowDown.png')}" data-mode="show" data-id={$ADDRESSLIST_ID}>
                            <span class="AddressListTitle">&nbsp;&nbsp;
                                {$ADDRESS_RECORD['address_type']}: {$ADDRESS_RECORD['address1']}, {$ADDRESS_RECORD['city']}, {$ADDRESS_RECORD['state']}, {$ADDRESS_RECORD['zip_code']}
                            </span>

                        </th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        {assign var=VIEW_NAME value='Edit'}
                        {assign var=COUNTER value=0}
                        {foreach key=FIELD_NAME item=FIELD_MODEL from=$ADDRESSLIST_BLOCK_FIELDS name=blockfields}
                        {if !empty($ADDRESS_RECORD[$FIELD_NAME])}
                            {assign var=FIELD_MODEL value=$FIELD_MODEL->set('fieldvalue',$ADDRESS_RECORD[$FIELD_NAME])}
                        {/if}
                        {assign var="isReferenceField" value=$FIELD_MODEL->getFieldDataType()}
                        {if $isReferenceField eq 'reference' && count($FIELD_MODEL->getReferenceList()) < 1}{continue}{/if}
                        {if $FIELD_MODEL->get('uitype') eq "20" or $FIELD_MODEL->get('uitype') eq "19"}
                        {if $COUNTER eq '1'}
                        <td class="{$WIDTHTYPE}"></td><td class="{$WIDTHTYPE}"></td>
                    </tr>
                    <tr>
                        {assign var=COUNTER value=0}
                        {/if}
                        {/if}
                        {if in_array($FIELD_MODEL->getName(),['city','long_carry'])}
                            {assign var=COUNTER value=2}
                        {elseif in_array($FIELD_MODEL->getName(),['state','zip_code','of_flights','of_elevators'])}
                            {assign var=COUNTER value=1}
                        {/if}
                        {if $COUNTER eq 2}
                    </tr>
                    <tr>
                        {assign var=COUNTER value=1}
                        {else}
                        {assign var=COUNTER value=$COUNTER+1}
                        {/if}
                        <td class="fieldLabel {$WIDTHTYPE}" style="width:auto">
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
                                            <select id="{$MODULE}_editView_fieldName_{$FIELD_MODEL->getName()}_dropDown" class="chzn-select referenceModulesList streched" style="width:160px;">
                                                <optgroup>
                                                    {foreach key=index item=value from=$REFERENCE_LIST}
                                                        <option value="{$value}" {if $value eq $REFERENCED_MODULE_NAME} selected {/if}>{vtranslate($value, $MODULE)}</option>
                                                    {/foreach}
                                                </optgroup>
                                            </select>
								</span>
                                    {else}
                                        <label class="muted pull-right marginRight10px">{if $FIELD_MODEL->isMandatory() eq true} <span class="redColor">*</span> {/if}{vtranslate($FIELD_MODEL->get('label'), $MODULE)}</label>
                                    {/if}
                                {else}
                                    {vtranslate($FIELD_MODEL->get('label'), $MODULE)}
                                {/if}
                                {if $isReferenceField neq "reference"}</label>{/if}
                        </td>
                        {if $FIELD_MODEL->get('uitype') neq "83"}
                            <td {if !in_array($FIELD_MODEL->getName(),['state','zip_code','of_flights','of_elevators','city','long_carry'])}colspan="2"{/if} class="fieldValue {$WIDTHTYPE}" {if $FIELD_MODEL->get('uitype') eq '19' or $FIELD_MODEL->get('uitype') eq '20'} colspan="3" {assign var=COUNTER value=$COUNTER+1} {/if} style="width:auto">
                                 <span class="value" data-field-type="{$FIELD_MODEL->getFieldDataType()}" {if $FIELD_MODEL->get('uitype') eq '19' or $FIELD_MODEL->get('uitype') eq '20' or $FIELD_MODEL->get('uitype') eq '21'} style="white-space:normal;" {/if}>
                                    {include file=vtemplate_path($FIELD_MODEL->getUITypeModel()->getDetailViewTemplateName(),'AddressList') FIELD_MODEL=$FIELD_MODEL USER_MODEL=$USER_MODEL MODULE='AddressList'}
                                 </span>
                            </td>
                        {/if}
                        {if $BLOCK_FIELDS|@count eq 1 and $FIELD_MODEL->get('uitype') neq "19" and $FIELD_MODEL->get('uitype') neq "20" and $FIELD_MODEL->get('uitype') neq "30" and $FIELD_MODEL->get('name') neq "recurringtype"}
                            <td class="{$WIDTHTYPE}" style="width:auto"></td><td  class="{$WIDTHTYPE}" style="width:auto"></td>
                        {/if}

                        {/foreach}
                        {* adding additional column for odd number of fields in a block *}
                        {if $ADDRESSLIST_BLOCK_FIELDS|@end eq true and $ADDRESSLIST_BLOCK_FIELDS|@count neq 1 and $COUNTER eq 1}
                            <td class="fieldLabel {$WIDTHTYPE}" style="width:auto"></td ><td colspan="2" class="{$WIDTHTYPE}" style="width:auto"></td>
                        {/if}
                    </tr>
                    </tbody>
                </table>
            </div>
        {/foreach}
    </div>
{/strip}