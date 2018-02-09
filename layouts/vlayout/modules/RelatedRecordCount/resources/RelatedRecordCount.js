/* ********************************************************************************
 * The content of this file is subject to the Related Record Count ("License");
 * You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is VTExperts.com
 * Portions created by VTExperts.com. are Copyright(C) VTExperts.com.
 * All Rights Reserved.
 * ****************************************************************************** */ 
var Vtiger_RelatedRecordCountVTE_Js = {

    container : null,

    init : function(){
        if(this.validListViewData()){
            this.relatedRecordCount();
        }
    },

    /*relatedRecordCount : function(){
        var thisInstance = this;
        thisInstance.container.find('li').each(function(){
            var relatedElement = jQuery(this);
            var relatedUrl = relatedElement.data('url');
            if(relatedUrl.indexOf('index.php')==-1){
                var url = thisInstance.parseUrl(relatedUrl);
                if(url['relatedModule'] !== 'undefined'){
                    var params = {};
                    params['relatedModule'] = url['relatedModule'];
                    params['pmodule'] = url['module'];
                    params['module'] = 'RelatedRecordCount';
                    params['view'] = 'GetCount';
                    params['record'] = url['record'];
                    thisInstance.getCount(relatedElement, params);
                }
            }
        });
    },*/

    relatedRecordCount : function(){
        var thisInstance = this;
        thisInstance.container.find('li').each(function(){
            var relatedElement = jQuery(this);
            var relatedUrl = relatedElement.data('url');
            if(relatedUrl){
                var url = thisInstance.parseUrl(relatedUrl);
                if(url['mode']=='showAllComments' || url['mode']=='showRecentActivities' || url['mode']=='showRelatedList'){
                    var params = {};
                    params['module'] = 'RelatedRecordCount';
                    params['view'] = 'GetCount';
                    params['record'] = url['record'];
                    params['mode'] = url['mode'];
                    if(url['mode']=='showRelatedList'){
                        params['relatedModule'] = url['relatedModule'];
                        params['pmodule'] = url['module'];
                    }
                    thisInstance.getCount(relatedElement, params);
                }
            }
        });
    },

    getCount : function(relatedElement, params){
        var thisInstance = this;
        var aDeferred = jQuery.Deferred();
        AppConnector.request(params).then(
            function(data){
                var response = jQuery.parseJSON(data);
                if(response.success){
                    if(response.result){
                        thisInstance.updateCount(relatedElement, response.result);
                    }
                }
                aDeferred.resolve(data);
            },

            function(error){
                aDeferred.reject(error);
            }
        );

        return aDeferred.promise();
    },

    updateCount : function(relatedElement, count_data){
        var countLabel = '';
        var len = count_data.length;
        countLabel += '<span class="relatedrecordcount"> (';
        for(var i=0; i<len; i++){
            countLabel += '<span style="color: '+count_data[i].color+'">'+count_data[i].label+'</span>';
            if(i < len-1){
                countLabel += '&nbsp;';
            }
        }
        countLabel += ')</span>';
        relatedElement.find('.relatedrecordcount').remove();
        relatedElement.find('strong').append(countLabel);
        relatedElement.find('a').removeClass('textOverflowEllipsis');
    },

    parseUrl : function(queryString){
        var params = {}, queries, temp, i, l;
        // Split into key/value pairs
        queries = queryString.split("&");
        // Convert the array of strings into an object
        for ( i = 0, l = queries.length; i < l; i++ ) {
            temp = queries[i].split('=');
            params[temp[0]] = temp[1];
        }

        return params;
    },

    validListViewData : function(){
        var viewName = app.getViewName();
        if(viewName == 'Detail'){
            if(jQuery('.detailViewContainer .related ul li').length > 0){
                this.container = jQuery('.detailViewContainer .related ul');
                return true;
            }
        }
        return false;
    }
}

jQuery(document).ready(function () {
    Vtiger_RelatedRecordCountVTE_Js.init();
    app.listenPostAjaxReady(function() {
        Vtiger_RelatedRecordCountVTE_Js.init();
    });
});