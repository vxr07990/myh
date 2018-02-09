Vtiger_Edit_Js("OrdersTask_EditExtraBlock_Js", {
	getInstance: function() {
		return new OrdersTask_EditExtraBlock_Js();
	}
}, {
	vpCalculator: function(table, spanClass, input){
	    var tot = 0;
	    jQuery('table[name="'+table+'"] tbody tr.itemRow:not(.hide)').each(function(){
		tot += parseInt(jQuery(this).find("span."+spanClass+" input").val());
	    });
	    jQuery('[name="'+input+'"]').val(tot);
	},
	registerRemoveItemButton : function(){
	    var thisInstance = this;
	    jQuery('html').on( 'click', '.removeItem', function(){
		var currentTable = jQuery(this).closest('table.dynamic_table');
		if(jQuery(this).siblings('input:hidden[name^="itemId_"]').val() == 'none'){
		    jQuery(this).closest('tr.itemRow').remove()
		} else{
		    jQuery(this).closest('tr.itemRow').addClass('hide');
		    jQuery(this).siblings('input:hidden[name^="itemDelete_"]').val('deleted');
		}
		var sequenceNode = jQuery("input[name^='numItem_']",currentTable);
		var sequence = jQuery( "tr.itemRow",currentTable ).length;
		sequenceNode.val(sequence);

		var table = spanClass = input = "";
		if(jQuery(currentTable).attr("name") == "LBL_PERSONNEL"){
		    table = "LBL_PERSONNEL";
		    spanClass = "personnelChange";
		    input = "total_estimated_personnel";
		}else{
		    table = "LBL_VEHICLES";
		    spanClass = "vehicleChange";
		    input = "total_estimated_vehicles";
		}
		
		thisInstance.vpCalculator(table,spanClass,input);
	    });
	},
	registerChangeExtraBlocksPV: function() {
	    var thisInstance = this;
	    jQuery(document).on("change",".personnelChange input", function(){
		thisInstance.vpCalculator("LBL_PERSONNEL","personnelChange","total_estimated_personnel");
	    });
	    jQuery(document).on("change",".vehicleChange input", function(){
		thisInstance.vpCalculator("LBL_VEHICLES","vehicleChange","total_estimated_vehicles");
	    });
	},
	registerAddItemButtons : function() {
		var thisInstance = this;
		var button = jQuery('.addItem');
		button.off('click');
		var addHandler = function() {
			var currentTable = jQuery(this).closest('table.dynamic_table');
			var newRow = jQuery('.defaultItem',currentTable).clone();
			var sequenceNode = jQuery('[name^="numItem"]',currentTable);
			var sequence = parseInt(sequenceNode.val());
			sequence++;
			sequenceNode.val(sequence);
			newRow.data('rowno',sequence);
			newRow.addClass('itemRow');
			newRow.removeClass('hide defaultItem');
			//add the new row to the table
			newRow = newRow.appendTo(currentTable);
			newRow.find('input, select').each(function(idx, ele){
				if(jQuery(ele).attr('name') !== 'popupReferenceModule'){
					jQuery(ele).attr('name', jQuery(ele).attr('name')+'_'+sequence);
					jQuery(ele).attr('id', jQuery(ele).attr('id')+'_'+sequence);


				}
				var fieldInfo = jQuery(ele).data('fieldinfo');
				if(fieldInfo != undefined){
					if(typeof fieldInfo != 'object'){
						fieldInfo = JSON.parse(fieldInfo);
					}
					if(fieldInfo.type == 'personnelpicklist'){
						jQuery(ele).addClass('select2');
					}else if(fieldInfo.type == 'vehiclepicklist'){
						jQuery(ele).addClass('chzn-select');
					}
				}
			});
			app.showReferenceMultiSelectView(newRow.find('input.select2'));
			newRow.find('.chzn-container').remove();
			newRow.find('select.chzn-select').removeClass('chzn-done').chosen();
			var ordersTaskInstance = new OrdersTask_Edit_Js();
			ordersTaskInstance.registerAutoCompleteFields(newRow);
			ordersTaskInstance.registerClearReferenceSelectionEvent(newRow);

		};
		button.on('click', addHandler);
	},
	removeSelectAnOption: function(){
	    jQuery('[name="LBL_VEHICLES"] tbody tr.defaultItem').find('[name="vehicle_type"]').find('option[value=""]').remove();
	    jQuery('[name="LBL_VEHICLES"] tbody tr:not(.defaultItem)').each(function(){
		jQuery(this).find('[name^="vehicle_type"]').find('option[value=""]').remove();
		jQuery(this).find('[name^="vehicle_type"]').trigger("liszt:updated");
	    });
	},
	registerEvents : function() {
		// Update field name
		jQuery(document).find('tr.itemRow').each(function (i,tre) {
			var tr= jQuery(tre);
			if(!tr.hasClass('hide')) {
				var sequence = tr.data('rowno');
				tr.find('input, select').each(function(idx, ele){
					jQuery(ele).attr('name', jQuery(ele).attr('name')+'_'+sequence);
					jQuery(ele).attr('id', jQuery(ele).attr('id')+'_'+sequence);
				});
			}

		});

		this.registerAddItemButtons();
		this.registerRemoveItemButton();
		this.registerChangeExtraBlocksPV();
		
		jQuery(".personnelChange input").change();
		jQuery(".vehicleChange input").change();
		this.removeSelectAnOption();
	}
});

jQuery(document).ready(function() {
	var instance = OrdersTask_EditExtraBlock_Js.getInstance();
	instance.registerEvents();
});