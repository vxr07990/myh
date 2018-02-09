jQuery.Class("ColorSettings_Js", {}, {
    addRowButton: function(){
        jQuery('#add_colorsetting').on('click', function () {
            var nro = jQuery('table tbody tr').length - 1;
            jQuery('table tr:last').after('<tr><td class="fieldLabel" style="width:15%"><label class="muted pull-right marginRight10px">Days to PU Date</label></td><td class="fieldValue" style="width:32.5%"><div class="row-fluid"><span class="span10"><select id="sdiastopudate'+nro+'"><option value="-1">Overdue</option><option value="0">0</option><option value="1">1</option><option value="2">2</option><option value="3">3</option><option value="4">4</option><option value="5">5</option><option value="6">6</option><option value="7">7</option><option value="8">8</option><option value="9">9</option><option value="10">10</option><option value="11">11</option><option value="12">12</option><option value="13">13</option><option value="14">14</option><option value="15">15</option></select></span></div></td><td class="fieldLabel" style="width:15%"><label class="muted pull-right marginRight10px">Use the color</label></td><td class="fieldValue" style="width:32.5%"><div class="row-fluid"><span class="span12"><input type="text" id="pickcolor'+nro+'" value="#000000" style="cursor:pointer;background-color: #000000;color:white;width: 20%;margin-left: 1%;"></span></div></td><td style="width:5%;padding-top: 1.5%;padding-left: 1.5%;"><i class="icon-remove"></i></td></tr>');
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
    addSavEvent: function(){
        jQuery(document).on( "click", "#guardar_datos", function() {
            var jason = '[';
            $('#colorsettings tbody tr:not(.not_for_json)').each(function(){
                jason += '{"days":"'+$(this).find('[id^="sdiastopudate"] option:selected').val()+'","color":"'+$(this).find('[id^="pickcolor"]').val()+'"},';
            });
            jason = jason.slice(0, - 1); 
            jason += ']';
            instance.updateValues(jason);
        });
    },
    updateValues: function (data) {
        var assigned_color = $('#pickcolorasignacion').val();
        var apu_color = $('#pickcolorapu').val();
        var short_haul_color = $('#pickcolorshorthaulcolor').val();
        var overflow = $('#pickcoloroverflow').val();
        var dataUrl = "index.php?module=Orders&action=ColorSettingsSave&data_colores="+data+"&assigned_color="+assigned_color+"&apu_color="+apu_color+"&short_haul_color="+short_haul_color+"&overflow="+overflow;
        AppConnector.request(dataUrl).then(
            function (data) {
                if (data.success) {
                    var params = {
                        title: app.vtranslate('Data has been saved'),
                        text: data.result.msg,
                        width: '35%'
                    };
                    Vtiger_Helper_Js.showPnotify(params);
                    window.location.replace("index.php?module=Orders&view=LDDList");
                    
                }
            },
            function (error, err) {
                alert('An error occurred, please try again.');
            }
        );
    },
    registerEvents: function () {
        this.addColorPickers();
        this.addSavEvent();
        this.addRowButton();
        this.deleteRowButton();
    },
});

var instance;
jQuery(document).ready(function () {
    instance = new ColorSettings_Js();
    instance.registerEvents();
});
