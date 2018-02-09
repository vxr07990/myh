{strip}
<!-- Salesperson.tpl -->
<table class="table table-bordered blockContainer showInlineTable equalSplit">
    <thead>
    <tr>
        <th class="blockHeader" colspan="8">Salesperson</th>
    </tr>
    </thead>
    <tbody id='salespersonTable'>
    <tr>
        <td style='width:100%;text-align:center' colspan="8">
            <button type="button" class="addSalesPerson" style="clear:left; float:left;">+</button>
            <button type="button" class="addSalesPerson" style="clear:right; float:right;">+</button>
        </td>
    </tr>
    <tr>
        <td style='text-align:center; width: 4%;'><input type="hidden" name="numSalesPerson" id="numSalesPersonCount" value="{$SALESPERSON_COUNT|@count}">&nbsp;</td>
        <td class="blockHeader" style='width: 16%;'>{vtranslate("LBL_ACCOUNTS_SALESPERSON", $MODULE)}</td>
        <td class="blockHeader" style='width: 20%;'>{vtranslate("LBL_ACCOUNTS_SALESPERSON_BOOKING", $MODULE)}</td>
        <td class="blockHeader" style='width: 20%;'>{vtranslate("LBL_ACCOUNTS_COMMODITY", $MODULE)}</td>
        <td class="blockHeader" style='width: 5%;'>{vtranslate("LBL_ACCOUNTS_SALESPERSON_SALESCREDIT", $MODULE)}</td>
        <td class="blockHeader" style='width: 7%;'>{vtranslate("LBL_ACCOUNTS_SALESPERSON_COMMISSION", $MODULE)}</td>
        <td class="blockHeader" style='width: 10%;'>{vtranslate("LBL_ACCOUNTS_SALESPERSON_FROMDATE", $MODULE)}</td>
        <td class="blockHeader" style='width: 10%;'>{vtranslate("LBL_ACCOUNTS_SALESPERSON_TODATE", $MODULE)}</td>
    </tr>

    <tr class="defaultSalesPerson hide">
        <td style='vertical-align:middle;text-align:center;width: 4%'>
            <a class="deleteSalesPersonButton">
                <i title="Delete" class="icon-trash alignMiddle"></i>
            </a>
            <input type="hidden" name="salesperson_id[]" value='0'>
        </td>

        <td style="width:16%">
            <select class="salesPersonSelect" name="sales_person[]" style="width: 100%;">
                <option value="">Select an Option</option>
                {foreach item=sales_person key=id from=$SALES_PERSONS}
                <option value="{$id}" class="textShadowNone">
                    {$sales_person}
                </option>
                {/foreach}
            </select>
        </td>
        <td style='width: 20%;'>
            <select class="salesPersonSelect" name="booking_office[]" style="width: 100%;">
                <option value="">Select an Option</option>
                {foreach item=booking_office key=id from=$BOOKING_OFFICES}
                <option value="{$id}" class="textShadowNone">
                    {$booking_office}
                </option>
                {/foreach}
            </select>
        </td>
        <td style='width: 20%;'>
            <select class="salesPersonSelect" name="salesperson_commodity[]" style="width: 100%;">
                <option value="">Select an Option</option>
                {foreach item=business_line key=ROW_NUM from=$BUSINESS_LINES}
                    <option value="{$business_line}">{vtranslate($business_line, $MODULE)}</option>
                {/foreach}
            </select>
        </td>
        <td style='width: 5%;'>
            <input type="number" name="sales_credit[]" min="0">
        </td>
        <td style='width: 5%;'>
            <div class="input-append">
                <input type="number" style="margin:auto;width: 55%;float:left;" name="sales_comm[]" value="{$sales_person_row.sales_comm}" min="0" max="100">
                <span class="add-on">%</span>
            </div>
        </td>

        <td style='width: 10%;'>
            <div class="input-append row-fluid" style="margin:auto;">
                <div class="span12 row-fluid date" style="width:100%">
                    <input style="margin:auto;float:none;width:60%" type="text" class="dateField" data-date-format="{$dateFormat}" name="effective_date_from[]">
                    <span class="add-on" style="float:none;clear:none;display:inline-block;vertical-align:middle;"><i class="icon-calendar"></i></span>
                </div>
            </div>
        </td>

        <td style='width: 10%;'>
            <div class="input-append row-fluid" style="margin:auto;">
                <div class="span12 row-fluid date" style="width:100%">
                    <input style="margin:auto;width:60%;float:none" name="effective_date_to[]" type="text" class="dateField" data-date-format="{$dateFormat}" name="default_rate_date">
                    <span class="add-on" style="float:none;clear:none;display:inline-block;vertical-align:middle;"><i class="icon-calendar"></i></span>
                </div>
            </div>
        </td>


    </tr>

    {foreach item=sales_person_row key=parent_id from=$CURRENT_SALES_PERSONS}
        <tr>
            <td style='vertical-align:middle;text-align:center;width: 4%'>
                <a class="deleteSalesPersonButton">
                    <i title="Delete" class="icon-trash alignMiddle"></i>
                </a>
                <input type="hidden" name="salesperson_id[]" value='{$sales_person_row.id}'>
            </td>

            <td style="width:16%">
                <select class="salesPersonSelect chosen-select chzn-select" name="sales_person[]" style="width: 100%;">
                    <option value="">Select an Option</option>
                    {foreach item=sales_person key=id from=$SALES_PERSONS}
                        <option value="{$id}" {if $sales_person_row.salesperson_id == $id} selected="selected" {/if} required class="textShadowNone">
                            {$sales_person}
                        </option>
                    {/foreach}
                </select>
            </td>
            <td style='width: 20%;'>
                <select class="salesPersonSelect chosen-select chzn-select" name="booking_office[]" style="width: 100%;">
                    <option value="">Select an Option</option>
                    {foreach item=booking_office key=id from=$BOOKING_OFFICES}
                        <option value="{$id}" {if $sales_person_row.booking_office_id == $id} selected="selected" {/if} required class="textShadowNone">
                            {$booking_office}
                        </option>
                    {/foreach}
                </select>
            </td>
            <td style='width: 20%;'>
                <select class="salesPersonSelect chosen-select chzn-select" name="salesperson_commodity[]" style="width: 100%;">
                    <option value="">Select an Option</option>
                    {foreach item=business_line key=ROW_NUM from=$BUSINESS_LINES}
                        <option {if $sales_person_row.commodity eq {vtranslate($business_line, $MODULE)}} selected="selected" {/if}>{vtranslate($business_line, $MODULE)}</option>
                    {/foreach}
                </select>
            </td>
            <td style='width: 5%;'>
                <input type="number" name="sales_credit[]" value="{$sales_person_row.sales_credit}" min="0">
            </td>
            <td style='width: 5%;'>
                <div class="input-append">
                    <input type="number" style="margin:auto;width: 55%;float:left;" name="sales_comm[]" value="{$sales_person_row.sales_comm}" required min="0" max="100">
                    <span class="add-on">%</span>
                </div>

            </td>

            <td style='width: 10%;'>
                <div class="input-append row-fluid" style="margin:auto;">
                    <div class="span12 row-fluid date" style="width:100%">
                        <input style="margin:auto;float:none;width: 60%" type="text" class="dateField" data-date-format="{$dateFormat}" required name="effective_date_from[]" value="{$sales_person_row.effective_date_from}">
                        <span class="add-on" style="float:none;clear:none;display:inline-block;vertical-align:middle;"><i class="icon-calendar"></i></span>
                    </div>
                </div>
            </td>

            <td style='width: 10%;'>
                <div class="input-append row-fluid" style="margin:auto;">
                    <div class="span12 row-fluid date" style="width:100%">
                        <input style="margin:auto;width:60%;float:none" name="effective_date_to[]"  type="text" class="dateField" required data-date-format="{$dateFormat}" name="default_rate_date" value="{$sales_person_row.effective_date_to}">
                        <span class="add-on" style="float:none;clear:none;display:inline-block;vertical-align:middle;"><i class="icon-calendar"></i></span>
                    </div>
                </div>
            </td>


        </tr>
    {/foreach}
    </tbody>
</table>
<br><br>
{/strip}
