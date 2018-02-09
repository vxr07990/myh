/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

Vtiger_Edit_Js("Users_Edit_Js", {}, {

    duplicateCheckCache: {},

    //Hold the conditions for a hour format
    hourFormatConditionMapping: false,


    registerWidthChangeEvent: function () {
        var widthType = app.cacheGet('widthType', 'narrowWidthType');
        jQuery('#currentWidthType').html(jQuery('li[data-class="' + widthType + '"]').html());
        jQuery('#widthType').on('click', 'li', function (e) {
            var value = jQuery(e.currentTarget).data('class');
            app.cacheSet('widthType', value);
            jQuery('#currentWidthType').html(jQuery(e.currentTarget).html());
            window.location.reload();
        });
    },

    registerHourFormatChangeEvent: function () {

    },

    registerRules: function (isEditView) {
        var rules = {
            has_permissions: {
                conditions: [
                    {
                        operator: 'is',
                        value: 'false',
                        targetFields: [
                            {
                                name: 'sts_user_id_nvl',
                                readonly: true
                            },
                            {
                                name: 'sts_user_id',
                                readonly: true
                            },
                            {
                                name: 'sts_agent_id',
                                readonly: true
                            },
                            {
                                name: 'sts_agent_id_nvl',
                                readonly: true
                            },
                        ]
                    }
                ]
            },
        }
        this.applyVisibilityRules(rules, isEditView);
    },

    getPermissions: function () {
        var params = {
            'type': 'POST',
            'url': 'index.php',
            'data': {
                'module': 'Users',
                'action': 'GetPermissions'
            }
        };

        var thisI = this
        AppConnector.request(params).then(
            function (data) {
                if (data.success) {
                    if(data.result.isAdmin || data.result.isParentVanlineUser) {
                        jQuery('input[name="has_permissions"]').val('true')
                    } else {
                        jQuery('input[name="has_permissions"]').val('false')
                    }
                    thisI.registerRules()
                }
            }
        );
    },

    changeStartHourValuesEvent: function (form) {
        var thisInstance = this;
        form.on('change', 'select[name="hour_format"]', function (e) {
            var hourFormatVal = jQuery(e.currentTarget).val();
            var startHourElement = jQuery('select[name="start_hour"]', form);
            var conditionSelected = startHourElement.val();
            var list = thisInstance.hourFormatConditionMapping['hour_format'][hourFormatVal]['start_hour'];
            var options = '';
            for (var key in list) {
                //IE Browser consider the prototype properties also, it should consider has own properties only.
                if (list.hasOwnProperty(key)) {
                    var conditionValue = list[key];
                    options += '<option value="' + key + '"';
                    if (key == conditionSelected) {
                        options += ' selected="selected" ';
                    }
                    options += '>' + conditionValue + '</option>';
                }
            }
            startHourElement.html(options).trigger("liszt:updated");
        });


    },

    triggerHourFormatChangeEvent: function (form) {
        this.hourFormatConditionMapping = jQuery('input[name="timeFormatOptions"]', form).data('value');
        this.changeStartHourValuesEvent(form);
        jQuery('select[name="hour_format"]', form).trigger('change');
    },

    /**
     * Function to register recordpresave event
     */
    registerRecordPreSaveEvent: function (form) {
        var thisInstance = this;
        form.on(Vtiger_Edit_Js.recordPreSave, function (e, data) {
            var userName = jQuery('input[name="user_name"]').val();
            var newPassword = jQuery('input[name="user_password"]').val();
            var confirmPassword = jQuery('input[name="confirm_password"]').val();
            var record = jQuery('input[name="record"]').val();
            if (record == '') {
                if (newPassword != confirmPassword) {
                    Vtiger_Helper_Js.showPnotify(app.vtranslate('JS_REENTER_PASSWORDS'));
                    e.preventDefault();
                    return;
                }

                if (!(userName in thisInstance.duplicateCheckCache)) {
                    thisInstance.checkDuplicateUser(userName).then(
                        function (data) {
                            if (data.result) {
                                thisInstance.duplicateCheckCache[userName] = data.result;
                                Vtiger_Helper_Js.showPnotify(app.vtranslate('JS_USER_EXISTS'));
                            }
                        },
                        function (data, error) {
                            thisInstance.duplicateCheckCache[userName] = data.result;
                            form.submit();
                        }
                    );
                } else {
                    if (thisInstance.duplicateCheckCache[userName] == true) {
                        Vtiger_Helper_Js.showPnotify(app.vtranslate('JS_USER_EXISTS'));
                    } else {
                        delete thisInstance.duplicateCheckCache[userName];
                        return true;
                    }
                }
                e.preventDefault();
            }
            // if (jQuery('#agent_ids').length > 0 && jQuery('[name="roleid"] option:selected').text() !== 'Administrator' && jQuery('#agent_ids').find('option:selected').length <= 0) {
            //     Vtiger_Helper_Js.showPnotify(app.vtranslate('Please choose an Agent or Vanline'));
            //     e.preventDefault();
            // }
        })
    },

    checkDuplicateUser: function (userName) {
        var aDeferred = jQuery.Deferred();
        var params = {
            'module': app.getModuleName(),
            'action': "SaveAjax",
            'mode': 'userExists',
            'user_name': userName
        }
        AppConnector.request(params).then(
            function (data) {
                if (data.result) {
                    aDeferred.resolve(data);
                } else {
                    aDeferred.reject(data);
                }
            }
        );
        return aDeferred.promise();
    },
    addSelect2Multipicklist: function () {
        $("#agent_ids").select2();
    },
    saveAgentsOrder: function () {
        var thisInstance = this;
        $("#agent_ids").on('change', function () {
            var data = $(this).select2('data');
            var array = [];
            $.each(data, function (index, val) {
                array[index] = val.id;
            });
            array.join(',');
            $("input[name=agent_ids_order]").val(array);
            thisInstance.updateMoveCoordinatorValues(array);
        });
    },
    roleChangeAgentsPicklist: function (container) {
        var thisInstance = this;

        var rolePickList = container.find('[name="roleid"]');
        if (typeof rolePickList === "undefined") {
            return;
        }

        rolePickList.on('change', function (e) {

            var vanline_depths = jQuery('input[name="vanline_depth"]').val().split(',');
            var isVanline = jQuery.inArray(jQuery('[name="roleid"] option:selected').val(), vanline_depths) >= 0;

            var targetPickList = jQuery('#agent_ids', container);
            //targetPickList.find('option').remove().end();
            var agentList = [];
            // if (isVanline) {
            //     //Need to hide the agents option and show only the valines
            //     if(typeof jQuery('[name="vanline_list"]').val() != "undefined") {
            //         agentList = JSON.parse(jQuery('[name="vanline_list"]').val());
            //     }
            //
            // } else {
            //     if(typeof jQuery('[name="agent_list"]').val() != "undefined") {
            //         agentList = JSON.parse(jQuery('[name="agent_list"]').val());
            //     }
            // }

            thisInstance.updateAgentsPicklistValues(container);

            jQuery('select[name="move_coordinator"]').empty().html("").trigger("liszt:updated");
            jQuery('select[name="move_coordinator_navl"]').empty().html("").trigger("liszt:updated");

            jQuery.each(agentList, function (i, e) {
                var decoded = $('<div/>').html(e).text();
                targetPickList.append($('<option>', {
                    value: i,
                    text: decoded
                }));
            });
            targetPickList.select2();

            // Check and show/hie OA/DA Coordinator
            var cf_oa_da_coordinator = jQuery('#Users_editView_fieldName_cf_oa_da_coordinator');
            var roleid = jQuery(e.currentTarget).val();
            if(roleid == 'H9' || roleid == 'H10' || roleid == 'H11') {
                cf_oa_da_coordinator.parent('td').removeClass('hide');
                cf_oa_da_coordinator.parent('td').prev('td').removeClass('hide');
            }else{
                cf_oa_da_coordinator.parent('td').addClass('hide');
                cf_oa_da_coordinator.parent('td').prev('td').addClass('hide');
            }

        });
        rolePickList.trigger('change');
    },
    updateAgentsPicklistValues: function (container) {
        var targetPickList = jQuery('#agent_ids', container);
        var selectedAgents = targetPickList.val();

        targetPickList.find('option').remove().end();
        var agentList = [];
        var selectedRole = jQuery('[name="roleid"] option:selected').text().toLowerCase();
        if (selectedRole.indexOf("van line") >= 0 || selectedRole == 'administrator') {
            //Need to hide the agents option and show only the valines
            if(typeof jQuery('[name="vanline_list"]').val() != "undefined") {
                agentList = JSON.parse(jQuery('[name="vanline_list"]').val());
            }
        } else {
            if(typeof jQuery('[name="agent_list"]').val() != "undefined") {
                agentList = JSON.parse(jQuery('[name="agent_list"]').val());
            }
        }

        jQuery.each(agentList, function (i, e) {
            var decoded = $('<div/>').html(e).text();
            targetPickList.append($('<option>', {
                value: i,
                text: decoded
            }));
        });
        targetPickList.val(selectedAgents);
        targetPickList.select2();
    },
    updateMoveCoordinatorValues: function () {
        var selectedValue   = "";
        var current_user_id = jQuery('#current_user_id').val();
        var agent_ids_order = jQuery('[name="agent_ids_order"]').val();
        if (typeof agent_ids_order === "undefined") {
            agent_ids_order = "";
        }
        var selectedID      = jQuery('select[name="move_coordinator"]').attr('data-selected-value');
        var selectedID_navl = jQuery('select[name="move_coordinator_navl"]').attr('data-selected-value');

        var dataUrl = "index.php?module=Users&action=GetMoveCoordinator&current_user_id="+current_user_id+"&agent_ids_order="+agent_ids_order;

        AppConnector.request(dataUrl).then(
            function(data) {
                if (data.success) {
                    var optionsList = "";
                     var optionsList_navl = "";
                    if (data.result != null) {
                        jQuery.each(data.result, function (i, value) {
                            if (value.vanline_id == 14) {
                                selectedValue = (selectedID == value.id) ? "selected" : "";
                                optionsList += '<option value="' + value.id + '" ' + selectedValue + '>' + value.first_name + ' ' + value.last_name + '</option>'
                             }
                             else {
                                selectedValue = (selectedID_navl == value.id) ? "selected" : "";
                                optionsList_navl += '<option value="' + value.id + '" ' + selectedValue + '>' + value.first_name + ' ' + value.last_name + '</option>'
                             }
                        });

                        if (optionsList == "") {
                            optionsList += '<option value="">Select an Option</option>'
                        }
                        if (optionsList_navl == "") {
                            optionsList_navl += '<option value="">Select an Option</option>'
                        }
                        jQuery('select[name="move_coordinator"]').html(optionsList).trigger('liszt:updated');
                        jQuery('select[name="move_coordinator_navl"]').html(optionsList_navl).trigger('liszt:updated');

                    }
                    else {
                        jQuery('select[name="move_coordinator"]').empty().html("").trigger("liszt:updated");
                        jQuery('select[name="move_coordinator_navl"]').empty().html("").trigger("liszt:updated");
                    }
                } else {
                    console.error('Error: ' + data.result);
                }
            },
            function () {
                //it doesn't have error returned because it just echoes!
                //console.error('Error: ' + error);
            }
        );
    },

    adminCheck: function() {
        var role = $('[name="roleid"]');
        var admin = $('[name="is_admin"]');
        var agents = $('#agent_ids');

        if(role.val() == "H6") {
            admin.prop("checked", true);
            Vtiger_Edit_Js.makeFieldNotMandatory(agents);
        }else {
            admin.prop("checked", false);
            Vtiger_Edit_Js.makeFieldMandatory(agents);
        }
    },

    registerAdminCheck: function() {
        $('[name="roleid"]').on('change', this.adminCheck);
    },

    registerEvents: function (isEditView) {
        this._super();

        var form = this.getForm();
        this.registerAdminCheck();
        this.registerWidthChangeEvent();
        this.triggerHourFormatChangeEvent(form);
        this.addSelect2Multipicklist();
        this.saveAgentsOrder();
        this.roleChangeAgentsPicklist(form);
        this.updateAgentsPicklistValues(form);
        this.updateMoveCoordinatorValues();
        this.registerRules(isEditView);
        this.getPermissions();
    }
});
