

{strip}
    <table class="table table-bordered blockContainer showInlineTable equalSplit" name="MenuGroupsTable">
        <thead>
        <tr>
            <th class="blockHeader" colspan="4">{vtranslate('LBL_MENUGROUPS', 'MenuGroups')}</th>
        </tr>
        </thead>
        <tbody>
        <tr class="fieldLabel">
            <td colspan="4">
                <input type="hidden" name="numMapping" value="{($MENUGROUPS_LIST|@count)}"/>
                <button type="button" class="addMenuGroups" >+</button>
                <button type="button" class="addMenuGroups" style="clear:right;float:right">+</button>
            </td>
        </tr>
        </tbody>
    </table>
    <div class="MenuGroupsList" data-rel-module="MenuGroups">
        {foreach from=$MENUGROUPS_LIST item=MENUGROUPS_RECORD_MODEL name=related_records_block key = MENUGROUPS_ID}
            {include file=vtemplate_path('BlockEditFields.tpl','MenuGroups') MENUGROUPS_ID=$MENUGROUPS_ID FIELDS_LIST=$MENUGROUPS_BLOCK_FIELDS MENUGROUPS_RECORD_MODEL=$MENUGROUPS_RECORD_MODEL ROWNO=$smarty.foreach.related_records_block.iteration BLOCK_TITLE=$BLOCK_TITLE}
        {/foreach}
    </div>

{/strip}