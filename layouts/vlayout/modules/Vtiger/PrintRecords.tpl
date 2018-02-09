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
{foreach key=index item=jsModel from=$SCRIPTS}
	<script type="{$jsModel->getType()}" src="{$jsModel->getSrc()}"></script>
{/foreach}
<div id="massPrintContainer" class='modelContainer'>
	<div class="modal-header contentsBackground">
		<button type="button" class="close " data-dismiss="modal" aria-hidden="true">&times;</button>
		<h3 id="massPrintHeader">{vtranslate('LBL_PRINTRECORDS', $MODULE)} {vtranslate($MODULE, $MODULE)}</h3>
	</div>
	<div class="modal-header">
		<div class="pull-right cancelLinkContainer" style="margin-top:0px;">
			<a class="cancelLink" type="reset" data-dismiss="modal">{vtranslate('LBL_CANCEL', $MODULE)}</a>
		</div>
		<a class="btn print-records" name="printButton"><strong>{vtranslate('LBL_PRINT', $MODULE)}</strong></a>
	</div>
	<form class="form-horizontal" id="massPrint" name="massPrint" method="post" action="index.php">
		{if !empty($PICKIST_DEPENDENCY_DATASOURCE)}
			<input type="hidden" name="picklistDependency" value='{Vtiger_Util_Helper::toSafeHTML($PICKIST_DEPENDENCY_DATASOURCE)}' />
		{/if}
		<link rel="stylesheet" href="layouts/vlayout/skins/bluelagoon/style.css?v=1.0" type="text/css" media="screen">
		<input type="hidden" name="module" value="{$MODULE}" />
		<input type="hidden" name="viewname" value="{$CVID}" />
		<input type="hidden" name="selected_ids" value={ZEND_JSON::encode($SELECTED_IDS)}>
		<input type="hidden" name="excluded_ids" value={ZEND_JSON::encode($EXCLUDED_IDS)}>
		<input type="hidden" name="search_key" value= "{$SEARCH_KEY}" />
		<input type="hidden" name="operator" value="{$OPERATOR}" />
		<input type="hidden" name="search_value" value="{$ALPHABET_VALUE}" />
		<input type="hidden" name="search_params" value='{ZEND_JSON::encode($SEARCH_PARAMS)}' />
		<input type="hidden" id="massPrintFieldsNameList" data-value='{Vtiger_Util_Helper::toSafeHTML(ZEND_JSON::encode($MASS_EDIT_FIELD_DETAILS))}' />
		<div>
			<div class="modal-body tabbable" id="massPrintContents">
				{literal}
					<style>
						@page{size: landscape;}
						.redColor{display: none;}
					</style>
				{/literal}
				{foreach key=RECORD_ID item=CURRENT_RECORD from=$RECORDS name='pagebreak'}
				<div {if not $smarty.foreach.pagebreak.last}style='page-break-after:always'{/if}>
					<table class="massPrintTable table table-bordered equalSplit">
					<tr>
						<th colspan="4">{vtranslate($MODULE, $MODULE)} Record: {$RECORD_ID}</th>
					</tr>
					{assign var="CURRENT_STRUCTURE" value=$RECORD_STRUCTURES[$RECORD_ID]}
					{foreach key=BLOCK_LABEL item=BLOCK_FIELDS from=Vtiger_RecordStructure_Model::getInstanceFromRecordModel($CURRENT_RECORD, Vtiger_RecordStructure_Model::RECORD_STRUCTURE_MODE_DETAIL)->getStructure() name=blockIterator}
						{if $BLOCK_FIELDS|@count gt 0}
						{if $BLOCK_LABEL == 'Emails_Block1'}
							{continue}
						{/if}

						<div class="tab-pane id="block_{$smarty.foreach.blockIterator.iteration}">
							<tr>
							{assign var=COUNTER value=0}
							{foreach key=FIELD_NAME item=FIELD_MODEL from=$BLOCK_FIELDS name=blockfields}
								{if $FIELD_MODEL->get('fieldvalue') eq '' && $FIELD_MODEL->get('fieldvalue') eq null || ({vtranslate($FIELD_MODEL->get('label'), $MODULE)} == $FIELD_MODEL->get('label') && $MODULE neq 'Emails')}
									{continue}
								{/if}
								{assign var="isReferenceField" value=$FIELD_MODEL->getFieldDataType()}
								{assign var="refrenceList" value=$FIELD_MODEL->getReferenceList()}
								{assign var="refrenceListCount" value=count($refrenceList)}
								{if $FIELD_MODEL->isEditable() eq true}
									{if $FIELD_MODEL->get('uitype') eq "19"}
										{if $COUNTER eq '1'}
											<td></td><td></td></tr><tr>
											{assign var=COUNTER value=0}
										{/if}
									{/if}
									{if $COUNTER eq 2}
										</tr><tr>
										{assign var=COUNTER value=1}
									{else}
										{assign var=COUNTER value=$COUNTER+1}
									{/if}
									<td class="fieldLabel alignMiddle">
									<label class="muted pull-right">
									{if {$isReferenceField} eq "reference"}
										{if $MODULE eq 'Emails'}
											<td></td>
											{continue}
										{/if}
										{if $refrenceListCount > 1}
											<select style="width: 150px;" class="chzn-select referenceModulesList" id="referenceModulesList">
												<optgroup>
													{foreach key=index item=value from=$refrenceList}
														<option value="{$value}">{vtranslate($value, $value)}</option>
													{/foreach}
												</optgroup>
											</select>
										{else}
											{vtranslate($FIELD_MODEL->get('label'), $MODULE)}
										{/if}
									{else}
										{vtranslate($FIELD_MODEL->get('label'), $MODULE)}
									{/if}
									&nbsp;&nbsp;
									</label>
								</td>
								<td class="fieldValue" {if $FIELD_MODEL->getFieldDataType() eq 'boolean'} style="width:25%" {/if} {if $FIELD_MODEL->get('uitype') eq '19'} colspan="3" {assign var=COUNTER value=$COUNTER+1} {/if}>
									{include file=vtemplate_path($FIELD_MODEL->getUITypeModel()->getDetailViewTemplateName(),$MODULE) FIELD_MODEL=$FIELD_MODEL USER_MODEL=$USER_MODEL MODULE=$MODULE RECORD=$CURRENT_RECORD}
								</td>
							{/if}
							{/foreach}
							{*If their are odd number of fields in massPrint then border top is missing so adding the check*}
							{if $COUNTER is odd}
								<td></td>
								<td></td>
							{/if}
							</tr>
						</div>
						{/if}
					{/foreach}
					</table>
				</div>
				<br>
				{/foreach}
			</div>
		</div>
		<div class="modal-footer">
			<div class="pull-right cancelLinkContainer" style="margin-top:0px;">
				<a class="cancelLink" type="reset" data-dismiss="modal">{vtranslate('LBL_CANCEL', $MODULE)}</a>
			</div>
			<a class="btn print-records" name="printButton"><strong>{vtranslate('LBL_PRINT', $MODULE)}</strong></a>
		</div>
	</form>
</div>
{/strip}
