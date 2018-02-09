Vtiger_Popup_Js("Claims_Popup_Js", {}, {
    /**
     * Function to get complete params
     */
    getCompleteParams: function () {
        var params = {};
        params['view'] = this.getView();
        params['claims_order'] = this.getClaimsOrder();
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

        if (this.isMultiSelectMode()) {
            params['multi_select'] = true;
        }
        var filterSelectElement = jQuery('#popupRecordFilter');
        if(filterSelectElement.length > 0){
            params['cvid'] = filterSelectElement.val();
        }
        return params;
    },

    getView : function(){
            return 'PopupAjax';
    },
    getClaimsOrder : function(){
        var val = jQuery('[name="claims_order"]').val();
        return val;
    },
    getSourceRecord : function(){
        var val = jQuery('[name="claims_order"]').val();
        return val;
    },
    getSearchedModule : function(){
		if(this.searchedModule == false){
			this.searchedModule = jQuery('#popupPageContainer #module').val();
		}
		return this.searchedModule;
	},

	/**
	 * Function to get source field
	 */
	getSourceField : function(){
		if(this.sourceField == false){
			this.sourceField = jQuery('#sourceField').val();
		}
		return this.sourceField;
	},

	/**
	 * Function to get related parent module
	 */
	getRelatedParentModule : function(){
		if(this.relatedParentModule == false){
			this.relatedParentModule = jQuery('#relatedParentModule').val();
		}
		return this.relatedParentModule;
	},
	/**
	 * Function to get related parent id
	 */
	getRelatedParentRecord : function(){
		if(this.relatedParentRecord == false){
			this.relatedParentRecord = jQuery('#relatedParentId').val();
		}
		return this.relatedParentRecord;
	},

	/**
	 * Function to get Search key
	 */
	getSearchKey : function(){
		return jQuery('#searchableColumnsList').val();
	},

	/**
	 * Function to get Search value
	 */
	getSearchValue : function(){
		return jQuery('#searchvalue').val();
	},

	/**
	 * Function to get Order by
	 */
	getOrderBy : function(){
		return jQuery('#orderBy').val();
	},

	/**
	 * Function to get Sort Order
	 */
	getSortOrder : function(){
			return jQuery("#sortOrder").val();
	},

	/**
	 * Function to get Page Number
	 */
	getPageNumber : function(){
		return jQuery('#pageNumber').val();
	},

	getPopupPageContainer : function(){
		if(this.popupPageContentsContainer == false) {
			this.popupPageContentsContainer = jQuery('#popupPageContainer');
		}
		return this.popupPageContentsContainer;

	}

});
