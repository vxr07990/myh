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
            <table class="table table-bordered listViewEntriesTable">
                <thead>
                    <tr class="listViewHeaders">
                        <th>{vtranslate('Transferee',$MODULE)}</th>
                        <th>{vtranslate('Account',$MODULE)}</th>
                        <th>{vtranslate('Order Number',$MODULE)}</th>
                        <th>{vtranslate('Service',$MODULE)}</th>
                        <th>{vtranslate('Origin Address',$MODULE)}</th>
                        <th>{vtranslate('Origin City',$MODULE)}</th>
                        <th>{vtranslate('Origin State',$MODULE)}</th>
                        <th>{vtranslate('Dest. Address',$MODULE)}</th>
                        <th>{vtranslate('Dest. City',$MODULE)}</th>
                        <th>{vtranslate('Dest. State',$MODULE)}</th>
                        <th>{vtranslate('Service Date From',$MODULE)}</th>
                        <th>{vtranslate('Service Date To',$MODULE)}</th>
                        <th>{vtranslate('Preferred Date',$MODULE)}</th>
                        <th>{vtranslate('Est. Weight',$MODULE)}</th>
                        <th>{vtranslate('Est. Cube',$MODULE)}</th>
                        <th>{vtranslate('Est. Linehaul',$MODULE)}</th>
                        <th>{vtranslate('Est. # of Crew',$MODULE)}</th>
                        <th>{vtranslate('Est. # of Vehicles',$MODULE)}</th>
                        <th>{vtranslate('Move Coordinator',$MODULE)}</th>
                    </tr>
                </thead>

                {foreach item=LISTVIEW_ENTRY from=$LISTVIEW_ENTRIES name=listview}
                    <tr class="listViewEntries actuals-rows" id="{$LISTVIEW_ENTRY.orderstaskid}">

                        <td>{$LISTVIEW_ENTRY.orders_contacts}</td>
                        <td>{$LISTVIEW_ENTRY.orders_account}</td>
                        <td>{$LISTVIEW_ENTRY.orders_no}</td>
                        <td>{$LISTVIEW_ENTRY.servicenameoptions}</td>
                        <td>{$LISTVIEW_ENTRY.origin_address1}</td>
                        <td>{$LISTVIEW_ENTRY.origin_city}</td>
                        <td>{$LISTVIEW_ENTRY.origin_state}</td>
                        <td>{$LISTVIEW_ENTRY.destination_address1}</td>
                        <td>{$LISTVIEW_ENTRY.destination_city}</td>
                        <td>{$LISTVIEW_ENTRY.destination_state}</td>
                        <td>{$LISTVIEW_ENTRY.service_date_from}</td>
                        <td>{$LISTVIEW_ENTRY.service_date_to}</td>
                        <td>{$LISTVIEW_ENTRY.pref_date_service}</td>
                        <td>{$LISTVIEW_ENTRY.orders_eweight}</td>
                        <td>{$LISTVIEW_ENTRY.orders_ecube}</td>
                        <td>{$LISTVIEW_ENTRY.orders_elinehaul}</td>
                        <td>{$LISTVIEW_ENTRY.crew_number}</td>
                        <td>{$LISTVIEW_ENTRY.est_vehicle_number}</td>
                        <td>{$LISTVIEW_ENTRY.move_coordinator}</td>

                    </tr>
                {/foreach}
            </table>

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

    <input id="selected-taskid" type="hidden" value="">

{/strip}
