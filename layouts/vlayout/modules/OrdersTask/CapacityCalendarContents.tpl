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
    {literal}
        <style type="text/css">
			.fc-event-skin {
			  color: #000;
			}
			.fc-content {
				background-color: transparent !important;
				margin-top: auto !important;
			}
			.fc-day-grid-container.fc-scroller {
				height: auto!important;
				overflow-y: auto;
			}
			.fc-day-grid-event .fc-content {
				white-space: normal;
				overflow: hidden;
			}
                        .fc-toolbar.fc-header-toolbar{
                                margin-bottom: 0;
                        }
                        .fc-widget-content.vgs-highlight:before {
                            content: '\00a0';
                            border: 3px solid #000;
                            height: 100%;
                            width: calc(100% - 6px);
                            display: block;
                            position: relative;
			}
                        .capacity-calendar.fc-widget-header {
                            padding: 2px 0px !important;
                            color: #666;
                            background: #eee;
                            border-color: #ccc;
                        }
						.fc-day-number{
							font-size:1.5em;	
						}
						.fc-event-container p{
							margin:0;
						}
</style>
    {/literal}

    <div class="capacity-calendar" style="padding: 1% 3% 1% 3%;">
        <div class="row-fluid">
            <div class="span12" style="min-width: 850px; margin-right: 2.5%;/* margin-bottom: 2%; */">
                <span id="actionsButton" class="btn-toolbar hide">
                    <span class="btn-group listViewMassActions">
                        <button class="btn dropdown-toggle" data-toggle="dropdown"><strong>{vtranslate('LBL_ACTIONS', $MODULE)}</strong>&nbsp;&nbsp;<i class="caret"></i></button>
                        <ul class="dropdown-menu">
                            <li id="open-oportunity"><a href="javascript:void(0);"  onclick="window.open('index.php?module=Opportunities&view=Edit&fromcapacity=true','_blank')" >{vtranslate('Create Opportunity', $MODULE)}</a></li>
                            <li id="open-orders"><a href="javascript:void(0);"  onclick="window.open('index.php?module=Orders&view=Edit&fromcapacity=true','_blank')" >{vtranslate('Create Order', $MODULE)}</a></li>
                            <li id="open-orderstask"><a href="javascript:void(0);"  onclick="OrdersTask_CapacityCalendarView_Js.createOrderTask();" >{vtranslate('Create Local Operations Task', $MODULE)}</a></li>
                            <li id="open-localdispatch"><a href="javascript:void(0);"  onclick="OrdersTask_CapacityCalendarView_Js.openLocalDispatch()" >{vtranslate('Open Local Dispatch', $MODULE)}</a></li>
                            <li id="open-settings"><a onclick="window.open('index.php?module=OrdersTask&view=CalendarSettings','_blank')" >Calendar Settings</a></li>
                            <li id="OrdersTask_listView_massAction_LBL_EDITFILTER"><a href="javascript:void(0);" onclick="OrdersTask_CapacityCalendarView_Js.triggerEditFilter();">Edit Current Filter</a></li>
                            <li id="OrdersTask_listView_massAction_LBL_DELETEFILTER"><a href="javascript:void(0);" onclick="triggerDeleteFilter();">Delete Current Filter</a></li>
                            <li id="OrdersTask_listView_massAction_LBL_CREATEFILTER"><a href="javascript:void(0);" onclick="triggerCreateFilter();">Create New Filter</a></li>
                        </ul>
                    </span>
                </span>
                <table class="table">
                    <tr>
                        <td style="border-top:0px;width:40%;">
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
                <i title="{vtranslate('LBL_DELETE', $MODULE)}" data-value="delete" class="icon-trash alignMiddle deleteFilter filterActionImage pull-right hide"></i>
                <i title="{vtranslate('LBL_EDIT', $MODULE)}" data-value="edit" class="icon-pencil alignMiddle editFilter filterActionImage pull-right hide"></i>
            </span>
                    </td >
                    <td style="border-top:0px;width:30%;">
                    <span style="">
                        <span>Resource:&nbsp;</span>
                        <select id="resource">
                            <option value="employees" selected>{vtranslate('Employees', 'Employees')}</option>
                            <option value="vehicles">{vtranslate('SINGLE_Vehicles', 'Vehicles')}</option>
                        </select>
                    </span>
                    </td>
                    <td style="border-top:0px;width:30%;">
                    <span id="resource_name">
                        Personnel Role:&nbsp;
                    </span>
                    <select id="resource_type">
                    </select>
                    </td>
                </tr>
                </table>
            </div>
            <div class="span12" style="min-width: 850px; margin-right: 2.5%; /*margin-bottom: 2.5%;*/margin-left:0%; margin-right: 0%;">


            <input type="hidden" id="currentView" value="{$smarty.request.view}" />
            <input type="hidden" id="activity_view" value="{$USER_MODEL->get('activity_view')}" />
            <input type="hidden" id="time_format" value="{$USER_MODEL->get('hour_format')}" />
            <input type="hidden" id="start_hour" value="{$USER_MODEL->get('start_hour')}" />
            <input type="hidden" id="date_format" value="{$USER_MODEL->get('date_format')}" />

            <div id="calendarview" class=""></div>
        </div>
        <div class="span12 row-fluid" style="min-width: 850px; margin-left: 0px;">
            <div class="resource_container">
                <table class="table table-bordered listViewEntriesTable">
                    <thead>
                        <tr class="listViewHeaders">
                            <th>
                                {vtranslate('LBL_DAILY_SUMMARY_FOR_DATE',$MODULE)}
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td style="padding: 4%;"> {vtranslate('Please choose a day to view the daily summary',$MODULE)}</td>
                        </tr>
                    </tbody>
                </table><br>
                <table class="table table-bordered listViewEntriesTable">
                    <thead>
                        <tr class="listViewHeaders">
                            <th>
                                {vtranslate('LBL_ORDER_FOR_DATE',$MODULE)}
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td style="padding: 4%;"> {vtranslate('Please choose a day to view the Local Operations Task list',$MODULE)}</td>
                        </tr>
                    </tbody>
                </table>
            </div>

        </div>
        <div class="span12 row-fluid" style="min-width: 850px; margin-left: 0px;margin-top: 1%;text-align: right;">

        </div>
    </div>
    </div>
</div>
</div>
</div>


{/strip}
