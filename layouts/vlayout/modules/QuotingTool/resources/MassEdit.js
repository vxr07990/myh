/**
 * Created by HoangHai on 25/05/2017.
 */
Emails_MassEdit_Js("QuotingTool_MassEdit_Js", {}, {
    registerEventsForToField : function(){
        var thisInstance = this;
        jQuery("#quotingtool_emailtemplate").on('click','.selectEmail',function(e){
            var moduleSelected = jQuery('.emailModulesList').val();
            var parentElem = jQuery(e.target).closest('.toEmailField');
            var sourceModule = jQuery('[name=module]').val();
            var params = {
                'module' : moduleSelected,
                'src_module' : sourceModule,
                'view': 'EmailsRelatedModulePopup'
            }
            var popupInstance =Vtiger_Popup_Js.getInstance();
            popupInstance.show(params, function(data){
                var responseData = JSON.parse(data);
                for(var id in responseData){
                    var data = {
                        'name' : responseData[id].name,
                        'id' : id,
                        'emailid' : responseData[id].info.email
                    }
                    thisInstance.setReferenceFieldValue(parentElem, data);
                    thisInstance.addToEmailAddressData(data);
                    thisInstance.appendToSelectedIds(id);
                    thisInstance.addToEmails(data);
                }
            },'relatedEmailModules');
        });


        jQuery("#quotingtool_emailtemplate").on('click','[name="clearToEmailField"]',function(e){
            var element = jQuery(e.currentTarget);
            element.closest('div.toEmailField').find('.sourceField').val('');
            jQuery("#quotingtool_emailtemplate").find('[name="toemailinfo"]').val(JSON.stringify(new Array()));
            jQuery("#quotingtool_emailtemplate").find('[name="selected_ids"]').val(JSON.stringify(new Array()));
            jQuery("#quotingtool_emailtemplate").find('[name="to"]').val(JSON.stringify(new Array()));

            var preloadData = [];
            thisInstance.setPreloadData(preloadData);
            jQuery("#quotingtool_emailtemplate").find('#emailField').select2('data', preloadData);
        });


    },
    addToEmails : function(mailInfo){
        var toEmails = jQuery("#quotingtool_emailtemplate").find('[name="to"]');
        var value = JSON.parse(toEmails.val());
        if(value == ""){
            value = new Array();
        }
        value.push(mailInfo.emailid);
        toEmails.val(JSON.stringify(value));
    },
    appendToSelectedIds : function(selectedId) {
        var selectedIdElement = jQuery("#quotingtool_emailtemplate").find('[name="selected_ids"]');
        var previousValue = '';
        if(JSON.parse(selectedIdElement.val()) != '') {
            previousValue = JSON.parse(selectedIdElement.val());
            //If value doesn't exist then insert into an array
            if(jQuery.inArray(selectedId,previousValue) === -1){
                previousValue.push(selectedId);
            }
        } else {
            previousValue = new Array(selectedId);
        }
        selectedIdElement.val(JSON.stringify(previousValue));

    },
    addToEmailAddressData : function(mailInfo) {
        var mailInfoElement = jQuery("#quotingtool_emailtemplate").find('[name="toemailinfo"]');
        var existingToMailInfo = JSON.parse(mailInfoElement.val());
        if(typeof existingToMailInfo.length != 'undefined') {
            existingToMailInfo = {};
        }
        //If same record having two different email id's then it should be appended to
        //existing email id
        if(existingToMailInfo.hasOwnProperty(mailInfo.id) === true){
            var existingValues = existingToMailInfo[mailInfo.id];
            var newValue = new Array(mailInfo.emailid);
            existingToMailInfo[mailInfo.id] = jQuery.merge(existingValues,newValue);
        } else {
            existingToMailInfo[mailInfo.id] = new Array(mailInfo.emailid);
        }
        mailInfoElement.val(JSON.stringify(existingToMailInfo));
    },

    /**
     * Function which will handle the reference auto complete event registrations
     * @params - container <jQuery> - element in which auto complete fields needs to be searched
     */
    registerAutoCompleteFields : function(container) {
        var thisInstance = this;

        container.find('#emailField').select2({
            minimumInputLength: 3,
            closeOnSelect : false,

            tags : [],
            tokenSeparators: [","],

            createSearchChoice : function(term) {
                return {id: term, text: term};
            },

            ajax : {
                'url' : 'index.php?module=Emails&action=BasicAjax',
                'dataType' : 'json',
                'data' : function(term,page){
                    var data = {};
                    data['searchValue'] = term;
                    return data;
                },
                'results' : function(data){
                    var finalResult = [];
                    var results = data.result;
                    var resultData = new Array();
                    for(var moduleName in results) {
                        var moduleResult = [];
                        moduleResult.text = moduleName;

                        var children = new Array();
                        for(var recordId in data.result[moduleName]) {
                            var emailInfo = data.result[moduleName][recordId];
                            for (var i in emailInfo) {
                                var childrenInfo = [];
                                childrenInfo.recordId = recordId;
                                childrenInfo.id = emailInfo[i].value;
                                childrenInfo.text = emailInfo[i].label;
                                children.push(childrenInfo);
                            }
                        }
                        moduleResult.children = children;
                        resultData.push(moduleResult);
                    }
                    finalResult.results = resultData;
                    return finalResult;
                },
                transport : function(params) {
                    return jQuery.ajax(params);
                }
            }

        }).on("change", function (selectedData) {
            var addedElement = selectedData.added;
            if (typeof addedElement != 'undefined') {
                var data = {
                    'id' : addedElement.recordId,
                    'name' : addedElement.text,
                    'emailid' : addedElement.id
                }
                thisInstance.addToEmails(data);
                if (typeof addedElement.recordId != 'undefined') {
                    thisInstance.addToEmailAddressData(data);
                    thisInstance.appendToSelectedIds(addedElement.recordId);
                }

                var preloadData = thisInstance.getPreloadData();
                var emailInfo = {
                    'id' : addedElement.id
                }
                if (typeof addedElement.recordId != 'undefined') {
                    emailInfo['text'] = addedElement.text;
                    emailInfo['recordId'] = addedElement.recordId;
                } else {
                    emailInfo['text'] = addedElement.id;
                }
                preloadData.push(emailInfo);
                thisInstance.setPreloadData(preloadData);
            }

            var removedElement = selectedData.removed;
            if (typeof removedElement != 'undefined') {
                var data = {
                    'id' : removedElement.recordId,
                    'name' : removedElement.text,
                    'emailid' : removedElement.id
                }
                thisInstance.removeFromEmails(data);
                if (typeof removedElement.recordId != 'undefined') {
                    thisInstance.removeFromEmailAddressData(data);
                    thisInstance.removeFromSelectedIds(removedElement.recordId);
                }

                var preloadData = thisInstance.getPreloadData();
                var updatedPreloadData = [];
                for(var i in preloadData) {
                    var preloadDataInfo = preloadData[i];
                    var skip = false;
                    if (removedElement.id == preloadDataInfo.id) {
                        skip = true;
                    }
                    if (skip == false) {
                        updatedPreloadData.push(preloadDataInfo);
                    }
                }
                thisInstance.setPreloadData(updatedPreloadData);
            }
        });

        container.find('#emailField').select2("container").find("ul.select2-choices").sortable({
            containment: 'parent',
            start: function(){
                container.find('#emailField').select2("onSortStart");
            },
            update: function(){
                container.find('#emailField').select2("onSortEnd");
            }
        });

        var toEmailNamesList = JSON.parse(container.find('[name="toMailNamesList"]').val());
        var toEmailInfo = JSON.parse(container.find('[name="toemailinfo"]').val());
        var toEmails = container.find('[name="toEmail"]').val();
        var toFieldValues = Array();
        if (toEmails.length > 0) {
            toFieldValues = toEmails.split(',');
        }

        var preloadData = thisInstance.getPreloadData();
        if (typeof toEmailInfo != 'undefined') {
            for(var key in toEmailInfo) {
                if (toEmailNamesList.hasOwnProperty(key)) {
                    for (var i in toEmailNamesList[key]) {
                        var emailInfo = [];
                        var emailId = toEmailNamesList[key][i].value;
                        var emailInfo = {
                            'recordId' : key,
                            'id' : emailId,
                            'text' : toEmailNamesList[key][i].label+' <b>('+emailId+')</b>'
                        }
                        preloadData.push(emailInfo);
                        if (jQuery.inArray(emailId, toFieldValues) != -1) {
                            var index = toFieldValues.indexOf(emailId);
                            if (index !== -1) {
                                toFieldValues.splice(index, 1);
                            }
                        }
                    }
                }
            }
        }
        if (typeof toFieldValues != 'undefined') {
            for(var i in toFieldValues) {
                var emailId = toFieldValues[i];
                var emailInfo = {
                    'id' : emailId,
                    'text' : emailId
                }
                preloadData.push(emailInfo);
            }
        }
        if (typeof preloadData != 'undefined') {
            var newPreloadData = [];
            for(var i=0;i<preloadData.length;++i)
            {
                if(typeof preloadData[i]['id'] == 'undefined'
                    || typeof preloadData[i]['text'] == 'undefined')
                {
                    continue;
                }
                if(preloadData[i]['text'].length == 0)
                {
                    continue;
                }
                newPreloadData.push(preloadData[i]);
            }
            thisInstance.setPreloadData(newPreloadData);
            container.find('#emailField').select2('data', newPreloadData);
        }

    },
    removeFromEmails : function(mailInfo){
        var toEmails = jQuery("#quotingtool_emailtemplate").find('[name="to"]');
        var previousValue = JSON.parse(toEmails.val());

        var updatedValue = [];
        for (var i in previousValue) {
            var email = previousValue[i];
            var skip = false;
            if (email == mailInfo.emailid) {
                skip = true;
            }
            if (skip == false) {
                updatedValue.push(email);
            }
        }
        toEmails.val(JSON.stringify(updatedValue));
    },
    removeFromEmailAddressData : function(mailInfo) {
        var mailInfoElement = jQuery("#quotingtool_emailtemplate").find('[name="toemailinfo"]');
        var previousValue = JSON.parse(mailInfoElement.val());
        var elementSize = previousValue[mailInfo.id].length;
        var emailAddress = mailInfo.emailid;
        var selectedId = mailInfo.id;
        //If element length is not more than two delete existing record.
        if(elementSize < 2){
            delete previousValue[selectedId];
        } else {
            // Update toemailinfo hidden element value
            var newValue;
            var reserveValue = previousValue[selectedId];
            delete previousValue[selectedId];
            //Remove value from an array and return the resultant array
            newValue = jQuery.grep(reserveValue, function(value) {
                return value != emailAddress;
            });
            previousValue[selectedId] = newValue;
            //update toemailnameslist hidden element value
        }
        mailInfoElement.val(JSON.stringify(previousValue));
    },

    removeFromSelectedIds : function(selectedId) {
        var selectedIdElement = jQuery("#quotingtool_emailtemplate").find('[name="selected_ids"]');
        var previousValue = JSON.parse(selectedIdElement.val());
        var mailInfoElement = jQuery("#quotingtool_emailtemplate").find('[name="toemailinfo"]');
        var mailAddress = JSON.parse(mailInfoElement.val());
        var elements  = mailAddress[selectedId];
        var noOfEmailAddress = 0;
        if(typeof elements != 'undefined') {
            noOfEmailAddress = elements.length;
        }

        //Don't remove id from selected_ids if element is having more than two email id's
        if(noOfEmailAddress < 2){
            var updatedValue = [];
            for (var i in previousValue) {
                var id = previousValue[i];
                var skip = false;
                if (id == selectedId) {
                    skip = true;
                }
                if (skip == false) {
                    updatedValue.push(id);
                }
            }
            selectedIdElement.val(JSON.stringify(updatedValue));
        }
    },


    registerEvents: function () {
        var composeEmailForm = jQuery("#quotingtool_emailtemplate");
        if(composeEmailForm.length >0) {
            this._super();
            this.registerEventsForToField();
            this.registerAutoCompleteFields(composeEmailForm);
        }
    }
});
