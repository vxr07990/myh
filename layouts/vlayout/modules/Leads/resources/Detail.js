/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/
/*
 * function to get data from the query url string
 * @param: the variable that you want to get from the url string
 * @return: the value of the variable
 */
function getQueryVariable(variable) {
	var query = window.location.search.substring(1);
	var vars = query.split("&");
	for (var i=0; i<vars.length; i++) {
		var pair = vars[i].split("=");
		if(pair[0] == variable) {return pair[1];}
	}
	return(false);
}

Vtiger_Detail_Js("Leads_Detail_Js",{

	//cache will store the convert lead data(Model)
	cache : {},

	//Holds detail view instance
	detailCurrentInstance : false,


	/*
	 * function to trigger Convert Lead action
	 * @param: Convert Lead url, currentElement.
	 */
	convertLead : function(convertLeadUrl, buttonElement) {

		var instance = Leads_Detail_Js.detailCurrentInstance;
		//Initially clear the elements to overwtite earliear cache
		instance.convertLeadContainer = false;
		instance.convertLeadForm = false;
		instance.convertLeadModules = false;
		if(jQuery.isEmptyObject(Leads_Detail_Js.cache)) {
			AppConnector.request(convertLeadUrl).then(
				function(data) {
					if(data) {
						try {
							var rd = JSON.parse(data);
							if(rd.result.redirect)
							{
								window.location.href = rd.result.redirect;
								return;
							}
						} catch (e)
						{
						}
						Leads_Detail_Js.cache = data;
						instance.displayConvertLeadModel(data, buttonElement);
					}
				},
				function(error,err){

				}
			);
		} else {
			instance.displayConvertLeadModel(Leads_Detail_Js.cache, buttonElement);
		}
	}

},{
	//Contains the convert lead form
	convertLeadForm : false,

	//contains the convert lead container
	convertLeadContainer : false,

	//contains all the checkbox elements of modules
	convertLeadModules : false,

	//constructor
	init : function() {
		this._super();
		Leads_Detail_Js.detailCurrentInstance = this;
	},

	/*
	 * function to disable the Convert Lead button
	 */
	disableConvertLeadButton : function(button) {
		jQuery(button).attr('disabled','disabled');
	},

	/*
	 * function to enable the Convert Lead button
	 */
	enableConvertLeadButton : function(button) {
		jQuery(button).removeAttr('disabled');
	},

	/*
	 * function to enable all the input and textarea elements
	 */
	removeDisableAttr : function(moduleBlock) {
		moduleBlock.find('input,textarea,select').removeAttr('disabled');
	},

	/*
	 * function to disable all the input and textarea elements
	 */
	addDisableAttr : function(moduleBlock) {
		moduleBlock.find('input,textarea,select').attr('disabled', 'disabled');
	},

	/*
	 * function to display the convert lead model
	 * @param: data used to show the model, currentElement.
	 */
	displayConvertLeadModel : function(data, buttonElement) {
		var instance = this;
		var errorElement = jQuery(data).find('#convertLeadError');
		if(errorElement.length != '0') {
			var errorMsg = errorElement.val();
			var errorTitle = jQuery(data).find('#convertLeadErrorTitle').val();
			var params = {
				title: errorTitle,
				text: errorMsg,
				addclass: "convertLeadNotify",
				width: '35%',
				pnotify_after_open: function(){
					instance.disableConvertLeadButton(buttonElement);
					instance.createConvertToLeadName();
				},
				pnotify_after_close: function(){
					instance.enableConvertLeadButton(buttonElement);
				}
			};
			Vtiger_Helper_Js.showPnotify(params);
		} else {
			var callBackFunction = function(data){
				var editViewObj = Vtiger_Edit_Js.getInstance();
				jQuery(data).find('.fieldInfo').collapse({
					'parent': '#leadAccordion',
					'toggle' : false
				});
				app.showScrollBar(jQuery(data).find('#leadAccordion'), {'height':'350px'});
				editViewObj.registerBasicEvents(data);
				var checkBoxElements = instance.getConvertLeadModules();
				jQuery.each(checkBoxElements, function(index, element){
					instance.checkingModuleSelection(element);
				});
				instance.registerForReferenceField();
				instance.registerForDisableCheckEvent();
				instance.registerConvertLeadEvents();
				//hide opportunity field
				instance.hideOpportunityField();
				instance.getConvertLeadForm().validationEngine(app.validationEngineOptions);
				instance.registerConvertLeadSubmit();
			};
			app.showModalWindow(data,function(data){
				if(typeof callBackFunction == 'function'){
					callBackFunction(data);
					instance.createConvertToLeadName();
				}
			},{
				'text-align' : 'left'
			});
		}
	},

	ajaxEditHandling : function(currentTdElement) {
		var thisInstance = this;
		var detailViewValue = jQuery('.value',currentTdElement);
		var editElement = jQuery('.edit',currentTdElement);
		var extElement = jQuery('.ext',currentTdElement);
		var actionElement = jQuery('.summaryViewEdit', currentTdElement);
		var fieldnameElement = jQuery('.fieldname', editElement);
		var fieldName = fieldnameElement.val();
		var fieldElement = jQuery('[name="'+ fieldName +'"]', editElement);

		var specialSaveType = jQuery.grep(currentTdElement.attr('class').split(' '), function(className, indexOf){
			if(className.substring(0, 5) == 'comp_'){
				return className;
			}
		})[0];
		//console.dir(specialSaveType);

		if(fieldElement.attr('disabled') == 'disabled'){
			return;
		}

		if(editElement.length <= 0) {
			return;
		}

		if(editElement.is(':visible')){
			return;
		}

		detailViewValue.addClass('hide');
		extElement.addClass('hide');
		editElement.removeClass('hide').show().children().filter('input[type!="hidden"]input[type!="image"],select').filter(':first').focus();

		var saveTriggred = false;
		var preventDefault = false;

		var saveHandler = function(e) {
			var element = jQuery(e.target);
			if((element.closest('td').is(currentTdElement))){
				return;
			}

			currentTdElement.removeAttr('tabindex');

			var previousValue = fieldnameElement.data('prevValue');
			var formElement = thisInstance.getForm();
			var formData = formElement.serializeFormData();
			var ajaxEditNewValue = formData[fieldName];
			//value that need to send to the server
			var fieldValue = ajaxEditNewValue;
			var fieldInfo = Vtiger_Field_Js.getInstance(fieldElement.data('fieldinfo'));

			// Since checkbox will be sending only on and off and not 1 or 0 as currrent value
			if(fieldElement.is('input:checkbox')) {
				if(fieldElement.is(':checked')) {
					ajaxEditNewValue = '1';
				} else {
					ajaxEditNewValue = '0';
				}
				fieldElement = fieldElement.filter('[type="checkbox"]');
			}
			var errorExists = fieldElement.validationEngine('validate');
			//If validation fails
			if(errorExists) {
				return;
			}

			fieldElement.validationEngine('hide');
			//Before saving ajax edit values we need to check if the value is changed then only we have to save
			if(previousValue == ajaxEditNewValue) {
				editElement.addClass('hide');
				detailViewValue.removeClass('hide');
				extElement.removeClass('hide');
				actionElement.show();
				jQuery(document).off('click', '*', saveHandler);
			} else {
				var preFieldSaveEvent = jQuery.Event(thisInstance.fieldPreSave);
				fieldElement.trigger(preFieldSaveEvent, {'fieldValue' : fieldValue,  'recordId' : thisInstance.getRecordId()});
				if(preFieldSaveEvent.isDefaultPrevented()) {
					//Stop the save
					saveTriggred = false;
					preventDefault = true;
					return
				}
				preventDefault = false;

				jQuery(document).off('click', '*', saveHandler);

				if(!saveTriggred && !preventDefault) {
					saveTriggred = true;
				}else{
					return;
				}

				if(specialSaveType != '' && typeof specialSaveType != 'undefined'){
					//console.dir('trying to specialSave');
					var url = "index.php?module=Leads&action=SaveCompPricing&record="+getQueryVariable('record')+"&type="+specialSaveType+"&value="+ajaxEditNewValue;
					//console.dir(url);
					AppConnector.request(url).then(
						function(data) {
							if(data.success) {
								currentTdElement.progressIndicator({'mode':'hide'});
								//fieldnameElement.data('prevValue', ajaxEditNewValue);
								jQuery('td.'+specialSaveType).find('input.fieldname').each(function(){jQuery(this).data('prevValue', ajaxEditNewValue)});
								editElement.find('[name="'+specialSaveType+'_prev"]').each(function(){jQuery(this).val(ajaxEditNewValue);});
								editElement.addClass('hide');
								//set all the other fields to No
								jQuery('.'+specialSaveType).each(function() {
									jQuery(this).find("span.value").html('No');
								});
								//set this one to Yes
								detailViewValue.html('Yes');
								detailViewValue.removeClass('hide');
								actionElement.show();
							}
						},
						function(error) {
							//console.dir('error');
						}
					);
				} else {
					currentTdElement.progressIndicator();
					editElement.addClass('hide');
					var fieldNameValueMap = {};
					if(fieldInfo.getType() == 'multipicklist' || fieldInfo.getType() == 'multiagent') {
						var multiPicklistFieldName = fieldName.split('[]');
						fieldName = multiPicklistFieldName[0];
					}
					fieldNameValueMap["value"] = fieldValue;
					fieldNameValueMap["field"] = fieldName;
					fieldNameValueMap = thisInstance.getCustomFieldNameValueMap(fieldNameValueMap);
					thisInstance.saveFieldValues(fieldNameValueMap).then(function(response) {
							var postSaveRecordDetails = response.result;
							currentTdElement.progressIndicator({'mode':'hide'});
							detailViewValue.removeClass('hide');
							extElement.removeClass('hide');
							actionElement.show();
							detailViewValue.html(postSaveRecordDetails[fieldName].display_value);
							fieldElement.trigger(thisInstance.fieldUpdatedEvent,{'old':previousValue,'new':fieldValue});
							fieldnameElement.data('prevValue', ajaxEditNewValue);
							fieldElement.data('selectedValue', ajaxEditNewValue);
							//After saving source field value, If Target field value need to change by user, show the edit view of target field.
							if(thisInstance.targetPicklistChange) {
								if(jQuery('.summaryView', thisInstance.getForm()).length > 0) {
									thisInstance.targetPicklist.find('.summaryViewEdit').trigger('click');
								} else {
									thisInstance.targetPicklist.trigger('click');
								}
								thisInstance.targetPicklistChange = false;
								thisInstance.targetPicklist = false;
							}
						},
						function(error){
							//TODO : Handle error
							currentTdElement.progressIndicator({'mode':'hide'});
						}
					)
				}
			}
		};

		jQuery(document).on('click','*', saveHandler);
	},

	/*
	 * Controls if the primary email field is set to mandatory or not. True sets it to mandatory, False sets it to optional. (for SIRVA instance)
	 */
	setMandatoryEmail : function(setting){
		var redStar = jQuery("input[name = 'email']").closest('td').prev('td').find('.redColor');
		if(setting == true){
			var validationEngine = 'validate[ required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]';
			if(redStar.hasClass('hide')){
				redStar.removeClass('hide');
			}
		} else{
			var validationEngine = '';
			if(!redStar.hasClass('hide')){
				redStar.addClass('hide');
			}
		}
		jQuery("input[name = 'email']").attr('data-validation-engine', validationEngine);
	},

	/*
	 * function to check which module is selected
	 * to disable or enable all the elements with in the block
	 */
	checkingModuleSelection : function(element) {
		var instance = this;
		var module = jQuery(element).val();
		var moduleBlock = jQuery(element).closest('.accordion-group').find('#'+module+'_FieldInfo');
		if(jQuery(element).is(':checked')) {
			instance.removeDisableAttr(moduleBlock);
		} else {
			instance.addDisableAttr(moduleBlock);
		}
	},

	registerForReferenceField : function() {
		var container = this.getConvertLeadContainer();
		var referenceField = jQuery('.reference', container);
		if(referenceField.length > 0) {
			jQuery('#AccountsModule').attr('readonly', 'readonly');
		}
	},
	registerForDisableCheckEvent : function() {
		var instance = this;
		var container = this.getConvertLeadContainer();
		var referenceField = jQuery('.reference', container);
		var oppAccMandatory = jQuery('#oppAccMandatory').val();
		var oppConMandatory = jQuery('#oppConMandatory').val();
		var conAccMandatory = jQuery('#conAccMandatory').val();

		jQuery('#OpportunitiesModule').on('click',function(){
			if((jQuery('#OpportunitiesModule').is(':checked')) && oppAccMandatory) {
				jQuery('#AccountsModule').attr({'disabled':'disabled','checked':'checked'});
			}else if(!conAccMandatory || !jQuery('#ContactsModule').is(':checked')) {
				jQuery('#AccountsModule').removeAttr('disabled');
			}
			if((jQuery('#OpportunitiesModule').is(':checked')) && oppConMandatory) {
				jQuery('#ContactsModule').attr({'disabled':'disabled','checked':'checked'});
			}else {
				jQuery('#ContactsModule').removeAttr('disabled');
			}
		});
		jQuery('#ContactsModule').on('click',function(){
			if((jQuery('#ContactsModule').is(':checked')) && conAccMandatory) {
				jQuery('#AccountsModule').attr({'disabled':'disabled','checked':'checked'});
			}else if(!oppAccMandatory || !jQuery('#OpportunitiesModule').is(':checked')) {
				jQuery('#AccountsModule').removeAttr('disabled');
			}
		});
	},

	/*
	 * function to register Convert Lead Events
	 */
	registerConvertLeadEvents : function() {
		var container = this.getConvertLeadContainer();
		var instance = this;

		//Trigger Event to change the icon while shown and hidden the accordion body
		container.on('hidden', '.accordion-body', function(e){
			console.dir('hidden triggered');
			console.dir(e);
			var currentTarget = jQuery(e.currentTarget);
			currentTarget.closest('.convertLeadModules').find('.iconArrow').removeClass('icon-chevron-up').addClass('icon-chevron-down');
		}).on('shown', '.accordion-body', function(e){
			console.dir('shown triggered');
			console.dir(e.currentTarget);
			AppConnector.request('http://www.google.com');
			var currentTarget = jQuery(e.currentTarget);
			currentTarget.closest('.convertLeadModules').find('.iconArrow').removeClass('icon-chevron-down').addClass('icon-chevron-up');
		});

		//Trigger Event on click of Transfer related records modules
		container.on('click', '.transferModule', function(e){
			var currentTarget = jQuery(e.currentTarget);
			var module = currentTarget.val();
			var moduleBlock = jQuery('#'+module+'_FieldInfo');
			if(currentTarget.is(':checked')) {
				jQuery('#'+module+'Module').attr('checked','checked');
				moduleBlock.collapse('show');
				instance.removeDisableAttr(moduleBlock);
			}
		});

		//Trigger Event on click of the Modules selection to convert the lead
		container.on('click','.convertLeadModuleSelection', function(e){
			var currentTarget = jQuery(e.currentTarget);
			var currentModuleName = currentTarget.val();
			var moduleBlock = currentTarget.closest('.accordion-group').find('#'+currentModuleName+'_FieldInfo');
			var currentTransferModuleElement = jQuery('#transfer'+currentModuleName);
			var otherTransferModuleElement = jQuery('input[name="transferModule"]').not(currentTransferModuleElement);
			var otherTransferModuleValue = jQuery(otherTransferModuleElement).val();
			var otherModuleElement = jQuery('#'+otherTransferModuleValue+'Module');

			if(currentTarget.is(':checked')) {
				moduleBlock.collapse('show');
				instance.removeDisableAttr(moduleBlock);
				if(!otherModuleElement.is(':checked')) {
					jQuery(currentTransferModuleElement).attr('checked', 'checked');
				}
			} else {
				moduleBlock.collapse('hide');
				instance.addDisableAttr(moduleBlock);
				jQuery(currentTransferModuleElement).removeAttr('checked');
				if(otherModuleElement.is(':checked')) {
					jQuery(otherTransferModuleElement).attr('checked','checked');
				}
			}
			e.stopImmediatePropagation();
		});
	},

	registerChangeLeadType : function(){
		jQuery('select[name="lead_type"]').on('change', function () {
			if(jQuery('select[name="lead_type"]').val() == 'National Account'){
				if(jQuery('table[name="LBL_LEADS_NATIONALACCOUNT"]').hasClass('hide')){
					jQuery('table[name="LBL_LEADS_NATIONALACCOUNT"]').removeClass('hide')
				}
			} else{
				if(!jQuery('table[name="LBL_LEADS_NATIONALACCOUNT"]').hasClass('hide')){
					jQuery('table[name="LBL_LEADS_NATIONALACCOUNT"]').addClass('hide')
				}
			}
		});
	},

	/*
	 * function to register Convert Lead Submit Event
	 */
	registerConvertLeadSubmit : function() {
		var thisInstance = this;
		var formElement = this.getConvertLeadForm();

		formElement.on('jqv.form.validating', function(e){
			var jQv = jQuery(e.currentTarget).data('jqv');
			//Remove the earlier validated fields from history so that it wont count disabled fields
			jQv.InvalidFields = [];
		});

		//Convert Lead Form Submission
		formElement.on('submit',function(e) {
			var convertLeadModuleElements = thisInstance.getConvertLeadModules();
			var moduleArray = [];
			var contactModel = formElement.find('#ContactsModule');
			var accountModel = formElement.find('#AccountsModule');

			//If the validation fails in the hidden Block, we should show that Block with error.
			var invalidFields = formElement.data('jqv').InvalidFields;
			if(invalidFields.length > 0) {
				var fieldElement = invalidFields[0];
				var moduleBlock = jQuery(fieldElement).closest('div.accordion-body');
				moduleBlock.collapse('show');
				e.preventDefault();
				return;
			}

			jQuery.each(convertLeadModuleElements, function(index, element) {
				if(jQuery(element).is(':checked')) {
					moduleArray.push(jQuery(element).val());
				}
			});
			formElement.find('input[name="modules"]').val(JSON.stringify(moduleArray));

			var contactElement = contactModel.length;
			var organizationElement = accountModel.length;

			if(contactElement != '0' && organizationElement != '0') {
				if(jQuery.inArray('Accounts',moduleArray) == -1 && jQuery.inArray('Contacts',moduleArray) == -1) {
					alert(app.vtranslate('JS_SELECT_ORGANIZATION_OR_CONTACT_TO_CONVERT_LEAD'));
					e.preventDefault();
				}
			} else if(organizationElement != '0') {
				if(jQuery.inArray('Accounts',moduleArray) == -1) {
					alert(app.vtranslate('JS_SELECT_ORGANIZATION'));
					e.preventDefault();
				}
			} else if(contactElement != '0') {
				if(jQuery.inArray('Contacts',moduleArray) == -1) {
					alert(app.vtranslate('JS_SELECT_CONTACTS'));
					e.preventDefault();
				}
			}
		});
	},

	/*
	 * function to get all the checkboxes which are representing the modules selection
	 */
	getConvertLeadModules : function() {
		var container = this.getConvertLeadContainer();
		if(this.convertLeadModules == false) {
			this.convertLeadModules = jQuery('.convertLeadModuleSelection', container);
		}
		return this.convertLeadModules;
	},

	/*
	 * function to get Convert Lead Form
	 */
	getConvertLeadForm : function() {
		if(this.convertLeadForm == false) {
			this.convertLeadForm = jQuery('#convertLeadForm');
		}
		return this.convertLeadForm;
	},

	/*
	 * function to get Convert Lead Container
	 */
	getConvertLeadContainer : function() {
		if(this.convertLeadContainer == false) {
			this.convertLeadContainer = jQuery('#leadAccordion');
		}
		return this.convertLeadContainer;
	},

	updateConvertLeadvalue : function() {
		var thisInstance = Vtiger_Detail_Js.getInstance();
		var detailContentsHolder = thisInstance.getContentHolder();
		detailContentsHolder.on(thisInstance.fieldUpdatedEvent,"input,select",function(e, params){
			var elem = jQuery(e.currentTarget);
			var fieldName = elem.attr("name");
			var ajaxnewValue = params.new;

			if(!(jQuery.isEmptyObject(Leads_Detail_Js.cache))){
				var sampleCache = jQuery(Leads_Detail_Js.cache);
				var contextElem = sampleCache.find('[name="'+fieldName+'"]');

				if(elem.is("select")) {
					var oldvalue= contextElem.val();
					contextElem.find('option[value="'+oldvalue+'"]').removeAttr("selected");
					contextElem.find('option[value="'+ ajaxnewValue +'"]').attr("selected","selected");

					contextElem.trigger("liszt:updated");

				}
				else{
					contextElem.attr("value",ajaxnewValue);
				}

				Leads_Detail_Js.cache = sampleCache;
			}
		});

	},


	preventEmptyDestination : function(){
		if(jQuery('select[name="move_type"]')){
			jQuery('input[name="destination_address1"]').on('blur', function(){
				if(jQuery('input[name="destination_address1"]').val() == ''){
					jQuery('input[name="destination_address1"]').val('Will Advise');
				}
			});
		}
	},

	registerChangeMoveType : function() {
		var thisInstance = this;
		jQuery('select[name="move_type"]').on('change', function(){
			var moveType = jQuery('select[name="move_type"]').find('option:selected').val();
			//console.dir(moveType);
			//console.dir(jQuery('select[name="business_line"]'));
			jQuery('select[name="business_line"]').find('option:selected').prop('selected', false);
			switch(moveType) {
				case 'Local Canada':
				case 'Local US':
					jQuery('select[name="business_line"]').find('option[value="Local Move"]').prop('selected', true).attr('selected', 'selected');
					//console.dir("Local Move");
					break;
				case 'Interstate':
				case 'Inter-Provincial':
				case 'Cross Border':
					jQuery('select[name="business_line"]').find('option[value="Interstate Move"]').prop('selected', true).attr('selected', 'selected');
					//console.dir("Interstate Move");
					break;
				case 'O&I':
					jQuery('select[name="business_line"]').find('option[value="Commercial Move"]').prop('selected', true).attr('selected', 'selected');
					//console.dir("Commercial Move");
					break;
				case 'Intrastate':
				case 'Intra-Provincial':
					jQuery('select[name="business_line"]').find('option[value="Intrastate Move"]').prop('selected', true).attr('selected', 'selected');
					//console.dir("Intrastate Move");
					break;
				case 'Alaska':
				case 'Hawaii':
				case 'International':
					jQuery('select[name="business_line"]').find('option[value="International Move"]').prop('selected', true).attr('selected', 'selected');
					//console.dir("International Move");
					break;
				default:
					//console.dir('Error: registerChangeMoveType() switch case mismatch');
					break;
			}
			//switch to set origin/destination country based on move type
			switch(moveType) {
				case 'Local Canada':
				case 'Inter-Provincial':
				case 'Intra-Provincial':
					jQuery('select[name="origin_country"]').find('option[value="Canada"]').prop('selected', true).attr('selected', 'selected');
					jQuery('select[name="destination_country"]').find('option[value="Canada"]').prop('selected', true).attr('selected', 'selected');
					if(jQuery('input[name="emailoptout"]').hasClass('hide')){
						jQuery('input[name="emailoptout"]').removeClass('hide');
						jQuery('input[name="emailoptout"]').closest('span').prev('span').removeClass('hide');
						jQuery('input[name="emailoptout"]').closest('td').addClass('fieldValue').prev('td').find('label').removeClass('hide');
					}
					//console.dir("Canada Move");
					break;
				case 'Interstate':
				case 'Local US':
				case 'Intrastate':
				case 'Alaska':
				case 'Hawaii':
					jQuery('select[name="origin_country"]').find('option[value="United States"]').prop('selected', true).attr('selected', 'selected');
					jQuery('select[name="destination_country"]').find('option[value="United States"]').prop('selected', true).attr('selected', 'selected');
					if(!jQuery('input[name="emailoptout"]').hasClass('hide')){
						jQuery('input[name="emailoptout"]').addClass('hide');
						jQuery('input[name="emailoptout"]').closest('span').prev('span').addClass('hide');
						jQuery('input[name="emailoptout"]').closest('td').removeClass('fieldValue').prev('td').find('label').addClass('hide');
					}
					//console.dir("US Move");
					break;
				case 'O&I':
				case 'International':
				case 'Cross Border':
					//console.dir("Other Move");
					break;
				default:
					//console.dir('Error: registerChangeMoveType() country switch case mismatch');
					break;
			}
			//update picklists & save new values
			jQuery('select[name="origin_country"]').trigger('liszt:updated').trigger('change');
			jQuery('select[name="destination_country"]').trigger('liszt:updated').trigger('change');
			jQuery('select[name="business_line"]').trigger('liszt:updated').trigger('change');
			thisInstance.saveItem(jQuery('select[name="business_line"]'));
			thisInstance.saveItem(jQuery('select[name="destination_country"]'));
			thisInstance.saveItem(jQuery('select[name="origin_country"]'));
			//jQuery('select[name="business_line"]').trigger('mouseup').trigger('change').trigger('click');
		});
	},

	registerPhoneTypeEvents : function(){
		jQuery('select[name="primary_phone_type"]').on('change', function() {
			var selectedOption = jQuery('select[name="primary_phone_type"]').val();
			if(selectedOption == 'Work'){
				if(jQuery('#primaryPhoneSpan').hasClass('hide')){
					jQuery('#primaryPhoneSpan').removeClass('hide');
				}
			}
			else{
				if(!jQuery('#primaryPhoneSpan').hasClass('hide')){
					jQuery('#primaryPhoneSpan').addClass('hide');
					jQuery('input[name="primary_phone_ext"]').val('');
				}
			}
		});

		jQuery('select[name="origin_phone1_type"]').on('change', function() {
			var selectedOption = jQuery('select[name="origin_phone1_type"]').val();
			if(selectedOption == 'Work'){
				if(jQuery('#originPhone1Span').hasClass('hide')){
					jQuery('#originPhone1Span').removeClass('hide');
				}
			}
			else{
				if(!jQuery('#originPhone1Span').hasClass('hide')){
					jQuery('#originPhone1Span').addClass('hide');
					jQuery('input[name="origin_phone1_ext"]').val('');
				}
			}
		});

		jQuery('select[name="origin_phone2_type"]').on('change', function() {
			var selectedOption = jQuery('select[name="origin_phone2_type"]').val();
			if(selectedOption == 'Work'){
				if(jQuery('#originPhone2Span').hasClass('hide')){
					jQuery('#originPhone2Span').removeClass('hide');
				}
			}
			else{
				if(!jQuery('#originPhone2Span').hasClass('hide')){
					jQuery('#originPhone2Span').addClass('hide');
					jQuery('input[name="origin_phone2_ext"]').val('');
				}
			}
		});

		jQuery('select[name="destination_phone1_type"]').on('change', function() {
			var selectedOption = jQuery('select[name="destination_phone1_type"]').val();
			if(selectedOption == 'Work'){
				if(jQuery('#destinationPhone1Span').hasClass('hide')){
					jQuery('#destinationPhone1Span').removeClass('hide');
				}
			}
			else{
				if(!jQuery('#destinationPhone1Span').hasClass('hide')){
					jQuery('#destinationPhone1Span').addClass('hide');
					jQuery('input[name="destination_phone1_ext"]').val('');
				}
			}
		});

		jQuery('select[name="destination_phone2_type"]').on('change', function() {
			var selectedOption = jQuery('select[name="destination_phone2_type"]').val();
			if(selectedOption == 'Work'){
				if(jQuery('#destinationPhone2Span').hasClass('hide')){
					jQuery('#destinationPhone2Span').removeClass('hide');
				}
			}
			else{
				if(!jQuery('#destinationPhone2Span').hasClass('hide')){
					jQuery('#destinationPhone2Span').addClass('hide');
					jQuery('input[name="destination_phone2_ext"]').val('');
				}
			}
		});

	},

	registerDispositionLostEvent : function() {
		//dispLostFiller
		//disposition_lost_reasons
		var thisInstance = this;
		var selectTag = jQuery('select[name="leadstatus"]');
		if(jQuery('#Leads_detailView_fieldValue_leadstatus').find('span')) {
			selectTag.siblings('.chzn-container').find('.chzn-results').on('mouseup', function () {
				//console.dir('firing leadstatus change event');
				var currentTdElement = jQuery(this).closest('td');
				var selected = currentTdElement.find('.result-selected').html();
				var optionId = currentTdElement.find('.result-selected').attr('id').split('_')[3];
				var selectedId = currentTdElement.find('option:eq(' + optionId + ')').val();
				//console.dir(selectedId);
				if (selectedId == 'Lost') {
					//console.dir('it was lost, its dead Jim');
					if (!jQuery('.dispLostFiller').hasClass('hide')) {
						jQuery('.dispLostFiller').addClass('hide');
					}
					if (jQuery('select[name="disposition_lost_reasons"]').closest('td').hasClass('hide')) {
						jQuery('select[name="disposition_lost_reasons"]').closest('td').removeClass('hide').prev('td').removeClass('hide');
					}
				} else {
					if (jQuery('.dispLostFiller').hasClass('hide')) {
						jQuery('.dispLostFiller').removeClass('hide');
					}
					if (!jQuery('select[name="disposition_lost_reasons"]').closest('td').hasClass('hide')) {
						jQuery('select[name="disposition_lost_reasons"]').closest('td').addClass('hide').prev('td').addClass('hide');

						var currentTdElement = jQuery('select[name="disposition_lost_reasons"]').closest('td');
						var detailViewValue = jQuery('.value', currentTdElement);
						var editElement = jQuery('.edit', currentTdElement);
						var actionElement = jQuery('.summaryViewEdit', currentTdElement);
						var fieldnameElement = jQuery('.fieldname', editElement);
						var fieldName = fieldnameElement.val();
						var fieldElement = jQuery('[name="' + fieldName + '"]', editElement);
						var previousValue = fieldnameElement.data('prevValue');

						currentTdElement.progressIndicator();
						detailViewValue.addClass('hide');

						var fieldNameValueMap = {};
						fieldNameValueMap["value"] = '';
						fieldNameValueMap["field"] = fieldName;
						fieldNameValueMap = thisInstance.getCustomFieldNameValueMap(fieldNameValueMap);
						thisInstance.saveFieldValues(fieldNameValueMap).then(function (response) {
							var postSaveRecordDetails = response.result;
							currentTdElement.progressIndicator({'mode': 'hide'});
							detailViewValue.removeClass('hide');
							jQuery('#interstateRateQuick').removeClass('hide');
							actionElement.show();
							detailViewValue.html(postSaveRecordDetails[fieldName].display_value);
							fieldElement.trigger(thisInstance.fieldUpdatedEvent, {'old': previousValue, 'new': ''});
							fieldnameElement.data('prevValue', '');
							fieldElement.data('selectedValue', '');
						});

						jQuery('select[name="disposition_lost_reasons"]').find('option:selected').prop('selected', false).find('option').first().prop('selected', true).closest('select').trigger('liszt:updated');
						jQuery('select[name="disposition_lost_reasons"]').siblings('.chzn-container').find('.chzn-results').children().first().trigger('mouseup');
					}
				}
				thisInstance.hideTheExtraRow();
			});
		}
	},
	registerCompPricingList : function() {
		var thisInstance = this;
		var selectTag = jQuery('select[name="disposition_lost_reasons"]');
		selectTag.siblings('.chzn-container').find('.chzn-results').on('mouseup', function() {
			//console.dir('firing disposition_lost_reasons change event');
			var currentTdElement = jQuery(this).closest('td');
			var selected = currentTdElement.find('.result-selected').html();
			var optionId = currentTdElement.find('.result-selected').attr('id').split('_')[3];
			var selectedId = currentTdElement.find('option:eq('+optionId+')').val();
			console.dir(selectedId);
			if(selectedId == 'Pricing'){
				//unhide the pricingtable
				if(jQuery('.pricingCompList').hasClass('hide')){
					jQuery('.pricingCompList').removeClass('hide');
				}
			} else {
				//hide the pricingtable
				if(!jQuery('.pricingCompList').hasClass('hide')){
					jQuery('.pricingCompList').addClass('hide');
				}
			}
			thisInstance.hideTheExtraRow();
		});
	},
	hideTheExtraRow : function() {
		if(jQuery('td.emptyTD:not(.hide)').first().closest('tr').find('td.emptyTD:not(.hide)').length == 4){
			if(!jQuery('td.emptyTD:not(.hide)').first().closest('tr').hasClass('hide')){
				jQuery('td.emptyTD:not(.hide)').first().closest('tr').addClass('hide');
			}
		} else {
			if(jQuery('td.emptyTD:not(.hide)').first().closest('tr').hasClass('hide')){
				jQuery('td.emptyTD:not(.hide)').first().closest('tr').removeClass('hide');
			}
		}
	},

	loadContents : function(url,data) {
		//console.dir("CORE LOAD CONTENTS ACTIVE");
		var thisInstance = this;
		var aDeferred = jQuery.Deferred();
		//console.dir("A DEFFERED");
		var detailContentsHolder = this.getContentHolder();
		var params = url;
		if(typeof data != 'undefined'){
			params = {};
			params.url = url;
			params.data = data;
		}
		AppConnector.requestPjax(params).then(
			function(responseData){
				//console.dir("IN PJAX");
				detailContentsHolder.html(responseData);
				//console.dir("GATHERING RESPONSE DATA");
				responseData = detailContentsHolder.html();
				//thisInstance.triggerDisplayTypeEvent();
				//console.dir("REGISTER BLOCK STATUS");
				thisInstance.registerBlockStatusCheckOnLoad();
				//Make select box more usability
				//console.dir("APP CHANGE ELEMENT VIEW");
				app.changeSelectElementView(detailContentsHolder);
				//Attach date picker event to date fields
				//console.dir("APP REGISTER EVENTS");
				app.registerEventForDatePickerFields(detailContentsHolder);
				app.registerEventForTextAreaFields(jQuery(".commentcontent"));
				//console.dir("AUTOSIZE");
				jQuery('.commentcontent').autosize();
				//console.dir("VALIDATION ENGINE");
				thisInstance.getForm().validationEngine();
				//console.dir("ATTEMPTING DEFFER RESOLVE");
				aDeferred.resolve(responseData);
				//console.dir("DEFFER RESOLVED!");
				if(thisInstance.addressAutofill) {thisInstance.initializeAddressAutofill(thisInstance.autofillModuleName);}

				thisInstance.registerSpecialBindings();
				//console.dir("PJAX DONE!");
				thisInstance.registerBusinessLineChangeEvent();
				jQuery('select[name="business_line"]').siblings('.chzn-container').find('.result-selected').trigger('mouseup');
			},
			function(){

			}
		);

		return aDeferred.promise();
	},

	registerBusinessLineChangeEvent : function() {
		var thisInstance = this;
		var selectTag = jQuery('select[name="business_line"]');
		var editable = selectTag.length;

		if (editable == 0) {
			var business_line = encodeURIComponent(jQuery.trim(jQuery('#Leads_detailView_fieldValue_business_line').find('span').html()));
			var moduleName = 'Leads';
			var dataURL = "index.php?module=Potentials&action=GetHiddenBlocks&viewMode=detail&formodule="+moduleName+"&businessline="+business_line;

			AppConnector.request(dataURL).then(function (data) {
				if (data.success) {
					var showBlocks = [];

					for (var key in data.result.show) {
						showBlocks.push(data.result.show[key]);
						jQuery("."+data.result.show[key]).removeClass('hide');
					}

					for (var key in data.result.hide) {
						if (showBlocks.indexOf(data.result.hide[key]) < 0) {
							jQuery("."+data.result.hide[key]).addClass('hide');
						}
					}
				}
			});
		} else {
			selectTag.siblings('.chzn-container').find('.chzn-results').on('mouseup', function() {
				var moduleName = 'Leads';
				var business_line = encodeURIComponent(jQuery.trim(jQuery('#Leads_detailView_fieldValue_business_line').find('.result-selected').text()));
				var dataURL = "index.php?module=Potentials&action=GetHiddenBlocks&viewMode=detail&formodule="+moduleName+"&businessline="+business_line;

				AppConnector.request(dataURL).then(function (data) {
					if (data.success) {
						var showBlocks = [];

						for (var key in data.result.show) {
							showBlocks.push(data.result.show[key]);
							jQuery("[name='"+data.result.show[key]+"']").removeClass('hide');
						}

						for (var key in data.result.hide) {
							if (showBlocks.indexOf(data.result.hide[key]) < 0) {
								jQuery("[name='"+data.result.hide[key]+"']").addClass('hide');
							}
						}
					}
				});
			});
		}
	},

	createConvertToLeadName: function() {
		var fullName = jQuery('#potentialName').data('name');
		jQuery('input[name="potentialname"]').val(fullName);
	},

	//Google Address Autofill
	registerEvents : function() {
		this._super();
		this.registerSpecialBindings();
        this.hideShowReasonField();
	},

	/*
	 This function will hide the fulfillment date and the label
	 */
	removeFulfilmentDate: function() {
		jQuery('#Leads_detailView_fieldLabel_fulfillment_date').html('');
		jQuery('#Leads_detailView_fieldValue_fulfillment_date').html('');
	},

	loadNewLeadCount : function () {
		var listLink = jQuery("#Leads_sideBar_link_LBL_RECORDS_LIST").find('a.quickLinks');
		//ActionAjax
		var recordLead = app.getRecordId();
		var dataURL = "index.php?module=Leads&action=ActionAjax&mode=countNewLeads&recordlead="+recordLead;
		AppConnector.request(dataURL).then(function (data) {
			if (data.success) {
				if(data.result.count != 'NOT_IGC_MOVEHQ') {
					var countIcon = '<strong class="call_notification_count" style="background-color: rgb(215, 77, 47); border-radius: 50%; color: white; display: inline; height: 35px; margin-left: 10px; margin-top: -10px; position: absolute; text-align: center; width: 35px;"><span style="margin: 7px 5px 5px; display: inline-block;">'+data.result.count+'</span></strong>';
					listLink.append(countIcon);
				}
			}
		});
	},
	hideOpportunityField : function () {
		if(jQuery('[name="business_line2"]').length > 0) {
			var selectTag = jQuery('select[name="business_line"]');
			Vtiger_Edit_Js.hideCell(selectTag);
		}
		// var sales_stage = jQuery('select[name="sales_stage"]');
		// Vtiger_Edit_Js.hideCell(sales_stage);
	},

    hideShowReasonField: function(){
        if(jQuery('[name="movehq"]').val() != 1) {
            return true;
        }
        var index = jQuery('#Leads_detailView_fieldValue_leadstatus').text().trim().indexOf('Cancelled');
        if (index < 0) {
            jQuery('#Leads_detailView_fieldLabel_reason_cancelled').find('label').addClass('hide');
            jQuery('#Leads_detailView_fieldValue_reason_cancelled').find('span').addClass('hide');
        }
    },


	registerSpecialBindings : function() {
		this.hideTheExtraRow();
		this.initializeAddressAutofill('Leads');
		this.preventEmptyDestination();
		this.registerBusinessLineChangeEvent();
		this.registerChangeLeadType();
		this.registerChangeMoveType();
		this.registerCompPricingList();
		this.registerDispositionLostEvent();
		this.registerPhoneTypeEvents();
		this.updateConvertLeadvalue();
		this.removeFulfilmentDate();
		this.loadNewLeadCount();
		jQuery('select[name="move_type"]').trigger('change');
		jQuery('select[name="lead_type"]').trigger('change');
		jQuery('select[name="business_line"]').siblings('.chzn-container').find('.result-selected').trigger('mouseup');
		jQuery('input[name="days_to_move"]').prop('readonly',true);
		jQuery('select[name="leadstatus"]').siblings('.chzn-container').find('.chzn-results').trigger('mouseup');
		jQuery('select[name="disposition_lost_reasons"]').siblings('.chzn-container').find('.chzn-results').trigger('mouseup');
	}
});


//On Page Load
jQuery(document).ready(function() {
	Vtiger_Index_Js.registerEvents();
});
