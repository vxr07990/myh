Vtiger_Edit_Js("TariffManager_Edit_Js",{},{
	registerViewAllButtons: function() {
		jQuery('.viewAllAgents').off('click').on('click', function() {
			var elementId = jQuery(this).attr('id');
			jQuery.colorbox({inline:true, width:'500px', height:'90%', left:'15%', top:'-5%', href:'#'+elementId+'Div', onClosed:function(){jQuery(document.body).css({overflow:'auto'});}, onComplete:function(){jQuery(document.body).css({overflow:'hidden'});}});
		});
	},

	registerApplyToAllAgents: function() {
		jQuery('.assignAllAgents').off('blur').on('blur', function() {
			var elementId = jQuery(this).attr('id');
			var divId = '#view'+elementId.substr(6)+'Div';
			var isChecked = jQuery(this).prop('checked');
			if(isChecked) {
				jQuery(divId).find('input[type="checkbox"]').each(function() {
					jQuery(this).prop('checked', false);
					jQuery(this).trigger('click');
					jQuery(this).on('click', function() {return false;});
				});
			} else {
				jQuery(divId).find('input[type="checkbox"]').each(function() {
					jQuery(this).off('click');
				});
			}
		});
	},

	registerAssignVanline: function() {
		jQuery('#assignVanline').on('click', function() {
			jQuery.colorbox({inline:true, width:'500px', height:'90%', left:'15%', top:'-5%', href:'#assignVanlinesDiv', onClosed:function(){jQuery(document.body).css({overflow:'auto'});}, onComplete:function(){jQuery(document.body).css({overflow:'hidden'});}});
		});
	},

	registerAssignVanlineSubmit: function() {
		var thisInstance = this;
		jQuery('.assignVanlineSubmit').on('click', function() {
			jQuery('#assignVanlinesDiv').find('.selectVanline').each(function() {
				var isChecked = jQuery(this).prop('checked');
				var id = jQuery(this).attr('id').substr(6);
				console.dir(id+'State');
				console.dir(jQuery('input[name="'+id+'State"]'));
				console.dir(jQuery('input[name="'+id+'State"]').val());
				if(isChecked && jQuery('input[name="'+id+'State"]').val() != 'assigned') {
					var row = jQuery(this).closest('tr');
					var html = "<td style='width:2%;text-align:center' class='vanline"+id.substr(7)+"'>";
					html += "<a class='deleteVanlineButton' id='delete"+id.substr(7)+"'>";
					html += "<i title='Delete' class='icon-trash alignMiddle'></i>";
					html += "</a>";
					html += "</td>";
					html += "<td style='width:35%' class='vanline"+id.substr(7)+"'>"+row.find('.vanlineName').html()+"</td>";
					html += "<td style='width:8%;text-align:center' class='vanline"+id.substr(7)+"'><button type='button' class='viewAllAgents' id='view"+id+"Agents'>View All</button></td>"
					html += "<td style='width:5%;text-align:center' class='vanline"+id.substr(7)+"'><input type='hidden' name='assign"+id+"Agents' value='0' /><input type='checkbox' class='assignAllAgents' name='assign"+id+"Agents' id='assign"+id+"Agents' /></td>";

					if(jQuery('.emptyRecord').length) {
						var appendRow = jQuery('.emptyRecord').closest('tr');
						jQuery('.emptyRecord').remove();
						appendRow.append(html);
					} else {
						html = "<tr>"+html;
						html += "<td style='width:2%' class='emptyRecord'>&nbsp;</td>";
						html += "<td style='width:35%' class='emptyRecord'>&nbsp;</td>";
						html += "<td style='width:8%' class='emptyRecord'>&nbsp;</td>";
						html += "<td style='width:5%' class='emptyRecord'>&nbsp;</td>";
						html += "</tr>";
						jQuery('#assignedVanlinesTable').append(html);
					}
					var stateInput = jQuery('input[name="'+id+'State"]');
					if(stateInput.length) {
						stateInput.val('assigned');
						stateInput.attr('value', 'assigned');
					} else {
						jQuery('#assignedVanlinesTable').append("<input type='hidden' name='"+id+"State' value='assigned' />");
					}
					row.addClass('hide');
				}
			});
			thisInstance.registerViewAllButtons();
			thisInstance.registerApplyToAllAgents();
			thisInstance.registerRemoveVanline();
			thisInstance.registerAutoFillCustomJs();
			thisInstance.limitCustomTariffPicklist();
			jQuery.colorbox.close();
		});
	},

	registerRemoveVanline: function() {
		var thisInstance = this;
		jQuery('.deleteVanlineButton').off('click').on('click', function() {
			var vanlineId = jQuery(this).attr('id').substr(6);
			jQuery('.vanline'+vanlineId).empty();
			jQuery('.vanline'+vanlineId).removeClass().addClass('emptyRecord');
			jQuery('#Vanline'+vanlineId).removeClass('hide');
			jQuery('#assignVanline'+vanlineId).prop('checked', false);
			jQuery('input[name="Vanline'+vanlineId+'State"]').val('unassigned').attr('value', 'unassigned');
			console.dir(jQuery('input[name="Vanline'+vanlineId+'State"]').val());
			thisInstance.compressVanlineTable();
			thisInstance.limitCustomTariffPicklist();
		});
	},

	compressVanlineTable: function() {
		var thisInstance = this;
		var tableBody = jQuery('#assignedVanlinesTable');
		var rows = tableBody.find('tr');
		var rowToFill = -1;
		var emptyBlockHtml = "<td style='width:2%' class='emptyRecord'>&nbsp;</td>";
		emptyBlockHtml += "<td style='width:35%' class='emptyRecord'>&nbsp;</td>";
		emptyBlockHtml += "<td style='width:8%' class='emptyRecord'>&nbsp;</td>";
		emptyBlockHtml += "<td style='width:5%' class='emptyRecord'>&nbsp;</td>";
		for(row = 0; row < rows.length; row++) {
			//Traverse table row-by-row
			if(rowToFill == -1) {
				//Haven't found any blanks yet
				var blockOneBlank = jQuery(rows[row]).find('td:eq(0)').hasClass('emptyRecord');
				var blockTwoBlank = jQuery(rows[row]).find('td:eq(4)').hasClass('emptyRecord');
				if(blockOneBlank && blockTwoBlank) {
					jQuery(rows[row]).remove();
					thisInstance.compressVanlineTable();
					break;
				} else if(blockOneBlank) {
					rowToFill = row;
					//Shift blockTwo over to fill in for blockOne
					jQuery(rows[row]).find('.emptyRecord').remove();
					jQuery(rows[row]).append(emptyBlockHtml);
					thisInstance.compressVanlineTable();
					break;
				} else if(blockTwoBlank) {
					rowToFill = row;
					if(row+1 == rows.length) {
						//No more rows, so this match may be ignored and loop may end
						break;
					}
					jQuery(rows[row]).find('.emptyRecord').remove();
					for(var ind = 0; ind <= 3; ind++) {
						jQuery(rows[row+1]).find('td:eq(0)').appendTo(jQuery(rows[row]));
					}
					jQuery(rows[row+1]).append(emptyBlockHtml);
					thisInstance.compressVanlineTable();
					break;
				}
			}
		}
	},

	limitCustomTariffPicklist: function() {
		//get the vanline
		var vanlineIds = [];
		jQuery("#assignedVanlinesTable").find('input[class^="VanlineId"]').each(function() {
			vanlineIds.push(jQuery(this).val());
		});
		var allied = false;
		var northAmerican = false;
		var base = false;
		for	(i = 0; i < vanlineIds.length; i++) {
			if(vanlineIds[i] == 1 && !northAmerican){
				allied = true;
			} else if(vanlineIds[i] == 9 && !allied){
				northAmerican = true;
			} else {
				base = true;
				break;
			}
		}

		var alliedTariffs = [//Allied Only
			'Base',
			'TPG',
			'Allied Express',
			'TPG GRR',
			'ALLV-2A',
			'400N Base',
			'400N/104G',
			'400NG',
			//'Local/Intra',
			//'Max 3',
			//'Max 4',
			'Intra - 400N',
			'Canada Gov\'t',
			'Canada Non-Govt',
			'UAS',
		];

		var northAmericanTariffs = [//North American Only
			'Base',
			'Pricelock',
			'Blue Express',
			'Pricelock GRR',
			'NAVL-12A',
			'400N Base',
			'400N/104G',
			'400NG',
			//'Local/Intra',
			//'Max 3',
			//'Max 4',
			'Intra - 400N',
			'Canada Gov\'t',
			'Canada Non-Govt',
			'UAS',
            'Truckload Express'
		];
		var baseTariffs = [//Else
			'Base',
			'400N Base',
            '400NG'
		];
		if(jQuery('input:hidden[name="instance"]').val() == 'graebel'){
			baseTariffs.push('1950-B');
			baseTariffs.push('MSI');
			baseTariffs.push('MMI');
			baseTariffs.push('400NG');
			baseTariffs.push('400N/104G');
			baseTariffs.push('AIReS');
			baseTariffs.push('RMX400');
			baseTariffs.push('RMW400');
			baseTariffs.push('ISRS200-A');
			baseTariffs.push('09CapRelo');
			baseTariffs.push('GSA01');
			baseTariffs.push('GSA-500A');
			baseTariffs.push('400DOE');
		}
		//hide the wrong fields
		jQuery('select[name="custom_tariff_type"]').find('option').prop('disabled',true);
		//we don't want to disable Select an Option
		jQuery('select[name="custom_tariff_type"]').find('option[value=""]').prop('disabled',false);
		if(base){
			//only show base
			console.dir('show base');
			for	(i = 0; i < baseTariffs.length; i++) {
				jQuery('select[name="custom_tariff_type"]').find('option[value="'+baseTariffs[i]+'"]').prop('disabled',false);
			}
		} else if(!base && allied > 0) {
			//show allied
			console.dir('show allied');
			for	(i = 0; i < alliedTariffs.length; i++) {
				jQuery('select[name="custom_tariff_type"]').find('option[value="'+alliedTariffs[i]+'"]').prop('disabled',false);
			}
		} else if(!base && northAmerican > 0){
			//show northAmerican
			console.dir('show northAmerican');
			for	(i = 0; i < northAmericanTariffs.length; i++) {
				jQuery('select[name="custom_tariff_type"]').find('option[value="'+northAmericanTariffs[i]+'"]').prop('disabled',false);
			}
		}
		//don't let selected stay something that was disabled
		if(jQuery('select[name="custom_tariff_type"]').find('option:selected').prop('disabled')){
			jQuery('select[name="custom_tariff_type"]').find('option:selected').prop('selected', false);
		}
		jQuery('select[name="custom_tariff_type"]').trigger('liszt:updated');
	},
	registerAutoFillCustomJs: function() {

		var thisInstance = this;

		//jQuery('select[name="custom_tariff_type"]').siblings('.chzn-container').find('.chzn-results').on('change', function() {
		jQuery('select[name="custom_tariff_type"]').on('change', function() {
			var alliedTariffs = [//Allied Only
				'TPG',
				'Allied Express',
				'TPG GRR',
				'ALLV-2A',
				//'400N Base',
				//'400N/104G',
				'400NG',
				//'Local/Intra',
				//'Max 3',
				//'Max 4',
				'Intra - 400N',
				'Canada Gov\'t',
				'Canada Non-Govt',
				'UAS',
			];

			var northAmericanTariffs = [//North American Only
				'Pricelock',
				'Blue Express',
				'Pricelock GRR',
				'NAVL-12A',
				//'400N Base',
				//'400N/104G',
				'400NG',
				//'Local/Intra',
				//'Max 3',
				//'Max 4',
				'Intra - 400N',
				'Canada Gov\'t',
				'Canada Non-Govt',
				'UAS',
				'Truckload Express'
			];

			if(jQuery('input:hidden[name="instance"]').val() == 'graebel') {
				jQuery('input[name="custom_javascript"]').val('');
			} else {
				if (jQuery('input:hidden[name="instance"]').val() == 'sirva' && (jQuery.inArray(jQuery('select[name="custom_tariff_type"]').find('option:selected').val(), alliedTariffs) !== -1 ||
					                                                             jQuery.inArray(jQuery('select[name="custom_tariff_type"]').find('option:selected').val(), northAmericanTariffs) !== -1)) {

					//move 400's to Estimate_BaseSIRVA instead of tpg
					var effective_tariff = jQuery('select[name="custom_tariff_type"]').find('option:selected').val().search('400');
					if (effective_tariff >= 0) {
						jQuery('input[name="custom_javascript"]').val('Estimates_BaseSIRVA_Js');
					} else {
						jQuery('input[name="custom_javascript"]').val('Estimates_TPGTariff_Js');
					}
				} else {
					jQuery('input[name="custom_javascript"]').val('');
				}
			}
		});
	},

	registerAddValuation : function(){
		var addValuationRow = jQuery('.addValuationRow');

		//handler to add new valuation row
		var newValuationRow = function(){
			var newRow = jQuery('.defaultValuationRow').clone(true,true);
			var sequence = parseInt(jQuery('input:hidden[name="valuation_count"]').val());
			sequence++;
			console.dir(sequence);
			jQuery('input:hidden[name="valuation_id"]').val(sequence);

			//newRow.find('input').prop('required', true);
			newRow.removeClass('defaultValuationRow').removeClass('hide').addClass('newRow');
			newRow.appendTo(jQuery(this).closest('table'));
			app.registerEventForDatePickerFields();

			jQuery('.selections').chosen();
		}

		addValuationRow.on('click', newValuationRow);
	},

	registerDeleteValuation : function(){
		var thisInstance = this;
		jQuery('.deleteValuationBtn').on('click', function(e) {
			var currentRow = jQuery(this).closest('tr');
			currentRow.remove();
		});
	},

	freeCheckboxSetter: function() {
		jQuery('.free-checkbox').click(function() {
			if(jQuery(this).is(':checked')) {
				jQuery(this).next().val('y');
			} else {
				jQuery(this).next().val('n');
			}
		});
	},

	registerEvents: function() {
		this._super();
		this.freeCheckboxSetter();
		this.registerViewAllButtons();
		this.registerApplyToAllAgents();
		this.registerAssignVanline();
		this.registerAssignVanlineSubmit();
		this.registerRemoveVanline();
		this.limitCustomTariffPicklist();
		this.registerAutoFillCustomJs();
		this.registerDeleteValuation();
		this.registerAddValuation();

		//trigger things at start so if they aren't touched on edit's it'll still save fine.
		jQuery('.assignAllAgents').trigger('blur');
		jQuery('select[name="custom_tariff_type"]').trigger('change');
	}
});
