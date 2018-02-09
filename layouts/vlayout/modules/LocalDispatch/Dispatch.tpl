<link rel='stylesheet' href='layouts/vlayout/modules/{$MODULE_NAME}/resources/css/fullcalendar.css' />
<link rel='stylesheet' href='layouts/vlayout/modules/{$MODULE_NAME}/resources/css/Dispatch.css' />
<script src='layouts/vlayout/modules/{$MODULE_NAME}/resources/js/moment.min.js'></script>
<script src='layouts/vlayout/modules/{$MODULE_NAME}/resources/js/fullcalendar.min.js'></script>
<script src='layouts/vlayout/modules/{$MODULE_NAME}/resources/js/Dispatch.js'></script>
<script src='layouts/vlayout/modules/{$MODULE_NAME}/resources/js/ldfilter.js'></script>

<div id="dispatch-container">
  <div id="dispatch-tab">
    <input type="button" id="dispatch" value="Go to Unassigned Orders" />
  </div>

  <div class="clr"></div>

  <div id="dispatch-calendar"></div>

  <div id="assets-container">
    <div id="assets-container-toggle">
      <i id="dispatch-panel-btn" class="icon-chevron-right"></i>
    </div>

    <div id="dispatch-panel-rt">
      <fieldset>
        <legend>Crew</legend>

        <label for="showAvailableCrew">
          <input type="checkbox" id="showAvailableCrew" /> Show Available
        </label>

        <div class="filters">
          <input autocomplete="off" class="filter" name="name" placeholder="Filter Name" data-col="name" />
          <input autocomplete="off" class="filter" name="role" placeholder="Filter Role" data-col="role" />
          <a class="clear-filter">X</a>
        </div>

        <table id="crew">
          <thead>
            <tr>
              <th>Status</th>
              <th>Name</th>
              <th>Role</th>
              <th>Avail. Hrs.</th>
              <th>Sched. Hrs.</th>
              <th>Worked Hrs.</th>
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
          <input type="checkbox" id="showAvailableTrucks" /> Show Available
        </label>

        <div class="filters">
          <input autocomplete="off" class="filter" name="vehicle" placeholder="Truck Number" data-col="vehicle" />
          <input autocomplete="off" class="filter" name="type" placeholder="Truck Type" data-col="type" />
          <a class="clear-filter">X</a>
        </div>

        <table id="trucks">
          <thead>
            <tr>
              <th>Status</th>
              <th>Vehicle#</th>
              <th>Type</th>
              <th>Capacity</th>
              <th>Sched. Hrs.</th>
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