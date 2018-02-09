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
    {assign var=PICKLIST_VALUES value=$USER_MODEL->getAccessibleOwnersForUser($MODULE, true)}
    {assign var=SEARCH_VALUES value=explode(',',$SEARCH_INFO['searchValue'])}
    <div class="row-fluid">
        <select class="select2 listSearchContributor span9" name="{$FIELD_MODEL->get('name')}" multiple style="width:150px;" data-fieldinfo='{$FIELD_INFO|escape}'>
            {foreach item=AGENT_NAME key=AGENT_ID from=$PICKLIST_VALUES}
            {if $AGENT_NAME == 'agents'}
            <optgroup label="Agents">
                {continue}
                {elseif $AGENT_NAME == 'vanlines'}
            </optgroup><optgroup label="Vanlines">
                {continue}
                {/if}
                <option value="{$AGENT_ID}" {if in_array($AGENT_ID,$SEARCH_VALUES) && ($AGENT_ID neq "") } selected{/if}>{$FIELD_MODEL->getUITypeModel()->getAgentDisplay($AGENT_ID)}</option>
                {/foreach}
        </select>
    </div>
{/strip}
