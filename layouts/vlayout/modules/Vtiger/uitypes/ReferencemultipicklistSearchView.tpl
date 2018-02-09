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
    {assign var=FIELD_NAME value=$FIELD_MODEL->get('name')}
    {assign var=FIELD_MODEL value=$FIELD_MODEL->set('fieldvalue',$SEARCH_INFO['searchValue'])}

    {assign var="REFERENCE_LIST" value=$FIELD_MODEL->getReferenceList()}
    {assign var="REFERENCE_LIST_COUNT" value=count($REFERENCE_LIST)}
    {assign var="FIELD_INFO" value=Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($FIELD_MODEL->getFieldInfo()))}
    {if {$REFERENCE_LIST_COUNT} eq 1}
        {assign var="REFERENCED_MODULE_NAME" value=$REFERENCE_LIST[0]}
    {/if}
    {if {$REFERENCE_LIST_COUNT} gt 1}
        {assign var="REFERENCED_MODULE_STRUCT" value=$FIELD_MODEL->getUITypeModel()->getReferenceModule($DISPLAYID)}
        {if !empty($REFERENCED_MODULE_STRUCT) AND in_array($REFERENCED_MODULE_STRUCT->get('name'), $REFERENCE_LIST)}
            {assign var="REFERENCED_MODULE_NAME" value=$REFERENCED_MODULE_STRUCT->get('name')}
        {else}
            {assign var="REFERENCED_MODULE_NAME" value=$REFERENCE_LIST[0]}
        {/if}
    {/if}

    {assign var=VIEW_NAME value={getPurifiedSmartyParameters('view')}}
    <input id="{$MODULE}_{$VIEW_NAME}_fieldName_{$FIELD_MODEL->get('name')}"  name="{$FIELD_MODEL->getFieldName()}" type="text" class="row-fluid autoComplete listSearchContributor select2"
           value="{$SEARCH_INFO['searchValue']}" data-fieldinfo='{$FIELD_INFO}'>
{/strip}
