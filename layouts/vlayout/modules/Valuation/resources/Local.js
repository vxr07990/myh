Vtiger_Edit_Js('Valuation_Local_Js', {
    active: null,
    getInstance: function() {
        if(this.active == null) {
            this.active = new Valuation_Local_Js();
        }
        return this.active;
    },
    I: function() {
        return this.getInstance();
    }
}, {
    /*
     * CLASS VARIABLES
     */

    gathered: false,
    mulipliers: null,
    amounts: null,
    weight: null,

    instance: $('[name="instance"]').val(),

    /*
     * HELPER FUNCTIONS
     */

    getFields: function(key) {
        var fields = {};

        if(typeof key == 'number') {
            // Get Multiplier field.
            fields['multiplier'] = this.multipliers[key];

            // Get Amount field.
            fields['amount'] = this.amounts[key];
        }else {
            return false;
        }

        return fields;
    },

    /*
     * LOGIC FUNCTIONS
     */

    calculateValuation: function(mult, round) {
        if(!this.gathered) {
            return console.error("Cannot calculate valuation until fields have been gathered.");
        }

        var weight = this.weight.val();

        var amount = Math.ceil(mult * weight);
        if(round) {
            amount = Math.ceil((amount / 100) * 100);
        }
        return amount;
    },

    enforceMinimums: function() {
        var thisI = this;
        this.amounts.forEach(function(ele, i) {
            if(!thisI.enforceMinimum(i)) {
                console.error("Local Valuation: There was an error enforcing minimum valuation.");
            }
        });
    },

    enforceMinimum: function(key) {
        var fields = this.getFields(key);
        if(!fields) {
            return false;
        }

        var valuation = this.calculateValuation(fields.multiplier.val());
        if(fields.amount.val() < valuation) {
            this.updateField(fields.amount, valuation);
        }

        return true;
    },

    updateAmounts: function() {
        var thisI = this;
        this.multipliers.forEach(function(ele, i) {
            if(!thisI.updateAmount(i)) {
                console.error("Local Valuation: There was an error updating valuation amounts.");
            }
        });
    },

    updateAmount: function(key) {
        var fields = this.getFields(key);
        if(!fields) {
            return false;
        }

        var valuation = this.calculateValuation(fields.multiplier.val());
        this.updateField(fields.amount, valuation);

        return true;
    },

    updateField: function(ele, val) {
        if(ele.hasClass('localValuationPick')) {
            this.updatePicklist(ele, val);
        }else {
            this.updateManual(ele, val);
        }
    },

    updatePicklist: function(ele, val, set) {
        if(typeof set == 'undefined') {
            set = true;
        }

        var compareAsNumbers = function(a, b) {
            return Number(a) - Number(b);
        }

        // This is a terrible way to do this, but the picklist is not guarenteed to be in numerical order.
        // So here we gooooooo...
        var valAmounts = [];
        var minAmount = -1;

        // Gather up all the amounts and sort them.
        ele.find('option').each(function() {
            // Avoid adding "Select An Option"
            if($(this).val() !== '') {
                valAmounts.push($(this).val());
                $(this).removeAttr('selected disabled');
            }
        });
        valAmounts = valAmounts.sort(compareAsNumbers);

        // Find the minimum valuation amount in this picklist.
        for(var i = 0; i < valAmounts.length; i++) {
            var amtEle = ele.find('option[value="'+valAmounts[i]+'"]');
            if(compareAsNumbers(valAmounts[i],val) >= 0) {
                if(minAmount == -1) minAmount = valAmounts[i];
            }else {
                amtEle.attr('disabled','disabled');
            }
        }
        // Fallback to largest value.
        if(minAmount == -1) {
            minAmount = valAmounts[valAmounts.length-1];
            ele.find('option[value="'+valAmounts[i]+'"]').removeAttr('disabled');
        }

        // And set it.
        if(set) {
            ele.val(minAmount);
            ele.trigger('change');
        }
        ele.trigger('liszt:updated');
    },

    updateManual: function(ele, val) {
        // Cam ne
        ele.val(val);
    },

    /*
     * CLASS INITIALIZATION FUNCTIONS
     */

    gatherFields: function() {
        var mult_eles = $('[name^="Multiplier"]');

        // Generate the arrays of jQuery elements in order.
        // This allows for the multiplier and respective amount have the same array ids.
        // Not using the standard jQuery list of eles for consistency.
        var mults = [];
        var amts = [];

        // Gather elements.
        mult_eles.each(function() {
            mults.push($(this));
            amts.push($(this).closest('tr').prev().find('[name^="Amount"]'));
        }).get();

        // Any extra logic can be placed here.
        // So lonely...

        // Set the classwide objects so that things can be properly bound.
        this.multipliers = mults;
        this.amounts = amts;
        this.weight = $('[name="local_weight"]');

        // Set flag in order to allow registration functions to bind without fear of failing.
        this.gathered = true;
    },

    /*
     * EVENT LISTENER REGISTRATION FUNCTIONS
     */

    registerEnforcedMinimum: function() {
        if(!this.gathered) {
            return console.error("Cannot bind events until fields have been gathered.");
        }
        var thisI = this;

        this.amounts.forEach(function(ele) {
            ele.on('value_change', function() {
                thisI.enforceMinimums();
            })
        });
    },

    registerAmountUpdate: function() {
        if(!this.gathered) {
            return console.error("Cannot bind events until fields have been gathered.");
        }
        var thisI = this;

        this.multipliers.forEach(function(ele) {
            ele.on('value_change', function() {
                thisI.updateAmounts();
            });
        });

        this.weight.on('value_change', function() {
            thisI.updateAmounts();
        });

        //thisI.updateAmounts();
    },

    registerEvents: function() {
        this.gatherFields();
        this.registerAmountUpdate();
        this.registerEnforcedMinimum();
    }
});
