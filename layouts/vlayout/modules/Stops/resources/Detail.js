Vtiger_Detail_Js("Stops_Detail_Js",{},{
		registerEvents : function() {
		this._super();
		jQuery('#Stops_detailView_fieldValue_stop_opp').closest('tr').addClass('hide');
		jQuery('#Stops_detailView_fieldValue_stop_est').closest('tr').addClass('hide');
		jQuery('#Stops_detailView_fieldValue_stop_order').closest('tr').addClass('hide');
		
		this.initializeAddressAutofill('Stops');
	}
	
});