Vtiger_List_Js("Orders_LDDList_Js", {
    addToTrip: function (e) {
        thisInstance = this;
        var orderstask = new Array();
        jQuery('.listViewEntriesCheckBox:checkbox:checked').each(function () {
            if (!jQuery(this).closest("tr").find(".on-hold").prop("checked"))
                orderstask.push(jQuery(this).val());
        });
        if (orderstask.length == 0) {
            jQuery('.asoc:checkbox:checked').each(function () {
                jQuery(this).prop("checked", false);
            });
            return false;
        }
        orderstask = orderstask.join(",");
        params = {
            'module': 'Orders',
            'view': 'ShowModals',
            'mode': 'showAdd2TripModal',
            'sourcemodule': app.getModuleName(),
            'orderslist': orderstask,
        }
        AppConnector.request(params).then(
                function (data) {
                    app.showModalWindow(data, function (data) {
                        var LDD = new Orders_LDDList_Js();
                        LDD.addToTripSave();
                    });
                },
                function (jqXHR, textStatus, errorThrown) {
                }
        );
    },
    createTrip: function (e) {
        thisInstance = this;
        var orders = new Array();
        jQuery('.listViewEntriesCheckBox:checkbox:checked').each(function () {
            if (!jQuery(this).closest("tr").find(".on-hold").prop("checked"))
                orders.push(jQuery(this).val());
        });

        if (orders.length == 0) {
            alert('Please choose at least one order');
            return false;
        }

        window.location.href = 'index.php?module=Trips&view=Edit&related_module=Orders&calledby=ldd&related_record_list='  + orders.join(",");

    },
    refreshView: function () {
        thisInstance = new Orders_LDDList_Js;
        thisInstance.getListViewRecords({'page': '1'}).then(
                function (data) {
                    //To unmark the all the selected ids
                    jQuery('#deSelectAllMsg').trigger('click');

                    jQuery('#recordsCount').val('');
                    //To Set the page number as first page
                    jQuery('#pageNumber').val('1');
                    jQuery('#pageToJump').val('1');
                    jQuery('#totalPageCount').text("");
                    thisInstance.calculatePages().then(function () {
                        thisInstance.updatePagination();
                    });
                },
                function (textStatus, errorThrown) {
                }
        );
    }
}, {
    weekDaysArray: {Sunday: 0, Monday: 1, Tuesday: 2, Wednesday: 3, Thursday: 4, Friday: 5, Saturday: 6},
    getDefaultParams: function () {
        var pageNumber = jQuery('#pageNumber').val();
        var module = app.getModuleName();
        var parent = app.getParentModuleName();
        var cvId = this.getCurrentCvId();
        var orderBy = jQuery('#orderBy').val();
        var sortOrder = jQuery("#sortOrder").val();
        var params = {
            'module': module,
            'parent': parent,
            'page': pageNumber,
            'view': "LDDList",
            'viewname': cvId,
            'orderby': orderBy,
            'sortorder': sortOrder
        }

        var searchValue = this.getAlphabetSearchValue();

        if ((typeof searchValue != "undefined") && (searchValue.length > 0)) {
            params['search_key'] = this.getAlphabetSearchField();
            params['search_value'] = searchValue;
            params['operator'] = "s";
        }
        params.search_params = JSON.stringify(this.getListSearchParams());
        return params;
    },
    showLoadingMessage: function (message) {
        var loadingMessage = app.vtranslate(message);
        var progressIndicatorElement = jQuery.progressIndicator({
            'message': loadingMessage,
            'position': 'html',
            'blockInfo': {
                'enabled': true
            }
        });

        return progressIndicatorElement;

    },
    hideLoadingMessage: function (progressIndicatorElement) {
        progressIndicatorElement.progressIndicator({
            'mode': 'hide'
        })
    },
    registerOnHold: function () {
        var instance = this;
        jQuery(document).on('click', '.on-hold', function () {
            var ordersid = $(this).data("id");
            var progressIndicator = instance.showLoadingMessage('JS_UPDATING_ORDER');

            var module = app.getModuleName();

            if (jQuery(this).prop('checked')) {
                var onhold = 'yes';
            } else {
                var onhold = 'no';
            }

            var checkBox = jQuery(this);

            var urlParams = {
                'module': module,
                'view': "LDDActions",
                'ordersid': ordersid,
                'mode': 'updateOnHoldStatus',
                'on-hold': onhold
            }

            AppConnector.request(urlParams).then(function (data) {
                progressIndicator.progressIndicator({'mode': 'hide'});
                data.replace(/^\s+|\s+$/g, '');
                data = JSON.parse(data);


                if (data.result.r = 'true') {
                    var params = {
                        title: app.vtranslate('Success'),
                        type: 'success',
                        text: app.vtranslate('Order Updated'),
                        width: '35%'
                    };
                    Vtiger_Helper_Js.showPnotify(params);
                    if (onhold == 'yes') {
                        checkBox.parent().parent().find('.asoc').prop('disabled', 'disabled');
                    } else {
                        checkBox.parent().parent().find('.asoc').prop("disabled", false);
                    }

                } else {
                    jQuery(this).prop('checked', false);
                    alert('Error settings this order on-hold');
                }
            });
        });
    },
    addDatePickers: function () {
        app.registerEventForDatePickerFields(jQuery('.dateField'), true);
    },
    registerAPU: function () {
        var thisInstance = this;

        jQuery(document).on('click', '.apu', function () {

            if (jQuery(this).prop('checked')) {
                jQuery(this).parent().parent().find('.orderstask_apudate').attr('disabled', false);
                thisInstance.initDatePickers();
            } else {
                var ordersid = $(this).data("id");
                thisInstance.updateAPU(ordersid, 'no', '');
            }

        });

    jQuery(document).on('change', '.orderstask_apudate', function () {
            if (jQuery(this).parent().parent().parent().parent().find('.apu').prop('checked')) {
                var ordersid = $(this).data("id");

                if (jQuery(this).parent().parent().parent().parent().find('.apu').prop('checked')) {
                    var apu = 'yes';
                } else {
                    var apu = 'no';
                }

                var apu_date = jQuery(this).val();
                thisInstance.updateAPU(ordersid, apu, apu_date);
            }
            ;
        });
    },

    updateAPU: function (ordersid, apu, apu_date) {
        var thisInstance = this;
        var module = app.getModuleName();
        var urlParams = {
            'module': module,
            'view': "LDDActions",
            'ordersid': ordersid,
            'mode': 'updateApuStatus',
            'apu': apu,
            'apu_date': apu_date
        }
        var progressIndicator = thisInstance.showLoadingMessage('JS_UPDATING_ORDER');
        AppConnector.request(urlParams).then(function (data) {
            progressIndicator.progressIndicator({'mode': 'hide'});
            data = JSON.parse(data);

            if (data.result.created == 'true') {
                var params = {
                    title: app.vtranslate('Success'),
                    type: 'success',
                    text: app.vtranslate('Order Updated'),
                    width: '35%'
                };
                Vtiger_Helper_Js.showPnotify(params);
                jQuery('#' + ordersid).find('.orderstask_apudate').attr('readonly', 'readonly').prop('disabled', true);

            } else {
                var params = {
                    title: app.vtranslate('Error'),
                    type: 'error',
                    text: data.result.msg,
                    width: '35%'
                };
                Vtiger_Helper_Js.showPnotify(params);
                jQuery('#' + ordersid).find('.apu').prop('checked', false);
                jQuery('#' + ordersid).find('.orderstask_apudate').val('');

            }
        });
    },
    createOverflow: function () {
        jQuery('.overflow').on('click', function (e) {
            e.stopImmediatePropagation();
            e.preventDefault();
            var message = app.vtranslate('Overflow Creation. Duplicate the existing order to create a new overflow?');
            var order_id = jQuery(this).attr('id');
            Vtiger_Helper_Js.showConfirmationBox({'message': message}).then(
                    function (e) {
                        var urlParams = {
                            module: 'Orders',
                            view: 'LDDActions',
                            mode: 'createOverflow',
                            order_id: order_id,
                        }
                        AppConnector.request(urlParams).then(function (data) {
                            data = JSON.parse(data);
                            if (data.result.created == 'true') {
                                var params = {
                                    title: app.vtranslate('Success'),
                                    type: 'success',
                                    text: app.vtranslate('New Order created'),
                                    width: '35%'
                                };
                                Vtiger_Helper_Js.showPnotify(params);

                            } else {
                                var params = {
                                    title: app.vtranslate('Error'),
                                    type: 'error',
                                    text: data.result.msg,
                                    width: '35%'
                                };
                                Vtiger_Helper_Js.showPnotify(params);
                            }
                        }
                        );
                    },
                    function (error, err) {
                    }
            );
        });

    },
    initDatePickers: function () {
        var element = jQuery('.dateFieldApu');
        var registerForAddon = true;

        if (registerForAddon == true) {
            var parentDateElem = element.closest('.date');
            jQuery('.add-on', parentDateElem).on('click', function (e) {
                var elem = jQuery(e.currentTarget);
                elem.closest('.date').find('input.dateFieldApu').get(0).focus();
            });
        }
        var dateFormat = element.data('dateFormat');
        var vtigerDateFormat = app.convertToDatePickerFormat(dateFormat);
        var language = jQuery('body').data('language');
        var lang = language.split('_');

        //Default first day of the week
        var defaultFirstDay = jQuery('#start_day').val();
        if (defaultFirstDay == '' || typeof (defaultFirstDay) == 'undefined') {
            var convertedFirstDay = 1
        } else {
            convertedFirstDay = this.weekDaysArray[defaultFirstDay];
        }
        var params = {
            format: vtigerDateFormat,
            calendars: 1,
            locale: $.fn.datepicker.dates[lang[0]],
            starts: convertedFirstDay,
            eventName: 'focus',
            onChange: function (formated) {
                var element = jQuery(this).data('datepicker').el;
                element = jQuery(element);
                var datePicker = jQuery('#' + jQuery(this).data('datepicker').id);
                var viewDaysElement = datePicker.find('table.datepickerViewDays');
                //If it is in day mode and the prev value is not eqaul to current value
                //Second condition is manily useful in places where user navigates to other month
                if (viewDaysElement.length > 0 && element.val() != formated) {
                    element.DatePickerHide();
                    element.blur();
                }
                element.val(formated).trigger('change').focusout();
            }
        }
        if (typeof customParams != 'undefined') {
            var params = jQuery.extend(params, customParams);
        }
        element.each(function (index, domElement) {
            var jQelement = jQuery(domElement);
            var dateObj = new Date();
            var selectedDate = app.getDateInVtigerFormat(dateFormat, dateObj);
            //Take the element value as current date or current date
            if (jQelement.val() != '') {
                selectedDate = jQelement.val();
            }
            params.date = selectedDate;
            params.current = selectedDate;
            jQelement.DatePicker(params)
        });
    },
    registerRowClickEvent: function () {
        return false;
    },
    registerLDDateListSearch: function () {
        var container = jQuery('.ListViewLDDActions');
        container.find('.dateField').each(function (index, element) {
            var dateElement = jQuery(element);
            var customParams = {
                calendars: 3,
                mode: 'range',
                className: 'rangeCalendar',
                onChange: function (formated) {
                    dateElement.val(formated.join(','));
                }
            }
            app.registerEventForDatePickerFields(dateElement, false, customParams);
        });

    },
    registerDateFilterChange: function () {
        thisInstance = this;
        jQuery(document).on('click', '#ldd_filter', function (e) {
	    Orders_LDDList_Js.refreshView();
        });

    },
    resetCustomLVFilter: function () {
        thisInstance = this;
        jQuery(document).on('click', '#ldd_clear_filter', function (e) {
            jQuery('#origin_zone').val('').trigger("liszt:updated");
            jQuery('#destination_zone').val('').trigger("liszt:updated");
            jQuery('#filter_dates').val('');
            jQuery("input[name='orders_ldate']").val('');

            Orders_LDDList_Js.refreshView();
        });
    },
    registerChangeCustomFilterEvent: function () {
        var thisInstance = this;
        var filterSelectElement = this.getFilterSelectElement();
        filterSelectElement.change(function (e) {
            jQuery('#pageNumber').val("1");
            jQuery('#pageToJump').val('1');
            jQuery('#orderBy').val('');
            jQuery("#sortOrder").val('');
            jQuery('#origin_zone').val('').trigger("liszt:updated");
            jQuery('#destination_zone').val('').trigger("liszt:updated");
            jQuery('#filter_dates').val('');
            var cvId = thisInstance.getCurrentCvId();
            selectedIds = new Array();
            excludedIds = new Array();

            var urlParams = {
                "viewname": cvId,
                //to make alphabetic search empty
                "search_key": thisInstance.getAlphabetSearchField(),
                "search_value": "",
                "search_params": ""
            }
            //Make the select all count as empty
            jQuery('#recordsCount').val('');
            //Make total number of pages as empty
            jQuery('#totalPageCount').text("");
            thisInstance.getListViewRecords(urlParams).then(function () {
                thisInstance.ListViewPostOperation();
                thisInstance.updatePagination();
            });
        });
    },
    getListSearchParams: function () {
        var listViewPageDiv = this.getListViewContainer();
        var listViewTable = listViewPageDiv.find('.listViewEntriesTable');
        var searchParams = new Array();
        listViewTable.find('.listSearchContributor').each(function (index, domElement) {
            var searchInfo = new Array();
            var searchContributorElement = jQuery(domElement);
            var fieldInfo = searchContributorElement.data('fieldinfo');
            var fieldName = searchContributorElement.attr('name');

            var searchValue = searchContributorElement.val();

            if (typeof searchValue == "object") {
                if (searchValue == null) {
                    searchValue = "";
                } else {
                    searchValue = searchValue.join(',');
                }
            }
            searchValue = searchValue.trim();
            if (searchValue.length <= 0) {
                //continue
                return true;
            }
            var searchOperator = 'c';
            if (fieldInfo.type == "date" || fieldInfo.type == "datetime") {
                searchOperator = 'bw';
            } else if (fieldInfo.type == 'percentage' || fieldInfo.type == "double" || fieldInfo.type == "integer"
                    || fieldInfo.type == 'currency' || fieldInfo.type == "number" || fieldInfo.type == "boolean" ||
                    fieldInfo.type == "picklist") {
                searchOperator = 'e';
            }
            searchInfo.push(fieldName);
            searchInfo.push(searchOperator);
            searchInfo.push(searchValue);
            searchParams.push(searchInfo);
        });

        //add the custom fields

        if (jQuery('#origin_zone').val() != '' && jQuery('#origin_zone').val() != 'all' && jQuery('#origin_zone').val() !== null) {
            var searchInfo = new Array();
            searchInfo.push('origin_zone');
            searchInfo.push('e');
            searchInfo.push(jQuery('#origin_zone').val());
            searchParams.push(searchInfo);
        }

        if (jQuery('#destination_zone').val() != '' && jQuery('#destination_zone').val() != 'all' && jQuery('#destination_zone').val() !== null) {
            var searchInfo = new Array();
            searchInfo.push('empty_zone');
            searchInfo.push('e');
            searchInfo.push(jQuery('#destination_zone').val());
            searchParams.push(searchInfo);
        }

        if (jQuery('#filter_dates').val() != '') {
            var searchInfo = new Array();
            searchInfo.push('orders_ldate');
            searchInfo.push('bw');
            searchInfo.push(jQuery('#filter_dates').val());
            searchParams.push(searchInfo);
        }


        return new Array(searchParams);
    },
    updateFiltersDates: function () {
        thisInstance = this;
        jQuery(document).on('change', '#filter_dates', function (e) {
            jQuery("input[name='orders_ldate']").val(jQuery(this).val());
        });
    },
    registerAddToTrip: function () {
        thisInstance = this;
        jQuery('#addtotrip').click(function () {
            if (jQuery('.asoc:checkbox:checked').length > 0) {
                thisInstance.addToTrip();
            } else {
                alert('Please select some Local Operations Task before.');
            }
        });
    },
    addToTripSave: function () {
        instance = this;
        jQuery('[name^="check_"]').change(function () {
            var name = jQuery(this).attr("name");
            if (jQuery(this).prop("checked")) {
                jQuery('[name^="check_"]').each(function () {
                    if (jQuery(this).attr("name") !== name) {
                        jQuery(this).prop("checked", false);
                    }
                });
            }
        });
        jQuery('[name="saveButton"]').click(function () {
            var orders = jQuery("#orders").val().split(',');
            var tripid = jQuery('[name^="check_"]:checked').attr('name').replace('check_', '');
            var progressIndicator = instance.showLoadingMessage('JS_UPDATING_ORDER');
            params = {
                'module': 'Trips',
                'action': 'RelationAjax',
                'mode': 'addRelation',
                'calledby': 'ldd',
                'src_record': tripid,
                'related_module': 'Orders',
                'related_record_list': orders,
            }
            AppConnector.request(params).then(
                    function (data) {

                        app.hideModalWindow();
                        var params = {
                            title: app.vtranslate('Success'),
                            text: app.vtranslate('Order Added to Trip'),
                            type: 'success',
                            width: '35%'
                        };
                        Vtiger_Helper_Js.showPnotify(params);
                        progressIndicator.progressIndicator({'mode': 'hide'});

                        Orders_LDDList_Js.refreshView();

                    },
                    function (jqXHR, textStatus, errorThrown) {
                    }
            );
        });
    },
    registerTableFilter: function () {
        jQuery(document).on('click', '#filtrar_tabla', function () {
            jQuery('#modaltable tbody tr.listViewEntries').each(function () {
                jQuery(this).removeClass('not_visible').addClass('visible');
            });
            var filter_tripid = jQuery('#filtro_tripid').val().toLowerCase();
            var filter_drivername = jQuery('#filtro_drivername').val().toLowerCase();
            var filter_agentname = jQuery('#filtro_agentname').val().toLowerCase();
            var filter_agentnumber = jQuery('#filtro_agentnumber').val().toLowerCase();
            var filter_emptydate = jQuery('#filtro_emptydate').val();
            var filter_emptyzone = jQuery('#filtro_emptyzone').val().toLowerCase();
            if (filter_tripid || filter_drivername || filter_agentname || filter_agentnumber || filter_emptydate || filtro_emptyzone) {
                jQuery('#modaltable tbody tr.listViewEntries').each(function () {
                    var check = true;
                    if (filter_tripid) {
                        var value = jQuery(this).find('td:nth-child(2)').text().toLowerCase();
                        if (value.indexOf(filter_tripid) === -1) {
                            check = false;
                        }
                    }
                    if (filter_drivername) {
                        var value = jQuery(this).find('td:nth-child(13)').text().toLowerCase();
                        if (value.indexOf(filter_drivername) === -1) {
                            check = false;
                        }
                    }
                    if (filter_agentname) {
                        var value = jQuery(this).find('td:nth-child(10)').text().toLowerCase();
                        if (value.indexOf(filter_agentname) === -1) {
                            check = false;
                        }
                    }
                    if (filter_agentnumber) {
                        var value = jQuery(this).find('td:nth-child(9)').text();
                        if (value.indexOf(filter_agentnumber) === -1) {
                            check = false;
                        }
                    }
                    if (filter_emptyzone) {
                        var value = jQuery(this).find('td:nth-child(6)').text().toLowerCase();
                        if (value.indexOf(filter_emptyzone) === -1) {
                            check = false;
                        }
                    }
                    if (filter_emptydate) {
                        var value = jQuery(this).find('td:nth-child(8)').text();
                        var d1 = new Date(filter_emptydate);
                        var d2 = new Date(value);
                        if (d1.getTime() !== d2.getTime())
                            check = false;
                    }
                    if (!check)
                        jQuery(this).removeClass('visible').addClass('not_visible');
                });
            } else {
                jQuery('#modaltable tbody tr.listViewEntries').each(function () {
                    jQuery(this).removeClass('not_visible').addClass('visible');
                });
            }
        });
    },
    clearAddToTripFilter: function () {
        jQuery(document).on('click', '#clear_addtotrip_filter', function () {
            jQuery('#modaltable tbody tr.listViewEntries').each(function () {
                jQuery(this).removeClass('not_visible').addClass('visible');
            });
        });
    },
    registerEvents: function () {
        this.registerOnHold(),
        this.registerAPU(),
        this.createOverflow(),
        this.initDatePickers();
        this.registerLDDateListSearch();
        this.registerDateFilterChange();
        this.updateFiltersDates();
        this.registerAddToTrip();
        this.registerTableFilter();
        this.clearAddToTripFilter();
        this.resetCustomLVFilter();
        this._super();
    }
});
