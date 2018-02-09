{strip}
	<table name='LongCarriesTable' class='table table-bordered blockContainer showInlineTable'>
		<thead>
		<tr>
			<th class='blockHeader' colspan='9'>{vtranslate('LongCarries', 'LongCarries')}</th>
		</tr>
		</thead>
		<tbody>
		{assign var=LONGCARRIES_NUMBER_FIELD_MODEL value=$LONGCARRIES_MODULE_MODEL->getField("longcarries_uptoft")}
		{assign var=LONGCARRIES_PERCENT_FIELD_MODEL value=$LONGCARRIES_MODULE_MODEL->getField("longcarries_percent")}

		{foreach key=ROW_NUM item=FLIGHT from=$LONGCARRIES_LIST}
			<tr style="margin:auto"class="longcarryRow{$ROW_NUM+1} longcarryRow">
				<td class="fieldLabel {$WIDTHTYPE}" style="margin:auto">
					{vtranslate($LONGCARRIES_NUMBER_FIELD_MODEL->get('label'),'LongCarries')}
				</td>
				<td class="fieldValue" style="margin:auto">
					<div class="row-fluid">
						<span class="span10">
							{assign var=LONGCARRIES_NUMBER_FIELD_MODEL value=$LONGCARRIES_NUMBER_FIELD_MODEL->set('fieldvalue',$FLIGHT['longcarries_uptoft'])}
							{$LONGCARRIES_NUMBER_FIELD_MODEL->getDisplayValue($LONGCARRIES_NUMBER_FIELD_MODEL->get('fieldvalue'))}
						</span>
					</div>
				</td>
				<td class="fieldLabel {$WIDTHTYPE}" style="margin:auto">
					{vtranslate($LONGCARRIES_PERCENT_FIELD_MODEL->get('label'),'LongCarries')}
				</td>
				<td class="fieldValue" style="margin:auto">
					<div class="row-fluid">
						<span class="span10">
							{assign var=LONGCARRIES_PERCENT_FIELD_MODEL value=$LONGCARRIES_PERCENT_FIELD_MODEL->set('fieldvalue',$FLIGHT['longcarries_percent'])}
							{$LONGCARRIES_PERCENT_FIELD_MODEL->getDisplayValue($LONGCARRIES_PERCENT_FIELD_MODEL->get('fieldvalue'))}
						</span>
					</div>
				</td>
			</tr>
		{/foreach}
		</tbody>
	</table>
	<br>
{/strip}