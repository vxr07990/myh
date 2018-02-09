{strip}
	<table name='RevenueGroupingItemTable' class='table table-bordered blockContainer showInlineTable'>
		<thead>
			<tr>
				<th class='blockHeader' colspan='9'>{vtranslate('RevenueGroupingItem', 'RevenueGroupingItem')}</th>
			</tr>
		</thead>
		<tbody>
			<tr class="fieldLabel">
				<td colspan="9">
					<button type="button" class="addRevenueGroupingItem">+</button>
					<input type="hidden" name="numAgents" value="{($REVENUEGROUPINGITEM_LIST|@count)}"/>
					<button type="button" class="addRevenueGroupingItem" style="clear:right;float:right">+</button>
				</td>
			</tr>
			{assign var=REVENUEGROUP_MODEL value=$REVENUEGROUPINGITEM_MODULE_MODEL->getField("revenuegroup")}
			{assign var=INVOICESEQUENCE_MODEL value=$REVENUEGROUPINGITEM_MODULE_MODEL->getField("invoicesequence")}

			<tr style="margin:auto"class="defaultRevenueGroupingItem revenuegroupingitemRow hide">
				<td class="fieldValue" style="margin:auto;min-width: 12px;">
					<i title="Delete" class="icon-trash removeRevenueGroupingItem"></i>
					<input type="hidden" class="default" name="revenuegroupingitemId" value="none" />
				</td>
				<td class="fieldLabel">
					<label class="muted">{if $REVENUEGROUP_MODEL->isMandatory() eq true} <span class="redColor">*</span> {/if}{vtranslate($REVENUEGROUP_MODEL->get('label'),'RevenueGroupingItem')}</label>
				</td>
				<td class="fieldValue" style="margin:auto">
					<div class="row-fluid">
						<span class="span10">
							{assign var=REVENUEGROUP_MODEL value=$REVENUEGROUP_MODEL->set('readonly',1)}
							{include file=vtemplate_path($REVENUEGROUP_MODEL->getUITypeModel()->getTemplateName(),'RevenueGroupingItem') FIELD_MODEL=$REVENUEGROUP_MODEL BLOCK_FIELDS=$REVENUEGROUPINGITEM_BLOCK_FIELDS DEFAULT_CHZN=1}
						</span>
					</div>
				</td>
				<td class="fieldLabel">
					<label class="muted">{if $INVOICESEQUENCE_MODEL->isMandatory() eq true} <span class="redColor">*</span> {/if}{vtranslate($INVOICESEQUENCE_MODEL->get('label'),'RevenueGroupingItem')}</label>
				</td>
				<td class="fieldValue" style="margin:auto">
					<div class="row-fluid">
						<span class="span10">
							{include file=vtemplate_path($INVOICESEQUENCE_MODEL->getUITypeModel()->getTemplateName(),'RevenueGroupingItem') FIELD_MODEL=$INVOICESEQUENCE_MODEL BLOCK_FIELDS=$REVENUEGROUPINGITEM_BLOCK_FIELDS DEFAULT_CHZN=1}
						</span>
					</div>
				</td>

				</tr>
				{foreach key=ROW_NUM item=REVENUEGROUPINGITEM from=$REVENUEGROUPINGITEM_LIST}
					<tr style="margin:auto" class="revenuegroupingitemRow{$ROW_NUM+1} revenuegroupingitemRow">
						<td class="fieldValue" style="margin:auto;min-width: 12px;">
							<input type="hidden" name="revenuegroupingitemId" value="{$REVENUEGROUPINGITEM['revenuegroupingitemid']}" />
							<input type="hidden" class="default" name="revenuegroupingitemDelete" value="" />
							<input type="hidden" class="row_num" name="row_num" value="{$ROW_NUM+1}" />
							{*{if $ROW_NUM >1}*}
								<i title="Delete" class="icon-trash removeRevenueGroupingItem"></i>
							{*{/if}*}

						</td>
						<td class="fieldLabel">

							<label class="muted">{if $REVENUEGROUP_MODEL->isMandatory() eq true} <span class="redColor">*</span> {/if}{vtranslate($REVENUEGROUP_MODEL->get('label'),'RevenueGroupingItem')}</label>
						</td>
						<td class="fieldValue typeCell" style="margin:auto">
							<div class="row-fluid">
								{assign var=REVENUEGROUP_MODEL value=$REVENUEGROUP_MODEL->set('fieldvalue',$REVENUEGROUPINGITEM['revenuegroup'])}
								{*{if $ROW_NUM <= 1}*}
									{*{assign var=REVENUEGROUP_MODEL value=$REVENUEGROUP_MODEL->set('readonly',0)}*}
								{*{else}*}
									{assign var=REVENUEGROUP_MODEL value=$REVENUEGROUP_MODEL->set('readonly',1)}
								{*{/if}*}
								<span class="span10">
									{include file=vtemplate_path($REVENUEGROUP_MODEL->getUITypeModel()->getTemplateName(),'RevenueGroupingItem') FIELD_MODEL=$REVENUEGROUP_MODEL BLOCK_FIELDS=$REVENUEGROUPINGITEM_BLOCK_FIELDS}
								</span>
							</div>
						</td>
						<td class="fieldLabel">

							<label class="muted">{if $INVOICESEQUENCE_MODEL->isMandatory() eq true} <span class="redColor">*</span> {/if}{vtranslate($INVOICESEQUENCE_MODEL->get('label'),'RevenueGroupingItem')}</label>
						</td>
						<td class="fieldValue typeCell" style="margin:auto">
							<div class="row-fluid">
								{assign var=INVOICESEQUENCE_MODEL value=$INVOICESEQUENCE_MODEL->set('fieldvalue',$REVENUEGROUPINGITEM['invoicesequence'])}
								<span class="span10">
									{include file=vtemplate_path($INVOICESEQUENCE_MODEL->getUITypeModel()->getTemplateName(),'RevenueGroupingItem') FIELD_MODEL=$INVOICESEQUENCE_MODEL BLOCK_FIELDS=$REVENUEGROUPINGITEM_BLOCK_FIELDS}
								</span>
							</div>
						</td>
					</tr>
				{/foreach}
				{if $REVENUEGROUPINGITEM_LIST|@count lte 0}
					{foreach  key = ROW_NUM item=ITEMVALUE from=$REVENUEGROUPINGITEM_LIST_DEFAULT}
						<tr style="margin:auto" class="revenuegroupingitemRow{$ROW_NUM+1} revenuegroupingitemRow">
							<td class="fieldValue" style="margin:auto;min-width: 12px;">
								<input type="hidden" name="revenuegroupingitemId" value="none" />
								<input type="hidden" class="row_num" name="row_num" value="{$ROW_NUM+1}" />
								{*{if $ROW_NUM >1}*}
									<i title="Delete" class="icon-trash removeRevenueGroupingItem"></i>
								{*{/if}*}
							</td>
							<td class="fieldLabel">
								<label class="muted">{if $REVENUEGROUP_MODEL->isMandatory() eq true} <span class="redColor">*</span> {/if}{vtranslate($REVENUEGROUP_MODEL->get('label'),'RevenueGroupingItem')}</label>
							</td>
							<td class="fieldValue typeCell" style="margin:auto">
								<div class="row-fluid">
									{assign var=REVENUEGROUP_MODEL value=$REVENUEGROUP_MODEL->set('fieldvalue',$ITEMVALUE)}
									{*{if $ROW_NUM <= 1}*}
										{*{assign var=REVENUEGROUP_MODEL value=$REVENUEGROUP_MODEL->set('readonly',0)}*}
									{*{else}*}
										{assign var=REVENUEGROUP_MODEL value=$REVENUEGROUP_MODEL->set('readonly',1)}
									{*{/if}*}
									<span class="span10">
									{include file=vtemplate_path($REVENUEGROUP_MODEL->getUITypeModel()->getTemplateName(),'RevenueGroupingItem') FIELD_MODEL=$REVENUEGROUP_MODEL BLOCK_FIELDS=$REVENUEGROUPINGITEM_BLOCK_FIELDS}
								</span>
								</div>
							</td>
							<td class="fieldLabel">
								<label class="muted">{if $INVOICESEQUENCE_MODEL->isMandatory() eq true} <span class="redColor">*</span> {/if}{vtranslate($INVOICESEQUENCE_MODEL->get('label'),'RevenueGroupingItem')}</label>
							</td>
							<td class="fieldValue typeCell" style="margin:auto">
								<div class="row-fluid">
									<span class="span10">
									{assign var=INVOICESEQUENCE_MODEL value=$INVOICESEQUENCE_MODEL->set('fieldvalue',$ROW_NUM + 1)}
									{include file=vtemplate_path($INVOICESEQUENCE_MODEL->getUITypeModel()->getTemplateName(),'RevenueGroupingItem') FIELD_MODEL=$INVOICESEQUENCE_MODEL BLOCK_FIELDS=$REVENUEGROUPINGITEM_BLOCK_FIELDS}
								</span>
								</div>
							</td>
						</tr>
					{/foreach}
				{/if}
		</tbody>
	</table>
	<br>
{/strip}
