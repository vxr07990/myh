<style>
    {literal}
        .visible{
            display:table-row;
        }
        .not_visible{
            display:none;
        }
        #modalOverflow {
            left: 15%;
            right: 15%;
        }
        #modalOverflow > .modal-header {
            height: 2em;
        }
        #modalOverflow > .modal-body {
            background-color: #fff;
            max-height: calc(100vh - 210px);
            overflow-y: auto;
            overflow-x: auto;
        }
        .modal-footer {
            position: absolute;
            bottom: 0;
            right: 0;
            left: 0;
            height: 2em;
        }
        table.dispatchremarks{
            margin-bottom: 7%;
        }
    {/literal}
</style>
<div class="modelContainer" id="modalOverflow" style="width: 1200px; max-width: 1200px;">
    <input type="hidden" value="{$ORDERID}" id="order_id_hidden">
    <div class="modal-header contentsBackground">
        <button data-dismiss="modal" class="close" title="{vtranslate('CLOSED') }">&times;</button>
        <h4>Create Order Overflow</h4>
    </div>
    <div class="modal-body tabbable">
        <div>
            <table class="table table-bordered equalSplit detailview-table">
                <thead>
                    <tr>
                        <th class="blockHeader" colspan="4">Order Overflow Information</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="fieldLabel medium">
                            <label class="muted pull-right marginRight10px">Estimated Weight</label>
                        </td>
                        <td class="fieldValue medium">
                            <div class="row-fluid">
                                <span class="span10">
                                    <input type="text" class="input-large" name="estimated_weight" value="{$ORDEREWEIGHT}" placeholder="Estimated Weight" autocomplete="off" disabled>
                                </span>
                            </div>
                        </td>
                        <td class="fieldLabel medium">
                            <label class="muted pull-right marginRight10px">O/F Weight</label>
                        </td>
                        <td class="fieldValue medium">
                            <div class="row-fluid">
                                <span class="span10">
                                    <input type="text" class="input-large" name="of_weight" value="" placeholder="O/F Weight" autocomplete="off">
                                </span>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td class="fieldLabel medium">
                            <label class="muted pull-right marginRight10px">Main Order %</label>
                        </td>
                        <td class="fieldValue medium">
                            <div class="row-fluid">
                                <span class="span10">
                                    <input type="text" class="input-large" name="main_order_percentage" value="" placeholder="Main Order %" autocomplete="off">
                                </span>
                            </div>
                        </td>
                        <td class="fieldLabel medium">
                            <label class="muted pull-right marginRight10px">O/F Order %</label>
                        </td>
                        <td class="fieldValue medium">
                            <div class="row-fluid">
                                <span class="span10">
                                    <input type="text" class="input-large" name="of_order_percentage" value="" placeholder="O/F Order %" autocomplete="off">
                                </span>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td class="fieldLabel medium">
                            <label class="muted pull-right marginRight10px">Main Order Linehaul</label>
                        </td>
                        <td class="fieldValue medium">
                            <div class="row-fluid">
                                <span class="span10">
                                    <input type="text" class="input-large" name="main_order_linehaul" value="{$ORDERLINEHAUL}" placeholder="Main Order Linehaul" autocomplete="off" disabled>
                                </span>
                            </div>
                        </td>
                        <td class="fieldLabel medium">
                            <label class="muted pull-right marginRight10px">O/F Order Linehaul</label>
                        </td>
                        <td class="fieldValue medium">
                            <div class="row-fluid">
                                <span class="span10">
                                    <input type="text" class="input-large" name="of_order_linehaul" value="" placeholder="O/F Order Linehaul" autocomplete="off">
                                </span>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td class="fieldLabel medium" id="Orders_detailView_fieldLabel_origin_address1">
                            <label class="muted pull-right marginRight10px">Main Order Cube</label>
                        </td>
                        <td class="fieldValue medium">
                            <div class="row-fluid">
                                <span class="span10">
                                    <input type="text" class="input-large" name="main_order_cube" value="{$ORDERCUBE}" placeholder="Main Order Cube" autocomplete="off" disabled>
                                </span>
                            </div>
                        </td>
                        <td class="fieldLabel medium" id="Orders_detailView_fieldLabel_destination_address1">
                            <label class="muted pull-right marginRight10px">O/F Order Cube</label>
                        </td>
                        <td class="fieldValue medium">
                            <div class="row-fluid">
                                <span class="span10">
                                    <input type="text" class="input-large" name="of_order_cube" value="" placeholder="O/F Order Cube" autocomplete="off">
                                </span>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
            <br>
            <table name="ordersVehiclesTable" class="table table-bordered detailview-table">
                <thead>
                    <tr>
                        <th class="blockHeader" colspan="7">Vehicles <button type="button" name="addVehicle" style="float: right;" onclick="Orders_Detail_Js.addVehicleButton();">+</button></th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="do-not-copy-me">
                        <td class="fieldLabel" style="width:5%;text-align: center;">Delete</td>
                        <td class="fieldLabel" style="width:10%;text-align: center;">Make</td>
                        <td class="fieldLabel" style="width:10%;text-align: center;">Model</td>
                        <td class="fieldLabel" style="width:10%;text-align: center;">Year</td>
                        <td class="fieldLabel" style="width:10%;text-align: center;">VIN</td>
                        <!--<td class="fieldLabel" style="width:10%;text-align: center;">Flat Rate</td>-->
                        <td class="fieldLabel" style="width:10%;text-align: center;">Type</td>
                        <td class="fieldLabel" style="width:10%;text-align: center;">Rating Type</td>
                    </tr>
                    <tr class="default hide">
                        <td style="text-align:center;margin:auto;width:5%;">
                                <span class="span2">
                                    <a class="deleteVehicleButton" onclick="Orders_Detail_Js.deleteVehicleRow(this);"><i title="Delete" class="icon-trash"></i></a>
                                </span>
                        </td>
                        <td class="fieldValue" style="width:10%;">
                                    <input type="text" class="input-medium" name="vehicle_make0" value="" autocomplete="off">
                        </td>
                        <td class="fieldValue" style="width:10%;">
                                    <input type="text" class="input-medium" name="vehicle_model0" value="" autocomplete="off">
                        </td>
                        <td class="fieldValue" style="width:10%;">
                                    <input type="text" class="input-medium" name="vehicle_year0" value="" autocomplete="off" pattern="[0-9]{4}">
                        </td>
                        <td class="fieldValue" style="width:10%;">

                                    <input type="text" class="input-medium" name="vehicle_vin0" value="" autocomplete="off">

                        </td>

                        <td style="margin:auto;width:10%;">

                                    <select class="" name="vehicletranstype0">
                                        {foreach item=DATA key=ID from=$TYPE_PICKLIST}
                                            <option value="{$DATA.id}">{$DATA.value}</option>
                                        {/foreach}
                                    </select>
                        </td>
                        <td style="margin:auto;width:10%;">

                                    <select class="" name="vehicle_ratingtype0">
                                        {foreach item=DATA key=VAL from=$RATINGTYPE_PICKLIST}
                                            <option value="{$DATA.val}">{$DATA.text}</option>
                                        {/foreach}
                                    </select>
                        </td>
                    </tr>
                </tbody>
            </table>
            <br>
            <table class="table table-bordered detailview-table dispatchremarks">
                <thead>
                    <tr>
                        <th class="blockHeader">Dispatch Remarks</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="fieldValue medium">
                            <div class="row-fluid">
                                <span class="span12">
                                    <textarea rows="2" name="description" style="resize: none;" placeholder="Dispatch Remarks"></textarea>
                                </span>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    <div class="modal-footer">
        <div class="pull-right cancelLinkContainer" style="margin-top:0px;">
            <a class="cancelLink" type="reset" data-dismiss="modal">{vtranslate('LBL_CANCEL')}</a>
        </div>
        <button class="btn btn-success" id="saveCreateOverflow" type="submit" name="saveCreateOverflow" onclick="Orders_Detail_Js.saveOverflow();"><strong>{vtranslate('Save', $MODULENAME) }</strong></button>
    </div>
</div>
