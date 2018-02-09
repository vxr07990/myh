{strip}
{if $VEHICLE_LOOKUP}
	<table name='preShipChecklistTable' class='table table-bordered equalSplit detailview-table'>
		<thead>
			<tr>
				<th class='blockHeader' colspan='4'>{vtranslate('LBL_VEHICLE_CHECKLIST', 'VehicleLookup')}</th>
			</tr>
		</thead>
		<tbody>
			{assign var=NUM_ITEMS value=0}
			{foreach key=CHECKLIST_ID item=CHECKLIST_ITEM from=$PRESHIP_CHECKLIST}
				{assign var=NUM_ITEMS value=$NUM_ITEMS+1}
				<tr class='checklistRow'>
					<td class='fieldValue' colspan='4' style='padding: 5px'>
						<span class='value' data-field-type='string'>
							{$CHECKLIST_ITEM['checklist_string']}
						</span>
					</td>
				</tr>
			{/foreach}
		</tbody>
	</table>
	<br />
			
{/if}
{/strip}