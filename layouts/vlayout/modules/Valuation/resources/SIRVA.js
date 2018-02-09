// I hate all of this code but at least now it's marginally more readable.
// On top of that, now it's not in a 2000+ line JS monstrosity.

Valuation_Common_Js("Valuation_SIRVA_Js", {
    instance: null,

    getInstance: function() {
        if(!this.instance) {
            this.instance = new Valuation_SIRVA_Js();
        }

        return this.instance;
    },

    I: function() {
        return this.getInstance();
    }
}, {
    picklistActive: false,
    picklistOptions: [6, 10, 15, 20, 25, 30, 35, 40, 50, 60, 75, 100, 125, 150, 175, 200, 225, 250],

    visibilityRules: function(isEditView) {
        this._super();
    },

    loadOptions: function (brand)
    {
        var valuation = jQuery('[name="valuation_deductible"]');
        if(valuation.length == 0)
        {
            return;
        }
        var selectedIndex = valuation[0].selectedIndex;
        if(brand == 'NVL')
        {
            Vtiger_Edit_Js.setPicklistOptions('valuation_deductible',
                ['60¢ /lb.','MVP - $0','MVP - $250','MVP - $500']);
        } else //AVL
        {
            Vtiger_Edit_Js.setPicklistOptions('valuation_deductible',
                ['60¢ /lb.','ECP - $0','ECP - $250','ECP - $500']);
        }
        if(selectedIndex == 0 && valuation.data('selected-value'))
        {
            valuation.val(valuation.data('selected-value'));
        } else {
            valuation[0].selectedIndex = selectedIndex;
        }
        valuation.trigger('liszt:updated').trigger('change');
    },

    calculate: function() {
        var weight = $('[name="weight"]');
        if(weight.length == 0)
        {
            return;
        }
        weight = weight.val().replace(',','');

        var factor = Valuation_Common_Js.getValuationWeightFactor();
        if(typeof factor == 'undefined')
        {
            factor = 6;
        }
        return Math.ceil(weight * factor / 100)*100;
    },

    update: function(softUpdate) {
        var tariff = $('#effective_tariff_custom_type').val();

        var oldVal = $('[name="valuation_amount"]').val();
        var newVal = this.calculate();

        // If it's a soft update, take the higher value.
        if(softUpdate && oldVal > newVal) {
            newVal = oldVal;
        }


        // first determine if we should use a picklist
        if(Estimates_Customer_Js.getTariffProperty('valPicklist', tariff)) {
            this.updatePicklist(newVal);
        } else {
            this.updateManual(newVal);
        }
    },

    updatePicklist: function(newVal) {
        var picklist = $('.valuationPick');
        var manual = $('.valuationManual');

        this.picklistActive = true;
        var options = {};
        var selected = false;
        var min = Math.max(this.calculate(), 6000);
        var actual = Math.max(newVal, 6000);
        for(var i = 0; i < this.picklistOptions.length ; ++i)
        {
            var val = this.picklistOptions[i] * 1000;
            if(min > val)
            {
                continue;
            }else if(!selected && newVal && val >= newVal) {
                selected = val;
            }
            options[val] = val.toFixed(2);
        }
        if(actual > 250000) {
            selected = "Over 250000";
        }else if(!selected) {
            selected = actual;
        }
        options['Over 250000'] = 'Over 250,000.00';
        var picklist_data = $('[name="valuation_amount_pick"]');
        Vtiger_Edit_Js.setPicklistOptions(picklist_data, options);
        Vtiger_Edit_Js.setValue(picklist_data, selected);
        picklist.removeClass('hide');

        var over = (selected == 'Over 250000');
        if(over) {
            manual.removeClass('hide');
        } else {
            manual.addClass('hide');
        }
        this.picklistChange(picklist_data, over);
    },

    updateManual: function(newVal) {
        var manual_data = $('[name="valuation_amount"]');
        this.picklistActive = false;

        $('.valuationManual').removeClass('hide');
        $('.valuationPick').addClass('hide');
        $('[name="valuation_amount"]').val(newVal).removeAttr('min');

        this.manualChange(manual_data);
    },

    picklistChange: function(ele, overMaxOption) {
        if(!this.picklistActive) {
            return;
        }
        if(typeof overMaxOption == 'undefined') {
            overMaxOption = ele.val() == "Over 250000";
        }

        var valEle = $('[name="valuation_amount"]');

        var val = ele.val();
        var valAmt = (valEle.val()/100)*100;
        if(overMaxOption)
        {
            $('.valuationManual').removeClass('hide');
            valEle.attr('min',250000).val(Math.max(250000,valAmt));
        } else {
            $('.valuationManual').addClass('hide');
            valEle.removeAttr('min').val(val);
        }

        this.manualChange(valEle);
    },

    manualChange: function(ele) {
        ele.val(100 * Math.ceil(ele.val() / 100));
    },

    registerUpdateEvent: function () {
        var thisI = this;
        $('.contentsDiv').on('value_change', '[name="move_type"],[name="shipper_type"],[name="effective_tariff"],[name="weight"],[name="min_declared_value_mult"]', function() {
            thisI.update();
        });
    },

    registerPicklistEvent: function() {
        var thisI = this;
        $('.contentsDiv').on('value_change', '[name="valuation_amount_pick"]', function() {
            thisI.picklistChange($(this));
        });
    },

    registerManualEvent: function() {
        var thisI = this;
        $('.contentsDiv').on('value_change', '[name="valuation_amount"]', function() {
            thisI.manualChange($(this));
        });
    },

    registerDeductibleEvent: function() {
        var thisI = this;
        var handler = function() {
            var ele = $(this);
            // Need to rerun this stuff because the hide rules blank out values on hide.
            if(ele.val() != '60¢ /lb.') {
                // This is a soft update because this is only present to enforce a minimum, and to keep the field from wiping when hidden.
                thisI.update(true);
            }
        };
        $('.contentsDiv').on('value_change', '[name="valuation_deductible"]', handler);
    },

    ensureSIRVAJsLoaded: function() {
        if(typeof Estimates_Customer_Js.getTariffProperty == 'function') {
            return true;
        }else {
            console.error("Attempted to load SIRVA valuation without SIRVA customer JS loaded.");
            return false;
        }
    },

    registerEvents: function() {
        if(this.ensureSIRVAJsLoaded()) {
            this.registerDeductibleEvent();
            this.registerUpdateEvent();
            this.registerPicklistEvent();
            this.registerManualEvent();
        }
    }
});
