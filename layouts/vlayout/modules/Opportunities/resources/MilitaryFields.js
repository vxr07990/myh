$.Class("MilitaryField_Js", {
    generate: function(fields, op, onchange) {
        let generated = [];
        fields.forEach(function(field) {
            generated.push(new MilitaryField_Js().initialize(field, op, onchange));
        });

        return generated;
    }
}, {
    field: null,
    operation: null,
    onchange: false,
    values: null,

    initialize: function(field, op, onchange, values) {
        this.field = $('[name="' + field + '"]');
        this.operation = op || 'hide';
        this.onchange = onchange || false;
        this.values = values || {'active': null, 'inactive': null};

        // Initialize previous-value data in order to allow deactivation first, without wiping the value.
        this.field.data('previous-value', this.field.val());

        return this;
    },

    update: function(active) {
        if(active) {
            this.field.data('previous-value', this.field.val());
            switch(typeof this.values.active) {
                case 'string':
                    this.field.val(this.values.active);
                    break;
                case 'function':
                    this.field.val(this.values.active());
                    break;
                default:
                    return console.error('No value to set field ' + this.field.selector + ' to.');
            }

            if(this.field.length > 0 && this.field[0].nodeName == 'SELECT') {
                this.field.trigger('liszt:updated');
            }
        }
        else {
            switch(typeof this.values.inactive) {
                case 'string':
                    this.field.val(this.values.inactive);
                    break;
                case 'function':
                    this.field.val(this.values.inactive());
                    break;
                default:
                    this.field.val(this.field.data('previous-value'));
            }
        }
        // I really wish this wasn't necessary.
        this.field.trigger('change');
    },

    activate: function() {
        switch(this.operation) {
            case 'hide':
                Vtiger_Edit_Js.hideCell(this.field);
                break;
            case 'lock':
                Vtiger_Edit_Js.setReadonly(this.field, true);
                break;
            case 'update':
                this.update(true);
                break;
            default:
                console.error('No implemented operation set.');
                break;
        }
    },

    deactivate: function() {
        switch(this.operation) {
            case 'hide':
                Vtiger_Edit_Js.showCell(this.field);
                break;
            case 'lock':
                Vtiger_Edit_Js.setReadonly(this.field, false);
                break;
            case 'update':
                this.update(false);
            default:
                console.error('No implemented operation set.');
                break;
        }
    }
});

Vtiger_Edit_Js("Opportunities_MilitaryFields_Js", {
    singleton: null,

    getInstance: function() {
        if(this.singleton == null) {
            this.singleton = new Opportunities_MilitaryFields_Js();
        }

        return this.singleton;
    },
    I: function() {
        return this.getInstance();
    }
}, {
    fields: [],

    isMilitary: function() {
        return $('select[name="move_type"]').val() == 'Sirva Military';
    },

    addFieldsToHide: function() {
        let hide = MilitaryField_Js.generate(
            ['probability',
             'forecast_amount',
             'closingdate',
             'funded',
             'moving_a_vehicle',
             'promotion_code',
             'program_terms',
             'billing_type',
             'special_terms',
             'opp_type',
             'business_channel',
             'irr_charge',
             'out_of_area'],
        'hide', true);

        this.fields.concat(hide);
    },

    addFieldsToLock: function() {
        let offFields = MilitaryField_Js.generate(
            ['potentialname',
            'sales_stage',
            'opportunity_disposition',
            'opp_type',
            'move_type',
            'opportunity_detail_disposition',
            'opportunity_type'],
        'lock', false);

        let onFields = MilitaryField_Js.generate(
            ['leadsource',
            'shipper_type',
            'opportunity_type',
            'contact_id',
            'related_to',
            'deliver_date',
            'deliver_to_date'],
        'lock', true);

        this.fields.concat(offFields, onFields);
    },

    addFieldsToUpdate: function() {
        let fieldMap = {
            'sales_stage': {
                'active': 'Closed Won',
            },
            'shipper_type': {
                'active': 'MIL',
                'inactive': 'COD'
            },
            'opp_type': {
                'active': 'Military Award Survey'
            },
            'opportunity_disposition': {
                'active': 'Booked'
            },
            'opportunity_detail_disposition': {
                'active': 'Other'
            },
            'business_channel': {
                'active': 'Military'
            },
            'opportunity_type': {
                'active': 'Military Award Survey'
            },
            'leadsource': {
                'active': 'Sirva Military'
            }
        };

        for(let field in fieldMap) {
            this.fields.push(new MilitaryField_Js().initialize(field, 'update', true, fieldMap[field]));
        }
    },

    hideMilitaryBlockList: function() {
        return ['LBL_OPPORTUNITY_EMPLOYERASSISTING','LBL_LEADS_EMPLOYERASSISTING', 'LBL_QUOTES_CONTACTDETAILS'];
    },

    registerBusinessChannelEvent: function() {
        var thisI = this;
        $('select[name="move_type"]').on('change', function() {
            thisI.run(true);
        });
    },

    run: function(changed) {
        if($('input[name="lock_military_fields"][type="checkbox"]').attr('checked') || this.isMilitary()) {
            this.activateMilitaryMode(changed);
        }
        else{
            this.deactivateMilitaryMode(changed);
        }
    },

    activateMilitaryMode: function(changed) {
        this.fields.forEach(function(field) {
            if((changed && field.onchange) || !changed) {
                field.activate();
            }
        });

        //hide whole blocks
        this.hideMilitaryBlockList().forEach(function(field) {
            $('[name="'+field+'"]').addClass('hide');
        });
    },

    deactivateMilitaryMode: function(changed) {
        this.fields.forEach(function(field) {
            if((changed && field.onchange) || !changed) {
                field.deactivate();
            }
        });

        //hide whole blocks
        this.hideMilitaryBlockList().forEach(function(field) {
            $('[name="'+field+'"]').removeClass('hide');
        });
    },

    // Why am I doing this? Because Array.prototype.concat doesn't mutate the caller, and thats just unacceptable.
    reimplementFieldConcat: function() {
        this.fields.concat = function() {
            let called = this;
            // This can't be prettier (e.g. [...arguments], Array.from(arguments)) because IE is garbage.
            let args = [].slice.call(arguments);

            args.forEach(function(arg) {
                if(arg instanceof Array) {
                    arg.forEach(function(item) {
                        called.push(item);
                    });
                }
                else {
                    called.push(arg);
                }
            });

            return this;
        }
    },

    registerEvents: function() {
        this.reimplementFieldConcat();

        this.addFieldsToHide();
        this.addFieldsToLock();
        this.addFieldsToUpdate();

        console.log(this.isMilitary());
        if(this.isMilitary()) {
            this.run(false);
        }else {
            this.registerBusinessChannelEvent();
        }
    },
});
