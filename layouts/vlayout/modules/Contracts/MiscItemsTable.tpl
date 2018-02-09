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
				<input type="hidden" class="hide" name="numMisc" value="0">&nbsp;</td>
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
			<td style='width:20%'>
				<b>Disc %</b>
			</td>
		</tr>
		<tr class='hide defaultMiscItem MiscItemRow'>
			<td class='fieldValue' style="width:5%;text-align:center;margin:auto">
				<a class="deleteMiscChargeButton">
					<i title="Delete" class="icon-trash alignMiddle"></i>
				</a>
			</td>
			<td class='fieldValue' style='text-align:center'>
				<input type="hidden" name="MiscFlatChargeOrQtyRate_prev" value="none" />
				<input type="radio" name="MiscFlatChargeOrQtyRate" value="0">
			</td>
			<td class='fieldValue' style='text-align:center'>
				<input type="radio" name="MiscFlatChargeOrQtyRate" value="1">
			</td>
			<td class='fieldValue' style='text-align:center'>
				<input type="text" class="input-large" name="MiscDescription" style="width:80%">
			</td>
			<td class='fieldValue'>
				<div class="row-fluid">
					<div class="input-prepend">
						<span class="span10">
							<span class="add-on">&#36;</span>
							<input name="MiscRate" class="input-medium currencyField" type="text" value="" data-decimal-seperator="." data-group-seperator="," data-number-of-decimal-places="2" />
						</span>
					</div>
				</div>
			</td>
			<td class='fieldValue' style='text-align:center'>
				<input type="number" class="input-large" name="MiscQty" style="width:80%">
			</td>
			<td class='fieldValue' style='text-align:center'>
				<input type="hidden" name="MiscDiscounted" value="0">
				<input type="checkbox" name="MiscDiscounted">
			</td>
			<td class='fieldValue' style='text-align:center'>
				<input type="number" class="input-large" name="MiscDiscount" style="width:80%">
			</td>
		</tr>
		{foreach item=MISC_CHARGE_ROW key=ROW_NUM from=$MISC_CHARGES.qty}
		<tr class='qtyRateItemRow' id='qtyRateItemRow{$ROW_NUM}'>
			<td class='fieldValue'>
				<a class="deleteMiscChargeButton">
					<i title="Delete" class="icon-trash alignMiddle"></i>
				</a>
			</td>
			<td class='fieldValue' style='text-align:center'>
				<input type='text' class='input-large' style='width:90%' name='qtyRateDescription{$ROW_NUM}' value='{$MISC_CHARGE_ROW->description}' />
			</td>
			<td class='fieldValue' style='text-align:center'>
				<div class='input-prepend'>
					<span class='add-on'>$</span>
					<input type='text' class='input-small currencyField' style='width:80%' name='qtyRateCharge{$ROW_NUM}' value='{$MISC_CHARGE_ROW->charge}' />
				</div>
			</td>
			<td class='fieldValue' style='text-align:center'>
				<input type='text' class='input-small' style='width:90%' name='qtyRateQty{$ROW_NUM}' value='{$MISC_CHARGE_ROW->qty}' />
			</td>
			<td class='fieldValue'>
				<input type='hidden' name='qtyRateDiscounted{$ROW_NUM}' value='0' />
				<input type='checkbox' name='qtyRateDiscounted{$ROW_NUM}'{if $MISC_CHARGE_ROW->discounted eq '1'} checked{/if} />
			</td>
			<td class='fieldValue' style='text-align:center'>
				<input type='text' class='input-small' style='width:90%' name='qtyRateDiscountPercent{$ROW_NUM}' value='{if $MISC_CHARGE_ROW->discounted eq '0'}0{else}{$MISC_CHARGE_ROW->discount}{/if}' />
			</td>
			<td class='fieldValue hide'>
				<input type='text' class='lineItemId' name='qtyRateLineItemId{$ROW_NUM}' value='{$MISC_CHARGE_ROW->lineItemId}' />
			</td>
		</tr>
		{/foreach}
	</tbody>
</table>
<br />
