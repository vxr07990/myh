Contracts_Edit_Js("Contracts_BaseSirva_Js",{
	currentInstance: false,
	getInstance : function() {
		return new Contracts_BaseSirva_Js();
	}
}, {
	registerReferenceSelectionEvent : function(container) {
		var thisInstance = this;

		jQuery('input[name="billing_contact"]', container).off(Vtiger_Edit_Js.referenceSelectionEvent);
		jQuery('input[name="billing_apn"]', container).on(Vtiger_Edit_Js.referenceSelectionEvent, function(e, data){
			thisInstance.referenceSelectionEventHandler(data, container);
		});

		jQuery('input[name="nat_account_no"]', container).on(Vtiger_Edit_Js.referenceSelectionEvent, function(e, data){
			if (data.apn !== 'undefined') {
				//now we should have the APN so we want to set that as the value of the display field.
				var dispElement = jQuery('#nat_account_no_display');
				jQuery(dispElement).val(data.apn);
			}
			//if we don't then it just displays the Account's name.
		});
	},

	registerChangeParent: function () {
		var thisInstance = this;
		jQuery('input:hidden[name="parent_contract"]').on(Vtiger_Edit_Js.referenceSelectionEvent + ' ' + Vtiger_Edit_Js.referenceDeSelectionEvent, function () {
			//console.dir('changed parent contract?');
			if (jQuery('input[name="parent_contract"]').val()) {
				thisInstance.setSubcontract();
				//console.dir(jQuery('input[name="parent_contract"]').val());
			} else {
				thisInstance.setParentContract();
				//console.dir(jQuery('input[name="parent_contract"]').val());
			}
		});
	},

	hideFields: {
		'contract_fields': [
			'parent_contract_display',
			'related_tariff_display'
		],
		'contract_blocks': [
			'billing_contact_display',
			'fixed_eff_date',
			'min_val_per_lb',
			'numAnnualRate',   //tbody id='annualRateIncreaseTable'
			'numMisc', 		//tbody id="qtyRateItemsTab"
		],
	},

	setParentContract: function () {
		var thisInstance = this;
		var contract = jQuery('input[name="contract_no"]');
		//contract.data('validationEngine', 'validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation,only[0-9a-zA-Z],maxSize[5]');
		//contract.attr('data-validation-engine', 'validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation,only[0-9a-zA-Z],maxSize[5]');
		contract.attr('data-validation-engine', 'validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation,custom[alphaFirstInteger],maxSize[5]');
		//@NOTE: This pulls from languages/en_us/Vtiger.php
		var message = app.vtranslate('JS_LBL_CONTRACTS_CONTRACTNUM_PARENT');
		var priorLabel = contract.closest('td').prev('td').children();
		priorLabel.html('<span class="redColor">*</span> ' + message);

		//remove things the parent doesn't need to use
		for (var index in thisInstance.hideFields['contract_blocks']) {
			var name = thisInstance.hideFields['contract_blocks'][index];
			//console.dir(name);
			var relatedContractBlock = jQuery('input[name="' + name + '"]');
			relatedContractBlock.closest('table').addClass('hide');
		}

		for (var index in thisInstance.hideFields['contract_fields']) {
			var name = thisInstance.hideFields['contract_fields'][index];
			var relatedContractField = jQuery('input[name="' + name + '"]');
			relatedContractField.closest('td').children().each(function () {
				jQuery(this).addClass('hide');
			});
			relatedContractField.closest('td').prev('td').children().each(function () {
				jQuery(this).addClass('hide');
			});
		}
	},

	setSubcontract : function() {
	    var thisInstance = this;
		var contract = jQuery('input[name="contract_no"]');
		//contract.data('validationEngine', 'validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation],custom[integer],maxSize[3]]');
		contract.attr('data-validation-engine', 'validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation],custom[integer],minSize[3],maxSize[3]]');
		//@NOTE: This pulls from languages/en_us/Vtiger.php
		var message = app.vtranslate('JS_LBL_CONTRACTS_CONTRACTNUM_SUB');
		var priorLabel = contract.closest('td').prev('td').children();
		priorLabel.html('<span class="redColor">*</span> ' + message);

		//@NOTE: perhaps unrequired now that thisInstance is set.
        if (
            typeof thisInstance != 'undefined' &&
            typeof thisInstance.hideFields != 'undefined'
        ) {
            //readd things the parent might have hidden doesn't need to use
            if (typeof thisInstance.hideFields['contract_blocks'] != 'undefined') {
                for (var index in thisInstance.hideFields['contract_blocks']) {
                    var name = thisInstance.hideFields['contract_blocks'][index];
                    //console.dir(name);
                    var relatedContractBlock = jQuery('input[name="' + name + '"]');
                    relatedContractBlock.closest('table').removeClass('hide');
                }
            }

            if (typeof thisInstance.hideFields['contract_fields'] != 'undefined') {
                for (var index in thisInstance.hideFields['contract_fields']) {
                    var name = thisInstance.hideFields['contract_fields'][index];
                    var relatedContractField = jQuery('input[name="' + name + '"]');
                    relatedContractField.closest('td').children().each(function () {
                        jQuery(this).removeClass('hide');
                    });
                    relatedContractField.closest('td').prev('td').children().each(function () {
                        jQuery(this).removeClass('hide');
                    });
                }
            }
        }
	},

	setNatAcctNbr : function() {
		var natAcctNbr = jQuery('input[name="nat_account_no"]');
		//soo I give in and just use attr to set the string I want.
		var priorLabel = natAcctNbr.closest('td').prev('td').children();
		var value = priorLabel.html();
		priorLabel.html('<span class="redColor">*</span> ' + value);
		natAcctNbr.attr('data-validation-engine', 'validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation],custom[integer],maxSize[8]]');
	},

	registerEvents : function(){
        var thisInstance = this;

        // NAT Account Number requirements setting.
        this.setNatAcctNbr();

        // Parent/Sub contract setting.
		this.registerChangeParent();
		var parentContractVal = jQuery('input:hidden[name="parent_contract"]').val();
		if (parentContractVal && parentContractVal != 0) {
			thisInstance.setSubcontract();
		} else {
			thisInstance.setParentContract();
		}
	}
});
