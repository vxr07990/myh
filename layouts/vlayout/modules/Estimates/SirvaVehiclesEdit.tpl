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
		{include file=vtemplate_path('CorporateVehicles.tpl',$MODULE)}
	{else}
<table class='table table-bordered blockContainer showInlineTable misc'>
	<thead>
		<th class='blockHeader' colspan='6'>
			<img class="cursorPointer alignMiddle blockToggle {if !($IS_HIDDEN)} hide {/if} "  src="{vimage_path('arrowRight.png')}" data-mode="hide" data-id="sirvaVehicles">
			<img class="cursorPointer alignMiddle blockToggle {if ($IS_HIDDEN)} hide {/if}"  src="{vimage_path('arrowDown.png')}" data-mode="show" data-id="sirvaVehicles">
			&nbsp;&nbsp;Vehicles
		</th>
	</thead>
	<tbody id='vehiclesTab'{if $IS_HIDDEN} class="hide" {/if}>
			<tr>
			<td colspan='6' style='padding:0'>
					<input type='hidden' id='numSirvaVehicles' name='numSirvaVehicles' value='{$NUM_VEHICLES}'>
					<button type='button' id='addVehicle'>+</button>
					<button type='button' id='addVehicle2' style='clear:right;float:right;'>+</button><br />
				</td>
			</tr>
		<tr>
			<td style='width:15%'>
				&nbsp;
			</td>
			<td style='width:17%'>
				<span class="redColor">*</span><b>Description</b>
			</td>
			<td style='width:17%'>
				<span class="redColor">*</span><b>Weight</b>
			</td>
			<td style='width:17%'>
				<b>Make</b>
			</td>
			<td style='width:17%'>
				<b>Model</b>
			</td>
			<td style='width:17%'>
				<b>Year</b>
			</td>
		</tr>
		<tr class='hide vehicleItem vehicleRow newVehicleRow'>
			<td class='fieldValue' style='text-align:center'>
				<a class="deleteVehicleButton">
					<i title="Delete" class="icon-trash alignMiddle"></i>
				</a>
			</td>
			<td class='fieldValue' style='text-align:center'>
                <!--<input type='text' class='input-large' style='width:90%' name='vehicleDescription' />-->
                <select class="chzn-select  chzn-done" name="vehicleDescription">
                {foreach item=VEHICLE_TYPE key=BULKY_ID from=$SIRVA_VEHICLE_TYPES}
                    <option data-bulky="{$BULKY_ID}" value="{$VEHICLE_TYPE}">{$VEHICLE_TYPE}</option>
                {/foreach}
			</td>
			<td class='fieldValue' style='text-align:center'>
				<div class='input-prepend input-prepend-centered'>
					<input type='text' class='input-medium' style='float:left' name='vehicleWeight' value=''/>
				</div>
			</td>
			<td class='fieldValue' style='text-align:center'>
				<div class='input-prepend input-prepend-centered'>
					<input type='text' class='input-medium' style='float:left' name='vehicleMake' value=''/>
				</div>
			</td>
			<td class='fieldValue' style='text-align:center'>
				<div class='input-prepend input-prepend-centered'>
					<input type='text' class='input-medium' style='float:left' name='vehicleModel' value=''/>
				</div>
			</td>
			<td class='fieldValue' style='text-align:center'>
				<div class='input-prepend input-prepend-centered'>
					<input type='text' class='input-medium' style='float:left' name='vehicleYear' value=''/>
				</div>
			</td>
		</tr>
        {foreach item=VEHICLE_ROW key=ROW_NUM from=$VEHICLES}
            <tr class='vehicleItem vehicleRow' id='vehicleRow-{$ROW_NUM}'>
			<td class='fieldValue' style='text-align:center'>
				<a class="deleteMiscChargeButton">
					<i title="Delete" class="icon-trash alignMiddle"></i>
				</a>
			</td>
			<td class='fieldValue' style='text-align:center'>
                <!--<input type='text' class='input-large' style='width:90%' name='vehicleDescription-{$ROW_NUM}' value='{$VEHICLE_ROW["description"]}'/>-->
                <select class="chzn-select" name="vehicleDescription-{$ROW_NUM}">
                {foreach item=VEHICLE_TYPE key=BULKY_ID from=$SIRVA_VEHICLE_TYPES}
                    <option data-bulky="{$BULKY_ID}" value="{$VEHICLE_TYPE}" {if $VEHICLE_TYPE eq $VEHICLE_ROW["description"]}selected="selected"{/if}>{$VEHICLE_TYPE}</option>
                {/foreach}
                </select>
				<input type='hidden' name='vehicleID-{$ROW_NUM}' value='{$VEHICLE_ROW["vehicle_id"]}'>
			</td>
			<td class='fieldValue' style='text-align:center'>
				<div class='input-prepend input-prepend-centered'>
					<input type='text' class='input-large' style='width:90%' name='vehicleWeight-{$ROW_NUM}' value='{$VEHICLE_ROW["weight"]}' />
				</div>
			</td>
			<td class='fieldValue' style='text-align:center'>
				<div class='input-prepend input-prepend-centered'>
					<input type='text' class='input-large' style='width:90%' name='vehicleMake-{$ROW_NUM}' value='{$VEHICLE_ROW["make"]}' />
				</div>
			</td>
			<td class='fieldValue' style='text-align:center'>
				<div class='input-prepend input-prepend-centered'>
					<input type='text' class='input-large' style='width:90%' name='vehicleModel-{$ROW_NUM}' value='{$VEHICLE_ROW["model"]}' />
				</div>
			</td>
			<td class='fieldValue' style='text-align:center'>
				<div class='input-prepend input-prepend-centered'>
					<input type='text' class='input-large' style='width:90%' name='vehicleYear-{$ROW_NUM}' value='{$VEHICLE_ROW["year"]}' />
				</div>
			</td>
		</tr>
        {/foreach}
	</tbody>
</table>
<br />
	{/if}
    {/if}
    </div>
