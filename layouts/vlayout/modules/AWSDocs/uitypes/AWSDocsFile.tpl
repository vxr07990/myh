    {*<!--
/*********************************************************************************
** The contents of this file are subject to the vtiger CRM Public License Version 1.0
* ("License"); You may not use this file except in compliance with the License
* The Original Code is:  vtiger CRM Open Source
* The Initial Developer of the Original Code is vtiger.
* Portions created by vtiger are Copyright (C) vtiger.
* All Rights Reserved.
*
********************************************************************************/
-->*}
{strip}


{assign var="FIELD_INFO" value=Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($FIELD_MODEL->getFieldInfo()))}

    <div class="fileUploadContainer">

        <input type="file" class="input-large {if $FIELD_MODEL->isNameField()}nameField{/if}" data-validation-engine="validate[{if $FIELD_MODEL->isMandatory() eq true}required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" name="{$FIELD_MODEL->getFieldName()}"
               value="{$FIELD_MODEL->fieldvalue}" data-fieldinfo='{$FIELD_INFO}'/>

        <div class="uploadedFileDetails ">
            <div class="uploadedFileSize"></div>
            <div class="uploadedFileName">
                {if !empty({$FIELD_MODEL->fieldvalue})}
                    [{$FIELD_MODEL->fieldvalue}]
                {/if}
            </div>
            <div class="uploadFileSizeLimit redColor">
                {vtranslate('LBL_MAX_UPLOAD_SIZE',$MODULE)}&nbsp;<span class="maxUploadSize" data-value="{$MAX_UPLOAD_LIMIT}">{$MAX_UPLOAD_LIMIT_MB}{vtranslate('MB',$MODULE)}</span>
            </div>
        </div>
    </div>