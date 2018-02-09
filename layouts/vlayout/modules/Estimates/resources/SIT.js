// This was built with SIRVA in mind, but if you think you can use it, go ahead.

Vtiger_Edit_Js("Estimates_SIT_Js", {
    instance: null,
    
    getInstance: function() {
        if(this.instance == null) {
            this.instance = new Estimates_SIT_Js();
        }

        return this.instance;
    },

    I: function() {
        return this.getInstance();
    }
}, {
    gatherData: function(ele) {
        var data = {};

        // Booleans for checking update event.
        data.is_dest = ele.attr('name').indexOf('dest') > -1;
        data.is_number_field = ele.attr('name').indexOf('number') > -1;

        // Build field name parts based on destination vs origin.
        data.field_type = data.is_dest ? "dest" : "origin";
        data.to_date_type = data.field_type + '_' + (data.is_dest ? "delivery" : "pickup");

        // Field elements
        data.days_ele = $('[name="sit_' + data.field_type + '_number_days"]');
        data.from_ele = $('[name="sit_' + data.field_type + '_date_in"]');
        data.to_ele = $('[name="sit_' + data.to_date_type + '_date"]');

        return data;
    },

    updateHandler: function(ele) {
        var data = this.gatherData(ele);

        // Ensure all fields are present.
        if(!data.days_ele.length || !data.from_ele.length || !data.to_ele.length) {
            bootbox.alert("Broken Fields: Unable to find fields to update.");
        }else {
            // Call correct handler.
            var result;
            if(data.is_number_field) {
                result = this.handleNumDays(data.days_ele, data.from_ele, data.to_ele);
            }else {
                result = this.handleDates(data.days_ele, data.from_ele, data.to_ele);
            }

            // Error handling.
            if(!result.success) {
                bootbox.alert(result.code+": "+result.message);
            }
        }
    },

    handleNumDays: function(days_ele, from_ele, to_ele) {
        // Error checking
        if(!days_ele || !to_ele) {
            // Silently return, we can't do anything without both, however it's not necessarily an error the user needs to see.
            return {'success': true};
        }

        // Gather date and build new date object.
        var new_date = Vtiger_Edit_Js.getDate(from_ele);
        var num_days = Number(days_ele.val());
        if(num_days > 0) {
            var new_days = Number(new_date.getDate()) + num_days;
            if(new_days < 0) {
                return {'success': false, 'code': 'Invalid Days', 'message': 'Invalid number of days used.'};
            }
            new_date.setDate(new_days);
        }else {
            days_ele.val(0);
        }

        // Set SIT to date.
        to_ele.DatePickerSetDate(new_date, true);
        to_ele.val(to_ele.DatePickerGetDate(true));
        return {'success': true};
    },

    handleDates: function(days_ele, from_ele, to_ele) {
        // Amount of milliseconds in a day.
        var one_day = 24*60*60*1000;

        var from_date = Vtiger_Edit_Js.getDate(from_ele);
        var to_date = Vtiger_Edit_Js.getDate(to_ele);
        if(!to_date || !from_date) {
            // Silently return, we can't do anything without both, however it's not necessarily an error the user needs to see.
            return {'success': true};
        }

        var from_time = from_date.getTime();
        var to_time = to_date.getTime();
        if(to_date < from_date) {
            return {'success': false, 'code': 'Invalid Dates', 'message': 'SIT to date cannot be less than SIT from date.'};
        }

        var new_days = Math.ceil(Math.abs((to_time - from_time) / one_day));
        days_ele.val(new_days);
        return {'success': true};
    },

    createDefaultDates: function() {
        var today = new Date();
        var ele = $('[name$="_date_in"]');
        ele.DatePickerSetDate(today, true);
        ele.val(ele.DatePickerGetDate(true));
    },

    rateOverride: function(loc, show) {
        // I... guess?
        if(loc == 'origin' && show)
        {
            $('[name="apply_sit_first_day_origin"]').prop('checked', true).trigger('change');
            $('[name="apply_sit_addl_day_origin"]').prop('checked', true).trigger('change');
            $('[name="apply_sit_cartage_origin"]').prop('checked', true).trigger('change');
        } else if(loc == 'dest' && show)
        {
            $('[name="apply_sit_first_day_dest"]').prop('checked', true).trigger('change');
            $('[name="apply_sit_addl_day_dest"]').prop('checked', true).trigger('change');
            $('[name="apply_sit_cartage_dest"]').prop('checked', true).trigger('change');
        }
        var buttons = $('.loadTariffSit');
        buttons.each(function() {
            var bLoc = $(this).data('location');
            if(bLoc == loc) {
                if(show) {
                    $(this).removeClass('hide');
                } else {
                    $(this).addClass('hide');
                }
            }
        });
    },

    loadButton: function(loc) {
        if ($('input[name="interstate_effective_date"]').val() === '') {
            bootbox.alert('Effective Date must be set to Load Tariff SIT');
            return;
        }
        if (loc == '') {
            bootbox.alert('Could not determine the location!');
            return;
        }

        var thisI = this;
        this.getTariffs().then(function(data) {
            thisI.showTariffDialog(data, loc).then(thisI.loadTariffDetails);
        });
    },

    getTariffs: function() {
        var deferred = $.Deferred();

        var currentTdElement = $('select[name="assigned_user_id"]').closest('td');
        var selected = currentTdElement.find('.result-selected').html();
        var optionId = currentTdElement.find('.result-selected').attr('id').split('_')[3];
        optionId--; //its off by one from normal because of the groups header
        var selectedId = currentTdElement.find('option:eq(' + optionId + ')').val();
        var assigned_to = selectedId;
        var dataUrl = "index.php?module=Estimates&action=GetSITTariffs&assigned_to=" + assigned_to;
        AppConnector.request(dataUrl).then(
            function(data) {
                deferred.resolve(data);
            },
            function(err) {
                deferred.reject(err);
            }
        );

        return deferred.promise();
    },

    showTariffDialog: function(data, loc) {
        if(!data.success) {
            return false;
        }
        
        var deferred = $.Deferred();
        var message = '<table class="massEditTable table table-bordered">' +
            '<tbody>' +
            '<tr>' +
            '<td class="fieldLabel" style="width:40%">' +
            '<label class="muted pull-right">Local Tariffs</label>' +
            '</td>' +
            '<td class="fieldValue">';
        message += data.result;
        message += '</td></tr></tbody></table>';

        bootbox.dialog({
            className: 'loadTariffSITContent',
            title: 'Load Tariff SIT',
            message: message,
            onEscape: function () {

            },
            buttons: {
                success: {
                    label: "Load",
                    className: "btn-success",
                    callback: function(result) {
                        deferred.resolve(loc, result);
                    }
                },
                cancel: {
                    label: "Cancel",
                    className: "btn-fail",
                    callback: function(result) {
                        deferred.reject(loc, result);
                    }
                }
            }
        });
        $('[name="local_tariff"]').chosen();

        return deferred.promise();
    },

    loadTariffDetails: function(loc) {
        var currentTdElement = $('div.loadTariffSITContent').find('select[name="local_tariff"]').closest('td');
        var selected = currentTdElement.find('.result-selected').html();
        var optionId = currentTdElement.find('.result-selected').attr('id').split('_')[3];
        var selectedId = currentTdElement.find('option:eq(' + optionId + ')').val();
        var effectiveDate = Vtiger_Edit_Js.getDate('interstate_effective_date');
        var loadTariffPackingUrl = "index.php?module=Estimates&action=LoadTariffSIT&tariffId=" + selectedId + "&effectiveDate=" + effectiveDate;
        AppConnector.request(loadTariffPackingUrl).then(
            function (data) {
                for (var key in data.result.sitItems) {
                    var node = $('input[name="' + key + '_' + loc + '_override"]');
                    node.val(data.result.sitItems[key]);
                }
            }
        );
    },

    registerDatesChangeEvent: function() {
        var thisI = this;
        $('.contentsDiv').on('value_change', '[name="sit_origin_pickup_date"],[name="sit_dest_delivery_date"],[name="sit_origin_date_in"],[name="sit_dest_date_in"]', function() {
            thisI.updateHandler($(this));
        });
    },

    registerDaysChangeEvent: function() {
        var thisI = this;
        $('.contentsDiv').on('value_change', '[name="sit_origin_number_days"],[name="sit_dest_number_days"]', function() {
            thisI.updateHandler($(this));
        });
    },

    registerRateOverrideEvent: function()
    {
        var thisI = this;
        $('.contentsDiv').on('value_change', '.sit_override', function (){
            var loc = $(this).data('location');
            var show = $(this).prop('checked');

            thisI.rateOverride(loc, show);
        });
    },

    registerLoadButtonEvent: function () {
        var thisI = this;
        $('.contentsDiv').on('click', '.loadTariffSit', function () {
            var loc = $(this).data('location');
            thisI.loadButton(loc);
        });
    },

    registerEvents: function() {
        this.registerDatesChangeEvent();
        this.registerDaysChangeEvent();
        this.registerRateOverrideEvent();
        this.registerLoadButtonEvent();

        if(!$('[name="record"]').val()) {
            this.createDefaultDates();
        }
    }
});
