{strip}

    <!-- EditBillingAddressesTable.tpl -->
    <table class="table table-bordered blockContainer showInlineTable">
        <thead>
        <tr>
            <th class="blockHeader" colspan="11">{vtranslate("LBL_ACCOUNTS_BILLING_ADDRESS", $MODULE)}</th>
        </tr>
        </thead>
        <tbody id='billingAddressesTable'>
        <!-- Adding Buttons -->
        <tr>
            <td style='width:100%;text-align:center' colspan="11">
                <button type="button" class="marginLeft10px addBillingAddress" style="clear:left; float:left;">+
                </button>
                <button type="button" class="marginRight10px addBillingAddress" style="clear:right; float:right;">+
                </button>
                &nbsp
            </td>
        </tr>


        <tr>
            <td style="width: 3%;"></td>
            <td class="blockHeader" style="width: 13%;"><span class="redColor">*</span> {vtranslate("LBL_ACCOUNTS_BUSINESSLINE", $MODULE)}</td>
            <td class="blockHeader" style="width: 11%;"><span class="redColor">*</span> {vtranslate("LBL_ACCOUNTS_ADDRESS_DESC", $MODULE)}</td>
            <td class="blockHeader" style="width: 11%;"><span class="redColor">*</span> {vtranslate("LBL_ACCOUNTS_COMPANY_NAME", $MODULE)}</td>
            <td class="blockHeader" style="width: 11%;"><span class="redColor">*</span> {vtranslate("LBL_ACCOUNTS_ADDRESS1", $MODULE)}</td>
            <td class="blockHeader" style="width: 11%;">{vtranslate("LBL_ACCOUNTS_ADDRESS2", $MODULE)}</td>
            <td class="blockHeader" style="width: 11%;"><span class="redColor">*</span> {vtranslate("LBL_ACCOUNTS_CITY", $MODULE)}</td>
            <td class="blockHeader" style="width: 8%;"><span class="redColor">*</span> {vtranslate("LBL_ACCOUNTS_STATE", $MODULE)}</td>
            <td class="blockHeader" style="width: 8%;"><span class="redColor">*</span> {vtranslate("LBL_ACCOUNTS_ZIP", $MODULE)}</td>
            <td class="blockHeader" style="width: 11%;"><span class="redColor">*</span> {vtranslate("LBL_ACCOUNTS_COUNTRY", $MODULE)}</td>
            <td class="blockHeader" style="width: 3%; text-align:center">{vtranslate("LBL_ACCOUNTS_ACTIVE_INACTIVE", $MODULE)}</td>
        </tr>

        <tr class="defaultBillingAddress hide" style="text-align:center">
            <td style="width: 3%; text-align: center">
                <i title="Delete" class="icon-trash deleteBillingAddress alignMiddle"></i>
                <input type="hidden" name="billing_id[]">
            </td>

            <td>
                <div class="row-fluid">
                    <span class="span12">
                        <select class="multipicklistall" multiple name="commodity[][]" style="width:95%">
                            <option value="All">{vtranslate('LBL_ALL')}</option>
                            {foreach item=PICKLIST_VALUE key=PICKLIST_NAME  from=$BUSINESS_LINES}
                                <option value="{Vtiger_Util_Helper::toSafeHTML($PICKLIST_VALUE)}">{vtranslate($PICKLIST_VALUE, $MODULE)}</option>
                            {/foreach}
                        </select>
                    </span>
                </div>
            </td>
            <td>
                <div class="row-fluid">
                    <span class="span12">
                        <input type="text" name="billing_address_desc[]" class="input-large" style="width: 90%;">
                    </span>
                </div>
            </td>
            <td>
                <div class="row-fluid">
                    <span class="span12">
                        <input type="text" name="billing_company_name[]" class="input-large" style="width: 90%;">
                    </span>
                </div>
            </td>
            <td>
                <div class="row-fluid">
                    <span class="span12">
                        <input type="text" name="billing_address1[]" class="input-large" style="width: 90%;">
                    </span>
                </div>
            </td>
            <td>
                <div class="row-fluid">
                    <span class="span12">
                        <input type="text" name="billing_address2[]" class="input-large" style="width: 90%;">
                    </span>
                </div>
            </td>
            <td>
                <div class="row-fluid">
                    <span class="span12">
                        <input type="text" name="billing_city[]" class="input-large" style="width: 90%;">
                    </span>
                </div>
            </td>
            <td>
                <div class="row-fluid">
                    <span class="span12">
                        <input type="text" style="width: 90%;" name="billing_state[]" class="input-large">
                    </span>
                </div>
            </td>
            <td>
                <div class="row-fluid">
                    <span class="span12">
                        <input type="text" style="width: 90%;" name="billing_zip[]" class="input-large">
                    </span>
                </div>
            </td>
            <td>
                <div class="row-fluid">
                    <span class="span12">
                        <input type="text" name="billing_country[]" style="width: 90%" class="input-large">
                    </span>
                </div>
            </td>
            <td style="text-align: center">
                <div class="row-fluid">
                    <span class="span12">
                        <input type="checkbox" value="yes" checked name="billing_active[]">
                    </span>
                </div>
            </td>
        </tr>


        <!-- DEFAULT TABLE ENDS -->
        {assign var=COUNT value=0}
        {foreach item=billing_row key=ROW_NUM from=$BILLING_ADDRESSES}
            {assign var=COUNT value=$COUNT+1}
            <tr>
                <td style="width: 72px; text-align: center">
                    <i title="Delete" class="icon-trash deleteBillingAddress" style="margin: 5px 10px;"></i>
                    <input type="hidden" name="billing_id[]" value="{$billing_row.id}">
                </td>

                <td>
                    <div class="row-fluid">
                    <span class="span10">
                        {* commodity field holds business lines *}
                        <select class="select2 multipicklistall" multiple name="commodity[{$COUNT}][]" required style="max-width: 150px;">
                            {assign var="FIELD_VALUE_LIST" value=$billing_row.commodity}
                            {if $FIELD_VALUE_LIST|count eq $BUSINESS_LINES|count}
                                <option value="All" selected>{vtranslate('LBL_ALL')}</option>
                                {foreach item=business_line key=NUM  from=$BUSINESS_LINES}
                                    <option value="{$business_line}">{vtranslate($business_line, $MODULE)}</option>
                                {/foreach}
                            {else}
                                <option value="All">{vtranslate('LBL_ALL')}</option>
                                {foreach item=business_line key=NUM from=$BUSINESS_LINES}
                                    <option {if in_array(vtranslate($business_line, $MODULE), $FIELD_VALUE_LIST)}selected {/if} value="{$business_line}">{vtranslate($business_line, $MODULE)}</option>
                                {/foreach}
                            {/if}
                        </select>
                    </span>
                    </div>
                </td>
                <td>
                    <div class="row-fluid">
                    <span class="span10">
                        <input type="text" name="billing_address_desc[]" required value="{$billing_row.address_desc}" class="input-large" style="max-width: 170px;">
                    </span>
                    </div>
                </td>
                <td>
                    <div class="row-fluid">
                    <span class="span10">
                        <input type="text" name="billing_company_name[]" required value="{$billing_row.company}" class="input-large" style="max-width: 170px;">
                    </span>
                    </div>
                </td>
                <td>
                    <div class="row-fluid">
                    <span class="span10">
                        <input type="text" name="billing_address1[]" id="billing_address1{$COUNT}" required value="{$billing_row.address1}" class="input-large" style="max-width: 170px;">
                    </span>
                    </div>
                </td>
                <td>
                    <div class="row-fluid">
                    <span class="span10">
                        <input type="text" name="billing_address2[]" id="billing_address2{$COUNT}" value="{$billing_row.address2}" class="input-large" style="max-width: 170px;">
                    </span>
                    </div>
                </td>
                <td>
                    <div class="row-fluid">
                    <span class="span10">
                        <input type="text" name="billing_city[]" required id="billing_city{$COUNT}" value="{$billing_row.city}" class="input-large" style="max-width: 170px;">
                    </span>
                    </div>
                </td>
                <td>
                    <div class="row-fluid">
                    <span class="span10">
                        <input type="text" style="width: 70px;" required id="billing_state{$COUNT}" value="{$billing_row.state}" name="billing_state[]" class="input-large">
                    </span>
                    </div>
                </td>
                <td>
                    <div class="row-fluid">
                    <span class="span10">
                        <input type="text" style="width: 70px;" required id="billing_zip{$COUNT}" value="{$billing_row.zip}" name="billing_zip[]" class="input-large">
                    </span>
                    </div>
                </td>
                <td>
                    <div class="row-fluid">
                    <span class="span10">
                        <input type="text" name="billing_country[]" id="billing_country{$COUNT}" required value="{$billing_row.country}" style="max-width: 170px;" class="input-large">
                    </span>
                    </div>
                </td>
                <td style="text-align: center">
                    <div class="row-fluid">
                    <span class="span10">
                        <input type="checkbox" value="yes" {if $billing_row.active == 'yes'}checked{/if} name="billing_active[]" class="input-large">
                    </span>
                    </div>
                </td>
            </tr>
        {/foreach}
            <input type="hidden" name="billingAddressCount" id="billingAddressCount" value="{$COUNT}">



        </tbody>
    </table>
    <br/>
{/strip}

