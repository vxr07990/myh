Vtiger_Detail_Js("Inbox_Detail_Js",{},{
	updateAgentRequest: function(){
		jQuery('.updateStatus').click(function(e){
			e.preventDefault() 
			var url = 'index.php?module=Opportunities&action=ParticipatingAgentStatus&mode=update&paid='+jQuery(this).attr('paid')+'&inboxid='+jQuery(this).attr('inboxid')+'&status='+jQuery(this).attr('status');
			AppConnector.request(url).then(function(data){
				if(data.result[0]!==''){
					alert(data.result[0]);
				}
				window.location.reload();

			});
		});
	},
	registerEvents : function() {
			this._super();
			this.updateAgentRequest();
	}
});