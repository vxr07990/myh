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
{* Get Time zone from Users module*}
{assign var=USER_MODULE_MODEL value=$USER_MODEL->getModule()}
{assign var=TIMEZONE_FIELD_MODEL value=$USER_MODULE_MODEL->getField('time_zone')}
{assign var="FIELD_INFO" value=Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($FIELD_MODEL->getFieldInfo()))}
{assign var="SPECIAL_VALIDATOR" value=$FIELD_MODEL->getValidator()}
{assign var=FIELD_VALUE value=$FIELD_MODEL->getEditViewDisplayValue($FIELD_MODEL->get('fieldvalue'))}
{assign var="TIME_FORMAT" value=$USER_MODEL->get('hour_format')}

	{if $TIMEZONE_FIELD_MODEL}
        {assign var=RELATED_DATE_FIELD value=$FIELD_MODEL->getRelatedDateField()}
		{assign var=PICKLIST_VALUES value=$TIMEZONE_FIELD_MODEL->getTimezoneValues($FIELD_MODEL, $RECORD)}
        {* Special case for survey_time which is linked to survey_date and is currently handled separately *}
		{if $RECORD_ID neq ''}
			{assign var=TIMEZONE_VALUE value=getFieldTimeZoneValue($FIELD_MODEL->getFieldName(), $RECORD_ID)}
            {*{if $TIMEZONE_VALUE}*}
                {*{assign var=DATETIME_MODEL value=DateTimeField::convertTimeZone($FIELD_VALUE, DateTimeField::getDBTimeZone(), $TIMEZONE_VALUE)}*}
                {*{assign var=FIELD_VALUE value=Vtiger_Time_UIType::getTimeValueInAMorPM($DATETIME_MODEL->format("H:i:s"))}*}
            {*{/if}*}

		{else}
			{assign var=TIMEZONE_VALUE value=$USER_MODEL->get('time_zone')}
		{/if}

        {if $RELATED_DATE_FIELD neq ''}
            <input type="hidden" class="dateTimeField" data-fieldname="{$FIELD_MODEL->getFieldName()}" value="{$RELATED_DATE_FIELD}" />
        {/if}
        <input type="hidden" name="time_fields[]" value="{$FIELD_MODEL->getFieldName()}">
        <input {if $FIELD_MODEL->isReadOnly()} readonly {/if} id="{$MODULE}_editView_fieldName_{$FIELD_MODEL->get('name')}" type="text" data-format="{$TIME_FORMAT}" class="timepicker-default input-small" value="{$FIELD_MODEL->getDisplayValue($FIELD_VALUE, $RECORD_ID, $RECORD)}" name="{$FIELD_MODEL->getFieldName()}" style="width: 100px"
                                                              data-validation-engine="validate[{if $FIELD_MODEL->isMandatory() eq true} required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]"   {if !empty($SPECIAL_VALIDATOR)}data-validator='{Zend_Json::encode($SPECIAL_VALIDATOR)}'{/if} data-fieldinfo='{$FIELD_INFO}' />

		<select class="chzn-select" name="timefield_{$FIELD_MODEL->getFieldName()}" data-validation-engine="validate[funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" >
		<option value="">{vtranslate('LBL_SELECT_OPTION','Vtiger')}</option>
		{foreach item=PICKLIST_VALUE key=PICKLIST_NAME from=$PICKLIST_VALUES}
			<option value="{Vtiger_Util_Helper::toSafeHTML($PICKLIST_NAME)}" {if trim(decode_html($TIMEZONE_VALUE)) eq trim($PICKLIST_NAME)} selected {/if}>{$PICKLIST_VALUE}</option>
		{/foreach}
	</select>&nbsp;
    {else}
        <div class="input-append time">
            <input {if $FIELD_MODEL->isReadOnly()} readonly {/if} id="{$MODULE}_editView_fieldName_{$FIELD_MODEL->get('name')}" type="text" data-format="{$TIME_FORMAT}" class="timepicker-default input-small" value="{$FIELD_VALUE}" name="{$FIELD_MODEL->getFieldName()}" style="width: 100px"
                                                                  data-validation-engine="validate[{if $FIELD_MODEL->isMandatory() eq true} required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]"   {if !empty($SPECIAL_VALIDATOR)}data-validator='{Zend_Json::encode($SPECIAL_VALIDATOR)}'{/if} data-fieldinfo='{$FIELD_INFO}' />
            {if !$FIELD_MODEL->isReadOnly()}
            <span class="add-on cursorPointer">
                <i class="icon-time"></i>
            </span>
            {/if}
        </div>
	{/if}
{/strip}
