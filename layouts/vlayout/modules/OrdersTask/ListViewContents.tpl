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
<div class="root-div" id="listViewContents">
<input type="hidden" id="view" value="{$VIEW}" />
<input type="hidden" id="pageStartRange" value="{$PAGING_MODEL->getRecordStartRange()}" />
<input type="hidden" id="pageEndRange" value="{$PAGING_MODEL->getRecordEndRange()}" />
<input type="hidden" id="previousPageExist" value="{$PAGING_MODEL->isPrevPageExists()}" />
<input type="hidden" id="nextPageExist" value="{$PAGING_MODEL->isNextPageExists()}" />
<input type="hidden" id="alphabetSearchKey" value= "{$MODULE_MODEL->getAlphabetSearchField()}" />
<input type="hidden" id="Operator" value="{$OPERATOR}" />
<input type="hidden" id="alphabetValue" value="{$ALPHABET_VALUE}" />
{*<input type="hidden" id="totalCount" value="{$LISTVIEW_COUNT}" />*}
<input type="hidden" id="totalCount" value="{$LISTVIEW_ENTRIES_COUNT}" />
<input type="hidden" name="customViewSplitterPosition" id="customViewSplitterPosition" value="{$SPLITTER_POSITION}">
<input type="hidden" name="customViewResourceHidden" id="customViewResourceHidden" value="{$RESOURCE_TAB_HIDDEN}">
<input type='hidden' value="{$PAGE_NUMBER}" id='pageNumber'>
<input type='hidden' value="{$PAGING_MODEL->getPageLimit()}" id='pageLimit'>
<input type="hidden" value="{$LISTVIEW_ENTRIES_COUNT}" id="noOfEntries">

{if !isset($LocalDispatch)}{assign var=LocalDispatch value='false'}{/if}
{if !isset($LocalDispatchActuals)}{assign var=LocalDispatchActuals value='false'}{/if}

{assign var = ALPHABETS_LABEL value = vtranslate('LBL_ALPHABETS', 'Vtiger')}
{assign var = ALPHABETS value = ','|explode:$ALPHABETS_LABEL}
{if $LocalDispatch neq 'true' && $LocalDispatchActuals neq 'true'}

<div class="alphabetSorting noprint">
    <table width="100%" class="table-bordered" style="border: 1px solid #ddd;table-layout: fixed">
        <tbody>
            <tr>
            {foreach item=ALPHABET from=$ALPHABETS}
                <td class="alphabetSearch textAlignCenter cursorPointer {if $ALPHABET_VALUE eq $ALPHABET} highlightBackgroundColor {/if}" style="padding : 0px !important"><a id="{$ALPHABET}" href="#">{$ALPHABET}</a></td>
            {/foreach}
            </tr>
        </tbody>
    </table>
</div>
{/if}
<div id="selectAllMsgDiv" class="alert-block msgDiv noprint">
    <strong><a id="selectAllMsg">{vtranslate('LBL_SELECT_ALL',$MODULE)}&nbsp;{vtranslate($MODULE ,$MODULE)}&nbsp;(<span id="totalRecordsCount"></span>)</a></strong>
</div>
<div id="deSelectAllMsgDiv" class="alert-block msgDiv noprint">
    <strong><a id="deSelectAllMsg">{vtranslate('LBL_DESELECT_ALL_RECORDS',$MODULE)}</a></strong>
</div>
<div id="ldd-table">
<div id="leftPane" class="listViewEntriesDiv {if $LocalDispatch neq 'true' && $LocalDispatchActuals neq 'true'} listViewContentDiv contents-bottomscroll{/if}" style="{if $LocalDispatch eq 'true' || $LocalDispatchActuals eq 'true'} /*padding-bottom: 100px; */overflow-x:scroll; /*margin-bottom: 1%; */width:100%;float:left{/if}">
    <div class="bottomscroll-div">
	<input type="hidden" value="{$ORDER_BY}" id="orderBy">
	<input type="hidden" value="{$SORT_ORDER}" id="sortOrder">
	<span class="listViewLoadingImageBlock hide modal noprint" id="loadingListViewModal">
            <img class="listViewLoadingImage" src="{vimage_path('loading.gif')}" alt="no-image" title="{vtranslate('LBL_LOADING', $MODULE)}"/>
            <p class="listViewLoadingMsg">{vtranslate('LBL_LOADING_LISTVIEW_CONTENTS', $MODULE)}........</p>
	</span>
	{assign var=WIDTHTYPE value=$CURRENT_USER_MODEL->get('rowheight')}
	<table class="table table-bordered listViewEntriesTable">
            <thead>
                <tr class="listViewHeaders">
                    <th width="5%">
                        {if $LocalDispatchActuals neq 'true'}<input type="checkbox" id="listViewEntriesMainCheckBox" />{/if}
                    </th>
                {foreach item=LISTVIEW_HEADER from=$LISTVIEW_HEADERS}

                    <th nowrap {if $LocalDispatch neq 'true' && $LocalDispatchActuals neq 'true' && $LISTVIEW_HEADER@last} colspan="2" {/if}>
                        <a href="javascript:void(0);" class="listViewHeaderValues" data-nextsortorderval="{if $COLUMN_NAME eq $LISTVIEW_HEADER->get('column')}{$NEXT_SORT_ORDER}{else}ASC{/if}" data-columnname="{$LISTVIEW_HEADER->get('column')}">{if $LISTVIEW_HEADER->get('column') eq 'ordersstatus'} {vtranslate('ordersstatus', $MODULE)} {else}{vtranslate($LISTVIEW_HEADER->get('label'), getTabModuleName($LISTVIEW_HEADER->getModuleId()))}{/if}
                        &nbsp;&nbsp;{if $COLUMN_NAME eq $LISTVIEW_HEADER->get('column')}<img class="{$SORT_IMAGE} icon-white">{/if}</a>
                    </th>
                {/foreach}
                {*if $LocalDispatch eq 'true'}
                    <th colspan="2">{vtranslate('Profitable',$MODULE)}</th>
                {/if*}
                <th colspan="2"></th>
                </tr>
            </thead>
    {if $MODULE_MODEL->isQuickSearchEnabled()}
        <tr>
            <td></td>
            {foreach item=LISTVIEW_HEADER from=$LISTVIEW_HEADERS}
             <td>
                {assign var=FIELD_UI_TYPE_MODEL value=$LISTVIEW_HEADER->getUITypeModel()}
				{if $LISTVIEW_HEADER->getName() eq 'disp_assigneddate'}
					<div class="row-fluid">
						<input type="text" name="disp_assigneddate" class="span9 listSearchContributor" value="" readonly>
					</div>
				{else}
                 {include file=vtemplate_path($FIELD_UI_TYPE_MODEL->getListSearchTemplateName(),$MODULE_NAME)
                    FIELD_MODEL= $LISTVIEW_HEADER SEARCH_INFO=$SEARCH_DETAILS[$LISTVIEW_HEADER->getName()] USER_MODEL=$CURRENT_USER_MODEL}
				{/if}
             </td>
            {/foreach}
            {if $LocalDispatch eq 'true'}
                <td style="min-width:70px;">&nbsp;&nbsp;</td>
            {/if}
            <td><button class="btn" data-trigger="listSearch">{vtranslate('LBL_SEARCH', $MODULE )}</button></td>
        </tr>
    {/if}
    {foreach item=LISTVIEW_ENTRY from=$LISTVIEW_ENTRIES name=listview key=LISTVIEW_KEY}
        {assign var=RAWDATA value=$LISTVIEW_ENTRY->getRawData()}
        {assign var=ORDERSTASKID value=$LISTVIEW_ENTRY->getId()}
        <tr class="listViewEntries" data-id='{$LISTVIEW_ENTRY->getId()}' data-recordUrl='{$LISTVIEW_ENTRY->getDetailViewUrl()}' id="{$MODULE}_listView_row_{$smarty.foreach.listview.index+1}" {if isParticipantForRecord($LISTVIEW_ENTRY->getId())}style="background-color: #ffff99"{/if}>
            <td  width="5%" class="{$WIDTHTYPE}">
                <input type="checkbox" value="{$LISTVIEW_ENTRY->getId()}" class="listViewEntriesCheckBox {if $LocalDispatch eq 'true' || $LocalDispatchActuals eq 'true'}select_task{/if}" data-id='{$LISTVIEW_ENTRY->getId()}'/>
            </td>
            {foreach item=LISTVIEW_HEADER from=$LISTVIEW_HEADERS}
                {assign var=LISTVIEW_HEADERNAME value=$LISTVIEW_HEADER->get('name')}
                    {if ($LocalDispatch eq 'true' || $LocalDispatchActuals eq 'true') && $LISTVIEW_HEADERNAME eq 'dispatch_status'}
                        <td>

                            {assign var = DISPATCH_STATUS_ARRAY value = $LISTVIEW_ENTRY->getDispatchStatusValue()}

                            <select class="dispatch_status span2" data-prevvalue="{$LISTVIEW_ENTRY->get($LISTVIEW_HEADERNAME)}" data-orderstaskid="{$LISTVIEW_ENTRY->getId()}" data-fieldname="dispatch_status">
                                {foreach key=KEY item=VALUE from=$DISPATCH_STATUS_ARRAY}
                                    <option value="{$VALUE}"  {if $LISTVIEW_ENTRY->get($LISTVIEW_HEADERNAME) eq $VALUE}selected{/if}>{$VALUE}</option>
                                {/foreach}
                            </select>
                        </td>
                    {else}

                        <td {if  (($LocalDispatch eq 'true' || $LocalDispatchActuals eq 'true') && $LISTVIEW_HEADERNAME eq 'disp_assignedstart') ||  (($LocalDispatch eq 'true' || $LocalDispatchActuals eq 'true') && $LISTVIEW_HEADERNAME eq 'disp_actualend') } style="min-width: 125px;" {/if} class="listViewEntryValue {$WIDTHTYPE} {if $LISTVIEW_HEADERNAME eq 'total_estimated_personnel' || $LISTVIEW_HEADERNAME eq 'total_estimated_vehicles'} customToolTip {$LISTVIEW_HEADERNAME}{/if}" data-field-type="{$LISTVIEW_HEADER->getFieldDataType()}" nowrap>

				{if ($LISTVIEW_HEADER->isNameField() eq true or $LISTVIEW_HEADER->get('uitype') eq '4') and $MODULE_MODEL->isListViewNameFieldNavigationEnabled() eq true }
                                    <a href="{$LISTVIEW_ENTRY->getDetailViewUrl()}">{$LISTVIEW_ENTRY->get($LISTVIEW_HEADERNAME)}</a>
				{else if $LISTVIEW_HEADER->get('uitype') eq '72'}
                                    {assign var=CURRENCY_SYMBOL_PLACEMENT value={$CURRENT_USER_MODEL->get('currency_symbol_placement')}}
                                    {if $CURRENCY_SYMBOL_PLACEMENT eq '1.0$'}
                                        {$LISTVIEW_ENTRY->get($LISTVIEW_HEADERNAME)}{$LISTVIEW_ENTRY->get('currencySymbol')}
                                    {else}
                                        {$LISTVIEW_ENTRY->get('currencySymbol')}{$LISTVIEW_ENTRY->get($LISTVIEW_HEADERNAME)}
                                    {/if}
				{else if $LISTVIEW_HEADER->get('uitype') eq 11}
					{$LISTVIEW_ENTRY->get($LISTVIEW_HEADERNAME)}
                                {else if $LISTVIEW_HEADER->get('uitype') eq 1008}{*assigned_employee*}

                                    {if $LocalDispatch eq 'true' || $LocalDispatchActuals eq 'true'}
                                        {assign var = LEAD_EMPLOYEE value = $LISTVIEW_ENTRY->getLeadEmployee($LISTVIEW_ENTRY->getId())}
                                        {assign var = CREW_ARRAY value = $LISTVIEW_ENTRY->getEmployees($LISTVIEW_ENTRY->get($LISTVIEW_HEADERNAME))}
                                        <select class="employees_chzn" data-resource_type="Employee" data-orderstaskid="{$LISTVIEW_ENTRY->getId()}" data-placeholder="Crew" multiple>
                                        {foreach key=KEY item=CREW from=$CREW_ARRAY}

                                                <option value="{$KEY}" class="{if $KEY eq $LEAD_EMPLOYEE}lead_employee {/if}employee_{$KEY}"  selected>{$CREW}</option>

                                        {/foreach}
                                        </select>
                                     {else}
                                       {$LISTVIEW_ENTRY->get($LISTVIEW_HEADERNAME)}
                                    {/if}
                                {else if $LISTVIEW_HEADER->get('uitype') eq 1010}{*assigned_vendor*}
                                    {if $LocalDispatch eq 'true' || $LocalDispatchActuals eq 'true'}
                                        {assign var = VENDOR_ARRAY value = $LISTVIEW_ENTRY->getVendors($LISTVIEW_ENTRY->get($LISTVIEW_HEADERNAME))}
                                        <select class="vendorchzn" data-resource_type="Vendor" data-orderstaskid="{$LISTVIEW_ENTRY->getId()}" multiple>
                                        {foreach key=KEY item=VENDOR from=$VENDOR_ARRAY}
                                            <option value="{$KEY}"  selected>{$VENDOR}</option>
                                        {/foreach}
                                        </select>
                                     {else}
                                       {$LISTVIEW_ENTRY->getVendors($LISTVIEW_ENTRY->get($LISTVIEW_HEADERNAME), false)}
                                    {/if}
                                {elseif $LISTVIEW_HEADERNAME eq 'estimated_hours' && $LocalDispatchActuals neq 'true'}
                                    <input type="hidden" class="esthours" value="{$LISTVIEW_ENTRY->get($LISTVIEW_HEADERNAME)}">
                                    {$LISTVIEW_ENTRY->get($LISTVIEW_HEADERNAME)}
                                {elseif  $LocalDispatch eq 'true' && $LISTVIEW_HEADERNAME eq 'disp_assigneddate'}
                                    <div class="input-append" style="min-width: 150px; margin-bottom: 0px;">
                                        <div class="row-fluid date">
                                            <input type="text" class="span2 notMultipleCalendar dateField {$LISTVIEW_HEADERNAME}" data-date-format="{$CURRENT_USER_MODEL->get('date_format')}" value="{$LISTVIEW_ENTRY->get($LISTVIEW_HEADERNAME)}" style="width: 100px;    padding: 4px !important;">
                                            <span class="add-on">
                                                <i class="icon-calendar"></i>
                                            </span>
                                        </div>
                                    </div>
                                {elseif  $LocalDispatch eq 'true' && $LISTVIEW_HEADERNAME eq 'disp_assignedstart' ||  $LocalDispatch eq 'true' && $LISTVIEW_HEADERNAME eq 'disp_actualend'}
                                    <div class="input-append time">
                                        <input type="text" data-format="{$CURRENT_USER_MODEL->get('hour_format')}" class="custom-tp timepicker-default input-small {$LISTVIEW_HEADERNAME}" {*if $LISTVIEW_HEADERNAME eq 'disp_actualend'} readonly {/if*} value="{$LISTVIEW_ENTRY->get($LISTVIEW_HEADERNAME)}" name="{$LISTVIEW_HEADERNAME}"
                                            data-validation-engine="validate[funcCall[Vtiger_Base_Validator_Js.invokeValidation]]"/>
                                        <span class="add-on cursorPointer">
                                            <i class="icon-time"></i>
                                        </span>
                                    </div>
                                {elseif $LISTVIEW_ENTRY->get($LISTVIEW_HEADERNAME) !='' && ( $VIEW eq 'List' && $LISTVIEW_HEADERNAME eq 'disp_assignedstart'  ||  $VIEW eq 'List' && $LISTVIEW_HEADERNAME eq 'disp_actualend' )}
                                        <span>{DateTimeField::convertToUserTimeZone($LISTVIEW_ENTRY->get($LISTVIEW_HEADERNAME))->format('h:i A')}</span>
                                {elseif  $LocalDispatchActuals eq 'true' && $LISTVIEW_HEADERNAME eq 'estimated_hours' || $LISTVIEW_HEADERNAME eq 'disp_actualhours'}
                                    <input type="number" step="0.1" data-orderstaskid="{$LISTVIEW_ENTRY->getId()}" data-fieldname="{$LISTVIEW_HEADERNAME}" class="{$LISTVIEW_HEADERNAME}" {*readonly*} value="{$RAWDATA[$LISTVIEW_HEADERNAME]}" name="{$LISTVIEW_HEADERNAME}" style="width:100px; cursor: pointer;"/>
                                {elseif  $LocalDispatchActuals eq 'true' && $LISTVIEW_HEADERNAME eq 'actual_of_crew' ||  $LocalDispatchActuals eq 'true' && $LISTVIEW_HEADERNAME eq 'actual_of_vehicles' }
                                    <input type="number" step="1" data-orderstaskid="{$LISTVIEW_ENTRY->getId()}" class="{$LISTVIEW_HEADERNAME}" data-fieldname="{$LISTVIEW_HEADERNAME}" {*readonly*} value="{$RAWDATA[$LISTVIEW_HEADERNAME]}" name="{$LISTVIEW_HEADERNAME}" style="width:100px; cursor: pointer;"/>
                               {elseif  $LISTVIEW_HEADER->get('uitype') eq 1009}{*assigned_vehicles*}

                                   {if $LocalDispatch eq 'true'}
                                         {assign var = EQUIPMENT_ARRAY value = $LISTVIEW_ENTRY->getVehicles($LISTVIEW_ENTRY->get($LISTVIEW_HEADERNAME))}
                                        <select class="vehicles_chzn" data-resource_type="Vehicle" data-orderstaskid="{$LISTVIEW_ENTRY->getId()}" data-placeholder="Assigned Vehicles" multiple>
                                            {foreach key=KEY item=EQUIPMENT from=$EQUIPMENT_ARRAY}
                                                <option value="{$KEY}"  data-orderstaskid="{$LISTVIEW_ENTRY->getId()}" selected>{$EQUIPMENT}</option>
                                            {/foreach}
                                        </select>
                                    {else}
                                       {$LISTVIEW_ENTRY->get($LISTVIEW_HEADERNAME)}
                                    {/if}

                                {else}
                                    {if $LISTVIEW_HEADER->getFieldDataType() eq 'double'}
                                        {decimalFormat($LISTVIEW_ENTRY->get($LISTVIEW_HEADERNAME))}
                                    {else}
										{if $LISTVIEW_HEADERNAME eq 'total_estimated_personnel' || $LISTVIEW_HEADERNAME eq 'total_estimated_vehicles'}
											<span>{$LISTVIEW_ENTRY->get($LISTVIEW_HEADERNAME)}</span>
										{else}
                                        {$LISTVIEW_ENTRY->get($LISTVIEW_HEADERNAME)}
                                    {/if}
				{/if}
				{/if}
				{if $LISTVIEW_HEADER@last}
				</td>
                                <td nowrap class="{$WIDTHTYPE}">
                                    <div class="actions pull-right">
                                        <span class="actionImages">
                                            <a href="{$LISTVIEW_ENTRY->getFullDetailViewUrl()}"><i title="{vtranslate('LBL_SHOW_COMPLETE_DETAILS', $MODULE)}" class="icon-th-list alignMiddle"></i></a>&nbsp;
                                            {if $IS_MODULE_EDITABLE}{* && $CURRENT_USER_MODEL->getExtraPermission($LISTVIEW_KEY)*}
                                                {if isPermitted($MODULE_MODEL->get('name'), 'EditView', $LISTVIEW_ENTRY->getId()) eq 'yes'}
                                                    <a href='{$LISTVIEW_ENTRY->getEditViewUrl()}'><i title="{vtranslate('LBL_EDIT', $MODULE)}" class="icon-pencil alignMiddle"></i></a>&nbsp;
                                                {/if}
                                            {/if}
                                            {if $IS_MODULE_DELETABLE}{* && $CURRENT_USER_MODEL->getExtraPermission($LISTVIEW_KEY)*}
                                                {if isPermitted($MODULE_MODEL->get('name'), 'Delete', $LISTVIEW_ENTRY->getId()) eq 'yes'}
                                                    <a class="deleteRecordButton"><i title="{vtranslate('LBL_DELETE', $MODULE)}" class="icon-trash alignMiddle"></i></a>
                                                {/if}
                                            {/if}
                                            {if $LocalDispatch eq 'true'}
                                                <a href="javascript:void(0)" class="calldata" data-id="{$LISTVIEW_ENTRY->getId()}" style="margin-left:2px;">
                                                    <i title="Call" class="icon-ok alignMiddle"></i>
                                                </a>
                                            {/if}
                                        </span>
                                    </div>
                                </td>
				{/if}
			</td>
                        {/if}
			{/foreach}
                        {if $LocalDispatch eq 'true'}
                            <td></td>
                        {/if}
		</tr>
		{/foreach}
	</table>

<!--added this div for Temporarily -->
{if $LISTVIEW_ENTRIES_COUNT eq '0'}
    <table class="emptyRecordsDiv">
        <tbody>
            <tr>
                <td>
                    {if $LocalDispatch eq 'true'}
                        {vtranslate('No Tasks for the selected date', $MODULE)}
                    {else}
                        {assign var=SINGLE_MODULE value="SINGLE_$MODULE"}
                        {vtranslate('LBL_NO')} {vtranslate($MODULE, $MODULE)} {vtranslate('LBL_FOUND')}.{if $IS_MODULE_EDITABLE} {vtranslate('LBL_CREATE')} <a href="{$MODULE_MODEL->getCreateRecordUrl()}">{vtranslate($SINGLE_MODULE, $MODULE)}</a>{/if}
                    {/if}
                </td>
            </tr>
        </tbody>
    </table>
{/if}
</div>
</div>
{if $LocalDispatchActuals eq 'true'}
	<div class="hide">
		<div>
			<select class="defaultEmployeesRoles" data-placeholder="Role">
			{foreach key=KEY item=ROLE from=$PERSONNEL_ROLES}
				<option value="{$KEY}">{$ROLE}</option>
			{/foreach}
			</select>
		</div>
		<div>
			<select class="defaultAssignedEmployees" data-placeholder="Available Employee"></select>
		</div>
	</div>
	<div class="divDeAbajo accordion hide" style="padding: 0 20px;">
		<div class="row">
		</div>
		<div class="row accordion-group">
			<div class="accordion-heading">
				<a class="accordion-toggle" data-toggle="collapse" href="#collapseUno">Personnel Assigned to Task</a>
			</div>
			<div id="collapseUno" class="accordion-body collapse" style="/*padding-bottom:5%;*/ overflow-y: scroll;">
				<div class="accordion-inner employeesassignedtotask" style="padding:1%;">
				</div>
			</div>
		</div>
		<div class="row accordion-group">
			<div class="accordion-heading">
				<a class="accordion-toggle" data-toggle="collapse" href="#collapseDos">CPUs</a>
			</div>
			<div id="collapseDos" class="accordion-body collapse" style="overflow-y:scroll;">
				<div class="accordion-inner cpus" style="padding:1%;">
				</div>
			</div>
		</div>
		<div class="row accordion-group">
			<div class="accordion-heading">
				<a class="accordion-toggle" data-toggle="collapse" href="#collapseTres">Equipment</a>
			</div>
			<div id="collapseTres" class="accordion-body collapse" style="overflow-y:scroll;">
				<div class="accordion-inner equipments" style="padding:1%;">
				</div>
			</div>
		</div>
		<span class="pull-right" style="margin-bottom:1%;">
			<button class="btn btn-success SaveActuals" type="submit"><strong>Save</strong></button>
			<a id="cancelLink" type="reset">Cancel</a>
		</span>
	</div>
	<input type="hidden" value="{$CURRENT_USER_MODEL->get('date_format')}" id="hiddenDateFormat">
{/if}
</div>
 
</div><!-- close div root-div -->
</div><!-- close div listViewContents -->
<br style="clear: both;" />
{if $LocalDispatchActuals eq 'true'}
    <input type="hidden" value="{$LocalDispatchActuals}" id="localDispatchActuals">
    <style>
    {literal}
        .accordion-inner{padding:0;}
        .row-fluid > .employees-tables,
        .row-fluid > .vehicles-tables,
        .row-fluid > .vendors-tables{
            resize: vertical;
        }
        .tdwrapp{
            max-height: 75px;
            white-space: nowrap;
            overflow:hidden;
        }
        a.ccordion-inner{
            height: 100% !important;
        }
    {/literal}
    </style>
{/if}
{/strip}
