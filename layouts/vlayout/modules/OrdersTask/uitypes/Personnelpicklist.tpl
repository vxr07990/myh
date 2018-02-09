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
    {assign var="FIELD_INFO" value=Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($FIELD_MODEL->getFieldInfo()))}

    {assign var=PICKLIST_VALUES value=$FIELD_MODEL->getReferenceValues('EmployeeRoles')}
    {assign var=VIEW_NAME value={getPurifiedSmartyParameters('view')}}

    <input id="{$MODULE}_{$VIEW_NAME}_fieldName_{$FIELD_MODEL->get('name')}" name="{$FIELD_NAME}" type="text" class="row-fluid {if !$IS_BASE_FIELD}select2{/if}"
           value="{$FIELD_MODEL->get('fieldvalue')}" data-validation-engine="validate[funcCall[Vtiger_Base_Validator_Js.invokeValidation ]]"
           data-fieldinfo='{$FIELD_INFO}' width="150px">
{/strip}