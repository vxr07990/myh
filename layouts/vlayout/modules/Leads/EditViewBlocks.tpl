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
    <form class="form-horizontal recordEditView" id="EditView" name="EditView" method="post" action="index.php"
          enctype="multipart/form-data">
        {assign var=WIDTHTYPE value=$USER_MODEL->get('rowheight')}
        {if !empty($PICKIST_DEPENDENCY_DATASOURCE)}
            <input type="hidden" name="picklistDependency"
                   value='{Vtiger_Util_Helper::toSafeHTML($PICKIST_DEPENDENCY_DATASOURCE)}'/>
        {/if}
        {assign var=QUALIFIED_MODULE_NAME value={$MODULE}}
        {assign var=IS_PARENT_EXISTS value=strpos($MODULE,":")}
        {if $IS_PARENT_EXISTS}
            {assign var=SPLITTED_MODULE value=":"|explode:$MODULE}
            <input type="hidden" name="module" value="{$SPLITTED_MODULE[1]}"/>
            <input type="hidden" name="parent" value="{$SPLITTED_MODULE[0]}"/>
        {else}
            <input type="hidden" name="module" value="{$MODULE}"/>
        {/if}
        <input type="hidden" id="duplicate" value="{$IS_DUPLICATE}"/>
        <input type="hidden" name="action" value="Save"/>
        <input type="hidden" name="record" value="{$RECORD_ID}"/>
        <input type="hidden" name="defaultCallDuration" value="{$USER_MODEL->get('callduration')}"/>
        <input type="hidden" name="defaultOtherEventDuration" value="{$USER_MODEL->get('othereventduration')}"/>
        {if $IS_RELATION_OPERATION }
            <input type="hidden" name="sourceModule" value="{$SOURCE_MODULE}"/>
            <input type="hidden" name="sourceRecord" value="{$SOURCE_RECORD}"/>
            <input type="hidden" name="relationOperation" value="{$IS_RELATION_OPERATION}"/>
        {/if}
        <div class="contentHeader row-fluid">
            {assign var=SINGLE_MODULE_NAME value='SINGLE_'|cat:$MODULE}
            {if $RECORD_ID neq ''}
                <h3 class="span8 textOverflowEllipsis"
                    title="{vtranslate('LBL_EDITING', $MODULE)} {vtranslate($SINGLE_MODULE_NAME, $MODULE)} {$RECORD_STRUCTURE_MODEL->getRecordName()}">{vtranslate('LBL_EDITING', $MODULE)} {vtranslate($SINGLE_MODULE_NAME, $MODULE)}
                    - {$RECORD_STRUCTURE_MODEL->getRecordName()}</h3>
            {else}
                <h3 class="span8 textOverflowEllipsis">{vtranslate('LBL_CREATING_NEW', $MODULE)} {vtranslate($SINGLE_MODULE_NAME, $MODULE)}</h3>
            {/if}
            <span class="pull-right">
				<button class="btn btn-success" type="submit"><strong>{vtranslate('LBL_SAVE', $MODULE)}</strong>
                </button>
				<a class="cancelLink" type="reset"
                   onclick="javascript:window.history.back();">{vtranslate('LBL_CANCEL', $MODULE)}</a>
			</span>
        </div>

        <input type="hidden" id="hiddenFields" name="hiddenFields" value="{$HIDDEN_FIELDS}"/>


        {foreach key=BLOCK_LABEL item=BLOCK_FIELDS from=$RECORD_STRUCTURE name="EditViewBlockLevelLoop"}
            {if $BLOCK_LABEL == 'LBL_LEADS_ADDRESSINFORMATION'}
                {if $IS_ACTIVE_ADDRESSLIST == true}
                    {include file=vtemplate_path('AddressListEdit.tpl', 'AddressList')}
                    {continue}
                {/if}
            {/if}
            {if $BLOCK_FIELDS|@count lte 0}{continue}{/if}
            {if $BLOCK_LABEL eq 'LBL_LEADS_DESCRIPTIONINFORMATION'}{continue}{/if}
            <table name='{$BLOCK_LABEL}'
                   class="table table-bordered blockContainer showInlineTable{if is_array($HIDDEN_BLOCKS)}{if in_array($BLOCK_LABEL, $HIDDEN_BLOCKS)} hide{/if}{/if}">
                <thead>
                <tr>
                    <th class="blockHeader" colspan="10">{vtranslate($BLOCK_LABEL, $MODULE)}</th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    {assign var=COUNTER value=0}
                    {foreach key=FIELD_NAME item=FIELD_MODEL from=$BLOCK_FIELDS name=blockfields}
                    {if $FIELD_NAME eq 'out_of_area' || $FIELD_NAME eq 'out_of_origin' || $FIELD_NAME eq 'out_of_time' ||$FIELD_NAME eq 'small_move' || $FIELD_NAME eq 'phone_estimate' || $FIELD_NAME eq 'primary_phone_ext' || ($FIELD_NAME eq 'business_line' && 'move_type'|array_key_exists:$BLOCK_FIELDS)}
                        {continue}
                    {/if}
                    {if $FIELD_NAME == 'employer_comments' && getenv('INSTANCE_NAME') == 'sirva'}
                    {if $COUNTER eq 1}
                    <td colspan='1' class="fieldLabel">&nbsp;</td>
                    <td colspan='1' class="fieldValue">&nbsp;</td>
                </tr>
                {assign var="COUNTER" value=0}
                {/if}
                <tr>
                    <td colspan='1' class="fieldLabel"><label class="muted pull-right marginRight10px"
                                                              style="padding-right: 5px">{vtranslate('LBL_LEADS_NONCONFORMING',$MODULE_NAME)}</label>
                    </td>
                    <td colspan='3' style="padding: 0">
                        <table class="table table-bordered equalSplit detailview-table"
                               style="padding: 0; border: 0">
                            <tr>
                                <th><label class="muted" style="text-align: center">Out of Area</label></th>
                                <th><label class="muted" style="text-align: center">Out of Origin</label></th>
                                <th><label class="muted" style="text-align: center">Out of Time</label></th>
                                <th><label class="muted" style="text-align: center">Small Move</label></th>
                                <th><label class="muted" style="text-align: center">Phone Estimate</label></th>
                            </tr>
                            <tr>
                                <td style='width: 20%; text-align: center'>
                                    {assign var="FIELD_INFO" value=Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($BLOCK_FIELDS['out_of_area']->getFieldInfo()))}
                                    {assign var="SPECIAL_VALIDATOR" value=$BLOCK_FIELDS['out_of_area']->getValidator()}
                                    {assign var="FIELD_NAME" value=$BLOCK_FIELDS['out_of_area']->get('name')}

                                    <input type="hidden" name="{$BLOCK_FIELDS['out_of_area']->getFieldName()}"
                                           value=0/>
                                    <input id="{$MODULE}_editView_fieldName_{$FIELD_NAME}" type="checkbox"
                                           name="{$BLOCK_FIELDS['out_of_area']->getFieldName()}"
                                           data-validation-engine="validate[funcCall[Vtiger_Base_Validator_Js.invokeValidation]]"
                                            {if $BLOCK_FIELDS['out_of_area']->get('fieldvalue') eq true} checked
                                            {/if} data-fieldinfo='{$FIELD_INFO}'
                                            {if !empty($SPECIAL_VALIDATOR)}data-validator={Zend_Json::encode($SPECIAL_VALIDATOR)}{/if}/>
                                </td>
                                <td style='width: 20%; text-align: center'>
                                    {assign var="FIELD_INFO" value=Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($BLOCK_FIELDS['out_of_origin']->getFieldInfo()))}
                                    {assign var="SPECIAL_VALIDATOR" value=$BLOCK_FIELDS['out_of_origin']->getValidator()}
                                    {assign var="FIELD_NAME" value=$BLOCK_FIELDS['out_of_origin']->get('name')}

                                    <input type="hidden" name="{$BLOCK_FIELDS['out_of_origin']->getFieldName()}"
                                           value=0/>
                                    <input id="{$MODULE}_editView_fieldName_{$FIELD_NAME}" type="checkbox"
                                           name="{$BLOCK_FIELDS['out_of_origin']->getFieldName()}"
                                           data-validation-engine="validate[funcCall[Vtiger_Base_Validator_Js.invokeValidation]]"
                                            {if $BLOCK_FIELDS['out_of_origin']->get('fieldvalue') eq true} checked
                                            {/if} data-fieldinfo='{$FIELD_INFO}'
                                            {if !empty($SPECIAL_VALIDATOR)}data-validator={Zend_Json::encode($SPECIAL_VALIDATOR)}{/if}/>
                                </td>
                                <td style='width: 20%; text-align: center'>
                                    {assign var="FIELD_INFO" value=Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($BLOCK_FIELDS['out_of_time']->getFieldInfo()))}
                                    {assign var="SPECIAL_VALIDATOR" value=$BLOCK_FIELDS['out_of_time']->getValidator()}
                                    {assign var="FIELD_NAME" value=$BLOCK_FIELDS['out_of_time']->get('name')}

                                    <input type="hidden" name="{$BLOCK_FIELDS['out_of_time']->getFieldName()}"
                                           value=0/>
                                    <input id="{$MODULE}_editView_fieldName_{$FIELD_NAME}" type="checkbox"
                                           name="{$BLOCK_FIELDS['out_of_time']->getFieldName()}"
                                           data-validation-engine="validate[funcCall[Vtiger_Base_Validator_Js.invokeValidation]]"
                                            {if $BLOCK_FIELDS['out_of_time']->get('fieldvalue') eq true} checked
                                            {/if} data-fieldinfo='{$FIELD_INFO}'
                                            {if !empty($SPECIAL_VALIDATOR)}data-validator={Zend_Json::encode($SPECIAL_VALIDATOR)}{/if}/>
                                </td>
                                <td style='width: 20%; text-align: center'>
                                    {assign var="FIELD_INFO" value=Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($BLOCK_FIELDS['small_move']->getFieldInfo()))}
                                    {assign var="SPECIAL_VALIDATOR" value=$BLOCK_FIELDS['small_move']->getValidator()}
                                    {assign var="FIELD_NAME" value=$BLOCK_FIELDS['small_move']->get('name')}

                                    <input type="hidden" name="{$BLOCK_FIELDS['small_move']->getFieldName()}"
                                           value=0/>
                                    <input id="{$MODULE}_editView_fieldName_{$FIELD_NAME}" type="checkbox"
                                           name="{$BLOCK_FIELDS['small_move']->getFieldName()}"
                                           data-validation-engine="validate[funcCall[Vtiger_Base_Validator_Js.invokeValidation]]"
                                            {if $BLOCK_FIELDS['small_move']->get('fieldvalue') eq true} checked
                                            {/if} data-fieldinfo='{$FIELD_INFO}'
                                            {if !empty($SPECIAL_VALIDATOR)}data-validator={Zend_Json::encode($SPECIAL_VALIDATOR)}{/if}/>
                                </td>
                                <td style='width: 20%; text-align: center'>
                                    {assign var="FIELD_INFO" value=Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($BLOCK_FIELDS['phone_estimate']->getFieldInfo()))}
                                    {assign var="SPECIAL_VALIDATOR" value=$BLOCK_FIELDS['phone_estimate']->getValidator()}
                                    {assign var="FIELD_NAME" value=$BLOCK_FIELDS['phone_estimate']->get('name')}

                                    <input type="hidden" name="{$BLOCK_FIELDS['phone_estimate']->getFieldName()}"
                                           value=0/>
                                    <input id="{$MODULE}_editView_fieldName_{$FIELD_NAME}" type="checkbox"
                                           name="{$BLOCK_FIELDS['phone_estimate']->getFieldName()}"
                                           data-validation-engine="validate[funcCall[Vtiger_Base_Validator_Js.invokeValidation]]"
                                            {if $BLOCK_FIELDS['phone_estimate']->get('fieldvalue') eq true} checked
                                            {/if} data-fieldinfo='{$FIELD_INFO}'
                                            {if !empty($SPECIAL_VALIDATOR)}data-validator={Zend_Json::encode($SPECIAL_VALIDATOR)}{/if}/>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                {/if}

                {assign var="isReferenceField" value=$FIELD_MODEL->getFieldDataType()}
                {if $FIELD_MODEL->get('uitype') eq "20" or $FIELD_MODEL->get('uitype') eq "19"}
                    {if $COUNTER eq '1'}
                    <td class="fieldLabel {$WIDTHTYPE}"></td>
                    <td class="{$WIDTHTYPE}"></td>
                    </tr>
                <tr>
                    {assign var=COUNTER value=0}
                    {/if}
                    {/if}
                    {if  $BLOCK_LABEL eq 'LBL_LEADS_DATES'}
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

                    {if $FIELD_NAME neq 'origin_phone1_ext' && $FIELD_NAME neq 'origin_phone2_ext' && $FIELD_NAME neq 'destination_phone1_ext' && $FIELD_NAME neq 'destination_phone2_ext' && $FIELD_NAME neq 'primary_phone_ext'}
                        <td class="fieldLabel {$WIDTHTYPE}{if $FIELD_NAME eq 'disposition_lost_reasons'} hide{/if}"
                            {if $BLOCK_LABEL eq 'LBL_LEADS_DATES'}style='width:14%'{/if}>
                            {if $isReferenceField neq "reference"}
                            <label class="muted pull-right marginRight10px">{/if}
                                {if $FIELD_MODEL->isMandatory() eq true && $isReferenceField neq "reference"}
                                    <span class="redColor">*</span>
                                {/if}
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
                                    {if $FIELD_MODEL->isMandatory() eq true}<span class="redColor">*</span>{/if}
                                            <select id="{$MODULE}_editView_fieldName_{$FIELD_MODEL->getName()}_dropDown"
                                                    class="chzn-select referenceModulesList streched"
                                                    style="width:160px;">
                                                <optgroup>
                                                    {foreach key=index item=value from=$REFERENCE_LIST}
                                                        <option value="{$value}" {if $value eq $REFERENCED_MODULE_NAME} selected {/if}>{vtranslate($value, $MODULE)}</option>
                                                    {/foreach}
                                                </optgroup>
                                            </select>
                                </span>
                                    {else}
                                        <label class="muted pull-right marginRight10px">{if $FIELD_MODEL->isMandatory() eq true}
                                                <span class="redColor">*</span>
                                            {/if}{vtranslate($FIELD_MODEL->get('label'), $MODULE)}</label>
                                    {/if}
                                {elseif $FIELD_MODEL->get('uitype') eq "83"}
                                    {include file=vtemplate_path($FIELD_MODEL->getUITypeModel()->getTemplateName(),$MODULE) COUNTER=$COUNTER MODULE=$MODULE}
                                {else}
                                    {vtranslate($FIELD_MODEL->get('label'), $MODULE)}
                                {/if}
                                {if $isReferenceField neq "reference"}</label>{/if}
                        </td>
                    {/if}
                    {if $FIELD_MODEL->get('uitype') neq "83" && $FIELD_NAME neq 'origin_phone1_ext' && $FIELD_NAME neq 'origin_phone2_ext' && $FIELD_NAME neq 'destination_phone1_ext' && $FIELD_NAME neq 'destination_phone2_ext' && $FIELD_NAME neq 'primary_phone_ext'}
                        <td class="fieldValue {$WIDTHTYPE}{if $FIELD_NAME eq 'disposition_lost_reasons'} hide{/if}"
                            {if $BLOCK_LABEL eq 'LBL_LEADS_DATES'}style='width:16.6%'{else} {/if} {if $FIELD_MODEL->get('uitype') eq '19' or $FIELD_MODEL->get('uitype') eq '20'} colspan="3" {assign var=COUNTER value=$COUNTER+1} {/if}>
                            <div class="row-fluid">
                        <span class="span10">
                            {if
                            $FIELD_NAME neq 'days_to_move' &&
                            $FIELD_NAME neq 'program_name' &&
                            $FIELD_NAME neq 'brand'}
                                {if $FIELD_NAME eq lmp_lead_id || $FIELD_NAME eq cc_disposition || $FIELD_NAME eq 'offer_number'}
                                    {assign var="FIELD_INFO" value=Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($FIELD_MODEL->getFieldInfo()))}
                                    {assign var="SPECIAL_VALIDATOR" value=$FIELD_MODEL->getValidator()}
                                    {assign var="FIELD_NAME" value=$FIELD_MODEL->get('name')}
                                    <input id="{$MODULE}_editView_fieldName_{$FIELD_NAME}" type="text"
                                           class="input-large {if $FIELD_MODEL->isNameField()}nameField{/if}"
                                           data-validation-engine="validate[{if $FIELD_MODEL->isMandatory() eq true}required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]"
                                           name="{$FIELD_MODEL->getFieldName()}"
                                           value="{$FIELD_MODEL->get('fieldvalue')}"
                                           readonly data-fieldinfo='{$FIELD_INFO}'
                                           {if !empty($SPECIAL_VALIDATOR)}data-validator={Zend_Json::encode($SPECIAL_VALIDATOR)}{/if}/>

                                                                            {elseif $FIELD_NAME eq 'brand'}
                                    {assign var="FIELD_INFO" value=Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($FIELD_MODEL->getFieldInfo()))}
                                    {assign var="SPECIAL_VALIDATOR" value=$FIELD_MODEL->getValidator()}
                                    {assign var="FIELD_NAME" value=$FIELD_MODEL->get('name')}

                                    <input id="{$MODULE}_editView_fieldName_{$FIELD_NAME}" type="text"
                                           class="input-large {if $FIELD_MODEL->isNameField()}nameField{/if}"
                                           data-validation-engine="validate[{if $FIELD_MODEL->isMandatory() eq true}required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]"
                                           name="{$FIELD_MODEL->getFieldName()}" value="{$BRAND_FIELD_MODEL}"
                                {else}
                                    {include file=vtemplate_path($FIELD_MODEL->getUITypeModel()->getTemplateName(),$MODULE) BLOCK_FIELDS=$BLOCK_FIELDS}
                                {/if}
                            {else}
                                {assign var="FIELD_INFO" value=Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($FIELD_MODEL->getFieldInfo()))}
                                {assign var="SPECIAL_VALIDATOR" value=$FIELD_MODEL->getValidator()}
                                {assign var="FIELD_NAME" value=$FIELD_MODEL->get('name')}
                                <input id="{$MODULE}_editView_fieldName_{$FIELD_NAME}" type="text"
                                       class="input-large {if $FIELD_MODEL->isNameField()}nameField{/if}"
                                       data-validation-engine="validate[{if $FIELD_MODEL->isMandatory() eq true}required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]"
                                       name="{$FIELD_MODEL->getFieldName()}"
                                       value="{$FIELD_MODEL->get('fieldvalue')}"
                                       readonly data-fieldinfo='{$FIELD_INFO}'
                                       {if !empty($SPECIAL_VALIDATOR)}data-validator={Zend_Json::encode($SPECIAL_VALIDATOR)}{/if}/>
                            {/if}
                            {if $FIELD_NAME eq 'phone' && 'primary_phone_ext'|array_key_exists:$BLOCK_FIELDS}
                                <span id='primaryPhoneSpan'
                                      {if $BLOCK_FIELDS['primary_phone_type']->get('fieldvalue') neq 'Work'}class='hide'{/if}>
                                &nbsp; Ext:&nbsp;
                                    {assign var="FIELD_INFO" value=Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($BLOCK_FIELDS['primary_phone_ext']->getFieldInfo()))}
                                    {assign var="SPECIAL_VALIDATOR" value=$BLOCK_FIELDS['primary_phone_ext']->getValidator()}
                                    {assign var="FIELD_LABEL" value=$BLOCK_FIELDS['primary_phone_ext']->get('name')}
                                    <input id="{$MODULE}_editView_fieldName_{$FIELD_LABEL}" type="text"
                                           class="input-large {if $BLOCK_FIELDS['primary_phone_ext']->isNameField()}nameField{/if}"
                                           data-validation-engine="validate[{if $BLOCK_FIELDS['primary_phone_ext']->isMandatory() eq true}required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]"
                                           name="{$BLOCK_FIELDS['primary_phone_ext']->getFieldName()}"
                                           value="{$BLOCK_FIELDS['primary_phone_ext']->get('fieldvalue')}"
                                            {if $BLOCK_FIELDS['primary_phone_ext']->get('uitype') eq '3' || $BLOCK_FIELDS['primary_phone_ext']->get('uitype') eq '4'|| $BLOCK_FIELDS['primary_phone_ext']->isReadOnly()} readonly {/if}
                                           data-fieldinfo='{$FIELD_INFO}'
                                            {if !empty($SPECIAL_VALIDATOR)}data-validator={Zend_Json::encode($SPECIAL_VALIDATOR)}{/if}/>
                                <style>
                                    [name=primary_phone_ext] {
                                        width: 50px;
                                    }
                                </style>
                                &nbsp;
                                </span>
                            {/if}
                            {if $FIELD_NAME eq 'origin_phone1' && 'origin_phone1_ext'|array_key_exists:$BLOCK_FIELDS}
                                <span id='originPhone1Span'
                                      {if $BLOCK_FIELDS['origin_phone1_type']->get('fieldvalue') neq 'Work'}class='hide'{/if}>
                                &nbsp; Ext:&nbsp;
                                    {assign var="FIELD_INFO" value=Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($BLOCK_FIELDS['origin_phone1_ext']->getFieldInfo()))}
                                    {assign var="SPECIAL_VALIDATOR" value=$BLOCK_FIELDS['origin_phone1_ext']->getValidator()}
                                    {assign var="FIELD_LABEL" value=$BLOCK_FIELDS['origin_phone1_ext']->get('name')}
                                    <input id="{$MODULE}_editView_fieldName_{$FIELD_LABEL}" type="text"
                                           class="input-large {if $BLOCK_FIELDS['origin_phone1_ext']->isNameField()}nameField{/if}"
                                           data-validation-engine="validate[{if $BLOCK_FIELDS['origin_phone1_ext']->isMandatory() eq true}required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]"
                                           name="{$BLOCK_FIELDS['origin_phone1_ext']->getFieldName()}"
                                           value="{$BLOCK_FIELDS['origin_phone1_ext']->get('fieldvalue')}"
                                            {if $BLOCK_FIELDS['origin_phone1_ext']->get('uitype') eq '3' || $BLOCK_FIELDS['origin_phone1_ext']->get('uitype') eq '4'|| $BLOCK_FIELDS['origin_phone1_ext']->isReadOnly()} readonly {/if}
                                           data-fieldinfo='{$FIELD_INFO}'
                                            {if !empty($SPECIAL_VALIDATOR)}data-validator={Zend_Json::encode($SPECIAL_VALIDATOR)}{/if}/>
                                <style>
                                    [name=origin_phone1_ext] {
                                        width: 50px;
                                    }
                                </style>
                                &nbsp;
                                </span>
                            {/if}
                            {if $FIELD_NAME eq 'origin_phone2' && 'origin_phone2_ext'|array_key_exists:$BLOCK_FIELDS}
                                <span id='originPhone2Span'
                                      {if $BLOCK_FIELDS['origin_phone2_type']->get('fieldvalue') neq 'Work'}class='hide'{/if}>
                                &nbsp; Ext:&nbsp;
                                    {assign var="FIELD_INFO" value=Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($BLOCK_FIELDS['origin_phone2_ext']->getFieldInfo()))}
                                    {assign var="SPECIAL_VALIDATOR" value=$BLOCK_FIELDS['origin_phone2_ext']->getValidator()}
                                    {assign var="FIELD_LABEL" value=$BLOCK_FIELDS['origin_phone2_ext']->get('name')}
                                    <input id="{$MODULE}_editView_fieldName_{$FIELD_LABEL}" type="text"
                                           class="input-large {if $BLOCK_FIELDS['origin_phone2_ext']->isNameField()}nameField{/if}"
                                           data-validation-engine="validate[{if $BLOCK_FIELDS['origin_phone2_ext']->isMandatory() eq true}required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]"
                                           name="{$BLOCK_FIELDS['origin_phone2_ext']->getFieldName()}"
                                           value="{$BLOCK_FIELDS['origin_phone2_ext']->get('fieldvalue')}"
                                            {if $BLOCK_FIELDS['origin_phone2_ext']->get('uitype') eq '3' || $BLOCK_FIELDS['origin_phone2_ext']->get('uitype') eq '4'|| $BLOCK_FIELDS['origin_phone2_ext']->isReadOnly()} readonly {/if}
                                           data-fieldinfo='{$FIELD_INFO}'
                                            {if !empty($SPECIAL_VALIDATOR)}data-validator={Zend_Json::encode($SPECIAL_VALIDATOR)}{/if}/>
                                <style>
                                    [name=origin_phone2_ext] {
                                        width: 50px;
                                    }
                                </style>
                                &nbsp;
                                </span>
                            {/if}
                            {if $FIELD_NAME eq 'destination_phone1' && 'destination_phone1_ext'|array_key_exists:$BLOCK_FIELDS}
                                <span id='destinationPhone1Span'
                                      {if $BLOCK_FIELDS['destination_phone1_type']->get('fieldvalue') neq 'Work'}class='hide'{/if}>
                                &nbsp; Ext:&nbsp;
                                    {assign var="FIELD_INFO" value=Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($BLOCK_FIELDS['destination_phone1_ext']->getFieldInfo()))}
                                    {assign var="SPECIAL_VALIDATOR" value=$BLOCK_FIELDS['destination_phone1_ext']->getValidator()}
                                    {assign var="FIELD_LABEL" value=$BLOCK_FIELDS['destination_phone1_ext']->get('name')}
                                    <input id="{$MODULE}_editView_fieldName_{$FIELD_LABEL}" type="text"
                                           class="input-large {if $BLOCK_FIELDS['destination_phone1_ext']->isNameField()}nameField{/if}"
                                           data-validation-engine="validate[{if $BLOCK_FIELDS['destination_phone1_ext']->isMandatory() eq true}required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]"
                                           name="{$BLOCK_FIELDS['destination_phone1_ext']->getFieldName()}"
                                           value="{$BLOCK_FIELDS['destination_phone1_ext']->get('fieldvalue')}"
                                            {if $BLOCK_FIELDS['destination_phone1_ext']->get('uitype') eq '3' || $BLOCK_FIELDS['destination_phone1_ext']->get('uitype') eq '4'|| $BLOCK_FIELDS['destination_phone1_ext']->isReadOnly()} readonly {/if}
                                           data-fieldinfo='{$FIELD_INFO}'
                                            {if !empty($SPECIAL_VALIDATOR)}data-validator={Zend_Json::encode($SPECIAL_VALIDATOR)}{/if}/>
                                <style>
                                    [name=destination_phone1_ext] {
                                        width: 50px;
                                    }
                                </style>
                                &nbsp;
                                </span>
                            {/if}
                            {if $FIELD_NAME eq 'destination_phone2' && 'destination_phone2_ext'|array_key_exists:$BLOCK_FIELDS}
                                <span id='destinationPhone2Span'
                                      {if $BLOCK_FIELDS['destination_phone2_type']->get('fieldvalue') neq 'Work'}class='hide'{/if}>
                                &nbsp; Ext:&nbsp;
                                    {assign var="FIELD_INFO" value=Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($BLOCK_FIELDS['destination_phone2_ext']->getFieldInfo()))}
                                    {assign var="SPECIAL_VALIDATOR" value=$BLOCK_FIELDS['destination_phone2_ext']->getValidator()}
                                    {assign var="FIELD_LABEL" value=$BLOCK_FIELDS['destination_phone2_ext']->get('name')}
                                    <input id="{$MODULE}_editView_fieldName_{$FIELD_LABEL}" type="text"
                                           class="input-large {if $BLOCK_FIELDS['destination_phone2_ext']->isNameField()}nameField{/if}"
                                           data-validation-engine="validate[{if $BLOCK_FIELDS['destination_phone2_ext']->isMandatory() eq true}required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]"
                                           name="{$BLOCK_FIELDS['destination_phone2_ext']->getFieldName()}"
                                           value="{$BLOCK_FIELDS['destination_phone2_ext']->get('fieldvalue')}"
                                            {if $BLOCK_FIELDS['destination_phone2_ext']->get('uitype') eq '3' || $BLOCK_FIELDS['destination_phone2_ext']->get('uitype') eq '4'|| $BLOCK_FIELDS['destination_phone2_ext']->isReadOnly()} readonly {/if}
                                           data-fieldinfo='{$FIELD_INFO}'
                                            {if !empty($SPECIAL_VALIDATOR)}data-validator={Zend_Json::encode($SPECIAL_VALIDATOR)}{/if}/>
                                <style>
                                    [name=destination_phone2_ext] {
                                        width: 50px;
                                    }
                                </style>
                                &nbsp;
                                </span>
                            {/if}
                        </span>
                            </div>
                        </td>
                    {/if}
                    {if $FIELD_NAME eq 'disposition_lost_reasons'}
                        <td class="dispLostFiller fieldLabel {$WIDTHTYPE}{if $COUNTER eq 1} hide{/if}"></td>
                        <td class="dispLostFiller fieldValue {$WIDTHTYPE}{if $COUNTER eq 1} hide{/if}"></td>
                        {assign var=COUNTER value=$COUNTER - 1}
                    {/if}
                    {if $BLOCK_FIELDS|@count eq 1 and $FIELD_MODEL->get('uitype') neq "19" and $FIELD_MODEL->get('uitype') neq "20" and $FIELD_MODEL->get('uitype') neq "30" and $FIELD_MODEL->get('name') neq "recurringtype"}
                        <td class="{$WIDTHTYPE}"></td>
                        <td class="{$WIDTHTYPE}"></td>
                    {/if}
                    {if $MODULE eq 'Events' && $BLOCK_LABEL eq 'LBL_EVENT_INFORMATION' && $smarty.foreach.blockfields.last }
                        {include file=vtemplate_path('uitypes/FollowUp.tpl',$MODULE) COUNTER=$COUNTER}
                    {/if}
                    {if $FIELD_NAME == 'disposition_lost_reasons' && getenv('INSTANCE_NAME') == 'sirva'}
                    {if $COUNTER == 1} {assign var=COUNTER value=0} {/if}
                    <tr colspan="4" style="padding:0;" class="hide pricingCompList">
                        <td colspan="4" style="padding:0;">
                            <table colspan="4" class="table table-bordered equalSplit"
                                   style="padding: 0; border: 0">
                                <tbody>
                                {assign var=LIST_WIDTH value=(1/8)*100}
                                <tr class="fieldLabel">
                                    <td colspan="8" class="blockHeader">
                                        <span class="PricingCompetitorList"><b>&nbsp;&nbsp;&nbsp;Pricing
                                                Competitors</b></span>
                                    </td>
                                </tr>
                                <td class="fieldLabel {$WIDTHTYPE}" style="width: {$LIST_WIDTH}%"></td>
                                <td class="fieldLabel {$WIDTHTYPE}" style="width: {$LIST_WIDTH}%"><label
                                            class="muted" style="text-align: center">Booked</label></td>
                                <td class="fieldLabel {$WIDTHTYPE}" style="width: {$LIST_WIDTH}%"><label
                                            class="muted" style="text-align: center">Estimate Provided</label></td>
                                <td class="fieldLabel {$WIDTHTYPE}" style="width: {$LIST_WIDTH}%"><label
                                            class="muted" style="text-align: center">None</label></td>
                                <td class="fieldLabel {$WIDTHTYPE}" style="width: {$LIST_WIDTH}%"></td>
                                <td class="fieldLabel {$WIDTHTYPE}" style="width: {$LIST_WIDTH}%"><label
                                            class="muted" style="text-align: center">Booked</label></td>
                                <td class="fieldLabel {$WIDTHTYPE}" style="width: {$LIST_WIDTH}%"><label
                                            class="muted" style="text-align: center">Estimate Provided</label></td>
                                <td class="fieldLabel {$WIDTHTYPE}" style="width: {$LIST_WIDTH}%"><label
                                            class="muted" style="text-align: center">None</label></td>
                                <tr>
                                </tr>
                                <tr>
                                    <td class="fieldLabel {$WIDTHTYPE}" style="width: {$LIST_WIDTH}%">
                                        <label class="muted pull-right marginRight10px">Allied</label>
                                    </td>
                                    <td class="fieldValue {$WIDTHTYPE}"
                                        style="width: {$LIST_WIDTH}%;text-align:center;margin:auto"><input
                                                type="radio" name="comp_allied" value="2"
                                                {if $COMPS['allied'] eq 2}checked{/if}></td>
                                    <td class="fieldValue {$WIDTHTYPE}"
                                        style="width: {$LIST_WIDTH}%;text-align:center;margin:auto"><input
                                                type="radio" name="comp_allied" value="1"
                                                {if $COMPS['allied'] eq 1}checked{/if}></td>
                                    <td class="fieldValue {$WIDTHTYPE}"
                                        style="width: {$LIST_WIDTH}%;text-align:center;margin:auto"><input
                                                type="radio" name="comp_allied" value="0"
                                                {if $COMPS['allied'] eq 0}checked{/if}></td>
                                    <td class="fieldLabel {$WIDTHTYPE}" style="width: {$LIST_WIDTH}%">
                                        <label class="muted pull-right marginRight10px">Atlas</label>
                                    </td>
                                    <td class="fieldValue {$WIDTHTYPE}"
                                        style="width: {$LIST_WIDTH}%;text-align:center;margin:auto"><input
                                                type="radio" name="comp_atlas" value="2"
                                                {if $COMPS['atlas'] eq 2}checked{/if}></td>
                                    <td class="fieldValue {$WIDTHTYPE}"
                                        style="width: {$LIST_WIDTH}%;text-align:center;margin:auto"><input
                                                type="radio" name="comp_atlas" value="1"
                                                {if $COMPS['atlas'] eq 1}checked{/if}></td>
                                    <td class="fieldValue {$WIDTHTYPE}"
                                        style="width: {$LIST_WIDTH}%;text-align:center;margin:auto"><input
                                                type="radio" name="comp_atlas" value="0"
                                                {if $COMPS['atlas'] eq 0}checked{/if}></td>
                                </tr>
                                <tr>
                                    <td class="fieldLabel {$WIDTHTYPE}" style="width: {$LIST_WIDTH}%">
                                        <label class="muted pull-right marginRight10px">Mayflower</label>
                                    </td>
                                    <td class="fieldValue {$WIDTHTYPE}"
                                        style="width: {$LIST_WIDTH}%;text-align:center;margin:auto"><input
                                                type="radio" name="comp_mayflower" value="2"
                                                {if $COMPS['mayflower'] eq 2}checked{/if}></td>
                                    <td class="fieldValue {$WIDTHTYPE}"
                                        style="width: {$LIST_WIDTH}%;text-align:center;margin:auto"><input
                                                type="radio" name="comp_mayflower" value="1"
                                                {if $COMPS['mayflower'] eq 1}checked{/if}></td>
                                    <td class="fieldValue {$WIDTHTYPE}"
                                        style="width: {$LIST_WIDTH}%;text-align:center;margin:auto"><input
                                                type="radio" name="comp_mayflower" value="0"
                                                {if $COMPS['mayflower'] eq 0}checked{/if}></td>
                                    <td class="fieldLabel {$WIDTHTYPE}" style="width: {$LIST_WIDTH}%">
                                        <label class="muted pull-right marginRight10px">North American
                                    </td>
                                    <td class="fieldValue {$WIDTHTYPE}"
                                        style="width: {$LIST_WIDTH}%;text-align:center;margin:auto"><input
                                                type="radio" name="comp_northamerican" value="2"
                                                {if $COMPS['north_american'] eq 2}checked{/if}></td>
                                    <td class="fieldValue {$WIDTHTYPE}"
                                        style="width: {$LIST_WIDTH}%;text-align:center;margin:auto"><input
                                                type="radio" name="comp_northamerican" value="1"
                                                {if $COMPS['north_american'] eq 1}checked{/if}></td>
                                    <td class="fieldValue {$WIDTHTYPE}"
                                        style="width: {$LIST_WIDTH}%;text-align:center;margin:auto"><input
                                                type="radio" name="comp_northamerican" value="0"
                                                {if $COMPS['north_american'] eq 0}checked{/if}></td>
                                </tr>
                                <tr>
                                    <td class="fieldLabel {$WIDTHTYPE}" style="width: {$LIST_WIDTH}%">
                                        <label class="muted pull-right marginRight10px">United</label>
                                    </td>
                                    <td class="fieldValue {$WIDTHTYPE}"
                                        style="width: {$LIST_WIDTH}%;text-align:center;margin:auto"><input
                                                type="radio" name="comp_united" value="2"
                                                {if $COMPS['united'] eq 2}checked{/if}></td>
                                    <td class="fieldValue {$WIDTHTYPE}"
                                        style="width: {$LIST_WIDTH}%;text-align:center;margin:auto"><input
                                                type="radio" name="comp_united" value="1"
                                                {if $COMPS['united'] eq 1}checked{/if}></td>
                                    <td class="fieldValue {$WIDTHTYPE}"
                                        style="width: {$LIST_WIDTH}%;text-align:center;margin:auto"><input
                                                type="radio" name="comp_united" value="0"
                                                {if $COMPS['united'] eq 0}checked{/if}></td>
                                    <td class="fieldLabel {$WIDTHTYPE}" style="width: {$LIST_WIDTH}%">
                                        <label class="muted pull-right marginRight10px">Independent</label>
                                    </td>
                                    <td class="fieldValue {$WIDTHTYPE}"
                                        style="width: {$LIST_WIDTH}%;text-align:center;margin:auto"><input
                                                type="radio" name="comp_independent" value="2"
                                                {if $COMPS['independent'] eq 2}checked{/if}></td>
                                    <td class="fieldValue {$WIDTHTYPE}"
                                        style="width: {$LIST_WIDTH}%;text-align:center;margin:auto"><input
                                                type="radio" name="comp_independent" value="1"
                                                {if $COMPS['independent'] eq 1}checked{/if}></td>
                                    <td class="fieldValue {$WIDTHTYPE}"
                                        style="width: {$LIST_WIDTH}%;text-align:center;margin:auto"><input
                                                type="radio" name="comp_independent" value="0"
                                                {if $COMPS['independent'] eq 0}checked{/if}></td>
                                </tr>
                                <tr>
                                    <td class="fieldLabel {$WIDTHTYPE}" style="width: {$LIST_WIDTH}%">
                                        <label class="muted pull-right marginRight10px">Other</label>
                                    </td>
                                    <td class="fieldValue {$WIDTHTYPE}"
                                        style="width: {$LIST_WIDTH}%;text-align:center;margin:auto"><input
                                                type="radio" name="comp_other" value="2"
                                                {if $COMPS['other'] eq 2}checked{/if}></td>
                                    <td class="fieldValue {$WIDTHTYPE}"
                                        style="width: {$LIST_WIDTH}%;text-align:center;margin:auto"><input
                                                type="radio" name="comp_other" value="1"
                                                {if $COMPS['other'] eq 1}checked{/if}></td>
                                    <td class="fieldValue {$WIDTHTYPE}"
                                        style="width: {$LIST_WIDTH}%;text-align:center;margin:auto"><input
                                                type="radio" name="comp_other" value="0"
                                                {if $COMPS['other'] eq 0}checked{/if}></td>
                                    <td class="fieldLabel {$WIDTHTYPE}" style="width: {$LIST_WIDTH}%"></td>
                                    <td class="fieldValue {$WIDTHTYPE}" style="width: {$LIST_WIDTH}%"></td>
                                    <td class="fieldValue {$WIDTHTYPE}" style="width: {$LIST_WIDTH}%"></td>
                                    <td class="fieldValue {$WIDTHTYPE}" style="width: {$LIST_WIDTH}%"></td>
                                </tr>
                                </tbody>
                            </table>
                        </td>
                    </tr>
                {/if}
                {/foreach}
                {* adding additional column for odd number of fields in a block *}
                {assign var=COUNTER value=$COUNTER+1}
                {if $BLOCK_FIELDS|@end eq true and $BLOCK_FIELDS|@count neq 1 and $COUNTER eq 2 && $BLOCK_LABEL neq 'LBL_LEADS_DATES'}
                    <td class="fieldLabel {$WIDTHTYPE}"></td>
                    <td class="{$WIDTHTYPE}"></td>
                {elseif $BLOCK_FIELDS|@end eq true and $BLOCK_FIELDS|@count neq 1 and $COUNTER eq 2 && $BLOCK_LABEL eq 'LBL_LEADS_EMPLOYERASSISTING'}
                    <td class="fieldLabel {$WIDTHTYPE}"></td>
                    <td class="{$WIDTHTYPE}"></td>
                {/if}
                {if $BLOCK_FIELDS|@end eq true && $BLOCK_LABEL eq 'LBL_LEADS_DATES'}
                    {for $iteration=0 to 3-$COUNTER}
                        <td class="fieldLabel {$WIDTHTYPE}"></td>
                        <td class="{$WIDTHTYPE}"></td>
                    {/for}
                {/if}
                </tr>

                {if $BLOCK_LABEL eq 'LBL_LEADS_INFORMATION' && 'move_type'|array_key_exists:$BLOCK_FIELDS}
                    <tr class="hide">
                        <td class="fieldLabel">
                            <label class="muted pull-right marginRight10px">{if $BLOCK_FIELDS['business_line']->isMandatory() eq true}
                                    <span class="redColor">*</span>
                                {/if}{vtranslate($BLOCK_FIELDS['business_line']->get('label'), $MODULE)}</label>
                        </td>
                        <td class="fieldValue">
                            {assign var="FIELD_INFO" value=Zend_Json::encode($BLOCK_FIELDS['business_line']->getFieldInfo())}
                            {assign var=PICKLIST_VALUES value=$BLOCK_FIELDS['business_line']->getPicklistValues()}
                            {assign var="SPECIAL_VALIDATOR" value=$BLOCK_FIELDS['business_line']->getValidator()}
                            {if $BLOCK_FIELDS['business_line']->get('name') eq {$BLFIELD}}
                            <select onchange="loadBlocksByBusinesLine('{$MODULE}', '{$BLFIELD}')"
                                    class="chzn-select {if $OCCUPY_COMPLETE_WIDTH} row-fluid {/if}"
                                    name="{$BLOCK_FIELDS['business_line']->getFieldName()}"
                                    data-validation-engine="validate[{if $BLOCK_FIELDS['business_line']->isMandatory() eq true} required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]"
                                    data-fieldinfo='{$FIELD_INFO|escape}'
                                    {if !empty($SPECIAL_VALIDATOR)}data-validator='{Zend_Json::encode($SPECIAL_VALIDATOR)}'{/if}
                                    data-selected-value='{$BLOCK_FIELDS['business_line']->get('fieldvalue')}'>
                                {else}
                                <select class="chzn-select {if $OCCUPY_COMPLETE_WIDTH} row-fluid {/if}"
                                        name="{$BLOCK_FIELDS['business_line']->getFieldName()}"
                                        data-validation-engine="validate[{if $BLOCK_FIELDS['business_line']->isMandatory() eq true} required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]"
                                        data-fieldinfo='{$FIELD_INFO|escape}'
                                        {if !empty($SPECIAL_VALIDATOR)}data-validator='{Zend_Json::encode($SPECIAL_VALIDATOR)}'{/if}
                                        data-selected-value='{$BLOCK_FIELDS['business_line']->get('fieldvalue')}'>
                                    {/if}
                                    {if $BLOCK_FIELDS['business_line']->isEmptyPicklistOptionAllowed()}
                                        <option value="">{vtranslate('LBL_SELECT_OPTION','Vtiger')}</option>{/if}
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
            {if $BLOCK_LABEL eq 'LBL_LEADS_DATES' && $MOVEROLES_MODULE_MODEL&& $MOVEROLES_MODULE_MODEL->isActive()}
                {include file=vtemplate_path('GuestEditBlocks.tpl', $MODULE)}

                {*{include file=vtemplate_path('MoveRolesEdit.tpl', 'MoveRoles')}*}
            {/if}
            {include file=vtemplate_path('SequencedGuestEditBlocks.tpl', $MODULE) BLOCK_LABEL=$BLOCK_LABEL}
        {/foreach}

        {if $RECORD_ID neq ''}
            <table class="table table-bordered blockContainer showInlineTable equalSplit">
                <thead>
                <tr>
                    <th class="blockHeader" colspan="4">{vtranslate('ModComments',$MODULE_NAME)}</th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td class="fieldValue {$WIDTHTYPE}" colspan="4">
                        {include file='ShowAllComments.tpl'|@vtemplate_path}
                    </td>
                </tr>
                </tbody>
            </table>
        {/if}
        {/strip}
