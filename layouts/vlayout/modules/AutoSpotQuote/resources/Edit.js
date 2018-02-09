/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

Vtiger_Edit_Js("AutoSpotQuote_Edit_Js",{
	getInstance: function() {
		return new AutoSpotQuote_Edit_Js();
	}
},{
	registerRateUpdate: function(){
		jQuery('#update_rates').on('click', function(e){
			e.preventDefault();

			if(jQuery('#AutoSpotQuote_editView_fieldName_auto_year').val() == '' ||
			   jQuery('#AutoSpotQuote_editView_fieldName_auto_smf').val() == '' ||
			   jQuery('#AutoSpotQuote_editView_fieldName_auto_load_from').val() == '' ||
			   jQuery('#AutoSpotQuote_editView_fieldName_auto_rush_fee').val() == '' ||
			   jQuery('input[name="estimate_id_display"]').val() == '' ||
			   jQuery('select[name="auto_condition"]').val() == '' ||
			   jQuery('select[name="auto_make"]').val() == '' ||
			   jQuery('select[name="auto_model"]').val() == ''){
				bootbox.alert('Please fill out all required information.');
			}else{
				var progressIndicatorElement = jQuery.progressIndicator({
					'position' : 'html',
					'blockInfo' : {
						'enabled' : true
					}
				});
				var params = {
					module: 'AutoSpotQuote',
					action: 'MontwayAPI',
					ajaxAction: 'getRates',
					formData: jQuery('form[name="EditView"]').serializeFormJSON()
				};
				AppConnector.request(params).then(
					function(data) {
						if(data.success) {
							var rates = jQuery.parseJSON(data.result);
												
							jQuery('#10_day_row td:nth-child(2)').html(rates.rates.ten_day_pickup.load_to_date);
							jQuery('#10_day_row td:nth-child(3)').html(rates.rates.ten_day_pickup.deliver_from_date);
							jQuery('#10_day_row td:nth-child(4)').html(rates.rates.ten_day_pickup.deliver_to_date);
							jQuery('#10_day_row td:nth-child(5)').html('$'+rates.rates.ten_day_pickup.price);

							
							jQuery('#7_day_row td:nth-child(2)').html(rates.rates.seven_day_pickup.load_to_date);
							jQuery('#7_day_row td:nth-child(3)').html(rates.rates.seven_day_pickup.deliver_from_date);
							jQuery('#7_day_row td:nth-child(4)').html(rates.rates.seven_day_pickup.deliver_to_date);
							jQuery('#7_day_row td:nth-child(5)').html('$'+rates.rates.seven_day_pickup.price);

							
							jQuery('#4_day_row td:nth-child(2)').html(rates.rates.four_day_pickup.load_to_date);
							jQuery('#4_day_row td:nth-child(3)').html(rates.rates.four_day_pickup.deliver_from_date);
							jQuery('#4_day_row td:nth-child(4)').html(rates.rates.four_day_pickup.deliver_to_date);
							jQuery('#4_day_row td:nth-child(5)').html('$'+rates.rates.four_day_pickup.price);

							
							jQuery('#2_day_row td:nth-child(2)').html(rates.rates.two_day_pickup.load_to_date);
							jQuery('#2_day_row td:nth-child(3)').html(rates.rates.two_day_pickup.deliver_from_date);
							jQuery('#2_day_row td:nth-child(4)').html(rates.rates.two_day_pickup.deliver_to_date);
							jQuery('#2_day_row td:nth-child(5)').html('$'+rates.rates.two_day_pickup.price);

							jQuery('#auto_quote_info').val(encodeURIComponent(data.result));
							jQuery('#auto_quote_id').val(rates.quote_id);
						}
						progressIndicatorElement.progressIndicator({
							'mode' : 'hide'
						})
					}
				);
			}
		});
	},
	registerEvents : function() {
		this.registerRateUpdate();
		this._super();
	},

});

(function($) {
$.fn.serializeFormJSON = function() {

	var o = {};
	var a = this.serializeArray();
	$.each(a, function() {
		if(this.name != 'picklistDependency'){
			if (o[this.name]) {
				if (!o[this.name].push) {
				   o[this.name] = [o[this.name]];
				}
				o[this.name].push(this.value || '');
			} else {
			   	o[this.name] = this.value || '';
			}
		}
	});
	return o;
};
})(jQuery);
