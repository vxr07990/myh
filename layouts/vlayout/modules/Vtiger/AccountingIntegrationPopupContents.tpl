{strip}
    <input type='hidden' id='pageNumber' value="{$PAGE_NUMBER}">
    <input type='hidden' id='pageLimit' value="{$PAGING_MODEL->getPageLimit()}">
    <input type="hidden" id="noOfEntries" value="{$LISTVIEW_ENTRIES_COUNT}">
    <input type="hidden" id="pageStartRange" value="{$PAGING_MODEL->getRecordStartRange()}" />
    <input type="hidden" id="pageEndRange" value="{$PAGING_MODEL->getRecordEndRange()}" />
    <input type="hidden" id="previousPageExist" value="{$PAGING_MODEL->isPrevPageExists()}" />
    <input type="hidden" id="nextPageExist" value="{$PAGING_MODEL->isNextPageExists()}" />
    <input type="hidden" id="totalCount" value="{$LISTVIEW_COUNT}" />
    <input type="hidden" id="agentId" value="{$AGENTID}" />
    <div class="contents-topscroll">
    <div class="topscroll-div">
        &nbsp;
    </div>
</div>

    <div class="popupEntriesDiv relatedContents contents-bottomscroll">
	<input type="hidden" value="{$ORDER_BY}" id="orderBy">
	<input type="hidden" value="{$SORT_ORDER}" id="sortOrder">
        {assign var=WIDTHTYPE value=$CURRENT_USER_MODEL->get('rowheight')}
        <div class="bottomscroll-div">
        <table class="table table-bordered listViewEntriesTable">
            <thead>
                <tr class="listViewHeaders">
                    {foreach item=LISTVIEW_HEADER from=$LISTVIEW_HEADERS}
                        <th class="{$WIDTHTYPE}">
                        <a href="javascript:void(0);" class="listViewHeaderValues" data-nextsortorderval="{if $ORDER_BY eq $LISTVIEW_HEADER.title}{$NEXT_SORT_ORDER}{else}ASC{/if}" data-columnname="{$LISTVIEW_HEADER.title}">{$LISTVIEW_HEADER.title}
                            {if $ORDER_BY eq $LISTVIEW_HEADER.title}<img class="sortImage" src="{vimage_path( $SORT_IMAGE, $MODULE_NAME)}">{else}<img class="hide sortingImage" src="{vimage_path( 'downArrowSmall.png', $MODULE_NAME)}">{/if}</a>
                    </th>
                    {/foreach}
                </tr>
            </thead>
            {foreach item=LISTVIEW_ENTRY from=$LISTVIEW_ENTRIES name=popupListView}
                <tr class="listViewEntries" data-dismiss="modal" data-id="{$LISTVIEW_ENTRY['id']}" data-name='{Vtiger_Util_Helper::toSafeHTML($LISTVIEW_ENTRY['label'])}' data-info='{Vtiger_Util_Helper::toSafeHTML(ZEND_JSON::encode($LISTVIEW_ENTRY))}'
                    id="{$SOURCE_MODULE}_popUpListView_row_{$smarty.foreach.popupListView.index+1}">

                {foreach item=LISTVIEW_HEADER from=$LISTVIEW_HEADERS}
                    <td class="listViewEntryValue {$WIDTHTYPE}">
                        {$LISTVIEW_ENTRY[$LISTVIEW_HEADER.title]}
                    </td>
                {/foreach}
            </tr>
            {/foreach}
        </table>
    </div>

        <!--added this div for Temporarily -->
        {if $LISTVIEW_ENTRIES_COUNT eq '0'}
            <div class="row-fluid">
            <div class="emptyRecordsDiv">{vtranslate('LBL_NO', $MODULE)} {vtranslate($SOURCE_MODULE, $MODULE_NAME)} {vtranslate('LBL_FOUND', $MODULE_NAME)}.</div>
        </div>
        {/if}
</div>
{/strip}