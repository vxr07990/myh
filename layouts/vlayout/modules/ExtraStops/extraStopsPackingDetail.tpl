{strip}
{assign var=IS_HIDDEN value='1'}
{assign var=PACKING_COLUMNS value=6}
{assign var=PACKING_LABEL_WIDTH value=20}
{assign var=PACKING_VALUE_WIDTH value=12}
{if getenv('INSTANCE_NAME') == 'graebel'}
	{assign var=PACKING_LABEL_WIDTH value=15}
	{assign var=PACKING_VALUE_WIDTH value=9}
	{assign var=PACKING_COLUMNS value=9}
{/if}
<table id='pack_table' class='table table-bordered equalSplit detailview-table packing'>
	<thead>
		<th class='blockHeader' colspan='{$PACKING_COLUMNS}'>
			<img class="cursorPointer alignMiddle blockToggle {if !($IS_HIDDEN)} hide {/if} "  src="{vimage_path('arrowRight.png')}" data-mode="hide">
			<img class="cursorPointer alignMiddle blockToggle {if ($IS_HIDDEN)} hide {/if}"  src="{vimage_path('arrowDown.png')}" data-mode="show">
			&nbsp;&nbsp;Packing
		</th>
	</thead>
	<tbody id='packingTab'{if $IS_HIDDEN} class="hide" {/if}>
		<tr>
			<td class="fieldLabel" style="width:{$PACKING_LABEL_WIDTH}%"></td>
            {if getenv('INSTANCE_NAME') == 'graebel'}<td class="fieldValue" style="width:{$PACKING_VALUE_WIDTH}%">Containers</td>{/if}
			<td class="fieldValue" style="width:{$PACKING_VALUE_WIDTH}%">Pk</td>
			<td class="fieldLabel" style="width:{$PACKING_LABEL_WIDTH}%"></td>
            {if getenv('INSTANCE_NAME') == 'graebel'}<td class="fieldValue" style="width:{$PACKING_VALUE_WIDTH}%">Containers</td>{/if}
			<td class="fieldValue" style="width:{$PACKING_VALUE_WIDTH}%">Pk</td>
			<td class="fieldLabel" style="width:{$PACKING_LABEL_WIDTH}%"></td>
            {if getenv('INSTANCE_NAME') == 'graebel'}<td class="fieldValue" style="width:{$PACKING_VALUE_WIDTH}%">Containers</td>{/if}
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
                <td style='text-align:center;width:{$PACKING_VALUE_WIDTH}%;' class='fieldValue {$WIDTHTYPE}'>
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
			<td class='{$WIDTHTYPE}' style="width:{$PACKING_VALUE_WIDTH}%">
				&nbsp;
			</td>
			{/if}
                {assign var=COUNTER value=$COUNTER+1}
            {/while}
		</tr>
	</tbody>
</table>
	<table id='unpack_table' class='table table-bordered equalSplit detailview-table packing'>
	<thead>
		<th class='blockHeader' colspan='9'>
			<img class="cursorPointer alignMiddle blockToggle {if !($IS_HIDDEN)} hide {/if} "  src="{vimage_path('arrowRight.png')}" data-mode="hide">
			<img class="cursorPointer alignMiddle blockToggle {if ($IS_HIDDEN)} hide {/if}"  src="{vimage_path('arrowDown.png')}" data-mode="show">
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

{/strip}
