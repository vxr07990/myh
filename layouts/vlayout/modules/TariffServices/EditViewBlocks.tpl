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


<div class='container-fluid editViewContainer'>
	<form novalidate class="form-horizontal recordEditView" id="EditView" name="EditView" method="post" action="index.php" enctype="multipart/form-data">
		{assign var=WIDTHTYPE value=$USER_MODEL->get('rowheight')}
		{if !empty($PICKIST_DEPENDENCY_DATASOURCE)}
			<input type="hidden" name="picklistDependency" value='{Vtiger_Util_Helper::toSafeHTML($PICKIST_DEPENDENCY_DATASOURCE)}' />
		{/if}
		{assign var=QUALIFIED_MODULE_NAME value={$MODULE}}
		{assign var=IS_PARENT_EXISTS value=strpos($MODULE,":")}
		{if $IS_PARENT_EXISTS}
			{assign var=SPLITTED_MODULE value=":"|explode:$MODULE}
			<input type="hidden" name="module" value="{$SPLITTED_MODULE[1]}" />
			<input type="hidden" name="parent" value="{$SPLITTED_MODULE[0]}" />
		{else}
			<input type="hidden" name="module" value="{$MODULE}" />
		{/if}
		<input type="hidden" name="action" value="Save" />
		<input type="hidden" name="record" value="{$RECORD_ID}" />
		<input type="hidden" name="defaultCallDuration" value="{$USER_MODEL->get('callduration')}" />
		<input type="hidden" name="defaultOtherEventDuration" value="{$USER_MODEL->get('othereventduration')}" />
		{if $IS_RELATION_OPERATION }
			<input type="hidden" name="sourceModule" value="{$SOURCE_MODULE}" />
			<input type="hidden" name="sourceRecord" value="{$SOURCE_RECORD}" />
			<input type="hidden" name="relationOperation" value="{$IS_RELATION_OPERATION}" />
		{/if}
		<div class="contentHeader row-fluid">
		{assign var=SINGLE_MODULE_NAME value='SINGLE_'|cat:$MODULE}
		{if $RECORD_ID neq ''}
			<h3 class="span8 textOverflowEllipsis" title="{vtranslate('LBL_EDITING', $MODULE)} {vtranslate($SINGLE_MODULE_NAME, $MODULE)} {$RECORD_STRUCTURE_MODEL->getRecordName()}">{vtranslate('LBL_EDITING', $MODULE)} {vtranslate($SINGLE_MODULE_NAME, $MODULE)} - {$RECORD_STRUCTURE_MODEL->getRecordName()}</h3>
		{else}
			<h3 class="span8 textOverflowEllipsis">{vtranslate('LBL_CREATING_NEW', $MODULE)} {vtranslate($SINGLE_MODULE_NAME, $MODULE)}</h3>
		{/if}
			<span class="pull-right">
				<button class="btn btn-success" type="submit"><strong>{vtranslate('LBL_SAVE', $MODULE)}</strong></button>
				<a class="cancelLink" type="reset" onclick="window.history.back();">{vtranslate('LBL_CANCEL', $MODULE)}</a>
			</span>
		</div>

                <input type="hidden" id="hiddenFields" name="hiddenFields" value="{$HIDDEN_FIELDS}" />

		{foreach key=BLOCK_LABEL item=BLOCK_FIELDS from=$RECORD_STRUCTURE name="EditViewBlockLevelLoop"}
			{assign var='HIDE_BLOCK' value=0}
			{if $BLOCK_FIELDS|@count lte 0 and $BLOCK_LABEL neq 'LBL_TARIFFSERVICES_BASEPLUS' and $BLOCK_LABEL neq 'LBL_TARIFFSERVICES_BREAKPOINT' and $BLOCK_LABEL neq 'LBL_TARIFFSERVICES_WEIGHTMILEAGE' and $BLOCK_LABEL neq 'LBL_TARIFFSERVICES_CHARGEPERHUNDRED' and $BLOCK_LABEL neq 'LBL_TARIFFSERVICES_COUNTYCHARGE'  and $BLOCK_LABEL neq 'LBL_TARIFFSERVICES_CWTBYWEIGHT'  and $BLOCK_LABEL neq 'LBL_TARIFFSERVICES_CWTPERQTY' and $BLOCK_LABEL neq 'LBL_TARIFFSERVICES_FLATRATEBYWEIGHT' and $BLOCK_LABEL neq 'LBL_TARIFFSERVICES_BULKY'}{continue}{/if}
			<table name='{$BLOCK_LABEL}' class="table table-bordered blockContainer showInlineTable equalSplit{if is_array($HIDDEN_BLOCKS)}{if in_array($BLOCK_LABEL, $HIDDEN_BLOCKS)}{assign var='HIDE_BLOCK' value=1} hide{/if}{/if}">
                <thead>
			<tr>
				<th class="blockHeader" colspan="{if $BLOCK_LABEL eq 'LBL_TARIFFSERVICES_BASEPLUS' or $BLOCK_LABEL eq 'LBL_TARIFFSERVICES_BREAKPOINT'}7{else if $BLOCK_LABEL eq 'LBL_TARIFFSERVICES_WEIGHTMILEAGE' or $BLOCK_LABEL eq 'LBL_TARIFFSERVICES_HOURLYSET'}6{else if $BLOCK_LABEL eq 'LBL_TARIFFSERVICES_BULKY' or $BLOCK_LABEL eq 'LBL_TARIFFSERVICES_CHARGEPERHUNDRED' or $BLOCK_LABEL eq 'LBL_TARIFFSERVICES_PACKING' or $BLOCK_LABEL eq 'LBL_TARIFFSERVICES_VALUATION'}5{else}4{/if}">{vtranslate($BLOCK_LABEL, $MODULE)}</th>
			</tr>
                </thead>
                <tbody>
			<tr>
			{assign var=COUNTER value=0}
			{foreach key=FIELD_NAME item=FIELD_MODEL from=$BLOCK_FIELDS name=blockfields}
				{if $FIELD_NAME eq 'assigned_user_id' && $PARENT_IS_ADMIN eq 'on'}<td class='hide'><input name='assigned_user_id' type='hidden' value='1'></td>{continue}{/if}
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
				<td class="fieldLabel {$WIDTHTYPE}" {if ($BLOCK_LABEL eq 'LBL_TARIFFSERVICES_HOURLYSET' or $BLOCK_LABEL eq 'LBL_TARIFFSERVICES_PACKING' or $BLOCK_LABEL eq 'LBL_TARIFFSERVICES_BULKY') and $COUNTER eq 1} colspan="2" {/if}>

                    {if $isReferenceField neq "reference"}<label class="muted pull-right marginRight10px">{/if}
						{if $FIELD_MODEL->isMandatory() eq true && $isReferenceField neq "reference"} <span class="redColor">*</span> {/if}
						{if $isReferenceField eq "reference"}
							{assign var="REFERENCE_LIST" value=$FIELD_MODEL->getReferenceList()}
							{assign var="REFERENCE_LIST_COUNT" value=count($REFERENCE_LIST)}
							{if $REFERENCE_LIST_COUNT > 1 && $FIELD_MODEL->getName() neq 'tariffservices_assigntorecord'}
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
						{else if $FIELD_MODEL->get('uitype') eq "83"}
							{include file=vtemplate_path($FIELD_MODEL->getUITypeModel()->getTemplateName(),$MODULE) COUNTER=$COUNTER MODULE=$MODULE}
						{else}
							{vtranslate($FIELD_MODEL->get('label'), $MODULE)}
						{/if}
					{if $isReferenceField neq "reference"}</label>{/if}
				</td>
				{if $FIELD_MODEL->get('uitype') neq "83"}
					<td class="fieldValue {$WIDTHTYPE}" {if $FIELD_MODEL->get('uitype') eq '19' or $FIELD_MODEL->get('uitype') eq '20' or $BLOCK_LABEL eq 'LBL_TARIFFSERVICES_BULKY' or $BLOCK_LABEL eq 'LBL_TARIFFSERVICES_PACKING'} colspan="3" {assign var=COUNTER value=$COUNTER+1} {else if $BLOCK_LABEL eq 'LBL_TARIFFSERVICES_HOURLYSET'} colspan="2" {else if $BLOCK_LABEL eq 'LBL_TARIFFSERVICES_VALUATION' and $COUNTER eq 1} colspan="2" {/if}>
						<div class="row-fluid">
							<span class="span10">
								{include file=vtemplate_path($FIELD_MODEL->getUITypeModel()->getTemplateName(),$MODULE) BLOCK_FIELDS=$BLOCK_FIELDS}
							</span>
						</div>
					</td>
				{/if}
				{if $BLOCK_FIELDS|@count eq 1 and $FIELD_MODEL->get('uitype') neq "19" and $FIELD_MODEL->get('uitype') neq "20" and $FIELD_MODEL->get('uitype') neq "30" and $FIELD_MODEL->get('name') neq "recurringtype" and $BLOCK_LABEL neq 'LBL_TARIFFSERVICES_BULKY' and $BLOCK_LABEL neq 'LBL_TARIFFSERVICES_PACKING'}
					<td class="{$WIDTHTYPE}"></td><td class="{$WIDTHTYPE}"></td>
				{/if}
				{if $MODULE eq 'Events' && $BLOCK_LABEL eq 'LBL_EVENT_INFORMATION' && $smarty.foreach.blockfields.last }
					{include file=vtemplate_path('uitypes/FollowUp.tpl',$MODULE) COUNTER=$COUNTER}
				{/if}
			{/foreach}
		{* adding additional column for odd number of fields in a block *}
		{if $BLOCK_FIELDS|@end eq true and $BLOCK_FIELDS|@count neq 1 and $COUNTER eq 1}
			<!-- extra cells -->
			<td class="fieldLabel {$WIDTHTYPE}"></td><td class="{$WIDTHTYPE}"></td>
		{/if}
			<!-- {$BLOCK_LABEL} -->
			</tr>
			{if $BLOCK_LABEL eq 'LBL_TARIFFSERVICES_BASEPLUS' or $BLOCK_LABEL eq 'LBL_TARIFFSERVICES_BREAKPOINT' or $BLOCK_LABEL eq 'LBL_TARIFFSERVICES_WEIGHTMILEAGE' or $BLOCK_LABEL eq 'LBL_TARIFFSERVICES_BULKY' or $BLOCK_LABEL eq 'LBL_TARIFFSERVICES_CHARGEPERHUNDRED' or $BLOCK_LABEL eq 'LBL_TARIFFSERVICES_COUNTYCHARGE' or $BLOCK_LABEL eq 'LBL_TARIFFSERVICES_HOURLYSET' or $BLOCK_LABEL eq 'LBL_TARIFFSERVICES_PACKING' or $BLOCK_LABEL eq 'LBL_TARIFFSERVICES_VALUATION' or $BLOCK_LABEL eq 'LBL_TARIFFSERVICES_CWTBYWEIGHT' or $BLOCK_LABEL eq 'LBL_TARIFFSERVICES_SERVICECHARGE' or $BLOCK_LABEL eq 'LBL_TARIFFSERVICES_FLATRATEBYWEIGHT'}
				{include file=vtemplate_path('ExtraTables.tpl', $MODULE) COUNTER=$COUNTER}
			{/if}
</tbody>
			</table>
			{if $HIDE_BLOCK neq 1 and $BLOCK_LABEL eq 'LBL_TARIFFSERVICES_INFORMATION'}<br>{/if}
		{/foreach}
		{include file=vtemplate_path('ValuationCBXContentEdit.tpl', $MODULE) COUNTER=$COUNTER}
		<br />
{/strip}
