Accounts_Edit_Js("Accounts_SalesPerson_Js",{
    currentInstance: false,
    getInstance : function() {
        return new Accounts_SalesPerson_Js();
    }
},{

    registerAddSalesPerson : function(){
        console.dir('registerAddAnnualRate ACTIVATED');
        console.dir(jQuery('#addRateIncrease'));
        var salesPersonBtn = jQuery('.addSalesPerson');

        //handler to add new annual rate increase row
        var newAnnualRow = function(){
            var newRow = jQuery('.defaultSalesPerson').clone(true,true);
            var sequence = parseInt(jQuery('input:hidden[name="numSalesPerson"]').val());
            sequence++;
            console.dir(sequence);
            jQuery('input:hidden[name="numSalesPersonCount"]').val(sequence);
            newRow.find('select').addClass('selections').prop('required', true).css({'width': '100%'});
            newRow.find('input').prop('required', true);
            newRow.removeClass('defaultSalesPerson').removeClass('hide').removeClass('chzn-done').addClass('newRow');
            newRow.appendTo(jQuery(this).closest('table'));
            app.registerEventForDatePickerFields();

            jQuery('.selections').chosen();
        }

        salesPersonBtn.on('click', newAnnualRow);
    },

    deleteSalesPersonEvent : function(){
        var thisInstance = this;
        jQuery('.deleteSalesPersonButton').on('click', function(e) {
            var currentRow = jQuery(this).closest('tr');
            currentRow.remove();
        });
    },

    registerEvents : function(){
        this.deleteSalesPersonEvent();
        this.registerAddSalesPerson();
        app.registerEventForDatePickerFields();
    }

});