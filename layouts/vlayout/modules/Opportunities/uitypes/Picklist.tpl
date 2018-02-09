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
    {if $RECORD_MODEL}
	   {assign var=PICKLIST_VALUES value=$RECORD_MODEL->getSalesPeopleByOwner($SALESPERSON_OWNER)}
    {/if}
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
	<select class="chzn-select {if $OCCUPY_COMPLETE_WIDTH} row-fluid {/if}" name="{$FIELD_MODEL->getFieldName()}" data-validation-engine="validate[{if $FIELD_MODEL->isMandatory() eq true} required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" data-fieldinfo='{$FIELD_INFO|escape}' {if !empty($SPECIAL_VALIDATOR)}data-validator='{Zend_Json::encode($SPECIAL_VALIDATOR)}'{/if} data-selected-value='{$FIELD_MODEL->get('fieldvalue')}' {if $FIELD_MODEL->get('disabled')}disabled{/if}>
{/if}
	{if $FIELD_MODEL->isEmptyPicklistOptionAllowed()}<option value="">{vtranslate('LBL_SELECT_OPTION','Vtiger')}</option>{/if}
    {* make sure the value is vtranslated *}
    {* ahh... so this shouldn't be done here, because the PICKLIST_VALUE is the vtranslate value.
    {assign var=FIELD_VALUE value=$FIELD_MODEL->set('fieldvalue', vtranslate($FIELD_MODEL->get('fieldvalue'), $MODULE_NAME))}
    *}
	{foreach item=PICKLIST_VALUE key=PICKLIST_NAME from=$PICKLIST_VALUES}
		{if $FIELD_MODEL->get('name') eq 'business_line' && $PICKLIST_NAME eq 'Auto Transportation' && !$VEHICLE_LOOKUP}{continue}{/if}
		{if $FIELD_MODEL->get('name') eq 'sales_stage'}
			{* OT1884 for searching *}
			{* added to drop This from the picklist pull down *}
			{if (getenv('INSTANCE_NAME') eq 'sirva') && ($PICKLIST_VALUE eq 'Negotiation or Review')}
				{continue}
            {/if}
			{if (getenv('INSTANCE_NAME') eq 'graebel') &&($PICKLIST_VALUE eq 'Negotiation or Review' || $PICKLIST_VALUE eq 'Perception Analysis')}
				{continue}
            {/if}
		{/if}
		<option value="{Vtiger_Util_Helper::toSafeHTML($PICKLIST_NAME)}" {if (trim(decode_html($FIELD_MODEL->get('fieldvalue'))) eq trim($PICKLIST_NAME)) OR ($RECORD_ID eq '' && $PICKLIST_NAME eq $USER_MODEL->getId())} selected {/if}>{$PICKLIST_VALUE}</option>
    {/foreach}
</select>
{/strip}
