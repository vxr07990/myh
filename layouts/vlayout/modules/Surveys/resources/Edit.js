/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

Inventory_Edit_Js('Surveys_Edit_Js', {}, {

    registerReferenceSelectionEvent : function(container) {
        this._super(container);
        var thisInstance = this;
        jQuery('input[name="account_id"],input[name="potential_id"],input[name="order_id"]', container).off(Vtiger_Edit_Js.referenceSelectionEvent).on(Vtiger_Edit_Js.referenceSelectionEvent, function(e, data){
            thisInstance.referenceSelectionEventHandler(data);
        });

    },

    referenceSelectionEventHandler : function(data){
        var thisInstance = this;
        var soucceModule = data['source_module'];
        var designatedModule =['Accounts','Orders','Opportunities']
        if(designatedModule.indexOf(soucceModule) != -1 && app.vtranslate('OVERWRITE_EXISTING_MSG1_V2')!=''){
            var message = app.vtranslate('OVERWRITE_EXISTING_MSG1_V2')+
                app.vtranslate('SINGLE_'+soucceModule)+
                ' ('+data['selectedName']+') '+app.vtranslate('OVERWRITE_EXISTING_MSG2_V2');
        }
        else {
            var message = app.vtranslate('OVERWRITE_EXISTING_MSG1') + app.vtranslate('SINGLE_' + data['source_module']) + ' (' + data['selectedName'] + ') ' + app.vtranslate('OVERWRITE_EXISTING_MSG2');
        }
        Vtiger_Helper_Js.showConfirmationBox({'message' : message}).then(
            function(e) {
                thisInstance.populateOppData(data);
            },
            function(error, err){

            }
        );
    },

    setSurveyTimeInterval: function() {
        if(jQuery('input[name="record"]').val() == "") {
            //Only fire this if it's a new record
            var timeSettings = jQuery('#userTimeFormat').data('value');
            var date = new Date();
            var min = date.getMinutes();
            var hour = parseInt(date.getHours());
            if (min < 15) {
                min = '15';
            } else if (min > 15 && min < 30) {
                min = '30';
            } else if (min > 30 && min < 45) {
                min = '45';
            } else {
                min = '00';
                hour++;
            }
            var timeFormat = 'h:i A';
            var time = hour >= 12 ? 'PM' : 'AM';
            if (timeSettings == '24') {
                timeFormat = 'H:i';
                time = '';
            } else {
                hour = hour > 12 ? hour - 12 : hour;
            }
            jQuery('.timepicker-default').timepicker({
                scrollDefault: '10:00 AM',
                step: 15,
                timeFormat: timeFormat,
                useSelect: true,
            });
            jQuery('.timepicker-default').first().val(hour + ':' + min + ' ' + time);
        }
    },

    populateOppData : function(data) {
       // var id = data['record'];
        // var url = 'index.php?module=Surveys&action=PopulateOppData&record=' + id;
        var params = {
            module: 'Surveys',
            action: 'PopulateOppData',
            record: data['record'],
            source_module: data['source_module']
        };
        AppConnector.request(params).then(
            function(data) {
                jQuery('input[name="address1"]').val(data.result.address['bill_street']);
                jQuery('input[name="address2"]').val(data.result.address['bill_street2']);
                jQuery('input[name="city"]').val(data.result.address['bill_city']);
                jQuery('input[name="state"]').val(data.result.address['bill_state']);
                jQuery('input[name="zip"]').val(data.result.address['bill_zip']);
                jQuery('input[name="phone1"]').val(data.result.address['phone']);
                jQuery('input[name="phone2"]').val(data.result.address['phone2']);
                jQuery('input[name="country"]').val(data.result.address['bill_country']);
                jQuery('input[name="address_desc"]').val(data.result.address['address_desc']);
            },
            function(err) {

            }
        );
    },

    /*
     * When creating a new survey appointment and the user selects the time this function fires and sets teh appointment
     * end time an hour from the time selected.
    */
    setSurveyEndTime: function(time) {
        var timeSettings = jQuery('#userTimeFormat').data('value');
        var parts, hours, minutes, tt;
        var startTime = new Date();

        if(timeSettings == '12') {
            parts = time.match(/(\d+):(\d+) (AM|PM)/);
            hours = parseInt(parts[1]);
            minutes = parseInt(parts[2]);
            tt = parts[3];

            if (tt === 'PM' && hours < 12) hours += 12;
            startTime.setHours(hours, minutes, 0, 0);
            startTime.setHours(startTime.getHours()+1);

            var min = ('0'+startTime.getMinutes()).slice(-2);
            var hours = ('0'+startTime.getHours()).slice(-2);

            var mid='AM';

            if(hours==0){
                hours=12;
            } else if(hours>12) {
                hours=hours%12;
                mid='PM';
            }
            if(parts[1] == '11') {
                if(parts[3] == 'AM') {
                    mid = 'PM';
                } else {
                    mid = 'AM';
                }
            }

        } else {
            parts = time.split(':');
            hours = parseInt(parts[0]);
            minutes = parseInt(parts[1]);
            startTime.setHours(hours, minutes, 0, 0);
            startTime.setHours(startTime.getHours()+1);

            min = ('0'+startTime.getMinutes()).slice(-2);
            hours = ('0'+startTime.getHours()).slice(-2);
            mid = '';
        }
        hours = new Number(hours);
        if (hours < 10) hours = "0" +hours;
        jQuery('input[name="survey_end_time"]').val(hours+':'+min+' '+mid);


    },
    setSurveyTime: function() {
        var thisInstance = this;
        jQuery('input[name="survey_time"]').change(function() {
            var timeSettings = jQuery('#userTimeFormat').data('value');
            var time = jQuery(this).val();
            thisInstance.setSurveyEndTime(time);
        });
    },
    getSurvey24Time: function(time) {
        var parts, hours, minutes, tt, hours24;

        if (!time) {
            return '0:0:0';
        }

        parts = time.match(/(\d+):(\d+) (AM|PM)/);
        hours = parseInt(parts[1]);
        minutes = parseInt(parts[2]);
        tt = parts[3];

        if (tt === 'PM' && hours < 12) {
            hours += 12;
        }
        else if (tt === 'AM' && hours == 12) {
            hours -= 12;
        }

        if (hours < 10) hours = "0" +hours;
        if (minutes < 10) minutes = "0" + minutes;

        hours24 = hours + ":" + minutes + ":00";

        return  hours24;
    },
    checkSurveyEndTime: function() {
        var thisInstance = this;

        jQuery('input[name="survey_end_time"]').change(function() {
            thisInstance.ensureSurveyEndTime();
        });
    },

    ensureSurveyEndTime: function() {
        var thisInstance = this;
        var timeSettings = jQuery('#userTimeFormat').data('value');

        var startSurveyTime = jQuery('input[name="survey_time"]').val();
        var endSurveyTime = jQuery('input[name="survey_end_time"]').val();

        start_24hours = thisInstance.getSurvey24Time(startSurveyTime);
        end_24hours = thisInstance.getSurvey24Time(endSurveyTime);

        if (start_24hours >= end_24hours) {
            thisInstance.setSurveyEndTime(startSurveyTime);
        }
    },

    disableEmptyTimeZone : function() {
      $('select[name^="timefield"]').each(function(){
        $(this).children().first().attr('disabled',true);
        $(this).trigger('liszt:updated');
      });
    },

    defaultStatus: function() {
        Vtiger_Edit_Js.setValue('survey_status', 'Assigned');
    },

    registerEvents: function(){
        this._super();
        this.setSurveyTimeInterval();
        if (getQueryVariable('survey_time')) {
            jQuery('input[name="survey_time"]').val(getQueryVariable('survey_time'));
        }
        if (getQueryVariable('survey_end_time')) {
            jQuery('input[name="survey_end_time"]').val(getQueryVariable('survey_end_time'));
        }
        this.setSurveyTime();
        this.checkSurveyEndTime();
        this.ensureSurveyEndTime();
        this.registerReferenceSelectionEvent();
        this.initializeAddressAutofill('Surveys');
        this.disableEmptyTimeZone();

        if($('[name="instance"]').val() == 'sirva') {
            this.defaultStatus();
        }
    }

});
