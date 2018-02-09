{strip}
{assign var=HAS_CONTENT value=(!$BLOCK_SUBLIST || $BLOCK_SUBLIST['ADDRESS_SEGMENTS_TABLE'])}
<div id="contentHolder_ADDRESS_SEGMENTS_TABLE" class="sectionContentHolder {$CONTENT_DIV_CLASS} {if !$ALWAYS_SHOW_CONTENT_DIV}hide{/if} {if !$HAS_CONTENT}inactive{/if}">
	{if $HAS_CONTENT}
	<table name='AddressSegmentsTable' class='table table-bordered blockContainer showInlineTable'>
		<thead>
		<tr>
			<th class='blockHeader' colspan='9'>{vtranslate('AddressSegments', 'AddressSegments')}</th>
		</tr>
		</thead>
		{*assign var=USE_STATUS value=true*}{*Change this to true to bring back the status column when messaging has been made to work*}
		<tbody>
		<tr style="width:100%" class="fieldLabel">
			<td style="text-align:center;margin:auto;width:12%;"><b>Segment Number</b></td>
			<td style="text-align:center;margin:auto;width:12%;"><b>Origin</b></td>
			<td style="text-align:center;margin:auto;width:12%;"><b>Destination</b></td>
			<td style="text-align:center;margin:auto;width:12%;"><b>Transportation</b></td>
			<td style="text-align:center;margin:auto;width:12%;"><b>Cube</b></td>
			<td style="text-align:center;margin:auto;width:12%;"><b>Weight</b></td>
			<td style="text-align:center;margin:auto;width:12%;"><b>Weight Override</b></td>
			<td style="text-align:center;margin:auto;width:12%;"><b>Cube Override</b></td>
		</tr>
		{assign var=SEQUENCE_FIELD_MODEL value=$ADDRESSSEGMENTS_MODULE_MODEL->getField("addresssegments_sequence")}
		{assign var=ORIGIN_FIELD_MODEL value=$ADDRESSSEGMENTS_MODULE_MODEL->getField("addresssegments_origin")}
		{assign var=DESTINATION_FIELD_MODEL value=$ADDRESSSEGMENTS_MODULE_MODEL->getField("addresssegments_destination")}
		{assign var=TRANSPORTATION_FIELD_MODEL value=$ADDRESSSEGMENTS_MODULE_MODEL->getField("addresssegments_transportation")}
		{assign var=CUBE_FIELD_MODEL value=$ADDRESSSEGMENTS_MODULE_MODEL->getField("addresssegments_cube")}
		{assign var=WEIGHT_FIELD_MODEL value=$ADDRESSSEGMENTS_MODULE_MODEL->getField("addresssegments_weight")}
		{assign var=WEIGHTOVERRIDE_FIELD_MODEL value=$ADDRESSSEGMENTS_MODULE_MODEL->getField("addresssegments_weightoverride")}
		{assign var=CUBEOVERRIDE_FIELD_MODEL value=$ADDRESSSEGMENTS_MODULE_MODEL->getField("addresssegments_cubeoverride")}

		{foreach key=ROW_NUM item=ADDRESSSEGMENTS from=$ADDRESSSEGMENTS_LIST}
			<tr style="margin:auto" class="addresssegmentRow{$ROW_NUM+1} addresssegmentRow">
				<td class="fieldValue typeCell" style="text-align:center;margin:auto">
					<div class="row-fluid">
						{assign var=SEQUENCE_FIELD_MODEL value=$SEQUENCE_FIELD_MODEL->set('fieldvalue',$ADDRESSSEGMENTS['addresssegments_sequence'])}
						<span class="span10">
							{$SEQUENCE_FIELD_MODEL->getDisplayValue($SEQUENCE_FIELD_MODEL->get('fieldvalue'))}
						</span>
					</div>
				</td>
				<td class="fieldValue" style="text-align:center;margin:auto">
					<div class="row-fluid">
						{assign var=ORIGIN_FIELD_MODEL value=$ORIGIN_FIELD_MODEL->set('fieldvalue',$ADDRESSSEGMENTS['addresssegments_origin'])}
						<span class="span10">
							{$ORIGIN_FIELD_MODEL->getDisplayValue($ORIGIN_FIELD_MODEL->get('fieldvalue'))}
						</span>
					</div>
				</td>
				<td class="fieldValue" style="text-align:center;margin:auto">
					<div class="row-fluid">
						{assign var=DESTINATION_FIELD_MODEL value=$DESTINATION_FIELD_MODEL->set('fieldvalue',$ADDRESSSEGMENTS['addresssegments_destination'])}
						<span class="span10">
							{$DESTINATION_FIELD_MODEL->getDisplayValue($DESTINATION_FIELD_MODEL->get('fieldvalue'))}
						</span>
					</div>
				</td>
				<td class="fieldValue" style="text-align:center;margin:auto">
					<div class="row-fluid">
						{assign var=TRANSPORTATION_FIELD_MODEL value=$TRANSPORTATION_FIELD_MODEL->set('fieldvalue',$ADDRESSSEGMENTS['addresssegments_transportation'])}
						<span class="span10">
							{$TRANSPORTATION_FIELD_MODEL->getDisplayValue($TRANSPORTATION_FIELD_MODEL->get('fieldvalue'))}
						</span>
					</div>
				</td>
				<td class="fieldValue" style="text-align:center;margin:auto">
					<div class="row-fluid">
						{assign var=CUBE_FIELD_MODEL value=$CUBE_FIELD_MODEL->set('fieldvalue',$ADDRESSSEGMENTS['addresssegments_cube'])}
						<span class="span10">
							{$CUBE_FIELD_MODEL->getDisplayValue($CUBE_FIELD_MODEL->get('fieldvalue'))}
						</span>
					</div>
				</td>
				<td class="fieldValue" style="text-align:center;margin:auto">
					<div class="row-fluid">
						{assign var=WEIGHT_FIELD_MODEL value=$WEIGHT_FIELD_MODEL->set('fieldvalue',$ADDRESSSEGMENTS['addresssegments_weight'])}
						<span class="span10">
							{$WEIGHT_FIELD_MODEL->getDisplayValue($WEIGHT_FIELD_MODEL->get('fieldvalue'))}
						</span>
					</div>
				</td>
				<td class="fieldValue" style="text-align:center;margin:auto">
					<div class="row-fluid">
						{assign var=WEIGHTOVERRIDE_FIELD_MODEL value=$WEIGHTOVERRIDE_FIELD_MODEL->set('fieldvalue',$ADDRESSSEGMENTS['addresssegments_weightoverride'])}
						<span class="span10">
							{$WEIGHTOVERRIDE_FIELD_MODEL->getDisplayValue($WEIGHTOVERRIDE_FIELD_MODEL->get('fieldvalue'))}
						</span>
					</div>
				</td>
				<td class="fieldValue" style="text-align:center;margin:auto">
					<div class="row-fluid">
						{assign var=CUBEOVERRIDE_FIELD_MODEL value=$CUBEOVERRIDE_FIELD_MODEL->set('fieldvalue',$ADDRESSSEGMENTS['addresssegments_cubeoverride'])}
						<span class="span10">
							{$CUBEOVERRIDE_FIELD_MODEL->getDisplayValue($CUBEOVERRIDE_FIELD_MODEL->get('fieldvalue'))}
						</span>
					</div>
				</td>
			</tr>
		{/foreach}
		</tbody>
	</table>
	<br>
	{/if}
	</div>
{/strip}