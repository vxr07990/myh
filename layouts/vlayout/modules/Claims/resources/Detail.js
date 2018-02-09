Vtiger_Detail_Js("Claims_Detail_Js",{},{
    registerMultiSPREvent: function(){
	jQuery(document).on("click","#multipleSPResp",function(){
	    var claimID = jQuery("#recordId").val();
	    var auxArr = new Array();
	    jQuery(".select_item:checked").each(function(){
		auxArr.push(jQuery(this).closest("tr").data("id"));
	    });
	    if(auxArr.length < 1){
		var notifyparams = {
		    title: app.vtranslate('ERROR'),
		    text: app.vtranslate('At least one item must be selected!'),
		    type: 'error',
		    width: '35%'
		};
		Vtiger_Helper_Js.showPnotify(notifyparams);
		return false;
	    }
	    auxArr = auxArr.join(",");
	    params = {
		'module': 'Claims',
		'view': 'ShowModals',
		'mode': 'showSPRespModal',
		'itemList': auxArr,
		'claimID': claimID,
	    }
	    AppConnector.request(params).then(
		function (data) {
		    app.showModalWindow(data, function (data) { 
			var CD = new Claims_Detail_Js();
			CD.registerModalEvents();
		    });
		}
	    );
	});
    },
    registerModalEvents: function () {
        instance = this;
	jQuery(document).on('change','.percentage',function () { //values between 0 to 100 %
	    var val = parseInt(jQuery(this).val());
	    if(val < 0) 
		jQuery(this).val('0');
	    if(val > 100) 
		jQuery(this).val('100');
	    if(parseInt(jQuery("#max-perc-available").val()) >= val){ //still has percent available
		jQuery("#max-perc-available").val(parseInt(jQuery("#max-perc-available").val()) - val);
	    }else{
		jQuery(this).val('');
	    }
	});
        jQuery('[name="saveModalSPR"]').click(function () {
            var items = jQuery("#items").val().split(',');
	    var infoArr = new Array();
	    jQuery("tr.participantRow:not(.defaultParticipant)").each(function(){
		var auxObj = new Object();
		auxObj.agentid = jQuery(this).find('[name^="participantAgentID"]').val();
		auxObj.agentname = jQuery(this).find('[name^="participantAgentName"]').val();
		auxObj.vendorid = jQuery(this).find('[name^="serviceProviderID"]').val();
		auxObj.vendorname = jQuery(this).find('[name^="serviceProviderName"]').val();
		auxObj.respon_percentage = jQuery(this).find('[name^="respon_percentage"]').val();
		//auxObj.respon_amount = jQuery(this).find('[name^="respon_amount"]').val();
		infoArr.push(auxObj);
	    });
            var progressIndicator = instance.showLoadingMessage('JS_UPDATING_ITEMS');
            params = {
                'module': 'Claims',
                'action': 'ClaimsActions',
                'mode': 'saveMassiveSPR',
                'itemsList': items,
		'infoArr': infoArr,
            }
            AppConnector.request(params).then(
		function (data) {
		    app.hideModalWindow();
		    var params = {
			title: app.vtranslate('Success'),
			text: app.vtranslate('Items Service Provider Responsability updated!'),
			type: 'success',
			width: '35%'
		    };
		    Vtiger_Helper_Js.showPnotify(params);
		    progressIndicator.progressIndicator({'mode': 'hide'});
		    jQuery(".select_item").prop("checked",false);
		},
		function (jqXHR, textStatus, errorThrown) {
		}
            );
        });
	jQuery('.addParticipant').click(function(){
	    var newRow = jQuery('.defaultParticipant').clone();
	    var sequence = jQuery("tr.participantRow:not(.defaultParticipant)").length + 1;

	    newRow.addClass('newParticipant');
	    newRow.removeClass('hide defaultParticipant');

	    newRow.find('.default').each(function(){
		jQuery(this).attr('name', jQuery(this).attr('name')+'_'+sequence);
		jQuery(this).removeClass('default change');
	    });

	    newRow.find('select[name="participantAgent"]').attr('name', 'participantAgent'+ sequence).addClass('chzn-select');
	    newRow.find('select[name="serviceProvider"]').attr('name', 'serviceProvider'+ sequence).addClass('chzn-select');
	    newRow.find('[name="participantAgentID"]').attr('name', 'participantAgentID'+ sequence);
	    newRow.find('[name="participantAgentName"]').attr('name', 'participantAgentName'+ sequence);
	    newRow.find('[name="serviceProviderID"]').attr('name', 'serviceProviderID'+ sequence);
	    newRow.find('[name="serviceProviderName"]').attr('name', 'serviceProviderName'+ sequence);
	    newRow.find('[name="respon_percentage"]').attr('name', 'respon_percentage'+ sequence);
	    newRow.find('[name="respon_amount"]').attr('name', 'respon_amount'+ sequence);
	    
	    jQuery('[name="servicePRTable"] tbody tr.totalsRow').before(newRow);
	    newRow.find('.chzn-select').chosen();
	});
	jQuery('.removeParticipant').click(function(){
	    jQuery(this).parent().parent().remove();
	});
    },  
    registerModalDropdownsEvents: function(){
	jQuery(document).on('change','.modalParticipantAgent',function(){
	    jQuery(this).closest("tr").find('[name^="participantAgentID"]').val(jQuery(this).find("option:selected").val());
	    jQuery(this).closest("tr").find('[name^="participantAgentName"]').val(jQuery(this).find("option:selected").text());
	});
	jQuery(document).on('change','.modalServiceProvider',function(){	    
	    jQuery(this).closest("tr").find('[name^="serviceProviderID"]').val(jQuery(this).find("option:selected").val());
	    jQuery(this).closest("tr").find('[name^="serviceProviderName"]').val(jQuery(this).find("option:selected").text()); 
	});
    },
    registerSelectAllEvent: function(){
	jQuery(document).on("click","#selectAllItems",function(){
	    var flag = (jQuery(this).prop("checked")) ? true : false;
	    jQuery(".select_item").prop("checked",flag);
	});
    },
    /*
    * Function to register the list view delete record click event
    */
    registerDeleteRecordClickEvent: function(){
            var thisInstance = this;
            var listViewContentDiv = jQuery('#claimitems_list');
            listViewContentDiv.on('click','.deleteRecordButton',function(e){
                    var elem = jQuery(e.currentTarget);
                    var recordId = elem.closest('tr').data('id');
                    Vtiger_List_Js.deleteRecord(recordId);
                    e.stopPropagation();
                    elem.closest('tr').hide();
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
    registerEvents : function() {
	this._super();
        this.registerDeleteRecordClickEvent();
	this.registerSelectAllEvent();
	this.registerMultiSPREvent();
	this.registerModalDropdownsEvents();
    }
});