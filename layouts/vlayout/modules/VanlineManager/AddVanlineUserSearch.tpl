{strip}
    <input type="hidden" id="parentModule" value="{$SOURCE_MODULE}"/>
    <input type="hidden" id="module" value="{$MODULE}"/>
    <input type="hidden" id="parent" value="{$PARENT_MODULE}"/>
    <input type="hidden" id="sourceRecord" value="{$SOURCE_RECORD}"/>
    <input type="hidden" id="sourceField" value="{$SOURCE_FIELD}"/>
    <input type="hidden" id="url" value="{$GETURL}" />
    <input type="hidden" id="multi_select" value="{$MULTI_SELECT}" />
    <input type="hidden" id="currencyId" value="{$CURRENCY_ID}" />
    <input type="hidden" id="relatedParentModule" value="{$RELATED_PARENT_MODULE}"/>
    <input type="hidden" id="relatedParentId" value="{$RELATED_PARENT_ID}"/>
    <input type="hidden" id="view" value="{$VIEW}"/>
    <div class="popupContainer row-fluid">
        <div class="logo span6"><img src="{$COMPANY_LOGO->get('imagepath')}" title="{$COMPANY_LOGO->get('title')}" alt="{$COMPANY_LOGO->get('alt')}" width="160px;"/></div>
        <div class="span6"><b>{vtranslate($MODULE_NAME, $MODULE_NAME)}</b></div>
    </div>
{/strip}