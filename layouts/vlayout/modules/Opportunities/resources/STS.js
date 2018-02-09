Vtiger_Edit_Js('Opportunities_STS_Js', {
   //lolk 
}, {
    // Something or other
    split_booking: null,
    split_origin: null,
    brand: null,
    business_channel: null,
    record: null,
    nat_number: null,
    billing_apn: null,

    // Row information
    agmt_cod: null,
    cod_row: null,
    agmt_nat: null,
    nat_row: null,

    // Miscellaneous things.
    cbs_ind: null,
    cbs_contact: null,
    payment_field: null,
    payment_method: null,
    check_amount: null,
    credit_check: null,
    ref_number: null,

    edit_view: true,
    module: 'Opportunities',

    getFieldObject: function(name) {
        if(this.edit_view) {
            return $('[name="' + name + '"]');
        }
        else {
            return $('#' + this.module + '_detailView_fieldValue_' + name);
        }
    },

    getFieldValue: function(field) {
        if(this.edit_view) {
            return field.val();
        }else {
            return field.text().trim();
        }
    },

    setField: function(field, value) {
        if(typeof this[field] != 'object') {
            console.error("Attempted to set non-field element.");
        }
        else {
            if(typeof value == 'string') {
                value = this.getFieldObject(value);
            }
            this[field] = value;
        }
    },

    setFields: function(map) {
        for(var key in map) {
            this.setField(key, map[key]);
        }
    },

    handleBookingEvents: function() {
        // Gather elements for use.
        var booker_split = this.split_booking;
        var origin_split = this.split_origin;

        // Is NAVL flag since this should only be ran for NAVL/NVL.
        var is_navl = this.getFieldValue(this.brand) == 'NVL';

        // Trigger an event instead of calling the handler directly because it's not a broken out function andt
        // this Js is gross and needs redone.
        if(this.edit_view) {
            this.business_channel.trigger('change');
        }

        // Hide the cells to be reshown if applicable.
        Vtiger_Edit_Js.hideCell(booker_split);
        Vtiger_Edit_Js.hideCell(origin_split);

        // Handle the actual logic.
        // SIRVA no want this no more.
        //if(!this.bookEstimateAgentCheck() && is_navl) {
        // Since we are actually doing this logic, set the maps for what should be done per-tariff name.
        // Apparently split_booking is always shown if out_of_area is not.
        //var split_booking = ['UAS','400N Base','400N/104G','Max 4','NAVL-12A'];
        var out_of_area = ['Pricelock','Blue Express','Pricelock GRR','Truckload Express'];

        // Call the action to get the primary estimate's tariff.
        var params = {
            module: 'Estimates',
            action: 'GetPrimaryEstimateTariff',
            opportunity_id: this.record.val(),
        };
        AppConnector.request(params).then(function(data) {
            if (data.success) {
                if(out_of_area.indexOf(data.result) > -1) {
                    Vtiger_Edit_Js.showCell(origin_split);
                //} else if(split_booking.indexOf(data.result) > -1) {
                } else {
                    Vtiger_Edit_Js.showCell(booker_split);
                }
            }
        });
        //}
    },

    detailViewCheck: function() {
        var bookEstimateAgent = [];
        var originAgent = undefined;
        var estimateAgent = undefined;

        var is_name = false;
        var name_type = undefined;

        $('tr[class^="participantRow"]').not('.hide').find('span.value').each(function() {
            var text = $(this).text().trim();

            if(text == "Booking Agent" || text == "Origin Agent" || text == "Estimating Agent") {
                is_name = true;
                name_type = text;
            }else if(is_name) {
                switch(name_type) {
                case "Booking Agent":
                    bookEstimateAgent.push(text);
                    break;
                case "Origin Agent":
                    originAgent = text;
                    break;
                case "Estimating Agent":
                    estimateAgent = text;
                    break;
                }

                is_name = false;
                name_type = undefined;
            }
        });

        return {
            'agent_array': bookEstimateAgent,
            'origin_agent': originAgent,
            'estimate_agent': estimateAgent
        };
    },

    editViewCheck: function() {
        var bookEstimateAgent = [];
        var originAgent = undefined;
        var estimateAgent = undefined;

        $('tr[class^="participantRow"]').not('.hide').find('select').each(function() {
            // Gather the type of the current agent.
            var agent_type = $(this).find('option:selected').val();

            // This is absurdly gross and we need a separate attribute to give us easier access to the id/num of
            // the current participating agent.
            var agent_num = $(this).attr('name').split('_').slice(-1);
            var agent_id = $('input[name="agents_id_' + agent_num + '"]').val();

            if(agent_type == "Booking Agent") {
                // This gets the numerical id from $(this), then gets the agent id with the same
                // numerical value because the order of participating agents can change
                bookEstimateAgent.push(agent_id);
            }
            else if (agent_type == 'Origin Agent') {
                // Same concept as above, but Origin Agent
                originAgent = agent_id;
            }
            else if (agent_type == 'Estimating Agent') {
                // Had to add this in because it was merging the estimate and booking agent for some reason
                estimateAgent = agent_id;
            }
        });

        return {
            'agent_array': bookEstimateAgent,
            'origin_agent': originAgent,
            'estimate_agent': estimateAgent
        };
    },

    bookEstimateAgentCheck: function() {
        var result = undefined;
        if(this.edit_view) {
            result = this.editViewCheck();
        }
        else {
            result = this.detailViewCheck();
        }

        if(result.agent_array.indexOf(result.origin_agent) >= 0 ||
            (
                typeof result.origin_agent != 'undefined' &&
                result.agent_array.indexOf(result.estimate_agent) >= 0
            )
        ) {
            return true;
        }
        return false;
    },

    oppTypeChange: function() {
        if(this.getFieldValue(this.business_channel) != 'Consumer') {
            this.nat_row.removeClass('hide');
            this.cod_row.addClass('hide');
            this.nat_number.prop('readonly', false);
            this.billing_apn.prop('readonly', false);

            // Show cbs again.
            Vtiger_Edit_Js.setReadonly(this.cbs_ind, false);
        }
        else {
            this.nat_row.addClass('hide');
            this.cod_row.removeClass('hide');

            if(this.edit_view) {
                this.nat_number.prop('readonly', true).val('');
                this.billing_apn.prop('readonly', true).val('');
                this.cbs_ind.prop('checked', false).trigger('change');
                Vtiger_Edit_Js.setReadonly(this.cbs_ind,true);
            }
        }

        // I... okay.
        if(this.brand == 'AVL') {
            this.agmt_cod.find('option["value="CGP"]').attr('disabled', true);
        }
        else {
            this.agmt_nat.find('option[value="CGP"').attr('disabled', false);
        }
        this.agmt_cod.trigger('liszt:updated');
    },

    defaultSTSFields : function() {
        // Hide everything by default before setting up the listeners.
        this.nat_number.prop('readonly', true);
        this.billing_apn.prop('readonly', true);
        this.check_amount.prop('readonly', true);
        this.cod_row.addClass('hide');
        this.nat_row.addClass('hide');
        Vtiger_Edit_Js.setReadonly(this.cbs_contact,true);
        Vtiger_Edit_Js.setReadonly(this.cbs_ind,true);
    },

    // For the next two functions: The logic regarding the change handlers was simple and small enough I did not deem it
    // necessary to be broken out into a separate function within the class, however it needs to be called on pageload.
    registerCBSIndEvents: function(){
        //I mean, should probably break this out to the rebinding, but the thing is... this is the only logic they have!
        $('textarea[name="sts_response"]').prop('readonly', true);
        $('input[name="registration_date"]').prop('readonly', true);
        var thisI = this;

        var handler = function() {
            if(thisI.cbs_ind.is(':checked') == true){
                Vtiger_Edit_Js.setReadonly(thisI.cbs_contact,false);
                Vtiger_Edit_Js.setReadonly(thisI.credit_check,false);
            }
            else {
                Vtiger_Edit_Js.setReadonly(thisI.cbs_contact,true);
                Vtiger_Edit_Js.setReadonly(thisI.credit_check,true);
            }
        };

        this.cbs_ind.on('change', handler);
        handler();
    },
    registerCreditCheckEvents : function() {
        var thisI = this;
        var handler = function() {
            if(!thisI.credit_check.prop('checked')){
                Vtiger_Edit_Js.setReadonly(thisI.ref_number,true);
                thisI.check_amount.prop('readonly', true).val('');
            }
            else {
                Vtiger_Edit_Js.setReadonly(thisI.ref_number,false);
                thisI.check_amount.prop('readonly', false);
            }
        }

        this.credit_check.on('change', handler);
        handler();
    },

    registerBookingEvents: function(){
        var thisI = this;
        this.brand.on('value_change', function() {
            thisI.handleBookingEvents();
        });
        this.handleBookingEvents();
    },

    registerOppTypeEvent: function() {
        var thisI = this;
        this.business_channel.on('change', function() {
            thisI.oppTypeChange();
        });
        this.oppTypeChange();
    },

	registerPaymentTypeEvents : function() {
        var thisI = this;
        var handler = function() {
			if($(this).find('option:selected').val() == 'CHG') {
				thisI.payment_method.val('').attr('disabled', true).trigger('liszt:updated');
            }
            else {
				thisI.payment_method.attr('disabled', false).trigger('liszt:updated');
			}
        };
		this.payment_field.change(handler);
        handler();
	},

    forceSTSResponseSizing: function() {
        // IE Only
        var isIE11 = !!navigator.userAgent.match(/Trident.*rv\:11\./);
        var isLegacyIE = window.navigator.userAgent.indexOf("MSIE ") > 0;
        if(isIE11 || isLegacyIE) {
            // It's time to party
            var ele = $('[name="sts_response"]')
                .removeClass('ui-resizable')
                .css({"height": "60px"});
            ele.parent()
                .css({"height": "60px","width": "100%"});
        }
    },

    setEditViewFlag: function(isEditView) {
        this.edit_view = isEditView;
    },

    defaultRecordField: function() {
        if(this.edit_view) {
            this.record = $('[name="record"]');
        }
        else {
            this.record = $('#recordId');
        }
    },

    setAgreementRows: function() {
        this.cod_row = this.agmt_cod.closest('tr');
        this.nat_row = this.agmt_nat.closest('tr');
    },

    setupDefaultFields: function() {
        this.split_booking = this.getFieldObject('booker_split');
        this.split_origin = this.getFieldObject('origin_split');
        this.brand = this.getFieldObject('brand');
        this.business_channel = this.getFieldObject('business_channel');
        this.nat_number = this.getFieldObject('national_account_number');
        this.billing_apn = this.getFieldObject('billing_apn');

        this.agmt_cod = this.getFieldObject('agrmt_cod');
        this.agmt_nat = this.getFieldObject('agmt_id');

        this.cbs_ind = this.getFieldObject('cbs_ind');
        this.cbs_contact = this.getFieldObject('cbs_contact');
        this.payment_method = this.getFieldObject('payment_method');
        this.payment_field = this.getFieldObject('payment_type_sts');
        this.check_amount = this.getFieldObject('credit_check_amount');
        this.credit_check = this.getFieldObject('credit_check');
        this.ref_number = this.getFieldObject('ref_number');
        
        this.defaultRecordField();
        this.setAgreementRows();
    },

    registerEditEvents: function() {
        this.defaultSTSFields();
        this.forceSTSResponseSizing();

        this.registerPaymentTypeEvents();
        this.registerOppTypeEvent();
        this.registerBookingEvents();
        this.registerCBSIndEvents();
        this.registerCreditCheckEvents();
    },

    registerDetailEvents: function() {
        this.handleBookingEvents();
        this.oppTypeChange();
    },

    registerEvents: function(isEditView, fields) {
        this.setEditViewFlag(isEditView);
        this.setupDefaultFields();
        if(typeof fields != 'undefined') {
            this.setFields(fields);
        }

        if(isEditView) {
            this.registerEditEvents();
        }
        else {
            this.registerDetailEvents();
        }
    }
});
