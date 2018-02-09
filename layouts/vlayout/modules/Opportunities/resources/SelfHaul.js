Vtiger_Edit_Js('Self_Haul_Js', {
    activeInstance: null,
    getInstance: function() {
        if(this.activeInstance == null) {
            this.activeInstance = new Self_Haul_Js();
        }
        return this.activeInstance;
    },
    I: function() {
        return this.getInstance();
    }
}, {
    updateCallback: {func: function() {}, inst: null},
    onUpdate: function(callback, instance) {
        if(typeof callback == 'function') {
            this.updateCallback.func = callback;
            if(instance != null && typeof instance == 'object') {
                this.updateCallback.inst = instance;
            }else {
                console.warn('Attempted to set instance to a non-object.');
            }
        }else {
            console.error('Attempted to use non-function as a callback.');
        }
    },

    get: function(callback, instance) {
        var agentid = jQuery('select[name="agentid"]').val();
        var dataUrl = "index.php?module=AgentManager&action=GetSelfHaul&source=" + agentid;

        // Ensure callback is set.
        if(typeof callback != 'function') {
            // Blankout function to avoid errors.
            callback = function(data) {};
        }

        // Ensure instance is set.
        var thisI = instance;
        if(thisI == null || typeof thisI != 'object') {
            thisI = this;
        }
        AppConnector.request(dataUrl).then(function(data) {
            callback.call(thisI, data);
        });
    },

    default: function(data) {
        // Get Self Haul and react accordingly.
        this.get(function(data) {
            if (!$('[name="record"]').val()) {
                this.update(data);
            }else{
                this.updateVisibility(data);
            }
        });
    },

    updateVisibility: function(data) {
        if(data == null) {
            console.error("Called self haul update visibility function without data.");
            return;
        }
        var selfHaul = data.result;
        var selfHaulElm = jQuery('input[name="self_haul"][type="hidden"]');
        if (selfHaul.visible) {
            selfHaulElm.closest('tr').removeClass('hide');
            Vtiger_Edit_Js.showCell('self_haul');
        } else {
            selfHaulElm.closest('tr').addClass('hide');
            Vtiger_Edit_Js.hideCell('self_haul');
        }

        Vtiger_Edit_Js.setReadonly('self_haul', selfHaul.readonly);
    },

    update: function(data) {
        if(data == null) {
            console.error("Called self haul update function without data.");
            return;
        }
        if (!data.success) {
            console.error("Error Updating Self Haul: " + data.error.message);
            return;
        }

        // Update visibility of checkbox.
        // Breaking this out into a separate function is not the prettiest solution,
        // however the visibility must always be updated on pageload, while the value
        // should not, and this is much better then adding a flag in the AJAX return.
        this.updateVisibility(data);

        // Update value of checkbox.
        var selfHaul = data.result;
        var selfHaulElm = jQuery('input[name="self_haul"][type="hidden"]');
        if (selfHaul.visible) {
            // Set self haul to default value.
            selfHaulElm.val(selfHaul.value ? 1 : 0);
            jQuery('input:checkbox[name="self_haul"]').prop('checked', selfHaul.value);
        } else {
            // Uncheck self haul.
            selfHaulElm.val(0);
            jQuery('input:checkbox[name="self_haul"]').prop('checked', false);
        }

        // Run custom callback.
        this.updateCallback.func.call(this.updateCallback.inst, selfHaul);
    },

    // So unfortunately this needs to be separate from the Sales_Person_Js update.
    // This is because that is called on pageload, and this specific functionality shouldn't be called on pageload.
    // So instead of building custom logic into a class designed to be more generic, I'm doing this.
    registerAgentIdChange: function() {
        var thisI = this;
        $('[name="agentid"]').on('change', function() {
            thisI.get(thisI.update);
        });
    },

    // I guess I just forgot this? Huh...
    registerSelfHaulChange: function() {
        var thisI = this;
        $('[name="self_haul"]').on('change', function() {
            thisI.update({
                'success': true,
                'result': {
                    'visible': true,
                    'readonly': false,
                    'value': $(this).is(':checked')
                }
            });
        });
    },

    registerEvents: function() {
        this.registerAgentIdChange();
        this.registerSelfHaulChange();
        this.default();
    }
});
