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
	{foreach key=BLOCK_LABEL_KEY item=FIELD_MODEL_LIST from=$RECORD_STRUCTURE}
		{assign var=BLOCK value=$BLOCK_LIST[$BLOCK_LABEL_KEY]}
		{if $BLOCK eq null or $FIELD_MODEL_LIST|@count lte 0}
			{continue}
		{/if}
		{assign var=IS_HIDDEN value=$BLOCK->isHidden()}
		{assign var=WIDTHTYPE value=$USER_MODEL->get('rowheight')}
		<input type=hidden name="timeFormatOptions" data-value='{$DAY_STARTS}' />
		<table class="table table-bordered equalSplit detailview-table {if $BLOCK->get('hideblock') eq true}hide{/if}">
			<thead>
				<tr>
					<th class="blockHeader" colspan="4">
						<img class="cursorPointer alignMiddle blockToggle {if !($IS_HIDDEN)} hide {/if} "  src="{vimage_path('arrowRight.png')}" data-mode="hide" data-id={$BLOCK_LIST[$BLOCK_LABEL_KEY]->get('id')}>
						<img class="cursorPointer alignMiddle blockToggle {if ($IS_HIDDEN)} hide {/if}"  src="{vimage_path('arrowDown.png')}" data-mode="show" data-id={$BLOCK_LIST[$BLOCK_LABEL_KEY]->get('id')}>
						&nbsp;&nbsp;{vtranslate({$BLOCK_LABEL_KEY},{$MODULE_NAME})}
					</th>
				</tr>
			</thead>
			<tbody {if $IS_HIDDEN} class="hide" {/if}>
			{assign var=COUNTER value=0}
				<tr>
					{foreach item=FIELD_MODEL key=FIELD_NAME from=$FIELD_MODEL_LIST}
						{if $FIELD_NAME eq 'potential_id'}
							{continue}
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
										<label class='muted pull-right marginRight10px'>{vtranslate($tax.taxlabel, $MODULE)}(%)</label>
									</td>
									<td class="fieldValue {$WIDTHTYPE}">
										<span class="value">
											{$tax.percentage}
										</span>
									</td>
								{/if}
							{/foreach}
						{elseif $FIELD_MODEL->get('uitype') eq "69" || $FIELD_MODEL->get('uitype') eq "105"}
							{if $COUNTER neq 0}
								{if $COUNTER eq 2}
									</tr><tr>
									{assign var=COUNTER value=0}
								{/if}
							{/if}
							<td class="fieldLabel {$WIDTHTYPE}"><label class="muted pull-right marginRight10px">{vtranslate({$FIELD_MODEL->get('label')},{$MODULE_NAME})}</label></td>
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
									{vtranslate({$FIELD_MODEL->get('label')},{$MODULE_NAME})}
									{if ($FIELD_MODEL->get('uitype') eq '72') && ($FIELD_MODEL->getName() eq 'unit_price')}
										({$BASE_CURRENCY_SYMBOL})
									{/if}
								</label>
							</td>
							<td class="fieldValue {$WIDTHTYPE}" id="{$MODULE_NAME}_detailView_fieldValue_{$FIELD_MODEL->getName()}" {if $FIELD_MODEL->get('uitype') eq '19' or $FIELD_MODEL->get('uitype') eq '20'} colspan="3" {assign var=COUNTER value=$COUNTER+1} {/if}>
								<span class="value" data-field-type="{$FIELD_MODEL->getFieldDataType()}" {if $FIELD_MODEL->get('uitype') eq '19' or $FIELD_MODEL->get('uitype') eq '20' or $FIELD_MODEL->get('uitype') eq '21'} style="white-space:normal;" {/if}>
									{include file=vtemplate_path($FIELD_MODEL->getUITypeModel()->getDetailViewTemplateName(),$MODULE_NAME) FIELD_MODEL=$FIELD_MODEL USER_MODEL=$USER_MODEL MODULE=$MODULE_NAME RECORD=$RECORD}
								</span>
								{if $IS_AJAX_ENABLED && $FIELD_MODEL->isEditable() eq 'true' && ($FIELD_MODEL->getFieldDataType()!=Vtiger_Field_Model::REFERENCE_TYPE) && $FIELD_MODEL->isAjaxEditable() eq 'true'}{*OLD SECURITIES && $CREATOR_PERMISSIONS eq 'true'*}
									<span class="hide edit">
										{include file=vtemplate_path($FIELD_MODEL->getUITypeModel()->getTemplateName(),$MODULE_NAME) FIELD_MODEL=$FIELD_MODEL USER_MODEL=$USER_MODEL MODULE=$MODULE_NAME}
										{if $FIELD_MODEL->getFieldDataType() eq 'multipicklist' || $FIELD_MODEL->getFieldDataType() eq 'multiagent'}
											<input type="hidden" class="fieldname" value='{$FIELD_MODEL->get('name')}[]' data-prev-value='{$FIELD_MODEL->getDisplayValue($FIELD_MODEL->get('fieldvalue'))}' />
										{else}
											<input type="hidden" class="fieldname" value='{$FIELD_MODEL->get('name')}' data-prev-value='{Vtiger_Util_Helper::toSafeHTML($FIELD_MODEL->getDisplayValue($FIELD_MODEL->get('fieldvalue')))}' />
										{/if}
									</span>
								{/if}
							</td>
						{/if}
						{if $FIELD_MODEL_LIST|@count eq 1 and $FIELD_MODEL->get('uitype') neq "19" and $FIELD_MODEL->get('uitype') neq "20" and $FIELD_MODEL->get('uitype') neq "30" and $FIELD_MODEL->get('name') neq "recurringtype" and $FIELD_MODEL->get('uitype') neq "69" and $FIELD_MODEL->get('uitype') neq "105"}
							<td class="fieldLabel {$WIDTHTYPE}"></td><td class="{$WIDTHTYPE}"></td>
						{/if}
					{/foreach}
					{* adding additional column for odd number of fields in a block *}
					{if $FIELD_MODEL_LIST|@end eq true and $FIELD_MODEL_LIST|@count neq 1 and $COUNTER eq 1}
						<td class="fieldLabel {$WIDTHTYPE}">
						</td>
						<td class="{$WIDTHTYPE}">
						</td>
					{/if}
				</tr>
			</tbody>
		</table>
		{if not $BLOCK->get('hideblock')}
			<br>
		{/if}
	{/foreach}
    <div class="sectionsContainer">
        {foreach item=SECTION from=$OPLIST_ARRAY['sections']}
            <table name="opListSectionBlock_{$SECTION['section_id']}" class="table table-bordered equalSplit detailview-table">
                <thead>
                    <tr>
                        <th class="blockHeader" colspan="4">
                            <img class="cursorPointer alignMiddle blockToggle {if !($IS_HIDDEN)} hide {/if} "  src="{vimage_path('arrowRight.png')}" data-mode="hide" data-id=1337{$SECTION['section_id']}>
                            <img class="cursorPointer alignMiddle blockToggle {if ($IS_HIDDEN)} hide {/if}"  src="{vimage_path('arrowDown.png')}" data-mode="show" data-id=1337{$SECTION['section_id']}>
                            &nbsp;&nbsp;{$SECTION['section_name']}
                        </th>
                    </tr>
                </thead>
                <tbody class="opListSection_{$SECTION['section_id']}">
                {foreach item=QUESTION from=$SECTION['questions']}
                    <tr class="question_{$QUESTION['section_id']}_{$QUESTION['question_id']}">
                        <td class='fieldLabel {$WIDTHTYPE}'>
                            <label class='muted pull-right marginRight10px'>{vtranslate('LBL_OPLIST_QUESTION_TYPE', $MODULE)}</label>
                        </td>
                        <td class='fieldValue {$WIDTHTYPE}'>
                            <div class='row-fluid'>
                                <span class="value">
                                    {if $QUESTION['question_type'] === "Text"}{vtranslate('LBL_OPLIST_TEXT', $MODULE)}
                                    {elseif $QUESTION['question_type'] === "Yes/No"}{vtranslate('LBL_OPLIST_YESNO', $MODULE)}
                                    {elseif $QUESTION['question_type'] === "Date"}{vtranslate('LBL_OPLIST_DATE', $MODULE)}
                                    {elseif $QUESTION['question_type'] === "Date and Time"}{vtranslate('LBL_OPLIST_DATETIME', $MODULE)}
                                    {elseif $QUESTION['question_type'] === "Time"}{vtranslate('LBL_OPLIST_TIME', $MODULE)}
                                    {elseif $QUESTION['question_type'] === "Quantity"}{vtranslate('LBL_OPLIST_QUANTITY', $MODULE)}
                                    {elseif $QUESTION['question_type'] === "Multiple Choice"}{vtranslate('LBL_OPLIST_MULTIPLECHOICE', $MODULE)}{/if}
                                </span>
                            </div>
                        </td>
                        <td class='fieldLabel {$WIDTHTYPE}'>
                            &nbsp;
                        </td>
                        <td class='fieldValue {$WIDTHTYPE}'>
                            &nbsp;
                        </td>
                    </tr>
                    <tr class="question_{$QUESTION['section_id']}_{$QUESTION['question_id']}">
                        <td class='fieldLabel {$WIDTHTYPE}'>
                            <label class='muted pull-right marginRight10px'>{vtranslate('LBL_OPLIST_QUESTION', $MODULE)}</label>
                        </td>
                        <td class="fieldValue {$WIDTHTYPE}" colspan="3" style="width:80%;" >
                            <span class="value">
                                {$QUESTION['question']}
                            </span>
                        </td>
                    </tr>
                    {if $QUESTION['question_type'] === 'Text'}
                        <tr class="answer_{$QUESTION['section_id']}_{$QUESTION['question_id']}_text">
                            <td class='fieldLabel {$WIDTHTYPE}'>
                                <label class='muted pull-right marginRight10px'>{vtranslate('LBL_OPLIST_DEFAULTANSWER', $MODULE)}</label>
                            </td>
                            <td class="fieldValue {$WIDTHTYPE}" colspan="3" style="width:80%;" >
                                <span class="value">
                                    {$QUESTION['text_answer']}
                                </span>
                            </td>
                        </tr>
                    {elseif $QUESTION['question_type'] === 'Yes/No'}
                        <tr class="answer_{$QUESTION['section_id']}_{$QUESTION['question_id']}_bool">
                            <td class='fieldLabel {$WIDTHTYPE}'>
                                <label class='muted pull-right marginRight10px'>{vtranslate('LBL_OPLIST_DEFAULTANSWER', $MODULE)}</label>
                            </td>
                            <td class="fieldValue {$WIDTHTYPE}">
                                <span class="value">
                                    {if $QUESTION['bool_answer'] == 1}{vtranslate('LBL_YES', $MODULE)}{else}{vtranslate('LBL_NO', $MODULE)}{/if}
                                </span>
                            </td>
                            <td class='fieldLabel {$WIDTHTYPE}'>
                                &nbsp;
                            </td>
                            <td class="fieldValue {$WIDTHTYPE}">
                                &nbsp;
                            </td>
                        </tr>
                    {elseif $QUESTION['question_type'] === 'Date'}
                        <tr class="answer_{$QUESTION['section_id']}_{$QUESTION['question_id']}_date">
                            <td class='fieldLabel {$WIDTHTYPE}'>
                                <label class='muted pull-right marginRight10px'>{vtranslate('LBL_OPLIST_DEFAULTANSWER', $MODULE)}</label>
                            </td>
                            <td class="fieldValue {$WIDTHTYPE}">
                                <span class="value">
                                    {$QUESTION['date_answer']}
                                </span>
                            </td>
                            <td class='fieldLabel {$WIDTHTYPE}'>
                                &nbsp;
                            </td>
                            <td class="fieldValue {$WIDTHTYPE}">
                                &nbsp;
                            </td>
                        </tr>
                    {elseif $QUESTION['question_type'] === 'Date and Time'}
                        <tr class="answer_{$QUESTION['section_id']}_{$QUESTION['question_id']}_datetime">
                            <td class='fieldLabel {$WIDTHTYPE}'>
                                <label class='muted pull-right marginRight10px'>{vtranslate('LBL_OPLIST_DEFAULTANSWERDATE', $MODULE)}</label>
                            </td>
                            <td class="fieldValue {$WIDTHTYPE}">
                                <span class="value">
                                    {$QUESTION['datetime_answer_date']}
                                </span>
                            </td>
                            <td class='fieldLabel {$WIDTHTYPE}'>
                                <label class='muted pull-right marginRight10px'>{vtranslate('LBL_OPLIST_DEFAULTANSWERTIME', $MODULE)}</label>
                            </td>
                            <td class="fieldValue {$WIDTHTYPE}">
                                <span class="value">
                                    {$QUESTION['datetime_answer_time']}
                                </span>
                            </td>
                        </tr>
                    {elseif $QUESTION['question_type'] === 'Time'}
                        <tr class="answer_{$QUESTION['section_id']}_{$QUESTION['question_id']}_time">
                            <td class='fieldLabel {$WIDTHTYPE}'>
                                <label class='muted pull-right marginRight10px'>{vtranslate('LBL_OPLIST_DEFAULTANSWER', $MODULE)}</label>
                            </td>
                            <td class="fieldValue {$WIDTHTYPE}">
                                <span class="value">
                                    {$QUESTION['time_answer']}
                                </span>
                            </td>
                            <td class='fieldLabel {$WIDTHTYPE}'>
                                &nbsp;
                            </td>
                            <td class="fieldValue {$WIDTHTYPE}">
                                &nbsp;
                            </td>
                        </tr>
                    {elseif $QUESTION['question_type'] === 'Quantity'}
                        <tr class="answer_{$QUESTION['section_id']}_{$QUESTION['question_id']}_number">
                            <td class='fieldLabel {$WIDTHTYPE}'>
                                <label class='muted pull-right marginRight10px'>{vtranslate('LBL_OPLIST_USEDECIMAL', $MODULE)}</label>
                            </td>
                            <td class="fieldValue {$WIDTHTYPE}">
                                <span class="value">
                                    {if $QUESTION['use_dec'] == 1}{vtranslate('LBL_YES', $MODULE)}{else}{vtranslate('LBL_NO', $MODULE)}{/if}
                                </span>
                            </td>
                            <td class='fieldLabel {$WIDTHTYPE}'>
                                <label class='muted pull-right marginRight10px'>{vtranslate('LBL_OPLIST_DEFAULTANSWER', $MODULE)}</label>
                            </td>
                            <td class="fieldValue {$WIDTHTYPE}">
                                <span class="value">
                                    {if $QUESTION['use_dec']}{$QUESTION['dec_answer']}{else}{$QUESTION['int_answer']}{/if}
                                </span>
                            </td>
                        </tr>
                    {elseif $QUESTION['question_type'] === 'Multiple Choice'}
                        <tr class="answer_{$QUESTION['section_id']}_{$QUESTION['question_id']}_multi">
                            <td class='fieldLabel {$WIDTHTYPE}'>
                                <label class='muted pull-right marginRight10px'>{vtranslate('LBL_OPLIST_SELECTMULTIPLE', $MODULE)}</label>
                            </td>
                            <td class="fieldValue {$WIDTHTYPE}">
                                <span class="value">
                                    {if $QUESTION['allow_multiple_answers'] == 1}{vtranslate('LBL_YES', $MODULE)}{else}{vtranslate('LBL_NO', $MODULE)}{/if}
                                </span>
                            </td>
                            <td class='fieldLabel {$WIDTHTYPE}'>
                                &nbsp;
                            </td>
                            <td class="fieldValue {$WIDTHTYPE}">
                                &nbsp;
                            </td>
                        </tr>
                        <tr class="answer_{$QUESTION['section_id']}_{$QUESTION['question_id']}_multi">
                            <td class='fieldLabel {$WIDTHTYPE}'>
                                <label class='muted pull-right marginRight10px'>{vtranslate('LBL_OPLIST_MULTIANSWERS', $MODULE)}</label>
                            </td>
                            <td class="fieldValue {$WIDTHTYPE}" colspan="3" style="width:100%;padding:0 !important">
                                <table class="table table-bordered equalSplit detailview-table" style="padding: 0 !important; border: 0">
                                    <tbody>
                                    <tr colspan="11">
                                        <td class="fieldLabel" style="width:20%;text-align:center;margin:auto;">
                                            <label class='muted'>{vtranslate('LBL_OPLIST_DEFAULTANSWER', $MODULE)}</label>
                                        </td>
                                        <td class="fieldLabel" style="width:80%;text-align:center;margin:auto;">
                                            <label class='muted'>{vtranslate('LBL_OPLIST_OPTION', $MODULE)}</label>
                                        </td>
                                    </tr>
                                    {foreach item=OPTION from=$QUESTION['multi_options']}
                                        <tr class="option_{$OPTION['section_id']}_{$OPTION['question_id']}_{$OPTION['option_id']}">
                                            <td class="fieldValue {$WIDTHTYPE}" style="width:20%;text-align:center;margin:auto;">
                                                <span class="value">
                                                    {if $OPTION['selected'] == 1}{vtranslate('LBL_YES', $MODULE)}{else}{vtranslate('LBL_NO', $MODULE)}{/if}
                                                </span>
                                            </td>
                                            <td class="fieldValue {$WIDTHTYPE}" style="width:80%;">
                                                <span class="value">
                                                    {$OPTION['answer']}
                                                </span>
                                            </td>
                                        </tr>
                                    {/foreach}
                                    </tbody>
                                </table>
                            </td>
                        </tr>
                    {/if}
                {/foreach}
                </tbody>
            </table>
            <br>
        {/foreach}
    </div>
{/strip}
