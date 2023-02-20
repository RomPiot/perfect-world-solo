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

/* Befor anything else block obvious spammers trying to inject email headers */
$pattern = "/\n|\r|\t|%0A|%0D|%08|%09/";
if (preg_match($pattern,$_POST['name']) || preg_match($pattern,$_POST['subject']))
{
	header('HTTP/1.1 403 Forbidden');
    exit();
}

hesk_session_start();

/* A security check */
hesk_token_check($_POST['token']);

/* Connect to database */
hesk_dbConnect();

$hesk_error_buffer = array();

if ($hesk_settings['question_use'])
{
	$question = hesk_input($_POST['question']);
	if (empty($question))
	{
		$hesk_error_buffer[]=$hesklang['q_miss'];
	}
	elseif (strtolower($question) != strtolower($hesk_settings['question_ans']))
	{
		$hesk_error_buffer[]=$hesklang['q_wrng'];
	}
	else
	{
		$_SESSION['c_question'] = $question;
	}
}

if ($hesk_settings['secimg_use'])
{
	$mysecnum = hesk_isNumber($_POST['mysecnum']);
	if (empty($mysecnum))
	{
		$hesk_error_buffer[]=$hesklang['sec_miss'];
	}
	else
	{
		require(HESK_PATH . 'secimg.inc.php');
		$sc = new PJ_SecurityImage($hesk_settings['secimg_sum']);
		if (!($sc->checkCode($mysecnum,$_SESSION['checksum'])))
		{
			$hesk_error_buffer[]=$hesklang['sec_wrng'];
		}
	}
}

$tmpvar['name']	    = hesk_input($_POST['name']) or $hesk_error_buffer[]=$hesklang['enter_your_name'];
$tmpvar['email']	= hesk_validateEmail($_POST['email'],'ERR',0) or $hesk_error_buffer[]=$hesklang['enter_valid_email'];
$tmpvar['category'] = hesk_input($_POST['category']) or $hesk_error_buffer[]=$hesklang['sel_app_cat'];
$tmpvar['priority'] = ($hesk_settings['cust_urgency'] ? intval($_POST['priority']) : 3) or $hesk_error_buffer[]=$hesklang['sel_app_priority'];
$tmpvar['subject']  = hesk_input($_POST['subject']) or $hesk_error_buffer[]=$hesklang['enter_ticket_subject'];
$tmpvar['message']  = hesk_input($_POST['message']) or $hesk_error_buffer[]=$hesklang['enter_message'];

/* Custom fields */
foreach ($hesk_settings['custom_fields'] as $k=>$v)
{
	if ($v['use'])
    {
        if ($v['type'] == 'checkbox')
        {
			$tmpvar[$k]='';

        	if (isset($_POST[$k]))
            {
				if (is_array($_POST[$k]))
				{
					foreach ($_POST[$k] as $myCB)
					{
						$tmpvar[$k].=hesk_input($myCB).'<br />';
					}
					$tmpvar[$k]=substr($tmpvar[$k],0,-6);
				}
            }
            else
            {
            	if ($v['req'])
                {
					$hesk_error_buffer[]=$hesklang['fill_all'].': '.$v['name'];
                }
            	$_POST[$k] = '';
            }
        }
		elseif ($v['req'])
        {
        	$tmpvar[$k]=hesk_makeURL(nl2br(hesk_input($_POST[$k]))) or $hesk_error_buffer[]=$hesklang['fill_all'].': '.$v['name'];
        }
		else
        {
        	$tmpvar[$k]=hesk_makeURL(nl2br(hesk_input($_POST[$k])));
        }
		$_SESSION["c_$k"]=$_POST[$k];
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

/* If we have any errors lets store info in session to avoid re-typing everything */
if (count($hesk_error_buffer)!=0)
{
    $_SESSION['c_name']     = $_POST['name'];
    $_SESSION['c_email']    = $_POST['email'];
    $_SESSION['c_category'] = $_POST['category'];
    $_SESSION['c_priority'] = isset($_POST['priority']) ? $_POST['priority'] : '';
    $_SESSION['c_subject']  = $_POST['subject'];
    $_SESSION['c_message']  = $_POST['message'];

    $tmp = '';
    foreach ($hesk_error_buffer as $error)
    {
        $tmp .= "<li>$error</li>\n";
    }
    $hesk_error_buffer = $tmp;

    $hesk_error_buffer = $hesklang['rfm'].'<br /><br /><ul>'.$hesk_error_buffer.'</ul>';
    hesk_process_messages($hesk_error_buffer,'index.php?a=add');
}

$tmpvar['message']=hesk_makeURL($tmpvar['message']);
$tmpvar['message']=nl2br($tmpvar['message']);

/* All good now, continue with ticket creation */
$trackingURL = $hesk_settings['hesk_url'].'/ticket.php?track='.$trackingID;

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

$sql = "
INSERT INTO `".hesk_dbEscape($hesk_settings['db_pfix'])."tickets` (
`trackid`,`name`,`email`,`category`,`priority`,`subject`,`message`,`dt`,`lastchange`,`ip`,`status`,`attachments`,`history`,`custom1`,`custom2`,`custom3`,`custom4`,`custom5`,`custom6`,`custom7`,`custom8`,`custom9`,`custom10`,`custom11`,`custom12`,`custom13`,`custom14`,`custom15`,`custom16`,`custom17`,`custom18`,`custom19`,`custom20`
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
'".hesk_dbEscape($myattachments)."',
'',
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

$result = hesk_dbQuery($sql);

/* Ticket array for later use in e-mails */
$ticket = array(
	'name' 		=> hesk_msgToPlain($tmpvar['name'],1),
	'subject' 	=> hesk_msgToPlain($tmpvar['subject'],1),
	'trackid' 	=> $trackingID,
	'category' 	=> $tmpvar['category'],
	'priority' 	=> $tmpvar['priority'],
    'lastreplier' => hesk_msgToPlain($tmpvar['name'],1),
	'message' 	=> hesk_msgToPlain($tmpvar['message'],1),
    'owner'		=> 0,
);

foreach ($hesk_settings['custom_fields'] as $k => $v)
{
	$ticket[$k] = $v['use'] ? hesk_msgToPlain($tmpvar[$k],1) : '';
}

/* Format e-mail message for customer */
$msg = hesk_getEmailMessage('new_ticket',$ticket);

/* Send e-mail */
$headers = "From: $hesk_settings[noreply_mail]\n";
$headers.= "Reply-to: $hesk_settings[noreply_mail]\n";
$headers.= "Return-Path: $hesk_settings[webmaster_mail]\n";
$headers.= "Content-type: text/plain; charset=".$hesklang['ENCODING'];
@mail($tmpvar['email'],$hesklang['ticket_received'],$msg,$headers);

/* Need to notify any admins? */
$admins=array();
$sql = "SELECT `email`,`isadmin`,`categories` FROM `".hesk_dbEscape($hesk_settings['db_pfix'])."users` WHERE `notify_new_unassigned`='1'";
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

/* Next ticket show suggested articles again */
$_SESSION['ARTICLES_SUGGESTED']=false;

/* Unset temporary variables */
unset($tmpvar);
hesk_cleanSessionVars('tmpvar');

/* Print header */
require_once(HESK_PATH . 'inc/header.inc.php');

?>
<table width="100%" border="0" cellspacing="0" cellpadding="0">
<tr>
<td width="3"><img src="img/headerleftsm.jpg" width="3" height="25" alt="" /></td>
<td class="headersm"><?php hesk_showTopBar($hesklang['ticket_submitted']); ?></td>
<td width="3"><img src="img/headerrightsm.jpg" width="3" height="25" alt="" /></td>
</tr>
</table>

<table width="100%" border="0" cellspacing="0" cellpadding="3">
<tr>
<td><span class="smaller"><a href="<?php echo $hesk_settings['site_url']; ?>" class="smaller"><?php echo $hesk_settings['site_title']; ?></a> &gt;
<a href="<?php echo $hesk_settings['hesk_url']; ?>" class="smaller"><?php echo $hesk_settings['hesk_title']; ?></a>
&gt; <?php echo $hesklang['ticket_submitted']; ?></span></td>
</tr>
</table>

</td>
</tr>
<tr>
<td>

<p>&nbsp;</p>

<?php

$tmp = $hesklang['ticket_submitted'].'<br /><br />'.$hesklang['ticket_submitted_success'].': <b>'.$trackingID.'</b><br /><br />
<a href="'.$trackingURL.'">'.$hesklang['view_your_ticket'].'</a>';
hesk_show_success($tmp);
?>

<p>&nbsp;</p>

<?php
require_once(HESK_PATH . 'inc/footer.inc.php');
exit();
?>
