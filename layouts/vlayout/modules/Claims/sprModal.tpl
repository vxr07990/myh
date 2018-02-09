<div class="modelContainer">
    <input type="hidden" value="{$ITEMS_LIST}" id="items">
    <div class="modal-header contentsBackground">
        <button data-dismiss="modal" class="close" title="{vtranslate('CLOSED') }">&times;</button>
        <h4> Service Provider Responsability</h4>
    </div>
    <div class="modal-body tabbable">
        <div class="container" style="width:100%;">
            <div class="row-fluid sprmodal">
                <input type="hidden" value="100" id="max-perc-available">
                <table name='servicePRTable' class='table table-bordered blockContainer showInlineTable' style="margin-top:1%;">
                        <thead>
                                <tr>
                                    <th class='blockHeader' colspan='6'>{vtranslate('LBL_SERVICE_PROVIDER_RESPONSIBILITY', 'Claims')}</th>
                                </tr>
                        </thead>
                        <tbody>
                                <tr colspan="6" class="fieldLabel">
                                    <td colspan="6">
                                            <button type="button" class="addParticipant">+</button>
                                            <button type="button" class="addParticipant" style="clear:right;float:right">+</button>
                                    </td>
                                </tr>
                                <tr style="width:100%" colspan="6" class="fieldLabel">
                                        <td style="text-align:center;margin:auto;width:10%;"></td>
                                        <td style="text-align:center;margin:auto;width:25%;">Participating Agent</td>
                                        <td style="text-align:center;margin:auto;width:25%;">Service Provider</td>
                                        <td style="text-align:center;margin:auto;width:20%;">% of Responsibility</td>
                                        <!--<td style="text-align:center;margin:auto;width:20%;">Responsibility Amount</td>-->
                                </tr>
                                <tr style="text-align:center;margin:auto"class="defaultParticipant participantRow hide">
                                                <td class="fieldValue" style="text-align:center;margin:auto">
                                                        <i title="Delete" class="icon-trash removeParticipant"></i>
                                                </td>
                                                <td class="fieldValue" style="text-align:center;margin:auto">
                                                    <input type="hidden" value="" name="participantAgentID">
                                                    <input type="hidden" value="" name="participantAgentName">
                                                    <select name="participantAgent" class="modalParticipantAgent">
                                                        <option value="null">Select Agent</option>
                                                        {foreach key=KEY item=PARTICIPANT from=$PARTICIPANT_AGENTS}
                                                            <option value="{$PARTICIPANT.id}">{$PARTICIPANT.agent}</option>
                                                        {/foreach}
                                                    </select>
                                                </td>
                                                <td class="fieldValue" style="text-align:center;margin:auto">
                                                    <input type="hidden" value="" name="serviceProviderID">
                                                    <input type="hidden" value="" name="serviceProviderName">
                                                    <select name="serviceProvider" class="modalServiceProvider">
                                                        <option value="null">Select Service Provider</option>
                                                        {foreach key=KEY item=SERVICE_PROVIDER from=$SERVICE_PROVIDERS}
                                                            <option value="{$SERVICE_PROVIDER.id}">{$SERVICE_PROVIDER.vendor}</option>
                                                        {/foreach}
                                                    </select>
                                                </td>
                                                <td class="fieldValue" style="text-align:center;margin:auto">
                                                        {$LOCALFIELDINFO.mandatory = true}
                                                        {$LOCALFIELDINFO.type = 'string'}
                                                        {$INFO = Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($LOCALFIELDINFO))}
                                                        <input type="text" class="default change respon_percentage percentage" name="respon_percentage" value="" />
                                                </td>
                                                {*<td class="fieldValue" style="text-align:center;margin:auto">
                                                        {$LOCALFIELDINFO.mandatory = true}
                                                        {$LOCALFIELDINFO.type = 'currency'}
                                                        {$INFO = Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($LOCALFIELDINFO))}
                                                        <input type="text" class="default change respon_amount" name="respon_amount" value="" />
                                                </td>*}
                                        </tr>
                                        <tr style="text-align:center;margin:auto" class="totalsRow">
                                        <!--        <td colspan="3" style="text-align:center;margin:auto">
                                                </td>
                                                <td style="text-align:center;margin:auto">
                                                    <span class="value sprtpercentage">
                                                        <b>Responsibility Total: 0</b>
                                                    </span>
                                                </td>
                                                <td style="text-align:center;margin:auto">
                                                    <input type="hidden" id="sprtamount" value="0">
                                                        <span class="value sprtamount">
                                                            <b>Amount Total: 0</b>
                                                        </span>
                                                </td>-->
                                        </tr>
                        </tbody>
                </table>            
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button class="btn btn-success" id="saveModalSPR" type="submit" name="saveModalSPR"><strong>{vtranslate('Save', $MODULENAME) }</strong></button>
    </div>     
</div>