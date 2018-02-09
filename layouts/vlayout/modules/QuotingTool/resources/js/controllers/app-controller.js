(function ($) {
    'use strict';

    var controllers = angular.module('AppControllers', ['AppModels', 'AppConfig', 'AppI18N', 'AppDirectives', 'ui.router']);

    controllers.controller('CtrlApp',
        function ($rootScope, $scope, $templateCache, $compile, $timeout, $translate, PageTitle, AppUtils, AppToolbar, AppConstants,
                  Template, TemplateSetting, GlobalConfig, $window, $location) {
            // Reset page title
            PageTitle.reset();

            $scope.AppConstants = AppConstants;
            $scope.inventoryFields = [];
            var productModuleFields = [];
            var idxProductBlockModules = {};

            $scope.tableBlockTotal = [];
            $scope.tableBlockTheme = AppConstants.TABLE_BLOCK.THEMES;

            $scope.settings = {};

            // Pricing table
            $scope.settings.pricing_table = {};
            $scope.settings.pricing_table.theme = $scope.tableBlockTheme[0];    // Default is first theme
            $scope.settings.pricing_table.total_fields = [];
            $scope.settings.pricing_table.total_fields_order = [];
            $scope.settings.pricing_table.item_fields = [];
            $scope.settings.pricing_table.item_fields_order = [];

            // Guest block
            $scope.settings.guest_block = {};
            $scope.settings.guest_block.theme = $scope.tableBlockTheme[0];    // Default is first theme
            $scope.settings.guest_block.item_fields = [];
            $scope.settings.guest_block.item_fields_order = [];

            $scope.changeLanguage = function (key) {
                $translate.use(key);
            };

            /**
             * @param container
             */
            $rootScope.refreshHeadings = function (container) {
                if (typeof container === 'undefined') {
                    container = $rootScope.app.container.find('[data-id="' + $rootScope.app.data.blocks.toc.template + '"]');
                }

                var htmlHeading = '';
                var tagName = '';
                var tagText = '';
                var headingNumber = 1;
                var marginLeft = '0';
                var tag = null;
                var objTag = null;
                var info = null;
                var indexing = false;

                var headings = $rootScope.app.container
                    .find('.quoting_tool-content:not([data-page-name="cover_page"])')
                    .find('.quoting_tool-content-main')
                    .find('.content-container:not([data-id="' + $rootScope.app.data.blocks.toc.template + '"])')
                    .find('h1, h2, h3, h4, h5, h6');

                for (var i = 0; i < headings.length; i++) {
                    tag = headings[i];
                    objTag = $(tag);
                    info = objTag.data('info');

                    if (!info) {
                        info = {};
                    }

                    indexing = (info['indexing']) ? info['indexing'] : false;

                    if (indexing) {
                        // Only show if it enable
                        tagName = tag.tagName.toLowerCase();
                        tagText = tag.textContent;
                        headingNumber = parseInt(tagName.substring(1));
                        marginLeft = ((headingNumber - 1) * 20) + 'px'; // Margin from h2

                        htmlHeading += '<' + tagName + ' style="margin-left: ' + marginLeft + ';">' + tagText + '</' + tagName + '>';
                    }

                }

                var tocBlockContainer = null;

                for (var i = 0; i < container.length; i++) {
                    tocBlockContainer = $(container[i]).find('.content-editable');
                    // Replace content
                    tocBlockContainer.html(htmlHeading);
                }
            };

            /**
             * @param $event
             */
            $rootScope.remove = function ($event) {
                $event.preventDefault();

                var target = $($event.target);
                var container = target.closest('.content-container');
                var template = container.data('id');

                // Only with draggable object (widgets)
                var next = container.next('.content-container.quoting_tool-draggable');
                // Remove the component
                container.remove();

                if (template == $rootScope.app.data.blocks.heading.template) {
                    // Refresh headings
                    $rootScope.refreshHeadings();
                }

                // Re calculate position. Only with draggable object (widgets)
                if (next && next.length > 0) {
                    $rootScope.calculateWidgetPosition(next);
                }
            };

            /**
             * Fn - $scope.removeCoverPage
             *
             * @param $event
             */
            $rootScope.removeCoverPage = function ($event) {
                $event.preventDefault();

                var coverPage = $('[data-id="' + $rootScope.app.data.blocks.cover_page.template + '"]');
                // Switch focus page:
                $rootScope.app.last_focus_page = coverPage.next('.quoting_tool-content');
                coverPage.remove();
            };

            /**
             * @param $event
             * @param {Object=} focus
             */
            $rootScope.setting = function ($event, focus) {
                $event.preventDefault();

                if (!focus) {
                    return false;
                }

                var target = $($event.target);
                var container = target.closest('.content-container');
                var editable = container.find('.content-editable');
                $rootScope.app.last_focus_item_setting = editable.find('input, select, textarea');

                // Update exist values
                var info = $rootScope.app.last_focus_item_setting.data('info');

                if (typeof info === 'undefined') {
                    info = {};
                }

                // When is block
                switch (focus) {
                    case $rootScope.app.data.blocks.heading:
                        AppUtils.loadTemplate($scope, focus.setting_template, true, function (html) {
                            var objTextField = container.find('h1, h2, h3, h4, h5, h6');
                            var info = objTextField.data('info');

                            if (typeof info === 'undefined') {
                                info = {};
                            }

                            html.find('[name="indexing"]').prop({
                                'checked': info['indexing'] ? info['indexing'] : false
                            });

                            AppHelper.showModalWindow(html, '#', function () {
                                // Trigger click
                                html.on('click', '.btn-submit', function () {
                                    var indexing = html.find('[name="indexing"]');
                                    info['indexing'] = indexing.is(':checked');
                                    objTextField.attr('data-info', JSON.stringify(info));
                                    // Refresh heading
                                    $rootScope.refreshHeadings();

                                    AppHelper.hideModalWindow();
                                });
                            });
                        });
                        break;
                    case $rootScope.app.data.widgets.signature:
                        AppUtils.loadTemplate($scope, focus.setting_template, true, function (html) {
                            AppHelper.showModalWindow(html, '#', function () {
                                // Before open modal
                                var contenteditable = editable.find('[contenteditable="true"]');

                                if (!contenteditable || contenteditable.length == 0) {
                                    return;
                                }

                                var objSignature = contenteditable.find('.quoting_tool-widget-signature-main');
                                var dataInfo = objSignature.data('info');

                                if (typeof dataInfo === 'undefined') {
                                    dataInfo = {};
                                }

                                var sigPad = html.find('.sigPad').signaturePad({
                                    lineTop: 114,
                                    lineWidth: 1,
                                    drawOnly: true,
                                    bgColour: 'transparent'
                                });

                                //
                                var widgetSignatureHeader = container.find('.quoting_tool-widget-signature-header');
                                var widgetSignatureImage = container.find('.quoting_tool-widget-signature-image');
                                var widgetSignature = container.find('.quoting_tool-widget-signature');
                                var targetContainer = widgetSignatureImage.parent('[data-target="#myModal"]');

                                //
                                var inName = html.find('[name="name"]');
                                var inSignature = html.find('[name="output"]');
                                inName.val(dataInfo['signature_name'] ? dataInfo['signature_name'] : $rootScope.app.user.profile['full_name']);
                                inSignature.val(dataInfo['signature']);
                                // Init signature pad image
                                if (dataInfo['signature']) {
                                    sigPad.regenerate(dataInfo['signature']);
                                }

                                // Default replace signature
                                var inReplaceSignatureButton = $('input[name="replace_signature_button"]');
                                if (targetContainer.length > 0) {
                                    inReplaceSignatureButton.prop('checked', true);
                                } else {
                                    inReplaceSignatureButton.prop('checked', false);
                                }

                                // Trigger click
                                html.on('click', '.btn-submit', function () {
                                    var signatureImage = sigPad.getSignatureImage();
                                    var signatureName = inName.val();
                                    var signature = inSignature.val();
                                    dataInfo['signature_name'] = signatureName;
                                    dataInfo['signature'] = signature;
                                    widgetSignature.html(signatureName);

                                    if (inReplaceSignatureButton.is(':checked')) {
                                        if (targetContainer.length == 0) {
                                            widgetSignatureImage.wrap('<a href="javascript:void(0);" data-target="#myModal"/>');
                                        }

                                        widgetSignatureImage.attr('src', $rootScope.app.config.base + 'modules/QuotingTool/resources/images/placeholder-signature.png');
                                        widgetSignatureHeader.css({
                                            'visibility': 'visible'
                                        });
                                    } else {
                                        if (targetContainer.length > 0) {
                                            widgetSignatureImage.unwrap();
                                        }

                                        widgetSignatureImage.attr('src', signatureImage);
                                        widgetSignatureHeader.css({
                                            'visibility': 'hidden'
                                        });
                                    }

                                    objSignature.attr('data-info', JSON.stringify(dataInfo));

                                    AppHelper.hideModalWindow();
                                });
                            });
                        });
                        break;
                    case $rootScope.app.data.widgets.date:
                        AppUtils.loadTemplate($scope, focus.setting_template, true, function (html) {
                            var objPicker = container.find('[name="datepicker"]');
                            var info = objPicker.data('info');
                            var dateFormat = objPicker.data('date-format');

                            if (typeof info === 'undefined') {
                                info = {};
                            }

                            // Current statuses
                            // time stamp
                            html.find('[name="current_timestamp"]').prop({
                                'checked': info['current_timestamp'] ? info['current_timestamp'] : false
                            });

                            // Editable
                            html.find('[name="editable"]').prop({
                                'checked': info['editable'] ? info['editable'] : false
                            });

                            AppHelper.showModalWindow(html, '#', function () {
                                // Trigger click
                                html.on('click', '.btn-submit', function () {
                                    var currentTimestamp = html.find('[name="current_timestamp"]');
                                    info['current_timestamp'] = currentTimestamp.is(':checked');
                                    var editable = html.find('[name="editable"]');
                                    info['editable'] = editable.is(':checked');
                                    // date format
                                    info['date_format'] = dateFormat;
                                    objPicker.attr('data-info', JSON.stringify(info));

                                    //
                                    if (info['current_timestamp']) {
                                        var timestamp = new Date();
                                        var currentDate = AppHelper.formatDate(dateFormat, timestamp);
                                        objPicker.attr({
                                            'value': currentDate
                                        });
                                        // Update
                                        objPicker.datepicker('update', timestamp);
                                    }

                                    AppHelper.hideModalWindow();
                                });
                            });
                        });
                        break;
                    case $rootScope.app.data.widgets.datetime:
                        AppUtils.loadTemplate($scope, focus.setting_template, true, function (html) {
                            var objPicker = container.find('[name="datetimepicker"]');
                            var info = objPicker.data('info');
                            var datetime_format = objPicker.data('datetime-format');
                            var date_format = objPicker.data('date-format');
                            var time_format = objPicker.data('time-format');

                            if (typeof info === 'undefined') {
                                info = {};
                            }

                            // Current statuses
                            // time stamp
                            html.find('[name="current_timestamp"]').prop({
                                'checked': info['current_timestamp'] ? info['current_timestamp'] : false
                            });

                            // Editable
                            html.find('[name="editable"]').prop({
                                'checked': info['editable'] ? info['editable'] : false
                            });

                            AppHelper.showModalWindow(html, '#', function () {
                                // Trigger click
                                html.on('click', '.btn-submit', function () {
                                    var currentTimestamp = html.find('[name="current_timestamp"]');
                                    info['current_timestamp'] = currentTimestamp.is(':checked');
                                    var editable = html.find('[name="editable"]');
                                    info['editable'] = editable.is(':checked');
                                    // date format
                                    info['datetime_format'] = datetime_format;
                                    info['date_format'] = date_format;
                                    info['time_format'] = time_format;
                                    objPicker.attr('data-info', JSON.stringify(info));

                                    var currentTime = '00:00';

                                    //
                                    if (info['current_timestamp']) {
                                        var timestamp = new Date();
                                        var currentDate = AppHelper.formatDate(date_format, timestamp);
                                        objPicker.attr({
                                            'value': currentDate + ' ' + currentTime
                                        });
                                        // // Update
                                        // objPicker.datepicker('update', timestamp);
                                    }

                                    AppHelper.hideModalWindow();
                                });
                            });
                        });
                        break;
                    case $rootScope.app.data.blocks.pricing_table:
                        AppUtils.loadTemplate($scope, focus.setting_template, false, function (html) {
                            // Before open modal
                            var contenteditable = editable.find('[contenteditable="true"]');

                            if (!contenteditable || contenteditable.length == 0) {
                                return;
                            }

                            var objTable = contenteditable.find('table');
                            var info = objTable.data('info');

                            if (typeof info === 'undefined') {
                                info = {};
                            }

                            $scope.inventoryFields = [];
                            $scope.inventoryFields.length = 0;

                            if (idxProductBlockModules[$rootScope.app.model.module]) {
                                $scope.inventoryFields = angular.copy(productModuleFields);

                                var module = angular.copy(idxProductBlockModules[$rootScope.app.model.module]);
                                // Final fields
                                $scope.tableBlockTotal = module['final_details'];
                                // fields
                                var fields = module.fields;
                                var field = null;
                                var LBL_ITEM_DETAILS = [];
                                var idxRemove = [];

                                for (var f = 0; f < fields.length; f++) {
                                    field = fields[f];
                                    field.block.label = module.name + ' - ' + field.block.label;

                                    if (field.block.name == 'LBL_ITEM_DETAILS') {
                                        LBL_ITEM_DETAILS.push(field);
                                        idxRemove.push(f);
                                    }
                                }

                                // Remove duplicate before concat
                                var removed = 0;
                                for (var f = 0; f < idxRemove.length; f++) {
                                    fields.splice((idxRemove[f] - removed), 1);

                                    removed++;
                                }

                                $scope.inventoryFields = $.merge($scope.inventoryFields, fields);
                                // Add Item detail to first
                                $scope.inventoryFields = LBL_ITEM_DETAILS.concat($scope.inventoryFields);
                            }

                            // Reset settings
                            $scope.settings.pricing_table.theme = $scope.tableBlockTheme[0];    // Default is first theme
                            $scope.settings.pricing_table.total_fields = [];
                            $scope.settings.pricing_table.total_fields.length = 0;
                            $scope.settings.pricing_table.total_fields_order = [];
                            $scope.settings.pricing_table.total_fields_order.length = 0;
                            $scope.settings.pricing_table.item_fields = [];
                            $scope.settings.pricing_table.item_fields.length = 0;
                            $scope.settings.pricing_table.item_fields_order = [];
                            $scope.settings.pricing_table.item_fields_order.length = 0;

                            // Default selected
                            if (info['settings']) {
                                var selected = null;
                                var selectedItem = null;
                                var inventoryFieldItem = null;

                                // item fields
                                if (info['settings']['item_fields']) {
                                    selected = info['settings']['item_fields'];

                                    for (var sf = 0; sf < selected.length; sf++) {
                                        selectedItem = selected[sf];

                                        for (var inv = 0; inv < $scope.inventoryFields.length; inv++) {
                                            inventoryFieldItem = $scope.inventoryFields[inv];

                                            if ((inventoryFieldItem.module == selectedItem.module)
                                                && (inventoryFieldItem.block.id == selectedItem.block.id)
                                                && (inventoryFieldItem.id == selectedItem.id)
                                                && (inventoryFieldItem.name == selectedItem.name)) {
                                                $scope.settings.pricing_table.item_fields.push($scope.inventoryFields[inv]);
                                            }
                                        }
                                    }
                                }

                                // total fields
                                if (info['settings']['total_fields']) {
                                    selected = info['settings']['total_fields'];

                                    for (var sf = 0; sf < selected.length; sf++) {
                                        selectedItem = selected[sf];

                                        for (var inv = 0; inv < $scope.tableBlockTotal.length; inv++) {
                                            inventoryFieldItem = $scope.tableBlockTotal[inv];

                                            if ((inventoryFieldItem.module == selectedItem.module)
                                                && (inventoryFieldItem.block.id == selectedItem.block.id)
                                                && (inventoryFieldItem.id == selectedItem.id)
                                                && (inventoryFieldItem.name == selectedItem.name)) {
                                                $scope.settings.pricing_table.total_fields.push($scope.tableBlockTotal[inv]);
                                            }
                                        }
                                    }
                                }

                                // Theme
                                if (info['settings']['theme']) {
                                    selectedItem = info['settings']['theme'];

                                    for (var inv = 0; inv < $scope.tableBlockTheme.length; inv++) {
                                        inventoryFieldItem = $scope.tableBlockTheme[inv];

                                        if ((inventoryFieldItem.id == selectedItem.id)
                                            && (inventoryFieldItem.name == selectedItem.name)) {
                                            $scope.settings.pricing_table.theme = $scope.tableBlockTheme[inv];
                                            break;
                                        }
                                    }
                                }

                                // order item fields
                                if (info['settings']['item_fields_order']) {
                                    $scope.settings.pricing_table.item_fields_order = info['settings']['item_fields_order'];
                                }

                                // order total fields
                                if (info['settings']['total_fields_order']) {
                                    $scope.settings.pricing_table.total_fields_order = info['settings']['total_fields_order'];
                                }

                            }

                            // Open modal
                            app.showModalWindow(html, '#', function () {
                                var settingsPricingTableFields = html.find('#settings_pricing_table_fields');
                                var settingsPricingTotalFields = html.find('#settings_pricing_total_fields');

                                $timeout(function () {
                                    AppHelper.arrangeSelectChoicesInOrder(settingsPricingTableFields, $scope.settings.pricing_table.item_fields_order);
                                    AppHelper.arrangeSelectChoicesInOrder(settingsPricingTotalFields, $scope.settings.pricing_table.total_fields_order);
                                }, 100);

                                // Trigger click
                                html.on('click', '.btn-submit', function () {
                                    // order item fields
                                    var itemFieldIds = AppHelper.getSelectedColumns(settingsPricingTableFields);
                                    var itemFields = [];
                                    var itemFieldSortedOption = null;
                                    var itemFieldSortedInfo = {};

                                    for (var id = 0; id < itemFieldIds.length; id++) {
                                        itemFieldSortedOption = settingsPricingTableFields.find('option[value="' + itemFieldIds[id] + '"]:selected');
                                        itemFieldSortedInfo = itemFieldSortedOption.data('info');
                                        itemFields.push(itemFieldSortedInfo);
                                    }

                                    $scope.settings.pricing_table.item_fields_order = itemFieldIds;

                                    // Total field order
                                    var totalFieldIds = AppHelper.getSelectedColumns(settingsPricingTotalFields);
                                    var totalFields = [];
                                    var totalFieldSortedOption = null;
                                    var totalFieldSortedInfo = {};

                                    for (var id = 0; id < totalFieldIds.length; id++) {
                                        totalFieldSortedOption = settingsPricingTotalFields.find('option[value="' + totalFieldIds[id] + '"]:selected');
                                        totalFieldSortedInfo = totalFieldSortedOption.data('info');
                                        totalFields.push(totalFieldSortedInfo);
                                    }

                                    $scope.settings.pricing_table.total_fields_order = totalFieldIds;

                                    var selectedItem = null;
                                    var colHeader = [],
                                        colItem = [],
                                        colFooter = [];

                                    for (var i = 0; i < itemFields.length; i++) {
                                        selectedItem = itemFields[i];
                                        colHeader.push(selectedItem.label);
                                        colItem.push(selectedItem);
                                    }

                                    for (var i = 0; i < totalFields.length; i++) {
                                        selectedItem = totalFields[i];
                                        colFooter.push({
                                            label: selectedItem.label,
                                            token: selectedItem.token
                                        });
                                    }

                                    // Save settings
                                    info['settings'] = angular.copy($scope.settings.pricing_table);
                                    objTable.attr('data-info', angular.toJson(info));

                                    // Set table style:
                                    if (info['settings'] && info['settings']['theme'] && info['settings']['theme']['settings']
                                        && info['settings']['theme']['settings']['style']) {
                                        // objTable.css(info['settings']['theme']['settings']['style']);
                                    }

                                    // Cell style:
                                    var cellStyle = {};
                                    if (info['settings'] && info['settings']['theme'] && info['settings']['theme']['settings']
                                        && info['settings']['theme']['settings']['cell'] && info['settings']['theme']['settings']['cell']['style']) {
                                        cellStyle = info['settings']['theme']['settings']['cell']['style'];

                                        objTable.find('th, td').css(cellStyle);
                                    }

                                    // Replace table
                                    // header
                                    var thead = objTable.find('thead');

                                    // Set thead style:
                                    var theadCellStyle = {};
                                    if (info['settings'] && info['settings']['theme'] && info['settings']['theme']['settings']
                                        && info['settings']['theme']['settings']['thead'] && info['settings']['theme']['settings']['thead']['style']) {
                                        theadCellStyle = $.extend({}, cellStyle, info['settings']['theme']['settings']['thead']['style']);
                                        thead.find('th, td').css(theadCellStyle);
                                    }

                                    var theadRows = thead.find('tr');
                                    var theadRow = null;
                                    var objTheadRow = null;
                                    var firstTheadCell = null;
                                    var newTheadCell = null;

                                    for (var thr = 0; thr < theadRows.length; thr++) {
                                        theadRow = theadRows[thr];
                                        objTheadRow = $(theadRow);
                                        firstTheadCell = objTheadRow.find('th, td').filter(':first');
                                        if (firstTheadCell && firstTheadCell.length > 0) {
                                            firstTheadCell = firstTheadCell.clone();
                                        } else {
                                            firstTheadCell = $('<th/>');
                                        }

                                        objTheadRow.empty();

                                        for (var i = 0; i < colHeader.length; i++) {
                                            newTheadCell = firstTheadCell.clone();
                                            newTheadCell.text(colHeader[i]);
                                            objTheadRow.append(newTheadCell);
                                        }
                                    }

                                    // body
                                    var tbody = objTable.find('tbody');
                                    var arrTr = tbody.find('tr');
                                    var tr = null,
                                        trText = null,
                                        trBlockStart = '',
                                        objTrBlockStart = null,
                                        trBlockEnd = '',
                                        objTrBlockEnd = null,
                                        markCell = null,
                                        objTrBlockItem = [],
                                        isStart = false,
                                        isEnd = false,
                                        oddStyle = {},
                                        evenStyle = {};

                                    if (info['settings'] && info['settings']['theme'] && info['settings']['theme']['settings']
                                        && info['settings']['theme']['settings']['tbody'] && info['settings']['theme']['settings']['tbody']['odd']
                                        && info['settings']['theme']['settings']['tbody']['odd']['style']) {
                                        oddStyle = info['settings']['theme']['settings']['tbody']['odd']['style'];
                                    }

                                    if (info['settings'] && info['settings']['theme'] && info['settings']['theme']['settings']
                                        && info['settings']['theme']['settings']['tbody'] && info['settings']['theme']['settings']['tbody']['even']
                                        && info['settings']['theme']['settings']['tbody']['even']['style']) {
                                        evenStyle = info['settings']['theme']['settings']['tbody']['even']['style'];
                                    }

                                    tbody.addAttributes({
                                        'data-odd-style': JSON.stringify(oddStyle),
                                        'data-even-style': JSON.stringify(evenStyle)
                                    });

                                    for (var i = 0; i < arrTr.length; i++) {
                                        tr = $(arrTr[i]);
                                        trText = tr.text();
                                        trText = trText ? trText.trim() : '';

                                        if (trText == '#PRODUCTBLOC_START#') {
                                            objTrBlockStart = tr;
                                            markCell = tr.find('td:contains(' + trText + ')');
                                            markCell.attr('colspan', (colHeader.length));
                                            markCell.css(oddStyle);
                                            trBlockStart = tr[0].outerHTML;
                                            isStart = true;
                                            continue;
                                        }

                                        if (trText && trText.trim() == '#PRODUCTBLOC_END#') {
                                            objTrBlockEnd = tr;
                                            markCell = tr.find('td:contains(' + trText + ')');
                                            markCell.attr('colspan', (colHeader.length));
                                            markCell.css(oddStyle);
                                            trBlockEnd = tr[0].outerHTML;
                                            isEnd = true;
                                            continue;
                                        }

                                        // item row
                                        if (isStart && !isEnd) {
                                            objTrBlockItem.push(tr);
                                        }
                                    }

                                    var tbodyRow = null;
                                    var objTbodyRow = null;
                                    var firstTbodyCell = null;
                                    var newTbodyCell = null;
                                    var arrNumberTypes = ['currency', 'double', 'integer', 'float'];

                                    for (var tbr = 0; tbr < objTrBlockItem.length; tbr++) {
                                        tbodyRow = objTrBlockItem[tbr];
                                        objTbodyRow = $(tbodyRow);
                                        firstTbodyCell = objTbodyRow.find('th, td').filter(':first');
                                        if (firstTbodyCell && firstTbodyCell.length > 0) {
                                            firstTbodyCell = firstTbodyCell.clone();
                                        } else {
                                            firstTbodyCell = $('<td/>');
                                        }

                                        objTbodyRow.empty();

                                        for (var i = 0; i < colItem.length; i++) {
                                            newTbodyCell = firstTbodyCell.clone();
                                            newTbodyCell.text(colItem[i].token);
                                            if ($.inArray(colItem[i].datatype, arrNumberTypes) >= 0) {
                                                newTbodyCell.css({
                                                    'text-align': 'right'
                                                })
                                            }

                                            objTbodyRow.append(newTbodyCell);
                                        }
                                    }

                                    // footer
                                    var tfoot = objTable.find('tfoot');
                                    var footerColspan1 = colHeader.length - 1;
                                    var tfootRow = null;
                                    var newFootRow = null;
                                    var newFootCell = null;

                                    var objFootRow = tfoot.find('tr:first');
                                    if (tfootRow && tfootRow.length > 0) {
                                        tfootRow.clone();
                                    } else {
                                        tfootRow = $('<tr/>');
                                    }

                                    var firstFootCell = objFootRow.find('th, td').filter(':first');
                                    if (firstFootCell && firstFootCell.length > 0) {
                                        firstFootCell = firstFootCell.clone();
                                    } else {
                                        firstFootCell = $('<td/>');
                                    }

                                    // Empty tfoot
                                    tfoot.empty();

                                    for (var i = 0; i < colFooter.length; i++) {
                                        selectedItem = colFooter[i];
                                        newFootRow = tfootRow.clone();
                                        newFootRow.empty();
                                        // Label
                                        newFootCell = firstFootCell.clone();
                                        newFootCell.attr('colspan', footerColspan1);
                                        newFootCell.css({
                                            'text-align': 'right'
                                        });
                                        newFootCell.text(selectedItem.label);
                                        newFootRow.append(newFootCell);
                                        // Value
                                        newFootCell = firstFootCell.clone();
                                        newFootCell.removeAttr('colspan');
                                        newFootCell.css({
                                            'text-align': 'right'
                                        });
                                        newFootCell.text(selectedItem.token);
                                        newFootRow.append(newFootCell);

                                        tfoot.append(newFootRow);
                                    }

                                    // Close modal
                                    app.hideModalWindow();
                                });
                            });
                            $compile(html.contents())($scope);
                        });
                        break;
                    case $rootScope.app.data.blocks.guest_block:
                        AppUtils.loadTemplate($scope, focus.setting_template, false, function (html) {
                            // Before open modal
                            var contenteditable = editable.find('[contenteditable="true"]');

                            if (!contenteditable || contenteditable.length == 0) {
                                return;
                            }

                            var objTable = contenteditable.find('table');
                            var info = objTable.data('info');

                            if (typeof info === 'undefined') {
                                info = {};
                            }

                            $scope.guestBlockFields = [];
                            $scope.guestBlockFields.length = 0;

                            //
                            var guestModule = objTable.data('module');
                            var guestGuestBlock = objTable.data('guest-block');
                            var module = $rootScope.app.data.idxModules[guestModule];
                            var block = angular.copy(module.idx_guest_blocks[guestGuestBlock]);

                            $scope.guestBlockFields = block.fields;

                            // Reset settings
                            $scope.settings.guest_block.theme = null;    // Default is first theme: $scope.tableBlockTheme[0]
                            $scope.settings.guest_block.item_fields = [];
                            $scope.settings.guest_block.item_fields.length = 0;
                            $scope.settings.guest_block.item_fields_order = [];
                            $scope.settings.guest_block.item_fields_order.length = 0;

                            // Default selected
                            if (info['settings']) {
                                var selected = null;
                                var selectedItem = null;

                                // item fields
                                if (info['settings']['item_fields']) {
                                    selected = info['settings']['item_fields'];
                                    var field = null;

                                    for (var sf = 0; sf < selected.length; sf++) {
                                        selectedItem = selected[sf];

                                        for (var gbf = 0; gbf < $scope.guestBlockFields.length; gbf++) {
                                            field = $scope.guestBlockFields[gbf];

                                            if ((field.id == selectedItem.id)
                                                && (field.name == selectedItem.name)) {
                                                $scope.settings.guest_block.item_fields.push($scope.guestBlockFields[gbf]);
                                            }
                                        }
                                    }
                                }

                                // Theme
                                if (info['settings']['theme']) {
                                    selectedItem = info['settings']['theme'];
                                    var theme = null;

                                    for (var t = 0; t < $scope.tableBlockTheme.length; t++) {
                                        theme = $scope.tableBlockTheme[t];

                                        if ((theme.id == selectedItem.id)
                                            && (theme.name == selectedItem.name)) {
                                            $scope.settings.guest_block.theme = $scope.tableBlockTheme[t];
                                            break;
                                        }
                                    }
                                }

                                // order item fields
                                if (info['settings']['item_fields_order']) {
                                    $scope.settings.guest_block.item_fields_order = info['settings']['item_fields_order'];
                                }

                            } else {
                                var templateFieldSelected = [];

                                var tbodyContent = objTable.find('tbody > tr.tbody-content');
                                var objFieldContent = tbodyContent.find('td');
                                var objFieldContentItem = null;

                                for (var fct = 0; fct < objFieldContent.length; fct++) {
                                    objFieldContentItem = $(objFieldContent[fct]);
                                    templateFieldSelected.push(objFieldContentItem.text().trim());
                                }

                                // item fields
                                var field = null;

                                for (var sf = 0; sf < templateFieldSelected.length; sf++) {
                                    for (var gbf = 0; gbf < $scope.guestBlockFields.length; gbf++) {
                                        field = $scope.guestBlockFields[gbf];

                                        if (field.token == templateFieldSelected[sf]) {
                                            $scope.settings.guest_block.item_fields.push($scope.guestBlockFields[gbf]);
                                        }
                                    }
                                }
                            }

                            // Open modal
                            app.showModalWindow(html, '#', function () {
                                var settingsGuestBlockFields = html.find('#settings_guest_block_fields');

                                if (info['settings']) {
                                    //
                                } else {
                                    var itemFieldIds = AppHelper.getSelectedColumns(settingsGuestBlockFields);
                                    $scope.settings.guest_block.item_fields_order = itemFieldIds;
                                }

                                $timeout(function () {
                                    AppHelper.arrangeSelectChoicesInOrder(settingsGuestBlockFields, $scope.settings.guest_block.item_fields_order);
                                }, 100);

                                // Trigger click
                                html.on('click', '.btn-submit', function () {
                                    // order item fields
                                    var itemFieldIds = AppHelper.getSelectedColumns(settingsGuestBlockFields);
                                    var itemFields = [];
                                    var itemFieldSortedOption = null;
                                    var itemFieldSortedInfo = {};

                                    for (var id = 0; id < itemFieldIds.length; id++) {
                                        itemFieldSortedOption = settingsGuestBlockFields.find('option[value="' + itemFieldIds[id] + '"]:selected');
                                        itemFieldSortedInfo = itemFieldSortedOption.data('info');
                                        itemFields.push(itemFieldSortedInfo);
                                    }

                                    $scope.settings.guest_block.item_fields_order = itemFieldIds;

                                    var selectedItem = null;
                                    var colHeader = [],
                                        colItem = [];

                                    for (var i = 0; i < itemFields.length; i++) {
                                        selectedItem = itemFields[i];
                                        colHeader.push(selectedItem.label);
                                        colItem.push(selectedItem);
                                    }

                                    // Save settings
                                    info['settings'] = angular.copy($scope.settings.guest_block);
                                    objTable.attr('data-info', angular.toJson(info));

                                    // Set table style:
                                    if (info['settings'] && info['settings']['theme'] && info['settings']['theme']['settings']
                                        && info['settings']['theme']['settings']['style']) {
                                        // objTable.css(info['settings']['theme']['settings']['style']);
                                    }

                                    // Cell style:
                                    var cellStyle = {};
                                    if (info['settings'] && info['settings']['theme'] && info['settings']['theme']['settings']
                                        && info['settings']['theme']['settings']['cell'] && info['settings']['theme']['settings']['cell']['style']) {
                                        cellStyle = info['settings']['theme']['settings']['cell']['style'];

                                        objTable.find('th, td').css(cellStyle);
                                    }

                                    // Replace table
                                    // header
                                    var thead = objTable.find('thead');

                                    // Set thead style:
                                    var theadCellStyle = {};
                                    if (info['settings'] && info['settings']['theme'] && info['settings']['theme']['settings']
                                        && info['settings']['theme']['settings']['thead'] && info['settings']['theme']['settings']['thead']['style']) {
                                        theadCellStyle = $.extend({}, cellStyle, info['settings']['theme']['settings']['thead']['style']);
                                        thead.find('th, td').css(theadCellStyle);
                                    }

                                    var theadRows = thead.find('tr');
                                    var theadRow = null;
                                    var objTheadRow = null;
                                    var firstTheadCell = null;
                                    var newTheadCell = null;

                                    for (var thr = 0; thr < theadRows.length; thr++) {
                                        theadRow = theadRows[thr];
                                        objTheadRow = $(theadRow);
                                        firstTheadCell = objTheadRow.find('th, td').filter(':first');
                                        if (firstTheadCell && firstTheadCell.length > 0) {
                                            firstTheadCell = firstTheadCell.clone();
                                        } else {
                                            firstTheadCell = $('<th/>');
                                        }

                                        objTheadRow.empty();

                                        for (var i = 0; i < colHeader.length; i++) {
                                            newTheadCell = firstTheadCell.clone();
                                            newTheadCell.text(colHeader[i]);
                                            objTheadRow.append(newTheadCell);
                                        }
                                    }

                                    // body
                                    var tbody = objTable.find('tbody');
                                    var arrTr = tbody.find('tr');
                                    var tr = null,
                                        trText = null,
                                        trBlockStart = '',
                                        objTrBlockStart = null,
                                        trBlockEnd = '',
                                        objTrBlockEnd = null,
                                        markCell = null,
                                        objTrBlockItem = [],
                                        isStart = false,
                                        isEnd = false,
                                        oddStyle = {},
                                        evenStyle = {};

                                    if (info['settings'] && info['settings']['theme'] && info['settings']['theme']['settings']
                                        && info['settings']['theme']['settings']['tbody'] && info['settings']['theme']['settings']['tbody']['odd']
                                        && info['settings']['theme']['settings']['tbody']['odd']['style']) {
                                        oddStyle = info['settings']['theme']['settings']['tbody']['odd']['style'];
                                    }

                                    if (info['settings'] && info['settings']['theme'] && info['settings']['theme']['settings']
                                        && info['settings']['theme']['settings']['tbody'] && info['settings']['theme']['settings']['tbody']['even']
                                        && info['settings']['theme']['settings']['tbody']['even']['style']) {
                                        evenStyle = info['settings']['theme']['settings']['tbody']['even']['style'];
                                    }

                                    tbody.addAttributes({
                                        'data-odd-style': JSON.stringify(oddStyle),
                                        'data-even-style': JSON.stringify(evenStyle)
                                    });

                                    for (var i = 0; i < arrTr.length; i++) {
                                        tr = $(arrTr[i]);
                                        trText = tr.text();
                                        trText = trText ? trText.trim() : '';

                                        if (trText == '#GUESTBLOC_START#') {
                                            objTrBlockStart = tr;
                                            markCell = tr.find('td:contains(' + trText + ')');
                                            markCell.attr('colspan', (colHeader.length));
                                            markCell.css(oddStyle);
                                            trBlockStart = tr[0].outerHTML;
                                            isStart = true;
                                            continue;
                                        }

                                        if (trText && trText.trim() == '#GUESTBLOC_END#') {
                                            objTrBlockEnd = tr;
                                            markCell = tr.find('td:contains(' + trText + ')');
                                            markCell.attr('colspan', (colHeader.length));
                                            markCell.css(oddStyle);
                                            trBlockEnd = tr[0].outerHTML;
                                            isEnd = true;
                                            continue;
                                        }

                                        // item row
                                        if (isStart && !isEnd) {
                                            objTrBlockItem.push(tr);
                                        }
                                    }

                                    var tbodyRow = null;
                                    var objTbodyRow = null;
                                    var firstTbodyCell = null;
                                    var newTbodyCell = null;
                                    var arrNumberTypes = ['currency', 'double', 'integer', 'float'];

                                    for (var tbr = 0; tbr < objTrBlockItem.length; tbr++) {
                                        tbodyRow = objTrBlockItem[tbr];
                                        objTbodyRow = $(tbodyRow);
                                        firstTbodyCell = objTbodyRow.find('th, td').filter(':first');
                                        if (firstTbodyCell && firstTbodyCell.length > 0) {
                                            firstTbodyCell = firstTbodyCell.clone();
                                        } else {
                                            firstTbodyCell = $('<td/>');
                                        }

                                        objTbodyRow.empty();

                                        for (var i = 0; i < colItem.length; i++) {
                                            newTbodyCell = firstTbodyCell.clone();
                                            newTbodyCell.text(colItem[i].token);
                                            if (colItem[i].datatype && $.inArray(colItem[i].datatype, arrNumberTypes) >= 0) {
                                                newTbodyCell.css({
                                                    'text-align': 'right'
                                                })
                                            }

                                            objTbodyRow.append(newTbodyCell);
                                        }
                                    }

                                    // Close modal
                                    app.hideModalWindow();
                                });
                            });
                            $compile(html.contents())($scope);
                        });
                        break;
                    case $rootScope.app.data.blocks.image:
                        AppUtils.loadTemplate($scope, focus.setting_template, true, function (html) {
                            // Before open modal
                            var contenteditable = editable.find('[contenteditable="true"]');

                            if (!contenteditable || contenteditable.length == 0) {
                                return;
                            }

                            var contenteditableId = contenteditable.attr('id');

                            var objTextField = container.find('img');
                            var info = objTextField.data('info');

                            if (typeof info === 'undefined') {
                                info = {};
                            }

                            html.find('[name="edge_to_edge"]').prop({
                                'checked': info['edge_to_edge'] ? info['edge_to_edge'] : false
                            });

                            html.find('[name="flatten"]').prop({
                                'checked': info['flatten'] ? info['flatten'] : false
                            });

                            AppHelper.showModalWindow(html, '#', function () {
                                // Trigger click
                                html.on('click', '.btn-submit', function () {
                                    if (typeof info['edge_to_edge'] === 'undefined') {
                                        // get original width before change
                                        info['original_width'] = objTextField[0].style.width;
                                        info['original_height'] = objTextField[0].style.height;
                                        info['height'] = objTextField.height();
                                    }

                                    // feature with flatten
                                    var sortable = container.find('.doc-block__control--drag');
                                    var editor = CKEDITOR.instances[contenteditableId];
                                    var myEditor = {editor: editor};
                                    var imageContextMenu = myEditor.editor._.menuItems.image;

                                    if (typeof info['flatten'] === 'undefined') {
                                        // get original image context menu before change
                                        var contextmenu_image = jQuery.extend({}, imageContextMenu);
                                        delete contextmenu_image.editor;
                                        contextmenu_image.group = 'image';
                                        info['contextmenu_image'] = contextmenu_image;
                                    }

                                    var edge_to_edge = html.find('[name="edge_to_edge"]');
                                    info['edge_to_edge'] = edge_to_edge.is(':checked');
                                    var flatten = html.find('[name="flatten"]');
                                    info['flatten'] = flatten.is(':checked');

                                    // set width for image
                                    if (info['edge_to_edge']) {
                                        objTextField.css({
                                            'width': '798px',
                                            'height': info['height'],
                                            'margin-left': '-60px',
                                            'margin-right': '-60px'
                                        });
                                    } else {
                                        objTextField.css({
                                            'width': (info['original_width']) ? info['original_width'] : '100%',
                                            'height': (info['original_height']) ? info['original_height'] : '',
                                            'margin-left': '',
                                            'margin-right': ''
                                        });
                                    }

                                    if (info['flatten']) {
                                        sortable.addClass('hide');
                                        var itemsToRemove = ['image'];
                                        $rootScope.customCKEditorContextMenu(myEditor, null, itemsToRemove);
                                    } else {
                                        sortable.removeClass('hide');
                                        var itemsToAdd = [
                                            {
                                                name: 'image',
                                                options: info['contextmenu_image']
                                            }
                                        ];
                                        $rootScope.customCKEditorContextMenu(myEditor, itemsToAdd);
                                    }

                                    // Save settings
                                    objTextField.attr('data-info', JSON.stringify(info));

                                    AppHelper.hideModalWindow();
                                });
                            });
                        });
                        break;
                    default:
                        AppUtils.loadTemplate($scope, focus.setting_template, true, function (html) {
                            var form = html.find('.form');
                            var key = null;
                            var value = null;

                            var fields = form.find('input, select, textarea');
                            for (var i = 0; i < fields.length; i++) {
                                var field = fields[i];
                                var objField = $(field);
                                var type = field.type;

                                switch (type) {
                                    case 'checkbox':
                                        key = objField.attr('name');
                                        value = info[key] ? info[key] : false;
                                        objField.prop({
                                            'checked': value
                                        });
                                        break;
                                    case 'text':
                                        if(objField.hasClass("color_picker") || objField.hasClass("fore_color")) {
                                            var classObj =  objField.attr('class');
                                            registerColorPicker(form,classObj);
                                            key = objField.attr('name');
                                            value = info[key] ? info[key] : '';
                                            objField.css("background-color", value);
                                            objField.val(value);
                                        }
                                        break;
                                    case 'select-one':
                                        if(objField.hasClass("fontFamSelect")){
                                            value = info['fontName'] ? info['fontName'] : '';
                                            objField.find('option[value="' + value + '"]').attr('selected', 'selected');
                                        }else if(objField.hasClass("fontSizeSelect")){
                                            value = info['fontSize'] ? info['fontSize'] : '';
                                            objField.find('option[value="' + value + '"]').attr('selected', 'selected');
                                        }
                                        break;
                                    default:
                                        break;
                                }
                            }
                            var fontWeight = form.find('.cke_button');
                            for (var i = 0; i < fontWeight.length; i++) {
                                var field = fontWeight[i];
                                var objField = $(field);
                                key = objField.attr('title');
                                value = info[key] ? info[key] : false;
                                if(value=='true') {
                                    if($(objField).hasClass('cke_button_off')){
                                        $(objField).removeClass('cke_button_off').addClass('cke_button_on');
                                    }
                                    if($(objField).hasClass('align_off')){
                                        $(objField).removeClass('align_off').addClass('align_on');
                                    }
                                }
                            }
                            registerFormatOption(form);
                            registerfontweigth(form);
                            AppHelper.showModalWindow(html);
                        });
                        break;
                }
            };
            var registerFormatOption = function (form) {
                var valEditable = jQuery(form).find("input[name='editable']");
                var FormatOption = jQuery(form).find(".formatOption");
                if(valEditable.is(":checked")) {
                    FormatOption.css("display","none");
                }else {
                    FormatOption.css("display","block");
                }
                valEditable.on("click",function () {
                    if(valEditable.is(":checked")) {
                        FormatOption.css("display","none");
                    }else {
                        FormatOption.css("display","block");
                    }
                })

            };
            var registerfontweigth = function (form) {
                jQuery(form).find('.cke_button').on('click',function () {
                    var focus = $(this);
                    if(focus.hasClass('cke_button_off')) {
                        focus.removeClass('cke_button_off').addClass('cke_button_on');
                        focus.css('background',"#C2D5F2")
                    }else if(focus.hasClass('cke_button_on')) {
                        focus.removeClass('cke_button_on').addClass('cke_button_off');
                        focus.css('background',"#FFFFFF")
                    }

                    if(focus.hasClass('align_off')) {
                        jQuery(form).find('.align_on').removeClass('align_on').addClass('align_off');
                        focus.removeClass('align_off').addClass('align_on');
                    }else if(focus.hasClass('align_on')) {
                        focus.removeClass('align_on').addClass('align_off');
                    }
                })
            };
            var  registerColorPicker = function(form,input){
                jQuery(form).find('input[name="'+input+'"]').ColorPicker({
                    color: '#0000ff',
                    onShow: function (colpkr) {
                        jQuery(colpkr).fadeIn(500);
                        jQuery(colpkr).css({'zIndex': '10010'});
                        // jQuery(colpkr).css({'position': 'fixed'});
                        return false;
                    },
                    onHide: function (colpkr) {
                        jQuery(colpkr).fadeOut(500);
                        return false;
                    },
                    onChange: function (hsb, hex, rgb) {
                        jQuery('input[name="'+input+'"]').css('backgroundColor', '#' + hex);
                        jQuery('input[name="'+input+'"]').val('#' + hex);
                    }
                }).bind('keyup', function(){
                    jQuery(this).ColorPickerSetColor(this.value);
                });
            };

            /**
             * Fn - changeSetting
             * @param $event
             */
            $rootScope.changeSetting = function ($event) {
                var target = $($event.target);
                var mainContainer = target.closest('.modal-settings');
                var form = mainContainer.find('.form');
                // var settingBox = modal.find('.modal-settings');
                // var dataType = settingBox.data('type');
                // var dataTarget = settingBox.data('target');

                var key = null;
                var value = null;

                if (!$rootScope.app.last_focus_item_setting || $rootScope.app.last_focus_item_setting.length == 0) {
                    // For blocks and others
                    return;
                }

                // if OK
                var newInfo = {};
                var fields = form.find('input, select, textarea');

                for (var i = 0; i < fields.length; i++) {
                    var field = fields[i];
                    var objField = $(field);
                    var type = field.type;

                    switch (type) {
                        case 'checkbox':
                            key = objField.attr('name');
                            value = objField.is(':checked');
                            break;
                        case 'text':
                            if(objField.hasClass("color_picker") || objField.hasClass("fore_color")) {
                                key = objField.attr('name');
                                value = objField.val();
                            }
                            break;
                        default:
                            break;
                    }

                    newInfo[key] = value;
                }
                var fontWeight = form.find('.cke_button');
                for (var i = 0; i < fontWeight.length; i++) {
                    var field = fontWeight[i];
                    var objField = $(field);
                    var type = field.type;
                    key = objField.attr('title');
                    if($(objField).hasClass('cke_button_on')) {
                        value = 'true';
                    }else if ($(objField).hasClass('cke_button_off')) {
                        value = 'false';
                    }
                    if($(objField).hasClass('align_on')) {
                        value = 'true';
                    }else if ($(objField).hasClass('align_off')) {
                        value = 'false';
                    }
                    newInfo[key] = value;
                }

                //font family
                var fontfamily =form.find(".fontFamSelect").val();
                newInfo['fontName'] = fontfamily;
                //font size
                var fontSize = form.find('.fontSizeSelect').val();
                newInfo['fontSize'] = fontSize;

                // Update for all item focus
                $rootScope.app.last_focus_item_setting.each(function () {
                    var thisFocus = $(this);
                    var container = thisFocus.closest(".content-container");
                    var parentFocus = thisFocus.parent();
                    var markRequired = parentFocus.find('.mark-required');
                    var info = thisFocus.data('info');

                    if (typeof info === 'undefined') {
                        info = {};
                    }

                    $.extend(info, newInfo);
                    thisFocus.attr('data-info', JSON.stringify(info));


                    var editable = newInfo['editable'];
                    if(editable === false) {
                        if(newInfo['color_picker'] == ''){
                            newInfo['color_picker'] = 'rgba(255,255,255,0)';
                        }
                        if(newInfo['fore_color'] == ''){
                            newInfo['fore_color'] = '#808080';
                        }
                        //background-color
                        thisFocus.css('background-color', newInfo['color_picker']);
                        thisFocus.css('color', newInfo['fore_color']);

                        //font-weight
                        if(newInfo['Bold']=='true'){
                            thisFocus.css("font-weight", "bold");
                        }else if(newInfo['Bold']=='false'){
                            thisFocus.css("font-weight", "normal");
                        }

                        //font-style
                        if(newInfo['Italic']=='true'){
                            thisFocus.css("font-style", "italic");
                        }else if(newInfo['Italic']=='false'){
                            thisFocus.css("font-style", "normal");
                        }

                        //text-decoration
                        if(newInfo['Underline']=='true'){
                            thisFocus.css("text-decoration", "underline");
                        }else if(newInfo['Underline']=='false'){
                            thisFocus.css("text-decoration", "none");
                        }

                        // Align text
                        if(newInfo['AlignLeft']=='true'){
                            thisFocus.css("text-align", "left");
                        }else if(newInfo['Center']=='true'){
                            thisFocus.css("text-align", "center");
                        }else if(newInfo['AlignRight']=='true') {
                            thisFocus.css("text-align", "right");
                        }else{
                            thisFocus.css("text-align", "left");
                        }

                        // font-family
                        thisFocus.css("font-family", newInfo['fontName']);

                        // font-size
                        thisFocus.css("font-size",newInfo['fontSize']);
                    }else {
                        thisFocus.css('background-color', "rgba(255,255,255,0)");
                        thisFocus.css('background-color', "#808080");
                        thisFocus.css("font-weight", "normal");
                        thisFocus.css("font-style", "normal");
                        thisFocus.css("text-decoration", "none");
                        thisFocus.css("text-align", "left");
                        thisFocus.css("font-family", '"Helvetica Neue", Helvetica, Arial, sans-serif');
                        thisFocus.css("font-size","13px");
                    }
                    // Mandatory field
                    if (info['required']) {
                        if (!markRequired || markRequired.length == 0) {
                            thisFocus.after('<span class="mark-required">*</span>');
                        }
                    } else {
                        markRequired.remove();
                    }

                    //allow to edit
                    if (info['editable']) {
                        container.resizable( "disable" );
                        container.css("opacity","1.35")
                    } else {
                        container.resizable( "enable" );
                    }

                });

                // Close modal
                AppHelper.hideModalWindow();
            };

            $rootScope.changeTableBlockTheme = function ($event, theme) {
                $event.preventDefault();

                $scope.settings.pricing_table.theme = theme;
            };

            $rootScope.changeGuestBlockTheme = function ($event, theme) {
                $event.preventDefault();

                $scope.settings.guest_block.theme = theme;
            };

            /**
             * Fn - $rootScope.registerEventFocusInput
             */
            $rootScope.registerEventFocusInput = function (container) {
                if (!container) {
                    container = $rootScope.app.container;
                }

                // With input
                var input = $(container).find('input[type="text"]');
                $.each(input, function () {
                    $(this).on('focus', function () {
                        var thisFocus = $(this);
                        $rootScope.app.last_focus_item = {
                            type: AppConstants.FOCUS_TYPE.INPUT,
                            focus: thisFocus
                        };
                    });
                });

                // With textarea
                var textarea = $(container).find('textarea');
                $.each(textarea, function () {
                    $(this).on('focus', function () {
                        var thisFocus = $(this);
                        $rootScope.app.last_focus_item = {
                            type: AppConstants.FOCUS_TYPE.TEXTAREA,
                            focus: thisFocus
                        };
                    });
                });

                // With CKEditor
                for (var name in CKEDITOR.instances) {
                    if (!CKEDITOR.instances.hasOwnProperty(name)) {
                        continue;
                    }

                    var thisCKEditor = CKEDITOR.instances[name];

                    (function (thisCKEditor) {
                        thisCKEditor.on('focus', function () {
                            $rootScope.app.last_focus_item = {
                                type: AppConstants.FOCUS_TYPE.CKEDITOR,
                                focus: thisCKEditor
                            };
                        });
                    })(thisCKEditor);
                }

                // With contenteditable
                var contenteditable = $(container).find('[contenteditable]');
                $.each(contenteditable, function () {
                    $(this).on('focus', function () {
                        var thisFocus = $(this);
                        $rootScope.app.last_focus_item = {
                            type: AppConstants.FOCUS_TYPE.CONTENTEDITABLE,
                            focus: thisFocus
                        };
                    });
                });

            };

            /**
             * Fn - registerEventFocusPage
             */
            $rootScope.registerEventFocusPage = function () {
                // Init (first time)
                var pages = $(document).find('.quoting_tool-content').first();
                if (pages.length > 0) {
                    // Init first page
                    $rootScope.app.last_focus_page = pages;
                }

                // When click to switch the pages
                $rootScope.app.container.on('click', '.quoting_tool-content', function (event) {
                    var target = event.target;
                    var isRemovePage = $(target).hasClass('quoting_tool-icon-remove-page');

                    if (!isRemovePage && !$rootScope.app.last_focus_page.is($(this))) {
                        $rootScope.app.last_focus_page = $(this);
                    }
                });
            };

            /**
             * Fn - registerEventSupportOptions
             * @param focus
             */
            $rootScope.registerEventSupportOptions = function (focus) {
                if (!focus.hasClass('content-container')) {
                    focus = focus.find('.content-container');
                }

                // Show support actions
                focus.on('mouseenter', function () {
                    var thisInstance = $(this);
                    thisInstance.addClass('quoting_tool-border-dotted');
                    thisInstance.find('> .quoting_tool-btn-options').show();
                });

                // Hide support actions
                focus.on('mouseleave', function () {
                    var thisInstance = $(this);
                    thisInstance.removeClass('quoting_tool-border-dotted');
                    thisInstance.find('.quoting_tool-btn-options').hide();
                });
            };

            /**
             * Fn - registerEventCoverPageSupportOptions
             */
            $rootScope.registerEventCoverPageSupportOptions = function () {
                // Show support actions
                $(document).on('mouseenter', '.quoting_tool-cover-page', function () {
                    var thisFocus = $(this);
                    thisFocus.find('> .quoting_tool-cover_page-options').show();
                });

                // Hide support actions
                $(document).on('mouseleave', function () {
                    var thisFocus = $(this);
                    thisFocus.find('> .quoting_tool-cover_page-options').hide();
                });
            };

            /**
             * @param focus
             */
            $rootScope.calculateWidgetPosition = function (focus) {
                var container = focus.closest('.content-container.block-handle');
                var containerHeight = container.height();
                // Apply css for the draggable object
                var objAction = focus.find('.quoting_tool-draggable-object');
                var objActionWidth = objAction.width();
                var objActionHeight = objAction.height();
                var marginTop = 0;
                var tmp = 2;
                var preElement = focus.prev('.content-container.quoting_tool-draggable');

                if (preElement && preElement.length > 0) {
                    marginTop = focus[0].offsetTop - (preElement[0].offsetTop + preElement.height()) + tmp;
                } else {
                    marginTop = '-' + (containerHeight - focus[0].offsetTop + tmp);

                }

                var imgElement = container.find("img");
                if(imgElement && imgElement.length >0) {
                    imgElement.css("width", imgElement.width());
                }
                // Update css for the Object
                objAction.css({
                    'margin-top': parseInt(marginTop),
                    'margin-left': focus[0].offsetLeft,
                    'width': ((objActionWidth >= 26) ? objActionWidth : 26), // Fix checkbox block on PDF
                    'height': objActionHeight
                });

                var next = focus.next('.content-container.quoting_tool-draggable');

                if (next && next.length > 0) {
                    $rootScope.calculateWidgetPosition(next);
                }
            };

            /**
             * Fn - registerMoveOnContainer
             * Allow the widget moveable on the container
             *
             * @param focus
             */
            $rootScope.registerMoveOnContainer = function (focus) {
                focus.draggable({
                    handle: 'i.icon-move, i.icon-align-justify',
                    scope: 'add-widget-dropzone',
                    revert: 'invalid',
                    cursor: 'move',
                    stop: function (event, ui) {
                        var objContext = $(ui.helper.context);
                        // Current focus
                        $rootScope.calculateWidgetPosition(objContext);
                    }
                });
            };

            /**
             * Fn - registerDropToContainer
             * @param focus
             */
            $rootScope.registerDropToContainer = function (focus) {
                focus.droppable({
                    scope: 'add-widget-dropzone',
                    tolerance: 'pointer',
                    drop: function (event, ui) {
                        var thisPosition = $(focus[0]).offset();
                        var positionX = thisPosition.left;
                        var positionY = thisPosition.top;
                        var moveX = 0;
                        var moveY = 0;

                        if (ui.helper.hasClass('quoting_tool-drag-widget-component-to-content')) {
                            var id = ui.helper.data('id');

                            moveX = $rootScope.dragOffset.left;
                            moveY = $rootScope.dragOffset.top;

                            // Change the coordinate before append
                            var css = {
                                top: (moveY - positionY) + 'px',
                                left: (moveX - positionX) + 'px'
                            };

                            // Remove origin element
                            ui.helper.replaceWith('');

                            $timeout(function () {
                                // Trigger mouse position after drop widget
                                var blockContainer = $($rootScope.currentPosition.target).closest('.content-container.block-handle');
                                var args = {
                                    id: id,
                                    css: css,
                                    container: blockContainer
                                };
                                $rootScope.$broadcast('$evtAddWidget', args);
                            }, 10);
                        } else {
                            // Move a widget from a block to another block
                            var currentContainer = $(ui.draggable).closest('.content-container.block-handle');

                            if (!focus.is(currentContainer)) {
                                moveX = $rootScope.currentPosition.clientX;
                                moveY = $rootScope.currentPosition.clientY;

                                // Change the coordinate before append
                                ui.draggable.css({
                                    top: (moveY - positionY) + 'px',
                                    left: (moveX - positionX) + 'px'
                                });
                                focus.append(ui.draggable);

                                var objActions1 = currentContainer.find('.content-container.quoting_tool-draggable');
                                var objAction1 = null;
                                for (var i = 0; i < objActions1.length; i++) {
                                    objAction1 = $(objActions1[i]);
                                    // Re-calculate all widget positions
                                    $rootScope.calculateWidgetPosition(objAction1);
                                }

                                var objActions2 = focus.find('.content-container.quoting_tool-draggable');
                                var objAction2 = null;
                                for (var i = 0; i < objActions2.length; i++) {
                                    objAction2 = $(objActions2[i]);
                                    // Re-calculate all widget positions
                                    $rootScope.calculateWidgetPosition(objAction2);
                                }
                            }
                        }
                    }
                });
            };

            /**
             * @param templateId
             */
            $rootScope.loadTemplate = function (templateId) {
                var body = QuotingToolUtils.base64Decode($rootScope.app.model.body);

                var content = $(body);

                // Remove overlay on old template
                /** @link https://stackoverflow.com/questions/21659888/javascript-find-and-remove-object-in-array-based-on-key-value */
                content = $.grep(content, function(e){
                    return !$(e).is('.blockUI.blockOverlay');
                });
                content = $(content);

                // Replace on content (of template)
                $rootScope.app.container.find('.quoting_tool-content').replaceWith(content);

                /**
                 * Bind event for template
                 * @link http://stackoverflow.com/questions/18618069/angularjs-event-binding-in-directive-template-doesnt-work-if-mouseout-used-but
                 */
                $compile(content.contents())($scope);

                var contentMain = $rootScope.app.container.find('.quoting_tool-content-main');
                var elementType = AppConstants.COMPONENT_TYPE.BLOCK;

                var container = content.find('.content-container').each(function () {
                    var html = $(this);
                    // var myContentContainer = thisFocus.closest('.content-container');
                    var myDataId = $(html[0]).data('id');
                    var component = $rootScope.app.data.blocks.init;

                    // Match with blocks
                    for (var b in $rootScope.app.data.blocks) {
                        if (!$rootScope.app.data.blocks.hasOwnProperty(b)) {
                            continue;
                        }

                        var bItem = $rootScope.app.data.blocks[b];
                        if (bItem.template == myDataId) {
                            component = bItem;
                            elementType = AppConstants.COMPONENT_TYPE.BLOCK;
                        }
                    }

                    // Match with widget
                    for (var w in $rootScope.app.data.widgets) {
                        if (!$rootScope.app.data.widgets.hasOwnProperty(w)) {
                            continue;
                        }

                        var wItem = $rootScope.app.data.widgets[w];
                        if (wItem.template == myDataId) {
                            component = wItem;
                            elementType = AppConstants.COMPONENT_TYPE.WIDGET;
                        }
                    }

                    // Integrate CKEditor to the element
                    html.find('[contenteditable]').each(function () {
                        var thisFocus = $(this);
                        if (typeof thisFocus.attr('id') === 'undefined') {
                            thisFocus.attr('id', QuotingToolUtils.getRandomId());
                        }

                        // Add agentid when browse or upload file.
                        AppToolbar.base_editor.settings.imageBrowser_listUrl = $rootScope.app.config.base
                            + GlobalConfig.IMG_URI_FETCH + '&agentid=' + $rootScope.app.model.agentid;
                        AppToolbar.base_editor.settings.filebrowserImageUploadUrl = $rootScope.app.config.base
                            + GlobalConfig.IMG_URI_UPLOAD + '&agentid=' + $rootScope.app.model.agentid;
                        // Merge settings
                        var settings = $.extend({}, AppToolbar.base_editor.settings, component.settings);

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

                            AppHelper.customKeyPress(editor);
                            // Custom focus
                            AppHelper.customFocus(editor);
                            // Context menu
                            $rootScope.customCKEditorContextMenu(editor);

                            // Text change:
                            editor.on('blur', function () {
                                // Refresh heading indexing
                                if (component == $rootScope.app.data.blocks.heading) {
                                    $rootScope.refreshHeadings();
                                }
                            });
                        });
                    });

                    if (elementType == AppConstants.COMPONENT_TYPE.BLOCK) {
                        $rootScope.registerDropToContainer(html);
                        // Sortable
                        contentMain.sortable({
                            handle: 'i.icon-move, i.icon-align-justify, .doc-block__control--drag',
                            axis: 'y',
                            stop: function (event, ui) {
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
                                        var args = {
                                            id: $rootScope.app.data.blocks[k].layout.id,
                                            // type: 'after',
                                            // focus: prev
                                        };
                                        // $rootScope.$broadcast('$evtAddBlock', args);

                                        if (prev.length > 0) {
                                            args['type'] = 'after';
                                            args['focus'] = prev;
                                        }
                                        else {
                                            args['type'] = 'prepend';
                                        }

                                        // Add block
                                        $rootScope.$broadcast('$evtAddBlock', args);

                                        break;
                                    }
                                }
                            }
                        });
                    } else if (elementType == AppConstants.COMPONENT_TYPE.WIDGET) {
                        AppHelper.resizeable(html);
                        $rootScope.registerMoveOnContainer(html);
                    }

                    $rootScope.registerEventSupportOptions(html);
                });
            };

            /**
             * Drag content component
             *
             * How to check if click event is already bound - JQuery
             * @link http://stackoverflow.com/questions/6361465/how-to-check-if-click-event-is-already-bound-jquery\
             *
             * Fix draggable on overflow
             * @link http://stackoverflow.com/questions/811037/jquery-draggable-and-overflow-issue
             */
            $rootScope.registerEventDragAndDropBlocks = function () {
                // Drag block to container
                $rootScope.app.container.droppable({
                    drop: function (e, ui) {
                        var dragObject = $(ui.draggable);
                        var dataId = dragObject.data('id');
                        // Special blocks
                        if (dataId == $rootScope.app.data.blocks.cover_page.layout.id
                            || dataId == $rootScope.app.data.blocks.page_header.layout.id
                            || dataId == $rootScope.app.data.blocks.page_footer.layout.id) {
                            var args = {
                                id: dataId
                            };

                            $rootScope.$broadcast('$evtAddBlock', args);
                        }
                    }
                });
            };

            // var initTemplate = function () {
            //     // First block in App body
            //     var args = {id: $rootScope.app.data.blocks.heading.layout.id};
            //     $rootScope.$broadcast('$evtAddBlock', args);
            // };

            $rootScope.watchCurrentPosition = function () {
                // Get current mouse position (coordinate)
                $(document).on('mousemove', '.content-container.block-handle', function (event) {
                    $rootScope.currentPosition = event;
                });
            };

            $rootScope.cancelTemplate = function () {
                    var isDuplicate = jQuery('#isDuplicate').val();
                    if(isDuplicate){
                        //delete
                        var recordId = jQuery('input:hidden[name="record"]').val();
                        if(recordId){
                            var module = app.getModuleName();
                            var postData = {
                                "module": module,
                                "action": "DeleteAjax",
                                "record": recordId,
                                "parent": app.getParentModuleName()
                            }
                            AppConnector.request(postData).then(
                                function(data){
                                    if(data.success) {
                                        console.log(data);
                                        window.onbeforeunload = null;
                                        var dataUrl = "index.php?module=QuotingTool&view=List";
                                        window.location.href = dataUrl;
                                    }
                                },
                                function(error,err){
                                    console.log(error);
                                });
                        }
                    }else{
                        var dataUrl = "index.php?module=QuotingTool&view=List";
                        window.location.href = dataUrl;
                    }
            };

            $rootScope.saveTemplate = function ($event) {
                $event.preventDefault();

                // Validate form data
                if (!$rootScope.app.form.isValid2()) {
                    AppHelper.showMessage('Form invalid');
                    return false;
                }

                $rootScope.app.progressIndicatorElement.progressIndicator({
                    'message': $translate.instant('Processing...'),
                    'position': 'html',
                    'blockInfo': {
                        'enabled': true
                    },
                    'mode': 'show'
                });

                var htmlContainer = $rootScope.app.container.clone();

                /**
                 * Fix placeholder
                 * @link http://stackoverflow.com/questions/11324559/jquery-if-div-contains-this-text-replace-that-part-of-the-text
                 */
                htmlContainer.find('[placeholder]').each(function () {
                    var thisFocus = $(this);
                    if (thisFocus.hasClass('placeholder')) {
                        var placeholderText = thisFocus.attr('placeholder');
                        var elem = thisFocus.find(':contains("' + placeholderText + '")');
                        elem.text(elem.text().replace(placeholderText, ''));
                    }
                });

                // Destroy with clone DOM element
                htmlContainer.find('.ui-resizable-handle').remove();
                htmlContainer.find('*')
                    .removeAttributes([])
                    .removeClasses(['focus-contenteditable']);

                var htmlBody = htmlContainer.html();
                var htmlMain = htmlContainer.find('.quoting_tool-content:not([data-page-name="cover_page"])');
                var htmlHeader = '';
                var htmlFooter = '';

                // Header & Footer
                if (htmlMain.length > 0) {
                    var tmpHtmlHeader = $($(htmlMain[0]).find('.quoting_tool-content-header')[0]).find('.content-editable');
                    var tmpHtmlFooter = $($(htmlMain[0]).find('.quoting_tool-content-footer')[0]).find('.content-editable');

                    if (tmpHtmlHeader.length > 0) {
                        htmlHeader = $(tmpHtmlHeader[0]).html();
                    }
                    if (tmpHtmlFooter.length > 0) {
                        htmlFooter = $(tmpHtmlFooter[0]).html();
                    }
                }

                var htmlContent = '';
                // Clone the content to new object
                var myContent = htmlContainer.find('.quoting_tool-content-main, .quoting_tool-content-page-break');
                myContent.find('*')
                    .removeAttributes([/*'placeholder',*/ 'contenteditable', 'tabindex', 'role', 'aria-label', 'aria-describedby',
                        'spellcheck', 'data-cke-saved-src', 'input-change'])
                    .removeClasses(['ui-droppable', 'cke_editable_inline', 'cke_contents_ltr', 'quoting_tool-cke-keep-element',
                        'cke_editable', /*'cke_show_border', 'cke_show_borders', */'doc-block--pagebreak', 'doc-block--editable',
                        'quoting_tool-first-focus', 'quoting_tool-disable-margin', 'cke_focus', 'focus-contenteditable']);

                if (myContent.length > 0) {
                    for (var i = 0; i < myContent.length; i++) {
                        var c = myContent[i];
                        htmlContent += AppHelper.getContentFromHtml(c);
                    }
                }

                // IFrame cke-realelement
                var tmpHtmlContent = $('<div/>');
                tmpHtmlContent.html(htmlContent);
                // htmlContent = tmpHtmlContent.html();
                var CKEIframes = tmpHtmlContent.find('.cke_iframe');
                var CKEIframe = null;
                var frame = null;

                for (var ckf = 0; ckf < CKEIframes.length; ckf++) {
                    CKEIframe = $(CKEIframes[ckf]);
                    frame = $(decodeURIComponent(CKEIframe.data('cke-realelement')));
                    CKEIframe.after(frame);
                    CKEIframe.attr('src', QuotingToolUtils.getYoutubeThumbnailFromIframe(frame));
                    CKEIframe.wrap('<a href="' + frame.attr('src') + '" class="wrap_video_link"></a>');
                }

                // Check multi line
                htmlContent = tmpHtmlContent.html();

                // Header
                $rootScope.app.model.header = QuotingToolUtils.base64Encode(htmlHeader);
                // Footer
                $rootScope.app.model.footer = QuotingToolUtils.base64Encode(htmlFooter);
                // Body
                $rootScope.app.model.body = QuotingToolUtils.base64Encode(htmlBody);
                // Content
                $rootScope.app.model.content = QuotingToolUtils.base64Encode(htmlContent);

                if (!$rootScope.app.model.id) {
                    // Init template by primary info on db
                    Template.save({
                        record: 0,
                        filename: $rootScope.app.model.filename,
                        agentid: $rootScope.app.model.agentid,
                        primary_module: $rootScope.app.model.module
                    }, function (response) {
                        if (response.success == true) {
                            var data = response.result;
                            $rootScope.app.model.id = data['id'];
                            // var newUrl = $rootScope.app.config.base
                            //     + 'index.php?module=QuotingTool&view=Edit&record=' + $rootScope.app.model.id + '#/content';
                            //
                            // if (history.pushState) {
                            //     // Replace URL in address bar
                            //     $window.history.pushState(null, PageTitle.get(), newUrl);
                            // }
                            // $location.path(newUrl);

                            // Update custom info to db
                            Template.save({
                                record: $rootScope.app.model.id,
                                description: $rootScope.app.model.description,
                                anwidget: $rootScope.app.model.anwidget,
                                email_subject: QuotingToolUtils.base64Encode($rootScope.app.model.email_subject),
                                email_content: QuotingToolUtils.base64Encode($rootScope.app.model.email_content),
                                mapping_fields: $rootScope.app.model.mapping_fields,
                                attachments: $rootScope.app.model.attachments
                            }, function (response) {
                                if (response.success == true) {
                                } else {
                                    AppHelper.showMessage(response.error.message)
                                }
                            });

                            // Update content to db
                            Template.save({
                                record: $rootScope.app.model.id,
                                body: $rootScope.app.model.body,
                                content: $rootScope.app.model.content,
                                header: $rootScope.app.model.header,
                                footer: $rootScope.app.model.footer,
                                history: true
                            }, function (response) {
                                if (response.success == true) {
                                } else {
                                    AppHelper.showMessage(response.error.message)
                                }
                            });

                            // Update settings to db
                            TemplateSetting.save({
                                record: $rootScope.app.model.id,
                                description: $rootScope.app.model.settings.description,
                                label_decline: $rootScope.app.model.settings.label_decline,
                                label_accept: $rootScope.app.model.settings.label_accept,
                                background: $rootScope.app.model.settings.background,
                                page_format: $rootScope.app.model.settings.page_format
                            }, function (response) {
                                if (response.success == true) {
                                } else {
                                    AppHelper.showMessage(response.error.message)
                                }
                            });

                            $scope.$apply(function () {
                                var newHistory = data['history'];

                                if (newHistory) {
                                    $rootScope.app.model.histories.push(newHistory);
                                    var args = {
                                        'history': newHistory
                                    };
                                    $rootScope.$broadcast('$evtSetSelectedHistory', args);
                                }
                            });

                            // Hide indicator
                            $rootScope.app.progressIndicatorElement.progressIndicator({'mode': 'hide'});
                            AppHelper.showMessage(data.message, AppHelper.MESSAGE_TYPE.SUCCESS);
                        } else {
                            AppHelper.showMessage(response.error.message)
                        }
                    });
                } else {
                    // Update content to db
                    Template.save({
                        record: $rootScope.app.model.id,
                        body: $rootScope.app.model.body,
                        content: $rootScope.app.model.content,
                        header: $rootScope.app.model.header,
                        footer: $rootScope.app.model.footer,
                        history: true
                    }, function (response) {
                        if (response.success == true) {
                            var data = response.result;

                            $scope.$apply(function () {
                                var newHistory = data['history'];

                                if (newHistory) {
                                    $rootScope.app.model.histories.push(newHistory);
                                    var args = {
                                        'history': newHistory
                                    };
                                    $rootScope.$broadcast('$evtSetSelectedHistory', args);
                                }
                            });

                            // Hide indicator
                            $rootScope.app.progressIndicatorElement.progressIndicator({'mode': 'hide'});
                            AppHelper.showMessage(data.message, AppHelper.MESSAGE_TYPE.SUCCESS);
                        } else {
                            AppHelper.showMessage(response.error.message)
                        }
                    });
                }

                return false;
            };

            /**
             * @param {CKEDITOR} editor
             * @param {Array=} itemsToAdd
             * @param {Array=} itemsToRemove
             */
            $rootScope.customCKEditorContextMenu = function (editor, itemsToAdd, itemsToRemove) {
                // editor.editor.removeMenuItem('cut');
                // editor.editor.removeMenuItem('copy');
                // editor.editor.removeMenuItem('paste');

                if ($rootScope.app.data.blocks.pricing_table || $rootScope.app.data.blocks.table) {
                    editor.editor.removeMenuItem('editdiv');
                    editor.editor.removeMenuItem('removediv');
                    editor.editor.removeMenuItem('tabledelete');
                }

                if (itemsToAdd && $.isArray(itemsToAdd)) {
                    var item = null;

                    for (var i = 0; i < itemsToAdd.length; i++) {
                        item = itemsToAdd[i];
                        editor.editor.addMenuItem(item.name, item.options);
                    }
                }

                if (itemsToRemove && $.isArray(itemsToRemove)) {
                    var item = null;

                    for (var i = 0; i < itemsToRemove.length; i++) {
                        item = itemsToRemove[i];
                        editor.editor.removeMenuItem(item);
                    }
                }
            };

            var insertCustomInputToImageUploader = function () {
                /**
                 * Custom default dialog values
                 * @link http://docs.cksource.com/CKEditor_3.x/Howto/Default_Field_Values
                 */
                CKEDITOR.on('dialogDefinition', function (e) {
                    // Take the dialog name and its definition from the event data.
                    // var dialogName = e.data.name;
                    // var dialog = e.data.definition.dialog;
                    // Change browse file url
                    // TODO: remove it
                    // e.editor.config.imageBrowser_listUrl = $rootScope.app.config.base
                    //     + GlobalConfig.IMG_URI_FETCH + '&agentid=' + $rootScope.app.model.agentid;
                    // CKEDITOR.config.imageBrowser_listUrl = $rootScope.app.config.base
                    //     + GlobalConfig.IMG_URI_FETCH + '&agentid=' + $rootScope.app.model.agentid;
                    
                    (function (e) {
                        // var dialogName = e.data.name;
                        var dialog = e.data.definition.dialog;
                        // var editor = e.editor;

                        dialog.on('show', function (e1, e2) {
                            // console.log('dialog ' + dialogName + ' opened. The width is ' + this.getSize().width + 'px.');
                            // // Change browse file url
                            // e.editor.config.imageBrowser_listUrl = $rootScope.app.config.base
                            //     + GlobalConfig.IMG_URI_FETCH + '&agentid=' + $rootScope.app.model.agentid;
                            // console.log('editor.config =', e.editor.config);

                            /**
                             * Insert content into iFrame
                             * @link http://stackoverflow.com/questions/21795761/insert-content-into-iframe#answer-21795832
                             */
                            var dialogTable = $('table.cke_dialog');
                            var input_file_iframe = dialogTable.find('iframe.cke_dialog_ui_input_file');
                            input_file_iframe.ready(function() {
                                var form = input_file_iframe.contents().find("form");
                                if (form.length > 0) {
                                    form.append('<input type="hidden" name="agentid" value="' + $rootScope.app.model.agentid + '"/>');
                                    // csrfMagicName & csrfMagicToken from vtiger
                                    form.append('<input type="hidden" name="' + csrfMagicName + '" value="' + csrfMagicToken + '"/>');
                                }
                            });
                            //fix bug invalid request when upload image on firefox
                                input_file_iframe.load(function() {
                                    var form = input_file_iframe.contents().find("form");
                                    if (form.length > 0) {
                                        form.append('<input type="hidden" name="agentid" value="' + $rootScope.app.model.agentid + '"/>');
                                        // csrfMagicName & csrfMagicToken from vtiger
                                        form.append('<input type="hidden" name="' + csrfMagicName + '" value="' + csrfMagicToken + '"/>');
                                    }
                                });
                        });

                        // dialog.on('hide', function (e1) {
                        //     console.log('dialog ' + dialogName + ' closed.');
                        // });

                    })(e);
                });
            };

            /**
             * @param page_format
             */
            $scope.changePageFormat = function (page_format) {
                if (!page_format) {
                    page_format = AppConstants.PAGE_FORMAT.PORTRAIT;  // Default page format
                }
                $rootScope.app.model.settings.page_format = page_format;
                // Setup page layout dimension
                var padding = 120;  // px

                AppHelper.customCssTag(' .quoting_tool-content {width: ' + (page_format.dimension.width - padding) + 'px;}' +
                    ' .document__block-list {min-height: ' + (page_format.dimension.height - padding) + 'px;}');

                // Hide function
                $rootScope.app.container_layout.hide();
            };

            /**
             * @link http://stackoverflow.com/questions/16150289/running-angularjs-initialization-code-when-view-is-loaded
             */
            var init = function () {
                // Template id
                $rootScope.app.model.id = $rootScope.app.form_item.record.val();
                // Primary module
                $rootScope.app.model.module = $rootScope.app.form_item.primary_module.val();
                // Settings
                var valSettings = $rootScope.app.form_item.settings.val();
                var objSettings = (valSettings && valSettings !== '[]') ? JSON.parse(valSettings) : {};
                if (!$.isEmptyObject(objSettings)) {
                    $rootScope.app.model.settings = objSettings;
                }
                // Body
                $rootScope.app.model.body = $rootScope.app.form_item.body.val();
                // File name
                $rootScope.app.model.filename = $rootScope.app.form_item.filename.val();
                // Owner
                $rootScope.app.model.agentid = $rootScope.app.form_item.agentid.val();
                if (!$rootScope.app.model.agentid) {
                    $rootScope.app.model.agentid = $rootScope.app.config.default_agentid;
                }

                // Page title
                PageTitle.set($rootScope.app.model.filename);
                // Description
                $rootScope.app.model.description = $rootScope.app.form_item.description.val();
                // Mapping fields
                var valMappingFields = $rootScope.app.form_item.mapping_fields.val();
                var objMappingFields = (valMappingFields && valMappingFields !== '[]') ? JSON.parse(valMappingFields) : {};
                if (!$.isEmptyObject(objMappingFields)) {
                    $rootScope.app.model.mapping_fields = objMappingFields;
                }
                // Attachments
                var valAttachments = $rootScope.app.form_item.attachments.val();
                var arrAttachments = (valAttachments && valAttachments !== '{}') ? JSON.parse(valAttachments) : [];
                if ($.isArray(arrAttachments)) {
                    $rootScope.app.model.attachments = arrAttachments;
                }
                // Email
                var valEmailSubject = $rootScope.app.form_item.email_subject.val();
                var valEmailContent = $rootScope.app.form_item.email_content.val();
                $rootScope.app.model.email_subject = (valEmailSubject) ? QuotingToolUtils.base64Decode(valEmailSubject) : '';
                $rootScope.app.model.email_content = (valEmailContent) ? QuotingToolUtils.base64Decode(valEmailContent) : '';

                // Config
                $('[ng-app="app"]').attr({
                    'base': $rootScope.app.config.base
                });
                // Init default & selected
                if ($rootScope.app.data.modules && $rootScope.app.data.modules.length > 0) {
                    if (!$rootScope.app.model.module) {
                        // Init selected module if not set
                        var defaultModule = $rootScope.app.data.modules[0];
                        $rootScope.app.form_item.primary_module.val(defaultModule.name);
                        $rootScope.app.model.module = defaultModule.name;
                        $rootScope.app.data.selectedModule = defaultModule;
                    } else {
                        var flag = false;
                        var module = null;

                        for (var m = 0; m < $rootScope.app.data.modules.length; m++) {
                            module = $rootScope.app.data.modules[m];

                            if (module.name == $rootScope.app.model.module) {
                                // Init selected module
                                $rootScope.app.data.selectedModule = module;
                                flag = true;
                                break;
                            }
                        }

                        if (!flag) {
                            // Show error if the selected module isn't match in enable module list
                            return AppHelper.showMessage('Invalid selected module');
                        }
                    }
                }

                if ($rootScope.app.data.selectedModule) {
                    // Set default selected
                    $rootScope.app.data.selectedModuleField = QuotingToolUtils.defaultSelected($rootScope.app.data.selectedModule.fields);
                    $rootScope.app.data.selectedRelatedModule = QuotingToolUtils.defaultSelected($rootScope.app.data.selectedModule.related_modules);
                    if ($rootScope.app.data.selectedRelatedModule) {
                        $rootScope.app.data.selectedRelatedModuleField = QuotingToolUtils.defaultSelected($rootScope.app.data.selectedRelatedModule.fields);
                    }
                }

                // Product block modules
                var inventoryModules = $.merge(GlobalConfig.INVENTORY_MODULES, GlobalConfig.PRODUCT_MODULES);
                var prepareModule = null;

                for (var i = 0; i < $rootScope.app.data.modules.length; i++) {
                    prepareModule = angular.copy($rootScope.app.data.modules[i]);

                    // indexing guest blocks
                    if (prepareModule.guest_blocks) {
                        prepareModule.idx_guest_blocks = {};

                        for (var gb = 0; gb < prepareModule.guest_blocks.length; gb++) {
                            prepareModule.idx_guest_blocks[prepareModule.guest_blocks[gb].name] = prepareModule.guest_blocks[gb];
                        }
                    }
                    $rootScope.app.data.idxModules[prepareModule.name] = prepareModule;

                    if (($.inArray(prepareModule.name, inventoryModules) < 0) || $rootScope.app.data.idxProductBlockModules[prepareModule.name]) {
                        // continue if the module isn't inventory module or exist on product block module list
                        continue;
                    }

                    // Push new product block module
                    $rootScope.app.data.idxProductBlockModules[prepareModule.name] = prepareModule;
                    // Get relation inventory modules

                    idxProductBlockModules = $rootScope.app.data.idxProductBlockModules;
                }

                $rootScope.app.data.selectedProductBlockModule = QuotingToolUtils.defaultSelected($rootScope.app.data.idxProductBlockModules);
                if ($rootScope.app.data.selectedProductBlockModule) {
                    $rootScope.app.data.selectedProductBlockModuleField = QuotingToolUtils.defaultSelected($rootScope.app.data.selectedProductBlockModule.fields);
                }

                // Inventory module fields:
                var inventoryModuleNames = GlobalConfig.PRODUCT_MODULES;
                var inventoryModuleName = null;
                var inventoryModule = null;
                var inventoryFields = null;
                var inventoryField = null;
                for (var i = 0; i < inventoryModuleNames.length; i++) {
                    inventoryModuleName = inventoryModuleNames[i];

                    if (!$rootScope.app.data.idxProductBlockModules[inventoryModuleName]) {
                        continue;
                    }

                    inventoryModule = angular.copy($rootScope.app.data.idxProductBlockModules[inventoryModuleName]);
                    inventoryFields = inventoryModule.fields;

                    for (var f = 0; f < inventoryFields.length; f++) {
                        inventoryField = inventoryFields[f];
                        inventoryField.block.label = inventoryModule.name + ' - ' + inventoryField.block.label;
                        inventoryField.module = inventoryModuleName;
                        productModuleFields.push(inventoryField);
                    }
                }

                // Change picklist values
                if ($rootScope.app.data.idxModules[$rootScope.app.model.module]) {
                    $rootScope.app.data.picklistField.options = $rootScope.app.data.idxModules[$rootScope.app.model.module]['picklist'];
                }

                // Initial
                if ($rootScope.app.model.id) {
                    // Init selected page format (saved)
                    $scope.changePageFormat($rootScope.app.model.settings.page_format);
                    $rootScope.loadTemplate($rootScope.app.model.id);
                } else {
                    // initTemplate()
                    $rootScope.app.container_layout.show();
                }

                $rootScope.registerEventDragAndDropBlocks();
                $rootScope.watchCurrentPosition();
                // Last focus page:
                $rootScope.registerEventFocusPage();
                // mouse enter event
                $rootScope.registerEventCoverPageSupportOptions();
                // Last focus item
                $rootScope.registerEventFocusInput();

                insertCustomInputToImageUploader();

                // Delay to hide progress indicator
                $timeout(function () {
                    // vtiger indicator
                    $rootScope.app.progressIndicatorElement.progressIndicator({'mode': 'hide'});
                }, 8000);
            };
            init();
        });

})(jQuery);
