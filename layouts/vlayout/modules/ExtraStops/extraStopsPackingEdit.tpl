{strip}
{assign var=PACKING_COLUMNS value=6}
{assign var=PACK_LABEL_WIDTH value='20%'}
{assign var=PACK_VALUE_WIDTH value='12%'}
{if getenv('INSTANCE_NAME') == 'graebel'}
	{assign var=PACK_LABEL_WIDTH value='15%'}
	{assign var=PACK_VALUE_WIDTH value='9%'}
	{assign var=PACKING_COLUMNS value=9}
{/if}
<table id='pack_table' class='table table-bordered detailview-table packing' blocktoggleid="stopPacking{$STOP_INDEX+1}">
	<thead>
		<th class='blockHeader' colspan='{$PACKING_COLUMNS}'>
			<img class="cursorPointer alignMiddle blockToggle {if !($IS_HIDDEN)} hide {/if} "  src="{vimage_path('arrowRight.png')}" data-mode="hide">
			<img class="cursorPointer alignMiddle blockToggle {if ($IS_HIDDEN)} hide {/if}"  src="{vimage_path('arrowDown.png')}" data-mode="show">
			&nbsp;&nbsp;Packing
		</th>
	</thead>
	<tbody id='packingTab'{if $IS_HIDDEN} class="packing hide" {/if} blocktoggleid="stopPacking{$STOP_INDEX+1}">
		<tr>
			<td style='width:{$PACK_LABEL_WIDTH}' class="fieldLabel"></td>
            {if getenv('INSTANCE_NAME') == 'graebel'}<td class="fieldValue" style="width:{$PACK_VALUE_WIDTH}">Containers</td>{/if}
			<td style='width:{$PACK_VALUE_WIDTH}' class="fieldValue">Pk</td>
            <td style='width:{$PACK_LABEL_WIDTH}' class="fieldLabel"></td>
            {if getenv('INSTANCE_NAME') == 'graebel'}<td class="fieldValue" style="width:{$PACK_VALUE_WIDTH}">Containers</td>{/if}
			<td style='width:{$PACK_VALUE_WIDTH}' class="fieldValue">Pk</td>
            <td style='width:{$PACK_LABEL_WIDTH}' class="fieldLabel"></td>
            {if getenv('INSTANCE_NAME') == 'graebel'}<td class="fieldValue" style="width:{$PACK_VALUE_WIDTH}">Containers</td>{/if}
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
                <td style='width:{$PACK_VALUE_WIDTH}; text-align:center;' class='fieldValue {$WIDTHTYPE}'>
				<input id='{$MODULE_NAME}_editView_fieldName_containers{$ITEM_NUM}' type='number' min='0' max class='input-large packCartonOnlyField' name='containers_pack{$STOP_INDEX+1}_{$ITEM_NUM}' style="width:80%;" value='{$PACKING_ITEM.containers}' />
			</td>
            {/if}
			<td style='width:{$PACK_VALUE_WIDTH}; text-align:center;' class='fieldValue {$WIDTHTYPE}'>
				<input id='{$MODULE_NAME}_editView_fieldName_pack{$STOP_INDEX+1}_{$ITEM_NUM}' type='number' min='0' max class='input-large packQtyField' name='pack{$STOP_INDEX+1}_{$ITEM_NUM}' style='width:80%;' value='{$PACKING_ITEM.pack}' />
			</td>
            {/foreach}
            {while $COUNTER lt 3}
            <td style='width:{$PACK_LABEL_WIDTH}; padding:0 5px 0 0;' class='fieldLabel {$WIDTHTYPE}'>
			</td>
            <td style='width:{$PACK_VALUE_WIDTH};' class='{$WIDTHTYPE}'>
			</td>
			{if getenv('INSTANCE_NAME') == 'graebel'}
				<td style='width:{$PACK_VALUE_WIDTH};' class='{$WIDTHTYPE}'>
			</td>
			{/if}
                {assign var=COUNTER value=$COUNTER+1}
            {/while}
		</tr>
	</tbody>
</table>
{if getenv('INSTANCE_NAME') != 'uvlc'}
    <table id='unpack_table' class='table table-bordered detailview-table packing unpacking' blocktoggleid="stopUnPacking{$STOP_INDEX+1}">
	<thead>
		<th class='blockHeader' colspan='9'>
			<img class="cursorPointer alignMiddle blockToggle {if !($IS_HIDDEN)} hide {/if} "  src="{vimage_path('arrowRight.png')}" data-mode="hide">
			<img class="cursorPointer alignMiddle blockToggle {if ($IS_HIDDEN)} hide {/if}"  src="{vimage_path('arrowDown.png')}" data-mode="show">
			&nbsp;&nbsp;Unpacking
		</th>
	</thead>
	<tbody id='otPackingTab'{if $IS_HIDDEN} class="unpacking hide" {/if} blocktoggleid="stopUnPacking{$STOP_INDEX+1}">
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
				<input id='{$MODULE_NAME}_editView_fieldName_unpack{$STOP_INDEX+1}_{$ITEM_NUM}' type='number' min='0' max class='input-large packQtyField' name='unpack{$STOP_INDEX+1}_{$ITEM_NUM}' style='width:80%;' value='{$PACKING_ITEM.unpack}' />
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
{/if}
{/strip}
