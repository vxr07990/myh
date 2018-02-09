{strip}
    <!-- EditinvoiceSettingesTable.tpl -->
    <table class="table table-bordered blockContainer showInlineTable">
        <thead>
        <tr>
            <th class="blockHeader" colspan="8">{vtranslate("LBL_ACCOUNT_INVOICE_SETTINGS", $MODULE)}</th>
        </tr>
        </thead>
        <tbody id='InvoiceSettingsTable'>
        <!-- Adding Buttons -->
        <tr>
            <td style='width:100%;text-align:center' colspan="8">
                <button type="button" class="marginLeft10px addInvoiceSetting" style="clear:left; float:left;">+
                </button>
                <button type="button" class="marginRight10px addInvoiceSetting" style="clear:right; float:right;">+
                </button>
                <input type="hidden" name="invoiceSettingCount" id="invoiceSettingCount" value="0">&nbsp
            </td>
        </tr>

        <tr>
            <td style="width: 2%;"></td>
            <td class="blockHeader" style="width: 14%;"><span class="redColor">*</span> {vtranslate("LBL_ACCOUNTS_COMMODITY", $MODULE)}</td>
            <td class="blockHeader" style="width: 14%;"><span class="redColor">*</span> {vtranslate("LBL_ACCOUNTS_INVOICE_TEMPLATE", $MODULE)}</td>
            <td class="blockHeader" style="width: 14%;"><span class="redColor">*</span> {vtranslate("LBL_ACCOUNTS_INVOICE_PACKET", $MODULE)}</td>
            <td class="blockHeader" style="width: 14%;"><span class="redColor">*</span> {vtranslate("LBL_ACCOUNTS_INVOICE_DOCUMENT_FORMAT", $MODULE)}</td>
            <td class="blockHeader" style="width: 14%;"><span class="redColor">*</span> {vtranslate("LBL_ACCOUNTS_INVOICE_DELIVERY", $MODULE)}</td>
            <td class="blockHeader" style="width: 14%;"><span class="redColor">*</span> {vtranslate("LBL_ACCOUNTS_FINANCE_CHARGE", $MODULE)}</td>
            <td class="blockHeader" style="width: 14%;"><span class="redColor">*</span> {vtranslate("LBL_ACCOUNTS_PAYMENT_TERMS", $MODULE)}</td>
        </tr>

        <tr class="defaultInvoiceSetting hide" style="text-align:center">
            <td style="text-align: center">
                <i title="Delete" class="icon-trash deleteInvoiceSetting alignMiddle" style="margin: 5px 10px;"></i>
                <input type="hidden" name="invoice_id[]">
            </td>

            <td>
                <div class="row-fluid">
                    <span class="span12">
                        <select class="chosen-select" name="invoice_commodity[]" style="width: 95%;">
                            <option value="">Select an Option</option>
                            {foreach item=business_line key=ROW_NUM from=$BUSINESS_LINES}
                                <option value="{$business_line}">{vtranslate($business_line, $MODULE)}</option>
                            {/foreach}
                        </select>
                    </span>
                </div>
            </td>
            <td>
                <div class="row-fluid">
                    <span class="span12">
                        <select class="chosen-select" name="invoice_format[]" style="width: 95%;">
                            <option value="">Select an Option</option>
                            {foreach item=invoice_format key=ROW_NUM from=$INVOICE_SETTINGS_OPTIONS.invoice_format}
                                <option value="{$invoice_format}">{$invoice_format}</option>
                            {/foreach}
                        </select>
                    </span>
                </div>
            </td>
            <td>
                <div class="row-fluid">
                    <span class="span12">
                        <select class="chosen-select" name="invoice_packet[]" style="width: 95%;">
                            <option value="">Select an Option</option>
                            {foreach item=invoice_pkg_format key=ROW_NUM from=$INVOICE_SETTINGS_OPTIONS.invoice_pkg_format}
                                <option value="{$invoice_pkg_format}">{$invoice_pkg_format}</option>
                            {/foreach}
                        </select>
                    </span>
                </div>
            </td>
            <td>
                <div class="row-fluid">
                    <span class="span12">
                        <select class="chosen-select" name="invoice_document[]" style="width: 95%;">
                            <option value="">Select an Option</option>
                            {foreach item=document_format key=ROW_NUM from=$INVOICE_SETTINGS_OPTIONS.document_format}
                                <option value="{$document_format}">{$document_format}</option>
                            {/foreach}
                        </select>
                    </span>
                </div>
            </td>
            <td>
                <div class="row-fluid">
                    <span class="span12">
                        <select class="chosen-select" name="invoice_delivery[]" style="width: 95%;">
                            <option value="">Select an Option</option>
                            {foreach item=delivery_format key=ROW_NUM from=$INVOICE_SETTINGS_OPTIONS.delivery_format}
                                <option value="{$delivery_format}">{$delivery_format}</option>
                            {/foreach}
                        </select>
                    </span>
                </div>
            </td>
            <td>
                <div class="row-fluid">
                    <span class="span12">
                        <input type="text" style="width: 95%;" name="invoice_finance_charge[]" class="input-large" maxlength="20">
                    </span>
                </div>
            </td>
            <td>
                <div class="row-fluid">
                    <span class="span12">
                        <input type="text" style="width: 95%;" name="payment_terms[]" class="input-large" maxlength="255">
                    </span>
                </div>
            </td>
        </tr>


        <!-- DEFAULT TABLE ENDS -->
            {foreach item=current_settings key=ROW_NUM from=$CURRENT_INVOICE_SETTINGS}
                <tr>
                <td style="width: 30px; text-align: center">
                    <i title="Delete" class="icon-trash deleteInvoiceSetting" style="margin: 5px 10px;"></i>
                    <input type="hidden" name="invoice_id[]" value="{$current_settings.id}">
                </td>

                <td>
                    <div class="row-fluid">
                    <span class="span10">
                        <select class="chosen-select chzn-select" name="invoice_commodity[]" style="max-width: 200px;">
                            <option value="">Select an Option</option>
                            {foreach item=business_line key=ROW_NUM from=$BUSINESS_LINES}
                                <option value="{$business_line}" {if $business_line == $current_settings.commodity}selected {/if}>{vtranslate($business_line, $MODULE)}</option>
                            {/foreach}
                        </select>
                    </span>
                    </div>
                </td>
                <td>
                    <div class="row-fluid">
                    <span class="span10">
                        <select class="chosen-select chzn-select" name="invoice_format[]" style="max-width: 200px;">
                            <option value="">Select an Option</option>
                            {foreach item=invoice_format key=ROW_NUM from=$INVOICE_SETTINGS_OPTIONS.invoice_format}
                                <option value="{$invoice_format}" {if $invoice_format == $current_settings.invoice_template}selected {/if}>{$invoice_format}</option>
                            {/foreach}
                        </select>
                    </span>
                    </div>
                </td>
                <td>
                    <div class="row-fluid">
                    <span class="span10">
                        <select class="chosen-select chzn-select" name="invoice_packet[]" style="max-width: 200px;">
                            <option value="">Select an Option</option>
                            {foreach item=invoice_pkg_format key=ROW_NUM from=$INVOICE_SETTINGS_OPTIONS.invoice_pkg_format}
                                <option value="{$invoice_pkg_format}" {if $invoice_pkg_format == $current_settings.invoice_packet}selected {/if}>{$invoice_pkg_format}</option>
                            {/foreach}
                        </select>
                    </span>
                    </div>
                </td>
                <td>
                    <div class="row-fluid">
                    <span class="span10">
                        <select class="chosen-select chzn-select" name="invoice_document[]" style="max-width: 200px;">
                            <option value="">Select an Option</option>
                            {foreach item=document_format key=ROW_NUM from=$INVOICE_SETTINGS_OPTIONS.document_format}
                                <option value="{$document_format}" {if $document_format == $current_settings.document_format}selected {/if}>{$document_format}</option>
                            {/foreach}
                        </select>
                    </span>
                    </div>
                </td>
                <td>
                    <div class="row-fluid">
                    <span class="span10">
                        <select class="chosen-select chzn-select" name="invoice_delivery[]" style="max-width: 200px;">
                            <option value="">Select an Option</option>
                            {foreach item=delivery_format key=ROW_NUM from=$INVOICE_SETTINGS_OPTIONS.delivery_format}
                                <option value="{$delivery_format}" {if $delivery_format == $current_settings.invoice_delivery}selected {/if}>{$delivery_format}</option>
                            {/foreach}
                        </select>
                    </span>
                    </div>
                </td>
                <td>
                    <div class="row-fluid">
                    <span class="span10">
                        <input type="text" style="width: 200px;" name="invoice_finance_charge[]" value="{$current_settings.finance_charge}" class="input-large" maxlength="20">
                    </span>
                    </div>
                </td>
                <td>
                    <div class="row-fluid">
                    <span class="span10">
                        <input type="text" style="width: 200px;" value="{$current_settings.payment_terms}" name="payment_terms[]" class="input-large" maxlength="255">
                    </span>
                    </div>
                </td>
            </tr>
            {/foreach}



        </tbody>
    </table>
    <br/>
{/strip}