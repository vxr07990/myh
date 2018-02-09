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
    {assign var=SELECTED_FIELDS value=$CUSTOMVIEW_MODEL->getSelectedFields()}
    <div class="container-fluid">
        <form class="form-inline" id="CustomView" name="CustomView" method="post" action="index.php">
			<input type="hidden" name="iscalendar" value="{$iscalendar}" />
            <input type=hidden name="record" id="record" value="{$RECORD_ID}" />
            <input type="hidden" name="module" value="{$MODULE}" />
            <input type="hidden" name="action" value="Save" />
            <input type="hidden" name="source_module" value="{$SOURCE_MODULE}"/>
            <input type="hidden" id="stdfilterlist" name="stdfilterlist" value=""/>
            <input type="hidden" id="advfilterlist" name="advfilterlist" value=""/>
            <input type="hidden" id="status" name="status" value="{$CV_PRIVATE_VALUE}"/>
            <input type="hidden" id="is_vanline_user" value="{$IS_VANLINE_USER}"/>
			<div class="CustomFilterViewTitle">
				<h3>{vtranslate('LBL_CREATE_VIEW',$MODULE)}</h3>
			</div>
			<hr>
            <input type="hidden" id="sourceModule" value="{$SOURCE_MODULE}">
            <input type="hidden" name="date_filters" data-value='{Vtiger_Util_Helper::toSafeHTML(ZEND_JSON::encode($DATE_FILTERS))}' />
			<input type="hidden" name="hidden_participating_agents" value="{$HIDDEN_PARTICIPATING_AGENTS}">
                <input type="hidden" id="resourceWidthValue" name="resourcewidth" value="{$DEFAULT_RESOURCE_WIDTH}" >{*OT5300*}
                <div class="filterBlocksAlignment">
				<br>
                <div class="row-fluid">
                    <h4 class="filterHeaders">{vtranslate('LBL_BASIC_DETAILS',$MODULE)} :</h4>
                </div>
				<br>
                <div class="row-fluid">
                    <label><span class="redColor">*</span> {vtranslate('LBL_VIEW_NAME',$MODULE)}&nbsp;</label>
                    <input type="text" id="viewname" data-validation-engine='validate[required]' name="viewname" value="{$CUSTOMVIEW_MODEL->get('viewname')}">&nbsp;&nbsp;
                    {if getenv('INSTANCE_NAME') == 'sirva'}
                    {if $ADMIN_USER eq 'on'}                                                                                                                                                              &nbsp;
                        <label class="checkbox"><input type="hidden" name="setdefault" value="0"><input id="setdefault" type="checkbox" name="setdefault" value="1" {if $CUSTOMVIEW_MODEL->isDefault()} checked="checked"{/if}>{vtranslate('LBL_SET_AS_DEFAULT',$MODULE)}</label>&nbsp;&nbsp;&nbsp;
                    {/if}
                    {else}
                        <label class="checkbox defaultCheckbox"><input type="hidden" name="setdefault" value="0"><input id="setdefault" type="checkbox" name="setdefault" value="1" {if $CUSTOMVIEW_MODEL->isDefault()} checked="checked"{/if}>{vtranslate('LBL_SET_AS_DEFAULT',$MODULE)}</label>&nbsp;&nbsp;&nbsp;
                    {/if}
                    <label class="checkbox"><input type="hidden" name="setmetrics" value="0"><input id="setmetrics" name="setmetrics" type="checkbox" value="1" {if $CUSTOMVIEW_MODEL->get('setmetrics') eq '1'} checked="checked"{/if}>{vtranslate('LBL_LIST_IN_METRICS',$MODULE)}</label>&nbsp;&nbsp;&nbsp;
                    <label class="checkbox hide"><input type="hidden" name="status" value="1"><input id="status" name="status" type="checkbox" {if $CUSTOMVIEW_MODEL->isSetPublic()} value="{$CUSTOMVIEW_MODEL->get('status')}" checked="checked" {else} value="{$CV_PENDING_VALUE}" {/if}>{vtranslate('LBL_SET_AS_PUBLIC',$MODULE)}</label>&nbsp;&nbsp;&nbsp;

                    {*CHANGE WITH NEW PERMISSIONS*}
                    {*OLD SECURITIES {if $DEPTH eq '6' OR $DEPTH eq '7'}*}
                    <label class="checkbox"><input type="hidden" name="assignToAgent" value="0"><input id="assignToAgent" name="assignToAgent" type="checkbox" value="1" {if $CUSTOMVIEW_MODEL->isAgent()}checked="checked"{/if}{*if $CUSTOMVIEW_MODEL->get('cvid') != ''} readonly onclick="return false;"{/if*}>{vtranslate('LBL_ASSIGN_TO_AGENT',$MODULE)}</label>&nbsp;&nbsp;&nbsp;
                    <label {if $CUSTOMVIEW_MODEL->isAgent()}{else}class="hide" {/if}id="assignedAgentLabel"><select name="assignedAgent" class="chzn-select">
                        <option value="">{vtranslate('LBL_SELECT_OPTION','Vtiger')}</option>
                        {foreach key=AGENTID item=AGENTNAME from=$AVAILABLE_AGENTS}
                            <option value="{$AGENTID}" {if $CUSTOMVIEW_MODEL->isAgent() AND $CUSTOMVIEW_MODEL->get('agentmanager_id') eq $AGENTID}selected{/if}>{vtranslate($AGENTNAME,$MODULE)}</option>
                        {/foreach}
                    </select></label>
                    {*{/if}*}

                </div>
                <br>
                <h4 class="filterHeaders">{vtranslate('LBL_CHOOSE_COLUMNS',$MODULE)} ({vtranslate('LBL_MAX_NUMBER_FILTER_COLUMNS')}) :</h4>
                <br>
				<div class="columnsSelectDiv">
                    {assign var=MANDATORY_FIELDS value=array()}
                    <select data-placeholder="{vtranslate('LBL_ADD_MORE_COLUMNS',$MODULE)}" multiple class="select2-container columnsSelect" id="viewColumnsSelect">
                        {foreach key=BLOCK_LABEL item=BLOCK_FIELDS from=$RECORD_STRUCTURE}
                            <optgroup label='{vtranslate($BLOCK_LABEL, $SOURCE_MODULE)}'>
                                {foreach key=FIELD_NAME item=FIELD_MODEL from=$BLOCK_FIELDS}
                                    {if $FIELD_MODEL->isMandatory()}
                                        {array_push($MANDATORY_FIELDS, $FIELD_MODEL->getCustomViewColumnName())}
                                    {/if}
                                    <option value="{$FIELD_MODEL->getCustomViewColumnName()}" data-field-name="{$FIELD_NAME}"
                                            {if in_array($FIELD_MODEL->getCustomViewColumnName(), $SELECTED_FIELDS)}
                                                selected
                                            {/if}
                                            >{vtranslate($FIELD_MODEL->get('label'), $SOURCE_MODULE)}
                            {if $FIELD_MODEL->isMandatory() eq true} <span>*</span> {/if}
                            </option>
                        {/foreach}
                        {if $SOURCE_MODULE == 'Documents' && $BLOCK_LABEL == 'LBL_NOTE_INFORMATION'}
                            <option value="DocumentsRelatedTo" {if in_array("DocumentsRelatedTo", $SELECTED_FIELDS)}selected{/if}>Related To</option>
                        {/if}
                        </optgroup>
                    {/foreach}
                        {foreach key=BLOCK_LABEL item=BLOCK_FIELDS from=$EXTRA_BLOCKS}
                            <optgroup label='{vtranslate($BLOCK_LABEL, $BLOCK_FIELDS.module)}'>
                                {foreach key=FIELD_NAME item=FIELD_DATA from=$BLOCK_FIELDS.fields}
                                    <option value="{$FIELD_DATA.value}" data-field-name="{$FIELD_NAME}"
                                            {if in_array($FIELD_DATA.value, $SELECTED_FIELDS)}
                                                selected
                                            {/if}
                                    >{vtranslate($FIELD_DATA.name, $BLOCK_FIELDS.module)}
                            </option>
                                {/foreach}
                        </optgroup>
                        {/foreach}
				{*Required to include event fields for columns in calendar module advanced filter*}
                        {foreach key=GUEST_MODULE_NAME item=GUEST_MODULE_INFO from=$GUEST_BLOCK_DATA}
                        {foreach key=BLOCK_LABEL item=BLOCK_FIELDS from=$GUEST_MODULE_INFO['guestBlocks']}
                            <optgroup label='{vtranslate($BLOCK_LABEL, $GUEST_MODULE_NAME)}'>
                                {foreach key=FIELD_NAME item=FIELD_MODEL from=$BLOCK_FIELDS}
                                    {*
                                    {if $FIELD_MODEL->isMandatory()}
                                        {array_push($MANDATORY_FIELDS, $FIELD_MODEL->getCustomViewColumnName())}
                                    {/if}
                                    *}
                                    <option value="{$FIELD_MODEL->getCustomViewColumnName()}" data-field-name="{$FIELD_NAME}"
                                            {if in_array($FIELD_MODEL->getCustomViewColumnName(), $SELECTED_FIELDS)}
                                                selected
                                            {/if}
                                    >{vtranslate($FIELD_MODEL->get('label'), $GUEST_MODULE_NAME)}
                                        {if $FIELD_MODEL->isMandatory() eq true} <span>*</span> {/if}
                                    </option>
                                {/foreach}
                            </optgroup>
                        {/foreach}
                        {/foreach}

                {foreach key=BLOCK_LABEL item=BLOCK_FIELDS from=$EVENT_RECORD_STRUCTURE}
					<optgroup label='{vtranslate($BLOCK_LABEL, 'Events')}'>
					{foreach key=FIELD_NAME item=FIELD_MODEL from=$BLOCK_FIELDS}
						{if $FIELD_MODEL->isMandatory()}
							{array_push($MANDATORY_FIELDS, $FIELD_MODEL->getCustomViewColumnName())}
						{/if}
						<option value="{$FIELD_MODEL->getCustomViewColumnName()}" data-field-name="{$FIELD_NAME}"
						{if in_array($FIELD_MODEL->getCustomViewColumnName(), $SELECTED_FIELDS)}
							selected
						{/if}
						>{vtranslate($FIELD_MODEL->get('label'), $SOURCE_MODULE)}
						{if $FIELD_MODEL->isMandatory() eq true} <span>*</span> {/if}
						</option>
					{/foreach}
					</optgroup>
				{/foreach}

                {*Required to include Orders fields for columns in OrdersTask module advanced filter*}
                {foreach key=BLOCK_LABEL item=BLOCK_FIELDS from=$ORDERS_RECORD_STRUCTURE}
                    <optgroup label='{vtranslate($BLOCK_LABEL, 'Orders')}'>
                        {foreach key=FIELD_NAME item=FIELD_MODEL from=$BLOCK_FIELDS}
                            {if $FIELD_MODEL->isMandatory()}
                                {array_push($MANDATORY_FIELDS, $FIELD_MODEL->getCustomViewColumnName())}
                            {/if}
                            <option value="{$FIELD_MODEL->getCustomViewColumnName()}" data-field-name="{$FIELD_NAME}"
                                    {if in_array($FIELD_MODEL->getCustomViewColumnName(), $SELECTED_FIELDS)}
                                        selected
                                    {/if}
                                    >({vtranslate('Orders','Orders')})  {vtranslate($FIELD_MODEL->get('label'), 'Orders')}
                        {if $FIELD_MODEL->isMandatory() eq true} <span>*</span> {/if}
                            </option>
                        {/foreach}
                </optgroup>
                {/foreach}


                {*Required to include Trips fields for columns in OrdersTask module advanced filter*}
                {foreach key=BLOCK_LABEL item=BLOCK_FIELDS from=$TRIPS_RECORD_STRUCTURE}
                    <optgroup label='{vtranslate($BLOCK_LABEL, 'Trips')}'>
                        {foreach key=FIELD_NAME item=FIELD_MODEL from=$BLOCK_FIELDS}
                            {if $FIELD_MODEL->isMandatory()}
                                {array_push($MANDATORY_FIELDS, $FIELD_MODEL->getCustomViewColumnName())}
                            {/if}
                            <option value="{$FIELD_MODEL->getCustomViewColumnName()}" data-field-name="{$FIELD_NAME}"
                                    {if in_array($FIELD_MODEL->getCustomViewColumnName(), $SELECTED_FIELDS)}
                                        selected
                                    {/if}
                                    >({vtranslate('Trips','Trips')})  {vtranslate($FIELD_MODEL->get('label'), 'Trips')}
                        {if $FIELD_MODEL->isMandatory() eq true} <span>*</span> {/if}
                    </option>
                {/foreach}
                </optgroup>
                {/foreach}


                {*Required to include Estimates fields for columns in OrdersTask module advanced filter*}
                {foreach key=BLOCK_LABEL item=BLOCK_FIELDS from=$ESTIMATES_RECORD_STRUCTURE}
                    <optgroup label='{vtranslate($BLOCK_LABEL, 'Estimates')}'>
                        {foreach key=FIELD_NAME item=FIELD_MODEL from=$BLOCK_FIELDS}
                            {if $FIELD_MODEL->isMandatory()}
                                {array_push($MANDATORY_FIELDS, $FIELD_MODEL->getCustomViewColumnName())}
                            {/if}
                            <option value="{$FIELD_MODEL->getCustomViewColumnName()}" data-field-name="{$FIELD_NAME}"
                                    {if in_array($FIELD_MODEL->getCustomViewColumnName(), $SELECTED_FIELDS)}
                                        selected
                                    {/if}
                                    >({vtranslate('Estimates','Estimates')})  {vtranslate($FIELD_MODEL->get('label'), 'Estimates')}
                        {if $FIELD_MODEL->isMandatory() eq true} <span>*</span> {/if}
                    </option>
                {/foreach}
                </optgroup>
                {/foreach}

                        {*Required to include Commission Plan Group fields for columns in OrdersTask module advanced filter*}
                        {foreach key=BLOCK_LABEL item=BLOCK_FIELDS from=$COMMISSIONPLANSFILTER_RECORD_STRUCTURE}
                            <optgroup label='{vtranslate($BLOCK_LABEL, 'CommissionPlansFilter')}'>
                                {foreach key=FIELD_NAME item=FIELD_MODEL from=$BLOCK_FIELDS}
                                    {if $FIELD_MODEL->isMandatory()}
                                        {array_push($MANDATORY_FIELDS, $FIELD_MODEL->getCustomViewColumnName())}
                                    {/if}
                                    <option value="{$FIELD_MODEL->getCustomViewColumnName()}" data-field-name="{$FIELD_NAME}"
                                        {if in_array($FIELD_MODEL->getCustomViewColumnName(), $SELECTED_FIELDS)}
                                            selected
                                        {/if}
                                    >({vtranslate('CommissionPlansFilter','CommissionPlansFilter')})  {vtranslate($FIELD_MODEL->get('label'), 'CommissionPlansFilter')}
                                        {if $FIELD_MODEL->isMandatory() eq true} <span>*</span> {/if}
                                    </option>
                                {/foreach}
                            </optgroup>
                        {/foreach}

                        {*Required to include ItemCodesMapping fields for columns in OrdersTask module advanced filter*}
                        {foreach key=BLOCK_LABEL item=BLOCK_FIELDS from=$ITEMCODESMAPPING_RECORD_STRUCTURE}
                            <optgroup label='{vtranslate($BLOCK_LABEL, 'ItemCodesMapping')}'>
                                {foreach key=FIELD_NAME item=FIELD_MODEL from=$BLOCK_FIELDS}
                                    {if $FIELD_MODEL->isMandatory()}
                                        {array_push($MANDATORY_FIELDS, $FIELD_MODEL->getCustomViewColumnName())}
                                    {/if}
                                    <option value="{$FIELD_MODEL->getCustomViewColumnName()}" data-field-name="{$FIELD_NAME}"
                                        {if in_array($FIELD_MODEL->getCustomViewColumnName(), $SELECTED_FIELDS)}
                                            selected
                                        {/if}
                                    >({vtranslate('ItemCodesMapping','ItemCodesMapping')})  {vtranslate($FIELD_MODEL->get('label'), 'ItemCodesMapping')}
                                        {if $FIELD_MODEL->isMandatory() eq true} <span>*</span> {/if}
                                    </option>
                                {/foreach}
                            </optgroup>
                        {/foreach}


                </select>
                <input type="hidden" name="columnslist" value='{ZEND_JSON::encode($SELECTED_FIELDS)}' />
                <input id="mandatoryFieldsList" type="hidden" value='{ZEND_JSON::encode($MANDATORY_FIELDS)}' />
            </div>
			<br>
			<div class="conditionsFromHere">
				<h4 class="filterHeaders">{vtranslate('LBL_CHOOSE_FILTER_CONDITIONS', $MODULE)} :</h4>
				<br>
				<div class="filterConditionsDiv">
					<div class="row-fluid">
						<span class="span12">
							{include file='AdvanceFilter.tpl'|@vtemplate_path}
						</span>
					</div>
				</div>

            {*Default Order*}

                <div class="row-fluid">
                    <h4 class="filterHeaders">{vtranslate('Default Sort Order',$MODULE)} :</h4>
                </div>
                <br>
                <div class="row-fluid">
                    <span class="span3">
                        <label><strong>{vtranslate('Sort Field',$MODULE)}&nbsp;&nbsp;</strong></label>
                        <select class="{if empty($NOCHOSEN)}chzn-select{/if} span3" name="sort_field">
                            <option value="">{vtranslate('LBL_SELECT_FIELD',$MODULE)}</option>
                            {foreach key=BLOCK_LABEL item=BLOCK_FIELDS from=$RECORD_STRUCTURE}
                                <optgroup label='{vtranslate($BLOCK_LABEL, $SOURCE_MODULE)}'>
                                    {foreach key=FIELD_NAME item=FIELD_MODEL from=$BLOCK_FIELDS}
                                        {assign var=FIELD_INFO value=$FIELD_MODEL->getFieldInfo()}
                                        {assign var=MODULE_MODEL value=$FIELD_MODEL->getModule()}
                                        <option value="{$FIELD_NAME}" data-fieldtype="{$FIELD_MODEL->getFieldType()}" data-field-name="{$FIELD_NAME}" {if {$CUSTOMVIEW_MODEL->get('sort_field')} eq $FIELD_NAME}selected{/if}>
                                            {vtranslate($FIELD_MODEL->get('label'), $SOURCE_MODULE)}
                                        </option>
                                    {/foreach}
                                </optgroup>
                            {/foreach}
                        </select>
                    </span>
                    <span class="span3">
                        <label><strong>{vtranslate('Sort Order',$MODULE)}&nbsp;&nbsp;</strong></label>
                        <select class="chzn-select span3" name="sort_order">
                            <option value="">{vtranslate('LBL_SELECT_OPTION',$MODULE)}</option>
                            <option value="ASC" {if {$CUSTOMVIEW_MODEL->get('sort_order')} eq 'ASC'}selected{/if}>ASC</option>
                            <option value="DESC" {if {$CUSTOMVIEW_MODEL->get('sort_order')} eq 'DESC'}selected{/if}>DESC</option>
                        </select>
                    </span>
                </div>
			</div>

                        {*OT5300*}
                        <br><br>
                        <div id="div-slider" class="hide">
                           <div class="row-fluid">
                               <span><label><strong>Resource window Default Width</strong></label></span>
                               <span style="margin-left:20px;margin-right:20px;">10</span>
                               <input style="margin-left:20px;" id="resourceWidthSlider" data-slider-id='resourceWidthSlider' type="text" data-slider-min="10" data-slider-max="50" data-slider-step="1" data-slider-value="{$DEFAULT_RESOURCE_WIDTH}"/>
                               <span style="margin-left:20px;">50</span>
                               <strong><span style="margin-left:20px;" id="sliderValLabel">Current Slider Value:
                                       <span id="sliderVal" style="margin-left:5px;">{$DEFAULT_RESOURCE_WIDTH}</span></span>
                               </strong>
                           </div>
                        </div>
                        {*OT5553*}
                        <br><br>
                        <div id="div-collapsed" class="hide">
                           <div class="row-fluid">
                               <span class="pull-left"><label><strong>Collapse Resource window by Default</strong></label></span>
                               <span class="span10">
                                   <input type="hidden" name="resourcecollapsed" value="{$DEFAULT_RESOURCE_COLLAPSED}">
                                   <input id="resourcecollapsed_checkbox" type="checkbox" {if $DEFAULT_RESOURCE_COLLAPSED eq 'yes'} checked="checked"{/if}>
                               </span>
                           </div>
                        </div>
        </div>

        <div class="filterActions">
            <a class="cancelLink pull-right" type="reset" onClick="window.location.reload()">{vtranslate('LBL_CANCEL', $MODULE)}</a>
            <button class="btn btn-success pull-right" id="customViewSubmit" type="submit"><strong>{vtranslate('LBL_SAVE', $MODULE)}</strong></button>
        </div>
    </form>
</div>
{/strip}
