{strip}
    <table class="table table-bordered tariff-valuation blockContainer showInlineTable equalSplit">
        <thead>
            <tr>
                <th class="blockHeader" colspan="8">Valuation Options</th>
            </tr>
        </thead>
        <tbody id='valuationTable'>
            <tr>
                <td style='width:100%;text-align:center' colspan="8">
                    <button type="button" class="addValuationRow" style="clear:left; float:left;">+</button>
                    <button type="button" class="addValuationRow" style="clear:right; float:right;">+</button>
                </td>
            </tr>
            <tr>
                <td style='text-align:center; width: 4%;'><input type="hidden" name="valuation_count" id="valuation-count" value="{$VALUATION_COUNT|@count}">&nbsp;</td>
                <td class="blockHeader" style='width: 15%;'>Valuation Name</td>
                <td class="blockHeader" style='width: 15%;'>Per Pound</td>
                <td class="blockHeader" style='width: 15%;'>Max Valuation</td>
                <td class="blockHeader" style='width: 15%;'>Additional Price Per</td>
                <td class="blockHeader" style='width: 15%;'>Additional Price Per SIT</td>
                <td class="blockHeader" style='width: 15%; text-align: center;'>Free</td>
                <td class="blockHeader" style='width: 15%;'>Free Amount</td>
            </tr>

            <tr class="defaultValuationRow hide">
                <td style='vertical-align:middle;text-align:center;width: 4%'>
                    <a class="deleteValuationBtn">
                        <i title="Delete" class="icon-trash alignMiddle"></i>
                    </a>
                    <input type="hidden" name="id[]" value='0'>
                </td>

                <td style="width:15%">
                    <input type="text" name="valuation_name[]" class="input-large" maxlength="30">
                </td>
                <td style='width: 15%;'>
                    <input type="text" name="per_pound[]" class="input-large" maxlength="10">
                </td>
                <td style='width: 15%;'>
                    <input type="text" name="max_amount[]" class="input-large" maxlength="10">
                </td>
                <td style='width: 15%;'>
                    <input type="text" name="additional_price_per[]" class="input-large" maxlength="10">
                </td>
                <td style='width: 15%;'>
                    <input type="text" name="additional_price_per_sit[]" class="input-large" maxlength="10">
                </td>
                <td style='width: 15%; text-align: center;'>
                    <input type="checkbox" class="input-large free-checkbox">
                    <input type="hidden" name="free[]" value="n">
                </td>
                <td style='width: 15%;'>
                    <input type="text" name="free_amount[]" class="input-large" maxlength="10">
                </td>
            </tr>

            {foreach item=valuation key=parent_id from=$VALUATIONS}
                <tr>
                    <td style='vertical-align:middle;text-align:center;width: 4%'>
                        <a class="deleteValuationBtn">
                            <i title="Delete" class="icon-trash alignMiddle"></i>
                        </a>
                        <input type="hidden" name="id[]" value='{$valuation.id}'>
                    </td>

                    <td style="width:15%">
                        <input type="text" name="valuation_name[]" value="{$valuation.valuation_name}" class="input-large" required maxlength="30">
                    </td>
                    <td style='width: 15%;'>
                        <input type="number" name="per_pound[]" value="{$valuation.per_pound}" class="input-large" required maxlength="10">
                    </td>
                    <td style='width: 15%;'>
                        <input type="number" name="max_amount[]" value="{$valuation.max_amount}" class="input-large" maxlength="10">
                    </td>
                    <td style='width: 15%;'>
                        <input type="number" name="additional_price_per[]" value="{$valuation.additional_price_per}" class="input-large" maxlength="10">
                    </td>
                    <td style='width: 15%;'>
                        <input type="number" name="additional_price_per_sit[]" value="{$valuation.additional_price_per_sit}" class="input-large" maxlength="10">
                    </td>
                    <td style='width: 15%;text-align: center'>
                        <input type="checkbox" {if $valuation.free eq 'y'}checked{/if} class="input-large free-checkbox" value="y">
                        <input type="hidden" name="free[]" value="{if $valuation.free eq 'y'}y{else}n{/if}">
                    </td>
                    <td style='width: 15%;'>
                        <input type="number" name="free_amount[]" class="input-large" value="{$valuation.free_amount}" maxlength="10">
                    </td>
                </tr>
            {/foreach}
        </tbody>
    </table>
    <br><br>
{/strip}