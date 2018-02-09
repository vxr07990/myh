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
	<div class='pull-right detailViewButtoncontainer'>
		<div class='btn-toolbar'>
			<span class='btn-group'>
				<button class="btn" type='button' id="AgentManager_detailView_basicAction_LBL_COORDINATORS" onclick="window.location.href='index.php?module=AgentManager&amp;view=CoordinatorListEdit&amp;record={$RECORD_ID}'">
					<strong>{vtranslate("LBL_MANAGE_COORDINATORS", $MODULE_NAME)}</strong>
				</button>
				<br>
				<br>
				<br>
			</span>
		</div>
	</div>
	{if $COORDINATOR_ROWS|@count > 0}
	<table class='table table-bordered blockContainer showInlineTable'>
		<thead>
			<th class='blockHeader' colspan='5'>
				&nbsp;&nbsp;Assigned Sales Coordinators
			</th>
		</thead>
		<tbody>
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
					<input type='text' id='{$MODULE_NAME}_CoordinatorListEdit_fieldName_sales_person' name='sales_person' class='input-large' />
				</td>
				<td class='fieldLabel' style='width:24%;'>
					Coordinators <span class='redColor'>*</span>
				</td>
				<td class='fieldValue' style='width:24%;'>
					{assign var="coordinatorName" value="coordinators"}
					{assign var="FIELD_VALUE_LIST" value=explode(' |##| ',$COORDINATOR_VALUES)}
					<input type="hidden" name="{$coordinatorName}" value="" />
					<select id="{$MODULE_NAME}_CoordinatorListEdit_fieldName_{$coordinatorName}" multiple name="{$coordinatorName}[]" data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" style="width: 60%">
						{foreach item=PICKLIST_VALUE key=PICKLIST_NAME  from=$COORDINATOR_VALUES}
							<option value="{Vtiger_Util_Helper::toSafeHTML($PICKLIST_NAME)}" {if in_array(Vtiger_Util_Helper::toSafeHTML($PICKLIST_NAME), $FIELD_VALUE_LIST)} selected {/if}>{$PICKLIST_VALUE}</option>
						{/foreach}
					</select>
				</td>
			</tr>
		</tbody>
	</table>
	<br />
	{else}
		<table class="emptyRecordsDiv">
			<tbody>
				<tr>
					<td>
						{vtranslate('LBL_NO')} {vtranslate("LBL_COORDINATORS", $MODULE_NAME)} {vtranslate('LBL_FOUND')}.
					</td>
				</tr>
			</tbody>
		</table>
	{/if}
{/strip}