/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

Vtiger_Detail_Js("Opportunities_Detail_Js",{

    //Holds detail view instance
    detailCurrentInstance : false,

    registerSTS : function(registerSTSUrl, buttonElement){
        var instance = this.detailCurrentInstance;

         var progressIndicatorElement = jQuery.progressIndicator({
                'position' : 'html',
                'blockInfo' : {
                    'enabled' : true
                }
            });
            var params = {
                module: 'Opportunities',
                action: 'RegisterSTSAutoCheck',
                recordId: instance.getRecordId()
            };
            AppConnector.request(params).then(
                function(data) {
                    if(data.success) {
                        if(data.result == 'Skip'){
                            var params = {
                                module: 'Opportunities',
                                action: 'RegisterSTS',
                                recordId: instance.getRecordId()
                            };
                            AppConnector.request(params).then(
                                function(data) {
                                    if(data.success) {
                                        if(data.result == 'Success'){
                                            bootbox.alert(data.result, function(){
                                                location.reload();
                                            });
                                        }else{
                                            bootbox.alert(data.result);
                                        }
                                    }
                                    progressIndicatorElement.progressIndicator({
                                        'mode' : 'hide'
                                    });
                                },
                                function(message, thrown, code) {
                                    bootbox.alert('An unknown error occurred attempting to register STS (ERR' + code + ').');
                                    progressIndicatorElement.progressIndicator({
                                        'mode' : 'hide'
                                    });
                                }
                            );
                        } else {
                            var message = "Less than 7 days to auto <span style='text-decoration:underline'>Load From</span> date for the following quote(s): Select ‘Yes’ and the Rush Request Fee of $100.00 will be added to the quote charge per vehicle.  Else, select ‘No’ and contact autoquote@sirva.com for service options<br /><br />";
                            message += '<table class="table table-bordered detailview-table  block_LBL_AUTOSPOTQUOTEDETAILS"><thead><tr><th class="blockHeader">Make</th><th class="blockHeader">Model</th><th class="blockHeader">Year</th><th class="blockHeader">Load Date</th><th class="blockHeader">Apply Rush Fee?</th></tr>';
                            jQuery.each(data.result, function(index, value){
                                message += '<tr><td>'+value.make +'</td><td>'+value.model+'</td><td>'+value.year+'</td><td>'+value.load_from+'</td><td>Yes<input type="radio" name="'+index+'" value="1"/> No<input name="'+index+'" type="radio" checked value="0" /></td></tr>';
                            });
                            bootbox.confirm(message, function(result){
                                if(result){
                                    var autoInfo = jQuery(this).find('input').serialize();

                                    var params = {
                                        module: 'Opportunities',
                                        action: 'RegisterSTS',
                                        recordId: instance.getRecordId(),
                                        autoInfo: autoInfo,
                                    };
                                    AppConnector.request(params).then(
                                        function(data) {
                                            if(data.success) {
                                                if(data.result == 'Success'){
                                                    bootbox.alert(data.result, function(){
                                                        location.reload();
                                                    });
                                                }else{
                                                    bootbox.alert(data.result);
                                                }
                                            }
                                            progressIndicatorElement.progressIndicator({
                                                'mode' : 'hide'
                                            });
                                        },
                                        function(message, thrown, code){
                                            bootbox.alert('An unknown error occurred attempting to register STS (ERR' + code + ').');
                                            progressIndicatorElement.progressIndicator({
                                                'mode' : 'hide'
                                            });
                                        }
                                    );
                                }
                            });
                            progressIndicatorElement.progressIndicator({
                                'mode' : 'hide'
                            });
                        }

                    }
                },
                function(error,err){
                    //location.reload();
                }
            );
        /*var progressIndicatorElement = jQuery.progressIndicator({
            'position' : 'html',
            'blockInfo' : {
                'enabled' : true
            }
        });
        AppConnector.request(registerSTSUrl).then(
            function(data) {
                if(data) {
                    Opportunities_Detail_Js.cache = data;
                    app.showModalWindow(data,function(){
                        Opportunities_Detail_Js.registerSTSEvents();
                        //console.dir(jQuery('.quickCreateContent'));
                        var editInstance = Vtiger_Edit_Js.getInstance();
                        editInstance.registerBasicEvents(jQuery('.quickCreateContent'));
                        //jQuery('.relatedPopup').removeClass('relatedPopup cursorPointer').find('.icon-search').removeClass('icon-search');
                    });
                    jQuery('#submitSTSBtn').on('click', function(){
                        var progressIndicatorElement = jQuery.progressIndicator({
                            'position' : 'html',
                            'blockInfo' : {
                                'enabled' : true
                            }
                        });
                        var params = {
                            module: 'Opportunities',
                            action: 'RegisterSTS',
                            stsInfo: jQuery('form[name="RegisterSTS"]').serializeArray(),
                            recordId: instance.getRecordId()
                        };
                        AppConnector.request(params).then(
                            function(data) {
                                if(data.success) {
                                    if(data.result == 'Success'){
                                        bootbox.alert(data.result, function(){
                                            location.reload();
                                        });
                                    }
                                    bootbox.alert(data.result);
                                }
                                progressIndicatorElement.progressIndicator({
                                    'mode' : 'hide'
                                })
                            }
                        );
                    });
                    progressIndicatorElement.progressIndicator({
                        'mode' : 'hide'
                    })
                }
            },
            function(error,err){
                progressIndicatorElement.progressIndicator({
                    'mode' : 'hide'
                })
            }
        );*/
    },

    registerSTSEvents : function(){
        var popUpForm = jQuery('form[name="RegisterSTS"]');
        //console.dir('register STS');
        //console.dir(popUpForm);
        Opportunities_Detail_Js.CBSIndEvents();
        Opportunities_Detail_Js.splitBookingEvents();
        Opportunities_Detail_Js.creditCheckEvents();
        popUpForm.find('input:checkbox[name="cbs_ind"]').trigger('change');
    },

    // registerOPReportsEvents : function(){
    //     //console.dir('event!');
    //     jQuery('#reportsContent').find('button').each(function (){
    //         //console.dir(jQuery(this));
    //         jQuery(this).on('click', function () {
    //             jQuery(this).closest('td').progressIndicator();
    //             jQuery(this).addClass('hide');
    //             var reportURL = 'index.php?module=OPList&action=GetReportOpList&reportId=' + jQuery(this).attr('name') + '&reportName=' + encodeURIComponent(jQuery(this).html());
    //             reportURL = reportURL + '&wsdlURL=' + jQuery('input[name="wsdlURL"]').val();
    //             var formData = jQuery.param(jQuery('#EditView').serializeFormData());
    //             var index = formData.indexOf('&record=');
    //             var urlAppend = formData.substring(index, formData.length - 1);
    //             reportURL = reportURL + urlAppend;
    //             //console.dir(reportURL);
    //             AppConnector.request(reportURL).then(
    //                 function (data) {
    //                     if (data.success) {
    //                         window.location.href = 'index.php?module=Documents&view=Detail&record=' + data.result;
    //                         //console.log(data.result);
    //                     }
    //                 },
    //                 function (error) {
    //                 }
    //             );
    //         });
    //     });
    // },
    //
    // getOpReports : function(reportsUrl){
    //     var instance = Opportunities_Detail_Js.detailCurrentInstance;
    //     var progressIndicatorElement = jQuery.progressIndicator({
    //         'position' : 'html',
    //         'blockInfo' : {
    //             'enabled' : true
    //         }
    //     });
    //     AppConnector.request(reportsUrl).then(
    //         function(data) {
    //             if(data) {
    //                 app.showModalWindow(
    //                     data.result,
    //                     function(){
    //                         Opportunities_Detail_Js.registerOPReportsEvents();
    //                     },
    //                     {inline:'true', width:'300px', height:'auto', top:'50%', 'text-align':'center', padding:'5px', href:'#reportContent'}
    //                 );
    //             }
    //             progressIndicatorElement.progressIndicator({
    //                 'mode' : 'hide'
    //             });
    //         },
    //         function(error,err){
    //             progressIndicatorElement.progressIndicator({
    //                 'mode' : 'hide'
    //             });
    //         }
    //     );
    // },

    splitBookingEvents : function(){
        var popUpForm = jQuery('form[name="RegisterSTS"]');
        var bookerSplit = popUpForm.find('input[name="booker_split"]');
        var originSplit = popUpForm.find('input[name="origin_split"]');
        originSplit.on('change', function(){
            var originSplitVal = originSplit.val();
            var bookerSplitVal = bookerSplit.val();
            if(originSplitVal>100){
                originSplitVal = 100;
                originSplit.val(100);
            } else if(originSplitVal<0){
                originSplitVal = 0;
                originSplit.val(0);
            }
            bookerSplit.val(100 - originSplitVal);
        });
        bookerSplit.on('change', function(){
            var originSplitVal = originSplit.val();
            var bookerSplitVal = bookerSplit.val();
            if(bookerSplitVal>100){
                bookerSplitVal = 100;
                bookerSplit.val(100);
            } else if(bookerSplitVal<0){
                bookerSplitVal = 0;
                bookerSplit.val(0);
            }
            originSplit.val(100 - bookerSplitVal);
        });
    },

    CBSIndEvents : function(){
        var popUpForm = jQuery('form[name="RegisterSTS"]');
        var cbsField = jQuery('input:checkbox[name="cbs_ind"]');
        cbsField.on('change', function(){
            if(!cbsField.prop('checked')){
                if(!jQuery('#Opportunities_editView_fieldName_credit_check').hasClass('hide')){
                    jQuery('#Opportunities_editView_fieldName_credit_check').addClass('hide');
                    jQuery('#Opportunities_editView_fieldName_credit_check').prop('checked', false);
                    jQuery('#Opportunities_editView_fieldName_credit_check').trigger('change');
                    jQuery('#Opportunities_editView_fieldName_credit_check').parent('td').prev('td').find('label').addClass('hide');
                }
            } else{
                if(jQuery('#Opportunities_editView_fieldName_credit_check').hasClass('hide')){
                    jQuery('#Opportunities_editView_fieldName_credit_check').removeClass('hide');
                    jQuery('#Opportunities_editView_fieldName_credit_check').parent('td').prev('td').find('label').removeClass('hide');
                }
            }
        });
    },
	intlQuote : function(registerUrl, buttonElement){
		var instance = Opportunities_Detail_Js.detailCurrentInstance;
		var progressIndicatorElement = jQuery.progressIndicator({
			'position' : 'html',
			'blockInfo' : {
				'enabled' : true
			}
		});
		AppConnector.request(registerUrl).then(
			function(data) {
				if(data) {
					app.showModalWindow(data,function(){
						Opportunities_Detail_Js.registerIntlEvents();
					});
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

	registerOPReportsEvents : function(){
		//console.dir('event!');
		 jQuery('#reportsContent').find('button').each(function (){
			//console.dir(jQuery(this));
			jQuery(this).on('click', function () {
				var windowBox = jQuery(this);
				var progressIndicator = jQuery(this).closest('td').progressIndicator();
				jQuery(this).addClass('hide');
				var reportURL = 'index.php?module=OPList&action=GetReportOpList&reportId=' + jQuery(this).attr('name') + '&reportName=' + encodeURIComponent(jQuery(this).html());
				reportURL = reportURL + '&wsdlURL=' + jQuery('input[name="wsdlURL"]').val();
				var formData = jQuery.param(jQuery('#EditView').serializeFormData());
				var index = formData.indexOf('&record=');
				var urlAppend = formData.substring(index, formData.length - 1);
				reportURL = reportURL + urlAppend;
				//console.dir(reportURL);
				AppConnector.request(reportURL).then(
					function (data) {
						if (data.success) {
							window.location.href = 'index.php?module=Documents&view=Detail&record=' + data.result;
							//console.log(data.result);
						} else {
							progressIndicator.hide();
							windowBox.closest('tbody').append('<tr><td>ERROR: '+data.error.message+'</td></tr>');
						}
					},
					function (error) {
					}
				);
			});
		});
	},

	getOpReports : function(reportsUrl){
		var instance = Opportunities_Detail_Js.detailCurrentInstance;
		var progressIndicatorElement = jQuery.progressIndicator({
			'position' : 'html',
			'blockInfo' : {
				'enabled' : true
			}
		});
		AppConnector.request(reportsUrl).then(
			function(data) {
				if(data) {
					app.showModalWindow(
						data.result,
						function(){
							Opportunities_Detail_Js.registerOPReportsEvents();
						},
						{inline:'true', width:'300px', height:'auto', top:'50%', 'text-align':'center', padding:'5px', href:'#reportContent'}
					);
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

    creditCheckEvents : function(){
        var popUpForm = jQuery('form[name="RegisterSTS"]');
        var creditCheck = jQuery('input:checkbox[name="credit_check"]');
        creditCheck.on('change', function(){
            if(!creditCheck.prop('checked')){
                if(!jQuery('#Opportunities_editView_fieldName_contact_name').closest('tr').hasClass('hide')){
                    jQuery('#Opportunities_editView_fieldName_contact_name').closest('tr').addClass('hide');
                }
                if(!jQuery('#Opportunities_editView_fieldName_ref_number').closest('tr').hasClass('hide')){
                    jQuery('#Opportunities_editView_fieldName_ref_number').closest('tr').addClass('hide');
                }
            } else{
                if(jQuery('#Opportunities_editView_fieldName_contact_name').closest('tr').hasClass('hide')){
                    jQuery('#Opportunities_editView_fieldName_contact_name').closest('tr').removeClass('hide');
                }
                if(jQuery('#Opportunities_editView_fieldName_ref_number').closest('tr').hasClass('hide')){
                    jQuery('#Opportunities_editView_fieldName_ref_number').closest('tr').removeClass('hide');
                }
            }
        });
    },
    intlQuote : function (registerUrl, buttonElement) {
        var instance = Opportunities_Detail_Js.detailCurrentInstance;
        instance.intlQuote(registerUrl, buttonElement);

    }
},{

    detailViewRecentContactsLabel : 'Contacts',
    detailViewRecentProductsTabLabel : 'Products',
    sts: null,

    //constructor
    init : function() {
        this._super();
        Opportunities_Detail_Js.detailCurrentInstance = this;
    },

    /*
     //to be considered for fixing when you click the detail/summary related list links
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
     console.dir("PJAX DONE!");
     Vtiger_RelatedList_Js.registerDuplicateButton();
     thisInstance.registerEvents();
     },
     function(){

     }
     );

     return aDeferred.promise();
     },
     */

    /**
     * Function which will register all the events
     */

    ajaxEditHandling : function(currentTdElement) {
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
            //Before saving ajax edit values we need to check if the value is changed then only we have to save
            if(previousValue == ajaxEditNewValue) {
                editElement.addClass('hide');
                detailViewValue.removeClass('hide');
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


                if(agentType==true || radioPermission==true){
                    //saving for custom fields
                    customField = currentTdElement.find('[name="'+fieldName+'"]');
                    currentTdElement.progressIndicator({'mode':'hide'});
                    currentTdElement.progressIndicator();
                    editElement.addClass('hide');
                    if(radioPermission == true){
                        var radioPrev = currentTdElement.parent().find('.radioPermission').find('[name$="_prev"]').val();
                        var typePrev = currentTdElement.parent().find('.typeCell').find('[name$="_prev"]').val();
                        var agentPrev = currentTdElement.parent().find('.agentReference').find('[name$="_prev"]').val();
                        var participantId = currentTdElement.parent().find('[name^="participantId"]').val();
                        var url = "index.php?module=Opportunities&action=SaveParticipants&record="+getQueryVariable('record')+"&field=participantPermission&fieldvalue="+ajaxEditNewValue+"&radioprev="+radioPrev+"&typeprev="+typePrev+"&agentprev="+agentPrev+"&id="+participantId;
                        AppConnector.request(url).then(
                            function(data) {
                                if(data.success) {
                                    currentTdElement.parent().find('.radioPermission').find('.edit').not('.hide').addClass('hide');
                                    currentTdElement.progressIndicator({'mode':'hide'});
                                    currentTdElement.parent().find('.radioName').data('prevValue', ajaxEditNewValue);
                                    fieldnameElement.data('prevValue', ajaxEditNewValue);
                                    fieldElement.data('selectedValue', ajaxEditNewValue);
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
                        var selectedId = currentTdElement.find('option:eq('+optionId+')').val();
                        var url = "index.php?module=Opportunities&action=SaveParticipants&record="+getQueryVariable('record')+"&field=agentType&fieldvalue="+ajaxEditNewValue+"&radioprev="+radioPrev+"&typeprev="+typePrev+"&agentprev="+agentPrev+"&id="+participantId;
                        AppConnector.request(url).then(
                            function(data) {
                                if(data.success) {
                                    currentTdElement.progressIndicator({'mode':'hide'});
                                    fieldnameElement.data('prevValue', ajaxEditNewValue);
                                    currentTdElement.find('.edit').find('[name$="_prev"]').val(ajaxEditNewValue);
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
        }

        jQuery(document).on('click','*', saveHandler);
    },

    registerPhoneTypeChange : function() {
        jQuery('select[name="origin_phone1_type"]').on('change', function() {
            var selectedOption = jQuery('select[name="origin_phone1_type"]').val();
            var extension = jQuery('select[name="origin_phone1_ext"]').val();
            if(selectedOption == 'Work'){
                if(jQuery('#originPhone1Span').hasClass('hide') && extension){
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

        if(currentTdElement.hasClass('agentType')){
            var agentType = true;
        }

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
        if(!extElement.hasClass('hide')){
            extElement.addClass('hide');
        }

        if((fieldName == 'origin_phone1' && jQuery('select[name="origin_phone1_type"]').val() == 'Work') || (fieldName == 'origin_phone2' && jQuery('select[name="origin_phone2_type"]').val() == 'Work') || (fieldName == 'destination_phone1' && jQuery('select[name="destination_phone1_type"]').val() == 'Work') || (fieldName == 'destination_phone2' && jQuery('select[name="destination_phone2_type"]').val() == 'Work')){
            jQuery('input[name="'+fieldName+'_ext"]').parent().removeClass('hide');
        }

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
                if((fieldName == 'origin_phone1' && jQuery('select[name="origin_phone1_type"]').val() != 'Work') || (fieldName == 'origin_phone2' && jQuery('select[name="origin_phone2_type"]').val() != 'Work') || (fieldName == 'destination_phone1' && jQuery('select[name="destination_phone1_type"]').val() != 'Work') || (fieldName == 'destination_phone2' && jQuery('select[name="destination_phone2_type"]').val() != 'Work')){
                    if(!jQuery('input[name="'+fieldName+'_ext"]').parent().hasClass('hide')){
                        jQuery('input[name="'+fieldName+'_ext"]').parent().addClass('hide');
                    }
                }
                detailViewValue.removeClass('hide');
                var extId = editElement.find('input:hidden').attr('id');
                if(extId){
                    if((extId.indexOf('origin_phone1') >= 0 && jQuery('select[name="origin_phone1_type"]').val() == 'Work') || (extId.indexOf('origin_phone2') >= 0 && jQuery('select[name="origin_phone2_type"]').val() == 'Work') || (extId.indexOf('destination_phone1') >= 0 && jQuery('select[name="destination_phone1_type"]').val() == 'Work') || (extId.indexOf('destination_phone2') >= 0 && jQuery('select[name="destination_phone2_type"]').val() == 'Work')){
                        extElement.removeClass('hide');
                    }
                }
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
                    var url = "index.php?module=Opportunities&action=SaveStopField&record="+getQueryVariable('record')+"&stopid="+stopId+"&field="+encodeURIComponent(splitField[1])+"&value="+encodeURIComponent(fieldValue);
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
                } else if(agentType==true || radioPermission==true){
                    //saving for custom fields
                    customField = currentTdElement.find('[name="'+fieldName+'"]');
                    currentTdElement.progressIndicator({'mode':'hide'});
                    currentTdElement.progressIndicator();
                    editElement.addClass('hide');
                    if(radioPermission == true){
                        var radioPrev = currentTdElement.parent().find('.radioPermission').find('[name$="_prev"]').val();
                        var typePrev = currentTdElement.parent().find('.typeCell').find('[name$="_prev"]').val();
                        var agentPrev = currentTdElement.parent().find('.agentReference').find('[name$="_prev"]').val();
                        var participantId = currentTdElement.parent().find('[name^="participantId"]').val();
                        var url = "index.php?module=Opportunities&action=SaveParticipants&record="+getQueryVariable('record')+"&field=participantPermission&fieldvalue="+ajaxEditNewValue+"&radioprev="+radioPrev+"&typeprev="+typePrev+"&agentprev="+agentPrev+"&id="+participantId;
                        AppConnector.request(url).then(
                            function(data) {
                                if(data.success) {
                                    currentTdElement.parent().find('.radioPermission').find('.edit').not('.hide').addClass('hide');
                                    currentTdElement.progressIndicator({'mode':'hide'});
                                    currentTdElement.parent().find('.radioName').data('prevValue', ajaxEditNewValue);
                                    fieldnameElement.data('prevValue', ajaxEditNewValue);
                                    fieldElement.data('selectedValue', ajaxEditNewValue);
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
                        var selectedId = currentTdElement.find('option:eq('+optionId+')').val();
                        var url = "index.php?module=Opportunities&action=SaveParticipants&record="+getQueryVariable('record')+"&field=agentType&fieldvalue="+ajaxEditNewValue+"&radioprev="+radioPrev+"&typeprev="+typePrev+"&agentprev="+agentPrev+"&id="+participantId;
                        AppConnector.request(url).then(
                            function(data) {
                                if(data.success) {
                                    currentTdElement.progressIndicator({'mode':'hide'});
                                    fieldnameElement.data('prevValue', ajaxEditNewValue);
                                    currentTdElement.find('.edit').find('[name$="_prev"]').val(ajaxEditNewValue);
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
                } else{
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
        }

        jQuery(document).on('click','*', saveHandler);
    },

    registerChangeMoveType : function() {
        var thisInstance = this;
        jQuery('select[name="move_type"]').on('change', function(){
            var moveType = jQuery('select[name="move_type"]').find('option:selected').val();
            jQuery('select[name="business_line"]').find('option:selected').prop('selected', false);
            switch(moveType) {
                case 'Local Canada':
                case 'Local US':
                    jQuery('select[name="business_line"]').find('option[value="Local Move"]').prop('selected', true).attr('selected', 'selected');
                    break;
                case 'Interstate':
                case 'Inter-Provincial':
                case 'Cross Border':
                    jQuery('select[name="business_line"]').find('option[value="Interstate Move"]').prop('selected', true).attr('selected', 'selected');
                    break;
                case 'O&I':
                    jQuery('select[name="business_line"]').find('option[value="Commercial Move"]').prop('selected', true).attr('selected', 'selected');
                    break;
                case 'Intrastate':
                case 'Intra-Provincial':
                    jQuery('select[name="business_line"]').find('option[value="Intrastate Move"]').prop('selected', true).attr('selected', 'selected');
                    break;
                case 'Alaska':
                case 'Hawaii':
                case 'International':
                    jQuery('select[name="business_line"]').find('option[value="International Move"]').prop('selected', true).attr('selected', 'selected');
                    break;
                default:
                    break;
            }
            jQuery('select[name="business_line"]').trigger('liszt:updated').trigger('change');
            thisInstance.saveItem(jQuery('select[name="business_line"]'));
            //jQuery('select[name="business_line"]').trigger('mouseup').trigger('change').trigger('click');
            //switch to set origin/destination country based on move type
            switch(moveType) {
                case 'Local Canada':
                case 'Inter-Provincial':
                case 'Intra-Provincial':
                    jQuery('select[name="origin_country"]').find('option[value="Canada"]').prop('selected', true).attr('selected', 'selected');
                    jQuery('select[name="destination_country"]').find('option[value="Canada"]').prop('selected', true).attr('selected', 'selected');
                    if(jQuery('input[name="emailoptout"]').hasClass('hide')){
                        jQuery('input[name="emailoptout"]').removeClass('hide');
                        jQuery('input[name="emailoptout"]').closest('td').prev('td').find('label').removeClass('hide');
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
                        jQuery('input[name="emailoptout"]').closest('td').prev('td').find('label').addClass('hide');
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
            //update picklists
            jQuery('select[name="business_line"]').trigger('liszt:updated').trigger('change').trigger('change').trigger('click');
            jQuery('select[name="origin_country"]').trigger('liszt:updated').trigger('change').trigger('change').trigger('click');
            jQuery('select[name="destination_country"]').trigger('liszt:updated').trigger('change').trigger('change').trigger('click');
            thisInstance.saveItem(jQuery('select[name="business_line"]'));
            thisInstance.saveItem(jQuery('select[name="destination_country"]'));
            thisInstance.saveItem(jQuery('select[name="origin_country"]'));
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

    registerPhoneTypeEvents : function(){
        jQuery('select[name="origin_phone1_type"]').on('change', function() {
            var selectedOption = jQuery('select[name="origin_phone1_type"]').val();
            if(selectedOption == 'Work'){
                if(jQuery('#originPhone1Span').hasClass('hide')){
                    jQuery('#originPhone1Span').removeClass('hide');
                }
                if(jQuery('#originPhone1ValueSpan').hasClass('hide')){
                    jQuery('#originPhone1ValueSpan').removeClass('hide');
                    if(jQuery('input[name="origin_phone1_ext"]').val()){
                        jQuery('#originPhone1ValueSpan').html('Ext. '+jQuery('input[name="origin_phone1_ext"]').val());
                    }
                }
            }
            else{
                if(!jQuery('#originPhone1Span').hasClass('hide')){
                    jQuery('#originPhone1Span').addClass('hide');
                }
                if(!jQuery('#originPhone1ValueSpan').hasClass('hide')){
                    jQuery('#originPhone1ValueSpan').addClass('hide');
                }
            }
        });

        jQuery('select[name="origin_phone2_type"]').on('change', function() {
            var selectedOption = jQuery('select[name="origin_phone2_type"]').val();
            if(selectedOption == 'Work'){
                if(jQuery('#originPhone2Span').hasClass('hide')){
                    jQuery('#originPhone2Span').removeClass('hide');
                }
                if(jQuery('#originPhone2ValueSpan').hasClass('hide')){
                    jQuery('#originPhone2ValueSpan').removeClass('hide');
                    if(jQuery('input[name="origin_phone2_ext"]').val()){
                        jQuery('#originPhone2ValueSpan').html('Ext. '+jQuery('input[name="origin_phone2_ext"]').val());
                    }
                }
            }
            else{
                if(!jQuery('#originPhone2Span').hasClass('hide')){
                    jQuery('#originPhone2Span').addClass('hide');
                }
                if(!jQuery('#originPhone2ValueSpan').hasClass('hide')){
                    jQuery('#originPhone2ValueSpan').addClass('hide');
                }
            }
        });

        jQuery('select[name="destination_phone1_type"]').on('change', function() {
            var selectedOption = jQuery('select[name="destination_phone1_type"]').val();
            if(selectedOption == 'Work'){
                if(jQuery('#destinationPhone1Span').hasClass('hide')){
                    jQuery('#destinationPhone1Span').removeClass('hide');
                }
                if(jQuery('#destinationPhone1ValueSpan').hasClass('hide')){
                    jQuery('#destinationPhone1ValueSpan').removeClass('hide');
                    if(jQuery('input[name="destination_phone1_ext"]').val()){
                        jQuery('#destinationPhone1ValueSpan').html('Ext. '+jQuery('input[name="destination_phone1_ext"]').val());
                    }
                }
            }
            else{
                if(!jQuery('#destinationPhone1Span').hasClass('hide')){
                    jQuery('#destinationPhone1Span').addClass('hide');
                }
                if(!jQuery('#destinationPhone1ValueSpan').hasClass('hide')){
                    jQuery('#destinationPhone1ValueSpan').addClass('hide');
                }
            }
        });

        jQuery('select[name="destination_phone2_type"]').on('change', function() {
            var selectedOption = jQuery('select[name="destination_phone2_type"]').val();
            if(selectedOption == 'Work'){
                if(jQuery('#destinationPhone2Span').hasClass('hide')){
                    jQuery('#destinationPhone2Span').removeClass('hide');
                }
                if(jQuery('#destinationPhone2ValueSpan').hasClass('hide')){
                    jQuery('#destinationPhone2ValueSpan').removeClass('hide');
                    if(jQuery('input[name="destination_phone2_ext"]').val()){
                        jQuery('#destinationPhone2ValueSpan').html('Ext. '+jQuery('input[name="destination_phone2_ext"]').val());
                    }
                }
            }
            else{
                if(!jQuery('#destinationPhone2Span').hasClass('hide')){
                    jQuery('#destinationPhone2Span').addClass('hide');
                }
                if(!jQuery('#destinationPhone2ValueSpan').hasClass('hide')){
                    jQuery('#destinationPhone2ValueSpan').addClass('hide');
                }
            }
        });

    },

    /*
     *   Check if move type of opportunity and hide the estimate field if the type is Canada based value. Temp solution
     *   until pricing has been scoped out and development can be completed on the Canadian tariffs.
     */
    removeEstimatesByType: function() {
        var thisInstance = this;
        jQuery('#Opportunities_detailView_moreAction_Create_Estimate').hide();
        var dataUrl = "index.php?module=Opportunities&action=CheckMoveType&contact_id="+thisInstance.getRecordId();
        AppConnector.request(dataUrl).then(
            function(data) {
                if (data.success) {
                    if(data.result.move_type != 'Inter-Provincial' && data.result.move_type != 'Intra-Provincial') {
                        jQuery('[data-label-key="Estimates"]').show();
                        jQuery('#Opportunities_detailView_moreAction_Create_Estimate').show();
                    }
                }
            },
            function(error, err) {

            }
        );
    },

    toggleNationalAccountBlock: function() {
        var shipperType = jQuery('#Opportunities_detailView_fieldValue_shipper_type').find('span').html();
        if(shipperType) {
            if(shipperType.trim() == 'NAT') {
                jQuery('table[name="LBL_POTENTIALS_NATIONALACCOUNT"]').removeClass('hide');
            }
        }

        app.listenPostAjaxReady(function() {
            var shipperType = jQuery('#Opportunities_detailView_fieldValue_shipper_type').find('span').html();
            if(shipperType) {
                if(shipperType.trim()=='NAT') {
                    jQuery('table[name="LBL_POTENTIALS_NATIONALACCOUNT"]').removeClass('hide');
                }
            }
        });
    },

    registerTransitGuideEvent : function() {
        var instance = this;
        try {
            instance = Opportunities_Detail_Js.detailCurrentInstance;
        } catch (errMT) {
            //ignore this error
        }
        jQuery('button[name="transitGuide"]').on('click', function () {
            var load_date = jQuery('input[name="load_date"]').val();

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
                + '&record=' + instance.getRecordId();
            AppConnector.request(url).then(
                function(data) {
                    if(data.success) {
                        var message = 'Pick a Transit Guide date set:';
                        instance.showTransitGuideBox({'message': message, 'results': data.result}).then(
                            function (e) {
                                //they pressed something. and e should have the dates to use.
                                var url = 'index.php?module=Opportunities&action=SetTransitGuideDates'
                                    + '&deliver_date=' + e.deliver_date
                                    + '&deliver_to_date=' + e.deliver_to_date
                                    + '&load_date=' + e.load_date
                                    + '&load_to_date=' + e.load_to_date
                                    + '&record=' + instance.getRecordId();
                                AppConnector.request(url).then(
                                    function (data) {
                                        if (data.success) {
                                            //console.dir('updated');
                                            //chose to have setTG return the user formatted dates. instead of doing a page reload
                                            var elements = ['deliver_date', 'deliver_to_date', 'load_date', 'load_to_date'];
                                            var classNameStart = 'Opportunities_detailView_fieldValue_';
                                            for (var elm in elements) {
                                                var tempElm = jQuery('#' + classNameStart + elements[elm]).find('span');
                                                tempElm.each(function() {
                                                    if (jQuery(this).hasClass('value')) {
                                                        jQuery(this).html(data.result[elements[elm]]);
                                                    }
                                                });
                                            }
                                        } else {
                                            bootbox.alert("Error updating Opportunity dates: " + data.error.message);
                                        }
                                    },
                                    function (error) {
                                        console.dir('error');
                                    }
                                );
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

    registerIntlEvents : function(){

        var validData = function(e){
            var thisInstance = this;
            var formElement = $("#quickCreate");

            var invalidFields = formElement.data('jqv').InvalidFields;
            if(invalidFields.length > 0) {console.log('found');
                var fieldElement = invalidFields[0];
                var moduleBlock = jQuery(fieldElement).closest('div.accordion-body');
                moduleBlock.collapse('show');
                e.preventDefault();
                return;
            }
        }
        jQuery('form[name="QuickCreate"]').on('click','#submitQuoteBtn', function(event){
            event.preventDefault();
            var formElement = $("#quickCreate");

            //If the validation fails in the hidden Block, we should show that Block with error.
            var invalidFields = formElement.data('jqv').InvalidFields;
            if(invalidFields.length > 0) {
                var fieldElement = invalidFields[0];
                var moduleBlock = jQuery(fieldElement).closest('div.accordion-body');
                moduleBlock.collapse('show');
                e.preventDefault();
                return;
            }

            params = {
                'module' : 'Opportunities',
                'action': "RegisterQuote",
                'quoteInfo' : $('#quickCreate').serializeArray()
            }
            var progressIndicatorElement = jQuery.progressIndicator({
                'position' : 'html',
                'blockInfo' : {
                    'enabled' : true
                }
            });
            AppConnector.request(params).then(
                function(responseData){
                    progressIndicatorElement.progressIndicator({
                        'mode' : 'hide'
                    });
                    bootbox.alert("Quote successfully sent!", function() {
                        location.reload();
                    });
                },
                function(error){
                    progressIndicatorElement.progressIndicator({
                        'mode' : 'hide'
                    });
                }
            );
        });
        jQuery('form[name="QuickCreate"]').on('click','#saveQuote', function(event){
            event.preventDefault();

            var array = $('#quickCreate').serializeArray();
            var params = {};
            $.each(array,function(index,value){
                params[value.name] = value.value;
            });
            var progressIndicatorElement = jQuery.progressIndicator({
                'position' : 'html',
                'blockInfo' : {
                    'enabled' : true
                }
            });
            AppConnector.request(params).then(
                function(responseData){
                    console.log(responseData);

                    progressIndicatorElement.progressIndicator({
                        'mode' : 'hide'
                    });
                    if(responseData.success){
                        bootbox.alert("Quote saved!", function() {});
                    }
                    else{
                        bootbox.alert(responseData.error.message, function() {});
                    }
                },
                function(error){
                    if(error == 'parsererror'){
                        bootbox.alert("Quote saved!", function() {});
                    }
                    else{
                        bootbox.alert("Quote not saved!", function() {});
                    }
                    progressIndicatorElement.progressIndicator({
                        'mode' : 'hide'
                    });
                }
            );
        });
    },

    intlQuote : function(registerUrl, buttonElement){
        var instance = this;
        var errorElement = $('body').find('#convertLeadError');
        if(errorElement.length != '0') {
            var errorMsg = errorElement.val();
            var errorTitle = 'Error Detected';
            var params = {
                title: errorTitle,
                text: errorMsg,
                addclass: "convertLeadNotify",
                width: '35%',
                pnotify_after_open: function(){
                    $('#saveQuote').attr('disabled','disabled');
                    //instance.createConvertToLeadName();
                },
                pnotify_after_close: function(){
                    $('#saveQuote').removeAttr('disabled');
                }
            }
            Vtiger_Helper_Js.showPnotify(params);
        }
        else{
            var callBackFunction = function(){
                $('#quickCreate').validationEngine(app.validationEngineOptions);
                instance.registerIntlEvents();
            }
            var progressIndicatorElement = jQuery.progressIndicator({
                'position' : 'html',
                'blockInfo' : {
                    'enabled' : true
                }
            });
            AppConnector.request(registerUrl).then(
                function(data) {
                    if(data) {
                        app.showModalWindow(data,function(){
                            if(typeof callBackFunction == 'function'){
                                callBackFunction();
                            }
                        });
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
        }


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
            if (desc == 'Standard') {
                standard = true;
            }
            if (desc == 'Optional') {
                optional = true;
            }

            //@TODO: Figure out how to make this set a callback to use with the correct results.
            //@TODO: Because variables are by ref then it's always the "last" one.
            if (desc.length > 0) {
                button[desc] = {
                    'label': app.vtranslate(desc),
                    //'className' : "btn-tg-" + desc.toLowerCase(),
                    'className': "btn-danger",
                    callback: function () {
                        aDeferred.resolve(data.results[desc]);
                    }
                    /*
                     // this processes the function when it sees it instead of setting(?) the function.
                     callback : (function(aDef, val) {
                     aDef.resolve(val);
                     })(aDeferred, data.results[desc])
                     */
                }
            }
        }

        //@TODO: not do this... seriously... make that loop work for the love of god.
        if (standard) {
            button['Standard'] = {
                'label': app.vtranslate('Standard'),
                'className': "btn-danger",
                callback: function () {
                    aDeferred.resolve(data.results['Standard']);
                }
            }
        }
        if (optional) {
            button['Optional'] = {
                'label': app.vtranslate('Optional'),
                'className': "btn-danger",
                callback: function () {
                    aDeferred.resolve(data.results['Optional']);
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

    toggleEmployerAssistingBlock : function() {
      jQuery('table[name="LBL_OPPORTUNITY_EMPLOYERASSISTING"] img').trigger('click');
    },

    initializeSTS: function() {
        this.sts = new Opportunities_STS_Js();
        this.sts.registerEvents(false);
    },
    registerCustomTooltipEvents: function() {
	var references = jQuery('span.customToolTip');
	var lastPopovers = [];

	// Fetching reference fields often is not a good idea on a given page.
	// The caching is done based on the URL so we can reuse.
	var CACHE_ENABLED = true; // TODO - add cache timeout support.

	function prepareAndShowTooltipView() {
	    hideAllTooltipViews();

	    var el = jQuery(this);
	    var id = jQuery(this).data("fielvalue"); //only DetailView
	    var module = jQuery(this).find('a').attr('title');
	    
	    var url = '?module='+module+'&view=TooltipAjax&record='+id;
	    var cachedView = CACHE_ENABLED ? jQuery('[data-url-cached="'+url+'"]') : null;
	    if (cachedView && cachedView.length) {
		    showTooltip(el, cachedView.html());
	    } else {
		AppConnector.request(url).then(function(data){
		    cachedView = jQuery('<div>').css({display:'none'}).attr('data-url-cached', url);
		    cachedView.html(data);
		    jQuery('body').append(cachedView);
		    showTooltip(el, data);
		});
	    }
	}
	
	function get_popover_placement(el) {
	    var width = window.innerWidth;
	    var left_pos = jQuery(el).offset().left;
	    if (width - left_pos > 400) return 'right';
	    return 'left';
	}

	function showTooltip(el, data) {
	    var the_placement = get_popover_placement(el);
	    el.popover({
		trigger: 'manual',
		content: data,
		animation: false,
		placement:  the_placement,
		template: '<div class="popover popover-tooltip"><div class="arrow"></div><div class="popover-inner"><button name="vtTooltipClose" class="close" style="color:white;opacity:1;font-weight:lighter;position:relative;top:3px;right:3px;">x</button><h3 class="popover-title"></h3><div class="popover-content"><div></div></div></div></div>'
	    });
	    lastPopovers.push(el.popover('show'));
	    registerToolTipDestroy();
	}

	function hideAllTooltipViews() {// Hide all previous popover
	    var lastPopover = null;
	    while (lastPopover = lastPopovers.pop()) {
		lastPopover.popover('hide');
	    }
	}

	references.each(function(index, el){
	    if(jQuery(el).text().trim() == ""){
		el = jQuery(el).closest('td');
	    }
	    
	    // unbind the hoverIntent
	    jQuery(el).unbind("mouseenter").unbind("mouseleave");
	    jQuery(el).removeProp('hoverIntent_t');
	    jQuery(el).removeProp('hoverIntent_s');

	    jQuery(el).hoverIntent({
		interval: 100,
		sensitivity: 7,
		timeout: 10,
		over: prepareAndShowTooltipView,
		out: hideAllTooltipViews
	    });
	});

	function registerToolTipDestroy() {
	    jQuery('button[name="vtTooltipClose"]').on('click', function(e){
		var lastPopover = lastPopovers.pop();
		lastPopover.popover('hide');
	    });
	}
    },
    registerEvents : function() {
        if($('[name="instance"]').val() == 'sirva') {
            this.initializeSTS();
        }
        var detailContentsHolder = this.getContentHolder();
        this._super();
        this.removeEstimatesByType();
        var thisInstance = this;

        detailContentsHolder.on('click','.moreRecentContacts', function(){
            var recentContactsTab = thisInstance.getTabByLabel(thisInstance.detailViewRecentContactsLabel);
            recentContactsTab.trigger('click');
        });

        detailContentsHolder.on('click','.moreRecentProducts', function(){
            var recentProductsTab = thisInstance.getTabByLabel(thisInstance.detailViewRecentProductsTabLabel);
            recentProductsTab.trigger('click');
        });

        this.initializeAddressAutofill('Opportunities');
        this.registerChangeMoveType();
        this.registerPhoneTypeEvents();
        this.preventEmptyDestination();
        this.toggleNationalAccountBlock();
        jQuery('.widgetContainer_contacts').parent().hide();
        this.registerTransitGuideEvent();
        this.toggleEmployerAssistingBlock();
	this.registerCustomTooltipEvents();
    },
})

