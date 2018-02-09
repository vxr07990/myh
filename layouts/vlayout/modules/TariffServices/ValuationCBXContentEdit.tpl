{strip}
<script type='text/javascript' src='libraries/jquery/colorbox/jquery.colorbox-min.js'></script>
<link rel="stylesheet" href="libraries/jquery/colorbox/example1/colorbox.css" />
<div class='hide'>
	<div id='valuationAmountContent' class='details'>
		<div class='contents'>
			<table name='valuationAmountTable' class="table table-bordered blockContainer showInlineTable equalSplit">
				<thead>
					<tr>
						<th class='blockHeader' colspan='2'>Valuation Amounts</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td colspan='2'>
							<button type='button' id='addValAmount'>+</button>
							<button type='button' id='addValAmount2' style='clear:right;float:right'>+</button><br />
						</td>
					</tr>
					<tr>
						<td style='width:5%'>
							&nbsp;
						</td>
						<td style='width:95%;text-align:center'>
							Amount
						</td>
					</tr>
					<tr class='hide defaultValAmount valAmountRow newItemRow'>
						<td class='fieldValue' style='width:5%'>
							<a class='deleteAmountButton'>
								<i title="Delete" class='icon-trash alignMiddle'></i>
							</a>
						</td>
						<td class='fieldValue' style='width:95%;text-align:center'>
							<div class='input-prepend'>
								<span class='add-on'>$</span>
								<input type='number' step='1000' class='input-large' name='valAmount' style='width:85%;text-align:center' value />
							</div>
						</td>
					</tr>

					{foreach item=VAL_AMOUNT key=ROW_NUM from=$VALAMOUNTS}
					<tr class='valAmountRow' id='valAmountRow{$ROW_NUM}'>
						<td class='fieldValue' style='width:5%'>
							<a class='deleteAmountButton'>
								<i title="Delete" class='icon-trash alignMiddle'></i>
							</a>
						</td>
						<td class='fieldValue' style='width:95%;text-align:center'>
							<div class='input-prepend'>
								<span class='add-on'>$</span>
								<input type='number' step='1000' class='input-large' name='valAmount{$ROW_NUM+1}' style='width:85%;text-align:center' value='{$VAL_AMOUNT}' />
							</div>
						</td>
					</tr>
					{/foreach}
				</tbody>
			</table>
		</div>
	</div>
</div>

<div class='hide'>
	<div id='valuationDeductibleContent' class='details'>
		<div class='contents'>
			<table name='valuationDeductibleTable' class="table table-bordered blockContainer showInlineTable equalSplit">
				<thead>
					<tr>
						<th class='blockHeader' colspan='2'>Valuation Deductibles</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td colspan='2'>
							<button type='button' id='addValDeductible'>+</button>
							<button type='button' id='addValDeductible2' style='clear:right;float:right'>+</button><br />
						</td>
					</tr>
					<tr>
						<td style='width:5%'>
							&nbsp;
						</td>
						<td style='width:95%;text-align:center'>
							Deductible
						</td>
					</tr>
					<tr class='hide defaultValDeductible valDeductibleRow newItemRow'>
						<td class='fieldValue' style='width:5%'>
							<a class='deleteDeductibleButton'>
								<i title="Delete" class='icon-trash alignMiddle'></i>
							</a>
						</td>
						<td class='fieldValue' style='width:95%;text-align:center'>
							<div class='input-prepend'>
								<span class='add-on'>$</span>
								<input type='number' step='10' class='input-large' name='valDeductible' style='width:85%;text-align:center' value />
							</div>
						</td>
					</tr>
					{foreach item=VAL_DEDUCTIBLE key=ROW_NUM from=$DEDUCTIBLES}
					<tr class='valDeductibleRow' id='valDeductibleRow{$ROW_NUM}'>
						<td style='width:5%'>
							<a class='deleteDeductibleButton'>
								<i title="Delete" class='icon-trash alignMiddle'></i>
							</a>
						</td>
						<td class='fieldValue' style='width:95%;text-align:center'>
							<div class='input-prepend'>
								<span class='add-on'>$</span>
								<input type='number' step='10' class='input-large' name='valDeductible{$ROW_NUM+1}' style='width:85%;text-align:center' value='{$VAL_DEDUCTIBLE}' />
							</div>
						</td>
					</tr>
					{/foreach}
				</tbody>
			</table>
		</div>
	</div>
</div>
{/strip}
