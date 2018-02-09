jQuery.Class("Trips_JS", {}, {
    createServiceHours: function(){
        thisInstance = this;
        jQuery(document).on('click', '.createServiceHours', function (e) {
            var driverFirstName = jQuery('#Trips_detailView_fieldValue_trips_driverfirstname > span').text().trim();
            var driverLastName = jQuery('#Trips_detailView_fieldValue_trips_driverlastname > span').text().trim();
            if(driverFirstName !== "" && driverLastName !== ""){
                var postQuickCreateSave = function (data) {
                     instance.updateURL();
                };
                var quickCreateParams = {};
                quickCreateParams['noCache'] = true;
                quickCreateParams['callbackFunction'] = postQuickCreateSave;
                var progress = jQuery.progressIndicator();
                var headerInstance = new Vtiger_Header_Js();
                headerInstance.getQuickCreateForm('index.php?module=ServiceHours&view=QuickCreateAjax', 'ServiceHours', quickCreateParams).then(function (data) {
                    progress.progressIndicator({'mode': 'hide'});
                    headerInstance.handleQuickCreateData(data, quickCreateParams);
                    jQuery('[name="employee_id_display"]').val(jQuery('#Trips_detailView_fieldValue_trips_driverfirstname > span').text().trim() + ' ' + jQuery('#Trips_detailView_fieldValue_trips_driverlastname > span').text().trim()); //employee name & lastname
                    jQuery('[name="employee_id"]').val(jQuery('#Trips_detailView_fieldValue_driver_id > span > a').attr('href').replace( /^\D+/g, '')); //employee id
                    jQuery('[name="trips_id_display"]').val(jQuery('#Trips_detailView_fieldValue_trips_id > span').text().trim()); //trips_id
                    jQuery('[name="trips_id"]').val(jQuery('#recordId').val()); //tripsid
                    jQuery('[name="employee_id"]').closest('tr').hide();
                    jQuery('[name="trips_id"]').closest('tr').hide();
                });
            }else{
                Vtiger_Helper_Js.showPnotify({'text' : 'You need to associate a driver to the trip.','type' : 'error'} );
            }
        });
    },
    reloadOrdersTable: function (data) {
        instance = this;

        var progressIndicator = instance.showLoadingMessage('JS_UPDATING_ORDERS_TABLE');
        var urlParams = {
            module: 'Trips',
            action: 'TripsActions',
            mode: 'reloadOrdersTable',
            tripid: $('#recordId').val(),
        }
        AppConnector.request(urlParams).then(
                function (data) {
                    if (jQuery("tr td:contains('No Orders related')").length) {
                        jQuery("tr td:contains('No Orders related')").remove();
                        jQuery('div.relatedContents table > thead tr').remove();
                        jQuery('div.relatedContents table > thead').append('<tr class="listViewHeaders"><th nowrap="">Status</th><th nowrap="">Planned Load Date</th><th nowrap="">Planned Delivery Date</th><th nowrap="">PU Date</th><th nowrap="">Actual Pickup</th><th nowrap="">Account Name</th><th nowrap="">Shipper Last Name</th><th nowrap="">Order No</th><th nowrap="">Est. Weight</th><th nowrap="">Actual Weight</th><th nowrap="">Total Linehaul</th><th nowrap="">RD Date</th><th nowrap="">Origin City</th><th nowrap="">Origin State</th><th nowrap="">Dest Agent</th><th nowrap="">Dest City</th><th nowrap="">Dest State</th><th nowrap="">Delivery</th><th nowrap="">Actual Weight Tare</th><th nowrap="">Actions</th></tr>');
                    }
                    jQuery("#orders_tbody").empty();
                    jQuery('div.relatedContents table > tbody').append(data.result);
                    jQuery('#Trips_detailView_fieldValue_total_line_haul').find('span').text(jQuery('#total_linehaul').val());
                    jQuery('#Trips_detailView_fieldValue_total_weight').find('span').text(jQuery('#total_weight').val());
                    jQuery('#total_linehaul,#total_weight').remove();
                    instance.hideLoadingMessage(progressIndicator);
                    instance.addDatePickers();
                    jQuery('[data-toggle="tooltip"]').tooltip();
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
    addStatusActions: function(){
        instance = this;
        jQuery(document).on('change', '.statusajax', function (e) {
	        var statusAjaxElement = jQuery(this);
            var status = jQuery(this).find('option:selected').val();
            var planned_load_date = jQuery(this).closest('tbody').find('#planned_load_date').val();
            var planned_delivery_date = jQuery(this).closest('tbody').find('#planned_delivery_date').val();
            var actual_load_date = jQuery(this).closest('tbody').find('#actual_load_date').val();
            var actual_delivery_date = jQuery(this).closest('tbody').find('#actual_delivery_date').val();
            var actual_weight = jQuery(this).closest('tbody').find('#actual_weight').val();

            var urlParams = {
                module: 'Trips',
                action: 'TripsActions',
                mode: 'updateOrdersOtherStatus',
                orderid: jQuery(this).closest('tbody').data('orderid'),
                other_status: status,
            }

            switch(status) {
                case 'Confirmed':
                    if (!(planned_load_date && planned_delivery_date)){
                        Vtiger_Helper_Js.showPnotify({'text' : 'You need to fill Planned Load Date and Planned Delivery Date fields.','type' : 'error'} );
                        jQuery(this).closest('tbody').find('select.statusajax option').removeAttr("selected");
                        jQuery(this).val(jQuery(this).closest('tbody').find('.statusajaxvalue').val());
                        jQuery(this).trigger('liszt:updated');
                        return false;
                    }else{
                        urlParams.planned_load_date = planned_load_date;
                        urlParams.planned_delivery_date = planned_delivery_date;
                    }
                    break;
                case 'Loaded':
                    if (!(actual_load_date && planned_delivery_date)){
                        Vtiger_Helper_Js.showPnotify({'text' : 'You need to fill Actual Load Date and Planned Delivery Date fields.','type' : 'error'} );
                        jQuery(this).closest('tbody').find('select.statusajax option').removeAttr("selected");
                        jQuery(this).val(jQuery(this).closest('tbody').find('.statusajaxvalue').val());
                        jQuery(this).trigger('liszt:updated');
                        return false;
                    }else{
                        urlParams.actual_load_date = actual_load_date;
                        urlParams.planned_delivery_date = planned_delivery_date;
                    }
                    break;
                case 'Delivered':
                    if (!(actual_delivery_date && actual_weight)){
                        Vtiger_Helper_Js.showPnotify({'text' : 'You need to fill Actual Delivery Date and Actual Weight fields.','type' : 'error'} );
                        jQuery(this).closest('tbody').find('select.statusajax option').removeAttr("selected");
                        jQuery(this).val(jQuery(this).closest('tbody').find('.statusajaxvalue').val());
                        jQuery(this).trigger('liszt:updated');
                        return false;
                    }else{
                        urlParams.actual_delivery_date = actual_delivery_date;
                        urlParams.actual_weight = actual_weight;
                    }
                    break;
            }

            var progressIndicator = instance.showLoadingMessage('Updating Order Status');

            AppConnector.request(urlParams).then(
                function (data) {
                    var data = eval(data);
                    instance.hideLoadingMessage(progressIndicator);
                    if(data.result == "Ok"){
                        Vtiger_Helper_Js.showPnotify({'text' : 'Order Updated!','type' : 'success'} );
                        statusAjaxElement.closest('tbody').find('.statusajaxvalue').val(urlParams.other_status);
                        if(status == "blank" || status == "Non-Planned"){
                            statusAjaxElement.closest('tbody').find('#planned_load_date').val(''); //planned_load_date
                            statusAjaxElement.closest('tbody').find('#planned_delivery_date').val(''); //planned_delivery_date
                            statusAjaxElement.closest('tbody').find('#actual_load_date').val(''); //actual_load_date
                            statusAjaxElement.closest('tbody').find('#actual_delivery_date').val(''); //actual_delivery_date
                        }
                    }else{
                        Vtiger_Helper_Js.showPnotify({'text' : 'Error updating the order. Please contact support','type' : 'error'} );
                        console.log(data.result);
                    }

		            instance.updateURL();
                });
        });
    },
    addActionsLinks: function () {

        thisInstance = this;

        jQuery(document).on('click', '.tripOrders .icon-eye-open', function (e) {
            window.open('index.php?module=Orders&view=Detail&record=' + jQuery(this).attr('id'), '_blank');
        });

        jQuery(document).on('click', '.tripOrders .icon-pencil', function (e) {
            window.open('index.php?module=Orders&view=Edit&record=' + jQuery(this).attr('id'), '_blank');
        });

        jQuery(document).on('click', '.tripOrders .icon-remove', function (e) {
            var currentRecord = jQuery('#recordId').val();
            var orderRow = jQuery(this).closest('tr');
            var params = {};
            params['mode'] = "deleteRelation";
            params['module'] = 'Trips';
            params['action'] = 'RelationAjax';

            params['related_module'] = 'Orders';
            params['src_record'] = jQuery('#recordId').val();
            var related_list_id = [];
            related_list_id[0] = jQuery(this).attr('id');
            params['related_record_list'] = JSON.stringify(related_list_id);
            var progressIndicator = thisInstance.showLoadingMessage('Removing Order');

            AppConnector.request(params).then(
                function (data) {
                   if(data == 1){
                        var params = {
                            title: app.vtranslate('Success'),
                            text: app.vtranslate('Order Removed from Trip'),
                            width: '35%'
                        };
			thisInstance.hideLoadingMessage(progressIndicator);
			Vtiger_Helper_Js.showPnotify(params);
			orderRow.hide();
                        instance.updateURL();
                   }
                },
                function (error) {}
            );

        });
    },
    addDatePickers: function () {
        app.registerEventForDatePickerFields(jQuery('.dateField'), true);
    },
    updateFields: function () {
        jQuery(document).on('change', '.sit', function () {
            var instance = new Trips_JS;
            var progressIndicator = instance.showLoadingMessage('JS_LOADING_ORDERS');
            var urlParams = {
                module: 'Trips',
                action: 'TripsActions',
                mode: 'updateSIT',
                orderid: jQuery(this).closest('tbody').data('orderid'),
                sit: (jQuery(this).prop("checked")) ? 1 : 0,
            }
            AppConnector.request(urlParams).then(
                function (data) {
                    if(!data){
                        Vtiger_Helper_Js.showPnotify({'text' : 'An error ocurred, please try again!','type' : 'error'} );
                    }
                    instance.updateURL();
                    instance.hideLoadingMessage(progressIndicator);
                });
        });

        jQuery(document).on('change', '#actual_weight', function () { // Weight Gross (Unuseful now)
            var instance = new Trips_JS;
            var progressIndicator = instance.showLoadingMessage('JS_LOADING_ORDERS');
            var urlParams = {
                module: 'Trips',
                action: 'TripsActions',
                mode: 'updateActualWeightOrders',
                orderid: jQuery(this).closest('tbody').data('orderid'),
                actual_weight: jQuery(this).val(),
                tripsid: jQuery('#recordId').val()
            }
            AppConnector.request(urlParams).then(
                function (data) {
                    instance.hideLoadingMessage(progressIndicator);
                    if(!data){
                       Vtiger_Helper_Js.showPnotify({'text' : 'An error ocurred, please try again!','type' : 'error'} );
                    }
                    instance.updateURL();
                });
        });
    },
    updateDates: function () {
        thisInstance = this;
        jQuery(document).on('change', '[name="actual_load_date"], [name="planned_load_date"], [name="planned_delivery_date"], [name="actual_delivery_date"], [name="pl_confirmed"], [name="pd_confirmed"]', function () {
            var orderRow = jQuery(this).closest('tbody');
            //OT19098 check date precedence
            var onChangeInputName = jQuery(this).attr('name');
            var dateOk = thisInstance.checkDates(onChangeInputName,orderRow);
            if ( ! dateOk ){
                return false;
            }
            var instance = new Trips_JS;
            var progressIndicator = instance.showLoadingMessage('JS_UPDATING_ORDER');
            var aldate = orderRow.find('input[name="actual_load_date"]').val();
            var addate = orderRow.find('input[name="actual_delivery_date"]').val();
            var pldate = orderRow.find('input[name="planned_load_date"]').val();
            var pddate = orderRow.find('input[name="planned_delivery_date"]').val();
            var urlParams = {
                module: 'Trips',
                action: 'TripsActions',
                mode: 'updateDateOrders',
                orderid: orderRow.data('orderid'),
                aldate: aldate,
                addate: addate,
                pldate: pldate,
                pddate: pddate,
                //pl_confirmed: orderRow.find('select[name="pl_confirmed"] option:selected').val(),
                //pd_confirmed: orderRow.find('select[name="pd_confirmed"] option:selected').val(),
            }
            AppConnector.request(urlParams).then(
                function (data) {
                    Vtiger_Helper_Js.showPnotify({'text' : 'Order Updated!','type' : 'info'} );
                    instance.hideLoadingMessage(progressIndicator);
                    instance.updateURL();

                });
        });
    },
    checkDates : function(onChangeInputName,orderRow){
        var dateOK = true;
        var dateChecks = [{
                on: 'actual_load_date',
                from: 'actual_load_date',
                to: 'actual_delivery_date',
				msg: 'The "Actual Load Date" should be before the "Actual Delivery Date"'
			},
			{
                on: 'actual_delivery_date',
                from: 'actual_load_date',
                to: 'actual_delivery_date',
				msg: 'The "Actual Delivery Date" should be after the "Actual Load Date"'
			},
			{
                on: 'planned_load_date',
                from: 'planned_load_date',
                to: 'planned_delivery_date',
				msg: 'The "Planned Load Date" should be before the "Planned Delivery Date"'
			},
			{
                on: 'planned_delivery_date',
                from: 'planned_load_date',
                to: 'planned_delivery_date',
				msg: 'The "Planned Delivery Date" should be after the "Planned Load Date"'
			},
		];
            $.each(dateChecks, function(key, date){
                if(onChangeInputName === date.on){
                    var domFrom = orderRow.find('input[name="'+date.from+'"]').val();
                    var domTo = orderRow.find('input[name="' + date.to + '"]').val();
                    if(domFrom !== '' && domTo !== ''){
                        var from = new Date(domFrom);
                        var to = new Date(domTo);
                        if(from>to){
                            orderRow.find('input[name="'+date.from+'"]').val('');
                            orderRow.find('input[name="' + date.to + '"]').val('');
                            dateOK = false;
                            bootbox.alert(date.msg);
                        }
                    }
                }
            });
            return dateOK;
    },
    registerArrows: function () {
        jQuery(document).on('click', '.imageElement', function () {
            if (jQuery(this).attr('src').indexOf('right') > -1) {
                jQuery(this).attr('src', jQuery(this).data('downimage'));
                jQuery('tr.aux.listViewEntries').remove();
                var instance = new Trips_JS;
                var progressIndicator = instance.showLoadingMessage('JS_LOADING_ORDERS');
                var tripid = jQuery(this).closest('tr').data('id');
                var urlParams = {
                    module: 'Trips',
                    action: 'TripsActions',
                    mode: 'getRelatedTable4ListView',
                    tripid: tripid,
                };
                AppConnector.request(urlParams).then(
                        function (data) {
                            var tabla = jQuery('#tabla_auxiliar').html();
                            jQuery('tr[data-id="' + tripid + '"]').after('<tr class="aux listViewEntries"><td colspan="5" style="max-width: 100%;overflow-x: auto;">' + tabla + '</td></tr>');
                            jQuery('tr.aux.listViewEntries').find('td > table > tbody').append(data.result);
                            instance.hideLoadingMessage(progressIndicator);
                        });
            } else {
                jQuery(this).attr('src', jQuery(this).data('rightimage'));
                jQuery(this).closest('tr').next('tr.aux.listViewEntries').remove();
            }
        });
    },
    /**
     * Uncomment this block of code should it be discovered that
     * this functionality is needed/used elsewhere. (Rik Davis)
    moveOrdersAction: function() {
        jQuery('#moveOrdersTask').click(function(){
            if (jQuery('.asoc:checkbox:checked').length > 0){
                var instance = new Trips_JS();
                instance.moveToTrip();
            } else {
                alert('Please select some OrdersTask before.');
            }
        });
    },
    */
    moveToTrip: function(){
        var ordersToMoved = new Array();
        jQuery('.asoc:checkbox:checked').each(function(){
            ordersToMoved.push(jQuery(this).parent().parent().data('id'));
        });
        ordersToMoved = ordersToMoved.join(",");
        params = {
            'module': 'Orders',
            'view': 'ShowModals',
            'mode': 'showMove2TripModal',
            'sourcemodule': app.getModuleName(),
            'orderslist': ordersToMoved,
            'tripid': jQuery('#recordId').val(),
        }
        AppConnector.request(params).then(
            function (data) {
                app.showModalWindow(data, function (data) {
                    var instance = new Trips_JS();
                    instance.bindSaveAction();
                });
            },
            function (jqXHR, textStatus, errorThrown) {
            }
        );
    },
    bindSaveAction: function(){
        thisInstance = this;
        jQuery('[name^="check_"]').change(function(){
            var name = jQuery(this).attr("name");
            if (jQuery(this).prop("checked")){
                jQuery('[name^="check_"]').each(function(){
                    if (jQuery(this).attr("name") !== name){
                        jQuery(this).prop("checked",false);
                    }
                });
            }
        });
        jQuery('#saveButton').click(function(){
            var currentRecord = jQuery('#oldtripid').val();
            params = {
                'module': 'Trips',
                'action': 'RelationAjax',
                'mode': 'moveRelation',
                'src_record': jQuery('[name^="check_"]:checked').attr('name').replace('check_',''),
                'old_src_record': jQuery('#oldtripid').val(),
                'related_module': 'Orders',
                'related_record_list': jQuery("#ordersids").val().split(','),
            }
            AppConnector.request(params).then(
                function (data) {
                    jQuery('.asoc:checkbox:checked').each(function(){
                        jQuery(this).parent().parent().remove();
                    });
                    app.hideModalWindow();
                    var params = {
                        title: app.vtranslate('Success'),
                        text: app.vtranslate('Order Moved to Trip'),
                        width: '35%'
                    };
                    Vtiger_Helper_Js.showPnotify(params);
                    instance.updateURL();
                },
                function (jqXHR, textStatus, errorThrown) {
                }
            );
        });
    },
    sequenceOrder: function () {
        //Helper function to keep table row from collapsing when being sorted
        var fixHelperModified = function(e, tr) {
            var $originals = tr.children();
            var $helper = tr.clone();
            $helper.children().each(function(index)
            {
              $(this).width($originals.eq(index).width())
            });
            return $helper;
        };

        //Make orders table sortable
        $("#orders_list tbody").sortable({
            helper: fixHelperModified,
            stop: function(event,ui) {renumber_table('#orders_list tbody')}
        }).disableSelection();

        //Renumber table rows
        function renumber_table(tableID) {
            instance = new Trips_JS();
            arrayOrderid = new Array();
            arraySequence = new Array();
            $(tableID + " tr").each(function() {
                newSequenceNumber = $(this).parent().children().index($(this)) + 1;
                oldSequenceNumber = $(this).find('.sequence').html()*1;
                //if sequence number changed save new sequence number to database
                if(newSequenceNumber != oldSequenceNumber){
                    arrayOrderid.push($(this).data('orderid'));
                    arraySequence.push(newSequenceNumber);
                    //change the number in the row
                    $(this).find('.sequence').html(newSequenceNumber);
                }
            });
            if(arrayOrderid.length > 0){
                var urlParams = {
                    module: 'Trips',
                    action: 'TripsActions',
                    mode: 'updateSequenceOrders',
                    orderid: arrayOrderid,
                    sequence: arraySequence,
                }
                 AppConnector.request(urlParams).then(
                    function (data) {
                        Vtiger_Helper_Js.showPnotify({'text' : 'Sequence Updated!','type' : 'info'} );
                        //console.log(data.length);
                        instance.updateURL();
                        document.getElementById('orders_list').scrollIntoView();
                });
            }
        }
    },
    updateURL: function () {
        //We need this so if the user clicks F5 do not go into the ajax response.
        if (jQuery('input[name="mode"]').val() == 'edit') {
            window.history.replaceState("", "", "index.php?module=Trips&view=Edit&record=" + jQuery('input[name="record"]').val());
        } else {
             window.history.replaceState("", "", "index.php?module=Trips&view=Detail&record=" + jQuery('#recordId').val());
        }

    },
    createDriverCheckin: function(){
        thisInstance = this;
        jQuery(document).on('click', '.createDriverCheckin', function (e) {
            var driverFirstName = jQuery('#Trips_detailView_fieldValue_trips_driverfirstname > span').text().trim();
            var driverLastName = jQuery('#Trips_detailView_fieldValue_trips_driverlastname > span').text().trim();
            if(driverFirstName !== "" && driverLastName !== ""){
                var postQuickCreateSave = function (data) {
                     thisInstance.updateURL();
                     window.location.reload(false);

                };
                var quickCreateParams = {};
                quickCreateParams['noCache'] = true;
                quickCreateParams['callbackFunction'] = postQuickCreateSave;
                var progress = jQuery.progressIndicator();
                var headerInstance = new Vtiger_Header_Js();
                headerInstance.getQuickCreateForm('index.php?module=TripsDriverCheckin&view=QuickCreateAjax', 'TripsDriverCheckin', quickCreateParams).then(function (data) {
                    progress.progressIndicator({'mode': 'hide'});
                    headerInstance.handleQuickCreateData(data, quickCreateParams);
                    jQuery('[name="tripsdrivercheckin_tripsid_display"]').val(jQuery('#Trips_detailView_fieldValue_trips_id > span').text().trim()); //trips_id
                    jQuery('[name="tripsdrivercheckin_tripsid"]').val(jQuery('#recordId').val()); //tripsid
                    jQuery('[name="tripsdrivercheckin_tripsid"]').closest('td').hide().prev('td').hide();
                });
            }else{
                Vtiger_Helper_Js.showPnotify({'text' : 'You need to associate a driver to the trip.','type' : 'error'} );
            }
        });
    },
        addDriverCheckinActionsLinks: function () {

        thisInstance = this;

        jQuery(document).on('click', '.deleteDriverCheckin', function (e) {
            var orderRow = jQuery(this).closest('tr');
            var params = {};
            params['mode'] = "deleteCheckin";
            params['module'] = 'Trips';
            params['action'] = 'TripsActions';
            params['src_record'] = jQuery('#recordId').val();
            params['drivercheckin_id'] = jQuery(this).data('id');
            var progressIndicator = thisInstance.showLoadingMessage('Removing Driver Checkin');

            AppConnector.request(params).then(
                function (data) {
                   if(data.result == 'ok'){
                        var params = {
                            title: app.vtranslate('Success'),
                            text: app.vtranslate('Deleted'),
                            width: '35%'
                        };
			thisInstance.hideLoadingMessage(progressIndicator);
			Vtiger_Helper_Js.showPnotify(params);
			orderRow.hide();
                        instance.updateURL();
                   }else{
                        var params = {
                            title: app.vtranslate('Success'),
                            text: app.vtranslate('Error Processing your request'),
                            width: '35%'
                        };
			thisInstance.hideLoadingMessage(progressIndicator);
			Vtiger_Helper_Js.showPnotify(params);
                        instance.updateURL();
                   }
                },
                function (error) {}
            );

        });
    },
    addChosenToSelects: function(){
        jQuery('.statusajax, .pl_confirmed, .pd_confirmed').chosen();
    },
    sequenceOrder: function () {
        //Helper function to keep table row from collapsing when being sorted
        var fixHelperModified = function(e, tr) {
            var $originals = tr.children();
            var $helper = tr.clone();
            $helper.children().each(function(index)
            {
              $(this).width($originals.eq(index).width())
            });
            return $helper;
        };

        //Make orders table sortable
        $("#orders_list").sortable({
            helper: fixHelperModified,
            stop: function(event,ui) {renumber_table('#orders_list')}
        }).disableSelection();

        //Renumber table rows
        function renumber_table(tableID) {
            instance = new Trips_JS();
            arrayOrderid = new Array();
            arraySequence = new Array();
            $(tableID + " tbody").each(function() {
                newSequenceNumber = $(this).parent().children().index($(this)) + 1;
                oldSequenceNumber = $(this).find('.sequence').html()*1;
                //if sequence number changed save new sequence number to database
                if(newSequenceNumber != oldSequenceNumber){
                    arrayOrderid.push($(this).data('orderid'));
                    arraySequence.push(newSequenceNumber);
                    //change the number in the row
                    $(this).find('.sequence').html(newSequenceNumber);
                }
            });
            if(arrayOrderid.length > 0){
                var urlParams = {
                    module: 'Trips',
                    action: 'TripsActions',
                    mode: 'updateSequenceOrders',
                    orderid: arrayOrderid,
                    sequence: arraySequence,
                }
                 AppConnector.request(urlParams).then(
                    function (data) {
                        Vtiger_Helper_Js.showPnotify({'text' : 'Sequence Updated!','type' : 'info'} );
                        //console.log(data.length);
                        instance.updateURL();
                        document.getElementById('orders_list').scrollIntoView();
                });
            }
        }
    },
    registerEvents: function () {
        this.addActionsLinks();
        this.addStatusActions();
        this.addDatePickers();
        this.updateDates();
        this.updateFields();
        this.registerArrows();
        // this.moveOrdersAction();
        this.createServiceHours();
        this.sequenceOrder();
        this.createDriverCheckin();
        this.addDriverCheckinActionsLinks();
        this.addChosenToSelects();
        this.sequenceOrder();

    }
});

jQuery(document).ready(function () {
    var instance = new Trips_JS;
    instance.registerEvents();
    jQuery('[data-toggle="tooltip"]').tooltip();
    jQuery('#orders_tbody').find('input').css('margin-bottom','0px');
    jQuery('#orders_tbody').find('select').css('margin-bottom','0px');
    jQuery('#orders_tbody').find('div.input-append').css('margin-bottom','0px');

    var availableHours = jQuery("#availableHours").val();
    if(availableHours < 10){
        bootbox.alert("There are less than 10 hours availables.");
    }
});
