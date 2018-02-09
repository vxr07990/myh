{strip}
</form>
{if $NON_FOUND}
    {*Make this prettier*}
    None found
{else}
    {if $SAVED}
        <script type="text/javascript">
            jQuery(document).ready(function() {
                var aDeferred = jQuery.Deferred();
                var bootBoxModal = bootbox.alert('Your Operational List answers have been saved!', function(result) {
                    if(result){
                        aDeferred.reject();
                    } else{
                        aDeferred.reject();
                    }
                });
                bootBoxModal.on('hidden',function(e){
                    if(jQuery('#globalmodal').length > 0) {
                        jQuery('body').addClass('modal-open');
                    }
                })
            })
        </script>
    {/if}
    <form class="form-horizontal recordEditView" id="EditView" name="EditView" method="post" action="index.php" enctype="multipart/form-data">
        {assign var=WIDTHTYPE value=$USER_MODEL->get('rowheight')}
        <input type="hidden" name="module" value="{$MODULE}" />
        <input type="hidden" name="action" value="SaveOpListAnswers" />
        <input type="hidden" name="record" value="{$RECORD}" />
        <input type="hidden" name="source_record" value="{$SOURCE_RECORD}" />
        <input type="hidden" name="defaultCallDuration" value="{$USER_MODEL->get('callduration')}" />
        <input type="hidden" name="defaultOtherEventDuration" value="{$USER_MODEL->get('othereventduration')}" />
        <div class="contentHeader row-fluid">
            {assign var=SINGLE_MODULE_NAME value='SINGLE_'|cat:$MODULE}
            <h3 class="span8 textOverflowEllipsis"
                title="{vtranslate('LBL_ANSWER', $MODULE)} {vtranslate($SINGLE_MODULE_NAME, $MODULE)} {$DISPLAY_NAME}">
                {vtranslate('LBL_ANSWERING', $MODULE)} {vtranslate($SINGLE_MODULE_NAME, $MODULE)}
                - {$DISPLAY_NAME}
            </h3>
            <input type="hidden" name="display_name" value="{$DISPLAY_NAME}" />
            <span class="pull-right">
				{assign var=GET_REPORTS_URL value="javascript:Opportunities_Detail_Js.getOpReports('index.php?module="|cat:{$MODULE}|cat:"&action=GetOpListReportTypes&record="|cat:{$SOURCE_RECORD}|cat:"',this);"}
				{*<input type="hidden" id="reportsUrl" value="{$GET_REPORTS_URL}">*}
				<button type="button" class="btn btn-report" id="getOpReports" onclick="{$GET_REPORTS_URL}">
					<strong>{vtranslate("LBL_OPLIST_GETREPORT", $MODULE)}</strong>
				</button>
                <button class="btn btn-success" type="submit">
                    <strong>{vtranslate('LBL_SAVE', $MODULE)}</strong>
                </button>
                <a class="cancelLink" type="reset" onclick="javascript:window.history.back();">
                    {vtranslate('LBL_CANCEL', $MODULE)}
                </a>
            </span>
        </div>
        <div class="sectionsContainer">
            <input type="hidden"
                   name="numSections"
                   value="{$NUM_SECTIONS}" />
            {foreach item=SECTION from=$OPLIST_ARRAY['sections']}
                <table name="opListSectionBlock_{$SECTION['section_id']}"
                       class="table table-bordered equalSplit">
                    <thead>
                        <tr>
                            <th class="blockHeader" colspan="4">
                                {* <img class="cursorPointer alignMiddle blockToggle {if !($IS_HIDDEN)} hide {/if} "
                                     src="{vimage_path('arrowRight.png')}"
                                     data-mode="hide"
                                     data-id=9999{$SECTION['section_id']}>
                                <img class="cursorPointer alignMiddle blockToggle {if ($IS_HIDDEN)} hide {/if}"
                                     src="{vimage_path('arrowDown.png')}"
                                     data-mode="show"
                                     data-id=9999{$SECTION['section_id']}> *}
                                &nbsp;&nbsp;{$SECTION['section_name']}
                                <input type="hidden"
                                       name="sectionOrder_{$SECTION['section_id']}"
                                       value="{$SECTION['section_order']}">
                                <input type="hidden"
                                       name="section_name_{$SECTION['section_id']}"
                                       value="{$SECTION['section_name']}" />
                                <input type="hidden"
                                       name="numQuestions_{$SECTION['section_id']}"
                                       value="{$SECTION['num_questions']}" />
                            </th>
                        </tr>
                    </thead>
                    <tbody class="opListSection_{$SECTION['section_id']}">
                        {foreach item=QUESTION from=$SECTION['questions']}
                            <tr class="question_{$QUESTION['section_id']}_{$QUESTION['question_id']}">
                                <td class='fieldLabel {$WIDTHTYPE}'>
                                    <label class='muted pull-right marginRight10px'>{vtranslate('LBL_OPLIST_QUESTION', $MODULE)}</label>
                                </td>
                                <td class="fieldValue {$WIDTHTYPE}" colspan="3" style="width:80%;">
                                    <span class="value">
                                        {$QUESTION['question']}
                                    </span>
                                    <input type="hidden"
                                           name="question_type_{$QUESTION['section_id']}_{$QUESTION['question_id']}"
                                           value="{$QUESTION['question_type']}" />
                                    <input type='hidden'
                                           name='question_order_{$QUESTION['section_id']}_{$QUESTION['question_id']}'
                                           value='{$QUESTION['question_order']}' />
                                    <input type="hidden"
                                           name="question_{$QUESTION['section_id']}_{$QUESTION['question_id']}"
                                           value="{$QUESTION['question']}" />
                                </td>
                            </tr>
                            {if $QUESTION['question_type'] === 'Text'}
                                <tr class="answer_{$QUESTION['section_id']}_{$QUESTION['question_id']}_text">
                                    <td class='fieldLabel {$WIDTHTYPE}'>
                                        <label class='muted pull-right marginRight10px'>{vtranslate('LBL_OPLIST_ANSWER', $MODULE)}</label>
                                    </td>
                                    <td class="fieldValue {$WIDTHTYPE}" colspan="3" style="width:80%;">
                                        {$LOCALFIELDINFO.mandatory = false}
                                        {$LOCALFIELDINFO.name = 'default_answer_text'}
                                        {$LOCALFIELDINFO.label = 'LBL_OPLIST_ANSWERTEXT'}
                                        {$LOCALFIELDINFO.type = 'text'}
                                        {$INFO = Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($LOCALFIELDINFO))}
                                        <textarea id="answer_text_{$QUESTION['section_id']}_{$QUESTION['question_id']}"
                                                  class="span11 "
                                                  name="answer_text_{$QUESTION['section_id']}_{$QUESTION['question_id']}"
                                                  data-fieldinfo={$INFO} data-validation-engine='validate[{if $LOCALFIELDINFO.mandatory}required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]'>
                                            {if $QUESTION['text_answer'] == NULL && $QUESTION['default_text_answer'] != NULL}{$QUESTION['default_text_answer']}{else}{$QUESTION['text_answer']}{/if}
                                        </textarea>
                                        <input type="hidden" name="default_answer_text_{$QUESTION['section_id']}_{$QUESTION['question_id']}" value="{if $QUESTION['default_text_answer'] != NULL}{$QUESTION['default_text_answer']}{else}{$QUESTION['text_answer']}{/if}" />
                                    </td>
                                </tr>
                            {elseif $QUESTION['question_type'] === 'Yes/No'}
                                <tr class="answer_{$QUESTION['section_id']}_{$QUESTION['question_id']}_bool">
                                    <td class='fieldLabel {$WIDTHTYPE}'>
                                        <label class='muted pull-right marginRight10px'>{vtranslate('LBL_OPLIST_ANSWER', $MODULE)}</label>
                                    </td>
                                    <td class="fieldValue {$WIDTHTYPE}">
                                        {$LOCALFIELDINFO.mandatory = false}
                                        {$LOCALFIELDINFO.name = 'default_answer_bool'}
                                        {$LOCALFIELDINFO.label = 'LBL_OPLIST_ANSWERBOOL'}
                                        {$LOCALFIELDINFO.type = 'boolean'}
                                        {$INFO = Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($LOCALFIELDINFO))}
                                        <input type="hidden"
                                               name="answer_bool_{$QUESTION['section_id']}_{$QUESTION['question_id']}"
                                               value="0">
                                        <input {if $QUESTION['bool_answer'] == 1}checked {/if}
                                               id='answer_bool_{$QUESTION['section_id']}_{$QUESTION['question_id']}'
                                               type="checkbox"
                                               name="answer_bool_{$QUESTION['section_id']}_{$QUESTION['question_id']}"
                                               data-fieldinfo={$INFO} data-validation-engine='validate[{if $LOCALFIELDINFO.mandatory}required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]'>
                                        <input type="hidden"
                                               name="default_answer_bool_{$QUESTION['section_id']}_{$QUESTION['question_id']}"
                                               value="{if $QUESTION['default_bool_answer'] != NULL}{$QUESTION['default_bool_answer']}{else}{$QUESTION['bool_answer']}{/if}">
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
                                        <label class='muted pull-right marginRight10px'>{vtranslate('LBL_OPLIST_ANSWER', $MODULE)}</label>
                                    </td>
                                    <td class="fieldValue {$WIDTHTYPE}">
                                        {assign var="dateFormat" value=$USER_MODEL->get('date_format')}
                                        {$LOCALFIELDINFO.mandatory = false}
                                        {$LOCALFIELDINFO.name = 'default_answer_date'}
                                        {$LOCALFIELDINFO.label = 'LBL_OPLIST_ANSWERDATE'}
                                        {$LOCALFIELDINFO.type = 'date'}
                                        {$LOCALFIELDINFO.dateFormat = $dateFormat}
                                        {$INFO = Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($LOCALFIELDINFO))}
                                        <div class="input-append row-fluid">
                                            <div class="span12 row-fluid date">
                                                <input value="{if $QUESTION['date_answer'] == NULL && $QUESTION['default_date_answer'] != NULL}{$QUESTION['default_date_answer']}{else}{$QUESTION['date_answer']}{/if}"
                                                       id='answer_date_{$QUESTION['section_id']}_{$QUESTION['question_id']}'
                                                       type="text"
                                                       class="dateField"
                                                       data-date-format="{$dateFormat}"
                                                       name="answer_date_{$QUESTION['section_id']}_{$QUESTION['question_id']}"
                                                       data-fieldinfo={$INFO} data-validation-engine='validate[{if $LOCALFIELDINFO.mandatory}required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]'>
                                                <input type="hidden"
                                                       name="default_answer_date_{$QUESTION['section_id']}_{$QUESTION['question_id']}"
                                                       value="{if $QUESTION['default_date_answer'] != NULL}{$QUESTION['default_date_answer']}{else}{$QUESTION['date_answer']}{/if}" />
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
                                        <label class='muted pull-right marginRight10px'>{vtranslate('LBL_OPLIST_ANSWERDATE', $MODULE)}</label>
                                    </td>
                                    <td class="fieldValue {$WIDTHTYPE}">
                                        {assign var="dateFormat" value=$USER_MODEL->get('date_format')}
                                        {$LOCALFIELDINFO.mandatory = false}
                                        {$LOCALFIELDINFO.name = 'default_answer_datetime_date'}
                                        {$LOCALFIELDINFO.label = 'LBL_OPLIST_ANSWERDATETIMEDATE'}
                                        {$LOCALFIELDINFO.type = 'date'}
                                        {$LOCALFIELDINFO.dateFormat = $dateFormat}
                                        {$INFO = Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($LOCALFIELDINFO))}
                                        <div class="input-append row-fluid">
                                            <div class="span12 row-fluid date">
                                                <input value="{if ($QUESTION['datetime_answer_date'] == '0000-00-00' || $QUESTION['datetime_answer_date'] == '') && $QUESTION['default_datetime_answer_date'] != NULL}{$QUESTION['default_datetime_answer_date']}{else}{$QUESTION['datetime_answer_date']}{/if}"
                                                       id='answer_datetime_date_{$QUESTION['section_id']}_{$QUESTION['question_id']}'
                                                       type="text"
                                                       class="dateField"
                                                       data-date-format="{$dateFormat}"
                                                       name="answer_datetime_date_{$QUESTION['section_id']}_{$QUESTION['question_id']}"
                                                       data-fieldinfo={$INFO} data-validation-engine='validate[{if $LOCALFIELDINFO.mandatory}required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]'>
                                                <input type="hidden"
                                                       name="default_answer_datetime_date_{$QUESTION['section_id']}_{$QUESTION['question_id']}"
                                                       value="{if $QUESTION['default_datetime_answer_date'] != NULL}{$QUESTION['default_datetime_answer_date']}{else}{$QUESTION['datetime_answer_date']}{/if}" />
                                                <span class="add-on"><i class="icon-calendar"></i></span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class='fieldLabel {$WIDTHTYPE}'>
                                        <label class='muted pull-right marginRight10px'>{vtranslate('LBL_OPLIST_ANSWERTIME', $MODULE)}</label>
                                    </td>
                                    <td class="fieldValue {$WIDTHTYPE}">
                                        {assign var="TIME_FORMAT" value=$USER_MODEL->get('hour_format')}
                                        {$LOCALFIELDINFO.mandatory = false}
                                        {$LOCALFIELDINFO.name = 'default_answer_datetime_time'}
                                        {$LOCALFIELDINFO.label = 'LBL_OPLIST_ANSWERDATETIMETIME'}
                                        {$LOCALFIELDINFO.type = 'time'}
                                        {$LOCALFIELDINFO.timeFormat = $TIME_FORMAT}
                                        {$INFO = Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($LOCALFIELDINFO))}
                                        <div class="input-append time">
                                            <input value="{if $QUESTION['datetime_answer_time'] == '' && $QUESTION['default_datetime_answer_time'] != NULL}{$QUESTION['default_datetime_answer_time']}{else}{$QUESTION['datetime_answer_time']}{/if}"
                                                   id='answer_datetime_time_{$QUESTION['section_id']}_{$QUESTION['question_id']}'
                                                   type="text"
                                                   class="timepicker-default input-small"
                                                   data-format="{$TIME_FORMAT}"
                                                   name="answer_datetime_time_{$QUESTION['section_id']}_{$QUESTION['question_id']}"
                                                   data-fieldinfo={$INFO} data-validation-engine='validate[{if $LOCALFIELDINFO.mandatory}required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]'>
                                            <input type="hidden"
                                                   name="default_answer_datetime_time_{$QUESTION['section_id']}_{$QUESTION['question_id']}"
                                                   value="{if $QUESTION['default_datetime_answer_time'] != NULL}{$QUESTION['default_datetime_answer_time']}{else}{$QUESTION['datetime_answer_time']}{/if}" />
                                            <span class="add-on cursorPointer">
                                                <i class="icon-time"></i>
                                            </span>
                                        </div>
                                    </td>
                                </tr>
                            {elseif $QUESTION['question_type'] === 'Time'}
                                <tr class="answer_{$QUESTION['section_id']}_{$QUESTION['question_id']}_time">
                                    <td class='fieldLabel {$WIDTHTYPE}'>
                                        <label class='muted pull-right marginRight10px'>{vtranslate('LBL_OPLIST_ANSWER', $MODULE)}</label>
                                    </td>
                                    <td class="fieldValue {$WIDTHTYPE}">
                                        {assign var="TIME_FORMAT" value=$USER_MODEL->get('hour_format')}
                                        {$LOCALFIELDINFO.mandatory = false}
                                        {$LOCALFIELDINFO.name = 'default_answer_time'}
                                        {$LOCALFIELDINFO.label = 'LBL_OPLIST_ANSWERDATETIME'}
                                        {$LOCALFIELDINFO.type = 'time'}
                                        {$LOCALFIELDINFO.timeFormat = $TIME_FORMAT}
                                        {$INFO = Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($LOCALFIELDINFO))}
                                        <div class="input-append time">
                                            <input value="{if $QUESTION['time_answer'] != NULL }{$QUESTION['time_answer']}{else}{$QUESTION['default_time_answer']}{/if}"
                                                   id='answer_time_{$QUESTION['section_id']}_{$QUESTION['question_id']}'
                                                   type="text"
                                                   class="timepicker-default input-small"
                                                   data-format="{$TIME_FORMAT}"
                                                   name="answer_time_{$QUESTION['section_id']}_{$QUESTION['question_id']}"
                                                   data-fieldinfo={$INFO} data-validation-engine='validate[{if $LOCALFIELDINFO.mandatory}required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]'>
                                            <input type="hidden"
                                                   name="default_answer_time_{$QUESTION['section_id']}_{$QUESTION['question_id']}"
                                                   value="{if $QUESTION['default_time_answer'] != NULL}{$QUESTION['default_time_answer']}{else}{$QUESTION['time_answer']}{/if}" />
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
                                        <label class='muted pull-right marginRight10px'>{vtranslate('LBL_OPLIST_ANSWER', $MODULE)}</label>
                                    </td>
                                    <td class="fieldValue {$WIDTHTYPE}">
                                        <div class='row-fluid'>
                                            <span class='span12'>
                                                {$LOCALFIELDINFO.mandatory = false}
                                                {$LOCALFIELDINFO.name = 'default_answer_number'}
                                                {$LOCALFIELDINFO.label = 'LBL_OPLIST_ANSWERNUMBER'}
                                                {$LOCALFIELDINFO.type = 'decimal'}
                                                {$INFO = Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($LOCALFIELDINFO))}
                                                <input id='answer_number_{$QUESTION['section_id']}_{$QUESTION['question_id']}'
                                                       type="number"
                                                       min="0"
                                                       max=""
                                                       step="{if $QUESTION['use_dec'] == true}.25{else}1{/if}"
                                                       class='input-large'
                                                       name='answer_number_{$QUESTION['section_id']}_{$QUESTION['question_id']}'
                                                           value='{if $QUESTION['use_dec']}{if $QUESTION['dec_answer'] == NULL && $QUESTION['default_dec_answer'] != NULL}{$QUESTION['default_dec_answer']}{else}{$QUESTION['dec_answer']}{/if}{else}{if $QUESTION['int_answer'] == NULL && $QUESTION['default_int_answer'] != NULL}{$QUESTION['default_int_answer']}{else}{$QUESTION['int_answer']}{/if}{/if}'
                                                       data-fieldinfo={$INFO} data-validation-engine='validate[{if $LOCALFIELDINFO.mandatory}required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]'>
                                                <input type="hidden"
                                                       name="answer_use_decimal_{$QUESTION['section_id']}_{$QUESTION['question_id']}"
                                                       value="{$QUESTION['use_dec']}" />
                                                <input type="hidden"
                                                       name="default_answer_use_decimal_{$QUESTION['section_id']}_{$QUESTION['question_id']}"
                                                       value="{$QUESTION['use_dec']}" />
                                                <input type="hidden"
                                                       name='default_answer_number_{$QUESTION['section_id']}_{$QUESTION['question_id']}'
                                                       value='{if $QUESTION['use_dec']}{if $QUESTION['default_dec_answer'] != NULL}{$QUESTION['default_dec_answer']}{else}{$QUESTION['dec_answer']}{/if}{else}{if $QUESTION['default_int_answer'] != NULL}{$QUESTION['default_int_answer']}{else}{$QUESTION['int_answer']}{/if}{/if}' />
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
                            {elseif $QUESTION['question_type'] === 'Multiple Choice'}
                                <tr class="answer_{$QUESTION['section_id']}_{$QUESTION['question_id']}_multi">
                                    <td class='fieldLabel {$WIDTHTYPE}'>
                                        <input type='hidden'
                                               name='numOptions_{$QUESTION['section_id']}_{$QUESTION['question_id']}'
                                               value='{$QUESTION['num_options']}' />
                                        <label class='muted pull-right marginRight10px'>{vtranslate('LBL_OPLIST_MULTIANSWERS', $MODULE)}</label>
                                    </td>
                                    <td class="fieldValue {$WIDTHTYPE}" colspan="3" style="width:100%;padding:0 !important">
                                        <table class="table table-bordered equalSplit"
                                               style="padding: 0 !important; border: 0">
                                            <tbody class='opListQuestion' style='display: table-row-group;'>
                                                {foreach item=OPTION from=$QUESTION['multi_options']}
                                                    <input type="hidden"
                                                           name="default_answer_select_multiple_{$QUESTION['section_id']}_{$QUESTION['question_id']}"
                                                           value="{$QUESTION['allow_multiple_answers']}">
                                                    <input type='hidden'
                                                           name='option_order_{$OPTION['section_id']}_{$OPTION['question_id']}_{$OPTION['option_id']}'
                                                           value='{$OPTION['option_order']}'>
                                                    <input type="hidden"
                                                           name="default_multi_option_{$OPTION['section_id']}_{$OPTION['question_id']}_{$OPTION['option_id']}"
                                                           value="{if $OPTION['default_selected'] != NULL}{$OPTION['default_selected']}{else}{$OPTION['selected']}{/if}" />
                                                    <tr class="option_{$OPTION['section_id']}_{$OPTION['question_id']}_{$OPTION['option_id']}">
                                                        <td class="multipleNotAllowed{if $QUESTION['allow_multiple_answers'] == 1} hide{/if}"
                                                            style="width:10%;text-align:center;margin:auto;">
                                                            <input type="hidden"
                                                                   name="MultiOption_prev_{$OPTION['section_id']}_{$OPTION['question_id']}"
                                                                   value="none" />
                                                            <input {if $OPTION['selected']}checked {/if}
                                                                   type="radio"
                                                                   name="MultiOption_{$OPTION['section_id']}_{$OPTION['question_id']}"
                                                                   value="{$OPTION['option_id']}">
                                                        </td>
                                                        <td class="multipleAllowed{if $QUESTION['allow_multiple_answers'] != 1} hide{/if}"
                                                            style="width:10%;text-align:center;margin:auto;">
                                                            {$LOCALFIELDINFO.mandatory = false}
                                                            {$LOCALFIELDINFO.name = 'default_multi_option'}
                                                            {$LOCALFIELDINFO.label = 'LBL_OPLIST_DEFAULTMULTIOPTION'}
                                                            {$LOCALFIELDINFO.type = 'boolean'}
                                                            {$INFO = Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($LOCALFIELDINFO))}
                                                            <input type="hidden"
                                                                   name="multi_option_{$OPTION['section_id']}_{$OPTION['question_id']}_{$OPTION['option_id']}"
                                                                   value="0">
                                                            <input {if $OPTION['selected']}checked {/if}
                                                                   id='multi_option_{$OPTION['section_id']}_{$OPTION['question_id']}_{$OPTION['option_id']}'
                                                                   type="checkbox"
                                                                   name="multi_option_{$OPTION['section_id']}_{$OPTION['question_id']}_{$OPTION['option_id']}"
                                                                   data-fieldinfo={$INFO} data-validation-engine='validate[{if $LOCALFIELDINFO.mandatory}required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]'>
                                                        </td>
                                                        <td style="width:75%;margin:auto;">
                                                            {$LOCALFIELDINFO.mandatory = true}
                                                            {$LOCALFIELDINFO.name = 'multi_option_answer'}
                                                            {$LOCALFIELDINFO.label = 'LBL_OPLIST_MULTIOPTIONANSWER'}
                                                            {$LOCALFIELDINFO.type = 'string'}
                                                            {$INFO = Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($LOCALFIELDINFO))}
                                                            <input id="multi_option_answer_{$OPTION['section_id']}_{$OPTION['question_id']}_{$OPTION['option_id']}"
                                                                   type='hidden'
                                                                   class='input-large'
                                                                   name="multi_option_answer_{$OPTION['section_id']}_{$OPTION['question_id']}_{$OPTION['option_id']}"
                                                                   value="{$OPTION['answer']}"
                                                                   data-fieldinfo={$INFO} data-validation-engine='validate[{if $LOCALFIELDINFO.mandatory}required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]'
                                                                   style="margin-top: 1px;margin-bottom: 1px;margin-left:3px;width:90%">
                                                            <span class="value" style="margin-top: 1px;margin-bottom: 1px;margin-left:3px;width:90%;text-align:left !important;">
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
		<div>
			<span class="pull-right">
				<button type="button" class="btn btn-report" id="getOpReports" onclick="{$GET_REPORTS_URL}">
					<strong>{vtranslate("LBL_OPLIST_GETREPORT", $MODULE)}</strong>
				</button>
                <button class="btn btn-success" type="submit">
                    <strong>{vtranslate('LBL_SAVE', $MODULE)}</strong>
                </button>
                <a class="cancelLink" type="reset" onclick="javascript:window.history.back();">
                    {vtranslate('LBL_CANCEL', $MODULE)}
                </a>
            </span>
		</div>
    </form>
{/if}
{/strip}
