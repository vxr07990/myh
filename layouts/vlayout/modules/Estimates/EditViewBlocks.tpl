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
{assign var=IS_HIDDEN value='0'}
<div class='editViewContainer container-fluid' data-movetype="{$MOVE_TYPE}" data-lockfields="{$LOCK_ESTIMATE}">
	<form novalidate class="form-horizontal recordEditView" id="EditView" name="EditView" method="post" action="index.php" enctype="multipart/form-data">
        <input type="hidden" name="currentBrand" value="Default Value Please Ignore." />
		<input type="hidden" name="hasUnratedChanges" value="0" />
		<input type="hidden" name="contractValuationOverride" value="0" />
		<input type="hidden" name="contractFlatRateAuto" value="0" />
		{assign var=WIDTHTYPE value=$USER_MODEL->get('rowheight')}
		{if !empty($PICKIST_DEPENDENCY_DATASOURCE)}
			<input type="hidden" name="picklistDependency" value='{Vtiger_Util_Helper::toSafeHTML($PICKIST_DEPENDENCY_DATASOURCE)}' />
		{/if}
		<input type="hidden" name="module" value="{$MODULE}" />
		<input type="hidden" id="primary-present" value="{$PRIMARY_ESTIMATES}" />
		<input type="hidden" name="action" value="Save" />
		<input type="hidden" name="record" value="{$RECORD_ID}" />
		<input type="hidden" name="instance" value="{getenv('INSTANCE_NAME')}" />
		<input type="hidden" name="duplicate" value="{$IS_DUPLICATE}" />
		{*<input type="hidden" name="pcLock" value="{$PRICING_COLOR_LOCKED}" />*}
		{if $IS_RELATION_OPERATION }
			<input type="hidden" name="sourceModule" value="{$SOURCE_MODULE}" />
			<input type="hidden" name="sourceRecord" value="{$SOURCE_RECORD}" />
			<input type="hidden" name="relationOperation" value="{$IS_RELATION_OPERATION}" />
		{/if}
		{*<h1>RELATION OPERATION: {$IS_RELATION_OPERATION}</h1>*}
		<div class="contentHeader row-fluid">
		{assign var=SINGLE_MODULE_NAME value='SINGLE_'|cat:$MODULE}
		{if $RECORD_ID neq ''}
            <div class="span6">
                <h3 title="{vtranslate('LBL_EDITING', $MODULE)} {vtranslate($SINGLE_MODULE_NAME, $MODULE)} {$RECORD_STRUCTURE_MODEL->getRecordName()}">{vtranslate('LBL_EDITING', $MODULE)} {vtranslate($SINGLE_MODULE_NAME, $MODULE)} - {$RECORD_STRUCTURE_MODEL->getRecordName()}</h3>
            </div>
        {else}
            <div class="span6">
                <h3>{vtranslate('LBL_CREATING_NEW', $MODULE)} {vtranslate($SINGLE_MODULE_NAME, $MODULE)}</h3>
            </div>
		{/if}
            <div class="span6">
                <div class="pull-right">
                    <button class="btn btn-success ieBtn" value="submit" type="submit"><strong>{vtranslate('LBL_SAVE', $MODULE)}</strong></button>
                    <a class="cancelLink" type="reset" onclick="javascript:window.history.back();">{vtranslate('LBL_CANCEL', $MODULE)}</a>
                </div>
            </div>
		</div>

		{foreach key=BLOCK_LABEL item=BLOCK_FIELDS from=$RECORD_STRUCTURE name="EditViewBlockLevelLoop"}
			{if $BLOCK_LABEL eq "LBL_ADDRESS_INFORMATION" and $IS_ACTIVE_ADDRESSLIST == true}
				{include file=vtemplate_path('AddressListEdit.tpl', 'AddressList')}
				{continue}
			{/if}
			{if $BLOCK_LABEL eq "LBL_QUOTES_SITDETAILS"}{*{include file='layouts/vlayout/modules/Estimates/RateEstimateEdit.tpl' WIDTHTYPE="$WIDTHTYPE"}*}<div id='inline_content'></div>{continue}

			{elseif $BLOCK_LABEL eq "LBL_QUOTES_ACCESSORIALDETAILS" or $BLOCK_LABEL eq "LBL_ESTIMATES_APPLIANCE" or $BLOCK_LABEL eq "LBL_QUOTES_LONGCARRY" or $BLOCK_LABEL eq "LBL_QUOTES_STAIR" or $BLOCK_LABEL eq "LBL_QUOTES_ELEVATOR" or $BLOCK_LABEL eq "LBL_QUOTES_SITDETAILS2" or $BLOCK_LABEL eq "LBL_SPACE_RESERVATION"}{continue}{/if}
			{if $BLOCK_LABEL eq "LBL_QUOTES_INTERSTATE_SERVICECHARGES"}{include file=vtemplate_path('InterstateServiceCharges.tpl',$MODULE)}{/if}
			{if $BLOCK_LABEL neq "LBL_QUOTES_LOCALMOVEDETAILS" and $BLOCK_FIELDS|@count lte 0}{continue}{/if}
			{if $BLOCK_LABEL eq 'LBL_ESTIMATES_DATES' AND  getenv('INSTANCE_NAME') neq 'sirva'}{continue}{/if}
			{if $BLOCK_LABEL eq 'LBL_POTENTIALS_DATES' AND  getenv('INSTANCE_NAME') neq 'sirva'}{continue}{/if}

			{*if $BLOCK_LABEL eq 'LBL_QUOTES_INTERSTATEMOVEDETAILS' && getenv('INSTANCE_NAME') eq 'sirva' && 'move_type'|array_key_exists:$BLOCK_FIELDS*}
			{*no move_type at this point apparently*}
			{* adding a hidden block that I can switch to for military types *}
			{*
			{if $BLOCK_LABEL eq 'LBL_QUOTES_INTERSTATEMOVEDETAILS' && getenv('INSTANCE_NAME') eq 'sirva'}
				<table name='{$BLOCK_LABEL}_MILITARY' class="table table-bordered blockContainer showInlineTable hide">
					<tr>
						<th class="blockHeader" colspan="4">{vtranslate($BLOCK_LABEL, $MODULE)}</th>
					</tr>
                	<script type='text/javascript' src='libraries/jquery/colorbox/jquery.colorbox-min.js'></script>
					{* @TODO: this needs table aligned *}
					{*
                	<tr><td class='fieldLabel'></td><td class='fieldValue'>&nbsp;</td><td class='fieldLabel'></td><td class='fieldValue'><button type='button' class='interstateRateDetail'>Detailed Rate Estimate</button></td></tr>
                </table>
            {/if}
            *}
			<table name='{$BLOCK_LABEL}' class="table table-bordered blockContainer showInlineTable{if $BLOCK_LABEL eq "LBL_QUOTES_TPGPRICELOCK"} hide{/if} {hide_hidden_block block_label=$BLOCK_LABEL hidden_blocks=$HIDDEN_BLOCKS tariff_blocks=$TARIFF_BLOCKS}">
			<thead>
				<tr>
					<th class="blockHeader" colspan="20">
						 <img class="cursorPointer alignMiddle blockToggle {if !($IS_HIDDEN)} hide {/if} "  src="{vimage_path('arrowRight.png')}" data-mode="hide">
						 <img class="cursorPointer alignMiddle blockToggle {if ($IS_HIDDEN)} hide {/if}"  src="{vimage_path('arrowDown.png')}" data-mode="show">
						{vtranslate($BLOCK_LABEL, $MODULE)}
					</th>
				</tr>
			</thead>
			{*OLD STOPS*}
			{*if $BLOCK_LABEL eq 'LBL_ESTIMATES_EXTRASTOPS'}
					<tr class="fieldLabel" colspan="4">
						<td colspan="4"><button type="button" name="addStop" id="addStop">+</button><input type="hidden" id="numStops" name="numStops" value="{$STOPS_ROWS|@count}"><button type="button" name="addStop2" id="addStop2" style="clear:right;float:right">+</button></td>
					</tr>
				<tbody class="defaultStop stopBlock hide">
					<tr class="fieldLabel" colspan="4">
						<td colspan="4" class="blockHeader">
							<img class="cursorPointer alignMiddle stopToggle{if !($IS_HIDDEN)} hide {/if} "  src="{vimage_path('arrowRight.png')}" data-mode="hide" data-id='defaultStop'>
							<img class="cursorPointer alignMiddle stopToggle{if ($IS_HIDDEN)} hide {/if}"  src="{vimage_path('arrowDown.png')}" data-mode="show" data-id='defaultStop'>
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
											<label class="muted pull-right marginRight10px"><span class="redColor">*</span>{vtranslate('LBL_ESTIMATES_STOPDESCRIPTION', $MODULE)}</label>
										</td>
										<td class="fieldValue medium">
											<div class="row-fluid">
												<span class="span12">
													{$LOCALFIELDINFO.mandatory = true}
													{$LOCALFIELDINFO.name = 'stop_description'}
													{$LOCALFIELDINFO.label = 'LBL_ESTIMATES_STOPDESCRIPTION'}
													{$LOCALFIELDINFO.type = 'string'}
													{$INFO = Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($LOCALFIELDINFO))}
													<input id="stop_description" type="text" class="input-large" name="stop_description" value="" data-fieldinfo={$INFO} data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]">
												</span>
											</div>
										</td>
										<td class="fieldLabel medium" colspan="1" style="text-align:center;margin:auto">
											<label class="muted pull-right marginRight10px"><span class="redColor">*</span>{vtranslate('LBL_ESTIMATES_STOPSEQUENCE', $MODULE)}</label>
										</td>
										<td class="fieldValue medium">
											<div class="row-fluid">
												<span class="span12">
													{$LOCALFIELDINFO.mandatory = true}
													{$LOCALFIELDINFO.name = 'stop_sequence'}
													{$LOCALFIELDINFO.label = 'LBL_ESTIMATES_STOPSEQUENCE'}
													{$LOCALFIELDINFO.type = 'string'}
													{$INFO = Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($LOCALFIELDINFO))}
													<input id="stop_sequence" type="text" class="input-large" name="stop_sequence" data-fieldinfo={$INFO} data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]">
												</span>
											</div>
										</td>
									</tr>
									<tr style="width:100%" colspan="4">
										<td class="fieldLabel medium" colspan="1" style="text-align:center;margin:auto">
											<label class="muted pull-right marginRight10px">{vtranslate('LBL_ESTIMATES_STOPWEIGHT', $MODULE)}</label>
										</td>
										<td class="fieldValue medium">
											<div class="row-fluid">
												<span class="span12">
													<input id="stop_weight" type="text" class="input-large" name="stop_weight" value="">
												</span>
											</div>
										</td>
										<td class="fieldLabel medium" colspan="1" style="text-align:center;margin:auto">
											<label class="muted pull-right marginRight10px">{vtranslate('LBL_ESTIMATES_STOPISPRIMARY', $MODULE)}</label>
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
											<label class="muted pull-right marginRight10px">{vtranslate('LBL_ESTIMATES_STOPADDRESS1', $MODULE)}</label>
										</td>
										<td class="fieldValue medium">
											<div class="row-fluid">
												<span class="span12">
													<input id="stop_address1" type="text" class="input-large" name="stop_address1" value="">
												</span>
											</div>
										</td>
										<td class="fieldLabel medium" colspan="1" style="text-align:center;margin:auto">
											<label class="muted pull-right marginRight10px">{vtranslate('LBL_ESTIMATES_STOPADDRESS2', $MODULE)}</label>
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
											<label class="muted pull-right marginRight10px">{vtranslate('LBL_ESTIMATES_STOPPHONE1', $MODULE)}<label>
										</td>
										<td class="fieldValue medium">
											<div class="row-fluid">
												<span class="span12">
													<input id="stop_phone1" type="text" class="input-large" name="stop_phone1" value="">
												</span>
											</div>
										</td>
										<td class="fieldLabel medium" colspan="1" style="text-align:center;margin:auto">
											<label class="muted pull-right marginRight10px">{vtranslate('LBL_ESTIMATES_STOPPHONE2', $MODULE)}</label>
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
											<label class="muted pull-right marginRight10px">{vtranslate('LBL_ESTIMATES_STOPPHONE1TYPE', $MODULE)}</label>
										</td>
										<td class="fieldValue medium">
											<input type="hidden" name="stop_phonetype1_prev" value="none">
											<select class="chzn-done" style="text-align:left" id="stop_phonetype1" name="stop_phonetype1">
												<option value="" style="text-align:left">Select an Option</option>
												<option style="text-align:left" value="Home">Home</option>
												<option style="text-align:left" value="Work">Work</option>
												<option style="text-align:left" value="Cell">Cell</option>
											</select>
										</td>
										<td class="fieldLabel medium" colspan="1" style="text-align:center;margin:auto">
											<label class="muted pull-right marginRight10px">{vtranslate('LBL_ESTIMATES_STOPPHONE2TYPE', $MODULE)}</label>
										</td>
										<td class="fieldValue medium">
											<input type="hidden" name="stop_phonetype2_prev" value="none">
											<select class="chzn-done" style="text-align:left" id="stop_phonetype2" name="stop_phonetype2" data-fieldinfo="" data-selected-value="">
												<option value="" style="text-align:left">Select an Option</option>
												<option style="text-align:left" value="Home">Home</option>
												<option style="text-align:left" value="Work">Work</option>
												<option style="text-align:left" value="Cell">Cell</option>
											</select>
										</td>
									</tr>
									<tr style="width:100%" colspan="4">
										<td class="fieldLabel medium" colspan="1" style="text-align:center;margin:auto">
											<label class="muted pull-right marginRight10px">{vtranslate('LBL_ESTIMATES_STOPCITY', $MODULE)}</label>
										</td>
										<td class="fieldValue medium">
											<div class="row-fluid">
												<span class="span12">
													<input id="stop_city" type="text" class="input-large" name="stop_city" value="">
												</span>
											</div>
										</td>
										<td class="fieldLabel medium" colspan="1" style="text-align:center;margin:auto">
											<label class="muted pull-right marginRight10px">{vtranslate('LBL_ESTIMATES_STOPSTATE', $MODULE)}</label>
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
											<label class="muted pull-right marginRight10px">{vtranslate('LBL_ESTIMATES_STOPZIP', $MODULE)}</label>
										</td>
										<td class="fieldValue medium">
											<div class="row-fluid">
												<span class="span12">
													<input id="stop_zip" type="text" class="input-large" name="stop_zip" value="">
												</span>
											</div>
										</td>
										<td class="fieldLabel medium" colspan="1" style="text-align:center;margin:auto">
											<label class="muted pull-right marginRight10px">{vtranslate('LBL_ESTIMATES_STOPCOUNTRY', $MODULE)}</label>
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
											<label class="muted pull-right marginRight10px">{vtranslate('LBL_ESTIMATES_STOPDATE', $MODULE)}</label>
										</td>
										<td class="fieldValue medium">
											<span class="span12">
												<div class="input-append row-fluid">
													<div class="row-fluid date">
														{$LOCALFIELDINFO.name = 'stop_date'}
														{$LOCALFIELDINFO.label = 'LBL_ESTIMATES_STOPDATE'}
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
											<label class="muted pull-right marginRight10px">{vtranslate('LBL_ESTIMATES_STOPCONTACT', $MODULE)}</label>
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
											<label class="muted pull-right marginRight10px">{vtranslate('LBL_ESTIMATES_STOPTYPE', $MODULE)}</label>
										</td>
										<td class="fieldValue medium">
											<input type="hidden" name="stop_type" value="none">
											<select class="chzn-done stopType" style="text-align:left" id="stop_type" name="stop_type" data-selected-value="">
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
											{if getenv('INSTANCE_NAME') eq 'sirva'}
												<input id="sirva_stop_type" type="hidden" name="sirva_stop_type" value="none">
											{/if}
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
								<img class="cursorPointer alignMiddle stopToggle{if !($IS_HIDDEN)} hide {/if} "  src="{vimage_path('arrowRight.png')}" data-mode="hide" data-id='stop{$CURRENT_STOP['stopid']}'>
								<img class="cursorPointer alignMiddle stopToggle{if ($IS_HIDDEN)} hide {/if}"  src="{vimage_path('arrowDown.png')}" data-mode="show" data-id='stop{$CURRENT_STOP['stopid']}'>
								<span class="stopTitle"><b>&nbsp;&nbsp;&nbsp;Stop {$STOP_INDEX+1}</b><a style="float: right; padding: 3px"><i title="Delete" class="deleteStopButton icon-trash"></i></a></span>
							</td>
						</tr>
						<tr colspan="4" class="stopContent">
							<td colspan="4" style="padding: 0px;">
								<table class="table equalSplit table-bordered" style="padding: 0px; border: 0px;">
									<tbody>
										<tr style="width:100%" colspan="4">
											<td class="fieldLabel medium" colspan="1" style="text-align:center;margin:auto">
												<label class="muted pull-right marginRight10px"><span class="redColor">*</span>{vtranslate('LBL_ESTIMATES_STOPDESCRIPTION', $MODULE)}</label>
											</td>
											<td class="fieldValue medium">
												<div class="row-fluid">
													<span class="span12">
														{$LOCALFIELDINFO.mandatory = true}
														{$LOCALFIELDINFO.name = 'stop_description_'|@cat:($STOP_INDEX+1)}
														{$LOCALFIELDINFO.label = 'LBL_OPPORTUNITY_STOPDESCRIPTION'}
														{$LOCALFIELDINFO.type = 'string'}
														{$INFO = Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($LOCALFIELDINFO))}
														<input id="stop_description_{$STOP_INDEX+1}" type="text" class="input-large" name="stop_description_{$STOP_INDEX+1}" value="{$CURRENT_STOP['stop_description']}" data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" data-fieldinfo={$INFO}>
													</span>
												</div>
											</td>
											<td class="fieldLabel medium" colspan="1" style="text-align:center;margin:auto">
												<label class="muted pull-right marginRight10px"><span class="redColor">*</span>{vtranslate('LBL_ESTIMATES_STOPSEQUENCE', $MODULE)}</label>
											</td>
											<td class="fieldValue medium">
												<div class="row-fluid">
													<span class="span12">
														{$LOCALFIELDINFO.mandatory = true}
														{$LOCALFIELDINFO.name = 'stop_sequence_'|@cat:($STOP_INDEX+1)}
														{$LOCALFIELDINFO.label = 'LBL_OPPORTUNITY_STOPSEQUENCE'}
														{$LOCALFIELDINFO.type = 'string'}
														{$INFO = Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($LOCALFIELDINFO))}
														<input id="stop_sequence_{$STOP_INDEX+1}" type="text" class="input-large" name="stop_sequence_{$STOP_INDEX+1}" value="{$CURRENT_STOP['stop_sequence']}" data-validation-engine="validate[required,custom[integer],funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" data-fieldinfo={$INFO}>
													</span>
												</div>
											</td>
										</tr>
										<tr style="width:100%" colspan="4">
											<td class="fieldLabel medium" colspan="1" style="text-align:center;margin:auto">
												<label class="muted pull-right marginRight10px">{vtranslate('LBL_ESTIMATES_STOPWEIGHT', $MODULE)}</label>
											</td>
											<td class="fieldValue medium">
												<div class="row-fluid">
													<span class="span12">
														<input id="stop_weight_{$STOP_INDEX+1}" type="text" class="input-large" name="stop_weight_{$STOP_INDEX+1}" value="{$CURRENT_STOP['stop_weight']}">
													</span>
												</div>
											</td>
											<td class="fieldLabel medium" colspan="1" style="text-align:center;margin:auto">
												<label class="muted pull-right marginRight10px">{vtranslate('LBL_ESTIMATES_STOPISPRIMARY', $MODULE)}</label>
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
												<label class="muted pull-right marginRight10px">{vtranslate('LBL_ESTIMATES_STOPADDRESS1', $MODULE)}</label>
											</td>
											<td class="fieldValue medium">
												<div class="row-fluid">
													<span class="span12">
														<input id="stop_address1_{$STOP_INDEX+1}" type="text" class="input-large" name="stop_address1_{$STOP_INDEX+1}" value="{$CURRENT_STOP['stop_address1']}">
													</span>
												</div>
											</td>
											<td class="fieldLabel medium" colspan="1" style="text-align:center;margin:auto">
												<label class="muted pull-right marginRight10px">{vtranslate('LBL_ESTIMATES_STOPADDRESS2', $MODULE)}</label>
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
												<label class="muted pull-right marginRight10px">{vtranslate('LBL_ESTIMATES_STOPPHONE1', $MODULE)}<label>
											</td>
											<td class="fieldValue medium">
												<div class="row-fluid">
													<span class="span12">
														<input id="stop_phone1_{$STOP_INDEX+1}" type="text" class="input-large" name="stop_phone1_{$STOP_INDEX+1}" value="{$CURRENT_STOP['stop_phone1']}">
													</span>
												</div>
											</td>
											<td class="fieldLabel medium" colspan="1" style="text-align:center;margin:auto">
												<label class="muted pull-right marginRight10px">{vtranslate('LBL_ESTIMATES_STOPPHONE2', $MODULE)}</label>
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
												<label class="muted pull-right marginRight10px">{vtranslate('LBL_ESTIMATES_STOPPHONE1TYPE', $MODULE)}</label>
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
												<label class="muted pull-right marginRight10px">{vtranslate('LBL_ESTIMATES_STOPPHONE2TYPE', $MODULE)}</label>
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
												<label class="muted pull-right marginRight10px">{vtranslate('LBL_ESTIMATES_STOPCITY', $MODULE)}</label>
											</td>
											<td class="fieldValue medium">
												<div class="row-fluid">
													<span class="span12">
														<input id="stop_city_{$STOP_INDEX+1}" type="text" class="input-large" name="stop_city_{$STOP_INDEX+1}" value="{$CURRENT_STOP['stop_city']}">
													</span>
												</div>
											</td>
											<td class="fieldLabel medium" colspan="1" style="text-align:center;margin:auto">
												<label class="muted pull-right marginRight10px">{vtranslate('LBL_ESTIMATES_STOPSTATE', $MODULE)}</label>
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
												<label class="muted pull-right marginRight10px">{vtranslate('LBL_ESTIMATES_STOPZIP', $MODULE)}</label>
											</td>
											<td class="fieldValue medium">
												<div class="row-fluid">
													<span class="span12">
														<input id="stop_zip_{$STOP_INDEX+1}" type="text" class="input-large" name="stop_zip_{$STOP_INDEX+1}" value="{$CURRENT_STOP['stop_zip']}">
													</span>
												</div>
											</td>
											<td class="fieldLabel medium" colspan="1" style="text-align:center;margin:auto">
												<label class="muted pull-right marginRight10px">{vtranslate('LBL_ESTIMATES_STOPCOUNTRY', $MODULE)}</label>
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
												<label class="muted pull-right marginRight10px">{vtranslate('LBL_ESTIMATES_STOPDATE', $MODULE)}</label>
											</td>
											<td class="fieldValue medium">
												<span class="span12">
													<div class="input-append row-fluid">
														<div class="row-fluid date">
															{$LOCALFIELDINFO.name = 'stop_date_'|@cat:($STOP_INDEX+1)}
															{$LOCALFIELDINFO.label = 'LBL_OPPORTUNITY_STOPDATE'}
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
												<label class="muted pull-right marginRight10px">{vtranslate('LBL_ESTIMATES_STOPCONTACT', $MODULE)}</label>
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
												<label class="muted pull-right marginRight10px">{vtranslate('LBL_ESTIMATES_STOPTYPE', $MODULE)}</label>
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
												{if getenv('INSTANCE_NAME') eq 'sirva'}
												<input id="sirva_stop_type_{$STOP_INDEX+1}" type="hidden" name="sirva_stop_type_{$STOP_INDEX+1}" value="{$CURRENT_STOP['stopid']}">
												{/if}
											</td>
										</tr>
									</tbody>
								</table>
							</td>
						</tr>
					</tbody>
				{/foreach}
			{/if*}
			{if ($BLOCK_LABEL eq 'LBL_ADDRESS_INFORMATION') and ($MODULE neq 'PurchaseOrder') }
				<!--<tr>
				<td class="fieldLabel {$WIDTHTYPE}" name="copyHeader1">
					<label class="muted pull-right marginRight10px" name="togglingHeader">{vtranslate('LBL_BILLING_ADDRESS_FROM', $MODULE)}</label>
				</td>
				<td class="fieldValue {$WIDTHTYPE}" name="copyAddress1">
					<div class="row-fluid">
						<div class="span5">
							<span class="row-fluid margin0px">
								<label class="radio">
								  <input type="radio" name="copyAddressFromRight" class="accountAddress" data-copy-address="billing" checked="">{vtranslate('SINGLE_Accounts', $MODULE)}
								</label>
							</span>
							<span class="row-fluid margin0px">
								<label class="radio">
								  <input type="radio" name="copyAddressFromRight" class="contactAddress" data-copy-address="billing" checked="">{vtranslate('SINGLE_Contacts', $MODULE)}
								</label>
							</span>
							<span class="row-fluid margin0px" name="togglingAddressContainerRight">
								<label class="radio">
							  <input type="radio" name="copyAddressFromRight" class="shippingAddress" data-target="shipping" checked="">{vtranslate('Shipping Address', $MODULE)}
								</label>
							</span>
							<span class="row-fluid margin0px hide" name="togglingAddressContainerLeft">
								<label class="radio">
							  <input type="radio" name="copyAddressFromRight"  class="billingAddress" data-target="billing" checked="">{vtranslate('Billing Address', $MODULE)}
								</label>
							</span>
						</div>
					</div>
				</td>
				<td class="fieldLabel {$WIDTHTYPE}" name="copyHeader2">
					<label class="muted pull-right marginRight10px" name="togglingHeader">{vtranslate('LBL_SHIPPING_ADDRESS_FROM', $MODULE)}</label>
				</td>
				<td class="fieldValue {$WIDTHTYPE}" name="copyAddress2">
					<div class="row-fluid">
						<div class="span5">
							<span class="row-fluid margin0px">
								<label class="radio">
								  <input type="radio" name="copyAddressFromLeft" class="accountAddress" data-copy-address="shipping" checked="">{vtranslate('SINGLE_Accounts', $MODULE)}
								</label>
							</span>
							<span class="row-fluid margin0px">
								<label class="radio">
								  <input type="radio" name="copyAddressFromLeft" class="contactAddress" data-copy-address="shipping" checked="">{vtranslate('SINGLE_Contacts', $MODULE)}
								</label>
							</span>
							<span class="row-fluid margin0px" name="togglingAddressContainerLeft">
								<label class="radio">
							  <input type="radio" name="copyAddressFromLeft" class="billingAddress" data-target="billing" checked="">{vtranslate('Billing Address', $MODULE)}
								</label>
							</span>
							<span class="row-fluid margin0px hide" name="togglingAddressContainerRight">
								<label class="radio">
							  <input type="radio" name="copyAddressFromLeft" class="shippingAddress" data-target="shipping" checked="">{vtranslate('Shipping Address', $MODULE)}
								</label>
							</span>
						</div>
					</div>
				</td>
			</tr>-->
			{/if}
			<tr>
			{assign var=COUNTER value=0}

			{foreach key=FIELD_NAME item=FIELD_MODEL from=$BLOCK_FIELDS name=blockfields}
				{if
					($FIELD_NAME eq 'increased_base') ||
					($FIELD_NAME eq 'declared_value') ||
					($FIELD_NAME eq 'valuation_flat_charge') ||
					($FIELD_NAME eq 'free_valuation_type')
				}{continue}{/if}

				{if $FIELD_NAME eq 'bottom_line_discount'}
					{*
						this will make the bottom_line_discount 0 if it's NOT set and not 0.
						this is the solution to the error that it must be set without having
						to edit each place that checks.
					*}
					{if !$FIELD_MODEL->get('fieldvalue') && $FIELD_MODEL->get('fieldvalue') !== 0}
						{$FIELD_MODEL = $FIELD_MODEL->set('fieldvalue', '0')}
					{/if}
				{/if}
				{* I added the effective_tariff field to the INTERSTATE block so it would build in pseudo_save.
				 So I have to skip it here, in order to show the correct select box.
				 Maybe I should have added the field to a block that was always hidden?
				 *}
				{if $FIELD_NAME eq 'effective_tariff'}{continue}{/if}
				{* {if $FIELD_NAME eq 'interstate_effective_date' && !$RECORD_ID}
					<h1>DATE: {$CURRENT_DATE|@debug_print_var}</h1>
					{$FIELD_MODEL->set('fieldvalue', '1990-01-01')}
				{/if} *}
				{if $FIELD_NAME eq 'business_line_est' && 'move_type'|array_key_exists:$BLOCK_FIELDS}
                    {continue}
				{/if}
				{if $FIELD_NAME eq 'apply_free_fvp' && $COUNTER neq 2}
					<td class="fieldLabel">&nbsp;</td>
					<td>&nbsp;</td>
					{assign var=COUNTER value=2}
				{/if}

				{if ($FIELD_NAME eq 'contract')}{assign var=CONTRACT value = 1}{/if}

				{assign var=HIDE_CONTRACT_NUMBER value=0}
				{if $FIELD_NAME eq "free_valuation_type" || $FIELD_NAME eq "rate_per_100" || $FIELD_NAME eq "valuation_flat_charge" || $FIELD_NAME eq "declared_value" || $FIELD_NAME eq "free_valuation_limit" || $FIELD_NAME eq "min_declared_value_mult" || $FIELD_NAME eq "apply_free_fvp" || $FIELD_NAME eq "increased_base" || $FIELD_NAME eq 'parent_contract' || $FIELD_NAME eq 'nat_account_no'}
					{if !$CONTRACT}
						{assign var=HIDE_CONTRACT_NUMBER value=1}
					{/if}
				{/if}
				{if $FIELD_NAME eq "apply_full_pack_rate_override" && $COUNTER eq 1 && getenv('INSTANCE_NAME') != 'sirva'}
					{assign var=COUNTER value=$COUNTER+1}
					<td class="fieldLabel {$WIDTHTYPE}" style="width:20%"></td><td class="{$WIDTHTYPE}" style="width:30%"></td>
				{/if}
				{if $FIELD_NAME eq "grr_cp"}
					<td class="fieldLabel {$WIDTHTYPE} hide" style="width:20%"></td><td class="{$WIDTHTYPE} hide" style="width:30%"></td>
					{assign var=COUNTER value=$COUNTER+1}
				{/if}
				{assign var="isReferenceField" value=$FIELD_MODEL->getFieldDataType()}
				{if $isReferenceField eq 'reference' && count($FIELD_MODEL->getReferenceList()) < 1}{continue}{/if}
				{if $FIELD_MODEL->get('uitype') eq "20" or $FIELD_MODEL->get('uitype') eq "19"}
					{if $COUNTER eq '1'}
						<td class="{$WIDTHTYPE}"></td><td class="{$WIDTH_TYPE_CLASSSES[$WIDTHTYPE]}"></td></tr><tr>
						{assign var=COUNTER value=0}
					{/if}
				{/if}
				{if  $BLOCK_LABEL eq 'LBL_ESTIMATES_DATES'}
					 {if $COUNTER eq '3'}
					 </tr>
							<tr>
						{assign var=COUNTER value=1}
					{else}
						{assign var=COUNTER value=$COUNTER+1}
				{/if}
				{else}
				{if $COUNTER eq 2}
					</tr><tr
						{if $FIELD_NAME eq 'full_pack'} id='full_pack_unpack_row'{/if}
						{if $FIELD_NAME eq 'parent_contract'} id="contract_row"{/if}
						{if (
						$FIELD_NAME eq 'lead_type' ||
						$HIDE_CONTRACT_NUMBER
						)} class="hide"{/if}
					>
					{assign var=COUNTER value=1}
				{else}
					{if $FIELD_NAME neq "pricing_color_lock" && $FIELD_NAME neq "smf_type"}
						{assign var=COUNTER value=$COUNTER+1}
					{/if}
				{/if}
				{/if}
				{if $FIELD_NAME eq 'priority_shipping' && $COUNTER eq 2}
					{assign var=COUNTER value=1}
					<td class="fieldLabel {$WIDTHTYPE}"></td><td class="fieldValue {$WIDTHTYPE}"></td></tr><tr>
				{/if}

				<td class="fieldLabel {$WIDTHTYPE}{if $FIELD_NAME eq "pricing_color_lock" || $FIELD_NAME eq "smf_type" || $FIELD_NAME eq "apply_full_pack_rate_override" || $FIELD_NAME eq "full_pack_rate_override" || ($FIELD_NAME eq "lead_type" && $COUNTER eq 1) || $FIELD_NAME eq "grr" || $FIELD_NAME eq "grr_override" || $FIELD_NAME eq "grr_override_amount" || $FIELD_NAME eq "grr_estimate" || $FIELD_NAME eq "grr_cp" || $HIDE_CONTRACT_NUMBER} hide{/if}" {if $BLOCK_LABEL eq 'LBL_ESTIMATES_DATES'}style='width:14%'{/if}>
					{if $isReferenceField neq "reference"}<label id="{$MODULE_NAME}_editView_fieldName_{$FIELD_NAME}_label" class="muted pull-right marginRight10px {if ($FIELD_NAME eq 'lead_type')}hide{/if}">{/if}
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
									<select class="chzn-select referenceModulesList streched" style="width:140px;">
										<optgroup>
											{foreach key=index item=value from=$REFERENCE_LIST}
												<option value="{$value}" {if $value eq $REFERENCED_MODULE_NAME} selected {/if}>{vtranslate($value, $MODULE)}</option>
											{/foreach}
										</optgroup>
									</select>
								</span>
							{else}
								<label class="muted pull-right marginRight10px label_{$FIELD_MODEL->get('label')}{if ($FIELD_NAME == 'local_carrier' && $BUSINESS_LINE neq 'Local Move')} hide{/if}">{if $FIELD_MODEL->isMandatory() eq true} <span class="redColor">*</span> {/if}{vtranslate($FIELD_MODEL->get('label'), $MODULE)}</label>

							{/if}
						{else if $FIELD_MODEL->get('uitype') eq "83"}
							{include file=vtemplate_path($FIELD_MODEL->getUITypeModel()->getTemplateName(),$MODULE) COUNTER=$COUNTER}
						{else if getenv('INSTANCE_NAME') != 'uvlc'  || ( getenv('INSTANCE_NAME') == 'uvlc' && $FIELD_NAME neq "pricing_type" )}
							{vtranslate($FIELD_MODEL->get('label'), $MODULE)}
						{/if}
					{if $isReferenceField neq "reference"}</label>{/if}
				</td>
				{if $FIELD_MODEL->get('uitype') neq "83"}
					{if $FIELD_NAME eq "pricing_color_lock" || $FIELD_NAME eq "pricing_color_lock" || $FIELD_NAME eq "smf_type" || ($FIELD_NAME eq "lead_type" && $COUNTER eq 1) || $FIELD_NAME eq "grr_cp"} {*This prevents random half rows when we are hiding a field and it thinks there needs to be an empty td to finish a line*}{assign var=COUNTER value=0} {/if}
					<td {if $BLOCK_LABEL eq 'LBL_ESTIMATES_DATES'}style='width:19%'{/if} class="fieldValue {$WIDTHTYPE} value_{$FIELD_MODEL->get('label')}{if $FIELD_NAME eq "pricing_color_lock" || $FIELD_NAME eq "smf_type" || $FIELD_NAME eq "apply_full_pack_rate_override" || $FIELD_NAME eq "full_pack_rate_override" || $FIELD_NAME eq "lead_type" || $FIELD_NAME eq "grr" || $FIELD_NAME eq "grr_override" || $FIELD_NAME eq "grr_override_amount" || $FIELD_NAME eq "grr_estimate" || $FIELD_NAME eq "grr_cp" || $HIDE_CONTRACT_NUMBER || $FIELD_NAME eq "increased_base"} hide{/if}" {if $FIELD_MODEL->get('uitype') eq '19'} colspan="3" {assign var=COUNTER value=$COUNTER+1} {/if} {if $FIELD_MODEL->get('uitype') eq '20'} colspan="3"{/if}>
						{if $FIELD_NAME eq "lead_type"}
							<span {if $FIELD_NAME eq 'lead_type'}class="hide"{/if}>
								{assign var="FIELD_INFO" value=Zend_Json::encode($FIELD_MODEL->getFieldInfo())}
								{* OLD SECURITIES {if $FIELD_MODEL->get('name') neq 'sales_person'} *}
									{assign var=PICKLIST_VALUES value=$FIELD_MODEL->getPicklistValues()}
								{* else *}
									{*assign var=PICKLIST_VALUES value=$USER_MODEL->getUserAgencyUsers($MODULE, true)*}
								{* {/if} *}
								{assign var="SPECIAL_VALIDATOR" value=$FIELD_MODEL->getValidator()}
								{if $FIELD_MODEL->get('name') eq {$BLFIELD}}
									<select onchange="loadBlocksByBusinesLine('{$MODULE}', '{$BLFIELD}')" class="chzn-select {if $OCCUPY_COMPLETE_WIDTH} row-fluid {/if}{if $FIELD_NAME eq 'lead_type'}hide{/if}" name="{$FIELD_MODEL->getFieldName()}" data-validation-engine="validate[{if $FIELD_MODEL->isMandatory() eq true} required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" data-fieldinfo='{$FIELD_INFO|escape}' {if !empty($SPECIAL_VALIDATOR)}data-validator='{Zend_Json::encode($SPECIAL_VALIDATOR)}'{/if} data-selected-value='{$FIELD_MODEL->get('fieldvalue')}'>
								{else}
									<select class="chzn-select {if $OCCUPY_COMPLETE_WIDTH} row-fluid {/if}{if $FIELD_NAME eq 'lead_type'}hide{/if}" name="{$FIELD_MODEL->getFieldName()}" data-validation-engine="validate[{if $FIELD_MODEL->isMandatory() eq true} required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" data-fieldinfo='{$FIELD_INFO|escape}' {if !empty($SPECIAL_VALIDATOR)}data-validator='{Zend_Json::encode($SPECIAL_VALIDATOR)}'{/if} data-selected-value='{$FIELD_MODEL->get('fieldvalue')}'>
								{/if}
									{if $FIELD_MODEL->isEmptyPicklistOptionAllowed()}<option value="">{vtranslate('LBL_SELECT_OPTION','Vtiger')}</option>{/if}
									{foreach item=PICKLIST_VALUE key=PICKLIST_NAME from=$PICKLIST_VALUES}
										{if $FIELD_MODEL->get('name') eq 'business_line' && $PICKLIST_NAME eq 'Auto Transportation' && !$VEHICLE_LOOKUP}{continue}{/if}
										<option value="{Vtiger_Util_Helper::toSafeHTML($PICKLIST_NAME)}" {if trim(decode_html($FIELD_MODEL->get('fieldvalue'))) eq trim($PICKLIST_NAME)}{assign var=LEAD_TYPE value = trim($PICKLIST_NAME)} selected {/if}>{$PICKLIST_VALUE}</option>
									{/foreach}
								</select>
							</span>
						{else if getenv('INSTANCE_NAME') != 'uvlc'  || ( getenv('INSTANCE_NAME') == 'uvlc' && $FIELD_NAME neq "pricing_type" )}
							{if ($FIELD_NAME == 'local_carrier' && $BUSINESS_LINE neq 'Local Move')}
								<span class="hide">
							{/if}
							{include file=vtemplate_path($FIELD_MODEL->getUITypeModel()->getTemplateName(),$MODULE) BLOCK_FIELDS=$BLOCK_FIELDS}
							{if ($FIELD_NAME == 'local_carrier' && $BUSINESS_LINE neq 'Local Move')}
								</span>
							{/if}
							{*This will add our hidden input with data-prev-value for JS for effective_date*}
							{if $FIELD_MODEL->get('uitype') eq 5 && $FIELD_NAME eq 'effective_date'}
								<input type="hidden" class="hide" name="prevEffectiveDate" value="EfectiveDate" data-prev-value="{$FIELD_MODEL->get('fieldvalue')}">
							{/if}
						{/if}
						{if $FIELD_NAME eq 'load_date' && $SHOW_TRANSIT_GUIDE}
										<span id="TransitGuide">
											<button type="button" class="transitGuide" name="transitGuide"><strong>{vtranslate('LBL_TRANSIT_GUIDE', $MODULE)}</strong></button>
                                        </span>
                                    {/if}
					</td>
				{/if}
				{*{if $BLOCK_FIELDS|@count eq 1 and $FIELD_MODEL->get('uitype') neq "19" and $FIELD_MODEL->get('uitype') neq "20" and $FIELD_MODEL->get('uitype') neq "30" and $FIELD_MODEL->get('name') neq "recurringtype"}
					<td class="{$WIDTHTYPE}"></td><td class="{$WIDTHTYPE}"></td>
				{/if}*}
				{if $FIELD_NAME eq "full_pack_rate_override"}
					<td class="fieldLabel {$WIDTHTYPE} hide fullPackOverrideFiller"></td><td class="{$WIDTHTYPE} hide fullPackOverrideFiller"></td>
				{/if}
                {*no idea why this fixes the UI but it does*}
				{if $FIELD_NAME eq "desired_total"}{$COUNTER = $COUNTER + 1}<td class='fieldLabel'>&nbsp;</td><td class='fieldValue'>&nbsp;</td>{/if}
				{if getenv('INSTANCE_NAME') neq 'sirva' && $BLOCK_LABEL eq "LBL_QUOTES_LOCALMOVEDETAILS" && $FIELD_NAME eq 'local_weight'}
					<td class='fieldLabel'>&nbsp;</td>
					<td class='fieldValue'>&nbsp;</td>
				{/if}
			{/foreach}

			{if $BLOCK_LABEL eq "LBL_QUOTES_LOCALMOVEDETAILS"}
				{if $COUNTER eq 1}
					{assign var=COUNTER value=$COUNTER+1}
					<td class="fieldLabel {$WIDTHTYPE}" style="width:20%"></td><td class="{$WIDTHTYPE}" style="width:30%"></td>
				{/if}
				<tr>
				<td class='fieldLabel {$WIDTHTYPE}'><label class='muted pull-right marginRight10px'><span class="redColor">*</span>Tariff</label></td>
				<td class='fieldValue {$WIDTHTYPE}'>
					{$LOCALFIELDINFO.mandatory = true}
					{$LOCALFIELDINFO.type = 'picklist'}
					{$INFO = Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($LOCALFIELDINFO))}
					<select class='chzn-select' name='local_tariff' data-fieldinfo='{$INFO}' data-validation-engine='validate[{if $LOCALFIELDINFO.mandatory}required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]'>
						<option value>Select an Option</option>
						{if getenv('INSTANCE_NAME') eq 'sirva'}
							{assign var=COMBINE_TARIFF value=$INTRASTATE_TARIFFS}
							{foreach item=TARIFF key=INDEX from=$COMBINE_TARIFF}
								<option class="{if $TARIFF->intrastate eq true}intrastateTariff {if $TARIFF->intraInterstate}intraInterstate {else}intraLocal {/if}{elseif $TARIFF->local eq true}localTariff {/if}" value="{$TARIFF->get('id')}" {if $TARIFF->get('id') eq $EFFECTIVE_TARIFF}selected{/if}>{if $TARIFF->intraInterstate}{$TARIFF->get('tariffmanagername')}{else}{$TARIFF->get('tariff_name')}{/if}</option>
							{/foreach}
						{else}
							{assign var=COMBINE_TARIFF value=$LOCAL_TARIFFS|array_merge:$INTRASTATE_TARIFFS}
							{foreach item=TARIFF key=INDEX from=$COMBINE_TARIFF}
							<option class="{if $TARIFF->intrastate eq true}intrastateTariff {if $TARIFF->intraInterstate}intraInterstate {else}intraLocal {/if}{elseif $TARIFF->local eq true}localTariff {/if}" value="{$TARIFF->get('id')}" {if $TARIFF->get('id') eq $EFFECTIVE_TARIFF && ($TARIFF->intrastate == $INTRA_TARIFF || $TARIFF->intraInterstate)}selected{/if}>{if $TARIFF->intraInterstate}{$TARIFF->get('tariffmanagername')}{else}{$TARIFF->get('tariff_name')}{/if}</option>
							{/foreach}
							{/if}
					</select>
				</td>
				<td class='fieldLabel'>&nbsp;</td>
				<td class='fieldValue'>{if !$LOCK_RATING}<button type="button" class="localRate">Get Rate Estimate</button>{/if}&nbsp;{if !$LOCK_RATING && (getenv('INSTANCE_NAME') neq 'sirva')}<button type="button" class="localRateMileage">Get Mileage</button>{/if}&nbsp;</td>
				</tr>
			{/if}
			{if $BLOCK_LABEL eq 'LBL_ESTIMATES_DATES'}
				{if $COUNTER eq 1}
					<td style="width:14%" class="fieldLabel {$WIDTHTYPE}"></td><td style="width:19%" class="{$WIDTHTYPE}"></td><td style="width:14%" class="fieldLabel {$WIDTHTYPE}"></td><td style="width:19%" class="{$WIDTHTYPE}"></td>
					{assign var=COUNTER value=$COUNTER+1}
				{elseif $COUNTER eq 2}
					<td style="width:14%" class="fieldLabel {$WIDTHTYPE}"></td><td style="width:19%" class="{$WIDTHTYPE}"></td>
				{/if}
			{/if}
			{* adding additional column for odd number of fields in a block *}
			{if $BLOCK_LABEL eq 'LBL_QUOTES_INTERSTATEMOVEDETAILS'}
				{if $BLOCK_FIELDS|@end eq true and $BLOCK_FIELDS|@count neq 1 and $BLOCK_LABEL neq 'LBL_ESTIMATES_DATES'}
					{if $COUNTER eq 2 || $COUNTER eq 0}<tr>{assign var=COUNTER value=0}{/if}
					<td class="fieldLabel {$WIDTHTYPE}"><label class="muted pull-right marginRight10px"><span class="redColor">*</span>{vtranslate('LBL_QUOTES_EFFECTIVETARIFF', $MODULE)}</label></td>
					<td class="fieldValue {$WIDTHTYPE}">
						<select class='chzn-select' name='effective_tariff' {if getenv('INSTANCE_NAME')=='graebel' && $MODULE=='Actuals'}data-validation-engine='validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]'{/if}>
							<option value>Select an Option</option>
							{foreach item=TARIFF key=INDEX from=$ASSIGNED_TARIFFS}
								{*ALLV-2A and NAVL-12A are only available for Lead Type of National Account*}
								{if $LEAD_TYPE != 'National Account'}
									{if $TARIFF->get('custom_tariff_type') eq 'ALLV-2A' ||
                                        $TARIFF->get('custom_tariff_type') eq 'NAVL-12A'
									}
										{*
										|| $TARIFF->get('custom_tariff_type') eq '400N Base'
										|| $TARIFF->get('custom_tariff_type') eq '400N/104G'
										|| $TARIFF->get('custom_tariff_type') eq '400NG'
										*}
										{continue}
									{/if}
								{/if}
								<option class="{if $TARIFF->intrastate}intrastateTariff {if $TARIFF->intraInterstate}intraInterstate{/if}{else}interstateTariff{/if}" value="{$TARIFF->get('id')}" {if $TARIFF->get('id') eq $EFFECTIVE_TARIFF}selected{/if}>{$TARIFF->get('tariffmanagername')}</option>
							{/foreach}
							{foreach item=TARIFF key=INDEX from=$INTRASTATE_TARIFFS}
								{if $TARIFF->intraInterstate}{continue}{/if}
								<option class="intrastateTariff" value="{$TARIFF->get('id')}" {if $TARIFF->get('id') eq $EFFECTIVE_TARIFF}selected{/if}>{$TARIFF->get('tariffmanagername')}</option>
							{/foreach}
						</select>
						<div id='hiddenTariffFields'>
							{foreach item=TARIFF key=INDEX from=$ASSIGNED_TARIFFS}
								<input type="hidden" id="customjs_{$TARIFF->get('id')}" value="{$TARIFF->get('custom_javascript')}">
								<input type="hidden" id="tariffType_{$TARIFF->get('id')}" value="{$TARIFF->get('custom_tariff_type')}">
							{/foreach}
						</div>
					</td>
					{assign var=COUNTER value=$COUNTER+1}
				{/if}
			{/if}

			{if $BLOCK_FIELDS|@end eq true and $BLOCK_FIELDS|@count neq 1 and $COUNTER eq 1}
				<!-- filling in row -->
				<td class="fieldLabel {$WIDTHTYPE}" style="width:20%"></td><td class="{$WIDTHTYPE}" style="width:30%"></td>
			{/if}
			</tr>
			{if $BLOCK_LABEL == 'LBL_QUOTES_INTERSTATEMOVEDETAILS'}
					<script type='text/javascript' src='libraries/jquery/colorbox/jquery.colorbox-min.js'></script>
					<tr><td class='fieldLabel'></td>
						<td class='fieldValue'>
							{if getenv('INSTANCE_NAME') eq 'sirva'}&nbsp;
								<button type='button' class='requote'>Re-Quote</button>
							{else}&nbsp;<!--<button type='button' id='interstateRateQuick'>Quick Rate Estimate</button>-->{/if}
						</td>
						<td class='fieldLabel'></td>
						<td class='fieldValue'>
							{if !$LOCK_RATING}
								<button type='button' class='interstateRateDetail'>{vtranslate('LBL_DETAILED_RATE_ESTIMATE', $MODULE)}</button>
							{/if}
						</td>
					</tr>
			{/if}
			{if $BLOCK_LABEL eq 'LBL_QUOTE_INFORMATION' && 'move_type'|array_key_exists:$BLOCK_FIELDS}
				<tr class="hide">
					<td class="fieldLabel">
						<label class="muted pull-right marginRight10px">{if $BLOCK_FIELDS['business_line_est']->isMandatory() eq true} <span class="redColor">*</span> {/if}{vtranslate($BLOCK_FIELDS['business_line_est']->get('label'), $MODULE)}</label>
					</td>
					<td class="fieldValue">
						{assign var="FIELD_INFO" value=Zend_Json::encode($BLOCK_FIELDS['business_line_est']->getFieldInfo())}
						{assign var=PICKLIST_VALUES value=$BLOCK_FIELDS['business_line_est']->getPicklistValues()}
						{assign var="SPECIAL_VALIDATOR" value=$BLOCK_FIELDS['business_line_est']->getValidator()}
						{if $BLOCK_FIELDS['business_line_est']->get('name') eq {$BLFIELD}}
							<select onchange="loadBlocksByBusinesLine('{$MODULE}', '{$BLFIELD}')" class="chzn-select {if $OCCUPY_COMPLETE_WIDTH} row-fluid {/if}" name="{$BLOCK_FIELDS['business_line_est']->getFieldName()}" data-validation-engine="validate[{if $BLOCK_FIELDS['business_line_est']->isMandatory() eq true} required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" data-fieldinfo='{$FIELD_INFO|escape}' {if !empty($SPECIAL_VALIDATOR)}data-validator='{Zend_Json::encode($SPECIAL_VALIDATOR)}'{/if} data-selected-value='{$BLOCK_FIELDS['business_line_est']->get('fieldvalue')}'>
						{else}
							<select class="chzn-select {if $OCCUPY_COMPLETE_WIDTH} row-fluid {/if}" name="{$BLOCK_FIELDS['business_line_est']->getFieldName()}" data-validation-engine="validate[{if $BLOCK_FIELDS['business_line_est']->isMandatory() eq true} required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" data-fieldinfo='{$FIELD_INFO|escape}' {if !empty($SPECIAL_VALIDATOR)}data-validator='{Zend_Json::encode($SPECIAL_VALIDATOR)}'{/if} data-selected-value='{$BLOCK_FIELDS['business_line_est']->get('fieldvalue')}'>
						{/if}
							{if $BLOCK_FIELDS['business_line_est']->isEmptyPicklistOptionAllowed()}<option value="">{vtranslate('LBL_SELECT_OPTION','Vtiger')}</option>{/if}
							{foreach item=PICKLIST_VALUE key=PICKLIST_NAME from=$PICKLIST_VALUES}
								<option value="{Vtiger_Util_Helper::toSafeHTML($PICKLIST_NAME)}" {if trim(decode_html($BLOCK_FIELDS['business_line_est']->get('fieldvalue'))) eq trim($PICKLIST_NAME)} selected {/if}>{$PICKLIST_VALUE}</option>
							{/foreach}
						</select>
					</td>
					<td class="fieldLabel">
						&nbsp;
					</td>
					<td>
						&nbsp;
					</td>
				</tr>
			{/if}
			</table>
			{if $BLOCK_LABEL neq 'LBL_ESTIMATES_EXTRASTOPS'}<br>{/if}

			{if $BLOCK_LABEL eq 'LBL_ADDRESS_INFORMATION' && $ADDRESSSEGMENTS_MODULE_MODEL && $ADDRESSSEGMENTS_MODULE_MODEL->isActive()}
				{include file=vtemplate_path('AddressSegmentsEdit.tpl', 'AddressSegments')}
			{/if}
		{/foreach}

		{* This is wrong - this table should be defined in Contracts and merely consumed in Estimates. *}
		{* OVERRULED! *}
		{if getenv('INSTANCE_NAME') eq 'graebel'}
			<!-- FLAT RATE AUTO STARTS -->
			{include file=vtemplate_path('AutoRateTableEdit.tpl', 'Contracts')}
			<!-- FLAT RATE AUTO ENDS -->
		{/if}

		{if getenv('INSTANCE_NAME') == 'sirva'}{include file=vtemplate_path('extraStopsEdit.tpl', 'ExtraStops')}{/if}
		{include file=vtemplate_path('GuestEditBlocks.tpl', $MODULE)}
		<!-- BEGIN LOCAL BLOCK EDIT -->
		<!-- START LOCAL MOVE DIV -->
		<div id='localMoveTables'>
			{include file=vtemplate_path('LocalBlockEdit.tpl', $MODULE_NAME)}
		<!-- END LOCAL MOVE DIV -->
		</div>
		<!-- BEGIN LOCAL BLOCK EDIT -->
		<input type='hidden' name='ratingResult' value='{$RATING_RETURN}' />
{/strip}
