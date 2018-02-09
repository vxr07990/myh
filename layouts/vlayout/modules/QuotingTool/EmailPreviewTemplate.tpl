{*<!--
/* ********************************************************************************
 * The content of this file is subject to the Quoting Tool ("License");
 * You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is VTExperts.com
 * Portions created by VTExperts.com. are Copyright(C) VTExperts.com.
 * All Rights Reserved.
 * ****************************************************************************** */
-->*}
{strip}
    <div id="massEditContainer" class='modelContainer'>
        <div id="massEdit">
            <div class="modal-header contentsBackground">
                <button type="button" class="close " data-dismiss="modal" aria-hidden="true">&times;</button>
                <h3 id="massEditHeader">Preview & Send Email</h3>
            </div>
            <form class="form-horizontal" action="index.php" id="quotingtool_emailtemplate" method="post">
                <input type="hidden" name="module" value="{$MODULE}"/>
                <input type="hidden" name="selected_ids" value='{ZEND_JSON::encode($SELECTED_IDS)}' />
	                <input type="hidden" name="action" value="PDFHandler"/>
                <input type="hidden" name="mode" value="preview_and_send_email"/>
                <input type="hidden" name="transaction_id" value='{$TRANSACTION_ID}'/>
                <input type="hidden" name="record" value="{$RECORDID}"/>
                <input type="hidden" name="template_id" value='{$TEMPLATEID}'/>
                <input type="hidden" name="relmodule" value='{$RELATED_MODULE}'/>
                <input type="hidden" name="toemailinfo" value='{ZEND_JSON::encode($TOMAIL_INFO)}' />
                <input type="hidden"  name="to" value='{ZEND_JSON::encode($TO)}' />
                <input type="hidden"  name="toMailNamesList" value='{ZEND_JSON::encode($TOMAIL_NAMES_LIST)}' />
                <textarea name="page_format" class="hide">{$SETTINGS->get('page_format')}</textarea>

                <div name='massEditContent' class="row-fluid">
                    <div class="modal-body">
                        <div class="row-fluid toEmailField padding10" style="width: 700px">
                            <span class="span8">
                                <span class="row-fluid">
                                    <span class="span2">{vtranslate('LBL_TO',$MODULE)}<span class="redColor">*</span></span>
                                    {if !empty($TO)}
                                        {assign var=TO_EMAILS value=","|implode:$TO}
                                    {/if}
                                    <span class="span9">
                                    <input id="emailField" name="toEmail" type="text" class="row-fluid autoComplete sourceField select2"
                                           value="{$TO_EMAILS}" data-validation-engine="validate[required, funcCall[Vtiger_To_Email_Validator_Js.invokeValidation]]"
                                           data-fieldinfo='{$FIELD_INFO}'
                                           {if !empty($SPECIAL_VALIDATOR)}data-validator='{Zend_Json::encode($SPECIAL_VALIDATOR)}'{/if}/>
                                </span>
                            </span>
                            </span>
                            <span class="span4">
                                <span class="row-fluid">
                                    <span class="span10">
                                        <div class="input-prepend">
                                            <span class="pull-right">
                                                <span class="add-on cursorPointer" name="clearToEmailField"><i class="icon-remove-sign" title="{vtranslate('LBL_CLEAR', $MODULE)}"></i></span>
                                                <select class="chzn-select emailModulesList" style="width:150px;">
                                                    <optgroup>
                                                        {foreach item=MODULE_NAME from=$EMAIL_RELATED_MODULES}

                                                            <option value="{$MODULE_NAME}" {if $MODULE_NAME eq $RELATED_MODULE} selected {/if}>{vtranslate($MODULE_NAME,$MODULE_NAME)}</option>
                                                        {/foreach}
                                                    </optgroup>
                                                </select>
                                            </span>
                            </div>
                                    </span>
                                    <span class="input-append span2 margin0px">
                                            <span class="add-on selectEmail cursorPointer"><i class="icon-search" title="{vtranslate('LBL_SELECT', $MODULE)}"></i></span>
                                    </span>
                                </span>
                            </span>
                        </div>
                        <div id="multiEmailContainer">
                                {if $EMAIL_FIELD_LIST}
                                    {assign var=i value=0}
                                    {assign var=allEmailArr value=[]}

                                    {foreach item=EMAIL_FIELD_LABEL key=EMAIL_FIELD_NAME from=$EMAIL_FIELD_LIST name=emailFieldIterator}
                                        {append var=allEmailArr value=$EMAIL_FIELD_LABEL index=$i}

                                    <input type="hidden"  class="emailField" name="selectedEmail[{$i++}]" value='{$EMAIL_FIELD_NAME}' />
                                    {/foreach}

                                <div class="row-fluid padding10" style="width: 700px">
                                <span class="span8">
                                    <span class="row-fluid">
                                        <span class="span2">{vtranslate('CC',$MODULE)}</span>
                                        <span class="span9">
                                        <input type="hidden" class="select2 select2-tags"
                                                   name="ccValues" data-tags='{$allEmailArr|json_encode}'
                                               style="width:100%;" />
                                        </span>
                                    </span>
                                </span>
                                        </div>

                                <div class="row-fluid padding10" style="width: 700px">
                                <span class="span8">
                                    <span class="row-fluid">
                                        <span class="span2">{vtranslate('BCC',$MODULE)}</span>
                                        <span class="span9">
                                        <input type="hidden" class="select2 select2-tags"
                                                   name="bccValues" data-tags='{$allEmailArr|json_encode}'
                                               style="width:100%;" />
                                        </span>
                                    </span>
                                </span>
                                    </div>
                                {else}
                                    {vtranslate('Does not have any email to select.', $MODULE)}
                                {/if}
                            <div class="row-fluid" style="margin: 5px;">
                                <div class="span12">
                                <input type="text" style="width: 98%;" class="input-large" id="email_subject" name="email_subject"
                                placeholder="Email Subject" value="{$EMAIL_SUBJECT}"/>
                                </div>
                            </div>
                        </div>

                        <div class="row-fluid">
                            <div class="span12">
                                <ul class="nav nav-pills">
                                    <li class="active">
                                        <a href="javascript:void(0);" data-target=".edit-email"
                                           data-toggle="tab" data-tab-name="edit-email">{vtranslate('Edit Email', $MODULE)}</a>
                                    </li>
                                    <li>
                                        <a href="javascript:void(0);" data-target=".edit-pdf"
                                           data-toggle="tab" data-tab-name="edit-pdf">{vtranslate('Edit PDF', $MODULE)}</a>
                                    </li>
                                </ul>
                            </div>
                        </div>

                        <div class="row-fluid" style="margin: 5px;">
                            <div class="span12">
                                <div class="tab-content overflowVisible">
                                    <div id="edit-email" class="edit-email tab-pane active">
                                        <textarea placeholder="Email Content" id="email_content" name="email_content">{$EMAIL_CONTENT}</textarea>
                                    </div>
                                    <div id="edit-pdf" class="edit-pdf tab-pane">
                                        <textarea id="pdf_content" name="pdf_content">{$PDF_CONTENT}</textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="pull-left custom_proposal_link">
                        <label class="checkbox check_attach_file">
                            <input type="checkbox" name="check_attach_file" />
                            <span>{vtranslate('EMAIL_ATTACH_DOCUMENT', $MODULE)}</span>
                        </label>
                        <a href="{$CUSTOM_PROPOSAL_LINK}" target="_blank">{vtranslate('EMAIL_DOCUMENT_PREVIEW', $MODULE)}</a>
                    </div>
                    <div class="pull-right cancelLinkContainer" style="margin-top:0;">
                        <a class="cancelLink" type="reset" data-dismiss="modal">{vtranslate('LBL_CANCEL', $MODULE)}</a>
                    </div>
                    <button class="btn addButton" type="submit" name="saveButton"
                            style="font-weight: bold;">{vtranslate('LBL_SEND', $MODULE)}</button>
                </div>
            </form>
        </div>
    </div>
{/strip}
