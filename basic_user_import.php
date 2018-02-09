<?php

require_once('includes/main/WebUI.php');
require_once('include/Webservices/Create.php');
require_once('include/Webservices/DescribeObject.php');
require_once('modules/Users/Users.php');
require_once('modules/Users/views/Edit.php');
require_once 'include/utils/utils.php';
require_once 'include/utils/VtlibUtils.php';
require_once 'modules/Emails/class.phpmailer.php';
require_once 'modules/Emails/mail.php';
require_once 'modules/Vtiger/helpers/ShortURL.php';

//This ensures the script will only run in CLI
if (PHP_SAPI == 'cli') {
    userImport('userImport.csv');
}else{
    echo 'permission denied';
}

function userImport($filePath)
{
    echo "Start User Import \n";

    // These are the headers it will attempt to look for
    // This is not a complete list. Please add fields as needed.
   $expectedHeaders = [
        'Username' => [
            'csv_column' => 'Username',
            'crm_field'  => 'user_name',
        ],
        'Status' => [
            'csv_column' => 'Status',
            'crm_field'  => 'status',
        ],
        'Last Name' => [
            'csv_column' => 'Last Name',
            'crm_field'  => 'last_name',
        ],
        'First Name' => [
            'csv_column' => 'First Name',
            'crm_field'  => 'first_name',
        ],
        'Email' => [
            'csv_column' => 'Email',
            'crm_field'  => 'email1',
        ],
        'Work Phone' => [
            'csv_column' => 'Work Phone',
            'crm_field'  => 'phone_work',
        ],
        'Fax' => [
            'csv_column' => 'Fax',
            'crm_field'  => 'phone_fax',
        ],
        'Fax' => [
            'csv_column' => 'Fax',
            'crm_field'  => 'phone_fax',
        ],
        'Mobile' => [
            'csv_column' => 'Mobile',
            'crm_field'  => 'phone_mobile',
        ],
        'Time Zone' => [
            'csv_column' => 'Time Zone',
            'crm_field'  => 'time_zone',
        ],
        'Member of' => [
            'csv_column' => 'Member of',
            'crm_field'  => 'agent_ids',
        ],
        //This one is special
        'Role' => [
            'csv_column' => 'Role Name',
        ],
    ];

    //Override with sirva specific fields
    if(getenv('INSTANCE_NAME') == 'sirva'){
        $sirvaFields = [
            'Username' => [
                'csv_column' => 'QLAB User ID',
                'crm_field'  => 'user_name',
            ],
            'Status' => [
                'csv_column' => 'Status (Active/Inactive)',
                'crm_field'  => 'status',
            ],
            'Work Phone' => [
                'csv_column' => 'Work Ph',
                'crm_field'  => 'phone_work',
            ],
            'Time Zone' => [
                'csv_column' => 'Time Zone',
                'crm_field'  => 'time_zone',
            ],
            'Member of' => [
                'csv_column' => 'agency_code',
                'crm_field'  => 'agent_ids',
            ],
            'AVL STS Agent ID' => [
                'csv_column' => 'AVL STS Agent ID',
                'crm_field'  => 'sts_agent_id',
            ],
            'NVL STS Agent ID' => [
                'csv_column' => 'NVL STS Agent ID',
                'crm_field'  => 'sts_agent_id_nvl',
            ],
            'AVL STS Username' => [
                'csv_column' => 'AVL STS Username',
                'crm_field'  => 'sts_user_id',
            ],
            'NVL STS Username' => [
                'csv_column' => 'NVL STS Username',
                'crm_field'  => 'sts_user_id_nvl',
            ],
            'AVL AMC Salesperson ID' => [
                'csv_column' => 'AVL AMC Salesperson ID',
                'crm_field'  => 'sts_salesperson_avl',
            ],
            'NVL AMC Salesperson ID' => [
                'csv_column' => 'NVL AMC Salesperson ID',
                'crm_field'  => 'sts_salesperson_navl',
            ],
            'AVL MC ID' => [
                'csv_column' => 'AVL MC ID',
                'crm_field'  => 'move_coordinator',
            ],
            'NVL MC ID' => [
                'csv_column' => 'NVL MC ID',
                'crm_field'  => 'move_coordinator_navl',
            ],
            'AVL Party ID' =>[
                'csv_column' => 'AVL Party ID',
                'crm_field'  => 'amc_salesperson_id',
            ],
            'NVL Party ID' =>[
                'csv_column' => 'NVL Party ID',
                'crm_field'  => 'amc_salesperson_id_nvl',
            ],

        ];
        $expectedHeaders = array_merge($expectedHeaders, $sirvaFields);
    }

    //Process all the users
    $rosterList = fopen($filePath, 'r');

    $headers    = fgetcsv($rosterList);

    $users      = [];
    while ($csv = fgetcsv($rosterList)) {
        $user = array_combine($headers, $csv);

        $data = [];
        //Only get the expected data
        foreach ($expectedHeaders as $key => $field) {
            if($key == 'Role'){
                $data['roleid'] = Users_Edit_View::getRoleIdByName($user[$field['csv_column']]);
            }elseif($key == 'Member of'){
                //Only way to determine vanline or agent right now is with the role names. It may need to change later with new roles.
                $data['agent_ids'] = getAgentIds(
                    explode(',', str_replace(' ', '', $user[$field['csv_column']])),
                    ($user['Role Name'] != "Child Van Line User" && $user['Role Name'] != "Parent Van Line User")
                );
            }else{
                $data[$field['crm_field']] = $user[$field['csv_column']];
            }
        }

        //Some default data
        $data['user_password']    = randomPassword();
        $data['confirm_password'] = $data['user_password'];

        //Remove blank lines
        if($data['user_name'] == ''){
            continue;
        }

        try {
            $user = new Users();
            $current_user = $user->retrieveCurrentUserInfoFromFile(Users::getActiveAdminId());
            $newUser = vtws_create('Users', $data, $current_user);
            if($data['status'] != 'Inactive' && getenv('INSTANCE_NAME') != 'sirva'){
                emailUser($data['email1'], $data['user_name']);
            }
            echo "User '".$data['user_name']."' created successfully. \n";
        } catch (Exception $ex) {
            echo "Failed to create user '".$data['user_name']."'. Error:".$ex->getMessage()."\n";
        }
    }
}

function randomPassword()
{
    if(getenv('INSTANCE_NAME') == 'sirva'){
        return 'Welcome1#';
    }
    $alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
    $pass = array(); //remember to declare $pass as an array
    $alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
    for ($i = 0; $i < 8; $i++) {
        $n = rand(0, $alphaLength);
        $pass[] = $alphabet[$n];
    }
    return implode($pass); //turn the array into a string
}

function getAgentIds($agent_ids, $agent = true){
    $db = PearDatabase::getInstance();
    if($agent){
        $query = "SELECT GROUP_CONCAT(`agentmanagerid`) AS ids FROM `vtiger_agentmanager` WHERE `agency_code` IN (".generateQuestionMarks($agent_ids).")";
        $result = $db->pquery($query, $agent_ids);
        if ($db->num_rows($result)>0) {
            $row = $db->fetch_array($result);
            if($row['ids'] != ''){
                return str_replace(',',' |##| ', $row['ids']);
            }
        }
    }else{
        $query = "SELECT GROUP_CONCAT(`vanlinemanagerid`) AS ids FROM `vtiger_vanlinemanager` WHERE `vanline_id` IN (".generateQuestionMarks($agent_ids).")";
        $result = $db->pquery($query, $agent_ids);
        if ($db->num_rows($result)>0) {
            $row = $db->fetch_array($result);
            if($row['ids'] != ''){
                return str_replace(',',' |##| ', $row['ids']);
            }
        }
    }
    return;
}

function emailUser($email, $username)
{
    $time = time();
    $options = array(
        'handler_path' => 'modules/Users/handlers/ForgotPassword.php',
        'handler_class' => 'Users_ForgotPassword_Handler',
        'handler_function' => 'changePassword',
        'handler_data' => array(
            'username' => $username,
            'email' => $email,
            'time' => $time,
            'hash' => md5($user['user_name'] . $time)
        )
    );
    $trackURL = Vtiger_ShortURL_Helper::generateURL($options);

    $instanceName = getenv('IGC_MOVEHQ') == 1 ? 'moveHQ' : 'moveCRM';

    $content = 'Thank you for setting up a new '.$instanceName.' login. The username for your account is <b>'.$username.'</b>.<br><br>
                To create a password for your account and login for the first time, please click on the link <a href="'.$trackURL.'">here</a>. Please note the link to create your password will expire 24 hours after this email was sent.<br><br>
                Thanks,<br>'.$instanceName.' Support Team<br>';
    $mail = new PHPMailer();

    setMailerProperties($mail, 'New '.$instanceName.' Login Details', $content, getenv('SUPPORT_EMAIL_ADDRESS'), $instanceName.' Support', $email);
    $status = MailSend($mail);
}
