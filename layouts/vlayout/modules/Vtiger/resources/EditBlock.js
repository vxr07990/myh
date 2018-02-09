	jQuery.Class("Vtiger_EditBlock_Js", {
	getInstance: function() {
		return new Vtiger_EditBlock_Js();
	}
}, {
	guestAddRecordEvent : function(guestModule){
		thisInstance = this;
		var addButton1 = jQuery('button[name="add' + guestModule + '"]');
		var addButton2 = jQuery('button[name="add' + guestModule + '2"]');
		if (typeof addButton1 != 'undefined' && addButton1.length > 0) {
			addButton1[0].guestModule = guestModule;
			addButton1[0].instance = thisInstance;
			addButton1.on('click', thisInstance.addGuestRecord);
		}
		if (typeof addButton2 != 'undefined' && addButton2.length > 0) {
			addButton2[0].guestModule = guestModule;
			addButton2[0].instance = thisInstance;
			addButton2.on('click', thisInstance.addGuestRecord);
		}

		//ok yes I know this is gross but I had less than an hour to make this work
		if(guestModule = 'WFLineItems' && jQuery('[name="module"]').val() == 'WFOperationsTasks') {
			var optasks = WFOperationsTasks_Edit_Js.getInstance();
			optasks.registerRebind();
		}
	},

	guestDeleteRecordEvent : function(guestModule){
		var thisInstance = this;
		jQuery('.delete' + guestModule).on('click', function(){
			var bodyContainer = jQuery(this).closest('tbody');
			var recordId = bodyContainer.find('input:hidden[name^="' + guestModule.toLowerCase() + '_id_"]').val();
			if(recordId && recordId != 'none'){
				console.dir('HIDDEN');
				bodyContainer.find('input:hidden[name^="' + guestModule.toLowerCase() + '_deleted"]').val('deleted');
				bodyContainer.addClass('hide');
			} else {
				console.dir('removed from DOM');
				bodyContainer.remove();
			}
		});
	},

	addGuestRecord : function(inst, module){
		var thisInstance = this.instance;
		if(typeof thisInstance == 'undefined')
		{
			thisInstance = inst;
		}
		var guestModule = this.guestModule;
		if(typeof guestModule == 'undefined')
		{
			guestModule = module;
		}
		var defaultRecordFields = jQuery('.default' + guestModule);
		var newRecordFields = defaultRecordFields.clone().removeClass('default' + guestModule + ' hide').appendTo('table[name="' + guestModule + 'Table"]');
		newRecordFields.find('.' + guestModule + 'Content').removeClass('hide');
		var recordCounter = jQuery('#num' + guestModule);
		var recordCount = recordCounter.val();
		recordCount++;
		recordCounter.val(recordCount);
		newRecordFields.addClass(guestModule + '_' + recordCount);
		newRecordFields.attr('guestid', recordCount);
		newRecordFields.find('.sourceField').each(function() {
			oldName = jQuery(this).attr('name')
			newName = jQuery(this).attr('name')+'_' + recordCount;
			jQuery(this).attr('name', newName);
			newRecordFields.find('[name="'+oldName+'_display"]').attr('name', newName + '_display').attr('id', newName + '_display').addClass('referenceDisplay');
		});

		newRecordFields.find('div').each(function() {
		    if (jQuery(this).hasClass('select2')) {
		    	/* this is ... it shouldn't be in the .tpl to get here, but that would be much more work to case.
				var defaultId = jQuery(this).attr('id');
				if (defaultId !== undefined) {
					jQuery(this).attr('id', defaultId + '_' + recordCount);
				}
				*/
				jQuery(this).remove();
			}
		});

		newRecordFields.find('input, select').not('.referenceDisplay').not('.sourceField').not('input:hidden[name="popupReferenceModule"]').each(function(){
			var defaultName = jQuery(this).attr('name');
            var defaultId = jQuery(this).attr('id');

			if (defaultName !== undefined) {
				var x = defaultName.match(/\[\]/);
				if (x) {
					jQuery(this).attr('name', defaultName.replace('[]','_'+recordCount+'[]'));
				} else {
					var index = defaultName.search(/\d/);
					var secondIndex = defaultName.indexOf('_', index);
					if(index > 0 && guestModule != 'ExtraStops') {
						if(secondIndex > 0) {
							var secondNumber = defaultName.substr(secondIndex + 1);
						}
						defaultName = defaultName.substr(0, index) + recordCount;
						if(typeof secondNumber != 'undefined')
						{
							defaultName = defaultName + '_' + secondNumber;
						}
						jQuery(this).attr('name', defaultName);
					} else {
						jQuery(this).attr('name', defaultName + '_' + recordCount);
					}
				}
				// console.dir(jQuery(this).attr('name'));
			}

            if (defaultId !== undefined) {
				jQuery(this).attr('id', defaultId+'_'+recordCount);
			}

			if(jQuery(this).is('select')) {
			    if (!jQuery(this).hasClass('select2')) {
					jQuery(this).addClass('chzn-select');
				}
			}
		});

		//Register date fields
		app.registerEventForDatePickerFields(jQuery('.dateField'), true);

		//Register the chosen fields
		newRecordFields.find('select.chzn-select').chosen();

		//register the select2 fields
		app.showSelect2ElementView(newRecordFields.find('select.select2'));

		var editInstance = Vtiger_Edit_Js.getInstance();
		editInstance.registerBasicEvents(newRecordFields);
		thisInstance.guestDeleteRecordEvent(guestModule);
		newRecordFields.recordCount = recordCount;
		try {
			eval('check = new ' + guestModule+'_EditBlock_Js();');
				if (typeof check != 'undefined') {
						check.registerBasicEvents(newRecordFields);
					} else {
				}
			} catch (errMT) {
			//do nothing this is fine
		}
		jQuery(this).closest('table').trigger({
			type:"addRecord",
			newRow:newRecordFields
		});
		return newRecordFields;
	},

	setEventsForGuestBlocks : function() {
		thisInstance = this;
		jQuery('.guestModule:not(.guestEventsDone)').each(function () {
			console.dir('setting events for: ' + jQuery(this).val())
			thisInstance.guestAddRecordEvent(jQuery(this).val());
			thisInstance.guestDeleteRecordEvent(jQuery(this).val());
			jQuery(this).addClass('guestEventsDone');

			var v = jQuery(this).val();
			try {
				eval('check = new ' + v+ '_EditBlock_Js();');
				if (typeof check != 'undefined') {
					jQuery('.' + v + 'Block').each(function(){
						check.registerBasicEvents(jQuery(this));
					});
				} else {
				}
			} catch (errMT) {
				//do nothing this is fine
			}
		});
	},

	registerEvents : function() {
		//register events
		this.setEventsForGuestBlocks();
	},
});

jQuery(document).ready(function() {
	var instance = new Vtiger_EditBlock_Js();
	instance.registerEvents();
});
