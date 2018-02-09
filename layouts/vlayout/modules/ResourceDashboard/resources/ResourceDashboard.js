$(document).ready(function() {

    $('#search').click(function() {

        var loadingMessage = jQuery('.listViewLoadingMsg').text();


        var progressIndicatorElement = jQuery.progressIndicator({
            'message': loadingMessage,
            'position': 'html',
            'blockInfo': {
                'enabled': true
            }
        });

        var resource_type = $('#resourceSel').val();
        var month = $('#monthSel').val();
        var year = $('#yearSel').val();

        $.ajax({
            type: "POST",
            url: "index.php?module=ResourceDashboard&action=UpdateDashboard",
            dataType: "json",
            data: {
                year: year,
                resource_type: resource_type,
                month: month
            },
            success: function(msg) {
                console.log(msg.result);
                $('.resourcedash').html(msg.result[0]);
                progressIndicatorElement.progressIndicator({
                    'mode': 'hide'
                })
            }
        });


    });

});