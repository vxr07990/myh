/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/
Vtiger_Popup_Js("Cubesheets_Popup_Js", {}, {

    effectiveTariffData : false,

    /**
     * Function to get complete params
     */
    getCompleteParams: function () {
        var params = {};
        var potential_id = jQuery('input[name="potential_id"]').val();

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
        params['potential_id'] = potential_id;

        if (this.isMultiSelectMode()) {
            params['multi_select'] = true;
        }
        return params;
    },

    registerPicklistUpdate : function(tariffData) {
      var thisInstance = this;
      this.effectiveTariffData = tariffData;
        $('[name="move_type"], [name^="business_line"]').on('change',function(){
  			thisInstance.updateEffectiveTariffPicklist();
  		});
  	},

    updateEffectiveTariffPicklist: function() {
        var data = this.effectiveTariffData;
        var res = {};
        // National Account ??
        var currentBusinessLine = jQuery('[name="business_line_est"]').val();
        if (!currentBusinessLine) {
            currentBusinessLine = jQuery('[name="business_line_est2"]').val();
        }
        var allowInterstate = [
            'Interstate',
            'Interstate Move',
            'HHG - International Air',
            'HHG - International Sea',
            'HHG - International Surface',
            'International Land',
            'Auto Transportation',
            'Sirva Military',
            'Military'
        ].indexOf(currentBusinessLine) >= 0;

        var allowIntrastate = [
            'Intrastate'
        ].indexOf(currentBusinessLine) >= 0;

        var allowLocal = [
            'Local',
            'Local Move',
            'Intrastate',
            'Intrastate Move',
            'Commercial - Distribution',
            'Commercial - International Air',
            'Commercial - Record Storage',
            'Commercial - Storage',
            'Commercial - Asset Management',
            'Commercial - Project',
            'Work Space - MAC',
            'Work Space - Special Services',
            'Work Space - Commodities'
        ].indexOf(currentBusinessLine) >= 0;

        if($('[name="instance"]').val() == 'sirva' && !allowLocal) {
            allowLocal = currentBusinessLine.indexOf('Intrastate') > -1;
        }

        for(var k in data)
        {
            var d = data[k];
            if(d['is_managed_tariff'])
            {
                if(d['is_intrastate'])
                {
                    if(!allowIntrastate) {
                        continue;
                    }
                } else {
                    if(!allowInterstate) {
                        continue;
                    }
                }
            } else {
                if(!allowLocal)
                {
                    continue;
                }
            }
            res[d['tariff_id']] = d['tariff_name'];
        }
        Vtiger_Edit_Js.setPicklistOptions('effective_tariff', res);
    },
    updatePackingPicklist: function() {
        var tariffPackingOptions = jQuery('#tariffPackingOptions').val();
        if (!tariffPackingOptions.length) {
            return;
        }
      var data = jQuery.parseJSON(tariffPackingOptions);
			var res = {};
      for(var k in data)
      {
        res[k] = data[k]['service_name'];
      }
      Vtiger_Edit_Js.setPicklistOptions('cp_schedule', res);
      Vtiger_Edit_Js.setPicklistOptions('u_schedule', res);
    },

    disableMoveType: function() {
      jQuery('[name="move_type"]').attr('readonly',true);
    },
});
