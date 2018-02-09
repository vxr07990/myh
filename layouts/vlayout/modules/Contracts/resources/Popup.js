/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/
Vtiger_Popup_Js("Contracts_Popup_Js",{},{
	assigenedTo : false,

	/**
	 * Function to get the AssignedTo
	 */
	getAssignedTo : function(){
		return jQuery('select[name="assigned_user_id"]').val();
	},

	getListViewEntries: function(e) {
        var thisInstance = this;
        var row = jQuery(e.currentTarget);
        var dataUrl = row.data('url');
        if (typeof dataUrl != 'undefined') {
            dataUrl = dataUrl + '&currency_id=' + jQuery('#currencyId').val();
            AppConnector.request(dataUrl).then(
                function (data) {
                    for (var id in data) {
                        if (typeof data[id] == "object") {
                            var recordData = data[id];
                        }
                    }
                    thisInstance.done(recordData, thisInstance.getEventName());
                    e.preventDefault();
                },
                function (error, err) {

                }
            );
        } else {
            var id = row.data('id');
            var recordName = row.data('name');
            var recordInfo = row.data('info');
            var move_type = row.data('move_type');
            if (move_type) {
                jQuery('[name="move_type"]').val(move_type).trigger('liszt:updated').trigger('change');
                Vtiger_Edit_Js.setReadonly('move_type', true);
            } else {
                Vtiger_Edit_Js.setReadonly('move_type', false);
            }
            var response = {};
            response[id] = {'name': recordName, 'info': recordInfo};
            if (recordInfo && typeof recordInfo !== 'undefined' && typeof recordInfo['apn'] !== 'undefined') {
                response[id]['recordAPN'] = recordInfo['apn'];
            }
            thisInstance.done(response, thisInstance.getEventName());
            e.preventDefault();
        }
    },
	/**
	 * Function to get complete params
	 */
	getCompleteParams : function(){
		var params = {};
		params['view'] = this.getView();
		params['src_module'] = this.getSourceModule();
		params['src_record'] = this.getSourceRecord();
		params['src_field'] = this.getSourceField();
		params['search_key'] =  this.getSearchKey();
		params['search_value'] =  this.getSearchValue();
		params['orderby'] =  this.getOrderBy();
		params['sortorder'] =  this.getSortOrder();
		params['page'] = this.getPageNumber();
		params['related_parent_module'] = this.getRelatedParentModule();
		params['related_parent_id'] = this.getRelatedParentRecord();
		params['module'] = this.getSearchedModule();
		params['assignedTo'] = this.getAssignedTo();
        params['agentId'] = jQuery('select[name="agentid"]').val();
        params['account_id'] = jQuery('input[name="account_id"]').val();

		if(this.isMultiSelectMode()) {
			params['multi_select'] = true;
		}

        var filterSelectElement = jQuery('#popupRecordFilter');
        if(filterSelectElement.length > 0){
            params['cvid'] = filterSelectElement.val();
        }

		return params;
	}
});
