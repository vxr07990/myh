{strip}
    <div class="container-fluid"  id="AsteriskServerDetails">
        <div class="widget_header row-fluid">
            <div class="span8"><h3>Move Easy</h3></div>
            <table class="table table-bordered table-condensed themeTableColor">
                <thead>
                <tr class="blockHeader">
                    <th colspan="2" class="mediumWidthType">
                        <span class="alignMiddle">Move Easy</span>
                    </th>
                </tr>
                </thead>
                    <tr>
                        <td colspan="2">
                            You are assigned to multiple agents, please choose an agent to use below:
                        </td>
                    </tr>
                    {foreach $AGENTS as $AGENT }
                        <tr>
                            <td colspan="2">
                                <a href="index.php?module=MoveEasyIntegration&parent=Settings&view=Index&agentID={$AGENT->id}">{$AGENT->name}</a>
                            </td>
                        </tr>
                    {/foreach}
            </table>
        </div>
    </div>
{/strip}
