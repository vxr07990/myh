/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

Vtiger_Edit_Js("Claims_Edit_Js", {}, {
    readonlyblock1: 'Amounts',

    setAmountsReadOnly: function () {
        var thisInstance = this;
        jQuery(document).find('.blockHeader:contains("' + thisInstance.readonlyblock1 + '")').closest('table').find('input').prop('disabled', true).prop('readonly', 'readonly')
    },
    registerAddParticipantButtons: function () {
        var thisInstance = this;
        var table = jQuery('[name^="serviceProviderResponsibilityTable"]').find('tbody');
        var button = jQuery('.addParticipant');

        var addHandler = function () {
            var newRow = jQuery('.defaultParticipant').clone();
            var sequenceNode = jQuery("input[name='numSPR']");
            var sequence = sequenceNode.val();
            sequence++;
            sequenceNode.val(sequence);
            newRow.addClass('newParticipant');
            newRow.removeClass('hide defaultParticipant');

            newRow.find('.default').each(function () {
                jQuery(this).attr('name', jQuery(this).attr('name') + '_' + sequence);
                jQuery(this).removeClass('default change');
            });


            //this again is a fix/hack to format the needed fields for the agent lookup so it can work
            newRow.find('select[name="agent_type"]').attr('name', 'agent_type_' + sequence);
            newRow.find('input[name="agents_id_display"]').attr('id', 'agents_id_display'.replace('_display', '_' + sequence + '_display'));
            newRow.find('input[name="agents_id_display"]').attr('name', 'agents_id_display'.replace('_display', '_' + sequence + '_display'));
            newRow.find('input[name="vendors_id_display"]').attr('id', 'vendors_id_display'.replace('_display', '_' + sequence + '_display'));
            newRow.find('input[name="vendors_id_display"]').attr('name', 'vendors_id_display'.replace('_display', '_' + sequence + '_display'));
            newRow.find('.validate').each(function () {
                var validator = "validate[";
                if (jQuery(this).data('fieldinfo').mandatory == true) {
                    validator += "required,";
                }
                validator += "funcCall[Vtiger_Base_Validator_Js.invokeValidation]]";
                jQuery(this).data('validationEngine', validator).attr('data-validation-engine', validator);
                jQuery(this).removeClass('validate');
            });

            jQuery('[name="serviceProviderResponsibilityTable"] tbody tr.totalsRow').before(newRow);
            thisInstance.registerClearReferenceSelectionEvent(newRow);
            jQuery('[name="agent_type_' + sequence + '"]').chosen();
        };



        button.on('click', addHandler);
    },
    setPaymentsTableSelects: function () {
        jQuery('[name="paymentsTable"] .chzn-select').each(function () {
            jQuery(this).find('option[value="' + jQuery(this).data('selected-value') + '"]').prop("selected", true).trigger("liszt:updated");
        });
    },
    registerAddPaymentsButtons: function () {
        var table = jQuery('[name="paymentsTable"]').find('tbody');
        var button = jQuery('.addClaimPayment');

        var addHandler = function () {
            var newRow = jQuery('.defaultPayment').clone();
            var sequence = parseInt(jQuery(".paymentRow:not(.defaultPayment)").length) + 1;

            newRow.find('select[name="paymentFees"]').attr('name', 'paymentFees' + sequence).addClass('chzn-select');
            newRow.find('input[name="feesDate"]').attr('name', 'feesDate' + sequence);
            newRow.find('input[name="feesAmount"]').attr('name', 'feesAmount' + sequence);
            newRow.addClass('paymentRow' + sequence);
            newRow.removeClass('hide defaultPayment');

            newRow.find('.default').each(function () {
                jQuery(this).removeClass('default change');
            });

            jQuery('[name="paymentsTable"] tbody tr.totalsRow').before(newRow);
            newRow.find('.chzn-select').chosen();
            app.registerEventForDatePickerFields(newRow.find(".dateField"));
            jQuery('input[name="numPayments"]').val(sequence);
        };
        button.on('click', addHandler);
    },
    registerRemovePaymentsButton: function () {
        var thisInstance = this;
        jQuery(document).on("click", ".removePayment", function () {
            jQuery(this).closest("tr.paymentRow").remove();
            thisInstance.calcFeesAmount();
            thisInstance.calcSPRPercent();
        });
    },
    registerRemoveParticipantButton: function () {
        var thisInstance = this;
        jQuery(document).on('click', '.removeParticipant', function () {
            if (jQuery(this).siblings('input:hidden[name^="participantId"]').val() == 'none') {
                jQuery(this).parent().parent().remove()
            } else {
                jQuery(this).parent().parent().addClass('hide');
                jQuery(this).siblings('input:hidden[name^="participantDelete"]').val('deleted');
            }
            thisInstance.calcSPRAmount();
            thisInstance.calcSPRPercent();
        });
    },
    calcFeesAmount: function () {
        var tamount = 0;
        jQuery(".feesAmount:not(.default)").each(function () {
            tamount += parseFloat(jQuery(this).val());
        });
        jQuery("#tamount").val(tamount.toFixed(2));
        jQuery(".tamount").text("Amount Total: " + tamount.toFixed(2));
    },
    calcSPRAmount: function () {
        var tamount = 0;
        jQuery(".respon_amount:not(.default)").each(function () {
            if (!jQuery(this).closest("tr").hasClass("hide") && jQuery(this).val() !== "")
                tamount += parseFloat(jQuery(this).val());
        });
        jQuery("#sprtamount").val(tamount.toFixed(2));//Math.round(tamount * 100) / 100);
        jQuery(".sprtamount").text("Amount Total: " + tamount.toFixed(2));//Math.round(tamount * 100) / 100);

    },
    calcSPRPercentFromAmount: function (amount) {
        var paymentsAmount = parseFloat(jQuery('#tamount').val());
        var percent = (jQuery(amount).val() * 100) / paymentsAmount;
        var name = jQuery(amount).closest("tr").find(".respon_percentage").attr("name");
        var auxPercent = percent;

        jQuery(".respon_percentage:not(.default)").each(function () {
            if (jQuery(this).attr("name") !== name) {
                auxPercent += parseFloat(jQuery(this).val());
            }
        });

        if (auxPercent <= 100) {
            jQuery(amount).closest("tr").find(".respon_percentage").val(percent.toFixed(2));//Math.round(percent * 100) / 100);
            jQuery(".sprtpercentage").text("Responsibility Total: " + percent.toFixed(2));//Math.round(percent * 100) / 100);
            this.calcSPRAmount();
        } else {
            this.calcSPRPercent();
        }
    },
    calcSPRPercent: function () {
        var paymentsAmount = parseFloat(jQuery('#tamount').val());

        var tpercent = 0;
        jQuery(".respon_percentage:not(.default)").each(function () {
            if (!jQuery(this).closest("tr").hasClass("hide") && jQuery(this).val() !== "") {
                var percentage = parseFloat(jQuery(this).val());
                var aux = paymentsAmount * percentage / 100;
                jQuery(this).parent().next().find('.respon_amount').val(aux.toFixed(2));
                tpercent += parseFloat(jQuery(this).val());
            }
        });
        jQuery(".sprtpercentage").text("Responsibility Total: " + tpercent.toFixed(2));//Math.round(tpercent * 100) / 100);

        this.calcSPRAmount();
    },
    registerFeesAmountChange: function () {
        var thisInstance = this;
        jQuery(document).on("blur", ".feesAmount:not(.default)", function () {
            thisInstance.calcFeesAmount();
            thisInstance.calcSPRPercent();
        });
    },
    registerSPRAmountChange: function () {
        var thisInstance = this;
        jQuery(document).on("change", ".respon_amount:not(.default)", function () { //like blur but has to change and blur to fire event
            thisInstance.calcSPRPercentFromAmount(jQuery(this));
        });
    },
    registerSPRPercentageChange: function () {
        var thisInstance = this;
        jQuery(document).on("focusin", ".respon_percentage:not(.default)", function () {
            jQuery(this).data('val', $(this).val());
        });
        jQuery(document).on("change", ".respon_percentage:not(.default)", function () {
            var prev = jQuery(this).data('val');
            var auxPercent = parseFloat(jQuery(this).val());
            var name = jQuery(this).attr("name");

            jQuery(".respon_percentage:not(.default)").each(function () {
                if (jQuery(this).attr("name") !== name && jQuery(this).val() !== "") {
                    auxPercent += parseFloat(jQuery(this).val());
                }
            });

            if (auxPercent <= 100) {
                thisInstance.calcSPRPercent();
            } else {
                jQuery(this).val(prev);
            }
        });
    },
    registerRecordPreSaveEvent: function () {
        jQuery(form).on(Vtiger_Edit_Js.recordPreSave, function (e, data) {
            var partAgent = jQuery(".participantRow:not(.defaultParticipant):not(.hide)").length;
            var settleAmount = jQuery(".settlementAmountRow:not(.defaultSettlementAmount):not(.hide)").length;
            // Will check amount if both tables have data or if only agents table has data
            // If only settlement amount table has data, will not check :)
            if ((settleAmount < 1 && partAgent > 0) || ((partAgent > 0) && (settleAmount > 0))) {
                if (parseInt(jQuery("#tamount").val()) !== parseInt(jQuery("#sprtamount").val())) {
                    e.preventDefault();
                    var params = {
                        title: app.vtranslate('JS_ERROR'),
                        text: "The Total Amount in the service providers section must be equal to the total amount in the settlements section.",
                        animation: 'show',
                        type: 'error'
                    };
                    Vtiger_Helper_Js.showPnotify(params);
                }
            }
        });
    },
    getPopUpParams: function (container) {

        var params = {};
        var sourceModule = app.getModuleName();
        var popupReferenceModule = jQuery('input[name="popupReferenceModule"]', container).val();
        var sourceFieldElement = jQuery('input[class="sourceField"]', container);
        var sourceField = sourceFieldElement.attr('name');
        var sourceRecordElement = jQuery('input[name="record"]');
        var sourceRecordId = '';
        if (sourceRecordElement.length > 0) {
            sourceRecordId = sourceRecordElement.val();
        }

        var isMultiple = false;
        if (sourceFieldElement.data('multiple') == true) {
            isMultiple = true;
        }

        var params = {
            'module': popupReferenceModule,
            'src_module': sourceModule,
            'src_field': sourceField,
            'src_record': sourceRecordId,
            'claimssummary_id': jQuery('input[name="claimssummary_id"]').val(),
        }

        if (isMultiple) {
            params.multi_select = true;
        }
        return params;
    },
    openPopUp: function (e) {

        var thisInstance = this;
        var parentElem = jQuery(e.target).closest('td');

        var params = this.getPopUpParams(parentElem);

        var isMultiple = false;
        if (params.multi_select) {
            isMultiple = true;
        }

        var sourceFieldElement = jQuery('input[class="sourceField"]', parentElem);

        var prePopupOpenEvent = jQuery.Event(Vtiger_Edit_Js.preReferencePopUpOpenEvent);
        sourceFieldElement.trigger(prePopupOpenEvent);

        if (prePopupOpenEvent.isDefaultPrevented()) {
            return;
        }

        var popupInstance = Vtiger_Popup_Js.getInstance();
        popupInstance.show(params, function (data) {
            var responseData = JSON.parse(data);
            var dataList = new Array();
            for (var id in responseData) {
                var data = {
                    'name': responseData[id].name,
                    'id': id
                }
                dataList.push(data);
                if (!isMultiple) {
                    thisInstance.setReferenceFieldValue(parentElem, data);
                }
            }

            if (isMultiple) {
                sourceFieldElement.trigger(Vtiger_Edit_Js.refrenceMultiSelectionEvent, {'data': dataList});
            }
            sourceFieldElement.trigger(Vtiger_Edit_Js.postReferenceSelectionEvent, {'data': responseData});
        });
    },

    registersReferenceSelectionEvent: function (form) {
        var thisInstance = this;
        if (typeof form == 'undefined') {
            form = this.getForm();
        }

        form.on(Vtiger_Edit_Js.postReferenceSelectionEvent, function (e, data) {
            var name = e.target.name;
            if (name.indexOf('vendors_id') >= 0) {
                var number = name.slice(-1);
                thisInstance.setICode(e.target.defaultValue, number);
            }
        });
    },

    setICode: function (vendorId, number) {
        var dataURL = 'index.php?module=Vendors&action=GetICode&vendorId=' + vendorId;
        AppConnector.request(dataURL).then(
                function (data) {
                    if (data.success) {
                        if (data.result.icode) {
                            jQuery('[name="icode_' + number).text(data.result.icode);
                        } else {
                            jQuery('[name="icode_' + number).text('');
                        }
                    }
                },
                function (error) {
                }
        );
    },
    registerClearReferenceSelectionEvent: function (container) {
        container.find('.clearReferenceSelection').on('click', function (e) {
            var element = jQuery(e.currentTarget);
            var parentTdElement = element.closest('td');
            var fieldNameElement = parentTdElement.find('.sourceField');
            var fieldName = fieldNameElement.attr('name');
            //logic to clear icode related to vendor
            if (fieldName.indexOf('vendors_id') >= 0) {
                var icode = 'icode_' + fieldName.slice(-1);
                jQuery('[name="' + icode + '"').text('');
            }
            fieldNameElement.val('');
            parentTdElement.find('#' + fieldName + '_display').removeAttr('readonly').val('');
            element.trigger(Vtiger_Edit_Js.referenceDeSelectionEvent);
            fieldNameElement.trigger(Vtiger_Edit_Js.referenceDeSelectionEvent);
            e.preventDefault();
        });
    },
    getPopUpParams: function (container) {
        var params = {};
        var sourceModule = app.getModuleName();
        var popupReferenceModule = jQuery('input[name="popupReferenceModule"]', container).val();
        var sourceFieldElement = jQuery('input[class="sourceField"]', container);
        var sourceField = sourceFieldElement.attr('name');
        var sourceRecordElement = jQuery('input[name="record"]');
        var isRelationOperation = jQuery('input[name="relationOperation"]').val();
        var claimSourceModule = jQuery('input[name="sourceModule"]').val();
        var claimSourceRecord = jQuery('input[name="sourceRecord"]').val();
        var sourceRecordId = '';
        if (sourceRecordElement.length > 0) {
            sourceRecordId = sourceRecordElement.val();
        }

        var isMultiple = false;
        if (sourceFieldElement.data('multiple') == true) {
            isMultiple = true;
        }

        var params = {
            'module': popupReferenceModule,
            'src_module': sourceModule,
            'src_field': sourceField,
            'src_record': sourceRecordId,
            'isRelationOperation': isRelationOperation,
            'claimSourceModule': claimSourceModule,
            'claimSourceRecord': claimSourceRecord
        };

        if (isMultiple) {
            params.multi_select = true;
        }
        return params;
    },
    loadPAFromOrder: function () {
        var thisInstance = this;
        jQuery(document).on("change", ".agents_type", function () {
            var is_there = false;
            var name = jQuery(this).attr('name');
            row = name.split('_');
            var row_no = row[2];
            var agent_type = jQuery(this).val();

            jQuery('.agents_type').each(function () {
                if (jQuery(this).val() == agent_type && jQuery(this).attr('name') !== name) {
                    is_there = true;
                }
            });

            if (is_there) {
                var params = {
                    title: app.vtranslate('JS_ERROR'),
                    text: app.vtranslate(agent_type + '  Duplicated'),
                    animation: 'show',
                    type: 'error'
                };
                Vtiger_Helper_Js.showPnotify(params);
                return;
            }


            var params = {
                'module': 'Claims',
                'action': 'ClaimsActions',
                'mode': 'loadOrderAgent',
                'claimssummary_id': jQuery('input[name="claimssummary_id"]').val(),
                'agent_type': agent_type,
            }

            var progressIndicatorElement = thisInstance.showLoadingMessage('Loading Agent...');
            AppConnector.request(params).then(
                    function (responseData) {
                        if (responseData.result.result !== "OK") {
                            thisInstance.hideLoadingMessage(progressIndicatorElement);
                            var params = {
                                title: app.vtranslate('JS_ERROR'),
                                text: app.vtranslate('Order does not have a designated ' + agent_type),
                                animation: 'show',
                                type: 'error'
                            };
                            Vtiger_Helper_Js.showPnotify(params);

                            jQuery('input[name="agents_id_' + row_no + '"]').val('');
                            jQuery('input[name="agents_id_' + row_no + '_display"]').val('');
                            jQuery('input[name="agents_id_' + row_no + '_display"]').attr('readonly', false);

                        } else {
                            jQuery('input[name="agents_id_' + row_no + '"]').val(responseData.result.agent_id);
                            jQuery('input[name="agents_id_' + row_no + '_display"]').val(responseData.result.agent_name);
                            jQuery('input[name="agents_id_' + row_no + '_display"]').attr('readonly', 'readonly');

                            thisInstance.hideLoadingMessage(progressIndicatorElement);

                        }
                    });
        });
    },
    showLoadingMessage: function (message) {
        var loadingMessage = app.vtranslate(message);
        var progressIndicatorElement = jQuery.progressIndicator({
            'message': loadingMessage,
            'position': 'html',
            'blockInfo': {
                'enabled': true
            }
        });

        return progressIndicatorElement;

    },
    hideLoadingMessage: function (progressIndicatorElement) {
        progressIndicatorElement.progressIndicator({
            'mode': 'hide'
        })
    },
    registerDistributionCheckboxEvent: function () {
	jQuery(document).on("change", ".distribution", function () {
	    if (jQuery(this).prop("checked")) {
		    jQuery(this).val("yes");
	    } else {
		    jQuery(this).val("no");
	    }
	});
    },    
    registerEvents : function() {
	this._super();
	this.setAmountsReadOnly();
	this.registerAddParticipantButtons();
	this.registerRemoveParticipantButton();
	this.setPaymentsTableSelects();
	this.registerAddPaymentsButtons();
	this.registerRemovePaymentsButton();
	this.registerFeesAmountChange();
	this.registerSPRAmountChange();
	this.registerSPRPercentageChange();
	this.registersReferenceSelectionEvent();
        this.loadPAFromOrder();
	this.registerDistributionCheckboxEvent();
	var form = this.getForm();
	this.registerRecordPreSaveEvent(form);
    }
});