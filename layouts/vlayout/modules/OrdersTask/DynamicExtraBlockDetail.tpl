{strip}
    {assign var=MODULE_MODEL  value=$RECORD_MODEL->getModule()}
    {assign var=LIST_FIELD  value=$BLOCK_SETTING.fields}
    {assign var=FIELD_COUNT  value=$LIST_FIELD|@count}
	{if $BLOCK_LABEL eq 'LBL_EQUIPMENT'}
		{assign var=COLSPAN  value=($FIELD_COUNT+1)*2}
	{else}
		{assign var=COLSPAN  value=$FIELD_COUNT*2}
	{/if}
{*	{assign var=EQUIP_ITEMS  value=$RECORD_MODEL->getActualsItems('LBL_EQUIPMENT')}*}
		
    {assign var=LIST_ITEM  value=$RECORD_MODEL->getExtraBlockFieldValues($BLOCK_LABEL)}
    <table  class='table table-bordered {if $BLOCK_LABEL neq 'LBL_EQUIPMENT'}equalSplit{/if} detailview-table'>
        <thead>
        <tr>
            <th class='blockHeader medium ' colspan='{$COLSPAN}'>
                <img class="cursorPointer alignMiddle blockToggle {if !($IS_HIDDEN)} hide {/if} "  src="{vimage_path('arrowRight.png')}" data-mode="hide" data-id={if $FROMEDIT neq 'TRUE'}{$BLOCK_LIST[$BLOCK_LABEL_KEY]->get('id')}{/if}>
                <img class="cursorPointer alignMiddle blockToggle {if ($IS_HIDDEN)} hide {/if}"  src="{vimage_path('arrowDown.png')}" data-mode="show" data-id={if $FROMEDIT neq 'TRUE'}{$BLOCK_LIST[$BLOCK_LABEL_KEY]->get('id')}{/if}>
                {vtranslate($BLOCK_LABEL, $MODULE)}
            </th>
        </tr>
        </thead>
        <tbody>
		{assign var=ARRAY_INDEX  value=0}
        {foreach from = $LIST_ITEM key=ROW_NUM item=FIELD_VALUES}
            <tr >
                {foreach item = FIELD_NAME from = $LIST_FIELD}
                    {assign var=FIELD_MODEL  value=$MODULE_MODEL->getField($FIELD_NAME)->set('fieldvalue',$FIELD_VALUES[$FIELD_NAME])}
                    <td class="fieldLabel medium " style="min-width: 100px">
                        <label class="muted pull-right marginRight10px">{vtranslate($FIELD_MODEL->get('label'),$MODULE)}</label>
                    </td>
                    <td class="fieldValue medium " style="min-width: 100px">
                        {$FIELD_MODEL->getDisplayValue($FIELD_VALUES[$FIELD_NAME])}
                    </td>
					{*{if $BLOCK_LABEL eq 'LBL_EQUIPMENT' && $FIELD_NAME eq 'equipmentqty'}
						<td class="fieldLabel medium ">
							<label class="muted">Actual {vtranslate($MODULE_MODEL->getField($FIELD_NAME)->get('label'),$MODULE)}</label>
						</td>
						<td class="fieldValue medium ">
							{if isset($EQUIP_ITEMS[$ARRAY_INDEX]->$FIELD_NAME)} 
								{$EQUIP_ITEMS[$ARRAY_INDEX]->$FIELD_NAME}
							{else}
								-
							{/if}
						</td>
					{/if}*}
                {/foreach}
            </tr>
			{assign var=ARRAY_INDEX  value=$ARRAY_INDEX+1}
        {/foreach}
        </tbody>
    </table>
    </br>
{/strip}
