<div class="modelContainer">
    <div class="modal-header contentsBackground"> 
        <button data-dismiss="modal" class="close" title="{vtranslate('CLOSED') }">&times;</button>
        <h4>Move OrdersTask to Trip</h4>
    </div>
    <div class="modal-body tabbable" style=" height: 200px; overflow-y: auto;">
       <p style="">Select the Trip where you want to move the OrdersTask</p>
        <form id="vgsCreateProjectForm" class="form-horizontal" method="post" action="index.php" >
            <table class="table table-bordered listViewEntriesTable"><tbody>
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
                        <th>Agent Unit</th>
                        <th>Planning Notes</th>
                        <th>Dispatch Notes</th>
                        <th>Driver</th>
                        <th>Total Linehaul</th>
                        <th>Total Weight</th>
                    </tr>
                </thead>
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
                        <td>{$triparr.agent_unit}</td>
                        <td>{$triparr.planning_notes}</td>
                        <td>{$triparr.dispatch_notes}</td>
                        <td>{$triparr.driver_id}</td>
                        <td>{$triparr.total_line_haul}</td>
                        <td>{$triparr.total_weight}</td>
                    </tr>
                {/foreach}
                </tbody>
            </table>
            <input type="hidden" value="{$orders}" id="ordersids">
            <input type="hidden" value="{$OLD_TRIP_ID}" id="oldtripid">
        </form>
    </div>
    <div class="modal-footer">
        <div class="pull-right cancelLinkContainer" style="margin-top:0px;">
            <a class="cancelLink" type="reset" data-dismiss="modal">{vtranslate('LBL_CANCEL')}</a>
        </div>
        <beutton class="btn btn-success" id="saveButton" type="submit" name="saveButton"><strong>{vtranslate('SAVE', $MODULENAME) }</strong></button>
        </form>
    </div>
</div>