{strip}
    {if $GUEST_MODULES}
    {assign var=GUEST_MODULES_INBLOCK value=$GUEST_MODULES[$BLOCK_LABEL]}
    {foreach key=GUEST_INDEX item=GUEST_MODULE from=$GUEST_MODULES_INBLOCK}
        {if $GUEST_MODULE eq 'DetailLineItems'}
            {continue}
        {/if}
        {if $BLOCK_SUBLIST && !array_key_exists($GUEST_MODULE, $BLOCK_SUBLIST)}
            {continue}
        {/if}
        {assign var=HAS_CONTENT value=(!$BLOCK_SUBLIST || $BLOCK_SUBLIST[$GUEST_MODULE])}
        <div id="contentHolder_{$GUEST_MODULE}" class="sectionContentHolder {$CONTENT_DIV_CLASS} {if !$ALWAYS_SHOW_CONTENT_DIV}hide{/if} {if !$HAS_CONTENT}inactive{/if}">
		{if $HAS_CONTENT}
            <input type="hidden" value="{$GUEST_MODULE}" class="guestModule" id="guestModule_{$GUEST_MODULE}">
            {include file=vtemplate_path('EditBlock.tpl', $GUEST_MODULE) GUEST_MODULE=$GUEST_MODULE}
        {/if}
		</div>
    {/foreach}
    {/if}
{/strip}
