/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

Vtiger_Edit_Js("MovePolicies_Edit_Js", {
    getInstance: function() {
        return new MovePolicies_Edit_Js();
    }

}, {
        loadTariffItems: function() {
            var thisInstance = this;
            jQuery('input[name="policies_tariffid"]').on('change', function(e) {
                params = {
                    'module': 'MovePolicies',
                    'view': 'LoadTariffItems',
                    'mode': 'LoadtariffEdit',
                    'tariff_id': jQuery('input[name="policies_tariffid"]').val(),
                }

                var progressIndicatorElement = jQuery.progressIndicator({
                    'message': 'Loading...',
                    'position': 'html',
                    'blockInfo': {
                        'enabled': true
                    }
                });

                AppConnector.request(params).then(
                    function(data) {
                        jQuery('.tariff_items_table').html('');
                        jQuery('.tariff_items_table').html(data);
                        jQuery('.chzn-select').chosen();
                        thisInstance.massUpdateAuth();
                        progressIndicatorElement.progressIndicator({
                            'mode': 'hide'
                        });
                    },
                    function(jqXHR, textStatus, errorThrown) {
                    }
                );
            });
        },
        loadContractTariff: function() {

            jQuery('input[name="policies_contractid"]').on('change', function(e) {
                params = {
                    'module': 'MovePolicies',
                    'view': 'LoadTariffItems',
                    'mode': 'LoadTariff',
                    'contract_id': jQuery('input[name="policies_contractid"]').val(),
                }

                var progressIndicatorElement = jQuery.progressIndicator({
                    'message': 'Loading...',
                    'position': 'html',
                    'blockInfo': {
                        'enabled': true
                    }
                });

                AppConnector.request(params).then(
                    function(data) {

                        if (data != 'No') {
                            var res = data.split("::");
                            jQuery('input[name="policies_tariffid"]').val(res[0]);
                            jQuery('#policies_tariffid_display').val(res[1]).attr('readonly', true);
                            ;
                        }

                        progressIndicatorElement.progressIndicator({
                            'mode': 'hide'
                        });

                        jQuery('input[name="policies_tariffid"]').trigger('change');
                    },
                    function(jqXHR, textStatus, errorThrown) {
                    }
                );
            });
        },
        setReferenceFieldValue: function(container, params) {
            var sourceField = container.find('input[class="sourceField"]').attr('name');
            var fieldElement = container.find('input[name="' + sourceField + '"]');
            var sourceFieldDisplay = sourceField + "_display";
            var fieldDisplayElement = container.find('input[name="' + sourceFieldDisplay + '"]');
            var popupReferenceModule = container.find('input[name="popupReferenceModule"]').val();

            var selectedName = params.name;
            var id = params.id;

            fieldElement.val(id)
            fieldDisplayElement.val(selectedName).attr('readonly', true);
            fieldElement.trigger(Vtiger_Edit_Js.referenceSelectionEvent, { 'source_module': popupReferenceModule, 'record': id, 'selectedName': selectedName });

            fieldDisplayElement.validationEngine('closePrompt', fieldDisplayElement);
            fieldElement.trigger('change');
        },
        openPopUp: function(e) {
            var thisInstance = this;
            var parentElem = jQuery(e.target).closest('td');

            var params = this.getPopUpParams(parentElem);

            var isMultiple = false;
            if (params.multi_select) {
                isMultiple = true;
            }

            // check agentid select exists
            if(jQuery('select[name="agentid"]').length>0){
                params['agentId'] = jQuery('select[name="agentid"]').val();
            }

            if (params.src_field == 'policies_contractid') {
                account_id = jQuery('input[name="policies_accountid_display"]').val();
                params.account_id = account_id;

            }


            var sourceFieldElement = jQuery('input[class="sourceField"]', parentElem);

            var prePopupOpenEvent = jQuery.Event(Vtiger_Edit_Js.preReferencePopUpOpenEvent);
            sourceFieldElement.trigger(prePopupOpenEvent);

            if (prePopupOpenEvent.isDefaultPrevented()) {
                return;
            }

            var popupInstance = Vtiger_Popup_Js.getInstance();
            popupInstance.show(params, function(data) {
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
                    sourceFieldElement.trigger(Vtiger_Edit_Js.refrenceMultiSelectionEvent, { 'data': dataList });
                }
                sourceFieldElement.trigger(Vtiger_Edit_Js.postReferenceSelectionEvent, { 'data': responseData });
            });
        },
        massUpdateAuth: function(e) {
            jQuery('select[name="mass_update_auth"]').on('change', function(e) {
                var auth_value = jQuery('select[name="mass_update_auth"]').val();
                jQuery('.authchecks').each(function() {
                    if (this.checked) {
                        var line_id = jQuery(this).val();
                        jQuery('select[name="items_auth_' + line_id + '"]').val(auth_value).trigger("liszt:updated");
                    }
                });
            });
        },
        registerAddMiscItemButtons: function() {
            var thisInstance = this;
            var table = jQuery('table[name="MiscItemsTable"]').find('tbody');

            var buttons = jQuery('[id^="addMiscItem"]');

            var sequenceItem = thisInstance.miscSequence;

            var defaultRowClass = 'defaultMiscItem';
            var rowId = 'MiscItemRow';
            var names = ['miscItemId', 'miscItemDescription', 'miscItemAuth', 'miscItemAuthLimit', 'misItemRemarks'];

            var addHandler = function() {
                var localContainer = jQuery(this).closest('tbody');
                var calledField = jQuery(this).attr('name');
                //var regExp = /\d+/g;
                //var serviceid = calledField.match(regExp);

                var newRow = localContainer.find('.' + defaultRowClass).clone(true, true);
                var sequenceNode = localContainer.find("input[name='numMiscItems']");
                var sequence = sequenceNode.val();
                sequence++;
                sequenceNode.val(sequence);

                newRow.removeClass('hide ' + defaultRowClass);
                newRow.attr('id', rowId + sequence);
                for (var i = 0; i < names.length; i++) {
                    var name = names[i];
                    newRow.find('input[name="' + name + '"]').attr('name', name + '-' + sequence);
                }

                newRow.find('select[name="miscItemAuth"]').attr('name', 'miscItemAuth-' + sequence);
                newRow = newRow.appendTo(localContainer.closest('table'));

            };

            buttons.on('click', addHandler);
        },

        registerDeleteMiscItemClickEvent: function() {
            var thisInstance = this;
            jQuery('.deleteMiscItemButton').on('click', function(e) {
                var currentRow = jQuery(e.currentTarget).closest('tr');

                var lineItemId = currentRow.find("input[name^='miscItemDbId'").val();
                if (lineItemId != '' && lineItemId) {
                    var dataURL = 'index.php?module=MovePolicies&action=DeleteMiscTariffItem&lineItemId=' + lineItemId;

                    AppConnector.request(dataURL).then(
                        function(data) {
                            if (data.success) {
                                currentRow.remove();
                                var sequenceNode = jQuery("input[name='numMiscItems']");
                                var sequence = sequenceNode.val();
                                sequence--;
                                sequenceNode.val(sequence);
                            }
                        },
                        function(error) {
                        }
                    );
                } else {
                    currentRow.remove();
                    var sequenceNode = jQuery("input[name='numMiscItems']");
                    var sequence = sequenceNode.val();
                    sequence++;
                    sequenceNode.val(sequence);
                }
            });
        },
        registerEvents: function() {
            this._super();
            this.loadContractTariff();
            this.loadTariffItems();
            this.massUpdateAuth();
            this.registerAddMiscItemButtons();
            this.registerDeleteMiscItemClickEvent();
        },
    });

