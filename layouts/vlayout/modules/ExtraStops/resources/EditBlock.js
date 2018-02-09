Vtiger_EditBlock_Js("ExtraStops_EditBlock_Js", {
}, {

	registerAddStopEvent : function(){
		var thisInstance = this;
		/*var addStopHandler = function(){
			var defaultStop = jQuery('.defaultStop');
			var newStop = defaultStop.clone().removeClass('defaultStop hide').appendTo('table[name="extraStopsTable"]');
			newStop.find('.stopContent').removeClass('hide');
			thisInstance.registerStopsAnimationEvent();
			thisInstance.registerStopTypeChange();
			thisInstance.deleteStopEvent();
			var stopCounter = jQuery('#numStops');
			var stopCount = stopCounter.val();
			stopCount++;
			stopCounter.val(stopCount);
			newStop.find('.stopTitle').html('<b>&nbsp;&nbsp;&nbsp;Stop '+stopCount+'</b>');
			newStop.addClass('stop_'+stopCount);
			newStop.find('.sourceField').attr('name', 'extrastops_contact_'+stopCount);
			newStop.find('[name="extrastops_contact_display"]').attr('name', 'extrastops_contact_'+stopCount+'_display').attr('id', 'extrastops_contact_'+stopCount+'_display');
			newStop.find('input, select').not('.stopReference').not('.sourceField').not('input:hidden[name="popupReferenceModule"]').not('[name="extrastops_contact_'+stopCount+'_display"]').each(function(){
				jQuery(this).attr('name', jQuery(this).attr('name')+'_'+stopCount);
				jQuery(this).attr('id', jQuery(this).attr('id')+'_'+stopCount);
				if(jQuery(this).attr('name') == 'extrastops_date_'+stopCount){
					app.registerEventForDatePickerFields(jQuery('.dateField'), true);
				}
				if(jQuery(this).is('select')) {
					jQuery(this).addClass('chzn-select');
				}
			});
			newStop.find('.chzn-select').chosen();
			thisInstance.formatStopsPhoneNumbers();
			var editInstance = Vtiger_Edit_Js.getInstance();
			editInstance.registerBasicEvents(newStop);
			editInstance.initializeAddressAutofill('ExtraStops');
			editInstance.initializeReverseZipAutoFill('ExtraStops');
			return newStop;
		}*/
		jQuery('button[name="addStop"]').on('click', thisInstance.addStopHandler);
		jQuery('button[name="addStop2"]').on('click', thisInstance.addStopHandler);
	},

	addStopHandler : function(){
		var thisInstance = ExtraStops_EditBlock_Js.getInstance();
		var defaultStop = jQuery('.defaultStop');
		var newStop = defaultStop.clone().removeClass('defaultStop hide').appendTo('table[name="extraStopsTable"]');
		newStop.find('.stopContent').removeClass('hide');
		thisInstance.registerStopTypeChange();
		thisInstance.deleteStopEvent();
		thisInstance.registerStopsAnimationEvent();
		var stopCounter = jQuery('#numStops');
		var stopCount = stopCounter.val();
		stopCount++;
		stopCounter.val(stopCount);

		newStop.find('.blockToggle').attr('blocktoggleid',"extraStops" + stopCount);
		newStop.find('.stopContent').attr('blocktoggleid',"extraStops" + stopCount);
		newStop.find('.packing:not(.unpacking)').attr('blocktoggleid', "stopPacking" + stopCount);
		newStop.find('.unpacking').attr('blocktoggleid', "stopUnPacking" + stopCount);
		newStop.find('.packing:not(.unpacking)').find('.blockToggle').attr('blocktoggleid', "stopPacking" + stopCount);
		newStop.find('.unpacking').find('.blockToggle').attr('blocktoggleid', "stopUnPacking" + stopCount);

		newStop.find('.stopTitle').html('<b>&nbsp;&nbsp;&nbsp;Stop '+stopCount+'</b>');
		newStop.addClass('stop_'+stopCount);
		newStop.find('.sourceField').attr('name', 'extrastops_contact_'+stopCount);
		newStop.find('[name="extrastops_contact_display"]').attr('name', 'extrastops_contact_'+stopCount+'_display').attr('id', 'extrastops_contact_'+stopCount+'_display');
		newStop.find('input, select').not('.stopReference').not('.sourceField').not('input:hidden[name="popupReferenceModule"]').not('[name="extrastops_contact_'+stopCount+'_display"]').each(function(){
			var name = jQuery(this).attr('name');
			// don't know what else to do here
			var index = name.indexOf('0_');
			if(index !== -1)
			{
				// packing item
				var namePrefix = name.substr(0, index);
				var nameSuffix = name.substr(index + 1);
				name = namePrefix + stopCount + nameSuffix;
			} else {
				name = name + '_' + stopCount;
			}
			jQuery(this).attr('name', name);
			jQuery(this).attr('id', jQuery(this).attr('id')+'_'+stopCount);
			if(jQuery(this).attr('name') == 'extrastops_date_'+stopCount){
				app.registerEventForDatePickerFields(jQuery('.dateField'), true);
			}
			if(jQuery(this).is('select')) {
				jQuery(this).addClass('chzn-select');
			}
		});
		newStop.find('.chzn-select').chosen();
		thisInstance.formatStopsPhoneNumbers();
		var editInstance = Vtiger_Edit_Js.getInstance();
		editInstance.registerBasicEvents(newStop);
		thisInstance.registerAutoFill(stopCount);
		thisInstance.registerReverseAutoFill(stopCount);
		newStop.stopCount = stopCount;
		return newStop;
	},

	registerStopTypeChange : function(){
		jQuery('.stopType').on('change',function(e){
			var sequenceNums = [];
			if(jQuery(this).val()=='Origin'){
				jQuery('[name^="extrastops_sequence_"]').each(function(){
					if(jQuery(this).closest('tbody').find('select[name^="extrastops_type_"]').val() == 'Origin' && jQuery(this).val()){
						sequenceNums.push(jQuery(this).val());
					}
				});
			} else if(jQuery(this).val()=='Destination'){
				jQuery('[name^="extrastops_sequence_"]').each(function(){
					if(jQuery(this).closest('tbody').find('select[name^="extrastops_type_"]').val() == 'Destination' && jQuery(this).val()){
						sequenceNums.push(jQuery(this).val());
					}
				});
			} else{
				return false;
			}
			sequenceNums.sort(function(a, b) { return a-b; });
			var lowest = -1;
			for (i = 0; i < sequenceNums.length; i++) {
				if (sequenceNums[i] != i+1) {
					lowest = i+1;
					break;
				}
			}
			if (lowest == -1) {
				lowest = parseInt(sequenceNums[sequenceNums.length - 1]) + 1;
			}
			if(isNaN(lowest)){
				lowest = 1;
			}

			if(!jQuery(this).closest('tbody').find('[name^="extrastops_sequence_"]').val()){
				jQuery(this).closest('tbody').find('[name^="extrastops_sequence_"]').val(lowest).trigger("liszt:updated");
			}
		});
	},

	deleteStopEvent : function(){
		jQuery('.deleteStopButton').on('click', function(){
			var bodyContainer = jQuery(this).closest('tbody');
			var stopId = bodyContainer.find('input:hidden[name^="extrastops_id_"]').val();
			if(stopId && stopId !='none'){
				bodyContainer.find('input:hidden[name^="extrastops_deleted"]').val('deleted');
				bodyContainer.addClass('hide');
			}else {
				bodyContainer.remove();
			}
		});
	},

	formatStopsPhoneNumbers: function() {
		jQuery('.block_EXTRASTOPS_BLOCK_LABEL').on('load, keyup', 'input[name^="extrastops_phone"]', function () {
			var input = jQuery(this).val().replace(/\D/g, '');
			if (input.length == 10) {
				var phone = '(' + input.substr(0, 3) + ') ' + input.substr(3, 3) + '-' + input.substr(6, 4);
				jQuery(this).val(phone);
			} else if (jQuery(this).val().length == 7) {
				var phone = input.substr(0, 3) + '-' + input.substr(3, 4);
				jQuery(this).val(phone);
			}
		});
	},

	registerStopsAnimationEvent : function(){
		var thisInstance = this;
		//console.dir(detailContentsHolder.find('.blockToggle'));
		jQuery('.stopToggle').on('click',function(e){
			var currentTarget =  jQuery(e.currentTarget);
			var blockId = currentTarget.data('id');
			var closestBlock = currentTarget.closest('.stopBlock');
			var bodyContents = closestBlock.find('.stopContent');
			var data = currentTarget.data();
			var module = app.getModuleName();
			var hideHandler = function() {
				//console.dir('hiding');
				bodyContents.hide('slow');
				app.cacheSet(module+'.'+blockId, 0);
			};
			var showHandler = function() {
				//console.dir('showing');
				bodyContents.show();
				app.cacheSet(module+'.'+blockId, 1);
				if(currentTarget.closest('div').parent().attr('id') == 'inline_content') {
					//closestBlock.siblings().find('tbody').hide('slow');
				}
			};
			if(data.mode == 'show'){
				hideHandler();
				currentTarget.hide();
				closestBlock.find("[data-mode='hide']").show();
			}else{
				showHandler();
				currentTarget.hide();
				closestBlock.find("[data-mode='show']").show();
				if(currentTarget.closest('div').parent().attr('id') == 'inline_content') {
					closestBlock.siblings().each(function() {
						jQuery(this).find("[data-mode='hide']").show();
						jQuery(this).find("[data-mode='show']").hide();
						app.cacheSet(module+'.'+jQuery(this).find("[data-mode='show']").data('id'), 0);
					});
				}
			}

		});
	},

	registerChangeLocationType : function(){
		$(document).on('change','select[name^="extrastops_type"]',function(){
			var stopBlock = jQuery(this).closest("tbody");
			var locationTypeField = stopBlock.find('select[name^="extrastops_type"]');
			var locationTypeValue = locationTypeField.val();
			var sirvaStopTypeField = stopBlock.find('select[name^="extrastops_sirvastoptype"]');
			var selectedOption = sirvaStopTypeField.find('option:selected');
			//console.dir(stopBlock);
			//console.dir(locationTypeField);
			//console.dir(locationTypeValue);
			//console.dir(sirvaStopTypeField);
			//console.dir(selectedOption);
			if(locationTypeValue == 'Origin' || locationTypeValue == 'Extra Pickup'){
				//deselect current option if it is a destination stop type
				if(selectedOption.hasClass('destStopType')){
					selectedOption.prop('selected', false);
				}
				//reveal origin stop types, hide destination stop types
				sirvaStopTypeField.find('option').each(function(){
					if(jQuery(this).hasClass('destStopType') && !jQuery(this).hasClass('hide')){
						jQuery(this).addClass('hide');
						jQuery(this).prop('disabled', true);
					} else if(jQuery(this).hasClass('originStopType') && jQuery(this).hasClass('hide')){
						jQuery(this).removeClass('hide');
						jQuery(this).prop('disabled', false);
					}
				});
			} else if(locationTypeValue == 'Destination' || locationTypeValue == 'Extra Delivery'){
				//deselect current option if it is an origin stop type
				if(selectedOption.hasClass('originStopType')){
					selectedOption.prop('selected', false);
				}
				sirvaStopTypeField.find('option').each(function(){
					if(jQuery(this).hasClass('originStopType') && !jQuery(this).hasClass('hide')){
						jQuery(this).addClass('hide');
						jQuery(this).prop('disabled', true);
					} else if(jQuery(this).hasClass('destStopType') && jQuery(this).hasClass('hide')){
						jQuery(this).removeClass('hide');
						jQuery(this).prop('disabled', false);
					}
				});
			} else if(locationTypeValue == 'Extra Pickup'){

			} else if(locationTypeValue == 'Extra Delivery'){

			} else{
				sirvaStopTypeField.find('option').each(function(){
					if(jQuery(this).val() && !jQuery(this).hasClass('hide')){
						jQuery(this).addClass('hide');
						jQuery(this).prop('disabled', true);
					}
				});
			}
			sirvaStopTypeField.trigger('liszt:updated');
		});
		jQuery('select[name^="extrastops_type"]').each(function(){
			jQuery(this).trigger('change');
		})
	},

	registerSirvaStopTypeClasses : function(){
		jQuery('select[name^="extrastops_sirvastoptype"] > option').each(function(){
			optionVal = jQuery(this).val();
			switch(optionVal) {
				case 'XP1':
				case 'XP2':
				case 'XP3':
				case 'XP4':
				case 'XP5':
					//jQuery(this).addClass('XPStopType');
				case 'OSIT':
				case 'OSTG':
				case 'OPRM':
					jQuery(this).addClass('originStopType');
					break;
				case 'XD1':
				case 'XD2':
				case 'XD3':
				case 'XD4':
				case 'XD5':
					//jQuery(this).addClass('XDStopType');
				case 'DSIT':
				case 'DSTG':
				case 'DPRM':
					jQuery(this).addClass('destStopType');
					break;
				default:
					break;
			}
		});
	},

	/*registerSirvaStopTypeChange : function() {
		//jQuery('select[name^="extrastops_type_"]').on('change',function(e){
		//jQuery('.sirvaStopType').on('change',function(e){
		jQuery('table[name="LBL_OPPORTUNITY_EXTRASTOPS"]').on('change','[name^="extrastops_sirvastoptype"]',function(e){
			//console.dir("updating location type");
			var selectedValue = jQuery(this).val();
			//add switch to select location type aka stopType.
			var stopTypeSelect = jQuery(this).closest('tbody').find('select[name^="extrastops_type_"]');
			switch(selectedValue) {
				case 'XP1':
				case 'XP2':
				case 'XP3':
				case 'XP4':
				case 'XP5':
				case 'OSIT':
				case 'OSTG':
				case 'OPRM':
					stopTypeSelect.val('Origin');
					break;
				case 'XD1':
				case 'XD2':
				case 'XD3':
				case 'XD4':
				case 'XD5':
				case 'DSIT':
				case 'DSTG':
				case 'DPRM':
				default:
					stopTypeSelect.val('Destination');
					break;
			}
			stopTypeSelect.trigger('liszt:updated');
		});
	},*/

	/*registerStopTypeChange : function(){
		jQuery('.stopType').on('change',function(e){
			//console.dir("updating sequence");
			var sequenceNums = [];
			if(jQuery(this).val()=='Origin'){
				jQuery('input[name^="extrastops_sequence_"]').each(function(){
					if(jQuery(this).closest('tbody').find('select[name^="extrastops_type_"]').val() == 'Origin' && jQuery(this).val()){
						sequenceNums.push(jQuery(this).val());
					}
				});
			} else if(jQuery(this).val()=='Destination'){
				jQuery('input[name^="extrastops_sequence_"]').each(function(){
					if(jQuery(this).closest('tbody').find('select[name^="extrastops_type_"]').val() == 'Destination' && jQuery(this).val()){
						sequenceNums.push(jQuery(this).val());
					}
				});
			} else{
				return false;
			}
			sequenceNums.sort(function(a, b) { return a-b; });
			//console.dir(sequenceNums);
			var lowest = -1;
			for (i = 0; i < sequenceNums.length; i++) {
				if (sequenceNums[i] != i+1) {
					lowest = i+1;
					break;
				}
			}
			if (lowest == -1) {
				lowest = parseInt(sequenceNums[sequenceNums.length - 1]) + 1;
			}
			if(isNaN(lowest)){
				lowest = 1;
			}
			if(!jQuery(this).closest('tbody').find('input[name^="extrastops_sequence_"]').val()){
				jQuery(this).closest('tbody').find('input[name^="extrastops_sequence_"]').val(lowest);
			}
		});
	},*/
	registerShowStops: function() {
		var thisInstance = this;
		var module = jQuery('#module').val();
		jQuery('.stopToggle').click(function() {
			var numStops = jQuery(this).parent().find('[id^="extrastops_id_"]').attr('id');
			var num = numStops.substr(numStops.length - 1);
			thisInstance.registerAutoFill(num);
			thisInstance.registerReverseAutoFill(num);
		});
	},

	registerReverseAutoFill: function(num) {
		var thisInstance = this;
		var module = jQuery('#module').val();
		if(jQuery('#'+module+'_editView_fieldName_extrastops_zip_'+num).length) {
			jQuery('#' + module + '_editView_fieldName_extrastops_zip_'+num).after('<button id="stopZipButton_'+num+'" type="button" class="hide">'+app.vtranslate('Lookup Postal Code')+'</button>');
			thisInstance.zipStopsAutoFill(num);
		}
	},

	zipStopsAutoFill: function(num) {
		var module = jQuery('#module').val();
		var button = jQuery('#stopZipButton_'+num);
		var cityField = jQuery('#'+module+'_editView_fieldName_extrastops_city_'+num);
		var stateField = jQuery('#'+module+'_editView_fieldName_extrastops_state_'+num);
		var zipField = jQuery('#'+module+'_editView_fieldName_extrastops_zip_'+num);

		cityField.on('change',function() {
			if (cityField.val().length > 0 && stateField.val().length > 1) {
				if(button.hasClass('hide')) {
					button.before('<br />');
				}
				button.removeClass('hide');
			} else{
				button.addClass('hide');
				button.parent().find('br').remove();
			}
		});

		stateField.on('change',function() {
			if (cityField.val().length > 0 && stateField.val().length > 1) {
				if(button.hasClass('hide')) {
					button.before('<br />');
				}
				button.removeClass('hide');
			} else{
				button.addClass('hide');
				button.parent().find('br').remove();
			}
		});

		button.on('click', function(e) {
			var progressIndicatorElement = jQuery.progressIndicator({
				'message': 'Searching for Postal Code',
				'position': 'html',
				'blockInfo': {
					'enabled': true
				}
			});
			//console.dir('this is getting fired');
			// city gets the value from #Potentials_editView_fieldName_origin_city
			document.body.style.cursor='wait';
			var city = cityField.val();
			if(city.substring(0,3).toLowerCase() == 'st ') {
				city = 'Saint '+city.substring(3);
			} else if(city.substring(0,3).toLowerCase() == 'st.') {
				city = 'Saint '+city.substring(4);
			}
			//console.dir(city);
			// state gets the value from #Potentials_editView_fieldName_origin_state
			var state = stateField.val();
			//console.dir(city+' - '+state);
			//console.dir(state);
			//zipField.val('');

			var dataURL = "index.php?module=Potentials&action=ReverseAddressLookup&city="+ city +"&state=" + state;
			//console.dir(dataURL);
			AppConnector.request(dataURL).then(
				function(data){
					thisInstance.zipCodeArray = [];
					if(data.success) {
						for (var key in data.result.items) {
							thisInstance.zipCodeArray.push(data.result.items[key]);
						}
						if(thisInstance.zipCodeArray.length == 0) {
							alert('Invalid city/state combination entered!');
						}
						else{
							zipField.autocomplete({
								source: thisInstance.zipCodeArray,
								minLength: 0,
								select: function(event, ui) {
									var selectedValue = ui.item.value;
									zipField.trigger('blur');
									zipField.val(selectedValue);
									button.addClass('hide');
									button.parent().find('br').remove();
								}

							});
						}
						//console.dir(thisInstance.zipCodeArray);
						zipField.autocomplete('search', '');
						zipField.focus();
						jQuery(document.head).append('<style>.ui-autocomplete {height:100px !important;overflow:auto;}</style>');
						document.body.style.cursor='default';
						progressIndicatorElement.progressIndicator({
							'mode': 'hide'
						});
						/*button.addClass('hide');
						 button.parent().find('br').remove();*/
					}
					else{
						document.body.style.cursor='default';
						progressIndicatorElement.progressIndicator({
							'mode': 'hide'
						});
					}
				},
				function(err) {
					console.dir("ERROR: "+err);
					document.body.style.cursor='default';
					progressIndicatorElement.progressIndicator({
						'mode': 'hide'
					});
				}
			);

		});
	},

	bindGoogleSuggestions: function(num) {
		var module = jQuery('#module').val();
		var thisInstance = this;
		if(jQuery('#'+module+'_editView_fieldName_extrastops_address1_'+num).length) {
			var autocompleteStopAddress = new google.maps.places.Autocomplete(
				(document.getElementById(module + '_editView_fieldName_extrastops_address1_'+num)),
				{ types: ['geocode'] });

			google.maps.event.addListener(autocompleteStopAddress, 'place_changed', function() {
				thisInstance.stopsFillInAddress(module, autocompleteStopAddress, num);
				jQuery('#' + module + '_editView_fieldName_extrastops_address1_'+num).closest('td').find('.formError').remove();
			});
		}
		if(jQuery('#'+module+'_editView_fieldName_extrastops_address2_'+num).length) {
			var autocompleteStopAddress2 = new google.maps.places.Autocomplete(
				(document.getElementById(module + '_editView_fieldName_extrastops_address2_'+num)),
				{ types: ['geocode'] });

			google.maps.event.addListener(autocompleteStopAddress2, 'place_changed', function() {
				thisInstance.stopsFillInAddress(module, autocompleteStopAddress2, num);
				jQuery('#' + module + '_editView_fieldName_extrastops_address2_'+num).closest('td').find('.formError').remove();
			});
		}
		if(jQuery('#'+module+'_editView_fieldName_extrastops_city_'+num).length) {
			var autocompleteStopCity = new google.maps.places.Autocomplete(
				(document.getElementById(module + '_editView_fieldName_extrastops_city_'+num)),
				{ types: ['geocode'] });

			google.maps.event.addListener(autocompleteStopCity, 'place_changed', function() {
				thisInstance.stopsFillInAddress(module, autocompleteStopCity, num);
				jQuery('#' + module + '_editView_fieldName_extrastops_city_'+num).closest('td').find('.formError').remove();
			});
		}
		if(jQuery('#'+module+'_editView_fieldName_extrastops_state_'+num).length) {
			var autocompleteStopState = new google.maps.places.Autocomplete(
				(document.getElementById(module + '_editView_fieldName_extrastops_state_'+num)),
				{ types: ['geocode'] });

			google.maps.event.addListener(autocompleteStopState, 'place_changed', function() {
				thisInstance.stopsFillInAddress(module, autocompleteStopState, num);
				jQuery('#' + module + '_editView_fieldName_extrastops_state_'+num).closest('td').find('.formError').remove();
			});
		}
		if(jQuery('#'+module+'_editView_fieldName_extrastops_zip_'+num).length) {
			var autocompleteStopZip = new google.maps.places.Autocomplete(
				(document.getElementById(module + '_editView_fieldName_extrastops_zip_'+num)),
				{ types: ['geocode'] });

			google.maps.event.addListener(autocompleteStopZip, 'place_changed', function() {
				thisInstance.stopsFillInAddress(module, autocompleteStopZip, num);
				jQuery('#' + module + '_editView_fieldName_extrastops_zip_'+num).closest('td').find('.formError').remove();
			});

            jQuery('#'+module+'_editView_fieldName_extrastops_zip_'+num).after('<button name="originZipButton_'+num+'" type="button" class="hide">'+app.vtranslate('Zip Code Lookup')+'</button>');
		}
		if(jQuery('#'+module+'_editView_fieldName_extrastops_country_'+num).length) {
			var autocompleteStopCountry = new google.maps.places.Autocomplete(
				(document.getElementById(module + '_editView_fieldName_extrastops_country_'+num)),
				{ types: ['geocode'] });

			google.maps.event.addListener(autocompleteStopCountry, 'place_changed', function() {
				thisInstance.stopsFillInAddress(module, autocompleteStopCountry, num);
				jQuery('#' + module + '_editView_fieldName_extrastops_country_'+num).closest('td').find('.formError').remove();
			});
		}

        thisInstance.registerZipLookupButton(module, num);
	},

    registerZipLookupButton : function(mod, num) {
        var thisInstance = this;
		var supportedCountries = ['united states', 'canada', 'usa', 'us', 'ca'];

        // Important information
        var button = $('[name="originZipButton_'+num+'"]');
        var city = '#'+mod+'_editView_fieldName_extrastops_city_'+num;
        var state = '#' + mod + '_editView_fieldName_extrastops_state_'+num;
        var country = '#'+mod+'_editView_fieldName_extrastops_country_'+num;
        var zip = '#'+mod+'_editView_fieldName_extrastops_zip_'+num;

        if($('[name="instance"]').val() != 'graebel') {
            $('.contentsDiv').on('change', city+','+state+','+country , function() {
                if($(city).val() && $(state).val()) {
                    $(button).removeClass('hide');
                }else {
                    $(button).addClass('hide');
                }
            });
        }
        button.on('click', function() {
            var progressIndicatorElement = jQuery.progressIndicator({
                'message': 'Searching for Postal Code',
                'position': 'html',
                'blockInfo': {
                    'enabled': true
                }
            });
			document.body.style.cursor='wait';
			var cityVal = $(city).val();
			if(cityVal.substring(0,3).toLowerCase() == 'st ') {
				cityVal = 'Saint '+cityVal.substring(3);
			} else if(city.substring(0,3).toLowerCase() == 'st.') {
				cityVal = 'Saint '+cityVal.substring(4);
			}
			var stateVal = $(state).val();

			var dataURL = "index.php?module=Potentials&action=ReverseAddressLookup&city="+ cityVal +"&state=" + stateVal;
			//console.dir(dataURL);
			AppConnector.request(dataURL).then(
				function(data){
					thisInstance.zipCodeArray = [];
					if(data.success) {
						for (var key in data.result.items) {
							thisInstance.zipCodeArray.push(data.result.items[key]);
						}
						if(thisInstance.zipCodeArray.length == 0) {
							alert('Invalid city/state combination entered!');
						}
                        else{
                            $(zip).autocomplete({
                                source: thisInstance.zipCodeArray,
                                minLength: 0,
                                select: function(event, ui) {
                                    var selectedValue = ui.item.value;
                                    $(zip).trigger('blur');
                                    $(zip).val(selectedValue);
                                    button.addClass('hide');
                                    button.parent().find('br').remove();
                                    if(typeof data.result.country != 'undefined') {
                                        $(country).val(data.result.country);
                                    }
                                }

                            });
                        }
						$(zip).autocomplete('search', '');
						$(zip).focus();
						jQuery(document.head).append('<style>.ui-autocomplete {height:100px !important;overflow:auto;}</style>');
                        document.body.style.cursor='default';
                        progressIndicatorElement.progressIndicator({
                            'mode': 'hide'
                        });
					}
                    else{
                        document.body.style.cursor='default';
                        progressIndicatorElement.progressIndicator({
                            'mode': 'hide'
                        });
                    }
				},
				function(err) {
					console.dir("ERROR: "+err);
                    document.body.style.cursor='default';
                    progressIndicatorElement.progressIndicator({
                        'mode': 'hide'
                    });
				}
			);
        });
    },

	stopsFillInAddress : function(formType, autocomplete, num) {
		var module = jQuery('#module').val();
		var thisInstance = this;
		var place = autocomplete.getPlace();
		var street_address = '';
		var form = '';

		thisInstance.extraStopsComponentForm = {
			street_address: module + '_editView_fieldName_extrastops_address1_'+num,
			locality: module + '_editView_fieldName_extrastops_city_'+num,
			administrative_area_level_1: module + '_editView_fieldName_extrastops_state_'+num,
			country: module + '_editView_fieldName_extrastops_country_'+num,
			postal_code: module + '_editView_fieldName_extrastops_zip_'+num
		};
		form = thisInstance.extraStopsComponentForm;
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
				if(addressType == 'street_number' && place.address_components[i][thisInstance.extraStopsComponentForm[addressType]] != '') {
					hasAddress = true;
					street_address = place.address_components[i]['short_name'];

				} else if(addressType == 'route') {
					hasRoute = true;
					street_address = street_address + ' ' + place.address_components[i]['short_name'];

				} else if(thisInstance.extraStopsComponentForm[addressType]) {
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
				// only need to trigger one of the possible fields
				// and only if there's a city and state and no zip

				//var field = jQuery('#'+form['locality']);
				//field.trigger('change');
				var field2 = jQuery('#'+form.administrative_area_level_1);
				field2.trigger('change');
			}
		}
	},

    populateDefaultAddress : function(inputName) {
        console.dir(jQuery('input[name="' + inputName + '"]').val()); //MM HERE
        if (jQuery('input[name="' + inputName + '"]').val() == '') {
            jQuery('input[name="' + inputName + '"]').val('Will Advise');
        }
    },

    registerAutoFill: function(num) {
        var inputName = 'extrastops_address1_';
        while (num > 0) {
            this.bindGoogleSuggestions(num);
            this.populateDefaultAddress(inputName+num);
            this.preventEmptyAddress(inputName+num);
            num--;
        }
    },

    preventEmptyAddress : function(inputName){
        jQuery('input[name="'+inputName+'"]').on('blur', function(){
            if (jQuery('input[name="' + inputName + '"]').val() == '') {
                jQuery('input[name="' + inputName + '"]').val('Will Advise');
            }
        });
    },


	registerBasicEvents : function(container) {
		//_super.registerBasicEvents(container);
		var recordNumber = container.attr('guestid');
		this.registerAutoFill(recordNumber);
        this.registerAddressLines(recordNumber);
        this.populateDefaultAddress(recordNumber);
	},

	registerSequenceChangeEvent : function(){
		jQuery(document).on('change','select[name^="extrastops_sequence"]',function(e){
			var currentTarget=jQuery(e.currentTarget);
			var currentName=currentTarget.attr('name');
			var selectedValue=currentTarget.val();
			jQuery.each(jQuery(document).find('select[name^="extrastops_sequence"]'), function (idx, elm) {
				var elemt=jQuery(elm);
				if(jQuery(elemt).attr('name') != currentName) {
					elemt.find('option[value="'+selectedValue+'"]').remove();
					//elemt.append(jQuery('<option>', {value:preVal, text:preVal}));
					elemt.trigger("liszt:updated");
				}
			})
		});
		jQuery(document).find('select[name^="extrastops_sequence"]').trigger('change');
	},

	registerEvents : function() {
		this.registerSirvaStopTypeClasses();
		//this.registerAddStopEvent();
		//this.registerStopsAnimationEvent();
		//this.deleteStopEvent();
		//this.registerShowStops();
		this.formatStopsPhoneNumbers();
		this.registerChangeLocationType();
		this.registerSequenceChangeEvent();
		//this.registerSirvaStopTypeChange();
		//this.registerStopTypeChange();
	},
});

jQuery(document).ready(function() {
	var instance = new ExtraStops_EditBlock_Js();
	instance.registerEvents();
});
