{strip}
<table class='table table-bordered equalSplit detailview-table packing'>
	<thead>
		<th class='blockHeader' colspan='6'>
			<img class="cursorPointer alignMiddle blockToggle {if !($IS_HIDDEN)} hide {/if} "  src="{vimage_path('arrowRight.png')}" data-mode="hide">
			<img class="cursorPointer alignMiddle blockToggle {if ($IS_HIDDEN)} hide {/if}"  src="{vimage_path('arrowDown.png')}" data-mode="show">
			&nbsp;&nbsp;Packing
		</th>
	</thead>
	<tbody id='packingTab'{if $IS_HIDDEN} class="hide" {/if}>
		<tr><td class="fieldLabel"></td><td class="fieldValue">Pk</td><td class="fieldValue">Unpk</td><td class="fieldLabel"></td><td class="fieldValue">Pk</td><td class="fieldValue">Unpk</td></tr>
		{assign var=COUNTER value=0}
		<tr>
		{foreach item=PACKING_ITEM key=ITEM_NUM from=$PACKING_ITEMS}
		{if $COUNTER eq 2}
		</tr>
		<tr>
		{assign var=COUNTER value=1}
		{else}
			{assign var=COUNTER value=$COUNTER+1}
		{/if}
			<td style='min-width:140px; padding:0 5px 0 0;' class='fieldLabel {$WIDTHTYPE}'>
				<label class="muted pull-right marginRight10px">
					{$PACKING_ITEM.label}
				</label>
			</td>
			<td style='min-width:30px; text-align:center;' class='fieldValue {$WIDTHTYPE}'>
				<input type='text' class='input-large' name='pack{$ITEM_NUM}' style='max-width:25px; padding:0;' value='{$PACKING_ITEM.pack}' />
			</td>
			<td style='min-width:30px; text-align:center;' class='fieldValue {$WIDTHTYPE}'>
				<input type='text' class='input-large' name='unpack{$ITEM_NUM}' style='max-width:25px; padding:0;' value='{$PACKING_ITEM.unpack}' />
			</td>
		{/foreach}
		{while $COUNTER lt 2}
			<td style='min-width:140px; padding:0 5px 0 0;' class='fieldLabel {$WIDTHTYPE}'>
				&nbsp;
			</td>
			<td style='min-width:30px;' class='{$WIDTHTYPE}'>
				&nbsp;
			</td>
			<td style='min-width:30px;' class='{$WIDTHTYPE}'>
				&nbsp;
			</td>
			{assign var=COUNTER value=$COUNTER+1}
		{/while}
		</tr>
	</tbody>
</table>
<br />

<table class='table table-bordered equalSplit detailview-table packing'>
	<thead>
		<th class='blockHeader' colspan='6'>
			<img class="cursorPointer alignMiddle blockToggle {if !($IS_HIDDEN)} hide {/if} "  src="{vimage_path('arrowRight.png')}" data-mode="hide">
			<img class="cursorPointer alignMiddle blockToggle {if ($IS_HIDDEN)} hide {/if}"  src="{vimage_path('arrowDown.png')}" data-mode="show">
			&nbsp;&nbsp;OT Packing
		</th>
	</thead>
	<tbody id='otPackingTab'{if $IS_HIDDEN} class="hide" {/if}>
		<tr><td class="fieldLabel"></td><td class="fieldValue">Pk</td><td class="fieldValue">Unpk</td><td class="fieldLabel"></td><td class="fieldValue">Pk</td><td class="fieldValue">Unpk</td></tr>
		{assign var=COUNTER value=0}
		<tr>
		{foreach item=PACKING_ITEM key=ITEM_NUM from=$PACKING_ITEMS}
		{if $COUNTER eq 2}
		</tr>
		<tr>
		{assign var=COUNTER value=1}
		{else}
			{assign var=COUNTER value=$COUNTER+1}
		{/if}
			<td style='min-width:140px; padding:0 5px 0 0;' class='fieldLabel {$WIDTHTYPE}'>
				<label class="muted pull-right marginRight10px">
					{$PACKING_ITEM.label}
				</label>
			</td>
			<td style='min-width:30px; text-align:center;' class='fieldValue {$WIDTHTYPE}'>
				<input type='text' class='input-large' name='ot_pack{$ITEM_NUM}' style='max-width:25px; padding:0;' value='{$PACKING_ITEM.otpack}' />
			</td>
			<td style='min-width:30px; text-align:center;' class='fieldValue {$WIDTHTYPE}'>
				<input type='text' class='input-large' name='ot_unpack{$ITEM_NUM}' style='max-width:25px; padding:0;' value='{$PACKING_ITEM.otunpack}' />
			</td>
		{/foreach}
		{while $COUNTER lt 2}
			<td style='min-width:140px; padding:0 5px 0 0;' class='fieldLabel {$WIDTHTYPE}'>
				&nbsp;
			</td>
			<td style='min-width:30px;' class='{$WIDTHTYPE}'>
				&nbsp;
			</td>
			<td style='min-width:30px;' class='{$WIDTHTYPE}'>
				&nbsp;
			</td>
			{assign var=COUNTER value=$COUNTER+1}
		{/while}
		</tr>
	</tbody>
</table>
<br />

<table class='table table-bordered equalSplit detailview-table bulky'>
	<thead>
		<th class='blockHeader' colspan='6'>
			<img class="cursorPointer alignMiddle blockToggle {if !($IS_HIDDEN)} hide {/if} "  src="{vimage_path('arrowRight.png')}" data-mode="hide">
			<img class="cursorPointer alignMiddle blockToggle {if ($IS_HIDDEN)} hide {/if}"  src="{vimage_path('arrowDown.png')}" data-mode="show">
			&nbsp;&nbsp;Bulky Items
		</th>
	</thead>
	<tbody id='bulkyItemsTab'{if $IS_HIDDEN} class="hide" {/if}>
		{assign var=COUNTER value=0}
		<tr>
		{foreach item=BULKY_ITEM key=ITEM_NUM from=$BULKY_ITEMS}
		{if $COUNTER eq 3}
		</tr>
		<tr>
		{assign var=COUNTER value=1}
		{else}
			{assign var=COUNTER value=$COUNTER+1}
		{/if}
			<td style='min-width:110px; padding:0 5px 0 0;' class='fieldLabel {$WIDTHTYPE}'>
				<label class="muted pull-right marginRight10px">
					{$BULKY_ITEM.label}
				</label>
			</td>
			<td style='min-width:23px; text-align:center;' class='fieldValue {$WIDTHTYPE}'>
				<input type='text' class='input-large' name='bulky{$ITEM_NUM}' style='max-width:20px; padding:0;' value='{$BULKY_ITEM.qty}' />
			</td>
		{/foreach}
		{while $COUNTER lt 3}
			<td style='min-width:110px; padding:0 5px 0 0;' class='fieldLabel {$WIDTHTYPE}'>
				&nbsp;
			</td>
			<td style='min-width:23px;' class='{$WIDTHTYPE}'>
				&nbsp;
			</td>
			{assign var=COUNTER value=$COUNTER+1}
		{/while}
		</tr>
	</tbody>
</table>
<br />

<table class='table table-bordered blockContainer showInlineTable misc'>
	<thead>
		<th class='blockHeader' colspan='5'>
			<img class="cursorPointer alignMiddle blockToggle {if !($IS_HIDDEN)} hide {/if} "  src="{vimage_path('arrowRight.png')}" data-mode="hide">
			<img class="cursorPointer alignMiddle blockToggle {if ($IS_HIDDEN)} hide {/if}"  src="{vimage_path('arrowDown.png')}" data-mode="show">
			&nbsp;&nbsp;Flat Charge Item Details
		</th>
	</thead>
	<tbody id='flatItemsTab'{if $IS_HIDDEN} class="hide" {/if}>
		<tr>
			<td colspan='5'>
				<button type='button' id='addFlatChargeItem'>+Add Flat Charge Item</button><br />
			</td>
		</tr>
		<tr>
			<td style='min-width:14px'>
				&nbsp;
			</td>
			<td style='min-width:225px'>
				<span class="redColor">*</span><b>Description</b>
			</td>
			<td style='min-width:75px'>
				<span class="redColor">*</span><b>Charge</b>
			</td>
			<td>
				<b>Disc</b>
			</td>
			<td style='min-width:50px'>
				<b>Disc %</b>
			</td>
		</tr>
		<tr class='hide defaultFlatItem flatItemRow newItemRow'>
			<td class='fieldValue'>
				<a class="deleteMiscChargeButton">
					<i title="Delete" class="icon-trash alignMiddle"></i>
				</a>
			</td>
			<td class='fieldValue'>
				<input type='text' class='input-large' name='flatDescription' />
			</td>
			<td class='fieldValue'>
				<div class='input-prepend'>
					<span class='add-on'>$</span>
					<input type='text' class='input-medium currencyField' name='flatCharge' value='0.00' style='max-width:45px' data-decimal-seperator="." data-group-seperator="," data-number-of-decimal-places="2" />
				</div>
			</td>
			<td class='fieldValue'>
				<input type='hidden' name='flatDiscounted' value='0' />
				<input type='checkbox' name='flatDiscounted' />
			</td>
			<td class='fieldValue'>
				<input type='text' class='input-large' name='flatDiscountPercent' value='0' style='max-width:50px' />
			</td>
		</tr>
		{foreach item=MISC_CHARGE_ROW key=ROW_NUM from=$MISC_CHARGES.flat}
		<tr class='flatItemRow' id='flatItemRow{$ROW_NUM}'>
			<td class='fieldValue'>
				<a class="deleteMiscChargeButton">
					<i title="Delete" class="icon-trash alignMiddle"></i>
				</a>
			</td>
			<td class='fieldValue'>
				<input type='text' class='input-large' name='flatDescription{$ROW_NUM}' value='{$MISC_CHARGE_ROW->description}' />
			</td>
			<td class='fieldValue'>
				<div class='input-prepend'>
					<span class='add-on'>$</span>
					<input type='text' class='input-medium currencyField' name='flatCharge{$ROW_NUM}' value='{$MISC_CHARGE_ROW->charge}' style='max-width:45px' />
				</div>
			</td>
			<td class='fieldValue'>
				<input type='hidden' name='flatDiscounted{$ROW_NUM}' value='0' />
				<input type='checkbox' name='flatDiscounted{$ROW_NUM}' {if $MISC_CHARGE_ROW->discounted eq '1'}checked{/if} />
			</td>
			<td class='fieldValue'>
				<input type='text' class='input-large' name='flatDiscountPercent{$ROW_NUM}' value='{if $MISC_CHARGE_ROW->discounted eq '0'}0{else}{$MISC_CHARGE_ROW->discount}{/if}' style='max-width:50px' />
			</td>
			<td class='fieldValue hide'>
				<input type='text' class='lineItemId' name='flatLineItemId{$ROW_NUM}' value='{$MISC_CHARGE_ROW->lineItemId}' />
			</td>
		</tr>
		{/foreach}
	</tbody>
</table>
<br />

<table class='table table-bordered blockContainer showInlineTable misc'>
	<thead>
		<th class='blockHeader' colspan='6'>
			<img class="cursorPointer alignMiddle blockToggle {if !($IS_HIDDEN)} hide {/if} "  src="{vimage_path('arrowRight.png')}" data-mode="hide">
			<img class="cursorPointer alignMiddle blockToggle {if ($IS_HIDDEN)} hide {/if}"  src="{vimage_path('arrowDown.png')}" data-mode="show">
			&nbsp;&nbsp;Qty/Rate Item Details
		</th>
	</thead>
	<tbody id='qtyRateItemsTab'{if $IS_HIDDEN} class="hide" {/if}>
		<tr>
			<td colspan='6'>
				<button type='button' id='addQtyRateChargeItem'>+Add Qty/Rate Item</button>
			</td>
		</tr>
		<tr>
			<td style='min-width:14px'>
				&nbsp;
			</td>
			<td style='min-width:200px'>
				<span class="redColor">*</span><b>Description</b>
			</td>
			<td style='min-width:70px'>
				<span class="redColor">*</span><b>Rate</b>
			</td>
			<td>
				<span class="redColor">*</span><b>Qty</b>
			</td>
			<td style='min-width:30px'>
				<b>Disc</b>
			</td>
			<td style='min-width:50px'>
				<b>Disc %</b>
			</td>
		</tr>
		<tr class='hide defaultQtyRateItem qtyRateItemRow newItemRow'>
			<td class='fieldValue'>
				<a class="deleteMiscChargeButton">
					<i title="Delete" class="icon-trash alignMiddle"></i>
				</a>
			</td>
			<td class='fieldValue'>
				<input type='text' class='input-large' name='qtyRateDescription' style='max-width:190px' />
			</td>
			<td class='fieldValue'>
				<div class='input-prepend'>
					<span class='add-on'>$</span>
					<input type='text' class='input-medium currencyField' name='qtyRateCharge' value='0.00' style='max-width:40px' />
				</div>
			</td>
			<td class='fieldValue'>
				<input type='text' class='input-large' name='qtyRateQty' style='max-width:30px' value='1' />
			</td>
			<td class='fieldValue'>
				<input type='hidden' name='qtyRateDiscounted' value='0' />
				<input type='checkbox' name='qtyRateDiscounted' />
			</td>
			<td class='fieldValue'>
				<input type='text' class='input-large' name='qtyRateDiscountPercent' value='0' style='max-width:45px' />
			</td>
		</tr>
		{foreach item=MISC_CHARGE_ROW key=ROW_NUM from=$MISC_CHARGES.qty}
		<tr class='qtyRateItemRow' id='qtyRateItemRow{$ROW_NUM}'>
			<td class='fieldValue'>
				<a class="deleteMiscChargeButton">
					<i title="Delete" class="icon-trash alignMiddle"></i>
				</a>
			</td>
			<td class='fieldValue'>
				<input type='text' class='input-large' name='qtyRateDescription{$ROW_NUM}' value='{$MISC_CHARGE_ROW->description}' style='max-width:190px' />
			</td>
			<td class='fieldValue'>
				<div class='input-prepend'>
					<span class='add-on'>$</span>
					<input type='text' class='input-medium currencyField' name='qtyRateCharge{$ROW_NUM}' value='{$MISC_CHARGE_ROW->charge}' style='max-width:40px' />
				</div>
			</td>
			<td class='fieldValue'>
				<input type='text' class='input-large' name='qtyRateQty{$ROW_NUM}' value='{$MISC_CHARGE_ROW->qty}' style='max-width:30px' />
			</td>
			<td class='fieldValue'>
				<input type='hidden' name='qtyRateDiscounted{$ROW_NUM}' value='0' />
				<input type='checkbox' name='qtyRateDiscounted{$ROW_NUM}'{if $MISC_CHARGE_ROW->discounted eq '1'} checked{/if} />
			</td>
			<td class='fieldValue'>
				<input type='text' class='input-large' name='qtyRateDiscountPercent{$ROW_NUM}' value='{if $MISC_CHARGE_ROW->discounted eq '0'}0{else}{$MISC_CHARGE_ROW->discount}{/if}' style='max-width:45px' />
			</td>
			<td class='fieldValue hide'>
				<input type='text' class='lineItemId' name='qtyRateLineItemId{$ROW_NUM}' value='{$MISC_CHARGE_ROW->lineItemId}' />
			</td>
		</tr>
		{/foreach}
	</tbody>
</table>
<br />

<table class='table table-bordered blockContainer showInlineTable misc'>
	<thead>
		<th class='blockHeader' colspan='5'>
			<img class="cursorPointer alignMiddle blockToggle {if !($IS_HIDDEN)} hide {/if} "  src="{vimage_path('arrowRight.png')}" data-mode="hide">
			<img class="cursorPointer alignMiddle blockToggle {if ($IS_HIDDEN)} hide {/if}"  src="{vimage_path('arrowDown.png')}" data-mode="show">
			&nbsp;&nbsp;Crate Details
		</th>
	</thead>
	<tbody id='cratesTab'{if $IS_HIDDEN} class="hide" {/if}>
		<tr>
			<td colspan='11'>
				<button type='button' id='addCrate'>+Add Crate</button>
			</td>
		</tr>
		<tr>
			<td style='text-align:center; background-color:#E8E8E8;' colspan='3'>
				&nbsp;
			</td>
			<td style='text-align:center; background-color:#E8E8E8;' colspan='3'>
				<b>Dimensions (in)</b>
			</td>
			<td style='text-align:center; background-color:#E8E8E8;' colspan='2'>
				<b>Pack</b>
			</td>
			<td style='text-align:center; background-color:#E8E8E8;' colspan='2'>
				<b>OT Pack</b>
			</td>
			<td style='text-align:center; background-color:#E8E8E8;'>
				&nbsp;
			</td>
		</tr>
		<tr>
			<td style='min-width:14px'>
				&nbsp;
			</td>
			<td style='min-width:30px'>
				<b>ID</b>
			</td>
			<td style='min-width:100px'>
				<span class="redColor">*</span><b>Description</b>
			</td>
			<td style='min-width:20px'>
				<span class="redColor">*</span><b>L</b>
			</td>
			<td style='min-width:20px'>
				<span class="redColor">*</span><b>W</b>
			</td>
			<td style='min-width:20px'>
				<span class="redColor">*</span><b>H</b>
			</td>
			<td style='min-width:30px'>
				<b>Pk</b>
			</td>
			<td style='min-width:30px'>
				<b>Unpk</b>
			</td>
			<td style='min-width:30px'>
				<b>Pk</b>
			</td>
			<td style='min-width:30px'>
				<b>Unpk</b>
			</td>
			<td style='min-width:30px'>
				<b>Disc</b>
			</td>
		</tr>
		<tr class='hide defaultCrate crateRow newItemRow'>
			<td class='fieldValue'>
				<a class="deleteMiscChargeButton">
					<i title="Delete" class="icon-trash alignMiddle"></i>
				</a>
			</td>
			<td class='fieldValue'>
				<input type='text' class='input-large' name='crateId' style='max-width:30px' value />
				<input type='hidden' class='fieldname' value='crateId' data-prev-value />
			</td>
			<td class='fieldValue'>
				<input type='text' class='input-large' name='crateDescription' style='max-width:190px' />
				<input type='hidden' class='fieldname' value='crateDescription' data-prev-value />
			</td>
			<td class='fieldValue'>
				<input type='text' class='input-large' name='crateLength' style='max-width:30px' value />
				<input type='hidden' class='fieldname' value='crateLength' data-prev-value />
			</td>
			<td class='fieldValue'>
				<input type='text' class='input-large' name='crateWidth' style='max-width:30px' value />
				<input type='hidden' class='fieldname' value='crateWidth' data-prev-value />
			</td>
			<td class='fieldValue'>
				<input type='text' class='input-large' name='crateHeight' style='max-width:30px' value />
				<input type='hidden' class='fieldname' value='crateHeight' data-prev-value />
			</td>
			<td class='fieldValue'>
				<input type='checkbox' name='cratePack' />
				<input type='hidden' class='fieldname' value='cratePack' data-prev-value='no' />
			</td>
			<td class='fieldValue'>
				<input type='checkbox' name='crateUnpack' />
				<input type='hidden' class='fieldname' value='crateUnpack' data-prev-value='no' />
			</td>
			<td class='fieldValue'>
				<input type='checkbox' name='crateOTPack' />
				<input type='hidden' class='fieldname' value='crateOTPack' data-prev-value='no' />
			</td>
			<td class='fieldValue'>
				<input type='checkbox' name='crateOTUnpack' />
				<input type='hidden' class='fieldname' value='crateOTUnpack' data-prev-value='no' />
			</td>
			<td class='fieldValue'>
				<input type='text' class='input-large' name='crateDiscountPercent' value='0' style='max-width:35px' />
				<input type='hidden' class='fieldname' value='crateDiscountPercent' data-prev-value='0' />
			</td>
		</tr>
		{foreach item=CRATE_ROW key=ROW_NUM from=$CRATES}
		<tr class='crateRow' id='crateRow{$ROW_NUM}'>
			<td class='fieldValue'>
				<a class="deleteMiscChargeButton">
					<i title="Delete" class="icon-trash alignMiddle"></i>
				</a>
			</td>
			<td class='fieldValue'>
				<input type='text' class='input-large' name='crateId{$ROW_NUM}' style='max-width:30px' value='{$CRATE_ROW->crateid}' />
			</td>
			<td class='fieldValue'>
				<input type='text' class='input-large' name='crateDescription{$ROW_NUM}' style='max-width:95px' value='{$CRATE_ROW->description}' />
			</td>
			<td class='fieldValue'>
				<input type='text' class='input-large' name='crateLength{$ROW_NUM}' style='max-width:18px' value='{$CRATE_ROW->crateLength}' />
			</td>
			<td class='fieldValue'>
				<input type='text' class='input-large' name='crateWidth{$ROW_NUM}' style='max-width:18px' value='{$CRATE_ROW->crateWidth}' />
			</td>
			<td class='fieldValue'>
				<input type='text' class='input-large' name='crateHeight{$ROW_NUM}' style='max-width:18px' value='{$CRATE_ROW->crateHeight}' />
			</td>
			<td class='fieldValue'>
				<input type='hidden' name='cratePack{$ROW_NUM}' value='0' />
				<input type='checkbox' name='cratePack{$ROW_NUM}'{if $CRATE_ROW->pack eq '1'} checked{/if} />
			</td>
			<td class='fieldValue'>
				<input type='hidden' name='crateUnpack{$ROW_NUM}' value='0' />
				<input type='checkbox' name='crateUnpack{$ROW_NUM}'{if $CRATE_ROW->unpack eq '1'} checked{/if} />
			</td>
			<td class='fieldValue'>
				<input type='hidden' name='crateOTPack{$ROW_NUM}' value='0' />
				<input type='checkbox' name='crateOTPack{$ROW_NUM}'{if $CRATE_ROW->otpack eq '1'} checked{/if} />
			</td>
			<td class='fieldValue'>
				<input type='hidden' name='crateOTUnpack{$ROW_NUM}' value='0' />
				<input type='checkbox' name='crateOTUnpack{$ROW_NUM}'{if $CRATE_ROW->otunpack eq '1'} checked{/if} />
			</td>
			<td class='fieldValue'>
				<input type='text' class='input-large' name='crateDiscountPercent{$ROW_NUM}' value='{$CRATE_ROW->discount}' style='max-width:35px' />
			</td>
			<input type='hidden' class='lineItemId' value='{$CRATE_ROW->lineItemId}' />
		</tr>
		{/foreach}
	</tbody>
</table>
{/strip}