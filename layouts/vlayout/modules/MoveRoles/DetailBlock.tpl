{*<!--
/*********************************************************************************
** The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
*
 ********************************************************************************/
-->*}
{strip}

    {assign var=GUEST_MODULE_CAPS value=$GUEST_MODULE|upper}
    {assign var=GUEST_MODULE_LOWER value=$GUEST_MODULE|lower}
    {assign var=GUEST_LIST value=$GUEST_MODULE_CAPS|cat: '_LIST'}
    {assign var=FIELD_ARRAY value=$GUEST_MODULE_CAPS|cat: '_BLOCK_FIELDS'}
    {assign var=GUEST_BLOCK_LABEL value=$GUEST_MODULE_CAPS|cat: '_BLOCK_LABEL'}
    {assign var=ID_COLUMN value=$GUEST_MODULE_LOWER|cat: 'id'}
    {if getenv('INSTANCE_NAME') == 'graebel'}
        {assign var=MR_COLUMNS value=8}
        {assign var=LABEL_WIDTH value=10}
        {assign var=VALUE_WIDTH value=15}
    {else}
        {assign var=MR_COLUMNS value=4}
        {assign var=LABEL_WIDTH value=20}
        {assign var=VALUE_WIDTH value=30}
    {/if}

    {if ${$GUEST_MODULE_CAPS} && ${$GUEST_LIST}|@count gt 0}
        {assign var=WIDTHTYPE value=$USER_MODEL->get('rowheight')}
        <input type=hidden name="timeFormatOptions" data-value='{$DAY_STARTS}'/>
        <table class="table table-bordered detailview-table">
        <thead>
            <tr>
                <th class="blockHeader" colspan="{$MR_COLUMNS}">
                <img class="cursorPointer alignMiddle blockToggle {if !($IS_HIDDEN)} hide {/if} "src="{vimage_path('arrowRight.png')}" data-mode="hide" data-id='{$GUEST_MODULE}'>{* Block-ID workaround *}
                    <img class="cursorPointer alignMiddle blockToggle {if ($IS_HIDDEN)} hide {/if}"src="{vimage_path('arrowDown.png')}" data-mode="show" data-id='{$GUEST_MODULE}'>&nbsp;&nbsp;{vtranslate(${$GUEST_BLOCK_LABEL},$GUEST_MODULE)}
                </th>
            </tr>
        </thead>
            {foreach key=RECORD_INDEX item=CURRENT_RECORD from=${$GUEST_LIST}}
                {assign var=RECORD_COUNT value=$RECORD_INDEX+1}
                <tbody {if $IS_HIDDEN} class="hide" {/if}>
                {assign var=COUNTER value=0}
                    <tr class="fieldLabel" colspan="{$MR_COLUMNS}">
                    <td colspan="{$MR_COLUMNS}" class="blockHeader" style="background-color:#E8E8E8;">
                        <span class="{$GUEST_MODULE}Title">&nbsp;</span>
                    </td>
                </tr>
                <tr class="START_FIELD_foreach">
                {foreach item=FIELD_MODEL key=FIELD_NAME from=${$FIELD_ARRAY}}
                    {assign var=CUSTOM_FIELD_NAME value=$FIELD_NAME|cat:"_"|cat:$RECORD_COUNT}
                    {assign var=FIELD_MODEL value=$FIELD_MODEL->set('fieldvalue',${$GUEST_LIST}[$RECORD_INDEX][$FIELD_NAME])}
                    {assign var=FIELD_MODEL value=$FIELD_MODEL->set('name',$CUSTOM_FIELD_NAME)}
                    {assign var=FIELD_MODEL value=$FIELD_MODEL->set('noncustomname',$FIELD_NAME)}

                    {if !$FIELD_MODEL->isViewableInDetailView()}
                        {continue}
                    {/if}
                    {if $COUNTER eq 4}
                        </tr><tr>
                        {assign var=COUNTER value=1}
                        {else}
                        {assign var=COUNTER value=$COUNTER+1}

                    {/if}
                    <td class="fieldLabel {$WIDTHTYPE}" id="{${$GUEST_MODULE}}_detailView_fieldLabel_{$FIELD_MODEL->getName()}" style="width:{$LABEL_WIDTH}%">
                        <label class="muted pull-right marginRight10px">
                            {vtranslate($FIELD_MODEL->get('label'),$GUEST_MODULE)}
                            {if ($FIELD_MODEL->get('uitype') eq '72') && ($FIELD_MODEL->getName() eq 'unit_price')} ({$BASE_CURRENCY_SYMBOL}) {/if}
                        </label>
                    </td>
                    <td class="fieldValue {$WIDTHTYPE}" style="width:{$VALUE_WIDTH}%" id="{${$GUEST_MODULE}}_detailView_fieldValue_{$FIELD_MODEL->getName()}" {if $FIELD_MODEL->get('uitype') eq '19' or $FIELD_MODEL->get('uitype') eq '20'} {assign var=COUNTER value=$COUNTER+1} {/if}>
                        <span class="value" data-field-type="{$FIELD_MODEL->getFieldDataType()}" {if $FIELD_MODEL->get('uitype') eq '19' or $FIELD_MODEL->get('uitype') eq '20' or $FIELD_MODEL->get('uitype') eq '21'} style="white-space:normal;" {/if}>
                            {include file=vtemplate_path($FIELD_MODEL->getUITypeModel()->getDetailViewTemplateName(),${$GUEST_MODULE}) FIELD_MODEL=$FIELD_MODEL USER_MODEL=$USER_MODEL MODULE=${$GUEST_MODULE} RECORD=$RECORD}
                        </span>
                    </td>
                    {* if ${$FIELD_ARRAY}|@count eq 1 and $FIELD_MODEL->get('uitype') neq "19" and $FIELD_MODEL->get('uitype') neq "20" and $FIELD_MODEL->get('uitype') neq "30" and $FIELD_MODEL->get('name') neq "recurringtype" and $FIELD_MODEL->get('uitype') neq "69" and $FIELD_MODEL->get('uitype') neq "105"}
                        <td class="fieldLabel {$WIDTHTYPE}"></td><td class="{$WIDTHTYPE}"></td>
                    {/if *}
                {/foreach}
            </tr>
        </tbody>
            {/foreach}
        </table>
    {/if}
    <br>
{/strip}