{strip}
    {if $VALUATIONS}
    <table class="table table-bordered tariff-valuation blockContainer showInlineTable equalSplit">
        <thead>
            <tr>
                <th class="blockHeader" colspan="8">Valuation Options</th>
            </tr>
        </thead>
        <tbody id='valuationTable'>
            <tr>
                <td style='text-align:center; width: 4%;'>&nbsp;</td>
                <td class="blockHeader" style='width: 15%;'>
                    <label class="muted">Valuation Name</label>
                </td>
                <td class="blockHeader" style='width: 15%;'>
                    <label class="muted">Per Pound</label>
                </td>
                <td class="blockHeader" style='width: 15%;'>
                    <label class="muted">Max Valuation</label>
                </td>
                <td class="blockHeader" style='width: 15%;'>
                    <label class="muted">Additional Price Per</label>
                </td>
                <td class="blockHeader" style='width: 15%;'>
                    <label class="muted">Additional Price Per SIT</label>
                </td>
                <td class="blockHeader" style='width: 5%;'>
                    <label class="muted">Free</label>
                </td>
                <td class="blockHeader" style='width: 20%;'>
                    <label class="muted">Free Amount</label>
                </td>
            </tr>
            {assign var=COUNTER value=1}
            {foreach item=valuation key=parent_id from=$VALUATIONS}
                <tr>
                    <td style='vertical-align:middle;text-align:center;width: 4%' class="fieldValue medium narrowWidthType" data-field-type="string">
                        <span class="value" data-field-type="string">{$COUNTER}.</span>
                    </td>

                    <td style="width:15%" class="fieldValue medium narrowWidthType">
                        <span class="value" data-field-type="string">{$valuation.valuation_name}</span>
                    </td>
                    <td style='width: 15%;' class="fieldValue medium narrowWidthType">
                        <span class="value" data-field-type="string">{$valuation.per_pound}</span>
                    </td>
                    <td style="width:15%" class="fieldValue medium narrowWidthType">
                        <span class="value" data-field-type="string">{$valuation.max_amount}</span>
                    </td>
                    <td style='width: 15%;' class="fieldValue medium narrowWidthType">
                        <span class="value" data-field-type="string">{$valuation.additional_price_per}</span>
                    </td>
                    <td style='width: 15%;' class="fieldValue medium narrowWidthType">
                        <span class="value" data-field-type="string">{$valuation.additional_price_per_sit}</span>
                    </td>
                    <td style="width:5%" class="fieldValue medium narrowWidthType">
                        <span class="value" data-field-type="string">{if $valuation.free eq 'y'}Yes{else}No{/if}</span>
                    </td>
                    <td style='width: 20%;' class="fieldValue medium narrowWidthType">
                        <span class="value" data-field-type="string">{$valuation.free_amount}</span>
                    </td>
                </tr>
                {assign var=COUNTER value=$COUNTER+1}
            {/foreach}


        </tbody>
    </table>
    <br><br>
    {/if}
{/strip}