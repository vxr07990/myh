Vtiger_Edit_Js("LongCarries_EditBlock_Js", {
	getInstance: function() {
		return new LongCarries_EditBlock_Js();
	}
}, {
	registerRemoveLongCarriesButton : function(){
		jQuery('html').on( 'click', '.removeLongCarries', function(){
			if(jQuery(this).siblings('input:hidden[name^="longcarryId"]').val() == 'none'){
				jQuery(this).parent().parent().remove()
			} else{
				jQuery(this).parent().parent().addClass('hide');
				jQuery(this).siblings('input:hidden[name^="longcarryDelete"]').val('deleted');
			}
		});
	},

	registerAddLongCarriesButtons : function() {
		var thisInstance = this;
		var table = jQuery('[name^="LongCarriesTable"]').find('tbody');

		var button = jQuery('.addLongCarries');

		var addHandler = function() {
			var newRow = jQuery('.defaultLongCarries').clone();
			var sequenceNode = jQuery("input[name='numLongCarriesAgents']");
			//a beautiful way to handle the tally that tracks the number of the longcarry we are currently adding
			var sequence = sequenceNode.val();
			sequence++;
			sequenceNode.val(sequence);
			newRow.addClass('newLongCarries');
			//remove the classes from the default row that cause it to be hidden and labeled
			newRow.removeClass('hide defaultLongCarries');

			newRow.find('input, select').each(function(idx, ele){
				if(jQuery(ele).attr('name') == 'longcarries_sequence') {
					jQuery(ele).find('option[value="1"]').remove();
				}
				if(jQuery(ele).attr('name') == 'longcarries_cube' || jQuery(ele).attr('name') == 'longcarries_weight') {
					jQuery(ele).attr('readonly','readonly');
				}
				jQuery(ele).attr('name', jQuery(ele).attr('name')+'_'+sequence);
				jQuery(ele).attr('id', jQuery(ele).attr('id')+'_'+sequence);

				if(jQuery(ele).is('select')) {
					jQuery(ele).addClass('chzn-select');
					jQuery(ele).css('width', '150px')
				}else{
					jQuery(ele).css('width', '100px')
				}
			});

			//add the new row to the table
			newRow = newRow.appendTo(table);
			//notifiy the js library that handles the reformating the ui has changed
			newRow.find('.chzn-select').chosen();

			jQuery(document).find('select[name^="longcarries_sequence_"]').trigger("change");
		};
		button.on('click', addHandler);
	},

	registerEventForWeightOverride : function () {
		var thisInstance=this;
		jQuery(document).find('[name="weight"]').attr('readonly','readonly');
		jQuery(document).on('change','input[name^="longcarries_weightoverride_"]',function(){
			var weightoverride= jQuery(this).val();
			var parentTr=jQuery(this).closest('tr.longcarryRow');
			var cubeoverride = weightoverride/7;
			parentTr.find('input[name^="longcarries_weight_"]').attr('readonly','readonly');
			parentTr.find('input[name^="longcarries_cubeoverride_"]').val(cubeoverride.toFixed(2));

			var totalweightoverride=thisInstance.calculateTotalWeight();
			if(totalweightoverride >0) {
				jQuery(document).find('input[name="weight"]').val(totalweightoverride);
			}
			jQuery('[name="weight"]').trigger('change');

		});

		jQuery(document).on('change','input[name^="longcarries_weight_"]',function(){
			var totalweightoverride=thisInstance.calculateTotalWeight();
			if(totalweightoverride >0) {
				jQuery(document).find('input[name="weight"]').val(totalweightoverride);
			}
		});


		jQuery(document).find('input[name^="longcarries_weightoverride_"]').trigger("change");
		jQuery(document).find('input[name^="longcarries_weight_"]').trigger("change");
	},

	calculateTotalWeight : function () {
		var totalweightoverride=0;
		jQuery(document).find('tr.longcarryRow').each(function (i,tre) {
			var tr = jQuery(tre);
			if (!tr.hasClass('hide')) {
				var weight = parseFloat(tr.find('input[name^="longcarries_weightoverride_"]').val());
				if(isNaN(weight) || weight == 0) {
					weight = parseFloat(tr.find('input[name^="longcarries_weight_"]').val());
				}
				if(isNaN(weight)) {
					weight=0;
				}
				totalweightoverride = totalweightoverride + weight;
			}
		});
		return totalweightoverride;
	},

	registerSequenceChangeEvent : function(){
		jQuery(document).on('change','select[name^="longcarries_sequence_"]',function(e){
			var currentTarget=jQuery(e.currentTarget);
			var currentName=currentTarget.attr('name');
			var selectedValue=currentTarget.val();
			jQuery.each(jQuery(document).find('select[name^="longcarries_sequence_"]'), function (idx, elm) {
				var elemt=jQuery(elm);
				if(jQuery(elemt).attr('name') != currentName && selectedValue !='') {
					elemt.find('option[value="'+selectedValue+'"]').remove();
					//elemt.append(jQuery('<option>', {value:preVal, text:preVal}));
					elemt.trigger("liszt:updated");
				}
			})
		});
		jQuery(document).find('select[name^="longcarries_sequence_"]').trigger("change");

	},

	registerEventVerifyTable : function () {
		var inputLongCarriesTable = jQuery('table[name=LongCarriesTable]').find('input[name^="longcarries_percent_"]');
		jQuery('table[name=LongCarriesTable]').find('input[name^="longcarries_percent_"]').on('focus',function (){
			jQuery.each(inputLongCarriesTable, function(index, obj){
				jQuery(obj).css( 'border-color', '#ccc' );
				jQuery(obj).parent('.input-append').find('span.add-on').css( 'border-color', '#ccc' );
			});
		});

		jQuery('#btn-verifyLongCarryTable').on('click',function () {
			var inputValues = [];
			jQuery.each(inputLongCarriesTable, function(index, obj){
				inputValues.push(parseFloat(jQuery(obj).val()));
				jQuery(obj).css( 'border-color', '#ccc' );
				jQuery(obj).parent('.input-append').find('span.add-on').css( 'border-color', '#ccc' );
			});

			var sorted_arr = inputValues.slice().sort();
			var duplicates = [];
			for (var i = 0; i < inputValues.length - 1; i++) {
				if (sorted_arr[i + 1] == sorted_arr[i]) {
					duplicates.push(sorted_arr[i]);
				}
			}

			duplicates = duplicates.filter(function(elem, index, self) {
				return index == self.indexOf(elem);
			})

			jQuery.each(inputLongCarriesTable, function(index, obj){
				var duplicate = duplicates.indexOf(parseFloat(jQuery(obj).val()));
				if (duplicate !== -1){
					jQuery(obj).css( 'border-color', 'red' );
					jQuery(obj).parent('.input-append').find('span.add-on').css( 'border-radius', '3px' );
					jQuery(obj).parent('.input-append').find('span.add-on').css( 'border-color', 'red' );
				}
			});
		});
	},

	registerEvents : function() {
		// Update field name
		jQuery(document).find('tr.longcarryRow').each(function (i,tre) {
			var tr= jQuery(tre);
			if(!tr.hasClass('hide')) {
				var sequence = tr.find('.row_num').val();
				var longcarries_fromcube = tr.find('.longcarries_fromcube').val();
				tr.find('input, select').each(function(idx, ele){
					if((jQuery(ele).attr('name') == 'longcarries_cube' || jQuery(ele).attr('name') == 'longcarries_weight') && (longcarries_fromcube || sequence == 1)) {
						jQuery(ele).attr('readonly','readonly');
					}
					if((jQuery(ele).attr('name') == 'longcarries_sequence' || jQuery(ele).attr('name') == 'longcarries_origin' || jQuery(ele).attr('name') == 'longcarries_destination' || jQuery(ele).attr('name') == 'longcarries_transportation') && sequence == 1) {
						jQuery(ele).prop('disabled', true).trigger('liszt:updated');
					}

					jQuery(ele).attr('name', jQuery(ele).attr('name')+'_'+sequence);
					jQuery(ele).attr('id', jQuery(ele).attr('id')+'_'+sequence);
					if(jQuery(ele).is('select')) {
						jQuery(ele).css('width', '150px')
					}else{
						jQuery(ele).css('width', '100px')
					}
				});
			}
		});

		this.registerAddLongCarriesButtons();
		this.registerRemoveLongCarriesButton();
		this.registerEventForWeightOverride();
		this.registerSequenceChangeEvent();
		this.registerEventVerifyTable();

	},
});

jQuery(document).ready(function() {
	var instance = LongCarries_EditBlock_Js.getInstance();
	instance.registerEvents();
});