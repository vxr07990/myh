// This was originally in 3 separate places, so I see no harm in making that not be the case anymore.
Vtiger_Edit_Js('Days_To_Move_Js', {
    getInstance: function() {
        return new Days_To_Move_Js();
    },
    I: function() {
        return this.getInstance();
    }
}, {
    // Allowing these to be editable so that discrepencies between modules can be accounted for.
    requestedMoveDateFieldname: 'preferred_pldate',
    expectedDeliveryDateFieldname: 'preferred_pddate',
    daysToMoveFieldname: 'days_to_move',
    instance: $('[name="instance"]').val(),

    calculate: function() {
        // Gather the correct dates for calculating days to move.
        var requestedDate = this.getRequestedDate();
        var expectedDate = this.getExpectedDate();

        // Calculate based on supplied dates.
        if(typeof requestedDate != 'undefined' && typeof expectedDate != 'undefined') {
            var oneDay = 24*60*60*1000;

            // I had to remove the subtracting of a day because it was causing issues and unintentionally adding a day.
            var diff = Math.round((expectedDate.getTime() - requestedDate.getTime()) / oneDay);

            // Instead of incrementing if you're past the start date, why don't we set it to 0.
            if(diff < 0) {
                diff = 0;
            }
            $('input[name="'+this.daysToMoveFieldname+'"]').val(diff);
        } else {
            $('input[name="'+this.daysToMoveFieldname+'"]').val('');
        }
    },

    getRequestedDate: function() {
        var date = null;
        if(this.instance == 'sirva') {
            // This isn't a pretty fix, but we need the Date object to 0 out the time, and leave the date, so...
            date = new Date();
            date = new Date(date.getFullYear(), date.getMonth(), date.getDate());
        }else {
            date = Vtiger_Edit_Js.getDate($('[name="'+this.requestedMoveDateFieldname+'"]'));
        }
        return date;
    },

    getExpectedDate: function() {
        var field = null;
        if(this.instance == 'sirva') {
            field = '[name="'+this.requestedMoveDateFieldname+'"]';
        }else {
            field = '[name="'+this.expectedDeliveryDateFieldname+'"]';
        }
        return Vtiger_Edit_Js.getDate($(field));
    },

    registerDaysToMoveEvent : function() {
        var thisInstance = this;
        $('input[name="'+this.daysToMoveFieldname+'"]').attr('readonly', true);

        // Gather fields to calculate on change.
        var changeFields = 'input[name="'+this.expectedDeliveryDateFieldname+'"]';
        if(this.instance == 'sirva') {
            changeFields += ', input[name="'+this.requestedMoveDateFieldname+'"]';
        }

        // Bind the event.
        $('.contentsDiv').on('value_change', changeFields, function() {
            // Doing this to force the "this" instance to be an instance of Days_To_Move_Js, and not the DOM object of the changed field.
            thisInstance.calculate();
        });
        this.calculate();
    },

    registerEvents: function() {
        this.registerDaysToMoveEvent();
    }
});
