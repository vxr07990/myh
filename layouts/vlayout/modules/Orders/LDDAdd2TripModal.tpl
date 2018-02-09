<style>
    {literal}
        .visible{
            display:table-row;
        }
        .not_visible{
            display:none;
        }
    {/literal}
</style>
<div class="modelContainer">
    <div class="modal-header contentsBackground">
        <button data-dismiss="modal" class="close" title="{vtranslate('CLOSED') }">&times;</button>
        <h4>{vtranslate('LBL_ORDERS_ADD_ORDER_TO_TRIP', $MODULENAME)}</h4>
    </div>
    <div class="modal-body tabbable">
        <p style="">{vtranslate('LBL_ORDERS_MODAL_SELECT_TRIP_DESC', $MODULENAME)}</p>
        <div>
            <input type="text" value="" placeholder="Trip ID" style="width:12%;margin-right: 0.5%;" id="filtro_tripid">
            <input type="text" value="" placeholder="Driver Name" style="width:12%;margin-right: 0.5%;" id="filtro_drivername">
            <input type="text" value="" placeholder="Agent Name" style="width:12%;margin-right: 0.5%;" id="filtro_agentname">
            <input type="text" value="" placeholder="Agent Number" style="width:12%;margin-right: 0.5%;" id="filtro_agentnumber">
            <div class="date" style="display: inline;margin-right: 0.5%;">
                <input type="text" class="dateField" placeholder="Empty Date" style="width:14%;" data-date-format="yyyy-mm-dd" id="filtro_emptydate">
            </div>
            <input type="text" value="" placeholder="Empty Zone" style="width:16%;margin-right: 0.5%;" id="filtro_emptyzone">
            <span class="btn" id="filtrar_tabla" style="margin-top: -0.9%;">Filter</span>
            <span  id="clear_addtotrip_filter" style="margin-top: -0.9%;margin-left: 0.5%;cursor: pointer;">Clear Filter</span>
        </div>
        <form id="vgsCreateProjectForm" class="form-horizontal" method="post" action="index.php" >

            <div class="trips-modal-table" style=" height: 200px; overflow-y: auto;">
                <table class="table table-bordered listViewEntriesTable" id="modaltable">
                    <thead>
                        <tr class="listViewHeaders">
                            <th>{vtranslate('SELECT', $MODULENAME)}</th>
                            <th>Trip ID</th>
                            <th>In Transit Zone</th>
                            <th>Origin Zone</th>
                            <th>Origin State</th>
                            <th>Empty Zone</th>
                            <th>Empty State</th>
                            <th>Empty Date</th>
                            <th>Agent Number</th>
                            <th>Agent Unit</th>
                            <th>Planning Notes</th>
                            <th>Dispatch Notes</th>
                            <th>Driver Name</th>
                            <th>Total Linehaul</th>
                            <th>Total Weight</th>
                        </tr>
                    </thead>
                    <tbody>
                        {foreach item=triparr from=$TRIPS}
                            <tr class="listViewEntries">
                                <td><input type="checkbox" name="check_{$triparr.tripsid}"/></td>
                                <td>{$triparr.id_trips}</td>
                                <td>{$triparr.intransitzone}</td>
                                <td>{$triparr.origin_zone}</td>
                                <td>{$triparr.origin_state}</td>
                                <td>{$triparr.empty_zone}</td>
                                <td>{$triparr.empty_state}</td>
                                <td>{$triparr.empty_date}</td>
                                <td>{$triparr.agent_number}</td>
                                <td>{$triparr.agent_unit}</td>
                                <td>{$triparr.planning_notes}</td>
                                <td>{$triparr.dispatch_notes}</td>
                                <td>{$triparr.driver_name}</td>
                                <td>{$triparr.total_line_haul}</td>
                                <td>{$triparr.total_weight}</td>
                            </tr>
                        {/foreach}
                    </tbody>
                </table>

            </div>


            <input type="hidden" value="{$orders}" id="orders">
        </form>
    </div>
    <div class="modal-footer">
        <div class="pull-right cancelLinkContainer" style="margin-top:0px;">
            <a class="cancelLink" type="reset" data-dismiss="modal">{vtranslate('LBL_CANCEL')}</a>
        </div>
        <beutton class="btn btn-success" id="createProjectSave" type="submit" name="saveButton"><strong>{vtranslate('SAVE', $MODULENAME) }</strong></button>
            </form>
    </div>
</div>
