{strip}
    {if !$EDIT_VIEW}
        <span class='value' data-field-type='picklist'>Select an Option</span>
        <span class='hide {*OLD SECURITIES{if $EXTRA_PERMISSIONS[0] === TRUE}*}edit{*{/if}*}'>
    {/if}
    {* INSTANCE NAME: {getenv('INSTANCE_NAME')}<br>
    MAX TARIFFS: {$MAX_TARIFFS|@debug_print_var}<br
    {$LOCAL_TARIFFS|@debug_print_var}<br> *}
    {if getenv('INSTANCE_NAME') eq 'sirva'}{assign var=COMBINE_TARIFF value=$INTRASTATE_TARIFFS}{else}{assign var=COMBINE_TARIFF value=$LOCAL_TARIFFS|array_merge:$INTRASTATE_TARIFFS}{/if}
    <select class='chzn-select' name='local_tariff'>
		<option value='0' selected>Select an Option</option>
        {foreach item=TARIFF key=INDEX from=$COMBINE_TARIFF}
            <option  class="{if $TARIFF->intrastate eq true}intrastateTariff {if $TARIFF->intraInterstate}intraInterstate {else}intraLocal {/if}{elseif $TARIFF->local eq true}localTariff {/if}" value='{$TARIFF->get('id')}'>{$TARIFF->get('tariff_name')}</option>
        {/foreach}
	</select>
    {if !$EDIT_VIEW}
        <input type='hidden'
               class='fieldname'
               value='local_tariff'
               data-prev-value='{if !empty($EFFECTIVE_TARIFF)}{$EFFECTIVE_TARIFF}{else}Select an Option{/if}'/>
        </span>
    {/if}
{/strip}
