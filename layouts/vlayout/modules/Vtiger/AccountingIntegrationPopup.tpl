{strip}
    <div id="popupPageContainer" class="contentsDiv">
        <script type="text/javascript" src="modules/Vtiger/resources/BaseList.js?v=1.0"></script>
        <div class="paddingLeftRight10px">{include file='AccountingIntegrationPopupSearch.tpl'|vtemplate_path:$MODULE_NAME}</div>
        <div id="popupContents" class="paddingLeftRight10px">{include file='AccountingIntegrationPopupContents.tpl'|vtemplate_path:$MODULE_NAME}</div>
        <input type="hidden" class="triggerEventName" value="{getPurifiedSmartyParameters('triggerEventName')}"/>
    </div>
    </div>
{/strip}