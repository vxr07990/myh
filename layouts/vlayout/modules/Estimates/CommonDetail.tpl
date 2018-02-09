{strip}
    {foreach key=BLOCK_LABEL_KEY item=FIELD_MODEL_LIST from=$RECORD_STRUCTURE}
        {if $BLOCK_SUBLIST && !array_key_exists($BLOCK_LABEL_KEY, $BLOCK_SUBLIST)}
            {continue}
        {/if}
		{if $FIELD_MODEL_LIST|@count lte 0}{continue}{/if}
		{assign var=HAS_CONTENT value=(!$BLOCK_SUBLIST || $BLOCK_SUBLIST[$BLOCK_LABEL_KEY])}
		<div id="contentHolder_{$BLOCK_LABEL_KEY}" class="sectionContentHolder {$CONTENT_DIV_CLASS} {if !$ALWAYS_SHOW_CONTENT_DIV}hide{/if} {if !$HAS_CONTENT}inactive{/if}">
		{if $HAS_CONTENT}
			{assign var=WIDTHTYPE value=$USER_MODEL->get('rowheight')}
			<input type=hidden name="timeFormatOptions" data-value='{$DAY_STARTS}' />
			<table class="table table-bordered equalSplit detailview-table block_{$BLOCK_LABEL_KEY}">
				<thead>
				<tr>
						<th class="blockHeader" colspan="40">
								<img class="cursorPointer alignMiddle blockToggle {if !($IS_HIDDEN)} hide {/if} "  src="{vimage_path('arrowRight.png')}" data-mode="hide" data-id={$BLOCK_LIST[$BLOCK_LABEL_KEY]->get('id')}>
								<img class="cursorPointer alignMiddle blockToggle {if ($IS_HIDDEN)} hide {/if}"  src="{vimage_path('arrowDown.png')}" data-mode="show" data-id={$BLOCK_LIST[$BLOCK_LABEL_KEY]->get('id')}>
							&nbsp;&nbsp;{vtranslate({$BLOCK_LABEL_KEY},{$MODULE_NAME})}
						</th>
				</tr>
				</thead>
			 <tbody {if $IS_HIDDEN} class="hide" {/if}>
			{assign var=COUNTER value=0}
				 {if $BLOCK_LABEL_KEY == 'LBL_ESTIMATES_DATES'}
					 {assign var=COUNTER_LOOP value=3}
					 {assign var=LABEL_STYLE value="width: 14%"}
					 {assign var=VALUE_STYLE value="width: 19%"}
				 {else}
					 {assign var=COUNTER_LOOP value=2}
					 {assign var=LABEL_STYLE value=""}
					 {assign var=VALUE_STYLE value=""}
				 {/if}
				 {assign var=LABEL_ATTR value=" class=\"fieldLabel {$WIDTHTYPE}\" style=\"{$LABEL_STYLE}\" "}
				 {assign var=VALUE_ATTR value=" class=\"fieldValue {$WIDTHTYPE}\" style=\"{$VALUE_STYLE}\" "}
				 <tr>
			{foreach item=FIELD_MODEL key=FIELD_NAME from=$FIELD_MODEL_LIST}
					 <!-- {$FIELD_NAME} -->
					 {if $FIELD_NAME eq 'oi_push_notification_token'}
						 {if $IS_OI_ENABLED neq 1}
							 <!-- O&I DISABLED -->
							 {continue}
						 {/if}
					 {/if}
					 {if $FIELD_NAME eq 'dbx_token'}
					 {if $IS_OI_ENABLED neq 1}
					 <!-- O&I DISABLED -->
					 {continue}
					 {else}
					 <!-- O&I ENABLED -->
					 <!-- {$FIELD_MODEL->getDisplayValue($FIELD_MODEL->get('fieldvalue'))} -->
					 {if $FIELD_MODEL->getDisplayValue($FIELD_MODEL->get('fieldvalue')) eq ''}
					 <!-- No DBX Token set -->
					 {if $COUNTER eq $COUNTER_LOOP}
								</tr><tr>
				 {assign var="COUNTER" value=1}
				 {else}
				 {assign var="COUNTER" value=$COUNTER+1}
				 {/if}
				 <td {$LABEL_ATTR}>
							<label class='muted pull-right marginRight10px'>
								{vtranslate({$FIELD_MODEL->get('label')},{$MODULE_NAME})}
							</label>
							</td>
							<td {$VALUE_ATTR}>
							<span class="value" id="dropbox_auth_token">
								<button type="button" onclick="getDropboxAuth()">Get Dropbox Authorization Token</button>
							</span>
							</td>
				 {else}
				 <!-- DBX Token is set -->
				 {if $COUNTER eq $COUNTER_LOOP}
				 </tr><tr>
				 {assign var="COUNTER" value=1}
				 {else}
				 {assign var="COUNTER" value=$COUNTER+1}
				 {/if}
							<td {$LABEL_ATTR}>
							<label class='muted pull-right marginRight10px'>
								{vtranslate({$FIELD_MODEL->get('label')},{$MODULE_NAME})}
							</label>
							</td>
							<td {$VALUE_ATTR}>
							<span class="value" id="dropbox_auth_token">
								[hidden]
							</span>
							</td>
				 {/if}
				 {continue}
				 {/if}
				 {/if}
				 {if !$FIELD_MODEL->isViewableInDetailView()}
					 {continue}
				 {/if}
				 {if $FIELD_MODEL->get('uitype') eq "83"}
				 {foreach item=tax key=count from=$TAXCLASS_DETAILS}
				 {if $tax.check_value eq 1}
				 {if $COUNTER eq $COUNTER_LOOP}
				 </tr><tr>
				 {assign var="COUNTER" value=1}
				 {else}
				 {assign var="COUNTER" value=$COUNTER+1}
				 {/if}
						<td {$LABEL_ATTR}>
						<label class='muted pull-right marginRight10px'>{vtranslate($tax.taxlabel, $MODULE)}(%)</label>
						</td>
						 <td {$VALUE_ATTR}>
							 <span class="value">
								 {$tax.percentage}
							 </span>
						 </td>
				 {/if}
				 {/foreach}
				 {else if $FIELD_MODEL->get('uitype') eq "69" || $FIELD_MODEL->get('uitype') eq "105"}
				 {if $COUNTER neq 0}
				 {if $COUNTER eq $COUNTER_LOOP}
				 </tr><tr>
				 {assign var=COUNTER value=0}
				 {/if}
				 {/if}
				 <td {$LABEL_ATTR}><label class="muted pull-right marginRight10px">{vtranslate({$FIELD_MODEL->get('label')},{$MODULE_NAME})}</label></td>
					<td {$VALUE_ATTR}>
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
				 {if $COUNTER eq $COUNTER_LOOP}
				 </tr><tr>
						{assign var=COUNTER value=1}
					 {else}
					 {assign var=COUNTER value=$COUNTER+1}
					 {/if}
					 <td {$LABEL_ATTR} id="{$MODULE_NAME}_detailView_fieldLabel_{$FIELD_MODEL->getName()}" {if $FIELD_MODEL->getName() eq 'description' or $FIELD_MODEL->get('uitype') eq '69'} style='width:8%'{/if}>
						 <label class="muted pull-right marginRight10px">
							 {vtranslate({$FIELD_MODEL->get('label')},{$MODULE_NAME})}
							 {if ($FIELD_MODEL->get('uitype') eq '72') && ($FIELD_MODEL->getName() eq 'unit_price')}
								 ({$BASE_CURRENCY_SYMBOL})
							 {/if}
						 </label>
					 </td>
					 <td {$VALUE_ATTR} id="{$MODULE_NAME}_detailView_fieldValue_{$FIELD_MODEL->getName()}" {if $FIELD_MODEL->get('uitype') eq '19' or $FIELD_MODEL->get('uitype') eq '20'} colspan="3" {assign var=COUNTER value=$COUNTER+1} {/if}>
						 <span class="value" data-field-type="{$FIELD_MODEL->getFieldDataType()}" {if $FIELD_MODEL->get('uitype') eq '19' or $FIELD_MODEL->get('uitype') eq '20' or $FIELD_MODEL->get('uitype') eq '21'} style="white-space:normal;" {/if}>
							 {if $FIELD_MODEL->get('label') == 'LBL_QUOTES_EFFECTIVE_TARIFF'}
								 <input type="hidden" disabled id="allAvailableTariffs" value="{$AVAILABLE_TARIFFS}">
								 <input type="hidden" id="tariff_customjs" value="{$EFFECTIVE_TARIFF_CUSTOMJS}">
								 <div id="_fieldValue_effective_tariff_custom_type" class="hide">
									 <span class="hide value">
										 {$EFFECTIVE_TARIFF_CUSTOMTYPE}
								 		 <input type="hidden" id="effective_tariff_custom_type" name="effective_tariff_custom_type" value="{$EFFECTIVE_TARIFF_CUSTOMTYPE}" data-tariffid="{$EFFECTIVE_TARIFF}">
										 <input type="hidden" id="isLocalRating" name="isLocalRating" value="{$IS_LOCAL_RATING}">
									 </span>
								 </div>
							 {/if}
							{include file=vtemplate_path($FIELD_MODEL->getUITypeModel()->getDetailViewTemplateName(),$MODULE_NAME) FIELD_MODEL=$FIELD_MODEL USER_MODEL=$USER_MODEL MODULE=$MODULE_NAME RECORD=$RECORD}
						 </span>
						 {if $IS_AJAX_ENABLED && $FIELD_MODEL->isEditable() eq 'true' && ($FIELD_MODEL->getFieldDataType()!=Vtiger_Field_Model::REFERENCE_TYPE) && $FIELD_MODEL->isAjaxEditable() eq 'true'}{* && $CREATOR_PERMISSIONS eq 'true'*}
							 <span class="hide edit">
								 {include file=vtemplate_path($FIELD_MODEL->getUITypeModel()->getTemplateName(),$MODULE_NAME) FIELD_MODEL=$FIELD_MODEL USER_MODEL=$USER_MODEL MODULE=$MODULE_NAME}
								 {if $FIELD_MODEL->getFieldDataType() eq 'multipicklist'}
									 <input type="hidden" class="fieldname" value='{$FIELD_MODEL->get('name')}[]' data-prev-value='{$FIELD_MODEL->getDisplayValue($FIELD_MODEL->get('fieldvalue'))}' />
								 {else}
									 <input type="hidden" class="fieldname" value='{$FIELD_MODEL->get('name')}' data-prev-value='{Vtiger_Util_Helper::toSafeHTML($FIELD_MODEL->getDisplayValue($FIELD_MODEL->get('fieldvalue')))}' />
								 {/if}
							 </span>
						 {/if}
					 </td>
					 {/if}

					 {if $FIELD_MODEL_LIST|@count eq 1 and $FIELD_MODEL->get('uitype') neq "19" and $FIELD_MODEL->get('uitype') neq "20" and $FIELD_MODEL->get('uitype') neq "30" and $FIELD_MODEL->get('name') neq "recurringtype" and $FIELD_MODEL->get('uitype') neq "69" and $FIELD_MODEL->get('uitype') neq "105"}
						 <td {$LABEL_ATTR}></td><td {$VALUE_ATTR}></td>
					 {/if}
					 {/foreach}
					 {* adding additional column for odd number of fields in a block *}
					 {while $FIELD_MODEL_LIST|@end eq true and $FIELD_MODEL_LIST|@count neq 1 and $COUNTER < $COUNTER_LOOP}
						 <td {$LABEL_ATTR}></td><td {$VALUE_ATTR}></td>
					 	{assign var=COUNTER value=$COUNTER+1}
					 {/while}
			</tr>
				 {if $BLOCK_LABEL_KEY == 'LBL_QUOTES_INTERSTATEMOVEDETAILS'}
					 <tr><td class='fieldLabel'></td>
						<td class='fieldValue'>
							{* Apparently detail view rating is not set up to do requotes... hiding for now *}
							{if getenv('INSTANCE_NAME') eq 'sirva' && false}&nbsp;
								<button type='button' class='requote'>Re-Quote</button>
							{else}&nbsp;<!--<button type='button' id='interstateRateQuick'>Quick Rate Estimate</button>-->{/if}
						</td>
						<td class='fieldLabel'></td>
						<td class='fieldValue'>
							{if !$LOCK_RATING}
								<button type='button' class='interstateRateDetail'>{vtranslate('LBL_DETAILED_RATE_ESTIMATE',$MODULE_NAME)}</button>
							{/if}
						</td>
					</tr>
				 {/if}
			</tbody>
		</table>
			<br>
		{/if}
		</div>
		{include file=vtemplate_path('SequencedGuestDetailBlocks.tpl', $MODULE) BLOCK_LABEL=$BLOCK_LABEL_KEY}
	{/foreach}
		{include file=vtemplate_path('GuestDetailBlocks.tpl', $MODULE_NAME)}
{/strip}
