Vtiger_Edit_Js("TimeSheets_Edit_Js", {
}, {
    //This will store the editview form
    editViewForm: false,
    resourcesCheckCache: {},
    /**
     * This function will return the current form
     */
    getForm: function () {
        if (this.editViewForm == false) {
            this.editViewForm = jQuery('#EditView');
        }
        return this.editViewForm;
    },
    getStartDate: function () {
        return jQuery('input[name="actual_start_date"]').val();
    },
    getStartHour: function () {
        return jQuery('input[name="actual_start_hour"]').val();
    },
    getEndHour: function () {
        return jQuery('input[name="actual_end_hour"]').val();
    },
    setEndHour: function (hour) {
        return jQuery('input[name="actual_end_hour"]').val(hour);
    },
    getTimeOff: function () {
        return jQuery('input[name="timeoff"]').val();
    },
    setTimeOff: function (timeoff) {
        return jQuery('input[name="timeoff"]').val(timeoff).change();
    },
    /**
     * This function will return the current RecordId
     */
    getRecordId: function () {
        return jQuery('input[name="record"]').val();
    },
    setWorkedHours: function (workedHours) {
        jQuery('input[name="total_hours"]').val(workedHours);
    },
    checkDates: function () {
        jQuery(document).on('change', 'input', function () {
            Instance = new TimeSheets_Edit_Js();
            var startDate = Instance.getStartDate();
            var startHour = Instance.getStartHour();
            var endHour = Instance.getEndHour();
            var timeOff = parseFloat(Instance.getTimeOff());
            if(timeOff < 0){
                alert(app.vtranslate('JS_TIMEOFF_NEGATIVE'));
                Instance.setTimeOff(0);
                return false;
            }
            if (startDate != '' && startHour != '' && endHour != '') {

                endHour = Instance.convertHours(endHour);
                startHour = Instance.convertHours(startHour);
                var workedHours = endHour - startHour;
                var finalValue = Math.round((workedHours / 3600000)*100)/100;
                if (finalValue < 0) {
                    alert(app.vtranslate('JS_HOURS_NEGATIVE'));
                    Instance.setEndHour(Instance.getStartHour());
                    return false;
                }
                if( ! isNaN(timeOff) ){
                    finalValue = Math.round( (finalValue - timeOff) *100) / 100;
                }
                if (finalValue < 0) {
                    alert(app.vtranslate('JS_HOURS_TIMEOFF_NEGATIVE'));
                    Instance.setTimeOff(0);
                    return false;
                } else {
                    Instance.setWorkedHours(finalValue);

                }

            }

        });
    },
    convertHours: function (time) {
        var hours = Number(time.match(/^(\d+)/)[1]);
        var minutes = Number(time.match(/:(\d+)/)[1]);
        var AMPM = time.match(/\s(.*)$/)[1];
        if (AMPM == "PM" && hours < 12)
            hours = hours + 12;
        if (AMPM == "AM" && hours == 12)
            hours = hours - 12;
        var sHours = hours.toString();
        var sMinutes = minutes.toString();
        if (hours < 10)
            sHours = "0" + sHours;
        if (minutes < 10)
            sMinutes = "0" + sMinutes;
        var dat = new Date;
        dat.setHours(sHours);
        dat.setMinutes(sMinutes);
        dat.setSeconds(0);

        return dat;
    },
    registerBasicEvents: function (container) {
        this._super(container);
        this.checkDates();
        jQuery('input[name="total_hours"]').attr('readonly','readonly');
    }
});

