{strip}
	{assign var=WIDTHTYPE value=$USER_MODEL->get('rowheight')}
	<table name='FlightsTable' class='table table-bordered blockContainer showInlineTable'>
		<thead>
			<tr>
				<th class='blockHeader' colspan='9'>
					{vtranslate('Flights', 'Flights')}
					<button type="button" class="btn btn-small pull-right" id="btn-verifyFlightTable">{vtranslate('LBL_FLIGHT_BUTTON_VERIFY_TABLE','Flights')}</button>
				</th>
			</tr>
		</thead>
		<tbody>
			<tr class="fieldLabel">
				<td colspan="9">
					<input type="hidden" name="numFlightsAgents" value="{($FLIGHTS_LIST|@count)}"/>
					<button type="button" class="addFlights">+</button>
					<button type="button" class="addFlights" style="clear:right;float:right">+</button>
				</td>
			</tr>
			{assign var=FLIGHTS_NUMBER_FIELD_MODEL value=$FLIGHTS_MODULE_MODEL->getField("flights_number")}
			{assign var=FLIGHTS_PERCENT_FIELD_MODEL value=$FLIGHTS_MODULE_MODEL->getField("flights_percent")}


			<tr style="margin:auto"class="defaultFlights flightRow hide">
				<td class="fieldValue" style="margin:auto">
					<i title="Delete" class="icon-trash removeFlights"></i>
					<input type="hidden" class="default" name="flightId" value="none" />
				</td>
				<td class="fieldLabel {$WIDTHTYPE}" style="margin:auto">
					{vtranslate($FLIGHTS_NUMBER_FIELD_MODEL->get('label'),'Flights')}
				</td>
				<td class="fieldValue" style="margin:auto">
					<div class="row-fluid">
						<span class="span10">
							{include file=vtemplate_path($FLIGHTS_NUMBER_FIELD_MODEL->getUITypeModel()->getTemplateName(),'Flights') FIELD_MODEL=$FLIGHTS_NUMBER_FIELD_MODEL BLOCK_FIELDS=$FLIGHTS_BLOCK_FIELDS DEFAULT_CHZN=1}
						</span>
					</div>
				</td>
				<td class="fieldLabel {$WIDTHTYPE}" style="margin:auto">
					{vtranslate($FLIGHTS_PERCENT_FIELD_MODEL->get('label'),'Flights')}
				</td>
				<td class="fieldValue" style="margin:auto">
					<div class="row-fluid">
						<span class="span10">
							{include file=vtemplate_path($FLIGHTS_PERCENT_FIELD_MODEL->getUITypeModel()->getTemplateName(),'Flights') FIELD_MODEL=$FLIGHTS_PERCENT_FIELD_MODEL BLOCK_FIELDS=$FLIGHTS_BLOCK_FIELDS DEFAULT_CHZN=1}
						</span>
					</div>
				</td>
			</tr>

			{foreach key=ROW_NUM item=FLIGHT from=$FLIGHTS_LIST}
				<tr style="margin:auto"class="flightRow{$ROW_NUM+1} flightRow">
					<td class="fieldValue" style="margin:auto">
						<input type="hidden" name="flightId" value="{$FLIGHT['flightsid']}" />
						<input type="hidden" class="default" name="flightDelete" value="" />
						<input type="hidden" class="row_num" name="row_num" value="{$ROW_NUM+1}" />
						<i title="Delete" class="icon-trash removeFlights"></i>
					</td>
					<td class="fieldLabel {$WIDTHTYPE}" style="margin:auto">
						{vtranslate($FLIGHTS_NUMBER_FIELD_MODEL->get('label'),'Flights')}
					</td>
					<td class="fieldValue" style="margin:auto">
						<div class="row-fluid">
						<span class="span10">
							{assign var=FLIGHTS_NUMBER_FIELD_MODEL value=$FLIGHTS_NUMBER_FIELD_MODEL->set('fieldvalue',$FLIGHT['flights_number'])}
							{include file=vtemplate_path($FLIGHTS_NUMBER_FIELD_MODEL->getUITypeModel()->getTemplateName(),'Flights') FIELD_MODEL=$FLIGHTS_NUMBER_FIELD_MODEL BLOCK_FIELDS=$FLIGHTS_BLOCK_FIELDS DEFAULT_CHZN=1}
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
							{include file=vtemplate_path($FLIGHTS_PERCENT_FIELD_MODEL->getUITypeModel()->getTemplateName(),'Flights') FIELD_MODEL=$FLIGHTS_PERCENT_FIELD_MODEL BLOCK_FIELDS=$FLIGHTS_BLOCK_FIELDS DEFAULT_CHZN=1}
						</span>
						</div>
					</td>
				</tr>
			{/foreach}
		</tbody>
	</table>
	<br>
{/strip}