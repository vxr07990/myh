{strip}
{assign var=HAS_CONTENT value=(!$BLOCK_SUBLIST || $BLOCK_SUBLIST['AUTO_TRANSPORT_VEHICLES'])}
<div id="contentHolder_AUTO_TRANSPORT_VEHICLES" class="sectionContentHolder {$CONTENT_DIV_CLASS} {if !$ALWAYS_SHOW_CONTENT_DIV}hide{/if} {if !$HAS_CONTENT}inactive{/if}">
{if $HAS_CONTENT}
{if $VEHICLE_LOOKUP}
	<table class='table table-bordered equalSplit detailview-table'>
		<thead>
			<tr>
				<th class='blockHeader' colspan='4'>
					<img class="cursorPointer alignMiddle blockToggle {if !($IS_HIDDEN)} hide {/if} "  src="{vimage_path('arrowRight.png')}" data-mode="hide" data-id="auto-trans">
					<img class="cursorPointer alignMiddle blockToggle {if ($IS_HIDDEN)} hide {/if}"  src="{vimage_path('arrowDown.png')}" data-mode="show" data-id="auto-trans">
					&nbsp;&nbsp;{vtranslate('LBL_VEHICLE_BLOCK','VehicleLookup')}
					{if $MODULE_NAME eq 'Estimates'}<span style="width:55%;clear:right;float:right;"><button type="button" style="float:left;width:20%" id="vehicleRatingButton" class="interstateRateBtns interstateRateDetail">Get Rate</button></span>{/if}
				</th>
			</tr>
		</thead>
		{foreach key=VEHICLE_INDEX item=VEHICLE_ITEM from=$VEHICLE_LIST}
			<tbody class='vehicleBlock'>
				<tr class="fieldLabel" colspan="4">
					<td colspan="4" class="cbxblockhead">
						<span class="vehicleTitle"><b>&nbsp;&nbsp;&nbsp;{vtranslate('LBL_VEHICLE_HEADER', 'VehicleLookup')} {$VEHICLE_INDEX+1}</b></span>
					</td>
				</tr>
				<tr class='vehicleContent'>
					<td class='fieldLabel {$WIDTHTYPE}'>
						<label class='muted pull-right marginRight10px'>{vtranslate('LBL_VEHICLE_MAKE', 'VehicleLookup')}</label>
					</td>
					<td class='fieldValue {$WIDTHTYPE}'>
						<span class='value' data-field-type='string'>
							{$VEHICLE_ITEM['vehicle_make']}
						</span>
					</td>
					<td class='fieldLabel {$WIDTHTYPE}'>
						<label class='muted pull-right marginRight10px'>{vtranslate('LBL_VEHICLE_MODEL', 'VehicleLookup')}</label>
					</td>
					<td class='fieldValue {$WIDTHTYPE}'>
						<span class='value' data-field-type='string'>
							{$VEHICLE_ITEM['vehicle_model']}
						</span>
					</td>
				</tr>
				<tr class='vehicleContent'>
					<td class='fieldLabel {$WIDTHTYPE}'>
						<label class='muted pull-right marginRight10px'>{vtranslate('LBL_VEHICLE_YEAR', 'VehicleLookup')}</label>
					</td>
					<td class='fieldValue {$WIDTHTYPE}'>
						<span class='value' data-field-type='integer'>
							{$VEHICLE_ITEM['vehicle_year']}
						</span>
					</td>
					<td class='fieldLabel {$WIDTHTYPE}'>
						<label class='muted pull-right marginRight10px'>{vtranslate('LBL_VEHICLE_VIN', 'VehicleLookup')}</label>
					</td>
					<td class='fieldValue {$WIDTHTYPE}'>
						<span class='value' data-field-type='string'>
							{$VEHICLE_ITEM['vehicle_vin']}
						</span>
					</td>
				</tr>
				<tr class='vehicleContent'>
					<td class='fieldLabel {$WIDTHTYPE}'>
						<label class='muted pull-right marginRight10px'>{vtranslate('LBL_VEHICLE_COLOR', 'VehicleLookup')}</label>
					</td>
					<td class='fieldValue {$WIDTHTYPE}'>
						<span class='value' data-field-type='string'>
							{$VEHICLE_ITEM['vehicle_color']}
						</span>
					</td>
					<td class='fieldLabel {$WIDTHTYPE}'>
						<label class='muted pull-right marginRight10px'>{vtranslate('LBL_VEHICLE_ODOMETER', 'VehicleLookup')}</label>
					</td>
					<td class='fieldValue {$WIDTHTYPE}'>
						<span class='value' data-field-type='decimal'>
							{$VEHICLE_ITEM['vehicle_odometer']}
						</span>
					</td>
				</tr>
				<tr class='vehicleContent'>
					<td class='fieldLabel {$WIDTHTYPE}'>
						<label class='muted pull-right marginRight10px'>{vtranslate('LBL_VEHICLE_LSTATE', 'VehicleLookup')}</label>
					</td>
					<td class='fieldValue {$WIDTHTYPE}'>
						<span class='value' data-field-type='string'>
							{$VEHICLE_ITEM['license_state']}
						</span>
					</td>
					<td class='fieldLabel {$WIDTHTYPE}'>
						<label class='muted pull-right marginRight10px'>{vtranslate('LBL_VEHICLE_LNUMBER', 'VehicleLookup')}</label>
					</td>
					<td class='fieldValue {$WIDTHTYPE}'>
						<span class='value' data-field-type='string'>
							{$VEHICLE_ITEM['license_number']}
						</span>
					</td>
				</tr>
				<tr class='vehicleContent'>
					<td class='fieldLabel {$WIDTHTYPE}'>
						<label class='muted pull-right marginRight10px'>{vtranslate('LBL_VEHICLE_TYPE', 'VehicleLookup')}</label>
					</td>
					<td class='fieldValue {$WIDTHTYPE}'>
						<span class='value' data-field-type='picklist'>
							{$VEHICLE_ITEM['vehicle_type']}
						</span>
					</td>
					<td class='fieldLabel {$WIDTHTYPE}'>
					</td>
					<td class='fieldValue {$WIDTHTYPE}'>
					</td>
				</tr>
				<input id="vehicle_id_{$VEHICLE_INDEX+1}" type="hidden" name="vehicle_id_{$VEHICLE_INDEX+1}" value="{$VEHICLE_ITEM['vehicleid']}">
			</tbody>
		{/foreach}
	</table>
	<br />
{/if}
	{/if}
	</div>
{/strip}