{strip}
    <table class="table table-bordered blockContainer showInlineTable equalSplit" >
        <thead>
        <tr>
            <th class="blockHeader" colspan="4">{vtranslate('LBL_ADDRESS_DETAIL', 'OrdersTaskAddresses')}</th>
        </tr>
        </thead>
    </table>
    <div>
        {foreach from=$ADDRESSES item=ADDRESS_RECORD name=related_records_block key = ADDRESS_ID}
            {assign var=WIDTHTYPE value=$USER_MODEL->get('rowheight')}
            {assign var=ADDRESS_ID value=$ADDRESS_RECORD['addresslistid']}
            <div class="AddressListRecords">
                <table class="table table-bordered equalSplit detailview-table">
                    <thead>
                    <tr>
                        <th class="blockHeader" colspan="4">
                            &nbsp;&nbsp;
                            <img class="cursorPointer alignMiddle blockToggle {if !($IS_HIDDEN)} hide {/if} "  src="{vimage_path('arrowRight.png')}" data-mode="hide" data-id={$ADDRESSLIST_ID}>
                            <img class="cursorPointer alignMiddle blockToggle {if ($IS_HIDDEN)} hide {/if}"  src="{vimage_path('arrowDown.png')}" data-mode="show" data-id={$ADDRESSLIST_ID}>
                        </th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        {assign var=VIEW_NAME value='Edit'}
                        {assign var=COUNTER value=0}
                        {foreach key=FIELD_NAME item=FIELD_MODEL from=$ADDRESSES_BLOCK_FIELDS name=blockfields}
                        {assign var=FIELD_MODEL value=$FIELD_MODEL->set('fieldvalue',$ADDRESS_RECORD[$FIELD_NAME])}
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
                        {if $COUNTER eq 2}
                    </tr>
                    <tr>
                        {assign var=COUNTER value=1}
                        {else}
                        {assign var=COUNTER value=$COUNTER+1}
                        {/if}
                        <td class="fieldLabel {$WIDTHTYPE}" style="width:auto">
                            {if $FIELD_MODEL->isMandatory() eq true && $isReferenceField neq "reference"} <span class="redColor">*</span> {/if}
                            {vtranslate($FIELD_MODEL->get('label'), $MODULE)}
                        </td>
                        {if $FIELD_MODEL->get('uitype') neq "83"}
                            <td  class="fieldValue {$WIDTHTYPE}" {if $FIELD_MODEL->get('uitype') eq '19' or $FIELD_MODEL->get('uitype') eq '20'} colspan="3" {assign var=COUNTER value=$COUNTER+1} {/if} style="width:auto">
                                 <span class="value" data-field-type="{$FIELD_MODEL->getFieldDataType()}" {if $FIELD_MODEL->get('uitype') eq '19' or $FIELD_MODEL->get('uitype') eq '20' or $FIELD_MODEL->get('uitype') eq '21'} style="white-space:normal;" {/if}>
                                    {include file=vtemplate_path($FIELD_MODEL->getUITypeModel()->getDetailViewTemplateName(),'OrdersTaskAddresses') FIELD_MODEL=$FIELD_MODEL USER_MODEL=$USER_MODEL MODULE='OrdersTaskAddresses'}
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
    <br>
{/strip}