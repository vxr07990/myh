jQuery.Class("TimeCalculator_RelatedEdit_Js", {
    getInstance: function() {
        return new TimeCalculator_RelatedEdit_Js();
    },
},
{
    registerEventForSubmitButton: function () {
        jQuery(document).on('click','.submitButton',function () {
            var progressInstance = jQuery.progressIndicator({
                'position' : 'html',
                'blockInfo' : {
                    'enabled' : true
                }
            });
            var form=jQuery(this).closest('form#detailView');
            var data=form.serialize();
            console.log(data);
            AppConnector.request(data).then(
                function(response){
                    progressInstance.progressIndicator({
                        'mode' : 'hide'
                    });
                    if(typeof response.result != 'undefined'){
                        var params = {
                            title : app.vtranslate('JS_MESSAGE'),
                            text: "Saved!",
                            animation: 'show',
                            type: 'info'
                        };
                        Vtiger_Helper_Js.showPnotify(params);
                        window.location.href= window.location.href;
                    } else {
                        var params = {
                            title : app.vtranslate('JS_MESSAGE'),
                            text: response.error.message,
                            animation: 'show',
                            type: 'error'
                        };
                        Vtiger_Helper_Js.showPnotify(params);
                    }
                }
            );
        }) ;
    },

    registerEventDeleteTimeCalculatorRecord : function () {
        jQuery('button.btn-delete-timecalculator').on('click',function () {
            var recordId = jQuery('input[name="recordId"]').val();
            var data = {
                'record':recordId,
                'action':'DeleteAjax',
                'module':'TimeCalculator',
            };
            AppConnector.request(data).then(
                function(){
                    window.location.href= window.location.href;
                }
            );
        });
    },

    registerEvents:function () {
        this.registerEventForSubmitButton();
        this.registerEventDeleteTimeCalculatorRecord();
    }
});

jQuery(document).ready(function() {
    var instance = TimeCalculator_RelatedEdit_Js.getInstance();
    instance.registerEvents();
});
