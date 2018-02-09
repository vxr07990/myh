{strip}
    <table class="table table-bordered blockContainer showInlineTable equalSplit" name="AgentCompensationItemsTable">
        <thead>
        <tr>
            <th class="blockHeader" colspan="4">{vtranslate('LBL_AGENTCOMPENSATION_DISTRIBUTION', 'AgentCompensationGroup')}</th>
        </tr>
        </thead>
    </table>
    <div class="AgentCompensationItemsList" data-rel-module="AgentCompensationItems">
        {include file=vtemplate_path('BlockEditFields.tpl','AgentCompensationItems') ITEM_RECORD_MODEL=$ITEM_RECORD_MODEL FIELDS_LIST=$FIELDS_LIST}
    </div>
{/strip}