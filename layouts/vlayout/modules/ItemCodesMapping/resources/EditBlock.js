Vtiger_Edit_Js("ItemCodesMapping_EditBlock_Js", {
	getInstance: function() {
		return new ItemCodesMapping_EditBlock_Js();
	}
}, {
	registerRemoveItemCodesMappingButton : function(container){
		container.on( 'click', '.deleteMappingButton', function(){
			if(jQuery(this).siblings('input:hidden[name^="itemcodesmappingid"]').val() == ''){
				jQuery(this).closest('div.ItemCodesMappingRecords').remove()
			} else{
				jQuery(this).closest('div.ItemCodesMappingRecords').addClass('hide');
				jQuery(this).siblings('input:hidden[name^="mapping_deleted_"]').val('deleted');
			}
			var rowno=jQuery('div.ItemCodesMappingRecords').length;
			console.log(rowno);
			jQuery('[name="ItemCodesMappingTable"]').find('[name="numMapping"]').val(rowno);
		});
	},

	registerCopyItemCodesMappingButton : function(container){
		var thisInstance = this;
		var editViewForm = this.getForm();
		container.on( 'click', '.copyMappingButton', function(){
			var ItemCodesMappingRecords = jQuery(this).closest('div.ItemCodesMappingRecords');
			var copyRowNo = ItemCodesMappingRecords.data('row-no');
			var rowno=jQuery('div.ItemCodesMappingRecords').length;
			var copyData=ItemCodesMappingRecords.find(':input').serialize();
			copyData = copyData + '&module=ItemCodesMapping&view=MassActionAjax&mode=duplicateBlock&rowno='+rowno+'&copy_rowno='+copyRowNo;
			var viewParams = {
				"type": "POST",
				"url": 'index.php',
				"dataType": "html",
				"data": copyData
			};

			AppConnector.request(viewParams).then(
				function (data) {
					if (data) {
						var newItemCodeMapping=jQuery(data);
						app.showSelect2ElementView(newItemCodeMapping.find('.select2'));
						jQuery('div.ItemCodesMappingList').append(newItemCodeMapping);
						jQuery('[name="ItemCodesMappingTable"]').find('[name="numMapping"]').val(rowno+1);
						thisInstance.registerEventsForMultipicklistall(newItemCodeMapping);
					}
				}
			)
		});
	},

	registerAddItemCodesMappingButtons : function() {
		var thisInstance = this;
		var editViewForm = this.getForm();
		var container = jQuery('[name="ItemCodesMappingTable"]');
		container.find('.addItemCodesMapping').on('click', function () {
			var rowno=jQuery('div.ItemCodesMappingRecords').length;
			var viewParams = {
				"type": "POST",
				"url": 'index.php?module=ItemCodesMapping',
				"dataType": "html",
				"data": {
					'view': 'MassActionAjax',
					'mode': 'generateNewBlock',
					'rowno': rowno
				}
			};

			AppConnector.request(viewParams).then(
				function (data) {
					if (data) {
						var newItemCodeMapping=jQuery(data);
						app.showSelect2ElementView(newItemCodeMapping.find('.select2'));
						jQuery('div.ItemCodesMappingList').append(newItemCodeMapping);
						jQuery('[name="ItemCodesMappingTable"]').find('[name="numMapping"]').val(rowno+1);
						thisInstance.registerEventsForMultipicklistall(newItemCodeMapping);
					}
				}
			)
		});
	},

	registerEventForChangeBlockTitle: function (container) {
		var thisInstance = this;
		container.on("change",'[name^="itcmapping_businessline_"], [name^="commodities_"], [name^="itcmapping_billingtype_"], [name^="itcmapping_authority_"]', function () {
			var ItemCodesMappingRecords = jQuery(this).closest('div.ItemCodesMappingRecords');
			var ItemCodesMappingTitle = ItemCodesMappingRecords.find('.ItemCodesMappingTitle');
			var businessline = ItemCodesMappingRecords.find('select[name^="itcmapping_businessline_"]').val() || '['+app.vtranslate('LBL_BUSINESSLINE')+']';
            var commodities = ItemCodesMappingRecords.find('select[name^="commodities_"]').val() || '['+app.vtranslate('LBL_COMMODITIES')+']';
			var billingtype = ItemCodesMappingRecords.find('select[name^="itcmapping_billingtype_"]').val() || '['+app.vtranslate('LBL_BILLING_TYPE')+']';
			var authority = ItemCodesMappingRecords.find('select[name^="itcmapping_authority_"]').val() || '['+app.vtranslate('LBL_AUTHORITY')+']';
			ItemCodesMappingTitle.html(businessline+' / '+commodities+' / '+billingtype+' / '+authority)
		});
	},

    buildOptionList: function(fieldName, parentObject) {
	    var returnOptions = [];
	    parentObject.find('select[name^="'+fieldName+'"] option').each(function() {
	        if(jQuery(this).val() != 'All') {
	            returnOptions.push(jQuery(this).val());
            }
        })

        return returnOptions;
    },

    registerItemCodesMappingDuplicateCheck: function () {
	    var thisInstance = this;
	    jQuery('#EditView').on('submit', function (e) {
	        e.preventDefault();
            var hasDuplicate = false;

            var combinationList = [];
            var combinationIds = [];
            var duplicateList = [];
            jQuery('.ItemCodesMappingRecords').each(function() {
                var businessLineOptions = thisInstance.buildOptionList("itcmapping_businessline_", jQuery(this));
                var commodityOptions = thisInstance.buildOptionList("commodities_", jQuery(this));
                var billingTypeOptions = thisInstance.buildOptionList("itcmapping_billingtype_", jQuery(this));
                var authorityOptions = thisInstance.buildOptionList("itcmapping_authority_", jQuery(this));
                var businessLines = jQuery(this).find('select[name^="itcmapping_businessline_"]').val();
                if(businessLines && businessLines.length == 1 && businessLines[0] == 'All') {
                    businessLines = businessLineOptions.slice();
                }
                var commodities = jQuery(this).find('select[name^="commodities_"]').val();
                if(commodities && commodities.length == 1 && commodities[0] == 'All') {
                    commodities = commodityOptions.slice();
                }
                var billingTypes = jQuery(this).find('select[name^="itcmapping_billingtype_"]').val();
                if(billingTypes && billingTypes.length == 1 && billingTypes[0] == 'All') {
                    billingTypes = billingTypeOptions.slice();
                }
                var authorities = jQuery(this).find('select[name^="itcmapping_authority_"]').val();
                if(authorities && authorities.length == 1 && authorities[0] == 'All') {
                    authorities = authorityOptions.slice();
                }

                outerLoop:
                for(var businessLineIterator=0; businessLineIterator<businessLines.length; businessLineIterator++) {
                    for(var commodityIterator=0; commodityIterator<commodities.length; commodityIterator++) {
                        for(var billingTypeIterator=0; billingTypeIterator<billingTypes.length; billingTypeIterator++) {
                            for(var authorityIterator=0; authorityIterator<authorities.length; authorityIterator++) {
                                var comboString = businessLines[businessLineIterator]+":"+commodities[commodityIterator]+":"+billingTypes[billingTypeIterator]+":"+authorities[authorityIterator];
                                var index = combinationList.indexOf(comboString);
                                if(index == -1) {
                                    combinationIds.push(jQuery(this).data('rowNo'));
                                    combinationList.push(comboString);
                                }
                                else {
                                    if(duplicateList.indexOf(combinationIds[index]) == -1) {
                                        duplicateList.push(combinationIds[index]);
                                    }
                                    duplicateList.push(jQuery(this).data('rowNo'));
                                    hasDuplicate = true;
                                    break outerLoop;
                                }
                            }
                        }
                    }
                }
            });

            jQuery('.ItemCodesMappingRecords th span').removeAttr('style');

            if (hasDuplicate) {
                for(var i=0; i<duplicateList.length; i++) {
                    jQuery('.ItemCodesMappingRecords[data-row-no="'+duplicateList[i]+'"] th span').css('color', 'red');
                }
                bootbox.alert('There are duplicate Item Code Mapping records present. Please review and correct.');
                return false;
            }

            if (jQuery(this).data('submit')) {
                this.submit();
			}
        });
    },
	registerEvents : function() {
		this.registerAddItemCodesMappingButtons();
		var container = jQuery('div.ItemCodesMappingList');
		this.registerRemoveItemCodesMappingButton(container);
		this.registerCopyItemCodesMappingButton(container);
		this.registerEventForChangeBlockTitle(container);

		this.registerItemCodesMappingDuplicateCheck();
	},
});

jQuery(document).ready(function() {
	var instance = ItemCodesMapping_EditBlock_Js.getInstance();
	instance.registerEvents();
});
