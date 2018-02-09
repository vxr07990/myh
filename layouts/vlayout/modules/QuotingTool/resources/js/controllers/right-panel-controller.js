(function ($) {
    'use strict';

    var controllers = angular.module('AppControllers');

    controllers.config(
        function ($stateProvider) {
            $stateProvider
                .state('base.content', {
                    url: 'content',
                    views: {
                        'right_panel_tool_items@': {
                            templateUrl: 'layouts/vlayout/modules/QuotingTool/resources/js/views/right_panel/content.html',
                            controller: 'CtrlAppRightPanelContent'
                        }
                    }
                })
                .state('base.general', {
                    url: 'general',
                    views: {
                        'right_panel_tool_items@': {
                            templateUrl: 'layouts/vlayout/modules/QuotingTool/resources/js/views/right_panel/general.html',
                            controller: 'CtrlAppRightPanelGeneral'
                        }
                    }
                })
                .state('base.history', {
                    url: 'history',
                    views: {
                        'right_panel_tool_items@': {
                            templateUrl: 'layouts/vlayout/modules/QuotingTool/resources/js/views/right_panel/history.html',
                            controller: 'CtrlAppRightPanelHistory'
                        }
                    }
                });
        });

    controllers.controller('CtrlAppRightPanel',
        function ($rootScope, $scope, AppToolbar, $timeout, $translate, PageTitle, Template, AppUtils, GlobalConfig) {
            $rootScope.section = $rootScope.SECTIONS.BLOCKS;
            $rootScope.sectionVisible = true;
            $rootScope.sectionDisabled = false;
            $scope.emailTemplate = AppToolbar.email_template;
            $scope.emailTemplateSettings = AppToolbar.email_template.settings;

            $scope.downloadPDF = function ($event) {
                $event.preventDefault();

                if (!$rootScope.app.model.id) {
                    AppHelper.showMessage($translate.instant('Please save template before download'));

                    return;
                }

                // Redirect download link
                window.location.href = 'index.php?module=QuotingTool&action=PDFHandler&mode=download&record=' + $rootScope.app.model.id;
            };

            $scope.showEmailForm = function ($event) {
                $event.preventDefault();

                $rootScope.app.progressIndicatorElement.progressIndicator({'mode': 'show'});

                AppUtils.loadTemplate($scope, $scope.emailTemplate.template, true, function (html) {
                    AppHelper.showModalWindow(html, '#', function (html) {
                        $rootScope.app.progressIndicatorElement.progressIndicator({'mode': 'hide'});
                        // Clear overlay modal
                        AppHelper.clearOverlayModal(html);
                        $rootScope.registerEventFocusInput(html);
                    }, $scope.emailTemplate.css);
                });
            };

            $scope.saveEmailTemplate = function () {
                // Hide modal
                AppHelper.hideModalWindow();

                if ($rootScope.app.model.id) {
                    // Update email to db
                    Template.save({
                        record: $rootScope.app.model.id,
                        email_subject: QuotingToolUtils.base64Encode($rootScope.app.model.email_subject),
                        email_content: QuotingToolUtils.base64Encode($rootScope.app.model.email_content)
                    }, function (response) {
                        if (response.success == true) {
                        } else {
                            AppHelper.showMessage(response.error.message)
                        }
                    });
                }
            };

            /**
             * Fn - showSection
             * @param section
             */
            $scope.showSection = function (section) {
                $rootScope.section = section;

                // Show default
                switch (section) {
                    case $rootScope.SECTIONS.THEMES:
                        $rootScope.blockSectionId = $rootScope.SECTIONS.GENERAL_BACKGROUND;
                        break;
                    case $rootScope.SECTIONS.DECISION:
                        $rootScope.blockSectionId = $rootScope.SECTIONS.GENERAL_ACCEPT;
                        break;
                    default:
                        break;
                }

            };

            /**
             * Fn - toggleSection
             *
             * Check is contenteditable
             * @link http://stackoverflow.com/questions/1318076/jquery-hasattr-checking-to-see-if-there-is-an-attribute-on-an-element
             * @param $event
             */
            $scope.toggleSection = function ($event) {
                var target = $($event.target);
                var editable = false;
                var contenteditable = target.attr('contenteditable');
                if (typeof contenteditable !== 'undefined' && contenteditable == 'true') {
                    editable = true;
                }

                // Only toggle show block when is not editable
                if (typeof $event !== 'undefined' && !editable) {
                    var container = target.closest('.block-section');
                    var sectionId = container.data('section-id');

                    var oldBlockSectionId = $rootScope.blockSectionId;
                    $rootScope.blockSectionId = sectionId;

                    // Toggle section visible
                    if (oldBlockSectionId == sectionId) {
                        $rootScope.sectionVisible = !$rootScope.sectionVisible;
                    } else {
                        $rootScope.sectionVisible = true;
                    }
                }
            };

            var registerFilenameChange = function () {
                $rootScope.app.form.on('change', '[name="tmp_filename"]', function () {
                    PageTitle.set($rootScope.app.model.filename);

                    if ($rootScope.app.model.id) {
                        // Update filename to db
                        Template.save({
                            record: $rootScope.app.model.id,
                            filename: $rootScope.app.model.filename
                        }, function (response) {
                            if (response.success == true) {
                            } else {
                                AppHelper.showMessage(response.error.message)
                            }
                        });
                    }
                });
            };
            var registerAgentChange = function () {
                $rootScope.app.form.on('change', '[name="tmp_agentid"]', function (){
                    // TODO: remove it
                    CKEDITOR.config.imageBrowser_listUrl = $rootScope.app.config.base
                        + GlobalConfig.IMG_URI_FETCH + '&agentid=' + $rootScope.app.model.agentid;
                    AppToolbar.base_editor.settings.filebrowserImageUploadUrl = $rootScope.app.config.base
                        + GlobalConfig.IMG_URI_UPLOAD + '&agentid=' + $rootScope.app.model.agentid;
                    CKEDITOR.timestamp = Date.now();

                    if ($rootScope.app.model.id) {
                        // Update filename to db
                        Template.save({
                            record: $rootScope.app.model.id,
                            agentid: $rootScope.app.model.agentid
                        }, function (response) {
                            if (response.success == true) {
                            } else {
                                AppHelper.showMessage(response.error.message)
                            }
                        });
                    }
                });
            };
            /**
             * Fn - addBlock
             * @param {Object} block
             * @param {String=} type
             * @param {Object=} focus
             */
            $scope.addBlock = function (block, type, focus) {
                // Main page content
                var content = $rootScope.app.container.find('.quoting_tool-content');
                // (Option) - Type for append
                if (typeof type === 'undefined') {
                    type = null;
                }

                // (Option) - Element to append
                if (typeof focus === 'undefined') {
                    focus = null;
                }

                // Focus content on the last focus page
                /**
                 * Check whether a jQuery element is in the DOM
                 * @link http://stackoverflow.com/questions/3086068/how-do-i-check-whether-a-jquery-element-is-in-the-dom
                 */
                if ($rootScope.app.last_focus_page !== null && $.contains(document, $rootScope.app.last_focus_page[0])) {
                    content = $rootScope.app.last_focus_page;
                }

                // Only append page header, page footer, page break in normal page (is not cover page)
                if (block == $rootScope.app.data.blocks.page_header || block == $rootScope.app.data.blocks.page_footer || block == $rootScope.app.data.blocks.page_break) {
                    var pageContent = $rootScope.app.container.find('.quoting_tool-content:not([data-page-name="cover_page"])');
                    if (pageContent.length > 0) {
                        content = pageContent[0];
                    }
                }

                content = $(content);

                var contentMain = content.find('.quoting_tool-content-main');
                var contentHeader = content.find('.quoting_tool-content-header');
                var contentFooter = content.find('.quoting_tool-content-footer');
                var coverPage = $rootScope.app.container.find('.quoting_tool-content[data-page-name="cover_page"]');

                AppUtils.loadTemplate($scope, block.template, true, function (html) {
                    html = $(html);
                    // Append data
                    if (block == $rootScope.app.data.blocks.cover_page) {
                        if (coverPage.length === 0) {
                            html.insertBefore(content);
                            // Change main to all page
                            contentMain = $rootScope.app.container.find('.quoting_tool-content-main');
                            $rootScope.app.last_focus_page = html;
                        } else {
                            AppHelper.showMessage($translate.instant('Cover page available'));
                        }
                    } else if (block == $rootScope.app.data.blocks.page_header) {
                        if (contentHeader.is(':empty')) {
                            contentHeader.append(html);
                        } else {
                            AppHelper.showMessage($translate.instant('Page header available'));
                        }
                    } else if (block == $rootScope.app.data.blocks.page_footer) {
                        if (contentFooter.is(':empty')) {
                            contentFooter.append(html);
                        } else {
                            AppHelper.showMessage($translate.instant('Page footer available'));
                        }
                    } else {
                        // At the begin of the page
                        if (type == 'after' && focus && focus.length == 0) {
                            type = 'prepend';
                        }

                        if (type == 'after') {
                            focus.after(html);
                        } else if (type == 'prepend') {
                            contentMain.prepend(html);
                        } else {
                            contentMain.append(html);
                        }
                    }
                    ResizeSensor(html[0], function(){
                        var objActions1 = jQuery(html[0]).find('.content-container.quoting_tool-draggable');
                        var objAction1 = null;
                        for (var i = 0; i < objActions1.length; i++) {
                            objAction1 = $(objActions1[i]);
                            // Re-calculate all widget positions
                            $rootScope.calculateWidgetPosition(objAction1);
                        }
                    });
                    // Full path for the image
                    html.find('img').each(function () {
                        $(this).attr({
                            'src': $rootScope.app.config.base + $(this).attr('src')
                        })
                    });

                    // Init TOC
                    if (block == $rootScope.app.data.blocks.toc) {
                        $rootScope.refreshHeadings(html);
                    }

                    // Integrate CKEditor to the element
                    html.find('[contenteditable]').each(function () {
                        var thisFocus = $(this);
                        if (typeof thisFocus.attr('id') === 'undefined') {
                            thisFocus.attr('id', QuotingToolUtils.getRandomId());
                        }

                        var myContentContainer = thisFocus.closest('.content-container');
                        var myDataId = $(myContentContainer[0]).data('id');
                        var component = $rootScope.app.data.blocks.init;

                        // Match with blocks
                        for (var b in $rootScope.app.data.blocks) {
                            if (!$rootScope.app.data.blocks.hasOwnProperty(b)) {
                                continue;
                            }

                            var bItem = $rootScope.app.data.blocks[b];
                            if (bItem.template == myDataId) {
                                component = bItem;
                            }
                        }

                        // Match with widget
                        for (var w in $rootScope.app.data.widgets) {
                            if ($rootScope.app.data.widgets.hasOwnProperty(w)) {
                                continue;
                            }

                            var wItem = $rootScope.app.data.widgets[w];
                            if (wItem.template == myDataId) {
                                component = wItem;
                            }
                        }

                        // Add agentid when browse or upload file.
                        AppToolbar.base_editor.settings.imageBrowser_listUrl = $rootScope.app.config.base
                            + GlobalConfig.IMG_URI_FETCH + '&agentid=' + $rootScope.app.model.agentid;
                        AppToolbar.base_editor.settings.filebrowserImageUploadUrl = $rootScope.app.config.base
                            + GlobalConfig.IMG_URI_UPLOAD + '&agentid=' + $rootScope.app.model.agentid;
                        // Merge settings
                        var settings = $.extend({}, AppToolbar.base_editor.settings, component.settings);

                        // Integrate CKEditor to the element
                        var editor = thisFocus.ckeditor(settings, function () {
                            // IFrame cke-realelement
                            var CKEIframes = thisFocus.find('img.cke_iframe');
                            var CKEIframe = null;
                            var frame = null;

                            for (var ckf = 0; ckf < CKEIframes.length; ckf++) {
                                CKEIframe = $(CKEIframes[ckf]);
                                frame = $(decodeURIComponent(CKEIframe.data('cke-realelement')));
                                var link = QuotingToolUtils.getYoutubeThumbnailFromIframe(frame);
                                CKEIframe.attr('src', QuotingToolUtils.getYoutubeThumbnailFromIframe(frame));
                            }

                            //CKFinder.setupCKEditor();
                            AppHelper.customKeyPress(editor);
                            // Custom focus
                            AppHelper.customFocus(editor);
                            // Context menu
                            $rootScope.customCKEditorContextMenu(editor);

                            // Text change:
                            editor.on('blur', function () {
                                // Refresh heading indexing
                                if (block == $rootScope.app.data.blocks.heading) {
                                    $rootScope.refreshHeadings();
                                }
                            });

                            thisFocus.focus();
                        });
                    });

                    // Sortable
                    contentMain.sortable({
                        handle: 'i.icon-move, i.icon-align-justify, .doc-block__control--drag',
                        axis: 'y',
                        beforeStop: function (e, ui) {
                            var placeholderFocus = $(document).find(ui.placeholder).closest('.quoting_tool-content');

                            if (placeholderFocus && placeholderFocus.length > 0) {
                                $rootScope.app.last_focus_page = placeholderFocus;
                            }
                        },
                        stop: function (e, ui) {
                            var prev = $(document).find(ui.item).prev();

                            if (!ui.item.hasClass("quoting_tool-drag-component-to-content")) {
                                return;
                            }

                            var id = ui.item.data('id');
                            // Remove origin element
                            ui.item.replaceWith('');

                            for (var k in $rootScope.app.data.blocks) {
                                if (!$rootScope.app.data.blocks.hasOwnProperty(k)) {
                                    continue;
                                }

                                if ($rootScope.app.data.blocks[k].layout.id == id) {
                                    if (prev.length > 0) {
                                        $scope.addBlock($rootScope.app.data.blocks[k], 'after', prev);
                                    } else {
                                        $scope.addBlock($rootScope.app.data.blocks[k], 'prepend');
                                    }

                                    break;
                                }
                            }
                        }
                    });

                    $rootScope.registerEventFocusInput(html);
                    $rootScope.registerDropToContainer(html);
                    $rootScope.registerEventSupportOptions(html);
                });
            };

            var evtAddBlock = $rootScope.$on('$evtAddBlock', function (event, args) {
                if (typeof args !== 'undefined') {
                    var id = args['id'];
                    var type = args['type'];
                    var focus = args['focus'];

                    for (var k in $rootScope.app.data.blocks) {
                        if (!$rootScope.app.data.blocks.hasOwnProperty(k)) {
                            continue;
                        }

                        if ($rootScope.app.data.blocks[k].layout.id == id) {
                            $scope.addBlock($rootScope.app.data.blocks[k], type, focus);
                            break;
                        }
                    }
                }
            });

            /**
             * Fn - addWidget
             * @param {Object} widget - widget object
             * @param container - jQuery object
             * @param {Object=} css
             */
            $scope.addWidget = function (widget, container, css) {
                // CSS
                if (!css) {
                    css = {};
                }

                // Container
                if (!container || container.length == 0) {
                    // Last block
                    container = $rootScope.app.container.find('.content-container.block-handle').last();

                    if (container.length == 0) {
                        // Invalid container
                        AppHelper.showMessage($translate.instant('Add a block first'));
                        return;
                    }
                }

                AppUtils.loadTemplate($scope, widget.template, true, function (template) {
                    // Append template to DOM
                    var html = $(template);
                    // z-index
                    var zIndexElement = ++$rootScope.app.last_zindex;
                    html.zIndex(zIndexElement);
                    html.find(".quoting_tool-draggable-object").zIndex(zIndexElement);
                    // Full path for the image
                    html.find('img').each(function () {
                        $(this).attr({
                            'src': $rootScope.app.config.base + $(this).attr('src')
                        })
                    });
                    // Update CSS
                    html.css(css);

                    // Append Widget to Block container
                    container.append(html);

                    var info = {
                        editable: true
                    };
                    var focus = null;
                    // Apply jquery components (Default)
                    switch (widget) {
                        case $rootScope.app.data.widgets.signature:
                            html.find('.quoting_tool-widget-signature').html($rootScope.app.user.profile['full_name']);
                            break;
                        case $rootScope.app.data.widgets.initials:
                            focus = html.find('[name="initials"]');
                            // Add custom classes
                            html.find('.quoting_tool-draggable-object')
                                .addClasses(['widget__bound']);
                            break;
                        case $rootScope.app.data.widgets.text_field:
                            focus = html.find('[name="text_field"]');
                            // Add custom classes
                            html.find('.quoting_tool-draggable-object')
                                .addClasses(['widget__bound']);
                            var resizeableOptions = {};
                            if (info['editable']) {
                                resizeableOptions['disabled'] = true;
                            }
                            AppHelper.resizeable(html, resizeableOptions);
                            break;
                        case $rootScope.app.data.widgets.date:
                            focus = html.find('[name="datepicker"]');
                            // Add custom classes
                            html.find('.quoting_tool-draggable-object')
                                .addClasses(['widget__bound']);
                            break;
                        case $rootScope.app.data.widgets.datetime:
                            focus = html.find('[name="datetimepicker"]');
                            // Add custom classes
                            html.find('.quoting_tool-draggable-object')
                                .addClasses(['widget__bound']);
                            break;
                        default:
                            break;
                    }

                    // Init data-info
                    if (focus && focus.length > 0) {
                        focus.attr('data-info', JSON.stringify(info));
                    }

                    // Integrate CKEditor to the element
                    html.find('[contenteditable]').each(function () {
                        var thisFocus = $(this);

                        // Add agentid when browse or upload file.
                        AppToolbar.base_editor.settings.imageBrowser_listUrl = $rootScope.app.config.base
                            + GlobalConfig.IMG_URI_FETCH + '&agentid=' + $rootScope.app.model.agentid;
                        AppToolbar.base_editor.settings.filebrowserImageUploadUrl = $rootScope.app.config.base
                            + GlobalConfig.IMG_URI_UPLOAD + '&agentid=' + $rootScope.app.model.agentid;
                        // Merge settings
                        var settings = $.extend({}, AppToolbar.base_editor.settings, widget.settings);

                        var editor = thisFocus.ckeditor(settings, function () {
                            // Custom focus
                            AppHelper.customFocus(editor);
                            // Context menu
                            $rootScope.customCKEditorContextMenu(editor);

                            thisFocus.focus();
                        });
                    });


                    $rootScope.calculateWidgetPosition(html);
                    $rootScope.registerEventFocusInput(html);
                    $rootScope.registerMoveOnContainer(html);
                    $rootScope.registerEventSupportOptions(html);
                });
            };

            var evtAddWidget = $rootScope.$on('$evtAddWidget', function (event, args) {
                if (typeof args !== 'undefined') {
                    var id = args['id'];
                    var container = args['container'];
                    var css = args['css'];

                    for (var k in $rootScope.app.data.widgets) {
                        if (!$rootScope.app.data.widgets.hasOwnProperty(k)) {
                            continue;
                        }

                        if ($rootScope.app.data.widgets[k].layout.id == id) {
                            $scope.addWidget($rootScope.app.data.widgets[k], container, css);
                            break;
                        }
                    }
                }
            });

            $scope.$on('$destroy', function () {
                // remove listener.
                evtAddBlock();
                evtAddWidget();
            });

            var init = function () {
                registerFilenameChange();
                registerAgentChange();

                // Custom functions
                if ($rootScope.app.data.customFunctions && $rootScope.app.data.customFunctions.length > 0) {
                    $rootScope.app.data.selectedCustomFunction = $rootScope.app.data.customFunctions[0];
                }

                // Custom fields
                if ($rootScope.app.data.customFields && $rootScope.app.data.customFields.length > 0) {
                    $rootScope.app.data.selectedCustomField = $rootScope.app.data.customFields[0];
                }

                $rootScope.app.data.productBlocks = AppToolbar.tokens.product_blocks;
                // Default
                $rootScope.app.data.selectedProductBlock = $rootScope.app.data.productBlocks[0];

                // For vendor plugins
                window.QuotingTool_leak = {};
                window.QuotingTool_leak.blocks = AppToolbar.blocks;
                window.QuotingTool_leak.addBlock = $scope.addBlock;

                if (!$rootScope.app.model.id) {
                    var blocks = $rootScope.app.container.find('.content-container.block-handle');

                    if (blocks.length == 0) {
                        // Add first block if not init
                        $scope.addBlock($rootScope.app.data.blocks.heading);
                    }
                }
            };
            init();

        });

    controllers.controller('CtrlAppRightPanelContent',
        function ($rootScope, $scope, $translate, $compile, AppUtils, AppToolbar, Template, GlobalConfig) {
            // Default section
            $rootScope.sectionVisible = true;
            $rootScope.section = $rootScope.SECTIONS.BLOCKS;
            $rootScope.blockSectionId = $rootScope.SECTIONS.CONTENT_PROPERTIES;
            var specialBlocks = [
                $rootScope.app.data.blocks.cover_page.layout.id,
                $rootScope.app.data.blocks.page_header.layout.id,
                $rootScope.app.data.blocks.page_footer.layout.id
            ];

            /**
             * @link http://stackoverflow.com/questions/15207788/calling-a-function-when-ng-repeat-has-finished
             */
            var ngRepeatBlockFinished = $scope.$on('ngRepeatBlockFinished', function (ngRepeatFinishedEvent, args) {
                var thisFocus = $(args['target']);

                thisFocus.draggable({
                    appendTo: '#quoting_tool-container',    // .quoting_tool-content
                    cursor: 'move',
                    scroll: false,
                    revert: 'invalid',
                    create: function () {
                        var target = $(this);
                        var dataId = target.data('id');

                        if ($.inArray(dataId, specialBlocks) < 0) {
                            // Allow sortable with the content elements
                            thisFocus.draggable('option', 'connectToSortable', '.quoting_tool-content-main');
                        }
                    },
                    drag: function () {
                        var target = $(this);
                        var dataId = target.data('id');

                        // Only bind event for the special blocks
                        if ($.inArray(dataId, specialBlocks) < 0) {
                            return;
                        }

                        if (app.isHidden($rootScope.app.container_overlay)) {
                            $rootScope.app.container_overlay.show();
                            $rootScope.app.container.css({
                                'overflow': 'hidden'
                            });
                        }
                    },
                    helper: function () {
                        return $(this).clone();
                    },
                    stop: function () {
                        // Hide mask content if it shown
                        if (!app.isHidden($rootScope.app.container_overlay)) {
                            $rootScope.app.container_overlay.hide();
                            $rootScope.app.container.css({
                                'overflow': 'scroll'
                            });
                        }
                    }
                });
            });

            var ngRepeatWidgetFinished = $scope.$on('ngRepeatWidgetFinished', function (ngRepeatFinishedEvent, args) {
                var thisFocus = $(args['target']);

                thisFocus.draggable({
                    cursor: 'move',
                    scope: 'add-widget-dropzone',
                    revert: 'invalid',
                    helper: function () {
                        return $(this).clone();
                    },
                    drag: function (e, ui) {
                        $rootScope.dragOffset = ui.offset;
                    }
                });
            });

            /**
             * Fn - changeSelectedModule
             * @link http://www.grobmeier.de/angular-js-ng-select-and-ng-options-21112012.html
             */
            $scope.changeSelectedModule = function () {
                $rootScope.app.model.module = $rootScope.app.data.selectedModule.name;
                // Default selected is first item
                $rootScope.app.data.selectedModuleField = null;

                if ($rootScope.app.data.selectedModule.fields && $rootScope.app.data.selectedModule.fields.length > 0) {
                    // first field
                    $rootScope.app.data.selectedModuleField = $rootScope.app.data.selectedModule.fields[0];
                }

                // $rootScope.app.data.selectedModule.related_modules = null;
                $rootScope.app.data.selectedRelatedModuleField = null;

                if ($rootScope.app.data.selectedModule.related_modules && $rootScope.app.data.selectedModule.related_modules.length > 0) {
                    // first related module
                    $rootScope.app.data.selectedRelatedModule = $rootScope.app.data.selectedModule.related_modules[0];

                    if ($rootScope.app.data.selectedRelatedModule.fields && $rootScope.app.data.selectedRelatedModule.fields.length > 0) {
                        // first related field
                        $rootScope.app.data.selectedRelatedModuleField = $rootScope.app.data.selectedRelatedModule.fields[0];
                    }
                }

                //guest blocks
                $rootScope.app.data.selectedGuestBlock = null;
                if ($rootScope.app.data.selectedModule.guest_blocks && $rootScope.app.data.selectedModule.guest_blocks.length > 0) {
                    // first guest block
                    $rootScope.app.data.selectedGuestBlock = $rootScope.app.data.selectedModule.guest_blocks[0];
                }

                // Change picklist values
                if ($rootScope.app.data.idxModules[$rootScope.app.model.module]) {
                    $rootScope.app.data.picklistField.options = $rootScope.app.data.idxModules[$rootScope.app.model.module]['picklist'];
                }

                if ($rootScope.app.model.id) {
                    // Update module to db
                    Template.save({
                        record: $rootScope.app.model.id,
                        primary_module: $rootScope.app.model.module
                    }, function (response) {
                        if (response.success == true) {
                            var data = response.result;

                            if (!$rootScope.app.model.id) {
                                $rootScope.app.model.id = data['id'];
                            }
                        } else {
                            AppHelper.showMessage(response.error.message)
                        }
                    });
                }
            };

            /**
             * @param $event
             */
            $scope.insertGuestBlock = function ($event) {
                $event.preventDefault();

                if (!$rootScope.app.data.selectedGuestBlock || $rootScope.app.data.selectedGuestBlock.length == 0) {
                    return;
                }

                var guestBlockTemplate = 'layouts/vlayout/modules/QuotingTool/resources/js/views/blocks/guest_block.html';
                var content = $rootScope.app.container.find('.quoting_tool-content');
                var contentMain = content.find('.quoting_tool-content-main');

                AppUtils.loadTemplate($scope, guestBlockTemplate, false, function (template) {
                    var html = $(template);

                    var htmlThead = '';
                    var htmlTbody = '';
                    var totalCol = 1;
                    var theadContent = html.find('.thead-content');
                    var tbodyContent = html.find('.tbody-content');

                    for (var i = 0; i < $rootScope.app.data.selectedGuestBlock.fields.length; i++) {
                        htmlThead += '<th>' + $rootScope.app.data.selectedGuestBlock.fields[i].label + '</th>';
                        htmlTbody += '<td>' + $rootScope.app.data.selectedGuestBlock.fields[i].token + '</td>';
                        totalCol++;
                    }

                    var tbodyGuestBlockStart = html.find('.tbody-guestblock-start');
                    tbodyGuestBlockStart.attr('colspan', totalCol);
                    var tbodyGuestBlockEnd = html.find('.tbody-guestblock-end');
                    tbodyGuestBlockEnd.attr('colspan', totalCol);
                    theadContent.append(htmlThead);
                    tbodyContent.append(htmlTbody);
                    contentMain.append(html);

                    // Integrate CKEditor to the element
                    html.find('[contenteditable]').each(function () {
                        var thisFocus = $(this);
                        if (typeof thisFocus.attr('id') === 'undefined') {
                            thisFocus.attr('id', QuotingToolUtils.getRandomId());
                        }

                        var myContentContainer = thisFocus.closest('.content-container');
                        var guestBlock = myContentContainer.find('table[data-table-type="guest_block"]');

                        // mark module & guest block name for guest block
                        guestBlock.attr('data-module', $rootScope.app.model.module);
                        guestBlock.attr('data-guest-block', $rootScope.app.data.selectedGuestBlock.name);

                        var myDataId = $(myContentContainer[0]).data('id');
                        var component = $rootScope.app.data.blocks.init;

                        // Match with blocks
                        for (var b in $rootScope.app.data.blocks) {
                            if (!$rootScope.app.data.blocks.hasOwnProperty(b)) {
                                continue;
                            }

                            var bItem = $rootScope.app.data.blocks[b];
                            if (bItem.template == myDataId) {
                                component = bItem;
                            }
                        }

                        // Match with widget
                        for (var w in $rootScope.app.data.widgets) {
                            if ($rootScope.app.data.widgets.hasOwnProperty(w)) {
                                continue;
                            }

                            var wItem = $rootScope.app.data.widgets[w];
                            if (wItem.template == myDataId) {
                                component = wItem;
                            }
                        }

                        // Add agentid when browse or upload file.
                        AppToolbar.base_editor.settings.imageBrowser_listUrl = $rootScope.app.config.base
                            + GlobalConfig.IMG_URI_FETCH + '&agentid=' + $rootScope.app.model.agentid;
                        AppToolbar.base_editor.settings.filebrowserImageUploadUrl = $rootScope.app.config.base
                            + GlobalConfig.IMG_URI_UPLOAD + '&agentid=' + $rootScope.app.model.agentid;
                        // Merge settings
                        var settings = $.extend({}, AppToolbar.base_editor.settings, component.settings);

                        // Integrate CKEditor to the element
                        var editor = thisFocus.ckeditor(settings, function () {
                            // IFrame cke-realelement
                            var CKEIframes = thisFocus.find('img.cke_iframe');
                            var CKEIframe = null;
                            var frame = null;

                            for (var ckf = 0; ckf < CKEIframes.length; ckf++) {
                                CKEIframe = $(CKEIframes[ckf]);
                                frame = $(decodeURIComponent(CKEIframe.data('cke-realelement')));
                                CKEIframe.attr('src', QuotingToolUtils.getYoutubeThumbnailFromIframe(frame));
                            }

                            //CKFinder.setupCKEditor();
                            AppHelper.customKeyPress(editor);
                            // Custom focus
                            AppHelper.customFocus(editor);
                            // Context menu
                            $rootScope.customCKEditorContextMenu(editor);

                            // // Text change:
                            // editor.on('blur', function () {
                            //     // Refresh heading indexing
                            //     if (block == $rootScope.app.data.blocks.heading) {
                            //         $rootScope.refreshHeadings();
                            //     }
                            // });

                            thisFocus.focus();
                        });
                    });

                    // Sortable
                    contentMain.sortable({
                        handle: 'i.icon-move, i.icon-align-justify, .doc-block__control--drag',
                        axis: 'y',
                        beforeStop: function (e, ui) {
                            var placeholderFocus = $(document).find(ui.placeholder).closest('.quoting_tool-content');

                            if (placeholderFocus && placeholderFocus.length > 0) {
                                $rootScope.app.last_focus_page = placeholderFocus;
                            }
                        },
                        stop: function (e, ui) {
                            var prev = $(document).find(ui.item).prev();

                            if (!ui.item.hasClass("quoting_tool-drag-component-to-content")) {
                                return;
                            }

                            var id = ui.item.data('id');
                            // Remove origin element
                            ui.item.replaceWith('');

                            for (var k in $rootScope.app.data.blocks) {
                                if (!$rootScope.app.data.blocks.hasOwnProperty(k)) {
                                    continue;
                                }

                                if ($rootScope.app.data.blocks[k].layout.id == id) {
                                    if (prev.length > 0) {
                                        $scope.addBlock($rootScope.app.data.blocks[k], 'after', prev);
                                    } else {
                                        $scope.addBlock($rootScope.app.data.blocks[k], 'prepend');
                                    }

                                    break;
                                }
                            }
                        }
                    });

                    // Compile HTML with angularjs
                    $compile(html.contents())($scope);

                    $rootScope.registerEventFocusInput(html);
                    $rootScope.registerDropToContainer(html);
                    $rootScope.registerEventSupportOptions(html);
                });
            };

            $scope.changeSelectedRelatedModule = function () {
                $rootScope.app.data.selectedRelatedModuleField = $rootScope.app.data.selectedRelatedModule.fields[0];
            };

            /**
             * Fn - changeSelectedModule
             * @link http://www.grobmeier.de/angular-js-ng-select-and-ng-options-21112012.html
             */
            $scope.changeSelectedProductBlockModule = function () {
                $rootScope.app.data.selectedProductBlockModuleField =
                    $rootScope.app.data.selectedProductBlockModule ? $rootScope.app.data.selectedProductBlockModule.fields[0] : null;
            };

            /**
             * Fn - insertModuleFieldToken
             * @param $event
             */
            $scope.insertModuleFieldToken = function ($event) {
                $event.preventDefault();

                var text = $rootScope.app.data.selectedModuleField.token;
                var info = {
                    id: $rootScope.app.data.selectedModuleField['id'],
                    name: $rootScope.app.data.selectedModuleField['name'],
                    label: $rootScope.app.data.selectedModuleField['label'],
                    uitype: $rootScope.app.data.selectedModuleField['uitype'],
                    datatype: $rootScope.app.data.selectedModuleField['datatype'],
                    module: $rootScope.app.data.selectedModule.name
                };
                AppUtils.pasteHtmlAtCaret(text, info);
            };

            /**
             * Fn - insertRelatedModuleFieldToken
             * @param $event
             */
            $scope.insertRelatedModuleFieldToken = function ($event) {
                $event.preventDefault();

                var text = $rootScope.app.data.selectedRelatedModuleField.token;
                var info = {
                    id: $rootScope.app.data.selectedRelatedModuleField['id'],
                    name: $rootScope.app.data.selectedRelatedModuleField['name'],
                    uitype: $rootScope.app.data.selectedRelatedModuleField['uitype'],
                    datatype: $rootScope.app.data.selectedRelatedModuleField['datatype'],
                    module: $rootScope.app.data.selectedRelatedModule.name
                };
                AppUtils.pasteHtmlAtCaret(text, info);
            };

            /**
             * Fn - insertProductBlockToken
             * @param $event
             * @returns {boolean}
             */
            $scope.insertProductBlockToken = function ($event) {
                $event.preventDefault();

                var text = $rootScope.app.data.selectedProductBlock.token;
                AppUtils.pasteHtmlAtCaret(text);
            };

            /**
             * Fn - insertProductBlockModuleFieldToken
             * @param $event
             */
            $scope.insertProductBlockModuleFieldToken = function ($event) {
                $event.preventDefault();

                var text = $rootScope.app.data.selectedProductBlockModuleField.token;
                AppUtils.pasteHtmlAtCaret(text);
            };

            /**
             * Fn - insertCustomFunctionToken
             * @param $event
             */
            $scope.insertCustomFunctionToken = function ($event) {
                $event.preventDefault();

                AppUtils.pasteHtmlAtCaret($rootScope.app.data.selectedCustomFunction.token);
            };

            /**
             * Fn - insertCustomFieldToken
             * @param $event
             */
            $scope.insertCustomFieldToken = function ($event) {
                $event.preventDefault();

                var text = $rootScope.app.data.selectedCustomField.token;
                AppUtils.pasteHtmlAtCaret(text);
            };

            $scope.$on('$destroy', function () {
                // remove listener.
                ngRepeatBlockFinished();
                ngRepeatWidgetFinished();
            });

            var init = function () {
            };
            init();

        });

    controllers.controller('CtrlAppRightPanelGeneral',
        function ($rootScope, $scope, $compile, $timeout, AppConstants, AppUtils, GlobalConfig, Template, TemplateSetting) {
            // Default section
            $rootScope.sectionVisible = true;
            $rootScope.section = $rootScope.SECTIONS.DECISION;
            $rootScope.blockSectionId = $rootScope.SECTIONS.GENERAL_ACCEPT;

            $scope.picklistField = {};
            $scope.picklistField.selectedField = {};
            $scope.picklistField.selectedValue = {};

            // Backup before change
            var originalBackground = {
                image: '',
                size: 'auto'
            };

            // Copy backup if the settings is valid
            if ($rootScope.app.model.settings.background) {
                if ($rootScope.app.model.settings.background.image) {
                    originalBackground.image = $rootScope.app.model.settings.background.image;
                }
                if ($rootScope.app.model.settings.background.size) {
                    originalBackground.size = $rootScope.app.model.settings.background.size;
                }
            } else {
                $rootScope.app.model.settings.background = originalBackground;
            }

            $scope.previewBackground = GlobalConfig.DEFAULT_BACKGROUND_IMAGE;

            $scope.formToken = {
                name: csrfMagicName,
                value: csrfMagicToken
            };

            /**
             * Fn - addFieldTokenMapping
             *
             * @param $event
             * @param {String} dataId
             * @param {String} selectedField
             * @param {String} selectedValue
             * @param {Number|undefined} mappingType - Value: -1 (Decline)/ 0 (Not sure)/ 1 (Accept)
             */
            $scope.addFieldTokenMapping = function ($event, dataId, selectedField, selectedValue, mappingType) {
                var isNew = $event ? true : false;

                var picklistContainer = null;
                // Option: mappingType
                if (!mappingType) {
                    mappingType = 0;
                }

                if (isNew) {
                    // Add new from UI
                    $event.preventDefault();

                    picklistContainer = $($event.target)
                        .closest('.quoting_tool-block-section-container')
                        .find('.quoting_tool-accept-decline-fields');

                    mappingType = 0;
                    if (picklistContainer.length > 0) {
                        if (picklistContainer[0].id == 'quoting_tool-accept-fields') {
                            mappingType = 1;
                        } else if (picklistContainer[0].id == 'quoting_tool-decline-fields') {
                            mappingType = -1;
                        }
                    }
                } else {
                    // Add new from exist template
                    if (mappingType == 1) {
                        picklistContainer = $('#quoting_tool-accept-fields');
                    } else {
                        picklistContainer = $('#quoting_tool-decline-fields');
                    }
                }

                if (!dataId) {
                    dataId = QuotingToolUtils.getRandomId();
                    $scope.picklistField.selectedField[dataId] = QuotingToolUtils.defaultSelected($rootScope.app.data.picklistField.options);
                    if ($scope.picklistField.selectedField[dataId]) {
                        $scope.picklistField.selectedValue[dataId] = QuotingToolUtils.defaultSelected($scope.picklistField.selectedField[dataId].values);
                    }


                } else {
                    $scope.picklistField.selectedField[dataId] = QuotingToolUtils.currentSelected($rootScope.app.data.picklistField.options, selectedField, 'id');
                    if ($scope.picklistField.selectedField[dataId]) {
                        $scope.picklistField.selectedValue[dataId] = selectedValue;
                    }
                }

                var picklistTemplate = 'layouts/vlayout/modules/QuotingTool/resources/js/views/right_panel/general_decision_item.html';
                AppUtils.loadTemplate($scope, picklistTemplate, false, function (template) {
                    var html = $(template);
                    // Set id for block
                    var attrs = {
                        'data-id': dataId
                    };

                    // Default selected
                    if ($scope.picklistField.selectedField[dataId]) {
                        attrs['data-selected-field'] = $scope.picklistField.selectedField[dataId].id;
                        attrs['data-selected-value'] = $scope.picklistField.selectedValue[dataId];
                        attrs['data-type'] = mappingType;
                    }

                    // Set attributes
                    html.attr(attrs);

                    // Select ng-change
                    html.find('select.quoting_tool-selector-picklist-field').attr({
                        'ng-model': "picklistField.selectedField." + dataId,
                        'ng-change': "changeSelectedPicklistField('" + dataId + "')"
                    });

                    html.find('select.quoting_tool-selector-picklist-value').attr({
                        'ng-model': "picklistField.selectedValue." + dataId,
                        'ng-options': "key as value for (key, value) in picklistField.selectedField." + dataId + ".values",
                        'ng-change': "changeSelectedPicklistValue('" + dataId + "')"
                    });
                    // Merge block to document
                    picklistContainer.append(html);

                    // Compile HTML with angularjs
                    $compile(html.contents())($scope);

                    if (isNew) {
                        // Add new mapping field to model
                        $rootScope.app.model.mapping_fields[dataId] = {
                            'selected-field': $scope.picklistField.selectedField[dataId].id,
                            'selected-value': $scope.picklistField.selectedValue[dataId],
                            'type': mappingType
                        };

                        if ($rootScope.app.model.id) {
                            // Update mapping fields to db
                            Template.save({
                                record: $rootScope.app.model.id,
                                mapping_fields: angular.toJson($rootScope.app.model.mapping_fields)
                            }, function (response) {
                                if (response.success == true) {
                                } else {
                                    AppHelper.showMessage(response.error.message)
                                }
                            });
                        }
                    }
                });
            };

            /**
             * Fn - removeFieldTokenMapping
             * @param $event
             */
            $scope.removeFieldTokenMapping = function ($event) {
                $event.preventDefault();

                var container = $($event.target).closest('.general_accept_decline_picklist');
                var dataId = container.data('id');
                container.remove();
                delete $rootScope.app.model.mapping_fields[dataId];

                if ($rootScope.app.model.id) {
                    // Update mapping fields to db
                    Template.save({
                        record: $rootScope.app.model.id,
                        mapping_fields: angular.toJson($rootScope.app.model.mapping_fields)
                    }, function (response) {
                        if (response.success == true) {
                        } else {
                            AppHelper.showMessage(response.error.message)
                        }
                    });
                }
            };

            /**
             * Fn - changeSelectedPicklistField
             */
            $scope.changeSelectedPicklistField = function (id) {
                $scope.picklistField.selectedValue[id] = QuotingToolUtils.defaultSelected($scope.picklistField.selectedField[id].values);
                // Update data-
                var container = $('[data-id="' + id + '"]');
                container.attr({
                    'data-selected-field': $scope.picklistField.selectedField[id].id,
                    'data-selected-value': $scope.picklistField.selectedValue[id]
                });

                $rootScope.app.model.mapping_fields[id]['selected-field'] = $scope.picklistField.selectedField[id].id;
                $rootScope.app.model.mapping_fields[id]['selected-value'] = $scope.picklistField.selectedValue[id];

                if ($rootScope.app.model.id) {
                    // Update mapping fields to db
                    Template.save({
                        record: $rootScope.app.model.id,
                        mapping_fields: angular.toJson($rootScope.app.model.mapping_fields)
                    }, function (response) {
                        if (response.success == true) {
                        } else {
                            AppHelper.showMessage(response.error.message)
                        }
                    });
                }
            };

            /**
             * Fn - changeSelectedPicklistValue
             */
            $scope.changeSelectedPicklistValue = function (id) {
                // Update data-
                var container = $('[data-id="' + id + '"]');
                container.attr({
                    'data-selected-value': $scope.picklistField.selectedValue[id]
                });

                $rootScope.app.model.mapping_fields[id]['selected-value'] = $scope.picklistField.selectedValue[id];

                if ($rootScope.app.model.id) {
                    // Update mapping fields to db
                    Template.save({
                        record: $rootScope.app.model.id,
                        mapping_fields: angular.toJson($rootScope.app.model.mapping_fields)
                    }, function (response) {
                        if (response.success == true) {
                        } else {
                            AppHelper.showMessage(response.error.message)
                        }
                    });
                }
            };

            /**
             * Fn - loadMappingFields
             */
            var loadMappingFields = function () {
                var mp = null;

                for (var k in $rootScope.app.model.mapping_fields) {
                    if ($rootScope.app.model.mapping_fields.hasOwnProperty(k)) {
                        mp = $rootScope.app.model.mapping_fields[k];
                        $scope.addFieldTokenMapping(null, k, mp['selected-field'], mp['selected-value'], mp['type']);
                    }
                }
            };

            /**
             * Fn - changeAcceptDeclineLabel
             *
             * @param {Object} $event
             * @param {String} field_name
             */
            $scope.changeAcceptDeclineLabel = function ($event, field_name) {
                var container = $($event.target).closest('.block-section__hd');
                var objName = container.find('.block-section__name');
                objName.attr({
                    'contenteditable': 'true'
                });
                objName.placeCaretAtEnd();

                // When blur
                objName.blur(function () {
                    var thisFocus = $(this);
                    thisFocus.attr({
                        'contenteditable': 'false'
                    });
                    var decisionText = thisFocus.text().trim();
                    $rootScope.app.model.settings[field_name] = decisionText;

                    if ($rootScope.app.model.id) {
                        // Update settings to db
                        var params = {
                            'record': $rootScope.app.model.id
                        };
                        params[field_name] = decisionText;
                        TemplateSetting.save(params, function (response) {
                            if (response.success == true) {
                            } else {
                                AppHelper.showMessage(response.error.message)
                            }
                        });
                    }
                });
            };

            /**
             * @param $event
             */
            $scope.changeBackgroundImage = function ($event) {
                $event.preventDefault();

                AppUtils.openImagebrowser(AppHelper.KCFINDER_FILE_TYPE.IMAGES, {}, function (url) {
                    $scope.$apply(function () {
                        $rootScope.app.model.settings.background.image = url;

                        if ($rootScope.app.model.id) {
                            // Update background settings to db
                            TemplateSetting.save({
                                'record': $rootScope.app.model.id,
                                'background': $rootScope.app.model.settings.background
                            }, function (response) {
                                if (response.success == true) {
                                } else {
                                    AppHelper.showMessage(response.error.message)
                                }
                            });
                        }
                    });
                });
            };

            /**
             * @param $event
             */
            $scope.removeBackgroundImage = function ($event) {
                $event.preventDefault();

                $rootScope.app.model.settings.background.image = '';
                $rootScope.app.model.settings.background.size = 'auto';

                if ($rootScope.app.model.id) {
                    // Update background settings to db
                    TemplateSetting.save({
                        'record': $rootScope.app.model.id,
                        'background': $rootScope.app.model.settings.background
                    }, function (response) {
                        if (response.success == true) {
                        } else {
                            AppHelper.showMessage(response.error.message)
                        }
                    });
                }
            };

            /**
             * @param $event
             */
            $scope.resetBackgroundImage = function ($event) {
                if (typeof $event !== 'undefined' && $event)
                    $event.preventDefault();

                $rootScope.app.model.settings.background.image = originalBackground.image;
                $rootScope.app.model.settings.background.size = originalBackground.size;

                if ($rootScope.app.model.id) {
                    // Update background settings to db
                    TemplateSetting.save({
                        'record': $rootScope.app.model.id,
                        'background': $rootScope.app.model.settings.background
                    }, function (response) {
                        if (response.success == true) {
                        } else {
                            AppHelper.showMessage(response.error.message)
                        }
                    });
                }
            };

            /**
             * Fn - changeBackgroundSize
             * @param value
             */
            $scope.changeBackgroundSize = function (value) {
                $rootScope.app.model.settings.background.size = value;

                if ($rootScope.app.model.id) {
                    // Update background settings to db
                    TemplateSetting.save({
                        'record': $rootScope.app.model.id,
                        'background': $rootScope.app.model.settings.background
                    }, function (response) {
                        if (response.success == true) {
                        } else {
                            AppHelper.showMessage(response.error.message)
                        }
                    });
                }
            };

            /**
             * @param $event
             */
            $scope.addAttachment = function ($event) {
                $event.preventDefault();

                AppHelper.openKCFinder(AppHelper.KCFINDER_FILE_TYPE.FILES, {}, function (url) {
                    $scope.$apply(function () {
                        // Add new file
                        $rootScope.app.model.attachments.push({
                            'name': QuotingToolUtils.getFilenameFromPath(url),
                            'full_path': url
                        });

                        if ($rootScope.app.model.id) {
                            // Update attachments to db
                            Template.save({
                                record: $rootScope.app.model.id,
                                attachments: $rootScope.app.model.attachments
                            }, function (response) {
                                if (response.success == true) {
                                } else {
                                    AppHelper.showMessage(response.error.message)
                                }
                            });
                        }
                    });
                });
            };

            /**
             * @param attachment
             */
            $scope.removeAttachment = function (attachment) {
                // Remove from model
                /** @link http://stackoverflow.com/questions/16118762/angularjs-wrong-index-after-orderby */
                $rootScope.app.model.attachments.splice($rootScope.app.model.attachments.indexOf(attachment), 1);

                if ($rootScope.app.model.id) {
                    // Update attachments to db
                    Template.save({
                        record: $rootScope.app.model.id,
                        attachments: $rootScope.app.model.attachments
                    }, function (response) {
                        if (response.success == true) {
                        } else {
                            AppHelper.showMessage(response.error.message)
                        }
                    });
                }
            };

            $scope.load_decision = function () {
                // Initial
                loadMappingFields();

                $rootScope.app.form.on('change', '[name="tmp_description"]', function () {
                    if ($rootScope.app.model.id) {
                        // Update description to db
                        Template.save({
                            record: $rootScope.app.model.id,
                            description: $rootScope.app.model.description
                        }, function (response) {
                            if (response.success == true) {
                            } else {
                                AppHelper.showMessage(response.error.message)
                            }
                        });
                    }
                });
                $rootScope.app.form.on('change', '[name="tmp_anwidget"]', function () {
                    if ($rootScope.app.model.id) {
                        // Update description to db
                        Template.save({
                            record: $rootScope.app.model.id,
                            anwidget: $rootScope.app.model.anwidget
                        }, function (response) {
                            if (response.success == true) {
                            } else {
                                AppHelper.showMessage(response.error.message)
                            }
                        });
                    }
                });
            };

            $scope.load_background = function () {
                // $scope.resetBackgroundImage();

                var form = $('#BackgroundImageUploadForm');
                var inputUpload = form.find('[name="upload"]');
                var submitUpload = form.find('.btn-submit');

                inputUpload.fileupload({
                    dataType: 'json',
                    autoUpload: false,
                    replaceFileInput:false,
                    add: function (e, data) {
                        data.context = submitUpload.off('click')
                            .on('click', function (e) {
                                e.preventDefault();

                                var newForm = $('<form />').html(form.html());
                                // Clone attributes
                                var attributes = AppHelper.getAllAttributes(form);
                                for (var attr in attributes) {
                                    if (!attributes.hasOwnProperty(attr)) {
                                        continue;
                                    }

                                    newForm.attr(attr, attributes[attr]);
                                }

                                data.form = newForm;
                                // Submit my form
                                data.submit();
                            });

                        // Upload image
                        submitUpload.click();
                    },
                    done: function (e, data) {
                        $scope.$apply(function () {
                            // clear input
                            inputUpload.val('');

                            var image = data.result[0];
                            var url = image.image;
                            $rootScope.app.model.settings.background.image = url;

                            if ($rootScope.app.model.id) {
                                // Update background settings to db
                                TemplateSetting.save({
                                    'record': $rootScope.app.model.id,
                                    'background': $rootScope.app.model.settings.background
                                }, function (response) {
                                    if (response.success == true) {
                                    } else {
                                        AppHelper.showMessage(response.error.message)
                                    }
                                });
                            }
                        });
                    }
                });
            };

            var init = function () {
            };
            init();

        });

    /**
     * Ctrl - CtrlAppRightPanelHistory
     */
    controllers.controller('CtrlAppRightPanelHistory',
        function ($rootScope, $scope, $timeout, $translate, TemplateHistory) {
            // Default section
            $rootScope.sectionVisible = true;
            $rootScope.section = $rootScope.SECTIONS.HISTORIES;
            $rootScope.blockSectionId = $rootScope.SECTIONS.HISTORY_TAB1;
            $scope.model = {};
            $scope.model.selectedHistory = null;
            var historyContainer = $('#quoting_tool-tool-items-container-history');
            // Show sub indicator
            historyContainer.progressIndicator({
                'message': $translate.instant('Loading...'),
                'mode': 'show'
            });

            $scope.showHistories = function () {
                var params = {record: $rootScope.app.model.id};
                TemplateHistory.getHistories(params, function (response) {
                    if (response.success == true) {
                        $rootScope.app.model.histories = response.result;

                        if ($rootScope.app.model.histories && $rootScope.app.model.histories.length > 0) {
                            $scope.$apply(function () {
                                $scope.model.selectedHistory = $rootScope.app.model.histories[$rootScope.app.model.histories.length - 1];
                            });
                        }

                        // Hide sub indicator
                        historyContainer.progressIndicator({'mode': 'hide'});
                    } else {
                        AppHelper.showMessage(response.message);
                    }
                });
            };

            $scope.removeHistory = function (history) {
                if ($rootScope.app.model.id) {
                    // Remove history from db
                    TemplateHistory.removeHistories({
                        'record': $rootScope.app.model.id,
                        'history_id': history.id
                    }, function (response) {
                        if (response.success == true) {
                            $scope.$apply(function () {
                                // Remove from model
                                /** @link http://stackoverflow.com/questions/16118762/angularjs-wrong-index-after-orderby */
                                $rootScope.app.model.histories.splice($rootScope.app.model.histories.indexOf(history), 1);
                            });
                        } else {
                            AppHelper.showMessage(response.error.message)
                        }
                    });
                }
            };

            $scope.restoreHistory = function (history) {
                $scope.model.selectedHistory = history;

                if ($rootScope.app.model.id) {
                    $rootScope.app.progressIndicatorElement.progressIndicator({
                        'message': $translate.instant('Processing...'),
                        'position': 'html',
                        'blockInfo': {
                            'enabled': true
                        },
                        'mode': 'show'
                    });

                    // Get history from db
                    TemplateHistory.getHistory({
                        'record': $rootScope.app.model.id,
                        'history_id': history.id
                    }, function (response) {
                        if (response.success == true) {
                            var data = response.result;
                            $rootScope.app.model.body = data['body'];
                            $rootScope.loadTemplate($rootScope.app.model.id);

                            $rootScope.registerEventDragAndDropBlocks();
                            $rootScope.watchCurrentPosition();
                            // Last focus page:
                            $rootScope.registerEventFocusPage();
                            // mouse enter event
                            $rootScope.registerEventCoverPageSupportOptions();
                            // Last focus item
                            $rootScope.registerEventFocusInput();
                            // Delay to hide progress indicator
                            $timeout(function () {
                                // Hide indicator
                                $rootScope.app.progressIndicatorElement.progressIndicator({'mode': 'hide'});
                            }, 8000);
                        } else {
                            AppHelper.showMessage(response.error.message)
                        }
                    });
                }

            };

            var setSelectedHistory = function (history) {
                $scope.model.selectedHistory = history;
            };

            var evtSetSelectedHistory = $rootScope.$on('$evtSetSelectedHistory', function (event, args) {
                if (typeof args !== 'undefined') {
                    var history = args['history'];

                    if (history) {
                        setSelectedHistory(history);
                    }
                }
            });

            $scope.load_history = function () {
                $scope.showHistories();
            };

            $scope.$on('$destroy', function () {
                // remove listener.
                evtSetSelectedHistory();
            });

            var init = function () {
            };
            init();

        });

})(jQuery);
