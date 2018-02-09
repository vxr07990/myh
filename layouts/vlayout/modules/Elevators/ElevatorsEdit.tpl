{strip}
	{assign var=WIDTHTYPE value=$USER_MODEL->get('rowheight')}
	<table name='ElevatorsTable' class='table table-bordered blockContainer showInlineTable'>
		<thead>
		<tr>
			<th class='blockHeader' colspan='9'>
				{vtranslate('Elevators', 'Elevators')}
				<button type="button" class="btn btn-small pull-right" id="btn-verifyElevatorTable">{vtranslate('LBL_ELEVATOR_BUTTON_VERIFY_TABLE','Elevators')}</button>
			</th>
		</tr>
		</thead>
		<tbody>
		<tr class="fieldLabel">
			<td colspan="9">
				<input type="hidden" name="numElevatorsAgents" value="{($ELEVATORS_LIST|@count)}"/>
				<button type="button" class="addElevators">+</button>
				<button type="button" class="addElevators" style="clear:right;float:right">+</button>
			</td>
		</tr>
		{assign var=ELEVATORS_NUMBER_FIELD_MODEL value=$ELEVATORS_MODULE_MODEL->getField("elevators_number")}
		{assign var=ELEVATORS_PERCENT_FIELD_MODEL value=$ELEVATORS_MODULE_MODEL->getField("elevators_percent")}

		<tr style="margin:auto"class="defaultElevators elevatorRow hide">
			<td class="fieldValue" style="margin:auto">
				<i title="Delete" class="icon-trash removeElevators"></i>
				<input type="hidden" class="default" name="elevatorId" value="none" />
			</td>
			<td class="fieldLabel {$WIDTHTYPE}" style="margin:auto">
				{vtranslate($ELEVATORS_NUMBER_FIELD_MODEL->get('label'),'Elevators')}
			</td>
			<td class="fieldValue" style="margin:auto">
				<div class="row-fluid">
						<span class="span10">
							{include file=vtemplate_path($ELEVATORS_NUMBER_FIELD_MODEL->getUITypeModel()->getTemplateName(),'Elevators') FIELD_MODEL=$ELEVATORS_NUMBER_FIELD_MODEL BLOCK_FIELDS=$ELEVATORS_BLOCK_FIELDS DEFAULT_CHZN=1}
						</span>
				</div>
			</td>
			<td class="fieldLabel {$WIDTHTYPE}" style="margin:auto">
				{vtranslate($ELEVATORS_PERCENT_FIELD_MODEL->get('label'),'Elevators')}
			</td>
			<td class="fieldValue" style="margin:auto">
				<div class="row-fluid">
						<span class="span10">
							{include file=vtemplate_path($ELEVATORS_PERCENT_FIELD_MODEL->getUITypeModel()->getTemplateName(),'Elevators') FIELD_MODEL=$ELEVATORS_PERCENT_FIELD_MODEL BLOCK_FIELDS=$ELEVATORS_BLOCK_FIELDS DEFAULT_CHZN=1}
						</span>
				</div>
			</td>
		</tr>

		{foreach key=ROW_NUM item=ELEVATOR from=$ELEVATORS_LIST}
			<tr style="margin:auto"class="elevatorRow{$ROW_NUM+1} elevatorRow">
				<td class="fieldValue" style="margin:auto">
					<input type="hidden" name="elevatorId" value="{$ELEVATOR['elevatorsid']}" />
					<input type="hidden" class="default" name="elevatorDelete" value="" />
					<input type="hidden" class="row_num" name="row_num" value="{$ROW_NUM+1}" />
					<i title="Delete" class="icon-trash removeElevators"></i>
				</td>
				<td class="fieldLabel {$WIDTHTYPE}" style="margin:auto">
					{vtranslate($ELEVATORS_NUMBER_FIELD_MODEL->get('label'),'Elevators')}
				</td>
				<td class="fieldValue" style="margin:auto">
					<div class="row-fluid">
						<span class="span10">
							{assign var=ELEVATORS_NUMBER_FIELD_MODEL value=$ELEVATORS_NUMBER_FIELD_MODEL->set('fieldvalue',$ELEVATOR['elevators_number'])}
							{include file=vtemplate_path($ELEVATORS_NUMBER_FIELD_MODEL->getUITypeModel()->getTemplateName(),'Elevators') FIELD_MODEL=$ELEVATORS_NUMBER_FIELD_MODEL BLOCK_FIELDS=$ELEVATORS_BLOCK_FIELDS DEFAULT_CHZN=1}
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
							{include file=vtemplate_path($ELEVATORS_PERCENT_FIELD_MODEL->getUITypeModel()->getTemplateName(),'Elevators') FIELD_MODEL=$ELEVATORS_PERCENT_FIELD_MODEL BLOCK_FIELDS=$ELEVATORS_BLOCK_FIELDS DEFAULT_CHZN=1}
						</span>
					</div>
				</td>
			</tr>
		{/foreach}
		</tbody>
	</table>
	<br>
{/strip}