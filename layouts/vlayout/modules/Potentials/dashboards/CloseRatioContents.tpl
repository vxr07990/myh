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

<div>
	{if $MODELS|is_array}
	<div class='row-fluid'>
		<div class='span12' style='border-bottom:1px solid #C0C0C0;font-weight:bold;font-size:12pt'>
			<div class='row-fluid'>
				<div class='span6' style='padding-left:5px'>
					<b>{vtranslate('User', $MODULE_NAME)}</b>
				</div>
				<div class='span4'>
					<b>{vtranslate('Ratio', $MODULE_NAME)}</b>
				</div>
			</div>
		</div>
		<hr>
		{foreach key=NAME item=RATIO from=$MODELS}
		<div class='row-fluid' style='font-weight:bold'>
			<div class='span6'>
				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{$NAME}
			</div>
			<div class='span6'>
				{$RATIO}%
			</div>
		</div>
		{/foreach}
	</div>
	{else}
	<div class='row-fluid'>
		<div class='row-fluid' style='font-weight:bold;font-size:12pt;text-align:center'>
			<div class='span6'>
				<strong>Total</strong>
			</div>
			<div class='span6'>
				<strong>{$MODELS}%</strong>
			</div>
		</div>
	</div>
	{/if}

{if $PAGING->get('nextPageExists') eq 'true'}
	<div class='pull-right' style='margin-top:5px;padding-right:5px;'>
        <a href="javascript:;" name="history_more" data-url="{$WIDGET->getUrl()}&page={$PAGING->getNextPage()}">{vtranslate('LBL_MORE')}...</a>
        <br />
        <br />
        <br />
        <br />
	</div>
{else}
    <br />
    <br />
    <br />
    <br />
{/if}
</div>