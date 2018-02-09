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

 <style type="text/css">
/*
     .checkboxes-label{
        width: 14%;
        float: left;
        padding-left: 0%;
        padding-bottom: 1%;
        padding-top: 1%;
        background: #f7f7f9;
        color: #999999;
        line-height: 18px;
        border-right: 1px solid #ddd;
        border-left: 1px solid #ddd;
        text-align: right;
        padding-right: 1%;
}

.first-check{
    border-left: 0px solid #ddd;
}

.checkboxes-values{
    width: 8%;
    float: left;
    padding-left: 1%;
    padding-bottom: 1%;
    padding-top: 1%;
}
*/
</style>
{strip}

<div class='container-fluid editViewContainer'>
	<form class="form-horizontal recordEditView" id="EditView" name="EditView" method="post" action="index.php" enctype="multipart/form-data">
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
				<a class="cancelLink" type="reset" onclick="javascript:window.history.back();">{vtranslate('LBL_CANCEL', $MODULE)}</a>
			</span>
		</div>

                <input type="hidden" id="hiddenFields" name="hiddenFields" value="{$HIDDEN_FIELDS}" />
		{assign var=EXTRA_BLOCKS_SETTINGS value=$RECORD_MODEL->getExtraBlockConfig()}

		{foreach key=BLOCK_LABEL item=BLOCK_FIELDS from=$RECORD_STRUCTURE name="EditViewBlockLevelLoop"}
			{if in_array($BLOCK_LABEL,array_keys($EXTRA_BLOCKS_SETTINGS))}
				{if $BLOCK_LABEL eq 'LBL_CPU_ACTUALS' || $BLOCK_LABEL eq 'LBL_EQUIPMENT_ACTUALS'}
					{include file=vtemplate_path('ExtraBlockDetail.tpl',$MODULE) BLOCK_SETTING = $EXTRA_BLOCKS_SETTINGS[$BLOCK_LABEL] FROMEDIT = TRUE}
				{else}
					{include file=vtemplate_path('ExtraBlockEdit.tpl',$MODULE) BLOCK_SETTING = $EXTRA_BLOCKS_SETTINGS[$BLOCK_LABEL]}
				{/if}
				{if $BLOCK_LABEL eq 'LBL_VEHICLES' && $IS_ACTIVE_ADDRESS}
					{include file=vtemplate_path('EditBlock.tpl','OrdersTaskAddresses')}
				{/if}
			{else}
			{if $BLOCK_FIELDS|@count lte 0}{continue}{/if}
			<table class="table table-bordered blockContainer showInlineTable equalSplit{if is_array($HIDDEN_BLOCKS)}{if in_array($BLOCK_LABEL, $HIDDEN_BLOCKS)} hide{/if}{/if}">
                <thead>
			<tr>
				<th class="blockHeader" colspan="4">{vtranslate($BLOCK_LABEL, $MODULE)}</th>
			</tr>
                </thead>
                <tbody>
			<tr>
			{assign var=COUNTER value=0}
			{foreach key=FIELD_NAME item=FIELD_MODEL from=$BLOCK_FIELDS name=blockfields}
                                {assign var=CHECKBOXES value=0}
                                {assign var=CHECKBOXES_FIRST value=0}

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

                                {*if $FIELD_MODEL->get('name') eq "date_spread"}
                                        {assign var=CHECKBOXES value=1}
                                        {assign var=CHECKBOXES_FIRST value=1}
                                        {assign var=COUNTER value=2}
                                {elseif $FIELD_MODEL->get('name') eq "include_saturday" || $FIELD_MODEL->get('name') eq "multiservice_date"}
                                        {assign var=CHECKBOXES value=1}
                                        {assign var=COUNTER value=0}
                                {elseif $FIELD_MODEL->get('name') eq "include_sunday"}
                                        {assign var=CHECKBOXES value=1}
                                        {assign var=COUNTER value=1}
                                        {assign var=CHECKBOXES_LAST value=1}
                                {/if*}




				{if $COUNTER eq 2}
                            </tr>
                            <tr>
					{assign var=COUNTER value=1}
				{else}
					{assign var=COUNTER value=$COUNTER+1}
				{/if}
                                {if $CHECKBOXES eq 1}
                                      {*if $CHECKBOXES_FIRST eq 1}
                                          <td colspan="4" style="padding:0px;">
                                      {/if*}

                                        <div class="checkboxes-label  {if $CHECKBOXES_FIRST eq 1} first-check {/if}">
                                            {if $FIELD_MODEL->isMandatory() eq true && $isReferenceField neq "reference"} <span class="redColor">*</span> {/if}
                                            {vtranslate($FIELD_MODEL->get('label'), $MODULE)}
                                        </div>
                                        <div class="checkboxes-values">
                                            <div class="row-fluid">
							<span class="span10">
								{include file=vtemplate_path($FIELD_MODEL->getUITypeModel()->getTemplateName(),$MODULE) BLOCK_FIELDS=$BLOCK_FIELDS}
							</span>
						</div>
                                        </div>
                                      {if $CHECKBOXES_LAST eq 1}
                                          </td>

                                        {assign var=COUNTER value=2}
                                      {/if}


                                {elseif $CHECKBOXES eq 0}
				<td class="fieldLabel {$WIDTHTYPE}">
					{if $isReferenceField neq "reference" || $FIELD_MODEL->get('name') eq 'participating_agent'}<label class="muted pull-right marginRight10px">{/if}
						{if $FIELD_MODEL->isMandatory() eq true} <span class="redColor">*</span> {/if}
						{if $isReferenceField eq "reference" && $FIELD_MODEL->get('name') neq 'participating_agent'}
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
						{elseif $FIELD_MODEL->get('uitype') eq "83"}
							{include file=vtemplate_path($FIELD_MODEL->getUITypeModel()->getTemplateName(),$MODULE) COUNTER=$COUNTER MODULE=$MODULE}
						{else}
							{vtranslate($FIELD_MODEL->get('label'), $MODULE)}
						{/if}
					{if $isReferenceField neq "reference"}</label>{/if}
				</td>
                                {if $FIELD_MODEL->get('name') eq "participating_agent"}
                                    {assign var="FIELD_INFO_RAW" value=$FIELD_MODEL->getFieldInfo()}
                                    {assign var="FIELD_INFO" value=Zend_Json::encode($FIELD_INFO_RAW)}

                                    {assign var=PICKLIST_VALUES value=ParticipatingAgents_Module_Model::getParticipantAgentsPicklistValues($ORDERS_ID)}
                                    {assign var=FIELD_VALUE value=$FIELD_MODEL->get('fieldvalue')}
                                    {if !array_key_exists($FIELD_VALUE, $PICKLIST_VALUES)}
                                            {*assign var=SAVED_OWNER_RECORD value=Users_Record_Model::getInstanceById($FIELD_VALUE,'Users')*}
                                            {$PICKLIST_VALUES[$FIELD_VALUE] = Users_Record_Model::getDisplaynameById($FIELD_VALUE)}
                                    {/if}

                                    {assign var="SPECIAL_VALIDATOR" value=$FIELD_MODEL->getValidator()}
                                    <td class="fieldValue {$WIDTHTYPE}" >
						<div class="row-fluid">
							<span class="span10">
                                                            <select {if $DEFAULT_CHZN eq 1}id="{$FIELD_MODEL->getFieldName()}" {/if}class="{if $DEFAULT_CHZN eq 0}chzn-select {/if}{if $OCCUPY_COMPLETE_WIDTH} row-fluid {/if}" name="{$FIELD_MODEL->getFieldName()}" data-validation-engine="validate[{if $FIELD_MODEL->isMandatory() eq true} required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" data-fieldinfo='{$FIELD_INFO|escape}' {if !empty($SPECIAL_VALIDATOR)}data-validator='{Zend_Json::encode($SPECIAL_VALIDATOR)}'{/if} data-selected-value='{$FIELD_MODEL->get('fieldvalue')}' {if $FIELD_MODEL->get('disabled')}disabled{/if}>
                                                                {if $FIELD_MODEL->isEmptyPicklistOptionAllowed()}<option value="">{vtranslate('LBL_SELECT_OPTION','Vtiger')}</option>{/if}
                                                                {foreach item=PICKLIST_VALUE key=PICKLIST_NAME from=$PICKLIST_VALUES}
                                                                        <option value="{$PICKLIST_VALUE.value}" {if $FIELD_MODEL->get('fieldvalue') eq $PICKLIST_VALUE.value} selected {/if}>{$PICKLIST_VALUE.label}</option>
                                                                {/foreach}
                                                            </select>
                                                    </span>
                                            </div>
                                    </td>
				{elseif $FIELD_MODEL->get('uitype') eq "14"}
                                        {assign var="FIELD_INFO" value=Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($FIELD_MODEL->getFieldInfo()))}
                                        {assign var="SPECIAL_VALIDATOR" value=$FIELD_MODEL->getValidator()}
                                        {assign var=FIELD_VALUE value=$FIELD_MODEL->getEditViewDisplayValue($FIELD_MODEL->get('fieldvalue'))}
                                        {assign var="TIME_FORMAT" value=$USER_MODEL->get('hour_format')}
                                        <td class="fieldValue {$WIDTHTYPE}" >
						<div class="row-fluid">
							<span class="span10">
                                                            <div class="input-append time">
                                                                <input id="{$MODULE}_editView_fieldName_{$FIELD_MODEL->get('name')}" type="text" data-format="{$TIME_FORMAT}" class="custom-tp timepicker-default input-small" value="{$FIELD_VALUE}" name="{$FIELD_MODEL->getFieldName()}"
                                                                    data-validation-engine="validate[{if $FIELD_MODEL->isMandatory() eq true} required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]"   {if !empty($SPECIAL_VALIDATOR)}data-validator='{Zend_Json::encode($SPECIAL_VALIDATOR)}'{/if} data-fieldinfo='{$FIELD_INFO}' />
                                                                <span class="add-on cursorPointer">
                                                                    <i class="icon-time"></i>
                                                                </span>
                                                            </div>
                                                        </span>
						</div>
					</td>
				{elseif $FIELD_MODEL->get('uitype') neq "83" && $FIELD_MODEL->get('uitype') neq "14"}
					<td class="fieldValue {$WIDTHTYPE}" {if $FIELD_MODEL->get('uitype') eq '19' or $FIELD_MODEL->get('uitype') eq '20'} colspan="3" {assign var=COUNTER value=$COUNTER+1} {/if}>
						<div class="row-fluid">
							<span class="span10">
								{include file=vtemplate_path($FIELD_MODEL->getUITypeModel()->getTemplateName(),$MODULE) BLOCK_FIELDS=$BLOCK_FIELDS}
							</span>
						</div>
					</td>
				{/if}
				{if $BLOCK_FIELDS|@count eq 1 and $FIELD_MODEL->get('uitype') neq "19" and $FIELD_MODEL->get('uitype') neq "20" and $FIELD_MODEL->get('uitype') neq "30" and $FIELD_MODEL->get('name') neq "recurringtype"}
					<td class="{$WIDTHTYPE}"></td><td class="{$WIDTHTYPE}"></td>
				{/if}

				{if $MODULE eq 'Events' && $BLOCK_LABEL eq 'LBL_EVENT_INFORMATION' && $smarty.foreach.blockfields.last }
					{include file=vtemplate_path('uitypes/FollowUp.tpl',$MODULE) COUNTER=$COUNTER}
				{/if}

                                {/if}
			{/foreach}
			{* adding additional column for odd number of fields in a block *}
			{if $BLOCK_FIELDS|@end eq true and $BLOCK_FIELDS|@count neq 1 and $COUNTER eq 1}
				<td class="fieldLabel {$WIDTHTYPE}"></td><td class="{$WIDTHTYPE}"></td>
			{/if}
				</tr>
			</tbody>
		</table>
		<br>
			{/if}
		{/foreach}
{/strip}
