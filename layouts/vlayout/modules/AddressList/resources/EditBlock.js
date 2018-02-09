Vtiger_Edit_Js("AddressList_EditBlock_Js", {
	getInstance: function() {
		return new AddressList_EditBlock_Js();
	}
}, {
	registerRemoveAddressListButton : function(container){
		jQuery('.deleteAddressButton').off('click').on('click',container, function(){
			if(jQuery(this).siblings('input:hidden[name^="addresslistid_"]').val() == ''){
				jQuery(this).closest('div.AddressListRecords').remove()
			} else{
				jQuery(this).closest('div.AddressListRecords').addClass('hide');
				jQuery(this).siblings('input:hidden[name^="address_deleted_"]').val('deleted');
			}
			var rowno=jQuery('div.AddressListRecords').length;
			jQuery('[name="AddressListTable"]').find('[name="numAddress"]').val(rowno);
		});
	},
	registerCopyAddressListButton : function(container){
		var thisInstance = this;
		container.off('click').on( 'click', '.copyAddressButton', function(){
			var AddressListRecords = jQuery(this).closest('div.AddressListRecords');
			var copyRowNo = AddressListRecords.data('row-no');
			var rowno=jQuery('div.AddressListRecords').length;
			var copyData=AddressListRecords.find(':input').serialize();
			copyData = copyData + '&module=AddressList&view=MassActionAjax&mode=duplicateBlock&rowno='+rowno+'&copy_rowno='+copyRowNo;
			var viewParams = {
				"type": "POST",
				"url": 'index.php',
				"dataType": "html",
				"data": copyData
			};

			AppConnector.request(viewParams).then(
				function (data) {
					if (data) {
						var newItem=jQuery(data);
						jQuery('div.AddressList').append(newItem);
						var newRowNo = jQuery('div.AddressList .AddressListRecords').length;
						jQuery('[name="AddressListTable"]').find('[name="numAddress"]').val(newRowNo);
						thisInstance.updateFieldNameForRow(newItem,newRowNo);
						app.changeSelectElementView(newItem);
						thisInstance.formatPhoneFieldOnChange();
						thisInstance.makeLineItemsSortable();
						thisInstance.registerAutoFill(newRowNo);
						thisInstance.registerEventForAddressType(newItem);
						thisInstance.registerRemoveAddressListButton(newItem);
					}
				}
			)
		});
	},

	registerAddAddressListButtons : function() {
		var thisInstance = this;
		var container = jQuery('[name="AddressListTable"]');
		container.find('.addAddress').off('click').on('click', function () {
			var rowno=jQuery('div.AddressListRecords').length;
			var viewParams = {
				"type": "POST",
				"url": 'index.php?module=AddressList',
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
						var newAddressList=jQuery(data);
						jQuery('div.AddressList').append(newAddressList);
						var newRowNo = jQuery('div.AddressList .AddressListRecords').length;
						jQuery('[name="AddressListTable"]').find('[name="numAddress"]').val(newRowNo);
						thisInstance.updateFieldNameForRow(newAddressList,newRowNo);
						app.changeSelectElementView(newAddressList);
						thisInstance.formatPhoneFieldOnChange();
						thisInstance.makeLineItemsSortable();
						thisInstance.registerAutoFill(newRowNo);
						thisInstance.registerEventForAddressType(newAddressList);
						thisInstance.registerRemoveAddressListButton(newAddressList);

					}
				}
			)
		});
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
	updateAllFieldForAll:function (container) {
		var thisInstance = this;
		var rowno = 0;
		container.find('.AddressListRecords').each(function () {
			rowno++;
			thisInstance.updateFieldNameForRow(jQuery(this),rowno);
		});
	},
	registerEventForChangeBlockTitle: function (container) {
		var thisInstance = this;
		container.on("change",'[name^="address_type_"], [name^="city_"], [name^="state_"], [name^="zip_code_"]', function () {
			var AddressListRecords = jQuery(this).closest('div.AddressListRecords');
			var AddressListTitle = AddressListRecords.find('.AddressListTitle');
			var addressType = AddressListRecords.find('select[name^="address_type_"]').val();
			var city = AddressListRecords.find('[name^="city_"]').val();
			var state = AddressListRecords.find('[name^="state_"]').val();
			var zip = AddressListRecords.find('[name^="zip_code_"]').val();
			AddressListTitle.html('&nbsp;&nbsp;'+addressType +': '+city+', '+state+', '+zip)
		});
	},
	makeLineItemsSortable : function() {
		var thisInstance = this;
		var container = jQuery('.AddressList');
		container.sortable({
			items: '.AddressListRecords',
			delay: 150,
			revert: 0,
			cusor: '.dragAddressButton',
			handle: '.dragAddressButton',
			update: function (e, ui) {
				thisInstance.updateAllFieldForAll(container);
			}
		});
	},
	initAutoCompleteAddress: function () {
		var thisInstance = this;
		jQuery('.AddressListRecords').each(function () {
			var elementRecord = jQuery(this).find('[name^="addresslistid_"]');
			var name = elementRecord.attr('name');
			var arrName = name.split('addresslistid_');
			var numRow = arrName[1];
			thisInstance.registerAutoFill(numRow);
		});

	},
	registerAutoFill: function (num) {
		var thisInstance = this;
		var fieldNames = ['address1','address2','city','state','country','zip_code'];
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
			country: 'country_'+num,
			postal_code: 'zip_code_'+num
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
				var currentBlock = jQuery(this).closest('div.AddressListRecords');
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
		this.registerAddAddressListButtons();
		var container = jQuery('div.AddressList');
		this.updateAllFieldForAll(container);
		this.registerRemoveAddressListButton(container);
		this.registerCopyAddressListButton(container);
		this.registerEventForChangeBlockTitle(container);
		this.makeLineItemsSortable();
		this.initAutoCompleteAddress();
		this.registerEventForAddressType(container);

	},
});

jQuery(document).ready(function() {
	var instance = AddressList_EditBlock_Js.getInstance();
	instance.registerEvents();
});