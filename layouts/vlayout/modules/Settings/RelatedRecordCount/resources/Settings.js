/* ********************************************************************************
 * The content of this file is subject to the Related Record Count ("License");
 * You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is VTExperts.com
 * Portions created by VTExperts.com. are Copyright(C) VTExperts.com.
 * All Rights Reserved.
 * ****************************************************************************** */

 var Settings_RelatedRecordCount_Js = {

    advanceFilterInstance : false,

    registerEditBtn : function() {
        var thisInstance = this;
        jQuery('.editButton').on('click', function(event){
            event.preventDefault();
            var url = jQuery(this).data('url');
            app.showModalWindow(null, url, function(){
                thisInstance.advanceFilterInstance = Vtiger_AdvanceFilter_Js.getInstance(jQuery('.filterContainer'));
                jQuery(document).find('.blockOverlay').unbind('click');
                thisInstance.registerColorPicker();
            });
        });
    },

    registerDeleteBtn : function() {
        var thisInstance = this;
        jQuery('.deleteButton').on('click', function(event){
            event.preventDefault();
            var url = jQuery(this).data('url');
            var message = app.vtranslate('LBL_DELETE_CONFIRMATION');
            Vtiger_Helper_Js.showConfirmationBox({'message' : message}).then(function(data) {
                var aDeferred = jQuery.Deferred();
                var progressIndicatorElement = jQuery.progressIndicator({
                    'position' : 'html',
                    'blockInfo' : {
                        'enabled' : true
                    }
                });
                AppConnector.request(url).then(
                    function(data) {
                        thisInstance.loadRecords().then(function(){
                            progressIndicatorElement.progressIndicator({
                                'mode' : 'hide'
                            });
                        });
                        aDeferred.resolve(data);
                    },
                    function(error,err){
                        progressIndicatorElement.progressIndicator({
                            'mode' : 'hide'
                        });
                        aDeferred.reject(error,err);
                    }
                );
                return aDeferred.promise();
                },
                function(error, err){
                }
            );
        });
    },

     registerDuplicateBtn : function() {
         var thisInstance = this;
         jQuery('.duplicateButton').on('click', function(event){
             event.preventDefault();
             var url = jQuery(this).data('url');
             var aDeferred = jQuery.Deferred();
             var progressIndicatorElement = jQuery.progressIndicator({
                 'position' : 'html',
                 'blockInfo' : {
                     'enabled' : true
                 }
             });
             AppConnector.request(url).then(
                 function(data) {
                     thisInstance.loadRecords().then(function(){
                         progressIndicatorElement.progressIndicator({
                             'mode' : 'hide'
                         });
                     });
                     aDeferred.resolve(data);
                 },
                 function(error,err){
                     progressIndicatorElement.progressIndicator({
                         'mode' : 'hide'
                     });
                     aDeferred.reject(error,err);
                 }
             );
             return aDeferred.promise();
         });
     },

    registerCloseBtn : function() {
        jQuery(document).on('click', '#CustomView .ui-condition-color-closer', function(event){
            event.preventDefault();
            app.hideModalWindow();
        });
    },

     registerModuleChange : function() {
         var thisInstance = this;
         jQuery(document).on('change', '#CustomView select[name=modulename]', function(event){
             event.preventDefault();
             jQuery('#advfilterlist').val('');
             var url = 'index.php?module=RelatedRecordCount&view=ModuleChangeAjax&parent=Settings&modulename='+jQuery(this).val();
             AppConnector.request(url).then(
                 function(data) {
                     var container = jQuery('#CustomView .vte-related-module-box');
                     container.find('.fieldValue').html(data);
                     app.changeSelectElementView(container);
                     jQuery('#CustomView select[name=related_modulename]').trigger('change');
                 }
             );
         });
     },

    registerRelatedModuleChange : function() {
        var thisInstance = this;
        jQuery(document).on('change', '#CustomView select[name=related_modulename]', function(event){
            event.preventDefault();
            jQuery('#advfilterlist').val('');
            var url = 'index.php?module=RelatedRecordCount&view=RelatedModuleChangeAjax&parent=Settings&related_modulename='+jQuery(this).val();
            AppConnector.request(url).then(
                function(data) {
                    jQuery('#CustomView .vte-advancefilter').html(data);
                    var container = jQuery('#CustomView .filterContainer');
                    thisInstance.advanceFilterInstance = Vtiger_AdvanceFilter_Js.getInstance(container);
                    app.changeSelectElementView(container);
                }
            );
        });
    },

    registerColorPicker : function(){
        jQuery(document).find('#CustomView input[name=color]').ColorPicker({
            color: '#0000ff',
            onShow: function (colpkr) {
                jQuery(colpkr).fadeIn(500);
                jQuery(colpkr).css({'zIndex': '10010'});
                return false;
            },
            onHide: function (colpkr) {
                jQuery(colpkr).fadeOut(500);
                return false;
            },
            onChange: function (hsb, hex, rgb) {
                jQuery('#CustomView input[name=color]').css('backgroundColor', '#' + hex);
                jQuery('#CustomView input[name=color]').val('#' + hex);
            }
        }).bind('keyup', function(){
            jQuery(this).ColorPickerSetColor(this.value);
        });
    },

    registerSaveBtn : function() {
        var thisInstance = this;
        jQuery(document).on('click', '#save-condition-color', function(event){
            event.preventDefault();
            var aDeferred = jQuery.Deferred();

            var advfilterlist = thisInstance.advanceFilterInstance.getValues();
            jQuery('#advfilterlist').val(JSON.stringify(advfilterlist));
            var form = jQuery('#CustomView');
            var formData = form.serializeFormData();
            var progressIndicatorElement = jQuery.progressIndicator({
                'position' : 'html',
                'blockInfo' : {
                    'enabled' : true
                }
            });
            AppConnector.request(formData).then(
                function(data) {
                    thisInstance.loadRecords().then(function(){
                        progressIndicatorElement.progressIndicator({
                            'mode' : 'hide'
                        });
                        app.hideModalWindow();
                    });
                    aDeferred.resolve(data);
                },
                function(error,err){
                    app.hideModalWindow();
                    aDeferred.reject(error,err);
                }
            );
            return aDeferred.promise();
        });
    },

    loadRecords : function(){
        var thisInstance = this;
        var aDeferred = jQuery.Deferred();
        var url = 'index.php?module=RelatedRecordCount&view=Settings&parent=Settings&ajax=true';
        AppConnector.request(url).then(
            function(data) {
                jQuery('.vte-related-record-count tbody').html(data);
                thisInstance.registerEditBtn();
                thisInstance.registerDeleteBtn();
                thisInstance.registerDuplicateBtn();
                aDeferred.resolve(data);
            },
            function(error,err){
                app.hideModalWindow();
                aDeferred.reject(error,err);
            }
        );
        return aDeferred.promise();
    },

    sortableRecords : function(){
        var thisInstance = this;
        var container = jQuery( ".vte-related-record-count tbody" );
        container.sortable({
            handle: ".icon-move",
            cursor: "move",
            update: function( event, ui ) {
                var records = [];
                jQuery(this).find('.icon-move').each(function(index, el){
                    records.push(jQuery(el).data('record'));
                });
                //update priority
                var aDeferred = jQuery.Deferred();
                var params = {};
                params['module'] = 'RelatedRecordCount';
                params['action'] = 'UpdatePriority';
                params['parent'] = 'Settings';
                params['records'] = records;
                AppConnector.request(params).then(
                    function(data) {
                        aDeferred.resolve(data);
                    },
                    function(error,err){
                        aDeferred.reject(error,err);
                    }
                );
                return aDeferred.promise();
            }
        });
        container.disableSelection();
    },

     unInstall : function() {
         var thisInstance = this;
         jQuery('#rel_uninstall_btn').on('click', function(){
             var message = app.vtranslate('LBL_DELETE_CONFIRMATION');
             Vtiger_Helper_Js.showConfirmationBox({'message' : message}).then(function(data) {
                 app.showModalWindow(null, 'index.php?module=RelatedRecordCount&action=uninstall&parent=Settings');
             });
         });
     },

    registerEvents : function() {
        this.registerEditBtn();
        this.registerDeleteBtn();
        this.registerDuplicateBtn();
        this.registerModuleChange();
        this.registerRelatedModuleChange();
        this.registerCloseBtn();
        this.registerSaveBtn();
        this.sortableRecords();
        this.unInstall();

    }

};
jQuery(document).ready(function(){
    Settings_RelatedRecordCount_Js.registerEvents();
});