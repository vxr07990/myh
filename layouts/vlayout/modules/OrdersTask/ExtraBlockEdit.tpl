{strip}
    {if $BLOCK_SETTING.isDynamicBlock}
        {include file=vtemplate_path('DynamicExtraBlockEdit.tpl',$MODULE)}
    {else}
        {include file=vtemplate_path('StaticExtraBlockEdit.tpl',$MODULE)}
    {/if}
{/strip}