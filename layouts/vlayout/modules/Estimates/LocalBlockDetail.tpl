{strip}
{*OLD SECURITIES {assign var=EDITABLE value=$EXTRA_PERMISSIONS[0]}*}
{*OLD SECURITIES {assign var=SHOW_RATES value=($EXTRA_PERMISSIONS[1] != 'no_rates')}*}
{assign var=HAS_CONTENT value=(!$BLOCK_SUBLIST || $BLOCK_SUBLIST['LOCAL_MOVE_CONTENTS'])}
<div id="contentHolder_LOCAL_MOVE_CONTENTS" class="sectionContentHolder {$CONTENT_DIV_CLASS} {if !$ALWAYS_SHOW_CONTENT_DIV}hide{/if} {if !$HAS_CONTENT}inactive{/if}">
{if $HAS_CONTENT}
{assign var=EDITABLE value=FALSE}
{assign var=SHOW_RATES value=TRUE}
<div id='Tariff{$EFFECTIVE_TARIFF}' data-id="{$EFFECTIVE_TARIFF}" class="localMove">
	{assign var=EFFECTIVE_DATE_ID value=$TARIFF_DETAILS.effectiveDate}
	<input type="hidden" class="hide" name="EffectiveDateId" value="{$EFFECTIVE_DATE_ID}">
	{foreach item=SECTION key=SECTION_INDEX from=$TARIFF_DETAILS.sections}
		{*Start making new tables*}
		<table name="Section{$SECTION.id}" class="table table-bordered equalSplit detailview-table localMove">
			<thead>
				<tr>
					<th class="blockHeader" colspan="4">
					<img class="cursorPointer alignMiddle blockToggle hide" data-id="644" data-mode="hide" src="layouts/vlayout/skins/tightview/images/arrowRight.png">
					<img class="cursorPointer alignMiddle blockToggle" data-id="644" data-mode="show" src="layouts/vlayout/skins/tightview/images/arrowDown.png">
					{$SECTION.name}
					</th>
				</tr>
			</thead>
			<tbody>
				{if $SECTION.is_discountable eq 1}
					{foreach item=ITEM key=index from=$SECTION_DISCOUNTS}
						{if $ITEM[0] eq $SECTION.id}
							{assign var=SECTION_DISCOUNT value=$ITEM[1]}
						{/if}
					{/foreach}
					<tr>
						<td class='fieldLabel'>
							<label class="muted pull-right marginRight10px">Section Discount</label>
						</td>
						<td class="fieldValue section_discount">
							<span class="value">{$SECTION_DISCOUNT}</span>
							{if $EDITABLE === TRUE}
								<span class="hide edit">
									<div class="input-append">
										<input type="number" class="input-medium" min="-100" max="100" name="SectionDiscount{$SECTION.id}"
										value="{$SECTION_DISCOUNT}" step="any" /><span class="add-on">%</span>
									</div>
									<input class="fieldname" data-prev-value="{$SECTION_DISCOUNT}" type="hidden" value="SectionDiscount{$SECTION.id}">
								</span>
							{/if}
						</td>
						<td class='fieldLabel'>&nbsp;</td>
						<td class='fieldValue'>&nbsp;</td>
					</tr>
				{/if}
				{foreach item=SERVICE key=SERVICE_INDEX from=$SECTION.services}
					{assign var=RATE_TYPE value=$SERVICE->get(rate_type)}
					{assign var=SERVICE_DETAILS value=$SERVICE->getRecordDetails($RECORD_ID,'DETAIL')}
					{assign var=ID value=$SERVICE->get(id)}
					{if $RATE_TYPE eq "Hourly Avg Lb/Man/Hour"}
						{continue}
					{/if}
					<tr>
						<td style='min-width:400px; background-color:#E8E8E8;' class='fieldLabel' colspan="4">&nbsp;&nbsp;&nbsp;{$SERVICE->get(service_name)}</td>
					</tr>
					{if $RATE_TYPE eq "Base Plus Trans."}
						<tr>
							<td class='fieldLabel'>
								<label class="muted pull-right marginRight10px">Miles</label>
							</td>
							<td class='fieldValue localBasePlusField'>
							{assign var=FIELDNAME value='Miles'|cat:$ID}
							{if $SERVICE->get(is_required)}{$LOCALFIELDINFO.mandatory = true}{else}{$LOCALFIELDINFO.mandatory = false}{/if}
							{$LOCALFIELDINFO.name = $FIELDNAME}
							{$LOCALFIELDINFO.label = 'Miles'}
							{$LOCALFIELDINFO.type = 'integer'}
							{$INFO = Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($LOCALFIELDINFO))}
								<span class="value">{$SERVICE_DETAILS.mileage}</span>
								{if $EDITABLE === TRUE}
									<span class="hide edit">
										<input id="{$MODULE_NAME}_editView_fieldName_Miles{$ID}" name="Miles{$ID}" class="input-large nameField LocalBaseRateTrans" type="number" value="{$SERVICE_DETAILS.mileage}" data-validation-engine="validate[{if $SERVICE->get(is_required)}required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" data-fieldinfo="{$INFO}" />
										<input type="hidden" class="fieldname" value="Miles{$ID}" data-prev-value="{$SERVICE_DETAILS.mileage}">
									</span>
								{/if}
							</td>
							{if $SHOW_RATES}
								<td class='fieldLabel'>
									<label class="muted pull-right marginRight10px">Rate</label>
								</td>
								<td class='fieldValue localBasePlusField'>
								{assign var=FIELDNAME value='Rate'|cat:$ID}
								{if $SERVICE->get(is_required)}{$LOCALFIELDINFO.mandatory = true}{else}{$LOCALFIELDINFO.mandatory = false}{/if}
								{$LOCALFIELDINFO.name = $FIELDNAME}
								{$LOCALFIELDINFO.label = 'Rate'}
								{$LOCALFIELDINFO.type = 'currency'}
								{$INFO = Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($LOCALFIELDINFO))}
									<span class="value">{if $SERVICE_DETAILS.rate}{number_format($SERVICE_DETAILS.rate, 2, '.', '')}{/if}</span>
									{if $EDITABLE === TRUE}
										<span class="hide edit">
											<div class="row-fluid">
												<div class="input-prepend">
													<span class="span10">
														<span class="add-on">&#36;</span>
														<input name="Rate{$ID}" class="input-medium currencyField" type="text" value="{if $SERVICE_DETAILS.rate}{number_format($SERVICE_DETAILS.rate, 2, '.', '')}{/if}" data-decimal-seperator="." data-group-seperator="," data-number-of-decimal-places="2" data-validation-engine="validate[{if $SERVICE->get(is_required)}required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" data-fieldinfo="{$INFO}" />
													</span>
												</div>
											</div>
											<input type="hidden" class="fieldname" value="Rate{$ID}" data-prev-value="{if $SERVICE_DETAILS.rate}{number_format($SERVICE_DETAILS.rate, 2, '.', '')}{/if}">
										</span>
									{/if}
								</td>
							{else}
								<td class='fieldLabel'>&nbsp;</td>
								<td class='fieldValue'>&nbsp;</td>
							{/if}
						</tr>
						<tr>
							<td class='fieldLabel'>
								<label class="muted pull-right marginRight10px">Weight</label>
							</td>
							<td class='fieldValue localBasePlusField'>
							{assign var=FIELDNAME value='Weight'|cat:$ID}
							{if $SERVICE->get(is_required)}{$LOCALFIELDINFO.mandatory = true}{else}{$LOCALFIELDINFO.mandatory = false}{/if}
							{$LOCALFIELDINFO.name = $FIELDNAME}
							{$LOCALFIELDINFO.label = 'Weight'}
							{$LOCALFIELDINFO.type = 'integer'}
							{$INFO = Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($LOCALFIELDINFO))}
								<span class="value">{$SERVICE_DETAILS.weight}</span>
								{if $EDITABLE === TRUE}
									<span class="hide edit">
										<input name="Weight{$ID}" class="input-large nameField LocalBaseRateTrans" type="number" value="{$SERVICE_DETAILS.weight}" data-validation-engine="validate[{if $SERVICE->get(is_required)}required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" data-fieldinfo="{$INFO}" />
										<input type="hidden" class="fieldname" value="Weight{$ID}" data-prev-value="{$SERVICE_DETAILS.weight}">
									</span>
								{/if}
							</td>
							{if $SHOW_RATES}
								<td class='fieldLabel'>
									<label class="muted pull-right marginRight10px">Excess</label>
								</td>
								<td class='fieldValue localBasePlusField'>
								{assign var=FIELDNAME value='Excess'|cat:$ID}
								{if $SERVICE->get(is_required)}{$LOCALFIELDINFO.mandatory = true}{else}{$LOCALFIELDINFO.mandatory = false}{/if}
								{$LOCALFIELDINFO.name = $FIELDNAME}
								{$LOCALFIELDINFO.label = 'Excess'}
								{$LOCALFIELDINFO.type = 'currency'}
								{$INFO = Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($LOCALFIELDINFO))}
									<span class="value">{if $SERVICE_DETAILS.excess}{number_format($SERVICE_DETAILS.excess, 2, '.', '')}{/if}</span>
									{if $EDITABLE === TRUE}
										<span class="hide edit">
											<div class="row-fluid">
												<div class="input-prepend">
													<span class="span10">
														<span class="add-on">&#36;</span>
														<input name="Excess{$ID}" class="input-medium currencyField" type="text" value="{if $SERVICE_DETAILS.excess}{number_format($SERVICE_DETAILS.excess, 2, '.', '')}{/if}" data-decimal-seperator="." data-group-seperator="," data-number-of-decimal-places="2" data-validation-engine="validate[{if $SERVICE->get(is_required)}required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" data-fieldinfo="{$INFO}" />
													</span>
												</div>
											</div>
											<input type="hidden" class="fieldname" value="Excess{$ID}" data-prev-value="{if $SERVICE_DETAILS.excess}{number_format($SERVICE_DETAILS.excess, 2, '.', '')}{/if}">
										</span>
									{/if}
								</td>
							{else}
								<td class='fieldLabel'>&nbsp;</td>
								<td class='fieldValue'>&nbsp;</td>
							{/if}
						</tr>
					{elseif $RATE_TYPE eq "Weight/Mileage Trans."}
					<tr>
								<td class='fieldLabel'>
									<label class="muted pull-right marginRight10px">Miles</label>
								</td>
								<td class='fieldValue localWeightMileField'>
								{assign var=FIELDNAME value='Miles'|cat:$ID}
								{if $SERVICE->get(is_required)}{$LOCALFIELDINFO.mandatory = true}{else}{$LOCALFIELDINFO.mandatory = false}{/if}
								{$LOCALFIELDINFO.name = $FIELDNAME}
								{$LOCALFIELDINFO.label = 'Miles'}
								{$LOCALFIELDINFO.type = 'integer'}
								{$INFO = Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($LOCALFIELDINFO))}
								<span class="value">{$SERVICE_DETAILS.mileage}</span>
								{if $EDITABLE === TRUE}
									<span class="hide edit">
										<input id="{$MODULE_NAME}_editView_fieldName_Miles{$ID}" name="Miles{$ID}" class="input-large nameField localWeightMile" type="number" value="{$SERVICE_DETAILS.mileage}" data-validation-engine="validate[{if $SERVICE->get(is_required)}required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" data-fieldinfo="{$INFO}" />
										<input type="hidden" class="fieldname" value="Miles{$ID}" data-prev-value="{$SERVICE_DETAILS.mileage}">
									</span>
								{/if}
								</td>
								{if $SHOW_RATES}
									<td class='fieldLabel'>
										<label class="muted pull-right marginRight10px">Rate</label>
									</td>
									<td class='fieldValue localWeightMileField'>
									{assign var=FIELDNAME value='Rate'|cat:$ID}
									{if $SERVICE->get(is_required)}{$LOCALFIELDINFO.mandatory = true}{else}{$LOCALFIELDINFO.mandatory = false}{/if}
									{$LOCALFIELDINFO.name = $FIELDNAME}
									{$LOCALFIELDINFO.label = 'Rate'}
									{$LOCALFIELDINFO.type = 'currency'}
									{$INFO = Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($LOCALFIELDINFO))}
									<span class="value">{if $SERVICE_DETAILS.rate}{number_format($SERVICE_DETAILS.rate, 2, '.', '')}{/if}</span>
									{if $EDITABLE === TRUE}
										<span class="hide edit">
											<div class="row-fluid">
												<div class="input-prepend">
													<span class="span10">
														<span class="add-on">&#36;</span>
															<input id="{$MODULE_NAME}_editView_fieldName_Rate{$ID}" name="Rate{$ID}" class="input-medium currencyField" type="text" value="{if $SERVICE_DETAILS.rate}{number_format($SERVICE_DETAILS.rate, 2, '.', '')}{/if}" data-decimal-seperator="." data-group-seperator="," data-number-of-decimal-places="2" data-validation-engine="validate[{if $SERVICE->get(is_required)}required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" data-fieldinfo="{$INFO}" />
															<input type="hidden" class="fieldname" value="Rate{$ID}" data-prev-value="{if $SERVICE_DETAILS.rate}{number_format($SERVICE_DETAILS.rate, 2, '.', '')}{/if}">
														</span>
													</span>
												</div>
											</div>
										</span>
									{/if}
									</td>
								{else}
									<td class='fieldLabel'>&nbsp;</td>
									<td class='fieldValue'>&nbsp;</td>
								{/if}
							</tr>
							<tr>
								<td class='fieldLabel'>
									<label class="muted pull-right marginRight10px">Weight</label>
								</td>
								<td class='fieldValue localWeightMileField'>
								{assign var=FIELDNAME value='Weight'|cat:$ID}
								{if $SERVICE->get(is_required)}{$LOCALFIELDINFO.mandatory = true}{else}{$LOCALFIELDINFO.mandatory = false}{/if}
								{$LOCALFIELDINFO.name = $FIELDNAME}
								{$LOCALFIELDINFO.label = 'Weight'}
								{$LOCALFIELDINFO.type = 'integer'}
								{$INFO = Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($LOCALFIELDINFO))}
								<span class="value">{$SERVICE_DETAILS.weight}</span>
								{if $EDITABLE === TRUE}
									<span class="hide edit">
										<input id="{$MODULE_NAME}_editView_fieldName_Weight{$ID}" name="Weight{$ID}" class="input-large nameField localWeightMile" type="number" value="{$SERVICE_DETAILS.weight}" data-validation-engine="validate[{if $SERVICE->get(is_required)}required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" data-fieldinfo="{$INFO}" />
										<input type="hidden" class="fieldname" value="Weight{$ID}" data-prev-value="{$SERVICE_DETAILS.weight}">
									</span>
								{/if}
								</td>
								<td class='fieldLabel' colspan="2">&nbsp;</td>
							</tr>
					{elseif $RATE_TYPE eq "County Charge"}
					{assign var=PICKLIST_VALUES value=$SERVICE->getCountyChargePicklists($ID)}
							<tr>
								<td class='fieldLabel'>
									<label class="muted pull-right marginRight10px">County</label>
								</td>
								<td class='fieldValue localCountyChargeField'>
								{assign var=FIELDNAME value='County'|cat:$ID}
								{if $SERVICE->get(is_required)}{$LOCALFIELDINFO.mandatory = true}{else}{$LOCALFIELDINFO.mandatory = false}{/if}
								{$LOCALFIELDINFO.name = $FIELDNAME}
								{$LOCALFIELDINFO.label = 'County'}
								{$LOCALFIELDINFO.type = 'picklist'}
								{$INFO = Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($LOCALFIELDINFO))}
									<span class="value">{if is_numeric($SERVICE_DETAILS.county)}Select an Option{else}{$SERVICE_DETAILS.county}{/if}</span>
									{if $EDITABLE === TRUE}
										<span class="hide edit">
											<select class="chzn-select" name="County{$ID}" class ="localCountyPick" data-validation-engine="validate[{if $SERVICE->get(is_required)}required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" data-fieldinfo="{$INFO}" >
												<option value="" {if $SERVICE_DETAILS.county eq ""}selected{/if}>{vtranslate('LBL_SELECT_OPTION','Vtiger')}</option>
												{foreach item=PICKLIST_VALUE key=PICKLIST_NAME from=$PICKLIST_VALUES}
													<option name="{$PICKLIST_VALUE}{$ID}" value="{$PICKLIST_VALUE}" {if $SERVICE_DETAILS.county eq $PICKLIST_VALUE}selected{/if}>{$PICKLIST_VALUE}</option>
												{/foreach}
											</select>
											<input type="hidden" class="fieldname" value="County{$ID}" data-prev-value="{$SERVICE_DETAILS.county}">
										</span>
									{/if}
								</td>
								{if $SHOW_RATES}
									<td class='fieldLabel'>
										<label class="muted pull-right marginRight10px">Rate</label>
									</td>
									<td class='fieldValue localCountyChargeField'>
									{assign var=FIELDNAME value='Rate'|cat:$ID}
									{if $SERVICE->get(is_required)}{$LOCALFIELDINFO.mandatory = true}{else}{$LOCALFIELDINFO.mandatory = false}{/if}
									{$LOCALFIELDINFO.name = $FIELDNAME}
									{$LOCALFIELDINFO.label = 'Rate'}
									{$LOCALFIELDINFO.type = 'currency'}
									{$INFO = Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($LOCALFIELDINFO))}
									<span class="value">{if $SERVICE_DETAILS.rate}{number_format($SERVICE_DETAILS.rate, 2, '.', '')}{/if}</span>
									{if $EDITABLE === TRUE}
										<span class="hide edit">
											<div class="row-fluid">
												<div class="input-prepend">
													<span class="span10">
														<span class="add-on">&#36;</span>
														<input name="Rate{$ID}" class="input-medium currencyField" type="text" value="{if $SERVICE_DETAILS.rate}{number_format($SERVICE_DETAILS.rate, 2, '.', '')}{/if}" data-decimal-seperator="." data-group-seperator="," data-number-of-decimal-places="2" data-validation-engine="validate[{if $SERVICE->get(is_required)}required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" data-fieldinfo="{$INFO}" />
														<input type="hidden" class="fieldname" value="Rate{$ID}" data-prev-value="{if $SERVICE_DETAILS.rate}{number_format($SERVICE_DETAILS.rate, 2, '.', '')}{/if}">
													</span>
												</div>
											</div>
										</span>
									{/if}
									</td>
								{else}
									<td class='fieldLabel'>&nbsp;</td>
									<td class='fieldValue'>&nbsp;</td>
								{/if}
							</tr>
					{elseif $RATE_TYPE eq "Service Base Charge"}
							<tr>
								<td class='fieldLabel'>
									<label class="muted pull-right marginRight10px">Rate</label>
								</td>
								<td class='fieldValue'>
                                    {assign var=RATE value=$SERVICE->getServiceCharges($RECORD_ID)}
									<div class="row-fluid">
										<div class="input-prepend">
											<span class="span10">
												{if $SERVICE->get('service_base_charge_matrix')}
														Base Charge Matrix
                                                {else}
                                                <span class="value">{($RATE.rate) ? $RATE.rate : $SERVICE->get('service_base_charge')}&#37;</span>
												{/if}
											</span>
										</div>
									</div>
								</td>
								<td class='fieldLabel'>
									<label class="muted pull-right marginRight10px">Applies to </label>
								</td>
								<td class='fieldValue'>
									{$SERVICE->getServiceChargesApplies()}
								</td>
							</tr>
					{elseif $RATE_TYPE eq "Storage Valuation"}
							<tr>
								<td class='fieldLabel'>
									<label class="muted pull-right marginRight10px">Rate</label>
								</td>
								<td class='fieldValue'>
                                    {assign var=RATE value=$SERVICE->getStorageValuation($RECORD_ID)}
									<div class="row-fluid">
										<div class="input-prepend">
											<span class="span10">
												{if $SERVICE->get('service_base_charge_matrix')}
														Base Charge Matrix
                                                {else}
                                                <span class="value">{if !empty($RATE.rate)}{$RATE.rate}&#37;{/if}</span>
												{/if}
											</span>
										</div>
									</div>
								</td>
								<td class='fieldLabel'>
									<label class="muted pull-right marginRight10px">Applies to </label>
								</td>
								<td class='fieldValue'>
									{$SERVICE->getServiceChargesApplies()}
								</td>
							</tr>
							<tr>
                                <td class='fieldLabel'>
									<label class="muted pull-right marginRight10px">Months</label>
								</td>
								<td class='fieldValue'>
                                     {assign var=MONTHS value=$SERVICE->getStorageValuationMonths($RECORD_ID)}
                                     <div class="row-fluid">
										<div class="input-prepend">
											<span class="span10">
                                                <span class="value">{($MONTHS.months) ? $MONTHS.months : ''}</span>
											</span>
										</div>
									</div>
                                </td>
                                <td></td><td></td>
                            </tr>
					{elseif $RATE_TYPE eq "Hourly Set"}
					<tr>
						<td class='fieldLabel'>
							<label class="muted pull-right marginRight10px">Men</label>
						</td>
						<td class='fieldValue localHourlySetField'>
						{assign var=FIELDNAME value='Men'|cat:$ID}
						{if $SERVICE->get(is_required)}{$LOCALFIELDINFO.mandatory = true}{else}{$LOCALFIELDINFO.mandatory = false}{/if}
						{$LOCALFIELDINFO.name = $FIELDNAME}
						{$LOCALFIELDINFO.label = 'Men'}
						{$LOCALFIELDINFO.type = 'integer'}
						{$INFO = Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($LOCALFIELDINFO))}
							<span class="value">{$SERVICE_DETAILS.men}</span>
							{if $EDITABLE === TRUE}
								<span class="hide edit">
									<input name="Men{$ID}" class="input-large nameField localHourlySet" type="number" value="{$SERVICE_DETAILS.men}" data-validation-engine="validate[{if $SERVICE->get(is_required)}required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" data-fieldinfo="{$INFO}" />
									<input type="hidden" class="fieldname" value="Men{$ID}" data-prev-value="{$SERVICE_DETAILS.men}">
								</span>
							{/if}
						</td>
						<td class='fieldLabel'>
							<label class="muted pull-right marginRight10px">Hours</label>
						</td>
						<td class='fieldValue localHourlySetField'>
						{assign var=FIELDNAME value='Hours'|cat:$ID}
						{if $SERVICE->get(is_required)}{$LOCALFIELDINFO.mandatory = true}{else}{$LOCALFIELDINFO.mandatory = false}{/if}
						{$LOCALFIELDINFO.name = $FIELDNAME}
						{$LOCALFIELDINFO.label = 'Hours'}
						{$LOCALFIELDINFO.type = 'double'}
						{$INFO = Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($LOCALFIELDINFO))}
						<span class="value">{$SERVICE_DETAILS.hours}</span>
						{if $EDITABLE === TRUE}
							<span class="hide edit">
								<input name="Hours{$ID}" class="input-large nameField" type="number" value="{$SERVICE_DETAILS.hours}" data-decimal-seperator="." data-group-seperator="," data-number-of-decimal-places="2" step=".25" data-validation-engine="validate[{if $SERVICE->get(is_required)}required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" data-fieldinfo="{$INFO}" />
								<input type="hidden" class="fieldname" value="Hours{$ID}" data-prev-value="{$SERVICE_DETAILS.hours}">
							</span>
						{/if}
						</td>

					</tr>
					{assign var=TDCOUNT value=0}
					<tr>
						{if $SERVICE->get(hourlyset_hasvan) eq 1}
						{assign var=TDCOUNT value=$TDCOUNT+1}
						<td class='fieldLabel'>
							<label class="muted pull-right marginRight10px">Vans</label>
						</td>
						<td class='fieldValue localHourlySetField'>
						{assign var=FIELDNAME value='Vans'|cat:$ID}
						{if $SERVICE->get(is_required)}{$LOCALFIELDINFO.mandatory = true}{else}{$LOCALFIELDINFO.mandatory = false}{/if}
						{$LOCALFIELDINFO.name = $FIELDNAME}
						{$LOCALFIELDINFO.label = 'Vans'}
						{$LOCALFIELDINFO.type = 'integer'}
						{$INFO = Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($LOCALFIELDINFO))}
							<span class="value">{$SERVICE_DETAILS.vans}</span>
							{if $EDITABLE === TRUE}
								<span class="hide edit">
									<input name="Vans{$ID}" class="input-large nameField localHourlySet" type="number" value="{$SERVICE_DETAILS.vans}" data-validation-engine="validate[{if $SERVICE->get(is_required)}required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" data-fieldinfo="{$INFO}" />
									<input type="hidden" class="fieldname" value="Vans{$ID}" data-prev-value="{$SERVICE_DETAILS.vans}">
								</span>
							{/if}
						</td>
						{/if}
						{if $SERVICE->get(hourlyset_hastravel) eq 1}
						{assign var=TDCOUNT value=$TDCOUNT+1}
							<td class='fieldLabel'>
								<label class="muted pull-right marginRight10px">Travel Time</label>
							</td>
							<td class='fieldValue localHourlySetField'>
							{assign var=FIELDNAME value='TravelTime'|cat:$ID}
							{if $SERVICE->get(is_required)}{$LOCALFIELDINFO.mandatory = true}{else}{$LOCALFIELDINFO.mandatory = false}{/if}
							{$LOCALFIELDINFO.name = $FIELDNAME}
							{$LOCALFIELDINFO.label = 'Travel Time'}
							{$LOCALFIELDINFO.type = 'double'}
							{$INFO = Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($LOCALFIELDINFO))}
								<span class="value">{$SERVICE_DETAILS.traveltime}</span>
								{if $EDITABLE === TRUE}
									<span class="hide edit">
										<input name="TravelTime{$ID}" class="input-large nameField" type="number" value="{$SERVICE_DETAILS.traveltime}" data-decimal-seperator="." data-group-seperator="," data-number-of-decimal-places="2" step=".25" data-validation-engine="validate[{if $SERVICE->get(is_required)}required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" data-fieldinfo="{$INFO}" />
										<input type="hidden" class="fieldname" value="TravelTime{$ID}" data-prev-value="{$SERVICE_DETAILS.traveltime}">
									</span>
								{/if}
							</td>
						{/if}
						{if ($TDCOUNT % 2) eq 0}
						</tr>
						{if $SHOW_RATES}
							<tr>
						{/if}
						{/if}
						{if $SHOW_RATES}
						{assign var=TDCOUNT value=$TDCOUNT+1}
						<td class='fieldLabel'>
							<label class="muted pull-right marginRight10px">Rate</label>
						</td>
						<td class='fieldValue localHourlySetField'>
						{assign var=FIELDNAME value='Rate'|cat:$ID}
						{if $SERVICE->get(is_required)}{$LOCALFIELDINFO.mandatory = true}{else}{$LOCALFIELDINFO.mandatory = false}{/if}
						{$LOCALFIELDINFO.name = $FIELDNAME}
						{$LOCALFIELDINFO.label = 'Rate'}
						{$LOCALFIELDINFO.type = 'currency'}
						{$INFO = Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($LOCALFIELDINFO))}
						<span class="value">{if $SERVICE_DETAILS.rate}{number_format($SERVICE_DETAILS.rate, 2, '.', '')}{/if}</span>
						{if $EDITABLE === TRUE}
							<span class="hide edit">
								<div class="row-fluid">
									<div class="input-prepend">
										<span class="span10">
											<span class="add-on">&#36;</span>
												<input name="Rate{$ID}" class="input-medium currencyField" type="number" value="{if $SERVICE_DETAILS.rate}{number_format($SERVICE_DETAILS.rate, 2, '.', '')}{/if}" data-decimal-seperator="." data-group-seperator="," data-number-of-decimal-places="2" data-validation-engine="validate[{if $SERVICE->get(is_required)}required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" data-fieldinfo="{$INFO}" />
												<input type="hidden" class="fieldname" value="Rate{$ID}" data-prev-value="{if $SERVICE_DETAILS.rate}{number_format($SERVICE_DETAILS.rate, 2, '.', '')}{/if}">
										</span>
									</div>
								</div>
							</span>
						{/if}
						</td>
						{if ($TDCOUNT % 2) eq 1}
							<td class='fieldLabel'>&nbsp;</td>
							<td class='fieldValue localHourlySetField'>&nbsp;</td>
						{/if}
					</tr>
					{/if}
					{else if $RATE_TYPE eq "Hourly Simple"}
						<tr>
							<td class='fieldLabel'>
								<label class="muted pull-right marginRight10px">Quantity</label>
							</td>
							<td class='fieldValue localHourlySimpleField'>
							{assign var=FIELDNAME value='Quantity'|cat:$ID}
							{if $SERVICE->get(is_required)}{$LOCALFIELDINFO.mandatory = true}{else}{$LOCALFIELDINFO.mandatory = false}{/if}
							{$LOCALFIELDINFO.name = $FIELDNAME}
							{$LOCALFIELDINFO.label = 'Quantity'}
							{$LOCALFIELDINFO.type = 'integer'}
							{$INFO = Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($LOCALFIELDINFO))}
								<span class="value">{if $SERVICE_DETAILS.quantity}{number_format($SERVICE_DETAILS.quantity, 0, '.', '')}{/if}</span>
								{if $EDITABLE === TRUE}
									<span class="hide edit">
										<input name="Quantity{$ID}" class="input-large nameField" type="number" value="{if $SERVICE_DETAILS.quantity}{number_format($SERVICE_DETAILS.quantity, 0, '.', '')}{/if}" data-validation-engine="validate[{if $SERVICE->get(is_required)}required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" data-fieldinfo="{$INFO}" />
										<input type="hidden" class="fieldname" value="Quantity{$ID}" data-prev-value="{if $SERVICE_DETAILS.quantity}{number_format($SERVICE_DETAILS.quantity, 0, '.', '')}{/if}">
									</span>
								{/if}
							</td>
							<td class='fieldLabel'>
								<label class="muted pull-right marginRight10px">Hours</label>
							</td>
							<td class='fieldValue localHourlySimpleField'>
							{assign var=FIELDNAME value='Hours'|cat:$ID}
							{if $SERVICE->get(is_required)}{$LOCALFIELDINFO.mandatory = true}{else}{$LOCALFIELDINFO.mandatory = false}{/if}
							{$LOCALFIELDINFO.name = $FIELDNAME}
							{$LOCALFIELDINFO.label = 'Hours'}
							{$LOCALFIELDINFO.type = 'double'}
							{$INFO = Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($LOCALFIELDINFO))}
								<span class="value">{if $SERVICE_DETAILS.hours}{number_format($SERVICE_DETAILS.hours, 2, '.', '')}{/if}</span>
								{if $EDITABLE === TRUE}
								<span class="hide edit">
									<input name="Hours{$ID}" class="input-large nameField" type="number" value="{if $SERVICE_DETAILS.hours}{number_format($SERVICE_DETAILS.hours, 2, '.', '')}{/if}" data-decimal-seperator="." data-group-seperator="," data-number-of-decimal-places="2" step=".25" data-validation-engine="validate[{if $SERVICE->get(is_required)}required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" data-fieldinfo="{$INFO}" />
									<input type="hidden" class="fieldname" value="Hours{$ID}" data-prev-value="{if $SERVICE_DETAILS.hours}{number_format($SERVICE_DETAILS.hours, 2, '.', '')}{/if}">
								</span>
								{/if}
							</td>
						</tr>
						{if $SHOW_RATES}
						<tr>
							<td class='fieldLabel'>
								<label class="muted pull-right marginRight10px">Rate</label>
							</td>
							<td class='fieldValue localHourlySimpleField'>
							{assign var=FIELDNAME value='Rate'|cat:$ID}
							{if $SERVICE->get(is_required)}{$LOCALFIELDINFO.mandatory = true}{else}{$LOCALFIELDINFO.mandatory = false}{/if}
							{$LOCALFIELDINFO.name = $FIELDNAME}
							{$LOCALFIELDINFO.label = 'Rate'}
							{$LOCALFIELDINFO.type = 'currency'}
							{$INFO = Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($LOCALFIELDINFO))}
								<span class="value">{if !empty($SERVICE_DETAILS.rate)}{number_format($SERVICE_DETAILS.rate, 2, '.', '')}{elseif $SERVICE->get('hourlysimple_rate')}{number_format($SERVICE->get('hourlysimple_rate'), 2, '.', '')}{/if}</span>
								{if $EDITABLE === TRUE}
									<span class="hide edit">
										<div class="row-fluid">
											<div class="input-prepend">
												<span class="span10">
													<span class="add-on">&#36;</span>
													<input name="Rate{$ID}" class="input-medium currencyField" type="number" value="{if !empty($SERVICE_DETAILS.rate)}{number_format($SERVICE_DETAILS.rate, 2, '.', '')}{elseif $SERVICE->get('hourlysimple_rate')}{number_format($SERVICE->get('hourlysimple_rate'), 2, '.', '')}{/if}" data-decimal-seperator="." data-group-seperator="," data-number-of-decimal-places="2" data-validation-engine="validate[{if $SERVICE->get(is_required)}required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" data-fieldinfo="{$INFO}" />
												</span>
											</div>
										</div>
										<input type="hidden" class="fieldname" value="Rate{$ID}" data-prev-value="{if !empty($SERVICE_DETAILS.rate)}{number_format($SERVICE_DETAILS.rate, 2, '.', '')}{elseif $SERVICE->get('hourlysimple_rate')}{number_format($SERVICE->get('hourlysimple_rate'), 2, '.', '')}{/if}">
									</span>
								{/if}
							</td>
						<td class='fieldLabel'>&nbsp;</td>
						<td class='fieldValue'>&nbsp;</td>
						</tr>
						{/if}
					{elseif $RATE_TYPE eq "Flat Charge"}
						{if $SHOW_RATES}
						<tr>
							<td class='fieldLabel'>
								<label class="muted pull-right marginRight10px">Rate</label>
								<span><input name = "RateIncluded{$ID}" class="muted pull-right" style = "margin-right: 10px" type = "checkbox" {if $SERVICE_DETAILS.rate_included == '1'}checked{/if} disabled></span>
							</td>
							<td class='fieldValue localFlatChargeField'>
							{assign var=FIELDNAME value='Rate'|cat:$ID}
							{if $SERVICE->get(is_required)}{$LOCALFIELDINFO.mandatory = true}{else}{$LOCALFIELDINFO.mandatory = false}{/if}
							{$LOCALFIELDINFO.name = $FIELDNAME}
							{$LOCALFIELDINFO.label = 'Rate'}
							{$LOCALFIELDINFO.type = 'currency'}
							{$INFO = Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($LOCALFIELDINFO))}
							<span class="value">{if !empty($SERVICE_DETAILS.rate)}{number_format($SERVICE_DETAILS.rate, 2, '.', '')}{elseif $SERVICE->get('flat_rate')}{$SERVICE->get('flat_rate')}{/if}</span>
							{if $EDITABLE === TRUE}
								<span class="hide edit">
									<div class="row-fluid">
										<div class="input-prepend">
											<span class="span10">
												<span class="add-on">&#36;</span>
													<input name="Rate{$ID}" class="input-medium currencyField" type="number" value="{if !empty($SERVICE_DETAILS.rate)}{number_format($SERVICE_DETAILS.rate, 2, '.', '')}{elseif $SERVICE->get('flat_rate')}{$SERVICE->get('flat_rate')}{/if}" data-decimal-seperator="." data-group-seperator="," data-number-of-decimal-places="2" step=".01" data-validation-engine="validate[{if $SERVICE->get(is_required)}required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" data-fieldinfo="{$INFO}" />
													<input type="hidden" class="fieldname" value="Rate{$ID}" data-prev-value="{if !empty($SERVICE_DETAILS.rate)}{number_format($SERVICE_DETAILS.rate, 2, '.', '')}{elseif $SERVICE->get('flat_rate')}{$SERVICE->get('flat_rate')}{/if}">
											</span>
										</div>
									</div>
								</span>
							{/if}
							</td>
							<td class='fieldLabel'>&nbsp;</td>
							<td class='fieldValue localFlatChargeField'>&nbsp;</td>
						</tr>
						{/if}
				    {elseif $RATE_TYPE eq "Per Cu Ft"}
                        <tr>
                            <td class='fieldLabel'>
                                <label class="muted pull-right marginRight10px">Cubic Feet</label>
                            </td>
                            <td class='fieldValue localCuFtField'>
                            {assign var=FIELDNAME value='CubicFeet'|cat:$ID}
                                {if $SERVICE->get(is_required)}{$LOCALFIELDINFO.mandatory = true}{else}{$LOCALFIELDINFO.mandatory = false}{/if}
                                {$LOCALFIELDINFO.name = $FIELDNAME}
                                {$LOCALFIELDINFO.label = 'Cubic Feet'}
                                {$LOCALFIELDINFO.type = 'double'}
                                {$INFO = Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($LOCALFIELDINFO))}
                                <span class="value">{if $SERVICE_DETAILS.cubicfeet}{number_format($SERVICE_DETAILS.cubicfeet, 2, '.', '')}{/if}</span>
                                {if $EDITABLE === TRUE}
                                    <span class="hide edit">
                                        <input name="CubicFeet{$ID}" class="input-large nameField" type="number" value="{if $SERVICE_DETAILS.cubicfeet}{number_format($SERVICE_DETAILS.cubicfeet, 2, '.', '')}{elseif $SERVICE->get('cuft_rate')}{number_format($SERVICE->get('cuft_rate'), 2, '.', '')}{/if}" data-decimal-seperator="." data-group-seperator="," data-number-of-decimal-places="2" step=".5" data-validation-engine="validate[{if $SERVICE->get(is_required)}required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" data-fieldinfo="{$INFO}" />
								        <input type="hidden" class="fieldname" value="CubicFeet{$ID}" data-prev-value="{if $SERVICE_DETAILS.cubicfeet}{number_format($SERVICE_DETAILS.cubicfeet, 2, '.', '')}{elseif $SERVICE->get('cuft_rate')}{number_format($SERVICE->get('cuft_rate'), 2, '.', '')}{/if}">
                                    </span>
                                {/if}
                            </td>
                            {if $SHOW_RATES}
                                <td class='fieldLabel'>
                                    <label class="muted pull-right marginRight10px">Rate</label>
                                </td>
                                <td class='fieldValue localCuFtField'>
                                {assign var=FIELDNAME value='Rate'|cat:$ID}
                                    {if $SERVICE->get(is_required)}{$LOCALFIELDINFO.mandatory = true}{else}{$LOCALFIELDINFO.mandatory = false}{/if}
                                    {$LOCALFIELDINFO.name = $FIELDNAME}
                                    {$LOCALFIELDINFO.label = 'Rate'}
                                    {$LOCALFIELDINFO.type = 'currency'}
                                    {$INFO = Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($LOCALFIELDINFO))}
                                    <span class="value">{if !empty($SERVICE_DETAILS.rate)}{number_format($SERVICE_DETAILS.rate, 2, '.', '')}{elseif $SERVICE->get('cuft_rate')}{number_format($SERVICE->get('cuft_rate'), 2, '.', '')}{/if}</span>
                                    {if $EDITABLE === TRUE}
                                        <span class="hide edit">
                                        <div class="row-fluid">
                                            <div class="input-prepend">
                                                <span class="span10">
                                                    <span class="add-on">&#36;</span>
                                                    <input name="Rate{$ID}" class="input-medium currencyField" type="number" value="{if !empty($SERVICE_DETAILS.rate)}{number_format(4, 2, '.', '')}{elseif $SERVICE->get('cuft_rate')}{number_format($SERVICE->get('cuft_rate'), 2, '.', '')}{/if}" data-decimal-seperator="." data-group-seperator="," data-number-of-decimal-places="2" data-validation-engine="validate[{if $SERVICE->get(is_required)}required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" data-fieldinfo="{$INFO}" />
                                                    <input type="hidden" class="fieldname" value="Rate{$ID}" data-prev-value="{if !empty($SERVICE_DETAILS.rate)}{number_format(3, 2, '.', '')}{elseif $SERVICE->get('cuft_rate')}{number_format($SERVICE->get('cuft_rate'), 2, '.', '')}{/if}">
                                                </span>
                                            </div>
                                        </div>
                                    </span>
                                    {/if}
                                </td>
                            {else}
                                <td class='fieldLabel'>&nbsp;</td>
                                <td class='fieldValue'>&nbsp;</td>
                            {/if}
                        </tr>
					{elseif $RATE_TYPE eq "Per Cu Ft/Per Day"}
					<tr>
						<td class='fieldLabel'>
							<label class="muted pull-right marginRight10px">Cubic Feet</label>
						</td>
						<td class='fieldValue localCuFtDayField'>
						{assign var=FIELDNAME value='CubicFeet'|cat:$ID}
						{if $SERVICE->get(is_required)}{$LOCALFIELDINFO.mandatory = true}{else}{$LOCALFIELDINFO.mandatory = false}{/if}
						{$LOCALFIELDINFO.name = $FIELDNAME}
						{$LOCALFIELDINFO.label = 'Cubic Feet'}
						{$LOCALFIELDINFO.type = 'double'}
						{$INFO = Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($LOCALFIELDINFO))}
						<span class="value">{if $SERVICE_DETAILS.cubicfeet}{number_format($SERVICE_DETAILS.cubicfeet, 2, '.', '')}{/if}</span>
						{if $EDITABLE === TRUE}
							<span class="hide edit">
								<input name="CubicFeet{$ID}" class="input-large nameField" type="number" value="{if $SERVICE_DETAILS.cubicfeet}{number_format($SERVICE_DETAILS.cubicfeet, 2, '.', '')}{/if}" data-decimal-seperator="." data-group-seperator="," data-number-of-decimal-places="2" step=".5" data-validation-engine="validate[{if $SERVICE->get(is_required)}required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" data-fieldinfo="{$INFO}" />
								<input type="hidden" class="fieldname" value="CubicFeet{$ID}" data-prev-value="{if $SERVICE_DETAILS.cubicfeet}{number_format($SERVICE_DETAILS.cubicfeet, 2, '.', '')}{/if}">
							</span>
						{/if}
						</td>
						<td class='fieldLabel'>
							<label class="muted pull-right marginRight10px">Days</label>
						</td>
						<td class='fieldValue localCuFtDayField'>
						{assign var=FIELDNAME value='Days'|cat:$ID}
						{if $SERVICE->get(is_required)}{$LOCALFIELDINFO.mandatory = true}{else}{$LOCALFIELDINFO.mandatory = false}{/if}
						{$LOCALFIELDINFO.name = $FIELDNAME}
						{$LOCALFIELDINFO.label = 'Days'}
						{$LOCALFIELDINFO.type = 'integer'}
						{$INFO = Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($LOCALFIELDINFO))}
						<span class="value">{if $SERVICE_DETAILS.days}{number_format($SERVICE_DETAILS.days, 0, '.', '')}{/if}</span>
						{if $EDITABLE === TRUE}
							<span class="hide edit">
								<input name="Days{$ID}" class="input-large nameField" type="number" value="{if $SERVICE_DETAILS.days}{number_format($SERVICE_DETAILS.days, 0, '.', '')}{/if}" data-validation-engine="validate[{if $SERVICE->get(is_required)}required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" data-fieldinfo="{$INFO}" />
								<input type="hidden" class="fieldname" value="Days{$ID}" data-prev-value="{if $SERVICE_DETAILS.days}{number_format($SERVICE_DETAILS.days, 0, '.', '')}{/if}">
							</span>
						{/if}
						</td>
					</tr>
					<tr>
						{if $SHOW_RATES}
							<td class='fieldLabel'>
								<label class="muted pull-right marginRight10px">Rate</label>
							</td>
							<td class='fieldValue localCuFtDayField'>
							{assign var=FIELDNAME value='Rate'|cat:$ID}
							{if $SERVICE->get(is_required)}{$LOCALFIELDINFO.mandatory = true}{else}{$LOCALFIELDINFO.mandatory = false}{/if}
							{$LOCALFIELDINFO.name = $FIELDNAME}
							{$LOCALFIELDINFO.label = 'Rate'}
							{$LOCALFIELDINFO.type = 'currency'}
							{$INFO = Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($LOCALFIELDINFO))}
							<span class="value">{if $SERVICE_DETAILS.rate != 0}{number_format($SERVICE_DETAILS.rate, 2, '.', '')}{elseif $SERVICE->get('cuftperday_rate')}{number_format($SERVICE->get('cuftperday_rate'), 2, '.', '')}{/if}</span>
							{if $EDITABLE === TRUE}
								<span class="hide edit">
									<div class="row-fluid">
										<div class="input-prepend">
											<span class="span10">
												<span class="add-on">&#36;</span>
												<input name="Rate{$ID}" class="input-medium currencyField" type="number" value="{if $SERVICE_DETAILS.rate != 0}{number_format($SERVICE_DETAILS.rate, 2, '.', '')}{elseif $SERVICE->get('cuftperday_rate')}{number_format($SERVICE->get('cuftperday_rate'), 2, '.', '')}{/if}" data-decimal-seperator="." data-group-seperator="," data-number-of-decimal-places="2" data-validation-engine="validate[{if $SERVICE->get(is_required)}required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" data-fieldinfo="{$INFO}" />
												<input type="hidden" class="fieldname" value="Rate{$ID}" data-prev-value="{if $SERVICE_DETAILS.rate != 0}{number_format($SERVICE_DETAILS.rate, 2, '.', '')}{elseif $SERVICE->get('cuftperday_rate')}{number_format($SERVICE->get('cuftperday_rate'), 2, '.', '')}{/if}">
											</span>
										</div>
									</div>
								</span>
							{/if}
							</td>
						{else}
							<td class='fieldLabel'>&nbsp;</td>
							<td class='fieldValue'>&nbsp;</td>
						{/if}
						<td class='fieldLabel'>&nbsp;</td>
						<td class='fieldValue localCuFtDayField'>&nbsp;</td>
					</tr>
				{elseif $RATE_TYPE eq "Per Cu Ft/Per Month"}
					<tr>
						<td class='fieldLabel'>
							<label class="muted pull-right marginRight10px">Cubic Feet</label>
						</td>
						<td class='fieldValue localCuFtMonthField'>
						{assign var=FIELDNAME value='CubicFeet'|cat:$ID}
						{if $SERVICE->get(is_required)}{$LOCALFIELDINFO.mandatory = true}{else}{$LOCALFIELDINFO.mandatory = false}{/if}
						{$LOCALFIELDINFO.name = $FIELDNAME}
						{$LOCALFIELDINFO.label = 'Cubic Feet'}
						{$LOCALFIELDINFO.type = 'double'}
						{$INFO = Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($LOCALFIELDINFO))}
						<span class="value">{if $SERVICE_DETAILS.cubicfeet}{number_format($SERVICE_DETAILS.cubicfeet, 2, '.', '')}{/if}</span>
						{if $EDITABLE === TRUE}
							<span class="hide edit">
								<input name="CubicFeet{$ID}" class="input-large nameField" type="number" value="{if $SERVICE_DETAILS.cubicfeet}{number_format($SERVICE_DETAILS.cubicfeet, 2, '.', '')}{/if}" data-decimal-seperator="." data-group-seperator="," data-number-of-decimal-places="2" step=".5" data-validation-engine="validate[{if $SERVICE->get(is_required)}required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" data-fieldinfo="{$INFO}" />
								<input type="hidden" class="fieldname" value="CubicFeet{$ID}" data-prev-value="{if $SERVICE_DETAILS.cubicfeet}{number_format($SERVICE_DETAILS.cubicfeet, 2, '.', '')}{/if}">
							</span>
						{/if}
						</td>
						<td class='fieldLabel'>
							<label class="muted pull-right marginRight10px">Months</label>
						</td>
						<td class='fieldValue localCuFtMonthField'>
						{assign var=FIELDNAME value='Months'|cat:$ID}
						{if $SERVICE->get(is_required)}{$LOCALFIELDINFO.mandatory = true}{else}{$LOCALFIELDINFO.mandatory = false}{/if}
						{$LOCALFIELDINFO.name = $FIELDNAME}
						{$LOCALFIELDINFO.label = 'Months'}
						{$LOCALFIELDINFO.type = 'integer'}
						{$INFO = Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($LOCALFIELDINFO))}
						<span class="value">{if $SERVICE_DETAILS.months}{number_format($SERVICE_DETAILS.months, 0, '.', '')}{/if}</span>
						{if $EDITABLE === TRUE}
							<span class="hide edit">
								<input name="Months{$ID}" class="input-large nameField" type="number" value="{if $SERVICE_DETAILS.months}{number_format($SERVICE_DETAILS.months, 0, '.', '')}{/if}" data-validation-engine="validate[{if $SERVICE->get(is_required)}required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" data-fieldinfo="{$INFO}" />
								<input type="hidden" class="fieldname" value="Months{$ID}" data-prev-value="{if $SERVICE_DETAILS.months}{number_format($SERVICE_DETAILS.months, 0, '.', '')}{/if}">
							</span>
						{/if}
						</td>
					</tr>
					{if $SHOW_RATES}
						<tr>
							<td class='fieldLabel'>
								<label class="muted pull-right marginRight10px">Rate</label>
							</td>
							<td class='fieldValue localCuFtMonthField'>
							{assign var=FIELDNAME value='Rate'|cat:$ID}
							{if $SERVICE->get(is_required)}{$LOCALFIELDINFO.mandatory = true}{else}{$LOCALFIELDINFO.mandatory = false}{/if}
							{$LOCALFIELDINFO.name = $FIELDNAME}
							{$LOCALFIELDINFO.label = 'Rate'}
							{$LOCALFIELDINFO.type = 'currency'}
							{$INFO = Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($LOCALFIELDINFO))}
							<span class="value">{if !empty($SERVICE_DETAILS.rate)}{number_format($SERVICE_DETAILS.rate, 2, '.', '')}{elseif $SERVICE->get('cuftpermonth_rate')}{number_format($SERVICE->get('cuftpermonth_rate'), 2, '.', '')}{/if}</span>
							{if $EDITABLE === TRUE}
								<span class="hide edit">
									<div class="row-fluid">
										<div class="input-prepend">
											<span class="span10">
												<span class="add-on">&#36;</span>
												<input name="Rate{$ID}" class="input-medium currencyField" type="number" value="{if !empty($SERVICE_DETAILS.rate)}{number_format($SERVICE_DETAILS.rate, 2, '.', '')}{elseif $SERVICE->get('cuftpermonth_rate')}{number_format($SERVICE->get('cuftpermonth_rate'), 2, '.', '')}{/if}" data-decimal-seperator="." data-group-seperator="," data-number-of-decimal-places="2" data-validation-engine="validate[{if $SERVICE->get(is_required)}required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" data-fieldinfo="{$INFO}" />
												<input type="hidden" class="fieldname" value="Rate{$ID}" data-prev-value="{if !empty($SERVICE_DETAILS.rate)}{number_format($SERVICE_DETAILS.rate, 2, '.', '')}{elseif $SERVICE->get('cuftpermonth_rate')}{number_format($SERVICE->get('cuftpermonth_rate'), 2, '.', '')}{/if}">
											</span>
										</div>
									</div>
								</span>
							{/if}
							</td>
							<td class='fieldLabel'>&nbsp;</td>
							<td class='fieldValue localCuFtMonthField'>&nbsp;</td>
						</tr>
					{/if}
					{elseif $RATE_TYPE eq "Per CWT" || $RATE_TYPE eq "SIT First Day Rate"}
					<tr>
						<td class='fieldLabel'>
							<label class="muted pull-right marginRight10px">Weight</label>
						</td>
						<td class='fieldValue localCWTField'>
						{assign var=FIELDNAME value='Weight'|cat:$ID}
						{if $SERVICE->get(is_required)}{$LOCALFIELDINFO.mandatory = true}{else}{$LOCALFIELDINFO.mandatory = false}{/if}
						{$LOCALFIELDINFO.name = $FIELDNAME}
						{$LOCALFIELDINFO.label = 'Weight'}
						{$LOCALFIELDINFO.type = 'integer'}
						{$INFO = Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($LOCALFIELDINFO))}
							<span class="value">{(int) $SERVICE_DETAILS.weight}</span>
							{if $EDITABLE === TRUE}
								<span class="hide edit">
									<input name="Weight{$ID}" class="input-large nameField" type="number" value="{(int) $SERVICE_DETAILS.weight}" data-validation-engine="validate[{if $SERVICE->get(is_required)}required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" data-fieldinfo="{$INFO}" />
									<input type="hidden" class="fieldname" value="Weight{$ID}" data-prev-value="{(int) $SERVICE_DETAILS.weight}">
								</span>
							{/if}
						</td>
						{if $SHOW_RATES}
							<td class='fieldLabel'>
								<label class="muted pull-right marginRight10px">Rate</label>
							</td>
							<td class='fieldValue localCWTField'>
							{assign var=FIELDNAME value='Rate'|cat:$ID}
							{if $SERVICE->get(is_required)}{$LOCALFIELDINFO.mandatory = true}{else}{$LOCALFIELDINFO.mandatory = false}{/if}
							{$LOCALFIELDINFO.name = $FIELDNAME}
							{$LOCALFIELDINFO.label = 'Rate'}
							{$LOCALFIELDINFO.type = 'currency'}
							{$INFO = Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($LOCALFIELDINFO))}
							<span class="value">{if !empty($SERVICE_DETAILS.rate)}{number_format($SERVICE_DETAILS.rate, 2, '.', '')}{elseif $SERVICE->get('cwt_rate')}{number_format($SERVICE->get('cwt_rate'), 2, '.', '')}{/if}</span>
							{if $EDITABLE === TRUE}
								<span class="hide edit">
									<div class="row-fluid">
										<div class="input-prepend">
											<span class="span10">
												<span class="add-on">&#36;</span>
												<input name="Rate{$ID}" class="input-medium currencyField" type="number" value="{if !empty($SERVICE_DETAILS.rate)}{number_format($SERVICE_DETAILS.rate, 2, '.', '')}{elseif $SERVICE->get('cwt_rate')}{number_format($SERVICE->get('cwt_rate'), 2, '.', '')}{/if}" data-decimal-seperator="." data-group-seperator="," data-number-of-decimal-places="2" data-validation-engine="validate[{if $SERVICE->get(is_required)}required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" data-fieldinfo="{$INFO}" />
												<input type="hidden" class="fieldname" value="Rate{$ID}" data-prev-value="{if !empty($SERVICE_DETAILS.rate)}{number_format($SERVICE_DETAILS.rate, 2, '.', '')}{elseif $SERVICE->get('cwt_rate')}{number_format($SERVICE->get('cwt_rate'), 2, '.', '')}{/if}">
											</span>
										</div>
									</div>
								</span>
							{/if}
							</td>
						{else}
							<td class='fieldLabel'>&nbsp;</td>
							<td class='fieldValue'>&nbsp;</td>
						{/if}
					</tr>
					{elseif $RATE_TYPE eq "Per CWT/Per Day" || $RATE_TYPE eq "SIT Additional Day Rate"}
						<tr>
							<td class='fieldLabel'>
								<label class="muted pull-right marginRight10px">Weight</label>
							</td>
							<td class='fieldValue localCWTDayField'>
							{assign var=FIELDNAME value='Weight'|cat:$ID}
							{if $SERVICE->get(is_required)}{$LOCALFIELDINFO.mandatory = true}{else}{$LOCALFIELDINFO.mandatory = false}{/if}
							{$LOCALFIELDINFO.name = $FIELDNAME}
							{$LOCALFIELDINFO.label = 'Weight'}
							{$LOCALFIELDINFO.type = 'integer'}
							{$INFO = Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($LOCALFIELDINFO))}
							<span class="value">{(int) $SERVICE_DETAILS.weight}</span>
							{if $EDITABLE === TRUE}
								<span class="hide edit">
									<input name="Weight{$ID}" class="input-large nameField" type="number" value="{(int) $SERVICE_DETAILS.weight}" data-validation-engine="validate[{if $SERVICE->get(is_required)}required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" data-fieldinfo="{$INFO}" />
									<input type="hidden" class="fieldname" value="Weight{$ID}" data-prev-value="{(int) $SERVICE_DETAILS.weight}">
								</span>
							{/if}
							</td>
							<td class='fieldLabel'>
								<label class="muted pull-right marginRight10px">Days</label>
							</td>
							<td class='fieldValue localCWTDayField'>
							{assign var=FIELDNAME value='Days'|cat:$ID}
							{if $SERVICE->get(is_required)}{$LOCALFIELDINFO.mandatory = true}{else}{$LOCALFIELDINFO.mandatory = false}{/if}
							{$LOCALFIELDINFO.name = $FIELDNAME}
							{$LOCALFIELDINFO.label = 'Days'}
							{$LOCALFIELDINFO.type = 'integer'}
							{$INFO = Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($LOCALFIELDINFO))}
							<span class="value">{(int) $SERVICE_DETAILS.days}</span>
							{if $EDITABLE === TRUE}
								<span class="hide edit">
									<input name="Days{$ID}" class="input-large nameField" type="number" value="{(int) $SERVICE_DETAILS.days}" data-validation-engine="validate[{if $SERVICE->get(is_required)}required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" data-fieldinfo="{$INFO}" />
									<input type="hidden" class="fieldname" value="Days{$ID}" data-prev-value="{(int) $SERVICE_DETAILS.days}">
								</span>
							{/if}
							</td>
						</tr>
						{if $SHOW_RATES}
							<tr>
								<td class='fieldLabel'>
									<label class="muted pull-right marginRight10px">Rate</label>
								</td>
								<td class='fieldValue localCWTDayField'>
								{assign var=FIELDNAME value='Rate'|cat:$ID}
								{if $SERVICE->get(is_required)}{$LOCALFIELDINFO.mandatory = true}{else}{$LOCALFIELDINFO.mandatory = false}{/if}
								{$LOCALFIELDINFO.name = $FIELDNAME}
								{$LOCALFIELDINFO.label = 'Rate'}
								{$LOCALFIELDINFO.type = 'currency'}
								{$INFO = Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($LOCALFIELDINFO))}
								<span class="value">{if !empty($SERVICE_DETAILS.rate)}{number_format($SERVICE_DETAILS.rate, 2, '.', '')}{elseif $SERVICE->get('cwtperday_rate')}{number_format($SERVICE->get('cwtperday_rate'), 2, '.', '')}{/if}</span>
								{if $EDITABLE === TRUE}
									<span class="hide edit">
										<div class="row-fluid">
											<div class="input-prepend">
												<span class="span10">
													<span class="add-on">&#36;</span>
													<input name="Rate{$ID}" class="input-medium currencyField" type="number" value="{if !empty($SERVICE_DETAILS.rate)}{number_format($SERVICE_DETAILS.rate, 2, '.', '')}{elseif $SERVICE->get('cwtperday_rate')}{number_format($SERVICE->get('cwtperday_rate'), 2, '.', '')}{/if}" data-decimal-seperator="." data-group-seperator="," data-number-of-decimal-places="2" data-validation-engine="validate[{if $SERVICE->get(is_required)}required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" data-fieldinfo="{$INFO}" />
													<input type="hidden" class="fieldname" value="Rate{$ID}" data-prev-value="{if !empty($SERVICE_DETAILS.rate)}{number_format($SERVICE_DETAILS.rate, 2, '.', '')}{elseif $SERVICE->get('cwtperday_rate')}{number_format($SERVICE->get('cwtperday_rate'), 2, '.', '')}{/if}">
												</span>
											</div>
										</div>
									</span>
								{/if}
								</td>
								<td class='fieldLabel'>&nbsp;</td>
								<td class='fieldValue localCWTDayField'>&nbsp;</td>
							</tr>
						{/if}
					{elseif $RATE_TYPE eq "Per CWT/Per Month"}
						<tr>
							<td class='fieldLabel'>
								<label class="muted pull-right marginRight10px">Weight</label>
							</td>
							<td class='fieldValue localCWTMonthField'>
							{assign var=FIELDNAME value='Weight'|cat:$ID}
							{if $SERVICE->get(is_required)}{$LOCALFIELDINFO.mandatory = true}{else}{$LOCALFIELDINFO.mandatory = false}{/if}
							{$LOCALFIELDINFO.name = $FIELDNAME}
							{$LOCALFIELDINFO.label = 'Weight'}
							{$LOCALFIELDINFO.type = 'integer'}
							{$INFO = Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($LOCALFIELDINFO))}
							<span class="value">{(int) $SERVICE_DETAILS.weight}</span>
							{if $EDITABLE === TRUE}
								<span class="hide edit">
									<input name="Weight{$ID}" class="input-large nameField" type="number" value="{(int) $SERVICE_DETAILS.weight}" data-validation-engine="validate[{if $SERVICE->get(is_required)}required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" data-fieldinfo="{$INFO}" />
									<input type="hidden" class="fieldname" value="Weight{$ID}" data-prev-value="{(int) $SERVICE_DETAILS.weight}">
								</span>
							{/if}
							</td>
							<td class='fieldLabel'>
								<label class="muted pull-right marginRight10px">Months</label>
							</td>
							<td class='fieldValue localCWTMonthField'>
							{assign var=FIELDNAME value='Months'|cat:$ID}
							{if $SERVICE->get(is_required)}{$LOCALFIELDINFO.mandatory = true}{else}{$LOCALFIELDINFO.mandatory = false}{/if}
							{$LOCALFIELDINFO.name = $FIELDNAME}
							{$LOCALFIELDINFO.label = 'Months'}
							{$LOCALFIELDINFO.type = 'integer'}
							{$INFO = Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($LOCALFIELDINFO))}
							<span class="value">{(int) $SERVICE_DETAILS.months}</span>
							{if $EDITABLE === TRUE}
								<span class="hide edit">
									<input name="Months{$ID}" class="input-large nameField" type="number" value="{(int) $SERVICE_DETAILS.months}" data-validation-engine="validate[{if $SERVICE->get(is_required)}required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" data-fieldinfo="{$INFO}" />
									<input type="hidden" class="fieldname" value="Months{$ID}" data-prev-value="{(int) $SERVICE_DETAILS.months}">
								</span>
							{/if}
							</td>
						</tr>
						{if $SHOW_RATES}
						<tr>
							<td class='fieldLabel'>
								<label class="muted pull-right marginRight10px">Rate</label>
							</td>
							<td class='fieldValue localCWTMonthField'>
							{assign var=FIELDNAME value='Rate'|cat:$ID}
							{if $SERVICE->get(is_required)}{$LOCALFIELDINFO.mandatory = true}{else}{$LOCALFIELDINFO.mandatory = false}{/if}
							{$LOCALFIELDINFO.name = $FIELDNAME}
							{$LOCALFIELDINFO.label = 'Rate'}
							{$LOCALFIELDINFO.type = 'currency'}
							{$INFO = Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($LOCALFIELDINFO))}
							<span class="value">{if !empty($SERVICE_DETAILS.rate)}{number_format($SERVICE_DETAILS.rate, 2, '.', '')}{elseif $SERVICE->get('cwtpermonth_rate')}{number_format($SERVICE->get('cwtpermonth_rate'), 2, '.', '')}{/if}</span>
							{if $EDITABLE === TRUE}
								<span class="hide edit">
									<div class="row-fluid">
										<div class="input-prepend">
											<span class="span10">
												<span class="add-on">&#36;</span>
												<input name="Rate{$ID}" class="input-medium currencyField" type="number" value="{if !empty($SERVICE_DETAILS.rate)}{number_format($SERVICE_DETAILS.rate, 2, '.', '')}{elseif $SERVICE->get('cwtpermonth_rate')}{number_format($SERVICE->get('cwtpermonth_rate'), 2, '.', '')}{/if}" data-decimal-seperator="." data-group-seperator="," data-number-of-decimal-places="2" data-validation-engine="validate[{if $SERVICE->get(is_required)}required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" data-fieldinfo="{$INFO}" />
												<input type="hidden" class="fieldname" value="Rate{$ID}" data-prev-value="{if !empty($SERVICE_DETAILS.rate)}{number_format($SERVICE_DETAILS.rate, 2, '.', '')}{elseif $SERVICE->get('cwtpermonth_rate')}{number_format($SERVICE->get('cwtpermonth_rate'), 2, '.', '')}{/if}">
											</span>
										</div>
									</div>
								</span>
							{/if}
							</td>

							<td class='fieldLabel'>&nbsp;</td>
							<td class='fieldValue localCWTMonthField'>&nbsp;</td>
						</tr>
						{/if}
					{elseif $RATE_TYPE eq "Per Quantity"}
						<tr>
							<td class='fieldLabel'>
								<label class="muted pull-right marginRight10px">Quantity</label>
							</td>
							<td class='fieldValue localPerQtyField'>
							{assign var=FIELDNAME value='Quantity'|cat:$ID}
							{if $SERVICE->get(is_required)}{$LOCALFIELDINFO.mandatory = true}{else}{$LOCALFIELDINFO.mandatory = false}{/if}
							{$LOCALFIELDINFO.name = $FIELDNAME}
							{$LOCALFIELDINFO.label = 'Quantity'}
							{$LOCALFIELDINFO.type = 'integer'}
							{$INFO = Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($LOCALFIELDINFO))}
							<span class="value">{if $SERVICE_DETAILS.quantity}{number_format($SERVICE_DETAILS.quantity, 0, '.', '')}{/if}</span>
							{if $EDITABLE === TRUE}
								<span class="hide edit">
									<input name="Quantity{$ID}" class="input-large nameField" type="number" value="{if $SERVICE_DETAILS.quantity}{number_format($SERVICE_DETAILS.quantity, 0, '.', '')}{/if}" data-validation-engine="validate[{if $SERVICE->get(is_required)}required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" data-fieldinfo="{$INFO}" />
									<input type="hidden" class="fieldname" value="Quantity{$ID}" data-prev-value="{if $SERVICE_DETAILS.quantity}{number_format($SERVICE_DETAILS.quantity, 0, '.', '')}{/if}">
								</span>
							{/if}
							</td>
							{if $SHOW_RATES}
							<td class='fieldLabel'>
								<label class="muted pull-right marginRight10px">Rate</label>
							</td>
							<td class='fieldValue localPerQtyField'>
							{assign var=FIELDNAME value='Rate'|cat:$ID}
							{if $SERVICE->get(is_required)}{$LOCALFIELDINFO.mandatory = true}{else}{$LOCALFIELDINFO.mandatory = false}{/if}
							{$LOCALFIELDINFO.name = $FIELDNAME}
							{$LOCALFIELDINFO.label = 'Rate'}
							{$LOCALFIELDINFO.type = 'currency'}
							{$INFO = Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($LOCALFIELDINFO))}
							<span class="value">{if !empty($SERVICE_DETAILS.rate)}{number_format($SERVICE_DETAILS.rate, 2, '.', '')}{elseif $SERVICE->get('qty_rate')}{number_format($SERVICE->get('qty_rate'), 2, '.', '')}{/if}</span>
							{if $EDITABLE === TRUE}
								<span class="hide edit">
									<div class="row-fluid">
										<div class="input-prepend">
											<span class="span10">
												<span class="add-on">&#36;</span>
												<input name="Rate{$ID}" class="input-medium currencyField" type="number" value="{if !empty($SERVICE_DETAILS.rate)}{number_format($SERVICE_DETAILS.rate, 2, '.', '')}{elseif $SERVICE->get('qty_rate')}{number_format($SERVICE->get('qty_rate'), 2, '.', '')}{/if}" data-decimal-seperator="." data-group-seperator="," data-number-of-decimal-places="2" data-validation-engine="validate[{if $SERVICE->get(is_required)}required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" data-fieldinfo="{$INFO}" />
												<input type="hidden" class="fieldname" value="Rate{$ID}" data-prev-value="{if !empty($SERVICE_DETAILS.rate)}{number_format($SERVICE_DETAILS.rate, 2, '.', '')}{elseif $SERVICE->get('qty_rate')}{number_format($SERVICE->get('qty_rate'), 2, '.', '')}{/if}">
											</span>
										</div>
									</div>
								</span>
							{/if}
							</td>
							{else}
								<td class='fieldLabel'>&nbsp;</td>
								<td class='fieldValue'>&nbsp;</td>
							{/if}
						</tr>
					{elseif $RATE_TYPE eq "Per Quantity/Per Day"}
						<tr>
							<td class='fieldLabel'>
								<label class="muted pull-right marginRight10px">Quantity</label>
							</td>
							<td class='fieldValue localPerQtyDayField'>
							{assign var=FIELDNAME value='Quantity'|cat:$ID}
							{if $SERVICE->get(is_required)}{$LOCALFIELDINFO.mandatory = true}{else}{$LOCALFIELDINFO.mandatory = false}{/if}
							{$LOCALFIELDINFO.name = $FIELDNAME}
							{$LOCALFIELDINFO.label = 'Quantity'}
							{$LOCALFIELDINFO.type = 'integer'}
							{$INFO = Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($LOCALFIELDINFO))}
							<span class="value">{if $SERVICE_DETAILS.quantity}{number_format($SERVICE_DETAILS.quantity, 0, '.', '')}{/if}</span>
							{if $EDITABLE === TRUE}
								<span class="hide edit">
									<input name="Quantity{$ID}" class="input-large nameField" type="number" value="{if $SERVICE_DETAILS.quantity}{number_format($SERVICE_DETAILS.quantity, 0, '.', '')}{/if}" data-validation-engine="validate[{if $SERVICE->get(is_required)}required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" data-fieldinfo="{$INFO}" />
									<input type="hidden" class="fieldname" value="Quantity{$ID}" data-prev-value="{if $SERVICE_DETAILS.quantity}{number_format($SERVICE_DETAILS.quantity, 0, '.', '')}{/if}">
								</span>
							{/if}
							</td>
							<td class='fieldLabel'>
								<label class="muted pull-right marginRight10px">Days</label>
							</td>
							<td class='fieldValue localPerQtyDayField'>
							{assign var=FIELDNAME value='Days'|cat:$ID}
							{if $SERVICE->get(is_required)}{$LOCALFIELDINFO.mandatory = true}{else}{$LOCALFIELDINFO.mandatory = false}{/if}
							{$LOCALFIELDINFO.name = $FIELDNAME}
							{$LOCALFIELDINFO.label = 'Days'}
							{$LOCALFIELDINFO.type = 'integer'}
							{$INFO = Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($LOCALFIELDINFO))}
							<span class="value">{if $SERVICE_DETAILS.days}{number_format($SERVICE_DETAILS.days, 0, '.', '')}{/if}</span>
							{if $EDITABLE === TRUE}
								<span class="hide edit">
									<input name="Days{$ID}" class="input-large nameField" type="number" value="{if $SERVICE_DETAILS.days}{number_format($SERVICE_DETAILS.days, 0, '.', '')}{/if}" data-validation-engine="validate[{if $SERVICE->get(is_required)}required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" data-fieldinfo="{$INFO}" />
									<input type="hidden" class="fieldname" value="Days{$ID}" data-prev-value="{if $SERVICE_DETAILS.days}{number_format($SERVICE_DETAILS.days, 0, '.', '')}{/if}">
								</span>
							{/if}
							</td>
						</tr>
						{if $SHOW_RATES}
						<tr>
							<td class='fieldLabel'>
								<label class="muted pull-right marginRight10px">Rate</label>
							</td>
							<td class='fieldValue localPerQtyDayField'>
							{assign var=FIELDNAME value='Rate'|cat:$ID}
							{if $SERVICE->get(is_required)}{$LOCALFIELDINFO.mandatory = true}{else}{$LOCALFIELDINFO.mandatory = false}{/if}
							{$LOCALFIELDINFO.name = $FIELDNAME}
							{$LOCALFIELDINFO.label = 'Rate'}
							{$LOCALFIELDINFO.type = 'currency'}
							{$INFO = Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($LOCALFIELDINFO))}
							<span class="value">{if !empty($SERVICE_DETAILS.rate)}{number_format($SERVICE_DETAILS.rate, 2, '.', '')}{elseif $SERVICE->get('qtyperday_rate')}{number_format($SERVICE->get('qtyperday_rate'), 2, '.', '')}{/if}</span>
							{if $EDITABLE === TRUE}
								<span class="hide edit">
									<div class="row-fluid">
										<div class="input-prepend">
											<span class="span10">
												<span class="add-on">&#36;</span>
												<input name="Rate{$ID}" class="input-medium currencyField" type="number" value="{if !empty($SERVICE_DETAILS.rate)}{number_format($SERVICE_DETAILS.rate, 2, '.', '')}{elseif $SERVICE->get('qtyperday_rate')}{number_format($SERVICE->get('qtyperday_rate'), 2, '.', '')}{/if}" data-decimal-seperator="." data-group-seperator="," data-number-of-decimal-places="2" data-validation-engine="validate[{if $SERVICE->get(is_required)}required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" data-fieldinfo="{$INFO}" />
												<input type="hidden" class="fieldname" value="Rate{$ID}" data-prev-value="{if !empty($SERVICE_DETAILS.rate)}{number_format($SERVICE_DETAILS.rate, 2, '.', '')}{elseif $SERVICE->get('qtyperday_rate')}{number_format($SERVICE->get('qtyperday_rate'), 2, '.', '')}{/if}">
											</span>
										</div>
									</div>
								</span>
							{/if}
							</td>
							<td class='fieldLabel'>&nbsp;</td>
							<td class='fieldValue localPerQtyDayField'>&nbsp;</td>
						</tr>
						{/if}
					{elseif $RATE_TYPE eq "Per Quantity/Per Month"}
						<tr>
							<td class='fieldLabel'>
								<label class="muted pull-right marginRight10px">Quantity</label>
							</td>
							<td class='fieldValue localPerQtyMonthField'>
							{assign var=FIELDNAME value='Quantity'|cat:$ID}
							{if $SERVICE->get(is_required)}{$LOCALFIELDINFO.mandatory = true}{else}{$LOCALFIELDINFO.mandatory = false}{/if}
							{$LOCALFIELDINFO.name = $FIELDNAME}
							{$LOCALFIELDINFO.label = 'Quantity'}
							{$LOCALFIELDINFO.type = 'integer'}
							{$INFO = Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($LOCALFIELDINFO))}
							<span class="value">{if $SERVICE_DETAILS.quantity}{number_format($SERVICE_DETAILS.quantity, 0, '.', '')}{/if}</span>
							{if $EDITABLE === TRUE}
								<span class="hide edit">
									<input name="Quantity{$ID}" class="input-large nameField" type="number" value="{if $SERVICE_DETAILS.quantity}{number_format($SERVICE_DETAILS.quantity, 0, '.', '')}{/if}" data-validation-engine="validate[{if $SERVICE->get(is_required)}required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" data-fieldinfo="{$INFO}" />
									<input type="hidden" class="fieldname" value="Quantity{$ID}" data-prev-value="{if $SERVICE_DETAILS.quantity}{number_format($SERVICE_DETAILS.quantity, 0, '.', '')}{/if}">
								</span>
							{/if}
							</td>
							<td class='fieldLabel'>
								<label class="muted pull-right marginRight10px">Months</label>
							</td>
							<td class='fieldValue localPerQtyMonthField'>
							{assign var=FIELDNAME value='Months'|cat:$ID}
							{if $SERVICE->get(is_required)}{$LOCALFIELDINFO.mandatory = true}{else}{$LOCALFIELDINFO.mandatory = false}{/if}
							{$LOCALFIELDINFO.name = $FIELDNAME}
							{$LOCALFIELDINFO.label = 'Months'}
							{$LOCALFIELDINFO.type = 'integer'}
							{$INFO = Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($LOCALFIELDINFO))}
							<span class="value">{if $SERVICE_DETAILS.months}{number_format($SERVICE_DETAILS.months, 0, '.', '')}{/if}</span>
							{if $EDITABLE === TRUE}
								<span class="hide edit">
									<input name="Months{$ID}" class="input-large nameField" type="number" value="{if $SERVICE_DETAILS.months}{number_format($SERVICE_DETAILS.months, 0, '.', '')}{/if}" data-validation-engine="validate[{if $SERVICE->get(is_required)}required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" data-fieldinfo="{$INFO}" />
									<input type="hidden" class="fieldname" value="Months{$ID}" data-prev-value="{if $SERVICE_DETAILS.months}{number_format($SERVICE_DETAILS.months, 0, '.', '')}{/if}">
								</span>
							{/if}
							</td>
						</tr>
						{if $SHOW_RATES}
						<tr>
							<td class='fieldLabel'>
								<label class="muted pull-right marginRight10px">Rate</label>
							</td>
							<td class='fieldValue localPerQtyMonthField'>
							{assign var=FIELDNAME value='Rate'|cat:$ID}
							{if $SERVICE->get(is_required)}{$LOCALFIELDINFO.mandatory = true}{else}{$LOCALFIELDINFO.mandatory = false}{/if}
							{$LOCALFIELDINFO.name = $FIELDNAME}
							{$LOCALFIELDINFO.label = 'Rate'}
							{$LOCALFIELDINFO.type = 'currency'}
							{$INFO = Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($LOCALFIELDINFO))}
							<span class="value">{if !empty($SERVICE_DETAILS.rate)}{number_format($SERVICE_DETAILS.rate, 2, '.', '')}{elseif $SERVICE->get('qtypermonth_rate')}{number_format($SERVICE->get('qtypermonth_rate'), 2, '.', '')}{/if}</span>
							{if $EDITABLE === TRUE}
								<span class="hide edit">
									<div class="row-fluid">
										<div class="input-prepend">
											<span class="span10">
												<span class="add-on">&#36;</span>
												<input name="Rate{$ID}" class="input-medium currencyField" type="number" value="{if !empty($SERVICE_DETAILS.rate)}{number_format($SERVICE_DETAILS.rate, 2, '.', '')}{elseif $SERVICE->get('qtypermonth_rate')}{number_format($SERVICE->get('qtypermonth_rate'), 2, '.', '')}{/if}" data-decimal-seperator="." data-group-seperator="," data-number-of-decimal-places="2" data-validation-engine="validate[{if $SERVICE->get(is_required)}required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" data-fieldinfo="{$INFO}" />
												<input type="hidden" class="fieldname" value="Rate{$ID}" data-prev-value="{if !empty($SERVICE_DETAILS.rate)}{number_format($SERVICE_DETAILS.rate, 2, '.', '')}{elseif $SERVICE->get('qtypermonth_rate')}{number_format($SERVICE->get('qtypermonth_rate'), 2, '.', '')}{/if}">
											</span>
										</div>
									</div>
								</span>
							{/if}
							</td>
							<td class='fieldLabel'>&nbsp;</td>
							<td class='fieldValue localPerQtyMonthField'>&nbsp;</td>
						</tr>
						{/if}
					{elseif $RATE_TYPE eq "Charge Per $100 (Valuation)"}
						<tr>
							<td class='fieldLabel'>
								<label class="muted pull-right marginRight10px">Valuation Type</label>
							</td>
							<td class='fieldValue localValuationField'>
							{assign var=FIELDNAME value='ValuationType'|cat:$ID}
							{if $SERVICE->get(is_required)}{$LOCALFIELDINFO.mandatory = true}{else}{$LOCALFIELDINFO.mandatory = false}{/if}
							{$LOCALFIELDINFO.name = $FIELDNAME}
							{$LOCALFIELDINFO.label = 'Valuation Type'}
							{$LOCALFIELDINFO.type = 'picklist'}
							{$INFO = Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($LOCALFIELDINFO))}
							<span class="value">{if $SERVICE_DETAILS.released == 2}Select an Option{elseif $SERVICE_DETAILS.released == 1}Released Valuation{elseif $SERVICE_DETAILS.released === '0'}Full Valuation{else}Select an Option{/if}</span>
							<span class="hide{if $EDITABLE === TRUE} edit{/if}">
								<!--released: {$SERVICE_DETAILS.released}-->
								<select class="chzn-select" name="ValuationType{$ID}" class ="localValuationType" data-validation-engine="validate[{if $SERVICE->get(is_required)}required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" data-fieldinfo="{$INFO}">
									<option value="2" selected>{vtranslate('LBL_SELECT_OPTION','Vtiger')}</option>
									{if $SERVICE->get(valuation_released) === '1'}
										<option value="1" {if $SERVICE_DETAILS.released eq 1}selected{/if}>Released Valuation</option>
									{/if}
									<option value="0" {if $SERVICE_DETAILS.released === '0'}selected{/if}>Full Valuation</option>
								</select>
								<input type="hidden" class="fieldname" value="ValuationType{$ID}" data-prev-value="{$SERVICE_DETAILS.released}">
							</span>
							</td>
							{* ??? <td class='fieldLabel noValuation{$ID}'>&nbsp;</td>
							<td class='fieldValue noValuation{$ID}'>&nbsp;</td>*}
                            {if $SERVICE_DETAILS.released === '1'}
    							<td class='fieldLabel releasedValuation{$ID}'>
    								<label class="muted pull-right marginRight10px">Coverage Per Lb.</label>
    							</td>
    							<td class='fieldValue localValuationField releasedValuation{$ID}'>
    							{assign var=FIELDNAME value='Coverage'|cat:$ID}
    							{if $SERVICE->get(is_required)}{$LOCALFIELDINFO.mandatory = true}{else}{$LOCALFIELDINFO.mandatory = false}{/if}
    							{$LOCALFIELDINFO.name = $FIELDNAME}
    							{$LOCALFIELDINFO.label = 'Coverage Per Lb.'}
    							{$LOCALFIELDINFO.type = 'currency'}
    							{$INFO = Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($LOCALFIELDINFO))}
    							<span class="value">{if !empty($SERVICE_DETAILS.released_amount)}{number_format($SERVICE_DETAILS.released_amount, 2, '.', '')}{elseif $SERVICE->get(valuation_releasedamount)}{number_format($SERVICE->get(valuation_releasedamount), 2, '.', '')}{/if}</span>
    							{if $EDITABLE === TRUE}
    								<span class="hide edit">
    									<div class="row-fluid">
    										<div class="input-prepend">
    											<span class="span10">
    												<span class="add-on">&#36;</span>
    												<input name="Coverage{$ID}" class="input-medium currencyField" type="number" value="{if !empty($SERVICE_DETAILS.released_amount)}{number_format($SERVICE_DETAILS.released_amount, 2, '.', '')}{elseif $SERVICE->get(valuation_releasedamount)}{number_format($SERVICE->get(valuation_releasedamount), 2, '.', '')}{/if}" data-decimal-seperator="." data-group-seperator="," data-number-of-decimal-places="2" step=".05" data-validation-engine="validate[{if $SERVICE->get(is_required)}required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" data-fieldinfo="{$INFO}" />
    												<input type="hidden" class="fieldname" value="Coverage{$ID}" data-prev-value="{if !empty($SERVICE_DETAILS.released_amount)}{number_format($SERVICE_DETAILS.released_amount, 2, '.', '')}{elseif $SERVICE->get(valuation_releasedamount)}{number_format($SERVICE->get(valuation_releasedamount), 2, '.', '')}{/if}">
    											</span>
    										</div>
    									</div>
    								</span>
    							{/if}
    							</td>
                            </tr>
							{elseif $SERVICE_DETAILS.released === '0'}
								<td class='fieldLabel'>&nbsp;</td>
								<td class='fieldValue'>&nbsp;</td>
						</tr>
						<tr class="fullValuation{$ID}">
							<td class='fieldLabel'>
								<label class="muted pull-right marginRight10px">Valuation Amount</label>
							</td>
							<td class='fieldValue localChargePerHundredField'>
							{assign var=FIELDNAME value='Amount'|cat:$ID}
							{if $SERVICE->get(is_required)}{$LOCALFIELDINFO.mandatory = true}{else}{$LOCALFIELDINFO.mandatory = false}{/if}
							{$LOCALFIELDINFO.name = $FIELDNAME}
							{$LOCALFIELDINFO.label = 'Amount'}
							{$LOCALFIELDINFO.type = 'currency'}
							{$INFO = Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($LOCALFIELDINFO))}
							<span class="value">{if $SERVICE_DETAILS.amount}{number_format($SERVICE_DETAILS.amount, 2, '.', '')}{/if}</span>
							{if $EDITABLE === TRUE}
								<span class="hide edit">
									<div class="row-fluid">
										<div class="input-prepend">
											<span class="span10">
												<span class="add-on">&#36;</span>
												<input name="Amount{$ID}" class="input-medium currencyField" type="number" value="{if $SERVICE_DETAILS.amount}{number_format($SERVICE_DETAILS.amount, 2, '.', '')}{/if}" data-decimal-seperator="." data-group-seperator="," data-number-of-decimal-places="2" data-validation-engine="validate[{if $SERVICE->get(is_required)}required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" data-fieldinfo="{$INFO}" />
												<input type="hidden" class="fieldname" value="Amount{$ID}" data-prev-value="{if $SERVICE_DETAILS.amount}{number_format($SERVICE_DETAILS.amount, 2, '.', '')}{/if}">
											</span>
										</div>
									</div>
								</span>
							{/if}
							</td>
							<td class='fieldLabel'>
								<label class="muted pull-right marginRight10px">Deductible</label>
							</td>
							<td class='fieldValue localChargePerHundredField'>
							{assign var=FIELDNAME value='Deductible'|cat:$ID}
							{if $SERVICE->get(is_required)}{$LOCALFIELDINFO.mandatory = true}{else}{$LOCALFIELDINFO.mandatory = false}{/if}
							{$LOCALFIELDINFO.name = $FIELDNAME}
							{$LOCALFIELDINFO.label = 'Deductible'}
							{$LOCALFIELDINFO.type = 'picklist'}
							{$INFO = Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($LOCALFIELDINFO))}
								{assign var=PICKLIST_VALUES value=$SERVICE->getDeductiblePicklists()}
								<span class="value">{if !isset($SERVICE_DETAILS.deductible)}Select an Option{else}{$SERVICE_DETAILS.deductible}{/if}</span>
								{if $EDITABLE === TRUE}
									<span class="hide edit">
										<select class="chzn-select" name="Deductible{$ID}" class ="localDeductiblePick" data-validation-engine="validate[{if $SERVICE->get(is_required)}required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" data-fieldinfo="{$INFO}">
											<option value="">{vtranslate('LBL_SELECT_OPTION','Vtiger')}</option>
											{foreach item=PICKLIST_VALUE key=PICKLIST_NAME from=$PICKLIST_VALUES}
												<option value="{$PICKLIST_VALUE}" {if ($SERVICE_DETAILS.deductible == $PICKLIST_VALUE)}selected{/if}>{$PICKLIST_VALUE}</option>
											{/foreach}
										</select>
										<input type="hidden" class="fieldname" value="Deductible{$ID}" data-prev-value="{$SERVICE_DETAILS.deductible}">
									</span>
								{/if}
							</td>
						</tr>
						{if $SHOW_RATES}
						<tr class="fullValuation{$ID}">
							<td class='fieldLabel'>
								<label class="muted pull-right marginRight10px">Rate per pound for Min Declared Valuation</label>
							</td>
							<td class='fieldValue localChargePerHundredField'>
							{assign var=FIELDNAME value='Multiplier'|cat:$ID}
							{if $SERVICE->get(is_required)}{$LOCALFIELDINFO.mandatory = true}{else}{$LOCALFIELDINFO.mandatory = false}{/if}
							{$LOCALFIELDINFO.name = $FIELDNAME}
							{$LOCALFIELDINFO.label = 'Multiplier'}
							{$LOCALFIELDINFO.type = 'currency'}
							{$INFO = Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($LOCALFIELDINFO))}
							<span class="value">{if !empty($SERVICE_DETAILS.multiplier)}{number_format($SERVICE_DETAILS.multiplier, 2, '.', '')}{/if}</span>
							{if $EDITABLE === TRUE}
								<span class="hide edit">
									<div class="row-fluid">
										<div class="input-prepend">
											<span class="span10">
												<span class="add-on">&#36;</span>
												<input name="Multiplier{$ID}" class="input-medium currencyField" type="number" value="{if !empty($SERVICE_DETAILS.multiplier)}{number_format($SERVICE_DETAILS.multiplier, 2, '.', '')}{/if}" data-decimal-seperator="." data-group-seperator="," data-number-of-decimal-places="2" data-validation-engine="validate[{if $SERVICE->get(is_required)}required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" data-fieldinfo="{$INFO}" />
												<input type="hidden" class="fieldname" value="Multiplier{$ID}" data-prev-value="{if !empty($SERVICE_DETAILS.multiplier)}{number_format($SERVICE_DETAILS.multiplier, 2, '.', '')}{/if}">
											</span>
										</div>
									</div>
								</span>
							{/if}
							</td>
							<td class='fieldLabel'>
								<label class="muted pull-right marginRight10px">Rate</label>
							</td>
							<td class='fieldValue localChargePerHundredField'>
							{assign var=FIELDNAME value='Rate'|cat:$ID}
							{if $SERVICE->get(is_required)}{$LOCALFIELDINFO.mandatory = true}{else}{$LOCALFIELDINFO.mandatory = false}{/if}
							{$LOCALFIELDINFO.name = $FIELDNAME}
							{$LOCALFIELDINFO.label = 'Rate'}
							{$LOCALFIELDINFO.type = 'currency'}
							{$INFO = Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($LOCALFIELDINFO))}
							<span class="value">{if !empty($SERVICE_DETAILS.rate)}{number_format($SERVICE_DETAILS.rate, 2, '.', '')}{/if}</span>
							{if $EDITABLE === TRUE}
								<span class="hide edit">
									<div class="row-fluid">
										<div class="input-prepend">
											<span class="span10">
												<span class="add-on">&#36;</span>
												<input name="Rate{$ID}" class="input-medium currencyField" type="number" value="{if !empty($SERVICE_DETAILS.rate)}{number_format($SERVICE_DETAILS.rate, 2, '.', '')}{/if}" data-decimal-seperator="." data-group-seperator="," data-number-of-decimal-places="2" data-validation-engine="validate[{if $SERVICE->get(is_required)}required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" data-fieldinfo="{$INFO}" />
												<input type="hidden" class="fieldname" value="Rate{$ID}" data-prev-value="{if !empty($SERVICE_DETAILS.rate)}{number_format($SERVICE_DETAILS.rate, 2, '.', '')}{/if}">
											</span>
										</div>
									</div>
								</span>
							{/if}
							</td>
						</tr>
						{/if}{/if}
					{elseif $RATE_TYPE eq "Tabled Valuation"}
						<tr>
							<td class='fieldLabel'>
								<label class="muted pull-right marginRight10px">Valuation Type</label>
							</td>
							<td class='fieldValue localValuationField'>
							{assign var=FIELDNAME value='ValuationType'|cat:$ID}
							{if $SERVICE->get(is_required)}{$LOCALFIELDINFO.mandatory = true}{else}{$LOCALFIELDINFO.mandatory = false}{/if}
							{$LOCALFIELDINFO.name = $FIELDNAME}
							{$LOCALFIELDINFO.label = 'Valuation Type'}
							{$LOCALFIELDINFO.type = 'picklist'}
							{$INFO = Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($LOCALFIELDINFO))}
							<span class="value">{if $SERVICE_DETAILS.released == 2}Select an Option{elseif $SERVICE_DETAILS.released == 1}Released Valuation{elseif $SERVICE_DETAILS.released === '0'}Full Valuation{else}Select an Option{/if}</span>
							<span class="hide{if $EDITABLE === TRUE} edit{/if}">
								<!--released: {$SERVICE_DETAILS.released}-->
								<select class="chzn-select" name="ValuationType{$ID}" class ="localValuationType" data-validation-engine="validate[{if $SERVICE->get(is_required)}required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" data-fieldinfo="{$INFO}">
									<option value="2" selected>{vtranslate('LBL_SELECT_OPTION','Vtiger')}</option>
									{if $SERVICE->get(valuation_released) === '1'}
										<option value="1" {if $SERVICE_DETAILS.released eq 1}selected{/if}>Released Valuation</option>
									{/if}
									<option value="0" {if $SERVICE_DETAILS.released === '0'}selected{/if}>Full Valuation</option>
								</select>
								<input type="hidden" class="fieldname" value="ValuationType{$ID}" data-prev-value="{$SERVICE_DETAILS.released}">
							</span>
							</td>
							{* ??? <td class='fieldLabel noValuation{$ID}'>&nbsp;</td>
							<td class='fieldValue noValuation{$ID}'>&nbsp;</td>*}
                            {if $SERVICE_DETAILS.released === '1'}
    							<td class='fieldLabel releasedValuation{$ID}'>
    								<label class="muted pull-right marginRight10px">Coverage Per Lb.</label>
    							</td>
    							<td class='fieldValue localValuationField releasedValuation{$ID}'>
    							{assign var=FIELDNAME value='Coverage'|cat:$ID}
    							{if $SERVICE->get(is_required)}{$LOCALFIELDINFO.mandatory = true}{else}{$LOCALFIELDINFO.mandatory = false}{/if}
    							{$LOCALFIELDINFO.name = $FIELDNAME}
    							{$LOCALFIELDINFO.label = 'Coverage Per Lb.'}
    							{$LOCALFIELDINFO.type = 'currency'}
    							{$INFO = Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($LOCALFIELDINFO))}
    							<span class="value">{if !empty($SERVICE_DETAILS.released_amount)}{number_format($SERVICE_DETAILS.released_amount, 2, '.', '')}{elseif $SERVICE->get(valuation_releasedamount)}{number_format($SERVICE->get(valuation_releasedamount), 2, '.', '')}{/if}</span>
    							{if $EDITABLE === TRUE}
    								<span class="hide edit">
    									<div class="row-fluid">
    										<div class="input-prepend">
    											<span class="span10">
    												<span class="add-on">&#36;</span>
    												<input name="Coverage{$ID}" class="input-medium currencyField" type="number" value="{if !empty($SERVICE_DETAILS.released_amount)}{number_format($SERVICE_DETAILS.released_amount, 2, '.', '')}{elseif $SERVICE->get(valuation_releasedamount)}{number_format($SERVICE->get(valuation_releasedamount), 2, '.', '')}{/if}" data-decimal-seperator="." data-group-seperator="," data-number-of-decimal-places="2" step=".05" data-validation-engine="validate[{if $SERVICE->get(is_required)}required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" data-fieldinfo="{$INFO}" />
    												<input type="hidden" class="fieldname" value="Coverage{$ID}" data-prev-value="{if !empty($SERVICE_DETAILS.released_amount)}{number_format($SERVICE_DETAILS.released_amount, 2, '.', '')}{elseif $SERVICE->get(valuation_releasedamount)}{number_format($SERVICE->get(valuation_releasedamount), 2, '.', '')}{/if}">
    											</span>
    										</div>
    									</div>
    								</span>
    							{/if}
    							</td>
							{elseif $SERVICE_DETAILS.released === '0'}
								<td class='fieldLabel fullValuation{$ID}'>
									<label class="muted pull-right marginRight10px">Rate</label>
								</td>
								<td class='fieldValue localValuationField fullValuation{$ID}'>
								{assign var=FIELDNAME value='Rate'|cat:$ID}
								{if $SERVICE->get(is_required)}{$LOCALFIELDINFO.mandatory = true}{else}{$LOCALFIELDINFO.mandatory = false}{/if}
								{$LOCALFIELDINFO.name = $FIELDNAME}
								{$LOCALFIELDINFO.label = 'Rate'}
								{$LOCALFIELDINFO.type = 'currency'}
								{$INFO = Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($LOCALFIELDINFO))}
								<span class="value">{if !empty($SERVICE_DETAILS.rate)}{number_format($SERVICE_DETAILS.rate, 2, '.', '')}{/if}</span>
								{if $EDITABLE === TRUE}
									<span class="hide edit">
										<div class="row-fluid">
											<div class="input-prepend">
												<span class="span10">
													<span class="add-on">&#36;</span>
													<input name="Rate{$ID}" class="input-medium currencyField" type="number" value="{if !empty($SERVICE_DETAILS.rate)}{number_format($SERVICE_DETAILS.rate, 2, '.', '')}{/if}" data-decimal-seperator="." data-group-seperator="," data-number-of-decimal-places="2" data-validation-engine="validate[{if $SERVICE->get(is_required)}required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" data-fieldinfo="{$INFO}" />
													<input type="hidden" class="fieldname" value="Rate{$ID}" data-prev-value="{if !empty($SERVICE_DETAILS.rate)}{number_format($SERVICE_DETAILS.rate, 2, '.', '')}{/if}">
												</span>
											</div>
										</div>
									</span>
								{/if}
								</td>
							{else}
								<td class='fieldLabel'>&nbsp;</td>
								<td class='fieldValue'>&nbsp;</td>
							{/if}
						</tr>
						<tr class ="fullValuation{$ID} {if $SERVICE_DETAILS.released !== '0'}hide{/if}">
							<td class='fieldLabel'>
								<label class="muted pull-right marginRight10px">Deductible</label>
							</td>
							<td class='fieldValue localValuationField localValuationField'>
							{assign var=FIELDNAME value='Deductible'|cat:$ID}
							{if $SERVICE->get(is_required)}{$LOCALFIELDINFO.mandatory = true}{else}{$LOCALFIELDINFO.mandatory = false}{/if}
							{$LOCALFIELDINFO.name = $FIELDNAME}
							{$LOCALFIELDINFO.label = 'Deductible'}
							{$LOCALFIELDINFO.type = 'picklist'}
							{$INFO = Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($LOCALFIELDINFO))}
								{assign var=PICKLIST_VALUES value=$SERVICE->getDistinct('deductible')}
							<span class="value">{if empty($SERVICE_DETAILS.deductible)}Select an Option{else}{$SERVICE_DETAILS.deductible}{/if}</span>
							{if $EDITABLE === TRUE}
								<span class="hide edit">
									<select class="chzn-select localValuationPick valDed" name="Deductible{$ID}" data-validation-engine="validate[{if $SERVICE->get(is_required)}required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" data-fieldinfo="{$INFO}">
										<option value="">{vtranslate('LBL_SELECT_OPTION','Vtiger')}</option>
										{foreach item=PICKLIST_VALUE key=PICKLIST_NAME from=$PICKLIST_VALUES}
											<option value="{$PICKLIST_VALUE}" {if $PICKLIST_VALUE eq $SERVICE_DETAILS.deductible}selected{/if}>{$PICKLIST_VALUE}</option>
										{/foreach}
									</select>
									<input type="hidden" class="fieldname" value="Deductible{$ID}" data-prev-value="{if !empty($SERVICE_DETAILS.deductible)}{$SERVICE_DETAILS.deductible}{else}Select an Option{/if}">
								</span>
							{/if}
							</td>
							<td class='fieldLabel'>
								<label class="muted pull-right marginRight10px">Amount</label>
							</td>
							<td class='fieldValue localValuationField localValuationField'>
							{assign var=FIELDNAME value='Amount'|cat:$ID}
							{if $SERVICE->get(is_required)}{$LOCALFIELDINFO.mandatory = true}{else}{$LOCALFIELDINFO.mandatory = false}{/if}
							{$LOCALFIELDINFO.name = $FIELDNAME}
							{$LOCALFIELDINFO.label = 'Amount'}
							{$LOCALFIELDINFO.type = 'picklist'}
							{$INFO = Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($LOCALFIELDINFO))}
							{assign var=PICKLIST_VALUES value=$SERVICE->getDistinct('amount')}
							<span class="value">{if empty($SERVICE_DETAILS.amount)}Select an Option{else}{$SERVICE_DETAILS.amount}{/if}</span>
							{if $EDITABLE === TRUE}
								<span class="hide edit">
									<select class="chzn-select localValuationPick valAm" name="Amount{$ID}" data-validation-engine="validate[{if $SERVICE->get(is_required)}required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" data-fieldinfo="{$INFO}">
											<option value="">{vtranslate('LBL_SELECT_OPTION','Vtiger')}</option>
											{foreach item=PICKLIST_VALUE key=PICKLIST_NAME from=$PICKLIST_VALUES}
												<option value="{$PICKLIST_VALUE}" {if $PICKLIST_VALUE eq $SERVICE_DETAILS.amount}selected{/if}>{$PICKLIST_VALUE}</option>
											{/foreach}
									</select>
									<input type="hidden" class="fieldname" value="Amount{$ID}" data-prev-value="{$SERVICE_DETAILS.amount}">
								</span>
							{/if}
							</td>
						</tr>
                        <tr class="fullValuation{$ID}">
							<td class='fieldLabel'>
								<label class="muted pull-right marginRight10px">Rate per pound for Min Declared Valuation</label>
							</td>
							<td class='fieldValue localValuationField'>
							{assign var=FIELDNAME value='Multiplier'|cat:$ID}
							{if $SERVICE->get(is_required)}{$LOCALFIELDINFO.mandatory = true}{else}{$LOCALFIELDINFO.mandatory = false}{/if}
							{$LOCALFIELDINFO.name = $FIELDNAME}
							{$LOCALFIELDINFO.label = 'Multiplier'}
							{$LOCALFIELDINFO.type = 'currency'}
							{$INFO = Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($LOCALFIELDINFO))}
							<span class="value">{if !empty($SERVICE_DETAILS.multiplier)}{number_format($SERVICE_DETAILS.multiplier, 2, '.', '')}{/if}</span>
							{if $EDITABLE === TRUE}
								<span class="hide edit">
									<div class="row-fluid">
										<div class="input-prepend">
											<span class="span10">
												<span class="add-on">&#36;</span>
												<input name="Multiplier{$ID}" class="input-medium currencyField" type="number" value="{if !empty($SERVICE_DETAILS.multiplier)}{number_format($SERVICE_DETAILS.multiplier, 2, '.', '')}{/if}" data-decimal-seperator="." data-group-seperator="," data-number-of-decimal-places="2" data-validation-engine="validate[{if $SERVICE->get(is_required)}required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" data-fieldinfo="{$INFO}" />
												<input type="hidden" class="fieldname" value="Multiplier{$ID}" data-prev-value="{if !empty($SERVICE_DETAILS.multiplier)}{number_format($SERVICE_DETAILS.multiplier, 2, '.', '')}{/if}">
											</span>
										</div>
									</div>
								</span>
							{/if}
							</td>
							<td class='fieldLabel'>&nbsp;</td>
							<td class='fieldValue'>&nbsp;</td>
                        </tr>
					{elseif $RATE_TYPE eq "Bulky List"}
						<td class="fluid" colspan="4" style='padding:0;'>
							<table name="Service{$ID}" class="table table-bordered" style='border:none;'>
								<tr>
								{assign var=chargePer value=$SERVICE->get(bulky_chargeper)}
								{assign var=bulkyWidth value=10}
									<td class='fieldLabel' style='width:{$bulkyWidth+10}%;text-align:center;margin:auto'>
										<input type="hidden" class="hide" name="NumBulkys{$ID}" value="{$SERVICE_DETAILS.bulkyList|@count}">
									</td>
									<td class='fieldLabel' style='width:{$bulkyWidth}%;text-align:center;margin:auto'>{($chargePer == 'Hourly')? 'Hours' : 'Quantity'}</td>
									<td class='fieldLabel' style='width:{$bulkyWidth}%;text-align:center;margin:auto'>Weight Additive</td>
									{if $SHOW_RATES}<td class='fieldLabel' style='width:{$bulkyWidth}%;text-align:center;margin:auto'>Rate</td>{/if}
									<td class='fieldLabel' style='{$bulkyWidth+10}%;text-align:center;margin:auto'></td>
									<td class='fieldLabel' style='width:{$bulkyWidth}%;text-align:center;margin:auto'>{($chargePer == 'Hourly')? 'Hours' : 'Quantity'}</td>
									<td class='fieldLabel' style='width:{$bulkyWidth}%;text-align:center;margin:auto'>Weight Additive</td>
									{if $SHOW_RATES}<td class='fieldLabel' style='width:{$bulkyWidth}%;text-align:center;margin:auto'>Rate</td>{/if}
								</tr>

								{assign var=bulkyList value=$SERVICE_DETAILS.bulkyList}
								{if !empty($bulkyList)}
									{foreach item=BULKY key=BULKY_INDEX from=$bulkyList}
										{if ($BULKY_INDEX%2 == 0)}
											<tr>
										{/if}
										<td class='fieldLabel' style='width:{$bulkyWidth}%;'>
											<label class="muted pull-right marginRight10px">{$BULKY[0]}</label>
											<input type="hidden" class="hide" name="bulkyDescription{$ID}-{$BULKY_INDEX}" value="{$BULKY[0]}">
											<input type="hidden" class="hide" name="BulkyID{$ID}-{$BULKY_INDEX}" value="{$BULKY[4]}">
										</td>
										<td class='fieldValue localBulkyField' style='width:{$bulkyWidth}%;'>
											<span class="value">{$BULKY[1]}</span>
											{if $EDITABLE === TRUE}
												<span class="hide edit">
													<input name="Qty{$ID}-{$BULKY_INDEX}" class="input-large nameField" type="number" style="width:85%;text-align:center;margin:auto" {if ($chargePer == 'Hourly')}step=".25"{/if} value="{$BULKY[1]}"/>
													<input type="hidden" class="fieldname" value="Qty{$ID}-{$BULKY_INDEX}" data-prev-value="{$BULKY[1]}">
												</span>
											{/if}
										</td>
										<td class='fieldValue localBulkyField' style='width:{$bulkyWidth}%;'>
											<span class="value">{$BULKY[2]}</span>
											{if $EDITABLE === TRUE}
												<span class="hide edit">
													<input name="WeightAdd{$ID}-{$BULKY_INDEX}" class="input-large nameField" type="number" style="width:85%;text-align:center;margin:auto" value="{$BULKY[2]}" />
													<input type="hidden" class="fieldname" value="WeightAdd{$ID}-{$BULKY_INDEX}" data-prev-value="{$BULKY[2]}">
												</span>
											{/if}
										</td>
										{if $SHOW_RATES}
											<td class='fieldValue localBulkyField' style='width:{$bulkyWidth}%;'>
											<span class="value">{if $BULKY[3]}{number_format($BULKY[3], 2, '.', '')}{/if}</span>
											{if $EDITABLE === TRUE}
												<span class="hide edit">
													<div class="row-fluid">
														<div class="input-prepend">
															<span class="span10">
																<span class="add-on">&#36;</span>
																<input name="Rate{$ID}-{$BULKY_INDEX}"style="width:62%;text-align:center;margin:auto" class="input-small currencyField" type="text" value="{if $BULKY[3]}{number_format($BULKY[3], 2, '.', '')}{/if}" data-decimal-seperator="." data-group-seperator="," data-number-of-decimal-places="2"/>
																<input type="hidden" class="fieldname" value="Rate{$ID}-{$BULKY_INDEX}" data-prev-value="{if $BULKY[3]}{number_format($BULKY[3], 2, '.', '')}{/if}">
															</span>
														</div>
													</div>
												</span>
											{/if}
											</td>
										{/if}
									{/foreach}
									{if ($BULKY_INDEX%2 == 1)}
										</tr>
									{/if}
									{if ($BULKY_INDEX%2 == 0)}
										<td class='fieldLabel' style='width:{$bulkyWidth}%;text-align:center;margin:auto'>&nbsp;</td>
										<td class='fieldLabel' style='width:{$bulkyWidth}%;text-align:center;margin:auto'>&nbsp;</td>
										<td class='fieldLabel' style='width:{$bulkyWidth}%;text-align:center;margin:auto'>&nbsp;</td>
										{if $SHOW_RATES}<td class='fieldLabel' style='width:{$bulkyWidth}%;text-align:center;margin:auto'>&nbsp;</td>{/if}
									{/if}
								{/if}

							</table>
						</td>
						</tr>
					{elseif $RATE_TYPE eq "Packing Items"}
                        <tr>
                            <td class='fieldLabel'>
                                <label class="muted pull-right marginRight10px">Sales Tax</label>
                            </td>
                            <td class="fieldValue section_discount">
                                <span class="value">{$SERVICE_DETAILS.sales_tax}</span>
                            </td>
                            <td class='fieldLabel'>&nbsp;</td>
                            <td class='fieldValue'>&nbsp;</td>
                        </tr>
						<td class="fluid" colspan="4" style='padding:0;'>
							<table name="Service{$ID}" class="table table-bordered" style='border:none;'>
							{assign var=packingList value=$SERVICE->getEntries('packingitems')}
							{assign var=hasContainers value=$SERVICE->get('packing_containers')}
							{assign var=hasPacking value=$SERVICE->get('packing_haspacking')}
							{assign var=hasUnpacking value=$SERVICE->get('packing_hasunpacking')}
							{assign var=numColumns value=(($SHOW_RATES+1)*$hasContainers)+(($SHOW_RATES+1)*$hasPacking)+(($SHOW_RATES+1)*$hasUnpacking)}
							{assign var=perRow value=1}
							{if $numColumns <= 5}
								{assign var=numColumns value=($numColumns*2)+1}
								{assign var=perRow value=2}
							{/if}
							{assign var=numColumns value=($numColumns+1)}
							{assign var=packingWidth value=(100/$numColumns)}
								<tr>
									<td class='fieldLabel' style='width:{$packingWidth}%;text-align:center;margin:auto'>
										<input type="hidden" class="hide" name="numPacking{$ID}" value="{$SERVICE_DETAILS.packingList|@count}">
									</td>

									{if $hasContainers eq 1}
										<td class='fieldLabel' style='width:{$packingWidth}%;text-align:center;margin:auto'>Container Quantity</td>
										{if $SHOW_RATES}<td class='fieldLabel' style='width:{$packingWidth}%;text-align:center;margin:auto'>Container Rate</td>{/if}
									{/if}
									{if $hasPacking eq 1}
										<td class='fieldLabel' style='width:{$packingWidth}%;text-align:center;margin:auto'>Pack Quantity</td>
										{if $SHOW_RATES}<td class='fieldLabel' style='width:{$packingWidth}%;text-align:center;margin:auto'>Pack Rate</td>{/if}
									{/if}
									{if $hasUnpacking eq 1}
										<td class='fieldLabel' style='width:{$packingWidth}%;text-align:center;margin:auto'>Unpack Quantity</td>
										{if $SHOW_RATES}<td class='fieldLabel' style='width:{$packingWidth}%;text-align:center;margin:auto'>Unpack Rate</td>{/if}
									{/if}
									{if $perRow == 2}
										<td class='fieldLabel' style='width:{$packingWidth}%;text-align:center;margin:auto'>Name</td>

										{if $hasContainers eq 1}
											<td class='fieldLabel' style='width:{$packingWidth}%;text-align:center;margin:auto'>Container Quantity</td>
											{if $SHOW_RATES}<td class='fieldLabel' style='width:{$packingWidth}%;text-align:center;margin:auto'>Container Rate</td>{/if}
										{/if}
										{if $hasPacking eq 1}
											<td class='fieldLabel' style='width:{$packingWidth}%;text-align:center;margin:auto'>Pack Quantity</td>
											{if $SHOW_RATES}<td class='fieldLabel' style='width:{$packingWidth}%;text-align:center;margin:auto'>Pack Rate</td>{/if}
										{/if}
										{if $hasUnpacking eq 1}
											<td class='fieldLabel' style='width:{$packingWidth}%;text-align:center;margin:auto'>Unpack Quantity</td>
											{if $SHOW_RATES}<td class='fieldLabel' style='width:{$packingWidth}%;text-align:center;margin:auto'>Unpack Rate</td>{/if}
										{/if}
									{/if}
								</tr>
							<tr>
							{foreach item=PACK_ITEM key=PACK_INDEX from=$SERVICE_DETAILS.packingList}

								<td class='fieldLabel' style='width:{$packingWidth}%;text-align:center;margin:auto'>
									<label class="muted pull-right marginRight10px">{$PACK_ITEM[0]}</label>
									<input type="hidden" class="hide" name="Name{$ID}-{$PACK_INDEX}" value="{$PACK_ITEM[0]}">
									<input type="hidden" class="hide" name="PackID{$ID}-{$PACK_INDEX}" value="{$PACK_ITEM[7]}">
								</td>
								{if $hasContainers eq 1}
									<td class='fieldValue localPackingField' style='width:{$packingWidth}%;text-align:center;margin:auto'>
										<span class="value">{$PACK_ITEM[1]}</span>
										{if $EDITABLE === TRUE}
											<span class="hide edit">
												<input name="containerQty{$ID}-{$PACK_INDEX}" class="input-large nameField" type="number" style="width:85%;text-align:center;margin:auto" value="{$PACK_ITEM[1]}"/>
												<input type="hidden" class="fieldname" value="containerQty{$ID}-{$PACK_INDEX}" data-prev-value="{$PACK_ITEM[1]}">
											</span>
										{/if}
									</td>
									{if $SHOW_RATES}
										<td class='fieldValue localPackingField' style='width:{$packingWidth}%;margin:auto'>
										<span class="value">{if $PACK_ITEM[2]}{number_format($PACK_ITEM[2], 2, '.', '')}{/if}</span>
										{if $EDITABLE === TRUE}
											<span class="hide edit">
												<div class="row-fluid">
													<div class="input-prepend">
														<span class="span10">
															<span class="add-on">&#36;</span>
															<input name="containerRate{$ID}-{$PACK_INDEX}"style="width:62%;text-align:center;margin:auto" class="input-small currencyField" type="text" value="{if $PACK_ITEM[2]}{number_format($PACK_ITEM[2], 2, '.', '')}{/if}" data-decimal-seperator="." data-group-seperator="," data-number-of-decimal-places="2"/>
															<input type="hidden" class="fieldname" value="containerRate{$ID}-{$PACK_INDEX}" data-prev-value="{if $PACK_ITEM[2]}{number_format($PACK_ITEM[2], 2, '.', '')}{/if}">
														</span>
													</div>
												</div>
											</span>
										{/if}
										</td>
									{/if}
								{/if}
								{if $hasPacking eq 1}
									<td class='fieldValue localPackingField' style='width:{$packingWidth}%;text-align:center;margin:auto'>
										<span class="value">{$PACK_ITEM[3]}</span>
										{if $EDITABLE === TRUE}
											<span class="hide edit">
												<input name="packQty{$ID}-{$PACK_INDEX}" class="input-large nameField" type="number" style="width:85%;text-align:center;margin:auto" value="{$PACK_ITEM[3]}"/>
												<input type="hidden" class="fieldname" value="packQty{$ID}-{$PACK_INDEX}" data-prev-value="{$PACK_ITEM[3]}">
											</span>
										{/if}
									</td>
									{if $SHOW_RATES}
										<td class='fieldValue localPackingField' style='width:{$packingWidth}%;margin:auto'>
										<span class="value">{if $PACK_ITEM[4]}{number_format($PACK_ITEM[4], 2, '.', '')}{/if}</span>
										{if $EDITABLE === TRUE}
											<span class="hide edit">
												<div class="row-fluid">
													<div class="input-prepend">
														<span class="span10">
															<span class="add-on">&#36;</span>
															<input name="packRate{$ID}-{$PACK_INDEX}"style="width:62%;text-align:center;margin:auto" class="input-small currencyField" type="text" value="{if $PACK_ITEM[4]}{number_format($PACK_ITEM[4], 2, '.', '')}{/if}" data-decimal-seperator="." data-group-seperator="," data-number-of-decimal-places="2"/>
															<input type="hidden" class="fieldname" value="packRate{$ID}-{$PACK_INDEX}" data-prev-value="{if $PACK_ITEM[4]}{number_format($PACK_ITEM[4], 2, '.', '')}{/if}">
														</span>
													</div>
												</div>
											</span>
										{/if}
										</td>
									{/if}
								{/if}
								{if $hasUnpacking eq 1}
									<td class='fieldValue localPackingField' style='width:{$packingWidth}%;text-align:center;margin:auto'>
										<span class="value">{$PACK_ITEM[5]}</span>
										{if $EDITABLE === TRUE}
											<span class="hide edit">
												<input name="unpackQty{$ID}-{$PACK_INDEX}" class="input-large nameField" type="number" style="width:85%;text-align:center;margin:auto" value="{$PACK_ITEM[5]}"/>
												<input type="hidden" class="fieldname" value="unpackQty{$ID}-{$PACK_INDEX}" data-prev-value="{$PACK_ITEM[5]}">
											</span>
										{/if}
									</td>
									{if $SHOW_RATES}
										<td class='fieldValue localPackingField' style='width:{$packingWidth}%;margin:auto'>
										<span class="value">{if $PACK_ITEM[6]}{number_format($PACK_ITEM[6], 2, '.', '')}{/if}</span>
										{if $EDITABLE === TRUE}
											<span class="hide edit">
												<div class="row-fluid">
													<div class="input-prepend">
														<span class="span10">
															<span class="add-on">&#36;</span>
															<input name="unpackRate{$ID}-{$PACK_INDEX}"style="width:62%;text-align:center;margin:auto" class="input-small currencyField" type="text" value="{if $PACK_ITEM[6]}{number_format($PACK_ITEM[6], 2, '.', '')}{/if}" data-decimal-seperator="." data-group-seperator="," data-number-of-decimal-places="2"/>
															<input type="hidden" class="fieldname" value="unpackRate{$ID}-{$PACK_INDEX}" data-prev-value="{if $PACK_ITEM[6]}{number_format($PACK_ITEM[6], 2, '.', '')}{/if}">
														</span>
													</div>
												</div>
											</span>
										{/if}
										</td>
									{/if}
								{/if}
								{if ($PACK_INDEX%$perRow == 1)||($perRow ==1)}
									</tr>
									<tr>
								{/if}
							{/foreach}
							{if ($perRow==2)&&($PACK_INDEX%$perRow==0)}
								<td class='fieldLabel localPackingField' style='width:{$packingWidth}%;margin:auto'>
								{for $var=0 to ($numColumns/2)-2}
									<td class='fieldValue localPackingField' style='width:{$packingWidth}%;margin:auto'>
								{/for}
							{/if}
							</tr>
							<input type="hidden" class="hide" name="NumPacking{$ID}" value="{$PACK_INDEX}">
							</table>
						</td>
						</tr>
					{elseif $RATE_TYPE eq "Crating Item"}
						<td class="fluid" colspan="4" style='padding:0;'>
							<table name="CratingItems{$ID}" class="table table-bordered" style='border:none'>
								{if $EDITABLE === TRUE AND !$LOCK_RATING}
									<tr class='fieldLabel' colspan="11">
										<td colspan="11">
											<button type="button" name="localAddCrate-{$ID}" id="localAddCrate-{$ID}">+</button><button type="button" name="localAddCrateTwo-{$ID}" id="localAddCrateTwo-{$ID}" style="clear:right;float:right">+</button><br>
										</td>
									</tr>
								{/if}
								<tr>
									{assign var=crateColNum value=8+($EDITABLE)+($SHOW_RATES)+($SHOW_RATES)}
									{assign var=cratingWidth value=100/$crateColNum}
									{if $EDITABLE === TRUE}<td class='fieldLabel' style='width:{$cratingWidth}%;text-align:center;margin:auto'>&nbsp;{/if}
										<input type="hidden" class="hide" name="numCrates{$ID}" value="{$SERVICE_DETAILS.highestCrate}">
									{if $EDITABLE === TRUE}</td>{/if}
									<td class='fieldLabel' style='width:{$cratingWidth-5}%;text-align:center;margin:auto'>ID</td>
									<td class='fieldLabel' style='width:{$cratingWidth+5}%;text-align:center;margin:auto'>Description</td>
									<td class='fieldLabel' style='width:{$cratingWidth}%;text-align:center;margin:auto'>Length</td>
									<td class='fieldLabel' style='width:{$cratingWidth}%;text-align:center;margin:auto'>Width</td>
									<td class='fieldLabel' style='width:{$cratingWidth}%;text-align:center;margin:auto'>Height</td>
									<td class='fieldLabel' style='width:{$cratingWidth}%;text-align:center;margin:auto'>Inches Added</td>
									<td class='fieldLabel' style='width:{$cratingWidth}%;text-align:center;margin:auto'>Crating Quantity</td>
									{if $SHOW_RATES}<td class='fieldLabel' style='width:{$cratingWidth}%;text-align:center;margin:auto'>Crating Rate</td>{/if}
									<td class='fieldLabel' style='width:{$cratingWidth}%;text-align:center;margin:auto'>Uncrating Quantity</td>
									{if $SHOW_RATES}<td class='fieldLabel' style='width:{$cratingWidth}%;text-align:center;margin:auto'>Uncrating Rate</td>{/if}
								</tr>
								<tr class="hide localDefaultCrate localCrateRow newItemRow">
									{if $EDITABLE === TRUE}
										<td class='fieldValue' style='width:{$cratingWidth}%;text-align:center;margin:auto'>
											<a class="deleteCartonButton"><i title="Delete" class="icon-trash alignMiddle"></i></a>
										</td>
									{/if}
									<td class='fieldValue localCratingField' style='width:{$cratingWidth-5}%;text-align:center;margin:auto'>
										<span class="value">C-0</span>
										{if $EDITABLE === TRUE}
											<span class="hide edit">
												{assign var=FIELDNAME value='crateID'|cat:$ID}
												{$LOCALFIELDINFO.mandatory = true}
												{$LOCALFIELDINFO.name = $FIELDNAME}
												{$LOCALFIELDINFO.label = 'ID'}
												{$LOCALFIELDINFO.type = 'text'}
												{$INFO = Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($LOCALFIELDINFO))}
												<input type="text" class="input-large" name="crateID{$ID}" style="width:85%;text-align:center;margin:auto" value="C-0" data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" data-fieldinfo="{$INFO}">
												<input type="hidden" class="fieldname" value="crateID{$ID}" data-prev-value="C-0">
											</span>
										{/if}
									</td>
									<td class='fieldValue localCratingField' style='width:{$cratingWidth+5}%;text-align:center;margin:auto'>
										<span class="value"></span>
										{if $EDITABLE === TRUE}
											<span class="hide edit">
												{assign var=FIELDNAME value='Description'|cat:$ID}
												{$LOCALFIELDINFO.mandatory = true}
												{$LOCALFIELDINFO.name = $FIELDNAME}
												{$LOCALFIELDINFO.label = 'Description'}
												{$LOCALFIELDINFO.type = 'text'}
												<input type="text" class="input-large" name="Description{$ID}" style="width:85%;text-align:center;margin:auto" value="" data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" data-fieldinfo="{$INFO}">
												<input type="hidden" class="fieldname" value="Description{$ID}" data-prev-value="">
											</span>
										{/if}
									</td>
									<td class='fieldValue localCratingField' style='width:{$cratingWidth}%;text-align:center;margin:auto'>
										<span class="value"></span>
										{if $EDITABLE === TRUE}
											<span class="hide edit">
												{assign var=FIELDNAME value='Length'|cat:$ID}
												{$LOCALFIELDINFO.mandatory = true}
												{$LOCALFIELDINFO.name = $FIELDNAME}
												{$LOCALFIELDINFO.label = 'Length'}
												{$LOCALFIELDINFO.type = 'integer'}
												<input name="Length{$ID}" class="input-large nameField" type="number" style="width:85%;text-align:center;margin:auto" value="" data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" data-fieldinfo="{$INFO}"/>
												<input type="hidden" class="fieldname" value="Length{$ID}" data-prev-value="">
											</span>
										{/if}
									</td>
									<td class='fieldValue localCratingField' style='width:{$cratingWidth}%;text-align:center;margin:auto'>
										<span class="value"></span>
										{if $EDITABLE === TRUE}
											<span class="hide edit">
												{assign var=FIELDNAME value='Width'|cat:$ID}
												{$LOCALFIELDINFO.mandatory = true}
												{$LOCALFIELDINFO.name = $FIELDNAME}
												{$LOCALFIELDINFO.label = 'Width'}
												{$LOCALFIELDINFO.type = 'integer'}
												<input name="Width{$ID}" class="input-large nameField" type="number" style="width:85%;text-align:center;margin:auto" value="" data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" data-fieldinfo="{$INFO}"/>
												<input type="hidden" class="fieldname" value="Width{$ID}" data-prev-value="">
											</span>
										{/if}
									</td>
									<td class='fieldValue localCratingField' style='width:{$cratingWidth}%;text-align:center;margin:auto'>
										<span class="value"></span>
										{if $EDITABLE === TRUE}
											<span class="hide edit">
												{assign var=FIELDNAME value='Height'|cat:$ID}
												{$LOCALFIELDINFO.mandatory = true}
												{$LOCALFIELDINFO.name = $FIELDNAME}
												{$LOCALFIELDINFO.label = 'Height'}
												{$LOCALFIELDINFO.type = 'integer'}
												<input name="Height{$ID}" class="input-large nameField" type="number" style="width:85%;text-align:center;margin:auto" value="" data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" data-fieldinfo="{$INFO}"/>
												<input type="hidden" class="fieldname" value="Height{$ID}" data-prev-value="">
											</span>
										{/if}
									</td>
									<td class='fieldValue localCratingField' style='width:{$cratingWidth}%;text-align:center;margin:auto'>
										<span class="value">{$SERVICE->get('crate_inches')}</span>
										{if $EDITABLE === TRUE}
											<span class="hide edit">
												{assign var=FIELDNAME value='InchesAdded'|cat:$ID}
												{$LOCALFIELDINFO.mandatory = true}
												{$LOCALFIELDINFO.name = $FIELDNAME}
												{$LOCALFIELDINFO.label = 'Inches Added'}
												{$LOCALFIELDINFO.type = 'integer'}
												<input name="InchesAdded{$ID}" class="input-large nameField" type="number" style="width:85%;text-align:center;margin:auto" value="{$SERVICE->get('crate_inches')}" data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" data-fieldinfo="{$INFO}"/>
												<input type="hidden" class="fieldname" value="InchesAdded{$ID}" data-prev-value="{$SERVICE->get('crate_inches')}">
											</span>
										{/if}
									</td>
									<td class='fieldValue localCratingField' style='width:{$cratingWidth}%;text-align:center;margin:auto'>
										<span class="value"></span>
										{if $EDITABLE === TRUE}
											<span class="hide edit">
												{assign var=FIELDNAME value='CratingQty'|cat:$ID}
												{$LOCALFIELDINFO.mandatory = true}
												{$LOCALFIELDINFO.name = $FIELDNAME}
												{$LOCALFIELDINFO.label = 'Crating Quantity'}
												{$LOCALFIELDINFO.type = 'integer'}
												<input name="CratingQty{$ID}" class="input-large nameField" type="number" style="width:85%;text-align:center;margin:auto" value="" data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" data-fieldinfo="{$INFO}"/>
												<input type="hidden" class="fieldname" value="CratingQty{$ID}" data-prev-value="">
											</span>
										{/if}
									</td>
									{if $SHOW_RATES}
									<td class='fieldValue localCratingField' style='width:{$cratingWidth}%;margin:auto'>
									<span class="value">{if $SERVICE->get('crate_packrate')}{number_format($SERVICE->get('crate_packrate'), 2, '.', '')}{/if}</span>
									{if $EDITABLE === TRUE}
										<span class="hide edit">
											<div class="row-fluid">
												<div class="input-prepend">
													<span class="span10">
														<span class="add-on">&#36;</span>
														{assign var=FIELDNAME value='CratingRate'|cat:$ID}
														{$LOCALFIELDINFO.mandatory = true}
														{$LOCALFIELDINFO.name = $FIELDNAME}
														{$LOCALFIELDINFO.label = 'Crating Rate'}
														{$LOCALFIELDINFO.type = 'currency'}
														<input name="CratingRate{$ID}"style="width:62%;margin:auto" class="input-medium currencyField" type="text" value="{if $SERVICE->get('crate_packrate')}{number_format($SERVICE->get('crate_packrate'), 2, '.', '')}{/if}" data-decimal-seperator="." data-group-seperator="," data-number-of-decimal-places="2" data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" data-fieldinfo="{$INFO}"/>
														<input type="hidden" class="fieldname" value="CratingRate{$ID}" data-prev-value="{if $SERVICE->get('crate_packrate')}{number_format($SERVICE->get('crate_packrate'), 2, '.', '')}{/if}">
													</span>
												</div>
											</div>
										</span>
									{/if}
									</td>
									{/if}
									<td class='fieldValue localCratingField' style='width:{$cratingWidth}%;text-align:center;margin:auto'>
										<span class="value"></span>
										{if $EDITABLE === TRUE}
											<span class="hide edit">
												{assign var=FIELDNAME value='UncratingQty'|cat:$ID}
												{$LOCALFIELDINFO.mandatory = true}
												{$LOCALFIELDINFO.name = $FIELDNAME}
												{$LOCALFIELDINFO.label = 'Uncrating Quantity'}
												{$LOCALFIELDINFO.type = 'integer'}
												<input name="UncratingQty{$ID}" class="input-large nameField" type="number" style="width:85%;text-align:center;margin:auto" value="" data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" data-fieldinfo="{$INFO}"/>
												<input type="hidden" class="fieldname" value="UncratingQty{$ID}" data-prev-value="">
											</span>
										{/if}
									</td>
									{if $SHOW_RATES}
									<td class='fieldValue localCratingField' style='width:{$cratingWidth}%;margin:auto'>
									<span class="value">{if $SERVICE->get('crate_unpackrate')}{number_format($SERVICE->get('crate_unpackrate'), 2, '.', '')}{/if}</span>
									{if $EDITABLE === TRUE}
										<span class="hide edit">
											<div class="row-fluid">
												<div class="input-prepend">
													<span class="span10">
														<span class="add-on">&#36;</span>
														{assign var=FIELDNAME value='UncratingRate'|cat:$ID}
														{$LOCALFIELDINFO.mandatory = true}
														{$LOCALFIELDINFO.name = $FIELDNAME}
														{$LOCALFIELDINFO.label = 'Uncrating Rate'}
														{$LOCALFIELDINFO.type = 'currency'}
														<input name="UncratingRate{$ID}"style="width:62%;margin:auto" class="input-medium currencyField" type="text" value="{if $SERVICE->get('crate_unpackrate')}{number_format($SERVICE->get('crate_unpackrate'), 2, '.', '')}{/if}" data-decimal-seperator="." data-group-seperator="," data-number-of-decimal-places="2" data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" data-fieldinfo="{$INFO}"/>
														<input type="hidden" class="fieldname" value="UncratingRate{$ID}" data-prev-value="{if $SERVICE->get('crate_unpackrate')}{number_format($SERVICE->get('crate_unpackrate'), 2, '.', '')}{/if}">
													</span>
												</div>
											</div>
										</span>
									{/if}
									</td>
									{/if}
								</tr>
								{assign var=cratingList value=$SERVICE_DETAILS.cratingList}
								{if !empty($cratingList)}
								{foreach item=CRATE key=CRATE_INDEX from=$cratingList}
									{assign var=STRING value=$ID|cat:'-'}
									{assign var=STRING value=$STRING|cat:$CRATE[10]}
									<tr class="localCrateRow" id="localCrateRow{$CRATE[10]}">
										{if $EDITABLE === TRUE}
											<td class='fieldValue' style='width:{$cratingWidth}%;text-align:center;margin:auto'>
													<a class="deleteCartonButton"><i title="Delete" class="icon-trash alignMiddle"></i></a>
											</td>
										{/if}
										<td class='fieldValue localCratingField' style='width:{$cratingWidth-5}%;text-align:center;margin:auto'>
											<span class="value">{$CRATE[0]}</span>
											{if $EDITABLE === TRUE}
												<span class="hide edit">
													{assign var=FIELDNAME value='crateID'|cat:$STRING}
													{$LOCALFIELDINFO.mandatory = true}
													{$LOCALFIELDINFO.name = $FIELDNAME}
													{$LOCALFIELDINFO.label = 'ID'}
													{$LOCALFIELDINFO.type = 'text'}
													<input type="text" class="input-large" name="crateID{$ID}-{$CRATE[10]}" style="width:85%;text-align:center;margin:auto" value="{$CRATE[0]}" data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" data-fieldinfo="{$INFO}">
													<input type="hidden" class="fieldname" value="crateID{$ID}-{$CRATE[10]}" data-prev-value="{$CRATE[0]}">
												</span>
											{/if}
										</td>
										<td class='fieldValue localCratingField' style='width:{$cratingWidth+5}%;text-align:center;margin:auto'>
											<span class="value">{$CRATE[1]}</span>
											{if $EDITABLE === TRUE}
												<span class="hide edit">
													{assign var=FIELDNAME value='Description'|cat:$STRING}
													{$LOCALFIELDINFO.mandatory = true}
													{$LOCALFIELDINFO.name = $FIELDNAME}
													{$LOCALFIELDINFO.label = 'Description'}
													{$LOCALFIELDINFO.type = 'text'}
													<input type="text" class="input-large" name="Description{$ID}-{$CRATE[10]}" style="width:85%;text-align:center;margin:auto" value="{$CRATE[1]}" data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" data-fieldinfo="{$INFO}">
													<input type="hidden" class="fieldname" value="Description{$ID}-{$CRATE[10]}" data-prev-value="{$CRATE[1]}">
												</span>
											{/if}
										</td>
										<td class='fieldValue localCratingField' style='width:{$cratingWidth}%;text-align:center;margin:auto'>
											<span class="value">{$CRATE[6]}</span>
											{if $EDITABLE === TRUE}
												<span class="hide edit">
													{assign var=FIELDNAME value='Length'|cat:$STRING}
													{$LOCALFIELDINFO.mandatory = true}
													{$LOCALFIELDINFO.name = $FIELDNAME}
													{$LOCALFIELDINFO.label = 'Length'}
													{$LOCALFIELDINFO.type = 'integer'}
													<input name="Length{$ID}-{$CRATE[10]}" class="input-large nameField" type="number" style="width:85%;text-align:center;margin:auto" value="{$CRATE[6]}" data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" data-fieldinfo="{$INFO}"/>
													<input type="hidden" class="fieldname" value="Length{$ID}-{$CRATE[10]}" data-prev-value="{$CRATE[6]}">
												</span>
											{/if}
										</td>
										<td class='fieldValue localCratingField' style='width:{$cratingWidth}%;text-align:center;margin:auto'>
											<span class="value">{$CRATE[7]}</span>
											{if $EDITABLE === TRUE}
												<span class="hide edit">
													{assign var=FIELDNAME value='Width'|cat:$STRING}
													{$LOCALFIELDINFO.mandatory = true}
													{$LOCALFIELDINFO.name = $FIELDNAME}
													{$LOCALFIELDINFO.label = 'Width'}
													{$LOCALFIELDINFO.type = 'integer'}
													<input name="Width{$ID}-{$CRATE[10]}" class="input-large nameField" type="number" style="width:85%;text-align:center;margin:auto" value="{$CRATE[7]}" data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" data-fieldinfo="{$INFO}"/>
													<input type="hidden" class="fieldname" value="Width{$ID}-{$CRATE[10]}" data-prev-value="{$CRATE[7]}">
												</span>
											{/if}
										</td>
										<td class='fieldValue localCratingField' style='width:{$cratingWidth}%;text-align:center;margin:auto'>
											<span class="value">{$CRATE[8]}</span>
											{if $EDITABLE === TRUE}
												<span class="hide edit">
													{assign var=FIELDNAME value='Height'|cat:$STRING}
													{$LOCALFIELDINFO.mandatory = true}
													{$LOCALFIELDINFO.name = $FIELDNAME}
													{$LOCALFIELDINFO.label = 'Height'}
													{$LOCALFIELDINFO.type = 'integer'}
													<input name="Height{$ID}-{$CRATE[10]}" class="input-large nameField" type="number" style="width:85%;text-align:center;margin:auto" value="{$CRATE[8]}" data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" data-fieldinfo="{$INFO}"/>
													<input type="hidden" class="fieldname" value="Height{$ID}-{$CRATE[10]}" data-prev-value="{$CRATE[8]}">
												</span>
											{/if}
										</td>
										<td class='fieldValue localCratingField' style='width:{$cratingWidth}%;text-align:center;margin:auto'>
											<span class="value">{$CRATE[9]}</span>
											{if $EDITABLE === TRUE}
												<span class="hide edit">
													{assign var=FIELDNAME value='InchesAdded'|cat:$STRING}
													{$LOCALFIELDINFO.mandatory = true}
													{$LOCALFIELDINFO.name = $FIELDNAME}
													{$LOCALFIELDINFO.label = 'Inches Added'}
													{$LOCALFIELDINFO.type = 'integer'}
													<input name="InchesAdded{$ID}-{$CRATE[10]}" class="input-large nameField" type="number" style="width:85%;text-align:center;margin:auto" value="{$CRATE[9]}" data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" data-fieldinfo="{$INFO}"/>
													<input type="hidden" class="fieldname" value="InchesAdded{$ID}-{$CRATE[10]}" data-prev-value="{$CRATE[9]}">
												</span>
											{/if}
										</td>
										<td class='fieldValue localCratingField' style='width:{$cratingWidth}%;text-align:center;margin:auto'>
											<span class="value">{$CRATE[2]}</span>
											{if $EDITABLE === TRUE}
												<span class="hide edit">
													{assign var=FIELDNAME value='CratingQty'|cat:$STRING}
													{$LOCALFIELDINFO.mandatory = true}
													{$LOCALFIELDINFO.name = $FIELDNAME}
													{$LOCALFIELDINFO.label = 'Crating Quantity'}
													{$LOCALFIELDINFO.type = 'integer'}
													<input name="CratingQty{$ID}-{$CRATE[10]}" class="input-large nameField" type="number" style="width:85%;text-align:center;margin:auto" value="{$CRATE[2]}" data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" data-fieldinfo="{$INFO}"/>
													<input type="hidden" class="fieldname" value="CratingQty{$ID}-{$CRATE[10]}" data-prev-value="{$CRATE[2]}">
												</span>
											{/if}
										</td>
										{if $SHOW_RATES}
										<td class='fieldValue localCratingField' style='width:{$cratingWidth}%;margin:auto'>
										<span class="value">{if $CRATE[3]}{number_format($CRATE[3], 2, '.', '')}{/if}</span>
										{if $EDITABLE === TRUE}
											<span class="hide edit">
												<div class="row-fluid">
													<div class="input-prepend">
														<span class="span10">
															<span class="add-on">&#36;</span>
															{assign var=FIELDNAME value='CratingRate'|cat:$STRING}
															{$LOCALFIELDINFO.mandatory = true}
															{$LOCALFIELDINFO.name = $FIELDNAME}
															{$LOCALFIELDINFO.label = 'Crating Rate'}
															{$LOCALFIELDINFO.type = 'currency'}
															<input name="CratingRate{$ID}-{$CRATE[10]}"style="width:62%;margin:auto" class="input-medium currencyField" type="text" value="{if $CRATE[3]}{number_format($CRATE[3], 2, '.', '')}{/if}" data-decimal-seperator="." data-group-seperator="," data-number-of-decimal-places="2" data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" data-fieldinfo="{$INFO}"/>
															<input type="hidden" class="fieldname" value="CratingRate{$ID}-{$CRATE[10]}" data-prev-value="{if $CRATE[3]}{number_format($CRATE[3], 2, '.', '')}{/if}">
														</span>
													</div>
												</div>
											</span>
										{/if}
										</td>
										{/if}
										<td class='fieldValue localCratingField' style='width:{$cratingWidth}%;text-align:center;margin:auto'>
											<span class="value">{$CRATE[4]}</span>
											{if $EDITABLE === TRUE}
												<span class="hide edit">
													{assign var=FIELDNAME value='UncratingQty'|cat:$STRING}
													{$LOCALFIELDINFO.mandatory = true}
													{$LOCALFIELDINFO.name = $FIELDNAME}
													{$LOCALFIELDINFO.label = 'Uncrating Quantity'}
													{$LOCALFIELDINFO.type = 'integer'}
													<input name="UncratingQty{$ID}-{$CRATE[10]}" class="input-large nameField" type="number" style="width:85%;text-align:center;margin:auto" value="{$CRATE[4]}" data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" data-fieldinfo="{$INFO}"/>
													<input type="hidden" class="fieldname" value="UncratingQty{$ID}-{$CRATE[10]}" data-prev-value="{$CRATE[4]}">
												</span>
											{/if}
										</td>
										{if $SHOW_RATES}
										<td class='fieldValue localCratingField' style='width:{$cratingWidth}%;margin:auto'>
											<span class="value">{if $CRATE[5]}{number_format($CRATE[5], 2, '.', '')}{/if}</span>
											{if $EDITABLE === TRUE}
												<span class="hide edit">
													<div class="row-fluid">
														<div class="input-prepend">
															<span class="span10">
																<span class="add-on">&#36;</span>
																{assign var=FIELDNAME value='UncratingRate'|cat:$STRING}
																{$LOCALFIELDINFO.mandatory = true}
																{$LOCALFIELDINFO.name = $FIELDNAME}
																{$LOCALFIELDINFO.label = 'Uncrating Rate'}
																{$LOCALFIELDINFO.type = 'currency'}
																<input name="UncratingRate{$ID}-{$CRATE[10]}"style="width:62%;margin:auto" class="input-medium currencyField" type="text" value="{if $CRATE[5]}{number_format($CRATE[5], 2, '.', '')}{/if}" data-decimal-seperator="." data-group-seperator="," data-number-of-decimal-places="2" data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" data-fieldinfo="{$INFO}"/>
																<input type="hidden" class="fieldname" value="UncratingRate{$ID}-{$CRATE[10]}" data-prev-value="{if $CRATE[5]}{number_format($CRATE[5], 2, '.', '')}{/if}">
															</span>
														</div>
													</div>
												</span>
											{/if}
										</td>
										{/if}
									</tr>
								{/foreach}
								{/if}
							</table>
						</td>
					{elseif $RATE_TYPE eq "Break Point Trans."}
						<tr>
							<td class='fieldLabel'>
								<label class="muted pull-right marginRight10px">Miles</label>
							</td>
							<td class='fieldValue localBreakPointField'>
							{assign var=FIELDNAME value='Miles'|cat:$ID}
							{if $SERVICE->get(is_required)}{$LOCALFIELDINFO.mandatory = true}{else}{$LOCALFIELDINFO.mandatory = false}{/if}
							{$LOCALFIELDINFO.name = $FIELDNAME}
							{$LOCALFIELDINFO.label = 'Miles'}
							{$LOCALFIELDINFO.type = 'integer'}
							{$INFO = Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($LOCALFIELDINFO))}
							<span class="value">{$SERVICE_DETAILS.mileage}</span>
								{if $EDITABLE === TRUE}
								<span class="hide edit">
									<input name="Miles{$ID}" class="input-large nameField localBreakPoint" type="number" value="{$SERVICE_DETAILS.mileage}" data-validation-engine="validate[{if $SERVICE->get(is_required)}required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" data-fieldinfo="{$INFO}" />
									<input type="hidden" class="fieldname" value="Miles{$ID}" data-prev-value="{$SERVICE_DETAILS.mileage}">
								</span>
							{/if}
							</td>
							{if $SHOW_RATES}
							<td class='fieldLabel'>
								<label class="muted pull-right marginRight10px">Rate</label>
							</td>
							<td class='fieldValue localBreakPointField'>
							{assign var=FIELDNAME value='Rate'|cat:$ID}
							{if $SERVICE->get(is_required)}{$LOCALFIELDINFO.mandatory = true}{else}{$LOCALFIELDINFO.mandatory = false}{/if}
							{$LOCALFIELDINFO.name = $FIELDNAME}
							{$LOCALFIELDINFO.label = 'Rate'}
							{$LOCALFIELDINFO.type = 'currency'}
							{$INFO = Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($LOCALFIELDINFO))}
							<span class="value">{if $SERVICE_DETAILS.rate}{number_format($SERVICE_DETAILS.rate, 2, '.', '')}{/if}</span>
							{if $EDITABLE === TRUE}
								<span class="hide edit">
									<div class="row-fluid">
										<div class="input-prepend">
											<span class="span10">
												<span class="add-on">&#36;</span>
												<input name="Rate{$ID}" class="input-medium currencyField" type="text" value="{if $SERVICE_DETAILS.rate}{number_format($SERVICE_DETAILS.rate, 2, '.', '')}{/if}" data-decimal-seperator="." data-group-seperator="," data-number-of-decimal-places="2" data-validation-engine="validate[{if $SERVICE->get(is_required)}required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" data-fieldinfo="{$INFO}" />
												<input type="hidden" class="fieldname" value="Rate{$ID}" data-prev-value="{if $SERVICE_DETAILS.rate}{number_format($SERVICE_DETAILS.rate, 2, '.', '')}{/if}">
											</span>
										</div>
									</div>
								</span>
							{/if}
							</td>
							{else}
								<td class='fieldLabel'>&nbsp;</td>
								<td class='fieldValue'>&nbsp;</td>
							{/if}
						</tr>
						<tr>
							<td class='fieldLabel'>
								<label class="muted pull-right marginRight10px">Weight</label>
							</td>
							<td class='fieldValue localBreakPointField'>
							{assign var=FIELDNAME value='Weight'|cat:$ID}
							{if $SERVICE->get(is_required)}{$LOCALFIELDINFO.mandatory = true}{else}{$LOCALFIELDINFO.mandatory = false}{/if}
							{$LOCALFIELDINFO.name = $FIELDNAME}
							{$LOCALFIELDINFO.label = 'Weight'}
							{$LOCALFIELDINFO.type = 'integer'}
							{$INFO = Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($LOCALFIELDINFO))}
							<span class="value">{$SERVICE_DETAILS.weight}</span>
							{if $EDITABLE === TRUE}
								<span class="hide edit">
									<input name="Weight{$ID}" class="input-large nameField localBreakPoint" type="number" value="{$SERVICE_DETAILS.weight}" data-validation-engine="validate[{if $SERVICE->get(is_required)}required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" data-fieldinfo="{$INFO}" />
									<input type="hidden" class="fieldname" value="Weight{$ID}" data-prev-value="{$SERVICE_DETAILS.weight}">
								</span>
							{/if}
							</td>

							<td class='fieldLabel'>
								<label class="muted pull-right marginRight10px">Calculated Weight</label>
							</td>
							<td class='fieldValue localBreakPointField'>
							{assign var=FIELDNAME value='calcWeight'|cat:$ID}
							{if $SERVICE->get(is_required)}{$LOCALFIELDINFO.mandatory = true}{else}{$LOCALFIELDINFO.mandatory = false}{/if}
							{$LOCALFIELDINFO.name = $FIELDNAME}
							{$LOCALFIELDINFO.label = 'Calculated Weight'}
							{$LOCALFIELDINFO.type = 'integer'}
							{$INFO = Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($LOCALFIELDINFO))}
							<span class="value">{$SERVICE_DETAILS.breakpoint}</span>
							{if $EDITABLE === TRUE}
								<span class="hide edit">
									<input type="text" name="calcWeight{$ID}" value="{$SERVICE_DETAILS.breakpoint}" class="input-large" readonly role="textbox" data-validation-engine="validate[{if $SERVICE->get(is_required)}required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" data-fieldinfo="{$INFO}" />
									<input type="hidden" class="fieldname" value="calcWeight{$ID}" data-prev-value="{$SERVICE_DETAILS.breakpoint}">
								</span>
							{/if}
							</td>
						</tr>
					{elseif $RATE_TYPE eq "Flat Rate By Weight"}
						{include file=vtemplate_path('localSectionsDetail/FlatRateByWeight.tpl','Estimates') DETAILS=$SERVICE_DETAILS.frbw}
					{elseif $RATE_TYPE eq "CWT by Weight" || $RATE_TYPE eq "SIT Cartage" }
						<tr>
							<td class='fieldLabel'>
								<label class="muted pull-right marginRight10px">Weight</label>
							</td>
							<td class='fieldValue localCWTbyWeight'>
							{assign var=FIELDNAME value='Weight'|cat:$ID}
							{if $SERVICE->get(is_required)}{$LOCALFIELDINFO.mandatory = true}{else}{$LOCALFIELDINFO.mandatory = false}{/if}
							{$LOCALFIELDINFO.name = $FIELDNAME}
							{$LOCALFIELDINFO.label = 'Weight'}
							{$LOCALFIELDINFO.type = 'integer'}
							{$INFO = Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($LOCALFIELDINFO))}
							<span class="value">{$SERVICE_DETAILS.weight}</span>
							{if $EDITABLE === TRUE}
								<span class="hide edit">
									<input id="{$MODULE_NAME}_editView_fieldName_Weight{$ID}" name="Weight{$ID}" class="input-large nameField localWeightMile" type="number" value="{$SERVICE_DETAILS.weight}" data-validation-engine="validate[{if $SERVICE->get(is_required)}required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" data-fieldinfo="{$INFO}" />
									<input type="hidden" class="fieldname" value="Weight{$ID}" data-prev-value="{$SERVICE_DETAILS.weight}">
								</span>
							{/if}
							</td>
							{if $SHOW_RATES}
								<td class='fieldLabel'>
									<label class="muted pull-right marginRight10px">Rate</label>
								</td>
								<td class='fieldValue localCWTbyWeight'>
								{assign var=FIELDNAME value='Rate'|cat:$ID}
								{if $SERVICE->get(is_required)}{$LOCALFIELDINFO.mandatory = true}{else}{$LOCALFIELDINFO.mandatory = false}{/if}
								{$LOCALFIELDINFO.name = $FIELDNAME}
								{$LOCALFIELDINFO.label = 'Rate'}
								{$LOCALFIELDINFO.type = 'currency'}
								{$INFO = Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($LOCALFIELDINFO))}
								{if $RATE_TYPE eq "CWT by Weight"}{assign var=SERVICE_RATE value=$SERVICE_DETAILS.rate}{else}{assign var=SERVICE_RATE value=$SERVICE->get('cartage_cwt_rate')}{/if}
								<span class="value">{if $SERVICE_RATE}{number_format($SERVICE_RATE, 2, '.', '')}{/if}</span>
								{if $EDITABLE === TRUE}
									<span class="hide edit">
										<div class="row-fluid">
											<div class="input-prepend">
												<span class="span10">
													<span class="add-on">&#36;</span>
														<input id="{$MODULE_NAME}_editView_fieldName_Rate{$ID}" name="Rate{$ID}" class="input-medium currencyField" type="text" value="{if $SERVICE_RATE}{number_format($SERVICE_RATE, 2, '.', '')}{/if}" data-decimal-seperator="." data-group-seperator="," data-number-of-decimal-places="2" data-validation-engine="validate[{if $SERVICE->get(is_required)}required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" data-fieldinfo="{$INFO}" />
														<input type="hidden" class="fieldname" value="Rate{$ID}" data-prev-value="{if $SERVICE_RATE}{number_format($SERVICE_RATE, 2, '.', '')}{/if}">
													</span>
												</span>
											</div>
										</div>
									</span>
								{/if}
								</td>
							{else}
								<td class='fieldLabel'>&nbsp;</td>
								<td class='fieldValue'>&nbsp;</td>
							{/if}
						</tr>
					{elseif $RATE_TYPE eq "SIT Item"}
						<tr>
								<td class='fieldLabel'>
									<label class="muted pull-right marginRight10px">Cartage Rate</label>
								</td>
								<td class='fieldValue'>
									<span class="value">{if $SERVICE_DETAILS.cartage_cwt_rate}{number_format($SERVICE_DETAILS.cartage_cwt_rate, 2, '.', '')}{/if}</span>
								</td>
								<td class='fieldLabel'>
									<label class="muted pull-right marginRight10px">First Day Rate</label>
								</td>
								<td class='fieldValue'>
									<span class="value">{if $SERVICE_DETAILS.first_day_rate}{number_format($SERVICE_DETAILS.first_day_rate, 2, '.', '')}{/if}</span>
								</td>
							</tr>
						<tr>
								<td class='fieldLabel'>
									<label class="muted pull-right marginRight10px">Additional Day Rate</label>
								</td>
								<td class='fieldValue'>
									<span class="value">{if $SERVICE_DETAILS.additional_day_rate}{number_format($SERVICE_DETAILS.additional_day_rate, 2, '.', '')}{/if}</span>
								</td>
								<td class='fieldLabel'></td>
								<td class='fieldValue'></td>
							</tr>
                    {elseif $RATE_TYPE eq "CWT Per Quantity" }
					<tr>
						<td class='fieldLabel'>
							<label class="muted pull-right marginRight10px">Quantity</label>
						</td>
						<td class='fieldValue localCWTbyWeight'>
						{assign var=FIELDNAME value='Quantity'|cat:$ID}
						{if $SERVICE->get(is_required)}{$LOCALFIELDINFO.mandatory = true}{else}{$LOCALFIELDINFO.mandatory = false}{/if}
						{$LOCALFIELDINFO.name = $FIELDNAME}
						{$LOCALFIELDINFO.label = 'Weight'}
						{$LOCALFIELDINFO.type = 'integer'}
						{$INFO = Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($LOCALFIELDINFO))}
						<span class="value">{$SERVICE_DETAILS.quantity}</span>
						{if $EDITABLE === TRUE}
							<span class="hide edit">
								<input id="{$MODULE_NAME}_editView_fieldName_Quantity{$ID}" name="Quantity{$ID}" class="input-large nameField localWeightMile" type="number" value="{$SERVICE_DETAILS.quantity}" data-validation-engine="validate[{if $SERVICE->get(is_required)}required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" data-fieldinfo="{$INFO}" />
								<input type="hidden" class="fieldname" value="Quantity{$ID}" data-prev-value="{$SERVICE_DETAILS.quantity}">
							</span>
						{/if}
						</td>
						{if $SHOW_RATES}
							<td class='fieldLabel'>
								<label class="muted pull-right marginRight10px">Rate</label>
							</td>
							<td class='fieldValue localCWTbyQuantity'>
							{assign var=FIELDNAME value='Rate'|cat:$ID}
							{if $SERVICE->get(is_required)}{$LOCALFIELDINFO.mandatory = true}{else}{$LOCALFIELDINFO.mandatory = false}{/if}
							{$LOCALFIELDINFO.name = $FIELDNAME}
							{$LOCALFIELDINFO.label = 'Rate'}
							{$LOCALFIELDINFO.type = 'currency'}
							{$INFO = Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($LOCALFIELDINFO))}
							{if $RATE_TYPE eq "CWT Per Quantity"}{assign var=SERVICE_RATE value=$SERVICE_DETAILS.rate}{else}{assign var=SERVICE_RATE value=$SERVICE->get('cwtperqty_rate')}{/if}
							<span class="value">{if $SERVICE_RATE}{number_format($SERVICE_RATE, 2, '.', '')}{/if}</span>
							{if $EDITABLE === TRUE}
								<span class="hide edit">
									<div class="row-fluid">
										<div class="input-prepend">
											<span class="span10">
												<span class="add-on">&#36;</span>
													<input id="{$MODULE_NAME}_editView_fieldName_Rate{$ID}" name="Rate{$ID}" class="input-medium currencyField" type="text" value="{if $SERVICE_RATE}{number_format($SERVICE_RATE, 2, '.', '')}{/if}" data-decimal-seperator="." data-group-seperator="," data-number-of-decimal-places="2" data-validation-engine="validate[{if $SERVICE->get(is_required)}required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" data-fieldinfo="{$INFO}" />
													<input type="hidden" class="fieldname" value="Rate{$ID}" data-prev-value="{if $SERVICE_RATE}{number_format($SERVICE_RATE, 2, '.', '')}{/if}">
												</span>
											</span>
										</div>
									</div>
								</span>
							{/if}
							</td>
						{else}
							<td class='fieldLabel'>&nbsp;</td>
							<td class='fieldValue'>&nbsp;</td>
						{/if}
					</tr>
					<tr>
						<td class='fieldLabel'>
							<label class="muted pull-right marginRight10px">Weight</label>
						</td>
						<td class='fieldValue localCWTperQuantity'>
						{assign var=FIELDNAME value='Weight'|cat:$ID}
						{if $SERVICE->get(is_required)}{$LOCALFIELDINFO.mandatory = true}{else}{$LOCALFIELDINFO.mandatory = false}{/if}
						{$LOCALFIELDINFO.name = $FIELDNAME}
						{$LOCALFIELDINFO.label = 'Weight'}
						{$LOCALFIELDINFO.type = 'integer'}
						{$INFO = Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($LOCALFIELDINFO))}
						<span class="value">{$SERVICE_DETAILS.weight}</span>
						{if $EDITABLE === TRUE}
							<span class="hide edit">
								<input id="{$MODULE_NAME}_editView_fieldName_Weight{$ID}" name="Weight{$ID}" class="input-large nameField localWeightMile" type="number" value="{$SERVICE_DETAILS.weight}" data-validation-engine="validate[{if $SERVICE->get(is_required)}required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" data-fieldinfo="{$INFO}" />
								<input type="hidden" class="fieldname" value="Weight{$ID}" data-prev-value="{$SERVICE_DETAILS.weight}">
							</span>
						{/if}
						</td>
						<td class='fieldLabel'>&nbsp;</td>
						<td class='fieldValue'>&nbsp;</td>
					</tr>
					{elseif $RATE_TYPE eq "Flat Rate By Weight"}
						{include file=vtemplate_path('localSectionsDetail/FlatRateByWeight.tpl','Estimates') DETAILS=$SERVICE_DETAILS.frbw}
					{else}
						<tr>
							<td class='fieldLabel'>&nbsp;</td>
							<td class='fieldValue'>&nbsp;</td>
							<td class='fieldLabel'>&nbsp;</td>
							<td class='fieldValue'>&nbsp;</td>
						</tr>
					{/if}
				{/foreach}
			</tbody>
		</table>
		<br/>
	{/foreach}
	</div>
{/if}
</div>
{/strip}
