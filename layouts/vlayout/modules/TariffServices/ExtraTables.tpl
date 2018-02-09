{strip}
	{if $BLOCK_LABEL eq 'LBL_TARIFFSERVICES_BASEPLUS'}
		<tr>
			<td colspan='7'>
				<button type='button' id='addBasePlus'>+</button>
				<button type='button' id='addBasePlus2' style='clear:right;float:right'>+</button><br />
			</td>
		</tr>
		<tr>
			<td style='width:5%'>
				&nbsp;
			</td>
			<td style='width:15%'>
				<b>From Miles</b>
			</td>
			<td style='width:15%'>
				<b>To Miles</b>
			</td>
			<td style='width:15%'>
				<b>From Weight</b>
			</td>
			<td style='width:15%'>
				<b>To Weight</b>
			</td>
			<td style='width:15%'>
				<b>Base Rate</b>
			</td>
			<td style='width:15%'>
				<b>Excess</b>
			</td>
		</tr>
		<tr class='hide defaultBasePlus basePlusRow newItemRow'>
			<td class='fieldValue' style='width:5%'>
				<a class='deleteBasePlusButton'>
					<i title="Delete" class="icon-trash alignMiddle"></i>
				</a>
			</td>
			<td class='fieldValue' style='width:15%'>
				<input type='number' class='input-medium' name='fromMilesBasePlus' style='width:95%' value />
			</td>
			<td class='fieldValue' style='width:15%'>
				<input type='number' class='input-medium' name='toMilesBasePlus' style='width:95%'  value />
			</td>
			<td class='fieldValue' style='width:15%'>
				<input type='number' class='input-medium' name='fromWeightBasePlus' style='width:95%'  value />
			</td>
			<td class='fieldValue' style='width:15%'>
				<input type='number' class='input-medium' name='toWeightBasePlus' style='width:95%'  value />
			</td>
			<td class='fieldValue' style='width:15%'>
				<input type='number' class='input-medium' name='baseRateBasePlus' step='0.01' style='width:95%'  value />
			</td>
			<td class='fieldValue' style='width:15%'>
				<input type='number' class='input-medium' name='excessBasePlus' step='0.01' style='width:95%'  value />
			</td>
		</tr>
		<input type='hidden' class='hide' name='numBasePlus' value='{count($BASEPLUS)}'>
		{foreach item=BASEPLUSROW key=ROW_NUM from=$BASEPLUS}
			<tr class='basePlusRow' id='basePlusRow{$ROW_NUM+1}'>
				<td class='fieldValue' style='width:5%'>
					<a class='deleteBasePlusButton'>
						<i title="Delete" class="icon-trash alignMiddle"></i>
					</a>
				</td>
				<td class='fieldValue' style='width:15%'>
					<input type='number' class='input-medium' name='fromMilesBasePlus{$ROW_NUM+1}' style='width:95%' value='{$BASEPLUSROW.from_miles}' />
				</td>
				<td class='fieldValue' style='width:15%'>
					<input type='number' class='input-medium' name='toMilesBasePlus{$ROW_NUM+1}' style='width:95%'  value='{$BASEPLUSROW.to_miles}' />
				</td>
				<td class='fieldValue' style='width:15%'>
					<input type='number' class='input-medium' name='fromWeightBasePlus{$ROW_NUM+1}' style='width:95%'  value='{$BASEPLUSROW.from_weight}' />
				</td>
				<td class='fieldValue' style='width:15%'>
					<input type='number' class='input-medium' name='toWeightBasePlus{$ROW_NUM+1}' style='width:95%'  value='{$BASEPLUSROW.to_weight}' />
				</td>
				<td class='fieldValue' style='width:15%'>
					<input type='number' class='input-medium' name='baseRateBasePlus{$ROW_NUM+1}' step='0.01' style='width:95%'  value='{$BASEPLUSROW.base_rate}' />
				</td>
				<td class='fieldValue' style='width:15%'>
					<input type='number' class='input-medium' name='excessBasePlus{$ROW_NUM+1}' step='0.01' style='width:95%'  value='{$BASEPLUSROW.excess}' />
				</td>
				<td class='fieldValue hide'>
					<input type='text' class='lineItemId' name='lineItemIdBasePlus{$ROW_NUM+1}' value='{$BASEPLUSROW.line_item_id}' />
				</td>
			</tr>
		{/foreach}
	{else if $BLOCK_LABEL  eq 'LBL_TARIFFSERVICES_BREAKPOINT'}
		<tr>
			<td colspan='7'>
				<button type='button' id='addBreakPoint'>+</button>
				<button type='button' id='addBreakPoint2' style='clear:right;float:right'>+</button><br />
			</td>
		</tr>
		<tr>
			<td style='width:5%'>
				&nbsp;
			</td>
			<td style='width:15%'>
				<b>From Miles</b>
			</td>
			<td style='width:15%'>
				<b>To Miles</b>
			</td>
			<td style='width:15%'>
				<b>From Weight</b>
			</td>
			<td style='width:15%'>
				<b>To Weight</b>
			</td>
			<td style='width:15%'>
				<b>Break Point</b>
			</td>
			<td style='width:15%'>
				<b>Base Rate</b>
			</td>
		</tr>
		<tr class='hide defaultBreakPoint breakPointRow newItemRow'>
			<td class='fieldValue' style='width:5%'>
				<a class='deleteBreakPointButton'>
					<i title="Delete" class="icon-trash alignMiddle"></i>
				</a>
			</td>
			<td class='fieldValue' style='width:15%'>
				<input type='number' class='input-medium' name='fromMilesBreakPoint' style='width:95%' value />
			</td>
			<td class='fieldValue' style='width:15%'>
				<input type='number' class='input-medium' name='toMilesBreakPoint' style='width:95%'  value />
			</td>
			<td class='fieldValue' style='width:15%'>
				<input type='number' class='input-medium' name='fromWeightBreakPoint' style='width:95%'  value />
			</td>
			<td class='fieldValue' style='width:15%'>
				<input type='number' class='input-medium' name='toWeightBreakPoint' style='width:95%'  value />
			</td>
			<td class='fieldValue' style='width:15%'>
				<input type='number' class='input-medium' name='breakPointBreakPoint' style='width:95%'  value />
			</td>
			<td class='fieldValue' style='width:15%'>
				<input type='number' class='input-medium' name='baseRateBreakPoint' step='0.01' style='width:95%'  value />
			</td>
		</tr>
		<input type='hidden' class='hide' name='numBreakPoint' value='{count($BREAKPOINT)}'>
		{foreach item=BREAKPOINTROW key=ROW_NUM from=$BREAKPOINT}
			<tr class='breakPointRow' id='breakPointRow{$ROW_NUM+1}'>
				<td class='fieldValue' style='width:5%'>
					<a class='deleteBreakPointButton'>
						<i title="Delete" class="icon-trash alignMiddle"></i>
					</a>
				</td>
				<td class='fieldValue' style='width:15%'>
					<input type='number' class='input-medium' name='fromMilesBreakPoint{$ROW_NUM+1}' style='width:95%' value='{$BREAKPOINTROW.from_miles}' />
				</td>
				<td class='fieldValue' style='width:15%'>
					<input type='number' class='input-medium' name='toMilesBreakPoint{$ROW_NUM+1}' style='width:95%'  value='{$BREAKPOINTROW.to_miles}' />
				</td>
				<td class='fieldValue' style='width:15%'>
					<input type='number' class='input-medium' name='fromWeightBreakPoint{$ROW_NUM+1}' style='width:95%'  value='{$BREAKPOINTROW.from_weight}' />
				</td>
				<td class='fieldValue' style='width:15%'>
					<input type='number' class='input-medium' name='toWeightBreakPoint{$ROW_NUM+1}' style='width:95%'  value='{$BREAKPOINTROW.to_weight}' />
				</td>
				<td class='fieldValue' style='width:15%'>
					<input type='number' class='input-medium' name='breakPointBreakPoint{$ROW_NUM+1}' style='width:95%'  value='{$BREAKPOINTROW.break_point}' />
				</td>
				<td class='fieldValue' style='width:15%'>
					<input type='number' class='input-medium' name='baseRateBreakPoint{$ROW_NUM+1}' step='0.01' style='width:95%'  value='{$BREAKPOINTROW.base_rate}' />
				</td>
				<td class='fieldValue hide'>
					<input type='text' class='lineItemId' name='lineItemIdBreakPoint{$ROW_NUM+1}' value='{$BREAKPOINTROW.line_item_id}' />
				</td>
			</tr>
		{/foreach}
	{else if $BLOCK_LABEL eq 'LBL_TARIFFSERVICES_SERVICECHARGE'}
		<tr class="serviceChargeMatrixInfo{if $SERVICECHARGEMATRIX} hide{/if}">
			<td colspan='6'>
				<button type='button' id='addServiceCharge'>+</button>
				<button type='button' id='addServiceCharge2' style='clear:right;float:right'>+</button><br />
			</td>
		</tr>
		<tr class="serviceChargeMatrixInfo{if $SERVICECHARGEMATRIX} hide{/if}">
			<td style='width:5%'>
				&nbsp;
			</td>
			<td style='width:19%'>
				<b>From Price</b>
			</td>
			<td style='width:19%'>
				<b>To Price</b>
			</td>
			<td style='width:19%'>
				<b>Charge</b>
			</td>
		</tr>
		<tr class='hide defaultServiceCharge serviceChargeRow newItemRow serviceChargeMatrixInfo'>
			<td class='fieldValue' style='width:5%'>
				<a class='deleteServiceChargeButton'>
					<i title="Delete" class="icon-trash alignMiddle"></i>
				</a>
			</td>
			<td class='fieldValue' style='width:19%'>
				<input type='number' class='input-medium' name='priceFromServiceBaseCharge' step='0.0001' tyle='width:95%' value />
			</td>
			<td class='fieldValue' style='width:19%'>
				<input type='number' class='input-medium' name='priceToServiceBaseCharge' step='0.0001' tyle='width:95%'  value />
			</td>
			<td class='fieldValue' style='width:19%'>
				<input type='number' class='input-medium' name='chargeServiceBaseCharge' step='0.01' tyle='width:95%'  value />
			</td>
		</tr>
		<input type='hidden' class='hide' name='numServiceCharge' value='{count($SERVICECHARGE)}'>
		{foreach item=SERVICECHARGEROW key=ROW_NUM from=$SERVICECHARGE}
			<tr class='serviceChargeRow serviceChargeMatrixInfo{if $SERVICECHARGEMATRIX} hide{/if}' id='serviceChargePlusRow{$ROW_NUM+1}'>
				<td class='fieldValue' style='width:5%'>
					<a class='deleteServiceChargeButton'>
						<i title="Delete" class="icon-trash alignMiddle"></i>
					</a>
				</td>
				<td class='fieldValue' style='width:19%'>
					<input type='number' class='input-medium' name='priceFromServiceBaseCharge{$ROW_NUM+1}' step='0.0001' style='width:95%' value='{$SERVICECHARGEROW.price_from}' />
				</td>
				<td class='fieldValue' style='width:19%'>
					<input type='number' class='input-medium' name='priceToServiceBaseCharge{$ROW_NUM+1}' step='0.0001' style='width:95%'  value='{$SERVICECHARGEROW.price_to}' />
				</td>
				<td class='fieldValue' style='width:19%'>
					<input type='number' class='input-medium' name='chargeServiceBaseCharge{$ROW_NUM+1}' step='0.01' style='width:95%'  value='{$SERVICECHARGEROW.factor}' />
				</td>
				<td class='fieldValue hide'>
					<input type='text' class='lineItemId' name='lineItemIdServiceBaseCharge{$ROW_NUM+1}' value='{$SERVICECHARGEROW.line_item_id}' />
				</td>
			</tr>
		{/foreach}
	{else if $BLOCK_LABEL eq 'LBL_TARIFFSERVICES_WEIGHTMILEAGE'}
		<tr>
			<td colspan='6'>
				<button type='button' id='addWeightMileage'>+</button>
				<button type='button' id='addWeightMileage2' style='clear:right;float:right'>+</button><br />
			</td>
		</tr>
		<tr>
			<td style='width:5%'>
				&nbsp;
			</td>
			<td style='width:19%'>
				<b>From Miles</b>
			</td>
			<td style='width:19%'>
				<b>To Miles</b>
			</td>
			<td style='width:19%'>
				<b>From Weight</b>
			</td>
			<td style='width:19%'>
				<b>To Weight</b>
			</td>
			<td style='width:19%'>
				<b>Base Rate</b>
			</td>
		</tr>
		<tr class='hide defaultWeightMileage weightMileageRow newItemRow'>
			<td class='fieldValue' style='width:5%'>
				<a class='deleteWeightMileageButton'>
					<i title="Delete" class="icon-trash alignMiddle"></i>
				</a>
			</td>
			<td class='fieldValue' style='width:19%'>
				<input type='number' class='input-medium' name='fromMilesWeightMileage' style='width:95%' value />
			</td>
			<td class='fieldValue' style='width:19%'>
				<input type='number' class='input-medium' name='toMilesWeightMileage' style='width:95%'  value />
			</td>
			<td class='fieldValue' style='width:19%'>
				<input type='number' class='input-medium' name='fromWeightWeightMileage' style='width:95%'  value />
			</td>
			<td class='fieldValue' style='width:19%'>
				<input type='number' class='input-medium' name='toWeightWeightMileage' style='width:95%'  value />
			</td>
			<td class='fieldValue' style='width:19%'>
				<input type='number' class='input-medium' name='baseRateWeightMileage' step='0.01' style='width:95%'  value />
			</td>
		</tr>
		<input type='hidden' class='hide' name='numWeightMileage' value='{count($WEIGHTMILEAGE)}'>
		{foreach item=WEIGHTMILEAGEROW key=ROW_NUM from=$WEIGHTMILEAGE}
			<tr class='weightMileageRow' id='weightMileagePlusRow{$ROW_NUM+1}'>
				<td class='fieldValue' style='width:5%'>
					<a class='deleteWeightMileageButton'>
						<i title="Delete" class="icon-trash alignMiddle"></i>
					</a>
				</td>
				<td class='fieldValue' style='width:19%'>
					<input type='number' class='input-medium' name='fromMilesWeightMileage{$ROW_NUM+1}' style='width:95%' value='{$WEIGHTMILEAGEROW.from_miles}' />
				</td>
				<td class='fieldValue' style='width:19%'>
					<input type='number' class='input-medium' name='toMilesWeightMileage{$ROW_NUM+1}' style='width:95%'  value='{$WEIGHTMILEAGEROW.to_miles}' />
				</td>
				<td class='fieldValue' style='width:19%'>
					<input type='number' class='input-medium' name='fromWeightWeightMileage{$ROW_NUM+1}' style='width:95%'  value='{$WEIGHTMILEAGEROW.from_weight}' />
				</td>
				<td class='fieldValue' style='width:19%'>
					<input type='number' class='input-medium' name='toWeightWeightMileage{$ROW_NUM+1}' style='width:95%'  value='{$WEIGHTMILEAGEROW.to_weight}' />
				</td>
				<td class='fieldValue' style='width:19%'>
					<input type='number' class='input-medium' name='baseRateWeightMileage{$ROW_NUM+1}' step='0.01' style='width:95%'  value='{$WEIGHTMILEAGEROW.base_rate}' />
				</td>
				<td class='fieldValue hide'>
					<input type='text' class='lineItemId' name='lineItemIdWeightMileage{$ROW_NUM+1}' value='{$WEIGHTMILEAGEROW.line_item_id}' />
				</td>
			</tr>
		{/foreach}
	{else if $BLOCK_LABEL eq 'LBL_TARIFFSERVICES_CWTBYWEIGHT'}
		<tr>
			<td colspan='6'>
				<button type='button' id='addCWTbyWeight'>+</button>
				<button type='button' id='addCWTbyWeight2' style='clear:right;float:right'>+</button><br />
			</td>
		</tr>
		<tr>
			<td style='width:5%'>
				&nbsp;
				<input type='hidden' class='hide' name='numCWTbyWeight' value='{$CWTBYWEIGHT|@count}'>
			</td>
			<td style='width:19%'>
				<b>From Weight</b>
			</td>
			<td style='width:19%'>
				<b>To Weight</b>
			</td>
			<td style='width:19%'>
				<b>Base Rate</b>
			</td>
		</tr>
		<tr class='hide defaultCWTbyWeight CWTbyWeightRow newItemRow'>
			<td class='fieldValue' style='width:5%'>
				<a class='deleteCWTbyWeight'>
					<i title="Delete" class="icon-trash alignMiddle"></i>
				</a>
			</td>
			<td class='fieldValue' style='width:19%'>
				<input type='number' class='input-medium' name='fromWeightCWTbyWeight' style='width:95%'  value />
			</td>
			<td class='fieldValue' style='width:19%'>
				<input type='number' class='input-medium' name='toWeightCWTbyWeight' style='width:95%'  value />
			</td>
			<td class='fieldValue' style='width:19%'>
				<input type='number' class='input-medium' name='baseRateCWTbyWeight' step='0.01' style='width:95%'  value />
			</td>
		</tr>
		{foreach item=CWTBYWEIGHTROW key=ROW_NUM from=$CWTBYWEIGHT}
			<tr class='CWTbyWeightRow' id='CWTbyWeightRow{$ROW_NUM+1}'>
				<td class='fieldValue' style='width:5%'>
					<a class='deleteCWTbyWeightButton'>
						<i title="Delete" class="icon-trash alignMiddle"></i>
					</a>
				</td>
				<td class='fieldValue' style='width:19%'>
					<input type='number' class='input-medium' name='fromWeightCWTbyWeight{$ROW_NUM+1}' style='width:95%'  value='{$CWTBYWEIGHTROW.from_weight}' />
				</td>
				<td class='fieldValue' style='width:19%'>
					<input type='number' class='input-medium' name='toWeightCWTbyWeight{$ROW_NUM+1}' style='width:95%'  value='{$CWTBYWEIGHTROW.to_weight}' />
				</td>
				<td class='fieldValue' style='width:19%'>
					<input type='number' class='input-medium' name='baseRateCWTbyWeight{$ROW_NUM+1}' step='0.01' style='width:95%'  value='{$CWTBYWEIGHTROW.rate}' />
				</td>
				<td class='fieldValue hide'>
					<input type='text' class='lineItemId' name='lineItemIdCWTbyWeight{$ROW_NUM+1}' value='{$CWTBYWEIGHTROW.line_item_id}' />
				</td>
			</tr>
		{/foreach}
	{else if $BLOCK_LABEL eq 'LBL_TARIFFSERVICES_BULKY'}
		<tr>
            {if !getenv('DISALLOW_CUSTOM_LOCAL_BULKY_LIST')}
                <td colspan='5'>
                    <button type='button' id='addBulky'>+</button>
                    <button type='button' id='addBulky2' style='clear:right;float:right'>+</button><br />
                </td>
            {/if}
		</tr>
		<tr>
			<td style='width:5%'>
				&nbsp;
			</td>
			<td colspan="2" style='text-align:center'>
				<b>Description</b>
			</td>
			<td style='width:20%;text-align:center'>
				<b>Weight Add</b>
			</td>
			<td style='width:20%;text-align:center'>
				<b>Rate</b>
			</td>
		</tr>
		<tr class='hide defaultBulky bulkyRow newItemRow'>
			<td class='fieldValue' style='width:5%'>
				<a class='deleteBulkyButton'>
					<i title="Delete" class="icon-trash alignMiddle"></i>
				</a>
			</td>
			<td class='fieldValue' colspan="2" style='text-align:center'>
				<input type='text' class='input-large' name='bulkyDescription' style='width:85%;text-align:center' value />
			</td>
			<td class='fieldValue' style='width:20%;text-align:center'>
				<input type='number' class='input-medium' name='bulkyWeight' style='width:85%;text-align:center' value />
			</td>
			<td class='fieldValue' style='width:20%;text-align:center'>
				<input type='number' class='input-medium' name='bulkyRate' style='width:85%;text-align:center' step='0.01' value />
			</td>
		</tr>
		{assign var=NUMROWS value=0}
    {assign var=BULKY_ROW_NUM value=1}
		{foreach item=BULKYROW key=ROW_NUM from=$BULKYITEMS}
			{assign var=NUMROWS value=$NUMROWS+1}
      {assign var=BULKY_ROW_NUM value=$BULKY_ROW_NUM+1}
			<tr class='bulkyRow' id='bulkyRow{$ROW_NUM+1}'>
				<td class='fieldValue' style='width:5%'>
                    {if getenv('DELETE_STANDARD_LOCAL_BULKY_LIST') || !$BULKYROW.standardItem}
                        <a class='deleteBulkyButton'>
                            <i title="Delete" class="icon-trash alignMiddle"></i>
                        </a>
                    {/if}
				</td>
				<td class='fieldValue' colspan="2" style='text-align:center'>
					<input type='text' class='input-large' name='bulkyDescription{$ROW_NUM+1}' style='width:85%;text-align:center' value='{$BULKYROW.description}' readonly=true/>
				</td>
				<td class='fieldValue' style='width:20%;text-align:center'>
					<input type='number' class='input-medium' name='bulkyWeight{$ROW_NUM+1}' style='width:85%;text-align:center' value='{$BULKYROW.weight}' />
				</td>
				<td class='fieldValue' style='width:20%;text-align:center'>
					<input type='number' class='input-medium' name='bulkyRate{$ROW_NUM+1}' style='width:85%;text-align:center' step='0.01' value='{$BULKYROW.rate}' />
				</td>
				<td class='fieldValue hide'>
					<input type='text' class='lineItemId' name='bulkyLineItemId{$ROW_NUM+1}' value='{$BULKYROW.line_item_id}' />
					<input type='text' class='lineItemId' name='CartonBulkyId{$ROW_NUM+1}' value='{$BULKYROW.CartonBulkyId}' />
				</td>
			</tr>
		{/foreach}
        <input type='hidden' class='hide' name='numBulky' value='{$BULKY_ROW_NUM}'>
	{else if $BLOCK_LABEL eq 'LBL_TARIFFSERVICES_CHARGEPERHUNDRED'}
		<tr>
            <td class="fieldLabel medium">
                <label class="muted pull-right marginRight10px">Has Released Valuation</label>
			</td>
            <td class="fieldValue medium">
                <input type="checkbox" name="chargePerHundredHasReleased" {if $RELEASED_VALUATION.has_released}checked="checked"{/if} />
			</td>
            <td class="fieldLabel medium">
                <label class="muted pull-right marginRight10px">Default Released Valuation Amount</label>
            </td>
            <td class="fieldValue medium">
                <div class="row-fluid">
                    <span class="span10">
                        <div class="input-prepend">
                            <span class="add-on">$</span>
                            <input type="number" step="0.01" min="0" class="input-medium" name="chargePerHundredDefaultReleased" value="{$RELEASED_VALUATION.released_amount}" />
                        </div>
                    </span>
                </div>
            </td>
        </tr>
        <tr>
			<td class='fieldLabel'>
                <label class="muted pull-right marginRight10px">Rate per pound for Min Declared Valuation</label>
			</td>
			<td class='fieldValue'>
				<input type='number' class='input-medium' step='0.01' name='chargePerHundredMultiplier' value="{$CHARGESPERHUNDRED[0].multiplier}" />
			</td>
			<td class='fieldLabel'>
                &nbsp;
			</td>
			<td class='fieldValue'>
				&nbsp;
			</td>
		</tr>
		<tr>
			<td colspan='5'>
				<button type='button' id='addChargePerHundred'>+</button>
				<button type='button' id='addChargePerHundred2' style='clear:right;float:right'>+</button><br />
			</td>
		</tr>
		<tr>
			<td style='width:5%;text-align:center'>
				&nbsp;
			</td>
			<td colspan='2' style='text-align:center'>
				<b>Deductible</b>
			</td>
			<td colspan='2' style='text-align:center'>
				<b>Rate</b>
			</td>
		</tr>
		<tr class='hide defaultChargePerHundred chargePerHundredRow newItemRow'>
			<td class='fieldValue' style='width:5%'>
				<a class='deleteChargePerHundredButton'>
					<i title="Delete" class="icon-trash alignMiddle"></i>
				</a>
			</td>
			<td class='fieldValue' colspan='2' style='text-align:center'>
				<input type='number' class='input-large' step='0.01' name='chargePerHundredDeductible' style='width:85%;text-align:center' value />
			</td>
			<td class='fieldValue' colspan='2' style='text-align:center'>
				<input type='number' class='input-medium' step='0.01' name='chargePerHundredRate' style='width:85%;text-align:center' value />
			</td>
		</tr>
		<input type='hidden' class='hide' name='numChargePer100' value='{count($CHARGESPERHUNDRED)}'>
		{foreach item=CHARGEROW key=ROW_NUM from=$CHARGESPERHUNDRED}
			<tr class='chargePerHundredRow' id='chargePerHundredRow{$ROW_NUM+1}'>
				<td class='fieldValue' style='width:5%'>
					<a class='deleteChargePerHundredButton'>
						<i title="Delete" class="icon-trash alignMiddle"></i>
					</a>
				</td>
				<td class='fieldValue' colspan='2' style='text-align:center'>
					<input type='number' class='input-large' step='0.01' name='chargePerHundredDeductible{$ROW_NUM+1}' style='width:85%;text-align:center' value='{$CHARGEROW.deductible}' />
				</td>
				<td class='fieldValue' colspan='2' style='text-align:center'>
					<input type='number' class='input-medium' step='0.01' name='chargePerHundredRate{$ROW_NUM+1}' style='width:85%;text-align:center' value='{$CHARGEROW.rate}' />
				</td>
				<td class='fieldValue hide'>
					<input type='text' class='lineItemId' name='chargePerHundredLineItemId{$ROW_NUM+1}' value='{$CHARGEROW.line_item_id}' />
				</td>
			</tr>
		{/foreach}
	{else if $BLOCK_LABEL eq 'LBL_TARIFFSERVICES_COUNTYCHARGE'}
		<!-- {$COUNTYCHARGES|@print_r} -->
		<tr>
			<td colspan='3'>
				<button type='button' id='addCounty'>+</button>
				<button type='button' id='addCounty2' style='clear:right;float:right'>+</button><br />
			</td>
		</tr>
		<tr>
			<td class='fieldValue' style='width:5%;text-align:center'>
				&nbsp;
			</td>
			<td class='fieldValue' style='width:60%;text-align:center'>
				<b>County Name</b>
			</td>
			<td class='fieldValue' style='width:35%;text-align:center'>
				<b>Rate</b>
			</td>
		</tr>
		<tr class='hide defaultCounty countyRow newItemRow'>
			<td class='fieldValue' style='width:5%'>
				<a class='deleteCountyButton'>
					<i title="Delete" class="icon-trash alignMiddle"></i>
				</a>
			</td>
			<td class='fieldValue' style='width:60%;text-align:center'>
				<input type='text' class='input-large' name='countyName' style='width:85%;text-align:center' value />
			</td>
			<td class='fieldValue' style='width:35%;text-align:center'>
				<input type='number' class='input-medium' step='0.01' name='countyRate' style='width:85%;text-align:center' value />
			</td>
		</tr>
		{assign var=NUMROWS value=$COUNTYCHARGES|@count}

		<input type='hidden' class='hide' name='numCountyCharges' value='{if $NUMROWS == 0}{$DEFAULTCOUNTIES|@count}{else}{$NUMROWS}{/if}'>
		{foreach item=COUNTYROW key=ROW_NUM from=$COUNTYCHARGES}
			<tr class='countyRow' id='countyRow{$ROW_NUM+1}'>
				<td class='fieldValue' style='width:5%'>
					<a class='deleteCountyButton'>
						<i title="Delete" class="icon-trash alignMiddle"></i>
					</a>
				</td>
				<td class='fieldValue' style='width:60%;text-align:center'>
					<input type='text' class='input-large' name='countyName{$ROW_NUM+1}' style='width:85%;text-align:center' value='{$COUNTYROW.name}' />
				</td>
				<td class='fieldValue' style='width:35%;text-align:center'>
					<input type='number' class='input-medium' ste='0.01' name='countyRate{$ROW_NUM+1}' style='width:85%;text-align:center' value='{$COUNTYROW.rate}' />
				</td>
				<td class='fieldValue hide'>
					<input type='text' class='lineItemId' name='countyLineItemId{$ROW_NUM+1}' value='{$COUNTYROW.line_item_id}' />
				</td>
			</tr>
		{/foreach}

		{if $NUMROWS < 1}

			{foreach item=COUNTY key=ROW_NUM from=$DEFAULTCOUNTIES}
				<tr class='countyRow newItemRow' id='countyRow{$ROW_NUM+1}'>
					<td class='fieldValue' style='width:5%'>
						<a class='deleteCountyButton'>
							<i title="Delete" class="icon-trash alignMiddle"></i>
						</a>
					</td>
					<td class='fieldValue' style='width:60%;text-align:center'>
						<input type='text' class='input-large' name='countyName{$ROW_NUM+1}' style='width:85%;text-align:center' value='{$COUNTY}' />
					</td>
					<td class='fieldValue' style='width:35%;text-align:center'>
						<input type='number' class='input-medium' step='0.01' name='countyRate{$ROW_NUM+1}' style='width:85%;text-align:center' value='0.00' />
					</td>
				</tr>
			{/foreach}
		{/if}
		<input type='text' class='hide' name='numRows' value='{$ROW_NUM+2}'>
	{else if $BLOCK_LABEL eq 'LBL_TARIFFSERVICES_HOURLYSET'}
		<tr>
			<td colspan='6'>
				<button type='button' id='addHourly'>+</button>
				<button type='button' id='addHourly2' style='clear:right;float:right'>+</button><br />
			</td>
		</tr>
		<tr>
			<td class='fieldValue' style='width:5%'>
				&nbsp;
			</td>
			<td class='fieldValue hasVanCol' colspan='{if $HASVANS[0] == 0}3{else}2{/if}' style='text-align:center'>
				<b>Men</b>
			</td>
			<td class='fieldValue hasVans{if $HASVANS[0] == 0} hide{/if}' colspan='2' style='text-align:center'>
				<b>Vans</b>
			</td>
			<td class='fieldValue hasVanCol' colspan='{if $HASVANS[0] == 0}3{else}2{/if}' style='text-align:center'>
				<b>Rate</b>
			</td>
		</tr>
		<tr class='hide defaultHourly hourlyRow newItemRow'>
			<td class='fieldValue' style='width:5%'>
				<a class='deleteHourlyButton'>
					<i title="Delete" class="icon-trash alignMiddle"></i>
				</a>
			</td>
			<td class='fieldValue hasVanCol' colspan='{if $HASVANS[0] == 0}3{else}2{/if}' style='text-align:center'>
				<input type='number' class='input-medium' style='width:85%;text-align:center' name='hourlyMen' value />
			</td>
			<td class='fieldValue hasVans{if $HASVANS[0] == 0} hide{/if}' colspan='2' style='text-align:center'>
				<input type='number' class='input-medium' style='width:85%;text-align:center' name='hourlyVans' value />
			</td>
			<td class='fieldValue hasVanCol' colspan='{if $HASVANS[0] == 0}3{else}2{/if}' style='text-align:center'>
				<input type='number' class='input-medium' step='0.01' style='width:85%;text-align:center' name='hourlyRate' value />
			</td>
		</tr>
		<input type='hidden' class='hide' name='numHourly' value='{count($HOURLYSET)}'>
		{foreach item=HOURLYROW key=ROW_NUM from=$HOURLYSET}
			<tr class='hourlyRow' id='hourlyRow{$ROW_NUM+1}'>
				<td class='fieldValue' style='width:5%'>
					<a class='deleteHourlyButton'>
						<i title="Delete" class="icon-trash alignMiddle"></i>
					</a>
				</td>
				<td class='fieldValue hasVanCol' colspan='{if $HASVANS[0] == 0}3{else}2{/if}' style='text-align:center'>
					<input type='number' class='input-medium' style='width:85%;text-align:center' name='hourlyMen{$ROW_NUM+1}' value='{$HOURLYROW.men}' />
				</td>
				<td class='fieldValue hasVans{if $HASVANS[0] == 0} hide{/if}' colspan='2' style='text-align:center'>
					<input type='number' class='input-medium' style='width:85%;text-align:center' name='hourlyVans{$ROW_NUM+1}' value='{$HOURLYROW.vans}' />
				</td>
				<td class='fieldValue hasVanCol' colspan='{if $HASVANS[0] == 0}3{else}2{/if}' style='text-align:center'>
					<input type='number' class='input-medium' step='0.01' style='width:85%;text-align:center' name='hourlyRate{$ROW_NUM+1}' value='{$HOURLYROW.rate}' />
				</td>
				<td class='fieldValue hide'>
					<input type='text' class='lineItemId' name='hourlyLineItemId{$ROW_NUM+1}' value='{$HOURLYROW.line_item_id}' />
				</td>
			</tr>
		{/foreach}
	{else if $BLOCK_LABEL eq 'LBL_TARIFFSERVICES_PACKING'}
		{assign var=numColumns value=0}

		{if $HASCONTAINER eq 1}{assign var=numColumns value=($numColumns+1)}{/if}
		{if $HASPACKING eq 1}{assign var=numColumns value=($numColumns+1)}{/if}
		{if $HASUNPACKING eq 1}{assign var=numColumns value=($numColumns+1)}{/if}
		{assign var=calcwidth value=60}
		{if $numColumns > 0}
			{assign var=calcwidth value=(60/$numColumns)}
		{/if}

			<td colspan='5'>
            {if !getenv('DISALLOW_CUSTOM_LOCAL_PACKING')}
				<button type='button' id='addCarton'>+</button>
				<button type='button' id='addCarton2' style='clear:right;float:right'>+</button><br />
            {else}
                &nbsp;
            {/if}
			</td>
		</tr>
		<tr>
			<td class='fieldValue' style='width:5%;text-align:center'>
				&nbsp;
			</td>
			<td class='fieldValue' style='width:35%;text-align:center'>
				<b>Carton Name</b>
			</td>
			<td class='fieldValue hasContainers{if $HASCONTAINER neq 1} hide{/if}' style='width:{$calcwidth}%;text-align:center'>
				<b>Container Rate</b>
			</td>
			<td class='fieldValue hasPacking{if $HASPACKING neq 1} hide{/if}' style='width:{$calcwidth}%;text-align:center'>
				<b>Packing Rate</b>
			</td>
			<td class='fieldValue hasUnpacking{if $HASUNPACKING neq 1} hide{/if}' style='width:{$calcwidth}%;text-align:center'>
				<b>Unpacking Rate</b>
			</td>
			<td class='noChecks {if $HASCONTAINER eq 1 || $HASPACKING eq 1 || $HASUNPACKING eq 1}hide{/if}' style='width:{$calcwidth}%;text-align:center'>
				&nbsp;
			</td>

		</tr>
		<tr class='hide defaultCarton cartonRow newItemRow'>
			<td class='fieldValue' style='width:5%'>
				<a class='deleteCartonButton'>
					<i title="Delete" class="icon-trash alignMiddle"></i>
				</a>
			</td>
			<td class='fieldValue' style='width:35%;text-align:center'>
				<input type='text' class='input-large' name='cartonName' style='width:85%;text-align:center' value />
			</td>
			<td class='fieldValue hasContainers{if $HASCONTAINER neq 1} hide{/if}' style='width:{$calcwidth}%;text-align:center'>
				<input type='number' class='input-medium' step='0.01' name='cartonContainerRate' style='width:85%;text-align:center' value="0.00" />
			</td>
			<td class='fieldValue hasPacking{if $HASPACKING neq 1} hide{/if}' style='width:{$calcwidth}%;text-align:center'>
				<input type='number' class='input-medium' step='0.01' name='cartonPackingRate' style='width:85%;text-align:center' value="0.00" />
			</td>
			<td class='fieldValue hasUnpacking{if $HASUNPACKING neq 1} hide{/if}' style='width:{$calcwidth}%;text-align:center'>
				<input type='number' class='input-medium' step='0.01' name='cartonUnpackingRate' style='width:85%;text-align:center' value="0.00" />
			</td>
			<td class='noChecks {if $HASCONTAINER eq 1 || $HASPACKING eq 1 || $HASUNPACKING eq 1}hide{/if}' style='width:{$calcwidth}%;text-align:center'>
				&nbsp;
			</td>

		</tr>
        {assign var=PACKING_ROW_NUM value=0}
		{foreach item=PACKINGROW key=ROW_NUM from=$PACKINGITEMS}
            {assign var=PACKING_ROW_NUM value=$PACKING_ROW_NUM+1}
			<tr class='cartonRow' id='cartonRow{$PACKING_ROW_NUM}'>
				<td class='fieldValue' style='width:5%'>
                    {if getenv('DELETE_STANDARD_LOCAL_PACKING') || !$PACKINGROW.standardItem}
					    <a class='deleteCartonButton'>
						    <i title="Delete" class='icon-trash alignMiddle'></i>
					    </a>
                    {/if}
				</td>
				<td class='fieldValue' style='width:35%;text-align:center'>
					<input type='text' class='input-large' name='cartonName{$PACKING_ROW_NUM}' style='width:85%;text-align:center' value='{$PACKINGROW.name}' {if $PACKINGROW.standardItem} readonly='readonly' {/if} />
				</td>
				<td class='fieldValue hasContainers{if $HASCONTAINER neq 1} hide{/if}' style='width:{$calcwidth}%;text-align:center'>
					<input type='number' class='input-medium' step='0.01' name='cartonContainerRate{$PACKING_ROW_NUM}' style='width:85%;text-align:center' value='{$PACKINGROW.container_rate}' />
				</td>
				<td class='fieldValue hasPacking{if $HASPACKING neq 1} hide{/if}' style='width:{$calcwidth}%;text-align:center'>
					<input type='number' class='input-medium' step='0.01' name='cartonPackingRate{$PACKING_ROW_NUM}' style='width:85%;text-align:center' value='{$PACKINGROW.packing_rate}' />
				</td>
				<td class='fieldValue hasUnpacking{if $HASUNPACKING neq 1} hide{/if}' style='width:{$calcwidth}%;text-align:center'>
					<input type='number' class='input-medium' step='0.01' name='cartonUnpackingRate{$PACKING_ROW_NUM}' style='width:85%;text-align:center' value='{$PACKINGROW.unpacking_rate}' />
				</td>
				<td class='fieldValue hide'>
					<input type='hidden' class='lineItemId' name='cartonLineItemId{$PACKING_ROW_NUM}' value='{$PACKINGROW.line_item_id}' />
					<input type='hidden' class='packItemId' name='packItemId{$PACKING_ROW_NUM}' value='{$PACKINGROW.pack_item_id}' />
					<input type='hidden' class='standardItem' name='standardItem{$PACKING_ROW_NUM}' value='{$PACKINGROW.standardItem}' />
				</td>
				<td class='noChecks {if $HASCONTAINER eq 1 || $HASPACKING eq 1 || $HASUNPACKING eq 1}hide{/if}' style='width:{$calcwidth}%;text-align:center'>
					&nbsp;
				</td>
			</tr>
		{/foreach}
        {*
		{if $NUMROWS < 1}
			{assign var=ROW_NUM value = 0}
			{foreach item=PACKINGNAME key=PACK_ITEM_ID from=$DEFAULTPACKING}
				<tr class='cartonRow' id='cartonRow{$ROW_NUM+1}'>
					<td class='fieldValue' style='width:5%'>
						<a class='deleteCartonButton'>
							<i title="Delete" class='icon-trash alignMiddle'></i>
						</a>
					</td>
					<td class='fieldValue' style='width:35%;text-align:center'>
						<input type='text' class='input-large' name='cartonName{$ROW_NUM+1}' style='width:85%;text-align:center' value='{$PACKINGNAME}' />
					</td>
					<td class='fieldValue hasContainers{if $HASCONTAINER neq 1} hide{/if}' style='width:{$calcwidth}%;text-align:center'>
						<input type='number' class='input-medium' step='0.01' name='cartonContainerRate{$ROW_NUM+1}' style='width:85%;text-align:center' value='0.00' />
					</td>
					<td class='fieldValue hasPacking{if $HASPACKING neq 1} hide{/if}' style='width:{$calcwidth}%;text-align:center'>
						<input type='number' class='input-medium' step='0.01' name='cartonPackingRate{$ROW_NUM+1}' style='width:85%;text-align:center' value='0.00' />
					</td>
					<td class='fieldValue hasUnpacking{if $HASUNPACKING neq 1} hide{/if}' style='width:{$calcwidth}%;text-align:center'>
						<input type='number' class='input-medium' step='0.01' name='cartonUnpackingRate{$ROW_NUM+1}' style='width:85%;text-align:center' value='0.00' />
						<input type='hidden' class='packItemId' name='packItemId{$ROW_NUM+1}' value='{$PACK_ITEM_ID}' />
					    <input type='hidden' class='standardItem' name='standardItem{$ROW_NUM+1}' value='1' />
					</td>
					<td class='noChecks {if $HASCONTAINER eq 1 || $HASPACKING eq 1 || $HASUNPACKING eq 1}hide{/if}' style='width:{$calcwidth}%;text-align:center'>
						&nbsp;
					</td>
				</tr>
				{assign var=ROW_NUM value=$ROW_NUM+1}
                {assign var=PACKING_ROW_NUM value=$PACKING_ROW_NUM+1}
			{/foreach}
		{/if}
        *}
        <input type='hidden' class='hide' name='numPacking' value='{$PACKING_ROW_NUM}'>
	{else if $BLOCK_LABEL eq 'LBL_TARIFFSERVICES_VALUATION'}
		<tr>
			<td class='fieldLabel medium'>
				<label class='muted pull-right marginRight10px'>Rate per pound for Min Declared Valuation</label>
			</td>
			<td class='fieldValue' colspan='2'>
				<input type='number' class='input-medium' step='0.01' name='valuationMultipler' value="{$VALUATIONITEMS[0].multiplier}" />
			</td>
			<td class='fieldLabel medium'>
                &nbsp;
			</td>
			<td class='fieldValue' colspan='2'>
                &nbsp;
			</td>
		</tr>
		<tr>
			<td colspan='2'>
				<button type='button' id='editValAmounts'>Edit Valuation Amounts</button>
				<input type='hidden' id='valuationNum' name='valuationNum' value='{$VALUATIONITEMS|@count}'>
			</td>
			<td colspan='3'>
				<button type='button' id='editValDeductibles'>Edit Valuation Deductibles</button>
			</td>
		</tr>
		<tr>
			<td class='fieldValue' colspan='2' style='text-align:center'>
				<b>Amount</b>
			</td>
			<td class='fieldValue' colspan='2' style='text-align:center'>
				<b>Deductible</b>
			</td>
			<td class='fieldValue' style='text-align:center'>
				<b>Cost</b>
			</td>
		</tr>
		<tr class='hide defaultValuation valuationRow newItemRow'>
			<td class='fieldValue' colspan='2' style='text-align:center'>
				<input type='text' class='input-medium amount' name='valuationAmount' style='width:85%;text-align:center' value readonly />
				<input type='hidden' id='amountRow' name='amountRow' value=''>
			</td>
			<td class='fieldValue' colspan='2' style='text-align:center'>
				<input type='text' class='input-medium deductible' name='valuationDeductible' style='width:85%;text-align:center' value readonly />
				<input type='hidden' id='deductibleRow' name='deductibleRow' value=''>
			</td>
			<td class='fieldValue' style='text-align:center'>
				<input type='number' class='input-medium cost' name='valuationCost' step='1' onchange='TariffServices_Edit_Js.setDecimalPlaces(this)' style='width:85%;text-align:center' value />
			</td>
		</tr>
		{foreach item=VALUATIONROW key=ROW_NUM from=$VALUATIONITEMS}
			{foreach item=IDAMOUNT key=ID_NUM_AMOUNT from=$VALAMOUNTS}
				{if $VALUATIONROW.amount==$IDAMOUNT}{$AMOUNT_ID=$ID_NUM_AMOUNT}{/if}
			{/foreach}
			{foreach item=IDDEDUCTIBLE key=ID_NUM_DEDUCTIBLE from=$DEDUCTIBLES}
				{if $VALUATIONROW.deductible==$IDDEDUCTIBLE}{$DEDUCTIBLE_ID=$ID_NUM_DEDUCTIBLE}{/if}
			{/foreach}
			<tr class='valuationRow valAmount{$ROWRELATION[$VALUATIONROW['amount_row']]} valDeductible{$ROWRELATION[$VALUATIONROW['deductible_row']]}' id='valuationRow{$ROW_NUM+1}'>
				<td class='fieldValue' colspan='2' style='text-align:center'>
					<input type='text' class='input-medium amount amount{$AMOUNT_ID}' name='valuationAmount{$ROW_NUM+1}' style='width:85%;text-align:center' value='{$VALUATIONROW.amount}' readonly />
					<input type='hidden' id='amountRow{$ROW_NUM+1}' name='amountRow{$ROW_NUM+1}' value='amountRow{$ROW_NUM+1}'>
				</td>
				<td class='fieldValue' colspan='2' style='text-align:center'>
					<input type='text' class='input-medium deductible deductible{$DEDUCTIBLE_ID}' name='valuationDeductible{$ROW_NUM+1}' style='width:85%;text-align:center' value='{$VALUATIONROW.deductible}' readonly />
					<input type='hidden' id='deductibleRow{$ROW_NUM+1}' name='deductibleRow{$ROW_NUM+1}' value='deductibleRow{$ROW_NUM+1}'>
				</td>
				<td class='fieldValue' style='text-align:center'>
					<input type='number' class='input-medium cost' step='1' onchange='TariffServices_Edit_Js.setDecimalPlaces(this)' name='valuationCost{$ROW_NUM+1}' style='width:85%;text-align:center' value='{$VALUATIONROW.cost}' />
				</td>
				<td class='fieldValue hide'>
					<input type='text' class='lineItemId' name='valuationLineItemId{$ROW_NUM+1}' value='{$VALUATIONROW.line_item_id}' />
				</td>
			</tr>
		{/foreach}
    {else if $BLOCK_LABEL eq 'LBL_TARIFFSERVICES_CWTPERQTY'}
    	<tr>
    		<td colspan='5'>
    			<button type='button' id='addCWTPerQty'>+</button>
    			<button type='button' id='addCWTPerQty2' style='clear:right;float:right'>+</button><br />
    		</td>
    	</tr>
    	<tr>
    		<td style='width:5%;text-align:center'>
    			&nbsp;
    		</td>
    		<td colspan='2' style='text-align:center'>
    			<b>Quantity</b>
    		</td>
    		<td colspan='2' style='text-align:center'>
    			<b>Rate</b>
    		</td>
    	</tr>
    	<tr class='hide defaultCWTPerQty cwtPerQtyRow newItemRow'>
    		<td class='fieldValue' style='width:5%'>
    			<a class='deleteCWTPerQty'>
    				<i title="Delete" class="icon-trash alignMiddle"></i>
    			</a>
    		</td>
    		<td class='fieldValue' colspan='2' style='text-align:center'>
    			<input type='number' class='input-large' step='1' name='cwtPerQty' style='width:85%;text-align:center' value />
    		</td>
    		<td class='fieldValue' colspan='2' style='text-align:center'>
    			<input type='number' class='input-medium' step='0.01' name='cwtPerQtyRate' style='width:85%;text-align:center' value />
    		</td>
    	</tr>
    	<input type='hidden' class='hide' name='numCWTPerQty' value='{count($CWTPERQTY)}'>
    	{foreach item=CHARGEROW key=ROW_NUM from=$CWTPERQTY}
    		<tr class='chargePerHundredRow' id='chargePerHundredRow{$ROW_NUM+1}'>
    			<td class='fieldValue' style='width:5%'>
    				<a class='deleteCWTPerQty'>
    					<i title="Delete" class="icon-trash alignMiddle"></i>
    				</a>
    			</td>
    			<td class='fieldValue' colspan='2' style='text-align:center'>
    				<input type='number' class='input-large' step='1' name='cwtPerQty{$ROW_NUM+1}' style='width:85%;text-align:center' value='{$CHARGEROW.quantity}' />
    			</td>
    			<td class='fieldValue' colspan='2' style='text-align:center'>
    				<input type='number' class='input-medium' step='0.01' name='cwtPerQtyRate{$ROW_NUM+1}' style='width:85%;text-align:center' value='{$CHARGEROW.rate}' />
    			</td>
    			<td class='fieldValue hide'>
    				<input type='text' class='lineItemId' name='chargePerHundredLineItemId{$ROW_NUM+1}' value='{$CHARGEROW.line_item_id}' />
    			</td>
    		</tr>
    	{/foreach}
    {else if $BLOCK_LABEL eq 'LBL_TARIFFSERVICES_FLATRATEBYWEIGHT'}
		<tr>
			<td colspan='6'>
				<button type='button' id='addFlatRateByWeight'>+</button>
				<button type='button' id='addFlatRateByWeight2' style='clear:right;float:right'>+</button><br />
			</td>
		</tr>
		<tr>
			<td style='width:5%'>
				<b>CWT Overflow Rate</b>
			</td>
			<td style='width:5%'>
				<input type='number' step='0.01' min='0' class='input-medium' name='flatratebyweight_cwtrate' style='width:95%' value='{$FLATRATEBYWEIGHT[0].cwt_rate}' />
			</td>
		</tr>
		<tr>
			<td style='width:5%'>
				&nbsp;
			</td>
			<td style='width:19%'>
				<b>From Weight</b>
			</td>
			<td style='width:19%'>
				<b>To Weight</b>
			</td>
			<td style='width:19%'>
				<b>Flat Rate</b>
			</td>
		</tr>
		<tr class='hide defaultFlatRateByWeight flatRateByWeightRow newItemRow'>
			<td class='fieldValue' style='width:5%'>
				<a class='deleteFlatRateByWeight'>
					<i title="Delete" class="icon-trash alignMiddle"></i>
				</a>
			</td>
			<td class='fieldValue' style='width:19%'>
				<input type='number' class='input-medium' name='flatratebyweight_from' style='width:95%'  value />
			</td>
			<td class='fieldValue' style='width:19%'>
				<input type='number' class='input-medium' name='flatratebyweight_to' style='width:95%'  value />
			</td>
			<td class='fieldValue' style='width:19%'>
				<input type='number' class='input-medium' name='flatratebyweight_rate' step='0.01' style='width:95%'  value />
			</td>
		</tr>
		<input type='hidden' class='hide' name='numRateByWeight' value='{count($FLATRATEBYWEIGHT)}'>
		{foreach item=FLATRATEBYWEIGHTROW key=ROW_NUM from=$FLATRATEBYWEIGHT}
			<tr class='flatRateByWeightRow' id='flatRateByWeightRow{$ROW_NUM+1}'>
				<td class='fieldValue' style='width:5%'>
					<a class='deleteWeightMileageButton'>
						<i title="Delete" class="icon-trash alignMiddle"></i>
					</a>
				</td>
				<td class='fieldValue' style='width:19%'>
					<input type='number' class='input-medium' name='flatratebyweight_from{$ROW_NUM+1}' style='width:95%' value='{$FLATRATEBYWEIGHTROW.from_weight}' />
				</td>
				<td class='fieldValue' style='width:19%'>
					<input type='number' class='input-medium' name='flatratebyweight_to{$ROW_NUM+1}' style='width:95%'  value='{$FLATRATEBYWEIGHTROW.to_weight}' />
				</td>
				<td class='fieldValue' style='width:19%'>
					<input type='number' step='0.01' min='0' class='input-medium' name='flatratebyweight_rate{$ROW_NUM+1}' step='0.01' style='width:95%'  value='{$FLATRATEBYWEIGHTROW.rate}' />
				</td>
				<td class='fieldValue hide'>
					<input type='text' class='lineItemId' name='flatratebyweight_lineitemId{$ROW_NUM+1}' value='{$FLATRATEBYWEIGHTROW.line_item_id}' />
				</td>
			</tr>
		{/foreach}
	{/if}
{/strip}
