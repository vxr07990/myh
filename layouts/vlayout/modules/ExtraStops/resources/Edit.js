Vtiger_Edit_Js("ExtraStops_Edit_Js", {
	getInstance: function() {
		return new ExtraStops_Edit_Js();
	}
}, {
	/*registerStopsAnimationEvent : function(){
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
			}
			var showHandler = function() {
				//console.dir('showing');
				bodyContents.show();
				app.cacheSet(module+'.'+blockId, 1);
				if(currentTarget.closest('div').parent().attr('id') == 'inline_content') {
					//closestBlock.siblings().find('tbody').hide('slow');
				}
			}
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
	},*/

	/*registerAddStopEvent : function(){
		var thisInstance = this;
		thisInstance.registerStopsAnimationEvent();
		var addStopHandler = function(){
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
			var editInstance = Vtiger_Edit_Js.getInstance();
			editInstance.registerBasicEvents(newStop);
		}
		jQuery('button[name="addStop"]').on('click', addStopHandler);
		jQuery('button[name="addStop2"]').on('click', addStopHandler);
	},*/

	/*registerStopTypeChange : function(){
		jQuery('.stopType').on('change',function(e){
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
			console.dir(sequenceNums);
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

	/*deleteStopEvent : function(){
		jQuery('.deleteStopButton').on('click', function(){
			var bodyContainer = jQuery(this).closest('tbody');
			var stopId = bodyContainer.find('input:hidden[name^="extrastops_id_"]').val();
			console.dir(stopId);
			if(stopId && stopId !='none'){
				bodyContainer.find('input:hidden[name^="extrastops_deleted"]').val('deleted');
				console.dir(bodyContainer.find('input:hidden[name^="extrastops_deleted"]'));
				bodyContainer.addClass('hide');
				console.dir('HIDDEN');
			}else {
				bodyContainer.remove();
				console.dir('REMOVED');
			}
		});
	},*/

	formatStopsPhoneNumbers: function() {
		jQuery('.phone-field').on('load, keyup', function () {
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

	registerChangeLocationType : function(){
		jQuery('select[name^="extrastops_type"]').on('change', function(){
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
	
	registerEvents : function() {
		this._super();
		this.initializeAddressAutofill('ExtraStops');
		this.initializeReverseZipAutoFill('ExtraStops');
		this.registerSirvaStopTypeClasses();
		//this.registerAddStopEvent();
		//this.registerStopsAnimationEvent();
		//this.deleteStopEvent();
		//this.formatStopsPhoneNumbers();
		this.registerChangeLocationType();
		//this.registerSirvaStopTypeChange();
		//this.registerStopTypeChange();
	},
});

jQuery(document).ready(function() {
	var instance = ExtraStops_Edit_Js.getInstance();
	instance.registerEvents();
});
