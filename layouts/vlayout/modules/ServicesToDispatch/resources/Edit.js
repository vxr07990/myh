Vtiger_Edit_Js("ServicesToDispatch_Edit_Js",{},{
	

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
		if(sourceField == 'stdispatch_stop') {
			search_key = 'extrastops_relcrmid';
			search_value = jQuery('input.sourceField[name="stdispatch_orders"]').data('displayvalue');
		}
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
			'search_value' : search_value
		}

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
				params.parent_id = jQuery('input[name="stdispatch_orders"').val();
				params.parent_module = "Orders";
				thisInstance.searchModuleNames(params).then(function(data){
					var reponseDataList = new Array();
					var serverDataFormat = data.result
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
	});
	