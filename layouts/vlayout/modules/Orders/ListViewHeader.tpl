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


    <div class="listViewPageDiv">
        <div class="listViewTopMenuDiv noprint">
            <div class="listViewActionsDiv row-fluid">
                <span class="btn-toolbar span4">
                    <span class="btn-group listViewMassActions">
                        {if $LDDList neq 'true'}
                            {if count($LISTVIEW_MASSACTIONS) gt 0 || $LISTVIEW_LINKS['LISTVIEW']|@count gt 0}
                                <button class="btn dropdown-toggle" data-toggle="dropdown"><strong>{vtranslate('LBL_ACTIONS', $MODULE)}</strong>&nbsp;&nbsp;<i class="caret"></i></button>
                                <ul class="dropdown-menu">
                                    {foreach item="LISTVIEW_MASSACTION" from=$LISTVIEW_MASSACTIONS name=actionCount}
                                        <li id="{$MODULE}_listView_massAction_{Vtiger_Util_Helper::replaceSpaceWithUnderScores($LISTVIEW_MASSACTION->getLabel())}" class="{if $LISTVIEW_MASSACTION->getLabel() eq 'LBL_DELETE'}hide{/if}"><a href="javascript:void(0);" {if stripos($LISTVIEW_MASSACTION->getUrl(), 'javascript:')===0}onclick='{$LISTVIEW_MASSACTION->getUrl()|substr:strlen("javascript:")};'{else} onclick="Vtiger_List_Js.triggerMassAction('{$LISTVIEW_MASSACTION->getUrl()}')"{/if} >{vtranslate($LISTVIEW_MASSACTION->getLabel(), $MODULE)}</a></li>
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
                        {else}
                            <button class="btn dropdown-toggle" data-toggle="dropdown"><strong>{vtranslate('LBL_ACTIONS', $MODULE)}</strong>&nbsp;&nbsp;<i class="caret"></i></button>
                            <ul class="dropdown-menu">
                                <li id="add-trip"><a href="javascript:void(0);" onclick="Orders_LDDList_Js.addToTrip();">{vtranslate('Add to Trip', $MODULE)}</a></li>
                                <li id="create-trip"><a href="javascript:void(0);"  onclick="Orders_LDDList_Js.createTrip();" >{vtranslate('Create Trip', $MODULE)}</a></li>
                                <li class="divider"></li>
                                {foreach item="LISTVIEW_MASSACTION" from=$LISTVIEW_MASSACTIONS name=actionCount}
                                        <li id="{$MODULE}_listView_massAction_{Vtiger_Util_Helper::replaceSpaceWithUnderScores($LISTVIEW_MASSACTION->getLabel())}" class="{if $LISTVIEW_MASSACTION->getLabel() eq 'LBL_DELETE'}hide{/if}"><a href="javascript:void(0);" {if stripos($LISTVIEW_MASSACTION->getUrl(), 'javascript:')===0}onclick='{$LISTVIEW_MASSACTION->getUrl()|substr:strlen("javascript:")};'{else} onclick="Vtiger_List_Js.triggerMassAction('{$LISTVIEW_MASSACTION->getUrl()}')"{/if} >{vtranslate($LISTVIEW_MASSACTION->getLabel(), $MODULE)}</a></li>
                                {/foreach}
                            </ul>
                        {/if}

                    </span>
                    {if $LDDList neq 'true'}
                        {foreach item=LISTVIEW_BASICACTION from=$LISTVIEW_LINKS['LISTVIEWBASIC']}
                            <span class="btn-group">
                                <button id="{$MODULE}_listView_basicAction_{Vtiger_Util_Helper::replaceSpaceWithUnderScores($LISTVIEW_BASICACTION->getLabel())}" class="btn addButton" {if stripos($LISTVIEW_BASICACTION->getUrl(), 'javascript:')===0} onclick='{$LISTVIEW_BASICACTION->getUrl()|substr:strlen("javascript:")};'{else} onclick='window.location.href = "{$LISTVIEW_BASICACTION->getUrl()}"'{/if}><i class="icon-plus"></i>&nbsp;<strong>{vtranslate($LISTVIEW_BASICACTION->getLabel(), $MODULE)}</strong></button>
                            </span>
                        {/foreach}
                    {/if}
                </span>
                <span class="btn-toolbar span4">
                    <span class="customFilterMainSpan btn-group">
                        {if $CUSTOM_VIEWS|@count gt 0}
                            <input type="hidden" name="lockedViews" value='{$LOCKED_VIEWS}'>
                            <select id="customFilter" style="width:350px;">
                                {foreach key=GROUP_LABEL item=GROUP_CUSTOM_VIEWS from=$CUSTOM_VIEWS}
                                    <optgroup label=' {if $GROUP_LABEL eq 'Mine'} &nbsp; {else if} {vtranslate($GROUP_LABEL)} {/if}' >
                                        {foreach item="CUSTOM_VIEW" from=$GROUP_CUSTOM_VIEWS}
                                            <option  data-editurl="{$CUSTOM_VIEW->getEditUrl()}" data-deleteurl="{$CUSTOM_VIEW->getDeleteUrl()}" data-approveurl="{$CUSTOM_VIEW->getApproveUrl()}" data-denyurl="{$CUSTOM_VIEW->getDenyUrl()}" data-editable="{$CUSTOM_VIEW->isEditable()}" data-deletable="{$CUSTOM_VIEW->isDeletable()}" data-pending="{$CUSTOM_VIEW->isPending()}" data-public="{$CUSTOM_VIEW->isPublic() && $CURRENT_USER_MODEL->isAdminUser()}" id="filterOptionId_{$CUSTOM_VIEW->get('cvid')}" value="{$CUSTOM_VIEW->get('cvid')}" data-id="{$CUSTOM_VIEW->get('cvid')}" {if $VIEWID neq '' && $VIEWID neq '0'  && $VIEWID == $CUSTOM_VIEW->getId()} selected="selected" {elseif ($VIEWID == '' or $VIEWID == '0')&& $CUSTOM_VIEW->isDefault() eq 'true'} selected="selected" {/if} class="filterOptionId_{$CUSTOM_VIEW->get('cvid')}">{if $CUSTOM_VIEW->get('viewname') eq 'All'}{vtranslate($CUSTOM_VIEW->get('viewname'), $MODULE)} {vtranslate($MODULE, $MODULE)}{else}{vtranslate($CUSTOM_VIEW->get('viewname'), $MODULE)}{/if}{if $GROUP_LABEL neq 'Mine'} [ {$CUSTOM_VIEW->getOwnerName()} ]  {/if}</option>
                                        {/foreach}
                                    </optgroup>
                                {/foreach}
                                {if $FOLDERS neq ''}
                                    <optgroup id="foldersBlock" label='{vtranslate('LBL_FOLDERS', $MODULE)}' >
                                        {foreach item=FOLDER from=$FOLDERS}
                                            <option data-foldername="{$FOLDER->getName()}" {if decode_html($FOLDER->getName()) eq $FOLDER_NAME} selected=""{/if} data-folderid="{$FOLDER->get('folderid')}" data-deletable="{!($FOLDER->hasDocuments())}" class="filterOptionId_folder{$FOLDER->get('folderid')} folderOption{if $FOLDER->getName() eq 'Default'} defaultFolder {/if}" id="filterOptionId_folder{$FOLDER->get('folderid')}" data-id="{$DEFAULT_CUSTOM_FILTER_ID}">{$FOLDER->getName()}</option>
                                        {/foreach}
                                    </optgroup>
                                {/if}
                            </select>
                            <span class="filterActionsDiv hide">
                                <ul class="filterActions hide">
                                    <li data-value="create" id="createFilter" data-createurl="{$CUSTOM_VIEW->getCreateUrl()}"><i class="icon-plus-sign"></i> {vtranslate('LBL_CREATE_NEW_FILTER')}</li>
                                </ul>
                            </span>
                            <img class="filterImage" src="{'filter.png'|vimage_path}" style="display:none;height:13px;margin-right:2px;vertical-align: middle;">
                        {else}
                            <input type="hidden" value="0" id="customFilter" />
                        {/if}
                    </span>
                </span>
                <span class="hide filterActionImages pull-right">
                    <i title="{vtranslate('LBL_DENY', $MODULE)}" data-value="deny" class="icon-ban-circle alignMiddle denyFilter filterActionImage pull-right"></i>
                    <i title="{vtranslate('LBL_APPROVE', $MODULE)}" data-value="approve" class="icon-ok alignMiddle approveFilter filterActionImage pull-right"></i>
                    <i title="{vtranslate('LBL_DELETE', $MODULE)}" data-value="delete" class="icon-trash alignMiddle deleteFilter filterActionImage pull-right hide"></i>
                    <i title="{vtranslate('LBL_EDIT', $MODULE)}" data-value="edit" class="icon-pencil alignMiddle editFilter filterActionImage pull-right hide"></i>
                </span>
                <span class="span4 btn-toolbar">
                    {include file='ListViewActions.tpl'|@vtemplate_path:$MODULE}
                </span>
            </div>
            {if $LDDList eq 'true'}
                <div class="ListViewLDDActions row-fluid">
                    <div class="span3">
                        <div style="margin-bottom: 3%;margin-top: 2%;">{vtranslate('Origin Zone', $MODULE)}</div>
                        <div>
                            <select name="change_originzone" id="origin_zone" class="changezone chzn-select" multiple>
                                <option value="all">All</option>
                                {foreach from=$ZONE_ARR key=ZONE_ID item=ZONE}
                                    <option value="{$ZONE_ID}" {if $ZONE eq $ORIG_ZONE_SELECTED} selected {/if}>{$ZONE}</option>
                                {/foreach}
                            </select>
                        </div>

                    </div>
                    <div class="span3">
                        <div style="margin-bottom: 3%;margin-top: 2%;">Dest Zone</div>
                        <div>
                            <select name="change_destzone" id="destination_zone" class="changezone chzn-select" multiple>
                                <option value="all">All</option>
                                {foreach from=$ZONE_ARR item=ZONE}
                                    <option value="{$ZONE}" {if $ZONE eq $DEST_ZONE_SELECTED} selected {/if}>{$ZONE}</option>
                                {/foreach}
                            </select>
                        </div>

                    </div>
                    <div class="span1"> </div>
                    <div class="span3 row-fluid date">
                        <div style="margin-bottom: 3%;margin-top: 2%;"><span>Time Range</span></div>
                        <div><input type="text" id="filter_dates" name="filter_dates" class="span2 dateField" data-date-format="{$CURRENT_USER_MODEL->get('date_format')}" data-calendar-type="range" value="" ><span class="add-on"><i class="icon-calendar"></i></span></div>
                    </div>
                    <div class="span2" style="margin-top: 3%;">
                        <span class="btn" id="ldd_filter">Filter</span>
                        <span  id="ldd_clear_filter" style="margin-left: 5%;cursor: pointer;">Clear Filter</span>
                    </div>

                </div>
            {/if}
        </div>
        <div class="listViewContentDiv" id="listViewContents">
        {/strip}
