/* ********************************************************************************
 * The content of this file is subject to the Notifications ("License");
 * You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is VTExperts.com
 * Portions created by VTExperts.com. are Copyright(C) VTExperts.com.
 * All Rights Reserved.
 * ****************************************************************************** */

/** @class Notifications_JS */
jQuery.Class("Notifications_JS", {
    /**
     * @param notificationLink
     * @param notificationList
     * @param notificationCounter
     */
    updateTotalCounter: function (notificationLink, notificationList, notificationCounter) {
        notificationLink.closest('li').remove();
        // Remove last divide
        notificationList.find('li:last').find('.divider').remove();

        // Update counter
        var currentTotal = notificationCounter.text();
        currentTotal = (currentTotal) ? parseInt(currentTotal) : 0;
        var total = currentTotal - 1;
        total = (total > 0) ? total : 0;
        notificationCounter.text(total);

        if (total == 0) {
            // Remove empty list
            notificationList.remove();
        }
    },

    clickToPP: function (btnPP) {
        var notificationContainer = jQuery('#headerNotification');
        var notificationList = jQuery('#headerNotificationList');
        var notificationCounter = notificationContainer.find('.notification_count');
        var currentTarget = jQuery(btnPP);
        var notificationLink = currentTarget.closest('.notification_link');
        Notifications_JS.updateTotalCounter(notificationLink, notificationList, notificationCounter);
        return false;
    },

    clickToOk: function (btnOK) {
        var notificationContainer = jQuery('#headerNotification');
        var notificationList = jQuery('#headerNotificationList');
        var notificationCounter = notificationContainer.find('.notification_count');
        var currentTarget = jQuery(btnOK);
        var notificationLink = currentTarget.closest('.notification_link');
        var id = notificationLink.data('id');

        // Mark notification read
        var params = {
            'module': 'Notifications',
            'action': 'ActionAjax',
            'mode': 'markNotificationRead',
            'record': id
        };

        AppConnector.request(params).then(
            function (response) {
                if (response.success == true) {
                    Notifications_JS.updateTotalCounter(notificationLink, notificationList, notificationCounter);
                } else {
                    Vtiger_Helper_Js.showMessage({
                        text: response.error.message,
                        type: 'error'
                    })
                }
            },
            function (error) {
                console.log(error);
            }
        );

        return false;
    }

}, {
    /**
     * Add icon to header
     */
    addHeaderIcon: function () {
        var headerLinksBig = jQuery('#headerLinksBig');
        // Init notifications
        var params = {
            'module': 'Notifications',
            'view': 'HeaderIcon'
        };
        AppConnector.request(params).then(
            function (response) {
                headerLinksBig.prepend(response);
                // Redirect link
                headerLinksBig.on('click', '.notification_link .notification_full_name', function (event) {
                    var currentTarget = jQuery(event.currentTarget);
                    var notificationLink = currentTarget.closest('.notification_link');
                    window.location.href = notificationLink.data('href');
                });
            },
            function (error) {
                console.log(error);
            }
        );
    },

    registerEvents: function () {
        var thisInstance = this;
        thisInstance.addHeaderIcon();
    }
});

jQuery(document).ready(function () {
    var instance = new Notifications_JS();
    instance.registerEvents();
});