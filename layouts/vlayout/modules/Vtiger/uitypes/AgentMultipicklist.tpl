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
    {assign var="FIELD_INFO" value=Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($FIELD_MODEL->getFieldInfo()))}
    {assign var="SPECIAL_VALIDATOR" value=$FIELD_MODEL->getValidator()}
    {assign var=ALL_ACTIVEAGENT_LIST value=$USER_MODEL->getAccessibleAgents()}
    {assign var=VANLINE_LIST value=$USER_MODEL->getVanlines()}
    {assign var=FIELD_NAME value=$FIELD_MODEL->get('name')}
    {assign var=CURRENT_USER_ID value=$USER_MODEL->get('id')}
    {assign var=FIELD_VALUE value=explode(' |##| ',$FIELD_MODEL->get('fieldvalue'))}
    {assign var=VANLINE_ARRAY value=json_decode($VANLINE_LIST,true)}
    <input type="hidden" name="agent_ids_order" value="{implode(',',$FIELD_VALUE)}">
    <input type="hidden" name="vanline_list" value="{Vtiger_Util_Helper::toSafeHTML($VANLINE_LIST)}">
    <input type="hidden" name="agent_list" value="{Vtiger_Util_Helper::toSafeHTML(json_encode($FIELD_MODEL->getUITypeModel()->getAgentDisplay($ALL_ACTIVEAGENT_LIST)))}">

    <select style="width: 220px;" id="agent_ids" class="{$FIELD_NAME} select2"data-name="{$FIELD_NAME}" name="{$FIELD_NAME}[]" multiple data-validation-engine="validate[{if $FIELD_MODEL->isMandatory() eq true} required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" data-fieldinfo='{*TODO: probably shouldn't escape like this*}{$FIELD_INFO|escape}' {if !empty($SPECIAL_VALIDATOR)}data-validator='{Zend_Json::encode($SPECIAL_VALIDATOR)}'{/if}>
        {foreach key=AGENT_ID item=AGENT_NAME from=$ALL_ACTIVEAGENT_LIST}
            <option value="{$AGENT_ID}" data-picklistvalue= '{$AGENT_NAME}'{foreach item=AGENT_ID_S from=$FIELD_VALUE}{if $AGENT_ID_S eq $AGENT_ID } selected {/if}{/foreach}
                    data-userId="{$CURRENT_USER_ID}">
                {$AGENT_NAME}
            </option>
        {/foreach}
        {foreach key=AGENT_ID item=AGENT_NAME from=$VANLINE_ARRAY}
            <option value="{$AGENT_ID}" data-picklistvalue= '{$AGENT_NAME}'{foreach item=AGENT_ID_S from=$FIELD_VALUE}{if $AGENT_ID_S eq $AGENT_ID } selected {/if}{/foreach}
                    data-userId="{$CURRENT_USER_ID}">
                {$AGENT_NAME}
            </option>
        {/foreach}


        {foreach key=AGENT_ID item=AGENT_NAME from=$VANLINE_ARRAY}
            <option value="{$AGENT_ID}" data-picklistvalue= '{$AGENT_NAME}'{foreach item=AGENT_ID_S from=$FIELD_VALUE}{if $AGENT_ID_S eq $AGENT_ID } selected {/if}{/foreach}
                    data-userId="{$CURRENT_USER_ID}">
                {$AGENT_NAME}
            </option>
        {/foreach}

    </select>
{/strip}
