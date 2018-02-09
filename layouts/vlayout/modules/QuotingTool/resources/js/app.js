(function ($) {
    'use strict';

    var app = angular.module('app', ['AppConfig', 'AppConstants', 'AppUtils', 'AppControllers', 'AppModels', 'AppDirectives',
        'AppI18N', 'ui.bootstrap', 'ngSanitize', 'ngCkeditor']);

    /**
     * trustAsResourceUrl filter
     */
    app.filter('trustAsResourceUrl', ['$sce', function ($sce) {
        return function (val) {
            return $sce.trustAsResourceUrl(val);
        };
    }]);

    /**
     * Config - config
     */
    app.config(function ($httpProvider, $locationProvider, $stateProvider, $urlRouterProvider, $translateProvider, GlobalConfig) {
        ////Enable cross domain calls
        //$httpProvider.defaults.useXDomain = true;
        //
        ////Remove the header used to identify ajax call  that would prevent CORS from working
        //delete $httpProvider.defaults.headers.common['X-Requested-With'];

        // Allow request api
        $httpProvider.interceptors.push('httpRequestInterceptor');
        $stateProvider.state('base', {
            url: GlobalConfig.BASE,
            abstract: true,
            views: {
                'right_panel_tool_items@': {
                    templateUrl: '',
                    controller: 'CtrlAppRightPanelContent'
                }
            }
        });

        // Go to content tab
        $urlRouterProvider.otherwise('content');

        // Default language:
        $translateProvider.preferredLanguage('en');
    });

    /**
     * Run - run
     */
    app.run(function ($rootScope, $state, $stateParams, $translate, AppConstants, GlobalConfig, AppToolbar, SECTIONS, AppConnection) {
        // App
        $rootScope.app = {};

        // Indicator
        $rootScope.app.progressIndicatorElement = $.progressIndicator({
            'message': $translate.instant('Loading...'),
            'position': 'html',
            'blockInfo': {
                'enabled': true
            }
        });

        // User
        $rootScope.app.user = {};

        // Sections
        $rootScope.SECTIONS = SECTIONS;

        // Form
        $rootScope.app.form = $('#EditView');
        $rootScope.app.form_item = {
            record: $('[name="record"]'),
            module: $('[name="module"]'),
            filename: $('[name="filename"]'),
            agentid: $('[name="agentid"]'),
            primary_module: $('[name="primary_module"]'),
            settings: $('[name="settings"]'),
            attachments: $('[name="attachments"]'),
            body: $('[name="body"]'),
            content: $('[name="content"]'),
            header: $('[name="header"]'),
            footer: $('[name="footer"]'),
            description: $('[name="description"]'),
            anwidget: $('[name="anwidget"]'),
            mapping_fields: $('[name="mapping_fields"]'),
            email_subject: $('[name="email_subject"]'),
            email_content: $('[name="email_content"]')
        };

        // Container
        $rootScope.app.container = $('#quoting_tool-center');
        // TODO: need update value when load controller successful
        $rootScope.app.container_overlay = $('#quoting_tool-overlay-content');
        $rootScope.app.container_layout = $('#quoting_tool-layout-content');

        $rootScope.app.last_zindex = 0;
        $rootScope.app.last_focus_page = null;
        $rootScope.app.last_focus_item = null;
        $rootScope.app.last_focus_item_setting = null;
        $rootScope.app.is_debug = GlobalConfig.DEBUG_MODE;
        $rootScope.app.is_debug_show_result = false;

        // Config
        $rootScope.app.config = {
            date_format: "mm-dd-yyyy",
            hour_format: "12",
            base: ''
        };

        // Data
        $rootScope.app.data = {
            agents: [],
            modules: [],
            picklistField: {
                options: []
            },
            idxModules: {},
            blocks: AppToolbar.blocks,
            widgets: AppToolbar.widgets,
            idxProductBlockModules: {},
            selectedProductBlockModule: {},
            selectedProductBlockModuleField: {}
        };

        // Model
        $rootScope.app.model = {
            id: '',
            module: 'Quotes',
            filename: '',
            agentid: '',
            body: '',
            content: '',
            header: '',
            footer: '',
            description: '',
            anwidget: '',
            attachments: [],
            mapping_fields: {},
            email_subject: '',
            email_content: '',
            settings: {
                description: '',
                label_accept: 'Accept',
                label_decline: 'Decline',
                background: {
                    image: '',
                    size: 'auto'
                },
                page_format: AppConstants.PAGE_FORMAT.PORTRAIT  // Default page format
            },
            histories: [],
            selectedHistory: null
        };

        // Config
        var js_config = jQuery('#js_config').text();
        if(js_config != '') {
            $rootScope.app.config = JSON.parse(js_config);
        }

        // Modules
        var js_modules = jQuery('#js_modules').text();
        if(js_modules != '') {
            $rootScope.app.data.modules = JSON.parse(js_modules);
        }

        // Owners
        var js_agents = jQuery('#js_agents').text();
        if(js_agents != '') {
            $rootScope.app.data.agents = JSON.parse(js_agents);
        }

        // Custom functions
        var js_custom_functions = jQuery('#js_custom_functions').text();
        if(js_custom_functions != '') {
            $rootScope.app.data.customFunctions = JSON.parse(js_custom_functions);
        }

        // Custom fields
        var js_custom_fields = jQuery('#js_custom_fields').text();
        if(js_custom_fields != '') {
            $rootScope.app.data.customFields = JSON.parse(js_custom_fields);
        }

        // Current user
        var js_currentUser = jQuery('#js_currentUser').text();
        if(js_currentUser != '') {
            $rootScope.app.user.profile = JSON.parse(js_currentUser);
        }

        $rootScope.currentPosition = null;
        $rootScope.dragOffset = null;

        // ngRoute
        $rootScope.$state = $state;
        $rootScope.$stateParams = $stateParams;

        // Document ready
        angular.element(document).ready(function () {
            // Watch object change
            $rootScope.$watchCollection("app.model.settings.background", function (newValue, oldValue) {
                // Background
                if ($rootScope.app.model.settings.background) {
                    var backgroundImage = $rootScope.app.model.settings.background.image ? 'url("' + $rootScope.app.model.settings.background.image + '")' : '';
                    var backgroundSize = $rootScope.app.model.settings.background.size ? $rootScope.app.model.settings.background.size : '';

                    $rootScope.app.container.css({
                        backgroundImage: backgroundImage,
                        backgroundSize: backgroundSize
                    });
                }
            });

            // Check timeout each 5 minutes
            setInterval(function () {
                AppConnection.validateRequest({});
            }, 300000);
        });
    });

    /**
     * Fac - httpRequestInterceptor
     * valid ACOS request
     */
    app.factory('httpRequestInterceptor', function ($rootScope, $translate, GlobalConfig) {
        return {
            request: function ($config) {
                $config.headers = $config.headers || {};

                // Header: Authorization - Override Authorization header config
                if ($config.headers['Authorization'] == undefined || $config.headers['Authorization'] === null) {
                    var auth = 'admin';

                    $config.headers['Authorization'] = 'Basic ' + auth;
                }

                // Headers: Appname
                $config.headers['Appname'] = $translate.instant(GlobalConfig.APP_NAME);

                return $config;
            }
        };
    });

    /**
     * Fac - PageTitle
     */
    app.factory('PageTitle', function ($rootScope, $window, $translate, GlobalConfig) {
        $rootScope.pageTitle = '';
        $rootScope.appTitle = $translate.instant(GlobalConfig.APP_NAME);

        return {
            get: function () {
                return $window.document.title;
            },
            /**
             * @param title
             */
            set: function (title) {
                $window.document.title = $rootScope.appTitle + ' | ' + title;
                $rootScope.pageTitle = title;
            },
            reset: function () {
                $window.document.title = $rootScope.appTitle;
                $rootScope.pageTitle = $rootScope.appTitle;
            }
        }
    });

    /**
     * @Link http://stackoverflow.com/questions/20715273/unshifting-to-ng-repeat-array-not-working-while-using-orderby
     */
    app.filter('reverse', function() {
        return function(items) {
            return items.slice().reverse();
        };
    });

})(jQuery);