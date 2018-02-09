(function () {
    'use strict';

    var utils = angular.module('AppUtils', []);

    utils.factory('AppUtils', function ($rootScope, $sce, $templateRequest, $compile, $translate, GlobalConfig, AppConstants) {
        return {

            /**
             * Fn - pasteHtmlAtCaret
             *
             * @link http://stackoverflow.com/questions/6690752/insert-html-at-caret-in-a-contenteditable-div
             * @param {String} text
             * @param {Object=} info
             */
            pasteHtmlAtCaret: function (text, info) {
                if (!info) {
                    info = {};
                }

                var thisFocus = $rootScope.app.last_focus_item;

                if (!thisFocus) {
                    // Not found focus object
                    AppHelper.showMessage($translate.instant('Place cursor to insert'));
                    return;
                }

                var type = thisFocus['type'];
                var focus = thisFocus['focus'];

                switch (type) {
                    case AppConstants.FOCUS_TYPE.INPUT:
                    case AppConstants.FOCUS_TYPE.TEXTAREA:
                    case AppConstants.FOCUS_TYPE.CONTENTEDITABLE:
                        var dataInfo = focus.data('info');
                        if (!dataInfo) {
                            dataInfo = {};
                        }

                        $.extend(dataInfo, info);
                        focus.attr('data-info', JSON.stringify(dataInfo));
                        focus.attr('title', info['label']);

                        var focusId = focus.attr('id');
                        if (focusId && CKEDITOR.instances[focusId]) {
                            // If CKEditor instance exist
                            focus = CKEDITOR.instances[focusId];
                            focus.insertHtml(text);
                        } else {
                            focus.insertAtCaret(text);
                        }

                        break;
                    case AppConstants.FOCUS_TYPE.CKEDITOR:
                        focus.insertHtml(text);
                        break;
                    default:
                        break;
                }
            },

            /**
             * Fn - loadTemplate
             *
             * @param $scope
             * @param {String} path
             * @param {boolean} compile
             * @param {Function=} callback - callback(jQuery)
             */
            loadTemplate: function ($scope, path, compile, callback) {
                /**
                 * Make sure that no bad URLs are fetched. You can omit this if your template URL is not dynamic.
                 * @link http://stackoverflow.com/questions/24496201/load-html-template-from-file-into-a-variable-in-angularjs
                 */
                var templateUrl = $sce.getTrustedResourceUrl(path);

                // template is the HTML template as a string
                // Let's put it into an HTML element and parse any directives and expressions
                // in the code. (Note: This is just an example, modifying the DOM from within
                // a controller is considered bad style.)
                // Append template to DOM
                $templateRequest(templateUrl).then(function (template) {
                    var html = $(template);

                    // Compile Angular
                    if (compile) {
                        /**
                         * Bind event for template
                         * @link http://stackoverflow.com/questions/18618069/angularjs-event-binding-in-directive-template-doesnt-work-if-mouseout-used-but
                         */
                        $compile(html.contents())($scope);
                    }

                    // Callback
                    /** @link http://stackoverflow.com/questions/5999998/how-can-i-check-if-a-javascript-variable-is-function-type */
                    if (typeof callback === 'function') {
                        callback(html);
                    }
                }, function () {
                    // An error has occurred
                    alert($translate.instant('Load template error'));
                });
            },

            /**
             * @param type
             * @param options
             * @param callback
             */
            openImagebrowser: function (type, options, callback) {
                if (!type) {
                    // Default type
                    type = 'images';
                }

                if (!options) {
                    options = {}
                }
                var defaultOptions = {
                    status: 0,
                    toolbar: 0,
                    location: 0,
                    menubasr: 0,
                    directories: 0,
                    resizable: 1,
                    scrollbars: 1,
                    width: 800,
                    height: 600
                };
                $.extend(defaultOptions, options);

                /**
                 * Return a value from window.open
                 * @link http://stackoverflow.com/questions/25775862/return-a-value-from-window-open#answer-25776316
                 */
                window.CkEditorImageBrowser = {
                    callBack: function (url) {
                        // Dispose CkEditorImageBrowser
                        window.CkEditorImageBrowser = null;

                        if (typeof callback == 'function') {
                            callback(url);
                        }
                    }
                };

                var url = $rootScope.app.config.base + GlobalConfig.IMG_URI_FETCH + '&agentid=' + $rootScope.app.model.agentid;
                var windowUrl = $rootScope.app.config.base + "layouts/vlayout/modules/QuotingTool/resources/js/libs/ckeditor_4.5.6_full/plugins/imagebrowser/browser/browser.html?listUrl="
                    + encodeURIComponent(url) + '&is_custom_browser=true';
                window.open(windowUrl + '&type=' + type, 'kcfinder_textbox', 'status=' + defaultOptions['status'] + ', toolbar=' + defaultOptions['toolbar']
                    + ', location=' + defaultOptions['location'] + ', menubasr=' + defaultOptions['menubasr'] + ', directories=' + defaultOptions['directories']
                    + ', resizable=' + defaultOptions['resizable'] + ', scrollbars=' + defaultOptions['scrollbars'] + ', width=' + defaultOptions['width']
                    + ', height=' + defaultOptions['height']
                );
            }

        };
    });

})();