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
<div class='container-fluid editViewContainer'>
	<form class="form-horizontal recordEditView" id="EditView" name="EditView" method="post" action="index.php" enctype="multipart/form-data">
		{assign var=WIDTHTYPE value=$USER_MODEL->get('rowheight')}
		{if !empty($PICKIST_DEPENDENCY_DATASOURCE)}
			<input type="hidden" name="picklistDependency" value='{Vtiger_Util_Helper::toSafeHTML($PICKIST_DEPENDENCY_DATASOURCE)}' />
		{/if}
		{assign var=QUALIFIED_MODULE_NAME value=$MODULE}
		{assign var=IS_PARENT_EXISTS value=strpos($MODULE,":")}
		{if $IS_PARENT_EXISTS}
			{assign var=SPLITTED_MODULE value=":"|explode:$MODULE}
			<input type="hidden" name="module" value="{$SPLITTED_MODULE[1]}" />
			<input type="hidden" name="parent" value="{$SPLITTED_MODULE[0]}" />
		{else}
			<input type="hidden" name="module" value="{$MODULE}" />
		{/if}
		<input type="hidden" name="action" value="Save" />
		<input type="hidden" name="record" value="{$RECORD_ID}" />
		<input type="hidden" name="defaultCallDuration" value="{$USER_MODEL->get('callduration')}" />
		<input type="hidden" name="defaultOtherEventDuration" value="{$USER_MODEL->get('othereventduration')}" />
		{if $IS_RELATION_OPERATION }
			<input type="hidden" name="sourceModule" value="{$SOURCE_MODULE}" />
			<input type="hidden" name="sourceRecord" value="{$SOURCE_RECORD}" />
			<input type="hidden" name="relationOperation" value="{$IS_RELATION_OPERATION}" />
		{/if}
		<div class="contentHeader row-fluid">
		{assign var=SINGLE_MODULE_NAME value='SINGLE_'|cat:$MODULE}
		{if $RECORD_ID neq ''}
			<h3 class="span8 textOverflowEllipsis" title="{vtranslate('LBL_EDITING', $MODULE)} {vtranslate($SINGLE_MODULE_NAME, $MODULE)} {$RECORD_STRUCTURE_MODEL->getRecordName()}">
				{vtranslate('LBL_EDITING', $MODULE)} {vtranslate($SINGLE_MODULE_NAME, $MODULE)} - {$RECORD_STRUCTURE_MODEL->getRecordName()}
			</h3>
		{else}
			<h3 class="span8 textOverflowEllipsis">
				{vtranslate('LBL_CREATING_NEW', $MODULE)} {vtranslate($SINGLE_MODULE_NAME, $MODULE)}
			</h3>
		{/if}
			<span class="pull-right">
				<button class="btn btn-success" type="submit">
					<strong>{vtranslate('LBL_SAVE', $MODULE)}</strong>
				</button>
				<a class="cancelLink" type="reset" onclick="javascript:window.history.back();">
					{vtranslate('LBL_CANCEL', $MODULE)}
				</a>
			</span>
		</div>
		<input type="hidden" id="hiddenFields" name="hiddenFields" value="{$HIDDEN_FIELDS}" />
		{foreach key=BLOCK_LABEL item=BLOCK_FIELDS from=$RECORD_STRUCTURE name="EditViewBlockLevelLoop"}
			{if $BLOCK_FIELDS|@count lte 0}
				{continue}
			{/if}
			<table class="table table-bordered blockContainer showInlineTable equalSplit{if is_array($HIDDEN_BLOCKS)}{if in_array($BLOCK_LABEL, $HIDDEN_BLOCKS)} hide{/if}{/if}">
                <thead>
					<tr>
						<th class="blockHeader" colspan="4">{vtranslate($BLOCK_LABEL, $MODULE)}</th>
					</tr>
                </thead>
                <tbody>
					<tr>
						{assign var=COUNTER value=0}
						{foreach key=FIELD_NAME item=FIELD_MODEL from=$BLOCK_FIELDS name=blockfields}
                            {if $FIELD_NAME eq 'potential_id'}
                                {*The potential_id field should only be used in the custom view*}
                                {continue}
                            {/if}
							{assign var="isReferenceField" value=$FIELD_MODEL->getFieldDataType()}
							{if $isReferenceField eq 'reference' && count($FIELD_MODEL->getReferenceList()) < 1}
								{continue}
							{/if}
							{if $FIELD_MODEL->get('uitype') eq "20" or $FIELD_MODEL->get('uitype') eq "19"}
								{if $COUNTER eq '1'}
									<td class="{$WIDTHTYPE}">
									</td>
									<td class="{$WIDTHTYPE}">
									</td>
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
							<td class="fieldLabel {$WIDTHTYPE}{if !$USER_MODEL->isAdminUser() && $FIELD_MODEL->get('uitype') eq "53"} hide{/if}">
								{if $isReferenceField neq "reference"}
									<label class="muted pull-right marginRight10px">
								{/if}
								{if $FIELD_MODEL->isMandatory() eq true && $isReferenceField neq "reference"}
									<span class="redColor">*</span>
								{/if}
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
										<select id="{$MODULE}_editView_fieldName_{$FIELD_MODEL->getName()}_dropDown" class="chzn-select referenceModulesList streched" style="width:160px;">
											<optgroup>
												{foreach key=index item=value from=$REFERENCE_LIST}
													<option value="{$value}" {if $value eq $REFERENCED_MODULE_NAME} selected {/if}>{vtranslate($value, $MODULE)}</option>
												{/foreach}
											</optgroup>
										</select>
									</span>
									{else}
										<label class="muted pull-right marginRight10px">{if $FIELD_MODEL->isMandatory() eq true} <span class="redColor">*</span> {/if}{vtranslate($FIELD_MODEL->get('label'), $MODULE)}</label>
									{/if}
								{elseif $FIELD_MODEL->get('uitype') eq "83"}
									{include file=vtemplate_path($FIELD_MODEL->getUITypeModel()->getTemplateName(),$MODULE) COUNTER=$COUNTER MODULE=$MODULE}
								{else}
									{vtranslate($FIELD_MODEL->get('label'), $MODULE)}
								{/if}
								{if $isReferenceField neq "reference"}
									</label>
								{/if}
							</td>
							{if $FIELD_MODEL->get('uitype') neq "83"}
								<td class="fieldValue {$WIDTHTYPE}{if !$USER_MODEL->isAdminUser() && $FIELD_MODEL->get('uitype') eq "53"} hide{/if}" {if $FIELD_MODEL->get('uitype') eq '19' or $FIELD_MODEL->get('uitype') eq '20'} colspan="3" {assign var=COUNTER value=$COUNTER+1} {/if}>
									<div class="row-fluid">
										<span class="span10">
                                            {*{vtemplate_path($FIELD_MODEL->getUITypeModel()->getTemplateName(),$MODULE)}*}
											{include file=vtemplate_path($FIELD_MODEL->getUITypeModel()->getTemplateName(),$MODULE) BLOCK_FIELDS=$BLOCK_FIELDS}
										</span>
									</div>
								</td>
							{/if}
							{if $BLOCK_FIELDS|@count eq 1 and $FIELD_MODEL->get('uitype') neq "19" and $FIELD_MODEL->get('uitype') neq "20" and $FIELD_MODEL->get('uitype') neq "30" and $FIELD_MODEL->get('name') neq "recurringtype"}
								<td class="{$WIDTHTYPE}">
								</td>
								<td class="{$WIDTHTYPE}">
								</td>
							{/if}
							{if $MODULE eq 'Events' && $BLOCK_LABEL eq 'LBL_EVENT_INFORMATION' && $smarty.foreach.blockfields.last }
								{include file=vtemplate_path('uitypes/FollowUp.tpl',$MODULE) COUNTER=$COUNTER}
							{/if}
                            {if !$USER_MODEL->isAdminUser() && $FIELD_MODEL->get('uitype') eq "53"}
                                {assign var=COUNTER value=$COUNTER-1}
                            {/if}
						{/foreach}
						{* adding additional column for odd number of fields in a block *}
						{if $BLOCK_FIELDS|@end eq true and $BLOCK_FIELDS|@count neq 1 and $COUNTER eq 1}
							<td class="fieldLabel {$WIDTHTYPE}"></td>
							<td class="{$WIDTHTYPE}">
								{if $BLOCK_LABEL eq 'LBL_OPLIST_INFORMATION'}
									<button type='button' name='addSection' id='addSection'>{vtranslate('LBL_OPLIST_ADDSECTION', $MODULE)}</button>
									<input type="hidden" name="numSections" value="{$NUM_SECTIONS}">
								{/if}
							</td>
						{elseif $BLOCK_LABEL eq 'LBL_OPLIST_INFORMATION'}
							</tr>
							<tr>
								<td class="fieldLabel {$WIDTHTYPE}">&nbsp;</td>
								<td class="fieldValue {$WIDTHTYPE}">
									<button type='button' name='addSection' id='addSection'>{vtranslate('LBL_OPLIST_ADDSECTION', $MODULE)}</button>
									<input type="hidden" name="numSections" value="{$NUM_SECTIONS}">
								</td>
								<td class="fieldLabel {$WIDTHTYPE}">&nbsp;</td>
								<td class="fieldValue {$WIDTHTYPE}">&nbsp;</td>
						{/if}
					</tr>
				</tbody>
			</table>
		{/foreach}
		<div class="sectionsContainer">
			<br>
			{*This is where the load logic will go*}
            {foreach item=SECTION from=$OPLIST_ARRAY['sections']}
                <table name="opListSectionBlock_{$SECTION['section_id']}" class="table table-bordered equalSplit detailview-table">
                    <thead>
                    <tr>
                        <th class="blockHeader" colspan="1" style="width:20%">
                            <label class="pull-right" style="padding:5px;">
                                <span class="redColor">*</span><b>Section Name</b>
                            </label>
                            <input type="hidden" name="sectionOrder_{$SECTION['section_id']}" value="{$SECTION['section_order']}">
                        </th>
                        <th class="blockHeader" colspan="3" style="width:80%">
                            <span>
                                <a style="float: right; padding: 3px">
                                    <i title="Delete Section" name="deleteSectionButton_{$SECTION['section_id']}" class="deleteSectionButton icon-trash" style="vertical-align: middle;"></i>&nbsp;&nbsp;&nbsp;
                                </a>
                                <button type="button" name="moveSectionDown_{$SECTION['section_id']}" style="float: right;">
                                    Section&nbsp;&nbsp;
                                    <i title="Move Down" class="moveSectionDown icon-chevron-down" style="vertical-align: top;"></i>
                                </button>
                                <button type="button" name="moveSectionUp_{$SECTION['section_id']}" style="float: right;">
                                    Section&nbsp;&nbsp;
                                    <i title="Move Up" class="moveSectionUp icon-chevron-up" style="vertical-align: top;"></i>
                                </button>
                            </span>
                            {$LOCALFIELDINFO.mandatory = true}
                            {$LOCALFIELDINFO.name = 'section_name'}
                            {$LOCALFIELDINFO.label = 'LBL_OPLIST_SECTIONNAME'}
                            {$LOCALFIELDINFO.type = 'string'}
                            {$INFO = Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($LOCALFIELDINFO))}
                            <input id="section_name_{$SECTION['section_id']}" type="text" class="input-large" name="section_name_{$SECTION['section_id']}" value="{$SECTION['section_name']}" data-fieldinfo={$INFO} data-validation-engine='validate[{if $LOCALFIELDINFO.mandatory}required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]' style="margin-top: 1px;margin-bottom: 1px;margin-left:3px;">
                        </th>
                    </tr>
                    </thead>
                    <tbody class="opListSection_{$SECTION['section_id']}">
                    <tr class="sectionRow_{$SECTION['section_id']}">
                        <td colspan="4" style="background-color:#E8E8E8;">
                            <button type="button" name="addQuestion1_{$SECTION['section_id']}" id="addQuestion1" style="clear:right;float:left;line-height:10px;padding-right:3px;padding-left:3px;">+</button>
                            <input type="hidden" id="numQuestions" name="numQuestions_{$SECTION['section_id']}" value="{$SECTION['num_questions']}">
                            <span class="SectionLabel">&nbsp;&nbsp;&nbsp;Add Question</span>
                            <button type="button" name="addQuestion2_{$SECTION['section_id']}" id="addQuestion2" style="clear:right;float:right;line-height:10px;padding-right:3px;padding-left:3px;">+</button>
                        </td>
                    </tr>
                    {foreach item=QUESTION from=$SECTION['questions']}
                        <tr class="questionLabelRow question_{$QUESTION['section_id']}_{$QUESTION['question_id']}">
                            <td class='fieldLabel {$WIDTHTYPE}' colspan="4" style="width:100%;border-left:0px;border-top:2px dotted #666">
                                <span>
                                    <a style='float: right; padding: 3px'>
                                        <i title='Delete Question' id="deleteQuestionButton_{$QUESTION['section_id']}_{$QUESTION['question_id']}" name="deleteQuestionButton_{$QUESTION['section_id']}_{$QUESTION['question_id']}" class='deleteQuestionButton icon-trash' style="vertical-align: middle;"></i>&nbsp;&nbsp;
                                    </a>
                                    <button type="button" name="moveQuestionDown_{$QUESTION['section_id']}_{$QUESTION['question_id']}" style='float: right;'>
                                        {vtranslate('LBL_OPLIST_QUESTIONMOVEDOWN', $MODULE)}
                                        <i title='Move Down' class='moveQuestionDown icon-chevron-down' style="vertical-align: top;"></i>
                                    </button>
                                    <button type="button" name="moveQuestionUp_{$QUESTION['section_id']}_{$QUESTION['question_id']}" style='float: right;'>
                                        {vtranslate('LBL_OPLIST_QUESTIONMOVEUP', $MODULE)}
                                        <i title='Move Up' class='moveQuestionUp icon-chevron-up' style="vertical-align: top;"></i>
                                    </button>
                                </span>
                                &nbsp;
                            </td>
                        </tr>
                        <tr class="question_{$QUESTION['section_id']}_{$QUESTION['question_id']}">
                            <td class='fieldLabel {$WIDTHTYPE}'>
                                <label class='muted pull-right marginRight10px'><span class="redColor">*</span>{vtranslate('LBL_OPLIST_QUESTION_TYPE', $MODULE)}</label>
                            </td>
                            <td class='fieldValue {$WIDTHTYPE}'>
                                <div class='row-fluid'>
                                <span class='span12'>
                                    {$LOCALFIELDINFO.mandatory = true}
                                    {$LOCALFIELDINFO.name = 'question_type'}
                                    {$LOCALFIELDINFO.label = 'LBL_OPLIST_QUESTION_TYPE'}
                                    {$LOCALFIELDINFO.type = 'string'}
                                    {$INFO = Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($LOCALFIELDINFO))}
                                    <input type="hidden" name="question_type_{$QUESTION['section_id']}_{$QUESTION['question_id']}" value="none">
                                    <select class="chzn-select" style="text-align:left" id="question_type_{$QUESTION['section_id']}_{$QUESTION['question_id']}" name="question_type_{$QUESTION['section_id']}_{$QUESTION['question_id']}" data-fieldinfo="" data-selected-value="">
                                        <option value="" style="text-align:left">{vtranslate('LBL_OPLIST_SELECTANOPTION', $MODULE)}</option>
                                        <option style="text-align:left" value="Text" {if $QUESTION['question_type'] === "Text"}selected{/if}>{vtranslate('LBL_OPLIST_TEXT', $MODULE)}</option>
                                        <option style="text-align:left" value="Yes/No" {if $QUESTION['question_type'] === "Yes/No"}selected{/if}>{vtranslate('LBL_OPLIST_YESNO', $MODULE)}</option>
                                        <option style="text-align:left" value="Date" {if $QUESTION['question_type'] === "Date"}selected{/if}>{vtranslate('LBL_OPLIST_DATE', $MODULE)}</option>
                                        <option style="text-align:left" value="Date and Time" {if $QUESTION['question_type'] === "Date and Time"}selected{/if}>{vtranslate('LBL_OPLIST_DATETIME', $MODULE)}</option>
                                        <option style="text-align:left" value="Time" {if $QUESTION['question_type'] === "Time"}selected{/if}>{vtranslate('LBL_OPLIST_TIME', $MODULE)}</option>
                                        <option style="text-align:left" value="Quantity" {if $QUESTION['question_type'] === "Quantity"}selected{/if}>{vtranslate('LBL_OPLIST_QUANTITY', $MODULE)}</option>
                                        <option style="text-align:left" value="Multiple Choice"{if $QUESTION['question_type'] === "Multiple Choice"}selected{/if}>{vtranslate('LBL_OPLIST_MULTIPLECHOICE', $MODULE)}</option>
                                    </select>
                                </span>
                                </div>
                            </td>
                            <td class='fieldLabel {$WIDTHTYPE}'>
                                <input type='hidden' id='question_order_{$QUESTION['section_id']}_{$QUESTION['question_id']}' name='question_order_{$QUESTION['section_id']}_{$QUESTION['question_id']}' value='{$QUESTION['question_order']}'>
                            </td>
                            <td class='fieldValue {$WIDTHTYPE}'>
                                &nbsp;
                            </td>
                        </tr>
                        <tr class="question_{$QUESTION['section_id']}_{$QUESTION['question_id']}">
                            <td class='fieldLabel {$WIDTHTYPE}'>
                                <label class='muted pull-right marginRight10px'><span class="redColor">*</span>{vtranslate('LBL_OPLIST_QUESTION', $MODULE)}</label>
                            </td>
                            <td class="fieldValue {$WIDTHTYPE}" colspan="3" style="width:80%;" >
                                {$LOCALFIELDINFO.mandatory = true}
                                {$LOCALFIELDINFO.name = 'question'}
                                {$LOCALFIELDINFO.label = 'LBL_OPLIST_QUESTION'}
                                {$LOCALFIELDINFO.type = 'text'}
                                {$INFO = Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($LOCALFIELDINFO))}
                                <textarea id="question_{$QUESTION['section_id']}_{$QUESTION['question_id']}" class="span11 " name="question_{$QUESTION['section_id']}_{$QUESTION['question_id']}" data-fieldinfo={$INFO} data-validation-engine='validate[{if $LOCALFIELDINFO.mandatory}required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]'>{$QUESTION['question']}</textarea>
                            </td>
                        </tr>
                        {if $QUESTION['question_type'] === 'Text'}
                            <tr class="answer_{$QUESTION['section_id']}_{$QUESTION['question_id']}_text">
                                <td class='fieldLabel {$WIDTHTYPE}'>
                                    <label class='muted pull-right marginRight10px'>{vtranslate('LBL_OPLIST_DEFAULTANSWER', $MODULE)}</label>
                                </td>
                                <td class="fieldValue {$WIDTHTYPE}" colspan="3" style="width:80%;" >
                                    {$LOCALFIELDINFO.mandatory = false}
                                    {$LOCALFIELDINFO.name = 'default_answer_text'}
                                    {$LOCALFIELDINFO.label = 'LBL_OPLIST_DEFAULTANSWERTEXT'}
                                    {$LOCALFIELDINFO.type = 'text'}
                                    {$INFO = Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($LOCALFIELDINFO))}
                                    <textarea id="default_answer_text_{$QUESTION['section_id']}_{$QUESTION['question_id']}" class="span11 " name="default_answer_text_{$QUESTION['section_id']}_{$QUESTION['question_id']}" data-fieldinfo={$INFO} data-validation-engine='validate[{if $LOCALFIELDINFO.mandatory}required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]'>{$QUESTION['text_answer']}</textarea>
                                </td>
                            </tr>
                        {elseif $QUESTION['question_type'] === 'Yes/No'}
                        <tr class="answer_{$QUESTION['section_id']}_{$QUESTION['question_id']}_bool">
                            <td class='fieldLabel {$WIDTHTYPE}'>
                                <label class='muted pull-right marginRight10px'>{vtranslate('LBL_OPLIST_DEFAULTANSWER', $MODULE)}</label>
                            </td>
                            <td class="fieldValue {$WIDTHTYPE}">
                                {$LOCALFIELDINFO.mandatory = false}
                                {$LOCALFIELDINFO.name = 'default_answer_bool'}
                                {$LOCALFIELDINFO.label = 'LBL_OPLIST_DEFAULTANSWERBOOL'}
                                {$LOCALFIELDINFO.type = 'boolean'}
                                {$INFO = Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($LOCALFIELDINFO))}
                                <input type="hidden" name="default_answer_bool_{$QUESTION['section_id']}_{$QUESTION['question_id']}" value="0">
                                <input {if $QUESTION['bool_answer'] == 1}checked {/if}id='default_answer_bool_{$QUESTION['section_id']}_{$QUESTION['question_id']}' type="checkbox" name="default_answer_bool_{$QUESTION['section_id']}_{$QUESTION['question_id']}" data-fieldinfo={$INFO} data-validation-engine='validate[{if $LOCALFIELDINFO.mandatory}required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]'>
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
                                {assign var="dateFormat" value=$USER_MODEL->get('date_format')}
                                {$LOCALFIELDINFO.mandatory = false}
                                {$LOCALFIELDINFO.name = 'default_answer_date'}
                                {$LOCALFIELDINFO.label = 'LBL_OPLIST_DEFAULTANSWERDATE'}
                                {$LOCALFIELDINFO.type = 'date'}
                                {$LOCALFIELDINFO.dateFormat = $dateFormat}
                                {$INFO = Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($LOCALFIELDINFO))}
                                <div class="input-append row-fluid">
                                    <div class="span12 row-fluid date">
                                        <input value="{$QUESTION['date_answer']}" id='default_answer_date_{$QUESTION['section_id']}_{$QUESTION['question_id']}' type="text" class="dateField" data-date-format="{$dateFormat}" name="default_answer_date_{$QUESTION['section_id']}_{$QUESTION['question_id']}" data-fieldinfo={$INFO} data-validation-engine='validate[{if $LOCALFIELDINFO.mandatory}required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]'>
                                        <span class="add-on"><i class="icon-calendar"></i></span>
                                    </div>
                                </div>
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
                                {assign var="dateFormat" value=$USER_MODEL->get('date_format')}
                                {$LOCALFIELDINFO.mandatory = false}
                                {$LOCALFIELDINFO.name = 'default_answer_datetime_date'}
                                {$LOCALFIELDINFO.label = 'LBL_OPLIST_DEFAULTANSWERDATETIMEDATE'}
                                {$LOCALFIELDINFO.type = 'date'}
                                {$LOCALFIELDINFO.dateFormat = $dateFormat}
                                {$INFO = Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($LOCALFIELDINFO))}
                                <div class="input-append row-fluid">
                                    <div class="span12 row-fluid date">
                                        <input value="{$QUESTION['datetime_answer_date']}" id='default_answer_datetime_date_{$QUESTION['section_id']}_{$QUESTION['question_id']}' type="text" class="dateField" data-date-format="{$dateFormat}" name="default_answer_datetime_date_{$QUESTION['section_id']}_{$QUESTION['question_id']}" data-fieldinfo={$INFO} data-validation-engine='validate[{if $LOCALFIELDINFO.mandatory}required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]'>
                                        <span class="add-on"><i class="icon-calendar"></i></span>
                                    </div>
                                </div>
                            </td>
                            <td class='fieldLabel {$WIDTHTYPE}'>
                                <label class='muted pull-right marginRight10px'>{vtranslate('LBL_OPLIST_DEFAULTANSWERTIME', $MODULE)}</label>
                            </td>
                            <td class="fieldValue {$WIDTHTYPE}">
                                {assign var="TIME_FORMAT" value=$USER_MODEL->get('hour_format')}
                                {$LOCALFIELDINFO.mandatory = false}
                                {$LOCALFIELDINFO.name = 'default_answer_datetime_time'}
                                {$LOCALFIELDINFO.label = 'LBL_OPLIST_DEFAULTANSWERDATETIMETIME'}
                                {$LOCALFIELDINFO.type = 'time'}
                                {$LOCALFIELDINFO.timeFormat = $TIME_FORMAT}
                                {$INFO = Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($LOCALFIELDINFO))}
                                <div class="input-append time">
                                    <input value="{$QUESTION['datetime_answer_time']}" id='default_answer_datetime_time_{$QUESTION['section_id']}_{$QUESTION['question_id']}' type="text" class="timepicker-default input-small" data-format="{$TIME_FORMAT}" name="default_answer_datetime_time_{$QUESTION['section_id']}_{$QUESTION['question_id']}" data-fieldinfo={$INFO} data-validation-engine='validate[{if $LOCALFIELDINFO.mandatory}required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]'>
							<span class="add-on cursorPointer">
								<i class="icon-time"></i>
							</span>
                                </div>
                            </td>
                        </tr>
                        {elseif $QUESTION['question_type'] === 'Time'}
                        <tr class="answer_{$QUESTION['section_id']}_{$QUESTION['question_id']}_time">
                            <td class='fieldLabel {$WIDTHTYPE}'>
                                <label class='muted pull-right marginRight10px'>{vtranslate('LBL_OPLIST_DEFAULTANSWER', $MODULE)}</label>
                            </td>
                            <td class="fieldValue {$WIDTHTYPE}">
                                {assign var="TIME_FORMAT" value=$USER_MODEL->get('hour_format')}
                                {$LOCALFIELDINFO.mandatory = false}
                                {$LOCALFIELDINFO.name = 'default_answer_time'}
                                {$LOCALFIELDINFO.label = 'LBL_OPLIST_DEFAULTANSWERDATETIME'}
                                {$LOCALFIELDINFO.type = 'time'}
                                {$LOCALFIELDINFO.timeFormat = $TIME_FORMAT}
                                {$INFO = Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($LOCALFIELDINFO))}
                                <div class="input-append time">
                                    <input value="{$QUESTION['time_answer']}" id='default_answer_time_{$QUESTION['section_id']}_{$QUESTION['question_id']}' type="text" class="timepicker-default input-small" data-format="{$TIME_FORMAT}" name="default_answer_time_{$QUESTION['section_id']}_{$QUESTION['question_id']}" data-fieldinfo={$INFO} data-validation-engine='validate[{if $LOCALFIELDINFO.mandatory}required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]'>
							<span class="add-on cursorPointer">
								<i class="icon-time"></i>
							</span>
                                </div>
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
                                {$LOCALFIELDINFO.mandatory = false}
                                {$LOCALFIELDINFO.name = 'default_answer_use_decimal'}
                                {$LOCALFIELDINFO.label = 'LBL_OPLIST_USEDEC'}
                                {$LOCALFIELDINFO.type = 'boolean'}
                                {$INFO = Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($LOCALFIELDINFO))}
                                <input type="hidden" name="default_answer_use_decimal_{$QUESTION['section_id']}_{$QUESTION['question_id']}" value="0">
                                <input {if $QUESTION['use_dec'] == true}checked {/if}id='default_answer_use_decimal_{$QUESTION['section_id']}_{$QUESTION['question_id']}' type="checkbox" name="default_answer_use_decimal_{$QUESTION['section_id']}_{$QUESTION['question_id']}" data-fieldinfo={$INFO} data-validation-engine='validate[{if $LOCALFIELDINFO.mandatory}required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]'>
                            </td>
                            <td class='fieldLabel {$WIDTHTYPE}'>
                                <label class='muted pull-right marginRight10px'>{vtranslate('LBL_OPLIST_DEFAULTANSWER', $MODULE)}</label>
                            </td>
                            <td class="fieldValue {$WIDTHTYPE}">
                                <div class='row-fluid'>
							<span class='span12'>
								{$LOCALFIELDINFO.mandatory = false}
                                {$LOCALFIELDINFO.name = 'default_answer_number'}
                                {$LOCALFIELDINFO.label = 'LBL_OPLIST_DEFAULTANSWERNUMBER'}
                                {$LOCALFIELDINFO.type = 'decimal'}
                                {$INFO = Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($LOCALFIELDINFO))}
                                <input id='default_answer_number_{$QUESTION['section_id']}_{$QUESTION['question_id']}' type="number" min="0" max="" step="any" class='input-large' name='default_answer_number_{$QUESTION['section_id']}_{$QUESTION['question_id']}' value='{if $QUESTION['use_dec']}{$QUESTION['dec_answer']}{else}{$QUESTION['int_answer']}{/if}' data-fieldinfo={$INFO} data-validation-engine='validate[{if $LOCALFIELDINFO.mandatory}required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]'>
							</span>
                                </div>
                            </td>
                        </tr>
                        {elseif $QUESTION['question_type'] === 'Multiple Choice'}
                        <tr class="answer_{$QUESTION['section_id']}_{$QUESTION['question_id']}_multi">
                            <td class='fieldLabel {$WIDTHTYPE}'>
                                <label class='muted pull-right marginRight10px'>{vtranslate('LBL_OPLIST_SELECTMULTIPLE', $MODULE)}</label>
                            </td>
                            <td class="fieldValue {$WIDTHTYPE}">
                                {$LOCALFIELDINFO.mandatory = false}
                                {$LOCALFIELDINFO.name = 'default_answer_select_multiple'}
                                {$LOCALFIELDINFO.label = 'LBL_OPLIST_DEFAULTANSWERSELECTMULTIPLE'}
                                {$LOCALFIELDINFO.type = 'boolean'}
                                {$INFO = Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($LOCALFIELDINFO))}
                                <input type="hidden" name="default_answer_select_multiple_{$QUESTION['section_id']}_{$QUESTION['question_id']}" value="0">
                                <input {if $QUESTION['allow_multiple_answers'] == 1}checked {/if}id='default_answer_select_multiple_{$QUESTION['section_id']}_{$QUESTION['question_id']}' type="checkbox" name="default_answer_select_multiple_{$QUESTION['section_id']}_{$QUESTION['question_id']}" data-fieldinfo={$INFO} data-validation-engine='validate[{if $LOCALFIELDINFO.mandatory}required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]'>
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
                                        <th style="background-color:#E8E8E8;padding:4px; border-top-width: 0px !important;" colspan="11">
                                            <button type='button' name='addMultiOption1_{$QUESTION['section_id']}_{$QUESTION['question_id']}' id='addMultiOption1_{$QUESTION['section_id']}_{$QUESTION['question_id']}' style="clear:right;float:left;line-height:10px;padding-right:3px;padding-left:3px;">+</button>
                                            <input type='hidden' id='numOptions_{$QUESTION['section_id']}_{$QUESTION['question_id']}' name='numOptions_{$QUESTION['section_id']}_{$QUESTION['question_id']}' value='{$QUESTION['num_options']}'>
                                        <span class='OptionsLabel'>
                                                &nbsp;&nbsp;&nbsp;{vtranslate('LBL_OPLIST_ADD_OPTION', $MODULE)}
                                        </span>
                                            <button type='button' name='addMultiOption2_{$QUESTION['section_id']}_{$QUESTION['question_id']}' id='addMultiOption2_{$QUESTION['section_id']}_{$QUESTION['question_id']}' style="clear:right;float:right;line-height:10px;padding-right:3px;padding-left:3px;">+</button>
                                        </th>
                                        <tr colspan="11">
                                            <td class="fieldLabel" style="width:5%;text-align:center;margin:auto;">
                                                &nbsp;
                                            </td>
                                            <td class="fieldLabel" style="width:10%;text-align:center;margin:auto;" colspan="2">
                                                <label class='muted'>{vtranslate('LBL_OPLIST_ORDER', $MODULE)}</label>
                                            </td>
                                            <td class="fieldLabel" style="width:10%;text-align:center;margin:auto;">
                                                <label class='muted'>{vtranslate('LBL_OPLIST_DEFAULTANSWER', $MODULE)}</label>
                                            </td>
                                            <td class="fieldLabel" style="width:75%;text-align:center;margin:auto;">
                                                <label class='muted'><span class="redColor">*</span>{vtranslate('LBL_OPLIST_OPTION', $MODULE)}</label>
                                            </td>
                                        </tr>
                                        {foreach item=OPTION from=$QUESTION['multi_options']}
                                            <tr class="option_{$OPTION['section_id']}_{$OPTION['question_id']}_{$OPTION['option_id']}">
                                                <td style="width:5%;text-align:center;margin:auto;">
                                                    <input type='hidden' id='option_order_{$OPTION['section_id']}_{$OPTION['question_id']}_{$OPTION['option_id']}' name='option_order_{$OPTION['section_id']}_{$OPTION['question_id']}_{$OPTION['option_id']}' value='{$OPTION['option_order']}'>
                                                    <i title='Delete Option' id="deleteMultiOption_{$OPTION['section_id']}_{$OPTION['question_id']}_{$OPTION['option_id']}" name="deleteMultiOption_{$OPTION['section_id']}_{$OPTION['question_id']}_{$OPTION['option_id']}" class='deleteMultiOption icon-trash' style="vertical-align: middle;"></i>
                                                </td>
                                                <td style="width:5%;text-align:center;margin:auto;">
                                                    <button type="button" name="moveOptionUp_{$OPTION['section_id']}_{$OPTION['question_id']}_{$OPTION['option_id']}">
                                                        {vtranslate('LBL_OPLIST_QUESTIONMOVEUP', $MODULE)}
                                                        <i title='Move Up' class='moveOptionUp icon-chevron-up' style="vertical-align: top;"></i>
                                                    </button>
                                                </td>
                                                <td style="width:5%;text-align:center;margin:auto;">
                                                    <button type="button" name="moveOptionDown_{$OPTION['section_id']}_{$OPTION['question_id']}_{$OPTION['option_id']}">
                                                        {vtranslate('LBL_OPLIST_QUESTIONMOVEDOWN', $MODULE)}
                                                        <i title='Move Down' class='moveOptionDown icon-chevron-down' style="vertical-align: top;"></i>
                                                    </button>
                                                </td>
                                                <td class="multipleNotAllowed{if $QUESTION['allow_multiple_answers'] == 1} hide{/if}" style="width:10%;text-align:center;margin:auto;">
                                                    <input type="hidden" name="defaultMultiOption_prev_{$OPTION['section_id']}_{$OPTION['question_id']}" value="none" />
                                                    <input {if $OPTION['selected']}checked {/if}type="radio" name="defaultMultiOption_{$OPTION['section_id']}_{$OPTION['question_id']}" value="{$OPTION['option_id']}">
                                                </td>
                                                <td class="multipleAllowed{if $QUESTION['allow_multiple_answers'] != 1} hide{/if}" style="width:10%;text-align:center;margin:auto;">
                                                    {$LOCALFIELDINFO.mandatory = false}
                                                    {$LOCALFIELDINFO.name = 'default_multi_option'}
                                                    {$LOCALFIELDINFO.label = 'LBL_OPLIST_DEFAULTMULTIOPTION'}
                                                    {$LOCALFIELDINFO.type = 'boolean'}
                                                    {$INFO = Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($LOCALFIELDINFO))}
                                                    <input type="hidden" name="default_multi_option_{$OPTION['section_id']}_{$OPTION['question_id']}_{$OPTION['option_id']}" value="0">
                                                    <input {if $OPTION['selected']}checked {/if}id='default_multi_option_{$OPTION['section_id']}_{$OPTION['question_id']}_{$OPTION['option_id']}' type="checkbox" name="default_multi_option_{$OPTION['section_id']}_{$OPTION['question_id']}_{$OPTION['option_id']}" data-fieldinfo={$INFO} data-validation-engine='validate[{if $LOCALFIELDINFO.mandatory}required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]'>
                                                </td>
                                                <td style="width:75%;text-align:center;margin:auto;">
                                                    {$LOCALFIELDINFO.mandatory = true}
                                                    {$LOCALFIELDINFO.name = 'multi_option_answer'}
                                                    {$LOCALFIELDINFO.label = 'LBL_OPLIST_MULTIOPTIONANSWER'}
                                                    {$LOCALFIELDINFO.type = 'string'}
                                                    {$INFO = Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($LOCALFIELDINFO))}
                                                    <input id="multi_option_answer_{$OPTION['section_id']}_{$OPTION['question_id']}_{$OPTION['option_id']}" type='text' class='input-large' name="multi_option_answer_{$OPTION['section_id']}_{$OPTION['question_id']}_{$OPTION['option_id']}" value="{$OPTION['answer']}" data-fieldinfo={$INFO} data-validation-engine='validate[{if $LOCALFIELDINFO.mandatory}required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]' style="margin-top: 1px;margin-bottom: 1px;margin-left:3px;width:90%">
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
		<table name='opListSectionBlock' class='table table-bordered equalSplit detailview-table defaultSection hide'>
			<thead>
				<th class='blockHeader' colspan='1' style="width:20%">
					<label class='pull-right' style="padding:5px;">
						<span class="redColor">*</span><b>{vtranslate('LBL_OPLIST_SECTIONNAME', $MODULE)}</b>
					</label>
					<input type="hidden" name="sectionOrder" value="0">
				</th>
				<th class='blockHeader' colspan='3' style="width:80%">
					<span>
						<a style='float: right; padding: 3px'>
							<i title='Delete Section' name='deleteSectionButton' class='deleteSectionButton icon-trash' style="vertical-align: middle;"></i>&nbsp;&nbsp;&nbsp;
						</a>
						<button type="button" name="moveSectionDown" style='float: right;'>
							{vtranslate('LBL_OPLIST_SECTIONMOVEDOWN', $MODULE)}&nbsp;&nbsp;
							<i title='Move Down' class='moveSectionDown icon-chevron-down' style="vertical-align: top;"></i>
						</button>
						<button type="button" name="moveSectionUp" style='float: right;'>
							{vtranslate('LBL_OPLIST_SECTIONMOVEUP', $MODULE)}&nbsp;&nbsp;
							<i title='Move Up' class='moveSectionUp icon-chevron-up' style="vertical-align: top;"></i>
						</button>
					</span>
					{$LOCALFIELDINFO.mandatory = true}
					{$LOCALFIELDINFO.name = 'section_name'}
					{$LOCALFIELDINFO.label = 'LBL_OPLIST_SECTIONNAME'}
					{$LOCALFIELDINFO.type = 'string'}
					{$INFO = Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($LOCALFIELDINFO))}
					<input id='section_name' type='text' class='input-large' name='section_name' value='' data-fieldinfo={$INFO} data-validation-engine='validate[{if $LOCALFIELDINFO.mandatory}required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]' style="margin-top: 1px;margin-bottom: 1px;margin-left:3px;">
				</th>
			</thead>
			<tbody class='opListSection'>
				<tr class="sectionRow defaultSectionRow">
					<td colspan='4' style="background-color:#E8E8E8;">
						<button type='button' name='addQuestion1' id='addQuestion1' style="clear:right;float:left;line-height:10px;padding-right:3px;padding-left:3px;">+</button>
						<input type='hidden' id='numQuestions' name='numQuestions' value=''>
						<span class='SectionLabel'>
								&nbsp;&nbsp;&nbsp;{vtranslate('LBL_OPLIST_ADD_QUESTION', $MODULE)}
						</span>
						<button type='button' name='addQuestion2' id='addQuestion2' style="clear:right;float:right;line-height:10px;padding-right:3px;padding-left:3px;">+</button>
					</td>
				</tr>
				<tr class="defaultQuestion questionLabelRow">
					<td class='fieldLabel {$WIDTHTYPE}' colspan="4" style="width:100%;border-left:0px;border-top:2px dotted #666">
						<span>
							<a style='float: right; padding: 3px'>
								<i title='Delete Question' id="deleteQuestionButton" name="deleteQuestionButton" class='deleteQuestionButton icon-trash' style="vertical-align: middle;"></i>&nbsp;&nbsp;
							</a>
							<button type="button" name="moveQuestionDown" style='float: right;'>
								{vtranslate('LBL_OPLIST_QUESTIONMOVEDOWN', $MODULE)}
								<i title='Move Down' class='moveQuestionDown icon-chevron-down' style="vertical-align: top;"></i>
							</button>
							<button type="button" name="moveQuestionUp" style='float: right;'>
								{vtranslate('LBL_OPLIST_QUESTIONMOVEUP', $MODULE)}
								<i title='Move Up' class='moveQuestionUp icon-chevron-up' style="vertical-align: top;"></i>
							</button>
						</span>
						&nbsp;
					</td>
				</tr>
				<tr class="defaultQuestion">
					<td class='fieldLabel {$WIDTHTYPE}'>
						<label class='muted pull-right marginRight10px'><span class="redColor">*</span>{vtranslate('LBL_OPLIST_QUESTION_TYPE', $MODULE)}</label>
					</td>
					<td class='fieldValue {$WIDTHTYPE}'>
						<div class='row-fluid'>
							<span class='span12'>
								{$LOCALFIELDINFO.mandatory = true}
								{$LOCALFIELDINFO.name = 'question_type'}
								{$LOCALFIELDINFO.label = 'LBL_OPLIST_QUESTION_TYPE'}
								{$LOCALFIELDINFO.type = 'string'}
								{$INFO = Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($LOCALFIELDINFO))}
								<input type="hidden" name="question_type" value="none">
								<select style="text-align:left" id="question_type" name="question_type" data-fieldinfo="" data-selected-value="">
									<option value="" style="text-align:left">{vtranslate('LBL_OPLIST_SELECTANOPTION', $MODULE)}</option>
									<option style="text-align:left" value="Text">{vtranslate('LBL_OPLIST_TEXT', $MODULE)}</option>
									<option style="text-align:left" value="Yes/No">{vtranslate('LBL_OPLIST_YESNO', $MODULE)}</option>
									<option style="text-align:left" value="Date">{vtranslate('LBL_OPLIST_DATE', $MODULE)}</option>
									<option style="text-align:left" value="Date and Time">{vtranslate('LBL_OPLIST_DATETIME', $MODULE)}</option>
									<option style="text-align:left" value="Time">{vtranslate('LBL_OPLIST_TIME', $MODULE)}</option>
									<option style="text-align:left" value="Quantity">{vtranslate('LBL_OPLIST_QUANTITY', $MODULE)}</option>
									<option style="text-align:left" value="Multiple Choice">{vtranslate('LBL_OPLIST_MULTIPLECHOICE', $MODULE)}</option>
								</select>
							</span>
						</div>
					</td>
					<td class='fieldLabel {$WIDTHTYPE}'>
						<input type='hidden' id='question_order' name='question_order' value='0'>
					</td>
					<td class='fieldValue {$WIDTHTYPE}'>
						&nbsp;
					</td>

				</tr>
				<tr class="defaultQuestion">
					<td class='fieldLabel {$WIDTHTYPE}'>
						<label class='muted pull-right marginRight10px'><span class="redColor">*</span>{vtranslate('LBL_OPLIST_QUESTION', $MODULE)}</label>
					</td>
					<td class="fieldValue {$WIDTHTYPE}" colspan="3" style="width:80%;" >
						{$LOCALFIELDINFO.mandatory = true}
						{$LOCALFIELDINFO.name = 'question'}
						{$LOCALFIELDINFO.label = 'LBL_OPLIST_QUESTION'}
						{$LOCALFIELDINFO.type = 'text'}
						{$INFO = Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($LOCALFIELDINFO))}
						<textarea id="question" class="span11 " name="question" data-fieldinfo={$INFO} data-validation-engine='validate[{if $LOCALFIELDINFO.mandatory}required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]'></textarea>
					</td>
				</tr>
				<tr class="defaultAnswer_text">
					<td class='fieldLabel {$WIDTHTYPE}'>
						<label class='muted pull-right marginRight10px'>{vtranslate('LBL_OPLIST_DEFAULTANSWER', $MODULE)}</label>
					</td>
					<td class="fieldValue {$WIDTHTYPE}" colspan="3" style="width:80%;" >
						{$LOCALFIELDINFO.mandatory = false}
						{$LOCALFIELDINFO.name = 'default_answer_text'}
						{$LOCALFIELDINFO.label = 'LBL_OPLIST_DEFAULTANSWERTEXT'}
						{$LOCALFIELDINFO.type = 'text'}
						{$INFO = Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($LOCALFIELDINFO))}
						<textarea id="default_answer_text" class="span11 " name="default_answer_text" data-fieldinfo={$INFO} data-validation-engine='validate[{if $LOCALFIELDINFO.mandatory}required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]'></textarea>
					</td>
				</tr>
				<tr class="defaultAnswer_bool">
					<td class='fieldLabel {$WIDTHTYPE}'>
						<label class='muted pull-right marginRight10px'>{vtranslate('LBL_OPLIST_DEFAULTANSWER', $MODULE)}</label>
					</td>
					<td class="fieldValue {$WIDTHTYPE}">
						{$LOCALFIELDINFO.mandatory = false}
						{$LOCALFIELDINFO.name = 'default_answer_bool'}
						{$LOCALFIELDINFO.label = 'LBL_OPLIST_DEFAULTANSWERBOOL'}
						{$LOCALFIELDINFO.type = 'boolean'}
						{$INFO = Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($LOCALFIELDINFO))}
						<input type="hidden" name="default_answer_bool" value="0">
						<input id='default_answer_bool' type="checkbox" name="default_answer_bool" data-fieldinfo={$INFO} data-validation-engine='validate[{if $LOCALFIELDINFO.mandatory}required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]'>
					</td>
					<td class='fieldLabel {$WIDTHTYPE}'>
						&nbsp;
					</td>
					<td class="fieldValue {$WIDTHTYPE}">
						&nbsp;
					</td>
				</tr>
				<tr class="defaultAnswer_date">
					<td class='fieldLabel {$WIDTHTYPE}'>
						<label class='muted pull-right marginRight10px'>{vtranslate('LBL_OPLIST_DEFAULTANSWER', $MODULE)}</label>
					</td>
					<td class="fieldValue {$WIDTHTYPE}">
						{assign var="dateFormat" value=$USER_MODEL->get('date_format')}
						{$LOCALFIELDINFO.mandatory = false}
						{$LOCALFIELDINFO.name = 'default_answer_date'}
						{$LOCALFIELDINFO.label = 'LBL_OPLIST_DEFAULTANSWERDATE'}
						{$LOCALFIELDINFO.type = 'date'}
						{$LOCALFIELDINFO.dateFormat = $dateFormat}
						{$INFO = Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($LOCALFIELDINFO))}
						<div class="input-append row-fluid">
							<div class="span12 row-fluid date">
								<input id='default_answer_date' type="text" class="dateField" data-date-format="{$dateFormat}" name="default_answer_date" data-fieldinfo={$INFO} data-validation-engine='validate[{if $LOCALFIELDINFO.mandatory}required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]'>
								<span class="add-on"><i class="icon-calendar"></i></span>
							</div>
						</div>
					</td>
					<td class='fieldLabel {$WIDTHTYPE}'>
						&nbsp;
					</td>
					<td class="fieldValue {$WIDTHTYPE}">
						&nbsp;
					</td>
				</tr>
				<tr class="defaultAnswer_datetime">
					<td class='fieldLabel {$WIDTHTYPE}'>
						<label class='muted pull-right marginRight10px'>{vtranslate('LBL_OPLIST_DEFAULTANSWERDATE', $MODULE)}</label>
					</td>
					<td class="fieldValue {$WIDTHTYPE}">
						{assign var="dateFormat" value=$USER_MODEL->get('date_format')}
						{$LOCALFIELDINFO.mandatory = false}
						{$LOCALFIELDINFO.name = 'default_answer_datetime_date'}
						{$LOCALFIELDINFO.label = 'LBL_OPLIST_DEFAULTANSWERDATETIMEDATE'}
						{$LOCALFIELDINFO.type = 'date'}
						{$LOCALFIELDINFO.dateFormat = $dateFormat}
						{$INFO = Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($LOCALFIELDINFO))}
						<div class="input-append row-fluid">
							<div class="span12 row-fluid date">
								<input id='default_answer_datetime_date' type="text" class="dateField" data-date-format="{$dateFormat}" name="default_answer_datetime_date" data-fieldinfo={$INFO} data-validation-engine='validate[{if $LOCALFIELDINFO.mandatory}required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]'>
								<span class="add-on"><i class="icon-calendar"></i></span>
							</div>
						</div>
					</td>
					<td class='fieldLabel {$WIDTHTYPE}'>
						<label class='muted pull-right marginRight10px'>{vtranslate('LBL_OPLIST_DEFAULTANSWERTIME', $MODULE)}</label>
					</td>
					<td class="fieldValue {$WIDTHTYPE}">
						{assign var="TIME_FORMAT" value=$USER_MODEL->get('hour_format')}
						{$LOCALFIELDINFO.mandatory = false}
						{$LOCALFIELDINFO.name = 'default_answer_datetime_time'}
						{$LOCALFIELDINFO.label = 'LBL_OPLIST_DEFAULTANSWERDATETIMETIME'}
						{$LOCALFIELDINFO.type = 'time'}
						{$LOCALFIELDINFO.timeFormat = $TIME_FORMAT}
						{$INFO = Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($LOCALFIELDINFO))}
						<div class="input-append time">
							<input id='default_answer_datetime_time' type="text" class="timepicker-default input-small" data-format="{$TIME_FORMAT}" name="default_answer_datetime_time" data-fieldinfo={$INFO} data-validation-engine='validate[{if $LOCALFIELDINFO.mandatory}required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]'>
							<span class="add-on cursorPointer">
								<i class="icon-time"></i>
							</span>
						</div>
					</td>
				</tr>
				<tr class="defaultAnswer_time">
					<td class='fieldLabel {$WIDTHTYPE}'>
						<label class='muted pull-right marginRight10px'>{vtranslate('LBL_OPLIST_DEFAULTANSWER', $MODULE)}</label>
					</td>
					<td class="fieldValue {$WIDTHTYPE}">
						{assign var="TIME_FORMAT" value=$USER_MODEL->get('hour_format')}
						{$LOCALFIELDINFO.mandatory = false}
						{$LOCALFIELDINFO.name = 'default_answer_time'}
						{$LOCALFIELDINFO.label = 'LBL_OPLIST_DEFAULTANSWERDATETIME'}
						{$LOCALFIELDINFO.type = 'time'}
						{$LOCALFIELDINFO.timeFormat = $TIME_FORMAT}
						{$INFO = Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($LOCALFIELDINFO))}
						<div class="input-append time">
							<input id='default_answer_time' type="text" class="timepicker-default input-small" data-format="{$TIME_FORMAT}" name="default_answer_time" data-fieldinfo={$INFO} data-validation-engine='validate[{if $LOCALFIELDINFO.mandatory}required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]'>
							<span class="add-on cursorPointer">
								<i class="icon-time"></i>
							</span>
						</div>
					</td>
					<td class='fieldLabel {$WIDTHTYPE}'>
						&nbsp;
					</td>
					<td class="fieldValue {$WIDTHTYPE}">
						&nbsp;
					</td>
				</tr>
				<tr class="defaultAnswer_number">
					<td class='fieldLabel {$WIDTHTYPE}'>
						<label class='muted pull-right marginRight10px'>{vtranslate('LBL_OPLIST_USEDECIMAL', $MODULE)}</label>
					</td>
					<td class="fieldValue {$WIDTHTYPE}">
						{$LOCALFIELDINFO.mandatory = false}
						{$LOCALFIELDINFO.name = 'default_answer_use_decimal'}
						{$LOCALFIELDINFO.label = 'LBL_OPLIST_USEDEC'}
						{$LOCALFIELDINFO.type = 'boolean'}
						{$INFO = Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($LOCALFIELDINFO))}
						<input type="hidden" name="default_answer_use_decimal" value="0">
						<input id='default_answer_use_decimal' type="checkbox" name="default_answer_use_decimal" data-fieldinfo={$INFO} data-validation-engine='validate[{if $LOCALFIELDINFO.mandatory}required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]'>
					</td>
					<td class='fieldLabel {$WIDTHTYPE}'>
						<label class='muted pull-right marginRight10px'>{vtranslate('LBL_OPLIST_DEFAULTANSWER', $MODULE)}</label>
					</td>
					<td class="fieldValue {$WIDTHTYPE}">
						<div class='row-fluid'>
							<span class='span12'>
								{$LOCALFIELDINFO.mandatory = false}
								{$LOCALFIELDINFO.name = 'default_answer_number'}
								{$LOCALFIELDINFO.label = 'LBL_OPLIST_DEFAULTANSWERNUMBER'}
								{$LOCALFIELDINFO.type = 'decimal'}
								{$INFO = Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($LOCALFIELDINFO))}
								<input id='default_answer_number' type="number" min="0" max="" step="any" class='input-large' name='default_answer_number' value='0' data-fieldinfo={$INFO} data-validation-engine='validate[{if $LOCALFIELDINFO.mandatory}required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]'>
							</span>
						</div>
					</td>
				</tr>
				<tr class="defaultAnswer_multi">
					<td class='fieldLabel {$WIDTHTYPE}'>
						<label class='muted pull-right marginRight10px'>{vtranslate('LBL_OPLIST_SELECTMULTIPLE', $MODULE)}</label>
					</td>
					<td class="fieldValue {$WIDTHTYPE}">
						{$LOCALFIELDINFO.mandatory = false}
						{$LOCALFIELDINFO.name = 'default_answer_select_multiple'}
						{$LOCALFIELDINFO.label = 'LBL_OPLIST_DEFAULTANSWERSELECTMULTIPLE'}
						{$LOCALFIELDINFO.type = 'boolean'}
						{$INFO = Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($LOCALFIELDINFO))}
						<input type="hidden" name="default_answer_select_multiple" value="0">
						<input id='default_answer_select_multiple' type="checkbox" name="default_answer_select_multiple" data-fieldinfo={$INFO} data-validation-engine='validate[{if $LOCALFIELDINFO.mandatory}required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]'>
					</td>
					<td class='fieldLabel {$WIDTHTYPE}'>
						&nbsp;
					</td>
					<td class="fieldValue {$WIDTHTYPE}">
						&nbsp;
					</td>
				</tr>
				<tr class="defaultAnswer_multi">
					<td class='fieldLabel {$WIDTHTYPE}'>
						<label class='muted pull-right marginRight10px'>{vtranslate('LBL_OPLIST_MULTIANSWERS', $MODULE)}</label>
					</td>
					<td class="fieldValue {$WIDTHTYPE}" colspan="3" style="width:100%;padding:0 !important">
						<table class="table table-bordered equalSplit detailview-table" style="padding: 0 !important; border: 0">
							<tbody>
								<th style="background-color:#E8E8E8;padding:4px; border-top-width: 0px !important;" colspan="11">
									<button type='button' name='addMultiOption1' id='addMultiOption1' style="clear:right;float:left;line-height:10px;padding-right:3px;padding-left:3px;">+</button>
									<input type='hidden' id='numOptions' name='numOptions' value='0'>
									<span class='OptionsLabel'>
											&nbsp;&nbsp;&nbsp;{vtranslate('LBL_OPLIST_ADD_OPTION', $MODULE)}
									</span>
									<button type='button' name='addMultiOption2' id='addMultiOption2' style="clear:right;float:right;line-height:10px;padding-right:3px;padding-left:3px;">+</button>
								</th>
								<tr colspan="11">
									<td class="fieldLabel" style="width:5%;text-align:center;margin:auto;">
										&nbsp;
									</td>
									<td class="fieldLabel" style="width:10%;text-align:center;margin:auto;" colspan="2">
										<label class='muted'>{vtranslate('LBL_OPLIST_ORDER', $MODULE)}</label>
									</td>
									<td class="fieldLabel" style="width:10%;text-align:center;margin:auto;">
										<label class='muted'>{vtranslate('LBL_OPLIST_DEFAULTANSWER', $MODULE)}</label>
									</td>
									<td class="fieldLabel" style="width:75%;text-align:center;margin:auto;">
										<label class='muted'><span class="redColor">*</span>{vtranslate('LBL_OPLIST_OPTION', $MODULE)}</label>
									</td>
								</tr>
								<tr class="defaultMultiOption">
									<td style="width:5%;text-align:center;margin:auto;">
										<input type='hidden' id='option_order' name='option_order' value='0'>
										<i title='Delete Option' id="deleteMultiOption" name="deleteMultiOption" class='deleteMultiOption icon-trash' style="vertical-align: middle;"></i>
									</td>
									<td style="width:5%;text-align:center;margin:auto;">
										<button type="button" name="moveOptionUp">
											{vtranslate('LBL_OPLIST_QUESTIONMOVEUP', $MODULE)}
											<i title='Move Up' class='moveOptionUp icon-chevron-up' style="vertical-align: top;"></i>
										</button>
									</td>
									<td style="width:5%;text-align:center;margin:auto;">
										<button type="button" name="moveOptionDown">
											{vtranslate('LBL_OPLIST_QUESTIONMOVEDOWN', $MODULE)}
											<i title='Move Down' class='moveOptionDown icon-chevron-down' style="vertical-align: top;"></i>
										</button>
									</td>
									<td class="multipleNotAllowed" style="width:10%;text-align:center;margin:auto;">
										<input type="hidden" name="defaultMultiOption_prev" value="none" />
										<input type="radio" name="defaultMultiOption" value="0">
									</td>
									<td class="multipleAllowed hide" style="width:10%;text-align:center;margin:auto;">
										{$LOCALFIELDINFO.mandatory = false}
										{$LOCALFIELDINFO.name = 'default_multi_option'}
										{$LOCALFIELDINFO.label = 'LBL_OPLIST_DEFAULTMULTIOPTION'}
										{$LOCALFIELDINFO.type = 'boolean'}
										{$INFO = Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($LOCALFIELDINFO))}
										<input type="hidden" name="default_multi_option" value="0">
										<input id='default_multi_option' type="checkbox" name="default_multi_option" data-fieldinfo={$INFO} data-validation-engine='validate[{if $LOCALFIELDINFO.mandatory}required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]'>
									</td>
									<td style="width:75%;text-align:center;margin:auto;">
										{$LOCALFIELDINFO.mandatory = true}
										{$LOCALFIELDINFO.name = 'multi_option_answer'}
										{$LOCALFIELDINFO.label = 'LBL_OPLIST_MULTIOPTIONANSWER'}
										{$LOCALFIELDINFO.type = 'string'}
										{$INFO = Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($LOCALFIELDINFO))}
										<input id='multi_option_answer' type='text' class='input-large' name='multi_option_answer' value='' data-fieldinfo={$INFO} data-validation-engine='validate[{if $LOCALFIELDINFO.mandatory}required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]' style="margin-top: 1px;margin-bottom: 1px;margin-left:3px;width:90%">
									</td>
								<tr>
							</tbody>
						</table>
					</td>
				</tr>
			</tbody>
		</table>
		{/strip}
