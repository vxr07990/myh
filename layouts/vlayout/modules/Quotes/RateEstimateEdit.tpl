{strip}
{assign var=IS_HIDDEN value='1'}
<div class='hide'>
	<div id="inline_content" class='details'>
		<div class='contents'>
			<link rel="stylesheet" href="libraries/jquery/colorbox/example1/colorbox.css" />
			{foreach key=BLOCK_LABEL item=BLOCK_FIELDS from=$RECORD_STRUCTURE name="EditViewBlockLevelLoop"}
				{if $BLOCK_LABEL neq "LBL_QUOTES_VALUATIONDETAILS" and $BLOCK_LABEL neq "LBL_QUOTES_SITDETAILS" and $BLOCK_LABEL neq "LBL_QUOTES_ACCESSORIALDETAILS"}{continue}{/if}
				{if $BLOCK_FIELDS|@count lte 0}{continue}{/if}
				<table class="table table-bordered blockContainer showInlineTable{if $BLOCK_LABEL eq "LBL_QUOTES_SITDETAILS" or $BLOCK_LABEL eq "LBL_QUOTES_ACCESSORIALDETAILS"} sit{/if}" {if in_array($BLOCK_LABEL, $HIDDEN_BLOCKS)} style="display:none;"{/if}>
				<thead>
				<tr>
					<th class="blockHeader" colspan="4">
						<img class="cursorPointer alignMiddle blockToggle {if !($IS_HIDDEN)} hide {/if} "  src="{vimage_path('arrowRight.png')}" data-mode="hide">
						<img class="cursorPointer alignMiddle blockToggle {if ($IS_HIDDEN)} hide {/if}"  src="{vimage_path('arrowDown.png')}" data-mode="show">
						&nbsp;&nbsp;{vtranslate($BLOCK_LABEL, $MODULE)}
					</th>
				</tr>
				</thead>
				<tbody{if $IS_HIDDEN} class="hide" {/if}>
				{if $BLOCK_LABEL eq "LBL_QUOTES_SITDETAILS"}
					<tr class='cbxblockhead'><td class='fieldLabel' colspan=2>Origin</td><td class='fieldLabel' colspan=2>Destination</td></tr>
				{/if}
				{if ($BLOCK_LABEL eq 'LBL_ADDRESS_INFORMATION') and ($MODULE neq 'PurchaseOrder') }
					<!--<tr>
					<td class="fieldLabel {$WIDTHTYPE}" name="copyHeader1">
						<label class="muted pull-right marginRight10px" name="togglingHeader">{vtranslate('LBL_BILLING_ADDRESS_FROM', $MODULE)}</label>
					</td>
					<td class="fieldValue {$WIDTHTYPE}" name="copyAddress1">
						<div class="row-fluid">
							<div class="span5">
								<span class="row-fluid margin0px">
									<label class="radio">
									  <input type="radio" name="copyAddressFromRight" class="accountAddress" data-copy-address="billing" checked="">{vtranslate('SINGLE_Accounts', $MODULE)}
									</label>
								</span>
								<span class="row-fluid margin0px">
									<label class="radio">
									  <input type="radio" name="copyAddressFromRight" class="contactAddress" data-copy-address="billing" checked="">{vtranslate('SINGLE_Contacts', $MODULE)}
									</label>
								</span>
								<span class="row-fluid margin0px" name="togglingAddressContainerRight">
									<label class="radio">
								  <input type="radio" name="copyAddressFromRight" class="shippingAddress" data-target="shipping" checked="">{vtranslate('Shipping Address', $MODULE)}
									</label>
								</span>
								<span class="row-fluid margin0px hide" name="togglingAddressContainerLeft">
									<label class="radio">
								  <input type="radio" name="copyAddressFromRight"  class="billingAddress" data-target="billing" checked="">{vtranslate('Billing Address', $MODULE)}
									</label>
								</span>
							</div>
						</div>
					</td>
					<td class="fieldLabel {$WIDTHTYPE}" name="copyHeader2">
						<label class="muted pull-right marginRight10px" name="togglingHeader">{vtranslate('LBL_SHIPPING_ADDRESS_FROM', $MODULE)}</label>
					</td>
					<td class="fieldValue {$WIDTHTYPE}" name="copyAddress2">
						<div class="row-fluid">
							<div class="span5">
								<span class="row-fluid margin0px">
									<label class="radio">
									  <input type="radio" name="copyAddressFromLeft" class="accountAddress" data-copy-address="shipping" checked="">{vtranslate('SINGLE_Accounts', $MODULE)}
									</label>
								</span>
								<span class="row-fluid margin0px">
									<label class="radio">
									  <input type="radio" name="copyAddressFromLeft" class="contactAddress" data-copy-address="shipping" checked="">{vtranslate('SINGLE_Contacts', $MODULE)}
									</label>
								</span>
								<span class="row-fluid margin0px" name="togglingAddressContainerLeft">
									<label class="radio">
								  <input type="radio" name="copyAddressFromLeft" class="billingAddress" data-target="billing" checked="">{vtranslate('Billing Address', $MODULE)}
									</label>
								</span>
								<span class="row-fluid margin0px hide" name="togglingAddressContainerRight">
									<label class="radio">
								  <input type="radio" name="copyAddressFromLeft" class="shippingAddress" data-target="shipping" checked="">{vtranslate('Shipping Address', $MODULE)}
									</label>
								</span>
							</div>
						</div>
					</td>
				</tr>-->
				{/if}
				<tr>
				{assign var=COUNTER value=0}
				{foreach key=FIELD_NAME item=FIELD_MODEL from=$BLOCK_FIELDS name=blockfields}
					{assign var="isReferenceField" value=$FIELD_MODEL->getFieldDataType()}
					{if $FIELD_MODEL->get('uitype') eq "20" or $FIELD_MODEL->get('uitype') eq "19"}
						{if $COUNTER eq '1'}
							<td class="{$WIDTHTYPE}"></td><td class="{$WIDTH_TYPE_CLASSSES[$WIDTHTYPE]}"></td></tr><tr>
							{assign var=COUNTER value=0}
						{/if}
					{/if}
					
					{if $COUNTER eq 2}
						</tr><tr>
						{assign var=COUNTER value=1}
					{else}
						{assign var=COUNTER value=$COUNTER+1}
					{/if}
					{if $FIELD_MODEL->getName() eq 'acc_shuttle_origin_weight'}
						 <td style='text-align:center; min-width:400px; background-color:#E8E8E8;' colspan=4>Shuttle Service</td></tr>
						 <tr class='cbxblockhead'><td class='fieldLabel' colspan=2>Origin</td><td class='fieldLabel' colspan=2>Destination</td></tr>
						 <tr>
					{else if $FIELD_MODEL->getName() eq 'acc_ot_origin_weight'}
						 <tr><td style='text-align:center; min-width:400px; background-color:#E8E8E8;' colspan=4>OT Service</td></tr>
						 <tr class='cbxblockhead'><td class='fieldLabel' colspan=2>Origin</td><td class='fieldLabel' colspan=2>Destination</td></tr>
						 <tr>
					{else if $FIELD_MODEL->getName() eq 'acc_selfstg_origin_weight'}
						 <tr><td style='text-align:center; min-width:400px; background-color:#E8E8E8;' colspan=4>Self/Mini Stg</td></tr>
						 <tr class='cbxblockhead'><td class='fieldLabel' colspan=2>Origin</td><td class='fieldLabel' colspan=2>Destination</td></tr>
						 <tr>
					{else if $FIELD_MODEL->getName() eq 'acc_exlabor_origin_hours'}
						 <tr><td style='text-align:center; min-width:400px; background-color:#E8E8E8;' colspan=4>Extra Labor</td></tr>
						 <tr class='cbxblockhead'><td class='fieldLabel' colspan=2>Origin</td><td class='fieldLabel' colspan=2>Destination</td></tr>
						 <tr>
					{else if $FIELD_MODEL->getName() eq 'acc_wait_origin_hours'}
						 <tr><td style='text-align:center; min-width:400px; background-color:#E8E8E8;' colspan=4>Wait Time</td></tr>
						 <tr class='cbxblockhead'><td class='fieldLabel' colspan=2>Origin</td><td class='fieldLabel' colspan=2>Destination</td></tr>
						 <tr>
					{/if}
					{if $FIELD_MODEL->getName() eq 'acc_shuttle_origin_ot'}
						{assign var=SPEC_CLASS value=' shuttleOriginOT'}
						{assign var=SPEC_CLASS_HIDE value=' hide shuttleOriginOTHide'}
					{else if $FIELD_MODEL->getName() eq 'acc_shuttle_dest_ot'}
						{assign var=SPEC_CLASS value=' shuttleDestOT'}
						{assign var=SPEC_CLASS_HIDE value=' hide shuttleDestOTHide'}
					{else if $FIELD_MODEL->getName() eq 'acc_shuttle_origin_over25'}
						{assign var=SPEC_CLASS value=' shuttleOriginOver25'}
						{assign var=SPEC_CLASS_HIDE value=' hide shuttleOriginOver25Hide'}
					{else if $FIELD_MODEL->getName() eq 'acc_shuttle_dest_over25'}
						{assign var=SPEC_CLASS value=' shuttleDestOver25'}
						{assign var=SPEC_CLASS_HIDE value=' hide shuttleDestOver25Hide'}
					{else if $FIELD_MODEL->getName() eq 'acc_shuttle_origin_miles'}
						{assign var=SPEC_CLASS value=' shuttleOriginMiles'}
						{assign var=SPEC_CLASS_HIDE value=' hide shuttleOriginMilesHide'}
					{else if $FIELD_MODEL->getName() eq 'acc_shuttle_dest_miles'}
						{assign var=SPEC_CLASS value=' shuttleDestMiles'}
						{assign var=SPEC_CLASS_HIDE value=' hide shuttleDestMilesHide'}
					{else if $FIELD_MODEL->getName() eq 'acc_selfstg_origin_ot'}
						{assign var=SPEC_CLASS value=' selfstgOriginOT'}
						{assign var=SPEC_CLASS_HIDE value=' hide selfstgOriginOTHide'}
					{else if $FIELD_MODEL->getName() eq 'acc_selfstg_dest_ot'}
						{assign var=SPEC_CLASS value=' selfstgDestOT'}
						{assign var=SPEC_CLASS_HIDE value=' hide selfstgDestOTHide'}
					{else}
						{assign var=SPEC_CLASS value=''}
						{assign var=SPEC_CLASS_HIDE value=' hide'}
					{/if}
					<td class="fieldLabel {$WIDTHTYPE} {$SPEC_CLASS}">
						{if $isReferenceField neq "reference"}<label class="muted pull-right marginRight10px">{/if}
							{if $FIELD_MODEL->isMandatory() eq true && $isReferenceField neq "reference"} <span class="redColor">*</span> {/if}
							{if $isReferenceField eq "reference"}
								{assign var="REFERENCE_LIST" value=$FIELD_MODEL->getReferenceList()}
								{assign var="REFERENCE_LIST_COUNT" value=count($REFERENCE_LIST)}
								{if $REFERENCE_LIST_COUNT > 1}
									{assign var="DISPLAYID" value=$FIELD_MODEL->get('fieldvalue')}
									{assign var="REFERENCED_MODULE_STRUCT" value=$FIELD_MODEL->getUITypeModel()->getReferenceModule($DISPLAYID)}
									{if !empty($REFERENCED_MODULE_STRUCT)}
										{assign var="REFERENCED_MODULE_NAME" value=$REFERENCED_MODULE_STRUCT->get('name')}
									{/if}
									<span class="pull-right">
										{if $FIELD_MODEL->isMandatory() eq true} <span class="redColor">*</span> {/if}
										<select class="chzn-select referenceModulesList streched" style="width:140px;">
											<optgroup>
												{foreach key=index item=value from=$REFERENCE_LIST}
													<option value="{$value}" {if $value eq $REFERENCED_MODULE_NAME} selected {/if}>{vtranslate($value, $MODULE)}</option>
												{/foreach}
											</optgroup>
										</select>
									</span>
								{else}
									<label class="muted pull-right marginRight10px">{if $FIELD_MODEL->isMandatory() eq true} <span class="redColor">*</span> {/if}{vtranslate($FIELD_MODEL->get('label'), $MODULE)}</label>
								{/if}
							{else if $FIELD_MODEL->get('uitype') eq "83"}
								{include file=vtemplate_path($FIELD_MODEL->getUITypeModel()->getTemplateName(),$MODULE) COUNTER=$COUNTER}
							{else}
								{vtranslate($FIELD_MODEL->get('label'), $MODULE)}
							{/if}
						{if $isReferenceField neq "reference"}</label>{/if}
					</td>
					<td class="fieldLabel {$WIDTHTYPE}{$SPEC_CLASS_HIDE}" style="width:20%"></td>
					{if $FIELD_MODEL->get('uitype') neq "83"}
						<td class="fieldValue {$WIDTHTYPE} {$SPEC_CLASS}" {if $FIELD_MODEL->get('uitype') eq '19'} colspan="3" {assign var=COUNTER value=$COUNTER+1} {/if} {if $FIELD_MODEL->get('uitype') eq '20'} colspan="3"{/if}>
							{include file=vtemplate_path($FIELD_MODEL->getUITypeModel()->getTemplateName(),$MODULE) BLOCK_FIELDS=$BLOCK_FIELDS}
						</td>
						 <td class="fieldValue {$WIDTHTYPE}{$SPEC_CLASS_HIDE}" style="width:20%"></td>
					{/if}
					{if $BLOCK_FIELDS|@count eq 1 and $FIELD_MODEL->get('uitype') neq "19" and $FIELD_MODEL->get('uitype') neq "20" and $FIELD_MODEL->get('uitype') neq "30" and $FIELD_MODEL->get('name') neq "recurringtype"}
						<td class="{$WIDTHTYPE}"></td><td class="{$WIDTHTYPE}"></td>
					{/if}
				{/foreach}
				{* adding additional column for odd number of fields in a block *}
				{if $BLOCK_FIELDS|@end eq true and $BLOCK_FIELDS|@count neq 1 and $COUNTER eq 1}
					<td class="fieldLabel {$WIDTHTYPE}"></td><td class="{$WIDTHTYPE}"></td>
				{/if}
				</tr>
				
					{if $BLOCK_LABEL == 'LBL_QUOTES_INTERSTATEMOVEDETAILS'}
						<script type='text/javascript' src='libraries/jquery/colorbox/jquery.colorbox-min.js'></script>
						<tr><td class='fieldLabel'></td><td class='fieldValue'><button type='button' id='interstateRateQuick'>Quick Rate Estimate</button></td><td class='fieldLabel'></td><td class='fieldValue'><button type='button' id='interstateRateDetail'>Detailed Rate Estimate</button></td></tr>
					{/if}
				</tbody>
				</table>
				<br>
			{/foreach}
			{include file='layouts/vlayout/modules/Quotes/MiscChargesEdit.tpl'}
		</div>
	</div>
	<div id='reportContent' class='details'>
	</div>
</div>
{/strip}