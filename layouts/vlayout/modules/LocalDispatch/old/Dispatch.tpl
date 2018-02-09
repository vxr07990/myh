<link rel='stylesheet' href='layouts/vlayout/modules/{$MODULE_NAME}/resources/css/fullcalendar.css' />
<link rel='stylesheet' href='layouts/vlayout/modules/{$MODULE_NAME}/resources/css/Dispatch.css' />
<script src='layouts/vlayout/modules/{$MODULE_NAME}/resources/js/moment.min.js'></script>
<script src='layouts/vlayout/modules/{$MODULE_NAME}/resources/js/fullcalendar.min.js'></script>
<script src='layouts/vlayout/modules/{$MODULE_NAME}/resources/js/Dispatch.js'></script>
<script src='layouts/vlayout/modules/{$MODULE_NAME}/resources/js/filter.js'></script>

<div id="dispatch-container">
  <div style="width:100%;padding:0;text-align:right;margin-bottom:10px;">
    <input type="button" id="dispatch" value="Go to Unassigned Orders" style="border-radius:5px;margin-top:10px;" />
  </div>

  <div style="float:left;">
    <div id="dispatch-calendar" style="display:inline;float:left;width:65%;"></div>
    <div style="display:inline;float:right;width:33%;">

      <div id="ldFilter">
        <fieldset>
          <legend>Crew</legend>

          <div id="filterPeople">

            <label for="showAvailablePeople"><input type="checkbox" value="people" id="showAvailablePeople" /> Show Available</label>

            <div class="ldFilterContainer">
              <input type="text" class="ldFilterInput default" placeholder="Filter Crew" />
              <a href="#" class="clearField" title="Clear Filter">x</a>
            </div>
            <div class="noResults"><strong>Sorry.</strong> There is no match for your filter; please try again.</div>

            <table class="ldFilterList" id="people">
              <thead>
                <tr>
                  <th>Status</th>
                  <th>Name</th>
                  <th>Role</th>
                  <th>Available Hours</th>
                  <th>Scheduled Hours</th>
                  <th>Hours Worked</th>
                </tr>
              <tbody>
                <tr>
                  <td class="available"></td>
                  <td>Jim Johnson</td>
                  <td>Contractor</td>
                  <td>33</td>
                  <td>21</td>
                  <td>40</td>
                </tr>
                <tr>
                  <td class="unavailable"></td>
                  <td>Alen Bergen</td>
                  <td>Employee</td>
                  <td>32</td>
                  <td>12</td>
                  <td>40</td>
                </tr>
                <tr>
                  <td class="warning"></td>
                  <td>Alen Bergen</td>
                  <td>Employee</td>
                  <td>32</td>
                  <td>12</td>
                  <td>40</td>
                </tr>
              </tbody>
            </table>
          </div>
        </fieldset>

        <fieldset>
          <legend>Trucks</legend>

          <div id="filterTrucks">

            <label for="showAvailableTrucks"><input type="checkbox" value="trucks" id="showAvailableTrucks" /> Show Available</label>

            <div class="ldFilterContainer">
              <input type="text" class="ldFilterInput default" placeholder="Filter Trucks" />
              <a href="#" class="clearField" title="Clear Filter">x</a>
            </div>
            <div class="noResults"><strong>Sorry.</strong> There is no match for your filter; please try again.</div>

            <table class="ldFilterList" id="trucks">
              <thead>
                <tr>
                  <th>Status</th>
                  <th>Vehicle#</th>
                  <th>Type</th>
                  <th>Capacity</th>
                  <th>Hours</th>
                </tr>
              <tbody>
                <tr>
                  <td class="unavailable"></td>
                  <td>1213</td>
                  <td>Flatbed</td>
                  <td>123'</td>
                  <td>22</td>
                </tr>
                <tr>
                  <td class="warning"></td>
                  <td>2132</td>
                  <td>Panel</td>
                  <td>80'</td>
                  <td>12</td>
                </tr>
                <tr>
                  <td class="available"></td>
                  <td>2132</td>
                  <td>Panel</td>
                  <td>80'</td>
                  <td>12</td>
                </tr>
              </tbody>
            </table>
          </div>
        </fieldset>
      </div>
    </div>
  </div>
</div>