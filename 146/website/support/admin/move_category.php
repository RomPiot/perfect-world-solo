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

/* Check permissions for this feature */
hesk_checkPermission('can_view_tickets');
hesk_checkPermission('can_reply_tickets');

/* A security check */
hesk_token_check($_POST['token']);

$trackingID = strtoupper(hesk_input($_POST['track'],"$hesklang[int_error]: $hesklang[no_trackID]."));
$category   = hesk_input($_POST['category']);
if (empty($category))
{
	hesk_process_messages($hesklang['your_kb_mod'],'admin_ticket.php?track='.$trackingID.'&Refresh='.rand(10000,99999));
}

/* Get new category name */
$sql = "SELECT `name` FROM `".hesk_dbEscape($hesk_settings['db_pfix'])."categories` WHERE `id`=".hesk_dbEscape($category)." LIMIT 1";
$res = hesk_dbQuery($sql);
if (hesk_dbNumRows($res) != 1)
{
	hesk_error("$hesklang[int_error]: $hesklang[trackID_not_found].");
}
$row = hesk_dbFetchAssoc($res);

$revision = sprintf($hesklang['thist1'],hesk_date(),$row['name'],$_SESSION['name'].' ('.$_SESSION['user'].')');
$sql = "UPDATE `".hesk_dbEscape($hesk_settings['db_pfix'])."tickets` SET `category`='".hesk_dbEscape($category)."' , `history`=CONCAT(`history`,'".hesk_dbEscape($revision)."') WHERE `trackid`='".hesk_dbEscape($trackingID)."' LIMIT 1";
$res = hesk_dbQuery($sql);

/* Need to notify any admins? */
$admins=array();
$sql = "SELECT `email`,`isadmin`,`categories` FROM `".hesk_dbEscape($hesk_settings['db_pfix'])."users` WHERE `notify_new_unassigned`='1' AND `id`!=".hesk_dbEscape($_SESSION['id']);
$result = hesk_dbQuery($sql);
while ($myuser=hesk_dbFetchAssoc($result))
{
    /* Is this an administrator? */
    if ($myuser['isadmin']) {$admins[]=$myuser['email']; continue;}
    /* Not admin, is he allowed this category? */
    $cat=substr($myuser['categories'], 0, -1);
    $myuser['categories']=explode(',',$cat);
    if (in_array($category,$myuser['categories']))
    {
        $admins[]=$myuser['email']; continue;
    }
}
if (count($admins)>0)
{
	/* Get details about the original ticket */
	$sql = "SELECT * FROM `".hesk_dbEscape($hesk_settings['db_pfix'])."tickets` WHERE `trackid`='".hesk_dbEscape($trackingID)."' LIMIT 1";
	$result = hesk_dbQuery($sql);
	if (hesk_dbNumRows($result) != 1)
	{
		hesk_error($hesklang['ticket_not_found']);
	}
	$ticket = hesk_dbFetchAssoc($result);

	/* Set last replier name */
	if ($ticket['lastreplier'])
	{
		if (empty($ticket['repliername']))
		{
			$ticket['repliername'] = $hesklang['staff'];
		}
	}
	else
	{
		$ticket['repliername'] = $ticket['name'];
	}

	/* Setup ticket message for e-mail */
	$ticket['message'] = hesk_msgToPlain($ticket['message'],1);

	/* Format e-mail message */
	$msg = hesk_getEmailMessage('category_moved',$ticket,1);

	/* Send e-mail */
    $email=implode(',',$admins);
	$headers = "From: $hesk_settings[noreply_mail]\n";
	$headers.= "Reply-to: $hesk_settings[noreply_mail]\n";
	$headers.= "Return-Path: $hesk_settings[webmaster_mail]\n";
	$headers.= "Content-type: text/plain; charset=".$hesklang['ENCODING'];
	@mail($email,$hesklang['ntmc'],$msg,$headers);
}

/* Is the user allowed to view tickets in the new category? */
if (hesk_okCategory($category,0))
{
	hesk_process_messages($hesklang['moved_to'],'admin_ticket.php?track='.$trackingID.'&Refresh='.rand(10000,99999),'SUCCESS');
}
else
{
    hesk_process_messages($hesklang['moved_to'],'admin_main.php','SUCCESS');
}
?>
