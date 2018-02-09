
{strip}
{if $SERVICE_PROVIDER_RESPO}
    <table class="table table-bordered detailview-table" style="margin-top:1%;">
            <thead>
                    <tr>
                            <th class="blockHeader" colspan="6">
                                            {if $BLOCK_LABEL_KEY}
                                                    <img class="cursorPointer alignMiddle blockToggle {if !($IS_HIDDEN)} hide {/if} "  src="{vimage_path('arrowRight.png')}" data-mode="hide" data-id={$BLOCK_LIST[$BLOCK_LABEL_KEY]->get('id')}>
                                                    <img class="cursorPointer alignMiddle blockToggle {if ($IS_HIDDEN)} hide {/if}"  src="{vimage_path('arrowDown.png')}" data-mode="show" data-id={$BLOCK_LIST[$BLOCK_LABEL_KEY]->get('id')}>
                                            {/if}
                                            &nbsp;&nbsp;{vtranslate('LBL_SERVICE_PROVIDER_RESPONSIBILITY', 'Claims')}
                            </th>
                    </tr>
            </thead>
            <tbody {if $IS_HIDDEN} class="hide" {/if}>
                <tr style="width:100%" class="fieldLabel">
                        <td style="text-align:center;margin:auto;width:20%;">Agent Type</td>
                        <td style="text-align:center;margin:auto;width:20%;">Participating Agent</td>
                        <td style="text-align:center;margin:auto;width:20%;">Service Provider</td>
                        <td style="text-align:center;margin:auto;width:20%;">I Code</td>
                        <td style="text-align:center;margin:auto;width:20%;">% of Responsibility</td>
                        <td style="text-align:center;margin:auto;width:20%;">Responsibility Amount</td>
                </tr>
                    {assign var="tpercentage" value=0}
                    {assign var="tamount" value=0}
                    {foreach key=ROW_NUM item=PARTICIPANT from=$SERVICE_PROVIDER_LIST}
                            <tr style="text-align:center;margin:auto" class="participantRow{$ROW_NUM+1}">
                                    <td style="text-align:center;margin:auto">
                                            <span class="value">
                                                    {$PARTICIPANT['agent_type']}
                                            </span>
                                    </td>
                                    <td style="text-align:center;margin:auto">
                                            <span class="value">
                                                    {$PARTICIPANT['agent_name']}
                                            </span>
                                    </td>
                                    <td style="text-align:center;margin:auto">
                                            <span class="value">
                                                    {$PARTICIPANT['vendors_name']}
                                            </span>
                                    </td>
                                    <td style="text-align:center;margin:auto">
                                                    {$PARTICIPANT['icode']}
                                    </td>
                                    <td style="text-align:center;margin:auto">
                                                    {$PARTICIPANT['respon_percentage']}
                                    </td>
                                    <td style="text-align:center;margin:auto">
                                            <span class="value">
                                                    {$PARTICIPANT['respon_amount']}
                                            </span>
                                    </td>
                            </tr>
                            {assign var="tpercentage" value=$tpercentage+$PARTICIPANT['respon_percentage']}
                            {assign var="tamount" value=$tamount+$PARTICIPANT['respon_amount']}
                    {/foreach}
                    <tr style="text-align:center;margin:auto" class="totalsRow">
                            <td colspan="4" style="text-align:center;margin:auto">
                            </td>
                            <td style="text-align:center;margin:auto">
                                <span class="value">
                                    <b> Responsibility Total: {$tpercentage}</b>
                                </span>
                            </td>
                            <td style="text-align:center;margin:auto">
                                <span class="value">
                                    <b> Amount Total: {$tamount} </b>
                                </span>
                            </td>
                    </tr>
            </tbody>
    </table>
{/if}
{/strip}