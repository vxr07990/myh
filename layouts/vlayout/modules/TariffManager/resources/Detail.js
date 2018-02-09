Vtiger_Detail_Js("TariffManager_Detail_Js",{},{
	registerViewAllButtons: function() {
		jQuery('.viewAllAgents').off('click').on('click', function() {
			var elementId = jQuery(this).attr('id');
			jQuery.colorbox({inline:true, width:'500px', height:'90%', left:'15%', top:'-5%', href:'#'+elementId+'Div', onClosed:function(){jQuery(document.body).css({overflow:'auto'});}, onComplete:function(){jQuery(document.body).css({overflow:'hidden'});}});
		});
	},
	
	registerEvents: function() {
		this._super();
		this.registerViewAllButtons();
	}
});