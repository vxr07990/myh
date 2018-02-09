{strip}
	<table class="table table-bordered blockContainer showInlineTable equalSplit" name="EscrowsTable">
		<thead>
		<tr>
			<th class="blockHeader" colspan="4">{vtranslate('Escrows', 'Escrows')}</th>
		</tr>
		</thead>
		<tbody>
		<tr class="fieldLabel">
			<td colspan="4">
				<input type="hidden" name="numMapping" value="{($ITEMCODES_MAPPING_LIST|@count)}"/>
				<button type="button" class="addEscrows" >+</button>
				<button type="button" class="addEscrows" style="clear:right;float:right">+</button>
			</td>
		</tr>
		</tbody>
	</table>
	<div class="EscrowsList" data-rel-module="Escrows">
		{foreach from=$ITEMCODES_MAPPING_LIST item=ITEMCODES_MAPPING_RECORD_MODEL name=related_records_block key = ITEMCODES_MAPPING_ID}
			{include file=vtemplate_path('BlockEditFields.tpl','Escrows') ITEMCODES_MAPPING_ID=$ITEMCODES_MAPPING_RECORD_MODEL->getId() FIELDS_LIST=$ITEMCODES_MAPPING_BLOCK_FIELDS ITEMCODES_MAPPING_RECORD_MODEL=$ITEMCODES_MAPPING_RECORD_MODEL ROWNO=$smarty.foreach.related_records_block.iteration BLOCK_TITLE=$BLOCK_TITLE}
		{/foreach}
	</div>
{/strip}