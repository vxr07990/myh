<div class="modal addViewsToCalendar hide">
    <div class="modal-header contentsBackground">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h3>{vtranslate('LBL_ADD_CALENDAR_VIEW', 'Calendar')}</h3>
    </div>
    <div class="modal-body">
        <form class="form-horizontal">
            <input type="hidden" class="selectedUser" value="" />
            <input type="hidden" class="selectedUserColor" value="" />
            <input type="hidden" class="userCalendarMode" value="" />
            <div class="control-group addCalendarViewsList">
                <label class="control-label">{vtranslate('LBL_SELECT_USER_CALENDAR', 'Calendar')}</label>
                <div class="controls">
                    <select class="select2" name="usersCalendarList" style="min-width: 250px;">
                        {foreach key=USER_ID item=USER_NAME from=$SHAREDUSERS}
                            {if !in_array($USER_ID, $ADDED_IDS)}
                                <option value="{$USER_ID}" >{$USER_NAME}</option>
                            {/if}
                        {/foreach}
                    </select>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label">{vtranslate('LBL_SELECT_CALENDAR_COLOR', 'Calendar')}</label>
                <div class="controls">
                    <p class="calendarColorPicker"></p>
                </div>
            </div>
        </form>
    </div>
    {include file='ModalFooter.tpl'|@vtemplate_path:$MODULE}
</div>
