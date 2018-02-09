{strip}
	<table name='ElevatorsTable' class='table table-bordered blockContainer showInlineTable'>
		<thead>
		<tr>
			<th class='blockHeader' colspan='9'>{vtranslate('Elevators', 'Elevators')}</th>
		</tr>
		</thead>
		<tbody>
		{assign var=ELEVATORS_NUMBER_FIELD_MODEL value=$ELEVATORS_MODULE_MODEL->getField("elevators_number")}
		{assign var=ELEVATORS_PERCENT_FIELD_MODEL value=$ELEVATORS_MODULE_MODEL->getField("elevators_percent")}

		{foreach key=ROW_NUM item=ELEVATOR from=$ELEVATORS_LIST}
			<tr style="margin:auto"class="elevatorRow{$ROW_NUM+1} elevatorRow">
				<td class="fieldLabel {$WIDTHTYPE}" style="margin:auto">
					{vtranslate($ELEVATORS_NUMBER_FIELD_MODEL->get('label'),'Elevators')}
				</td>
				<td class="fieldValue" style="margin:auto">
					<div class="row-fluid">
						<span class="span10">
							{assign var=ELEVATORS_NUMBER_FIELD_MODEL value=$ELEVATORS_NUMBER_FIELD_MODEL->set('fieldvalue',$ELEVATOR['elevators_number'])}
							{$ELEVATORS_NUMBER_FIELD_MODEL->getDisplayValue($ELEVATORS_NUMBER_FIELD_MODEL->get('fieldvalue'))}
						</span>
					</div>
				</td>
				<td class="fieldLabel {$WIDTHTYPE}" style="margin:auto">
					{vtranslate($ELEVATORS_PERCENT_FIELD_MODEL->get('label'),'Elevators')}
				</td>
				<td class="fieldValue" style="margin:auto">
					<div class="row-fluid">
						<span class="span10">
							{assign var=ELEVATORS_PERCENT_FIELD_MODEL value=$ELEVATORS_PERCENT_FIELD_MODEL->set('fieldvalue',$ELEVATOR['elevators_percent'])}
							{$ELEVATORS_PERCENT_FIELD_MODEL->getDisplayValue($ELEVATORS_PERCENT_FIELD_MODEL->get('fieldvalue'))}
						</span>
					</div>
				</td>
			</tr>
		{/foreach}
		</tbody>
	</table>
	<br>
{/strip}