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
<input type="hidden" id="disabledGoogleModules" value="{getenv('GOOGLE_ADDRESS_DISABLE')}">
{include file="EditViewBlocks.tpl"|@vtemplate_path:$MODULE}
<!--
<table class='table table-bordered blockContainer showInlineTable'>
<tbody>
	<tr>
		<th><center>Pricing Options<center></th>
		<th>Load To</th>
		<th>Deliver From</th>
		<th>Deliver To</th>
		<th>Price</th>
		<th>&nbsp;</th>
	</tr>
	<tr id='10_day_row'>
		<th><span class='pull-right'>10 Day Load</span></th>
		<td>{$AUTO_QUOTE_10_load}</td>
		<td>{$AUTO_QUOTE_10_from}</td>
		<td>{$AUTO_QUOTE_10_to}</td>
		<td>${$AUTO_QUOTE_10_price}</td>
		<td><input type='radio' name='auto_quote_select' value='1' {if $AUTO_QUOTE_SELECT eq 1}checked{/if}/></td>
	</tr>
	<tr id='7_day_row'>
		<th><span class='pull-right'>7 Day Load</span></th>
		<td>{$AUTO_QUOTE_7_load}</td>
		<td>{$AUTO_QUOTE_7_from}</td>
		<td>{$AUTO_QUOTE_7_to}</td>
		<td>${$AUTO_QUOTE_7_price}</td>
		<td><input type='radio' name='auto_quote_select' value='2' {if $AUTO_QUOTE_SELECT eq 2}checked{/if}/></td>
	</tr>
	<tr id='4_day_row'>
		<th><span class='pull-right'>4 Day Load</span></th>
		<td>{$AUTO_QUOTE_4_load}</td>
		<td>{$AUTO_QUOTE_4_from}</td>
		<td>{$AUTO_QUOTE_4_to}</td>
		<td>${$AUTO_QUOTE_4_price}</td>
		<td><input type='radio' name='auto_quote_select' value='3' {if $AUTO_QUOTE_SELECT eq 3}checked{/if}/></td>
	</tr>
	<tr id='2_day_row'>
		<th><span class='pull-right'>2 Day Load</span></th>
		<td>{$AUTO_QUOTE_2_load}</td>
		<td>{$AUTO_QUOTE_2_from}</td>
		<td>{$AUTO_QUOTE_2_to}</td>
		<td>${$AUTO_QUOTE_2_price}</td>
		<td><input type='radio' name='auto_quote_select' value='4' {if $AUTO_QUOTE_SELECT eq 4}checked{/if}/></td>
	</tr>
	<tr>
		<td colspan="6">
			<button class="btn btn-success ieBtn" id='update_rates'><strong>Update Rates</strong></button>
		</td>
	</tr>
</tbody>
</table>
<input type='hidden' value='{$AUTO_QUOTE_INFO}' name='auto_quote_info' id='auto_quote_info' />
<input type='hidden' value='{$AUTO_QUOTE_ID}' name='auto_quote_id' id='auto_quote_id' />
<br />
<table class='table table-bordered blockContainer showInlineTable hide'>
	<thead><tr><th class="blockHeader" colspan="4">Quote Details</th></tr></thead>
	<tbody>
		<tr>
			
		</tr>
	</tbody>
</table>
-->
{include file="EditViewActions.tpl"|@vtemplate_path:$MODULE}
