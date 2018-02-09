/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

Vtiger_Detail_Js("ZoneAdmin_Detail_Js", {}, {
    setZipCodesHeight: function () {
        var html = jQuery('#ZoneAdmin_detailView_fieldValue_zip_code').html();
        jQuery('#ZoneAdmin_detailView_fieldValue_zip_code').html('<div style="height:200px; overflow-y:scroll;">' + html + '</div>');

    },
    hideBlocks: function () {
        if(jQuery('#ZoneAdmin_detailView_fieldValue_za_state').find('span').html().trim() == ''){
            jQuery('#ZoneAdmin_detailView_fieldValue_za_state').closest('td').hide().prev('td').hide();
        }
        
        if(jQuery('#ZoneAdmin_detailView_fieldValue_zip_code').find('span').html().trim() == ''){
             jQuery('#ZoneAdmin_detailView_fieldValue_zip_code').closest('tr').hide();
        }
    },
    
    registerEvents : function() {
		this.hideBlocks();
		this.setZipCodesHeight();
                this._super();
		
	}

});


