{strip}
    {* Get Time zone from Users module*}
    {assign var=USER_MODULE_MODEL value=$USER_MODEL->getModule()}
    {assign var=TIMEZONE_FIELD_MODEL value=$USER_MODULE_MODEL->getField('time_zone')}
    {assign var="FIELD_INFO" value=Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($FIELD_MODEL->getFieldInfo()))}
    {assign var="SPECIAL_VALIDATOR" value=$FIELD_MODEL->getValidator()}
    {assign var="FIELD_NAME" value=$FIELD_MODEL->get('name')}
    {assign var="TIME_FORMAT" value=$USER_MODEL->get('hour_format')}
    {assign var="dateFormat" value=$USER_MODEL->get('date_format')}
    <input {if $FIELD_MODEL->isReadOnly()} readonly {/if} id="{$MODULE}_editView_fieldName_{$FIELD_NAME}" type="text" class="dateTimeField" name="{$FIELD_MODEL->getFieldName()}" data-date-format="{$dateFormat}" data-time-format="{$TIME_FORMAT}" style="width: 150px" value="{$FIELD_MODEL->getEditViewDisplayValue($FIELD_MODEL->get('fieldvalue'))}" data-validation-engine="validate[{if $FIELD_MODEL->isMandatory() eq true} required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]"   {if !empty($SPECIAL_VALIDATOR)}data-validator='{Zend_Json::encode($SPECIAL_VALIDATOR)}'{/if} data-fieldinfo='{$FIELD_INFO}'/>

    {if $TIMEZONE_FIELD_MODEL}
        {assign var=PICKLIST_VALUES value=$TIMEZONE_FIELD_MODEL->getPicklistValues()}
        {if $RECORD_ID neq ''}
            {assign var=RECORD_MODEL value=Vtiger_Record_Model::getInstanceById($RECORD_ID)}
        {else}
            {assign var=RECORD_MODEL value=Vtiger_Record_Model::getCleanInstance($MODULE)}
        {/if}
        {assign var=MODULE_MODEL value=$RECORD_MODEL->getModule()}
        {assign var=ZONE_FIELD_NAME value=$FIELD_NAME|cat:'_zone'}
        {assign var=DATETIMEZONE_FIELD_MODEL value=$MODULE_MODEL->getField($ZONE_FIELD_NAME)}
        {if $RECORD_MODEL->get($ZONE_FIELD_NAME)}
            {assign var=DATETIMEZONE_FIELD_MODEL value=$DATETIMEZONE_FIELD_MODEL->set('fieldvalue', $RECORD_MODEL->get($ZONE_FIELD_NAME))}
        {else}
            {assign var=DATETIMEZONE_FIELD_MODEL value=$DATETIMEZONE_FIELD_MODEL->set('fieldvalue', $USER_MODEL->get('time_zone'))}
        {/if}
        <select class="chzn-select" name="{$ZONE_FIELD_NAME}" data-validation-engine="validate[funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" >
            <option value="">{vtranslate('LBL_SELECT_OPTION','Vtiger')}</option>
            {foreach item=PICKLIST_VALUE key=PICKLIST_NAME from=$PICKLIST_VALUES}
                <option value="{Vtiger_Util_Helper::toSafeHTML($PICKLIST_NAME)}" {if trim(decode_html($DATETIMEZONE_FIELD_MODEL->get('fieldvalue'))) eq trim($PICKLIST_NAME)} selected {/if}>{$PICKLIST_VALUE}</option>
            {/foreach}
        </select>&nbsp;
    {/if}

{/strip}