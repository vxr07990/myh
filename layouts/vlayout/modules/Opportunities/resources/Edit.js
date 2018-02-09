/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

Vtiger_Edit_Js("Opportunities_Edit_Js", {
	getInstance : function() {
		return new Opportunities_Edit_Js();
    },
    I: function() {
        return this.getInstance();
    },
    className: "Opportunities_Edit_Js"
}, {
    moveType: null,
    militaryFields: null,

	defaultLeadSourceOptions : jQuery('select[name="leadsource"]').html(),
    leadsourceWorkspaceOptions  : jQuery('select[name="leadsource_workspace"]').html(),
    leadsourceNationalOptions  : jQuery('select[name="leadsource_national"]').html(),
    daysToMove: null,
    salesperson: null,
    oppType: null,
    sts: null,

	currentSalesPerson : '',
    currentOwner : '',
	registerFormSubmitEvent: function () {
		var handlers = jQuery('#EditView').data('events');
		var editViewForm = jQuery('#EditView');
		editViewForm.submit(function (e) {
			var error = false;

			jQuery('input[real-name], select[real-name]').each(function () {
				var name = jQuery(this).attr('real-name');
				jQuery(this).attr('real-name', jQuery(this).attr('name'));
				jQuery(this).attr('name', name);
			});

			jQuery('.validate').each(function () {
				if (jQuery(this).val() == '') {

					jQuery(this).closest('td').css('background-color', 'red');
					e.preventDefault();
					alert('You are missing required fields in the Participating Agents section!');
					error = true;
					return false;
				}
			});

			jQuery('.sourceField[real-name]').each(function () {
				if (!jQuery(this).hasClass('default') && (jQuery(this).val() == '0' || jQuery(this).val() == '') && !error) {
					jQuery(this).closest('td').css('background-color', 'red');
					e.preventDefault();
					alert('You have not set one or more Participating Agents!');
					error = true;
					return false;
				}
			});
			if (error) {
				$('html,body').animate({
					scrollTop: jQuery("table[name='participatingAgentsTable']").offset().top - 100
				});
				editViewForm.removeData('submit');
			}
			//var isValid = jQuery('#EditView').validationEngine('validate');
			//error = !isValid;
			//alert(isValid);
			if (error) {
				jQuery('input[real-name], select[real-name]').each(function () {
					var name = jQuery(this).attr('name');
					jQuery(this).attr('name', jQuery(this).attr('real-name'));
					jQuery(this).attr('real-name', name);
				});
			}

			return !error;
		});
	},
	registerRemoveParticipantButton: function () {
		var statuses = ['Pending', 'Accepted', 'Removed'];
		jQuery('html').on('click', '.removeParticipant', function () {
			if (jQuery(this).parent().parent().hasClass('newParticipant')) {
				jQuery(this).parent().parent().remove();
				return;
			}
			if (jQuery(this).hasClass('icon-trash')) {
				jQuery(this).removeClass('icon-trash').addClass('icon-repeat');
				jQuery(this).parent().parent().find('.status').val(2);
				jQuery(this).parent().parent().find('.status-label').html(statuses[2]);
				jQuery(this).parent().parent().find('.status-label').addClass('redColor');
            } else {
				var statusInput = jQuery(this).parent().parent().find('.status');
				jQuery(this).removeClass('icon-repeat').addClass('icon-trash');
				statusInput.val(statusInput.attr('default'));
				jQuery(this).parent().parent().find('.status-label').html(statuses[parseInt(statusInput.attr('default'))]);
				jQuery(this).parent().parent().find('.status-label').removeClass('redColor');
			}
		});
	},

	toggleNationalAccountBlock: function() {
		var shipperType = jQuery('select[name="shipper_type"]').val();
		if(shipperType == 'NAT') {
			jQuery('table[name="LBL_POTENTIALS_NATIONALACCOUNT"]').removeClass('hide');
		}
		jQuery('select[name="shipper_type"]').on('change', function() {
			if(jQuery(this).val() == 'NAT') {
				if(jQuery('table[name="LBL_POTENTIALS_NATIONALACCOUNT"]').hasClass('hide')){
					jQuery('table[name="LBL_POTENTIALS_NATIONALACCOUNT"]').removeClass('hide');
				}
            } else {
				if(!jQuery('table[name="LBL_POTENTIALS_NATIONALACCOUNT"]').hasClass('hide')){
					jQuery('table[name="LBL_POTENTIALS_NATIONALACCOUNT"]').addClass('hide');
				}
			}
		});
	},

	registerChangeOppType : function() {
        // Lead type do this now
		jQuery('select[name="lead_type"]').on('change', function() {
			if(jQuery('select[name="lead_type"]').val() == 'National Account') {
				if(jQuery('table[name="LBL_POTENTIALS_NATIONALACCOUNT"]').hasClass('hide')) {
					jQuery('table[name="LBL_POTENTIALS_NATIONALACCOUNT"]').removeClass('hide');
				}
			} else {
				if(!jQuery('table[name="LBL_POTENTIALS_NATIONALACCOUNT"]').hasClass('hide')) {
					jQuery('table[name="LBL_POTENTIALS_NATIONALACCOUNT"]').addClass('hide');
				}
			}
		});
	},

    //Sirva requires that there are either a load from AND load to date, or nieghter. Can't be one without the other.
    bindLoadFromToDate: function() {
        var load_from = jQuery('#Opportunities_editView_fieldName_load_date');
        var load_to = jQuery('#Opportunities_editView_fieldName_load_to_date');
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

    setLeadTypeToReadonly: function() {
        // This entire function is here so that in the case that more logic is needed, it can be done.
		// ..wat
		if(jQuery('[name="lead_type"]').val() == '') {
			$('[name="lead_type"]').val('Consumer').trigger('liszt:updated');
		}
		// also they don't want this either
        //Vtiger_Edit_Js.setReadonly('lead_type', true);
    },

	deleteStopEvent : function(){
		jQuery('.deleteStopButton').on('click', function(){
			var bodyContainer = jQuery(this).closest('tbody');
			var stopId = jQuery(this).closest('tbody').find('input:hidden[name^="stop_id_"]').val();
			//console.dir(stopId);
			if(stopId && stopId !='none'){
				var url = "index.php?module=Opportunities&action=DeleteStop&record="+getQueryVariable('record')+"&stopid="+stopId;
				AppConnector.request(url).then(
					function(data) {
						if(data.success) {
							//console.dir('success');
						}
					},
					function(error) {
						//console.dir('error');
					}
				);
			}
			bodyContainer.remove();
		});
	},

	registerPhoneTypeEvents : function() {
		jQuery('select[name="origin_phone1_type"]').on('change', function() {
			var selectedOption = jQuery('select[name="origin_phone1_type"]').val();
			if(selectedOption == 'Work'){
				if(jQuery('#originPhone1Span').hasClass('hide')){
					jQuery('#originPhone1Span').removeClass('hide');
				}
            } else {
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
            } else {
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
            } else {
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
            } else {
				if(!jQuery('#destinationPhone2Span').hasClass('hide')){
					jQuery('#destinationPhone2Span').addClass('hide');
					jQuery('input[name="destination_phone2_ext"]').val('');
				}
			}
		});
	},

	populateAccountDetails : function() {
		var contactId = jQuery('input[name="sourceRecord"]').val();
		if(contactId>0) {
			var dataUrl = "index.php?module=Opportunities&action=PopulateAccountDetails&source="+contactId;
			AppConnector.request(dataUrl).then(
				function(data) {
					if(data.success) {
						var user = data.result.entity.column_fields;
						console.log(user.bill_street);
						jQuery('input[name="street"]').html(user.bill_street);
						jQuery('input[name="pobox"]').val(user.bill_pobox);
						jQuery('input[name="city"]').val(user.bill_city);
						jQuery('input[name="state"]').val(user.bill_state);
						jQuery('input[name="zip"]').val(user.bill_code);
						jQuery('input[name="country"]').val(user.bill_country);
					}
				}
			);
		}
	},

	registerChangeContact : function(){
		thisInstance = this;
		//code to handle populating contact info during a relation operation
		if(jQuery('input:hidden[name="relationOperation"]').val() == 'true' && jQuery('input:hidden[name="sourceModule"]').val() == 'Contacts'){
			if(jQuery('input:hidden[name="sourceRecord"]').val()){
				contactIsTransferee(jQuery('input:hidden[name="sourceRecord"]').val(), false);
			}
		}
		//code to handle populating contact info when selecting a new contact
		hiddenElement = jQuery('input:hidden[name="contact_id"]');
		hiddenElement.on(Vtiger_Edit_Js.referenceSelectionEvent, function(event){
			contactIsTransferee(hiddenElement.val(), true);
		});
		hiddenElement.on('populateContactAuto', function(){
			//auto populate event for relation operations
			//console.dir('populateContactAuto');
			populateContactData(jQuery('input:hidden[name="sourceRecord"]').val());
		});
		hiddenElement.on('populateContactPopUp', function(){
			//event to ask the user if they wish to popuate data
			hiddenElement = jQuery('input:hidden[name="contact_id"]');
			contactId = hiddenElement.val();
			var message = 'Would you like to load data from the Contact?';
            Vtiger_Helper_Js.showConfirmationBox({
                'message': message
            }).then(
				function(e){
					populateContactData(contactId);
				},
				function(error, err) {
					//they pressed no don't populate the data.
				}
			);
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

	validDate : function(){
        var dateChecks = [{
                on: 'pack_date',
                from: 'pack_date',
                to: 'pack_to_date',
				msg: 'The "Pack From Date" should not be before the "Pack to Date"'
			},
			{
                on: 'pack_to_date',
                from: 'pack_date',
                to: 'pack_to_date',
				msg: 'The "Pack From Date" should not be before the "Pack to Date"'
			},
			{
                on: 'load_date',
                from: 'load_date',
                to: 'load_to_date',
				msg: 'The "Load From Date" should not be before the "Load to Date"'
			},
			{
                on: 'load_to_date',
                from: 'load_date',
                to: 'load_to_date',
				msg: 'The "Load From Date" should not be before the "Load to Date"'
			},
			{
                on: 'deliver_date',
                from: 'deliver_date',
                to: 'deliver_to_date',
				msg: 'The "Deliver From Date" should not be before the "Deliver to Date"'
			},
			{
                on: 'deliver_to_date',
                from: 'deliver_date',
                to: 'deliver_to_date',
				msg: 'The "Deliver From Date" should not be before the "Deliver to Date"'
			},
		];
		$.each(dateChecks, function(key, date){
            $('[name="' + date.on + '"]').on('change', function() {
				var domFrom = $('[name="'+date.from+'"]');
                var domTo = $('[name="' + date.to + '"]');
                var daysToMove = jQuery('input[name="days_to_move"]');
				var from = new Date(domFrom.val());
                var to = new Date(domTo.val());
				if(from>to){
					domFrom.val('');
					domTo.val('');
                    daysToMove.val('');
					bootbox.alert(date.msg);
				}
			});
		});
	},

	updateTabIndexValues: function() {
		//console.dir('Updating tabindex for all fields');
		var tabindex = 1;
		jQuery('table').each(function() {
			if(jQuery(this).attr('name') == 'LBL_POTENTIALS_ADDRESSDETAILS'){
				var row = 1;
				jQuery(this).find('input,select, textarea').each(function() {
					if (this.type != "hidden" && !jQuery(this).closest('span').hasClass('hide')) {
						if(row == 1){
							var $input = jQuery(this);
							$input.attr("tabindex", tabindex);
							tabindex++;
							row = 2;
                        } else {
							row = 1;
						}
					}
				});
				var row = 1;
				jQuery(this).find('input,select, textarea').each(function() {
					if (this.type != "hidden" && !jQuery(this).closest('span').hasClass('hide')) {
						if(row == 2){
							var $input = jQuery(this);
							$input.attr("tabindex", tabindex);
							tabindex++;
							row = 1;
                        } else {
							row = 2;
						}
					}
				});
			}
		});
	},

	//function to change sales_stage or opportunity_disposition based if either is updated.
	registerChangeSalesStage : function() {
		var thisInstance = this;

		jQuery('select[name="opportunity_disposition"]').on('change', function(){
			var opportunityDisposition = jQuery('select[name="opportunity_disposition"]').find('option:selected').text();
			jQuery('select[name="sales_stage"]').find('option:selected').prop('selected', false).removeAttr('selected');
			jQuery('select[name="sales_stage"]').find('option').each(function(index, element){
				//console.dir(opportunityDisposition);
				if (element.text == opportunityDisposition) {
					jQuery('select[name="sales_stage"]').find('option[value="'+element.value+'"]').prop('selected', true).attr('selected', 'selected');
					return false;
				}
			});
			jQuery('select[name="sales_stage"]').trigger('liszt:updated');
			if (opportunityDisposition == 'Lost') {
				thisInstance.showDispositionLost();
			} else {
				thisInstance.hideDispositionLost();
			}
		});

		jQuery('select[name="sales_stage"]').on('change', function(){
			var salesStage = jQuery('select[name="sales_stage"]').find('option:selected').text();
			jQuery('select[name="opportunity_disposition"]').find('option:selected').prop('selected', false).removeAttr('selected');
			jQuery('select[name="opportunity_disposition"]').find('option[value="'+salesStage+'"]').prop('selected', true).attr('selected', 'selected');
			jQuery('select[name="opportunity_disposition"]').trigger('liszt:updated');
			if (salesStage == 'Lost') {
				thisInstance.showDispositionLost();
			} else {
				thisInstance.hideDispositionLost();
			}
		});
	},

	registerChangeDispositionLost : function() {
		var thisInstance = this;
		jQuery('select[name="disposition_lost_reasons"]').on('change', function(){
			var opportunityDisposition = jQuery('select[name="disposition_lost_reasons"]').find('option:selected').text();
			if (opportunityDisposition == 'Other') {
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

	hideDispositionLost : function() {
		if(jQuery('.dispLostFiller').hasClass('hide')){
			jQuery('.dispLostFiller').removeClass('hide');
		}
		var disLostReasons = jQuery('select[name="disposition_lost_reasons"]');
		if (!disLostReasons.closest('td').find('div').hasClass('hide')){
			disLostReasons.closest('td').prev('td').find('label').addClass('hide');
            disLostReasons.find('option:selected').prop('selected', false).trigger('liszt:updated');
			disLostReasons.siblings('.chzn-container').find('.chzn-results').trigger('change');
			disLostReasons.closest('td').find('div').addClass('hide');
		}
		this.hideDispositionLostOther();
	},

	showDispositionLost : function() {
		//console.dir('it was lost, its dead Jim');
		if(jQuery('.dispLostFiller').hasClass('hide')){
			jQuery('.dispLostFiller').removeClass('hide');
		}
		var disLostReasons = jQuery('select[name="disposition_lost_reasons"]');
		if (disLostReasons.closest('td').find('div').hasClass('hide')){
			disLostReasons.closest('td').prev('td').find('label').removeClass('hide');
			disLostReasons.closest('td').find('div').removeClass('hide');
		}
	},

	setDefaultInputs: function() {
		if(jQuery('select[name="preferred_language"]').val() == '') {
			jQuery('select[name="preferred_language"]').val('English').trigger('liszt:updated');
		}
	},

	setParticipatingBookingAgent: function() {
	    //I think this is obsolete but I'm not certain it's not used by Sirva so...
        if (jQuery('[name="movehq"]').val()){
            return;
        }
		jQuery('table[name="participatingAgentsTable"]').on('change', 'select[name^="agent_type"]', function() {
			if(jQuery(this).val() == 0) {
				var userAgencyName = jQuery('#userAgencyName').val();
				var userAgencyId = jQuery('#userAgencyId').val();
				if(typeof userAgencyId == 'undefined') {
					var dataUrl = 'index.php?module=Opportunities&action=GetUsersAgentSettings';
					AppConnector.request(dataUrl).then(
						function(data) {
							if (data.success) {
								userAgencyId = data.result.agency_id;
								userAgencyName = data.result.agency_name;
								jQuery('input[name="opp_participants['+fieldNum+']"]').val(userAgencyId);
								jQuery('input[name="opp_participants'+fieldNum+'_display"]').val(userAgencyName);

								jQuery('#EditView').prepend('<input type="hidden" id="userAgencyName" value="' + userAgencyName + '">');
								jQuery('#EditView').prepend('<input type="hidden" id="userAgencyId" value="' + userAgencyId + '">');
                                userAgencyName = jQuery('#userAgencyName').val();
                                userAgencyId = jQuery('#userAgencyId').val();
							}
						}
					);
				}

				userAgencyName = jQuery('#userAgencyName').val();
				userAgencyId = jQuery('#userAgencyId').val();

                var fieldNum = jQuery(this).attr('name').replace(/\D/g, '');

				jQuery('input[name="opp_participants['+fieldNum+']"]').val(userAgencyId);
				jQuery('input[name="opp_participants'+fieldNum+'_display"]').val(userAgencyName);
			}
			jQuery('select[name="brand"]').trigger('change');
		});
	},


	registerChangeSalesPerson : function() {
		var thisInstance = this;
		thisInstance.currentSalesPerson = jQuery('select[name="sales_person"]').find('option:selected').val();
		jQuery('select[name="sales_person"]').on('change', function() {
			if(jQuery('select[name="sales_person"]').find('option:selected').val() != thisInstance.currentSalesPerson) {
				jQuery('input[name="sent_to_mobile"]').prop("checked", false);
				thisInstance.currentSalesPerson = jQuery('select[name="sales_person"]').find('option:selected').val();

                var agentid = jQuery('select[name="agentid"]').val();
                thisInstance.getCoordinators(agentid, thisInstance.currentSalesPerson);
			}
		});
	},

	registerTransitGuideEvent : function() {
		var instance = this;
		jQuery('button[name="transitGuide"]').on('click', function () {
			var load_date = jQuery('input[name="load_date"]').val();
			var origin_zip = jQuery('input[name="origin_zip"]').val();
			var destination_zip = jQuery('input[name="destination_zip"]').val();
			var business_line = jQuery('input[name="business_line"]').val();
			var origin_country = jQuery('input[name="origin_country"]').val();
			var destination_country = jQuery('input[name="destination_country"]').val();
			var record = jQuery('input[name="record"]').val();
			var extra_stops_origin = [];
			var extra_stops_destination = [];

			var numStops = jQuery('#numStops').val();
			for (var i = 1; i <= numStops; i++) {
				//var extraStop = jQuery('#extrastops_id_'+ i).closest('tbody');
				var sequence = jQuery('input[name="extrastops_sequence_' + i + '"]').val();
				var zip = jQuery('input[name="extrastops_zip_' + i + '"]').val();
				var type = jQuery('select[name="extrastops_type_' + i + '"]').val();

				if (zip !== 'undefined') {
					if (sequence === 'undefined') {
                        sequence = i + numStops; //just set it to the "end"
					}
					if (type == 'Origin' || type == 'Extra Pickup') {
						extra_stops_origin[sequence] = zip;
					} else if (type == 'Destination' || type == 'Extra Delivery') {
						extra_stops_destination[sequence] = zip;
					}
				}
			}

			/*
			 //@TODO: possibly let the user select the load date on the popup?
			 if(!load_date) {
			 var message = app.vtranslate("JS_PLEASE_SET_LOAD_DATE");
			 bootbox.alert(message);
			 return;
			 }
			 */

			//it's one or the other who knows?  maybe google.
			if (typeof load_date === 'Undefined' || typeof load_date === 'undefined') {
				load_date = '';
			}

			//leaving it open to have the load_date entered from the popup.
			var url = 'index.php?module=Opportunities&action=GetTransitGuide'
				+ '&load_date=' + load_date
				+ '&origin_zip=' + origin_zip
				+ '&destination_zip=' + destination_zip
				+ '&origin_country=' + origin_country
				+ '&destination_country=' + destination_country
				+ '&business_line=' + business_line
				+ '&record=' + record
				+ '&extra_stops_origin=' + extra_stops_origin.toString()
				+ '&extra_stops_destination=' + extra_stops_destination.toString()
				+ '&edit=1';

			AppConnector.request(url).then(
				function(data) {
					if(data.success) {
						var message = 'Pick a Transit Guide date set:';
                        instance.showTransitGuideBox({
                            'message': message,
                            'results': data.result
                        }).then(
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
					console.dir('error 4');
				}
			);
		});
	},

	/*
	 * Function to show the transit date picker messagebox
	 */
	showTransitGuideBox : function(data) {
		var aDeferred = jQuery.Deferred();
		var standard = false;
		var optional = false;

		var button = {};

		for (var desc in data.results) {
			//@TODO: this should be removed...
			if (desc == 'standard') {
				standard = true;
			}
			if (desc == 'optional') {
				optional = true;
			}
			//
			//	//@TODO: Figure out how to make this set a callback to use with the correct results.
			//	//@TODO: Because variables are by ref then it's always the "last" one.
			//	if (desc.length > 0) {
			//		button[desc] = {
			//			'label': app.vtranslate(desc),
			//			//'className' : "btn-tg-" + desc.toLowerCase(),
			//			'className': "btn-danger",
			//			callback: function () {
			//				aDeferred.resolve(data.results[desc]);
			//			}
			//			/*
			//			 // this processes the function when it sees it instead of setting(?) the function.
			//			 callback : (function(aDef, val) {
			//			 aDef.resolve(val);
			//			 })(aDeferred, data.results[desc])
			//			 */
			//		}
			//	}
		}

		//@TODO: not do this... seriously... make that loop work for the love of god.
		if (standard) {
			button['Standard'] = {
				'label': app.vtranslate('Standard'),
				'className': "btn-danger",
				callback: function () {
					aDeferred.resolve(data.results['standard']);
				}
			}
		}
		if (optional) {
			button['Optional'] = {
				'label': app.vtranslate('Optional'),
				'className': "btn-danger",
				callback: function () {
					aDeferred.resolve(data.results['optional']);
				}
			}
		}

		var bootBoxModal = bootbox.dialog({
			message: data['message'],
			buttons: button
		});

		bootBoxModal.on('hidden', function (e) {
			//In Case of multiple modal. like mass edit and quick create, if bootbox is shown and hidden , it will remove
			// modal open
			if (jQuery('#globalmodal').length > 0) {
				// Mimic bootstrap modal action body state change
				jQuery('body').addClass('modal-open');
			}
		});
		return aDeferred.promise();
	},

    registerBillingTypeField: function() {
        var thisInstance = this;
        jQuery('select[name="billing_type"]').change(function() {
            if(jQuery('input:hidden[name="instance"]').val() == 'graebel') {
				participantInstance = ParticipatingAgents_Edit_Js.getInstance();
                var carrierRow = participantInstance.findParticipantRow('Carrier');
                businessLineValue = jQuery('select[name="business_line"]').find('option:selected').html();
                if (carrierRow && carrierRow.attr('data-state') == 'auto-set' && jQuery('input:hidden[name="instance"]').val() == 'graebel'){
                    participantInstance.setDefaultCarrier(businessLineValue);
                }
			}
		});
    },

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
                console.log('Show National Options');
                //use the setPicklistOptions function to set these, this perserves the existing selection if possible.
                Vtiger_Edit_Js.setPicklistOptions(jQuery('select[name="leadsource"]'), thisInstance.leadsourceNationalOptions);
            } else if (thisInstance.leadsourceWorkspaceOptions && businessLine.indexOf('Work') > -1) {
                console.log('Show Workspace Options');
                Vtiger_Edit_Js.setPicklistOptions(jQuery('select[name="leadsource"]'), thisInstance.leadsourceWorkspaceOptions);
            } else if (thisInstance.leadsourceHHGOptions && businessLine.indexOf('HHG') > -1) {
                console.log('Show HHG Options');
                Vtiger_Edit_Js.setPicklistOptions(jQuery('select[name="leadsource"]'), thisInstance.leadsourceHHGOptions);
            }
		} else {
                console.log('Show Default Options');
                Vtiger_Edit_Js.setPicklistOptions(jQuery('select[name="leadsource"]'), thisInstance.defaultLeadSourceOptions);
		}
        participantInstance = ParticipatingAgents_Edit_Js.getInstance();
        var carrierRow = participantInstance.findParticipantRow('Carrier');
        if (carrierRow && carrierRow.attr('data-state') == 'auto-set' && jQuery('input:hidden[name="instance"]').val() == 'graebel') {
            participantInstance.setDefaultCarrier(businessLine);
        }
        if(jQuery('[name="instance"]').val() != 'graebel') {
            var hideAddressBlocks = ['National Account', 'Commercial - Distribution', 'Commercial - Record Storage', 'Commercial - Storage', 'Commercial - Asset Management', 'Work Space - MAC', 'Commercial - Project', 'Work Space - Special Services', 'Work Space - Commodities'];
            if (jQuery.inArray(jQuery('select[name="business_line"]').val(), hideAddressBlocks) !== -1) {
                jQuery('table[name="LBL_LEADS_ADDRESSINFORMATION"]').addClass('hide');
            }
        }
        if(jQuery('[name="instance"]').val() == 'sirva') {
            this.updateBrand();
        }

        // This line needs to go. It is jumping up AJAX requests and causing event chains when the "updateBrand" call
        // above is achieving the same thing.
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
	setReferenceFieldValue : function(container, params) {
		//tfs22818 trigger when related_to field modified.
		var sourceField = container.find('input[class="sourceField"]').attr('name');
		if(sourceField == 'related_to'){
			this.setShipperTypeNat();
		}
		var fieldElement = container.find('input[name="'+sourceField+'"]');
		var sourceFieldDisplay = sourceField+"_display";
		var fieldDisplayElement = container.find('input[name="'+sourceFieldDisplay+'"]');
		var popupReferenceModule = container.find('input[name="popupReferenceModule"]').val();

		var selectedName = params.name;
		var id = params.id;

		fieldElement.val(id);
        fieldDisplayElement.val(selectedName).attr('readonly', true);
        fieldElement.trigger(Vtiger_Edit_Js.referenceSelectionEvent, {
            'source_module': popupReferenceModule,
            'record': id,
            'selectedName': selectedName
        });

        fieldDisplayElement.validationEngine('closePrompt', fieldDisplayElement);
	},

	setShipperTypeNat : function(){
		//tfs22818 when related_to field gets a selection, change shipper_type to NAT
		jQuery('select[name="shipper_type"]').val("NAT").trigger('liszt:updated').trigger('change');

	},

	//OT2969

	setReasonRowVisibility: function() {
		var oppStatus = jQuery('select[name="sales_stage"]').find('option:selected').val();
		if (oppStatus == 'Lost') {
			jQuery('select[name="opportunities_reason"]').closest('tr').children().removeClass('hide');
		} else {
			jQuery('select[name="opportunities_reason"]').closest('tr').children().addClass('hide');
		}
	},

	registerOppStatusChange: function() {
		var thisInstance = this;
		jQuery('select[name="sales_stage"]').change(function() {
			thisInstance.setReasonRowVisibility();
		})
	},


	setVanlineVisibility: function() {
		var oppStatus = jQuery('select[name="opportunities_reason"]').find('option:selected').val();
		if (oppStatus == 'Pricing') {
			jQuery('select[name="opportunities_vanline"]').closest('td').children().removeClass('hide');
			jQuery('select[name="opportunities_vanline"]').closest('td').prev('td').children().removeClass('hide');
		} else {
			jQuery('select[name="opportunities_vanline"]').closest('td').children().addClass('hide');
			jQuery('select[name="opportunities_vanline"]').closest('td').prev('td').children().addClass('hide');
		}
	},

	registerReasonChange: function() {
		var thisInstance = this;
		jQuery('select[name="opportunities_reason"]').change(function() {
			thisInstance.setVanlineVisibility();
		})
	},

	updateBrandEvent : function(){
        if(jQuery('[name="instance"]').val() == 'sirva' && jQuery('select[name="move_type"]').val() != 'Sirva Military'){
            jQuery('select[name="agentid"]').change(this.updateBrand);
            this.updateBrand();
        } else if(jQuery('[name="instance"]').val() == 'sirva') {
            jQuery('select[name="brand"]').attr('disabled', true).trigger('liszt:updated').trigger('change');
        }
	},

    updateBrand: function() {
        var params = {
            module: 'AgentManager',
            action: 'GetBrand',
            agent_vanline_id: jQuery('[name="agentid"]').find('option:selected').val(),
        };
        AppConnector.request(params).then(
            function(data) {
                if (data.success) {
                    jQuery('select[name="brand"]').find('option').attr('disabled', false).closest('select').val(data.result).find('option:not(:selected)').attr('disabled', true).trigger('liszt:updated').trigger('change');
                }
            }
        );
    },

    //@NOTE: Moved to parent because Opportunities_Edit_Js and Leads_Edit_Js used the same function
    //registerSourceNameChange : function() {},

    // Populate load to date if it is currently empty
	registerLoadFromPopulateLoadTo : function() {
		var load_from = jQuery('input[name="load_date"]');
		var load_to = jQuery('input[name="load_to_date"]');
		load_from.on('change', function() {
			if(load_to.val() == '') {
				load_to.val(load_from.val());
			}
		});
	},

    updateHaulingAgent: function(data) {
        var thisI = this;
        if (this.className != Opportunities_Edit_Js.className) {
            thisI = new Opportunities_Edit_Js();
        }
        // References to the DOM.
        var selfHaul = jQuery('input[name="self_haul"]');
        var agentid = jQuery('select[name="agentid"]').val();

        // Hauling agent information.
        var HASelectOptionNumber = 3;
        var HASelectOptionValue = 'Hauling Agent';

        if (data.value) {
            //Check for an existing hauling agent - don't add one if one already exists
            var hasHaulingAgent = false;
            thisI.findParticipatingAgent(HASelectOptionValue, function() {
                hasHaulingAgent = true;
            });
            // Hauling Agent does is not present, add one.
            if (!hasHaulingAgent) {
                return thisI.addHaulingAgent(HASelectOptionValue, HASelectOptionNumber, agentid);
            }
        } else {
            //unset the participating agent with a type of Hauling Agent and a value of agentid
            thisI.findParticipatingAgent(HASelectOptionValue, function() {
                var parentTR = jQuery(this).closest('tr');
                parentTR.find('.removeParticipant').trigger('click');
                jQuery('select[name="brand"]').trigger('change');
            });
        }
	},
    addHaulingAgent: function(HASelectOptionValue, HASelectOptionNumber, agentid) {
        ParticipatingAgents_Edit_Js.getInstance().add();

        //update the type selector to have the Hauling Agent type.
        var numAgents = jQuery("input[name='numAgents']").val();
        var findSelector = jQuery('select[name="agent_type_' + numAgents + '"]');
        var workingDiv = findSelector.siblings().first();
        workingDiv.find('li.result-selected').removeClass('result-selected');
        workingDiv.find('li:contains("' + HASelectOptionValue + '")').addClass('result-selected');
        workingDiv.find('span').html(HASelectOptionValue).attr('readonly', 'readonly');
        findSelector.data('selectedValue', HASelectOptionValue).attr('readonly', 'readonly');
        findSelector.find('option').prop('selected', false).attr('readonly', 'readonly');
        findSelector.val(HASelectOptionValue);
        findSelector.find('option[value="' + HASelectOptionNumber + '"]').prop('selected', true).attr('readonly', 'readonly');

        //update the picklist uitype=10 to have this Agent.
        //ONLY populate if I successfully find the agent to use...
        //otherwise they'll have to click the search and find what they want.
        var dataUrl = "index.php?module=Opportunities&action=PullAgentDetails&source=" + agentid;
        AppConnector.request(dataUrl).then(
            function(data) {
                if (data.success) {
                    var agent = data.result;
                    if (agent.record_id) {
                        //@TODO: this will probably need fixed in the future.
                        var agentLabel = agent.agentname;
                        var container = jQuery('#agents_id_' + numAgents + '_display').closest('td');
                        var obj = {
                            id: agent.record_id,
                            name: agentLabel,
                            suppress: true
                        };
                        thisInstance.setReferenceFieldValue(container, obj);
                    }
                }
            },
            function(error) {
                console.error('Error Getting Agent Details: ' + error);
            });
        //Update the access to Full because that seems proper.
        var findRadios = jQuery("input[name='agent_permission_" + numAgents + "'");
        findRadios.each(function() {
            if (jQuery(this).val().toLowerCase() == 'full') {
                jQuery(this).attr("checked", "checked");
            } else {
                jQuery(this).prop("checked", false);
            }
        });
    },
	registerPostReferenceSelectionEvent : function (container) {
		var thisInstance = this;
		jQuery('input[name="related_to"]', container).on(Vtiger_Edit_Js.postReferenceSelectionEvent, function(e, data) {
			var element=jQuery(e.currentTarget);
			var accountid=element.val();

			var contactElm = jQuery('input[name="oppotunitiescontract"]');
			var closestTD = contactElm.closest('td');
			closestTD.find('.clearReferenceSelection').trigger('click');
			var params = {
                'module': app.getModuleName(),
                'action': 'ActionAjax',
                'mode': 'getRelatedContacts',
                'accountid': accountid
			};
			AppConnector.request(params).then(
				function(data){
					if(data.result.contractid != 'MULTIPLE') {
						jQuery('input[name="oppotunitiescontract"]').val(data.result.contractid);
						jQuery('input[name="oppotunitiescontract_display"]').val(data.result.contract_name);
                        jQuery('input[name="oppotunitiescontract_display"]').attr('readonly', true);
					}
					jQuery('input[name="city"]').val(data.result.cityAccount);
					jQuery('input[name="opportunities_nat_account_no"]').val(data.result.national_account_number);
					jQuery('input[name="zip"]').val(data.result.zip_codeAccount);
					jQuery('input[name="state"]').val(data.result.stateAccount);
					jQuery('input[name="country"]').val(data.result.countryAccount);
					jQuery('textarea[name="street"]').val(data.result.address1Account);
					jQuery('input[name="pobox"]').val(data.result.address2Account);
				},
                function(error) {}
			)
		});
	},
	billingTypeNationalAccountBlock: function () {
		var billingType = jQuery('select[name="billing_type"]').val();
        if (billingType == 'National Account') {
			jQuery('table[name="LBL_POTENTIALS_NATIONALACCOUNT"]').show();
		}

		jQuery('select[name="billing_type"]').on('change', function() {
			if(jQuery(this).val() == 'National Account') {
				// if(jQuery('table[name="LBL_POTENTIALS_NATIONALACCOUNT"]').hasClass('hide')){
					jQuery('table[name="LBL_POTENTIALS_NATIONALACCOUNT"]').show();
				// }
            } else {
				// if(!jQuery('table[name="LBL_POTENTIALS_NATIONALACCOUNT"]').hasClass('hide')){
					jQuery('table[name="LBL_POTENTIALS_NATIONALACCOUNT"]').hide();
				// }
			}
		});
	},
	registerChangeOppStatus: function () {

		var OppStatust = jQuery('[name="opportunitystatus"]').val();
		if(OppStatust== 'Cancelled' || OppStatust == 'Lost')
		{
			Vtiger_Edit_Js.showCell('opportunityreason');
		}
		else {
			Vtiger_Edit_Js.hideCell('opportunityreason');
		}
		jQuery('[name="opportunitystatus"]').on('change', function() {
			if (jQuery(this).val() == 'Cancelled' || jQuery(this).val() == 'Lost') {
					Vtiger_Edit_Js.showCell('opportunityreason');
				} else {
					Vtiger_Edit_Js.hideCell('opportunityreason');
				}
			});
	},

	referenceModulePopupRegisterEvent : function(container){
		var thisInstance = this;
        container.on("click", '.relatedPopup', function(e) {
			// get related module
			var td=jQuery(e.currentTarget).closest('td');
			var popupReferenceModule = td.find('[name="popupReferenceModule"]').val();
			if(popupReferenceModule == 'Contracts') {
				// Check Role value
				var related_to = jQuery('[name="related_to"]').val();
				if(related_to !='') {
					thisInstance.openPopUp(e);
                } else {

				}
            } else {
				thisInstance.openPopUp(e);
			}
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
	/**
	 * Function to get popup params
	 */
	getPopUpParams : function(container) {
		var params = this._super(container);
        var sourceFieldElement = jQuery('input[class="sourceField"]', container);

		if(sourceFieldElement.attr('name') == 'oppotunitiescontract') {
			var form = this.getForm();
			var parentIdElement  = form.find('[name="related_to"]');
			if(parentIdElement.length > 0 && parentIdElement.val().length > 0 && parentIdElement.val() != 0) {
				var closestContainer = parentIdElement.closest('td');
				params['related_parent_id'] = parentIdElement.val();
				params['related_parent_module'] = closestContainer.find('[name="popupReferenceModule"]').val();
            } else {
				var closestContainer = parentIdElement.closest('td');
				params['related_parent_id'] = '';
				params['related_parent_module'] = '';
			}
		}
		return params;
	},
	/**
	 * Function to search module names
	 */
	registerAutoCompleteFields : function(container) {
		var thisInstance = this;
		var autoCompleteOptions = {
            'minLength': '3',
            'source': function(request, response) {
				//element will be array of dom elements
				//here this refers to auto complete instance
				var inputElement = jQuery(this.element[0]);
				var searchValue = request.term;
				var params = thisInstance.getReferenceSearchParams(inputElement);
				params.search_value = searchValue;
				thisInstance.searchModuleNames(params).then(function(data){
					var reponseDataList = [];
					var serverDataFormat = data.result;
					if(serverDataFormat.length <= 0) {
						jQuery(inputElement).val('');
						serverDataFormat = new Array({
                            'label': app.vtranslate('JS_NO_RESULTS_FOUND'),
                            'type': 'no results'
						});
					}
					for(var id in serverDataFormat){
						var responseData = serverDataFormat[id];
						reponseDataList.push(responseData);
					}
					response(reponseDataList);
				});
			},
            'select': function(event, ui) {
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

                fieldElement.trigger(Vtiger_Edit_Js.postReferenceSelectionEvent, {
                    'data': selectedItemData
                });
			},
            'change': function(event, ui) {
				var element = jQuery(this);
				//if you dont have readonly attribute means the user didnt select the item
				if(element.attr('readonly')== undefined) {
					element.closest('td').find('.clearReferenceSelection').trigger('click');
				}
			},
            'open': function(event, ui) {
				//To Make the menu come up in the case of quick create
                jQuery(this).data('autocomplete').menu.element.css('z-index', '100001');

			}
		};

		container.find('input.autoComplete').autocomplete(autoCompleteOptions);
	},


	searchModuleNames : function(params) {
		var aDeferred = jQuery.Deferred();
		if(typeof params.module == 'undefined') {
			params.module = app.getModuleName();
		}
		if(typeof params.action == 'undefined') {
			params.action = 'BasicAjax';
		}

		if (params.search_module == 'Contracts' ) {
			var form = this.getForm();
			var parentIdElement  = form.find('[name="related_to"]');
			if(parentIdElement.length > 0 && parentIdElement.val().length > 0) {
				var closestContainer = parentIdElement.closest('td');
				params.parent_id = parentIdElement.val();
				params.parent_module = closestContainer.find('[name="popupReferenceModule"]').val();
			}
		}
		AppConnector.request(params).then(
			function(data){
				aDeferred.resolve(data);
			},
			function(error){
				aDeferred.reject();
			}
		);

		return aDeferred.promise();
	},
    getUrlParameter: function(sParam) {
    var sPageURL = decodeURIComponent(window.location.search.substring(1)),
        sURLVariables = sPageURL.split('&'),
        sParameterName,
        i;

    for (i = 0; i < sURLVariables.length; i++) {
        sParameterName = sURLVariables[i].split('=');

        if (sParameterName[0] === sParam) {
            return sParameterName[1] === undefined ? true : sParameterName[1];
        }
    }
},
	registerBasicEvents : function(container) {
		this._super(container);
		this.registerPostReferenceSelectionEvent(container);
        this.initializeAddressAutofill('Opportunities');

		if(this.getUrlParameter("fromcapacity") == "true"){
            jQuery(".cancelLink").attr("onclick", "window.open('index.php?module=Opportunities&view=List','_self')");
		}
	},

    // Populate load to date if it is currently empty
	registerLoadFromPopulateLoadTo : function() {
		var load_from = jQuery('input[name="load_date"]');
		var load_to = jQuery('input[name="load_to_date"]');
		load_from.on('change', function() {
			if(load_to.val() == '') {
				load_to.val(load_from.val());
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

		fieldElement.val(id);
        fieldDisplayElement.val(selectedName).attr('readonly', true);
        fieldElement.trigger(Vtiger_Edit_Js.referenceSelectionEvent, {
            'source_module': popupReferenceModule,
            'record': id,
            'selectedName': selectedName
        });

        fieldDisplayElement.validationEngine('closePrompt', fieldDisplayElement);
		if(jQuery('[name="instance"]').val() != 'graebel') {
			this.registerAddMorolesByChangeAccount(id, container);
		}
	},

    registerAddMorolesByChangeAccount: function(id, container) {
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
                        } else {
							$(this).remove();
						}
					});
					jQuery('[name^="MoveRolesTable"]').find('.numMoveRoles').val(numberMoveroles);
                    $.each(data.result, function(k, v) {
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
                                    jQuery(this).attr('name', defaultName.replace('[]', '_' + recordCount + '[]'));
								} else {
									var index = defaultName.search(/\d/);
									var secondIndex = defaultName.indexOf('_', index);
									if(index > 0 && guestModule != 'ExtraStops') {
										if(secondIndex > 0) {
											var secondNumber = defaultName.substr(secondIndex + 1);
										}
										defaultName = defaultName.substr(0, index) + recordCount;
                                        if (typeof secondNumber != 'undefined') {
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
                        $('[name=moveroles_role_' + recordCount + '_display]').attr('readonly', 'readonly');

						$('[name=moveroles_employees_'+recordCount+']').val(v['user']);
						$('[name=moveroles_employees_'+recordCount + '_display]').val(v['employee_name']);
                        $('[name=moveroles_employees_' + recordCount + '_display]').attr('readonly', 'readonly');
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
                            } else {}
						} catch (errMT) {
							//do nothing this is fine
						}
						jQuery(this).closest('table').trigger({
                            type: "addRecord",
                            newRow: newRecordFields
						});
					});


				}
			});
		}
    },

    hideBusinessChannel: function() {
        Vtiger_Edit_Js.hideCell('business_channel');
    },

    tieBusinessChannelAndShipperType: function() {
        var business_channel = $('[name="business_channel"]');
        var shipper_type = $('[name="shipper_type"]');

        shipper_type.on('change', function() {
            var map = {
                'COD': 'Consumer',
                'NAT': 'Corporate'
            };
            Vtiger_Edit_Js.setValue('business_channel', map[shipper_type.val()]);
        });
    },

    initializeDaysToMove: function() {
        this.daysToMove = new Days_To_Move_Js();
        this.daysToMove.registerEvents();
    },

    moveTypeUpdate: function(moveType, prevType) {
        this.updateBrandEvent();
    },

    businessLineUpdate: function(businessLine, moveType, militaryTariff) {
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

    agentChangeCallback : function() {
        agentid = jQuery('select[name="agentid"]').val();
        if(Number($('[name="movehq"]').val())) {
            participantInstance = ParticipatingAgents_Edit_Js.getInstance();
            var bookingRow = participantInstance.findParticipantRow('Booking Agent');
            if(bookingRow) {
                var selectedAgent = jQuery('[name = "agentid"]');
                var bookingAgentId = selectedAgent.val();
                var bookingAgentName = selectedAgent.find('option:selected').text();
                //var bookingAgentName = jQuery('[name = "agentid"]').text();
                var dataURL = "index.php?module=Opportunities&action=GetParticipantIdFromAgentOwner&agentmanagerid=" + bookingAgentId;
                AppConnector.request(dataURL).then(
                    function (data) {
                        if(data.success){
                            bookingAgentId = data.result['agentid'];
                            bookingAgentName = data.result['agentName'];
                            participantInstance.setParticipantField(bookingRow, 'Booking Agent', bookingAgentId, bookingAgentName, 'full');
                        }
                    }
                );
            }
        }
    },

    initializeSalesPerson: function() {
        this.salesperson = new Sales_Person_Js();
        this.salesperson.onAgentChange(this.agentChangeCallback);
        this.salesperson.registerEvents();
    },

	initializeSelfHaul : function() {
        this.selfhaul = new Self_Haul_Js();
        this.selfhaul.onUpdate(this.updateHaulingAgent, this);
        this.selfhaul.registerEvents();
    },

    initializeSTS: function() {
        this.sts = new Opportunities_STS_Js();
        this.sts.registerEvents(true);
    },

    initializeMilitaryFields: function() {
        this.militaryFields = new Opportunities_MilitaryFields_Js();
        this.militaryFields.registerEvents();
    },

    sirvaEvents: function() {
        this.updateBrandEvent();

        this.bindLoadFromToDate();
        this.setLeadTypeToReadonly();
        this.hideBusinessChannel();
        this.tieBusinessChannelAndShipperType();

        this.initializeMoveType();
        this.initializeSTS();
        this.initializeMilitaryFields();
    },

	registerEvents : function() {
		this._super();

        this.initializeSalesPerson();
        this.initializeDaysToMove();
        this.initializeSelfHaul();
        this.defaultLeadSourceOptions = this.getPickOptions(jQuery('select[name="leadsource"]'));
        this.leadsourceWorkspaceOptions  = this.getPickOptions(jQuery('select[name="leadsource_workspace"]'));
        this.leadsourceNationalOptions  = this.getPickOptions(jQuery('select[name="leadsource_national"]'));
        this.leadsourceHHGOptions = this.getPickOptions(jQuery('select[name="leadsource_hhg"]'));
		this.registerSourceNameChange();
        //this.initializeAddressAutofill('Opportunities');
		this.initializeReverseZipAutoFill('Opportunities');
		this.registerPhoneTypeEvents();
		this.preventEmptyDestination();
        if(jQuery('select[name="move_type"]').length) {
            this.initializeMoveType();
        }
		//this.registerAddStopEvent();
		this.setParticipatingBookingAgent();
		//this.registerStopTypeChange();
		//this.formatStopsPhoneNumbers();
		//this.registerSirvaStopTypeChange();
		//this.deleteStopEvent();
		//this.registerChangeLocationType();
		this.registerChangeOppType();
		this.registerChangeContact();
		this.populateAccountDetails();
		this.toggleNationalAccountBlock();

		loadBlocksByBusinesLine('Opportunities', 'business_line');
		//this links sales_stage to opportunity_disposition
		this.registerChangeSalesStage();
		//to link the disposition lost other reason text box
		this.registerChangeDispositionLost();
		//to link the Opportunity Disposition field on load
		jQuery('select[name="sales_stage"]').trigger('change');
		this.updateTabIndexValues();
		this.setDefaultInputs();
		this.registerTransitGuideEvent();
		this.validDate();
        jQuery('select[name="shipper_type"]').trigger('change');
        jQuery('input:checkbox[name="cbs_ind"]').trigger('change');
		this.setLeadSourceOptions();
		this.setReasonRowVisibility();
		this.setVanlineVisibility();
		this.registerOppStatusChange();
		this.registerReasonChange();
		this.billingTypeNationalAccountBlock();
		this.registerChangeOppStatus();
		// 'oppotunitiescontract' really?
		this.registerAutoCompleteFields(jQuery('[name="oppotunitiescontract"]').parent());

        this.registerBillingTypeField();
        // Populate load to date if it is currently empty
		this.registerLoadFromPopulateLoadTo();

        if($('[name="instance"]').val() == 'sirva') {
            this.sirvaEvents();
        }
	}
});

function contactIsTransferee(contactId, popUp){
	var url = 'index.php?module=Opportunities&action=PopulateContactData&get_type=true&contact_id=' + contactId;
	AppConnector.request(url).then(
		function(data) {
			if (data.success) {
				if(data.result.contact_type == 'Transferee'){
					console.dir('contactIsTransferee: true');
					if(popUp == true){
						jQuery('input:hidden[name="contact_id"]').trigger('populateContactPopUp');
                    } else {
						jQuery('input:hidden[name="contact_id"]').trigger('populateContactAuto');
					}
                } else {
					//console.dir('contactIsTransferee: false');
				}
			}
		},
		function(err){
			//there was a problem
		}
	);
}

function populateContactData(contactId){
	var thisInstance = this;
	var url = 'index.php?module=Opportunities&action=PopulateContactData&contact_id=' + contactId;
	AppConnector.request(url).then(
		function(data) {
			if (data.success) {
				jQuery('input[name="origin_address1"]').val(data.result.mailingstreet);
				jQuery('input[name="origin_address2"]').val(data.result.otherstreet);
				jQuery('input[name="origin_city"]').val(data.result.mailingcity);
				jQuery('input[name="origin_state"]').val(data.result.mailingstate);
				jQuery('input[name="origin_zip"]').val(data.result.mailingzip);
				jQuery('input[name="origin_fax"]').val(data.result.fax);
				jQuery('input[name="potentialname"]').val(data.result.firstname+' '+data.result.lastname);
                jQuery('input[name="primary_contact_email"]').val(data.result.email);
				//update origin country regardless of if it's a picklist or a text field
				jQuery('input[name="origin_country"]').val(data.result.mailingcountry);
				console.dir(jQuery('select[name="origin_country"]').find('option[value="'+data.result.mailingcountry+'"]'));
				jQuery('select[name="origin_country"]').find('option[value="'+data.result.mailingcountry+'"]').prop('selected', true).attr('selected', 'selected');
				jQuery('select[name="origin_country"]').trigger('liszt:updated');
				//update phones
				var phone1Set = false;
				var phone2Set = false;
				//primary phone
				if(data.result.phone){
					jQuery('input[name="origin_phone1"]').val(data.result.phone);
					jQuery('select[name="origin_phone1_type"]').find('option[value="Work"]').prop('selected', true).attr('selected', 'selected');
					jQuery('select[name="origin_phone1_type"]').trigger('liszt:updated');
					phone1Set = true;
				}
				//mobile phone
				if(data.result.mobile){
					if(phone1Set == false){
						jQuery('input[name="origin_phone1"]').val(data.result.mobile);
						jQuery('select[name="origin_phone1_type"]').find('option[value="Cell"]').prop('selected', true).attr('selected', 'selected');
						jQuery('select[name="origin_phone1_type"]').trigger('liszt:updated');
						phone1Set = true;
					} else if(phone1Set == true && phone2Set == false){
						jQuery('input[name="origin_phone2"]').val(data.result.mobile);
						jQuery('select[name="origin_phone2_type"]').find('option[value="Cell"]').prop('selected', true).attr('selected', 'selected');
						jQuery('select[name="origin_phone2_type"]').trigger('liszt:updated');
						phone2Set = true;
					}
				}
				//home phone
				if(data.result.homephone){
					if(phone1Set == false){
						jQuery('input[name="origin_phone1"]').val(data.result.homephone);
						jQuery('select[name="origin_phone1_type"]').find('option[value="Home"]').prop('selected', true).attr('selected', 'selected');
						jQuery('select[name="origin_phone1_type"]').trigger('liszt:updated');
						phone1Set = true;
					} else if(phone1Set == true && phone2Set == false){
						jQuery('input[name="origin_phone2"]').val(data.result.homephone);
						jQuery('select[name="origin_phone2_type"]').find('option[value="Home"]').prop('selected', true).attr('selected', 'selected');
						jQuery('select[name="origin_phone2_type"]').trigger('liszt:updated');
						phone2Set = true;
					}
				}
				//other phone
				if(data.result.homephone){
					if(phone1Set == false){
						jQuery('input[name="origin_phone1"]').val(data.result.homephone);
						phone1Set = true;
					} else if(phone1Set == true && phone2Set == false){
						jQuery('input[name="origin_phone2"]').val(data.result.homephone);
						phone2Set = true;
					}
				}

			}
		},
		function(err){
			//there was a problem
		}
	);
}


/*/
 //This was made by Amin and appears to be awful and used no where.  Keeping it here to preserve how not to write code
 function random()
 {
 var text = "";
 var possible = "0123456789";

 for( var i=0; i < 5; i++ )
 text += possible.charAt(Math.floor(Math.random() * possible.length));

 return text;
 }
 //*/
function getQueryVariable(variable) {
	var query = window.location.search.substring(1);
	var vars = query.split("&");
	for (var i=0; i<vars.length; i++) {
		var pair = vars[i].split("=");
        if (pair[0] == variable) {
            return pair[1];
        }
	}
	return(false);
}

function checkRegistrationNumber() {
	var inputElement = jQuery('input[name="register_sts_number"]');

    if(inputElement.prop('readonly')){
        // They no longer want it to be disabled, only allow Booked and Lost.
        // jQuery('select[name="sales_stage"]').attr('disabled', true).trigger('liszt:updated');;
        restrictSalesStage(['Closed Won','Closed Lost']);
        jQuery('select[name="opportunity_disposition"]').attr('disabled', true).trigger('liszt:updated');;
    } else {
	if(jQuery('select[name="sales_stage"]').find('option:selected').val() == 'Closed Won') {
		inputElement.closest('td').prev().find('label').prepend("<span class='redColor'>* </span>");
            inputElement.attr('data-validation-engine', 'validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]');
		var validationFields = JSON.parse('{"mandatory":true,"presence":true,"quickcreate":true,"masseditable":true,"defaultvalue":false,"type":"string","name":"register_sts_number","label":"Registration Number"}');
            inputElement.attr('data-fieldinfo', JSON.stringify(validationFields));
		inputElement.addClass('validation');
	} else {
		inputElement.closest('td').prev().find('label').find('span').remove();
            inputElement.attr('data-validation-engine', '');
            inputElement.attr('data-fieldinfo', '');
		inputElement.removeClass('validation');
    	}
	}
}

function restrictSalesStage(allowed) {
    var sales_stage = $('[name="sales_stage"]');
    sales_stage.find('option').each(function() {
        var ele = $(this);
        if(allowed.indexOf(ele.val()) === -1) {
            ele.attr('disabled','disabled');
        }else {
            ele.removeAttr('disabled');
        }
    });

    sales_stage.trigger('liszt:updated');
}

$(document).ready(function() {
	checkRegistrationNumber();
	jQuery('select[name="sales_stage"]').on('change', function(){
		checkRegistrationNumber();
	});
    var instance = jQuery('input[name="instance"]').val();
    if (instance == 'sirva') {
        jQuery('select[name="leadsource"]').attr('disabled', 'disabled');
    }
    if(jQuery('input[name="movehq"]').val()){
        jQuery('input[name="amount"]').attr('disabled', 'disabled');
    }
	jQuery('select[name="brand"]').trigger('change');
});
