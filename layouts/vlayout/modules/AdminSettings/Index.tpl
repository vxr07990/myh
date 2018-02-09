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
	<div class="container-fluid settingsIndexPage">
		<br><br>
		<h3>{vtranslate('LBL_SETTINGS_GLOBAL',$MODULE)}</h3>
		<hr>
		{foreach from=$AGENT_INFO item=AGENT}
		<form method='post' action='index.php'>
			<input type='hidden' name='agentmanagerid' value='{$AGENT["agentmanagerid"]}' />
			<table class='table table-bordered blockContainer showInlineTable'>
				<thead>
					<tr>					
						<th class="blockHeader" colspan="4" style="font-family: Sacramento;">
							<img class="cursorPointer alignMiddle blockToggle " src="layouts/vlayout/skins/bluelagoon/images/arrowRight.png" data-mode="hide" data-id="92">
							<img class="cursorPointer alignMiddle blockToggle hide " src="layouts/vlayout/skins/bluelagoon/images/arrowDown.png" data-mode="show" data-id="77">
							&nbsp;&nbsp;{$AGENT['agency_name']}
						</th>
					</tr>
				</thead>
				<tbody class='hide'>
					<tr>
						<td class='fieldLabel medium'>{vtranslate('LBL_VAL_DISCOUNT',$MODULE)}</td>
						<td class='fieldValue medium'><input name='valuation_discount' type='text' value='{$AGENT["valuation_discount"]}'/></td>
					</tr>
					<tr>
						<td class='fieldLabel medium'>{vtranslate('LBL_STORAGE_DISCOUNT',$MODULE)}</td>
						<td class='fieldValue medium'><input name='storage_discount' type='text' value='{$AGENT["storage_discount"]}' /></td>
					</tr>
					<tr>
						<td class='fieldLabel medium'>{vtranslate('LBL_MAX_SHARE_VARIANCE',$MODULE)}</td>
						<td class='fieldValue medium'><input name='max_share_variance' type='text' value='{$AGENT['max_share_variance']}' /></td>
					</tr>
					<tr>
						<td class='fieldLabel medium'>{vtranslate('LBL_APPLY_PACKING',$MODULE)}</td>
						<td class='fieldValue medium'>
							<input name='packing_fee' type='hidden' value='0' />
							<input name='packing_fee' type='checkbox' value='1' {if $AGENT['packing_fee'] == 1}checked{/if} />
						</td>
					</tr>
					<tr>
						<td class='fieldLabel medium'>{vtranslate('LBL_SEND_DISPATCH',$MODULE)}</td>
						<td class='fieldValue medium'>
							<input name='disable_dispatch' type='hidden' value='0' />
							<input name='disable_dispatch' type='checkbox' value='1' {if $AGENT['disable_dispatch'] == 1}checked{/if} />
						</td>
					</tr>
					<tr>
						<td class='fieldLabel medium'>{vtranslate('LBL_PACKING_DISCOUNT_CRATE',$MODULE)}</td>
						<td class='fieldValue medium'>
							<input name='apply_packing_discount' type='hidden' value='0' />
							<input name='apply_packing_discount' type='checkbox' value='1' {if $AGENT['apply_packing_discount'] == 1}checked{/if} />
						</td>
					</tr>
					<tr>
						<td class='fieldLabel medium'>{vtranslate('LBL_ALLOW_IRR',$MODULE)}</td>
						<td class='fieldValue medium'>
							<input name='allow_irr_discount' type='hidden' value='0' />
							<input name='allow_irr_discount' type='checkbox' value='1' {if $AGENT['allow_irr_discount'] == 1}checked{/if} />
						</td>
					</tr>
					<tr>
						<td class='fieldLabel medium'>{vtranslate('LBL_FERRY_DISCOUNT',$MODULE)}</td>
						<td class='fieldValue medium'>
							<input name='allow_ferry_discount' type='hidden' value='0' />
							<input name='allow_ferry_discount' type='checkbox' value='1' {if $AGENT['allow_ferry_discount'] == 1}checked{/if} />
						</td>
					</tr>
					<tr>
						<td class='fieldLabel medium'>{vtranslate('LBL_LABOR_SURCHARGE_DISCOUNT',$MODULE)}</td>
						<td class='fieldValue medium'>
							<input name='allow_labor_surcharge_discount' type='hidden' value='0' />
							<input name='allow_labor_surcharge_discount' type='checkbox' value='1' {if $AGENT['allow_labor_surcharge_discount'] == 1}checked{/if} />
						</td>
					</tr>
					<tr>
					<td>
					</td>
					<td>
						<button class="btn btn-success" type="submit"><strong>Save</strong></button>
					</td>
					</tr>
				</tbody>
			</table>
		</form>
		<br />
		{/foreach}
		
{/strip}
