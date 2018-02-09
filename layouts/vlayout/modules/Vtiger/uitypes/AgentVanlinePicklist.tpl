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
    {assign var=PICKLIST_VALUES value=$USER_MODEL->getAccessibleOwnersForUser($MODULE,true)}
    {assign var=AGENTS_USER_REL value=$USER_MODEL->getAgentUserRel()}
    {assign var=SPECIAL_VALIDATOR value=$FIELD_MODEL->getValidator()}
    {if $FIELD_MODEL->get('fieldvalue')}
        {assign var=SELECTED value=$FIELD_MODEL->get('fieldvalue')}
		{assign var=FIELD_VALUE value=$FIELD_MODEL->get('fieldvalue')}
		{if !array_key_exists($FIELD_VALUE, $PICKLIST_VALUES)}
			{assign var=SAVED_OWNER_RECORD value=Vtiger_Record_Model::getInstanceById($FIELD_VALUE,'AgentManager')}
			{$PICKLIST_VALUES[$FIELD_VALUE] = $SAVED_OWNER_RECORD->getDisplayName()}
		{/if}
    {else}
        {assign var=SELECTED value=$USER_MODEL->getPrimaryOwnerForUser()}
    {/if}
    <select {if $DEFAULT_CHZN eq 1}id="{$FIELD_MODEL->getFieldName()}"{/if} class="{if $DEFAULT_CHZN eq 0}chzn-select {/if}{if $OCCUPY_COMPLETE_WIDTH} row-fluid {/if}" name="{$FIELD_MODEL->getFieldName()}" data-validation-engine="validate[{if $FIELD_MODEL->isMandatory() eq true} required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" data-fieldinfo='{$FIELD_INFO|escape}' {if !empty($SPECIAL_VALIDATOR)}data-validator='{Zend_Json::encode($SPECIAL_VALIDATOR)}'{/if} data-selected-value='{$FIELD_MODEL->get('fieldvalue')}' {if $FIELD_MODEL->get('disabled')}disabled{/if}>
        {foreach item=AGENT_NAME key=AGENT_ID from=$PICKLIST_VALUES}
            {if $AGENT_NAME == 'agents'}
                <optgroup label="Agents">
                {continue}
            {elseif $AGENT_NAME == 'vanlines'}
                </optgroup><optgroup label="Vanlines">
                {continue}
            {/if}
            <option value="{$AGENT_ID}" {if $SELECTED eq $AGENT_ID} selected {/if}>{$FIELD_MODEL->getUITypeModel()->getAgentDisplay($AGENT_ID)}</option>
        {/foreach}
        </optgroup>
    </select>
    {if !empty($AGENTS_USER_REL) && $AGENTS_USER_REL neq ''}
        <input type="hidden" name="agentUserRel" value='{Vtiger_Util_Helper::toSafeHTML($AGENTS_USER_REL)}' />
    {/if} 
{/strip}