{strip}
{if $SERVICE_PROVIDER_RESPO}
    
        {assign var=AGENTS_PICKLIST_VALUES value=Claims_Module_Model::getOrderParticipantPicklistValues($CLAIM_SUMMARY_ID)}
    
	<table name='serviceProviderResponsibilityTable' class='table table-bordered blockContainer showInlineTable{if is_array($HIDDEN_BLOCKS)}{*if in_array($BLOCK_LABEL, $HIDDEN_BLOCKS)} hide{/if*}{/if}' style="margin-top:1%;">
		<thead>
			<tr>
				<th class='blockHeader' colspan='7'>{vtranslate('LBL_SERVICE_PROVIDER_RESPONSIBILITY', 'Claims')}</th>
			</tr>
		</thead>
		<tbody>
			<tr class="fieldLabel">
				<td colspan="7">
					<button type="button" class="addParticipant">+</button>
					<button type="button" class="addParticipant" style="clear:right;float:right">+</button>
				</td>
			</tr>
			<tr style="width:100%" colspan="6" class="fieldLabel">
				<td style="text-align:center;margin:auto;width:5%;"><input type="hidden" name="numSPR" value="{($SERVICE_PROVIDER_LIST|@count)}"/></td>
                                <td style="text-align:center;margin:auto;width:15%;">Agent Type</td>
				<td style="text-align:center;margin:auto;width:25%;">Participating Agent</td>
				<td style="text-align:center;margin:auto;width:25%;">Service Provider</td>
				<td style="text-align:center;margin:auto;width:10%;">I Code</td>
				<td style="text-align:center;margin:auto;width:10%;">% of Responsibility</td>
				<td style="text-align:center;margin:auto;width:10%;">Responsibility Amount</td>
			</tr>
			<tr style="text-align:center;margin:auto"class="defaultParticipant participantRow hide">
					<td class="fieldValue" style="text-align:center;margin:auto">
						<i title="Delete" class="icon-trash removeParticipant"></i>
						<input type="hidden" class="default" name="participantId" value="none" />
					</td>
                                        <td class="fieldValue typeCell" style="text-align:center;margin:auto">
						{$LOCALFIELDINFO.mandatory = true}
						{$LOCALFIELDINFO.type = 'picklist'}
						{$INFO = Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($LOCALFIELDINFO))}
						
						<select style="text-align:left" class="agents_type select default validate" name="agent_type" data-fieldinfo='{$INFO}' data-selected-value=''>
							<option value="" style="text-align:left">{vtranslate('LBL_SELECT_OPTION','Vtiger')}</option>
							{foreach item=PICKLIST_VALUE key=PICKLIST_NAME from=$AGENTS_PICKLIST_VALUES}
								<option style="text-align:left" value="{Vtiger_Util_Helper::toSafeHTML($PICKLIST_NAME)}">{$PICKLIST_VALUE}</option>
							{/foreach}
						</select>
					</td>
					<td class="fieldValue" style="text-align:center;margin:auto">
						{$LOCALFIELDINFO.mandatory = false}{*true*}
						{$LOCALFIELDINFO.type = 'reference'}
                                                {$LOCALFIELDINFO.name ='agents_id'}
						{$INFO = Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($LOCALFIELDINFO))}
							<input name="popupReferenceModule" type="hidden" value="Agents"/>
							<div class="input-prepend input-append" style="text-align:center;margin:auto">
								<input name="{$LOCALFIELDINFO.name}" type="hidden" value class="sourceField default" data-displayvalue="{$PARTICIPANT['agent_name']}" data-fieldinfo='{$INFO}' />
								<span class="add-on clearReferenceSelection cursorPointer alignMiddle" style="float:none;clear:none;display:inline-block">
									<i id="{$MODULE}_editView_fieldName_{$LOCALFIELDINFO.name}_clear" class='icon-remove-sign' title="{vtranslate('LBL_CLEAR', $MODULE)}"></i>
								</span>
								<input id="{$LOCALFIELDINFO.name}_display" name="{$LOCALFIELDINFO.name}_display" type="text" style="margin:auto;width:auto;float:none" class="autoComplete validate" value="{$PARTICIPANT['agent_name']}" data-fieldinfo='{$INFO}' placeholder="{vtranslate('LBL_TYPE_SEARCH',$MODULE)}">
								 </input>
								<span class="add-on relatedPopup cursorPointer alignMiddle" style="float:none;clear:none;display:inline-block">
									<i id="{$MODULE}_editView_fieldName_{$LOCALFIELDINFO.name}_select" class="icon-search relatedPopup" title="{vtranslate('LBL_SELECT', $MODULE)}"></i>
								</span>
							</div>
					</td>
					<td class="fieldValue" style="text-align:center;margin:auto">
						{$LOCALFIELDINFO.mandatory = false}{*true*}
						{$LOCALFIELDINFO.type = 'reference'}
                                                {$LOCALFIELDINFO.name ='vendors_id'}
						{$INFO = Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($LOCALFIELDINFO))}
							<input name="popupReferenceModule" type="hidden" value="Vendors"/>
							<div class="input-prepend input-append" style="text-align:center;margin:auto">
								<input name="{$LOCALFIELDINFO.name}" type="hidden" value class="sourceField default" data-displayvalue="{$PARTICIPANT['vendor_name']}" data-fieldinfo='{$INFO}' />
								<span class="add-on clearReferenceSelection cursorPointer alignMiddle" style="float:none;clear:none;display:inline-block">
									<i id="{$MODULE}_editView_fieldName_{$LOCALFIELDINFO.name}_clear" class='icon-remove-sign' title="{vtranslate('LBL_CLEAR', $MODULE)}"></i>
								</span>
								<input id="{$LOCALFIELDINFO.name}_display" name="{$LOCALFIELDINFO.name}_display" type="text" style="margin:auto;width:auto;float:none" class="autoComplete validate" value="{$PARTICIPANT['vendor_name']}" data-fieldinfo='{$INFO}' placeholder="{vtranslate('LBL_TYPE_SEARCH',$MODULE)}">
								 </input>
								<span class="add-on relatedPopup cursorPointer alignMiddle" style="float:none;clear:none;display:inline-block">
									<i id="{$MODULE}_editView_fieldName_{$LOCALFIELDINFO.name}_select" class="icon-search relatedPopup" title="{vtranslate('LBL_SELECT', $MODULE)}"></i>
								</span>
							</div>
					</td>
					<td class="fieldValue" style="text-align:center;margin:auto">
						<span class="default change icode" name="icode"></span>
					</td>
					<td class="fieldValue" style="text-align:center;margin:auto">
						{$LOCALFIELDINFO.mandatory = true}
						{$LOCALFIELDINFO.type = 'string'}
						{$INFO = Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($LOCALFIELDINFO))}
						<input type="text" class="default change respon_percentage" name="respon_percentage" value="" style="width: 80%;"/>
					</td>
					<td class="fieldValue" style="text-align:center;margin:auto">
						{$LOCALFIELDINFO.mandatory = true}
						{$LOCALFIELDINFO.type = 'currency'}
						{$INFO = Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($LOCALFIELDINFO))}
						<input type="text" class="default change respon_amount" name="respon_amount" value="" style="width: 80%;"/>
					</td>
				</tr>
                                {assign var="tpercentage" value=0}
                                {assign var="tamount" value=0}
				{foreach key=ROW_NUM item=PARTICIPANT from=$SERVICE_PROVIDER_LIST}
					<tr style="text-align:center;margin:auto" class="participantRow{$ROW_NUM+1} participantRow">
						<td class="fieldValue" style="text-align:center;margin:auto">
							<input type="hidden" name="participantId_{$ROW_NUM+1}" value="{$PARTICIPANT['sprid']}" />
							<input type="hidden" class="default" name="participantDelete_{$ROW_NUM+1}" value="" />
							<i title="Delete" class="icon-trash removeParticipant"></i>
						</td>
                                                <td class="fieldValue typeCell" style="text-align:center;margin:auto">
                                                        {$LOCALFIELDINFO.mandatory = true}
                                                        {$LOCALFIELDINFO.type = 'picklist'}
                                                        {$INFO = Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($LOCALFIELDINFO))}
                                                        {$LOCALFIELDINFO.name = 'agent_type_'|cat:($ROW_NUM+1)}
                                                        <select id="agents_type_{$ROW_NUM+1}" style="text-align:left" class="agents_type chzn-select select default validate" name="{$LOCALFIELDINFO.name}" data-fieldinfo='{$INFO}' data-selected-value=''>
                                                                <option value="" style="text-align:left">{vtranslate('LBL_SELECT_OPTION','Vtiger')}</option>
                                                                {foreach item=PICKLIST_VALUE key=PICKLIST_NAME from=$AGENTS_PICKLIST_VALUES}
                                                                        <option {if $PARTICIPANT['agent_type'] eq $PICKLIST_VALUE} selected{/if} style="text-align:left" value="{Vtiger_Util_Helper::toSafeHTML($PICKLIST_NAME)}">{$PICKLIST_VALUE}</option>
                                                                {/foreach}
                                                        </select>
                                                </td>
						<td class="fieldValue" style="text-align:center;margin:auto">
							{$LOCALFIELDINFO.mandatory = false}{*true*}
							{$LOCALFIELDINFO.type = 'reference'}
							{$LOCALFIELDINFO.name = 'agents_id_'|cat:($ROW_NUM+1)}
							{$INFO = Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($LOCALFIELDINFO))}
							{assign var="REFERENCE_LIST" value=$FIELD_MODEL->getReferenceList()}
							<input name="popupReferenceModule" type="hidden" value="Agents"/>
							<div class="input-prepend input-append" style="text-align:center;margin:auto">
								<input name="{$LOCALFIELDINFO.name}" type="hidden" value="{$PARTICIPANT['agents_id']}" class="sourceField" data-displayvalue="{$PARTICIPANT['agent_name']}" data-fieldinfo='{$INFO}' />
								<span class="add-on clearReferenceSelection cursorPointer alignMiddle" style="float:none;clear:none;display:inline-block">
									<i id="{$MODULE}_editView_fieldName_{$LOCALFIELDINFO.name}_clear" class='icon-remove-sign' title="{vtranslate('LBL_CLEAR', $MODULE)}"></i>
								</span>
								<input id="{$LOCALFIELDINFO.name}_display" name="{$LOCALFIELDINFO.name}_display" type="text" style="margin:auto;width:auto;float:none" class="autoComplete" value="{$PARTICIPANT['agent_name']}" data-fieldinfo='{$INFO}' placeholder="{vtranslate('LBL_TYPE_SEARCH',$MODULE)}"{if $PARTICIPANT['agent_name'] != ''} readonly{/if} data-validation-engine='validate[{*if $LOCALFIELDINFO.mandatory}required,{/if*}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]'>
								 </input>
								<span class="add-on relatedPopup cursorPointer alignMiddle" style="float:none;clear:none;display:inline-block">
									<i id="{$MODULE}_editView_fieldName_{$LOCALFIELDINFO.name}_select" class="icon-search relatedPopup" title="{vtranslate('LBL_SELECT', $MODULE)}"></i>
								</span>
							</div>
						</td>
                                                <td class="fieldValue" style="text-align:center;margin:auto">
							{$LOCALFIELDINFO.mandatory = false}{*true*}
							{$LOCALFIELDINFO.type = 'reference'}
							{$LOCALFIELDINFO.name = 'vendors_id_'|cat:($ROW_NUM+1)}
							{$INFO = Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($LOCALFIELDINFO))}
							{assign var="REFERENCE_LIST" value=$FIELD_MODEL->getReferenceList()}
							<input name="popupReferenceModule" type="hidden" value="Vendors"/>
							<div class="input-prepend input-append" style="text-align:center;margin:auto">
								<input name="{$LOCALFIELDINFO.name}" type="hidden" value="{$PARTICIPANT['vendors_id']}" class="sourceField" data-displayvalue="{$PARTICIPANT['vendors_name']}" data-fieldinfo='{$INFO}' />
								<span class="add-on clearReferenceSelection cursorPointer alignMiddle" style="float:none;clear:none;display:inline-block">
									<i id="{$MODULE}_editView_fieldName_{$LOCALFIELDINFO.name}_clear" class='icon-remove-sign' title="{vtranslate('LBL_CLEAR', $MODULE)}"></i>
								</span>
								<input id="{$LOCALFIELDINFO.name}_display" name="{$LOCALFIELDINFO.name}_display" type="text" style="margin:auto;width:auto;float:none" class="autoComplete" value="{$PARTICIPANT['vendors_name']}" data-fieldinfo='{$INFO}' placeholder="{vtranslate('LBL_TYPE_SEARCH',$MODULE)}"{if $PARTICIPANT['vendors_name'] != ''} readonly{/if} data-validation-engine='validate[{*if $LOCALFIELDINFO.mandatory}required,{/if*}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]'>
								 </input>
								<span class="add-on relatedPopup cursorPointer alignMiddle" style="float:none;clear:none;display:inline-block">
									<i id="{$MODULE}_editView_fieldName_{$LOCALFIELDINFO.name}_select" class="icon-search relatedPopup" title="{vtranslate('LBL_SELECT', $MODULE)}"></i>
								</span>
							</div>
						</td>
						<td class="fieldValue " style="text-align:center;margin:auto">
							<span name="icode_{$ROW_NUM+1}">{$PARTICIPANT['icode']}</span>
						</td>
						<td class="fieldValue" style="text-align:center;margin:auto">
							{$LOCALFIELDINFO.mandatory = true}
							{$LOCALFIELDINFO.type = 'string'}
							{$INFO = Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($LOCALFIELDINFO))}
							<input type="text" class="respon_percentage" name="respon_percentage_{$ROW_NUM+1}" value="{$PARTICIPANT['respon_percentage']}"  style="width: 80%;"/>
						</td>
						<td class="fieldValue" style="text-align:center;margin:auto">
							{$LOCALFIELDINFO.mandatory = true}
							{$LOCALFIELDINFO.type = 'currency'}
							{$INFO = Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($LOCALFIELDINFO))}
							<input type="text" class="respon_amount" name="respon_amount_{$ROW_NUM+1}" value="{$PARTICIPANT['respon_amount']}" style="width: 80%;" />
						</td>
					</tr>
                                        {assign var="tpercentage" value=$tpercentage+$PARTICIPANT['respon_percentage']}
                                        {assign var="tamount" value=$tamount+$PARTICIPANT['respon_amount']}
				{/foreach}
                                <tr style="text-align:center;margin:auto" class="totalsRow">
                                        <td colspan="5" style="text-align:center;margin:auto">
                                        </td>
                                        <td style="text-align:center;margin:auto">
                                            <span class="value sprtpercentage">
                                                <b>Responsibility Total: {$tpercentage}</b>
                                            </span>
                                        </td>
                                        <td style="text-align:center;margin:auto">
                                            <input type="hidden" id="sprtamount" value="{$tamount}">
                                                <span class="value sprtamount">
                                                    <b>Amount Total: {$tamount}</b>
                                                </span>
                                        </td>
                                </tr>
		</tbody>
	</table>
{/if}
{/strip}