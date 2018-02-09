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
{assign var="FIELD_PARAMS" value = $FIELD_MODEL->getFieldInfo()}
{assign var="SPECIAL_VALIDATOR" value=$FIELD_MODEL->getValidator()}
{if $MODULE eq 'HelpDesk' && ($FIELD_MODEL->get('name') eq 'days' || $FIELD_MODEL->get('name') eq 'hours')}
	{assign var="FIELD_VALUE" value=$FIELD_MODEL->getDisplayValue($FIELD_MODEL->get('fieldvalue'))}
{else}
	{assign var="FIELD_VALUE" value=$FIELD_MODEL->get('fieldvalue')}
{/if}
<div class="row-fluid input-prepend input-append">
<input {if $FIELD_MODEL->isBatchAddSubtract()}readonly{/if} id="{$MODULE}_editView_fieldName_{$FIELD_MODEL->get('name')}" type="{if $FIELD_PARAMS['min'] eq 0 || $FIELD_PARAMS['min'] || $FIELD_PARAMS['max']}number{else}text{/if}" class="input-large" {if $FIELD_PARAMS['min'] neq ''} min="{$FIELD_PARAMS['min']}" {/if}{if $FIELD_PARAMS['max'] neq ''} max="{$FIELD_PARAMS['max']}" {/if} {if $FIELD_PARAMS['step'] neq ''}step="{$FIELD_PARAMS['step']}"{/if} data-validation-engine="validate[{if $FIELD_MODEL->isMandatory() eq true} required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" name="{$FIELD_MODEL->getFieldName()}"
value="{$FIELD_VALUE}" data-fieldinfo='{$FIELD_INFO}' {if !empty($SPECIAL_VALIDATOR)}data-validator={Zend_Json::encode($SPECIAL_VALIDATOR)}{/if} />
{if $FIELD_MODEL->isBatchAddSubtract()}
    <span class="add-on cursorPointer batchAddSubtract" data-relatedfield='{$MODULE}_editView_fieldName_{$FIELD_MODEL->get('name')}'><i class="icon-plus"></i><i class="icon-minus"></i></span>
{/if}
</div>
{/strip}
