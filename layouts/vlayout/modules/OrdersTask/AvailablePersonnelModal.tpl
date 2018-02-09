<div class="modelContainer">
    <div class="modal-header contentsBackground">
        <button data-dismiss="modal" class="close" title="{vtranslate('CLOSE') }">&times;</button>
        <h4> Available {$RESOURCE_NAME} for {$DATE}</h4>
    </div>
    <div class="modal-body tabbable">
        <div class="container" style="width: 800px; max-height: 500px; overflow-y: scroll;">
            <div class="row-fluid">
                <table class="table table-bordered detailview-table">
                    <tbody>
                        {assign var=TOTAL_HS value=0}
                        {assign var=ROW value=1}
                        <tr>
                            <td class="fieldLabel medium" style="width: 5%;">
                                <label class="muted" style="text-align: center;">#</label>
                            </td>
                            <td class="fieldLabel medium" style="width: 20%;">
                                <label class="muted" style="text-align: center;">Personnel Name</label>
                            </td>
                            <td class="fieldLabel medium" style="width: 20%;">
                                <label class="muted" style="text-align: center;">Primary Role</label>
                            </td>
                            <td class="fieldLabel medium" style="width: 20%;">
                                <label class="muted" style="text-align: center;">Secondary Role(s)</label>
                            </td>
                            <td class="fieldLabel medium" style="width: 10%;">
                                <label class="muted" style="text-align: center;">Hours Available</label>
                            </td>
                            <td class="fieldLabel medium" style="width: 10%;">
                                <label class="muted" style="text-align: center;">Available For Local</label>
                            </td>
                            <td class="fieldLabel medium" style="width: 15%;">
                                <label class="muted" style="text-align: center;">Available For Interestate</label>
                            </td>
                        </tr>
                        {foreach from=$RESOURCES key=ROW_NUMBER item=EMPLOYEE}
                        <tr>
                            <td class="fieldValue medium" style="text-align: center;">
                                <span class="value" data-field-type="string">{$ROW}</span>
                            </td>
                            
                            <td class="fieldValue medium" style="text-align: center;">
                                <span class="value" data-field-type="string">{$EMPLOYEE.fullname}</span>
                            </td>
                            
                            <td class="fieldValue medium" style="text-align: center;">
                                <span class="value" data-field-type="string">{$EMPLOYEE.prole}</span>
                            </td>
                            
                            <td class="fieldValue medium" style="text-align: center;">
                                <span class="value" data-field-type="string">{$EMPLOYEE.srole}</span>
                            </td>
                            
                            <td class="fieldValue medium" style="text-align: center;">
                                <span class="value" data-field-type="string">{$EMPLOYEE.available_hours}</span>
                            </td>
                            
                            <td class="fieldValue medium" style="text-align: center;">
                                <span class="value" data-field-type="string">{$EMPLOYEE.avail_local}</span>
                            </td>
                            
                            <td class="fieldValue medium" style="text-align: center;">
                                <span class="value" data-field-type="string">{$EMPLOYEE.avail_long}</span>
                            </td>
                        </tr>
                        {assign var=TOTAL_HS value=$TOTAL_HS+$EMPLOYEE.available_hours}
                        {assign var=ROW value=$ROW+1}
                        {/foreach}
                        <tr>
                            <td class="fieldLabel medium" colspan="4">
                                <label class="muted pull-right">Total Hours Available</label>
                            </td>
                            <td class="fieldValue medium">
                                <span class="value" data-field-type="string" style="text-align: center;">{$TOTAL_HS}</span>
                            </td>
                            <td colspan="2"></td>
                        </tr>
                    </tbody>
                </table>    
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button data-dismiss="modal" class="close" title="{vtranslate('CLOSE') }">{vtranslate('CLOSE') }</button>
    </div>                    
</div>