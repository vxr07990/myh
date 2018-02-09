{strip}
<div class="detailViewInfo">
	<table class="table table-bordered equalSplit detailview-table" style="table-layout:fixed">
		<tr>
			<td class="fieldLabel narrowWidthType" nowrap>
				<label class="muted">{vtranslate('Estimated Number',$MODULE)}</label>
			</td>
			<td class="fieldLabel narrowWidthType" nowrap>
				<label class="muted">{vtranslate($customType,$MODULE)}</label>
			</td>
		</tr>
		{foreach item=ELEMENT from=$CUSTOM_TOOLTIPS name=fieldsCount}
			{if $smarty.foreach.fieldsCount.index < 7}
				<tr>
					<td class="fieldValue narrowWidthType">
						<span class="value">
							{$ELEMENT.estimatedNumber}
						</span>
					</td>
					<td class="fieldValue narrowWidthType">
						<span class="value">
							{$ELEMENT.type}
						</span>
					</td>
				</tr>
			{/if}
		{foreachelse}
			<tr>
				<td class="fieldValue narrowWidthType" colspan='2'>
					No data to show.
				</td>
			</tr>
		{/foreach}
	</table>
</div>
{/strip}
