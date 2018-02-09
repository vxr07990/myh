{strip}
	<table name='corpVehicleTable' class='table table-bordered equalSplit detailview-table{if is_array($HIDDEN_BLOCKS)}{if in_array($BLOCK_LABEL, $HIDDEN_BLOCKS)} hide{/if}{/if}' blocktoggleid="corpVehicleTable">
		<thead>
			<tr>
				<th class="blockHeader" colspan="4">
					<img class="cursorPointer alignMiddle blockToggle {if !($IS_HIDDEN)} hide {/if} "  src="{vimage_path('arrowRight.png')}" data-mode="hide" data-id="corpVehicles" blocktoggleid="corpVehicleTable">
					<img class="cursorPointer alignMiddle blockToggle {if ($IS_HIDDEN)} hide {/if}"  src="{vimage_path('arrowDown.png')}" data-mode="show" data-id="corpVehicles" blocktoggleid="corpVehicleTable">
					&nbsp;&nbsp;{vtranslate('LBL_VEHICLE_CORPORATE_BLOCK', $MODULE)}
				</th>
			</tr>
		</thead>
		<tbody class="{if ($IS_HIDDEN)} hide {/if}" blocktoggleid="corpVehicleTable">
			<tr class='fieldLabel' colspan='4'>
				<td colspan='4'><button type='button' id='addCorpVehicle'>+</button><input type='hidden' id='numCorporateVehicles' name='numCorporateVehicles' value='{$NUM_CORP_VEHICLES}'><button type='button' id='addCorpVehicle2' style='clear:right;float:right'>+</button></td>
			</tr>
			<tbody class='defaultVehicle vehicleBlock hide'>
				<tr class='fieldLabel' colspan='4'>
					<td colspan='4' class='blockHeader'>
						{*
						<img class='cursorPointer alignMiddle vehicleToggle{if !($IS_HIDDEN)} hide {/if} '  src='{vimage_path('arrowRight.png')}' data-mode='hide' data-id='defaultVehicle'>
						<img class='cursorPointer alignMiddle vehicleToggle{if ($IS_HIDDEN)} hide {/if}'  src='{vimage_path('arrowDown.png')}' data-mode='show' data-id='defaultVehicle'>
						*}
						<span class='vehicleTitle'><b>&nbsp;&nbsp;&nbsp;{vtranslate('LBL_VEHICLE_HEADER', $MODULE)}</b></span>
						<span><a style='float: right; padding: 3px'><i title='Delete' class='deleteVehicleButton icon-trash'></i></a></span>
					</td>
				</tr>
				<tr class='vehicleContent defaultVehicleContent'>
					<td class='fieldLabel {$WIDTHTYPE}'>
						<label class='muted pull-right marginRight10px'>{vtranslate('LBL_VEHICLE_MAKE', $MODULE)}</label>
					</td>
					<td class='fieldValue {$WIDTHTYPE}'>
						<div class='row-fluid'>
							<span class='span12'>
								{$LOCALFIELDINFO.mandatory = false}
								{$LOCALFIELDINFO.name = 'vehicle_make'}
								{$LOCALFIELDINFO.label = 'LBL_VEHICLE_MAKE'}
								{$LOCALFIELDINFO.type = 'string'}
								{$INFO = Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($LOCALFIELDINFO))}

								<input type="hidden" name="vehicle_make" value="none">
								<select style="text-align:left" id="vehicle_make" name="vehicle_make" data-fieldinfo="" data-selected-value="">
									<option value="" style="text-align:left">Select an Option</option>
									<option style="text-align:left" value="ACURA">Acura</option>
									<option style="text-align:left" value="AUDI">Audi</option>
									<option style="text-align:left" value="BENTLEY">Bentley</option>
									<option style="text-align:left" value="BMW">BMW</option>
									<option style="text-align:left" value="BUICK">Buick</option>
									<option style="text-align:left" value="CADILLAC">Cadillac</option>
									<option style="text-align:left" value="CHEVROLET">Chevrolet</option>
									<option style="text-align:left" value="CHRYSLER">Chrysler</option>
									<option style="text-align:left" value="DODGE">Dodge</option>
									<option style="text-align:left" value="FERRARI">Ferrari</option>
									<option style="text-align:left" value="FORD">Ford</option>
									<option style="text-align:left" value="GMC">GMC</option>
									<option style="text-align:left" value="HONDA">Honda</option>
									<option style="text-align:left" value="HUMMER">Hummer</option>
									<option style="text-align:left" value="HYUNDAI">Hyundai</option>
									<option style="text-align:left" value="INFINITI">Infiniti</option>
									<option style="text-align:left" value="ISUZU">Isuzu</option>
									<option style="text-align:left" value="JAGUAR">Jaguar</option>
									<option style="text-align:left" value="JEEP">Jeep</option>
									<option style="text-align:left" value="KIA">Kia</option>
									<option style="text-align:left" value="LAND ROVER">Land Rover</option>
									<option style="text-align:left" value="LEXUS">Lexus</option>
									<option style="text-align:left" value="LINCOLN">Lincoln</option>
									<option style="text-align:left" value="LOTUS">Lotus</option>
									<option style="text-align:left" value="MASERATI">Maserati</option>
									<option style="text-align:left" value="MAZDA">Mazda</option>
									<option style="text-align:left" value="MERCEDES">Mercedes</option>
									<option style="text-align:left" value="MERCURY">Mercury</option>
									<option style="text-align:left" value="MINI">Mini</option>
									<option style="text-align:left" value="MITSUBISHI">Mitsubishi</option>
									<option style="text-align:left" value="NISSAN">Nissan</option>
									<option style="text-align:left" value="OLDSMOBILE">Oldsmobile</option>
									<option style="text-align:left" value="OTHER">Other</option>
									<option style="text-align:left" value="PONTIAC">Pontiac</option>
									<option style="text-align:left" value="PORSCHE">Porsche</option>
									<option style="text-align:left" value="ROLLSROYCE">Rolls-Royce</option>
									<option style="text-align:left" value="SAAB">Saab</option>
									<option style="text-align:left" value="SATURN">Saturn</option>
									<option style="text-align:left" value="SCION">Scion</option>
									<option style="text-align:left" value="SUBARU">Subaru</option>
									<option style="text-align:left" value="SUZUKI">Suzuki</option>
									<option style="text-align:left" value="TOYOTA">Toyota</option>
									<option style="text-align:left" value="VOLKSWAGEN">Volkswagen</option>
									<option style="text-align:left" value="VOLVO">Volvo</option>
								</select>
							</span>
						</div>
					</td>
					<td class='fieldLabel {$WIDTHTYPE}'>
						<label class='muted pull-right marginRight10px'>{vtranslate('LBL_VEHICLE_MODEL', $MODULE)}</label>
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
						<label class='muted pull-right marginRight10px'>{vtranslate('LBL_VEHICLE_YEAR', $MODULE)}</label>
					</td>
					<td class='fieldValue {$WIDTHTYPE}'>
						<div class='row-fluid'>
							<span class='span12'>
								{$LOCALFIELDINFO.mandatory = false}
								{$LOCALFIELDINFO.name = 'vehicle_year'}
								{$LOCALFIELDINFO.label = 'LBL_VEHICLE_YEAR'}
								{$LOCALFIELDINFO.type = 'integer'}
								{$INFO = Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($LOCALFIELDINFO))}
								<input id='vehicle_year' type="number" min="1900" max="{date('Y')+1}" class='input-large' name='vehicle_year' value='{date('Y')-10}' data-fieldinfo={$INFO} data-validation-engine='validate[{if $LOCALFIELDINFO.mandatory}required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]'>
							</span>
						</div>
					</td>
					<td class='fieldLabel {$WIDTHTYPE}'>
						<label class='muted pull-right marginRight10px'>{vtranslate('LBL_VEHICLE_WEIGHT', $MODULE)}</label>
					</td>
					<td class='fieldValue {$WIDTHTYPE}'>
						<div class='row-fluid'>
							<span class='span12'>
								{$LOCALFIELDINFO.mandatory = false}
								{$LOCALFIELDINFO.name = 'vehicle_weight'}
								{$LOCALFIELDINFO.label = 'LBL_VEHICLE_WEIGHT'}
								{$LOCALFIELDINFO.type = 'decimal'}
								{$INFO = Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($LOCALFIELDINFO))}
								<input id='vehicle_weight' type="number" min="0" max="" step=".5" class='input-large' name='vehicle_weight' value='0' data-fieldinfo={$INFO} data-validation-engine='validate[{if $LOCALFIELDINFO.mandatory}required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]'>
							</span>
						</div>
					</td>
				</tr>
				<tr class='vehicleContent defaultVehicleContent'>
					<td class='fieldLabel {$WIDTHTYPE}'>
						<label class='muted pull-right marginRight10px'>{vtranslate('LBL_VEHICLE_CUBE', $MODULE)}</label>
					</td>
					<td class='fieldValue {$WIDTHTYPE}'>
						<div class='row-fluid'>
							<span class='span12'>
								{$LOCALFIELDINFO.mandatory = false}
								{$LOCALFIELDINFO.name = 'vehicle_cube'}
								{$LOCALFIELDINFO.label = 'LBL_VEHICLE_CUBE'}
								{$LOCALFIELDINFO.type = 'decimal'}
								{$INFO = Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($LOCALFIELDINFO))}
								<input id='vehicle_cube' type="number" min="0" max="" class='input-large' name='vehicle_cube' value='0' data-fieldinfo={$INFO} data-validation-engine='validate[{if $LOCALFIELDINFO.mandatory}required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]'>
							</span>
						</div>
					</td>
					<td class='fieldLabel {$WIDTHTYPE}'>
						<label class='muted pull-right marginRight10px'>{vtranslate('LBL_VEHICLE_SERVICE', $MODULE)}</label>
					</td>
					<td class='fieldValue {$WIDTHTYPE}'>
						<div class='row-fluid'>
							<span class='span12'>
								{$LOCALFIELDINFO.mandatory = false}
								{$LOCALFIELDINFO.name = 'vehicle_service'}
								{$LOCALFIELDINFO.label = 'LBL_VEHICLE_SERVICE'}
								{$LOCALFIELDINFO.type = 'string'}
								{$INFO = Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($LOCALFIELDINFO))}

								<input type="hidden" name="vehicle_service" value="none">
								<select style="text-align:left" id="vehicle_service" name="vehicle_service" data-fieldinfo="" data-selected-value="">
									<option value="" style="text-align:left">Select an Option</option>
									<option style="text-align:left" value="Contract">Contract</option>
									<option style="text-align:left" value="Budget">Budget</option>
									<option style="text-align:left" value="Premier">Premier</option>
									<option style="text-align:left" value="Bulker">Bulker</option>
								</select>
							</span>
						</div>
					</td>
				</tr>
				<tr class='vehicleContent defaultVehicleContent'>
					<td class='fieldLabel {$WIDTHTYPE}'>
						<label class='muted pull-right marginRight10px'>{vtranslate('LBL_VEHICLE_DVPVALUE', $MODULE)}</label>
					</td>
					<td class='fieldValue {$WIDTHTYPE}'>
						<div class='row-fluid'>
							<span class='span12'>
								{$LOCALFIELDINFO.mandatory = false}
								{$LOCALFIELDINFO.name = 'vehicle_dvp_value'}
								{$LOCALFIELDINFO.label = 'LBL_VEHICLE_DVPVALUE'}
								{$LOCALFIELDINFO.type = 'integer'}
								{$INFO = Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($LOCALFIELDINFO))}
								<input id='vehicle_dvp_value' type="number" min="0" max="" class='input-large' name='vehicle_dvp_value' value='0' data-fieldinfo={$INFO} data-validation-engine='validate[{if $LOCALFIELDINFO.mandatory}required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]'>
							</span>
						</div>
					</td>
					<td class="fieldLabel">
						<label class='muted pull-right marginRight10px'>{vtranslate('LBL_VEHICLE_CARONVAN', $MODULE)}</label>
					</td>
					<td class="fieldValue {$WIDTHTYPE}">
						{$LOCALFIELDINFO.mandatory = false}
						{$LOCALFIELDINFO.name = 'vehicle_car_on_van'}
						{$LOCALFIELDINFO.label = 'LBL_VEHICLE_CARONVAN'}
						{$LOCALFIELDINFO.type = 'boolean'}
						{$INFO = Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($LOCALFIELDINFO))}
						<input type="hidden" name="vehicle_car_on_van" value="0">
						<input id='vehicle_car_on_van' type="checkbox" name="vehicle_car_on_van" data-fieldinfo={$INFO} data-validation-engine='validate[{if $LOCALFIELDINFO.mandatory}required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]'>
					</td>
				</tr>
				<tr class='vehicleContent defaultVehicleContent'>
					<td class='fieldLabel {$WIDTHTYPE}'>
						<label class='muted pull-right marginRight10px'>{vtranslate('LBL_VEHICLE_OVERSIZECLASS', $MODULE)}</label>
					</td>
					<td class='fieldValue {$WIDTHTYPE}'>
						<div class='row-fluid'>
							<span class='span12'>
								{$LOCALFIELDINFO.mandatory = false}
								{$LOCALFIELDINFO.name = 'vehicle_oversize_class'}
								{$LOCALFIELDINFO.label = 'LBL_VEHICLE_OVERSIZECLASS'}
								{$LOCALFIELDINFO.type = 'string'}
								{$INFO = Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($LOCALFIELDINFO))}

								<input type="hidden" name="vehicle_oversize_class" value="none">
								<select style="text-align:left" id="vehicle_oversize_class" name="vehicle_oversize_class" data-fieldinfo="" data-selected-value="">
									<option value="" style="text-align:left">Select an Option</option>
									<option style="text-align:left" value="None">None</option>
									<option style="text-align:left" value="I">I</option>
									<option style="text-align:left" value="II">II</option>
									<option style="text-align:left" value="III">III</option>
									<option style="text-align:left" value="IV">IV</option>
								</select>
							</span>
						</div>
					</td>
					<td class="fieldLabel">
						<label class='muted pull-right marginRight10px'>{vtranslate('LBL_VEHICLE_INOPERABLE', $MODULE)}</label>
					</td>
					<td class="fieldValue {$WIDTHTYPE}">
						{$LOCALFIELDINFO.mandatory = false}
						{$LOCALFIELDINFO.name = 'vehicle_inoperable'}
						{$LOCALFIELDINFO.label = 'LBL_VEHICLE_INOPERABLE'}
						{$LOCALFIELDINFO.type = 'boolean'}
						{$INFO = Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($LOCALFIELDINFO))}
						<input type="hidden" name="vehicle_inoperable" value="0">
						<input id='vehicle_inoperable' type="checkbox" name="vehicle_inoperable" data-fieldinfo={$INFO} data-validation-engine='validate[{if $LOCALFIELDINFO.mandatory}required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]'>
					</td>
				</tr>
				<tr class='vehicleContent defaultVehicleContent'>
					<td class='fieldLabel {$WIDTHTYPE}'>
						<label class='muted pull-right marginRight10px'>{vtranslate('LBL_VEHICLE_LENGTH', $MODULE)}</label>
					</td>
					<td class='fieldValue {$WIDTHTYPE}'>
						<div class='row-fluid'>
							<span class='span12'>
								{$LOCALFIELDINFO.mandatory = false}
								{$LOCALFIELDINFO.name = 'vehicle_length'}
								{$LOCALFIELDINFO.label = 'LBL_VEHICLE_LENGTH'}
								{$LOCALFIELDINFO.type = 'integer'}
								{$INFO = Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($LOCALFIELDINFO))}
								<input id='vehicle_length' type="number" min="0" max="" class='input-large' name='vehicle_length' value='0' data-fieldinfo={$INFO} data-validation-engine='validate[{if $LOCALFIELDINFO.mandatory}required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]'>
							</span>
						</div>
					</td>
					<td class='fieldLabel {$WIDTHTYPE}'>
						<label class='muted pull-right marginRight10px'>{vtranslate('LBL_VEHICLE_WIDTH', $MODULE)}</label>
					</td>
					<td class='fieldValue {$WIDTHTYPE}'>
						<div class='row-fluid'>
							<span class='span12'>
								{$LOCALFIELDINFO.mandatory = false}
								{$LOCALFIELDINFO.name = 'vehicle_width'}
								{$LOCALFIELDINFO.label = 'LBL_VEHICLE_WIDTH'}
								{$LOCALFIELDINFO.type = 'integer'}
								{$INFO = Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($LOCALFIELDINFO))}
								<input id='vehicle_width' type="number" min="0" max="" class='input-large' name='vehicle_width' value='0' data-fieldinfo={$INFO} data-validation-engine='validate[{if $LOCALFIELDINFO.mandatory}required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]'>
							</span>
						</div>
					</td>
				</tr>
				<tr class='vehicleContent defaultVehicleContent'>
					<td class='fieldLabel {$WIDTHTYPE}'>
						<label class='muted pull-right marginRight10px'>{vtranslate('LBL_VEHICLE_HEIGHT', $MODULE)}</label>
					</td>
					<td class='fieldValue {$WIDTHTYPE}'>
						<div class='row-fluid'>
							<span class='span12'>
								{$LOCALFIELDINFO.mandatory = false}
								{$LOCALFIELDINFO.name = 'vehicle_height'}
								{$LOCALFIELDINFO.label = 'LBL_VEHICLE_HEIGHT'}
								{$LOCALFIELDINFO.type = 'integer'}
								{$INFO = Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($LOCALFIELDINFO))}
								<input id='vehicle_height' type="number" min="0" max="" class='input-large' name='vehicle_height' value='0' data-fieldinfo={$INFO} data-validation-engine='validate[{if $LOCALFIELDINFO.mandatory}required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]'>
							</span>
						</div>
					</td>
					<td class='fieldLabel {$WIDTHTYPE}'>
						<label class='muted pull-right marginRight10px'>{vtranslate('LBL_VEHICLE_CHARGE', $MODULE)}</label>
					</td>
					<td class='fieldValue {$WIDTHTYPE}'>
						<div class='input-prepend input-prepend-centered'>
							{$LOCALFIELDINFO.mandatory = false}
							{$LOCALFIELDINFO.name = 'vehicle_charge'}
							{$LOCALFIELDINFO.label = 'LBL_VEHICLE_CHARGE'}
							{$LOCALFIELDINFO.type = 'decimal'}
							{$INFO = Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($LOCALFIELDINFO))}
							<span class='add-on'>$</span>
							<input type='number' step="any" class='input-medium currencyField' style='width:80%;float:left' name='vehicle_charge' value='0.00' data-decimal-seperator="." data-group-seperator="," data-number-of-decimal-places="2" />
						</div>
					</td>
				</tr>
				<tr class='vehicleContent defaultVehicleContent'>
					<td class='fieldLabel {$WIDTHTYPE}'>
						<label class='muted pull-right marginRight10px'>{vtranslate('LBL_VEHICLE_SHIPPINGCOUNT', $MODULE)}</label>
					</td>
					<td class='fieldValue {$WIDTHTYPE}'>
						<div class='row-fluid'>
							<span class='span12'>
								{$LOCALFIELDINFO.mandatory = false}
								{$LOCALFIELDINFO.name = 'vehicle_shipping_count'}
								{$LOCALFIELDINFO.label = 'LBL_VEHICLE_SHIPPINGCOUNT'}
								{$LOCALFIELDINFO.type = 'integer'}
								{$INFO = Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($LOCALFIELDINFO))}
								<input id='vehicle_shipping_count' type="number" min="0" max="" class='input-large' name='vehicle_shipping_count' value='0' data-fieldinfo={$INFO} data-validation-engine='validate[{if $LOCALFIELDINFO.mandatory}required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]'>
							</span>
						</div>
					</td>
					<td class='fieldLabel {$WIDTHTYPE}'>
						<label class='muted pull-right marginRight10px'>{vtranslate('LBL_VEHICLE_NOTSHIPPINGCOUNT', $MODULE)}</label>
					</td>
					<td class='fieldValue {$WIDTHTYPE}'>
						<div class='row-fluid'>
							<span class='span12'>
								{$LOCALFIELDINFO.mandatory = false}
								{$LOCALFIELDINFO.name = 'vehicle_not_shipping_count'}
								{$LOCALFIELDINFO.label = 'LBL_VEHICLE_NOTSHIPPINGCOUNT'}
								{$LOCALFIELDINFO.type = 'integer'}
								{$INFO = Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($LOCALFIELDINFO))}
								<input id='vehicle_not_shipping_count' type="number" min="0" max="" class='input-large' name='vehicle_not_shipping_count' value='0' data-fieldinfo={$INFO} data-validation-engine='validate[{if $LOCALFIELDINFO.mandatory}required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]'>
							</span>
						</div>
					</td>
				</tr>
				<tr class='vehicleContent defaultVehicleContent'>
					<td class='fieldLabel {$WIDTHTYPE}'>
						<label class='muted pull-right marginRight10px'>{vtranslate('LBL_VEHICLE_COMMENT', $MODULE)}</label>
					</td>
					<td class="fieldValue {$WIDTHTYPE}" colspan="3">
						{$LOCALFIELDINFO.mandatory = false}
						{$LOCALFIELDINFO.name = 'vehicle_comment'}
						{$LOCALFIELDINFO.label = 'LBL_VEHICLE_COMMENT'}
						{$LOCALFIELDINFO.type = 'text'}
						{$INFO = Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($LOCALFIELDINFO))}
						<textarea id="vehicle_comment" class="span11 " name="vehicle_comment" data-fieldinfo={$INFO} data-validation-engine='validate[{if $LOCALFIELDINFO.mandatory}required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]'></textarea>
					</td>
				</tr>
				<input id="vehicle_id" type="hidden" name="vehicle_id" value="none">
			</tbody>
		{foreach key=VEHICLE_INDEX item=VEHICLE from=$CORP_VEHICLES}
			<tbody class='vehicleBlock {if ($IS_HIDDEN)} hide {/if}' blocktoggleid="corpVehicleTable">
					<tr class='fieldLabel' colspan='4'>
						<td colspan='4' class='blockHeader'>
							<img class='cursorPointer alignMiddle vehicleToggle{if !($IS_HIDDEN)} hide {/if} '  src='{vimage_path('arrowRight.png')}' data-mode='hide' data-id='defaultVehicle' blocktoggleid="vehicleTable{$VEHICLE_INDEX+1}">
							<img class='cursorPointer alignMiddle vehicleToggle{if ($IS_HIDDEN)} hide {/if}'  src='{vimage_path('arrowDown.png')}' data-mode='show' data-id='defaultVehicle' blocktoggleid="vehicleTable{$VEHICLE_INDEX+1}">
							<span class='vehicleTitle'><b>&nbsp;&nbsp;&nbsp;{vtranslate('LBL_VEHICLE_HEADER', $MODULE)} {$VEHICLE['vehicle_id']}</b></span>
							<span><a style='float: right; padding: 3px'><i title='Delete' class='deleteVehicleButton icon-trash'></i></a></span>
							<input type="hidden" name="vehicle_id" value="{$VEHICLE['vehicle_id']}">
						</td>
					</tr>
					<tr class='vehicleContent {if ($IS_HIDDEN)} hide {/if}' blocktoggleid="vehicleTable{$VEHICLE_INDEX+1}">
						<td class='fieldLabel {$WIDTHTYPE}'>
							<label class='muted pull-right marginRight10px'>{vtranslate('LBL_VEHICLE_MAKE', $MODULE)}</label>
						</td>
						<td class='fieldValue {$WIDTHTYPE}'>
							<div class='row-fluid'>
								<span class='span12'>
									{$LOCALFIELDINFO.mandatory = false}
									{$LOCALFIELDINFO.name = 'vehicle_make'}
									{$LOCALFIELDINFO.label = 'LBL_VEHICLE_MAKE'}
									{$LOCALFIELDINFO.type = 'string'}
									{$INFO = Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($LOCALFIELDINFO))}

									<input type="hidden" name="vehicle_make" value="none">
									<select class="chzn-select" style="text-align:left" id="vehicle_make_{$VEHICLE['vehicle_id']}" name="vehicle_make_{$VEHICLE['vehicle_id']}" data-fieldinfo="" data-selected-value="">
										<option value="" style="text-align:left">Select an Option</option>
										<option style="text-align:left" value="ACURA"{if $VEHICLE['make'] == 'ACURA'}selected{/if}>Acura</option>
										<option style="text-align:left" value="AUDI"{if $VEHICLE['make'] == 'AUDI'}selected{/if}>Audi</option>
										<option style="text-align:left" value="BENTLEY"{if $VEHICLE['make'] == 'BENTLEY'}selected{/if}>Bentley</option>
										<option style="text-align:left" value="BMW"{if $VEHICLE['make'] == 'BMW'}selected{/if}>BMW</option>
										<option style="text-align:left" value="BUICK"{if $VEHICLE['make'] == 'BUICK'}selected{/if}>Buick</option>
										<option style="text-align:left" value="CADILLAC"{if $VEHICLE['make'] == 'CADILLAC'}selected{/if}>Cadillac</option>
										<option style="text-align:left" value="CHEVROLET"{if $VEHICLE['make'] == 'CHEVROLET'}selected{/if}>Chevrolet</option>
										<option style="text-align:left" value="CHRYSLER"{if $VEHICLE['make'] == 'CHRYSLER'}selected{/if}>Chrysler</option>
										<option style="text-align:left" value="DODGE"{if $VEHICLE['make'] == 'DODGE'}selected{/if}>Dodge</option>
										<option style="text-align:left" value="FERRARI"{if $VEHICLE['make'] == 'FERRARI'}selected{/if}>Ferrari</option>
										<option style="text-align:left" value="FORD"{if $VEHICLE['make'] == 'FORD'}selected{/if}>Ford</option>
										<option style="text-align:left" value="GMC"{if $VEHICLE['make'] == 'GMC'}selected{/if}>GMC</option>
										<option style="text-align:left" value="HONDA"{if $VEHICLE['make'] == 'HONDA'}selected{/if}>Honda</option>
										<option style="text-align:left" value="HUMMER"{if $VEHICLE['make'] == 'HUMMER'}selected{/if}>Hummer</option>
										<option style="text-align:left" value="HYUNDAI"{if $VEHICLE['make'] == 'HYUNDAI'}selected{/if}>Hyundai</option>
										<option style="text-align:left" value="INFINITI"{if $VEHICLE['make'] == 'INFINITI'}selected{/if}>Infiniti</option>
										<option style="text-align:left" value="ISUZU"{if $VEHICLE['make'] == 'ISUZU'}selected{/if}>Isuzu</option>
										<option style="text-align:left" value="JAGUAR"{if $VEHICLE['make'] == 'JAGUAR'}selected{/if}>Jaguar</option>
										<option style="text-align:left" value="JEEP"{if $VEHICLE['make'] == 'JEEP'}selected{/if}>Jeep</option>
										<option style="text-align:left" value="KIA"{if $VEHICLE['make'] == 'KIA'}selected{/if}>Kia</option>
										<option style="text-align:left" value="LAND ROVER"{if $VEHICLE['make'] == 'LAND ROVER'}selected{/if}>Land Rover</option>
										<option style="text-align:left" value="LEXUS"{if $VEHICLE['make'] == 'LEXUS'}selected{/if}>Lexus</option>
										<option style="text-align:left" value="LINCOLN"{if $VEHICLE['make'] == 'LINCOLN'}selected{/if}>Lincoln</option>
										<option style="text-align:left" value="LOTUS"{if $VEHICLE['make'] == 'LOTUS'}selected{/if}>Lotus</option>
										<option style="text-align:left" value="MASERATI"{if $VEHICLE['make'] == 'MASERATI'}selected{/if}>Maserati</option>
										<option style="text-align:left" value="MAZDA"{if $VEHICLE['make'] == 'MAZDA'}selected{/if}>Mazda</option>
										<option style="text-align:left" value="MERCEDES"{if $VEHICLE['make'] == 'MERCEDES'}selected{/if}>Mercedes</option>
										<option style="text-align:left" value="MERCURY"{if $VEHICLE['make'] == 'MERCURY'}selected{/if}>Mercury</option>
										<option style="text-align:left" value="MINI"{if $VEHICLE['make'] == 'MINI'}selected{/if}>Mini</option>
										<option style="text-align:left" value="MITSUBISHI"{if $VEHICLE['make'] == 'MITSUBISHI'}selected{/if}>Mitsubishi</option>
										<option style="text-align:left" value="NISSAN"{if $VEHICLE['make'] == 'NISSAN'}selected{/if}>Nissan</option>
										<option style="text-align:left" value="OLDSMOBILE"{if $VEHICLE['make'] == 'OLDSMOBILE'}selected{/if}>Oldsmobile</option>
										<option style="text-align:left" value="OTHER"{if $VEHICLE['make'] == 'OTHER'}selected{/if}>Other</option>
										<option style="text-align:left" value="PONTIAC"{if $VEHICLE['make'] == 'PONTIAC'}selected{/if}>Pontiac</option>
										<option style="text-align:left" value="PORSCHE"{if $VEHICLE['make'] == 'PORSCHE'}selected{/if}>Porsche</option>
										<option style="text-align:left" value="ROLLSROYCE"{if $VEHICLE['make'] == 'ROLLSROYCE'}selected{/if}>Rolls-Royce</option>
										<option style="text-align:left" value="SAAB"{if $VEHICLE['make'] == 'SAAB'}selected{/if}>Saab</option>
										<option style="text-align:left" value="SATURN"{if $VEHICLE['make'] == 'SATURN'}selected{/if}>Saturn</option>
										<option style="text-align:left" value="SCION"{if $VEHICLE['make'] == 'SCION'}selected{/if}>Scion</option>
										<option style="text-align:left" value="SUBARU"{if $VEHICLE['make'] == 'SUBARU'}selected{/if}>Subaru</option>
										<option style="text-align:left" value="SUZUKI"{if $VEHICLE['make'] == 'SUZUKI'}selected{/if}>Suzuki</option>
										<option style="text-align:left" value="TOYOTA"{if $VEHICLE['make'] == 'TOYOTA'}selected{/if}>Toyota</option>
										<option style="text-align:left" value="VOLKSWAGEN"{if $VEHICLE['make'] == 'VOLKSWAGEN'}selected{/if}>Volkswagen</option>
										<option style="text-align:left" value="VOLVO"{if $VEHICLE['make'] == 'VOLVO'}selected{/if}>Volvo</option>
									</select>
								</span>
							</div>
						</td>
						<td class='fieldLabel {$WIDTHTYPE}'>
							<label class='muted pull-right marginRight10px'>{vtranslate('LBL_VEHICLE_MODEL', $MODULE)}</label>
						</td>
						<td class='fieldValue {$WIDTHTYPE}'>
							<div class='row-fluid'>
								<span class='span12'>
									{$LOCALFIELDINFO.mandatory = false}
									{$LOCALFIELDINFO.name = 'vehicle_model'}
									{$LOCALFIELDINFO.label = 'LBL_VEHICLE_MODEL'}
									{$LOCALFIELDINFO.type = 'string'}
									{$INFO = Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($LOCALFIELDINFO))}
									<input id='vehicle_model_{$VEHICLE['vehicle_id']}' type='text' class='input-large' name='vehicle_model_{$VEHICLE['vehicle_id']}' value='{$VEHICLE['model']}' data-fieldinfo={$INFO} data-validation-engine='validate[{if $LOCALFIELDINFO.mandatory}required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]'>
								</span>
							</div>
						</td>
					</tr>
					<tr class='vehicleContent{if ($IS_HIDDEN)} hide {/if}' blocktoggleid="vehicleTable{$VEHICLE_INDEX+1}">
						<td class='fieldLabel {$WIDTHTYPE}'>
							<label class='muted pull-right marginRight10px'>{vtranslate('LBL_VEHICLE_YEAR', $MODULE)}</label>
						</td>
						<td class='fieldValue {$WIDTHTYPE}'>
							<div class='row-fluid'>
								<span class='span12'>
									{$LOCALFIELDINFO.mandatory = false}
									{$LOCALFIELDINFO.name = 'vehicle_year'}
									{$LOCALFIELDINFO.label = 'LBL_VEHICLE_YEAR'}
									{$LOCALFIELDINFO.type = 'integer'}
									{$INFO = Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($LOCALFIELDINFO))}
									<input id='vehicle_year_{$VEHICLE['vehicle_id']}' type="number" min="1900" max="{date('Y')+1}" class='input-large' name='vehicle_year_{$VEHICLE['vehicle_id']}' value='{$VEHICLE['year']}' data-fieldinfo={$INFO} data-validation-engine='validate[{if $LOCALFIELDINFO.mandatory}required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]'>
								</span>
							</div>
						</td>
						<td class='fieldLabel {$WIDTHTYPE}'>
							<label class='muted pull-right marginRight10px'>{vtranslate('LBL_VEHICLE_WEIGHT', $MODULE)}</label>
						</td>
						<td class='fieldValue {$WIDTHTYPE}'>
							<div class='row-fluid'>
								<span class='span12'>
									{$LOCALFIELDINFO.mandatory = false}
									{$LOCALFIELDINFO.name = 'vehicle_weight'}
									{$LOCALFIELDINFO.label = 'LBL_VEHICLE_WEIGHT'}
									{$LOCALFIELDINFO.type = 'decimal'}
									{$INFO = Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($LOCALFIELDINFO))}
									<input id='vehicle_weight_{$VEHICLE['vehicle_id']}' type="number" min="0" max="" step=".5" class='input-large' name='vehicle_weight_{$VEHICLE['vehicle_id']}' value='{$VEHICLE['weight']}' data-fieldinfo={$INFO} data-validation-engine='validate[{if $LOCALFIELDINFO.mandatory}required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]'>
								</span>
							</div>
						</td>
					</tr>
					<tr class='vehicleContent{if ($IS_HIDDEN)} hide {/if}' blocktoggleid="vehicleTable{$VEHICLE_INDEX+1}">
						<td class='fieldLabel {$WIDTHTYPE}'>
							<label class='muted pull-right marginRight10px'>{vtranslate('LBL_VEHICLE_CUBE', $MODULE)}</label>
						</td>
						<td class='fieldValue {$WIDTHTYPE}'>
							<div class='row-fluid'>
								<span class='span12'>
									{$LOCALFIELDINFO.mandatory = false}
									{$LOCALFIELDINFO.name = 'vehicle_cube'}
									{$LOCALFIELDINFO.label = 'LBL_VEHICLE_CUBE'}
									{$LOCALFIELDINFO.type = 'decimal'}
									{$INFO = Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($LOCALFIELDINFO))}
									<input id='vehicle_cube_{$VEHICLE['vehicle_id']}' type="number" min="0" max="" class='input-large' name='vehicle_cube_{$VEHICLE['vehicle_id']}' value='{$VEHICLE['cube']}' data-fieldinfo={$INFO} data-validation-engine='validate[{if $LOCALFIELDINFO.mandatory}required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]'>
								</span>
							</div>
						</td>
						<td class='fieldLabel {$WIDTHTYPE}'>
							<label class='muted pull-right marginRight10px'>{vtranslate('LBL_VEHICLE_SERVICE', $MODULE)}</label>
						</td>
						<td class='fieldValue {$WIDTHTYPE}'>
							<div class='row-fluid'>
								<span class='span12'>
									{$LOCALFIELDINFO.mandatory = false}
									{$LOCALFIELDINFO.name = 'vehicle_service'}
									{$LOCALFIELDINFO.label = 'LBL_VEHICLE_SERVICE'}
									{$LOCALFIELDINFO.type = 'string'}
									{$INFO = Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($LOCALFIELDINFO))}

									<input type="hidden" name="vehicle_service_{$VEHICLE['vehicle_id']}" value="none">
									<select class="chzn-select" style="text-align:left" id="vehicle_service_{$VEHICLE['vehicle_id']}" name="vehicle_service_{$VEHICLE['vehicle_id']}" data-fieldinfo="" data-selected-value="">
										<option value="" style="text-align:left">Select an Option</option>
										<option style="text-align:left" value="Contract"{if $VEHICLE['service'] == 'Contract'}selected{/if}>Contract</option>
										<option style="text-align:left" value="Budget"{if $VEHICLE['service'] == 'Budget'}selected{/if}>Budget</option>
										<option style="text-align:left" value="Premier"{if $VEHICLE['service'] == 'Premier'}selected{/if}>Premier</option>
										<option style="text-align:left" value="Bulker"{if $VEHICLE['service'] == 'Bulker'}selected{/if}>Bulker</option>
									</select>
								</span>
							</div>
						</td>
					</tr>
					<tr class='vehicleContent{if ($IS_HIDDEN)} hide {/if}' blocktoggleid="vehicleTable{$VEHICLE_INDEX+1}">
						<td class='fieldLabel {$WIDTHTYPE}'>
							<label class='muted pull-right marginRight10px'>{vtranslate('LBL_VEHICLE_DVPVALUE', $MODULE)}</label>
						</td>
						<td class='fieldValue {$WIDTHTYPE}'>
							<div class='row-fluid'>
								<span class='span12'>
									{$LOCALFIELDINFO.mandatory = false}
									{$LOCALFIELDINFO.name = 'vehicle_dvp_value'}
									{$LOCALFIELDINFO.label = 'LBL_VEHICLE_DVPVALUE'}
									{$LOCALFIELDINFO.type = 'integer'}
									{$INFO = Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($LOCALFIELDINFO))}
									<input id='vehicle_dvp_value_{$VEHICLE['vehicle_id']}' type="number" min="0" max="" class='input-large' name='vehicle_dvp_value_{$VEHICLE['vehicle_id']}' value='{$VEHICLE['dvp_value']}' data-fieldinfo={$INFO} data-validation-engine='validate[{if $LOCALFIELDINFO.mandatory}required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]'>
								</span>
							</div>
						</td>
						<td class="fieldLabel">
							<label class='muted pull-right marginRight10px'>{vtranslate('LBL_VEHICLE_CARONVAN', $MODULE)}</label>
						</td>
						<td class="fieldValue {$WIDTHTYPE}">
							{$LOCALFIELDINFO.mandatory = false}
							{$LOCALFIELDINFO.name = 'vehicle_car_on_van'}
							{$LOCALFIELDINFO.label = 'LBL_VEHICLE_CARONVAN'}
							{$LOCALFIELDINFO.type = 'boolean'}
							{$INFO = Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($LOCALFIELDINFO))}
							<input type="hidden" name="vehicle_car_on_van" value="0">
							<input id='vehicle_car_on_van_{$VEHICLE['vehicle_id']}' type="checkbox"{if $VEHICLE['car_on_van'] == 'on'} checked{/if} name="vehicle_car_on_van_{$VEHICLE['vehicle_id']}" data-fieldinfo={$INFO} data-validation-engine='validate[{if $LOCALFIELDINFO.mandatory}required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]'>
						</td>
					</tr>
					<tr class='vehicleContent{if ($IS_HIDDEN)} hide {/if}' blocktoggleid="vehicleTable{$VEHICLE_INDEX+1}">
						<td class='fieldLabel {$WIDTHTYPE}'>
							<label class='muted pull-right marginRight10px'>{vtranslate('LBL_VEHICLE_OVERSIZECLASS', $MODULE)}</label>
						</td>
						<td class='fieldValue {$WIDTHTYPE}'>
							<div class='row-fluid'>
								<span class='span12'>
									{$LOCALFIELDINFO.mandatory = false}
									{$LOCALFIELDINFO.name = 'vehicle_oversize_class'}
									{$LOCALFIELDINFO.label = 'LBL_VEHICLE_OVERSIZECLASS'}
									{$LOCALFIELDINFO.type = 'string'}
									{$INFO = Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($LOCALFIELDINFO))}

									<input type="hidden" name="vehicle_oversize_class_{$VEHICLE['vehicle_id']}" value="none">
									<select class="chzn-select" style="text-align:left" id="vehicle_oversize_class_{$VEHICLE['vehicle_id']}" name="vehicle_oversize_class_{$VEHICLE['vehicle_id']}" data-fieldinfo="" data-selected-value="">
										<option value="" style="text-align:left">Select an Option</option>
										<option style="text-align:left" value="None"{if $VEHICLE['oversize_class'] == 'None'} selected{/if}>None</option>
										<option style="text-align:left" value="I"{if $VEHICLE['oversize_class'] == 'I'} selected{/if}>I</option>
										<option style="text-align:left" value="II"{if $VEHICLE['oversize_class'] == 'II'} selected{/if}>II</option>
										<option style="text-align:left" value="III"{if $VEHICLE['oversize_class'] == 'III'} selected{/if}>III</option>
										<option style="text-align:left" value="IV"{if $VEHICLE['oversize_class'] == 'IV'} selected{/if}>IV</option>
									</select>
								</span>
							</div>
						</td>
						<td class="fieldLabel">
							<label class='muted pull-right marginRight10px'>{vtranslate('LBL_VEHICLE_INOPERABLE', $MODULE)}</label>
						</td>
						<td class="fieldValue {$WIDTHTYPE}">
							{$LOCALFIELDINFO.mandatory = false}
							{$LOCALFIELDINFO.name = 'vehicle_inoperable'}
							{$LOCALFIELDINFO.label = 'LBL_VEHICLE_INOPERABLE'}
							{$LOCALFIELDINFO.type = 'boolean'}
							{$INFO = Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($LOCALFIELDINFO))}
							<input type="hidden" name="vehicle_inoperable_{$VEHICLE['vehicle_id']}" value="0">
							<input id='vehicle_inoperable_{$VEHICLE['vehicle_id']}' type="checkbox"{if $VEHICLE['inoperable'] == 'on'} checked{/if} name="vehicle_inoperable_{$VEHICLE['vehicle_id']}" data-fieldinfo={$INFO} data-validation-engine='validate[{if $LOCALFIELDINFO.mandatory}required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]'>
						</td>
					</tr>
					<tr class='vehicleContent{if ($IS_HIDDEN)} hide {/if}' blocktoggleid="vehicleTable{$VEHICLE_INDEX+1}">
						<td class='fieldLabel {$WIDTHTYPE}'>
							<label class='muted pull-right marginRight10px'>{vtranslate('LBL_VEHICLE_LENGTH', $MODULE)}</label>
						</td>
						<td class='fieldValue {$WIDTHTYPE}'>
							<div class='row-fluid'>
								<span class='span12'>
									{$LOCALFIELDINFO.mandatory = false}
									{$LOCALFIELDINFO.name = 'vehicle_length'}
									{$LOCALFIELDINFO.label = 'LBL_VEHICLE_LENGTH'}
									{$LOCALFIELDINFO.type = 'integer'}
									{$INFO = Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($LOCALFIELDINFO))}
									<input id='vehicle_length_{$VEHICLE['vehicle_id']}' type="number" min="0" max="" class='input-large' name='vehicle_length_{$VEHICLE['vehicle_id']}' value='{$VEHICLE['length']}' data-fieldinfo={$INFO} data-validation-engine='validate[{if $LOCALFIELDINFO.mandatory}required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]'>
								</span>
							</div>
						</td>
						<td class='fieldLabel {$WIDTHTYPE}'>
							<label class='muted pull-right marginRight10px'>{vtranslate('LBL_VEHICLE_WIDTH', $MODULE)}</label>
						</td>
						<td class='fieldValue {$WIDTHTYPE}'>
							<div class='row-fluid'>
								<span class='span12'>
									{$LOCALFIELDINFO.mandatory = false}
									{$LOCALFIELDINFO.name = 'vehicle_width'}
									{$LOCALFIELDINFO.label = 'LBL_VEHICLE_WIDTH'}
									{$LOCALFIELDINFO.type = 'integer'}
									{$INFO = Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($LOCALFIELDINFO))}
									<input id='vehicle_width_{$VEHICLE['vehicle_id']}' type="number" min="0" max="" class='input-large' name='vehicle_width_{$VEHICLE['vehicle_id']}' value='{$VEHICLE['width']}' data-fieldinfo={$INFO} data-validation-engine='validate[{if $LOCALFIELDINFO.mandatory}required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]'>
								</span>
							</div>
						</td>
					</tr>
					<tr class='vehicleContent{if ($IS_HIDDEN)} hide {/if}' blocktoggleid="vehicleTable{$VEHICLE_INDEX+1}">
						<td class='fieldLabel {$WIDTHTYPE}'>
							<label class='muted pull-right marginRight10px'>{vtranslate('LBL_VEHICLE_HEIGHT', $MODULE)}</label>
						</td>
						<td class='fieldValue {$WIDTHTYPE}'>
							<div class='row-fluid'>
								<span class='span12'>
									{$LOCALFIELDINFO.mandatory = false}
									{$LOCALFIELDINFO.name = 'vehicle_height'}
									{$LOCALFIELDINFO.label = 'LBL_VEHICLE_HEIGHT'}
									{$LOCALFIELDINFO.type = 'integer'}
									{$INFO = Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($LOCALFIELDINFO))}
									<input id='vehicle_height_{$VEHICLE['vehicle_id']}' type="number" min="0" max="" class='input-large' name='vehicle_height_{$VEHICLE['vehicle_id']}' value='{$VEHICLE['height']}' data-fieldinfo={$INFO} data-validation-engine='validate[{if $LOCALFIELDINFO.mandatory}required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]'>
								</span>
							</div>
						</td>
						<td class='fieldLabel {$WIDTHTYPE}'>
							<label class='muted pull-right marginRight10px'>{vtranslate('LBL_VEHICLE_CHARGE', $MODULE)}</label>
						</td>
						<td class='fieldValue {$WIDTHTYPE}'>
							<div class='input-prepend input-prepend-centered'>
								{$LOCALFIELDINFO.mandatory = false}
								{$LOCALFIELDINFO.name = 'vehicle_charge'}
								{$LOCALFIELDINFO.label = 'LBL_VEHICLE_CHARGE'}
								{$LOCALFIELDINFO.type = 'decimal'}
								{$INFO = Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($LOCALFIELDINFO))}
								<span class='add-on'>$</span>
								<input type='number' step="any" class='input-medium currencyField' style='width:80%;float:left' name='vehicle_charge_{$VEHICLE['vehicle_id']}' value='{$VEHICLE['charge']}' data-decimal-seperator="." data-group-seperator="," data-number-of-decimal-places="2" />
							</div>
						</td>
					</tr>
					<tr class='vehicleContent{if ($IS_HIDDEN)} hide {/if}' blocktoggleid="vehicleTable{$VEHICLE_INDEX+1}">
						<td class='fieldLabel {$WIDTHTYPE}'>
							<label class='muted pull-right marginRight10px'>{vtranslate('LBL_VEHICLE_SHIPPINGCOUNT', $MODULE)}</label>
						</td>
						<td class='fieldValue {$WIDTHTYPE}'>
							<div class='row-fluid'>
								<span class='span12'>
									{$LOCALFIELDINFO.mandatory = false}
									{$LOCALFIELDINFO.name = 'vehicle_shipping_count'}
									{$LOCALFIELDINFO.label = 'LBL_VEHICLE_SHIPPINGCOUNT'}
									{$LOCALFIELDINFO.type = 'integer'}
									{$INFO = Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($LOCALFIELDINFO))}
									<input id='vehicle_shipping_coun_{$VEHICLE['vehicle_id']}t' type="number" min="0" max="" class='input-large' name='vehicle_shipping_count_{$VEHICLE['vehicle_id']}' value='{$VEHICLE['shipping_count']}' data-fieldinfo={$INFO} data-validation-engine='validate[{if $LOCALFIELDINFO.mandatory}required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]'>
								</span>
							</div>
						</td>
						<td class='fieldLabel {$WIDTHTYPE}'>
							<label class='muted pull-right marginRight10px'>{vtranslate('LBL_VEHICLE_NOTSHIPPINGCOUNT', $MODULE)}</label>
						</td>
						<td class='fieldValue {$WIDTHTYPE}'>
							<div class='row-fluid'>
								<span class='span12'>
									{$LOCALFIELDINFO.mandatory = false}
									{$LOCALFIELDINFO.name = 'vehicle_not_shipping_count'}
									{$LOCALFIELDINFO.label = 'LBL_VEHICLE_NOTSHIPPINGCOUNT'}
									{$LOCALFIELDINFO.type = 'integer'}
									{$INFO = Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($LOCALFIELDINFO))}
									<input id='vehicle_not_shipping_count_{$VEHICLE['vehicle_id']}' type="number" min="0" max="" class='input-large' name='vehicle_not_shipping_count_{$VEHICLE['vehicle_id']}' value='{$VEHICLE['not_shipping_count']}' data-fieldinfo={$INFO} data-validation-engine='validate[{if $LOCALFIELDINFO.mandatory}required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]'>
								</span>
							</div>
						</td>
					</tr>
					<tr class='vehicleContent{if ($IS_HIDDEN)} hide {/if}' blocktoggleid="vehicleTable{$VEHICLE_INDEX+1}">
						<td class='fieldLabel {$WIDTHTYPE}'>
							<label class='muted pull-right marginRight10px'>{vtranslate('LBL_VEHICLE_COMMENT', $MODULE)}</label>
						</td>
						<td class="fieldValue {$WIDTHTYPE}" colspan="3">
							{$LOCALFIELDINFO.mandatory = false}
							{$LOCALFIELDINFO.name = 'vehicle_comment'}
							{$LOCALFIELDINFO.label = 'LBL_VEHICLE_COMMENT'}
							{$LOCALFIELDINFO.type = 'text'}
							{$INFO = Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($LOCALFIELDINFO))}
							<textarea id="vehicle_comment_{$VEHICLE['vehicle_id']}" class="span11 " name="vehicle_comment_{$VEHICLE['vehicle_id']}" data-fieldinfo={$INFO} data-validation-engine='validate[{if $LOCALFIELDINFO.mandatory}required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]'>{$VEHICLE['comment']}</textarea>
						</td>
					</tr>
					<input id="vehicle_id_{$VEHICLE['vehicle_id']}" type="hidden" name="vehicle_id_{$VEHICLE['vehicle_id']}" value="{$VEHICLE['vehicle_id']}">
				</tbody>
		{/foreach}
		</tbody>
	</table>
	<br/>
{/strip}