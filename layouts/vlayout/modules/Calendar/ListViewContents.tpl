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
    <input type="hidden" id="view" value="{$VIEW}"/>
    <input type="hidden" id="pageStartRange" value="{$PAGING_MODEL->getRecordStartRange()}"/>
    <input type="hidden" id="pageEndRange" value="{$PAGING_MODEL->getRecordEndRange()}"/>
    <input type="hidden" id="previousPageExist" value="{$PAGING_MODEL->isPrevPageExists()}"/>
    <input type="hidden" id="nextPageExist" value="{$PAGING_MODEL->isNextPageExists()}"/>
    <input type="hidden" id="alphabetSearchKey" value="{$MODULE_MODEL->getAlphabetSearchField()}"/>
    <input type="hidden" id="Operator" value="{$OPERATOR}"/>
    <input type="hidden" id="alphabetValue" value="{$ALPHABET_VALUE}"/>
    <input type="hidden" id="totalCount" value="{$LISTVIEW_COUNT}"/>
    <input type='hidden' value="{$PAGE_NUMBER}" id='pageNumber'>
    <input type='hidden' value="{$PAGING_MODEL->getPageLimit()}" id='pageLimit'>
    <input type="hidden" value="{$LISTVIEW_ENTRIES_COUNT}" id="noOfEntries">
    {assign var = ALPHABETS_LABEL value = vtranslate('LBL_ALPHABETS', 'Vtiger')}
    {assign var = ALPHABETS value = ','|explode:$ALPHABETS_LABEL}
    <div class="alphabetSorting noprint">
        <table width="100%" class="table-bordered" style="border: 1px solid #ddd;table-layout: fixed">
            <tbody>
            <tr>
                {foreach item=ALPHABET from=$ALPHABETS}
                    <td class="alphabetSearch textAlignCenter cursorPointer {if $ALPHABET_VALUE eq $ALPHABET} highlightBackgroundColor {/if}"
                        style="padding : 0px !important"><a id="{$ALPHABET}" href="#">{$ALPHABET}</a></td>
                {/foreach}
            </tr>
            </tbody>
        </table>
    </div>
    <div id="selectAllMsgDiv" class="alert-block msgDiv noprint">
        <strong><a id="selectAllMsg">{vtranslate('LBL_SELECT_ALL',$MODULE)}&nbsp;{vtranslate($MODULE ,$MODULE)}
                &nbsp;(<span id="totalRecordsCount"></span>)</a></strong>
    </div>
    <div id="deSelectAllMsgDiv" class="alert-block msgDiv noprint">
        <strong><a id="deSelectAllMsg">{vtranslate('LBL_DESELECT_ALL_RECORDS',$MODULE)}</a></strong>
    </div>
    <div class="contents-topscroll noprint">
        <div class="topscroll-div">
            &nbsp;
        </div>
    </div>
    <div class="listViewEntriesDiv">
        <div class="">
            <input type="hidden" value="{$ORDER_BY}" id="orderBy">
            <input type="hidden" value="{$SORT_ORDER}" id="sortOrder">
	<span class="listViewLoadingImageBlock hide modal noprint" id="loadingListViewModal">
		<img class="listViewLoadingImage" src="{vimage_path('loading.gif')}" alt="no-image"
             title="{vtranslate('LBL_LOADING', $MODULE)}"/>
		<p class="listViewLoadingMsg">{vtranslate('LBL_LOADING_LISTVIEW_CONTENTS', $MODULE)}........</p>
	</span>
            {assign var=WIDTHTYPE value=$CURRENT_USER_MODEL->get('rowheight')}
            <table class="table table-bordered listViewEntriesTable">
                <thead>
                <tr class="listViewHeaders">
                    <th width="5%">
                        <input type="checkbox" id="listViewEntriesMainCheckBox"/>
                    </th>
                    {foreach item=LISTVIEW_HEADER from=$LISTVIEW_HEADERS}
                        <th nowrap {if $LISTVIEW_HEADER@last} colspan="2" {/if}>
                            <a href="javascript:void(0);" class="listViewHeaderValues"
                               data-nextsortorderval="{if $COLUMN_NAME eq $LISTVIEW_HEADER->get('column')}{$NEXT_SORT_ORDER}{else}ASC{/if}"
                               data-columnname="{$LISTVIEW_HEADER->get('column')}">{vtranslate($LISTVIEW_HEADER->get('label'), $LISTVIEW_HEADER->getModuleName())}
                                &nbsp;&nbsp;{if $COLUMN_NAME eq $LISTVIEW_HEADER->get('column')}<img
                                class="{$SORT_IMAGE} icon-white">{/if}</a>
                        </th>
                    {/foreach}
                </tr>
                </thead>
                <tr>
                    <td></td>
                    {foreach item=LISTVIEW_HEADER from=$LISTVIEW_HEADERS}
                        <td>
                            {assign var=FIELD_UI_TYPE_MODEL value=$LISTVIEW_HEADER->getUITypeModel()}
                            {include file=vtemplate_path($FIELD_UI_TYPE_MODEL->getListSearchTemplateName(),$MODULE)
                            FIELD_MODEL= $LISTVIEW_HEADER SEARCH_INFO=$SEARCH_DETAILS[$LISTVIEW_HEADER->getName()] USER_MODEL=$CURRENT_USER_MODEL}
                        </td>
                    {/foreach}
                    <td>
                        <button data-trigger="listSearch">Search</button>
                    </td>
                </tr>
                {foreach item=LISTVIEW_ENTRY from=$LISTVIEW_ENTRIES name=listview}
                    {assign var=CURRENT_USER_ID value=$CURRENT_USER_MODEL->getId()}
                    {assign var=RAWDATA value=$LISTVIEW_ENTRY->getRawData()}
                    {assign var=ACTIVITYTYPE value=Vtiger_Record_Model::getInstanceById($LISTVIEW_ENTRY->getId(), $MODULE)->get('activitytype')}

                    {if !$CURRENT_USER_MODEL->isAdminUser() && $ACTIVITYTYPE != 'Task' && $LISTVIEW_ENTRY->get('visibility') == 'Private'}
                        {assign var=DETAIL_VIEW_URL value=''}
                        {assign var=FULL_DETAIL_VIEW_URL value=''}
                        {assign var=EDIT_VIEW_URL value=''}
					{else}
						{assign var=DETAIL_VIEW_URL value=$LISTVIEW_ENTRY->getDetailViewUrl()}
						{assign var=FULL_DETAIL_VIEW_URL value=$LISTVIEW_ENTRY->getFullDetailViewUrl()}
						{assign var=EDIT_VIEW_URL value=$LISTVIEW_ENTRY->getEditViewUrl()}
                    {/if}
					
					{assign var=IS_MODULE_VIEWABLE value=isPermitted($MODULE, 'DetailView', $LISTVIEW_ENTRY->getId())}
					{assign var=IS_MODULE_EDITABLE value=isPermitted($MODULE, 'EditView', $LISTVIEW_ENTRY->getId())}
					{assign var=IS_MODULE_DELETABLE value=isPermitted($MODULE, 'Delete', $LISTVIEW_ENTRY->getId())}
                    
					<tr class="listViewEntries" data-id='{$LISTVIEW_ENTRY->getId()}'
                            {if $DETAIL_VIEW_URL} data-recordUrl='{$DETAIL_VIEW_URL}' {/if}
                            data-activity-type = '{$ACTIVITYTYPE}'
                        id="{$MODULE}_listView_row_{$smarty.foreach.listview.index+1}">
                        <td width="5%" class="{$WIDTHTYPE}">
                            <input type="checkbox" value="{$LISTVIEW_ENTRY->getId()}" class="listViewEntriesCheckBox"/>
                        </td>
                        {foreach item=LISTVIEW_HEADER from=$LISTVIEW_HEADERS}
                        {assign var=LISTVIEW_HEADERNAME value=$LISTVIEW_HEADER->get('name')}
                        <td class="{if $LISTVIEW_HEADERNAME eq 'taskstatus'} listViewEntryNotLink {/if} listViewEntryValue {$WIDTHTYPE}" data-field-type="{$LISTVIEW_HEADER->getFieldDataType()}" nowrap>
                            {if $LISTVIEW_HEADER->isNameField() eq true or $LISTVIEW_HEADER->get('uitype') eq '4'}
                                <a href="{$LISTVIEW_ENTRY->getDetailViewUrl()}">{$LISTVIEW_ENTRY->get($LISTVIEW_HEADERNAME)}</a>
                            {else if $LISTVIEW_HEADER->get('uitype') eq '72'}
                                {assign var=CURRENCY_SYMBOL_PLACEMENT value={$CURRENT_USER_MODEL->get('currency_symbol_placement')}}
                                {if $CURRENCY_SYMBOL_PLACEMENT eq '1.0$'}
                                    {$LISTVIEW_ENTRY->get($LISTVIEW_HEADERNAME)}{$LISTVIEW_ENTRY->get('currencySymbol')}
                                {else}
                                    {$LISTVIEW_ENTRY->get('currencySymbol')}{$LISTVIEW_ENTRY->get($LISTVIEW_HEADERNAME)}
                                {/if}
                            {else if $LISTVIEW_HEADERNAME eq 'taskstatus' && isPermitted($MODULE, 'EditView', $LISTVIEW_ENTRY->getId()) eq 'yes'}
                                {if $ACTIVITYTYPE eq 'Task'}
                                <select class="chzn-select taskstatus span2" data-recordid="{$LISTVIEW_ENTRY->getId()}" data-fieldname="taskstatus">
                                    {foreach key=KEY item=VALUE from=$TASKSTATUS_ARRAY}
                                        <option value="{$KEY}"  {if $LISTVIEW_ENTRY->get($LISTVIEW_HEADERNAME) eq $VALUE}selected{/if}>{$VALUE}</option>
                                    {/foreach}
                                </select>
                                {else}
                                    <select class="chzn-select taskstatus span2" data-recordid="{$LISTVIEW_ENTRY->getId()}" data-fieldname="eventstatus">
                                    {foreach key=KEY item=VALUE from=$EVENTSTATUS_ARRAY}
                                        <option value="{$KEY}"  {if $LISTVIEW_ENTRY->get($LISTVIEW_HEADERNAME) eq $VALUE}selected{/if}>{$VALUE}</option>
                                    {/foreach}
                                </select>
                                {/if}
                            {else}
                                {$LISTVIEW_ENTRY->get($LISTVIEW_HEADERNAME)}
                            {/if}
                            {if $LISTVIEW_HEADER@last}
                            </td><td nowrap class="{$WIDTHTYPE}">
                            <div class="actions pull-right">
                                <span class="actionImages">
                                    {if $IS_MODULE_EDITABLE eq 'yes' && $EDIT_VIEW_URL && $LISTVIEW_ENTRY->get('taskstatus') neq 'Held' && $LISTVIEW_ENTRY->get('taskstatus') neq 'Completed'}
                                        <a class="markAsHeld"><i title="{if $ACTIVITYTYPE neq 'Task'}{vtranslate('LBL_MARK_AS_HELD', $MODULE)}{else}{vtranslate('LBL_MARK_COMPLETED', $MODULE)}{/if}" class="icon-ok alignMiddle"></i></a>&nbsp;
                                    {/if}
                                    {if $IS_MODULE_EDITABLE eq 'yes' && $EDIT_VIEW_URL && $LISTVIEW_ENTRY->get('taskstatus') eq 'Held'}
                                        <a class="holdFollowupOn"><i title="{vtranslate('LBL_HOLD_FOLLOWUP_ON', "Events")}" class="icon-flag alignMiddle"></i></a>&nbsp;
                                    {/if}
                                    {if $IS_MODULE_VIEWABLE eq 'yes' && $FULL_DETAIL_VIEW_URL}
                                        <a href="{$FULL_DETAIL_VIEW_URL}"><i title="{vtranslate('LBL_SHOW_COMPLETE_DETAILS', $MODULE)}" class="icon-th-list alignMiddle"></i></a>&nbsp;
                                    {/if}
                                    {if $IS_MODULE_EDITABLE eq 'yes' && $EDIT_VIEW_URL}
                                        <a href='{$EDIT_VIEW_URL}'><i title="{vtranslate('LBL_EDIT', $MODULE)}" class="icon-pencil alignMiddle"></i></a>&nbsp;
                                    {/if}
                                    {if $IS_MODULE_DELETABLE eq 'yes'}
                                        <a class="deleteRecordButton"><i title="{vtranslate('LBL_DELETE', $MODULE)}" class="icon-trash alignMiddle"></i></a>
                                    {/if}
                                </span>
                            </div></td>
                            {/if}
                        </td>
                        {/foreach}
                    </tr>
                {/foreach}
            </table>

            <!--added this div for Temporarily -->
            {if $LISTVIEW_ENTRIES_COUNT eq '0'}
                <table class="emptyRecordsDiv">
                    <tbody>
                    <tr>
                        <td>
                            {assign var=SINGLE_MODULE value="SINGLE_$MODULE"}
                            {vtranslate('LBL_NO')} {vtranslate($MODULE, $MODULE)} {vtranslate('LBL_FOUND')}
                            .{if $IS_MODULE_EDITABLE} {vtranslate('LBL_CREATE')} <a
                            href="{$MODULE_MODEL->getCreateRecordUrl()}">{vtranslate($SINGLE_MODULE, $MODULE)}</a>{/if}
                        </td>
                    </tr>
                    </tbody>
                </table>
            {/if}
        </div>
    </div>
{/strip}
