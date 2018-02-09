{strip}
	{if $EXTRA_STOPS}
		<table class="table table-bordered blockContainer showInlineTable equalSplit{if is_array($HIDDEN_BLOCKS)}{if in_array($STOPS_BLOCK_LABEL, $HIDDEN_BLOCKS)} {/if}{/if}" name="extraStopsTable">
			<thead>
			<tr>
				<th class="blockHeader" colspan="4">{vtranslate($STOPS_BLOCK_LABEL, 'ExtraStops')}</th>
			</tr>
			</thead>
			<tbody>
				<tr class="fieldLabel {if $MODULE eq 'Estimates'}hide{/if}" >
					<td colspan="4">
						<button type="button" class="addStop" name="addStop">+</button>
						<input type="hidden" id="numStops" name="numStops" value="{$EXTRASTOPS_LIST|@count}">
						<button type="button" class="addStop" name="addStop2" style="clear:right;float:right">+</button>
					</td>
				</tr>
			</tbody>
			{*hidden, default stop*}
			<tbody class="defaultStop stopBlock hide">
				<tr class="fieldLabel" colspan="4">
					<td colspan="4" class="blockHeader">
						<img class="cursorPointer alignMiddle blockToggle stopToggle{if !($IS_HIDDEN)} hide {/if} "  src="{vimage_path('arrowRight.png')}" data-mode="hide" data-id=''>
						<img class="cursorPointer alignMiddle blockToggle stopToggle{if ($IS_HIDDEN)} hide {/if}"  src="{vimage_path('arrowDown.png')}" data-mode="show" data-id=''>
						<span class="stopTitle"><b>&nbsp;&nbsp;&nbsp;Default Stop</b></span>
						{if $MODULE neq 'Estimates'}<a style="float: right; padding: 3px"><i title="Delete" class="deleteStopButton icon-trash"></i></a>{/if}
					</td>
				</tr>
				<tr style="padding: 0px;">
					<td style="padding: 0px;">
						<table class="table equalSplit table-bordered" style="padding: 0px; border: 0px;">
							<tbody class="stopContent defaultStopContent hide">
								<tr>
									{assign var=DEFAULT_CHZN value=1}
									{assign var=COUNTER value=0}
									{foreach key=FIELD_NAME item=FIELD_MODEL from=$STOPS_BLOCK_FIELDS name=blockfields}
									{assign var="isReferenceField" value=$FIELD_MODEL->getFieldDataType()}
									{if $isReferenceField eq 'reference' && count($FIELD_MODEL->getReferenceList()) < 1}{continue}{/if}
									{if $FIELD_MODEL->get('presence') eq 1}{continue}{/if}
									{if $FIELD_MODEL->get('uitype') eq "20" or $FIELD_MODEL->get('uitype') eq "19"}
									{if $COUNTER eq '1'}
									<td class="{$WIDTHTYPE}"></td><td class="{$WIDTHTYPE}"></td>
								</tr>
								<tr>
									{assign var=COUNTER value=0}
									{/if}
									{/if}
									{if $COUNTER eq 2}
								</tr>
								<tr>
									{assign var=COUNTER value=1}
									{else}
									{assign var=COUNTER value=$COUNTER+1}
									{/if}
									<td class="fieldLabel {$WIDTHTYPE}">
										{if $isReferenceField neq "reference"}<label class="muted pull-right marginRight10px">{/if}
											{if $FIELD_MODEL->isMandatory() eq true && $isReferenceField neq "reference"} <span class="redColor">*</span> {/if}
											{if $isReferenceField eq "reference"}
												{assign var="REFERENCE_LIST" value=$FIELD_MODEL->getReferenceList()}
												{assign var="REFERENCE_LIST_COUNT" value=count($REFERENCE_LIST)}
												{if $REFERENCE_LIST_COUNT > 1}
													{assign var="DISPLAYID" value=$FIELD_MODEL->get('fieldvalue')}
													{assign var="REFERENCED_MODULE_STRUCT" value=$FIELD_MODEL->getUITypeModel()->getReferenceModule($DISPLAYID)}
													{if !empty($REFERENCED_MODULE_STRUCT)}
														{assign var="REFERENCED_MODULE_NAME" value=$REFERENCED_MODULE_STRUCT->get('name')}
													{/if}
													<span class="pull-right">
														{if $FIELD_MODEL->isMandatory() eq true} <span class="redColor">*</span> {/if}
														<select id="ExtraStops_editView_fieldName_{$FIELD_MODEL->getName()}_dropDown" class="chzn-select referenceModulesList streched" style="width:160px;">
															<optgroup>
																{foreach key=index item=value from=$REFERENCE_LIST}
																	<option value="{$value}" {if $value eq $REFERENCED_MODULE_NAME} selected {/if}>{vtranslate($value, 'ExtraStops')}</option>
																{/foreach}
															</optgroup>
														</select>
													</span>
												{else}
													<label class="muted pull-right marginRight10px">{if $FIELD_MODEL->isMandatory() eq true} <span class="redColor">*</span> {/if}{vtranslate($FIELD_MODEL->get('label'), 'ExtraStops')}</label>
												{/if}
											{elseif $FIELD_MODEL->get('uitype') eq "83"}
												{include file=vtemplate_path($FIELD_MODEL->getUITypeModel()->getTemplateName(),'ExtraStops') COUNTER=$COUNTER MODULE='ExtraStops'}
											{else}
												{vtranslate($FIELD_MODEL->get('label'), 'ExtraStops')}
											{/if}
											{if $isReferenceField neq "reference"}</label>{/if}
									</td>
									{if $FIELD_MODEL->get('uitype') neq "83"}
										<td class="fieldValue {$WIDTHTYPE}" {if $FIELD_MODEL->get('uitype') eq '19' or $FIELD_MODEL->get('uitype') eq '20'} colspan="3" {assign var=COUNTER value=$COUNTER+1} {/if}>
											<div class="row-fluid">
												<span class="span10">
													{include file=vtemplate_path($FIELD_MODEL->getUITypeModel()->getTemplateName(),'ExtraStops') STOPS_BLOCK_FIELDS=$STOPS_BLOCK_FIELDS}
												</span>
											</div>
										</td>
									{/if}
									{if $STOPS_BLOCK_FIELDS|@count eq 1 and $FIELD_MODEL->get('uitype') neq "19" and $FIELD_MODEL->get('uitype') neq "20" and $FIELD_MODEL->get('uitype') neq "30" and $FIELD_MODEL->get('name') neq "recurringtype"}
										<td class="{$WIDTHTYPE}"></td><td class="{$WIDTHTYPE}"></td>
									{/if}
									{/foreach}
									{* adding additional column for odd number of fields in a block *}
									{if $STOPS_BLOCK_FIELDS|@end eq true and $STOPS_BLOCK_FIELDS|@count neq 1 and $COUNTER eq 1}
										<td class="fieldLabel {$WIDTHTYPE}"></td><td class="{$WIDTHTYPE}"></td>
									{/if}
								</tr>
							</tbody>
						</table>
					</td>
				</tr>
			</tbody>
			{foreach key=STOP_INDEX item=CURRENT_STOP from=$EXTRASTOPS_LIST}
				{assign var=STOP_COUNT value=$STOP_INDEX+1}
				{assign var=DEFAULT_CHZN value=0}
				<tbody class="stopBlock">
					<tr class="fieldLabel" colspan="4">
						<td colspan="4" class="blockHeader">
							<img class="cursorPointer alignMiddle blockToggle stopToggle"  src="{vimage_path('arrowRight.png')}" data-mode="hide" data-id='stop{$CURRENT_STOP['extrastopsid']}'>
							<img class="cursorPointer alignMiddle blockToggle stopToggle hide"  src="{vimage_path('arrowDown.png')}" data-mode="show" data-id='stop{$CURRENT_STOP['extrastopsid']}'>
							<span class="stopTitle"><b>&nbsp;&nbsp;&nbsp;Stop {$STOP_INDEX+1}</b>
								{if $MODULE neq 'Estimates'}<a style="float: right; padding: 3px"><i title="Delete" class="deleteStopButton icon-trash"></i></a>{/if}
							</span>
							<input id="extrastops_id_{$STOP_COUNT}" type="hidden" name="extrastops_id_{$STOP_COUNT}" value="{$CURRENT_STOP['extrastopsid']}">
							<input id="extrastops_deleted" type="hidden" name="extrastops_deleted_{$STOP_COUNT}" value="none">
						</td>
					</tr>
					<tr colspan="4">
						<td colspan="4" style="padding: 0px;">
							<table class="table equalSplit table-bordered" style="padding: 0px; border: 0px;">
								<tbody class="stopContent hide">
									<tr>
										{assign var=COUNTER value=0}
										{foreach key=FIELD_NAME item=FIELD_MODEL from=$STOPS_BLOCK_FIELDS name=blockfields}
										{assign var=CUSTOM_FIELD_NAME value=$FIELD_NAME|cat:"_"|cat:$STOP_COUNT}
										{assign var=FIELD_MODEL value=$FIELD_MODEL->set('fieldvalue',$EXTRASTOPS_LIST[$STOP_INDEX][$FIELD_NAME])}
										{assign var=FIELD_MODEL value=$FIELD_MODEL->set('name',$CUSTOM_FIELD_NAME)}
										{assign var=FIELD_MODEL value=$FIELD_MODEL->set('noncustomname',$FIELD_NAME)}
										{assign var="isReferenceField" value=$FIELD_MODEL->getFieldDataType()}
										{if $isReferenceField eq 'reference' && count($FIELD_MODEL->getReferenceList()) < 1}{continue}{/if}
										{if $FIELD_MODEL->get('uitype') eq "20" or $FIELD_MODEL->get('uitype') eq "19"}
										{if $COUNTER eq '1'}
										<td class="{$WIDTHTYPE}"></td><td class="{$WIDTHTYPE}"></td>
									</tr>
									<tr>
										{assign var=COUNTER value=0}
										{/if}
										{/if}
										{if $COUNTER eq 2}
									</tr>
									<tr>
										{assign var=COUNTER value=1}
										{else}
										{assign var=COUNTER value=$COUNTER+1}
										{/if}
										<td class="fieldLabel {$WIDTHTYPE}">
											{if $isReferenceField neq "reference"}<label class="muted pull-right marginRight10px">{/if}
												{if $FIELD_MODEL->isMandatory() eq true && $isReferenceField neq "reference"} <span class="redColor">*</span> {/if}
												{if $isReferenceField eq "reference"}
													{assign var="REFERENCE_LIST" value=$FIELD_MODEL->getReferenceList()}
													{assign var="REFERENCE_LIST_COUNT" value=count($REFERENCE_LIST)}
													{if $REFERENCE_LIST_COUNT > 1}
														{assign var="DISPLAYID" value=$FIELD_MODEL->get('fieldvalue')}
														{assign var="REFERENCED_MODULE_STRUCT" value=$FIELD_MODEL->getUITypeModel()->getReferenceModule($DISPLAYID)}
														{if !empty($REFERENCED_MODULE_STRUCT)}
															{assign var="REFERENCED_MODULE_NAME" value=$REFERENCED_MODULE_STRUCT->get('name')}
														{/if}
														<span class="pull-right">
															{if $FIELD_MODEL->isMandatory() eq true} <span class="redColor">*</span> {/if}
															<select id="ExtraStops_editView_fieldName_{$FIELD_MODEL->getName()}_dropDown" class="chzn-select referenceModulesList streched" style="width:160px;">
																<optgroup>
																	{foreach key=index item=value from=$REFERENCE_LIST}
																		<option value="{$value}" {if $value eq $REFERENCED_MODULE_NAME} selected {/if}>{vtranslate($value, 'ExtraStops')}</option>
																	{/foreach}
																</optgroup>
															</select>
														</span>
													{else}
														<label class="muted pull-right marginRight10px">{if $FIELD_MODEL->isMandatory() eq true} <span class="redColor">*</span> {/if}{vtranslate($FIELD_MODEL->get('label'), 'ExtraStops')}</label>
													{/if}
												{elseif $FIELD_MODEL->get('uitype') eq "83"}
													{include file=vtemplate_path($FIELD_MODEL->getUITypeModel()->getTemplateName(),'ExtraStops') COUNTER=$COUNTER MODULE='ExtraStops'}
												{else}
													{vtranslate($FIELD_MODEL->get('label'), 'ExtraStops')}
												{/if}
												{if $isReferenceField neq "reference"}</label>{/if}
										</td>
										{if $FIELD_MODEL->get('uitype') neq "83"}
											<td class="fieldValue {$WIDTHTYPE}" {if $FIELD_MODEL->get('uitype') eq '19' or $FIELD_MODEL->get('uitype') eq '20'} colspan="3" {assign var=COUNTER value=$COUNTER+1} {/if}>
												<div class="row-fluid">
													<span class="span10">
														{include file=vtemplate_path($FIELD_MODEL->getUITypeModel()->getTemplateName(),'ExtraStops') STOPS_BLOCK_FIELDS=$STOPS_BLOCK_FIELDS}
													</span>
												</div>
											</td>
										{/if}
										{if $STOPS_BLOCK_FIELDS|@count eq 1 and $FIELD_MODEL->get('uitype') neq "19" and $FIELD_MODEL->get('uitype') neq "20" and $FIELD_MODEL->get('uitype') neq "30" and $FIELD_MODEL->get('name') neq "recurringtype"}
											<td class="{$WIDTHTYPE}"></td><td class="{$WIDTHTYPE}"></td>
										{/if}
										{/foreach}
										{if $STOPS_BLOCK_FIELDS|@end eq true and $STOPS_BLOCK_FIELDS|@count neq 1 and $COUNTER eq 1}
											<td class="fieldLabel {$WIDTHTYPE}"></td><td class="{$WIDTHTYPE}"></td>
										{/if}
									</tr>
								</tbody>
							</table>
						</td>
					</tr>
				</tbody>
			{/foreach}
		</table>
		<br>
	{/if}
{/strip}