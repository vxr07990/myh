/**
 * Created by dbolin on 12/21/2016.
 */
Vtiger_Edit_Js("MiscItems_Edit_Js", {
        getInstance: function() {
            if(MiscItems_Edit_Js.currentInstance)
            {
                return MiscItems_Edit_Js.currentInstance;
            }
            MiscItems_Edit_Js.currentInstance = new MiscItems_Edit_Js();
            return MiscItems_Edit_Js.currentInstance;
        },
        I: function(){
            return MiscItems_Edit_Js.getInstance();
        },
    },
    {
        registerAddLocalCrateButtons : function() {
            var thisInstance = this;

            var defaultRowClass = 'localDefaultCrate';
            var rowId = 'localCrateRow';
            var names = ['crateID','Description','Length','Width','Height','InchesAdded', 'CratingQty', 'CratingRate', 'UncratingQty', 'UncratingRate','CratingCost','UncratingCost'];

            var addHandler = function() {
                var localContainer = jQuery(this).closest('tbody');
                var calledField = jQuery(this).attr('name');
                var regExp = /\d+/g;
                var serviceid = calledField.match(regExp);

                var newRow = localContainer.find('.'+defaultRowClass).clone(true,true);
                var sequenceNode = localContainer.find("input[name='numCrates"+serviceid[0]+"']'");
                var sequence = sequenceNode.val();
                sequence++;
                sequenceNode.val(sequence);

                newRow.removeClass('hide '+defaultRowClass);
                newRow.attr('id', rowId+sequence);
                for(var i=0; i<names.length; i++) {
                    var name = names[i]+serviceid[0];
                    if( name == 'crateID'+serviceid[0]){
                        var idnames = localContainer.find('input[name^="'+name+'"]');
                        var idList = [];
                        idList.push(0);
                        idList = idnames.map(function (index) {
                            var numId = localContainer.find(this).attr('value').match(regExp);
                            var validId = [];
                            if(numId){
                                for (var k = 0; k < numId.length; k++) {
                                    if(parseInt(numId[k]) != parseInt(serviceid[0])) {
                                        validId.push(parseInt(numId[k]));
                                    }
                                }
                                return validId;
                            }
                            return 0;
                        });
                        var next = 1;
                        var lowestTemp = 10000;
                        for (var l=0; l<idList.length; l++) {
                            var temp = parseInt(idList[l]) + 1;
                            for (var j=0; j<idList.length; j++){
                                if(parseInt(idList[j]) == temp) {
                                    temp = false;
                                }
                            }
                            if (temp != false && temp < lowestTemp){
                                lowestTemp = temp;
                            }
                        }
                        next = lowestTemp;
                        var nextStr = 'C-'+next;
                        newRow.find('input[name="'+name+'"]').attr('value', nextStr);
                        newRow.find('input[name="'+name+'"]').closest('td').find('.value').html(nextStr);
                        newRow.find('input[name="'+name+'"]').closest('td').find('.fieldname').data('prevValue', nextStr);
                        newRow.find('input[name="'+name+'"]').closest('td').find('.fieldname').val(name+'-'+sequence);
                        newRow.find('input[name="'+name+'"]').attr('name', name+'-'+sequence);
                    }
                    else {
                        newRow.find('input[name="'+name+'"]').attr('name', name+'-'+sequence);
                        newRow.find('input[name="'+name+'"]').closest('td').find('.fieldname').data('prevValue', '0');
                        newRow.find('input[name="'+name+'-'+sequence+'"]').closest('td').find('.fieldname').val(name+'-'+sequence);
                    }
                }
                newRow = newRow.appendTo(localContainer.closest('table'));
            };

            jQuery('.contentsDiv').on('click', '[id^="localAddCrate"]', addHandler);
        },

        registerCrateDimensionChange : function() {
            jQuery('input[name^="crateWidth"], input[name^="crateHeight"], input[name^="crateLength"]').off('change').on('change', function() {
                var row = jQuery(this).closest('tr');
                var padVal = 4;
                if(jQuery('[name="instance"]').val() == 'graebel'){
                    padVal = 0;
                }
                var calcLength = parseInt(row.find("input[name^='crateLength']").val()) + padVal;
                var calcWidth = parseInt(row.find("input[name^='crateWidth']").val()) + padVal;
                var calcHeight = parseInt(row.find("input[name^='crateHeight']").val()) + padVal;
                var cubic = Math.ceil((calcLength*calcWidth*calcHeight) / 1728);
                if (!isNaN(cubic)) {
                    row.find('input[name^="cube"]').val(cubic);
                } else {
                    row.find('input[name^="cube"]').val('');
                }
            });
        },

        registerAddMiscItems: function() {
            var thisInstance = this;

            var addFlatHandler = function() {
                var flatItemsTable = jQuery('#flatItemsTab');
                var flatCount = parseInt(jQuery('#interstateNumFlat').val(), 10);
                jQuery('#interstateNumFlat').val(flatCount+1);
                var newRow = jQuery('.defaultFlatItem').clone(true, true);

                var sequence = flatCount+1;
                newRow.removeClass('hide defaultFlatItem');
                newRow.attr('id', 'flatItemRow'+sequence);
                newRow.find('input.rowNumber').val(sequence);
                var name = "flatDescription";
                newRow.find('input[name="'+name+'"]').attr('name', name+sequence);
                newRow.find('.fieldname[value="'+name+'"]').val(name+sequence);
                name = "flatCharge";
                newRow.find('input[name="'+name+'"]').attr('name', name+sequence);
                newRow.find('.fieldname[value="'+name+'"]').val(name+sequence);
                name = "flatDiscounted";
                newRow.find('input[name="'+name+'"]').attr('name', name+sequence);
                newRow.find('.fieldname[value="'+name+'"]').val(name+sequence);
                name = "flatDiscountPercent";
                newRow.find('input[name="'+name+'"]').attr('name', name+sequence);
                newRow.find('.fieldname[value="'+name+'"]').val(name+sequence);
                name = "flatChargeToBeRated";
                newRow.find('input[name="'+name+'"]').attr('name', name+sequence);
                newRow = newRow.appendTo(flatItemsTable);
            };

            var addQtyRateHandler = function() {
                var qtyRateItemsTable = jQuery('#qtyRateItemsTab');
                var qtyCount = parseInt(jQuery('#interstateNumQty').val(), 10);
                jQuery('#interstateNumQty').val(qtyCount+1);
                var newRow = jQuery('.defaultQtyRateItem').clone(true, true);
                var sequence = qtyCount+1;
                newRow.removeClass('hide defaultQtyRateItem');
                newRow.attr('id', 'qtyRateItem'+sequence);
                newRow.find('input.rowNumber').val(sequence);
                var name = "qtyRateDescription";
                newRow.find('input[name="'+name+'"]').attr('name', name+sequence);
                newRow.find('.fieldname[value="'+name+'"]').val(name+sequence);
                name = "qtyRateCharge";
                newRow.find('input[name="'+name+'"]').attr('name', name+sequence);
                newRow.find('.fieldname[value="'+name+'"]').val(name+sequence);
                name = "qtyRateQty";
                newRow.find('input[name="'+name+'"]').attr('name', name+sequence);
                newRow.find('.fieldname[value="'+name+'"]').val(name+sequence);
                name = "qtyRateDiscounted";
                newRow.find('input[name="'+name+'"]').attr('name', name+sequence);
                newRow.find('.fieldname[value="'+name+'"]').val(name+sequence);
                name = "qtyRateDiscountPercent";
                newRow.find('input[name="'+name+'"]').attr('name', name+sequence);
                newRow.find('.fieldname[value="'+name+'"]').val(name+sequence);
                name = "qtyChargeToBeRated";
                newRow.find('input[name="'+name+'"]').attr('name', name+sequence);
                newRow = newRow.appendTo(qtyRateItemsTable);
            };

            var addCrateHandler = function() {
                var cratesTable = jQuery('#cratesTab');
                var crateCount = parseInt(jQuery('#interstateNumCrates').val(), 10);
                jQuery('#interstateNumCrates').val(crateCount+1);
                var newRow = jQuery('.defaultCrate').clone(true, true);
                var sequence = crateCount+1;

                newRow.removeClass('hide defaultCrate');
                newRow.attr('id', 'crate'+sequence);
                newRow.find('input.rowNumber').val(sequence);
                var name = "crateID";
                var idInt = 1;
                while(true) {
                    var matchFound = false;
                    jQuery('input[name*="'+name+'"]').each(function() {
                        if(jQuery(this).val() == 'C-'+idInt) {
                            matchFound = true;
                            idInt++;
                        }
                    });
                    if(!matchFound) {break;}
                }
                newRow.find('input[name="'+name+'"]').val('C-'+idInt);
                newRow.find('input[name="'+name+'"]').closest('td').find('.value').html('C-'+idInt);
                newRow.find('input[name="'+name+'"]').attr('name', name+sequence);
                newRow.find('.fieldname[value="'+name+'"]').val(name+sequence);
                name = "crateDescription";
                newRow.find('input[name="'+name+'"]').attr('name', name+sequence);
                newRow.find('.fieldname[value="'+name+'"]').val(name+sequence);
                name = "crateLength";
                newRow.find('input[name="'+name+'"]').attr('name', name+sequence);
                newRow.find('.fieldname[value="'+name+'"]').val(name+sequence);
                name = "crateWidth";
                newRow.find('input[name="'+name+'"]').attr('name', name+sequence);
                newRow.find('.fieldname[value="'+name+'"]').val(name+sequence);
                name = "crateHeight";
                newRow.find('input[name="'+name+'"]').attr('name', name+sequence);
                newRow.find('.fieldname[value="'+name+'"]').val(name+sequence);
                name = "cratePack";
                newRow.find('input[name="'+name+'"]').attr('name', name+sequence);
                newRow.find('.fieldname[value="'+name+'"]').val(name+sequence);
                name = "crateUnpack";
                newRow.find('input[name="'+name+'"]').attr('name', name+sequence);
                newRow.find('.fieldname[value="'+name+'"]').val(name+sequence);
                name = "crateOTPack";
                newRow.find('input[name="'+name+'"]').attr('name', name+sequence);
                newRow.find('.fieldname[value="'+name+'"]').val(name+sequence);
                name = "crateOTUnpack";
                newRow.find('input[name="'+name+'"]').attr('name', name+sequence);
                newRow.find('.fieldname[value="'+name+'"]').val(name+sequence);
                name = "crateDiscountPercent";
                newRow.find('input[name="'+name+'"]').attr('name', name+sequence);
                newRow.find('.fieldname[value="'+name+'"]').val(name+sequence);
                name = "crateDiscounted";
                newRow.find('input[name="'+name+'"]').attr('name', name+sequence);
                newRow.find('.fieldname[value="'+name+'"]').val(name+sequence);
                name = "crateApplyTariff";
                newRow.find('input[name="'+name+'"]').attr('name', name+sequence);
                newRow.find('.fieldname[value="'+name+'"]').val(name+sequence);
                name = "crateCustomRateAmount";
                newRow.find('input[name="'+name+'"]').attr('name', name+sequence);
                newRow.find('.fieldname[value="'+name+'"]').val(name+sequence);
                name = "crateCustomRateAmountUnpack";
                newRow.find('input[name="'+name+'"]').attr('name', name+sequence);
                newRow.find('.fieldname[value="'+name+'"]').val(name+sequence);
                newRow = newRow.appendTo(cratesTable);
                thisInstance.registerCrateDimensionChange();
            };

            jQuery('.contentsDiv').on('click', '#addFlatChargeItem', addFlatHandler);
            jQuery('.contentsDiv').on('click', '#addFlatChargeItem2', addFlatHandler);

            jQuery('.contentsDiv').on('click', '#addQtyRateChargeItem', addQtyRateHandler);
            jQuery('.contentsDiv').on('click', '#addQtyRateChargeItem2', addQtyRateHandler);

            jQuery('.contentsDiv').on('click', '#addCrate', addCrateHandler);
            jQuery('.contentsDiv').on('click', '#addCrate2', addCrateHandler);
        },

        registerAddCorpVehicle: function () {
            var thisInstance = this;
            //console.dir('registering local add vehicles');
            var addVehicleHandler = function () {
                var defaultVehicle = jQuery('.defaultVehicle');
                var newVehicle = defaultVehicle.clone().removeClass('defaultVehicle hide').attr('blocktoggleid','corpVehicleTable').appendTo('table[name="corpVehicleTable"]');
                //console.dir('registering local vehicles animation');
                //thisInstance.registerVehicleAnimationEvent();
                //console.dir('registering local vehicles delete');
                //thisInstance.registerDeleteVehicleEvent();
                //thisInstance.registerLookupByVIN();
                var vehicleCounter = jQuery('#numCorporateVehicles');
                var vehicleCount = vehicleCounter.val();
                vehicleCount++;
                vehicleCounter.val(vehicleCount);
                newVehicle.find('.vehicleTitle').find('b').append(' ' + vehicleCount);
                newVehicle.addClass('vehicle_' + vehicleCount);
                newVehicle.find('input, select, button, textarea').each(function () {
                    jQuery(this).attr('name', jQuery(this).attr('name') + '_' + vehicleCount);
                    if (jQuery(this).attr('name') == 'vehicle_id_' + vehicleCount) {
                        jQuery(this).val(vehicleCount);
                    }
                    jQuery(this).attr('id', jQuery(this).attr('id') + '_' + vehicleCount);
                    if (jQuery(this).prop('nodeName') == 'SELECT') {
                        jQuery(this).chosen();
                    }
                });
                //bind the special handler for Service after making the picklist named correctly
                //jQuery('select[name="vehicle_service_' + vehicleCount + '"]').siblings('.chzn-container').find('.chzn-results').on('mouseup', thisInstance.corpVehicleServiceHandler);
                //jQuery('input[name^="vehicle_weight_' + vehicleCount + '"]').on('change', thisInstance.corpVehicleWeightHandler);
            };

            jQuery('.contentsDiv').on('click', '#addCorpVehicle', addVehicleHandler);
            jQuery('.contentsDiv').on('click', '#addCorpVehicle2', addVehicleHandler);
        },

        registerVehiclesEventsForLoaded: function () {
            jQuery('.contentsDiv').on('mouseup', 'select[name^="vehicle_service_"] ~ .chzn-container .chzn-results',
                this.corpVehicleServiceHandler);
            //jQuery('select[name^="vehicle_service_"]').siblings('.chzn-container').find('.chzn-results').on('mouseup', thisInstance.corpVehicleServiceHandler).trigger('mouseup');
            jQuery('.contentsDiv').on('value_change', 'input[name^="vehicle_weight_"]', this.corpVehicleWeightHandler);
            //jQuery('input[name^="vehicle_weight_"]').on('change', thisInstance.corpVehicleWeightHandler);
        },

        corpVehicleWeightHandler: function () {
            var selectedId = jQuery(this).attr('name').split('_')[2];
            jQuery('input[name="vehicle_cube_' + selectedId + '"]').val(parseInt(jQuery(this).val() / 7));
        },

        corpVehicleServiceHandler: function () {
            var currentTdElement = jQuery(this).closest('td');
            var vehicle_id = currentTdElement.find('select[name^="vehicle_service_"]').attr('name').split('_')[2];
            var selected = currentTdElement.find('.result-selected').html();
            var optionId = currentTdElement.find('.result-selected').attr('id').split('_')[5];
            var selectedId = currentTdElement.find('option:eq(' + optionId + ')').val();
            //console.dir(selectedId);
            //stop anything from being disabled, only disable when it makes sense
            jQuery('input:disabled').prop('disabled', false);
            switch (selectedId) {
                case 'Contract':
                    break;
                case 'Budget':
                    jQuery('input:checkbox[name="vehicle_car_on_van_' + vehicle_id + '"]').prop('checked', false).prop('disabled', true);
                    jQuery('input:checkbox[name="vehicle_inoperable_' + vehicle_id + '"]').prop('checked', false).prop('disabled', true);
                default:
                    jQuery('input[name="vehicle_charge_' + vehicle_id + '"]').val('0.00').prop('disabled', true);
                    break;
            }
        },

        registerDeleteVehicleEvent: function () {
            var thisInstance = this;
            jQuery('.contentsDiv').on('click', '.deleteVehicleButton', function () {
                var bodyContainer = jQuery(this).closest('tbody');
                var vehicleId = jQuery(this).closest('tbody').find('input:hidden[name^="vehicle_id_"]').val();
                if (vehicleId && vehicleId != 'none') {
                    jQuery('table[name="corpVehicleTable"]').append('<input type="hidden" name="removeVehicle_' + vehicleId + '" value="' + vehicleId + '" />');
                }
                bodyContainer.remove();
                if($('[name="instance"]').val() == 'sirva') {
                    thisInstance.updateAutoBulkies();
                }
            });
        },

        registerVehicleAnimationEvent: function () {
            var thisInstance = this;
            //console.dir('in the register bit here');
            jQuery('.contentsDiv').on('click', '.vehicleToggle', function (e) {
                var currentTarget = jQuery(e.currentTarget);
                var blockId = currentTarget.data('id');
                var closestBlock = currentTarget.closest('.vehicleBlock');
                var bodyContents = closestBlock.find('.vehicleContent');
                var data = currentTarget.data();
                var module = app.getModuleName();
                var hideHandler2 = function () {
                    bodyContents.hide('slow');
                    app.cacheSet(module + '.' + blockId, 0);
                };
                var showHandler2 = function () {
                    bodyContents.show();
                    app.cacheSet(module + '.' + blockId, 1);
                };
                if (data.mode == 'show') {
                    hideHandler2();
                    currentTarget.hide();
                    closestBlock.find("[data-mode='hide']").show();
                } else {
                    showHandler2();
                    currentTarget.hide();
                    closestBlock.find("[data-mode='show']").show();
                }
            });
        },

        registerAddVehicleButtons : function() {
            var thisInstance = this;

            var defaultRowClass = 'newVehicleRow';
            var rowId = 'vehicleRow';
            //TODO need to need to find out which set of variables are required (depends on chosen estimate)
            //var names = ['vehicleDescription','vehicleWeight'];
            var names = ['vehicleRateType','vehicleDescription', 'vehicleSITDays', 'vehicleWeight'];
            var addHandler = function() {
                var localContainer = jQuery(this).closest('tbody');
                var calledField = jQuery(this).attr('name');

                var newRow = localContainer.find('.'+defaultRowClass).clone(true,true);
                var sequenceNode = jQuery('#numSirvaVehicles');
                var sequence = sequenceNode.val();
                sequence++;
                sequenceNode.val(sequence);

                newRow.removeClass('hide '+defaultRowClass);
                newRow.attr('id', rowId+'-'+sequence);
                for(var i=0; i<names.length; i++) {
                    var name = names[i];//+serviceid[0];
                    newRow.find('input[name="'+name+'"], select[name="'+name+'"]').attr('name', name+'-'+sequence).removeClass('chzn-done');
                }
                newRow = newRow.appendTo(localContainer.closest('table'));
                newRow.find('select').chosen();
                if($('[name="instance"]').val() == 'sirva') {
                    thisInstance.updateAutoBulkies();
                }
            };

            //var buttons = jQuery('[id^="addVehicle"]');
            //buttons.off('click').on('click', addHandler);
            jQuery('.contentsDiv').on('click', '[id^="addVehicle"]', addHandler);
        },

        updateAutoBulkies : function(){
            if( jQuery('[name="shipper_type"]').val() != 'NAT'){
                jQuery('#vehiclesTab select[name="vehicleDescription"] option').each(function(index) {
                    var bulkyId = jQuery( this ).data('bulky');
                    var bulkyTotal = jQuery('#vehiclesTab select:not([name="vehicleDescription"]) option:selected[data-bulky="'+bulkyId+'"]').length;
                    jQuery('#Estimates_editView_fieldName_bulky'+bulkyId).val(bulkyTotal);
                });
            }
        },

        registerAutoBulkyInit : function(){
            var thisInstance = this;
            if( jQuery('[name="shipper_type"]').val() != 'NAT'){
                jQuery('#vehiclesTab select[name="vehicleDescription"] option').each(function(index) {
                    jQuery('#Estimates_editView_fieldName_bulky'+jQuery( this ).data('bulky')).attr('readonly', true);
                });
            }
            jQuery('#vehiclesTab select[name^="vehicleDescription"]').on('change', function(){
                thisInstance.updateAutoBulkies();
            });
            this.updateAutoBulkies();
        },

        deleteMiscItem : function(currentRow, serviceid, rowNum) {
            var newInput = jQuery('<input>').attr({
                type: 'hidden',
                class: 'hide',
                name: 'deleteRow' + serviceid + '-' + rowNum
            });
            currentRow.closest('table').append(newInput);

            var lineItemId = currentRow.find('.lineItemId').val();
            if (!lineItemId) lineItemId = currentRow.find('input[name^="vehicleID"]').val();//Second check for vehicles
            //Third check for locals
            var extraParams = '';
            if (currentRow.attr('class') == 'localCrateRow') {
                lineItemId = currentRow.find('input[name^="crateID"]').prop('name');
                var record = jQuery('input[name="record"]').val();
                extraParams = '&estimateid='+record;
            }

            if (lineItemId) {
                var dataURL = 'index.php?module=' + Estimates_Edit_Js.I().moduleName + '&action=DeleteMiscItem&rowType=' + currentRow.attr('class') + '&lineItemId=' + lineItemId+extraParams;

                AppConnector.request(dataURL).then(
                    function (data) {
                        if (data.success) {
                            currentRow.remove();
                        }
                    },
                    function (error) {
                    }
                );
            } else {
                currentRow.remove();
            }
        },

        registerDeleteMiscItemClickEvent : function() {
            var thisInstance = this;
            jQuery('.contentsDiv').on('click', 'table.misc .icon-trash', function(e) {
                var currentRow = jQuery(e.currentTarget).closest('tr');
                rowId = currentRow.attr('id');
                var regExp = /\d+/g;
                var rowNumbers = rowId.match(regExp);
                var serviceid = rowNumbers[0];
                var rowNum = rowNumbers[1];

                thisInstance.deleteMiscItem(currentRow, serviceid, rowNum);
            });
        },

        registerEvents : function()
        {
            this.registerAddLocalCrateButtons();
            this.registerAddMiscItems();
            this.registerCrateDimensionChange();
            this.registerDeleteMiscItemClickEvent();

            this.registerAddCorpVehicle();
            this.registerAddVehicleButtons();
            this.registerDeleteVehicleEvent();
            this.registerVehiclesEventsForLoaded();
            this.registerVehicleAnimationEvent();
            if($('[name="instance"]').val() == 'sirva') {
                this.registerAutoBulkyInit();
            }
        }
    }
);
