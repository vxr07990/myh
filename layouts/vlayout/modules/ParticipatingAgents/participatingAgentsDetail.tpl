{strip}
{if $PARTICIPATING_AGENTS}
	{if $PARTICIPANT_LIST|@count gt 0}
		<table class="table table-bordered detailview-table {* OLD SECURITIES if $CREATOR_PERMISSIONS neq true }hide{ /if *}">
			<thead>
				<tr>
					<th class="blockHeader" colspan="7">
							{if $BLOCK_LABEL_KEY}
								<img class="cursorPointer alignMiddle blockToggle {if !($IS_HIDDEN)} hide {/if} "  src="{vimage_path('arrowRight.png')}" data-mode="hide" data-id={$BLOCK_LIST[$BLOCK_LABEL_KEY]->get('id')}>
								<img class="cursorPointer alignMiddle blockToggle {if ($IS_HIDDEN)} hide {/if}"  src="{vimage_path('arrowDown.png')}" data-mode="show" data-id={$BLOCK_LIST[$BLOCK_LABEL_KEY]->get('id')}>
							{/if}
							&nbsp;&nbsp;{vtranslate('LBL_PARTICIPATING_AGENTS', 'ParticipatingAgents')}
					</th>
				</tr>
			</thead>
			{*assign var=USE_STATUS value=true*}{*Change this to true to bring back the status column when messaging has been made to work*}
			<tbody {if $IS_HIDDEN} class="hide" {/if}>
				<tr colspan="7">
					<td colspan="2" style="text-align:center;margin:auto;background-color:#E8E8E8;width:50%">&nbsp;</td>
					<td colspan="4" style="text-align:center;margin:auto;background-color:#E8E8E8;width:50%"><b>Permission Level</b></td>
					{if $USE_STATUS}<td colspan="1" style="text-align:center;margin:auto;background-color:#E8E8E8;">&nbsp;</td>{/if}
				</tr>
				<tr style="width:100%" colspan="7" class="fieldLabel">
					<td style="text-align:center;margin:auto;width:20%;">Type</td>
					<td style="text-align:center;margin:auto;width:20%;">Agent</td>
					<td style="text-align:center;margin:auto;width:11%;">Full</td>
					<td style="text-align:center;margin:auto;width:11%;">No-rates</td>
					<td style="text-align:center;margin:auto;width:11%;">Read-only</td>
					<td style="text-align:center;margin:auto;width:11%;">No-Access</td>
					{if $USE_STATUS}<td style="text-align:center;margin:auto;width:10%;">Status</td>{/if}
				</tr>
				{foreach key=ROW_NUM item=PARTICIPANT from=$PARTICIPANT_LIST}
					<tr style="text-align:center;margin:auto" class="participantRow{$ROW_NUM+1}">
						<td style="text-align:center;margin:auto">
							<span class="value">
								{*assign var=PICKLIST_VALUES value=['Booking Agent', 'Destination Agent', 'Destination Storage Agent', 'Hauling Agent', 'Invoicing Agent', 'Origin Agent', 'Origin Storage Agent', 'Estimating Agent']*}
								{assign var=PICKLIST_VALUES value=ParticipatingAgents_Module_Model::getParticipantPicklistValues()}
								{$PICKLIST_VALUES[$PARTICIPANT['agent_type']]}
							</span>
						</td>
						<td style="text-align:center;margin:auto">
							<span class="value">
								{$PARTICIPANT['agentName']} ({$PARTICIPANT['agent_number']})
							</span>
						</td>
						<td style="text-align:center;margin:auto">
								{if $PARTICIPANT['view_level'] eq 'full'}Yes{else}No{/if}
						</td>
						<td style="text-align:center;margin:auto">
							<span class="value">
								{if $PARTICIPANT['view_level'] eq 'no_rates'}Yes{else}No{/if}
							</span>
						</td>
						<td radioPermission" style="text-align:center;margin:auto">
								{if $PARTICIPANT['view_level'] eq 'read_only'}Yes{else}No{/if}
						</td>
						<td style="text-align:center;margin:auto">
							<span class="value">
								{if $PARTICIPANT['view_level'] eq 'no_access'}Yes{else}No{/if}
							</span>
						</td>
						{if $USE_STATUS}
							<td style="text-align:center;margin:auto">
								<span class='status-label'>{$PARTICIPANT['status']}</span>
							</td>
						{/if}
					</tr>
				{/foreach}
			</tbody>
		</table>
	{/if}
{/if}
<br>
{/strip}