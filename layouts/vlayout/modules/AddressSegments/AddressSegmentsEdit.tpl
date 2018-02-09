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
			<tr class="fieldLabel">
				<td colspan="9">
					<button type="button" class="addAddressSegments">+</button>
					{* Amin made this button it makes no sense leaving it here until I get word from higher up to get rid of it
					<button type="button" class="hideRemovedAddressSegments">Toggle Removed AddressSegmentss</button>
					*}
					<button type="button" class="addAddressSegments" style="clear:right;float:right">+</button>
				</td>
			</tr>
			<tr style="width:100%" class="fieldLabel">
				<td style="text-align:center;margin:auto;width:4%;">
					<input type="hidden" name="numAgents" value="{($ADDRESSSEGMENTS_LIST|@count)}"/></td>
				<td style="text-align:center;margin:auto;width:12%;">Segment Number</td>
				<td style="text-align:center;margin:auto;width:12%;">Origin</td>
				<td style="text-align:center;margin:auto;width:12%;">Destination</td>
				<td style="text-align:center;margin:auto;width:12%;">Transportation</td>
				<td style="text-align:center;margin:auto;width:12%;">Cube</td>
				<td style="text-align:center;margin:auto;width:12%;">Weight</td>
				<td style="text-align:center;margin:auto;width:12%;">Weight Override</td>
				<td style="text-align:center;margin:auto;width:12%;">Cube Override</td>
			</tr>
			{assign var=SEQUENCE_FIELD_MODEL value=$ADDRESSSEGMENTS_MODULE_MODEL->getField("addresssegments_sequence")}
			{assign var=ORIGIN_FIELD_MODEL value=$ADDRESSSEGMENTS_MODULE_MODEL->getField("addresssegments_origin")}
			{assign var=DESTINATION_FIELD_MODEL value=$ADDRESSSEGMENTS_MODULE_MODEL->getField("addresssegments_destination")}
			{assign var=TRANSPORTATION_FIELD_MODEL value=$ADDRESSSEGMENTS_MODULE_MODEL->getField("addresssegments_transportation")}
			{assign var=CUBE_FIELD_MODEL value=$ADDRESSSEGMENTS_MODULE_MODEL->getField("addresssegments_cube")}
			{assign var=WEIGHT_FIELD_MODEL value=$ADDRESSSEGMENTS_MODULE_MODEL->getField("addresssegments_weight")}
			{assign var=WEIGHTOVERRIDE_FIELD_MODEL value=$ADDRESSSEGMENTS_MODULE_MODEL->getField("addresssegments_weightoverride")}
			{assign var=CUBEOVERRIDE_FIELD_MODEL value=$ADDRESSSEGMENTS_MODULE_MODEL->getField("addresssegments_cubeoverride")}

			<tr style="margin:auto"class="defaultAddressSegments addresssegmentRow hide">
				<td class="fieldValue" style="margin:auto">
					<i title="Delete" class="icon-trash removeAddressSegments"></i>
					<input type="hidden" class="default" name="addresssegmentId" value="none" />
				</td>
				<td class="fieldValue typeCell" style="margin:auto">
					<div class="row-fluid">
						<span class="span10">
							{include file=vtemplate_path($SEQUENCE_FIELD_MODEL->getUITypeModel()->getTemplateName(),'AddressSegments') FIELD_MODEL=$SEQUENCE_FIELD_MODEL BLOCK_FIELDS=$ADDRESSSEGMENTS_BLOCK_FIELDS DEFAULT_CHZN=1}
						</span>
					</div>
				</td>
				<td class="fieldValue" style="margin:auto">
					<div class="row-fluid">
						<span class="span10">
							{include file=vtemplate_path($ORIGIN_FIELD_MODEL->getUITypeModel()->getTemplateName(),'AddressSegments') FIELD_MODEL=$ORIGIN_FIELD_MODEL BLOCK_FIELDS=$ADDRESSSEGMENTS_BLOCK_FIELDS DEFAULT_CHZN=1}
						</span>
					</div>
				</td>
				<td class="fieldValue" style="margin:auto">
					<div class="row-fluid">
						<span class="span10">
							{include file=vtemplate_path($DESTINATION_FIELD_MODEL->getUITypeModel()->getTemplateName(),'AddressSegments') FIELD_MODEL=$DESTINATION_FIELD_MODEL BLOCK_FIELDS=$ADDRESSSEGMENTS_BLOCK_FIELDS DEFAULT_CHZN=1}
						</span>
					</div>
				</td>
				<td class="fieldValue" style="margin:auto">
					<div class="row-fluid">
						<span class="span10">
							{include file=vtemplate_path($TRANSPORTATION_FIELD_MODEL->getUITypeModel()->getTemplateName(),'AddressSegments') FIELD_MODEL=$TRANSPORTATION_FIELD_MODEL BLOCK_FIELDS=$ADDRESSSEGMENTS_BLOCK_FIELDS DEFAULT_CHZN=1}
						</span>
					</div>
				</td>
				<td class="fieldValue" style="margin:auto">
					<div class="row-fluid">
						<span class="span10">
							{include file=vtemplate_path($CUBE_FIELD_MODEL->getUITypeModel()->getTemplateName(),'AddressSegments') FIELD_MODEL=$CUBE_FIELD_MODEL BLOCK_FIELDS=$ADDRESSSEGMENTS_BLOCK_FIELDS}
						</span>
					</div>
				</td>
				<td class="fieldValue" style="margin:auto">
					<div class="row-fluid">
						<span class="span10">
							{include file=vtemplate_path($WEIGHT_FIELD_MODEL->getUITypeModel()->getTemplateName(),'AddressSegments') FIELD_MODEL=$WEIGHT_FIELD_MODEL BLOCK_FIELDS=$ADDRESSSEGMENTS_BLOCK_FIELDS}
						</span>
					</div>
				</td>
				<td class="fieldValue" style="margin:auto">
					<div class="row-fluid">
						<span class="span10">
							{include file=vtemplate_path($WEIGHTOVERRIDE_FIELD_MODEL->getUITypeModel()->getTemplateName(),'AddressSegments') FIELD_MODEL=$WEIGHTOVERRIDE_FIELD_MODEL BLOCK_FIELDS=$ADDRESSSEGMENTS_BLOCK_FIELDS}
						</span>
					</div>
				</td>
				<td class="fieldValue" style="margin:auto">
					<div class="row-fluid">
						<span class="span10">
							{include file=vtemplate_path($CUBEOVERRIDE_FIELD_MODEL->getUITypeModel()->getTemplateName(),'AddressSegments') FIELD_MODEL=$CUBEOVERRIDE_FIELD_MODEL BLOCK_FIELDS=$ADDRESSSEGMENTS_BLOCK_FIELDS}
						</span>
					</div>
				</td>

			</tr>
				{foreach key=ROW_NUM item=ADDRESSSEGMENTS from=$ADDRESSSEGMENTS_LIST}
					<tr style="margin:auto" class="addresssegmentRow{$ROW_NUM+1} addresssegmentRow">
						<td class="fieldValue" style="margin:auto">
							<input type="hidden" name="addresssegmentId" value="{$ADDRESSSEGMENTS['addresssegmentsid']}" />
							<input type="hidden" class="addresssegments_fromcube" value="{$ADDRESSSEGMENTS['addresssegments_fromcube']}" />
							<input type="hidden" class="default" name="addresssegmentDelete" value="" />
							<input type="hidden" class="row_num" name="row_num" value="{$ROW_NUM+1}" />
							{if $ROW_NUM neq 0}<i title="Delete" class="icon-trash removeAddressSegments"></i>{/if}
						</td>
						<td class="fieldValue typeCell" style="margin:auto">
							<div class="row-fluid">
								{assign var=SEQUENCE_FIELD_MODEL value=$SEQUENCE_FIELD_MODEL->set('fieldvalue',$ADDRESSSEGMENTS['addresssegments_sequence'])}
								{*{assign var=SEQUENCE_FIELD_MODEL value=$SEQUENCE_FIELD_MODEL->set('name',"addresssegments_sequence_"|cat:$ROW_NUM)}*}
								<span class="span10">
									{include file=vtemplate_path($SEQUENCE_FIELD_MODEL->getUITypeModel()->getTemplateName(),'AddressSegments') FIELD_MODEL=$SEQUENCE_FIELD_MODEL BLOCK_FIELDS=$ADDRESSSEGMENTS_BLOCK_FIELDS}
								</span>
							</div>
						</td>
						<td class="fieldValue" style="margin:auto">
							<div class="row-fluid">
								<span class="span10">
									{assign var=ORIGIN_FIELD_MODEL value=$ORIGIN_FIELD_MODEL->set('fieldvalue',$ADDRESSSEGMENTS['addresssegments_origin'])}
									{*{assign var=ORIGIN_FIELD_MODEL value=$ORIGIN_FIELD_MODEL->set('name',"addresssegments_origin_"|cat:$ROW_NUM)}*}
									{include file=vtemplate_path($ORIGIN_FIELD_MODEL->getUITypeModel()->getTemplateName(),'AddressSegments') FIELD_MODEL=$ORIGIN_FIELD_MODEL BLOCK_FIELDS=$ADDRESSSEGMENTS_BLOCK_FIELDS}
								</span>
							</div>
						</td>
						<td class="fieldValue" style="margin:auto">
							<div class="row-fluid">
								{assign var=DESTINATION_FIELD_MODEL value=$DESTINATION_FIELD_MODEL->set('fieldvalue',$ADDRESSSEGMENTS['addresssegments_destination'])}
								{*{assign var=DESTINATION_FIELD_MODEL value=$DESTINATION_FIELD_MODEL->set('name',"addresssegments_sequence_"|cat:$ROW_NUM)}*}
								<span class="span10">
									{include file=vtemplate_path($DESTINATION_FIELD_MODEL->getUITypeModel()->getTemplateName(),'AddressSegments') FIELD_MODEL=$DESTINATION_FIELD_MODEL BLOCK_FIELDS=$ADDRESSSEGMENTS_BLOCK_FIELDS}
								</span>
							</div>
						</td>
						<td class="fieldValue" style="margin:auto">
							<div class="row-fluid">
								{assign var=TRANSPORTATION_FIELD_MODEL value=$TRANSPORTATION_FIELD_MODEL->set('fieldvalue',$ADDRESSSEGMENTS['addresssegments_transportation'])}
								{*{assign var=TRANSPORTATION_FIELD_MODEL value=$TRANSPORTATION_FIELD_MODEL->set('name',"addresssegments_transportation_"|cat:$ROW_NUM)}*}
								<span class="span10">
									{include file=vtemplate_path($TRANSPORTATION_FIELD_MODEL->getUITypeModel()->getTemplateName(),'AddressSegments') FIELD_MODEL=$TRANSPORTATION_FIELD_MODEL BLOCK_FIELDS=$ADDRESSSEGMENTS_BLOCK_FIELDS}
								</span>
							</div>
						</td>
						<td class="fieldValue" style="margin:auto">
							<div class="row-fluid">
								{assign var=CUBE_FIELD_MODEL value=$CUBE_FIELD_MODEL->set('fieldvalue',$ADDRESSSEGMENTS['addresssegments_cube'])}
								{*{assign var=CUBE_FIELD_MODEL value=$CUBE_FIELD_MODEL->set('name',"addresssegments_cube_"|cat:$ROW_NUM)}*}
								<span class="span10">
									{include file=vtemplate_path($CUBE_FIELD_MODEL->getUITypeModel()->getTemplateName(),'AddressSegments') FIELD_MODEL=$CUBE_FIELD_MODEL BLOCK_FIELDS=$ADDRESSSEGMENTS_BLOCK_FIELDS}
								</span>
							</div>
						</td>
						<td class="fieldValue" style="margin:auto">
							<div class="row-fluid">
								{assign var=WEIGHT_FIELD_MODEL value=$WEIGHT_FIELD_MODEL->set('fieldvalue',$ADDRESSSEGMENTS['addresssegments_weight'])}
								{*{assign var=WEIGHT_FIELD_MODEL value=$WEIGHT_FIELD_MODEL->set('name',"addresssegments_weight_"|cat:$ROW_NUM)}*}
								<span class="span10">
									{include file=vtemplate_path($WEIGHT_FIELD_MODEL->getUITypeModel()->getTemplateName(),'AddressSegments') FIELD_MODEL=$WEIGHT_FIELD_MODEL BLOCK_FIELDS=$ADDRESSSEGMENTS_BLOCK_FIELDS}
								</span>
							</div>
						</td>
						<td class="fieldValue" style="margin:auto">
							<div class="row-fluid">
								{assign var=WEIGHTOVERRIDE_FIELD_MODEL value=$WEIGHTOVERRIDE_FIELD_MODEL->set('fieldvalue',$ADDRESSSEGMENTS['addresssegments_weightoverride'])}
								{*{assign var=WEIGHTOVERRIDE_FIELD_MODEL value=$WEIGHTOVERRIDE_FIELD_MODEL->set('name',"addresssegments_weightoverride_"|cat:$ROW_NUM)}*}
								<span class="span10">
									{include file=vtemplate_path($WEIGHTOVERRIDE_FIELD_MODEL->getUITypeModel()->getTemplateName(),'AddressSegments') FIELD_MODEL=$WEIGHTOVERRIDE_FIELD_MODEL BLOCK_FIELDS=$ADDRESSSEGMENTS_BLOCK_FIELDS}
								</span>
							</div>
						</td>
						<td class="fieldValue" style="margin:auto">
							<div class="row-fluid">
								{assign var=CUBEOVERRIDE_FIELD_MODEL value=$CUBEOVERRIDE_FIELD_MODEL->set('fieldvalue',$ADDRESSSEGMENTS['addresssegments_cubeoverride'])}
								{*{assign var=CUBEOVERRIDE_FIELD_MODEL value=$CUBEOVERRIDE_FIELD_MODEL->set('name',"addresssegments_cubeoverride_"|cat:$ROW_NUM)}*}
								<span class="span10">
									{include file=vtemplate_path($CUBEOVERRIDE_FIELD_MODEL->getUITypeModel()->getTemplateName(),'AddressSegments') FIELD_MODEL=$CUBEOVERRIDE_FIELD_MODEL BLOCK_FIELDS=$ADDRESSSEGMENTS_BLOCK_FIELDS}
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