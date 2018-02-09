Vtiger_List_Js("OrdersTask_NewLocalDispatchActuals_Js", {
    getPopupParams: function (id) {
        var parameters = {};
        var parameters = {
            'module': "OrdersTask",
            'src_module': "OrdersTask",
            'src_record': id,
            'multi_select': false,
            'popup_type': 'local_dispatch_related',
        }
        return parameters;
    },
    openPopUp: function (id) {
        var thisInstance = this;
        var idTo = jQuery('.select_task:checkbox:checked').val();
        var OrderTaskInstance = new OrdersTask_NewLocalDispatchActuals_Js();
        if (!OrderTaskInstance.isTaskAccepted(idTo)) { //dispatch_status
            return false;
        }



        var params = thisInstance.getPopupParams(idTo);

        // check agentid select exists
        if (jQuery('select[name="agentid"]').length > 0) {
            params['agentId'] = jQuery('select[name="agentid"]').val();
        }

        var popupInstance = Vtiger_Popup_Js.getInstance();
        popupInstance.show(params, function (data) {
            var data = JSON.parse(data);
            for (first in data) {
                thisInstance.copyResources(data[first].info.orderstaskid, idTo);
                break;
            }
        });
    },
}, {
        is_running: false,
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
        //@TODO: Remove this? Are we even using it?
        isTaskAccepted: function (task_id) {
            var allAccepted = true;
            for (i = 0; i < task_id.length; i++) {
                if (jQuery(".dispatch_status[data-orderstaskid='" + task_id[i] + "'] option:selected").text().trim() !== "Accepted") { //dispatch_status
                    allAccepted = false;
                }
            }
            if (!allAccepted) {
                var params = {
                    title: app.vtranslate('JS_ERROR'),
                    text: app.vtranslate('Order task dispatch status must be accepted first.'),
                    animation: 'show',
                    type: 'error'
                };
                Vtiger_Helper_Js.showPnotify(params);
                return false;
            } else {
                return true;
            }
        },
        hideLoadingMessage: function (progressIndicatorElement) {
            progressIndicatorElement.progressIndicator({
                'mode': 'hide'
            });
        },
        loadEmployeesAssigned: function (task_id) {
            instance = this;
            var params = {
                'module': app.getModuleName(),
                'parent': app.getParentModuleName(),
                'mode': 'getEmployeesAssignedToTask',
                'view': 'NewLoadLocalDispatchActuals',
                'task_id': task_id,
            }
            var progressIndicatorElementEmployees = instance.showLoadingMessage('LBL_LOADING_RESOURCES');
            window.progressIndicatorElementEmployees = progressIndicatorElementEmployees;
            AppConnector.request(params).then(function (data) {
                var data = JSON.parse(data);
                if (data.success) {
                    if (jQuery("#collapseUno").hasClass("in")) //if open, then close it (bug after adding html programmatically
                        jQuery('a[href="#collapseUno"]').click();
                    jQuery('.employeesassignedtotask').html(data.result);
                    var progressIndicatorElementEmployees = window.progressIndicatorElementEmployees;
                    instance.hideLoadingMessage(progressIndicatorElementEmployees);
                    if (!jQuery("#collapseUno").hasClass("in")) //if not open, then open it
                        jQuery('a[href="#collapseUno"]').click();
                    jQuery("#collapseUno").css("height", "auto");
                    //jQuery("#collapseUno").find("tr.assignedemployee select.chzn-select").chosen();
                } else {
                    var progressIndicatorElementEmployees = window.progressIndicatorElementEmployees;
                    instance.hideLoadingMessage(progressIndicatorElementEmployees);
                }
                jQuery(".timepicker").timepicker();
                instance.initDatePicker();
                instance.registerDeleteEmployee();
                //OT5304
                instance.registerCheckDates(jQuery('.employeesassignedtotask'));
                jQuery('tr.assignedemployee:not(.defaultassignedemployee) td.timeoff input').change();
            });
        },
        loadCPUs: function (task_id) {
            instance = this;
            var params = {
                'module': app.getModuleName(),
                'parent': app.getParentModuleName(),
                'mode': 'getTaskCPUs',
                'view': 'NewLoadLocalDispatchActuals',
                'task_id': task_id,
            }
            var progressIndicatorElementCPUS = instance.showLoadingMessage('LBL_LOADING_RESOURCES');
            window.progressIndicatorElementCPUS = progressIndicatorElementCPUS;
            AppConnector.request(params).then(function (data) {
                var data = JSON.parse(data);
                if (data.success) {
                    if (jQuery("#collapseDos").hasClass("in")) //if open, then close it (bug after adding html programmatically
                        jQuery('a[href="#collapseDos"]').click();
                        jQuery('.cpus').html(data.result);
                        jQuery('.cpus table.dynamic_table tbody tr td').each(function () {
                            jQuery(this).find("select").css("width", "90%");
                            jQuery(this).find("input").css("width", "90%");
                        });
                    var progressIndicatorElementCPUS = window.progressIndicatorElementCPUS;
                    instance.hideLoadingMessage(progressIndicatorElementCPUS);
                    if (!jQuery("#collapseDos").hasClass("in")) //if not open, then open it
                        jQuery('a[href="#collapseDos"]').click();
                    jQuery("#collapseDos").css("height", "auto");
                    var extraJS = OrdersTask_EditExtraBlock_Js.getInstance();
                    extraJS.registerAddItemButtons();
                } else {
                    var progressIndicatorElementCPUS = window.progressIndicatorElementCPUS;
                    instance.hideLoadingMessage(progressIndicatorElementCPUS);
                }

            });
        },
        loadEquipment: function (task_id) {
            instance = this;
            var params = {
                'module': app.getModuleName(),
                'parent': app.getParentModuleName(),
                'mode': 'getTaskEquipments',
                'view': 'NewLoadLocalDispatchActuals',
                'task_id': task_id,
            }
            var progressIndicatorElementCPUS = instance.showLoadingMessage('LBL_LOADING_RESOURCES');
            window.progressIndicatorElementCPUS = progressIndicatorElementCPUS;
            AppConnector.request(params).then(function (data) {
                var data = JSON.parse(data);
                if (data.success) {
                    if (jQuery("#collapseTres").hasClass("in")) //if open, then close it (bug after adding html programmatically
                        jQuery('a[href="#collapseTres"]').click();
                    jQuery('.equipments').html(data.result);
                    var progressIndicatorElementCPUS = window.progressIndicatorElementCPUS;
                    instance.hideLoadingMessage(progressIndicatorElementCPUS);
                    if (!jQuery("#collapseTres").hasClass("in")) //if not open, then open it
                        jQuery('a[href="#collapseTres"]').click();
                    jQuery("#collapseTres").css("height", "auto");
                    var extraJS = OrdersTask_EditExtraBlock_Js.getInstance();
                    extraJS.registerAddItemButtons();
                } else {
                    var progressIndicatorElementCPUS = window.progressIndicatorElementCPUS;
                    instance.hideLoadingMessage(progressIndicatorElementCPUS);
                }

            });
        },
        //@TODO: We need to uncheck others before check a new one
        registerCheckboxSelection: function () {
            instance = this;
            jQuery(document).on("change", ".select_task", function () {
                //instance.resetResourceTable();
                if (jQuery('.select_task:checkbox:checked').length > 0) {

                    if (jQuery('.select_task:checkbox:checked').length > 1) {
                        jQuery('.select_task').attr('checked', false);
                        jQuery(this).attr('checked', true).change();
                        return false;
                    }

                    var task_id = 0;
                    jQuery(".resources_crew,.resources_equipment").show();
                    task_id = jQuery('.select_task:checkbox:checked').data("id");
                    jQuery(".divDeAbajo").removeClass("hide");
                    instance.loadEmployeesAssigned(task_id);
                    instance.loadCPUs(task_id);
                    instance.loadEquipment(task_id);
                } else {
                    jQuery(".divDeAbajo").addClass("hide");
                }
            });
        },
        refreshView: function () {
            thisInstance = this;
            if (!thisInstance.is_running) {
                thisInstance.is_running = true;
                thisInstance.getListViewRecords({ 'page': '1' }).then(
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
                        thisInstance.is_running = false;
                        jQuery('table.listViewEntriesTable tbody input[name="disp_assigneddate"]').val('');
                    },
                    function (textStatus, errorThrown) {
                    });
            }
        },
        registerDateFilterChange: function () {
            jQuery('#filter_date_from').on('change', function (ev) {
                var d1 = new Date(jQuery('#filter_date_from').val().replace(/-/g, "/")); //safari
                var d2 = new Date(jQuery('#filter_date_to').val().replace(/-/g, "/")); //safari
                if (d1 > d2) {
                    jQuery('#filter_date_to').val(jQuery('#filter_date_from').val());
                    instance.initDatePicker();
                }
                instance.refreshView();
            });

            jQuery('#filter_date_to').on('change', function (ev) {
                var d1 = new Date(jQuery('#filter_date_from').val().replace(/-/g, "/")); //safari
                var d2 = new Date(jQuery('#filter_date_to').val().replace(/-/g, "/")); //safari
                if (d2 < d1) {
                    jQuery('#filter_date_from').val(jQuery('#filter_date_to').val());
                    instance.initDatePicker();
                }
                instance.refreshView();
            });
        },
        getDefaultParams: function () {
            var pageNumber = jQuery('#pageNumber').val();
            var module = app.getModuleName();
            var parent = app.getParentModuleName();
            var orderBy = jQuery('#orderBy').val();
            var sortOrder = jQuery("#sortOrder").val();
            var cvid = this.getCurrentCvId();

            var params = {
                'module': module,
                'parent': parent,
                'page': pageNumber,
                'view': "NewLocalDispatchActuals",
                'viewname': cvid,
                'orderby': orderBy,
                'sortorder': sortOrder,
                'from_date': this.getDBformattedDate(new Date(Date.parse(jQuery("#filter_date_from").val()))),
                'to_date': this.getDBformattedDate(new Date(Date.parse(jQuery("#filter_date_to").val()))),
            }

            params.search_params = JSON.stringify(this.getListSearchParams());

            return params;
        },
        getDBformattedDate: function (date) {
            var mm = date.getMonth() + 1;
            mm = (mm < 10) ? '0' + mm : mm;
            var dd = date.getDate();
            dd = (dd < 10) ? '0' + dd : dd;
            return date.getFullYear() + '-' + mm + '-' + dd;
        },
        initDatePicker: function () {
            app.registerEventForDatePickerFields();
        },
        auxSetHours: function (current_hour, estimated_hours) {
            var d = new Date(),
                s = current_hour,
                parts = s.match(/(\d+)\.(\d+) (\w+)/),
                hours = /am/i.test(parts[3]) ? parseInt(parts[1], 10) : parseInt(parts[1], 10) + 12,
                minutes = parseInt(parts[2], 10);

            d.setHours(hours + estimated_hours);
            d.setMinutes(minutes);

            return d;
        },
        formatAMPM: function (date) {
            var hours = date.getHours();
            var minutes = date.getMinutes();
            var ampm = hours >= 12 ? 'PM' : 'AM';
            hours = hours % 12;
            hours = hours ? hours : 12; // the hour '0' should be '12'
            minutes = minutes < 10 ? '0' + minutes : minutes;
            var strTime = hours + ':' + minutes + ' ' + ampm;

            return strTime;
        },
        registerRowClickEvent: function () {
            var listViewContentDiv = this.getListViewContentContainer();
            listViewContentDiv.on('click', '.listViewEntries', function (e) {
                return;
            });
        },
        registerCreateFilterClickEvent: function () {
            var thisInstance = this;
            jQuery('#createFilter').on('click', function (event) {
                //to close the dropdown
                thisInstance.getFilterSelectElement().data('select2').close();
                var currentElement = jQuery(event.currentTarget);
                var createUrl = currentElement.data('createurl');
                thisInstance.loadFilterView(createUrl);
            });
        },
        loadFilterView: function (url) {
            var thisInstance = this;
            var progressIndicatorElement = jQuery.progressIndicator();
            AppConnector.request(url).then(
                function (data) {
                    app.hideModalWindow();
                    var contents = jQuery(".contentsDiv").html(data);
                    progressIndicatorElement.progressIndicator({ 'mode': 'hide' });
                    Vtiger_CustomView_Js.registerEvents();
                    Vtiger_CustomView_Js.advanceFilterInstance = Vtiger_AdvanceFilter_Js.getInstance(jQuery('.filterContainer', contents));
                },
                function (error, err) {

                }
            );
        },
        registerEditFilterClickEvent: function () {
            var thisInstance = this;
            var listViewFilterBlock = this.getFilterBlock();
            if (listViewFilterBlock != false) {
                listViewFilterBlock.on('mouseup', 'li i.editFilter', function (event) {
                    //to close the dropdown
                    thisInstance.getFilterSelectElement().data('select2').close();
                    var liElement = jQuery(event.currentTarget).closest('.select2-result-selectable');
                    var currentOptionElement = thisInstance.getSelectOptionFromChosenOption(liElement);
                    var editUrl = currentOptionElement.data('editurl');
                    thisInstance.loadFilterView(editUrl);
                    event.stopPropagation();
                });
            }
        },
        registerDateListSearch: function (container) {
            container.find('.dateField').each(function (index, element) {
		if(jQuery(this).attr("name") != "disp_assigneddate"){
		    var dateElement = jQuery(element);

		    if (dateElement.hasClass('notMultipleCalendar')) {
			var customParams = {}
		    } else {
			var customParams = {
			    calendars: 3,
			    mode: 'range',
			    className: 'rangeCalendar',
			    onChange: function (formated) {
				dateElement.val(formated.join(','));
			    }
			}
		    }
		    app.registerEventForDatePickerFields(dateElement, false, customParams);
		}
            });

        },
        registerNoMultipleCalendarChange: function () {
            jQuery(document).on('change', '.notMultipleCalendar', function () {
                var task_id = jQuery(this).closest('tr').data('id');
                var assigned_date = jQuery(this).val();

                var params = {
                    'module': app.getModuleName(),
                    'mode': 'updateAssignedDate',
                    'view': 'NewLoadLocalDispatch',
                    'task_id': task_id,
                    'assigned_date': thisInstance.getDBformattedDate(new Date(Date.parse(assigned_date))),
                }
                var progressIndicatorElement = thisInstance.showLoadingMessage('Updating Task Assigned Date...');
                AppConnector.request(params).then(function (data) {
                    var data = JSON.parse(data);
                    thisInstance.hideLoadingMessage(progressIndicatorElement);
                    if (data.success && data.result.result == "OK") {
                    } else {
                        console.log(data.msg);
                    }
                });
            });
        },
        hoursDifference: function (start, end) {
            var start = new Date("01/01/2017 " + start);
            var end = new Date("01/01/2017 " + end);
            var result = (end.getHours() + (end.getMinutes() / 60)) - (start.getHours() + (start.getMinutes() / 60));
            return result;
        },
        convertTimeformat: function (time) { //am/pm to 24 hour format
            var hours = Number(time.match(/^(\d+)/)[1]);
            var minutes = Number(time.match(/:(\d+)/)[1]);
            var AMPM = time.match(/\s(.*)$/)[1];
            if (AMPM == "PM" && hours < 12) hours = hours + 12;
            if (AMPM == "AM" && hours == 12) hours = hours - 12;
            var sHours = hours.toString();
            var sMinutes = minutes.toString();
            if (hours < 10) sHours = "0" + sHours;
            if (minutes < 10) sMinutes = "0" + sMinutes;

            return (sHours + ":" + sMinutes + ":00");
        },
        //@TODO: We need to validate that all mandatory fields are completed
        registerSaveActuals: function () {
            instance = this;
            jQuery(document).on("click", ".SaveActuals", function () {
                var assignedemployees = new Array();
                var allMandatoryFieldsCompleted = true;
                var progressIndicatorElement = instance.showLoadingMessage('Saving...');
                
                jQuery(".employeesassignedtotask table tbody tr:not(.defaultassignedemployee)").each(function () {
                    if (jQuery(this).find(".select_assigned_employee").prop("checked")) {
                        var aux = new Object();
                        aux.personnelrole = jQuery(this).find(".personnelrole select.prole option:selected").val();
                        if (jQuery(this).find('input[name="personnelID"]').length) {
                            var personnel = jQuery(this).find("#personnelID_display").val();
                            var employee_id = jQuery(this).find('input[name="personnelID"]').val();
                        } else {
                            var personnel = jQuery(this).find(".personnel").text();
                            var employee_id = jQuery(this).find(".select_assigned_employee").data('employeeid');
                        }
                        aux.personnel = personnel;
                        aux.employee_id = employee_id;
                        aux.actualdate = jQuery(this).find(".actualdate").find("input.dateField").val();

                        var start = jQuery(this).find(".actualstarthours input.timepicker").val();
                        var end = jQuery(this).find(".actualendhours input.timepicker").val();

                        start = start.substr(0, start.length - 2) + " " + start.substr(start.length - 2, start.length).toUpperCase();
                        end = end.substr(0, end.length - 2) + " " + end.substr(end.length - 2, end.length).toUpperCase();

                        aux.actualstarthours = start.trim();
                        aux.actualendhours = end.trim();
                        aux.timeoff = jQuery(this).find("td.timeoff input").val();
//                        aux.totalhours = (start.trim() == "" || end.trim() == "") ? "0" : instance.hoursDifference(instance.convertTimeformat(start), instance.convertTimeformat(end));
                        aux.totalhours = jQuery(this).find("td.totalworkedhours input").val();                        
                        aux.delete = (jQuery(this).hasClass('delete') ? 1 : 0);
                        //Check if all mandatory fields are completed
                        var text = "";
                        if (aux.employee_id == "0") {
                            allMandatoryFieldsCompleted = false;
                            text = "Personnel Name";
                        } else if (aux.actualdate == "") {
                            allMandatoryFieldsCompleted = false;
                            text = "Actual Date";
                        } else if (aux.actualstarthours == "") {
                            allMandatoryFieldsCompleted = false;
                            text = "Actual Start Hours";
                        } else if (aux.actualendhours == "") {
                            allMandatoryFieldsCompleted = false;
                            text = "Actual End Hours";
                        } else if (instance.checkHours(aux.actualstarthours, aux.actualendhours) == false) {
                            allMandatoryFieldsCompleted = false;
                            text = "End Hours needs to be greater than Start Hours";
                        }
                        if (!allMandatoryFieldsCompleted) {
                            var params = {
                                title: app.vtranslate("Please Complete Mandatory Field!"),
                                text: text,
                                animation: 'show',
                                type: "error"
                            };
                            Vtiger_Helper_Js.showPnotify(params);
                            instance.hideLoadingMessage(progressIndicatorElement);
                            return false;
                        }
                        assignedemployees.push(aux);
                    }
                });
                if (!allMandatoryFieldsCompleted) {
                    instance.hideLoadingMessage(progressIndicatorElement);
                    return false;
                }
                var cpus = new Array();
                jQuery(".cpus table tbody tr.itemRow").each(function () {
                    var aux = new Object();
                    aux.carton_name = jQuery(this).find('[name^="carton_name"] option:selected').val();
                    aux.cartonqty = jQuery(this).find('[name^="cartonqty"]').val();
                    aux.packingqty = jQuery(this).find('[name^="packingqty"]').val();
                    aux.unpackingqty = jQuery(this).find('[name^="unpackingqty"]').val();
                    var is_deleted = (jQuery(this).find('[name^="itemDelete"]').val() == "deleted") ? "true" : "false";

                    if(is_deleted == 'false'){
                        cpus.push(aux);
                    }
                });

                var equipments = new Array();
                jQuery(".equipments table tbody tr.itemRow").each(function () {
                    var aux = new Object();
                    aux.equipment_name = jQuery(this).find('[name^="equipment_name"]').val();
                    aux.equipmentqty = jQuery(this).find('[name^="equipmentqty"]').val();
                    var is_deleted = (jQuery(this).find('[name^="itemDelete"]').val() == "deleted") ? "true" : "false";

                    if(is_deleted == 'false'){
                        equipments.push(aux);
                    }
                });

                var taskID = jQuery(".select_task:checked").val();
                var params = {
                    'module': app.getModuleName(),
                    'parent': app.getParentModuleName(),
                    'mode': 'saveActuals',
                    'view': 'NewLoadLocalDispatchActuals',
                    'task_id': taskID,
                    'assignedemployees': assignedemployees,
                    'cpus': JSON.stringify(cpus),
                    'equipments': JSON.stringify(equipments),
                }
                AppConnector.request(params).then(function (data) {
                    var data = JSON.parse(data);
                    if(data.success){
                        var params = {
                            title: app.vtranslate("Values Saved correctly"),
                            text: '',
                            animation: 'show',
                            type: "info"
                        };
                        Vtiger_Helper_Js.showPnotify(params);
                        jQuery(".select_task:checked").attr('checked', true).change();
                        instance.hideLoadingMessage(progressIndicatorElement);
                    }else{
                        var params = {
                            title: app.vtranslate("Values Not Correctly Saved"),
                            text: '',
                            animation: 'show',
                            type: "error"
                        };
                        Vtiger_Helper_Js.showPnotify(params);
                        instance.hideLoadingMessage(progressIndicatorElement);
                    }
                });
            });
        },
        findChildNumber: function (val) {
            var childNumber = 0;
            var count = jQuery("table.listViewEntriesTable thead th").length;
            for (var i = 1; i <= count; i++) {
                if (jQuery("table.listViewEntriesTable thead").find("th:nth-child(" + i + ")").find('a[data-columnname="' + val + '"]').length) {
                    childNumber = i;
                    break;
                }
            }
            return childNumber;
        },
        registerAddPersonnel: function () {
            instance = this;
            jQuery(document).on("click", "#addNewPersonnel", function () {
                var taskID = jQuery(".select_task:checked").val();

                var personnelRole = jQuery(".employeesassignedtotask tr.defaultassignedemployee td.personnelrole select").clone();
                var row = jQuery(".defaultassignedemployee").clone().removeClass("defaultassignedemployee").removeClass("hide").addClass("assignedemployee");

                var operationTask = jQuery('tr.listViewEntries[data-id="' + taskID + '"]').find("td:nth-child(" + instance.findChildNumber("operations_task") + ")").text();
                var date = jQuery('tr.listViewEntries[data-id="' + taskID + '"]').find("td:nth-child(" + instance.findChildNumber("disp_assigneddate") + ")").text();

                jQuery(row).find("td.operationtask").text(operationTask);
                jQuery(row).find("td.actualdate input.dateField").val(date);
                jQuery(row).find("td.personnelrole").html(personnelRole);

                jQuery(".employeesassignedtotask table tbody").append(row);
                var container = jQuery(".employeesassignedtotask table tbody tr:last");
                container.find("input.timepicker").timepicker();
                instance.initDatePicker();
                instance.registerDeleteEmployee();
                instance.registerClearReferenceSelectionEvent(container);
                instance.registerAutoCompleteFields(container);
                //OT5304
                instance.registerCheckDates(container);
                container.find('td.timeoff input:last').change();
            });
        },
        registerFieldChange: function () {
            var thisInstance = this;

            jQuery(document).on('focusin', ".disp_actualhours,.actual_of_crew,.actual_of_vehicles,.dispatch_status", function () {
                jQuery(this).data('oldval', jQuery(this).val());
            });

            jQuery(document).on("change", ".disp_actualhours,.actual_of_crew,.actual_of_vehicles,.dispatch_status", function () {
                var thes = jQuery(this);
                var fieldName = jQuery(this).data("fieldname");
                var fieldValue = (jQuery(this).hasClass("dispatch_status")) ? jQuery(this).find("option:selected").text() :  jQuery(this).val();
                var ordersTaskId = jQuery(this).data("orderstaskid");

                var params = {
                    'module': app.getModuleName(),
                    'mode': 'updateOrdersTaskActualsEditableValues',
                    'view': 'NewLoadLocalDispatchActuals',
                    'task_id': ordersTaskId,
                    'fieldName': fieldName,
                    'fieldValue': fieldValue,
                };
		if(Math.sign(fieldValue) === -1){
		    jQuery(this).val("0");
		    return false;
		}
                var progressIndicatorElement = thisInstance.showLoadingMessage('Updating Task Editable Values...');
                AppConnector.request(params).then(function (data) {
                    var data = JSON.parse(data);
                    thisInstance.hideLoadingMessage(progressIndicatorElement);
                    if (data.success && data.result.result == "OK") {
                        var params = {
                            title: app.vtranslate("Values Updated correctly!"),
                            text: '',
                            animation: 'show',
                            type: "success"
                        };
                        Vtiger_Helper_Js.showPnotify(params);
                    } else {
                        if (fieldName !== "dispatch_status") {
                            jQuery(thes).val(jQuery(thes).data("oldval"));
                        } else {
                            jQuery(thes).val(jQuery(thes).data("oldval")).trigger("liszt:updated")
                        }
                        var params = {
                            title: app.vtranslate("Update Failed!"),
                            text: data.result.msg,
                            animation: 'show',
                            type: "error"
                        };
                        Vtiger_Helper_Js.showPnotify(params);
                    }
                });
            });
        },
        /* Start - Functions from Edit.js */
        registerClearReferenceSelectionEvent: function (container) {
            container.find('.clearReferenceSelection').on('click', function (e) {
                var element = jQuery(e.currentTarget);
                var parentTdElement = element.closest('td');
                var fieldNameElement = parentTdElement.find('.sourceField');
                var fieldInfo = fieldNameElement.data('fieldinfo');
                if (fieldInfo != undefined) {
                    if (typeof fieldInfo != 'object') {
                        fieldInfo = JSON.parse(fieldInfo);
                    }
                    var fieldName = fieldInfo.name;
                } else {
                    var fieldName = fieldNameElement.attr('name');
                }
                fieldNameElement.val('');
                parentTdElement.find('[name^="' + fieldName + '_display"]').removeAttr('readonly').val('');
                element.trigger(Vtiger_Edit_Js.referenceDeSelectionEvent);
                fieldNameElement.trigger(Vtiger_Edit_Js.referenceDeSelectionEvent);
                e.preventDefault();
            });
        },
        setReferenceFieldValue: function (container, params) {
            var fieldInfo = container.find('input[class="sourceField"]').data('fieldinfo');
            if (fieldInfo != undefined) {
                if (typeof fieldInfo != 'object') {
                    fieldInfo = JSON.parse(fieldInfo);
                }
                var sourceField = fieldInfo.name;
            } else {
                var sourceField = container.find('input[class="sourceField"]').attr('name');
            }
            var fieldElement = container.find('input[name^="' + sourceField + '"]');
            var sourceFieldDisplay = sourceField + "_display";
            var fieldDisplayElement = container.find('input[name^="' + sourceFieldDisplay + '"]');
            var popupReferenceModule = container.find('input[name^="popupReferenceModule"]').val();

            var selectedName = params.name;
            var id = params.id;

            fieldElement.val(id);
            fieldDisplayElement.val(selectedName).attr('readonly', true);

            fieldElement.trigger(Vtiger_Edit_Js.referenceSelectionEvent, { 'source_module': popupReferenceModule, 'record': id, 'selectedName': selectedName });
            fieldDisplayElement.validationEngine('closePrompt', fieldDisplayElement);
        },
        getPopUpParams: function (container) {
            var params = {};
            var sourceModule = app.getModuleName();
            var popupReferenceModule = jQuery('input[name="popupReferenceModule"]', container).val();
            var sourceFieldElement = jQuery('input[class="sourceField"]', container);
            var sourceField = sourceFieldElement.attr('name');
            var sourceRecordElement = jQuery('table.listViewEntriesTable .select_task:checked');
            var sourceRecordId = '';
            if (sourceRecordElement.length > 0) {
                sourceRecordId = sourceRecordElement.val();
            }
            var roleId = container.closest('tr').find('select.prole option:selected').val();

            var isMultiple = false;
            if (sourceFieldElement.data('multiple') == true) {
                isMultiple = true;
            }

            var params = {
                'module': popupReferenceModule,
                'src_module': sourceModule,
                'src_field': sourceField,
                'src_record': sourceRecordId,
                'roleId': roleId
            };

            if (isMultiple) {
                params.multi_select = true;
            }
            return params;
        },
        openPopUp: function (e) {
            var thisInstance = this;
            var parentElem = jQuery(e.target).closest('td');

            var params = this.getPopUpParams(parentElem);
            if (params === false) {
                return;
            }

            var isMultiple = false;
            if (params.multi_select) {
                isMultiple = true;
            }

            // check agentid select exists
            if (jQuery('select[name="agentid"]').length > 0) {
                params['agentId'] = jQuery('select[name="agentid"]').val();
            }

            var sourceFieldElement = jQuery('input[class="sourceField"]', parentElem);

            var prePopupOpenEvent = jQuery.Event(Vtiger_Edit_Js.preReferencePopUpOpenEvent);
            sourceFieldElement.trigger(prePopupOpenEvent);

            if (prePopupOpenEvent.isDefaultPrevented()) {
                return;
            }

            var popupInstance = Vtiger_Popup_Js.getInstance();
            popupInstance.show(params, function (data) {
                var responseData = JSON.parse(data);
                var dataList = [];
                for (var id in responseData) {
                    var data = {
                        'name': responseData[id].name,
                        'id': id
                    };
                    dataList.push(data);
                    if (!isMultiple) {
                        thisInstance.setReferenceFieldValue(parentElem, data);
                    }
                }

                if (isMultiple) {
                    sourceFieldElement.trigger(Vtiger_Edit_Js.refrenceMultiSelectionEvent, { 'data': dataList });
                }
                sourceFieldElement.trigger(Vtiger_Edit_Js.postReferenceSelectionEvent, { 'data': responseData });
            });
        },
        referenceModulePopupRegisterEvent: function () {
            var thisInstance = this;
            jQuery(document).off("click", '.relatedPopup').on("click", '.relatedPopup', function (e) {
                thisInstance.openPopUp(e);
            });

            jQuery(document).find('.referenceModulesList').chosen().change(function (e) {
                var element = jQuery(e.currentTarget);
                var closestTD = element.closest('td').next();
                var popupReferenceModule = element.val();
                var referenceModuleElement = jQuery('input[name^="popupReferenceModule"]', closestTD);
                var prevSelectedReferenceModule = referenceModuleElement.val();
                referenceModuleElement.val(popupReferenceModule);

                //If Reference module is changed then we should clear the previous value
                if (prevSelectedReferenceModule != popupReferenceModule) {
                    closestTD.find('.clearReferenceSelection').trigger('click');
                }
            });
        },
        getReferencedModuleName : function(parenElement){
		return jQuery('input[name="popupReferenceModule"]',parenElement).val();
	},
        /**
	 * Function to get reference search params
	 */
	getReferenceSearchParams : function(element){
		var tdElement = jQuery(element).closest('td');
		var params = {};
		var searchModule = this.getReferencedModuleName(tdElement);
		params.search_module = searchModule;
                var sourceRecordElement = jQuery('table.listViewEntriesTable .select_task:checked');
                var sourceRecordId = '';
                if (sourceRecordElement.length > 0) {
                    params.recordId = sourceRecordElement.val();
                }
                params.roleId = tdElement.closest('tr').find('select.prole option:selected').val();
		return params;
	},

	/**
	 * Function which will handle the reference auto complete event registrations
	 * @params - container <jQuery> - element in which auto complete fields needs to be searched
	 */
	registerAutoCompleteFields : function(container) {
		var thisInstance = this;
        var autoCompleteOptions = {
            'minLength' : '3',
            'source' : function(request, response){
                //element will be array of dom elements
                //here this refers to auto complete instance
                var inputElement = jQuery(this.element[0]);
                var searchValue = request.term;
                var params = thisInstance.getReferenceSearchParams(inputElement);
                params.search_value = searchValue;
                params.field_name = inputElement.attr('name');
                thisInstance.searchModuleNames(params).then(function(data){
                    var reponseDataList = [];
                    var serverDataFormat = data.result;
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
				if(app.getModuleName() == 'Workflows') {
				    tdElement = element.closest('.conditionRow');
                }
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
        };
		$(document).on('keydown.autoComplete', '.autoComplete', function() {
			$(this).autocomplete(autoCompleteOptions);
		});
		container.find('input.autoComplete').autocomplete(autoCompleteOptions);
	},
        searchModuleNames : function(params) {
		var aDeferred = jQuery.Deferred();

		if(typeof params.module == 'undefined') {
			params.module = app.getModuleName();
		}

		if(typeof params.action == 'undefined') {
			params.action = 'BasicAjax';
		}

		// check agentid select exists
		if (jQuery('select[name="agentid"]').length > 0) {
			params.agentId = jQuery('select[name="agentid"]').val();
		}

		AppConnector.request(params).then(
			function(data){
				aDeferred.resolve(data);
			},
			function(error){
				//TODO : Handle error
				aDeferred.reject();
			}
			);
		return aDeferred.promise();
	},
        /* End - Functions from Edit.js */
        registerHideHeaderFilter: function () {
            jQuery('#s2id_customFilter').addClass('hide');
        },
        registerDeleteEmployee: function () {
            jQuery('a.deleteEmployeeButton').on('click', function () {
                var element = jQuery(this);
                var employeeId = element.data('employeeid');
                if (employeeId) {
                    element.closest('tr').addClass('hide').addClass('delete');
                } else {
                    element.closest('tr').remove();
                }
            });
        },
        registerHoursChange: function () {
            var thisInstance = this;
            //al timepicker input
            jQuery(document).on('change', '.timepicker', function () {

                var tr = jQuery(this).closest('tr');
                var start = tr.find('td.actualstarthours input').val();
                var end = tr.find('td.actualendhours input').val();
                start = start.substr(0, start.length - 2) + " " + start.substr(start.length - 2, start.length).toUpperCase();
                end = end.substr(0, end.length - 2) + " " + end.substr(end.length - 2, end.length).toUpperCase();
                start = start.trim();
                end = end.trim();

                var result = thisInstance.checkHours(start, end);

            });
        },
        checkHours: function (start, end) {
            var thisInstance = this;
            if (start == '' && end != '') {
                var params = {
                    title: app.vtranslate('JS_MESSAGE'),
                    text: app.vtranslate('Please complete Start Hours.'),
                    animation: 'show',
                    type: 'error'
                };
                Vtiger_Helper_Js.showPnotify(params);

                return false;
            }

            if (start != '' && end == '') {
                var params = {
                    title: app.vtranslate('JS_MESSAGE'),
                    text: app.vtranslate('Please complete End Hours.'),
                    animation: 'show',
                    type: 'error'
                };
                Vtiger_Helper_Js.showPnotify(params);

                return false;
            }

            if (start != '' && end != '' && thisInstance.getTwentyFourHourTime(start) > thisInstance.getTwentyFourHourTime(end)) {
                var params = {
                    title: app.vtranslate('JS_MESSAGE'),
                    text: app.vtranslate('End Hours needs to be greater than Start Hours'),
                    animation: 'show',
                    type: 'error',
                };
                Vtiger_Helper_Js.showPnotify(params);

                return false;
            }

        },
        getTwentyFourHourTime: function (amPmString) {
            var d = new Date("1/1/2013 " + amPmString);
            return d;

        },
        registerCancelButton: function () {
            var thisInstance = this;
            jQuery("#cancelLink").click(function () {
                jQuery('.select_task:checkbox:checked').prop("checked", false);
                jQuery(".select_task:first").change(); //cambio cualquiera para que tire el evento y cierre lo demas 
            });
        },
        registerNoNegativeValues: function () {
            jQuery(document).on("change", "input[name='cartonqty'],input[name='packingqty'],input[name='unpackingqty']", function (e) {
                if(jQuery(this).val() < 0){
                    jQuery(this).val(0);
                    var params = {
                    title: app.vtranslate('JS_MESSAGE'),
                    text: app.vtranslate('No negative numbers allowed.'),
                    animation: 'show',
                    type: 'info'
                };
                Vtiger_Helper_Js.showPnotify(params);
                }
            });
        },
        registerHideDispatchStatusPicklistOptions: function () {
            var thisInstance = this;
            var allowedOptions = ['Actuals Entered','Completed'];
            jQuery('select[data-fieldname="dispatch_status"]').each(function () {
                jQuery(this).find('option').each(function () {
                    if( jQuery.inArray(jQuery(this).html().trim(),allowedOptions ) === -1 ){
                        jQuery(this).remove();
                    }
                });
                jQuery(this).trigger("liszt:updated");
            });
            jQuery('select[name="dispatch_status"]').find('option').each(function () {
                    if( jQuery.inArray(jQuery(this).html().trim(),allowedOptions ) === -1 ){
                        jQuery(this).remove();
                    }
                    jQuery(this).attr('selected',false);
                });
                jQuery(this).trigger("liszt:updated");
            
        },
        registerCheckDates: function (container) {
            var thisInstance = this;
        container.on('change', 'input.timepicker,td.timeoff input', function () {
            var tr = jQuery(this).closest('tr');
            var startDate = tr.find('.dateField').val();
            var startHour = tr.find('td.actualstarthours input').val();
            var endHour = tr.find('td.actualendhours input').val();
            var timeOff = parseFloat(tr.find('td.timeoff input').val());
            if(timeOff < 0){
                alert(app.vtranslate('JS_TIMEOFF_NEGATIVE'));
                tr.find('td.timeoff input').val(0).change();
                return false;
            }
            
            if (startDate != '' && startHour != '' && endHour != '') {

                endHour = thisInstance.convertHours(endHour);
                startHour = thisInstance.convertHours(startHour);
                var workedHours = endHour - startHour;
                var finalValue = Math.round((workedHours / 3600000)*100)/100;
                if (finalValue < 0) {
                    alert(app.vtranslate('JS_HOURS_NEGATIVE'));
                    return false;
                }
                if( ! isNaN(timeOff) ){
                    finalValue = Math.round( (finalValue - timeOff) *100) / 100;
                }
                if (finalValue < 0) {
                    alert(app.vtranslate('JS_HOURS_TIMEOFF_NEGATIVE'));
                    tr.find('td.timeoff input').val(0).change();
                    return false;
                } else {
                    tr.find('td.totalworkedhours input').val(finalValue);

                }

            }

        });
    },
    convertHours: function (time) {
        var hours = Number(time.match(/^(\d+)/)[1]);
        var minutes = Number(time.match(/:(\d+)/)[1]);
        var AMPM = time.match(/:..(\w+)/)[1];
        if (AMPM == "pm" && hours < 12)
            hours = hours + 12;
        if (AMPM == "am" && hours == 12)
            hours = hours - 12;
        var sHours = hours.toString();
        var sMinutes = minutes.toString();
        if (hours < 10)
            sHours = "0" + sHours;
        if (minutes < 10)
            sMinutes = "0" + sMinutes;
        var dat = new Date;
        dat.setHours(sHours);
        dat.setMinutes(sMinutes);
        dat.setSeconds(0);

        return dat;
    },
        registerEvents: function () {
            this.registerDateFilterChange();
            this.initDatePicker();
            this.registerCheckboxSelection();
            this.registerNoMultipleCalendarChange();
            this.registerSaveActuals();
            this.registerAddPersonnel();
            this.registerFieldChange();
            this.referenceModulePopupRegisterEvent();
            this._super();
            this.registerHideHeaderFilter();
            this.registerHoursChange();
            this.registerCancelButton();
            jQuery('table.listViewEntriesTable tbody input[name="disp_assigneddate"]').val('');
            this.registerHideDispatchStatusPicklistOptions();
            this.registerNoNegativeValues();
        }
    });

     app.listenPostAjaxReady(function() {
         var instance = new OrdersTask_NewLocalDispatchActuals_Js;
         jQuery('table.listViewEntriesTable tbody input[name="disp_assigneddate"]').val('');
         
         instance.registerHideDispatchStatusPicklistOptions();
     });