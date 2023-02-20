<?php
/*******************************************************************************
*  Title: Help Desk Software HESK
*  Version: 2.2 from 9th June 2010
*  Author: Klemen Stirn
*  Website: http://www.hesk.com
********************************************************************************
*  COPYRIGHT AND TRADEMARK NOTICE
*  Copyright 2005-2010 Klemen Stirn. All Rights Reserved.
*  HESK is a registered trademark of Klemen Stirn.

*  The HESK may be used and modified free of charge by anyone
*  AS LONG AS COPYRIGHT NOTICES AND ALL THE COMMENTS REMAIN INTACT.
*  By using this code you agree to indemnify Klemen Stirn from any
*  liability that might arise from it's use.

*  Selling the code for this program, in part or full, without prior
*  written consent is expressly forbidden.

*  Using this code, in part or full, to create derivate work,
*  new scripts or products is expressly forbidden. Obtain permission
*  before redistributing this software over the Internet or in
*  any other medium. In all cases copyright and header must remain intact.
*  This Copyright is in full effect in any country that has International
*  Trade Agreements with the United States of America or
*  with the European Union.

*  Removing any of the copyright notices without purchasing a license
*  is expressly forbidden. To remove HESK copyright notice you must purchase
*  a license for this script. For more information on how to obtain
*  a license please visit the page below:
*  https://www.hesk.com/buy.php
*******************************************************************************/

define('IN_SCRIPT',1);
define('HESK_PATH','./');

/* Get all the required files and functions */
require(HESK_PATH . 'hesk_settings.inc.php');
require(HESK_PATH . 'inc/common.inc.php');
require(HESK_PATH . 'inc/database.inc.php');

hesk_session_start();

/* A security check */
hesk_token_check($_POST['token']);

$hesk_error_buffer = array();

/* Tracking ID */
$trackingID  = hesk_input($_POST['orig_track']) or die($hesklang['int_error'].': No orig_track');
$trackingURL = $hesk_settings['hesk_url'].'/ticket.php?track='.$trackingID.'&Refresh='.rand(10000,99999);

/* Message entered? */
$message = hesk_input($_POST['message']);
if (!strlen($message))
{
	hesk_process_messages($hesklang['enter_message'],'ticket.php?track='.$trackingID.'&Refresh='.rand(10000,99999));
}

$message = hesk_makeURL($message);
$message = nl2br($message);

/* Connect to database */
hesk_dbConnect();

/* Get details about the original ticket */
$sql = "SELECT * FROM `".hesk_dbEscape($hesk_settings['db_pfix'])."tickets` WHERE `trackid`='".hesk_dbEscape($trackingID)."' LIMIT 1";
$result = hesk_dbQuery($sql);
if (hesk_dbNumRows($result) != 1)
{
	hesk_error($hesklang['ticket_not_found']);
}
$ticket = hesk_dbFetchAssoc($result);
$ticket['lastreplier'] = $ticket['name'];

/* Ticket locked? */
if ($ticket['locked'])
{
	hesk_process_messages($hesklang['tislock2'],'ticket.php?track='.$trackingID.'&Refresh='.rand(10000,99999));
	exit();
}

/* Attachments */
if ($hesk_settings['attachments']['use'])
{
    require(HESK_PATH . 'inc/attachments.inc.php');
    $attachments = array();
    for ($i=1;$i<=$hesk_settings['attachments']['max_number'];$i++)
    {
        $att = hesk_uploadFile($i);
        if (!empty($att))
        {
            $attachments[$i] = $att;
        }
    }
}
$myattachments='';

if ($hesk_settings['attachments']['use'] && !empty($attachments))
{
    foreach ($attachments as $myatt)
    {
        $sql = "INSERT INTO `".hesk_dbEscape($hesk_settings['db_pfix'])."attachments` (`ticket_id`,`saved_name`,`real_name`,`size`) VALUES (
        '".hesk_dbEscape($trackingID)."',
        '".hesk_dbEscape($myatt['saved_name'])."',
        '".hesk_dbEscape($myatt['real_name'])."',
        '".hesk_dbEscape($myatt['size'])."'
        )";
        $result = hesk_dbQuery($sql);
        $myattachments .= hesk_dbInsertID() . '#' . $myatt['real_name'] .',';
    }
}

/* Make sure the ticket is open */
$sql = "UPDATE `".hesk_dbEscape($hesk_settings['db_pfix'])."tickets` SET `status`='1',`lastreplier`='0',`lastchange`=NOW() WHERE `id`=".hesk_dbEscape($ticket['id'])." LIMIT 1";
$result = hesk_dbQuery($sql);

/* Add reply */
$sql = "
INSERT INTO `".hesk_dbEscape($hesk_settings['db_pfix'])."replies` (
`replyto`,`name`,`message`,`dt`,`attachments`
)
VALUES (
'".hesk_dbEscape($ticket['id'])."',
'".hesk_dbEscape($ticket['name'])."',
'".hesk_dbEscape($message)."',
NOW(),
'".hesk_dbEscape($myattachments)."'
)
";
$result = hesk_dbQuery($sql);

/* Need to notify any admins? */
$admins=array();

// --> if ticket is assigned just notify the owner
if ($ticket['owner'])
{
	$sql = "SELECT `email`,`isadmin`,`categories` FROM `".hesk_dbEscape($hesk_settings['db_pfix'])."users` WHERE `id`=".hesk_dbEscape($ticket['owner'])." AND `notify_reply_my`='1'";
}
else
{
    $sql = "SELECT `email`,`isadmin`,`categories` FROM `".hesk_dbEscape($hesk_settings['db_pfix'])."users` WHERE `notify_reply_unassigned`='1'";
}
$result = hesk_dbQuery($sql);
while ($myuser=hesk_dbFetchAssoc($result))
{
	/* Is this an administrator? */
	if ($myuser['isadmin'])
	{
		$admins[]=$myuser['email'];
		continue;
	}

	/* Not admin, is he allowed this category? */
	$myuser['categories']=explode(',',$myuser['categories']);
	if (in_array($ticket['category'],$myuser['categories']))
	{
		$admins[]=$myuser['email'];
		continue;
	}
}

if (count($admins)>0)
{
	/* Prepare ticket message for the e-mail */
	$ticket['message'] = hesk_msgToPlain($message,1);

	/* Format e-mail message */
	$msg = hesk_getEmailMessage('new_reply_by_customer',$ticket,1);

	/* Send e-mail to staff */
	$email=implode(',',$admins);
	$headers = "From: $hesk_settings[noreply_mail]\n";
	$headers.= "Reply-to: $hesk_settings[noreply_mail]\n";
	$headers.= "Return-Path: $hesk_settings[webmaster_mail]\n";
	$headers.= "Content-type: text/plain; charset=".$hesklang['ENCODING'];
	@mail($email,$hesklang['new_reply_ticket'],$msg,$headers);
}

/* Show the ticket and the success message */
hesk_process_messages($hesklang['reply_submitted_success'],'ticket.php?track='.$trackingID.'&Refresh='.rand(10000,99999),'SUCCESS');
exit();
?>
