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
{assign var="FIELD_INFO_RAW" value=$FIELD_MODEL->getFieldInfo()}
{assign var="FIELD_INFO" value=Zend_Json::encode($FIELD_INFO_RAW)}
{if $FIELD_MODEL->get('name') neq 'sales_person'}
	{*assign var=PICKLIST_VALUES value=$FIELD_MODEL->getPicklistValues()*}
	{assign var=PICKLIST_VALUES value=$FIELD_INFO_RAW['picklistvalues']}
{else}
	{assign var=PICKLIST_VALUES value=$USER_MODEL->getAccessibleSalesPeople()}
	{assign var=FIELD_VALUE value=$FIELD_MODEL->get('fieldvalue')}
	{if !array_key_exists($FIELD_VALUE, $PICKLIST_VALUES)}
		{*assign var=SAVED_OWNER_RECORD value=Users_Record_Model::getInstanceById($FIELD_VALUE,'Users')*}
		{$PICKLIST_VALUES[$FIELD_VALUE] = Users_Record_Model::getDisplaynameById($FIELD_VALUE)}
	{/if}
{/if}
{assign var="SPECIAL_VALIDATOR" value=$FIELD_MODEL->getValidator()}
{if $FIELD_MODEL->get('name') eq {$BLFIELD}}
	<select onchange="loadBlocksByBusinesLine('{$MODULE}', '{$BLFIELD}')" class="chzn-select {if $OCCUPY_COMPLETE_WIDTH} row-fluid {/if}" name="{$FIELD_MODEL->getFieldName()}" data-validation-engine="validate[{if $FIELD_MODEL->isMandatory() eq true} required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" data-fieldinfo='{$FIELD_INFO|escape}' {if !empty($SPECIAL_VALIDATOR)}data-validator='{Zend_Json::encode($SPECIAL_VALIDATOR)}'{/if} data-selected-value='{$FIELD_MODEL->get('fieldvalue')}' {if $FIELD_MODEL->get('disabled')}disabled{/if}>
{else}
	<select {if $DEFAULT_CHZN eq 1}id="{$FIELD_MODEL->getFieldName()}" {/if}class="{if $DEFAULT_CHZN eq 0}chzn-select {/if}{if $OCCUPY_COMPLETE_WIDTH} row-fluid {/if}" name="{$FIELD_MODEL->getFieldName()}" data-validation-engine="validate[{if $FIELD_MODEL->isMandatory() eq true} required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" data-fieldinfo='{$FIELD_INFO|escape}' {if !empty($SPECIAL_VALIDATOR)}data-validator='{Zend_Json::encode($SPECIAL_VALIDATOR)}'{/if} data-selected-value='{$FIELD_MODEL->get('fieldvalue')}' {if $FIELD_MODEL->get('disabled')}disabled{/if}>
{/if}
	{if $FIELD_MODEL->isEmptyPicklistOptionAllowed()}<option value="">{vtranslate('LBL_SELECT_OPTION','Vtiger')}</option>{/if}
	{foreach item=PICKLIST_VALUE key=PICKLIST_NAME from=$PICKLIST_VALUES}
		{if $FIELD_MODEL->get('name') eq 'business_line' && $PICKLIST_NAME eq 'Auto Transportation' && !$VEHICLE_LOOKUP}{continue}{/if}
        <option value="{Vtiger_Util_Helper::toSafeHTML($PICKLIST_NAME)}" {if (trim(decode_html($FIELD_MODEL->get('fieldvalue'))) eq trim($PICKLIST_NAME)) OR ($RECORD_ID eq '' && $PICKLIST_NAME eq $USER_MODEL->getId())} selected {/if}>
			{if $FIELD_MODEL->get('name') eq 'agentcompgr_type'}
				{vtranslate($PICKLIST_VALUE, $PICKLIST_VALUE)}
			{else}
				{$PICKLIST_VALUE}
			{/if}
		</option>
    {/foreach}
</select>
{/strip}
