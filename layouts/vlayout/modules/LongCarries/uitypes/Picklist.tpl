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
{assign var="FIELD_INFO" value=Zend_Json::encode($FIELD_MODEL->getFieldInfo())}
{assign var=PICKLIST_VALUES value=$FIELD_MODEL->getPicklistValues()}
{assign var=FIELD_VALUE value=$FIELD_MODEL->get('fieldvalue')}
{assign var=FIELD_NAME value=$FIELD_MODEL->get('name')}

{assign var="SPECIAL_VALIDATOR" value=$FIELD_MODEL->getValidator()}
<select {if $DEFAULT_CHZN eq 1}id="{$FIELD_MODEL->getFieldName()}" {/if}class="{if $DEFAULT_CHZN eq 0}chzn-select {/if}{if $OCCUPY_COMPLETE_WIDTH} row-fluid {/if}" style="width: 150px;"
	name="{$FIELD_MODEL->getFieldName()}" data-validation-engine="validate[{if $FIELD_MODEL->isMandatory() eq true} required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" data-fieldinfo='{$FIELD_INFO|escape}' {if !empty($SPECIAL_VALIDATOR)}data-validator='{Zend_Json::encode($SPECIAL_VALIDATOR)}'{/if} data-selected-value='{$FIELD_VALUE}' {if $FIELD_MODEL->get('disabled')}disabled{/if}>
	{if $FIELD_MODEL->isEmptyPicklistOptionAllowed()}<option value="">{vtranslate('LBL_SELECT_OPTION','Vtiger')}</option>{/if}
	{foreach item=PICKLIST_VALUE key=PICKLIST_NAME from=$PICKLIST_VALUES}
		{if $FIELD_NAME eq 'addresssegments_origin'}
			{if in_array($PICKLIST_NAME,$FROMLOCATIONTYPE)}
				{if $EXTRASTOP_LOCATIONTYPE|count >0}
					{if in_array($PICKLIST_NAME,$EXTRASTOP_LOCATIONTYPE)}
						<option value="{Vtiger_Util_Helper::toSafeHTML($PICKLIST_NAME)}" {if (trim(decode_html($FIELD_VALUE)) eq trim($PICKLIST_NAME))} selected {/if}>{$PICKLIST_VALUE}</option>
					{/if}
				{else}
					<option value="{Vtiger_Util_Helper::toSafeHTML($PICKLIST_NAME)}" {if (trim(decode_html($FIELD_VALUE)) eq trim($PICKLIST_NAME))} selected {/if}>{$PICKLIST_VALUE}</option>
				{/if}
			{/if}

		{elseif $FIELD_NAME eq 'addresssegments_destination'}
			{if in_array($PICKLIST_NAME,$TOLOCATIONTYPE)}
				{if $EXTRASTOP_LOCATIONTYPE|count >0}
					{if in_array($PICKLIST_NAME,$EXTRASTOP_LOCATIONTYPE)}
						<option value="{Vtiger_Util_Helper::toSafeHTML($PICKLIST_NAME)}" {if (trim(decode_html($FIELD_VALUE)) eq trim($PICKLIST_NAME))} selected {/if}>{$PICKLIST_VALUE}</option>
					{/if}
				{else}
					<option value="{Vtiger_Util_Helper::toSafeHTML($PICKLIST_NAME)}" {if (trim(decode_html($FIELD_VALUE)) eq trim($PICKLIST_NAME))} selected {/if}>{$PICKLIST_VALUE}</option>
				{/if}
			{/if}
		{else}
			<option value="{Vtiger_Util_Helper::toSafeHTML($PICKLIST_NAME)}" {if (trim(decode_html($FIELD_VALUE)) eq trim($PICKLIST_NAME))} selected {/if}>{$PICKLIST_VALUE}</option>
		{/if}

    {/foreach}
</select>
{/strip}