{if $CLAIMS_ITEMS_COUNT > 0}
    <div class="relatedContents contents-bottomscroll">
        <div class="bottomscroll-div" style="width: 818px;">
            <table id="claimitems_list" class="table table-bordered listViewEntriesTable" style="margin-bottom: 1%;">
                <thead>
                    <tr class="listViewHeaders">
                        <th nowrap="">
                            <input type="checkbox" value="" id="selectAllItems">
                        </th>
                        {foreach item=VAL from=$CLAIMS_ITEMS_ARRAY_HEADER}
                            <th nowrap="">{vtranslate($VAL,"ClaimItems")}</th>
                        {/foreach}
                        <th nowrap="">&nbsp;</th>
                    </tr>
                </thead>
                <tbody id="claimitems_tbody">
                    {assign var="amount" value=0}
                    {foreach item=MINIARR from=$CLAIMS_ITEMS_ARRAY}
                        <tr class="listViewEntries" data-id="{$MINIARR.claimitemsid}">
                            <td nowrap="" class="medium">
                                <input type="checkbox" value="" name="selectItem{$amount}" class="select_item">
                            </td>
                            {foreach key=COLNAME item=COLVAL from=$MINIARR}
                                {if $COLNAME neq 'claimitemsid'}
                                    <td class="" data-field-name="{$COLNAME}" data-field-type="string" nowrap="">{$COLVAL}</td>
                                {/if}
                            {/foreach}
                            <td nowrap="" class="medium">
                                <div class="actions pull-right">
                                    <span>
                                        <a href="index.php?module=ClaimItems&view=Detail&record={$MINIARR.claimitemsid}">
                                            <i title="Complete Details" class="icon-th-list alignMiddle"></i>
                                        </a>&nbsp;<a href="index.php?module=ClaimItems&view=Edit&record={$MINIARR.claimitemsid}">
                                            <i title="Edit" class="icon-pencil alignMiddle"></i>
                                        </a>&nbsp;<a class="deleteRecordButton">
                                            <i title="Delete" class="icon-trash alignMiddle"></i>
                                        </a>
                                    </span>
                                </div>
                            </td>
                        </tr>
                        {assign var="amount" value=$amount+1}
                    {/foreach}
                </tbody>
            </table>
        </div>
    </div>
{else}
    <div class="relatedContents contents-bottomscroll">
        <div id="claimtype-claimitems-table">
            <table class="table table-bordered listViewEntriesTable">
                <thead>
                    <tr>
                        <th class="blockHeader">Claim Items Detail Information</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td style="height: 40px;text-align: center;vertical-align: middle;">{vtranslate('LBL_NO_CLAIMITEMS', $MODULE_NAME)}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
{/if}