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
 <style type="text/css">
   
@media print
{
    body * { visibility: hidden; }
    div.contents * { visibility: visible; }
    div.contents { position: absolute; top: 40px; left: 30px; }
}
/*
     .checkboxes-label{
        width: 14%;
        float: left;
        padding-left: 0%;
        padding-bottom: 1%;
        padding-top: 1%;
        background: #f7f7f9;
        color: #999999;
        line-height: 18px;
        border-right: 1px solid #ddd;
        border-left: 1px solid #ddd;
        text-align: right;
        padding-right: 1%;
}

.first-check{
    border-left: 0px solid #ddd;
}

.checkboxes-values{
    width: 8%;
    float: left;
    padding-left: 1%;
    padding-bottom: 1%;
    padding-top: 1%;
}*/

</style>

{strip}
    {assign var=EXTRA_BLOCKS_SETTINGS value=$RECORD_MODEL->getExtraBlockConfig()}
	{foreach key=BLOCK_LABEL_KEY item=FIELD_MODEL_LIST from=$RECORD_STRUCTURE}
    {if in_array($BLOCK_LABEL_KEY,array_keys($EXTRA_BLOCKS_SETTINGS))}
        {include file=vtemplate_path('ExtraBlockDetail.tpl',$MODULE) BLOCK_SETTING = $EXTRA_BLOCKS_SETTINGS[$BLOCK_LABEL_KEY] BLOCK_LABEL = $BLOCK_LABEL_KEY}
        {if $BLOCK_LABEL_KEY eq 'LBL_VEHICLES' && $IS_ACTIVE_ADDRESS}
            {include file=vtemplate_path('DetailBlock.tpl','OrdersTaskAddresses')}
        {/if}
    {else}
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
                         {if !$FIELD_MODEL->isViewableInDetailView()}
				 {continue}
			 {/if}
                         
                            {assign var=CHECKBOXES value=0}
                            {*if $FIELD_MODEL->get('name') eq "date_spread"}  
                                        {assign var=CHECKBOXES value=1}
                                        {assign var=CHECKBOXES_FIRST value=1}
                                        {assign var=COUNTER value=2}
                                {elseif $FIELD_MODEL->get('name') eq "include_saturday" || $FIELD_MODEL->get('name') eq "multiservice_date"}
                                        {assign var=CHECKBOXES value=1}
                                        {assign var=COUNTER value=0}
                                        {assign var=CHECKBOXES_FIRST value=0}
                                {elseif $FIELD_MODEL->get('name') eq "include_sunday"}
                                        {assign var=CHECKBOXES value=1}
                                        {assign var=COUNTER value=1}
                                        {assign var=CHECKBOXES_LAST value=1}
                                        {assign var=CHECKBOXES_FIRST value=0}
                            {/if*}
                         
                            
                         
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
					 </tr>
                                         {if $CHECKBOXES_FIRST eq 1}
                                             <tr style="padding:0px;">
                                          {else}
                                             <tr> 
                                          {/if}    
					{assign var=COUNTER value=1}
				{else}
					{assign var=COUNTER value=$COUNTER+1}
				 {/if}
                                 
                                 {if $CHECKBOXES eq 1}
                                     {if $CHECKBOXES_FIRST eq 1}
                                          <td colspan="4" style="padding:0px;">    
                                      {/if}  
                                      <div class="checkboxes-label  {if $CHECKBOXES_FIRST eq 1} first-check {/if}"> 
                                            {if $FIELD_MODEL->isMandatory() eq true && $isReferenceField neq "reference"} <span class="redColor">*</span> {/if}
                                            {vtranslate($FIELD_MODEL->get('label'), $MODULE)}
                                        </div>
                                        <div class="checkboxes-values">
                                                         <span class="value" data-field-type="{$FIELD_MODEL->getFieldDataType()}" {if $FIELD_MODEL->get('uitype') eq '19' or $FIELD_MODEL->get('uitype') eq '20' or $FIELD_MODEL->get('uitype') eq '21'} style="white-space:normal;" {/if}>
                                                       {include file=vtemplate_path($FIELD_MODEL->getUITypeModel()->getDetailViewTemplateName(),$MODULE_NAME) FIELD_MODEL=$FIELD_MODEL USER_MODEL=$USER_MODEL MODULE=$MODULE_NAME RECORD=$RECORD}
                                                </span>
                                        </div>
                                      {if $CHECKBOXES_LAST eq 1}
                                          </td>
                                       
                                        {assign var=COUNTER value=2}
                                      {/if}    
                                     
                                 {else}    
                                 
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
                                                {if $IS_AJAX_ENABLED && $FIELD_MODEL->isEditable() eq 'true' && ($FIELD_MODEL->getFieldDataType()!=Vtiger_Field_Model::REFERENCE_TYPE) && $FIELD_MODEL->isAjaxEditable() eq 'true'}{* && $CREATOR_PERMISSIONS eq 'true'*}
                                                        <span class="hide edit">
                                            {if $FIELD_MODEL->get('uitype') eq "14"}
                                                                {assign var="FIELD_INFO" value=Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($FIELD_MODEL->getFieldInfo()))}
                                                                {assign var="SPECIAL_VALIDATOR" value=$FIELD_MODEL->getValidator()}
                                                                {assign var=FIELD_VALUE value=$FIELD_MODEL->getEditViewDisplayValue($FIELD_MODEL->get('fieldvalue'))}
                                                                {assign var="TIME_FORMAT" value=$USER_MODEL->get('hour_format')}
                                                                <div class="input-append time">
                                                                    <input id="{$MODULE}_editView_fieldName_{$FIELD_MODEL->get('name')}" type="text" data-format="{$TIME_FORMAT}" class="custom-tp timepicker-default input-small" value="{$FIELD_VALUE}" name="{$FIELD_MODEL->getFieldName()}"
                                                                        data-validation-engine="validate[{if $FIELD_MODEL->isMandatory() eq true} required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]"   {if !empty($SPECIAL_VALIDATOR)}data-validator='{Zend_Json::encode($SPECIAL_VALIDATOR)}'{/if} data-fieldinfo='{$FIELD_INFO}' />
                                                                    <span class="add-on cursorPointer">
                                                                        <i class="icon-time"></i>
                                                                    </span>
                                                                </div>
                                                            {else}
                                                                {include file=vtemplate_path($FIELD_MODEL->getUITypeModel()->getTemplateName(),$MODULE_NAME) FIELD_MODEL=$FIELD_MODEL USER_MODEL=$USER_MODEL MODULE=$MODULE_NAME}
                                    
                                                        {/if}
                                    {if $FIELD_MODEL->getFieldDataType() eq 'multipicklist'}
                                       <input type="hidden" class="fieldname" value='{$FIELD_MODEL->get('name')}[]' data-prev-value='{$FIELD_MODEL->getDisplayValue($FIELD_MODEL->get('fieldvalue'))}' />
                                    {else}
                                        <input type="hidden" class="fieldname" value='{$FIELD_MODEL->get('name')}' data-prev-value='{Vtiger_Util_Helper::toSafeHTML($FIELD_MODEL->getDisplayValue($FIELD_MODEL->get('fieldvalue')))}' />
                                    {/if}
                                                        </span>
                                                {/if}
                                        </td>
                                 
                                {/if}
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
    {/if}
	{/foreach}
<!-- TimeSheets Agregado -->
{if $TieneRelatedTS eq 'si'}
    <div class="relatedContents contents-bottomscroll">
        <div class="bottomscroll-div" style="width: 818px;">
            <table class="table table-bordered listViewEntriesTable">
                <thead>
                    <tr class="listViewHeaders">
                        <th nowrap=""><a href="javascript:void(0);" class="relatedListHeaderValues" data-nextsortorderval="ASC" data-fieldname="timesheet_id">TimeSheet ID</a></th>
                        <th nowrap=""><a href="javascript:void(0);" class="relatedListHeaderValues" data-nextsortorderval="ASC" data-fieldname="employee_role">Employee Role</a></th>
                        <th nowrap=""><a href="javascript:void(0);" class="relatedListHeaderValues" data-nextsortorderval="ASC" data-fieldname="employee_name">Employee Name</a></th>
                        <th nowrap=""><a href="javascript:void(0);" class="relatedListHeaderValues" data-nextsortorderval="ASC" data-fieldname="actual_start_date">Actual Date</a></th>
                        <th nowrap=""><a href="javascript:void(0);" class="relatedListHeaderValues" data-nextsortorderval="ASC" data-fieldname="actual_start_hour">Actual Start Hour</a></th>
                        <th nowrap=""><a href="javascript:void(0);" class="relatedListHeaderValues" data-nextsortorderval="ASC" data-fieldname="actual_end_hour">Actual End Hour</a></th>
                        <th nowrap=""><a href="javascript:void(0);" class="relatedListHeaderValues" data-nextsortorderval="ASC" data-fieldname="timeoff">Time Off</a></th>
                        <th colspan="2" nowrap=""><a href="javascript:void(0);" class="relatedListHeaderValues" data-nextsortorderval="ASC" data-fieldname="total_hours">Total Worked Hours</a></th>
                    </tr>
                </thead>
                <tbody>
                    {foreach item=MINIARR from=$TimeSheets}
                        <tr class="listViewEntries" data-id="{$MINIARR.timesheetsid}" data-recordurl="index.php?module=TimeSheets&amp;view=Detail&amp;record={$MINIARR.timesheetsid}">
                            <td class="" data-field-type="string" nowrap=""><a href="index.php?module=TimeSheets&amp;view=Detail&amp;record={$MINIARR.timesheetsid}">{$MINIARR.timesheet_id}</a></td>
                            <td class="" data-field-type="string" nowrap="">{$MINIARR.employee_role}</td>
                            <td class="" data-field-type="string" nowrap=""><a href="index.php?module=TimeSheets&amp;view=Detail&amp;record={$MINIARR.timesheetsid}">{$MINIARR.employee_name}</a></td>
                            <td class="" data-field-type="date" nowrap="">{$MINIARR.actual_start_date}</td>
                            <td class="" data-field-type="time" nowrap="">{DateTimeField::convertToUserTimeZone($MINIARR.actual_start_hour)->format('h:i A')}</td>
                            <td class="" data-field-type="time" nowrap="">{DateTimeField::convertToUserTimeZone($MINIARR.actual_end_hour)->format('h:i A')}</td>
                            <td class="" data-field-type="double" nowrap="">{$MINIARR.timeoff}</td>
                            <td class="" data-field-type="double" nowrap="">{$MINIARR.total_hours}</td>
                        </tr>
                    {/foreach}
                </tbody>
            </table>
        </div>
    </div>
{/if}
{/strip}
