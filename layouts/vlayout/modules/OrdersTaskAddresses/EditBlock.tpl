{strip}
<table class="table table-bordered blockContainer showInlineTable equalSplit" name="OrdersTaskAddressesTable">
    <thead>
    <tr>
        <th class="blockHeader" colspan="4">{vtranslate('LBL_ADDRESS_DETAIL', 'OrdersTaskAddresses')}</th>
    </tr>
    </thead>
    <tbody>
    <tr class="fieldLabel">
        <td colspan="4">
            <input type="hidden" name="numAddress" value="{($ADDRESSES|@count)}"/>
            <button type="button" class="addAddress" >+</button>
            <button type="button" class="addAddress" style="clear:right;float:right">+</button>
        </td>
    </tr>
    </tbody>
</table>
<div class="OrdersTaskAddresses" data-rel-module="OrdersTaskAddresses">
    {foreach from=$ADDRESSES item=ADDRESS_RECORD name=related_records_block key = ADDRESS_ID}
        {include file=vtemplate_path('BlockEditFields.tpl','OrdersTaskAddresses')   ROWNO=$smarty.foreach.related_records_block.iteration MODULE = 'OrdersTaskAddresses' }
    {/foreach}
</div>
<br>
{/strip}