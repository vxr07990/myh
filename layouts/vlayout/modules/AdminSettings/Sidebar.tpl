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
<div class="sidebarTitleBlock">
	<h3 class="titlePadding themeTextColor unSelectedQuickLink cursorPointer"><a href="index.php?module=System&parent=Settings&view=Index">{vtranslate('LBL_SETTINGS', $QUALIFIED_MODULE)}</a></h3>
</div>
<!--div>
	<input class='input-medium' type='text' name='settingsSearch' placeholder={vtranslate("LBL_SEARCH_SETTINGS_PLACEHOLDER", $QUALIFIED_MODULE)} >
</div-->
<div class="quickWidgetContainer accordion" id="settingsQuickWidgetContainer">
	<div class="quickWidget">
		<div  style="border-bottom: 1px solid black;" class="widgetContainer accordion-body">
			<div class="selectedMenuItem selectedListItem" style="padding:7px;border-top:0px;">
				<div class="row-fluid menuItem"  data-actionurl="index">
					<a href="" data-id="index" class="span9 menuItemLabel" data-menu-item="true" >{vtranslate('LBL_SETTINGS_GLOBAL', $QUALIFIED_MODULE)}</a>
					<span class="span1">&nbsp;</span>
					<div class="clearfix"></div>
				</div>
			</div>
			<div style="padding:7px;border-top:0px;">
				<div class="row-fluid menuItem"  data-actionurl="index">
					<a href="" data-id="index" class="span9 menuItemLabel" data-menu-item="true" >{vtranslate('LBL_AGENT_USER', $QUALIFIED_MODULE)}</a>
					<span class="span1">&nbsp;</span>
					<div class="clearfix"></div>
				</div>
			</div>
		</div>
	</div>
</div>
<!---
<div class="row-fluid">
    <button type="button" class="row-fluid extensionStorebtn" onclick='window.location.href="index.php?module=ExtensionStore&parent=Settings&view=ExtensionStore"'>{vtranslate('LBL_EXTENSION_STORE', $QUALIFIED_MODULE)}</button>
</div>
-->
{/strip}
