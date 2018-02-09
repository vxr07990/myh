Contracts_Edit_Js("Contracts_AnnualRateIncrease_Js",{
	currentInstance: false,
	getInstance : function() {
		return new Contracts_AnnualRateIncrease_Js();
	}
},{

	registerAddAnnualRate : function(){
		//console.dir('registerAddAnnualRate ACTIVATED');
		//console.dir(jQuery('#addRateIncrease'));
		var annualRateBtn = jQuery('#addRateIncrease');
		var annualRateBtn2 = jQuery('#addRateIncrease2');
		//handler to add new annual rate increase row
		var newAnnualRow = function(){
			var newRow = jQuery('.defaultAnnualRate').clone(true,true);
			var sequence = parseInt(jQuery('input:hidden[name="numAnnualRate"]').val());
			sequence++;
			//console.dir(sequence);
			jQuery('input:hidden[name="numAnnualRate"]').val(sequence);
			//update date field
			var fieldData = newRow.find('input[name="default_rate_date"]').data('fieldinfo');
			var fieldName = fieldData.name;
			fieldName = fieldName+sequence;
			newRow.find('input[name="default_rate_date"]').attr('name', fieldName);
			//update percentage field
			var fieldData = newRow.find('input[name="default_rate_increase"]').data('fieldinfo');
			var fieldName = fieldData.name;
			fieldName = fieldName+sequence;
			newRow.find('input[name="default_rate_increase"]').attr('name', fieldName);
			//update ID field
			var idInput = newRow.find('input[name="annualRateId"]')
			var fieldName = 'annualRateId'+sequence;
			newRow.find('input[name="annualRateId"]').attr('name', fieldName);
			//update deleted field
			var fieldName = newRow.find('input[name="annualRateDeleted"]').attr('name');
			newRow.find('input[name="annualRateDeleted"]').attr('name', fieldName+sequence);
			newRow.removeClass('hide defaultAnnualRate').addClass('annualRate').attr('id', 'annualRateRow'+sequence);
			newRow = newRow.appendTo(jQuery(this).closest('table'));
			app.registerEventForDatePickerFields();
		}

		annualRateBtn.on('click', newAnnualRow);
		annualRateBtn2.on('click', newAnnualRow);
	},

	deleteAnnualRateEvent : function(){
		var thisInstance = this;
		jQuery('.deleteAnnualRateButton').on('click', function(e) {
			var currentRow = jQuery(this).closest('tr');
			currentRow.find('input[name^="annualRateDeleted"]').val('DELETE');
			currentRow.addClass('hide');
		});
	},

	importAccountRates : function(){
		var thisInstance = this;
		jQuery('input:hidden[name="account_id"]').on('change', function(){
			var message = 'Would you like to import annual rates from this account?';
			Vtiger_Helper_Js.showConfirmationBox({'message' : message}).then(
				function(e) {
					var id = jQuery('input[name="account_id"]').val();
					var url = 'index.php?module=Contracts&action=PopulateAnnualRates&account_id=' + id;
					AppConnector.request(url).then(
						function(data){
							if (data.success) {
								//console.dir(data);
								if(Object.keys(data.result).length == 0){
									var params = {
										title: app.vtranslate('JS_NO_RATES'),
										text: app.vtranslate('JS_NO_RATES_EXPLANATION'),
										width: '35%'
									};
									Vtiger_Helper_Js.showPnotify(params);
								} else{
									jQuery('input:hidden[name^="annualRateDeleted"]').filter('[value="IMPORT"]').closest('tr').find('.deleteAnnualRateButton').trigger('click');
									for(rateCount = 0; rateCount < Object.keys(data.result).length; rateCount++){
										var newRow = jQuery('.defaultAnnualRate').clone(true,true);
										var sequence = parseInt(jQuery('input:hidden[name="numAnnualRate"]').val());
										sequence++;
										jQuery('input:hidden[name="numAnnualRate"]').val(sequence);
										//update date field
										var fieldData = newRow.find('input[name="default_rate_date"]').data('fieldinfo');
										var fieldName = fieldData.name;
										fieldName = fieldName+sequence;
										newRow.find('input[name="default_rate_date"]').val(data.result[rateCount].date);
										newRow.find('input[name="default_rate_date"]').attr('name', fieldName);
										//update percentage field
										var fieldData = newRow.find('input[name="default_rate_increase"]').data('fieldinfo');
										var fieldName = fieldData.name;
										fieldName = fieldName+sequence;
										newRow.find('input[name="default_rate_increase"]').val(data.result[rateCount].rate);
										newRow.find('input[name="default_rate_increase"]').attr('name', fieldName);
										//update ID field
										var idInput = newRow.find('input[name="annualRateId"]')
										var fieldName = 'annualRateId'+sequence;
										newRow.find('input[name="annualRateId"]').attr('name', fieldName);
										//update deleted field
										var fieldName = newRow.find('input[name="annualRateDeleted"]').attr('name');
										newRow.find('input[name="annualRateDeleted"]').val('IMPORT');
										newRow.find('input[name="annualRateDeleted"]').attr('name', fieldName+sequence);
										newRow.removeClass('hide defaultAnnualRate').addClass('annualRate').attr('id', 'annualRateRow'+sequence);
										newRow = newRow.appendTo(jQuery('#addRateIncrease').closest('table'));
										app.registerEventForDatePickerFields();
										//console.dir(data.result[rateCount]);
									}
								}
							}
						},
						function(err){

						}
					);
				}, function(error, err) {
					//they pressed no don't populate the data.
				}
			);
		});
	},

	registerEvents : function(){
		//console.dir('ANNUAL RATES!!!!');
		this.registerAddAnnualRate();
		this.deleteAnnualRateEvent();
		this.importAccountRates();
		app.registerEventForDatePickerFields();
	}

});
