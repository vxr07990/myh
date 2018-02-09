/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

Vtiger_Edit_Js("ClaimItems_Edit_Js", {}, {
    setSettlementAmountTableSelects: function () {
        jQuery('[name="settlementAmountTable"] .chzn-select').each(function () {
            jQuery(this).find('option[value="' + jQuery(this).data('selected-value') + '"]').prop("selected", true).trigger("liszt:updated");
        });
    },
    registerAddSettlementAmountButtons: function () {
        var table = jQuery('[name="settlementAmountTable"]').find('tbody');
        var button = jQuery('.addSettlementAmount');

        var addHandler = function () {
            var newRow = jQuery('.defaultSettlementAmount').clone();
            var sequence = parseInt(jQuery(".settlementAmountRow:not(.defaultSettlementAmount)").length) + 1;

            newRow.find('select[name="paymentType"]').attr('name', 'paymentType' + sequence).addClass('chzn-select');
            newRow.find('input[name="amount"]').attr('name', 'amount' + sequence);
            newRow.find('input[name="amountDenied"]').attr('name', 'amountDenied' + sequence);
            newRow.find('input[name="itemOmitted"]').attr('name', 'itemOmitted' + sequence);
            newRow.addClass('settlementAmountRow' + sequence);
            newRow.removeClass('hide defaultSettlementAmount');

            newRow.find('.default').each(function () {
                jQuery(this).removeClass('default change');
            });

            jQuery('[name="settlementAmountTable"] tbody tr.totalsRow').before(newRow);
            newRow.find('.chzn-select').chosen();
            jQuery('input[name="numSettlementAmount"]').val(sequence);
        };
        button.on('click', addHandler);
    },
    registerRemoveSettlementAmountButton: function () {
        var thisInstance = this;
        jQuery(document).on("click", ".removeSettlementAmount", function () {
            jQuery(this).closest("tr.settlementAmountRow").remove();
            thisInstance.calcSettlementAmount();
            thisInstance.calcSettlementAmountDenied();
            thisInstance.calcSPRPercent();
        });
    },
    registerSPRAmountChange: function () {
        var thisInstance = this;
        jQuery(document).on("change", ".respon_amount:not(.default)", function () { //like blur but has to change and blur to fire event
            thisInstance.calcSPRPercentFromAmount(jQuery(this));
        });
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
    calcSettlementAmount: function () {
        var tamount = 0;
        jQuery('[name="settlementAmountTable"] .sAmount:not(.default)').each(function () {
            tamount += parseFloat(jQuery(this).val());
        });
        jQuery("#tamount").val(tamount.toFixed(2));
        jQuery(".tamount").text("Total Amount: " + tamount.toFixed(2));
    },
    calcSettlementAmountDenied: function () {
        var tamountd = 0;
        jQuery('[name="settlementAmountTable"] .sAmountD:not(.default)').each(function () {
            tamountd += parseFloat(jQuery(this).val());
        });
        jQuery("#tamountd").val(tamountd.toFixed(2));
        jQuery(".tamountd").text("Total Amount Denied: " + tamountd.toFixed(2));
    },
    calcSPRAmount: function () {
        var tamount = 0;
        jQuery(".respon_amount:not(.default)").each(function () {
            if (!jQuery(this).closest("tr").hasClass("hide"))
                tamount += parseFloat(jQuery(this).val());
        });
        jQuery("#sprtamount").val(tamount.toFixed(2));
        jQuery(".sprtamount").text("Amount Total: " + tamount.toFixed(2));

    },
    calcSPRPercent: function () {
        var paymentsAmount = parseFloat(jQuery('#tamount').val());

        var tpercent = 0;
        jQuery(".respon_percentage:not(.default)").each(function () {
            if (!jQuery(this).closest("tr").hasClass("hide")) {
                var percentage = parseFloat(jQuery(this).val());
                var aux = paymentsAmount * percentage / 100;
                jQuery(this).parent().next().find('.respon_amount').val(aux.toFixed(2));
                tpercent += parseFloat(jQuery(this).val());
            }
        });
        jQuery(".sprtpercentage").text("Responsibility Total: " + tpercent.toFixed(2));

        this.calcSPRAmount();
    },
    registerSettlementAmountChange: function () {
        var thisInstance = this;
        jQuery(document).on("blur", ".sAmount:not(.default)", function () {
            thisInstance.calcSettlementAmount();
            thisInstance.calcSPRPercent();
        });
        jQuery(document).on("blur", ".sAmountD:not(.default)", function () {
            thisInstance.calcSettlementAmountDenied();
        });
    },
    registerSPRAmountChange: function () {
        var thisInstance = this;
        jQuery(document).on("blur", ".respon_amount:not(.default)", function () {
            thisInstance.calcSPRAmount();
        });
    },
    registerSPRPercentageChange: function () {
        var thisInstance = this;
        jQuery(document).on("blur", ".respon_percentage:not(.default)", function () {
            thisInstance.calcSPRPercent();
        });
    },
    registerSettlementAmountCheckboxEvent: function () {
        jQuery(document).on("change", ".itemOmitted2", function () {
            if (jQuery(this).prop("checked")) {
                jQuery(this).val("yes");
            } else {
                jQuery(this).val("no");
            }
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
        });
    },
    registerAddParticipantButtons: function () {
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
            jQuery('[name="agent_type_' + sequence + '"]').chosen();
            //newRow = newRow.appendTo(table);
        };
        button.on('click', addHandler);
    },
    registerAddOrigialConditionButtons: function () {
        var table = jQuery('[name="originalConditionTable"]').find('tbody');
        var button = jQuery('.addOriginalCondition');

        var addHandler = function () {
            var newRow = jQuery('.defaultOriginalCondition').clone();
            var sequence = parseInt(jQuery(".originalConditionRow:not(.defaultOriginalCondition)").length) + 1;

            newRow.find('input[name="inventoryNumber"]').attr('name', 'inventoryNumber' + sequence);
            newRow.find('input[name="tagColor"]').attr('name', 'tagColor' + sequence);
            newRow.find('input[name="originalCondition"]').attr('name', 'originalCondition' + sequence);
            newRow.find('input[name="exceptions"]').attr('name', 'exceptions' + sequence);
            newRow.find('input[name="dateTaken"]').attr('name', 'dateTaken' + sequence);
            newRow.addClass('originalConditionRow' + sequence);
            newRow.removeClass('hide defaultOriginalCondition');

            newRow.find('.default').each(function () {
                jQuery(this).removeClass('default change');
            });

            newRow = newRow.appendTo(table);
            app.registerEventForDatePickerFields(newRow.find(".dateField"));
            jQuery('input[name="numOriginalConditions"]').val(sequence);
        };
        button.on('click', addHandler);
    },
    registerRemoveOriginalConditionButton: function () {
        jQuery(document).on("click", ".removeOriginalCondition", function () {
            jQuery(this).closest("tr.originalConditionRow").remove();
            jQuery('input[name="numOriginalConditions"]').val(jQuery(".originalConditionRow:not(.defaultOriginalCondition)").length);
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
    registerClaimantRequest: function () {
        var claimantRequest = jQuery('[name="claimitemsdetails_claimantrequest"] option:selected').val();
        if (claimantRequest !== "Cash") {
            jQuery('[name="LBL_CLAIMITEMS_INFORMATION"]').find("td:contains(Amount)").hide().next("td").hide();
        }
        jQuery(document).on('change', '[name="claimitemsdetails_claimantrequest"]', function () {
            if (jQuery(this).val() == "Cash") {
                jQuery('[name="LBL_CLAIMITEMS_INFORMATION"]').find("td:contains(Amount)").show().next("td").show();
            } else {
                jQuery('[name="LBL_CLAIMITEMS_INFORMATION"]').find("td:contains(Amount)").hide().next("td").hide();
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
            'linked_claim': jQuery('input[name="linked_claim"]').val(),
        };

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
    registerLossCodeSelect: function () {
        var thisInstance = this;
        var lossCode = jQuery('[name="claimitemsdetails_losscode"] option:selected').val();
        var claimType = jQuery('[name="claim_type"]').val();

        if (claimType != 'Service Recovery') {
            return false;
        }

        if (lossCode === 'Inconvenience' || lossCode === 'Reassembly') {
            thisInstance.toggleReassemblyInconvenienceFields('show');
            thisInstance.toggleNOTReassemblyInconvenienceFields('hide');
            thisInstance.toggleLikeInventoryItems('show');
        } else {
            thisInstance.toggleReassemblyInconvenienceFields('hide');
            thisInstance.toggleNOTReassemblyInconvenienceFields('show');
            thisInstance.toggleLikeInventoryItems('hide');
        }
        jQuery(document).on('change', '[name="claimitemsdetails_losscode"]', function () {
            var lossCode = jQuery(this).val();
            if (lossCode === 'Inconvenience' || lossCode === 'Reassembly') {
                thisInstance.toggleReassemblyInconvenienceFields('show');
                thisInstance.toggleNOTReassemblyInconvenienceFields('hide');
                thisInstance.toggleLikeInventoryItems('show');
            } else {
                thisInstance.toggleReassemblyInconvenienceFields('hide');
                thisInstance.toggleNOTReassemblyInconvenienceFields('show');
                thisInstance.toggleLikeInventoryItems('hide');
            }
        });
    },
    toggleLikeInventoryItems: function (state) {
        if (state === 'show') {
            jQuery('[name="originalConditionTable"]').removeClass('hide');
        } else {
            jQuery('[name="originalConditionTable"]').addClass('hide');
        }
    },
    toggleReassemblyInconvenienceFields: function (state) {
        var toggleFields = [
            'inventory_number',
            'item_description',
            'tag_color',
            'claimitemsdetails_item',
            'claimitemsdetails_claimantrequest',
            'claimitemsdetails_originalconditions',
            'claimitemsdetails_exceptions',
            'claimitemsdetails_datetaken',
            'claimitemsdetails_documented'
        ];

        jQuery.each(toggleFields, function (key, value) {
            if (value.match("^LBL")) { // IF the value is a label
                if (state === 'show') {
                    jQuery('[name="' + value + '"]').removeClass('hide');
                } else {
                    jQuery('[name="' + value + '"]').addClass('hide');
                }
            } else { //else its an input
                if (state === 'show') {
                    jQuery('[name="' + value + '"]').closest('td').removeClass('hide').closest('td').prev().find('label').closest('td').removeClass('hide');
                } else {
                    jQuery('[name="' + value + '"]').closest('td').addClass('hide').closest('td').prev().find('label').closest('td').addClass('hide');
                }
            }

        });
    },
    toggleNOTReassemblyInconvenienceFields: function (state) {
        var toggleFields = [
            'LBL_CLAIMITEMS_CUSTOMER_REQUEST',
            'LBL_CLAIMITEMS_CUSTOMER_AUTHORIZED',
        ];
        if (state === 'show') {
            jQuery('[name="dailyExpenseTable"]').removeClass('hide');
        } else {
            jQuery('[name="dailyExpenseTable"]').addClass('hide');
        }

        jQuery.each(toggleFields, function (key, value) {
            if (value.match("^LBL")) { // IF the value is a label
                if (state === 'show') {
                    jQuery('[name="' + value + '"]').removeClass('hide');
                } else {
                    jQuery('[name="' + value + '"]').addClass('hide');
                }
            } else { //else its an input
                if (state === 'show') {
                    jQuery('[name="' + value + '"]').parent().removeClass('hide').closest('td').prev().find('label').removeClass('hide');
                } else {
                    jQuery('[name="' + value + '"]').parent().addClass('hide').closest('td').prev().find('label').addClass('hide');
                }
            }

        });
    },
    registerAddDailyExpenseButtons: function () {
        var table = jQuery('[name="dailyExpenseTable"]').find('tbody');
        var button = jQuery('.addDailyExpense');

        var addHandler = function () {
            var newRow = jQuery('.defaultDailyExpense').clone();
            var sequence = parseInt(jQuery(".dailyExpenseRow:not(.defaultDailyExpense)").length) + 1;

            newRow.find('input[name="dailyExpenseId_"]').attr('name', 'dailyExpenseId_' + sequence);
            newRow.find('input[name="expenseDate"]').attr('name', 'expenseDate' + sequence);
            newRow.find('input[name="nAdults"]').attr('name', 'nAdults' + sequence);
            newRow.find('input[name="nChildren"]').attr('name', 'nChildren' + sequence);
            newRow.find('input[name="dailyRate"]').attr('name', 'dailyRate' + sequence);
            newRow.find('input[name="nMeals"]').attr('name', 'nMeals' + sequence);
            newRow.find('input[name="tCostMeals"]').attr('name', 'tCostMeals' + sequence);
            newRow.find('input[name="dailyTotal"]').attr('name', 'dailyTotal' + sequence);
            newRow.addClass('dailyExpenseRow' + sequence);
            newRow.removeClass('hide defaultDailyExpense');

            newRow.find('.default').each(function () {
                jQuery(this).removeClass('default change');
            });

            jQuery('[name="dailyExpenseTable"] tbody tr.totalsRow').before(newRow);
            app.registerEventForDatePickerFields(newRow.find(".dateField"));
            jQuery('input[name="numDailyExpenses"]').val(sequence);
        };
        button.on('click', addHandler);
    },
    registerRemoveDailyExpenseButton: function () {
        var thisInstance = this;
        jQuery(document).on("click", ".removeDailyExpense", function () {
            if (jQuery(this).siblings('input:hidden[name^="dailyExpenseId_"]').val() === 'none') {
                jQuery(this).parent().parent().remove();
            } else {
                jQuery(this).parent().parent().addClass('hide');
                jQuery(this).siblings('input:hidden[name^="dailyExpenseDelete_"]').val('deleted');
            }
            jQuery('input[name="numDailyExpenses"]').val(jQuery(".dailyExpenseRow:not(.defaultDailyExpense)").length);
            thisInstance.calcDailyExpense();
        });
    },
    calcDailyExpense: function () {
        var tamount = 0;
        jQuery('[name="dailyExpenseTable"] .dailyTotal:not(.default)').each(function () {
            if (!jQuery(this).closest("tr").hasClass("hide")) {
                tamount += parseInt(jQuery(this).val());
            }
        });
        jQuery("#dtamount").val(tamount);
        jQuery(".dtamount").text("Total Amount: " + tamount);
    },
    registerDailyExpenseChange: function () {
        var thisInstance = this;
        jQuery(document).on("blur", ".dailyTotal:not(.default)", function () {
            thisInstance.calcDailyExpense();
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
                'claimstype_id': jQuery('input[name="linked_claim"]').val(),
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
    registerEvents: function () {
        this._super();
        this.registerClaimantRequest();
        this.registerSettlementAmountChange();
        this.registerSPRAmountChange();
        this.registerSPRPercentageChange();
        this.setSettlementAmountTableSelects();
        this.registerAddSettlementAmountButtons();
        this.registerRemoveSettlementAmountButton();
        this.registerSettlementAmountCheckboxEvent();
        this.registerAddParticipantButtons();
        this.registerRemoveParticipantButton();
        this.registerAddOrigialConditionButtons();
        this.registerRemoveOriginalConditionButton();
        //jQuery('#linked_claim_display').closest('tr').addClass('hide');
        var form = this.getForm();
        this.registerRecordPreSaveEvent(form);
        this.registerLossCodeSelect();
        this.registerAddDailyExpenseButtons();
        this.registerRemoveDailyExpenseButton();
        this.registerDailyExpenseChange();
        this.registersReferenceSelectionEvent(form);
        this.loadPAFromOrder();
    }
});
