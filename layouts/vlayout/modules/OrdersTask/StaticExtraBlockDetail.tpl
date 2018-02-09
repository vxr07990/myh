{strip}
    {assign var=MODULE_MODEL  value=$RECORD_MODEL->getModule()}
    {assign var=LIST_FIELD  value=$BLOCK_SETTING.fields}
    {assign var=FIELD_COUNT  value=$LIST_FIELD|@count}
	{if $BLOCK_LABEL eq 'LBL_CPU'}
		{assign var=COLSPAN  value=$FIELD_COUNT+3}
	{else}
		{assign var=COLSPAN  value=$FIELD_COUNT}
	{/if}
    {assign var=IS_HIDDEN  value=1}
    {assign var=LIST_ITEM  value=$RECORD_MODEL->getExtraBlockFieldValues($BLOCK_LABEL)}
	{*{assign var=CPU_ITEMS  value=$RECORD_MODEL->getActualsItems('LBL_CPU')}*}
	{$ACTUALS_FIELDS = ['cartonqty','packingqty','unpackingqty']}
	
    <table  class='table table-bordered {if $BLOCK_LABEL neq 'LBL_CPU'}equalSplit{/if} detailview-table' data-extra-block="1">
        <thead>
        <tr>
            <th class="blockHeader" colspan="{$COLSPAN}">
                <img class="cursorPointer alignMiddle blockToggle {if !($IS_HIDDEN)} hide {/if} "  src="{vimage_path('arrowRight.png')}" data-mode="hide" data-id={$BLOCK_LIST[$BLOCK_LABEL]->get('id')}>
                <img class="cursorPointer alignMiddle blockToggle {if ($IS_HIDDEN)} hide {/if}"  src="{vimage_path('arrowDown.png')}" data-mode="show" data-id={$BLOCK_LIST[$BLOCK_LABEL]->get('id')}>
                &nbsp;&nbsp;{vtranslate($BLOCK_LABEL, $MODULE)}
            </th>
        </tr>
        </thead>
        <tbody {if $IS_HIDDEN} class="hide" {/if}>
        <tr>
            {foreach item = FIELD_NAME from = $LIST_FIELD}
                {assign var=FIELD_MODEL  value=$MODULE_MODEL->getField($FIELD_NAME)}
                <td class="{if $BLOCK_LABEL neq 'LBL_CPU'}fieldLabel{/if} medium" style="background: #f7f7f9;">
                    <label class="muted">{vtranslate($FIELD_MODEL->get('label'),$MODULE)}</label>
                </td>
				{if $BLOCK_LABEL eq 'LBL_CPU' && $FIELD_NAME|in_array:$ACTUALS_FIELDS}
					<td class="{if $BLOCK_LABEL neq 'LBL_CPU'}fieldLabel{/if} medium" style="background: #f7f7f9;">
						<label class="muted">Actual {vtranslate($MODULE_MODEL->getField($FIELD_NAME)->get('label'),$MODULE)}</label>
					</td>
				{/if}
            {/foreach}
        </tr>
		{assign var=ARRAY_INDEX  value=0}
        {foreach from = $LIST_ITEM key=ROW_NUM item=FIELD_VALUES}
			
            <tr>
                {foreach item = FIELD_NAME from = $LIST_FIELD}
                    {assign var=FIELD_MODEL  value=$MODULE_MODEL->getField($FIELD_NAME)->set('fieldvalue',$FIELD_VALUES[$FIELD_NAME])}
                    <td class="fieldValue medium">
                        <span class="value">
                            {$FIELD_MODEL->getDisplayValue($FIELD_VALUES[$FIELD_NAME])}
                        </span>
                    </td>
					{*{if $BLOCK_LABEL eq 'LBL_CPU' && $FIELD_NAME|in_array:$ACTUALS_FIELDS}
						<td class="fieldValue medium">
							<span class="value">
								{if isset($CPU_ITEMS[$ARRAY_INDEX]->$FIELD_NAME)} 
									{$CPU_ITEMS[$ARRAY_INDEX]->$FIELD_NAME}
								{else}
									-
								{/if}
							</span>
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
