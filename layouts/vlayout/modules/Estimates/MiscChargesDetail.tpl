{strip}
{assign var=IS_HIDDEN value='1'}
{assign var=PACKING_COLUMNS value=6}
{assign var=PACKING_LABEL_WIDTH value=20}
{assign var=PACKING_VALUE_WIDTH value=12}
{if getenv('INSTANCE_NAME') == 'graebel' && $HAS_CONTAINERS}
	{assign var=PACKING_LABEL_WIDTH value=15}
	{assign var=PACKING_VALUE_WIDTH value=9}
	{assign var=PACKING_COLUMNS value=9}
	{assign var=CONTAINER_CELL_CLASS value="containerCell"}
{else}
	{assign var=CONTAINER_CELL_CLASS value="hide"}
{/if}
{if $HIDE_PACKING_CONTAINERS}
    {assign var=HIDE_PACKING_CONTAINERS value="hide"}
{/if}
{if $HIDE_PACKING_CUSTOM_RATE}
    {assign var=HIDE_PACKING_CUSTOM_RATE value="hide"}
{/if}
{if $HIDE_PACKING_PACK_RATE}
    {assign var=HIDE_PACKING_PACK_RATE value="hide"}
{/if}

{assign var=HAS_CONTENT value=(!$BLOCK_SUBLIST || $BLOCK_SUBLIST['INTERSTATE_MISC_CHARGES'])}
<div id="contentHolder_INTERSTATE_MISC_CHARGES" class="sectionContentHolder {$CONTENT_DIV_CLASS} {if !$ALWAYS_SHOW_CONTENT_DIV}hide{/if} {if !$HAS_CONTENT}inactive{/if}">
{if $HAS_CONTENT}
{if getenv('INSTANCE_NAME') == 'sirva'}
    {* NOTE: Sirva packing seems pointlessly different *}
	{assign var=PACK_LABEL_WIDTH value='15%'}
	{assign var=PACK_CSTRATE_WIDTH value='8%'}
	{assign var=PACK_VALUE_WIDTH value='10%'}
	{assign var=IS_HIDDEN value='1'}
	{assign var=TARIFF_TYPE value=$EFFECTIVE_TARIFF_CUSTOMTYPE}
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
			{if in_array($TARIFF_TYPE, $CUSTOM_TARIFF_TYPE_FOR_USE_CUSTOM_RATES)}
				<tr>
				<td style='width:{$PACK_LABEL_WIDTH}' class="fieldLabel">
					<label class="muted pull-right marginRight10px">Use Custom Rates</label>
				</td>
				<td class="fieldValue customPacking" style="text-align:left">
					<span class="value" data-field-type="boolean">{if $CUSTOM_PACKING == 1}Yes{else}No{/if}</span>
					<span class="hide">
						<input type="hidden" name="apply_custom_pack_rate_override" value="{($CUSTOM_RATES.apply_custom_pack_rate_override)?$CUSTOM_RATES.apply_custom_pack_rate_override:0}">
						<input type="checkbox" name="apply_custom_pack_rate_override" {if $CUSTOM_PACKING == 1}checked{/if}>
						<input type="hidden" class="fieldname" value="apply_custom_pack_rate_override" data-prev-value="{($CUSTOM_RATES.apply_custom_pack_rate_override)?Yes:No}">
					</span>
				</td>
					{*
				<td colspan="2" class="fieldValue packingCustomRate" style="text-align:left">
					<button type='button' name="LoadTariffPacking" class="hide">Load Tariff Packing</button>
				</td>
				*}
				<td style='width:{$PACK_LABEL_WIDTH}' class="fieldLabel">
					<label class="muted pull-right marginRight10px"> OT Packing</label>
				</td>
				<td style='width:{$PACK_LABEL_WIDTH}' class="fieldValue">
					{$SIRVA_OT_PACKING}
				</td>
				<td style='width:{$PACK_LABEL_WIDTH}' class="fieldLabel">
					<label class="muted pull-right marginRight10px">&nbsp;OT Unpacking</label>
				</td>
				<td style='width:{$PACK_LABEL_WIDTH}' class="fieldValue">
					{$SIRVA_OT_PACKING}
				</td>
			</tr>
			{/if}
			<tr>
				<td style='width:{$PACK_LABEL_WIDTH}' class="fieldLabel"></td>
				<td style='width:{$PACK_VALUE_WIDTH};text-align:center;' class="fieldValue {$HIDE_PACKING_CONTAINERS} ContCol">Containers</td>
				<td style='width:{$PACK_CSTRATE_WIDTH};text-align:center;' class="fieldValue {$HIDE_PACKING_CUSTOM_RATE} packingCustomRate">Custom Rate</td>
				<td style='width:{$PACK_VALUE_WIDTH};text-align:center;' class="fieldValue PkCol">Packing</td>
				<td style='width:{$PACK_CSTRATE_WIDTH};text-align:center;' class="fieldValue {$HIDE_PACKING_PACK_RATE} packingPackRate">Pack Rate</td>
				<td style='width:{$PACK_VALUE_WIDTH};text-align:center;' class="fieldValue UnpkCol">Unpacking</td>
			</tr>
			{assign var=COUNTER value=0}
			{foreach item=PACKING_ITEM key=ITEM_NUM from=$PACKING_ITEMS}
				<tr>
				{assign var=COUNTER value=$COUNTER+1}
					<td style='padding:0 5px 0 0;width:{$PACK_LABEL_WIDTH};' class='fieldLabel {$WIDTHTYPE}'>
					<label class="muted pull-right marginRight10px">
						{$PACKING_ITEM.label}
					</label>
				</td>
				<td style="text-align:center;width:{$PACK_VALUE_WIDTH};" class="fieldValue {$WIDTHTYPE} {$HIDE_PACKING_CONTAINERS} ContCol">
					<span class="value" data-field-type="string">{($PACKING_ITEM.cont)?$PACKING_ITEM.cont:0}</span>
				</td>
				<td style='width:{$PACK_CSTRATE_WIDTH}; text-align:center;' class='fieldValue {$WIDTHTYPE} {$HIDE_PACKING_CUSTOM_RATE} packingCustomRate'>
					<span class="value" data-field-type="number">
						{number_format($PACKING_ITEM.customRate, 2, '.', '')}
					</span>
				</td>
				<td style='text-align:center;width:{$PACK_VALUE_WIDTH};' class='fieldValue {$WIDTHTYPE} PkCol'>
					<span class='value' data-field-type='string'>{$PACKING_ITEM.pack}</span>
				</td>
				<td style='width:{$PACK_CSTRATE_WIDTH}; text-align:center;' class='fieldValue {$WIDTHTYPE} {$HIDE_PACKING_PACK_RATE} packingPackRate'>
					<span class="value" data-field-type="number">
						{number_format($PACKING_ITEM.packRate, 2, '.', '')}
					</span>
				</td>
				<td style='text-align:center;width:{$PACK_VALUE_WIDTH};' class='fieldValue {$WIDTHTYPE} UnpkCol'>
					<span class='value' data-field-type='string'>{$PACKING_ITEM.unpack}</span>
				</td>
			</tr>
			{/foreach}
		</tbody>
	</table>
	<br />

{else}
	<table id='pack_table' class='table table-bordered equalSplit detailview-table packing'>
	<thead>
		<th class='blockHeader' colspan='{$PACKING_COLUMNS}'>
			<img class="cursorPointer alignMiddle blockToggle {if !($IS_HIDDEN)} hide {/if} "  src="{vimage_path('arrowRight.png')}" data-mode="hide" data-id="packing">
			<img class="cursorPointer alignMiddle blockToggle {if ($IS_HIDDEN)} hide {/if}"  src="{vimage_path('arrowDown.png')}" data-mode="show" data-id="packing">
			&nbsp;&nbsp;Packing
		</th>
	</thead>
	<tbody id='packingTab'{if $IS_HIDDEN} class="hide" {/if}>
		<tr>
			<td class="fieldLabel" style="width:{$PACKING_LABEL_WIDTH}%"></td>
			{if getenv('INSTANCE_NAME') == 'graebel'}<td class="fieldValue {$CONTAINER_CELL_CLASS}" style="width:{$PACKING_VALUE_WIDTH}%">Containers</td>{/if}
			<td class="fieldValue" style="width:{$PACKING_VALUE_WIDTH}%">Pk</td>
			<td class="fieldLabel" style="width:{$PACKING_LABEL_WIDTH}%"></td>
			{if getenv('INSTANCE_NAME') == 'graebel'}<td class="fieldValue {$CONTAINER_CELL_CLASS}" style="width:{$PACKING_VALUE_WIDTH}%">Containers</td>{/if}
			<td class="fieldValue" style="width:{$PACKING_VALUE_WIDTH}%">Pk</td>
			<td class="fieldLabel" style="width:{$PACKING_LABEL_WIDTH}%"></td>
			{if getenv('INSTANCE_NAME') == 'graebel'}<td class="fieldValue {$CONTAINER_CELL_CLASS}" style="width:{$PACKING_VALUE_WIDTH}%">Containers</td>{/if}
			<td class="fieldValue" style="width:{$PACKING_VALUE_WIDTH}%">Pk</td>
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
			<td style='padding:0 5px 0 0;width:{$PACKING_LABEL_WIDTH}%;' class='fieldLabel {$WIDTHTYPE}'>
				<label class="muted pull-right marginRight10px">
					{$PACKING_ITEM.label}
				</label>
			</td>
			{if getenv('INSTANCE_NAME') == 'graebel'}
				<td style='text-align:center;width:{$PACKING_VALUE_WIDTH}%;' class='fieldValue {$WIDTHTYPE} {$CONTAINER_CELL_CLASS}'>
				<span class='value' data-field-type='string'>{$PACKING_ITEM.containers}</span>
			</td>
			{/if}
			<td style='text-align:center;width:{$PACKING_VALUE_WIDTH}%;' class='fieldValue {$WIDTHTYPE}'>
				<span class='value' data-field-type='string'>{$PACKING_ITEM.pack}</span>
			</td>
			{/foreach}
			{while $COUNTER lt 3}
				<td style='padding:0 5px 0 0;width:{$PACKING_LABEL_WIDTH}%;' class='fieldLabel {$WIDTHTYPE}'>
				&nbsp;
			</td>
				<td class='{$WIDTHTYPE}' style="width:{$PACKING_VALUE_WIDTH}%">
				&nbsp;
			</td>
			{if getenv('INSTANCE_NAME') == 'graebel'}
				<td class='{$WIDTHTYPE} {$CONTAINER_CELL_CLASS}' style="width:{$PACKING_VALUE_WIDTH}%">
				&nbsp;
			</td>
			{/if}
				{assign var=COUNTER value=$COUNTER+1}
			{/while}
		</tr>
	</tbody>
</table>
	<br/>

	<table id='unpack_table' class='table table-bordered equalSplit detailview-table packing'>
	<thead>
		<th class='blockHeader' colspan='9'>
			<img class="cursorPointer alignMiddle blockToggle {if !($IS_HIDDEN)} hide {/if} "  src="{vimage_path('arrowRight.png')}" data-mode="hide" data-id="unpacking">
			<img class="cursorPointer alignMiddle blockToggle {if ($IS_HIDDEN)} hide {/if}"  src="{vimage_path('arrowDown.png')}" data-mode="show" data-id="unpacking">
			&nbsp;&nbsp;Unpacking
		</th>
	</thead>
	<tbody id='otPackingTab'{if $IS_HIDDEN} class="hide" {/if}>
		<tr><td class="fieldLabel" style="width:15%"></td>
			<td class="fieldValue" style="width:9%">Unpk</td>
			<td class="fieldLabel" style="width:15%"></td>
			<td class="fieldValue" style="width:9%">Unpk</td>
			<td class="fieldLabel" style="width:15%"></td>
			<td class="fieldValue" style="width:9%">Unpk</td>
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
			<td style='padding:0 5px 0 0;width:15%;' class='fieldLabel {$WIDTHTYPE}'>
				<label class="muted pull-right marginRight10px">
					{$PACKING_ITEM.label}
				</label>
			</td>
			<td style='text-align:center;width:9%;' class='fieldValue {$WIDTHTYPE}'>
				<span class='value' data-field-type='string'>{$PACKING_ITEM.unpack}</span>
			</td>
			{/foreach}
			{while $COUNTER lt 3}
				<td style='padding:0 5px 0 0;width:15%;' class='fieldLabel {$WIDTHTYPE}'>
				&nbsp;
			</td>
				<td class='fieldValue {$WIDTHTYPE}' style="width:9%">
				&nbsp;
			</td>
				{assign var=COUNTER value=$COUNTER+1}
			{/while}
		</tr>
	</tbody>
</table>
<br/>
{/if}
<table id='bulky_table' class='table table-bordered equalSplit detailview-table bulky'>
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
			<td style=' padding:0 5px 0 0;width:15%;' class='fieldLabel {$WIDTHTYPE}'>
				<label class="muted pull-right marginRight10px">
					{$BULKY_ITEM.label}
				</label>
			</td>
			<td style='text-align:center;width:10%;' class='fieldValue {$WIDTHTYPE}'>
				<span class='value' data-field-type='string'>{$BULKY_ITEM.qty}</span>
			</td>
		{/foreach}
		{while $COUNTER lt 4}
			<td style='padding:0 5px 0 0;width:15%;' class='fieldLabel {$WIDTHTYPE}'>
				&nbsp;
			</td>
			<td class='{$WIDTHTYPE}' style="width:10%">
				&nbsp;
			</td>
			{assign var=COUNTER value=$COUNTER+1}
		{/while}
		</tr>
	</tbody>
</table>
<br/>


{if getenv('INSTANCE_NAME') == 'sirva'}
	<table class='table table-bordered equalSplit detailview-table crating'>
		<thead>
			<th class='blockHeader' colspan='10'>
				<img class="cursorPointer alignMiddle blockToggle {if !($IS_HIDDEN)} hide {/if} "  src="{vimage_path('arrowRight.png')}" data-mode="hide" data-id="crating">
				<img class="cursorPointer alignMiddle blockToggle {if ($IS_HIDDEN)} hide {/if}"  src="{vimage_path('arrowDown.png')}" data-mode="show" data-id="crating">
				&nbsp;&nbsp;Crate Details
			</th>
		</thead>
		<tbody id='cratesTab'{if $IS_HIDDEN} class="hide" {/if}>
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
				<td style='text-align:center; background-color:#E8E8E8;'>
					&nbsp;
				</td>
			</tr>
			<tr>
				{assign var=INTERSTATE_CRATE_TD_WIDTH value=10}
				<td class="fieldLabel" style="width:{$INTERSTATE_CRATE_TD_WIDTH-5}%">
					<b>ID</b>
				</td>
				<td class="fieldLabel" style="width:{$INTERSTATE_CRATE_TD_WIDTH+5}%">
					<span class="redColor">*</span><b>Description</b>
				</td>
				<td class="fieldLabel" style="width:{$INTERSTATE_CRATE_TD_WIDTH}%">
					<span class="redColor">*</span><b>L</b>
				</td>
				<td class="fieldLabel" style="width:{$INTERSTATE_CRATE_TD_WIDTH}%">
					<span class="redColor">*</span><b>W</b>
				</td>
				<td class="fieldLabel" style="width:{$INTERSTATE_CRATE_TD_WIDTH}%">
					<span class="redColor">*</span><b>H</b>
				</td>
				<td class="fieldLabel" style="text-align:center; width:{$INTERSTATE_CRATE_TD_WIDTH}%">
					<b>Cube</b>
				</td>
				<td class="fieldLabel" style="width:{$INTERSTATE_CRATE_TD_WIDTH}%">
					<b>Pk</b>
				</td>
				<td class="fieldLabel" style="width:{$INTERSTATE_CRATE_TD_WIDTH}%">
					<b>Unpk</b>
				</td>
				<td class="fieldLabel" style="width:{$INTERSTATE_CRATE_TD_WIDTH}%">
					<b>Pk</b>
				</td>
				<td class="fieldLabel" style="width:{$INTERSTATE_CRATE_TD_WIDTH}%">
					<b>Unpk</b>
				</td>
				<td class="fieldLabel" style="width:{$INTERSTATE_CRATE_TD_WIDTH}%">
					<b>Disc</b>
				</td>
			</tr>
			{foreach item=CRATE_ROW key=ROW_NUM from=$CRATES}
				<tr class='crateRow' id='crateRow{$ROW_NUM}'>
				<td class='fieldValue' style="width:{$INTERSTATE_CRATE_TD_WIDTH-5}%">
					<span class='value' data-field-type='string'>{$CRATE_ROW->crateid}</span>
				</td>
				<td class='fieldValue' style="width:{$INTERSTATE_CRATE_TD_WIDTH+5}%">
					<span class='value' data-field-type='string'>{$CRATE_ROW->description}</span>
				</td>
				<td class='fieldValue' style="width:{$INTERSTATE_CRATE_TD_WIDTH}%">
					<span class='value' data-field-type='string'>{$CRATE_ROW->crateLength}</span>
				</td>
				<td class='fieldValue' style="width:{$INTERSTATE_CRATE_TD_WIDTH}%">
					<span class='value' data-field-type='string'>{$CRATE_ROW->crateWidth}</span>
				</td>
				<td class='fieldValue' style="width:{$INTERSTATE_CRATE_TD_WIDTH}%">
					<span class='value' data-field-type='string'>{$CRATE_ROW->crateHeight}</span>
				</td>
				<td class='fieldValue' style="width:{$INTERSTATE_CRATE_TD_WIDTH}%">
					<span class='value' data-field-type='string'>{$CRATE_ROW->cube}</span>
				</td>
				<td class='fieldValue' style="width:{$INTERSTATE_CRATE_TD_WIDTH}%">
					<span class='value' data-field-type='int'>{$CRATE_ROW->pack}</span>
				</td>
				<td class='fieldValue' style="width:{$INTERSTATE_CRATE_TD_WIDTH}%">
					<span class='value' data-field-type='int'>{$CRATE_ROW->unpack}</span>
				</td>
				<td class='fieldValue' style="width:{$INTERSTATE_CRATE_TD_WIDTH}%">
					<span class='value' data-field-type='int'>{$CRATE_ROW->otpack}</span>
				</td>
				<td class='fieldValue' style="width:{$INTERSTATE_CRATE_TD_WIDTH}%">
					<span class='value' data-field-type='int'>{$CRATE_ROW->otunpack}</span>
				</td>
				<td class='fieldValue' style="width:{$INTERSTATE_CRATE_TD_WIDTH}%">
					<span class='value' data-field-type='double'>{$CRATE_ROW->discount}</span>
				</td>
				<input type='hidden' class='lineItemId' value='{$CRATE_ROW->lineItemId}' />
			</tr>
			{/foreach}
		</tbody>
	</table>
	<br/>
{else}
{assign var=CRATE_COLUMNS value=10}
{if getenv('IGC_MOVEHQ')}
	{assign var=CRATE_COLUMNS value=13}
{/if}
<table id='crating_table' class='table table-bordered equalSplit detailview-table'>
	<thead>
		<th class='blockHeader' colspan='{$CRATE_COLUMNS}'>
			<img class="cursorPointer alignMiddle blockToggle {if !($IS_HIDDEN)} hide {/if} "  src="{vimage_path('arrowRight.png')}" data-mode="hide" data-id="crates">
			<img class="cursorPointer alignMiddle blockToggle {if ($IS_HIDDEN)} hide {/if}"  src="{vimage_path('arrowDown.png')}" data-mode="show" data-id="crates">
			&nbsp;&nbsp;Crate Details
		</th>
	</thead>
	<tbody id='cratesTab'{if $IS_HIDDEN} class="hide" {/if}>
		<tr>
			<td style='text-align:center; background-color:#E8E8E8;' colspan='2'>
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
			<td class="fieldLabel" style="width:{$INTERSTATE_CRATE_TD_WIDTH-5}%">
				<b>ID</b>
			</td>
			<td class="fieldLabel" style="text-align:center; width:{$INTERSTATE_CRATE_TD_WIDTH+5}%">
				<span class="redColor">*</span><b>Description</b>
			</td>
			<td class="fieldLabel" style="text-align:center; width:{$INTERSTATE_CRATE_TD_WIDTH}%">
				<span class="redColor">*</span><b>L</b>
			</td>
			<td class="fieldLabel" style="text-align:center; width:{$INTERSTATE_CRATE_TD_WIDTH}%">
				<span class="redColor">*</span><b>W</b>
			</td>
			<td class="fieldLabel" style="text-align:center; width:{$INTERSTATE_CRATE_TD_WIDTH}%">
				<span class="redColor">*</span><b>H</b>
			</td>
			<td class="fieldLabel" style="text-align:center; width:{$INTERSTATE_CRATE_TD_WIDTH}%">
				<b>Cube</b>
			</td>
			<td class="fieldLabel" style="width:{$INTERSTATE_CRATE_TD_WIDTH}%">
				<b>Pk</b>
			</td>
			<td class="fieldLabel" style="width:{$INTERSTATE_CRATE_TD_WIDTH}%">
				<b>Unpk</b>
			</td>
			{if getenv('IGC_MOVEHQ')}
				<td class="fieldLabel" style="width:{$INTERSTATE_CRATE_TD_WIDTH}%">
				<b>Apply Tariff</b>
			</td>
			<td class="fieldLabel" style="width:{$INTERSTATE_CRATE_TD_WIDTH}%">
				<b>Custom Rate Pack</b>
			</td>
				<td class="fieldLabel" style="width:{$INTERSTATE_CRATE_TD_WIDTH}%">
				<b>Custom Rate Unpack</b>
			</td>
			{/if}
			<td class="fieldLabel" style="width:{$INTERSTATE_CRATE_TD_WIDTH}%">
				<b>Disc</b>
			</td>
		</tr>
		<tr class='hide defaultCrate crateRow newItemRow'>
			<td class='fieldValue' style="width:{$INTERSTATE_CRATE_TD_WIDTH-5}%">
				<span class='value' data-field-type='string'></span>
			</td>
			<td class='fieldValue' style="width:{$INTERSTATE_CRATE_TD_WIDTH+5}%">
				<span class='value' data-field-type='string'></span>
			</td>
			<td class='fieldValue' style="text-align:right ;width:{$INTERSTATE_CRATE_TD_WIDTH}%">
				<span class='value' data-field-type='string'></span>
			</td>
			<td class='fieldValue' style="width:{$INTERSTATE_CRATE_TD_WIDTH}%">
				<span class='value' data-field-type='string'></span>
			</td>
			<td class='fieldValue' style="width:{$INTERSTATE_CRATE_TD_WIDTH}%">
				<span class='value' data-field-type='string'></span>
			</td>
			<td class='fieldValue' style="width:{$INTERSTATE_CRATE_TD_WIDTH}%">
				<span class='value' data-field-type='string'></span>
			</td>
			<td class='fieldValue' style="width:{$INTERSTATE_CRATE_TD_WIDTH}%">
				<span class='value' data-field-type='boolean'>No</span>
			</td>
			{if getenv('IGC_MOVEHQ')}
				<td class='fieldValue' style="width:{$INTERSTATE_CRATE_TD_WIDTH}%">
					<span class='value' data-field-type='boolean'>No</span>
				</td>
				<td class='fieldValue' style="width:{$INTERSTATE_CRATE_TD_WIDTH}%">
					<span class='value' data-field-type='string'></span>
				</td>
				<td class='fieldValue' style="width:{$INTERSTATE_CRATE_TD_WIDTH}%">
					<span class='value' data-field-type='string'></span>
				</td>
			{/if}
			<td class='fieldValue' style="width:{$INTERSTATE_CRATE_TD_WIDTH}%">
				<span class='value' data-field-type='boolean'>No</span>
			</td>
		</tr>
		{foreach item=CRATE_ROW key=ROW_NUM from=$CRATES}
		<tr class='crateRow' id='crateRow{$ROW_NUM}'>
			<td class='fieldValue' style="width:{$INTERSTATE_CRATE_TD_WIDTH-5}%">
				<span class='value' data-field-type='string'>{$CRATE_ROW->crateid}</span>
			</td>
			<td class='fieldValue' style="width:{$INTERSTATE_CRATE_TD_WIDTH+5}%">
				<span class='value' data-field-type='string'>{$CRATE_ROW->description}</span>
			</td>
			<td class='fieldValue' style="width:{$INTERSTATE_CRATE_TD_WIDTH}%">
				<span class='value' data-field-type='string'>{$CRATE_ROW->crateLength}</span>
			</td>
			<td class='fieldValue' style="width:{$INTERSTATE_CRATE_TD_WIDTH}%">
				<span class='value' data-field-type='string'>{$CRATE_ROW->crateWidth}</span>
			</td>
			<td class='fieldValue' style="width:{$INTERSTATE_CRATE_TD_WIDTH}%">
				<span class='value' data-field-type='string'>{$CRATE_ROW->crateHeight}</span>
			</td>
			<td class='fieldValue' style="width:{$INTERSTATE_CRATE_TD_WIDTH}%">
				<span class='value' data-field-type='string'>{$CRATE_ROW->cube}</span>
			</td>
			<td class='fieldValue' style="width:{$INTERSTATE_CRATE_TD_WIDTH}%">
                <span class='value' data-field-type='int'>{$CRATE_ROW->pack}</span>
			</td>
			<td class='fieldValue' style="width:{$INTERSTATE_CRATE_TD_WIDTH}%">
                <span class='value' data-field-type='int'>{$CRATE_ROW->unpack}</span>
			</td>
			{if getenv('IGC_MOVEHQ')}
				<td class='fieldValue' style="width:{$INTERSTATE_CRATE_TD_WIDTH}%">
					<span class='value' data-field-type='boolean'>{if $CRATE_ROW->apply_tariff eq '0'}No{else}Yes{/if}</span>
				</td>
				<td class='fieldValue' style="width:{$INTERSTATE_CRATE_TD_WIDTH}%">
					<span class='value' data-field-type='string'>{$CRATE_ROW->custom_rate_amount}</span>
				</td>
				<td class='fieldValue' style="width:{$INTERSTATE_CRATE_TD_WIDTH}%">
					<span class='value' data-field-type='string'>{$CRATE_ROW->custom_rate_amount_unpack}</span>
				</td>
			{/if}
			<td class='fieldValue' style="width:{$INTERSTATE_CRATE_TD_WIDTH}%">
				<span class='value' data-field-type='boolean'>{if $CRATE_ROW->discount eq '1'}Yes{else}No{/if}</span>
			</td>
			<input type='hidden' class='lineItemId' value='{$CRATE_ROW->lineItemId}' />
		</tr>
		{/foreach}
	</tbody>
</table>
<br/>
{/if}

{if getenv('INSTANCE_NAME') != 'graebel' && getenv('INSTANCE_NAME') != 'sirva'}
<table class='table table-bordered equalSplit detailview-table'>
	<thead>
		<th class='blockHeader' colspan='4'>
			<img class="cursorPointer alignMiddle blockToggle {if !($IS_HIDDEN)} hide {/if} "  src="{vimage_path('arrowRight.png')}" data-mode="hide" data-id="vehicles">
			<img class="cursorPointer alignMiddle blockToggle {if ($IS_HIDDEN)} hide {/if}"  src="{vimage_path('arrowDown.png')}" data-mode="show" data-id="vehicles">
			&nbsp;&nbsp;Vehicles
		</th>
	</thead>
	<tbody id='vehiclesTab'{if $IS_HIDDEN} class="hide" {/if}>
		<tr>
			<td style='width:60%' class='fieldLabel'>
				<span class="redColor">*</span><b>Description</b>
			</td>
			<td style='width:40%' class='fieldLabel'>
				<span class="redColor">*</span><b>Weight</b>
			</td>
		</tr>
		<tr class='hide vehicleItem vehicleRow newVehicleRow'>
			<td class='fieldValue' style='text-align:center'>
				<span class='value' data-field-type='string'></span>
			</td>
			<td class='fieldValue' style='text-align:center'>
				<div class='input-prepend input-prepend-centered'>
					<span class='value' data-field-type='string'></span>
				</div>
			</td>
		</tr>
		{foreach item=VEHICLE_ROW key=ROW_NUM from=$VEHICLES}
		<tr class='vehicleItem vehicleRow' id='vehicleRow-{$ROW_NUM}'>
			<td class='fieldValue' style='text-align:center'>
				<span class='value' data-field-type='string'>{$VEHICLE_ROW["description"]}</span>
			</td>
			<td class='fieldValue' style='text-align:center'>
				<div class='input-prepend input-prepend-centered'>
					<span class='value' data-field-type='string'>{$VEHICLE_ROW["weight"]}</span>
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
