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
    <input type="hidden" value="{$TAKS_WITH_RESOURCES}" id="task_with_resources">

    <div class="listViewEntriesDiv contents-bottomscroll" style="min-height: 0px;max-height: 450px;">
        <div class="bottomscroll-div">
            <span class="listViewLoadingImageBlock hide modal noprint" id="loadingListViewModal">
                <img class="listViewLoadingImage" src="{vimage_path('loading.gif')}" alt="no-image" title="{vtranslate('LBL_LOADING', $MODULE)}"/>
                <p class="listViewLoadingMsg">{vtranslate('LBL_LOADING_LISTVIEW_CONTENTS', $MODULE)}........</p>
            </span>
            <table class="table table-bordered listViewEntriesTable" id="orders_table">
                <thead>
                    <tr class="listViewHeaders">
                        <th colspan="2" style="padding-bottom: 0.5%;">Status</th> 
                        <th colspan="2">
                        <select id="associated_filter" class="chzn-select">
                            <option value="all" {if 'all' eq $TASK_STATUS} selected {/if}>All</option>
                            <option value="assigned" {if 'assigned' eq $TASK_STATUS} selected {/if}>Assigned</option>
                            <option value="unassigned" {if 'unassigned' eq $TASK_STATUS} selected {/if}>Unassigned</option>
                        </select>

                        </th>
                        <th>&nbsp;</th>
                        <th colspan="2" style="padding-bottom: 0.5%;">Commodity</th> 
                        <th colspan="2">
                            <select name="change_commodity" id="change_commodity" class="chzn-select">
                                <option value="--">All</option>
                                {foreach from=$COMMODITY_ARR item=commodity}
                                    <option value="{$commodity|lower}" {if $commodity|lower eq $COMMODODITY} selected {/if}>{$commodity}</option>
                                {/foreach}
                            </select>
                        </th>  
                        <th>&nbsp;</th>
                        <th colspan="2" style="padding-bottom: 0.5%;">Authority</th>
                        <th colspan="2">
                            <select name="change_authority" id="change_authority" class="chzn-select">
                                <option value="--">All</option>
                                {foreach from=$AUTORITY_ARR item=autority}
                                    <option value="{$autority|lower}" {if {$autority|lower} eq $AUTHORITY} selected {/if}>{$autority}</option>
                                {/foreach}
                            </select>
                        </th> 
                        <th colspan="16"></th>
                    </tr>
                    <tr class="listViewHeaders">
                        <th>{vtranslate('Transferee',$MODULE)}</th>
                        <th>{vtranslate('Account',$MODULE)}</th>
                        <th>{vtranslate('Order Number',$MODULE)}</th>
                        <th>{vtranslate('Service',$MODULE)}</th>
                        <th><a href="javascript:void(0);" class="HeaderValues" data-nextsortorderval="ASC" data-columnname="driver">{vtranslate('Driver  ',$MODULE)}</a><img class="icon-chevron-down" style="display:none;"><img class="icon-chevron-up" style="display:none;"></th>
                        <th>{vtranslate('Drivers Notes',$MODULE)}</th>
                        <th><a href="javascript:void(0);" class="HeaderValues" data-nextsortorderval="ASC" data-columnname="driver">{vtranslate('Origin ZIP  ',$MODULE)}</a><img class="icon-chevron-down" style="display:none;"><img class="icon-chevron-up" style="display:none;"></th>
                        <th>{vtranslate('Origin Address',$MODULE)}</th>
                        <th>{vtranslate('Origin City',$MODULE)}</th>
                        <th>{vtranslate('Origin State',$MODULE)}</th>
                        <th>{vtranslate('Dest. Address',$MODULE)}</th>
                        <th>{vtranslate('Dest. City',$MODULE)}</th>
                        <th>{vtranslate('Dest. State',$MODULE)}</th>
                        <th>{vtranslate('Service Date From',$MODULE)}</th>
                        <th>{vtranslate('Service Date To',$MODULE)}</th>
                        <th>{vtranslate('Assigned Date',$MODULE)}</th>
                        <th>{vtranslate('Est. Weight',$MODULE)}</th>
                        <th>{vtranslate('Est. Cube',$MODULE)}</th>
                        <th>{vtranslate('Est. Linehaul',$MODULE)}</th>
                        <th>{vtranslate('Est. # of Crew',$MODULE)}</th>
                        <th>{vtranslate('Est. # of Vehicles',$MODULE)}</th>
                        <th>{vtranslate('Move Coordinator',$MODULE)}</th>
                        <th>{vtranslate('Action',$MODULE)}</th>
                    </tr>
                </thead>

                {foreach item=LISTVIEW_ENTRY from=$LISTVIEW_ENTRIES name=listview}
                    <tr class="listViewEntries" data-id="{$LISTVIEW_ENTRY.orderstaskid}">

                        <td>{$LISTVIEW_ENTRY.orders_contacts}</td>
                        <td>{$LISTVIEW_ENTRY.orders_account}</td>
                        <td>{$LISTVIEW_ENTRY.orders_no}</td>
                        <td>{$LISTVIEW_ENTRY.servicenameoptions}</td>
                        <td>{$LISTVIEW_ENTRY.related_employee}</td>
                        <td><a href="javascript:void(0);" data-toggle="tooltip" data-placement="top" title="{$LISTVIEW_ENTRY.drivers_notes}">{vtranslate('Drivers Notes',$MODULE)}</a></td>
                        <td>{$LISTVIEW_ENTRY.origin_zip}</td>
                        <td>{$LISTVIEW_ENTRY.origin_address1}</td>
                        <td>{$LISTVIEW_ENTRY.origin_city}</td>
                        <td>{$LISTVIEW_ENTRY.origin_state}</td>
                        <td>{$LISTVIEW_ENTRY.destination_address1}</td>
                        <td>{$LISTVIEW_ENTRY.destination_city}</td>
                        <td>{$LISTVIEW_ENTRY.destination_state}</td>
                        <td>{$LISTVIEW_ENTRY.service_date_from}</td>
                        <td>{$LISTVIEW_ENTRY.service_date_to}</td>
                        <td>{$LISTVIEW_ENTRY.disp_assigneddate}</td>
                        <td>{$LISTVIEW_ENTRY.orders_eweight}</td>
                        <td>{$LISTVIEW_ENTRY.orders_ecube}</td>
                        <td>{$LISTVIEW_ENTRY.orders_elinehaul}</td>
                        <td>{$LISTVIEW_ENTRY.crew_number}</td>
                        <td>{$LISTVIEW_ENTRY.est_vehicle_number}</td>
                        <td>{$LISTVIEW_ENTRY.move_coordinator}</td>
                        <td><i id="{$LISTVIEW_ENTRY.orderstaskid}" class="icon-eye-open"></i><i id="{$LISTVIEW_ENTRY.orderstaskid}" class="icon-pencil"></i></td>
                    </tr>
                {/foreach}
            </table>
            
            <!--<input type="hidden" id="hidden_column_to_sort" value="">
            <input type="hidden" id="hidden_sort" value="">-->
            
            <!--added this div for Temporarily -->
            {if $LISTVIEW_ENTRIES_COUNT eq '0'}
                <table class="emptyRecordsDiv">
                    <tbody>
                        <tr>
                            <td>
                                {vtranslate('No Tasks for the selected date', $MODULE)}
                            </td>
                        </tr>
                    </tbody>
                </table>
            {/if}
        </div>
    </div>
{/strip}
