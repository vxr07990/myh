Vtiger_Detail_Js("WFLocations_Detail_Js",{

    getLocationTypeInfo : function () {
        var thisInstance = this;
        var LocationTypeElement = jQuery('#WFLocations_detailView_fieldValue_wflocation_type');
        var typeText = LocationTypeElement.find('span').text();
        if(!typeText){
            typeText = 'EMPTY';
        }
        var LocationTypeRecordURL = LocationTypeElement.find('span a').attr('href');
        if (LocationTypeRecordURL && LocationTypeRecordURL.length > 0){
            var split = LocationTypeRecordURL.split('record=');
            var typeRecordId = split[1];
            var params = {};
            params.module   = app.getModuleName();
            params.action   = 'ActionAjax';
            params.location = typeRecordId;
            params.mode     = 'getInfoItemLocationType';
            AppConnector.request(params).then(function (data) {
                if (data.success) {
                    var result = data.result;
                    LocationTypeElement.attr('data-base', result.base);
                    LocationTypeElement.attr('data-container', result.container);
                }
            });
        }
    },

    registerRules: function(isEditView)
    {
        var thisInstance = Vtiger_Edit_Js.getInstance();
        var rules = {
            create_multiple: {
                conditions: [
                    {
                        operator: 'exists',
                        targetFields: [
                            {
                                name: 'range_from',
                                hide: true,
                            },
                            {
                                name: 'range_to',
                                hide: true,
                            },
                            {
                                name: 'row_to',
                                hide: true,
                            },
                            {
                                name: 'bay_to',
                                hide: true,
                            },
                            {
                                name: 'level_to',
                                hide: true,
                            },
                            {
                                name: 'create_multiple',
                                hide: true,
                            }
                        ]
                    },
                ]
            },
            wflocation_type: {
                conditions: [
                    {
                        operator: 'set',
                        and: {
                            source: 'wflocation_type',
                            operator: 'in',
                            not: true,
                            value: ['Rack', 'Record Storage'],
                        },
                        targetFields: [
                            {
                                name: 'row',
                                hide: true,
                            },
                            {
                                name: 'bay',
                                hide: true,
                            },
                            {
                                name: 'level',
                                hide: true,
                            },
                        ]
                    },
                    {
                        operator: 'set',
                        and: {
                            source: 'wflocation_type',
                            operator: 'is',
                            not: true,
                            value: 'Rack',
                        },
                        targetFields: [
                            {
                                name: 'wfslot_configuration',
                                hide: true,
                            }
                        ]
                    },
                    {
                        operator: 'set',
                        and: {
                            source: 'wflocation_type',
                            operator: 'is',
                            not: true,
                            value: 'Floor',
                        },
                        targetFields: [
                            {
                                name: 'double_high',
                                hide: true,
                            },
                            {
                                name: 'vault_capacity',
                                hide: true,
                            }
                        ]
                    },
                ]
            },
            base_location_type: {
                conditions: [
                    {
                        operator: 'in',
                        not: true,
                        value: ['Rack', 'Record Storage'],

                        targetFields: [
                            {
                                name: 'base_slot',
                                hide: true,
                            },
                        ]
                    },
                ]
            },
            container_location: {
                conditions: [
                    {
                        operator: 'in',
                        value: ['false', '0'],
                        targetFields: [
                            {
                                name: 'container_capacity',
                                hide: true,
                            },
                            {
                                name: 'container_capacity_on',
                                hide: true,
                            },
                            {
                                name: 'double_high',
                                hide: true,
                            },
                        ]
                    },
                    {
                        operator: 'is',
                        value: '1',
                        and: {
                            source: 'base_location',
                            operator: 'is',
                            value: '0',
                        },
                        targetFields: [
                            {
                                name: 'double_high',
                                hide: true,
                            },
                        ]
                    },
                    {
                        operator: 'is',
                        value: '1',
                        and: {
                            source: 'base_location',
                            operator: 'is',
                            value: '1',
                        },
                        targetFields: [
                            {
                                name: 'container_capacity',
                                hide: true,
                            },
                            {
                                name: 'container_capacity_on',
                                hide: true,
                            },
                        ]
                    }
                ]
            },
        };
        thisInstance.applyVisibilityRules(rules, isEditView);
    },

    registerEvents : function() {
        var isEditView = jQuery('#isEditView').length > 0;
       this.registerRules(isEditView);
        this.getLocationTypeInfo();
    }
});

function triggerMoveLocationDetail() {
  var record = [jQuery('input[type="hidden"][id="recordId"]').val()];
  var params = {
      'data': {
          'module': 'WFLocations',
          'view': 'MoveLocation',
          'detail_view': 'true',
          'location_ids': record,
      }
  };
  AppConnector.request(params).then(
    function (data) {
      app.showModalWindow(data);
    }
  ).then(
    function () {
      var editInstance = Vtiger_Edit_Js.getInstance();
      editInstance.registerBasicEvents(jQuery('#moveContainer'));
    }
  );
}

