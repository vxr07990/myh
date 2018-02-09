/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

Vtiger_Detail_Js("Cubesheets_Detail_Js",{

	effectiveTariffData : false,

	convertCubesheet : function(buttonElement){
		thisInstance = this;
		buttonElement = jQuery(buttonElement);
		buttonContainer = jQuery('#createEstimateBtnContainer');
		buttonElement.addClass('hide');
		buttonContainer.progressIndicator();
		convertUrl = "index.php?module=Cubesheets&view=ConvertCubesheet&record=" + jQuery('#recordId').val() + "&sourceModule=" + jQuery('[name="sourceModule"').val();
		//console.dir(convertUrl);
		AppConnector.request(convertUrl).then(
			function (data) {
				if (data) {
					buttonElement.removeClass('hide');
					buttonContainer.progressIndicator({'mode' : 'hide'});
					//console.dir(data);
					app.showModalWindow(data, function(){
						//TODO put these events into their own JS file and clean it up
						//enable form validation
						var params = app.validationEngineOptions;
							params.onValidationComplete = function(form, valid){
								//console.dir(valid);
								//console.dir(form);
								if(valid){
									jQuery('#CreateEstimateForm').off('submit');
                                                                        conversionBtn = jQuery('#convertCubesheet');
                                                                        conversionBtn.addClass('hide');
                                                                        conversionBtn.parent().progressIndicator();
									jQuery('#cubesheet')[0].contentWindow.saveAllItems().then(function(){
										form.submit();
									});
								}
								return false;
							};
						jQuery('#CreateEstimateForm').validationEngine(app.validationEngineOptions);
						jQuery('#CreateEstimateForm').on('submit', function(e){
							e.preventDefault();
						});
						//create account checkbox events
						jQuery('input:checkbox[name="createAccount"]').on('click', function(){
							if(jQuery(this).prop('checked') == true && jQuery(this).closest('table').find('.blockContents').hasClass('hide')){
								jQuery(this).closest('table').find('.blockContents').removeClass('hide');
								jQuery('input[name="account_id"]').closest('td').find('div').addClass('hide').closest('td').prev('td').find('label').addClass('hide');
								jQuery('input[name="account_id"]').prop('disabled', true);
								jQuery('input[name="account_accountname"]').prop('disabled', false);
								jQuery('select[name="account_assigned_user_id"]').prop('disabled', false);
								jQuery('select[name="account_agentid"]').prop('disabled', false);
								//console.dir(jQuery(this).closest('tr').find('label.hide'));
								if(jQuery('input[name="account_id"]').closest('tr').find('label.hide').length == 2){
									jQuery('input[name="account_id"]').closest('tr').addClass('hide'); //odd number of fields and the business line is last so hide the fields
								}
							}
							if(jQuery(this).prop('checked') == false && !jQuery(this).closest('table').find('.blockContents').hasClass('hide')){
								jQuery(this).closest('table').find('.blockContents').addClass('hide');
								jQuery('input[name="account_id"]').prop('disabled', false);
								jQuery('input[name="account_accountname"]').prop('disabled', true);
								jQuery('select[name="account_assigned_user_id"]').prop('disabled', true);
								jQuery('select[name="account_agentid"]').prop('disabled', true);
								jQuery('input[name="account_id"]').closest('td').find('div').removeClass('hide').closest('td').prev('td').find('label').removeClass('hide');
								jQuery('input[name="account_id"]').closest('tr').removeClass('hide');
							}
						});
						//initialize account_id field to be hidden or not
						/*if(jQuery('input:checkbox[name="createAccount"]').prop('checked') == true){
							jQuery('input[name="account_id"]').closest('td').find('div').addClass('hide').closest('td').prev('td').find('label').addClass('hide');
							jQuery('input[name="account_id"]').prop('disabled', true);
						}*/
						//register basic edit field events
						var editInstance = Vtiger_Edit_Js.getInstance();
						editInstance.registerBasicEvents(jQuery('#convertContainer'));
						//editInstance.eventToHandleChangesForReferenceFields();
						//hide business line for sirva
						if(jQuery('input:hidden[name="instance_name"]').val() == 'sirva'){
							var businessLineElement = jQuery('select[name="business_line_est"]');
							// businessLineElement.prop('disabled', true);
							//console.dir(businessLineElement.closest('tr').find('label.hide'));
							if(businessLineElement.closest('tr').find('label.hide')){
								businessLineElement.closest('tr').addClass('hide'); //odd number of fields and the business line is last so hide the fields
							}
							businessLineElement.closest('td').find('.chzn-container').addClass('hide'); //hide the business line select
							businessLineElement.closest('td').prev('td').find('label').addClass('hide'); //hide the label
						}
						//initialize local tariff to be hidden or not
						if(jQuery('select[name="move_type"]').find('option:selected').val() != 'Local US' && jQuery('select[name="move_type"]').find('option:selected').val() != 'Local Canada' && jQuery('select[name="business_line_est"]').find('option:selected').val() != 'Local Move'){
							jQuery('select[name="local_tariff"]').closest('td').find('.chzn-container').addClass('hide'); //hide local tariff select
							jQuery('select[name="local_tariff"]').closest('td').prev('td').find('label').addClass('hide'); //hide label
							jQuery('select[name="local_tariff"]').prop('disabled', true);
						}
						//local tariff picklist hide/show events
						jQuery('select[name="move_type"], select[name="business_line_est"]').on('change', function(){
							var newValue = jQuery(this).find('option:selected').val();
							if(newValue == 'Local Move' || newValue == 'Local US' || newValue == 'Local Canada'){
								jQuery('select[name="local_tariff"]').closest('td').find('.chzn-container').removeClass('hide'); //hide local tariff select
								jQuery('select[name="local_tariff"]').closest('td').prev('td').find('label').removeClass('hide'); //hide label
								jQuery('select[name="local_tariff"]').prop('disabled', false);
							} else{
								jQuery('select[name="local_tariff"]').closest('td').find('.chzn-container').addClass('hide'); //hide local tariff select
								jQuery('select[name="local_tariff"]').closest('td').prev('td').find('label').addClass('hide'); //hide label
								jQuery('select[name="local_tariff"]').prop('disabled', true);
							}
						});
						//sirva movetype to business line mapping
						jQuery('select[name="move_type"]').on('change', function(e){
							var moveType = jQuery('select[name="move_type"]').find('option:selected').val();
							jQuery('select[name="business_line_est"]').find('option:selected').prop('selected', false);
							switch(moveType) {
								case 'Local Canada':
								case 'Local US':
									jQuery('select[name="business_line_est"]').find('option[value="Local Move"]').prop('selected', true);
									break;
								case 'Max 3':
									//special logic for max 3
									jQuery('select[name="business_line_est"]').find('option[value="Local Move"]').prop('selected', true);
									break;
								case 'Max 4':
									//special logic for max 4
									jQuery('select[name="business_line_est"]').find('option[value="Local Move"]').prop('selected', true);
									break;
								case 'Sirva Military':
									jQuery('select[name="business_line_est"]').find('option[value="Interstate Move"]').prop('selected', true);
									break;
								case 'Interstate':
								case 'Inter-Provincial':
								case 'Cross Border':
									jQuery('select[name="business_line_est"]').find('option[value="Interstate Move"]').prop('selected', true);
									break;
								case 'O&I':
									jQuery('select[name="business_line_est"]').find('option[value="Commercial Move"]').prop('selected', true);
									break;
								case 'Intrastate':
								case 'Intra-Provincial':
									jQuery('select[name="business_line_est"]').find('option[value="Intrastate Move"]').prop('selected', true);
									break;
								case 'Alaska':
								case 'Hawaii':
								case 'International':
									jQuery('select[name="business_line_est"]').find('option[value="International Move"]').prop('selected', true);
									break;
								default:
									break;
							}
							//update picklists
							jQuery('select[name="business_line_est"]').trigger('liszt:updated');
						});
						//create account actually needs to always initialize as hidden
						var accountElement = jQuery('input:checkbox[name="createAccount"]');
						if(!accountElement.closest('table').find('.blockContents').hasClass('hide')){
							accountElement.closest('table').find('.blockContents').addClass('hide');
							jQuery('input[name="account_id"]').prop('disabled', false);
							jQuery('input[name="account_accountname"]').prop('disabled', true);
							jQuery('select[name="account_assigned_user_id"]').prop('disabled', true);
							jQuery('select[name="account_agentid"]').prop('disabled', true);
							jQuery('input[name="account_id"]').closest('td').find('div').removeClass('hide').closest('td').prev('td').find('label').removeClass('hide');
							jQuery('input[name="account_id"]').closest('tr').removeClass('hide');
						}
					}, {'display':'block', 'overflow-y':'visible'});
				}
			},
			function (error) {
				//something went wrong
				buttonElement.removeClass('hide');
				buttonContainer.progressIndicator({'mode' : 'hide'});
			}
		).then(
			function() {
				this.effectiveTariffData = JSON.parse(jQuery('#allAvailableTariffs').val());
				// Loads tariff info
				var popupInstance = Cubesheets_Popup_Js.getInstance();
				popupInstance.registerPicklistUpdate(this.effectiveTariffData);
				popupInstance.updatePackingPicklist();
				if(jQuery('input:hidden[name="instance_name"]').val() == 'sirva') {
					popupInstance.disableMoveType();
				}
                var isSirva = $('input[name="instance"]').val() == 'sirva' ? true : false;
				var isArpin = $('input[name="instance"]').val() == 'arpin' ? true : false;
				if (isSirva || isArpin) {
				    $('[name="move_type"]').trigger('change');
                    Vtiger_Edit_Js.setReadonly('business_line_est', true);
                    Vtiger_Edit_Js.setReadonly('effective_tariff', true);
                } else {
                    popupInstance.updateEffectiveTariffPicklist();
                }
			}
		);
	}
},{
    registerLeavePageWithoutSubmit : function(){
		jQuery('a').on('click',function(e){
            if (jQuery('#cubesheet')[0].contentWindow.containsLocalModifications()) {
                if(confirm("Changes you made may not be saved")){
                    return;
                }else{
                    return false;
                }
            }
        });
    },

	/*regiserCreateEstimate : function(){
		jQuery('#createEstimate').on('click', function(){
			var actionURL = 'index.php?module=Cubesheets&action=CreateEstimate&record=' + jQuery('#recordId').val();
			AppConnector.request(actionURL).then(
				function (data) {
					if (data.success) {
						console.dir(data);
						//window.location.href = 'index.php?module=Estimates&view=Detail&record=' + data.result.estimateId;
					}
				},
				function (error) {
					console.dir(data);
					//something went wrong
				}
			);
		});
	},*/

	registerViewArchiveButton : function() {
		var thisInstance = this;
		jQuery('#viewArchiveButton').off('click').on('click', function() {
			var recordId = jQuery('#recordId').val();

			var dataUrl = 'index.php?module=Cubesheets&action=GetArchiveURI&record='+recordId;

			AppConnector.request(dataUrl).then(
				function(data) {
					if(data.success) {
						//var viewWindow = window.open(data.result, '_blank');
						//viewWindow.focus();
						app.showModalWindow(data.result, function() {
							jQuery('.archiveButton').on('click', function() {
								var url = jQuery(this).data('url');
								var viewWindow = window.open(url, '_blank');
								app.hideModalWindow();
							})
						});
					} else {
						bootbox.alert(data.error.code + ': ' + data.error.message);
					}
				},
				function(error, err) {
					bootbox.alert(error + ': ' + err);
				}
			)
		});
	},

    registerEventForRelatedTabClick : function() {

    },

	// registerRelatedRowClickEvent: function(){
	// 	var detailContentsHolder = this.getContentHolder();
	// 	detailContentsHolder.on('click','.listViewEntries',function(e){
	// 		var targetElement = jQuery(e.target, jQuery(e.currentTarget));
	// 		if(targetElement.is('td:first-child') && (targetElement.children('input[type="checkbox"]').length > 0)) return;
	// 		if(jQuery(e.target).is('input[type="checkbox"]')) return;
	// 		var elem = jQuery(e.currentTarget);
	// 		var recordUrl = elem.data('recordurl');
	// 		if(typeof recordUrl != "undefined"){
	// 			var newWindow = window.open(recordUrl, "_blank");
	// 		}
	// 	});
    //
	// },

    registerEvents : function(){
				this._super();
        document.domain = jQuery('#site_domain').val();
        //document.domain = "localhost";
				jQuery('form').submit(function(e) {
            e.preventDefault();
            console.dir(jQuery('#cubesheet')[0].contentWindow.containsLocalModifications());
            //fire save function in cubesheet, then display bootbox message to user
            jQuery('#cubesheet')[0].contentWindow.saveAllItems().then(
                function(data) {
                    bootbox.alert(data);
                }
            );
        });
        this.registerLeavePageWithoutSubmit();
		//this.registerViewArchiveButton();
		//this.regiserCreateEstimate();
	},
});
