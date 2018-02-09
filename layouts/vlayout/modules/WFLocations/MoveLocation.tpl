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
<div id="moveContainer" class="modelContainer">
	<div class="modal-header contentsBackground">
		<button class="close" aria-hidden="true" data-dismiss="modal" type="button" title="{vtranslate('LBL_CLOSE')}">x</button>
		<h3>{vtranslate('Move Location', $MODULE)}</h3>
	</div>

	<form class="form-horizontal recordEditView" name="MoveLocationForm" id="MoveLocationForm" method="post" action="index.php">
		{if !empty($PICKIST_DEPENDENCY_DATASOURCE)}
			<input type="hidden" name="picklistDependency" value='{Vtiger_Util_Helper::toSafeHTML($PICKIST_DEPENDENCY_DATASOURCE)}' />
		{/if}
		<input type="hidden" name="module" value="{$MODULE}">
		<input type="hidden" name="instance_name" value="{getenv('INSTANCE_NAME')}">
		<input type="hidden" name="action" value="MoveLocation">
    <input type="hidden" name="location_ids" value="{Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($LOCATION_IDS))}" />
		<input type="hidden" name="detail_view" value="{$DETAIL_VIEW}" />
		<div class="quickCreateContent">
			<div class="modal-body">
				<table>
					<tr>
						<td class="fieldLabel">{vtranslate({$FIELD_MODEL->label})}</td>
						<td class="fieldValue">{include file=vtemplate_path($FIELD_MODEL->getUITypeModel()->getTemplateName(),$MODULE)}</td>
					</tr>
				</table>
			</div>
		</div>
		<div class="modal-footer">
			<div class="pull-right cancelLinkContainer" style="margin-top:0px;">
				<a class="cancelLink" type="reset" data-dismiss="modal">Cancel</a>
			</div>
			<button type="submit" class="btn btn-success" id="convertCubesheet">
				<strong>{vtranslate('Move Location', $MODULE)}</strong>
			</button>
		</div>
	</form>
</div>
{/strip}
