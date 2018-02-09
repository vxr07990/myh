{strip}
{if $PARTICIPATING_AGENTS && !$IS_PARTICIPANT}
	<input type="hidden" name="primary_est_tariff_type" value="{$PRIMARY_EST_TARIFF_TYPE}">
	<input type="hidden" name="primary_owner_agent" value="{$PRIMARY_OWNER_AGENT}">
	<input type="hidden" name="primary_owner_agent_name" value="{$PRIMARY_OWNER_AGENT_NAME}">
    {if $PARTICIPATING_CARRIER_DEFAULTS}
        {foreach key=CARRIERTYPE item=CARRIER from=$PARTICIPATING_CARRIER_DEFAULTS}
            <input type = "hidden" name="{$CARRIERTYPE}AgentName" value="{$CARRIER['agentName']}">
            <input type = "hidden" name="{$CARRIERTYPE}Agents_id" value="{$CARRIER['agents_id']}">
        {/foreach}
    {/if}
	<table name='participatingAgentsTable' class='table table-bordered blockContainer showInlineTable{if is_array($HIDDEN_BLOCKS)}{*if in_array($BLOCK_LABEL, $HIDDEN_BLOCKS)} hide{/if*}{/if}'>
		<thead>
			<tr>
				<th class='blockHeader' colspan='8'>{vtranslate('LBL_PARTICIPATING_AGENTS', 'ParticipatingAgents')}</th>
			</tr>
		</thead>
		{*assign var=USE_STATUS value=true*}{*Change this to true to bring back the status column when messaging has been made to work*}
		<tbody>
			<tr colspan="8" class="fieldLabel">
				<td colspan="8">
					<button type="button" class="addParticipant">+</button>
					{* Amin made this button it makes no sense leaving it here until I get word from higher up to get rid of it
					<button type="button" class="hideRemovedParticipant">Toggle Removed Participants</button>
					*}
					<button type="button" class="addParticipant" style="clear:right;float:right">+</button>
				</td>
			</tr>
			<tr colspan="8">
				<td colspan="3" style="text-align:center;margin:auto;background-color:#E8E8E8;width:50%">&nbsp;</td>
				<td colspan="4" style="text-align:center;margin:auto;background-color:#E8E8E8;width:50%"><b>Permission Level</b></td>
				{if $USE_STATUS}<td colspan="1" style="text-align:center;margin:auto;background-color:#E8E8E8;"><b>Status</b></td>{/if}
			</tr>
			<tr style="width:100%" colspan="8" class="fieldLabel">
				<td style="text-align:center;margin:auto;width:6%;"><input type="hidden" name="numAgents" value="{($PARTICIPANT_LIST|@count)}"/></td>
				<td style="text-align:center;margin:auto;width:20%;">Type</td>
				<td style="text-align:center;margin:auto;width:20%;">Agent</td>
				<td style="text-align:center;margin:auto;width:11%;">Full</td>
				<td style="text-align:center;margin:auto;width:11%;">No-rates</td>
				<td style="text-align:center;margin:auto;width:11%;">Read-only</td>
				<td style="text-align:center;margin:auto;width:11%;">No-Access</td>
				{if $USE_STATUS}<td style="text-align:center;margin:auto;width:10%;"></td>{/if}
			</tr>
			<tr style="text-align:center;margin:auto"class="defaultParticipant participantRow hide">
					<td class="fieldValue" style="text-align:center;margin:auto">
						<i title="Delete" class="icon-trash removeParticipant"></i>
						<input type="hidden" class="default" name="participantId" value="none" />
					</td>
					<td class="fieldValue typeCell" style="text-align:center;margin:auto"><span class="redColor">*&nbsp&nbsp</span>
						{$LOCALFIELDINFO.mandatory = true}
						{$LOCALFIELDINFO.type = 'picklist'}
						{$INFO = Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($LOCALFIELDINFO))}
						{$LOCALFIELDINFO.name ='agent_type'}
						{*assign var=PICKLIST_VALUES value=['Booking Agent', 'Destination Agent', 'Destination Storage Agent', 'Hauling Agent', 'Invoicing Agent', 'Origin Agent', 'Origin Storage Agent', 'Estimating Agent']*}
						{assign var=PICKLIST_VALUES value=ParticipatingAgents_Module_Model::getParticipantPicklistValues()}
						<select style="text-align:left" class="select default validate" name="{$LOCALFIELDINFO.name}" data-fieldinfo='{$INFO}' data-selected-value=''>
							<option value="" style="text-align:left">{vtranslate('LBL_SELECT_OPTION','Vtiger')}</option>
							{foreach item=PICKLIST_VALUE key=PICKLIST_NAME from=$PICKLIST_VALUES}
								<option style="text-align:left" value="{Vtiger_Util_Helper::toSafeHTML($PICKLIST_NAME)}">{$PICKLIST_VALUE}</option>
							{/foreach}
						</select>
					</td>
					<td class="fieldValue" style="text-align:center;white-space:nowrap;margin:auto">
						{$LOCALFIELDINFO.mandatory = true}
						{$LOCALFIELDINFO.type = 'reference'}
						{$INFO = Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($LOCALFIELDINFO))}
						{$LOCALFIELDINFO.name ='agents_id'}
						{assign var="REFERENCE_LIST" value=$FIELD_MODEL->getReferenceList()}
							<input name="popupReferenceModule" type="hidden" value="Agents"/>
							<div class="input-prepend input-append" style="text-align:center;margin:auto"><span class="redColor">*&nbsp&nbsp</span>
								<input name="{$LOCALFIELDINFO.name}" type="hidden" value class="sourceField default" data-displayvalue="{$PARTICIPANT['agentName']}" data-fieldinfo='{$INFO}' />
								<span class="add-on clearReferenceSelection cursorPointer alignMiddle" style="float:none;clear:none;display:inline-block">
									<i id="{$MODULE}_editView_fieldName_{$LOCALFIELDINFO.name}_clear" class='icon-remove-sign' title="{vtranslate('LBL_CLEAR', $MODULE)}"></i>
								</span>
								<input id="{$LOCALFIELDINFO.name}_display" name="{$LOCALFIELDINFO.name}_display" type="text" style="margin:auto;width:auto;float:none" class="autoComplete validate" value="{$PARTICIPANT['agentName']}" data-fieldinfo='{$INFO}' placeholder="{vtranslate('LBL_TYPE_SEARCH',$MODULE)}">
                                </input>
                                <span class="add-on relatedPopup cursorPointer alignMiddle" style="float:none;clear:none;display:inline-block">
									<i id="{$MODULE}_editView_fieldName_{$LOCALFIELDINFO.name}_select" class="icon-search relatedPopup" title="{vtranslate('LBL_SELECT', $MODULE)}"></i>
								</span>
							</div>
					</td>
					<td class="fieldValue" style="text-align:center;margin:auto">
						{$LOCALFIELDINFO.mandatory = true}
						{$LOCALFIELDINFO.type = 'radio'}
						{$INFO = Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($LOCALFIELDINFO))}
						<input type="radio" class="default change" name="agent_permission" value="full" />
					</td>
					<td class="fieldValue" style="text-align:center;margin:auto">
						{$LOCALFIELDINFO.mandatory = true}
						{$LOCALFIELDINFO.type = 'radio'}
						{$INFO = Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($LOCALFIELDINFO))}
						<input type="radio" class="default change" name="agent_permission" value="no_rates" />
					</td>
					<td class="fieldValue" style="text-align:center;margin:auto">
						{$LOCALFIELDINFO.mandatory = true}
						{$LOCALFIELDINFO.type = 'radio'}
						{$INFO = Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($LOCALFIELDINFO))}
						<input type="radio" class="default change" name="agent_permission" value="read_only" checked />
					</td>
					<td class="fieldValue" style="text-align:center;margin:auto">
						{$LOCALFIELDINFO.mandatory = true}
						{$LOCALFIELDINFO.type = 'radio'}
						{$INFO = Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($LOCALFIELDINFO))}
						<input type="radio" class="default change" name="agent_permission" value="no_access" />
					</td>
                    {if $USE_STATUS}
                        <td class="fieldValue" style="text-align:center;margin:auto;">
                           {* <input type="hidden" class="default change status" default='pending' name="agent_status" value="{$PARTICIPANT['status']}"/>*}
                            <span class='status-label'>Pending</span>
                        </td>
                    {/if}
				</tr>
				{foreach key=ROW_NUM item=PARTICIPANT from=$PARTICIPANT_LIST}
					<tr style="text-align:center;margin:auto" class="participantRow{$ROW_NUM+1} participantRow">
						<td class="fieldValue" style="text-align:center;margin:auto">
							<input type="hidden" name="participantId_{$ROW_NUM+1}" value="{$PARTICIPANT['participatingagentsid']}" />
							<input type="hidden" class="default" name="participantDelete_{$ROW_NUM+1}" value="" />
							<i title="Delete" class="icon-trash removeParticipant"></i>
						</td>
						<td class="fieldValue typeCell" style="text-align:center;margin:auto">
							{$LOCALFIELDINFO.mandatory = true}
							{$LOCALFIELDINFO.type = 'picklist'}
							{$LOCALFIELDINFO.name = 'agent_type_'|cat:($ROW_NUM+1)}
							{$INFO = Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($LOCALFIELDINFO))}
							{*assign var=PICKLIST_VALUES value=['Booking Agent', 'Destination Agent', 'Destination Storage Agent', 'Hauling Agent', 'Invoicing Agent', 'Origin Agent', 'Origin Storage Agent', 'Estimating Agent']*}
							{assign var=PICKLIST_VALUES value=ParticipatingAgents_Module_Model::getParticipantPicklistValues()}
							<select class="chzn-select{if $OCCUPY_COMPLETE_WIDTH} row-fluid {/if}" style="text-align:left" id="{$LOCALFIELDINFO.name}" name="{$LOCALFIELDINFO.name}" data-fieldinfo='{$INFO}' data-validation-engine='validate[{if $LOCALFIELDINFO.mandatory}required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]' {if !empty($SPECIAL_VALIDATOR)}data-validator='{Zend_Json::encode($SPECIAL_VALIDATOR)}'{/if} data-selected-value='{$PARTICIPANT['agent_type']}'>
								{if $FIELD_MODEL->isEmptyPicklistOptionAllowed()}<option value="" style="text-align:left">{vtranslate('LBL_SELECT_OPTION','Vtiger')}</option>{/if}
								{foreach item=PICKLIST_VALUE key=PICKLIST_NAME from=$PICKLIST_VALUES}
									<option style="text-align:left" value="{Vtiger_Util_Helper::toSafeHTML($PICKLIST_NAME)}" {if trim($PARTICIPANT['agent_type']) eq trim($PICKLIST_NAME)} selected {/if}>{$PICKLIST_VALUE}</option>
                            {/foreach}
                            </select>
						</td>
						<td class="fieldValue" style="text-align:center;margin:auto">
							{$LOCALFIELDINFO.mandatory = true}
							{$LOCALFIELDINFO.type = 'reference'}
							{$LOCALFIELDINFO.name = 'agents_id_'|cat:($ROW_NUM+1)}
							{$INFO = Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($LOCALFIELDINFO))}
							{assign var="REFERENCE_LIST" value=$FIELD_MODEL->getReferenceList()}
							<input name="popupReferenceModule" type="hidden" value="Agents"/>
							<div class="input-prepend input-append" style="text-align:center;margin:auto">
								<input name="{$LOCALFIELDINFO.name}" type="hidden" value="{$PARTICIPANT['agents_id']}" class="sourceField" data-displayvalue="{$PARTICIPANT['agentName']}" data-fieldinfo='{$INFO}' />
								<span class="add-on clearReferenceSelection cursorPointer alignMiddle" style="float:none;clear:none;display:inline-block">
									<i id="{$MODULE}_editView_fieldName_{$LOCALFIELDINFO.name}_clear" class='icon-remove-sign' title="{vtranslate('LBL_CLEAR', $MODULE)}"></i>
								</span>
								<input id="{$LOCALFIELDINFO.name}_display" name="{$LOCALFIELDINFO.name}_display" type="text" style="margin:auto;width:auto;float:none" class="autoComplete" value="{$PARTICIPANT['agentName']}" data-fieldinfo='{$INFO}' placeholder="{vtranslate('LBL_TYPE_SEARCH',$MODULE)}"{if $PARTICIPANT['agentName'] != ''} readonly{/if} data-validation-engine='validate[{if $LOCALFIELDINFO.mandatory}required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]'>
                                </input>
                                <span class="add-on relatedPopup cursorPointer alignMiddle" style="float:none;clear:none;display:inline-block">
									<i id="{$MODULE}_editView_fieldName_{$LOCALFIELDINFO.name}_select" class="icon-search relatedPopup" title="{vtranslate('LBL_SELECT', $MODULE)}"></i>
								</span>
							</div>
						</td>
						<td class="fieldValue" style="text-align:center;margin:auto">
							{$LOCALFIELDINFO.mandatory = true}
							{$LOCALFIELDINFO.type = 'radio'}
							{$INFO = Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($LOCALFIELDINFO))}
							<input type="radio" name="agent_permission_{$ROW_NUM+1}" value="full" {if $PARTICIPANT['view_level'] eq "full"}checked{/if} />
						</td>
						<td class="fieldValue" style="text-align:center;margin:auto">
							{$LOCALFIELDINFO.mandatory = true}
							{$LOCALFIELDINFO.type = 'radio'}
							{$INFO = Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($LOCALFIELDINFO))}
							<input type="radio" name="agent_permission_{$ROW_NUM+1}" value="no_rates" {if $PARTICIPANT['view_level'] eq "no_rates"}checked{/if} />
						</td>
						<td class="fieldValue" style="text-align:center;margin:auto">
							{$LOCALFIELDINFO.mandatory = true}
							{$LOCALFIELDINFO.type = 'radio'}
							{$INFO = Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($LOCALFIELDINFO))}
							<input type="radio" name="agent_permission_{$ROW_NUM+1}" value="read_only" {if $PARTICIPANT['view_level'] eq "read_only"}checked{/if} />
						</td>
						<td class="fieldValue" style="text-align:center;margin:auto">
							{$LOCALFIELDINFO.mandatory = true}
							{$LOCALFIELDINFO.type = 'radio'}
							{$INFO = Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($LOCALFIELDINFO))}
							<input type="radio" name="agent_permission_{$ROW_NUM+1}" value="no_access" {if $PARTICIPANT['view_level'] eq "no_access"}checked{/if} />
						</td>
                        {if $USE_STATUS}
							<td class="fieldValue" style="text-align:center;margin:auto;">
                                {*<input type="hidden" name="agent_status_{$ROW_NUM+1}" default="{if $PARTICIPANT['status'] eq 2}0{else}{$PARTICIPANT['status']}{/if}" value="{$PARTICIPANT['status']}" class="status"/>*}
                                <span class='status-label'>{$PARTICIPANT['status']}</span>
                            </td>
                        {/if}
					</tr>
				{/foreach}
		</tbody>
	</table>
	<br>
{/if}
{/strip}
