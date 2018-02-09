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

    
<div class='container-fluid editViewContainer'>
	<form class="form-horizontal recordEditView" id="EditView" name="EditView" method="post" action="index.php" enctype="multipart/form-data">
		{assign var=WIDTHTYPE value=$USER_MODEL->get('rowheight')}
		{if !empty($PICKIST_DEPENDENCY_DATASOURCE)}
			<input type="hidden" name="picklistDependency" value='{Vtiger_Util_Helper::toSafeHTML($PICKIST_DEPENDENCY_DATASOURCE)}' />
		{/if}
		{assign var=QUALIFIED_MODULE_NAME value={$MODULE}}
		{assign var=IS_PARENT_EXISTS value=strpos($MODULE,":")}
		{if $IS_PARENT_EXISTS}
			{assign var=SPLITTED_MODULE value=":"|explode:$MODULE}
			<input type="hidden" name="module" value="{$SPLITTED_MODULE[1]}" />
			<input type="hidden" name="parent" value="{$SPLITTED_MODULE[0]}" />
		{else}
			<input type="hidden" name="module" value="{$MODULE}" />
		{/if}
		<input type="hidden" name="action" value="Save" />
		<input type="hidden" name="record" value="{$RECORD_ID}" />
                <input type="hidden" name="mode" value="edit" />
		<input type="hidden" name="defaultCallDuration" value="{$USER_MODEL->get('callduration')}" />
		<input type="hidden" name="defaultOtherEventDuration" value="{$USER_MODEL->get('othereventduration')}" />
		{if $IS_RELATION_OPERATION }
			<input type="hidden" name="sourceModule" value="{$SOURCE_MODULE}" />
			<input type="hidden" name="sourceRecord" value="{$SOURCE_RECORD}" />
			<input type="hidden" name="relationOperation" value="{$IS_RELATION_OPERATION}" />
		{/if}
		<div class="contentHeader row-fluid">
		{assign var=SINGLE_MODULE_NAME value='SINGLE_'|cat:$MODULE}
		{if $RECORD_ID neq ''}
			<h3 class="span8 textOverflowEllipsis" title="{vtranslate('LBL_EDITING', $MODULE)} {vtranslate($SINGLE_MODULE_NAME, $MODULE)} {$RECORD_STRUCTURE_MODEL->getRecordName()}">{vtranslate('LBL_EDITING', $MODULE)} {vtranslate($SINGLE_MODULE_NAME, $MODULE)} - {$RECORD_STRUCTURE_MODEL->getRecordName()}</h3>
		{else}
			<h3 class="span8 textOverflowEllipsis">{vtranslate('LBL_CREATING_NEW', $MODULE)} {vtranslate($SINGLE_MODULE_NAME, $MODULE)}</h3>
		{/if}
			<span class="pull-right">
				<button class="btn btn-success" type="submit"><strong>{vtranslate('LBL_SAVE', $MODULE)}</strong></button>
				<a class="cancelLink" type="reset" onclick="javascript:window.history.back();">{vtranslate('LBL_CANCEL', $MODULE)}</a>
			</span>
		</div>
                        
                <input type="hidden" id="hiddenFields" name="hiddenFields" value="{$HIDDEN_FIELDS}" />
                                       
		{foreach key=BLOCK_LABEL item=BLOCK_FIELDS from=$RECORD_STRUCTURE name="EditViewBlockLevelLoop"}
			{if $BLOCK_FIELDS|@count lte 0}{continue}{/if}
			<table class="table table-bordered blockContainer showInlineTable equalSplit{if is_array($HIDDEN_BLOCKS)}{if in_array($BLOCK_LABEL, $HIDDEN_BLOCKS)} hide{/if}{/if}">
                <thead>
			<tr>
				<th class="blockHeader" colspan="4">{vtranslate($BLOCK_LABEL, $MODULE)}</th>
			</tr>
                </thead>
                <tbody>
			<tr>
			{assign var=COUNTER value=0}
			{foreach key=FIELD_NAME item=FIELD_MODEL from=$BLOCK_FIELDS name=blockfields}

				{assign var="isReferenceField" value=$FIELD_MODEL->getFieldDataType()}
				{if $isReferenceField eq 'reference' && count($FIELD_MODEL->getReferenceList()) < 1}{continue}{/if}
				{if $FIELD_MODEL->get('uitype') eq "20" or $FIELD_MODEL->get('uitype') eq "19"}
					{if $COUNTER eq '1'}
                                    <td class="{$WIDTHTYPE}"></td><td class="{$WIDTHTYPE}"></td>
                                </tr>
                                <tr>
						{assign var=COUNTER value=0}
					{/if}
				{/if}
				{if $COUNTER eq 2}
                            </tr>
                            <tr>
					{assign var=COUNTER value=1}
				{else}
					{assign var=COUNTER value=$COUNTER+1}
				{/if}
				<td class="fieldLabel {$WIDTHTYPE}">
					{if $isReferenceField neq "reference"}<label class="muted pull-right marginRight10px">{/if}
						{if $FIELD_MODEL->isMandatory() eq true && $isReferenceField neq "reference"} <span class="redColor">*</span> {/if}
						{if $isReferenceField eq "reference"}
							{assign var="REFERENCE_LIST" value=$FIELD_MODEL->getReferenceList()}
							{assign var="REFERENCE_LIST_COUNT" value=count($REFERENCE_LIST)}
							{if $REFERENCE_LIST_COUNT > 1}
								{assign var="DISPLAYID" value=$FIELD_MODEL->get('fieldvalue')}
								{assign var="REFERENCED_MODULE_STRUCT" value=$FIELD_MODEL->getUITypeModel()->getReferenceModule($DISPLAYID)}
								{if !empty($REFERENCED_MODULE_STRUCT)}
									{assign var="REFERENCED_MODULE_NAME" value=$REFERENCED_MODULE_STRUCT->get('name')}
								{/if}
								<span class="pull-right">
									{if $FIELD_MODEL->isMandatory() eq true} <span class="redColor">*</span> {/if}
                                    <select id="{$MODULE}_editView_fieldName_{$FIELD_MODEL->getName()}_dropDown" class="chzn-select referenceModulesList streched" style="width:160px;">
										<optgroup>
											{foreach key=index item=value from=$REFERENCE_LIST}
												<option value="{$value}" {if $value eq $REFERENCED_MODULE_NAME} selected {/if}>{vtranslate($value, $MODULE)}</option>
											{/foreach}
										</optgroup>
									</select>
								</span>
							{else}
								<label class="muted pull-right marginRight10px">{if $FIELD_MODEL->isMandatory() eq true} <span class="redColor">*</span> {/if}{vtranslate($FIELD_MODEL->get('label'), $MODULE)}</label>
							{/if}
						{elseif $FIELD_MODEL->get('uitype') eq "83"}
							{include file=vtemplate_path($FIELD_MODEL->getUITypeModel()->getTemplateName(),$MODULE) COUNTER=$COUNTER MODULE=$MODULE}
						{else}
							{vtranslate($FIELD_MODEL->get('label'), $MODULE)}
						{/if}
					{if $isReferenceField neq "reference"}</label>{/if}
				</td>
				{if $FIELD_MODEL->get('uitype') neq "83"}
					<td class="fieldValue {$WIDTHTYPE}" {if $FIELD_MODEL->get('uitype') eq '19' or $FIELD_MODEL->get('uitype') eq '20'} colspan="3" {assign var=COUNTER value=$COUNTER+1} {/if}>
						<div class="row-fluid">
							<span class="span10">
								{include file=vtemplate_path($FIELD_MODEL->getUITypeModel()->getTemplateName(),$MODULE) BLOCK_FIELDS=$BLOCK_FIELDS}
							</span>
						</div>
					</td>
				{/if}
				{if $BLOCK_FIELDS|@count eq 1 and $FIELD_MODEL->get('uitype') neq "19" and $FIELD_MODEL->get('uitype') neq "20" and $FIELD_MODEL->get('uitype') neq "30" and $FIELD_MODEL->get('name') neq "recurringtype"}
					<td class="{$WIDTHTYPE}"></td><td class="{$WIDTHTYPE}"></td>
				{/if}
				{if $MODULE eq 'Events' && $BLOCK_LABEL eq 'LBL_EVENT_INFORMATION' && $smarty.foreach.blockfields.last }
					{include file=vtemplate_path('uitypes/FollowUp.tpl',$MODULE) COUNTER=$COUNTER}
				{/if}
                                {include file=vtemplate_path('SequencedGuestEditBlocks.tpl', $MODULE) BLOCK_LABEL=$BLOCK_LABEL}
			{/foreach}
		{* adding additional column for odd number of fields in a block *}
		{if $BLOCK_FIELDS|@end eq true and $BLOCK_FIELDS|@count neq 1 and $COUNTER eq 1}
			<td class="fieldLabel {$WIDTHTYPE}"></td><td class="{$WIDTHTYPE}"></td>
		{/if}
			</tr>
</tbody>
			</table>
			<br>
		{/foreach}
                {if $ORDERS_COUNT > 0}
					<div class="relatedContents tripOrders contents-bottomscroll" style="min-height:550px;">
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
										.trips-orders a {
											text-decoration:underline;
										}
                    
							{/literal}
							</style>
							<div class="trips-orders">
								<table id="orders_list" class="table table-bordered listViewEntriesTable" style="margin-bottom: 1%;">
									{foreach item=MINIARR from=$ORDERS_ARRAY name=foo}
										<tbody data-sequence="{$MINIARR.orders_sequence}" data-id="{$MINIARR.ordersid}" data-orderid="{$MINIARR.ordersid}">
											<tr data-tr="firstHeader">
												<th id="co0" headers="blank">{if $smarty.foreach.foo.iteration == 1}<input type="checkbox" id="selectall">{/if}</th>
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
												<td headers="co0"><input type="checkbox" class="asoc"></td>
												<td headers="co1"><a target="_blank" href="index.php?module=Orders&view=Detail&record={$MINIARR.ordersid}">{$MINIARR.order_no}</a></td>
												<td headers="co2"><input type="hidden" value="{$MINIARR.otherstatus}" id="statusajaxvalue"><select class="statusajax" style="width:100%;min-width:100px;"><option value="blank">  </option> {foreach item=LDD_STATUS from=$ORDERS_STATUS}  <option {if $LDD_STATUS eq $MINIARR.otherstatus} selected {/if} value="{$LDD_STATUS}">{$LDD_STATUS}</option>  {/foreach}</select></td>
												<td headers="co3">{$MINIARR.origin_city}</td>
												<td headers="co4">{$MINIARR.origin_state}</td>
												<td headers="co5">{$MINIARR.pudate}</td>
												<td headers="co6"><div class="input-append row-fluid" style="  min-width: 110px;"><div class="span12 row-fluid date"><input id="planned_load_date" type="text" class="dateField" name="planned_load_date" data-date-format="{$MINIARR.dateformat}" type="text" value="{$MINIARR.planned_load_date}" style="width:75px;"/><span class="add-on"><i class="icon-calendar"></i></span></div></div></td>
												<td headers="co7">{$MINIARR.delivery_date}</td>
												<td headers="co8"><div class="input-append row-fluid" style="  min-width: 110px;"><div class="span12 row-fluid date"><input id="planned_delivery_date" type="text" class="dateField" name="planned_delivery_date" data-date-format="{$MINIARR.dateformat}" type="text" value="{$MINIARR.planned_delivery_date}" style="width:75px;"/><span class="add-on"><i class="icon-calendar"></i></span></div></div></td>
												<td headers="co9">{$MINIARR.account_name}</td>
												<td headers="co10">{$MINIARR.origin_agent}</td>
												<td headers="co11"><input type="checkbox" class="sit" {if $MINIARR.sit eq '1'}checked{/if}></td>
											</tr>
											<tr data-tr="secondHeader">
												<th id="co0" headers="blank">&nbsp;</th>
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
												<td headers="co0">&nbsp;</td>
												<td headers="co1">{$MINIARR.ship_lastname}</td>
												<td headers="co2">{$MINIARR.est_weight}</td>
												<td headers="co3">{$MINIARR.dest_city}</td>
												<td headers="co4">{$MINIARR.dest_state}</td>
												<td headers="co5">{$MINIARR.load_to_date}</td>
												<td headers="co6"><div class="input-append row-fluid" style="  min-width: 110px;"><div class="span12 row-fluid date"><input id="actual_load_date" type="text" class="dateField" name="actual_load_date" data-date-format="{$MINIARR.dateformat}" type="text" value="{$MINIARR.actual_load_date}" style="width:75px;"/><span class="add-on"><i class="icon-calendar"></i></span></div></div></td>
												<td headers="co7">{$MINIARR.delivery_to_date}</td>
												<td headers="co8"><div class="input-append row-fluid" style="  min-width: 110px;"><div class="span12 row-fluid date"><input id="actual_delivery_date" type="text" class="dateField" name="actual_delivery_date" data-date-format="{$MINIARR.dateformat}" type="text" value="{$MINIARR.actual_delivery_date}" style="width:75px;"/><span class="add-on"><i class="icon-calendar"></i></span></div></div></td>
												<td headers="co9"><input type="text" value="{$MINIARR.actual_weight}" id="actual_weight" style="width: 80%;"></td>
												<td headers="co10">{$MINIARR.dest_agent}</td>
												<td headers="co11">{$MINIARR.linehaul}</td>
											</tr>
											<tr><td colspan="12" style="padding: 0;background-color: black;border: none;line-height: 3px;">&nbsp;</td></tr>
										</tbody>
									{/foreach}
								</table>
							</div>
						</div>
					</div>
                {else}
                    <div class="relatedContents contents-bottomscroll">
                        
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
                                      <td style="height: 40px;text-align: center;vertical-align: middle;">{vtranslate('Please save the trip before adding an order to it', $MODULE_NAME)}</td>
                                        
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
		{include file=vtemplate_path('GuestEditBlocks.tpl', $MODULE)}
{/strip}
