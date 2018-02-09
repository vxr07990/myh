

{strip}
    {assign var="FIELD_INFO" value=Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($FIELD_MODEL->getFieldInfo()))}
    {*{assign var="SPECIAL_VALIDATOR" value=$FIELD_MODEL->getValidator()}*}
    {assign var="dateFormat" value=$USER_MODEL->get('date_format')}
    <div class="input-append row-fluid">
	<div class="span12 row-fluid date">
		{assign var=FIELD_NAME value=$FIELD_MODEL->get('name')}
        <input id="{$MODULE}_editView_fieldName_{$FIELD_NAME}" type="text" class="dateField" name="{$FIELD_MODEL->getFieldName()}" data-date-format="{$dateFormat}"
               type="text" value="{$FIELD_MODEL->getEditViewDisplayValue($FIELD_MODEL->get('fieldvalue'))}" data-validation-engine="validate[{if $FIELD_MODEL->isMandatory() eq true} required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]] {if $FIELD_NAME eq 'end_date'}validate[funcCall[Vtiger_greaterThanDependentField_Validator_Js.invokeValidation]]{/if}" data-fieldinfo='{$FIELD_INFO}'
                {if $MODE eq 'edit' && $FIELD_NAME eq 'due_date'} data-user-changed-time="true" {/if} />
		<span class="add-on"><i class="icon-calendar"></i></span>
	</div>
</div>
{/strip}















