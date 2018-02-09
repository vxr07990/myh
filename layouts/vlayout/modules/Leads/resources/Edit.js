/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

Vtiger_Edit_Js("Leads_Edit_Js",{
	getInstance: function() {
		return new Leads_Edit_Js();
	}
},{
    moveType: null,
    daysToMove: null,
    salesperson: null,
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

    validDate : function(){
		var dateChecks = [
			{
				on:'pack', from: 'pack', to: 'pack_to',
				msg: 'The "Pack From Date" should not be before the "Pack to Date"'
			},
			{
				on:'pack_to',from: 'pack',to: 'pack_to',
				msg: 'The "Pack From Date" should not be before the "Pack to Date"'
			},
			{
				on:'load_from', from: 'load_from', to: 'load_to',
				msg: 'The "Load From Date" should not be before the "Load to Date"'
			},
			{
				on:'load_to', from: 'load_from', to: 'load_to',
				msg: 'The "Load From Date" should not be before the "Load to Date"'
			},
			{
				on:'deliver', from: 'deliver', to: 'deliver_to',
				msg: 'The "Deliver From Date" should not be before the "Deliver to Date"'
			},
			{
				on:'deliver_to', from: 'deliver', to: 'deliver_to',
				msg: 'The "Deliver From Date" should not be before the "Deliver to Date"'
			},

        ];
        $.each(dateChecks, function(key, date){
            $('[name="'+date.on+'"]').on('change',function() {
				var domFrom = $('[name="'+date.from+'"]');
				var	domTo	= $('[name="'+date.to+'"]');
                var from = new Date(domFrom.val());
				var	to = new Date(domTo.val());
                if(from>to){
					domFrom.val('');
					domTo.val('');
                    bootbox.alert(date.msg);
                }
            });
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

	registerChangeLeadType : function(){
		var thisInstance = this;
		jQuery('select[name="lead_type"]').on('change', function(){
			if(jQuery('select[name="lead_type"]').val() == 'National Account'){
				if(jQuery('table[name="LBL_LEADS_NATIONALACCOUNT"]').hasClass('hide')){
					jQuery('table[name="LBL_LEADS_NATIONALACCOUNT"]').removeClass('hide');
				}
			} else {
				if(!jQuery('table[name="LBL_LEADS_NATIONALACCOUNT"]').hasClass('hide')){
					jQuery('table[name="LBL_LEADS_NATIONALACCOUNT"]').addClass('hide');
				}
			}
			if(jQuery('select[name="lead_type"]').val() != 'Consumer') {
				jQuery('select[name="business_channel"]').val('').trigger('liszt:updated');
			} else {
				jQuery('select[name="business_channel"]').val('Consumer').trigger('liszt:updated');
			}
		});
	},

	registerDispositionLostEvent : function() {
		//dispLostFiller
		//disposition_lost_reasons
		var thisInstance = this;
		var selectTag = jQuery('select[name="leadstatus"]');
		selectTag.siblings('.chzn-container').find('.chzn-results').on('mouseup', function() {
			var selectedId = selectTag.val();
			if(selectedId == 'Lost'){
				if(jQuery('.dispLostFiller').hasClass('hide')){
					jQuery('.dispLostFiller').removeClass('hide');
				}
				if(jQuery('select[name="disposition_lost_reasons"]').closest('td').hasClass('hide')){
					jQuery('select[name="disposition_lost_reasons"]').closest('td').removeClass('hide').prev('td').removeClass('hide');
				}
			} else {
				if(!jQuery('select[name="disposition_lost_reasons"]').closest('td').hasClass('hide')){
					jQuery('select[name="disposition_lost_reasons"]').closest('td').addClass('hide').prev('td').addClass('hide');
					jQuery('select[name="disposition_lost_reasons"]').find('option:selected').prop('selected',false).trigger('liszt:updated');
					jQuery('select[name="disposition_lost_reasons"]').siblings('.chzn-container').find('.chzn-results').trigger('mouseup');
				}
				thisInstance.hideDispositionLostOther();
			}
		});
	},

	registerChangeDispositionLost : function() {
		var thisInstance = this;
		jQuery('select[name="disposition_lost_reasons"]').on('change', function(){
			var Disposition = jQuery('select[name="disposition_lost_reasons"]').find('option:selected').text();
			if (Disposition == 'Other') {
				thisInstance.showDispositionLostOther();
			} else {
				thisInstance.hideDispositionLostOther();
			}
		});
	},

	hideDispositionLostOther : function() {
		var otherTag = jQuery('input[name="disposition_lost_reasons_other"]');
		if(!otherTag.hasClass('hide')) {
			otherTag.closest('td').prev('td').find('label').addClass('hide');
			//clear any value entered
			otherTag.val('');
			otherTag.addClass('hide');
		}
	},

	showDispositionLostOther : function() {
		var otherTag = jQuery('input[name="disposition_lost_reasons_other"]');
		if(otherTag.hasClass('hide')) {
			otherTag.closest('td').prev('td').find('label').removeClass('hide');
			otherTag.removeClass('hide');
		}
	},

	setDefaultInputs: function() {
		if(jQuery('input[name="funded"]').val() == '') {
			jQuery('input[name="funded"]').val('AGT Funded');
		}
		if(jQuery('select[name="languages"]').val() == '') {
			jQuery('select[name="languages"]').val('English').trigger('liszt:updated');
		}
	},

	registerCompPricingList : function() {
		var thisInstance = this;
		var selectTag = jQuery('select[name="disposition_lost_reasons"]');
		selectTag.siblings('.chzn-container').find('.chzn-results').on('mouseup', function() {
			//console.dir('firing disposition_lost_reasons change event');
			var currentTdElement = jQuery(this).closest('td');
			var selected = currentTdElement.find('.result-selected').html();
			if(selected) {
				var optionId = currentTdElement.find('.result-selected').attr('id').split('_')[3];
				var selectedId = currentTdElement.find('option:eq(' + optionId + ')').val();
				//console.dir(selectedId);
				if (selectedId == 'Pricing') {
					//unhide the pricingtable
					if (jQuery('.pricingCompList').hasClass('hide')) {
						jQuery('.pricingCompList').removeClass('hide');
					}
				} else {
					//hide the pricingtable
					if (!jQuery('.pricingCompList').hasClass('hide')) {
						jQuery('.pricingCompList').addClass('hide');
					}
				}
			}
		});
	},


    updateTabIndexValues: function() {
        var tabindex = 1;
        jQuery('table').each(function() {
            if(jQuery(this).attr('name') == 'LBL_LEADS_ADDRESSINFORMATION'){
                    var row = 1;
                    var extensionSuffix = "_ext";
                    jQuery(this).find('input, textarea, a').each(function() {
                        var fieldName = this.name;
                        if (fieldName.indexOf(extensionSuffix) ==-1 && !jQuery(this).parent().hasClass('chzn-search')) {
                            if(row == 1){
                                var input = jQuery(this);
                                input.attr("tabindex", tabindex);
                                tabindex++;
                                row = 2;
                            }
                            else{
                                row = 1;
                            }
                        }
                    });
                    var row = 1;
                    jQuery(this).find('input, textarea, a').each(function() {
                        var fieldName = this.name;
                        if (fieldName.indexOf(extensionSuffix) ==-1  && !jQuery(this).parent().hasClass('chzn-search')) {
                            if(row == 2){
                                var input = jQuery(this);
                                input.attr("tabindex", tabindex);
                                tabindex++;
                                row = 1;
                            }
                            else{
                                row = 2;
                            }
                        }
                    });
                } else{
                    jQuery(this).find('input, textarea, a').each(function() {
                        if (this.type != "hidden" && !jQuery(this).closest('td').hasClass('hide') && !jQuery(this).hasClass('hide') && !jQuery(this).parent().hasClass('chzn-search')) {
                            var input = jQuery(this);
                            input.attr("tabindex", tabindex);
                            tabindex++;
                        }
                    });
                }
        });
    },

	generateReceivedDate: function() {
		var today = new Date();
		var month = ("0" + (today.getMonth()+1)).slice(-2);
		var day = ("0" + today.getDate()).slice(-2);
		var userSettings = jQuery('input[name="lead_receive_date"]').data('date-format');
		switch(userSettings) {
			case 'mm-dd-yyyy':
				var received = month+'-'+day+'-'+today.getFullYear();
				break;
			case 'yyyy-mm-dd':
				var received = today.getFullYear()+'-'+month+'-'+day;
				break;
			case 'dd-mm-yyyy':
				var received = day+'-'+month+'-'+today.getFullYear();
				break;
			default:
				var received = month+'-'+day+'-'+today.getFullYear();
				break;
		}
		jQuery('input[name="lead_receive_date"]').val(received);
	},

	setDefaultLeadType: function() {
		if(jQuery('select[name="lead_type"]').val() == '') {
			jQuery('select[name="lead_type"]').val('Consumer').trigger('liszt:updated');
		}
	},

    //@NOTE: Moved to parent because Opportunities_Edit_Js and Leads_Edit_Js used the same function
    //registerSourceNameChange : function() {},

	setBlockFieldsByBusinessLine: function() {
		var thisInstance = this;
		var updateLeadsource = function(opt)
		{
			var cur = jQuery('select[name="leadsource"]').parent().find('[class="active-result result-selected"]').text();
			jQuery('select[name="leadsource"]').html(opt).trigger('liszt:updated');
			jQuery('select[name="leadsource"]').parent().find('[class="active-result result-selected"]').attr('class', 'active-result');
			var found = false;
			jQuery('select[name="leadsource"]').parent().find('[class="active-result"]').filter(
				function() {var res = (jQuery(this).text() == cur); if(res) {found = true;} return res;}
			).attr('class', 'active-result result-selected');
			if(found) {
				jQuery('select[name="leadsource"]').parent().find('span').text(cur);
				jQuery('select[name="leadsource"]').val(cur);
			}
		};

		var businessLine = jQuery('select[name="business_line"]').next().find('.result-selected').html();
		if(businessLine){

		//ensure these things exist before trying to use them.
        if (thisInstance.leadsourceNationalOptions && businessLine == 'National Account') {
            //console.log('Show National Options');
            //use the setPicklistOptions function to set these, this perserves the existing selection if possible.
            Vtiger_Edit_Js.setPicklistOptions(jQuery('select[name="leadsource"]'), thisInstance.leadsourceNationalOptions);
        } else if (thisInstance.leadsourceWorkspaceOptions && businessLine.indexOf('Work') > -1) {
            //console.log('Show Workspace Options');
            Vtiger_Edit_Js.setPicklistOptions(jQuery('select[name="leadsource"]'), thisInstance.leadsourceWorkspaceOptions);
        } else if (thisInstance.leadsourceHHGOptions && businessLine.indexOf('HHG') > -1) {
            //console.log('Show HHG Options');
            Vtiger_Edit_Js.setPicklistOptions(jQuery('select[name="leadsource"]'), thisInstance.leadsourceHHGOptions);
        } else {
            //console.log('Show Default Options');
            Vtiger_Edit_Js.setPicklistOptions(jQuery('select[name="leadsource"]'), thisInstance.defaultLeadSourceOptions);
        }

		}

		if(jQuery('[name="instance"]').val() != 'graebel') {
			var hideAddressBlocks = ['National Account', 'Commercial - Distribution', 'Commercial - Record Storage', 'Commercial - Storage', 'Commercial - Asset Management', 'Work Space - MAC', 'Commercial - Project', 'Work Space - Special Services', 'Work Space - Commodities'];
			if (jQuery.inArray(jQuery('select[name="business_line"]').val(), hideAddressBlocks) !== -1) {
				jQuery('table[name="LBL_LEADS_ADDRESSINFORMATION"]').addClass('hide');
			}
		}

		// jQuery('[name="agentid"]').trigger('change');
	},

	setLeadSourceOptions: function() {
		var thisInstance = this;
		thisInstance.setBlockFieldsByBusinessLine();

		//only works until they move stuff.
		//jQuery('select[name="leadsource_national"]').closest('tr').hide();
		//jQuery('select[name="leadsource_workspace"]').parent().hide().closest('td').prev().children().hide();
        Vtiger_Edit_Js.hideCell('leadsource_national');
        Vtiger_Edit_Js.hideCell('leadsource_workspace');
        Vtiger_Edit_Js.hideCell('leadsource_hhg');

		var trNat = jQuery('select[name="leadsource_national"]').closest('tr');
		var trWrkSpace = jQuery('select[name="leadsource_workspace"]').closest('tr');
		//var trarray = [];
		//trarray.push(trNat);
		//trarray.push(trWrkSpace);
		//thisInstance.fixFormatingForOpportunities(trarray);
		if(trNat.find('label.hide').length == 2){
			trNat.addClass('hide'); //odd number of fields and the business line is last so hide the fields
		}
	},

    //pull the picklistvalues object or return null.
    getPickOptions : function(fieldObject) {
	    if (typeof fieldObject == 'undefined') {
	        return null;
        }
        if (!fieldObject) {
	        return null;
	    }
	    if (typeof fieldObject.data('fieldinfo') == 'undefined') {
	        return null;
        }
        if (typeof fieldObject.data('fieldinfo').picklistvalues == 'object') {
            return fieldObject.data('fieldinfo').picklistvalues;
        }

        return null;
    },

	registerChangeFieldLeadReasonTypeReadOnly : function () {
		var leadStatus = jQuery('select[name="leadstatus"]');
		leadStatus.on('change',function () {
			if(leadStatus.val() == 'Cancelled')
			{
			    if(jQuery('[name="movehq"]').val()){
			        Vtiger_Edit_Js.showCell('reason_cancelled');
                } else {
                    jQuery('[name="reason_cancelled"]').prop('disabled', false);
                    jQuery('[name="reason_cancelled"]').trigger('liszt:updated');
                }
			}
			else {
                if(jQuery('[name="movehq"]').val()){
                    Vtiger_Edit_Js.hideCell('reason_cancelled');
                } else {
                    jQuery('[name="reason_cancelled"]').val('');
                    jQuery('[name="reason_cancelled"]').prop('disabled', true);
                    jQuery('[name="reason_cancelled"]').trigger('liszt:updated');
                }
			}
		});
		leadStatus.trigger('change');
	},

	registerClearReferenceSelectionEventMoveRoles : function(container) {
		container.find('.clearReferenceSelection').on('click', function(e){
			var element = jQuery(e.currentTarget);
			var parentTdElement = element.closest('td');
			var fieldNameElement = parentTdElement.find('.sourceField');
			var fieldInfo = fieldNameElement.data('fieldinfo');
			var fieldName = fieldInfo.name;
			fieldNameElement.val('');
			parentTdElement.find('[name^="'+fieldName+'_display"]').removeAttr('readonly').val('');
			element.trigger(Vtiger_Edit_Js.referenceDeSelectionEvent);
			e.preventDefault();
		})
	},

	setReferenceFieldValue : function(container, params) {
		var sourceField = container.find('input[class="sourceField"]').attr('name');
		var fieldElement = container.find('input[name="'+sourceField+'"]');
		var sourceFieldDisplay = sourceField+"_display";
		var fieldDisplayElement = container.find('input[name="'+sourceFieldDisplay+'"]');
		var popupReferenceModule = container.find('input[name="popupReferenceModule"]').val();

		var selectedName = params.name;
		var id = params.id;

		fieldElement.val(id);
		fieldDisplayElement.val(selectedName).attr('readonly',true);
		fieldElement.trigger(Vtiger_Edit_Js.referenceSelectionEvent, {'source_module' : popupReferenceModule, 'record' : id, 'selectedName' : selectedName});

		fieldDisplayElement.validationEngine('closePrompt',fieldDisplayElement);
		if(jQuery('[name="instance"]').val() != 'graebel') {
			this.registerAddMorolesByChangeAccount(id, container);
		}
	},

	registerAddMorolesByChangeAccount : function(id,container) {
		var thisInstance = new Vtiger_EditBlock_Js;
		var guestModule = 'MoveRoles';

		var referenceModuleName = this.getReferencedModuleName(container);
		var business_line2 = $('[name="business_line2"]').val();
		if(referenceModuleName == 'Accounts'){
			var params = {
				module: 'MoveRoles',
				action: 'FilterByBusinesslineForAccount',
				accountid: id,
				business_line: business_line2
			};
			AppConnector.request(params).then(function (data) {
				if(data.result.length > 0){
					var numberMoveroles = 0;
					jQuery('[name^="MoveRolesTable"]').find('.MoveRolesBlock:visible').each(function (key) {
						var i = key + 1;
						if($(this).find('#moveroles_id_'+i).val()){
							$(this).hide();
							$(this).find('[name=moveroles_deleted_'+i+']').val('deleted');
							numberMoveroles ++
						}else{
							$(this).remove();
						}
					});
					jQuery('[name^="MoveRolesTable"]').find('.numMoveRoles').val(numberMoveroles);
					$.each(data.result, function (k,v) {
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
							var oldName = jQuery(this).attr('name');
							var newName = jQuery(this).attr('name')+'_' + recordCount;

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
								//console.dir(jQuery(this).attr('name'));
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

						$('[name=moveroles_role_'+recordCount+ ']').val(v['role']);
						$('[name=moveroles_role_'+recordCount+ '_display]').val(v['emprole_desc']);
						$('[name=moveroles_role_'+recordCount+ '_display]').attr('readonly','readonly');

						$('[name=moveroles_employees_'+recordCount+']').val(v['user']);
						$('[name=moveroles_employees_'+recordCount + '_display]').val(v['employee_name']);
						$('[name=moveroles_employees_'+recordCount+ '_display]').attr('readonly','readonly');
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
					});


				}
			});
		}
	},

    moveTypeUpdate: function(moveType, prevType) {
        // stub if custom functionality is needed.
    },

    businessLineUpdate: function(moveType, prevType) {
        this.setBlockFieldsByBusinessLine();
    },

    initializeMoveType: function() {
        this.moveType = new Move_Type_Js();
        this.moveType.onMoveTypeChange(this.moveTypeUpdate, this);
        this.moveType.onBusinessLineChange(this.businessLineUpdate, this);
        this.moveType.registerEvents();

        // This (unfortunately) needs to be called to properly trigger the chain of events leading to loading the correct blocks.
        this.moveType.updateBusinessLine();
    },

    initializeDaysToMove: function() {
        this.daysToMove = new Days_To_Move_Js();
        this.daysToMove.loadFromFieldname = 'load_from';
        this.daysToMove.registerEvents();
    },

    initializeSalesPerson: function() {
        this.salesperson = new Sales_Person_Js();
        this.salesperson.registerEvents();
    },

    registerEvents : function() {
        var instance = $('input[name="instance"]').val();

        //moved from being instance variables to in here to be set on register
        this.defaultLeadSourceOptions = this.getPickOptions(jQuery('select[name="leadsource"]'));
        this.leadsourceWorkspaceOptions  = this.getPickOptions(jQuery('select[name="leadsource_workspace"]'));
        this.leadsourceNationalOptions  = this.getPickOptions(jQuery('select[name="leadsource_national"]'));
        this.leadsourceHHGOptions = this.getPickOptions(jQuery('select[name="leadsource_hhg"]'));

		this._super();
		if(jQuery('select[name="move_type"]').length) {
            this.initializeMoveType();
        }
        this.setDefaultLeadType();
        this.initializeDaysToMove();
        this.initializeSalesPerson();
		this.generateReceivedDate();
		this.initializeAddressAutofill('Leads');
		this.initializeReverseZipAutoFill('Leads');
		this.registerShipperTypeChangeEvent();
		this.registerleadStatusChangeEvent();
		this.registerPhoneTypeEvents();
		this.preventEmptyDestination();
		this.registerDispositionLostEvent();
		this.registerChangeDispositionLost();
		this.setDefaultInputs();
		this.registerCompPricingList();
		this.toggleNationalAccountDetails();
		this.registerChangeLeadType();
		this.validDate();
		this.registerSourceNameChange();
		this.appendCopyIfDuplication();
		this.setLeadSourceOptions();
		this.registerChangeFieldLeadReasonTypeReadOnly();

		this.setMoveTypeStateBased();
		jQuery('select[name="business_channel"]').val('Consumer').trigger('liszt:updated');
		jQuery('select[name="leadstatus"]').siblings('.chzn-container').find('.chzn-results').trigger('mouseup');
		jQuery('select[name="disposition_lost_reasons"]').siblings('.chzn-container').find('.chzn-results').trigger('mouseup');
		//jQuery('select[name="leadstatus"]').trigger('change');
		//jQuery('select[name="disposition_lost_reasons"]').trigger('change');

		loadBlocksByBusinesLine('Leads', 'business_line');
		this.updateTabIndexValues();
		this.registerVTExpertsComments();

        if(instance == 'sirva') {
            // Bind Load To and Load From date so they have to both be present, or neighter.
            this.bindLoadFromToDate();
        }else {
            this.registerEventsChangeAccount();
        }

		var status = jQuery('select[name="leadstatus"]').val();
		if(status == 'Cancelled' || status == 'Duplicate') {
			jQuery('.editViewContainer *').filter(':input').each(function () {
				if (jQuery(this).hasClass('chzn-select')) {
					jQuery(this).prop('disabled', true).trigger('liszt:updated');
				} else if (jQuery(this).hasClass('dateField')) {
					jQuery(this).prop("disabled", true);
				} else if (jQuery(this).prop('type') == 'checkbox') {
					jQuery(this).prop("disabled", true);
				} else {
					jQuery(this).prop('readonly', true);
				}
			});
		}
	},

    //Sirva requires that there are either a load from AND load to date, or nieghter. Can't be one without the other.
    bindLoadFromToDate: function() {
        var load_from = jQuery('#Leads_editView_fieldName_load_from');
        var load_to = jQuery('#Leads_editView_fieldName_load_to');
        var labels = jQuery.merge(load_from.closest('td').prev().first(), load_to.closest('td').prev().first());
        jQuery.merge(load_from, load_to).on('change', function() {
            //If we have a date in either, we need them both to be manditory
            if (load_from.val().length > 0 || load_to.val().length > 0) {
                //Since they are bound to have the same requirements, we only need to check if one is already required
                if (load_from.data('validation-engine').indexOf("required") < 0) {
                    jQuery.merge(load_from, load_to).attr('data-validation-engine', 'validate[ required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]');
                    jQuery.merge(load_from, load_to).data('validation-engine', 'validate[ required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]');
                    jQuery(labels).each(function() {
                        var name = '<span class="redColor">*</span>' + jQuery(this).html();
                        jQuery(this).html(name);
                    });
                }
            } else {
                jQuery.merge(load_from, load_to).attr('data-validation-engine', 'validate[funcCall[Vtiger_Base_Validator_Js.invokeValidation]]');
                jQuery.merge(load_from, load_to).data('validation-engine', 'validate[funcCall[Vtiger_Base_Validator_Js.invokeValidation]]');
                jQuery(labels).each(function() {
                    var name = jQuery(this).html().replace('<span class="redColor">*</span>', '');
                    jQuery(this).html(name);
                });
            }
        });
    },

	/**
	 * Function to get child comments
	 */
	getChildComments : function(commentId){
		var aDeferred = jQuery.Deferred();
		var url= 'module='+app.getModuleName()+'&view=Detail&record='+this.getRecordId()+'&mode=showChildComments&commentid='+commentId;
		var dataObj = this.getCommentThread(url);
		dataObj.then(function(data){
			aDeferred.resolve(data);
		});
		return aDeferred.promise();
	},

	/**
	 * function to return the UI of the comment.
	 * return html
	 */
	getCommentUI : function(commentId){
		var aDeferred = jQuery.Deferred();
		var postData = {
			'view' : 'DetailAjax',
			'module' : 'ModComments',
			'record' : commentId
		};
		AppConnector.request(postData).then(
			function(data){
				aDeferred.resolve(data);
			},
			function(error,err){

			}
		);
		return aDeferred.promise();
	},

	/**
	 * function to save comment
	 * return json response
	 */
	saveComment : function(e) {
		var thisInstance = this;
		var aDeferred = jQuery.Deferred();
		var currentTarget = jQuery(e.currentTarget);
		var commentMode = currentTarget.data('mode');
		var closestCommentBlock = currentTarget.closest('.addCommentBlock');
		var commentContent = closestCommentBlock.find('.commentcontent');
		var commentContentValue = commentContent.val();
		var errorMsg;
		if(commentContentValue == ""){
			errorMsg = app.vtranslate('JS_LBL_COMMENT_VALUE_CANT_BE_EMPTY');
			commentContent.validationEngine('showPrompt', errorMsg , 'error','bottomLeft',true);
			aDeferred.reject();
			return aDeferred.promise();
		}
		if(commentMode == "edit"){
			var editCommentReason = closestCommentBlock.find('[name="reasonToEdit"]').val();
		}

		var progressIndicatorElement = jQuery.progressIndicator({});
		var element = jQuery(e.currentTarget);
		element.attr('disabled', 'disabled');

		var commentInfoHeader = closestCommentBlock.closest('.commentDetails').find('.commentInfoHeader');
		var commentId = commentInfoHeader.data('commentid');
		var parentCommentId = commentInfoHeader.data('parentcommentid');
		var postData = {
			'commentcontent' : 	commentContentValue,
			'related_to': jQuery('input[name="record"]',currentTarget.closest('form')).val(),
			'module' : 'ModComments'
		};

		if(commentMode == "edit"){
			postData['record'] = commentId;
			postData['reasontoedit'] = editCommentReason;
			postData['parent_comments'] = parentCommentId;
			postData['mode'] = 'edit';
			postData['action'] = 'Save';
		} else if(commentMode == "add"){
			postData['parent_comments'] = commentId;
			postData['action'] = 'SaveAjax';
		}
		AppConnector.request(postData).then(
			function(data){
				progressIndicatorElement.progressIndicator({'mode':'hide'});
				aDeferred.resolve(data);
			},
			function(textStatus, errorThrown){
				progressIndicatorElement.progressIndicator({'mode':'hide'});
				element.removeAttr('disabled');
				aDeferred.reject(textStatus, errorThrown);
			}
		);

		return aDeferred.promise();
	},

	registerVTExpertsComments : function() {
		var thisInstance = this;
		var editViewForm = this.getForm();
		editViewForm.on('click','.saveComment', function(e){
			var element = jQuery(e.currentTarget);
			if(!element.is(":disabled")) {
				var currentTarget = jQuery(e.currentTarget);
				var mode = currentTarget.data('mode');
				var dataObj = thisInstance.saveComment(e);
				dataObj.then(function(data){
					var closestAddCommentBlock = currentTarget.closest('.addCommentBlock');
					var commentTextAreaElement = closestAddCommentBlock.find('.commentcontent');
					var commentInfoBlock = currentTarget.closest('.singleComment');
					commentTextAreaElement.val('');
					if(mode == "add"){
						var commentId = data['result']['id'];
						var commentHtml = thisInstance.getCommentUI(commentId);
						commentHtml.then(function(data){
							var commentBlock = closestAddCommentBlock.closest('.commentDetails');
							var detailContentsHolder = editViewForm;
							var noCommentsMsgContainer = jQuery('.noCommentsMsgContainer',detailContentsHolder);
							noCommentsMsgContainer.remove();
							if(commentBlock.length > 0){
								closestAddCommentBlock.remove();
								var childComments = commentBlock.find('ul');
								if(childComments.length <= 0){
									var currentChildCommentsCount = commentInfoBlock.find('.viewThreadBlock').data('childCommentsCount');
									var newChildCommentCount = currentChildCommentsCount + 1;
									commentInfoBlock.find('.childCommentsCount').text(newChildCommentCount);
									var parentCommentId = commentInfoBlock.find('.commentInfoHeader').data('commentid');
									thisInstance.getChildComments(parentCommentId).then(function(responsedata){
										jQuery(responsedata).appendTo(commentBlock);
										commentInfoBlock.find('.viewThreadBlock').hide();
										commentInfoBlock.find('.hideThreadBlock').show();
									});
								}else {
									jQuery('<ul class="liStyleNone"><li class="commentDetails">'+data+'</li></ul>').appendTo(commentBlock);
								}
							} else {
								jQuery('<ul class="liStyleNone"><li class="commentDetails">'+data+'</li></ul>').prependTo(closestAddCommentBlock.closest('.commentContainer').find('.commentsList'));
								commentTextAreaElement.css({height : '71px'});
							}
							commentInfoBlock.find('.commentActionsContainer').show();
						});
					}else if(mode == "edit"){
						var modifiedTime = commentInfoBlock.find('.commentModifiedTime');
						var commentInfoContent = commentInfoBlock.find('.commentInfoContent');
						var commentEditStatus = commentInfoBlock.find('[name="editStatus"]');
						var commentReason = commentInfoBlock.find('[name="editReason"]');
						commentInfoContent.html(data.result.commentcontent);
						commentReason.html(data.result.reasontoedit);
						modifiedTime.text(data.result.modifiedtime);
						modifiedTime.attr('title',data.result.modifiedtimetitle);
						if(commentEditStatus.hasClass('hide')){
							commentEditStatus.removeClass('hide');
						}
						if(data.result.reasontoedit != ""){
							commentInfoBlock.find('.editReason').removeClass('hide')
						}
						commentInfoContent.show();
						commentInfoBlock.find('.commentActionsContainer').show();
						closestAddCommentBlock.remove();
					}
					element.removeAttr('disabled');
				});
			}
		});
	},

	registerleadStatusChangeEvent : function() {
		//declare general variables
		var thisInstance = this;
		var selectTag = jQuery('select[name="lead_type"]');
		//add a mouseup event on chosen dropdown for the select tag
		selectTag.siblings('.chzn-container').find('.chzn-results').on('mouseup', function() {
			//find what we have selected
			var selectedOption = selectTag.find('option:selected').val();
			//declare nodes for the picklist we will be limiting
			var leadStatusNode = jQuery('select[name="leadstatus"]');
			var prevSelectedNode = leadStatusNode.find('option:selected');
			var prevSelected = prevSelectedNode.val();
			//undisable any of the options that we may have previously disabled
			leadStatusNode.find('option:disabled').each(function () {
				jQuery(this).prop('disabled',false);
			});
			//handle OA survey option
			if(selectedOption == 'OA Survey'){
				//change value if set to a newly disabled option
				if(prevSelected == 'Inactive' || prevSelected == 'Pending'){
					leadStatusNode.find('option[value="New"]').prop('selected',true);
				}
				//disable inappropriate options
				leadStatusNode.find('option[value="Inactive"]').prop('disabled',true);
				leadStatusNode.find('option[value="Pending"]').prop('disabled',true);
			} else{
				//change value if set to a newly disabled option
				if(prevSelected == 'Fax/Busy' || prevSelected == 'No Answer' || prevSelected == 'Left Voicemail' || prevSelected == 'Prefer Call Back' || prevSelected == 'Do Not Call Requested' || prevSelected == 'Wrong/Disconnected #' || prevSelected == 'Completed'){
					leadStatusNode.find('option[value="New"]').prop('selected',true);
				}
				//disable inappropriate options
				leadStatusNode.find('option[value="Fax/Busy"]').prop('disabled',true);
				leadStatusNode.find('option[value="No Answer"]').prop('disabled',true);
				leadStatusNode.find('option[value="Left Voicemail"]').prop('disabled',true);
				leadStatusNode.find('option[value="Prefer Call Back"]').prop('disabled',true);
				leadStatusNode.find('option[value="Do Not Call Requested"]').prop('disabled',true);
				leadStatusNode.find('option[value="Wrong/Disconnected #"]').prop('disabled',true);
				leadStatusNode.find('option[value="Completed"]').prop('disabled',true);
			}
			//update list
			leadStatusNode.trigger("liszt:updated");
		});
	},

	toggleNationalAccountDetails: function() {
		var shipperType = jQuery('select[name="shipper_type"]');
		if (shipperType.val() == 'COD') {
			jQuery('table[name="LBL_LEADS_NATIONALACCOUNT"]').addClass('hide');
		}

		shipperType.change(function() {
			if (jQuery(this).val() == 'COD') {
				jQuery('table[name="LBL_LEADS_NATIONALACCOUNT"]').addClass('hide');
			} else {
				jQuery('table[name="LBL_LEADS_NATIONALACCOUNT"]').removeClass('hide');
			}
		});
	},

	appendCopyIfDuplication: function() {
		var isDup = jQuery('#duplicate').val();
		if(isDup) {
			var lastName = jQuery('#Leads_editView_fieldName_lastname').val();
			var copy = lastName+' Copy';
			jQuery('#Leads_editView_fieldName_lastname').val(copy);
		}
	},
	registerEventsChangeAccount: function () {
		var thisInstance = this;
		jQuery('[name="related_account"]').on(Vtiger_Edit_Js.referenceSelectionEvent,function () {
			thisInstance.registerEventsChangeBusinessByAccount();

		});
		jQuery('[name="related_account"]').on(Vtiger_Edit_Js.referenceDeSelectionEvent,function () {
			thisInstance.registerEventsChangeBusinessByAccount();

		});
		jQuery('[name="related_account"]').trigger(Vtiger_Edit_Js.referenceSelectionEvent);
	},
	registerEventsChangeBusinessByAccount: function () {
		var currentField = jQuery('[name="business_line2"]');
		var ValAccount = jQuery('[name="related_account"]').val();
		var params = {
			module: 'Leads',
			action: 'ActionAjax',
			mode: 'getBusinessLineByAccount',
			ValAccount: ValAccount
		}
		var progressIndicatorElement = jQuery.progressIndicator({
			'position': 'html',
			'blockInfo': {
				'enabled': true
			}
		});
		AppConnector.request(params).then(function (data) {
			if(data.success) {
				var selectedVal=currentField.val();
				progressIndicatorElement.progressIndicator({'mode': 'hide'});
				var response = data.result;
				var options = '<option value="">Select an Option</option>';
				jQuery.each(response, function (index, picklistObj) {
					var selected="";
					picklistObj = jQuery('<div><div>').html(picklistObj).text();
					if(selectedVal == picklistObj) {
						selected="selected='selected'";
					}
					options += '<option value="' + picklistObj + '" '+selected+'>' + picklistObj + '</option>';
				})
				currentField.html(options);
				currentField.trigger('liszt:updated');
			}
		})
	},
	registerShipperTypeChangeEvent : function() {
		//declare general variables
		var thisInstance = this;
		var selectTag = jQuery('select[name="shipper_type"]');

		//add a mouseup event on the chosen dropdown for our select tag
		selectTag.siblings('.chzn-container').find('.chzn-results').on('mouseup', function() {
			//find what we have selected
			var selectedOption = selectTag.find('option:selected').val();
			//declare nodes for the picklist we will be limiting
			var leadTypeNode = jQuery('select[name="lead_type"]');
			var prevSelectedNode = leadTypeNode.find('option:selected');
			var prevSelected = prevSelectedNode.val();
			//undisable any of the options that we may have previously disabled
			leadTypeNode.find('option:disabled').each(function () {
				jQuery(this).prop('disabled',false);
			});
			//handle the COD option
			if(selectedOption == 'COD'){
				//only change stuff if the value selected isn't an allowable value for COD
				if(prevSelected != 'Consumer' && prevSelected != 'OA Survey'){
					//unselect the not allowable value
					prevSelectedNode.prop('selected',false);
					//set Consumer as the default selected value
					leadTypeNode.find('option[value="Consumer"]').prop('selected',true);
				}
				//disable anything that shouldn't be allowable
				leadTypeNode.find('option[value="National Account"]').prop('disabled',true);
			} else if (selectedOption == 'NAT'){ //handle the NAT option
				//only change stuff if the value selected isn't an allowable value for NAT
				if(prevSelected != 'National Account' && prevSelected != 'OA Survey'){
					//unselect the not allowable value
					prevSelectedNode.prop('selected',false);
					//set National Account as the default selected value
					leadTypeNode.find('option[value="National Account"]').prop('selected',true);
				}
				//disable anything that shouldn't be allowable
				leadTypeNode.find('option[value="Consumer"]').prop('disabled',true);
			}
			//update the chosen on the picklist we changed
			leadTypeNode.trigger("liszt:updated");
		});
	}
});
