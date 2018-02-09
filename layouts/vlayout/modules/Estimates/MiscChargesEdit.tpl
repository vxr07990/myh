{strip}
{assign var=PACKING_COLUMNS value=6}
{assign var=PACK_LABEL_WIDTH value='20%'}
{assign var=PACK_VALUE_WIDTH value='12%'}
{if getenv('INSTANCE_NAME') == 'graebel' && $HAS_CONTAINERS}
	{assign var=PACK_LABEL_WIDTH value='15%'}
	{assign var=PACK_VALUE_WIDTH value='9%'}
	{assign var=PACKING_COLUMNS value=9}
	{assign var=CONTAINER_CELL_CLASS value="containerCell"}
{else}
	{assign var=CONTAINER_CELL_CLASS value="containerCell hide"}
{/if}
{if $HIDE_PACKING_CONTAINERS}
    {assign var=HIDE_PACKING_CONTAINERS value="hide"}
{/if}
{if $HIDE_PACKING_CUSTOM_RATE}
    {assign var=HIDE_PACKING_CUSTOM_RATE value="hide"}
{else}
    {assign var=CUSTOM_RATES_AVAILABLE value=true}
{/if}
{if $HIDE_PACKING_PACK_RATE}
    {assign var=HIDE_PACKING_PACK_RATE value="hide"}
{else}
    {assign var=PACKING_CUSTOM_RATES_AVAILABLE value=true}
{/if}
{assign var=BULKY_LABEL_WIDTH value='15%'}
{assign var=BULKY_VALUE_WIDTH value='10%'}

{assign var=HAS_CONTENT value=(!$BLOCK_SUBLIST || $BLOCK_SUBLIST['INTERSTATE_MISC_CHARGES'])}
<div id="contentHolder_INTERSTATE_MISC_CHARGES" class="sectionContentHolder {$CONTENT_DIV_CLASS} {if !$ALWAYS_SHOW_CONTENT_DIV}hide{/if} {if !$HAS_CONTENT}inactive{/if}">
{if $HAS_CONTENT}
{if getenv('INSTANCE_NAME')=='sirva'}
	{assign var=PACK_LABEL_WIDTH value='15%'}
	{assign var=PACK_CSTRATE_WIDTH value='8%'}
	{assign var=PACK_VALUE_WIDTH value='10%'}
	{assign var=PACKING_WIDTH value=0}

	{if $CUSTOM_PACKING == 1}
		{assign var=PACKING_WIDTH value=100/6}
	{else}
		{assign var=PACKING_WIDTH value=100/4}
	{/if}
	{assign var=TARIFF_TYPE value=$EFFECTIVE_TARIFF_CUSTOMTYPE}
    {*assign var=CUSTOM_RATES_AVAILABLE value={in_array($TARIFF_TYPE, $CUSTOM_TARIFF_TYPE_FOR_USE_CUSTOM_RATES)}*}
    {* NOTE: Just because the thing shouldn't be on doesn't mean it isn't on*}
    {if $CUSTOM_PACKING == 1}
       {assign var=CUSTOM_RATES_AVAILABLE value=true}
    {/if}
	<table class='table table-bordered detailview-table packing' data-tariff-type="{$TARIFF_TYPE}">
				<thead>
					<tr>
						<th class='blockHeader' colspan='6'>
						<img class="cursorPointer alignMiddle blockToggle {if !($IS_HIDDEN)} hide {/if} "  src="{vimage_path('arrowRight.png')}" data-mode="hide" data-id="packing">
						<img class="cursorPointer alignMiddle blockToggle {if ($IS_HIDDEN)} hide {/if}"  src="{vimage_path('arrowDown.png')}" data-mode="show" data-id="packing">
						&nbsp;&nbsp;Packing
					</th>
					</tr>
				</thead>
				<tbody id='packingTab'{if $IS_HIDDEN} class="hide" {/if}>
						<tr class="customRatesCheckboxRow {if !$CUSTOM_RATES_AVAILABLE}hide{/if}">
						<td class="fieldLabel">
							<label class="muted pull-right marginRight10px">Use Custom Rates</label>
						</td>
						<td class="fieldValue" style="text-align:left">
							<input type="hidden" name="apply_custom_pack_rate_override" value="0">
							<input type="checkbox" name="apply_custom_pack_rate_override" value = "1" {if $CUSTOM_PACKING == 1}checked{/if}>
						</td>
						<td colspan="4" class="fieldValue" style="text-align:left">
							<button type='button' name="LoadTariffPacking" {if $CUSTOM_PACKING != 1}class="hide"{/if}>Load Tariff Packing</button>
						</td>
					</tr>
					<tr>
                        <td></td>
                        <td></td>
						<td class="packingCustomRate {if !$CUSTOM_RATES_AVAILABLE}hide{/if}"></td>
						<td>
                            <span class="muted marginRight10px otPackingField">OT Packing</span> &nbsp;
							{assign var=OTPackingField value = $RECORD_STRUCTURE['LBL_QUOTES_ACCESSORIALDETAILS']['accesorial_ot_packing']}
							{include file=vtemplate_path($OTPackingField->getUITypeModel()->getTemplateName(),$MODULE) FIELD_MODEL = $OTPackingField BLOCK_FIELDS=$RECORD_STRUCTURE['LBL_QUOTES_ACCESSORIALDETAILS']}
						</td>
						<td class="packingCustomRate {if !$CUSTOM_RATES_AVAILABLE}hide{/if}"></td>
						<td>
                            <span class="muted marginRight10px otPackingField">OT Unpacking</span> &nbsp;
							{assign var=OTUnPackingField value = $RECORD_STRUCTURE['LBL_QUOTES_ACCESSORIALDETAILS']['accesorial_ot_unpacking']}
							{include file=vtemplate_path($OTUnPackingField->getUITypeModel()->getTemplateName(),$MODULE) FIELD_MODEL = $OTUnPackingField BLOCK_FIELDS=$RECORD_STRUCTURE['LBL_QUOTES_ACCESSORIALDETAILS']}
						</td>
					</tr>
					<tr>
						<td class="fieldLabel"></td>
						<td class="fieldValue ContCol">{vtranslate('Containers', $MODULE)}</td>
						<td class="fieldValue {if !$CUSTOM_RATES_AVAILABLE}hide{/if} packingCustomRate">{vtranslate('Custom Rate', $MODULE)}</td>
						<td class="fieldValue PkCol">{vtranslate('Packing', $MODULE)}</td>
						<td class="fieldValue {if !$PACKING_CUSTOM_RATES_AVAILABLE}hide{/if} packingPackRate">{vtranslate('Pack Rate', $MODULE)}</td>
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
								<td style='text-align:center; width: {$PACKING_WIDTH}%;' class='fieldValue {if !$CUSTOM_RATES_AVAILABLE}hide{/if} {$WIDTHTYPE} packingCustomRate'>
							<div class="row-fluid">
								<div class="input-append" style="margin: auto;">
									<input name="packCustomRate{$ITEM_NUM}" style="text-align:center;"
										   class="input-medium currencyField" type="text" value="{number_format($PACKING_ITEM.customRate, 2, '.', '')}"
										   data-decimal-seperator="." data-group-seperator="," data-number-of-decimal-places="2"/>
									<span class="add-on">&#36;</span>
								</div>
							</div>
						</td>
							<td style='text-align:center; width: {$PACKING_WIDTH}%;' class='fieldValue {$WIDTHTYPE} PkCol'>
							<input type='number' min='0' max class='input-medium packQtyField' name='pack{$ITEM_NUM}'
								   value='{($PACKING_ITEM.pack)?$PACKING_ITEM.pack:0}' />
						</td>
								<td style='text-align:center; width: {$PACKING_WIDTH}%;' class='fieldValue {if !$PACKING_CUSTOM_RATES_AVAILABLE}hide{/if} {$WIDTHTYPE} packingPackRate'>
							<div class="row-fluid">
								<div class="input-append" style="margin: auto;">
									<input name="packPackRate{$ITEM_NUM}" style="text-align:center;"
										   class="input-medium currencyField" type="text" value="{number_format($PACKING_ITEM.packRate, 2, '.', '')}"
										   data-decimal-seperator="." data-group-seperator="," data-number-of-decimal-places="2"/>
									<span class="add-on">&#36;</span>
								</div>
							</div>
						</td>
							<td style='text-align:center; width: {$PACKING_WIDTH}%;' class='fieldValue {$WIDTHTYPE} UnpkCol'>
							<input type='number' min='0' max class='input-medium unpackQtyField' name='unpack{$ITEM_NUM}'
								   value='{($PACKING_ITEM.unpack)?$PACKING_ITEM.unpack:0}' />
						</td>
					</tr>
					{/foreach}
				</tbody>
			</table>
	<br />
{else}

	<table id='pack_table' class='table table-bordered detailview-table packing'>
	<thead>
		<th class='blockHeader' colspan='{$PACKING_COLUMNS}'>
			<img class="cursorPointer alignMiddle blockToggle {if !($IS_HIDDEN)} hide {/if} "  src="{vimage_path('arrowRight.png')}" data-mode="hide" data-id="packing">
			<img class="cursorPointer alignMiddle blockToggle {if ($IS_HIDDEN)} hide {/if}"  src="{vimage_path('arrowDown.png')}" data-mode="show" data-id="packing">
			&nbsp;&nbsp;Packing
		</th>
	</thead>
	<tbody id='packingTab'{if $IS_HIDDEN} class="hide" {/if}>
		<tr>
			<td style='width:{$PACK_LABEL_WIDTH}' class="fieldLabel"></td>
			{if getenv('INSTANCE_NAME') == 'graebel'}<td class="fieldValue {$CONTAINER_CELL_CLASS}" style="width:{$PACK_VALUE_WIDTH}">Containers</td>{/if}
			<td style='width:{$PACK_VALUE_WIDTH}' class="fieldValue">Pk</td>
			<td style='width:{$PACK_LABEL_WIDTH}' class="fieldLabel"></td>
			{if getenv('INSTANCE_NAME') == 'graebel'}<td class="fieldValue {$CONTAINER_CELL_CLASS}" style="width:{$PACK_VALUE_WIDTH}">Containers</td>{/if}
			<td style='width:{$PACK_VALUE_WIDTH}' class="fieldValue">Pk</td>
			<td style='width:{$PACK_LABEL_WIDTH}' class="fieldLabel"></td>
			{if getenv('INSTANCE_NAME') == 'graebel'}<td class="fieldValue {$CONTAINER_CELL_CLASS}" style="width:{$PACK_VALUE_WIDTH}">Containers</td>{/if}
			<td style='width:{$PACK_VALUE_WIDTH}' class="fieldValue">Pk</td>
		</tr>
		{assign var=COUNTER value=0}
		<tr>
		{foreach item=PACKING_ITEM key=ITEM_NUM from=$PACKING_ITEMS}
			{if $COUNTER eq 3}
		</tr>
		<tr>
		{assign var=COUNTER value=1}
			{else}
			{assign var=COUNTER value=$COUNTER+1}
			{/if}
			<td style='width:{$PACK_LABEL_WIDTH}; padding:0 5px 0 0;' class='fieldLabel {$WIDTHTYPE}'>
				<label class="muted pull-right marginRight10px">
					{$PACKING_ITEM.label}
				</label>
			</td>
			{if getenv('INSTANCE_NAME') == 'graebel'}
				<td style='width:{$PACK_VALUE_WIDTH}; text-align:center;' class='fieldValue {$WIDTHTYPE} {$CONTAINER_CELL_CLASS}'>
				<input id='{$MODULE_NAME}_editView_fieldName_containers{$ITEM_NUM}' type='number' min='0' max class='input-large packContainersField' name='containers_pack{$ITEM_NUM}' style="width:80%;" value='{$PACKING_ITEM.containers}' />
			</td>
			{/if}
			<td style='width:{$PACK_VALUE_WIDTH}; text-align:center;' class='fieldValue {$WIDTHTYPE}'>
				<input data-validator='[{ldelim}"name":"WholeNumber"{rdelim}]' data-validation-engine="validate[funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" id='{$MODULE_NAME}_editView_fieldName_pack{$ITEM_NUM}' type='number' min='0' max class='input-large packQtyField' name='pack{$ITEM_NUM}' style='width:80%;' value='{$PACKING_ITEM.pack}' />
			</td>
			{/foreach}
			{while $COUNTER lt 3}
				<td style='width:{$PACK_LABEL_WIDTH}; padding:0 5px 0 0;' class='fieldLabel {$WIDTHTYPE}'>
				&nbsp;
			</td>
				<td style='width:{$PACK_VALUE_WIDTH};' class='{$WIDTHTYPE}'>
				&nbsp;
			</td>
			{if getenv('INSTANCE_NAME') == 'graebel'}
				<td style='width:{$PACK_VALUE_WIDTH};' class='{$WIDTHTYPE} {$CONTAINER_CELL_CLASS}'>
				&nbsp;
			</td>
			{/if}
				{assign var=COUNTER value=$COUNTER+1}
			{/while}
		</tr>
	</tbody>
	</table>
	<br/>
{/if}
{if getenv('INSTANCE_NAME') != 'uvlc' && getenv('INSTANCE_NAME') != 'sirva'}
	<table id='unpack_table' class='table table-bordered detailview-table packing'>
	<thead>
		<th class='blockHeader' colspan='9'>
			<img class="cursorPointer alignMiddle blockToggle {if !($IS_HIDDEN)} hide {/if} "  src="{vimage_path('arrowRight.png')}" data-mode="hide" data-id="unpacking">
			<img class="cursorPointer alignMiddle blockToggle {if ($IS_HIDDEN)} hide {/if}"  src="{vimage_path('arrowDown.png')}" data-mode="show" data-id="unpacking">
			&nbsp;&nbsp;Unpacking
		</th>
	</thead>
	<tbody id='otPackingTab'{if $IS_HIDDEN} class="hide" {/if}>
		<tr><td style='width:{$PACK_LABEL_WIDTH}' class="fieldLabel"></td>
			<td style='width:{$PACK_VALUE_WIDTH}' class="fieldValue">Unpk</td>
			<td style='width:{$PACK_LABEL_WIDTH}' class="fieldLabel"></td>
			<td style='width:{$PACK_VALUE_WIDTH}' class="fieldValue">Unpk</td>
			<td style='width:{$PACK_LABEL_WIDTH}' class="fieldLabel"></td>
			<td style='width:{$PACK_VALUE_WIDTH}' class="fieldValue">Unpk</td>
			</tr>
		{assign var=COUNTER value=0}
		<tr>
		{foreach item=PACKING_ITEM key=ITEM_NUM from=$PACKING_ITEMS}
			{if $COUNTER eq 3}
		</tr>
		<tr>
		{assign var=COUNTER value=1}
			{else}
			{assign var=COUNTER value=$COUNTER+1}
			{/if}
			<td style='width:{$PACK_LABEL_WIDTH}; padding:0 5px 0 0;' class='fieldLabel {$WIDTHTYPE}'>
				<label class="muted pull-right marginRight10px">
					{$PACKING_ITEM.label}
				</label>
			</td>
			<td style='width:{$PACK_VALUE_WIDTH}; text-align:center;' class='fieldValue {$WIDTHTYPE}'>
				<input data-validator='[{ldelim}"name":"WholeNumber"{rdelim}]' data-validation-engine="validate[funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" id='{$MODULE_NAME}_editView_fieldName_unpack{$ITEM_NUM}' type='number' min='0' max class='input-large packQtyField' name='unpack{$ITEM_NUM}' style='width:80%;' value='{$PACKING_ITEM.unpack}' />
			</td>
			{/foreach}
			{while $COUNTER lt 3}
				<td style='width:{$PACK_LABEL_WIDTH}; padding:0 5px 0 0;' class='fieldLabel {$WIDTHTYPE}'>
				&nbsp;
			</td>
				<td style='width:{$PACK_VALUE_WIDTH};' class='{$WIDTHTYPE}'>
				&nbsp;
			</td>
				{assign var=COUNTER value=$COUNTER+1}
			{/while}
		</tr>
	</tbody>
</table>
<br/>
{/if}
<table id='bulky_table' class='table table-bordered detailview-table bulky'>
	<thead>
		{assign var=BULKY_TABLE_LABEL value = 'BULKY_TABLE_LABEL'}
		<th class='blockHeader' colspan='8'>
			<img class="cursorPointer alignMiddle blockToggle {if !($IS_HIDDEN)} hide {/if} "  src="{vimage_path('arrowRight.png')}" data-mode="hide" data-id="bulkies">
			<img class="cursorPointer alignMiddle blockToggle {if ($IS_HIDDEN)} hide {/if}"  src="{vimage_path('arrowDown.png')}" data-mode="show" data-id="bulkies">
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
				<input data-validator='[{ldelim}"name":"WholeNumber"{rdelim}]' data-validation-engine="validate[funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" id='{$MODULE_NAME}_editView_fieldName_bulky{$ITEM_NUM}' type='number' min='0' class='input-large' name='bulky{$ITEM_NUM}' style='width:80%;' value='{$BULKY_ITEM.qty}' />
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
<br/>

<input type='hidden' id='interstateNumCrates' name='interstateNumCrates' value='{$CRATES|@count}'>
{if getenv('INSTANCE_NAME') == 'sirva'}
	<table class='table table-bordered blockContainer showInlineTable misc'>
				<thead>
					<th class='blockHeader' colspan='10'>
						<img class="cursorPointer alignMiddle blockToggle {if !($IS_HIDDEN)} hide {/if} "  src="{vimage_path('arrowRight.png')}" data-mode="hide" data-id="crates">
						<img class="cursorPointer alignMiddle blockToggle {if ($IS_HIDDEN)} hide {/if}"  src="{vimage_path('arrowDown.png')}" data-mode="show" data-id="crates">
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
							<input type='number' class='input-large' style='width:80%' step="1" min="0" name='cratePack' />
							<input type='hidden' class='fieldname' value='cratePack' data-prev-value='no' />
						</td>
						<td class='fieldValue'>
							<input type='number' class='input-large' style='width:80%' step="1" min="0" name='crateUnpack' />
							<input type='hidden' class='fieldname' value='crateUnpack' data-prev-value='no' />
						</td>
						<td class='fieldValue'>
							<input type='number' class='input-large' style='width:80%' step="1" min="0" name='crateOTPack' />
							<input type='hidden' class='fieldname' value='crateOTPack' data-prev-value='no' />
						</td>
						<td class='fieldValue'>
							<input type='number' class='input-large' style='width:80%' step="1" min="0" name='crateOTUnpack' />
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
						<td class='fieldValue' style='text-align:center;'>
							<input type='number' class='input-large' style='width:80%' step="1" min="0" name='cratePack{$ROW_NUM}' value="{$CRATE_ROW->pack}" />
						</td>
						<td class='fieldValue' style='text-align:center;'>
							<input type='number' class='input-large' style='width:80%' step="1" min="0" name='crateUnpack{$ROW_NUM}' value="{$CRATE_ROW->unpack}" />
						</td>
						<td class='fieldValue' style='text-align:center;'>
							<input type='number' class='input-large' style='width:80% 'step="1" min="0" name='crateOTPack{$ROW_NUM}' value="{$CRATE_ROW->otpack}" />
						</td>
						<td class='fieldValue' style='text-align:center;'>
							<input type='number' class='input-large' style='width:80%' step="1" min="0" name='crateOTUnpack{$ROW_NUM}' value="{$CRATE_ROW->otunpack}" />
						</td>
						<input type='hidden' class='lineItemId' value='{$CRATE_ROW->lineItemId}' name='crateLineItemId{$ROW_NUM}'/>
					</tr>
					{/foreach}
				</tbody>
			</table>
	<br />
{else}
{assign var=CRATE_COLUMNS value=10}
{if getenv('IGC_MOVEHQ')}
	{assign var=CRATE_COLUMNS value=13}
{/if}
<table id='crating_table' class='table table-bordered blockContainer showInlineTable misc'>
	<thead>
		<th class='blockHeader' colspan='{$CRATE_COLUMNS}'>
			<img class="cursorPointer alignMiddle blockToggle {if !($IS_HIDDEN)} hide {/if} "  src="{vimage_path('arrowRight.png')}" data-mode="hide" data-id="crates">
			<img class="cursorPointer alignMiddle blockToggle {if ($IS_HIDDEN)} hide {/if}"  src="{vimage_path('arrowDown.png')}" data-mode="show" data-id="crates">
			&nbsp;&nbsp;Crate Details
		</th>
	</thead>
	<tbody id='cratesTab'{if $IS_HIDDEN} class="hide" {/if}>
		{if !$LOCK_RATING}
			<tr>
				<td colspan='{$CRATE_COLUMNS}' style='padding:0'>
					<button type='button' id='addCrate'>+</button>
					<button type='button' id='addCrate2' style='clear:right; float:right;'>+</button>
				</td>
			</tr>
		{/if}
		<tr>
			<td style='text-align:center; background-color:#E8E8E8;' colspan='3'>
				&nbsp;
			</td>
			<td style='text-align:center; background-color:#E8E8E8;' colspan='3'>
				<b>Dimensions (in)</b>
			</td>
			<td style='text-align:center; background-color:#E8E8E8;' colspan='1'>
			</td>
			<td style='text-align:center; background-color:#E8E8E8;' colspan='2'>
				<b>Pack</b>
			</td>
			<td style='text-align:center; background-color:#E8E8E8;' colspan='{$CRATE_COLUMNS - 9}'>
				&nbsp;
			</td>
		</tr>
		<tr>
			{assign var=INTERSTATE_CRATE_TD_WIDTH value=100/$CRATE_COLUMNS}
			<td style='width:{$INTERSTATE_CRATE_TD_WIDTH-3}%'>
				&nbsp;
			</td>
			<td style='width:{$INTERSTATE_CRATE_TD_WIDTH-3}%'>
				<b>ID</b>
			</td>
			<td style='width:{$INTERSTATE_CRATE_TD_WIDTH+5}%'>
				<span class="redColor">*</span><b>Description</b>
			</td>
			<td style='width:{$INTERSTATE_CRATE_TD_WIDTH}%'>
				<span class="redColor">*</span><b>L</b>
			</td>
			<td style='width:{$INTERSTATE_CRATE_TD_WIDTH}%'>
				<span class="redColor">*</span><b>W</b>
			</td>
			<td style='width:{$INTERSTATE_CRATE_TD_WIDTH}%'>
				<span class="redColor">*</span><b>H</b>
			</td>
			<td style='width:{$INTERSTATE_CRATE_TD_WIDTH}%'>
				<b>Cube</b>
			</td>
			<td style='width:{$INTERSTATE_CRATE_TD_WIDTH}%'>
				<b>Pack</b>
			</td>
			<td style='width:{$INTERSTATE_CRATE_TD_WIDTH}%'>
				<b>Unpack</b>
			</td>
			{if getenv('IGC_MOVEHQ')}
				<td style="width:{$INTERSTATE_CRATE_TD_WIDTH}%">
				<b>Apply Tariff</b>
			</td>
			<td style="width:{$INTERSTATE_CRATE_TD_WIDTH}%">
				<b>Custom Rate Pack</b>
			</td>
				<td style="width:{$INTERSTATE_CRATE_TD_WIDTH}%">
				<b>Custom Rate Unpack</b>
			</td>
			{/if}
			<td style='width:{$INTERSTATE_CRATE_TD_WIDTH}%'>
				<b>Disc</b>
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
				<input type='text' class='input-large' name='crateLength' style='width:80%' value data-validator='[{ldelim}"name":"WholeNumber"{rdelim}]' data-validation-engine="validate[funcCall[Vtiger_Base_Validator_Js.invokeValidation]]"/>
				<input type='hidden' class='fieldname' value='crateLength' data-prev-value />
			</td>
			<td class='fieldValue' style='text-align:center;'>
				<input type='text' class='input-large' name='crateWidth' style='width:80%' value data-validator='[{ldelim}"name":"WholeNumber"{rdelim}]' data-validation-engine="validate[funcCall[Vtiger_Base_Validator_Js.invokeValidation]]"/>
				<input type='hidden' class='fieldname' value='crateWidth' data-prev-value />
			</td>
			<td class='fieldValue' style='text-align:center;'>
				<input type='text' class='input-large' name='crateHeight' style='width:80%' value data-validator='[{ldelim}"name":"WholeNumber"{rdelim}]' data-validation-engine="validate[funcCall[Vtiger_Base_Validator_Js.invokeValidation]]"/>
				<input type='hidden' class='fieldname' value='crateHeight' data-prev-value />
			</td>
			<td class='fieldValue' style='text-align:center;'>
				<input type='text' class='input-large' name='cube' disabled style='width:80%' value data-validator='[{ldelim}"name":"WholeNumber"{rdelim}]' data-validation-engine="validate[funcCall[Vtiger_Base_Validator_Js.invokeValidation]]"/>
				<input type='hidden' class='fieldname' value='cube' data-prev-value />
			</td>
			<td class='fieldValue'>
                <input type='number' class='input-large' style='width:80%' step="1" min="0" name='cratePack' data-validator='[{ldelim}"name":"WholeNumber"{rdelim}]' data-validation-engine="validate[funcCall[Vtiger_Base_Validator_Js.invokeValidation]]"/>
                <input type='hidden' class='fieldname' value='cratePack' data-prev-value='no' />
			</td>
			<td class='fieldValue'>
                <input type='number' class='input-large' style='width:80%' step="1" min="0" name='crateUnpack' data-validator='[{ldelim}"name":"WholeNumber"{rdelim}]' data-validation-engine="validate[funcCall[Vtiger_Base_Validator_Js.invokeValidation]]"/>
                <input type='hidden' class='fieldname' value='crateUnpack' data-prev-value='no' />
			</td>
			{if getenv('IGC_MOVEHQ')}
				<td class='fieldValue'>
					<input type='checkbox' name='crateApplyTariff' />
				</td>
				<td class='fieldValue' style='text-align:center;'>
					<input type='text' class='input-large' name='crateCustomRateAmount' style='width:80%' value data-validator='[{ldelim}"name":"WholeNumber"{rdelim}]' data-validation-engine="validate[funcCall[Vtiger_Base_Validator_Js.invokeValidation]]"/>
				</td>
				<td class='fieldValue' style='text-align:center;'>
					<input type='text' class='input-large' name='crateCustomRateAmountUnpack' style='width:80%' value data-validator='[{ldelim}"name":"WholeNumber"{rdelim}]' data-validation-engine="validate[funcCall[Vtiger_Base_Validator_Js.invokeValidation]]"/>
				</td>
			{/if}
			<td>
				<input type='hidden' name='crateDiscounted' value="0" />
				<input type='checkbox' name='crateDiscounted' value="1" />
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
				<input type='text' class='input-large' name='crateLength{$ROW_NUM}' style='width:80%' value='{$CRATE_ROW->crateLength}' data-validator='[{ldelim}"name":"WholeNumber"{rdelim}]' data-validation-engine="validate[funcCall[Vtiger_Base_Validator_Js.invokeValidation]]"/>
			</td>
			<td class='fieldValue' style='text-align:center;'>
				<input type='text' class='input-large' name='crateWidth{$ROW_NUM}' style='width:80%' value='{$CRATE_ROW->crateWidth}' data-validator='[{ldelim}"name":"WholeNumber"{rdelim}]' data-validation-engine="validate[funcCall[Vtiger_Base_Validator_Js.invokeValidation]]"/>
			</td>
			<td class='fieldValue' style='text-align:center;'>
				<input type='text' class='input-large' name='crateHeight{$ROW_NUM}' style='width:80%' value='{$CRATE_ROW->crateHeight}' data-validator='[{ldelim}"name":"WholeNumber"{rdelim}]' data-validation-engine="validate[funcCall[Vtiger_Base_Validator_Js.invokeValidation]]"/>
			</td>
			<td class='fieldValue' style='text-align:center;'>
				<input type='text' class='input-large' name='cube{$ROW_NUM}' disabled style='width:80%' value='{$CRATE_ROW->cube}' data-validator='[{ldelim}"name":"WholeNumber"{rdelim}]' data-validation-engine="validate[funcCall[Vtiger_Base_Validator_Js.invokeValidation]]"/>
			</td>
			<td class='fieldValue'>
                <input type='number' class='input-large' style='width:80%' step="1" min="0" name='cratePack{$ROW_NUM}' value="{$CRATE_ROW->pack}" data-validator='[{ldelim}"name":"WholeNumber"{rdelim}]' data-validation-engine="validate[funcCall[Vtiger_Base_Validator_Js.invokeValidation]]"/>
			</td>
			<td class='fieldValue'>
                <input type='number' class='input-large' style='width:80%' step="1" min="0" name='crateUnpack{$ROW_NUM}' value="{$CRATE_ROW->unpack}" data-validator='[{ldelim}"name":"WholeNumber"{rdelim}]' data-validation-engine="validate[funcCall[Vtiger_Base_Validator_Js.invokeValidation]]"/>
			</td>
			{if getenv('IGC_MOVEHQ')}
				<td class='fieldValue'>
					<input type='checkbox' name='crateApplyTariff{$ROW_NUM}'{if $CRATE_ROW->apply_tariff eq '1'} checked{/if} />
				</td>
				<td class='fieldValue' style='text-align:center;'>
					<input type='text' class='input-large' name='crateCustomRateAmount{$ROW_NUM}' style='width:80%' value='{$CRATE_ROW->custom_rate_amount}' data-validator='[{ldelim}"name":"WholeNumber"{rdelim}]' data-validation-engine="validate[funcCall[Vtiger_Base_Validator_Js.invokeValidation]]"/>
				</td>
				<td class='fieldValue' style='text-align:center;'>
					<input type='text' class='input-large' name='crateCustomRateAmountUnpack{$ROW_NUM}' style='width:80%' value='{$CRATE_ROW->custom_rate_amount_unpack}' data-validator='[{ldelim}"name":"WholeNumber"{rdelim}]' data-validation-engine="validate[funcCall[Vtiger_Base_Validator_Js.invokeValidation]]"/>
				</td>
			{/if}
			<td>
				<input type='hidden' name='crateDiscounted{$ROW_NUM}' value='0' />
				<input type='checkbox' name='crateDiscounted{$ROW_NUM}'{if $CRATE_ROW->discount eq '1'} checked{/if} value="1" />
			</td>
			<input type='hidden' class='lineItemId' value='{$CRATE_ROW->lineItemId}' name='crateLineItemId{$ROW_NUM}'/>
		</tr>
		{/foreach}
	</tbody>
</table>
<br/>
{/if}

{if getenv('INSTANCE_NAME') != 'graebel' && getenv('INSTANCE_NAME') != 'sirva'}
<table id='standard_vehicles_table' class='table table-bordered blockContainer showInlineTable misc'>
	<thead>
		<th class='blockHeader' colspan='5'>
			<img class="cursorPointer alignMiddle blockToggle {if !($IS_HIDDEN)} hide {/if} "  src="{vimage_path('arrowRight.png')}" data-mode="hide" data-id="vehicles">
			<img class="cursorPointer alignMiddle blockToggle {if ($IS_HIDDEN)} hide {/if}"  src="{vimage_path('arrowDown.png')}" data-mode="show" data-id="vehicles">
			&nbsp;&nbsp;Vehicles
		</th>
	</thead>
	<tbody id='vehiclesTab'{if $IS_HIDDEN} class="hide" {/if}>
		{if !$LOCK_RATING}
			<tr>
				<td colspan='5' style='padding:0'>
					<button type='button' id='addVehicle'>+</button>
					<button type='button' id='addVehicle2' style='clear:right;float:right;'>+</button><br />
				</td>
			</tr>
		{/if}
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
				<select name='vehicleRateType' class='chzn-select chzn-done'>
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
					<input type='text' class='input-medium' style='float:left' name='vehicleSITDays' value='' data-validator='[{ldelim}"name":"WholeNumber"{rdelim}]' data-validation-engine="validate[funcCall[Vtiger_Base_Validator_Js.invokeValidation]]"/>
				</div>
			</td>
			<td class='fieldValue' style='text-align:center'>
				<div class='input-prepend input-prepend-centered'>
					<input type='text' class='input-medium' style='float:left' name='vehicleWeight' value='' data-validator='[{ldelim}"name":"WholeNumber"{rdelim}]' data-validation-engine="validate[funcCall[Vtiger_Base_Validator_Js.invokeValidation]]"/>
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
				<input type='hidden' name='vehicleID-{$ROW_NUM}' value='{$VEHICLE_ROW["vehicle_id"]}'>
			</td>
			<td class='fieldValue' style='text-align:center'>
				<input type='text' class='input-large' style='width:90%' name='vehicleSITDays-{$ROW_NUM}' value='{$VEHICLE_ROW["sit_days"]}' data-validator='[{ldelim}"name":"WholeNumber"{rdelim}]' data-validation-engine="validate[funcCall[Vtiger_Base_Validator_Js.invokeValidation]]"/>
			</td>
			<td class='fieldValue' style='text-align:center'>
				<div class='input-prepend input-prepend-centered'>
					<input type='text' class='input-large' style='width:90%' name='vehicleWeight-{$ROW_NUM}' value='{$VEHICLE_ROW["weight"]}' data-validator='[{ldelim}"name":"WholeNumber"{rdelim}]' data-validation-engine="validate[funcCall[Vtiger_Base_Validator_Js.invokeValidation]]"/>
				</div>
			</td>
		</tr>
		{/foreach}
	</tbody>
</table>
<br/>
{/if}
{/if}
</div>
{/strip}
