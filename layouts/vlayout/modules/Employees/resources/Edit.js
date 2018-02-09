/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

Vtiger_Edit_Js("Employees_Edit_Js", {}, {
    getParameterByName:function(name) {
	var url = window.location.href;
	name = name.replace(/[\[\]]/g, "\\$&");
	var regex = new RegExp("[?&]" + name + "(=([^&#]*)|&|#|$)"), results = regex.exec(url);
	if (!results) return null;
	if (!results[2]) return '';
	return decodeURIComponent(results[2].replace(/\+/g, " "));
    },
    registerEvents: function () {
        this._super();
        this.initializeAddressAutofill('Employees');
        loadBlocksByBusinesLine('Employees', 'employee_type');
        //this.registerPrimaryRoleField();
        this.registerEventForPersonnelRoleFields();
        this.registerEventForOwnerField();
        this.setReadOnlyFields();
        this.registerEventAutoFillAvailability();
        this.registerAssociateTypeChange();
        this.registerStatus();
        this.registerRehireEligibility();
        this.registerRehireEligibilityChange();
        this.registerStatusChange();
        this.registerVendorNumberChange();
        this.registerDefaultAllDayEveryDay();

        // Register event for "Move HQ User" field
        var thisInstance = this;
        jQuery('[name="move_hq_user"]').on('change', thisInstance.move_hq_userEventChange);
        this.move_hq_userEventChange();

        if(jQuery('[name="instance"]').val() == 'graebel'){
            var common = new Employees_Common_Js();
            common.applyAllVisibilityRules(false);
            this.registerRoleField();
        }

    //get first Contractor from PersonnelType (IC Transportation Contractor,Terminal Service Contractor,Contractor Surveyor)
	if(thisInstance.getParameterByName("sourceModule") == "Vendors" && thisInstance.getParameterByName("relationOperation") == "true"){
	    jQuery('[name="employee_type"] option:contains("Contractor"):first').prop("selected",true).trigger("liszt:updated")
	}

        /*
        //Won't handle this case.  leaving for if in the future.
        var rules = {
            move_hq_user: {
                conditions: [
                    {
                        operator: 'in',
                        value: ['Yes'],
                        not: true,
                        targetFields: [
                            {
                                name: 'userid',
                                hide: true,
                                }
                        ]
                                }
                ]
                            }
        };
        this.applyVisibilityRules(rules, true);
         */
                    },

    registerRoleField: function () {
        var thisInstance = this;
        var fn = function() {
            var isDriver = false;
            if (jQuery('select[name="contractor_prole"]').find(':selected').val().indexOf("Driver") >= 0) {
                isDriver = true;
            } else if (jQuery('select[name="employee_prole"]').find(':selected').val().indexOf("Driver") >= 0) {
                isDriver = true;
            } else if (jQuery('select[name="employee_srole"]').find(':selected').val().indexOf("Driver") >= 0) {
                isDriver = true;
            }
            if (isDriver) {
                thisInstance.toggleDriverInformationFields('show');
            } else {
                thisInstance.toggleDriverInformationFields('hide');
            }
        };
        jQuery('[name="contractor_prole"],[name="employee_prole"],[name="employee_srole"]').on('change', fn);
        fn();
    },

    move_hq_userEventChange: function() {
        var move_hq_user = jQuery('[name="move_hq_user"]').val();
        if (move_hq_user == 'Yes') {
            Vtiger_Edit_Js.showCell('userid');
        } else {
            Vtiger_Edit_Js.hideCell('userid');
            jQuery('[name="userid"]').val('0').trigger('liszt:updated');
        }
    },

    registerVendorNumberChange : function()
    {
        jQuery('.contentsDiv').on(Vtiger_Edit_Js.postReferenceSelectionEvent, '[name="employee_vendornumber"]', function(e,data){
            data = data['data'];
            var message = 'Would you like to load the remote data from the Vendor?';
            Vtiger_Helper_Js.showConfirmationBox({'message' : message}).then(
                function(){
                    data = data[Object.keys(data)[0]];
                    if(typeof data['info'] == 'object')
                    {
                        data = data['info'];
                    }
                    var map = {
                        'name': 'First Name',
                        'employee_lastname': 'Last Name',
                        'address1': 'Address 1',
                        'address2': 'Address 2',
                        'city': 'City',
                        'state': 'State',
                        'zip': 'Zip',
                        'country': 'Country',
                        'employee_hphone': 'Primary Phone',
                        'employee_mphone': 'Mobile Phone',
                        'employee_email': 'Primary Email',
                    };
                    Vtiger_Edit_Js.populateData(data, map);
                },
                function(error, err) {
                    //they pressed no don't populate the data.
                }
            );
        });
    },


    registerEventForPersonnelRoleFields: function () {
        var editViewForm = this.getForm();
        editViewForm.find('table[name="LBL_DRIVER_INFORMATION"]').addClass('hide');
        editViewForm.on('change', '[name="employee_secondaryrole"]', function (e) {
            var selectedSecondaryRoleValues = jQuery(this).val();
            var selectedPrimaryRoleValue = jQuery('[name="employee_primaryrole"]').val();
            if (selectedPrimaryRoleValue != ''){
                if ( selectedSecondaryRoleValues != ''){
                    selectedValues = selectedPrimaryRoleValue+','+selectedSecondaryRoleValues;
                }else{
                    selectedValues = selectedPrimaryRoleValue;
                }
            }else{
                if ( selectedSecondaryRoleValues != ''){
                    selectedValues = selectedSecondaryRoleValues;
                }else{
                    selectedValues = '';
                }
            }

            var dataUrl = "index.php?module=EmployeeRoles&action=ActionAjax&mode=checkClass&selected_ids=" + selectedValues;
            AppConnector.request(dataUrl).then(
                    function (data) {
                        if (data.success) {
                            if (data.result.value == 'true') {
                                editViewForm.find('table[name="LBL_DRIVER_INFORMATION"]').removeClass('hide');
                            } else {
                                editViewForm.find('table[name="LBL_DRIVER_INFORMATION"]').addClass('hide');
                            }
                        }
                    },
                    function (error, err) {

                    }
            );
        });

        var clearReferenceSelection = jQuery('[name="employee_primaryrole"]').closest('td').find('.clearReferenceSelection');
        clearReferenceSelection.on('click', function () {
            editViewForm.find('[name="employee_secondaryrole"]').trigger("change");
        });

        editViewForm.on(Vtiger_Edit_Js.referenceSelectionEvent, '[name="employee_primaryrole"]', function (e) {
            // var selectedValues = jQuery(this).val();
            var selectedPrimaryRoleValue = jQuery(this).val();
            var agentid = jQuery('select[name=agentid]');
            var selectedSecondaryRoleValues = editViewForm.find('[name="employee_secondaryrole"]').val();
            var employee_secondaryrole = editViewForm.find('[name="employee_secondaryrole"]');
            var customFilters={};
            customFilters['agentid'] = agentid.val();

            if (selectedPrimaryRoleValue != ''){
                if ( selectedSecondaryRoleValues != ''){
                    selectedValues = selectedPrimaryRoleValue+','+selectedSecondaryRoleValues;
                }else{
                    selectedValues = selectedPrimaryRoleValue;
                }
            }else{
                if ( selectedSecondaryRoleValues != ''){
                    selectedValues = selectedSecondaryRoleValues;
                }else{
                    selectedValues = '';
                }
            }

            customFilters['employee_primaryrole']=selectedValues;
            employee_secondaryrole.data('custom-filter',JSON.stringify(customFilters));
            var dataUrl = "index.php?module=EmployeeRoles&action=ActionAjax&mode=checkClass&selected_ids=" + selectedValues;
            AppConnector.request(dataUrl).then(
                    function (data) {
                        if (data.success) {
                            if (data.result.value == 'true') {
                                editViewForm.find('table[name="LBL_DRIVER_INFORMATION"]').removeClass('hide');
                            } else {
                                editViewForm.find('table[name="LBL_DRIVER_INFORMATION"]').addClass('hide');
                            }
                        }
                    },
                    function (error, err) {

                    }
            );
        });

        editViewForm.on(Vtiger_Edit_Js.referenceDeSelectionEvent,'[name="employee_primaryrole"]', function(e){
            var selectedValues = jQuery(this).val();
            var agentid = jQuery('select[name=agentid]');
            var employee_secondaryrole = editViewForm.find('[name="employee_secondaryrole"]');
            var customFilters={};
            customFilters['agentid'] = agentid.val();
            customFilters['employee_primaryrole']=selectedValues;
            employee_secondaryrole.data('custom-filter',JSON.stringify(customFilters));
        });

        editViewForm.find('[name="employee_secondaryrole"]').trigger("change");
        editViewForm.find('[name="employee_primaryrole"]').trigger(Vtiger_Edit_Js.referenceSelectionEvent);
    },


    //OT 2471- Driver info field should show when Primary Role is "driver" and be hidden otherwise.
    //Picklists changed under OT 3150, modified code to show block if Primary or Secondary role includes "Driver"

    toggleDriverInformationFields: function (state) {
        var thisInstance = this;
        var toggleFields = [
//            'isqualify',
//            'notes',
//            'driver_no',
//            'carb_compliant',
//            'fleet_type',
//            'trailer',
            'LBL_DRIVER_INFORMATION',
        ];

        thisInstance.showHideFields(toggleFields, state);

        if (state == 'show') {
            jQuery('[name="employees_isdriver"]').attr('checked', true);
        } else {
            jQuery('[name="employees_isdriver"]').attr('checked', false);
        }


    },


    registerRehireEligibilityChange: function() {
        var thisInstance = this;
        jQuery('select[name="rehire_eligibility"]').change(function(){
            thisInstance.registerRehireEligibility();
        });
    },

    registerRehireEligibility: function() {
        var eligibilityField = jQuery('select[name="rehire_eligibility"]');
        var eligibilityDateField = jQuery('[name="rehire_eligibility_date"]')
        if(eligibilityField.find(':selected').val() == 'Yes'){
            eligibilityDateField.parent().closest('tr').removeClass('hide');

        } else {
            eligibilityDateField.parent().closest('tr').addClass('hide');
        }
    },


    showHideFields: function (fields, state) {
        jQuery.each(fields, function (key, value) {
            if (value.match("LBL")) {
                if (state == 'show') {
                    jQuery('[name="' + value + '"]').removeClass('hide');
                } else {
                    jQuery('[name="' + value + '"]').addClass('hide');
                }
            } else { //else its an input
                if (state == 'show') {
                    jQuery('[name="'+value+'"]').val('').parent().removeClass('hide').closest('td').prev().find('label').removeClass('hide');
                } else {
                    jQuery('[name="' + value + '"]').parent().addClass('hide').closest('td').prev().find('label').addClass('hide');
                }
            }

        });
    },



    registerStatusChange: function() {
        var thisInstance = this;
        var statusField = jQuery('select[name="employee_status"]');
        statusField.change(function(){
            thisInstance.registerStatus();
        });
    },

    registerStatus: function () {
        var statusField = jQuery('select[name="employee_status"]');
        if (statusField.val() == 'Terminated'){
            jQuery('[name="LBL_TERMINATION_INFO"]').show();
        } else {
            jQuery('[name="LBL_TERMINATION_INFO"]').hide();
        }
    },


    setReadOnlyFields: function () {
        if(jQuery('[name="instance"]').val() != 'graebel') {
        jQuery('[name=driver_no]').prop('readonly', 'readonly');
        }
    },
    registerAssociateTypeChange: function () {
        var editViewForm = this.getForm();
        editViewForm.on('change', '[name="employee_type"]', function (e) {
            loadBlocksByBusinesLine('Employees', 'employee_type');
        });
    },


    /**
     * Function to get popup params
     */
    getPopUpParams : function(container) {
        var params = this._super(container);
        var sourceFieldElement = jQuery('input[class="sourceField"]',container);

        if(sourceFieldElement.attr('name') == 'employee_primaryrole') {
            var form = this.getForm();
            var agentidElement  = form.find('[name="agentid"]');
            if(agentidElement.length > 0 && agentidElement.val().length > 0) {
                params.agentId = agentidElement.val();
            }
        }
        return params;
    },

    /**
     * Function to search module names
     */
    searchModuleNames : function(params) {
        var aDeferred = jQuery.Deferred();

        if(typeof params.module == 'undefined') {
            params.module = app.getModuleName();
                    }
        if(typeof params.action == 'undefined') {
            params.action = 'BasicAjax';
        }


        // Add Owner to filter
        if (params.search_module == 'EmployeeRoles') {
            var form = this.getForm();
            var agentidElement  = form.find('[name="agentid"]');
            if(agentidElement.length > 0 && agentidElement.val().length > 0) {
                params.agentId = agentidElement.val();
                        }
                    }

        AppConnector.request(params).then(
                                function (data) {
                aDeferred.resolve(data);
            },
            function(error){
                aDeferred.reject();
            }
        );
        return aDeferred.promise();
    },

    registerEventForOwnerField : function () {
        var thisInstance = this;
        jQuery('select[name=agentid]').on('change',function () {
            var form = thisInstance.getForm();
            var customFilters={};
            var agentid = jQuery(this).val();
            var employee_secondaryrole = form.find('[name="employee_secondaryrole"]');
            var employee_primaryrole = form.find('[name="employee_primaryrole"]');
            customFilters['agentid'] = agentid;
            customFilters['employee_primaryrole']=employee_primaryrole.val();
            employee_secondaryrole.data('custom-filter',JSON.stringify(customFilters));
        });

        jQuery('select[name=agentid]').change();
    },

    registerEventAutoFillAvailability : function () {
        var checkboxAllday = $('.block_LBL_EMPLOYEES_AVAILABILITY').find('input[name$="all"]');
        $(checkboxAllday).each(function( index, element ) {
            $(element).on('change',function () {
                var focus = $(element);
                var trElementParent = focus.closest('tr');
                if (focus.prop('checked')==true){
                    var agentId = jQuery('select[name=agentid]').val();
                    var params = [];

                    params.module = 'Employees';
                    params.action = 'BasicAjax';
                    params.agentId = agentId;
                    params.mode = 'getAgentPersonnelTimes';

                    AppConnector.request(params).then(function(data){
                                    if (data.success) {
                            var result = data.result;
                            trElementParent.find('input[name$="start"]').val(result.timeToStart);
                            trElementParent.find('input[name$="end"]').val(result.timeToEnd);
                            var selectOptionsTimeZoneStart = trElementParent.find('select[name$="start"]');
                            var selectOptionsTimeZoneEnd   = trElementParent.find('select[name$="end"]');
                            var optionsTimeZoneStart = jQuery(selectOptionsTimeZoneStart).find('option');
                            var optionsTimeZoneEnd   = jQuery(selectOptionsTimeZoneEnd).find('option');
                            jQuery.each(optionsTimeZoneStart, function (index, item) {
                                if(jQuery(item).val() == result.timezoneToStart){
                                    console.log(jQuery(item).val());
                                    jQuery(item).prop("selected",true);
                                }else{
                                    jQuery(item).removeProp("selected");
                                        }
                            });
                            selectOptionsTimeZoneStart.trigger('liszt:updated');

                            jQuery.each(optionsTimeZoneEnd, function (index, item) {
                                if(jQuery(item).val() == result.timezoneToEnd){
                                    console.log(jQuery(item).val());
                                    jQuery(item).prop("selected",true);
                                }else{
                                    jQuery(item).removeProp("selected");
                                            }
                            });
                            selectOptionsTimeZoneEnd.trigger('liszt:updated');
                                        }
                    });
                }else{
                    trElementParent.find('input[name$="start"]').val('');
                    trElementParent.find('input[name$="end"]').val('');
                    var selectOptionsTimeZoneStart = trElementParent.find('select[name$="start"]');
                    var selectOptionsTimeZoneEnd   = trElementParent.find('select[name$="end"]');
                    var optionsTimeZoneStart = jQuery(selectOptionsTimeZoneStart).find('option');
                    var optionsTimeZoneEnd   = jQuery(selectOptionsTimeZoneEnd).find('option');
                    jQuery.each(optionsTimeZoneStart, function (index, item) {
                        jQuery(item).removeProp("selected");
                    });
                    selectOptionsTimeZoneStart.trigger('liszt:updated');

                    jQuery.each(optionsTimeZoneEnd, function (index, item) {
                        jQuery(item).removeProp("selected");
                    });
                    selectOptionsTimeZoneEnd.trigger('liszt:updated');
                                }
            })
        });
            },
            
        registerDefaultAllDayEveryDay: function(){
            if(jQuery('input[name="record"]').val() === ''){
                var checkboxs = [
                    'employees_monday',
                    'employees_mondayall',
                    'employees_tuesday',
                    'employees_tuesdayall',
                    'employees_wednesday',
                    'employees_wednesdayall',
                    'employees_thursday',
                    'employees_thursdayall',
                    'employees_friday',
                    'employees_fridayall',
                    'employees_saturday',
                    'employees_saturdayall'
                ];
                $.each(checkboxs,function(index,checkbox){
                    jQuery('input[type="checkbox"][name="' + checkbox +'"]').attr('checked',true).change();
                });
            }
        },
});


