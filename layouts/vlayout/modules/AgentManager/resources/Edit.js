/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

Vtiger_Edit_Js("AgentManager_Edit_Js",{},{

	//when the self haul is checked it adds a participating agent,
	//when it's unchecked it removes that Participating Agent.
	//That is all.
	registerChangeSelfHaul : function() {
		var thisInstance = this;
		var selfHaulCB = jQuery('input:checkbox[name="self_haul"]');

		selfHaulCB.on('change', function () {

			var agentid = jQuery('input[name="record"]').val();
			var agentcode = jQuery('input[name="agency_code"]').val();
			var agentname = jQuery('input[name="agency_name"]').val();

			if (selfHaulCB.prop('checked')) {
				//set self haul agency
				console.dir("Checked Is Self Haul");
				var agentLabel = agentname;
				var container = jQuery('#self_haul_agentmanagerid_display');
				var obj = {id: agentid, name: agentLabel, suppress: true};
				thisInstance.setReferenceFieldValue(container, obj);
			} else {
				//unset self haul agency.
				console.dir("REMOVE Is Self Haul");
			}
		});
	},
    registerChangeDepositType: function () {
        var thisInstance = this;
        thisInstance.checkAndDisableField();
        jQuery('[name="default_deposit_type"]').on('change', function () {
            var type = jQuery(this).val();
            console.log(type);
            if(type == 'Flat Amount'){
                jQuery('.currency_container').removeClass('hide');
                jQuery('.percent_container').removeClass('hide');
                jQuery('.percent_container').addClass('hide');
                thisInstance.checkAndDisableField();
            }else{
                jQuery('.currency_container').removeClass('hide');
                jQuery('.percent_container').removeClass('hide');
                jQuery('.currency_container').addClass('hide');
                thisInstance.checkAndDisableField();
            }
        });
    },
    checkAndDisableField: function () {
        jQuery('.percent_container,.currency_container').each(function () {
            if(jQuery(this).hasClass('hide')){
                jQuery(this).find('[name="default_deposit_amount"]').prop('disabled',true);
            }else{
                jQuery(this).find('[name="default_deposit_amount"]').prop('disabled',false);
            }
        });
    },

    removeSelectOptionPayrollWeekStartDate: function(){
	jQuery('[name="payroll_week_start_date"]').find('option[value=""]').remove();
	jQuery('[name="payroll_week_start_date"]').trigger("liszt:updated");
    },
    registerHoursChange: function () {
        var thisInstance = this;
        //al timepicker input
        jQuery(document).on('change', '[name*="order_task_"], [name*="personnel_"]', function () {

            var fieldname = jQuery(this).attr('name');

            if (fieldname.indexOf('order_task') !== -1) {
                var start = jQuery('[name="order_task_start_time"]').val();
                var end = jQuery('[name="order_task_end_time"]').val();
                var start_timezone = jQuery('[name="timefield_order_task_start_time"]').val();
                var end_timezone = jQuery('[name="timefield_order_task_end_time"]').val();
            } else {
                var start = jQuery('[name="personnel_start_time"]').val();
                var end = jQuery('[name="personnel_end_time"]').val();
                var start_timezone = jQuery('[name="timefield_personnel_start_time"]').val();
                var end_timezone = jQuery('[name="timefield_personnel_end_time"]').val();
            }
            
            var result = thisInstance.checkHours(start, end, start_timezone, end_timezone);
            if( result.ok == false){
                if (result.field == 'timefield' && fieldname.indexOf(result.field) !== -1){
                    var fieldname2 = '';
                    if ( fieldname.indexOf('end') !== -1 ) {
                        fieldname2 = fieldname.replace('end','start');
                    } else {
                        fieldname2 = fieldname.replace('start','end');
                    }
                    //jQuery('[name="' + fieldname2 + '"]').val('').trigger('liszt:updated');
                }else if ( result.field != 'none' && fieldname.indexOf(result.field) !== -1 ) {
                    jQuery(this).val('');
                }
            }

        });
    },
    checkHours: function(start, end, start_timezone, end_timezone){
        var thisInstance = this;
        if (start == '' && end != '') {
                var params = {
		    title: app.vtranslate('JS_MESSAGE'),
		    text: app.vtranslate('Please complete Start Time.'),
		    animation: 'show',
		    type: 'error',
		};
		Vtiger_Helper_Js.showPnotify(params);
                
                return {ok: false, field: 'start_time'};
            }
            
        if (start != '' && end == '') {
                var params = {
		    title: app.vtranslate('JS_MESSAGE'),
		    text: app.vtranslate('Please complete End Time.'),
		    animation: 'show',
		    type: 'error',
		};
		Vtiger_Helper_Js.showPnotify(params);
                
                return {ok: false, field: 'end_time'};
            }

            if (start_timezone != end_timezone || start != '' && start_timezone == '' ||  end != '' && end_timezone == '' ) {
                var params = {
		    title: app.vtranslate('JS_MESSAGE'),
		    text: app.vtranslate('Both times need to be on the same timezone'),
		    animation: 'show',
		    type: 'error',
		};
		Vtiger_Helper_Js.showPnotify(params);
               
                return {ok: false, field: 'timefield'};
            }

            if (start != '' && end != '' && thisInstance.getTwentyFourHourTime(start) > thisInstance.getTwentyFourHourTime(end)) {
                var params = {
		    title: app.vtranslate('JS_MESSAGE'),
		    text: app.vtranslate('End Time needs to be greater than Start Time'),
		    animation: 'show',
		    type: 'error',
		};
		Vtiger_Helper_Js.showPnotify(params);
                
                return {ok: false, field: 'time'};
            }

    },
    getTwentyFourHourTime: function(amPmString){
       var d = new Date("1/1/2013 " + amPmString); 
       return d;
        
    },
    registerRecordPreSaveEvent: function () {
        var thisInstance = this;
        form = this.getForm();
        form.on(Vtiger_Edit_Js.recordPreSave, function (e, data) {

            //Check Orders Task
            
            

            var start = jQuery('[name="order_task_start_time"]').val();
            var end = jQuery('[name="order_task_end_time"]').val();
            var start_timezone = jQuery('[name="timefield_order_task_start_time"]').val();
            var end_timezone = jQuery('[name="timefield_order_task_end_time"]').val();
            
            var result = thisInstance.checkHours(start, end, start_timezone, end_timezone);
            if (result && result.ok == false) {
                 e.preventDefault();
               return false;
            }

            //Checking Personnel Hours
            var start = jQuery('[name="personnel_start_time"]').val();
            var end = jQuery('[name="personnel_end_time"]').val();
            var start_timezone = jQuery('[name="timefield_personnel_start_time"]').val();
            var end_timezone = jQuery('[name="timefield_personnel_end_time"]').val();

            result = thisInstance.checkHours(start, end, start_timezone, end_timezone);
            if (result && result.ok == false) {
                 e.preventDefault();
                return false;
            }
            
            form.submit();

        });
    },
    
	registerEvents: function(){
		this._super();
		this.initializeAddressAutofill('AgentManager');
		this.registerChangeSelfHaul();
		this.registerChangeDepositType();
		this.removeSelectOptionPayrollWeekStartDate();
                this.registerHoursChange();
                this.registerRecordPreSaveEvent();
	}
});
