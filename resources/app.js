/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

var app = {

	/**
	 * variable stores client side language strings
	 */
	languageString : [],


	weekDaysArray : {Sunday : 0,Monday : 1, Tuesday : 2, Wednesday : 3,Thursday : 4, Friday : 5, Saturday : 6},

	/**
	 * Function to get the module name. This function will get the value from element which has id module
	 * @return : string - module name
	 */
	getModuleName : function() {
		return jQuery('#module').val();
	},

	/**
	 * Function to get the module name. This function will get the value from element which has id module
	 * @return : string - module name
	 */
	getParentModuleName : function() {
		return jQuery('#parent').val();
	},

	/**
	 * Function returns the current view name
	 */
	getViewName : function() {
		return jQuery('#view').val();
	},

        /**
         * Function returns the record id
         */
        getRecordId : function(){
            var view = jQuery('[name="view"]').val();
            var recordId;
            if(view == "Edit"){
                recordId = jQuery('[name="record"]').val();
            }else if(view == "Detail"){
                recordId = jQuery('#recordId').val();
            }
            return recordId;
        },

	/**
	 * Function to get the contents container
	 * @returns jQuery object
	 */
	getContentsContainer : function() {
		return jQuery('.bodyContents');
	},

	/**
	 * Function which will convert ui of select boxes.
	 * @params parent - select element
	 * @params view - select2
	 * @params viewParams - select2 params
	 * @returns jquery object list which represents changed select elements
	 */
	changeSelectElementView : function(parent, view, viewParams){

		var selectElement = jQuery();
		if(typeof parent == 'undefined') {
			parent = jQuery('body');
		}

		//If view is select2, This will convert the ui of select boxes to select2 elements.
		if(view == 'select2') {
			app.showSelect2ElementView(parent, viewParams);
			return;
		}
		selectElement = jQuery('.chzn-select', parent);
		//parent itself is the element
		if(parent.is('select.chzn-select')) {
			selectElement = parent;
		}

		//fix for multiselect error prompt hide when validation is success
		selectElement.filter('[multiple]').filter('[data-validation-engine*="validate"]').on('change',function(e){
			jQuery(e.currentTarget).trigger('focusout');
		});

		var chosenElement = selectElement.chosen();
		var chosenSelectConainer = jQuery('.chzn-container');
		//Fix for z-index issue in IE 7
		if (jQuery.browser.msie && jQuery.browser.version === "7.0") {
			var zidx = 1000;
			chosenSelectConainer.each(function(){
				$(this).css('z-index', zidx);
				zidx-=10;
			});
		}
		return chosenSelectConainer;
	},

	/**
	 * Function to destroy the chosen element and get back the basic select Element
	 */
	destroyChosenElement : function(parent) {
		var selectElement = jQuery();
		if(typeof parent == 'undefined') {
			parent = jQuery('body');
		}

		selectElement = jQuery('.chzn-select', parent);
		//parent itself is the element
		if(parent.is('select.chzn-select')) {
			selectElement = parent;
		}

		selectElement.css('display','block').removeClass("chzn-done").data("chosen", null).next().remove();

		return selectElement;

	},
	/**
	 * Function which will show the select2 element for select boxes . This will use select2 library
	 */
	showSelect2ElementView : function(selectElement, params) {
		if(typeof params == 'undefined') {
			params = {};
		}

		var data = selectElement.data();
		if(data != null) {
			params = jQuery.extend(data,params);
		}

		// Sort DOM nodes alphabetically in select box.
		if (typeof params['customSortOptGroup'] != 'undefined' && params['customSortOptGroup']) {
			jQuery('optgroup', selectElement).each(function(){
				var optgroup = jQuery(this);
				var options  = optgroup.children().toArray().sort(function(a, b){
					var aText = jQuery(a).text();
					var bText = jQuery(b).text();
					return aText < bText ? 1 : -1;
				});
				jQuery.each(options, function(i, v){
					optgroup.prepend(v);
				});
			});
			delete params['customSortOptGroup'];
		}

		//formatSelectionTooBig param is not defined even it has the maximumSelectionSize,
		//then we should send our custom function for formatSelectionTooBig
		if(typeof params.maximumSelectionSize != "undefined" && typeof params.formatSelectionTooBig == "undefined") {
			var limit = params.maximumSelectionSize;
			//custom function which will return the maximum selection size exceeds message.
			var formatSelectionExceeds = function(limit) {
					return app.vtranslate('JS_YOU_CAN_SELECT_ONLY')+' '+limit+' '+app.vtranslate('JS_ITEMS');
			};
			params.formatSelectionTooBig = formatSelectionExceeds;
		}
		if(selectElement.attr('multiple') != 'undefined' && typeof params.closeOnSelect == 'undefined') {
			params.closeOnSelect = false;
		}
		selectElement.select2(params)
					 .on("open", function(e) {
						 var element = jQuery(e.currentTarget);
						 var instance = element.data('select2');
						 instance.dropdown.css('z-index',1000002);
					 });
		if(typeof params.maximumSelectionSize != "undefined") {
			app.registerChangeEventForMultiSelect(selectElement,params);
		}
		return selectElement;
	},
	showReferenceMultiSelectView : function(selectElement, params) {
		selectElement.each(function () {
			var ele = jQuery(this);
			var fieldInfo = ele.data('fieldinfo');
            if(typeof fieldInfo === 'undefined'){
                fieldInfo = {};
            }
			if(typeof fieldInfo != 'object' && fieldInfo){
				fieldInfo = JSON.parse(fieldInfo);
			}
			var isMulti = true;
			if(fieldInfo.type =='personnelpicklist'){
				isMulti = false;
			}
			ele.select2({
				multiple: isMulti,
				tokenSeparators: [","],
				ajax : {
					'url' : 'index.php?module='+app.getModuleName()+'&action=GetReferencePicklist',
					'dataType' : 'json',
					'quietMillis': 250,
					'data' : function(term,page){
						var data = {};
						data['search_value'] = term;
						data['related_module'] = fieldInfo['reference_module'];
						data['fieldname'] = fieldInfo['name'];

						if(fieldInfo['type'] =='personnelpicklist'){
							var participating_agent = jQuery('select[name="participating_agent"]').val();
							if(participating_agent != undefined && participating_agent !=''){
								data['participating_agent'] = participating_agent;
							}
						}

                        var customFilter=jQuery.parseJSON(ele.data('custom-filter'));
                        if(customFilter != null) {
                            jQuery.each(customFilter,function (k,v) {
                                data[k] = v;
                            });
                        }

						return data;
					},
					'results' : function(data){
						var finalResult = [];
						var results = data.result;
						if(fieldInfo.type == 'referencemultipicklistall'){
							var resultData = new Array({
								id : 'all',
								text : 'All'
							});
						}else if(fieldInfo.type =='personnelpicklist'){
							var resultData = new Array({
								id : -1,
								text : 'Any Personnel Type'
							});
						}else{
							var resultData = [];
						}
						for(var index in results) {
							resultData.push({
								id : index,
								text : results[index]
							});
						}
						finalResult.results = resultData;
						return finalResult;
					},
					transport : function(params) {
						return jQuery.ajax(params);
					},
					cache: true
				},
				initSelection : function (element, callback) {
					var fieldinfo = jQuery(element).data('fieldinfo');
					var data = fieldinfo.picklistvalues;
					var fieldValue = jQuery(element).val();
					if(fieldValue !='' && fieldValue != undefined){
						if(isMulti == true){
							var selectedValues = [];
							var selectedValuesId = fieldValue.split(',');
							if (data && data!=undefined) {
								jQuery.each(data, function (index,value) {
									if(jQuery.inArray(index,selectedValuesId) != -1){
										selectedValues.push({
											id:index,
											text:value
										});
									}
								});
							}
							if(jQuery.inArray('all',selectedValuesId) != -1){
								selectedValues.push({
									id:'all',
									text:'All'
								});
							}
						}else{
							var selectedValues = {};
							if(fieldValue == -1){
								selectedValues = {
									id:'-1',
									text:'Any Personnel Type'
								};
							}else{
								jQuery.each(data, function (index,value) {
									if(fieldValue == index){
										selectedValues={
											id:index,
											text:value
										};
										return false;
									}
								});
							}
						}
						callback(selectedValues);
					}
				},
			});
			ele.on('change',function () {
				var selectedValues = jQuery(this).val().split(',');
				if(selectedValues != undefined && selectedValues.length > 1 && selectedValues.indexOf('all') >=0){
					var data = [];
					if(selectedValues.indexOf('all') == selectedValues.length -1){
						data.push({
							id:'all',
							text:'All'
						});
					}else{
						var divElement = jQuery(this).prev();
						divElement.find('.select2-search-choice').each(function () {
							var select2Data = jQuery(this).data('select2Data');
							if(select2Data.id != 'all'){
								data.push(select2Data);
							}
						});
					}
					ele.select2('data', data);
					ele.trigger('change');
				}
			});
		});
	},

	/**
	 * Function to check the maximum selection size of multiselect and update the results
	 * @params <object> multiSelectElement
	 * @params <object> select2 params
	 */

	registerChangeEventForMultiSelect :  function(selectElement,params) {
		if(typeof selectElement == 'undefined') {
			return;
		}
		var instance = selectElement.data('select2');
		var limit = params.maximumSelectionSize;
		selectElement.on('change',function(e){
			var data = instance.data();
			if (jQuery.isArray(data) && data.length >= limit ) {
				instance.updateResults();
            }
		});

	},

	/**
	 * Function to get data of the child elements in serialized format
	 * @params <object> parentElement - element in which the data should be serialized. Can be selector , domelement or jquery object
	 * @params <String> returnFormat - optional which will indicate which format return value should be valid values "object" and "string"
	 * @return <object> - encoded string or value map
	 */
	getSerializedData : function(parentElement, returnFormat){
		if(typeof returnFormat == 'undefined') {
			returnFormat = 'string';
		}

		parentElement = jQuery(parentElement);

		var encodedString = parentElement.children().serialize();
		if(returnFormat == 'string'){
			return encodedString;
		}
		var keyValueMap = {};
		var valueList = encodedString.split('&');

		for(var index in valueList){
			var keyValueString = valueList[index];
			var keyValueArr = keyValueString.split('=');
			var nameOfElement = keyValueArr[0];
			var valueOfElement =  keyValueArr[1];
			keyValueMap[nameOfElement] = decodeURIComponent(valueOfElement);
		}
		return keyValueMap;
	},

	//Returns a non-existing blockUI id
	getNewWindowId: function() {
		var counter = 0;
		if(jQuery('#popupWindowCounter').length == 0) {
			var counterEl = jQuery('<input/>',{type:'hidden',id:'popupWindowCounter',value:0});
			counterEl.appendTo('body');
		}
		else {
			var counter = parseInt(jQuery('#popupWindowCounter').val()) + 1;
			jQuery('#popupWindowCounter').val(counter);
		}
		return 'globalmodal' + counter;
	},
	showModalWindow: function(data, url, cb, css) {
		/*
		console.dir("At the start");
		console.dir('data');
		console.dir(data);
		console.dir('url');
		console.dir(url);
		console.dir('cb');
		console.dir(cb);
		console.dir('css');
		console.dir(css);
		*/
		var unBlockCb = function(){};
		var overlayCss = {};

		//null is also an object
		if(typeof data == 'object' && data != null && !(data instanceof jQuery)){
			css = data.css;
			cb = data.cb;
			url = data.url;
			unBlockCb = data.unblockcb;
			overlayCss = data.overlayCss;
			data = data.data

		}
		if (typeof url == 'function') {
			if(typeof cb == 'object') {
				css = cb;
			}
			cb = url;
			url = false;
		}
		else if (typeof url == 'object') {
			cb = function() { };
			css = url;
			url = false;
		}

		if (typeof cb != 'function') {
			cb = function() { }
		}

		//var id = 'globalmodal';

		var id = this.getNewWindowId();


		var container = jQuery('#'+id);
		if (container.length) {
			container.remove();
		}
		container = jQuery('<div></div>');
		container.attr('id', id);
        // Doing this to hide the scrollbars that do nothing.
        // Response: WELLLLL except allow you to scroll IFF you need to.
        //container.css({'overflow': 'hidden'});
        //@NOTE: Some search tags: globalmodal style css overflow height
        container.css({'overflow': 'auto'});

		var showModalData = function (data) {
			var defaultCss = {
							'top' : '0px',
							'width' : 'auto',
							'cursor' : 'default',
							'left' : '35px',
							'text-align' : 'left',
							'border-radius':'6px',
							//'overflow-y' : 'auto', // this is not relevant because the container css above was overriding it.
							};
			var effectiveCss = defaultCss;
			if(typeof css == 'object') {
				effectiveCss = jQuery.extend(defaultCss, css)
			}

			var defaultOverlayCss = {
										'cursor' : 'default',
									};
			var effectiveOverlayCss = defaultOverlayCss;
			if(typeof overlayCss == 'object' ) {
				effectiveOverlayCss = jQuery.extend(defaultOverlayCss,overlayCss);
			}
			container.html(data);

			// Mimic bootstrap modal action body state change
			jQuery('body').addClass('modal-open');

			$("<div id='" + id + "Container' style='position:fixed;width:100%;height:100%;z-index:2000;top:0;' />").appendTo("body").block({
					'message' : container,
					'overlayCSS' : effectiveOverlayCss,
					'css' : effectiveCss,

					// disable if you want key and mouse events to be enable for content that is blocked (fix for select2 search box)
					bindEvents: false,

					//Fix for overlay opacity issue in FF/Linux
					applyPlatformOpacityRules : false
				});
			var unblockUi = function() {
				app.hideModalWindow(unBlockCb, id);
				jQuery(document).unbind("keyup",escapeKeyHandler);
			};
			var escapeKeyHandler = function(e){
				if (e.keyCode == 27) {
						unblockUi();
				}
			};
			jQuery('.blockOverlay').click(unblockUi);
			jQuery(document).on('keyup',escapeKeyHandler);
			jQuery('[data-dismiss="modal"]', container).click(unblockUi);
			/*jQuery('form', container).submit(function() {
				unblockUi();
			});*/
			jQuery( document ).ajaxComplete(function() {
				// jQuery('[data-dismiss="modal"]', container).click(unblockUi);
				var rows = container.find('[data-dismiss="modal"]');
				var row = null;
				var cells = null;
				var cell = null;

				for (var r = 0; r < rows.length; r++) {
					row = $(rows[r]);
					cells = row.find('td');

					for (var c = 0; c < cells.length; c++) {
						cell = $(cells[c]);
						if (cell.find('input.entryCheckBox').length == 0) {
							cell.click(unblockUi)
						}
					}
				}

				// select button
				container.find('button.select').click(unblockUi);
			});

			container.closest('.blockMsg').position({
				'of' : jQuery(window),
				'my' : 'center top',
				'at' : 'center top',
				'collision' : 'flip none',
				//TODO : By default the position of the container is taking as -ve so we are giving offset
				// Check why it is happening
				'offset' : '0 50'
			});
			container.css({'max-height' : (jQuery(window).innerHeight()-100)+'px'});

			// TODO Make it better with jQuery.on
			app.changeSelectElementView(container);
            //register all select2 Elements
            app.showSelect2ElementView(container.find('select.select2'));
            app.showReferenceMultiSelectView(container.find('input.select2'));
			//register date fields event to show mini calendar on click of element
			app.registerEventForDatePickerFields(container);
			cb(container);
		//console.trace();

		};

		if (data) {
			showModalData(data);
		} else {
			jQuery.get(url).then(function(response){
				showModalData(response);
			});
		}
        // Dear future dev, I, RC, bid you hello! I know this is gross and I'm kind of sorry
        if(container.children(':first').hasClass('SendEmailFormStep2')) {
            var emailEditInstance = new Emails_MassEdit_Js();
            emailEditInstance.registerEvents();
        }
		return container;
	},

	/**
	 * Function which you can use to hide the modal
	 * This api assumes that we are using block ui plugin and uses unblock api to unblock it
	 */
	hideModalWindow : function(callback, windowId) {
		// Mimic bootstrap modal action body state change - helps to avoid body scroll
		// when modal is shown using css: http://stackoverflow.com/a/11013994
		jQuery('body').removeClass('modal-open');

		//If no specific window(lightbox) Id is passed, remove all
		if (typeof windowId === "undefined" || windowId === null) {
				jQuery("[id^=globalmodal]").unblock({
				onUnblock : function(){jQuery("[id^=globalmodal]").remove();}
			});
		}

		//If a specific window(lightbox) Id is passed, only remove that one
		else {
			var id = windowId;
			var container = jQuery('#'+id);
			if (container.length <= 0) {
				return;
			}

			if(typeof callback != 'function') {
				callback = function() {};
			}
			jQuery('#' + id + 'Container').unblock({
				onUnblock : function(){jQuery('#' + windowId + 'Container').remove();}
			});
		}
	},

	isHidden : function(element) {
		if(element.css('display')== 'none') {
			return true;
		}
		return false;
	},

	/**
	 * Default validation eninge options
	 */
	validationEngineOptions: {
		// Avoid scroll decision and let it scroll up page when form is too big
		// Reference: http://www.position-absolute.com/articles/jquery-form-validator-because-form-validation-is-a-mess/
		scroll: false,
		promptPosition: 'topLeft',
		//to support validation for chosen select box
		prettySelect : true,
		useSuffix: "_chzn",
        usePrefix : "s2id_"
	},

	/**
	 * Function to push down the error message size when validation is invoked
	 * @params : form Element
	 */

	formAlignmentAfterValidation : function(form){
		// to avoid hiding of error message under the fixed nav bar
		var destination = form.find(".formError:not('.greenPopup'):first").offset().top;
		var resizedDestnation = destination-105;
		jQuery('html').animate({
			scrollTop:resizedDestnation
		}, 'slow');
	},

	/**
	 * Function to push down the error message size when validation is invoked
	 * @params : form Element
	 */
	formAlignmentAfterValidation : function(form){
		// to avoid hiding of error message under the fixed nav bar
		if(typeof form == 'undefined' || form == null) {
			form = this.getForm();
		}
        var destinationElement = form.find(".formError:not('.greenPopup'):first");
		if (destinationElement) {
            var destinationOffset = destinationElement.offset();
            if (destinationOffset) {
                var destination = destinationOffset.top;
                var resizedDestination = destination - 105;
                jQuery('html').animate({
                    scrollTop: resizedDestination
                }, 'slow');
            }
        }
	},

	convertToDatePickerFormat: function(dateFormat){
		if(dateFormat == 'yyyy-mm-dd'){
			return 'Y-m-d';
		} else if(dateFormat == 'mm-dd-yyyy') {
			return 'm-d-Y';
		} else if (dateFormat == 'dd-mm-yyyy') {
			return 'd-m-Y';
		}
	},

	convertTojQueryDatePickerFormat: function(dateFormat){
		var i = 0;
		var splitDateFormat = dateFormat.split('-');
		for(var i in splitDateFormat){
			var sectionDate = splitDateFormat[i];
			var sectionCount = sectionDate.length;
			if(sectionCount == 4){
				var strippedString = sectionDate.substring(0,2);
				splitDateFormat[i] = strippedString;
			}
		}
		var joinedDateFormat =  splitDateFormat.join('-');
		return joinedDateFormat;
	},
	getDateInVtigerFormat: function(dateFormat,dateObject){
		var finalFormat = app.convertTojQueryDatePickerFormat(dateFormat);
		var date = jQuery.datepicker.formatDate(finalFormat,dateObject);
		return date;
	},

	registerEventForTextAreaFields : function(parentElement) {
		if(typeof parentElement == 'undefined') {
			parentElement = jQuery('body');
		}

		parentElement = jQuery(parentElement);

		if(parentElement.is('textarea')){
			var element = parentElement;
		}else{
			var element = jQuery('textarea', parentElement);
		}
		if(element.length == 0){
			return;
		}
		element.autosize();
	},

	registerEventForDatePickerFields : function(parentElement,registerForAddon,customParams){
		if(typeof parentElement == 'undefined') {
			parentElement = jQuery('body');
		}
		if(typeof registerForAddon == 'undefined'){
			registerForAddon = true;
		}

		parentElement = jQuery(parentElement);

		if(parentElement.hasClass('dateField')){
			var element = parentElement;
		}else{
			//Adding this so that we can have 'readonly' date fields.
			var element = jQuery('.dateField:not([readonly])', parentElement);
		}
		if(element.length == 0){
			return;
		}
		if(registerForAddon == true){
			var parentDateElem = element.closest('.date');
			jQuery('.add-on',parentDateElem).on('click',function(e){
				var elem = jQuery(e.currentTarget);
				//Using focus api of DOM instead of jQuery because show api of datePicker is calling e.preventDefault
				//which is stopping from getting focus to input element
				elem.closest('.date').find('input.dateField').get(0).focus();
			});
		}
		var dateFormat = element.data('dateFormat');
		var vtigerDateFormat = app.convertToDatePickerFormat(dateFormat);
		var language = jQuery('body').data('language');
		var lang = language.split('_');

		//Default first day of the week
		var defaultFirstDay = jQuery('#start_day').val();
		if(defaultFirstDay == '' || typeof(defaultFirstDay) == 'undefined'){
			var convertedFirstDay = 1
		} else {
			convertedFirstDay = this.weekDaysArray[defaultFirstDay];
		}
		var params = {
			format : vtigerDateFormat,
			calendars: 1,
			locale: $.fn.datepicker.dates[lang[0]],
			starts: convertedFirstDay,
			eventName : 'focus',
			onChange: function(formated){
                var element = jQuery(this).data('datepicker').el;
                element = jQuery(element);
				if(element.attr('readonly') == true || element.attr('readonly') == 'readonly') {
					element.DatePickerHide();
					element.blur();
					bootbox.alert('The field that you are attempting to update is read-only.');
					return false;
				}
                var datePicker = jQuery('#'+ jQuery(this).data('datepicker').id);
                var viewDaysElement = datePicker.find('table.datepickerViewDays');
                //If it is in day mode and the prev value is not eqaul to current value
                //Second condition is manily useful in places where user navigates to other month
                if(viewDaysElement.length > 0 && element.val() != formated) {
                    element.DatePickerHide();
                    element.blur();
                }
				element.val(formated).trigger('change').focusout();
				// Sigh.
				var ni = element.closest('td').nextAll('td').find('input').first();
				if(ni.length > 0)
				{
					ni.focus();
					ni.DatePickerHide();
				}
			}
		};
		if(typeof customParams != 'undefined'){
			var params = jQuery.extend(params,customParams);
		}
		element.each(function(index,domElement){
			var jQelement = jQuery(domElement);
			var dateObj = new Date();
			var selectedDate = app.getDateInVtigerFormat(dateFormat, dateObj);
			//Take the element value as current date or current date
			if(jQelement.val() != '') {
				selectedDate = jQelement.val();
			}
			params.date = selectedDate;
			params.current = selectedDate;
			jQelement.DatePicker(params);
			jQelement.on('keydown', function(ev) {
				if(ev.keyCode == 9)
				{
					jQuery(this).DatePickerHide();
				}
			});
		});

	},
	registerEventForDateFields : function(parentElement) {
		if(typeof parentElement == 'undefined') {
			parentElement = jQuery('body');
		}

		parentElement = jQuery(parentElement);

		if(parentElement.hasClass('dateField')){
			var element = parentElement;
		}else{
			var element = jQuery('.dateField', parentElement);
		}
		element.datepicker({'autoclose':true}).on('changeDate', function(ev){
			var currentElement = jQuery(ev.currentTarget);
			var dateFormat = currentElement.data('dateFormat');
			var finalFormat = app.getDateInVtigerFormat(dateFormat,ev.date);
			var date = jQuery.datepicker.formatDate(finalFormat,ev.date);
			currentElement.val(date);
		});
	},

	/**
	 * Function which will register time fields
	 *
	 * @params : container - jquery object which contains time fields with class timepicker-default or itself can be time field
	 *			 registerForAddon - boolean value to register the event for Addon or not
	 *			 params  - params for the  plugin
	 *
	 * @return : container to support chaining
	 */
	registerEventForTimeFields : function(container, registerForAddon, params) {

		if(typeof cotainer == 'undefined') {
			container = jQuery('body');
		}
		if(typeof registerForAddon == 'undefined'){
			registerForAddon = true;
		}

		container = jQuery(container);

		if(container.hasClass('timepicker-default')) {
			var element = container;
		}else{
			//Adding this so we can have 'readonly' time fields
			var element = container.find('.timepicker-default:not([readonly])');
		}

		if(registerForAddon == true){
			var parentTimeElem = element.closest('.time');
			jQuery('.add-on',parentTimeElem).on('click',function(e){
				var elem = jQuery(e.currentTarget);
				elem.closest('.time').find('.timepicker-default').focus();
			});
		}

		if(typeof params == 'undefined') {
			params = {};
		}

		var timeFormat = element.data('format');
		if(timeFormat == '24') {
			timeFormat = 'H:i';
		} else {
			timeFormat = 'h:i A';
		}
		var defaultsTimePickerParams = {
			'timeFormat' : timeFormat,
			'className'  : 'timePicker'
		};
		var params = jQuery.extend(defaultsTimePickerParams, params);
                if(jQuery(element).hasClass("custom-tp")){
                    params['step'] = 15;
                }
		element.timepicker(params);

		return container;
	},

	/**
	 * Function to destroy time fields
	 */
	destroyTimeFields : function(container) {

		if(typeof cotainer == 'undefined') {
			container = jQuery('body');
		}

		if(container.hasClass('timepicker-default')) {
			var element = container;
		}else{
			var element = container.find('.timepicker-default');
		}
		element.data('timepicker-list',null);
		return container;
	},

	/**
	 * Function to get the chosen element from the raw select element
	 * @params: select element
	 * @return : chosenElement - corresponding chosen element
	 */
	getChosenElementFromSelect : function(selectElement) {
		var selectId = selectElement.attr('id');
		var chosenEleId = selectId+"_chzn";
		return jQuery('#'+chosenEleId);
	},

	/**
	 * Function to get the select2 element from the raw select element
	 * @params: select element
	 * @return : select2Element - corresponding select2 element
	 */
	getSelect2ElementFromSelect : function(selectElement) {
		var selectId = selectElement.attr('id');
		//since select2 will add s2id_ to the id of select element
		var select2EleId = "s2id_"+selectId;
		return jQuery('#'+select2EleId);
	},

	/**
	 * Function to get the select element from the chosen element
	 * @params: chosen element
	 * @return : selectElement - corresponding select element
	 */
	getSelectElementFromChosen : function(chosenElement) {
		var chosenId = chosenElement.attr('id');
		var selectEleIdArr = chosenId.split('_chzn');
		var selectEleId = selectEleIdArr['0'];
		return jQuery('#'+selectEleId);
	},

	/**
	 * Function to set with of the element to parent width
	 * @params : jQuery element for which the action to take place
	 */
	setInheritWidth : function(elements) {
		jQuery(elements).each(function(index,element){
			var parentWidth = jQuery(element).parent().width();
			jQuery(element).width(parentWidth);
		});
	},


	initGuiders: function (list) {
	},

	showScrollBar : function(element, options) {
		if(typeof options == 'undefined') {
			options = {};
		}
		if(typeof options.height == 'undefined') {
			options.height = element.css('height');
		}

		return element.slimScroll(options);
	},

	showHorizontalScrollBar : function(element, options) {
		if(typeof options == 'undefined') {
			options = {};
		}
		var params = {
			horizontalScroll: true,
			theme: "dark-thick",
			advanced: {
				autoExpandHorizontalScroll:true
			}
		};
		if(typeof options != 'undefined'){
			var params = jQuery.extend(params,options);
		}
		return element.mCustomScrollbar(params);
	},

	/**
	 * Function returns translated string
	 */
	vtranslate : function(key) {
		if(app.languageString[key] != undefined) {
			return app.languageString[key];
		} else {
			var strings = jQuery('#js_strings').text();
			if(strings != '') {
				app.languageString = JSON.parse(strings);
				if(key in app.languageString){
					return app.languageString[key];
				}
			}
		}
		return key;
	},

	/**
	 * Function which will set the contents height to window height
	 */
	setContentsHeight : function() {
		var borderTopWidth = parseInt(jQuery(".mainContainer").css('margin-top'))+21; // (footer height 21px)
		jQuery('.bodyContents').css('min-height',(jQuery(window).innerHeight()-borderTopWidth));
	},

	/**
	 * Function will return the current users layout + skin path
	 * @param <string> img - image name
	 * @return <string>
	 */
	vimage_path : function(img) {
		return jQuery('body').data('skinpath')+ '/images/' + img ;
	},

	/*
	 * Cache API on client-side
	 */
	cacheNSKey: function(key) { // Namespace in client-storage
		return 'vtiger6.' + key;
	},
	cacheGet: function(key, defvalue) {
		key = this.cacheNSKey(key);
		return jQuery.jStorage.get(key, defvalue);
	},
	cacheSet: function(key, value) {
		key = this.cacheNSKey(key);
		jQuery.jStorage.set(key, value);
	},
	cacheClear : function(key) {
		key = this.cacheNSKey(key);
		return jQuery.jStorage.deleteKey(key);
	},

	htmlEncode : function(value){
		if (value) {
			return jQuery('<div />').text(value).html();
		} else {
			return '';
		}
	},

	htmlDecode : function(value) {
		if (value) {
			return $('<div />').html(value).text();
		} else {
			return '';
		}
	},

	/**
	 * Function places an element at the center of the page
	 * @param <jQuery Element> element
	 */
	placeAtCenter : function(element) {
		element.css("position","absolute");
		element.css("top", ((jQuery(window).height() - element.outerHeight()) / 2) + jQuery(window).scrollTop() + "px");
		element.css("left", ((jQuery(window).width() - element.outerWidth()) / 2) + jQuery(window).scrollLeft() + "px");
	},

	getvalidationEngineOptions : function(select2Status){
		return app.validationEngineOptions;
	},

	/**
	 * Function to notify UI page ready after AJAX changes.
	 * This can help in re-registering the event handlers (which was done during ready event).
	 */
	notifyPostAjaxReady: function() {
		jQuery(document).trigger('postajaxready');
	},

	/**
	 * Listen to xready notiications.
	 */
	listenPostAjaxReady: function(callback) {
		jQuery(document).on('postajaxready', callback);
	},

	/**
	 * Form function handlers
	 */
	setFormValues: function(kv) {
		for (var k in kv) {
			jQuery(k).val(kv[k]);
		}
	},

	setRTEValues: function(kv) {
		for (var k in kv) {
			var rte = CKEDITOR.instances[k];
			if (rte) rte.setData(kv[k]);
		}
	},

	/**
	 * Function returns the javascript controller based on the current view
	 */
	getPageController : function() {
		var moduleName = app.getModuleName();
		var view = app.getViewName();
		var parentModule = app.getParentModuleName();

		var moduleClassName = parentModule+"_"+moduleName+"_"+view+"_Js";
		if(typeof window[moduleClassName] == 'undefined'){
			moduleClassName = parentModule+"_Vtiger_"+view+"_Js";
		}
		if(typeof window[moduleClassName] == 'undefined') {
			moduleClassName = moduleName+"_"+view+"_Js";
		}
		if(typeof window[moduleClassName] == 'undefined') {
			moduleClassName = "Vtiger_"+view+"_Js";
		}
        if(typeof window[moduleClassName] != 'undefined') {
        	this.currentPageControllerClassName = moduleClassName;
        	this.currentPageController = new window[moduleClassName]();
			return this.currentPageController;
		}
	},

	currentPageControllerClassName : '',
	currentPageController : false,

	/**
	 * Function to decode the encoded htmlentities values
	 */
	getDecodedValue : function(value) {
		return jQuery('<div></div>').html(value).text();
	},

	/**
	 * Function to check whether the color is dark or light
	 */
	getColorContrast: function(hexcolor){
		var r = parseInt(hexcolor.substr(0,2),16);
		var g = parseInt(hexcolor.substr(2,2),16);
		var b = parseInt(hexcolor.substr(4,2),16);
		var yiq = ((r*299)+(g*587)+(b*114))/1000;
		return (yiq >= 128) ? 'light' : 'dark';
	},

    updateRowHeight : function() {
        var rowType = jQuery('#row_type').val();
        if(typeof rowType != 'undefined' && rowType.length <=0 ){
            //Need to update the row height
            var widthType = app.cacheGet('widthType', 'mediumWidthType');
            var serverWidth = widthType;
            switch(serverWidth) {
                case 'narrowWidthType' : serverWidth = 'narrow'; break;
                case 'wideWidthType' : serverWidth = 'wide'; break;
                default : serverWidth = 'medium';
            }
			var userid = jQuery('#current_user_id').val();
            var params = {
                'module' : 'Users',
                'action' : 'SaveAjax',
                'record' : userid,
                'value' : serverWidth,
                'field' : 'rowheight'
            };
            AppConnector.request(params).then(function(){
                jQuery(rowType).val(serverWidth);
            });
        }
    },

	getCookie : function(c_name) {
		var c_value = document.cookie;
		var c_start = c_value.indexOf(" " + c_name + "=");
		if (c_start == -1)
		  {
		  c_start = c_value.indexOf(c_name + "=");
		  }
		if (c_start == -1)
		  {
		  c_value = null;
		  }
		else
		  {
		  c_start = c_value.indexOf("=", c_start) + 1;
		  var c_end = c_value.indexOf(";", c_start);
		  if (c_end == -1)
			{
			c_end = c_value.length;
			}
		  c_value = unescape(c_value.substring(c_start,c_end));
		  }
		return c_value;
	},

	setCookie : function(c_name,value,exdays) {
		var exdate=new Date();
		exdate.setDate(exdate.getDate() + exdays);
		var c_value=escape(value) + ((exdays==null) ? "" : "; expires="+exdate.toUTCString());
		document.cookie=c_name + "=" + c_value;
	},

	registerDateTimePicker: function (parentElement) {

		if(typeof parentElement == 'undefined') {
			parentElement = jQuery('body');
		}

		parentElement = jQuery(parentElement);

		if(parentElement.hasClass('dateTimeField')){
			var element = parentElement;
		}else{
			var element = jQuery('.dateTimeField', parentElement);
		}
		element.datepicker({'autoclose':true}).on('changeDate', function(ev){
			var currentElement = jQuery(ev.currentTarget);
			var dateFormat = currentElement.data('dateFormat');
			var finalFormat = app.getDateInVtigerFormat(dateFormat,ev.date);
			var date = jQuery.datepicker.formatDate(finalFormat,ev.date);
			currentElement.val(date);
		});

		var dateFormat = element.data('date-format');
		var timeFormat = element.data('time-format');
		if(timeFormat == 12) {
			element.datetimepicker({
				format: dateFormat + ' HH:ii P',
				autoclose: true,
				todayBtn: true,
				showMeridian: true
			});
		}else{
			element.datetimepicker({
				format: dateFormat + ' hh:ii',
				autoclose: true,
				todayBtn: true
			});
		}
		element.datetimepicker('update');

	},

};

jQuery(document).ready(function(){
	app.changeSelectElementView();

	//register all select2 Elements
	app.showSelect2ElementView(jQuery('body').find('select.select2'));
    app.showReferenceMultiSelectView(jQuery('body').find('input.select2'));
	app.setContentsHeight();

	//Updating row height
	app.updateRowHeight();

	jQuery(window).resize(function(){
		app.setContentsHeight();
	});

	String.prototype.toCamelCase = function(){
		var value = this.valueOf();
		return  value.charAt(0).toUpperCase() + value.slice(1).toLowerCase()
	};

    // in IE resize option for textarea is not there, so we have to use .resizable() api
    if(jQuery.browser.msie || (/Trident/).test(navigator.userAgent)) {
        jQuery('textarea').resizable();
    }

	// Instantiate Page Controller
	var pageController = app.getPageController();

	if(pageController) pageController.registerEvents();
});

/* Global function for UI5 embed page to callback */
function resizeUI5IframeReset() {
	jQuery('#ui5frame').height(650);
}
function resizeUI5Iframe(newHeight) {
	jQuery('#ui5frame').height(parseInt(newHeight,10)+15); // +15px - resize on IE without scrollbars
}
