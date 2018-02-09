Vtiger_Edit_Js("Stops_Edit_Js",{},{
	
	
	registerEvents : function() {
		this._super();
		jQuery('#stop_opp_display').closest('tr').addClass('hide');
		jQuery('#stop_est_display').closest('tr').addClass('hide');
		jQuery('#stop_order_display').closest('tr').addClass('hide');
		
		this.initializeAddressAutofill('Stops');
		this.initializeReverseZipAutoFill('Stops');
	}
});

 