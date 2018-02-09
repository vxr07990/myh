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
    <div class="relatedContainer">
        {assign var=RELATED_MODULE_NAME value=$RELATED_MODULE->get('name')}
        {if !isset($IS_CALENDAR_STATUS_EDITABLE)}{assign var = IS_CALENDAR_STATUS_EDITABLE value = 'false'}{/if}
        <input type="hidden" name="currentPageNum" value="{$PAGING->getCurrentPage()}"/>
        <input type="hidden" name="relatedModuleName" class="relatedModuleName" value="{$RELATED_MODULE->get('name')}"/>
        <input type="hidden" value="{$ORDER_BY}" id="orderBy">
        <input type="hidden" value="{$SORT_ORDER}" id="sortOrder">
        <input type="hidden" value="{$RELATED_ENTIRES_COUNT}" id="noOfEntries">
        <input type='hidden' value="{$PAGING->getPageLimit()}" id='pageLimit'>
        <input type='hidden' value="{$TOTAL_ENTRIES}" id='totalCount'>
        <div class="relatedHeader ">
            <div class="btn-toolbar row-fluid">

                {* Custom filter - start *}
                    <span class="btn-toolbar span5">
                        <span class="btn-group listViewMassActions">
                            {if count($LISTVIEW_MASSACTIONS) gt 0 || $LISTVIEW_LINKS['LISTVIEW']|@count gt 0}
                                <button class="btn dropdown-toggle" data-toggle="dropdown"><strong>{vtranslate('LBL_ACTIONS', $MODULE)}</strong>&nbsp;&nbsp;<i class="caret"></i></button>
                                <ul class="dropdown-menu">
                                    {foreach item="LISTVIEW_MASSACTION" from=$LISTVIEW_MASSACTIONS name=actionCount}
                                        <li id="{$MODULE}_listView_massAction_{Vtiger_Util_Helper::replaceSpaceWithUnderScores($LISTVIEW_MASSACTION->getLabel())}"><a href="javascript:void(0);" {if stripos($LISTVIEW_MASSACTION->getUrl(), 'javascript:')===0}onclick='{$LISTVIEW_MASSACTION->getUrl()|substr:strlen("javascript:")};'{else} onclick="Vtiger_List_Js.triggerMassAction('{$LISTVIEW_MASSACTION->getUrl()}')"{/if} >{vtranslate($LISTVIEW_MASSACTION->getLabel(), $MODULE)}</a></li>
                                        {if $smarty.foreach.actionCount.last eq true}
                                        <li class="divider"></li>
                                    {/if}
                                    {/foreach}
                                    {if $LISTVIEW_LINKS['LISTVIEW']|@count gt 0}
                                        {foreach item=LISTVIEW_ADVANCEDACTIONS from=$LISTVIEW_LINKS['LISTVIEW']}
                                            <li id="{$MODULE}_listView_advancedAction_{Vtiger_Util_Helper::replaceSpaceWithUnderScores($LISTVIEW_ADVANCEDACTIONS->getLabel())}"><a {if stripos($LISTVIEW_ADVANCEDACTIONS->getUrl(), 'javascript:')===0} href="javascript:void(0);" onclick='{$LISTVIEW_ADVANCEDACTIONS->getUrl()|substr:strlen("javascript:")};'{else} href='{$LISTVIEW_ADVANCEDACTIONS->getUrl()}' {/if}>{vtranslate($LISTVIEW_ADVANCEDACTIONS->getLabel(), $MODULE)}</a></li>
                                        {/foreach}
                                    {/if}
                                </ul>
                            {/if}
                        </span>
                        {foreach item=LISTVIEW_BASICACTION from=$LISTVIEW_LINKS['LISTVIEWBASIC']}
                            <span class="btn-group">
                                <button id="{$MODULE}_listView_basicAction_{Vtiger_Util_Helper::replaceSpaceWithUnderScores($LISTVIEW_BASICACTION->getLabel())}" class="btn addButton" {if stripos($LISTVIEW_BASICACTION->getUrl(), 'javascript:')===0} onclick='{$LISTVIEW_BASICACTION->getUrl()|substr:strlen("javascript:")};'{else} onclick='window.location.href="{$LISTVIEW_BASICACTION->getUrl()}"'{/if}><i class="icon-plus"></i>&nbsp;<strong>{vtranslate($LISTVIEW_BASICACTION->getLabel(), $MODULE)}</strong></button>
                            </span>
                        {/foreach}

                    {foreach item=RELATED_LINK from=$RELATED_LIST_LINKS['LISTVIEWBASIC']}
                        <div class="btn-group">
                            {assign var=IS_SELECT_BUTTON value={$RELATED_LINK->get('_selectRelation')}}
                            <button type="button" class="btn addButton
                            {if $IS_SELECT_BUTTON eq true} selectRelation {/if} "
                                {if $IS_SELECT_BUTTON eq true}
                                    data-moduleName={$RELATED_LINK->get('_module')->get('name')} {/if}
                                    {if ($RELATED_LINK->isPageLoadLink())}
                                    {if $RELATION_FIELD} data-name="{$RELATION_FIELD->getName()}" {/if}
                                {if $RELATED_MODULE->get('name') neq 'Emails'}data-url="{$RELATED_LINK->getUrl()}"{/if}
                                {/if}
                                {if $IS_SELECT_BUTTON neq true}name={if $RELATED_MODULE->get('name') eq 'Emails'}"composeEmail"{else}"addButton"{/if}{/if}>
                            {if $IS_SELECT_BUTTON eq false}<i class="icon-plus icon-white"></i>{/if}&nbsp;<strong>{$RELATED_LINK->getLabel()}</strong>
                            </button>
                        </div>
                    {/foreach}
                                    &nbsp;
                    </span>
                <div class="span4">
                    <span class="customFilterMainSpan row-fluid">
                        {if $CUSTOM_VIEWS|@count gt 0}
                            <input type="hidden" name="lockedViews" value='{$LOCKED_VIEWS}'>

                            <select id="recordsFilter" class="span12">
                                {foreach key=GROUP_LABEL item=GROUP_CUSTOM_VIEWS from=$CUSTOM_VIEWS}
                                    <optgroup label=' {if $GROUP_LABEL eq 'Mine'} &nbsp; {else if} {vtranslate($GROUP_LABEL)} {/if}' >
									{foreach item="CUSTOM_VIEW" from=$GROUP_CUSTOM_VIEWS}
                                        <option  data-editurl="{$CUSTOM_VIEW->getEditUrl()}" data-deleteurl="{$CUSTOM_VIEW->getDeleteUrl()}" data-approveurl="{$CUSTOM_VIEW->getApproveUrl()}" data-denyurl="{$CUSTOM_VIEW->getDenyUrl()}" data-editable="{$CUSTOM_VIEW->isEditable()}" data-deletable="{$CUSTOM_VIEW->isDeletable()}" data-pending="{$CUSTOM_VIEW->isPending()}" data-public="{$CUSTOM_VIEW->isPublic() && $USER_MODEL->isAdminUser()}" id="filterOptionId_{$CUSTOM_VIEW->get('cvid')}" value="{$CUSTOM_VIEW->get('cvid')}" data-id="{$CUSTOM_VIEW->get('cvid')}" {if $VIEWID neq '' && $VIEWID neq '0'  && $VIEWID == $CUSTOM_VIEW->getId()} selected="selected" {elseif ($VIEWID == '' or $VIEWID == '0')&& $CUSTOM_VIEW->isDefault() eq 'true'} selected="selected" {/if} class="filterOptionId_{$CUSTOM_VIEW->get('cvid')}">{if $CUSTOM_VIEW->get('viewname') eq 'All'}{vtranslate($CUSTOM_VIEW->get('viewname'), $MODULE)} {vtranslate($RELATED_MODULE_NAME, $MODULE)}{else}{vtranslate($CUSTOM_VIEW->get('viewname'), $MODULE)}{/if}{if $GROUP_LABEL neq 'Mine'} [ {$CUSTOM_VIEW->getOwnerName()} ]  {/if}</option>
                                    {/foreach}
								</optgroup>
                                {/foreach}
                            </select>
                            <span class="filterActionsDiv {if $MODULE neq 'Calendar'}hide{/if}">
							{if $MODULE eq 'Calendar'}<hr>{/if}
                                <ul class="filterActions {if $MODULE neq 'Calendar'}hide{/if}">
                                    <li data-value="create" id="createFilter" data-createurl="{$CUSTOM_VIEW->getCreateUrl()}"><i class="icon-plus-sign"></i> {vtranslate('LBL_CREATE_NEW_FILTER')}</li>
                                </ul>
                            </span>
                            <img class="filterImage" src="{'filter.png'|vimage_path}"
                                 style="display:none;height:13px;margin-right:2px;vertical-align: middle;">

                                        {else}

                            <input type="hidden" value="0" id="recordsFilter"/>
                        {/if}
                    </span>
                    <span class="hide filterActionImages pull-right">
                        <i title="{vtranslate('LBL_DENY', $MODULE)}" data-value="deny" class="icon-ban-circle alignMiddle denyFilter filterActionImage pull-right"></i>
                        <i title="{vtranslate('LBL_APPROVE', $MODULE)}" data-value="approve" class="icon-ok alignMiddle approveFilter filterActionImage pull-right"></i>
                        <i title="{vtranslate('LBL_DELETE', $MODULE)}" data-value="delete" class="icon-trash alignMiddle deleteFilter filterActionImage pull-right"></i>
                        <i title="{vtranslate('LBL_EDIT', $MODULE)}" data-value="edit" class="icon-pencil alignMiddle editFilter filterActionImage pull-right"></i>
                    </span>
                </div>
                {* Custom filter - end *}
                <div class="span3">
                    <div class="pull-right">
                        <span class="pageNumbers">
                            <span
                                class="pageNumbersText">{if !empty($RELATED_RECORDS)} {$PAGING->getRecordStartRange()} {vtranslate('LBL_to', $RELATED_MODULE->get('name'))} {$PAGING->getRecordEndRange()}{else}
                                    <span>&nbsp;</span>
                                {/if}</span>
                            <span class="icon-refresh totalNumberOfRecords cursorPointer{if empty($RELATED_RECORDS)} hide{/if}"></span>
                        </span>
                        <span class="btn-group">
                            <button class="btn" id="relatedListPreviousPageButton" {if !$PAGING->isPrevPageExists()} disabled {/if}
                                    type="button"><span class="icon-chevron-left"></span></button>
                            <button class="btn dropdown-toggle" type="button" id="relatedListPageJump"
                                    data-toggle="dropdown" {if $PAGE_COUNT eq 1} disabled {/if}>
                                <i class="vtGlyph vticon-pageJump" title="{vtranslate('LBL_LISTVIEW_PAGE_JUMP',$moduleName)}"></i>
                            </button>
                            <ul class="listViewBasicAction dropdown-menu" id="relatedListPageJumpDropDown">
                                <li>
                                    <span class="row-fluid">
                                        <span class="span3"><span class="pull-right">{vtranslate('LBL_PAGE',$moduleName)}</span></span>
                                        <span class="span4">
                                            <input type="text" id="pageToJump" class="listViewPagingInput"
                                                   value="{$PAGING->getCurrentPage()}"/>
                                        </span>
                                        <span class="span2 textAlignCenter">
                                            {vtranslate('LBL_OF',$moduleName)}
                                        </span>
                                        <span class="span3" id="totalPageCount">{$PAGE_COUNT}</span>
                                    </span>
                                </li>
                            </ul>
                            <button class="btn"
                                    id="relatedListNextPageButton" {if (!$PAGING->isNextPageExists()) or ($PAGE_COUNT eq 1)} disabled {/if}
                                    type="button"><span class="icon-chevron-right"></span></button>
                        </span>
                    </div>
                </div>
            </div>
        </div>
        <div class="contents-topscroll">
            <div class="topscroll-div">
                &nbsp;
            </div>
        </div>
        <div class="relatedContents contents-bottomscroll">
            <div class="bottomscroll-div" {if $RELATED_MODULE->get('name') eq 'Calendar'}style="padding-bottom:200px;"{/if}>
                {assign var=WIDTHTYPE value=$USER_MODEL->get('rowheight')}
                <table class="table table-bordered listViewEntriesTable">
                    <thead>
                    <tr class="listViewHeaders">
                        {if $RELATED_MODULE->get('name') eq 'Media'}
                            <th nowrap>&nbsp;</th>
                        {/if}
                        {foreach item=HEADER_FIELD from=$RELATED_HEADERS}
                            <th {if $HEADER_FIELD@last} colspan="2" {/if} nowrap>
                                {if $HEADER_FIELD->get('column') eq 'access_count' or $HEADER_FIELD->get('column') eq 'idlists' }
                                    <a href="javascript:void(0);"
                                       class="noSorting">{vtranslate($HEADER_FIELD->get('label'), $RELATED_MODULE->get('name'))}</a>
                                {else}
                                    <a href="javascript:void(0);" class="relatedListHeaderValues"
                                       data-nextsortorderval="{if $COLUMN_NAME eq $HEADER_FIELD->get('column')}{$NEXT_SORT_ORDER}{else}ASC{/if}"
                                       data-fieldname="{$HEADER_FIELD->get('column')}">
                                        {if $IS_PARENT && $HEADER_FIELD->get('column') eq 'contract_no'}
                                            {vtranslate('LBL_CONTRACTS_PARENT', $RELATED_MODULE->get('name'))}
                                        {else}
                                            {vtranslate($HEADER_FIELD->get('label'), $RELATED_MODULE->get('name'))}
                                        {/if}
                                        &nbsp;&nbsp;{if $COLUMN_NAME eq $HEADER_FIELD->get('column')}<img
                                        class="{$SORT_IMAGE}">{/if}
                                    </a>
                                {/if}
                            </th>
                        {/foreach}
                    </tr>
                    </thead>
                    <tr>
                        {if $RELATED_MODULE_NAME eq 'Media'}
                            <td>&nbsp;</td>
                        {/if}
                        {foreach item=LISTVIEW_HEADER from=$RELATED_HEADERS}
                            <td>
                                {assign var=FIELD_UI_TYPE_MODEL value=$LISTVIEW_HEADER->getUITypeModel()}
                                {include file=vtemplate_path($FIELD_UI_TYPE_MODEL->getListSearchTemplateName(),$RELATED_MODULE_NAME)
                                FIELD_MODEL= $LISTVIEW_HEADER SEARCH_INFO=$SEARCH_DETAILS[$LISTVIEW_HEADER->getName()] USER_MODEL=$USER_MODEL}
                            </td>
                        {/foreach}
                        <td>
                            <button class="btn pull-right" type="button" data-trigger="listSearch">{vtranslate('LBL_SEARCH', $MODULE )}</button>
                        </td>
                    </tr>
                    {foreach item=RELATED_RECORD from=$RELATED_RECORDS key=RELATED_KEY}
                        <tr class="listViewEntries" data-id='{$RELATED_RECORD->getId()}'
                            {if $RELATED_MODULE_NAME eq 'Calendar'}
                            {assign var=ACTIVITYTYPE value=Vtiger_Record_Model::getInstanceById($RELATED_RECORD->getId(), $RELATED_MODULE_NAME)->get('activitytype')}
                            {assign var=DETAILVIEWPERMITTED value=isPermitted($RELATED_MODULE->get('name'), 'DetailView', $RELATED_RECORD->getId())}
                            {if $DETAILVIEWPERMITTED eq 'yes'}
                                data-recordUrl='{$RELATED_RECORD->getDetailViewUrl()}'
                            {/if}
                                data-activity-type="{$ACTIVITYTYPE}"
                            {else}
                            data-recordUrl='{$RELATED_RECORD->getDetailViewUrl()}'
                            {/if}
                        >
                            {if $RELATED_MODULE_NAME eq 'Media'}
                                <td><img src="{$RELATED_RECORD->get('thumbnail')}" /></td>
                            {/if}
                            {foreach item=HEADER_FIELD from=$RELATED_HEADERS}
                                {assign var=RELATED_HEADERNAME value=$HEADER_FIELD->get('name')}
                            <td class="{$WIDTHTYPE}" data-field-type="{$HEADER_FIELD->getFieldDataType()}" nowrap>
                                {if $HEADER_FIELD->isNameField() eq true or $HEADER_FIELD->get('uitype') eq '4'}
                                    <a href="{$RELATED_RECORD->getDetailViewUrl()}">{$RELATED_RECORD->getDisplayValue($RELATED_HEADERNAME)}</a>
                                {elseif $RELATED_HEADERNAME eq 'access_count'}
                                    {$RELATED_RECORD->getAccessCountValue($PARENT_RECORD->getId())}
                                {elseif $RELATED_HEADERNAME eq ''}
                                    {$RELATED_RECORD->getDisplayValue($RELATED_HEADERNAME)}
                                {elseif $RELATED_HEADERNAME eq 'listprice' || $RELATED_HEADERNAME eq 'unit_price'}
                                    {CurrencyField::convertToUserFormat($RELATED_RECORD->get($RELATED_HEADERNAME), null, true)}
                                    {if $RELATED_HEADERNAME eq 'listprice'}
                                        {assign var="LISTPRICE" value=CurrencyField::convertToUserFormat($RELATED_RECORD->get($RELATED_HEADERNAME), null, true)}
                                    {/if}
                                {elseif
                                ($RELATED_HEADERNAME eq 'filename') ||
                                ($RELATED_HEADERNAME eq 'nat_account_no') ||
                                ($RELATED_HEADERNAME eq 'billing_apn')
                                }
                                    {$RELATED_RECORD->get($RELATED_HEADERNAME)}
                                {else if $IS_CALENDAR_STATUS_EDITABLE eq 'true' && $RELATED_HEADERNAME eq 'taskstatus' && isPermitted('Calendar', 'EditView', $RELATED_RECORD->getId()) eq 'yes'}

                                            {if $ACTIVITYTYPE eq 'Task'}
                                                {assign var=CALENDAR_FIELD_NAME value='taskstatus'}
                                                {assign var=CALENDAR_STATUS_VALUES value=$TASKSTATUS_ARRAY}
                                            {else}
                                                {assign var=CALENDAR_FIELD_NAME value='eventstatus'}
                                                {assign var=CALENDAR_STATUS_VALUES value=$EVENTSTATUS_ARRAY}
                                            {/if}
                                                <select class="chzn-select taskstatus span2" data-recordid="{$RELATED_RECORD->getId()}" data-fieldname="{$CALENDAR_FIELD_NAME}">
                                                    {foreach key=KEY item=VALUE from=$CALENDAR_STATUS_VALUES}
                                                        <option value="{$KEY}"  {if $RELATED_RECORD->get($RELATED_HEADERNAME) eq $VALUE}selected{/if}>{$VALUE}</option>
                                                    {/foreach}
                                                </select>
                                {else}
                                    {$RELATED_RECORD->getDisplayValue($RELATED_HEADERNAME)}
                                {/if}
                                {if $HEADER_FIELD@last}
                                    </td>
                                    <td nowrap class="{$WIDTHTYPE}">
                                        <div class="pull-right actions">
                                    <span class="actionImages">
                                        {if $RELATED_MODULE_NAME eq 'Calendar'}
											{assign var=IS_EDITABLE_CALENDAR value=isPermitted($RELATED_MODULE_NAME, 'EditView', $RELATED_RECORD->getId())}
                                            {if $IS_EDITABLE_CALENDAR eq 'yes' && $RELATED_RECORD->get('taskstatus') neq 'Held' && $RELATED_RECORD->get('taskstatus') neq 'Completed'}{* && $USER_MODEL->getExtraPermission($RELATED_KEY)*}
                                                <a class="markAsHeld">
                                                    <i title="{if $ACTIVITYTYPE neq 'Task'}{vtranslate('LBL_MARK_AS_HELD', $MODULE)}{else}{vtranslate('LBL_MARK_COMPLETED', $MODULE)}{/if}" class="icon-ok alignMiddle"></i>
                                                </a>
                                                &nbsp;
                                            {/if}
                                            {if $IS_EDITABLE_CALENDAR eq 'yes' && $RELATED_RECORD->get('taskstatus') eq 'Held'}{* && $USER_MODEL->getExtraPermission($RELATED_KEY)*}
                                            <a class="holdFollowupOn">
                                                <i title="{vtranslate('LBL_HOLD_FOLLOWUP_ON', "Events")}" class="icon-flag alignMiddle"></i>
                                            </a>
                                            &nbsp;
                                        {/if}
                                            {if $DETAILVIEWPERMITTED eq 'yes'}
                                            <a href="{$RELATED_RECORD->getFullDetailViewUrl()}">
                                                <i title="{vtranslate('LBL_SHOW_COMPLETE_DETAILS', $MODULE)}" class="icon-th-list alignMiddle"></i>
                                            </a>
                                            &nbsp;
                                        {/if}
                                        {elseif $RELATED_MODULE_NAME neq 'TariffServices'}

                                            <a href="{$RELATED_RECORD->getFullDetailViewUrl()}">
                                                <i title="{vtranslate('LBL_SHOW_COMPLETE_DETAILS', $MODULE)}" class="icon-th-list alignMiddle"></i>
                                            </a>
                                            &nbsp;
                                        {/if}
                                        {if $IS_EDITABLE}{* && $USER_MODEL->getExtraPermission($RELATED_KEY)*}
                                            {if $RELATED_MODULE_NAME eq 'PriceBooks'}
                                                <a data-url="index.php?module=PriceBooks&view=ListPriceUpdate&record={$PARENT_RECORD->getId()}&relid={$RELATED_RECORD->getId()}&currentPrice={$LISTPRICE}"
                                                   class="editListPrice cursorPointer"
                                                   data-related-recordid='{$RELATED_RECORD->getId()}'
                                                   data-list-price={$LISTPRICE}>
                                                    <i class="icon-pencil alignMiddle" title="{vtranslate('LBL_EDIT', $MODULE)}"></i>
                                                </a>

                                            {elseif $RELATED_MODULE_NAME eq 'Calendar'}
                                                {if isPermitted($RELATED_MODULE->get('name'), 'EditView', $RELATED_RECORD->getId()) eq 'yes'}
                                                    <a href='{$RELATED_RECORD->getEditViewUrl()}'>
                                                        <i title="{vtranslate('LBL_EDIT', $MODULE)}" class="icon-pencil alignMiddle"></i>
                                                    </a>
                                                {/if}
                                            {elseif $RELATED_MODULE_NAME eq 'Estimates'}
														<a href="index.php?module={$RELATED_MODULE->get('name')}&view=Edit&record={$RELATED_RECORD->getId()}&isDuplicate=true">
															<img title="Duplicate" src="layouts/vlayout/skins/images/duplicate.png" class="alignMiddle"/>
														</a>
											
                                            {elseif $RELATED_MODULE_NAME neq 'TariffServices'}
												{if isPermitted($RELATED_MODULE->get('name'), 'EditView', $RELATED_RECORD->getId()) eq 'yes'}
													<a href='{$RELATED_RECORD->getEditViewUrl()}'>
														<i title="{vtranslate('LBL_EDIT', $MODULE)}" class="icon-pencil alignMiddle"></i>
													</a>
													&nbsp;
													{if $RELATED_MODULE->get('name') eq 'TariffServices'}
														<a href="{$RELATED_RECORD->getDuplicateUrl()}">
															<img title="Duplicate" src="layouts/vlayout/skins/images/duplicate.png" class="alignMiddle"/>
														</a>
													{else if $RELATED_MODULE->get('name') eq 'Estimates'}
														<a href="{$RELATED_RECORD->getEditViewUrl()}&isDuplicate=true">
															<img title="Duplicate" src="layouts/vlayout/skins/images/duplicate.png" class="alignMiddle"/>
														</a>
													{else}
														<span class='duplicateSpan' data-url="{$RELATED_RECORD->getDuplicateUrl()}">
															<img title="Duplicate" src="layouts/vlayout/skins/images/duplicate.png" class="alignMiddle"/>
														</span>
													{/if}
													&nbsp;
												{/if}
											{elseif $RELATED_MODULE_NAME eq 'Calendar'}
												{assign var=CAN_EDIT value=isPermitted($RELATED_MODULE_NAME, 'EditView', $RELATED_RECORD->getId())}
												{if $CAN_EDIT eq 'yes'}
													<a href='{$RELATED_RECORD->getEditViewUrl()}'>
														<i title="{vtranslate('LBL_EDIT', $MODULE)}" class="icon-pencil alignMiddle"></i>
													</a>
													&nbsp;
												{/if}
                                            {/if}
                                        {/if}
                                        {if $IS_DELETABLE}{* && $USER_MODEL->getExtraPermission($RELATED_KEY)*}
                                            {if $RELATED_MODULE_NAME eq 'Calendar'}
												{assign var=IS_DELATABLE_CALENDAR value=isPermitted($RELATED_MODULE_NAME, 'Delete', $RELATED_RECORD->getId())}
                                                {if $IS_DELATABLE_CALENDAR eq 'yes'}
                                                    <a class="relationDelete">
                                                        <i title="{vtranslate('LBL_DELETE', $MODULE)}" class="icon-trash alignMiddle"></i>
                                                    </a>
                                                {/if}
                                            {else}
												{if isPermitted($RELATED_MODULE->get('name'), 'Delete', $RELATED_RECORD->getId()) eq 'yes'}
													<a class="relationDelete {if $RELATED_MODULE->get('name') eq 'Estimates' && $RELATED_RECORD->get('quotestage') eq 'Accepted'}hide{/if}">
														<i title="{vtranslate('LBL_DELETE', $MODULE)}" class="icon-trash alignMiddle"></i>
													</a>
												{/if}
                                            {/if}
                                        {/if}
                                    </span>
                                        </div>
                                    </td>
                                {/if}
                                </td>
                            {/foreach}
                        </tr>
                    {/foreach}
                </table>
            </div>
        </div>
    </div>
{/strip}
