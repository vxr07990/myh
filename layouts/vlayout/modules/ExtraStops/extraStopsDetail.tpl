{*<!--
/*********************************************************************************
** The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
*
 ********************************************************************************/
-->*}
{strip}
	{if $EXTRA_STOPS && $EXTRASTOPS_LIST|@count gt 0}
		{assign var=WIDTHTYPE value=$USER_MODEL->get('rowheight')}
		<input type=hidden name="timeFormatOptions" data-value='{$DAY_STARTS}' />
		<table class="table table-bordered equalSplit detailview-table">
			<thead>
			<tr>
				<th class="blockHeader" colspan="4">
					<img class="cursorPointer alignMiddle blockToggle {if !($IS_HIDDEN)} hide {/if} "  src="{vimage_path('arrowRight.png')}" data-mode="hide" data-id='Extra Stops'>{* Block-ID workaround *}
					<img class="cursorPointer alignMiddle blockToggle {if ($IS_HIDDEN)} hide {/if}"  src="{vimage_path('arrowDown.png')}" data-mode="show" data-id='Extra Stops'>
					&nbsp;&nbsp;{vtranslate({$STOPS_BLOCK_LABEL},'ExtraStops')}
				</th>
			</tr>
			</thead>
			{foreach key=STOP_INDEX item=CURRENT_STOP from=$EXTRASTOPS_LIST}
				{assign var=STOP_COUNT value=$STOP_INDEX+1}
				<tbody class="stopBlock">
					<tr colspan="4" style="padding: 0px;">
						<td colspan="4" style="padding: 0px;">
							<table class="table equalSplit table-bordered" style="padding: 0px; border: 0px;">
								<tbody class="stopContent">
								<tr>
									<td colspan="4" class="blockheader" style="background-color:#E8E8E8;">
										{*<img class="cursorPointer alignMiddle blockToggle stopToggle"  src="{vimage_path('arrowRight.png')}" data-mode="hide" data-id='stop{$CURRENT_STOP['extrastopsid']}'>
										<img class="cursorPointer alignMiddle blockToggle stopToggle hide"  src="{vimage_path('arrowDown.png')}" data-mode="show" data-id='stop{$CURRENT_STOP['extrastopsid']}'>*}
										<span class="stopTitle"><b>&nbsp;&nbsp;&nbsp;Stop {$STOP_INDEX+1}</b></span>
									</td>
								</tr>
								{assign var=COUNTER value=0}
								<tr>
									{foreach item=FIELD_MODEL key=FIELD_NAME from=$STOPS_BLOCK_FIELDS}
									{assign var=CUSTOM_FIELD_NAME value=$FIELD_NAME|cat:"_"|cat:$STOP_COUNT}
									{assign var=FIELD_MODEL value=$FIELD_MODEL->set('fieldvalue',$EXTRASTOPS_LIST[$STOP_INDEX][$FIELD_NAME])}
									{assign var=FIELD_MODEL value=$FIELD_MODEL->set('name',$CUSTOM_FIELD_NAME)}
									{assign var=FIELD_MODEL value=$FIELD_MODEL->set('noncustomname',$FIELD_NAME)}
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
									{if $COUNTER eq 2}
								</tr><tr>
									{assign var="COUNTER" value=1}
									{else}
									{assign var="COUNTER" value=$COUNTER+1}
									{/if}
									<td class="fieldLabel {$WIDTHTYPE}">
										<label class='muted pull-right marginRight10px'>
											{vtranslate({$FIELD_MODEL->get('label')},'ExtraStops')}
										</label>
									</td>
									<td class="fieldValue {$WIDTHTYPE}">
														<span class="value" id="dropbox_auth_token">
															<button type="button" onclick="getDropboxAuth()">Get Dropbox Authorization Token</button>
														</span>
									</td>
									{else}
									<!-- DBX Token is set -->
									{if $COUNTER eq 2}
								</tr><tr>
									{assign var="COUNTER" value=1}
									{else}
									{assign var="COUNTER" value=$COUNTER+1}
									{/if}
									<td class="fieldLabel {$WIDTHTYPE}">
										<label class='muted pull-right marginRight10px'>
											{vtranslate({$FIELD_MODEL->get('label')},'ExtraStops')}
										</label>
									</td>
									<td class="fieldValue {$WIDTHTYPE}">
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
									{if $COUNTER eq 2}
								</tr><tr>
									{assign var="COUNTER" value=1}
									{else}
									{assign var="COUNTER" value=$COUNTER+1}
									{/if}
									<td class="fieldLabel {$WIDTHTYPE}">
										<label class='muted pull-right marginRight10px'>{vtranslate($tax.taxlabel, 'ExtraStops')}(%)</label>
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
									<td class="fieldLabel {$WIDTHTYPE}"><label class="muted pull-right marginRight10px">{vtranslate({$FIELD_MODEL->get('label')},'ExtraStops')}</label></td>
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
									<td class="fieldLabel {$WIDTHTYPE}" id="{$MODULE_NAME}_detailView_fieldLabel_{$FIELD_MODEL->getName()}" {if $FIELD_MODEL->getName() eq 'description' or $FIELD_MODEL->get('uitype') eq '69'} style='width:8%'{/if}>
										<label class="muted pull-right marginRight10px">
											{vtranslate({$FIELD_MODEL->get('label')},'ExtraStops')}
											{if ($FIELD_MODEL->get('uitype') eq '72') && ($FIELD_MODEL->getName() eq 'unit_price')}
												({$BASE_CURRENCY_SYMBOL})
											{/if}
										</label>
									</td>
									<td class="fieldValue {$WIDTHTYPE}" id="{$MODULE_NAME}_detailView_fieldValue_{$FIELD_MODEL->getName()}" {if $FIELD_MODEL->get('uitype') eq '19' or $FIELD_MODEL->get('uitype') eq '20'} colspan="3" {assign var=COUNTER value=$COUNTER+1} {/if}>
													 <span class="value" data-field-type="{$FIELD_MODEL->getFieldDataType()}" {if $FIELD_MODEL->get('uitype') eq '19' or $FIELD_MODEL->get('uitype') eq '20' or $FIELD_MODEL->get('uitype') eq '21'} style="white-space:normal;" {/if}>
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

									{if $STOPS_BLOCK_FIELDS|@count eq 1 and $FIELD_MODEL->get('uitype') neq "19" and $FIELD_MODEL->get('uitype') neq "20" and $FIELD_MODEL->get('uitype') neq "30" and $FIELD_MODEL->get('name') neq "recurringtype" and $FIELD_MODEL->get('uitype') neq "69" and $FIELD_MODEL->get('uitype') neq "105"}
										<td class="fieldLabel {$WIDTHTYPE}"></td><td class="{$WIDTHTYPE}"></td>
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
			{/foreach}
		</table>
		<br>
	{/if}
{/strip}