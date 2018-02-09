{strip}
	<!-- AnnualRateTable.tpl -->
	<table class="table table-bordered equalSplit detailview-table ">
		<thead>
				<th class="blockHeader" colspan="8">
					<img class="cursorPointer alignMiddle blockToggle {if !($IS_HIDDEN)} hide {/if} "  src="{vimage_path('arrowRight.png')}" data-mode="hide">
					<img class="cursorPointer alignMiddle blockToggle {if ($IS_HIDDEN)} hide {/if}"  src="{vimage_path('arrowDown.png')}" data-mode="show">
					&nbsp;&nbsp;{vtranslate({$BLOCK_LABEL_KEY},{$MODULE_NAME})}
				</th>
		</thead>
		<tbody id='annualRateIncreaseTable'>
			<tr>
				<td class="blockHeader" style='width:50%;text-align:center;'>{vtranslate("LBL_ACCOUNTS_FROMDATE", $MODULE)}</td>
				<td class="blockHeader" style='width:50%;text-align:center;'>{vtranslate("LBL_ACCOUNTS_PERCINCREASE", $MODULE)}</td>
			</tr>
		{foreach key=ROW_NUM item=ANNUAL_RATE from=$ANNUAL_RATES}
			<tr class="annualRate" id="annualRateRow{$ROW_NUM+1}">
				<td class="{$WIDTHTYPE}" style='width:50%;text-align:center'>
					<span class="value">
						{$ANNUAL_RATE['date']}
					</span>
				</td>
				<td class="{$WIDTHTYPE}" style='width:50%;text-align:center'>
					<span class="value">
						{$ANNUAL_RATE['rate']}
					</span>
				</td>
			</tr>
		{/foreach}
		</tbody>
	</table>
	<br />
	<div class='hide'>
		<link rel="stylesheet" href="libraries/jquery/colorbox/example1/colorbox.css" />
		<script type='text/javascript' src='libraries/jquery/colorbox/jquery.colorbox-min.js'></script>
	</div>
{/strip}