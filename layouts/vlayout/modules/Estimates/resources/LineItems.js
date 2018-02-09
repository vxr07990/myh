/**
 * Created by dbolin on 9/21/2016.
 */

jQuery.Class("LineItems_Js", {

    addNewLineItem: function(oldLineItems, newLineItem, afterThisItem)
    {
        jQuery(newLineItem).find('input[name^="detaillineitemid"]').val('');
        var nextRowIndex = 1;
        oldLineItems.find('tr').not('.defaultLineItemRow, .innerRow').each(function(){
            if(jQuery('input[name="rowNumber"]', this).val() >= nextRowIndex)
            {
                nextRowIndex = Number(jQuery('input[name="rowNumber"]', this).val()) + 1;
            }
        });
        jQuery(newLineItem).find('input[name="rowNumber"]').val(nextRowIndex);

        var highestSequence = 1;
        oldLineItems.find('tr').not('.defaultLineItemRow, .innerRow').each(function(){
            if(jQuery('input[name^="invoice_sequence"]', this).val() >= highestSequence)
            {
                highestSequence = Number(jQuery('input[name^="invoice_sequence"]', this).val()) + 1;
            }
            if(jQuery('input[name^="distribution_sequence"]', this).val() >= highestSequence)
            {
                highestSequence = Number(jQuery('input[name^="distribution_sequence"]', this).val()) + 1;
            }
        });
        //jQuery(newLineItem).find('input[name^="invoice_sequence"]').val(highestSequence);
        //jQuery(newLineItem).find('input[name^="distribution_sequence"]').val(highestSequence);

        var updateF = function(){
            var name = jQuery(this).attr('name');
            if(typeof name == 'undefined'){
                return;
            }
            var oldName = name;
            var index = name.search(/\d/);
            var secondIndex = name.indexOf('_', index);
            if(index > 0) {
                if(secondIndex > 0) {
                    var secondNumber = name.substr(secondIndex + 1);
                }
                name = name.substr(0, index) + nextRowIndex;
                if(typeof secondNumber != 'undefined')
                {
                    name = name + '_' + secondNumber;
                }
                jQuery(this).attr('name', name);
            }
            // check for a corresponding div
            var div = jQuery('div[name="' + oldName + '"]', jQuery(this).parent());
            if(div.length > 0)
            {
                div.attr('name', name);
            }
        };
        jQuery('input,select', newLineItem).each(updateF);
        jQuery(newLineItem).addClass("usedRow").attr('id','insertedRow' + nextRowIndex);
        if(typeof afterThisItem == 'undefined') {
            oldLineItems.find('tr:last').prev('tr').before(newLineItem);
        } else {
            afterThisItem.after(newLineItem);
        }
        var res = jQuery('#insertedRow' + nextRowIndex);

        return res;
    },

    updateTabIndex: function()
    {
        var firstTabIndex = null;
        jQuery('.lineItemsEdit').find('input,select').each(function() {
            var tabIndex = jQuery(this).attr('tabindex');
            if(!firstTabIndex && typeof tabIndex != 'undefined' && tabIndex > 0)
            {
                firstTabIndex = tabIndex;
            } else if(firstTabIndex)
            {
                jQuery(this).attr('tabindex', ++firstTabIndex);
            }
        });
    },

    processRateResult: function(lineItems, isDetailView){
        var thisInstance = this;
        var getShortest = function(sel)
        {
            var returnIndex = 0;
            var len = 10000;
            for(var i = 0; i < sel.length; i++)
            {
                var l = jQuery(sel[i]).attr('name').length;
                if(l < len)
                {
                    len = l;
                    returnIndex = i;
                }
            }
            return jQuery(sel[returnIndex]);
        }
        var toMatch = ['tariffitem', 'tariffsection', 'section', 'description'];
        var toCopy = ['baserate',
                    'unitrate',
                    'quantity',
                    'gross',
                    'unitOfMeasurement',
                    'invoicecostnet',
                    'distributablecostnet',
                    'invoicediscountpct',
                    'distributablediscountpct',
                    'location',
                    'gcs_flag',
                    'metro_flag',
                    'item_weight',
                    'rate_net',
                    'invoiceable',
                    'distributable'
        ];
        var excluded = ['serviceProvider', 'role', 'roleID'];
        var newLineItems = jQuery(lineItems);
        var oldLineItems = jQuery('.lineItemsEdit');
        oldLineItems.find('thead').html(newLineItems.find('thead').html());
        oldLineItems.find('tr').not('.defaultLineItemRow, .innerRow').first().addClass('usedRow');
        oldLineItems.find('tbody').find('tr').not('.defaultLineItemRow, .innerRow').first().addClass('usedRow');
        // mark all invoiced line items as used
        var invoicedLines = jQuery('input[name^="invoicedone"], input[name^="distributed"]', oldLineItems).filter(function(){
            return jQuery(this).attr('checked');
        }).closest('tr');
        invoicedLines.addClass('usedRow');

        newLineItems.find('tr').not('.defaultLineItemRow, .innerRow').each(function() {
            // skip header row
            if(jQuery('.row-fluid', this).length == 0) {
                return;
            }

            // search for matching old line item
            var newMatchData = new Object();
            for(var m in toMatch)
            {
                newMatchData[toMatch[m]] = jQuery('input[name^="' + toMatch[m] + '"]', this).val();
            }
            var match = oldLineItems.find('tr').not('.defaultLineItemRow, .innerRow, .deletedRow').filter(function() {
                for(var m in toMatch)
                {
                    var v = jQuery('input[name^="' + toMatch[m] + '"]', this);
                    if(v.length == 0){
                        return false;
                    }
                    // hack
                    if(v.val().replace(' - Item Adjustment','') != newMatchData[toMatch[m]])
                    {
                        return false;
                    }
                }
                return true;
            });
            var primaryMatch = match.filter(function(){
                return jQuery('input[name^="description"]', this).val().indexOf(' - Item Adjustment') == -1;
            });
            if(match.length > 0 && primaryMatch.length > 0) {
                jQuery(match).addClass('usedRow');
                // check if the item is invoiced
                // if it is, we need to create an adjustment line item instead of updating the old one
                // or, if there is a non-invoiced adjustment line item, we can update that one
                // updated to check distributed as well
                var invoiced = jQuery('input[name^="invoicedone"]', primaryMatch).attr('checked')
                                || jQuery('input[name^="distributed"]', primaryMatch).attr('checked');
                var lineToUpdate = false;
                var newInvoice = false;
                var newDistribution = false;
                if(invoiced)
                {
                    // check to see if we can update an adjustment line item
                    var lines = jQuery(match).filter(function() {
                       return !(jQuery('input[name^="invoicedone"]', this).attr('checked') || jQuery('input[name^="distributed"]', this).attr('checked'));
                    });
                    var totalInvoice = 0;
                    var totalDistribution = 0;
                    // calculate the delta
                    jQuery(match).each(function() {
                       if(lines.has(this).length > 0)
                       {
                           // not invoiced, skip
                           return;
                       }
                       totalInvoice += Number(jQuery('input[name^="invoicecostnet"]', this).val().replace(',',''));
                       totalDistribution += Number(jQuery('input[name^="distributablecostnet"]', this).val().replace(',',''));
                    });
                    newInvoice = (Number(jQuery('input[name^="invoicecostnet"]', this).val().replace(',','')) - totalInvoice).toFixed(2);
                    newDistribution = (Number(jQuery('input[name^="distributablecostnet"]', this).val().replace(',','')) - totalDistribution).toFixed(2);
                    if(lines.length > 0)
                    {
                        // just update the adjustment line item, since it's not invoiced
                        lineToUpdate = lines[0];
                        // the updated line is already counted against the difference, so just add them here
                        newInvoice = (Number(newInvoice) + Number(jQuery('input[name^="invoicecostnet"]', lineToUpdate).val().replace(',',''))).toFixed(2);
                        newDistribution = (Number(newDistribution) + Number(jQuery('input[name^="distributablecostnet"]', lineToUpdate).val().replace(',',''))).toFixed(2);
                    } else if (newDistribution != 0 || newInvoice != 0) {
                        // have to clone the line item, update the row index numbers, and then let it update the distribution
                        var newLine = primaryMatch.clone();
                        // fix select
                        newLine.find('.chzn-container').remove();
                        var selectTag = newLine.find('select');
                        selectTag.removeClass('chzn-done').removeAttr('tabindex').removeAttr('id').removeAttr('style');
                        var prevSelected = primaryMatch.find('select');
                        for(var i=0;i<prevSelected.length;i++)
                        {
                            var opt = jQuery(prevSelected[i]).val();
                            jQuery(selectTag[i]).find('option:selected').prop('selected', false);
                            jQuery(selectTag[i]).find('option').filter(function() {
                                return jQuery(this).val() == opt;
                            }).prop('selected', true);
                        }

                        jQuery('input[name^="invoicedone"]', newLine).attr('checked',false);
                        jQuery('input[name^="distributed"]', newLine).attr('checked',false);
                        jQuery('input[name^="description"]', newLine).val(jQuery('input[name^="description"]', newLine).val() + ' - Item Adjustment');
                        lineToUpdate = thisInstance.addNewLineItem(oldLineItems, newLine, match.last());

                        jQuery('.chosen-select').not('.chzn-done').chosen();
                        jQuery('select', lineToUpdate).trigger('change');
                    }
                } else {
                    // just update the primary line if not invoiced
                    lineToUpdate = primaryMatch;
                }
                if(lineToUpdate)
                {
                    for(var c in toCopy) {
                        var dest = getShortest(jQuery('input[name^="' + toCopy[c] + '"]', lineToUpdate));
                        var source = getShortest(jQuery('input[name^="' + toCopy[c] + '"]', this));
                        // hack
                        if(toCopy[c] == 'invoiceable' || toCopy[c] == 'distributable')
                        {
                            if(source.is(':checked') || source.attr('checked'))
                            {
                                dest.attr('checked', true);
                            } else {
                                dest.attr('checked', false);
                            }
                        } else {
                            dest.val(source.val());
                        }
                    }
                    // if we need to override the distribution
                    if(newInvoice !== false)
                    {
                        jQuery('input[name^="invoicecostnet"]', lineToUpdate).val(newInvoice);
                    }
                    if(newDistribution !== false)
                    {
                        jQuery('input[name^="distributablecostnet"]', lineToUpdate).val(newDistribution);
                    }
                    // Don't need to automatically recalculate anymore
                    //thisInstance.serviceProvidersCalcAmount(jQuery(lineToUpdate));
                }
            } else {
                thisInstance.addNewLineItem(oldLineItems, this);
            }
        });
        oldLineItems.find('tr').last().remove();
        oldLineItems.find('tr').last().remove();
        oldLineItems.find('tr').not('.usedRow, .defaultLineItemRow, .innerRow').addClass('deletedRow hide').find('input[name^="deleted"]').val('yes');
        newLineItems.find('tr').not('.innerRow').last().prev('tr').appendTo(oldLineItems.find('tbody').not('.innerTableBody'));
        newLineItems.find('tr').not('.innerRow').last().appendTo(oldLineItems.find('tbody').not('.innerTableBody'));
        // copy hidden inputs to divs when applicable
        oldLineItems.find('.usedRow').find('input').each(function () {
            var n = jQuery(this).attr('name');
            if(typeof n == 'undefined')
            {
                return;
            }

            for (var x in excluded) {
                if (n.indexOf(excluded[x]) != -1) {
                    return;
                }
            }

            var div = jQuery('div[name="' + n + '"]', jQuery(this).closest('td.narrowWidthType'));
            if (div.length > 0) {
                if (jQuery(this).hasClass('booleanInput')) {
                    if (jQuery(this).attr('checked')) {
                        div.text('Yes');
                    } else {
                        div.text('No');
                    }
                } else {
                    div.text(jQuery(this).val());
                }
            }
        });

        oldLineItems.find('.usedRow').removeClass('usedRow');
        thisInstance.updateTabIndex();
        thisInstance.registerLineItemEvents();

        // save rerate in detail view
        if(isDetailView) {
            this.saveDetailLineItems();
        } else {
        var highestRowIndex = 0;
        oldLineItems.find('tr').not('.defaultLineItemRow, .innerRow').each(function () {
            if (jQuery('input[name="rowNumber"]', this).val() >= highestRowIndex) {
                highestRowIndex = Number(jQuery('input[name="rowNumber"]', this).val()) + 1;
            }
        });
            jQuery('input[name="detailLineItemCount"]').val(highestRowIndex);
        }
    },

    saveDetailLineItems : function () {
        var highestRowIndex = 0;
        jQuery('.lineItemsEdit').find('tr').not('.defaultLineItemRow, .innerRow').each(function () {
            if (jQuery('input[name="rowNumber"]', this).val() >= highestRowIndex) {
                highestRowIndex = Number(jQuery('input[name="rowNumber"]', this).val()) + 1;
            }
        });
            var params = new Object();
            params.url = 'index.php?module=Estimates&action=SaveDetailLineItems&record=' + getQueryVariable('record');
            params.data = new Object();
            jQuery('input').each(function () {
                params.data[jQuery(this).attr('name')] = jQuery(this).val();
            });
            params.data['detailLineItemCount'] = highestRowIndex;
            AppConnector.request(params);
    },

    registerDetailLineAddNewLineEvent : function () {
        var thisInstance = this;
        jQuery('.addNewLineItem').off('click').on('click', function (){
            var lineItems = jQuery('.lineItemsEdit');
            var lastLine = lineItems.find('.defaultLineItemRow');
            var newLine = lastLine.clone();
            newLine.removeClass('hide').removeClass('defaultLineItemRow');
            // fix select
            newLine.find('.chzn-container').remove();
            var selectTag = newLine.find('select');
            selectTag.removeClass('chzn-done').removeAttr('tabindex').removeAttr('id').removeAttr('style');

            newLine.find('input').prop('readonly', false);
            newLine.find('select').prop('readonly', false);
            newLine.find('input').prop('disabled', false);
            newLine.find('select').prop('disabled', false);
            newLine.find('input[name^="invoicedone"]').prop('disabled', true);
            newLine.find('input[name^="distributed"]').prop('disabled', true);

            var divs = jQuery('.serviceProviderDiv', newLine);
            for(var i=1;i<divs.length;i++)
            {
                jQuery(divs[i].remove());
            }

            var lineToUpdate = thisInstance.addNewLineItem(lineItems, newLine);
            lineToUpdate.removeClass('usedRow');

            jQuery('.chosen-select').not('.chzn-done').chosen();

            thisInstance.updateTabIndex();
            thisInstance.registerLineItemEvents();

            jQuery('select', lineToUpdate).trigger('change');
            var highestRowIndex = 0;
            lineItems.find('tr').not('.defaultLineItemRow, .innerRow').each(function () {
                if (jQuery('input[name="rowNumber"]', this).val() >= highestRowIndex) {
                    highestRowIndex = Number(jQuery('input[name="rowNumber"]', this).val()) + 1;
                }
            });
            jQuery('input[name="detailLineItemCount"]').val(highestRowIndex);
        });
    },

    registerLineItemEvents : function() {
        this.registerDetailLineParticipantRoleChangeEvent();
        this.registerDetailLineServiceProviderChangeEvent();
        this.registerDetailLineApprovalFields();
        this.registerDetailLineBaseRateChangeEvent();
        this.registerDetailLineUnitRateChangeEvent();
        this.registerDetailLineQuantityChangeEvent();
        this.registerInvoiceDiscountChange();
        this.registerDistributionDiscountChange();
        this.registerDetailLineAddServiceProviderEvent();
        this.registerDetailLineServiceProviderDeleteEvent();
        this.registerDetailLineServiceProviderSplitEvents();
        this.registerDetailLineAddNewLineEvent();
        this.registerDetailLineUpdateReadyToEvent();
        jQuery('.chosen-select').chosen();
        app.registerEventForDatePickerFields(jQuery('.detailDateField'), true);
    },

    registerMoveHQLineItemEvents : function() {
        jQuery('.contentsDiv').on('click', '.lineItemSectionToggleShow', function () {
            var section = jQuery(this).closest('tr').data('rollup');
            jQuery('.section_' + section).removeClass('hide');
            jQuery(this).addClass('hide');
            jQuery(this).parent().find('.lineItemSectionToggleHide').removeClass('hide');
        });
        jQuery('.contentsDiv').on('click', '.lineItemSectionToggleHide', function () {
            var section = jQuery(this).closest('tr').data('rollup');
            jQuery('.section_' + section).addClass('hide');
            jQuery(this).addClass('hide');
            jQuery(this).parent().find('.lineItemSectionToggleShow').removeClass('hide');
        });
    },

    //This is used for detail line items, so when a user selects a ROLE (Participating Agent) it auto sets the agency name.
    registerDetailLineParticipantRoleChangeEvent : function() {
        var thisInstance = this;
        var selectTag = jQuery('.rolePicklist');
        selectTag.off('change').on('change', function () {
            PARoleSelected = jQuery(this).find('option:selected').val();
            if (PARoleSelected) {
                currentRow = jQuery(this).closest('tr');
                mappedValueID = jQuery('.rolemap[name="' + PARoleSelected + 'agents_id"]').val();
                mappedValueName = jQuery('.rolemap[name="' + PARoleSelected + 'name"]').val();
                // //This was when it was thought the serviceprovider was the role, that's not the thing now.
                // //serviceProviderDiv = currentRow.find('div[name^="serviceprovider"]');
                // //serviceProviderInput = currentRow.find('input[name^="serviceprovider"]');
                // //serviceProviderDiv.html(mappedValueName);
                // //serviceProviderInput.val(mappedValueID);
                // roleNameDiv = currentRow.find('div[name^="roleName"]');
                // roleNameDiv.html(mappedValueName);
                // roleNameInput = currentRow.find('input[name^="roleName"]');
                // roleNameInput.val(mappedValueID);
                thisInstance.updateDetailLineItemField(currentRow, 'roleID', mappedValueName, mappedValueID);
            }
        });
    },

    //This is used for detail line items, so when a user selects a Service Provider it auto sets the Provider's Name.
    registerDetailLineServiceProviderChangeEvent : function(){
        var thisInstance = this;
        var selectTag = jQuery('.serviceProviderPicklist');
        selectTag.off('change').on('change', function() {
            var PARoleSelected = jQuery(this).find('option:selected').val();
            var currentRow = jQuery(this).closest('div.serviceProviderDiv');
            if (PARoleSelected) {
                var mappedValueID = jQuery('.moverolemap[name="'+PARoleSelected+'id"]').val();
                var mappedValueName = jQuery('.moverolemap[name="'+PARoleSelected+'name"]').val();
                var mappedValueICode = jQuery('.moverolemap[name="'+PARoleSelected+'icode"]').val();
                //serviceProviderDiv = currentRow.find('div[name^="serviceproviderName"]');
                //serviceProviderInput = currentRow.find('input[name^="serviceproviderName"]');
                //serviceProviderDiv.html(mappedValueName);
                //serviceProviderInput.val(mappedValueID);
                thisInstance.updateDetailLineItemField(currentRow, 'serviceProvider', mappedValueName + ' - ' + mappedValueICode, mappedValueID);
            } else {
                thisInstance.updateDetailLineItemField(currentRow, 'serviceProvider', '', '');
            }
        });
    },

    //This is used for detail line items, so a user can multi-select the approval fields.
    registerDetailLineApprovalFields : function(){
        var selectTag = jQuery('.approvalPicklistMaster');
        selectTag.off('change').on('change', function() {
            approvalRoleSelected = jQuery(this).find('option:selected').val();
            if (approvalRoleSelected) {
                ///OOOOHHHHHHHHHH!!!!
                //jQuery('.approvalPicklist').val(approvalRoleSelected);
                jQuery('.approvalPicklist').find('option:selected').prop('selected', false).closest('select').find('option[value="' + approvalRoleSelected + '"]').prop('selected', true).closest('select').trigger('liszt:updated');
                //a dumb way...
                //jQuery('.approvalPicklist option['+approvalRoleSelected+']').attr('selected', true);
            }
        });
    },

    registerDetailLineBaseRateChangeEvent : function(){
        var thisInstance = this;
        var selectedTag = jQuery('input[name^="baserate"]');
        selectedTag.off('change').on('change', function() {
            var changedFieldType = 'baserate';
            thisInstance.handleDetailLineCalculations(this, changedFieldType);
        });
    },

    registerDetailLineUnitRateChangeEvent : function(){
        var thisInstance = this;
        var selectedTag = jQuery('input[name^="unitrate"]');
        selectedTag.off('change').on('change', function() {
            var changedFieldType = 'unitrate';
            thisInstance.handleDetailLineCalculations(this, changedFieldType);
        });
    },

    registerDetailLineQuantityChangeEvent : function(){
        var thisInstance = this;
        var selectedTag = jQuery('input[name^="quantity"]');
        selectedTag.off('change').on('change', function () {
            var changedFieldType = 'quantity';
            thisInstance.handleDetailLineCalculations(this, changedFieldType);
        });
    },

    handleDetailLineCalculations : function(changedField, changedFieldType){
        var thisInstance = this;
        var currentRow = jQuery(changedField).closest('tr');
        var changedValue = jQuery(changedField).val();
        var zeroFixed = (0).toFixed(2);
        if(changedValue === undefined || changedFieldType === undefined){
            return false;
                }
        switch(changedFieldType){
            case 'baserate':
                var newGross = changedValue;
                currentRow.find('input[name^="quantity"]').val(zeroFixed);
                currentRow.find('input[name^="unitrate"]').val(zeroFixed);
                break;
            case 'unitrate':
                var quantity = currentRow.find('input[name^="quantity"]').val();
                var newGross = quantity * changedValue;
                currentRow.find('input[name^="baserate"]').val(zeroFixed);
                break;
            case 'quantity':
                var unitRate = currentRow.find('input[name^="unitrate"]').val();
                var newGross = unitRate * changedValue;
                currentRow.find('input[name^="baserate"]').val(zeroFixed);
                break;
        }
        if (newGross) {
            var newInvoiceCostNet = 0;
            var newDistributableCostNet = 0;
            var invoicedDiscountField = currentRow.find('input[name^="invoicediscountpct"]');
            var distributableDiscountField = currentRow.find('input[name^="distributablediscountpct"]');

            if(jQuery('[name^="invoiceable"]', currentRow).is(':checked')){
                newInvoiceCostNet = newGross;
            }  else {
                invoicedDiscountField.val(undefined);
            }
            if(jQuery('[name^="distributable"]', currentRow).is(':checked')) {
                newDistributableCostNet = newGross;
            } else {
                distributableDiscountField.val(undefined);
                }

            var invoicedDiscountPct = invoicedDiscountField.val();
            var distributableDiscountPct = distributableDiscountField.val();

            if (invoicedDiscountPct) {
                newInvoiceCostNet = thisInstance.calcDiscounted(newInvoiceCostNet, invoicedDiscountPct);
            }

            if (distributableDiscountPct) {
                newDistributableCostNet = thisInstance.calcDiscounted(newDistributableCostNet, distributableDiscountPct);
            }

            thisInstance.updateDetailLineItemField(currentRow, 'gross', newGross, newGross);
            thisInstance.updateDetailLineItemField(currentRow, 'invoicecostnet', newInvoiceCostNet, newInvoiceCostNet);
            thisInstance.updateDetailLineItemField(currentRow, 'distributablecostnet', newDistributableCostNet, newDistributableCostNet);
        }
    },

    registerInvoiceDiscountChange : function(){
        var thisInstance = this;
        var selectedTag = jQuery('input[name^="invoicediscountpct"]');
        selectedTag.off('change').on('change', function() {
            var currentRow = jQuery(this).closest('tr');
            var discountPct = jQuery('input[name^="invoicediscountpct"]', currentRow).val();
            if(jQuery('[name^="invoiceable"]', currentRow).is(':checked') && discountPct) {
                if(invoiceGross = jQuery('input[name^="gross"', currentRow).val().replace(/,/g,'')){
                    var newInvoiceCostNet = thisInstance.calcDiscounted(invoiceGross, discountPct);
                    thisInstance.updateDetailLineItemField(currentRow, 'invoicecostnet', newInvoiceCostNet, newInvoiceCostNet);
                }
            }
        });
    },

    registerDistributionDiscountChange : function() {
        var thisInstance = this;
        var selectedTag = jQuery('input[name^="distributablediscountpct"]');
        selectedTag.off('change').on('change', function () {
            var currentRow = jQuery(this).closest('tr');
            var discountPct = jQuery('input[name^="distributablediscountpct"]', currentRow).val();
            if(jQuery('[name^="distributable"]', currentRow).is(':checked') && discountPct) {
                if(distributableGross = jQuery('input[name^="gross"', currentRow).val().replace(/,/g,'')){
                    var newDistributableCostNet = thisInstance.calcDiscounted(distributableGross, discountPct);
                    thisInstance.updateDetailLineItemField(currentRow, 'distributablecostnet', newDistributableCostNet, newDistributableCostNet);
                }
            }
        });
    },

    calcDiscounted : function(gross, discountpct){
        if (discountpct === undefined) {
            discountpct = 0;
            }
        if (gross === undefined){
            gross = 0;
        }
        var discounted = ((100 - discountpct )/100 * gross).toFixed(2);
        return discounted;
    },

    updateDetailLineItemReadyToFields : function()
    {
        var invoice_total = 0;
        var dist_total = 0;
        jQuery('.lineItemsEdit tbody tr:not(:hidden)').each(function()
        {
            if(jQuery('[name^="invoiceable"]', this).is(':checked')
                && jQuery('[name^="ready_to_invoice"]', this).is(':checked')
                && jQuery('[name^="invoicedone"]', this).not(':checked'))
            {
                invoice_total += Number(jQuery('[name^="invoicecostnet"]', this).val().replace(',',''));
            }
            if(jQuery('[name^="distributable"]', this).is(':checked')
                && jQuery('[name^="ready_to_distribute"]', this).is(':checked')
                && jQuery('[name^="distributed"]', this).not(':checked'))
            {
                dist_total += Number(jQuery('[name^="distributablecostnet"]', this).val().replace(',',''));
            }
        });
        jQuery('[name="total_ready_to_invoice"]').val(invoice_total.toFixed(2));
        jQuery('.readyToInvoiceTotals').text(invoice_total.toFixed(2));
        jQuery('[name="total_ready_to_dist"]').val(dist_total.toFixed((2)));
        jQuery('.readyToDistTotals').text(dist_total.toFixed(2));
    },

    registerDetailLineUpdateReadyToEvent : function()
    {
        var thisInstance = this;
        jQuery('[name^="ready_to"]').off('change').on('change', function() {
            thisInstance.updateDetailLineItemReadyToFields();
        });
    },

    updateDetailLineItemField : function(currentRow, name, divValue, inputValue) {
        divElement = currentRow.find('div[name^="'+name+'"]');
        inputElement = currentRow.find('input[name^="'+name+'"]');
        divElement.html(divValue);
        inputElement.each(function() {
            if(jQuery(this).attr('name').search(/\d/) == name.length)
            {
                jQuery(this).val(inputValue);
                jQuery(this).trigger('change');
            }
        });
    },

    serviceProvidersCalcPercent : function() {
        jQuery('.serviceProviderDiv').each(function()
        {
            var v = Number(jQuery(this).closest('tr').find('input[name^="distributablecostnet"]').val().replace(',',''));
            var v2 = Number(jQuery(this).find('input[name^="serviceProviderSplit"]').val().replace(',',''));
            jQuery(this).find('input[name^="serviceProviderPercent"]').val((100 * v2 / v).toFixed(2));
        });
    },

    serviceProvidersCalcAmount : function(element) {
        if(typeof element == 'undefined')
        {
            element =jQuery('.serviceProviderDiv').closest('tr');
        }
        element.each(function()
        {
            var v = Number(jQuery(this).find('input[name^="distributablecostnet"]').val().replace(',',''));
            var divisor = 100;
            jQuery('.serviceProviderDiv', this).each(function() {
                var v2 = Number(jQuery(this).find('input[name^="serviceProviderPercent"]').val().replace(',',''));
                if(divisor <= 0)
                {
                    jQuery(this).find('input[name^="serviceProviderSplit"]').val('0');
                    return;
                }
                var nV = (v2 * v / divisor).toFixed(2);
                jQuery(this).find('input[name^="serviceProviderSplit"]').val(nV);
                divisor -= v2;
                v -= nV;
            });
        });
    },

    detailLineServiceProviderSplitInputChangeFunction : function(thisInput, splits, maxV, callbackAfter)
    {
        var totalValue = 0;
        for(var i=0; i < splits.length; i++)
        {
            if(splits[i] == thisInput)
            {
                continue;
            }
            totalValue += Number(splits[i].value.replace(',',''));
        }
        var thisValue = Number(thisInput.value.replace(',',''));
        var exceeds = totalValue + thisValue - maxV;

        if(totalValue + thisValue > maxV)
        {
            // prefer taking from the bottom up, then split equally
            for(var i=splits.length-1; i >=0 ; i--)
            {
                if(splits[i] == thisInput)
                {
                    break;
                }
                var v = Number(splits[i].value.replace(',',''));
                if(v >= exceeds)
                {
                    //we can finish here
                    splits[i].value = (v - exceeds).toFixed(2);
                    callbackAfter();
                    return;
                }
                splits[i].value = 0;
                exceeds -= v;
            }

            for(var i=0; i < splits.length; i++)
            {
                if(splits[i] == thisInput)
                {
                    continue;
                }
                var v = Number(splits[i].value.replace(',',''));
                splits[i].value = (v - (exceeds * (v / totalValue))).toFixed(2);
            }
        } else if(totalValue + thisValue < maxV) {
            // add to the bottom up, then top down
            for(var i=splits.length-1; i >= 0; i--)
            {
                if(splits[i] == thisInput)
                {
                    break;
                }
                var v = Number(splits[i].value.replace(',',''));
                splits[i].value = (v + maxV - (totalValue + thisValue)).toFixed(2);
                callbackAfter();
                return;
            }
            for(var i=0; i < splits.length; i++)
            {
                if(splits[i] == thisInput)
                {
                    break;
                }
                var v = Number(splits[i].value.replace(',',''));
                splits[i].value = (v + maxV - (totalValue + thisValue)).toFixed(2);
                callbackAfter();
                return;
            }
        }
        callbackAfter();
    },

    registerDetailLineServiceProviderSplitEvents : function() {
        // Don't need to hide single providers now
        // jQuery('.serviceProviderDiv').closest('td').each(function() {
        //     if(jQuery(this).find('.splitInput').filter(function() {
        //             return jQuery(this).closest('.serviceProviderDiv').find('input[name^="serviceProviderDeleted"]').val() != 'yes';
        //         }).length > 4)
        //     {
        //         jQuery(this).find('.splitInput').removeClass('hide');
        //     } else {
        //         jQuery(this).find('.splitInput').addClass('hide');
        //     }
        // });
        // init split fields

        if(jQuery('#isLocalRating').val() != '1') {
            var weight = jQuery('input[name="billed_weight"]');
            if (weight.length > 0) {
                weight = weight.val();
            } else {
                weight = jQuery('.value_LBL_QUOTES_BILLED_WEIGHT').find('span').first().text();
            }
            weight = Number(weight.replace(', ', ''));
            // obviously (?) only going to work on interstate tariffs
            var miles = jQuery('input[name="interstate_mileage"]');
            if (miles.length > 0) {
                miles = miles.val();
            } else {
                miles = jQuery('.value_LBL_QUOTES_MILEAGE').find('span').first().text();
            }
            miles = Number(miles.replace(', ', ''));
        } else {
            var weight = '0';
            var miles = '0';
        }

        jQuery('.serviceProviderDiv').each(function()
        {
            // if(jQuery(this).find('input[name^="serviceProviderSplit"]').val() == '')
            // {
            //     var v = jQuery(this).closest('tr').find('input[name^="distributablecostnet"]').val().replace(',','');
            //     jQuery(this).find('input[name^="serviceProviderSplit"]').val(v);
            // }
            if(jQuery(this).find('input[name^="serviceProviderWeight"]').val() == '')
            {
                jQuery(this).find('input[name^="serviceProviderWeight"]').val(weight);
            }
            if(jQuery(this).find('input[name^="serviceProviderMiles"]').val() == '')
            {
                jQuery(this).find('input[name^="serviceProviderMiles"]').val(miles);
            }
        });

        // Don't need to automatically recalculate anymore
        //app.currentPageController.lineItemsJs.serviceProvidersCalcPercent();

        jQuery('input[name^="serviceProviderPercent"]').off('change').on('change', function() {
            var v = Number(jQuery(this).val());
            if(v > 100)
            {
                jQuery(this).val(100);
            }
            // Don't need to automatically recalculate anymore
            // var splits = jQuery(this).closest('td').find('input[name^="serviceProviderPercent"]').filter(function(){
            //     if(jQuery(this).closest('.serviceProviderDiv').find('input[name^="serviceProviderDeleted"]').val() == 'yes')
            //     {
            //         return false;
            //     }
            //     return true;
            // });
            // var maxV = 100;
            // app.currentPageController.lineItemsJs.detailLineServiceProviderSplitInputChangeFunction(this, splits, maxV,
            //     function() {
            //         app.currentPageController.lineItemsJs.serviceProvidersCalcAmount();
            //     });
        });
        // Don't need to automatically recalculate anymore
        // jQuery('input[name^="serviceProviderSplit"]').off('change').on('change', function(){
        //     var splits = jQuery(this).closest('td').find('input[name^="serviceProviderSplit"]').filter(function(){
        //         if(jQuery(this).closest('.serviceProviderDiv').find('input[name^="serviceProviderDeleted"]').val() == 'yes')
        //         {
        //             return false;
        //         }
        //         return true;
        //     });
        //     var maxV = Number(jQuery(this).closest('tr').find('input[name^="distributablecostnet"]').val().replace(',',''));
        //     app.currentPageController.lineItemsJs.detailLineServiceProviderSplitInputChangeFunction(this, splits, maxV,
        //         function() {
        //             app.currentPageController.lineItemsJs.serviceProvidersCalcPercent();
        //         });
        // })

        // Don't need to automatically recalculate anymore
        // jQuery('input[name^="distributablecostnet"]').off('change').on('change', function() {
        //     app.currentPageController.lineItemsJs.serviceProvidersCalcAmount();
        // });
    },

    registerDetailLineServiceProviderDeleteEvent : function()
    {
        jQuery('.deleteServiceProvider').off('click').on('click', function() {
            var cell = jQuery(this).closest('.serviceProviderDiv');
            // Don't need to automatically recalculate anymore
            //cell.find('input[name^="serviceProviderPercent"]').val('0').trigger('change');
            cell.addClass('hide');
            cell.find('input[name^="serviceProviderDeleted"').val('yes');
            // no need to hide this stuff anymore
            // cell.closest('td.narrowWidthType').each(function() {
            //     if(jQuery(this).find('.splitInput').filter(function() {
            //             return jQuery(this).closest('.serviceProviderDiv').find('input[name^="serviceProviderDeleted"]').val() != 'yes';
            //         }).length > 4)
            //     {
            //         jQuery(this).find('.splitInput').removeClass('hide');
            //     } else {
            //         jQuery(this).find('.splitInput').addClass('hide');
            //     }
            // });
        });
    },

    registerDetailLineAddServiceProviderEvent : function()
    {
        var thisInstance = this;
        jQuery('button.addServiceProvider').off('click').on('click', function(){
            var cell = jQuery(this).closest('td.narrowWidthType');

            var newIndex = 1;
            cell.children().each(function(){
                var v = Number(jQuery('input[name="spIndexNumber"]', this).val());
                if(v >= newIndex)
                {
                    newIndex = v + 1;
                }
            });

            var div = cell.children().filter(function() {
                return jQuery(this).find('input[name^="serviceProviderDeleted"]').val() != 'yes';
            }).last();

            var newDiv = div.clone();
            newDiv.find('button').remove();
            newDiv.find('.deleteServiceProvider').removeClass('hide');

            newDiv.find('[name^="serviceProvider"').each(function () {
                var n = jQuery(this).attr('name');
                n = n.substr(0, n.indexOf('_') + 1) + newIndex;
                jQuery(this).attr('name', n);
            });

            newDiv.find('input[name="spIndexNumber"]').val(newIndex);
            newDiv.find('input[name^="serviceProviderID"]').val('');
            newDiv.find('input[name^="serviceProviderSplit"]').val('');
            newDiv.find('input[name^="serviceProviderPercent"]').val('');
            newDiv.find('input[name^="serviceProviderWeight"]').val('');
            newDiv.find('input[name^="serviceProviderMiles"]').val('');

            newDiv.find('.chzn-container').remove();
            var selectTag = newDiv.find('select');
            selectTag.removeClass('chzn-done').removeAttr('tabindex').removeAttr('id').removeAttr('style');

            newDiv.appendTo(cell);
            selectTag.chosen();
            thisInstance.registerDetailLineServiceProviderChangeEvent();
            thisInstance.registerDetailLineServiceProviderDeleteEvent();
            thisInstance.registerDetailLineServiceProviderSplitEvents();
            selectTag.trigger('change');

            cell.find('.splitInput').removeClass('hide');
        });
    },

});
