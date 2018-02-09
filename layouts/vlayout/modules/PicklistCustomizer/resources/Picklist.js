/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/
var PicklistCustomizer_Picklist_Js = {

	registerModulePickListChangeEvent: function () {

		jQuery('#agentmanager_id').on('change', function (e) {
			jQuery('#modulePickListValuesContainer').html('');
			if (jQuery("#agentmanager_id option:selected").val() == 'default') {
				return;
			}
			if (jQuery('#module_select option:selected').val() != 'default' && jQuery('#field_select option:selected').val() != 'default') {
				jQuery('#field_select').change();
			}
		});
		jQuery('#field_select').on('change', function (e) {
			jQuery('#modulePickListValuesContainer').html('');
			if (jQuery("#agentmanager_id option:selected").val() == 'default') {
				var params = {
					title: app.vtranslate('JS_ERROR'),
					text: app.vtranslate('Owner must be selected first.'),
					animation: 'show',
					type: 'error'
				};
				Vtiger_Helper_Js.showPnotify(params);
				jQuery('#field_select option[value="default"]').prop('selected', true).trigger('liszt:updated');
				return false;
			}
			if (jQuery('#field_select option:selected').val() == 'default') {
				return;
			}
			var params = {
				module: app.getModuleName(),
				source_module: jQuery('#module_select').val(),
				view: 'IndexAjax',
				mode: 'getPickListValueForField',
				pickListFieldName: jQuery('#field_select option:selected').data('fieldname'),
				pickListFieldId: jQuery('#field_select').val(),
				idAgentManager: jQuery("#agentmanager_id option:selected").val()
			}
			var progressIndicatorElement = jQuery.progressIndicator({
				'position': 'html',
				'blockInfo': {
					'enabled': true
				}
			});

			AppConnector.request(params).then(function (data) {
				jQuery('#modulePickListValuesContainer').html(data);
				app.showSelect2ElementView(jQuery('#rolesList'));
				PicklistCustomizer_Picklist_Js.registerItemActions();
				jQuery('#modulePickListValuesContainer').find('.chzn-select').chosen();
				progressIndicatorElement.progressIndicator({ 'mode': 'hide' });
			});
		});

		jQuery('#module_select').on('change', function () {
			jQuery('#modulePickListValuesContainer').html('');
			if (jQuery('#module_select option:selected').val() == 'default') {
				return;
			}
			var params = {
				module: app.getModuleName(),
				action: 'ActionAjax',
				mode: 'getPickListFieldsForModule',
				selectedModule: jQuery(this).val()
			};
			var progressIndicatorElement = jQuery.progressIndicator({
				'position': 'html',
				'blockInfo': {
					'enabled': true
				}
			});
			AppConnector.request(params).then(function (data) {
				if (data.success) {
					jQuery('#field_select').html(data.result);
					jQuery('#field_select').trigger('liszt:updated');
				}
				progressIndicatorElement.progressIndicator({ 'mode': 'hide' });
			});
		});
	},

	registerAddItemEvent: function () {
		jQuery('#addItem').on('click', function (e) {
			var valSelectLeadAgent = jQuery("#agentmanager_id option:selected").val();
			if (valSelectLeadAgent == 'default') {
				return;
			}
			jQuery("#id_lead_manager").val(valSelectLeadAgent);
			var data = jQuery('#createViewContents').find('.modal');
			var clonedCreateView = data.clone(true, true).removeClass('basicCreateView').addClass('createView');
			clonedCreateView.find('.rolesList').addClass('select2');
			var callBackFunction = function (data) {
				jQuery('[name="addItemForm"]', data).validationEngine();
				PicklistCustomizer_Picklist_Js.registerAddItemSaveEvent(data);
				PicklistCustomizer_Picklist_Js.regiserSelectRolesEvent(data);
			};
			app.showModalWindow(clonedCreateView, function (data) {
				if (typeof callBackFunction == 'function') {
					callBackFunction(data);
				}
			});
		});
	},

	//@TODO: In Use?
	registerAssingValueToRuleEvent: function () {
		jQuery('#assignValue').on('click', function () {
			var pickListValuesTable = jQuery('#pickListValuesTable');
			var selectedListItem = jQuery('.selectedListItem', pickListValuesTable);
			if (selectedListItem.length > 0) {
				var selectedValues = [];
				jQuery.each(selectedListItem, function (i, element) {
					selectedValues.push(jQuery(element).closest('tr').data('key'));

				});
			}

			var params = {
				module: app.getModuleName(),
				parent: app.getParentModuleName(),
				source_module: jQuery('#pickListModules').val(),
				view: 'IndexAjax',
				mode: 'showAssignValueToRoleView',
				pickListFieldId: jQuery('#modulePickList').val()
			};
			AppConnector.request(params).then(function (data) {
				app.showModalWindow(data);
				jQuery('[name="addItemForm"]', jQuery(data)).validationEngine();
				PicklistCustomizer_Picklist_Js.registerAssignValueToRoleSaveEvent(jQuery(data));
				if (selectedListItem.length > 0) {
					jQuery('[name="assign_values[]"]', jQuery('#assignValueToRoleForm')).select2('val', selectedValues);
				}
			});
		});
	},

	//@TODO: In Use?
	registerAssignValueToRoleSaveEvent: function (data) {
		jQuery('#assignValueToRoleForm').on('submit', function (e) {
			var form = jQuery(e.currentTarget);

			var assignValuesSelectElement = jQuery('[name="assign_values[]"]', form);
			var assignValuesSelect2Element = app.getSelect2ElementFromSelect(assignValuesSelectElement);
			var assignValueResult = Vtiger_MultiSelect_Validator_Js.invokeValidation(assignValuesSelectElement);
			if (assignValueResult != true) {
				assignValuesSelect2Element.validationEngine('showPrompt', assignValueResult, 'error', 'topLeft', true);
			} else {
				assignValuesSelect2Element.validationEngine('hide');
			}

			var rolesSelectElement = jQuery('[name="rolesSelected[]"]', form);
			var select2Element = app.getSelect2ElementFromSelect(rolesSelectElement);
			var result = Vtiger_MultiSelect_Validator_Js.invokeValidation(rolesSelectElement);
			if (result != true) {
				select2Element.validationEngine('showPrompt', result, 'error', 'bottomLeft', true);
			} else {
				select2Element.validationEngine('hide');
			}

			if (assignValueResult != true || result != true) {
				e.preventDefault();
				return;
			} else {
				form.find('[name="saveButton"]').attr('disabled', "disabled");
			}
			var params = jQuery(e.currentTarget).serializeFormData();
			AppConnector.request(params).then(function (data) {
				if (typeof data.result != 'undefined') {
					app.hideModalWindow();
					Settings_Vtiger_Index_Js.showMessage({ text: app.vtranslate('JS_VALUE_ASSIGNED_SUCCESSFULLY'), type: 'success' })
				}
			});
			e.preventDefault();
		});
	},

	//@TODO: In Use?
	registerEnablePickListValueClickEvent: function () {
		jQuery('#listViewContents').on('click', '.assignToRolePickListValue', function (e) {
			jQuery('#saveOrder').removeAttr('disabled');

			var pickListVaue = jQuery(e.currentTarget);
			if (pickListVaue.hasClass('selectedCell')) {
				pickListVaue.removeClass('selectedCell').addClass('unselectedCell');
				pickListVaue.find('.icon-ok').remove();
			} else {
				pickListVaue.removeClass('unselectedCell').addClass('selectedCell');
				pickListVaue.prepend('<i class="icon-ok pull-left"></i>');
			}
		});
	},

	//@TODO: In Use?
	registerenableOrDisableListSaveEvent: function () {
		jQuery('#saveOrder').on('click', function (e) {
			var progressIndicatorElement = jQuery.progressIndicator({
				'position': 'html',
				'blockInfo': {
					'enabled': true,
					'elementToBlock': jQuery('.tab-content')
				}
			});
			var pickListValues = jQuery('.assignToRolePickListValue');
			var disabledValues = [];
			var enabledValues = [];
			jQuery.each(pickListValues, function (i, element) {
				var currentValue = jQuery(element);
				if (currentValue.hasClass('selectedCell')) {
					enabledValues.push(currentValue.data('id'));
				} else {
					disabledValues.push(currentValue.data('id'));
				}
			});
			var params = {
				module: app.getModuleName(),
				parent: app.getParentModuleName(),
				action: 'SaveAjax',
				mode: 'enableOrDisable',
				enabled_values: enabledValues,
				disabled_values: disabledValues,
				picklistName: jQuery('[name="picklistName"]').val(),
				rolesSelected: jQuery('#rolesList').val()
			};
			AppConnector.request(params).then(function (data) {
				if (typeof data.result != 'undefined') {
					jQuery(e.currentTarget).attr('disabled', 'disabled');
					progressIndicatorElement.progressIndicator({ mode: 'hide' });
					Settings_Vtiger_Index_Js.showMessage({ text: app.vtranslate('JS_LIST_UPDATED_SUCCESSFULLY'), type: 'success' })
				}
			});
		});
	},

	//@TODO: In Use?
	regiserSelectRolesEvent: function (data) {
		data.find('[name="rolesSelected[]"]').on('change', function (e) {
			var rolesSelectElement = jQuery(e.currentTarget);
			var selectedValue = rolesSelectElement.val();
			if (jQuery.inArray('all', selectedValue) != -1) {
				rolesSelectElement.select2("val", "");
				rolesSelectElement.select2("val", "all");
				rolesSelectElement.select2("close");
				rolesSelectElement.find('option').not(':first').attr('disabled', 'disabled');
				data.find(jQuery('.modal-body')).append('<div class="alert alert-info textAlignCenter">' + app.vtranslate('JS_ALL_ROLES_SELECTED') + '</div>')
			} else {
				rolesSelectElement.find('option').removeAttr('disabled', 'disabled');
				data.find('.modal-body').find('.alert').remove();
			}
		});
	},

	registerRenameItemEvent: function () {
		var thisInstance = this;
		jQuery('#renameItem').on('click', function (e) {
			var valSelectLeadAgent = jQuery("#agentmanager_id option:selected").val();
			if (valSelectLeadAgent == 'default') {
				return;
			}
			var pickListValuesTable = jQuery('#pickListValuesTable');
			var selectedListItem = jQuery('.selectedListItem', pickListValuesTable);
			var selectedListItemLength = selectedListItem.length;
			if (selectedListItemLength > 1) {
				var params = {
					title: app.vtranslate('JS_MESSAGE'),
					text: app.vtranslate('JS_MORE_THAN_ONE_ITEM_SELECTED'),
					animation: 'show',
					type: 'error'
				};
				Vtiger_Helper_Js.showPnotify(params);

			} else if(selectedListItemLength == 0){
			    var params = {
			        title: app.vtranslate('JS_MESSAGE'),
                    text: app.vtranslate('JS_SELECT_SOME_VALUE'),
                    animation: 'show',
                    type: 'error'
                };
                Vtiger_Helper_Js.showPnotify(params);
            } else if(selectedListItem.closest('tr').data('special') == 1){
                var params = {
                        title: app.vtranslate('JS_MESSAGE'),
                        text: app.vtranslate('JS_CANNOT_RENAME_VALUE'),
                        animation: 'show',
                        type: 'error'
                };
                Vtiger_Helper_Js.showPnotify(params);
			} else {
				var params = {
					module: app.getModuleName(),
					source_module: jQuery('#module_select option:selected').val(),
					view: 'IndexAjax',
					mode: 'showEditView',
					idAgentManager: valSelectLeadAgent,
					pickListFieldId: jQuery('#field_select option:selected').val(),
					fieldValue: selectedListItem.closest('tr').data('key')
				};
				AppConnector.request(params).then(function (data) {
					app.showModalWindow(data);
					var form = jQuery('#renameItemForm');
					form.validationEngine();
					PicklistCustomizer_Picklist_Js.registerRenameItemSaveEvent();
				});
			}
		});
	},
	registerDeleteItemEvent: function () {
		var thisInstance = this;
		jQuery('#deleteItem').on('click', function (e) {
			var valSelectLeadAgent = jQuery("#agentmanager_id option:selected").val();
			if (valSelectLeadAgent == 'default') {
				return;
			}
			var pickListValuesTable = jQuery('#pickListValuesTable');
			var selectedListItem = jQuery('.selectedListItem', pickListValuesTable);
			var selectedListItemsArray = [];

                        var fieldNamesArray = [];
			jQuery.each(selectedListItem, function (index, element) {
                            if(jQuery(element).closest('tr').data('special') == 1){
                                fieldNamesArray.push(jQuery(element).closest('tr').data('key'));
                            }
                        });
                        if(fieldNamesArray.length > 0){
                            var stringNames = fieldNamesArray.toString();
                            var params = {
                                    title: app.vtranslate('JS_MESSAGE'),
                                    text: app.vtranslate('JS_CANNOT_DELETE_VALUE'),
                                    animation: 'show',
                                    type: 'error'
                            };
                            Vtiger_Helper_Js.showPnotify(params);
                            return;
                        }
			jQuery.each(selectedListItem, function (index, element) {
				selectedListItemsArray.push(jQuery(element).closest('tr').data('key'));
			});
            if(selectedListItemsArray.length == 0) {
                var params = {
                    title: app.vtranslate('JS_MESSAGE'),
                    text: app.vtranslate('JS_SELECT_SOME_VALUE'),
                    animation: 'show',
                    type: 'error'
                };
                Vtiger_Helper_Js.showPnotify(params);
                return;
            }
			var params = {
				module: app.getModuleName(),
				source_module: jQuery('#module_select option:selected').val(),
				view: 'IndexAjax',
				mode: 'showDeleteView',
				idAgentManager: valSelectLeadAgent,
				pickListFieldId: jQuery('#field_select option:selected').val(),
				fieldValue: JSON.stringify(selectedListItemsArray)
			};
			thisInstance.showDeleteItemForm(params);
		});
	},

	registerDeleteOptionEvent: function () {

		function result(value) {
			var replaceValueElement = jQuery('#replaceValue');
			if (typeof value.added != 'undefined') {
				var id = value.added.id;
				jQuery('#replaceValue option[value="' + id + '"]').remove();
				replaceValueElement.trigger('liszt:updated');
			} else {
				var id = value.removed.id;
				var text = value.removed.text;
				replaceValueElement.append('<option value="' + id + '">' + text + '</option>');
				replaceValueElement.trigger('liszt:updated');
			}
		}
		jQuery('[name="delete_value[]"]').on("change", function (e) {
			result({
				val: e.val,
				added: e.added,
				removed: e.removed
			});
		})
	},

	duplicateItemNameCheck: function (container) {
		var pickListValues = JSON.parse(jQuery('[name="pickListValues"]', container).val());
		var pickListValuesArr = [];
		jQuery.each(pickListValues, function (i, e) {
			var decodedValue = app.getDecodedValue(e);
			pickListValuesArr.push(jQuery.trim(decodedValue.toLowerCase()));
		});

		var mode = jQuery('[name="mode"]', container).val();
		var newValue = jQuery.trim(jQuery('[name="newValue"]', container).val());
		var lowerCasedNewValue = newValue.toLowerCase();

		//Checking the new picklist value is already exists
		if (jQuery.inArray(lowerCasedNewValue, pickListValuesArr) != -1) {
			//while renaming the picklist values
			if (mode == 'rename') {
				var oldValue = jQuery.trim(jQuery('[name="oldValue"]', container).val());
				var lowerCasedOldValue = oldValue.toLowerCase();
				//allow to rename when the new value should not be same as old value and the new value only with case diffrence
				if (oldValue != newValue && lowerCasedOldValue == lowerCasedNewValue) {
					return false;
				}
			}
			//while adding or renaming with different existing value
			return true;
		} else {
			return false;
		}
	},

	//@TODO: In Use?
	registerChangeRoleEvent: function () {
		jQuery('#rolesList').on('change', function (e) {
			var progressIndicatorElement = jQuery.progressIndicator({
				'position': 'html',
				'blockInfo': {
					'enabled': true,
					'elementToBlock': jQuery('.tab-content')
				}
			});
			var rolesList = jQuery(e.currentTarget);
			var params = {
				module: app.getModuleName(),
				parent: app.getParentModuleName(),
				view: 'IndexAjax',
				mode: 'getPickListValueByRole',
				rolesSelected: rolesList.val(),
				pickListFieldId: jQuery('#modulePickList').val()
			};
			AppConnector.request(params).then(function (data) {
				jQuery('#pickListValeByRoleContainer').html(data);
				PicklistCustomizer_Picklist_Js.registerenableOrDisableListSaveEvent();
				progressIndicatorElement.progressIndicator({ mode: 'hide' });
			});
		})
	},

	registerAddItemSaveEvent: function (container) {
		container.find('[name="addItemForm"]').on('submit', function (e) {
			var form = jQuery(e.currentTarget);
			var validationResult = form.validationEngine('validate');
			if (validationResult == true) {
				var duplicateCheckResult = PicklistCustomizer_Picklist_Js.duplicateItemNameCheck(container);
				if (duplicateCheckResult == true) {
					var errorMessage = app.vtranslate('JS_DUPLIACATE_ENTRIES_FOUND_FOR_THE_VALUE');
					var newValueEle = jQuery('[name="newValue"]', container);
					var newValue = newValueEle.val();
					newValueEle.validationEngine('showPrompt', errorMessage + ' ' + '"' + newValue + '"', 'error', 'bottomLeft', true);
					e.preventDefault();
					return;
				}
				var invalidFields = form.data('jqv').InvalidFields;
				if (invalidFields.length == 0) {
					form.find('[name="saveButton"]').attr('disabled', "disabled");
				}

				var params = jQuery(e.currentTarget).serializeFormData();
				var newValue = params.newValue;
				params.newValue = jQuery.trim(newValue);
				AppConnector.request(params).then(function (data) {
					if (data.success) {
						data = data.result;
						var newValue = jQuery.trim(jQuery('[name="newValue"]', container).val());
						var dragImagePath = jQuery('#dragImagePath').val();
						var newElement = '<tr class="pickListValue cursorPointer"><td class="textOverflowEllipsis"><img class="alignMiddle" src="' + dragImagePath + '" />&nbsp;&nbsp;' + newValue + '</td></tr>';
						var newPickListValueRow = jQuery(newElement).appendTo(jQuery('#pickListValuesTable').find('tbody'));
						newPickListValueRow.attr('data-key', newValue);
						newPickListValueRow.attr('data-key-id', data['id']);
						app.hideModalWindow();
						var params = {
							title: app.vtranslate('JS_MESSAGE'),
							text: app.vtranslate('JS_ITEM_ADDED_SUCCESSFULLY'),
							animation: 'show',
							type: 'success'
						};
						Vtiger_Helper_Js.showPnotify(params);
						//update the new item in the hidden picklist values array
						var pickListValuesEle = jQuery('[name="pickListValues"]');
						var pickListValuesArray = JSON.parse(pickListValuesEle.val());
						pickListValuesArray[data['id']] = newValue;
						pickListValuesEle.val(JSON.stringify(pickListValuesArray));
					} else {
						//there was error
						app.hideModalWindow();
						var params = {
							title: app.vtranslate('JS_MESSAGE'),
							text: app.vtranslate('JS_NOT_ALLOWED'),
							animation: 'show',
							type: 'fail'
						};
						Vtiger_Helper_Js.showPnotify(params);
					}

				});
			}
			e.preventDefault();
		});
	},

	registerRenameItemSaveEvent: function () {
		jQuery('#renameItemForm').on('submit', function (e) {
			var form = jQuery(e.currentTarget);
			var validationResult = form.validationEngine('validate');
			if (validationResult == true) {
				var duplicateCheckResult = PicklistCustomizer_Picklist_Js.duplicateItemNameCheck(form);
				var newValueEle = jQuery('[name="newValue"]', form);
				var newValue = jQuery.trim(newValueEle.val());
				if (duplicateCheckResult == true) {
					var errorMessage = app.vtranslate('JS_DUPLIACATE_ENTRIES_FOUND_FOR_THE_VALUE');
					newValueEle.validationEngine('showPrompt', errorMessage + ' ' + '"' + newValue + '"', 'error', 'bottomLeft', true);
					e.preventDefault();
					return;
				}
				var oldElem = jQuery('[name="oldValue"]', form);
				var oldValue = oldElem.val();
				var id = oldElem.data('id');
				var params = jQuery(e.currentTarget).serializeFormData();
				params.newValue = newValue;
				params.id = id;
				params.agentid = jQuery('#agentmanager_id').val();
				params.fieldid = jQuery('#field_select').val();
				var invalidFields = form.data('jqv').InvalidFields;
				if (invalidFields.length == 0) {
					form.find('[name="saveButton"]').attr('disabled', "disabled");
				}
				AppConnector.request(params).then(function (data) {
					if (typeof data.result != 'undefined') {
						app.hideModalWindow();
						var encodedOldValue = oldValue.replace(/"/g, '\\"');
						var dragImagePath = jQuery('#dragImagePath').val();
						var renamedElement = '<tr class="pickListValue cursorPointer"><td class="textOverflowEllipsis"><img class="alignMiddle" src="' + dragImagePath + '" />&nbsp;&nbsp;' + newValue + '</td></tr>';
						var renamedElement = jQuery(renamedElement).attr('data-key', newValue).attr('data-key-id', id);
						jQuery('[data-key="' + encodedOldValue + '"]').replaceWith(renamedElement);
						var params = {
							title: app.vtranslate('JS_MESSAGE'),
							text: app.vtranslate('JS_ITEM_RENAMED_SUCCESSFULLY'),
							animation: 'show',
							type: 'success'
						};
						Vtiger_Helper_Js.showPnotify(params);

						//update the new item in the hidden picklist values array
						var pickListValuesEle = jQuery('[name="pickListValues"]');
						var pickListValuesArray = JSON.parse(pickListValuesEle.val());
						pickListValuesArray[id] = newValueEle.val();
						pickListValuesEle.val(JSON.stringify(pickListValuesArray));
					}
				});
			}
			e.preventDefault();
		});
	},

	showDeleteItemForm: function (params) {
		var thisInstance = this;
		AppConnector.request(params).then(function (data) {
			app.showModalWindow(data, function (data) {
				if (typeof callBackFunction == 'function') {
					callBackFunction(data);
				}
			});
		});

		var callBackFunction = function (data) {
			var form = data.find('#deleteItemForm');
			var params = app.getvalidationEngineOptions(true);
			data.find('[name="saveButton"]').focus();
			params.onValidationComplete = function (form, valid) {
				if (valid) {

					var deleteValues = [];
					deleteValues.push(jQuery('[name="delete_value"]').val());
					var params = form.serializeFormData();
					AppConnector.request(params).then(function (data) {
						if (typeof data.result != 'undefined') {
							app.hideModalWindow();
							//delete the item in the hidden picklist values array
							var pickListValuesEle = jQuery('[name="pickListValues"]');
							var pickListValuesArray = JSON.parse(pickListValuesEle.val());
							jQuery.each(deleteValues, function (i, e) {
								var encodedOldValue = e.replace(/"/g, '\\"');
								jQuery('[data-key-id="' + encodedOldValue + '"]').remove();
								delete pickListValuesArray[e];
							});
							pickListValuesEle.val(JSON.stringify(pickListValuesArray));
							var params = {
								title: app.vtranslate('JS_MESSAGE'),
								text: app.vtranslate('JS_ITEMS_DELETED_SUCCESSFULLY'),
								animation: 'show',
								type: 'success'
							};
							Vtiger_Helper_Js.showPnotify(params);
						}
					});
				}
				return false;
			};
			form.validationEngine(params);
			//data.css('overflow','hidden');
		}
	},

	registerSelectPickListValueEvent: function () {
		jQuery("#pickListValuesTable").on('click', '.pickListValue', function (event) {
			var currentRow = jQuery(event.currentTarget);
			var currentRowTd = currentRow.find('td');
			event.preventDefault();

			// if (event.ctrlKey) {
			// 	currentRowTd.toggleClass('selectedListItem');
			// } else {
				jQuery(".pickListValue").find('td').not(currentRowTd).removeClass("selectedListItem");
				currentRowTd.toggleClass('selectedListItem');
			// }
		});
	},

	registerPickListValuesSortableEvent: function () {
		var tbody = jQuery("tbody", jQuery('#pickListValuesTable'));
		tbody.sortable({
			'helper': function (e, ui) {
				//while dragging helper elements td element will take width as contents width
				//so we are explicity saying that it has to be same width so that element will not
				//look like distrubed
				ui.children().each(function (index, element) {
					element = jQuery(element);
					element.width(element.width());
				});
				return ui;
			},
			'containment': tbody,
			'revert': true,
			update: function (e, ui) {
				jQuery('#saveSequence').removeAttr('disabled');
			}
		});
	},

	registerSaveSequenceClickEvent: function () {
		jQuery('#saveSequence').on('click', function (e) {
			var progressIndicatorElement = jQuery.progressIndicator({
				'position': 'html',
				'blockInfo': {
					'enabled': true,
					'elementToBlock': jQuery('.tab-content')
				}
			});
			var pickListValuesSequenceArray = {};
			var pickListValues = jQuery('#pickListValuesTable').find('.pickListValue');
			jQuery.each(pickListValues, function (i, element) {
				pickListValuesSequenceArray[jQuery(element).data('key-id')] = ++i;
			});
			var params = {
				module: 'Picklist',
				parent: 'Settings',
				action: 'SaveAjax',
				mode: 'saveOrder',
				picklistValues: pickListValuesSequenceArray,
				picklistName: jQuery('[name="picklistName"]').val()
			};
			AppConnector.request(params).then(function (data) {
				if (typeof data.result != 'undefined') {
					jQuery('#saveSequence').attr('disabled', 'disabled');
					progressIndicatorElement.progressIndicator({ mode: 'hide' });
					Settings_Vtiger_Index_Js.showMessage({ text: app.vtranslate('JS_SEQUENCE_UPDATED_SUCCESSFULLY'), type: 'success' })
				}
			});
		});
	},

	//@TODO: In Use?
	registerAssingValueToRoleTabClickEvent: function () {
		jQuery('#assignedToRoleTab').on('click', function (e) {
			jQuery('#rolesList').trigger('change');
		});
	},
	registerDependencyPicklistEvent: function () {
            var thisInstance = this;
            jQuery('#target_field_select').trigger('liszt:updated');
            jQuery('#target_field_select').on('change', function (e) {
                    if (jQuery("#agentmanager_id option:selected").val() == 'default') {
                            var params = {
                                    title: app.vtranslate('JS_ERROR'),
                                    text: app.vtranslate('Owner must be selected first.'),
                                    animation: 'show',
                                    type: 'error'
                            };
                            Vtiger_Helper_Js.showPnotify(params);
                            jQuery('#target_field_select option[value="default"]').prop('selected', true).trigger('liszt:updated');
                            return false;
                    }
                    if (jQuery('#target_field_select option:selected').val() == 'default') {
                            return;
                    }
                    var module = jQuery('#module_select option:selected').val();
                    var sourceField = jQuery('#field_select option:selected').data('fieldname');
                    var targetField = jQuery('#target_field_select option:selected').data('fieldname');
                    if(sourceField == targetField){
                        var params = {
                                    title: app.vtranslate('JS_ERROR'),
                                    text: app.vtranslate('JS_SOURCE_AND_TARGET_FIELDS_SHOULD_NOT_BE_SAME'),
                                    animation: 'show',
                                    type: 'error'
                            };
                            Vtiger_Helper_Js.showPnotify(params);
                            return;
                    }
                    var dependencyGraph = jQuery('#dependencyGraph');

                   	thisInstance.addNewDependencyPickList(module,sourceField,targetField);

            });
	},



        //functions for Picklist Dependency tab
        /**
	 * Function used to check the cyclic dependency of the selected picklist fields
	 * @params: sourceModule - selected module
	 *			sourceFieldValue - source picklist value
	 *			targetFieldValue - target picklist value
	 */
	checkCyclicDependency : function(sourceModule, sourceFieldValue, targetFieldValue) {
		var aDeferred = jQuery.Deferred();
		var params = {};
		params['mode'] = 'checkCyclicDependency';
		params['module'] = app.getModuleName();
		params['action'] = 'ActionAjax';
		params['sourceModule'] = sourceModule;
		params['sourcefield'] = sourceFieldValue;
		params['targetfield'] = targetFieldValue;

		AppConnector.request(params).then(
			function(data) {
				aDeferred.resolve(data);
			}, function(error, err) {
				aDeferred.reject();
			}
		);
		return aDeferred.promise();
	},

        /**
	 * Function used to show the new picklist dependency graph
	 * @params: sourceModule - selected module
	 *			sourceFieldValue - source picklist value
	 *			targetFieldValue - target picklist value
	 */
	addNewDependencyPickList : function(sourceModule, sourceFieldValue, targetFieldValue) {
		var progressIndicatorElement = jQuery.progressIndicator({
			'position': 'html',
			'blockInfo': {
				'enabled': true
			}
		});

		var thisInstance = this;
		thisInstance.updatedSourceValues = [];
		var params = {};
		params['mode'] = 'getDependencyGraph';
		params['module'] = app.getModuleName();
		params['view'] = 'IndexAjax';
		params['sourceModule'] = sourceModule;
		params['sourcefield'] = sourceFieldValue;
		params['targetfield'] = targetFieldValue;
		params['agentmanagerid'] = jQuery("#agentmanager_id option:selected").val();

		AppConnector.request(params).then(
			function(data) {
				var dependencyGraph = jQuery('#dependencyGraph');
				dependencyGraph.html(data).css(	{'padding': '10px','border': '1px solid #ddd','background': '#fff'});

				thisInstance.registerDependencyGraphEvents();
				 progressIndicatorElement.progressIndicator({'mode' : 'hide'});
			}, function (error, err) {
				var params = {
					title: app.vtranslate('JS_ERROR'),
					text: app.vtranslate('Error Loading Fields Information'),
					animation: 'show',
					type: 'error'
				};
				Vtiger_Helper_Js.showPnotify(params);
				progressIndicatorElement.progressIndicator({ 'mode': 'hide' });

			}
		);
	},
	/**
	 * Function used to update the value mapping to save the picklist dependency
	 */
	updateValueMapping : function(dependencyGraph) {
		var thisInstance = this;
		thisInstance.valueMapping = [];
		var sourceValuesArray = thisInstance.updatedSourceValues;
		var dependencyTable = dependencyGraph.find('.pickListDependencyTable');
		for(var key in sourceValuesArray) {
			if(typeof sourceValuesArray[key] == 'string'){
				var encodedSourceValue = sourceValuesArray[key].replace(/"/g, '\\"');
			} else {
				encodedSourceValue = sourceValuesArray[key];
			}
			var selectedTargetValues = dependencyTable.find('td[data-source-value="'+encodedSourceValue+'"]').filter('.selectedCell');
			var targetValues = [];
			if(selectedTargetValues.length > 0) {
				jQuery.each(selectedTargetValues, function(index, element) {
					targetValues.push(jQuery(element).data('targetValue'));
				});
			} else {
				targetValues.push('');
			}
			thisInstance.valueMapping.push({'sourcevalue' : sourceValuesArray[key], 'targetvalues' : targetValues});
		}
	},
		/**
	 * This function will save the picklist dependency details
	 */
	savePickListDependency : function(form) {
		var thisInstance = this;
		var progressIndicatorElement = jQuery.progressIndicator({
			'position' : 'html',
			'blockInfo' : {
				'enabled' : true
			}
		});
		var data = form.serializeFormData();
		data['module'] = app.getModuleName();
		data['parent'] = app.getParentModuleName();
		data['action'] = 'ActionAjax';
		data['mode'] = 'savePicklistDependency';
		data['agentid'] = jQuery('#agentmanager_id').val();
		data['mapping'] = JSON.stringify(thisInstance.valueMapping);
		AppConnector.request(data).then(
			function(data) {
				if(data['success']) {
					progressIndicatorElement.progressIndicator({'mode' : 'hide'});
					var params = {};
					params.text = app.vtranslate('JS_PICKLIST_DEPENDENCY_SAVED');
					Settings_Vtiger_Index_Js.showMessage(params);
//					thisInstance.loadListViewContents(thisInstance.listViewForModule);
				}
			},
			function(error) {
				progressIndicatorElement.progressIndicator({'mode' : 'hide'});
			}
		);
	},
	registerSubmitEvent : function() {
		var thisInstance = this;
		var form = jQuery('#pickListDependencyForm');
		var dependencyGraph = jQuery('#dependencyGraph');
		form.submit(function(e) {
			e.preventDefault();
			try{
				thisInstance.updateValueMapping(dependencyGraph);
			}catch(e) {
				bootbox.alert(e.message);
				return;
			}
			thisInstance.savePickListDependency(form);
		});
	},

        /**
	 * Register all the events in editView of picklist dependency
	 */
	registerDependencyGraphEvents : function() {
		var thisInstance = this;
//		var form = jQuery('#pickListDependencyForm');
		var dependencyGraph = jQuery('#dependencyGraph');
//		form.find('.cancelAddView').addClass('hide');
		thisInstance.registerTargetFieldsClickEvent(dependencyGraph);
		thisInstance.registerSelectSourceValuesClick(dependencyGraph);
		thisInstance.registerSubmitEvent();
		thisInstance.registerCancelButton();
//		thisInstance.registerCancelDependency(form);
	},

        /**
	 * Register the click event for target fields in dependency graph
	 */
	registerTargetFieldsClickEvent : function(dependencyGraph) {
		var thisInstance = this;
		thisInstance.updatedSourceValues = [];
		dependencyGraph.find('td.picklistValueMapping').on('click', function(e) {
			var currentTarget = jQuery(e.currentTarget);
			var sourceValue = currentTarget.data('sourceValue');
			if(jQuery.inArray(sourceValue, thisInstance.updatedSourceValues) == -1) {
				thisInstance.updatedSourceValues.push(sourceValue);
			}
			if(currentTarget.hasClass('selectedCell')) {
				currentTarget.addClass('unselectedCell').removeClass('selectedCell').find('i.icon-ok').remove();
			} else {
				currentTarget.addClass('selectedCell').removeClass('unselectedCell').prepend('<i class="icon-ok pull-left"></i>');
			}
		});
	},

        /**
	 * register click event for select source values button in add/edit view
	 */
	registerSelectSourceValuesClick : function(dependencyGraph) {
		var thisInstance = this;
		dependencyGraph.find('button.sourceValues').click(function() {
			var selectSourceValues = dependencyGraph.find('.modalCloneCopy');
			var clonedContainer = selectSourceValues.clone(true, true).removeClass('modalCloneCopy');
			var callBackFunction = function(data) {
				data.find('.sourcePicklistValuesModal').removeClass('hide');
				data.find('[name="saveButton"]').click(function(e) {
					thisInstance.selectedSourceValues = [];
					var sourceValues = data.find('.sourceValue');
					jQuery.each(sourceValues, function(index, ele) {
						var element = jQuery(ele);
						var value = element.val();
						if(typeof value == 'string'){
							var encodedValue = value.replace(/"/g, '\\"');
						} else {
							encodedValue = value;
						}
						var hiddenElement = selectSourceValues.find('[class*="'+encodedValue+'"]');
						if(element.is(':checked')) {
							thisInstance.selectedSourceValues.push(value);
							hiddenElement.attr('checked', true);
						} else {
							hiddenElement.removeAttr('checked');
						}
					})
					app.hideModalWindow();
					thisInstance.loadMappingForSelectedValues(dependencyGraph);
				});
			}

			app.showModalWindow(clonedContainer,function(data) {
				if(typeof callBackFunction == 'function') {
					callBackFunction(data);
				}
			}, {'width':'1000px'});
		})
	},

        /**
	 * Function used to load mapping for selected picklist fields
	 */
	loadMappingForSelectedValues : function(dependencyGraph) {
		var thisInstance = this;
		var allSourcePickListValues = jQuery.parseJSON(dependencyGraph.find('.allSourceValues').val());
		var dependencyTable = dependencyGraph.find('.pickListDependencyTable');
		for(var key in allSourcePickListValues) {
			if(typeof allSourcePickListValues[key] == 'string'){
				var encodedSourcePickListValue = allSourcePickListValues[key].replace(/"/g, '\\"');
			} else {
				encodedSourcePickListValue = allSourcePickListValues[key];
			}
			var mappingCells = dependencyTable.find('[data-source-value="'+encodedSourcePickListValue+'"]');
			if(jQuery.inArray(allSourcePickListValues[key], thisInstance.selectedSourceValues) == -1) {
				mappingCells.hide();
			} else {
				mappingCells.show();
			}
		}
		dependencyGraph.find('.dependencyMapping').mCustomScrollbar("update");
	},

	registerItemActions: function () {
		PicklistCustomizer_Picklist_Js.registerAddItemEvent();
		PicklistCustomizer_Picklist_Js.registerRenameItemEvent();
		PicklistCustomizer_Picklist_Js.registerDeleteItemEvent();
		PicklistCustomizer_Picklist_Js.registerSelectPickListValueEvent();
		PicklistCustomizer_Picklist_Js.registerAssingValueToRuleEvent();
		PicklistCustomizer_Picklist_Js.registerChangeRoleEvent();
		PicklistCustomizer_Picklist_Js.registerAssingValueToRoleTabClickEvent();
		PicklistCustomizer_Picklist_Js.registerPickListValuesSortableEvent();
		PicklistCustomizer_Picklist_Js.registerSaveSequenceClickEvent();
		PicklistCustomizer_Picklist_Js.registerDependencyPicklistEvent();
	},
	hideLeftPanel: function(){
		var leftPanel = jQuery('#leftPanel');
			var rightPanel = jQuery('#rightPanel');
			var tButtonImage = jQuery('#toggleButton');
			if (leftPanel.attr('class').indexOf(' hide') == -1) {
                var leftPanelshow = 1;
				leftPanel.addClass('hide');
				rightPanel.removeClass('span10').addClass('span12');
				tButtonImage.hide();
			}
	},

	registerCancelButton: function(){
		var dependencyGraph = jQuery('#dependencyGraph');
		dependencyGraph.find('.cancelDependency').on('click', function(e) {
			jQuery('#target_field_select').trigger('change');
			jQuery('.nav-tabs a[href="#allValuesLayout"]').tab('show');
		});
	},
	registerEvents: function () {
            var thisInstance = this;
            //holds the listview forModule
            thisInstance.listViewForModule = '';

            //holds the updated sourceValues while editing dependency
            thisInstance.updatedSourceValues = [];

            //holds the new mapping of source values and target values
            thisInstance.valueMapping = [];

            //holds the list of selected source values for dependency
            thisInstance.selectedSourceValues = [];

		PicklistCustomizer_Picklist_Js.registerModulePickListChangeEvent();
		PicklistCustomizer_Picklist_Js.registerItemActions();
		PicklistCustomizer_Picklist_Js.registerEnablePickListValueClickEvent();
		thisInstance.hideLeftPanel();
	}
};

jQuery(document).ready(function () {
	PicklistCustomizer_Picklist_Js.registerEvents();
        window.onbeforeunload = function() {
            if( ! jQuery('#saveSequence').prop('disabled') ){
                return true;
            }
        };
});

Vtiger_Base_Validator_Js("Vtiger_FieldLabel_Validator_Js", {

	/**
	 *Function which invokes field validation
	 *@param accepts field element as parameter
	 * @return error if validation fails true on success
	 */
	invokeValidation: function (field, rules, i, options) {
		var instance = new Vtiger_FieldLabel_Validator_Js();
		instance.setElement(field);
		var response = instance.validate();
		if (response != true) {
			return instance.getError();
		}
	}

}, {
		/**
		 * Function to validate the field label
		 * @return true if validation is successfull
		 * @return false if validation error occurs
		 */
		validate: function () {
			var fieldValue = this.getFieldValue();
			return this.validateValue(fieldValue);
		},

		validateValue: function (fieldValue) {
			var specialChars = /[<\>\"\,]/;

			if (specialChars.test(fieldValue)) {
				var errorInfo = app.vtranslate('JS_SPECIAL_CHARACTERS') + " < > \" , " + app.vtranslate('JS_NOT_ALLOWED');
				this.setError(errorInfo);
				return false;
			}
			return true;
		}
	});

