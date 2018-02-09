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
	<div class='modelContainer'>
		<div class="modal-header">
			<button data-dismiss="modal" class="close" title="{vtranslate('LBL_CLOSE')}">x</button>
			<h3>{vtranslate('LBL_RENAME_PICKLIST_ITEM', $QUALIFIED_MODULE)}</h3>
		</div>
		<form id="renameItemForm" class="form-horizontal" method="post" action="index.php">
			<input type="hidden" name="module" value="{$MODULE}" />
			<input type="hidden" name="source_module" value="{$SOURCE_MODULE}" />
			<input type="hidden" name="oldValue" value="{$FIELD_VALUE}" data-id="{$FIELD_VALUE_ID}"/>
			<input type="hidden" name="action" value="SaveAjax" />
			<input type="hidden" name="mode" value="rename" />
			<input type="hidden" name="picklistName" value="{$FIELD_MODEL->get('name')}" />
			<input type="hidden" name="pickListValues" value='{Vtiger_Util_Helper::toSafeHTML(ZEND_JSON::encode($SELECTED_PICKLISTFIELD_EDITABLE_VALUES))}' />
			<div class="modal-body tabbable">
				<div class="control-group">
					<div class="row">				
						<div class="span3 control-label">{vtranslate('LBL_ITEM_TO_RENAME',$QUALIFIED_MODULE)}</div>
						<div class="span3 control-label"> {$FIELD_VALUE}</div>
					</div>
					<div class="row" style="margin-top: 10px;">
						<div class="span3 control-label"><span class="redColor">*</span>{vtranslate('LBL_ENTER_NEW_NAME',$QUALIFIED_MODULE)}</div>
						<div class="span3 controls" style="margin-left:30px;"><input type="text" data-validation-engine="validate[required, funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" data-validator={Zend_Json::encode([['name'=>'FieldLabel']])} name="newValue"></div>
					</div>
				</div>
			</div>
			{include file='ModalFooter.tpl'|@vtemplate_path:$qualifiedName}
		</form>
	</div>
{/strip}