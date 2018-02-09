{strip}
	<table name='RevenueGroupingItemTable' class='table table-bordered blockContainer showInlineTable'>
		<thead>
		<tr>
			<th class='blockHeader' colspan='9'>{vtranslate('RevenueGroupingItem', 'RevenueGroupingItem')}</th>
		</tr>
		</thead>
		<tbody>
		{assign var=REVENUEGROUP_MODEL value=$REVENUEGROUPINGITEM_MODULE_MODEL->getField("revenuegroup")}
		{assign var=INVOICESEQUENCE_MODEL value=$REVENUEGROUPINGITEM_MODULE_MODEL->getField("invoicesequence")}
		{foreach key=ROW_NUM item=REVENUEGROUPINGITEM from=$REVENUEGROUPINGITEM_LIST}
			<tr style="margin:auto" class="revenuegroupingitemRow{$ROW_NUM+1} revenuegroupingitemRow">
				<td class="fieldLabel medium">
					<label class="muted">{vtranslate($REVENUEGROUP_MODEL->get('label'),'RevenueGroupingItem')}</label>
				</td>
				<td class="fieldValue typeCell" style="text-align:center;margin:auto">
					<div class="row-fluid">
						{assign var=REVENUEGROUP_MODEL value=$REVENUEGROUP_MODEL->set('fieldvalue',$REVENUEGROUPINGITEM['revenuegroup'])}
						<span class="span10">
							{$REVENUEGROUP_MODEL->getDisplayValue($REVENUEGROUP_MODEL->get('fieldvalue'))}
						</span>
					</div>
				</td>
				<td class="fieldLabel medium">
					<label class="muted">{vtranslate($INVOICESEQUENCE_MODEL->get('label'),'RevenueGroupingItem')}</label>
				</td>
                <td class="fieldValue typeCell" style="text-align:center;margin:auto">
					<div class="row-fluid">
						{assign var=INVOICESEQUENCE_MODEL value=$INVOICESEQUENCE_MODEL->set('fieldvalue',$REVENUEGROUPINGITEM['invoicesequence'])}
						<span class="span10">
							{$INVOICESEQUENCE_MODEL->getDisplayValue($INVOICESEQUENCE_MODEL->get('fieldvalue'))}
						</span>
					</div>
				</td>

			</tr>
		{/foreach}
		</tbody>
	</table>
	<br>
{/strip}