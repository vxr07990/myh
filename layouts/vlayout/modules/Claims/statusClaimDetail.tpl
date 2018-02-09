{strip}
{if $STATUS_LIST|@count gt 0}
    <table class="table table-bordered detailview-table" style="margin-top:1%;">
        <thead>
            <tr style="width:100%" class="fieldLabel">
                <td style="text-align:center;margin:auto;width:25%;">Status</td>
                <td style="text-align:center;margin:auto;width:50%;">Reason</td>
                <td style="text-align:center;margin:auto;width:25%;">Effective Date</td>
            </tr>
        </thead>
        <tbody>
            {foreach key=ROW_NUM item=STATUS from=$STATUS_LIST}
                <tr style="text-align:center;margin:auto" class="statusRow{$ROW_NUM+1}">
                    <td style="text-align:center;margin:auto"><span class="value"> {$STATUS['status']}</span></td>
                    <td style="text-align:center;margin:auto"><span class="value"> {$STATUS['reason']}</span></td>
                    <td style="text-align:center;margin:auto">{$STATUS['effective_date']}</td>
                </tr>
            {/foreach}
        </tbody>
    </table>
{/if}
{/strip}