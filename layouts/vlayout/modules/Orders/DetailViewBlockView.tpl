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
	{foreach key=BLOCK_LABEL_KEY item=FIELD_MODEL_LIST from=$RECORD_STRUCTURE}
	{if $BLOCK_LABEL_KEY == 'LBL_ORDERS_ORIGINADDRESS'}
		{if $IS_ACTIVE_ADDRESSLIST == true}
			{include file=vtemplate_path('AddressListDetail.tpl', 'AddressList')}
			{continue}
		{/if}
	{elseif $BLOCK_LABEL_KEY == "LBL_GSA_INFORMATION" && $BILLING_TYPE_FLAG eq false}
		{continue}
	{/if}
	{assign var=AGENT_NO_INVOICE value='false'}
	{*{if $AGENT_PERMISSIONS eq 'no_rates' && $BLOCK_LABEL_KEY eq 'LBL_ORDERS_INVOICE'}{$AGENT_NO_INVOICE = 'true'}{/if}*}
	{if $AGENT_NO_INVOICE neq 'true'}
		{if $MODULE_NAME eq "Quotes" and ($BLOCK_LABEL_KEY eq "LBL_QUOTES_VALUATIONDETAILS" or $BLOCK_LABEL_KEY eq "LBL_QUOTES_SITDETAILS" or $BLOCK_LABEL_KEY eq "LBL_QUOTES_ACCESSORIALDETAILS")}{continue}{/if}
		{assign var=BLOCK value=$BLOCK_LIST[$BLOCK_LABEL_KEY]}
		{if $BLOCK eq null or $FIELD_MODEL_LIST|@count lte 0}{continue}{/if}
		{assign var=IS_HIDDEN value=$BLOCK->isHidden()}
		{assign var=WIDTHTYPE value=$USER_MODEL->get('rowheight')}
		<input type=hidden name="timeFormatOptions" data-value='{$DAY_STARTS}' />
		<table class="table table-bordered {if $BLOCK_LABEL_KEY eq 'LBL_ORDERS_DATES' or $BLOCK_LABEL_KEY eq 'LBL_ORDERS_WEIGHTS'}{else}equalSplit{/if} detailview-table" name="{$BLOCK_LABEL_KEY}">
			<thead>
			<tr>
					<th class="blockHeader" colspan="{if $BLOCK_LABEL_KEY eq 'LBL_ORDERS_DATES' or $BLOCK_LABEL_KEY eq 'LBL_ORDERS_WEIGHTS'}6{else}4{/if}">
							<img class="cursorPointer alignMiddle blockToggle {if !($IS_HIDDEN)} hide {/if} "  src="{vimage_path('arrowRight.png')}" data-mode="hide" data-id={$BLOCK_LIST[$BLOCK_LABEL_KEY]->get('id')}>
							<img class="cursorPointer alignMiddle blockToggle {if ($IS_HIDDEN)} hide {/if}"  src="{vimage_path('arrowDown.png')}" data-mode="show" data-id={$BLOCK_LIST[$BLOCK_LABEL_KEY]->get('id')}>
							&nbsp;&nbsp;{vtranslate({$BLOCK_LABEL_KEY},{$MODULE_NAME})}
					</th>
			</tr>
			</thead>
			 <tbody {if $IS_HIDDEN} class="hide" {/if}>
			{assign var=COUNTER value=0}
			<tr>
			{foreach item=FIELD_MODEL key=FIELD_NAME from=$FIELD_MODEL_LIST}
				{if $FIELD_NAME eq 'origin_zone' || $FIELD_NAME eq 'empty_zone'}
					<input type="hidden" value="{$FIELD_MODEL->get('fieldvalue')}" name="{$FIELD_NAME}" id="{$FIELD_NAME}">
					{continue}
				{/if}
				{assign var=AGENT_NO_FIELD value='false'}
				{if $AGENT_PERMISSIONS eq 'no_rates' && ($FIELD_NAME eq 'orders_elinehaul' or $FIELD_NAME eq 'orders_etype' or $FIELD_NAME eq 'orders_discount' or $FIELD_NAME eq 'orders_etotal')}{$AGENT_NO_FIELD = 'true'}{/if}
				{if $AGENT_NO_FIELD neq 'true'}
					{if $FIELD_NAME eq 'oi_push_notification_token'}
						{if $IS_OI_ENABLED neq 1}
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
									{vtranslate({$FIELD_MODEL->get('label')},{$MODULE_NAME})}
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
									{vtranslate({$FIELD_MODEL->get('label')},{$MODULE_NAME})}
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
							<label class='muted pull-right marginRight10px'>{vtranslate($tax.taxlabel, $MODULE)}(%)</label>
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
						<td class="fieldLabel {$WIDTHTYPE}"><label class="muted pull-right marginRight10px">{vtranslate({$FIELD_MODEL->get('label')},{$MODULE_NAME})}</label></td>
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
						{* I start here *}
						{if  $BLOCK_LABEL_KEY eq 'LBL_ORDERS_DATES' or $BLOCK_LABEL_KEY eq 'LBL_ORDERS_WEIGHTS'}
							 {if $COUNTER eq '3'}
							 </tr>
									<tr>
							{assign var=COUNTER value=1}
							{else}
								{assign var=COUNTER value=$COUNTER+1}
							{/if}
						{else}
							{if $COUNTER eq 2}
										</tr>
										<tr>
								{assign var=COUNTER value=1}
							{else}
								{assign var=COUNTER value=$COUNTER+1}
							{/if}
						{/if}
						 {* I end here *}
						 <td class="fieldLabel {$WIDTHTYPE}"{if $BLOCK_LABEL_KEY eq 'LBL_ORDERS_DATES' or $BLOCK_LABEL_KEY eq 'LBL_ORDERS_WEIGHTS'}style='width:12.5%'{/if} id="{$MODULE_NAME}_detailView_fieldLabel_{$FIELD_MODEL->getName()}"{if $FIELD_MODEL->getName() eq 'description' or $FIELD_MODEL->get('uitype') eq '69'} style='width:8%'{/if}>
							 <label class="muted pull-right marginRight10px">
								 {vtranslate({$FIELD_MODEL->get('label')},{$MODULE_NAME})}
								 {if ($FIELD_MODEL->get('uitype') eq '72') && ($FIELD_MODEL->getName() eq 'unit_price')}
									({$BASE_CURRENCY_SYMBOL})
								{/if}
							 </label>
						 </td>
						 <td class="fieldValue {$WIDTHTYPE}"{if $BLOCK_LABEL_KEY eq 'LBL_ORDERS_DATES' or $BLOCK_LABEL_KEY eq 'LBL_ORDERS_WEIGHTS'}style='width:16.6%'{/if} id="{$MODULE_NAME}_detailView_fieldValue_{$FIELD_MODEL->getName()}" {if $FIELD_MODEL->get('uitype') eq '19' or $FIELD_MODEL->get('uitype') eq '20'} colspan="3" {assign var=COUNTER value=$COUNTER+1} {/if}>
							 <span class="value" data-field-type="{$FIELD_MODEL->getFieldDataType()}" {if $FIELD_MODEL->get('uitype') eq '19' or $FIELD_MODEL->get('uitype') eq '20' or $FIELD_MODEL->get('uitype') eq '21'} style="white-space:normal;" {/if}>
								 {if $FIELD_MODEL->get('name') == 'tariff_id'}
									 <input type="hidden" disabled id="allAvailableTariffs" value="{$AVAILABLE_TARIFFS}">
									 <input type="hidden" id="tariff_customjs" value="{$EFFECTIVE_TARIFF_CUSTOMJS}">
									 <div id="_fieldValue_effective_tariff_custom_type" class="hide">
									 	<span class="hide value">
										 	{$EFFECTIVE_TARIFF_CUSTOMTYPE}
								 		 	<input type="hidden" id="effective_tariff_custom_type" name="effective_tariff_custom_type" value="{$EFFECTIVE_TARIFF_CUSTOMTYPE}">
									 	</span>
									 </div>
								 {/if}
								{include file=vtemplate_path($FIELD_MODEL->getUITypeModel()->getDetailViewTemplateName(),$MODULE_NAME) FIELD_MODEL=$FIELD_MODEL USER_MODEL=$USER_MODEL MODULE=$MODULE_NAME RECORD=$RECORD}
							 </span>
							 {if $IS_AJAX_ENABLED && $FIELD_MODEL->isEditable() eq 'true' && ($FIELD_MODEL->getFieldDataType()!=Vtiger_Field_Model::REFERENCE_TYPE) && $FIELD_MODEL->isAjaxEditable() eq 'true'}{* && $CREATOR_PERMISSIONS eq 'true'*}
								 <span class="hide edit">
									 {include file=vtemplate_path($FIELD_MODEL->getUITypeModel()->getTemplateName(),$MODULE_NAME) FIELD_MODEL=$FIELD_MODEL USER_MODEL=$USER_MODEL MODULE=$MODULE_NAME}
									 {if $FIELD_MODEL->getFieldDataType() eq 'multipicklist' || $FIELD_MODEL->getFieldDataType() eq 'multiagent'}
										<input type="hidden" class="fieldname" value='{$FIELD_MODEL->get('name')}[]' data-prev-value='{$FIELD_MODEL->getDisplayValue($FIELD_MODEL->get('fieldvalue'))}' />
									 {else}
										 <input type="hidden" class="fieldname" value='{$FIELD_MODEL->get('name')}' data-prev-value='{Vtiger_Util_Helper::toSafeHTML($FIELD_MODEL->getDisplayValue($FIELD_MODEL->get('fieldvalue')))}' />
									 {/if}
								 </span>
							 {/if}
						 </td>
					 {/if}

				{if $FIELD_MODEL_LIST|@count eq 1 and $FIELD_MODEL->get('uitype') neq "19" and $FIELD_MODEL->get('uitype') neq "20" and $FIELD_MODEL->get('uitype') neq "30" and $FIELD_MODEL->get('name') neq "recurringtype" and $FIELD_MODEL->get('uitype') neq "69" and $FIELD_MODEL->get('uitype') neq "105"}
					<td class="fieldLabel {$WIDTHTYPE}"></td><td class="{$WIDTHTYPE}"></td>
				{/if}
			{/if}
			{/foreach}
			{/if}
			{* adding additional column for odd number of fields in a block *}
			{if $BLOCK_LABEL_KEY eq 'LBL_ORDERS_DATES' or $BLOCK_LABEL_KEY eq 'LBL_ORDERS_WEIGHTS'}
				{if $FIELD_MODEL_LIST|@end eq true and $FIELD_MODEL_LIST|@count neq 1 and $COUNTER <= 2}
					<td class="fieldLabel {$WIDTHTYPE}"></td><td class="{$WIDTHTYPE}"></td>
				{/if}
			{/if}
			{if $FIELD_MODEL_LIST|@end eq true and $FIELD_MODEL_LIST|@count neq 1 and $COUNTER eq 1}
				<td class="fieldLabel {$WIDTHTYPE}"></td><td class="{$WIDTHTYPE}"></td>
			{/if}
			</tr>
			{if $MODULE_NAME eq "Quotes" and $BLOCK_LABEL_KEY eq "LBL_QUOTES_INTERSTATEMOVEDETAILS"}
				<script type='text/javascript' src='libraries/jquery/colorbox/jquery.colorbox-min.js'></script>
				<tr><td class='fieldLabel'></td><td class='fieldValue'><button type='button' id='interstateRateQuick'>Quick Rate Estimate</button></td><td class='fieldLabel'></td><td class='fieldValue'><button type='button' id='interstateRateDetail'>Detailed Rate Estimate</button></td></tr>
			{/if}
			</tbody>
		</table>
		<br>
		{if $MODULE_NAME eq 'Orders' && $BLOCK_LABEL_KEY eq 'LBL_ORDERS_DATES'}
			{if getenv('INSTANCE_NAME') neq 'graebel'}
                {include file=vtemplate_path('participatingAgentsDetail.tpl', 'ParticipatingAgents')}
            {/if}
			{*{include file=vtemplate_path('extraStopsDetail.tpl', 'ExtraStops')}*}
			{include file=vtemplate_path('GuestDetailBlocks.tpl', $MODULE_NAME)}
			{* {include file=vtemplate_path('DetailBlock.tpl', 'MoveRoles') GUEST_MODULE='MoveRoles'}
			{include file=vtemplate_path('DetailBlock.tpl', 'OrdersMilestone') GUEST_MODULE='OrdersMilestone'} *}
		{/if}
		{include file=vtemplate_path('SequencedGuestDetailBlocks.tpl', $MODULE) BLOCK_LABEL=$BLOCK_LABEL_KEY}
	{/foreach}
	{if $MODULE_NAME eq "Documents"}
	<div>
		<iframe width="100%" height="600px" id="DocumentRenderer" src=''></iframe>
	</div>
	{/if}
	{if $MODULE_NAME eq "Quotes"}
		<!-- BEGIN RateEstimateDetail -->
		{include file='layouts/vlayout/modules/Quotes/RateEstimateDetail.tpl'}
	{/if}
    {*Cancellation Log Block*}
    {if ($CANCELATION_LOG_ARRAY|@count) gt 0}
    <table name="cancellationTable" class="table table-bordered blockContainer showInlineTable" style="margin-top:1%;">
        <thead>
            <tr>
                <th class="blockHeader" colspan="6">{vtranslate('LBL_CANCELLATION_BLOCK', 'Orders')}</th>
            </tr>
        </thead>
        <tbody>
        <tr class="fieldLabel">
            <td style="text-align:center;margin:auto;width:25%;">Action</td>
            <td style="text-align:center;margin:auto;width:25%;">Reason</td>
            <td style="text-align:center;margin:auto;width:25%;">User</td>
            <td style="text-align:center;margin:auto;width:25;">Date/Time</td>
        </tr>
        {foreach key=ROW_NUM item=CANCELATION_LOG from=$CANCELATION_LOG_ARRAY}
            <tr style="text-align:center;margin:auto">
                <input type="hidden" name="cancelid{$ROW_NUM}" value="{$CANCELATION_LOG['id']}">
                <td class="fieldValue typeCell" style="text-align:center;margin:auto">{$CANCELATION_LOG['action']}</td>
                <td class="fieldValue" style="text-align:center;margin:auto;">{$CANCELATION_LOG['reason']}</td>
                <td class="fieldValue" style="text-align:center;margin:auto">{$CANCELATION_LOG['user']}</td>
                <td class="fieldValue" style="text-align:center;margin:auto">{$CANCELATION_LOG['datetime']}</td>
            </tr>
        {/foreach}
        </tbody>
    </table>
    {/if}
{/strip}
