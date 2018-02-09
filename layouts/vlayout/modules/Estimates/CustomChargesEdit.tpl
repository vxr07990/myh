{strip}
{assign var=HAS_CONTENT value=(!$BLOCK_SUBLIST || $BLOCK_SUBLIST['CUSTOM_MISC_CHARGES'])}
<div id="contentHolder_CUSTOM_MISC_CHARGES" class="sectionContentHolder {$CONTENT_DIV_CLASS} {if !$ALWAYS_SHOW_CONTENT_DIV}hide{/if} {if !$HAS_CONTENT}inactive{/if}">
{if $HAS_CONTENT}
    <table id='flat_charge_table' class='table table-bordered blockContainer showInlineTable misc'>
	<thead>
		<th class='blockHeader' colspan='5'>
			<img class="cursorPointer alignMiddle blockToggle {if !($IS_HIDDEN)} hide {/if} "  src="{vimage_path('arrowRight.png')}" data-mode="hide" data-id="flatcharges">
			<img class="cursorPointer alignMiddle blockToggle {if ($IS_HIDDEN)} hide {/if}"  src="{vimage_path('arrowDown.png')}" data-mode="show" data-id="flatcharges">
			&nbsp;&nbsp;Flat Charge Item Details
		</th>
	</thead>
	<tbody id='flatItemsTab'{if $IS_HIDDEN} class="hide" {/if}>
		{if !$LOCK_RATING}
            <tr>
				<td colspan='5' style='padding:0'>
					<button type='button' id='addFlatChargeItem'>+</button>
					<button type='button' id='addFlatChargeItem2' style='clear:right;float:right;'>+</button><br />
				</td>
			</tr>
        {/if}
        <tr>
			<td style='width:5%; background-color:#E8E8E8;'>
				&nbsp;
				<input type='hidden' id='interstateNumFlat' name='interstateNumFlat' value='{$MISC_CHARGES.flat|@count}'>
			</td>
            {if getenv('INSTANCE_NAME') == 'graebel'}
                <td style='width:10%; background-color:#E8E8E8;'>
				<b>Included</b>
				</td>
                <td style='width:30%; background-color:#E8E8E8;'>
					<span class="redColor">*</span><b>Description</b>
				</td>
                <td style='width:20%; background-color:#E8E8E8;'>
					<span class="redColor">*</span><b>Charge</b>
				</td>
                <td style='width:15%; background-color:#E8E8E8;'>
					<b>Disc</b>
				</td>
			{elseif getenv('IGC_MOVEHQ')}
				<td style='width:10%; background-color:#E8E8E8;'>
				<b>Included</b>
				</td>
                <td style='width:30%; background-color:#E8E8E8;'>
					<span class="redColor">*</span><b>Description</b>
				</td>
                <td style='width:20%; background-color:#E8E8E8;'>
					<span class="redColor">*</span><b>Charge</b>
				</td>
                <td style='width:15%; background-color:#E8E8E8;'>
					<b>Disc</b>
				</td>
			{else}
				<td style='width:30%; background-color:#E8E8E8;'>
					<span class="redColor">*</span><b>Description</b>
				</td>
                <td style='width:25%; background-color:#E8E8E8;'>
					<span class="redColor">*</span><b>Charge</b>
				</td>
                <td style='width:20%; background-color:#E8E8E8;'>
					<b>Disc</b>
				</td>
            {/if}
		</tr>
		<tr class='hide defaultFlatItem flatItemRow newItemRow'>
			<td class='fieldValue' style='text-align:center'>
				<a class="deleteMiscChargeButton">
					<i title="Delete" class="icon-trash alignMiddle"></i>
				</a>
			</td>
            {if getenv('INSTANCE_NAME') == 'graebel'}
                <td class='fieldValue' style='text-align:center'>
					<input type='checkbox' name='flatChargeToBeRated' checked/>
				</td>
			{elseif getenv('IGC_MOVEHQ')}
				<td class='fieldValue' style='text-align:center'>
					<input type='checkbox' name='flatChargeToBeRated' checked/>
				</td>
            {/if}
            <td class='fieldValue' style='text-align:center; padding: 5px'>
				<input type='text' class='input-large' style='width:90%' name='flatDescription' />
			</td>
			<td class='fieldValue' style='text-align:center; padding: 5px'>
				<div class='input-prepend input-prepend-centered'>
					<span class='add-on'>$</span>
					<input type='text' class='input-medium currencyField' style='width:80%;float:left' name='flatCharge' value='0.00' data-decimal-seperator="." data-group-seperator="," data-number-of-decimal-places="2" />
				</div>
			</td>
			<td class='fieldValue' style="padding: 5px">
				<input type='hidden' name='flatDiscounted' value='0' />
				<input type='checkbox' name='flatDiscounted' />
			</td>
		</tr>
        {foreach item=MISC_CHARGE_ROW key=ROW_NUM from=$MISC_CHARGES.flat}
            <tr class='flatItemRow' id='flatItemRow{$ROW_NUM}'>
			<td class='fieldValue' style='text-align:center'>
				<a class="deleteMiscChargeButton">
					{if $MISC_CHARGE_ROW->enforced == 0}<i title="Delete" class="icon-trash alignMiddle"></i>{/if}
				</a>
			</td>
                {if getenv('INSTANCE_NAME') == 'graebel'}
                    <td class='fieldValue' style='text-align:center'>
					<input type="checkbox" name='flatChargeToBeRated{$ROW_NUM}' {if $MISC_CHARGE_ROW->included eq '1'}checked{/if}/>
				</td>
			{elseif getenv('IGC_MOVEHQ')}
				<td class='fieldValue' style='text-align:center'>
					<input type="checkbox" name='flatChargeToBeRated{$ROW_NUM}' {if $MISC_CHARGE_ROW->included eq '1'}checked{/if}/>
				</td>
                {/if}
                <td class='fieldValue' style='text-align:center; padding: 5px'>
				<input type='text' class='input-large' style='width:90%' name='flatDescription{$ROW_NUM}' value='{$MISC_CHARGE_ROW->description}' {if $MISC_CHARGE_ROW->enforced == 1}readonly{/if} />
			</td>
			<td class='fieldValue' style='text-align:center; padding: 5px'>
				<div class='input-prepend input-prepend-centered'>
					<span class='add-on'>$</span>
					<input type='text' class='input-small currencyField' style='width:80%;float:left' name='flatCharge{$ROW_NUM}' value='{$MISC_CHARGE_ROW->charge}' {if $MISC_CHARGE_ROW->enforced == 1}readonly{/if} />
				</div>
			</td>
			<td class='fieldValue' style="padding: 5px">
				<input type='hidden' name='flatDiscounted{$ROW_NUM}' value='{if $MISC_CHARGE_ROW->enforced == 0}0{else}{if $MISC_CHARGE_ROW->discounted eq '1'}1{else}0{/if}{/if}' />
                {if $MISC_CHARGE_ROW->enforced == 0}<input type='checkbox' name='flatDiscounted{$ROW_NUM}'{if $MISC_CHARGE_ROW->discounted eq '1'} checked{/if} />{else}{if $MISC_CHARGE_ROW->discounted eq '1'}Yes{else}No{/if}{/if}
			</td>
				<input type='hidden' class='lineItemId' name='flatLineItemId{$ROW_NUM}' value='{$MISC_CHARGE_ROW->lineItemId}' />
				<input type='hidden' class='enforced' name='flatEnforced{$ROW_NUM}' value='{$MISC_CHARGE_ROW->enforced}' />
				<input type='hidden' class='blah' name='flatFromContract{$ROW_NUM}' value='{$MISC_CHARGE_ROW->fromContract}' />
		</tr>
        {/foreach}
	</tbody>
</table>
    <br/>

    <table id='qty_rate_table' class='table table-bordered blockContainer showInlineTable misc'>
	<thead>
		<th class='blockHeader' colspan='6'>
			<img class="cursorPointer alignMiddle blockToggle {if !($IS_HIDDEN)} hide {/if} "  src="{vimage_path('arrowRight.png')}" data-mode="hide" data-id="qtycharges">
			<img class="cursorPointer alignMiddle blockToggle {if ($IS_HIDDEN)} hide {/if}"  src="{vimage_path('arrowDown.png')}" data-mode="show" data-id="qtycharges">
			&nbsp;&nbsp;Qty/Rate Item Details
		</th>
	</thead>
	<tbody id='qtyRateItemsTab'{if $IS_HIDDEN} class="hide" {/if}>
		{if !$LOCK_RATING}
            <tr>
				<td colspan='6' style='padding:0'>
					<button type='button' id='addQtyRateChargeItem'>+</button>
					<button type='button' id='addQtyRateChargeItem2' style='clear:right; float:right;'>+</button>
				</td>
			</tr>
        {/if}
        <tr>
			<td style="width:5%; background-color:#E8E8E8;">
				&nbsp;
				<input type='hidden' id='interstateNumQty' name='interstateNumQty' value='{$MISC_CHARGES.qty|@count}'>
			</td>
            {if getenv('INSTANCE_NAME') == 'graebel'}
                <td style='width:10%; background-color:#E8E8E8;'>
					<b>Included</b>
				</td>
                <td style='width:30%; background-color:#E8E8E8;'>
					<span class="redColor">*</span><b>Description</b>
				</td>
                <td style='width:15%; background-color:#E8E8E8;'>
					<span class="redColor">*</span><b>Rate</b>
				</td>
                <td style='width:15%; background-color:#E8E8E8;'>
					<span class="redColor">*</span><b>Qty</b>
				</td>
                <td style='width:10%; background-color:#E8E8E8;'>
					<b>Disc</b>
				</td>
			{elseif getenv('IGC_MOVEHQ')}
				<td style='width:10%; background-color:#E8E8E8;'>
					<b>Included</b>
				</td>
                <td style='width:30%; background-color:#E8E8E8;'>
					<span class="redColor">*</span><b>Description</b>
				</td>
                <td style='width:15%; background-color:#E8E8E8;'>
					<span class="redColor">*</span><b>Rate</b>
				</td>
                <td style='width:15%; background-color:#E8E8E8;'>
					<span class="redColor">*</span><b>Qty</b>
				</td>
                <td style='width:10%; background-color:#E8E8E8;'>
					<b>Disc</b>
				</td>
			{else}
				<td style='width:30%; background-color:#E8E8E8;'>
					<span class="redColor">*</span><b>Description</b>
				</td>
                <td style='width:20%; background-color:#E8E8E8;'>
					<span class="redColor">*</span><b>Rate</b>
				</td>
                <td style='width:15%; background-color:#E8E8E8;'>
					<span class="redColor">*</span><b>Qty</b>
				</td>
                <td style='width:15%; background-color:#E8E8E8;'>
					<b>Disc</b>
				</td>
            {/if}
		</tr>
		<tr class='hide defaultQtyRateItem qtyRateItemRow newItemRow'>
			<td class='fieldValue' style='text-align:center'>
				<a class="deleteMiscChargeButton">
					<i title="Delete" class="icon-trash alignMiddle"></i>
				</a>
			</td>
            {if getenv('INSTANCE_NAME') == 'graebel'}
                <td class='fieldValue' style='text-align:center'>
					<input type='checkbox' name='qtyChargeToBeRated' checked/>
				</td>
			{elseif getenv('IGC_MOVEHQ')}
				<td class='fieldValue' style='text-align:center'>
					<input type='checkbox' name='qtyChargeToBeRated' checked/>
				</td>
            {/if}
            <td class='fieldValue' style='text-align:center; padding: 5px'>
				<input type='text' class='input-large' style='width:90%' name='qtyRateDescription' />
			</td>
			<td class='fieldValue' style='text-align:center; padding: 5px'>
				<div class='input-prepend input-prepend-centered'>
					<span class='add-on'>$</span>
					<input type='text' class='input-small currencyField' style='width:80%;float:left' name='qtyRateCharge' value='0.00' />
				</div>
			</td>
			<td class='fieldValue' style='text-align:center; padding: 5px'>
				<input type='text'  data-validator='[{ldelim}"name":"WholeNumber"{rdelim}]' data-validation-engine="validate[funcCall[Vtiger_Base_Validator_Js.invokeValidation]]"  class='input-small' style='width:90%' name='qtyRateQty' value='1' />
			</td>
			<td class='fieldValue' style="padding: 5px">
				<input type='hidden' name='qtyRateDiscounted' value='0' />
				<input type='checkbox' name='qtyRateDiscounted' />
			</td>
		</tr>
        {foreach item=MISC_CHARGE_ROW key=ROW_NUM from=$MISC_CHARGES.qty}
            <tr class='qtyRateItemRow' id='qtyRateItemRow{$ROW_NUM}'>
			<td class='fieldValue' style='text-align:center'>
				<a class="deleteMiscChargeButton">
					{if $MISC_CHARGE_ROW->enforced == 0}<i title="Delete" class="icon-trash alignMiddle"></i>{/if}
				</a>
			</td>
                {if getenv('INSTANCE_NAME') == 'graebel'}
                    <td class='fieldValue' style='text-align:center'>
					<input type="checkbox" name='qtyChargeToBeRated{$ROW_NUM}' {if $MISC_CHARGE_ROW->included eq '1'}checked{/if}/>
				</td>
			{elseif getenv('IGC_MOVEHQ')}
				<td class='fieldValue' style='text-align:center'>
					<input type="checkbox" name='qtyChargeToBeRated{$ROW_NUM}' {if $MISC_CHARGE_ROW->included eq '1'}checked{/if}/>
				</td>
                {/if}
                <td class='fieldValue' style='text-align:center; padding: 5px'>
				<input type='text' class='input-large' style='width:90%' name='qtyRateDescription{$ROW_NUM}' value='{$MISC_CHARGE_ROW->description}' {if $MISC_CHARGE_ROW->enforced == 1}readonly{/if} />
			</td>
			<td class='fieldValue' style='text-align:center; padding: 5px'>
				<div class='input-prepend input-prepend-centered'>
					<span class='add-on'>$</span>
					<input type='text' class='input-small currencyField' style='width:80%;float:left' name='qtyRateCharge{$ROW_NUM}' value='{$MISC_CHARGE_ROW->charge}' {if $MISC_CHARGE_ROW->enforced == 1}readonly{/if} />
				</div>
			</td>
			<td class='fieldValue' style='text-align:center; padding: 5px'>
				<input type='text'  data-validator='[{ldelim}"name":"WholeNumber"{rdelim}]' data-validation-engine="validate[funcCall[Vtiger_Base_Validator_Js.invokeValidation]]"  class='input-small' style='width:90%' name='qtyRateQty{$ROW_NUM}' value='{$MISC_CHARGE_ROW->qty}' {if $MISC_CHARGE_ROW->enforced == 1}readonly{/if} />
			</td>
			<td class='fieldValue' style="padding: 5px">
				<input type='hidden' name='qtyRateDiscounted{$ROW_NUM}' value='{if $MISC_CHARGE_ROW->enforced == 0}0{else}{if $MISC_CHARGE_ROW->discounted eq '1'}1{else}0{/if}{/if}' />
                {if $MISC_CHARGE_ROW->enforced == 0}<input type='checkbox' name='qtyRateDiscounted{$ROW_NUM}'{if $MISC_CHARGE_ROW->discounted eq '1'} checked{/if} />{else}{if $MISC_CHARGE_ROW->discounted eq '1'}Yes{else}No{/if}{/if}
			</td>
				<input type='hidden' class='lineItemId' name='qtyRateLineItemId{$ROW_NUM}' value='{$MISC_CHARGE_ROW->lineItemId}' />
				<input type='hidden' class='enforced' name='qtyRateEnforced{$ROW_NUM}' value='{$MISC_CHARGE_ROW->enforced}' />
				<input type='hidden' class='blah' name='qtyRateFromContract{$ROW_NUM}' value='{$MISC_CHARGE_ROW->fromContract}' />
		</tr>
        {/foreach}
	</tbody>
</table>
<br/>
{/if}
</div>
{/strip}