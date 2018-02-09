Accounts_Edit_Js("Accounts_AnnualRateIncrease_Js",{
	currentInstance: false,
	getInstance : function() {
		return new Accounts_AnnualRateIncrease_Js();
	}
},{
	
	registerAddAnnualRate : function(){
		console.dir('registerAddAnnualRate ACTIVATED');
		console.dir(jQuery('#addRateIncrease'));
		var annualRateBtn = jQuery('#addRateIncrease');
		var annualRateBtn2 = jQuery('#addRateIncrease2');
		//handler to add new annual rate increase row
		var newAnnualRow = function(){
			var newRow = jQuery('.defaultAnnualRate').clone(true,true);
			var sequence = parseInt(jQuery('input:hidden[name="numAnnualRate"]').val());
			sequence++;
			console.dir(sequence);
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
	
	registerEvents : function(){
		console.dir('ANNUAL RATES!!!!');
		this.registerAddAnnualRate();
		this.deleteAnnualRateEvent();
		app.registerEventForDatePickerFields();
	}
	
});