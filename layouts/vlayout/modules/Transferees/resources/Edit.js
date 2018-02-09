Vtiger_Edit_Js("Transferees_Edit_Js",{},{
	
	
	registerEvents : function() {
		this._super();
		jQuery('#transferees_orders_display').closest('tr').addClass('hide');
		
		this.initializeAddressAutofill('Transferees');
		//this.initializeAddressAutofill('TransfereesShipping');
	}
});

 