{strip}
{assign var=FIELD_NAME value=$FIELD_MODEL->get('name')}
{assign var="REFERENCE_LIST" value=$FIELD_MODEL->getReferenceList()}
{assign var="REFERENCE_LIST_COUNT" value=count($REFERENCE_LIST)}
{assign var="FIELD_INFO" value=Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($FIELD_MODEL->getFieldInfo()))}
{assign var="SPECIAL_VALIDATOR" value=$FIELD_MODEL->getValidator()}
{* TODO: Make all this work with more than one reference MODULE *}
{if {$REFERENCE_LIST_COUNT} eq 1}
    <input name="popupReferenceModule" type="hidden" value="{$REFERENCE_LIST[0]['type']}" />
{/if}
<input name="{$FIELD_MODEL->getFieldName()}" type="hidden" value="{$FIELD_MODEL->get('fieldvalue')}" class="sourceField" data-displayvalue='{$FIELD_MODEL->getEditViewDisplayValue($FIELD_MODEL->get('fieldvalue'))}' data-fieldinfo='{$FIELD_INFO}' />
{assign var="displayId" value=$FIELD_MODEL->get('fieldvalue')}
<div class="row-fluid input-prepend input-append">
<span class="add-on clearReferenceSelection clearReferenceSelectionAccountingIntegration cursorPointer {$MODULE}_editView_fieldName_{$FIELD_NAME}_clear">
    <i id="{$MODULE}_editView_fieldName_{$FIELD_NAME}_clear" class='icon-remove-sign' title="{vtranslate('LBL_CLEAR', $MODULE)}"></i>
</span>
{assign var=VIEW_NAME value={getPurifiedSmartyParameters('view')}}
{assign var=MODULE_NAME value={getPurifiedSmartyParameters('module')}}
<input id="{$FIELD_NAME}_display" name="{$FIELD_MODEL->getFieldName()}_display" type="text" class="{if (($VIEW_NAME eq 'Edit') or ($MODULE_NAME eq 'Webforms'))} span7 {else} span8 {/if}	marginLeftZero accountingIntegrationAutoComplete" {if !empty($displayId)}readonly="true"{/if}
            value="{$FIELD_MODEL->getEditViewDisplayValue($displayId)}" data-validation-engine="validate[{if $FIELD_MODEL->isMandatory() eq true} required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]"
            data-fieldinfo='{$FIELD_INFO}' placeholder="{vtranslate('LBL_TYPE_SEARCH',$MODULE)}"
            {if !empty($SPECIAL_VALIDATOR)}data-validator='{Zend_Json::encode($SPECIAL_VALIDATOR)}'{/if} title="Accounting Integration Reference - {$REFERENCE_LIST[0]['type']}"/>
                <span class="add-on accountingIntegrationRelatedPopup cursorPointer">
                    <i id="{$MODULE}_editView_fieldName_{$FIELD_NAME}_select" class="icon-search" title="{vtranslate('LBL_SELECT', $MODULE)}" ></i>
                </span>
</div>
{/strip}