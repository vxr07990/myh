{assign var=HAS_CONTENT value=(!$BLOCK_SUBLIST || $BLOCK_SUBLIST['SIRVA_VEHICLES'])}
<div id="contentHolder_SIRVA_VEHICLES" class="sectionContentHolder {$CONTENT_DIV_CLASS} {if !$ALWAYS_SHOW_CONTENT_DIV}hide{/if} {if !$HAS_CONTENT}inactive{/if}">
{if $HAS_CONTENT}
    {if
    $EFFECTIVE_TARIFF_CUSTOMTYPE == 'ALLV-2A' ||
    $EFFECTIVE_TARIFF_CUSTOMTYPE == 'NAVL-12A' ||
    $EFFECTIVE_TARIFF_CUSTOMTYPE == '400N Base' ||
    $EFFECTIVE_TARIFF_CUSTOMTYPE == '400N/104G' ||
    $EFFECTIVE_TARIFF_CUSTOMTYPE == '400NG'
    }
        {include file=vtemplate_path('CorporateVehiclesDetail.tpl',$MODULE)}
    {else}
<table class='table table-bordered blockContainer showInlineTable misc equalSplit detailview-table'>
	<thead>
		<th class='blockHeader' colspan='6'>
			<img class="cursorPointer alignMiddle blockToggle {if !($IS_HIDDEN)} hide {/if} "  src="{vimage_path('arrowRight.png')}" data-mode="hide" data-id="sirvaVehicles">
			<img class="cursorPointer alignMiddle blockToggle {if ($IS_HIDDEN)} hide {/if}"  src="{vimage_path('arrowDown.png')}" data-mode="show" data-id="sirvaVehicles">
			&nbsp;&nbsp;Vehicles
		</th>
	</thead>
	<tbody id='vehiclesTab'{if $IS_HIDDEN} class="hide" {/if}>
		<tr>
			<td>
				<b>Description</b>
			</td>
			<td>
				<b>Weight</b>
			</td>
			<td>
				<b>Make</b>
			</td>
			<td>
				<b>Model</b>
			</td>
			<td>
				<b>Year</b>
			</td>
		</tr>
        {foreach item=VEHICLE_ROW key=ROW_NUM from=$VEHICLES}
            <tr class='vehicleItem vehicleRow' id='vehicleRow-{$ROW_NUM}'>
			<td class='fieldValue' style='text-align:center'>
				<span class='value' data-field-type='string'>{$VEHICLE_ROW["description"]}</span>
				<input type='hidden' name='vehicleID-{$ROW_NUM}' value='{$VEHICLE_ROW["vehicle_Id"]}'>
			</td>
			<td class='fieldValue' style='text-align:center'>
				<span class='value' data-field-type='string'>{$VEHICLE_ROW["weight"]}</span>
			</td>
			<td class='fieldValue' style='text-align:center'>
				<span class='value' data-field-type='string'>{$VEHICLE_ROW["make"]}</span>
			</td>
			<td class='fieldValue' style='text-align:center'>
				<span class='value' data-field-type='string'>{$VEHICLE_ROW["model"]}</span>
			</td>
			<td class='fieldValue' style='text-align:center'>
				<span class='value' data-field-type='string'>{$VEHICLE_ROW["year"]}</span>
			</td>
		</tr>
        {/foreach}
	</tbody>
</table>
<br/>
    {/if}
{/if}
</div>