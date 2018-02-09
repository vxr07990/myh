{strip}
    {assign var=MODULE value=Accounts}
    <!-- EditBillingAddressesTable.tpl -->
    <table class="table table-bordered blockContainer showInlineTable">
        <thead>
        <tr>
            <th class="blockHeader" colspan="10">
                <img class="cursorPointer alignMiddle blockToggle  hide  "
                     src="layouts/vlayout/skins/bluelagoon/images/arrowRight.png" data-mode="hide" data-id="319">
                <img class="cursorPointer alignMiddle blockToggle "
                     src="layouts/vlayout/skins/bluelagoon/images/arrowDown.png" data-mode="show" data-id="319"
                     style="display: inline;">
                &nbsp;&nbsp;{vtranslate("LBL_ACCOUNTS_BILLING_ADDRESS", $MODULE)}
            </th>
        </tr>
        </thead>
        <tbody id='billingAddressesTable'>


        <tr>
            <td class="blockHeader fieldLabel medium narrowWidthType" style="min-width: 130px">{vtranslate("LBL_ACCOUNTS_BUSINESSLINE", $MODULE)}</td>
            <td class="blockHeader fieldLabel medium narrowWidthType" style="min-width: 130px">{vtranslate("LBL_ACCOUNTS_ADDRESS_DESC", $MODULE)}</td>
            <td class="blockHeader fieldLabel medium narrowWidthType" style="min-width: 130px">{vtranslate("LBL_ACCOUNTS_COMPANY_NAME", $MODULE)}</td>
            <td class="blockHeader fieldLabel medium narrowWidthType" style="min-width: 130px">{vtranslate("LBL_ACCOUNTS_ADDRESS1", $MODULE)}</td>
            <td class="blockHeader fieldLabel medium narrowWidthType" style="min-width: 130px">{vtranslate("LBL_ACCOUNTS_ADDRESS2", $MODULE)}</td>
            <td class="blockHeader fieldLabel medium narrowWidthType" style="min-width: 130px">{vtranslate("LBL_ACCOUNTS_CITY", $MODULE)}</td>
            <td class="blockHeader fieldLabel medium narrowWidthType" style="min-width: 80px">{vtranslate("LBL_ACCOUNTS_STATE", $MODULE)}</td>
            <td class="blockHeader fieldLabel medium narrowWidthType" style="min-width: 80px">{vtranslate("LBL_ACCOUNTS_ZIP", $MODULE)}</td>
            <td class="blockHeader fieldLabel medium narrowWidthType" style="min-width: 130px">{vtranslate("LBL_ACCOUNTS_COUNTRY", $MODULE)}</td>
            <td class="blockHeader fieldLabel medium narrowWidthType"
                style=" text-align:center; min-width: 50px;">{vtranslate("LBL_ACCOUNTS_ACTIVE_INACTIVE", $MODULE)}</td>
        </tr>


            {foreach item=billing_row key=ROW_NUM from=$BILLING_ADDRESSES}
                <tr>
                <td>
                    <div class="row-fluid">
                    <span class="span10">
                        {assign var="FIELD_VALUE_LIST" value=' |##| '|explode:$billing_row.commodity}
                        {foreach item=field_value from=$FIELD_VALUE_LIST}
                            {vtranslate($field_value, $MODULE)}<br>
                        {/foreach}
                    </span>
                    </div>
                </td>
                <td class="fieldValue medium narrowWidthType">

                    <span class="value">
                        {$billing_row.address_desc}
                    </span>
                </td>
                <td class="fieldValue medium narrowWidthType">

                    <span class="value">
                       {$billing_row.company}

                    </span>
                </td>
                <td class="fieldValue medium narrowWidthType">

                    <span class="value">
                        {$billing_row.address1}
                    </span>
                </td>
                <td class="fieldValue medium narrowWidthType">

                    <span class="value">
                        {$billing_row.address2}
                    </span>
                </td>
                <td class="fieldValue medium narrowWidthType">

                    <span class="value">
                        {$billing_row.city}
                    </span>

                </td>
                <td class="fieldValue medium narrowWidthType">

                    <span class="value">
                        {$billing_row.state}
                    </span>
                </td>
                <td class="fieldValue medium narrowWidthType">

                    <span class="value">
                            {$billing_row.zip}
                        </span>
                </td>
                <td class="fieldValue medium narrowWidthType">

                    <span class="value">
                           {$billing_row.country}
                        </span>
                </td>
                <td class="fieldValue medium narrowWidthType" style="text-align:center">

                    <span class="value">
                        {if $billing_row.active == 'yes'}Yes{else if}No {/if}
                    </span>
                    </div>
                </td>
            </tr>
            {/foreach}


        </tbody>
    </table>
    <br/>
{/strip}