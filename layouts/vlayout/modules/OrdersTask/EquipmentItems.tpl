{strip}
{assign var=ROW_COUNT  value=$LIST_ITEM|@count}
{if empty($ROW_COUNT)}
    {assign var=$ROW_COUNT  value=0}
{/if}
<tr class="fieldLabel">
    <td class="hide">
        <input type="hidden" name="numItem_{$BLOCK_LABEL}" value="{$ROW_COUNT}">
    </td>
    {foreach item = FIELD_NAME from = $LIST_FIELD}
        {assign var=FIELD_MODEL  value=$MODULE_MODEL->getField($FIELD_NAME)}
        <td class="fieldLabel">
            <label class="muted">{if $FIELD_MODEL->isMandatory() eq true} <span class="redColor">*</span> {/if}{vtranslate($FIELD_MODEL->get('label'),$MODULE)}</label>
        </td>
    {/foreach}
</tr>
{foreach from = $LIST_ITEM key=ROW_NUM item=FIELD_VALUES}
    <tr class="itemRow" data-rowno = "{$ROW_NUM}">
        <td class="hide">
            <input type="hidden" name="itemId_{$BLOCK_LABEL}" value="{if $FIELD_VALUES['id']}{$FIELD_VALUES['id']}{else}none{/if}" />
        </td>
        {foreach item = FIELD_NAME from = $LIST_FIELD}
            {assign var=FIELD_MODEL  value=$MODULE_MODEL->getField($FIELD_NAME)->set('fieldvalue',$FIELD_VALUES[$FIELD_NAME])}
            <td class="fieldValue ">
                <div class="row-fluid">
                            <span class="span10">
                                {include file=vtemplate_path($FIELD_MODEL->getUITypeModel()->getTemplateName(),$MODULE) FIELD_MODEL=$FIELD_MODEL}
                            </span>
                </div>
            </td>
        {/foreach}
    </tr>
{/foreach}
{/strip}