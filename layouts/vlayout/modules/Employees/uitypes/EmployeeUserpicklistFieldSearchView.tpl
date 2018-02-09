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
    {assign var=PICKLIST_VALUES value=$USER_MODEL->getAccessibleUsers()}
    {assign var=SEARCH_VALUES value=explode(',',$SEARCH_INFO['searchValue'])}
    <div class="row-fluid">
        <select class="select2 listSearchContributor span9" name="{$FIELD_MODEL->get('name')}" multiple style="width:150px;" data-fieldinfo='{$FIELD_INFO|escape}'>
           <option value="0"></option>
			{foreach item=USER_NAME key=USER_ID from=$PICKLIST_VALUES}
				<option value="{$USER_ID}" {if in_array($USER_ID,$SEARCH_VALUES) && ($USER_ID neq "") } selected{/if}>{$USER_NAME}</option>
			{/foreach}
        </select>
    </div>
{/strip}
