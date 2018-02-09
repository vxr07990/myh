{strip}
{if $VEHICLE_LOOKUP}
	<table name='preShipChecklistTable' class='table table-bordered blockContainer showInlineTable'>
		<thead>
			<tr>
				<th class='blockHeader' colspan='4'>{vtranslate('LBL_VEHICLE_CHECKLIST', 'VehicleLookup')}</th>
			</tr>
		</thead>
		<tbody>
			<tr class='fieldLabel' colspan='4'>
				<td colspan='4'><button type='button' name='addChecklistItem' id='addChecklistItem'>+</button><button type='button' name='addChecklistItem2' id='addChecklistItem2' style='clear:right;float:right'>+</button></td>
			</tr>
			<tr class='hide defaultChecklistItem checklistRow newItemRow'>
				<td style='text-align:center; width:5%;'>
					<a class="deleteChecklistItemButton">
						<i title="Delete" class="icon-trash alignMiddle"></i>
					</a>
				</td>
				<td class='fieldValue' colspan='3' style='text-align:center; padding: 5px'>
					<input type='text' class='input-large' style='width:90%' name='checklistItemDescription' />
				</td>
			</tr>
			{assign var=NUM_ITEMS value=0}
			{foreach key=CHECKLIST_ID item=CHECKLIST_ITEM from=$PRESHIP_CHECKLIST}
				{assign var=NUM_ITEMS value=$NUM_ITEMS+1}
				<tr class='checklistRow newItemRow'>
					<td style='text-align:center; width:5%;'>
						<a class="deleteChecklistItemButton">
							<i title="Delete" class="icon-trash alignMiddle"></i>
						</a>
					</td>
					<td class='fieldValue' colspan='3' style='text-align:center; padding: 5px'>
						<input type='hidden' name='checklistItemId_{$NUM_ITEMS}' value={if $CHECKLIST_ITEM['agentmanagerid'] eq 0 }'default'{else}{$CHECKLIST_ID}{/if} />
						<input type='text' class='input-large' style='width:90%' name='checklistItemDescription_{$NUM_ITEMS}' value='{$CHECKLIST_ITEM['checklist_string']}' />
					</td>
				</tr>
			{/foreach}
		</tbody>
	</table>
	<br />
			
{/if}
{/strip}