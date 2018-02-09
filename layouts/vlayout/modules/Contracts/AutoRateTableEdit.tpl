{assign var=HAS_CONTENT value=(!$BLOCK_SUBLIST || $BLOCK_SUBLIST['FLAT_RATE_AUTO'])}
<div id="contentHolder_FLAT_RATE_AUTO" class="sectionContentHolder {$CONTENT_DIV_CLASS} {if !$ALWAYS_SHOW_CONTENT_DIV}hide{/if} {if !$HAS_CONTENT}inactive{/if}">
{if $HAS_CONTENT}
<input type="hidden" name="contractFlatRateAuto" value="{$HAS_FLAT_AUTO}" />
<table id="auto-rate-table" class="table table-bordered blockContainer showInlineTable">
	<thead>
		<th class='blockHeader' colspan='8'>
			<img class="cursorPointer alignMiddle blockToggle {if !($IS_HIDDEN)} hide {/if} "  src="{vimage_path('arrowRight.png')}" data-mode="hide">
			<img class="cursorPointer alignMiddle blockToggle {if ($IS_HIDDEN)} hide {/if}"  src="{vimage_path('arrowDown.png')}" data-mode="show">
			&nbsp;&nbsp; Flat Rate Auto Table
		</th>
	</thead>
	<tbody>
		{if $MODULE neq 'Estimates' && $MODULE neq 'Actuals'}
		<tr>
			<td colspan='5' style='padding:0' id='flatRateAutoButtonRow'>
				<button type='button' id='addFlatRateAutoRow'>+</button>
				<button type='button' id='addFlatRateAutoRow2' style='clear:right; float:right;'>+</button>
			</td>
		</tr>
		{/if}
		<tr>
			<td style='width:5%'>
            <input type="hidden" class="hide" name="numFlatRateAuto" value="{$FLAT_RATE_AUTO_TABLE|@count - 1}" />&nbsp;</td>
			<td style='width:30%' id='fparentFromMileage'>
				<span class="redColor">*</span><b>From Mileage</b>
			</td>
			<td style='width:30%' id='fparentToMileage'>
				<span class="redColor">*</span><b>To Mileage</b>
			</td>
			<td style='width:35%' id='fparentRate'>
				<span class="redColor">*</span><b>Rate</b>
			</td>
			<td style='width:23.75%' id='fparentDiscount'>
				<b>Discount</b>
			</td>
		</tr>

		<tr class='hide defaultFlatRateAutoRow flatRateAutoRow'>
			<td class='fieldValue' style="width:5%;text-align:center;margin:auto">
				{if $MODULE neq 'Estimates' && $MODULE neq 'Actuals'}
					<a class="deleteFlatRateAutoButton">
						<i title="Delete" class="icon-trash alignMiddle"></i>
					</a>
				{/if}
			</td>
			<td class='fieldValue' style="width:30%">
                <input name="FlatRateAutoTableFromMileage" class="input-medium" style='width:85%' type="text" value="" data-decimal-seperator="." data-group-seperator="," data-number-of-decimal-places="2" disabled />
			</td>
			<td class='fieldValue' style="width:30%">
                <input name="FlatRateAutoTableToMileage" class="input-medium" style='width:85%' type="text" value="" data-decimal-seperator="." data-group-seperator="," data-number-of-decimal-places="2" disabled />
			</td>
			<td class='fieldValue' style="width:35%;text-align:center">
				<input name="FlatRateAutoTableRate" class="input-medium currencyField" style='width:85%' type="text" value="" data-decimal-seperator="." data-group-seperator="," data-number-of-decimal-places="2" disabled />
			</td>
			<td class='fieldValue' style="width:23.75%;text-align:center;">
				<input type="hidden" name="FlatRateAutoTableDiscount" value="0" disabled />
				<input type="checkbox" name="FlatRateAutoTableDiscount" disabled />
			</td>
			<input type="hidden" name="FlatRateAutoTableId" value="none" disabled />
		</tr>

		{foreach item=FLAT_RATE_AUTO_ROW key=ROW_NUM from=$FLAT_RATE_AUTO_TABLE}
		<tr class='FlatRateAutoRow'>
			<td class='fieldValue' style="width:5%;text-align:center;margin:auto">
				{if $MODULE neq 'Estimates' && $MODULE neq 'Actuals'}
					<a class="deleteFlatRateAutoButton">
						<i title="Delete" class="icon-trash alignMiddle"></i>
					</a>
				{/if}
			</td>
			<td class='fieldValue' style="width:30%;">
				<div class="row-fluid">
					<span class="span10">
						<input name="FlatRateAutoTableFromMileage-{$ROW_NUM}" class="input-medium" style='width:85%' type="text" value="{$FLAT_RATE_AUTO_ROW.from_mileage}" {if $MODULE eq 'Estimates' || $MODULE eq 'Actuals'}readonly{/if} data-decimal-seperator="." data-group-seperator="," data-number-of-decimal-places="2" />
					</span>
				</div>
			</td>
			<td class='fieldValue' style="width:30%;">
				<div class="row-fluid">
					<span class="span10">
						<input name="FlatRateAutoTableToMileage-{$ROW_NUM}" {if $MODULE eq 'Estimates' || $MODULE eq 'Actuals'}readonly{/if} class="input-medium" style='width:85%' type="text" value="{$FLAT_RATE_AUTO_ROW.to_mileage}" data-decimal-seperator="." data-group-seperator="," data-number-of-decimal-places="2" />
					</span>
				</div>
			</td>
			<td class='fieldValue' style="width:35%;text-align:center;">
				<div class="row-fluid">
					<div class="input-prepend">
						<span class="span10">
							<span class="add-on">&#36;</span>
							<input name="FlatRateAutoTableRate-{$ROW_NUM}" {if $MODULE eq 'Estimates' || $MODULE eq 'Actuals'}readonly{/if} class="input-large" style='width:85%' type="text" value="{$FLAT_RATE_AUTO_ROW.rate}" data-decimal-seperator="." data-group-seperator="," data-number-of-decimal-places="4" />
						</span>
					</div>
				</div>
			</td>
			<td class='fieldValue' style="width:23.75%;text-align:center;">
				<input type="hidden" {if $MODULE eq 'Estimates' || $MODULE eq 'Actuals'}disabled{/if} name="FlatRateAutoTableDiscount-{$ROW_NUM}" value="{$FLAT_RATE_AUTO_ROW.discount}"  />
				<input type="checkbox" {if $MODULE eq 'Estimates' || $MODULE eq 'Actuals'}disabled{/if} name="FlatRateAutoTableDiscount-{$ROW_NUM}" {if $FLAT_RATE_AUTO_ROW.discount eq 'on'} checked{/if}  />
			</td>
			<input type="hidden" name="FlatRateAutoTableId-{$ROW_NUM}" value="{$FLAT_RATE_AUTO_ROW.line_item_id}"  />
		</tr>
		{/foreach}
	</tbody>
</table>
<br />
	{/if}
	</div>
