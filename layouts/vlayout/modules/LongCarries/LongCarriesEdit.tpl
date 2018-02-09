{strip}
	{assign var=WIDTHTYPE value=$USER_MODEL->get('rowheight')}
	<table name='LongCarriesTable' class='table table-bordered blockContainer showInlineTable'>
		<thead>
			<tr>
				<th class='blockHeader' colspan='9'>
					{vtranslate('LongCarries', 'LongCarries')}
					<button type="button" class="btn btn-small pull-right" id="btn-verifyLongCarryTable">{vtranslate('LBL_LONG_CARRY_BUTTON_VERIFY_TABLE','LongCarries')}</button>
				</th>
			</tr>
		</thead>
		<tbody>
			<tr class="fieldLabel">
				<td colspan="9">
					<input type="hidden" name="numLongCarriesAgents" value="{($LONGCARRIES_LIST|@count)}"/>
					<button type="button" class="addLongCarries">+</button>
					<button type="button" class="addLongCarries" style="clear:right;float:right">+</button>
				</td>
			</tr>
			{assign var=LONGCARRIES_NUMBER_FIELD_MODEL value=$LONGCARRIES_MODULE_MODEL->getField("longcarries_uptoft")}
			{assign var=LONGCARRIES_PERCENT_FIELD_MODEL value=$LONGCARRIES_MODULE_MODEL->getField("longcarries_percent")}


			<tr style="margin:auto"class="defaultLongCarries longcarryRow hide">
				<td class="fieldValue" style="margin:auto">
					<i title="Delete" class="icon-trash removeLongCarries"></i>
					<input type="hidden" class="default" name="longcarryId" value="none" />
				</td>
				<td class="fieldLabel {$WIDTHTYPE}" style="margin:auto">
					{vtranslate($LONGCARRIES_NUMBER_FIELD_MODEL->get('label'),'LongCarries')}
				</td>
				<td class="fieldValue" style="margin:auto">
					<div class="row-fluid">
						<span class="span10">
							{include file=vtemplate_path($LONGCARRIES_NUMBER_FIELD_MODEL->getUITypeModel()->getTemplateName(),'LongCarries') FIELD_MODEL=$LONGCARRIES_NUMBER_FIELD_MODEL BLOCK_FIELDS=$LONGCARRIES_BLOCK_FIELDS DEFAULT_CHZN=1}
						</span>
					</div>
				</td>
				<td class="fieldLabel {$WIDTHTYPE}" style="margin:auto">
					{vtranslate($LONGCARRIES_PERCENT_FIELD_MODEL->get('label'),'LongCarries')}
				</td>
				<td class="fieldValue" style="margin:auto">
					<div class="row-fluid">
						<span class="span10">
							{include file=vtemplate_path($LONGCARRIES_PERCENT_FIELD_MODEL->getUITypeModel()->getTemplateName(),'LongCarries') FIELD_MODEL=$LONGCARRIES_PERCENT_FIELD_MODEL BLOCK_FIELDS=$LONGCARRIES_BLOCK_FIELDS DEFAULT_CHZN=1}
						</span>
					</div>
				</td>
			</tr>

			{foreach key=ROW_NUM item=LONGCARRY from=$LONGCARRIES_LIST}
				<tr style="margin:auto"class="longcarryRow{$ROW_NUM+1} longcarryRow">
					<td class="fieldValue" style="margin:auto">
						<input type="hidden" name="longcarryId" value="{$LONGCARRY['longcarriesid']}" />
						<input type="hidden" class="default" name="longcarryDelete" value="" />
						<input type="hidden" class="row_num" name="row_num" value="{$ROW_NUM+1}" />
						<i title="Delete" class="icon-trash removeLongCarries"></i>
					</td>
					<td class="fieldLabel {$WIDTHTYPE}" style="margin:auto">
						{vtranslate($LONGCARRIES_NUMBER_FIELD_MODEL->get('label'),'LongCarries')}
					</td>
					<td class="fieldValue" style="margin:auto">
						<div class="row-fluid">
						<span class="span10">
							{assign var=LONGCARRIES_NUMBER_FIELD_MODEL value=$LONGCARRIES_NUMBER_FIELD_MODEL->set('fieldvalue',$LONGCARRY['longcarries_uptoft'])}
							{include file=vtemplate_path($LONGCARRIES_NUMBER_FIELD_MODEL->getUITypeModel()->getTemplateName(),'LongCarries') FIELD_MODEL=$LONGCARRIES_NUMBER_FIELD_MODEL BLOCK_FIELDS=$LONGCARRIES_BLOCK_FIELDS DEFAULT_CHZN=1}
						</span>
						</div>
					</td>
					<td class="fieldLabel {$WIDTHTYPE}" style="margin:auto">
						{vtranslate($LONGCARRIES_PERCENT_FIELD_MODEL->get('label'),'LongCarries')}
					</td>
					<td class="fieldValue" style="margin:auto">
						<div class="row-fluid">
						<span class="span10">
							{assign var=LONGCARRIES_PERCENT_FIELD_MODEL value=$LONGCARRIES_PERCENT_FIELD_MODEL->set('fieldvalue',$LONGCARRY['longcarries_percent'])}
							{include file=vtemplate_path($LONGCARRIES_PERCENT_FIELD_MODEL->getUITypeModel()->getTemplateName(),'LongCarries') FIELD_MODEL=$LONGCARRIES_PERCENT_FIELD_MODEL BLOCK_FIELDS=$LONGCARRIES_BLOCK_FIELDS DEFAULT_CHZN=1}
						</span>
						</div>
					</td>
				</tr>
			{/foreach}
		</tbody>
	</table>
	<br>
{/strip}