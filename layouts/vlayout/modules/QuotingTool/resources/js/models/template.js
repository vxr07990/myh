(function () {
    'use strict';

    var models = angular.module('AppModels');

    models.factory('Template', function ($rootScope) {
        var baseUri = 'index.php?';

        return {
            /**
             *
             * @param uri
             * @param params
             * @returns {*}
             */
            get: function (uri, params) {
                var q = $.Deferred();
                uri = baseUri + uri;

                $.get(uri, params).done(function (result) {
                    q.resolve(result);
                }).fail(function (response) {
                    q.reject(response);
                });

                return q.promise();
            },

            /**
             *
             * @param uri
             * @param params
             * @returns {*}
             */
            post: function (uri, params) {
                uri = baseUri + uri;

                var q = $.Deferred();
                $.post(uri, params).done(function (result) {
                    q.resolve(result);
                }).fail(function (response) {
                    q.reject(response);
                });

                return q.promise();
            },

            /**
             *
             * @param {Object} params
             * @param {Function=} callback
             */
            save: function (params, callback) {
                var defaultParams = {
                    module: 'QuotingTool',
                    action: 'ActionAjax',
                    mode: 'save'
                };
                $.extend(params, defaultParams);

                AppConnector.request(params).then(
                    function (response) {
                        // expired or invalid request
                        if (!response.success && (response.error.message && response.error.message == 'Illegal request')) {
                            alert('Illegal request.');
                            location.reload();
                            return;
                        }

                        if (typeof callback == 'function') {
                            callback(response);
                        }
                    },
                    function (error) {
                        console.log(error);
                        alert('Session time out, the page will now refresh.');
                        if ($rootScope.app.model.id) {
                            location.href = 'index.php?module=QuotingTool&view=Edit&record=' + $rootScope.app.model.id;
                        } else {
                            location.reload();
                        }
                    }
                );
            }
        };
    });

    models.factory('TemplateSetting', function ($rootScope) {
        return {
            /**
             *
             * @param {Object} params
             * @param {Function=} callback
             */
            save: function (params, callback) {
                var defaultParams = {
                    module: 'QuotingTool',
                    action: 'ActionAjax',
                    mode: 'save_setting'
                };
                $.extend(params, defaultParams);

                AppConnector.request(params).then(
                    function (response) {
                        // expired or invalid request
                        if (!response.success && (response.error.message && response.error.message == 'Illegal request')) {
                            alert('Illegal request.');
                            location.reload();
                            return;
                        }

                        if (typeof callback == 'function') {
                            callback(response);
                        }
                    },
                    function (error) {
                        console.log(error);
                        alert('Session time out, the page will now refresh.');
                        if ($rootScope.app.model.id) {
                            location.href = 'index.php?module=QuotingTool&view=Edit&record=' + $rootScope.app.model.id;
                        } else {
                            location.reload();
                        }
                    }
                );
            }
        };
    });

    models.factory('TemplateHistory', function ($rootScope) {
        return {
            /**
             *
             * @param {Object} params
             * @param {Function=} callback
             */
            getHistories: function (params, callback) {
                var defaultParams = {
                    module: 'QuotingTool',
                    action: 'ActionAjax',
                    mode: 'getHistories'
                };
                $.extend(params, defaultParams);

                AppConnector.request(params).then(
                    function (response) {
                        // expired or invalid request
                        if (!response.success && (response.error.message && response.error.message == 'Illegal request')) {
                            alert('Illegal request.');
                            location.reload();
                            return;
                        }

                        if (typeof callback == 'function') {
                            callback(response);
                        }
                    },
                    function (error) {
                        console.log(error);
                        alert('Session time out, the page will now refresh.');
                        if ($rootScope.app.model.id) {
                            location.href = 'index.php?module=QuotingTool&view=Edit&record=' + $rootScope.app.model.id;
                        } else {
                            location.reload();
                        }
                    }
                );
            },
            /**
             *
             * @param {Object} params
             * @param {Function=} callback
             */
            getHistory: function (params, callback) {
                var defaultParams = {
                    module: 'QuotingTool',
                    action: 'ActionAjax',
                    mode: 'getHistory'
                };
                $.extend(params, defaultParams);

                AppConnector.request(params).then(
                    function (response) {
                        // expired or invalid request
                        if (!response.success && (response.error.message && response.error.message == 'Illegal request')) {
                            alert('Illegal request.');
                            location.reload();
                            return;
                        }

                        if (typeof callback == 'function') {
                            callback(response);
                        }
                    },
                    function (error) {
                        console.log(error);
                        alert('Session time out, the page will now refresh.');
                        if ($rootScope.app.model.id) {
                            location.href = 'index.php?module=QuotingTool&view=Edit&record=' + $rootScope.app.model.id;
                        } else {
                            location.reload();
                        }
                    }
                );
            },
            /**
             *
             * @param {Object} params
             * @param {Function=} callback
             */
            removeHistories: function (params, callback) {
                var defaultParams = {
                    module: 'QuotingTool',
                    action: 'ActionAjax',
                    mode: 'removeHistories'
                };
                $.extend(params, defaultParams);

                AppConnector.request(params).then(
                    function (response) {
                        // expired or invalid request
                        if (!response.success && (response.error.message && response.error.message == 'Illegal request')) {
                            alert('Illegal request.');
                            location.reload();
                            return;
                        }

                        if (typeof callback == 'function') {
                            callback(response);
                        }
                    },
                    function (error) {
                        console.log(error);
                        alert('Session time out, the page will now refresh.');
                        if ($rootScope.app.model.id) {
                            location.href = 'index.php?module=QuotingTool&view=Edit&record=' + $rootScope.app.model.id;
                        } else {
                            location.reload();
                        }
                    }
                );
            }
        };
    });

})();
