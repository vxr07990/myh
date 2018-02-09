<div class="modelContainer" data-orderstaskid="{$RECORD_ID}">
    <div class="modal-header contentsBackground">
        <button data-dismiss="modal" class="close" title="{vtranslate('CLOSED') }">&times;</button>
        <h4> {vtranslate('Client Information', $MODULENAME)}</h4>
    </div>
    <div class="modal-body tabbable">
        <div class="container" style="width:100%;">
            <div class="row-fluid">
                <table class="table table-bordered equalSplit detailview-table">
                    <tbody>
                        <tr>
                            <td class="fieldLabel medium">
                                <label class="muted pull-right marginRight10px">Business Line</label>
                            </td>
                            <td class="fieldValue medium">
                                <span class="value" data-field-type="string">{$BUSINESS_LINE}</span>
                            </td>
                            <td class="fieldLabel medium">
                                <label class="muted pull-right marginRight10px">Dispatch Status</label>
                            </td>
                            <td class="fieldValue medium">
                                <span class="value" data-field-type="string">{$DISPATCH_STATUS}</span>
                            </td>
                        </tr>
                        <tr>
                            <td class="fieldLabel medium">
                                <label class="muted pull-right marginRight10px">First Name</label>
                            </td>
                            <td class="fieldValue medium">
                                <span class="value" data-field-type="string">{$CLIENT_FIRST_NAME}</span>
                            </td>
                            <td class="fieldLabel medium">
                                <label class="muted pull-right marginRight10px">Last Name</label>
                            </td>
                            <td class="fieldValue medium">
                                <span class="value" data-field-type="string">{$CLIENT_LAST_NAME}</span>
                            </td>
                        </tr>
                        <tr>
                            <td class="fieldLabel medium">
                                <label class="muted pull-right marginRight10px">Office Phone</label>
                            </td>
                            <td class="fieldValue medium">
                                <span class="value" data-field-type="string">{$CLIENT_OFFICE_PHONE}</span>
                            </td>
                            <td class="fieldLabel medium">
                                <label class="muted pull-right marginRight10px">Home Phone</label>
                            </td>
                            <td class="fieldValue medium" id="SalesOrder_detailView_fieldValue_subject">
                                <span class="value" data-field-type="string">{$CLIENT_HOME_PHONE}</span>
                            </td>
                        </tr>
                        <tr>
                            <td class="fieldLabel medium">
                                <label class="muted pull-right marginRight10px">Mobile Phone</label>
                            </td>
                            <td class="fieldValue medium">
                                <span class="value" data-field-type="string">{$CLIENT_MOBILE_PHONE}</span>
                            </td>
                            <td class="fieldLabel medium">
                                <label class="muted pull-right marginRight10px">Other Phone</label>
                            </td>
                            <td class="fieldValue medium">
                                <span class="value phone-field" data-field-type="phone">{$CLIENT_OTHER_PHONE}</span>
                            </td>
                        </tr>
                        <tr>
                            <td class="fieldLabel medium">
                                <label class="muted pull-right marginRight10px">Check Call</label>
                            </td>
                            <td class="fieldValue medium">
                                <div class="row-fluid">
                                    <span class="span10">
                                        <select class="chzn-select" data-orderstaskid="{$RECORD_ID}" id="check_call">
                                            <option value="--">--</option>
                                            {foreach item=VALUE key=ID from=$CHECK_CALL_PICKLIST}
                                                <option value="{$ID}" {if $CHECK_CALL_SELECTED eq $VALUE}selected{/if}>{$VALUE}</option>
                                            {/foreach}
                                        </select>
                                    </span>
                                </div>        
                            </td>
                            <td class="fieldLabel medium"><label class="muted pull-right marginRight10px"> </label></td>
                            <td class="fieldValue medium"> </td>
                        </tr>
                    </tbody>
                </table>    
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button class="btn btn-success" id="saveCheckCall" type="submit" name="saveButton"><strong>{vtranslate('Save', $MODULENAME) }</strong></button>
    </div>                    
</div>