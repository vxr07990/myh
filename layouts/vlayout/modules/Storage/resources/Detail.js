Vtiger_Detail_Js("Storage_Detail_Js", {
    cancelRecord: function () {
        var message = app.vtranslate('JS_CANCEL_CONFIRMATION', 'Storage');
        var storageid = jQuery('#recordId').val();
        var status = jQuery('#Storage_detailView_fieldValue_storage_status').text().trim();
        if (status !== 'Cancelled') {
            Vtiger_Helper_Js.showConfirmationBox({'message': message}).then(function (data) {
                AppConnector.request('index.php?module=Storage&action=Cancel&storageid=' + storageid).then(
                        function (data) {
                            if (data.success == true) {
                                window.location.href = data.result;
                            } else {
                                Vtiger_Helper_Js.showPnotify(data.error.message);
                            }
                        });
            },
                    function (error, err) {
                    }
            );
        } else {
            Vtiger_Helper_Js.showPnotify('Record alredy Cancelled');
        }
    }
}, {
    registerShowBlocks: function () {
        var option = jQuery('#Storage_detailView_fieldValue_storage_option').find('span').html().trim();
        if (option == 'SIT') {
            jQuery('#Storage_detailView_fieldValue_storage_perm_datein').closest('table').addClass('hide');
            jQuery('#Storage_detailView_fieldLabel_storage_perm_authorization').find('label').addClass('hide');
            jQuery('#Storage_detailView_fieldValue_storage_perm_authorization').find('span').addClass('hide');
        }
        if (option == 'Perm') {
            jQuery('#Storage_detailView_fieldValue_storage_sit_datein').closest('table').addClass('hide');
            jQuery('#Storage_detailView_fieldLabel_storage_sit_authorization').find('label').addClass('hide');
            jQuery('#Storage_detailView_fieldValue_storage_sit_authorization').find('span').addClass('hide');
        }
    },
    toggleMilitaryDetailFields: function () {
        var storageId = jQuery('#recordId').val();
        var orderId = jQuery('[name="storage_orders"]').val();
        var dataUrl = "index.php?module=Orders&action=GetBillingType&storageId=" + storageId + "&orderId=" + orderId;
        AppConnector.request(dataUrl).then(
                function (data) {
                    if (data.success) {
                        if (data.result['tariff_type'] != 'Military') {
                            jQuery('#Storage_detailView_fieldLabel_storage_military_control').find('label').addClass('hide');
                            jQuery('#Storage_detailView_fieldValue_storage_military_control').find('span').addClass('hide');
                        }
                    }
                },
                function (error) {
                    console.dir('Error: ' + error);
                });

    },
    registerEvents: function () {
        this._super();
        this.registerShowBlocks();
        this.toggleMilitaryDetailFields();
        if (jQuery('#Storage_detailView_fieldValue_storage_status').text().trim() === "") {
            jQuery('#Storage_detailView_fieldValue_storage_datetime_cancelled').text("");
        }
    }

});
