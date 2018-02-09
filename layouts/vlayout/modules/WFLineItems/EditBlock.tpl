{strip}
    {assign var=SPECIAL_COL_SPAN value=8}
	{assign var=GUEST_MODULE_CAPS value=$GUEST_MODULE|upper}
	{assign var=GUEST_MODULE_LOWER value=$GUEST_MODULE|lower}
	{if ${$GUEST_MODULE_CAPS}}
		{assign var=GUEST_LIST value=$GUEST_MODULE_CAPS|cat: '_LIST'}
		{assign var=FIELD_ARRAY value=$GUEST_MODULE_CAPS|cat: '_BLOCK_FIELDS'}
		{assign var=GUEST_BLOCK_LABEL value=$GUEST_MODULE_CAPS|cat: '_BLOCK_LABEL'}
		{assign var=ID_COLUMN value=$GUEST_MODULE_LOWER|cat: 'id'}
		{assign var=TABLE_NAME value=$GUEST_MODULE|cat: 'Table'}
		<table class="table table-bordered blockContainer showInlineTable {if is_array($HIDDEN_BLOCKS)}{if in_array($TABLE_NAME, $HIDDEN_BLOCKS)}hide{/if}{/if} block_{$GUEST_BLOCK_LABEL}" name="{$TABLE_NAME}">
			<thead>
			<tr>
				<th class="blockHeader" colspan="{$SPECIAL_COL_SPAN}">{vtranslate(${$GUEST_BLOCK_LABEL}, $GUEST_MODULE)}</th>
			</tr>
			</thead>
			{if !$LOCK_RATING}
				<tbody>
				<tr class="fieldLabel">
					<td colspan="{$SPECIAL_COL_SPAN}">
						<button type="button" class="add{$GUEST_MODULE}" name="add{$GUEST_MODULE}">+</button>
						<input type="hidden" id="num{$GUEST_MODULE}" name="num{$GUEST_MODULE}" value="{${$GUEST_LIST}|@count}">
						<button type="button" class="add{$GUEST_MODULE}" name="add{$GUEST_MODULE}2" style="clear:right;float:right">+</button>
					</td>
				</tr>
				</tbody>
			{/if}
			<tr>
				<td {*style="width:2.5%;"*}></td>
				{foreach key=FIELD_NAME item=FIELD_MODEL from=${$FIELD_ARRAY} name=blockfields}
					{if $FIELD_MODEL->get('name') eq 'wfinventory' OR $FIELD_MODEL->get('name') eq 'wfarticle'}
						<td {*style="width:200px;"*}>
					{elseif $FIELD_MODEL->get('name') eq 'description'}
						<td {*style="width:250px;"*}>
                    {elseif $FIELD_MODEL->get('name') eq 'onhand' OR $FIELD_MODEL->get('name') eq 'requested' OR $FIELD_MODEL->get('name') eq 'processed'}
                        <td {*style="width:50px;"*}>
					{else}
						<td>
					{/if}
						{vtranslate($FIELD_MODEL->get('label'), $GUEST_MODULE)}
					</td>
				{/foreach}
			</tr>
			<tbody class="default{$GUEST_MODULE} {$GUEST_MODULE}Block hide">
				{assign var=DEFAULT_CHZN value=1}
				{assign var=COUNTER value=1}
				{foreach key=FIELD_NAME item=FIELD_MODEL from=${$FIELD_ARRAY} name=blockfields}
				{if $COUNTER eq '1'}
					<a>
						<td class="blockHeader" style="background-color:#E8E8E8; cursor:pointer; width:1%; vertical-align: middle;">
							<i title="Delete" class="delete{$GUEST_MODULE} icon-trash"></i>
							<input id="{$GUEST_MODULE_LOWER}_id" type="hidden" name="{$GUEST_MODULE_LOWER}_id" value="none">
						</td>
					</a>
				{/if}
				{assign var="isReferenceField" value=$FIELD_MODEL->getFieldDataType()}
				{if $isReferenceField eq 'reference' && count($FIELD_MODEL->getReferenceList()) < 1}{continue}{/if}
				{if $FIELD_MODEL->get('uitype') eq "20" or $FIELD_MODEL->get('uitype') eq "19"}
                {while ($COUNTER < $SPECIAL_COL_SPAN)}
                    <td class="{$WIDTHTYPE}"></td><td class="{$WIDTHTYPE}"></td>
                    {assign var=COUNTER value=$COUNTER+1}
                {/while}
				</tr>
				<tr>
					<td>
					{assign var=COUNTER value=1}
					{/if}
					{if ($COUNTER eq $SPECIAL_COL_SPAN)}
				</tr>
				<tr>
					{assign var=COUNTER value=1}
					{else}
					{assign var=COUNTER value=$COUNTER+1}
					{/if}
					{if $FIELD_MODEL->get('uitype') neq "83"}
						<td class="fieldValue {$WIDTHTYPE}"
							{if $FIELD_MODEL->get('uitype') eq '19' or $FIELD_MODEL->get('uitype') eq '20'} colspan="3" {assign var=COUNTER value=$COUNTER+1} {/if}
							{if $FIELD_MODEL->get('name') eq 'description'} style="width: unset;" {/if}
							>
							{if $FIELD_MODEL->get('name') neq "description"}
								<div class="row-fluid">
									<span class="span10">
								{/if}
										{include file=vtemplate_path($FIELD_MODEL->getUITypeModel()->getTemplateName(),$GUEST_MODULE) GUEST_FIELDS=${$FIELD_ARRAY}}
							{if $FIELD_MODEL->get('name') neq "description"}
									</span>
								</div>
							{/if}
						</td>
					{/if}
					{if ${$FIELD_ARRAY}|@count eq 1 and $FIELD_MODEL->get('uitype') neq "19" and $FIELD_MODEL->get('uitype') neq "20" and $FIELD_MODEL->get('uitype') neq "30" and $FIELD_MODEL->get('name') neq "recurringtype"}
						<td class="{$WIDTHTYPE}"></td><td class="{$WIDTHTYPE}"></td>
					{/if}
                    {if isset($SINGLE_FIELDS) && in_array($FIELD_NAME, $SINGLE_FIELDS)}
                        <td class="fieldLabel {$WIDTHTYPE}"></td><td class="fieldValue {$WIDTHTYPE}"></td>
                        </tr>
                        <tr>
                        {assign var=COUNTER value=$COUNTER+1}
                    {/if}
					{/foreach}
					{* adding additional column for odd number of fields in a block *}
					{if ${$FIELD_ARRAY}|@end eq true and ${$FIELD_ARRAY}|@count neq 1 and $COUNTER eq 1}
						<td class="fieldLabel {$WIDTHTYPE}"></td><td class="{$WIDTHTYPE}"></td>
					{/if}
				</tr>
			</tbody>
			{assign var=DEFAULT_CHZN value=0}
			{foreach key=RECORD_INDEX item=CURRENT_RECORD from=${$GUEST_LIST}}
				{assign var=RECORD_COUNT value=$RECORD_INDEX+1}
				<tbody class="{$GUEST_MODULE}Block" guestid="{$RECORD_COUNT}">
					<tr class="fieldLabel" colspan="{$SPECIAL_COL_SPAN}">
						<td colspan="{$SPECIAL_COL_SPAN}" class="blockHeader" style="background-color:#E8E8E8;">
							<span class="{$GUEST_MODULE}Title"></span>
							<a style="float: right; padding: 3px"><i title="Delete" class="delete{$GUEST_MODULE} icon-trash"></i></a>
							<input id="{$GUEST_MODULE_LOWER}_id_{$RECORD_COUNT}" type="hidden" name="{$GUEST_MODULE_LOWER}_id_{$RECORD_COUNT}" value="{$CURRENT_RECORD[$ID_COLUMN]}">
							<input id="{$GUEST_MODULE_LOWER}_deleted" type="hidden" name="{$GUEST_MODULE_LOWER}_deleted_{$RECORD_COUNT}" value="none">
						</td>
					</tr>
					{assign var=COUNTER value=1}
                    {foreach key=FIELD_NAME item=FIELD_MODEL from=${$FIELD_ARRAY} name=blockfields}
                    {if $COUNTER eq '1'}
                        <td class="{$WIDTHTYPE}"></td>{*<td class="{$WIDTHTYPE}"></td>*}
                    {/if}
                        {assign var=CUSTOM_FIELD_NAME value=$FIELD_NAME|cat:"_"|cat:$RECORD_COUNT}
                        {assign var=FIELD_MODEL value=$FIELD_MODEL->set('fieldvalue',${$GUEST_LIST}[$RECORD_INDEX][$FIELD_NAME])}
                        {assign var=FIELD_MODEL value=$FIELD_MODEL->set('name',$CUSTOM_FIELD_NAME)}
                        {assign var=FIELD_MODEL value=$FIELD_MODEL->set('noncustomname',$FIELD_NAME)}
                        {assign var="isReferenceField" value=$FIELD_MODEL->getFieldDataType()}
                        {if $isReferenceField eq 'reference' && count($FIELD_MODEL->getReferenceList()) < 1}{continue}{/if}
                        {if $FIELD_MODEL->get('uitype') eq "20" or $FIELD_MODEL->get('uitype') eq "19"}
                            {if $COUNTER eq '1'}
                                <td class="{$WIDTHTYPE}"></td><td class="{$WIDTHTYPE}"></td>
                                </tr>
                                <tr>
                                {assign var=COUNTER value=0}
                            {/if}
                        {/if}
                        {if $COUNTER eq $SPECIAL_COL_SPAN}
                            </tr>
                            <tr>
                            {assign var=COUNTER value=1}
                        {else}
                            {assign var=COUNTER value=$COUNTER+1}
                        {/if}
                        {if $FIELD_MODEL->get('uitype') neq "83"}
                            <td class="fieldValue {$WIDTHTYPE}" {if $FIELD_MODEL->get('uitype') eq '19' or $FIELD_MODEL->get('uitype') eq '20'} colspan="3" {assign var=COUNTER value=$COUNTER+1} {/if}>
                                <div class="row-fluid">
                                    <span class="span10">
                                        {include file=vtemplate_path($FIELD_MODEL->getUITypeModel()->getTemplateName(),$GUEST_MODULE) GUEST_FIELDS=${$FIELD_ARRAY}}
                                    </span>
                                </div>
                            </td>
                        {/if}
                        {if ${$FIELD_ARRAY}|@count eq 1 and $FIELD_MODEL->get('uitype') neq "19" and $FIELD_MODEL->get('uitype') neq "20" and $FIELD_MODEL->get('uitype') neq "30" and $FIELD_MODEL->get('name') neq "recurringtype"}
                            {*NOTE: Not sure that this is right?*}
                            <td class="{$WIDTHTYPE}"></td><td class="{$WIDTHTYPE}"></td>
                        {/if}
                        {if isset($SINGLE_FIELDS) && in_array($FIELD_NAME, $SINGLE_FIELDS)}
                            {while ($COUNTER < $SPECIAL_COL_SPAN)}
                                <td class="fieldLabel {$WIDTHTYPE}"></td><td class="fieldValue {$WIDTHTYPE}"></td>
                                {assign var=COUNTER value=$COUNTER+1}
                            {/while}
                            </tr>
                            <tr>
                        {/if}
                    {/foreach}
                    {* adding additional column for odd number of fields in a block *}
                    {if ${$FIELD_ARRAY}|@end eq true and ${$FIELD_ARRAY}|@count neq 1 and $COUNTER eq 1}
                        <td class="fieldLabel {$WIDTHTYPE}"></td><td class="{$WIDTHTYPE}"></td>
                    {/if}
                </tr>
            </tbody>
        {/foreach}
    </table>
    <br>
	{/if}
{/strip}
