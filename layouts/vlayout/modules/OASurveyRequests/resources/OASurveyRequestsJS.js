var Vtiger_OASurveyRequests_Js = {
    registerAcceptReject: function(){
		thisInstance = this
        jQuery(document).on("click",".accept",function(){
            var url = {
                module: 'OASurveyRequests',
                action: 'OASurveyRequestsHandler',
                mode: 'ajaxHandler',
                status: '1',
                id: jQuery(this).attr("id").replace("accept_",""),
                agent_id: jQuery(this).data("agentid"),
                crmid: jQuery(this).data("crmid"),
                agent_type: jQuery(this).data("agenttype"),
            };
			buttonElement = jQuery(this);
            AppConnector.request(url).then(function(data){
                if(data == 'Accepted'){
                    params = {'text' : 'Outgoing Success','type' : 'info'}   
                }else{
                    params = {'text' : 'Outgoing Failure','type' : 'error'} 
                }
                /*setTimeout(function () {
                    Vtiger_Helper_Js.showPnotify(params);
                }, 5000);*/
            });
			//console.dir(buttonElement.closest('.ui-pnotify-container').find('.ui-pnotify-closer'));
			//close the pop-up workaround
			buttonElement.closest('.ui-pnotify-container').find('.ui-pnotify-closer').trigger('click');
        });
        jQuery(document).on("click",".reject",function(){
            var url = {
                module: 'OASurveyRequests',
                action: 'OASurveyRequestsHandler',
                mode: 'ajaxHandler',
                status: '3',
                id: jQuery(this).attr("id").replace("reject_",""),
                agent_id: jQuery(this).data("agentid"),
                crmid: jQuery(this).data("crmid"),
                agent_type: jQuery(this).data("agenttype"),
            };
			buttonElement = jQuery(this);
            AppConnector.request(url).then(function(data){
                if(data == 'Declined'){
                    params = {'text' : 'Outgoing Success','type' : 'info'}   
                }else{
                    params = {'text' : 'Outgoing Failure','type' : 'error'} 
                }
				//close the pop-up workaround
                /*setTimeout(function () {
                    Vtiger_Helper_Js.showPnotify(params);
                }, 5000);*/
            });
			//console.dir(buttonElement.closest('.ui-pnotify-container').find('.ui-pnotify-closer'));
			//close the pop-up workaround
			buttonElement.closest('.ui-pnotify-container').find('.ui-pnotify-closer').trigger('click');
        });
    },
    registerMsgPopup : function() {
        Vtiger_OASurveyRequests_Js.requestGetMsgs();
    },
    requestGetMsgs : function() {
        var url = 'index.php?module=OASurveyRequests&action=getRequests&mode=getRequestsForUser';
        AppConnector.request(url).then(function(data){
            if(data.success && data.result) {
                for(i=0; i< data.result.length; i++) {
                    var record  = data.result[i];
                    if(jQuery('#messaging_s'+record.id+'').size()== 0 )
                        Vtiger_OASurveyRequests_Js.showIncomingMsgPopup(record);
                    }
                }
        });
    },
    showIncomingMsgPopup : function(record) {
        var params = {
            title: "Incoming Message",
            text: '<div class="row-fluid oasurveyrequest" id="messaging_'+record.id+'" messagingid='+record.id+' style="color:black">\n\
                <span class="span12" id="caller" value="'+record.user_requestor+'"><strong>Message from: </strong>'+record.user_requestor+'</span>\n\
                <span class="span12" id="related_record" value="'+record.related_record+'"><strong>Related Record: </strong><a href="index.php?module='+record.modulo+'&view=Detail&record='+record.crmid+'">'+record.related_record+'</a></span>\n\
                <span class="span12" id="related_record" value="'+record.message+'"><strong>Message: </strong>'+record.message+'</span>\n\
                <div class="pull-right"><button class="btn btn-success accept" type="submit" id="accept_'+record.id+'" data-agentid="'+record.agent_id+'" data-crmid="'+record.crmid+'" data-agenttype="'+record.agent_type+'"><strong>Accept</strong></button><button class="btn btn-danger reject" type="submit" id="reject_'+record.id+'" data-agentid="'+record.agent_id+'" data-crmid="'+record.crmid+'" data-agenttype="'+record.agent_type+'"><strong>Reject</strong></button></div>\n\
                </div>',
            width: '28%',
            min_height: '75px',
            addclass:'vtCall',
            icon: 'vtCall-icon',
            hide:false,
            closer:true,
            type:'info',
            after_open:function(p) {
                jQuery(p).data('info', record);
            }
        };
        Vtiger_Helper_Js.showPnotify(params);
    },
     registerEvents : function(){
        var thisInstance = this;
        Vtiger_OASurveyRequests_Js.registerMsgPopup();
        thisInstance.registerAcceptReject();
    }  
}

//On Page Load
jQuery(document).ready(function() {
    Vtiger_OASurveyRequests_Js.registerEvents();
});