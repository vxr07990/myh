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
	<input type="hidden" id="site_domain" value="{$DOMAIN}" />
	{foreach key=BLOCK_LABEL_KEY item=FIELD_MODEL_LIST from=$RECORD_STRUCTURE}
	{if $MODULE_NAME eq "Quotes" and ($BLOCK_LABEL_KEY eq "LBL_QUOTES_VALUATIONDETAILS" or $BLOCK_LABEL_KEY eq "LBL_QUOTES_SITDETAILS" or $BLOCK_LABEL_KEY eq "LBL_QUOTES_ACCESSORIALDETAILS")}{continue}{/if}
	{assign var=BLOCK value=$BLOCK_LIST[$BLOCK_LABEL_KEY]}
	{if $BLOCK eq null or $FIELD_MODEL_LIST|@count lte 0}{continue}{/if}
	{assign var=IS_HIDDEN value=$BLOCK->isHidden()}
	{assign var=WIDTHTYPE value=$USER_MODEL->get('rowheight')}
	<div class="contentHeader row-fluid">
		{assign var=SINGLE_MODULE_NAME value='SINGLE_'|cat:$MODULE}
		<div class="btn-toolbar pull-right">{*"index.php?module=Leads&view=ConvertLead&record=854",this);*}
			{assign var=PARTICIPANT_INFO value=getParticipantInfoForRecord($RECORD->getId())}
			{if isPermitted('Estimates', 'EditView', $RECORD->getId()) == 'yes' && ((in_array('full', $PARTICIPANT_INFO['view_levels']) || in_array('read_only', $PARTICIPANT_INFO['view_levels'])) || !isParticipantForRecord($RECORD->getId()))}
				<span id="createEstimateBtnContainer" class="btn-group">
					<button type="button" class="btn" id="createEstimate" onClick="Javascript:Cubesheets_Detail_Js.convertCubesheet(this)"><strong>{vtranslate('LBL_CUBESHEETS_CREATEESTIMATE', $MODULE)}</strong></button>
				</span>
			{/if}
			{*if getenv('VIDEO_SURVEY_ARCHIVING') && $USER_MODEL->get('tokbox_permitted') eq 'on' && $SURVEY_TYPE eq 'Virtual'}
			<span class="btn-group">
				<button type="button" class="btn" id="viewArchiveButton">
					<strong>{vtranslate('LBL_VIEW_ARCHIVE', $MODULE)}</strong>
				</button>
			</span>
			{/if*}
			{if $USER_MODEL->getUserRoleDepth() != 8}
			<span class="btn-group">
				<button id="save_button" class="btn btn-success" type="submit">
					<strong>{vtranslate('LBL_SAVE', $MODULE)}</strong>
				</button>
			</span>
			{/if}
		</div>
	</div>
		<input type="hidden" name="reportSave" value="1" />
	<input type=hidden name="timeFormatOptions" data-value='{$DAY_STARTS}' />
	<input type=hidden name="sourceModule" value='{$SOURCE_MODULE}' />
	<table class="table table-bordered equalSplit detailview-table {if $BLOCK->get('hideblock') eq true}hide{/if}">
		<thead>
		<tr>
				<th class="blockHeader" colspan="4">
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
			{if !$FIELD_MODEL->isViewableInDetailView()}
				 {continue}
			 {/if}
			 {if $FIELD_MODEL->getName() eq 'effective_tariff'}
			 	<td class="fieldLabel {$WIDTHTYPE}"><label class="muted pull-right marginRight10px">{vtranslate({$FIELD_MODEL->get('label')},{$MODULE_NAME})}</label></td>
				<td class="fieldValue {$WIDTHTYPE}">{$TARIFFS[{$FIELD_MODEL->get('fieldvalue')}]['tariff_name']}</td>
				{assign var="COUNTER" value=$COUNTER+1}
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
				 {if $COUNTER eq 2}
					 </tr><tr>
					{assign var=COUNTER value=1}
				{else}
					{assign var=COUNTER value=$COUNTER+1}
				 {/if}
				 <td class="fieldLabel {$WIDTHTYPE}" id="{$MODULE_NAME}_detailView_fieldLabel_{$FIELD_MODEL->getName()}" {if $FIELD_MODEL->getName() eq 'description' or $FIELD_MODEL->get('uitype') eq '69'} style='width:8%'{/if}>
					 <label class="muted pull-right marginRight10px">
						 {vtranslate({$FIELD_MODEL->get('label')},{$MODULE_NAME})}
						 {if ($FIELD_MODEL->get('uitype') eq '72') && ($FIELD_MODEL->getName() eq 'unit_price')}
							({$BASE_CURRENCY_SYMBOL})
						{/if}
					 </label>
				 </td>
				 <td class="fieldValue {$WIDTHTYPE}" id="{$MODULE_NAME}_detailView_fieldValue_{$FIELD_MODEL->getName()}" {if $FIELD_MODEL->get('uitype') eq '19' or $FIELD_MODEL->get('uitype') eq '20'} colspan="3" {assign var=COUNTER value=$COUNTER+1} {/if}>
					 {if $FIELD_NAME eq 'opportunities_id'}
						<span class="value" data-field-type="{$FIELD_MODEL->getFieldDataType()}" {if $FIELD_MODEL->get('uitype') eq '19' or $FIELD_MODEL->get('uitype') eq '20' or $FIELD_MODEL->get('uitype') eq '21'} style="white-space:normal;" {/if}>
							{$POTENTIAL_LINK}
						 </span>
					{else}
						 <span class="value" data-field-type="{$FIELD_MODEL->getFieldDataType()}" {if $FIELD_MODEL->get('uitype') eq '19' or $FIELD_MODEL->get('uitype') eq '20' or $FIELD_MODEL->get('uitype') eq '21'} style="white-space:normal;" {/if}>
							{include file=vtemplate_path($FIELD_MODEL->getUITypeModel()->getDetailViewTemplateName(),$MODULE_NAME) FIELD_MODEL=$FIELD_MODEL USER_MODEL=$USER_MODEL MODULE=$MODULE_NAME RECORD=$RECORD}
						 </span>
					 {/if}
					 {if $IS_AJAX_ENABLED && $FIELD_MODEL->isEditable() eq 'true' && ($FIELD_MODEL->getFieldDataType()!=Vtiger_Field_Model::REFERENCE_TYPE) && $FIELD_MODEL->isAjaxEditable() eq 'true'}
						 <span class="hide edit">
							 {include file=vtemplate_path($FIELD_MODEL->getUITypeModel()->getTemplateName(),$MODULE_NAME) FIELD_MODEL=$FIELD_MODEL USER_MODEL=$USER_MODEL MODULE=$MODULE_NAME}
                             {if $FIELD_MODEL->getFieldDataType() eq 'multipicklist'}
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
		{/foreach}
		{* adding additional column for odd number of fields in a block *}
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
	{if not $BLOCK->get('hideblock')}<br>{/if}
	{/foreach}

	{*<div id="cubesheetPopupLink">
		<a href="#" onclick="var cubesheet = window.open('{getenv('CUBESHEET_URL')}?relatedRecordId={$RECORDID}&userId={$USER_MODEL->get('id')}&firstName={$FIRSTNAME}&lastName={$LASTNAME};', 'Cubesheet', 'width=640,height=480,scrollbars=yes'); jQuery('#save_button').prop('disabled', true); cubesheet.onbeforeunload = function(){ jQuery('#save_button').prop('disabled', false); var iframe = document.getElementById('iframeCubesheet'); iframe.src = iframe.src; }">Open Cubesheet</a>
	</div>*}

	<div id="iframeContent" style="position:relative; width:100%; height:60vh;">
		<iframe id="cubesheet"style="position:absolute; width:100%; height:100%;" src="{getenv('CUBESHEET_URL')}?relatedRecordId={$RECORDID}&userId={$USER_MODEL->get('id')}&firstName={$FIRSTNAME}&lastName={$LASTNAME}&pricingModeForce={$POTENTIAL_MOVETYPE}&languageCode={$POTENTIAL_LANGUAGE}"></iframe>
	</div>
{/strip}
