/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/
Vtiger_Popup_Js("Contacts_Popup_Js", {}, {
//    /**
//     * Function to get complete params
//     */
//    getCompleteParams: function () {
//        var params = {};
//        params['view'] = this.getView();
//        params['cvid'] = this.getCvIdByContactType();
//        params['src_module'] = this.getSourceModule();
//        params['src_record'] = this.getSourceRecord();
//        params['src_field'] = this.getSourceField();
//        params['search_key'] = this.getSearchKey();
//        params['search_value'] = this.getSearchValue();
//        params['orderby'] = this.getOrderBy();
//        params['sortorder'] = this.getSortOrder();
//        params['page'] = this.getPageNumber();
//        params['related_parent_module'] = this.getRelatedParentModule();
//        params['related_parent_id'] = this.getRelatedParentRecord();
//        params['module'] = app.getModuleName();
//
//        if (this.isMultiSelectMode()) {
//            params['multi_select'] = true;
//        }
//        return params;
//    },
    getCvIdByContactType: function () {
        if (this.urlParam('contact_type') != 0 && this.urlParam('contact_type') != 'Transferee') {
            return 50;
        } else if (this.urlParam('contact_type') != 0 && this.urlParam('contact_type') != 'Agent') {
            return 52;
        } else if (this.urlParam('contact_type') != 0 && this.urlParam('contact_type') != 'Accounts') {
            return 53;
        } else if (this.urlParam('contact_type') != 0 && this.urlParam('contact_type') != 'Vanlines') {
            return 51;
        } else {
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
