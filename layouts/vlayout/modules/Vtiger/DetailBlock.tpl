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
    {if ${$GUEST_MODULE_CAPS} && ${$GUEST_LIST}|@count gt 0}
        {assign var=WIDTHTYPE value=$USER_MODEL->get('rowheight')}
        <input type=hidden name="timeFormatOptions" data-value='{$DAY_STARTS}'/>
        <table class="table table-bordered equalSplit detailview-table block_{$GUEST_BLOCK_LABEL}">
        <thead>
    <tr>
        <th class="blockHeader" colspan="4">
        <img class="cursorPointer alignMiddle blockToggle {if !($IS_HIDDEN)} hide {/if} "
             src="{vimage_path('arrowRight.png')}" data-mode="hide" data-id='{$GUEST_MODULE}'>{* Block-ID workaround *}
        <img class="cursorPointer alignMiddle blockToggle {if ($IS_HIDDEN)} hide {/if}"
             src="{vimage_path('arrowDown.png')}" data-mode="show" data-id='{$GUEST_MODULE}'>
        &nbsp;&nbsp;{vtranslate(${$GUEST_BLOCK_LABEL},$GUEST_MODULE)}
    </th>
    </tr>
    </thead>
        {foreach key=RECORD_INDEX item=CURRENT_RECORD from=${$GUEST_LIST}}
            {assign var=RECORD_COUNT value=$RECORD_INDEX+1}
            <tbody class="hide {$GUEST_MODULE}Block" guestid="{$RECORD_COUNT}">
                {assign var=COUNTER value=0}
                <tr class="fieldLabel" colspan="4">
                    <td colspan="4" class="blockHeader" style="background-color:#E8E8E8;">
                        <span class="{$GUEST_MODULE}Title">&nbsp;</span>
                    </td>
                </tr>
                <tr>
                {foreach item=FIELD_MODEL key=FIELD_NAME from=${$FIELD_ARRAY}}
                    {assign var=CUSTOM_FIELD_NAME value=$FIELD_NAME|cat:"_"|cat:$RECORD_COUNT}
                    {assign var=FIELD_MODEL value=$FIELD_MODEL->set('fieldvalue',${$GUEST_LIST}[$RECORD_INDEX][$FIELD_NAME])}
                    {assign var=FIELD_MODEL value=$FIELD_MODEL->set('name',$CUSTOM_FIELD_NAME)}
                    {assign var=FIELD_MODEL value=$FIELD_MODEL->set('noncustomname',$FIELD_NAME)}
                        {if $FIELD_NAME eq 'oi_push_notification_token'}
                            {if $IS_OI_ENABLED neq 1}
                            <!-- O&I DISABLED -->
                                {continue}
                            {/if}
                        {/if}
                        {if $FIELD_NAME eq 'dbx_token'}
                            {if $IS_OI_ENABLED neq 1}
                            <!-- O&I DISABLED -->
                                {continue}
                            {else}
                            <!-- O&I ENABLED -->
                            <!-- {$FIELD_MODEL->getDisplayValue($FIELD_MODEL->get('fieldvalue'))} -->
                                {if $FIELD_MODEL->getDisplayValue($FIELD_MODEL->get('fieldvalue')) eq ''}
                                <!-- No DBX Token set -->
                                    {if $COUNTER eq 2}
                                    </tr><tr>
                                        {assign var="COUNTER" value=1}
                                    {else}
                                        {assign var="COUNTER" value=$COUNTER+1}
                                    {/if}
                                <td class="fieldLabel {$WIDTHTYPE}">
                                            <label class='muted pull-right marginRight10px'>
                                                {vtranslate($FIELD_MODEL->get('label'),$GUEST_MODULE)}
                                            </label>
                                            </td>
                                            <td class="fieldValue {$WIDTHTYPE}">
                                            <span class="value" id="dropbox_auth_token">
                                                <button type="button" onclick="getDropboxAuth()">Get Dropbox Authorization Token</button>
                                            </span>
                                            </td>
                                        {else}
                                            <!-- DBX Token is set -->
                                            {if $COUNTER eq 2}
                                                </tr><tr>
                                                {assign var="COUNTER" value=1}
                                            {else}
                                                {assign var="COUNTER" value=$COUNTER+1}
                                            {/if}
                                            <td class="fieldLabel {$WIDTHTYPE}">
                                            <label class='muted pull-right marginRight10px'>
                                                {vtranslate($FIELD_MODEL->get('label'),$GUEST_MODULE)}
                                            </label>
                                            </td>
                                            <td class="fieldValue {$WIDTHTYPE}">
                                            <span class="value" id="dropbox_auth_token">
                                                [hidden]
                                            </span>
                                            </td>
                                        {/if}
                                        {continue}
                                    {/if}
                                {/if}
                                {if !$FIELD_MODEL->isViewableInDetailView()}
                                     {continue}
                                 {/if}
                                 {if $FIELD_MODEL->get('uitype') eq "83"}
                                    {foreach item=tax key=count from=$TAXCLASS_DETAILS}
                                    {if $tax.check_value eq 1}
                                        {if $COUNTER eq 2}
                                            </tr><tr>
                                            {assign var="COUNTER" value=1}
                                        {else}
                                            {assign var="COUNTER" value=$COUNTER+1}
                                        {/if}
                                        <td class="fieldLabel {$WIDTHTYPE}">
                                        <label class='muted pull-right marginRight10px'>{vtranslate($tax.taxlabel, $GUEST_MODULE)}(%)</label>
                                        </td>
                                         <td class="fieldValue {$WIDTHTYPE}">
                                             <span class="value">
                                                 {$tax.percentage}
                                             </span>
                                         </td>
                                    {/if}
                                    {/foreach}
                                {else if $FIELD_MODEL->get('uitype') eq "69" || $FIELD_MODEL->get('uitype') eq "105"}
                                    {if $COUNTER neq 0}
                                        {if $COUNTER eq 2}
                                            </tr><tr>
                                            {assign var=COUNTER value=0}
                                        {/if}
                                    {/if}
                                    <td class="fieldLabel {$WIDTHTYPE}"><label class="muted pull-right marginRight10px">{vtranslate($FIELD_MODEL->get('label'),$GUEST_MODULE)}</label></td>
                                    <td class="fieldValue {$WIDTHTYPE}">
                                        <div id="imageContainer" width="300" height="200">
                                            {foreach key=ITER item=IMAGE_INFO from=$IMAGE_DETAILS}
                                                {if !empty($IMAGE_INFO.path) && !empty({$IMAGE_INFO.orgname})}
                                                    <img src="{$IMAGE_INFO.path}_{$IMAGE_INFO.orgname}" width="300" height="200">
                                                {/if}
                                            {/foreach}
                                        </div>
                                    </td>
                                    {assign var=COUNTER value=$COUNTER+1}
                                {else}
                                    {if $FIELD_MODEL->get('uitype') eq "20" or $FIELD_MODEL->get('uitype') eq "19"}
                                        {if $COUNTER eq '1'}
                                            <td class="{$WIDTHTYPE}"></td><td class="{$WIDTHTYPE}"></td></tr><tr>
                                            {assign var=COUNTER value=0}
                                        {/if}
                                    {/if}
                                     {if $COUNTER eq 2}
                                         </tr><tr>
                                        {assign var=COUNTER value=1}
                                    {else}
                                        {assign var=COUNTER value=$COUNTER+1}
                                     {/if}
                                     <td class="fieldLabel {$WIDTHTYPE}" id="{${$GUEST_MODULE}}_detailView_fieldLabel_{$FIELD_MODEL->get('name')}" {if $FIELD_MODEL->getName() eq 'description' or $FIELD_MODEL->get('uitype') eq '69'} style='width:8%'{/if}>
                                         <label class="muted pull-right marginRight10px">
                                             {vtranslate($FIELD_MODEL->get('label'),$GUEST_MODULE)}
                                             {if ($FIELD_MODEL->get('uitype') eq '72') && ($FIELD_MODEL->getName() eq 'unit_price')}
                                                ({$BASE_CURRENCY_SYMBOL})
                                            {/if}
                                         </label>
                                     </td>
                                     <td class="fieldValue {$WIDTHTYPE}" id="{${$GUEST_MODULE}}_detailView_fieldValue_{$FIELD_MODEL->get('name')}" {if $FIELD_MODEL->get('uitype') eq '19' or $FIELD_MODEL->get('uitype') eq '20'} colspan="3" {assign var=COUNTER value=$COUNTER+1} {/if}>
                                         <span class="value" data-field-type="{$FIELD_MODEL->getFieldDataType()}" {if $FIELD_MODEL->get('uitype') eq '19' or $FIELD_MODEL->get('uitype') eq '20' or $FIELD_MODEL->get('uitype') eq '21'} style="white-space:normal;" {/if}>
                                            {include file=vtemplate_path($FIELD_MODEL->getUITypeModel()->getDetailViewTemplateName(),${$GUEST_MODULE}) FIELD_MODEL=$FIELD_MODEL USER_MODEL=$USER_MODEL MODULE=${$GUEST_MODULE} RECORD=$RECORD}
                                         </span>
                                     </td>
                                 {/if}

                                {if ${$FIELD_ARRAY}|@count eq 1 and $FIELD_MODEL->get('uitype') neq "19" and $FIELD_MODEL->get('uitype') neq "20" and $FIELD_MODEL->get('uitype') neq "30" and $FIELD_MODEL->get('name') neq "recurringtype" and $FIELD_MODEL->get('uitype') neq "69" and $FIELD_MODEL->get('uitype') neq "105"}
                                    <td class="fieldLabel {$WIDTHTYPE}"></td><td class="{$WIDTHTYPE}"></td>
                                {/if}
                                {if isset($SINGLE_FIELDS) && in_array($FIELD_NAME, $SINGLE_FIELDS)}
                                    <td class="fieldLabel {$WIDTHTYPE}"></td><td class="fieldValue {$WIDTHTYPE}"></td>
                                        </tr>
                                            <tr>
                                    {assign var=COUNTER value=$COUNTER+1}
                                {/if}
                            {/foreach}
                            {* adding additional column for odd number of fields in a block *}
                            {if ${$FIELD_ARRAY}|@end eq true and ${$FIELD_ARRAY}|@count neq 1 and $COUNTER eq 1}
                                <td class="fieldLabel {$WIDTHTYPE}"></td><td class="{$WIDTHTYPE}"></td>
                            {/if}
                        </tr>
                    </tbody>
			{/foreach}
		</table>
        <br>
	{/if}
{/strip}
