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
    {foreach key=index item=jsModel from=$SCRIPTS}
        <script type="{$jsModel->getType()}" src="{$jsModel->getSrc()}"></script>
    {/foreach}
    <div class="modelContainer">
<div class="modal-header contentsBackground">
	<button class="close" aria-hidden="true" data-dismiss="modal" type="button" title="{vtranslate('LBL_CLOSE')}">x</button>
    <h3>{vtranslate('LBL_INTL_QUOTE', $MODULE)}</h3>
</div>
<form class="form-horizontal recordEditView" id="quickCreate" name="QuickCreate" method="post" action='index.php'>
<button class="btn" id='submitQuoteBtn' type='submit'>
	<strong>Send Quote</strong>
</button>
    <button class="btn btn-success" type='submit' id='saveQuote'>
	<strong>Save</strong>
</button>
    {if !empty($PICKIST_DEPENDENCY_DATASOURCE)}
        <input type="hidden" name="picklistDependency" value='{Vtiger_Util_Helper::toSafeHTML($PICKIST_DEPENDENCY_DATASOURCE)}' />
    {/if}
    <input type="hidden" name="module" value="IntlQuote">
	<input type="hidden" name="action" value="Save">
	<input type="hidden" name="record" value="{$RECORD_ID}">

	<div class="quickCreateContent">
		<div class="modal-body">
			{foreach key=BLOCK_LABEL item=FIELD_MODELS from=$REGISTER_QUOTES_FIELDS}
                <table class="table table-bordered equalSplit">
					<tr>
						<th class="blockHeader" colspan="4">
								&nbsp;&nbsp;{vtranslate({$BLOCK_LABEL},{$MODULE})}
						</th>
					</tr>
					<tr>
					{assign var=COUNTER value=0}
                    {foreach key=FIELD_NUM item=FIELD_MODEL from=$FIELD_MODELS name=blockfields}
                        {assign var="isReferenceField" value=$FIELD_MODEL->getFieldDataType()}
                        {assign var="refrenceList" value=$FIELD_MODEL->getReferenceList()}
                        {assign var="refrenceListCount" value=count($refrenceList)}
                        {assign var="FIELD_NAME" value=$FIELD_MODEL->get('name')}
                        {assign var="FIELD_INFO" value=Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($FIELD_MODEL->getFieldInfo()))}

                        {if $FIELD_MODEL->get('name') eq 'air_weight'}
                            <tr>
                            <td class='fieldLabel' colspan="4">Air</td>
                            </tr>
                        {/if}
                        {if $FIELD_MODEL->get('name') eq 'lcl_weight'}
                            <tr>
                            <td class='fieldLabel' colspan="4">LCL</td>
                            </tr>
                        {/if}
                        {if $FIELD_MODEL->get('name') eq 'fcl_weight'}
                            <tr>
                            <td class='fieldLabel' colspan="4">FCL</td>
                            </tr>
                        {/if}
                        {if $FIELD_MODEL->get('name') eq 'vehicle_weight'}
                            {assign var=COUNTER value=0}
                            <td class='fieldLabel'></td><td class='fieldValue'></td></tr><tr>
                            <tr>
                            <td class='fieldLabel' colspan="4">Vehicle</td>
                            </tr>
                        {/if}
                        {if $FIELD_MODEL->get('uitype') eq "19"}
                            {if $COUNTER eq '1'}
                                <td class='fieldLabel'></td>
                                <td class='fieldValue'></td></tr><tr>
								{assign var=COUNTER value=0}
                            {/if}
                        {/if}
                        {if $COUNTER eq 2}
							</tr><tr>
							{assign var=COUNTER value=1}
                        {else}
                            {assign var=COUNTER value=$COUNTER+1}
                        {/if}
                        <td class='fieldLabel'>
							{if $isReferenceField neq "reference"}<label class="muted pull-right">{/if}
                                {if $FIELD_MODEL->isMandatory() eq true && $isReferenceField neq "reference"} <span class="redColor">*</span> {/if}
                                {if $isReferenceField eq "reference"}
                                    {if $refrenceListCount > 1}
                                        {assign var="DISPLAYID" value=$FIELD_MODEL->get('fieldvalue')}
                                        {assign var="REFERENCED_MODULE_STRUCT" value=$FIELD_MODEL->getUITypeModel()->getReferenceModule($DISPLAYID)}
                                        {if !empty($REFERENCED_MODULE_STRUCT)}
                                            {assign var="REFERENCED_MODULE_NAME" value=$REFERENCED_MODULE_STRUCT->get('name')}
                                        {/if}
                                        <span class="pull-right">
										    {if $FIELD_MODEL->isMandatory() eq true} <span class="redColor">*</span> {/if}
                                            <select style="width: 150px;" class="chzn-select referenceModulesList" id="referenceModulesList">
                                                <optgroup>
                                                    {foreach key=index item=value from=$refrenceList}
                                                        <option value="{$value}" {if $value eq $REFERENCED_MODULE_NAME} selected {/if} >{vtranslate($value, $value)}</option>
                                                    {/foreach}
                                                </optgroup>
										    </select>
									    </span>
								    {else}
									    <label class="muted pull-right">{if $FIELD_MODEL->isMandatory() eq true} <span class="redColor">*</span> {/if}{vtranslate($FIELD_MODEL->get('label'), $MODULE)}</label>
                                    {/if}
                                {else}
                                    {vtranslate($FIELD_MODEL->get('label'), $MODULE)}
                                {/if}
                                {if $isReferenceField neq "reference"}</label>{/if}
						</td>
						<td class="fieldValue" {if $FIELD_MODEL->get('uitype') eq '19'} colspan="3" {assign var=COUNTER value=$COUNTER+1} {/if}>
							{if $FIELD_MODEL->get('name') eq 'origin_type' || $FIELD_MODEL->get('name') eq 'destination_type'}
                                &nbsp;Door&nbsp;<input type='radio' value='Door' name='{$FIELD_MODEL->get('name')}' {if $FIELD_MODEL->get('fieldvalue') eq 'Door'}checked="checked"{/if} />&nbsp;&nbsp;&nbsp;
							&nbsp;Warehouse&nbsp;<input type='radio' value='Warehouse' {if $FIELD_MODEL->get('fieldvalue') eq 'Warehouse'}checked="checked"{/if} name='{$FIELD_MODEL->get('name')}' />&nbsp;&nbsp;&nbsp;
							&nbsp;Port&nbsp;<input type='radio' value='Port' {if $FIELD_MODEL->get('fieldvalue') eq 'Port'}checked="checked"{/if} name='{$FIELD_MODEL->get('name')}' />
							{elseif
                            $FIELD_MODEL->get('name') eq 'air_weight_type' ||
                            $FIELD_MODEL->get('name') eq 'air_volume_type' ||
                            $FIELD_MODEL->get('name') eq 'lcl_weight_type' ||
                            $FIELD_MODEL->get('name') eq 'lcl_volume_type' ||
                            $FIELD_MODEL->get('name') eq 'fcl_weight_type' ||
                            $FIELD_MODEL->get('name') eq 'fcl_volume_type'

                            }
                                &nbsp;Net&nbsp;<input type='radio' value='Net' {if $FIELD_MODEL->get('fieldvalue') eq 'Net'}checked{/if} name='{$FIELD_MODEL->get('name')}'/>&nbsp;&nbsp;&nbsp;
                                &nbsp;Gross&nbsp;<input type='radio' value='Gross' {if $FIELD_MODEL->get('fieldvalue') eq 'Gross'}checked{/if} name='{$FIELD_MODEL->get('name')}' />&nbsp;&nbsp;&nbsp;
							{elseif $FIELD_MODEL->get('name') eq 'air_packing_type'}
                                &nbsp;Wood&nbsp;<input type='radio' value='Wood' {if $FIELD_MODEL->get('fieldvalue') eq 'Wood'}checked{/if} name='{$FIELD_MODEL->get('name')}'/>&nbsp;&nbsp;&nbsp;
                                &nbsp;Tri-Wall&nbsp;<input type='radio' value='Tri-Wall' {if $FIELD_MODEL->get('fieldvalue') eq 'Tri-Wall'}checked{/if} name='{$FIELD_MODEL->get('name')}'/>&nbsp;&nbsp;&nbsp;
                                &nbsp;Other&nbsp;<input type='radio' value='Other' {if $FIELD_MODEL->get('fieldvalue') eq 'Other'}checked{/if} name='{$FIELD_MODEL->get('name')}' />&nbsp;&nbsp;&nbsp;
							{elseif $FIELD_MODEL->get('name') eq 'lcl_packing_type'}
                                &nbsp;Wood&nbsp;<input type='radio' value='Wood' {if $FIELD_MODEL->get('fieldvalue') eq 'Wood'}checked{/if} name='{$FIELD_MODEL->get('name')}'/>&nbsp;&nbsp;&nbsp;
                                &nbsp;Other&nbsp;<input type='radio' value='Other' {if $FIELD_MODEL->get('fieldvalue') eq 'Other'}checked{/if} name='{$FIELD_MODEL->get('name')}' />&nbsp;&nbsp;&nbsp;
							{elseif $FIELD_MODEL->get('name') eq 'fcl_packing_type'}
                                &nbsp;20'&nbsp;<input type='radio' value='20' {if $FIELD_MODEL->get('fieldvalue') eq '20'}checked{/if} name='{$FIELD_MODEL->get('name')}'/>&nbsp;&nbsp;&nbsp;
                                &nbsp;40'&nbsp;<input type='radio' value='40' {if $FIELD_MODEL->get('fieldvalue') eq '40'}checked{/if} name='{$FIELD_MODEL->get('name')}' />&nbsp;&nbsp;&nbsp;
                                &nbsp;40'HC&nbsp;<input type='radio' value='40HC' {if $FIELD_MODEL->get('fieldvalue') eq '40HC'}checked{/if} name='{$FIELD_MODEL->get('name')}'/>&nbsp;&nbsp;&nbsp;
                                &nbsp;45'HC&nbsp;<input type='radio' value='45HC' {if $FIELD_MODEL->get('fieldvalue') eq '45HC'}checked{/if} name='{$FIELD_MODEL->get('name')}' />&nbsp;&nbsp;&nbsp;
							{elseif $FIELD_MODEL->get('name') eq 'fcl_packing_type_2'}
                                &nbsp;Loose&nbsp;<input type='radio' value='Loose' {if $FIELD_MODEL->get('fieldvalue') eq 'Loose'}checked{/if} name='{$FIELD_MODEL->get('name')}'/>&nbsp;&nbsp;&nbsp;
                                &nbsp;Wood in Steel&nbsp;<input type='radio' value='WoodInSteel' {if $FIELD_MODEL->get('fieldvalue') eq 'WoodInSteel'}checked{/if} name='{$FIELD_MODEL->get('name')}' />&nbsp;&nbsp;&nbsp;
							{elseif $FIELD_MODEL->get('name') eq 'vehicle_packing_type'}
                                &nbsp;In Container&nbsp;<input type='radio' value='container' {if $FIELD_MODEL->get('fieldvalue') eq 'container'}checked{/if} name='{$FIELD_MODEL->get('name')}'/>&nbsp;&nbsp;&nbsp;
                                &nbsp;Ro-Ro&nbsp;<input type='radio' value='Ro-Ro' {if $FIELD_MODEL->get('fieldvalue') eq 'Ro-RO'}checked{/if} name='{$FIELD_MODEL->get('name')}' />&nbsp;&nbsp;&nbsp;
                                &nbsp;Other&nbsp;<input type='radio' value='Other' {if $FIELD_MODEL->get('fieldvalue') eq 'Other'}checked{/if} name='{$FIELD_MODEL->get('name')}' />&nbsp;&nbsp;&nbsp;
                            {elseif $FIELD_MODEL->get('name') eq 'to_request_date'}
                            <input id="{$MODULE}_editView_fieldName_{$FIELD_MODEL->get('name')}" type="text" class="dateField" data-date-format="mm-dd-yyyy" data-validation-engine="validate[{if $FIELD_MODEL->isMandatory() eq true}required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" name="{$FIELD_MODEL->get('name')}" value="{$FIELD_MODEL->get('fieldvalue')|date_format:"%m-%d-%Y"}"
                                    {if $FIELD_MODEL->get('uitype') eq '3' || $FIELD_MODEL->get('uitype') eq '4'|| $FIELD_MODEL->isReadOnly()} readonly {/if} data-fieldinfo='{$FIELD_INFO}' {if !empty($SPECIAL_VALIDATOR)}data-validator={Zend_Json::encode($SPECIAL_VALIDATOR)}{/if} />
                            {elseif $FIELD_MODEL->get('uitype') eq '56'}
                                <input id="{$MODULE}_editView_fieldName_{$FIELD_MODEL->get('name')}" type="checkbox" data-fieldinfo='{$FIELD_INFO}' data-validation-engine="validate[{if $FIELD_MODEL->isMandatory() eq true}required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" name="{$FIELD_MODEL->get('name')}" {if $FIELD_MODEL->get('fieldvalue') eq 1} checked="true" {/if}"/>
                            {else}
                                <input id="{$MODULE}_editView_fieldName_{$FIELD_MODEL->get('name')}" type="text" class="input-large nameField" data-validation-engine="validate[{if $FIELD_MODEL->isMandatory() eq true}required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" data-fieldinfo='{$FIELD_INFO}' name="{$FIELD_MODEL->get('name')}" value="{$FIELD_MODEL->get('fieldvalue')}"
                                    {if $FIELD_MODEL->get('uitype') eq '3' || $FIELD_MODEL->get('uitype') eq '4'|| $FIELD_MODEL->isReadOnly()} readonly {/if}  {if !empty($SPECIAL_VALIDATOR)}data-validator={Zend_Json::encode($SPECIAL_VALIDATOR)}{/if} />

                            {/if}
						</td>
                        {/foreach}
                        {if $COUNTER eq '1'}
                        <td class='fieldLabel'></td><td class='fieldValue'></td></tr><tr>
						{assign var=COUNTER value=0}
                        {/if}
					</tr>
				</table>
                <br>
                <br>
            {/foreach}
		</div>
	</div>
</form>
</div>
{/strip}
