Vtiger_Edit_Js("Storage_Edit_Js", {
}, {
    //This will store the editview form
    editViewForm: false,
   // operativeNameBlock: 'Operative Task Information',
    disabledDispatchBlocks: ['Number Of Days in Storage','Conversion to Perm Storage Date'],
    resourcesCheckCache: {},
    /**
     * This function will return the current form
     */
    getForm: function () {
        if (this.editViewForm == false) {
            this.editViewForm = jQuery('#EditView');
        }
        return this.editViewForm;
    },

    setDispatchFieldsReadOnly: function() {
        jQuery('[name="storage_sit_days"]').prop('readonly','readonly');
        jQuery('[name="storage_perm_days"]').prop('readonly','readonly');
        jQuery('[name="storage_sit_date_perm_storage"]').prop('readonly','readonly').prop('disabled',true);
        jQuery('[name="storage_sit_days_in_storage"]').prop('readonly','readonly');
        jQuery('[name="storage_perm_days_in_storage"]').prop('readonly','readonly');
    },

    setAddressFieldsReadOnly: function() {
        jQuery('[name="storage_address_1"]').prop('readonly','readonly');
        jQuery('[name="storage_address_2"]').prop('readonly','readonly');
        jQuery('[name="storage_city"]').prop('readonly','readonly');
        jQuery('[name="storage_zip"]').prop('readonly','readonly');
        jQuery('[name="storage_state"]').prop('readonly','readonly');
        jQuery('[name="storage_phone"]').prop('readonly','readonly');

    },

    registerSITRangeEvent : function() {
        jQuery('input[name*="_dateout"], input[name*="_datein"]').on('change', function(){
            var name = jQuery(this).attr('name');
            var isSIT = (name == 'storage_sit_datein' || name == 'storage_sit_dateout' || name == 'storage_sit_approved_datein'?true:false);
            var fieldName;
            if(isSIT){
                if(jQuery('input[name="storage_sit_approved_datein"').val() != ''){
                    fieldName = 'storage_sit_approved_datein';
                }else{
                    fieldName = 'storage_sit_datein';
                }
            }else{
                if(jQuery('input[name="storage_perm_approved_datein"').val() != ''){
                    fieldName = 'storage_perm_approved_datein';
                }else{
                    fieldName = 'storage_perm_datein';
                }
            }
            var dateIn = jQuery('input[name="'+fieldName+'"]').val();
            if(isSIT){
                fieldName = 'storage_sit_dateout';
            }else{
                fieldName = 'storage_perm_dateout';
            }
            dateOut= jQuery('input[name="'+fieldName+'"]').val();

            if(dateIn != 'undefined' && dateOut != 'undefined'){
                
                var date1 = new Date(dateIn);
                var date2 = new Date(dateOut);
                var timeDiff = Math.abs(date2.getTime() - date1.getTime());
                var diffDays = Math.ceil(timeDiff / (1000 * 3600 * 24)); 
                if(!isNaN(diffDays)){
                    if(isSIT){
                                jQuery('input[name="storage_sit_days_in_storage"]').val(diffDays);
                            }else{
                                jQuery('input[name="storage_perm_days_in_storage"]').val(diffDays);
                            }
                }
            }
        });
    },
    
    registerOptionChange: function() {
        var thisInstance = this;
        switch(jQuery('[name="storage_option"]').find('option:selected').val()){
            case 'SIT':
                jQuery('[name="storage_perm_datein"]').closest('table').addClass('hide');
                thisInstance.toggleStorageInformationFields('Perm','hide');
                break;
            case 'Perm':
                jQuery('[name="storage_sit_datein"]').closest('table').addClass('hide');
                thisInstance.toggleStorageInformationFields('SIT','hide');
                break;
            default:
                jQuery('[name="storage_sit_datein"]').closest('table').addClass('hide');
                jQuery('[name="storage_perm_datein"]').closest('table').addClass('hide');
                thisInstance.toggleStorageInformationFields('Perm','hide');
                thisInstance.toggleStorageInformationFields('SIT','hide');
                break;
        }
        jQuery('[name="storage_option"]').on('change',function(){
            var option = jQuery(this).find('option:selected').val();
            if(option == 'SIT'){
                jQuery('[name="storage_perm_datein"]').closest('table').addClass('hide');
                jQuery('[name="storage_sit_datein"]').closest('table').removeClass('hide');
                thisInstance.toggleStorageInformationFields('Perm','hide');
                thisInstance.toggleStorageInformationFields('SIT','show');
            }else if(option == 'Perm'){
                jQuery('[name="storage_perm_datein"]').closest('table').removeClass('hide');
                jQuery('[name="storage_sit_datein"]').closest('table').addClass('hide');
                thisInstance.toggleStorageInformationFields('SIT','hide');
                thisInstance.toggleStorageInformationFields('Perm','show');
            }
        });
    },
    toggleStorageInformationFields: function(option,state) {
        var toggleFields;
        var SitFields = ['storage_sit_authorization'];
        var PermFields = ['storage_perm_authorization'];
        if(option == 'SIT'){
            toggleFields = SitFields;
        }else{
            toggleFields = PermFields;
        }
        jQuery.each( toggleFields, function( key, value ) {
            if (value.match("^LBL")) { // IF the value is a label
                if(state == 'show') {
                    jQuery('[name="' + value + '"]').removeClass('hide');
                } else {
                    jQuery('[name="' + value + '"]').addClass('hide');
                }
            } else { //else its an input
                if(state == 'show') {
                    if(value == 'storage_perm_authorization') {
                        jQuery('[name="' + value + '"]').parent().removeClass('hide').closest('td').prev().find('label').removeClass('hide');
                        jQuery('[name="' + value + '"]').prop('disabled',true);
                    } else {
                        jQuery('[name="' + value + '"]').val('').parent().removeClass('hide').closest('td').prev().find('label').removeClass('hide');
                    }
                } else {
                    jQuery('[name="'+value+'"]').parent().addClass('hide').closest('td').prev().find('label').addClass('hide');
                }
            }

        });
    },
    getPopUpParams : function(container) {
		var params = {};
		var sourceModule = app.getModuleName();
		var popupReferenceModule = jQuery('input[name="popupReferenceModule"]',container).val();
		var sourceFieldElement = jQuery('input[class="sourceField"]',container);
		var sourceField = sourceFieldElement.attr('name');
		var sourceRecordElement = jQuery('input[name="record"]');
		var sourceRecordId = '';
		if(sourceRecordElement.length > 0) {
			sourceRecordId = sourceRecordElement.val();
		}
                
		var isMultiple = false;
		if(sourceFieldElement.data('multiple') == true){
			isMultiple = true;
		}

		var params = {
			'module' : popupReferenceModule,
			'src_module' : sourceModule,
			'src_field' : sourceField,
			'src_record' : sourceRecordId
		}
                
                if(jQuery('[name="storage_location"]') != undefined){
                    params.storage_location = jQuery('[name="storage_location"]').val();
                }
                
                 if(jQuery('[name="storage_orders"]') != undefined){
                    params.storage_orders = jQuery('[name="storage_orders"]').val();
                }

		if(isMultiple) {
			params.multi_select = true ;
		}
		return params;
	},
    
    toggleMilitaryEditFields: function(){
        var storageId = jQuery('[name="record"]').val();
        var orderId = jQuery('[name="storage_orders"]').val();
        var dataUrl = "index.php?module=Storage&action=GetBillingType&storageId="+storageId+"&orderId="+orderId;
                AppConnector.request(dataUrl).then(
                    function(data) {
                        if (data.success){
                                if(data.result.tariff_type != 'Military'){
                                    jQuery('input[name="storage_military_control"]').parent().addClass('hide').closest('td').prev().find('label').addClass('hide');
                                }
                        }
                    },
                    function(error){
                        console.dir('Error: '+error);
                    });
    },
    getApproveStorageDays: function(){
        jQuery(document).on('change','[name="storage_location"],[name="storage_orders"]', function(){
        var storage_type = jQuery('[name="storage_location"]').val();
        var orderId = jQuery('[name="storage_orders"]').val();
        var dataUrl = "index.php?module=Storage&action=getStorageDays&storage_type="+storage_type+"&orderId="+orderId;
                AppConnector.request(dataUrl).then(
                    function(data) {
                        if (data.success){
                                if(data.result.days){
                                    jQuery('input[name="storage_adays"]').val(data.result.days)
                                }
                        }
                    },
                    function(error){
                        console.dir('Error: '+error);
                    });
        });
        
    },
    updatePermStorageDate: function(){
      jQuery(document).on('change','[name="storage_sit_conv_perm_storage"]', function(){
          if(jQuery('[name="storage_sit_approved_datein"]').val() != ''){
              var dateIn = jQuery('[name="storage_sit_approved_datein"]').val();
          }else{
              var dateIn = jQuery('[name="storage_sit_datein"]').val();
          }
          
          var dat1= new Date(Date.parse(dateIn)); var dat2 = new Date(dat1.setDate(dat1.getDate()+1)); console.log(dat2.toString());
          
          					var dateFormat = jQuery('input[name="storage_sit_days_in_storage]').data('dateFormat');
						var y = dat2.getFullYear();
						var m = dat2.getMonth() + 1;
						var d = dat2.getDate();
						var userDate = '';
						for (var i = 0; i < 10; i++) {
							if (dateFormat[i] == 'y') {
								userDate += y[0];
								y = y.substr(1);
							} else if (dateFormat[i] == 'm') {
								userDate += m[0];
								m = m.substr(1);
							} else if (dateFormat[i] == 'd') {
								userDate += d[0];
								d = d.substr(1);
							} else if (dateFormat[i] == '-') {
								userDate += '-';
							}
						}
						jQuery('input[name="storage_sit_days_in_storage"]').val(userDate);
          
      });
    },

    populateAgentAddress: function() {
        var id = jQuery('input[name="storage_agent"]').val();
        var dataUrl = 'index.php?module=Storage&action=PopulateAgentData&agent_id='+id;
        AppConnector.request(dataUrl).then(
            function(data) {
                if (data.success){
                    jQuery('input[name="storage_city"]').val(data.result['city']).attr('readonly','readonly');
                    jQuery('input[name="storage_address_1"]').val(data.result['address1']).attr('readonly','readonly');
                    jQuery('input[name="storage_phone"]').val(data.result['phone']).attr('readonly', 'readonly');
                    jQuery('input[name="storage_state"]').val(data.result['state']).attr('readonly','readonly');
                    jQuery('input[name="storage_zip"]').val(data.result['zip']).attr('readonly','readonly');
                    jQuery('input[name="storage_address_2"]').val(data.result['address2']).attr('readonly','readonly');
                }
            },
            function(error){
                console.dir('Error: '+error);
            });

    },

    registerStorageAgentChange: function() {
        var thisInstance = this;
        jQuery('input:hidden[name="storage_agent"]').off(Vtiger_Edit_Js.referenceSelectionEvent).on(Vtiger_Edit_Js.referenceSelectionEvent, function () {
            thisInstance.populateAgentAddress();
        });

        jQuery('#Storage_editView_fieldName_storage_agent_clear').on('click', function () {
            thisInstance.clearAddressFields();
        });
    },

    clearAddressFields: function() {
            jQuery('[name="storage_address_1"]').val('').prop('readonly','readonly');
            jQuery('[name="storage_address_2"]').val('').prop('readonly','readonly');
            jQuery('[name="storage_city"]').val('').prop('readonly','readonly');
            jQuery('[name="storage_zip"]').val('').prop('readonly','readonly');
            jQuery('[name="storage_state"]').val('').prop('readonly','readonly');
            jQuery('[name="storage_phone"]').val('').prop('readonly','readonly');
    },

    
    registerBasicEvents: function (container) {
        this._super(container);
        this.setDispatchFieldsReadOnly();
        this.setAddressFieldsReadOnly();
        this.registerStorageAgentChange();
        this.populateAgentAddress();
        this.registerSITRangeEvent();
        this.registerOptionChange();
        this.toggleMilitaryEditFields();
        this.getApproveStorageDays();
    },
            'change' : function(event, ui) {
                var element = jQuery(this);
                //if you dont have readonly attribute means the user didnt select the item
                if(element.attr('readonly')== undefined) {
                    element.closest('td').find('.clearReferenceSelection').trigger('click');
                }
            },
            'open' : function(event,ui) {
                //To Make the menu come up in the case of quick create
                jQuery(this).data('autocomplete').menu.element.css('z-index','100001');

            }
        
        });
 
