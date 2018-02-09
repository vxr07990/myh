{strip}

<table class="table table-bordered equalSplit detailview-table">
    <thead>
    <tr>
        <th class="blockHeader" colspan="4">
            <img class="cursorPointer alignMiddle blockToggle {if !($IS_HIDDEN)} hide {/if} "  src="{vimage_path('arrowRight.png')}" data-mode="hide" data-id={$BLOCK_LIST[$BLOCK_LABEL_KEY]->get('id')}>
            <img class="cursorPointer alignMiddle blockToggle {if ($IS_HIDDEN)} hide {/if}"  src="{vimage_path('arrowDown.png')}" data-mode="show" data-id={$BLOCK_LIST[$BLOCK_LABEL_KEY]->get('id')}>
            &nbsp;&nbsp;Salesperson
        </th>
    </tr>
    </thead>
    <tbody>
    <tr>
        <td class="blockHeader fieldLabel medium" style='width: 16%;'>Salesperson</td>
        <td class="blockHeader fieldLabel medium" style='width: 20%;'>Booking Office</td>
        <td class="blockHeader fieldLabel medium" style='width: 20%;'>Business Line</td>
        <td class="blockHeader fieldLabel medium" style='width: 10%;'>Sales Credit</td>
        <td class="blockHeader fieldLabel medium" style='width: 10%;'>Sales Comm %</td>
        <td class="blockHeader fieldLabel medium" style='width: 10%;'>Effective Date From</td>
        <td class="blockHeader fieldLabel medium" style='width: 10%;'>Effective Date To</td>
    </tr>

    {assign var=COUNTER value=0}

    {foreach item=sales_person_row key=parent_id from=$CURRENT_SALES_PERSONS}
        {assign var=COUNTER value=$COUNTER+1}
        <tr>
            <td style="width:16%">
                <span class="value">{$sales_person_row.salesperson_id}</span>
            </td>
            <td style='width: 20%;'>
                <span class="value">{$sales_person_row.booking_office_id}</span>
            </td>
            <td style='width: 20%;'>
                <span class="value">{vtranslate($sales_person_row.commodity, $MODULE)}</span>
            </td>
            <td style='width: 10%;'>
                <span class="value">{$sales_person_row.sales_credit}</span>
            </td>
            <td style='width: 10%;'>
                <span class="value">{$sales_person_row.sales_comm}%</span>
            </td>
            <td style='width: 10%;'>
                <span class="value">{$sales_person_row.effective_date_from}</span>
            </td>
            <td style='width: 10%;'>
                <span class="value">{$sales_person_row.effective_date_to}</span>
            </td>


        </tr>
    {/foreach}

    </tbody>
</table>
    <br>
{/strip}
