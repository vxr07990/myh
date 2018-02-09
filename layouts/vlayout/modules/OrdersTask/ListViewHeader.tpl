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
<div class="listViewPageDiv" style="margin:0;">
    <div class="listViewTopMenuDiv noprint">
        <div class="listViewActionsDiv row-fluid">
            <span class="btn-toolbar span4">
                {if $LocalDispatch neq 'true' && $LocalDispatchActuals neq 'true'}
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
                            <button id="{$MODULE}_listView_basicAction_{Vtiger_Util_Helper::replaceSpaceWithUnderScores($LISTVIEW_BASICACTION->getLabel())}" class="btn addButton" {if stripos($LISTVIEW_BASICACTION->getUrl(), 'javascript:')===0} onclick='{$LISTVIEW_BASICACTION->getUrl()|substr:strlen("javascript:")};'{else} onclick='window.location.href = "{$LISTVIEW_BASICACTION->getUrl()}"'{/if}><i class="icon-plus"></i>&nbsp;<strong>{vtranslate($LISTVIEW_BASICACTION->getLabel(), $MODULE)}</strong></button>
                        </span>
                    {/foreach}
                {else}
					{if $LocalDispatchActuals neq 'true'}
						<span class="btn-group listViewMassActions">
							{if count($LISTVIEW_MASSACTIONS) gt 0 || $LISTVIEW_LINKS['LISTVIEW']|@count gt 0}
								<button class="btn dropdown-toggle" data-toggle="dropdown"><strong>{vtranslate('LBL_ACTIONS', $MODULE)}</strong>&nbsp;&nbsp;<i class="caret"></i></button>
								<ul class="dropdown-menu">
									{foreach item="LISTVIEW_MASSACTION" from=$LISTVIEW_MASSACTIONS name=actionCount}
										<li id="{$MODULE}_listView_massAction_{Vtiger_Util_Helper::replaceSpaceWithUnderScores($LISTVIEW_MASSACTION->getLabel())}"><a href="javascript:void(0);" {if stripos($LISTVIEW_MASSACTION->getUrl(), 'javascript:')===0}{if Vtiger_Util_Helper::replaceSpaceWithUnderScores($LISTVIEW_MASSACTION->getLabel()) eq 'LBL_EDITFILTER'}onclick='OrdersTask_LocalDispatch_Js.{$LISTVIEW_MASSACTION->getUrl()|substr:strlen("javascript:")};'{else}onclick='{$LISTVIEW_MASSACTION->getUrl()|substr:strlen("javascript:")};'{/if}{else} onclick="Vtiger_List_Js.triggerMassAction('{$LISTVIEW_MASSACTION->getUrl()}')"{/if} >{vtranslate($LISTVIEW_MASSACTION->getLabel(), $MODULE)}</a></li>
									{/foreach}
								</ul>
							{/if}
						</span>
					{/if}
                {/if}
            </span>
            <span class="{if $LocalDispatchActuals neq 'true'}btn-toolbar {/if}span4">
            {if $LocalDispatchActuals eq 'true'}
            <div class="localDispatch row" style="padding: 0px;">
                <div class="span5" style="padding-left:1%;">&nbsp;
                    <div style="display:none">
                    <select id="associated_filter" class="chzn-select" >  <!-- Feature not yet implemented -->
                        <option value="all" {if 'all' eq $TASK_STATUS} selected {/if}>Nothing</option>
                        <option value="assigned" {if 'assigned' eq $TASK_STATUS} selected {/if}>Lead</option>
                        <option value="unassigned" {if 'unassigned' eq $TASK_STATUS} selected {/if}>Vehicle</option>
                    </select>
                    </div>
                </div>

                <div class="span7">
                    <div class="input-append row-fluid">
                        <div class="date span5">
                            <input id="filter_date_from" type="text" class="span2 dateField" name="filter_from" data-date-format="{$CURRENT_USER_MODEL->get('date_format')}" value="{$DATE_FROM}">
                            <span class="add-on"><i class="icon-calendar"></i></span>
                        </div>
                        <div class="date span5">
                            <input id="filter_date_to" type="text" class="span2 dateField" name="filter_to" data-date-format="{$CURRENT_USER_MODEL->get('date_format')}" value="{$DATE_TO}">
                            <span class="add-on"><i class="icon-calendar"></i></span>
                        </div>
                    </div>
                </div>
            </div>
        {/if}
                <span class="customFilterMainSpan btn-group">
                    {if $CUSTOM_VIEWS|@count gt 0}
                        <input type="hidden" name="lockedViews" value='{$LOCKED_VIEWS}'>
                        <select id="customFilter" style="width:350px;">
                            {foreach key=GROUP_LABEL item=GROUP_CUSTOM_VIEWS from=$CUSTOM_VIEWS}

                                <optgroup label=' {if $GROUP_LABEL eq 'Mine'} &nbsp; {else if} {vtranslate($GROUP_LABEL)} {/if}' >
                                    {foreach item="CUSTOM_VIEW" from=$GROUP_CUSTOM_VIEWS}
                                        <option  data-editurl="{$CUSTOM_VIEW->getEditUrl()}" data-deleteurl="{$CUSTOM_VIEW->getDeleteUrl()}" data-approveurl="{$CUSTOM_VIEW->getApproveUrl()}" data-denyurl="{$CUSTOM_VIEW->getDenyUrl()}" data-editable="false" data-deletable="false" data-pending="{$CUSTOM_VIEW->isPending()}" data-public="{$CUSTOM_VIEW->isPublic() && $CURRENT_USER_MODEL->isAdminUser()}" id="filterOptionId_{$CUSTOM_VIEW->get('cvid')}" value="{$CUSTOM_VIEW->get('cvid')}" data-id="{$CUSTOM_VIEW->get('cvid')}" {if $VIEWID neq '' && $VIEWID neq '0'  && $VIEWID == $CUSTOM_VIEW->getId()} selected="selected" {elseif ($VIEWID == '' or $VIEWID == '0')&& $CUSTOM_VIEW->isDefault() eq 'true'} selected="selected" {/if} class="filterOptionId_{$CUSTOM_VIEW->get('cvid')}">{if $CUSTOM_VIEW->get('viewname') eq 'All'}{vtranslate($CUSTOM_VIEW->get('viewname'), $MODULE)} {vtranslate($MODULE, $MODULE)}{else}{vtranslate($CUSTOM_VIEW->get('viewname'), $MODULE)}{/if}{if $GROUP_LABEL neq 'Mine'} [ {$CUSTOM_VIEW->getOwnerName()} ]  {/if}</option>
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
                        <input type="hidden" data-value="create" id="createFilter" data-createurl="{$CUSTOM_VIEW->getCreateUrl()}">
                        <!--span class="filterActionsDiv hide">
                            <ul class="filterActions hide">
                                <li data-value="create" id="createFilter" data-createurl="{$CUSTOM_VIEW->getCreateUrl()}"><i class="icon-plus-sign"></i> {vtranslate('LBL_CREATE_NEW_FILTER')}</li>
                            </ul>
                        </span-->
                        <img class="filterImage" src="{'filter.png'|vimage_path}" style="display:none;height:13px;margin-right:2px;vertical-align: middle;">
                    {else}
                        <input type="hidden" value="0" id="customFilter" />
                    {/if}
                </span>
            </span>
            <span class="hide filterActionImages pull-right">
                <i title="{vtranslate('LBL_DENY', $MODULE)}" data-value="deny" class="icon-ban-circle alignMiddle denyFilter filterActionImage pull-right"></i>
                <i title="{vtranslate('LBL_APPROVE', $MODULE)}" data-value="approve" class="icon-ok alignMiddle approveFilter filterActionImage pull-right"></i>
                <i title="{vtranslate('LBL_DELETE', $MODULE)}" data-value="delete" class="icon-trash alignMiddle deleteFilter filterActionImage pull-right"></i>
                <i title="{vtranslate('LBL_EDIT', $MODULE)}" data-value="edit" class="icon-pencil alignMiddle editFilter filterActionImage pull-right"></i>
            </span>
            <span class="span4 btn-toolbar">
                {if $LocalDispatch eq 'true'}<div class="pull-right" style="margin-left:15px;"><div id="customToggleButton" class="toggleButton" style="position: relative; top:-7px" title="Hide/Show Right Panel" style="top:0px;"><i id="ctButtonImage" class="icon-chevron-right"></i></div></div>{/if}
		{include file='ListViewActions.tpl'|@vtemplate_path}
            </span>
        </div>
            {if $LocalDispatch eq 'true'}
		<div class="localDispatch row">
			<div class="input-append row-fluid">
				<div style="width:50%;float:left">
					<div class="date" style="width:50%;margin:0 auto;">
						<input id="filter_date_from" type="text" class="span2 dateField" name="filter_from" data-date-format="{$CURRENT_USER_MODEL->get('date_format')}" value="{$DATE_FROM}">
						<span class="add-on"><i class="icon-calendar"></i></span>
					</div>
				</div>
				<div style="width:50%;float:left">
					<div class="date" style="width:50%;margin:0 auto;">
						<input id="filter_date_to" type="text" class="span2 dateField" name="filter_to" data-date-format="{$CURRENT_USER_MODEL->get('date_format')}" value="{$DATE_TO}">
						<span class="add-on"><i class="icon-calendar"></i></span>
					</div>
				</div>
			</div>
		</div>
	{/if}
</div>
{/strip}
