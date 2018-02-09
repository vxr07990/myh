{strip}
<table class="table table-bordered lineItemTable" id="invoiceLineItemTable">
    <thead>
        <tr>
            <th colspan="7"><span class="inventoryLineItemHeader">{vtranslate('LBL_ITEM_DETAILS', $MODULE)}</span></th>
        </tr>
        <tr>
            <td class="fieldLabel">Item Code</td>
            <td class="fieldLabel">Base Rate</td>
            <td class="fieldLabel">Unit Rate</td>
            <td class="fieldLabel">Quantity</td>
            <td class="fieldLabel">Unit Measurement</td>
            <td class="fieldLabel">Amount</td>
            <td class="fieldLabel">Net Amount</td>
        </tr>
    </thead>
    <tbody>
        {include file="LineItemsContent.tpl"|@vtemplate_path:'Invoice'}
    </tbody>

</table>
<br>
{/strip}