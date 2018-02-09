{assign var=HAS_CONTENT value=(!$BLOCK_SUBLIST || $BLOCK_SUBLIST['FLAT_RATE_AUTO'])}
<div id="contentHolder_FLAT_RATE_AUTO" class="sectionContentHolder {$CONTENT_DIV_CLASS} {if !$ALWAYS_SHOW_CONTENT_DIV}hide{/if} {if !$HAS_CONTENT}inactive{/if}">
{if $HAS_CONTENT}
	<input type="hidden" name="contractFlatRateAuto" value="{$HAS_FLAT_AUTO}" />
	<table class='table table-bordered detailview-table'>
		<thead>
			<th class='blockHeader' colspan='8'>
				<img class="cursorPointer alignMiddle blockToggle {if !($IS_HIDDEN)} hide {/if} "  src="{vimage_path('arrowRight.png')}" data-mode="hide">
				<img class="cursorPointer alignMiddle blockToggle {if ($IS_HIDDEN)} hide {/if}"  src="{vimage_path('arrowDown.png')}" data-mode="show">
				&nbsp;&nbsp;Flat Rate Auto Table
			</th>
		</thead>
		<tbody>
			<tr>
				<td style='width:30%' id='fparentFromMileage'>
					<b>From Mileage</b>
				</td>
				<td style='width:30%' id='fparentToMileage'>
					<b>To Mileage</b>
				</td>
				<td style='width:35%' id='fparentRate'>
					<b>Rate</b>
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
				<td class='medium narrowWidthType' style="width:30%;">
					<span class="value">{$FLAT_RATE_AUTO_ROW.from_mileage}</span>
				</td>
				<td class='medium narrowWidthType' style="width:30%;">
					<span class="value">{$FLAT_RATE_AUTO_ROW.to_mileage}</span>
				</td>
				<td class='medium narrowWidthType' style="width:35%;">
					<span class="value">{$FLAT_RATE_AUTO_ROW.rate}</span>
				</td>
				<td class='medium narrowWidthType' style="width:23.75%;text-align:center;">
					{if $FLAT_RATE_AUTO_ROW.discount eq 'on'}
						<span class="value">Yes</span>
					{else}
						<span class="value">No</span>
					{/if}

				</td>
			</tr>
			{/foreach}
		</tbody>
	</table>
	<br />
	{/if}
	</div>
