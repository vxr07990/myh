Vtiger_Edit_Js("AddressSegments_EditBlock_Js", {
	getInstance: function() {
		return new AddressSegments_EditBlock_Js();
	}
}, {
	registerRemoveAddressSegmentsButton : function(){
		jQuery('html').on( 'click', '.removeAddressSegments', function(){
			if(jQuery(this).siblings('input:hidden[name^="addresssegmentId"]').val() == 'none'){
				jQuery(this).parent().parent().remove()
			} else{
				jQuery(this).parent().parent().addClass('hide');
				jQuery(this).siblings('input:hidden[name^="addresssegmentDelete"]').val('deleted');
			}
		});
	},

	registerAddAddressSegmentsButtons : function() {
		var thisInstance = this;
		var table = jQuery('[name^="AddressSegmentsTable"]').find('tbody');

		var button = jQuery('.addAddressSegments');

		var addHandler = function() {
			var newRow = jQuery('.defaultAddressSegments').clone();
			var sequenceNode = jQuery("input[name='numAgents']");
			//a beautiful way to handle the tally that tracks the number of the addresssegment we are currently adding
			var sequence = sequenceNode.val();
			sequence++;
			sequenceNode.val(sequence);
			newRow.addClass('newAddressSegments');
			//remove the classes from the default row that cause it to be hidden and labeled
			newRow.removeClass('hide defaultAddressSegments');

			newRow.find('input, select').each(function(idx, ele){
				if(jQuery(ele).attr('name') == 'addresssegments_sequence') {
					jQuery(ele).find('option[value="1"]').remove();
				}
				if(jQuery(ele).attr('name') == 'addresssegments_cube' || jQuery(ele).attr('name') == 'addresssegments_weight') {
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

			jQuery(document).find('select[name^="addresssegments_sequence_"]').trigger("change");
		};
		button.on('click', addHandler);
	},

	updateTotalWeights : function () {
		var totalweightoverride=this.calculateTotalWeight();
		if(totalweightoverride >0) {
			jQuery(document).find('input[name="weight"]').val(Math.ceil(totalweightoverride));
			jQuery(document).find('input[name="local_weight"]').val(Math.ceil(totalweightoverride)).trigger('change');
		}
	},

	registerEventForWeightOverride : function () {
		var thisInstance=this;
		jQuery(document).find('[name="weight"]').attr('readonly','readonly');
		jQuery(document).on('change','input[name^="addresssegments_weightoverride_"]',function(){
			var weightoverride= jQuery(this).val();
			var parentTr=jQuery(this).closest('tr.addresssegmentRow');
			var cubeoverride = Math.ceil(weightoverride/7);
			parentTr.find('input[name^="addresssegments_weight_"]').attr('readonly','readonly');
			parentTr.find('input[name^="addresssegments_cubeoverride_"]').val(cubeoverride);

			thisInstance.updateTotalWeights();

			jQuery('[name="weight"]').trigger('change');

		});

		jQuery(document).on('change','input[name^="addresssegments_weight_"]',function(){
			thisInstance.updateTotalWeights();
		});


		jQuery(document).find('input[name^="addresssegments_weightoverride_"]').trigger("change");
		jQuery(document).find('input[name^="addresssegments_weight_"]').trigger("change");
	},

	calculateTotalWeight : function () {
		var totalweightoverride=0;
		jQuery(document).find('tr.addresssegmentRow').each(function (i,tre) {
			var tr = jQuery(tre);
			if (!tr.hasClass('hide')) {
				var weight = parseFloat(tr.find('input[name^="addresssegments_weightoverride_"]').val());
				if(isNaN(weight) || weight == 0) {
					weight = parseFloat(tr.find('input[name^="addresssegments_weight_"]').val());
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
		jQuery(document).on('change','select[name^="addresssegments_sequence_"]',function(e){
			var currentTarget=jQuery(e.currentTarget);
			var currentName=currentTarget.attr('name');
			var selectedValue=currentTarget.val();
			jQuery.each(jQuery(document).find('select[name^="addresssegments_sequence_"]'), function (idx, elm) {
				var elemt=jQuery(elm);
				if(jQuery(elemt).attr('name') != currentName && selectedValue !='') {
					elemt.find('option[value="'+selectedValue+'"]').remove();
					//elemt.append(jQuery('<option>', {value:preVal, text:preVal}));
					elemt.trigger("liszt:updated");
				}
			})
		});
		jQuery(document).find('select[name^="addresssegments_sequence_"]').trigger("change");

	},

	registerEvents : function() {
		// Update field name
		jQuery(document).find('tr.addresssegmentRow').each(function (i,tre) {
			var tr= jQuery(tre);
			if(!tr.hasClass('hide')) {
				var sequence = tr.find('.row_num').val();
				var addresssegments_fromcube = tr.find('.addresssegments_fromcube').val();
				tr.find('input, select').each(function(idx, ele){
					if((jQuery(ele).attr('name') == 'addresssegments_cube' || jQuery(ele).attr('name') == 'addresssegments_weight') && (addresssegments_fromcube || sequence == 1)) {
						jQuery(ele).attr('readonly','readonly');
					}
					if((jQuery(ele).attr('name') == 'addresssegments_sequence' || jQuery(ele).attr('name') == 'addresssegments_origin' || jQuery(ele).attr('name') == 'addresssegments_destination' || jQuery(ele).attr('name') == 'addresssegments_transportation') && sequence == 1) {
						//jQuery(ele).prop('disabled', true).trigger('liszt:updated');
						Vtiger_Edit_Js.setReadonly(jQuery(ele), true);
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

		this.registerAddAddressSegmentsButtons();
		this.registerRemoveAddressSegmentsButton();
		this.registerEventForWeightOverride();
		this.registerSequenceChangeEvent();

	},
});

jQuery(document).ready(function() {
	var instance = AddressSegments_EditBlock_Js.getInstance();
	instance.registerEvents();
});
