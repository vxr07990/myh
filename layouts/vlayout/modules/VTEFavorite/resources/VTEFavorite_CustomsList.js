/* ********************************************************************************
 * The content of this file is subject to the VTEFavorite ("License");
 * You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is VTExperts.com
 * Portions created by VTExperts.com. are Copyright(C) VTExperts.com.
 * All Rights Reserved.
 * ****************************************************************************** */


jQuery(document).ready(function(){

var ulFav=	document.getElementById('vteFav_customslist');

var actionParams = {
						"type":"GET",
						"dataType":"html",
						"data" : {						
							'module':'Contacts',
							'view':'List',
							'viewname':7							
						}
					};
	AppConnector.request(actionParams).then(
		function (data) {
			ulFav.innerHTML = data.getElementsByClassName('listViewEntriesTable')[0].outerHTML;
			
		},
		function (error) {

			//alert('error');
			//TODO : Handle error
		}
	);	
	
	
});

