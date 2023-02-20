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
define('HESK_PATH','../');

/* Get all the required files and functions */
require(HESK_PATH . 'hesk_settings.inc.php');
require(HESK_PATH . 'inc/common.inc.php');
require(HESK_PATH . 'inc/database.inc.php');

hesk_session_start();
hesk_dbConnect();
hesk_isLoggedIn();

$hesk_error_buffer = array();

$tmpvar['name']	    = hesk_input($_POST['name']) or $hesk_error_buffer[]=$hesklang['enter_your_name'];
$tmpvar['email']	= hesk_validateEmail($_POST['email'],'ERR',0) or $hesk_error_buffer[]=$hesklang['enter_valid_email'];
$tmpvar['category'] = hesk_input($_POST['category']) or $hesk_error_buffer[]=$hesklang['sel_app_cat'];
$tmpvar['priority'] = intval($_POST['priority']) or $hesk_error_buffer[]=$hesklang['sel_app_priority'];
$tmpvar['subject']  = hesk_input($_POST['subject']) or $hesk_error_buffer[]=$hesklang['enter_ticket_subject'];
$tmpvar['message']  = hesk_input($_POST['message']) or $hesk_error_buffer[]=$hesklang['enter_message'];

/* Custom fields */
foreach ($hesk_settings['custom_fields'] as $k=>$v)
{
	if ($v['use'] && isset($_POST[$k]))
    {
       	if (is_array($_POST[$k]))
           {
			$tmpvar[$k]='';
			foreach ($_POST[$k] as $myCB)
			{
				$tmpvar[$k].=hesk_input($myCB).'<br />';
			}
			$tmpvar[$k]=substr($tmpvar[$k],0,-6);
           }
           else
           {
    		$tmpvar[$k]=hesk_makeURL(nl2br(hesk_input($_POST[$k])));
           }
	}
    else
    {
    	$tmpvar[$k] = '';
    }
}

/* Generate tracking ID and make sure it's not a duplicate one */
$useChars = 'AEUYBDGHJLMNPQRSTVWXZ123456789';
$trackingID = $useChars{mt_rand(0,29)};
for($i=1;$i<10;$i++)
{
	$trackingID .= $useChars{mt_rand(0,29)};
}

/* Check for duplicate Tracking ID. Small chance, but on some servers... */
$sql = "SELECT `id` FROM `".hesk_dbEscape($hesk_settings['db_pfix'])."tickets` WHERE `trackid` = '".hesk_dbEscape($trackingID)."' LIMIT 1";
$res = hesk_dbQuery($sql);

if (hesk_dbNumRows($res) != 0)
{
	/* Tracking ID not unique, let's try another way */
	$trackingID  = $useChars[mt_rand(0,29)];
	$trackingID .= $useChars[mt_rand(0,29)];
	$trackingID .= $useChars[mt_rand(0,29)];
	$trackingID .= $useChars[mt_rand(0,29)];
	$trackingID .= $useChars[mt_rand(0,29)];
	$trackingID .= substr(microtime(), -5);

	$sql = "SELECT `id` FROM `".hesk_dbEscape($hesk_settings['db_pfix'])."tickets` WHERE `trackid` = '".hesk_dbEscape($trackingID)."' LIMIT 1";
	$res = hesk_dbQuery($sql);

	if (hesk_dbNumRows($res) != 0)
	{
    	$hesk_error_buffer[]=$hesklang['e_tid'];
	}
}

/* Owner */
$owner = 0;
if (hesk_checkPermission('can_assign_others',0))
{
	$owner = intval($_REQUEST['owner']);

	/* If ID is -1 the ticket will be unassigned */
	if ($owner == -1)
	{
		$owner = 0;
	}
	elseif ($owner < 1)
	{
	    $owner = 0;
	}
    else
    {
	    /* Has the new owner access to the selected category? */
		$sql = "SELECT `name`,`isadmin`,`categories` FROM `".hesk_dbEscape($hesk_settings['db_pfix'])."users` WHERE `id`=".hesk_dbEscape($owner)." LIMIT 1";
		$res = hesk_dbQuery($sql);
	    if (hesk_dbNumRows($res) == 1)
	    {
	    	$row = hesk_dbFetchAssoc($res);
	        if (!$row['isadmin'])
	        {
				$row['categories']=explode(',',$row['categories']);
				if (!in_array($tmpvar['category'],$row['categories']))
				{
					$hesk_error_buffer[]=$hesklang['onasc'];
				}
	        }
	    }
	    else
	    {
	    	$hesk_error_buffer[]=$hesklang['onasc'];
	    }
    }
}
elseif (hesk_checkPermission('can_assign_self',0) && hesk_okCategory($tmpvar['category'],0) && !empty($_POST['assing_to_self']))
{
	$owner = intval($_SESSION['id']);
}

/* Notify customer of the ticket? */
$notify = !empty($_POST['notify']) ? 1 : 0;

/* Show ticket after submission? */
$show = !empty($_POST['show']) ? 1 : 0;

/* If we have any errors lets store info in session to avoid re-typing everything */
if (count($hesk_error_buffer)!=0)
{
    $_SESSION['as_name']     = $_POST['name'];
    $_SESSION['as_email']    = $_POST['email'];
    $_SESSION['as_category'] = $_POST['category'];
    $_SESSION['as_priority'] = $_POST['priority'];
    $_SESSION['as_subject']  = $_POST['subject'];
    $_SESSION['as_message']  = $_POST['message'];
    $_SESSION['as_owner']    = $owner;
    $_SESSION['as_notify']   = $notify;
    $_SESSION['as_show']     = $show;

	foreach ($hesk_settings['custom_fields'] as $k=>$v)
	{
		if ($v['use'])
		{
			$_SESSION["as_$k"] = isset($_POST[$k]) ? $_POST[$k]: '';
		}
	}

    $tmp = '';
    foreach ($hesk_error_buffer as $error)
    {
        $tmp .= "<li>$error</li>\n";
    }
    $hesk_error_buffer = $tmp;

    $hesk_error_buffer = $hesklang['rfm'].'<br /><br /><ul>'.$hesk_error_buffer.'</ul>';
    hesk_process_messages($hesk_error_buffer,'new_ticket.php');
}

/* Attachments */
if ($hesk_settings['attachments']['use'])
{
    require_once(HESK_PATH . 'inc/attachments.inc.php');
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
        $sql = "INSERT INTO `".hesk_dbEscape($hesk_settings['db_pfix'])."attachments` (`ticket_id`,`saved_name`,`real_name`,`size`) VALUES ('".hesk_dbEscape($trackingID)."', '".hesk_dbEscape($myatt['saved_name'])."', '".hesk_dbEscape($myatt['real_name'])."', '".hesk_dbEscape($myatt['size'])."')";
        $result = hesk_dbQuery($sql);
        $myattachments .= hesk_dbInsertID() . '#' . $myatt['real_name'] .',';
    }
}

$revision = sprintf($hesklang['thist7'],hesk_date(),$_SESSION['name'].' ('.$_SESSION['user'].')');

$tmpvar['message']=hesk_makeURL($tmpvar['message']);
$tmpvar['message']=nl2br($tmpvar['message']);

$sql = "
INSERT INTO `".hesk_dbEscape($hesk_settings['db_pfix'])."tickets` (
`trackid`,`name`,`email`,`category`,`priority`,`subject`,`message`,`dt`,`lastchange`,`ip`,`status`,`owner`,`attachments`,`history`,`custom1`,`custom2`,`custom3`,`custom4`,`custom5`,`custom6`,`custom7`,`custom8`,`custom9`,`custom10`,`custom11`,`custom12`,`custom13`,`custom14`,`custom15`,`custom16`,`custom17`,`custom18`,`custom19`,`custom20`
)
VALUES (
'".hesk_dbEscape($trackingID)."',
'".hesk_dbEscape($tmpvar['name'])."',
'".hesk_dbEscape($tmpvar['email'])."',
'".hesk_dbEscape($tmpvar['category'])."',
'".hesk_dbEscape($tmpvar['priority'])."',
'".hesk_dbEscape($tmpvar['subject'])."',
'".hesk_dbEscape($tmpvar['message'])."',
NOW(),
NOW(),
'".hesk_dbEscape($_SERVER['REMOTE_ADDR'])."',
'0',
".hesk_dbEscape($owner).",
'".hesk_dbEscape($myattachments)."',
CONCAT(`history`,'".hesk_dbEscape($revision)."'),
'".hesk_dbEscape($tmpvar['custom1'])."',
'".hesk_dbEscape($tmpvar['custom2'])."',
'".hesk_dbEscape($tmpvar['custom3'])."',
'".hesk_dbEscape($tmpvar['custom4'])."',
'".hesk_dbEscape($tmpvar['custom5'])."',
'".hesk_dbEscape($tmpvar['custom6'])."',
'".hesk_dbEscape($tmpvar['custom7'])."',
'".hesk_dbEscape($tmpvar['custom8'])."',
'".hesk_dbEscape($tmpvar['custom9'])."',
'".hesk_dbEscape($tmpvar['custom10'])."',
'".hesk_dbEscape($tmpvar['custom11'])."',
'".hesk_dbEscape($tmpvar['custom12'])."',
'".hesk_dbEscape($tmpvar['custom13'])."',
'".hesk_dbEscape($tmpvar['custom14'])."',
'".hesk_dbEscape($tmpvar['custom15'])."',
'".hesk_dbEscape($tmpvar['custom16'])."',
'".hesk_dbEscape($tmpvar['custom17'])."',
'".hesk_dbEscape($tmpvar['custom18'])."',
'".hesk_dbEscape($tmpvar['custom19'])."',
'".hesk_dbEscape($tmpvar['custom20'])."'
)
";

$res = hesk_dbQuery($sql);

/* Ticket array for later use in e-mails */
$ticket = array(
	'name' 		=> hesk_msgToPlain($tmpvar['name'],1),
	'subject' 	=> hesk_msgToPlain($tmpvar['subject'],1),
	'trackid' 	=> $trackingID,
	'category' 	=> $tmpvar['category'],
	'priority' 	=> $tmpvar['priority'],
    'lastreplier' => hesk_msgToPlain($tmpvar['name'],1),
	'message' 	=> hesk_msgToPlain($tmpvar['message'],1),
    'owner'		=> $owner,
);
foreach ($hesk_settings['custom_fields'] as $k => $v)
{
	$ticket[$k] = $v['use'] ? hesk_msgToPlain($tmpvar[$k],1) : '';
}

/* Notify the customer about the ticket? */
if ($notify)
{
	/* Format e-mail message for customer */
	$msg = hesk_getEmailMessage('new_ticket',$ticket);

	/* Send e-mail */
	$headers = "From: $hesk_settings[noreply_mail]\n";
	$headers.= "Reply-to: $hesk_settings[noreply_mail]\n";
	$headers.= "Return-Path: $hesk_settings[webmaster_mail]\n";
	$headers.= "Content-type: text/plain; charset=".$hesklang['ENCODING'];
	@mail($tmpvar['email'],$hesklang['ticket_received'],$msg,$headers);
}

/* If ticket is assigned to someone notify them? */
if ($owner && $owner != intval($_SESSION['id']))
{
	$sql = "SELECT `email`,`isadmin`,`categories` FROM `".hesk_dbEscape($hesk_settings['db_pfix'])."users` WHERE `id`=".hesk_dbEscape($ticket['owner'])." AND `notify_assigned`='1'";
	$res = hesk_dbQuery($sql);

	if (hesk_dbNumRows($res))
	{
		$myuser = hesk_dbFetchAssoc($res);
		$admins[] = $myuser['email'];

		/* Format e-mail message */
		$msg = hesk_getEmailMessage('ticket_assigned_to_you',$ticket,1);

		/* Send e-mail to staff */
		$email=implode(',',$admins);
		$headers = "From: $hesk_settings[noreply_mail]\n";
		$headers.= "Reply-to: $hesk_settings[noreply_mail]\n";
		$headers.= "Return-Path: $hesk_settings[webmaster_mail]\n";
		$headers.= "Content-type: text/plain; charset=".$hesklang['ENCODING'];
		@mail($email,$hesklang['ticket_assigned_to_you'],$msg,$headers);
	}
}
/* Ticket unassigned, notify everyone that  */
elseif (!$owner)
{
	$admins=array();
	$sql = "SELECT `email`,`isadmin`,`categories` FROM `".hesk_dbEscape($hesk_settings['db_pfix'])."users` WHERE `id`!=".hesk_dbEscape($_SESSION['id'])." AND `notify_new_unassigned`='1'";
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
		if (in_array($tmpvar['category'],$myuser['categories']))
		{
			$admins[]=$myuser['email'];
			continue;
		}
	}
	if (count($admins)>0)
	{
		/* Format e-mail message for staff */
		$msg = hesk_getEmailMessage('new_ticket_staff',$ticket,1);

		/* Send e-mail to staff */
		$email=implode(',',$admins);
		$headers = "From: $hesk_settings[noreply_mail]\n";
		$headers.= "Reply-to: $hesk_settings[noreply_mail]\n";
		$headers.= "Return-Path: $hesk_settings[webmaster_mail]\n";
		$headers.= "Content-type: text/plain; charset=".$hesklang['ENCODING'];
		@mail($email,$hesklang['new_ticket_submitted'],$msg,$headers);
	}
}

/* Unset temporary variables */
unset($tmpvar);
hesk_cleanSessionVars('tmpvar');
hesk_cleanSessionVars('as_name');
hesk_cleanSessionVars('as_email');
hesk_cleanSessionVars('as_category');
hesk_cleanSessionVars('as_priority');
hesk_cleanSessionVars('as_subject');
hesk_cleanSessionVars('as_message');
hesk_cleanSessionVars('as_owner');
hesk_cleanSessionVars('as_notify');
hesk_cleanSessionVars('as_show');
foreach ($hesk_settings['custom_fields'] as $k=>$v)
{
	if ($v['use'])
	{
        hesk_cleanSessionVars("as_$k");
	}
}

/* Show the ticket or just the success message */
if ($show)
{
	hesk_process_messages($hesklang['new_ticket_submitted'],'admin_ticket.php?track='.$trackingID.'&Refresh='.mt_rand(10000,99999),'SUCCESS');
}
else
{
	hesk_process_messages($hesklang['new_ticket_submitted'].'. <a href="admin_ticket.php?track='.$trackingID.'&Refresh='.mt_rand(10000,99999).'">'.$hesklang['view_ticket'].'</a>','new_ticket.php','SUCCESS');
}
?>
