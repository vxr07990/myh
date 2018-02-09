Vtiger_Edit_Js("MenuGroups_EditBlock_Js", {
	getInstance: function() {
		return new MenuGroups_EditBlock_Js();
	}
}, {
	registerRemoveMenuGroupsButton : function(container){
		var thisInstance = this;

		container.on( 'click', '.deleteMappingButton', function(){
			if(jQuery(this).siblings('input:hidden[name^="menugroupid"]').val() == ''){
				jQuery(this).closest('div.MenuGroupsRecords').remove()
			} else{
				jQuery(this).closest('div.MenuGroupsRecords').addClass('hide');
				jQuery(this).siblings('input:hidden[name^="menugroup_deleted_"]').val('deleted');
			}
			var rowno=jQuery('div.MenuGroupsRecords').length;
			jQuery('[name="MenuGroupsTable"]').find('[name="numMapping"]').val(rowno);

			var currentMenuGroupsRecords = jQuery(this).closest('div.MenuGroupsRecords');
			// Remove selectedPicklistItems on current selections
			var currentSelections = currentMenuGroupsRecords.find('select[name^="group_module_"]:not(input)');
			currentSelections.each(function () {
				var focus = $(this);
				if (thisInstance.selectedPicklistItems[focus.attr('name')]) {
					delete thisInstance.selectedPicklistItems[focus.attr('name')];
				}
			});

			// Update other module group field on other groups
			var otherSelections = container.find('select[name^="group_module_"]:not(input)').not(currentSelections);
			otherSelections.each(function () {
				var focus = $(this);
				thisInstance.changeModuleGroupFields(focus, true);
			});
			
		});
	},

	registerAddMenuGroupsButtons : function() {
		var thisInstance = this;
		var editViewForm = this.getForm();
		var container = jQuery('[name="MenuGroupsTable"]');
		container.find('.addMenuGroups').on('click', function () {
			var rowno=jQuery('div.MenuGroupsRecords').length;
			var viewParams = {
				"type": "POST",
				"url": 'index.php?module=MenuGroups',
				"dataType": "html",
				"data": {
					'view': 'MassActionAjax',
					'mode': 'generateNewBlock',
					'rowno': rowno
				}
			};

			AppConnector.request(viewParams).then(
				function (data) {
					if (data) {
						var newItemCodeMapping=jQuery(data);

						app.showSelect2ElementView(newItemCodeMapping.find('.select2'));
						jQuery('div.MenuGroupsList').append(newItemCodeMapping);
						jQuery('[name="MenuGroupsTable"]').find('[name="numMapping"]').val(rowno+1);
						thisInstance.registerEventForModuleGroupFields2(jQuery('div.MenuGroupsList'), newItemCodeMapping);
						thisInstance.makeMenuItemsListSortable(jQuery('div.MenuGroupsList'));
						// jQuery('div.MenuGroupsList').find('[name^="group_module_"]').trigger("change");
					}
				}
			)
		});
	},

	registerEventForChangeBlockTitle: function (container) {
		var thisInstance = this;
		container.on("change",'[name^="group_name_"]', function () {
			var MenuGroupsRecords = jQuery(this).closest('div.MenuGroupsRecords');
			var MenuGroupsTitle = MenuGroupsRecords.find('.MenuGroupsTitle');
			if(jQuery(this).val() !='') {
				MenuGroupsTitle.html(jQuery(this).val());
			}else{
				MenuGroupsTitle.html('Description of Group');
			}
		});
	},

	registerEventForModuleGroupFields : function (container) {
		var thisInstance = this;
		container.find('select[name^="group_module_"]').on('focus', function (e) {
			var element=jQuery(e.currentTarget);
			element.data('pre-values', element.val());
		}).change(function (e) {
			var element=jQuery(e.currentTarget);
			var selectModule=element.val();
			if(element.data('pre-values') != null && typeof(element.data('pre-values')) != 'undefined') {
				var removeModule = arr_diff(selectModule, jQuery(this).data('pre-values'));
			}
			var currentElmName=element.attr('name');

			container.find('select[name^="group_module_"]').each(function (idx, ele) {
				if(jQuery(ele).attr('name')!=currentElmName) {
					if(selectModule != null) {
						jQuery.each(selectModule, function (i, moduleName) {
							jQuery(ele).find('option[value="' + moduleName + '"]').remove();
						})
					}
					if(removeModule != null && typeof(removeModule) != 'undefined' && removeModule != 'undefined') {
						if(typeof removeModule[0] !='undefined') {
							jQuery(ele).append('<option value="' + removeModule[0] + '" >'+removeModule[0]+'</option>');
						}
					}
					jQuery(ele).select2();
					thisInstance.makeMenuItemsListSortable(container);
				}
			});
			element.data('pre-values', element.val());
		});
		container.find('[name^="group_module_"]').trigger("change");
	},

	makeMenuItemsListSortable : function(container) {
		var thisInstance = this;
		var selectElements = container.find('select[name^="group_module_"]');
		jQuery.each(selectElements, function (idx, selectElement) {
			var select2Element = app.getSelect2ElementFromSelect(jQuery(selectElement));
			//TODO : peform the selection operation in context this might break if you have multi select element in menu editor
			//The sorting is only available when Select2 is attached to a hidden input field.
			var select2ChoiceElement = select2Element.find('ul.select2-choices');

			select2ChoiceElement.sortable({
				'containment': select2ChoiceElement,
				start: function() { jQuery('#selectedMenus').select2("onSortStart");
					},
				update: function() {
					jQuery('#selectedMenus').select2("onSortEnd");
				}
			});
		})
	},

	selectedPicklistItems: {},

	getHiddenOptions: function (current) {
		var thisInstance = this;
		var hiddenOptions = [];

		for (var k in thisInstance.selectedPicklistItems) {
			if (!thisInstance.selectedPicklistItems.hasOwnProperty(k) || k == current.attr('name')) {
				continue;
			}

			hiddenOptions = hiddenOptions.concat(thisInstance.selectedPicklistItems[k]).unique();
		}

		return hiddenOptions;
	},

	/**
	 * @link layouts/vlayout/modules/Settings/MenuEditor/resources/MenuEditor.js:101
	 * Function which will get the selected columns with order preserved
	 * @return : array of selected values in order
	 */
	getSelectedModuleColumns : function(selectElement) {
		var deferred = $.Deferred();

		// var selectElement = this.getMenuListSelectElement();
		var select2Element = app.getSelect2ElementFromSelect(selectElement);

		var selectedValuesByOrder = {};
		var selectedOptions = selectElement.find('option:selected');
		var orderedSelect2Options = select2Element.find('li.select2-search-choice').find('div');
		var i = 1;
		orderedSelect2Options.each(function(index,element){
			var chosenOption = jQuery(element);
			selectedOptions.each(function(optionIndex, domOption){
				var option = jQuery(domOption);
				if(option.html() == chosenOption.html()) {
					selectedValuesByOrder[i++] = option.val();
					return false;
				}
			});
		});

		deferred.resolve(selectedValuesByOrder);

		// return selectedValuesByOrder;
		return deferred.promise();
	},

	/**
	 * @param focus
	 * @param {=Boolean} includeFocus
	 */
	changeModuleGroupFields: function (focus, includeFocus) {
		var thisInstance = this;
		if (typeof includeFocus === 'undefined' || !includeFocus) {
			includeFocus = false;
		}
		var focusChangeVal = focus.val();
		thisInstance.selectedPicklistItems[focus.attr('name')] = focusChangeVal ? focusChangeVal : [];

		for (var k in thisInstance.selectedPicklistItems) {
			if (!thisInstance.selectedPicklistItems.hasOwnProperty(k) || (k == focus.attr('name') && !includeFocus)) {
				continue;
			}

			// Get other select
			var select = $('[name="' + k + '"]');

			(function (select) {
				var dSelectedModuleColumns = thisInstance.getSelectedModuleColumns(select);
				dSelectedModuleColumns.then(function (selectedValuesByOrder) {
					var hiddenOptions = thisInstance.getHiddenOptions(select);
					var selectVal = select.val();
					if (!selectVal) {
						selectVal = [];
					}
					var fieldinfo = select.data('fieldinfo');
					var allOptions = fieldinfo.picklistvalues;
					var newOptions = {};

					// Merge with selected
					for (var k in selectedValuesByOrder) {
						if (!selectedValuesByOrder.hasOwnProperty(k)) {
							continue;
						}

						var selectedValue = selectedValuesByOrder[k];

						if (!newOptions.hasOwnProperty(selectedValue)) {
							// Use label instead of value
							newOptions[selectedValue] = (allOptions[selectedValue]) ? allOptions[selectedValue] : selectedValue;
						}
					}

					// Merge with not select
					for (var k in allOptions) {
						if (!allOptions.hasOwnProperty(k) || hiddenOptions.indexOf(k) >= 0) {
							continue;
						}

						if (!newOptions.hasOwnProperty(k)) {
							newOptions[k] = allOptions[k];
						}
					}

					// Clear and re-create options
					select.select2('destroy');
					select.empty();

					for (var k in newOptions) {
						if (!newOptions.hasOwnProperty(k)) {
							continue;
						}

						var newOption = $('<option/>')
							.attr('value', k)
							.text(newOptions[k]);
						if (selectVal.indexOf(k) >= 0) {
							newOption.prop('selected', true);
						}

						select.append(newOption);
					}

					// select.val(selectVal);
					select.select2();
					thisInstance.makeMenuItemsListSortable(select.parent());
				});
			})(select);

			// select.trigger('change');
		}
	},

	updateSelectedOptions: function (focus){
		var thisInstance = this;
		var hiddenOptions = thisInstance.getHiddenOptions(focus);

		for (var k in thisInstance.selectedPicklistItems) {
			if (!thisInstance.selectedPicklistItems.hasOwnProperty(k) || k != focus.attr('name')) {
				continue;
			}

			var select = $('[name="' + k + '"]');
			var selectVal = select.val();
			var fieldinfo = select.data('fieldinfo');
			var allOptions = fieldinfo.picklistvalues;
			select.empty();

			var newOptions = {};
			for (var k in allOptions) {
				if (!allOptions.hasOwnProperty(k) || hiddenOptions.indexOf(k) >= 0) {
					continue;
				}

				newOptions[k] = allOptions[k];
			}

			for (var k in newOptions) {
				if (!newOptions.hasOwnProperty(k)) {
					continue;
				}

				var newOption = $('<option/>')
					.attr('value', k)
					.text(newOptions[k]);
				select.append(newOption);
			}

			// select.trigger('change');
		}
	},

	registerEventForModuleGroupFields2: function (container, focusContainer) {
		var thisInstance = this;

		var currentSelections = container.find('select[name^="group_module_"]:not(input)');
		currentSelections.each(function () {
			var focus = $(this);
			var focusVal = focus.val();
			thisInstance.selectedPicklistItems[focus.attr('name')] = focusVal ? focusVal : [];

			focus.on('change', function () {
				var focusChange = $(this);
				thisInstance.changeModuleGroupFields(focusChange);
			});
		});

		if (focusContainer) {
			var currentSelections1 = focusContainer.find('select[name^="group_module_"]:not(input)');
			currentSelections1.each(function () {
				var focusNew = $(this);
				var focusNewVal = focusNew.val();
				thisInstance.selectedPicklistItems[focusNew.attr('name')] = focusNewVal ? focusNewVal : [];
				thisInstance.updateSelectedOptions(focusNew);

				focusNew.on('change', function () {
					var focusNewChange = $(this);
					thisInstance.changeModuleGroupFields(focusNewChange);
				});
			});
		}
	},

	registerCustomFunctions: function () {
		Array.prototype.unique = function () {
			var a = this.concat();
			for (var i = 0; i < a.length; ++i) {
				for (var j = i + 1; j < a.length; ++j) {
					if (a[i] === a[j])
						a.splice(j--, 1);
				}
			}

			return a;
		};
	},

	submitForm: function (container) {
	    var thisInstance = this;
	    container.submit(function (e) {
			// e.preventDefault();
			
			//
		var selects = container.find('select[name^="group_module_"]:not(input)');
		selects.each(function () {
		    var select = $(this);
		    (function (select) {
			var dSelectedModuleColumns = thisInstance.getSelectedModuleColumns(select);
			dSelectedModuleColumns.then(function (selectedValuesByOrder) {
			    var selectName = select.attr('name');
			    var input = $('[name="' + selectName.substring(0, selectName.length - 2) + '_selected_order"]');
			    input.val(JSON.stringify(selectedValuesByOrder));
			});
		    })(select);
		});
		
		var otherSelect = jQuery('#menuListSelectElement');
		var dSelectedModuleColumns = thisInstance.getSelectedModuleColumns(otherSelect);
		dSelectedModuleColumns.then(function (selectedValuesByOrder) {
		    var selectName = otherSelect.attr('id');
		    var input = $('[name="' + selectName + '_selected_order"]');
		    input.val(JSON.stringify(selectedValuesByOrder));
		});

		return true;
	    });
	},
	arrangeSelectChoicesInOrder : function() {
		jQuery('select.select2').each(function (i,e) {
			var selectElement = jQuery(this);
			var fieldName = selectElement.attr('name');
			var fieldName = fieldName.replace("[]", "");
			var select2Element = app.getSelect2ElementFromSelect(selectElement);

			var choicesContainer = select2Element.find('ul.select2-choices');
			var choicesList = choicesContainer.find('li.select2-search-choice');
			var selectedOptions = selectElement.find('option:selected');
			if(jQuery(this).prop("id") == "menuListSelectElement"){
			    fieldName = jQuery(this).prop("id");
			}
			var selectedOrder = JSON.parse(jQuery('input[name="'+fieldName+'_selected_order"]').val());
			for(var index=selectedOrder.length ; index > 0 ; index--) {
				var selectedValue = selectedOrder[index-1];
				var option = selectedOptions.filter('[value="'+selectedValue+'"]');
				choicesList.each(function(choiceListIndex,element){
					var liElement = jQuery(element);
					if(liElement.find('div').html() == option.html()){
						choicesContainer.prepend(liElement);
						return false;
					}
				});
			}
		});
	},
	registerEvents : function() {
		var thisInstance = this;

		var container = jQuery('div.MenuGroupsList');
		var formContainer = jQuery('#EditView');
		this.registerCustomFunctions();
		this.registerAddMenuGroupsButtons();
		this.arrangeSelectChoicesInOrder();
		this.registerRemoveMenuGroupsButton(container);
		this.registerEventForChangeBlockTitle(container);
		this.registerEventForModuleGroupFields2(container);

		// Update Module option on edit record item
		var record = app.getRecordId();
		if (record) {

			// Update other module group field on other groups
			var otherSelections = container.find('select[name^="group_module_"]:not(input)');
			otherSelections.each(function () {
				var focus = $(this);
				thisInstance.changeModuleGroupFields(focus, true);
			});

		}

		this.makeMenuItemsListSortable(container);
		this.submitForm(formContainer);
	},
	

});

jQuery(document).ready(function() {
    var instance = MenuGroups_EditBlock_Js.getInstance();
    instance.registerEvents();
});

function arr_diff (a1, a2) {

	var a = [], diff = [];

	for (var i = 0; i < a1.length; i++) {
		a[a1[i]] = true;
	}

	for (var i = 0; i < a2.length; i++) {
		if (a[a2[i]]) {
			delete a[a2[i]];
		} else {
			a[a2[i]] = true;
		}
	}

	for (var k in a) {
		diff.push(k);
	}

	return diff;
};