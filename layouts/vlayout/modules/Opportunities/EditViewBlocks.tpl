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
	<form class="form-horizontal recordEditView" id="EditView" name="EditView" method="post" action="index.php" enctype="multipart/form-data">
        {if $ISDUPLICATE}
        <input type="hidden" name="isDuplicate" value="1" />
        <input type="hidden" name="oldRecord" value="{$OLD_RECORD_ID}" />
        {/if}
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
		<input type="hidden" name="instance" value="{$INSTANCE_NAME}" />
		{if $HAULING_AGENT_ID}
			<input type="hidden" name="hauling_agent_id" value="{$HAULING_AGENT_ID}">
			<input type="hidden" name="hauling_agent_name" value="{$HAULING_AGENT_NAME}">
		{/if}
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


		{foreach key=BLOCK_LABEL item=BLOCK_FIELDS from=$RECORD_STRUCTURE name="EditViewBlockLevelLoop"}
			{if $BLOCK_LABEL == 'LBL_POTENTIALS_ADDRESSDETAILS'}
				{if $IS_ACTIVE_ADDRESSLIST == true}
					{include file=vtemplate_path('AddressListEdit.tpl', 'AddressList')}
					{continue}
				{/if}
			{/if}
			{if $BLOCK_FIELDS|@count lte 0}{continue}{/if}
			{if $BLOCK_LABEL eq 'LBL_POTENTIALS_PARTICIPANTS' AND getenv('INSTANCE_NAME') eq 'mccollisters'}{continue}{/if}
			{if $BLOCK_LABEL eq 'LBL_OPPORTUNITIES_REFERRAL'}{continue}{/if}

			{*if $BLOCK_LABEL eq 'LBL_OPPORTUNITY_REGISTERSTS'*}{*continue*}{*/if*}

			<table name="{if $BLOCK_LABEL neq 'LBL_POTENTIALS_PARTICIPANTS'}{$BLOCK_LABEL}{else}participatingAgentsTable{/if}" class="table table-bordered blockContainer showInlineTable equalSplit{if is_array($HIDDEN_BLOCKS)}{if in_array($BLOCK_LABEL, $HIDDEN_BLOCKS)} hide{/if}{/if}" style='table-layout:fixed'>
                <thead>
			<tr>
                {if $USE_STATUS eq true}
                    {assign var=PARTICIPATING_WIDTH value=40}
                {else}
                    {assign var=PARTICIPATING_WIDTH value=33}
                {/if}
				<th class="blockHeader" colspan="{if $BLOCK_LABEL eq 'LBL_POTENTIALS_DATES'}6{elseif $BLOCK_LABEL eq 'LBL_POTENTIALS_PARTICIPANTS'}{$PARTICIPATING_WIDTH}{else}4{/if}">
					{if $BLOCK_LABEL == 'LBL_OPPORTUNITY_EMPLOYERASSISTING'
						|| $BLOCK_LABEL == 'LBL_OPPORTUNITY_REGISTERSTS'}
						<img class="cursorPointer alignMiddle blockToggle "  src="{vimage_path('arrowRight.png')}" data-mode="hide" data-id="{$BLOCK_LABEL}">
						<img class="cursorPointer alignMiddle blockToggle hide"  src="{vimage_path('arrowDown.png')}" data-mode="show" data-id="{$BLOCK_LABEL}">
					{/if}
					{vtranslate($BLOCK_LABEL, $MODULE)}
				</th>
			</tr>
                </thead>
                <tbody 					{if ($BLOCK_LABEL == 'LBL_OPPORTUNITY_EMPLOYERASSISTING'
										|| $BLOCK_LABEL == 'LBL_OPPORTUNITY_REGISTERSTS')} class="hide"
				{/if}>
			<tr>
			{assign var=COUNTER value=0}
			{foreach key=FIELD_NAME item=FIELD_MODEL from=$BLOCK_FIELDS name=blockfields}
				{if $FIELD_NAME eq 'converted_from' || $FIELD_NAME eq 'out_of_area' || $FIELD_NAME eq 'out_of_origin' || $FIELD_NAME eq 'small_move' || $FIELD_NAME eq 'phone_estimate' || $FIELD_NAME eq 'origin_phone1_ext' || $FIELD_NAME eq 'origin_phone2_ext' || $FIELD_NAME eq 'destination_phone1_ext' || $FIELD_NAME eq 'destination_phone2_ext' || ($FIELD_NAME eq 'business_line' && 'move_type'|array_key_exists:$BLOCK_FIELDS)}
					{continue}
				{/if}
				{if $FIELD_NAME eq 'self_haul_opp'}
					{assign var="HIDE_SELF_HAUL" value=1}
					{if ($FIELD_MODEL->get('fieldvalue') eq 1) || $USER_MODEL->isAgencyAdmin()}
						{assign var="HIDE_SELF_HAUL" value=0}
					{/if}
				{else}
					{assign var="HIDE_SELF_HAUL" value=0}
				{/if}

				{assign var="isReferenceField" value=$FIELD_MODEL->getFieldDataType()}
				{if $FIELD_MODEL->get('uitype') eq "20" or $FIELD_MODEL->get('uitype') eq "19" or $FIELD_NAME == 'sales_person'}
					{if $COUNTER eq '1'}
									<td class="{$WIDTHTYPE} fieldLabel"></td><td class="{$WIDTHTYPE} fieldValue"></td>
								</tr>
								<tr>
						{assign var=COUNTER value=0}
					{/if}
				{/if}
					{* I start here *}
				{if  $BLOCK_LABEL eq 'LBL_POTENTIALS_DATES'}
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
								<tr{if $FIELD_NAME eq 'lock_military_fields' || $HIDE_SELF_HAUL} class="hide"{/if}>
						{assign var=COUNTER value=1}
					{else}
						{assign var=COUNTER value=$COUNTER+1}
					{/if}
				{/if}
				{* I end here *}

				<td class="fieldLabel {$WIDTHTYPE} {if $HIDE_SELF_HAUL OR $FIELD_MODEL->getName() eq 'leadsource_national' OR $FIELD_MODEL->getName() eq 'leadsource_workspace'}hide{/if}" {if $BLOCK_LABEL eq 'LBL_POTENTIALS_DATES'}style='width:14%'{/if}>
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
						{elseif (getenv('INSTANCE_NAME') eq 'sirva') && ($FIELD_NAME eq 'closingdate')}
							{*OT13398 hide the Fulfillment date for sirva*}
							<span class="hide"></span>
						{elseif $FIELD_MODEL->get('uitype') eq "83"}
							{include file=vtemplate_path($FIELD_MODEL->getUITypeModel()->getTemplateName(),$MODULE) COUNTER=$COUNTER MODULE=$MODULE}
						{else}
							{if $FIELD_NAME eq 'lock_military_fields' || $HIDE_SELF_HAUL} <span class="hide">{/if}
							{vtranslate($FIELD_MODEL->get('label'), $MODULE)}
							{if $FIELD_NAME eq 'lock_military_fields' || $HIDE_SELF_HAUL} </span>{/if}
						{/if}
					{if $isReferenceField neq "reference"}</label>{/if}
				</td>

					{if $FIELD_MODEL->get('uitype') neq "83"}
						<td class="fieldValue {$WIDTHTYPE} {if $HIDE_SELF_HAUL OR $FIELD_MODEL->getName() eq 'leadsource_national' OR $FIELD_MODEL->getName() eq 'leadsource_workspace'}hide{/if}" {if $BLOCK_LABEL eq 'LBL_POTENTIALS_DATES'}style='width:19%'{/if} {if $FIELD_MODEL->get('uitype') eq '19' or $FIELD_MODEL->get('uitype') eq '20'} colspan="3" {assign var=COUNTER value=$COUNTER+1} {/if}>
							<div class="row-fluid">
								<span class="span12 wwww {if $FIELD_NAME eq 'lock_military_fields' || $HIDE_SELF_HAUL} hide"{/if}">
									{if $FIELD_NAME eq 'lock_military_fields'}
										{assign var="FIELD_INFO" value=Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($FIELD_MODEL->getFieldInfo()))}
										{assign var="SPECIAL_VALIDATOR" value=$FIELD_MODEL->getValidator()}
										{assign var="FIELD_NAME" value=$FIELD_MODEL->get('name')}

										<input class='hide' type="hidden" name="{$FIELD_MODEL->getFieldName()}" value=0 />
										<input class='hide' id="{$MODULE}_editView_fieldName_{$FIELD_NAME}" type="checkbox" name="{$FIELD_MODEL->getFieldName()}" data-validation-engine="validate[funcCall[Vtiger_Base_Validator_Js.invokeValidation]]"
										{if $FIELD_MODEL->get('fieldvalue') eq true} checked
										{/if} data-fieldinfo='{$FIELD_INFO}' {if !empty($SPECIAL_VALIDATOR)}data-validator={Zend_Json::encode($SPECIAL_VALIDATOR)}{/if} />
									{*
									{elseif (getenv('INSTANCE_NAME') eq 'sirva') && $HIDE_SELF_HAUL}
									*}
									{elseif $FIELD_NAME eq 'register_sts_number' && getenv('INSTANCE_NAME') eq 'sirva'}
                                        <input id="{$MODULE}_editView_fieldName_{$FIELD_NAME}" type="text" class="input-large" name="{$FIELD_MODEL->getFieldName()}" value="{$FIELD_MODEL->get('fieldvalue')}" {if $RECORD_MODEL->isRegisteredSTS()}readonly {/if} data-fieldinfo='{$FIELD_INFO}' />
									{elseif $HIDE_SELF_HAUL}
										{include file=vtemplate_path($FIELD_MODEL->getUITypeModel()->getDetailViewTemplateName(),$MODULE_NAME) FIELD_MODEL=$FIELD_MODEL USER_MODEL=$USER_MODEL MODULE=$MODULE_NAME RECORD=$RECORD_MODEL RECORD_ID=$RECORD_ID}
									{elseif (getenv('INSTANCE_NAME') eq 'sirva') && ($FIELD_NAME eq 'closingdate')}
										{*OT13398 hide the Fulfillment date for sirva*}
										<span class="hide"></span>
									{elseif $FIELD_NAME neq 'days_to_move'}
										{include file=vtemplate_path($FIELD_MODEL->getUITypeModel()->getTemplateName(),$MODULE) BLOCK_FIELDS=$BLOCK_FIELDS}
									{else}
										{assign var="FIELD_INFO" value=Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($FIELD_MODEL->getFieldInfo()))}
										{assign var="SPECIAL_VALIDATOR" value=$FIELD_MODEL->getValidator()}
										{assign var="FIELD_NAME" value=$FIELD_MODEL->get('name')}
										<input id="{$MODULE}_editView_fieldName_{$FIELD_NAME}" type="text" class="input-large {if $FIELD_MODEL->isNameField()}nameField{/if}" data-validation-engine="validate[{if $FIELD_MODEL->isMandatory() eq true}required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" name="{$FIELD_MODEL->getFieldName()}" value="{$FIELD_MODEL->get('fieldvalue')}" readonly data-fieldinfo='{$FIELD_INFO}' {if !empty($SPECIAL_VALIDATOR)}data-validator={Zend_Json::encode($SPECIAL_VALIDATOR)}{/if} />
									{/if}
									{if $FIELD_NAME eq 'origin_phone1' && 'origin_phone1_ext'|array_key_exists:$BLOCK_FIELDS}
										<span id='originPhone1Span' {if $BLOCK_FIELDS['origin_phone1_type']->get('fieldvalue') neq 'Work'}class='hide'{/if}>
										&nbsp; Ext:&nbsp;
										{assign var="FIELD_INFO" value=Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($BLOCK_FIELDS['origin_phone1_ext']->getFieldInfo()))}
										{assign var="SPECIAL_VALIDATOR" value=$BLOCK_FIELDS['origin_phone1_ext']->getValidator()}
										{assign var="FIELD_LABEL" value=$BLOCK_FIELDS['origin_phone1_ext']->get('name')}
										<input id="{$MODULE}_editView_fieldName_{$FIELD_LABEL}" type="text" class="input-large {if $BLOCK_FIELDS['origin_phone1_ext']->isNameField()}nameField{/if}" data-validation-engine="validate[{if $BLOCK_FIELDS['origin_phone1_ext']->isMandatory() eq true}required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" name="{$BLOCK_FIELDS['origin_phone1_ext']->getFieldName()}" value="{$BLOCK_FIELDS['origin_phone1_ext']->get('fieldvalue')}"
										{if $BLOCK_FIELDS['origin_phone1_ext']->get('uitype') eq '3' || $BLOCK_FIELDS['origin_phone1_ext']->get('uitype') eq '4'|| $BLOCK_FIELDS['origin_phone1_ext']->isReadOnly()} readonly {/if} data-fieldinfo='{$FIELD_INFO}' {if !empty($SPECIAL_VALIDATOR)}data-validator={Zend_Json::encode($SPECIAL_VALIDATOR)}{/if} />
										<style>
											[name=origin_phone1_ext]{
												width: 50px;
											}
										</style>
										&nbsp;
										</span>
									{/if}
									{if $FIELD_NAME eq 'origin_phone2' && 'origin_phone2_ext'|array_key_exists:$BLOCK_FIELDS}
										<span id='originPhone2Span' {if $BLOCK_FIELDS['origin_phone2_type']->get('fieldvalue') neq 'Work'}class='hide'{/if}>
										&nbsp; Ext:&nbsp;
										{assign var="FIELD_INFO" value=Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($BLOCK_FIELDS['origin_phone2_ext']->getFieldInfo()))}
										{assign var="SPECIAL_VALIDATOR" value=$BLOCK_FIELDS['origin_phone2_ext']->getValidator()}
										{assign var="FIELD_LABEL" value=$BLOCK_FIELDS['origin_phone2_ext']->get('name')}
										<input id="{$MODULE}_editView_fieldName_{$FIELD_LABEL}" type="text" class="input-large {if $BLOCK_FIELDS['origin_phone2_ext']->isNameField()}nameField{/if}" data-validation-engine="validate[{if $BLOCK_FIELDS['origin_phone2_ext']->isMandatory() eq true}required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" name="{$BLOCK_FIELDS['origin_phone2_ext']->getFieldName()}" value="{$BLOCK_FIELDS['origin_phone2_ext']->get('fieldvalue')}"
										{if $BLOCK_FIELDS['origin_phone2_ext']->get('uitype') eq '3' || $BLOCK_FIELDS['origin_phone2_ext']->get('uitype') eq '4'|| $BLOCK_FIELDS['origin_phone2_ext']->isReadOnly()} readonly {/if} data-fieldinfo='{$FIELD_INFO}' {if !empty($SPECIAL_VALIDATOR)}data-validator={Zend_Json::encode($SPECIAL_VALIDATOR)}{/if} />
										<style>
											[name=origin_phone2_ext]{
												width: 50px;
											}
										</style>
										&nbsp;
										</span>
									{/if}
									{if $FIELD_NAME eq 'destination_phone1' && 'destination_phone1_ext'|array_key_exists:$BLOCK_FIELDS}
										<span id='destinationPhone1Span' {if $BLOCK_FIELDS['destination_phone1_type']->get('fieldvalue') neq 'Work'}class='hide'{/if}>
										&nbsp; Ext:&nbsp;
										{assign var="FIELD_INFO" value=Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($BLOCK_FIELDS['destination_phone1_ext']->getFieldInfo()))}
										{assign var="SPECIAL_VALIDATOR" value=$BLOCK_FIELDS['destination_phone1_ext']->getValidator()}
										{assign var="FIELD_LABEL" value=$BLOCK_FIELDS['destination_phone1_ext']->get('name')}
										<input id="{$MODULE}_editView_fieldName_{$FIELD_LABEL}" type="text" class="input-large {if $BLOCK_FIELDS['destination_phone1_ext']->isNameField()}nameField{/if}" data-validation-engine="validate[{if $BLOCK_FIELDS['destination_phone1_ext']->isMandatory() eq true}required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" name="{$BLOCK_FIELDS['destination_phone1_ext']->getFieldName()}" value="{$BLOCK_FIELDS['destination_phone1_ext']->get('fieldvalue')}"
										{if $BLOCK_FIELDS['destination_phone1_ext']->get('uitype') eq '3' || $BLOCK_FIELDS['destination_phone1_ext']->get('uitype') eq '4'|| $BLOCK_FIELDS['destination_phone1_ext']->isReadOnly()} readonly {/if} data-fieldinfo='{$FIELD_INFO}' {if !empty($SPECIAL_VALIDATOR)}data-validator={Zend_Json::encode($SPECIAL_VALIDATOR)}{/if} />
										<style>
											[name=destination_phone1_ext]{
												width: 50px;
											}
										</style>
										&nbsp;
										</span>
									{/if}
									{if $FIELD_NAME eq 'destination_phone2' && 'destination_phone2_ext'|array_key_exists:$BLOCK_FIELDS}
										<span id='destinationPhone2Span' {if $BLOCK_FIELDS['destination_phone2_type']->get('fieldvalue') neq 'Work'}class='hide'{/if}>
										&nbsp; Ext:&nbsp;
										{assign var="FIELD_INFO" value=Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($BLOCK_FIELDS['destination_phone2_ext']->getFieldInfo()))}
										{assign var="SPECIAL_VALIDATOR" value=$BLOCK_FIELDS['destination_phone2_ext']->getValidator()}
										{assign var="FIELD_LABEL" value=$BLOCK_FIELDS['destination_phone2_ext']->get('name')}
										<input id="{$MODULE}_editView_fieldName_{$FIELD_LABEL}" type="text" class="input-large {if $BLOCK_FIELDS['destination_phone2_ext']->isNameField()}nameField{/if}" data-validation-engine="validate[{if $BLOCK_FIELDS['destination_phone2_ext']->isMandatory() eq true}required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" name="{$BLOCK_FIELDS['destination_phone2_ext']->getFieldName()}" value="{$BLOCK_FIELDS['destination_phone2_ext']->get('fieldvalue')}"
										{if $BLOCK_FIELDS['destination_phone2_ext']->get('uitype') eq '3' || $BLOCK_FIELDS['destination_phone2_ext']->get('uitype') eq '4'|| $BLOCK_FIELDS['destination_phone2_ext']->isReadOnly()} readonly {/if} data-fieldinfo='{$FIELD_INFO}' {if !empty($SPECIAL_VALIDATOR)}data-validator={Zend_Json::encode($SPECIAL_VALIDATOR)}{/if} />
										<style>
											[name=destination_phone2_ext]{
												width: 50px;
											}
										</style>
										&nbsp;
										</span>
									{/if}
								{if $SHOW_TRANSIT_GUIDE && ($FIELD_NAME eq 'load_date')}
										<span id="TransitGuide">
<button type="button" class="transitGuide" name="transitGuide"><strong>{vtranslate('LBL_TRANSIT_GUIDE', $MODULE)}</strong></button>
                                        </span>
                                    {/if}
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
				{/foreach}

		{if $BLOCK_LABEL eq 'LBL_POTENTIALS_DATES'}
			{if $COUNTER eq 1}
				<td class="fieldLabel {$WIDTHTYPE}"></td><td class="{$WIDTHTYPE}"></td><td class="fieldLabel {$WIDTHTYPE}"></td><td class="{$WIDTHTYPE}"></td>
			{elseif $COUNTER eq 2}
				<td class="fieldLabel {$WIDTHTYPE}"></td><td class="{$WIDTHTYPE}"></td>
			{/if}
		{/if}
		{* adding additional column for odd number of fields in a block *}
		{if $BLOCK_FIELDS|@end eq true and $BLOCK_FIELDS|@count neq 1 and $COUNTER eq 1 and $BLOCK_LABEL neq 'LBL_POTENTIALS_DATES'}
			<td class="fieldLabel {$WIDTHTYPE}"></td><td class="{$WIDTHTYPE}"></td>
		{/if}
			</tr>
		{if ($BLOCK_LABEL eq 'LBL_OPPORTUNITY_INFORMATION' || $BLOCK_LABEL eq 'LBL_POTENTIALS_INFORMATION') && 'out_of_area'|array_key_exists:$BLOCK_FIELDS && 'out_of_origin'|array_key_exists:$BLOCK_FIELDS && 'small_move'|array_key_exists:$BLOCK_FIELDS && 'phone_estimate'|array_key_exists:$BLOCK_FIELDS}
        {if getenv('INSTANCE_NAME') == 'sirva'}
            {continue}
        {/if}
			<tr>
				<td colspan='1' class="fieldLabel"><label class="muted pull-right marginRight10px" style="padding-right: 5px">{vtranslate('LBL_OPPORTUNITY_NONCONFORMING',$MODULE_NAME)}</label></td>
				<td colspan='3' style="padding: 0">
					<table class="table table-bordered equalSplit detailview-table" style="padding: 0; border: 0">
						<tr>
							<th><label class="muted" style="text-align: center">Out of Area</label></th>
							<th><label class="muted" style="text-align: center">Out of Origin</label></th>
							<th><label class="muted" style="text-align: center">Small Move</label></th>
							<th><label class="muted" style="text-align: center">Phone Estimate</label></th>
						</tr>
						<tr>
							<td style='width: 25%; text-align: center'>
								{assign var="FIELD_INFO" value=Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($BLOCK_FIELDS['out_of_area']->getFieldInfo()))}
								{assign var="SPECIAL_VALIDATOR" value=$BLOCK_FIELDS['out_of_area']->getValidator()}
								{assign var="FIELD_NAME" value=$BLOCK_FIELDS['out_of_area']->get('name')}

								<input type="hidden" name="{$BLOCK_FIELDS['out_of_area']->getFieldName()}" value=0 />
								<input id="{$MODULE}_editView_fieldName_{$FIELD_NAME}" type="checkbox" name="{$BLOCK_FIELDS['out_of_area']->getFieldName()}" data-validation-engine="validate[funcCall[Vtiger_Base_Validator_Js.invokeValidation]]"
								{if $BLOCK_FIELDS['out_of_area']->get('fieldvalue') eq true} checked
								{/if} data-fieldinfo='{$FIELD_INFO}' {if !empty($SPECIAL_VALIDATOR)}data-validator={Zend_Json::encode($SPECIAL_VALIDATOR)}{/if} />
							</td>
							<td style='width: 25%; text-align: center'>
								{assign var="FIELD_INFO" value=Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($BLOCK_FIELDS['out_of_origin']->getFieldInfo()))}
								{assign var="SPECIAL_VALIDATOR" value=$BLOCK_FIELDS['out_of_origin']->getValidator()}
								{assign var="FIELD_NAME" value=$BLOCK_FIELDS['out_of_origin']->get('name')}

								<input type="hidden" name="{$BLOCK_FIELDS['out_of_origin']->getFieldName()}" value=0 />
								<input id="{$MODULE}_editView_fieldName_{$FIELD_NAME}" type="checkbox" name="{$BLOCK_FIELDS['out_of_origin']->getFieldName()}" data-validation-engine="validate[funcCall[Vtiger_Base_Validator_Js.invokeValidation]]"
								{if $BLOCK_FIELDS['out_of_origin']->get('fieldvalue') eq true} checked
								{/if} data-fieldinfo='{$FIELD_INFO}' {if !empty($SPECIAL_VALIDATOR)}data-validator={Zend_Json::encode($SPECIAL_VALIDATOR)}{/if} />
							</td>
							<td style='width: 25%; text-align: center'>
								{assign var="FIELD_INFO" value=Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($BLOCK_FIELDS['small_move']->getFieldInfo()))}
								{assign var="SPECIAL_VALIDATOR" value=$BLOCK_FIELDS['small_move']->getValidator()}
								{assign var="FIELD_NAME" value=$BLOCK_FIELDS['small_move']->get('name')}

								<input type="hidden" name="{$BLOCK_FIELDS['small_move']->getFieldName()}" value=0 />
								<input id="{$MODULE}_editView_fieldName_{$FIELD_NAME}" type="checkbox" name="{$BLOCK_FIELDS['small_move']->getFieldName()}" data-validation-engine="validate[funcCall[Vtiger_Base_Validator_Js.invokeValidation]]"
								{if $BLOCK_FIELDS['small_move']->get('fieldvalue') eq true} checked
								{/if} data-fieldinfo='{$FIELD_INFO}' {if !empty($SPECIAL_VALIDATOR)}data-validator={Zend_Json::encode($SPECIAL_VALIDATOR)}{/if} />
							</td>
							<td style='width: 25%; text-align: center'>
								{assign var="FIELD_INFO" value=Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($BLOCK_FIELDS['phone_estimate']->getFieldInfo()))}
								{assign var="SPECIAL_VALIDATOR" value=$BLOCK_FIELDS['phone_estimate']->getValidator()}
								{assign var="FIELD_NAME" value=$BLOCK_FIELDS['phone_estimate']->get('name')}

								<input type="hidden" name="{$BLOCK_FIELDS['phone_estimate']->getFieldName()}" value=0 />
								<input id="{$MODULE}_editView_fieldName_{$FIELD_NAME}" type="checkbox" name="{$BLOCK_FIELDS['phone_estimate']->getFieldName()}" data-validation-engine="validate[funcCall[Vtiger_Base_Validator_Js.invokeValidation]]"
								{if $BLOCK_FIELDS['phone_estimate']->get('fieldvalue') eq true} checked
								{/if} data-fieldinfo='{$FIELD_INFO}' {if !empty($SPECIAL_VALIDATOR)}data-validator={Zend_Json::encode($SPECIAL_VALIDATOR)}{/if} />
							</td>
						</tr>
					</table>
				</td>
			</tr>
		{/if}
		{if ($BLOCK_LABEL eq 'LBL_OPPORTUNITY_INFORMATION' || $BLOCK_LABEL eq 'LBL_POTENTIALS_INFORMATION') && 'move_type'|array_key_exists:$BLOCK_FIELDS}
			<tr class="hide">
				<td class="fieldLabel">
					<label class="muted pull-right marginRight10px">{if $BLOCK_FIELDS['business_line']->isMandatory() eq true} <span class="redColor">*</span> {/if}{vtranslate($BLOCK_FIELDS['business_line']->get('label'), $MODULE)}</label>
				</td>
				<td class="fieldValue">
					{assign var="FIELD_INFO" value=Zend_Json::encode($BLOCK_FIELDS['business_line']->getFieldInfo())}
					{assign var=PICKLIST_VALUES value=$BLOCK_FIELDS['business_line']->getPicklistValues()}
					{assign var="SPECIAL_VALIDATOR" value=$BLOCK_FIELDS['business_line']->getValidator()}
					{if $BLOCK_FIELDS['business_line']->get('name') eq {$BLFIELD}}
						<select onchange="loadBlocksByBusinesLine('{$MODULE}', '{$BLFIELD}')" class="chzn-select {if $OCCUPY_COMPLETE_WIDTH} row-fluid {/if}" name="{$BLOCK_FIELDS['business_line']->getFieldName()}" data-validation-engine="validate[{if $BLOCK_FIELDS['business_line']->isMandatory() eq true} required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" data-fieldinfo='{$FIELD_INFO|escape}' {if !empty($SPECIAL_VALIDATOR)}data-validator='{Zend_Json::encode($SPECIAL_VALIDATOR)}'{/if} data-selected-value='{$BLOCK_FIELDS['business_line']->get('fieldvalue')}'>
					{else}
						<select class="chzn-select {if $OCCUPY_COMPLETE_WIDTH} row-fluid {/if}" name="{$BLOCK_FIELDS['business_line']->getFieldName()}" data-validation-engine="validate[{if $BLOCK_FIELDS['business_line']->isMandatory() eq true} required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" data-fieldinfo='{$FIELD_INFO|escape}' {if !empty($SPECIAL_VALIDATOR)}data-validator='{Zend_Json::encode($SPECIAL_VALIDATOR)}'{/if} data-selected-value='{$BLOCK_FIELDS['business_line']->get('fieldvalue')}'>
					{/if}
						{if $BLOCK_FIELDS['business_line']->isEmptyPicklistOptionAllowed()}<option value="">{vtranslate('LBL_SELECT_OPTION','Vtiger')}</option>{/if}
						{foreach item=PICKLIST_VALUE key=PICKLIST_NAME from=$PICKLIST_VALUES}
							<option value="{Vtiger_Util_Helper::toSafeHTML($PICKLIST_NAME)}" {if trim(decode_html($BLOCK_FIELDS['business_line']->get('fieldvalue'))) eq trim($PICKLIST_NAME)} selected {/if}>{$PICKLIST_VALUE}</option>
						{/foreach}
					</select>
				</td>
				<td class="fieldLabel">
					&nbsp;
				</td>
				<td>
					&nbsp;
				</td>
			</tr>
		{/if}
</tbody>
			</table>
			<br>
			{if $MODULE eq 'Opportunities' && $BLOCK_LABEL eq 'LBL_POTENTIALS_DATES' && getenv('INSTANCE_NAME') != 'graebel'}
				{if isParticipantForRecord($RECORD_ID)}
					{include file=vtemplate_path('participatingAgentsDetail.tpl', 'ParticipatingAgents')}
				{else}
					{include file=vtemplate_path('participatingAgentsEdit.tpl', 'ParticipatingAgents')}
				{/if}
			{/if}
			{if $MODULE eq 'Opportunities' && $BLOCK_LABEL eq 'LBL_POTENTIALS_DATES'}
				{if $AUTO_SPOT_QUOTE_MODULE && AUTO_QUOTES && $AUTO_SPOT_QUOTE_MODULE->isActive()}
				<table name='AddressSegmentsTable' class='table table-bordered blockContainer showInlineTable'>
					<thead>
					<tr>
						<th class='blockHeader' colspan='9'>{vtranslate('AutoSpotQuote', 'AutoSpotQuote')}</th>
					</tr>
					</thead>
					<tbody>
						<tr style="width:100%" class="fieldLabel">
							<td style="text-align:center;margin:auto;width:25%;"><b>Make</b></td>
							<td style="text-align:center;margin:auto;width:25%;"><b>Model</b></td>
							<td style="text-align:center;margin:auto;width:25%;"><b>Year</b></td>
							<td style="text-align:center;margin:auto;width:25%;"><b>Registration #</b></td>
						</tr>
						{foreach item=AUTO_QUOTES_VALUE key=AUTO_QUOTES_number from=$AUTO_QUOTES}
							<tr style="width:100%">
							<td style="text-align:center;margin:auto;width:25%;"><b>{$AUTO_QUOTES_VALUE.auto_make}</b></td>
							<td style="text-align:center;margin:auto;width:25%;"><b>{$AUTO_QUOTES_VALUE.auto_model}</b></td>
							<td style="text-align:center;margin:auto;width:25%;"><b>{$AUTO_QUOTES_VALUE.auto_year}</b></td>
							<td style="text-align:center;margin:auto;width:25%;"><b>{if $AUTO_QUOTES_VALUE.registration_number neq ''}{$AUTO_QUOTES_VALUE.registration_number}{else}Not registered{/if}</b></td>
						</tr>
						{/foreach}
					</tbody>
				</table>
				<br />
				{/if}
				{*{include file=vtemplate_path('extraStopsEdit.tpl', 'ExtraStops')}*}
				{include file=vtemplate_path('GuestEditBlocks.tpl', $MODULE)}
                {if getenv('INSTANCE_NAME') != 'graebel' && getenv('INSTANCE_NAME') != 'sirva' }
                    {include file=vtemplate_path('ReferralBlockEdit.tpl', 'Opportunities') BLOCK_LABEL='LBL_OPPORTUNITIES_REFERRAL' BLOCK_FIELDS = $RECORD_STRUCTURE['LBL_OPPORTUNITIES_REFERRAL'] }
                {/if}
			{/if}
			{include file=vtemplate_path('SequencedGuestEditBlocks.tpl', $MODULE) BLOCK_LABEL=$BLOCK_LABEL}
		{/foreach}
{/strip}
