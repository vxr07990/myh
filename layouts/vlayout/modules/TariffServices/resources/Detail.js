Vtiger_Detail_Js("TariffServices_Detail_Js",{},{
	registerRateChange : function() {
		jQuery('select[name="rate_type"]').on('change', function() {

			var business_lines='';
			jQuery('.result-selected').each(function( index ) {
				business_lines = business_lines + '::' + jQuery.trim(jQuery( this ).text());
			});

			var dataUrl = "index.php?module=Potentials&action=GetHiddenBlocks&viewMode=detail&formodule=TariffServices&businessline="+business_lines;
			AppConnector.request(dataUrl).then(
					function(data) {

						if (data.success) {
							var showBlocks = [];
							for (var key in data.result.show) {
								showBlocks.push(data.result.show[key]);
								var blockToMove = jQuery("th:contains('" + data.result.show[key] + "')").closest('table');
								var formElement = blockToMove.parent();
								blockToMove.remove();
								blockToMove.insertAfter(formElement.find('br').first());
								blockToMove.removeClass('hide');
							}
							for (var key in data.result.hide) {
								if(showBlocks.indexOf(data.result.hide[key]) < 0) {
									jQuery("th:contains('" + data.result.hide[key] + "')").closest('table').addClass('hide');
								}
							}
						}
					},
					function(error, err) {

					}
			);
		});
	},

	registerEvents : function() {
		this._super();
		this.registerRateChange();
	}
});
