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
<div class='container-fluid editViewContainer'>
	<form class="form-horizontal recordEditView" id="EditView" name="EditView" method="post" action="index.php" enctype="multipart/form-data">
		{assign var=WIDTHTYPE value=$USER_MODEL->get('rowheight')}
		{if !empty($PICKIST_DEPENDENCY_DATASOURCE)}
			<input type="hidden" name="picklistDependency" value='{Vtiger_Util_Helper::toSafeHTML($PICKIST_DEPENDENCY_DATASOURCE)}' />
		{/if}
		{assign var=QUALIFIED_MODULE_NAME value={$MODULE}}
		{assign var=IS_PARENT_EXISTS value=strpos($MODULE,":")}
		{if $IS_PARENT_EXISTS}
			{assign var=SPLITTED_MODULE value=":"|explode:$MODULE}
			<input type="hidden" name="module" value="{$SPLITTED_MODULE[1]}" />
			<input type="hidden" name="parent" value="{$SPLITTED_MODULE[0]}" />
		{else}
			<input type="hidden" name="module" value="{$MODULE}" />
		{/if}
		<input type="hidden" name="action" value="SaveCoordinators" />
		<input type="hidden" name="record" value="{$RECORD_ID}" />
		<input type="hidden" name="defaultCallDuration" value="{$USER_MODEL->get('callduration')}" />
		<input type="hidden" name="defaultOtherEventDuration" value="{$USER_MODEL->get('othereventduration')}" />
		{if $IS_RELATION_OPERATION }
			<input type="hidden" name="sourceModule" value="{$SOURCE_MODULE}" />
			<input type="hidden" name="sourceRecord" value="{$SOURCE_RECORD}" />
			<input type="hidden" name="relationOperation" value="{$IS_RELATION_OPERATION}" />
		{/if}
		{assign var=SINGLE_MODULE_NAME value='SINGLE_'|cat:$MODULE}
		<div class="contentHeader row-fluid">
			<h3 class="span8 textOverflowEllipsis">&nbsp;&nbsp;{vtranslate('LBL_MANAGE_COORDINATORS', $MODULE)}</h3>
			<span class="pull-right">
				<button class="btn btn-success" type="submit" id="submitButton">
					<strong>{vtranslate('LBL_SAVE')}</strong>
				</button>
				<a class="cancelLink" type="reset" onclick="javascript:window.history.back();">
					{vtranslate('LBL_CANCEL')}
				</a>
			</span>
		</div>
		<table class='table table-bordered blockContainer showInlineTable'>
			<thead>
				<th class='blockHeader' colspan='5'>
					&nbsp;&nbsp;Assigned Sales Coordinators
				</th>
			</thead>
			<tbody>
				<tr colspan='5'>
					<td class='fieldLabel' colspan='5'>
						<button type="button" class="addCoordinator">+</button>
						<input type="hidden" name="numCoordinators" id="numCoordinators" value="{$COORDINATORS|@count}">
						<button type="button" class="addCoordinator" style="clear:right;float:right">+</button>
					</td>
				</tr>
				<tr class='hide defaultCoordinator'>
					<td class='fieldLabel' style='width:4%;text-align:center;vertical-align:middle'>
						<a class="deleteCoordinatorButton">
							<i title="Delete" class="icon-trash alignMiddle"></i>
						</a>
						<input type="hidden" name="coordinatorId" value='0'>
						<input type="hidden" name="coordinatorDeleted" value=''>
					</td>
					<td class='fieldLabel' style='width:24%;'>
						Sales Person <span class='redColor'>*</span>
					</td>
					<td class='fieldValue' style='width:24%;'>
						{assign var="salesName" value="sales_person"}
						<input type='text' id='{$MODULE}_CoordinatorListEdit_fieldName_sales_person' name='sales_person' class='input-large' />
					</td>
					<td class='fieldLabel' style='width:24%;'>
						Coordinators <span class='redColor'>*</span>
					</td>
					<td class='fieldValue' style='width:24%;'>
						{assign var="coordinatorName" value="coordinators"}
						{assign var="FIELD_VALUE_LIST" value=explode(' |##| ',$COORDINATOR_VALUES)}
						<input type="hidden" name="{$coordinatorName}" value="" />
						<select id="{$MODULE}_CoordinatorListEdit_fieldName_{$coordinatorName}" multiple name="{$coordinatorName}[]" data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" style="width: 60%">
							{foreach item=PICKLIST_VALUE key=PICKLIST_NAME  from=$COORDINATOR_VALUES}
								<option value="{Vtiger_Util_Helper::toSafeHTML($PICKLIST_NAME)}" {if in_array(Vtiger_Util_Helper::toSafeHTML($PICKLIST_NAME), $FIELD_VALUE_LIST)} selected {/if}>{$PICKLIST_VALUE}</option>
							{/foreach}
						</select>
					</td>
				</tr>
			</tbody>
		</table>
		<br />
	</form>
</div>
{/strip}