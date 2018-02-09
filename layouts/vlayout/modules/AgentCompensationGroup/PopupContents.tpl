{*<!--
/*********************************************************************************
** The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
*
 ********************************************************************************/
-->*}
{strip}
    {if $SOURCE_FIELD eq 'agentcompgr_tariffcontract' && $SOURCE_MODULE eq 'AgentCompensationGroup'}
        <input type='hidden' id='pageNumber' value="{$PAGE_NUMBER}">
        <input type='hidden' id='pageLimit' value="{$PAGING_MODEL->getPageLimit()}">
        <input type="hidden" id="noOfEntries" value="{$LISTVIEW_ENTRIES_COUNT}">
        <input type="hidden" id="pageStartRange" value="{$PAGING_MODEL->getRecordStartRange()}"/>
        <input type="hidden" id="pageEndRange" value="{$PAGING_MODEL->getRecordEndRange()}"/>
        <input type="hidden" id="previousPageExist" value="{$PAGING_MODEL->isPrevPageExists()}"/>
        <input type="hidden" id="nextPageExist" value="{$PAGING_MODEL->isNextPageExists()}"/>
        <input type="hidden" id="totalCount" value="{$LISTVIEW_COUNT}"/>
        <input type="hidden" id="agentId" value="{$AGENTID}" />
        <div class="contents-topscroll">
            <div class="topscroll-div">
                &nbsp;
            </div>
        </div>
        <div class="popupEntriesDiv relatedContents contents-bottomscroll">
            <input type="hidden" value="{$ORDER_BY}" id="orderBy">
            <input type="hidden" value="{$SORT_ORDER}" id="sortOrder">
            {if $SOURCE_MODULE eq "Emails"}
                <input type="hidden" value="Vtiger_EmailsRelatedModule_Popup_Js" id="popUpClassName"/>
            {/if}
            {assign var=WIDTHTYPE value=$CURRENT_USER_MODEL->get('rowheight')}
            <div class="bottomscroll-div">
                <table class="table table-bordered listViewEntriesTable">
                    <thead>
                    <tr class="listViewHeaders">
                        {if $MULTI_SELECT}
                            <th class="{$WIDTHTYPE}">
                                <input type="checkbox" class="selectAllInCurrentPage"/>
                            </th>
                        {/if}
                        <th class="{$WIDTHTYPE}">
                            Name
                        </th>
                        <th class="{$WIDTHTYPE}">
                            Entity Type
                        </th>
                    </tr>
                    </thead>
                    {foreach item=LISTVIEW_ENTRY from=$LISTVIEW_ENTRIES name=popupListView}
                        <tr class="listViewEntries" data-dismiss="modal" data-id="{$LISTVIEW_ENTRY->getId()}"
                            data-name='{$LISTVIEW_ENTRY->getName()}'
                            data-info='{ZEND_JSON::encode($LISTVIEW_ENTRY->getRawData())}'
                            id="{$LISTVIEW_ENTRY->getModule()->getName()}_popUpListView_row_{$smarty.foreach.popupListView.index+1}">
                            {if $MULTI_SELECT}
                                <td class="{$WIDTHTYPE} entryCheckBoxTd">
                                    <input class="entryCheckBox" type="checkbox"/>
                                </td>
                            {/if}

                            <td class="listViewEntryValue {$WIDTHTYPE}">{$LISTVIEW_ENTRY->getName()}</td>
                            <td class="listViewEntryValue {$WIDTHTYPE}">{vtranslate($LISTVIEW_ENTRY->getModule()->getName(), $LISTVIEW_ENTRY->getModule()->getName())}</td>
                        </tr>
                    {/foreach}
                </table>
            </div>

            <!--added this div for Temporarily -->
            {if $LISTVIEW_ENTRIES_COUNT eq '0'}
                <div class="row-fluid">
                    <div class="emptyRecordsDiv">{vtranslate('LBL_NO', $MODULE)} {vtranslate('Tariffs', 'Tariffs')} / {vtranslate('TariffManager', 'TariffManager')} {vtranslate('LBL_FOUND', $MODULE)}
                        .
                    </div>
                </div>
            {/if}
        </div>
    {else}
        {include file='PopupContents.tpl'|@vtemplate_path}
    {/if}
{/strip}
