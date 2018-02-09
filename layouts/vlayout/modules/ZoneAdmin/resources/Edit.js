/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


Vtiger_Edit_Js("ZoneAdmin_Edit_Js", {}, {
    hideBlocks: function () {
        if (jQuery('#ZoneAdmin_Edit_fieldName_za_state').val() == null) {
            jQuery('#ZoneAdmin_Edit_fieldName_za_state').closest('td').hide().prev('td').hide();
        } else {
            jQuery('#state_checkbox').prop('checked', true);
        }

        if (jQuery('#ZoneAdmin_editView_fieldName_zip_code').val() == '') {
            jQuery('#ZoneAdmin_editView_fieldName_zip_code').closest('td').hide().prev('td').hide();
        } else {
            jQuery('#zip_checkbox').prop('checked', true);
        }



    },
    showCheckboxes: function () {
        jQuery('#ZoneAdmin_editView_fieldName_za_zone').closest('td').next('td').attr('colspan', 2).html('<input class="zone_checkbox" type="checkbox" id="state_checkbox" value="state"> Create zone using states<br><input class="zone_checkbox" type="checkbox" id="zip_checkbox" value="zipcode"> Create zone using zip codes<br>').attr('style', 'width:100%;').next('td').hide();
    },
    registerCheckboxChange: function () {
        jQuery(document).on('change', '.zone_checkbox', function () {
            if (this.checked) {

                var value = jQuery(this).val();
                if (value == 'state') {
                    jQuery('#zip_checkbox').prop('checked', false);
                    jQuery('#ZoneAdmin_Edit_fieldName_za_state').closest('td').show().prev('td').show();
                    jQuery('#ZoneAdmin_editView_fieldName_zip_code').closest('td').hide().prev('td').hide();
                    jQuery('#ZoneAdmin_editView_fieldName_zip_code').val('');
                }
                if (value == 'zipcode') {
                    jQuery('#state_checkbox').prop('checked', false);
                    jQuery('#ZoneAdmin_editView_fieldName_zip_code').closest('td').show().prev('td').show();
                    jQuery('#ZoneAdmin_Edit_fieldName_za_state').closest('td').hide().prev('td').hide();
                    jQuery("#ZoneAdmin_Edit_fieldName_za_state").find('option:selected').removeAttr("selected");
                    jQuery("#ZoneAdmin_Edit_fieldName_za_state").select2();
                }
            } else {
                var value = jQuery(this).val();

                if (value == 'state') {
                    jQuery('#ZoneAdmin_Edit_fieldName_za_state').closest('td').hide().prev('td').hide();
                    jQuery("#ZoneAdmin_Edit_fieldName_za_state").find('option:selected').removeAttr("selected");
                    jQuery("#ZoneAdmin_Edit_fieldName_za_state").select2();
                }
                if (value == 'zipcode') {
                    jQuery('#ZoneAdmin_editView_fieldName_zip_code').closest('td').hide().prev('td').hide();
                    jQuery('#ZoneAdmin_editView_fieldName_zip_code').val('');
                }
            }
        });
    },
    registerBeforeSave: function () {
        jQuery(document).on('click', 'button.btn-success', function (e) {
            e.preventDefault();
            var urlParams = {
                module: 'ZoneAdmin',
                action: 'checkZipNState',
                states: jQuery("#ZoneAdmin_Edit_fieldName_za_state").select2("val"),
                zips: jQuery('#ZoneAdmin_editView_fieldName_zip_code').val(),
                state_checkbox: jQuery('#state_checkbox').prop("checked"),
                zip_checkbox: jQuery('#zip_checkbox').prop("checked"),
		agentid: jQuery('[name="agentid"]').val(),
		id: jQuery('[name="record"]').val(),
            }
            AppConnector.requestPjax(urlParams).then(
                    function (data) {
                        if (data.result.result == 'OK') {
                            jQuery('#EditView').submit();
                        } else {
                            if (jQuery('#zip_checkbox').prop("checked")) {
                                var message = app.vtranslate('There is already one ZoneAdmin record that contains the zip codes selected, do you want to proceed?');
                            } else if (jQuery('#state_checkbox').prop("checked")) {
                                var message = app.vtranslate('There is already one ZoneAdmin record that contains the states selected, do you want to proceed?');
                            }
                            Vtiger_Helper_Js.showConfirmationBox({'message': message}).then(
                                    function (e) {
                                        jQuery('#EditView').submit();
                                    },
                                    function (error, err) {

                                    }
                            );
                        }
                    }
            );
        });
    },
    registerZipAutoComplete: function () {
        var thisInstance = this;

        if (jQuery('#ZoneAdmin_editView_fieldName_zip_code').length) {

            jQuery('#ZoneAdmin_editView_fieldName_zip_code').before('Type to search zip database: <input style="margin-bottom: 1%;" id="zip_search" type="text" class="input-large " placeholder="Search by zip code" autocomplete="off"></input><div> Selected Zip Codes:</div>');
            jQuery('#ZoneAdmin_editView_fieldName_zip_code').attr('style', 'width: 20% !important; height:220px;');

            jQuery('#zip_search').autocomplete({
                'minLength': '1',
                'source': function (request, response) {

                    var searchValue = request.term;
                    var params = {};
                    params.search_module = 'ZoneAdmin'
                    params.search_value = searchValue;
                    thisInstance.searchModuleNames(params).then(function (data) {
                        var reponseDataList = new Array();
                        var serverDataFormat = data.result
                        if (serverDataFormat.length <= 0) {
                            serverDataFormat = new Array({
                                'label': app.vtranslate('JS_NO_RESULTS_FOUND'),
                                'type': 'no results'
                            });
                        }
                        for (var id in serverDataFormat) {
                            var responseData = serverDataFormat[id];
                            reponseDataList.push(responseData);
                        }
                        response(reponseDataList);
                    });
                },
                'select': function (event, ui) {
                    var selectedItemData = ui.item.value;
                    //To stop selection if no results is selected
                    if (typeof selectedItemData.type != 'undefined' && selectedItemData.type == "no results") {
                        return false;
                    }


                    
                    if (selectedItemData != undefined) {
                        var arr = jQuery('#ZoneAdmin_editView_fieldName_zip_code').val().split("\n");
                        var exists = (jQuery.inArray(selectedItemData, arr) > -1) ? true : false;
                        if (!exists) {
                            if (jQuery('#ZoneAdmin_editView_fieldName_zip_code').val() != '') {
                                jQuery('#ZoneAdmin_editView_fieldName_zip_code').val(jQuery('#ZoneAdmin_editView_fieldName_zip_code').val() + '\n' + selectedItemData);
                            } else {
                                jQuery('#ZoneAdmin_editView_fieldName_zip_code').val(selectedItemData);

                            }
                        }
                    }
                    
                    jQuery('#zip_search').val('');
                    return false;

                }
            });
        }
    },
    registerEvents: function () {
        this.showCheckboxes();
        this.hideBlocks();
        this.registerCheckboxChange();
        this.registerBeforeSave();
        this.registerZipAutoComplete();
        this._super();

    }

});
