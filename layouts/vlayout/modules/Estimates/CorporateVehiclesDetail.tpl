{strip}
<table class='table table-bordered equalSplit detailview-table' name='corpVehicleTable' >
{assign var=IS_HIDDEN value=1}
		<thead>
			<th class='blockHeader' colspan='4'>
				<img class="cursorPointer alignMiddle blockToggle {if !($IS_HIDDEN)} hide {/if} "  src="{vimage_path('arrowRight.png')}" data-mode="hide" data-id="corpVehicles">
				<img class="cursorPointer alignMiddle blockToggle {if ($IS_HIDDEN)} hide {/if}"  src="{vimage_path('arrowDown.png')}" data-mode="show" data-id="corpVehicles">
				&nbsp;&nbsp;{vtranslate('LBL_VEHICLE_CORPORATE_BLOCK', $MODULE)}
			</th>
		</thead>
			{foreach key=VEHICLE_INDEX item=VEHICLE from=$CORP_VEHICLES}
				<tbody class='vehicleBlock{if ($IS_HIDDEN)} hide{/if}'>
					<tr class='fieldLabel' colspan='4'>
						<td colspan='4' class='blockHeader'>
							<span class='vehicleTitle'><b>&nbsp;&nbsp;&nbsp;{vtranslate('LBL_VEHICLE_HEADER', $MODULE)} {$VEHICLE['vehicle_id']}</b></span>
							<input type="hidden" name="vehicle_id" value="{$VEHICLE['vehicle_id']}">
						</td>
					</tr>
					<tr class='vehicleContent'>
						<td class='fieldLabel {$WIDTHTYPE}'>
							<label class='muted pull-right marginRight10px'>{vtranslate('LBL_VEHICLE_MAKE', $MODULE)}</label>
						</td>
						<td class='fieldValue {$WIDTHTYPE}'>
							<span class='value' data-field-type='string'>{vtranslate($VEHICLE['make'], $MODULE)}</span>
						</td>
						<td class='fieldLabel {$WIDTHTYPE}'>
							<label class='muted pull-right marginRight10px'>{vtranslate('LBL_VEHICLE_MODEL', $MODULE)}</label>
						</td>
						<td class='fieldValue {$WIDTHTYPE}'>
							<span class='value' data-field-type='string'>{$VEHICLE['model']}</span>
						</td>
					</tr>
					<tr class='vehicleContent'>
						<td class='fieldLabel {$WIDTHTYPE}'>
							<label class='muted pull-right marginRight10px'>{vtranslate('LBL_VEHICLE_YEAR', $MODULE)}</label>
						</td>
						<td class='fieldValue {$WIDTHTYPE}'>
							<span class='value' data-field-type='string'>{$VEHICLE['year']}</span>
						</td>
						<td class='fieldLabel {$WIDTHTYPE}'>
							<label class='muted pull-right marginRight10px'>{vtranslate('LBL_VEHICLE_WEIGHT', $MODULE)}</label>
						</td>
						<td class='fieldValue {$WIDTHTYPE}'>
							<span class='value' data-field-type='string'>{$VEHICLE['weight']}</span>
						</td>
					</tr>
					<tr class='vehicleContent'>
						<td class='fieldLabel {$WIDTHTYPE}'>
							<label class='muted pull-right marginRight10px'>{vtranslate('LBL_VEHICLE_CUBE', $MODULE)}</label>
						</td>
						<td class='fieldValue {$WIDTHTYPE}'>
							<span class='value' data-field-type='string'>{$VEHICLE['cube']}</span>
						</td>
						<td class='fieldLabel {$WIDTHTYPE}'>
							<label class='muted pull-right marginRight10px'>{vtranslate('LBL_VEHICLE_SERVICE', $MODULE)}</label>
						</td>
						<td class='fieldValue {$WIDTHTYPE}'>
							<span class='value' data-field-type='string'>{$VEHICLE['service']}</span>
						</td>
					</tr>
					<tr class='vehicleContent'>
						<td class='fieldLabel {$WIDTHTYPE}'>
							<label class='muted pull-right marginRight10px'>{vtranslate('LBL_VEHICLE_DVPVALUE', $MODULE)}</label>
						</td>
						<td class='fieldValue {$WIDTHTYPE}'>
							<span class='value' data-field-type='string'>{$VEHICLE['dvp_value']}</span>
						</td>
						<td class="fieldLabel">
							<label class='muted pull-right marginRight10px'>{vtranslate('LBL_VEHICLE_CARONVAN', $MODULE)}</label>
						</td>
						<td class="fieldValue {$WIDTHTYPE}">
							<span class='value' data-field-type='string'>{if $VEHICLE['car_on_van'] == 'on'}Yes{else}No{/if}</span>
						</td>
					</tr>
					<tr class='vehicleContent'>
						<td class='fieldLabel {$WIDTHTYPE}'>
							<label class='muted pull-right marginRight10px'>{vtranslate('LBL_VEHICLE_OVERSIZECLASS', $MODULE)}</label>
						</td>
						<td class='fieldValue {$WIDTHTYPE}'>
							<div class='row-fluid'>
								<span class='value' data-field-type='string'>{$VEHICLE['oversize_class']}</span>
							</div>
						</td>
						<td class="fieldLabel">
							<label class='muted pull-right marginRight10px'>{vtranslate('LBL_VEHICLE_INOPERABLE', $MODULE)}</label>
						</td>
						<td class="fieldValue {$WIDTHTYPE}">
							<span class='value' data-field-type='string'>{if $VEHICLE['inoperable'] == 'on'}Yes{else}No{/if}</span>
						</td>
					</tr>
					<tr class='vehicleContent'>
						<td class='fieldLabel {$WIDTHTYPE}'>
							<label class='muted pull-right marginRight10px'>{vtranslate('LBL_VEHICLE_LENGTH', $MODULE)}</label>
						</td>
						<td class='fieldValue {$WIDTHTYPE}'>
							<span class='value' data-field-type='string'>{$VEHICLE['length']}</span>
						</td>
						<td class='fieldLabel {$WIDTHTYPE}'>
							<label class='muted pull-right marginRight10px'>{vtranslate('LBL_VEHICLE_WIDTH', $MODULE)}</label>
						</td>
						<td class='fieldValue {$WIDTHTYPE}'>
							<span class='value' data-field-type='string'>{$VEHICLE['width']}</span>
						</td>
					</tr>
					<tr class='vehicleContent'>
						<td class='fieldLabel {$WIDTHTYPE}'>
							<label class='muted pull-right marginRight10px'>{vtranslate('LBL_VEHICLE_HEIGHT', $MODULE)}</label>
						</td>
						<td class='fieldValue {$WIDTHTYPE}'>
							<span class='value' data-field-type='string'>{$VEHICLE['height']}</span>
						</td>
						<td class='fieldLabel {$WIDTHTYPE}'>
							<label class='muted pull-right marginRight10px'>{vtranslate('LBL_VEHICLE_CHARGE', $MODULE)}</label>
						</td>
						<td class='fieldValue {$WIDTHTYPE}'>
							<span class='value' data-field-type='string'>{$VEHICLE['charge']}</span>
						</td>
					</tr>
					<tr class='vehicleContent'>
						<td class='fieldLabel {$WIDTHTYPE}'>
							<label class='muted pull-right marginRight10px'>{vtranslate('LBL_VEHICLE_SHIPPINGCOUNT', $MODULE)}</label>
						</td>
						<td class='fieldValue {$WIDTHTYPE}'>
							<div class='row-fluid'>
								<span class='value' data-field-type='string'>{$VEHICLE['shipping_count']}</span>
							</div>
						</td>
						<td class='fieldLabel {$WIDTHTYPE}'>
							<label class='muted pull-right marginRight10px'>{vtranslate('LBL_VEHICLE_NOTSHIPPINGCOUNT', $MODULE)}</label>
						</td>
						<td class='fieldValue {$WIDTHTYPE}'>
							<span class='value' data-field-type='string'>{$VEHICLE['not_shipping_count']}</span>
						</td>
					</tr>
					<tr class='vehicleContent'>
						<td class='fieldLabel {$WIDTHTYPE}'>
							<label class='muted pull-right marginRight10px'>{vtranslate('LBL_VEHICLE_COMMENT', $MODULE)}</label>
						</td>
						<td class="fieldValue {$WIDTHTYPE}" colspan="3">
							<span class='value' data-field-type='string'>{$VEHICLE['comment']}</span>
						</td>
					</tr>
					<input id="vehicle_id_{$VEHICLE['vehicle_id']}" type="hidden" name="vehicle_id_{$VEHICLE['vehicle_id']}" value="{$VEHICLE['vehicle_id']}">
				</tbody>
			{/foreach}
	</table>
	<br/>
{/strip}