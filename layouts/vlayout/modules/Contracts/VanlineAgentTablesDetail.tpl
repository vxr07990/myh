{strip}
	<!-- VanlineAgentTablesDetail.tpl -->
	<table class="table table-bordered blockContainer showInlineTable equalSplit{if is_array($HIDDEN_BLOCKS)}{if in_array("LBL_CONTRACTS_VANLINES", $HIDDEN_BLOCKS)} hide{/if}{else}{if $VANLINE_OWNER neq true} hide{/if}{/if}">
		<thead>
			<tr>
				<th class="blockHeader" colspan="8">{vtranslate("LBL_TARIFFMANAGER_VANLINES", $MODULE_NAME)}</th>
			</tr>
		</thead>
		<tbody id='assignedVanlinesTable'>
			<tr style='background-color:#F5F5F5'>
				<td style='width:35%;text-align:center'>Vanline Name</td>
				<td style='width:8%;text-align:center'>View All Agents</td>
				<td style='width:7%;text-align:center'>Apply to All Agents</td>
			</tr>
			{if $ASSIGNED_RECORDS.Vanlines|@count gt 0}
				<tr>
				{assign var=COUNTER value=0}
				{foreach key=ROWNUM item=VANLINE_RECORD from=$ASSIGNED_RECORDS.Vanlines}
					{if $COUNTER eq 1}{continue}{/if}
					{if $COUNTER eq 2}
						{assign var=COUNTER value=0}
						</tr><tr>
					{/if}
					{assign var=COUNTER value=$COUNTER+1}
					<td style='width:35%' class='vanline{$VANLINE_RECORD->get('id')}'>{$VANLINE_RECORD->get('vanline_name')}</td>
					<td style='width:8%;text-align:center' class='vanline{$VANLINE_RECORD->get('id')}'><button type='button' class='viewAllAgents' id='viewVanline{$VANLINE_RECORD->get('id')}Agents'>View All</button></td>
					<td style='width:7%;text-align:center' class='vanline{$VANLINE_RECORD->get('id')}'>{if $VANLINE_RECORD->get('id')|in_array:$ASSIGNED_RECORDS.ApplyToAll}Yes{else}No{/if}</td>
				{/foreach}
				</tr>
				{foreach key=ROWNUM item=VANLINE_RECORD from=$ASSIGNED_RECORDS.Vanlines}
					<input type='hidden' name='Vanline{$VANLINE_RECORD->get('id')}State' value='assigned' />
				{/foreach}
			{/if}
		</tbody>
	</table>
	<br />
	{*<table class="table table-bordered blockContainer showInlineTable equalSplit{if is_array($HIDDEN_BLOCKS)}{if in_array("LBL_TARIFFMANAGER_AGENTS", $HIDDEN_BLOCKS)} hide{/if}{/if}">
		<thead>
			<tr>
				<th class="blockHeader" colspan="4">{vtranslate("LBL_TARIFFMANAGER_AGENTS", $MODULE_NAME)}</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td class='fieldValue'>&nbsp;</td>
				<td class='fieldLabel'>&nbsp;</td>
				<td class='fieldValue'>&nbsp;</td>
			</tr>
		</tbody>
	</table>
	<br />*}
	<div class='hide'>
		<link rel="stylesheet" href="libraries/jquery/colorbox/example1/colorbox.css" />
		<script type='text/javascript' src='libraries/jquery/colorbox/jquery.colorbox-min.js'></script>
		{foreach key=VANLINE_ID item=AGENT_RECORD_LIST from=$AGENTS}
			<div id='viewVanline{$VANLINE_ID}AgentsDiv'>
				<table class="table table-bordered blockContainer showInlineTable equalSplit">
					<thead>
						<tr>
							<th class="blockHeader" colspan="4">{$VANLINE_NAMES[$VANLINE_ID]}</th>
						</tr>
					</thead>
					<tbody>
						{if $AGENT_RECORD_LIST|@count gt 0}
							<tr>
							{assign var=COUNTER value=0}
							{foreach item=AGENT_RECORD from=$AGENT_RECORD_LIST}
								{if $COUNTER eq 2}
									</tr><tr>
									{assign var=COUNTER value=0}
								{/if}
								{assign var=COUNTER value=$COUNTER+1}
								<td style='width:5%;text-align:center'>{if $AGENT_RECORD->get('id')|in_array:$ASSIGNED_RECORDS.Agents}Yes{else}No{/if}</td>
								<td style='width:45%'>{$AGENT_RECORD->get('agency_name')}</td>
							{/foreach}
							{if $COUNTER lt 2}
								<td style='width:5%'>&nbsp;</td>
								<td style='width:45%'>&nbsp;</td>
							{/if}
							</tr>
						{/if}
					</tbody>
				</table>
			</div>
		{/foreach}
	</div>
{/strip}