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
define('HESK_NO_ROBOTS',1);

/* Get all the required files and functions */
require(HESK_PATH . 'hesk_settings.inc.php');
require(HESK_PATH . 'inc/common.inc.php');
require(HESK_PATH . 'inc/database.inc.php');

hesk_session_start();

if (isset($_GET['track']))
{
	$trackingID = strtoupper(hesk_input($_GET['track']));
	if (empty($trackingID))
	{
		print_form();
	}
}
else
{
	print_form();
}

/* Print header */
require_once(HESK_PATH . 'inc/header.inc.php');

/* Connect to database */
hesk_dbConnect();

/* Get ticket info */
$sql = "SELECT `t1`.* , `t2`.name AS `repliername`
FROM `".hesk_dbEscape($hesk_settings['db_pfix'])."tickets` AS `t1` LEFT JOIN `".hesk_dbEscape($hesk_settings['db_pfix'])."users` AS `t2` ON `t1`.`replierid` = `t2`.`id`
WHERE `trackid`='".hesk_dbEscape($trackingID)."' LIMIT 1";
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

/* Get category name and ID */
$sql = "SELECT * FROM `".hesk_dbEscape($hesk_settings['db_pfix'])."categories` WHERE `id`=".hesk_dbEscape($ticket['category'])." LIMIT 1";
$result = hesk_dbQuery($sql);

/* If this category has been deleted use the default category with ID 1 */
if (hesk_dbNumRows($result) != 1)
{
	$sql = "SELECT * FROM `".hesk_dbEscape($hesk_settings['db_pfix'])."categories` WHERE `id`=1 LIMIT 1";
	$result = hesk_dbQuery($sql);
}

$category = hesk_dbFetchAssoc($result);

/* Get replies */
$sql = "SELECT * FROM `".hesk_dbEscape($hesk_settings['db_pfix'])."replies` WHERE `replyto`='".hesk_dbEscape($ticket['id'])."' ORDER BY `id` ASC";
$result = hesk_dbQuery($sql);
$replies = hesk_dbNumRows($result);
?>

<table width="100%" border="0" cellspacing="0" cellpadding="0">
<tr>
<td width="3"><img src="img/headerleftsm.jpg" width="3" height="25" alt="" /></td>
<td class="headersm"><?php hesk_showTopBar($hesklang['cid'].': '.$trackingID); ?></td>
<td width="3"><img src="img/headerrightsm.jpg" width="3" height="25" alt="" /></td>
</tr>
</table>

<table width="100%" border="0" cellspacing="0" cellpadding="3">
<tr>
<td><span class="smaller"><a href="<?php echo $hesk_settings['site_url']; ?>" class="smaller"><?php echo $hesk_settings['site_title']; ?></a> &gt;
<a href="<?php echo $hesk_settings['hesk_url']; ?>" class="smaller"><?php echo $hesk_settings['hesk_title']; ?></a>
&gt; <?php echo $hesklang['your_ticket']; ?></span></td>
</tr>
</table>

</td>
</tr>
<tr>
<td>

<?php
/* This will handle error, success and notice messages */
hesk_handle_messages();
?>

<h3 style="text-align:center"><?php echo $ticket['subject']; ?></h3>

<table width="100%" border="0" cellspacing="0" cellpadding="0">
<tr>
	<td width="7" height="7"><img src="img/roundcornerslt.jpg" width="7" height="7" alt="" /></td>
	<td class="roundcornerstop"></td>
	<td><img src="img/roundcornersrt.jpg" width="7" height="7" alt="" /></td>
</tr>
<tr>
	<td class="roundcornersleft">&nbsp;</td>
	<td>
    <!-- START TICKET HEAD -->

		<table border="0" cellspacing="1" cellpadding="1">
		<?php
		echo '
		<tr>
		<td>'.$hesklang['ticket_status'].': </td>
		<td>';
		$random=rand(10000,99999);

		    switch ($ticket['status'])
		    {
		    case 0:
		        echo '<font class="open">'.$hesklang['open'].'</font> [<a
		        href="change_status.php?track='.$trackingID.'&amp;s=3&amp;Refresh='.$random.'&amp;token='.hesk_token_echo(0).'">'.$hesklang['close_action'].'</a>]';
		        break;
		    case 1:
		        echo '<font class="replied">'.$hesklang['wait_staff_reply'].'</font> [<a
		        href="change_status.php?track='.$trackingID.'&amp;s=3&amp;Refresh='.$random.'&amp;token='.hesk_token_echo(0).'">'.$hesklang['close_action'].'</a>]';
		        break;
		    case 2:
		        echo '<font class="waitingreply">'.$hesklang['wait_cust_reply'].'</font> [<a
		        href="change_status.php?track='.$trackingID.'&amp;s=3&amp;Refresh='.$random.'&amp;token='.hesk_token_echo(0).'">'.$hesklang['close_action'].'</a>]';
		        break;
		    default:
		        echo '<font class="resolved">'.$hesklang['closed'].'</font>';
                if ($ticket['locked'] != 1)
                {
                	echo ' [<a href="change_status.php?track='.$trackingID.'&amp;s=1&amp;Refresh='.$random.'&amp;token='.hesk_token_echo(0).'">'.$hesklang['open_action'].'</a>]';
                }
		    }

		echo '</td>
		</tr>
		<tr>
		<td>'.$hesklang['created_on'].': </td>
		<td>'.hesk_date($ticket['dt']).'</td>
		</tr>
		<tr>
		<td>'.$hesklang['last_update'].': </td>
		<td>'.hesk_date($ticket['lastchange']).'</td>
		</tr>
		<tr>
		<td>'.$hesklang['last_replier'].': </td>
		<td>'.$ticket['repliername'].'</td>
		</tr>
		<tr>
		<td>'.$hesklang['category'].': </td>
		<td>'.$category['name'].'</td>
		</tr>
		<tr>
		<td>'.$hesklang['replies'].': </td>
		<td>'.$replies.'</td>
		</tr>
        ';

		if ($hesk_settings['cust_urgency'])
		{
			echo '
			<tr>
			<td>'.$hesklang['priority'].': </td>
			<td>';
			if ($ticket['priority']==1) {echo '<font class="important">'.$hesklang['high'].'</font>';}
			elseif ($ticket['priority']==2) {echo '<font class="medium">'.$hesklang['medium'].'</font>';}
			else {echo $hesklang['low'];}
			echo '
			</td>
			</tr>
			';
		}

		?>
		<tr>
		<td>&nbsp;</td>
		<td><a href="print.php?track=<?php echo $trackingID; ?>" target="_blank"><?php echo $hesklang['printer_friendly']; ?></a></td>
		</tr>
		</table>

    <!-- END TICKET HEAD -->
	</td>
	<td class="roundcornersright">&nbsp;</td>
</tr>
<tr>
	<td><img src="img/roundcornerslb.jpg" width="7" height="7" alt="" /></td>
	<td class="roundcornersbottom"></td>
	<td width="7" height="7"><img src="img/roundcornersrb.jpg" width="7" height="7" alt="" /></td>
</tr>
</table>

        <br />

<table width="100%" border="0" cellspacing="0" cellpadding="0">
<tr>
	<td width="7" height="7"><img src="img/roundcornerslt.jpg" width="7" height="7" alt="" /></td>
	<td class="roundcornerstop"></td>
	<td><img src="img/roundcornersrt.jpg" width="7" height="7" alt="" /></td>
</tr>
<tr>
	<td class="roundcornersleft">&nbsp;</td>
	<td>
    <!-- START TICKET REPLIES -->

		<table border="0" cellspacing="1" cellpadding="1" width="100%">
		<tr>
		<td class="ticketalt">
		    <table border="0" cellspacing="1">
		    <tr>
		    <td class="tickettd"><?php echo $hesklang['date']; ?>:</td>
		    <td class="tickettd"><?php echo hesk_date($ticket['dt']); ?></td>
		    </tr>
		    <tr>
		    <td class="tickettd"><?php echo $hesklang['name']; ?>:</td>
		    <td class="tickettd"><?php echo $ticket['name']; ?></td>
		    </tr>
		    <tr>
		    <td class="tickettd"><?php echo $hesklang['email']; ?>:</td>
		    <td class="tickettd"><?php echo str_replace(array('@','.'),array(' (at) ',' (dot) '),$ticket['email']); ?></td>
		    </tr>
		    </table>

		<?php
		/* custom fields before message */
		$print_table = 0;
		$myclass = ' class="tickettd"';

		foreach ($hesk_settings['custom_fields'] as $k=>$v)
		{
			if ($v['use'] && $v['place']==0)
		    {
		    	if ($print_table == 0)
		        {
		        	echo '<table border="0" cellspacing="1" cellpadding="2">';
		        	$print_table = 1;
		        }

		        echo '
				<tr>
				<td valign="top" '.$myclass.'>'.$v['name'].':</td>
				<td valign="top" '.$myclass.'>'.$ticket[$k].'</td>
				</tr>
		        ';
		    }
		}
		if ($print_table)
		{
			echo '</table>';
		}
		?>

		<p><b><?php echo $hesklang['message']; ?>:</b></p>
		<p><?php echo $ticket['message']; ?><br />&nbsp;</p>

		<?php
		/* custom fields after message */
		$print_table = 0;
		$myclass = 'class="tickettd"';

		foreach ($hesk_settings['custom_fields'] as $k=>$v)
		{
			if ($v['use'] && $v['place'])
		    {
		    	if ($print_table == 0)
		        {
		        	echo '<table border="0" cellspacing="1" cellpadding="2">';
		        	$print_table = 1;
		        }

		        echo '
				<tr>
				<td valign="top" '.$myclass.'>'.$v['name'].':</td>
				<td valign="top" '.$myclass.'>'.$ticket[$k].'</td>
				</tr>
		        ';
		    }
		}
		if ($print_table)
		{
			echo '</table>';
		}
		?>

		<?php
		if ($hesk_settings['attachments']['use'] && !empty($ticket['attachments'])) {
		    echo '<p><b>'.$hesklang['attachments'].':</b><br />';
		    $att=explode(',',substr($ticket['attachments'], 0, -1));
		    foreach ($att as $myatt) {
		        list($att_id, $att_name) = explode('#', $myatt);
		        echo '<img src="img/clip.png" width="16" height="16" alt="'.$att_name.'" style="align:text-bottom" /><a href="download_attachment.php?att_id='.$att_id.'&amp;track='.$trackingID.'">'.$att_name.'</a><br />';
		    }
		    echo '</p>';
		}
		?>

		</td>
		</tr>

	<?php
	$i=1;
	while ($reply = hesk_dbFetchAssoc($result))
	{
	if ($i) {$color='class="ticketrow"'; $i=0;}
	else {$color='class="ticketalt"'; $i=1;}
    $reply['dt']=hesk_date($reply['dt']);
	echo <<<EOC
	    <tr>
	    <td $color>
	        <table border="0" cellspacing="1" cellpadding="1">
	        <tr>
	        <td class="tickettd">$hesklang[date]:</td>
	        <td class="tickettd">$reply[dt]</td>
	        </tr>
	        <tr>
	        <td class="tickettd">$hesklang[name]:</td>
	        <td class="tickettd">$reply[name]</td>
	        </tr>
	        </table>
	    <p><b>$hesklang[message]:</b></p>
	    <p>$reply[message]</p>

EOC;

	if ($hesk_settings['attachments']['use'] && !empty($reply['attachments']))
	{
	    echo '<p><b>'.$hesklang['attachments'].':</b><br />';
	    $att=explode(',',substr($reply['attachments'], 0, -1));
	    foreach ($att as $myatt)
	    {
	        list($att_id, $att_name) = explode('#', $myatt);
	        echo '<img src="img/clip.png" width="16" height="16" alt="'.$att_name.'" style="align:text-bottom" /><a href="download_attachment.php?att_id='.$att_id.'&amp;track='.$trackingID.'">'.$att_name.'</a><br />';
	    }
	    echo '</p>';
	}

	if ($hesk_settings['rating'] && $reply['staffid'])
	{
		if ($reply['rating']==1)
	    {
	    	echo '<p class="rate">'.$hesklang['rnh'].'</p>';
	    }
	    elseif ($reply['rating']==5)
	    {
	    	echo '<p class="rate">'.$hesklang['rh'].'</p>';
	    }
	    else
	    {
			echo '
	        <div id="rating'.$reply['id'].'" class="rate">
	        '.$hesklang['r'].'
	        <a href="Javascript:void(0)" onclick="Javascript:hesk_rate(\'rate.php?rating=5&amp;id='.$reply['id'].'&amp;trackid='.$trackingID.'\',\'rating'.$reply['id'].'\')">'.strtolower($hesklang['yes']).'</a> /
	        <a href="Javascript:void(0)" onclick="Javascript:hesk_rate(\'rate.php?rating=1&amp;id='.$reply['id'].'&amp;trackid='.$trackingID.'\',\'rating'.$reply['id'].'\')">'.strtolower($hesklang['no']).'</a>
	        </div>
	        ';
	    }
	}

	echo '</td></tr>';
	}
	?>
	</table>

    <!-- END TICKET REPLIES -->
	</td>
	<td class="roundcornersright">&nbsp;</td>
</tr>
<tr>
	<td><img src="img/roundcornerslb.jpg" width="7" height="7" alt="" /></td>
	<td class="roundcornersbottom"></td>
	<td width="7" height="7"><img src="img/roundcornersrb.jpg" width="7" height="7" alt="" /></td>
</tr>
</table>

<?php
if ($ticket['locked'] != 1 && $ticket['status'] != 3)
{
?>

<br /><hr />

<table width="100%" border="0" cellspacing="0" cellpadding="0">
<tr>
	<td width="7" height="7"><img src="img/roundcornerslt.jpg" width="7" height="7" alt="" /></td>
	<td class="roundcornerstop"></td>
	<td><img src="img/roundcornersrt.jpg" width="7" height="7" alt="" /></td>
</tr>
<tr>
	<td class="roundcornersleft">&nbsp;</td>
	<td>

	<h3 style="text-align:center"><?php echo $hesklang['add_reply']; ?></h3>

	<form method="post" action="reply_ticket.php" enctype="multipart/form-data">
	<p align="center"><?php echo $hesklang['message']; ?>: <span class="important">*</span><br />
	<textarea name="message" rows="12" cols="60"></textarea></p>

	<?php
	/* attachments */
	if ($hesk_settings['attachments']['use'])
    {
	?>

	<p align="center">

	<?php
	echo $hesklang['attachments'].':<br />';
	for ($i=1;$i<=$hesk_settings['attachments']['max_number'];$i++)
    {
	    echo '<input type="file" name="attachment['.$i.']" size="50" /><br />';
	}
	?>

	<?php echo$hesklang['accepted_types']; ?>: <?php echo '*'.implode(', *', $hesk_settings['attachments']['allowed_types']); ?><br />
	<?php echo $hesklang['max_file_size']; ?>: <?php echo $hesk_settings['attachments']['max_size']; ?> Kb
	(<?php echo sprintf("%01.2f",($hesk_settings['attachments']['max_size']/1024)); ?> Mb)

	</p>

	<?php
	}
	?>

	<p align="center">
    <input type="hidden" name="token" value="<?php hesk_token_echo(); ?>" />
    <input type="hidden" name="orig_track" value="<?php echo $trackingID; ?>" />
	<input type="submit" value="<?php echo $hesklang['submit_reply']; ?>" class="orangebutton" onmouseover="hesk_btn(this,'orangebuttonover');" onmouseout="hesk_btn(this,'orangebutton');" /></p>

	</form>

	</td>
	<td class="roundcornersright">&nbsp;</td>
</tr>
<tr>
	<td><img src="img/roundcornerslb.jpg" width="7" height="7" alt="" /></td>
	<td class="roundcornersbottom"></td>
	<td width="7" height="7"><img src="img/roundcornersrb.jpg" width="7" height="7" alt="" /></td>
</tr>
</table>

<?php
} // END if ticket status

require_once(HESK_PATH . 'inc/footer.inc.php');

/*** START FUNCTIONS ***/

function print_form() {
global $hesk_settings, $hesklang;

/* Print header */
$hesk_settings['tmp_title'] = $hesk_settings['hesk_title'] . ' - ' . $hesklang['view_ticket'];
require_once(HESK_PATH . 'inc/header.inc.php');
?>
<table width="100%" border="0" cellspacing="0" cellpadding="0">
<tr>
<td width="3"><img src="img/headerleftsm.jpg" width="3" height="25" alt="" /></td>
<td class="headersm"><?php hesk_showTopBar($hesklang['view_ticket']); ?></td>
<td width="3"><img src="img/headerrightsm.jpg" width="3" height="25" alt="" /></td>
</tr>
</table>

<table width="100%" border="0" cellspacing="0" cellpadding="3">
<tr>
<td><span class="smaller"><a href="<?php echo $hesk_settings['site_url']; ?>" class="smaller"><?php echo $hesk_settings['site_title']; ?></a> &gt;
<a href="<?php echo $hesk_settings['hesk_url']; ?>" class="smaller"><?php echo $hesk_settings['hesk_title']; ?></a>
&gt; <?php echo $hesklang['view_ticket']; ?></span></td>
</tr>
</table>

</td>
</tr>
<tr>
<td>

<p>&nbsp;</p>

<div align="center">
<table border="0" cellspacing="0" cellpadding="0">
<tr>
	<td width="7" height="7"><img src="img/roundcornerslt.jpg" width="7" height="7" alt="" /></td>
	<td class="roundcornerstop"></td>
	<td><img src="img/roundcornersrt.jpg" width="7" height="7" alt="" /></td>
</tr>
<tr>
	<td class="roundcornersleft">&nbsp;</td>
	<td>

	<form action="ticket.php" method="get">
	<p align="center"><?php echo $hesklang['ticket_trackID']; ?>: <input type="text" name="track" maxlength="10" size="20" /></p>
	<p align="center"><input type="hidden" name="Refresh" value="<?php echo rand(10000,99999); ?>" />
	<input type="submit" value="<?php echo $hesklang['view_ticket']; ?>" class="orangebutton" onmouseover="hesk_btn(this,'orangebuttonover');" onmouseout="hesk_btn(this,'orangebutton');" /></p>
	</form>

	<p><a href="Javascript:void(0)" onclick="javascript:hesk_toggleLayerDisplay('forgot')"><?php echo $hesklang['forgot_tid'];?></a></p>

	<div id="forgot" style="display: none;">
	<script language="javascript" type="text/javascript"><!--
	function hesk_checkEmail()
    {
		d=document.form1;
		if (d.email.value=='' || d.email.value.indexOf(".") == -1 || d.email.value.indexOf("@") == -1)
		{
        	alert('<?php echo $hesklang['enter_valid_email']; ?>');
            return false;
        }
		return true;
	}
	//-->
	</script>
	<form action="index.php" method="post" name="form1" onsubmit="return hesk_checkEmail()">
	<p><?php echo $hesklang['tid_mail']; ?>:<br />
	<input type="text" name="email" size="30" maxlength="50" /><input type="hidden" name="a" value="forgot_tid" /><br /><input type="submit" value="<?php echo $hesklang['tid_send']; ?>" class="orangebutton" onmouseover="hesk_btn(this,'orangebuttonover');" onmouseout="hesk_btn(this,'orangebutton');" /></p>
    </form>
	</div>

	</td>
	<td class="roundcornersright">&nbsp;</td>
</tr>
<tr>
	<td><img src="img/roundcornerslb.jpg" width="7" height="7" alt="" /></td>
	<td class="roundcornersbottom"></td>
	<td width="7" height="7"><img src="img/roundcornersrb.jpg" width="7" height="7" alt="" /></td>
</tr>
</table>
</div>

<p>&nbsp;</p>
<p>&nbsp;</p>
<p>&nbsp;</p>
<?php
require_once(HESK_PATH . 'inc/footer.inc.php');
exit();
} // End print_form()

?>
