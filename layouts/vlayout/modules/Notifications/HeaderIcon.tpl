{strip}
    <span class="dropdown span">
        <span class="dropdown-toggle settingIcons" data-toggle="dropdown" href="#">
            <a id="headerNotification">
                <img title="Notifications" alt="Notifications"
                     src="layouts/vlayout/modules/Notifications/resources/img/icon-bell.png" class="alignMiddle">
                <strong class="notification_count">{$LISTVIEW_ENTRIES_COUNT}</strong>
            </a>
        </span>

        <ul id="headerNotificationList" class="dropdown-menu pull-right">
            {foreach item=ITEM from=$LISTVIEW_ENTRIES}
                <li>
                    <a class="notification_link" href="javascript:;" data-href="{$ITEM['link']}" data-id="{$ITEM['id']}" data-rel_id="{$ITEM['rel_id']}">
                        <div class="notification-container">
                            <button class="btn btn-small btn-success" onclick="return Notifications_JS.clickToOk(this);">&nbsp;{vtranslate('OK', $MODULE)}</button>
                            <div class="notification_detail">
                                <span class="notification_full_name" title="{$ITEM['full_name']}">{$ITEM['full_name']}&nbsp;</span>
                                <span class="notification_description" title="{$ITEM['description']}">{$ITEM['description']}&nbsp;</span>
                                <span class="notification_createdtime" title="{$ITEM['createdtime']}">{$ITEM['createdtime']}&nbsp;</span>
                            </div>
                            <button class="btn btn-small btn-warning" onclick="return Notifications_JS.clickToPP(this);">{vtranslate('PP', $MODULE)}&nbsp;</button>
                            <div class="clearfix"></div>
                        </div>
                    </a>
                    <div class="divider">&nbsp;</div>
                </li>
            {/foreach}
        </ul>
    </span>
{/strip}