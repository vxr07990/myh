Vtiger_Edit_Js("RevenueGroupingItem_EditBlock_Js", {
    getInstance: function () {
        return new RevenueGroupingItem_EditBlock_Js();
    }
}, {
    registerRemoveRevenueGroupingItemButton: function () {
        jQuery('html').on('click', '.removeRevenueGroupingItem', function () {
            if (jQuery(this).siblings('input:hidden[name^="revenuegroupingitemId"]').val() == 'none') {
                jQuery(this).parent().parent().remove()
            } else {
                jQuery(this).parent().parent().addClass('hide');
                jQuery(this).siblings('input:hidden[name^="revenuegroupingitemDelete"]').val('deleted');
            }
            var sequenceNode = jQuery("input[name='numAgents']");
            var sequence = jQuery(".revenuegroupingitemRow:gt(0)").length;
            sequenceNode.val(sequence);
        });
    },

    registerEditBlockRevenueGrouping: function () {
        jQuery('html').on('click', '#btnAddRevenueGrouping', function () {
            window.location.href = window.location.href + "&editblock=1";
        });
    },
    registerDeleteBlockRevenueGrouping: function () {
        jQuery('html').on('click', '#btnDeleteRevenueGrouping', function () {
            var recordId = jQuery(this).data('record-id');
            var deleteUrl = "index.php?module=RevenueGrouping&action=Delete&record=" + recordId;
            AppConnector.request(deleteUrl + '&ajaxDelete=true').then(
                function (data) {
                    if (data.success == true) {
                        window.location.href = window.location.href;
                    } else {
                        Vtiger_Helper_Js.showPnotify(data.error.message);
                    }
                }
            );
        });
    },

    registerAddRevenueGroupingItemButtons: function () {
        var thisInstance = this;
        var table = jQuery('[name^="RevenueGroupingItemTable"]').find('tbody');

        var button = jQuery('.addRevenueGroupingItem');

        var addHandler = function () {
            var newRow = jQuery('.defaultRevenueGroupingItem').clone();
            var sequenceNode = jQuery("input[name='numAgents']");
            //a beautiful way to handle the tally that tracks the number of the revenuegroupingitem we are currently adding
            var sequence = sequenceNode.val();
            sequence++;
            sequenceNode.val(sequence);
            newRow.addClass('newRevenueGroupingItem');
            //remove the classes from the default row that cause it to be hidden and labeled
            newRow.removeClass('hide defaultRevenueGroupingItem');


            //add the new row to the table
            newRow = newRow.appendTo(table);
            newRow.find('input, select').each(function (idx, ele) {
                jQuery(ele).attr('name', jQuery(ele).attr('name') + '_' + sequence);
                jQuery(ele).attr('id', jQuery(ele).attr('id') + '_' + sequence);

                if (jQuery(ele).is('select')) {
                    jQuery(ele).addClass('chzn-select');
                    jQuery(ele).css('width', '150px')
                } else {
                    jQuery(ele).css('width', '150px')
                }
            });
            //notifiy the js library that handles the reformating the ui has changed
            newRow.find('.chzn-select').chosen();
            jQuery(document).find('select[name^="revenuegroupingitems_sequence_"]').trigger("change");
        };
        button.on('click', addHandler);
    },


    registerEvents: function () {
        // Update field name
        jQuery(document).find('tr.revenuegroupingitemRow').each(function (i, tre) {
            var tr = jQuery(tre);
            if (!tr.hasClass('hide')) {
                var sequence = tr.find('.row_num').val();
                tr.find('input, select').each(function (idx, ele) {
                    jQuery(ele).attr('name', jQuery(ele).attr('name') + '_' + sequence);
                    jQuery(ele).attr('id', jQuery(ele).attr('id') + '_' + sequence);
                    if (jQuery(ele).is('select')) {
                        jQuery(ele).css('width', '150px')
                    } else {
                        jQuery(ele).css('width', '150px')
                    }

                });
            }
        });
        var sequenceNode = jQuery("input[name='numAgents']");
        var sequence = jQuery(".revenuegroupingitemRow:gt(0)").length;
        sequenceNode.val(sequence);
        var container = jQuery('table[name="RevenueGroupingItemTable"]');
        this.registerAddRevenueGroupingItemButtons();
        this.registerRemoveRevenueGroupingItemButton();
        this.registerEditBlockRevenueGrouping();
        this.registerDeleteBlockRevenueGrouping();
    }
});

jQuery(document).ready(function () {
    var instance = RevenueGroupingItem_EditBlock_Js.getInstance();
    instance.registerEvents();
});