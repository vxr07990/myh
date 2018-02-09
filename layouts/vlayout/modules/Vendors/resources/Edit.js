Vtiger_Edit_Js("Vendors_Edit_Js",{},{


	registerVendorNumberChange : function()
	{
		jQuery('.contentsDiv').on(Vtiger_Edit_Js.postReferenceSelectionEvent, '[name="vendors_vendornum"]', function(e,data){
			data = data['data'];
			var message = 'Would you like to load the remote data from the Vendor?';
			Vtiger_Helper_Js.showConfirmationBox({'message' : message}).then(
				function(){
					data = data[Object.keys(data)[0]];
					if(typeof data['info'] == 'object')
					{
						data = data['info'];
					}
					var map = {
						'vendorname': 'Vendor Name',
						'vendors_primcontact': 'Full Name',
						'origin_address1': 'Address 1',
						'origin_address2': 'Address 2',
						'origin_city': 'City',
						'origin_state': 'State',
						'origin_zip': 'Zip',
						'origin_country': 'Country',
						'phone': 'Primary Phone',
						'phone2': 'Secondary Phone',
						'email': 'Primary Email',
						'website': 'Website',
					};
					Vtiger_Edit_Js.populateData(data, map);
				},
				function(error, err) {
					//they pressed no don't populate the data.
				}
			);
		});
	},

	registerEvents : function() {
		this._super();
		
		this.initializeAddressAutofill('Vendors');
		this.registerVendorNumberChange();
	}
});

 