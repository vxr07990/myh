/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/
Vtiger_Popup_Js("Employees_Popup_Js", {}, {
    /**
     * Function to get complete params
     */
    getCompleteParams: function () {
        // I wish this had checked for the field they wanted instead of just
        var params = {};
        params['view'] = this.getView();
        params['cvid'] = this.getCvIdByEmployeeType();
        params['src_module'] = this.getSourceModule();
        params['src_record'] = this.getSourceRecord();
        params['src_field'] = this.getSourceField();
        params['search_key'] = this.getSearchKey();
        params['search_value'] = this.getSearchValue();
        params['orderby'] = this.getOrderBy();
        params['sortorder'] = this.getSortOrder();
        params['page'] = this.getPageNumber();
        params['related_parent_module'] = this.getRelatedParentModule();
        params['related_parent_id'] = this.getRelatedParentRecord();
        params['module'] = this.getSearchedModule();
        params['agentid'] = Vtiger_Edit_Js.getAgentId();

        if (this.isMultiSelectMode()) {
            params['multi_select'] = true;
        }
        var filterSelectElement = jQuery('#popupRecordFilter');
        if(filterSelectElement.length > 0){
            params['cvid'] = filterSelectElement.val();
        }

        return params;
    },
    getCvIdByEmployeeType: function () {
        if(this.urlParam('employee_type')!=0 && this.urlParam('employee_type')!='Contractor'){
            return 68;
        }else{
            return 0;
        }

    },
    urlParam: function (name) {
        var results = new RegExp('[\?&amp;]' + name + '=([^&amp;#]*)').exec(window.location.href);
        if (results == null || results.length == 0) {
            return 0;
        } else {
            return results[1];
        }
    }
});
