{strip}
{assign var=IS_HIDDEN value='0'}
<link rel="stylesheet" href="libraries/jquery/colorbox/example1/colorbox.css" />
		<div>
			{foreach key=BLOCK_LABEL item=BLOCK_FIELDS from=$RECORD_STRUCTURE name="EditViewBlockLevelLoop"}
				{if $BLOCK_LABEL neq "LBL_QUOTES_VALUATIONDETAILS" and $BLOCK_LABEL neq "LBL_QUOTES_VALUATIONDETAILS" and $BLOCK_LABEL neq "LBL_QUOTES_SITDETAILS" and $BLOCK_LABEL neq "LBL_QUOTES_ACCESSORIALDETAILS"}{continue}{/if}
				{if $BLOCK_FIELDS|@count lte 0}{continue}{/if}
				<table name='{$BLOCK_LABEL}' class="table table-bordered blockContainer showInlineTable{if $BLOCK_LABEL eq "LBL_QUOTES_SITDETAILS" or $BLOCK_LABEL eq "LBL_QUOTES_ACCESSORIALDETAILS"} sit{/if}{if in_array($BLOCK_LABEL, $HIDDEN_BLOCKS)} hide{/if}">
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
					<tr>
						<td class="fieldLabel">
							<label class="muted pull-right marginRight10px">Use Custom Rates - Origin</label>
						</td>
						<td class="fieldValue" style="text-align:left">
							<input type="hidden" name="apply_custom_sit_rate_override" value="{($CUSTOM_RATES.apply_custom_sit_rate_override)?$CUSTOM_RATES.apply_custom_sit_rate_override:0}">
							<input type="checkbox" class="sit_override "  data-location="origin"  name="apply_custom_sit_rate_override" {if $CUSTOM_RATES.apply_custom_sit_rate_override == 1}checked{/if}>
                            <a href="javascript:void(0)" class="btn pull-right marginBottom5px btnLoadTariff {if !$CUSTOM_RATES.apply_custom_sit_rate_override}hide{/if}" data-location="origin" >Load Tariff</a>
						</td>
						<td class="fieldLabel">
                            <label class="muted pull-right marginRight10px">Use Custom Rates - Destination</label>
                        </td>
						<td class="fieldValue" style="text-align:left">
                            <input type="hidden" name="apply_custom_sit_rate_override_dest" value="{($CUSTOM_RATES.apply_custom_sit_rate_override_dest)?$CUSTOM_RATES.apply_custom_sit_rate_override_dest:0}">
							<input type="checkbox" class="sit_override" data-location="dest" name="apply_custom_sit_rate_override_dest" {if $CUSTOM_RATES.apply_custom_sit_rate_override_dest == 1}checked{/if}>
                            <a href="javascript:void(0)" class="btn pull-right marginBottom5px btnLoadTariff  {if !$CUSTOM_RATES.apply_custom_sit_rate_override_dest}hide{/if}" data-location="dest" >Load Tariff</a>
                        </td>
					</tr>
					<tr class='cbxblockhead'>
						<td class='fieldLabel' colspan=2>Origin</td>
						<td class='fieldLabel' colspan=2>Destination</td>
					</tr>
				{/if}
				<tr>
				{assign var=COUNTER value=0}
				{assign var=SHOW_SHUTTLE_SELF_MINI value=false}
				{foreach key=FIELD_NAME item=FIELD_MODEL from=$BLOCK_FIELDS name=blockfields}

                {if $FIELD_MODEL->getName() eq 'acc_ot_origin_weight' || $FIELD_MODEL->getName() eq 'acc_ot_dest_weight' || $FIELD_MODEL->getName() eq 'acc_ot_origin_applied' || $FIELD_MODEL->getName() eq 'acc_ot_dest_applied'}{continue}{/if}

					{assign var="isReferenceField" value=$FIELD_MODEL->getFieldDataType()}
					{if $FIELD_MODEL->get('uitype') eq "20" or $FIELD_MODEL->get('uitype') eq "19"}
						{if $COUNTER eq '1'}
							<td class="{$WIDTHTYPE}"></td><td class="{$WIDTH_TYPE_CLASSSES[$WIDTHTYPE]}"></td></tr><tr>
							{assign var=COUNTER value=0}
						{/if}
					{/if}

					{if $FIELD_MODEL->getName() eq 'acc_shuttle_origin_weight'}
						 {assign var=ROW_HAS_CLASS value=true}
						 {assign var=ROW_CLASS value='shuttle'}
						 {assign var=ROW_COUNT value=0}
					{elseif $FIELD_MODEL->getName() eq 'acc_ot_origin_weight'}
						 {assign var=ROW_HAS_CLASS value=true}
						 {assign var=ROW_CLASS value='otServiceRow'}
						 {assign var=ROW_COUNT value=0}
					{elseif $FIELD_MODEL->getName() eq 'acc_selfstg_origin_weight'}
						 {assign var=ROW_HAS_CLASS value=true}
						 {assign var=ROW_CLASS value='selfStgRow'}
						 {assign var=ROW_COUNT value=0}
					{elseif $FIELD_MODEL->getName() eq 'acc_exlabor_origin_hours'}
						 {assign var=ROW_HAS_CLASS value=true}
						 {assign var=ROW_CLASS value='exLaborRow'}
						 {assign var=ROW_COUNT value=0}
					{elseif $FIELD_MODEL->getName() eq 'acc_wait_origin_hours'}
						 {assign var=ROW_HAS_CLASS value=true}
						 {assign var=ROW_CLASS value='waitRow'}
						 {assign var=ROW_COUNT value=0}
					{elseif $FIELD_MODEL->getName() eq 'bulky_article_changes'}
						 {assign var=ROW_HAS_CLASS value=true}
						 {assign var=ROW_CLASS value='bulkyArticleRow'}
						 {assign var=ROW_COUNT value=0}
					{elseif $FIELD_MODEL->getName() eq 'rush_shipment_fee'}
						 {assign var=ROW_HAS_CLASS value=true}
						 {assign var=ROW_CLASS value='pricingRow'}
						 {assign var=ROW_COUNT value=0}
					{/if}

					{if $COUNTER eq 2}
						</tr><tr class="{$ROW_CLASS}Row">
						{assign var=COUNTER value=1}
					{else}
						{assign var=COUNTER value=$COUNTER+1}
					{/if}
					{if $FIELD_MODEL->getName() eq 'acc_shuttle_origin_weight'}
						 <td style='background-color:#E8E8E8; padding-left:30px;' colspan=4>- Shuttle Service</td></tr>
					<tr class='cbxblockhead {$ROW_CLASS}Row'><td class='fieldLabel' colspan=2>Origin</td><td class='fieldLabel' colspan=2>{if getenv('INSTANCE_NAME') neq 'sirva'}&nbsp;Destination{/if}</td></tr>
						 <tr class="{$ROW_CLASS}Row">
					{elseif $FIELD_MODEL->getName() eq 'acc_ot_origin_weight'}
						 <tr><td style='background-color:#E8E8E8; padding-left:30px;' colspan=4>- OT Service</td></tr>
						 <tr class='cbxblockhead'><td class='fieldLabel' colspan=2>Origin</td><td class='fieldLabel' colspan=2>Destination</td></tr>
						 <tr>
					{elseif $FIELD_MODEL->getName() eq 'acc_selfstg_origin_weight'}
						 <tr><td style='background-color:#E8E8E8; padding-left:30px;' colspan=4>- Self/Mini Stg</td></tr>
						 <tr class='cbxblockhead'><td class='fieldLabel' colspan=2>Origin</td><td class='fieldLabel' colspan=2>Destination</td></tr>
						 {assign var=SHOW_SHUTTLE_SELF_MINI value=true}
						 <tr>
					{elseif $FIELD_MODEL->getName() eq 'acc_exlabor_origin_hours'}
						 <tr><td style='background-color:#E8E8E8; padding-left:30px;' colspan=4>- Extra Labor</td></tr>

						 <tr class='cbxblockhead'><td class='fieldLabel' colspan=2>Origin</td><td class='fieldLabel' colspan=2>Destination</td></tr>
						 <tr>
					{elseif $FIELD_MODEL->getName() eq 'acc_wait_origin_hours'}
						 <tr><td style='background-color:#E8E8E8; padding-left:30px;' colspan=4>- Wait Time</td></tr>
						 <tr class='cbxblockhead'><td class='fieldLabel' colspan=2>Origin</td><td class='fieldLabel' colspan=2>Destination</td></tr>
						 <tr>
					{elseif $FIELD_MODEL->getName() eq 'acc_day_certain_pickup'}
						 <tr id='Estimates_editView_fieldName_day_certain'><td style='background-color:#E8E8E8; padding-left:30px;' colspan=4>- Day Certain Pickup</td></tr>
						 <tr id='Estimates_editView_fieldName_day_certain_row'>
					{elseif $FIELD_MODEL->getName() eq 'bulky_article_changes'}
						{if $COUNTER eq 2}
                            <td class='fieldLabel'></td><td></td></tr>
                            {assign var=COUNTER value=1}
                        {/if}
							<td style='background-color:#E8E8E8; padding-left:30px;' colspan=4>- Special Services</td></tr>
						{assign var=ROW_COUNT value=$ROW_COUNT+1}
						<tr id='{$ROW_CLASS}_{$ROW_COUNT}'>
                    {elseif $FIELD_MODEL->getName() eq 'rush_shipment_fee'}
                      	{if $COUNTER eq 2}
                            <td class='fieldLabel'></td><td></td></tr>
                        <tr id='{$ROW_CLASS}_{$ROW_COUNT}'>
                            {assign var=COUNTER value=1}
                        {/if}
                        <td style='background-color:#E8E8E8; padding-left:30px;' colspan=4>- Pricing</td></tr>
                        {assign var=ROW_COUNT value=$ROW_COUNT+1}
                        <tr id='{$ROW_CLASS}_{$ROW_COUNT}'>
					{/if}
					{assign var=SHOW_SHUTTLE value=true}
					{if $FIELD_MODEL->getName() eq 'acc_shuttle_origin_ot'}
						{assign var=SPEC_CLASS value=' shuttleOriginOT'}
						{assign var=SPEC_CLASS_HIDE value=' hide shuttleOriginOTHide'}
					{elseif $FIELD_MODEL->getName() eq 'acc_shuttle_dest_ot'}
						{assign var=SPEC_CLASS value=' shuttleDestOT shuttleDest'}
						{assign var=SPEC_CLASS_HIDE value=' hide shuttleDestOTHide'}
						{assign var=SHOW_SHUTTLE value=false}
					{elseif $FIELD_MODEL->getName() eq 'acc_shuttle_origin_over25'}
						{assign var=SPEC_CLASS value=' shuttleOriginOver25'}
						{assign var=SPEC_CLASS_HIDE value=' hide shuttleOriginOver25Hide'}
					{elseif $FIELD_MODEL->getName() eq 'acc_shuttle_dest_over25'}
						{assign var=SPEC_CLASS value=' shuttleDestOver25 shuttleDest'}
						{assign var=SPEC_CLASS_HIDE value=' hide shuttleDestOver25Hide'}
						{assign var=SHOW_SHUTTLE value=false}
					{elseif $FIELD_MODEL->getName() eq 'acc_shuttle_origin_miles'}
						{assign var=SPEC_CLASS value=' shuttleOriginMiles'}
						{assign var=SPEC_CLASS_HIDE value=' hide shuttleOriginMilesHide'}
					{elseif $FIELD_MODEL->getName() eq 'acc_shuttle_dest_miles'}
						{assign var=SPEC_CLASS value=' shuttleDestMiles shuttleDest'}
						{assign var=SPEC_CLASS_HIDE value=' hide shuttleDestMilesHide'}
						{assign var=SHOW_SHUTTLE value=false}
					{elseif $FIELD_MODEL->getName() eq 'acc_selfstg_origin_ot'}
						{assign var=SPEC_CLASS value=' selfstgOriginOT'}
						{assign var=SPEC_CLASS_HIDE value=' hide selfstgOriginOTHide'}
					{elseif $FIELD_MODEL->getName() eq 'acc_selfstg_dest_ot'}
						{assign var=SPEC_CLASS value=' selfstgDestOT shuttleDest'}
						{assign var=SPEC_CLASS_HIDE value=' hide selfstgDestOTHide'}
						{if !$SHOW_SHUTTLE_SELF_MINI }
							{assign var=SHOW_SHUTTLE value=false}
						{/if}
					{elseif $FIELD_MODEL->getName() eq 'acc_shuttle_dest_applied' || $FIELD_MODEL->getName() eq 'acc_shuttle_dest_weight'}
						{assign var=SPEC_CLASS value=' shuttleDest'}
						{assign var=SHOW_SHUTTLE value=false}
					{else}
						{assign var=SPEC_CLASS value=''}
						{assign var=SPEC_CLASS_HIDE value=' hide'}
					{/if}
                    {if $FIELD_MODEL->getName() neq 'accesorial_ot_unpacking' &&  $FIELD_MODEL->getName() neq 'accesorial_ot_packing'}
					<td class="fieldLabel {$WIDTHTYPE}{if $BLOCK_LABEL eq "LBL_QUOTES_ACCESSORIALDETAILS" && $SPEC_CLASS neq ''}{$SPEC_CLASS}{/if}">
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
							{elseif $FIELD_MODEL->get('uitype') eq "83"}
								{include file=vtemplate_path($FIELD_MODEL->getUITypeModel()->getTemplateName(),$MODULE) COUNTER=$COUNTER}
							{else}
								{if getenv('INSTANCE_NAME') neq 'sirva' || $SHOW_SHUTTLE}
									{vtranslate($FIELD_MODEL->get('label'), $MODULE)}
								{/if}
							{/if}
						{if $isReferenceField neq "reference"}</label>{/if}
					</td>

					{if $BLOCK_LABEL eq "LBL_QUOTES_ACCESSORIALDETAILS" && $SPEC_CLASS neq ''}<td class="fieldLabel {$WIDTHTYPE}{$SPEC_CLASS_HIDE}"></td>{/if}
					{if $FIELD_MODEL->get('uitype') neq "83"}
						<td class="fieldValue {$WIDTHTYPE}{if $BLOCK_LABEL eq "LBL_QUOTES_ACCESSORIALDETAILS" && $SPEC_CLASS neq ''}{$SPEC_CLASS}{/if}" {if $FIELD_MODEL->get('uitype') eq '19'} colspan="3" {assign var=COUNTER value=$COUNTER+1} {/if} {if $FIELD_MODEL->get('uitype') eq '20'} colspan="3"{/if}>
							{if getenv('INSTANCE_NAME') neq 'sirva' || $SHOW_SHUTTLE}
								{include file=vtemplate_path($FIELD_MODEL->getUITypeModel()->getTemplateName(),$MODULE) BLOCK_FIELDS=$BLOCK_FIELDS}
							{/if}
						</td>
						{if $BLOCK_LABEL eq "LBL_QUOTES_ACCESSORIALDETAILS" && $SPEC_CLASS neq ''}<td class="fieldValue {$WIDTHTYPE}{$SPEC_CLASS_HIDE}"></td>{/if}
					{/if}
					{if $BLOCK_FIELDS|@count eq 1 and $FIELD_MODEL->get('uitype') neq "19" and $FIELD_MODEL->get('uitype') neq "20" and $FIELD_MODEL->get('uitype') neq "30" and $FIELD_MODEL->get('name') neq "recurringtype"}
						<td class="{$WIDTHTYPE}"></td><td class="{$WIDTHTYPE}"></td>
					{/if}
                    {else}
                        <td class="fieldLabel {$WIDTHTYPE}"></td><td class="{$WIDTHTYPE}"></td>
                    {/if}
				{/foreach}
				{* adding additional column for odd number of fields in a block *}
				{if $BLOCK_FIELDS|@end eq true and $BLOCK_FIELDS|@count neq 1 and $COUNTER eq 1}
					<td class="fieldLabel {$WIDTHTYPE}"></td><td class="{$WIDTHTYPE}"></td>
				{/if}
				</tr>
					{if $BLOCK_LABEL == 'LBL_QUOTES_INTERSTATEMOVEDETAILS'}
						<script type='text/javascript' src='libraries/jquery/colorbox/jquery.colorbox-min.js'></script>

						{if !$LOCK_RATING}
							<tr><td class='fieldLabel'></td><td class='fieldValue'><button type='button' id='interstateRateQuick'>Quick Rate Estimate</button></td><td class='fieldLabel'></td><td class='fieldValue'><button type='button' class='interstateRateDetail'>{vtranslate('LBL_DETAILED_RATE_ESTIMATE', $MODULE)}</button></td></tr>
						{/if}
					{/if}
				</tbody>
				</table>
				<br>
			{/foreach}
			{assign var=PACK_LABEL_WIDTH value='15%'}
			{assign var=PACK_CSTRATE_WIDTH value='8%'}
			{assign var=PACK_VALUE_WIDTH value='10%'}
			{assign var=BULKY_LABEL_WIDTH value='15%'}
			{assign var=BULKY_VALUE_WIDTH value='10%'}
			{assign var=PACKING_WIDTH value=0}

			{if $CUSTOM_RATES.apply_custom_pack_rate_override == 1}
				{assign var=PACKING_WIDTH value=100/6}
			{else}
				{assign var=PACKING_WIDTH value=100/4}
			{/if}

			<table class='table table-bordered detailview-table packing' data-tariff-type="{$TARIFF_TYPE}">
				<thead>
					<tr>
						<th class='blockHeader' colspan='6'>
						<img class="cursorPointer alignMiddle blockToggle {if !($IS_HIDDEN)} hide {/if} "  src="{vimage_path('arrowRight.png')}" data-mode="hide">
						<img class="cursorPointer alignMiddle blockToggle {if ($IS_HIDDEN)} hide {/if}"  src="{vimage_path('arrowDown.png')}" data-mode="show">
						&nbsp;&nbsp;Packing
					</th>
					</tr>
				</thead>
				<tbody id='packingTab'{if $IS_HIDDEN} class="hide" {/if}>
					{if in_array($TARIFF_TYPE, $CUSTOM_TARIFF_TYPE_FOR_USE_CUSTOM_RATES)}
					<tr>
						<td class="fieldLabel">
							<label class="muted pull-right marginRight10px">Use Custom Rates</label>
						</td>
						<td class="fieldValue" style="text-align:left">
							<input type="hidden" name="apply_custom_pack_rate_override" value="0">
							<input type="checkbox" name="apply_custom_pack_rate_override" {if $CUSTOM_RATES.apply_custom_pack_rate_override == 1}checked{/if}>
						</td>
						<td colspan="4" class="fieldValue packingCustomRate" style="text-align:left">
							<button type='button' name="LoadTariffPacking" class="hide">Load Tariff Packing</button>
						</td>
					</tr>
					{/if}
					<tr>
                        <td></td>
                        <td></td>
						{if in_array($TARIFF_TYPE, $CUSTOM_TARIFF_TYPE_FOR_USE_CUSTOM_RATES)}
						<td class="packingCustomRate"></td>
					{/if}
                        <td>
                            <span class="muted marginRight10px otPackingField">OT Packing</span> &nbsp;
                            {assign var=OTPackingField value = $RECORD_STRUCTURE['LBL_QUOTES_ACCESSORIALDETAILS']['accesorial_ot_packing']}
                            {include file=vtemplate_path($OTPackingField->getUITypeModel()->getTemplateName(),$MODULE) FIELD_MODEL = $OTPackingField BLOCK_FIELDS=$RECORD_STRUCTURE['LBL_QUOTES_ACCESSORIALDETAILS']}
						</td>
						{if in_array($TARIFF_TYPE, $CUSTOM_TARIFF_TYPE_FOR_USE_CUSTOM_RATES)}
						<td class="packingCustomRate"></td>
						{/if}
						<td>
                            <span class="muted marginRight10px otPackingField">OT Unpacking</span> &nbsp;
                            {assign var=OTUnPackingField value = $RECORD_STRUCTURE['LBL_QUOTES_ACCESSORIALDETAILS']['accesorial_ot_unpacking']}
                            {include file=vtemplate_path($OTUnPackingField->getUITypeModel()->getTemplateName(),$MODULE) FIELD_MODEL = $OTUnPackingField BLOCK_FIELDS=$RECORD_STRUCTURE['LBL_QUOTES_ACCESSORIALDETAILS']}
						</td>
					</tr>
					<tr>
						<td class="fieldLabel"></td>
						<td class="fieldValue ContCol">{vtranslate('Containers', $MODULE)}</td>
						{if in_array($TARIFF_TYPE, $CUSTOM_TARIFF_TYPE_FOR_USE_CUSTOM_RATES)}
						<td class="fieldValue packingCustomRate">{vtranslate('Custom Rate', $MODULE)}</td>
						{/if}
						<td class="fieldValue PkCol">{vtranslate('Packing', $MODULE)}</td>
						{if in_array($TARIFF_TYPE, $CUSTOM_TARIFF_TYPE_FOR_USE_CUSTOM_RATES)}
						<td class="fieldValue packingPackRate">{vtranslate('Pack Rate', $MODULE)}</td>
						{/if}
						<td class="fieldValue UnpkCol">{vtranslate('Unpacking', $MODULE)}</td>
					</tr>
					{assign var=COUNTER value=0}
					{foreach item=PACKING_ITEM key=ITEM_NUM from=$PACKING_ITEMS}
					<tr data-pack_item_id="{$ITEM_NUM}" data-pack_item_name="{$PACKING_ITEM.label}">
						{assign var=COUNTER value=$COUNTER+1}
						<td class='fieldLabel {$WIDTHTYPE}' style="width: {$PACKING_WIDTH}%;">
							<label class="muted pull-right marginRight10px">
								{$PACKING_ITEM.label}
							</label>
						</td>
						<td style="text-align:center; width: {$PACKING_WIDTH}%;" class="fieldValue {$WIDTHTYPE} ContCol">
							<input type="number" min="0" max class="input-medium contQtyField" name="pack_cont{$ITEM_NUM}"
								   value="{($PACKING_ITEM.cont)?$PACKING_ITEM.cont:0}" />
						</td>
						{if in_array($TARIFF_TYPE, $CUSTOM_TARIFF_TYPE_FOR_USE_CUSTOM_RATES)}
						<td style='text-align:center; width: {$PACKING_WIDTH}%;' class='fieldValue {$WIDTHTYPE} packingCustomRate'>
							<div class="row-fluid">
								<div class="input-append" style="margin: auto;">
									<input name="packCustomRate{$ITEM_NUM}" style="text-align:center;"
										   class="input-medium currencyField" type="text" value="{number_format($PACKING_ITEM.customRate, 2, '.', '')}"
										   data-decimal-seperator="." data-group-seperator="," data-number-of-decimal-places="2"/>
									<span class="add-on">&#36;</span>
								</div>
							</div>
						</td>
						{/if}
						<td style='text-align:center; width: {$PACKING_WIDTH}%;' class='fieldValue {$WIDTHTYPE} PkCol'>
							<input type='number' min='0' max class='input-medium packQtyField' name='pack{$ITEM_NUM}'
								   value='{($PACKING_ITEM.pack)?$PACKING_ITEM.pack:0}' />
						</td>
						{if in_array($TARIFF_TYPE, $CUSTOM_TARIFF_TYPE_FOR_USE_CUSTOM_RATES)}
						<td style='text-align:center; width: {$PACKING_WIDTH}%;' class='fieldValue {$WIDTHTYPE} packingPackRate'>
							<div class="row-fluid">
								<div class="input-append" style="margin: auto;">
									<input name="packPackRate{$ITEM_NUM}" style="text-align:center;"
										   class="input-medium currencyField" type="text" value="{number_format($PACKING_ITEM.packRate, 2, '.', '')}"
										   data-decimal-seperator="." data-group-seperator="," data-number-of-decimal-places="2"/>
									<span class="add-on">&#36;</span>
								</div>
							</div>
						</td>
						{/if}
						<td style='text-align:center; width: {$PACKING_WIDTH}%;' class='fieldValue {$WIDTHTYPE} UnpkCol'>
							<input type='number' min='0' max class='input-medium unpackQtyField' name='unpack{$ITEM_NUM}'
								   value='{($PACKING_ITEM.unpack)?$PACKING_ITEM.unpack:0}' />
						</td>
					</tr>
					{/foreach}
				</tbody>
			</table>
			<br />

			{*<table class='table table-bordered detailview-table packing'>*}
				{*<thead>*}
					{*<th class='blockHeader' colspan='9'>*}
						{*<img class="cursorPointer alignMiddle blockToggle {if !($IS_HIDDEN)} hide {/if} "  src="{vimage_path('arrowRight.png')}" data-mode="hide">*}
						{*<img class="cursorPointer alignMiddle blockToggle {if ($IS_HIDDEN)} hide {/if}"  src="{vimage_path('arrowDown.png')}" data-mode="show">*}
						{*&nbsp;&nbsp;OT Packing*}
					{*</th>*}
				{*</thead>*}
				{*{assign var=PACK_VALUE_WIDTH value='15%'}*}
				{*<tbody id='otPackingTab'{if $IS_HIDDEN} class="hide" {/if}>*}
					{*<tr>*}
						{*<td style='width:{$PACK_LABEL_WIDTH}' class="fieldLabel"></td>*}
						{*<td style='width:{$PACK_VALUE_WIDTH}' class="fieldValue">Pk</td>*}
						{*<td style='width:{$PACK_VALUE_WIDTH}' class="fieldValue">Unpk</td>*}
						{*<td style='width:{$PACK_LABEL_WIDTH}' class="fieldLabel"></td>*}
						{*<td style='width:{$PACK_VALUE_WIDTH}' class="fieldValue">Pk</td>*}
						{*<td style='width:{$PACK_VALUE_WIDTH}' class="fieldValue">Unpk</td>*}
					{*</tr>*}
					{*{assign var=COUNTER value=0}*}
					{*<tr>*}
					{*{foreach item=PACKING_ITEM key=ITEM_NUM from=$PACKING_ITEMS}*}
					{*{if $COUNTER eq 2}*}
					{*</tr>*}
					{*<tr>*}
					{*{assign var=COUNTER value=1}*}
					{*{else}*}
						{*{assign var=COUNTER value=$COUNTER+1}*}
					{*{/if}*}
						{*<td style='width:{$PACK_LABEL_WIDTH}; padding:0 5px 0 0;' class='fieldLabel {$WIDTHTYPE}'>*}
							{*<label class="muted pull-right marginRight10px">*}
								{*{$PACKING_ITEM.label}*}
							{*</label>*}
						{*</td>*}
						{*<td style='width:{$PACK_VALUE_WIDTH}; text-align:center;' class='fieldValue {$WIDTHTYPE}'>*}
							{*<input type='number' min='0' max class='input-large packQtyField' name='ot_pack{$ITEM_NUM}' style='width:80%;' value='{$PACKING_ITEM.otpack}' />*}
						{*</td>*}
						{*<td style='width:{$PACK_VALUE_WIDTH}; text-align:center;' class='fieldValue {$WIDTHTYPE}'>*}
							{*<input type='number' min='0' max class='input-large unpackQtyField' name='ot_unpack{$ITEM_NUM}' style='width:80%;' value='{$PACKING_ITEM.otunpack}' />*}
						{*</td>*}
					{*{/foreach}*}
					{*{while $COUNTER lt 2}*}
						{*<td style='width:{$PACK_LABEL_WIDTH}; padding:0 5px 0 0;' class='fieldLabel {$WIDTHTYPE}'>*}
							{*&nbsp;*}
						{*</td>*}
						{*<td style='width:{$PACK_VALUE_WIDTH};' class='{$WIDTHTYPE}'>*}
							{*&nbsp;*}
						{*</td>*}
						{*<td style='width:{$PACK_VALUE_WIDTH};' class='{$WIDTHTYPE}'>*}
							{*&nbsp;*}
						{*</td>*}
						{*{assign var=COUNTER value=$COUNTER+1}*}
					{*{/while}*}
					{*</tr>*}
				{*</tbody>*}
			{*</table>*}
			{*<br />*}

			<table class='table table-bordered detailview-table bulky'>
				<thead>
					{assign var=BULKY_TABLE_LABEL value = 'BULKY_TABLE_LABEL'}
					<th class='blockHeader' colspan='8'>
						<img class="cursorPointer alignMiddle blockToggle {if !($IS_HIDDEN)} hide {/if} "  src="{vimage_path('arrowRight.png')}" data-mode="hide">
						<img class="cursorPointer alignMiddle blockToggle {if ($IS_HIDDEN)} hide {/if}"  src="{vimage_path('arrowDown.png')}" data-mode="show">
						&nbsp;&nbsp;{vtranslate({$BULKY_TABLE_LABEL},{$MODULE_NAME})}
					</th>
				</thead>
				<tbody id='bulkyItemsTab'{if $IS_HIDDEN} class="hide" {/if}>
					{assign var=COUNTER value=0}
					<tr>
					{foreach item=BULKY_ITEM key=ITEM_NUM from=$BULKY_ITEMS}
					{if $COUNTER eq 4}
					</tr>
					<tr>
					{assign var=COUNTER value=1}
					{else}
						{assign var=COUNTER value=$COUNTER+1}
					{/if}
						<td style='width:{$BULKY_LABEL_WIDTH}; padding:0 5px 0 0;' class='fieldLabel {$WIDTHTYPE}'>
							<label class="muted pull-right marginRight10px">
								{$BULKY_ITEM.label}
							</label>
						</td>
						<td style='width:{$BULKY_VALUE_WIDTH}; text-align:center;' class='fieldValue {$WIDTHTYPE}'>
							<input type='number' min='0' class='input-large' name='bulky{$ITEM_NUM}' style='width:80%;' value='{$BULKY_ITEM.qty}' />
						</td>
					{/foreach}
					{while $COUNTER lt 4}
						<td style='width:{$BULKY_LABEL_WIDTH}; padding:0 5px 0 0;' class='fieldLabel {$WIDTHTYPE}'>
							&nbsp;
						</td>
						<td style='width:{$BULKY_VALUE_WIDTH}' class='{$WIDTHTYPE}'>
							&nbsp;
						</td>
						{assign var=COUNTER value=$COUNTER+1}
					{/while}
					</tr>
				</tbody>
			</table>
			<br />

			<table class='table table-bordered blockContainer showInlineTable misc'>
				<thead>
					<th class='blockHeader' colspan='5'>
						<img class="cursorPointer alignMiddle blockToggle {if !($IS_HIDDEN)} hide {/if} "  src="{vimage_path('arrowRight.png')}" data-mode="hide">
						<img class="cursorPointer alignMiddle blockToggle {if ($IS_HIDDEN)} hide {/if}"  src="{vimage_path('arrowDown.png')}" data-mode="show">
						&nbsp;&nbsp;Flat Charge Item Details
					</th>
				</thead>
				<tbody id='flatItemsTab'{if $IS_HIDDEN} class="hide" {/if}>
						<tr>
							<td colspan='5' style='padding:0'>
								<button type='button' id='addFlatChargeItem'>+</button>
								<button type='button' id='addFlatChargeItem2' style='clear:right;float:right;'>+</button><br />
							</td>
						</tr>
					<tr>
						<td style='width:5%'>
							&nbsp;
						</td>
						<td style='width:30%'>
							<span class="redColor">*</span><b>Description</b>
						</td>
						<td style='width:25%'>
							<span class="redColor">*</span><b>Charge</b>
						</td>
						<td style='width:20%'>
							<b>Discountable</b>
						</td>
					</tr>
					<tr class='hide defaultFlatItem flatItemRow newItemRow'>
						<td class='fieldValue' style='text-align:center'>
							<a class="deleteMiscChargeButton">
								<i title="Delete" class="icon-trash alignMiddle"></i>
							</a>
						</td>
						<td class='fieldValue' style='text-align:center'>
							<input type='text' class='input-large' style='width:90%' name='flatDescription' />
						</td>
						<td class='fieldValue' style='text-align:center'>
							<div class='input-prepend input-prepend-centered'>
								<span class='add-on'>$</span>
								<input type='text' class='input-medium currencyField' style='width:80%;float:left' name='flatCharge' value='0.00' data-decimal-seperator="." data-group-seperator="," data-number-of-decimal-places="2" />
							</div>
						</td>
						<td class='fieldValue'>
							<input type='hidden' name='flatDiscounted' value='0' />
							<input type='checkbox' name='flatDiscounted' />
						</td>
					</tr>
					{foreach item=MISC_CHARGE_ROW key=ROW_NUM from=$MISC_CHARGES.flat}
					<tr class='flatItemRow' id='flatItemRow{$ROW_NUM}'>
						<td class='fieldValue' style='text-align:center'>
							<a class="deleteMiscChargeButton">
								{if $MISC_CHARGE_ROW->enforced == 0}<i title="Delete" class="icon-trash alignMiddle"></i>{/if}
							</a>
						</td>
						<td class='fieldValue' style='text-align:center'>
							<input type='text' class='input-large' style='width:90%' name='flatDescription{$ROW_NUM}' value='{$MISC_CHARGE_ROW->description}' {if $MISC_CHARGE_ROW->enforced == 1}readonly{/if} />
						</td>
						<td class='fieldValue' style='text-align:center'>
							<div class='input-prepend input-prepend-centered'>
								<span class='add-on'>$</span>
								<input type='text' class='input-small currencyField' style='width:80%;float:left' name='flatCharge{$ROW_NUM}' value='{$MISC_CHARGE_ROW->charge}' {if $MISC_CHARGE_ROW->enforced == 1}readonly{/if} />
							</div>
						</td>
						<td class='fieldValue'>
							<input type='hidden' name='flatDiscounted{$ROW_NUM}' value='{if $MISC_CHARGE_ROW->enforced == 0}0{else}{if $MISC_CHARGE_ROW->discounted eq '1'}1{else}0{/if}{/if}' />
							{if $MISC_CHARGE_ROW->enforced == 0}<input type='checkbox' name='flatDiscounted{$ROW_NUM}'{if $MISC_CHARGE_ROW->discounted eq '1'} checked{/if} />{else}{if $MISC_CHARGE_ROW->discounted eq '1'}Yes{else}No{/if}{/if}
						</td>
							<input type='hidden' class='lineItemId' name='flatLineItemId{$ROW_NUM}' value='{$MISC_CHARGE_ROW->lineItemId}' />
							<input type='hidden' class='enforced' name='flatEnforced{$ROW_NUM}' value='{$MISC_CHARGE_ROW->enforced}' />
							<input type='hidden' class='blah' name='flatFromContract{$ROW_NUM}' value='{$MISC_CHARGE_ROW->fromContract}' />
					</tr>
					{/foreach}
				</tbody>
			</table>
			<br />

			<table class='table table-bordered blockContainer showInlineTable misc'>
				<thead>
					<th class='blockHeader' colspan='6'>
						<img class="cursorPointer alignMiddle blockToggle {if !($IS_HIDDEN)} hide {/if} "  src="{vimage_path('arrowRight.png')}" data-mode="hide">
						<img class="cursorPointer alignMiddle blockToggle {if ($IS_HIDDEN)} hide {/if}"  src="{vimage_path('arrowDown.png')}" data-mode="show">
						&nbsp;&nbsp;Qty/Rate Item Details
					</th>
				</thead>
				<tbody id='qtyRateItemsTab'{if $IS_HIDDEN} class="hide" {/if}>
						<tr>
							<td colspan='6' style='padding:0'>
								<button type='button' id='addQtyRateChargeItem'>+</button>
								<button type='button' id='addQtyRateChargeItem2' style='clear:right; float:right;'>+</button>
							</td>
						</tr>
					<tr>
						<td style='width:5%'>
							&nbsp;
						</td>
						<td style='width:30%'>
							<span class="redColor">*</span><b>Description</b>
						</td>
						<td style='width:20%'>
							<span class="redColor">*</span><b>Rate</b>
						</td>
						<td style='width:15%'>
							<span class="redColor">*</span><b>Qty</b>
						</td>
						<td style='width:15%'>
							<b>Discountable</b>
						</td>
					</tr>
					<tr class='hide defaultQtyRateItem qtyRateItemRow newItemRow'>
						<td class='fieldValue' style='text-align:center'>
							<a class="deleteMiscChargeButton">
								<i title="Delete" class="icon-trash alignMiddle"></i>
							</a>
						</td>
						<td class='fieldValue' style='text-align:center'>
							<input type='text' class='input-large' style='width:90%' name='qtyRateDescription' />
						</td>
						<td class='fieldValue' style='text-align:center'>
							<div class='input-prepend input-prepend-centered'>
								<span class='add-on'>$</span>
								<input type='text' class='input-small currencyField' style='width:80%;float:left' name='qtyRateCharge' value='0.00' />
							</div>
						</td>
						<td class='fieldValue' style='text-align:center'>
							<input type='text' class='input-small' style='width:90%' name='qtyRateQty' value='1' />
						</td>
						<td class='fieldValue'>
							<input type='hidden' name='qtyRateDiscounted' value='0' />
							<input type='checkbox' name='qtyRateDiscounted' />
						</td>
					</tr>
					{foreach item=MISC_CHARGE_ROW key=ROW_NUM from=$MISC_CHARGES.qty}
					<tr class='qtyRateItemRow' id='qtyRateItemRow{$ROW_NUM}'>
						<td class='fieldValue' style='text-align:center'>
							<a class="deleteMiscChargeButton">
								{if $MISC_CHARGE_ROW->enforced == 0}<i title="Delete" class="icon-trash alignMiddle"></i>{/if}
							</a>
						</td>
						<td class='fieldValue' style='text-align:center'>
							<input type='text' class='input-large' style='width:90%' name='qtyRateDescription{$ROW_NUM}' value='{$MISC_CHARGE_ROW->description}' {if $MISC_CHARGE_ROW->enforced == 1}readonly{/if} />
						</td>
						<td class='fieldValue' style='text-align:center'>
							<div class='input-prepend input-prepend-centered'>
								<span class='add-on'>$</span>
								<input type='text' class='input-small currencyField' style='width:80%;float:left' name='qtyRateCharge{$ROW_NUM}' value='{$MISC_CHARGE_ROW->charge}' {if $MISC_CHARGE_ROW->enforced == 1}readonly{/if} />
							</div>
						</td>
						<td class='fieldValue' style='text-align:center'>
							<input type='text' class='input-small' style='width:90%' name='qtyRateQty{$ROW_NUM}' value='{$MISC_CHARGE_ROW->qty}' {if $MISC_CHARGE_ROW->enforced == 1}readonly{/if} />
						</td>
						<td class='fieldValue'>
							<input type='hidden' name='qtyRateDiscounted{$ROW_NUM}' value='{if $MISC_CHARGE_ROW->enforced == 0}0{else}{if $MISC_CHARGE_ROW->discounted eq '1'}1{else}0{/if}{/if}' />
							{if $MISC_CHARGE_ROW->enforced == 0}<input type='checkbox' name='qtyRateDiscounted{$ROW_NUM}'{if $MISC_CHARGE_ROW->discounted eq '1'} checked{/if} />{else}{if $MISC_CHARGE_ROW->discounted eq '1'}Yes{else}No{/if}{/if}
						</td>
							<input type='hidden' class='lineItemId' name='qtyRateLineItemId{$ROW_NUM}' value='{$MISC_CHARGE_ROW->lineItemId}' />
							<input type='hidden' class='enforced' name='qtyRateEnforced{$ROW_NUM}' value='{$MISC_CHARGE_ROW->enforced}' />
							<input type='hidden' class='blah' name='qtyRateFromContract{$ROW_NUM}' value='{$MISC_CHARGE_ROW->fromContract}' />
					</tr>
					{/foreach}
				</tbody>
			</table>
			<br />

			<table class='table table-bordered blockContainer showInlineTable misc'>
				<thead>
					<th class='blockHeader' colspan='10'>
						<img class="cursorPointer alignMiddle blockToggle {if !($IS_HIDDEN)} hide {/if} "  src="{vimage_path('arrowRight.png')}" data-mode="hide">
						<img class="cursorPointer alignMiddle blockToggle {if ($IS_HIDDEN)} hide {/if}"  src="{vimage_path('arrowDown.png')}" data-mode="show">
						&nbsp;&nbsp;Crate Details
					</th>
				</thead>
				<tbody id='cratesTab'{if $IS_HIDDEN} class="hide" {/if}>
						<tr>
						<td style='padding:0'>
								<button type='button' id='addCrate'>+</button>
						</td>
						<td class="fieldLabel">
							&nbsp;
						</td>
						<td class="fieldValue" style="text-align:left">
							&nbsp;
						</td>
						<td class="fieldLabel cratingCustomRate hide">
							<label class="muted pull-right marginRight10px">Custom Crate Rate</label>
						</td>
						<td class="fieldValue cratingCustomRate hide" style="text-align:left">
							<input type="text" name="tpg_custom_crate_rate" value="{$CUSTOM_RATES.tpg_custom_crate_rate}">
						</td>
						<td colspan="3" class="cratingCustomRate hide">
							<button type='button' name="LoadTariffCrating" class="hide">Load Tariff Crating</button>
						</td>
						<td colspan="10" style="padding:0">
								<button type='button' id='addCrate2' style='clear:right; float:right;'>+</button>
							</td>
						</tr>
					<tr>
						<td style='text-align:center; background-color:#E8E8E8;' colspan='3'>
							&nbsp;
						</td>
						<td style='text-align:center; background-color:#E8E8E8;' colspan='3'>
							<b>Dimensions (in)</b>
						</td>
						<td style='text-align:center; background-color:#E8E8E8;' colspan='2'>
							<b>Pack</b>
						</td>
						<td style='text-align:center; background-color:#E8E8E8;' colspan='2'>
							<b>OT Pack</b>
						</td>
					</tr>
					<tr>
						<td style='width:5%'>
							&nbsp;
						</td>
						<td style='width:5%'>
							<b>ID</b>
						</td>
						<td style='width:15%'>
							<span class="redColor">*</span><b>Description</b>
						</td>
						<td style='width:9.5%'>
							<span class="redColor">*</span><b>L</b>
						</td>
						<td style='width:9.5%'>
							<span class="redColor">*</span><b>W</b>
						</td>
						<td style='width:9.5%'>
							<span class="redColor">*</span><b>H</b>
						</td>
						<td style='width:9.5%'>
							<b>Pack</b>
						</td>
						<td style='width:9%'>
							<b>Unpack</b>
						</td>
						<td style='width:9.5%'>
							<b>Pack</b>
						</td>
						<td style='width:9%'>
							<b>Unpack</b>
						</td>
					</tr>
					<tr class='hide defaultCrate crateRow newItemRow'>
						<td class='fieldValue' style='text-align:center'>
							<a class="deleteMiscChargeButton">
								<i title="Delete" class="icon-trash alignMiddle"></i>
							</a>
						</td>
						<td class='fieldValue' style='text-align:center;'>
							<input type='text' class='input-large' name='crateID' style='width:80%' value />
							<input type='hidden' class='fieldname' value='crateID' data-prev-value />
						</td>
						<td class='fieldValue' style='text-align:center;'>
							<input type='text' class='input-large' name='crateDescription' style='width:80%' />
							<input type='hidden' class='fieldname' value='crateDescription' data-prev-value />
						</td>
						<td class='fieldValue' style='text-align:center;'>
							<input type='text' class='input-large' name='crateLength' style='width:80%' value />
							<input type='hidden' class='fieldname' value='crateLength' data-prev-value />
						</td>
						<td class='fieldValue' style='text-align:center;'>
							<input type='text' class='input-large' name='crateWidth' style='width:80%' value />
							<input type='hidden' class='fieldname' value='crateWidth' data-prev-value />
						</td>
						<td class='fieldValue' style='text-align:center;'>
							<input type='text' class='input-large' name='crateHeight' style='width:80%' value />
							<input type='hidden' class='fieldname' value='crateHeight' data-prev-value />
						</td>
						<td class='fieldValue'>
							<input type='checkbox' name='cratePack' />
							<input type='hidden' class='fieldname' value='cratePack' data-prev-value='no' />
						</td>
						<td class='fieldValue'>
							<input type='checkbox' name='crateUnpack' />
							<input type='hidden' class='fieldname' value='crateUnpack' data-prev-value='no' />
						</td>
						<td class='fieldValue'>
							<input type='checkbox' name='crateOTPack' />
							<input type='hidden' class='fieldname' value='crateOTPack' data-prev-value='no' />
						</td>
						<td class='fieldValue'>
							<input type='checkbox' name='crateOTUnpack' />
							<input type='hidden' class='fieldname' value='crateOTUnpack' data-prev-value='no' />
						</td>
					</tr>
					{foreach item=CRATE_ROW key=ROW_NUM from=$CRATES}
					<tr class='crateRow' id='crateRow{$ROW_NUM}'>
						<td class='fieldValue' style='text-align:center'>
							<a class="deleteMiscChargeButton">
								<i title="Delete" class="icon-trash alignMiddle"></i>
							</a>
						</td>
						<td class='fieldValue' style='text-align:center;'>
							<input type='text' class='input-large' name='crateID{$ROW_NUM}' style='width:80%' value='{$CRATE_ROW->crateid}' />
						</td>
						<td class='fieldValue' style='text-align:center;'>
							<input type='text' class='input-large' name='crateDescription{$ROW_NUM}' style='width:80%' value='{$CRATE_ROW->description}' />
						</td>
						<td class='fieldValue' style='text-align:center;'>
							<input type='text' class='input-large' name='crateLength{$ROW_NUM}' style='width:80%' value='{$CRATE_ROW->crateLength}' />
						</td>
						<td class='fieldValue' style='text-align:center;'>
							<input type='text' class='input-large' name='crateWidth{$ROW_NUM}' style='width:80%' value='{$CRATE_ROW->crateWidth}' />
						</td>
						<td class='fieldValue' style='text-align:center;'>
							<input type='text' class='input-large' name='crateHeight{$ROW_NUM}' style='width:80%' value='{$CRATE_ROW->crateHeight}' />
						</td>
						<td class='fieldValue'>
							<input type='hidden' name='cratePack{$ROW_NUM}' value='0' />
							<input type='checkbox' name='cratePack{$ROW_NUM}'{if $CRATE_ROW->pack eq '1'} checked{/if} />
						</td>
						<td class='fieldValue'>
							<input type='hidden' name='crateUnpack{$ROW_NUM}' value='0' />
							<input type='checkbox' name='crateUnpack{$ROW_NUM}'{if $CRATE_ROW->unpack eq '1'} checked{/if} />
						</td>
						<td class='fieldValue'>
							<input type='hidden' name='crateOTPack{$ROW_NUM}' value='0' />
							<input type='checkbox' name='crateOTPack{$ROW_NUM}'{if $CRATE_ROW->otpack eq '1'} checked{/if} />
						</td>
						<td class='fieldValue'>
							<input type='hidden' name='crateOTUnpack{$ROW_NUM}' value='0' />
							<input type='checkbox' name='crateOTUnpack{$ROW_NUM}'{if $CRATE_ROW->otunpack eq '1'} checked{/if} />
						</td>
						<input type='hidden' class='lineItemId' value='{$CRATE_ROW->lineItemId}' name='crateLineItemId{$ROW_NUM}'/>
					</tr>
					{/foreach}
				</tbody>
			</table>
			<br />
{if 1==1}
{if
$TARIFF_TYPE == 'ALLV-2A' ||
$TARIFF_TYPE == 'NAVL-12A' ||
$TARIFF_TYPE == '400N Base' ||
$TARIFF_TYPE == '400N/104G' ||
$TARIFF_TYPE == '400NG'
}
	{include file=vtemplate_path('CorporateVehicles.tpl',$MODULE)}
{else}
<table class='table table-bordered blockContainer showInlineTable misc'>
	<thead>
		<th class='blockHeader' colspan='6'>
			<img class="cursorPointer alignMiddle blockToggle {if !($IS_HIDDEN)} hide {/if} "  src="{vimage_path('arrowRight.png')}" data-mode="hide">
			<img class="cursorPointer alignMiddle blockToggle {if ($IS_HIDDEN)} hide {/if}"  src="{vimage_path('arrowDown.png')}" data-mode="show">
			&nbsp;&nbsp;Vehicles
		</th>
	</thead>
	<tbody id='vehiclesTab'{if $IS_HIDDEN} class="hide" {/if}>
			<tr>
			<td colspan='6' style='padding:0'>
					<button type='button' id='addVehicle'>+</button>
					<button type='button' id='addVehicle2' style='clear:right;float:right;'>+</button><br />
				</td>
			</tr>
		<tr>
			<td style='width:15%'>
				&nbsp;
			</td>
			<td style='width:17%'>
				<span class="redColor">*</span><b>Description</b>
			</td>
			<td style='width:17%'>
				<span class="redColor">*</span><b>Weight</b>
			</td>
			<td style='width:17%'>
				<b>Make</b>
			</td>
			<td style='width:17%'>
				<b>Model</b>
			</td>
			<td style='width:17%'>
				<b>Year</b>
			</td>
		</tr>
		<tr class='hide vehicleItem vehicleRow newVehicleRow'>
			<td class='fieldValue' style='text-align:center'>
				<a class="deleteVehicleButton">
					<i title="Delete" class="icon-trash alignMiddle"></i>
				</a>
			</td>
			<td class='fieldValue' style='text-align:center'>
				<input type='text' class='input-large' style='width:90%' name='vehicleDescription' />
			</td>
			<td class='fieldValue' style='text-align:center'>
				<div class='input-prepend input-prepend-centered'>
					<input type='text' class='input-medium' style='float:left' name='vehicleWeight' value=''/>
				</div>
			</td>
			<td class='fieldValue' style='text-align:center'>
				<div class='input-prepend input-prepend-centered'>
					<input type='text' class='input-medium' style='float:left' name='vehicleMake' value=''/>
				</div>
			</td>
			<td class='fieldValue' style='text-align:center'>
				<div class='input-prepend input-prepend-centered'>
					<input type='text' class='input-medium' style='float:left' name='vehicleModel' value=''/>
				</div>
			</td>
			<td class='fieldValue' style='text-align:center'>
				<div class='input-prepend input-prepend-centered'>
					<input type='text' class='input-medium' style='float:left' name='vehicleYear' value=''/>
				</div>
			</td>
		</tr>
		{foreach item=VEHICLE_ROW key=ROW_NUM from=$VEHICLES}
		<tr class='vehicleItem vehicleRow' id='vehicleRow-{$ROW_NUM}'>
			<td class='fieldValue' style='text-align:center'>
				<a class="deleteMiscChargeButton">
					<i title="Delete" class="icon-trash alignMiddle"></i>
				</a>
			</td>
			<td class='fieldValue' style='text-align:center'>
				<input type='text' class='input-large' style='width:90%' name='vehicleDescription-{$ROW_NUM}' value='{$VEHICLE_ROW["description"]}'/>
				<input type='hidden' name='vehicleID-{$ROW_NUM}' value='{$VEHICLE_ROW["vehicle_id"]}'>
			</td>
			<td class='fieldValue' style='text-align:center'>
				<div class='input-prepend input-prepend-centered'>
					<input type='text' class='input-large' style='width:90%' name='vehicleWeight-{$ROW_NUM}' value='{$VEHICLE_ROW["weight"]}' />
				</div>
			</td>
			<td class='fieldValue' style='text-align:center'>
				<div class='input-prepend input-prepend-centered'>
					<input type='text' class='input-large' style='width:90%' name='vehicleMake-{$ROW_NUM}' value='{$VEHICLE_ROW["make"]}' />
				</div>
			</td>
			<td class='fieldValue' style='text-align:center'>
				<div class='input-prepend input-prepend-centered'>
					<input type='text' class='input-large' style='width:90%' name='vehicleModel-{$ROW_NUM}' value='{$VEHICLE_ROW["model"]}' />
				</div>
			</td>
			<td class='fieldValue' style='text-align:center'>
				<div class='input-prepend input-prepend-centered'>
					<input type='text' class='input-large' style='width:90%' name='vehicleYear-{$ROW_NUM}' value='{$VEHICLE_ROW["year"]}' />
				</div>
			</td>
		</tr>
		{/foreach}
	</tbody>
</table>
<br />
{/if}
{/if}
{if 1==0}<!-- TODO: This section needs the correct values from $VEHICLES -->
<table class='table table-bordered blockContainer showInlineTable misc'>
	<thead>
		<th class='blockHeader' colspan='5'>
			<img class="cursorPointer alignMiddle blockToggle {if !($IS_HIDDEN)} hide {/if} "  src="{vimage_path('arrowRight.png')}" data-mode="hide">
			<img class="cursorPointer alignMiddle blockToggle {if ($IS_HIDDEN)} hide {/if}"  src="{vimage_path('arrowDown.png')}" data-mode="show">
			&nbsp;&nbsp;Vehicles
		</th>
	</thead>
	<tbody id='vehiclesTab'{if $IS_HIDDEN} class="hide" {/if}>
			<tr>
				<td colspan='5' style='padding:0'>
					<button type='button' id='addVehicle'>+</button>
					<button type='button' id='addVehicle2' style='clear:right;float:right;'>+</button><br />
				</td>
			</tr>
		<tr>
			<td style='width:5%'>
				&nbsp;
			</td>
			<td style='width:30%'>
				<span class="redColor">*</span><b>Rating Type</b>
			</td>
			<td style='width:25%'>
				<span class="redColor">*</span><b>Description</b>
			</td>
			<td style='width:25%'>
				<span class="redColor">*</span><b>SIT Days</b>
			</td>
			<td style='width:25%'>
				<span class="redColor">*</span><b>Weight</b>
			</td>
		</tr>
		<tr class='hide vehicleItem vehicleRow newVehicleRow'>
			<td class='fieldValue' style='text-align:center'>
				<a class="deleteVehicleButton">
					<i title="Delete" class="icon-trash alignMiddle"></i>
				</a>
			</td>
			<td class='fieldValue' style='text-align:center'>
				<select name='vehicleRateType'>
					<option value='weight'>
						Actual Weight
					</option>
					<option value='mileage'>
						Mileage Rate
					</option>
					<option value='point'>
						Point-to-Point
					</option>
				</select>
			</td>
			<td class='fieldValue' style='text-align:center'>
				<div class='input-prepend input-prepend-centered'>
					<input type='text' class='input-medium' style='float:left' name='vehicleDescription' value=''/>
				</div>
			</td>
			<td class='fieldValue' style='text-align:center'>
				<div class='input-prepend input-prepend-centered'>
					<input type='text' class='input-medium' style='float:left' name='vehicleSITDays' value=''/>
				</div>
			</td>
			<td class='fieldValue' style='text-align:center'>
				<div class='input-prepend input-prepend-centered'>
					<input type='text' class='input-medium' style='float:left' name='vehicleWeight' value=''/>
				</div>
			</td>
		</tr>

		{foreach item=VEHICLE_ROW key=ROW_NUM from=$VEHICLES}
		<tr class='vehicleItem vehicleRow' id='vehicleRow-{$ROW_NUM}'>
			<td class='fieldValue' style='text-align:center'>
				<a class="deleteMiscChargeButton">
					<i title="Delete" class="icon-trash alignMiddle"></i>
				</a>
			</td>

			<td class='fieldValue' style='text-align:center'>
				<select name='vehicleRateType-{$ROW_NUM}'>
					<option value='weight'>
						Actual Weight
					</option>
					<option value='mileage'>
						Mileage Rate
					</option>
					<option value='point'>
						Point-to-Point
					</option>
				</select>
			</td>

			<td class='fieldValue' style='text-align:center'>
				<input type='text' class='input-large' style='width:90%' name='vehicleDescription-{$ROW_NUM}' value='{$VEHICLE_ROW["description"]}'/>
				<input type='hidden' name='vehicleID-{$ROW_NUM}' value='{$VEHICLE_ROW["vehicle_Id"]}'>
			</td>
			<td class='fieldValue' style='text-align:center'>
				<input type='text' class='input-large' style='width:90%' name='vehicleSITDays-{$ROW_NUM}' value='{$VEHICLE_ROW["sit_days"]}'/>
			</td>
			<td class='fieldValue' style='text-align:center'>
				<div class='input-prepend input-prepend-centered'>
					<input type='text' class='input-large' style='width:90%' name='vehicleWeight-{$ROW_NUM}' value='{$VEHICLE_ROW["weight"]}' />
				</div>
			</td>
		</tr>
		{/foreach}
	</tbody>
</table>
{/if}
		</div>
	<div id='reportContent' class='details'>
	</div>
{/strip}
