Vtiger_Edit_Js("Elevators_EditBlock_Js", {
	getInstance: function() {
		return new Elevators_EditBlock_Js();
	}
}, {
	registerRemoveElevatorsButton : function(){
		jQuery('html').on( 'click', '.removeElevators', function(){
			if(jQuery(this).siblings('input:hidden[name^="elevatorId"]').val() == 'none'){
				jQuery(this).parent().parent().remove()
			} else{
				jQuery(this).parent().parent().addClass('hide');
				jQuery(this).siblings('input:hidden[name^="elevatorDelete"]').val('deleted');
			}
		});
	},

	registerAddElevatorsButtons : function() {
		var thisInstance = this;
		var table = jQuery('[name^="ElevatorsTable"]').find('tbody');

		var button = jQuery('.addElevators');

		var addHandler = function() {
			var newRow = jQuery('.defaultElevators').clone();
			var sequenceNode = jQuery("input[name='numElevatorsAgents']");
			//a beautiful way to handle the tally that tracks the number of the elevator we are currently adding
			var sequence = sequenceNode.val();
			sequence++;
			sequenceNode.val(sequence);
			newRow.addClass('newElevators');
			//remove the classes from the default row that cause it to be hidden and labeled
			newRow.removeClass('hide defaultElevators');

			newRow.find('input, select').each(function(idx, ele){
				if(jQuery(ele).attr('name') == 'elevators_sequence') {
					jQuery(ele).find('option[value="1"]').remove();
				}
				if(jQuery(ele).attr('name') == 'elevators_cube' || jQuery(ele).attr('name') == 'elevators_weight') {
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

			jQuery(document).find('select[name^="elevators_sequence_"]').trigger("change");
		};
		button.on('click', addHandler);
	},

	registerEventForWeightOverride : function () {
		var thisInstance=this;
		jQuery(document).find('[name="weight"]').attr('readonly','readonly');
		jQuery(document).on('change','input[name^="elevators_weightoverride_"]',function(){
			var weightoverride= jQuery(this).val();
			var parentTr=jQuery(this).closest('tr.elevatorRow');
			var cubeoverride = weightoverride/7;
			parentTr.find('input[name^="elevators_weight_"]').attr('readonly','readonly');
			parentTr.find('input[name^="elevators_cubeoverride_"]').val(cubeoverride.toFixed(2));

			var totalweightoverride=thisInstance.calculateTotalWeight();
			if(totalweightoverride >0) {
				jQuery(document).find('input[name="weight"]').val(totalweightoverride);
			}
			jQuery('[name="weight"]').trigger('change');

		});

		jQuery(document).on('change','input[name^="elevators_weight_"]',function(){
			var totalweightoverride=thisInstance.calculateTotalWeight();
			if(totalweightoverride >0) {
				jQuery(document).find('input[name="weight"]').val(totalweightoverride);
			}
		});


		jQuery(document).find('input[name^="elevators_weightoverride_"]').trigger("change");
		jQuery(document).find('input[name^="elevators_weight_"]').trigger("change");
	},

	calculateTotalWeight : function () {
		var totalweightoverride=0;
		jQuery(document).find('tr.elevatorRow').each(function (i,tre) {
			var tr = jQuery(tre);
			if (!tr.hasClass('hide')) {
				var weight = parseFloat(tr.find('input[name^="elevators_weightoverride_"]').val());
				if(isNaN(weight) || weight == 0) {
					weight = parseFloat(tr.find('input[name^="elevators_weight_"]').val());
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
		jQuery(document).on('change','select[name^="elevators_sequence_"]',function(e){
			var currentTarget=jQuery(e.currentTarget);
			var currentName=currentTarget.attr('name');
			var selectedValue=currentTarget.val();
			jQuery.each(jQuery(document).find('select[name^="elevators_sequence_"]'), function (idx, elm) {
				var elemt=jQuery(elm);
				if(jQuery(elemt).attr('name') != currentName && selectedValue !='') {
					elemt.find('option[value="'+selectedValue+'"]').remove();
					//elemt.append(jQuery('<option>', {value:preVal, text:preVal}));
					elemt.trigger("liszt:updated");
				}
			})
		});
		jQuery(document).find('select[name^="elevators_sequence_"]').trigger("change");

	},

	registerEventVerifyTable : function () {
		jQuery('table[name=ElevatorsTable]').find('input[name^="elevators_percent_"]').on('focus',function (){
			var inputElevatorsTable = jQuery('table[name=ElevatorsTable]').find('input[name^="elevators_percent_"]');
			jQuery.each(inputElevatorsTable, function(index, obj){
				jQuery(obj).css( 'border-color', '#ccc' );
				jQuery(obj).parent('.input-append').find('span.add-on').css( 'border-color', '#ccc' );
			});
		});

		jQuery('#btn-verifyElevatorTable').on('click',function () {
			var inputElevatorsTable = jQuery('table[name=ElevatorsTable]').find('input[name^="elevators_percent_"]');
			var inputValues = [];
			jQuery.each(inputElevatorsTable, function(index, obj){
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

			jQuery.each(inputElevatorsTable, function(index, obj){
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
		jQuery(document).find('tr.elevatorRow').each(function (i,tre) {
			var tr= jQuery(tre);
			if(!tr.hasClass('hide')) {
				var sequence = tr.find('.row_num').val();
				var elevators_fromcube = tr.find('.elevators_fromcube').val();
				tr.find('input, select').each(function(idx, ele){
					if((jQuery(ele).attr('name') == 'elevators_cube' || jQuery(ele).attr('name') == 'elevators_weight') && (elevators_fromcube || sequence == 1)) {
						jQuery(ele).attr('readonly','readonly');
					}
					if((jQuery(ele).attr('name') == 'elevators_sequence' || jQuery(ele).attr('name') == 'elevators_origin' || jQuery(ele).attr('name') == 'elevators_destination' || jQuery(ele).attr('name') == 'elevators_transportation') && sequence == 1) {
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

		this.registerAddElevatorsButtons();
		this.registerRemoveElevatorsButton();
		this.registerEventForWeightOverride();
		this.registerSequenceChangeEvent();
		this.registerEventVerifyTable();

	},
});

jQuery(document).ready(function() {
	var instance = Elevators_EditBlock_Js.getInstance();
	instance.registerEvents();
});