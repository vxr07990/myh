Vtiger_Detail_Js("Transferees_Detail_Js",{},{
	
	registerEvents : function() {
		this._super();
		
		this.initializeAddressAutofill('Transferees');
	}
});