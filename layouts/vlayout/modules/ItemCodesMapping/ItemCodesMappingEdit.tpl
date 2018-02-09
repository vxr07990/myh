{strip}
<table class="table table-bordered blockContainer showInlineTable equalSplit" name="ItemCodesMappingTable">
    <thead>
    <tr>
        <th class="blockHeader" colspan="4">{vtranslate('LBL_ITEMCODES_MAPPING', 'ItemCodesMapping')}</th>
    </tr>
    </thead>
    <tbody>
    <tr class="fieldLabel">
        <td colspan="4">
            <input type="hidden" name="numMapping" value="{($ITEMCODES_MAPPING_LIST|@count)}"/>
            <button type="button" class="addItemCodesMapping" >+</button>
            <button type="button" class="addItemCodesMapping" style="clear:right;float:right">+</button>
        </td>
    </tr>
    </tbody>
</table>
<div class="ItemCodesMappingList" data-rel-module="ItemCodesMapping">
    {foreach from=$ITEMCODES_MAPPING_LIST item=ITEMCODES_MAPPING_RECORD_MODEL name=related_records_block key = ITEMCODES_MAPPING_ID}
        {include file=vtemplate_path('BlockEditFields.tpl','ItemCodesMapping') ITEMCODES_MAPPING_ID=$ITEMCODES_MAPPING_ID FIELDS_LIST=$ITEMCODES_MAPPING_BLOCK_FIELDS ITEMCODES_MAPPING_RECORD_MODEL=$ITEMCODES_MAPPING_RECORD_MODEL ROWNO=$smarty.foreach.related_records_block.iteration BLOCK_TITLE=$BLOCK_TITLE}
    {/foreach}
</div>
{/strip}