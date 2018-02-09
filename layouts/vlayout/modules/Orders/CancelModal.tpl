<div class="modelContainer" data-ordersid="{$RECORD_ID}">
    <div class="modal-header contentsBackground">
        <button data-dismiss="modal" class="close" title="{vtranslate('CLOSED') }">&times;</button>
        <input type="hidden" id="modal_action" value="{$MODAL_ACTION}">
        <h4> {if $MODAL_ACTION eq 'cancel'}{vtranslate('LBL_CANCEL_ORDER', $MODULENAME)}{else}{vtranslate('LBL_UNCANCEL_ORDER', $MODULENAME)}{/if}</h4>
    </div>
    <div class="modal-body tabbable">
        <div class="container" style="width:100%;">
            <div class="row-fluid">
                <input type="hidden" value="{$RECORD_ID}" id="modalordersid">
                <table class="table table-bordered equalSplit detailview-table">
                    <tbody>
                        <tr>
                            <td class="fieldLabel medium">
                                <label class="muted pull-right marginRight10px">User Name</label>
                            </td>
                            <td class="fieldValue medium">
                                <input type="hidden" id="modaluserid" value="{$CURRENT_USER_ID}">
                                <span class="value" data-field-type="string">{$CURRENT_USER_FULL_NAME}</span>
                            </td>
                            <td class="fieldLabel medium">
                                <label class="muted pull-right marginRight10px">Date/Time</label>
                            </td>
                            <td class="fieldValue medium">
                                <input type="hidden" id="modaldatetime" value="{$CURRENT_DATE_TIME}">
                                <span class="value" data-field-type="string">{$CURRENT_DATE_TIME}</span>
                            </td>
                        </tr>
                        <tr>
                            <td class="fieldLabel medium">
                                <label class="muted pull-right marginRight10px">{if $MODAL_ACTION eq 'cancel'}Cancel Reason{else}Uncancel Reason{/if}</label>
                            </td>
                            {if $MODAL_ACTION eq 'cancel'}
                                <td class="fieldValue medium">  
                                    <div class="row-fluid">
                                        <span class="span10">
                                            <select class="chzn-select" data-ordersid="{$RECORD_ID}" id="cancel_reason">
                                                <option value="--">--</option>
                                                {foreach item=VALUE from=$CANCEL_REASON_LIST}
                                                    <option value="{$VALUE}">{$VALUE}</option>
                                                {/foreach}
                                            </select>
                                        </span>
                                    </div>   
                                </td>
                                <td class="fieldLabel medium"><label class="muted pull-right marginRight10px"> </label></td>
                                <td class="fieldValue medium"> </td>
                            {else}
                                <td colspan="3" class="fieldValue medium">
                                    <div class="row-fluid">
                                        <span class="span10">
                                            <textarea id="uncancel_reason" class="span11"></textarea>
                                        </span>
                                    </div> 
                                </td>
                            {/if}
                        </tr>
                    </tbody>
                </table>    
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button class="btn btn-success" id="saveCancelOrder" type="submit" name="saveCancelOrder"><strong>{vtranslate('Save', $MODULENAME) }</strong></button>
    </div>                    
</div>