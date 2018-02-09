{strip}
{if $MISC_CHARGES|@count gt 0}
	<table class='table table-bordered detailview-table misc' name="MiscItemsTable">
		<thead>
			<th class='blockHeader' colspan='8'>
				<img class="cursorPointer alignMiddle blockToggle {if !($IS_HIDDEN)} hide {/if} "  src="{vimage_path('arrowRight.png')}" data-mode="hide">
				<img class="cursorPointer alignMiddle blockToggle {if ($IS_HIDDEN)} hide {/if}"  src="{vimage_path('arrowDown.png')}" data-mode="show">
				&nbsp;&nbsp;Default Miscellaneous Items
			</th>
		</thead>
		<tbody id='qtyRateItemsTab'{if $IS_HIDDEN} class="hide" {/if}>
			<tr>
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
				<!--
				<td style='width:15%'>
					<b>Disc %</b>
				</td>
				-->
			</tr>			
			<tr class='hide defaultMiscItem MiscItemRow'>
				<td class='fieldValue' style='text-align:center'>
					<span class='value' data-field-type='string'>No</span>
					<span class='hide edit'>
						<input type="hidden" name="MiscFlatChargeOrQtyRate_prev" value="none"  />
						<input type="hidden" name="MiscId" value=""  />
						<input type="radio" name="MiscFlatChargeOrQtyRate" value="0" />
						<input type='hidden' class='fieldname' value='MiscFlatChargeOrQtyRate' data-prev-value=1 />
					</span>
				</td>
				<td class='fieldValue' style='text-align:center'>
					<span class='value' data-field-type='string'>Yes</span>
					<span class='hide edit'>
						<input type="radio" name="MiscFlatChargeOrQtyRate" value="1" checked/>
						<input type='hidden' class='fieldname' value='MiscFlatChargeOrQtyRate' data-prev-value=0 />
					</span>
				</td>
				<td class='fieldValue' style='text-align:center'>
					<span class='value' data-field-type='string'></span>
					<span class='hide edit'>
						<input type="text" class="input-large" name="MiscDescription" value=""  />
						<input type='hidden' class='fieldname' value='MiscDescription' data-prev-value='' />
					</span>
				</td>
				<td class='fieldValue'>
					<div class="row-fluid">
						<div class="input-prepend">
							<span class="span10">
								<span class='value' data-field-type='string'></span>
								<span class='hide edit'>
									<input name="MiscRate" class="input-medium currencyField" type="text" value="" />
									<input type='hidden' class='fieldname' value='MiscRate' data-prev-value='' />
								</span>
							</span>
						</div>
					</div>
				</td>
				<td class='fieldValue' style='text-align:center'>
					<span class='value' data-field-type='string'></span>
					<span class='hide edit'>
						<input type="number" class="input-large" name="MiscQty" value=""  />
						<input type='hidden' class='fieldname' value='MiscQty' data-prev-value='' />
					</span>
				</td>
				<td class='fieldValue' style='text-align:center'>
					<span class='value' data-field-type='string'>No</span>
					<span class='hide edit'>
						<input type="checkbox" name="MiscDiscounted" /> 
						<input type='hidden' class='fieldname' value='MiscDiscounted' data-prev-value='N0' />
					</span>
				</td>
				<td class='fieldValue' style='text-align:center'>
					<span class='value' data-field-type='string'></span>
					<span class='hide edit'>
						<input type="number" class="input-large" name="MiscDiscount" value=""  />
						<input type='hidden' class='fieldname' value='MiscDiscount' data-prev-value='' />
					</span>
				</td>
			</tr>
			{foreach item=MISC_CHARGE_ROW key=ROW_NUM from=$MISC_CHARGES}
			<tr class='MiscItemRow' id='MiscItemRow{$ROW_NUM}'>
				<td class='fieldValue' style='text-align:center'>
					<span class='value' data-field-type='string'>{if $MISC_CHARGE_ROW.is_quantity_rate eq '0'}Yes{else}No{/if}</span>
					<span class='hide edit'>
						<input type="hidden" name="MiscFlatChargeOrQtyRate_prev-{$ROW_NUM}" value="none"  />
						<input type="hidden" name="MiscId-{$ROW_NUM}" value="{$MISC_CHARGE_ROW.contracts_misc_id}"  />
						<input type="radio" name="MiscFlatChargeOrQtyRate-{$ROW_NUM}" value="0" {if $MISC_CHARGE_ROW.is_quantity_rate eq '0'} checked{/if}  />
						<input type='hidden' class='fieldname' value='MiscFlatChargeOrQtyRate-{$ROW_NUM}' data-prev-value=1 />
					</span>
				</td>
				<td class='fieldValue' style='text-align:center'>
					<span class='value' data-field-type='string'>{if $MISC_CHARGE_ROW.is_quantity_rate eq '0'}No{else}Yes{/if}</span>
					<span class='hide edit'>
						<input type="radio" name="MiscFlatChargeOrQtyRate-{$ROW_NUM}" value="1"  {if $MISC_CHARGE_ROW.is_quantity_rate eq '1'} checked{/if} />
						<input type='hidden' class='fieldname' value='MiscFlatChargeOrQtyRate-{$ROW_NUM}' data-prev-value=0 />
					</span>
				</td>
				<td class='fieldValue' style='text-align:center'>
					<span class='value' data-field-type='string'>{$MISC_CHARGE_ROW.description}</span>
					<span class='hide edit'>
						<input type="text" class="input-large" name="MiscDescription-{$ROW_NUM}" value="{$MISC_CHARGE_ROW.description}"  />
						<input type='hidden' class='fieldname' value='MiscDescription-{$ROW_NUM}' data-prev-value='{$MISC_CHARGE_ROW.description}' />
					</span>
				</td>
				<td class='fieldValue'>
					<div class="row-fluid">
						<div class="input-prepend">
							<span class="span10">
								<span class='value' data-field-type='string'>${$MISC_CHARGE_ROW.rate}</span>
								<span class='hide edit'>
									<input name="MiscRate-{$ROW_NUM}" class="input-medium currencyField" type="text" value="{$MISC_CHARGE_ROW.rate}" />
									<input type='hidden' class='fieldname' value='MiscRate-{$ROW_NUM}' data-prev-value='{$MISC_CHARGE_ROW.rate}' />
								</span>
							</span>
						</div>
					</div>
				</td>
				<td class='fieldValue' style='text-align:center'>
					<span class='value' data-field-type='string'>{$MISC_CHARGE_ROW.quantity}</span>
					<span class='hide edit'>
						<input type="number" class="input-large" name="MiscQty-{$ROW_NUM}" value="{$MISC_CHARGE_ROW.quantity}"  />
						<input type='hidden' class='fieldname' value='MiscQty-{$ROW_NUM}' data-prev-value='{$MISC_CHARGE_ROW.quantity}' />
					</span>
				</td>
				<td class='fieldValue' style='text-align:center'>
					<span class='value' data-field-type='string'>{if $MISC_CHARGE_ROW.discounted eq 'on'}Yes{else}No{/if}</span>
					<span class='hide edit'>
						<input type="checkbox" name="MiscDiscounted-{$ROW_NUM}" {if $MISC_CHARGE_ROW.discounted eq 'on'} checked{/if}  /> 
						<input type='hidden' class='fieldname' value='MiscDiscounted-{$ROW_NUM}' data-prev-value='{if $MISC_CHARGE_ROW.discounted eq 'on'}Yes{else}No{/if}' />
					</span>
				</td>
				<!--
				<td class='fieldValue' style='text-align:center'>
					<span class='value' data-field-type='string'>{$MISC_CHARGE_ROW.discount}</span>
					<span class='hide edit'>
						<input type="number" class="input-large" name="MiscDiscount-{$ROW_NUM}" value="{$MISC_CHARGE_ROW.discount}"  />
						<input type='hidden' class='fieldname' value='MiscDiscount-{$ROW_NUM}' data-prev-value='{$MISC_CHARGE_ROW.discount}' />
					</span>
				</td>
				-->
			</tr>
			{/foreach}
		</tbody>
	</table>
	<br />
{/if}
