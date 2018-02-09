{strip}
	{if $IS_ACTIVE_COMMISSIONPLANSITEM}
		<table class="table table-bordered blockContainer showInlineTable equalSplit" name="commissionPlansItemTable">
			<thead>
			<tr>
				<th class="blockHeader" colspan="4">{vtranslate('CommissionPlansItem', 'CommissionPlansItem')}</th>
			</tr>
			</thead>
			<tbody>
			<tr class="fieldLabel" >
				<td colspan="4">
					<button type="button" class="addItem" name="addItem">+</button>
					<input type="hidden" id="numItems" name="numItems" value="{$COMMISSIONPLANITEMS_LIST|@count}">
					<button type="button" class="addItem" name="addItem2" style="clear:right;float:right">+</button>
				</td>
			</tr>
			</tbody>
			
			{*hidden, default item*}
			<tbody class="defaultItem itemBlock hide">
			<tr class="fieldLabel" colspan="4">
				<td colspan="4" class="blockHeader">
					{*<img class="cursorPointer alignMiddle blockToggle itemToggle{if !($IS_HIDDEN)} hide {/if} "  src="{vimage_path('arrowRight.png')}" data-mode="hide" data-id=''>*}
					{*<img class="cursorPointer alignMiddle blockToggle itemToggle{if ($IS_HIDDEN)} hide {/if}"  src="{vimage_path('arrowDown.png')}" data-mode="show" data-id=''>*}
					<span class="itemTitle"><b>&nbsp;&nbsp;&nbsp;Description of Item</b></span>
					<a style="float: right; padding: 3px"><i title="Delete" class="deleteItemButton icon-trash"></i></a>
					<a style="float: right; padding: 3px"><i title="Copy" class="copyItemButton"><img src="layouts/vlayout/modules/CommissionPlansItem/resources/copy-icon.png" width="14" height="14"></i></a>
				</td>
			</tr>
			<tr style="padding: 0px;">
				<td style="padding: 0px;">
					<table class="table equalSplit table-bordered" style="padding: 0px; border: 0px;">
						<tbody class="itemContent defaultItemContent hide">
						<tr>
							{assign var=DEFAULT_CHZN value=1}
							{assign var=COUNTER value=0}
							{foreach key=FIELD_NAME item=FIELD_MODEL from=$COMMISSIONPLANITEMS_BLOCK_FIELDS name=blockfields}
							{assign var="isReferenceField" value=$FIELD_MODEL->getFieldDataType()}
							{assign var=FIELD_MODEL value=$FIELD_MODEL->set('fieldvalue',$FIELD_MODEL->getDefaultFieldValue())}
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
												<select id="CommissionPlansItem_editView_fieldName_{$FIELD_MODEL->getName()}_dropDown" class="chzn-select referenceModulesList streched" style="width:160px;">
													<optgroup>
														{foreach key=index item=value from=$REFERENCE_LIST}
															<option value="{$value}" {if $value eq $REFERENCED_MODULE_NAME} selected {/if}>{vtranslate($value, 'CommissionPlansItem')}</option>
														{/foreach}
													</optgroup>
												</select>
													</span>
										{else}
											<label class="muted pull-right marginRight10px">{if $FIELD_MODEL->isMandatory() eq true} <span class="redColor">*</span> {/if}{vtranslate($FIELD_MODEL->get('label'), 'CommissionPlansItem')}</label>
										{/if}
									{elseif $FIELD_MODEL->get('uitype') eq "83"}
										{include file=vtemplate_path($FIELD_MODEL->getUITypeModel()->getTemplateName(),'CommissionPlansItem') COUNTER=$COUNTER MODULE='CommissionPlansItem'}
									{else}
										{vtranslate($FIELD_MODEL->get('label'), 'CommissionPlansItem')}
									{/if}
									{if $isReferenceField neq "reference"}</label>{/if}
							</td>
							{if $FIELD_MODEL->get('uitype') neq "83"}
								<td class="fieldValue {$WIDTHTYPE}" {if $FIELD_MODEL->get('uitype') eq '19' or $FIELD_MODEL->get('uitype') eq '20'} colspan="3" {assign var=COUNTER value=$COUNTER+1} {/if}>
									<div class="row-fluid">
												<span class="span10">
													{include file=vtemplate_path($FIELD_MODEL->getUITypeModel()->getTemplateName(),'CommissionPlansItem') COMMISSIONPLANITEMS_BLOCK_FIELDS=$COMMISSIONPLANITEMS_BLOCK_FIELDS}
												</span>
									</div>
								</td>
							{/if}
							{if $COMMISSIONPLANITEMS_BLOCK_FIELDS|@count eq 1 and $FIELD_MODEL->get('uitype') neq "19" and $FIELD_MODEL->get('uitype') neq "20" and $FIELD_MODEL->get('uitype') neq "30" and $FIELD_MODEL->get('name') neq "recurringtype"}
								<td class="{$WIDTHTYPE}"></td><td class="{$WIDTHTYPE}"></td>
							{/if}
							{/foreach}
							{* adding additional column for odd number of fields in a block *}
							{if $COMMISSIONPLANITEMS_BLOCK_FIELDS|@end eq true and $COMMISSIONPLANITEMS_BLOCK_FIELDS|@count neq 1 and $COUNTER eq 1}
								<td class="fieldLabel {$WIDTHTYPE}"></td><td class="{$WIDTHTYPE}"></td>
							{/if}
						</tr>
						</tbody>
					</table>
				</td>
			</tr>
			</tbody>
			{foreach key=ITEM_INDEX item=CURRENT_ITEM from=$COMMISSIONPLANITEMS_LIST}
				{assign var=ITEM_COUNT value=$ITEM_INDEX+1}
				{assign var=DEFAULT_CHZN value=0}
				<tbody class="itemBlock">
				<tr class="fieldLabel" colspan="4">
					<td colspan="4" class="blockHeader">
						{*<img class="cursorPointer alignMiddle blockToggle itemToggle"  src="{vimage_path('arrowRight.png')}" data-mode="hide" data-id='item{$CURRENT_ITEM['CommissionPlansItemid']}'>*}
						{*<img class="cursorPointer alignMiddle blockToggle itemToggle hide"  src="{vimage_path('arrowDown.png')}" data-mode="show" data-id='item{$CURRENT_ITEM['CommissionPlansItemid']}'>*}
						<span class="itemTitle " style=""><b>&nbsp;&nbsp;&nbsp; {$CURRENT_ITEM['commissiontype']}</b></span>
						<a style="float: right; padding: 3px"><i title="Delete" class="deleteItemButton icon-trash"></i></a>
						<a style="float: right; padding: 3px"><i title="Copy" class="copyItemButton" data-seq = '{$ITEM_COUNT}'><img src="layouts/vlayout/modules/CommissionPlansItem/resources/copy-icon.png" width="14" height="14"></i></a>
						<input id="CommissionPlansItem_id_{$ITEM_COUNT}" type="hidden" name="CommissionPlansItem_id_{$ITEM_COUNT}" value="{$CURRENT_ITEM['commissionplansitemid']}">
						<input id="CommissionPlansItem_deleted" type="hidden" name="CommissionPlansItem_deleted_{$ITEM_COUNT}" value="none">
					</td>
				</tr>
				<tr colspan="4">
					<td colspan="4" style="padding: 0px;">
						<table class="table equalSplit table-bordered" style="padding: 0px; border: 0px;">
							<tbody class="itemContent">
							<tr>
								{assign var=COUNTER value=0}
								{foreach key=FIELD_NAME item=FIELD_MODEL from=$COMMISSIONPLANITEMS_BLOCK_FIELDS name=blockfields}
								{assign var=CUSTOM_FIELD_NAME value=$FIELD_NAME|cat:"_"|cat:$ITEM_COUNT}
								{assign var=FIELD_MODEL value=$FIELD_MODEL->set('fieldvalue',$COMMISSIONPLANITEMS_LIST[$ITEM_INDEX][$FIELD_NAME])}
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
													<select id="CommissionPlansItem_editView_fieldName_{$FIELD_MODEL->getName()}_dropDown" class="chzn-select referenceModulesList streched" style="width:160px;">
														<optgroup>
															{foreach key=index item=value from=$REFERENCE_LIST}
																<option value="{$value}" {if $value eq $REFERENCED_MODULE_NAME} selected {/if}>{vtranslate($value, 'CommissionPlansItem')}</option>
															{/foreach}
														</optgroup>
													</select>
													</span>
											{else}
												<label class="muted pull-right marginRight10px">{if $FIELD_MODEL->isMandatory() eq true} <span class="redColor">*</span> {/if}{vtranslate($FIELD_MODEL->get('label'), 'CommissionPlansItem')}</label>
											{/if}
										{elseif $FIELD_MODEL->get('uitype') eq "83"}
											{include file=vtemplate_path($FIELD_MODEL->getUITypeModel()->getTemplateName(),'CommissionPlansItem') COUNTER=$COUNTER MODULE='CommissionPlansItem'}
										{else}
											{vtranslate($FIELD_MODEL->get('label'), 'CommissionPlansItem')}
										{/if}
										{if $isReferenceField neq "reference"}</label>{/if}
								</td>
								{if $FIELD_MODEL->get('uitype') neq "83"}
									<td class="fieldValue {$WIDTHTYPE}" {if $FIELD_MODEL->get('uitype') eq '19' or $FIELD_MODEL->get('uitype') eq '20'} colspan="3" {assign var=COUNTER value=$COUNTER+1} {/if}>
										<div class="row-fluid">
											<span class="span10">
												{include file=vtemplate_path($FIELD_MODEL->getUITypeModel()->getTemplateName(),'CommissionPlansItem') COMMISSIONPLANITEMS_BLOCK_FIELDS=$COMMISSIONPLANITEMS_BLOCK_FIELDS}
											</span>
										</div>
									</td>
								{/if}
								{if $COMMISSIONPLANITEMS_BLOCK_FIELDS|@count eq 1 and $FIELD_MODEL->get('uitype') neq "19" and $FIELD_MODEL->get('uitype') neq "20" and $FIELD_MODEL->get('uitype') neq "30" and $FIELD_MODEL->get('name') neq "recurringtype"}
									<td class="{$WIDTHTYPE}"></td><td class="{$WIDTHTYPE}"></td>
								{/if}
								{/foreach}
								{if $COMMISSIONPLANITEMS_BLOCK_FIELDS|@end eq true and $COMMISSIONPLANITEMS_BLOCK_FIELDS|@count neq 1 and $COUNTER eq 1}
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