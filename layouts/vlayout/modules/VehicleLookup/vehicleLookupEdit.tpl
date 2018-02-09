{strip}
	{assign var=HAS_CONTENT value=(!$BLOCK_SUBLIST || $BLOCK_SUBLIST['AUTO_TRANSPORT_VEHICLES'])}
<div id="contentHolder_AUTO_TRANSPORT_VEHICLES" class="sectionContentHolder {$CONTENT_DIV_CLASS} {if !$ALWAYS_SHOW_CONTENT_DIV}hide{/if} {if !$HAS_CONTENT}inactive{/if}">
	{if $HAS_CONTENT}
{if $VEHICLE_LOOKUP}
	<table name='vehicleLookupTable' class='table table-bordered blockContainer showInlineTable{if is_array($HIDDEN_BLOCKS)}{if in_array($BLOCK_LABEL, $HIDDEN_BLOCKS)} hide{/if}{/if}'>
		<thead>
			<tr>
				<th class='blockHeader' colspan='4'>{vtranslate('LBL_VEHICLE_BLOCK', 'VehicleLookup')}</th>
			</tr>
		</thead>
		<tbody>
			<tr class='fieldLabel' colspan='4'>
				<td colspan='4'><button type='button' name='addVehicle' id='addVehicle'>+</button><input type='hidden' id='numVehicles' name='numVehicles' value='{$VEHICLE_LIST|@count}'><span style="width:55%;clear:right;float:right;">{if $MODULE_NAME eq 'Estimates'}<button type="button" style="float:left;width:20%" id="vehicleRatingButton" class="interstateRateBtns interstateRateDetail">Get Rate</button>{/if}<button type='button' name='addVehicle2' id='addVehicle2' style='clear:right;float:right'>+</button></span></td>
			</tr>
		<tbody class='defaultVehicle vehicleBlock hide'>
				<tr class='fieldLabel' colspan='4'>
					<td colspan='4' class='blockHeader'>
						<img class='cursorPointer alignMiddle blockToggle vehicleToggle{if !($IS_HIDDEN)} hide {/if} '  src='{vimage_path('arrowRight.png')}' data-mode='hide' data-id='defaultVehicle'>
						<img class='cursorPointer alignMiddle blockToggle vehicleToggle{if ($IS_HIDDEN)} hide {/if}'  src='{vimage_path('arrowDown.png')}' data-mode='show' data-id='defaultVehicle'>
						<span class='vehicleTitle'><b>&nbsp;&nbsp;&nbsp;{vtranslate('LBL_VEHICLE_HEADER', 'VehicleLookup')}</b></span>
						<span><a style='float: right; padding: 3px'><i title='Delete' class='deleteVehicleButton icon-trash'></i></a></span>
					</td>
				</tr>
				<tr class='vehicleContent defaultVehicleContent'>
					<td class='fieldLabel {$WIDTHTYPE}'>
						<label class='muted pull-right marginRight10px'>{vtranslate('LBL_VEHICLE_MAKE', 'VehicleLookup')}</label>
					</td>
					<td class='fieldValue {$WIDTHTYPE}'>
						<div class='row-fluid'>
							<span class='span12'>
								{$LOCALFIELDINFO.mandatory = false}
								{$LOCALFIELDINFO.name = 'vehicle_make'}
								{$LOCALFIELDINFO.label = 'LBL_VEHICLE_MAKE'}
								{$LOCALFIELDINFO.type = 'string'}
								{$INFO = Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($LOCALFIELDINFO))}
								<input id='vehicle_make' type='text' class='input-large' name='vehicle_make' value='' data-fieldinfo={$INFO} data-validation-engine='validate[{if $LOCALFIELDINFO.mandatory}required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]'>
							</span>
						</div>
					</td>
					<td class='fieldLabel {$WIDTHTYPE}'>
						<label class='muted pull-right marginRight10px'>{vtranslate('LBL_VEHICLE_MODEL', 'VehicleLookup')}</label>
					</td>
					<td class='fieldValue {$WIDTHTYPE}'>
						<div class='row-fluid'>
							<span class='span12'>
								{$LOCALFIELDINFO.mandatory = false}
								{$LOCALFIELDINFO.name = 'vehicle_model'}
								{$LOCALFIELDINFO.label = 'LBL_VEHICLE_MODEL'}
								{$LOCALFIELDINFO.type = 'string'}
								{$INFO = Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($LOCALFIELDINFO))}
								<input id='vehicle_model' type='text' class='input-large' name='vehicle_model' value='' data-fieldinfo={$INFO} data-validation-engine='validate[{if $LOCALFIELDINFO.mandatory}required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]'>
							</span>
						</div>
					</td>
				</tr>
				<tr class='vehicleContent defaultVehicleContent'>
					<td class='fieldLabel {$WIDTHTYPE}'>
						<label class='muted pull-right marginRight10px'>{vtranslate('LBL_VEHICLE_YEAR', 'VehicleLookup')}</label>
					</td>
					<td class='fieldValue {$WIDTHTYPE}'>
						<div class='row-fluid'>
							<span class='span12'>
								{$LOCALFIELDINFO.mandatory = false}
								{$LOCALFIELDINFO.name = 'vehicle_year'}
								{$LOCALFIELDINFO.label = 'LBL_VEHICLE_YEAR'}
								{$LOCALFIELDINFO.type = 'string'}
								{$INFO = Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($LOCALFIELDINFO))}
								<input id='vehicle_year' type='text' class='input-large' name='vehicle_year' value='' data-fieldinfo={$INFO} data-validation-engine='validate[{if $LOCALFIELDINFO.mandatory}required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]'>
							</span>
						</div>
					</td>
					<td class='fieldLabel {$WIDTHTYPE}'>
						<label class='muted pull-right marginRight10px'>{vtranslate('LBL_VEHICLE_VIN', 'VehicleLookup')}</label>
					</td>
					<td class='fieldValue {$WIDTHTYPE}'>
						<div class='row-fluid'>
							<span class='span12'>
								{$LOCALFIELDINFO.mandatory = false}
								{$LOCALFIELDINFO.name = 'vehicle_vin'}
								{$LOCALFIELDINFO.label = 'LBL_VEHICLE_VIN'}
								{$LOCALFIELDINFO.type = 'string'}
								{$INFO = Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($LOCALFIELDINFO))}
								<input id='vehicle_vin' type='text' class='input-large' name='vehicle_vin' value='' data-fieldinfo={$INFO} data-validation-engine='validate[{if $LOCALFIELDINFO.mandatory}required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]'>
							</span>
						</div>
					</td>
				</tr>
				<tr class='vehicleContent defaultVehicleContent'>
					<td class='fieldLabel {$WIDTHTYPE}'>
						<label class='muted pull-right marginRight10px'>{vtranslate('LBL_VEHICLE_COLOR', 'VehicleLookup')}</label>
					</td>
					<td class='fieldValue {$WIDTHTYPE}'>
						<div class='row-fluid'>
							<span class='span12'>
								{$LOCALFIELDINFO.mandatory = false}
								{$LOCALFIELDINFO.name = 'vehicle_color'}
								{$LOCALFIELDINFO.label = 'LBL_VEHICLE_COLOR'}
								{$LOCALFIELDINFO.type = 'string'}
								{$INFO = Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($LOCALFIELDINFO))}
								<input id='vehicle_color' type='text' class='input-large' name='vehicle_color' value='' data-fieldinfo={$INFO} data-validation-engine='validate[{if $LOCALFIELDINFO.mandatory}required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]'>
							</span>
						</div>
					</td>
					<td class='fieldLabel {$WIDTHTYPE}'>
						<label class='muted pull-right marginRight10px'>{vtranslate('LBL_VEHICLE_ODOMETER', 'VehicleLookup')}</label>
					</td>
					<td class='fieldValue {$WIDTHTYPE}'>
						<div class='row-fluid'>
							<span class='span12'>
								{$LOCALFIELDINFO.mandatory = false}
								{$LOCALFIELDINFO.name = 'vehicle_odometer'}
								{$LOCALFIELDINFO.label = 'LBL_VEHICLE_ODOMETER'}
								{$LOCALFIELDINFO.type = 'string'}
								{$INFO = Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($LOCALFIELDINFO))}
								<input id='vehicle_odometer' type='text' class='input-large' name='vehicle_odometer' value='' data-fieldinfo={$INFO} data-validation-engine='validate[{if $LOCALFIELDINFO.mandatory}required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]'>
							</span>
						</div>
					</td>
				</tr>
				<tr class='vehicleContent defaultVehicleContent'>
					<td class='fieldLabel {$WIDTHTYPE}'>
						<label class='muted pull-right marginRight10px'>{vtranslate('LBL_VEHICLE_LSTATE', 'VehicleLookup')}</label>
					</td>
					<td class='fieldValue {$WIDTHTYPE}'>
						<div class='row-fluid'>
							<span class='span12'>
								{$LOCALFIELDINFO.mandatory = false}
								{$LOCALFIELDINFO.name = 'vehicle_lstate'}
								{$LOCALFIELDINFO.label = 'LBL_VEHICLE_LSTATE'}
								{$LOCALFIELDINFO.type = 'string'}
								{$INFO = Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($LOCALFIELDINFO))}
								<input id='vehicle_lstate' type='text' class='input-large' name='vehicle_lstate' value='' data-fieldinfo={$INFO} data-validation-engine='validate[{if $LOCALFIELDINFO.mandatory}required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]'>
							</span>
						</div>
					</td>
					<td class='fieldLabel {$WIDTHTYPE}'>
						<label class='muted pull-right marginRight10px'>{vtranslate('LBL_VEHICLE_LNUMBER', 'VehicleLookup')}</label>
					</td>
					<td class='fieldValue {$WIDTHTYPE}'>
						<div class='row-fluid'>
							<span class='span12'>
								{$LOCALFIELDINFO.mandatory = false}
								{$LOCALFIELDINFO.name = 'vehicle_lnumber'}
								{$LOCALFIELDINFO.label = 'LBL_VEHICLE_LNUMBER'}
								{$LOCALFIELDINFO.type = 'string'}
								{$INFO = Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($LOCALFIELDINFO))}
								<input id='vehicle_lnumber' type='text' class='input-large' name='vehicle_lnumber' value='' data-fieldinfo={$INFO} data-validation-engine='validate[{if $LOCALFIELDINFO.mandatory}required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]'>
							</span>
						</div>
					</td>
				</tr>
				<tr class='vehicleContent defaultVehicleContent'>
					<td class='fieldLabel {$WIDTHTYPE}'>
						<label class='muted pull-right marginRight10px'>{vtranslate('LBL_VEHICLE_TYPE', 'VehicleLookup')}</label>
					</td>
					<td class='fieldValue {$WIDTHTYPE}'>
						<div class='row-fluid'>
							<span class='span12'>
								{$LOCALFIELDINFO.mandatory = false}
								{$LOCALFIELDINFO.name = 'vehicle_type'}
								{$LOCALFIELDINFO.label = 'LBL_VEHICLE_TYPE'}
								{$LOCALFIELDINFO.type = 'string'}
								{$INFO = Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($LOCALFIELDINFO))}
								
								<input type="hidden" name="vehicle_type" value="none">
								<select style="text-align:left" id="vehicle_type" name="vehicle_type" data-fieldinfo="" data-selected-value="">
									<option value="" style="text-align:left">Select an Option</option>
									<option style="text-align:left" value="Car">Car</option>
									<option style="text-align:left" value="Truck">Truck</option>
									<option style="text-align:left" value="SUV">SUV</option>
								</select>
							</span>
						</div>
					</td>
					<td class='fieldLabel {$WIDTHTYPE}'>
					</td>
					<td class='fieldValue {$WIDTHTYPE}'>
						<button type='button' name='lookupVin' id='lookupVin'>{vtranslate('LBL_VEHICLE_LOOKUP', 'VehicleLookup')}</button>
					</td>
				</tr>
				<input id="vehicle_id" type="hidden" name="vehicle_id" value="none">
			</tbody>
			{foreach key=VEHICLE_INDEX item=VEHICLE_ITEM from=$VEHICLE_LIST}
				<tbody class='vehicleBlock'>
					<tr class='fieldLabel' colspan='4'>
						<td colspan='4' class='blockHeader'>
							<img class='cursorPointer alignMiddle blockToggle vehicleToggle{if !($IS_HIDDEN)} hide {/if} '  src='{vimage_path('arrowRight.png')}' data-mode='hide' data-id='defaultVehicle' blocktoggleid="vehicleLookupTable{$VEHICLE_INDEX+1}">
							<img class='cursorPointer alignMiddle blockToggle vehicleToggle{if ($IS_HIDDEN)} hide {/if}'  src='{vimage_path('arrowDown.png')}' data-mode='show' data-id='defaultVehicle' blocktoggleid="vehicleLookupTable{$VEHICLE_INDEX+1}">
							<span class='vehicleTitle'><b>&nbsp;&nbsp;&nbsp;{vtranslate('LBL_VEHICLE_HEADER', 'VehicleLookup')} {$VEHICLE_INDEX+1}</b></span>
							<span><a style='float: right; padding: 3px'><i title='Delete' class='deleteVehicleButton icon-trash'></i></a></span>
						</td>
					</tr>
					<tr class='vehicleContent' blocktoggleid="vehicleLookupTable{$VEHICLE_INDEX+1}">
						<td class='fieldLabel {$WIDTHTYPE}'>
							<label class='muted pull-right marginRight10px'>{vtranslate('LBL_VEHICLE_MAKE', 'VehicleLookup')}</label>
						</td>
						<td class='fieldValue {$WIDTHTYPE}'>
							<div class='row-fluid'>
								<span class='span12'>
									{$LOCALFIELDINFO.mandatory = false}
									{$LOCALFIELDINFO.name = 'vehicle_make_'|@cat:($VEHICLE_INDEX+1)}
									{$LOCALFIELDINFO.label = 'LBL_VEHICLE_MAKE'}
									{$LOCALFIELDINFO.type = 'string'}
									{$INFO = Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($LOCALFIELDINFO))}
									<input id='vehicle_make_{$VEHICLE_INDEX+1}' type='text' class='input-large' name='vehicle_make_{$VEHICLE_INDEX+1}' data-fieldinfo={$INFO} data-validation-engine='validate[{if $LOCALFIELDINFO.mandatory}required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]' value="{$VEHICLE_ITEM['vehicle_make']}">
								</span>
							</div>
						</td>
						<td class='fieldLabel {$WIDTHTYPE}'>
							<label class='muted pull-right marginRight10px'>{vtranslate('LBL_VEHICLE_MODEL', 'VehicleLookup')}</label>
						</td>
						<td class='fieldValue {$WIDTHTYPE}'>
							<div class='row-fluid'>
								<span class='span12'>
									{$LOCALFIELDINFO.mandatory = false}
									{$LOCALFIELDINFO.name = 'vehicle_model_'|@cat:($VEHICLE_INDEX+1)}
									{$LOCALFIELDINFO.label = 'LBL_VEHICLE_MODEL'}
									{$LOCALFIELDINFO.type = 'string'}
									{$INFO = Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($LOCALFIELDINFO))}
									<input id='vehicle_model_{$VEHICLE_INDEX+1}' type='text' class='input-large' name='vehicle_model_{$VEHICLE_INDEX+1}' data-fieldinfo={$INFO} data-validation-engine='validate[{if $LOCALFIELDINFO.mandatory}required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]' value="{$VEHICLE_ITEM['vehicle_model']}">
								</span>
							</div>
						</td>
					</tr>
					<tr class='vehicleContent' blocktoggleid="vehicleLookupTable{$VEHICLE_INDEX+1}">
						<td class='fieldLabel {$WIDTHTYPE}'>
							<label class='muted pull-right marginRight10px'>{vtranslate('LBL_VEHICLE_YEAR', 'VehicleLookup')}</label>
						</td>
						<td class='fieldValue {$WIDTHTYPE}'>
							<div class='row-fluid'>
								<span class='span12'>
									{$LOCALFIELDINFO.mandatory = false}
									{$LOCALFIELDINFO.name = 'vehicle_year_'|@cat:($VEHICLE_INDEX+1)}
									{$LOCALFIELDINFO.label = 'LBL_VEHICLE_YEAR'}
									{$LOCALFIELDINFO.type = 'string'}
									{$INFO = Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($LOCALFIELDINFO))}
									<input id='vehicle_year_{$VEHICLE_INDEX+1}' type='text' class='input-large' name='vehicle_year_{$VEHICLE_INDEX+1}' data-fieldinfo={$INFO} data-validation-engine='validate[{if $LOCALFIELDINFO.mandatory}required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]' value="{$VEHICLE_ITEM['vehicle_year']}">
								</span>
							</div>
						</td>
						<td class='fieldLabel {$WIDTHTYPE}'>
							<label class='muted pull-right marginRight10px'>{vtranslate('LBL_VEHICLE_VIN', 'VehicleLookup')}</label>
						</td>
						<td class='fieldValue {$WIDTHTYPE}'>
							<div class='row-fluid'>
								<span class='span12'>
									{$LOCALFIELDINFO.mandatory = false}
									{$LOCALFIELDINFO.name = 'vehicle_vin_'|@cat:($VEHICLE_INDEX+1)}
									{$LOCALFIELDINFO.label = 'LBL_VEHICLE_VIN'}
									{$LOCALFIELDINFO.type = 'string'}
									{$INFO = Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($LOCALFIELDINFO))}
									<input id='vehicle_vin_{$VEHICLE_INDEX+1}' type='text' class='input-large' name='vehicle_vin_{$VEHICLE_INDEX+1}' data-fieldinfo={$INFO} data-validation-engine='validate[{if $LOCALFIELDINFO.mandatory}required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]' value="{$VEHICLE_ITEM['vehicle_vin']}">
								</span>
							</div>
						</td>
					</tr>
					<tr class='vehicleContent' blocktoggleid="vehicleLookupTable{$VEHICLE_INDEX+1}">
						<td class='fieldLabel {$WIDTHTYPE}'>
							<label class='muted pull-right marginRight10px'>{vtranslate('LBL_VEHICLE_COLOR', 'VehicleLookup')}</label>
						</td>
						<td class='fieldValue {$WIDTHTYPE}'>
							<div class='row-fluid'>
								<span class='span12'>
									{$LOCALFIELDINFO.mandatory = false}
									{$LOCALFIELDINFO.name = 'vehicle_color_'|@cat:($VEHICLE_INDEX+1)}
									{$LOCALFIELDINFO.label = 'LBL_VEHICLE_COLOR'}
									{$LOCALFIELDINFO.type = 'string'}
									{$INFO = Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($LOCALFIELDINFO))}
									<input id='vehicle_color_{$VEHICLE_INDEX+1}' type='text' class='input-large' name='vehicle_color_{$VEHICLE_INDEX+1}' data-fieldinfo={$INFO} data-validation-engine='validate[{if $LOCALFIELDINFO.mandatory}required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]' value="{$VEHICLE_ITEM['vehicle_color']}">
								</span>
							</div>
						</td>
						<td class='fieldLabel {$WIDTHTYPE}'>
							<label class='muted pull-right marginRight10px'>{vtranslate('LBL_VEHICLE_ODOMETER', 'VehicleLookup')}</label>
						</td>
						<td class='fieldValue {$WIDTHTYPE}'>
							<div class='row-fluid'>
								<span class='span12'>
									{$LOCALFIELDINFO.mandatory = false}
									{$LOCALFIELDINFO.name = 'vehicle_odometer_'|@cat:($VEHICLE_INDEX+1)}
									{$LOCALFIELDINFO.label = 'LBL_VEHICLE_ODOMETER'}
									{$LOCALFIELDINFO.type = 'string'}
									{$INFO = Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($LOCALFIELDINFO))}
									<input id='vehicle_odometer_{$VEHICLE_INDEX+1}' type='text' class='input-large' name='vehicle_odometer_{$VEHICLE_INDEX+1}' data-fieldinfo={$INFO} data-validation-engine='validate[{if $LOCALFIELDINFO.mandatory}required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]' value="{$VEHICLE_ITEM['vehicle_odometer']}">
								</span>
							</div>
						</td>
					</tr>
					<tr class='vehicleContent' blocktoggleid="vehicleLookupTable{$VEHICLE_INDEX+1}">
						<td class='fieldLabel {$WIDTHTYPE}'>
							<label class='muted pull-right marginRight10px'>{vtranslate('LBL_VEHICLE_LSTATE', 'VehicleLookup')}</label>
						</td>
						<td class='fieldValue {$WIDTHTYPE}'>
							<div class='row-fluid'>
								<span class='span12'>
									{$LOCALFIELDINFO.mandatory = false}
									{$LOCALFIELDINFO.name = 'vehicle_lstate_'|@cat:($VEHICLE_INDEX+1)}
									{$LOCALFIELDINFO.label = 'LBL_VEHICLE_LSTATE'}
									{$LOCALFIELDINFO.type = 'string'}
									{$INFO = Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($LOCALFIELDINFO))}
									<input id='vehicle_lstate_{$VEHICLE_INDEX+1}' type='text' class='input-large' name='vehicle_lstate_{$VEHICLE_INDEX+1}' data-fieldinfo={$INFO} data-validation-engine='validate[{if $LOCALFIELDINFO.mandatory}required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]' value="{$VEHICLE_ITEM['license_state']}">
								</span>
							</div>
						</td>
						<td class='fieldLabel {$WIDTHTYPE}'>
							<label class='muted pull-right marginRight10px'>{vtranslate('LBL_VEHICLE_LNUMBER', 'VehicleLookup')}</label>
						</td>
						<td class='fieldValue {$WIDTHTYPE}'>
							<div class='row-fluid'>
								<span class='span12'>
									{$LOCALFIELDINFO.mandatory = false}
									{$LOCALFIELDINFO.name = 'vehicle_lnumber_'|@cat:($VEHICLE_INDEX+1)}
									{$LOCALFIELDINFO.label = 'LBL_VEHICLE_LNUMBER'}
									{$LOCALFIELDINFO.type = 'string'}
									{$INFO = Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($LOCALFIELDINFO))}
									<input id='vehicle_lnumber_{$VEHICLE_INDEX+1}' type='text' class='input-large' name='vehicle_lnumber_{$VEHICLE_INDEX+1}' data-fieldinfo={$INFO} data-validation-engine='validate[{if $LOCALFIELDINFO.mandatory}required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]' value="{$VEHICLE_ITEM['license_number']}">
								</span>
							</div>
						</td>
					</tr>
					<tr class='vehicleContent' blocktoggleid="vehicleLookupTable{$VEHICLE_INDEX+1}">
						<td class='fieldLabel {$WIDTHTYPE}'>
							<label class='muted pull-right marginRight10px'>{vtranslate('LBL_VEHICLE_TYPE', 'VehicleLookup')}</label>
						</td>
						<td class='fieldValue {$WIDTHTYPE}'>
							<div class='row-fluid'>
								<span class='span12'>
									{$LOCALFIELDINFO.mandatory = false}
									{$LOCALFIELDINFO.name = 'vehicle_type_'|@cat:($VEHICLE_INDEX+1)}
									{$LOCALFIELDINFO.label = 'LBL_VEHICLE_TYPE'}
									{$LOCALFIELDINFO.type = 'string'}
									{$INFO = Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($LOCALFIELDINFO))}
									<input type="hidden" name="vehicle_type" value="none">
									<select class='chzn-select' style="text-align:left" id="vehicle_type_{$VEHICLE_INDEX+1}" name="vehicle_type_{$VEHICLE_INDEX+1}" data-fieldinfo="" data-selected-value="">
										<option value="" style="text-align:left">Select an Option</option>
										<option style="text-align:left" value="Car"{if $VEHICLE_ITEM['vehicle_type'] eq 'Car'} selected{/if}>Car</option>
										<option style="text-align:left" value="Truck"{if $VEHICLE_ITEM['vehicle_type'] eq 'Truck'} selected{/if}>Truck</option>
										<option style="text-align:left" value="SUV"{if $VEHICLE_ITEM['vehicle_type'] eq 'SUV'} selected{/if}>SUV</option>
									</select>
								</span>
							</div>
						</td>
						<td class='fieldLabel {$WIDTHTYPE}'>
						</td>
						<td class='fieldValue {$WIDTHTYPE}'>
							<button type='button' name='lookupVin{$VEHICLE_INDEX+1}' id='lookupVin{$VEHICLE_INDEX+1}'>{vtranslate('LBL_VEHICLE_LOOKUP', 'VehicleLookup')}</button>
						</td>
					</tr>
					<input id="vehicle_id_{$VEHICLE_INDEX+1}" type="hidden" name="vehicle_id_{$VEHICLE_INDEX+1}" value="{$VEHICLE_ITEM['vehicleid']}">
				</tbody>
			{/foreach}
		</tbody>
	</table>
	<br />
{/if}
	{/if}
	</div>
{/strip}