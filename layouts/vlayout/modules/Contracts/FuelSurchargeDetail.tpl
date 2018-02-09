{if $FUEL_TABLE|@count gt 0}
	<table class='table table-bordered detailview-table' name="FuelSurchargeTable">
		<thead>
			<th class='blockHeader' colspan='8'>
				<img class="cursorPointer alignMiddle blockToggle {if !($IS_HIDDEN)} hide {/if} "  src="{vimage_path('arrowRight.png')}" data-mode="hide">
				<img class="cursorPointer alignMiddle blockToggle {if ($IS_HIDDEN)} hide {/if}"  src="{vimage_path('arrowDown.png')}" data-mode="show">
				&nbsp;&nbsp;Fuel Surcharge Lookup Table
			</th>
		</thead>
		<tbody>
			<tr>
				<td style='width:30%' id='fparentFromCost'>
					<b>From Cost</b>
				</td>
				<td style='width:30%' id='fparentToCost'>
					<b>To Cost</b>
				</td>
				<td style='width:35%' id='fparentRate'>
					<b>Rate</b>
				</td>
				<td style='width:23.75%' class='hide' id='fparentPercentage'>
					<b>Percentage</b>
				</td>
			</tr>
			{foreach item=FUEL_ROW key=ROW_NUM from=$FUEL_TABLE}
			<tr class='FuelSurchargeRow'>
				<td class='medium narrowWidthType' style="width:30%;">
					<span class="value">{$FUEL_ROW.from_cost}</span>
				</td>
				<td class='medium narrowWidthType' style="width:30%;">
					<span class="value">{$FUEL_ROW.to_cost}</span>
				</td>
				<td class='medium narrowWidthType' style="width:35%;">
					<span class="value">{$FUEL_ROW.rate}</span>
				</td>
				<td class='medium narrowWidthType{if !($FUEL_SURCHARGE_TYPE eq 'DOE - Fuel Percentage' OR $FUEL_SURCHARGE_TYPE eq 'DOE - Rate/Mile or Percentage')} hide{/if}' style="width:23.75%;text-align:center;">
					<span class="value">{$FUEL_ROW.percent}</span>
				</td>
			</tr>
			{/foreach}
		</tbody>
	</table>
	<br />
{/if}