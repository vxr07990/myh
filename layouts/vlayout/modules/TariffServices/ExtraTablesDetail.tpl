{strip}
	{if $BLOCK_LABEL_KEY eq 'LBL_TARIFFSERVICES_BASEPLUS'}
		<tr>
			<td style='width:5%'>
				&nbsp;
			</td>
			<td style='width:16%'>
				<b>From Miles</b>
			</td>
			<td style='width:16%'>
				<b>To Miles</b>
			</td>
			<td style='width:16%'>
				<b>From Weight</b>
			</td>
			<td style='width:16%'>
				<b>To Weight</b>
			</td>
			<td style='width:16%'>
				<b>Base Rate</b>
			</td>
			<td style='width:15%'>
				<b>Excess</b>
			</td>
		</tr>
		{foreach item=BASEPLUSROW key=ROW_NUM from=$BASEPLUS}
			<tr class='basePlusRow' id='basePlusRow{$ROW_NUM+1}'>
				<td class='fieldValue' style='width:5%'>
					<a class='deleteBasePlusButton'>
						<i title="Delete" class="icon-trash alignMiddle"></i>
					</a>
				</td>
				<td class='fieldValue' style='width:16%'>
					<span class='value' data-field-type='integer'>{$BASEPLUSROW.from_miles}</span>
					<span class='hide edit'>
						<input type='number' class='input-medium' name='fromMilesBasePlus{$ROW_NUM+1}' style='width:85%' value='{$BASEPLUSROW.from_miles}' />
						<input type='hidden' class='fieldname' value='fromMilesBasePlus' data-prev-value='{$BASEPLUSROW.from_miles}' />
					</span>
				</td>
				<td class='fieldValue' style='width:16%'>
					<span class='value' data-field-type='integer'>{$BASEPLUSROW.to_miles}</span>
					<span class='hide edit'>
						<input type='number' class='input-medium' name='toMilesBasePlus{$ROW_NUM+1}' style='width:85%' value='{$BASEPLUSROW.to_miles}' />
						<input type='hidden' class='fieldname' value='toMilesBasePlus' data-prev-value='{$BASEPLUSROW.to_miles}' />
					</span>
				</td>
				<td class='fieldValue' style='width:16%'>
					<span class='value' data-field-type='integer'>{$BASEPLUSROW.from_weight}</span>
					<span class='hide edit'>
						<input type='number' class='input-medium' name='fromWeightBasePlus{$ROW_NUM+1}' style='width:85%' value='{$BASEPLUSROW.from_weight}' />
						<input type='hidden' class='fieldname' value='fromWeightBasePlus{$ROW_NUM+1}' data-prev-value='{$BASEPLUSROW.from_weight}' />
					</span>
				</td>
				<td class='fieldValue' style='width:16%'>
					<span class='value' data-field-type='integer'>{$BASEPLUSROW.to_weight}</span>
					<span class='hide edit'>
						<input type='number' class='input-medium' name='toWeightBasePlus{$ROW_NUM+1}' style='width:85%' value='{$BASEPLUSROW.to_weight}' />
						<input type='hidden' class='fieldname' value='toWeightBasePlus{$ROW_NUM+1}' data-prev-value='{$BASEPLUSROW.to_weight}' />
					</span>
				</td>
				<td class='fieldValue' style='width:16%'>
					<span class='value' data-field-type='currency'>{$BASEPLUSROW.base_rate}</span>
					<span class='hide edit'>
						<div class='input-prepend'>
							<span class='add-on'>$</span>
							<input type='number' class='input-medium currencyField' name='baseRateBasePlus{$ROW_NUM+1}' step='0.01' style='width:75%' value='{$BASEPLUSROW.base_rate}' />
							<input type='hidden' class='fieldname' value='baseRateBasePlus{$ROW_NUM+1}' data-prev-value='{$BASEPLUSROW.base_rate}' />
						<div>
					</span>
				</td>
				<td class='fieldValue' style='width:15%'>
					<span class='value' data-field-type='currency'>{$BASEPLUSROW.excess}</span>
					<span class='hide edit'>
						<div class='input-prepend'>
							<span class='add-on'>$</span>
							<input type='number' class='input-medium currencyField' name='excessBasePlus{$ROW_NUM+1}' step='0.01' style='width:75%' value='{$BASEPLUSROW.excess}' />
							<input type='hidden' class='fieldname' value='excessBasePlus{$ROW_NUM+1}' data-prev-value='{$BASEPLUSROW.excess}' />
						</div>
					</span>
				</td>
				<td class='fieldValue hide'>
					<input type='text' class='lineItemId' name='basePlusLineItemId{$ROW_NUM+1}' value='{$BASEPLUSROW.line_item_id}' />
				</td>
			</tr>
		{/foreach}
	{else if $BLOCK_LABEL_KEY eq 'LBL_TARIFFSERVICES_BREAKPOINT'}
		<tr>
			<td style='width:5%'>
				&nbsp;
			</td>
			<td style='width:16%'>
				<b>From Miles</b>
			</td>
			<td style='width:16%'>
				<b>To Miles</b>
			</td>
			<td style='width:16%'>
				<b>From Weight</b>
			</td>
			<td style='width:16%'>
				<b>To Weight</b>
			</td>
			<td style='width:16%'>
				<b>Break Point</b>
			</td>
			<td style='width:15%'>
				<b>Base Rate</b>
			</td>
		</tr>
		{foreach item=BREAKPOINTROW key=ROW_NUM from=$BREAKPOINT}
			<tr class='breakPointRow' id='breakPointRow{$ROW_NUM+1}'>
				<td class='fieldValue' style='width:5%'>
					<a class='deleteBreakPointButton'>
						<i title="Delete" class="icon-trash alignMiddle"></i>
					</a>
				</td>
				<td class='fieldValue' style='width:16%'>
					<span class='value' data-field-type='integer'>{$BREAKPOINTROW.from_miles}</span>
					<span class='hide edit'>
						<input type='number' class='input-medium' name='fromMilesBreakPoint{$ROW_NUM+1}' style='width:85%' value='{$BREAKPOINTROW.from_miles}' />
						<input type='hidden' class='fieldname' value='fromMilesBreakPoint{$ROW_NUM+1}' data-prev-value='{$BREAKPOINTROW.from_miles}' />
					</span>
				</td>
				<td class='fieldValue' style='width:16%'>
					<span class='value' data-field-type='integer'>{$BREAKPOINTROW.to_miles}</span>
					<span class='hide edit'>
						<input type='number' class='input-medium' name='toMilesBreakPoint{$ROW_NUM+1}' style='width:85%' value='{$BREAKPOINTROW.to_miles}' />
						<input type='hidden' class='fieldname' value='toMilesBreakPoint{$ROW_NUM+1}' data-prev-value='{$BREAKPOINTROW.to_miles}' />
					</span>
				</td>
				<td class='fieldValue' style='width:16%'>
					<span class='value' data-field-type='integer'>{$BREAKPOINTROW.from_weight}</span>
					<span class='hide edit'>
						<input type='number' class='input-medium' name='fromWeightBreakPoint{$ROW_NUM+1}' style='width:85%' value='{$BREAKPOINTROW.from_weight}' />
						<input type='hidden' class='fieldname' value='fromWeightBreakPoint{$ROW_NUM+1}' data-prev-value='{$BREAKPOINTROW.from_weight}' />
					</span>
				</td>
				<td class='fieldValue' style='width:16%'>
					<span class='value' data-field-type='integer'>{$BREAKPOINTROW.to_weight}</span>
					<span class='hide edit'>
						<input type='number' class='input-medium' name='toWeightBreakPoint{$ROW_NUM+1}' style='width:85%' value='{$BREAKPOINTROW.to_weight}' />
						<input type='hidden' class='fieldname' value='toWeightBreakPoint{$ROW_NUM+1}' data-prev-value='{$BREAKPOINTROW.to_weight}' />
					</span>
				</td>
				<td class='fieldValue' style='width:16%'>
					<span class='value' data-field-type='integer'>{$BREAKPOINTROW.break_point}</span>
					<span class='hide edit'>
						<input type='number' class='input-medium' name='breakPointBreakPoint{$ROW_NUM+1}' style='width:85%' value='{$BREAKPOINTROW.break_point}' />
						<input type='hidden' class='fieldname' value='breakPointBreakPoint{$ROW_NUM+1}' data-prev-value='{$BREAKPOINTROW.break_point}' />
					</span>
				</td>
				<td class='fieldValue' style='width:15%'>
					<span class='value' data-field-type='currency'>{$BREAKPOINTROW.base_rate}</span>
					<span class='hide edit'>
						<div class='input-prepend'>
							<span class='add-on'>$</span>
							<input type='number' class='input-medium currencyField' name='baseRateBreakPoint{$ROW_NUM+1}' step='0.01' style='width:75%' value='{$BREAKPOINTROW.base_rate}' />
							<input type='hidden' class='fieldname' value='baseRateBasePlus{$ROW_NUM+1}' data-prev-value='{$BREAKPOINTROW.base_rate}' />
						</div>
					</span>
				</td>
				<td class='fieldValue hide'>
					<input type='text' class='lineItemId' name='breakPointLineItemId{$ROW_NUM+1}' value='{$BREAKPOINTROW.line_item_id}' />
				</td>
			</tr>
		{/foreach}
	{else if $BLOCK_LABEL_KEY eq 'LBL_TARIFFSERVICES_WEIGHTMILEAGE'}
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
		{foreach item=WEIGHTMILEAGEROW key=ROW_NUM from=$WEIGHTMILEAGE}
			<tr class='weightMileageRow' id='weightMileagePlusRow{$ROW_NUM+1}'>
				<td class='fieldValue' style='width:5%'>
					<a class='deleteWeightMileageButton'>
						<i title="Delete" class="icon-trash alignMiddle"></i>
					</a>
				</td>
				<td class='fieldValue' style='width:19%'>
					<span class='value' data-field-type='integer'>{$WEIGHTMILEAGEROW.from_miles}</span>
					<span class='hide edit'>
						<input type='number' class='input-medium' name='fromMilesWeightMileage{$ROW_NUM+1}' style='width:85%' value='{$WEIGHTMILEAGEROW.from_miles}' />
						<input type='hidden' class='fieldname' value='fromMilesWeightMileage{$ROW_NUM+1}' data-prev-value='{$WEIGHTMILEAGEROW.from_miles}' />
					</span>
				</td>
				<td class='fieldValue' style='width:19%'>
					<span class='value' data-field-type='integer'>{$WEIGHTMILEAGEROW.to_miles}</span>
					<span class='hide edit'>
						<input type='number' class='input-medium' name='toMilesWeightMileage{$ROW_NUM+1}' style='width:85%' value='{$WEIGHTMILEAGEROW.to_miles}' />
						<input type='hidden' class='fieldname' value='toMilesWeightMileage{$ROW_NUM+1}' data-prev-value='{$WEIGHTMILEAGEROW.to_miles}' />
					</span>
				</td>
				<td class='fieldValue' style='width:19%'>
					<span class='value' data-field-type='integer'>{$WEIGHTMILEAGEROW.from_weight}</span>
					<span class='hide edit'>
						<input type='number' class='input-medium' name='fromWeightWeightMileage{$ROW_NUM+1}' style='width:85%' value='{$WEIGHTMILEAGEROW.from_weight}' />
						<input type='hidden' class='fieldname' value='fromWeightWeightMileage{$ROW_NUM+1}' data-prev-value='{$WEIGHTMILEAGEROW.from_weight}' />
					</span>
				</td>
				<td class='fieldValue' style='width:19%'>
					<span class='value' data-field-type='integer'>{$WEIGHTMILEAGEROW.to_weight}</span>
					<span class='hide edit'>
						<input type='number' class='input-medium' name='toWeightWeightMileage{$ROW_NUM+1}' style='width:85%' value='{$WEIGHTMILEAGEROW.to_weight}' />
						<input type='hidden' class='fieldname' value='toWeightWeightMileage{$ROW_NUM+1}' data-prev-value='{$WEIGHTMILEAGEROW.to_weight}' />
					</span>
				</td>
				<td class='fieldValue' style='width:19%'>
					<span class='value' data-field-type='currency'>{$WEIGHTMILEAGEROW.base_rate}</span>
					<span class='hide edit'>
						<div class='input-prepend'>
							<span class='add-on'>$</span>
							<input type='number' class='input-medium currencyField' name='baseRateWeightMileage{$ROW_NUM+1}' style='width:85%' value='{$WEIGHTMILEAGEROW.base_rate}' />
							<input type='hidden' class='fieldname' value='baseRateWeightMileage{$ROW_NUM+1}' data-prev-value='{$WEIGHTMILEAGEROW.base_rate}' />
						</div>
					</span>
				</td>
				<td class='fieldValue hide'>
					<input type='text' class='lineItemId' name='weightMileageLineItemId{$ROW_NUM+1}' value='{$WEIGHTMILEAGEROW.line_item_id}' />
				</td>
			</tr>
		{/foreach}
	{foreach item=CWTBYWEIGHTEROW key=ROW_NUM from=$CWTBYWEIGHT}
			<tr class='CWTbyWeightRow' id='CWTbyWeightPlusRow{$ROW_NUM+1}'>
				<td class='fieldValue' style='width:5%'>
					<a class='deleteCWTbyWeightButton'>
						<i title="Delete" class="icon-trash alignMiddle"></i>
					</a>
				</td>
				<td class='fieldValue' style='width:19%'>
					<span class='value' data-field-type='integer'>{$CWTBYWEIGHTEROW.from_weight}</span>
					<span class='hide edit'>
						<input type='number' class='input-medium' name='fromWeightCWTbyWeight{$ROW_NUM+1}' style='width:85%' value='{$CWTBYWEIGHTEROW.from_weight}' />
						<input type='hidden' class='fieldname' value='fromWeightCWTbyWeight{$ROW_NUM+1}' data-prev-value='{$CWTBYWEIGHTEROW.from_weight}' />
					</span>
				</td>
				<td class='fieldValue' style='width:19%'>
					<span class='value' data-field-type='integer'>{$CWTBYWEIGHTEROW.to_weight}</span>
					<span class='hide edit'>
						<input type='number' class='input-medium' name='toWeightCWTbyWeight{$ROW_NUM+1}' style='width:85%' value='{$CWTBYWEIGHTEROW.to_weight}' />
						<input type='hidden' class='fieldname' value='toWeightCWTbyWeight{$ROW_NUM+1}' data-prev-value='{$CWTBYWEIGHTEROW.to_weight}' />
					</span>
				</td>
				<td class='fieldValue' style='width:19%'>
					<span class='value' data-field-type='currency'>{$CWTBYWEIGHTEROW.base_rate}</span>
					<span class='hide edit'>
						<div class='input-prepend'>
							<span class='add-on'>$</span>
							<input type='number' class='input-medium currencyField' name='baseRateCWTbyWeight{$ROW_NUM+1}' style='width:85%' value='{$CWTBYWEIGHTEROW.base_rate}' />
							<input type='hidden' class='fieldname' value='baseRateCWTbyWeight{$ROW_NUM+1}' data-prev-value='{$CWTBYWEIGHTEROW.base_rate}' />
						</div>
					</span>
				</td>
				<td class='fieldValue hide'>
					<input type='text' class='lineItemId' name='CWTbyWeightLineItemId{$ROW_NUM+1}' value='{$CWTBYWEIGHTEROW.line_item_id}' />
				</td>
			</tr>
		{/foreach}
	{else if $BLOCK_LABEL_KEY eq 'LBL_TARIFFSERVICES_BULKY'}
		<tr>
			<td style='width:5%'>
				&nbsp;
			</td>
			<td colspan="2" style='text-align:center'>
				<b>Description</b>
			</td>
			<td style='width:20%;text-align:center'>
				<b>Weight</b>
			</td>
			<td style='width:20%;text-align:center'>
				<b>Rate</b>
			</td>
		</tr>
		{foreach item=BULKYROW key=ROW_NUM from=$BULKYITEMS}
			<tr class='bulkyRow' id='bulkyRow{$ROW_NUM+1}'>
				<td class='fieldValue' style='width:5%'>
					<a class='deleteBulkyButton'>
						<i title="Delete" class="icon-trash alignMiddle"></i>
					</a>
				</td>
				<td class='fieldValue' colspan="2" style='text-align:center'>
					<span class='value' data-field-type='string'>{$BULKYROW.description}</span>
					<span class='hide edit'>
						<input type='text' class='input-large' name='bulkyDescription{$ROW_NUM+1}' style='width:85%;text-align:center' value='{$BULKYROW.description}' />
						<input type='hidden' class='fieldname' value='bulkyDescription{$ROW_NUM+1}' data-prev-value='{$BULKYROW.description}' />
					</span>
				</td>
				<td class='fieldValue' style='width:20%;text-align:center'>
					<span class='value' data-field-type='integer'>{$BULKYROW.weight}</span>
					<span class='hide edit'>
						<input type='number' class='input-medium' name='bulkyWeight{$ROW_NUM+1}' style='width:85%;text-align:center' value='{$BULKYROW.weight}' />
						<input type='hidden' class='fieldname' value='bulkyWeight{$ROW_NUM+1}' data-prev-value='{$BULKYROW.weight}' />
					</span>
				</td>
				<td class='fieldValue' style='width:20%;text-align:center'>
					<span class='value' data-field-type='currency'>{$BULKYROW.rate}</span>
					<span class='hide edit'>
						<div class='input-prepend'>
							<span class='add-on'>$</span>
							<input type='number' class='input-medium' name='bulkyRate{$ROW_NUM+1}' step='0.01' style='width:85%;text-align:center' value='{$BULKYROW.rate}' />
							<input type='hidden' class='fieldname' value='bulkyRate{$ROW_NUM+1}' data-prev-value='{$BULKYROW.rate}' />
						</div>
					</span>
				</td>
				<td class='fieldValue hide'>
					<input type='text' class='lineItemId' name='bulkyLineItemId{$ROW_NUM+1}' value='{$BULKYROW.line_item_id}' />
				</td>
			</tr>
		{/foreach}
	{else if $BLOCK_LABEL_KEY eq 'LBL_TARIFFSERVICES_CHARGEPERHUNDRED'}
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
		{foreach item=CHARGEROW key=ROW_NUM from=$CHARGESPERHUNDRED}
			<tr class='chargePerHundredRow' id='chargePerHundredRow{$ROW_NUM+1}'>
				<td class='fieldValue' style='width:5%'>
					<a class='deleteChargePerHundredButton'>
						<i title="Delete" class="icon-trash alignMiddle"></i>
					</a>
				</td>
				<td class='fieldValue' colspan='2' style='text-align:center'>
					<span class='value' data-field-type='currency'>{$CHARGEROW.deductible}</span>
					<span class='hide edit'>
						<div class='input-prepend'>
							<span class='add-on'>$</span>
							<input type='number' class='input-medium currencyField' name='chargePerHundredDeductible{$ROW_NUM+1}' step='0.01' style='width:85%;text-align:center' value='{$CHARGEROW.deductible}' />
							<input type='hidden' class='fieldname' value='chargePerHundredDeductible{$ROW_NUM+1}' data-prev-value='{$CHARGEROW.deductible}' />
						</div>
					</span>
				</td>
				<td class='fieldValue' colspan='2' style='text-align:center'>
					<span class='value' data-field-type='currency'>{$CHARGEROW.rate}</span>
					<span class='hide edit'>
						<div class='input-prepend'>
							<span class='add-on'>$</span>
							<input type='number' class='input-medium currencyField' name='chargePerHundredRate{$ROW_NUM+1}' step='0.01' style='width:85%;text-align:center' value='{$CHARGEROW.rate}' />
							<input type='hidden' class='fieldname' value='chargePerHundredRate{$ROW_NUM+1}' data-prev-value='{$CHARGEROW.rate}' />
						</div>
					</span>
				</td>
				<td class='fieldValue hide'>
					<input type='text' class='lineItemId' name='chargePerHundredLineItemId{$ROW_NUM+1}' value='{$CHARGEROW.line_item_id}' />
				</td>
			</tr>
		{/foreach}
	{else if $BLOCK_LABEL_KEY eq 'LBL_TARIFFSERVICES_COUNTYCHARGE'}
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
		{foreach item=COUNTYROW key=ROW_NUM from=$COUNTYCHARGES}
			<tr class='countyRow' id='countyRow{$ROW_NUM+1}'>
				<td class='fieldValue' style='width:5%'>
					<a class='deleteCountyButton'>
						<i title="Delete" class="icon-trash alignMiddle"></i>
					</a>
				</td>
				<td class='fieldValue' style='width:60%;text-align:center'>
					<span class='value' data-field-type='string'>{$COUNTYROW.name}</span>
					<span class='hide edit'>
						<input type='text' class='input-large' name='countyName{$ROW_NUM+1}' style='width:85%;text-align:center' value='{$COUNTYROW.name}' />
						<input type='hidden' class='fieldname' value='countyName{$ROW_NUM+1}' data-prev-value='{$COUNTYROW.name}' />
					</span>
				</td>
				<td class='fieldValue' style='width:35%;text-align:center'>
					<span class='value' data-field-type='currency'>{$COUNTYROW.rate}</span>
					<span class='hide edit'>
						<div class='input-prepend'>
							<span class='add-on'>$</span>
							<input type='number' class='input-medium currencyField' name='countyRate{$ROW_NUM+1}' style='width:75%;text-align:center' value='{$COUNTYROW.rate}' />
							<input type='hidden' class='fieldname' value='countyRate{$ROW_NUM+1}' data-prev-value='{$COUNTYROW.rate}' />
						</div>
					</span>
				</td>
				<td class='fieldValue hide'>
					<input type='text' class='lineItemId' name='countyLineItemId{$ROW_NUM+1}' value='{$COUNTYROW.line_item_id}' />
				</td>
			</tr>
		{/foreach}
	{else if $BLOCK_LABEL_KEY eq 'LBL_TARIFFSERVICES_HOURLYSET'}
		<tr>
			<td class='fieldValue' style='width:5%'>
				&nbsp;
			</td>
			<td class='fieldValue' colspan='2' style='text-align:center'>
				<b>Men</b>
			</td>
			<td class='fieldValue' colspan='2' style='text-align:center'>
				<b>Vans</b>
			</td>
			<td class='fieldValue' style='text-align:center'>
				<b>Rate</b>
			</td>
		</tr>
		{foreach item=HOURLYROW key=ROW_NUM from=$HOURLYSET}
			<tr class='hourlyRow' id='hourlyRow{$ROW_NUM+1}'>
				<td class='fieldValue' style='width:5%'>
					<a class='deleteHourlyButton'>
						<i title="Delete" class="icon-trash alignMiddle"></i>
					</a>
				</td>
				<td class='fieldValue' colspan='2' style='text-align:center'>
					<span class='value' data-field-type='integer'>{$HOURLYROW.men}</span>
					<span class='hide edit'>
						<input type='number' class='input-medium' name='hourlyMen{$ROW_NUM+1}' style='width:85%;text-align:center' value='{$HOURLYROW.men}' />
						<input type='hidden' class='fieldname' value='hourlyMen{$ROW_NUM+1}' data-prev-value='{$HOURLYROW.men}' />
					</span>
				</td>
				<td class='fieldValue' colspan='2' style='text-align:center'>
					<span class='value' data-field-type='integer'>{$HOURLYROW.vans}</span>
					<span class='hide edit'>
						<input type='number' class='input-medium' name='hourlyVans{$ROW_NUM+1}' style='width:85%;text-align:center' value='{$HOURLYROW.vans}' />
						<input type='hidden' class='fieldname' value='hourlyVans{$ROW_NUM+1}' data-prev-value='{$HOURLYROW.vans}' />
					</span>
				</td>
				<td class='fieldValue' style='text-align:center'>
					<span class='value' data-field-type='currency'>{$HOURLYROW.rate}</span>
					<span class='hide edit'>
						<div class='input-prepend'>
							<span class='add-on'>$</span>
							<input type='number' class='input-medium currencyField' name='hourlyRate{$ROW_NUM+1}' step='0.01' style='width:75%;text-align:center' value='{$HOURLYROW.rate}' />
							<input type='hidden' class='fieldname' value='hourlyRate{$ROW_NUM+1}' data-prev-value='{$HOURLYROW.rate}' />
						</div>
					</span>
				</td>
				<td class='fieldValue hide'>
					<input type='text' class='lineItemId' name='hourlyLineItemId{$ROW_NUM+1}' value='{$HOURLYROW.line_item_id}' />
				</td>
			</tr>
		{/foreach}
	{else if $BLOCK_LABEL_KEY eq 'LBL_TARIFFSERVICES_PACKING'}
		<tr>
			<td class='fieldValue' style='width:5%'>
				&nbsp;
			</td>
			<td class='fieldValue' style='text-align:center'>
				<b>Carton Name</b>
			</td>
			<td class='fieldValue' style='text-align:center'>
				<b>Container Rate</b>
			</td>
			<td class='fieldValue' style='width:25%;text-align:center'>
				<b>Packing Rate</b>
			</td>
			<td class='fieldValue' style='width:25%;text-align:center'>
				<b>Unpacking Rate</b>
			</td>
		</tr>
		{foreach item=PACKINGROW key=ROW_NUM from=$PACKINGITEMS}
			<tr class='cartonRow' id='cartonRow{$ROW_NUM+1}'>
				<td class='fieldValue' style='width:5%'>
					<a class='deleteCartonButton'>
						<i title="Delete" class='icon-trash alignMiddle'></i>
					</a>
				</td>
				<td class='fieldValue' style='text-align:center'>
					<span class='value' data-field-type='string'>{$PACKINGROW.name}</span>
					<span class='hide edit'>
						<input type='text' class='input-large' name='cartonName{$ROW_NUM+1}' style='width:85%;text-align:center' value='{$PACKINGROW.name}' />
						<input type='hidden' class='fieldname' value='cartonName{$ROW_NUM+1}' data-prev-value='{$PACKINGROW.name}' />
					</span>
				</td>
				<td class='fieldValue' style='text-align:center'>
					<span class='value' data-field-type='currency'>{$PACKINGROW.container_rate}</span>
					<span class='hide edit'>
						<div class='input-prepend'>
							<span class='add-on'>$</span>
							<input type='number' class='input-medium' name='cartonContainerRate{$ROW_NUM+1}' step='0.01' style='width:75%;text-align:center' value='{$PACKINGROW.container_rate}' />
							<input type='hidden' class='fieldname' value='cartonContainerRate{$ROW_NUM+1}' data-prev-value='{$PACKINGROW.container_rate}' />
						</div>
					</span>
				</td>
				<td class='fieldValue' style='text-align:center'>
					<span class='value' data-field-type='currency'>{$PACKINGROW.packing_rate}</span>
					<span class='hide edit'>
						<div class='input-prepend'>
							<span class='add-on'>$</span>
							<input type='number' class='input-medium' name='cartonPackingRate{$ROW_NUM+1}' step='0.01' style='width:75%;text-align:center' value='{$PACKINGROW.packing_rate}' />
							<input type='hidden' class='fieldname' value='cartonPackingRate{$ROW_NUM+1}' data-prev-value='{$PACKINGROW.packing_rate}' />
						</div>
					</span>
				</td>
				<td class='fieldValue' style='text-align:center'>
					<span class='value' data-field-type='currency'>{$PACKINGROW.unpacking_rate}</span>
					<span class='hide edit'>
						<div class='input-prepend'>
							<span class='add-on'>$</span>
							<input type='number' class='input-medium' name='cartonUnpackingRate{$ROW_NUM+1}' step='0.01' style='width:75%;text-align:center' value='{$PACKINGROW.unpacking_rate}' />
							<input type='hidden' class='fieldname' value='cartonUnpackingRate{$ROW_NUM+1}' data-prev-value='{$PACKINGROW.unpacking_rate}' />
						</div>
					</span>
				</td>
			</tr>
		{/foreach}
	{else if $BLOCK_LABEL_KEY eq 'LBL_TARIFFSERVICES_VALUATION'}
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
		{foreach item=VALUATIONROW key=ROW_NUM from=$VALUATIONITEMS}
			<tr>
				<td class='fieldValue' colspan='2' style='text-align:center'>
					<span class='value' data-field-type='currency'>${$VALUATIONROW.amount}</span>
				</td>
				<td class='fieldValue' colspan='2' style='text-align:center'>
					<span class='value' data-field-type='currency'>${$VALUATIONROW.deductible}</span>
				</td>
				<td class='fieldValue' style='text-align:center'>
					<span class='value' data-field-type='currency'>${$VALUATIONROW.cost}</span>
				</td>
			</tr>
		{/foreach}
	{/if}
{/strip}