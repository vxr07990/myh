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
	<link rel="stylesheet" href="libraries/jquery/colorbox/example1/colorbox.css" />
{strip}
	{foreach key=BLOCK_LABEL item=FIELD_MODEL_LIST from=$RECORD_STRUCTURE}
		{if $BLOCK_LABEL eq "LBL_ADDRESS_INFORMATION" and $IS_ACTIVE_ADDRESSLIST == true}
			{include file=vtemplate_path('AddressListDetail.tpl', 'AddressList')}
			{continue}
		{/if}
		{if $BLOCK_LABEL eq "LBL_AUTO_RATE_INFORMATION"}
			{include file=vtemplate_path('AutoRateTableDetail.tpl', 'Contracts')}
			{continue}
		{/if}

		{if $BLOCK_LABEL eq "LBL_QUOTES_INTERSTATE_SERVICECHARGES" && getenv('INSTANCE_NAME') == 'graebel'}{include file=vtemplate_path('InterstateServiceChargesDetail.tpl',$MODULE)}{/if}

		{if ($BLOCK_LABEL eq "LBL_QUOTES_SITDETAILS" or $BLOCK_LABEL eq "LBL_QUOTES_ACCESSORIALDETAILS" or $BLOCK_LABEL eq "LBL_ESTIMATES_APPLIANCE" or $BLOCK_LABEL eq "LBL_QUOTES_LONGCARRY" or $BLOCK_LABEL eq "LBL_QUOTES_STAIR" or $BLOCK_LABEL eq "LBL_QUOTES_ELEVATOR" or $BLOCK_LABEL eq "LBL_QUOTES_SITDETAILS2" or $BLOCK_LABEL eq 'LBL_SPACE_RESERVATION')}{continue}{/if}
		{if $BLOCK_LABEL eq 'LBL_ESTIMATES_DATES' AND  getenv('INSTANCE_NAME') neq 'sirva'}{continue}{/if}
		{if $BLOCK_LABEL eq 'LBL_POTENTIALS_DATES' AND  getenv('INSTANCE_NAME') neq 'sirva'}{continue}{/if}

		{assign var=BLOCK value=$BLOCK_LIST[$BLOCK_LABEL]}
		{if $BLOCK_LABEL eq "LBL_QUOTES_INTERSTATE_SERVICECHARGES"}{include file=vtemplate_path('InterstateServiceChargesDetail.tpl',$MODULE)}{/if}
		{if $BLOCK eq null or $FIELD_MODEL_LIST|@count lte 0 and $BLOCK_LABEL neq "LBL_QUOTES_LOCALMOVEDETAILS"}{continue}{/if}
		{assign var=IS_HIDDEN value=$BLOCK->isHidden()}
		{assign var=WIDTHTYPE value=$USER_MODEL->get('rowheight')}
		<input type=hidden name="timeFormatOptions" data-value='{$DAY_STARTS}' />
		{if $BLOCK_LABEL neq 'LBL_ESTIMATES_EXTRASTOPS'}
		<table class="table table-bordered equalSplit detailview-table {if $BLOCK_LABEL eq "LBL_QUOTES_TPGPRICELOCK" OR $BLOCK_LABEL eq "LBL_QUOTES_VALUATION"} hide{/if} {$BLOCK_LABEL} {if $BLOCK->get('hideblock') eq true}hide{/if}" name="{$BLOCK_LABEL}">
			<thead>
				<tr>
					<th class="blockHeader" colspan="{if $BLOCK_LABEL eq 'LBL_ESTIMATES_DATES'}6{else}4{/if}" {if $MODULE_NAME eq "Estimates"} class="hide" {/if}>
							<img class="cursorPointer alignMiddle blockToggle {if !($IS_HIDDEN)} hide {/if} "  src="{vimage_path('arrowRight.png')}" data-mode="hide" data-id={$BLOCK_LIST[$BLOCK_LABEL]->get('id')}>
							<img class="cursorPointer alignMiddle blockToggle {if ($IS_HIDDEN)} hide {/if}"  src="{vimage_path('arrowDown.png')}" data-mode="show" data-id={$BLOCK_LIST[$BLOCK_LABEL]->get('id')}>
							&nbsp;&nbsp;{vtranslate({$BLOCK_LABEL},{$MODULE_NAME})}
					</th>
				</tr>
			</thead>
	{/if}
	 		<tbody {if $IS_HIDDEN} class="hide" {/if}>

			{assign var=COUNTER value=0}
			<tr>
				{foreach item=FIELD_MODEL key=FIELD_NAME from=$FIELD_MODEL_LIST}
				{if $FIELD_NAME eq 'cubesheet' && $FIELD_MODEL->get('fieldvalue') eq ''}{continue}{/if}
					{if $FIELD_NAME eq 'small_shipment'}
						{assign var=SMALL_SHIP_TOGGLE value=$FIELD_MODEL->get('fieldvalue')}
					{elseif $FIELD_NAME eq 'priority_shipping'}
						{assign var=PRIORITY_SHIP_TOGGLE value=$FIELD_MODEL->get('fieldvalue')}
					{/if}
					{if $FIELD_NAME eq 'business_line_est'}
						{assign var=BUSINESS_LINE value=$FIELD_MODEL->get('fieldvalue')}
						<input type="hidden" name="businessLine" value="{$FIELD_MODEL->get('fieldvalue')}" />
						<td class="hide"/>
					{/if}
					{* OT14156 hide counties on detail view except for local us *}
					{if $BUSINESS_LINE && $BUSINESS_LINE eq 'Local Move' || ($BUSINESS_LINE == 'Intrastate_Move' && !$INTRA_INTERSTATE)}
						{if (
						$FIELD_NAME eq 'local_carrier' ||
						$FIELD_NAME eq 'estimates_origin_county' ||
						$FIELD_NAME eq 'estimates_destination_county'
						)}
							{*just skip them... don't do the js thing because they can't change move_type.*}
							{continue}
						{/if}
					{/if}
					{* I added the effective_tariff field to the INTERSTATE block so it would build in pseudo_save.
						 So I have to skip it here, in order to show the correct select box.
						 Maybe I should have added the field to a block that was always hidden?
				 	*}
					{*if $FIELD_NAME eq 'business_line_est'}<input type='hidden' name='businessLine' value='{$FIELD_MODEL->get('fieldvalue')}' />{/if*}
					{if
						($FIELD_NAME eq 'increased_base') ||
						($FIELD_NAME eq 'declared_value') ||
						($FIELD_NAME eq 'valuation_flat_charge') ||
						($FIELD_NAME eq 'effective_tariff') ||
						($FIELD_NAME eq 'free_valuation_type')
						}
							{continue}
						{/if}
					{if !$FIELD_MODEL->isViewableInDetailView() || ($FIELD_NAME eq 'business_line_est' && 'move_type'|array_key_exists:$FIELD_MODEL_LIST)}
						{continue}
					{/if}
					{if $FIELD_NAME eq "lead_type"}
						{continue}
					{/if}
			{if $FIELD_NAME eq "apply_full_pack_rate_override" && $COUNTER eq 1}
					{assign var=COUNTER value=$COUNTER+1}
					<td class="fieldLabel {$WIDTHTYPE}" style="width:20%"></td><td class="{$WIDTHTYPE}" style="width:30%"></td>
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
								<td class="{$WIDTHTYPE}" style="width:20%"></td><td class="{$WIDTHTYPE}" style="width:30%"></td></tr><tr>
								{assign var=COUNTER value=0}
							{/if}
						{/if}
						{if  $BLOCK_LABEL eq 'LBL_ESTIMATES_DATES'}
							{if $COUNTER eq '3'}
								</tr><tr>
								{assign var=COUNTER value=1}
							{else}
								{assign var=COUNTER value=$COUNTER+1}
							{/if}
						{else}
							{if $COUNTER eq 2}
								</tr><tr{if !$PRIORITY_SHIP_TOGGLE && ($FIELD_NAME eq "pshipping_booker_commission" || $FIELD_NAME eq "pshipping_origin_miles" || $FIELD_NAME eq "pshipping_destination_miles")} class="hide"{/if}>
								{assign var=COUNTER value=1}
							{else}
								{if $FIELD_NAME neq "pricing_color_lock" && $FIELD_NAME neq "smf_type"}
									{assign var=COUNTER value=$COUNTER+1}
								{/if}
							{/if}
							{if $FIELD_NAME eq 'priority_shipping' && $COUNTER eq 2}
								{assign var=COUNTER value=1}
								<td class="{$WIDTHTYPE}" style="width:20%"></td><td class="{$WIDTHTYPE}" style="width:30%"></td></tr><tr>
							{/if}
						{/if}
						 {if $BLOCK_LABEL eq 'LBL_QUOTES_TPGPRICELOCK'}
							 {if $FIELD_MODEL_LIST|@end eq true and $FIELD_MODEL_LIST|@count neq 1 and $COUNTER eq 1 and $FIELD_NAME eq 'smf_type'}
								 <td class="fieldLabel {$WIDTHTYPE}" style="width:20%"></td><td class="fieldValue {$WIDTHTYPE}" style="width:30%"></td>
								 {assign var=COUNTER value=2}
							 {/if}
						 {/if}
				 		<td class="fieldLabel {$WIDTHTYPE}{if $FIELD_NAME eq "pricing_color_lock" || $FIELD_NAME eq "smf_type" || $FIELD_NAME eq "apply_full_pack_rate_override" || $FIELD_NAME eq "full_pack_rate_override" || ($FIELD_NAME eq "lead_type" && $COUNTER eq 1) || $FIELD_NAME eq "grr" || $FIELD_NAME eq "grr_override" || $FIELD_NAME eq "grr_override_amount" || $FIELD_NAME eq "grr_estimate" || $FIELD_NAME eq "grr_cp" || $FIELD_NAME eq "free_valuation_type" || $FIELD_NAME eq "rate_per_100" || $FIELD_NAME eq "valuation_flat_charge" || $FIELD_NAME eq "declared_value" || $FIELD_NAME eq "free_valuation_limit" || $FIELD_NAME eq "min_declared_value_mult" || $FIELD_NAME eq "apply_free_fvp" || $FIELD_NAME eq "increased_base"} hide{/if}" {if $BLOCK_LABEL eq 'LBL_ESTIMATES_DATES'}style='width:14%'{/if}>
					 		<label class="muted pull-right marginRight10px{if (!$SMALL_SHIP_TOGGLE && ($FIELD_NAME eq "small_shipment_miles" || $FIELD_NAME eq "small_shipment_ot")) || (!$PRIORITY_SHIP_TOGGLE && ($FIELD_NAME eq "pshipping_booker_commission" || $FIELD_NAME eq "pshipping_origin_miles" || $FIELD_NAME eq "pshipping_destination_miles"))} hide{/if}">
						 		{vtranslate({$FIELD_MODEL->get('label')},{$MODULE_NAME})}
						 		{if ($FIELD_MODEL->get('uitype') eq '72') && ($FIELD_MODEL->getName() eq 'unit_price')}
									({$BASE_CURRENCY_SYMBOL})
								{/if}
					 		</label>
				 		</td>
				 		<td id="{$MODULE_NAME}_detailView_fieldValue_{$FIELD_MODEL->getName()}" {if $BLOCK_LABEL eq 'LBL_ESTIMATES_DATES'}style='width:19%'{/if} class="fieldValue {$WIDTHTYPE} value_{$FIELD_MODEL->get('label')}{if $FIELD_NAME eq "pricing_color_lock" || $FIELD_NAME eq "smf_type" || $FIELD_NAME eq "apply_full_pack_rate_override" || $FIELD_NAME eq "full_pack_rate_override" || $FIELD_NAME eq "lead_type" || $FIELD_NAME eq "grr" || $FIELD_NAME eq "grr_override" || $FIELD_NAME eq "grr_override_amount" || $FIELD_NAME eq "grr_estimate" || $FIELD_NAME eq "grr_cp" || $FIELD_NAME eq "free_valuation_type" || $FIELD_NAME eq "rate_per_100" || $FIELD_NAME eq "valuation_flat_charge" || $FIELD_NAME eq "declared_value" || $FIELD_NAME eq "free_valuation_limit" || $FIELD_NAME eq "min_declared_value_mult" || $FIELD_NAME eq "apply_free_fvp" || $FIELD_NAME eq "increased_base"} hide{/if}" {if $FIELD_MODEL->get('uitype') eq '19'} colspan="3" {assign var=COUNTER value=$COUNTER+1} {/if} {if $FIELD_MODEL->get('uitype') eq '20'} colspan="3"{/if}>
							{if $FIELD_NAME eq 'potential_id'}
								<span class="value" data-field-type="{$FIELD_MODEL->getFieldDataType()}" {if $FIELD_MODEL->get('uitype') eq '19' or $FIELD_MODEL->get('uitype') eq '20' or $FIELD_MODEL->get('uitype') eq '21'} style="white-space:normal;" {/if}>
									{$POTENTIAL_LINK}
							 	</span>
							{elseif $FIELD_NAME eq 'orders_id'}
								{if !$ORDERS_ENABLED}{continue}{/if}
								<span class="value" data-field-type="{$FIELD_MODEL->getFieldDataType()}" {if $FIELD_MODEL->get('uitype') eq '19' or $FIELD_MODEL->get('uitype') eq '20' or $FIELD_MODEL->get('uitype') eq '21'} style="white-space:normal;" {/if}>
									{$ORDER_LINK}
								</span>

							{elseif $FIELD_NAME eq 'contact_id' && getenv('INSTANCE_NAME') != 'sirva' }
								<span class="value

								" data-field-type="{$FIELD_MODEL->getFieldDataType()}" {if $FIELD_MODEL->get('uitype') eq '19' or $FIELD_MODEL->get('uitype') eq '20' or $FIELD_MODEL->get('uitype') eq '21'} style="white-space:normal;" {/if}>
									{$CONTACT_NAME}
								</span>

					{elseif $FIELD_NAME eq 'percent_smf'}
						<span class="value" data-field-type="{$FIELD_MODEL->getFieldDataType()}" {if $FIELD_MODEL->get('uitype') eq '19' or $FIELD_MODEL->get('uitype') eq '20' or $FIELD_MODEL->get('uitype') eq '21'} style="white-space:normal;" {/if}>
							{assign var=PERCENT_SMF value=$FIELD_MODEL->get('fieldvalue')}
							{number_format(floatval($PERCENT_SMF), 2, '.', '')}
								</span>
							{else}
								<span class="value{if (!$SMALL_SHIP_TOGGLE && ($FIELD_NAME eq "small_shipment_miles" || $FIELD_NAME eq "small_shipment_ot")) || (!$PRIORITY_SHIP_TOGGLE && ($FIELD_NAME eq "pshipping_booker_commission" || $FIELD_NAME eq "pshipping_origin_miles" || $FIELD_NAME eq "pshipping_destination_miles"))} hide{/if}" data-field-type="{$FIELD_MODEL->getFieldDataType()}" {if $FIELD_MODEL->get('uitype') eq '19' or $FIELD_MODEL->get('uitype') eq '20' or $FIELD_MODEL->get('uitype') eq '21'} style="white-space:normal;" {/if}>
									{include file=vtemplate_path($FIELD_MODEL->getUITypeModel()->getDetailViewTemplateName(),$MODULE_NAME) FIELD_MODEL=$FIELD_MODEL USER_MODEL=$USER_MODEL MODULE=$MODULE_NAME RECORD=$RECORD}
									{if $FIELD_NAME eq 'assigned_user_id'}<input name="assignedToId" type="hidden" value="{$FIELD_MODEL->get('fieldvalue')}"> {/if}
								</span>
							{/if}
							{if $IS_AJAX_ENABLED && $FIELD_MODEL->isEditable() eq 'true' && ($FIELD_MODEL->getFieldDataType()!=Vtiger_Field_Model::REFERENCE_TYPE) && $FIELD_MODEL->isAjaxEditable() eq 'true'}
								<span class="hide {*OLD SECURITIES{if $EXTRA_PERMISSIONS[0] === TRUE}*}edit{*{/if}*}">
									{include file=vtemplate_path($FIELD_MODEL->getUITypeModel()->getTemplateName(),$MODULE_NAME) FIELD_MODEL=$FIELD_MODEL USER_MODEL=$USER_MODEL MODULE=$MODULE_NAME}
									{if $FIELD_MODEL->getFieldDataType() eq 'multipicklist'}
										<input type="hidden" class="fieldname" value='{$FIELD_MODEL->get('name')}[]' data-prev-value='{$FIELD_MODEL->getDisplayValue($FIELD_MODEL->get('fieldvalue'))}' />
									{else}
										<input type="hidden" class="fieldname" value='{$FIELD_MODEL->get('name')}' data-prev-value='{Vtiger_Util_Helper::toSafeHTML($FIELD_MODEL->getDisplayValue($FIELD_MODEL->get('fieldvalue')))}' />
									{/if}
								 </span>
							 {/if}
						</td>
			 		{/if}
			 		{if $FIELD_NAME eq 'validtill' && getenv('INSTANCE_NAME') == 'sirva'}{assign var=COUNTER value=0}{/if}
			 		{if $FIELD_NAME eq 'irr_charge' && getenv('INSTANCE_NAME') == 'sirva'}{assign var=COUNTER value=1}{/if}

			{if $FIELD_MODEL_LIST|@count eq 1 and $FIELD_MODEL->get('uitype') neq "19" and $FIELD_MODEL->get('uitype') neq "20" and $FIELD_MODEL->get('uitype') neq "30" and $FIELD_MODEL->get('name') neq "recurringtype" and $FIELD_MODEL->get('uitype') neq "69" and $FIELD_MODEL->get('uitype') neq "105"}
				<td class="fieldLabel {$WIDTHTYPE}"></td><td class="{$WIDTHTYPE}"></td>
			{/if}
			{if $FIELD_NAME eq "full_pack_rate_override"}
				<td class="fieldLabel {$WIDTHTYPE} hide fullPackOverrideFiller"></td><td class="{$WIDTHTYPE} hide fullPackOverrideFiller"></td>
			{/if}
		{/foreach}
		{if $BLOCK_LABEL eq 'LBL_QUOTES_INTERSTATEMOVEDETAILS'}
			{if $FIELD_MODEL_LIST|@end eq true and $FIELD_MODEL_LIST|@count neq 1}
				{if $COUNTER eq 2}</tr><tr>{assign var=COUNTER value=0}{/if}
				<td class="fieldLabel {$WIDTHTYPE}" id="{$MODULE}_detailView_fieldLabel_effective_tariff"><label class="muted pull-right marginRight10px">{vtranslate('LBL_QUOTES_EFFECTIVETARIFF', $MODULE)}</label></td>
				<td class="fieldValue {$WIDTHTYPE}" id="{$MODULE}_detailView_fieldValue_effective_tariff">
					<span class="value" data-field-type="picklist">{$EFFECTIVE_TARIFF_NAME}</span>
					<input id="customjs" type="hidden" value="{$CUSTOM_JS}">
                    <input id="tariffType" type="hidden" value="{$CUSTOM_TARIFF_TYPE}">
				</td>
				{assign var=COUNTER value=$COUNTER+1}
			{/if}
		{/if}
		{if $BLOCK_LABEL eq 'LBL_ESTIMATES_DATES'}
			{if $COUNTER eq 1}
				<td style="width:14%" class="fieldLabel {$WIDTHTYPE}"></td><td style="width:19%" class="{$WIDTHTYPE}"></td><td style="width:14%" class="fieldLabel {$WIDTHTYPE}"></td><td style="width:19%" class="{$WIDTHTYPE}"></td>
				{assign var=COUNTER value=$COUNTER+1}
			{elseif $COUNTER eq 2}
				<td style="width:14%" class="fieldLabel {$WIDTHTYPE}"></td><td style="width:19%" class="{$WIDTHTYPE}"></td>
			{/if}
		{/if}
		{if $BLOCK_LABEL eq 'LBL_ESTIMATES_EXTRASTOPS'}
			{if $STOPS_ROWS|@count gt 0}
				<table id='extra_stops' class="table table-bordered equalSplit detailview-table hide" style="width: 100;">
					<thead>
						<tr>
							<th class="blockHeader" colspan="4">
									<img class="cursorPointer alignMiddle blockToggle {if !($IS_HIDDEN)} hide {/if} "  src="{vimage_path('arrowRight.png')}" data-mode="hide" data-id={$BLOCK_LIST[$BLOCK_LABEL]->get('id')}>
									<img class="cursorPointer alignMiddle blockToggle {if ($IS_HIDDEN)} hide {/if}"  src="{vimage_path('arrowDown.png')}" data-mode="show" data-id={$BLOCK_LIST[$BLOCK_LABEL]->get('id')}>
									&nbsp;&nbsp;{vtranslate({$BLOCK_LABEL},{$MODULE_NAME})}
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
													<label class="muted pull-right marginRight10px"><span class="redColor">*</span>{vtranslate('LBL_ESTIMATES_STOPDESCRIPTION', $MODULE_NAME)}</label>
												</td>
												<td class="fieldValue medium" id="Opportunities_detailView_fieldLabel_stop_description_{$STOP_INDEX+1}">
													<div class="row-fluid">
														<span class="value" data-field-type="string">
															{$CURRENT_STOP['stop_description']}
														</span>
													</div>
												</td>
												<td class="fieldLabel medium" colspan="1" style="text-align:center;margin:auto">
													<label class="muted pull-right marginRight10px"><span class="redColor">*</span>{vtranslate('LBL_ESTIMATES_STOPSEQUENCE', $MODULE_NAME)}</label>
												</td>
												<td class="fieldValue medium" id="Opportunities_detailView_fieldLabel_stop_sequence_{$STOP_INDEX+1}">
													<div class="row-fluid">
														<span class="value" data-field-type="string">
															{$CURRENT_STOP['stop_sequence']}
														</span>
													</div>
												</td>
											</tr>
											<tr style="width:100%" colspan="4">
												<td class="fieldLabel medium" colspan="1" style="text-align:center;margin:auto">
													<label class="muted pull-right marginRight10px">{vtranslate('LBL_ESTIMATES_STOPWEIGHT', $MODULE_NAME)}</label>
												</td>
												<td class="fieldValue medium" id="Opportunities_detailView_fieldLabel_stop_weight_{$STOP_INDEX+1}">
													<div class="row-fluid">
														<span class="value" data-field-type="string">
															{$CURRENT_STOP['stop_weight']}
														</span>
													</div>
												</td>
												<td class="fieldLabel medium" colspan="1" style="text-align:center;margin:auto">
													<label class="muted pull-right marginRight10px">{vtranslate('LBL_ESTIMATES_STOPISPRIMARY', $MODULE_NAME)}</label>
												</td>
												<td class="fieldValue medium" id="Opportunities_detailView_fieldLabel_stop_isprimary_{$STOP_INDEX+1}">
													<div class="row-fluid">
														<span class="value" data-field-type="boolean">
															{if $CURRENT_STOP['stop_isprimary'] eq '1' || $CURRENT_STOP['stop_isprimary'] eq 'yes' || $CURRENT_STOP['stop_isprimary'] eq 'on'}Yes{else}No{/if}
														</span>
													</div>
												</td>
											</tr>
											<tr style="width:100%" colspan="4">
												<td class="fieldLabel medium" colspan="1" style="text-align:center;margin:auto">
													<label class="muted pull-right marginRight10px">{vtranslate('LBL_ESTIMATES_STOPADDRESS1', $MODULE_NAME)}</label>
												</td>
												<td class="fieldValue medium" id="Opportunities_detailView_fieldLabel_stop_address1_{$STOP_INDEX+1}">
													<div class="row-fluid">
														<span class="value" data-field-type="string">
															{$CURRENT_STOP['stop_address1']}
														</span>
													</div>
												</td>
												<td class="fieldLabel medium" colspan="1" style="text-align:center;margin:auto">
													<label class="muted pull-right marginRight10px">{vtranslate('LBL_ESTIMATES_STOPADDRESS2', $MODULE_NAME)}</label>
												</td>
												<td class="fieldValue medium" id="Opportunities_detailView_fieldLabel_stop_address2_{$STOP_INDEX+1}">
													<div class="row-fluid">
														<span class="value" data-field-type="string">
															{$CURRENT_STOP['stop_address2']}
														</span>
													</div>
												</td>
											</tr>
											<tr style="width:100%" colspan="4">
												<td class="fieldLabel medium" colspan="1" style="text-align:center;margin:auto">
													<label class="muted pull-right marginRight10px">{vtranslate('LBL_ESTIMATES_STOPPHONE1', $MODULE_NAME)}<label>
												</td>
												<td class="fieldValue medium" id="Opportunities_detailView_fieldLabel_stop_phone1_{$STOP_INDEX+1}">
													<div class="row-fluid">
														<span class="value" data-field-type="string">
															{$CURRENT_STOP['stop_phone1']}
														</span>
													</div>
												</td>
												<td class="fieldLabel medium" colspan="1" style="text-align:center;margin:auto">
													<label class="muted pull-right marginRight10px">{vtranslate('LBL_ESTIMATES_STOPPHONE2', $MODULE_NAME)}</label>
												</td>
												<td class="fieldValue medium" id="Opportunities_detailView_fieldLabel_stop_phone1_{$STOP_INDEX+1}">
													<div class="row-fluid">
														<span class="value" data-field-type="string">
															{$CURRENT_STOP['stop_phone2']}
														</span>
													</div>
												</td>
											</tr>
											<tr style="width:100%" colspan="4">
												<td class="fieldLabel medium" colspan="1" style="text-align:center;margin:auto">
													<label class="muted pull-right marginRight10px">{vtranslate('LBL_ESTIMATES_STOPPHONE1TYPE', $MODULE_NAME)}</label>
												</td>
												<td class="fieldValue medium" id="Opportunities_detailView_fieldLabel_stop_phonetype1_{$STOP_INDEX+1}">
													<span class="value" data-field-type="picklist">
															{$CURRENT_STOP['stop_phonetype1']}
														</span>
												</td>
												<td class="fieldLabel medium" colspan="1" style="text-align:center;margin:auto">
													<label class="muted pull-right marginRight10px">{vtranslate('LBL_ESTIMATES_STOPPHONE2TYPE', $MODULE_NAME)}</label>
												</td>
												<td class="fieldValue medium" id="Opportunities_detailView_fieldLabel_stop_phonetype2_{$STOP_INDEX+1}">
													<span class="value" data-field-type="picklist">
														{$CURRENT_STOP['stop_phonetype2']}
													</span>
												</td>
											</tr>
											<tr style="width:100%" colspan="4">
												<td class="fieldLabel medium" colspan="1" style="text-align:center;margin:auto">
													<label class="muted pull-right marginRight10px">{vtranslate('LBL_ESTIMATES_STOPCITY', $MODULE_NAME)}</label>
												</td>
												<td class="fieldValue medium" id="Opportunities_detailView_fieldLabel_stop_city_{$STOP_INDEX+1}">
													<div class="row-fluid">
														<span class="value" data-field-type="string">
															{$CURRENT_STOP['stop_city']}
														</span>
													</div>
												</td>
												<td class="fieldLabel medium" colspan="1" style="text-align:center;margin:auto">
													<label class="muted pull-right marginRight10px">{vtranslate('LBL_ESTIMATES_STOPSTATE', $MODULE_NAME)}</label>
												</td>
												<td class="fieldValue medium" id="Opportunities_detailView_fieldLabel_stop_state_{$STOP_INDEX+1}">
													<div class="row-fluid">
														<span class="value" data-field-type="string">
															{$CURRENT_STOP['stop_state']}
														</span>
													</div>
												</td>
											</tr>
											<tr style="width:100%" colspan="4">
												<td class="fieldLabel medium" colspan="1" style="text-align:center;margin:auto">
													<label class="muted pull-right marginRight10px">{vtranslate('LBL_ESTIMATES_STOPZIP', $MODULE_NAME)}</label>
												</td>
												<td class="fieldValue medium" id="Opportunities_detailView_fieldLabel_stop_zip_{$STOP_INDEX+1}">
													<div class="row-fluid">
														<span class="value" data-field-type="string">
															{$CURRENT_STOP['stop_zip']}
														</span>
													</div>
												</td>
												<td class="fieldLabel medium" colspan="1" style="text-align:center;margin:auto">
													<label class="muted pull-right marginRight10px">{vtranslate('LBL_ESTIMATES_STOPCOUNTRY', $MODULE_NAME)}</label>
												</td>
												<td class="fieldValue medium" id="Opportunities_detailView_fieldLabel_stop_country_{$STOP_INDEX+1}">
													<div class="row-fluid">
														<span class="value" data-field-type="string">
															{$CURRENT_STOP['stop_country']}
														</span>
													</div>
												</td>
											</tr>
											<tr style="width:100%" colspan="4">
												<td class="fieldLabel medium" colspan="1" style="text-align:center;margin:auto">
													<label class="muted pull-right marginRight10px">{vtranslate('LBL_ESTIMATES_STOPDATE', $MODULE_NAME)}</label>
												</td>
												<td class="fieldValue medium" id="Opportunities_detailView_fieldLabel_stop_date_{$STOP_INDEX+1}">
													<span class="value" data-field-type="string">
															{$CURRENT_STOP['stop_date']}
													</span>
												</td>
											<td class="fieldLabel medium" colspan="1" style="text-align:center;margin:auto">
												<label class="muted pull-right marginRight10px">{vtranslate('LBL_ESTIMATES_STOPCONTACT', $MODULE_NAME)}</label>
											</td>
											<td class="fieldValue medium">
												<span class="value" data-field-type="reference">
													{$CURRENT_STOP['stop_contact_link']}
												</span>
											</td>
										</tr>
										<tr>
											<td class="fieldLabel medium">
												<label class="muted pull-right marginRight10px">{vtranslate('LBL_ESTIMATES_STOPTYPE', $MODULE_NAME)}</label>
											</td>
											<td class="fieldValue medium" id="Opportunities_detailView_fieldLabel_stop_type_{$STOP_INDEX+1}">
												<span class="value" data-field-type="picklist">
													{$CURRENT_STOP['stop_type']}
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
			{/if}
		{/if}
		{if $BLOCK_LABEL eq 'LBL_QUOTES_LOCALMOVEDETAILS'}
					{if $COUNTER eq 1}
						{assign var=COUNTER value=$COUNTER+1}
						<td class="fieldLabel {$WIDTHTYPE}" style="width:20%"></td><td class="{$WIDTHTYPE}" style="width:30%"></td>
					{/if}
			<tr>
				<td class='fieldLabel {$WIDTHTYPE}'><label class='muted pull-right marginRight10px'>Weight</label></td>
				<td class='fieldValue {$WIDTHTYPE} value_LBL_QUOTES_LOCAL_WEIGHT'>{$ESTIMATE_CUBESHEET_WEIGHT}</td>
				<td class='fieldLabel {$WIDTHTYPE}'><label class='muted pull-right marginRight10px'>Tariff</label></td>
				<td class='fieldValue local {$WIDTHTYPE}'>
					<span class="value" data-field-type="picklist">{if !empty($EFFECTIVE_TARIFF_NAME)}{$EFFECTIVE_TARIFF_NAME}{else}Select an Option{/if}</span>
					<span class="hide {*OLD SECURITIES{if $EXTRA_PERMISSIONS[0] === TRUE}*}edit{*{/if}*}">
						<select class='chzn-select' name='local_tariff'>
							<option value='0' selected>Select an Option</option>
							{if getenv('INSTANCE_NAME') eq 'sirva'}{assign var=COMBINE_TARIFF value=$INTRASTATE_TARIFFS}{else}{assign var=COMBINE_TARIFF value=$LOCAL_TARIFFS|array_merge:$INTRASTATE_TARIFFS}{/if}
							{*foreach item=TARIFF key=INDEX from=$LOCAL_TARIFFS*}
							{foreach item=TARIFF key=INDEX from=$COMBINE_TARIFF}
								<option class="{if $TARIFF->intrastate eq true}intrastateTariff {if $TARIFF->intraInterstate}intraInterstate {/if}{elseif $TARIFF->local eq true}localTariff {/if}" value="{$TARIFF->get('id')}" {if $TARIFF->get('id') eq $EFFECTIVE_TARIFF}selected{/if}>{$TARIFF->get('tariff_name')}</option>
							{/foreach}
						</select>
						<input type="hidden" class="fieldname" value="local_tariff" data-prev-value="{if !empty($EFFECTIVE_TARIFF)}{$EFFECTIVE_TARIFF}{else}Select an Option{/if}" />
					</span>
				</td>
			</tr>
			<tr>
				<td class='fieldLabel'>&nbsp;</td>
				<td class='fieldValue'>&nbsp;</td>
				<td class='fieldLabel'>&nbsp;</td>
				{*OLD SECURITIES{if $EXTRA_PERMISSIONS[0] === TRUE}*}
					<td class='fieldValue'><button type="button" class="localRate">Get Rate Estimate</button></td>
				{*OLD SECURITIES{else}
					<td class='fieldValue'>&nbsp;</td>
				{/if}*}
			</tr>
			{*End the pre-existing table so we can make new ones in the correct place*}
			</tbody>
			</table>
			{if $BLOCK_LABEL neq 'LBL_ESTIMATES_EXTRASTOPS'}<br>{/if}
			<div id='localMoveTables'>
				{include file=vtemplate_path('LocalBlockDetail.tpl', $MODULE)}
			</div>
		{/if}
		{if $BLOCK_LABEL eq 'LBL_QUOTES_TPGPRICELOCK'}
			{assign var=COUNTER value=0}
		{/if}
		{* adding additional column for odd number of fields in a block *}
		{if $FIELD_MODEL_LIST|@end eq true and $FIELD_MODEL_LIST|@count neq 1 and $COUNTER eq 1}
			<td class="fieldLabel {$WIDTHTYPE}"></td><td class="{$WIDTHTYPE}"></td>
		{/if}
		</tr>
		{if $BLOCK_LABEL eq 'LBL_QUOTE_INFORMATION' && 'move_type'|array_key_exists:$FIELD_MODEL_LIST}
			<tr class="hide">
				<td class="fieldLabel">
					<label class="muted pull-right marginRight10px">{if $FIELD_MODEL_LIST['business_line_est']->isMandatory() eq true} <span class="redColor">*</span> {/if}{vtranslate($FIELD_MODEL_LIST['business_line_est']->get('label'), $MODULE)}</label>
				</td>
				<td class="fieldValue" id="{$MODULE}_detailView_fieldValue_business_line_est">
					<span class="value" data-field-type="picklist">{$FIELD_MODEL_LIST['business_line_est']->get('fieldvalue')}</span>
				</td>
				<td class="fieldLabel">
					&nbsp;
				</td>
				<td>
					&nbsp;
				</td>
			</tr>
		{/if}
		{if $BLOCK_LABEL eq "LBL_QUOTES_INTERSTATEMOVEDETAILS"}
			{*OLD SECURITIES {if $EXTRA_PERMISSIONS[1] != 'no_rates'}*}
				<tr>
                    <td class='fieldLabel'></td>
                    <td class='fieldValue'>
						{* we think this is for nobody.
                        {if getenv('INSTANCE_NAME') eq 'sirva'}&nbsp;
                        {else}
                            <button type='button' id='interstateRateQuick'>Quick Rate Estimate</button>
                        {/if}
                        *}
                    </td>
                    <td class='fieldLabel'></td>
                    <td class='fieldValue'>
						{if !$LOCK_RATING}
                        	<button type='button' class='interstateRateDetail'>{vtranslate('LBL_DETAILED_RATE_ESTIMATE', $MODULE)}</button>
						{/if}
                    </td>
                </tr>
			{*{/if}*}
		{/if}
		</tbody>
	</table>
	{if not $BLOCK->get('hideblock')}<br>{/if}<th class="blockHeader" colspan="4">

	{if $BLOCK_LABEL eq "LBL_QUOTES_INTERSTATEMOVEDETAILS"}
		{*OLD SECURITIES {if $EXTRA_PERMISSIONS[1] != 'no_rates'}*}
			<!-- BEGIN RateEstimateDetail -->
            <div id="inline_content">
                    {include file=vtemplate_path('RateEstimateDetail.tpl', $MODULE)}
            </div>
		{*{/if}*}
		{include file=vtemplate_path('vehicleLookupDetail.tpl', 'VehicleLookup')}
		{if getenv('INSTANCE_NAME') eq 'sirva'}{include file=vtemplate_path('extraStopsDetail.tpl', 'ExtraStops')}{/if}
		{include file=vtemplate_path('GuestDetailBlocks.tpl', $MODULE_NAME)}
	{/if}
	{if $BLOCK_LABEL eq 'LBL_ADDRESS_INFORMATION' && $ADDRESSSEGMENTS_MODULE_MODEL && $ADDRESSSEGMENTS_MODULE_MODEL->isActive()}
		{include file=vtemplate_path('AddressSegmentsDetail.tpl', 'AddressSegments')}
	{/if}
	{/foreach}
	<div id='reportContent' class=''>
	</div>
	{*{if $MODULE_NAME eq "Estimates"}
		{if $EXTRA_PERMISSIONS[1] != 'no_rates'}
			<!-- BEGIN RateEstimateDetail -->
			{include file='layouts/vlayout/modules/Estimates/RateEstimateDetail.tpl'}
		{/if}
	{/if}*}
{/strip}
