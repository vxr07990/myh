/**
 * Created by dbolin on 1/12/2017.
 */
jQuery.Class("GoogleCalculator_Js", {

    doUpdateFromAddresses: function(addressList, agent)
    {
        var params = {
            module: 'Estimates',
            action: 'GoogleMilesCalculator',
            list: addressList,
            agent: agent
        };
        jQuery('#googleCalculatorButton').addClass('hide');
        jQuery('#googleCalculatorButton').closest('td').progressIndicator();
        AppConnector.request(params).then(function (data) {
            jQuery('#googleCalculatorButton').removeClass('hide');
            jQuery('#googleCalculatorButton').closest('td').progressIndicator({'mode': 'hide'});
            if(data.success)
            {
                jQuery('#contentHolder_GOOGLE_CALCULATOR').replaceWith(data.result);
                jQuery('#contentHolder_GOOGLE_CALCULATOR').removeClass('hide');
            } else {
                Estimates_Edit_Js.I().showAlertBox(data.result);
            }
        }, function(err) {
            jQuery('#googleCalculatorButton').removeClass('hide');
            jQuery('#googleCalculatorButton').closest('td').progressIndicator({'mode': 'hide'});
            Estimates_Edit_Js.I().showAlertBox({'message': 'An error has occurred with the Google miles / travel time calculator.'});
        });
    }

},
    {
    }
);