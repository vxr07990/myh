<link rel='stylesheet' href='layouts/vlayout/modules/{$MODULE_NAME}/resources/css/fullcalendar.css' />
<link rel='stylesheet' href='layouts/vlayout/modules/{$MODULE_NAME}/resources/css/Dispatch.css' />
<script src='layouts/vlayout/modules/{$MODULE_NAME}/resources/js/moment.min.js'></script>
<script src='layouts/vlayout/modules/{$MODULE_NAME}/resources/js/fullcalendar.min.js'></script>
<script src='layouts/vlayout/modules/{$MODULE_NAME}/resources/js/Dispatch.js'></script>
<script src='layouts/vlayout/modules/{$MODULE_NAME}/resources/js/ldfilter.js'></script>

<div id="dispatch-container">
  <div style="width:100%;padding:0;text-align:right;margin-bottom:10px;">
    <input type="button" id="dispatch" value="Go to Unassigned Orders" style="border-radius:5px;margin-top:10px;" />
  </div>

  <div style="float:left;">
    <div id="dispatch-calendar" style="display:inline;float:left;width:65%;"></div>
    <div style="display:inline;float:right;width:33%;">
      <fieldset>
        <legend>Crew</legend>

        <label for="showAvailableCrew">
          <input type="checkbox" id="showAvailableCrew" style="display:inline" /> Show Available
        </label>

        <div class="filters" style="margin-top: 5px;">
          <input autocomplete="off" class="filter" name="name" placeholder="Filter Name" data-col="name" style="display:inline" />
          <input autocomplete="off" class="filter" name="role" placeholder="Filter Role" data-col="role" style="display:inline" />
          <a class="clear-filter">X</a>
        </div>

        <table id="crew">
          <thead>
            <tr>
              <th>Status</th>
              <th>Name</th>
              <th>Role</th>
              <th>Available Hours</th>
              <th>Scheduled Hours</th>
              <th>Hours Worked</th>
            </tr>
          </thead>
          <tbody>
            {$CREW}
          </tbody>
        </table>
      </fieldset>

      <fieldset>
        <legend>Trucks</legend>

        <label for="showAvailableTrucks">
          <input type="checkbox" id="showAvailableTrucks" style="display:inline" /> Show Available
        </label>

        <div class="filters" style="margin-top:5px;">
          <input autocomplete="off" class="filter" name="vehicle" placeholder="Truck Number" data-col="vehicle" style="display:inline" />
          <input autocomplete="off" class="filter" name="type" placeholder="Truck Type" data-col="type" style="display:inline" />
          <a class="clear-filter">X</a>
        </div>

        <table id="trucks">
          <thead>
            <tr>
              <th>Status</th>
              <th>Vehicle#</th>
              <th>Type</th>
              <th>Capacity</th>
              <th>Hours</th>
            </tr>
          </thead>
          <tbody>
            {$VEHICLES}
          </tbody>
        </table>
      </fieldset>
    </div>
  </div>
</div>