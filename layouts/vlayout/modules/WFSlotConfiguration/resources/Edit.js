Vtiger_Edit_Js("WFSlotConfiguration_Edit_Js", {},
    {
        registerCheckSlotCongifurantion : function () {
            if(typeof form == 'undefined') {
                form = this.getForm();
            }
            var slotconfigurations = jQuery('input[name^="slotpercentage"]',form);
            slotconfigurations.on('keyup', function (event) {
                var focus = $(this);
                var valInput = focus.val();
                if (valInput != parseInt(valInput))
                {
                    focus.val(parseInt(valInput));
                }
            });
        },

        registerRecordPreSaveEvent : function(form) {
            var thisInstance = this;
            if(typeof form == 'undefined') {
                form = this.getForm();
            }
            form.on(Vtiger_Edit_Js.recordPreSave, function(e, data) {
                var TotalSlots = 0;
                form.find('input[name^="slotpercentage"]').each(function (idx, elm) {
                   var slotVal=parseInt(jQuery(elm).val());
                    if(isNaN(slotVal)) {
                        slotVal = 0;
                    }
                    TotalSlots += slotVal;
                });
                if(TotalSlots != 100) {
                    var params = {
                        title: app.vtranslate('JS_ERROR'),
                        text: "Slots percentage must add up to 100.",
                        animation: 'show',
                        type: 'error'
                    };
                    Vtiger_Helper_Js.showPnotify(params);
                    e.preventDefault();
                }
                else {
                        form.submit();
                }
            })
        },
        registerEvents: function () {
            this._super();
            this.registerRecordPreSaveEvent();
            this.registerCheckSlotCongifurantion();
        },
    });
