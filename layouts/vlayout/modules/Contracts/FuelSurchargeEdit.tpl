<table class='table table-bordered blockContainer showInlineTable' name="FuelSurchargeTable">
	<thead>
		<th class='blockHeader' colspan='8'>
			<img class="cursorPointer alignMiddle blockToggle {if !($IS_HIDDEN)} hide {/if} "  src="{vimage_path('arrowRight.png')}" data-mode="hide">
			<img class="cursorPointer alignMiddle blockToggle {if ($IS_HIDDEN)} hide {/if}"  src="{vimage_path('arrowDown.png')}" data-mode="show">
			&nbsp;&nbsp;Fuel Surcharge Lookup Table
		</th>
	</thead>
	<tbody>
		<tr>
			<td colspan='4' style='padding:0' id='fuelButtonRow'>
				<button type='button' id='addFuelRow'>+</button>
				<button type='button' id='addFuelRow2' style='clear:right; float:right;'>+</button>
			</td>
		</tr>
		<tr>
			<td style='width:5%'>
				<input type="hidden" class="hide" name="numFuel" value="{$FUEL_TABLE|@count - 1}" />&nbsp;</td>
			<td style='width:30%' id='fparentFromCost'>
				<span class="redColor">*</span><b>From Cost</b>
			</td>
			<td style='width:30%' id='fparentToCost'>
				<span class="redColor">*</span><b>To Cost</b>
			</td>
			<td style='width:35%' id='fparentRate'>
				<span class="redColor">*</span><b>Rate</b>
			</td>
			<td style='width:23.75%' class='hide' id='fparentPercentage'>
				<span class="redColor">*</span><b>Percentage</b>
			</td>
		</tr>
		<tr class='hide defaultFuelSurchargeRow FuelSurchargeRow'>
			<td class='fieldValue' style="width:5%;text-align:center;margin:auto">
				<a class="deleteMiscChargeButton">
					<i title="Delete" class="icon-trash alignMiddle"></i>
				</a>
			</td>
			<td class='fieldValue' style="width:30%">
				<div class="row-fluid">
					<div class="input-prepend">
						<span class="span10">
							<span class="add-on">&#36;</span>
							<input name="FuelTableFromCost" class="input-medium currencyField" style='width:85%' type="text" value="" data-decimal-seperator="." data-group-seperator="," data-number-of-decimal-places="2" disabled />
						</span>
					</div>
				</div>
			</td>
			<td class='fieldValue' style="width:30%">
				<div class="row-fluid">
					<div class="input-prepend">
						<span class="span10">
							<span class="add-on">&#36;</span>
							<input name="FuelTableToCost" class="input-medium currencyField" style='width:85%' type="text" value="" data-decimal-seperator="." data-group-seperator="," data-number-of-decimal-places="2" disabled />
						</span>
					</div>
				</div>
			</td>
			<td class='fieldValue' style="width:35%;text-align:center">
				<input name="FuelTableRate" class="input-medium currencyField" style='width:85%' type="text" value="" data-decimal-seperator="." data-group-seperator="," data-number-of-decimal-places="2" disabled />
			</td>
			<td class='fieldValue hide' style="width:23.75%;text-align:center;">
				<input name="FuelTablePercent" class="input-large" style='width:85%' type="number" step='0.01' value="" data-decimal-seperator="." data-group-seperator="," data-number-of-decimal-places="2" />
				<span class="add-on">%</span>
			</td>
			<input type="hidden" name="FuelTableId" value="none" disabled />
		</tr>
		{foreach item=FUEL_ROW key=ROW_NUM from=$FUEL_TABLE}
		<tr class='FuelSurchargeRow'>
			<td class='fieldValue' style="width:5%;text-align:center;margin:auto">
				<a class="deleteMiscChargeButton">
					<i title="Delete" class="icon-trash alignMiddle"></i>
				</a>
			</td>
			<td class='fieldValue' style="width:30%;">
				<div class="row-fluid">
					<div class="input-prepend">
						<span class="span10">
							<span class="add-on">&#36;</span>
							<input name="FuelTableFromCost-{$ROW_NUM}" class="input-medium currencyField" style='width:85%' type="text" value="{$FUEL_ROW.from_cost}" data-decimal-seperator="." data-group-seperator="," data-number-of-decimal-places="2" />
						</span>
					</div>
				</div>
			</td>
			<td class='fieldValue' style="width:30%;">
				<div class="row-fluid">
					<div class="input-prepend">
						<span class="span10">
							<span class="add-on">&#36;</span>
							<input name="FuelTableToCost-{$ROW_NUM}" class="input-medium currencyField" style='width:85%' type="text" value="{$FUEL_ROW.to_cost}" data-decimal-seperator="." data-group-seperator="," data-number-of-decimal-places="2" />
						</span>
					</div>
				</div>
			</td>
			<td class='fieldValue' style="width:35%;text-align:center;">
				<input name="FuelTableRate-{$ROW_NUM}" class="input-large" style='width:85%' type="text" value="{$FUEL_ROW.rate}" data-decimal-seperator="." data-group-seperator="," data-number-of-decimal-places="4" />
			</td>
			<td class='fieldValue{if !($FUEL_SURCHARGE_TYPE eq 'DOE - Fuel Percentage' OR $FUEL_SURCHARGE_TYPE eq 'DOE - Rate/Mile or Percentage')} hide{/if}' style="width:23.75%;text-align:center;">
				<input name="FuelTablePercent-{$ROW_NUM}" class="input-large" style='width:85%' type="number" step='0.01' value="{$FUEL_ROW.percentage}" data-decimal-seperator="." data-group-seperator="," data-number-of-decimal-places="2" />
				<span class="add-on">%</span>
			</td>
			<input type="hidden" name="FuelId-{$ROW_NUM}" value="{$FUEL_ROW.line_item_id}"  />
		</tr>
		{/foreach}
	</tbody>
</table>
<br />