/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

jQuery.Class("Vtiger_Edit_Js",{

	//Event that will triggered when reference field is selected
	referenceSelectionEvent : 'Vtiger.Reference.Selection',

	//Event that will triggered when reference field is selected
	referenceDeSelectionEvent : 'Vtiger.Reference.DeSelection',

	//Event that will triggered before saving the record
	recordPreSave : 'Vtiger.Record.PreSave',

	refrenceMultiSelectionEvent : 'Vtiger.MultiReference.Selection',

	preReferencePopUpOpenEvent : 'Vtiger.Referece.Popup.Pre',

	editInstance : false,

    postReferenceSelectionEvent: 'Vtiger.PostReference.Selection',

	/**
	 * Function to get Instance by name
	 * @params moduleName:-- Name of the module to create instance
	 */
	getInstanceByModuleName : function(moduleName){
		if(typeof moduleName == "undefined"){
			moduleName = app.getModuleName();
		}
		var parentModule = app.getParentModuleName();
		if(parentModule == 'Settings'){
			var moduleClassName = parentModule+"_"+moduleName+"_Edit_Js";
			if(typeof window[moduleClassName] == 'undefined'){
				moduleClassName = moduleName+"_Edit_Js";
			}
			var fallbackClassName = parentModule+"_Vtiger_Edit_Js";
			if(typeof window[fallbackClassName] == 'undefined') {
				fallbackClassName = "Vtiger_Edit_Js";
			}
		} else {
			moduleClassName = moduleName+"_Edit_Js";
			fallbackClassName = "Vtiger_Edit_Js";
		}
		if(moduleClassName == app.currentPageControllerClassName
			|| fallbackClassName == app.currentPageControllerClassName)
		{
			return app.currentPageController;
		}
		if(typeof window[moduleClassName] != 'undefined'){
			var instance = new window[moduleClassName]();
		}else{
			var instance = new window[fallbackClassName]();
		}
		return instance;
	},
  getAddress: function(selector, addr1, city, state, zip, suffix)
  {
    if(typeof suffix == 'undefined')
    {
      suffix = '';
    }
    addr1 = jQuery('[name="'+addr1+suffix+'"]').val().trim();
    city = jQuery('[name="'+city+suffix+'"]').val().trim();
    state = jQuery('[name="'+state+suffix+'"]').val().trim();
    zip = jQuery('[name="'+zip+suffix+'"]').val().trim();
    var res = addr1;
    if(city.length > 0)
    {
      if(res.length > 0){
        res += ', ';
      }
      res += city;
    }
    if(state.length > 0)
    {
      if(res.length > 0){
        res += ', ';
      }
      res += state;
    }
    if(zip.length > 0)
    {
      if(res.length > 0){
        res += ', ';
      }
      res += zip;
    }
    return res;
  },

	getInstance: function(){
		if(Vtiger_Edit_Js.editInstance == false){
			var instance = Vtiger_Edit_Js.getInstanceByModuleName();
			Vtiger_Edit_Js.editInstance = instance;
			return instance;
		}
		return Vtiger_Edit_Js.editInstance;
	},

	I: function(){
		return Vtiger_Edit_Js.getInstance();
	},

	setReadonly : function(name, val)
	{
		// allow passing jQuery selections
		if(typeof name == 'string') {
			var field = jQuery('[name="' + name + '"]');
		} else {
			var field = name;
		}
		if(field.prop('readonly') == val) {
			return;
		}
		if(val)
		{
			// set readonly
			field.prop('readonly', true);
			if(field.is('select'))
			{
				field.addClass('forceHidden hide');
				var id = field.attr('id');
				var chz = field.siblings('#'+id+'_chzn').addClass('hide');
				field.after('<input type="text" style="width: '+ (chz.width()-20) +'px" class="readonlySelect" name="rs_' + field.attr('name') + '_disp" value="' + field.find('option:selected').text() + '" readonly>');
			}
			if(field.hasClass('sourceField'))
			{
                field.addClass('forceHidden hide');
                var displayField = Vtiger_Edit_Js.getDisplayField(field);
                displayField.prop('readonly', true);
                //hiding buttons on the uitype 10
                field.parent().find(".add-on").addClass('forceHidden hide');
            }
		} else {
			// revert readonly
			field.prop('readonly', false);
			if(field.hasClass('forceHidden'))
			{
				var prevVal = field.val();
				field.parent().find('input[name="rs_' + field.attr('name') + '_disp"]').remove();
				field.removeClass('forceHidden hide');
				if(field.length > 1) {
				field = jQuery(field[1]);
				}
                if(field.hasClass('sourceField'))
                {
                    var displayField = Vtiger_Edit_Js.getDisplayField(field);
                    //Only want to remove readonly on a uitype 10 if it doesn't have a value.
                    if(displayField.val().length == 0) {
                        displayField.prop('readonly', false);
                    }
                    field.parent().find(".add-on").removeClass('forceHidden hide');
                }
				var id = field.attr('id');
				field.siblings('#'+id+'_chzn').removeClass('hide');
				if(field.val() != prevVal) {
					field.val(prevVal);
					if(field.hasClass('chosen-select'))
					{
						field.trigger('liszt:updated');
					}
					field.trigger('change');
				}
			}
		}
	},

	getAgentId: function()
	{
		var agentid = jQuery('[name="agentid"]').val();
		if(!agentid)
		{
			agentid = jQuery('[name="agentmanager_id"]').val();
		}
		if(app.getModuleName() == 'AgentManager' && !agentid)
		{
			agentid = getQueryVariable('record');
		}
		return agentid;
	},

	populateData: function(data, map)
	{
		for(var key in map)
		{
			var src = map[key];
			Vtiger_Edit_Js.setValue(key, data[src]);
		}
	},

    clearMultiPicklist : function(field){
        var possibleMulti = jQuery('[name="'+field.prop('name')+'[]"]');
        if (possibleMulti) {
            if(possibleMulti.hasClass('select2')){
                possibleMulti.children(':selected').removeProp('selected');
                field.next().find('li.select2-search-choice').remove();

            }
        }
    },


	setValue : function(field, value)
	{
        // Some functions do this, some don't, we should just make it do this consistently.
        if(typeof field == "string") {
            field = $('[name="' + field + '"]');
        }
        if(typeof trigger == 'undefined') {
            trigger = true;
        }
		if(field.is(':checkbox'))
		{
			field.prop('checked', value);
		}
		else if(field.is('select'))
		{
			field.val(value);
			var option = field.find('option:selected');
			var text = value;
			if(option.length > 0)
			{
				text = option.text();
			}
			field.closest('td').find('span:not([class])').last().text(text);
			field.closest('td').find('[class="active-result result-selected"]').attr('class', 'active-result');
			var index = field[0].selectedIndex;
			if(index >= 0)
			{
				jQuery('#' + field.attr('id') + '_chzn_o_' + index).addClass('result-selected');
			}

            // Picklist is possibly readonly, need to update the display value in that case.
            var readonly_field = $('[name="rs_' + field.attr('name') + '_disp"]');
            if(readonly_field.length > 0) {
                readonly_field.val(text);
            }
		}
		else
		{
			field.val(value);
		}

        if(trigger) {
            //Infinite loops
            //field.trigger('change');
        }
	},

	// field: jQuery selector
	getDate: function (field) {
        if (typeof field == 'string') {
            field = $('[name="' + field + '"]');
        }
		if (
		    field.length == 0 ||
            field.val() == ''
        ) {
			return new Date();
		}
		var fmt = field.data('date-format');
		if (!fmt) {
			console.log('Date field ' + field.attr('name') + ' without a date format set!');
			return new Date(field.val());
		}
		fmt = fmt.split('-');
		var val = field.val().split('-');
		var yearIndex = fmt.indexOf('yyyy');
		var monthIndex = fmt.indexOf('mm');
		var dayIndex = fmt.indexOf('dd');
		if (yearIndex == -1 || monthIndex == -1 || dayIndex == -1 || val.length < 3) {
			return undefined;
		}
		return new Date(Number(val[yearIndex]), Number(val[monthIndex])-1, Number(val[dayIndex]));
	},
	// field: jQuery selector
	// date: Date object
	setDate: function (field, date) {
		if (field.length == 0) {
			return;
		}
		var fmt = field.data('date-format');
		if (!fmt) {
			console.log('Date field ' + field.attr('name') + ' without a date format set!');
			return;
		}
		fmt = fmt.split('-');
		var yearIndex = fmt.indexOf('yyyy');
		var monthIndex = fmt.indexOf('mm');
		var dayIndex = fmt.indexOf('dd');
		if (yearIndex == -1 || monthIndex == -1 || dayIndex == -1) {
			return undefined;
		}
		var val = [];
		val[yearIndex] = date.getFullYear();
		val[monthIndex] = date.getMonth()+1;
		val[dayIndex] = date.getDate();
		field.val(val[0] + '-' + val[1] + '-' + val[2]);
	},

	hideCell : function(name) {
		// allow passing jQuery selections
		if(typeof name == 'string') {
			var hiddenField = jQuery('[name="' + name + '"]');
		} else {
			var hiddenField = name;
		}
		if(hiddenField.hasClass('forceHidden'))
		{
			return;
		}
		var fieldCell = hiddenField.closest('td');
		if (!fieldCell.hasClass('hiddenCell')) {
			var mainRow = hiddenField.closest('tr');
			var labelCell = fieldCell.prev('td');
			var blockTbody = hiddenField.closest('tbody');
			var lastRow = blockTbody.find('tr:not(:hidden)').last();
			var blankCells = '<td class="fieldLabel medium blankCell">&nbsp;</td><td class="fieldValue medium blankCell">&nbsp;</td>';
			labelCell.addClass('hide hiddenCell');
			fieldCell.addClass('hide hiddenCell');
			if(fieldCell.hasClass('preservePlace'))
			{
				jQuery(blankCells).insertAfter(fieldCell);
			}
			else
			{
				jQuery(blankCells).appendTo(mainRow);
			}
			//hide blank rows that result from wither case
			// ---- BEGIN check row to see if it should be hidden ---- //
			//var hidden = [];
			var notHidden = [];
			//check the whole row to see if all the TD are empty, if they are hide the row.
			mainRow.children('td').each(function (index, child) {
				if ($(child).hasClass('hide')) {
					//hidden.push(child);
				} else {
					//must check if the TD is just plain EMPTY
					if (child.innerHTML.length != 0 && child.innerHTML != '&nbsp;' &&
						!jQuery(child).hasClass('hiddenCell') && !jQuery(child).hasClass('blankCell')) {
						notHidden.push(child);
					}
				}
			});
			if (notHidden.length <= 0) {
				//there are NO non-hidden tds so hide the row.
				mainRow.addClass('hide hiddenRow');
			}
			// ---- END check row to see if it should be hidden ---- //
		}
	},

	showCell : function(name) {
		// allow passing jQuery selections
		if(typeof name == 'string') {
			var hiddenField = jQuery('[name="' + name + '"]');
		} else {
			var hiddenField = name;
		}
		var fieldCell = hiddenField.closest('td');
		if (fieldCell.hasClass('hiddenCell')) {
			var labelCell = fieldCell.prev('td');
			var blockTbody = hiddenField.closest('tbody');
			var mainRow = hiddenField.closest('tr');
			labelCell.removeClass('hide hiddenCell');
			fieldCell.removeClass('hide hiddenCell');
			mainRow.removeClass('hide hiddenRow');
			if(fieldCell.hasClass('preservePlace')) {
				var blankLabel = fieldCell.next('.blankCell');
				var blankField = blankLabel.next('.blankCell');
				blankLabel.remove();
				blankField.remove();
			} else {
				var blankLabel = mainRow.find('.blankCell').first();
				var blankField = blankLabel.next('.blankCell');
				blankLabel.remove();
				blankField.remove();
			}
		}
	},

	updateVisibilityField : function(targetName, isEditView, getField, getFieldValue)
	{
		var data = visibilityDataFields[targetName];
		var hidden = false;
		var checked = false;
		for(var source in data.hiddenBy)
		{
			if(data.hiddenBy[source].indexOf(true) != -1)
			{
				Vtiger_Edit_Js.hideCell(getField(targetName));
				hidden = true;
				break;
			}
		}
		if(!hidden) {
			Vtiger_Edit_Js.showCell(getField(targetName));
		}

		var readonly = false;
		for(var source in data.readonlyBy)
		{
			if(data.readonlyBy[source].indexOf(true) != -1)
			{
				Vtiger_Edit_Js.setReadonly(getField(targetName), true);
				readonly = true;
				break;
			}
		}
		if(!readonly) {
			Vtiger_Edit_Js.setReadonly(getField(targetName), false);
		}
		Vtiger_Edit_Js.storeFieldMandatoryState(getField(targetName));

		var mandatory = false;
		for (var source in data.mandatoryBy) {
		    if(data.mandatoryBy[source].indexOf(true) != -1)
		    {
		        Vtiger_Edit_Js.makeFieldMandatory(getField(targetName));
		        mandatory = true;
		        break;
		    }
		}

		var unmandatory = false;
		if (!mandatory) {
		    for (var source in data.unmandatoryBy) {
		        if (data.unmandatoryBy[source].indexOf(true) != -1) {
		            Vtiger_Edit_Js.makeFieldNotMandatory(getField(targetName));
		            unmandatory = true;
		            break;
		        }
		    }
		}

		if(!mandatory && !unmandatory) {
		    Vtiger_Edit_Js.restoreMandatoryState(getField(targetName));
		}

		var pickListOverride = false;
		for(var source in data.pickListSet)
		{
			var options = data.pickListSet[source];
			if(options !== false)
			{
				Vtiger_Edit_Js.setPicklistOptions(getField(targetName), options, getFieldValue);
				pickListOverride = true;
				break;
			}
		}
		if(!pickListOverride)
		{
			// TODO: this won't work if the picklist values are first overwritten by a dependency handler
			Vtiger_Edit_Js.setPicklistOptions(getField(targetName), undefined, getFieldValue);
		}
	},
	updateVisibilityBlock : function(targetLabel, isEditView, getBlock)
	{
		var data = visibilityDataBlocks[targetLabel];
		var hidden = false;
		for(var source in data.hiddenBy)
		{
			if(data.hiddenBy[source].indexOf(true) != -1)
			{
				getBlock(targetLabel).addClass('hide inactiveBlock');
				hidden = true;
				break;
			}
		}
		if(!hidden) {
			getBlock(targetLabel).removeClass('hide inactiveBlock');
		}
	},

	// options can be any of the following
	// Array of strings: text and value of the options are the same
	// Object: property names are the values, property values are the text
	// Array of objects: [{value: x, text: x},...] -- this allows you to force a certain order rather than following the order of the values
	setPicklistOptions : function(targetName, options, getFieldValue)
	{
		if(typeof targetName == 'string') {
			var targetField = jQuery('[name="' + targetName + '"]');
		} else {
			var targetField = targetName;
		}

		if(typeof options == 'undefined') {
			if(typeof visibilityDataFields != 'undefined'
				&& typeof visibilityDataFields[targetName] != 'undefined'
				&& typeof visibilityDataFields[targetName].defaultPickList != 'undefined')
			{
				options = visibilityDataFields[targetName].defaultPickList;
			} else {
				var original = targetField.data('fieldinfo');
				if(typeof original != 'undefined')
				{
					var data = original;
					if(typeof data.picklistvalues != 'undefined')
					{
						options = data.picklistvalues;
					}
					else {
						options = [];
					}
				} else {
					options = [];
				}
			}
		}

		if(typeof getFieldValue != 'undefined') {
			var targetVal = getFieldValue(targetField);
		} else {
			var targetVal = targetField.val();
		}
		if(typeof targetVal =='undefined' || targetVal == '')
		{
			targetVal = targetField.data('selected-value');
		}
		targetField.data('selected-value', targetVal);
		var triggerChange = false;
		var origVal = targetField.val();

		if(targetField.is('select')) {
			targetField.find('option[value!=""]').remove();
			if (options.constructor === Array) {
				for (var k = 0; k < options.length; ++k) {
					var opt = options[k];
					if(typeof opt == 'string') {
						var sel = targetVal == opt ? ' selected' : '';
						targetField.append('<option value="' + opt + '"' + sel + '>' + app.vtranslate(opt) + '</option>');
					} else {
						var sel = targetVal == opt['value'] ? ' selected' : '';
						targetField.append('<option value="' + opt['value'] + '"' + sel + '>' + app.vtranslate(opt['text']) + '</option>');
					}
				}
			} else {
				for (var v in options) {
					var label = options[v];
					var sel = targetVal == v ? ' selected' : '';
					targetField.append('<option value="' + v + '"' + sel + '>' + label + '</option>');
				}
			}
			targetField.trigger('liszt:updated');
			if (origVal != targetField.val()) {
				triggerChange = true;
			}
			if(triggerChange)
			{
				targetField.trigger('change');
			}
			targetField.trigger('picklist_updated');
		} else if(options.constructor !== Array) {
			// see if we can update the span text
			if(typeof options[targetVal] != 'undefined')
			{
				targetField.children('span.value').text(options[targetVal]);
			}
		}
	},

    findLeftTDThing : function(fieldObject, thing) {
	    if (fieldObject == 'undefined') {
            return false;
        }
        if (fieldObject.closest('td') == 'undefined') {
            return false;
        }
        if (fieldObject.closest('td').prev('td') == 'undefined') {
            return false;
        }
        if (fieldObject.closest('td').prev('td').find(thing) == 'undefined') {
            return false;
        }
        return fieldObject.closest('td').prev('td').find(thing);
    },

    addOptionalText : function(fieldObject, optionalText) {
        labelObject = Vtiger_Edit_Js.findLeftTDThing(fieldObject, 'label');
        addedSpan = labelObject.find('.addedText');
        if (addedSpan != 'undefined'){
            addedSpan.remove();
        }
        labelObject.append('<span class = "addedText"> '+optionalText+'</span>');
    },

    removeOptionalText : function(fieldObject){
        labelObject = Vtiger_Edit_Js.findLeftTDThing(fieldObject, 'label');
        labelObject.find('.addedText').remove();
    },

    removeMandatoryFlag : function(fieldObject) {
        var search = Vtiger_Edit_Js.findLeftTDThing(fieldObject, 'span');
        if (!search || search.length == 0) {
            return;
        }

        search.each(function() {
            //The assumption is one of the span's in the td to the left of the field has class redColor for the *
            //@TODO: might have to match it's a * in the innerhtml.
            if (jQuery(this).hasClass('redColor')) {
                jQuery(this).remove();
            }
        });
    },

    addMandatoryFlag : function(fieldObject) {
        Vtiger_Edit_Js.removeMandatoryFlag(fieldObject);

        var search = Vtiger_Edit_Js.findLeftTDThing(fieldObject, 'label');
        if (!search) {
            return;
        }
        if (search.length == 0) {
            //@TODO: I don't think label is always there.  but I can't find an example of this.  write to the console to be found.
            console.error("Failed to find the label to add the mandatory flag to.");
        } else {
            search.prepend('<span class="redColor">*</span>');
        }
    },

    restoreMandatoryState : function (fieldObject) {
        var fieldObject = Vtiger_Edit_Js.getDisplayField(fieldObject);
        var priorMandatoryState = fieldObject.attr('data-prev-mandatory-state');
        if (typeof priorMandatoryState != 'undefined' && priorMandatoryState == 'on') {
            Vtiger_Edit_Js.makeFieldMandatory(fieldObject);
        } else {
            Vtiger_Edit_Js.makeFieldNotMandatory(fieldObject);
        }
    },

    storeFieldMandatoryState : function (fieldToChange) {
        var fieldToChange = Vtiger_Edit_Js.getDisplayField(fieldToChange);
        if (fieldToChange && typeof fieldToChange != 'undefined') {
            if (fieldToChange.attr('data-prev-mandatory-state')) {
                return;
            }
            var currentValidation = fieldToChange.attr('data-validation-engine');
            if (!currentValidation) {
                fieldToChange.attr('data-prev-mandatory-state', 'off');
            } else {
                var testVal = new RegExp(/validate.*?\[\s*required,/i);
                var test = currentValidation.match(testVal);
                //@NOTE: .match returns null if it DOESN'T match.
                if (test == null) {
                    // IS NOT mandatory
                    fieldToChange.attr('data-prev-mandatory-state', 'off');
                } else {
                    // IS mandatory
                    fieldToChange.attr('data-prev-mandatory-state', 'on');
                }
            }
        }
    },

    makeFieldNotMandatory : function (fieldToChange) {
        if(typeof fieldToChange == 'string') {
            fieldToChange = $('[name="' + fieldToChange + '"]');
        }

        if (fieldToChange && typeof fieldToChange != 'undefined') {
            Vtiger_Edit_Js.removeMandatoryFlag(fieldToChange);
            displayField = Vtiger_Edit_Js.getDisplayField(fieldToChange);
            var currentValidation = displayField.attr('data-validation-engine');
            if (!currentValidation) {
                return;
            } else {
                var testVal = new RegExp(/validate.*?\[\s*required,/i);
                var test = currentValidation.match(testVal);
                //@NOTE: .match returns null if it DOESN'T match.
                if (test != null) {
                    //there IS a required at the start so remove just that part.
                    var newValidation = currentValidation.replace(testVal, 'validate[');
                    displayField.attr('data-validation-engine', newValidation);
                }
            }
        }
    },

    makeFieldMandatory : function (fieldToChange) {
        if(typeof fieldToChange == 'string') {
            fieldToChange = $('[name="' + fieldToChange + '"]');
        }

        var fieldToChange = Vtiger_Edit_Js.getDisplayField(fieldToChange);
        if (fieldToChange && typeof fieldToChange != 'undefined') {
            var currentValidation = fieldToChange.attr('data-validation-engine');
            Vtiger_Edit_Js.addMandatoryFlag(fieldToChange);
            if (!currentValidation) {
                //@TODO: Verify this is the basic SET validation attribute.
                fieldToChange.attr('data-validation-engine', 'validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]');
                return;
            } else {
                var testVal = new RegExp(/validate.*?\[\s*required,/i);
                var test = currentValidation.match(testVal);
                //@NOTE: .match returns null if it DOESN'T match.
                if (test != null) {
                    return;
                } else {
                    //there is no required in the validate so simply add it in (hopefully)
                    var newValidation = currentValidation.replace(/validate.*?\[/i, 'validate[required,');
                    fieldToChange.attr('data-validation-engine', newValidation);
                }
            }
        }
    },

    getDisplayField : function (fieldToChange){
        if(fieldToChange.hasClass('sourceField')){
            var displayFieldName = fieldToChange.attr('name')+'_display';
            displayField = jQuery('[name = "'+displayFieldName+'"]');
            if (typeof displayField != 'undefined'){
                return displayField;
            }
        }
        return fieldToChange;
    }

},{

	formElement : false,

	zipCodeArray : '',
	autocompleteOriginAdd : '',
	autocompleteOriginZip : '',
	autocompleteDestinationAdd : '',
	autocompleteDestinationZip : '',
	componentForm : '',
	originComponentForm : '',
	destinationComponentForm : '',
	billingComponentForm : '',
	contractBillComponentForm: '',
	agentComponentForm : '',
	vanlineComponentForm : '',
	stopsComponentForm : '',
	transfereesComponentForm: '',
	transfereeComponentForm: '',
	transferee1ComponentForm:'',
	addressComponentForm: '',
  WFWarehousesComponentForm: '',
	WFOperationsTaskFormTest: '',
	leadsComponentForm: '',
	mailingComponentForm: '',
	otherComponentForm: '',
    employeeMailingForm: '',
    agentManagerMailingForm: '',
	//transfereesshippingComponentForm:'',
    //Stored history of duplicate and duplicate check result
    duplicateCheckCache : {},
	businessLineMapping: {
		'HHG - Interstate': 'Interstate Move',
		'HHG - Intrastate': 'Intrastate Move',
		'HHG - Local': 'Local Move',
		'HHG - International': 'Local Move',
		'Electronics - Interstate': 'Interstate Move',
		'Electronics - Intrastate': 'Intrastate Move',
		'Electronics - Local': 'Local Move',
		'Electronics - International': 'Local Move',
		'Display & Exhibits - Interstate': 'Interstate Move',
		'Display & Exhibits - Intrastate': 'Intrastate Move',
		'Display & Exhibits - Local': 'Local Move',
		'Display & Exhibits - International': 'Local Move',
		'General Commodities - Interstate': 'Interstate Move',
		'General Commodities - Intrastate': 'Intrastate Move',
		'General Commodities - Local': 'Local Move',
		'General Commodities - International': 'Local Move',
		'Auto - Interstate': 'Interstate Move',
		'Auto - Intrastate': 'Intrastate Move',
		'Auto - Local': 'Local Move',
		'Auto - International': 'Local Move',
		'Commercial - Interstate': 'Interstate Move',
		'Commercial - Intrastate': 'Intrastate Move',
		'Commercial - Local': 'Local Move',
		'Commercial - International': 'Local Move'
	},
    duplicateCheckCache : {},
	initializeAddressAutofill : function(moduleName) {
		var disabledModules = jQuery('#disabledGoogleModules').val();
		if(typeof disabledModules == 'undefined'){
			disabledModules = [];
		} else {
			disabledModules = disabledModules.split('::');
		}
		if (typeof google == 'undefined') {
			return;
		}

		if(disabledModules.indexOf(moduleName) != -1) {
			return;
		}
		var thisInstance = this;
		if(jQuery('#' + moduleName + '_editView_fieldName_origin_address1').length) {
			autocompleteOriginAdd = new google.maps.places.Autocomplete(
				(document.getElementById(moduleName + '_editView_fieldName_origin_address1')),
				{ types: ['geocode'] });

			google.maps.event.addListener(autocompleteOriginAdd, 'place_changed', function() {
				thisInstance.fillInAddress('Origin', autocompleteOriginAdd);
			});
		}

		if(jQuery('#' + moduleName + '_editView_fieldName_origin_city').length) {
			autocompleteOriginCity = new google.maps.places.Autocomplete(
				(document.getElementById(moduleName + '_editView_fieldName_origin_city')),
				{ types: ['geocode'] });

			google.maps.event.addListener(autocompleteOriginCity, 'place_changed', function() {
				thisInstance.fillInAddress('Origin', autocompleteOriginCity);
			});
		}

		if(jQuery('#' + moduleName + '_editView_fieldName_origin_state').length) {
			autocompleteOriginState = new google.maps.places.Autocomplete(
				(document.getElementById(moduleName + '_editView_fieldName_origin_state')),
				{ types: ['geocode'] });

			google.maps.event.addListener(autocompleteOriginState, 'place_changed', function() {
				thisInstance.fillInAddress('Origin', autocompleteOriginState);
			});
		}

		if(jQuery('#' + moduleName + '_editView_fieldName_origin_zip').length) {
			autocompleteOriginZip = new google.maps.places.Autocomplete(
				(document.getElementById(moduleName + '_editView_fieldName_origin_zip')),
				{ types: ['geocode'] });

			google.maps.event.addListener(autocompleteOriginZip, 'place_changed', function() {
				thisInstance.fillInAddress('Origin', autocompleteOriginZip);
				jQuery('#'+moduleName+'_editView_fieldName_origin_zip').closest('td').find('.formError').remove();
			});
		}

		if(jQuery('#' + moduleName + '_editView_fieldName_origin_country').length) {
			autocompleteOriginCountry = new google.maps.places.Autocomplete(
				(document.getElementById(moduleName + '_editView_fieldName_origin_country')),
				{ types: ['geocode'] });

			google.maps.event.addListener(autocompleteOriginCountry, 'place_changed', function() {
				thisInstance.fillInAddress('Origin', autocompleteOriginCountry);
			});
		}

		if(jQuery('#' + moduleName + '_editView_fieldName_destination_address1').length) {
			autocompleteDestinationAdd = new google.maps.places.Autocomplete(
				(document.getElementById(moduleName + '_editView_fieldName_destination_address1')),
				{ types: ['geocode'] });

			google.maps.event.addListener(autocompleteDestinationAdd, 'place_changed', function() {
				thisInstance.fillInAddress('Destination', autocompleteDestinationAdd);
			});
		}

		if(jQuery('#' + moduleName + '_editView_fieldName_destination_city').length) {
			autocompleteDestinationCity = new google.maps.places.Autocomplete(
				(document.getElementById(moduleName + '_editView_fieldName_destination_city')),
				{ types: ['geocode'] });

			google.maps.event.addListener(autocompleteDestinationCity, 'place_changed', function() {
				thisInstance.fillInAddress('Destination', autocompleteDestinationCity);
			});
		}

		if(jQuery('#' + moduleName + '_editView_fieldName_destination_state').length) {
			autocompleteDestinationState = new google.maps.places.Autocomplete(
				(document.getElementById(moduleName + '_editView_fieldName_destination_state')),
				{ types: ['geocode'] });

			google.maps.event.addListener(autocompleteDestinationState, 'place_changed', function() {
				thisInstance.fillInAddress('Destination', autocompleteDestinationState);
			});
		}

		if(jQuery('#' + moduleName + '_editView_fieldName_destination_zip').length) {
			autocompleteDestinationZip = new google.maps.places.Autocomplete(
				(document.getElementById(moduleName + '_editView_fieldName_destination_zip')),
				{ types: ['geocode'] });

			google.maps.event.addListener(autocompleteDestinationZip, 'place_changed', function() {
				thisInstance.fillInAddress('Destination', autocompleteDestinationZip);
				jQuery('#'+moduleName+'_editView_fieldName_destination_zip').closest('td').find('.formError').remove();
			});
		}

		if(jQuery('#' + moduleName + '_editView_fieldName_destination_country').length) {
			autocompleteDestinationCountry = new google.maps.places.Autocomplete(
				(document.getElementById(moduleName + '_editView_fieldName_destination_country')),
				{ types: ['geocode'] });

			google.maps.event.addListener(autocompleteDestinationCountry, 'place_changed', function() {
				thisInstance.fillInAddress('Destination', autocompleteDestinationCountry);
			});
		}

		//add address completion for Account specific field name
		//street_address: moduleName + '_editView_fieldName_ship_street',
		if(jQuery('#' + moduleName + '_editView_fieldName_ship_street').length) {
			autocompleteShippingAdd = new google.maps.places.Autocomplete(
				(document.getElementById(moduleName + '_editView_fieldName_ship_street')),
				{ type: ['geocode'] });

			google.maps.event.addListener(autocompleteShippingAdd, 'place_changed', function() {
				thisInstance.fillInAddress('AccountShip', autocompleteShippingAdd);
			});
		}

		//locality: moduleName + '_editView_fieldName_ship_city',
		if(jQuery('#' + moduleName + '_editView_fieldName_ship_city').length) {
			autocompleteShippingCity = new google.maps.places.Autocomplete(
				(document.getElementById(moduleName + '_editView_fieldName_ship_city')),
				{ type: ['geocode'] });

			google.maps.event.addListener(autocompleteShippingCity, 'place_changed', function() {
				thisInstance.fillInAddress('AccountShip', autocompleteShippingCity);
			});
		}

		//administrative_area_level_1: moduleName + '_editView_fieldName_ship_state',
		if(jQuery('#' + moduleName + '_editView_fieldName_ship_state').length) {
			autocompleteShippingState = new google.maps.places.Autocomplete(
				(document.getElementById(moduleName + '_editView_fieldName_ship_state')),
				{ type: ['geocode'] });

			google.maps.event.addListener(autocompleteShippingState, 'place_changed', function() {
				thisInstance.fillInAddress('AccountShip', autocompleteShippingState);
			});
		}

		//postal_code: moduleName + '_editView_fieldName_ship_code'
		if(jQuery('#' + moduleName + '_editView_fieldName_ship_code').length) {
			autocompleteShippingCode = new google.maps.places.Autocomplete(
				(document.getElementById(moduleName + '_editView_fieldName_ship_code')),
				{ type: ['geocode'] });

			google.maps.event.addListener(autocompleteShippingCode, 'place_changed', function() {
				thisInstance.fillInAddress('AccountShip', autocompleteShippingCode);
			});
		}

		//end address completion for Account specific field name

		if(jQuery('#' + moduleName + '_editView_fieldName_bill_street').length) {
			autocompleteBillingAdd = new google.maps.places.Autocomplete(
				(document.getElementById(moduleName + '_editView_fieldName_bill_street')),
				{ type: ['geocode'] });

			google.maps.event.addListener(autocompleteBillingAdd, 'place_changed', function() {
				thisInstance.fillInAddress('Billing', autocompleteBillingAdd);
			});
		}

		if(jQuery('#' + moduleName + '_editView_fieldName_bill_city').length) {
			autocompleteBillingCity = new google.maps.places.Autocomplete(
				(document.getElementById(moduleName + '_editView_fieldName_bill_city')),
				{ type: ['geocode'] });

			google.maps.event.addListener(autocompleteBillingCity, 'place_changed', function() {
				thisInstance.fillInAddress('Billing', autocompleteBillingCity);
			});
		}

		if(jQuery('#' + moduleName + '_editView_fieldName_bill_state').length) {
			autocompleteBillingState = new google.maps.places.Autocomplete(
				(document.getElementById(moduleName + '_editView_fieldName_bill_state')),
				{ type: ['geocode'] });

			google.maps.event.addListener(autocompleteBillingState, 'place_changed', function() {
				thisInstance.fillInAddress('Billing', autocompleteBillingState);
			});
		}

		if(jQuery('#' + moduleName + '_editView_fieldName_bill_code').length) {
			autocompleteBillingZip = new google.maps.places.Autocomplete(
				(document.getElementById(moduleName + '_editView_fieldName_bill_code')),
				{ types: ['geocode'] });

			google.maps.event.addListener(autocompleteBillingZip, 'place_changed', function() {
				thisInstance.fillInAddress('Billing', autocompleteBillingZip);
				jQuery('#' + moduleName + '_editView_fieldName_bill_code').closest('td').find('.formError').remove();
			});
		}

		if(jQuery('#'+moduleName+'_editView_fieldName_agent_address1').length) {
			autocompleteAgentAdd = new google.maps.places.Autocomplete(
				(document.getElementById(moduleName + '_editView_fieldName_agent_address1')),
				{ types: ['geocode'] });

			google.maps.event.addListener(autocompleteAgentAdd, 'place_changed', function() {
				thisInstance.fillInAddress('Agent', autocompleteAgentAdd);
			});
		}

		if(jQuery('#'+moduleName+'_editView_fieldName_agent_city').length) {
			autocompleteAgentCity = new google.maps.places.Autocomplete(
				(document.getElementById(moduleName + '_editView_fieldName_agent_city')),
				{ types: ['geocode'] });

			google.maps.event.addListener(autocompleteAgentCity, 'place_changed', function() {
				thisInstance.fillInAddress('Agent', autocompleteAgentCity);
			});
		}

		if(jQuery('#'+moduleName+'_editView_fieldName_agent_state').length) {
			autocompleteAgentState = new google.maps.places.Autocomplete(
				(document.getElementById(moduleName + '_editView_fieldName_agent_state')),
				{ types: ['geocode'] });

			google.maps.event.addListener(autocompleteAgentState, 'place_changed', function() {
				thisInstance.fillInAddress('Agent', autocompleteAgentState);
			});
		}

		if(jQuery('#'+moduleName+'_editView_fieldName_agent_zip').length) {
			autocompleteAgentZip = new google.maps.places.Autocomplete(
				(document.getElementById(moduleName + '_editView_fieldName_agent_zip')),
				{ types: ['geocode'] });

			google.maps.event.addListener(autocompleteAgentZip, 'place_changed', function() {
				thisInstance.fillInAddress('Agent', autocompleteAgentZip);
				jQuery('#' + moduleName + '_editView_fieldName_agent_zip').closest('td').find('.formError').remove();
			});
		}

		if(jQuery('#'+moduleName+'_editView_fieldName_agent_country').length) {
			autocompleteAgentCountry = new google.maps.places.Autocomplete(
				(document.getElementById(moduleName + '_editView_fieldName_agent_country')),
				{ types: ['geocode'] });

			google.maps.event.addListener(autocompleteAgentCountry, 'place_changed', function() {
				thisInstance.fillInAddress('Agent', autocompleteAgentCountry);
			});
		}

		if(jQuery('#'+moduleName+'_editView_fieldName_vanline_address1').length) {
			autocompleteVanlineAdd = new google.maps.places.Autocomplete(
				(document.getElementById(moduleName + '_editView_fieldName_vanline_address1')),
				{ types: ['geocode'] });

			google.maps.event.addListener(autocompleteVanlineAdd, 'place_changed', function() {
				thisInstance.fillInAddress('Vanline', autocompleteVanlineAdd);
			});
		}

		if(jQuery('#'+moduleName+'_editView_fieldName_vanline_city').length) {
			autocompleteAgentCity = new google.maps.places.Autocomplete(
				(document.getElementById(moduleName + '_editView_fieldName_vanline_city')),
				{ types: ['geocode'] });

			google.maps.event.addListener(autocompleteAgentCity, 'place_changed', function() {
				thisInstance.fillInAddress('Vanline', autocompleteAgentCity);
			});
		}

		if(jQuery('#'+moduleName+'_editView_fieldName_vanline_state').length) {
			autocompleteAgentState = new google.maps.places.Autocomplete(
				(document.getElementById(moduleName + '_editView_fieldName_vanline_state')),
				{ types: ['geocode'] });

			google.maps.event.addListener(autocompleteAgentState, 'place_changed', function() {
				thisInstance.fillInAddress('Vanline', autocompleteAgentState);
			});
		}

		if(jQuery('#'+moduleName+'_editView_fieldName_vanline_zip').length) {
			autocompleteVanlineZip = new google.maps.places.Autocomplete(
				(document.getElementById(moduleName + '_editView_fieldName_vanline_zip')),
				{ types: ['geocode'] });

			google.maps.event.addListener(autocompleteVanlineZip, 'place_changed', function() {
				thisInstance.fillInAddress('Vanline', autocompleteVanlineZip);
				jQuery('#' + moduleName + '_editView_fieldName_vanline_zip').closest('td').find('.formError').remove();
			});
		}

		if(jQuery('#'+moduleName+'_editView_fieldName_vanline_country').length) {
			autocompleteAgentCountry = new google.maps.places.Autocomplete(
				(document.getElementById(moduleName + '_editView_fieldName_vanline_country')),
				{ types: ['geocode'] });

			google.maps.event.addListener(autocompleteAgentCountry, 'place_changed', function() {
				thisInstance.fillInAddress('Vanline', autocompleteAgentCountry);
			});
		}

		if(jQuery('#'+moduleName+'_editView_fieldName_extrastops_address1').length) {
			autocompleteStopAdd = new google.maps.places.Autocomplete(
				(document.getElementById(moduleName + '_editView_fieldName_extrastops_address1')),
				{ types: ['geocode'] });

			google.maps.event.addListener(autocompleteStopAdd, 'place_changed', function() {
				thisInstance.fillInAddress('ExtraStops', autocompleteStopAdd);
			});
		}
		if(jQuery('#'+moduleName+'_editView_fieldName_extrastops_address2').length) {
			autocompleteStopAdd2 = new google.maps.places.Autocomplete(
				(document.getElementById(moduleName + '_editView_fieldName_extrastops_address2')),
				{ types: ['geocode'] });

			/*google.maps.event.addListener(autocompleteStopAdd2, 'place_changed', function() {
				thisInstance.fillInAddress('ExtraStops', autocompleteStopAdd2);
			});*/
		}
		if(jQuery('#'+moduleName+'_editView_fieldName_extrastops_city').length) {
			autocompleteStopCity = new google.maps.places.Autocomplete(
				(document.getElementById(moduleName + '_editView_fieldName_extrastops_city')),
				{ types: ['geocode'] });

			google.maps.event.addListener(autocompleteStopCity, 'place_changed', function() {
				thisInstance.fillInAddress('ExtraStops', autocompleteStopCity);
			});
		}
		if(jQuery('#'+moduleName+'_editView_fieldName_extrastops_state').length) {
			autocompleteStopState = new google.maps.places.Autocomplete(
				(document.getElementById(moduleName + '_editView_fieldName_extrastops_state')),
				{ types: ['geocode'] });

			google.maps.event.addListener(autocompleteStopState, 'place_changed', function() {
				thisInstance.fillInAddress('ExtraStops', autocompleteStopState);
			});
		}
		if(jQuery('#'+moduleName+'_editView_fieldName_extrastops_country').length) {
			autocompleteStopCountry = new google.maps.places.Autocomplete(
				(document.getElementById(moduleName + '_editView_fieldName_extrastops_country')),
				{ types: ['geocode'] });

			google.maps.event.addListener(autocompleteStopCountry, 'place_changed', function() {
				thisInstance.fillInAddress('ExtraStops', autocompleteStopCountry);
			});
		}

		if(jQuery('#'+moduleName+'_editView_fieldName_extrastops_zip').length) {
			autocompleteStopZip = new google.maps.places.Autocomplete(
				(document.getElementById(moduleName + '_editView_fieldName_extrastops_zip')),
				{ types: ['geocode'] });

			google.maps.event.addListener(autocompleteStopZip, 'place_changed', function() {
				thisInstance.fillInAddress('ExtraStops', autocompleteStopZip);
				jQuery('#' + moduleName + '_editView_fieldName_extrastops_zip').closest('td').find('.formError').remove();
			});
		}

		if(jQuery('#'+moduleName+'_editView_fieldName_transferees_address1').length) {
			autocompleteTransfereesAdd = new google.maps.places.Autocomplete(
				(document.getElementById(moduleName + '_editView_fieldName_transferees_address1')),
				{ types: ['geocode'] });

			google.maps.event.addListener(autocompleteTransfereesAdd, 'place_changed', function() {
				thisInstance.fillInAddress('Transferee', autocompleteTransfereesAdd);
			});
		}

		if(jQuery('#'+moduleName+'_editView_fieldName_transferees_zip').length) {
			autocompleteTransfereesZip = new google.maps.places.Autocomplete(
				(document.getElementById(moduleName + '_editView_fieldName_transferees_zip')),
				{ types: ['geocode'] });

			google.maps.event.addListener(autocompleteTransfereesZip, 'place_changed', function() {
				thisInstance.fillInAddress('Transferee', autocompleteTransfereesZip);
				jQuery('#' + moduleName + '_editView_fieldName_transferees_zip').closest('td').find('.formError').remove();
			});
		}

		////This bit for Mailing AutoFill in Contacts
		if(jQuery('#' + moduleName + '_editView_fieldName_mailingstreet').length) {
			autocompleteMailingAdd = new google.maps.places.Autocomplete(
				(document.getElementById(moduleName + '_editView_fieldName_mailingstreet')),
				{ types: ['geocode'] });

			google.maps.event.addListener(autocompleteMailingAdd, 'place_changed', function() {
				thisInstance.fillInAddress('Mailing', autocompleteMailingAdd);
			});
		}

		if(jQuery('#' + moduleName + '_editView_fieldName_mailingstreet2').length) {
			autocompleteMailingAdd = new google.maps.places.Autocomplete(
				(document.getElementById(moduleName + '_editView_fieldName_mailingstreet2')),
				{ types: ['geocode'] });

			google.maps.event.addListener(autocompleteMailingAdd, 'place_changed', function() {
				thisInstance.fillInAddress('Mailing', autocompleteMailingAdd);
			});
		}

		if(jQuery('#' + moduleName + '_editView_fieldName_mailingpobox').length) {
			autocompleteMailingAdd2 = new google.maps.places.Autocomplete(
				(document.getElementById(moduleName + '_editView_fieldName_mailingpobox')),
				{ types: ['geocode'] });

			google.maps.event.addListener(autocompleteMailingAdd2, 'place_changed', function() {
				thisInstance.fillInAddress('Mailing', autocompleteMailingAdd2);
			});
		}

		if(jQuery('#' + moduleName + '_editView_fieldName_mailingcity').length) {
			autocompleteMailingCity = new google.maps.places.Autocomplete(
				(document.getElementById(moduleName + '_editView_fieldName_mailingcity')),
				{ types: ['geocode'] });

			google.maps.event.addListener(autocompleteMailingCity, 'place_changed', function() {
				thisInstance.fillInAddress('Mailing', autocompleteMailingCity);
			});
		}

		if(jQuery('#' + moduleName + '_editView_fieldName_mailingstate').length) {
			autocompleteMailingState = new google.maps.places.Autocomplete(
				(document.getElementById(moduleName + '_editView_fieldName_mailingstate')),
				{ types: ['geocode'] });

			google.maps.event.addListener(autocompleteMailingState, 'place_changed', function() {
				thisInstance.fillInAddress('Mailing', autocompleteMailingState);
			});
		}

		if(jQuery('#' + moduleName + '_editView_fieldName_mailingzip').length) {
			autocompleteMailingZip = new google.maps.places.Autocomplete(
				(document.getElementById(moduleName + '_editView_fieldName_mailingzip')),
				{ types: ['geocode'] });

			google.maps.event.addListener(autocompleteMailingZip, 'place_changed', function() {
				thisInstance.fillInAddress('Mailing', autocompleteMailingZip);
				jQuery('#'+moduleName+'_editView_fieldName_mailingzip').closest('td').find('.formError').remove();
			});
		}

		if(jQuery('#' + moduleName + '_editView_fieldName_mailingcountry').length) {
			autocompleteMailingCountry = new google.maps.places.Autocomplete(
				(document.getElementById(moduleName + '_editView_fieldName_mailingcountry')),
				{ types: ['geocode'] });

			google.maps.event.addListener(autocompleteMailingCountry, 'place_changed', function() {
				thisInstance.fillInAddress('Mailing', autocompleteMailingCountry);
			});
		}
		////End of Autofill for Contacts Mailing

		////This bit for Other AutoFill in Contacts
		if(jQuery('#' + moduleName + '_editView_fieldName_otherstreet').length) {
			autocompleteOtherAdd = new google.maps.places.Autocomplete(
				(document.getElementById(moduleName + '_editView_fieldName_otherstreet')),
				{ types: ['geocode'] });

			google.maps.event.addListener(autocompleteOtherAdd, 'place_changed', function() {
				thisInstance.fillInAddress('Other', autocompleteOtherAdd);
			});
		}

		if(jQuery('#' + moduleName + '_editView_fieldName_otherstreet2').length) {
			autocompleteOtherAdd = new google.maps.places.Autocomplete(
				(document.getElementById(moduleName + '_editView_fieldName_otherstreet2')),
				{ types: ['geocode'] });

			google.maps.event.addListener(autocompleteOtherAdd, 'place_changed', function() {
				thisInstance.fillInAddress('Other', autocompleteOtherAdd);
			});
		}

		if(jQuery('#' + moduleName + '_editView_fieldName_otherpobox').length) {
			autocompleteOtherAdd2 = new google.maps.places.Autocomplete(
				(document.getElementById(moduleName + '_editView_fieldName_otherpobox')),
				{ types: ['geocode'] });

			google.maps.event.addListener(autocompleteOtherAdd2, 'place_changed', function() {
				thisInstance.fillInAddress('Other', autocompleteOtherAdd2);
			});
		}

		if(jQuery('#' + moduleName + '_editView_fieldName_othercity').length) {
			autocompleteOtherCity = new google.maps.places.Autocomplete(
				(document.getElementById(moduleName + '_editView_fieldName_othercity')),
				{ types: ['geocode'] });

			google.maps.event.addListener(autocompleteOtherCity, 'place_changed', function() {
				thisInstance.fillInAddress('Other', autocompleteOtherCity);
			});
		}

		if(jQuery('#' + moduleName + '_editView_fieldName_otherstate').length) {
			autocompleteOtherState = new google.maps.places.Autocomplete(
				(document.getElementById(moduleName + '_editView_fieldName_otherstate')),
				{ types: ['geocode'] });

			google.maps.event.addListener(autocompleteOtherState, 'place_changed', function() {
				thisInstance.fillInAddress('Other', autocompleteOtherState);
			});
		}

		if(jQuery('#' + moduleName + '_editView_fieldName_otherzip').length) {
			autocompleteOtherZip = new google.maps.places.Autocomplete(
				(document.getElementById(moduleName + '_editView_fieldName_otherzip')),
				{ types: ['geocode'] });

			google.maps.event.addListener(autocompleteOtherZip, 'place_changed', function() {
				thisInstance.fillInAddress('Other', autocompleteOtherZip);
				jQuery('#'+moduleName+'_editView_fieldName_otherzip').closest('td').find('.formError').remove();
			});
		}

		if(jQuery('#' + moduleName + '_editView_fieldName_othercountry').length) {
			autocompleteOtherCountry = new google.maps.places.Autocomplete(
				(document.getElementById(moduleName + '_editView_fieldName_othercountry')),
				{ types: ['geocode'] });

			google.maps.event.addListener(autocompleteOtherCountry, 'place_changed', function() {
				thisInstance.fillInAddress('Other', autocompleteOtherCountry);
			});
		}
		////End of Autofill for Contacts Other
		if(moduleName == 'WFWarehouses') {
			if(jQuery('#'+moduleName+'_editView_fieldName_address').length) {
					autocompleteAddress = new google.maps.places.Autocomplete(
							(document.getElementById(moduleName + '_editView_fieldName_address')),
							{ types: ['geocode'] });

					google.maps.event.addListener(autocompleteAddress, 'place_changed', function() {
							thisInstance.fillInAddress('WFWarehouses', autocompleteAddress);
					});
			}
			if(jQuery('#'+moduleName+'_editView_fieldName_street').length) {
				autocompleteAddress = new google.maps.places.Autocomplete(
					(document.getElementById(moduleName + '_editView_fieldName_street')),
					{ types: ['geocode'] });

				google.maps.event.addListener(autocompleteAddress, 'place_changed', function() {
					thisInstance.fillInAddress('WFWarehouses', autocompleteAddress);
				});
			}
			if(jQuery('#'+moduleName+'_editView_fieldName_city').length) {
				autocompleteCity = new google.maps.places.Autocomplete(
					(document.getElementById(moduleName + '_editView_fieldName_city')),
					{ types: ['geocode'] });

				google.maps.event.addListener(autocompleteCity, 'place_changed', function() {
					thisInstance.fillInAddress('WFWarehouses', autocompleteCity);
				});
			}

			if(jQuery('#'+moduleName+'_editView_fieldName_state').length) {
				autocompleteState = new google.maps.places.Autocomplete(
					(document.getElementById(moduleName + '_editView_fieldName_state')),
					{ types: ['geocode'] });

				google.maps.event.addListener(autocompleteState, 'place_changed', function() {
					thisInstance.fillInAddress('WFWarehouses', autocompleteState);
				});
			}

			if(jQuery('#'+moduleName+'_editView_fieldName_postal_code').length) {
				autocompleteZip = new google.maps.places.Autocomplete(
					(document.getElementById(moduleName + '_editView_fieldName_postal_code')),
					{ types: ['geocode'] });

				google.maps.event.addListener(autocompleteZip, 'place_changed', function() {
					thisInstance.fillInAddress('WFWarehouses', autocompleteZip);
					jQuery('#' + moduleName + '_editView_fieldName_postal_code').closest('td').find('.formError').remove();
				});
			}

			if (jQuery('#' + moduleName + '_editView_fieldName_country').length) {
				autocompleteCountry = new google.maps.places.Autocomplete(
					(document.getElementById(moduleName + '_editView_fieldName_country')),
					{types: ['geocode']});

				google.maps.event.addListener(autocompleteCountry, 'place_changed', function () {
					thisInstance.fillInAddress('WFWarehouses', autocompleteCountry);
					jQuery('#' + moduleName + '_editView_fieldName_country').closest('td').find('.formError').remove();
				});
			}
		}

		if(moduleName != 'Leads' && moduleName != 'WFWarehouses') {
            if(moduleName != 'Opportunities') {
    			if(jQuery('#'+moduleName+'_editView_fieldName_address1').length) {
    				autocompleteAddress = new google.maps.places.Autocomplete(
    					(document.getElementById(moduleName + '_editView_fieldName_address1')),
    					{ types: ['geocode'] });

    				google.maps.event.addListener(autocompleteAddress, 'place_changed', function() {
    					thisInstance.fillInAddress('Address', autocompleteAddress);
    				});
    			}
                if(jQuery('#'+moduleName+'_editView_fieldName_address').length) {
                    autocompleteAddress = new google.maps.places.Autocomplete(
                        (document.getElementById(moduleName + '_editView_fieldName_address')),
                        { types: ['geocode'] });

                    google.maps.event.addListener(autocompleteAddress, 'place_changed', function() {
                        thisInstance.fillInAddress('Address', autocompleteAddress);
                    });
                }
            }else{
    			if(jQuery('#'+moduleName+'_editView_fieldName_street').length) {
    				autocompleteAddress = new google.maps.places.Autocomplete(
    					(document.getElementById(moduleName + '_editView_fieldName_street')),
    					{ types: ['geocode'] });

    				google.maps.event.addListener(autocompleteAddress, 'place_changed', function() {
    					thisInstance.fillInAddress('Address', autocompleteAddress);
    				});
    			}
            }

			if(jQuery('#'+moduleName+'_editView_fieldName_city').length) {
				autocompleteCity = new google.maps.places.Autocomplete(
					(document.getElementById(moduleName + '_editView_fieldName_city')),
					{ types: ['geocode'] });

				google.maps.event.addListener(autocompleteCity, 'place_changed', function() {
					thisInstance.fillInAddress('Address', autocompleteCity);
				});
			}

			if(jQuery('#'+moduleName+'_editView_fieldName_state').length) {
				autocompleteState = new google.maps.places.Autocomplete(
					(document.getElementById(moduleName + '_editView_fieldName_state')),
					{ types: ['geocode'] });

				google.maps.event.addListener(autocompleteState, 'place_changed', function() {
					thisInstance.fillInAddress('Address', autocompleteState);
				});
			}

			if(jQuery('#'+moduleName+'_editView_fieldName_zip').length) {
				autocompleteZip = new google.maps.places.Autocomplete(
					(document.getElementById(moduleName + '_editView_fieldName_zip')),
					{ types: ['geocode'] });

				google.maps.event.addListener(autocompleteZip, 'place_changed', function() {
					thisInstance.fillInAddress('Address', autocompleteZip);
					jQuery('#' + moduleName + '_editView_fieldName_zip').closest('td').find('.formError').remove();
				});
			}

			if (jQuery('#' + moduleName + '_editView_fieldName_country').length) {
				autocompleteCountry = new google.maps.places.Autocomplete(
					(document.getElementById(moduleName + '_editView_fieldName_country')),
					{types: ['geocode']});

				google.maps.event.addListener(autocompleteCountry, 'place_changed', function () {
					thisInstance.fillInAddress('Address', autocompleteCountry);
					jQuery('#' + moduleName + '_editView_fieldName_country').closest('td').find('.formError').remove();
				});
			}
		} else if(moduleName != 'WFWarehouses') {
			if(jQuery('#'+moduleName+'_editView_fieldName_lane').length) {
				autocompleteLane = new google.maps.places.Autocomplete(
					(document.getElementById(moduleName + '_editView_fieldName_lane')),
					{ types: ['geocode'] });

				google.maps.event.addListener(autocompleteLane, 'place_changed', function() {
					thisInstance.fillInAddress('LeadAddress', autocompleteLane);
				});
			}

			if(jQuery('#'+moduleName+'_editView_fieldName_city').length) {
				autocompleteLeadCity = new google.maps.places.Autocomplete(
					(document.getElementById(moduleName + '_editView_fieldName_city')),
					{ types: ['geocode'] });

				google.maps.event.addListener(autocompleteLeadCity, 'place_changed', function() {
					thisInstance.fillInAddress('LeadAddress', autocompleteLeadCity);
				});
			}

			if(jQuery('#'+moduleName+'_editView_fieldName_state').length) {
				autocompleteLeadState = new google.maps.places.Autocomplete(
					(document.getElementById(moduleName + '_editView_fieldName_state')),
					{ types: ['geocode'] });

				google.maps.event.addListener(autocompleteLeadState, 'place_changed', function() {
					thisInstance.fillInAddress('LeadAddress', autocompleteLeadState);
				});
			}

			if(jQuery('#'+moduleName+'_editView_fieldName_code').length) {
				autocompleteCode = new google.maps.places.Autocomplete(
					(document.getElementById(moduleName + '_editView_fieldName_code')),
					{ types: ['geocode'] });

				google.maps.event.addListener(autocompleteCode, 'place_changed', function() {
					thisInstance.fillInAddress('LeadAddress', autocompleteCode);
					jQuery('#' + moduleName + '_editView_fieldName_code').closest('td').find('.formError').remove();
				});
			}
		}

		if(jQuery('#'+moduleName+'_editView_fieldName_transferees_saddress1').length) {
			autocompleteTransferee1Add = new google.maps.places.Autocomplete(
				(document.getElementById(moduleName + '_editView_fieldName_transferees_saddress1')),
				{ types: ['geocode'] });

			google.maps.event.addListener(autocompleteTransferee1Add, 'place_changed', function() {
				thisInstance.fillInAddress('Transferee1', autocompleteTransferee1Add);
			});
		}

		if(jQuery('#'+moduleName+'_editView_fieldName_transferees_szip').length) {
			autocompleteTransferee1Zip = new google.maps.places.Autocomplete(
				(document.getElementById(moduleName + '_editView_fieldName_transferees_szip')),
				{ types: ['geocode'] });

			google.maps.event.addListener(autocompleteTransferee1Zip, 'place_changed', function() {
				thisInstance.fillInAddress('Transferee1', autocompleteTransferee1Zip);
			});
		}

		if (jQuery('#' + moduleName + '_editView_fieldName_billing_address1').length) {
			autocompleteContractBillAdd = new google.maps.places.Autocomplete(
				(document.getElementById(moduleName + '_editView_fieldName_billing_address1')),
				{types: ['geocode']});

			google.maps.event.addListener(autocompleteContractBillAdd, 'place_changed', function () {
				thisInstance.fillInAddress('ContractBill', autocompleteContractBillAdd);
			});
		}

		if (jQuery('#' + moduleName + '_editView_fieldName_billing_city').length) {
			autocompleteContractBillCity = new google.maps.places.Autocomplete(
				(document.getElementById(moduleName + '_editView_fieldName_billing_city')),
				{types: ['geocode']});

			google.maps.event.addListener(autocompleteContractBillCity, 'place_changed', function () {
				thisInstance.fillInAddress('ContractBill', autocompleteContractBillCity);
			});
		}

		if (jQuery('#' + moduleName + '_editView_fieldName_billing_state').length) {
			autocompleteContractBillState = new google.maps.places.Autocomplete(
				(document.getElementById(moduleName + '_editView_fieldName_billing_state')),
				{types: ['geocode']});

			google.maps.event.addListener(autocompleteContractBillState, 'place_changed', function () {
				thisInstance.fillInAddress('ContractBill', autocompleteContractBillState);
			});
		}

		if (jQuery('#' + moduleName + '_editView_fieldName_billing_zip').length) {
			autocompleteContractBillZip = new google.maps.places.Autocomplete(
				(document.getElementById(moduleName + '_editView_fieldName_billing_zip')),
				{types: ['geocode']});

			google.maps.event.addListener(autocompleteContractBillZip, 'place_changed', function () {
				thisInstance.fillInAddress('ContractBill', autocompleteContractBillZip);
			});
		}

        if (jQuery('#' + moduleName + '_editView_fieldName_agentmanager_mailing_address_1').length) {
            autocompleteAMMailingAddr1 = new google.maps.places.Autocomplete(
                (document.getElementById(moduleName + '_editView_fieldName_agentmanager_mailing_address_1')),
                {types: ['geocode']});

            google.maps.event.addListener(autocompleteAMMailingAddr1, 'place_changed', function () {
                thisInstance.fillInAddress('AgentManagerMailing', autocompleteAMMailingAddr1);
            });
        }

        if (jQuery('#' + moduleName + '_editView_fieldName_agentmanager_mailing_city').length) {
            autocompleteAMMailingCity = new google.maps.places.Autocomplete(
                (document.getElementById(moduleName + '_editView_fieldName_agentmanager_mailing_city')),
                {types: ['geocode']});

            google.maps.event.addListener(autocompleteAMMailingCity, 'place_changed', function () {
                thisInstance.fillInAddress('AgentManagerMailing', autocompleteAMMailingCity);
            });
        }

        if (jQuery('#' + moduleName + '_editView_fieldName_agentmanager_mailing_country').length) {
            autocompleteAMMailingCountry = new google.maps.places.Autocomplete(
                (document.getElementById(moduleName + '_editView_fieldName_agentmanager_mailing_country')),
                {types: ['geocode']});

            google.maps.event.addListener(autocompleteAMMailingCountry, 'place_changed', function () {
                thisInstance.fillInAddress('AgentManagerMailing', autocompleteAMMailingCountry);
            });
        }

        if (jQuery('#' + moduleName + '_editView_fieldName_agentmanager_mailing_state').length) {
            autocompleteAMMailingState = new google.maps.places.Autocomplete(
                (document.getElementById(moduleName + '_editView_fieldName_agentmanager_mailing_state')),
                {types: ['geocode']});

            google.maps.event.addListener(autocompleteAMMailingState, 'place_changed', function () {
                thisInstance.fillInAddress('AgentManagerMailing', autocompleteAMMailingState);
            });
        }

        if (jQuery('#' + moduleName + '_editView_fieldName_agentmanager_mailing_zip').length) {
            autocompleteAMMailingZip = new google.maps.places.Autocomplete(
                (document.getElementById(moduleName + '_editView_fieldName_agentmanager_mailing_zip')),
                {types: ['geocode']});

            google.maps.event.addListener(autocompleteAMMailingZip, 'place_changed', function () {
                thisInstance.fillInAddress('AgentManagerMailing', autocompleteAMMailingZip);
            });
        }
    //Agents Warehouse Module
	if(moduleName == 'Agents'){
	    if (jQuery('#' + moduleName + '_editView_fieldName_agents_wareaddress1').length) {
		autocompleteAMWarehouseAddr1 = new google.maps.places.Autocomplete(
		    (document.getElementById(moduleName + '_editView_fieldName_agents_wareaddress1')),
		    {types: ['geocode']});

		google.maps.event.addListener(autocompleteAMWarehouseAddr1, 'place_changed', function () {
		    thisInstance.fillInAddress('AgentsWarehouse', autocompleteAMWarehouseAddr1);
		});
	    }

	    if (jQuery('#' + moduleName + '_editView_fieldName_agents_warecity').length) {
		autocompleteAMWarehouseCity = new google.maps.places.Autocomplete(
		    (document.getElementById(moduleName + '_editView_fieldName_agents_warecity')),
		    {types: ['geocode']});

		google.maps.event.addListener(autocompleteAMWarehouseCity, 'place_changed', function () {
		    thisInstance.fillInAddress('AgentsWarehouse', autocompleteAMWarehouseCity);
		});
	    }

	    if (jQuery('#' + moduleName + '_editView_fieldName_agents_warecountry').length) {
		autocompleteAMWarehouseCountry = new google.maps.places.Autocomplete(
		    (document.getElementById(moduleName + '_editView_fieldName_agents_warecountry')),
		    {types: ['geocode']});

		google.maps.event.addListener(autocompleteAMWarehouseCountry, 'place_changed', function () {
		    thisInstance.fillInAddress('AgentsWarehouse', autocompleteAMWarehouseCountry);
		});
	    }

	    if (jQuery('#' + moduleName + '_editView_fieldName_agents_warestate').length) {
		autocompleteAMWarehouseState = new google.maps.places.Autocomplete(
		    (document.getElementById(moduleName + '_editView_fieldName_agents_warestate')),
		    {types: ['geocode']});

		google.maps.event.addListener(autocompleteAMWarehouseState, 'place_changed', function () {
		    thisInstance.fillInAddress('AgentsWarehouse', autocompleteAMWarehouseState);
		});
	    }

	    if (jQuery('#' + moduleName + '_editView_fieldName_agents_warezip').length) {
		autocompleteAWarehouseZip = new google.maps.places.Autocomplete(
		    (document.getElementById(moduleName + '_editView_fieldName_agents_warezip')),
		    {types: ['geocode']});

		google.maps.event.addListener(autocompleteAWarehouseZip, 'place_changed', function () {
		    thisInstance.fillInAddress('AgentsWarehouse', autocompleteAWarehouseZip);
		});
	    }
	}

	if(moduleName == "Orders"){
	    if (jQuery('#' + moduleName + '_editView_fieldName_bill_country').length) {
		    autocompleteBillCountry = new google.maps.places.Autocomplete(
			    (document.getElementById(moduleName + '_editView_fieldName_bill_country')),
			    {types: ['geocode']});

		    google.maps.event.addListener(autocompleteBillCountry, 'place_changed', function () {
			    thisInstance.fillInAddress('Billing', autocompleteBillCountry);
		    });
	    }
	}

	if(moduleName == "Contracts"){
	    if (jQuery('#' + moduleName + '_editView_fieldName_billing_country').length) {
		    autocompleteBillingCountry = new google.maps.places.Autocomplete(
			    (document.getElementById(moduleName + '_editView_fieldName_billing_country')),
			    {types: ['geocode']});

		    google.maps.event.addListener(autocompleteBillingCountry, 'place_changed', function () {
			    thisInstance.fillInAddress('ContractBill', autocompleteBillingCountry);
		    });
	    }
	}
	// if(moduleName == 'WFOperationsTasks') {
	// 	var addressFields = [
	// 		'address',
	// 		'city',
	// 		'state',
	// 		'country',
	// 		'zip'
	// 	];
	//
	// 	jQuery.each(addressFields,function(i, val) {
	// 		if(jQuery('#' + moduleName + '_editView_fieldName_' + val).length) {
	// 			WFOperationsTaskFormTest = new google.maps.places.Autocomplete(
	// 				(document.getElementById(moduleName + '_editView_fieldName_' + val)),
	// 				{ types: ['geocode'] });
	//
	// 			google.maps.event.addListener(WFOperationsTaskFormTest, 'place_changed', function() {
	// 				thisInstance.fillInAddress('WFOperationsTasks', WFOperationsTaskFormTest);
	// 			});
	// 		}
	// 	});
	// }
		//for employees mailing address
		if(moduleName == 'Employees'){
			if (jQuery('#' + moduleName + '_editView_fieldName_employee_mailingaddress1').length) {
				autocompleteEmployeeMailingaddress1 = new google.maps.places.Autocomplete(
					(document.getElementById(moduleName + '_editView_fieldName_employee_mailingaddress1')),
					{types: ['geocode']});

				google.maps.event.addListener(autocompleteEmployeeMailingaddress1, 'place_changed', function () {
					thisInstance.fillInAddress('EmployeeMailing', autocompleteEmployeeMailingaddress1);
				});
			}
			if (jQuery('#' + moduleName + '_editView_fieldName_employee_mailingcity').length) {
				autocompleteEmployeeMailingcity = new google.maps.places.Autocomplete(
					(document.getElementById(moduleName + '_editView_fieldName_employee_mailingcity')),
					{types: ['geocode']});

				google.maps.event.addListener(autocompleteEmployeeMailingcity, 'place_changed', function () {
					thisInstance.fillInAddress('EmployeeMailing', autocompleteEmployeeMailingcity);
				});
			}
			if (jQuery('#' + moduleName + '_editView_fieldName_employee_mailingstate').length) {
				autocompleteEmployeeMailingstate = new google.maps.places.Autocomplete(
					(document.getElementById(moduleName + '_editView_fieldName_employee_mailingstate')),
					{types: ['geocode']});

				google.maps.event.addListener(autocompleteEmployeeMailingstate, 'place_changed', function () {
					thisInstance.fillInAddress('EmployeeMailing', autocompleteEmployeeMailingstate);
				});
			}
			if (jQuery('#' + moduleName + '_editView_fieldName_employee_mailingzip').length) {
				autocompleteEmployeeMailingzip= new google.maps.places.Autocomplete(
					(document.getElementById(moduleName + '_editView_fieldName_employee_mailingzip')),
					{types: ['geocode']});

				google.maps.event.addListener(autocompleteEmployeeMailingzip, 'place_changed', function () {
					thisInstance.fillInAddress('EmployeeMailing', autocompleteEmployeeMailingzip);
				});
			}
			if (jQuery('#' + moduleName + '_editView_fieldName_employee_mailingcountry').length) {
				autocompleteEmployeeMailingcountry= new google.maps.places.Autocomplete(
					(document.getElementById(moduleName + '_editView_fieldName_employee_mailingcountry')),
					{types: ['geocode']});

				google.maps.event.addListener(autocompleteEmployeeMailingcountry, 'place_changed', function () {
					thisInstance.fillInAddress('EmployeeMailing', autocompleteEmployeeMailingcountry);
				});
			}

		}

		//make sure autocomplete containers end up on top of pop-up containers
		jQuery('.pac-container').css('z-index', '500000');

		thisInstance.componentForm = {
			street_number: 'short_name',
			route: 'long_name',
			locality: 'long_name',
			administrative_area_level_1: 'short_name',
			country: 'long_name',
			postal_code: 'short_name'
		};
		thisInstance.originComponentForm = {
			street_address: moduleName + '_editView_fieldName_origin_address1',
			locality: moduleName + '_editView_fieldName_origin_city',
			administrative_area_level_1: moduleName + '_editView_fieldName_origin_state',
			country: moduleName + '_editView_fieldName_origin_country',
			postal_code: moduleName + '_editView_fieldName_origin_zip'
		};
		thisInstance.destinationComponentForm = {
			street_address: moduleName + '_editView_fieldName_destination_address1',
			locality: moduleName + '_editView_fieldName_destination_city',
			administrative_area_level_1: moduleName + '_editView_fieldName_destination_state',
			country: moduleName + '_editView_fieldName_destination_country',
			postal_code: moduleName + '_editView_fieldName_destination_zip'
		};
		thisInstance.accountShipComponentForm = {
			street_address: moduleName + '_editView_fieldName_ship_street',
			locality: moduleName + '_editView_fieldName_ship_city',
			administrative_area_level_1: moduleName + '_editView_fieldName_ship_state',
			country: moduleName + '_editView_fieldName_ship_country',
			postal_code: moduleName + '_editView_fieldName_ship_code'
		};
		thisInstance.accountBillComponentForm = {
			street_address: 'bill_street',
			locality: moduleName + '_editView_fieldName_bill_city',
			administrative_area_level_1: moduleName + '_editView_fieldName_bill_state',
			country: moduleName + '_editView_fieldName_bill_country',
			postal_code: moduleName + '_editView_fieldName_bill_code'
		};
		thisInstance.billingComponentForm = {
			street_address: moduleName + '_editView_fieldName_bill_street',
			locality: moduleName + '_editView_fieldName_bill_city',
			administrative_area_level_1: moduleName + '_editView_fieldName_bill_state',
			country: moduleName + '_editView_fieldName_bill_country',
			postal_code: moduleName + '_editView_fieldName_bill_code'
		};
		thisInstance.contractBillComponentForm = {
			street_address: moduleName + '_editView_fieldName_billing_address1',
			locality: moduleName + '_editView_fieldName_billing_city',
			administrative_area_level_1: moduleName + '_editView_fieldName_billing_state',
			country: moduleName + '_editView_fieldName_billing_country',
			postal_code: moduleName + '_editView_fieldName_billing_zip'
		};
		thisInstance.agentComponentForm = {
			street_address: moduleName + '_editView_fieldName_agent_address1',
			locality: moduleName + '_editView_fieldName_agent_city',
			administrative_area_level_1: moduleName + '_editView_fieldName_agent_state',
			country: moduleName + '_editView_fieldName_agent_country',
			postal_code: moduleName + '_editView_fieldName_agent_zip'
		};
		thisInstance.vanlineComponentForm = {
			street_address: moduleName + '_editView_fieldName_vanline_address1',
			locality: moduleName + '_editView_fieldName_vanline_city',
			administrative_area_level_1: moduleName + '_editView_fieldName_vanline_state',
			country: moduleName + '_editView_fieldName_vanline_country',
			postal_code: moduleName + '_editView_fieldName_vanline_zip'
		};
		thisInstance.extraStopsComponentForm = {
			street_address: moduleName + '_editView_fieldName_extrastops_address1',
			locality: moduleName + '_editView_fieldName_extrastops_city',
			administrative_area_level_1: moduleName + '_editView_fieldName_extrastops_state',
			country: moduleName + '_editView_fieldName_extrastops_country',
			postal_code: moduleName + '_editView_fieldName_extrastops_zip'
		};
		thisInstance.transfereeComponentForm = {
			street_address: moduleName + '_editView_fieldName_transferees_address1',
			locality: moduleName + '_editView_fieldName_transferees_city',
			administrative_area_level_1: moduleName + '_editView_fieldName_transferees_state',
			country: moduleName + '_editView_fieldName_transferees_country',
			postal_code: moduleName + '_editView_fieldName_transferees_zip'
		};
		thisInstance.addressComponentForm = {
			street_address: moduleName + '_editView_fieldName_address1',
			locality: moduleName + '_editView_fieldName_city',
			administrative_area_level_1: moduleName + '_editView_fieldName_state',
			country: moduleName + '_editView_fieldName_country',
			postal_code: moduleName + '_editView_fieldName_zip'
		};
    thisInstance.WFWarehousesComponentForm = {
        street_address: moduleName + '_editView_fieldName_address',
        locality: moduleName + '_editView_fieldName_city',
        administrative_area_level_1: moduleName + '_editView_fieldName_state',
        country: moduleName + '_editView_fieldName_country',
        postal_code: moduleName + '_editView_fieldName_postal_code'
    };
		thisInstance.leadsComponentForm = {
			street_address: moduleName + '_editView_fieldName_lane',
			locality: moduleName + '_editView_fieldName_city',
			administrative_area_level_1: moduleName + '_editView_fieldName_state',
			country: moduleName + '_editView_fieldName_country',
			postal_code: moduleName + '_editView_fieldName_code'
		};
		thisInstance.transferee1ComponentForm = {
			street_address: moduleName + '_editView_fieldName_transferees_saddress1',
			locality: moduleName + '_editView_fieldName_transferees_scity',
			administrative_area_level_1: moduleName + '_editView_fieldName_transferees_sstate',
			country: moduleName + '_editView_fieldName_transferees_scountry',
			postal_code: moduleName + '_editView_fieldName_transferees_szip'
		};
		thisInstance.mailingComponentForm = {
			street_address: moduleName + '_editView_fieldName_mailingstreet',
			locality: moduleName + '_editView_fieldName_mailingcity',
			administrative_area_level_1: moduleName + '_editView_fieldName_mailingstate',
			country: moduleName + '_editView_fieldName_mailingcountry',
			postal_code: moduleName + '_editView_fieldName_mailingzip'
		};
		thisInstance.otherComponentForm = {
			street_address: moduleName + '_editView_fieldName_otherstreet',
			locality: moduleName + '_editView_fieldName_othercity',
			administrative_area_level_1: moduleName + '_editView_fieldName_otherstate',
			country: moduleName + '_editView_fieldName_othercountry',
			postal_code: moduleName + '_editView_fieldName_otherzip'
		};
		thisInstance.employeeMailingForm = {
			street_address: moduleName + '_editView_fieldName_employee_mailingaddress1',
			locality: moduleName + '_editView_fieldName_employee_mailingcity',
			administrative_area_level_1: moduleName + '_editView_fieldName_employee_mailingstate',
			country: moduleName + '_editView_fieldName_employee_mailingcountry',
			postal_code: moduleName + '_editView_fieldName_employee_mailingzip'
		};
		thisInstance.agentManagerMailingForm = {
		    street_address: moduleName + '_editView_fieldName_agentmanager_mailing_address_1',
		    locality: moduleName + '_editView_fieldName_agentmanager_mailing_city',
		    administrative_area_level_1: moduleName + '_editView_fieldName_agentmanager_mailing_state',
		    country: moduleName + '_editView_fieldName_agentmanager_mailing_country',
		    postal_code: moduleName + '_editView_fieldName_agentmanager_mailing_zip'
		};
		thisInstance.agentWarehouseForm = {
		    street_address: moduleName + '_editView_fieldName_agents_wareaddress1',
		    locality: moduleName + '_editView_fieldName_agents_warecity',
		    administrative_area_level_1: moduleName + '_editView_fieldName_agents_warestate',
		    country: moduleName + '_editView_fieldName_agents_warecountry',
		    postal_code: moduleName + '_editView_fieldName_agents_warezip'
		};
		// thisInstance.WFOperationsTasksForm = {
		// 		street_address: moduleName + '_editView_fieldName_address',
		// 		locality: moduleName + '_editView_fieldName_city',
		// 		administrative_area_level_1: moduleName + '_editView_fieldName_state',
		// 		country: moduleName + '_editView_fieldName_country',
		// 		postal_code: moduleName + '_editView_fieldName_zip'
		// };
	},

	fillInAddress : function(formType, autocomplete) {
		var thisInstance = this;
		var place = autocomplete.getPlace();
		var street_address = '';
		var form = '';
		if(formType == 'Origin') {
			form = thisInstance.originComponentForm;
		} else if(formType == 'Destination') {
			form = thisInstance.destinationComponentForm;
		} else if(formType == 'Billing') {
			form = thisInstance.billingComponentForm;
		//} else if(formType == 'AccountBill') {
			//form = thisInstance.accountBillComponentForm;
		} else if (formType == 'ContractBill') {
			form = thisInstance.contractBillComponentForm;
		} else if(formType == 'AccountShip') {
			form = thisInstance.accountShipComponentForm;
		} else if(formType == 'Agent') {
			form = thisInstance.agentComponentForm;
		} else if(formType == 'Vanline') {
			form = thisInstance.vanlineComponentForm;
		}else if(formType == 'ExtraStops') {
			form = thisInstance.extraStopsComponentForm;
		} else if(formType == 'Transferees') {
			form = thisInstance.transfereesComponentForm;
		} else if(formType == 'Address') {
			form = thisInstance.addressComponentForm;
		} else if(formType == 'LeadAddress') {
			form = thisInstance.leadsComponentForm;
		} else if(formType == 'Mailing') {
			form = thisInstance.mailingComponentForm;
		} else if(formType == 'Other') {
			form = thisInstance.otherComponentForm;
		}else if (formType == 'EmployeeMailing') {
			form = thisInstance.employeeMailingForm;
		} else if (formType == 'WFWarehouses'){
		    form = thisInstance.WFWarehousesComponentForm;
    // } else if(formType == 'WFOperationsTasks') {
		// 		form = thisInstance.WFOperationsTasksForm;
		} else if(formType == 'AgentManagerMailing') {
		    form = thisInstance.agentManagerMailingForm;
		} else if(formType == 'AgentsWarehouse') {
		    form = thisInstance.agentWarehouseForm;
        }
		jQuery(':focus').trigger('blur');
		console.log(form);
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
				if(addressType == 'street_number' && place.address_components[i][thisInstance.componentForm[addressType]] != '') {
					hasAddress = true;
					street_address = place.address_components[i][thisInstance.componentForm[addressType]];
				}
				else if(addressType == 'route') {
					hasRoute = true;
					street_address = street_address + ' ' + place.address_components[i][thisInstance.componentForm[addressType]];
				}
				else if(thisInstance.componentForm[addressType]) {
                    hasCity = true;
					if(addressType == 'locality') {
						hasCity = true;
					} else if(addressType == 'administrative_area_level_1') {
                        hasState = true;
					} else if(addressType == 'postal_code') {
						hasZip = true;
					}
					var val = place.address_components[i][thisInstance.componentForm[addressType]];
					if(addressType == 'locality' && val.substring(0, 3) == 'St ') {
						val = 'Saint '+val.substring(3);
					}
					if(jQuery('#'+form[addressType]).length) {
						var field = jQuery('#'+form[addressType]);
						field.val(val);
						field.trigger('propertychange');
						field.trigger('change');

						field.validationEngine('validate');
					}

					if(jQuery('select[name="bill_country"]') && addressType == 'country' &&
                        formType == 'Billing'&&jQuery('[name="module"]').val()=='Accounts'){

						jQuery('select[name="bill_country"]').find('option[value="'+val+'"]').prop('selected', true);
						jQuery('select[name="bill_country"]').trigger('liszt:updated');
					}
                    if(jQuery('select[name="ship_country"]') && addressType == 'country' &&
                        formType == 'AccountShip'&&jQuery('[name="module"]').val()=='Accounts'){

                        jQuery('select[name="ship_country"]').find('option[value="'+val+'"]').prop('selected', true);
                        jQuery('select[name="ship_country"]').trigger('liszt:updated');
                    }
					if(jQuery('select[name="origin_country"]') && addressType == 'country' && formType == 'Origin'){
						jQuery('select[name="origin_country"]').find('option[value="'+val+'"]').prop('selected', true);
						jQuery('select[name="origin_country"]').trigger('liszt:updated');
					}
					if(jQuery('select[name="destination_country"]') && addressType == 'country' && formType == 'Destination'){
						jQuery('select[name="destination_country"]').find('option[value="'+val+'"]').prop('selected', true);
						jQuery('select[name="destination_country"]').trigger('liszt:updated');
					}
				}
			}

			if(!hasAddress && !hasRoute && jQuery('#'+form['street_address']).val() != 'Will Advise') {
				/*
				Removed below because it was removing the street address when the user enters a zip code and clicks
				a result.

				... which is good, so I've added it back
				Update: Sirva does not want this, conditionalizing.
				... but apparently doing the right thing is the wrong thing
				 */
				//jQuery('#'+form['street_address']).val('');
			} else if(jQuery('#'+form['street_address']).val() != 'Will Advise'){
				jQuery('#'+form['street_address']).val(street_address);
			}
			if(!hasCity) {
				jQuery('#'+form['locality']).val('');
			}
			if(!hasState) {
				jQuery('#'+form['administrative_area_level_1']).val('');
			}
			if(!hasZip) {
				jQuery('#'+form['postal_code']).val('');
			}

			//trigger Lookup Postal Code to appear after google api populates an address block
			if (hasState && hasCity && !hasZip) {
				// only need to trigger one of the possible fields
                // and only if there's a city and state and no zip

				//var field = jQuery('#'+form['locality']);
				//field.trigger('change');
				var field2 = jQuery('#'+form['administrative_area_level_1']);
				field2.trigger('change');
			}
		}

		thisInstance.setAddressLabelsAutoFill();
	},

	/**
	 * @Purpose autocomplete functionality for ReverseAddressLookup.php
	 * @Description Takes user input in the form of city/state, returns an
	 *              array of zip codes with in a city/state area and sends
	 * 				the array back to php as a json object.
	*/
	initializeReverseZipAutoFill : function(moduleName) {
        // Broken out into new JS for easier maintaining.
        Zip_Auto_Fill_Js.I().initializeForModule(moduleName);
	},

	getForm : function() {
		if(this.formElement == false){
			this.setForm(jQuery('#EditView'));
		}
		return this.formElement;
	},

	setForm : function(element){
		this.formElement = element;
		return this;
	},

    // Putting this here, other things need it now.
    // Supply the type of the agent you need, and the callbacks for each case.
    findParticipatingAgent: function(name_to_find, found_cb, not_found_cb) {
        jQuery('select[name^="agent_type_"]').each(function() {
            var selectedOption = $(this).val();
            var selectedName = $(this).find('option[value="' + selectedOption + '"]').text();
            var nameTag = $(this).attr('name');
            // IE doesn't support negative values in substr.
            var selectedId = nameTag.substr(nameTag.length - 1);
            if (typeof found_cb == 'function' && name_to_find == selectedName) {
                found_cb.call(this, selectedOption, selectedName, selectedId);
            } else if (typeof not_found_cb == 'function') {
                not_found_cb.call(this, selectedOption, selectedName, selectedId);
            }
        });
    },

	getPopUpParams : function(container) {
		var params = {};
		var sourceModule = app.getModuleName();
		var popupReferenceModule = jQuery('input[name="popupReferenceModule"]',container).val();
		var sourceFieldElement = jQuery('input[class="sourceField"]',container);
		var sourceField = sourceFieldElement.attr('name');
		var sourceRecordElement = jQuery('input[name="record"]');
		var sourceRecordId = '';
		if(sourceRecordElement.length > 0) {
			sourceRecordId = sourceRecordElement.val();
		}

		var isMultiple = false;
		if(sourceFieldElement.data('multiple') == true){
			isMultiple = true;
		}

		var params = {
			'module' : popupReferenceModule,
			'src_module' : sourceModule,
			'src_field' : sourceField,
			'src_record' : sourceRecordId
		};

		if(isMultiple) {
			params.multi_select = true ;
		}
		return params;
	},

	openPopUp : function(e){
		var thisInstance = this;
		var parentElem = jQuery(e.target).closest('td');

		//var params = this.getPopUpParams(parentElem);
        var params = Vtiger_Edit_Js.I().getPopUpParams(parentElem);
		if (params === false) {
			return;
		}

		var isMultiple = false;
		if(params.multi_select) {
			isMultiple = true;
		}

		// check agentid select exists
		if (jQuery('select[name="agentid"]').length > 0) {
			params['agentId'] = jQuery('select[name="agentid"]').val();
		}

		var sourceFieldElement = jQuery('input[class="sourceField"]',parentElem);

		var prePopupOpenEvent = jQuery.Event(Vtiger_Edit_Js.preReferencePopUpOpenEvent);
		sourceFieldElement.trigger(prePopupOpenEvent);

		if(prePopupOpenEvent.isDefaultPrevented()) {
			return ;
		}

		var popupInstance =Vtiger_Popup_Js.getInstance();
		popupInstance.show(params,function(data){
			var responseData = JSON.parse(data);
			var dataList = [];
			for(var id in responseData){
				var data = {
					'name' : responseData[id].name,
					'id' : id
				};
				dataList.push(data);
				if(!isMultiple) {
					thisInstance.setReferenceFieldValue(parentElem, data);
				}
			}

			if(isMultiple) {
                    sourceFieldElement.trigger(Vtiger_Edit_Js.refrenceMultiSelectionEvent,{'data':dataList});
			}
                sourceFieldElement.trigger(Vtiger_Edit_Js.postReferenceSelectionEvent,{'data':responseData});
		});
	},

	setReferenceFieldValue : function(container, params) {
		// console.log('fsdafs')
		var sourceField = container.find('input.sourceField').attr('name');
		var fieldElement = container.find('input[name="'+sourceField+'"]');
		var sourceFieldDisplay = sourceField+"_display";
		var fieldDisplayElement = container.find('input[name="'+sourceFieldDisplay+'"]');
		var popupReferenceModule = container.find('input[name="popupReferenceModule"]').val();

		var selectedName = params.name;
		var id = params.id;

		fieldElement.val(id);
		fieldDisplayElement.val(selectedName).attr('readonly',true);
		fieldElement.trigger(Vtiger_Edit_Js.referenceSelectionEvent, {
			'source_module': popupReferenceModule,
			'record': id,
			'selectedName': selectedName
		});

		fieldDisplayElement.validationEngine('closePrompt',fieldDisplayElement);
	},

	proceedRegisterEvents : function(){
		if(jQuery('.recordEditView').length > 0){
			return true;
		}else{
			return false;
		}
	},

	referenceModulePopupRegisterEvent : function(container){
		var thisInstance = this;
		container.on("click",'.relatedPopup',function(e){
			thisInstance.openPopUp(e);
		});
		container.find('.referenceModulesList').chosen().change(function(e){
			var element = jQuery(e.currentTarget);
			var closestTD = element.closest('td').next();
			var popupReferenceModule = element.val();
			var referenceModuleElement = jQuery('input[name="popupReferenceModule"]', closestTD);
			var prevSelectedReferenceModule = referenceModuleElement.val();
			referenceModuleElement.val(popupReferenceModule);

			//If Reference module is changed then we should clear the previous value
			if(prevSelectedReferenceModule != popupReferenceModule) {
				closestTD.find('.clearReferenceSelection').trigger('click');
			}
		});
	},

    batchAddSubtractEvent : function(container){
        var thisInstance = this;
        container.on("click",'.batchAddSubtract',function(e){
            var btnElement  = jQuery(this);
            var targetField = jQuery('#'+btnElement.data('relatedfield'));
            var originalVal       = parseInt(targetField.val());
            if (isNaN(originalVal)){
                originalVal = 0;
                targetField.val(0);
            }
            var message     = '<table class="table table-bordered blockContainer showInlineTable equalSplit block_LBL_NOTE_INFORMATION"><thead><tr>' +
                '<th class="blockHeader" colspan="2">'+app.vtranslate('JS_BATCH_ADD_SUBTRACT_HEADER')+'</th></tr></thead>' +
                '<tbody><tr><td class="fieldLabel medium"><label class="muted pull-right marginRight10px">'+app.vtranslate('JS_BATCH_ADD_SUBTRACT_FIELD')+'</label></td>' +
                '<td class="fieldValue medium">' +
                '<div class="row-fluid"><span class="span10">'+originalVal+'</span>' +
                '</div></td></tr><tr><td class="fieldLabel medium">' +
                '<label class="muted pull-right marginRight10px">'+app.vtranslate('JS_BATCH_ADD_SUBTRACT_OPERATOR')+'</label></td>' +
                '<td class="fieldValue medium"><div class="row-fluid"><span class="span10"><div class="row-fluid input-prepend input-append"><select class="chzn-select chzn-done" name="batch_type" '+(originalVal? '' : 'disabled ')+'>' +
                '<option '+(originalVal? 'selected' :'hidden ' )+' value="">'+app.vtranslate('JS_SELECT_OPTION')+'</option>' +
                '<option '+(originalVal? '' : 'selected ')+'value="add">'+app.vtranslate('JS_BATCH_ADD_SUBTRACT_ADD')+'</option>' +
                '<option '+(originalVal? '' :'hidden ' )+'value="subtract">'+app.vtranslate('JS_BATCH_ADD_SUBTRACT_SUBTRACT')+'</option></select></div></span></div></td></tr>' +
                '<tr><td class="fieldLabel medium"><label class="muted pull-right marginRight10px">'+app.vtranslate('JS_BATCH_ADD_SUBTRACT_SUBTRAHAND_ADDEND')+'</label></td>' +
                '<td class="fieldValue medium"><div class="row-fluid"><span class="span10"><input type="number" class="input-large" name="batch_number" value="0" min="0"></span></div></td></tr>' +
                '</tbody></table>';
            bootbox.confirm(message, function(confirm){
                if(confirm){
                    var modVal = parseInt(jQuery(this).find('[name="batch_number"]').val());
                    if(jQuery(this).find('[name="batch_type"]').val() == 'add'){
                        if(targetField.attr('max') && (originalVal + modVal) > targetField.attr('max')){
                            bootbox.alert(app.vtranslate('JS_BATCH_INVALID_EXCEEDS_MAXIMUM_VALUE'));
                            return false;
                        }
                        targetField.val( originalVal + modVal).trigger('value_change');
                    }else if(jQuery(this).find('[name="batch_type"]').val() == 'subtract'){
                        if(targetField.attr('min') && (originalVal - modVal) < targetField.attr('min')){
                            bootbox.alert(app.vtranslate('JS_BATCH_INVALID_BELOW_MINIMUM_VALUE'));
                            return false;
                        }
                        targetField.val( originalVal - modVal).trigger('value_change');
                    }else{
                        bootbox.alert(app.vtranslate('JS_BATCH_ADD_SUBTRACT_REQUIRED_OPERATOR'));
                        return false;
                    }
                }
            });
        });
    },

	getReferencedModuleName : function(parenElement){
		return jQuery('input[name="popupReferenceModule"]',parenElement).val();
	},

	searchModuleNames : function(params) {
		var aDeferred = jQuery.Deferred();

		if(typeof params.module == 'undefined') {
			params.module = app.getModuleName();
		}

		if(typeof params.action == 'undefined') {
			params.action = 'BasicAjax';
		}

		// check agentid select exists
		if (jQuery('select[name="agentid"]').length > 0) {
			params.agentId = jQuery('select[name="agentid"]').val();
		}

		AppConnector.request(params).then(
			function(data){
				aDeferred.resolve(data);
			},
			function(error){
				//TODO : Handle error
				aDeferred.reject();
			}
			);
		return aDeferred.promise();
	},

	/**
	 * Function to get reference search params
	 */
	getReferenceSearchParams : function(element){
		var tdElement = jQuery(element).closest('td');
		var params = {};
		var searchModule = this.getReferencedModuleName(tdElement);
		params.search_module = searchModule;
		return params;
	},

	/**
	 * Function which will handle the reference auto complete event registrations
	 * @params - container <jQuery> - element in which auto complete fields needs to be searched
	 */
	registerAutoCompleteFields : function(container) {
		var thisInstance = this;
        var autoCompleteOptions = {
            'minLength' : '3',
            'source' : function(request, response){
                //element will be array of dom elements
                //here this refers to auto complete instance
                var inputElement = jQuery(this.element[0]);
                var searchValue = request.term;
                var params = thisInstance.getReferenceSearchParams(inputElement);
                params.search_value = searchValue;
                params.field_name = inputElement.attr('name');
                thisInstance.searchModuleNames(params).then(function(data){
                    var reponseDataList = [];
                    var serverDataFormat = data.result;
                    if(serverDataFormat.length <= 0) {
                        jQuery(inputElement).val('');
                        serverDataFormat = new Array({
                            'label' : app.vtranslate('JS_NO_RESULTS_FOUND'),
                            'type'  : 'no results'
                        });
                    }
                    for(var id in serverDataFormat){
                        var responseData = serverDataFormat[id];
                        reponseDataList.push(responseData);
                    }
                    response(reponseDataList);
                });
            },
            'select' : function(event, ui ){
                var selectedItemData = ui.item;
                //To stop selection if no results is selected
                if(typeof selectedItemData.type != 'undefined' && selectedItemData.type=="no results"){
                    return false;
                }
                selectedItemData.name = selectedItemData.value;
                var element = jQuery(this);
                var tdElement = element.closest('td');
				if(app.getModuleName() == 'Workflows') {
				    tdElement = element.closest('.conditionRow');
                }
                thisInstance.setReferenceFieldValue(tdElement, selectedItemData);

                var sourceField = tdElement.find('input[class="sourceField"]').attr('name');
                var fieldElement = tdElement.find('input[name="'+sourceField+'"]');

                fieldElement.trigger(Vtiger_Edit_Js.postReferenceSelectionEvent,{'data':selectedItemData});
            },
            'change' : function(event, ui) {
                var element = jQuery(this);
                //if you dont have readonly attribute means the user didnt select the item
                if(element.attr('readonly')== undefined) {
                    element.closest('td').find('.clearReferenceSelection').trigger('click');
                }
            },
            'open' : function(event,ui) {
                //To Make the menu come up in the case of quick create
                jQuery(this).data('autocomplete').menu.element.css('z-index','100001');

            }
        };
		$(document).on('keydown.autoComplete', '.autoComplete', function() {
			$(this).autocomplete(autoCompleteOptions);
		});
		container.find('input.autoComplete').autocomplete(autoCompleteOptions);
	},


	/**
	 * Function which will register reference field clear event
	 * @params - container <jQuery> - element in which auto complete fields needs to be searched
	 */
	registerClearReferenceSelectionEvent : function(container) {
		container.find('.clearReferenceSelection').on('click', function(e){
			var element = jQuery(e.currentTarget);
			var parentTdElement = element.closest('td');
			var fieldNameElement = parentTdElement.find('.sourceField');
			var fieldName = fieldNameElement.attr('name');
			fieldNameElement.val('').trigger('change'); // WHY would you not trigger change?!
			parentTdElement.find('#'+fieldName+'_display').removeAttr('readonly').val('');
			element.trigger(Vtiger_Edit_Js.referenceDeSelectionEvent);
			fieldNameElement.trigger(Vtiger_Edit_Js.referenceDeSelectionEvent);
			e.preventDefault();
		})
	},

	/**
	 * Function which will register event to prevent form submission on pressing on enter
	 * @params - container <jQuery> - element in which auto complete fields needs to be searched
	 */
	registerPreventingEnterSubmitEvent : function(container) {
		container.on('keypress', function(e){
			//Stop the submit when enter is pressed in the form
			var currentElement = jQuery(e.target);
			if(e.which == 13 && (!currentElement.is('textarea'))) {
				e. preventDefault();
			}
		})
	},

	/**
	 * Function which will give you all details of the selected record
	 * @params - an Array of values like {'record' : recordId, 'source_module' : searchModule, 'selectedName' : selectedRecordName}
	 */
	getRecordDetails : function(params) {
		var aDeferred = jQuery.Deferred();
		var url = "index.php?module="+app.getModuleName()+"&action=GetData&record="+params['record']+"&source_module="+params['source_module'];
		AppConnector.request(url).then(
			function(data){
				if(data['success']) {
					aDeferred.resolve(data);
				} else {
					aDeferred.reject(data['message']);
				}
			},
			function(error){
				aDeferred.reject();
			}
			);
		return aDeferred.promise();
	},


	registerTimeFields : function(container) {
		app.registerEventForTimeFields(container);
		app.registerDateTimePicker(container);
	},

	referenceCreateHandler : function(container) {
		var thisInstance = this;
		var postQuickCreateSave  = function(data) {
			var params = {};
			params.name = data.result._recordLabel;
			params.id = data.result._recordId;
            thisInstance.setReferenceFieldValue(container, params);
		};

		var referenceModuleName = this.getReferencedModuleName(container);
		var quickCreateNode = jQuery('#quickCreateModules').find('[data-name="'+ referenceModuleName +'"]');
		if(quickCreateNode.length <= 0) {
			Vtiger_Helper_Js.showPnotify(app.vtranslate('JS_NO_CREATE_OR_NOT_QUICK_CREATE_ENABLED'))
		}
        quickCreateNode.trigger('click',{'callbackFunction':postQuickCreateSave});
	},

	/**
	 * Function which will register event for create of reference record
	 * This will allow users to create reference record from edit view of other record
	 */
	registerReferenceCreate : function(container) {
		var thisInstance = this;
		container.on('click','.createReferenceRecord', function(e){
			var element = jQuery(e.currentTarget);
			var controlElementTd = element.closest('td');

			thisInstance.referenceCreateHandler(controlElementTd);
		})
	},

	/**
	 * Function to register the event status change event
	 */
	registerEventStatusChangeEvent : function(container){
		var followupContainer = container.find('.followUpContainer');
		//if default value is set to Held then display follow up container
		var defaultStatus = container.find('select[name="eventstatus"]').val();
		if(defaultStatus == 'Held'){
			followupContainer.show();
		}
		container.find('select[name="eventstatus"]').on('change',function(e){
			var selectedOption = jQuery(e.currentTarget).val();
			if(selectedOption == 'Held'){
				followupContainer.show();
			} else{
				followupContainer.hide();
			}
		});
	},

    registerCommodityChangeEvent : function(){
        if(!jQuery('input[name="movehq"]').val()){
            return;
        }
        jQuery('select[name="commodities"]').change(function() {
            moduleName = app.getModuleName();
            fieldName = 'business_line';
            if(moduleName == 'Actuals' || moduleName == 'Estimates'){
                fieldName = 'business_line_est';
            }
            loadBlocksByBusinesLine(moduleName, fieldName);
        });
    },

	removeEmployerAssisting : function() {
		var thisInstance = this;
		var type = jQuery('select[name="billing_type"]').val();
		if(type == 'NAT') {
			thisInstance.toggleEmployerAssisting('hide');
		}
		jQuery('select[name="billing_type"]').change(function() {
			if(jQuery(this).val() == 'NAT') {
				thisInstance.toggleEmployerAssisting('hide');
			} else {
				thisInstance.toggleEmployerAssisting('show');
			}
		});
	},

	toggleEmployerAssisting: function(state) {
		if(state == 'hide') {
			jQuery('table[name="LBL_OPPORTUNITY_EMPLOYERASSISTING"]').addClass('hide');
			jQuery('table[name="LBL_LEADS_EMPLOYERASSISTING"]').addClass('hide');
		} else {
			jQuery('table[name="LBL_OPPORTUNITY_EMPLOYERASSISTING"]').removeClass('hide');
			jQuery('table[name="LBL_LEADS_EMPLOYERASSISTING"]').removeClass('hide');
		}
	},

	setAddressLabelsAutoFill: function() {
		//var thisInstance = this;
		//var canadianLabels = {
		//	'origin_state': 'Origin Province',
		//	'origin_zip': 'Origin Postal Code',
		//	'destination_state': 'Destination Province',
		//	'destination_zip': 'Destination Postal Code',
		//};
        //
		//var americanLabels = {
		//	'origin_state': 'Origin State',
		//	'origin_zip': 'Origin Zip',
		//	'destination_state': 'Destination State',
		//	'destination_zip': 'Destination Zip',
		//};
        //
		//var origin = jQuery('select[name="origin_country"]');
		//var destination = jQuery('select[name="destination_country"]');
        //
		//if (origin.val()) {
		//	if (origin.val() == 'Canada') {
		//		thisInstance.changeMoveTypeLabels(canadianLabels, 'origin');
		//	} else {
		//		thisInstance.changeMoveTypeLabels(americanLabels, 'origin');
		//	}
		//} else {
		//	thisInstance.setAddressLabelsAutoFillByMoveType(americanLabels, canadianLabels, 'origin');
		//}
        //
		//if (destination.val()) {
		//	if (destination.val() == 'Canada') {
		//		thisInstance.changeMoveTypeLabels(canadianLabels, 'destination');
		//	} else {
		//		thisInstance.changeMoveTypeLabels(americanLabels, 'destination');
		//	}
		//} else {
		//	thisInstance.setAddressLabelsAutoFillByMoveType(americanLabels, canadianLabels, 'destination');
		//}
	},

    setAddressLabelsAutoFillByMoveType: function(americanLabels, canadianLabels, location) {
		var thisInstance = this;
		var moveType = jQuery('select[name="move_type"]').find('option:selected').val();
		switch (moveType) {
			case 'Local Canada':
			case 'Inter-Provincial':
			case 'Intra-Provincial':
				thisInstance.changeMoveTypeLabels(canadianLabels, location);
				break;
			default:
				thisInstance.changeMoveTypeLabels(americanLabels, location);
				break;
		}
	},

	//@TODO: find why this is here.
	canadianLabels: function() {
		var thisInstance = this;
		var canadianLabels = {
			'origin_state':'Origin Province',
			'origin_zip':'Origin Postal Code',
			'destination_state':'Destination Province',
			'destination_zip':'Destination Postal Code',
		};

		var americanLabels = {
			'origin_state':'Origin State',
			'origin_zip':'Origin Zip',
			'destination_state':'Destination State',
			'destination_zip':'Destination Zip',
		};

		var origin = jQuery('select[name="origin_country"]');
		origin.change(function() {
			if(origin.val()=='Canada') {
				thisInstance.changeMoveTypeLabels(canadianLabels, 'origin');
			} else {
				thisInstance.changeMoveTypeLabels(americanLabels, 'origin');
			}
		});

		var destination = jQuery('select[name="destination_country"]');
		destination.change(function() {
			if(destination.val()=='Canada') {
				thisInstance.changeMoveTypeLabels(canadianLabels, 'destination');
			} else {
				thisInstance.changeMoveTypeLabels(americanLabels, 'destination');
			}
		});
	},

	/*
	 *   Accepts an object with the key being the label of the input field and the value is what you want the label to
	 *   be. If it is a required field it adds the astrisk to the label as well.
	 */
	changeMoveTypeLabels: function(labelObj, type) {
		var req = '<span class="redColor">*</span>';
		$.each(labelObj, function(key, value) {
			if(key.indexOf(type)>-1) {
				if(jQuery('input[name="'+key+'"]').length) {
					var label = jQuery('input[name="' + key + '"]').closest('td').prev().children();
					label.html().indexOf('*') > 0 ? label.html(req + ' ' + value) : label.html(value);
				}
			}
		});
	},

    //@NOTE: Moved to parent because Opportunities_Edit_Js and Leads_Edit_Js used the same function
    registerSourceNameChange : function() {
        if (jQuery('input:hidden[name="source_name"]').length) {
            jQuery('input:hidden[name="source_name"]').on(Vtiger_Edit_Js.referenceSelectionEvent, function () {
                //they pressed yes
                var id = jQuery('input[name="source_name"]').val();
                var url = 'index.php?module=LeadSourceManager&action=PopulateLeadSource&id=' + id;

                AppConnector.request(url).then(
                    function (data) {
                        if (data.success) {
                            var entityData = data.result.data;
                            //set the billing address
                            jQuery('input[name="brand"]').val(entityData['brand']).attr('readonly', 'readonly');
                            jQuery('input[name="program_name"]').val(entityData['source_name']).attr('readonly', 'readonly');
                            //jQuery('input[name="leadsource"]').val(entityData['marketing_channel']).attr('readonly', 'readonly');
                            jQuery('select[name="leadsource"]').val(entityData['marketing_channel']).trigger("liszt:updated");
                            //TFS27543: remove or grey out marketing channel
                            Vtiger_Edit_Js.hideCell(jQuery('select[name="leadsource"]'));
                            //ADDED PER: TFS18796 -- note added because this was removed before as "wrong".
                            jQuery('[name="special_terms"]').val(entityData['program_terms']).attr('readonly', 'readonly');
                        }
                    },
                    function (err) {
                        bootbox.alert('Could not process the request. Please try again later.');
                    }
                );
            });
        }
    },
    registerCloseOnBlurSelect2: function(){
        jQuery(document).on('open', '.select2', function(e){
            var element_name = jQuery(this).attr('name');
			jQuery('.select2[name!="'+element_name+'"]').each(function(){
				jQuery(this).select2('close');
			});
		});

		jQuery(document).on('click', '.chzn-container', function(e){
			jQuery('.select2').each(function(){
				jQuery(this).select2('close');
			});
		});
    },
	/**
	 * Function which will register basic events which will be used in quick create as well
	 *
	 */
	registerBasicEvents : function(container) {
//		this.getPicklistValuesBasedOnOwner();
		this.referenceModulePopupRegisterEvent(container);
        this.batchAddSubtractEvent(container);
		this.removeEmployerAssisting();
		this.registerAutoCompleteFields(container);
		this.registerClearReferenceSelectionEvent(container);
		this.registerPreventingEnterSubmitEvent(container);
		this.registerTimeFields(container);
		//Added here instead of register basic event of calendar. because this should be registered all over the places like quick create, edit, list..
		this.registerEventStatusChangeEvent(container);
		this.registerRecordAccessCheckEvent(container);
		this.registerEventForPicklistDependencySetup(container);
		this.canadianLabels();
        this.registerRecordPreSaveEvent(container);
		if(jQuery('[name="movehq"]').val()) {
			// Actually I think we should still allow search, but just limit it to records already in the DB
			// We will need to add a way to create new placeholder records though
			this.registerAgentChangeForAccountingIntegration(container);
			this.registerAccountingIntegrationPopup(container);
			this.registerAccountingIntegrationAutoComplete(container);
		}
		this.registerCloseOnBlurSelect2();		
	},

	registerAgentChangeForAccountingIntegration: function(container)
	{
		var thisInstance = this;
		container.on('value_change', '[name="agentid"]', function() {
			// TODO: ask the user whether they really want to do this.
			jQuery('.clearReferenceSelectionAccountingIntegration').trigger('click');
			//thisInstance.checkAccountingIntegration();
		});
		if(app.getModuleName() == 'Agents') {
			container.on('value_change', '[name="agentmanager_id"]', function () {
				// TODO: ask the user whether they really want to do this.
				jQuery('.clearReferenceSelectionAccountingIntegration').trigger('click');
				//thisInstance.checkAccountingIntegration();
			});
		}
		//this.checkAccountingIntegration();
	},

	checkAccountingIntegration : function() {
		if(jQuery('#accountingIntegrationActive').length == 0)
		{
			jQuery('.contentsDiv').append('<input type="hidden" id="accountingIntegrationActive">');
		}
		var params = {
			module: app.getModuleName(),
			action: 'AccountingIntegrationActionAjax',
			mode: 'isActive',
			agentid: jQuery('[name="agentid"]').val()
		};

		AppConnector.request(params).then(function (data) {
			if (data.success && data.result.access) {
				jQuery('#accountingIntegrationActive').val(1);
			} else {
				jQuery('#accountingIntegrationActive').val(0);
			}
		});
	},

	registerAccountingIntegrationPopup : function(container){
		var thisInstance = this;
		container.on("click",'.accountingIntegrationRelatedPopup:not(.popupOff)',function(e){
			var parentElem = jQuery(e.target).closest('td');

			var popupReferenceModule = jQuery('input[name="popupReferenceModule"]',parentElem).val();
			var sourceFieldElement = jQuery('input[class="sourceField"]',parentElem);
			var sourceField = sourceFieldElement.attr('name');

			var params = {
				'module' : 'Vtiger',
				'src_module' : popupReferenceModule,
				'src_field' : sourceField,
				'view' : 'AccountingIntegrationPopup',
				'parent_module' : app.getModuleName()
			};

			var agentid = Vtiger_Edit_Js.getAgentId();
			params['agentid'] = agentid;

			var prePopupOpenEvent = jQuery.Event(Vtiger_Edit_Js.preReferencePopUpOpenEvent);
			sourceFieldElement.trigger(prePopupOpenEvent);

			if(prePopupOpenEvent.isDefaultPrevented()) {
				return ;
			}

			var popupInstance =Vtiger_Popup_Js.getInstance();
			popupInstance.show(params,function(data){
				var responseData = JSON.parse(data);
				var dataList = [];
				for(var id in responseData){
					var data = {
						'name' : responseData[id].name,
						'id' : id
					};
					dataList.push(data);
					thisInstance.setReferenceFieldValue(parentElem, data);
				}
				sourceFieldElement.trigger(Vtiger_Edit_Js.postReferenceSelectionEvent,{'data':responseData});
			});
		});
	},
	registerAccountingIntegrationAutoComplete : function(container) {
		var thisInstance = this;
		container.find('input.accountingIntegrationAutoComplete').autocomplete({
			'minLength' : '3',
			'source' : function(request, response){
				//element will be array of dom elements
				//here this refers to auto complete instance
				var inputElement = jQuery(this.element[0]);
				var searchValue = request.term;
				var params = thisInstance.getReferenceSearchParams(inputElement);
				params.search_value = searchValue;
				thisInstance.searchAccountingIntegrationRecord(params).then(function(data){
					if(data.success) {
						var reponseDataList = new Array();
						var serverDataFormat = data.result;
						if (serverDataFormat.length <= 0) {
							jQuery(inputElement).val('');
							serverDataFormat = new Array({
								'label': app.vtranslate('JS_NO_RESULTS_FOUND'),
								'type': 'no results'
							});
						}
						for (var id in serverDataFormat) {
							var responseData = serverDataFormat[id];
							reponseDataList.push(responseData);
						}
						response(reponseDataList);
					}
				});
			},
			'select' : function(event, ui ){
				var selectedItemData = ui.item;
				//To stop selection if no results is selected
				if(typeof selectedItemData.type != 'undefined' && selectedItemData.type=="no results"){
					return false;
				}
				selectedItemData.name = selectedItemData.value;
				var element = jQuery(this);
				var tdElement = element.closest('td');
				thisInstance.setReferenceFieldValue(tdElement, selectedItemData);

				var sourceField = tdElement.find('input[class="sourceField"]').attr('name');
				var fieldElement = tdElement.find('input[name="'+sourceField+'"]');

				var id = selectedItemData.id;
				var data = {};
				data['data'] = {};
				data['data'][id] = selectedItemData;
				fieldElement.trigger(Vtiger_Edit_Js.postReferenceSelectionEvent,data);
			},
			'change' : function(event, ui) {
				var element = jQuery(this);
				//if you dont have readonly attribute means the user didnt select the item
				if(element.attr('readonly')== undefined) {
					element.closest('td').find('.clearReferenceSelectionAccountingIntegration').trigger('click');
				}
			},
			'open' : function(event,ui) {
				//To Make the menu come up in the case of quick create
				jQuery(this).data('autocomplete').menu.element.css('z-index','100001');

			}
		});
	},

	searchAccountingIntegrationRecord : function (params) {
		var aDeferred = jQuery.Deferred();

		if(typeof params.module == 'undefined') {
			params.module = 'Vtiger';
		}

		if(typeof params.action == 'undefined') {
			params.action = 'AccountingIntegrationSearchAjax';
		}

		var agentid = Vtiger_Edit_Js.getAgentId();
		params['agentid'] = agentid;

		AppConnector.request(params).then(
			function(data){
				aDeferred.resolve(data);
			},
			function(error){
				//TODO : Handle error
				aDeferred.reject();
			}
		)
		return aDeferred.promise();
	},

	/**
	 * Function to register event for image delete
	 */
	registerEventForImageDelete : function(){
		var formElement = this.getForm();
		var recordId = formElement.find('input[name="record"]').val();
		formElement.find('.imageDelete').on('click',function(e){
			var element = jQuery(e.currentTarget);
			var parentTd = element.closest('td');
			var imageUploadElement = parentTd.find('[name$="[]"]');
			var fieldInfo = imageUploadElement.data('fieldinfo');
			var mandatoryStatus = fieldInfo.mandatory;
			var imageId = element.closest('div').find('img').data().imageId;
			element.closest('div').remove();
			var exisitingImages = parentTd.find('[name="existingImages"]');
			if(exisitingImages.length < 1 && mandatoryStatus){
				formElement.validationEngine('detach');
				imageUploadElement.attr('data-validation-engine','validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]');
				formElement.validationEngine('attach');
			}

			if(formElement.find('[name=imageid]').length != 0) {
				var imageIdValue = JSON.parse(formElement.find('[name=imageid]').val());
				imageIdValue.push(imageId);
				formElement.find('[name=imageid]').val(JSON.stringify(imageIdValue));
			} else {
				var imageIdJson = [];
				imageIdJson.push(imageId);
				formElement.append('<input type="hidden" name="imgDeleted" value="true" />');
				formElement.append('<input type="hidden" name="imageid" value="'+JSON.stringify(imageIdJson)+'" />');
			}
		});
	},

	triggerDisplayTypeEvent : function() {
		var widthType = app.cacheGet('widthType', 'narrowWidthType');
		if(widthType) {
			var elements = jQuery('#EditView').find('td');
			elements.addClass(widthType);
		}
	},
    validateMoveType: function() {
        var moveType = jQuery('select[name="move_type"]').val();
        var orgState = jQuery('input[name="origin_state"]').val();
        var destState = jQuery('input[name="destination_state"]').val();
        var error = false;
        var canadianError = false;
        var state = '';
        //from https://gist.github.com/tleen/6395808
        var CanadianProvinces = ['AB', 'BC', 'MB', 'NB', 'NL', 'NT', 'NS', 'NU', 'ON', 'PE', 'QC', 'SK', 'YT'];
        switch(moveType) {
            case 'Local Canada':
                if(orgState != '' && destState != '') {
                    if($.inArray(orgState.toUpperCase(),CanadianProvinces) == -1 || $.inArray(destState.toUpperCase(),CanadianProvinces) == -1)  {
                        error = true;
                        canadianError = true;
                    }
                }
                break;
            case 'Alaska':
                if(orgState != '' && destState != '') {
                    if(orgState.toLowerCase() != 'ak' || destState.toLowerCase() != 'ak')  {
                        error = true;
                        state = 'AK';
                    }
                }
                break;
            case 'Hawaii':
                if(orgState != '' && destState != '') {
                    if(orgState.toLowerCase() != 'hi' || destState.toLowerCase() != 'hi') {
                        error = true;
                        state = 'HI';
                    }
                }
                break;
            default:
        }

        if(error) {

            if(!jQuery('.bootbox-alert').hasClass('in')) {
                if(canadianError){
                    bootbox.alert('The origin and destination location should be set to a Canadian location for the move type selected.');
                }
                else{
                    bootbox.alert('The origin and destination state should be set to '+state+' for the move type selected.');
                }

            }
            return false;
        }
        return true;
    },
	registerSubmitEvent: function() {
		var editViewForm = this.getForm();
		var thisInstance = this;

		editViewForm.submit(function(e){
			var module = jQuery(e.currentTarget).find('[name="module"]').val();

            if(module=='Leads'||module=='Estimates'||module=='Opportunities'){
                if(typeof thisInstance.validateMoveType == 'function' && thisInstance.validateMoveType() === false) {
                    return false;
                }
            }

			//Form should submit only once for multiple clicks also

			if(typeof editViewForm.data('submit') != "undefined") {
				return false;
			} else {
				if(editViewForm.validationEngine('validate')) {
                    //remove phone number formating
                    jQuery('.phone-field').each(function() {
                        jQuery(this).val( jQuery(this).val().replace(/\D/g,'') );
                    });
					//Once the form is submiting add data attribute to that form element
					editViewForm.data('submit', 'true');
					//on submit form trigger the recordPreSave event
					var recordPreSaveEvent = jQuery.Event(Vtiger_Edit_Js.recordPreSave);
                    editViewForm.trigger(recordPreSaveEvent, {'value' : 'edit'});
					if(recordPreSaveEvent.isDefaultPrevented()) {
						//If duplicate record validation fails, form should submit again
						editViewForm.removeData('submit');
						e.preventDefault();
					}
				} else {
					//If validation fails, form should submit again
					editViewForm.removeData('submit');
					// to avoid hiding of error message under the fixed nav bar
					app.formAlignmentAfterValidation(editViewForm);
				}
			}
		});
	},

	/*
	 * Function to check the view permission of a record after save
	 */

	registerRecordAccessCheckEvent : function(form) {

		form.on(Vtiger_Edit_Js.recordPreSave, function(e, data) {
			var assignedToSelectElement = jQuery('[name="assigned_user_id"]',form);
			if(assignedToSelectElement.data('recordaccessconfirmation') == true ||
				jQuery('[name="module"]').val() == 'Contracts' ||
				jQuery('[name="module"]').val() == 'AgentManager' ||
				jQuery('[name="module"]').val() == 'Estimates' ||
				jQuery('[name="module"]').val() == 'Cubesheets' ||
				jQuery('[name="module"]').val() == 'Surveys' ||
				jQuery('[name="module"]').val() == 'OPList' ||
				jQuery('[name="module"]').val() == 'Leads' ||
				jQuery('[name="module"]').val() == 'Orders' ||
				jQuery('[name="module"]').val() == 'Estimates' ||
				jQuery('[name="module"]').val() == 'Opportunities') {
				return;
			}else{
				if(assignedToSelectElement.data('recordaccessconfirmationprogress') != true) {
					var recordAccess = assignedToSelectElement.find('option:selected').data('recordaccess');
					if(recordAccess == false) {
						var message = app.vtranslate('JS_NO_VIEW_PERMISSION_AFTER_SAVE');
						Vtiger_Helper_Js.showConfirmationBox({'message' : message}).then(
							function(e) {
								assignedToSelectElement.data('recordaccessconfirmation',true);
								assignedToSelectElement.removeData('recordaccessconfirmationprogress');
								form.append('<input type="hidden" name="returnToList" value="true" />');
								form.submit();
							},
							function(error, err){
								assignedToSelectElement.removeData('recordaccessconfirmationprogress');
								e.preventDefault();
							});
						assignedToSelectElement.data('recordaccessconfirmationprogress',true);
					} else {
						return true;
					}
				}
			}
			e.preventDefault();
		});
	},

	/**
	 * Function to register event for setting up picklistdependency
	 * for a module if exist on change of picklist value
	 */
	registerEventForPicklistDependencySetup : function(container){
		var picklistDependcyElemnt = jQuery('[name="picklistDependency"]',container);
		if(picklistDependcyElemnt.length <= 0) {
			return;
		}
		var picklistDependencyMapping = JSON.parse(picklistDependcyElemnt.val());

		var sourcePicklists = Object.keys(picklistDependencyMapping);
		if(sourcePicklists.length <= 0){
			return;
		}

		var sourcePickListNames = "";
		for(var i=0;i<sourcePicklists.length;i++){
			sourcePickListNames += '[name="'+sourcePicklists[i]+'"],';
		}
		var sourcePickListElements = container.find(sourcePickListNames);

		sourcePickListElements.on('change',function(e){
			var currentElement = jQuery(e.currentTarget);
			var sourcePicklistname = currentElement.attr('name');

			var configuredDependencyObject = picklistDependencyMapping[sourcePicklistname];
			var selectedValue = currentElement.val();
			var targetObjectForSelectedSourceValue = configuredDependencyObject[selectedValue];
			var picklistmap = configuredDependencyObject["__DEFAULT__"];

			if(typeof targetObjectForSelectedSourceValue == 'undefined'){
				targetObjectForSelectedSourceValue = picklistmap;
			}
			jQuery.each(picklistmap,function(targetPickListName,targetPickListValues){
				var targetPickListMap = targetObjectForSelectedSourceValue[targetPickListName];
				if(typeof targetPickListMap == "undefined"){
					targetPickListMap = targetPickListValues;
				}
				var targetPickList = jQuery('[name="'+targetPickListName+'"]',container);
				if(targetPickList.length <= 0){
					return;
				}

				var listOfAvailableOptions = targetPickList.data('availableOptions');
				if(typeof listOfAvailableOptions == "undefined"){
					listOfAvailableOptions = jQuery('option',targetPickList);
					targetPickList.data('available-options', listOfAvailableOptions);
				}

				var targetOptions = new jQuery();
				var optionSelector = [];
				optionSelector.push('');
				for(var i=0; i<targetPickListMap.length; i++){
					optionSelector.push(targetPickListMap[i]);
				}

				jQuery.each(listOfAvailableOptions, function(i,e) {
					var picklistValue = jQuery(e).val();
					if(jQuery.inArray(picklistValue, optionSelector) != -1) {
						targetOptions = targetOptions.add(jQuery(e));
					}
				});
				var targetPickListSelectedValue = '';
				var targetPickListSelectedValue = targetOptions.filter('[selected]').val();
				targetPickList.html(targetOptions).val(targetPickListSelectedValue).trigger("liszt:updated");
			})
		});

		//To Trigger the change on load
		sourcePickListElements.trigger('change');
	},

	registerLeavePageWithoutSubmit : function(form){
        InitialFormData = form.serialize();
        window.onbeforeunload = function(e){
            if (InitialFormData != form.serialize() && form.data('submit') != "true") {
                return app.vtranslate("JS_CHANGES_WILL_BE_LOST");
            }
        };
    },

	/**
	 * Function to format phone number field
	 */
    formatPhoneFieldOnChange : function() {
		jQuery('input[name="origin_phone1"]').addClass('phone-field');
		jQuery('input[name="origin_phone2"]').addClass('phone-field');
		jQuery('input[name="origin_fax"]').addClass('phone-field');
		jQuery('input[name="destination_phone1"]').addClass('phone-field');
		jQuery('input[name="destination_phone2"]').addClass('phone-field');
		jQuery('input[name="destination_fax"]').addClass('phone-field');
		jQuery('input[name="otherphone"]').addClass('phone-field');
		jQuery('input[name="contact_phone"]').addClass('phone-field');
		jQuery('input[name^="phone1"]').addClass('phone-field');
		jQuery('input[name^="phone2"]').addClass('phone-field');

        jQuery('.phone-field').on('load, keyup', function() {
			var input = jQuery(this).val().replace(/\D/g,'');
            if(input.length==10) {
                var phone = '('+input.substr(0, 3) + ') ' + input.substr(3, 3) + '-' + input.substr(6, 4);
                jQuery(this).val(phone);
            } else if(jQuery(this).val().length==7) {
                var phone = input.substr(0, 3) + '-' + input.substr(3, 4);
                jQuery(this).val(phone);
            }
        });

		jQuery('.phone-field').each(function() {
			var input = jQuery(this).val().replace(/\D/g,'');
			if(input.length==10) {
				var phone = '('+input.substr(0, 3) + ') ' + input.substr(3, 3) + '-' + input.substr(6, 4);
				jQuery(this).val(phone);
			} else if(jQuery(this).val().length==7) {
				var phone = input.substr(0, 3) + '-' + input.substr(3, 4);
				jQuery(this).val(phone);
			} else {
				jQuery(this).val()
			}
		});

    },

	/*
	 autopopulate survey name on surveys
	 */
	setSurveyName : function() {
		var thisInstance = this;

		if(app.getModuleName() == 'Cubesheets' && jQuery('input[name="cubesheet_name"]').length>0) {
			var today = new Date();
			var dateString =
				("0" + (today.getMonth() + 1)).slice(-2) +
				'-' + today.getDate() +
				'-' + today.getFullYear() +
				' ' + today.getHours() +
				':' + (today.getMinutes() < 10 ? '0' : '') + today.getMinutes();

			// Check if survey has a name associated with it
			var params = {
				'record': jQuery('input[name="sourceRecord"]').val(),
				'source_module': jQuery('input[name="sourceModule"]').val(),
			};
			var name = '';
			var url = "index.php?module="+app.getModuleName()+"&action=GetData&record="+params['record']+"&source_module="+params['source_module'];
			AppConnector.request(url).then(
				function(data){
					if(data.result.data.potentialname.length>0) {
						// A name is associated with the survey
						var nameArray = data.result.data.potentialname.split(' ');
						var lastName = nameArray[nameArray.length - 1].toUpperCase();
						var firstInit = nameArray[0].substr(0, 1).toUpperCase();
						name = lastName + firstInit + ' ' + dateString;

						jQuery('input[name="cubesheet_name"]').val(name);
					} else {
						jQuery('input[name="cubesheet_name"]').val('Survey '+dateString);
					}
				},
				function(error){
					jQuery('input[name="cubesheet_name"]').val('Survey '+dateString);
				}
			)
		}
	},

	registerBlockAnimationEvent : function(){
		var contentsHolder = jQuery('.editViewContainer');
			contentsHolder.on('click', '.blockToggle', function(e) {
			var currentTarget =  jQuery(e.currentTarget);
			var closestBlock = currentTarget.closest('table[blocktoggleid]');
			var blockToggleId;
			var bodyContents;
			if (closestBlock.length == 0) {
				closestBlock = currentTarget.closest('table');
			} else {
				blockToggleId = closestBlock.attr('blocktoggleid');
			}
			if (typeof currentTarget.attr('blocktoggleid') !== 'undefined') {
				blockToggleId = currentTarget.attr('blocktoggleid');
			}
			if (typeof blockToggleId !== 'undefined') {
				bodyContents = closestBlock.find("[blocktoggleid='" + blockToggleId + "']'");
			} else {
				bodyContents = closestBlock.find('tbody');
			}

			var hideHandler = function() {
				bodyContents.hide('slow').promise().done(
					function(){
						Vtiger_Header_Js.getInstance().adjustContentHeight()
					}
				);
			};
			var showHandler = function() {
				bodyContents.show().promise().done(
					function(){
						Vtiger_Header_Js.getInstance().adjustContentHeight()
					}
				);
			};
			var data = currentTarget.data();
			if(data.mode == 'show'){
				hideHandler();
				currentTarget.hide();
				currentTarget.parent().find("[data-mode='hide']").show();
			}else{
				showHandler();
				currentTarget.hide();
				currentTarget.parent().find("[data-mode='show']").show();
				// might need to remove/edit this
				if (currentTarget.closest('div').parent().attr('id') == 'inline_content') {
					closestBlock.siblings().each(function () {
						jQuery(this).find("[data-mode='hide']").show();
						jQuery(this).find("[data-mode='show']").hide();
					});
				}
			}
		});

	},

	registerReadOnlyCopyEvent: function (container) {
		jQuery(container).on('change', 'select.forceHidden', function (){
			var dest = jQuery(this).siblings('input[name="rs_' + jQuery(this).attr('name') +'_disp"]');
			var val = jQuery('option:selected', this).text();
			dest.val(val);
		});
	},

	registerEventsForMultipicklistall : function(form) {
		form.find('select.multipicklistall').on('focus', function (e) {
			var element=jQuery(e.currentTarget);
			element.data('pre-values', element.val());
		}).change(function(e) {
			var element=jQuery(e.currentTarget);
			var preVals=element.data('pre-values');
			var currentVals=element.val();
			if(jQuery.inArray("All",preVals) !== -1 && typeof preVals != 'undefined') {
				// Remove "All"
				element.find('option[value="All"]').prop('selected',false);
				element.select2();
			}else{
				if(jQuery.inArray("All",currentVals) !== -1 && typeof currentVals != 'undefined'){
					// Remove all values != "All"
					element.find('option[value!="All"]').prop('selected',false);
					element.select2();
				}
			}
			element.data('pre-values', element.val());
		});
		form.find('select.multipicklistall').trigger("change");
	},

	registerEventsForBusinessLine2: function () {
		var thisInstance = this;
		var editViewForm = this.getForm();
		if(jQuery('select[name="business_line2"]').length == 0)
		{
			return;
		}
		// Hide Business Line
		var moduleName = app.getModuleName();
		if (moduleName != 'OrdersTask') {
			Vtiger_Edit_Js.hideCell(editViewForm.find('select[name="business_line"]'));
			// register change event for Business Line est 2
			editViewForm.on("change", 'select[name="business_line2"]', function () {
				var selectedVal = jQuery(this).val();
				var business_line_val = thisInstance.businessLineMapping[selectedVal];
				jQuery('select[name="business_line"]').find('option[value="' + business_line_val + '"]').prop('selected', true);
				editViewForm.find('select[name="business_line"]').trigger("liszt:updated");
				editViewForm.find('select[name="business_line"]').trigger("change");
			});
		}
	},
	registerEventForOwnerField: function () {
		var thisInstance = this;
		var form = thisInstance.getForm();
		form.on('change', 'select[name=agentid]', function () {
			var customFilters = {};
			var agentid = jQuery(this).val();
			var referencemultipicklistall = form.find('input.referencemultipicklistall');
			customFilters['agentid'] = agentid;
			referencemultipicklistall.data('custom-filter', JSON.stringify(customFilters));
            //OT3997 update custompicklist values on agentid change
            thisInstance.updateCustomPicklistValues(agentid);
		});

	},
	loadRefenceMultipicklist: function(){
		var thisInstance = this;
		var form = thisInstance.getForm();
		var customFilters = {};
		var referencemultipicklistall = form.find('input.referencemultipicklistall');
		customFilters['agentid'] = form.find('select[name=agentid]').val();
		referencemultipicklistall.data('custom-filter', JSON.stringify(customFilters));
	},

    updateCustomPicklistValues: function(agentid){
	    var thisInstance = this;
	    var targetModule = jQuery('#module').val();
	    var url = 'index.php?module=PicklistCustomizer&action=ActionAjax&mode=getCustomPicklistValues&targetModule=' + targetModule + '&agentid=' + agentid;
	    AppConnector.request(url).then(
	        function(data) {
	            for(var fieldName in data.result) {
                    if (data.result.hasOwnProperty(fieldName)) {
                        var valueArray = data.result[fieldName];
                        var field = jQuery('select[name="' + fieldName + '"]');
                        field.find('option:gt(0)').remove();
                        for (var databaseValue in valueArray) {
                            if (valueArray.hasOwnProperty(databaseValue)) {
                                translatedValue = valueArray[databaseValue];
                                field.append(jQuery("<option></option>").attr("value", databaseValue).text(translatedValue));
                            }
                        }
                        field.trigger("liszt:updated");
                    }
                }
            },
            function(err) {
	            console.log('Could not get custom picklist values');
            }
        );
    },

	registerValueChangeEvent: function () {
    	// make readonly checkboxes non-toggleable
    	jQuery('div.contentsDiv').on('click', 'input:checkbox:[readonly]', function (e){
    		e.preventDefault();
		});

		jQuery('div.contentsDiv').find('input:not(:checkbox),select').each(function() {
			var obj = jQuery(this);
			obj.data('prev-value', obj.val());
		});
		jQuery('div.contentsDiv').find('input:checkbox').each(function() {
			var obj = jQuery(this);
			obj.data('prev-value', obj.is(':checked') ? 'Yes' : 'No');
		});
        jQuery('div.contentsDiv').on('change', 'input:not(:checkbox),select', function(){
            var obj = jQuery(this);
			if (obj.val() == obj.data('prev-value')) {
                return;
            }
            // set the prev-value after the trigger in case the handler changes it
            obj.trigger('value_change');
            obj.data('prev-value', obj.val());
        });
		jQuery('div.contentsDiv').on('change', 'input:checkbox', function(){
			var obj = jQuery(this);
			if (obj.is(':checked') && obj.data('prev-value') == 'Yes') {
				return;
			}
			// set the prev-value after the trigger in case the handler changes it
			obj.trigger('value_change');
			obj.data('prev-value', obj.is(':checked') ? 'Yes' : 'No');
		});
    },

    registerDateChangeEventForTimezones: function() {
	    var thisInstance = this;
	    var listOfDateFields = {};
	    jQuery('.dateTimeField').each(function() {
	        var dateField = jQuery(this).val();
	        var timeField = jQuery(this).data('fieldname');

	        if(!listOfDateFields.hasOwnProperty(dateField)) {
	            listOfDateFields[dateField] = [];
            }

            listOfDateFields[dateField].push(timeField);
        });

	    for(var prop in listOfDateFields) {
	        if(listOfDateFields.hasOwnProperty(prop)) {
	            //Bind change event onto date field
                jQuery('input[name="'+prop+'"]').on('change', function() {
                    var fieldName = jQuery(this).prop('name');
                    AppConnector.request('index.php?module=Vtiger&action=UpdateTimezonePicklist&date='+jQuery(this).val()).then(function(data) {
                        if(data.success) {
                            var fieldsToUpdate = listOfDateFields[fieldName];
                            for(var i=0; i < fieldsToUpdate.length; i++) {
                                Vtiger_Edit_Js.setPicklistOptions('timefield_' + fieldsToUpdate[i], data.result);
                            }
                        }
                    });
                });
            }
        }
    },

    // So as it turns out, when you update a date field without using the date picker, the date picker won't actually
    // update. And in some cases, the value will revert if the datepicker decides to refresh the input it's attached to.
    // So instead of hunting down everywhere that happens, I'm just going to make typing it in actually update the date
    // picker.
    registerDatePickerUpdate: function() {
        var handler = function() {
            var ele = $(this);

            // Need to call this explicitly since getDate is static.
            // I think...
            var date = Vtiger_Edit_Js.getDate(ele);
            if(typeof date == 'undefined') {
                console.error('Invalid date.');
            }else {
                ele.DatePickerSetDate(date, true);
            }
        }
        $('.dateField').on('value_change', handler);
    },

    registerEvents: function(){
		var editViewForm = this.getForm();
		var statusToProceed = this.proceedRegisterEvents();
		if(!statusToProceed){
			return;
		}
		this.registerValueChangeEvent();
        this.registerDatePickerUpdate();
        this.formatPhoneFieldOnChange();
		this.setSurveyName();
		this.registerBasicEvents(this.getForm());
		this.registerEventForImageDelete();
		this.registerSubmitEvent();
		this.registerLeavePageWithoutSubmit(editViewForm);
		this.registerEventsForMultipicklistall(editViewForm);

		this.registerBlockAnimationEvent();
		this.registerEventsForBusinessLine2();
		this.registerEventForOwnerField();
		this.loadRefenceMultipicklist();

		this.registerReadOnlyCopyEvent(editViewForm);
        this.registerCommodityChangeEvent();
		//jQuery('select[name="move_type"]').validationEngine('showPrompt', 'Invalid Test' , 'error', 'topRight', true);

		app.registerEventForDatePickerFields('#EditView');

		var params = app.validationEngineOptions;
		params.onValidationComplete = function(element,valid){
			if(valid){
				var ckEditorSource = editViewForm.find('.ckEditorSource');
				if(ckEditorSource.length > 0){
					var ckEditorSourceId = ckEditorSource.attr('id');
					var fieldInfo = ckEditorSource.data('fieldinfo');
					var isMandatory = fieldInfo.mandatory;
					var CKEditorInstance = CKEDITOR.instances;
					var ckEditorValue = jQuery.trim(CKEditorInstance[ckEditorSourceId].document.getBody().getText());
					if(isMandatory && (ckEditorValue.length === 0)){
						var ckEditorId = 'cke_'+ckEditorSourceId;
						var message = app.vtranslate('JS_REQUIRED_FIELD');
						jQuery('#'+ckEditorId).validationEngine('showPrompt', message , 'error','topLeft',true);
						return false;
					}else{
						return valid;
					}
				}
				return valid;
			}
			return valid
		};
		editViewForm.validationEngine(params);

		this.registerReferenceCreate(editViewForm);
		this.registerDateChangeEventForTimezones();
	//this.triggerDisplayTypeEvent();
	},

	// Object
	// 	.sourceField (is a name)
	//		.inGuestBlock (true/false, if true guest suffix will be added to source field and target fields)
	// 		.conditions[]
	//			.operator (is,contains,in,gt,lt,always)
	//			.not (true/false: inverts the condition)
	//			.value
	//			.and (condition object {source: name,...})
	//			.or (condition object {source: name,...})
	//			.targetFields[]
	//				.name
	//				.inGuestBlock (true/false, if true guest suffix will be added)
	//				.hide (true/false)
	//				.readonly (true/false)
	//				.pickListOptions ({Value=>DisplayName,...} OR ['Option 1','Option 2',...])
	//				.setValue (set the target field value to this if specified)
    //              .addToLabel append additional text to label
    //              .defaultLabel remove appended text from label
	//			.targetBlocks[]
	//				.label
	//				.hide (true/false)
	applyVisibilityRules: function (rulesObject, isEditView, guestBlockFieldSuffix) {
		// global data to store state so that e.g. a field won't get unhidden if multiple things
		// are hiding it and one thing stops hiding it
		if (typeof guestBlockFieldSuffix == 'undefined') {
			guestBlockFieldSuffix = '';
		}
		if(typeof visibilityDataFields == 'undefined') {
			visibilityDataFields = {};
		}
		if(typeof visibilityDataBlocks == 'undefined') {
			visibilityDataBlocks = {};
		}
		if(typeof rulesHandlerList == 'undefined') {
			rulesHandlerList = [];
		}
		var thisInstance = this;
		var getBlock = function(label) {
			var res = jQuery('#contentHolder_' + label);
			if (res.length == 0) {
				res = jQuery('.block_' + label);
			}
			if (res.length == 0) {
				res = jQuery('[name="' + label + '"]');
			}
			return res;
		};
		var getFieldValueEdit = function (obj) {
			if (obj.hasClass('dateField')) {
				var res = new Date(obj.val());
				if (isNaN(res)) {
					return new Date();
				}
				return res;
			}
			if (obj.is(':checkbox')) {
				if (obj.is(':checked')) {
					return 'Yes';
				}
				else {
					return 'No';
				}
			}
			return obj.val();
		};
		if (isEditView) {
			var getField = function (name) {
				return jQuery('[name="' + name + '"]');
			};
			var getFieldSelector = function(name) {
				return '[name="'+name+'"]';
			};
			var getFieldValue = getFieldValueEdit;
		} else {
			var getField = function (name) {
				var res = jQuery('[id$="_fieldValue_' + name + '"],[name="' + name + '"]');
				return res;
			}
			var getFieldSelector = function(name) {
				return '[id$="_fieldValue_' + name + '"],[name="' + name + '"]';
			};
			var getFieldValue = function (obj) {
				if (obj.is('input')) {
					return getFieldValueEdit(obj.filter(function () {
						return jQuery(this).is('input')
					}));
				}
				// TODO: figure out how to handle multiple matches correctly
				var span = obj.children('span.value:first');
				if (span.data('field-type') == 'date') {
					var res = new Date(jQuery.trim(span.text()));
					if (isNaN(res)) {
						return new Date();
					}
					return res;
				}
				return jQuery.trim(span.text());
			}
		}
		var getCondValue = function(condition, val) {
			if (condition.operator == 'is') {
				var condValue = condition.value == val;
			}
			else if (condition.operator == 'gt') {
				var condValue = condition.value < val;
			}
			else if (condition.operator == 'lt') {
				var condValue = condition.value > val;
			}
			else if (condition.operator == 'contains') {
				var condValue = val.indexOf(condition.value) != -1;
			}
			else if (condition.operator == 'in') {
				var condValue = condition.value.indexOf(val) != -1;
			}
			else if (condition.operator == 'exists') {
                var condValue = typeof val != 'undefined';
			}
            else if (condition.operator == 'set') {
                var condValue = val.length > 0;
            }
			else {
				var condValue = false;
			}
			if (typeof condition.not != 'undefined' && condition.not) {
				condValue = !condValue;
			}
			if (typeof condition.and != 'undefined') {
				var innerVal = getFieldValue(getField(condition.and.source));
				return condValue && getCondValue(condition.and, innerVal);
			}
			if (typeof condition.or != 'undefined') {
				if (condValue) {
					return true;
				}
				var innerVal = getFieldValue(getField(condition.or.source));
				return getCondValue(condition.or, innerVal);
			}
			return condValue;
		};

		var getSourceFieldsImpl = function (subSources, condition) {
			if (typeof condition.source != 'undefined') {
				subSources[condition.source] = getFieldSelector(condition.source);
			}
			if (typeof condition.and != 'undefined') {
				getSourceFieldsImpl(subSources, condition.and);
			}
			if (typeof condition.or != 'undefined') {
				getSourceFieldsImpl(subSources, condition.or);
			}
		};
		var getSourceFields = function(subSources, conditionsList)
		{
			for(var i=0;i<conditionsList.length;++i)
			{
				getSourceFieldsImpl(subSources, conditionsList[i]);
			}
		};

		for (var key in rulesObject) {
			var inGuestBlock = false;
			if(typeof rulesObject[key].inGuestBlock != 'undefined' && rulesObject[key].inGuestBlock) {
				rulesObject[key + guestBlockFieldSuffix] = rulesObject[key];
				delete rulesObject[key];
				key = key + guestBlockFieldSuffix;
				inGuestBlock = true;
			}
			var handlerCreator = function(key,inGuestBlock) {
				return function(){
					var fieldJQ = getField(key);
					if (fieldJQ.length <= 0) {
						return;
					}
					var val = getFieldValue(fieldJQ);
					var conditions = rulesObject[key].conditions;
					for (var i = 0; i < conditions.length; ++i) {
						var condValue = (conditions[i].operator == 'always') ? true : getCondValue(conditions[i], val);
						var targets = conditions[i].targetFields;
						if(typeof targets != 'undefined') {
							for (var j = 0; j < targets.length; ++j) {
								if(inGuestBlock || (typeof targets[j].inGuestBlock != 'undefined' && targets[j].inGuestBlock)) {
									var targetName = targets[j].name + guestBlockFieldSuffix;
								} else {
									var targetName = targets[j].name;
								}
								var targetField = getField(targetName);
								if (typeof visibilityDataFields[targetName] == 'undefined') {
									visibilityDataFields[targetName] = {
										hiddenBy: {},
										readonlyBy: {},
										pickListSet: {},
										defaultPickList: [],
										mandatoryBy: {},
										unmandatoryBy: {},
									};
									// initialize pickListSet so that it will have the right order
									for (var pk in rulesObject) {
										for (var ix = 0; ix < rulesObject[pk].conditions.length; ix++) {
											if (typeof rulesObject[pk].conditions[ix].targetFields == 'undefined') {
												continue;
											}
											for (var jx = 0; jx < rulesObject[pk].conditions[ix].targetFields.length; jx++) {
												visibilityDataFields[targetName].pickListSet[pk + '__' + ix + '__' + jx] = false;
											}
										}
									}
									targetField.find('option').not(':disabled').each(function () {
										visibilityDataFields[targetName].defaultPickList.push(jQuery(this).val());
									});
								}
								var targetInfo = visibilityDataFields[targetName];
								if (typeof targetInfo.hiddenBy[key] == 'undefined') {
									targetInfo.hiddenBy[key] = [];
								}
								if (typeof targetInfo.readonlyBy[key] == 'undefined') {
									targetInfo.readonlyBy[key] = [];
								}
								if(typeof targetInfo.unmandatoryBy[key] == 'undefined')
								{
									targetInfo.unmandatoryBy[key] = [];
								}
								if(typeof targetInfo.mandatoryBy[key] == 'undefined')
								{
									targetInfo.mandatoryBy[key] = [];
								}
								var doUpdate = false;
								if (condValue) {
									if (typeof targets[j].hide != 'undefined') {
										if (targets[j].hide) {
											if (targetInfo.hiddenBy[key][i] != true) {
												targetInfo.hiddenBy[key][i] = true;
												doUpdate = true;
											}
										} else {
											if (targetInfo.hiddenBy[key][i] != false) {
												targetInfo.hiddenBy[key][i] = false;
												doUpdate = true;
											}
										}
									}
                                    if(typeof targets[j].unmandatory != 'undefined')
                                    {
                                        if (targets[j].unmandatory) {
                                            if (targetInfo.unmandatoryBy[key][i] != true) {
                                                targetInfo.unmandatoryBy[key][i] = true;
                                                doUpdate = true;
                                            }
                                        } else {
                                            if (targetInfo.unmandatoryBy[key][i] != false) {
                                                targetInfo.unmandatoryBy[key][i] = false;
                                                doUpdate = true;
                                            }
                                        }
                                    }
                                    if(typeof targets[j].mandatory != 'undefined')
                                    {
                                        if (targets[j].mandatory) {
                                            if (targetInfo.mandatoryBy[key][i] != true) {
                                                targetInfo.mandatoryBy[key][i] = true;
                                                doUpdate = true;
                                            }
                                        } else {
                                            if (targetInfo.mandatoryBy[key][i] != false) {
                                                targetInfo.mandatoryBy[key][i] = false;
                                                doUpdate = true;
                                            }
                                        }
                                    }
									if(typeof targets[j].readonly != 'undefined') {
										if (targets[j].readonly) {
											if (targetInfo.readonlyBy[key][i] != true) {
												targetInfo.readonlyBy[key][i] = true;
												doUpdate = true;
											}
										} else {
											if (targetInfo.readonlyBy[key][i] != false) {
												targetInfo.readonlyBy[key][i] = false;
												doUpdate = true;
											}
										}
									}

									if (typeof targets[j].pickListOptions != 'undefined') {
										if (targetInfo.pickListSet[key + '__' + i + '__' + j] != targets[j].pickListOptions) {
											targetInfo.pickListSet[key + '__' + i + '__' + j] = targets[j].pickListOptions;
											doUpdate = true;
										}
									}
								} else {
									if (typeof targets[j].hide != 'undefined') {
										if (targets[j].hide) {
											if (targetInfo.hiddenBy[key][i] != false) {
												targetInfo.hiddenBy[key][i] = false;
												doUpdate = true;
											}
										}
									}
									if (typeof targets[j].readonly != 'undefined') {
										if (targets[j].readonly) {
											if (targetInfo.readonlyBy[key][i] != false) {
												targetInfo.readonlyBy[key][i] = false;
												doUpdate = true;
											}
										}
									}
									if (typeof targets[j].pickListOptions != 'undefined') {
										if (targetInfo.pickListSet[key + '__' + i + '__' + j] != false) {
											targetInfo.pickListSet[key + '__' + i + '__' + j] = false;
											doUpdate = true;
										}
									}
                                    if(typeof targets[j].unmandatory != 'undefined')
                                    {
                                        if (targetInfo.unmandatoryBy[key][i] != false) {
                                            targetInfo.unmandatoryBy[key][i] = false;
                                            doUpdate = true;
                                        }
                                    }
                                    if(typeof targets[j].mandatory != 'undefined')
                                    {
                                        if (targetInfo.mandatoryBy[key][i] != false) {
                                            targetInfo.mandatoryBy[key][i] = false;
                                            doUpdate = true;
                                        }
                                    }
								}
								if (doUpdate) {
									Vtiger_Edit_Js.updateVisibilityField(targetName, isEditView, getField, getFieldValue);
								}
								if(condValue && typeof targets[j].setValue != 'undefined') {
									Vtiger_Edit_Js.setValue(targetField, targets[j].setValue);
								}
								if(condValue && typeof targets[j].addToLabel != 'undefined' && !targets[j].defaultLabel) {
								    Vtiger_Edit_Js.addOptionalText(targetField, targets[j].addToLabel);
                                }
                                if(condValue && targets[j].defaultLabel == true){
                                    Vtiger_Edit_Js.removeOptionalText(targetField);
                                }
                                if(condValue && targets[j].clearMulti == true){
                                    Vtiger_Edit_Js.clearMultiPicklist(targetField);
                                }
							}
						}
						var targets = conditions[i].targetBlocks;
						if(typeof targets != 'undefined') {
							for (var j = 0; j < targets.length; ++j) {
								var targetLabel = targets[j].label;
								if (typeof visibilityDataBlocks[targetLabel] == 'undefined') {
									visibilityDataBlocks[targetLabel] = {
										hiddenBy: {},
									};
								}
								var targetInfo = visibilityDataBlocks[targetLabel];
								if (typeof targetInfo.hiddenBy[key] == 'undefined') {
									targetInfo.hiddenBy[key] = [];
								}
								var doUpdate = false;
								if (condValue) {
									if (targets[j].hide) {
										if (targetInfo.hiddenBy[key][i] != true) {
											targetInfo.hiddenBy[key][i] = true;
											doUpdate = true;
										}
									}
								} else {
									if (targets[j].hide) {
										if (targetInfo.hiddenBy[key][i] != false) {
											targetInfo.hiddenBy[key][i] = false;
											doUpdate = true;
										}
									}
								}
								if (doUpdate) {
									Vtiger_Edit_Js.updateVisibilityBlock(targetLabel, isEditView, getBlock);
								}
							}
						}
					}
				};
			};

			var handler = handlerCreator(key, inGuestBlock);
			rulesHandlerList.push(handler);
			var subSources = {};
			getSourceFields(subSources, rulesObject[key].conditions);

            var onString = 'value_change'
                +' runRules'
                +' '+Vtiger_Edit_Js.referenceSelectionEvent
                +' '+Vtiger_Edit_Js.referenceDeSelectionEvent;

			for (var k in subSources) {
				jQuery('.contentsDiv').on(onString, subSources[k], handler);
			}
			var sourceField = getFieldSelector(key);
			jQuery('.contentsDiv').on(onString, sourceField, handler);
			handler();
		}
	},

    registerRecordPreSaveEvent : function (form) {
        var thisInstance = this;
        if(typeof form == 'undefined') {
            form = this.getForm();
        }
        var duplicateCheckFields = form.find('#duplicateCheckFields');
        if(
            typeof duplicateCheckFields != 'undefined' &&
            duplicateCheckFields.val() != 'false' &&
            typeof duplicateCheckFields.val() != 'undefined' &&
            duplicateCheckFields.val() != ''
        ) {
            form.on(Vtiger_Edit_Js.recordPreSave, function(e, data) {
                var checkExistName = '';
                var fields={};
                var fieldsToCheck=JSON.parse(form.find('#duplicateCheckFields').val());
                jQuery.each(fieldsToCheck, function (idx, val) {
                    var fieldVal=jQuery('[name="'+val+'"]',form).val();
                    checkExistName +=fieldVal;
                    fields[val] = fieldVal;
                });
                var recordId = jQuery('input[name="record"]',form).val();
                if(!(checkExistName in thisInstance.duplicateCheckCache)) {
                    if(typeof form.data('record-submit') != "undefined") {
                        e.preventDefault();
                        return false;
                    }
                    form.data('record-submit', 'true');

                    thisInstance.checkDuplicateRecord({
                        'fields' : fields,
                        'recordId' : recordId,
                    }).then(
                        function(data){
                            form.removeData('record-submit');
                            thisInstance.duplicateCheckCache[checkExistName] = data.result['success'];
                            //TODO: No, this is fine.  Completely fine.  There is nothing wrong.
                            form.submit();
                            //jQuery('#EditView')[0].submit();
                            return true;
                        },
                        function(data, err){
                            form.removeData('record-submit');
                            thisInstance.duplicateCheckCache[checkExistName] = data.result['success'];
                            thisInstance.duplicateCheckCache['message'] = data.result['message'];
                            var message = app.vtranslate('JS_DUPLICATE_CODE_EXISTS_FOR_ACCOUNT');
                            bootbox.alert(message);
                            return false;
                        }
                    );
                } else {
                    if(thisInstance.duplicateCheckCache[checkExistName] == true){
                        var message = app.vtranslate('JS_DUPLICATE_CODE_EXISTS_FOR_ACCOUNT');
                        bootbox.alert(message);
                        return false;

                    } else {
                        delete thisInstance.duplicateCheckCache[checkExistName];
                        return true;
                    }
                }
                e.preventDefault();
            })
        }
    },

    checkDuplicateRecord : function (details) {
        var aDeferred = jQuery.Deferred();
        var params = {
            'module' : app.getModuleName(),
            'action' : "CheckDuplicate",
            'fields' : details.fields,
            'record' : details.recordId
        }
        AppConnector.request(params).then(
            function(data) {
                if(data.success) {
                    if(data.result['success'] == true){
                        aDeferred.reject(data);
                    } else {
                        aDeferred.resolve(data);
                    }
                }
            },
            function(error,err){
                aDeferred.reject();
            }
        );
        return aDeferred.promise();
    },

	runAllRules: function () {
		visibilityDataFields = {};
		visibilityDataBlocks = {};
		for (var i = 0; i < rulesHandlerList.length; ++i) {
			rulesHandlerList[i]();
		}
	},

	loadContentData: function (data) {
		var newObj = jQuery(data);
		newObj.filter('.sectionContentHolder').each(function(){
			var id = jQuery(this).attr('id');
			// TODO: copy the inputs if possible
			var orig = jQuery('#' + id);
			orig.replaceWith(jQuery(this));
			var newContent = jQuery('#' + id);
			Vtiger_Edit_Js.I().registerBasicEvents(newContent);
			app.registerEventForDatePickerFields(newContent);
			jQuery('.chzn-select', newContent).not('.chzn-done').chosen();
			Vtiger_EditBlock_Js.getInstance().setEventsForGuestBlocks();
		});
		Vtiger_Edit_Js.I().runAllRules();
	},
        //@NOTE @deprecated since OT3997
	getPicklistValuesBasedOnOwner:function () {
		var elementAgent = jQuery('[name="agentid"]');
		var customPicklistFields = {
			'Leads': ['leadsource','reason_cancelled'],
			'Opportunities': ['leadsource','opportunityreason'],
			'Contacts': ['leadsource'],
			'Accounts': ['leadsource'],
		};
		var picklistFields=customPicklistFields[app.getModuleName()];
		if(typeof picklistFields !='undefined') {
			if (elementAgent.length) {
				elementAgent.on('change', function (e) {
					jQuery.each(picklistFields, function (idx, fieldname) {
						//console.log(app.getModuleName());
						if (jQuery('[name="' + fieldname + '"]').length > 0) {
							var params = {
								module: app.getModuleName(),
								action: 'ActionAjax',
								mode: 'getPickListValueForOwner',
								fieldname: fieldname,
								idAgentManager: elementAgent.val()
							};
							var progressIndicatorElement = jQuery.progressIndicator({
								'position': 'html',
								'blockInfo': {
									'enabled': true
	}
							});

							AppConnector.request(params).then(function (data) {
								if(data.success) {
									progressIndicatorElement.progressIndicator({'mode': 'hide'});
									var response = data.result;
									var options = '<option value="">Select an Option</option>';
									var currentField = jQuery('[name="' + fieldname + '"]');
									var selectedVal = currentField.val();
									jQuery.each(response, function (index, picklistObj) {
										var strSelected = '';
										if (selectedVal == picklistObj.value) {
											var strSelect = 'selected';
										}
										options += '<option value="' + picklistObj.value + '" ' + strSelect + '>' + picklistObj.label + '</option>';
									});
									currentField.html(options);
									currentField.trigger('liszt:updated');
								}else{
									progressIndicatorElement.progressIndicator({'mode': 'hide'});
								}
							})
						}
					})
				});
				elementAgent.trigger('change');
			}
		}
	},

    /*
        NOTE: This has been moved from 3 places to this 1, since this is better then maintaining 3 same function.
        Watches for changes to the origin state, destination state, origin country, destination country for changes sets
        move type based on the values matching
     */
    setMoveTypeStateBased: function(country_prefix, state_prefix) {
        // Handle field prefixes since different modules may not have the same naming.
        if(typeof country_prefix == 'undefined') {
            country_prefix = '';
        }else if(country_prefix != '') {
            country_prefix += '_';
        }
        if(typeof state_prefix == 'undefined') {
            state_prefix = '';
        } else if(state_prefix != '') {
            state_prefix += '_';
        }

        // Possible cross border countries.
        var crossBorder = ['united states', 'usa', 'us', 'canada', 'ca'];

        jQuery('input[name="'+state_prefix+'origin_state"], input[name="'+state_prefix+'destination_state"], [name="'+country_prefix+'origin_country"], [name="'+country_prefix+'destination_country"]').change(function() {
            // Gather required values.
            var originState = jQuery('input[name="'+state_prefix+'origin_state"]').val();
            var destinationState = jQuery('input[name="'+state_prefix+'destination_state"]').val();
            var originCountry = jQuery('[name="'+country_prefix+'origin_country"]').val();
            var destinationCountry = jQuery('[name="'+country_prefix+'destination_country"]').val();

            // Move Type field and current value.
            var moveTypeEle = jQuery('select[name="move_type"]');
            var moveType = moveTypeEle.val();

            // Logic casing to step move type to the appropriate type based on origin/destination locations.
            if(originCountry == '' || destinationCountry == '') {
                // They are not done setting the addresses, there's no reason to set the move type around.
                return;
            }else if (originCountry.toLowerCase() === destinationCountry.toLowerCase()) {
                if(originState.toLowerCase() === destinationState.toLowerCase()) {
                    moveType = "Intrastate"
                }else if(originState.toLowerCase() !== destinationState.toLowerCase()){
                    moveType = "Interstate";
                }
            // This is gross and I'm sorry
            // I made it slightly less gross.
            } else if(crossBorder.indexOf(originCountry.toLowerCase()) > -1 && crossBorder.indexOf(destinationCountry.toLowerCase()) > -1) {
                moveType = 'Cross Border';
            } else if (originCountry.toLowerCase() != destinationCountry.toLowerCase()) {
                moveType = 'International';
            }
            moveTypeEle.val(moveType);
            moveTypeEle.addClass('flipped');
            moveTypeEle.trigger('liszt:updated');
            moveTypeEle.trigger('change');
        });
    }
});

function getQueryVariable(variable) {
	var query = window.location.search.substring(1);
	var vars = query.split("&");
	for (var i=0; i<vars.length; i++) {
		var pair = vars[i].split("=");
		if (pair[0] == variable) {
			return decodeURIComponent(pair[1].replace('+',' '));
		}
	}
	return(false);
		}

function loadBlocksByBusinesLine(module, fieldName) {
	var business_lines='';
	var business_line2='';
	/*jQuery('.result-selected').each(function( index ) {
	 business_lines = business_lines + '::' + jQuery.trim(jQuery( this ).text());
	 });*/
	business_lines = jQuery('select[name="' + fieldName + '"]').find(':selected').val();
    if(!business_lines) {
        return;
    }
    var business_line2Element=jQuery('select[name="business_line2"]');
    if(business_line2Element.length >0) {
        business_line2 = business_line2Element.find(':selected').val();
        if(jQuery('input[name="movehq"]').val() && business_line2.indexOf('International') > -1){
            business_lines = 'International Move';
        }
    }
    var commodity = jQuery('select[name="commodities"]').find(':selected').val();
    if(commodity){
        var noAuto = ['Leads','Potentials', 'Quotes', 'Orders', 'Estimates', 'Actuals']
        if(commodity.indexOf('Commercial') > -1) {
            business_lines = 'Commercial';
        } else if (commodity.indexOf('Auto') > -1 && jQuery.inArray(module, noAuto) < 0 ){
            business_lines = 'Auto';
        }
    }
    var dataUrl = "index.php?module=Potentials&action=GetHiddenBlocks&formodule=" + module + "&businessline=" + business_lines;
	AppConnector.request(dataUrl).then(
		function(data) {

			if (data.success) {
				var showBlocks = [];
				for (var key in data.result.show) {
					showBlocks.push(data.result.show[key]);
                    jQuery("table[name='" + data.result.show[key] + "']").removeClass('hide');
                    if(jQuery('input[name="movehq"]').val() && module == 'Orders') { //In this situation, LDD section may have display:none so removeclass hide isn't enough.
                        jQuery("table[name='" + data.result.show[key] + "']").show();
                    }
				}
				for (var key in data.result.hide) {
                    if(showBlocks.indexOf(data.result.hide[key]) < 0 && data.result.hide[key] != 'LBL_POTENTIALS_ADDRESSDETAILS') {
                        if( business_line2 !== ''){
                            if(data.result.hide[key] !== 'participatingAgentsTable'){
						        jQuery("table[name='" + data.result.hide[key] + "']").addClass('hide');
                            }
                        }else{
                            jQuery("table[name='" + data.result.hide[key] + "']").addClass('hide');
                        }
					}
				}
			}
		},
		function(error, err) {

		}
	);
}
