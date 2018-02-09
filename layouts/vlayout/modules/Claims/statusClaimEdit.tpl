{strip}

<table name="statusClaimTable" class="table table-bordered blockContainer showInlineTable" style="margin-top:1%;">
    <tbody>
        <tr class="fieldLabel">
            <td colspan="4">
                <button type="button" class="addStatus">+</button>
                <button type="button" class="addStatus" style="clear:right;float:right">+</button>
            </td>
        </tr>
        <tr style="width:100%" class="fieldLabel">
            <td style="text-align:center;margin:auto;width:10%;">
                <input type="hidden" name="numStatus" value="{($STATUS_LIST|@count)}"/>
            </td>
            <td style="text-align:center;margin:auto;width:30%;">Status</td>
            <td style="text-align:center;margin:auto;width:30%;">Reason</td>
            <td style="text-align:center;margin:auto;width:30%;">Effective Date</td>
        </tr>
    {foreach key=ROW_NUM item=STATUS from=$STATUS_LIST}
        <tr style="text-align:center;margin:auto" class="statusRow{$ROW_NUM+1} statusRow">
            <td class="fieldValue" style="text-align:center;margin:auto">
                <input type="hidden" name="statusDbId-{$ROW_NUM+1}" value="{$STATUS['id']}" />
            </td>
            <td class="fieldValue" style="text-align:center;margin:auto">
                <span class="value"> {$STATUS['status']} </span>
            </td>
            <td class="fieldValue" style="text-align:center;margin:auto">
                <span class="value"> {$STATUS['reason']} </span>
            </td>
            <td class="fieldValue" style="text-align:center;margin:auto">
                {$STATUS['effective_date']}
            </td>
        </tr>
    {/foreach}
        <tr class='hide defaultStatus StatusRow'>
            <td class='fieldValue' style="text-align:center;margin:auto">
                <a class="removeStatus">
                    <i title="Delete" class="icon-trash alignMiddle"></i>
                    <input type="hidden" name="statusDbId" value="none" />
                </a>
            </td>
            {assign var=FIELD_MODEL value=$RECORD_STRUCTURE['LBL_STATUS_INFORMATION']['claims_status_statusgrid']}
            <td class="fieldValue" style="text-align:center;margin:auto" >
                <div class="row-fluid">
                    <span class="span10">
                        {include file=vtemplate_path($FIELD_MODEL->getUITypeModel()->getTemplateName(),$MODULE) BLOCK_FIELDS=$BLOCK_FIELDS}
                    </span>
                </div>
            </td>
            {assign var=FIELD_MODEL value=$RECORD_STRUCTURE['LBL_STATUS_INFORMATION']['claims_reason_statusgrid']}
            <td class="fieldValue" style="text-align:center;margin:auto" >
                <div class="row-fluid">
                    <span class="span10">
                        {include file=vtemplate_path($FIELD_MODEL->getUITypeModel()->getTemplateName(),$MODULE) BLOCK_FIELDS=$BLOCK_FIELDS}
                    </span>
                </div>
            </td>
            {assign var=FIELD_MODEL value=$RECORD_STRUCTURE['LBL_STATUS_INFORMATION']['claims_effective_date_statusgrid']}
            <td class="fieldValue" style="text-align:center;margin:auto" >
                <div class="row-fluid">
                    <span class="span10">
                        {include file=vtemplate_path($FIELD_MODEL->getUITypeModel()->getTemplateName(),$MODULE) BLOCK_FIELDS=$BLOCK_FIELDS}
                    </span>
                </div>
            </td>
        </tr>
    </tbody>
</table>
{/strip}