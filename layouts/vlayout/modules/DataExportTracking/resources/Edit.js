/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

Vtiger_Edit_Js("Payments_Edit_Js",{

},{

    setReferenceFieldValue : function(container, params) {
        var sourceField = container.find('input[class="sourceField"]').attr('name');
        var fieldElement = container.find('input[name="'+sourceField+'"]');
        var sourceFieldDisplay = sourceField+"_display";
        var fieldDisplayElement = container.find('input[name="'+sourceFieldDisplay+'"]');
        var popupReferenceModule = container.find('input[name="popupReferenceModule"]').val();

        var selectedName = params.name;
        var id = params.id;

        fieldElement.val(id);
        fieldDisplayElement.val(selectedName).attr('readonly',true);
        fieldElement.trigger(Vtiger_Edit_Js.referenceSelectionEvent, {'source_module' : popupReferenceModule, 'record' : id, 'selectedName' : selectedName});

        fieldDisplayElement.validationEngine('closePrompt',fieldDisplayElement);

        // Auto fill Account, Contact when user select Invoice for Payment
        if(sourceField=='invoice' && popupReferenceModule=='Invoice' && app.getModuleName()=='Payments'){
            jQuery.ajax({
                url: 'index.php?module=Payments&mode=getInvoiceData&id='+id,
                data: {action:'Ajax'},
                type: 'POST',
                success: function(dataresponse) {
                    var invoiceData =JSON.parse(dataresponse.result);
                    var parentForm = fieldElement.closest('form');
                    parentForm.find('input[name="organization"]').val(invoiceData.accountid);
                    parentForm.find('input[name="organization_display"]').val(invoiceData.accountname);
                    parentForm.find('input[name="contact"]').val(invoiceData.contactid);
                    parentForm.find('input[name="contact_display"]').val(invoiceData.contact_name);
                }
            });
        }
    }
});