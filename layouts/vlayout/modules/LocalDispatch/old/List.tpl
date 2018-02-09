<link rel='stylesheet' href='layouts/vlayout/modules/{$MODULE_NAME}/resources/css/fullcalendar.css' />
<link rel='stylesheet' href='layouts/vlayout/modules/{$MODULE_NAME}/resources/css/Dispatch.css' />
<script src='layouts/vlayout/modules/{$MODULE_NAME}/resources/js/moment.min.js'></script>
<script src='layouts/vlayout/modules/{$MODULE_NAME}/resources/js/fullcalendar.min.js'></script>
<script src='layouts/vlayout/modules/{$MODULE_NAME}/resources/js/Dispatch.js'></script>

<div id="dispatch-orders">

  <div>
    <form method="post" action="" style="display:block;">
      <label for="dispatch-from">From</label>
      <input type="text" id="dispatch-from" name="dispatch-from" />
      <label for="dispatch-to">To</label>
      <input type="text" id="dispatch-to" name="dispatch-to" />
      <input type="submit" name="dispatch-date-submit" id="dispatch-date-submit" value="Search" />
      <input type="button" id="unassigned-orders" value="Go to Day Book" style="float:right" />
    </form>
  </div>

  <div id="scrollable-orders">
    <table id="dispatch-orders">
      <tbody>
        <tr>
          <th>BOL#</th>
          <th>Accept</th>
          <th>Reject</th>
          <th>Driver Notes</th>
          <th>Order Notes</th>
          <th>Pack Date</th>
          <th>Pack To Date</th>
          <th>Preferred Pack Date</th>
          <th>Load Date</th>
          <th>Load To Date</th>
          <th>Preferred Load Date</th>
          <th>Delivery Date</th>
          <th>Delivery To Date</th>
          <th>Preferred Delivery Date</th>
          <th>Transferee Name</th>
          <th>Order Number</th>
          <th>Account Name</th>
          <th>Service</th>
          <th>Crew Needed</th>
          <th>Vehicles Needed</th>
          <th>Estimated Hours</th>
          <th>Estimated Weight</th>
          <th>Estimated Cube</th>
          <th>Estimated Pieces</th>
          <th>Estimated Containers</th>
          <th>Estimated Crates</th>
          <th>Job Start Time</th>
          <th>Job End Time</th>
          <th>Assigned Crew Members</th>
          <th>Assigned Equipment</th>
          <th>Origin Address 1</th>
          <th>Origin Address 2</th>
          <th>Origin City</th>
          <th>Origin State</th>
          <th>Origin Zip</th>
          <th>Destination Address 1</th>
          <th>Destination Address 2</th>
          <th>Destination City</th>
          <th>Destination State</th>
          <th>Destination Zip</th>
          <th>Estimated Linehaul</th>
          <th>Estimated Order Total</th>
          <th>Contact Name</th>
          <th>Contact Phone</th>
          <th>Move Coordinator</th>
        </tr>
        <tr>
          <td>88888888888</td>
          <td><input type="button" data-id="1" name="" value="Accept" /></td>
          <td><input type="button" data-id="1" name="" value="Reject" /></td>
          <td><input type="button" data-id="1" name="" value="Driver Notes" /></td>
          <td><input type="button" data-id="1" name="" value="Order Notes" /></td>
          <td>Data Here</td>
          <td>Data Here</td>
          <td>Data Here</td>
          <td>Data Here</td>
          <td>Data Here</td>
          <td>Data Here</td>
          <td>Data Here</td>
          <td>Data Here</td>
          <td>Data Here</td>
          <td>Data Here</td>
          <td>Data Here</td>
          <td>Data Here</td>
          <td>Data Here</td>
          <td>Data Here</td>
          <td>Data Here</td>
          <td>Data Here</td>
          <td>Data Here</td>
          <td>Data Here</td>
          <td>Data Here</td>
          <td>Data Here</td>
          <td>Data Here</td>
          <td>Data Here</td>
          <td>Data Here</td>
          <td>Data Here</td>
          <td>Data Here</td>
          <td>Data Here</td>
          <td>Data Here</td>
          <td>Data Here</td>
          <td>Data Here</td>
          <td>Data Here</td>
          <td>Data Here</td>
          <td>Data Here</td>
          <td>Data Here</td>
          <td>Data Here</td>
          <td>Data Here</td>
          <td>Data Here</td>
          <td>Data Here</td>
          <td>Data Here</td>
          <td>Data Here</td>
          <td>Data Here</td>
        </tr>
        <tr>
          <td>0000000000</td>
          <td><input type="button" data-id="2" name="" value="Accept" /></td>
          <td><input type="button" data-id="2" name="" value="Reject" /></td>
          <td><input type="button" data-id="2" name="" value="Driver Notes" /></td>
          <td><input type="button" data-id="2" name="" value="Order Notes" /></td>
          <td>Data Here</td>
          <td>Data Here</td>
          <td>Data Here</td>
          <td>Data Here</td>
          <td>Data Here</td>
          <td>Data Here</td>
          <td>Data Here</td>
          <td>Data Here</td>
          <td>Data Here</td>
          <td>Data Here</td>
          <td>Data Here</td>
          <td>Data Here</td>
          <td>Data Here</td>
          <td>Data Here</td>
          <td>Data Here</td>
          <td>Data Here</td>
          <td>Data Here</td>
          <td>Data Here</td>
          <td>Data Here</td>
          <td>Data Here</td>
          <td>Data Here</td>
          <td>Data Here</td>
          <td>Data Here</td>
          <td>Data Here</td>
          <td>Data Here</td>
          <td>Data Here</td>
          <td>Data Here</td>
          <td>Data Here</td>
          <td>Data Here</td>
          <td>Data Here</td>
          <td>Data Here</td>
          <td>Data Here</td>
          <td>Data Here</td>
          <td>Data Here</td>
          <td>Data Here</td>
          <td>Data Here</td>
          <td>Data Here</td>
          <td>Data Here</td>
          <td>Data Here</td>
          <td>Data Here</td>
        </tr>
        <tr>
          <td>1111111111</td>
          <td><input type="button" data-id="3" name="" value="Accept" /></td>
          <td><input type="button" data-id="3" name="" value="Reject" /></td>
          <td><input type="button" data-id="3" name="" value="Driver Notes" /></td>
          <td><input type="button" data-id="3" name="" value="Order Notes" /></td>
          <td>Data Here</td>
          <td>Data Here</td>
          <td>Data Here</td>
          <td>Data Here</td>
          <td>Data Here</td>
          <td>Data Here</td>
          <td>Data Here</td>
          <td>Data Here</td>
          <td>Data Here</td>
          <td>Data Here</td>
          <td>Data Here</td>
          <td>Data Here</td>
          <td>Data Here</td>
          <td>Data Here</td>
          <td>Data Here</td>
          <td>Data Here</td>
          <td>Data Here</td>
          <td>Data Here</td>
          <td>Data Here</td>
          <td>Data Here</td>
          <td>Data Here</td>
          <td>Data Here</td>
          <td>Data Here</td>
          <td>Data Here</td>
          <td>Data Here</td>
          <td>Data Here</td>
          <td>Data Here</td>
          <td>Data Here</td>
          <td>Data Here</td>
          <td>Data Here</td>
          <td>Data Here</td>
          <td>Data Here</td>
          <td>Data Here</td>
          <td>Data Here</td>
          <td>Data Here</td>
          <td>Data Here</td>
          <td>Data Here</td>
          <td>Data Here</td>
          <td>Data Here</td>
          <td>Data Here</td>
        </tr>
        <tr>
          <td>2222222222</td>
          <td><input type="button" data-id="4" name="" value="Accept" /></td>
          <td><input type="button" data-id="4" name="" value="Reject" /></td>
          <td><input type="button" data-id="4" name="" value="Driver Notes" /></td>
          <td><input type="button" data-id="4" name="" value="Order Notes" /></td>
          <td>Data Here</td>
          <td>Data Here</td>
          <td>Data Here</td>
          <td>Data Here</td>
          <td>Data Here</td>
          <td>Data Here</td>
          <td>Data Here</td>
          <td>Data Here</td>
          <td>Data Here</td>
          <td>Data Here</td>
          <td>Data Here</td>
          <td>Data Here</td>
          <td>Data Here</td>
          <td>Data Here</td>
          <td>Data Here</td>
          <td>Data Here</td>
          <td>Data Here</td>
          <td>Data Here</td>
          <td>Data Here</td>
          <td>Data Here</td>
          <td>Data Here</td>
          <td>Data Here</td>
          <td>Data Here</td>
          <td>Data Here</td>
          <td>Data Here</td>
          <td>Data Here</td>
          <td>Data Here</td>
          <td>Data Here</td>
          <td>Data Here</td>
          <td>Data Here</td>
          <td>Data Here</td>
          <td>Data Here</td>
          <td>Data Here</td>
          <td>Data Here</td>
          <td>Data Here</td>
          <td>Data Here</td>
          <td>Data Here</td>
          <td>Data Here</td>
          <td>Data Here</td>
          <td>Data Here</td>
        </tr>
        <tr>
          <td>33333333333</td>
          <td><input type="button" data-id="5" name="" value="Accept" /></td>
          <td><input type="button" data-id="5" name="" value="Reject" /></td>
          <td><input type="button" data-id="5" name="" value="Driver Notes" /></td>
          <td><input type="button" data-id="5" name="" value="Order Notes" /></td>
          <td>Data Here</td>
          <td>Data Here</td>
          <td>Data Here</td>
          <td>Data Here</td>
          <td>Data Here</td>
          <td>Data Here</td>
          <td>Data Here</td>
          <td>Data Here</td>
          <td>Data Here</td>
          <td>Data Here</td>
          <td>Data Here</td>
          <td>Data Here</td>
          <td>Data Here</td>
          <td>Data Here</td>
          <td>Data Here</td>
          <td>Data Here</td>
          <td>Data Here</td>
          <td>Data Here</td>
          <td>Data Here</td>
          <td>Data Here</td>
          <td>Data Here</td>
          <td>Data Here</td>
          <td>Data Here</td>
          <td>Data Here</td>
          <td>Data Here</td>
          <td>Data Here</td>
          <td>Data Here</td>
          <td>Data Here</td>
          <td>Data Here</td>
          <td>Data Here</td>
          <td>Data Here</td>
          <td>Data Here</td>
          <td>Data Here</td>
          <td>Data Here</td>
          <td>Data Here</td>
          <td>Data Here</td>
          <td>Data Here</td>
          <td>Data Here</td>
          <td>Data Here</td>
          <td>Data Here</td>
        </tr>
        <tr>
          <td>444444444444</td>
          <td><input type="button" data-id="6" name="" value="Accept" /></td>
          <td><input type="button" data-id="6" name="" value="Reject" /></td>
          <td><input type="button" data-id="6" name="" value="Driver Notes" /></td>
          <td><input type="button" data-id="6" name="" value="Order Notes" /></td>
          <td>Data Here</td>
          <td>Data Here</td>
          <td>Data Here</td>
          <td>Data Here</td>
          <td>Data Here</td>
          <td>Data Here</td>
          <td>Data Here</td>
          <td>Data Here</td>
          <td>Data Here</td>
          <td>Data Here</td>
          <td>Data Here</td>
          <td>Data Here</td>
          <td>Data Here</td>
          <td>Data Here</td>
          <td>Data Here</td>
          <td>Data Here</td>
          <td>Data Here</td>
          <td>Data Here</td>
          <td>Data Here</td>
          <td>Data Here</td>
          <td>Data Here</td>
          <td>Data Here</td>
          <td>Data Here</td>
          <td>Data Here</td>
          <td>Data Here</td>
          <td>Data Here</td>
          <td>Data Here</td>
          <td>Data Here</td>
          <td>Data Here</td>
          <td>Data Here</td>
          <td>Data Here</td>
          <td>Data Here</td>
          <td>Data Here</td>
          <td>Data Here</td>
          <td>Data Here</td>
          <td>Data Here</td>
          <td>Data Here</td>
          <td>Data Here</td>
          <td>Data Here</td>
          <td>Data Here</td>
        </tr>
        <tr>
          <td>5555555555</td>
          <td><input type="button" data-id="7" name="" value="Accept" /></td>
          <td><input type="button" data-id="7" name="" value="Reject" /></td>
          <td><input type="button" data-id="7" name="" value="Driver Notes" /></td>
          <td><input type="button" data-id="7" name="" value="Order Notes" /></td>
          <td>Data Here</td>
          <td>Data Here</td>
          <td>Data Here</td>
          <td>Data Here</td>
          <td>Data Here</td>
          <td>Data Here</td>
          <td>Data Here</td>
          <td>Data Here</td>
          <td>Data Here</td>
          <td>Data Here</td>
          <td>Data Here</td>
          <td>Data Here</td>
          <td>Data Here</td>
          <td>Data Here</td>
          <td>Data Here</td>
          <td>Data Here</td>
          <td>Data Here</td>
          <td>Data Here</td>
          <td>Data Here</td>
          <td>Data Here</td>
          <td>Data Here</td>
          <td>Data Here</td>
          <td>Data Here</td>
          <td>Data Here</td>
          <td>Data Here</td>
          <td>Data Here</td>
          <td>Data Here</td>
          <td>Data Here</td>
          <td>Data Here</td>
          <td>Data Here</td>
          <td>Data Here</td>
          <td>Data Here</td>
          <td>Data Here</td>
          <td>Data Here</td>
          <td>Data Here</td>
          <td>Data Here</td>
          <td>Data Here</td>
          <td>Data Here</td>
          <td>Data Here</td>
          <td>Data Here</td>
        </tr>
        <tr>
          <td>777777777</td>
          <td><input type="button" data-id="8" name="" value="Accept" /></td>
          <td><input type="button" data-id="8" name="" value="Reject" /></td>
          <td><input type="button" data-id="8" name="" value="Driver Notes" /></td>
          <td><input type="button" data-id="8" name="" value="Order Notes" /></td>
          <td>Data Here</td>
          <td>Data Here</td>
          <td>Data Here</td>
          <td>Data Here</td>
          <td>Data Here</td>
          <td>Data Here</td>
          <td>Data Here</td>
          <td>Data Here</td>
          <td>Data Here</td>
          <td>Data Here</td>
          <td>Data Here</td>
          <td>Data Here</td>
          <td>Data Here</td>
          <td>Data Here</td>
          <td>Data Here</td>
          <td>Data Here</td>
          <td>Data Here</td>
          <td>Data Here</td>
          <td>Data Here</td>
          <td>Data Here</td>
          <td>Data Here</td>
          <td>Data Here</td>
          <td>Data Here</td>
          <td>Data Here</td>
          <td>Data Here</td>
          <td>Data Here</td>
          <td>Data Here</td>
          <td>Data Here</td>
          <td>Data Here</td>
          <td>Data Here</td>
          <td>Data Here</td>
          <td>Data Here</td>
          <td>Data Here</td>
          <td>Data Here</td>
          <td>Data Here</td>
          <td>Data Here</td>
          <td>Data Here</td>
          <td>Data Here</td>
          <td>Data Here</td>
          <td>Data Here</td>
        </tr>
      </tbody>
    </table>
  </div>
</div>

<div id="unassigned-calendar"></div>