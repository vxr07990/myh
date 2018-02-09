<?php
require_once 'include/utils/utils.php';
require_once 'include/utils/VtlibUtils.php';
require_once 'modules/Emails/class.phpmailer.php';
require_once 'modules/Emails/mail.php';
require_once 'modules/Vtiger/helpers/ShortURL.php';

function emailAllUsers()
{
    echo 'Sending out reset password emails. <br />';
    $db = PearDatabase::getInstance();
    $users = $db->pquery('SELECT user_name, email1 FROM `vtiger_users`', []);
    while ($user =& $users->fetchRow()) {
        if ($user['user_name'] != 'admin') {
            $time = time();
            $options = array(
                'handler_path' => 'modules/Users/handlers/ForgotPassword.php',
                'handler_class' => 'Users_ForgotPassword_Handler',
                'handler_function' => 'changePassword',
                'handler_data' => array(
                    'username' => $user['user_name'],
                    'email' => $user['email1'],
                    'time' => $time,
                    'hash' => md5($user['user_name'] . $time)
                )
            );
            $trackURL = Vtiger_ShortURL_Helper::generateURL($options);

            $instanceName = getenv('IGC_MOVEHQ') == 1 ? 'moveHQ' : 'moveCRM';

            $content = 'Thank you for setting up a new '.$instanceName.' login. The username for your account is <b>'.$user['user_name'].'</b>.<br><br>
						To create a password for your account and login for the first time, please click on the link <a href="'.$trackURL.'">here</a>. Please note the link to create your password will expire 24 hours after this email was sent.<br><br>
						Thanks,<br>'.$instanceName.' Support Team<br>';
            $mail = new PHPMailer();

            setMailerProperties($mail, 'New '.$instanceName.' Login Details', $content, getenv('SUPPORT_EMAIL_ADDRESS'), $instanceName.' Support', $user['email1']);
            $status = MailSend($mail);
        }
    }
    echo 'Emails sent to all users <br />';
}
