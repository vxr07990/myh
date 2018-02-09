
<div class="actualResourceContainer">
    <div>
        <h4> {vtranslate('LBL_ACTUAL_RESOURCE_TITLE', $MODULENAME)}</h4>
    </div><div class="tabbable">
        <p style="">{vtranslate('LBL_ACTUAL_RESOURCE_EXPLAIN', $MODULENAME)}</p>
        <table id="actualsResourceTable" class="table table-bordered listViewEntriesTable" style="width: 80%;margin: auto;margin-bottom: 2%;margin-top: 2%;"><tbody>
            <thead>
                <tr class="listViewHeaders">
                    <th>{vtranslate('SELECT', $MODULENAME)}</th>
                    <th>{vtranslate('TASK_NAME', $MODULENAME)}</th>
                    <th>{vtranslate('EMPLOYEE_NAME', $MODULENAME)}</th>
                    <th>{vtranslate('ACTUAL_DATE', $MODULENAME)}</th>
                    <th>{vtranslate('ACTUAL_START_HOUR', $MODULENAME)}</th>
                    <th>{vtranslate('ACTUAL_END_HOUR', $MODULENAME)}</th>
                </tr>
            </thead>

            {foreach item=EMPLOYEE_DATA key=EMPLOYEE_ID from=$EMPLOYEES}

                <tr class="listViewEntries">
                    <td><input type="checkbox" {$EMPLOYEE_DATA.checked} name="check_{$EMPLOYEE_ID}"/></td>
                    <td>{$EMPLOYEE_DATA.taskname}</td>
                    <td>{$EMPLOYEE_DATA.employeename}</td>
                    <td><div class="row-fluid input-append date"><input id="actual-date{$EMPLOYEE_ID}" type="text" class="span8 dateField" name="actualdate_{$EMPLOYEE_ID}" data-date-format="{$current_user->date_format}" value="{$EMPLOYEE_DATA.startdate}"><span class="add-on"><i class="icon-calendar"></i></span></div></td>
                    <td><div class="input-append time"><input id="start_hour{$EMPLOYEE_ID}" type="text" class="timepicker-default input-small"  name="start_hour_{$EMPLOYEE_ID}" value="{$EMPLOYEE_DATA.start_hour}"><span class="add-on cursorPointer"><i class="icon-time"></i></span></div></td>
                    <td><div class="input-append time"><input id="end_hour{$EMPLOYEE_ID}" type="text"  class="timepicker-default input-small"  name="end_hours_{$EMPLOYEE_ID}" value="{$EMPLOYEE_DATA.end_hour}"><span class="add-on cursorPointer"><i class="icon-time"></i></span></div></td>

                </tr>
               
            {/foreach}
                <tr class="last">
                    <td style="padding:1%;" colspan="6"> <span class="btn" id="addRow"><strong>{vtranslate('LBL_ADD_ROW', $MODULENAME) }</strong></span></td>
                </tr>

            </tbody></table>
                <input id="employee_count" type="hidden" value="{$EMPLOYEE_COUNT}">
        
       
    </div>
    <div class="pull-right" style="margin-top:2%; margin-bottom: 2%;">
        <span class="btn btn-success" id="saveTimeSheets" name="saveButton"><strong>{vtranslate('LBL_SAVE_ACTUALS', $MODULENAME) }</strong></span>
        <a class="cancelLink" type="reset" data-dismiss="modal">{vtranslate('LBL_CANCEL')}</a>
    </div>

    <input id="servicename" type="hidden" value="{$SERVICENAME}">
    <input id="start-date" type="hidden" value="{$START_DATE}">
    <input id="start-hour" type="hidden" value="{$START_HOUR}">
    <input id="end-hour" type="hidden" value="{$END_HOUR}">
    <input id="date-format" type="hidden" value="{$current_user->date_format}">

</div>

<script type="text/javascript">
    {literal}
            app.registerEventForDatePickerFields();
    {/literal}   
</script>    