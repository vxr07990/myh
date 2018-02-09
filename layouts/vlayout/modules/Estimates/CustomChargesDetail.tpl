{strip}
{assign var=HAS_CONTENT value=(!$BLOCK_SUBLIST || $BLOCK_SUBLIST['CUSTOM_MISC_CHARGES'])}
<div id="contentHolder_CUSTOM_MISC_CHARGES" class="sectionContentHolder {$CONTENT_DIV_CLASS} {if !$ALWAYS_SHOW_CONTENT_DIV}hide{/if} {if !$HAS_CONTENT}inactive{/if}">
{if $HAS_CONTENT}
    <table id='flat_charge_table' class='table table-bordered equalSplit detailview-table'>
	<thead>
		<th class='blockHeader' colspan='4'>
			<img class="cursorPointer alignMiddle blockToggle {if !($IS_HIDDEN)} hide {/if} "  src="{vimage_path('arrowRight.png')}" data-mode="hide" data-id="flatcharges">
			<img class="cursorPointer alignMiddle blockToggle {if ($IS_HIDDEN)} hide {/if}"  src="{vimage_path('arrowDown.png')}" data-mode="show" data-id="flatcharges">
			&nbsp;&nbsp;Flat Charge Details
		</th>
	</thead>
	<tbody id='flatItemsTab'{if $IS_HIDDEN} class="hide" {/if}>
		<tr>
			{assign var=FLAT_CHARGE_TD_WIDTH value=100/4}
            {if getenv('INSTANCE_NAME') == 'graebel'}
                {assign var=FLAT_CHARGE_TD_WIDTH value=100/5}
                <td class="fieldLabel" style="width:{$FLAT_CHARGE_TD_WIDTH}%; background-color:#E8E8E8;">
				<b>Included</b>
				</td>
			{elseif getenv('IGC_MOVEHQ')}
				{assign var=FLAT_CHARGE_TD_WIDTH value=100/5}
				<td class="fieldLabel" style="width:{$FLAT_CHARGE_TD_WIDTH}%; background-color:#E8E8E8;">
				<b>Included</b>
				</td>
            {/if}
            <td class="fieldLabel" style="width:{$FLAT_CHARGE_TD_WIDTH}%; background-color:#E8E8E8;">
				<span class="redColor">*</span><b>Description</b>
			</td>
			<td class="fieldLabel" style="width:{$FLAT_CHARGE_TD_WIDTH}%; background-color:#E8E8E8;">
				<span class="redColor">*</span><b>Charge</b>
			</td>
			<td class="fieldLabel" style="width:{$FLAT_CHARGE_TD_WIDTH}%; background-color:#E8E8E8;">
				<b>Disc</b>
			</td>
		</tr>
		<tr class='hide defaultFlatItem flatItemRow newItemRow'>
			<td class='fieldValue' style="width:{$FLAT_CHARGE_TD_WIDTH}%">
				<span class='value' data-field-type='string'>&nbsp;</span>
			</td>
			<td class='fieldValue' style="width:{$FLAT_CHARGE_TD_WIDTH}%">
				<span class='value' data-field-type='string'>0.00</span>
			</td>
			<td class='fieldValue' style="width:{$FLAT_CHARGE_TD_WIDTH}%">
				<span class='value' data-field-type='boolean'>No</span>
			</td>
		</tr>
        {foreach item=MISC_CHARGE_ROW key=ROW_NUM from=$MISC_CHARGES.flat}
            <tr class='flatItemRow' id='flatItemRow{$ROW_NUM}'>
			{if getenv('INSTANCE_NAME') == 'graebel'}
                <td class='fieldValue' style="width:{$FLAT_CHARGE_TD_WIDTH}%">
				<span class='value' data-field-type='boolean'>{if $MISC_CHARGE_ROW->included eq '0'}No{else}Yes{/if}</span>
				</td>
			{elseif getenv('IGC_MOVEHQ')}
				<td class='fieldValue' style="width:{$FLAT_CHARGE_TD_WIDTH}%">
				<span class='value' data-field-type='boolean'>{if $MISC_CHARGE_ROW->included eq '0'}No{else}Yes{/if}</span>
				</td>
            {/if}
                <td class='fieldValue' style="width:{$FLAT_CHARGE_TD_WIDTH}%">
				<span class='value' data-field-type='string'>{$MISC_CHARGE_ROW->description}</span>
			</td>
			<td class='fieldValue' style="width:{$FLAT_CHARGE_TD_WIDTH}%">
				<span class='value' data-field-type='string'>{$MISC_CHARGE_ROW->charge}</span>
			</td>
			<td class='fieldValue' style="width:{$FLAT_CHARGE_TD_WIDTH}%">
				<span class='value' data-field-type='boolean'>{if $MISC_CHARGE_ROW->discounted eq '0'}No{else}Yes{/if}</span>
			</td>
			<input type='hidden' class='lineItemId' value='{$MISC_CHARGE_ROW->lineItemId}' />
		</tr>
        {/foreach}
	</tbody>
</table>
<br/>

<table id='qty_rate_table' class='table table-bordered equalSplit detailview-table'>
	<thead>
		<th class='blockHeader' colspan='5'>
			<img class="cursorPointer alignMiddle blockToggle {if !($IS_HIDDEN)} hide {/if} "  src="{vimage_path('arrowRight.png')}" data-mode="hide" data-id="qtycharges">
			<img class="cursorPointer alignMiddle blockToggle {if ($IS_HIDDEN)} hide {/if}"  src="{vimage_path('arrowDown.png')}" data-mode="show" data-id="qtycharges">
			&nbsp;&nbsp;Qty/Rate Details
		</th>
	</thead>
	<tbody id='qtyRateItemsTab'{if $IS_HIDDEN} class="hide" {/if}>
		<tr>
			{assign var=RATE_DETAILS_TD_WIDTH value=100/5}
            {if getenv('INSTANCE_NAME') == 'graebel'}
                {assign var=RATE_DETAILS_TD_WIDTH value=100/6}
                <td class="fieldLabel" style="width:{$RATE_DETAILS_TD_WIDTH}%; background-color:#E8E8E8;">
				<b>Included</b>
				</td>
			{elseif getenv('IGC_MOVEHQ')}
				{assign var=RATE_DETAILS_TD_WIDTH value=100/6}
				<td class="fieldLabel" style="width:{$RATE_DETAILS_TD_WIDTH}%; background-color:#E8E8E8;">
				<b>Included</b>
				</td>
            {/if}
            <td class="fieldLabel" style="width:{$RATE_DETAILS_TD_WIDTH}%; background-color:#E8E8E8;">
				<span class="redColor">*</span><b>Description</b>
			</td>
			<td class="fieldLabel" style="width:{$RATE_DETAILS_TD_WIDTH}%; background-color:#E8E8E8;">
				<span class="redColor">*</span><b>Rate</b>
			</td>
			<td class="fieldLabel" style="width:{$RATE_DETAILS_TD_WIDTH}%; background-color:#E8E8E8;">
				<span class="redColor">*</span><b>Qty</b>
			</td>
			<td class="fieldLabel" style="width:{$RATE_DETAILS_TD_WIDTH}%; background-color:#E8E8E8;">
				<b>Disc</b>
			</td>
		</tr>
		<tr class='hide defaultQtyRateItem qtyRateItemRow newItemRow'>
			<td class='fieldValue' style="width:{$RATE_DETAILS_TD_WIDTH}%">
				<span class='value' data-field-type='string'>&nbsp;</span>
			</td>
			<td class='fieldValue' style="width:{$RATE_DETAILS_TD_WIDTH}%">
				<span class='value' data-field-type='string'>0.00</span>
			</td>
			<td class='fieldValue' style="width:{$RATE_DETAILS_TD_WIDTH}%">
				<span class='value' data-field-type='string'>1</span>
			</td>
			<td class='fieldValue' style="width:{$RATE_DETAILS_TD_WIDTH}%">
				<span class='value' data-field-type='boolean'>No</span>
			</td>
		</tr>
        {foreach item=MISC_CHARGE_ROW key=ROW_NUM from=$MISC_CHARGES.qty}
            <tr class='qtyRateItemRow' id='qtyRateItemRow{$ROW_NUM}'>
			{if getenv('INSTANCE_NAME') == 'graebel'}
                <td class='fieldValue' style="width:{$RATE_DETAILS_TD_WIDTH}%">
				<span class='value' data-field-type='boolean'>{if $MISC_CHARGE_ROW->included eq '0'}No{else}Yes{/if}</span>
				</td>
			{elseif getenv('IGC_MOVEHQ')}
				<td class='fieldValue' style="width:{$RATE_DETAILS_TD_WIDTH}%">
				<span class='value' data-field-type='boolean'>{if $MISC_CHARGE_ROW->included eq '0'}No{else}Yes{/if}</span>
				</td>
            {/if}
                <td class='fieldValue' style="width:{$RATE_DETAILS_TD_WIDTH}%">
				<span class='value' data-field-type='string'>{$MISC_CHARGE_ROW->description}</span>
			</td>
			<td class='fieldValue' style="width:{$RATE_DETAILS_TD_WIDTH}%">
				<span class='value' data-field-type='string'>{$MISC_CHARGE_ROW->charge}</span>
			</td>
			<td class='fieldValue' style="width:{$RATE_DETAILS_TD_WIDTH}%">
				<span class='value' data-field-type='string'>{$MISC_CHARGE_ROW->qty}</span>
			</td>
			<td class='fieldValue' style="width:{$RATE_DETAILS_TD_WIDTH}%">
				<span class='value' data-field-type='boolean'>{if $MISC_CHARGE_ROW->discounted eq '0'}No{else}Yes{/if}</span>
			</td>
			<input type='hidden' class='lineItemId' value='{$MISC_CHARGE_ROW->lineItemId}' />
		</tr>
        {/foreach}
	</tbody>
</table>
<br/>
{/if}
</div>
{/strip}