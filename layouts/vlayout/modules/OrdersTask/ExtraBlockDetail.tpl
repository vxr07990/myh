{strip}
    {if $BLOCK_SETTING.isDynamicBlock}
        {include file=vtemplate_path('DynamicExtraBlockDetail.tpl',$MODULE)}
    {else}
        {include file=vtemplate_path('StaticExtraBlockDetail.tpl',$MODULE)}
    {/if}
{/strip}