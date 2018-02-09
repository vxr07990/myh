{strip}
	<table class="table table-bordered blockContainer showInlineTable equalSplit" name="EscrowsTable">
		<thead>
		<tr>
			<th class="blockHeader" colspan="4">{vtranslate('Escrows', 'Escrows')}</th>
		</tr>
		</thead>
	</table>
	<div class="EscrowsList" data-rel-module="Escrows">
		{foreach from=$ITEMCODES_MAPPING_LIST item=ITEMCODES_MAPPING_RECORD_MODEL name=related_records_block key = ITEMCODES_MAPPING_ID}
			{assign var=WIDTHTYPE value=$USER_MODEL->get('rowheight')}
			{assign var=FIELDS_LIST value=$ITEMCODES_MAPPING_BLOCK_FIELDS}
			<div class="EscrowsRecords"  data-row-no="{$ROWNO}" data-id = "{$ITEMCODES_MAPPING_ID}">
				<table class="table table-bordered equalSplit detailview-table">
					<thead>
					<tr>
						<th class="blockHeader" colspan="4">
							&nbsp;&nbsp;
							<img class="cursorPointer alignMiddle blockToggle {if !($IS_HIDDEN)} hide {/if} "  src="{vimage_path('arrowRight.png')}" data-mode="hide" data-id={$ITEMCODES_MAPPING_ID}>
							<img class="cursorPointer alignMiddle blockToggle {if ($IS_HIDDEN)} hide {/if}"  src="{vimage_path('arrowDown.png')}" data-mode="show" data-id={$ITEMCODES_MAPPING_ID}>
                            <span class="EscrowsTitle">&nbsp;&nbsp;
								{if $ITEMCODES_MAPPING_RECORD_MODEL->get('escrows_desc')}
									{$ITEMCODES_MAPPING_RECORD_MODEL->get('escrows_desc')}
								{else}
									Description of Escrow
								{/if}
                            </span>

						</th>
					</tr>
					</thead>
					<tbody>
					{assign var=COUNTER value=0}
					<tr>
						{foreach item=FIELD_MODEL key=FIELD_NAME from=$FIELDS_LIST}
						{if $FIELD_NAME eq 'escrows_agentcompgr'}{continue}{/if}
						{if $ITEMCODES_MAPPING_RECORD_MODEL}
							{assign var=FIELD_MODEL value=$FIELD_MODEL->set('fieldvalue',$ITEMCODES_MAPPING_RECORD_MODEL->get($FIELD_MODEL->getFieldName()))}
						{/if}
						{if !$FIELD_MODEL->isViewableInDetailView()}
							{continue}
						{/if}
						{if $FIELD_MODEL->get('uitype') eq "83"}
						{foreach item=tax key=count from=$TAXCLASS_DETAILS}
						{if $tax.check_value eq 1}
						{if $COUNTER eq 2}
					</tr><tr>
						{assign var="COUNTER" value=1}
						{else}
						{assign var="COUNTER" value=$COUNTER+1}
						{/if}
						<td class="fieldLabel {$WIDTHTYPE}">
							<label class='muted pull-right marginRight10px'>{vtranslate($tax.taxlabel, 'Escrows')}(%)</label>
						</td>
						<td class="fieldValue {$WIDTHTYPE}">
                             <span class="value">
                                 {$tax.percentage}
                             </span>
						</td>
						{/if}
						{/foreach}
						{else if $FIELD_MODEL->get('uitype') eq "69" || $FIELD_MODEL->get('uitype') eq "105"}
						{if $COUNTER neq 0}
						{if $COUNTER eq 2}
					</tr><tr>
						{assign var=COUNTER value=0}
						{/if}
						{/if}
						<td class="fieldLabel {$WIDTHTYPE}"><label class="muted pull-right marginRight10px">{vtranslate({$FIELD_MODEL->get('label')},'Escrows')}</label></td>
						<td class="fieldValue {$WIDTHTYPE}">
							<div id="imageContainer" width="300" height="200">
								{foreach key=ITER item=IMAGE_INFO from=$IMAGE_DETAILS}
									{if !empty($IMAGE_INFO.path) && !empty({$IMAGE_INFO.orgname})}
										<img src="{$IMAGE_INFO.path}_{$IMAGE_INFO.orgname}" width="300" height="200">
									{/if}
								{/foreach}
							</div>
						</td>
						{assign var=COUNTER value=$COUNTER+1}
						{else}
						{if $FIELD_MODEL->get('uitype') eq "20" or $FIELD_MODEL->get('uitype') eq "19"}
						{if $COUNTER eq '1'}
						<td class="{$WIDTHTYPE}"></td><td class="{$WIDTHTYPE}"></td></tr><tr>
						{assign var=COUNTER value=0}
						{/if}
						{/if}
						{if $COUNTER eq 2}
					</tr><tr>
						{assign var=COUNTER value=1}
						{else}
						{assign var=COUNTER value=$COUNTER+1}
						{/if}
						<td class="fieldLabel {$WIDTHTYPE}" id="Escrows_detailView_fieldLabel_{$FIELD_MODEL->getName()}" {if $FIELD_MODEL->getName() eq 'description' or $FIELD_MODEL->get('uitype') eq '69'} style='width:8%'{/if}>
							<label class="muted pull-right marginRight10px">
								{vtranslate({$FIELD_MODEL->get('label')},'Escrows')}
								{if ($FIELD_MODEL->get('uitype') eq '72') && ($FIELD_MODEL->getName() eq 'unit_price')}
									({$BASE_CURRENCY_SYMBOL})
								{/if}
							</label>
						</td>
						<td class="fieldValue {$WIDTHTYPE}" id="Escrows_detailView_fieldValue_{$FIELD_MODEL->getName()}" {if $FIELD_MODEL->get('uitype') eq '19' or $FIELD_MODEL->get('uitype') eq '20'} colspan="3" {assign var=COUNTER value=$COUNTER+1} {/if}>
                    <span class="value" data-field-type="{$FIELD_MODEL->getFieldDataType()}" {if $FIELD_MODEL->get('uitype') eq '19' or $FIELD_MODEL->get('uitype') eq '20' or $FIELD_MODEL->get('uitype') eq '21'} style="white-space:normal;" {/if}>
                        {include file=vtemplate_path($FIELD_MODEL->getUITypeModel()->getDetailViewTemplateName(),'Escrows') FIELD_MODEL=$FIELD_MODEL USER_MODEL=$USER_MODEL MODULE='Escrows' RECORD=$ITEMCODES_MAPPING_RECORD_MODEL}
                     </span>
							{if $FIELD_MODEL->isEditable() eq 'true' && ($FIELD_MODEL->getFieldDataType()!=Vtiger_Field_Model::REFERENCE_TYPE)}
								<span class="hide edit">
                            {if $ITEMCODES_MAPPING_RECORD_MODEL->get('isEvent') eq 1}
								{include file=vtemplate_path($FIELD_MODEL->getUITypeModel()->getTemplateName(),'Events') FIELD_MODEL=$FIELD_MODEL USER_MODEL=$USER_MODEL MODULE='Escrows' RECORD_STRUCTURE_MODEL = $RECORD_STRUCTURE_MODEL}
							{else}
								{include file=vtemplate_path($FIELD_MODEL->getUITypeModel()->getTemplateName(),'Escrows') FIELD_MODEL=$FIELD_MODEL USER_MODEL=$USER_MODEL MODULE='Escrows' RECORD_STRUCTURE_MODEL = $RECORD_STRUCTURE_MODEL}
							{/if}

									<br />
                            <a href="javascript:void(0);" data-field-name="{$FIELD_MODEL->getFieldName()}{if $FIELD_MODEL->get('uitype') eq '33'}[]{/if}" data-record-id="{$ITEMCODES_MAPPING_RECORD_MODEL->getId()}" data-rel-module="Escrows" class="hoverEditSave">{vtranslate('LBL_SAVE')}</a> |
                            <a href="javascript:void(0);" class="hoverEditCancel">{vtranslate('LBL_CANCEL')}</a>
                        </span>
							{/if}
						</td>
						{/if}

						{if $FIELDS_LIST|@count eq 1 and $FIELD_MODEL->get('uitype') neq "19" and $FIELD_MODEL->get('uitype') neq "20" and $FIELD_MODEL->get('uitype') neq "30" and $FIELD_MODEL->get('name') neq "recurringtype" and $FIELD_MODEL->get('uitype') neq "69" and $FIELD_MODEL->get('uitype') neq "105"}
							<td class="fieldLabel {$WIDTHTYPE}"></td><td class="{$WIDTHTYPE}"></td>
						{/if}
						{/foreach}
						{* adding additional column for odd number of fields in a block *}
						{if $FIELDS_LIST|@end eq true and $FIELDS_LIST|@count neq 1 and $COUNTER eq 1}
							<td class="fieldLabel {$WIDTHTYPE}"></td><td class="{$WIDTHTYPE}"></td>
						{/if}
					</tr>
					</tbody>
				</table>
			</div>
		{/foreach}
	</div>
{/strip}