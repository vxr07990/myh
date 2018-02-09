<?php
require_once('includes/main/WebUI.php');
require_once('include/Webservices/Create.php');
require_once('modules/Users/Users.php');

function vanlineImport()
{
    $db = PearDatabase::getInstance();
    echo 'Adding vanlines<br />';
    $vanlines = [
        [
            'vanline_id'       => 1,
            'vanline_name'     => 'Allied',
            'assigned_user_id' => vtws_getWebserviceEntityId('Users', Users::getActiveAdminId()),
            'local_report_url' => 'https://print.moverdocs.com/SIRVA/IGCReportingService.asmx?wsdl',
            'address1'         => 'P.O. Box 4403',
            'city'               => 'Chicago',
            'state'           => 'IL',
            'zip'                => '60680',
            'country'           => 'United States',
            'phone1'           => '800-470-2851',
            'website'           => 'http://www.allied.com',
        ],
        [
            'vanline_id'       => 9,
            'vanline_name'     => 'North American Van Lines',
            'assigned_user_id' => vtws_getWebserviceEntityId('Users', Users::getActiveAdminId()),
            'local_report_url' => 'https://print.moverdocs.com/SIRVA/IGCReportingService.asmx?wsdl',
            'address1'         => 'P.O. Box 988',
            'city'               => 'Ft. Wayne',
            'state'           => 'IN',
            'zip'                => '46801-0998',
            'country'           => 'United States',
            'phone1'           => '800-234-1127',
            'fax'              => '219-429-1853',
            'website'           => 'http://www.northamerican-vanlines.com',
        ],
    ];

    try {
        $user = new Users();
        $current_user = $user->retrieveCurrentUserInfoFromFile(Users::getActiveAdminId());
        foreach ($vanlines as $vanline) {
            $newAgent = vtws_create('VanlineManager', $vanline, $current_user);
        }
    } catch (WebServiceException $ex) {
        echo $ex->getMessage();
        echo "<br><br>";
        die;
    }
    echo 'Done adding vanlines<br />';
}
