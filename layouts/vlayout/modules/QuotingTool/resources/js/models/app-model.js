(function () {
    'use strict';

    var models = angular.module('AppModels', ['ngResource', 'AppConfig']);

    models.factory('AppConnection', function ($rootScope) {
        return {
            /**
             * @param {Object} params
             * @param {Function=} callback
             */
            validateRequest: function (params, callback) {
                var defaultParams = {
                    module: 'QuotingTool',
                    action: 'ActionAjax',
                    mode: 'validate_request'
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
