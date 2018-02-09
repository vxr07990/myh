{strip}
    {assign var=MODULE value=Accounts}
    <!-- EditBillingAddressesTable.tpl -->
    <table class="table table-bordered blockContainer showInlineTable">
        <thead>
        <tr>
            <th class="blockHeader" colspan="7">
                <img class="cursorPointer alignMiddle blockToggle  hide  " src="layouts/vlayout/skins/bluelagoon/images/arrowRight.png" data-mode="hide" data-id="319">
                <img class="cursorPointer alignMiddle blockToggle " src="layouts/vlayout/skins/bluelagoon/images/arrowDown.png" data-mode="show" data-id="319" style="display: inline;">
                &nbsp;&nbsp;
                {vtranslate("LBL_ACCOUNT_INVOICESETTINGS", $MODULE)}
            </th>
        </tr>
        </thead>
        <tbody name="{$BLOCK_LABEL_KEY}">

        <tr>
            <td class="blockHeader fieldLabel medium narrowWidthType" style="width: 200px;">{vtranslate("LBL_ACCOUNTS_COMMODITY", $MODULE)}</td>
            <td class="blockHeader fieldLabel medium narrowWidthType" style="width: 200px;">{vtranslate("LBL_ACCOUNTS_INVOICE_TEMPLATE", $MODULE)}</td>
            <td class="blockHeader fieldLabel medium narrowWidthType" style="width: 200px;">{vtranslate("LBL_ACCOUNTS_INVOICE_PACKET", $MODULE)}</td>
            <td class="blockHeader fieldLabel medium narrowWidthType" style="width: 200px;">{vtranslate("LBL_ACCOUNTS_INVOICE_DOCUMENT_FORMAT", $MODULE)}</td>
            <td class="blockHeader fieldLabel medium narrowWidthType" style="width: 200px;">{vtranslate("LBL_ACCOUNTS_INVOICE_DELIVERY", $MODULE)}</td>
            <td class="blockHeader fieldLabel medium narrowWidthType" style="width: 200px;">{vtranslate("LBL_ACCOUNTS_FINANCE_CHARGE", $MODULE)}</td>
            <td class="blockHeader fieldLabel medium narrowWidthType" style="width: 200px;">{vtranslate("LBL_ACCOUNTS_PAYMENT_TERMS", $MODULE)}</td>
        </tr>


            {foreach item=setting key=ROW_NUM from=$INVOICE_SETTINGS}
                <tr>
                 <td class="fieldValue medium narrowWidthType">

                    <span class="value">
                        {vtranslate($setting.commodity, $MODULE)}
                    </span>

                </td>
                <td class="fieldValue medium narrowWidthType">

                    <span class="value">
                        {$setting.invoice_template}
                    </span>

                </td>
                <td class="fieldValue medium narrowWidthType">

                    <span class="value">
                       {$setting.invoice_packet}
                    </span>

                </td>
                <td class="fieldValue medium narrowWidthType">

                    <span class="value">
                        {$setting.document_format}
                    </span>

                </td>
                <td class="fieldValue medium narrowWidthType">

                    <span class="value">
                        {$setting.invoice_delivery}
                    </span>

                </td>
                <td class="fieldValue medium narrowWidthType">

                    <span class="value">
                        {$setting.finance_charge}
                    </span>

                </td>
                <td class="fieldValue medium narrowWidthType">

                    <span class="value">
                        {$setting.payment_terms}
                    </span>

                </td>

            </tr>
            {/foreach}



        </tbody>
    </table>
    <br/>
{/strip}