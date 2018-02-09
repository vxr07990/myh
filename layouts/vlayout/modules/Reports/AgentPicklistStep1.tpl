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
    {*{assign var=FIELD_INFO value=Zend_Json::encode($FIELD_MODEL->getFieldInfo())}*}
    {assign var=PICKLIST_VALUES value=$USER_MODEL->getAccessibleOwnersForUser($MODULE)}
    {assign var=AGENTS_USER_REL value=$USER_MODEL->getAgentUserRel()}
    {*{assign var=SPECIAL_VALIDATOR value=$FIELD_MODEL->getValidator()}*}
    {if $REPORT_MODEL->get('agentid')}
        {assign var=SELECTED value=$REPORT_MODEL->get('agentid')}
        {assign var=FIELD_VALUE value=$REPORT_MODEL->get('agentid')}
        {if !array_key_exists($FIELD_VALUE, $PICKLIST_VALUES)}
            {assign var=SAVED_OWNER_RECORD value=Vtiger_Record_Model::getInstanceById($FIELD_VALUE,'AgentManager')}
            {$PICKLIST_VALUES[$FIELD_VALUE] = $SAVED_OWNER_RECORD->getDisplayName()}
        {/if}
    {else}
        {foreach item=REPORT_FOLDER from=$REPORT_FOLDERS}
            {assign var=SELECTED value=$REPORT_FOLDER->get('agentid')}
        {/foreach}
    {/if}
    <select id="agentid" class="chzn-select" name="agentid" data-validation-engine="validate[ required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" data-selected-value='{$REPORT_MODEL->get('agentid')}'>
        {foreach item=AGENT_NAME key=AGENT_ID from=$PICKLIST_VALUES}
        {if $AGENT_NAME == 'agents'}
        <optgroup label="Agents">
            {continue}
            {elseif $AGENT_NAME == 'vanlines'}
        </optgroup><optgroup label="Vanlines">
            {continue}
            {/if}
            <option value="{$AGENT_ID}" {if $SELECTED eq $AGENT_ID} selected {/if}>{$AGENT_NAME}</option>
            {/foreach}
        </optgroup>
    </select>
    {if !empty($AGENTS_USER_REL) && $AGENTS_USER_REL neq ''}
        <input type="hidden" name="agentUserRel" value='{Vtiger_Util_Helper::toSafeHTML($AGENTS_USER_REL)}' />
    {/if}
{/strip}