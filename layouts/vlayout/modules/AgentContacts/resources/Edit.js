Vtiger_Edit_Js("AgentContacts_Edit_Js",{},{
	
	
	registerEvents : function() {
		this._super();
		jQuery('#acontacts_agents_display').closest('tr').addClass('hide');
		
		this.initializeAddressAutofill('AgentContacts');
	}
});

 