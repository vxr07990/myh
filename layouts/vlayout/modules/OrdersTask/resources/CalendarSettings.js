jQuery.Class("CalendarSettings_Js", {}, {
    addRowButton: function(){
        jQuery('#add_color_percentage').on('click', function () {
            var nro = jQuery('table tbody tr').length - 1;
            jQuery('table tr:last').before('<tr><td class="fieldLabel" style="width:15%"><label class="muted pull-right marginRight10px">Porcentaje</label></td><td class="fieldValue" style="width:32.5%"><div class="row-fluid"><span class="span10"><input id="porcentaje'+nro+'" type="text" class="input-large nameField" name="porcentaje'+nro+'" value=""></span></div></td><td class="fieldLabel" style="width:15%"><label class="muted pull-right marginRight10px">Color</label></td><td class="fieldValue" style="width:32.5%"><div class="row-fluid"><span class="span12"><input id="color{$myId}" type="text" class="input-large nameField" name="color'+nro+'" value="#000000"><input type="text" id="pickcolor'+nro+'" value="#000000" style="cursor:pointer;background-color: #000000;color:white;width: 20%;margin-left: 1%;"></span></div></td><td style="width:5%"><i class="icon-remove"></i></td></tr>');
            jQuery('#pickcolor'+nro).ColorPicker({
                color: this.value,
                onShow: function (colpkr) {
                    $(colpkr).fadeIn(500);
                    return false;
                },
                onHide: function (colpkr) {
                    $(colpkr).fadeOut(500);
                    return false;
                },
                onSubmit: function(hsb, hex, rgb, el) {
                    $(el).val('#' + hex.toUpperCase()).css('background-color','#' + hex.toUpperCase()); 
                    $(el).ColorPickerHide();
                },
                onBeforeShow: function () {
                    $(this).ColorPickerSetColor(this.value);
                },
                onChange: function(hsb, hex, rgb,el){
                    $(el).val('#' + hex.toUpperCase()).css('background-color','#' + hex.toUpperCase()); 
                }
            });
        });
    },
    deleteRowButton: function(){
        jQuery(document).on( "click", ".icon-remove", function() {
            jQuery(this).closest('tr').remove();
        });
    },
    addColorPickers: function () {
        jQuery('input[id^=pickcolor]').each(function(){
            jQuery(this).ColorPicker({
                color: this.value,
                onShow: function (colpkr) {
                    $(colpkr).fadeIn(500);
                    return false;
                },
                onHide: function (colpkr) {
                    $(colpkr).fadeOut(500);
                    return false;
                },
                onSubmit: function(hsb, hex, rgb, el) {
                    $(el).val('#' + hex.toUpperCase()).css('background-color','#' + hex.toUpperCase()); 
                    $(el).ColorPickerHide();
                },
                onBeforeShow: function () {
                    $(this).ColorPickerSetColor(this.value);
                },
                onChange: function(hsb, hex, rgb,el){
                    $(el).val('#' + hex.toUpperCase()).css('background-color','#' + hex.toUpperCase()); 
                }
            });
        });
    },
    registerSaveEvent: function(){
        jQuery(document).on( "click", "#save_data", function() {
            var saturday = ($('#saturday').attr('checked') == 'checked' ? 'Yes' : 'No');
            var sunday = ($('#sunday').attr('checked') == 'checked' ? 'Yes' : 'No');
            var percentage1 = $('#spercentage1 :selected').val();
            var percentage2 = $('#spercentage2 :selected').val();
            var percentage3 = $('#spercentage3 :selected').val();
            var color1 = $('#pickcolor1').val();
            var color2 = $('#pickcolor2').val();
            var color3 = $('#pickcolor3').val();
            var progressIndicatorElement = jQuery.progressIndicator({});
            var params = {
                module: 'OrdersTask',
                action: 'CalendarSettingsSave',
                percentage1: percentage1,
                percentage2: percentage2,
                percentage3: percentage3,
                color1: color1,
                color2: color2,
                color3: color3,
                saturday: saturday,
                sunday: sunday
            };
            AppConnector.request(params).then(
                function (data) {
                    if (data.success) {
                        //var arr = eval(data.result);
                        progressIndicatorElement.progressIndicator({'mode': 'hide'});
                        var params = {
                            title: app.vtranslate('Data has been saved'),
                            text: data.result.msg,
                            width: '35%'
                        };
                        Vtiger_Helper_Js.showPnotify(params);
                        window.location.replace("index.php?module=OrdersTask&view=LocalDispatchCapacityCalendar");

                    }
                },
                function (error, err) {
                    alert('An error occurred, please try again.');
                }
            );
        });
    },
    registerEvents: function () {
        this.addColorPickers();
        this.registerSaveEvent();
        //this.addRowButton();
        //this.deleteRowButton();
    },
});

var instance;
jQuery(document).ready(function () {
    instance = new CalendarSettings_Js();
    instance.registerEvents();
});
