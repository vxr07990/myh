{strip}
    <table name='MoveRolesTable' class='table table-bordered blockContainer showInlineTable'>
        <thead>
        <tr>
            <th class='blockHeader' colspan='9'>{vtranslate('LBL_MOVEROLES_INFORMATION', 'MoveRoles')}</th>
        </tr>
        </thead>
        {*assign var=USE_STATUS value=true*}{*Change this to true to bring back the status column when messaging has been made to work*}
        <tbody>
        <tr class="fieldLabel">
            <td colspan="9">
                <button type="button" class="addMoveRoles">+</button>
                {* Amin made this button it makes no sense leaving it here until I get word from higher up to get rid of it
                <button type="button" class="hideRemovedCapacityCalendarCounter">Toggle Removed CapacityCalendarCounters</button>
                *}
                <button type="button" class="addMoveRoles" style="clear:right;float:right">+</button>
            </td>
        </tr>
        <tr style="width:100%" class="fieldLabel">
            <td style="text-align:center;margin:auto;width:4%;">
                <input type="hidden" name="numAgents" value="{($MOVEROLES_LIST|@count)}"/></td>
            <td style="text-align:center;margin:auto;width:12%;">Role <span class="redColor">*</span></td>
            <td style="text-align:center;margin:auto;width:12%;">Personnel <span class="redColor">*</span></td>
        </tr>
        {assign var=ROLE_MODEL value=$MOVEROLES_MODULE_MODEL->getField("moveroles_role")}
        {assign var=EMPLOYEES_MODEL value=$MOVEROLES_MODULE_MODEL->getField("moveroles_employees")}

        <tr style="margin:auto"class="defaultMoveRoles moverolsRow hide">
            <td class="fieldValue" style="margin:auto">
                <i title="Delete" class="icon-trash removeMoveRoles"></i>
                <input type="hidden" class="default" name="moverolesidId" value="none" />
            </td>

            <td class="fieldValue" style="margin:auto">
                <div class="row-fluid">
						<span class="span10">
							{include file=vtemplate_path($ROLE_MODEL->getUITypeModel()->getTemplateName(),'MoveRoles') FIELD_MODEL=$ROLE_MODEL  DEFAULT_CHZN=1}
						</span>
                </div>
            </td>

            <td class="fieldValue" style="margin:auto">
                <div class="row-fluid">
						<span class="span10">
							{include file=vtemplate_path($EMPLOYEES_MODEL->getUITypeModel()->getTemplateName(),'MoveRoles') FIELD_MODEL=$EMPLOYEES_MODEL DEFAULT_CHZN=1}
						</span>
                </div>
            </td>

        </tr>
        {foreach key=ROW_NUM item=MOVEROLES from=$MOVEROLES_LIST}
            <tr style="margin:auto" class="moverolesRow{$ROW_NUM+1} moverolesRow">
                <td class="fieldValue" style="margin:auto">
                    <input type="hidden" name="moverolesidId" value="{$MOVEROLES['moverolesid']}" />
                    <input type="hidden" class="default" name="moverolesDelete" value="" />
                    <input type="hidden" class="row_num" name="row_num" value="{$ROW_NUM+1}" />
                    <i title="Delete" class="icon-trash removeMoveRoles"></i>
                </td>
                <td class="fieldValue typeCell" style="margin:auto">
                    <div class="row-fluid">
                        {assign var=ROLE_MODEL value=$ROLE_MODEL->set('fieldvalue',$MOVEROLES['moveroles_role'])}
                        <span class="span10">
									{include file=vtemplate_path($ROLE_MODEL->getUITypeModel()->getTemplateName(),'MoveRoles') FIELD_MODEL=$ROLE_MODEL }
								</span>
                    </div>
                </td>
                <td class="fieldValue typeCell" style="margin:auto">
                    <div class="row-fluid">
                        {assign var=EMPLOYEES_MODEL value=$EMPLOYEES_MODEL->set('fieldvalue',$MOVEROLES['moveroles_employees'])}
                        <span class="span10">
									{include file=vtemplate_path($EMPLOYEES_MODEL->getUITypeModel()->getTemplateName(),'MoveRoles') FIELD_MODEL=$EMPLOYEES_MODEL }
								</span>
                    </div>
                </td>

            </tr>
        {/foreach}
        </tbody>
    </table>
    <br>
{/strip}