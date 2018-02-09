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
    {assign var=FIELD_INFO value=Zend_Json::encode($FIELD_MODEL->getFieldInfo())}
    {assign var=SPECIAL_VALIDATOR value=$FIELD_MODEL->getValidator()}
    {if $FIELD_MODEL->get('fieldvalue')}
        {assign var=SELECTED value=$FIELD_MODEL->get('fieldvalue')}
		{assign var=FIELD_VALUE value=$FIELD_MODEL->get('fieldvalue')}
		{if !array_key_exists($FIELD_VALUE, $TARIFF_LIST)}
			{assign var=SAVED_OWNER_RECORD value=Vtiger_Record_Model::getInstanceById($FIELD_VALUE,'TariffManager')}
            {if !$SAVED_OWNER_RECORD->get('tariffmanagername')}
                {assign var=SAVED_OWNER_RECORD value=Vtiger_Record_Model::getInstanceById($FIELD_VALUE,'Tariffs')}
            {/if}
			{$TARIFF_LIST[$FIELD_VALUE] = $SAVED_OWNER_RECORD->getDisplayName()}
		{/if}
    {/if}
    <select class="chzn-select {if $OCCUPY_COMPLETE_WIDTH} row-fluid {/if}" name="{$FIELD_MODEL->getFieldName()}" data-validation-engine="validate[{if $FIELD_MODEL->isMandatory() eq true} required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" data-fieldinfo='{$FIELD_INFO|escape}' {if !empty($SPECIAL_VALIDATOR)}data-validator='{Zend_Json::encode($SPECIAL_VALIDATOR)}'{/if} data-selected-value='{$FIELD_MODEL->get('fieldvalue')}' {if $FIELD_MODEL->get('disabled')}disabled{/if}>
        <option value="">{vtranslate('LBL_SELECT_OPTION','Vtiger')}</option>
        {foreach item=TARIFF_NAME key=TARIFF_ID from=$TARIFF_LIST}
            <option value="{$TARIFF_ID}" {if $SELECTED eq $TARIFF_ID} selected {/if}>{$FIELD_MODEL->getUITypeModel()->getTariffDisplay($TARIFF_ID)}</option>
        {/foreach}
        </optgroup>
    </select>
{/strip}