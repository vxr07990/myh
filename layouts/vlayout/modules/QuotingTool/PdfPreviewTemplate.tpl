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
                <h3 id="massEditHeader">Edit PDF</h3>
            </div>
            <form class="form-horizontal" method="post" action="index.php" id="quotingtool_pdftemplate">
                <input type="hidden" name="module" value="{$MODULE}"/>
                <input type="hidden" name="action" value="PDFHandler"/>
                <input type="hidden" name="mode" value="preview_and_edit_pdf"/>
                <input type="hidden" name="transaction_id" value='{$TRANSACTION_ID}'/>
                <input type="hidden" name="record" value="{$RECORDID}"/>
                <input type="hidden" name="template_id" value='{$TEMPLATEID}'/>
                <input type="hidden" name="relmodule" value='{$RELATED_MODULE}'/>
                <textarea name="page_format" class="hide">{$SETTINGS->get('page_format')}</textarea>

                <div name='massEditContent' class="row-fluid">
                    <div class="modal-body">
                        <div class="row-fluid" style="margin: 5px;">
                            <div class="span12">
                                <textarea id="pdf_content" name="pdf_content">{$PDF_CONTENT}</textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="pull-right cancelLinkContainer" style="margin-top:0;">
                        <a class="cancelLink" type="reset" data-dismiss="modal">{vtranslate('LBL_CANCEL', $MODULE)}</a>
                    </div>
                    <button class="btn addButton" type="submit" name="saveButton">
                        <strong>{vtranslate('LBL_EXPORT', $MODULE)}</strong>
                    </button>
                </div>
            </form>
        </div>
    </div>
{/strip}
