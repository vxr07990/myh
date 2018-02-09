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
<div id="convertContainer" class="modelContainer">
	<div class="modal-header contentsBackground">
		<button class="close" aria-hidden="true" data-dismiss="modal" type="button" title="{vtranslate('LBL_CLOSE')}">x</button>
		<h3>{vtranslate('LBL_CUBESHEETS_CONVERT', $MODULE)} : {$RECORD->getName()}</h3>
	</div>

	<form class="form-horizontal recordEditView" name="CreateEstimateForm" id="CreateEstimateForm" method="post" action="index.php">
		{if !empty($PICKIST_DEPENDENCY_DATASOURCE)}
			<input type="hidden" name="picklistDependency" value='{Vtiger_Util_Helper::toSafeHTML($PICKIST_DEPENDENCY_DATASOURCE)}' />
		{/if}
		<input type="hidden" name="module" value="{$MODULE}">
		<input type="hidden" name="instance_name" value="{getenv('INSTANCE_NAME')}">
		<input type="hidden" name="record" value="{$RECORD->getId()}">
		<input type="hidden" name="action" value="CreateEstimate">
		<input type="hidden" disabled id="allAvailableTariffs" value="{$AVAILABLE_TARIFFS}" />
		<input type="hidden" disabled id="tariffPackingOptions" value="{$PACKING_OPTIONS}" />
		<div class="quickCreateContent">
			<div class="modal-body" style="overflow-y: scroll; width: auto; height: 400px;">
				{foreach key=BLOCK_LABEL item=FIELD_MODELS from=$CONVERT_CUBESHEET_FIELDS}
                    {if $BLOCK_LABEL eq 'LBL_CUBESHEETS_ACCOUNT'}
                        {assign var=PREFIX value='account_'}
                    {else}
                        {assign var=PREFIX value=''}
                    {/if}
					<table class="massEditTable table table-bordered equalSplit" name="{$BLOCK_LABEL}">
						<thead>
							<tr>
								<th class="blockHeader" colspan="4">
										&nbsp;&nbsp;{vtranslate({$BLOCK_LABEL},{$MODULE})}
										&nbsp;&nbsp;{if $BLOCK_LABEL eq 'LBL_CUBESHEETS_ACCOUNT'}
											<input type="checkbox" name="createAccount" id="createAccount" {*if !$CONVERT_CUBESHEET_FIELDS['LBL_CUBESHEETS_ESTIMATE']['account_id']->get('fieldvalue')}checked{/if*}>
										{/if}
								</th>
							</tr>
						</thead>
						<tbody class="blockContents">
							<tr>
							{assign var=COUNTER value=0}
							{foreach key=FIELD_NUM item=FIELD_MODEL from=$FIELD_MODELS name=blockfields}
								{assign var="isReferenceField" value=$FIELD_MODEL->getFieldDataType()}
								{assign var="refrenceList" value=$FIELD_MODEL->getReferenceList()}
								{assign var="refrenceListCount" value=count($refrenceList)}
								{assign var="FIELD_NAME" value=$FIELD_MODEL->get('name')}
                                {if $FIELD_NAME eq 'move_type'}
                                    {assign var="BUSINESS_LINE" value=$FIELD_MODEL->get('fieldvalue')}
                                {/if}
								{if $FIELD_MODEL->get('uitype') eq "19"}
									{if $COUNTER eq '1'}
										<td class='fieldLabel'></td><td class='fieldValue'></td></tr><tr>
										{assign var=COUNTER value=0}
									{/if}
								{/if}
								{if $COUNTER eq 2}
									</tr><tr>
									{assign var=COUNTER value=1}
								{else}
									{assign var=COUNTER value=$COUNTER+1}
								{/if}
								<td class='fieldLabel'>
									{if $isReferenceField neq "reference"}<label class="muted pull-right">{/if}
									{if $FIELD_MODEL->isMandatory() eq true && $isReferenceField neq "reference"} <span class="redColor">*</span> {/if}
									{if $isReferenceField eq "reference"}
										{if $refrenceListCount > 1}
											{assign var="DISPLAYID" value=$FIELD_MODEL->get('fieldvalue')}
											{assign var="REFERENCED_MODULE_STRUCT" value=$FIELD_MODEL->getUITypeModel()->getReferenceModule($DISPLAYID)}
											{if !empty($REFERENCED_MODULE_STRUCT)}
												{assign var="REFERENCED_MODULE_NAME" value=$REFERENCED_MODULE_STRUCT->get('name')}
											{/if}
											<span class="pull-right">
												{if $FIELD_MODEL->isMandatory() eq true} <span class="redColor">*</span> {/if}
												<select style="width: 150px;" class="chzn-select referenceModulesList" id="referenceModulesList">
													<optgroup>
														{foreach key=index item=value from=$refrenceList}
															<option value="{$value}" {if $value eq $REFERENCED_MODULE_NAME} selected {/if} >{vtranslate($value, $value)}</option>
														{/foreach}
													</optgroup>
												</select>
											</span>
										{else}
											<label class="muted pull-right">{if $FIELD_MODEL->isMandatory() eq true} <span class="redColor">*</span> {/if}{vtranslate($FIELD_MODEL->get('label'), $FIELD_MODEL->getModule()->name)}</label>
										{/if}
									{else}
										{vtranslate($FIELD_MODEL->get('label'), $FIELD_MODEL->getModule()->name)}
									{/if}
								{if $isReferenceField neq "reference"}</label>{/if}
								</td>
								<td class="fieldValue" {if $FIELD_MODEL->get('uitype') eq '19'} colspan="3" {assign var=COUNTER value=$COUNTER+1} {/if}>
										{include file=vtemplate_path($FIELD_MODEL->getUITypeModel()->getTemplateName(),$MODULE) PREFIX=$PREFIX}
								</td>
                                                                {if $FIELD_NAME eq "assigned_user_id"}
                                                                    </tr><tr>
                                                                    {assign var=COUNTER value=0}
                                                                {/if}
							{/foreach}
							{if $COUNTER eq '1'}
								<td class='fieldLabel'></td><td class='fieldValue'></td></tr><tr>
								{assign var=COUNTER value=1}
							{/if}
							</tr>
						</tbody>
					</table>
					<br>
					<br>
				{/foreach}
			</div>
		</div>
		<div class="modal-footer">
			<div class="pull-right cancelLinkContainer" style="margin-top:0px;">
				<a class="cancelLink" type="reset" data-dismiss="modal">Cancel</a>
			</div>
			<button type="submit" class="btn btn-success" id="convertCubesheet">
				<strong>{vtranslate('LBL_CUBESHEETS_CONVERT', $MODULE)}</strong>
			</button>
		</div>
	</form>
</div>
{/strip}
