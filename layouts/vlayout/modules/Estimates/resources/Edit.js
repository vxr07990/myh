/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

Quotes_Edit_Js('Estimates_Edit_Js', {
    primaryconf : function() {
        if(!jQuery('input[name="is_primary"]').is(':checked')) {
            return true;
        } else {
            var sourceModule = getQueryVariable('sourceModule') == 'Opportunities' ? 'Opportunity' : getQueryVariable('sourceModule').slice(0, -1);
            var moduleName   = getQueryVariable('module').slice(0, -1);
            var box = confirm("The parent " + sourceModule + " already has a primary " + moduleName + ". Override and make this the primary?");
            return box;
        }
    },

    checkParents : function() {
        var isPrimaryCheckbox = jQuery('input:checkbox[name="is_primary"]');
        var oppField = jQuery('input[name="potential_id"]').val();
        var orderField = jQuery('input[name="orders_id"]').val();
        if(oppField || orderField) {
            if (!isPrimaryCheckbox.is(':checked')) {
                return true;
            } else {
                isPrimaryCheckbox.closest('td').progressIndicator();
                isPrimaryCheckbox.addClass('hide');

                // Changed this to use the JSON system, since it is more manageable and AppConnector will actually check
                // for empty variables and not include them in the request.
                var params = {
                    'type': 'GET',
                    'url': 'index.php',
                    'data': {
                        'module': 'Estimates',
                        'action': 'CheckPrimary',
                        'orderid': orderField,
                        'potentialid': oppField
                    }
                };

                AppConnector.request(params).then(
                    function (data) {
                        if (data.success) {
                            if (data.result['hasParent'] == 1) {
                                var moduleName = getQueryVariable('module').slice(0, -1);
                                var box = confirm("The parent " + data.result['parentModule'] + " already has a primary " + moduleName + ". Override and make this the primary?");
                                if (box) {
                                    isPrimaryCheckbox.attr('checked', true);
                                }
                            } else {
                                isPrimaryCheckbox.attr('checked', true);
                            }

                            isPrimaryCheckbox.closest('td').progressIndicator({'mode': 'hide'});
                            isPrimaryCheckbox.removeClass('hide');
                        }
                    }
                );
                return false;
            }
        } else {
            return true;
        }
    }
}, {
	moduleName : 'Estimates',

	currentTariff : '',

	previousEffectiveTariff : '',

	effectiveTariffData : false,
	customerJs: false,

	afterTariffLoadCallbacks: [],
	afterTariffPicklistUpdateCallbacks: [],

    localValuation: null,

    showAlertBox : function(data){
        var aDeferred = jQuery.Deferred();
        var bootBoxModal = bootbox.alert(data['message'], function(result) {
            if(result){
                aDeferred.reject(); //we only want the button to make the modal box disappear
            } else{
                aDeferred.reject();
            }
        });

        bootBoxModal.on('hidden',function(e){
            //In Case of multiple modal. like mass edit and quick create, if bootbox is shown and hidden , it will remove
            // modal open
            if(jQuery('#globalmodal').length > 0) {
                // Mimic bootstrap modal action body state change
                jQuery('body').addClass('modal-open');
            }
        });
        return aDeferred.promise();
    },

	updateTabIndexValues: function() {
		var tabindex = 1;
		jQuery('table').each(function() {
			if (!jQuery(this).hasClass('hide')) {
				if(jQuery(this).hasClass('block_LBL_ADDRESS_INFORMATION') || jQuery(this).hasClass('block_LBL_QUOTES_SITDETAILS')){
					var row = 1;
					jQuery(this).find('input,select, textarea').each(function() {
						if (this.type != "hidden" && !jQuery(this).closest('td').hasClass('hide')) {
							if(row == 1){
								var $input = jQuery(this);
								$input.attr("tabindex", tabindex);
								tabindex++;
								row = 2;
							}
							else{
								row = 1;
							}
						}
					});
					var row = 1;
					jQuery(this).find('input,select, textarea').each(function() {
						if (this.type != "hidden" && !jQuery(this).closest('td').hasClass('hide')) {
							if(row == 2){
								var $input = jQuery(this);
								$input.attr("tabindex", tabindex);
								tabindex++;
								row = 1;
							}
							else{
								row = 2;
							}
						}
					});
				}
				else{
					jQuery(this).find('input,select, textarea').each(function() {
						if (this.type != "hidden" && !jQuery(this).closest('td').hasClass('hide')) {
							var $input = jQuery(this);
							$input.attr("tabindex", tabindex);
							tabindex++;
						}
					});
				}
			}
		});
	},

	lineItemsJs: false,

    //Override Vtiger_Edit_Js registerSubmitEvent to add Service Charge handling
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

                //Serialize Service Charges into a single hidden input
                Service_Charges_Js.compile();
            }
        });
    },

	setReferenceFieldValue : function(container, params) {
		var sourceField = container.find('input[class="sourceField"]').attr('name');
		var fieldElement = container.find('input[name="'+sourceField+'"]');
		var sourceFieldDisplay = sourceField+"_display";
		var fieldDisplayElement = container.find('input[name="'+sourceFieldDisplay+'"]');
		var popupReferenceModule = container.find('input[name="popupReferenceModule"]').val();
		var selectedName = params.name;
		var id = params.id;

		fieldElement.val(id)
		fieldDisplayElement.val(selectedName).attr('readonly',true);
		if(!params.suppress) {
			fieldElement.trigger(Vtiger_Edit_Js.referenceSelectionEvent, {'source_module' : popupReferenceModule, 'record' : id, 'selectedName' : selectedName});
		}

		fieldDisplayElement.validationEngine('closePrompt',fieldDisplayElement);
		fieldElement.trigger('change');
	},

	getCurrentDate: function(format) {
		var today = new Date();
		var dd = today.getDate();
		var mm = today.getMonth()+1;
		var yyyy = today.getFullYear();

		if(dd<10) {
			dd='0'+dd;
		}

		if(mm<10) {
			mm='0'+mm;
		}

		if(format == 'yyyy-mm-dd') {
			return yyyy+'-'+mm+'-'+dd;
		} else if(format == 'mm-dd-yyyy') {
			return mm+'-'+dd+'-'+yyyy;
		} else {
			return dd+'-'+mm+'-'+yyyy;
		}
	},

	registerReportsButton: function () {
		var thisInstance = this;
		jQuery('.contentsDiv').on('click', '#getReportSelectButton', function() {
			thisInstance.currentTariff.reportButtonEdit();
		});
	},

	registerInterstateRateEstimate: function () {
		var thisInstance = this;
		jQuery('.contentsDiv').on('click', '#interstateRateQuick', function () {
			thisInstance.currentTariff.quickRateEdit();
		});
		jQuery('.contentsDiv').on('click', '.interstateRateDetail', function () {
			thisInstance.currentTariff.detailedRateEdit(false);
		});
        jQuery('.contentsDiv').on('click', '.requote', function () {
            thisInstance.currentTariff.detailedRateEdit(true);
        });
	},

	validateMoveType: function() {
        var moveType = jQuery('select[name="move_type"]').val();
        var orgState = jQuery('input[name="origin_state"]').val();
        var destState = jQuery('input[name="destination_state"]').val();
        var error = false;
        var state = '';
        switch(moveType) {
            case 'Local CA':
                if(orgState != '' && destState != '') {
                    if(orgState.toLowerCase() != 'ca' || destState.toLowerCase() != 'ca')  {
                        error = true;
                        state = 'CA';
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
                bootbox.alert('The origin and destination state should be set to '+state+' for the move type selected.');
            }
            return false;
        }
        return true;
    },

	// local tariff events
	registerLocalRateLookup : function () {
		var thisInstance = this;
		jQuery('.contentsDiv').on('blur', '.LocalBaseRateTrans', function() {
			var thisNode = jQuery(this);
			var prevValueNode = thisNode.parent().find('.fieldname');
			var prevValue = prevValueNode.data('prevValue');
			if (jQuery(this).val() == prevValue) {
				return;
			}
			var calledField = jQuery(this).attr('name');
			var regExp = /\d+/g;
			var serviceid = calledField.match(regExp);
			var dataURL = 'index.php?module=TariffServices&action=LocalRateLookup&serviceid='+serviceid[0]+'&weight='+jQuery("input[name='Weight"+serviceid[0]+"']").val()+'&miles='+jQuery("input[name='Miles"+serviceid[0]+"']").val();
			AppConnector.request(dataURL).then(
				function(data) {
					if(data.success) {
						jQuery("input[name='Rate"+serviceid[0]+"']").val(data.result.rate);
						jQuery("input[name='Excess"+serviceid[0]+"']").val(data.result.excess);
						prevValueNode.data('prevValue', thisNode.val());
					}
				},
				function(error) {

				}
			);
		});

	},

	registerLocalCalcWeightLookup : function () {
		var thisInstance = this;
		jQuery('.contentsDiv').on('blur', '.localBreakPoint', function () {
			var thisNode = jQuery(this);
			var prevValueNode = thisNode.parent().find('.fieldname');
			var prevValue = prevValueNode.data('prevValue');
			if (jQuery(this).val() == prevValue) {
				return;
			}
			var calledField = jQuery(this).attr('name');
			var regExp = /\d+/g;
			var serviceid = calledField.match(regExp);
			var url = 'index.php?module=TariffServices&action=LocalCalcWeightLookup&serviceid='+serviceid[0]+'&weight='+jQuery("input[name='Weight"+serviceid[0]+"']").val()+'&miles='+jQuery("input[name='Miles"+serviceid[0]+"']").val();
			AppConnector.request(url).then(
				function(data) {
					if(data.success) {
						jQuery("input[name='Rate"+serviceid[0]+"']").val(data.result.rate);
						jQuery("input[name='calcWeight"+serviceid[0]+"']").val(data.result.calcWeight);
						prevValueNode.data('prevValue', thisNode.val());
					}
				},
				function(error) {
					console.log('error');
				}
			);
		});

	},

	registerLocalWeightMileLookup : function () {
		var thisInstance = this;
		jQuery('.contentsDiv').on('blur', '.localWeightMile', function () {
			var thisNode = jQuery(this);
			var prevValueNode = thisNode.parent().find('.fieldname');
			var prevValue = prevValueNode.data('prevValue');
			if (jQuery(this).val() == prevValue) {
				return;
			}
			var calledField = jQuery(this).attr('name');
			var regExp = /\d+/g;
			var serviceid = calledField.match(regExp);
			var dataURL = 'index.php?module=TariffServices&action=LocalWeightMileLookup&serviceid='+serviceid[0]+'&weight='+jQuery("input[name='Weight"+serviceid[0]+"']").val()+'&miles='+jQuery("input[name='Miles"+serviceid[0]+"']").val();
			AppConnector.request(dataURL).then(
				function(data) {
					if(data.success) {
						jQuery("input[name='Rate"+serviceid[0]+"']").val(data.result.rate);
						prevValueNode.data('prevValue', thisNode.val());
					}
				},
				function(error) {
				}
			);
		});
	},

	registerLocalCWTbyWeightLookup : function() {
		var thisInstance = this;
		jQuery('.contentsDiv').on('blur', '.localCWTbyWeight', function () {
			var thisNode = jQuery(this);
			var prevValueNode = thisNode.parent().find('.fieldname');
			var prevValue = prevValueNode.data('prevValue');
			if (jQuery(this).val() == prevValue) {
				return;
			}
			var calledField = jQuery(this).attr('name');
			var regExp = /\d+/g;
			var serviceid = calledField.match(regExp);
			var dataURL = 'index.php?module=TariffServices&action=LocalCWTbyWeightLookup&serviceid='+serviceid[0]+'&weight='+jQuery("input[name='Weight"+serviceid[0]+"']").val();
			AppConnector.request(dataURL).then(
				function(data) {
					if(data.success) {
						jQuery("input[name='Rate"+serviceid[0]+"']").val(data.result.rate);
						prevValueNode.data('prevValue', thisNode.val());
					}
				},
				function(error) {
				}
			);
		});
	},

	registerLocalCountyLookup : function () {
		jQuery('.contentsDiv').on('value_change', 'select[name^="County"]', function() {
			var county = $(this).chosen().val();
			var calledField = jQuery(this).attr('name');
			var regExp = /\d+/g;
			var serviceid = calledField.match(regExp);

			var dataURL = 'index.php?module=TariffServices&action=LocalCountyLookup&serviceid='+serviceid[0]+'&county='+county;
			AppConnector.request(dataURL).then(
				function(data) {
					if(data.success) {
						jQuery("input[name='Rate"+serviceid[0]+"']").val(data.result.rate);
					}
				},
				function(error) {
				}
			);
		});
	},

	registerLocalHourlySetLookup : function () {
		var thisInstance = this;
		jQuery('.contentsDiv').on('blur', '.localHourlySet', function () {
			var thisNode = jQuery(this);
			var prevValueNode = thisNode.parent().find('.fieldname');
			var prevValue = prevValueNode.data('prevValue');
			if (jQuery(this).val() == prevValue) {
				return;
			}
			var calledField = jQuery(this).attr('name');
			var regExp = /\d+/g;
			var serviceid = calledField.match(regExp);
			var dataURL = 'index.php?module=TariffServices&action=LocalHourlySetLookup&serviceid='+serviceid[0]+'&men='+jQuery("input[name='Men"+serviceid[0]+"']").val()+'&vans='+(jQuery("input[name='Vans"+serviceid[0]+"']").length ? jQuery("input[name='Vans"+serviceid[0]+"']").val() : '');
			AppConnector.request(dataURL).then(
				function(data) {
					if(data.success) {
						jQuery("input[name='Rate"+serviceid[0]+"']").val(data.result.rate);
						prevValueNode.data('prevValue', thisNode.val());
					}
				},
				function(error) {
				}
			);
		});

	},

	registerLocalValuationLookup : function () {
		var thisInstance = this;
		jQuery('.contentsDiv').on('value_change', 'select[name^="Deductible"]', function() {
			var calledField = jQuery(this).closest('td').find('select').attr('name');
			var regExp = /\d+/g;
			var serviceid = calledField.match(regExp);
			//var deductible = jQuery(this).find('.result-selected').html();
			var deductible = jQuery(this).val();
			var dataURL = 'index.php?module=TariffServices&action=LocalValuationLookup&serviceid='+serviceid[0]+'&deductible='+deductible;
			AppConnector.request(dataURL).then(
				function(data) {
					if(data.success) {
						jQuery("input[name='Rate"+serviceid[0]+"']").val(data.result.rate);
					}
				},
				function(error) {
				}
			);
		});
	},

	registerHiddenByValuation : function () {
		var thisInstance = this;
		jQuery('.contentsDiv').on('value_change', 'select[name^="ValuationType"]', function() {
			thisInstance.hideByValuation(this);
		});
        this.hideByValuation();
	},

    hideByValuation : function(valType) {
        if(!valType) {
            var thisI = this;
            valType = 'select[name^="ValuationType"]';
            $(valType).each(function() {
                thisI.hideByValuation(this);
            });
            return;
        }
        var calledField = jQuery(valType).attr('name');
        if(!calledField) {
            return;
        }
        var regExp = /\d+/g;
        var serviceid = calledField.match(regExp);
        var selectedValue = jQuery(valType).find('option:selected').html();

        if (selectedValue == 'Full Valuation') {
            jQuery('.noValuation'+serviceid[0]).addClass('hide');
            jQuery('.releasedValuation'+serviceid[0]).addClass('hide');
            jQuery('.releasedValuation'+serviceid[0]).find('input').attr('disabled', true);
            jQuery('.releasedValuation'+serviceid[0]).find('select').attr('disabled', true);

            jQuery('.fullValuation'+serviceid[0]).removeClass('hide');
            jQuery('.fullValuation'+serviceid[0]).find('input').attr('disabled', false);
            jQuery('.fullValuation'+serviceid[0]).find('select').attr('disabled', false);
        }
        else if (selectedValue == 'Released Valuation') {
            jQuery('.noValuation'+serviceid[0]).addClass('hide');
            jQuery('.fullValuation'+serviceid[0]).addClass('hide');
            jQuery('.fullValuation'+serviceid[0]).find('input').attr('disabled', true);
            jQuery('.fullValuation'+serviceid[0]).find('select').attr('disabled', true);

            jQuery('.releasedValuation'+serviceid[0]).removeClass('hide');
            jQuery('.releasedValuation'+serviceid[0]).find('input').attr('disabled', false);
            jQuery('.releasedValuation'+serviceid[0]).find('select').attr('disabled', false);
        }
        else {
            jQuery('.releasedValuation'+serviceid[0]).addClass('hide');
            jQuery('.releasedValuation'+serviceid[0]).find('input').attr('disabled', true);
            jQuery('.releasedValuation'+serviceid[0]).find('select').attr('disabled', true);
            jQuery('.fullValuation'+serviceid[0]).addClass('hide');
            jQuery('.fullValuation'+serviceid[0]).find('input').attr('disabled', true);
            jQuery('.fullValuation'+serviceid[0]).find('select').attr('disabled', true);

            jQuery('.noValuation'+serviceid[0]).removeClass('hide');
        }
    },

	registerLocalTabledValuationLookup : function () {
		var thisInstance = this;
		jQuery('.contentsDiv').on('change', '.localValuationPick', function() {

			var calledField = jQuery(this).closest('td').find('select').attr('name');

			var regExp = /\d+/g;
			var serviceid = calledField.match(regExp);

			var dedVal = jQuery('select[name="Deductible'+serviceid[0]+'"]').siblings('.chzn-container').find('.result-selected').html();

			var amVal = jQuery('select[name="Amount'+serviceid[0]+'"]').siblings('.chzn-container').find('.result-selected').html();


			if(dedVal == 'Select an Option' || amVal == 'Select an Option' ) {
				return;
			}
			else {
				var dataURL = 'index.php?module=TariffServices&action=LocalTabledValuationLookup&serviceid='+serviceid[0]+'&deductible='+dedVal+'&amount='+amVal;
				AppConnector.request(dataURL).then(
					function(data) {
						if(data.success) {
							jQuery("input[name='Rate"+serviceid[0]+"']").val(data.result.rate);
						}
					},
					function(error) {
					}
				);
			}
		});
	},

	// END local tariff events

	registerShuttleMinMilesValidation: function() {
		jQuery('.contentsDiv').on('value_change', 'input[name="acc_shuttle_origin_over25"], input[name="acc_shuttle_dest_over25"]', function() {
			if(jQuery(this).prop('name') == 'acc_shuttle_origin_over25') {
				var mileField = jQuery('input[name="acc_shuttle_origin_miles"]');
			} else {
				var mileField = jQuery('input[name="acc_shuttle_dest_miles"]');
			}

			if(jQuery(this).prop('checked') && mileField.val()<26) {
				mileField.val('26');
			} else if(mileField.val()>25) {
				mileField.val('25');
			}
		});
	},

	registerAddressLookupComponentForms : function() {
		var thisInstance = this;
		var moduleName = Estimates_Edit_Js.I().moduleName;
		thisInstance.originComponentForm = {
			street_address: moduleName + '_editView_fieldName_origin_address1',
			locality: moduleName + '_editView_fieldName_origin_city',
			administrative_area_level_1: moduleName + '_editView_fieldName_origin_state',
			country: moduleName + '_editView_fieldName_estimates_origin_country',
			postal_code: moduleName + '_editView_fieldName_origin_zip'
		};
		thisInstance.destinationComponentForm = {
			street_address: moduleName + '_editView_fieldName_destination_address1',
			locality: moduleName + '_editView_fieldName_destination_city',
			administrative_area_level_1: moduleName + '_editView_fieldName_destination_state',
			country: moduleName + '_editView_fieldName_estimates_destination_country',
			postal_code: moduleName + '_editView_fieldName_destination_zip'
		};
	},

	registerWeightChangeEvent : function() {
		var thisInstance = this;
        if($('[name="instance"]').val() != 'sirva') {
            jQuery('.contentsDiv').on('value_change', 'input[name="weight"],[name="min_declared_value_mult"]', function() {
                thisInstance.updateValuation();
            });
        }
	},

    updateValuation: function () {
        this.ValuationJS.enforceMinimumValuation();
    },

	afterTariffPicklistUpdate: function(callback)
	{
		this.afterTariffPicklistUpdateCallbacks.push(callback);
	},

	doAfterTariffPicklistUpdate: function()
	{
		var list = this.afterTariffPicklistUpdateCallbacks;
		this.afterTariffPicklistUpdateCallbacks = [];
		for(var i=0;i<list.length;++i)
		{
			list[i]();
		}

        if(jQuery('[name="business_line_est"]').val().indexOf('Local') != -1){
            this.showFieldByName('is_multi_day',true);
        }else{
            this.showFieldByName('is_multi_day',false);
        }
	},


	afterTariffLoad: function(callback)
	{
		this.afterTariffLoadCallbacks.push(callback);
	},

	doAfterTariffLoad: function()
	{
		var list = this.afterTariffLoadCallbacks;
		this.afterTariffLoadCallbacks = [];
		for(var i=0;i<list.length;++i)
		{
			list[i]();
		}
	},

	addRelationURLParams: function(url) {
		var isRelation = getQueryVariable('relationOperation');
		var sourceRecord = getQueryVariable('sourceRecord');
		var sourceModule = getQueryVariable('sourceModule');
		if(isRelation && sourceRecord)
		{
			url += '&relationOperation=true&sourceRecord=' + sourceRecord + '&sourceModule=' + sourceModule;
		}
		return url;
	},

	loadLocalMoveContents: function() {
		var thisInstance = this;
		jQuery('#isLocalRating').val(1);
		var newTariff = jQuery('[name="effective_tariff"]').val();
		var effDate = Vtiger_Edit_Js.getDate(jQuery('[name="effective_date"]'));
		var dateStr = (effDate.getYear() +1900) + '-' + (effDate.getMonth()+1) + '-' + effDate.getDate();

		var loadBlocks = ['LOCAL_MOVE_CONTENTS'];
		var show = '.localMoveContent:not(.inactive,.inactiveBlock)';
		if(typeof thisInstance.customerJs.getShowQuery != 'undefined')
		{
			var show = thisInstance.customerJs.getShowQuery(show, false);
		}
		var hide = '.interstateContent:not(.localMoveContent)';
		// TODO: refactor the show/hide to the proper place
		jQuery(hide).addClass('hide');
		var checkBlocks = jQuery('.sectionContentHolder.inactive.localMoveContent');
		checkBlocks.each(function() {
			// contentHolder_
			loadBlocks.push(jQuery(this).attr('id').substr(14));
		});
		var progressElement = jQuery.progressIndicator({
			'position' : 'html',
			'blockInfo' : {
				'enabled' : true
			}
		});
		var url = 'index.php?action=ReloadContents&_controllerBlockList='+loadBlocks.join(',')+'&effective_tariff_id=' + newTariff + '&effective_date=' + dateStr + '&module=' + thisInstance.moduleName + '&view=Edit&record=' + jQuery('[name="record"]').val();
		url = this.addRelationURLParams(url);
		AppConnector.request(url).then(function(data) {
			Vtiger_Edit_Js.I().loadContentData(data);
			Estimates_Edit_Js.I().customerJs.postReloadContents(false);
            thisInstance.localValuation.registerEvents();
			thisInstance.updateTabIndexValues();
			jQuery(show).removeClass('hide');
			progressElement.progressIndicator({'mode': 'hide'});
			thisInstance.doAfterTariffLoad();
		});
	},
        

	registerTariffChangedEvent: function() {
    	var thisInstance = this;
    	jQuery('[name="effective_tariff"]').on('value_change', function() {
    		var prevTariff = jQuery(this).data('prev-value');
    		var newTariff = jQuery(this).val();
			if(!newTariff)
			{
				jQuery('.interstateContent').addClass('hide');
				jQuery('.localMoveContent').addClass('hide');
				jQuery('#effective_tariff_custom_type').val('').trigger('change');
				jQuery('#tariff_customjs').val('').trigger('change');
				return;
			}
			var tariffData = thisInstance.effectiveTariffData[newTariff];
			jQuery('#effective_tariff_custom_type').val(tariffData['custom_tariff_type']).trigger('change');
			jQuery('#tariff_customjs').val(tariffData['custom_js']).trigger('change');

			if(tariffData['is_managed_tariff'])
			{
				jQuery('#isLocalRating').val(0);
				var show = '.interstateContent:not(.inactive,.inactiveBlock)';
				if(typeof thisInstance.customerJs.getShowQuery != 'undefined')
				{
					var show = thisInstance.customerJs.getShowQuery(show, true, newTariff, tariffData);
				}
				var hide = '.localMoveContent:not(.interstateContent)';
				// TODO: refactor the show/hide to the proper place
				var loadBlocks = [];
				var checkBlocks = jQuery('.sectionContentHolder.inactive.interstateContent');
				checkBlocks.each(function() {
					// contentHolder_
					loadBlocks.push(jQuery(this).attr('id').substr(14));
				});
				thisInstance.customerJs.getBlocksToLoad(loadBlocks, prevTariff, newTariff, tariffData);

				jQuery(hide).addClass('hide inactive').html('');
				if(loadBlocks.length > 0)
				{
					var progressElement = jQuery.progressIndicator({
						'position' : 'html',
						'blockInfo' : {
							'enabled' : true
						}
					});
					var url = 'index.php?action=ReloadContents&_controllerBlockList='+loadBlocks.join(',')+'&effective_tariff_id=' + newTariff + '&module=' + thisInstance.moduleName + '&view=Edit&record=' + jQuery('[name="record"]').val();
					url = thisInstance.addRelationURLParams(url);
					AppConnector.request(url).then(function(data) {
						Vtiger_Edit_Js.I().loadContentData(data);
						Estimates_Edit_Js.I().customerJs.postReloadContents(true, prevTariff, newTariff, tariffData);
						thisInstance.updateTabIndexValues();
						jQuery(show).removeClass('hide');
						progressElement.progressIndicator({'mode': 'hide'});
						thisInstance.doAfterTariffLoad();
					})
				} else {
					thisInstance.doAfterTariffLoad();
				}
				jQuery(show).removeClass('hide');
			}
			else
			{
				thisInstance.loadLocalMoveContents();
			}
			thisInstance.afterTariffLoad(function () {
			    thisInstance.updateEffectiveDateNew();
			});  	
		});
	},

	registerEffectiveDateChangedEvent : function () {
		var thisInstance = this;
		jQuery('.contentsDiv').on('value_change', '[name="effective_date"]', function ()
		{
			var newTariff = jQuery('[name="effective_tariff"]').val();
			if(!newTariff)
			{
				return;
			}
			var tariffData = thisInstance.effectiveTariffData[newTariff];
			if(!tariffData['is_managed_tariff'])
			{
				// need to reload local tariff contents
				thisInstance.loadLocalMoveContents();
			}
		});
	},

	registerTransitGuideButtonEvent: function()
	{
		var thisInstance = this;
		jQuery('.contentsDiv').on('click', 'button.transitGuide', function(){
			var progressElement = jQuery.progressIndicator({
				'position' : 'html',
				'blockInfo' : {
					'enabled' : true
				}
			});

			Estimates_BaseSIRVA_Js.I().getTransitGuide(
				function(data) {
					progressElement.progressIndicator({'mode': 'hide'});
					if(data.success) {
						var message = 'Pick a Transit Guide date set:';
						Estimates_BaseSIRVA_Js.I().showTransitGuideBox({'message': message, 'results': data.result}).then(
							function (e) {
								console.dir(e);
								//console.dir('updated');
								//chose to have setTG return the user formatted dates. instead of doing a page reload
								var elements = ['deliver_date', 'deliver_to_date', 'load_date', 'load_to_date'];
								for (var elm in elements) {
									jQuery('input[name="' + elements[elm] + '"]').val(e[elements[elm]]);
								}
							},
							function (error, err) {
								console.dir('error 2');
							}
						);
					} else {
						console.dir('error 3');
						bootbox.alert("Error retrieving transit guide: " + data.error.message);
					}
				},
				function(error) {
					progressElement.progressIndicator({'mode': 'hide'});
					bootbox.alert(error);
				}
			);
		});
	},

    //locks fields down if the estimate is primary and stage is accepted to remove lock from fields add name to skiplock
    lockPrimaryEstimate: function() {
        var lock = jQuery('.editViewContainer').data('lockfields');
        //Add inputs you don't want locked to array below(by name)
        var skipLock = [];
        if(lock) {
            setTimeout(function(){ // had to add this so that all the fields would be loaded into the dom before this runs
                //console.log('Locking Fields Edit.js 3622');
                //loop through all inputs and lock fields
                jQuery('.editViewContainer *').filter(':input').each(function() {
                    if(jQuery.inArray(jQuery(this).prop('name'), skipLock) === -1) {
                        if(jQuery(this).hasClass('chzn-select')) {
                            jQuery(this).prop('disabled', true).trigger('liszt:updated');
                        } else if(jQuery(this).hasClass('dateField')) {
                            jQuery(this).prop("disabled", true);
                        } else if(jQuery(this).prop('type') == 'checkbox') {
                            jQuery(this).prop("disabled", true);
                        } else {
                            jQuery(this).prop('readonly', true);
                        }
                    }
                });
            }, 2000);
        }
        //Remove disabled from inputs so the same data still saves
        jQuery("form").submit(function() {
            jQuery('.editViewContainer *').filter(':input').each(function() {
                jQuery(this).prop("disabled", false);
            });
        });
    },
	registerEffectiveTariffOptionsUpdateEvent: function ()
	{
		var thisInstance = this;
		jQuery('[name="business_line_est"]').on('value_change', function(){
                    if(jQuery(this).val().indexOf('Local') !== -1){
                        thisInstance.showFieldByName('is_multi_day',true);
                    }else{
                        thisInstance.showFieldByName('is_multi_day',false);
                    }
			thisInstance.customerJs.updateEffectiveTariffPicklist();
		});
		jQuery('[name="commodities"]').on('value_change', function(){
			thisInstance.customerJs.updateEffectiveTariffPicklist();
		});
	},

	registerCustomJavascriptForTariff: function () {
		var thisInstance = this;
		var fn = function() {
			thisInstance.customerJs.loadCustomJavascript();
		};
		jQuery('.contentsDiv').on('value_change', '#tariff_customjs', fn);
		fn();
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

	registerGoogleCalculator: function () {
		var thisInstance = this;
		jQuery('.contentsDiv').on('click', '#googleCalculatorButton', function() {
			var addressList = [
				{
					type: 'Origin',
					address: thisInstance.getAddress(jQuery('.contentsDiv'),'origin_address1', 'origin_city', 'origin_state', 'origin_zip'),
					sequence: 0,
				}
			]

			jQuery('.ExtraStopsBlock:not(:hidden)').each(function() {
				var suffix = '_' + jQuery(this).attr('guestid');
				addressList.push({
					type: 'Extra stop',
					address: thisInstance.getAddress(jQuery(this), 'extrastops_address1', 'extrastops_city', 'extrastops_state', 'extrastops_zip', suffix),
					sequence: jQuery('[name="extrastops_sequence'+suffix+'"]', this).val()
				});
			});
			addressList.push(
				{
					type: 'Destination',
					address: thisInstance.getAddress(jQuery('.contentsDiv'),'destination_address1', 'destination_city', 'destination_state', 'destination_zip'),
					sequence: 100,
				}
			);

			var agent = jQuery('[name="agentid"]').val();

			GoogleCalculator_Js.doUpdateFromAddresses(addressList, agent);
		});
	},

	registerAccountChange: function() {
		jQuery('[name="account_id"]').on('value_change', function (){
			var subagrmt = $('[name="contract_display"]');
			if($(this).val() != '') {
				subagrmt.attr('parent',$(this).attr('name'));
			}else{
				subagrmt.removeAttr('parent');
			}
		});
	},

	getReferenceSearchParams : function(element){
		var tdElement = jQuery(element).closest('td');
		var params = {};
		var searchModule = this.getReferencedModuleName(tdElement);
		params.search_module = searchModule;
		if(element.attr('parent')) {
			var parentEle = $('[name="'+element.attr('parent')+'"]');
			params.parent_module = parentEle.siblings('[name="popupReferenceModule"]').val();
			params.parent_id = parentEle.val();
		}
		return params;
	},

	setSubAgreementNumberParent : function() {
		var parentEle = $('[name="account_id"]');
		if(parentEle.val() != '') {
			var childEle = $('[name="contract_display"]');
			childEle.attr('parent','account_id');
		}
	},

    // Yes this is ugly, but I don't have the time to rewrite the horrific experience that is google autofill.
    // So one step at a time.
    quickCreateAutoFill: function() {
        var thisInstance = this;

        if(jQuery('#Estimates_editView_fieldName_origin_zip').length) {
            var autocompleteOriginZip = new google.maps.places.Autocomplete(
                (document.getElementById('Estimates_editView_fieldName_origin_zip')),
                { types: ['geocode'] });

            google.maps.event.addListener(autocompleteOriginZip, 'place_changed', function() {
                thisInstance.fillInZip(jQuery('#Estimates_editView_fieldName_origin_zip'), autocompleteOriginZip);
                jQuery('#Estimates_editView_fieldName_origin_zip').closest('td').find('.formError').remove();
            });
        }
        if(jQuery('#Estimates_editView_fieldName_destination_zip').length) {
            var autocompleteDestinationZip = new google.maps.places.Autocomplete(
                (document.getElementById('Estimates_editView_fieldName_destination_zip')),
                { types: ['geocode'] });

            google.maps.event.addListener(autocompleteDestinationZip, 'place_changed', function() {
                thisInstance.fillInZip(jQuery('#Estimates_editView_fieldName_destination_zip'), autocompleteDestinationZip);
                jQuery('#Estimates_editView_fieldName_destination_zip').closest('td').find('.formError').remove();
            });
        }
    },

    quickCreateUpdateTariff: function(agentEle, form) {
        var getTariffOption = function(tariff, name) {
            var obj = null;
            tariff.find('option').each(function(ele) {
                if($(this).text().indexOf(name) > -1) {
                    obj = $(this);
                    return false;
                }
                return true;
            });

            return obj;
        };

        var tariffEle = form.find('[name="effective_tariff"]');

        var params = {
            'type': 'GET',
            'url': 'index.php',
            'data': {
                'module': 'AgentManager',
                'action': 'GetBrand',
                'agent_vanline_id': agentEle.val()
            }
        };

        AppConnector.request(params).then(function(data) {
            if(data.success) {
                var id = -1;
                if(data.result == 'AVL') {
                    id = getTariffOption(tariffEle, "Allied").val();
                }else {
                    id = getTariffOption(tariffEle, "North American").val();
                }

                if(id != null && id > -1) {
                    Vtiger_Edit_Js.setValue(tariffEle, id);
                }
            }else {
                // shrug emoji here.
            }
        });
    },

    quickCreateTariffControl: function(form) {
        var thisI = this;
        form.find("[name='agentid']").on('change', function() {
            thisI.quickCreateUpdateTariff($(this), form);
        });
        this.quickCreateUpdateTariff(form.find("[name='agentid']"), form);
    },

    fillInZip: function(field, info) {
        var place = info.getPlace();
        for(var i = 0; i < place.address_components.length; i++) {
            var ele = place.address_components[i];
            if(ele.types[0] == 'postal_code') {
                field.val(ele.long_name);
                break;
            }
        }
    },

    initializeLocalValuation: function() {
        this.localValuation = Valuation_Local_Js.I();
        this.localValuation.registerEvents();
    },

    overrideServiceChargeEvent: function() {
        let handler = function() {
            let override = $(this).is(':checked'),
                ele = $('[name="ServiceCharge' + $(this).data('service-id') + '"]');

            Vtiger_Edit_Js.setReadonly(ele, !override);
            if(!override) {
                ele.data('previous-value', ele.val());
                ele.val('');
            }
            else if(ele.data('previous-value')) {
                ele.val(ele.data('previous-value'));
            }
        };
        let overrideEle = $('[name^="ServiceChargeOverride"]');

        overrideEle.on('change', handler);
        handler.call(overrideEle);
    },

    // Register events for quick create.
    registerBasicEvents: function(form, data, isQuickCreate) {
        this._super(form);
        if(isQuickCreate) {
            this.registerWeightChangeEvent(form);
            this.quickCreateAutoFill(form);

            if($('[name="instance"]').val() == 'sirva') {
                this.quickCreateTariffControl(form);
            }

            Vtiger_Edit_Js.setReadonly('move_type', true);
            Vtiger_Edit_Js.setReadonly('shipper_type', true);
            Vtiger_Edit_Js.setReadonly('effective_tariff', true);
        }
    },

	registerEvents: function() {
		var thisInstance = this;
		// always disable the submit button while ajax is pending
		jQuery(document).ajaxStart(function () {
			jQuery('button[type="submit"]').prop('disabled', true);
		});
		jQuery(document).ajaxStop(function () {
			jQuery('button[type="submit"]').prop('disabled', false);
		});

		this.effectiveTariffData = jQuery('#allAvailableTariffs').length ? JSON.parse(jQuery('#allAvailableTariffs').val()) : [];

		this._super();

		this.initializeAddressAutofill(Estimates_Edit_Js.I().moduleName);
		this.initializeReverseZipAutoFill(Estimates_Edit_Js.I().moduleName);

        this.registerReportsButton();
        this.registerInterstateRateEstimate();

        this.initializeLocalValuation();

		try {
			this.ValuationJS = new Valuation_Common_Js();
			this.ValuationJS.registerEvents(true, this.moduleName);
		} catch(e) {}
		try {
			var common = new Estimates_Common_Js();
			common.registerEvents(true, this.moduleName);
		} catch(e) {}
		try {
			var vt = new VehicleTransportation_EditBlock_Js();
			vt.registerEvents();
		} catch (e) {}

		try {
			var miscEdit = MiscItems_Edit_Js.I();
			miscEdit.registerEvents();
		} catch(e) {}

		try {
			var contractJs = Estimates_Contract_Js.I();
			contractJs.registerEvents();
		} catch(e) {}

		this.customerJs = Estimates_Customer_Js.I();
		this.customerJs.registerEvents(true);

		this.registerCustomJavascriptForTariff();

		this.lineItemsJs = new LineItems_Js();
		this.lineItemsJs.registerLineItemEvents();
		this.lineItemsJs.registerMoveHQLineItemEvents();

			if(this.moduleName == 'Actuals') {
				Vtiger_Edit_Js.makeFieldMandatory(jQuery('[name="load_date"]'));
                Vtiger_Edit_Js.makeFieldMandatory(jQuery('[name="delivery_date"]'));
			}
		//@TODO: HACK HACK HACK HACK HACK
		if(jQuery('[name="instance"]').val() == 'arpin') {
            jQuery('select[name="pricing_type"]').find('option[value="Non Peak"]').prop('selected', true);
			jQuery('select[name="pricing_type"]').find('option[value="Peak"]').remove();
            jQuery('select[name="pricing_type"]').trigger('liszt:updated');
		}
        if(jQuery('#instance').val() == 'graebel') {
            } else if (jQuery('[name="movehq"]').val()) {
		    this.registerEventsForBusinessLine2();
            this.lockPrimaryEstimate();
		}

		this.registerTariffChangedEvent();
		this.registerEffectiveTariffOptionsUpdateEvent();
		this.registerEffectiveDateChangedEvent();
		this.registerWeightChangeEvent();
        this.updateValuation();
		this.registerTransitGuideButtonEvent();
		// local events
		this.registerLocalCalcWeightLookup();
		this.registerLocalCountyLookup();
		this.registerLocalCWTbyWeightLookup();
		this.registerLocalHourlySetLookup();
		this.registerLocalRateLookup();
		this.registerLocalValuationLookup();
		this.registerLocalWeightMileLookup();
		this.registerHiddenByValuation();
		this.registerLocalTabledValuationLookup();
		//
		this.registerShuttleMinMilesValidation();
		this.registerAddressLookupComponentForms();
        // On account change, link account to subagrmt
        this.registerAccountChange();

		// google address miles/time lookup
		this.registerGoogleCalculator();

		this.updateTabIndexValues();
		//progressElement.progressIndicator({'mode': 'hide'});
		var show = '.sectionContentHolder';
		if(typeof this.customerJs.getShowQuery != 'undefined')
		{
			var newTariff = jQuery('[name="effective_tariff"]').val();
			var tariffData = this.effectiveTariffData[newTariff];
			var managed = false;
			if(typeof tariffData != 'undefined')
			{
				managed = tariffData['is_managed_tariff'];
			}
			var show = this.customerJs.getShowQuery(show, managed, newTariff, tariffData);
		}
		jQuery(show).not('.inactiveBlock').removeClass('hide');

		this.customerJs.updateEffectiveTariffPicklist();

		this.setSubAgreementNumberParent();
                if(jQuery('[name="business_line_est"').val().indexOf('Local') === -1){
                    this.showFieldByName('is_multi_day',false);
                }
                this.registerPriceTypeChange();
                this.registerRemoveSelectAnOptionOption();
		this.registerAfterLoadTariffFunction();
		this.registerLoadDateChange();
		this.overrideServiceChargeEvent();
	},
	
	registerAfterLoadTariffFunction: function(){
	    var thisInstance = this;
	    
	    thisInstance.afterTariffLoad(function () {
		thisInstance.updateEffectiveDateNew();
	    });  
	},

	updateEffectiveDateNew: function(){
        // Just going to do this, because idk why this is a thing, but it sure isn't working.
        if($('[name="instance"]').val() == 'sirva') {
            return;
        }

	    jQuery('input[name*=effective_date]:visible').val(jQuery('[name="load_date"]').val());
	    var id = jQuery('input[name*=effective_date]:visible').prop("id");
	    var dateToSet = new Date(jQuery('[name="load_date"]').val());
		jQuery('#'+id).DatePickerSetDate(dateToSet, true);
		app.registerEventForDatePickerFields();
	},

	registerLoadDateChange: function(){
	    var thisInstance = this;
	    jQuery(document).on('change','[name="load_date"]',function(){
		thisInstance.updateEffectiveDateNew();
	    });
	},

        showFieldByName: function(field,show){
            var fieldTR = jQuery('[name="' + field + '"]').closest('tr');
            var fieldSpan = jQuery('[name="' + field + '"]').closest('span');
            var fieldLabel = jQuery('[name="' + field + '"]').closest('td').prev('td').find('label');
            if(show){
                fieldSpan.removeClass('hide');
                fieldLabel.removeClass('hide');
                if(fieldTR.find('td.fieldValue').find('span.hide').length !== 2){
                    fieldTR.removeClass('hide');
                }
            }else{
                fieldSpan.addClass('hide');
                fieldLabel.addClass('hide');
                if(fieldTR.find('td.fieldValue').find('span.hide').length === 2){
                    fieldTR.addClass('hide');
                }
            }
        },
        
        registerPriceTypeChange: function(){
            if($('[name="instance"]').val() == 'sirva') {
                return;
            }

            var thisInstance = this;
            var fieldsToHide = ['validtill','quotestage','is_primary'];
            if(jQuery('select[name="pricing_mode"]').val() !== 'Estimate' && jQuery('select[name="pricing_mode"]').val() !== ""){
                $.each(fieldsToHide,function( index, value ){
                    thisInstance.showFieldByName(value,false);
                });
            }
            jQuery('select[name="pricing_mode"]').on('change', function() {
                var show = ( jQuery(this).val() === 'Estimate' ? true : false );
                $.each(fieldsToHide,function( index, value ){
                    thisInstance.showFieldByName(value,show);
                });
            });
        },
        
        registerRemoveSelectAnOptionOption: function(){
            $.each(jQuery('select[name="pricing_mode"] option'),function(){
                if(jQuery(this).html().trim() === "Select an Option"){
                    jQuery(this).remove();
                }
            });
            jQuery('select[name="pricing_mode"]').trigger('liszt:updated');
        },
        
	getPopUpParams: function (container, e) {
		var params = this._super(container);
		var sourceFieldElement = jQuery('input[class="sourceField"]', container);

		if (sourceFieldElement.attr('name') == 'contract') {
			var parentIdElement = jQuery('[name="account_id"]');
			if (parentIdElement.length > 0 && parentIdElement.val().length > 0 && parentIdElement.val() != 0) {
				params['accountId'] = parentIdElement.val();

			}else{
				var params = {
					title: app.vtranslate('JS_ERROR'),
					text: app.vtranslate('Account can not be null. Please choose an Account first'),
					animation: 'show',
					type: 'error'
				};
				Vtiger_Helper_Js.showPnotify(params);
				return false;
			}
			//below shouldn't be necessary currently as Contract field is only unhidden when business_line is set but might as well keep the check just in case.
			if(jQuery('[name="business_line"]').val() != ''){
				params['businessLine'] = jQuery('[name="business_line_est"]').val();
			}else{
				var params = {
					title: app.vtranslate('JS_ERROR'),
					text: app.vtranslate('Business Line can not be null. Please choose a business line first'),
					animation: 'show',
					type: 'error'
				};
				Vtiger_Helper_Js.showPnotify(params);
				return false;
			}
		}
		return params;
	},

	registerEventsForBusinessLine2: function () {
		var thisInstance = this;
		var editViewForm = this.getForm();
		// Hide Business Line EST
		if(jQuery('select[name="business_line_est2"]').length == 0)
		{
			return;
		}

		editViewForm.find('select[name="business_line_est"]').closest('td').children().addClass('hide');
		editViewForm.find('select[name="business_line_est"]').closest('td').prev('td').children().addClass('hide');
		// register change event for Business Line est 2
		jQuery(document).on("change",'select[name="business_line_est2"]', function () {
			console.log('change handler for business_line_est2');
			var selectedVal=jQuery(this).val();
			var business_line_val = thisInstance.businessLineMapping[selectedVal];
			jQuery('select[name="business_line_est"]').find('option[value="'+business_line_val+'"]').prop('selected', true);
			editViewForm.find('select[name="business_line_est"]').trigger("liszt:updated");
			editViewForm.find('select[name="business_line_est"]').trigger("change");
		});
	},

});

function triggerDuplicate(){
    var checkbox = jQuery('.listViewEntriesTable input[type="checkbox"]:checked');
    if(checkbox.length !== 1){
        var params = {
            title: app.vtranslate('JS_MESSAGE'),
            text: app.vtranslate('Select one record to duplicate'),
            animation: 'show',
            type: 'info'
        };
        Vtiger_Helper_Js.showPnotify(params);
        return false;
    }else{
        window.location.href='index.php?module=Estimates&view=Edit&record=' + checkbox.val() + '&isDuplicate=true';
    }
}

$(document).ready(function(){
    // $('.ieBtn').bind('click',function(){
    //     $('#EditView').submit();
    // });
	$("form").each(function ()
	{
		var v = $(this).data("validator");
		if(typeof v != 'undefined') {
			v.settings.success = false;
		}
	})
});
