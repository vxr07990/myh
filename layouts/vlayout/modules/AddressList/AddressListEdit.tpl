{strip}
<table class="table table-bordered blockContainer showInlineTable equalSplit" name="AddressListTable">
    <thead>
    <tr>
        <th class="blockHeader" colspan="4">{vtranslate('LBL_ADDRESSES', 'AddressList')}</th>
    </tr>
    </thead>
    <tbody>
    <tr class="fieldLabel">
        <td colspan="4">
            <input type="hidden" name="numAddress" value="{($ADDRESSESLIST|@count)}"/>
            <button type="button" class="addAddress" >+</button>
            <button type="button" class="addAddress" style="clear:right;float:right">+</button>
        </td>
    </tr>
    </tbody>
</table>
<div class="AddressList" data-rel-module="AddressList">
    {foreach from=$ADDRESSESLIST item=ADDRESS_RECORD name=related_records_block key = ADDRESSLIST_ID}
        {include file=vtemplate_path('BlockEditFields.tpl','AddressList')   ROWNO=$smarty.foreach.related_records_block.iteration MODULE = 'AddressList' }
    {/foreach}
</div>
{/strip}