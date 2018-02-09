/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

Vtiger_Detail_Js("Orders_Detail_Js",{
    createWorkflow: function(){
            var loadingMessage = app.vtranslate('Loading..');
            var progressIndicatorElement = jQuery.progressIndicator({
                'message': loadingMessage,
                'position': 'html',
                'blockInfo': {
                    'enabled': true
                }
            });

            params = {
                'module': 'Orders',
                'view': 'ShowModals',
                'mode': 'showCreateOverflowModal',
                'order_id': $('#recordId').val(),
            };
            AppConnector.request(params).then(
                function (data) {
                    app.showModalWindow(data, function (data) {

                        Orders_Detail_Js.registerModalFields();
                        progressIndicatorElement.progressIndicator({
                            'mode': 'hide'
                        });

                    });
                },
                function (jqXHR, textStatus, errorThrown) {}
            );
    },
    saveOverflow: function(){
        var arr = [];
        jQuery("table[name='ordersVehiclesTable'] > tbody > tr:not(.default):not(.do-not-copy-me)").each(function(){
            var vehicle = {};
            vehicle.make = jQuery(this).find('input[name^="vehicle_make"]').val();
            vehicle.model = jQuery(this).find('input[name^="vehicle_model"]').val();
            vehicle.year = jQuery(this).find('input[name^="vehicle_year"]').val();
            vehicle.vin = jQuery(this).find('input[name^="vehicle_vin"]').val();
            //vehicle.flatrate = jQuery(this).find('input[name^="nombre_campo_flatrate"]').prop("checked");
            vehicle.transptype = jQuery(this).find('select[name^="vehicletranstype"] option:selected').text().trim();
            vehicle.ratingtype = jQuery(this).find('select[name^="vehicle_ratingtype"] option:selected').text().trim();
            arr.push(vehicle);
        });
        var params = {
            'module' : 'Orders',
            'action' : 'SaveOverflows',
            'mode'   : 'saveOverflows',
            'vehicles' : JSON.stringify(arr),
            'orders_ecube': jQuery('[name="of_order_cube"]').val(),
            'orders_elinehaul': jQuery('[name="of_order_linehaul"]').val(),
            //'of_order_percentage': jQuery('[name="of_order_percentage"]').val(), //nosequees
            //'main_order_percentage': jQuery('[name="main_order_percentage"]').val(), //nosequees
            'orders_eweight': jQuery('[name="of_weight"]').val(),
            'order_id' : jQuery('#order_id_hidden').val(),
            'description' : jQuery('[name="description"]').val(),
        };
        AppConnector.request(params).then(
            function(data) {



                if(data.result.created !== 'false' && data.result.order_id != undefined){
                    app.hideModalWindow();
                    window.location.href = 'index.php?module=Orders&view=Detail&record=' + data.result.order_id;

                }else{
                    var msgAddon = '.';
                    if (data.result.msg){
                        msgAddon = ':<br/>'+data.result.msg;
                    }
                    var params = {
                        title: app.vtranslate('Error'),
                        text: app.vtranslate('There was an error creating the overflow'+msgAddon),
                        animation: 'show',
                        type: 'info'
                    };
                    Vtiger_Helper_Js.showPnotify(params);
                    app.hideModalWindow();
                    console.log(data);
                }

        });
    },
    deleteVehicleRow: function(row){
        jQuery(row).closest('tr').remove();
        var num = 0;
        jQuery("table[name='ordersVehiclesTable'] > tbody > tr:not(.default):not(.do-not-copy-me)").each(function(){
            jQuery(this).attr('id',num);
            jQuery(this).find('input[name^="nombre_campo_make"]').attr('name', 'nombre_campo_make'+num);
            jQuery(this).find('input[name^="nombre_campo_model"]').attr('name', 'nombre_campo_model'+num);
            jQuery(this).find('input[name^="nombre_campo_year"]').attr('name', 'nombre_campo_year'+num);
            jQuery(this).find('input[name^="nombre_campo_vin"]').attr('name', 'nombre_campo_vin'+num);
            //jQuery(this).find('input[name^="nombre_campo_flatrate"]').attr('name', 'nombre_campo_flatrate'+num);
            jQuery(this).find('select[name^="nombre_vehicletranstype"]').attr('name', 'nombre_vehicletranstype'+num);
            num++;
        });
    },
    addVehicleButton: function(){
        var newRow = jQuery('table[name="ordersVehiclesTable"] > tbody > tr.default:not(.do-not-copy-me)').clone(true,true);
        var sequenceNode = jQuery("table[name='ordersVehiclesTable'] > tbody > tr:not(.default):not(.do-not-copy-me)").length;

        newRow.removeClass('default').removeClass('hide');
        newRow.attr('id', sequenceNode);
        newRow.find('input[name="nombre_campo_make0"]').attr('name', 'nombre_campo_make'+sequenceNode);
        newRow.find('input[name="nombre_campo_model0"]').attr('name', 'nombre_campo_model'+sequenceNode);
        newRow.find('input[name="nombre_campo_year0"]').attr('name', 'nombre_campo_year'+sequenceNode);
        newRow.find('input[name="nombre_campo_vin0"]').attr('name', 'nombre_campo_vin'+sequenceNode);
        //newRow.find('input[name="nombre_campo_flatrate0"]').attr('name', 'nombre_campo_flatrate'+sequenceNode);
        newRow.find('select[name="nombre_vehicletranstype0"]').attr('name', 'nombre_vehicletranstype'+sequenceNode);

        newRow.appendTo(jQuery("table[name='ordersVehiclesTable'] > tbody"));
        jQuery("table[name='ordersVehiclesTable'] > tbody").find('select[name="nombre_vehicletranstype'+sequenceNode+'"]').chosen();
    },
    registerModalFields: function () {
        jQuery('[name="of_weight"]').on('change', function (e) {
        	var percent = (jQuery('[name="of_weight"]').val() / jQuery('[name="estimated_weight"]').val())*100;
			jQuery('[name="of_order_percentage"]').val(Math.round(percent));
            jQuery('[name="main_order_percentage"]').val(Math.round(100 - percent));
            jQuery('[name="of_order_linehaul"]').val(Math.round(parseFloat(jQuery('[name="main_order_linehaul"]').val().replace(/\./g, '').replace(/,00$/,'')) * percent / 100));
            jQuery('[name="of_order_cube"]').val(Math.round(parseFloat(jQuery('[name="main_order_cube"]').val().replace(/\./g, '').replace(/,00$/,''))  * percent) / 100);
        });
    },

	generatePaperwork : function(buttonElement){
		thisInstance = this.getInstance();
		var progressIndicatorElement = jQuery.progressIndicator({
			'position' : 'html',
			'blockInfo' : {
				'enabled' : true
			}
		});
		var paperworkURL = 'index.php?module=Orders&action=GetPaperworkTypes&record=' + jQuery('input:hidden[id="recordId"]').val();
		AppConnector.request(paperworkURL).then(
			function(data) {
				if(data && data.result) {
					app.showModalWindow(
						data.result,
						function(){
							thisInstance.registerPVOReportsEvents();
						},
						{inline:'true', width:'300px', height:'auto', top:'50%', 'text-align':'center', padding:'5px', href:'#reportsContent'}
					);
				} else {
				    if(data.error.message == 'Estimate_Record_Model is required for the pricing mode') {
				        reportsError = 'Error Processing Request:<br>Cannot create the report without a Primary Pricing record being created against the Order.';
                    } else {
                        reportsError = 'Error Processing Request:<br>Could not access paperwork.';
                    }
                    thisInstance.showAlertBox({'message':reportsError});
				}
				progressIndicatorElement.progressIndicator({
					'mode' : 'hide'
				});
			},
			function(error,err){
				progressIndicatorElement.progressIndicator({
					'mode' : 'hide'
				});
			}
		);
	},
},{

	detailViewRecentTicketsTabLabel : 'Trouble Tickets',
	detailViewRecentTasksTabLabel : 'Local Operations Tasks',
	detailViewRecentMileStonesLabel : 'Orders Milestones',

	registerPVOReportsEvents : function(){
		//console.dir('event!');
		jQuery('#reportsContent').find('button').each(function (){
			//console.dir(jQuery(this));
			jQuery(this).on('click', function () {
			    if(jQuery(this).hasClass('lock')) {
			        thisInstance.showAlertBox({'message':'Error Processing Request:<br>Cannot create the report because it is for Interstate shipments only.'})
                    return;
                }
				var windowBox = jQuery(this);
				var progressIndicator = jQuery(this).closest('td').progressIndicator();
				jQuery(this).addClass('hide');
				var reportURL = 'index.php?module=Orders&action=GetPaperwork&record=' + jQuery('input:hidden[id="recordId"]').val() + '&reportId=' + jQuery(this).attr('name') + '&reportName=' + encodeURIComponent(jQuery(this).html());
				reportURL = reportURL + '&wsdlURL=' + jQuery('input[name="wsdlURL"]').val();
				//var formData = jQuery.param(jQuery('#EditView').serializeFormData());
				//var index = formData.indexOf('&record=');
				//var urlAppend = formData.substring(index, formData.length - 1);
				//reportURL = reportURL + urlAppend;
				console.dir(reportURL);
				AppConnector.request(reportURL).then(
					function (data) {
						if (data.success && data.result) {
							window.location.href = 'index.php?module=Documents&view=Detail&record=' + data.result;
							//console.log(data.result);
						} else {
							progressIndicator.hide();
							windowBox.closest('tbody').append('<tr><td>ERROR: '+data.error.message+'</td></tr>');
						}
					},
					function (error) {
                        thisInstance.showAlertBox({'message':'There was an error with rating engine, try again'});
					}
				);
			});
		});
	},

    showAlertBox: function (data) {
        var aDeferred = jQuery.Deferred();
        var bootBoxModal = bootbox.alert(data['message'], function (result) {
            if (result) {
                aDeferred.reject(); //we only want the button to make the modal box disappear
            } else {
                aDeferred.reject();
            }
        });

        bootBoxModal.on('hidden', function (e) {
            //In Case of multiple modal. like mass edit and quick create, if bootbox is shown and hidden , it will remove
            // modal open
            if (jQuery('#globalmodal').length > 0) {
                // Mimic bootstrap modal action body state change
                jQuery('body').addClass('modal-open');
            }
        })
        return aDeferred.promise();
	},

	/**
	 * Function to register event for create related record
	 * in summary view widgets
	 */
	registerSummaryViewContainerEvents : function(summaryViewContainer){
		this._super(summaryViewContainer);
		this.registerStatusChangeEventForWidget();
		this.registerEventForAddingModuleRelatedRecordFromSummaryWidget();
	},

		 ajaxEditHandling : function(currentTdElement) {
		//console.dir('AJAX EDIT HANDLING!!!!!!');
		var thisInstance = this;
		var detailViewValue = jQuery('.value',currentTdElement);
		var editElement = jQuery('.edit',currentTdElement);
		var actionElement = jQuery('.summaryViewEdit', currentTdElement);
		var fieldnameElement = jQuery('.fieldname', editElement);
		var fieldName = fieldnameElement.val();
		var fieldElement = jQuery('[name="'+ fieldName +'"]', editElement);

		var agentType = false;
		var permission = false;

		if(currentTdElement.hasClass('agentType')){
			var agentType = true;
		}

		//console.dir(fieldElement.attr('name'));
		//console.dir(fieldName);
		//console.dir(fieldElement);
		//console.dir(currentTdElement);


		if(currentTdElement.hasClass('radioPermission')){
			var radioPermission = true;
		}

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
			if(fieldElement.is('input:radio') && currentTdElement.hasClass('radioPermission')) {
				for(i=1; i<=4; i++){
					if(currentTdElement.parent().find('.radioInput'+i).attr('checked') == 'checked'){
						ajaxEditNewValue = i-1;
					}
				}
			}
			var errorExists = fieldElement.validationEngine('validate');
			//If validation fails
			if(errorExists) {
				return;
			}




			fieldElement.validationEngine('hide');
			//console.dir('current: '+ajaxEditNewValue);
			//console.dir('prev: '+previousValue);
			//Before saving ajax edit values we need to check if the value is changed then only we have to save
			if(previousValue == ajaxEditNewValue) {
				//console.dir('same value');
				editElement.addClass('hide');
				detailViewValue.removeClass('hide');
				actionElement.show();
				jQuery(document).off('click', '*', saveHandler);
			} else {
				var preFieldSaveEvent = jQuery.Event(thisInstance.fieldPreSave);
				fieldElement.trigger(preFieldSaveEvent, {'fieldValue' : fieldValue,  'recordId' : thisInstance.getRecordId()});
				if(preFieldSaveEvent.isDefaultPrevented()) {
					//Stop the save
					//console.dir('prevent default');
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


				if(agentType==true || radioPermission==true){
					//saving for custom fields
					customField = currentTdElement.find('[name="'+fieldName+'"]');
					currentTdElement.progressIndicator({'mode':'hide'});
					currentTdElement.progressIndicator();
					editElement.addClass('hide');
					if(radioPermission == true){
						//console.dir(customField);
						//console.dir(customField.val());
						//console.dir(customField.attr('checked'));
						var radioPrev = currentTdElement.parent().find('.radioPermission').find('[name$="_prev"]').val();
						var typePrev = currentTdElement.parent().find('.typeCell').find('[name$="_prev"]').val();
						var agentPrev = currentTdElement.parent().find('.agentReference').find('[name$="_prev"]').val();
						var participantId = currentTdElement.parent().find('[name^="participantId"]').val();
						var url = "index.php?module=Orders&action=SaveParticipants&record="+getQueryVariable('record')+"&field=participantPermission&fieldvalue="+ajaxEditNewValue+"&radioprev="+radioPrev+"&typeprev="+typePrev+"&agentprev="+agentPrev+"&id="+participantId;
						AppConnector.request(url).then(
							function(data) {
								if(data.success) {
									//console.dir('sucess');
									currentTdElement.parent().find('.radioPermission').find('.edit').not('.hide').addClass('hide');
									currentTdElement.progressIndicator({'mode':'hide'});
									//console.dir(currentTdElement.parent().find('.radioName'));
									currentTdElement.parent().find('.radioName').data('prevValue', ajaxEditNewValue);
									fieldnameElement.data('prevValue', ajaxEditNewValue);
									fieldElement.data('selectedValue', ajaxEditNewValue);
									//console.dir(ajaxEditNewValue);
									//console.dir('prev: '+fieldnameElement.data('prevValue'));
									currentTdElement.parent().find('.radio1').html('No');
									currentTdElement.parent().find('.radio2').html('No');
									currentTdElement.parent().find('.radio3').html('No');
									currentTdElement.parent().find('.radio4').html('No');
									for(i=1; i<=4; i++){
										if(currentTdElement.parent().find('.radioInput'+i).attr('checked') == 'checked'){
											currentTdElement.parent().find('.radio'+i).html('Yes')
										}
									}
									currentTdElement.parent().find('.radioPermission').find('[name$="_prev"]').val(ajaxEditNewValue);
									currentTdElement.parent().find('.radioPermission > .value.hide').removeClass('hide');
									//detailViewValue.removeClass('hide');
									actionElement.show();
								}
							},
							function(error) {
								//console.dir('error');
							}
						);
					} else if(agentType == true){
						var radioPrev = currentTdElement.parent().find('.radioPermission').find('[name$="_prev"]').val();
						var typePrev = currentTdElement.parent().find('.typeCell').find('[name$="_prev"]').val();
						var agentPrev = currentTdElement.parent().find('.agentReference').find('[name$="_prev"]').val();
						var participantId = currentTdElement.parent().find('[name^="participantId"]').val();
						var selected = currentTdElement.find('.result-selected').html();
						var optionId = currentTdElement.find('.result-selected').attr('id').split('_')[4];
						//console.dir(optionId);
						var selectedId = currentTdElement.find('option:eq('+optionId+')').val();
						var url = "index.php?module=Orders&action=SaveParticipants&record="+getQueryVariable('record')+"&field=agentType&fieldvalue="+ajaxEditNewValue+"&radioprev="+radioPrev+"&typeprev="+typePrev+"&agentprev="+agentPrev+"&id="+participantId;
						//console.dir(url);
						AppConnector.request(url).then(
							function(data) {
								if(data.success) {
									//console.dir('sucess');
									currentTdElement.progressIndicator({'mode':'hide'});
									fieldnameElement.data('prevValue', ajaxEditNewValue);
									currentTdElement.find('.edit').find('[name$="_prev"]').val(ajaxEditNewValue);
									//console.dir(ajaxEditNewValue);
									//console.dir(fieldnameElement.data('prevValue'));
									detailViewValue.html(selected);
									detailViewValue.removeClass('hide');
									actionElement.show();
								}
							},
							function(error) {
								//console.dir('error');
							}
						);
					}
				}
				else{
					//saving for normal fields
					console.dir("normal field");
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
					if(fieldElement.hasClass('stopField')){
						var selected = currentTdElement.find('.result-selected').html();
						var splitField = fieldName.split('_');
						var stopId = currentTdElement.closest('table').find('input:hidden[name^="stop_id"]').val();
						if(fieldElement.is('input:checkbox')){
							if(fieldElement.is(':checked')){
								fieldValue='1';
							} else{
								fieldValue='0';
							}
						}
						var url = "index.php?module=Orders&action=SaveStopField&record="+getQueryVariable('record')+"&stopid="+stopId+"&field="+encodeURIComponent(splitField[1])+"&value="+encodeURIComponent(fieldValue);
						//console.dir(url);
						AppConnector.request(url).then(
							function(data) {
								if(data.success) {
									//console.dir('sucess');
									currentTdElement.progressIndicator({'mode':'hide'});
									fieldnameElement.data('prevValue', ajaxEditNewValue);
									//console.dir(ajaxEditNewValue);
									//console.dir(fieldnameElement.data('prevValue'));
									if(fieldElement.is('input:checkbox')) {
										if(ajaxEditNewValue == 0) {
											ajaxEditNewValue = 'No';
										} else {
											ajaxEditNewValue = 'Yes';
										}
										fieldElement = fieldElement.filter('[type="checkbox"]');
									}
									detailViewValue.html(ajaxEditNewValue);
									detailViewValue.removeClass('hide');
									actionElement.show();
								}
							},
							function(error) {
								console.dir('error');
							}
						);
					} else{
						thisInstance.saveFieldValues(fieldNameValueMap).then(function(response) {
							var postSaveRecordDetails = response.result;
							currentTdElement.progressIndicator({'mode':'hide'});
							detailViewValue.removeClass('hide');
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
						);
					}
				}
			}
		};

		jQuery(document).on('click','*', saveHandler);
	 },

	/**
	* Function to get records according to ticket status
	*/
	registerStatusChangeEventForWidget : function(){
		var thisInstance = this;
		jQuery('[name="ticketstatus"],[name="orderstaskstatus"],[name="orderstaskprogress"]').on('change',function(e){
            var picklistName = this.name;
			var statusCondition = {};
			var params = {};
			var currentElement = jQuery(e.currentTarget);
			var summaryWidgetContainer = currentElement.closest('.summaryWidgetContainer');
			var widgetDataContainer = summaryWidgetContainer.find('.widget_contents');
			var referenceModuleName = widgetDataContainer.find('[name="relatedModule"]').val();
			var recordId = thisInstance.getRecordId();
			var module = app.getModuleName();
			var selectedStatus = currentElement.find('option:selected').text();
			if(selectedStatus != "Select Status" && referenceModuleName == "HelpDesk"){
				statusCondition['vtiger_troubletickets.status'] = selectedStatus;
				params['whereCondition'] = statusCondition;
			} else if(selectedStatus != app.vtranslate('JS_LBL_SELECT_STATUS') && referenceModuleName == "OrdersTask" && picklistName == 'orderstaskstatus'){
				statusCondition['vtiger_orderstask.orderstaskstatus'] = selectedStatus;
				params['whereCondition'] = statusCondition;
			}
            else if(selectedStatus != app.vtranslate('JS_LBL_SELECT_PROGRESS') && referenceModuleName == "OrdersTask" && picklistName == 'orderstaskprogress'){
				statusCondition['vtiger_orderstask.orderstaskprogress'] = selectedStatus;
				params['whereCondition'] = statusCondition;
			}

			params['record'] = recordId;
			params['view'] = 'Detail';
			params['module'] = module;
			params['page'] = widgetDataContainer.find('[name="page"]').val();
			params['limit'] = widgetDataContainer.find('[name="pageLimit"]').val();
			params['relatedModule'] = referenceModuleName;
			params['mode'] = 'showRelatedRecords';
			AppConnector.request(params).then(
				function(data) {
					widgetDataContainer.html(data);
				}
			);
	   })
	},

	/**
	 * Function to add module related record from summary widget
	 */
	registerEventForAddingModuleRelatedRecordFromSummaryWidget : function(){
		var thisInstance = this;
		jQuery('#createOrdersMileStone,#createOrdersTask').on('click',function(e){
			var currentElement = jQuery(e.currentTarget);
			var summaryWidgetContainer = currentElement.closest('.summaryWidgetContainer');
			var widgetDataContainer = summaryWidgetContainer.find('.widget_contents');
			var referenceModuleName = widgetDataContainer.find('[name="relatedModule"]').val();
			var quickcreateUrl = currentElement.data('url');
			var parentId = thisInstance.getRecordId();
			var quickCreateParams = {};
			var relatedField = currentElement.data('parentRelatedField');
			var moduleName = currentElement.closest('.widget_header').find('[name="relatedModule"]').val();
			var relatedParams = {};
			relatedParams[relatedField] = parentId;

			var postQuickCreateSave = function(data) {
				thisInstance.postSummaryWidgetAddRecord(data,currentElement);
				if(referenceModuleName == "OrdersTask"){
					thisInstance.loadModuleSummary();
				}
			};

			if(typeof relatedField != "undefined"){
				quickCreateParams['data'] = relatedParams;
			}
			quickCreateParams['noCache'] = true;
			quickCreateParams['callbackFunction'] = postQuickCreateSave;
			var progress = jQuery.progressIndicator();
			var headerInstance = new Vtiger_Header_Js();
			headerInstance.getQuickCreateForm(quickcreateUrl, moduleName,quickCreateParams).then(function(data){
				headerInstance.handleQuickCreateData(data,quickCreateParams);
				progress.progressIndicator({'mode':'hide'});
			});
		})
	},

	/**
	 * Function to load module summary of Orderss
	 */
	loadModuleSummary : function(){
		var summaryParams = {};
		summaryParams['module'] = app.getModuleName();
		summaryParams['view'] = "Detail";
		summaryParams['mode'] = "showModuleSummaryView";
		summaryParams['record'] = jQuery('#recordId').val();

		AppConnector.request(summaryParams).then(
			function(data) {
				jQuery('.summaryView').html(data);
			}
		);
	},

	updateMoveRoleFieldsVisibility :function (){
		var moveRoles = jQuery('td[id$="fieldValue_moveroles_role"]');
		moveRoles.each(function(){
			var salesCommission = jQuery(this).closest('tbody').find('td[id$="fieldValue_sales_commission"]');
			var serviceProvider = jQuery(this).closest('tbody').find('td[id$="fieldValue_service_provider"]');
			if(jQuery(this).children(':first').text().indexOf("Salesperson") != -1)
			{
				salesCommission.closest('td').children().each(function(){
					jQuery(this).removeClass('hide');
				});
				salesCommission.closest('td').prev('td').children().each(function(){
					jQuery(this).removeClass('hide');
				});
			} else {
				salesCommission.closest('td').children().each(function(){
					jQuery(this).addClass('hide');
				});
				salesCommission.closest('td').prev('td').children().each(function(){
					jQuery(this).addClass('hide');
				});
			}
			if(jQuery(this).children(':first').text().indexOf("Billing Clerk") != -1)
			{
				serviceProvider.closest('td').children().each(function(){
					jQuery(this).addClass('hide');
				});
				serviceProvider.closest('td').prev('td').children().each(function(){
					jQuery(this).addClass('hide');
				});
			} else {
				serviceProvider.closest('td').children().each(function(){
					jQuery(this).removeClass('hide');
				});
				serviceProvider.closest('td').prev('td').children().each(function(){
					jQuery(this).removeClass('hide');
				});
			}

		});
	},
    hideShowProjectField: function(){
        var index = jQuery('#Orders_detailView_fieldValue_business_line').text().trim().indexOf('Work Space');
		if(index < 0){
			jQuery('#Orders_detailView_fieldLabel_orders_projectname').find('label').addClass('hide');
			jQuery('#Orders_detailView_fieldValue_orders_projectname').find('span').addClass('hide');
		}

    },
    hideShowReasonField: function(){
        var index = jQuery('#Orders_detailView_fieldValue_ordersstatus').text().trim().indexOf('Cancelled');
        if(index < 0){
            jQuery('#Orders_detailView_fieldLabel_order_reason').find('label').addClass('hide');
            jQuery('#Orders_detailView_fieldValue_order_reason').find('span').addClass('hide');
        }
    },

	registerSaveCancelOrder: function(){
	    jQuery(document).on("click","#saveCancelOrder",function(){
		var modal_action = jQuery("#modal_action").val();
		var message = (modal_action == "cancel") ? "Do you really want to cancel this order??" : "Do you really want to uncancel this order??"; //@TODO change this
		bootbox.confirm({
		    message: message,
		    buttons: { confirm: { label: 'Yes', }, cancel: { label: 'No', } },
		    callback: function (result) {
			if(result){
			    var order_id = jQuery("#modalordersid").val();
			    var reason = (modal_action == "cancel") ? jQuery("#cancel_reason option:selected").text() : jQuery("#uncancel_reason").val();
			    var datetime = jQuery("#modaldatetime").val();
			    var user_id = jQuery("#modaluserid").val();

			    var url = "index.php?module=Orders&action=SaveCancel&order_id="+order_id+"&reason="+reason+"&datetime="+datetime+"&user_id="+user_id+"&modal_action="+modal_action;
			    AppConnector.request(url).then(function(data) {
				var data = eval(data);
				if(data.success && data.result == "OK") {
				    app.hideModalWindow();
				    location.reload();
				}else{
				    console.dir(data);
				}
			    });
			}else{
			    app.hideModalWindow();
			}
		    }
		});
	    });
	},
	registerCancelOrderShowModalEvent: function(){
	    jQuery(document).on("click","#showUnCancelModal",function(){
		params = { 'module': 'Orders', 'view': 'ShowModals', 'mode': 'showCancelModal', 'modalaction' : 'uncancel', 'order_id': jQuery("#recordId").val(), };

		AppConnector.request(params).then(
		    function (data) {
			app.showModalWindow(data, function (data) { });
		});
	    });
	    jQuery(document).on("click","#showCancelModal",function(){
		params = { 'module': 'Orders', 'view': 'ShowModals', 'mode': 'showCancelModal', 'modalaction' : 'cancel', 'order_id': jQuery("#recordId").val(), };

		AppConnector.request(params).then(
		    function (data) {
			app.showModalWindow(data, function (data) { });
		});
	    });
	},
	toggleMilitaryFields: function(state) {
		var toggleFields = [
			'LBL_MILITARY_INFORMATION',
			'LBL_MILITARY_POST_MOVE_SURVEY'
		];

		jQuery.each( toggleFields, function( key, value ) {
			if(state == 'show') {
				jQuery('[name="' + value + '"]').removeClass('hide');
			} else {
				jQuery('[name="' + value + '"]').addClass('hide');
			}
		});
	},
        registerHideMilitaryFields: function(){
            var index = jQuery('#Orders_detailView_fieldValue_billing_type').text().trim().indexOf('Military');
            if(index < 0){
            	this.toggleMilitaryFields('hide');
            }
        },
	formatedDate: function() {
	    var d = new Date(),
		month = '' + (d.getMonth() + 1),
		day = '' + d.getDate(),
		year = d.getFullYear();

	    if (month.length < 2) month = '0' + month;
	    if (day.length < 2) day = '0' + day;

	    return [year, month, day].join('-');
	},
	registerActivityClicks: function(){
	    var thisInstance = this;
	    jQuery(document).on("click","span.well.squeezedWell",function(){
		var transfieldname = jQuery(this).data("transfieldname");
		var fieldname = jQuery(this).data("fieldname");
		var addURL = "";

		if(transfieldname == "Tasks Open" || fieldname == "LBL_TASKS_OPEN"){
		    addURL = '&search_params=[[["taskstatus","e","Not Started,Not Held"]]]';
		}else if(transfieldname == "Progress" || fieldname == "Progress"){
		    addURL = '&search_params=[[["taskstatus","e","In Progress,Pending Input,Deferred,Planned"]]]';
		}else if(transfieldname == "Overdue Tasks" || fieldname == "LBL_TASKS_DUE"){
		    var today = thisInstance.formatedDate();
		    addURL = '&search_params=[[["due_date","l","'+today+'"]]]';
		}else if(transfieldname == "Tasks Completed" || fieldname == "LBL_TASKS_COMPLETED"){
		    addURL = '&search_params=[[["taskstatus","e","Completed,Held"]]]';
		}else if(transfieldname == "High Priority" || fieldname == "LBL_TASKS_HIGH"){
		    addURL = '&search_params=[[["taskpriority","e","High"]]]';
		}else if(transfieldname == "Medium Priority" || fieldname == "LBL_TASKS_NORMAL"){
		    addURL = '&search_params=[[["taskpriority","e","Medium"]]]';
		}else if(transfieldname == "Low Priority" || fieldname == "LBL_TASKS_LOW"){
		    addURL = '&search_params=[[["taskpriority","e","Low"]]]';
		}else if(transfieldname == "No Priority " || fieldname == "LBL_TASKS_OTHER"){
		    addURL = '&search_params=[[["taskpriority","n","High,Low,Medium"]]]';
		}

		window.location = jQuery("ul.relatedLists").find('li[data-label-key="Activities"]').find('a').attr('href') + addURL;
	    });
	},
	registerEvents : function(){
		var detailContentsHolder = this.getContentHolder();
		var thisInstance = this;
		this._super();

		detailContentsHolder.on('click','.moreRecentMilestones', function(){
			var recentMilestonesTab = thisInstance.getTabByLabel(thisInstance.detailViewRecentMileStonesLabel);
			recentMilestonesTab.trigger('click');
		});

		detailContentsHolder.on('click','.moreRecentTickets', function(){
			var recentTicketsTab = thisInstance.getTabByLabel(thisInstance.detailViewRecentTicketsTabLabel);
			recentTicketsTab.trigger('click');
		});

		detailContentsHolder.on('click','.moreRecentTasks', function(){
			var recentTasksTab = thisInstance.getTabByLabel(thisInstance.detailViewRecentTasksTabLabel);
			recentTasksTab.trigger('click');
		});
		this.initializeAddressAutofill('Orders');
		this.updateMoveRoleFieldsVisibility();
        this.hideShowProjectField();
        this.hideShowReasonField();
		var common = new Valuation_Common_Js();
		common.registerEvents(false, 'Orders');
		this.registerCancelOrderShowModalEvent();
		this.registerSaveCancelOrder();
		this.registerHideMilitaryFields();
		this.registerActivityClicks();
	}



});

function getQueryVariable(variable) {
	var query = window.location.search.substring(1);
	var vars = query.split("&");
	for (var i=0; i<vars.length; i++) {
		var pair = vars[i].split("=");
		if(pair[0] == variable) {return pair[1];}
	}
	return(false);
}

//On Page Load.
//Doesn't appear to be necessary and binds events twice. Commenting out for now.
// jQuery(document).ready(function() {
// 	//Vtiger_Index_Js.registerEvents();
// });
