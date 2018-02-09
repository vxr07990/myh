<?php
if (function_exists("call_ms_function_ver")) {
    $version = 1;
    if (call_ms_function_ver(__FILE__, $version)) {
        //already ran
        print "\e[33mSKIPPING: " . __FILE__ . "<br />\n\e[0m";
        return;
    }
}
print "\e[32mRUNNING: " . __FILE__ . "<br />\n\e[0m";



/*include_once('vtlib/Vtiger/Menu.php');
include_once('vtlib/Vtiger/Module.php');
include_once('modules/ModTracker/ModTracker.php');
include_once('modules/ModComments/ModComments.php');*/

function updateTemplateBody($templateName, $body)
{
    Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_emailtemplates` SET body = '$body' WHERE templatename = '$templateName'");
}

function updateTemplateSubject($templateName, $subject)
{
    Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_emailtemplates` SET subject = '$subject' WHERE templatename = '$templateName'");
}

echo 'begin default email template alteration script.<br>';

$templates = array(
    'Announcement for Release' => '<html>
		<head>
			<title></title>
		</head>
		<body class="scayt-enabled">
		<div style="border:2px solid #204e81;">
		<div style="background-color:#eeeff2;padding:10px;"><span style="font-weight:bold;">$logo$ </span>
		<h3><span style="font-weight:bold;">$software_name$</span></h3>
		<span style="font-weight:bold;"> </span></div>

		<div style="padding:10px;background-color:#fafafb;">Hello!<br />
		<br />
		On behalf of the $software_name$ team, I am pleased to announce the release of $software_name$ 2.0 . This is a feature packed release including X, Y, Z,&nbsp;and a host of other utilities. $software_name$ runs on all platforms.<br />
		<br />
		Notable Features of $software_name$ are :<br />
		<br />
		-IJKL<br />
		-MNOP<br />
		-QRST<br />
		<br />
		Known Issues:<br />
		-ABCD<br />
		-EFGH<br />
		&nbsp;</div>

		<div style="background-color:#204e81;padding:5px;vertical-align:middle">
		<p align="center" style="color:#ffffff;font-size:.9em;font-family:Arial, Helvetica, sans-serif;line-height:11px;"><span style="font-weight:bold;">Powered by $software_name$ $version_num$   &copy; $year$ <a href="http://$developer_site$" style="color:#ffffff;font-size:.9em;font-family:Arial, Helvetica, sans-serif;line-height:11px;text-decoration:none;">$developer_name$</a></span></p>
		</div>
		</div>
		</body>
		</html>',
    'Pending Invoices' => '<html>
		<head>
			<title></title>
		</head>
		<body class="scayt-enabled">
		<div style="border:2px solid #204e81;">

		<div style="padding:10px;background-color:#fafafb;">
		street,<br />
		city,<br />
		state,<br />
		zip<br />
		<br />
		Dear<br />
		<br />
		Please check the following invoices that are yet to be paid by you:<br />
		<br />
		No. Date Amount<br />
		1 1/1/01 $4000<br />
		2 2/2//01 $5000<br />
		3 3/3/01 $10000<br />
		4 7/4/01 $23560<br />
		<br />
		Kindly let us know if there are any issues that you feel are pending to be discussed.<br />
		We will be more than happy to give you a call.<br />
		We appreciate your continued business.<br><br>
		</div>

		<div style="background-color:#204e81;padding:5px;vertical-align:middle">
		<p align="center" style="color:#ffffff;font-size:.9em;font-family:Arial, Helvetica, sans-serif;line-height:11px;"><span style="font-weight:bold;">Powered by $software_name$ $version_num$   &copy; $year$ <a href="http://$developer_site$" style="color:#ffffff;font-size:.9em;font-family:Arial, Helvetica, sans-serif;line-height:11px;text-decoration:none;">$developer_name$</a></span></p>
		</div>
		</div>
		</body>
		</html>',
    'Acceptance Proposal' => '<html>
		<head>
			<title></title>
		</head>
		<body class="scayt-enabled">
		<div style="border:2px solid #204e81;">

		<div style="padding:10px;background-color:#fafafb;">Dear<br />
		<br />
		Your proposal on the project WXYZ has been reviewed by us<br />
		and is acceptable in its entirety.<br />
		<br />
		We are eagerly looking forward to this project<br />
		and are pleased about having the opportunity to work<br />
		together. We look forward to a long standing relationship<br />
		with your esteemed firm.<br />
		<br />
		I would like to take this opportunity to invite you<br />
		to lunch.<br />
		<br />
		Looking forward to seeing you there.<br />
		&nbsp;</div>

		<div style="background-color:#204e81;padding:5px;vertical-align:middle">
		<p align="center" style="color:#ffffff;font-size:.9em;font-family:Arial, Helvetica, sans-serif;line-height:11px;"><span style="font-weight:bold;">Powered by $software_name$ $version_num$   &copy; $year$ <a href="http://$developer_site$" style="color:#ffffff;font-size:.9em;font-family:Arial, Helvetica, sans-serif;line-height:11px;text-decoration:none;">$developer_name$</a></span></p>
		</div>
		</div>
		</body>
		</html>',
    'Goods received acknowledgement' => '<html>
		<head>
			<title></title>
		</head>
		<body class="scayt-enabled">
		<div style="border:2px solid #204e81;">

		<div style="padding:10px;background-color:#fafafb;">
		The undersigned hereby acknowledges receipt and delivery of the goods.<br />
		The undersigned will release the payment subject to the goods being discovered not satisfactory.<br />
		<br />
		Signed under seal this <date>$date$</date><br><br>
		</div>

		<div style="background-color:#204e81;padding:5px;vertical-align:middle">
		<p align="center" style="color:#ffffff;font-size:.9em;font-family:Arial, Helvetica, sans-serif;line-height:11px;"><span style="font-weight:bold;">Powered by $software_name$ $version_num$   &copy; $year$ <a href="http://$developer_site$" style="color:#ffffff;font-size:.9em;font-family:Arial, Helvetica, sans-serif;line-height:11px;text-decoration:none;">$developer_name$</a></span></p>
		</div>
		</div>
		</body>
		</html>',
    'Accept Order' => '<html>
		<head>
			<title></title>
		</head>
		<body class="scayt-enabled">
		<div style="border:2px solid #204e81;">

		<div style="padding:10px;background-color:#fafafb;">Dear<br />
		<br />
		We are in receipt of your order as contained in the<br />
		purchase order form. We consider this to be final and binding on both sides.<br />
		If there be any exceptions noted, we shall consider them<br />
		only if the objection is received within ten days of receipt of<br />
		this notice.<br />
		<br />
		Thank you for your patronage.<br />
		&nbsp;</div>

		<div style="background-color:#204e81;padding:5px;vertical-align:middle">
		<p align="center" style="color:#ffffff;font-size:.9em;font-family:Arial, Helvetica, sans-serif;line-height:11px;"><span style="font-weight:bold;">Powered by $software_name$ $version_num$   &copy; $year$ <a href="http://$developer_site$" style="color:#ffffff;font-size:.9em;font-family:Arial, Helvetica, sans-serif;line-height:11px;text-decoration:none;">$developer_name$</a></span></p>
		</div>
		</div>
		</body>
		</html>',
    'Address Change' => '<html>
		<head>
			<title></title>
		</head>
		<body class="scayt-enabled">
		<div style="border:2px solid #204e81;">

		<div style="padding:10px;background-color:#fafafb;">Dear<br />
		<br />
		We are relocating our office to<br />
		1111, ABCDEF,<br />
		GHIJKL Street<br />
		The telephone number for this new location is (555) 555-5555.<br />
		<br />
		Our Manufacturing Division will continue operations<br />
		at 2222 MNOPQR Avenue, in STUVWX.<br />
		<br />
		We hope to keep in touch with you all.<br />
		Please update your addressbooks.<br />
		<br />
		&nbsp;</div>

		<div style="background-color:#204e81;padding:5px;vertical-align:middle">
		<p align="center" style="color:#ffffff;font-size:.9em;font-family:Arial, Helvetica, sans-serif;line-height:11px;"><span style="font-weight:bold;">Powered by $software_name$ $version_num$   &copy; $year$ <a href="http://$developer_site$" style="color:#ffffff;font-size:.9em;font-family:Arial, Helvetica, sans-serif;line-height:11px;text-decoration:none;">$developer_name$</a></span></p>
		</div>
		</div>
		</body>
		</html>',
    'Follow Up' => '<html>
		<head>
			<title></title>
		</head>
		<body class="scayt-enabled">
		<div style="border:2px solid #204e81;">

		<div style="padding:10px;background-color:#fafafb;">Dear<br />
		<br />
		Thank you for extending us the opportunity to meet with<br />
		you and members of your staff.<br />
		<br />
		I know that John Doe serviced your account<br />
		for many years and made many friends at your firm. He has personally<br />
		discussed with me the strong bond that he had with your firm.<br />
		While his presence will be missed, I can promise that we will<br />
		continue to provide the fine service that John delivered&nbsp;to<br />
		your firm.<br />
		<br />
		I was genuinely glad to receive such fine hospitality.<br />
		<br />
		Thank you once again.<br />
		&nbsp;</div>

		<div style="background-color:#204e81;padding:5px;vertical-align:middle">
		<p align="center" style="color:#ffffff;font-size:.9em;font-family:Arial, Helvetica, sans-serif;line-height:11px;"><span style="font-weight:bold;">Powered by $software_name$ $version_num$   &copy; $year$ <a href="http://$developer_site$" style="color:#ffffff;font-size:.9em;font-family:Arial, Helvetica, sans-serif;line-height:11px;text-decoration:none;">$developer_name$</a></span></p>
		</div>
		</div>
		</body>
		</html>',
    'Target Crossed!' => '<html>
		<head>
			<title></title>
		</head>
		<body class="scayt-enabled">
		<div style="border:2px solid #204e81;">

		<div style="padding:10px;background-color:#fafafb;">Congratulations!<br />
		<br />
		The numbers are in and I am proud to inform you that our<br />
		total sales for the previous quarter<br />
		amounts to $100,000,000.00! This is the first time<br />
		we have exceeded the target by 30%.<br />
		We have also beat the previous quarter record by a<br />
		whopping 70%!<br />
		<br />
		Keep up the good work!<br />
		&nbsp;</div>

		<div style="background-color:#204e81;padding:5px;vertical-align:middle">
		<p align="center" style="color:#ffffff;font-size:.9em;font-family:Arial, Helvetica, sans-serif;line-height:11px;"><span style="font-weight:bold;">Powered by $software_name$ $version_num$   &copy; $year$ <a href="http://$developer_site$" style="color:#ffffff;font-size:.9em;font-family:Arial, Helvetica, sans-serif;line-height:11px;text-decoration:none;">$developer_name$</a></span></p>
		</div>
		</div>
		</body>
		</html>',
    'Thanks Note' => '<html>
		<head>
			<title></title>
		</head>
		<body class="scayt-enabled">
		<div style="border:2px solid #204e81;">

		<div style="padding:10px;background-color:#fafafb;">
		<p>Dear<br />
		<br />
		Thank you for your confidence in our service.<br />
		We appreciate your business and I look<br />
		forward to establishing our partnership.<br />
		Should any need arise, please give us a call.</p>
		<span style="font-weight:bold;"><strong style="padding:2px;font-family:Arial, Helvetica, sans-serif;font-size:12px;color:rgb(0,0,0);font-weight:bold;">Sincerly,</strong></span><br />
		&nbsp;</div>

		<div style="background-color:#204e81;padding:5px;vertical-align:middle">
		<p align="center" style="color:#ffffff;font-size:.9em;font-family:Arial, Helvetica, sans-serif;line-height:11px;"><span style="font-weight:bold;">Powered by $software_name$ $version_num$   &copy; $year$ <a href="http://$developer_site$" style="color:#ffffff;font-size:.9em;font-family:Arial, Helvetica, sans-serif;line-height:11px;text-decoration:none;">$developer_name$</a></span></p>
		</div>
		</div>
		</body>
		</html>',
    'Customer Login Details' => '<html>
		<head>
			<title></title>
		</head>
		<body class="scayt-enabled">
		<div style="border:2px solid #204e81;">
		<div style="background-color:#eeeff2;padding:10px;"><span style="font-weight:bold;">$logo$ </span>
		<span style="font-weight:bold;"> </span></div>

		<div style="padding:10px;background-color:#fafafb;">
		<div style="font-family: Arial,Helvetica,sans-serif; font-size: 14px; color: rgb(22, 72, 134); font-weight: bolder; line-height: 15px;">Dear $contact_name$,</div>
		<p>Thank you for subscribing to the $software_name$ - annual support service.<br>
		Here is your self service portal login details:</p><br><br>
		<table border="0" cellpadding="10" cellspacing="0" align="center" style="border: 2px solid rgb(180, 180, 179); background-color: rgb(226, 226, 225); font-family: Arial,Helvetica,sans-serif; font-size: 12px; color: rgb(0, 0, 0); font-weight: normal;" width="75%">
			<tbody>
				<tr>
					<td><br />
					User ID : <font color="#990000"><strong> $login_name$</strong></font></td>
				</tr>
				<tr>
					<td>Password: <font color="#990000"><strong> $password$</strong></font></td>
				</tr>
				<tr>
					<td align="center"><strong>$URL$</strong></td>
				</tr>
			</tbody>
		</table>
		<br>
		<table>
			<tbody>
				<tr>
					<td style="font-family: Arial,Helvetica,sans-serif; font-size: 12px; color: rgb(0, 0, 0); font-weight: normal; text-align: justify; line-height: 20px;"><strong>NOTE:</strong> We suggest you to change your password after logging in first time.<br />
					<br />
					<strong><u>Help Documentation</u></strong><br />
					<br />
					After logging in to the $software_name$ Self-service Portal first time, you can access $software_name$ documents from the <strong>Documents</strong> tab. Following documents are available for your reference:
					<ul>
						<li>Installation Manual (Windows &amp; Linux OS)</li>
						<li>User &amp; Administrator Manual</li>
						<li>$software_name$ Customer Portal - User Manual</li>
						<li>$software_name$ Outlook Plugin - User Manual</li>
						<li>$software_name$ Office Plug-in - User Manual</li>
						<li>$software_name$ Thunderbird Extension - User Manual</li>
						<li>$software_name$ Web Forms - User Manual</li>
						<li>$software_name$ Firefox Tool bar - User Manual</li>
					</ul>
					<br />
					<br />
					<strong><u>Knowledge Base</u></strong><br />
					<br />
					Periodically we update frequently asked question based on our customer experiences. You can access the latest articles from the <strong>FAQ</strong> tab.<br />
					<br />
					<strong><u>$software_name$ - Details</u></strong><br />
					<br />
					Kindly let us know your current $software_name$ version and system specification so that we can provide you necessary guidelines to enhance your $software_name$ system performance. Based on your system specification we alert you about the latest security &amp; upgrade patches.<br />
					<br />
					Thank you once again and wish you a wonderful experience with $software_name$.</td>
				</tr>
			</tbody>
		</table>
			<div align="right" style="text-align:right">
				<br />
				<br />
				<strong align="right" style="padding: 2px; font-family: Arial,Helvetica,sans-serif; font-size: 12px; color: rgb(0, 0, 0); font-weight: bold;">Best Regards</strong><br>
				<div align="right" style="font-family: Arial,Helvetica,sans-serif; font-size: 12px; color: rgb(0, 0, 0); font-weight: normal; line-height: 20px;">$support_team$</div><br>
				<div align="right"><a href="http://$website$" style="font-family: Arial,Helvetica,sans-serif; font-size: 12px; font-weight: bolder; text-decoration: none; color: rgb(66, 66, 253);">$website$</a></div><br>
				</tr>
			</div>
		</div>

		<div style="background-color:#204e81;padding:5px;vertical-align:middle">
		<p align="center" style="color:#ffffff;font-size:.9em;font-family:Arial, Helvetica, sans-serif;line-height:11px;"><span style="font-weight:bold;">Powered by $software_name$ $version_num$   &copy; $year$ <a href="http://$developer_site$" style="color:#ffffff;font-size:.9em;font-family:Arial, Helvetica, sans-serif;line-height:11px;text-decoration:none;">$developer_name$</a></span></p>
		</div>
		</div>
		</body>
		</html>',
    'Support end notification before a week' => '<html>
		<head>
			<title></title>
		</head>
		<body>
		<div style="border:2px solid #204e81;">
		<div style="background-color:#eeeff2;padding:10px;"><span style="font-weight:bold;">$logo$ </span>
		<span style="font-weight:bold;"> </span></div>

		<div style="padding:10px;background-color:#fafafb;">This is just a notification mail regarding your support end.<br />
		<span style="font-weight:bold;">Priority:</span> Urgent<br />
		Your Support is going to expire next week<br />
		Please contact <a href="mailto:$support_email$">$support_email$</a>.
		<div align="right"><br />
		<span style="font-weight:bold;"><strong style="padding:2px;font-family:Arial, Helvetica, sans-serif;font-size:12px;color:rgb(0,0,0);font-weight:bold;">Sincerly,</strong><br />
		$support_team$<br />
		<a href="http://$website$" style="font-family:Arial, Helvetica, sans-serif;font-size:12px;font-weight:bolder;text-decoration:none;color:rgb(66,66,253);">$website$</a></span></div>
		<span style="font-weight:bold;"> &nbsp;</span></div>

		<div style="background-color:#204e81;padding:5px;vertical-align:middle">
		<p align="center" style="color:#ffffff;font-size:.9em;font-family:Arial, Helvetica, sans-serif;line-height:11px;"><span style="font-weight:bold;">Powered by $software_name$ $version_num$   &copy; $year$ <a href="http://$developer_site$" style="color:#ffffff;font-size:.9em;font-family:Arial, Helvetica, sans-serif;line-height:11px;text-decoration:none;">$developer_name$</a></span></p>
		</div>
		</div>
		</body>
		</html>',
    'Support end notification before a month' => '<html>
		<head>
			<title></title>
		</head>
		<body>
		<div style="border:2px solid #204e81;">
		<div style="background-color:#eeeff2;padding:10px;"><span style="font-weight:bold;">$logo$ </span>
		<span style="font-weight:bold;"> </span></div>

		<div style="padding:10px;background-color:#fafafb;">This is just a notification mail regarding your support end.<br />
		<span style="font-weight:bold;">Priority:</span> Normal<br />
		Your Support is going to expire next month<br />
		Please contact <a href="mailto:$support_email$">$support_email$</a>.
		<div align="right"><br />
		<span style="font-weight:bold;"><strong style="padding:2px;font-family:Arial, Helvetica, sans-serif;font-size:12px;color:rgb(0,0,0);font-weight:bold;">Sincerly,</strong><br />
		$support_team$<br />
		<a href="http://$website$" style="font-family:Arial, Helvetica, sans-serif;font-size:12px;font-weight:bolder;text-decoration:none;color:rgb(66,66,253);">$website$</a></span></div>
		<span style="font-weight:bold;"> &nbsp;</span></div>

		<div style="background-color:#204e81;padding:5px;vertical-align:middle">
		<p align="center" style="color:#ffffff;font-size:.9em;font-family:Arial, Helvetica, sans-serif;line-height:11px;"><span style="font-weight:bold;">Powered by $software_name$ $version_num$   &copy; $year$ <a href="http://$developer_site$" style="color:#ffffff;font-size:.9em;font-family:Arial, Helvetica, sans-serif;line-height:11px;text-decoration:none;">$developer_name$</a></span></p>
		</div>
		</div>
		</body>
		</html>',
);

echo 'templates array created<br>';

foreach ($templates as $name => $body) {
    echo 'altering template: '.$name.'<br>';
    updateTemplateBody($name, $body);
    echo $name.' body has been updated!<br>';
    if ($name == 'Support end notification before a week' || $name == 'Support end notification before a month') {
        updateTemplateSubject($name, '$software_name$ Support Notification');
        echo $name.' subject has been updated!<br>';
    }
}

echo 'all email templates updated.<br>';
echo 'script completed.<br>';


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";