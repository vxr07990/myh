Vtiger_Edit_Js("VanlineContacts_Edit_Js",{},{
	
	
	registerEvents : function() {
		this._super();
		jQuery('#vcontacts_vanlines_display').closest('tr').addClass('hide');
		this.initializeAddressAutofill('VanlineContacts');
		
	}
});

 