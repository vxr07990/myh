{strip}
{assign var=HAS_CONTENT value=(!$BLOCK_SUBLIST || $BLOCK_SUBLIST['LOCAL_MOVE_CONTENTS'])}
<div id="contentHolder_LOCAL_MOVE_CONTENTS" class="sectionContentHolder {$CONTENT_DIV_CLASS} {if !$ALWAYS_SHOW_CONTENT_DIV}hide{/if} {if !$HAS_CONTENT}inactive{/if}">
{if $HAS_CONTENT}
{$LOCALFIELDINFO = ['mandatory' => false, 'presence' => true, 'quickcreate' => false, 'masseditable' => false, 'defaultvalue' => false, 'type' => 'integer', 'name' => '', 'label' => '']}
{if getenv('INSTANCE_NAME') eq 'sirva'}
<input type="hidden" class="hide" name="validLocalEstimateTypes" value="{$LOCAL_ESTIMATE_TYPES}">
{else}
    {assign var=IS_HIDDEN value='1'}
{/if}
    <!-- START TARIFF DIV -->
    <div id='Tariff{$EFFECTIVE_TARIFF}' data-id="{$EFFECTIVE_TARIFF}" class="localMove">
        {assign var=EFFECTIVE_DATE_ID value=$TARIFF_DETAILS.effectiveDate}
        <input type="hidden" class="hide" name="EffectiveDateId" value="{$EFFECTIVE_DATE_ID}">
        <input type="hidden" class="hide" name="NoServices" value="{$TARIFF_DETAILS.no_service}">
        {foreach item=SECTION key=SECTION_INDEX from=$TARIFF_DETAILS.sections}
            <!-- START SECTION TABLE -->
            <table name="Section{$SECTION.id}" data-id="{$SECTION.id}" class="table table-bordered blockContainer showInlineTable localMove">
                <thead>
                    <tr>
                        <th class="blockHeader" colspan="12">
                        <img class="cursorPointer alignMiddle blockToggle {if !($IS_HIDDEN)} hide {/if} "  src="{vimage_path('arrowRight.png')}" data-mode="hide">
                        <img class="cursorPointer alignMiddle blockToggle {if ($IS_HIDDEN)} hide {/if}"  src="{vimage_path('arrowDown.png')}" data-mode="show">
                        {vtranslate($SECTION.name, $MODULE)}
                        &nbsp;&nbsp;{*{vtranslate($BLOCK_LABEL, $MODULE)}*}
                       </th>
                     </tr>
                </thead>
                <tbody{if $IS_HIDDEN} class="hide" {/if}>
                    {* <tr>
                         <th class="blockHeader" colspan="4">{$SECTION.name}</th>
                      </tr> *}

                    {assign var=SECTION_DISCOUNT_READONLY value=''}
                    {assign var=TITLETEXT value=''}
                    {assign var=PLACEHOLDER value=''}
                    {assign var=SECTION_DISCOUNT_DISABLED value=''}
                    {assign var=SECTION_DISCOUNT value=''}

                    {if $SECTION.is_discountable eq 1}
                     {foreach item=ITEM key=index from=$SECTION_DISCOUNTS}
                       {if $ITEM[0] eq $SECTION.id}
                        {assign var=SECTION_DISCOUNT value=$ITEM[1]}
                       {/if}
                     {/foreach}
                    {/if}

                    {if $SECTION.bottomline_discount_override ne 1}
                        {assign var=SECTION_DISCOUNT_READONLY value='readonly="readonly"'}
                        {assign var=TITLETEXT value=vtranslate('LBL_NO_DISCOUNT_OVERRIDE_TITLE_TEXT',$MODULE_NAME)}
                    {/if}

                    {if $SECTION.is_discountable ne 1}
                        {assign var=PLACEHOLDER value=vtranslate('LBL_NO_DISCOUNT_PLACE_HOLDER',$MODULE_NAME)}
                        {assign var=TITLETEXT value=vtranslate('LBL_NO_DISCOUNT_TITLE_TEXT',$MODULE_NAME)}
                        {assign var=SECTION_DISCOUNT_DISABLED value="disabled placeholder='`$PLACEHOLDER`'"}
                        {assign var=SECTION_DISCOUNT value=''}
                    {/if}

                     <tr>
                       <td class='fieldLabel'>
                        <label class="muted pull-right marginRight10px">Section Discount</label>
                       </td>
                       <td>
                        <div class="input-append">
                            <input type="number" class="input-medium" min="-100" max="100" name="SectionDiscount{$SECTION.id}" value="{$SECTION_DISCOUNT}" step="any" {$SECTION_DISCOUNT_READONLY} {$SECTION_DISCOUNT_DISABLED} title="{$TITLETEXT}" /><span class="add-on">%</span>
                        </div>
                       </td>
                       <td class='fieldLabel'>&nbsp;</td>
                       <td class='fieldValue'>&nbsp;</td>
                     </tr>
                    {foreach item=SERVICE key=SERVICE_INDEX from=$SECTION.services}
                        {assign var=RATE_TYPE value=$SERVICE->get(rate_type)}
                        {assign var=SERVICE_DETAILS value=$SERVICE->getRecordDetails($RECORD_ID)}
                        {assign var=COSTS value=$SERVICE->getCostTotals($RECORD_ID)}

                        {if $RATE_TYPE eq "Hourly Avg Lb/Man/Hour"}
                          {continue}
                        {/if}

                        {assign var=ID value=$SERVICE->get(id)}
                        <tr>
                            <td class='fieldLabel' colspan="12">{$SERVICE->get(service_name)}
                            <input type="hidden" class="hide localService" value="{$ID}"/>
                            <input type="hidden" class="hide localRateType" value="{$RATE_TYPE}"/>
                            <input type="hidden" class="hide localServiceCost" name="cost_service_total{$ID}" value="{$COSTS[0]}"/>
                            <input type="hidden" class="hide localServiceCost" name="cost_container_total{$ID}" value="{$COSTS[1]}"/>
                            <input type="hidden" class="hide localServiceCost" name="cost_packing_total{$ID}" value="{$COSTS[2]}"/>
                            <input type="hidden" class="hide localServiceCost" name="cost_unpacking_total{$ID}" value="{$COSTS[3]}"/>
                            <input type="hidden" class="hide localServiceCost" name="cost_crating_total{$ID}" value="{$COSTS[4]}"/>
                            <input type="hidden" class="hide localServiceCost" name="cost_uncrating_total{$ID}" value="{$COSTS[5]}"/>
                            </td>
                        </tr>
                        {if $RATE_TYPE eq "Base Plus Trans."}
                           <tr>
                            <td class='fieldLabel'>
                                <label class="muted pull-right marginRight10px">{if $SERVICE->get(is_required)}<span class="redColor">*</span>{/if}Miles</label>
                            </td>
                            <td class='fieldValue'>
                            {assign var=FIELDNAME value='Miles'|cat:$ID}
                            {if $SERVICE->get(is_required)}{$LOCALFIELDINFO.mandatory = true}{else}{$LOCALFIELDINFO.mandatory = false}{/if}
                            {$LOCALFIELDINFO.name = $FIELDNAME}
                            {$LOCALFIELDINFO.label = 'Miles'}
                            {$LOCALFIELDINFO.type = 'integer'}
                            {$INFO = Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($LOCALFIELDINFO))}
                                <input id="{$MODULE_NAME}_editView_fieldName_Miles{$ID}" name="Miles{$ID}" class="input-large nameField LocalBaseRateTrans" type="number" value="{$SERVICE_DETAILS.mileage}" data-validation-engine="validate[{if $SERVICE->get(is_required)}required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" data-fieldinfo="{$INFO}" />
                                <input type="hidden" class="fieldname" value="Miles{$ID}" data-prev-value="{$SERVICE_DETAILS.mileage}">
                            </td>
                            <td class='fieldLabel'>
                                <label class="muted pull-right marginRight10px">{if $SERVICE->get(is_required)}<span class="redColor">*</span>{/if}Rate</label>
                            </td>
                            <td class='fieldValue'>
                            {assign var=FIELDNAME value='Rate'|cat:$ID}
                            {if $SERVICE->get(is_required)}{$LOCALFIELDINFO.mandatory = true}{else}{$LOCALFIELDINFO.mandatory = false}{/if}
                            {$LOCALFIELDINFO.name = $FIELDNAME}
                            {$LOCALFIELDINFO.label = 'Rate'}
                            {$LOCALFIELDINFO.type = 'currency'}
                            {$INFO = Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($LOCALFIELDINFO))}
                                <div class="row-fluid">
                                   <div class="input-prepend">
                                     <span class="span10">
                                      <span class="add-on">&#36;</span>
                                      <input name="Rate{$ID}" step="0.01" class="input-medium currencyField" type="text" value="{number_format((float)$SERVICE_DETAILS.rate, 2, '.', '')}" data-decimal-seperator="." data-group-seperator="," data-number-of-decimal-places="2" data-validation-engine="validate[{if $SERVICE->get(is_required)}required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" data-fieldinfo="{$INFO}" />
                                     </span>
                                   </div>
                                </div>
                            </td>
                           </tr>
                           <tr>
                            <td class='fieldLabel'>
                                <label class="muted pull-right marginRight10px">{if $SERVICE->get(is_required)}<span class="redColor">*</span>{/if}Weight</label>
                            </td>
                            <td class='fieldValue'>
                            {assign var=FIELDNAME value='Weight'|cat:$ID}
                            {if $SERVICE->get(is_required)}{$LOCALFIELDINFO.mandatory = true}{else}{$LOCALFIELDINFO.mandatory = false}{/if}
                            {$LOCALFIELDINFO.name = $FIELDNAME}
                            {$LOCALFIELDINFO.label = 'Weight'}
                            {$LOCALFIELDINFO.type = 'integer'}
                            {$INFO = Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($LOCALFIELDINFO))}
                                <input data-validator='[{ldelim}"name":"WholeNumber"{rdelim}]' data-validation-engine="validate[funcCall[Vtiger_Base_Validator_Js.invokeValidation]]"  min="0" name="Weight{$ID}" class="input-large nameField LocalBaseRateTrans" type="number" value="{$SERVICE_DETAILS.weight}" data-validation-engine="validate[{if $SERVICE->get(is_required)}required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" data-fieldinfo="{$INFO}" />
                                <input type="hidden" class="fieldname" value="Weight{$ID}" data-prev-value="{$SERVICE_DETAILS.weight}">
                            </td>

                            <td class='fieldLabel'>
                                <label class="muted pull-right marginRight10px">{if $SERVICE->get(is_required)}<span class="redColor">*</span>{/if}Excess</label>
                            </td>
                            <td class='fieldValue'>
                            {assign var=FIELDNAME value='Excess'|cat:$ID}
                            {if $SERVICE->get(is_required)}{$LOCALFIELDINFO.mandatory = true}{else}{$LOCALFIELDINFO.mandatory = false}{/if}
                            {$LOCALFIELDINFO.name = $FIELDNAME}
                            {$LOCALFIELDINFO.label = 'Excess'}
                            {$LOCALFIELDINFO.type = 'currency'}
                            {$INFO = Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($LOCALFIELDINFO))}
                            <div class="row-fluid">
                                   <div class="input-prepend">
                                     <span class="span10">
                                      <span class="add-on">&#36;</span>
                                      <input name="Excess{$ID}" step="0.01" class="input-medium currencyField" type="text" value="{number_format((float)$SERVICE_DETAILS.excess, 2, '.', '')}" data-decimal-seperator="." data-group-seperator="," data-number-of-decimal-places="2" data-validation-engine="validate[{if $SERVICE->get(is_required)}required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" data-fieldinfo="{$INFO}" />
                                     </span>
                                   </div>
                                </div>
                            </td>
                           </tr>
                        {elseif $RATE_TYPE eq "Break Point Trans."}
                           <tr>
                            <td class='fieldLabel'>
                                <label class="muted pull-right marginRight10px">{if $SERVICE->get(is_required)}<span class="redColor">*</span>{/if}Miles</label>
                            </td>
                            <td class='fieldValue'>
                            {assign var=FIELDNAME value='Miles'|cat:$ID}
                            {if $SERVICE->get(is_required)}{$LOCALFIELDINFO.mandatory = true}{else}{$LOCALFIELDINFO.mandatory = false}{/if}
                            {$LOCALFIELDINFO.name = $FIELDNAME}
                            {$LOCALFIELDINFO.label = 'Miles'}
                            {$LOCALFIELDINFO.type = 'integer'}
                            {$INFO = Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($LOCALFIELDINFO))}
                                <input name="Miles{$ID}" class="input-large nameField localBreakPoint" type="number" value="{$SERVICE_DETAILS.mileage}" data-validation-engine="validate[{if $SERVICE->get(is_required)}required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" data-fieldinfo="{$INFO}" />
                                <input type="hidden" class="fieldname" value="Miles{$ID}" data-prev-value="{$SERVICE_DETAILS.mileage}">
                            </td>
                            <td class='fieldLabel'>
                                <label class="muted pull-right marginRight10px">{if $SERVICE->get(is_required)}<span class="redColor">*</span>{/if}Rate</label>
                            </td>
                            <td class='fieldValue'>
                            {assign var=FIELDNAME value='Rate'|cat:$ID}
                            {if $SERVICE->get(is_required)}{$LOCALFIELDINFO.mandatory = true}{else}{$LOCALFIELDINFO.mandatory = false}{/if}
                            {$LOCALFIELDINFO.name = $FIELDNAME}
                            {$LOCALFIELDINFO.label = 'Rate'}
                            {$LOCALFIELDINFO.type = 'currency'}
                            {$INFO = Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($LOCALFIELDINFO))}
                                <div class="row-fluid">

										<div class="input-prepend">
											<span class="span10">
												<span class="add-on">&#36;</span>
												<input id="{$MODULE_NAME}_editView_fieldName_Rate{$ID}" name="Rate{$ID}" step="0.01" class="input-medium currencyField" type="text" value="{number_format((float)$SERVICE_DETAILS.rate, 2, '.', '')}" data-decimal-seperator="." data-group-seperator="," data-number-of-decimal-places="2" data-validation-engine="validate[{if $SERVICE->get(is_required)}required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" data-fieldinfo="{$INFO}" />
											</span>
										</div>
									</div>
								</td>
							</tr>
							<tr>
								<td class='fieldLabel'>
									<label class="muted pull-right marginRight10px">{if $SERVICE->get(is_required)}<span class="redColor">*</span>{/if}Weight</label>
								</td>
								<td class='fieldValue'>
								{assign var=FIELDNAME value='Weight'|cat:$ID}
								{if $SERVICE->get(is_required)}{$LOCALFIELDINFO.mandatory = true}{else}{$LOCALFIELDINFO.mandatory = false}{/if}
								{$LOCALFIELDINFO.name = $FIELDNAME}
								{$LOCALFIELDINFO.label = 'Weight'}
								{$LOCALFIELDINFO.type = 'integer'}
								{$INFO = Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($LOCALFIELDINFO))}
									<input data-validator='[{ldelim}"name":"WholeNumber"{rdelim}]' data-validation-engine="validate[funcCall[Vtiger_Base_Validator_Js.invokeValidation]]"  min="0" id="{$MODULE_NAME}_editView_fieldName_Weight{$ID}" name="Weight{$ID}" class="input-large nameField localBreakPoint" type="number" value="{$SERVICE_DETAILS.weight}" data-validation-engine="validate[{if $SERVICE->get(is_required)}required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" data-fieldinfo="{$INFO}" />
									<input type="hidden" class="fieldname" value="Weight{$ID}" data-prev-value="{$SERVICE_DETAILS.weight}">
								</td>
								<td class='fieldLabel' colspan="2">&nbsp;</td>
							</tr>
						{elseif $RATE_TYPE eq "County Charge"}
							{assign var=PICKLIST_VALUES value=$SERVICE->getCountyChargePicklists($ID)}
							<tr>
								<td class='fieldLabel'>
									<label class="muted pull-right marginRight10px">{if $SERVICE->get(is_required)}<span class="redColor">*</span>{/if}County</label>
								</td>
								<td class='fieldValue'>
								{assign var=FIELDNAME value='County'|cat:$ID}
								{if $SERVICE->get(is_required)}{$LOCALFIELDINFO.mandatory = true}{else}{$LOCALFIELDINFO.mandatory = false}{/if}
								{$LOCALFIELDINFO.name = $FIELDNAME}
								{$LOCALFIELDINFO.label = 'County'}
								{$LOCALFIELDINFO.type = 'picklist'}
								{$INFO = Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($LOCALFIELDINFO))}
									<select class="chzn-select" name="County{$ID}" class ="localCountyPick" data-validation-engine="validate[{if $SERVICE->get(is_required)}required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" data-fieldinfo="{$INFO}" >
										<option value="" {if $SERVICE_DETAILS.county eq ""}selected{/if}>{vtranslate('LBL_SELECT_OPTION','Vtiger')}</option>
										{foreach item=PICKLIST_VALUE key=PICKLIST_NAME from=$PICKLIST_VALUES}
											<option name="{$PICKLIST_VALUE}{$ID}" value="{$PICKLIST_VALUE}" {if $SERVICE_DETAILS.county eq $PICKLIST_VALUE}selected{/if}>{$PICKLIST_VALUE}</option>
										{/foreach}
									</select>
								</td>
								<td class='fieldLabel'>
									<label class="muted pull-right marginRight10px">{if $SERVICE->get(is_required)}<span class="redColor">*</span>{/if}Rate</label>
								</td>
								<td class='fieldValue'>
								{assign var=FIELDNAME value='Rate'|cat:$ID}
								{if $SERVICE->get(is_required)}{$LOCALFIELDINFO.mandatory = true}{else}{$LOCALFIELDINFO.mandatory = false}{/if}
								{$LOCALFIELDINFO.name = $FIELDNAME}
								{$LOCALFIELDINFO.label = 'Rate'}
								{$LOCALFIELDINFO.type = 'currency'}
								{$INFO = Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($LOCALFIELDINFO))}
									<div class="row-fluid">


                                   <div class="input-prepend">
                                     <span class="span10">
                                      <span class="add-on">&#36;</span>
                                      <input name="Rate{$ID}" step="0.01" class="input-medium currencyField" type="text" value="{number_format((float)$SERVICE_DETAILS.rate, 2, '.', '')}" data-decimal-seperator="." data-group-seperator="," data-number-of-decimal-places="2" data-validation-engine="validate[{if $SERVICE->get(is_required)}required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" data-fieldinfo="{$INFO}" />
                                     </span>
                                   </div>
                                </div>
                            </td>
                           </tr>
                           <tr>
                            <td class='fieldLabel'>
                                <label class="muted pull-right marginRight10px">{if $SERVICE->get(is_required)}<span class="redColor">*</span>{/if}Weight</label>
                            </td>
                            <td class='fieldValue'>
                            {assign var=FIELDNAME value='Weight'|cat:$ID}
                            {if $SERVICE->get(is_required)}{$LOCALFIELDINFO.mandatory = true}{else}{$LOCALFIELDINFO.mandatory = false}{/if}
                            {$LOCALFIELDINFO.name = $FIELDNAME}
                            {$LOCALFIELDINFO.label = 'Weight'}
                            {$LOCALFIELDINFO.type = 'integer'}
                            {$INFO = Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($LOCALFIELDINFO))}
                                <input data-validator='[{ldelim}"name":"WholeNumber"{rdelim}]' data-validation-engine="validate[funcCall[Vtiger_Base_Validator_Js.invokeValidation]]"  min="0" name="Weight{$ID}" class="input-large nameField localBreakPoint" type="number" value="{$SERVICE_DETAILS.weight}" data-validation-engine="validate[{if $SERVICE->get(is_required)}required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" data-fieldinfo="{$INFO}" />
                                <input type="hidden" class="fieldname" value="Weight{$ID}" data-prev-value="{$SERVICE_DETAILS.weight}">
                            </td>

                            <td class='fieldLabel'>
                                <label class="muted pull-right marginRight10px">{if $SERVICE->get(is_required)}<span class="redColor">*</span>{/if}Calculated Weight</label>
                            </td>
                            <td class='fieldValue'>
                            {assign var=FIELDNAME value='calcWeight'|cat:$ID}
                            {if $SERVICE->get(is_required)}{$LOCALFIELDINFO.mandatory = true}{else}{$LOCALFIELDINFO.mandatory = false}{/if}
                            {$LOCALFIELDINFO.name = $FIELDNAME}
                            {$LOCALFIELDINFO.label = 'Calculated Weight'}
                            {$LOCALFIELDINFO.type = 'integer'}
                            {$INFO = Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($LOCALFIELDINFO))}
                                <input data-validator='[{ldelim}"name":"WholeNumber"{rdelim}]' data-validation-engine="validate[funcCall[Vtiger_Base_Validator_Js.invokeValidation]]"  min="0" type="text" name="calcWeight{$ID}" value="{$SERVICE_DETAILS.breakpoint}" class="input-large" readonly role="textbox" data-validation-engine="validate[{if $SERVICE->get(is_required)}required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" data-fieldinfo="{$INFO}" />
                            </td>
                           </tr>
                        {elseif $RATE_TYPE eq "Weight/Mileage Trans."}
                           <tr>
                            <td class='fieldLabel'>
                                <label class="muted pull-right marginRight10px">{if $SERVICE->get(is_required)}<span class="redColor">*</span>{/if}Miles</label>
                            </td>
                            <td class='fieldValue'>
                            {assign var=FIELDNAME value='Miles'|cat:$ID}
                            {if $SERVICE->get(is_required)}{$LOCALFIELDINFO.mandatory = true}{else}{$LOCALFIELDINFO.mandatory = false}{/if}
                            {$LOCALFIELDINFO.name = $FIELDNAME}
                            {$LOCALFIELDINFO.label = 'Miles'}
                            {$LOCALFIELDINFO.type = 'integer'}
                            {$INFO = Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($LOCALFIELDINFO))}
                                <input data-validator='[{ldelim}"name":"WholeNumber"{rdelim}]' data-validation-engine="validate[funcCall[Vtiger_Base_Validator_Js.invokeValidation]]"  min="0" id="{$MODULE_NAME}_editView_fieldName_Miles{$ID}" name="Miles{$ID}" class="input-large nameField localWeightMile" type="number" value="{$SERVICE_DETAILS.mileage}" data-validation-engine="validate[{if $SERVICE->get(is_required)}required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" data-fieldinfo="{$INFO}" />
                                <input type="hidden" class="fieldname" value="Miles{$ID}" data-prev-value="{$SERVICE_DETAILS.mileage}">
                            </td>
                            <td class='fieldLabel'>
                                <label class="muted pull-right marginRight10px">{if $SERVICE->get(is_required)}<span class="redColor">*</span>{/if}Rate</label>
                            </td>
                            <td class='fieldValue'>
                            {assign var=FIELDNAME value='Rate'|cat:$ID}
                            {if $SERVICE->get(is_required)}{$LOCALFIELDINFO.mandatory = true}{else}{$LOCALFIELDINFO.mandatory = false}{/if}
                            {$LOCALFIELDINFO.name = $FIELDNAME}
                            {$LOCALFIELDINFO.label = 'Rate'}
                            {$LOCALFIELDINFO.type = 'currency'}
                            {$INFO = Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($LOCALFIELDINFO))}
                                <div class="row-fluid">


                                   <div class="input-prepend">
                                     <span class="span10">
                                      <span class="add-on">&#36;</span>
                                      <input id="{$MODULE_NAME}_editView_fieldName_Rate{$ID}" name="Rate{$ID}" step="0.01" class="input-medium currencyField" type="text" value="{number_format((float)$SERVICE_DETAILS.rate, 2, '.', '')}" data-decimal-seperator="." data-group-seperator="," data-number-of-decimal-places="2" data-validation-engine="validate[{if $SERVICE->get(is_required)}required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" data-fieldinfo="{$INFO}" />
                                     </span>
                                   </div>
                                </div>
                            </td>
                           </tr>
                           <tr>
                            <td class='fieldLabel'>
                                <label class="muted pull-right marginRight10px">{if $SERVICE->get(is_required)}<span class="redColor">*</span>{/if}Weight</label>
                            </td>
                            <td class='fieldValue'>
                            {assign var=FIELDNAME value='Weight'|cat:$ID}
                            {if $SERVICE->get(is_required)}{$LOCALFIELDINFO.mandatory = true}{else}{$LOCALFIELDINFO.mandatory = false}{/if}
                            {$LOCALFIELDINFO.name = $FIELDNAME}
                            {$LOCALFIELDINFO.label = 'Weight'}
                            {$LOCALFIELDINFO.type = 'integer'}
                            {$INFO = Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($LOCALFIELDINFO))}
                                <input data-validator='[{ldelim}"name":"WholeNumber"{rdelim}]' data-validation-engine="validate[funcCall[Vtiger_Base_Validator_Js.invokeValidation]]"  min="0" id="{$MODULE_NAME}_editView_fieldName_Weight{$ID}" name="Weight{$ID}" class="input-large nameField localWeightMile" type="number" value="{$SERVICE_DETAILS.weight}" data-validation-engine="validate[{if $SERVICE->get(is_required)}required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" data-fieldinfo="{$INFO}" />
                                <input type="hidden" class="fieldname" value="Weight{$ID}" data-prev-value="{$SERVICE_DETAILS.weight}">
                            </td>
                            <td class='fieldLabel' colspan="2">&nbsp;</td>
                           </tr>
                        {elseif $RATE_TYPE eq "Service Base Charge"}
                           <tr>
                            <td class='fieldLabel'>
                                <label class="muted pull-right marginRight10px">Rate</label>
                            </td>
                            <td class='fieldValue'>
                                            {assign var=RATE value=$SERVICE->getServiceCharges($RECORD_ID)}

                                <div class="row-fluid">
                                    <div class="input-prepend">
                                        <span class="span10">
                                        {if $SERVICE->get('service_base_charge_matrix')}
                                            {$LOCALFIELDINFO.type = 'currency'}
                                            {$INFO = Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($LOCALFIELDINFO))}
                                            <span class="add-on">&#37;</span>
                                            <input name="ServiceCharge{$ID}" step="0.01" class="input-medium" type="text" placeholder="Base Service Matrix" data-decimal-seperator="." data-group-seperator="," data-number-of-decimal-places="2" data-validation-engine="validate[{if $SERVICE->get(is_required)}required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" data-fieldinfo="{$INFO}" {if $RATE.rate > 0}value="{$RATE.rate}"{/if} />
                                            <br/>
                                            <input id="{$MODULE_NAME}_editView_fieldName_ServiceChargeOverride{$ID}" type="checkbox" name="ServiceChargeOverride{$ID}" data-service-id="{$ID}" {if $RATE.rate > 0}checked="checked"{/if} />
                                            <label for="{$MODULE_NAME}_editView_fieldName_ServiceChargeOverride{$ID}" style="display:inline;padding:5px;">Override?</label>
                                        {else}
                                            {$LOCALFIELDINFO.type = 'currency'}
                                            {$INFO = Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($LOCALFIELDINFO))}
                                            <span class="add-on">&#37;</span>
                                            <input name="ServiceCharge{$ID}" step="0.01" class="input-medium" type="text" value="{($RATE.rate) ? $RATE.rate : $SERVICE->get('service_base_charge')}" data-decimal-seperator="." data-group-seperator="," data-number-of-decimal-places="2" data-validation-engine="validate[{if $SERVICE->get(is_required)}required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" data-fieldinfo="{$INFO}" />
                                        {/if}
                                        </span>
                                    </div>
                                </div>
                            </td>
                            <td class='fieldLabel'>
                                <label class="muted pull-right marginRight10px">Applies to </label>
                            </td>
                            <td class='fieldValue'>
                                {$SERVICE->getServiceChargesApplies()}
                            </td>
                           </tr>
                        {elseif $RATE_TYPE eq "Storage Valuation"}
                           <tr>
                            <td class='fieldLabel'>
                                <label class="muted pull-right marginRight10px">Rate</label>
                            </td>
                            <td class='fieldValue'>
                                            {assign var=RATE value=$SERVICE->getStorageValuation($RECORD_ID)}
                                <div class="row-fluid">
                                   <div class="input-prepend">
                                     <span class="span10">
                                      {if $SERVICE->get('service_base_charge_matrix')}
                                          Base Charge Matrix
                                      {else}
                                          <span class="add-on">&#37;</span>
                                          <input name="StorageValuation{$ID}" class="input-medium" type="text" value="{($RATE.rate) ? $RATE.rate : $SERVICE->get('service_base_charge')}" data-decimal-seperator="." data-group-seperator="," data-number-of-decimal-places="2" data-validation-engine="validate[{if $SERVICE->get(is_required)}required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" data-fieldinfo="{$INFO}" />
                                      {/if}
                                     </span>
                                   </div>
                                </div>
                            </td>
                            <td class='fieldLabel'>
                                <label class="muted pull-right marginRight10px">Applies to </label>
                            </td>
                            <td class='fieldValue'>
                                {$SERVICE->getServiceChargesApplies()}
                            </td>
                           </tr>
                           <tr>

                                        <td class='fieldLabel'>
                                <label class="muted pull-right marginRight10px">Months</label>
                            </td>
                            <td class='fieldValue'>
                                             {assign var=MONTHS value=$SERVICE->getStorageValuationMonths($RECORD_ID)}
                                             <input id="Month{$ID}" name="Month{$ID}" class="input-large " type="number" value="{($MONTHS.months) ? $MONTHS.months :''}" data-validation-engine="validate[funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" >
                                        </td>
                                        <td></td><td></td>
                                    </tr>
                        {elseif $RATE_TYPE eq "Hourly Set"}
                           <tr>
                            <td class='fieldLabel'>
                                <label class="muted pull-right marginRight10px">{if $SERVICE->get(is_required)}<span class="redColor">*</span>{/if}Men</label>
                            </td>
                            <td class='fieldValue'>
                            {assign var=FIELDNAME value='Men'|cat:$ID}
                            {if $SERVICE->get(is_required)}{$LOCALFIELDINFO.mandatory = true}{else}{$LOCALFIELDINFO.mandatory = false}{/if}
                            {$LOCALFIELDINFO.name = $FIELDNAME}
                            {$LOCALFIELDINFO.label = 'Men'}
                            {$LOCALFIELDINFO.type = 'integer'}
                            {$INFO = Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($LOCALFIELDINFO))}
                                <input data-validator='[{ldelim}"name":"WholeNumber"{rdelim}]' data-validation-engine="validate[funcCall[Vtiger_Base_Validator_Js.invokeValidation]]"  min="0" name="Men{$ID}" class="input-large nameField localHourlySet" type="number" value="{$SERVICE_DETAILS.men}" data-validation-engine="validate[{if $SERVICE->get(is_required)}required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" data-fieldinfo="{$INFO}" />
                                <input type="hidden" class="fieldname" value="Men{$ID}" data-prev-value="{$SERVICE_DETAILS.men}">
                            </td>
                            <td class='fieldLabel'>
                                <label class="muted pull-right marginRight10px">{if $SERVICE->get(is_required)}<span class="redColor">*</span>{/if}Hours</label>
                            </td>
                            <td class='fieldValue'>
                            {assign var=FIELDNAME value='Hours'|cat:$ID}
                            {if $SERVICE->get(is_required)}{$LOCALFIELDINFO.mandatory = true}{else}{$LOCALFIELDINFO.mandatory = false}{/if}
                            {$LOCALFIELDINFO.name = $FIELDNAME}
                            {$LOCALFIELDINFO.label = 'Hours'}
                            {$LOCALFIELDINFO.type = 'double'}
                            {$INFO = Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($LOCALFIELDINFO))}
                                <input min="0" data-validator='[{ldelim}"name":"PositiveNumber"{rdelim}]' data-validation-engine="validate[funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" name="Hours{$ID}" class="input-large nameField" type="number" value="{$SERVICE_DETAILS.hours}" data-decimal-seperator="." data-group-seperator="," data-number-of-decimal-places="2" step=".25" data-validation-engine="validate[{if $SERVICE->get(is_required)}required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" data-fieldinfo="{$INFO}" />
                            </td>

                           </tr>
                           {assign var=TDCOUNT value=0}
                           <tr>
                            {if $SERVICE->get(hourlyset_hasvan) eq 1}
                            {assign var=TDCOUNT value=$TDCOUNT+1}
                            <td class='fieldLabel'>
                                <label class="muted pull-right marginRight10px">{if $SERVICE->get(is_required)}<span class="redColor">*</span>{/if}Vans</label>
                            </td>
                            <td class='fieldValue'>
                            {assign var=FIELDNAME value='Vans'|cat:$ID}
                            {if $SERVICE->get(is_required)}{$LOCALFIELDINFO.mandatory = true}{else}{$LOCALFIELDINFO.mandatory = false}{/if}
                            {$LOCALFIELDINFO.name = $FIELDNAME}
                            {$LOCALFIELDINFO.label = 'Vans'}
                            {$LOCALFIELDINFO.type = 'integer'}
                            {$INFO = Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($LOCALFIELDINFO))}
                                <input data-validator='[{ldelim}"name":"WholeNumber"{rdelim}]' data-validation-engine="validate[funcCall[Vtiger_Base_Validator_Js.invokeValidation]]"  min="0" name="Vans{$ID}" class="input-large nameField localHourlySet" type="number" value="{$SERVICE_DETAILS.vans}" data-validation-engine="validate[{if $SERVICE->get(is_required)}required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" data-fieldinfo="{$INFO}" />
                                <input type="hidden" class="fieldname" value="Vans{$ID}" data-prev-value="{$SERVICE_DETAILS.vans}">
                            </td>
                            {/if}
                            {if $SERVICE->get(hourlyset_hastravel) eq 1}
                            {assign var=TDCOUNT value=$TDCOUNT+1}
                                <td class='fieldLabel'>
                                   <label class="muted pull-right marginRight10px">{if $SERVICE->get(is_required)}<span class="redColor">*</span>{/if}Travel Time</label>
                                </td>
                                <td class='fieldValue'>
                                {assign var=FIELDNAME value='TravelTime'|cat:$ID}
                                {if $SERVICE->get(is_required)}{$LOCALFIELDINFO.mandatory = true}{else}{$LOCALFIELDINFO.mandatory = false}{/if}
                                {$LOCALFIELDINFO.name = $FIELDNAME}
                                {$LOCALFIELDINFO.label = 'Travel Time'}
                                {$LOCALFIELDINFO.type = 'double'}
                                {$INFO = Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($LOCALFIELDINFO))}
                                   <input min="0" data-validator='[{ldelim}"name":"PositiveNumber"{rdelim}]' data-validation-engine="validate[funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" name="TravelTime{$ID}" class="input-large nameField" type="number" value="{$SERVICE_DETAILS.traveltime}" data-decimal-seperator="." data-group-seperator="," data-number-of-decimal-places="2" step=".25" data-validation-engine="validate[{if $SERVICE->get(is_required)}required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" data-fieldinfo="{$INFO}" />
                                </td>
                            {/if}
                            {if ($TDCOUNT % 2) eq 0}
                            </tr>
                            <tr>
                            {/if}
                            {assign var=TDCOUNT value=$TDCOUNT+1}
                            <td class='fieldLabel'>
                                <label class="muted pull-right marginRight10px">{if $SERVICE->get(is_required)}<span class="redColor">*</span>{/if}Rate</label>
                            </td>
                            <td class='fieldValue'>
                            {assign var=FIELDNAME value='Rate'|cat:$ID}
                            {if $SERVICE->get(is_required)}{$LOCALFIELDINFO.mandatory = true}{else}{$LOCALFIELDINFO.mandatory = false}{/if}
                            {$LOCALFIELDINFO.name = $FIELDNAME}
                            {$LOCALFIELDINFO.label = 'Rate'}
                            {$LOCALFIELDINFO.type = 'currency'}
                            {$INFO = Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($LOCALFIELDINFO))}
                                <div class="row-fluid">

										<div class="input-prepend">
											<span class="span10">
												<span class="add-on">&#36;</span>
												<input name="Rate{$ID}" step="0.01" class="input-medium currencyField" type="number" value="{number_format((float)$SERVICE_DETAILS.rate, 2, '.', '')}" data-decimal-seperator="." data-group-seperator="," data-number-of-decimal-places="2" data-validation-engine="validate[{if $SERVICE->get(is_required)}required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" data-fieldinfo="{$INFO}" />
											</span>
										</div>
									</div>
								</td>
								{if ($TDCOUNT % 2) eq 1}
									<td class='fieldLabel'>&nbsp;</td>
									<td class='fieldValue'>&nbsp;</td>
								{/if}
							</tr>
						{elseif $RATE_TYPE eq "Hourly Simple"}
							<tr>
								<td class='fieldLabel'>
									<label class="muted pull-right marginRight10px">{if $SERVICE->get(is_required)}<span class="redColor">*</span>{/if}Quantity</label>
								</td>
								<td class='fieldValue'>
								{assign var=FIELDNAME value='Quantity'|cat:$ID}
								{if $SERVICE->get(is_required)}{$LOCALFIELDINFO.mandatory = true}{else}{$LOCALFIELDINFO.mandatory = false}{/if}
								{$LOCALFIELDINFO.name = $FIELDNAME}
								{$LOCALFIELDINFO.label = 'Quantity'}
								{$LOCALFIELDINFO.type = 'integer'}
								{$INFO = Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($LOCALFIELDINFO))}
									<input name="Quantity{$ID}" class="input-large nameField" type="number" value="{(int) $SERVICE_DETAILS.quantity}" data-validation-engine="validate[{if $SERVICE->get(is_required)}required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" data-fieldinfo="{$INFO}" />
								</td>
								<td class='fieldLabel'>
									<label class="muted pull-right marginRight10px">{if $SERVICE->get(is_required)}<span class="redColor">*</span>{/if}Hours</label>
								</td>
								<td class='fieldValue'>
								{assign var=FIELDNAME value='Hours'|cat:$ID}
								{if $SERVICE->get(is_required)}{$LOCALFIELDINFO.mandatory = true}{else}{$LOCALFIELDINFO.mandatory = false}{/if}
								{$LOCALFIELDINFO.name = $FIELDNAME}
								{$LOCALFIELDINFO.label = 'Hours'}
								{$LOCALFIELDINFO.type = 'double'}
								{$INFO = Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($LOCALFIELDINFO))}
									<input min="0" data-validator='[{ldelim}"name":"PositiveNumber"{rdelim}]' data-validation-engine="validate[funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" name="Hours{$ID}" class="input-large nameField" type="number" value="{number_format((float)$SERVICE_DETAILS.hours, 2, '.', '')}" data-decimal-seperator="." data-group-seperator="," data-number-of-decimal-places="2" step=".25" data-validation-engine="validate[{if $SERVICE->get(is_required)}required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" data-fieldinfo="{$INFO}" />
								</td>
							</tr>
							<tr>
								<td class='fieldLabel'>
									<label class="muted pull-right marginRight10px">{if $SERVICE->get(is_required)}<span class="redColor">*</span>{/if}Rate</label>
								</td>
								<td class='fieldValue'>
								{assign var=FIELDNAME value='Rate'|cat:$ID}
								{if $SERVICE->get(is_required)}{$LOCALFIELDINFO.mandatory = true}{else}{$LOCALFIELDINFO.mandatory = false}{/if}
								{$LOCALFIELDINFO.name = $FIELDNAME}
								{$LOCALFIELDINFO.label = 'Rate'}
								{$LOCALFIELDINFO.type = 'currency'}
								{$INFO = Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($LOCALFIELDINFO))}
									<div class="row-fluid">


                                   <div class="input-prepend">
                                     <span class="span10">
                                      <span class="add-on">&#36;</span>
                                      <input name="Rate{$ID}" step="0.01" class="input-medium currencyField" type="number" value="{if !empty($SERVICE_DETAILS.rate)}{number_format((float)$SERVICE_DETAILS.rate, 2, '.', '')}{else}{number_format((float)$SERVICE->get('hourlysimple_rate'), 2, '.', '')}{/if}" data-decimal-seperator="." data-group-seperator="," data-number-of-decimal-places="2" data-validation-engine="validate[{if $SERVICE->get(is_required)}required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" data-fieldinfo="{$INFO}" />
                                     </span>
                                   </div>
                                </div>
                            </td>
                            {if ($TDCOUNT % 2) eq 1}
                                <td class='fieldLabel'>&nbsp;</td>
                                <td class='fieldValue'>&nbsp;</td>
                            {/if}
                           </tr>
                        {*{elseif $RATE_TYPE eq "Hourly Simple"}*}
                           {*<tr>*}
                            {*<td class='fieldLabel'>*}
                                {*<label class="muted pull-right marginRight10px">{if $SERVICE->get(is_required)}<span class="redColor">*</span>{/if}Quantity</label>*}
                            {*</td>*}
                            {*<td class='fieldValue'>*}
                            {*{assign var=FIELDNAME value='Quantity'|cat:$ID}*}
                            {*{if $SERVICE->get(is_required)}{$LOCALFIELDINFO.mandatory = true}{else}{$LOCALFIELDINFO.mandatory = false}{/if}*}
                            {*{$LOCALFIELDINFO.name = $FIELDNAME}*}
                            {*{$LOCALFIELDINFO.label = 'Quantity'}*}
                            {*{$LOCALFIELDINFO.type = 'integer'}*}
                            {*{$INFO = Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($LOCALFIELDINFO))}*}
                                {*<input name="Quantity{$ID}" class="input-large nameField" type="number" value="{(int) $SERVICE_DETAILS.quantity}" data-validation-engine="validate[{if $SERVICE->get(is_required)}required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" data-fieldinfo="{$INFO}" />*}
                            {*</td>*}
                            {*<td class='fieldLabel'>*}
                                {*<label class="muted pull-right marginRight10px">{if $SERVICE->get(is_required)}<span class="redColor">*</span>{/if}Hours</label>*}
                            {*</td>*}
                            {*<td class='fieldValue'>*}
                            {*{assign var=FIELDNAME value='Hours'|cat:$ID}*}
                            {*{if $SERVICE->get(is_required)}{$LOCALFIELDINFO.mandatory = true}{else}{$LOCALFIELDINFO.mandatory = false}{/if}*}
                            {*{$LOCALFIELDINFO.name = $FIELDNAME}*}
                            {*{$LOCALFIELDINFO.label = 'Hours'}*}
                            {*{$LOCALFIELDINFO.type = 'double'}*}
                            {*{$INFO = Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($LOCALFIELDINFO))}*}
                                {*<input name="Hours{$ID}" class="input-large nameField" type="number" value="{number_format((float)$SERVICE_DETAILS.hours, 2, '.', '')}" data-decimal-seperator="." data-group-seperator="," data-number-of-decimal-places="2" step=".25" data-validation-engine="validate[{if $SERVICE->get(is_required)}required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" data-fieldinfo="{$INFO}" />*}
                            {*</td>*}
                           {*</tr>*}
                           {*<tr>*}
                            {*<td class='fieldLabel'>*}
                                {*<label class="muted pull-right marginRight10px">{if $SERVICE->get(is_required)}<span class="redColor">*</span>{/if}Rate</label>*}
                            {*</td>*}
                            {*<td class='fieldValue'>*}
                            {*{assign var=FIELDNAME value='Rate'|cat:$ID}*}
                            {*{if $SERVICE->get(is_required)}{$LOCALFIELDINFO.mandatory = true}{else}{$LOCALFIELDINFO.mandatory = false}{/if}*}
                            {*{$LOCALFIELDINFO.name = $FIELDNAME}*}
                            {*{$LOCALFIELDINFO.label = 'Rate'}*}
                            {*{$LOCALFIELDINFO.type = 'currency'}*}
                            {*{$INFO = Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($LOCALFIELDINFO))}*}
                                {*<div class="row-fluid">*}

								{*<td class='fieldLabel'>&nbsp;</td>*}
								{*<td class='fieldValue'>&nbsp;</td>*}
								{**}
							{*</tr>*}
						{elseif $RATE_TYPE eq "Per Cu Ft"}
							<tr>
								<td class='fieldLabel'>
									<label class="muted pull-right marginRight10px">{if $SERVICE->get(is_required)}<span class="redColor">*</span>{/if}Cubic Feet</label>
								</td>
								<td class='fieldValue'>
								{assign var=FIELDNAME value='CubicFeet'|cat:$ID}
									{if $SERVICE->get(is_required)}{$LOCALFIELDINFO.mandatory = true}{else}{$LOCALFIELDINFO.mandatory = false}{/if}
									{$LOCALFIELDINFO.name = $FIELDNAME}
									{$LOCALFIELDINFO.label = 'Cubic Feet'}
									{$LOCALFIELDINFO.type = 'double'}
									{$INFO = Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($LOCALFIELDINFO))}
                                    <input min="0" data-validator='[{ldelim}"name":"PositiveNumber"{rdelim}]' data-validation-engine="validate[funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" name="CubicFeet{$ID}" class="input-large nameField" type="number" value="{number_format((float)$SERVICE_DETAILS.cubicfeet, 2, '.', '')}" data-decimal-seperator="." data-group-seperator="," data-number-of-decimal-places="2" step=".5" data-validation-engine="validate[{if $SERVICE->get(is_required)}required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" data-fieldinfo="{$INFO}" />
								</td>
								<td class='fieldLabel'>
									<label class="muted pull-right marginRight10px">{if $SERVICE->get(is_required)}<span class="redColor">*</span>{/if}Rate</label>
								</td>
								<td class='fieldValue'>
								{assign var=FIELDNAME value='Rate'|cat:$ID}
									{if $SERVICE->get(is_required)}{$LOCALFIELDINFO.mandatory = true}{else}{$LOCALFIELDINFO.mandatory = false}{/if}
									{$LOCALFIELDINFO.name = $FIELDNAME}
									{$LOCALFIELDINFO.label = 'Rate'}
									{$LOCALFIELDINFO.type = 'currency'}
									{$INFO = Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($LOCALFIELDINFO))}
									<div class="row-fluid">

                                   <div class="input-prepend">
                                     <span class="span10">
                                      <span class="add-on">&#36;</span>
                                      <input name="Rate{$ID}" step="0.01" class="input-medium currencyField" type="number" value="{if !empty($SERVICE_DETAILS.rate)}{number_format((float)$SERVICE_DETAILS.rate, 2, '.', '')}{else}{number_format((float)$SERVICE->get('hourlysimple_rate'), 2, '.', '')}{/if}" data-decimal-seperator="." data-group-seperator="," data-number-of-decimal-places="2" data-validation-engine="validate[{if $SERVICE->get(is_required)}required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" data-fieldinfo="{$INFO}" />
                                     </span>
                                   </div>
                                </div>
                            </td>
                           <td class='fieldLabel'>&nbsp;</td>
                           <td class='fieldValue'>&nbsp;</td>
                           </tr>
                        {elseif $RATE_TYPE eq "Flat Charge"}
                           <tr>
                            <td class='fieldLabel'>
                                <div class="input-prepend">
                                   <span><label class="muted pull-right marginRight10px">{if $SERVICE->get(is_required)}<span class="redColor">*</span>{/if}Rate</label></span>
                                     <span><input name = "rateIncluded{$ID}" class="muted pull-right" style = "margin-right: 10px" type = "checkbox" {if $SERVICE_DETAILS.rate_included == '1'}checked{/if}></span>
                                </div>
                            </td>
                            <td class='fieldValue'>
                            {assign var=FIELDNAME value='Rate'|cat:$ID}
                            {if $SERVICE->get(is_required)}{$LOCALFIELDINFO.mandatory = true}{else}{$LOCALFIELDINFO.mandatory = false}{/if}
                            {$LOCALFIELDINFO.name = $FIELDNAME}
                            {$LOCALFIELDINFO.label = 'Rate'}
                            {$LOCALFIELDINFO.type = 'double'}
                            {$INFO = Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($LOCALFIELDINFO))}
                                <div class="row-fluid">
                                   <div class="input-prepend">
                                     <span class="span10">
                                      <span class="add-on">&#36;</span>
                                      <input name="Rate{$ID}" step="0.01" class="input-medium currencyField" type="number" value="{if !empty($SERVICE_DETAILS.rate)}{number_format((float)$SERVICE_DETAILS.rate, 2, '.', '')}{else}{number_format((float)$SERVICE->get('flat_rate'), 2, '.', '')}{/if}" data-decimal-seperator="." data-group-seperator="," data-number-of-decimal-places="2" step=".01" data-validation-engine="validate[{if $SERVICE->get(is_required)}required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" data-fieldinfo="{$INFO}" />
                                     </span>
                                   </div>
                                </div>
                            </td>

                            <td class='fieldLabel'>&nbsp;</td>
                            <td class='fieldValue'>&nbsp;</td>

                           </tr>
						{elseif $RATE_TYPE eq "Per CWT" || $RATE_TYPE eq "SIT First Day Rate"}
							<tr>
								<td class='fieldLabel'>
									<label class="muted pull-right marginRight10px">{if $SERVICE->get(is_required)}<span class="redColor">*</span>{/if}Weight</label>
								</td>
								<td class='fieldValue'>
								{assign var=FIELDNAME value='Weight'|cat:$ID}
								{if $SERVICE->get(is_required)}{$LOCALFIELDINFO.mandatory = true}{else}{$LOCALFIELDINFO.mandatory = false}{/if}
								{$LOCALFIELDINFO.name = $FIELDNAME}
								{$LOCALFIELDINFO.label = 'Weight'}
								{$LOCALFIELDINFO.type = 'integer'}
								{$INFO = Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($LOCALFIELDINFO))}
									<input data-validator='[{ldelim}"name":"WholeNumber"{rdelim}]' data-validation-engine="validate[funcCall[Vtiger_Base_Validator_Js.invokeValidation]]"  min="0" name="Weight{$ID}" class="input-large nameField" type="number" value="{(int) $SERVICE_DETAILS.weight}" data-validation-engine="validate[{if $SERVICE->get(is_required)}required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" data-fieldinfo="{$INFO}" />
								</td>
								<td class='fieldLabel'>
									<label class="muted pull-right marginRight10px">{if $SERVICE->get(is_required)}<span class="redColor">*</span>{/if}Rate</label>
								</td>
								<td class='fieldValue'>
								{assign var=FIELDNAME value='Rate'|cat:$ID}
								{if $SERVICE->get(is_required)}{$LOCALFIELDINFO.mandatory = true}{else}{$LOCALFIELDINFO.mandatory = false}{/if}
								{$LOCALFIELDINFO.name = $FIELDNAME}
								{$LOCALFIELDINFO.label = 'Rate'}
								{$LOCALFIELDINFO.type = 'currency'}
								{$INFO = Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($LOCALFIELDINFO))}
									<div class="row-fluid">


                                   <div class="input-prepend">
                                     <span class="span10">
                                      <span class="add-on">&#36;</span>
                                      <input name="Rate{$ID}" step="0.01" class="input-medium currencyField" type="number" value="{if !empty($SERVICE_DETAILS.rate)}{number_format((float)$SERVICE_DETAILS.rate, 2, '.', '')}{else}{number_format((float)$SERVICE->get('cwt_rate'), 2, '.', '')}{/if}" data-decimal-seperator="." data-group-seperator="," data-number-of-decimal-places="2" data-validation-engine="validate[{if $SERVICE->get(is_required)}required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" data-fieldinfo="{$INFO}" />
                                     </span>
                                   </div>
                                </div>
                            </td>
                           </tr>
                        {elseif $RATE_TYPE eq "Per Cu Ft/Per Day"}
                           <tr>
                            <td class='fieldLabel'>
                                <label class="muted pull-right marginRight10px">{if $SERVICE->get(is_required)}<span class="redColor">*</span>{/if}Cubic Feet</label>
                            </td>
                            <td class='fieldValue'>
                            {assign var=FIELDNAME value='CubicFeet'|cat:$ID}
                            {if $SERVICE->get(is_required)}{$LOCALFIELDINFO.mandatory = true}{else}{$LOCALFIELDINFO.mandatory = false}{/if}
                            {$LOCALFIELDINFO.name = $FIELDNAME}
                            {$LOCALFIELDINFO.label = 'Cubic Feet'}
                            {$LOCALFIELDINFO.type = 'double'}
                            {$INFO = Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($LOCALFIELDINFO))}
                                <input min="0" data-validator='[{ldelim}"name":"PositiveNumber"{rdelim}]' data-validation-engine="validate[funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" name="CubicFeet{$ID}" class="input-large nameField" type="number" value="{number_format((float)$SERVICE->get('cubicfeet'), 2, '.', '')}" data-decimal-seperator="." data-group-seperator="," data-number-of-decimal-places="2" step=".5" data-validation-engine="validate[{if $SERVICE->get(is_required)}required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" data-fieldinfo="{$INFO}" />
                            </td>
                            <td class='fieldLabel'>
                                <label class="muted pull-right marginRight10px">{if $SERVICE->get(is_required)}<span class="redColor">*</span>{/if}Days</label>
                            </td>
                            <td class='fieldValue'>
                            {assign var=FIELDNAME value='Days'|cat:$ID}
                            {if $SERVICE->get(is_required)}{$LOCALFIELDINFO.mandatory = true}{else}{$LOCALFIELDINFO.mandatory = false}{/if}
                            {$LOCALFIELDINFO.name = $FIELDNAME}
                            {$LOCALFIELDINFO.label = 'Days'}
                            {$LOCALFIELDINFO.type = 'integer'}
                            {$INFO = Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($LOCALFIELDINFO))}
                                <input name="Days{$ID}" class="input-large nameField" type="number" value="{number_format((float)$SERVICE_DETAILS.days, 0, '.', '')}" data-validation-engine="validate[{if $SERVICE->get(is_required)}required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" data-fieldinfo="{$INFO}" />
                            </td>
                           </tr>
                           <tr>
                            <td class='fieldLabel'>
                                <label class="muted pull-right marginRight10px">{if $SERVICE->get(is_required)}<span class="redColor">*</span>{/if}Rate</label>
                            </td>
                            <td class='fieldValue'>
                            {assign var=FIELDNAME value='Rate'|cat:$ID}
                            {if $SERVICE->get(is_required)}{$LOCALFIELDINFO.mandatory = true}{else}{$LOCALFIELDINFO.mandatory = false}{/if}
                            {$LOCALFIELDINFO.name = $FIELDNAME}
                            {$LOCALFIELDINFO.label = 'Rate'}
                            {$LOCALFIELDINFO.type = 'currency'}
                            {$INFO = Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($LOCALFIELDINFO))}
                                <div class="row-fluid">

										<div class="input-prepend">
											<span class="span10">
												<span class="add-on">&#36;</span>
												<input name="Rate{$ID}" step="0.01" class="input-medium currencyField" type="number" value="{if $SERVICE_DETAILS.rate != 0}{number_format((float)$SERVICE_DETAILS.rate, 2, '.', '')}{else}{number_format((float)$SERVICE->get('cuftperday_rate'), 2, '.', '')}{/if}" data-decimal-seperator="." data-group-seperator="," data-number-of-decimal-places="2" data-validation-engine="validate[{if $SERVICE->get(is_required)}required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" data-fieldinfo="{$INFO}" />
											</span>
										</div>
									</div>
								</td>

								<td class='fieldLabel'>&nbsp;</td>
								<td class='fieldValue'>&nbsp;</td>

							</tr>
						{elseif $RATE_TYPE eq "Per CWT/Per Month"}
							<tr>
								<td class='fieldLabel'>
									<label class="muted pull-right marginRight10px">{if $SERVICE->get(is_required)}<span class="redColor">*</span>{/if}Weight</label>
								</td>
								<td class='fieldValue'>
								{assign var=FIELDNAME value='Weight'|cat:$ID}
								{if $SERVICE->get(is_required)}{$LOCALFIELDINFO.mandatory = true}{else}{$LOCALFIELDINFO.mandatory = false}{/if}
								{$LOCALFIELDINFO.name = $FIELDNAME}
								{$LOCALFIELDINFO.label = 'Weight'}
								{$LOCALFIELDINFO.type = 'integer'}
								{$INFO = Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($LOCALFIELDINFO))}
									<input data-validator='[{ldelim}"name":"WholeNumber"{rdelim}]' data-validation-engine="validate[funcCall[Vtiger_Base_Validator_Js.invokeValidation]]"  min="0" name="Weight{$ID}" class="input-large nameField" type="number" value="{(int) $SERVICE_DETAILS.weight}" data-validation-engine="validate[{if $SERVICE->get(is_required)}required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" data-fieldinfo="{$INFO}" />
								</td>
								<td class='fieldLabel'>
									<label class="muted pull-right marginRight10px">{if $SERVICE->get(is_required)}<span class="redColor">*</span>{/if}Months</label>
								</td>
								<td class='fieldValue'>
								{assign var=FIELDNAME value='Months'|cat:$ID}
								{if $SERVICE->get(is_required)}{$LOCALFIELDINFO.mandatory = true}{else}{$LOCALFIELDINFO.mandatory = false}{/if}
								{$LOCALFIELDINFO.name = $FIELDNAME}
								{$LOCALFIELDINFO.label = 'Months'}
								{$LOCALFIELDINFO.type = 'integer'}
								{$INFO = Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($LOCALFIELDINFO))}
									<input data-validator='[{ldelim}"name":"WholeNumber"{rdelim}]' data-validation-engine="validate[funcCall[Vtiger_Base_Validator_Js.invokeValidation]]"  min="0" name="Months{$ID}" class="input-large nameField" type="number" value="{(int) $SERVICE_DETAILS.months}" data-validation-engine="validate[{if $SERVICE->get(is_required)}required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" data-fieldinfo="{$INFO}" />
								</td>
							</tr>
							<tr>
								<td class='fieldLabel'>
									<label class="muted pull-right marginRight10px">{if $SERVICE->get(is_required)}<span class="redColor">*</span>{/if}Rate</label>
								</td>
								<td class='fieldValue'>
								{assign var=FIELDNAME value='Rate'|cat:$ID}
								{if $SERVICE->get(is_required)}{$LOCALFIELDINFO.mandatory = true}{else}{$LOCALFIELDINFO.mandatory = false}{/if}
								{$LOCALFIELDINFO.name = $FIELDNAME}
								{$LOCALFIELDINFO.label = 'Rate'}
								{$LOCALFIELDINFO.type = 'currency'}
								{$INFO = Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($LOCALFIELDINFO))}
									<div class="row-fluid">


                                   <div class="input-prepend">
                                     <span class="span10">
                                      <span class="add-on">&#36;</span>
                                      <input name="Rate{$ID}" step="0.01" class="input-medium currencyField" type="number" value="{if $SERVICE_DETAILS.rate != 0}{number_format((float)$SERVICE_DETAILS.rate, 2, '.', '')}{else}{number_format($SERVICE->get('cwtpermonth_rate'), 2, '.', '')}{/if}" data-decimal-seperator="." data-group-seperator="," data-number-of-decimal-places="2" data-validation-engine="validate[{if $SERVICE->get(is_required)}required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" data-fieldinfo="{$INFO}" />
                                     </span>
                                   </div>
                                </div>
                            </td>
                            <td class='fieldLabel'>&nbsp;</td>
                            <td class='fieldValue'>&nbsp;</td>
                           </tr>
                        {elseif $RATE_TYPE eq "Per Cu Ft/Per Month"}
                           <tr>
                            <td class='fieldLabel'>
                                <label class="muted pull-right marginRight10px">{if $SERVICE->get(is_required)}<span class="redColor">*</span>{/if}Cubic Feet</label>
                            </td>
                            <td class='fieldValue'>
                            {assign var=FIELDNAME value='CubicFeet'|cat:$ID}
                            {if $SERVICE->get(is_required)}{$LOCALFIELDINFO.mandatory = true}{else}{$LOCALFIELDINFO.mandatory = false}{/if}
                            {$LOCALFIELDINFO.name = $FIELDNAME}
                            {$LOCALFIELDINFO.label = 'Cubic Feet'}
                            {$LOCALFIELDINFO.type = 'double'}
                            {$INFO = Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($LOCALFIELDINFO))}
                                <input min="0" data-validator='[{ldelim}"name":"PositiveNumber"{rdelim}]' data-validation-engine="validate[funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" name="CubicFeet{$ID}" class="input-large nameField" type="number" value="{number_format((float)$SERVICE_DETAILS.cubicfeet, 2, '.', '')}" data-decimal-seperator="." data-group-seperator="," data-number-of-decimal-places="2" step=".5" data-validation-engine="validate[{if $SERVICE->get(is_required)}required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" data-fieldinfo="{$INFO}" />
                            </td>
                            <td class='fieldLabel'>
                                <label class="muted pull-right marginRight10px">{if $SERVICE->get(is_required)}<span class="redColor">*</span>{/if}Months</label>
                            </td>
                            <td class='fieldValue'>
                            {assign var=FIELDNAME value='Months'|cat:$ID}
                            {if $SERVICE->get(is_required)}{$LOCALFIELDINFO.mandatory = true}{else}{$LOCALFIELDINFO.mandatory = false}{/if}
                            {$LOCALFIELDINFO.name = $FIELDNAME}
                            {$LOCALFIELDINFO.label = 'Months'}
                            {$LOCALFIELDINFO.type = 'integer'}
                            {$INFO = Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($LOCALFIELDINFO))}
                                <input name="Months{$ID}" class="input-large nameField" type="number" value="{number_format((float)$SERVICE_DETAILS.months, 0, '.', '')}" data-validation-engine="validate[{if $SERVICE->get(is_required)}required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" data-fieldinfo="{$INFO}" />
                            </td>
                           </tr>
                           <tr>
                            <td class='fieldLabel'>
                                <label class="muted pull-right marginRight10px">{if $SERVICE->get(is_required)}<span class="redColor">*</span>{/if}Rate</label>
                            </td>
                            <td class='fieldValue'>
                            {assign var=FIELDNAME value='Rate'|cat:$ID}
                            {if $SERVICE->get(is_required)}{$LOCALFIELDINFO.mandatory = true}{else}{$LOCALFIELDINFO.mandatory = false}{/if}
                            {$LOCALFIELDINFO.name = $FIELDNAME}
                            {$LOCALFIELDINFO.label = 'Rate'}
                            {$LOCALFIELDINFO.type = 'currency'}
                            {$INFO = Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($LOCALFIELDINFO))}
                                <div class="row-fluid">

										<div class="input-prepend">
											<span class="span10">
												<span class="add-on">&#36;</span>
												<input name="Rate{$ID}" step="0.01" class="input-medium currencyField" type="number" value="{if $SERVICE_DETAILS.rate != 0}{number_format((float)$SERVICE_DETAILS.rate, 2, '.', '')}{else}{number_format((float)$SERVICE->get('cuftpermonth_rate'), 2, '.', '')}{/if}" data-decimal-seperator="." data-group-seperator="," data-number-of-decimal-places="2" data-validation-engine="validate[{if $SERVICE->get(is_required)}required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" data-fieldinfo="{$INFO}" />
											</span>
										</div>
									</div>
								</td>
							</tr>
						{elseif $RATE_TYPE eq "Per Quantity/Per Day"}
							<tr>
								<td class='fieldLabel'>
									<label class="muted pull-right marginRight10px">{if $SERVICE->get(is_required)}<span class="redColor">*</span>{/if}Quantity</label>
								</td>
								<td class='fieldValue'>
								{assign var=FIELDNAME value='Quantity'|cat:$ID}
								{if $SERVICE->get(is_required)}{$LOCALFIELDINFO.mandatory = true}{else}{$LOCALFIELDINFO.mandatory = false}{/if}
								{$LOCALFIELDINFO.name = $FIELDNAME}
								{$LOCALFIELDINFO.label = 'Quantity'}
								{$LOCALFIELDINFO.type = 'integer'}
								{$INFO = Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($LOCALFIELDINFO))}
									<input data-validator='[{ldelim}"name":"WholeNumber"{rdelim}]' data-validation-engine="validate[funcCall[Vtiger_Base_Validator_Js.invokeValidation]]"  min="0" name="Quantity{$ID}" class="input-large nameField" type="number" value="{(int) $SERVICE_DETAILS.quantity}" data-validation-engine="validate[{if $SERVICE->get(is_required)}required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" data-fieldinfo="{$INFO}" />
								</td>
								<td class='fieldLabel'>
									<label class="muted pull-right marginRight10px">{if $SERVICE->get(is_required)}<span class="redColor">*</span>{/if}Days</label>
								</td>
								<td class='fieldValue'>
								{assign var=FIELDNAME value='Days'|cat:$ID}
								{if $SERVICE->get(is_required)}{$LOCALFIELDINFO.mandatory = true}{else}{$LOCALFIELDINFO.mandatory = false}{/if}
								{$LOCALFIELDINFO.name = $FIELDNAME}
								{$LOCALFIELDINFO.label = 'Days'}
								{$LOCALFIELDINFO.type = 'integer'}
								{$INFO = Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($LOCALFIELDINFO))}
									<input name="Days{$ID}" class="input-large nameField" type="number" value="{number_format((float)$SERVICE_DETAILS.days, 0, '.', '')}" data-validation-engine="validate[{if $SERVICE->get(is_required)}required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" data-fieldinfo="{$INFO}" />
								</td>
							</tr>
							<tr>
								<td class='fieldLabel'>
									<label class="muted pull-right marginRight10px">{if $SERVICE->get(is_required)}<span class="redColor">*</span>{/if}Rate</label>
								</td>
								<td class='fieldValue'>
								{assign var=FIELDNAME value='Rate'|cat:$ID}
								{if $SERVICE->get(is_required)}{$LOCALFIELDINFO.mandatory = true}{else}{$LOCALFIELDINFO.mandatory = false}{/if}
								{$LOCALFIELDINFO.name = $FIELDNAME}
								{$LOCALFIELDINFO.label = 'Rate'}
								{$LOCALFIELDINFO.type = 'currency'}
								{$INFO = Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($LOCALFIELDINFO))}
									<div class="row-fluid">
										<div class="input-prepend">
											<span class="span10">
												<span class="add-on">&#36;</span>
												<input name="Rate{$ID}" step="0.01" class="input-medium currencyField" type="number" value="{if $SERVICE_DETAILS.rate != 0}{number_format((float)$SERVICE_DETAILS.rate, 2, '.', '')}{else}{number_format((float)$SERVICE->get('qtyperday_rate'), 2, '.', '')}{/if}" data-decimal-seperator="." data-group-seperator="," data-number-of-decimal-places="2" data-validation-engine="validate[{if $SERVICE->get(is_required)}required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" data-fieldinfo="{$INFO}" />
											</span>
										</div>
									</div>
								</td>
								<td class='fieldLabel'>&nbsp;</td>
								<td class='fieldValue'>&nbsp;</td>
							</tr>
						{elseif $RATE_TYPE eq "Per Quantity/Per Month"}
							<tr>
								<td class='fieldLabel'>
									<label class="muted pull-right marginRight10px">{if $SERVICE->get(is_required)}<span class="redColor">*</span>{/if}Quantity</label>
								</td>
								<td class='fieldValue'>
								{assign var=FIELDNAME value='Quantity'|cat:$ID}
								{if $SERVICE->get(is_required)}{$LOCALFIELDINFO.mandatory = true}{else}{$LOCALFIELDINFO.mandatory = false}{/if}
								{$LOCALFIELDINFO.name = $FIELDNAME}
								{$LOCALFIELDINFO.label = 'Quantity'}
								{$LOCALFIELDINFO.type = 'integer'}
								{$INFO = Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($LOCALFIELDINFO))}
									<input data-validator='[{ldelim}"name":"WholeNumber"{rdelim}]' data-validation-engine="validate[funcCall[Vtiger_Base_Validator_Js.invokeValidation]]"  min="0" name="Quantity{$ID}" class="input-large nameField" type="number" value="{(int) $SERVICE_DETAILS.quantity}" data-validation-engine="validate[{if $SERVICE->get(is_required)}required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" data-fieldinfo="{$INFO}" />
								</td>
								<td class='fieldLabel'>
									<label class="muted pull-right marginRight10px">{if $SERVICE->get(is_required)}<span class="redColor">*</span>{/if}Months</label>
								</td>
								<td class='fieldValue'>
								{assign var=FIELDNAME value='Months'|cat:$ID}
								{if $SERVICE->get(is_required)}{$LOCALFIELDINFO.mandatory = true}{else}{$LOCALFIELDINFO.mandatory = false}{/if}
								{$LOCALFIELDINFO.name = $FIELDNAME}
								{$LOCALFIELDINFO.label = 'Months'}
								{$LOCALFIELDINFO.type = 'integer'}
								{$INFO = Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($LOCALFIELDINFO))}
									<input name="Months{$ID}" class="input-large nameField" type="number" value="{number_format((float)$SERVICE_DETAILS.months, 0, '.', '')}" data-validation-engine="validate[{if $SERVICE->get(is_required)}required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" data-fieldinfo="{$INFO}" />
								</td>
							</tr>
							<tr>
								<td class='fieldLabel'>
									<label class="muted pull-right marginRight10px">{if $SERVICE->get(is_required)}<span class="redColor">*</span>{/if}Rate</label>
								</td>
								<td class='fieldValue'>
								{assign var=FIELDNAME value='Rate'|cat:$ID}
								{if $SERVICE->get(is_required)}{$LOCALFIELDINFO.mandatory = true}{else}{$LOCALFIELDINFO.mandatory = false}{/if}
								{$LOCALFIELDINFO.name = $FIELDNAME}
								{$LOCALFIELDINFO.label = 'Rate'}
								{$LOCALFIELDINFO.type = 'currency'}
								{$INFO = Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($LOCALFIELDINFO))}
									<div class="row-fluid">
										<div class="input-prepend">
											<span class="span10">
												<span class="add-on">&#36;</span>
												<input name="Rate{$ID}" step="0.01" class="input-medium currencyField" type="number" value="{if !empty($SERVICE_DETAILS.rate)}{number_format((float)$SERVICE_DETAILS.rate, 2, '.', '')}{else}{number_format((float)$SERVICE->get('qtypermonth_rate'), 2, '.', '')}{/if}" data-decimal-seperator="." data-group-seperator="," data-number-of-decimal-places="2" data-validation-engine="validate[{if $SERVICE->get(is_required)}required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" data-fieldinfo="{$INFO}" />
											</span>
										</div>
									</div>
								</td>
								<td class='fieldLabel'>&nbsp;</td>
								<td class='fieldValue'>&nbsp;</td>
							</tr>
						{elseif $RATE_TYPE eq "Charge Per $100 (Valuation)"}
							<tr>
								<td class='fieldLabel'>
									<label class="muted pull-right marginRight10px">Valuation Type</label>
								</td>
								<td class='fieldValue'>
								{assign var=FIELDNAME value='ValuationType'|cat:$ID}
								{if $SERVICE->get(is_required)}{$LOCALFIELDINFO.mandatory = true}{else}{$LOCALFIELDINFO.mandatory = false}{/if}
								{$LOCALFIELDINFO.name = $FIELDNAME}
								{$LOCALFIELDINFO.label = 'Valuation Type'}
								{$LOCALFIELDINFO.type = 'picklist'}
								{$INFO = Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($LOCALFIELDINFO))}
									<select class="chzn-select" name="ValuationType{$ID}" class ="localValuationType" data-validation-engine="validate[{if $SERVICE->get(is_required)}required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" data-fieldinfo="{$INFO}">
										<option value="2" selected>{vtranslate('LBL_SELECT_OPTION','Vtiger')}</option>
										{if $SERVICE->get(valuation_released) eq 1}
											<option value="1" {if $SERVICE_DETAILS.released eq 1}selected{/if}>Released Valuation</option>
										{/if}
										<option value="0" {if $SERVICE_DETAILS.released eq 0}selected{/if}>Full Valuation</option>
									</select>
								</td>
								<td class='fieldLabel fullValuation{$ID}'>&nbsp;</td>
								<td class='fieldValue fullValuation{$ID}'>&nbsp;</td>

								<td class='fieldLabel releasedValuation{$ID} hide'>
									<label class="muted pull-right marginRight10px">Coverage Per Lb.</label>
								</td>
								<td class='fieldValue releasedValuation{$ID} hide'>
								{assign var=FIELDNAME value='Coverage'|cat:$ID}
								{if $SERVICE->get(is_required)}{$LOCALFIELDINFO.mandatory = true}{else}{$LOCALFIELDINFO.mandatory = false}{/if}
								{$LOCALFIELDINFO.name = $FIELDNAME}
								{$LOCALFIELDINFO.label = 'Coverage Per Lb.'}
								{$LOCALFIELDINFO.type = 'currency'}
								{$INFO = Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($LOCALFIELDINFO))}
									<div class="row-fluid">
										<div class="input-prepend">
											<span class="span10">
												<span class="add-on">&#36;</span>
												<input name="Coverage{$ID}" step="0.01" class="input-medium currencyField" type="number" value="{if !empty($SERVICE_DETAILS.released_amount)}{$SERVICE_DETAILS.released_amount}{else}{$SERVICE->get(valuation_releasedamount)}{/if}" data-decimal-seperator="." data-group-seperator="," data-number-of-decimal-places="2" step=".05" data-validation-engine="validate[{if $SERVICE->get(is_required)}required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" data-fieldinfo="{$INFO}" />
											</span>
										</div>
									</div>
								</td>
								{* <td class='fieldLabel fullValuation{$ID} hide'>
									<label class="muted pull-right marginRight10px">Rate</label>
								</td>
								<td class='fieldValue fullValuation{$ID} hide'>
								{assign var=FIELDNAME value='Rate'|cat:$ID}
								{if $SERVICE->get(is_required)}{$LOCALFIELDINFO.mandatory = true}{else}{$LOCALFIELDINFO.mandatory = false}{/if}
								{$LOCALFIELDINFO.name = $FIELDNAME}
								{$LOCALFIELDINFO.label = 'Rate'}
								{$LOCALFIELDINFO.type = 'currency'}
								{$INFO = Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($LOCALFIELDINFO))}
									<div class="row-fluid">
										<div class="input-prepend">
											<span class="span10">
												<span class="add-on">&#36;</span>
												<input name="Rate{$ID}" step="0.01" class="input-medium currencyField" type="number" value="{if !empty($SERVICE_DETAILS.rate)}{number_format((float)$SERVICE_DETAILS.rate, 2, '.', '')}{/if}" data-decimal-seperator="." data-group-seperator="," data-number-of-decimal-places="2" data-validation-engine="validate[{if $SERVICE->get(is_required)}required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" data-fieldinfo="{$INFO}" />
											</span>
										</div>
									</div>
								</td> *}
							</tr>
							<tr class="fullValuation{$ID}">
								<td class='fieldLabel'>
									<label class="muted pull-right marginRight10px">{if $SERVICE->get(is_required)}<span class="redColor">*</span>{/if}Valuation Amount</label>
								</td>
								<td class='fieldValue'>
								{assign var=FIELDNAME value='Amount'|cat:$ID}
								{if $SERVICE->get(is_required)}{$LOCALFIELDINFO.mandatory = true}{else}{$LOCALFIELDINFO.mandatory = false}{/if}
								{$LOCALFIELDINFO.name = $FIELDNAME}
								{$LOCALFIELDINFO.label = 'Amount'}
								{$LOCALFIELDINFO.type = 'currency'}
								{$INFO = Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($LOCALFIELDINFO))}
									<div class="row-fluid">
										<div class="input-prepend">
                                            <span class="span10">
                                                <span class="add-on">&#36;</span>
                                                <input name="Amount{$ID}" step="0.01" class="input-medium currencyField" type="number" value="{number_format((float)$SERVICE_DETAILS.amount, 2, '.', '')}" data-decimal-seperator="." data-group-seperator="," data-number-of-decimal-places="2" data-validation-engine="validate[{if $SERVICE->get(is_required)}required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" data-fieldinfo="{$INFO}" />
                                            </span>
										</div>
									</div>
								</td>
								<td class='fieldLabel'>
									<label class="muted pull-right marginRight10px">{if $SERVICE->get(is_required)}<span class="redColor">*</span>{/if}Deductible</label>
								</td>
								<td class='fieldValue'>
								{assign var=FIELDNAME value='Deductible'|cat:$ID}
								{if $SERVICE->get(is_required)}{$LOCALFIELDINFO.mandatory = true}{else}{$LOCALFIELDINFO.mandatory = false}{/if}
								{$LOCALFIELDINFO.name = $FIELDNAME}
								{$LOCALFIELDINFO.label = 'Deductible'}
								{$LOCALFIELDINFO.type = 'picklist'}
								{$INFO = Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($LOCALFIELDINFO))}
									{assign var=PICKLIST_VALUES value=$SERVICE->getDeductiblePicklists()}
									<select class="chzn-select" name="Deductible{$ID}" class ="localDeductiblePick" data-validation-engine="validate[{if $SERVICE->get(is_required)}required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" data-fieldinfo="{$INFO}">
										<option value="">{vtranslate('LBL_SELECT_OPTION','Vtiger')}</option>
										{foreach item=PICKLIST_VALUE key=PICKLIST_NAME from=$PICKLIST_VALUES}
											<option value="{$PICKLIST_VALUE}" {if ($SERVICE_DETAILS.deductible == $PICKLIST_VALUE)}selected{/if}>{$PICKLIST_VALUE}</option>
										{/foreach}
									</select>
								</td>
							</tr>
							<tr class="fullValuation{$ID}">
								<td class='fieldLabel'>
									<label class="muted pull-right marginRight10px">{if $SERVICE->get(is_required)}<span class="redColor">*</span>{/if}Rate per pound for Min Declared Valuation</label>
								</td>
								<td class='fieldValue'>
								{assign var=FIELDNAME value='Multiplier'|cat:$ID}
								{if $SERVICE->get(is_required)}{$LOCALFIELDINFO.mandatory = true}{else}{$LOCALFIELDINFO.mandatory = false}{/if}
								{$LOCALFIELDINFO.name = $FIELDNAME}
								{$LOCALFIELDINFO.label = 'Rate per pound for Min Declared Valuation'}
								{$LOCALFIELDINFO.type = 'currency'}
								{$INFO = Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($LOCALFIELDINFO))}
									<div class="row-fluid">
										<div class="input-prepend">
											<span class="span10">
												<span class="add-on">&#36;</span>
												<input name="Multiplier{$ID}" step="0.01" class="input-medium currencyField" type="number" value="{if $SERVICE_DETAILS.multiplier != 0}{number_format((float)$SERVICE_DETAILS.multiplier, 2, '.', '')}{else}{number_format((float)$SERVICE->get('multiplier'), 2, '.', '')}{/if}" data-decimal-seperator="." data-group-seperator="," data-number-of-decimal-places="2" data-validation-engine="validate[{if $SERVICE->get(is_required)}required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" data-fieldinfo="{$INFO}" step = '.01'/>
											</span>
										</div>
									</div>
								</td>
								<td class='fieldLabel'>
									<label class="muted pull-right marginRight10px">{if $SERVICE->get(is_required)}<span class="redColor">*</span>{/if}Rate</label>
								</td>
								<td class='fieldValue'>
								{assign var=FIELDNAME value='Rate'|cat:$ID}
								{if $SERVICE->get(is_required)}{$LOCALFIELDINFO.mandatory = true}{else}{$LOCALFIELDINFO.mandatory = false}{/if}
								{$LOCALFIELDINFO.name = $FIELDNAME}
								{$LOCALFIELDINFO.label = 'Rate'}
								{$LOCALFIELDINFO.type = 'currency'}
								{$INFO = Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($LOCALFIELDINFO))}
									<div class="row-fluid">
										<div class="input-prepend">
											<span class="span10">
												<span class="add-on">&#36;</span>
												<input name="Rate{$ID}" step="0.01" class="input-medium currencyField" type="number" value="{if $SERVICE_DETAILS.rate != 0}{number_format((float)$SERVICE_DETAILS.rate, 2, '.', '')}{else}{number_format((float)$SERVICE->get('qtypermonth_rate'), 2, '.', '')}{/if}" data-decimal-seperator="." data-group-seperator="," data-number-of-decimal-places="2" data-validation-engine="validate[{if $SERVICE->get(is_required)}required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" data-fieldinfo="{$INFO}" step = '.01'/>
											</span>
										</div>
									</div>
								</td>
							</tr>
						{elseif $RATE_TYPE eq "Tabled Valuation"}
							<tr>
								<td class='fieldLabel'>
									<label class="muted pull-right marginRight10px">Valuation Type</label>
								</td>
								<td class='fieldValue'>
								{assign var=FIELDNAME value='ValuationType'|cat:$ID}
								{if $SERVICE->get(is_required)}{$LOCALFIELDINFO.mandatory = true}{else}{$LOCALFIELDINFO.mandatory = false}{/if}
								{$LOCALFIELDINFO.name = $FIELDNAME}
								{$LOCALFIELDINFO.label = 'Valuation Type'}
								{$LOCALFIELDINFO.type = 'picklist'}
								{$INFO = Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($LOCALFIELDINFO))}
									<select class="chzn-select" name="ValuationType{$ID}" class ="localValuationType" data-validation-engine="validate[{if $SERVICE->get(is_required)}required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" data-fieldinfo="{$INFO}">
										<option value="2" selected>{vtranslate('LBL_SELECT_OPTION','Vtiger')}</option>
										{if $SERVICE->get(valuation_released) eq 1}
											<option value="1" {if $SERVICE_DETAILS.released eq 1}selected{/if}>Released Valuation</option>
										{/if}
										<option value="0" {if $SERVICE_DETAILS.released eq 0}selected{/if}>Full Valuation</option>
									</select>
								</td>
								<td class='fieldLabel noValuation{$ID}'>&nbsp;</td>
								<td class='fieldValue noValuation{$ID}'>&nbsp;</td>

								<td class='fieldLabel releasedValuation{$ID} hide'>
									<label class="muted pull-right marginRight10px">Coverage Per Lb.</label>
								</td>
								<td class='fieldValue releasedValuation{$ID} hide'>
								{assign var=FIELDNAME value='Coverage'|cat:$ID}
								{if $SERVICE->get(is_required)}{$LOCALFIELDINFO.mandatory = true}{else}{$LOCALFIELDINFO.mandatory = false}{/if}
								{$LOCALFIELDINFO.name = $FIELDNAME}
								{$LOCALFIELDINFO.label = 'Coverage Per Lb.'}
								{$LOCALFIELDINFO.type = 'currency'}
								{$INFO = Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($LOCALFIELDINFO))}
									<div class="row-fluid">
										<div class="input-prepend">
											<span class="span10">
												<span class="add-on">&#36;</span>
												<input name="Coverage{$ID}" step="0.01" class="input-medium currencyField" type="number" value="{if !empty($SERVICE_DETAILS.released_amount)}{$SERVICE_DETAILS.released_amount}{else}{$SERVICE->get(valuation_releasedamount)}{/if}" data-decimal-seperator="." data-group-seperator="," data-number-of-decimal-places="2" step=".05" data-validation-engine="validate[{if $SERVICE->get(is_required)}required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" data-fieldinfo="{$INFO}" />
											</span>
										</div>
									</div>
								</td>
								<td class='fieldLabel fullValuation{$ID} hide'>
									<label class="muted pull-right marginRight10px">Rate</label>
								</td>
								<td class='fieldValue fullValuation{$ID} hide'>
								{assign var=FIELDNAME value='Rate'|cat:$ID}
								{if $SERVICE->get(is_required)}{$LOCALFIELDINFO.mandatory = true}{else}{$LOCALFIELDINFO.mandatory = false}{/if}
								{$LOCALFIELDINFO.name = $FIELDNAME}
								{$LOCALFIELDINFO.label = 'Rate'}
								{$LOCALFIELDINFO.type = 'currency'}
								{$INFO = Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($LOCALFIELDINFO))}
									<div class="row-fluid">
										<div class="input-prepend">
											<span class="span10">
												<span class="add-on">&#36;</span>
												<input name="Rate{$ID}" step="1" class="input-medium currencyField" type="number" value="{if !empty($SERVICE_DETAILS.rate)}{number_format((float)$SERVICE_DETAILS.rate, 2, '.', '')}{/if}" data-decimal-seperator="." data-group-seperator="," data-number-of-decimal-places="2" data-validation-engine="validate[{if $SERVICE->get(is_required)}required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" data-fieldinfo="{$INFO}" />
											</span>
										</div>
									</div>
								</td>
							</tr>
							<tr class ='fullValuation{$ID} hide'>
								<td class='fieldLabel'>
									<label class="muted pull-right marginRight10px">Deductible</label>
								</td>
								<td class='fieldValue'>
								{assign var=FIELDNAME value='Deductible'|cat:$ID}
								{if $SERVICE->get(is_required)}{$LOCALFIELDINFO.mandatory = true}{else}{$LOCALFIELDINFO.mandatory = false}{/if}
								{$LOCALFIELDINFO.name = $FIELDNAME}
								{$LOCALFIELDINFO.label = 'Deductible'}
								{$LOCALFIELDINFO.type = 'picklist'}
								{$INFO = Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($LOCALFIELDINFO))}
									{assign var=PICKLIST_VALUES value=$SERVICE->getDistinct('deductible')}
									<select class="chzn-select localValuationPick valDed" name="Deductible{$ID}" data-validation-engine="validate[{if $SERVICE->get(is_required)}required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" data-fieldinfo="{$INFO}">
										<option value="">{vtranslate('LBL_SELECT_OPTION','Vtiger')}</option>
										{foreach item=PICKLIST_VALUE key=PICKLIST_NAME from=$PICKLIST_VALUES}
											<option value="{$PICKLIST_VALUE}" {if $PICKLIST_VALUE eq $SERVICE_DETAILS.deductible}selected{/if}>{$PICKLIST_VALUE}</option>
										{/foreach}
									</select>
								</td>
								<td class='fieldLabel'>
									<label class="muted pull-right marginRight10px">Amount</label>
								</td>
								<td class='fieldValue'>
								{assign var=FIELDNAME value='Amount'|cat:$ID}
								{if $SERVICE->get(is_required)}{$LOCALFIELDINFO.mandatory = true}{else}{$LOCALFIELDINFO.mandatory = false}{/if}
								{$LOCALFIELDINFO.name = $FIELDNAME}
								{$LOCALFIELDINFO.label = 'Amount'}
								{$LOCALFIELDINFO.type = 'picklist'}
								{$INFO = Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($LOCALFIELDINFO))}
									{assign var=PICKLIST_VALUES value=$SERVICE->getDistinct('amount')}
									<select class="chzn-select localValuationPick valAm" name="Amount{$ID}" data-validation-engine="validate[{if $SERVICE->get(is_required)}required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" data-fieldinfo="{$INFO}">
										<option value="">{vtranslate('LBL_SELECT_OPTION','Vtiger')}</option>
										{foreach item=PICKLIST_VALUE key=PICKLIST_NAME from=$PICKLIST_VALUES}
											<option value="{$PICKLIST_VALUE}" {if $PICKLIST_VALUE eq $SERVICE_DETAILS.amount}selected{/if}>{$PICKLIST_VALUE}</option>
										{/foreach}
									</select>
								</td>
							</tr>
                            <tr class='fullValuation{$ID}'>
								<td class='fieldLabel'>
									<label class="muted pull-right marginRight10px">{if $SERVICE->get(is_required)}<span class="redColor">*</span>{/if}Rate per pound for Min Declared Valuation</label>
								</td>
								<td class='fieldValue'>
								{assign var=FIELDNAME value='Multiplier'|cat:$ID}
								{if $SERVICE->get(is_required)}{$LOCALFIELDINFO.mandatory = true}{else}{$LOCALFIELDINFO.mandatory = false}{/if}
								{$LOCALFIELDINFO.name = $FIELDNAME}
								{$LOCALFIELDINFO.label = 'Multiplier'}
								{$LOCALFIELDINFO.type = 'currency'}
								{$INFO = Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($LOCALFIELDINFO))}
									<div class="row-fluid">
										<div class="input-prepend">
											<span class="span10">
												<span class="add-on">&#36;</span>
												<input name="Multiplier{$ID}" step="0.01" class="input-medium currencyField" type="number" value="{if $SERVICE_DETAILS.multiplier != 0}{number_format((float)$SERVICE_DETAILS.multiplier, 2, '.', '')}{else}{number_format((float)$SERVICE->get('multiplier'), 2, '.', '')}{/if}" data-decimal-seperator="." data-group-seperator="," data-number-of-decimal-places="2" data-validation-engine="validate[{if $SERVICE->get(is_required)}required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" data-fieldinfo="{$INFO}" step = '.01'/>
											</span>
										</div>
									</div>
								</td>
								<td class='fieldLabel'>&nbsp;</td>
								<td class='fieldValue'>&nbsp;</td>
                            </tr>
						{elseif $RATE_TYPE eq "Bulky List"}
							<td class="fluid" colspan="4" style='padding:0;'>
								<table name="Service{$ID}" class="table table-bordered" style='border:none;'>
									<tr>
									{assign var=bulkyWidth value=9}
									{assign var=chargePer value=$SERVICE->get(bulky_chargeper)}
									{assign var=bulkyWidth value=12}
										<td class='fieldLabel' style='width:14%;text-align:center;margin:auto'>&nbsp;
											<input type="hidden" class="hide" name="NumBulkys{$ID}" value="{$SERVICE_DETAILS.bulkyList|@count}">
										</td>
										<td class='fieldLabel' style='width:{$bulkyWidth-5}%;text-align:center;margin:auto'>{($chargePer == 'Hourly')? 'Hours' : 'Quantity'}</td>
										<td class='fieldLabel' style='width:{$bulkyWidth}%;text-align:center;margin:auto'>Weight Additive</td>
										<td class='fieldLabel' style='width:{$bulkyWidth}%;text-align:center;margin:auto'>Rate</td>
										<td class='fieldLabel' style='width:14%;text-align:center;margin:auto'>&nbsp;</td>
										<td class='fieldLabel' style='width:{$bulkyWidth}%;text-align:center;margin:auto'>{($chargePer == 'Hourly')? 'Hours' : 'Quantity'}</td>
										<td class='fieldLabel' style='width:{$bulkyWidth}%;text-align:center;margin:auto'>Weight Additive</td>
										<td class='fieldLabel' style='width:{$bulkyWidth}%;text-align:center;margin:auto'>Rate</td>
									</tr>
									{assign var=bulkyList value=$SERVICE_DETAILS.bulkyList}
									{if !empty($bulkyList)}
										{foreach item=BULKY key=BULKY_INDEX from=$bulkyList}
											{if ($BULKY_INDEX%2 == 0)}
												<tr>
											{/if}
											<td class='fieldLabel' style='width:14%;'>
												<label class="muted pull-right marginRight10px">{$BULKY[0]}</label>
												<input type="hidden" class="hide" name="bulkyDescription{$ID}-{$BULKY_INDEX}" value="{$BULKY[0]}">
												<input type="hidden" class="hide" name="BulkyID{$ID}-{$BULKY_INDEX}" value="{$BULKY[4]}">
												<input type="hidden" class="hide" name="BulkyCost{$ID}-{$BULKY_INDEX}" id="{$BULKY[4]}" value="{$BULKY[5]}">
											</td>
											<td class='fieldValue' style='width:{$bulkyWidth}%;'>
												<input name="Qty{$ID}-{$BULKY_INDEX}" class="input-large nameField" type="number" style="width:85%;text-align:center;margin:auto" {if ($chargePer == 'Hourly')}step=".25"{/if} value="{$BULKY[1]}"/>
											</td>
											<td class='fieldValue' style='width:{$bulkyWidth}%;'>
												<input name="WeightAdd{$ID}-{$BULKY_INDEX}" class="input-large nameField" type="number" style="width:85%;text-align:center;margin:auto" value="{$BULKY[2]}" />
											</td>
											<td class='fieldValue' style='width:{$bulkyWidth}%;'>
												<div class="row-fluid">

													<div class="input-prepend">
														<span class="span10">
															<span class="add-on">&#36;</span>
															<input name="Rate{$ID}-{$BULKY_INDEX}" step="0.01" style="width:62%;text-align:center;margin:auto" class="input-small currencyField" type="text" value="{number_format((float)$BULKY[3], 2, '.', '')}" data-decimal-seperator="." data-group-seperator="," data-number-of-decimal-places="2"/>
														</span>
													</div>
												</div>
											</td>
										{/foreach}
										{if ($BULKY_INDEX%2 == 1)}
											</tr>
										{/if}
										{if ($BULKY_INDEX%2 == 0)}
											<td class='fieldValue' colspan="4">&nbsp;</td>
										{/if}
									{/if}

								</table>
							</td>
							</tr>

                        {elseif $RATE_TYPE eq "Per CWT/Per Day"  || $RATE_TYPE eq "SIT Additional Day Rate" }
                           <tr>
                            <td class='fieldLabel'>
                                <label class="muted pull-right marginRight10px">{if $SERVICE->get(is_required)}<span class="redColor">*</span>{/if}Weight</label>
                            </td>
                            <td class='fieldValue'>
                            {assign var=FIELDNAME value='Weight'|cat:$ID}
                            {if $SERVICE->get(is_required)}{$LOCALFIELDINFO.mandatory = true}{else}{$LOCALFIELDINFO.mandatory = false}{/if}
                            {$LOCALFIELDINFO.name = $FIELDNAME}
                            {$LOCALFIELDINFO.label = 'Weight'}
                            {$LOCALFIELDINFO.type = 'integer'}
                            {$INFO = Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($LOCALFIELDINFO))}
                                <input name="Weight{$ID}" class="input-large nameField" type="number" value="{(int) $SERVICE_DETAILS.weight}" data-validation-engine="validate[{if $SERVICE->get(is_required)}required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" data-fieldinfo="{$INFO}" />
                            </td>
                            <td class='fieldLabel'>
                                <label class="muted pull-right marginRight10px">{if $SERVICE->get(is_required)}<span class="redColor">*</span>{/if}Days</label>
                            </td>
                            <td class='fieldValue'>
                            {assign var=FIELDNAME value='Days'|cat:$ID}
                            {if $SERVICE->get(is_required)}{$LOCALFIELDINFO.mandatory = true}{else}{$LOCALFIELDINFO.mandatory = false}{/if}
                            {$LOCALFIELDINFO.name = $FIELDNAME}
                            {$LOCALFIELDINFO.label = 'Days'}
                            {$LOCALFIELDINFO.type = 'integer'}
                            {$INFO = Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($LOCALFIELDINFO))}
                                <input name="Days{$ID}" class="input-large nameField" type="number" value="{(int) $SERVICE_DETAILS.days}" data-validation-engine="validate[{if $SERVICE->get(is_required)}required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" data-fieldinfo="{$INFO}" />
                            </td>
                           </tr>
                           <tr>
                            <td class='fieldLabel'>
                                <label class="muted pull-right marginRight10px">{if $SERVICE->get(is_required)}<span class="redColor">*</span>{/if}Rate</label>
                            </td>
                            <td class='fieldValue'>
                            {assign var=FIELDNAME value='Rate'|cat:$ID}
                            {if $SERVICE->get(is_required)}{$LOCALFIELDINFO.mandatory = true}{else}{$LOCALFIELDINFO.mandatory = false}{/if}
                            {$LOCALFIELDINFO.name = $FIELDNAME}
                            {$LOCALFIELDINFO.label = 'Rate'}
                            {$LOCALFIELDINFO.type = 'currency'}
                            {$INFO = Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($LOCALFIELDINFO))}
                                <div class="row-fluid">


                                   <div class="input-prepend">
                                     <span class="span10">
                                      <span class="add-on">&#36;</span>
                                      <input name="Rate{$ID}" step="0.01" class="input-medium currencyField" type="number" value="{if $SERVICE_DETAILS.rate != 0}{number_format((float)$SERVICE_DETAILS.rate, 2, '.', '')}{else}{number_format((float)$SERVICE->get('cwtperday_rate'), 2, '.', '')}{/if}" data-decimal-seperator="." data-group-seperator="," data-number-of-decimal-places="2" data-validation-engine="validate[{if $SERVICE->get(is_required)}required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" data-fieldinfo="{$INFO}" />
                                     </span>
                                   </div>
                                </div>
                            </td>

                            <td class='fieldLabel'>&nbsp;</td>
                            <td class='fieldValue'>&nbsp;</td>

                           </tr>
                        {elseif $RATE_TYPE eq "Per Quantity"}
                           <tr>
                            <td class='fieldLabel'>
                                <label class="muted pull-right marginRight10px">{if $SERVICE->get(is_required)}<span class="redColor">*</span>{/if}Quantity</label>
                            </td>
                            <td class='fieldValue'>
                            {assign var=FIELDNAME value='Quantity'|cat:$ID}
                            {if $SERVICE->get(is_required)}{$LOCALFIELDINFO.mandatory = true}{else}{$LOCALFIELDINFO.mandatory = false}{/if}
                            {$LOCALFIELDINFO.name = $FIELDNAME}
                            {$LOCALFIELDINFO.label = 'Quantity'}
                            {$LOCALFIELDINFO.type = 'integer'}
                            {$INFO = Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($LOCALFIELDINFO))}
                                <input data-validator='[{ldelim}"name":"WholeNumber"{rdelim}]' data-validation-engine="validate[funcCall[Vtiger_Base_Validator_Js.invokeValidation]]"  min="0" name="Quantity{$ID}" class="input-large nameField" type="number" value="{(int) $SERVICE_DETAILS.quantity}" data-validation-engine="validate[{if $SERVICE->get(is_required)}required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" data-fieldinfo="{$INFO}" />
                            </td>
                            <td class='fieldLabel'>
                                <label class="muted pull-right marginRight10px">{if $SERVICE->get(is_required)}<span class="redColor">*</span>{/if}Rate</label>
                            </td>
                            <td class='fieldValue'>
                            {assign var=FIELDNAME value='Rate'|cat:$ID}
                            {if $SERVICE->get(is_required)}{$LOCALFIELDINFO.mandatory = true}{else}{$LOCALFIELDINFO.mandatory = false}{/if}
                            {$LOCALFIELDINFO.name = $FIELDNAME}
                            {$LOCALFIELDINFO.label = 'Rate'}
                            {$LOCALFIELDINFO.type = 'currency'}
                            {$INFO = Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($LOCALFIELDINFO))}
                                <div class="row-fluid">


                                   <div class="input-prepend">
                                     <span class="span10">
                                      <span class="add-on">&#36;</span>
                                      <input name="Rate{$ID}" step="0.01" class="input-medium currencyField" type="number" value="{if !empty($SERVICE_DETAILS.rate)}{number_format((float)$SERVICE_DETAILS.rate, 2, '.', '')}{else}{number_format((float)$SERVICE->get('qty_rate'), 2, '.', '')}{/if}" data-decimal-seperator="." data-group-seperator="," data-number-of-decimal-places="2" data-validation-engine="validate[{if $SERVICE->get(is_required)}required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" data-fieldinfo="{$INFO}" />
                                     </span>
                                   </div>
                                </div>
                            </td>
                           </tr>
                        {elseif $RATE_TYPE eq "Packing Items"}
                            <tr>
                              <td class='fieldLabel'>
                               <label class="muted pull-right marginRight10px">Sales Tax</label>
                              </td>
                              <td>
                               <div class="input-append">
                                   <input type="number" class="input-medium" min=0" max="100" name="SalesTax{$ID}"
                                   value="{$SERVICE_DETAILS.sales_tax}" step="any" /><span class="add-on">%</span>
                               </div>
                              </td>
                              <td class='fieldLabel'>&nbsp;</td>
                              <td class='fieldValue'>&nbsp;</td>
                            </tr>
                            <td class="fluid" colspan="4" style='padding:0;'>
                            <table name="Service{$ID}" class="table table-bordered" style='border:none;'>
                            {*UNUSED? assign var=packingList value=$SERVICE->getEntries('packingitems')*}
                            {assign var=hasContainers value=$SERVICE->get('packing_containers')}
                            {assign var=hasPacking value=$SERVICE->get('packing_haspacking')}
                            {assign var=hasUnpacking value=$SERVICE->get('packing_hasunpacking')}
                            {assign var=numColumns value=(2*$hasContainers)+(2*$hasPacking)+(2*$hasUnpacking)}
                            {assign var=perRow value=1}
                            {if $numColumns <= 5}
                                {assign var=numColumns value=($numColumns*2)+1}
                                {assign var=perRow value=2}
                            {/if}
                            {assign var=numColumns value=($numColumns+1)}
                            {assign var=packingWidth value=(100/$numColumns)}
                                <tr>
                                   <td class='fieldLabel' style='width:{$packingWidth}%;text-align:center;margin:auto'>
                                     <input type="hidden" class="hide" name="numPacking{$ID}" value="{$SERVICE_DETAILS.packingList|@count}">
                                   </td>

                                   {if $hasContainers eq 1}
                                     <td class='fieldLabel' style='width:{$packingWidth}%;text-align:center;margin:auto'>Container Quantity</td>
                                     <td class='fieldLabel' style='width:{$packingWidth}%;text-align:center;margin:auto'>Container Rate</td>
                                   {/if}
                                   {if $hasPacking eq 1}
                                     <td class='fieldLabel' style='width:{$packingWidth}%;text-align:center;margin:auto'>Pack Quantity</td>
                                     <td class='fieldLabel' style='width:{$packingWidth}%;text-align:center;margin:auto'>Pack Rate</td>
                                   {/if}
                                   {if $hasUnpacking eq 1}
                                     <td class='fieldLabel' style='width:{$packingWidth}%;text-align:center;margin:auto'>Unpack Quantity</td>
                                     <td class='fieldLabel' style='width:{$packingWidth}%;text-align:center;margin:auto'>Unpack Rate</td>
                                   {/if}
                                   {if $perRow == 2}
                                     <td class='fieldLabel' style='width:{$packingWidth}%;text-align:center;margin:auto'>Name</td>

                                     {if $hasContainers eq 1}
                                      <td class='fieldLabel' style='width:{$packingWidth}%;text-align:center;margin:auto'>Container Quantity</td>
                                      <td class='fieldLabel' style='width:{$packingWidth}%;text-align:center;margin:auto'>Container Rate</td>
                                     {/if}
                                     {if $hasPacking eq 1}
                                      <td class='fieldLabel' style='width:{$packingWidth}%;text-align:center;margin:auto'>Pack Quantity</td>
                                      <td class='fieldLabel' style='width:{$packingWidth}%;text-align:center;margin:auto'>Pack Rate</td>
                                     {/if}
                                     {if $hasUnpacking eq 1}
                                      <td class='fieldLabel' style='width:{$packingWidth}%;text-align:center;margin:auto'>Unpack Quantity</td>
                                      <td class='fieldLabel' style='width:{$packingWidth}%;text-align:center;margin:auto'>Unpack Rate</td>
                                     {/if}
                                   {/if}
                                </tr>
                            <tr>

                            {foreach item=PACK_ITEM key=PACK_INDEX from=$SERVICE_DETAILS.packingList}
                                <td class='fieldLabel' style='width:{$packingWidth}%;text-align:center;margin:auto'>
                                   <label class="muted pull-right marginRight10px">{$PACK_ITEM[0]}</label>
                                   <input type="hidden" class="hide" name="Name{$ID}-{$PACK_INDEX}" value="{$PACK_ITEM[0]}">
                                   <input type="hidden" class="hide" name="PackID{$ID}-{$PACK_INDEX}" value="{$PACK_ITEM[7]}">

                                   <input type="hidden" class="hide {$PACK_ITEM[7]}" name="ContainerCost{$ID}-{$PACK_INDEX}" value="{$PACK_ITEM['cost_container']}">
                                   <input type="hidden" class="hide {$PACK_ITEM[7]}" name="PackingCost{$ID}-{$PACK_INDEX}" value="{$PACK_ITEM[9]}">
                                   <input type="hidden" class="hide {$PACK_ITEM[7]}" name="UnpackingCost{$ID}-{$PACK_INDEX}" value="{$PACK_ITEM[10]}">
                                </td>
                                {if $hasContainers eq 1}
                                   <td class='fieldValue' style='width:{$packingWidth}%;text-align:center;margin:auto'>
                                     <input data-validator='[{ldelim}"name":"WholeNumber"{rdelim}]' data-validation-engine="validate[funcCall[Vtiger_Base_Validator_Js.invokeValidation]]"  min="0" name="containerQty{$ID}-{$PACK_INDEX}" class="input-large nameField" type="number" style="width:85%;text-align:center;margin:auto" value="{$PACK_ITEM[1]}" min=0 />
                                   </td>
                                   <td class='fieldValue' style='width:{$packingWidth}%;margin:auto'>
                                     <div class="row-fluid">

                                      <div class="input-prepend">
                                          <span class="span10">
                                             <span class="add-on">&#36;</span>
                                             <input name="containerRate{$ID}-{$PACK_INDEX}" step="0.01" style="width:62%;text-align:center;margin:auto" class="input-small currencyField" type="text" value="{number_format((float)$PACK_ITEM[2], 2, '.', '')}" data-decimal-seperator="." data-group-seperator="," data-number-of-decimal-places="2"/>
                                          </span>
                                      </div>
                                     </div>
                                   </td>
                                {/if}
                                {if $hasPacking eq 1}
                                   <td class='fieldValue' style='width:{$packingWidth}%;text-align:center;margin:auto'>
                                     <input data-validator='[{ldelim}"name":"WholeNumber"{rdelim}]' data-validation-engine="validate[funcCall[Vtiger_Base_Validator_Js.invokeValidation]]"  min="0" name="packQty{$ID}-{$PACK_INDEX}" class="input-large nameField" type="number" style="width:85%;text-align:center;margin:auto" value="{$PACK_ITEM[3]}" min=0 />
                                   </td>
                                   <td class='fieldValue' style='width:{$packingWidth}%;margin:auto'>
                                     <div class="row-fluid">

                                      <div class="input-prepend">
                                          <span class="span10">
                                             <span class="add-on">&#36;</span>
                                             <input name="packRate{$ID}-{$PACK_INDEX}" step="0.01" style="width:62%;text-align:center;margin:auto" class="input-small currencyField" type="text" value="{number_format((float)$PACK_ITEM[4], 2, '.', '')}" data-decimal-seperator="." data-group-seperator="," data-number-of-decimal-places="2"/>
                                          </span>
                                      </div>
                                     </div>
                                   </td>
                                {/if}
                                {if $hasUnpacking eq 1}
                                   <td class='fieldValue' style='width:{$packingWidth}%;text-align:center;margin:auto'>
                                     <input data-validator='[{ldelim}"name":"WholeNumber"{rdelim}]' data-validation-engine="validate[funcCall[Vtiger_Base_Validator_Js.invokeValidation]]"  min="0" name="unpackQty{$ID}-{$PACK_INDEX}" class="input-large nameField" type="number" style="width:85%;text-align:center;margin:auto" value="{$PACK_ITEM[5]}" min=0 />
                                   </td>
                                   <td class='fieldValue' style='width:{$packingWidth}%;margin:auto'>
                                     <div class="row-fluid">

                                      <div class="input-prepend">
                                          <span class="span10">
                                             <span class="add-on">&#36;</span>
                                             <input name="unpackRate{$ID}-{$PACK_INDEX}" step="0.01" style="width:62%;text-align:center;margin:auto" class="input-small currencyField" type="text" value="{number_format((float)$PACK_ITEM[6], 2, '.', '')}" data-decimal-seperator="." data-group-seperator="," data-number-of-decimal-places="2"/>
                                          </span>
                                      </div>
                                     </div>
                                   </td>
                                {/if}
                                {if ($PACK_INDEX%$perRow == 1)||($perRow ==1)}
                                   </tr>
                                   <tr>
                                {/if}
                            {/foreach}
                            {if ($perRow==2)&&($PACK_INDEX%$perRow==0)}
                                <td colspan="{$numColumns/2}">&nbsp;</td>
                            {/if}
                            </tr>
                            <input type="hidden" class="hide" name="NumPacking{$ID}" value="{$PACK_INDEX}">
                            </table>
                           </td>
                           </tr>
                        {elseif $RATE_TYPE eq "Crating Item"}
                             <td class="fluid" colspan="4" style='padding:0;'>
                               <table name="CratingItems{$ID}" class="table table-bordered misc" style='border:none'>
                                {if !$LOCK_RATING}
                                    <tr class='fieldLabel' colspan="11">
                                       <td colspan="11">
                                         <button type="button" name="localAddCrate-{$ID}" id="localAddCrate">+</button><button type="button" name="localAddCrate2-{$ID}" id="localAddCrate2" style="clear:right;float:right">+</button><br>
                                       </td>
                                    </tr>
                                {/if}
                                <tr>
                                    {assign var=cratingWidth value=100/11}
                                    <td class='fieldLabel' style='width:{$cratingWidth-5}%;text-align:center;margin:auto'>&nbsp;
                                       <input type="hidden" class="hide" name="numCrates{$ID}" value="{$SERVICE_DETAILS.highestCrate}">
                                    </td>
                                    <td class='fieldLabel' style='width:{$cratingWidth}%;text-align:center;margin:auto'>ID</td>
                                    <td class='fieldLabel' style='width:{$cratingWidth+5}%;text-align:center;margin:auto'>Description</td>
                                    <td class='fieldLabel' style='width:{$cratingWidth}%;text-align:center;margin:auto'>Length</td>
                                    <td class='fieldLabel' style='width:{$cratingWidth}%;text-align:center;margin:auto'>Width</td>
                                    <td class='fieldLabel' style='width:{$cratingWidth}%;text-align:center;margin:auto'>Height</td>
                                    <td class='fieldLabel' style='width:{$cratingWidth}%;text-align:center;margin:auto'>Inches Added</td>
                                    <td class='fieldLabel' style='width:{$cratingWidth}%;text-align:center;margin:auto'>Crating Quantity</td>
                                    <td class='fieldLabel' style='width:{$cratingWidth}%;text-align:center;margin:auto'>Crating Rate</td>
                                    <td class='fieldLabel' style='width:{$cratingWidth}%;text-align:center;margin:auto'>Uncrating Quantity</td>
                                    <td class='fieldLabel' style='width:{$cratingWidth}%;text-align:center;margin:auto'>Uncrating Rate</td>
                                </tr>
                                <tr class="hide localDefaultCrate localCrateRow newItemRow">
                                    <td class='fieldValue' style='width:{$cratingWidth-5}%;text-align:center;margin:auto'>
                                       <a class="deleteCartonButton"><i title="Delete" class="icon-trash alignMiddle"></i></a>
                                       <input type="hidden" class="hide" name="CratingCost{$ID}" value="">
                                       <input type="hidden" class="hide" name="UncratingCost{$ID}" value="">
                                    </td>
                                    <td class='fieldValue' style='width:{$cratingWidth}%;text-align:center;margin:auto'>
                                       {assign var=FIELDNAME value='crateID'|cat:$ID}
                                       {$LOCALFIELDINFO.mandatory = true}
                                       {$LOCALFIELDINFO.name = $FIELDNAME}
                                       {$LOCALFIELDINFO.label = 'ID'}
                                       {$LOCALFIELDINFO.type = 'text'}
                                       {$INFO = Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($LOCALFIELDINFO))}
                                       <input type="text" class="input-large" name="crateID{$ID}" style="width:85%;text-align:center;margin:auto" value="C-0" data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" data-fieldinfo="{$INFO}">
                                    </td>
                                    <td class='fieldValue' style='width:{$cratingWidth+5}%;text-align:center;margin:auto'>
                                       {assign var=FIELDNAME value='Description'|cat:$ID}
                                       {$LOCALFIELDINFO.mandatory = true}
                                       {$LOCALFIELDINFO.name = $FIELDNAME}
                                       {$LOCALFIELDINFO.label = 'Description'}
                                       {$LOCALFIELDINFO.type = 'text'}
                                       <input type="text" class="input-large" name="Description{$ID}" style="width:85%;text-align:center;margin:auto" value="" data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" data-fieldinfo="{$INFO}">
                                    </td>
                                    <td class='fieldValue' style='width:{$cratingWidth}%;text-align:center;margin:auto'>
                                       {assign var=FIELDNAME value='Length'|cat:$ID}
                                       {$LOCALFIELDINFO.mandatory = true}
                                       {$LOCALFIELDINFO.name = $FIELDNAME}
                                       {$LOCALFIELDINFO.label = 'Length'}
                                       {$LOCALFIELDINFO.type = 'integer'}
                                       <input name="Length{$ID}" class="input-large nameField" type="number" style="width:85%;text-align:center;margin:auto" value="" data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" data-fieldinfo="{$INFO}"/>
                                    </td>
                                    <td class='fieldValue' style='width:{$cratingWidth}%;text-align:center;margin:auto'>
                                       {assign var=FIELDNAME value='Width'|cat:$ID}
                                       {$LOCALFIELDINFO.mandatory = true}
                                       {$LOCALFIELDINFO.name = $FIELDNAME}
                                       {$LOCALFIELDINFO.label = 'Width'}
                                       {$LOCALFIELDINFO.type = 'integer'}
                                       <input name="Width{$ID}" class="input-large nameField" type="number" style="width:85%;text-align:center;margin:auto" value="" data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" data-fieldinfo="{$INFO}"/>
                                    </td>
                                    <td class='fieldValue' style='width:{$cratingWidth}%;text-align:center;margin:auto'>
                                       {assign var=FIELDNAME value='Height'|cat:$ID}
                                       {$LOCALFIELDINFO.mandatory = true}
                                       {$LOCALFIELDINFO.name = $FIELDNAME}
                                       {$LOCALFIELDINFO.label = 'Height'}
                                       {$LOCALFIELDINFO.type = 'integer'}
                                       <input name="Height{$ID}" class="input-large nameField" type="number" style="width:85%;text-align:center;margin:auto" value="" data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" data-fieldinfo="{$INFO}"/>
                                    </td>
                                    <td class='fieldValue' style='width:{$cratingWidth}%;text-align:center;margin:auto'>
                                       {assign var=FIELDNAME value='InchesAdded'|cat:$ID}
                                       {$LOCALFIELDINFO.mandatory = true}
                                       {$LOCALFIELDINFO.name = $FIELDNAME}
                                       {$LOCALFIELDINFO.label = 'Inches Added'}
                                       {$LOCALFIELDINFO.type = 'integer'}
                                       <input data-validator='[{ldelim}"name":"WholeNumber"{rdelim}]' data-validation-engine="validate[funcCall[Vtiger_Base_Validator_Js.invokeValidation]]"  min="0" name="InchesAdded{$ID}" class="input-large nameField" type="number" style="width:85%;text-align:center;margin:auto" value="{$SERVICE->get('crate_inches')}" data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" data-fieldinfo="{$INFO}"/>
                                    </td>
                                    <td class='fieldValue' style='width:{$cratingWidth}%;text-align:center;margin:auto'>
                                       {assign var=FIELDNAME value='CratingQty'|cat:$ID}
                                       {$LOCALFIELDINFO.mandatory = true}
                                       {$LOCALFIELDINFO.name = $FIELDNAME}
                                       {$LOCALFIELDINFO.label = 'Crating Quantity'}
                                       {$LOCALFIELDINFO.type = 'integer'}
                                       <input data-validator='[{ldelim}"name":"WholeNumber"{rdelim}]' data-validation-engine="validate[funcCall[Vtiger_Base_Validator_Js.invokeValidation]]"  min="0" name="CratingQty{$ID}" class="input-large nameField" type="number" style="width:85%;text-align:center;margin:auto" value="" data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" data-fieldinfo="{$INFO}"/>
                                    </td>
                                    <td class='fieldValue' style='width:{$cratingWidth}%;margin:auto'>
                                    <div class="row-fluid">

                                       <div class="input-prepend">
                                         <span class="span10">
                                          <span class="add-on">&#36;</span>
                                          {assign var=FIELDNAME value='CratingRate'|cat:$ID}
                                          {$LOCALFIELDINFO.mandatory = true}
                                          {$LOCALFIELDINFO.name = $FIELDNAME}
                                          {$LOCALFIELDINFO.label = 'Crating Rate'}
                                          {$LOCALFIELDINFO.type = 'currency'}
                                          <input name="CratingRate{$ID}" step="0.01" style="width:62%;margin:auto" class="input-medium currencyField" type="text" value="{$SERVICE->get('crate_packrate')}" data-decimal-seperator="." data-group-seperator="," data-number-of-decimal-places="2" data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" data-fieldinfo="{$INFO}"/>
                                         </span>
                                       </div>
                                    </div>
                                    </td>
                                    <td class='fieldValue' style='width:{$cratingWidth}%;text-align:center;margin:auto'>
                                       {assign var=FIELDNAME value='UncratingQty'|cat:$ID}
                                       {$LOCALFIELDINFO.mandatory = true}
                                       {$LOCALFIELDINFO.name = $FIELDNAME}
                                       {$LOCALFIELDINFO.label = 'Uncrating Quantity'}
                                       {$LOCALFIELDINFO.type = 'integer'}
                                       <input data-validator='[{ldelim}"name":"WholeNumber"{rdelim}]' data-validation-engine="validate[funcCall[Vtiger_Base_Validator_Js.invokeValidation]]"  min="0" name="UncratingQty{$ID}" class="input-large nameField" type="number" style="width:85%;text-align:center;margin:auto" value="" data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" data-fieldinfo="{$INFO}"/>
                                    </td>
                                    <td class='fieldValue' style='width:{$cratingWidth}%;margin:auto'>
                                       <div class="row-fluid">

                                         <div class="input-prepend">
                                          <span class="span10">
                                              <span class="add-on">&#36;</span>
                                              {assign var=FIELDNAME value='CratingRate'|cat:$ID}
                                              {$LOCALFIELDINFO.mandatory = true}
                                              {$LOCALFIELDINFO.name = $FIELDNAME}
                                              {$LOCALFIELDINFO.label = 'Uncrating Rate'}
                                              {$LOCALFIELDINFO.type = 'currency'}
                                              <input name="UncratingRate{$ID}" step="0.01" style="width:62%;margin:auto" class="input-medium currencyField" type="text" value="{$SERVICE->get('crate_unpackrate')}" data-decimal-seperator="." data-group-seperator="," data-number-of-decimal-places="2" data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" data-fieldinfo="{$INFO}"/>
                                          </span>
                                         </div>
                                       </div>
                                    </td>
                                </tr>
                                {assign var=cratingList value=$SERVICE_DETAILS.cratingList}
                                {if !empty($cratingList)}
                                    {foreach item=CRATE key=CRATE_INDEX from=$cratingList}
                                        {assign var=STRING value=$ID|cat:'-'}
                                        {assign var=STRING value=$STRING|cat:$CRATE[10]}
                                        <tr class="localCrateRow" id="localCrateRow{$CRATE[10]}">
                                           <td class='fieldValue' style='width:{$cratingWidth-5}%;text-align:center;margin:auto'>
                                             <a class="deleteCartonButton"><i title="Delete" class="icon-trash alignMiddle"></i></a>
                                             <input type="hidden" class="hide" name="CratingCost{$ID}-{$CRATE[10]}" value="{$CRATE[11]}">
                                             <input type="hidden" class="hide" name="UncratingCost{$ID}-{$CRATE[10]}" value="{$CRATE[12]}">
                                           </td>
                                           <td class='fieldValue' style='width:{$cratingWidth}%;text-align:center;margin:auto'>
                                             {assign var=FIELDNAME value='crateID'|cat:$STRING}
                                             {$LOCALFIELDINFO.mandatory = true}
                                             {$LOCALFIELDINFO.name = $FIELDNAME}
                                             {$LOCALFIELDINFO.label = 'ID'}
                                             {$LOCALFIELDINFO.type = 'text'}
                                             <input type="text" class="input-large" name="crateID{$ID}-{$CRATE[10]}" style="width:85%;text-align:center;margin:auto" value="{$CRATE[0]}" data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" data-fieldinfo="{$INFO}">
                                           </td>
                                           <td class='fieldValue' style='width:{$cratingWidth+5}%;text-align:center;margin:auto'>
                                             {assign var=FIELDNAME value='Description'|cat:$STRING}
                                             {$LOCALFIELDINFO.mandatory = true}
                                             {$LOCALFIELDINFO.name = $FIELDNAME}
                                             {$LOCALFIELDINFO.label = 'Description'}
                                             {$LOCALFIELDINFO.type = 'text'}
                                             <input type="text" class="input-large" name="Description{$ID}-{$CRATE[10]}" style="width:85%;text-align:center;margin:auto" value="{$CRATE[1]}" data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" data-fieldinfo="{$INFO}">
                                           </td>
                                           <td class='fieldValue' style='width:{$cratingWidth}%;text-align:center;margin:auto'>
                                             {assign var=FIELDNAME value='Length'|cat:$STRING}
                                             {$LOCALFIELDINFO.mandatory = true}
                                             {$LOCALFIELDINFO.name = $FIELDNAME}
                                             {$LOCALFIELDINFO.label = 'Length'}
                                             {$LOCALFIELDINFO.type = 'integer'}
                                             <input name="Length{$ID}-{$CRATE[10]}" class="input-large nameField" type="number" style="width:85%;text-align:center;margin:auto" value="{$CRATE[6]}" data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" data-fieldinfo="{$INFO}"/>
                                           </td>
                                           <td class='fieldValue' style='width:{$cratingWidth}%;text-align:center;margin:auto'>
                                             {assign var=FIELDNAME value='Width'|cat:$STRING}
                                             {$LOCALFIELDINFO.mandatory = true}
                                             {$LOCALFIELDINFO.name = $FIELDNAME}
                                             {$LOCALFIELDINFO.label = 'Width'}
                                             {$LOCALFIELDINFO.type = 'integer'}
                                             <input name="Width{$ID}-{$CRATE[10]}" class="input-large nameField" type="number" style="width:85%;text-align:center;margin:auto" value="{$CRATE[7]}" data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" data-fieldinfo="{$INFO}"/>
                                           </td>
                                           <td class='fieldValue' style='width:{$cratingWidth}%;text-align:center;margin:auto'>
                                             {assign var=FIELDNAME value='Height'|cat:$STRING}
                                             {$LOCALFIELDINFO.mandatory = true}
                                             {$LOCALFIELDINFO.name = $FIELDNAME}
                                             {$LOCALFIELDINFO.label = 'Height'}
                                             {$LOCALFIELDINFO.type = 'integer'}
                                             <input name="Height{$ID}-{$CRATE[10]}" class="input-large nameField" type="number" style="width:85%;text-align:center;margin:auto" value="{$CRATE[8]}" data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" data-fieldinfo="{$INFO}"/>
                                           </td>
                                           <td class='fieldValue' style='width:{$cratingWidth}%;text-align:center;margin:auto'>
                                             {assign var=FIELDNAME value='InchesAdded'|cat:$STRING}
                                             {$LOCALFIELDINFO.mandatory = true}
                                             {$LOCALFIELDINFO.name = $FIELDNAME}
                                             {$LOCALFIELDINFO.label = 'Inches Added'}
                                             {$LOCALFIELDINFO.type = 'integer'}
                                             <input name="InchesAdded{$ID}-{$CRATE[10]}" class="input-large nameField" type="number" style="width:85%;text-align:center;margin:auto" value="{$CRATE[9]}" data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" data-fieldinfo="{$INFO}"/>
                                           </td>
                                           <td class='fieldValue' style='width:{$cratingWidth}%;text-align:center;margin:auto'>
                                             {assign var=FIELDNAME value='CratingQty'|cat:$STRING}
                                             {$LOCALFIELDINFO.mandatory = true}
                                             {$LOCALFIELDINFO.name = $FIELDNAME}
                                             {$LOCALFIELDINFO.label = 'Crating Quantity'}
                                             {$LOCALFIELDINFO.type = 'integer'}
                                             <input data-validator='[{ldelim}"name":"WholeNumber"{rdelim}]' data-validation-engine="validate[funcCall[Vtiger_Base_Validator_Js.invokeValidation]]"  min="0" name="CratingQty{$ID}-{$CRATE[10]}" class="input-large nameField" type="number" style="width:85%;text-align:center;margin:auto" value="{$CRATE[2]}" data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" data-fieldinfo="{$INFO}"/>
                                           </td>
                                           <td class='fieldValue' style='width:{$cratingWidth}%;margin:auto'>
                                           <div class="row-fluid">

                                             <div class="input-prepend">
                                              <span class="span10">
                                                  <span class="add-on">&#36;</span>
                                                  {assign var=FIELDNAME value='CratingRate'|cat:$STRING}
                                                  {$LOCALFIELDINFO.mandatory = true}
                                                  {$LOCALFIELDINFO.name = $FIELDNAME}
                                                  {$LOCALFIELDINFO.label = 'Crating Rate'}
                                                  {$LOCALFIELDINFO.type = 'currency'}
                                                  <input name="CratingRate{$ID}-{$CRATE[10]}" step="0.01" style="width:62%;margin:auto" class="input-medium currencyField" type="text" value="{{$CRATE[3]}}" data-decimal-seperator="." data-group-seperator="," data-number-of-decimal-places="2" data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" data-fieldinfo="{$INFO}"/>
                                              </span>
                                             </div>
                                           </div>
                                           </td>
                                           <td class='fieldValue' style='width:{$cratingWidth}%;text-align:center;margin:auto'>
                                             {assign var=FIELDNAME value='UncratingQty'|cat:$STRING}
                                             {$LOCALFIELDINFO.mandatory = true}
                                             {$LOCALFIELDINFO.name = $FIELDNAME}
                                             {$LOCALFIELDINFO.label = 'Uncrating Quantity'}
                                             {$LOCALFIELDINFO.type = 'integer'}
                                             <input data-validator='[{ldelim}"name":"WholeNumber"{rdelim}]' data-validation-engine="validate[funcCall[Vtiger_Base_Validator_Js.invokeValidation]]"  min="0" name="UncratingQty{$ID}-{$CRATE[10]}" class="input-large nameField" type="number" style="width:85%;text-align:center;margin:auto" value="{$CRATE[4]}" data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" data-fieldinfo="{$INFO}"/>
                                           </td>
                                           <td class='fieldValue' style='width:{$cratingWidth}%;margin:auto'>
                                             <div class="row-fluid">

                                              <div class="input-prepend">
                                                  <span class="span10">
                                                     <span class="add-on">&#36;</span>
                                                     {assign var=FIELDNAME value='UncratingRate'|cat:$STRING}
                                                     {$LOCALFIELDINFO.mandatory = true}
                                                     {$LOCALFIELDINFO.name = $FIELDNAME}
                                                     {$LOCALFIELDINFO.label = 'Uncrating Rate'}
                                                     {$LOCALFIELDINFO.type = 'currency'}
                                                     <input name="UncratingRate{$ID}-{$CRATE[10]}" step="0.01" style="width:62%;margin:auto" class="input-medium currencyField" type="text" value="{$CRATE[5]}" data-decimal-seperator="." data-group-seperator="," data-number-of-decimal-places="2" data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" data-fieldinfo="{$INFO}"/>
                                                  </span>
                                              </div>
                                             </div>
                                           </td>
                                        </tr>
                                    {/foreach}
                                {/if}
                               </table>
                             </td>
                        {elseif $RATE_TYPE eq "CWT by Weight" || $RATE_TYPE eq "SIT Cartage" }
                           <tr>
                            <td class='fieldLabel'>
                                <label class="muted pull-right marginRight10px">{if $SERVICE->get(is_required)}<span class="redColor">*</span>{/if}Weight</label>
                            </td>
                            <td class='fieldValue'>
                            {assign var=FIELDNAME value='Weight'|cat:$ID}
                            {if $SERVICE->get(is_required)}{$LOCALFIELDINFO.mandatory = true}{else}{$LOCALFIELDINFO.mandatory = false}{/if}
                            {$LOCALFIELDINFO.name = $FIELDNAME}
                            {$LOCALFIELDINFO.label = 'Weight'}
                            {$LOCALFIELDINFO.type = 'integer'}
                            {$INFO = Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($LOCALFIELDINFO))}
                                <input id="{$MODULE_NAME}_editView_fieldName_Weight{$ID}" name="Weight{$ID}" class="input-large nameField localCWTbyWeight" type="number" value="{$SERVICE_DETAILS.weight}" data-validation-engine="validate[{if $SERVICE->get(is_required)}required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" data-fieldinfo="{$INFO}" />
                                <input data-validator='[{ldelim}"name":"WholeNumber"{rdelim}]' data-validation-engine="validate[funcCall[Vtiger_Base_Validator_Js.invokeValidation]]"  min="0" type="hidden" class="fieldname" value="Weight{$ID}" data-prev-value="{$SERVICE_DETAILS.weight}">
                            </td>
                            <td class='fieldLabel'>
                                <label class="muted pull-right marginRight10px">{if $SERVICE->get(is_required)}<span class="redColor">*</span>{/if}Rate</label>
                            </td>
                            <td class='fieldValue'>
                            {assign var=FIELDNAME value='Rate'|cat:$ID}
                            {if $SERVICE->get(is_required)}{$LOCALFIELDINFO.mandatory = true}{else}{$LOCALFIELDINFO.mandatory = false}{/if}
                            {$LOCALFIELDINFO.name = $FIELDNAME}
                            {$LOCALFIELDINFO.label = 'Rate'}
                            {$LOCALFIELDINFO.type = 'currency'}
                            {$INFO = Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($LOCALFIELDINFO))}
                                <div class="row-fluid">
                                   <div class="input-prepend">
                                     <span class="span10">
                                      <span class="add-on">&#36;</span>
                                      {if $RATE_TYPE eq "CWT by Weight"}{assign var=SERVICE_RATE value=$SERVICE_DETAILS.rate}{else}{assign var=SERVICE_RATE value=$SERVICE->get('cartage_cwt_rate')}{/if}
                                      <input id="{$MODULE_NAME}_editView_fieldName_Rate{$ID}" name="Rate{$ID}" step="0.01" class="input-medium currencyField" type="text" value="{number_format((float)$SERVICE_RATE, 2, '.', '')}" data-decimal-seperator="." data-group-seperator="," data-number-of-decimal-places="2" data-validation-engine="validate[{if $SERVICE->get(is_required)}required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" data-fieldinfo="{$INFO}" />
                                     </span>
                                   </div>
                                </div>
                            </td>
                           </tr>
                        {elseif $RATE_TYPE eq "SIT Item"}
                           <tr>
                            <td class='fieldLabel'>
                                <label class="muted pull-right marginRight10px">{if $SERVICE->get(is_required)}<span class="redColor">*</span>{/if}Cartage Rate</label>
                            </td>
                            <td class='fieldValue'>
                                {assign var=FIELDNAME value='Cartage'|cat:$ID}
                                {if $SERVICE->get(is_required)}{$LOCALFIELDINFO.mandatory = true}{else}{$LOCALFIELDINFO.mandatory = false}{/if}
                                {$LOCALFIELDINFO.name = $FIELDNAME}
                                {$LOCALFIELDINFO.label = 'Cartage'}
                                {$LOCALFIELDINFO.type = 'currency'}
                                {$INFO = Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($LOCALFIELDINFO))}
                                <div class="row-fluid">
                                   <div class="input-prepend">
                                     <span class="span10">
                                      <span class="add-on">&#36;</span>
                                      {* <input name="Cartage{$ID}" class="input-medium currencyField" type="text" value="{number_format((float)$SERVICE_DETAILS.cartage_cwt_rate, 2, '.', '')}"
                                             data-decimal-seperator="." data-group-seperator="," data-number-of-decimal-places="2"
                                             data-validation-engine="validate[{if $SERVICE->get(is_required)}required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]"
                                             data-fieldinfo="{$INFO}" /> *}
                                        <input name="Cartage{$ID}" step="0.01" class="input-medium currencyField" type="text" value="{number_format((float)$SERVICE->get('cartage_cwt_rate'), 2, '.', '')}"
                                            data-decimal-seperator="." data-group-seperator="," data-number-of-decimal-places="2"
                                            data-validation-engine="validate[{if $SERVICE->get(is_required)}required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]"
                                            data-fieldinfo="{$INFO}" />
                                     </span>
                                   </div>
                                </div>
                            </td>
                            <td class='fieldLabel'>
                                <label class="muted pull-right marginRight10px">{if $SERVICE->get(is_required)}<span class="redColor">*</span>{/if}First Day Rate</label>
                            </td>
                            <td class='fieldValue'>
                                {assign var=FIELDNAME value='FirstDay'|cat:$ID}
                                {if $SERVICE->get(is_required)}{$LOCALFIELDINFO.mandatory = true}{else}{$LOCALFIELDINFO.mandatory = false}{/if}
                                {$LOCALFIELDINFO.name = $FIELDNAME}
                                {$LOCALFIELDINFO.label = 'FirstDay'}
                                {$LOCALFIELDINFO.type = 'currency'}
                                {$INFO = Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($LOCALFIELDINFO))}
                                <div class="row-fluid">
                                   <div class="input-prepend">
                                     <span class="span10">
                                      <span class="add-on">&#36;</span>
                                      <input name="FirstDay{$ID}" step="0.01" class="input-medium currencyField" type="text" value="{number_format((float)$SERVICE->get('first_day_rate'), 2, '.', '')}"
                                             data-decimal-seperator="." data-group-seperator="," data-number-of-decimal-places="2"
                                             data-validation-engine="validate[{if $SERVICE->get(is_required)}required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]"
                                             data-fieldinfo="{$INFO}" />
                                     </span>
                                   </div>
                                </div>
                            </td>
                           </tr>
                           <tr>
                                <td class='fieldLabel'>
                                   <label class="muted pull-right marginRight10px">{if $SERVICE->get(is_required)}<span class="redColor">*</span>{/if}Additional Day Rate</label>
                                </td>
                                <td class='fieldValue'>
                                   {assign var=FIELDNAME value='AdditionalDay'|cat:$ID}
                                   {if $SERVICE->get(is_required)}{$LOCALFIELDINFO.mandatory = true}{else}{$LOCALFIELDINFO.mandatory = false}{/if}
                                   {$LOCALFIELDINFO.name = $FIELDNAME}
                                   {$LOCALFIELDINFO.label = 'AdditionalDay'}
                                   {$LOCALFIELDINFO.type = 'currency'}
                                   {$INFO = Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($LOCALFIELDINFO))}
                                   <div class="row-fluid">
                                     <div class="input-prepend">
                                      <span class="span10">
                                          <span class="add-on">&#36;</span>
                                          <input name="AdditionalDay{$ID}" step="0.01" class="input-medium currencyField" type="text" value="{number_format((float)$SERVICE->get('additional_day_rate'), 2, '.', '')}"
                                                data-decimal-seperator="." data-group-seperator="," data-number-of-decimal-places="2"
                                                data-validation-engine="validate[{if $SERVICE->get(is_required)}required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]"
                                                data-fieldinfo="{$INFO}" />
                                      </span>
                                     </div>
                                   </div>
                                </td>
                                <td class='fieldLabel'></td>
                                <td class='fieldValue'></td>
                            </tr>
                        {elseif $RATE_TYPE eq "CWT Per Quantity"}
                           <tr>
                            <td class='fieldLabel'>
                                <label class="muted pull-right marginRight10px">{if $SERVICE->get(is_required)}<span class="redColor">*</span>{/if}Quantity</label>
                            </td>
                            <td class='fieldValue'>
                            {assign var=FIELDNAME value='Quantity'|cat:$ID}
                            {if $SERVICE->get(is_required)}{$LOCALFIELDINFO.mandatory = true}{else}{$LOCALFIELDINFO.mandatory = false}{/if}
                            {$LOCALFIELDINFO.name = $FIELDNAME}
                            {$LOCALFIELDINFO.label = 'Quantity'}
                            {$LOCALFIELDINFO.type = 'integer'}
                            {$INFO = Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($LOCALFIELDINFO))}
                                <input id="{$MODULE_NAME}_editView_fieldName_Quantity{$ID}" name="Quantity{$ID}" class="input-large nameField localCWTPerQuantity" type="number" value="{$SERVICE_DETAILS.quantity}" data-validation-engine="validate[{if $SERVICE->get(is_required)}required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" data-fieldinfo="{$INFO}" />
                                <input type="hidden" class="fieldname" value="Quantity{$ID}" data-prev-value="{$SERVICE_DETAILS.quantity}">
                            </td>
                            <td class='fieldLabel'>
                                <label class="muted pull-right marginRight10px">{if $SERVICE->get(is_required)}<span class="redColor">*</span>{/if}Rate</label>
                            </td>
                            <td class='fieldValue'>
                            {assign var=FIELDNAME value='Rate'|cat:$ID}
                            {if $SERVICE->get(is_required)}{$LOCALFIELDINFO.mandatory = true}{else}{$LOCALFIELDINFO.mandatory = false}{/if}
                            {$LOCALFIELDINFO.name = $FIELDNAME}
                            {$LOCALFIELDINFO.label = 'Rate'}
                            {$LOCALFIELDINFO.type = 'currency'}
                            {$INFO = Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($LOCALFIELDINFO))}
                                <div class="row-fluid">
                                   <div class="input-prepend">
                                     <span class="span10">
                                      <span class="add-on">&#36;</span>
                                      {if $RATE_TYPE eq "CWT Per Quantity"}{assign var=SERVICE_RATE value=$SERVICE_DETAILS.rate}{else}{assign var=SERVICE_RATE value=$SERVICE->get('cwtperqty_rate')}{/if}
                                      <input id="{$MODULE_NAME}_editView_fieldName_Rate{$ID}" name="Rate{$ID}" step="0.01" class="input-medium currencyField" type="text" value="{number_format((float)$SERVICE_RATE, 2, '.', '')}" data-decimal-seperator="." data-group-seperator="," data-number-of-decimal-places="2" data-validation-engine="validate[{if $SERVICE->get(is_required)}required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" data-fieldinfo="{$INFO}" />
                                     </span>
                                   </div>
                                </div>
                            </td>
                           </tr>
                           <tr>
                               <td class='fieldLabel'>
                                   <label class="muted pull-right marginRight10px">{if $SERVICE->get(is_required)}<span class="redColor">*</span>{/if}Weight</label>
                               </td>
                               <td class='fieldValue'>
                                    {assign var=FIELDNAME value='Weight'|cat:$ID}
                                    {if $SERVICE->get(is_required)}{$LOCALFIELDINFO.mandatory = true}{else}{$LOCALFIELDINFO.mandatory = false}{/if}
                                    {$LOCALFIELDINFO.name = $FIELDNAME}
                                    {$LOCALFIELDINFO.label = 'Weight'}
                                    {$LOCALFIELDINFO.type = 'integer'}
                                    {$INFO = Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($LOCALFIELDINFO))}
                                    <input data-validator='[{ldelim}"name":"WholeNumber"{rdelim}]' data-validation-engine="validate[funcCall[Vtiger_Base_Validator_Js.invokeValidation]]"  min="0" id="{$MODULE_NAME}_editView_fieldName_Weight{$ID}" name="Weight{$ID}" class="input-large nameField localCWTPerWeight" type="number" value="{$SERVICE_DETAILS.weight}" data-validation-engine="validate[{if $SERVICE->get(is_required)}required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" data-fieldinfo="{$INFO}" />
                                    <input type="hidden" class="fieldname" value="Weight{$ID}" data-prev-value="{$SERVICE_DETAILS.weight}">
                                </td>
                            </tr>
                        {elseif $RATE_TYPE eq "Flat Rate By Weight"}
                          {include file=vtemplate_path('localSectionsEdit/FlatRateByWeight.tpl','Estimates') SERVICE=$SERVICE DETAILS=$SERVICE_DETAILS.frbw}
                        {else}
                            <td class='fieldLabel' colspan="4">&nbsp;</td>
                         {/if}
                    {/foreach}
                </tbody>
            </table>
            <!-- END SECTION TABLE DIV -->
            <br>
        {/foreach}
    </div>
    <!-- END TARIFF DIV -->
    {/if}
    </div>
{/strip}
