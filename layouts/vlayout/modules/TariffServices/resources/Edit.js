Vtiger_Edit_Js("TariffServices_Edit_Js",{
    setDecimalPlaces : function(event) {
        //console.log(event.value);
        event.value = parseFloat(event.value).toFixed(2);
    }
},{

	basePlusSequence : jQuery('.basePlusRow').length,
	breakPointSequence : jQuery('.breakPointRow').length,
	weightMileageSequence : jQuery('.weightMileageRow').length,
	serviceChargeSequence : jQuery('.serviceChargeRow').length,
    CWTbyWeightSequence : jQuery('.CWTbyWeightRow').length,
	bulkySequence : jQuery('.bulkyRow').length,
	chargePerHundredSequence : jQuery('.chargePerHundredRow').length,
	countySequence : jQuery('.countyRow').length,
	hourlySequence : jQuery('.hourlyRow').length,
	cartonSequence : jQuery('.cartonRow').length,
	valAmountSequence : jQuery('.valAmountRow').length,
	valDeductibleSequence : jQuery('.valDeductibleRow').length,
	valuationSequence : jQuery('.valuationRow').length,
	flatRateByWeightSequence : jQuery('.flatRateByWeightRow').length,

	getPopUpParams : function(container) {
		var params = {};
		var sourceModule = app.getModuleName();
		var popupReferenceModule = jQuery('input[name="popupReferenceModule"]',container).val();
		var sourceFieldElement = jQuery('input[class="sourceField"]',container);
		var sourceField = sourceFieldElement.attr('name');
		var sourceRecordElement = jQuery('input[name="record"]');
		var sourceRecordId = '';
		var search_key = '';
		var search_value = '';
		// if(sourceField == 'tariff_section') {
		// 	search_key = 'related_tariff';
		// 	search_value = jQuery('input.sourceField[name="related_tariff"]').data('displayvalue');
		// }
		if(sourceRecordElement.length > 0) {
			sourceRecordId = sourceRecordElement.val();
		}

		var isMultiple = false;
		if(sourceFieldElement.data('multiple') == true){
			isMultiple = true;
		}

		var params = {
			'module' : popupReferenceModule,
			'src_module' : sourceModule,
			'src_field' : sourceField,
			'src_record' : sourceRecordId,
			'search_key' : search_key,
			'search_value' : search_value,
			'related_tariff' : jQuery('input.sourceField[name="related_tariff"]').val(),
		};

		if(isMultiple) {
			params.multi_select = true ;
		}
		return params;
	},

	registerAutoCompleteFields : function(container) {
		var thisInstance = this;
		container.find('input.autoComplete').autocomplete({
			'minLength' : '3',
			'source' : function(request, response){
				//element will be array of dom elements
				//here this refers to auto complete instance
				var inputElement = jQuery(this.element[0]);
				var searchValue = request.term;
				var params = thisInstance.getReferenceSearchParams(inputElement);
				params.search_value = searchValue;
				params.parent_id = jQuery('input[name="related_tariff"]').val();
				params.parent_module = "Tariffs";
				thisInstance.searchModuleNames(params).then(function(data){
					var reponseDataList = [];
					var serverDataFormat = data.result;
					if(serverDataFormat.length <= 0) {
						jQuery(inputElement).val('');
						serverDataFormat = new Array({
							'label' : app.vtranslate('JS_NO_RESULTS_FOUND'),
							'type'  : 'no results'
						});
					}
					for(var id in serverDataFormat){
						var responseData = serverDataFormat[id];
						reponseDataList.push(responseData);
					}
					response(reponseDataList);
				});
			},
			'select' : function(event, ui ){
				var selectedItemData = ui.item;
				//To stop selection if no results is selected
				if(typeof selectedItemData.type != 'undefined' && selectedItemData.type=="no results"){
					return false;
				}
				selectedItemData.name = selectedItemData.value;
				var element = jQuery(this);
				var tdElement = element.closest('td');
				thisInstance.setReferenceFieldValue(tdElement, selectedItemData);

                var sourceField = tdElement.find('input[class="sourceField"]').attr('name');
                var fieldElement = tdElement.find('input[name="'+sourceField+'"]');

                fieldElement.trigger(Vtiger_Edit_Js.postReferenceSelectionEvent,{'data':selectedItemData});
			},
			'change' : function(event, ui) {
				var element = jQuery(this);
				//if you dont have readonly attribute means the user didnt select the item
				if(element.attr('readonly')== undefined) {
					element.closest('td').find('.clearReferenceSelection').trigger('click');
				}
			},
			'open' : function(event,ui) {
				//To Make the menu come up in the case of quick create
				jQuery(this).data('autocomplete').menu.element.css('z-index','100001');

			}
		});
	},

	registerAddButtons : function(label) {
		var thisInstance = this;
		var addRequired = false;
		var table = jQuery('[name="'+label+'"]').find('tbody');
		if(label == 'LBL_TARIFFSERVICES_BASEPLUS') {
			var button1 = jQuery('#addBasePlus');
			var button2 = jQuery('#addBasePlus2');
			var sequenceItem = thisInstance.basePlusSequence;
			var defaultRowClass = 'defaultBasePlus';
			var rowId = 'basePlusRow';
			var names = ['fromMilesBasePlus', 'toMilesBasePlus', 'fromWeightBasePlus', 'toWeightBasePlus', 'baseRateBasePlus', 'excessBasePlus'];
			var numCount = 'numBasePlus';
			addRequired = true;
		} else if(label == 'LBL_TARIFFSERVICES_BREAKPOINT') {
			var button1 = jQuery('#addBreakPoint');
			var button2 = jQuery('#addBreakPoint2');
			var sequenceItem = thisInstance.breakPointSequence;
			var defaultRowClass = 'defaultBreakPoint';
			var rowId = 'breakPointRow';
			var names = ['fromMilesBreakPoint', 'toMilesBreakPoint', 'fromWeightBreakPoint', 'toWeightBreakPoint', 'breakPointBreakPoint', 'baseRateBreakPoint'];
			var numCount = 'numBreakPoint';
		} else if(label == 'LBL_TARIFFSERVICES_WEIGHTMILEAGE') {
			var button1 = jQuery('#addWeightMileage');
			var button2 = jQuery('#addWeightMileage2');
			var sequenceItem = thisInstance.weightMileageSequence;
			var defaultRowClass = 'defaultWeightMileage';
			var rowId = 'weightMileageRow';
			var names = ['fromMilesWeightMileage', 'toMilesWeightMileage', 'fromWeightWeightMileage', 'toWeightWeightMileage', 'baseRateWeightMileage'];
			var numCount = 'numWeightMileage';
		} else if(label == 'LBL_TARIFFSERVICES_SERVICECHARGE') {
			var button1 = jQuery('#addServiceCharge');
			var button2 = jQuery('#addServiceCharge2');
			var sequenceItem = thisInstance.serviceChargeSequence;
			var defaultRowClass = 'defaultServiceCharge';
			var rowId = 'ServiceChargeRow';
			var names = ['priceFromServiceBaseCharge', 'priceToServiceBaseCharge', 'chargeServiceBaseCharge'];
			var numCount = 'numServiceCharge';
		} else if(label == 'LBL_TARIFFSERVICES_CWTBYWEIGHT') {
			var button1 = jQuery('#addCWTbyWeight');
			var button2 = jQuery('#addCWTbyWeight2');
			var sequenceItem = thisInstance.CWTbyWeightSequence;
			var defaultRowClass = 'defaultCWTbyWeight';
			var rowId = 'CWTbyWeightRow';
			var names = ['fromWeightCWTbyWeight', 'toWeightCWTbyWeight', 'baseRateCWTbyWeight'];
			var numCount = 'numCWTbyWeight';
		} else if(label == 'LBL_TARIFFSERVICES_BULKY') {
			var button1 = jQuery('#addBulky');
			var button2 = jQuery('#addBulky2');
			var sequenceItem = thisInstance.bulkySequence;
			var defaultRowClass = 'defaultBulky';
			var rowId = 'bulkyRow';
			var names = ['bulkyDescription', 'bulkyWeight', 'bulkyRate'];
			var numCount = 'numBulky';
		} else if(label == 'LBL_TARIFFSERVICES_CHARGEPERHUNDRED') {
			var button1 = jQuery('#addChargePerHundred');
			var button2 = jQuery('#addChargePerHundred2');
			var sequenceItem = thisInstance.chargePerHundredSequence;
			var defaultRowClass = 'defaultChargePerHundred';
			var rowId = 'chargePerHundredRow';
			var names = ['chargePerHundredDeductible', 'chargePerHundredRate'];
			var numCount = 'numChargePer100';
		} else if(label == 'LBL_TARIFFSERVICES_COUNTYCHARGE') {
			var button1 = jQuery('#addCounty');
			var button2 = jQuery('#addCounty2');
			var sequenceItem = thisInstance.countySequence;
			var defaultRowClass = 'defaultCounty';
			var rowId = 'countyRow';
			var names = ['countyName', 'countyRate'];
			var numCount = 'numCountyCharges';
		} else if(label == 'LBL_TARIFFSERVICES_HOURLYSET') {
			var button1 = jQuery('#addHourly');
			var button2 = jQuery('#addHourly2');
			var sequenceItem = thisInstance.hourlySequence;
			var defaultRowClass = 'defaultHourly';
			var rowId = 'hourlyRow';
			var names = ['hourlyMen', 'hourlyVans', 'hourlyRate'];
			var numCount = 'numHourly';
		} else if(label == 'LBL_TARIFFSERVICES_PACKING') {
			var button1 = jQuery('#addCarton');
			var button2 = jQuery('#addCarton2');
			var sequenceItem = thisInstance.cartonSequence;
			var defaultRowClass = 'defaultCarton';
			var rowId = 'cartonRow';
			var names = ['cartonName', 'cartonContainerRate', 'cartonPackingRate', 'cartonUnpackingRate'];
			var numCount = 'numPacking';
		} else if(label == 'valuationAmountTable') {
			var button1 = jQuery('#addValAmount');
			var button2 = jQuery('#addValAmount2');
			var sequenceItem = thisInstance.valAmountSequence;
			var defaultRowClass = 'defaultValAmount';
			var rowId = 'valAmountRow';
			var names = ['valAmount'];
		} else if(label == 'valuationDeductibleTable') {
			var button1 = jQuery('#addValDeductible');
			var button2 = jQuery('#addValDeductible2');
			var sequenceItem = thisInstance.valDeductibleSequence;
			var defaultRowClass = 'defaultValDeductible';
			var rowId = 'valDeductibleRow';
			var names = ['valDeductible'];
		} else if(label == 'LBL_TARIFFSERVICES_FLATRATEBYWEIGHT') {
			var button1 = jQuery('#addFlatRateByWeight');
			var button2 = jQuery('#addFlatRateByWeight2');
			var sequenceItem = thisInstance.flatRateByWeightSequence;
			var defaultRowClass = 'defaultFlatRateByWeight';
			var rowId = 'flatRateByWeightRow';
			var names = ['flatratebyweight_from','flatratebyweight_to','flatratebyweight_rate','flatratebyweight_lineitemId'];
			var numCount = 'numRateByWeight';
		}

		var addHandler = function() {
			var newRow = jQuery('.'+defaultRowClass).clone(true,true);
			var sequence = sequenceItem++;
			newRow.removeClass('hide '+defaultRowClass);
			newRow.attr('id', rowId+sequence);
			for(var i=0; i<names.length; i++) {
				var name = names[i];
                newRow.find('input[name="'+name+'"]').attr('name', name+sequence);
                Vtiger_Edit_Js.makeFieldMandatory(newRow.find('input[name="'+name+sequence+'"]'));
			}
			newRow = newRow.appendTo(table);
			var sequenceNode = jQuery("input[name='"+numCount+"']");
			var sequence = sequenceNode.val();
			sequence++;
			sequenceNode.val(sequence);
		};

		button1.on('click', addHandler);
		button2.on('click', addHandler);
	},

	registerValuationEditButtons : function() {
		var thisInstance = this;
		var table = jQuery('[name="LBL_TARIFFSERVICES_VALUATION"]').find('tbody');
		jQuery('#editValAmounts').on('click', function() {
			jQuery.colorbox({inline:true, width:'300px', height:'60%', left:'25%', href:'#valuationAmountContent', onClosed:function(){jQuery(document.body).css({overflow:'auto'});thisInstance.addValuationRows();}, onComplete:function(){jQuery(document.body).css({overflow:'hidden'});}});
		});
		jQuery('#editValDeductibles').on('click', function() {
			jQuery.colorbox({inline:true, width:'300px', height:'60%', left:'25%', href:'#valuationDeductibleContent', onClosed:function(){jQuery(document.body).css({overflow:'auto'});thisInstance.addValuationRows();}, onComplete:function(){jQuery(document.body).css({overflow:'hidden'});}});
		});
	},

	addValuationRows : function() {
		var thisInstance = this;
		var amountTable = jQuery('[name="valuationAmountTable"]').find('tbody');
		var deductibleTable = jQuery('[name="valuationDeductibleTable"]').find('tbody');
		var valuationTable = jQuery('[name="LBL_TARIFFSERVICES_VALUATION"]').find('tbody');
		var amounts = [];
		var deductibles = [];
		var amountRowNames = [];
		var deductRowNames = [];
		amountTable.find('tr.valAmountRow').not('.defaultValAmount').each(function() {
			amounts.push(jQuery(this).find('input').val());
			amountRowNames.push(jQuery(this).find('input').attr('name'));
		});
		deductibleTable.find('tr.valDeductibleRow').not('.defaultValDeductible').each(function() {
			deductibles.push(jQuery(this).find('input').val());
			deductRowNames.push(jQuery(this).find('input').attr('name'));
		});
		var valuations = valuationTable.find('tr.valuationRow').not('.defaultValuation').each(function(){
			var amountSourceExists = false;
			var deductibleSourceExists = false;
			var currentValuation = jQuery(this);
			//console.log(currentValuation);
			//console.log('^^^CURRENT VALUATION^^^');
			amountTable.find('tr.valAmountRow').not('.defaultValAmount').each(function(){
				//console.log('CHECKING');
				//console.log(jQuery(this).attr('name'));
				//console.log(jQuery(this));
				//console.log('^^^AMOUNT^^^');
				if(currentValuation.hasClass(jQuery(this).find('input').attr('name'))){
					amountSourceExists = true;
				}
			});
			deductibleTable.find('tr.valDeductibleRow').not('.defaultValDeductible').each(function(){
				//console.log('CHECKING');
				//console.log(jQuery(this).attr('name'));
				//console.log(jQuery(this));
				//console.log('^^^DEDUCT^^^');
				if(currentValuation.hasClass(jQuery(this).find('input').attr('name'))){
					deductibleSourceExists = true;
				}
			});
			if(deductibleSourceExists != true || amountSourceExists != true){
				//console.log('REMOVED!!!');
				currentValuation.remove();
			}
		});
		for(var i=0; i<amounts.length; i++) {
			var valuationAmounts = jQuery('.amount'+i);
			if(valuationAmounts.length == 0){
					//console.log("val amounts length is 0");
					if(deductibles.length > 0 && parseFloat(amounts[i]) > 0) {
						for(var j=0; j<deductibles.length; j++) {
							if(deductibles[j] == '') {continue;}
							var newRow = jQuery('.defaultValuation').clone(true,true);
							var sequence = thisInstance.valuationSequence++;
							newRow.removeClass('hide defaultValuation');
							newRow.addClass(amountRowNames[i]+' '+deductRowNames[j]);
							var amountInput = newRow.find('input[name="valuationAmount"]');
							var deductibleInput = newRow.find('input[name="valuationDeductible"]');
							var amountRowInput = newRow.find('input[name="amountRow"]');
							var deductibleRowInput = newRow.find('input[name="deductibleRow"]');
							amountInput.attr('name', 'valuationAmount'+sequence);
							amountInput.val(amounts[i]);
							amountInput.attr('value', amounts[i]);
							amountInput.addClass('amount'+i);
							amountRowInput.attr('name', 'amountRow'+sequence);
							amountRowInput.val(amountRowNames[i]);
							amountRowInput.attr('value', amountRowNames[i]);
							deductibleInput.attr('name', 'valuationDeductible'+sequence);
							deductibleInput.val(deductibles[j]);
							deductibleInput.attr('value', deductibles[j]);
							deductibleRowInput.attr('name', 'deductibleRow'+sequence);
							deductibleRowInput.val(deductRowNames[j]);
							deductibleRowInput.attr('value', deductRowNames[j]);
							newRow.find('input[name="valuationCost"]').attr('name', 'valuationCost'+sequence);
							newRow = newRow.appendTo(valuationTable);
						}
					}
			} else {
				//console.log("val amounts length is not 0");
				if(deductibles.length > 0 && parseFloat(amounts[i]) > 0) {
					for(var j=0; j<deductibles.length; j++) {
						if(deductibles[j] == '') {continue;}
						var valuationFound = false;
						valuations.each(function() {
							/* console.log('amount: '+amounts[i]);
							console.log('deduct: '+deductibles[j]);
							console.log(amounts);
							console.log(deductibles);
							console.log('Checking: '+jQuery(this).attr('name'));
							console.log(jQuery(this)); */
							if(jQuery(this).hasClass(amountRowNames[i]) && jQuery(this).hasClass(deductRowNames[j])) {
								valuationFound = true;
								jQuery(this).find('.amount').val(amounts[i]);
								jQuery(this).find('.deductible').val(deductibles[j]);
								//console.log('FOUND: '+jQuery(this).attr('name'));
							}
						});
						if(valuationFound) {continue;}
						var newRow = jQuery('.defaultValuation').clone(true,true);
						var sequence = thisInstance.valuationSequence++;
						newRow.removeClass('hide defaultValuation');
						newRow.addClass(amountRowNames[i]+' '+deductRowNames[j]);
						var amountInput = newRow.find('input[name="valuationAmount"]');
						var deductibleInput = newRow.find('input[name="valuationDeductible"]');
						var amountRowInput = newRow.find('input[name="amountRow"]');
						var deductibleRowInput = newRow.find('input[name="deductibleRow"]');
						amountInput.addClass('amount'+i);
						amountInput.attr('name', 'valuationAmount'+sequence);
						amountInput.val(amounts[i]);
						amountInput.attr('value', amounts[i]);
						amountRowInput.attr('name', 'amountRow'+sequence);
						amountRowInput.val(amountRowNames[i]);
						amountRowInput.attr('value', amountRowNames[i]);
						deductibleInput.attr('name', 'valuationDeductible'+sequence);
						deductibleInput.val(deductibles[j]);
						deductibleInput.attr('value', deductibles[j]);
						deductibleRowInput.attr('name', 'deductibleRow'+sequence);
						deductibleRowInput.val(deductRowNames[j]);
						deductibleRowInput.attr('value', deductRowNames[j]);
						newRow.find('input[name="valuationCost"]').attr('name', 'valuationCost'+sequence);
						newRow = newRow.insertAfter(valuationAmounts.last().closest('tr'));
						valuationAmounts = jQuery('.amount'+i);
					}
				}
			}
		}
		jQuery('#valuationNum').val(thisInstance.valuationSequence);
	},

	registerDeleteItemClickEvent : function() {
		var thisInstance = this;
		jQuery('.icon-trash').on('click', function(e) {
			var currentRow = jQuery(e.currentTarget).closest('tr');
			var lineItemId = currentRow.find('.lineItemId').val();
			//console.log(currentRow.attr('class'))
			if(lineItemId) {
				var dataURL = 'index.php?module=TariffServices&action=DeleteItem&rowType='+currentRow.attr('class')+'&lineItemId='+lineItemId;
				var response = confirm("Deleting this item will immediately remove it from this record. Proceed?");
				if(response == true) {
					AppConnector.request(dataURL).then(

						function(data) {
							if(data.success) {
								currentRow.remove();
							}
						},
						function(error) {
						}
					);
				}
			} else {currentRow.remove();}
		});
	},

	registerHideHasVans : function () {
		var thisInstance = this;
		var node = jQuery('input[name="hourlyset_hasvan"]');
		node.on('change', function() {
			if(node.is(':checked')) {
				jQuery(".hasVans").removeClass('hide');
				jQuery('.hasVanCol').attr('colspan', 2);
			}
			else {
				jQuery(".hasVans").addClass('hide');
				jQuery('.hasVanCol').attr('colspan', 3);
			}
		});
	},

	registerHideContainers : function () {
		var thisInstance = this;
		var node = jQuery('input[name="packing_containers"]');
		node.on('change', function() {
			if(node.is(':checked')) {
				var numColumns = 1;
				if(jQuery(".hasPacking").hasClass('hide') == false) {
					numColumns++;
				}
				if(jQuery(".hasUnpacking").hasClass('hide') == false) {
					numColumns++;
				}
				var calcwidth = 0;
				if(numColumns > 0) {
					calcwidth = 60/numColumns;
				}
				jQuery(".hasContainers").attr('style', 'width:'+calcwidth+'%;text-align:center');
				jQuery(".hasPacking").attr('style', 'width:'+calcwidth+'%;text-align:center');
				jQuery(".hasUnpacking").attr('style', 'width:'+calcwidth+'%;text-align:center');
				jQuery(".hasContainers").removeClass('hide');
				jQuery(".noChecks").addClass('hide');
			}
			else {
				var numColumns = 0;
				if(jQuery(".hasPacking").hasClass('hide') == false) {
					numColumns++;
				}
				if(jQuery(".hasUnpacking").hasClass('hide') == false) {
					numColumns++;
				}
				var calcwidth = 0;
				if(numColumns > 0) {
					calcwidth = 60/numColumns;
				}
				jQuery(".hasContainers").attr('style', 'width:'+calcwidth+'%;text-align:center');
				jQuery(".hasPacking").attr('style', 'width:'+calcwidth+'%;text-align:center');
				jQuery(".hasUnpacking").attr('style', 'width:'+calcwidth+'%;text-align:center');
				jQuery(".hasContainers").addClass('hide');
				if(numColumns == 0) {
					jQuery(".noChecks").removeClass('hide');
				}

			}
		});
	},

	registerHidePacking : function () {
		var thisInstance = this;
		var node = jQuery('input[name="packing_haspacking"]');
		node.on('change', function() {
			if(node.is(':checked')) {
				var numColumns = 1;
				if(jQuery(".hasContainers").hasClass('hide') == false) {
					numColumns++;
				}
				if(jQuery(".hasUnpacking").hasClass('hide') == false) {
					numColumns++;
				}
				var calcwidth = 0;
				if(numColumns > 0) {
					calcwidth = 60/numColumns;
				}
				jQuery(".hasContainers").attr('style', 'width:'+calcwidth+'%;text-align:center');
				jQuery(".hasPacking").attr('style', 'width:'+calcwidth+'%;text-align:center');
				jQuery(".hasUnpacking").attr('style', 'width:'+calcwidth+'%;text-align:center');
				jQuery(".hasPacking").removeClass('hide');
				jQuery(".noChecks").addClass('hide');


			}
			else {
				var numColumns = 0;
				if(jQuery(".hasContainers").hasClass('hide') == false) {
					numColumns++;
				}
				if(jQuery(".hasUnpacking").hasClass('hide') == false) {
					numColumns++;
				}
				var calcwidth = 0;
				if(numColumns > 0) {
					calcwidth = 60/numColumns;
				}
				jQuery(".hasContainers").attr('style', 'width:'+calcwidth+'%;text-align:center');
				jQuery(".hasPacking").attr('style', 'width:'+calcwidth+'%;text-align:center');
				jQuery(".hasUnpacking").attr('style', 'width:'+calcwidth+'%;text-align:center');
				jQuery(".hasPacking").addClass('hide');
				if(numColumns == 0) {
					jQuery(".noChecks").removeClass('hide');
				}

			}
		});
	},

	registerHideUnpacking : function () {
		var thisInstance = this;
		var node = jQuery('input[name="packing_hasunpacking"]');
		node.on('change', function() {
			if(node.is(':checked')) {
				var numColumns = 1;
				if(jQuery(".hasContainers").hasClass('hide') == false) {
					numColumns++;
				}
				if(jQuery(".hasPacking").hasClass('hide') == false) {
					numColumns++;
				}
				var calcwidth = 0;
				if(numColumns > 0) {
					calcwidth = 60/numColumns;
				}
				jQuery(".hasContainers").attr('style', 'width:'+calcwidth+'%;text-align:center');
				jQuery(".hasPacking").attr('style', 'width:'+calcwidth+'%;text-align:center');
				jQuery(".hasUnpacking").attr('style', 'width:'+calcwidth+'%;text-align:center');
				jQuery(".hasUnpacking").removeClass('hide');
				jQuery(".noChecks").addClass('hide');


			}
			else {
				var numColumns = 0;
				if(jQuery(".hasContainers").hasClass('hide') == false) {
					numColumns++;
				}
				if(jQuery(".hasPacking").hasClass('hide') == false) {
					numColumns++;
				}
				var calcwidth = 0;
				if(numColumns > 0) {
					calcwidth = 60/numColumns;
				}
				jQuery(".hasContainers").attr('style', 'width:'+calcwidth+'%;text-align:center');
				jQuery(".hasPacking").attr('style', 'width:'+calcwidth+'%;text-align:center');
				jQuery(".hasUnpacking").attr('style', 'width:'+calcwidth+'%;text-align:center');
				jQuery(".hasUnpacking").addClass('hide');
				if(numColumns == 0) {
					jQuery(".noChecks").removeClass('hide');
				}

			}
		});
	},

	registerChargeMatrix : function() {
		var matrixElm = jQuery('input:checkbox[name="service_base_charge_matrix"]');
		var chargeElm = jQuery('input[name="service_base_charge"]');

		matrixElm.on('change', function() {
			//IFF checked clear and readonly the charge text field.
			if(matrixElm.is(':checked')) {
				chargeElm.prop('readonly', true).val('');
				//chargeElm.attr('readonly', true).val('');
				chargeElm.prop('readonly', true).val('');
				jQuery('.serviceChargeMatrixInfo').removeClass('hide');
				//there is probably a way to not this from the removeClass line
				jQuery('.defaultServiceCharge').addClass('hide');
			} else {
				chargeElm.prop('readonly', false);
				jQuery('.serviceChargeMatrixInfo').addClass('hide');
			}
		});
		matrixElm.trigger('change');
	},

	registerServiceCodeDependency : function () {
		if(jQuery('input[name="instance"]').val() != 'graebel')
		{
			return;
		}
		var rules = {
			service_line : {
				conditions : [
					{
						operator : 'is',
						value: 'HHG',
						targetFields: [
							{
								name: 'service_code',
								pickListOptions: [
									'TRLA - Transportation Labor',
									'TRAN - Transportation Charges',
									'STPI - Storage Pickup',
									'SITD - Storage Delivery',
									'CSPA - Custom Service Packing',
									'FSPA - Full Service Packing',
									'FSLA - Full Service Packing-Labor',
									'FSMA - Full Service Packing-Material',
									'PACO - Packing Containers',
									'PALA - Packing Labor',
									'TRVA - Transit Valuation',
									'RVP - Replacement Value Protection',
									'RVPA - Add’l Replacement Value Protection',
									'FRV - Full Replacement Value',
									'FMSV - First Month Storage Valuation',
									'MTSV - Monthly Storage Valuation',
									'STVA - Storage Valuation',
									'SIVA - Storage In Transit Valuation',
									'FMPS - First Month Perm Storage',
									'PMTH - Perm Storage Monthly',
									'PEST - Perm Storage',
									'SITA - Storage In Transit-add’l days',
									'SITF - Storage In Transit-First Day',
									'STFB - Storage 1st day (Bundled)',
									'STFW - Storage 1st day-Whse Handling',
									'WAHA - Warehouse Handling/Perm',
									'WHST - Warehouse Handling SIT',
									'ADCH - Advance Charges',
									'APSO - Appliance Service-Origin',
									'APSD - Appliance Service-Dest.',
									'AUTO - Auto, Truck, Van',
									'BULK - Bulky Article',
									'CRAT - Crating',
									'CSUN - Custom Service Unpacking',
									'DEPI - Debris Pickup',
									'DICO - Distance Carry - Origin',
									'DICD - Distance Carry - Dest.',
									'ELEO - Elevator - Origin',
									'ELED - Elevator - Dest.',
									'EXDE - Extra Delivery',
									'EXLO - Extra Labor - Origin',
									'EXLD - Extra Labor - Dest.',
									'EXPI - Extra Pickup',
									'FFWR - Fine Finish Wrapping',
									'FSUN - Full Service Unpack',
									'IMOP - Impractical Operations',
									'MISC - Miscellaneous',
									'OVLO - Overtime Loading',
									'OVUN - Overtime Unloading',
									'PIOH - Piano Handling',
									'STCO - Stair Carry-Origin',
									'STCD - Stair Carry-Dest.',
									'UNFW - Unit Fine Finish Wrapping',
									'UNLA - Unpacking Labor',
									'UNUW - Unit Upholstery Wrapping',
									'UPWR - Upholstery Wrapping',
									'WATD - Waiting Time-Dest.',
									'WATO - Waiting Time-Origin',
									'WTLO - Waiting Time Labor-Origin',
									'WTLD - Waiting Time Labor-Dest.',
									'WTVO - Waiting Time Van-Origin',
									'WTVD - Waiting Time Van-Dest.',
									'UNWR - Unwrapping',
									'WRMA - Wrapping Material',
									'ADTO - Add’l Transportation-Origin',
									'ADTD - Add’l Transportation-Dest.',
									'OSCH - Origin Service Charge',
									'DSCH - Destination Service Charge',
									'FUCH - Fuel Charge',
									'FUSU - Fuel Surcharge',
									'FUSC - Fuel Surcharge Cartage',
									'FUSV - Fuel surcharge-Vehicle',
									'AUFE - Audit Fee',
									'BRCN - Brokerage Commissions-Non-Inter',
									'GMIS - G/Miscellaneous Inter-Company',
									'IINV - International not Billed',
									'INSM - Insurance Surcharge',
									'REMK - Remarks',
									'SATA - Sales Tax',
									'CODC - C O D Payment Received',
									'CODR - C O D Reclassification',
									'CRCA - Credit Card – American Express',
									'CRCD - Credit Card – Discoverer',
									'CRCM - Credit Card – Mastercard',
									'CRCV - Credit Card – Visa',
									'REFU - Refund',
									'ORSE - Origin Services',
									'DESE - Destination Services',
									'MSIO - Miscellaneous International - Origin',
									'MSID - Miscellaneous International - Dest.',
									'ELIO - Extra Labor International - Origin',
									'ELID - Extra Labor International - Dest.',
									'EXMD - Excess Miles International - Dest.',
									'EXMO - Excess Miles International - Origin.',
									'IOIO - Impractical Operations International - Origin',
									'IOID - Impractical Operations International - Dest.',
									'BOOI - Booking Commissions-Int’l',
									'INIT - Insurance-International',
									'SFCH - Single Factor Charge',
									'ORSF - Origin Svc – Single Factor',
									'DESF - Dest. Svc – Single factor',
									'SFLA - SFR – Complete Pack Labor',
									'SFMA - SFR – Complete Pack Material',
								]
							}
						]
					},
					{
						operator: 'is',
						value: 'WPS',
						targetFields: [
							{
								name: 'service_code',
								pickListOptions: [
									'X15B - 1.5 Book Carton',
									'X30M - 3.0 Medium Carton',
									'MISC - Miscellaneous Services',
									'XAB1 - Anti-Static Lg. Bubble 24x250',
									'XAB2 - Anti-Static Lg. Bubble 48x250',
									'XAB3 - Anti-Static Sm. Bubble 24x750',
									'XAB4 - Anti-Static Sm. Bubble 48x750',
									'XBC - Rolling Book Cart',
									'XBU1 - Large Bubble 24x375',
									'XBU2 - Large Bubble 48x250',
									'XCAL - California Intrastate',
									'XCB - Corner Boards',
									'XCBC - Canadian Border Crossing',
									'XCCU - Climate Control Van',
									'XCOF - Change Order Fixed Price Costs',
									'XCPK - Cell Pack Kit',
									'XCRG - Corrugate',
									'XCT - Cap Toggles',
									'XCWT - Warehousing - Hundred Weight',
									'DEMI - Deadhead Mileage',
									'XDLS - Four Wheel Dolly',
									'XDLT - Two Wheel Dolly',
									'XDP - Dock Plate',
									'ADDI - Desk Riser',
									'XDSH - Dishpack Carton',
									'XDT0 - 8.9 Cu Ft Carton',
									'XDT1 - "D" Container With Pallet',
									'XDT2 - "D" Container Without Pallet',
									'XDT3 - "E" Container (42x29x25.5)',
									'XDT4 - 1.5 Cu Ft Lock Box (DTW)',
									'XDT5 - 10.6 Cu Ft Carton',
									'XDT6 - 15.2 Cu Ft Carton',
									'XDT7 - 17.0 Cu Ft Carton',
									'XDT8 - 20.0 Cu Ft Carton',
									'XDT9 - 5.4 Cu Ft Carton',
									'XDW - Dolphin Foam Wrap',
									'XERP - Employee Relocation Packet',
									'XSTP - Extra Stop',
									'XFL - Fork Lift',
									'XFL2 - Forklift',
									'GMIS - G/Miscellaneous Inter-Company',
									'FUSU - Fuel Surcharge',
									'XGDL - Gondolas - Wooden',
									'XINS - Installer',
									'XISA - System Administrator-Inventory',
									'XITC - Inventory Technician',
									'XJB - Johnson Bar',
									'XKBD - Kick Back Dolly',
									'XL - Labels',
									'XLBR - Labor',
									'XLB1 - 1.5 Cu Ft Lock Box',
									'XLB2 - 2.3 Cu Ft Lock Box',
									'XLC - Lamp Carton',
									'XLC1 - 4.5 Cu Ft Large Carton',
									'XLC2 - 6.0 Cu Ft Large Carton',
									'XLD - Local Driver',
									'XLIN - Installer-Lead',
									'XLL - Load Locks',
									'XLTL - Less Than Truck Load Shipment',
									'XM - Masonite',
									'XMC - Machine Carts',
									'XMCF - Microfoam',
									'XMIS - Miscellaneous Materials',
									'XMOV - Mover',
									'XNPP - Newsprint Packing',
									'XOVD - Overnight Detention',
									'XP - Placards',
									'XPAL - Warehousing - Pallet',
									'FEBR - Ferry/Bridge',
									'XPC - Panel Cart',
									'XPCL - Picture Carton Large',
									'XPCS - Picture Carton Small',
									'XPDS - Furniture Pads',
									'XPDW - Per Day Waiting Time Charge',
									'XPEA - 15 Cu Ft Styrofoam Bag',
									'MISC - Miscellaneous Services',
									'XPJE - Pallet Jack Electrical',
									'XPJM - Pallet Jack Manual',
									'XPKR - Packer',
									'XPM - Project Manager',
									'XPV - Pack Van',
									'XRAL - Roll-A-Lift(set)',
									'XRDL - Refer dolly',
									'XRSC - 1.5 Cu Ft Record Storage Cartn',
									'XRSH - Warehousing - Rack Shelf',
									'XRVC - Replacement Value-Cargo',
									'XRVS - Replacement Value-Storage',
									'XS - Straps',
									'XSB - 3/4" or 1/2" Steel Banding',
									'XSB1 - Small Bubble Wrap 24x750',
									'XSB2 - Small Bubble Wrap 48x750',
									'XSC - Spider Crane',
									'XSF - Warehousing - Square Feet',
									'XSFL - Static Free Bubble Bag Lg',
									'XSFM - Static Free Bubble Bag Med',
									'XSP - Speed Pack',
									'SPCT - Special Compensation-Trnsp',
									'XST - Straight Truck',
									'SATA - Sales Tax',
									'XSTT - Storage Trailer',
									'XSUP - Inventory Supervisor',
									'XSPP - Project Supervisor',
									'SURV - Survey',
									'XSW1 - 18" Shrink Wrap',
									'XSW2 - 20" Shrink Wrap',
									'XTP1 - Tape 2" Duct',
									'XTP2 - Tape 2" PVC 55 Yd Roll',
									'XTP3 - Tape Pack',
									'XTP4 - Tape Blue',
									'XTP6 - Masking Tape',
									'XTEX - Texas Instrastate Trans. Srvc.',
									'XTL - Truck Load Shipments',
									'XTMP - Team Pay',
									'XTNU - Truck Ordered - Not Used',
									'X3P - Third Party Services',
									'XTS - Tool Set',
									'XTT - Tractor Trailer',
									'XUPK - Unpacker',
									'XVLT - Warehousing - Vault',
									'XWB - Walkboard',
									'XWFO - Warehouse Forklift Operator',
									'XWLB - Warehouse Labor',
									'XWSP - Warehouse Supervisor',
									'XZLB - Zip Lock Bags',
									'XPSV - Passenger Van',
									'XCCB - Cable/Computer Bags',
									'XPCK - Plastic Crate',
									'XPCD - Plastic Crate Dolly',
									'XPCC - Plastic Computer Crate',
									'XPFC - Plastic File Crate',
									'XPA - Project Administration Fee',
									'XBRF - Valet Briefcase Boxes',
									'XCWP - Corregated Wall Protection',
									'XMSK - Carpet Mask',
									'XPLT - Pallet',
									'XPPR - Paper Pad',
									'XPRT - Parts Bag',
									'XSGB - Super Gum Bands',
									'XSUH - Supplies/Hardware',
									'XSW3 - Security Stretch Wrap',
									'XWDB - Wardrobe Carton',
									'XCLS - Cleaning Supplies',
									'XCUW - Comp U Wraps',
									'XMAD - Mat-A-Doors',
									'XSCD - Spider Crane Dollies',
									'XSG - Space Gobblers',
									'XSGI - Space Gobbler Inflators',
									'XVAC - Vacuum',
									'XDSP - Disposal/Dumpster Fee',
									'XCUF - Warehousing - Cubic Feet',
									'XSW3 - Security Stretch Wrap',
									'XCPT - Computer Technician',
									'XCAD - AutoCAD Operator',
									'XPSD - Planning Services Director',
									'XPCO - Project Coordinator',
									'XSPM - Senior Project Manager',
									'XSPL - Space Planner',
								]
							}
						]
					}
				]
			}
		};
		this.applyVisibilityRules(rules, true);
	},

	registerEventForAssignToModule : function() {
		var editViewForm = this.getForm();
		editViewForm.on('change','[name="tariffservices_assigntomodule"]', function (e) {
			var element=jQuery(e.currentTarget);
			var selectedModule = element.val();
			var tariffservices_assigntorecord = editViewForm.find('input[name="tariffservices_assigntorecord"]');
			var parentTd=tariffservices_assigntorecord.closest('td.fieldValue');
			parentTd.find('input[name="popupReferenceModule"]').val(selectedModule);
		});
	},
	loadBlocksByBusinesLine: function(module) {

		var business_lines = '';
		/*jQuery('.result-selected').each(function( index ) {
		 business_lines = business_lines + '::' + jQuery.trim(jQuery( this ).text());
		 });*/

		business_lines = jQuery('select[name^="rate_type"]').find(':selected').val();

		var dataUrl = "index.php?module=Potentials&action=GetHiddenBlocks&formodule=" + module + "&businessline=" + business_lines;
		AppConnector.request(dataUrl).then(
			function (data) {

				if (data.success) {
					var showBlocks = [];
					for (var key in data.result.show) {
						showBlocks.push(data.result.show[key]);
						jQuery("table[name='" + data.result.show[key] + "']").removeClass('hide');
					}
					for (var key in data.result.hide) {
						if (showBlocks.indexOf(data.result.hide[key]) < 0) {
							jQuery("table[name='" + data.result.hide[key] + "']").addClass('hide');
						}
					}
				}
			},
			function (error, err) {

			}
		);
	},

	registerRateTypeChangeEvent : function()
	{
		var thisInstance = this;
		jQuery('.contentsDiv').on('change', 'select[name^="rate_type"]', function(){
			thisInstance.loadBlocksByBusinesLine('TariffServices');
		});
	},
    
    registerTariffSectionChangeEvent : function () {
        var thisInstance = this;
        var fieldElement = jQuery('[name="tariff_section"]');
        fieldElement.on(Vtiger_Edit_Js.postReferenceSelectionEvent, function() {
            thisInstance.updateDiscountField(jQuery(this));
        });
    },

    updateDiscountField : function(tariffSelectionElement) {
        var tariffSectionID = tariffSelectionElement.val();

        if (!tariffSectionID) {
            return;
        }

        var dataUrl = "index.php?module=TariffSections&action=GetSectionInformation&record=" + tariffSectionID;
        AppConnector.request(dataUrl).then(
            function (data) {
                if (data.success) {
                    var serviceDiscountElement = jQuery('[type="checkbox"][name="tariffservices_discountable"]');
                    if(
                        data.result &&
                        data.result['is_discountable'] == 1
                    ) {
                        serviceDiscountElement.attr('checked', true);
                        Vtiger_Edit_Js.setReadonly(serviceDiscountElement, false);
                    } else {
                        Vtiger_Edit_Js.setReadonly(serviceDiscountElement, true);
                        //serviceDiscountElement.removeAttr('checked');
                        serviceDiscountElement.attr('checked', false);
                    }
                } else {
                    console.log("Error getting info: " + data.error.message);
                }
            },
            function (error, err) {
            }
        );
    },

	registerEvents : function() {
		this._super();
		var effectiveDateTd = jQuery('#effective_date_display,#related_tariff_display').closest('td');
		effectiveDateTd.children().addClass('hide');
		effectiveDateTd.prev().children().addClass('hide');
		jQuery('#related_tariff_display').closest('tr').addClass('hide');
		this.registerAddButtons('LBL_TARIFFSERVICES_BASEPLUS');
		this.registerAddButtons('LBL_TARIFFSERVICES_BREAKPOINT');
		this.registerAddButtons('LBL_TARIFFSERVICES_WEIGHTMILEAGE');
		this.registerAddButtons('LBL_TARIFFSERVICES_BULKY');
		this.registerAddButtons('LBL_TARIFFSERVICES_CHARGEPERHUNDRED');
		this.registerAddButtons('LBL_TARIFFSERVICES_COUNTYCHARGE');
		this.registerAddButtons('LBL_TARIFFSERVICES_HOURLYSET');
		this.registerAddButtons('LBL_TARIFFSERVICES_PACKING');
		this.registerAddButtons('LBL_TARIFFSERVICES_CWTBYWEIGHT');
		this.registerAddButtons('valuationAmountTable');
		this.registerAddButtons('valuationDeductibleTable');
		this.registerAddButtons('LBL_TARIFFSERVICES_SERVICECHARGE');
		this.registerAddButtons('LBL_TARIFFSERVICES_FLATRATEBYWEIGHT');
		this.registerValuationEditButtons();
		this.registerDeleteItemClickEvent();
		this.registerHideHasVans();
		this.registerHideContainers();
		this.registerHidePacking();
		this.registerHideUnpacking();
		this.registerChargeMatrix();
		this.loadBlocksByBusinesLine('TariffServices');
		this.registerServiceCodeDependency();
		this.registerEventForAssignToModule();
		this.registerRateTypeChangeEvent();
        this.registerTariffSectionChangeEvent();
	}
});

