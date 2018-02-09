{strip}
    {assign var=MODULE value=Accounts}
    <!-- EditBillingAddressesTable.tpl -->
    <table class="table table-bordered blockContainer showInlineTable">
        <thead>
        <tr>
            <th class="blockHeader" colspan="4">{vtranslate("LBL_ACCOUNTS_ADDITIONAL_ROLES", $MODULE)}</th>
        </tr>
        </thead>
        <tbody id='additionalRolesTable'>
        <!-- Adding Buttons -->

        <tr>
            <td class="blockHeader fieldLabel medium narrowWidthType">{vtranslate("LBL_ACCOUNTS_COMMODITY", $MODULE)}</td>
            <td class="blockHeader fieldLabel medium narrowWidthType">{vtranslate("LBL_ACCOUNTS_ROLE", $MODULE)}</td>
            <td class="blockHeader fieldLabel medium narrowWidthType">{vtranslate("LBL_ACCOUNTS_USER", $MODULE)}</td>
        </tr>

        <!-- DEFAULT TABLE ENDS -->

        {foreach item=roles key=ROW_NUM from=$ADDITIONAL_ROLES}
            <tr>
                <td class="fieldValue medium narrowWidthType">
                    {if $roles.commodity|count eq $BUSINESS_LINES|count}
                        <span class="span10">{vtranslate('LBL_ALL')}</span>
                    {else}
                        <span class="span10">{', '|implode:$roles.commodity} </span>
                    {/if}
                </td>
                <td class="fieldValue medium narrowWidthType">
                    {assign var=ROLE value=Vtiger_Record_Model::getInstanceById($roles.role)}
                    <span class="span10">{$ROLE->getDisplayName()}</span>
                </td>
                <td class="fieldValue medium narrowWidthType">
                    {assign var=EMPLOYEE value=Vtiger_Record_Model::getInstanceById($roles.user)}
                    <span class="span10">{$EMPLOYEE->getDisplayName()}</span>
                </td>
            </tr>
        {/foreach}
        </tbody>
    </table>
    <br/>
{/strip}

