/* ********************************************************************************
 * The content of this file is subject to the Lead Company Lookup ("License");
 * You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is VTExperts.com
 * Portions created by VTExperts.com. are Copyright(C) VTExperts.com.
 * All Rights Reserved.
 * ****************************************************************************** */

jQuery(document).ready(function(){
    var sPageURL = window.location.search.substring(1);
    if(sPageURL.indexOf('module=Leads') != -1) {
        jQuery( "#Leads_editView_fieldName_company" ).autocomplete({
            source: function( request, response ) {
                jQuery.ajax({
                    url: "index.php?module=LeadCompanyLookup&action=ActionAjax",
                    dataType: "json",
                    data: {
                        key_search: request.term
                    },
                    success: function( data ) {
                        response(data);
                    }
                });
            },
            minLength: 1,
            select: function( event, ui ) {
            },
            change: function(event, ui) {
            }
        });
    }

    // haph86@gmail.com - #20358 - 11242015
    jQuery('.quickCreateModule').click(function(){
        function refresh() {
            if(jQuery('.quickCreateContent').length > 0) {
                jQuery( "#globalmodal #Leads_editView_fieldName_company" ).autocomplete({
                    source: function( request, response ) {
                        jQuery.ajax({
                            url: "index.php?module=LeadCompanyLookup&action=ActionAjax",
                            dataType: "json",
                            data: {
                                key_search: request.term
                            },
                            success: function( data ) {
                                response(data);
                            }
                        });
                    },
                    minLength: 1,
                    select: function( event, ui ) {
                    },
                    change: function(event, ui) {
                    }
                });
            } else {
                setTimeout(function(){
                    refresh();
                }, 50);
            }
        }
        refresh();
    });

});