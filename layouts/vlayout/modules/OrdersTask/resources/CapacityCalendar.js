Vtiger_List_Js("OrdersTask_CapacityCalendarView_Js", {

    currentInstance: false,

    getInstanceByView: function () {
        var view = jQuery('#currentView').val();
        var jsFileName = view + 'View';
        var moduleClassName = view + "_" + jsFileName + "_Js";
        if (typeof window[moduleClassName] != 'undefined') {
            var instance = new window[moduleClassName]();
        } else {
            instance = new OrdersTask_CapacityCalendarView_Js();
        }
        return instance;
    },
    initiateCalendarFeeds: function () {
        OrdersTask_CapacityCalendarView_Js.performCalendarFeedIntiate();
    },
   createOrderTask: function (){
	var date = jQuery("#selected_date").val();
	if(date){
	    window.open('index.php?module=OrdersTask&view=Edit&fromcapacity=true&seldate='+date,'_blank');
	}else{
            var params = {
                title: app.vtranslate('JS_MESSAGE'),
                text: app.vtranslate('Please select a day from the calendar first.'),
                animation: 'show',
                type: 'info'
            };
            Vtiger_Helper_Js.showPnotify(params);
	}
    },
    createDailyNote: function (e) {
        thisInstance = this;
	var postQuickCreateSave = function (data) {
            var params = {
                title: app.vtranslate('JS_MESSAGE'),
                text: app.vtranslate('DailyNotes created with DailyNotes Number: ' + data.result._recordLabel),
                animation: 'show',
                type: 'info'
            };
            Vtiger_Helper_Js.showPnotify(params);
        };
        var quickCreateParams = {};
        quickCreateParams['noCache'] = true;
        quickCreateParams['callbackFunction'] = postQuickCreateSave;
        var progress = jQuery.progressIndicator();
        var headerInstance = new Vtiger_Header_Js();
        headerInstance.getQuickCreateForm('index.php?module=DailyNotes&view=QuickCreateAjax', 'DailyNotes', quickCreateParams).then(function (data) {
            progress.progressIndicator({'mode': 'hide'});
            headerInstance.handleQuickCreateData(data, quickCreateParams);	    
        });
    },
    createHoliday: function (type) {
        thisInstance = this;
	var postQuickCreateSave = function (data) {
            var params = {
                title: app.vtranslate('JS_MESSAGE'),
                text: app.vtranslate('Holiday created with Holiday Number: ' + data.result._recordLabel),
                animation: 'show',
                type: 'info'
            };
            Vtiger_Helper_Js.showPnotify(params);
        };
        var quickCreateParams = {};
        quickCreateParams['noCache'] = true;
        quickCreateParams['callbackFunction'] = postQuickCreateSave;
        var progress = jQuery.progressIndicator();
        var headerInstance = new Vtiger_Header_Js();
        headerInstance.getQuickCreateForm('index.php?module=Holiday&view=QuickCreateAjax', 'Holiday', quickCreateParams).then(function (data) {
            progress.progressIndicator({'mode': 'hide'});
            headerInstance.handleQuickCreateData(data, quickCreateParams);
	    jQuery('div.modal-body > table.massEditTable').find('[name="holiday_type"]').find('option[value="'+type+'"]').prop("selected",true).trigger("liszt:updated");
	    
        });
    },
    openLocalDispatch: function (){
	var date = jQuery("#selected_date").val﻿().replace(/-/g, "/");
	if(date){
            var date2 = new Date(date);
            var month = date2.getMonth();
            var m = '';
            if(month < 9){m = '0'+(date2.getMonth()+1);}else{m = (date2.getMonth()+1);}
            var day = date2.getDate();
            var d = '';
            if(day < 10){d = '0'+(date2.getDate());}else{d = date2.getDate();}
            var sqlFormatDate = date2.getFullYear()+'-'+m+'-'+d;
	    window.open('index.php?module=OrdersTask&view=NewLocalDispatch&from_date='+sqlFormatDate+'&to_date='+sqlFormatDate,'_blank');
	}else{
            var params = {
                title: app.vtranslate('JS_MESSAGE'),
                text: app.vtranslate('Please select a day from the calendar first.'),
                animation: 'show',
                type: 'info'
            };
            Vtiger_Helper_Js.showPnotify(params);
	}
    },
    triggerEditFilter: function() {
	var selectedFilterElement = jQuery('#customFilter').find(':selected');
	var cvid = selectedFilterElement.val();
	var lockedViews = JSON.parse(jQuery('input[name="lockedViews"]').val());
	if(lockedViews.indexOf(cvid) != -1) {
	    var message = app.vtranslate('JS_LBL_NO_EDIT_FILTER_PERMISSIONS') + ' ' + app.vtranslate('JS_LBL_CREATE_NEW_FILTER_CONFIRMATION');
	    Vtiger_Helper_Js.showConfirmationBox({'message' : message}).then(
		function(e) {
		    triggerCreateFilter();
		}
	    );
	} else {
	    var editURL = selectedFilterElement.data('editurl');
	    console.log(editURL);
	    Vtiger_CustomView_Js.loadFilterView(selectedFilterElement.data('editurl'));
	}
    },
}, {
    filterSelectElement: false,
    calendarView: false,
    calendarCreateView: false,
    //Hold the conditions for a hour format
    hourFormatConditionMapping: false,
    //Hold the saved values of calendar settings
    calendarSavedSettings: false,
    CalendarSettingsContainer: false,
    weekDaysArray: {Sunday: 0, Monday: 1, Tuesday: 2, Wednesday: 3, Thursday: 4, Friday: 5, Saturday: 6},
    calendarfeedDS: {},
    getCalendarView: function () {
        if (this.calendarView == false) {
            this.calendarView = jQuery('#calendarview');
        }
        return this.calendarView;
    },
    fetchCalendarFeed: function () {

        var thisInstance = this;
      
        
        this.calendarfeedDS = function (start, end, timezone, callback) {
            var filterSelectElement = thisInstance.getFilterSelectElement();
            var startDate = thisInstance.getCalendarView().fullCalendar('getView').start;
            var endDate = thisInstance.getCalendarView().fullCalendar('getView').end;
            var resource = (jQuery('#resource').val() != null ? jQuery('#resource').val() : '');
	    var resourceType = (jQuery('#resource_type').val() != null ? jQuery('#resource_type').val() : '');

            var loadingMessage = app.vtranslate('JS_LOADING_CAPACITY_CALENDAR');
            var progressIndicatorElement = jQuery.progressIndicator({
                'message': loadingMessage,
                'position': 'html',
                'blockInfo': {
                    'enabled': true
                }
            });
            var params = {
                module: 'OrdersTask',
                action: 'CapacityCalendarFeed',
                start: thisInstance.toDateString(startDate),
                end: thisInstance.toDateString(endDate),
                custom_view: filterSelectElement.val(),
                resource: resource,
                resourceType: resourceType,
		filter_id: jQuery("#customFilter option:selected").val(),
            }

            AppConnector.request(params).then(function (events) {
                callback(events);
                progressIndicatorElement.progressIndicator({
                    'mode': 'hide'
                })
            },
                    function (error) {
                        //To send empty events if error occurs
                        callback([]);
                    });
        }

        this.getCalendarView().fullCalendar('removeEventSources');
        this.getCalendarView().fullCalendar('removeEvents');
        this.getCalendarView().fullCalendar('addEventSource', this.calendarfeedDS);
    },
    toDateString: function (date) {
       
        var d = date._d.getDate();
        var m = date._d.getMonth() + 1;
        var y = date._d.getFullYear();
        d = (d <= 9) ? ("0" + d) : d;
        m = (m <= 9) ? ("0" + m) : m;
        return y + "-" + m + "-" + d;
    },
    performCalendarFeedIntiate: function () {
        //this.registerCalendarFeedChange();
        this.fetchCalendarFeed();
    },
    registerCalendarFeedChange: function () {
        var thisInstance = this;
        jQuery('#calendarview-feeds').on('change', '[data-calendar-feed]', function (e) {
            var currentTarget = $(e.currentTarget);
            var type = currentTarget.data('calendar-sourcekey');
            if (currentTarget.is(':checked')) {
                // NOTE: We are getting cache data fresh - as it shared between browser tabs
                var disabledOnes = app.cacheGet('calendar.feeds.disabled', []);
                // http://stackoverflow.com/a/3596096
                disabledOnes = jQuery.grep(disabledOnes, function (value) {
                    return value != type;
                });
                app.cacheSet('calendar.feeds.disabled', disabledOnes);
                if (!thisInstance.calendarfeedDS[type]) {
                    thisInstance.fetchAllCalendarFeeds();
                }
                thisInstance.getCalendarView().fullCalendar('addEventSource', thisInstance.calendarfeedDS[type]);
            } else {
                // NOTE: We are getting cache data fresh - as it shared between browser tabs
                var disabledOnes = app.cacheGet('calendar.feeds.disabled', []);
                if (disabledOnes.indexOf(type) == -1)
                    disabledOnes.push(type);
                app.cacheSet('calendar.feeds.disabled', disabledOnes);
                thisInstance.getCalendarView().fullCalendar('removeEventSources', thisInstance.calendarfeedDS[type]);
            }
        });
    },
    loadTask: function (start, jsEvent, view) {
        var thisInstance = this;
        thisInstance.fetchOrdersDay(start._d, 'JS_LOADING_ORDERS');
    },
    registerCalendar: function (customConfig) {
        var thisInstance = this;
        var calendarview = this.getCalendarView();
        //Default time format
        var userDefaultTimeFormat = jQuery('#time_format').val();
        if (userDefaultTimeFormat == 24) {
            userDefaultTimeFormat = 'H(:mm)';
        } else {
            userDefaultTimeFormat = 'h(:mm)tt';
        }

        //Default first day of the week
        var defaultFirstDay = jQuery('#start_day').val();
        var convertedFirstDay = thisInstance.weekDaysArray[defaultFirstDay];
        //Default first hour of the day
        var defaultFirstHour = jQuery('#start_hour').val();
        var explodedTime = defaultFirstHour.split(':');
        defaultFirstHour = explodedTime['0'];
        //Date format in agenda view must respect user preference
        var dateFormat = jQuery('#date_format').val();
        //Converting to fullcalendar accepting date format
        monthPos = dateFormat.search("mm");
        datePos = dateFormat.search("dd");
        if (monthPos < datePos)
            dateFormat = "M/d";
        else
            dateFormat = "d/M";
        var config = {
            header: {
                left: '',
                center: 'title today',
                right: 'prev,next'
            },
            columnFormat:'dddd',
            height: 600,
            timeFormat: userDefaultTimeFormat + '{ - ' + userDefaultTimeFormat + '}',
            axisFormat: userDefaultTimeFormat,
            firstHour: defaultFirstHour,
            firstDay: convertedFirstDay,
            defaultView: 'month',
            editable: false,
            eventStartEditable: false,
            selectable: true,
            selectHelper: true,
            select: function (start, end, jsEvent, view) {

		jQuery(".fc-state-highlight").removeClass("fc-state-highlight");
		jQuery(".vgs-highlight").removeClass("vgs-highlight");
		jQuery("td[data-date="+start.format('YYYY-MM-DD')+"]").addClass("vgs-highlight");

                thisInstance.loadTask(start, jsEvent, view);
            },
            eventClick: function (calEvent, jsEvent, view) {
		jQuery(".fc-state-highlight").removeClass("fc-state-highlight");
		jQuery(".vgs-highlight").removeClass("vgs-highlight");
		jQuery("td[data-date="+calEvent.start.format('YYYY-MM-DD')+"]").addClass("vgs-highlight");

                thisInstance.loadTask(calEvent.start, jsEvent, view);
            },
	    eventRender: function(event, element, view) {
		var color = (event.color == "#FFF") ? "#000" : "#FFF";
		var blocked = "";
		var holiday = "";
		var notes = "";
		var text = element.find('span.fc-title').text();
		
		notes = (event.hasNotes) ? "See notes below" : "&nbsp;";
		blocked = (event.isBlocked) ? "Blocked" : "&nbsp;";
		holiday = (event.isHoliday) ? "Holiday" : "&nbsp;";
		
		var fontSize = "150%;";
		element.find('span.fc-title').after("<div class='row-fluid' style='height: 100%'><div class='span12' style='height: 40%'><p style='text-align: center;font-size: "+fontSize+";color:"+color+";'>"+text+"</p></div><div class='span12' style='height: 20%'><p style='text-align: center;color:"+color+";'>"+notes+"</p></div><div class='span12' style='height: 20%'><div class='row-fluid'><div class='span6'><p style='text-align: left;padding-left:0.5%;color:"+color+";'>"+blocked+"</p></div><div class='span6'><p style='text-align: right;padding-right:5%;color:"+color+";'>"+holiday+"</p></div></div></div></div>");
		element.find('span.fc-title').remove();
	    },
            minTime: '06:00:00',
            maxTime: '21:00:00',
            businessHours: {
                start: '6:00', // a start time (10am in this example)
                end: '21:00', // an end time (6pm in this example)
		dow: [0, 1, 2, 3, 4, 5, 6]
            },
            slotMinutes: 30,
            defaultEventMinutes: 0,
            monthNames: [app.vtranslate('LBL_JANUARY'), app.vtranslate('LBL_FEBRUARY'), app.vtranslate('LBL_MARCH'),
                app.vtranslate('LBL_APRIL'), app.vtranslate('LBL_MAY'), app.vtranslate('LBL_JUNE'), app.vtranslate('LBL_JULY'),
                app.vtranslate('LBL_AUGUST'), app.vtranslate('LBL_SEPTEMBER'), app.vtranslate('LBL_OCTOBER'),
                app.vtranslate('LBL_NOVEMBER'), app.vtranslate('LBL_DECEMBER')],
            monthNamesShort: [app.vtranslate('LBL_JAN'), app.vtranslate('LBL_FEB'), app.vtranslate('LBL_MAR'),
                app.vtranslate('LBL_APR'), app.vtranslate('LBL_MAY'), app.vtranslate('LBL_JUN'), app.vtranslate('LBL_JUL'),
                app.vtranslate('LBL_AUG'), app.vtranslate('LBL_SEP'), app.vtranslate('LBL_OCT'), app.vtranslate('LBL_NOV'),
                app.vtranslate('LBL_DEC')],
            dayNames: [app.vtranslate('LBL_SUNDAY'), app.vtranslate('LBL_MONDAY'), app.vtranslate('LBL_TUESDAY'),
                app.vtranslate('LBL_WEDNESDAY'), app.vtranslate('LBL_THURSDAY'), app.vtranslate('LBL_FRIDAY'),
                app.vtranslate('LBL_SATURDAY')],
            dayNamesShort: [app.vtranslate('LBL_SUN'), app.vtranslate('LBL_MON'), app.vtranslate('LBL_TUE'),
                app.vtranslate('LBL_WED'), app.vtranslate('LBL_THU'), app.vtranslate('LBL_FRI'),
                app.vtranslate('LBL_SAT')],
            buttonText: {
                today: app.vtranslate('LBL_TODAY'),
                month: app.vtranslate('LBL_MONTH'),
                week: app.vtranslate('LBL_WEEK'),
                day: app.vtranslate('LBL_DAY')
            },
            allDayText: app.vtranslate('LBL_ALL_DAY'),
            eventAfterRender: function (event, element, view) {
		jQuery('td.fc-day[data-date="'+event.start._i+'"]').css("background-color", event.color);
            },
            droppable: true,
            drop: function (date) {
                thisInstance = new OrdersTask_CapacityCalendarView_Js();
                thisInstance.handleDropEvent(date, jQuery(this).attr('id'));
            },   
	    dayClick: function (date, jsEvent, view){
		jQuery(".fc-highlight").removeClass("fc-highlight");
	    }
        }
        if (typeof customConfig != 'undefined') {
            config = jQuery.extend(config, customConfig);
        }
        calendarview.fullCalendar(config);
    },
    addActionsLinks: function () {
        jQuery('.icon-eye-open').on('click', function (e) {
            window.open('index.php?module=OrdersTask&view=Detail&record=' + jQuery(this).attr('id'), '_blank');
        });

        jQuery('.icon-pencil').on('click', function (e) {
            window.open('index.php?module=OrdersTask&view=Edit&fromcapacity=true&record=' + jQuery(this).attr('id'), '_blank');
        });
    },
    handleDropEvent: function (date, task_id) {
        var thisInstance = this;

//        if (date.getDay() == 0 || date.getDay() == 6) {
//            return false;
//        }

        var loadingMessage = app.vtranslate('JS_UPDATING_NEW_TASK_DAY');
        var progressIndicatorElement = jQuery.progressIndicator({
            'message': loadingMessage,
            'position': 'html',
            'blockInfo': {
                'enabled': true
            }
        });
        var params = {
            module: 'OrdersTask',
            action: 'CapacityCalendarActions',
            mode: 'movetask',
            new_date: thisInstance.toDateString(date),
            task_id: task_id
        };

        AppConnector.request(params).then(function (events) {
            progressIndicatorElement.progressIndicator({
                'mode': 'hide'
            });
            jQuery('#calendarview').fullCalendar('refetchEvents');
            thisInstance.fetchOrdersDay(date);
        },
                function (error) {
                    //To send empty events if error occurs
                    jQuery('#calendarview').fullCalendar('refetchEvents');
                });

    },
    fetchOrdersDay: function (start, message='JS_LOADING_CAPACITY_CALENDAR') {
	//start = start._d;
        thisInstance = this;
        var resource = jQuery('#resource').val();
        var resourceType = jQuery('#resource_type').val();
        var params = {
            'module': app.getModuleName(),
            'action': 'CapacityCalendarOrdersFeed',
            'start_date': start.getTime() / 1000,
            'offset': start.getTimezoneOffset(),
            'resource': resource,
            'resource_type': resourceType,
	    'filter_id': jQuery("#customFilter option:selected").val(),
        };

        var loadingMessage = app.vtranslate(message);
        var progressIndicatorElement = jQuery.progressIndicator({
            'message': loadingMessage,
            'position': 'html',
            'blockInfo': {
                'enabled': true
            }
        });
        AppConnector.request(params).then(function (data) {

            if (data.success) {
                progressIndicatorElement.progressIndicator({
                    'mode': 'hide'
                });
                var resourceContainer = jQuery('.resource_container');
                if (data.result.result == "OK") {

                    resourceContainer.html(data.result.result_data);
		    var date = new Date(jQuery("#selected_date").data('forsafari'));
                    jQuery('.selected-day').html(thisInstance.getFormattedDate(new Date(date.valueOf() + date.getTimezoneOffset() * 60000)));
                    
                    thisInstance.addActionsLinks();
                    thisInstance.changeTaskDispatchStatus();
		    jQuery('.chzn.select').chosen();
                    jQuery('[name^="HolidayBlockedTable"]').find('.chzn.select').chosen();
                    
                    //Adding Black Box to the date
                    jQuery(".fc-state-highlight").removeClass("fc-state-highlight");
                    jQuery(".vgs-highlight").removeClass("vgs-highlight");
                    jQuery("td[data-date="+ date.toISOString().substring(0, 10) +"]").addClass("vgs-highlight");
		    
		    var e = new OrdersTask_List_Js;
		    e.registerCustomTooltipEvents();
                }
            }
        });
    },
    fetchNotesHolidays: function (mode) {
        if(jQuery("#selected_date").val() != ""){
	    if(new Date(jQuery("#selected_date").val())){
		var start = new Date(jQuery("#selected_date").data("forsafari"));
	    }
	}else{
	    var start = new Date();
	}
        
	//start = start._d;
        thisInstance = this;
        var resource = jQuery('#resource').val();
        var resourceType = jQuery('#resource_type').val();
        var params = {
            'module': app.getModuleName(),
            'action': 'CapacityCalendarOrdersFeed',
            'mode': mode,
            'start_date': start.getTime() / 1000,
            'offset': start.getTimezoneOffset(),
            'resource': resource,
            'resource_type': resourceType,
	    'filter_id': jQuery("#customFilter option:selected").val(),
        };
        
        var loadingMessage = app.vtranslate('Loading');
        var progressIndicatorElementHolidays = jQuery.progressIndicator({
            'message': loadingMessage,
            'position': 'html',
            'blockInfo': {
                'enabled': true
            }
        });
        AppConnector.request(params).then(function (data) {

            if (data.success) {
                
                
                if(mode == 'holidays'){
                    var resourceContainer = jQuery('.holidays');
                }else{
                    var resourceContainer = jQuery('.notes');
                }
                
                
                if (data.result.result == "OK") {

                    resourceContainer.html(data.result.result_data);
                    progressIndicatorElementHolidays.progressIndicator({
                        'mode': 'hide'
                    });

		    jQuery('.chzn.select').chosen();
                    jQuery('[name^="HolidayBlockedTable"]').find('.chzn.select').chosen();
                    jQuery('.selected-day').html(thisInstance.getFormattedDate(new Date(start.valueOf() + start.getTimezoneOffset() * 60000)));


                }
            }
        });
    },
    getUsefulDateFormat: function(date){
        var dd = (date.getDate() < 10) ? '0'+date.getDate() : date.getDate();
	var mm = ((date.getMonth()+1) < 10) ? '0'+(date.getMonth()+1) : (date.getMonth()+1);
        var yyyy = date.getFullYear();


        return yyyy+'-'+mm+'-'+dd;
    },
    getFormattedDate: function (date) {
        var dd = date.getDate();
        var yyyy = date.getFullYear();
        var month = new Array();
        month[0] = "January";
        month[1] = "February";
        month[2] = "March";
        month[3] = "April";
        month[4] = "May";
        month[5] = "June";
        month[6] = "July";
        month[7] = "August";
        month[8] = "September";
        month[9] = "October";
        month[10] = "November";
        month[11] = "December";
        var n = month[date.getMonth()];

        return n + ' ' + dd + ', ' + yyyy;
    },
    changeTaskDispatchStatus: function () {
        jQuery('.dispatch_status').on('change', function (e) {

            var new_status = jQuery(this).val();
            var loadingMessage = app.vtranslate('JS_UPDATING_NEW_TASK_DAY');
            var progressIndicatorElement = jQuery.progressIndicator({
                'message': loadingMessage,
                'position': 'html',
                'blockInfo': {
                    'enabled': true
                }
            });
            var params = {
                module: 'OrdersTask',
                action: 'CapacityCalendarActions',
                mode: 'udpatestatus',
                status: new_status,
                task_id: jQuery(this).attr('id')
            };

            AppConnector.request(params).then(function (result) {
                progressIndicatorElement.progressIndicator({
                    'mode': 'hide'
                });

                if (new_status == 'Rejected' || result.result.old_status == 'Rejected') {
                    jQuery('#calendarview').fullCalendar('refetchEvents');
                }
            },
                    function (error) {
                        //To send empty events if error occurs
                        jQuery('#calendarview').fullCalendar('refetchEvents');
                    });

        });
    },
    refreshBottomInfo: function(){
	//if date selected then safari way or other browsers way, if no date selected then today is the date
	if(jQuery("#selected_date").val() != ""){
	    if(new Date(jQuery("#selected_date").val())){
		var myDate = new Date(jQuery("#selected_date").data("forsafari"));
	    }
	}else{
	    var myDate = new Date();
	}
	thisInstance.fetchOrdersDay(myDate);
    },
    registerChangeCustomFilterEvent: function () {
        var thisInstance = this;
        var filterSelectElement = this.getFilterSelectElement();
        filterSelectElement.change(function (e) {
            thisInstance.fetchCalendarFeed();
	    thisInstance.refreshBottomInfo();
	    jQuery('#resource').trigger('change'); //reload resource type
	    var e = new OrdersTask_List_Js;
	    e.registerCustomTooltipEvents();

        });
    },
    registerChangeResource: function () {
	var thisInstance = this;
	var firstTime = true;
        jQuery('#resource').on('change', function (e) {
            var resource = jQuery(this).val();
            var params = {
                module: 'OrdersTask',
                action: 'CapacityCalendarActions',
                mode: 'getResourceDropdownOptions',
                resource: resource,
		cvid: jQuery("#customFilter option:selected").val(),
            };
            if(resource == 'employees'){
                $("#resource_name").html('Personnel Role:&nbsp;')
            }else{
                $("#resource_name").html('Vehicle Type:&nbsp;')
            }
            AppConnector.request(params).then(function (result) {
                if (result.success) {
                    var options = result.result.options;
                    $("#resource_type").html(options);
		    if(!firstTime){
			thisInstance.fetchCalendarFeed(); //because of "no daily notes text" change too.
			thisInstance.refreshBottomInfo();
		    }else{
			firstTime = false;
		    }
                }
            },
            function (error) {
            
            });
        });
        jQuery('#resource').trigger('change');
    },
    registerChangeResourceValue: function () {
        var thisInstance = this;
        jQuery('#resource_type').on('change', function (e) {
            thisInstance.fetchCalendarFeed();
	    thisInstance.refreshBottomInfo();
        });
    },
    registerAppendActionsButton: function () {
        $('.fc-left').append($('#actionsButton').removeClass('hide'))
    },
    changeCustomFilterElementView : function() {
	var filterSelectElement = this.getFilterSelectElement();
	if(filterSelectElement.length > 0 && filterSelectElement.is("select")) {
	    app.showSelect2ElementView(filterSelectElement,{
		formatSelection : function(data, contianer){
		    var resultContainer = jQuery('<span></span>');
		    resultContainer.append(jQuery(jQuery('.filterImage').clone().get(0)).show());
		    resultContainer.append(data.text);
		    return resultContainer;
		},
		customSortOptGroup : true
	    });

	    var select2Instance = filterSelectElement.data('select2');
            // jQuery('span.filterActionsDiv').prepend("<hr>").appendTo(select2Instance.dropdown).removeClass('hide');
	    // jQuery('ul.filterActions').removeClass('hide');
	}
    },
    registerRemoveHolidayBlockedButton : function(){
	jQuery(document).on( 'click', '.removeHolidayBlocked', function(){
	    if(jQuery(this).siblings('input:hidden[name^="holidayblockedId"]').val() == 'none'){
		jQuery(this).parent().parent().remove()
	    } else{
		jQuery(this).parent().parent().addClass('hide');
		jQuery(this).siblings('input:hidden[name^="holidayblockedDelete"]').val('deleted');
	    }
            
            jQuery('.holidayblockedSave').trigger('click');
            
	});
    },
    registerAddHolidayBlockedButtons : function() {
	jQuery(document).on('click','.addHolidayBlocked',function() {
	    var table = jQuery('[name^="HolidayBlockedTable"]').find('tbody');
	    var newRow = jQuery('.defaultholidayblockedRow').clone();
	    var sequence = jQuery('[name="holidayblockedNumRows"]').val();
	    sequence++;
	    jQuery('[name="holidayblockedNumRows"]').val(sequence);
	    newRow.addClass('newHolidayBlockedRow').removeClass('hide defaultholidayblockedRow');

	    newRow = newRow.appendTo(table);
	    newRow.find('input, select').each(function(idx, ele){
		if(jQuery(ele).is('select')){
		    jQuery(ele).removeClass('defaultselect');
		    jQuery(ele).addClass('chzn select');
		}
	    });

	    jQuery('[name^="HolidayBlockedTable"] tbody tr:last').find('.chzn.select').chosen();
	});
    },
    registerHolidayBlockedSave: function(){
        thisInstance = this;
	jQuery(document).on('click','.holidayblockedSave',function() {
	    var holidayblocked = new Array();
	    jQuery('[name^="HolidayBlockedTable"] tbody tr.holidayblockedRow:not(.defaultholidayblockedRow)').each(function(){
		var obj = new Object();
		
		obj.holidayblockedDelete = jQuery(this).find("td:nth-child(1)").find('[name="holidayblockedDelete"]').val();
		obj.holidayblockedId = jQuery(this).find("td:nth-child(1)").find('[name="holidayblockedId"]').val();
		obj.holidayblockedType = jQuery(this).find("td:nth-child(2) span.holidaytype").find("select option:selected").val();
		obj.holidayblockedOwner = jQuery(this).find("td:nth-child(3) span.owner").find("select option:selected").val();
		obj.holidayblockedBussinesLine = jQuery(this).find("td:nth-child(4) span.bussinesline select").val();
		
		holidayblocked.push(obj);
	    });

	    var params = {
		'module': app.getModuleName(),
		'action': 'CapacityCalendarActions',
		'mode': 'saveHolidayBlocked',
		'holidayblocked' : holidayblocked,
		'holiday_date' : jQuery("#selected_date").val(),
	    };

	    var loadingMessage = app.vtranslate('JS_SAVING_HOLIDAY_BLOCKED');
	    var progressIndicatorElement = jQuery.progressIndicator({
		'message': loadingMessage,
		'position': 'html',
		'blockInfo': {
		    'enabled': true
		}
	    });
	    AppConnector.request(params).then(function (data) {
		if (data.success) {
		    progressIndicatorElement.progressIndicator({
			'mode': 'hide'
		    });
		    
		    if (data.result == "OK") {
			var params = {
			    title: app.vtranslate('JS_OK'),
			    text: app.vtranslate('Blocked / Holiday information succefully save!'),
			    animation: 'show',
			    type: 'info'
			};
                        
                        jQuery('[name^="HolidayBlockedTable"] tbody tr.holidayblockedRow:not(.defaultholidayblockedRow)').each(function(){
                            if(jQuery(this).hasClass('hide')){
                                jQuery(this).remove();
                            }
                        });
                        
                        thisInstance.performCalendarFeedIntiate();
			thisInstance.fetchCalendarFeed(); //(F)
                        thisInstance.fetchNotesHolidays('holidays');
		    }else{
			var params = {
			    title: app.vtranslate('JS_ERROR'),
			    text: app.vtranslate(data.result),
			    animation: 'show',
			    type: 'error'
			};
		    }
		    Vtiger_Helper_Js.showPnotify(params);
		}
	    });
	});
    },
    //UIType 3333 --> if "All" is selected after other values have been entered, it should clear the other selections and only show "All".  And vice-versa, if "All is entered" and they put another value, it should clear out "All".
    registerBL: function(){ 
	jQuery(document).on("change",".bussinesline select",function(){
	    var thes = jQuery(this);
	    var val = jQuery(thes).val();
	    if(val && val.indexOf("All") > -1){
		jQuery(".bussinesline select option:selected").each(function(){
		    if(jQuery(this).val() != "All")
			jQuery(this).prop("selected",false);
		});
		jQuery(thes).trigger("liszt:updated");
	    }
	});
    },
    registerRemoveDailyNotesButton : function(){
	jQuery(document).on( 'click', '.removeDailyNotes', function(){
	    if(jQuery(this).siblings('input:hidden[name^="dailynotesId"]').val() == 'none'){
		jQuery(this).parent().parent().remove()
	    } else{
		jQuery(this).parent().parent().addClass('hide');
		jQuery(this).siblings('input:hidden[name^="dailynotesDelete"]').val('deleted');
	    }
            jQuery('.dailynotesSave').trigger('click');
            
	});
    },
    registerAddDailyNotesButtons : function() {
	jQuery(document).on('click','.addDailyNotes',function() {
	    var table = jQuery('[name^="DailyNotesTable"]').find('tbody');
	    var newRow = jQuery('.defaultdailynotesRow').clone();
	    var sequence = jQuery('[name="dailynotesNumRows"]').val();
	    sequence++;
	    jQuery('[name="dailynotesNumRows"]').val(sequence);
	    newRow.addClass('newDailyNotesRow').removeClass('hide defaultdailynotesRow');

	    newRow = newRow.appendTo(table);
	    newRow.find('input, select').each(function(idx, ele){
		if(jQuery(ele).is('select')){
		    jQuery(ele).removeClass('defaultselect');
		    jQuery(ele).addClass('chzn select');
		}
	    });

	    jQuery('[name^="DailyNotesTable"] tbody tr:last').find('.chzn.select').chosen();
	});
    },
    registerDailyNotesSave: function(){
	jQuery(document).on('click','.dailynotesSave',function() {
	    var dailyNotes = new Array();
	    jQuery('[name^="DailyNotesTable"] tbody tr.dailynotesRow:not(.defaultdailynotesRow)').each(function(){
		var obj = new Object();
		
		obj.dailynotesDelete = jQuery(this).find("td:nth-child(1)").find('[name="dailynotesDelete"]').val();
		obj.dailynotesId = jQuery(this).find("td:nth-child(1)").find('[name="dailynotesId"]').val();
		obj.dailynotesOwner = jQuery(this).find("td:nth-child(2) span.owner").find("select option:selected").val();
		obj.dailynotesNote = jQuery(this).find("td:nth-child(3) span.note textarea").val();
		
		dailyNotes.push(obj);


	    });

	    var params = {
		'module': app.getModuleName(),
		'action': 'CapacityCalendarActions',
		'mode': 'saveDailyNotes',
		'dailyNotes' : dailyNotes,
		'dailynotes_date' : jQuery("#selected_date").val(),
	    };

	    var loadingMessage = app.vtranslate('JS_SAVING_DAILY_NOTES');
	    var progressIndicatorElement = jQuery.progressIndicator({
		'message': loadingMessage,
		'position': 'html',
		'blockInfo': {
		    'enabled': true
		}
	    });
	    AppConnector.request(params).then(function (data) {
		if (data.success) {
		    progressIndicatorElement.progressIndicator({
			'mode': 'hide'
		    });
		    
		    if (data.result == "OK") {
			var params = {
			    title: app.vtranslate('JS_OK'),
			    text: app.vtranslate('Daily Notes information succefully save!'),
			    animation: 'show',
			    type: 'info'
			};
			thisInstance.fetchCalendarFeed(); //(F)
                        thisInstance.fetchNotesHolidays('notes');
                        jQuery('[name^="DailyNotesTable"] tbody tr.dailynotesRow:not(.defaultdailynotesRow').each(function(){
                            if(jQuery(this).hasClass('hide')){
                                jQuery(this).remove();
                            }
                        });

		    }else{
			var params = {
			    title: app.vtranslate('JS_ERROR'),
			    text: app.vtranslate(data.result),
			    animation: 'show',
			    type: 'error'
			};
		    }
		    Vtiger_Helper_Js.showPnotify(params);
                    thisInstance.performCalendarFeedIntiate();
		}
	    });
	});
    },
    registerModalEvent: function(){
        jQuery(document).on("click",".resourcePopup",function(){
            var progress = jQuery.progressIndicator();
            var date = jQuery("#selected_date").val();
            var getEmployees = $(this).hasClass('employeesNumber');
            var resource = jQuery('#resource').val();
            var resourceType = jQuery('#resource_type').val();
            var cvid = jQuery("#customFilter option:selected").val();
            var params = {
                'module': 'OrdersTask',
                'view': 'LocalDispatchCapacityCalendar',
                'mode': 'ShowResourcesModal',
                'date': date,
                'getEmployees': getEmployees,
                'resource': resource,
                'resourceType': resourceType,
                'cvid': cvid
            };
            AppConnector.request(params).then(function (data) {
                    progress.progressIndicator({'mode': 'hide'});
                    if (data) {
                        app.showModalWindow(data, function (data) {
                        });
                    }else{
                        var params = {
                            title: app.vtranslate("Error!"),
                            text: app.vtranslate(data.result),
                            animation: 'show',
                            type: "error"
                        };
                        Vtiger_Helper_Js.showPnotify(params);
                    }
            });
        });
    },
    registerEvents: function () {
        this.registerCalendar();
        this.registerChangeResource();
        this.registerChangeResourceValue();
        this.registerAppendActionsButton();
	
	this.registerAddHolidayBlockedButtons();
	this.registerRemoveHolidayBlockedButton();
	this.registerHolidayBlockedSave();
        this.registerBL();
	
    	this.registerRemoveDailyNotesButton();
	this.registerDailyNotesSave();
	this.registerAddDailyNotesButtons();
	this.registerModalEvent();

    //Advanced Filter Events
	this.registerChangeCustomFilterEvent();
	this.changeCustomFilterElementView();



        return this;
    },
});
jQuery(document).ready(function () {
    var instance = OrdersTask_CapacityCalendarView_Js.getInstanceByView();
    instance.registerEvents();
    instance.performCalendarFeedIntiate();
    OrdersTask_CapacityCalendarView_Js.currentInstance = instance;
    instance.fetchOrdersDay(new Date(),"JS_LOADING_CAPACITY_CALENDAR");
});
