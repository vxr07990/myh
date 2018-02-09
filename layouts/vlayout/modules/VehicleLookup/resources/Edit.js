Vtiger_Edit_Js("VehicleLookup_Edit_Js", {
	getInstance: function() {
		return new VehicleLookup_Edit_Js();
	}
}, {

	registerAddChecklistItem : function() {
		var thisInstance = this;
		var addChecklistItem = function() {
			var defaultItem = jQuery('.defaultChecklistItem');
			var newItem = defaultItem.clone().removeClass('defaultChecklistItem hide').appendTo('table[name="preShipChecklistTable"]');
			thisInstance.registerDeleteChecklistItemEvent();
			var itemCount = jQuery('.checklistRow').length;
			newItem.find('input, select').each(function() {
				jQuery(this).attr('name', jQuery(this).attr('name')+'_'+itemCount);
				jQuery(this).attr('id', jQuery(this).attr('id')+'_'+itemCount);
			});
		};
	},
	
	registerDeleteChecklistItemEvent : function() {
		jQuery('.deleteChecklistItemButton').off('click').on('click', function() {
			var rowContainer = jQuery(this).closest('tr');
			var itemId = rowContainer.find('input:hidden[name^="checklistItemId_"]').val();
			if(itemId && itemId != 'default') {
				/*var dataURL = "index.php?module=VehicleLookup&action=DeleteChecklistItem&itemid="+itemId;
				AppConnector.request(dataURL).then(
					function(data) {
						if(data.success) {
							console.dir('success');
						} else {
							console.dir(data.error.message);
						}
					},
					function(error) {
						console.dir(error);
					}
				);*/
				jQuery('table[name="preShipChecklistTable"]').append('<input type="hidden" name="removeChecklistItem_'+itemId+'" value="'+itemId+'" />');
			}
			rowContainer.remove();
		});
	},
	
	registerLookupByVIN : function() {
		var thisInstance = this;
		jQuery('.contentsDiv').on('click', 'button[id^="lookupVin"]', function(e) {
			var currentTarget = jQuery(e.currentTarget);
			var bodyContainer = currentTarget.closest('tbody');
			var vin = bodyContainer.find('input[name^="vehiclelookup_vin_"]').val();
			if(vin.length == 0) {
				return;
			}
			
			var dataURL = 'index.php?module=VehicleLookup&action=LookupVIN&vin='+vin;
			AppConnector.request(dataURL).then(
				function(data) {
					if(data.success) {
						console.dir(data.result);
						if(data.result.isTruck)
						{
							bodyContainer.find('input[name^="vehiclelookup_make"]').val(data.result['Make']);
							bodyContainer.find('input[name^="vehiclelookup_model"]').val(data.result['Model']);
							bodyContainer.find('input[name^="vehiclelookup_year"]').val(data.result['Model Year']);
							bodyContainer.find('select[name^="vehiclelookup_type"]').val('Truck').trigger('liszt:updated').trigger('change');
						} else {
							bodyContainer.find('input[name^="vehiclelookup_make"]').val(data.result.make.name);
							bodyContainer.find('input[name^="vehiclelookup_model"]').val(data.result.model.name);
							bodyContainer.find('input[name^="vehiclelookup_year"]').val(data.result.years[0].year);
							bodyContainer.find('select[name^="vehiclelookup_type"]').next().find('li:contains("' + data.result.categories.vehicleType + '")').trigger('mouseup');
						}
						//bodyContainer.find('select[id^="vehicle_type"]').find('option').removeAttr('selected');
						//bodyContainer.find('select[id^="vehicle_type"]').find('option[value="'+data.result.categories.vehicleType+'"]').attr('selected', true);
					} else {
						bootbox.alert(data.error.code + ': ' + data.error.message);
					}
				},
				function(error) {
					console.dir(error);
				}
			);
		});
	},
	
	registerEvents : function() {
		//this.registerAddVehicle();
		this.registerAddChecklistItem();
		//this.registerDeleteVehicleEvent();
		this.registerDeleteChecklistItemEvent();
		this.registerLookupByVIN();
	},
});

jQuery(document).ready(function() {
	var instance = VehicleLookup_Edit_Js.getInstance();
	instance.registerEvents();
});