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
    {foreach key=BLOCK_LABEL_KEY item=FIELD_MODEL_LIST from=$RECORD_STRUCTURE}
        {assign var=BLOCK value=$BLOCK_LIST[$BLOCK_LABEL_KEY]}
    {if $BLOCK eq null or $FIELD_MODEL_LIST|@count lte 0}{continue}{/if}
    {assign var=IS_HIDDEN value=$BLOCK->isHidden()}
    {assign var=WIDTHTYPE value=$USER_MODEL->get('rowheight')}
    <input type=hidden name="timeFormatOptions" data-value='{$DAY_STARTS}' />
    <table class="table table-bordered equalSplit detailview-table {if $BLOCK->get('hideblock') eq true}hide{/if}">
        <thead>
            <tr>
                <th class="blockHeader" colspan="4">
                    <img class="cursorPointer alignMiddle blockToggle {if !($IS_HIDDEN)} hide {/if} "  src="{vimage_path('arrowRight.png')}" data-mode="hide" data-id={$BLOCK_LIST[$BLOCK_LABEL_KEY]->get('id')}>
                    <img class="cursorPointer alignMiddle blockToggle {if ($IS_HIDDEN)} hide {/if}"  src="{vimage_path('arrowDown.png')}" data-mode="show" data-id={$BLOCK_LIST[$BLOCK_LABEL_KEY]->get('id')}>
                    &nbsp;&nbsp;{vtranslate({$BLOCK_LABEL_KEY},{$MODULE_NAME})}
                </th>
            </tr>
        </thead>
        <tbody {if $IS_HIDDEN} class="hide" {/if}>
            {assign var=COUNTER value=0}
            <tr>
                {foreach item=FIELD_MODEL key=FIELD_NAME from=$FIELD_MODEL_LIST}
                    <!-- {$FIELD_NAME} -->
                    {if $FIELD_NAME eq 'oi_push_notification_token'}
                        {if $IS_OI_ENABLED neq 1}
                            <!-- O&I DISABLED -->
                            {continue}
                        {/if}
                    {/if}
                    {if $FIELD_NAME eq 'dbx_token'}
                        {if $IS_OI_ENABLED neq 1}
                            <!-- O&I DISABLED -->
                            {continue}
                        {else}
                            <!-- O&I ENABLED -->
                            <!-- {$FIELD_MODEL->getDisplayValue($FIELD_MODEL->get('fieldvalue'))} -->
                            {if $FIELD_MODEL->getDisplayValue($FIELD_MODEL->get('fieldvalue')) eq ''}
                                <!-- No DBX Token set -->
                                {if $COUNTER eq 2}
                                </tr><tr>
                                    {assign var="COUNTER" value=1}
                                {else}
                                    {assign var="COUNTER" value=$COUNTER+1}
                                {/if}
                                <td class="fieldLabel {$WIDTHTYPE}">
                                    <label class='muted pull-right marginRight10px'>
                                        {vtranslate({$FIELD_MODEL->get('label')},{$MODULE_NAME})}
                                    </label>
                                </td>
                                <td class="fieldValue {$WIDTHTYPE}">
                                    <span class="value" id="dropbox_auth_token">
                                        <button type="button" onclick="getDropboxAuth()">Get Dropbox Authorization Token</button>
                                    </span>
                                </td>
                            {else}
                                <!-- DBX Token is set -->
                                {if $COUNTER eq 2}
                                </tr><tr>
                                    {assign var="COUNTER" value=1}
                                {else}
                                    {assign var="COUNTER" value=$COUNTER+1}
                                {/if}
                                <td class="fieldLabel {$WIDTHTYPE}">
                                    <label class='muted pull-right marginRight10px'>
                                        {vtranslate({$FIELD_MODEL->get('label')},{$MODULE_NAME})}
                                    </label>
                                </td>
                                <td class="fieldValue {$WIDTHTYPE}">
                                    <span class="value" id="dropbox_auth_token">
                                        [hidden]
                                    </span>
                                </td>
                            {/if}
                            {continue}
                        {/if}
                    {/if}
                    {if !$FIELD_MODEL->isViewableInDetailView()}
                        {continue}
                    {/if}
                    {if $FIELD_MODEL->get('uitype') eq "83"}
                        {foreach item=tax key=count from=$TAXCLASS_DETAILS}
                            {if $tax.check_value eq 1}
                                {if $COUNTER eq 2}
                                </tr><tr>
                                    {assign var="COUNTER" value=1}
                                {else}
                                    {assign var="COUNTER" value=$COUNTER+1}
                                {/if}
                                <td class="fieldLabel {$WIDTHTYPE}">
                                    <label class='muted pull-right marginRight10px'>{vtranslate($tax.taxlabel, $MODULE)}(%)</label>
                                </td>
                                <td class="fieldValue {$WIDTHTYPE}">
                                    <span class="value">
                                        {$tax.percentage}
                                    </span>
                                </td>
                            {/if}
                        {/foreach}
                    {else if $FIELD_MODEL->get('uitype') eq "69" || $FIELD_MODEL->get('uitype') eq "105"}
                        {if $COUNTER neq 0}
                            {if $COUNTER eq 2}
                            </tr><tr>
                                {assign var=COUNTER value=0}
                            {/if}
                        {/if}
                        <td class="fieldLabel {$WIDTHTYPE}"><label class="muted pull-right marginRight10px">{vtranslate({$FIELD_MODEL->get('label')},{$MODULE_NAME})}</label></td>
                        <td class="fieldValue {$WIDTHTYPE}">
                            <div id="imageContainer" width="300" height="200">
                                {foreach key=ITER item=IMAGE_INFO from=$IMAGE_DETAILS}
                                    {if !empty($IMAGE_INFO.path) && !empty({$IMAGE_INFO.orgname})}
                                        <img src="{$IMAGE_INFO.path}_{$IMAGE_INFO.orgname}" width="300" height="200">
                                    {/if}
                                {/foreach}
                            </div>
                        </td>
                        {assign var=COUNTER value=$COUNTER+1}
                    {else}
                        {if $FIELD_MODEL->get('uitype') eq "20" or $FIELD_MODEL->get('uitype') eq "19"}
                            {if $COUNTER eq '1'}
                                <td class="{$WIDTHTYPE}"></td><td class="{$WIDTHTYPE}"></td></tr><tr>
                                    {assign var=COUNTER value=0}
                                {/if}
                            {/if}
                            {if $COUNTER eq 2}
                        </tr><tr>
                            {assign var=COUNTER value=1}
                        {else}
                            {assign var=COUNTER value=$COUNTER+1}
                        {/if}
                        <td class="fieldLabel {$WIDTHTYPE}" id="{$MODULE_NAME}_detailView_fieldLabel_{$FIELD_MODEL->getName()}" {if $FIELD_MODEL->getName() eq 'description' or $FIELD_MODEL->get('uitype') eq '69'} style='width:8%'{/if}>
                            <label class="muted pull-right marginRight10px">
                                {vtranslate({$FIELD_MODEL->get('label')},{$MODULE_NAME})}
                                {if ($FIELD_MODEL->get('uitype') eq '72') && ($FIELD_MODEL->getName() eq 'unit_price')}
                                    ({$BASE_CURRENCY_SYMBOL})
                                {/if}
                            </label>
                        </td>
                        <td class="fieldValue {$WIDTHTYPE}" id="{$MODULE_NAME}_detailView_fieldValue_{$FIELD_MODEL->getName()}" {if $FIELD_MODEL->get('uitype') eq '19' or $FIELD_MODEL->get('uitype') eq '20'} colspan="3" {assign var=COUNTER value=$COUNTER+1} {/if}>
                            <span class="value" data-field-type="{$FIELD_MODEL->getFieldDataType()}" {if $FIELD_MODEL->get('uitype') eq '19' or $FIELD_MODEL->get('uitype') eq '20' or $FIELD_MODEL->get('uitype') eq '21'} style="white-space:normal;" {/if}>
                                {include file=vtemplate_path($FIELD_MODEL->getUITypeModel()->getDetailViewTemplateName(),$MODULE_NAME) FIELD_MODEL=$FIELD_MODEL USER_MODEL=$USER_MODEL MODULE=$MODULE_NAME RECORD=$RECORD}
                            </span>
                            {if $IS_AJAX_ENABLED && $FIELD_MODEL->isEditable() eq 'true' && ($FIELD_MODEL->getFieldDataType()!=Vtiger_Field_Model::REFERENCE_TYPE) && $FIELD_MODEL->isAjaxEditable() eq 'true'}
                                <span class="hide edit">
                                    {include file=vtemplate_path($FIELD_MODEL->getUITypeModel()->getTemplateName(),$MODULE_NAME) FIELD_MODEL=$FIELD_MODEL USER_MODEL=$USER_MODEL MODULE=$MODULE_NAME}
                                    {if $FIELD_MODEL->getFieldDataType() eq 'multipicklist'}
                                        <input type="hidden" class="fieldname" value='{$FIELD_MODEL->get('name')}[]' data-prev-value='{$FIELD_MODEL->getDisplayValue($FIELD_MODEL->get('fieldvalue'))}' />
                                    {else}
                                        <input type="hidden" class="fieldname" value='{$FIELD_MODEL->get('name')}' data-prev-value='{Vtiger_Util_Helper::toSafeHTML($FIELD_MODEL->getDisplayValue($FIELD_MODEL->get('fieldvalue')))}' />
                                    {/if}
                                </span>
                            {/if}
                        </td>
                    {/if}

                    {if $FIELD_MODEL_LIST|@count eq 1 and $FIELD_MODEL->get('uitype') neq "19" and $FIELD_MODEL->get('uitype') neq "20" and $FIELD_MODEL->get('uitype') neq "30" and $FIELD_MODEL->get('name') neq "recurringtype" and $FIELD_MODEL->get('uitype') neq "69" and $FIELD_MODEL->get('uitype') neq "105"}
                        <td class="fieldLabel {$WIDTHTYPE}"></td><td class="{$WIDTHTYPE}"></td>
                        {/if}
                    {/foreach}
                    {* adding additional column for odd number of fields in a block *}
                    {if $FIELD_MODEL_LIST|@end eq true and $FIELD_MODEL_LIST|@count neq 1 and $COUNTER eq 1}
                    <td class="fieldLabel {$WIDTHTYPE}"></td><td class="{$WIDTHTYPE}"></td>
                    {/if}
            </tr>
        </tbody>
    </table>
    {if not $BLOCK->get('hideblock')}<br>{/if}
    
{/foreach}
    
{if $SERVICEHOURS_COUNT > 0}
    <div class="relatedContents contents-bottomscroll">
        <input type="hidden" value="{$HOURS_AVAILABLE}" id="availableHours">
        <div style="margin-top: 2%;margin-left:1%;float: left;font-weight: bold;{if $HOURS_AVAILABLE lt 10}color:red;{/if}">Available {$HOURS_AVAILABLE} Hrs</div>
        <button style="margin-bottom: 2%;float: right;" type="button" class="btn addButton createServiceHours" data-modulename="ServiceHours">&nbsp;<strong>Create Service Hour</strong></button>
        <div class="bottomscroll-div" style="width: 818px;">
            <table class="table table-bordered listViewEntriesTable" style="margin-bottom: 1%;">
                <thead>
                    <tr class="listViewHeaders">
                        <th nowrap="">{vtranslate('Service Hours ID',$MODULE_NAME)}</th>
                        <th nowrap="">{vtranslate('Employee',$MODULE_NAME)}</th>
                        <th nowrap="">{vtranslate('Actual Date',$MODULE_NAME)}</th>
                        <th nowrap="">{vtranslate('Worked Hours',$MODULE_NAME)}</th>
                        <th nowrap="" style="min-width:350px;">{vtranslate('Driver Message',$MODULE_NAME)}</th>
                    </tr>
                </thead>
                <tbody id="servicehours_tbody">
                    {foreach item=OTROARR from=$SERVICEHOURS_ARRAY}
                        <tr class="listViewEntries" data-id="{$OTROARR.servicehoursid}" data-orderid="{$OTROARR.servicehoursid}">
                            <td class="" data-field-type="string" nowrap=""><a href="index.php?module=ServiceHours&view=Detail&record={$OTROARR.servicehoursid}">{$OTROARR.servhours_id}</a></td>
                            <td class="" data-field-type="string" nowrap=""><a href="index.php?module=Employees&view=Detail&record={$OTROARR.employee_id}">{$OTROARR.employee}</a></td>
                            <td class="" data-field-type="date" nowrap="">{$OTROARR.actual_start_date}</td>
                            <td class="" data-field-type="double" nowrap="">{$OTROARR.total_hours}</td>
                            <td class="" data-field-type="string" nowrap="">{$OTROARR.driver_message}</td>
                        </tr>
                    {/foreach}
                </tbody>
            </table>
        </div>
    </div>
{else}
    <div class="relatedContents contents-bottomscroll">
        <button style="margin-bottom: 2%;float: right;" type="button" class="btn addButton createServiceHours" data-modulename="ServiceHours">&nbsp;<strong>Create Service Hour</strong></button>

        <div id="trips-orders-table">
            <table class="table table-bordered listViewEntriesTable">
                <thead>
                    <tr>
                        <th class="blockHeader">Service Hours Information</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td style="height: 40px;text-align: center;vertical-align: middle;">{vtranslate('LBL_NO_SERVICEHOURS_RECORDS', $MODULE_NAME)}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
{/if}
{if $DRIVERCHECKIN_COUNT gt 0}
<br>
    <div class="relatedContents driverCheckin contents-bottomscroll">
        <button style="margin-bottom: 2%;float: right;" type="button" class="btn addButton createDriverCheckin" data-modulename="TripsDriverCheckin">&nbsp;<strong>Create Driver Check In</strong></button>
        <div class="bottomscroll-div" style="width: 818px;">
            <table class="table table-bordered listViewEntriesTable" style="margin-bottom: 1%;">
                <thead>
                    <tr class="listViewHeaders">
                        <th nowrap="">{vtranslate('LBL_TRIPSDRIVERCHECKIN_NO','TripsDriverCheckin')}</th>
                        <th nowrap="">{vtranslate('LBL_TRIPSDRIVERCHECKIN_CURRENTLOCATION','TripsDriverCheckin')}</th>
                        <th nowrap="">{vtranslate('LBL_TRIPSDRIVERCHECKIN_NEXTLOCATION','TripsDriverCheckin')}</th>
                        <th nowrap="">{vtranslate('LBL_TRIPSDRIVERCHECKIN_ACTIVITY','TripsDriverCheckin')}</th>
                        <th nowrap="">Created Time</th>
                        <th nowrap="" style="min-width:350px;">{vtranslate('LBL_TRIPSDRIVERCHECKIN_COMMENTS','TripsDriverCheckin')}</th>
                        <th nowrap=""></th>
                    </tr>
                </thead>
                <tbody id="drivercheckin_tbody">
                    {foreach item=OTROARR from=$DRIVERCHECKIN_ARRAY}
                        <tr class="listViewEntries" data-id="{$OTROARR.tripsdrivercheckinid}" data-orderid="{$OTROARR.tripsdrivercheckinid}">
                            <td class="" data-field-type="string" nowrap=""><a href="index.php?module=TripsDriverCheckin&view=Detail&record={$OTROARR.tripsdrivercheckinid}">{$OTROARR.tripsdrivercheckin_no}</a></td>
                            <td class="" data-field-type="string" nowrap="">{$OTROARR.tripsdrivercheckin_currentlocation}</td>
                            <td class="" data-field-type="string" nowrap="">{$OTROARR.tripsdrivercheckin_nextlocation}</td>
                            <td class="" data-field-type="string" nowrap="">{$OTROARR.tripsdrivercheckin_activity}</td>
                            <td class="" data-field-type="time" nowrap="">{$OTROARR.createdtime}</td>
                            <td class="" data-field-type="string" nowrap="">{$OTROARR.tripsdrivercheckin_comments}</td>
                            <td class="" data-field-type="string" nowrap="">
                                <div class="pull-right actions">
                                    <span class="actionImages">
                                       <a href="index.php?module=TripsDriverCheckin&amp;view=Edit&amp;record={$OTROARR.tripsdrivercheckinid}"><i title="Edit" class="icon-pencil alignMiddle"></i></a>
                                       &nbsp;
                                       <a class="deleteDriverCheckin"  data-id="{$OTROARR.tripsdrivercheckinid}"><i title="Delete" class="icon-trash alignMiddle"></i></a>
                                    </span>
                                </div>
                            </td>
                        </tr>
                    {/foreach}
                </tbody>
            </table>
        </div>
    </div>
                 <br>   
{else}
    <br>
    <div class="relatedContents contents-bottomscroll">
        <button style="margin-bottom: 2%;float: right;" type="button" class="btn addButton createDriverCheckin" data-modulename="TripsDriverCheckin">&nbsp;<strong>Create Driver Check In</strong></button>

        <div id="trips-orders-table">
            <table class="table table-bordered listViewEntriesTable">
                <thead>
                    <tr>
                        <th class="blockHeader">{vtranslate('LBL_TRIPSDRIVERCHECKIN_INFORMATION','TripsDriverCheckin')}</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td style="height: 40px;text-align: center;vertical-align: middle;">{vtranslate('LBL_NO_DRIVERCHECKIN_RECORDS', 'TripsDriverCheckin')}</td>
                    </tr>
                </tbody>
            </table>
                    
        </div>
    </div>
                <br>        
{/if}
{if $ORDERS_COUNT > 0}
    <div class="relatedContents tripOrders contents-bottomscroll" style="min-height:550px;">
    <button style="margin-bottom: 2%;float: right;" type="button" class="btn addButton selectRelation" data-modulename="Orders">&nbsp;<strong>Select Orders</strong></button>
        <input type="hidden" name="relatedModuleName" class="relatedModuleName" value="Orders">
        <div class="bottomscroll-div" style="width: 818px;">
			<style>
				{literal}
					table#orders_list th {
						text-align: left;
						background-color: #ccc !important;
						font-weight:500;
					}
					table#orders_list th, table#orders_list td {
						padding: .5em;
						border: 0px solid #999;
                        padding-top: 0.1em;
                        padding-bottom: 0.1em;
					}
                    table#orders_list td {
						padding-top: 0.5em;
					}
					table#orders_list th {
						cursor: move; /* fallback if grab cursor is unsupported */
						cursor: grab;
						cursor: -moz-grab;
						cursor: -webkit-grab;
					}
					table#orders_list th span{
						font-weight:bold;
						font-family: Verdana !important;
					}

                    .trips-orders .input-prepend, .trips-orders .input-append {
                        margin-bottom: 0px;
                    }
                    
				{/literal}
			</style>
            <div class="trips-orders">
                <table id="orders_list" class="table table-bordered listViewEntriesTable" style="margin-bottom: 1%;">
                    {foreach item=MINIARR from=$ORDERS_ARRAY}
                        <tbody data-sequence="{$MINIARR.orders_sequence}" data-id="{$MINIARR.ordersid}" data-orderid="{$MINIARR.ordersid}">
                            <tr data-tr="firstHeader">
                                <th id="co1" headers="blank"><span>{vtranslate('Order No',$MODULE_NAME)}</span></th>
                                <th id="co2" headers="blank"><span>{vtranslate('Status',$MODULE_NAME)}</span></th>
                                <th id="co3" headers="blank"><span>{vtranslate('Origin',$MODULE_NAME)}</span></th>
                                <th id="co4" headers="blank"><span>{vtranslate('ST',$MODULE_NAME)}</span></th>
                                <th id="co5" headers="blank"><span>{vtranslate('Load',$MODULE_NAME)}</span></th>
                                <th id="co6" headers="blank"><span>{vtranslate('Planned Load',$MODULE_NAME)}</span></th>
                                <th id="co7" headers="blank"><span>{vtranslate('Delivery',$MODULE_NAME)}</span></th>
                                <th id="co8" headers="blank"><span>{vtranslate('Planned Delivery',$MODULE_NAME)}</span></th>
                                <th id="co9" headers="blank"><span>{vtranslate('Account',$MODULE_NAME)}</span></th>
                                <th id="co10" headers="blank"><span>{vtranslate('O/A',$MODULE_NAME)}</span></th>
                                <th id="co11" headers="blank"><span>{vtranslate('SIT',$MODULE_NAME)}</span></th>
                            </tr>
                            <tr data-tr="firstData">
                                <td headers="co1"><a target="_blank" href="index.php?module=Orders&view=Detail&record={$MINIARR.ordersid}">{$MINIARR.order_no}</a></td>
                                <td headers="co2"><input type="hidden" value="{$MINIARR.otherstatus}" class="statusajaxvalue"><select class="statusajax" style="width:100%;min-width:100px;"><option value="blank">  </option> {foreach item=LDD_STATUS from=$ORDERS_STATUS}  <option {if $LDD_STATUS eq $MINIARR.otherstatus} selected {/if} value="{$LDD_STATUS}">{$LDD_STATUS}</option>  {/foreach}</select></td>
                                <td headers="co3">{$MINIARR.origin_city}</td>
                                <td headers="co4">{$MINIARR.origin_state}</td>
                                <td headers="co5" style="min-width:75px">{$MINIARR.pudate}</td>
                                <td headers="co6"><div class="input-append row-fluid" style="  min-width: 110px;"><div class="span12 row-fluid date"><input id="planned_load_date" type="text" class="dateField" name="planned_load_date" data-date-format="{$MINIARR.dateformat}" type="text" value="{$MINIARR.planned_load_date}" style="width:75px;"/><span class="add-on"><i class="icon-calendar"></i></span></div></div></td>
                                <td headers="co7" style="min-width:75px">{$MINIARR.delivery_date}</td>
                                <td headers="co8"><div class="input-append row-fluid" style="  min-width: 110px;"><div class="span12 row-fluid date"><input id="planned_delivery_date" type="text" class="dateField" name="planned_delivery_date" data-date-format="{$MINIARR.dateformat}" type="text" value="{$MINIARR.planned_delivery_date}" style="width:75px;"/><span class="add-on"><i class="icon-calendar"></i></span></div></div></td>
                                <td headers="co9">{$MINIARR.account_name}</td>
                                <td headers="co10">{$MINIARR.origin_agent}</td>
                                <td headers="co11"><input type="checkbox" class="sit" {if $MINIARR.sit eq '1'}checked{/if}></td>
                            </tr>
                            <tr data-tr="secondHeader">
                                <th id="co1" headers="blank"><span>{vtranslate('Shipper',$MODULE_NAME)}</span></th>
                                <th id="co2" headers="blank"><span>{vtranslate('Est. Weight',$MODULE_NAME)}</span></th>
                                <th id="co3" headers="blank"><span>{vtranslate('Destination',$MODULE_NAME)}</span></th>
                                <th id="co4" headers="blank"><span>{vtranslate('ST',$MODULE_NAME)}</span></th>
                                <th id="co5" headers="blank"><span>{vtranslate('Load To',$MODULE_NAME)}</span></th>
                                <th id="co6" headers="blank"><span>{vtranslate('Actual Load',$MODULE_NAME)}</span></th>
                                <th id="co7" headers="blank"><span>{vtranslate('Delivery To',$MODULE_NAME)}</span></th>
                                <th id="co8" headers="blank"><span>{vtranslate('Actual Delivery',$MODULE_NAME)}</span></th>
                                <th id="co9" headers="blank"><span>{vtranslate('Actual Weight',$MODULE_NAME)}</span></th>
                                <th id="co10" headers="blank"><span>{vtranslate('D/A',$MODULE_NAME)}</span></th>
                                <th id="co11" headers="blank"><span>{vtranslate('Linehaul',$MODULE_NAME)}</span></th>
                            </tr>
                            <tr data-tr="secondData">
                                <td headers="co1">{$MINIARR.ship_lastname}</td>
                                <td headers="co2">{$MINIARR.est_weight}</td>
                                <td headers="co3">{$MINIARR.dest_city}</td>
                                <td headers="co4">{$MINIARR.dest_state}</td>
                                <td headers="co5" style="min-width:75px">{$MINIARR.load_to_date}</td>
                                <td headers="co6"><div class="input-append row-fluid" style="  min-width: 110px;"><div class="span12 row-fluid date"><input id="actual_load_date" type="text" class="dateField" name="actual_load_date" data-date-format="{$MINIARR.dateformat}" type="text" value="{$MINIARR.actual_load_date}" style="width:75px;"/><span class="add-on"><i class="icon-calendar"></i></span></div></div></td>
                                <td headers="co7" style="min-width:75px">{$MINIARR.delivery_to_date}</td>
                                <td headers="co8"><div class="input-append row-fluid" style="  min-width: 110px;"><div class="span12 row-fluid date"><input id="actual_delivery_date" type="text" class="dateField" name="actual_delivery_date" data-date-format="{$MINIARR.dateformat}" type="text" value="{$MINIARR.actual_delivery_date}" style="width:75px;"/><span class="add-on"><i class="icon-calendar"></i></span></div></div></td>
                                <td headers="co9"><input type="text" value="{$MINIARR.actual_weight}" id="actual_weight" style="width: 80%;"></td>
                                <td headers="co10">{$MINIARR.dest_agent}</td>
                                <td headers="co11">{$MINIARR.linehaul}</td>
                            </tr>
                            <tr><td colspan="11" style="padding: 0;background-color: black;border: none;line-height: 3px;">&nbsp;</td></tr>
                        </tbody>
                    {/foreach}
                </table>
            </div>
        </div>
    </div>
{else}
    <div class="relatedContents contents-bottomscroll">
        <button style="margin-bottom: 2%;float: right;" type="button" class="btn addButton selectRelation" data-modulename="Orders">&nbsp;<strong>Select Order</strong></button>
        <input type="hidden" name="relatedModuleName" class="relatedModuleName" value="Orders">

        <div id="trips-orders-table">
            <table class="table table-bordered listViewEntriesTable">
                <thead>
                    <tr>
                        <th class="blockHeader">Orders Information</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td style="height: 40px;text-align: center;vertical-align: middle;">{vtranslate('LBL_NO_ORDERS', $MODULE_NAME)}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
{/if}
<script>
{literal}
    jQuery(document).ready(function(){
        jQuery('#selectall').click(function(){
            if(jQuery(this).prop('checked')){
                jQuery('.asoc:checkbox').each(function(){
                    jQuery(this).prop('checked',true);
                });
            }else{
                jQuery('.asoc:checkbox').each(function(){
                    jQuery(this).prop('checked',false);
                });   
            }
        });
        jQuery('#orders_tbody > tr').each(function(){ // Show Orders Other Status from DB
            var order_other_status = jQuery(this).find('#statusajaxvalue').val();//.trim();
            jQuery(this).find('select.statusajax option').removeAttr("selected");
            jQuery(this).find('select.statusajax option[value="'+order_other_status+'"]').attr("selected",true);
        });
        jQuery('#orders_tbody > tr').each(function(){ // Show pl_committed from DB
            var pl_confirmed = jQuery(this).find('#pl_confirmedvalue').val();//.trim();
            jQuery(this).find('select.pl_confirmed option').removeAttr("selected");
            jQuery(this).find('select.pl_confirmed option[value="'+pl_confirmed+'"]').attr("selected",true);
        });
        jQuery('#orders_tbody > tr').each(function(){ // Show pl_committed from DB
            var pd_confirmed = jQuery(this).find('#pd_confirmedvalue').val();//.trim();
            jQuery(this).find('select.pd_confirmed option').removeAttr("selected");
            jQuery(this).find('select.pd_confirmed option[value="'+pd_confirmed+'"]').attr("selected",true);
        });
    });
{/literal}
</script>
{/strip}
