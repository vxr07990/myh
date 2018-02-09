{strip}
    <table name='MoveRolesTable' class='table table-bordered blockContainer showInlineTable'>
        <thead>
        <tr>
            <th class='blockHeader' colspan='9'>{vtranslate('LBL_MOVEROLES_INFORMATION', 'MoveRoles')}</th>
        </tr>
        </thead>
        {*assign var=USE_STATUS value=true*}{*Change this to true to bring back the status column when messaging has been made to work*}
        <tbody>
        <tr style="width:100%" class="fieldLabel">
            <td style="text-align:center;margin:auto;width:12%;"><b>Role</b></td>
            <td style="text-align:center;margin:auto;width:12%;"><b>Personnel</b></td>
        </tr>
        {assign var=ROLE_MODEL value=$MOVEROLES_MODULE_MODEL->getField("moveroles_role")}
        {assign var=EMPLOYEES_MODEL value=$MOVEROLES_MODULE_MODEL->getField("moveroles_employees")}

        {foreach key=ROW_NUM item=MOVEROLES from=$MOVEROLES_LIST}
            <tr style="margin:auto" class="moverolesRow{$ROW_NUM+1} moverolesRow">
                <td class="fieldValue typeCell" style="text-align:center;margin:auto">
                    <div class="row-fluid">
                        {assign var=ROLE_MODEL value=$ROLE_MODEL->set('fieldvalue',$MOVEROLES['moveroles_role'])}
                        <span class="span10">
							{$ROLE_MODEL->getDisplayValue($ROLE_MODEL->get('fieldvalue'))}
						</span>
                    </div>
                </td>

                <td class="fieldValue typeCell" style="text-align:center;margin:auto">
                    <div class="row-fluid">
                        {assign var=EMPLOYEES_MODEL value=$EMPLOYEES_MODEL->set('fieldvalue',$MOVEROLES['moveroles_employees'])}
                        <span class="span10">
							{$EMPLOYEES_MODEL->getDisplayValue($EMPLOYEES_MODEL->get('fieldvalue'))}
						</span>
                    </div>
                </td>
            </tr>
        {/foreach}
        </tbody>
    </table>
    <br>
{/strip}