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
		<input id='activityReminder' class='hide noprint' type="hidden" value="{$ACTIVITY_REMINDER}"/>

		{* Feedback side-panel button *}
		{if $HEADER_LINKS && $MAIN_PRODUCT_SUPPORT && !$MAIN_PRODUCT_WHITELABEL}
		{assign var="FIRSTHEADERLINK" value=$HEADER_LINKS.0}
		{assign var="FIRSTHEADERLINKCHILDRENS" value=$FIRSTHEADERLINK->get('childlinks')}
		{assign var="FEEDBACKLINKMODEL" value=$FIRSTHEADERLINKCHILDRENS.2}
		<div id="userfeedback" class="feedback noprint">
			
		</div>
		{/if}

		{if !$MAIN_PRODUCT_WHITELABEL && isset($CURRENT_USER_MODEL)}
		<footer class="noprint">
			<div class="vtFooter">
				<p>
					{assign var=HQ value=$CURRENT_USER_MODEL->getMoveHQVersion()}
					{if $HQ == 1}{vtranslate('POWEREDBY_MOVEHQ')}{else}{vtranslate('POWEREDBY_MOVECRM')}{/if}&nbsp;{$VTIGER_VERSION}&nbsp;{getenv('FOOTER')}
					&nbsp;&nbsp;&copy; {date('Y')}&nbsp;
					{if $HQ == 1}{vtranslate('WIRG_LINKTHROUGH')}{else}{vtranslate('IGC_LINKTHROUGH')}{/if}
					{if getenv('DISPLAY_ERRORS') eq 1}&nbsp;|&nbsp;Errors: ON{/if}
					{if getenv('VTIGER_DEBUGGING') eq 1}&nbsp;|&nbsp;Debug Logging: ON{/if}
				</p>
			</div>
		</footer>
		{/if}

		{* javascript files *}
		{include file='JSResources.tpl'|@vtemplate_path}
		</div>

	</body>
</html>
{/strip}

