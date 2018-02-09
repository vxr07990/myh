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
	{foreach key=BLOCK_LABEL_KEY item=FIELD_MODEL_LIST from=$RECORD_STRUCTURE}
		{if $BLOCK_LABEL_KEY eq 'LBL_OPPORTUNITIES_REFERRAL'}{continue}{/if}
		{if $BLOCK_LABEL_KEY eq 'LBL_OPPORTUNITY_EMPLOYERASSISTING' && $HIDE_EMPLOYEE_ASSISTING}{continue}{/if}

	{if $BLOCK_LABEL_KEY == 'LBL_POTENTIALS_ADDRESSDETAILS'}
		{if $IS_ACTIVE_ADDRESSLIST == true}
			{include file=vtemplate_path('AddressListDetail.tpl', 'AddressList')}
			{continue}
		{/if}
	{/if}

	{*if $BLOCK_LABEL_KEY eq 'LBL_OPPORTUNITY_REGISTERSTS'*}{*continue*}{*/if*}
	{if $MODULE_NAME eq "Quotes" and ($BLOCK_LABEL_KEY eq "LBL_QUOTES_VALUATIONDETAILS" or $BLOCK_LABEL_KEY eq "LBL_QUOTES_SITDETAILS" or $BLOCK_LABEL_KEY eq "LBL_QUOTES_ACCESSORIALDETAILS")}{continue}{/if}
	{assign var=BLOCK value=$BLOCK_LIST[$BLOCK_LABEL_KEY]}
	{if $BLOCK eq null or $FIELD_MODEL_LIST|@count lte 0}{continue}{/if}
	{assign var=IS_HIDDEN value=$BLOCK->isHidden()}
	{assign var=WIDTHTYPE value=$USER_MODEL->get('rowheight')}
	<input type=hidden name="timeFormatOptions" data-value='{$DAY_STARTS}' />
	{*if $BLOCK_LABEL_KEY eq 'TURTLES'*}
		{*{if $STOPS_ROWS|@count gt 0}
				<table class="table table-bordered equalSplit detailview-table" style="width: 100;">
					<thead>
						<tr>
							<th class="blockHeader" colspan="4">
									<img class="cursorPointer alignMiddle blockToggle {if !($IS_HIDDEN)} hide {/if} "  src="{vimage_path('arrowRight.png')}" data-mode="hide" data-id={$BLOCK_LIST[$BLOCK_LABEL_KEY]->get('id')}>
									<img class="cursorPointer alignMiddle blockToggle {if ($IS_HIDDEN)} hide {/if}"  src="{vimage_path('arrowDown.png')}" data-mode="show" data-id={$BLOCK_LIST[$BLOCK_LABEL_KEY]->get('id')}>
									&nbsp;&nbsp;{vtranslate({$BLOCK_LABEL_KEY},{$MODULE_NAME})}
							</th>
						</tr>
					</thead>
					{foreach key=STOP_INDEX item=CURRENT_STOP from=$STOPS_ROWS}
						<tbody class="stopBlock">
							<tr class="fieldLabel" colspan="4">
								<td colspan="4" class="cbxblockhead">
									<span class="stopTitle"><b>&nbsp;&nbsp;&nbsp;Stop {$STOP_INDEX+1}</b></span>
								</td>
							</tr>
							<tr colspan="4">
								<td colspan="4" style="padding: 0px;">
									<table class="table equalSplit table-bordered detailview-table" style="padding: 0px; border: 0px;">
										<tbody class="stopBody">
											<tr style="width:100%" colspan="4">
												<td class="fieldLabel medium" colspan="1" style="text-align:center;margin:auto">
													<label class="muted pull-right marginRight10px">{vtranslate('LBL_OPPORTUNITY_STOPDESCRIPTION', $MODULE_NAME)}</label>
												</td>
												<td class="fieldValue medium" id="Opportunities_detailView_fieldLabel_stop_description_{$STOP_INDEX+1}">
													<div class="row-fluid">
														<span class="value" data-field-type="string">
															{$CURRENT_STOP['stop_description']}
														</span>
														<span class="hide">
															{$LOCALFIELDINFO.mandatory = true}
															{$LOCALFIELDINFO.name = 'stop_description_'|@cat:($STOP_INDEX+1)}
															{$LOCALFIELDINFO.label = 'LBL_OPPORTUNITY_STOPDESCRIPTION'}
															{$LOCALFIELDINFO.type = 'string'}
															{$INFO = Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($LOCALFIELDINFO))}
															<input id="stop_description_{$STOP_INDEX+1}" type="text" class="input-large stopField" name="stop_description_{$STOP_INDEX+1}" value="{$CURRENT_STOP['stop_description']}" data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" data-fieldinfo={$INFO}>
															<input type="hidden" class="fieldname" value="stop_description_{$STOP_INDEX+1}" data-prev-value="{$CURRENT_STOP['stop_description']}">
														</span>
													</div>
												</td>
												<td class="fieldLabel medium" colspan="1" style="text-align:center;margin:auto">
													{if getenv('INSTANCE_NAME') eq 'sirva'}
														<label class="muted pull-right marginRight10px">{vtranslate('LBL_OPPORTUNITY_SIRVASTOPTYPE', 'Opportunities')}</label>
													{else}
														<label class="muted pull-right marginRight10px">{vtranslate('LBL_OPPORTUNITY_STOPSEQUENCE', $MODULE)}</label>
													{/if}
												</td>
												<td class="fieldValue medium" id="Opportunities_detailView_fieldLabel_stop_sequence_{$STOP_INDEX+1}">
													<div class="row-fluid">
														{if getenv('INSTANCE_NAME') eq 'sirva'}
															<span class="value" data-field-type="picklist">
																{assign var="sirvaStopTypeValue" value=$CURRENT_STOP['sirva_stop_type']}
																{if $CURRENT_STOP['stop_type'] eq 'Origin' && ($CURRENT_STOP['sirva_stop_type'] eq 'PRM' || $CURRENT_STOP['sirva_stop_type'] eq 'STG')}
																	{if $CURRENT_STOP['sirva_stop_type'] eq 'PRM'}
																		{$sirvaStopTypeValue = 'OPRM'}
																	{/if}
																	{if $CURRENT_STOP['sirva_stop_type'] eq 'STG'}
																		{$sirvaStopTypeValue = 'OSTG'}
																	{/if}
																{elseif $CURRENT_STOP['stop_type'] eq 'Destination' && ($CURRENT_STOP['sirva_stop_type'] eq 'PRM' || $CURRENT_STOP['sirva_stop_type'] eq 'STG')}
																	{if $CURRENT_STOP['sirva_stop_type'] eq 'PRM'}
																		{$sirvaStopTypeValue = 'DPRM'}
																	{/if}
																	{if $CURRENT_STOP['sirva_stop_type'] eq 'STG'}
																		{$sirvaStopTypeValue = 'DSTG'}
																	{/if}
																{/if}
																{vtranslate($sirvaStopTypeValue, 'Opportunities')}
															</span>
														{else}
															<span class="value" data-field-type="string">
																{$CURRENT_STOP['stop_sequence']}
															</span>
														{/if}
														<span class="hide">
															{$LOCALFIELDINFO.mandatory = true}
															{$LOCALFIELDINFO.name = 'stop_sequence_'|@cat:($STOP_INDEX+1)}
															{$LOCALFIELDINFO.label = 'LBL_OPPORTUNITY_STOPSEQUENCE'}
															{$LOCALFIELDINFO.type = 'string'}
															{$INFO = Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($LOCALFIELDINFO))}
															<input id="stop_sequence_{$STOP_INDEX+1}" type="text" class="input-large stopField" name="stop_sequence_{$STOP_INDEX+1}" value="{$CURRENT_STOP['stop_sequence']}" data-validation-engine="validate[required,custom[integer],funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" data-fieldinfo={$INFO}>
															<input type="hidden" class="fieldname" value="stop_sequence_{$STOP_INDEX+1}" data-prev-value="{$CURRENT_STOP['stop_sequence']}">
														</span>
													</div>
												</td>
											</tr>
											<tr style="width:100%" colspan="4">
												<td class="fieldLabel medium" colspan="1" style="text-align:center;margin:auto">
													<label class="muted pull-right marginRight10px">{vtranslate('LBL_OPPORTUNITY_STOPWEIGHT', $MODULE_NAME)}</label>
												</td>
												<td class="fieldValue medium" id="Opportunities_detailView_fieldLabel_stop_weight_{$STOP_INDEX+1}">
													<div class="row-fluid">
														<span class="value" data-field-type="string">
															{$CURRENT_STOP['stop_weight']}
														</span>
														<span class="hide">
															<input id="stop_weight_{$STOP_INDEX+1}" type="text" class="input-large stopField" name="stop_weight_{$STOP_INDEX+1}" value="{$CURRENT_STOP['stop_weight']}">
															<input type="hidden" class="fieldname" value="stop_weight_{$STOP_INDEX+1}" data-prev-value="{$CURRENT_STOP['stop_weight']}">
														</span>
													</div>
												</td>
												<td class="fieldLabel medium" colspan="1" style="text-align:center;margin:auto">
													<label class="muted pull-right marginRight10px">{vtranslate('LBL_OPPORTUNITY_STOPISPRIMARY', $MODULE_NAME)}</label>
												</td>
												<td class="fieldValue medium" id="Opportunities_detailView_fieldLabel_stop_isprimary_{$STOP_INDEX+1}">
													<div class="row-fluid">
														<span class="value" data-field-type="boolean">
															{if $CURRENT_STOP['stop_isprimary'] eq '1' || $CURRENT_STOP['stop_isprimary'] eq 'yes' || $CURRENT_STOP['stop_isprimary'] eq 'on'}Yes{else}No{/if}
														</span>
														<span class="hide">
															<input type="hidden" class="stopField" name="stop_isprimary_{$STOP_INDEX+1}" value="{if $CURRENT_STOP['stop_isprimary'] eq '1' || $CURRENT_STOP['stop_isprimary'] eq 'yes' || $CURRENT_STOP['stop_isprimary'] eq 'on'}1{else}0{/if}">
															<input id="Opportunities_detailView_fieldName_stop_isprimary_{$STOP_INDEX+1}" type="checkbox" class="input-large stopField" name="stop_isprimary_{$STOP_INDEX+1}" {if $CURRENT_STOP['stop_isprimary'] eq '1' || $CURRENT_STOP['stop_isprimary'] eq 'yes' || $CURRENT_STOP['stop_isprimary'] eq 'on'}checked{/if} data-fieldinfo="{literal}{'type':'boolean'}{/literal}">
															<input type="hidden" class="fieldname" value="stop_isprimary_{$STOP_INDEX+1}" data-prev-value="{if $CURRENT_STOP['stop_isprimary'] eq '1' || $CURRENT_STOP['stop_isprimary'] eq 'yes' || $CURRENT_STOP['stop_isprimary'] eq 'on'}Yes{else}No{/if}">
														</span>
													</div>
												</td>
											</tr>
											<tr style="width:100%" colspan="4">
												<td class="fieldLabel medium" colspan="1" style="text-align:center;margin:auto">
													<label class="muted pull-right marginRight10px">{vtranslate('LBL_OPPORTUNITY_STOPADDRESS1', $MODULE_NAME)}</label>
												</td>
												<td class="fieldValue medium" id="Opportunities_detailView_fieldLabel_stop_address1_{$STOP_INDEX+1}">
													<div class="row-fluid">
														<span class="value" data-field-type="string">
															{$CURRENT_STOP['stop_address1']}
														</span>
														<span class="hide">
															<input id="stop_address1_{$STOP_INDEX+1}" type="text" class="input-large stopField" name="stop_address1_{$STOP_INDEX+1}" value="{$CURRENT_STOP['stop_address1']}">
															<input type="hidden" class="fieldname" value="stop_address1_{$STOP_INDEX+1}" data-prev-value="{$CURRENT_STOP['stop_address1']}">
														</span>
													</div>
												</td>
												<td class="fieldLabel medium" colspan="1" style="text-align:center;margin:auto">
													<label class="muted pull-right marginRight10px">{vtranslate('LBL_OPPORTUNITY_STOPADDRESS2', $MODULE_NAME)}</label>
												</td>
												<td class="fieldValue medium" id="Opportunities_detailView_fieldLabel_stop_address2_{$STOP_INDEX+1}">
													<div class="row-fluid">
														<span class="value" data-field-type="string">
															{$CURRENT_STOP['stop_address2']}
														</span>
														<span class="hide">
															<input id="stop_address2_{$STOP_INDEX+1}" type="text" class="input-large stopField" name="stop_address2_{$STOP_INDEX+1}" value="{$CURRENT_STOP['stop_address2']}">
															<input type="hidden" class="fieldname" value="stop_address2_{$STOP_INDEX+1}" data-prev-value="{$CURRENT_STOP['stop_address2']}">
														</span>
													</div>
												</td>
											</tr>
											<tr style="width:100%" colspan="4">
												<td class="fieldLabel medium" colspan="1" style="text-align:center;margin:auto">
													<label class="muted pull-right marginRight10px">{vtranslate('LBL_OPPORTUNITY_STOPPHONE1', $MODULE_NAME)}<label>
												</td>
												<td class="fieldValue medium" id="Opportunities_detailView_fieldLabel_stop_phone1_{$STOP_INDEX+1}">
													<div class="row-fluid">
														<span class="value" data-field-type="string">
															{$CURRENT_STOP['stop_phone1']}
														</span>
														<span class="hide">
															<input id="stop_phone1_{$STOP_INDEX+1}" type="text" class="input-large stopField" name="stop_phone1_{$STOP_INDEX+1}" value="{$CURRENT_STOP['stop_phone1']}">
															<input type="hidden" class="fieldname" value="stop_phone1_{$STOP_INDEX+1}" data-prev-value="{$CURRENT_STOP['stop_phone1']}">
														</span>
													</div>
												</td>
												<td class="fieldLabel medium" colspan="1" style="text-align:center;margin:auto">
													<label class="muted pull-right marginRight10px">{vtranslate('LBL_OPPORTUNITY_STOPPHONE2', $MODULE_NAME)}</label>
												</td>
												<td class="fieldValue medium" id="Opportunities_detailView_fieldLabel_stop_phone1_{$STOP_INDEX+1}">
													<div class="row-fluid">
														<span class="value" data-field-type="string">
															{$CURRENT_STOP['stop_phone2']}
														</span>
														<span class="hide">
															<input id="stop_phone2_{$STOP_INDEX+1}" type="text" class="input-large stopField" name="stop_phone2_{$STOP_INDEX+1}" value="{$CURRENT_STOP['stop_phone2']}">
															<input type="hidden" class="fieldname" value="stop_phone2_{$STOP_INDEX+1}" data-prev-value="{$CURRENT_STOP['stop_phone2']}">
														</span>
													</div>
												</td>
											</tr>
											<tr style="width:100%" colspan="4">
												<td class="fieldLabel medium" colspan="1" style="text-align:center;margin:auto">
													<label class="muted pull-right marginRight10px">{vtranslate('LBL_OPPORTUNITY_STOPPHONE1TYPE', $MODULE_NAME)}</label>
												</td>
												<td class="fieldValue medium" id="Opportunities_detailView_fieldLabel_stop_phonetype1_{$STOP_INDEX+1}">
													<span class="value" data-field-type="picklist">
															{$CURRENT_STOP['stop_phonetype1']}
														</span>
													<span class="hide">
														<input type="hidden" name="stop_phonetype1_{$STOP_INDEX+1}_prev" value="none">
														<select class="chzn-select stopField" style="text-align:left" id="stop_phonetype1_{$STOP_INDEX+1}" name="stop_phonetype1_{$STOP_INDEX+1}" data-selected-value="">
															<option value="" style="text-align:left">Select an Option</option>
															<option style="text-align:left" value="Home" {if $CURRENT_STOP['stop_phonetype1'] eq 'Home'}selected{/if}>Home</option>
															<option style="text-align:left" value="Work" {if $CURRENT_STOP['stop_phonetype1'] eq 'Work'}selected{/if}>Work</option>
															<option style="text-align:left" value="Cell" {if $CURRENT_STOP['stop_phonetype1'] eq 'Cell'}selected{/if}>Cell</option>
														</select>
														<input type="hidden" class="fieldname" value="stop_phonetype1_{$STOP_INDEX+1}" data-prev-value="{$CURRENT_STOP['stop_phonetype1']}">
													<span>
												</td>
												<td class="fieldLabel medium" colspan="1" style="text-align:center;margin:auto">
													<label class="muted pull-right marginRight10px">{vtranslate('LBL_OPPORTUNITY_STOPPHONE2TYPE', $MODULE_NAME)}</label>
												</td>
												<td class="fieldValue medium" id="Opportunities_detailView_fieldLabel_stop_phonetype2_{$STOP_INDEX+1}">
													<span class="value" data-field-type="picklist">
														{$CURRENT_STOP['stop_phonetype2']}
													</span>
													<span class="hide">
														<input type="hidden" name="stop_phonetype2_prev" value="none">
														<select class="chzn-select stopField" style="text-align:left" id="stop_phonetype2_{$STOP_INDEX+1}" name="stop_phonetype2_{$STOP_INDEX+1}" data-selected-value="">
															<option value="" style="text-align:left">Select an Option</option>
															<option style="text-align:left" value="Home" {if $CURRENT_STOP['stop_phonetype2'] eq 'Home'}selected{/if}>Home</option>
															<option style="text-align:left" value="Work" {if $CURRENT_STOP['stop_phonetype2'] eq 'Work'}selected{/if}>Work</option>
															<option style="text-align:left" value="Cell" {if $CURRENT_STOP['stop_phonetype2'] eq 'Cell'}selected{/if}>Cell</option>
														</select>
														<input type="hidden" class="fieldname" value="stop_phonetype2_{$STOP_INDEX+1}" data-prev-value="{$CURRENT_STOP['stop_phonetype2']}">
													</span>
												</td>
											</tr>
											<tr style="width:100%" colspan="4">
												<td class="fieldLabel medium" colspan="1" style="text-align:center;margin:auto">
													<label class="muted pull-right marginRight10px">{vtranslate('LBL_OPPORTUNITY_STOPCITY', $MODULE_NAME)}</label>
												</td>
												<td class="fieldValue medium" id="Opportunities_detailView_fieldLabel_stop_city_{$STOP_INDEX+1}">
													<div class="row-fluid">
														<span class="value" data-field-type="string">
															{$CURRENT_STOP['stop_city']}
														</span>
														<span class="hide">
															<input id="stop_city_{$STOP_INDEX+1}" type="text" class="input-large stopField" name="stop_city_{$STOP_INDEX+1}" value="{$CURRENT_STOP['stop_city']}">
															<input type="hidden" class="fieldname" value="stop_city_{$STOP_INDEX+1}" data-prev-value="{$CURRENT_STOP['stop_city']}">
														</span>
													</div>
												</td>
												<td class="fieldLabel medium" colspan="1" style="text-align:center;margin:auto">
													<label class="muted pull-right marginRight10px">{vtranslate('LBL_OPPORTUNITY_STOPSTATE', $MODULE_NAME)}</label>
												</td>
												<td class="fieldValue medium" id="Opportunities_detailView_fieldLabel_stop_state_{$STOP_INDEX+1}">
													<div class="row-fluid">
														<span class="value" data-field-type="string">
															{$CURRENT_STOP['stop_state']}
														</span>
														<span class="hide">
															<input id="stop_state_{$STOP_INDEX+1}" type="text" class="input-large stopField" name="stop_state_{$STOP_INDEX+1}" value="{$CURRENT_STOP['stop_state']}">
															<input type="hidden" class="fieldname" value="stop_state_{$STOP_INDEX+1}" data-prev-value="{$CURRENT_STOP['stop_state']}">
														</span>
													</div>
												</td>
											</tr>
											<tr style="width:100%" colspan="4">
												<td class="fieldLabel medium" colspan="1" style="text-align:center;margin:auto">
													<label class="muted pull-right marginRight10px">{vtranslate('LBL_OPPORTUNITY_STOPZIP', $MODULE_NAME)}</label>
												</td>
												<td class="fieldValue medium" id="Opportunities_detailView_fieldLabel_stop_zip_{$STOP_INDEX+1}">
													<div class="row-fluid">
														<span class="value" data-field-type="string">
															{$CURRENT_STOP['stop_zip']}
														</span>
														<span class="hide">
															<input id="stop_zip_{$STOP_INDEX+1}" type="text" class="input-large stopField" name="stop_zip_{$STOP_INDEX+1}" value="{$CURRENT_STOP['stop_zip']}">
															<input type="hidden" class="fieldname" value="stop_zip_{$STOP_INDEX+1}" data-prev-value="{$CURRENT_STOP['stop_zip']}">
														</span>
													</div>
												</td>
												<td class="fieldLabel medium" colspan="1" style="text-align:center;margin:auto">
													<label class="muted pull-right marginRight10px">{vtranslate('LBL_OPPORTUNITY_STOPCOUNTRY', $MODULE_NAME)}</label>
												</td>
												<td class="fieldValue medium" id="Opportunities_detailView_fieldLabel_stop_country_{$STOP_INDEX+1}">
													<div class="row-fluid">
														<span class="value" data-field-type="string">
															{$CURRENT_STOP['stop_country']}
														</span>
														<span class="hide">
															<input id="stop_country_{$STOP_INDEX+1}" type="text" class="input-large stopField" name="stop_country_{$STOP_INDEX+1}" value="{$CURRENT_STOP['stop_country']}">
															<input type="hidden" class="fieldname" value="stop_country_{$STOP_INDEX+1}" data-prev-value="{$CURRENT_STOP['stop_country']}">
														</span>
													</div>
												</td>
											</tr>
											<tr style="width:100%" colspan="4">
												<td class="fieldLabel medium" colspan="1" style="text-align:center;margin:auto">
													<label class="muted pull-right marginRight10px">{vtranslate('LBL_OPPORTUNITY_STOPDATE', $MODULE_NAME)}</label>
												</td>
												<td class="fieldValue medium" id="Opportunities_detailView_fieldLabel_stop_date_{$STOP_INDEX+1}">
													<span class="value" data-field-type="string">
															{$CURRENT_STOP['stop_date']}
													</span>
													<span class="hide">
														<div class="input-append row-fluid">
															<div class="row-fluid date">
																{$LOCALFIELDINFO.name = 'stop_date_'|@cat:($STOP_INDEX+1)}
																{$LOCALFIELDINFO.label = 'LBL_OPPORTUNITY_STOPDATE'}
																{$LOCALFIELDINFO.type = 'date'}
																{$INFO = Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($LOCALFIELDINFO))}
																<input id="{$MODULE_NAME}_editView_fieldName_stop_date_{$STOP_INDEX+1}" type="text" class="dateField input-large stopField" name="stop_date_{$STOP_INDEX+1}" data-date-format="mm-dd-yyyy" value="{$CURRENT_STOP['stop_date']}" data-validation-engine="validate[funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" data-fieldinfo={$INFO}>
																<span class="add-on">
																<i class="icon-calendar"></i>
																</span>
															</div>
														</div>
														<input type="hidden" class="fieldname" value="stop_date_{$STOP_INDEX+1}" data-prev-value="{$CURRENT_STOP['stop_date']}">
													</span>
												</td>
												<td class="fieldLabel medium" colspan="1" style="text-align:center;margin:auto">
													<label class="muted pull-right marginRight10px">{vtranslate('LBL_OPPORTUNITY_STOPCONTACT', $MODULE_NAME)}</label>
												</td>
												<td class="fieldValue medium">
													<span class="value" data-field-type="reference">
														{$CURRENT_STOP['stop_contact_link']}
													</span>
												</td>
											</tr>
											<tr>
												<td class="fieldLabel medium">
													<label class="muted pull-right marginRight10px">{vtranslate('LBL_OPPORTUNITY_STOPTYPE', $MODULE_NAME)}</label>
												</td>
												<td class="fieldValue medium" id="Opportunities_detailView_fieldLabel_stop_type_{$STOP_INDEX+1}">
													<span class="value" data-field-type="picklist">
														{$CURRENT_STOP['stop_type']}
													</span>
													<span class="hide">
														<input type="hidden" name="stop_type_{$STOP_INDEX+1}_prev" value="none">
														<select class="chzn-select stopField" style="text-align:left" id="stop_type_{$STOP_INDEX+1}" name="stop_type_{$STOP_INDEX+1}" data-selected-value="{$CURRENT_STOP['stop_type']}">
															<option value="" style="text-align:left">Select an Option</option>
															<option style="text-align:left" value="Origin" {if $CURRENT_STOP['stop_type'] eq 'Origin'}selected{/if}>Origin</option>
															<option style="text-align:left" value="Destination" {if $CURRENT_STOP['stop_type'] eq 'Destination'}selected{/if}>Destination</option>
														</select>
														<input type="hidden" class="fieldname" value="stop_type_{$STOP_INDEX+1}" data-prev-value="{$CURRENT_STOP['stop_type']}">
													</span>
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
				</table>
			{/if*}
		{*else*}
			<table name="{$BLOCK_LABEL_KEY}" class="table table-bordered equalSplit detailview-table {if $BLOCK->get('hideblock') eq true}hide{/if}" {if $BLOCK_LABEL_KEY eq 'LBL_POTENTIALS_DATES'}style='table-layout:fixed'{/if}>
				<thead>
					<tr>
							<th class="blockHeader" {if  $BLOCK_LABEL_KEY eq 'LBL_POTENTIALS_DATES'}colspan="6"{else}colspan="4"{/if}>
									<img class="cursorPointer alignMiddle blockToggle {if !($IS_HIDDEN)} hide {/if} "  src="{vimage_path('arrowRight.png')}" data-mode="hide" data-id={$BLOCK_LIST[$BLOCK_LABEL_KEY]->get('id')}>
									<img class="cursorPointer alignMiddle blockToggle {if ($IS_HIDDEN)} hide {/if}"  src="{vimage_path('arrowDown.png')}" data-mode="show" data-id={$BLOCK_LIST[$BLOCK_LABEL_KEY]->get('id')}>
									&nbsp;&nbsp;{vtranslate({$BLOCK_LABEL_KEY},{$MODULE_NAME})}
							</th>
					</tr>
				</thead>
				<tbody {if $IS_HIDDEN} class="hide" {/if}>
					{assign var=COUNTER value=0}
						<tr>
							{foreach item=FIELD_MODEL key=FIELD_NAME from=$FIELD_MODEL_LIST}
							{if $FIELD_NAME eq 'leadsource_workspace' || $FIELD_NAME eq 'leadsource_national' || $FIELD_NAME eq 'shipper_type'}
								{continue}
							{/if}

								{if $OPP_TYPE neq 'National Account' && ($FIELD_NAME eq 'national_account_number' || $FIELD_NAME eq 'billing_apn' || $FIELD_NAME eq 'cbs_ind' || $FIELD_NAME eq 'ref_number' || $FIELD_NAME eq 'credit_check' || $FIELD_NAME eq 'credit_check_amount' || $FIELD_NAME eq 'ref_type')}{continue}{/if}

								{if $OPP_TYPE eq 'National Account' && ($FIELD_NAME eq 'agrmt_cod' || $FIELD_NAME eq 'subagrmt_cod')}{continue}{/if}

								{if $FIELD_NAME eq 'out_of_area' || $FIELD_NAME eq 'out_of_origin' || $FIELD_NAME eq 'potential_no'  || $FIELD_NAME eq 'small_move' || $FIELD_NAME eq 'phone_estimate' || $FIELD_NAME eq 'origin_phone1_ext' || $FIELD_NAME eq 'origin_phone2_ext' || $FIELD_NAME eq 'destination_phone1_ext' || $FIELD_NAME eq 'destination_phone2_ext' || ($FIELD_NAME eq 'business_line' && 'move_type'|array_key_exists:$FIELD_MODEL_LIST)}
									{continue}
								{/if}
							{* OT 16378 *}
								{if ($FIELD_NAME eq 'opportunities_vanline' || $FIELD_NAME eq 'opportunities_reason') && $SALES_STAGE_VAL neq 'Lost'}
									{continue}
								{elseif $FIELD_NAME eq 'opportunities_vanline' && $REASON_VAL neq 'Pricing'}
									{continue}
								{/if}
							{*
							* I can see no reason this shouldn't be shown.
							* but because I'm probably wrong... it's just commented out.
                                {if $FIELD_NAME eq 'self_haul' && !$AGENT_SELF_HAUL}
                                    {continue}
                                {/if}
							*}
								{* OT 5273 *}
								{if $FIELD_NAME eq 'opportunityreason' && $OPP_STATUS neq 'Cancelled'}
									{continue}
								{/if}
								{*OLD SECURITIES*}
								{assign var=AGENT_NO_FIELD value='false'}
								{* {if $AGENT_PERMISSIONS eq 'no_rates' && ($FIELD_NAME eq 'forecast_amount' or $FIELD_NAME eq 'amount' or $FIELD_NAME eq 'estimate_type')}{$AGENT_NO_FIELD = 'true'}{/if} *}
									{if $AGENT_NO_FIELD neq 'true'}
										{if $FIELD_NAME eq 'oi_push_notification_token'}
											{if $IS_OI_ENABLED neq 1}
												{continue}
											{/if}
										{/if}
										{if $FIELD_NAME eq 'dbx_token'}
											{if $IS_OI_ENABLED neq 1}
												<!-- O&I DISABLED -->
												{continue}
											{else}
												<!-- O&I ENABLED -->
												<!-- {$FIELD_MODEL->getDisplayValue($FIELD_MODEL->get('fieldvalue'))} -->
												{if $FIELD_MODEL->getDisplayValue($FIELD_MODEL->get('fieldvalue')) eq ''}
													<!-- No DBX Token set -->
													{if $COUNTER eq 2}
														</tr><tr>
														{assign var="COUNTER" value=1}
													{else}
														{assign var="COUNTER" value=$COUNTER+1}
													{/if}
													<td class="fieldLabel {$WIDTHTYPE}">
													<label class='muted pull-right marginRight10px'>
														{vtranslate({$FIELD_MODEL->get('label')},{$MODULE_NAME})}
													</label>
													</td>
													<td class="fieldValue {$WIDTHTYPE}">
													<span class="value" id="dropbox_auth_token">
														<button type="button" onclick="getDropboxAuth()">Get Dropbox Authorization Token</button>
													</span>
													</td>
												{else}
													<!-- DBX Token is set -->
													{if $COUNTER eq 2}
														</tr><tr>
														{assign var="COUNTER" value=1}
													{else}
														{assign var="COUNTER" value=$COUNTER+1}
													{/if}
													<td class="fieldLabel {$WIDTHTYPE}">
													<label class='muted pull-right marginRight10px'>
														{vtranslate({$FIELD_MODEL->get('label')},{$MODULE_NAME})}
													</label>
													</td>
													<td class="fieldValue {$WIDTHTYPE}">
													<span class="value" id="dropbox_auth_token">
														[hidden]
													</span>
													</td>
												{/if}
												{continue}
											{/if}
										{/if}
										{if !$FIELD_MODEL->isViewableInDetailView()}
											 {continue}
										 {/if}
										 {if $FIELD_MODEL->get('uitype') eq "83"}
											{foreach item=tax key=count from=$TAXCLASS_DETAILS}
											{if $tax.check_value eq 1}
												{if $COUNTER eq 2}
													</tr><tr>
													{assign var="COUNTER" value=1}
												{else}
													{assign var="COUNTER" value=$COUNTER+1}
												{/if}
												<td class="fieldLabel {$WIDTHTYPE}">
												<label class='muted pull-right marginRight10px'>{vtranslate($tax.taxlabel, $MODULE)}(%)</label>
												</td>
												 <td class="fieldValue {$WIDTHTYPE}">
													 <span class="value">
														 {$tax.percentage}
													 </span>
												 </td>
											{/if}
											{/foreach}
										{elseif $FIELD_MODEL->get('uitype') eq "69" || $FIELD_MODEL->get('uitype') eq "105"}
											{if $COUNTER neq 0}
												{if $COUNTER eq 2}
													</tr><tr>
													{assign var=COUNTER value=0}
												{/if}
											{/if}
											<td class="fieldLabel {$WIDTHTYPE}"><label class="muted pull-right marginRight10px">{vtranslate({$FIELD_MODEL->get('label')},{$MODULE_NAME})}</label></td>
											<td class="fieldValue {$WIDTHTYPE}">
												<div id="imageContainer" width="300" height="200">
													{foreach key=ITER item=IMAGE_INFO from=$IMAGE_DETAILS}
														{if !empty($IMAGE_INFO.path) && !empty({$IMAGE_INFO.orgname})}
															<img src="{$IMAGE_INFO.path}_{$IMAGE_INFO.orgname}" width="300" height="200">
														{/if}
													{/foreach}
												</div>
											</td>
											{assign var=COUNTER value=$COUNTER+1}
										{else}
											{if $FIELD_MODEL->get('uitype') eq "20" or $FIELD_MODEL->get('uitype') eq "19"}
												{if $COUNTER eq '1'}
													<td class="{$WIDTHTYPE} fieldLabel"></td><td class="{$WIDTHTYPE} fieldValue"></td></tr><tr>
													{assign var=COUNTER value=0}
												{/if}
											{/if}
											{if  $BLOCK_LABEL_KEY eq 'LBL_POTENTIALS_DATES'}
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
											 <td class="fieldLabel {$WIDTHTYPE} {if $FIELD_NAME eq 'disposition_lost_reasons' && $RECORD->get('sales_stage') neq 'Closed Lost'} hide{/if}"{if $BLOCK_LABEL_KEY eq 'LBL_POTENTIALS_DATES'}style='min-width:12.6%'{/if} id="{$MODULE_NAME}_detailView_fieldLabel_{$FIELD_MODEL->getName()}"{if $FIELD_MODEL->getName() eq 'description' or $FIELD_MODEL->get('uitype') eq '69'} style='width:8%'{/if}>
												 <label class="muted pull-right marginRight10px">
													 {vtranslate({$FIELD_MODEL->get('label')},{$MODULE_NAME})}
													 {if ($FIELD_MODEL->get('uitype') eq '72') && ($FIELD_MODEL->getName() eq 'unit_price')}
														({$BASE_CURRENCY_SYMBOL})
													{/if}
												 </label>
											 </td>
											 <td class="fieldValue {$WIDTHTYPE} {if $FIELD_NAME eq 'disposition_lost_reasons' && $RECORD->get('sales_stage') neq 'Closed Lost'} hide{/if}"{if $BLOCK_LABEL_KEY eq 'LBL_POTENTIALS_DATES'}style='min-width:16.6%'{/if} id="{$MODULE_NAME}_detailView_fieldValue_{$FIELD_MODEL->getName()}" {if $FIELD_MODEL->get('uitype') eq '19' or $FIELD_MODEL->get('uitype') eq '20'} colspan="3" {assign var=COUNTER value=$COUNTER+1} {/if}>
                                                 <span class="value" data-field-type="{$FIELD_MODEL->getFieldDataType()}" {if $FIELD_MODEL->get('uitype') eq '19' or $FIELD_MODEL->get('uitype') eq '20' or $FIELD_MODEL->get('uitype') eq '21'} style="white-space:normal;" {/if}>
													{include file=vtemplate_path($FIELD_MODEL->getUITypeModel()->getDetailViewTemplateName(),$MODULE_NAME) FIELD_MODEL=$FIELD_MODEL USER_MODEL=$USER_MODEL MODULE=$MODULE_NAME RECORD=$RECORD}
												 </span>
												 {if $IS_AJAX_ENABLED && $FIELD_MODEL->isEditable() eq 'true' && ($FIELD_MODEL->getFieldDataType()!=Vtiger_Field_Model::REFERENCE_TYPE) && $FIELD_MODEL->isAjaxEditable() eq 'true'}{* OLD SECURITIES && $CREATOR_PERMISSIONS eq 'true' *}
													 <span class="hide">
														{if $FIELD_NAME neq 'days_to_move'}
															{include file=vtemplate_path($FIELD_MODEL->getUITypeModel()->getTemplateName(),$MODULE_NAME) FIELD_MODEL=$FIELD_MODEL USER_MODEL=$USER_MODEL MODULE=$MODULE_NAME}
														{else}
															{assign var="FIELD_INFO" value=Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($FIELD_MODEL->getFieldInfo()))}
															{assign var="SPECIAL_VALIDATOR" value=$FIELD_MODEL->getValidator()}
															{assign var="FIELD_NAME" value=$FIELD_MODEL->get('name')}
															<input id="{$MODULE}_editView_fieldName_{$FIELD_NAME}" type="text" class="input-large {if $FIELD_MODEL->isNameField()}nameField{/if}" data-validation-engine="validate[{if $FIELD_MODEL->isMandatory() eq true}required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" name="{$FIELD_MODEL->getFieldName()}" value="{$FIELD_MODEL->get('fieldvalue')}"
															readonly data-fieldinfo='{$FIELD_INFO}' {if !empty($SPECIAL_VALIDATOR)}data-validator={Zend_Json::encode($SPECIAL_VALIDATOR)}{/if} />
														{/if}
														{if $FIELD_MODEL->getFieldDataType() eq 'multipicklist' || $FIELD_MODEL->getFieldDataType() eq 'multiagent'}
															<input type="hidden" class="fieldname" value='{$FIELD_MODEL->get('name')}[]' data-prev-value='{$FIELD_MODEL->getDisplayValue($FIELD_MODEL->get('fieldvalue'))}' />
														{else}
															 <input type="hidden" class="fieldname" value='{$FIELD_MODEL->get('name')}' data-prev-value='{Vtiger_Util_Helper::toSafeHTML($FIELD_MODEL->getDisplayValue($FIELD_MODEL->get('fieldvalue')))}' />
														 {/if}
													 </span>
												 {/if}
												 {if $FIELD_NAME eq 'origin_phone1' && 'origin_phone1_ext'|array_key_exists:$FIELD_MODEL_LIST}
													 &nbsp;
													 <span class="ext{if $FIELD_MODEL_LIST['origin_phone1_type']->get('fieldvalue') neq 'Work'} hide{/if}" data-field-type="{$FIELD_MODEL_LIST['origin_phone1_ext']->getFieldDataType()}" {if $FIELD_MODEL_LIST['origin_phone1_ext']->get('uitype') eq '19' or $FIELD_MODEL_LIST['origin_phone1_ext']->get('uitype') eq '20' or $FIELD_MODEL_LIST['origin_phone1_ext']->get('uitype') eq '21'} style="white-space:normal;" {/if} id="originPhone1ValueSpan">
														{if $FIELD_MODEL_LIST['origin_phone1_ext']->get('fieldvalue') neq ''}Ext. {/if}{include file=vtemplate_path($FIELD_MODEL_LIST['origin_phone1_ext']->getUITypeModel()->getDetailViewTemplateName(),$MODULE_NAME) FIELD_MODEL=$FIELD_MODEL_LIST['origin_phone1_ext'] USER_MODEL=$USER_MODEL MODULE=$MODULE_NAME RECORD=$RECORD}
													</span>
												{/if}
												{if $FIELD_NAME eq 'origin_phone2' && 'origin_phone2_ext'|array_key_exists:$FIELD_MODEL_LIST}
													 &nbsp;
													 <span class="ext{if $FIELD_MODEL_LIST['origin_phone2_type']->get('fieldvalue') neq 'Work'} hide{/if}" data-field-type="{$FIELD_MODEL_LIST['origin_phone2_ext']->getFieldDataType()}" {if $FIELD_MODEL_LIST['origin_phone2_ext']->get('uitype') eq '19' or $FIELD_MODEL_LIST['origin_phone2_ext']->get('uitype') eq '20' or $FIELD_MODEL_LIST['origin_phone2_ext']->get('uitype') eq '21'} style="white-space:normal;" {/if} id="originPhone2ValueSpan">
														{if $FIELD_MODEL_LIST['origin_phone2_ext']->get('fieldvalue') neq ''}Ext. {/if}{include file=vtemplate_path($FIELD_MODEL_LIST['origin_phone2_ext']->getUITypeModel()->getDetailViewTemplateName(),$MODULE_NAME) FIELD_MODEL=$FIELD_MODEL_LIST['origin_phone2_ext'] USER_MODEL=$USER_MODEL MODULE=$MODULE_NAME RECORD=$RECORD}
													</span>
												{/if}
												{if $FIELD_NAME eq 'destination_phone1' && 'destination_phone1_ext'|array_key_exists:$FIELD_MODEL_LIST}
													 &nbsp;
													 <span class="ext{if $FIELD_MODEL_LIST['destination_phone1_type']->get('fieldvalue') neq 'Work'} hide{/if}" data-field-type="{$FIELD_MODEL_LIST['destination_phone1_ext']->getFieldDataType()}" {if $FIELD_MODEL_LIST['destination_phone1_ext']->get('uitype') eq '19' or $FIELD_MODEL_LIST['destination_phone1_ext']->get('uitype') eq '20' or $FIELD_MODEL_LIST['destination_phone1_ext']->get('uitype') eq '21'} style="white-space:normal;" {/if} id="destinaitonPhone1ValueSpan">
														{if $FIELD_MODEL_LIST['destination_phone1_ext']->get('fieldvalue') neq ''}Ext. {/if}{include file=vtemplate_path($FIELD_MODEL_LIST['destination_phone1_ext']->getUITypeModel()->getDetailViewTemplateName(),$MODULE_NAME) FIELD_MODEL=$FIELD_MODEL_LIST['destination_phone1_ext'] USER_MODEL=$USER_MODEL MODULE=$MODULE_NAME RECORD=$RECORD}
													</span>
												{/if}
												{if $FIELD_NAME eq 'destination_phone2' && 'destination_phone2_ext'|array_key_exists:$FIELD_MODEL_LIST}
													 &nbsp;
													 <span class="ext{if $FIELD_MODEL_LIST['destination_phone2_type']->get('fieldvalue') neq 'Work'} hide{/if}" data-field-type="{$FIELD_MODEL_LIST['destination_phone2_ext']->getFieldDataType()}" {if $FIELD_MODEL_LIST['destination_phone2_ext']->get('uitype') eq '19' or $FIELD_MODEL_LIST['destination_phone2_ext']->get('uitype') eq '20' or $FIELD_MODEL_LIST['destination_phone2_ext']->get('uitype') eq '21'} style="white-space:normal;" {/if} id="destinaitonPhone2ValueSpan">
														{if $FIELD_MODEL_LIST['destination_phone2_ext']->get('fieldvalue') neq ''}Ext. {/if}{include file=vtemplate_path($FIELD_MODEL_LIST['destination_phone2_ext']->getUITypeModel()->getDetailViewTemplateName(),$MODULE_NAME) FIELD_MODEL=$FIELD_MODEL_LIST['destination_phone2_ext'] USER_MODEL=$USER_MODEL MODULE=$MODULE_NAME RECORD=$RECORD}
													</span>
												{/if}
												{if $FIELD_NAME eq 'origin_phone1' && 'origin_phone1_ext'|array_key_exists:$FIELD_MODEL_LIST}
													<span class="hide">
														<span id='originPhone1Span' {if $FIELD_MODEL_LIST['origin_phone1_type']->get('fieldvalue') neq 'Work'}class='hide'{/if}>
														&nbsp; Ext:&nbsp;
														{assign var="FIELD_INFO" value=Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($FIELD_MODEL_LIST['origin_phone1_ext']->getFieldInfo()))}
														{assign var="SPECIAL_VALIDATOR" value=$FIELD_MODEL_LIST['origin_phone1_ext']->getValidator()}
														{assign var="FIELD_LABEL" value=$FIELD_MODEL_LIST['origin_phone1_ext']->get('name')}
														<input id="{$MODULE}_editView_fieldName_{$FIELD_LABEL}" type="text" class="input-large {if $FIELD_MODEL_LIST['origin_phone1_ext']->isNameField()}nameField{/if}" data-validation-engine="validate[{if $FIELD_MODEL_LIST['origin_phone1_ext']->isMandatory() eq true}required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" name="{$FIELD_MODEL_LIST['origin_phone1_ext']->getFieldName()}" value="{$FIELD_MODEL_LIST['origin_phone1_ext']->get('fieldvalue')}"
														{if $FIELD_MODEL_LIST['origin_phone1_ext']->get('uitype') eq '3' || $FIELD_MODEL_LIST['origin_phone1_ext']->get('uitype') eq '4'|| $FIELD_MODEL_LIST['origin_phone1_ext']->isReadOnly()} readonly {/if} data-fieldinfo='{$FIELD_INFO}' {if !empty($SPECIAL_VALIDATOR)}data-validator={Zend_Json::encode($SPECIAL_VALIDATOR)}{/if} disabled />
														<style>
															[name=origin_phone1_ext]{
																width: 50px;
															}
														</style>
														&nbsp;
														 <input type="hidden" class="fieldname" value='{$FIELD_MODEL_LIST['origin_phone1_ext']->get('name')}' data-prev-value='{Vtiger_Util_Helper::toSafeHTML($FIELD_MODEL_LIST['origin_phone1_ext']->getDisplayValue($FIELD_MODEL_LIST['origin_phone1_ext']->get('fieldvalue')))}' />
														</span>
													</span>
												{/if}
												{if $FIELD_NAME eq 'origin_phone2' && 'origin_phone2_ext'|array_key_exists:$FIELD_MODEL_LIST}
													<span class="hide">
														<span id='originPhone2Span' {if $FIELD_MODEL_LIST['origin_phone2_type']->get('fieldvalue') neq 'Work'}class='hide'{/if}>
														&nbsp; Ext:&nbsp;
														{assign var="FIELD_INFO" value=Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($FIELD_MODEL_LIST['origin_phone2_ext']->getFieldInfo()))}
														{assign var="SPECIAL_VALIDATOR" value=$FIELD_MODEL_LIST['origin_phone2_ext']->getValidator()}
														{assign var="FIELD_LABEL" value=$FIELD_MODEL_LIST['origin_phone2_ext']->get('name')}
														<input id="{$MODULE}_editView_fieldName_{$FIELD_LABEL}" type="text" class="input-large {if $FIELD_MODEL_LIST['origin_phone2_ext']->isNameField()}nameField{/if}" data-validation-engine="validate[{if $FIELD_MODEL_LIST['origin_phone2_ext']->isMandatory() eq true}required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" name="{$FIELD_MODEL_LIST['origin_phone2_ext']->getFieldName()}" value="{$FIELD_MODEL_LIST['origin_phone2_ext']->get('fieldvalue')}"
														{if $FIELD_MODEL_LIST['origin_phone2_ext']->get('uitype') eq '3' || $FIELD_MODEL_LIST['origin_phone2_ext']->get('uitype') eq '4'|| $FIELD_MODEL_LIST['origin_phone2_ext']->isReadOnly()} readonly {/if} data-fieldinfo='{$FIELD_INFO}' {if !empty($SPECIAL_VALIDATOR)}data-validator={Zend_Json::encode($SPECIAL_VALIDATOR)}{/if} disabled />
														<style>
															[name=origin_phone2_ext]{
																width: 50px;
															}
														</style>
														&nbsp;
														<input type="hidden" class="fieldname" value='{$FIELD_MODEL_LIST['origin_phone2_ext']->get('name')}' data-prev-value='{Vtiger_Util_Helper::toSafeHTML($FIELD_MODEL_LIST['origin_phone2_ext']->getDisplayValue($FIELD_MODEL_LIST['origin_phone2_ext']->get('fieldvalue')))}' />
														</span>
													</span>
												{/if}
                                                {if $FIELD_NAME eq 'destination_phone1' && 'destination_phone1_ext'|array_key_exists:$FIELD_MODEL_LIST}
													<span class="hide">
														<span id='destinationPhone1Span' {if $FIELD_MODEL_LIST['destination_phone1_type']->get('fieldvalue') neq 'Work'}class='hide'{/if}>
														&nbsp; Ext:&nbsp;
														{assign var="FIELD_INFO" value=Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($FIELD_MODEL_LIST['destination_phone1_ext']->getFieldInfo()))}
														{assign var="SPECIAL_VALIDATOR" value=$FIELD_MODEL_LIST['destination_phone1_ext']->getValidator()}
														{assign var="FIELD_LABEL" value=$FIELD_MODEL_LIST['destination_phone1_ext']->get('name')}
														<input id="{$MODULE}_editView_fieldName_{$FIELD_LABEL}" type="text" class="input-large {if $FIELD_MODEL_LIST['destination_phone1_ext']->isNameField()}nameField{/if}" data-validation-engine="validate[{if $FIELD_MODEL_LIST['destination_phone1_ext']->isMandatory() eq true}required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" name="{$FIELD_MODEL_LIST['destination_phone1_ext']->getFieldName()}" value="{$FIELD_MODEL_LIST['destination_phone1_ext']->get('fieldvalue')}"
														{if $FIELD_MODEL_LIST['destination_phone1_ext']->get('uitype') eq '3' || $FIELD_MODEL_LIST['destination_phone1_ext']->get('uitype') eq '4'|| $FIELD_MODEL_LIST['destination_phone1_ext']->isReadOnly()} readonly {/if} data-fieldinfo='{$FIELD_INFO}' {if !empty($SPECIAL_VALIDATOR)}data-validator={Zend_Json::encode($SPECIAL_VALIDATOR)}{/if} disabled />
														<style>
															[name=destination_phone1_ext]{
																width: 50px;
															}
														</style>
														&nbsp;
														<input type="hidden" class="fieldname" value='{$FIELD_MODEL_LIST['destination_phone1_ext']->get('name')}' data-prev-value='{Vtiger_Util_Helper::toSafeHTML($FIELD_MODEL_LIST['destination_phone1_ext']->getDisplayValue($FIELD_MODEL_LIST['destination_phone1_ext']->get('fieldvalue')))}' />
														</span>
													</span>
												{/if}
												{if $FIELD_NAME eq 'destination_phone2' && 'destination_phone2_ext'|array_key_exists:$FIELD_MODEL_LIST}
													<span class="hide">
														<span id='destinationPhone2Span' {if $FIELD_MODEL_LIST['destination_phone2_type']->get('fieldvalue') neq 'Work'}class='hide'{/if}>
														&nbsp; Ext:&nbsp;
														{assign var="FIELD_INFO" value=Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($FIELD_MODEL_LIST['destination_phone2_ext']->getFieldInfo()))}
														{assign var="SPECIAL_VALIDATOR" value=$FIELD_MODEL_LIST['destination_phone2_ext']->getValidator()}
														{assign var="FIELD_LABEL" value=$FIELD_MODEL_LIST['destination_phone2_ext']->get('name')}
														<input id="{$MODULE}_editView_fieldName_{$FIELD_LABEL}" type="text" class="input-large {if $FIELD_MODEL_LIST['destination_phone2_ext']->isNameField()}nameField{/if}" data-validation-engine="validate[{if $FIELD_MODEL_LIST['destination_phone2_ext']->isMandatory() eq true}required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" name="{$FIELD_MODEL_LIST['destination_phone2_ext']->getFieldName()}" value="{$FIELD_MODEL_LIST['destination_phone2_ext']->get('fieldvalue')}"
														{if $FIELD_MODEL_LIST['destination_phone2_ext']->get('uitype') eq '3' || $FIELD_MODEL_LIST['destination_phone2_ext']->get('uitype') eq '4'|| $FIELD_MODEL_LIST['destination_phone2_ext']->isReadOnly()} readonly {/if} data-fieldinfo='{$FIELD_INFO}' {if !empty($SPECIAL_VALIDATOR)}data-validator={Zend_Json::encode($SPECIAL_VALIDATOR)}{/if} disabled />
														<style>
															[name=destination_phone2_ext]{
																width: 50px;
															}
														</style>
														&nbsp;
														<input type="hidden" class="fieldname" value='{$FIELD_MODEL_LIST['destination_phone2_ext']->get('name')}' data-prev-value='{Vtiger_Util_Helper::toSafeHTML($FIELD_MODEL_LIST['destination_phone2_ext']->getDisplayValue($FIELD_MODEL_LIST['destination_phone2_ext']->get('fieldvalue')))}' />
														</span>
													</span>
												{/if}
                                                 {if $SHOW_TRANSIT_GUIDE && ($FIELD_NAME eq 'load_date')}
                                                     <span id="TransitGuide">
             <button type="button" class="transitGuide" name="transitGuide"><strong>{vtranslate('LBL_TRANSIT_GUIDE', $MODULE)}</strong></button>
                                                     </span>
                                                 {/if}
											 </td>
										 {/if}
                                    {if $FIELD_NAME eq 'disposition_lost_reasons'  && $RECORD->get('sales_stage') neq 'Closed Lost'}
                                        <td class="dispLostFiller emptyTD fieldLabel {$WIDTHTYPE}"></td>
                                        <td class="dispLostFiller emptyTD fieldValue {$WIDTHTYPE}"></td>
                                    {/if}

									{if $FIELD_MODEL_LIST|@count eq 1 and $FIELD_MODEL->get('uitype') neq "19" and $FIELD_MODEL->get('uitype') neq "20" and $FIELD_MODEL->get('uitype') neq "30" and $FIELD_MODEL->get('name') neq "recurringtype" and $FIELD_MODEL->get('uitype') neq "69" and $FIELD_MODEL->get('uitype') neq "105"}
										<td class="fieldLabel {$WIDTHTYPE}"></td><td class="{$WIDTHTYPE}"></td>
									{/if}
								{/if}

                                {* BEGIN disposition_lost_details *}
					{if $FIELD_NAME == 'disposition_lost_reasons' && getenv('INSTANCE_NAME') == 'sirva'}
					{*$BLOCK_FIELDS|@end eq true and $BLOCK_FIELDS|@count neq 1 and *}
					{if $COUNTER eq 1}
						<td class="fieldLabel emptyTD {$WIDTHTYPE}"></td><td class="{$WIDTHTYPE} emptyTD"></td></tr>
						{assign var=COUNTER value=0}
					{/if}
						<tr colspan="4" style="padding:0;" class="hide pricingCompList">
							<td colspan="4" style="padding:0;">
								<table colspan="4" class="table table-bordered equalSplit" style="padding: 0; border: 0">
									<tbody>
									{assign var=LIST_WIDTH value=(1/8)*100}
									<tr class="fieldLabel">
										<td colspan="8" class="blockHeader">
											<span class="PricingCompetitorList"><b>&nbsp;&nbsp;&nbsp;Pricing Competitors</b></span>
										</td>
									</tr>
									<td class="fieldLabel {$WIDTHTYPE}" style= "width: {$LIST_WIDTH}%"></td>
									<td class="fieldLabel {$WIDTHTYPE}" style= "width: {$LIST_WIDTH}%"><label class="muted" style="text-align: center">Booked</label></td>
									<td class="fieldLabel {$WIDTHTYPE}" style= "width: {$LIST_WIDTH}%"><label class="muted" style="text-align: center">Estimate Provided</label></td>
									<td class="fieldLabel {$WIDTHTYPE}" style= "width: {$LIST_WIDTH}%"><label class="muted" style="text-align: center">None</label></td>
									<td class="fieldLabel {$WIDTHTYPE}" style= "width: {$LIST_WIDTH}%"></td>
									<td class="fieldLabel {$WIDTHTYPE}" style= "width: {$LIST_WIDTH}%"><label class="muted" style="text-align: center">Booked</label></td>
									<td class="fieldLabel {$WIDTHTYPE}" style= "width: {$LIST_WIDTH}%"><label class="muted" style="text-align: center">Estimate Provided</label></td>
									<td class="fieldLabel {$WIDTHTYPE}" style= "width: {$LIST_WIDTH}%"><label class="muted" style="text-align: center">None</label></td>
									<tr>
									</tr>
									<tr>
										<td class="fieldLabel {$WIDTHTYPE}" style= "width: {$LIST_WIDTH}%">
											<label class="muted pull-right marginRight10px">Allied</label>
										</td>
										<td class="fieldValue comp_allied {$WIDTHTYPE}" style= "width: {$LIST_WIDTH}%;text-align:center;margin:auto">
											<span class="value" data-field-type="boolean">{if $COMPS['allied'] eq 2}Yes{else}No{/if}</span>
									<span class="edit hide">
										<input type="hidden" class="fieldname" value="comp_allied" data-prev-value="{$COMPS['allied']}">
										<input type="hidden" name="comp_allied_prev" value="0">
										<input type="radio" name="comp_allied" value="2" {if $COMPS['allied'] eq 2}checked{/if}>
									</span>
										</td>
										<td class="fieldValue comp_allied {$WIDTHTYPE}" style= "width: {$LIST_WIDTH}%;text-align:center;margin:auto">
											<span class="value" data-field-type="boolean">{if $COMPS['allied'] eq 1}Yes{else}No{/if}</span>
									<span class="edit hide">
										<input type="hidden" class="fieldname" value="comp_allied" data-prev-value="{$COMPS['allied']}">
										<input type="hidden" name="comp_allied_prev" value="0">
										<input type="radio" name="comp_allied" value="1" {if $COMPS['allied'] eq 1}checked{/if}>
									</span>
										</td>
										<td class="fieldValue comp_allied {$WIDTHTYPE}" style= "width: {$LIST_WIDTH}%;text-align:center;margin:auto">
											<span class="value" data-field-type="boolean">{if $COMPS['allied'] eq 0}Yes{else}No{/if}</span>
									<span class="edit hide">
										<input type="hidden" class="fieldname" value="comp_allied" data-prev-value="{$COMPS['allied']}">
										<input type="hidden" name="comp_allied_prev" value="0">
										<input type="radio" name="comp_allied" value="0" {if $COMPS['allied'] eq 0}checked{/if}>
									</span>
										</td>
										<td class="fieldLabel {$WIDTHTYPE}" style= "width: {$LIST_WIDTH}%">
											<label class="muted pull-right marginRight10px">Atlas</label>
										</td>
										<td class="fieldValue comp_atlas {$WIDTHTYPE}" style= "width: {$LIST_WIDTH}%;text-align:center;margin:auto">
											<span class="value" data-field-type="boolean">{if $COMPS['atlas'] eq 2}Yes{else}No{/if}</span>
									<span class="edit hide">
										<input type="hidden" class="fieldname" value="comp_atlas" data-prev-value="{$COMPS['atlas']}">
										<input type="hidden" name="comp_atlas_prev" value="0">
										<input type="radio" name="comp_atlas" value="2" {if $COMPS['atlas'] eq 2}checked{/if}>
									</span>
										</td>
										<td class="fieldValue comp_atlas {$WIDTHTYPE}" style= "width: {$LIST_WIDTH}%;text-align:center;margin:auto">
											<span class="value" data-field-type="boolean">{if $COMPS['atlas'] eq 1}Yes{else}No{/if}</span>
									<span class="edit hide">
										<input type="hidden" class="fieldname" value="comp_atlas" data-prev-value="{$COMPS['atlas']}">
										<input type="hidden" name="comp_atlas_prev" value="0">
										<input type="radio" name="comp_atlas" value="1" {if $COMPS['atlas'] eq 1}checked{/if}>
									</span>
										</td>
										<td class="fieldValue comp_atlas {$WIDTHTYPE}" style= "width: {$LIST_WIDTH}%;text-align:center;margin:auto">
											<span class="value" data-field-type="boolean">{if $COMPS['atlas'] eq 0}Yes{else}No{/if}</span>
									<span class="edit hide">
										<input type="hidden" class="fieldname" value="comp_atlas" data-prev-value="{$COMPS['atlas']}">
										<input type="hidden" name="comp_atlas_prev" value="0">
										<input type="radio" name="comp_atlas" value="0" {if $COMPS['atlas'] eq 0}checked{/if}>
									</span>
										</td>
									</tr>
									<tr>
										<td class="fieldLabel {$WIDTHTYPE}" style= "width: {$LIST_WIDTH}%">
											<label class="muted pull-right marginRight10px">Mayflower</label>
										</td>
										<td class="fieldValue comp_mayflower {$WIDTHTYPE}" style= "width: {$LIST_WIDTH}%;text-align:center;margin:auto">
											<span class="value" data-field-type="boolean">{if $COMPS['mayflower'] eq 2}Yes{else}No{/if}</span>
									<span class="edit hide">
										<input type="hidden" class="fieldname" value="comp_mayflower" data-prev-value="{$COMPS['mayflower']}">
										<input type="hidden" name="comp_mayflower_prev" value="0">
										<input type="radio" name="comp_mayflower" value="2" {if $COMPS['mayflower'] eq 2}checked{/if}>
									</span>
										</td>
										<td class="fieldValue comp_mayflower {$WIDTHTYPE}" style= "width: {$LIST_WIDTH}%;text-align:center;margin:auto">
											<span class="value" data-field-type="boolean">{if $COMPS['mayflower'] eq 1}Yes{else}No{/if}</span>
									<span class="edit hide">
										<input type="hidden" class="fieldname" value="comp_mayflower" data-prev-value="{$COMPS['mayflower']}">
										<input type="hidden" name="comp_mayflower_prev" value="0">
										<input type="radio" name="comp_mayflower" value="1" {if $COMPS['mayflower'] eq 1}checked{/if}>
									</span>
										</td>
										<td class="fieldValue comp_mayflower {$WIDTHTYPE}" style= "width: {$LIST_WIDTH}%;text-align:center;margin:auto">
											<span class="value" data-field-type="boolean">{if $COMPS['mayflower'] eq 0}Yes{else}No{/if}</span>
									<span class="edit hide">
										<input type="hidden" class="fieldname" value="comp_mayflower" data-prev-value="{$COMPS['mayflower']}">
										<input type="hidden" name="comp_mayflower_prev" value="0">
										<input type="radio" name="comp_mayflower" value="0" {if $COMPS['mayflower'] eq 0}checked{/if}>
									</span>
										</td>
										<td class="fieldLabel {$WIDTHTYPE}" style= "width: {$LIST_WIDTH}%">
											<label class="muted pull-right marginRight10px">North American</label>
										</td>
										<td class="fieldValue comp_northamerican {$WIDTHTYPE}" style= "width: {$LIST_WIDTH}%;text-align:center;margin:auto">
											<span class="value" data-field-type="boolean">{if $COMPS['north_american'] eq 2}Yes{else}No{/if}</span>
									<span class="edit hide">
										<input type="hidden" class="fieldname" value="comp_northamerican" data-prev-value="{$COMPS['north_american']}">
										<input type="hidden" name="comp_northamerican_prev" value="0">
										<input type="radio" name="comp_northamerican" value="2" {if $COMPS['north_american'] eq 2}checked{/if}>
									</span>
										</td>
										<td class="fieldValue comp_northamerican {$WIDTHTYPE}" style= "width: {$LIST_WIDTH}%;text-align:center;margin:auto">
											<span class="value" data-field-type="boolean">{if $COMPS['north_american'] eq 1}Yes{else}No{/if}</span>
									<span class="edit hide">
										<input type="hidden" class="fieldname" value="comp_northamerican" data-prev-value="{$COMPS['north_american']}">
										<input type="hidden" name="comp_northamerican_prev" value="0">
										<input type="radio" name="comp_northamerican" value="1" {if $COMPS['north_american'] eq 1}checked{/if}>
									</span>
										</td>
										<td class="fieldValue comp_northamerican {$WIDTHTYPE}" style= "width: {$LIST_WIDTH}%;text-align:center;margin:auto">
											<span class="value" data-field-type="boolean">{if $COMPS['north_american'] eq 0}Yes{else}No{/if}</span>
									<span class="edit hide">
										<input type="hidden" class="fieldname" value="comp_northamerican" data-prev-value="{$COMPS['north_american']}">
										<input type="hidden" name="comp_northamerican_prev" value="0">
										<input type="radio" name="comp_northamerican" value="0" {if $COMPS['north_american'] eq 0}checked{/if}>
									</span>
										</td>
									</tr>
									<tr>
										<td class="fieldLabel {$WIDTHTYPE}" style= "width: {$LIST_WIDTH}%">
											<label class="muted pull-right marginRight10px">United</label>
										</td>
										<td class="fieldValue comp_united {$WIDTHTYPE}" style= "width: {$LIST_WIDTH}%;text-align:center;margin:auto">
											<span class="value" data-field-type="boolean">{if $COMPS['united'] eq 2}Yes{else}No{/if}</span>
									<span class="edit hide">
										<input type="hidden" class="fieldname" value="comp_united" data-prev-value="{$COMPS['united']}">
										<input type="hidden" name="comp_united_prev" value="0">
										<input type="radio" name="comp_united" value="2" {if $COMPS['united'] eq 2}checked{/if}>
									</span>
										</td>
										<td class="fieldValue comp_united {$WIDTHTYPE}" style= "width: {$LIST_WIDTH}%;text-align:center;margin:auto">
											<span class="value" data-field-type="boolean">{if $COMPS['united'] eq 1}Yes{else}No{/if}</span>
									<span class="edit hide">
										<input type="hidden" class="fieldname" value="comp_united" data-prev-value="{$COMPS['united']}">
										<input type="hidden" name="comp_united_prev" value="0">
										<input type="radio" name="comp_united" value="1" {if $COMPS['united'] eq 1}checked{/if}>
									</span>
										</td>
										<td class="fieldValue comp_united {$WIDTHTYPE}" style= "width: {$LIST_WIDTH}%;text-align:center;margin:auto">
											<span class="value" data-field-type="boolean">{if $COMPS['united'] eq 0}Yes{else}No{/if}</span>
									<span class="edit hide">
										<input type="hidden" class="fieldname" value="comp_united" data-prev-value="{$COMPS['united']}">
										<input type="hidden" name="comp_united_prev" value="0">
										<input type="radio" name="comp_united" value="0" {if $COMPS['united'] eq 0}checked{/if}>
									</span>
										</td>
										<td class="fieldLabel {$WIDTHTYPE}" style= "width: {$LIST_WIDTH}%">
											<label class="muted pull-right marginRight10px">Independent</label>
										</td>
										<td class="fieldValue comp_independent {$WIDTHTYPE}" style= "width: {$LIST_WIDTH}%;text-align:center;margin:auto">
											<span class="value" data-field-type="boolean">{if $COMPS['independent'] eq 2}Yes{else}No{/if}</span>
									<span class="edit hide">
										<input type="hidden" class="fieldname" value="comp_independent" data-prev-value="{$COMPS['independent']}">
										<input type="hidden" name="comp_independent_prev" value="0">
										<input type="radio" name="comp_independent" value="2" {if $COMPS['independent'] eq 2}checked{/if}>
									</span>
										</td>
										<td class="fieldValue comp_independent {$WIDTHTYPE}" style= "width: {$LIST_WIDTH}%;text-align:center;margin:auto">
											<span class="value" data-field-type="boolean">{if $COMPS['independent'] eq 1}Yes{else}No{/if}</span>
									<span class="edit hide">
										<input type="hidden" class="fieldname" value="comp_independent" data-prev-value="{$COMPS['independent']}">
										<input type="hidden" name="comp_independent_prev" value="0">
										<input type="radio" name="comp_independent" value="1" {if $COMPS['independent'] eq 1}checked{/if}>
									</span>
										</td>
										<td class="fieldValue comp_independent {$WIDTHTYPE}" style= "width: {$LIST_WIDTH}%;text-align:center;margin:auto">
											<span class="value" data-field-type="boolean">{if $COMPS['independent'] eq 0}Yes{else}No{/if}</span>
									<span class="edit hide">
										<input type="hidden" class="fieldname" value="comp_independent" data-prev-value="{$COMPS['independent']}">
										<input type="hidden" name="comp_independent_prev" value="0">
										<input type="radio" name="comp_independent" value="0" {if $COMPS['independent'] eq 0}checked{/if}>
									</span>
										</td>
									</tr>
									<tr>
										<td class="fieldLabel {$WIDTHTYPE}" style= "width: {$LIST_WIDTH}%">
											<label class="muted pull-right marginRight10px">Other</label>
										</td>
										<td class="fieldValue comp_other {$WIDTHTYPE}" style= "width: {$LIST_WIDTH}%;text-align:center;margin:auto">
											<span class="value" data-field-type="boolean">{if $COMPS['other'] eq 2}Yes{else}No{/if}</span>
									<span class="edit hide">
										<input type="hidden" class="fieldname" value="comp_other" data-prev-value="{$COMPS['other']}">
										<input type="hidden" name="comp_other_prev" value="0">
										<input type="radio" name="comp_other" value="2" {if $COMPS['other'] eq 2}checked{/if}>
									</span>
										</td>
										<td class="fieldValue comp_other {$WIDTHTYPE}" style= "width: {$LIST_WIDTH}%;text-align:center;margin:auto">
											<span class="value" data-field-type="boolean">{if $COMPS['other'] eq 1}Yes{else}No{/if}</span>
									<span class="edit hide">
										<input type="hidden" class="fieldname" value="comp_other" data-prev-value="{$COMPS['other']}">
										<input type="hidden" name="comp_other_prev" value="0">
										<input type="radio" name="comp_other" value="1" {if $COMPS['other'] eq 1}checked{/if}>
									</span>
										</td>
										<td class="fieldValue comp_other {$WIDTHTYPE}" style= "width: {$LIST_WIDTH}%;text-align:center;margin:auto">
											<span class="value" data-field-type="boolean">{if $COMPS['other'] eq 0}Yes{else}No{/if}</span>
									<span class="edit hide">
										<input type="hidden" class="fieldname" value="comp_other" data-prev-value="{$COMPS['other']}">
										<input type="hidden" name="comp_other_prev" value="0">
										<input type="radio" name="comp_other" value="0" {if $COMPS['other'] eq 0}checked{/if}>
									</span>
										</td>
										<td class="fieldLabel {$WIDTHTYPE}" style= "width: {$LIST_WIDTH}%"></td>
										<td class="fieldValue {$WIDTHTYPE}" style= "width: {$LIST_WIDTH}%"></td>
										<td class="fieldValue {$WIDTHTYPE}" style= "width: {$LIST_WIDTH}%"></td>
										<td class="fieldValue {$WIDTHTYPE}" style= "width: {$LIST_WIDTH}%"></td>
									</tr>
									</tbody>
								</table>
							</td>
						</tr>
					{/if}
                                {* END disposition_lost_details *}
							{/foreach}
						{* adding additional column for odd number of fields in a block *}
						{if $BLOCK_LABEL_KEY eq 'LBL_POTENTIALS_DATES' and $FIELD_MODEL_LIST|@end eq true and $FIELD_MODEL_LIST|@count neq 1}
							{if $COUNTER eq 1}
								<td class="fieldLabel {$WIDTHTYPE}"></td><td class="{$WIDTHTYPE}"></td><td class="fieldLabel {$WIDTHTYPE}"></td><td class="{$WIDTHTYPE}"></td>
							{elseif $COUNTER eq 2}
								<td class="fieldLabel {$WIDTHTYPE}"></td><td class="{$WIDTHTYPE}"></td>
							{/if}
						{elseif $FIELD_MODEL_LIST|@end eq true and $FIELD_MODEL_LIST|@count neq 1 and $COUNTER eq 1}
							<td class="fieldLabel {$WIDTHTYPE}"></td><td class="fieldValue {$WIDTHTYPE}"></td>
						{/if}
						</tr>
					{*/if*}
				{if ($BLOCK_LABEL_KEY eq 'LBL_OPPORTUNITY_INFORMATION' || $BLOCK_LABEL_KEY eq 'LBL_POTENTIALS_INFORMATION') && 'out_of_area'|array_key_exists:$FIELD_MODEL_LIST && 'out_of_origin'|array_key_exists:$FIELD_MODEL_LIST && 'small_move'|array_key_exists:$FIELD_MODEL_LIST && 'phone_estimate'|array_key_exists:$FIELD_MODEL_LIST}
                {if getenv('INSTANCE_NAME') == 'sirva'}
                    {continue}
                {/if}
					<tr>
						<td colspan='1' class="fieldLabel"><label class="muted pull-right marginRight10px" style="padding-right: 5px">{vtranslate('LBL_OPPORTUNITY_NONCONFORMING',$MODULE_NAME)}</label></td>
						<td colspan='3' style="padding: 0">
							<table class="table table-bordered equalSplit" style="padding: 0; border: 0">
								<tbody>
									<tr>
										<th><label class="muted" style="text-align: center">Out of Area</label></th>
										<th><label class="muted" style="text-align: center">Out of Origin</label></th>
										<th><label class="muted" style="text-align: center">Small Move</label></th>
										<th><label class="muted" style="text-align: center">Phone Estimate</label></th>
									</tr>
									<tr>
										<td style='width: 25%; text-align: center' class="fieldValue medium narrowWidthType" id="Leads_detailView_fieldValue_out_of_area">
											{assign var="FIELD_INFO" value=Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($FIELD_MODEL_LIST['out_of_area']->getFieldInfo()))}
											{assign var="SPECIAL_VALIDATOR" value=$FIELD_MODEL_LIST['out_of_area']->getValidator()}
											{assign var="FIELD_NAME" value=$FIELD_MODEL_LIST['out_of_area']->get('name')}
											<span class='value' data-field-type='boolean'>
												{if $FIELD_MODEL_LIST['out_of_area']->get('fieldvalue') eq 1}Yes{else}No{/if}
											</span>
											<span class='hide'>
												<input type="hidden" name="{$FIELD_MODEL_LIST['out_of_area']->getFieldName()}" value=0 />
												<input id="{$MODULE}_editView_fieldName_{$FIELD_NAME}" type="checkbox" name="{$FIELD_MODEL_LIST['out_of_area']->getFieldName()}" data-validation-engine="validate[funcCall[Vtiger_Base_Validator_Js.invokeValidation]]"
												{if $FIELD_MODEL_LIST['out_of_area']->get('fieldvalue') eq true} checked
												{/if} data-fieldinfo='{$FIELD_INFO}' {if !empty($SPECIAL_VALIDATOR)}data-validator={Zend_Json::encode($SPECIAL_VALIDATOR)}{/if} />
												<input type="hidden" class="fieldname" value="out_of_area" data-prev-value="{if $FIELD_MODEL_LIST['out_of_area']->get('fieldvalue') eq 1}Yes{else}No{/if}">
											</span>
										</td>
										<td style='width: 25%; text-align: center' class="fieldValue medium narrowWidthType" id="Leads_detailView_fieldValue_out_of_origin">
											{assign var="FIELD_INFO" value=Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($FIELD_MODEL_LIST['out_of_origin']->getFieldInfo()))}
											{assign var="SPECIAL_VALIDATOR" value=$FIELD_MODEL_LIST['out_of_origin']->getValidator()}
											{assign var="FIELD_NAME" value=$FIELD_MODEL_LIST['out_of_origin']->get('name')}
											<span class='value' data-field-type='boolean'>
												{if $FIELD_MODEL_LIST['out_of_origin']->get('fieldvalue') eq 1}Yes{else}No{/if}
											</span>
											<span class='hide'>
												<input type="hidden" name="{$FIELD_MODEL_LIST['out_of_origin']->getFieldName()}" value=0 />
												<input id="{$MODULE}_editView_fieldName_{$FIELD_NAME}" type="checkbox" name="{$FIELD_MODEL_LIST['out_of_origin']->getFieldName()}" data-validation-engine="validate[funcCall[Vtiger_Base_Validator_Js.invokeValidation]]"
												{if $FIELD_MODEL_LIST['out_of_origin']->get('fieldvalue') eq true} checked
												{/if} data-fieldinfo='{$FIELD_INFO}' {if !empty($SPECIAL_VALIDATOR)}data-validator={Zend_Json::encode($SPECIAL_VALIDATOR)}{/if} />
												<input type="hidden" class="fieldname" value="out_of_origin" data-prev-value="{if $FIELD_MODEL_LIST['out_of_origin']->get('fieldvalue') eq 1}Yes{else}No{/if}">
											</span>
										</td>
										<td style='width: 25%; text-align: center' class="fieldValue medium narrowWidthType" id="Leads_detailView_fieldValue_small_move">
											{assign var="FIELD_INFO" value=Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($FIELD_MODEL_LIST['small_move']->getFieldInfo()))}
											{assign var="SPECIAL_VALIDATOR" value=$FIELD_MODEL_LIST['small_move']->getValidator()}
											{assign var="FIELD_NAME" value=$FIELD_MODEL_LIST['small_move']->get('name')}
											<span class='value' data-field-type='boolean'>
												{if $FIELD_MODEL_LIST['small_move']->get('fieldvalue') eq 1}Yes{else}No{/if}
											</span>
											<span class='hide'>
												<input type="hidden" name="{$FIELD_MODEL_LIST['small_move']->getFieldName()}" value=0 />
												<input id="{$MODULE}_editView_fieldName_{$FIELD_NAME}" type="checkbox" name="{$FIELD_MODEL_LIST['small_move']->getFieldName()}" data-validation-engine="validate[funcCall[Vtiger_Base_Validator_Js.invokeValidation]]"
												{if $FIELD_MODEL_LIST['small_move']->get('fieldvalue') eq true} checked
												{/if} data-fieldinfo='{$FIELD_INFO}' {if !empty($SPECIAL_VALIDATOR)}data-validator={Zend_Json::encode($SPECIAL_VALIDATOR)}{/if} />
												<input type="hidden" class="fieldname" value="small_move" data-prev-value="{if $FIELD_MODEL_LIST['small_move']->get('fieldvalue') eq 1}Yes{else}No{/if}">
											</span>
										</td>
										<td style='width: 25%; text-align: center' class="fieldValue medium narrowWidthType" id="Leads_detailView_fieldValue_phone_estimate">
											{assign var="FIELD_INFO" value=Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($FIELD_MODEL_LIST['phone_estimate']->getFieldInfo()))}
											{assign var="SPECIAL_VALIDATOR" value=$FIELD_MODEL_LIST['phone_estimate']->getValidator()}
											{assign var="FIELD_NAME" value=$FIELD_MODEL_LIST['phone_estimate']->get('name')}
											<span class='value' data-field-type='boolean'>
												{if $FIELD_MODEL_LIST['phone_estimate']->get('fieldvalue') eq 1}Yes{else}No{/if}
											</span>
											<span class='hide'>
											<input type="hidden" name="{$FIELD_MODEL_LIST['phone_estimate']->getFieldName()}" value=0 />
												<input id="{$MODULE}_editView_fieldName_{$FIELD_NAME}" type="checkbox" name="{$FIELD_MODEL_LIST['phone_estimate']->getFieldName()}" data-validation-engine="validate[funcCall[Vtiger_Base_Validator_Js.invokeValidation]]"
												{if $FIELD_MODEL_LIST['phone_estimate']->get('fieldvalue') eq true} checked
												{/if} data-fieldinfo='{$FIELD_INFO}' {if !empty($SPECIAL_VALIDATOR)}data-validator={Zend_Json::encode($SPECIAL_VALIDATOR)}{/if} />
												<input type="hidden" class="fieldname" value="phone_estimate" data-prev-value="{if $FIELD_MODEL_LIST['phone_estimate']->get('fieldvalue') eq 1}Yes{else}No{/if}">
											</span>
										</td>
									</tr>
								</tbody>
							</table>
						</td>
					</tr>
				{/if}
				{if ($BLOCK_LABEL_KEY eq 'LBL_OPPORTUNITY_INFORMATION' || $BLOCK_LABEL_KEY eq 'LBL_POTENTIALS_INFORMATION') && 'move_type'|array_key_exists:$FIELD_MODEL_LIST}
					<tr class="hide">
						<td class="fieldLabel medium narrowWidthType">
							<label class="muted pull-right marginRight10px">{vtranslate($FIELD_MODEL_LIST['business_line']->get('label'), $MODULE)}</label>
						</td>
						<td class="fieldValue medium narrowWidthType" id="Leads_detailView_fieldValue_business_line">
							<span class='hide'>
								<span class="value hide" data-field-type="picklist">
									{$FIELD_MODEL_LIST['business_line']->get('fieldvalue')}
								</span>
								{assign var="FIELD_INFO" value=Zend_Json::encode($FIELD_MODEL_LIST['business_line']->getFieldInfo())}
								{assign var=PICKLIST_VALUES value=$FIELD_MODEL_LIST['business_line']->getPicklistValues()}
								{assign var="SPECIAL_VALIDATOR" value=$FIELD_MODEL_LIST['business_line']->getValidator()}
								{if $FIELD_MODEL_LIST['business_line']->get('name') eq {$BLFIELD}}
									<select onchange="loadBlocksByBusinesLine('{$MODULE}', '{$BLFIELD}')" class="chzn-select {if $OCCUPY_COMPLETE_WIDTH} row-fluid {/if}" name="{$FIELD_MODEL_LIST['business_line']->getFieldName()}" data-validation-engine="validate[{if $FIELD_MODEL_LIST['business_line']->isMandatory() eq true} required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" data-fieldinfo='{$FIELD_INFO|escape}' {if !empty($SPECIAL_VALIDATOR)}data-validator='{Zend_Json::encode($SPECIAL_VALIDATOR)}'{/if} data-selected-value='{$FIELD_MODEL_LIST['business_line']->get('fieldvalue')}'>
								{else}
									<select class="chzn-select {if $OCCUPY_COMPLETE_WIDTH} row-fluid {/if}" name="{$FIELD_MODEL_LIST['business_line']->getFieldName()}" data-validation-engine="validate[{if $FIELD_MODEL_LIST['business_line']->isMandatory() eq true} required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" data-fieldinfo='{$FIELD_INFO|escape}' {if !empty($SPECIAL_VALIDATOR)}data-validator='{Zend_Json::encode($SPECIAL_VALIDATOR)}'{/if} data-selected-value='{$FIELD_MODEL_LIST['business_line']->get('fieldvalue')}'>
								{/if}
									{if $FIELD_MODEL_LIST['business_line']->isEmptyPicklistOptionAllowed()}<option value="">{vtranslate('LBL_SELECT_OPTION','Vtiger')}</option>{/if}
									{foreach item=PICKLIST_VALUE key=PICKLIST_NAME from=$PICKLIST_VALUES}
										<option value="{Vtiger_Util_Helper::toSafeHTML($PICKLIST_NAME)}" {if trim(decode_html($FIELD_MODEL_LIST['business_line']->get('fieldvalue'))) eq trim($PICKLIST_NAME)} selected {/if}>{$PICKLIST_VALUE}</option>
									{/foreach}
								</select>
								<input type="hidden" class="fieldname" value="business_line" data-prev-value="{$FIELD_MODEL_LIST['business_line']->get('fieldvalue')}">
							</span>
						</td>
						<td>
							&nbsp;
						</td>
						<td>
							&nbsp;
						</td>
					</tr>
				{/if}
			</tbody>
		</table>
		<br>

		{if $MODULE_NAME eq 'Opportunities' && $BLOCK_LABEL_KEY eq 'LBL_POTENTIALS_DATES'}

			{*{include file=vtemplate_path('participatingAgentsDetail.tpl', 'ParticipatingAgents')}*}
			{if $AUTO_SPOT_QUOTE_MODULE && AUTO_QUOTES && $AUTO_SPOT_QUOTE_MODULE->isActive()}
			<table name='AddressSegmentsTable' class='table table-bordered blockContainer showInlineTable'>
				<thead>
				<tr>
					<th class='blockHeader' colspan='9'>{vtranslate('AutoSpotQuote', 'AutoSpotQuote')}</th>
				</tr>
				</thead>
				<tbody>
					<tr style="width:100%" class="fieldLabel">
						<td style="text-align:center;margin:auto;width:25%;"><b>Make</b></td>
						<td style="text-align:center;margin:auto;width:25%;"><b>Model</b></td>
						<td style="text-align:center;margin:auto;width:25%;"><b>Year</b></td>
						<td style="text-align:center;margin:auto;width:25%;"><b>Registration #</b></td>
					</tr>
					{foreach item=AUTO_QUOTES_VALUE key=AUTO_QUOTES_number from=$AUTO_QUOTES}
						<tr style="width:100%">
						<td style="text-align:center;margin:auto;width:25%;"><b>{$AUTO_QUOTES_VALUE.auto_make}</b></td>
						<td style="text-align:center;margin:auto;width:25%;"><b>{$AUTO_QUOTES_VALUE.auto_model}</b></td>
						<td style="text-align:center;margin:auto;width:25%;"><b>{$AUTO_QUOTES_VALUE.auto_year}</b></td>
						<td style="text-align:center;margin:auto;width:25%;"><b>{if $AUTO_QUOTES_VALUE.registration_number neq ''}{$AUTO_QUOTES_VALUE.registration_number}{else}Not registered{/if}</b></td>
					</tr>
					{/foreach}
				</tbody>
			</table>
			<br />
			{/if}
            {if $BLOCK_LABEL_KEY eq 'LBL_POTENTIALS_DATES' && getenv('INSTANCE_NAME') != 'graebel'}
                {include file=vtemplate_path('participatingAgentsDetail.tpl', 'ParticipatingAgents')}
                {include file=vtemplate_path('GuestDetailBlocks.tpl', $MODULE_NAME)}
            {/if}
			{* {include file=vtemplate_path('extraStopsDetail.tpl', 'ExtraStops')} *}
            {if getenv('IGC_MOVEHQ') eq 1 && getenv('INSTANCE_NAME') != 'graebel'}
                {include file=vtemplate_path('ReferralBlockDetail.tpl', 'Opportunities') BLOCK_LABEL_KEY='LBL_OPPORTUNITIES_REFERRAL' FIELD_MODEL_LIST= $RECORD_STRUCTURE['LBL_OPPORTUNITIES_REFERRAL'] }
            {/if}
		{/if}
		{include file=vtemplate_path('SequencedGuestDetailBlocks.tpl', $MODULE) BLOCK_LABEL=$BLOCK_LABEL_KEY}
	{/foreach}
	{if $MODULE_NAME eq "Documents"}
		<div>
			<iframe width="100%" height="600px" id="DocumentRenderer" src=''></iframe>
		</div>
	{/if}
	{if $MODULE_NAME eq "Quotes"}
		<!-- BEGIN RateEstimateDetail -->
		{include file='layouts/vlayout/modules/Quotes/RateEstimateDetail.tpl'}
	{/if}
{/strip}
