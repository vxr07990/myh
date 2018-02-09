Vtiger_Edit_Js("Address_EditBlock_Js", {
	getInstance: function() {
		return new Address_EditBlock_Js();
	}
}, {
	orderinfo:false,
	registerRemoveAddresButtons : function(container){
		jQuery('.deleteAddressButton').off('click').on('click',container, function(){
			if(jQuery(this).siblings('input:hidden[name^="orderstask_address_"]').val() == ''){
				jQuery(this).closest('div.AddressesRecords').remove()
			} else{
				jQuery(this).closest('div.AddressesRecords').addClass('hide');
				jQuery(this).siblings('input:hidden[name^="address_deleted_"]').val('deleted');
			}
			var rowno=jQuery('div.AddressesRecords').length;
			jQuery('[name="OrdersTaskAddressesTable"]').find('[name="numAddress"]').val(rowno);
		});
	},
	registerAddAddressButtons : function() {
		var thisInstance = this;
		var container = jQuery('[name="OrdersTaskAddressesTable"]');
		container.find('.addAddress').off('click').on('click', function () {
			var rowno=jQuery('div.AddressesRecords').length;
			var viewParams = {
				"type": "POST",
				"url": 'index.php?module=OrdersTaskAddresses',
				"dataType": "html",
				"data": {
					'view': 'MassActionAjax',
					'mode': 'generateNewBlock',
					'rowno': rowno
				}
			};
			AppConnector.request(viewParams).then(
				function (data) {
					if (data) {
						var newAddress=jQuery(data);
						jQuery('div.OrdersTaskAddresses').append(newAddress);
						var newRowNo = jQuery('div.OrdersTaskAddresses .AddressesRecords').length;
						jQuery('[name="OrdersTaskAddressesTable"]').find('[name="numAddress"]').val(newRowNo);
						thisInstance.updateFieldNameForRow(newAddress,newRowNo);
						app.changeSelectElementView(newAddress);
						thisInstance.formatPhoneFieldOnChange();
						thisInstance.registerAutoFill(newRowNo);
						thisInstance.registerEventForAddressType(newAddress);
						thisInstance.registerRemoveAddresButtons(newAddress);
						var orderEle = jQuery('[name="ordersid"]');
						orderEle.trigger(Vtiger_Edit_Js.referenceSelectionEvent,{});
						thisInstance.registerEventChangeRelatedAddressField(newAddress);
					}
				}
			)
		});
	},
	registerEventChangeOrderField: function () {
		var thisInstance = this;
		var orderEle = jQuery('[name="ordersid"]');
		orderEle.on(Vtiger_Edit_Js.referenceSelectionEvent,function (e,v) {
			var  orderId = jQuery(this).val();
			if(orderId == undefined || orderId =='') return;
			var params = {
				module: 'OrdersTaskAddresses',
				action:'GetOrderInformations',
				order_id: orderId
			};
			AppConnector.request(params).then(
				function (data) {
					var results = data.result;
					thisInstance.orderinfo = data.result;
					results.extra_stops.push({'extrastops_name':'Destination Address'});
					results.extra_stops.push({'extrastops_name':'Origin Address'});
					results.extra_stops.reverse();
					jQuery('[name^="related_address_"]').each(function () {
						var currentAddress = jQuery(this).data('selected-value');
						var options = '<option value="">Select an Option</option>';
						jQuery.each(results.extra_stops,function (i,extraStopObj) {
							if(extraStopObj.extrastops_name == currentAddress){
								var selected = "selected";
							}else{
								var selected = "";
							}
							options +='<option value="'+extraStopObj.extrastops_name+'" '+selected+'>'+extraStopObj.extrastops_name+'</option>';
						});
						jQuery(this).html(options);
						jQuery(this).trigger('liszt:updated');
					});
				}
			);
		});
		orderEle.trigger(Vtiger_Edit_Js.referenceSelectionEvent,{});
	},
	updateFieldNameForRow:function (row,newRowNo) {
		row.find('tbody input,tbody select').not('input:hidden[name="popupReferenceModule"]').each(function(){
			var fieldInfo = jQuery(this).data('fieldinfo');
			if(fieldInfo != undefined){
				var name = jQuery(this).attr('name');
				var id = jQuery(this).attr('id');
				if(name.indexOf("_display") !=-1){
					var newName = fieldInfo.name+'_'+newRowNo+'_display';
					jQuery(this).attr('name', newName);
					jQuery(this).attr('id', newName);
				}else {
					jQuery(this).attr('name', fieldInfo.name+'_'+newRowNo);
					jQuery(this).attr('id', fieldInfo.name+'_'+newRowNo);
				}

			}
		});
	},
	updateFieldNameForAllRow:function (container) {
		var thisInstance = this;
		var rowno = 0;
		container.find('.AddressesRecords').each(function () {
			rowno++;
			thisInstance.updateFieldNameForRow(jQuery(this),rowno);
		});
	},
	initAutoCompleteAddress: function () {
		var thisInstance = this;
		jQuery('.AddressesRecords').each(function () {
			var elementRecord = jQuery(this).find('[name^="orderstask_address_"]');
			var name = elementRecord.attr('name');
			var arrName = name.split('orderstask_address_');
			var numRow = arrName[1];
			thisInstance.registerAutoFill(numRow);
		});

	},
	registerEventChangeRelatedAddressField: function (container) {
		var thisInstance = this;
		jQuery('select[name^="related_address_"]',container).on('change',function () {
			var currentVal = jQuery(this).val();
			jQuery(this).data('selected-value',currentVal);
			if(currentVal == undefined) return;
			var currentBlock = jQuery(this).closest('div.AddressesRecords');
			if(currentVal == 'Origin Address'){
				if(thisInstance.orderinfo.addresses != undefined){
					var addresses  = thisInstance.orderinfo.addresses;
					jQuery('[name^="address1_"]',currentBlock).val(addresses.origin_address1).prop('readonly',true);
					jQuery('[name^="address2_"]',currentBlock).val(addresses.origin_address2).prop('readonly',true);
					jQuery('[name^="city_"]',currentBlock).val(addresses.origin_city).prop('readonly',true);
					jQuery('[name^="state_"]',currentBlock).val(addresses.origin_state).prop('readonly',true);
					jQuery('[name^="zip_"]',currentBlock).val(addresses.origin_zip).prop('readonly',true);
					jQuery('[name^="phone1_"]',currentBlock).val(addresses.origin_phone1).prop('readonly',true);
					jQuery('[name^="phone2_"]',currentBlock).val(addresses.origin_phone2).prop('readonly',true);
					jQuery('[name^="description_"]',currentBlock).val(addresses.origin_description).attr('disabled',true).trigger('liszt:updated');
				}
			}else if(currentVal == 'Destination Address'){
				if(thisInstance.orderinfo.addresses != undefined){
					var addresses  = thisInstance.orderinfo.addresses;
					jQuery('[name^="address1_"]',currentBlock).val(addresses.destination_address1).prop('readonly',true);
					jQuery('[name^="address2_"]',currentBlock).val(addresses.destination_address2).prop('readonly',true);
					jQuery('[name^="city_"]',currentBlock).val(addresses.destination_city).prop('readonly',true);
					jQuery('[name^="state_"]',currentBlock).val(addresses.destination_state).prop('readonly',true);
					jQuery('[name^="zip_"]',currentBlock).val(addresses.destination_zip).prop('readonly',true);
					jQuery('[name^="phone1_"]',currentBlock).val(addresses.destination_phone1).prop('readonly',true);
					jQuery('[name^="phone2_"]',currentBlock).val(addresses.destination_phone2).prop('readonly',true);
					jQuery('[name^="description_"]',currentBlock).val(addresses.destination_description).attr('disabled',true).trigger('liszt:updated');
				}
			}else{
				if(thisInstance.orderinfo.extra_stops.length >0){
					var extra_stops = thisInstance.orderinfo.extra_stops;
					var selectedStop = false;
					jQuery.each(extra_stops,function (i,extrastopObj) {
						if(extrastopObj.extrastops_name == currentVal){
							selectedStop = extrastopObj;
							return false;
						}
					});
					if(selectedStop !== false){
						jQuery('[name^="address1_"]',currentBlock).val(selectedStop.extrastops_address1).prop('readonly',true);
						jQuery('[name^="address2_"]',currentBlock).val(selectedStop.extrastops_address2).prop('readonly',true);
						jQuery('[name^="city_"]',currentBlock).val(selectedStop.extrastops_city).prop('readonly',true);
						jQuery('[name^="state_"]',currentBlock).val(selectedStop.extrastops_state).prop('readonly',true);
						jQuery('[name^="zip_"]',currentBlock).val(selectedStop.extrastops_zip).prop('readonly',true);
						jQuery('[name^="phone1_"]',currentBlock).val(selectedStop.extrastops_phone1).prop('readonly',true);
						jQuery('[name^="phone2_"]',currentBlock).val(selectedStop.extrastops_phone2).prop('readonly',true);
						jQuery('[name^="description_"]',currentBlock).val(selectedStop.extrastops_description).attr('disabled',true).trigger('liszt:updated');
						jQuery('[name^="description_"]',currentBlock).trigger('liszt:updated');
					}
				}
			}
			if (currentVal ==''){
				thisInstance.makeReadOnlyAddressFields(currentBlock,false);
			}
		});
		jQuery('select[name^="related_address_"]',container).each(function () {
			var currentVal = jQuery(this).data('selected-value');
			var currentBlock = jQuery(this).closest('div.AddressesRecords');
			if (currentVal =='' || currentVal==undefined){
				thisInstance.makeReadOnlyAddressFields(currentBlock,false);
			}else{
				thisInstance.makeReadOnlyAddressFields(currentBlock,true);
			}
		});

	},
	makeReadOnlyAddressFields: function (currentBlock,readonly) {
		jQuery('[name^="address1_"]',currentBlock).prop('readonly',readonly);
		jQuery('[name^="address2_"]',currentBlock).prop('readonly',readonly);
		jQuery('[name^="city_"]',currentBlock).prop('readonly',readonly);
		jQuery('[name^="state_"]',currentBlock).prop('readonly',readonly);
		jQuery('[name^="zip_"]',currentBlock).prop('readonly',readonly);
		jQuery('[name^="phone1_"]',currentBlock).prop('readonly',readonly);
		jQuery('[name^="phone2_"]',currentBlock).prop('readonly',readonly);
		jQuery('[name^="description_"]',currentBlock).attr('disabled',readonly).trigger('liszt:updated');
	},
	registerAutoFill: function (num) {
		var thisInstance = this;
		var fieldNames = ['address1','city','state','zip'];
		jQuery.each(fieldNames,function (i,fieldName) {
			if(jQuery('#'+fieldName+'_'+num).length) {
				var autoObj = new google.maps.places.Autocomplete(
					(document.getElementById(fieldName+'_'+num)),
					{ types: ['geocode'] });

				google.maps.event.addListener(autoObj, 'place_changed', function() {
					thisInstance.fillAddress(autoObj, num);
					jQuery('[name^="address_type_'+num+'"]').trigger('change');
					jQuery('#'+fieldName+'_'+num).closest('td').find('.formError').remove();
				});
			}

		});
	},
	fillAddress : function(autocomplete, num) {
		var module = jQuery('#module').val();
		var thisInstance = this;
		var place = autocomplete.getPlace();
		var street_address = '';

		thisInstance.addressComponentForm = {
			street_address:'address1_'+num,
			locality: 'city_'+num,
			administrative_area_level_1: 'state_'+num,
			postal_code: 'zip_'+num
		};
		form = thisInstance.addressComponentForm;
		jQuery(':focus').trigger('blur');

		for (var component in form) {
			jQuery('#'+component).val('');
		}
		var hasAddress = false;
		var hasRoute = false;
		var hasCity = false;
		var hasState = false;
		var hasZip = false;

		if(typeof place.address_components != 'undefined') {
			for (var i=0; i<place.address_components.length; i++) {
				var addressType = place.address_components[i].types[0];
				if(addressType == 'street_number' && place.address_components[i][thisInstance.addressComponentForm[addressType]] != '') {
					hasAddress = true;
					street_address = place.address_components[i]['short_name'];

				} else if(addressType == 'route') {
					hasRoute = true;
					street_address = street_address + ' ' + place.address_components[i]['short_name'];

				} else if(thisInstance.addressComponentForm[addressType]) {
					hasCity = true;
					if(addressType == 'locality') {
						hasCity = true;
					} else if(addressType == 'administrative_area_level_1') {
						hasState = true;
					} else if(addressType == 'postal_code') {
						hasZip = true;
					}

					var val = place.address_components[i]['short_name'];

					if(val) {
						if(addressType == 'locality' && val.substring(0, 3) == 'St ') {
							val = 'Saint '+val.substring(3);
						}
					}
					if(jQuery('#'+form[addressType]).length) {
						var field = jQuery('#'+form[addressType]);
						field.val(val);
						field.trigger('propertychange');

						field.validationEngine('validate');
					}



				}
			}

			if(!hasAddress && !hasRoute && jQuery('#'+form['street_address']).val() != 'Will Advise') {
				/*
				 Removed below because it was removing the street address when the user enters a zip code and clicks
				 a result.
				 */
				//jQuery('#'+form['street_address']).val('');
			} else if(jQuery('#'+form.street_address).val() != 'Will Advise'){
				jQuery('#'+form.street_address).val(street_address);
			}
			if(!hasCity) {
				jQuery('#'+form.locality).val('');
			}
			if(!hasState) {
				jQuery('#'+form.administrative_area_level_1).val('');
			}
			if(!hasZip) {
				jQuery('#'+form.postal_code).val('');
			}

			//trigger Lookup Postal Code to appear after google api populates an address block
			if (hasState && hasCity && !hasZip) {
				var field2 = jQuery('#'+form.administrative_area_level_1);
				field2.trigger('change');
			}
		}
	},
	registerEventForAddressType: function (container) {
		var addressTypeListSingle = ['Origin', 'Destination', 'Customer Mailing', 'Customer Billing', 'Customer Shipping'];
		container.find('[name^="address_type_"]').on('change',function () {
			var currentEle = jQuery(this);
			var currentVal = currentEle.val();
			var mes = '';
			if(addressTypeListSingle.indexOf(currentVal) != -1){
				var currentBlock = jQuery(this).closest('div.AddressesRecords');
				currentBlock.siblings().find('[name^="address_type_"]').each(function () {
					if(jQuery(this).val() == currentVal) {
						mes = "Cannot have multiple "+currentVal+" Addresses within the table";
						return false;
					}
				});
			}
			if(mes != ''){
				currentEle.next().validationEngine('showPrompt',mes,'error',"topLeft",true);
				currentEle.val('');
				currentEle.trigger('liszt:updated');
			}else{
				currentEle.closest('td.fieldValue').find('.formErrorContent').remove();
				currentEle.closest('td.fieldValue').find('.formErrorArrow').remove();

			}
		});
	},
	registerEvents : function() {
		this.registerAddAddressButtons();
		var container = jQuery('div.OrdersTaskAddresses');
		this.updateFieldNameForAllRow(container);
		this.registerRemoveAddresButtons(container);
		this.initAutoCompleteAddress();
		this.registerEventForAddressType(container);
		this.formatPhoneFieldOnChange();
		this.registerEventChangeOrderField();
		this.registerEventChangeRelatedAddressField(container);
		jQuery('#EditView').on('submit', function(e){
			jQuery('[name^="description_"]').prop('disabled',false).trigger("liszt:updated");
		});
	},
});

jQuery(document).ready(function() {
	var instance = Address_EditBlock_Js.getInstance();
	instance.registerEvents();
});