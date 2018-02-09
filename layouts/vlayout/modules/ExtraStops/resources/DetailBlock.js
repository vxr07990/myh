/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

Vtiger_Detail_Js("ExtraStops_DetailBlock_Js", {
	getInstance: function() {
		return new ExtraStops_DetailBlock_Js();
	}
}, {

	registerStopsAnimationEvent : function(){
		var thisInstance = this;
		jQuery('.stopToggle').on('click',function(e){
			var currentTarget =  jQuery(e.currentTarget);
			var blockId = currentTarget.data('id');
			var closestBlock = currentTarget.closest('.stopBlock');
			var bodyContents = closestBlock.find('.stopContent');
			var data = currentTarget.data();
			var module = app.getModuleName();
			var hideHandler = function() {
				//console.dir('hiding');
				bodyContents.hide('slow');
				app.cacheSet(module+'.'+blockId, 0);
			}
			var showHandler = function() {
				bodyContents.show();
				app.cacheSet(module+'.'+blockId, 1);
				if(currentTarget.closest('div').parent().attr('id') == 'inline_content') {
					//closestBlock.siblings().find('tbody').hide('slow');
				}
			}
			if(data.mode == 'show'){
				hideHandler();
				currentTarget.hide();
				closestBlock.find("[data-mode='hide']").show();
			}else{
				showHandler();
				currentTarget.hide();
				closestBlock.find("[data-mode='show']").show();
				if(currentTarget.closest('div').parent().attr('id') == 'inline_content') {
					closestBlock.siblings().each(function() {
						jQuery(this).find("[data-mode='hide']").show();
						jQuery(this).find("[data-mode='show']").hide();
						app.cacheSet(module+'.'+jQuery(this).find("[data-mode='show']").data('id'), 0);
					});
				}
			}

		});
	},

	registerEvents : function() {
		this.registerStopsAnimationEvent();
	},
});

jQuery(document).ready(function() {
	var instance = ExtraStops_DetailBlock_Js.getInstance();
	instance.registerEvents();
});
