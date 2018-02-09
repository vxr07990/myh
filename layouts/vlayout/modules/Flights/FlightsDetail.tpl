{strip}
	<table name='FlightsTable' class='table table-bordered blockContainer showInlineTable'>
		<thead>
		<tr>
			<th class='blockHeader' colspan='9'>{vtranslate('Flights', 'Flights')}</th>
		</tr>
		</thead>
		<tbody>
		{assign var=FLIGHTS_NUMBER_FIELD_MODEL value=$FLIGHTS_MODULE_MODEL->getField("flights_number")}
		{assign var=FLIGHTS_PERCENT_FIELD_MODEL value=$FLIGHTS_MODULE_MODEL->getField("flights_percent")}

		{foreach key=ROW_NUM item=FLIGHT from=$FLIGHTS_LIST}
			<tr style="margin:auto"class="flightRow{$ROW_NUM+1} flightRow">
				<td class="fieldLabel {$WIDTHTYPE}" style="margin:auto">
					{vtranslate($FLIGHTS_NUMBER_FIELD_MODEL->get('label'),'Flights')}
				</td>
				<td class="fieldValue" style="margin:auto">
					<div class="row-fluid">
						<span class="span10">
							{assign var=FLIGHTS_NUMBER_FIELD_MODEL value=$FLIGHTS_NUMBER_FIELD_MODEL->set('fieldvalue',$FLIGHT['flights_number'])}
							{$FLIGHTS_NUMBER_FIELD_MODEL->getDisplayValue($FLIGHTS_NUMBER_FIELD_MODEL->get('fieldvalue'))}
						</span>
					</div>
				</td>
				<td class="fieldLabel {$WIDTHTYPE}" style="margin:auto">
					{vtranslate($FLIGHTS_PERCENT_FIELD_MODEL->get('label'),'Flights')}
				</td>
				<td class="fieldValue" style="margin:auto">
					<div class="row-fluid">
						<span class="span10">
							{assign var=FLIGHTS_PERCENT_FIELD_MODEL value=$FLIGHTS_PERCENT_FIELD_MODEL->set('fieldvalue',$FLIGHT['flights_percent'])}
							{$FLIGHTS_PERCENT_FIELD_MODEL->getDisplayValue($FLIGHTS_PERCENT_FIELD_MODEL->get('fieldvalue'))}
						</span>
					</div>
				</td>
			</tr>
		{/foreach}
		</tbody>
	</table>
	<br>
{/strip}