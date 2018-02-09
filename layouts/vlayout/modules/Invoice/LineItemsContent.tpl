{strip}
{if !empty($BLOCK_DATA.items)}
    <tbody>
    {foreach item = ITEM key=ROW_NO from=$BLOCK_DATA.items}
        <tr>
            <td class="fieldValue">
                <table class="table inline-table">
                    <tr>
                        <td class="fieldValue"><span>{$ITEM.item_code}</span></td>
                    </tr>
                    <tr>
                        <td class="fieldValue"><span>{$ITEM.item_code_description}</span></td>
                    </tr>
                </table>
            </td>
            <td class="fieldValue"><span>{$ITEM.base_rate}</span></td>
            <td class="fieldValue"><span>{$ITEM.unit_rate}</span></td>
            <td class="fieldValue"><span>{$ITEM.quantity}</span></td>
            <td class="fieldValue"><span>{$ITEM.unit_measurement}</span></td>
            <td class="fieldValue">
                <table class="table inline-table">
                    <tr>
                        <td colspan="2" class="fieldValue"><span>{$ITEM.amount}</span></td>
                    </tr>
                    <tr>
                        <td>Discount:</td>
                        <td class="fieldValue"><span>{$ITEM.discount}</span></td>
                    </tr>
                    <tr>
                        <td>Total After Discount:</td>
                        <td class="fieldValue"><span>{$ITEM.total_after_discount}</span></td>
                    </tr>
                </table>
            </td>
            <td class="fieldValue">
                <span>{$ITEM.net_amount}</span>
            </td>
        </tr>
    {/foreach}
    </tbody>

    <tfoot>
        <tr>
            <td class="totalLabel" colspan="6"><strong>Items Total</strong></td>
            <td class="totalValue" >
                <span>{$BLOCK_DATA.subtotal}</span>
                <input type="hidden" name="hdnSubTotal" value="{$BLOCK_DATA.subtotal}">
            </td>
        </tr>
        <tr>
            <td class="totalLabel" colspan="6"><strong>Discount</strong></td>
            <td class="totalValue" colspan="2">
                <span>{$BLOCK_DATA.total_discount}</span>
                <input type="hidden" name="hdnDiscountAmount" value="{$BLOCK_DATA.total_discount}">
            </td>
        </tr>
        <tr>
            <td class="totalLabel" colspan="6"><strong>Grand Total</strong></td>
            <td class="totalValue" colspan="2">
                <span>{$BLOCK_DATA.total}</span>
                <input type="hidden" name="hdnGrandTotal" value="{$BLOCK_DATA.total}">
            </td>
        </tr>
        <tr>
            <td class="totalLabel" colspan="6"><strong>Received</strong></td>
            <td class="totalValue" colspan="2">
                <span>{$BLOCK_DATA.received}</span>
                <input type="hidden" name="received" value="{$BLOCK_DATA.received}">
            </td>
        </tr>
        <tr>
            <td class="totalLabel" colspan="6"><strong>Balance</strong></td>
            <td class="totalValue" colspan="2">
                <span>{$BLOCK_DATA.balance}</span>
                <input type="hidden" name="balance" value="{$BLOCK_DATA.balance}">
            </td>
        </tr>
    </tfoot>
{/if}
{/strip}