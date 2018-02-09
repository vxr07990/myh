
jQuery.Class("LocalDispatch_Js", {}, {
    loadingResources: {},
    ViewGanttChart: function () {

        gantt.config.columns = [
            {name: "text", label: "Task name", width: "*", tree: true},
            {name: "start_date", label: "Start time", align: "center", width: 70},
        ];


        var current_date = jQuery('#selected_date_input').val();
        var date_array = current_date.split('-');
        var year = date_array[0];
        var month = date_array[1] - 1;
        var day = date_array[2];

        var date = new Date(year, month, day, 7, 0, 0);
        var days = parseInt(jQuery('#days').val());
        if (isNaN(days)) {
            days = 0;
        }
        var end_date = new Date(date.setDate(date.getDate() + days));
        end_date.setHours(21);

        gantt.config.start_date = new Date(year, month, day, 7, 0, 0);
        gantt.config.end_date = end_date;

        gantt.config.sort = true;

        gantt.config.date_grid = "%H:%i";
        gantt.config.xml_date = "%Y-%m-%d %H:%i";

        var days = jQuery('#days').val();

        if (isNaN(days) || days == '1' || days == '') {
            gantt.config.scale_unit = "hour";
            gantt.config.duration_unit = "minute";
            gantt.config.date_scale = "%H:%i";
            gantt.config.drag_resize = true;
            gantt.config.duration_unit = "hour";
            gantt.config.duration_step = 1;
            gantt.config.time_step = 1;
            gantt.config.min_duration = 1000; // 1000ms

        } else if (days == '7') {
            gantt.config.scale_unit = "day";
            gantt.config.duration_unit = "hour";
            gantt.config.date_scale = "%m-%d";
            gantt.config.drag_resize = false;


        } else {

            gantt.config.scale_unit = "week";
            gantt.config.duration_unit = "hour";
            gantt.config.date_scale = "Week of %m-%d"
            gantt.config.drag_resize = false;

        }

        gantt.config.details_on_create = true;
        gantt.config.drag_progress = false;
        gantt.config.drag_links = false;
        gantt.config.details_on_dblclick = false;
        gantt.config.round_dnd_dates = false;

        gantt.init("gantt-here");

        gantt.config.buttons_left = ["dhx_save_btn", "dhx_cancel_btn"]

        var selected_date = this.formatSelectedDate();
        var task_status = jQuery('#associated_filter option:selected').val();
        var commodity = jQuery('#change_commodity option:selected').val();
        var authority = jQuery('#change_authority option:selected').val();

        gantt.clearAll();
        gantt.load("index.php?module=OrdersTask&mode=loadGanttData&view=LoadLocalDispatch&selected_date=" + selected_date + "&filtro=" + task_status + "&commodity=" + commodity + "&authority=" + authority + "&days=" + jQuery("#days").val(), "json");

        gantt.attachEvent("onTaskClick", function (id, e) {
            thisInstance = new LocalDispatch_Js();
            jQuery('.gantt_task_line').droppable('destroy');
            var taskData = gantt.getTask(id);
            if (taskData.type !== 'project') {

                jQuery('#selected_task_id').val(id);
                var target_div = jQuery('div[task_id="' + id + '"].gantt_task_line');

                target_div.droppable({
                    activeClass: "ui-state-default",
                    drop: thisInstance.handleDropEvent
                });
            }

            thisInstance.loadResources(id);
        });

        gantt.attachEvent("onBeforeTaskChanged", function (id, mode, task) {
            task_cache = {};
            task_cache.start_date = task.start_date.getTime();
            task_cache.end_date = task.end_date.getTime();
            jQuery('#task_cache').val(JSON.stringify(task_cache));
            return true;
        });

        gantt.attachEvent("onAfterTaskDrag", function (id, mode, e) {
            thisInstance = new LocalDispatch_Js();

            var initial_task = JSON.parse(jQuery('#task_cache').val());
            var taskData = gantt.getTask(id);

            thisInstance.checkResources(taskData, gantt, initial_task);
        });

        gantt.templates.grid_row_class = function (start, end, task)
        {
            if (task.$level > 0)
            {
                return "nested_task"
            }
            return "";
        };

        gantt.attachEvent("onGanttRender", function () {
            gantt.sort("start_date", false);
        });

    },
    checkResources: function (taskData, gantt, initial_task) {
        thisInstance = new LocalDispatch_Js();
        var is_running = instance.loadingResources;

        if (is_running == 1) {
            return false;
        }


        var params = {
            'module': app.getModuleName(),
            'parent': app.getParentModuleName(),
            'mode': 'checkResources',
            'view': 'LoadLocalDispatch',
            'task_id': taskData.id,
            'start_date': taskData.start_date.getTime() / 1000,
            'end_date': taskData.end_date.getTime() / 1000,
            'offset': taskData.end_date.getTimezoneOffset()
        }

        var progressIndicatorElement = thisInstance.showLoadingMessage('JS_CHECKING_RESOURCE_AVAILABILITY');

        instance.loadingResources = 1;

        AppConnector.request(params).then(function (data) {
            var data = JSON.parse(data);

            if (data.success) {

                thisInstance.hideLoadingMessage(progressIndicatorElement);

                var response = data.result.response;

                if (response != 'conflict') {
                    return thisInstance.updateTask(params);
                } else {
                    if (confirm(app.vtranslate('JS_RESOURCE_CONFLICTS_UNLINK'))) {
                        return thisInstance.updateTask(params);
                    } else {

                        gantt.getTask(params.task_id).start_date = new Date(initial_task.start_date);
                        gantt.getTask(params.task_id).end_date = new Date(initial_task.end_date);
                        gantt.updateTask(params.task_id);
                    }
                }
            }
        });
    },
    updateTask: function (params) {
        params.mode = 'updateTaskData';
        thisInstance = new LocalDispatch_Js();
        var progressIndicatorElement = thisInstance.showLoadingMessage('JS_UPDATING_TASK');

        AppConnector.request(params).then(function (data) {
            var data = JSON.parse(data);

            if (data.success) {
                thisInstance.hideLoadingMessage(progressIndicatorElement);

                instance.loadingResources = 0;

                if (data.result.result = 'OK') {
                    var params = {
                        title: app.vtranslate('LBL_TASK_UPDATE_TITLE'),
                        text: data.result.msg,
                        width: '35%'
                    };
                    Vtiger_Helper_Js.showPnotify(params);
                } else {
                    var params = {
                        title: app.vtranslate('LBL_TASK_UPDATE_TITLE'),
                        text: data.result.msg,
                        width: '35%'
                    };
                    Vtiger_Helper_Js.showPnotify(params);
                }

            }
        });
    },
    handleDropEvent: function (event, ui) {
        thisInstance = new LocalDispatch_Js();
        var draggableId = ui.draggable.attr("id");
        var droppableId = $(this).attr("task_id");


        var params = {
            'module': app.getModuleName(),
            'parent': app.getParentModuleName(),
            'mode': 'addResourceToTask',
            'view': 'LoadLocalDispatch',
            'task_id': droppableId,
            'resource_id': draggableId,
            'adding_mode': 'add',
        }

        thisInstance.addResourceToTask(params);

    },
    handleAssignedCheckbox: function () {
        $(document).on('change', '.assigned_resource', function (e) {

            var resource_id = jQuery(this).attr('id').split('_');
            var adding_mode = 'remove';
            if (jQuery(this).attr('checked')) {
                adding_mode = 'add';
            }

            var params = {
                'module': app.getModuleName(),
                'parent': app.getParentModuleName(),
                'mode': 'addResourceToTask',
                'view': 'LoadLocalDispatch',
                'task_id': jQuery('#selected_task_id').val(),
                'resource_id': resource_id[1], //
                'adding_mode': adding_mode, //
            }

            thisInstance = new LocalDispatch_Js();
            thisInstance.addResourceToTask(params);

        });
    },
    addResourceToTask: function (params) {
        thisInstance = new LocalDispatch_Js();
        var progressIndicatorElement = thisInstance.showLoadingMessage('JS_ADDING_RESOURCE_TO_TASK');

        var adding_mode = params.adding_mode;
        var task_id = params.task_id;
        AppConnector.request(params).then(function (data) {
            var data = JSON.parse(data);

            if (data.success) {
                thisInstance.hideLoadingMessage(progressIndicatorElement);


                if (data.result.result == 'OK') {
                    var params = {
                        title: data.result.title,
                        text: data.result.message,
                        width: '35%'
                    };
                    Vtiger_Helper_Js.showPnotify(params);

                    var resource_id = data.result.resource_id;
                    if (adding_mode == 'add') {
                        jQuery('#assigned_' + resource_id).attr('checked', true);

                    } else {
                        jQuery('#assigned_' + resource_id).attr('checked', false);
                    }

                    if (data.result.assigned == 'true') {
                        jQuery('[data-id="' + task_id + '"]').css('background-color', '#A9A8A8');
                    } else {
                        jQuery('[data-id="' + task_id + '"]').css('background-color', '#FFF');
                    }

                } else {
                    var params = {
                        title: app.vtranslate('Duplicate Records'),
                        text: 'Error adding the resources',
                        width: '35%'
                    };
                    Vtiger_Helper_Js.showPnotify(params);
                }


            }

        });
    },
    loadResources: function (task_id) {
        instance.resetResourceTable();
        var is_running = instance.loadingResources;

        if (is_running == 1) {
            return false;
        }

        instance.loadingResources = 1;

        var progressIndicatorElement = thisInstance.showLoadingMessage('LBL_LOADING_RESOURCES');

        var params = {
            'module': app.getModuleName(),
            'parent': app.getParentModuleName(),
            'mode': 'loadResourceData',
            'view': 'LoadLocalDispatch',
            'task_id': task_id,
        }

        AppConnector.request(params).then(function (data) {
            instance.loadingResources = 0;
            var data = JSON.parse(data);
            if (data.success) {
                instance.hideLoadingMessage(progressIndicatorElement);
                var resourceContainer = jQuery('.resource_container');
                if (data.result.result == "OK") {
                    resourceContainer.html(data.result.result_date);
                    jQuery('.draggable-resource').draggable({revert: "invalid", helper: 'clone'});
                    jQuery(".chzn-select").chosen();
                }
            }
        });
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
    getDBformattedDate: function (date) {
        var mm = date.getMonth() + 1;
        return date.getFullYear() + '-' + mm + '-' + date.getDate();
    },
    registerDateBack: function () {
        jQuery('.fc-button-prev').on('click', function () {
            var instance = new LocalDispatch_Js();
            var current_date = jQuery('#selected_date_input').val();
            var date_array = current_date.split('-');
            var year = date_array[0];
            var month = date_array[1] - 1;
            var day = date_array[2];

            var date = new Date(year, month, day, 0, 0, 0);
            var daystoadd = parseInt(jQuery('#days').val());
            var days = parseInt(jQuery('#days').val());
            if (isNaN(daystoadd)) {
                daystoadd = 1;
            }
            if (isNaN(days)) {
                days = 1;
            }
            var new_date = new Date(date.setDate(date.getDate() - daystoadd));
            var new_end_date = new Date(date.setDate(date.getDate() + days));

            if (days == 1 || isNaN(days) || typeof days === "undefined") {
                jQuery('#selected_date').html(instance.getFormattedDate(new_date));

            } else {
                jQuery('#selected_date').html(instance.getFormattedDate(new_date) + ' - ' + instance.getFormattedDate(new_end_date));

            }
            jQuery('#selected_date_input').val(instance.getDBformattedDate(new_date));
            instance.updateDatePicker(new_date);
            instance.resetResourceTable();
            instance.reloadListView();


        });
    },
    registerDateForward: function () {
        jQuery('.fc-button-next').on('click', function () {
            var instance = new LocalDispatch_Js();
            var current_date = jQuery('#selected_date_input').val();
            var date_array = current_date.split('-');
            var year = date_array[0];
            var month = date_array[1] - 1;
            var day = date_array[2];

            var date = new Date(year, month, day, 0, 0, 0);
            var daystoadd = parseInt(jQuery('#days').val());
            var days = parseInt(jQuery('#days').val());

            if (isNaN(daystoadd)) {
                daystoadd = 1;
            }
            if (isNaN(days)) {
                days = 1;
            }
            var new_date = new Date(date.setDate(date.getDate() + daystoadd));
            var new_end_date = new Date(date.setDate(date.getDate() + days));

            if (days == 1 || isNaN(days) || typeof days === "undefined") {
                jQuery('#selected_date').html(instance.getFormattedDate(new_date));

            } else {
                jQuery('#selected_date').html(instance.getFormattedDate(new_date) + ' - ' + instance.getFormattedDate(new_end_date));

            }
            jQuery('#selected_date_input').val(instance.getDBformattedDate(new_date));
            instance.updateDatePicker(new_date);
            instance.resetResourceTable();
            instance.reloadListView();


        });
    },
    registerDateFilterChanges: function () {
        jQuery('#filter_date').on('change', function () {
            var instance = new LocalDispatch_Js();

            var dateTime = jQuery(this).val();
            var dateFormat = jQuery(this).data('dateFormat');
            var date = Vtiger_Helper_Js.getDateInstance(dateTime, dateFormat);

            var new_date = Date.parse(date);
            var days = parseInt(jQuery('#days').val());
            var new_end_date = new Date();
            new_end_date.setDate(new_date.getDate() + days);

            if (days == 1 || isNaN(days) || typeof days === "undefined") {
                jQuery('#selected_date').html(instance.getFormattedDate(new_date));

            } else {
                jQuery('#selected_date').html(instance.getFormattedDate(new_date) + ' - ' + instance.getFormattedDate(new_end_date));

            }

            jQuery('#selected_date_input').val(instance.getDBformattedDate(new_date));

            instance.resetResourceTable();
            instance.reloadListView();


        });
    },
    resetResourceTable: function () {
        var empty_table = '<table class="table table-bordered listViewEntriesTable"><thead><tr class="listViewHeaders"><th>Resources</th></tr></thead><tbody><tr><td style="padding: 4%;"> Please choose a task to view availble resources</td></tr></tbody></table>';
        jQuery('.resource_container').html(empty_table);
    },
    reloadListView: function () {
        instance = this;
        listListInstance = Vtiger_List_Js.getInstance();
        var progressIndicator = instance.showLoadingMessage('JS_LOADING_DAYBOOK');


        var defaultParams = this.getDefaultParams();
        var urlParams = jQuery.extend(defaultParams, urlParams);
        AppConnector.requestPjax(urlParams).then(
                function (data) {
                    var listViewContentsContainer = jQuery('.listViewEntriesDiv')
                    listViewContentsContainer.html(data);

                    instance.ViewGanttChart();

                    jQuery.when(instance.coloredListviewRows()).then(function () {

                        instance.reformatTableHeight();
                        instance.hideLoadingMessage(progressIndicator);


                    });

                    jQuery(".chzn-select").chosen();
                    jQuery('[data-toggle="tooltip"]').tooltip();
                    instance.addActionsLinks();
                });
    },
    getDefaultParams: function () {
        var pageNumber = jQuery('#pageNumber').val();
        var module = app.getModuleName();
        var parent = app.getParentModuleName();
        //var cvId = this.getCurrentCvId(); OJO
        var cvId = 121;
        var orderBy = jQuery('#orderBy').val();
        var sortOrder = jQuery("#sortOrder").val();

        var selected_date = this.formatSelectedDate();

        var params = {
            'module': module,
            'parent': parent,
            'page': pageNumber,
            'view': "LocalDispatchDayBook",
            'days': jQuery("#days").val(),
            'viewname': cvId,
            'orderby': orderBy,
            'sortorder': sortOrder,
            'selected_date': selected_date,
            //'column_to_sort': jQuery('#hidden_column_to_sort').val(),
            //'sort': jQuery('#hidden_sort').val(),
            'filtro': jQuery('#associated_filter option:selected').val(),
            'commodity': jQuery('#change_commodity option:selected').val(),
            'authority': jQuery('#change_authority option:selected').val(),
        }


        return params;
    },
    formatSelectedDate: function () {
        var selected_date = jQuery('#selected_date_input').val().split('-');
        if (selected_date[1].length == 1) {
            selected_date[1] = 0 + selected_date[1];
        }

        selected_date = selected_date[0] + '-' + selected_date[1] + '-' + selected_date[2];
        return selected_date;
    },
    setInitialDate: function () {

        instance = this;
        var selected_date = instance.urlParam('selected_date');
        if (selected_date != 0) {
            var today = new Date(selected_date);
        } else {
            var today = new Date();
        }


        jQuery('#selected_date').html(this.getFormattedDate(today));
        jQuery('#selected_date_input').val(this.getDBformattedDate(today));
        instance.updateDatePicker(today);
        instance.reloadListView();
    },
    urlParam: function (name) {
        var results = new RegExp('[\?&amp;]' + name + '=([^&amp;#]*)').exec(window.location.href);
        if (results == null || results.length == 0) {
            return 0;
        } else {
            return results[1];
        }

    },
    filterResources: function () {

        $(document).on('change', '.status-select', function (e) {

            var resource_type = jQuery(this).attr('id').split('-');
            var resource_type = resource_type[1];
            var resource_status = jQuery('#values-' + resource_type).val().split('::');
            var selected_value = jQuery(this).val();

            jQuery('.' + resource_type).show('slow');
            
           
            if (selected_value === 'all') {  
                return false;
            }

            jQuery.each(resource_status, function (key, value) {
                if (value != selected_value)
                {
                    jQuery('.' + resource_type + '.' + value).hide('slow');
                }
            });

        });

        $(document).on('change', '.employee-status-select, .employee-type-select', function (e) {

          
            var status = jQuery('.employee-status-select').val();
            var type = jQuery('.employee-type-select').val();
            
            if(status == ''){status = 'all';}
            if(type == ''){type = 'all';}

            jQuery('.employees').hide('slow');
            
            if(status == 'all' && type == 'all'){
                jQuery('.employees').show('slow');
                return false;
            }else if(status != 'all' && type == 'all'){
                jQuery('.employees.' + status).show('slow');
            }else if(status == 'all' && type != 'all'){
                 jQuery('.employees.' + type).show('slow');
            }else{
                jQuery('.' + type + '.' + status).show('slow');
            }
            

        });
    },
    updateDatePicker: function (selectedDate) {
        jQuery('#filter_date').val(app.getDateInVtigerFormat(jQuery('#filter_date').data('date-format'), selectedDate));
    },
    initDatePicker: function () {
        app.registerEventForDatePickerFields();
    },
    initSelects: function () {
        jQuery(".chzn-select").chosen();
    },
    registerExcelExport: function () {
        jQuery('.exportExcel').on('click', function () {
            var current_date = jQuery('#selected_date_input').val();
            window.location.href = "index.php?module=OrdersTask&view=ExportExcel&mode=GetXLS&start_date=" + current_date;
        });
    },
    reformatTableHeight: function () {
        jQuery('.listViewEntriesDiv').css('height', 'auto');
        jQuery('.gantt-section').css('height', 'auto');
    },
    coloredListviewRows: function () {

        var selected_date = this.formatSelectedDate();
        var params = {
            'module': app.getModuleName(),
            'parent': app.getParentModuleName(),
            'mode': 'getTaskWithResources',
            'view': 'LoadLocalDispatch',
            'selected_date': selected_date,
        }

        return AppConnector.request(params).then(function (data) {
            var data = JSON.parse(data);
            if (data.success) {
                if (data.result.result == "OK") {
                    var tasks_ids = data.result.task_ids;
                    jQuery.each(tasks_ids, function (index, value) {
                        jQuery('[data-id="' + value + '"]').css('background-color', '#A9A8A8');

                    });
                }
            }
        });


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
    viewOneDay: function () {
        jQuery('#one-day').on('click', function () {
            var instance = new LocalDispatch_Js();
            var current_date = jQuery('#selected_date_input').val();
            var date_array = current_date.split('-');
            var year = date_array[0];
            var month = date_array[1] - 1;
            var day = date_array[2];

            var date = new Date(year, month, day, 0, 0, 0);
            var new_date = new Date(date.setDate(date.getDate()));
            jQuery('#selected_date').html(instance.getFormattedDate(new_date));
            jQuery('#selected_date_input').val(instance.getDBformattedDate(new_date));
            jQuery('#days').val(1);
            instance.resetResourceTable();
            instance.reloadListView();


        });
    },
    viewSevenDays: function () {
        jQuery('#next-7-days').on('click', function () {
            var instance = new LocalDispatch_Js();
            var current_date = jQuery('#selected_date_input').val();
            var date_array = current_date.split('-');
            var year = date_array[0];
            var month = date_array[1] - 1;
            var day = date_array[2];

            var date = new Date(year, month, day, 0, 0, 0);
            var new_date = new Date(date.setDate(date.getDate()));
            var new_end_date = new Date(date.setDate(date.getDate() + 7));
            jQuery('#selected_date').html(instance.getFormattedDate(new_date) + ' - ' + instance.getFormattedDate(new_end_date));
            jQuery('#selected_date_input').val(instance.getDBformattedDate(new_date));
            jQuery('#days').val(7);
            instance.resetResourceTable();
            instance.reloadListView();


        });
    },
    viewThirtyDays: function () {
        jQuery('#next-30-days').on('click', function () {
            var instance = new LocalDispatch_Js();
            var current_date = jQuery('#selected_date_input').val();
            var date_array = current_date.split('-');
            var year = date_array[0];
            var month = date_array[1] - 1;
            var day = date_array[2];

            var date = new Date(year, month, day, 0, 0, 0);
            var new_date = new Date(date.setDate(date.getDate()));
            var new_end_date = new Date(date.setDate(date.getDate() + 30));
            jQuery('#selected_date').html(instance.getFormattedDate(new_date) + ' - ' + instance.getFormattedDate(new_end_date));
            jQuery('#selected_date_input').val(instance.getDBformattedDate(new_date));
            jQuery('#days').val(30);
            instance.resetResourceTable();
            instance.reloadListView();


        });
    },
    registerFilterChange: function() {
        $(document).on('change', '#associated_filter, #change_commodity, #change_authority', function (e) {
            var instance = new LocalDispatch_Js();
            instance.resetResourceTable();
            instance.reloadListView();
        });
    },
    addActionsLinks: function () {
        jQuery('.icon-eye-open').on('click', function (e) {
            window.open('index.php?module=OrdersTask&view=Detail&record=' + jQuery(this).attr('id'), '_blank');
        });

        jQuery('.icon-pencil').on('click', function (e) {
            window.open('index.php?module=OrdersTask&view=Edit&record=' + jQuery(this).attr('id'), '_blank');
        });
    },
    selectAssoc: function(){
        jQuery('#associated_filter').change(function(){
            var instance = new LocalDispatch_Js();
            instance.reloadListView();
        });
    },
    sortTable: function(f,n){
        var rows = jQuery('#orders_table tbody  tr.listViewEntries');//.get();
        rows.sort(function(a, b) {
            var A = $(a).children('td').eq(n).text().toUpperCase();
            var B = $(b).children('td').eq(n).text().toUpperCase();
            if(A < B) {
                return -1*f;
            }
            if(A > B) {
                return 1*f;
            }
            return 0;
        });
        jQuery('#orders_table tbody  tr.listViewEntries').remove();
        jQuery.each(rows, function(index, row) {
            jQuery('#orders_table').children('tbody').append(row);
        });
    },
    registerOrderingStuff: function(){
        jQuery(document).on('click', '.HeaderValues', function (e) {
            var esto = jQuery(this);
            var instance = new LocalDispatch_Js();
            var progressIndicator = instance.showLoadingMessage('Filtering ..');
            var n = esto.parent().prevAll().length;
            jQuery('#orders_table').find('img.icon-chevron-up,img.icon-chevron-down').hide();
            if (esto.data('nextsortorderval') == 'ASC'){
                instance.sortTable(1,n);
                esto.parent().find('img').hide();
                esto.parent().find('img.icon-chevron-down').show();
                esto.data('nextsortorderval','DESC');
            }else{
                instance.sortTable(-1,n);
                esto.parent().find('img').hide();
                esto.parent().find('img.icon-chevron-up').show();
                esto.data('nextsortorderval','ASC');
            }
            instance.hideLoadingMessage(progressIndicator); 
        });
    },
    registerEvents: function () {
        instance = this;
        instance.registerDateBack();
        instance.registerDateForward();
        instance.filterResources();
        instance.handleAssignedCheckbox();
        instance.initDatePicker();
        instance.initSelects();
        instance.registerDateFilterChanges();
        instance.registerExcelExport();
        instance.reformatTableHeight();
        instance.coloredListviewRows();
        instance.viewOneDay();
        instance.viewSevenDays();
        instance.viewThirtyDays();
        instance.registerFilterChange();
        instance.selectAssoc();
        instance.registerOrderingStuff();
    }

});

jQuery(document).ready(function () {
    jQuery('.listViewEntries').hide();
    var instance = new LocalDispatch_Js();
    instance.setInitialDate();
    instance.ViewGanttChart();
    instance.registerEvents();
});