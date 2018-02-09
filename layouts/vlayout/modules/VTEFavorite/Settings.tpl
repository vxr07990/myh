    {*/* ********************************************************************************
* The content of this file is subject to the VTEFavorite ("License");
* You may not use this file except in compliance with the License
* The Initial Developer of the Original Code is VTExperts.com
* Portions created by VTExperts.com. are Copyright(C) VTExperts.com.
* All Rights Reserved.
* ****************************************************************************** */*}
{strip}
<div class="widget_header row-fluid">
<div class="span8"><h3>{vtranslate('LBL_SETTING_TITLE','VTEFavorite')}</h3></div>
</div>
<hr>
<div class="contents tabbable ui-sortable">
	<ul class="nav nav-tabs layoutTabs massEditTabs">
		<li {if $TYPE eq 'favorite' } class="active" {else} class="relatedListTab" {/if}><a  href="?module=VTEFavorite&parent=Settings&view=Settings&type=favorite"><strong>{vtranslate('LBL_SETTING_FAVORITE','VTEFavorite')}</strong></a></li>
		<li {if $TYPE eq 'recently' } class="active" {else} class="relatedListTab" {/if}><a href="?module=VTEFavorite&parent=Settings&view=Settings&type=recently"><strong>{vtranslate('LBL_SETTING_RECENT','VTEFavorite')}</strong></a></li>
		<li {if $TYPE eq 'customlist' } class="active" {else} class="relatedListTab" {/if}><a href="?module=VTEFavorite&parent=Settings&view=Settings&type=customlist"><strong>{vtranslate('LBL_SETTING_CUSTOMLIST','VTEFavorite')}</strong></a></li>
	</ul>
	<div class="tab-content layoutContent padding20 themeTableColor overflowVisible">
		<div class="tab-pane {if $TYPE eq 'favorite' }active{/if}" id="FavoriteRecords">
		{if $TYPE eq 'favorite' }
			{include file=vtemplate_path("SettingFavoriteRecords.tpl", $MODULE_NAME)}
		{/if}
		
		</div>
		<div class="tab-pane {if $TYPE eq 'recently' }active{/if}" id="RecentRecords">
		{if $TYPE eq 'recently' }
			{include file=vtemplate_path("SettingRecentlyRecords.tpl", $MODULE_NAME)}
			
		{/if}
		
		</div>
		<div class="tab-pane {if $TYPE eq 'customlist' }active{/if}" id="Customlists">
		{if $TYPE eq 'customlist' }
			{include file=vtemplate_path("SettingCustomListRecords.tpl", $MODULE_NAME)}
			
		{/if}
		</div>
	</div>
{/strip}
