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
	<form class="form-horizontal recordEditView" id="EditView" name="EditView" method="post" action="index.php" enctype="multipart/form-data" data-lockdatefield="{$LOCK_RECEIVED_DATE}">
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
		<input type="hidden" name="action" value="Save" />
		<input type="hidden" name="record" value="{$RECORD_ID}" />
		<input type="hidden" name="defaultCallDuration" value="{$USER_MODEL->get('callduration')}" />
		<input type="hidden" name="defaultOtherEventDuration" value="{$USER_MODEL->get('othereventduration')}" />
		<input type="hidden" name="instance" value="{getenv('INSTANCE_NAME')}">
		{if $IS_RELATION_OPERATION }
			<input type="hidden" name="sourceModule" value="{$SOURCE_MODULE}" />
			<input type="hidden" name="sourceRecord" value="{$SOURCE_RECORD}" />
			<input type="hidden" name="relationOperation" value="{$IS_RELATION_OPERATION}" />
		{/if}
		<div class="contentHeader row-fluid">
		{assign var=SINGLE_MODULE_NAME value='SINGLE_'|cat:$MODULE}
		{if $RECORD_ID neq ''}
			<h3 class="span8 textOverflowEllipsis" title="{vtranslate('LBL_EDITING', $MODULE)} {vtranslate($SINGLE_MODULE_NAME, $MODULE)} {$RECORD_STRUCTURE_MODEL->getRecordName()}">{vtranslate('LBL_EDITING', $MODULE)} {vtranslate($SINGLE_MODULE_NAME, $MODULE)} - {$RECORD_STRUCTURE_MODEL->getRecordName()}</h3>
		{else}
			<h3 class="span8 textOverflowEllipsis">{vtranslate('LBL_CREATING_NEW', $MODULE)} {vtranslate($SINGLE_MODULE_NAME, $MODULE)}</h3>
		{/if}
			<span class="pull-right">
				<button class="btn btn-success" type="submit"><strong>{vtranslate('LBL_SAVE', $MODULE)}</strong></button>
				<a class="cancelLink" type="reset" onclick="javascript:window.history.back();">{vtranslate('LBL_CANCEL', $MODULE)}</a>
			</span>
		</div>

		<input type="hidden" id="hiddenFields" name="hiddenFields" value="{$HIDDEN_FIELDS}" />
		{foreach key=BLOCK_LABEL item=BLOCK_FIELDS from=$RECORD_STRUCTURE name="EditViewBlockLevelLoop"}
			{if $BLOCK_LABEL == 'LBL_ORDERS_EXTRASTOPS'}{continue}{/if}
			{if $BLOCK_LABEL == 'LBL_ORDERS_ORIGINADDRESS'}
				{if $IS_ACTIVE_ADDRESSLIST == true}
					{include file=vtemplate_path('AddressListEdit.tpl', 'AddressList')}
					{continue}
				{/if}
			{/if}
			{if $BLOCK_FIELDS|@count lte 0}{continue}{/if}
			{if $BLOCK_LABEL eq 'LBL_ORDERS_PARTICIPANTS' AND getenv('INSTANCE_NAME') eq 'mccollisters'}{continue}{/if}
			<table name="{if $BLOCK_LABEL neq 'LBL_ORDERS_PARTICIPANTS'}{$BLOCK_LABEL}{else}participatingAgentsTable{/if}" class="table table-bordered blockContainer showInlineTable {if $BLOCK_LABEL neq 'LBL_ORDERS_PARTICIPANTS' && $BLOCK_LABEL neq 'LBL_ORDERS_DATES' && $BLOCK_LABEL neq 'LBL_ORDERS_WEIGHTS'}equalSplit{/if}" {if is_array($HIDDEN_BLOCKS)}{if in_array($BLOCK_LABEL, $HIDDEN_BLOCKS)} style="display:none;"{/if}{/if}>
                <thead>
			<tr>
				<th class="blockHeader" colspan="{if $BLOCK_LABEL eq 'LBL_ORDERS_DATES' or $BLOCK_LABEL eq 'LBL_ORDERS_WEIGHTS'}6{elseif $BLOCK_LABEL eq 'LBL_ORDERS_PARTICIPANTS'}40{else}4{/if}">{vtranslate($BLOCK_LABEL, $MODULE)}</th>
			</tr>
                </thead>
                <tbody>
			<tr>
			{*if $BLOCK_LABEL eq 'LBL_ORDERS_PARTICIPANTS'}


			{elseif $BLOCK_LABEL eq 'LBL_ORDERS_EXTRASTOPS'}
				</tr>
					<tr class="fieldLabel" colspan="4">
						<td colspan="4"><button type="button" name="addStop" id="addStop">+</button><input type="hidden" id="numStops" name="numStops" value="{$STOPS_ROWS|@count}"><button type="button" name="addStop2" id="addStop2" style="clear:right;float:right">+</button></td>
					</tr>
				<tbody class="defaultStop stopBlock hide">
					<tr class="fieldLabel" colspan="4">
						<td colspan="4" class="blockHeader">
							<img class="cursorPointer alignMiddle blockToggle stopToggle{if !($IS_HIDDEN)} hide {/if} "  src="{vimage_path('arrowRight.png')}" data-mode="hide" data-id='defaultStop'>
							<img class="cursorPointer alignMiddle blockToggle stopToggle{if ($IS_HIDDEN)} hide {/if}"  src="{vimage_path('arrowDown.png')}" data-mode="show" data-id='defaultStop'>
							<span class="stopTitle"><b>&nbsp;&nbsp;&nbsp;Stop Number</b></span>
							<span><a style="float: right; padding: 3px"><i title="Delete" class="deleteStopButton icon-trash"></i></a></span>
						</td>
					</tr>
					<tr colspan="4" class="stopContent defaultStopContent hide">
						<td colspan="4" style="padding: 0px;">
							<table class="table equalSplit table-bordered" style="padding: 0px; border: 0px;">
								<tbody>
									<tr style="width:100%" colspan="4">
										<td class="fieldLabel medium" colspan="1" style="text-align:center;margin:auto">
											<label class="muted pull-right marginRight10px"><span class="redColor">*</span>{vtranslate('LBL_ORDERS_STOPDESCRIPTION', $MODULE)}</label>
										</td>
										<td class="fieldValue medium">
											<div class="row-fluid">
												<span class="span12">
													{$LOCALFIELDINFO.mandatory = true}
													{$LOCALFIELDINFO.name = 'stop_description'}
													{$LOCALFIELDINFO.label = 'LBL_ORDERS_STOPDESCRIPTION'}
													{$LOCALFIELDINFO.type = 'string'}
													{$INFO = Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($LOCALFIELDINFO))}
													<input id="stop_description" type="text" class="input-large" name="stop_description" value="" data-fieldinfo={$INFO} data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]">
												</span>
											</div>
										</td>
										<td class="fieldLabel medium" colspan="1" style="text-align:center;margin:auto">
											<label class="muted pull-right marginRight10px"><span class="redColor">*</span>{vtranslate('LBL_ORDERS_STOPSEQUENCE', $MODULE)}</label>
										</td>
										<td class="fieldValue medium">
											<div class="row-fluid">
												<span class="span12">
													{$LOCALFIELDINFO.mandatory = true}
													{$LOCALFIELDINFO.name = 'stop_sequence'}
													{$LOCALFIELDINFO.label = 'LBL_ORDERS_STOPSEQUENCE'}
													{$LOCALFIELDINFO.type = 'string'}
													{$INFO = Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($LOCALFIELDINFO))}
													<input id="stop_sequence" type="text" class="input-large" name="stop_sequence" data-fieldinfo={$INFO} data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]">
												</span>
											</div>
										</td>
									</tr>
									<tr style="width:100%" colspan="4">
										<td class="fieldLabel medium" colspan="1" style="text-align:center;margin:auto">
											<label class="muted pull-right marginRight10px">{vtranslate('LBL_ORDERS_STOPWEIGHT', $MODULE)}</label>
										</td>
										<td class="fieldValue medium">
											<div class="row-fluid">
												<span class="span12">
													<input id="stop_weight" type="text" class="input-large" name="stop_weight" value="">
												</span>
											</div>
										</td>
										<td class="fieldLabel medium" colspan="1" style="text-align:center;margin:auto">
											<label class="muted pull-right marginRight10px">{vtranslate('LBL_ORDERS_STOPISPRIMARY', $MODULE)}</label>
										</td>
										<td class="fieldValue medium">
											<div class="row-fluid">
												<span class="span12">
													<input id="stop_isprimary" type="checkbox" class="input-large" name="stop_isprimary">
												</span>
											</div>
										</td>
									</tr>
									<tr style="width:100%" colspan="4">
										<td class="fieldLabel medium" colspan="1" style="text-align:center;margin:auto">
											<label class="muted pull-right marginRight10px">{vtranslate('LBL_ORDERS_STOPADDRESS1', $MODULE)}</label>
										</td>
										<td class="fieldValue medium">
											<div class="row-fluid">
												<span class="span12">
													<input id="stop_address1" type="text" class="input-large" name="stop_address1" value="">
												</span>
											</div>
										</td>
										<td class="fieldLabel medium" colspan="1" style="text-align:center;margin:auto">
											<label class="muted pull-right marginRight10px">{vtranslate('LBL_ORDERS_STOPADDRESS2', $MODULE)}</label>
										</td>
										<td class="fieldValue medium">
											<div class="row-fluid">
												<span class="span12">
													<input id="stop_address2" type="text" class="input-large" name="stop_address2" value="">
												</span>
											</div>
										</td>
									</tr>
									<tr style="width:100%" colspan="4">
										<td class="fieldLabel medium" colspan="1" style="text-align:center;margin:auto">
											<label class="muted pull-right marginRight10px">{vtranslate('LBL_ORDERS_STOPPHONE1', $MODULE)}<label>
										</td>
										<td class="fieldValue medium">
											<div class="row-fluid">
												<span class="span12">
													<input id="stop_phone1" type="text" class="input-large" name="stop_phone1" value="">
												</span>
											</div>
										</td>
										<td class="fieldLabel medium" colspan="1" style="text-align:center;margin:auto">
											<label class="muted pull-right marginRight10px">{vtranslate('LBL_ORDERS_STOPPHONE2', $MODULE)}</label>
										</td>
										<td class="fieldValue medium">
											<div class="row-fluid">
												<span class="span12">
													<input id="stop_phone2" type="text" class="input-large" name="stop_phone2" value="">
												</span>
											</div>
										</td>
									</tr>
									<tr style="width:100%" colspan="4">
										<td class="fieldLabel medium" colspan="1" style="text-align:center;margin:auto">
											<label class="muted pull-right marginRight10px">{vtranslate('LBL_ORDERS_STOPPHONE1TYPE', $MODULE)}</label>
										</td>
										<td class="fieldValue medium">
											<input type="hidden" name="stop_phonetype1_prev" value="none">
											<select style="text-align:left" id="stop_phonetype1" name="stop_phonetype1" data-fieldinfo="" data-selected-value="">
												<option value="" style="text-align:left">Select an Option</option>
												<option style="text-align:left" value="Home">Home</option>
												<option style="text-align:left" value="Work">Work</option>
												<option style="text-align:left" value="Cell">Cell</option>
											</select>
										</td>
										<td class="fieldLabel medium" colspan="1" style="text-align:center;margin:auto">
											<label class="muted pull-right marginRight10px">{vtranslate('LBL_ORDERS_STOPPHONE2TYPE', $MODULE)}</label>
										</td>
										<td class="fieldValue medium">
											<input type="hidden" name="stop_phonetype2_prev" value="none">
											<select style="text-align:left" id="stop_phonetype2" name="stop_phonetype2" data-fieldinfo="" data-selected-value="">
												<option value="" style="text-align:left">Select an Option</option>
												<option style="text-align:left" value="Home">Home</option>
												<option style="text-align:left" value="Work">Work</option>
												<option style="text-align:left" value="Cell">Cell</option>
											</select>
										</td>
									</tr>
									<tr style="width:100%" colspan="4">
										<td class="fieldLabel medium" colspan="1" style="text-align:center;margin:auto">
											<label class="muted pull-right marginRight10px">{vtranslate('LBL_ORDERS_STOPCITY', $MODULE)}</label>
										</td>
										<td class="fieldValue medium">
											<div class="row-fluid">
												<span class="span12">
													<input id="stop_city" type="text" class="input-large" name="stop_city" value="">
												</span>
											</div>
										</td>
										<td class="fieldLabel medium" colspan="1" style="text-align:center;margin:auto">
											<label class="muted pull-right marginRight10px">{vtranslate('LBL_ORDERS_STOPSTATE', $MODULE)}</label>
										</td>
										<td class="fieldValue medium">
											<div class="row-fluid">
												<span class="span12">
													<input id="stop_state" type="text" class="input-large" name="stop_state" value="">
												</span>
											</div>
										</td>
									</tr>
									<tr style="width:100%" colspan="4">
										<td class="fieldLabel medium" colspan="1" style="text-align:center;margin:auto">
											<label class="muted pull-right marginRight10px">{vtranslate('LBL_ORDERS_STOPZIP', $MODULE)}</label>
										</td>
										<td class="fieldValue medium">
											<div class="row-fluid">
												<span class="span12">
													<input id="stop_zip" type="text" class="input-large" name="stop_zip" value="">
												</span>
											</div>
										</td>
										<td class="fieldLabel medium" colspan="1" style="text-align:center;margin:auto">
											<label class="muted pull-right marginRight10px">{vtranslate('LBL_ORDERS_STOPCOUNTRY', $MODULE)}</label>
										</td>
										<td class="fieldValue medium">
											<div class="row-fluid">
												<span class="span12">
													<input id="stop_country" type="text" class="input-large" name="stop_country" value="">
												</span>
											</div>
										</td>
									</tr>
									<tr style="width:100%" colspan="4">
										<td class="fieldLabel medium" colspan="1" style="text-align:center;margin:auto">
											<label class="muted pull-right marginRight10px">{vtranslate('LBL_ORDERS_STOPDATE', $MODULE)}</label>
										</td>
										<td class="fieldValue medium">
											<span class="span12">
												<div class="input-append row-fluid">
													<div class="row-fluid date">
														{$LOCALFIELDINFO.name = 'stop_date'}
														{$LOCALFIELDINFO.label = 'LBL_ORDERS_STOPDATE'}
														{$LOCALFIELDINFO.type = 'date'}
														{$INFO = Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($LOCALFIELDINFO))}
														<input id="{$MODULE}_editView_fieldName_stop_date" type="text" class="dateField input-large" name="stop_date" data-date-format="mm-dd-yyyy" value="" data-validation-engine="validate[funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" data-fieldinfo={$INFO}>
														<span class="add-on">
														<i class="icon-calendar"></i>
														</span>
													</div>
												</div>
											</span>
										</td>
										<td class="fieldLabel medium" colspan="1" style="text-align:center;margin:auto">
											<label class="muted pull-right marginRight10px">{vtranslate('LBL_ORDERS_STOPCONTACT', $MODULE)}</label>
										</td>
										<td class="fieldValue medium">
											<div class="row-fluid">
												<input class="stopReference referenceModule" name="popupReferenceModule" type="hidden" value="Contacts">
												<span class="span12">
													<div class="row-fluid input-prepend input-append">
														<input class="sourceField" name="stop_contact" type="hidden" value="" data-displayvalue="">
														<span class="add-on clearReferenceSelection cursorPointer">
															<i id="Opportunities_editView_fieldName_stop_contact_clear" class="icon-remove-sign" title="Clear"></i>
														</span>
														<input id="stop_contact_display" name="stop_contact_display" type="text" class="span7 marginLeftZero autoComplete ui-autocomplete-input stopReference referenceDisplay" readonly="true" data-validation-engine="validate[funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" placeholder="Type to search" autocomplete="off" role="textbox" aria-autocomplete="list" aria-haspopup="true">
														<span class="add-on relatedPopup cursorPointer">
															<i id="Opportunities_editView_fieldName_stop_contact_select" class="icon-search" title="Select"></i>
														</span>
														<span class="add-on cursorPointer createReferenceRecord">
															<i id="Opportunities_editView_fieldName_stop_contact_create" class="icon-plus" title="Create"></i>
														</span>
													</div>
												</span>
											</div>
										</td>
									</tr>
									<tr>
										<td class="fieldLabel medium">
											<label class="muted pull-right marginRight10px">{vtranslate('LBL_ORDERS_STOPTYPE', $MODULE)}</label>
										</td>
										<td class="fieldValue medium">
											<input type="hidden" name="stop_type" value="none">
											<select class="stopType" style="text-align:left" id="stop_type" name="stop_type" data-selected-value="">
												<option value="" style="text-align:left">Select an Option</option>
												<option style="text-align:left" value="Origin">Origin</option>
												<option style="text-align:left" value="Destination">Destination</option>
											</select>
										</td>
										<td class="fieldLabel medium">
											&nbsp;
										</td>
										<td class="fieldValue medium">
											<input id="stop_id" type="hidden" name="stop_id" value="none">
										</td>
									</tr>
								</table>
							</td>
						</tr>
					</tbody>
				{foreach key=STOP_INDEX item=CURRENT_STOP from=$STOPS_ROWS}
					<tbody class="stopBlock">
						<tr class="fieldLabel" colspan="4">
							<td colspan="4" class="blockHeader">
								<img class="cursorPointer alignMiddle blockToggle stopToggle{if !($IS_HIDDEN)} hide {/if} "  src="{vimage_path('arrowRight.png')}" data-mode="hide" data-id='stop{$CURRENT_STOP['stopid']}'>
								<img class="cursorPointer alignMiddle blockToggle stopToggle{if ($IS_HIDDEN)} hide {/if}"  src="{vimage_path('arrowDown.png')}" data-mode="show" data-id='stop{$CURRENT_STOP['stopid']}'>
								<span class="stopTitle"><b>&nbsp;&nbsp;&nbsp;Stop {$STOP_INDEX+1}</b><a style="float: right; padding: 3px"><i title="Delete" class="deleteStopButton icon-trash"></i></a></span>
							</td>
						</tr>
						<tr colspan="4" class="stopContent">
							<td colspan="4" style="padding: 0px;">
								<table class="table equalSplit table-bordered" style="padding: 0px; border: 0px;">
									<tbody>
										<tr style="width:100%" colspan="4">
											<td class="fieldLabel medium" colspan="1" style="text-align:center;margin:auto">
												<label class="muted pull-right marginRight10px"><span class="redColor">*</span>{vtranslate('LBL_ORDERS_STOPDESCRIPTION', $MODULE)}</label>
											</td>
											<td class="fieldValue medium">
												<div class="row-fluid">
													<span class="span12">
														{$LOCALFIELDINFO.mandatory = true}
														{$LOCALFIELDINFO.name = 'stop_description_'|@cat:($STOP_INDEX+1)}
														{$LOCALFIELDINFO.label = 'LBL_ORDERS_STOPDESCRIPTION'}
														{$LOCALFIELDINFO.type = 'string'}
														{$INFO = Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($LOCALFIELDINFO))}
														<input id="stop_description_{$STOP_INDEX+1}" type="text" class="input-large" name="stop_description_{$STOP_INDEX+1}" value="{$CURRENT_STOP['stop_description']}" data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" data-fieldinfo={$INFO}>
													</span>
												</div>
											</td>
											<td class="fieldLabel medium" colspan="1" style="text-align:center;margin:auto">
												<label class="muted pull-right marginRight10px"><span class="redColor">*</span>{vtranslate('LBL_ORDERS_STOPSEQUENCE', $MODULE)}</label>
											</td>
											<td class="fieldValue medium">
												<div class="row-fluid">
													<span class="span12">
														{$LOCALFIELDINFO.mandatory = true}
														{$LOCALFIELDINFO.name = 'stop_sequence_'|@cat:($STOP_INDEX+1)}
														{$LOCALFIELDINFO.label = 'LBL_ORDERS_STOPSEQUENCE'}
														{$LOCALFIELDINFO.type = 'string'}
														{$INFO = Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($LOCALFIELDINFO))}
														<input id="stop_sequence_{$STOP_INDEX+1}" type="text" class="input-large" name="stop_sequence_{$STOP_INDEX+1}" value="{$CURRENT_STOP['stop_sequence']}" data-validation-engine="validate[required,custom[integer],funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" data-fieldinfo={$INFO}>
													</span>
												</div>
											</td>
										</tr>
										<tr style="width:100%" colspan="4">
											<td class="fieldLabel medium" colspan="1" style="text-align:center;margin:auto">
												<label class="muted pull-right marginRight10px">{vtranslate('LBL_ORDERS_STOPWEIGHT', $MODULE)}</label>
											</td>
											<td class="fieldValue medium">
												<div class="row-fluid">
													<span class="span12">
														<input id="stop_weight_{$STOP_INDEX+1}" type="text" class="input-large" name="stop_weight_{$STOP_INDEX+1}" value="{$CURRENT_STOP['stop_weight']}">
													</span>
												</div>
											</td>
											<td class="fieldLabel medium" colspan="1" style="text-align:center;margin:auto">
												<label class="muted pull-right marginRight10px">{vtranslate('LBL_ORDERS_STOPISPRIMARY', $MODULE)}</label>
											</td>
											<td class="fieldValue medium">
												<div class="row-fluid">
													<span class="span12">
														<input id="stop_isprimary_{$STOP_INDEX+1}" type="checkbox" class="input-large" name="stop_isprimary_{$STOP_INDEX+1}" {if $CURRENT_STOP['stop_isprimary'] eq '1' || $CURRENT_STOP['stop_isprimary'] eq 'yes' || $CURRENT_STOP['stop_isprimary'] eq 'on'}checked{/if}>
													</span>
												</div>
											</td>
										</tr>
										<tr style="width:100%" colspan="4">
											<td class="fieldLabel medium" colspan="1" style="text-align:center;margin:auto">
												<label class="muted pull-right marginRight10px">{vtranslate('LBL_ORDERS_STOPADDRESS1', $MODULE)}</label>
											</td>
											<td class="fieldValue medium">
												<div class="row-fluid">
													<span class="span12">
														<input id="stop_address1_{$STOP_INDEX+1}" type="text" class="input-large" name="stop_address1_{$STOP_INDEX+1}" value="{$CURRENT_STOP['stop_address1']}">
													</span>
												</div>
											</td>
											<td class="fieldLabel medium" colspan="1" style="text-align:center;margin:auto">
												<label class="muted pull-right marginRight10px">{vtranslate('LBL_ORDERS_STOPADDRESS2', $MODULE)}</label>
											</td>
											<td class="fieldValue medium">
												<div class="row-fluid">
													<span class="span12">
														<input id="stop_address2_{$STOP_INDEX+1}" type="text" class="input-large" name="stop_address2_{$STOP_INDEX+1}" value="{$CURRENT_STOP['stop_address2']}">
													</span>
												</div>
											</td>
										</tr>
										<tr style="width:100%" colspan="4">
											<td class="fieldLabel medium" colspan="1" style="text-align:center;margin:auto">
												<label class="muted pull-right marginRight10px">{vtranslate('LBL_ORDERS_STOPPHONE1', $MODULE)}<label>
											</td>
											<td class="fieldValue medium">
												<div class="row-fluid">
													<span class="span12">
														<input id="stop_phone1_{$STOP_INDEX+1}" type="text" class="input-large" name="stop_phone1_{$STOP_INDEX+1}" value="{$CURRENT_STOP['stop_phone1']}">
													</span>
												</div>
											</td>
											<td class="fieldLabel medium" colspan="1" style="text-align:center;margin:auto">
												<label class="muted pull-right marginRight10px">{vtranslate('LBL_ORDERS_STOPPHONE2', $MODULE)}</label>
											</td>
											<td class="fieldValue medium">
												<div class="row-fluid">
													<span class="span12">
														<input id="stop_phone2_{$STOP_INDEX+1}" type="text" class="input-large" name="stop_phone2_{$STOP_INDEX+1}" value="{$CURRENT_STOP['stop_phone2']}">
													</span>
												</div>
											</td>
										</tr>
										<tr style="width:100%" colspan="4">
											<td class="fieldLabel medium" colspan="1" style="text-align:center;margin:auto">
												<label class="muted pull-right marginRight10px">{vtranslate('LBL_ORDERS_STOPPHONE1TYPE', $MODULE)}</label>
											</td>
											<td class="fieldValue medium">
												<input type="hidden" name="stop_phonetype1_prev" value="none">
												<select class="chzn-select" style="text-align:left" id="stop_phonetype1_{$STOP_INDEX+1}" name="stop_phonetype1_{$STOP_INDEX+1}" data-fieldinfo="" data-selected-value="">
													<option value="" style="text-align:left">Select an Option</option>
													<option style="text-align:left" value="Home" {if $CURRENT_STOP['stop_phonetype1'] eq 'Home'}selected{/if}>Home</option>
													<option style="text-align:left" value="Work" {if $CURRENT_STOP['stop_phonetype1'] eq 'Work'}selected{/if}>Work</option>
													<option style="text-align:left" value="Cell" {if $CURRENT_STOP['stop_phonetype1'] eq 'Cell'}selected{/if}>Cell</option>
												</select>
											</td>
											<td class="fieldLabel medium" colspan="1" style="text-align:center;margin:auto">
												<label class="muted pull-right marginRight10px">{vtranslate('LBL_ORDERS_STOPPHONE2TYPE', $MODULE)}</label>
											</td>
											<td class="fieldValue medium">
												<input type="hidden" name="stop_phonetype2_prev" value="none">
												<select class="chzn-select" style="text-align:left" id="stop_phonetype2_{$STOP_INDEX+1}" name="stop_phonetype2_{$STOP_INDEX+1}" data-fieldinfo="" data-selected-value="">
													<option value="" style="text-align:left">Select an Option</option>
													<option style="text-align:left" value="Home" {if $CURRENT_STOP['stop_phonetype2'] eq 'Home'}selected{/if}>Home</option>
													<option style="text-align:left" value="Work" {if $CURRENT_STOP['stop_phonetype2'] eq 'Work'}selected{/if}>Work</option>
													<option style="text-align:left" value="Cell" {if $CURRENT_STOP['stop_phonetype2'] eq 'Cell'}selected{/if}>Cell</option>
												</select>
											</td>
										</tr>
										<tr style="width:100%" colspan="4">
											<td class="fieldLabel medium" colspan="1" style="text-align:center;margin:auto">
												<label class="muted pull-right marginRight10px">{vtranslate('LBL_ORDERS_STOPCITY', $MODULE)}</label>
											</td>
											<td class="fieldValue medium">
												<div class="row-fluid">
													<span class="span12">
														<input id="stop_city_{$STOP_INDEX+1}" type="text" class="input-large" name="stop_city_{$STOP_INDEX+1}" value="{$CURRENT_STOP['stop_city']}">
													</span>
												</div>
											</td>
											<td class="fieldLabel medium" colspan="1" style="text-align:center;margin:auto">
												<label class="muted pull-right marginRight10px">{vtranslate('LBL_ORDERS_STOPSTATE', $MODULE)}</label>
											</td>
											<td class="fieldValue medium">
												<div class="row-fluid">
													<span class="span12">
														<input id="stop_state_{$STOP_INDEX+1}" type="text" class="input-large" name="stop_state_{$STOP_INDEX+1}" value="{$CURRENT_STOP['stop_state']}">
													</span>
												</div>
											</td>
										</tr>
										<tr style="width:100%" colspan="4">
											<td class="fieldLabel medium" colspan="1" style="text-align:center;margin:auto">
												<label class="muted pull-right marginRight10px">{vtranslate('LBL_ORDERS_STOPZIP', $MODULE)}</label>
											</td>
											<td class="fieldValue medium">
												<div class="row-fluid">
													<span class="span12">
														<input id="stop_zip_{$STOP_INDEX+1}" type="text" class="input-large" name="stop_zip_{$STOP_INDEX+1}" value="{$CURRENT_STOP['stop_zip']}">
													</span>
												</div>
											</td>
											<td class="fieldLabel medium" colspan="1" style="text-align:center;margin:auto">
												<label class="muted pull-right marginRight10px">{vtranslate('LBL_ORDERS_STOPCOUNTRY', $MODULE)}</label>
											</td>
											<td class="fieldValue medium">
												<div class="row-fluid">
													<span class="span12">
														<input id="stop_country_{$STOP_INDEX+1}" type="text" class="input-large" name="stop_country_{$STOP_INDEX+1}" value="{$CURRENT_STOP['stop_country']}">
													</span>
												</div>
											</td>
										</tr>
										<tr style="width:100%" colspan="4">
											<td class="fieldLabel medium" colspan="1" style="text-align:center;margin:auto">
												<label class="muted pull-right marginRight10px">{vtranslate('LBL_ORDERS_STOPDATE', $MODULE)}</label>
											</td>
											<td class="fieldValue medium">
												<span class="span12">
													<div class="input-append row-fluid">
														<div class="row-fluid date">
															{$LOCALFIELDINFO.name = 'stop_date_'|@cat:($STOP_INDEX+1)}
															{$LOCALFIELDINFO.label = 'LBL_ORDERS_STOPDATE'}
															{$LOCALFIELDINFO.type = 'date'}
															{$INFO = Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($LOCALFIELDINFO))}
															<input id="{$MODULE}_editView_fieldName_stop_date_{$STOP_INDEX+1}" type="text" class="dateField input-large" name="stop_date_{$STOP_INDEX+1}" data-date-format="mm-dd-yyyy" value="{$CURRENT_STOP['stop_date']}" data-validation-engine="validate[funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" data-fieldinfo={$INFO}>
															<span class="add-on">
															<i class="icon-calendar"></i>
															</span>
														</div>
													</div>
												</span>
											</td>
										<td class="fieldLabel medium" colspan="1" style="text-align:center;margin:auto">
												<label class="muted pull-right marginRight10px">{vtranslate('LBL_ORDERS_STOPCONTACT', $MODULE)}</label>
											</td>
											<td class="fieldValue medium">
												<div class="row-fluid">
													<input class="stopReference referenceModule" name="popupReferenceModule" type="hidden" value="Contacts">
													<span class="span12">
														<div class="row-fluid input-prepend input-append">
															<input class="sourceField" name="stop_contact_{$STOP_INDEX+1}" type="hidden" value="{$CURRENT_STOP['stop_contact']}" data-displayvalue="{$CURRENT_STOP['stop_contact_name']}">
															<span class="add-on clearReferenceSelection cursorPointer">
																<i id="Opportunities_editView_fieldName_stop_contact_clear" class="icon-remove-sign" title="Clear"></i>
															</span>
															<input id="stop_contact_display" name="stop_contact_{$STOP_INDEX+1}_display" type="text" class="span7 marginLeftZero autoComplete ui-autocomplete-input stopReference referenceDisplay" readonly="true" data-validation-engine="validate[funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" placeholder="Type to search" autocomplete="off" role="textbox" aria-autocomplete="list" aria-haspopup="true" value="{$CURRENT_STOP['stop_contact_name']}">
															<span class="add-on relatedPopup cursorPointer">
																<i id="Opportunities_editView_fieldName_stop_contact_select" class="icon-search" title="Select"></i>
															</span>
															<span class="add-on cursorPointer createReferenceRecord">
																<i id="Opportunities_editView_fieldName_stop_contact_create" class="icon-plus" title="Create"></i>
															</span>
														</div>
													</span>
												</div>
											</td>
										</tr>
										<tr>
											<td class="fieldLabel medium">
												<label class="muted pull-right marginRight10px">{vtranslate('LBL_ORDERS_STOPTYPE', $MODULE)}</label>
											</td>
											<td class="fieldValue medium">
												<input type="hidden" name="stop_type_{$STOP_INDEX+1}" value="none">
												<select class="chzn-select stopField stopType" style="text-align:left" id="stop_type_{$STOP_INDEX+1}" name="stop_type_{$STOP_INDEX+1}" data-fieldinfo="" data-selected-value="{$CURRENT_STOP['stop_type']}">
													<option value="" style="text-align:left">Select an Option</option>
													<option style="text-align:left" value="Origin" {if $CURRENT_STOP['stop_type'] eq 'Origin'}selected{/if}>Origin</option>
													<option style="text-align:left" value="Destination" {if $CURRENT_STOP['stop_type'] eq 'Destination'}selected{/if}>Destination</option>
												</select>
											</td>
											<td class="fieldLabel medium">
												&nbsp;
											</td>
											<td class="fieldValue medium">
												<input id="stop_id_{$STOP_INDEX+1}" type="hidden" name="stop_id_{$STOP_INDEX+1}" value="{$CURRENT_STOP['stopid']}">
											</td>
										</tr>
									</tbody>
								</table>
							</td>
						</tr>
					</tbody>
				{/foreach}
			{else*}
				{assign var=COUNTER value=0}

				{foreach key=FIELD_NAME item=FIELD_MODEL from=$BLOCK_FIELDS name=blockfields}
					{if $FIELD_NAME eq 'origin_zone' || $FIELD_NAME eq 'empty_zone'}
						<input type="hidden" value="{$FIELD_MODEL->get('fieldvalue')}" name="{$FIELD_NAME}" id="{$FIELD_NAME}">
						{continue}
					{/if}
					{assign var="isReferenceField" value=$FIELD_MODEL->getFieldDataType()}
					{if $FIELD_MODEL->get('uitype') eq "20" or $FIELD_MODEL->get('uitype') eq "19"}
						{if $COUNTER eq '1'}
										<td class="{$WIDTHTYPE}"></td><td class="{$WIDTHTYPE}"></td>
									</tr>
									<tr>
							{assign var=COUNTER value=0}
						{/if}
					{/if}


					{* I start here *}
					{if  $BLOCK_LABEL eq 'LBL_ORDERS_DATES' or $BLOCK_LABEL eq 'LBL_ORDERS_WEIGHTS' }
						{if $COUNTER eq '3'}
							</tr>
							<tr>
							{assign var=COUNTER value=1}
						{else}
							{assign var=COUNTER value=$COUNTER+1}
						{/if}
					{else}
						{if $COUNTER eq 2}
							</tr>
							<tr>
							{assign var=COUNTER value=1}
						{else}
							{assign var=COUNTER value=$COUNTER+1}
						{/if}
					{/if}
					{* I end here *}

					<td class="fieldLabel {$WIDTHTYPE}" {if $BLOCK_LABEL eq 'LBL_ORDERS_DATES' or 'LBL_ORDERS_WEIGHTS'}style='width:14%'{/if}>
						{if $isReferenceField neq "reference"}<label class="muted pull-right marginRight10px">{/if}
							{if $FIELD_MODEL->isMandatory() eq true && $isReferenceField neq "reference"} <span class="redColor">*</span> {/if}
							{if $isReferenceField eq "reference"}
								{assign var="REFERENCE_LIST" value=$FIELD_MODEL->getReferenceList()}
								{assign var="REFERENCE_LIST_COUNT" value=count($REFERENCE_LIST)}
								{if $REFERENCE_LIST_COUNT > 1}
									{assign var="DISPLAYID" value=$FIELD_MODEL->get('fieldvalue')}
									{assign var="REFERENCED_MODULE_STRUCT" value=$FIELD_MODEL->getUITypeModel()->getReferenceModule($DISPLAYID)}
									{if !empty($REFERENCED_MODULE_STRUCT)}
										{assign var="REFERENCED_MODULE_NAME" value=$REFERENCED_MODULE_STRUCT->get('name')}
									{/if}
									<span class="pull-right">
										{if $FIELD_MODEL->isMandatory() eq true} <span class="redColor">*</span> {/if}
										<select id="{$MODULE}_editView_fieldName_{$FIELD_MODEL->getName()}_dropDown" class="chzn-select referenceModulesList streched" style="width:160px;">
											<optgroup>
												{foreach key=index item=value from=$REFERENCE_LIST}
													<option value="{$value}" {if $value eq $REFERENCED_MODULE_NAME} selected {/if}>{vtranslate($value, $MODULE)}</option>
												{/foreach}
											</optgroup>
										</select>
									</span>
								{else}
									<label class="muted pull-right marginRight10px">{if $FIELD_MODEL->isMandatory() eq true} <span class="redColor">*</span> {/if}{vtranslate($FIELD_MODEL->get('label'), $MODULE)}</label>
								{/if}
							{else if $FIELD_MODEL->get('uitype') eq "83"}
								{include file=vtemplate_path($FIELD_MODEL->getUITypeModel()->getTemplateName(),$MODULE) COUNTER=$COUNTER MODULE=$MODULE}
							{else}
								{vtranslate($FIELD_MODEL->get('label'), $MODULE)}
							{/if}
						{if $isReferenceField neq "reference"}</label>{/if}
					</td>
					{if $FIELD_MODEL->get('uitype') neq "83"}
						<td class="fieldValue {$WIDTHTYPE}" {if $BLOCK_LABEL eq 'LBL_ORDERS_DATES' or $BLOCK_LABEL eq 'LBL_ORDERS_WEIGHTS'}style='width:16.6%'{/if} {if $FIELD_MODEL->get('uitype') eq '19' or $FIELD_MODEL->get('uitype') eq '20'} colspan="3" {assign var=COUNTER value=$COUNTER+1} {/if}>
							<div class="row-fluid">
								<span class="span10">
									 {if $FIELD_MODEL->get('name') == 'tariff_id'}
										 <input type="hidden" id="tariff_customjs" value="{$EFFECTIVE_TARIFF_CUSTOMJS}">
										 <input type="hidden" disabled id="allAvailableTariffs" value="{$AVAILABLE_TARIFFS}">
										 <input type="hidden" id="effective_tariff_custom_type" name="effective_tariff_custom_type" value="{$EFFECTIVE_TARIFF_CUSTOMTYPE}">
									 {/if}
									{include file=vtemplate_path($FIELD_MODEL->getUITypeModel()->getTemplateName(),$MODULE) BLOCK_FIELDS=$BLOCK_FIELDS}
								</span>
							</div>
						</td>
					{/if}
					{if $BLOCK_FIELDS|@count eq 1 and $FIELD_MODEL->get('uitype') neq "19" and $FIELD_MODEL->get('uitype') neq "20" and $FIELD_MODEL->get('uitype') neq "30" and $FIELD_MODEL->get('name') neq "recurringtype"}
						<td class="{$WIDTHTYPE}"></td><td class="{$WIDTHTYPE}"></td>
					{/if}
					{if $MODULE eq 'Events' && $BLOCK_LABEL eq 'LBL_EVENT_INFORMATION' && $smarty.foreach.blockfields.last }
						{include file=vtemplate_path('uitypes/FollowUp.tpl',$MODULE) COUNTER=$COUNTER}
					{/if}
				{/foreach}

		{* adding additional column for odd number of fields in a block *}
		{if $BLOCK_FIELDS|@end eq true and $BLOCK_FIELDS|@count neq 1 and $COUNTER eq 1}
			<td class="fieldLabel {$WIDTHTYPE}">&nbsp;</td><td class="{$WIDTHTYPE}">&nbsp;</td>
		{/if}

		{if  $BLOCK_LABEL eq 'LBL_ORDERS_DATES' or $BLOCK_LABEL eq 'LBL_ORDERS_WEIGHTS' }
			{if $BLOCK_FIELDS|@end eq true and $BLOCK_FIELDS|@count neq 2 and $COUNTER neq 0}
				<td class="fieldLabel {$WIDTHTYPE}"></td><td class="{$WIDTHTYPE}"></td>
			{/if}
		{/if}

			</tr>
</tbody>
			</table>
			<br>
			{if $MODULE eq 'Orders' && $BLOCK_LABEL eq 'LBL_ORDERS_BLOCK_VALUATION'}
                {if getenv('INSTANCE_NAME') neq 'graebel'}
                    {if isParticipantForRecord($RECORD_ID)}
                        {include file=vtemplate_path('participatingAgentsDetail.tpl', 'ParticipatingAgents')}
                    {else}
                        {include file=vtemplate_path('participatingAgentsEdit.tpl', 'ParticipatingAgents')}
                    {/if}
                {/if}
				{*{include file=vtemplate_path('extraStopsEdit.tpl', 'ExtraStops')}*}
				{include file=vtemplate_path('GuestEditBlocks.tpl', $MODULE)}
				{* {include file=vtemplate_path('EditBlock.tpl', 'MoveRoles') GUEST_MODULE='MoveRoles'}
				{include file=vtemplate_path('EditBlock.tpl', 'OrdersMilestone') GUEST_MODULE='OrdersMilestone'} *}
			{/if}
			{include file=vtemplate_path('SequencedGuestEditBlocks.tpl', $MODULE) BLOCK_LABEL=$BLOCK_LABEL}
		{/foreach}
{/strip}
