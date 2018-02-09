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
	{*<div class="row-fluid">
		<span class="span7">
			<strong>{vtranslate('LBL_QUOTES_SUBJECT','Estimates')}</strong>
		</span>
		<span class="span4">
			<span class="pull-right">
				<strong>{vtranslate('LBL_QUOTES_HDNGRANDTOTAL','Estimates')}</strong>
			</span>
		</span>
	</div>*}
	{foreach item=RELATED_RECORD from=$RELATED_RECORDS}
		<div class="recentActivitiesContainer">
			<ul class="unstyled">
				<li>
					<div class="row-fluid">
						<span class="span7 textOverflowEllipsis">
							<a href="{$RELATED_RECORD->getDetailViewUrl()}" id="{$MODULE}_{$RELATED_MODULE}_Related_Record_{$RELATED_RECORD->get('id')}" title="{$RELATED_RECORD->getDisplayValue('subject')}">
								{$RELATED_RECORD->getDisplayValue('subject')}
							</a>
						</span>
						<span class="span4">
							<span class="pull-right">
								{$RELATED_RECORD->getDisplayValue('hdnGrandTotal')}
							</span>
						</span>
					</div>
				</li>
			</ul>
		</div>
	{/foreach}
	{assign var=NUMBER_OF_RECORDS value=count($RELATED_RECORDS)}
	{if $NUMBER_OF_RECORDS eq 5}
		<div class="row-fluid">
			<div class="pull-right">
				<a class="moreRecentContacts cursorPointer">{vtranslate('LBL_MORE',$MODULE_NAME)}</a>
			</div>
		</div>
	{/if}
{/strip}
