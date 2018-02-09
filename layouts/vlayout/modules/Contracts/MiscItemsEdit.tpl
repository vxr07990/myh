<table class='table table-bordered blockContainer showInlineTable misc' name="MiscItemsTable">
	<thead>
		<th class='blockHeader' colspan='8'>
			<img class="cursorPointer alignMiddle blockToggle {if !($IS_HIDDEN)} hide {/if} "  src="{vimage_path('arrowRight.png')}" data-mode="hide">
			<img class="cursorPointer alignMiddle blockToggle {if ($IS_HIDDEN)} hide {/if}"  src="{vimage_path('arrowDown.png')}" data-mode="show">
			&nbsp;&nbsp;Default Miscellaneous Items
		</th>
	</thead>
	<tbody id='qtyRateItemsTab'{if $IS_HIDDEN} class="hide" {/if}>
		<tr>
			<td colspan='8' style='padding:0'>
				<button type='button' id='addMiscItem'>+</button>
				<button type='button' id='addMiscItem2' style='clear:right; float:right;'>+</button>
			</td>
		</tr>
		<tr>
			<td style='width:5%'>
				<input type="hidden" class="hide" name="numMisc" value="{$MISC_CHARGES|@count - 1}" />&nbsp;</td>
			<td style='width:7.5%'>
				<b>Flat Charge</b>
			</td>
			<td style='width:7.5%'>
				<b>Qty/Rate</b>
			</td>
			<td style='width:25%'>
				<span class="redColor">*</span><b>Description</b>
			</td>
			<td style='width:20%'>
				<span class="redColor">*</span><b>Rate</b>
			</td>
			<td style='width:15%'>
				<span class="redColor">*</span><b>Qty</b>
			</td>
			<td style='width:5%'>
				<b>Disc</b>
			</td>
		</tr>
		<tr class='hide defaultMiscItem MiscItemRow'>
			<td class='fieldValue' style="width:5%;text-align:center;margin:auto">
				<a class="deleteMiscChargeButton">
					<i title="Delete" class="icon-trash alignMiddle"></i>
				</a>
			</td>
			<td class='fieldValue' style='text-align:center'>
				<input type="hidden" name="MiscFlatChargeOrQtyRate_prev" value="none" disabled />
				<input type="hidden" name="MiscId" value="none" disabled />
				<input type="radio" name="MiscFlatChargeOrQtyRate" value="0" disabled />
			</td>
			<td class='fieldValue' style='text-align:center'>
				<input type="radio" name="MiscFlatChargeOrQtyRate" value="1" disabled checked />
			</td>
			<td class='fieldValue' style='text-align:center'>
				<input type="text" class="input-large" name="MiscDescription" style="width:80%" disabled />
			</td>
			<td class='fieldValue'>
				<div class="row-fluid">
					<div class="input-prepend">
						<span class="span10">
							<span class="add-on">&#36;</span>
							<input name="MiscRate" class="input-medium currencyField" type="text" value="" data-decimal-seperator="." data-group-seperator="," data-number-of-decimal-places="2" disabled />
						</span>
					</div>
				</div>
			</td>
			<td class='fieldValue' style='text-align:center'>
				<input type="number" class="input-large" name="MiscQty" style="width:80%" disabled />
			</td>
			<td class='fieldValue' style='text-align:center'>
				<input type="hidden" name="MiscDiscounted" value="0" disabled />
				<input type="checkbox" name="MiscDiscounted" disabled /> 
			</td>
		</tr>
		
		{foreach item=MISC_CHARGE_ROW key=ROW_NUM from=$MISC_CHARGES}
		<tr class='MiscItemRow' id='MiscItemRow{$ROW_NUM}'>
			<td class='fieldValue' style="width:5%;text-align:center;margin:auto">
				<a class="deleteMiscChargeButton">
					<i title="Delete" class="icon-trash alignMiddle"></i>
				</a>
			</td>
			<td class='fieldValue' style='text-align:center'>
				<input type="hidden" name="MiscFlatChargeOrQtyRate_prev-{$ROW_NUM}" value="none"  />
				<input type="hidden" name="MiscId-{$ROW_NUM}" value="{$MISC_CHARGE_ROW.contracts_misc_id}"  />
				<input type="radio" name="MiscFlatChargeOrQtyRate-{$ROW_NUM}" value="0" {if $MISC_CHARGE_ROW.is_quantity_rate eq '0'} checked{/if}  />
			</td>
			<td class='fieldValue' style='text-align:center'>
				<input type="radio" name="MiscFlatChargeOrQtyRate-{$ROW_NUM}" value="1"  {if $MISC_CHARGE_ROW.is_quantity_rate eq '1'} checked{/if} />
			</td>
			<td class='fieldValue' style='text-align:center'>
				<input type="text" class="input-large" name="MiscDescription-{$ROW_NUM}" style="width:80%" value="{$MISC_CHARGE_ROW.description}"  />
			</td>
			<td class='fieldValue'>
				<div class="row-fluid">
					<div class="input-prepend">
						<span class="span10">
							<span class="add-on">&#36;</span>
							<input name="MiscRate-{$ROW_NUM}" class="input-medium currencyField" type="text" value="{$MISC_CHARGE_ROW.rate}" data-decimal-seperator="." data-group-seperator="," data-number-of-decimal-places="2" />
						</span>
					</div>
				</div>
			</td>
			<td class='fieldValue' style='text-align:center'>
				<input type="number" class="input-large" name="MiscQty-{$ROW_NUM}" value="{$MISC_CHARGE_ROW.quantity}" style="width:80%" {if $MISC_CHARGE_ROW.is_quantity_rate eq '0'} disabled{/if}  />
			</td>
			<td class='fieldValue' style='text-align:center'>
				<input type="hidden" name="MiscDiscounted-{$ROW_NUM}" value="{$MISC_CHARGE_ROW.discounted}"  />
				<input type="checkbox" name="MiscDiscounted-{$ROW_NUM}" {if $MISC_CHARGE_ROW.discounted eq 'on'} checked{/if}  /> 
			</td>
		</tr>
		{/foreach}
	</tbody>
</table>
<br />
