<div style="margin-top:3%;">
<table class='table table-bordered blockContainer showInlineTable misc' name="MiscItemsTable">
	<thead>
		<th class='blockHeader' colspan='8'>
			<img class="cursorPointer alignMiddle blockToggle {if !($IS_HIDDEN)} hide {/if} "  src="{vimage_path('arrowRight.png')}" data-mode="hide">
			<img class="cursorPointer alignMiddle blockToggle {if ($IS_HIDDEN)} hide {/if}"  src="{vimage_path('arrowDown.png')}" data-mode="show">
			&nbsp;&nbsp;Account Policy Items
		</th>
	</thead>
	<tbody id='qtyRateItemsTab'{if $IS_HIDDEN} class="hide" {/if}>
		<tr>
			<td colspan='8' style='padding:0'>
				<button type='button' id='addMiscItem'>+</button>
				<button type='button' id='addMiscItem2' style='clear:right; float:right;'>+</button>
			</td>
		</tr>
		<tr>
			<td style='width:10%'>
				<input type="hidden" class="hide" name="numMisc" value="{$MISC_CHARGES|@count - 1}" />&nbsp;</td>
			<td style='width:25%'>
				<span class="redColor">*</span><b>Description</b>
			</td>
			<td style='width:20%'>
				<span class="redColor">*</span><b>Authorization</b>
			</td>
			<td style='width:20%'>
				<b> Authorization Limits</b>
			</td>
            <td style='width:2515%'>
				<b>Remarks</b>
			</td>
			
		</tr>
        <input type="hidden" name="numMiscItems" value="{$MISC_ITEMS_COUNT}">
		<tr class='hide defaultMiscItem MiscItemRow'>
			<td class='fieldValue' style="width:5%;text-align:center;margin:auto">
				<a class="deleteMiscItemButton">
					<i title="Delete" class="icon-trash alignMiddle"></i>
				</a>
			</td>
			<td class='fieldValue' style='text-align:center'>
                <input type="hidden"  name="miscItemDbId" value=""/>
				<input type="text" class="input-large" name="miscItemDescription" style="width:80%"  />
			</td>
			<td class='fieldValue'>
				<select name="miscItemAuth" class="chzn-select- ">
                            <option value=""></option>
                            <option value="Authorized">Authorized</option>
                            <option value="Seek Approval">Seek Approval</option>
                            <option value="Not Authorized">Not Authorized</option>
                        </select>
			</td>
			<td class='fieldValue' style='text-align:center'>
				<input style="width: 60%;" type="text" name="miscItemAuthLimit" value=""/>
			</td>
			<td class='fieldValue' style='text-align:center'>
				<input type="text" name="misItemRemarks" value=""/>
			</td>
		</tr>
		
		{foreach item=MISC_ITEM key=ROW_NUM from=$MISC_TARIFF_ITEMS}
		<tr class='MiscItemRow'>
			<td class='fieldValue' style="width:5%;text-align:center;margin:auto">
				<a class="deleteMiscItemButton">
					<i title="Delete" class="icon-trash alignMiddle"></i>
				</a>
			</td>
			<td class='fieldValue' style='text-align:center'>
                <input type="hidden"  name="miscItemDbId-{$MISC_ITEM.tmp_id}" value="{$MISC_ITEM.id}"/>
				<input type="text" class="input-large" name="miscItemDescription-{$MISC_ITEM.tmp_id}" style="width:80%" value="{$MISC_ITEM.item_des}" />
			</td>
			<td class='fieldValue'>
				<select name="miscItemAuth-{$MISC_ITEM.tmp_id}" class="chzn-select- ">
                            <option value=""></option>
                            <option value="Authorized" {if $MISC_ITEM.item_auth eq 'Authorized'} selected {/if}>Authorized</option>
                            <option value="Seek Approval" {if $MISC_ITEM.item_auth eq 'Seek Approval'} selected {/if}>Seek Approval</option>
                            <option value="Not Authorized" {if $MISC_ITEM.item_auth eq 'Not Authorized'} selected {/if} >Not Authorized</option>
                        </select>
			</td>
			<td class='fieldValue' style='text-align:center'>
				<input style="width: 60%;" type="text" name="miscItemAuthLimit-{$MISC_ITEM.tmp_id}" value="{$MISC_ITEM.item_auth_limits}"/>
			</td>
			<td class='fieldValue' style='text-align:center'>
				<input type="text" name="misItemRemarks-{$MISC_ITEM.tmp_id}" value="{$MISC_ITEM.item_remarks}"/>
			</td>
		</tr>
		{/foreach}
	</tbody>
</table>
<br />
</div>
