$( document ).ready(function() {
	var contentsHolder = jQuery('.contentsDiv');
	contentsHolder.on('click', '.blockToggle', function(e) {
		var currentTarget = jQuery(e.currentTarget);
		var closestBlock = currentTarget.closest('table');
		var bodyContents = closestBlock.find('tbody');
		var data = currentTarget.data();
		var module = app.getModuleName();
		var hideHandler = function() {
			bodyContents.hide('slow');
		}
		var showHandler = function() {
			bodyContents.show();
			if(currentTarget.closest('div').parent().attr('id') == 'inline_content') {
				closestBlock.siblings().find('tbody').hide('slow');
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
	
	contentsHolder.on('click', '.btn-success', function(e) {
		e.preventDefault();
	
		var values = {};
		$.each(jQuery(this).closest('form').serializeArray(), function(i, field) {
			values[field.name] = field.value;
		});

		var params = {
			module: 'AdminSettings',
			action: 'SaveSettings',
			agencySettings: JSON.stringify(values)	
		}
		AppConnector.request(params).then(
			function(data) {
				if(data.success) {
					bootbox.alert(data.result);
				}
				else {
					//console.dir('Error');
				}
			});
	});
});

