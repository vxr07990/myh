/* ********************************************************************************
 * The content of this file is subject to the Quoting Tool ("License");
 * You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is VTExperts.com
 * Portions created by VTExperts.com. are Copyright(C) VTExperts.com.
 * All Rights Reserved.
 * ****************************************************************************** */

/* Include on link table */

/** @class QuotingToolJS */
jQuery.Class("QuotingToolJS", {}, {
    MODULE: 'QuotingTool',
    detailViewButtoncontainer: null,

    /**
     * Fn - getSelectedTemplates
     * @returns {*}
     */
    getSelectedTemplates: function () {
        var lstTemplates = jQuery('#lstTemplates');
        var selected = lstTemplates.val();

        if (selected == null || selected.length == 0) {
            alert(app.vtranslate('Please select template'));
            return null;
        }

        if (typeof selected !== 'Array') {
            return selected;
        }

        // When multi select
        var strSelected = '';
        for (var i = 0; i < selected.length; i++) {
            strSelected += selected[i] + '+';
        }

        strSelected = strSelected.substring(0, strSelected.length - 1);

        return strSelected;
    },

    /**
     * Function returns the record id
     */
    getRecordId: function () {
        var view = jQuery('[name="view"]').val();
        var recordId;
        if (view == "Edit") {
            recordId = jQuery('[name="record"]').val();
        } else if (view == "Detail") {
            recordId = jQuery('#recordId').val();
        }
        return recordId;
    },

    /**
     * Fn - registerWidgetActions
     */
    registerWidgetActions: function () {
        var thisInstance = this;
        var module = app.getModuleName();
        var recordId = thisInstance.getRecordId();

        // Export PDF
        jQuery(document).on('click', '[data-action="export"]', function () {
            var thisFocus = $(this);
            // Priority: 1. current button; 2. select box
            var templateId = thisFocus.data('template');

            if (!templateId) {
                templateId = thisInstance.getSelectedTemplates();
            }

            if (templateId) {
                document.location.href = 'index.php?module=QuotingTool&action=PDFHandler&mode=export&relmodule='
                    + module + '&record=' + recordId + '&template_id=' + templateId;
            }
        });

        // Send email
        jQuery(document).on('click', '[data-action="send_email"]', function () {
            var thisFocus = $(this);
            // Priority: 1. current button; 2. select box
            var templateId = thisFocus.data('template');

            if (!templateId) {
                templateId = thisInstance.getSelectedTemplates();
            }

            if (templateId) {
                var progressIndicatorElement = jQuery.progressIndicator({
                    'position': 'html',
                    'blockInfo': {
                        'enabled': true
                    }
                });
                AppConnector.request('index.php?module=QuotingTool&view=SelectEmailFields&mode=send_email&relmodule='
                    + module + '&record=' + recordId + '&template_id=' + templateId).then(
                    function (data) {
                        progressIndicatorElement.progressIndicator({'mode': 'hide'});

                        if (data) {
                            var callBackFunction = function (data) {
                                var form = jQuery('#SendEmailFormStep1');
                                var params = app.validationEngineOptions;
                                params.onValidationComplete = function (form, valid) {
                                    if (valid) {
                                        app.hideModalWindow();
                                        var progressIndicatorElement = jQuery.progressIndicator({
                                            'message': 'Sending...',
                                            'position': 'html',
                                            'blockInfo': {
                                                'enabled': true
                                            }
                                        });
                                        var data = form.serializeFormData();
                                        AppConnector.request(data).then(
                                            function (response) {
                                                progressIndicatorElement.progressIndicator({'mode': 'hide'});

                                                if (response.success == true) {
                                                    Vtiger_Helper_Js.showMessage({
                                                        type: 'success',
                                                        text: response.result.message
                                                    });
                                                } else {
                                                    Vtiger_Helper_Js.showMessage({
                                                        type: 'error',
                                                        text: response.error.message
                                                    });
                                                }
                                            },
                                            function (error) {
                                                progressIndicatorElement.progressIndicator({'mode': 'hide'});
                                                //TODO : Handle error
                                                Vtiger_Helper_Js.showMessage({
                                                    type: 'error',
                                                    text: error
                                                });
                                            }
                                        );

                                        return valid;
                                    }
                                };
                                form.validationEngine(params);

                                form.submit(function (e) {
                                    e.preventDefault();
                                })
                            };
                            app.showModalWindow(data, function (data) {
                                if (typeof callBackFunction == 'function') {
                                    callBackFunction(data);
                                }
                            }, {'width': '350px'})
                        }
                    }
                );
            }
        });

        // Preview and send email
        jQuery(document).on('click', '[data-action="preview_and_send_email"]', function () {
            var thisFocus = $(this);
            // Priority: 1. current button; 2. select box
            var templateId = thisFocus.data('template');

            if (!templateId) {
                templateId = thisInstance.getSelectedTemplates();
            }

            if (templateId) {
                // Show indicator
                var progressIndicatorElement = jQuery.progressIndicator({
                    'position': 'html',
                    'blockInfo': {
                        'enabled': false
                    }
                });

                var actionParams = {
                    "type": "POST",
                    "url": 'index.php?module=QuotingTool&view=EmailPreviewTemplate&record=' + recordId + '&relmodule=' + module + '&template_id=' + templateId,
                    "dataType": "html",
                    "data": {
                        module: 'QuotingTool',
                        view: 'EmailPreviewTemplate'
                    }
                };
                AppConnector.request(actionParams).then(function (data) {
                    // Hide indicator
                    progressIndicatorElement.progressIndicator({'mode': 'hide'});

                    if (data) {
                        data = $(data);
                        var width = 800;
                        var inPageFormat = data.find('[name="page_format"]');
                        // Change page format
                        var page_format = inPageFormat.val();
                        if (page_format) {
                            page_format = JSON.parse(page_format);
                            width = page_format.dimension.width;
                        }

                        app.showModalWindow(data, function (html) {
                            thisInstance.registerEventForEmailPopup(progressIndicatorElement, html);
                            var emailEditInstance = new QuotingTool_MassEdit_Js();
                            emailEditInstance.registerEvents();
                        }, {'width': width + 'px'});
                    }
                });
            }
        });

        // Preview and edit pdf
        jQuery(document).on('click', '[data-action="preview_and_edit_pdf"]', function () {
            var thisFocus = $(this);
            // Priority: 1. current button; 2. select box
            var templateId = thisFocus.data('template');

            if (!templateId) {
                templateId = thisInstance.getSelectedTemplates();
            }

            if (templateId) {
                // Show indicator
                var progressIndicatorElement = jQuery.progressIndicator({
                    'position': 'html',
                    'blockInfo': {
                        'enabled': false
                    }
                });

                var actionParams = {
                    "type": "POST",
                    "url": 'index.php?module=QuotingTool&view=PdfPreviewTemplate&record=' + recordId + '&relmodule='
                            + module + '&template_id=' + templateId,
                    "dataType": "html",
                    "data": {
                        module: 'QuotingTool',
                        view: 'PdfPreviewTemplate'
                    }
                };
                AppConnector.request(actionParams).then(function (data) {
                    // Hide indicator
                    progressIndicatorElement.progressIndicator({'mode': 'hide'});

                    if (data) {
                        data = $(data);
                        var width = 800;
                        var inPageFormat = data.find('[name="page_format"]');
                        // Change page format
                        var page_format = inPageFormat.val();
                        if (page_format) {
                            page_format = JSON.parse(page_format);
                            width = page_format.dimension.width;
                        }

                        app.showModalWindow(data, function (html) {
                            thisInstance.registerEventForPdfPopup(html);
                        }, {'width': width + 'px'});
                    }
                });
            }
        });

        // Download PDF with signature
        jQuery(document).on('click', '[data-action="download_with_signature"]', function () {
            var templateId = thisInstance.getSelectedTemplates();

            if (templateId) {
                document.location.href = 'index.php?module=QuotingTool&action=PDFHandler&mode=download_with_signature&relmodule='
                    + module + '&record=' + recordId + '&template_id=' + templateId;
            }
        });

    },

    /**
     * @param progressIndicatorElement
     * @param html
     */
    registerEventForEmailPopup: function (progressIndicatorElement, html) {
        html = $(html);
        var thisInstance = this;
        var blockMsg = html.closest('.blockUI.blockMsg');
        var padding = 120;  // px
        var width = 800;
        var inPageFormat = html.find('[name="page_format"]');
        // Change page format
        var page_format = inPageFormat.val();
        if (page_format) {
            page_format = JSON.parse(page_format);
            width = page_format.dimension.width;
        }

        width = width - padding;
        thisInstance.registerEmailTags();

        var formEmail = jQuery('#quotingtool_emailtemplate');
        var inEmailSubject = formEmail.find('#email_subject');
        var inEmailContent = formEmail.find('#email_content');
        var inPdfContent = formEmail.find('#pdf_content');

        // Email content
        var editorEmailContent = CKEDITOR.replace('email_content', {
            fullPage: true,
            skin: 'office2013',
            removePlugins: 'magicline',
            resize_dir: 'both',
            height: '200px',
            width: width + 'px', // for PDF size
            toolbar: [
                {name: 'clipboard', items: ['Undo', 'Redo']},
                {name: 'tools', items: ['Source', 'Maximize', 'Preview']},
                {
                    name: 'editing',
                    groups: ['find', 'selection', 'spellchecker'],
                    items: ['Find', 'Replace', 'SelectAll', 'Scayt']
                },
                /*'/',*/
                {name: 'styles', items: ['Styles', 'Font', 'FontSize']},
                {name: 'colors', items: ['TextColor', 'BGColor']},
                '/',
                {name: 'insert', items: ['Image', 'Table']},
                {name: 'links', items: ['Link', 'Unlink']},
                {
                    name: 'basicstyles',
                    items: ['Bold', 'Italic', 'Underline', 'Strike', 'Subscript', 'Superscript', '-', 'RemoveFormat']
                },
                {
                    name: 'paragraph',
                    //groups: ['list', 'indent', 'blocks', 'align', 'bidi'],
                    //items: ['Blockquote', 'CreateDiv', '-', 'BidiLtr', 'BidiRtl']
                    items: ['NumberedList', 'BulletedList', 'Outdent', 'Indent', 'JustifyLeft', 'JustifyCenter',
                        'JustifyRight', 'JustifyBlock', 'BidiLtr', 'BidiRtl']
                },
                {name: 'about', items: ['About']}
            ]
        });

        CKEDITOR.instances['email_content'].on("instanceReady", function (event) {
            // To allow resize the box
            blockMsg.css({
                width: 'auto'
            });
            // Make draggable
            blockMsg.draggable({
                handle: ".modal-header"
            });
        });

        // PDF content
        var editorPdfContent = CKEDITOR.replace('pdf_content', {
            fullPage: true,
            skin: 'office2013',
            removePlugins: 'magicline',
            height: '200px',
            width: width + 'px', // for PDF size
            resize_dir: 'both',
            toolbar: [
                {name: 'tools', items: ['Source', 'Maximize']},
                {name: 'styles', items: ['Styles', 'Font', 'FontSize']},
                {name: 'colors', items: ['TextColor', 'BGColor']},
                {name: 'links', items: ['Link', 'Unlink']},
                {name: 'basicstyles', items: ['Bold', 'Italic', 'Underline', 'Strike', '-', 'RemoveFormat']},
                {name: 'paragraph', items: ['JustifyLeft', 'JustifyCenter', 'JustifyRight']},
                {
                    name: 'editing',
                    groups: ['spellchecker'],
                    items: ['Scayt']
                },
                {name: 'about', items: ['About']}
            ]
        });

        // Change action and text between Send Email or Export PDF
        $('.nav.nav-pills').on('click', '[data-toggle="tab"]', function () {
            var focus = $(this);
            var tabName = focus.data('tab-name');
            // var activeTabContent = formEmail.find('.tab-content .tab-pane.active');
            // var activeTabContentId = activeTabContent.attr('id');
            var formMode = formEmail.find('[name="mode"]');
            var formSubmitButton = formEmail.find('[type="submit"]');

            if (tabName == 'edit-email') {
                formMode.val('preview_and_send_email');
                formSubmitButton.text(app.vtranslate('Send'));
            } else if (tabName == 'edit-pdf') {
                formMode.val('preview_and_edit_pdf');
                formSubmitButton.text(app.vtranslate('Export'));
            }
        });

        // When update email content
        formEmail.submit(function (event) {
            var activeTabContent = formEmail.find('.tab-content .tab-pane.active');
            var activeTabContentId = activeTabContent.attr('id');
            // console.log('activeTabContentId =', activeTabContentId);
            if (activeTabContentId == 'edit-pdf') {
                return true;
            }

            event.preventDefault();
            var toEmail = formEmail.find("#emailField").val();
            if (activeTabContentId == 'edit-email' && toEmail.length == 0) {
                Vtiger_Helper_Js.showMessage({
                    type: 'error',
                    text: 'Please select at least one email'
                });

                return;
            }

            // Show indicator
            progressIndicatorElement.progressIndicator({
                'mode': 'show',
                'message': 'Sending...',
                'position': 'html',
                'blockInfo': {
                    'enabled': true
                }
            });

            inEmailSubject.val(QuotingToolUtils.base64Encode(inEmailSubject.val()));
            inEmailContent.val(QuotingToolUtils.base64Encode(editorEmailContent.getData()));
            inPdfContent.val(QuotingToolUtils.base64Encode(editorPdfContent.getData()));
            var data = formEmail.serializeFormData();

            AppConnector.request(data).then(
                function (response) {
                    if (response.success == true) {
                        Vtiger_Helper_Js.showMessage({
                            type: 'success',
                            text: response.result.message
                        });
                    } else {
                        Vtiger_Helper_Js.showMessage({
                            type: 'error',
                            text: response.error.message
                        });
                    }
                },
                function (error) {
                    console.log('error =', error);
                }
            ).done(function () {
                progressIndicatorElement.progressIndicator({'mode': 'hide'});
                // Hide modal
                app.hideModalWindow();
            });
        });
    },

    /**
     * @param html
     */
    registerEventForPdfPopup: function (html) {
        html = $(html);
        var blockMsg = html.closest('.blockUI.blockMsg');
        var padding = 120;  // px
        var width = 800;
        var inPageFormat = html.find('[name="page_format"]');
        // Change page format
        var page_format = inPageFormat.val();
        if (page_format) {
            page_format = JSON.parse(page_format);
            width = page_format.dimension.width;
        }

        width = width - padding;

        CKEDITOR.replace('pdf_content', {
            fullPage: true,
            skin: 'office2013',
            removePlugins: 'magicline',
            height: '500px',
            width: width + 'px', // for PDF size
            resize_dir: 'both',
            toolbar: [
                {name: 'tools', items: ['Source', 'Maximize']},
                {name: 'styles', items: ['Styles', 'Font', 'FontSize']},
                {name: 'colors', items: ['TextColor', 'BGColor']},
                {name: 'links', items: ['Link', 'Unlink']},
                {name: 'basicstyles', items: ['Bold', 'Italic', 'Underline', 'Strike', '-', 'RemoveFormat']},
                {name: 'paragraph', items: ['JustifyLeft', 'JustifyCenter', 'JustifyRight']},
                {
                    name: 'editing',
                    groups: ['spellchecker'],
                    items: ['Scayt']
                },
                {name: 'about', items: ['About']}
            ]
        });

        CKEDITOR.instances['pdf_content'].on("instanceReady", function (event) {
            // To allow resize the box
            blockMsg.css({
                width: 'auto'
            });
            // Make draggable
            blockMsg.draggable({
                handle: ".modal-header"
            });
        });
    },

    registerWidgetButtons: function () {
        var thisInstance = this;
        var module = app.getModuleName();
        var view = app.getViewName();
        var record = thisInstance.getRecordId();

        // Add Quoting Tool button
        if (view == "Detail" && record != undefined) {
            //var progressIndicatorInstance = $.progressIndicator({});
            var actionParams = {
                "action": "ActionAjax",
                "mode": "getTemplate",
                "module": thisInstance.MODULE,
                "record": record,
                "rel_module": module
            };

            AppConnector.request(actionParams).then(
                function (response) {
                    if (response) {
                        var templates = response.result;
                        var button = jQuery('<span class="btn-group"><button class="btn btn-quoting_tool"><strong>Document Designer</strong></button></span>');

                        var themeSettings = QuotingToolUtils.getThemeSettings();
                        button.find('.btn-quoting_tool').css({
                            'background-color': themeSettings['background-color'],
                            'color': themeSettings['color']
                        });

                        if (templates.length > 0) {
                            var firstButton = thisInstance.detailViewButtoncontainer.find('.btn-toolbar > span:nth-child(1)"');
                            var btnQuotingTool  = firstButton.find('.btn-quoting_tool');
                            if(btnQuotingTool.length == 0) {
                                firstButton.before(button);
                            }
                            button.on('click', function () {
                                thisInstance.showWidgetModal(templates);
                            });
                        }

                        //progressIndicatorInstance.hide();

                    }
                },
                function (error) {
                    //progressIndicatorInstance.hide();
                    console.log('error =', error);
                }
            );
        }
    },

    /**
     * Fn - showWidgetModal
     * @param templates
     */
    showWidgetModal: function (templates) {
        var html = '<div id="modalQuotingToolWidget" class="modal-quotingtool-widget">'
            + '<div class="modal-header">'
            + '<button type="button" class="close" data-dismiss="modal" aria-label="Close">'
            + '<span aria-hidden="true">&times;</span>'
            + '</button>'
            + '<h4 class="modal-title" id="myModalLabel">Document Designer (Email/PDF)</h4>'
            + '</div>'
            + '<div class="modal-body">'
            + '<form method="post" action="">'
            + '<table id="tableQuotingToolWidget">'
            + '<thead>'
            + '<th>Template Name</th>'
            + '<th class="actions">Export</th>'
            + '<th class="actions">Edit</th>'
            + '<th class="actions">Email</th>'
            + '</thead>'
            + '<tbody>';
        var template = null;

        for (var i = 0; i < templates.length; i++) {
            template = templates[i];

            html += '<tr>' +
                '<td>' + template.filename + '</td>' +
                '<td><a href="javascript:;" data-action="export" data-template="' + template.id + '">' +
                '<img src="layouts/vlayout/modules/QuotingTool/resources/img/icons/widget-pdf.png" /></a></td>' +
                '<td><a href="javascript:;" data-action="preview_and_edit_pdf" data-template="' + template.id + '">' +
                '<img src="layouts/vlayout/modules/QuotingTool/resources/img/icons/pdf-xchange-editor-24x24.png" /></a></td>' +
                '<td><a href="javascript:;" data-action="preview_and_send_email" data-template="' + template.id + '">' +
                '<img src="layouts/vlayout/modules/QuotingTool/resources/img/icons/widget-mail.png" /></td>' +
                '</tr>'
        }

        html += '</tbody>'
            + '</table>'
            + '</form>'
            + '</div>'
            + '</div>';

        app.showModalWindow(html, '#', function (data) {
        }, {'width': '600px'});
    },

    registerEmailTags: function () {
        var selectTags = jQuery('.select2-tags');

        selectTags.each(function () {
            var focus = jQuery(this);
            var tags = focus.data('tags');
            if (typeof tags === 'undefined' || !tags) {
                tags = [];
            }
            var select2params = {tags: tags/*, tokenSeparators: [","]*/};
            app.showSelect2ElementView(focus, select2params);
        });
    },

    registerEvents: function () {
        var thisInstance = this;
        thisInstance.registerWidgetActions();
        thisInstance.registerWidgetButtons();
    }
});

jQuery(document).ready(function () {
    // Fix auto add resizeable to textarea on IE
    if (jQuery.isFunction(jQuery.fn.resizable)) {
        jQuery('#quoting_tool-body').find("textarea.hide")
            .resizable('destroy')
            .removeAttr('style');
    }
    $.getScript("layouts/vlayout/modules/QuotingTool/resources/MassEdit.js")

    var instance = new QuotingToolJS();
    instance.detailViewButtoncontainer = jQuery('.detailViewButtoncontainer');
    instance.registerEvents();
});
