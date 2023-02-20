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
hesk_checkPermission('can_run_reports');

/* Set defaul values */
define('CALENDAR',1);

$selected = array(
	'w'    => array(0=>'',1=>''),
	'time' => array(1=>'',2=>'',3=>'',4=>'',5=>'',6=>'',7=>'',8=>'',9=>'',10=>'',11=>'',12=>''),
    'type' => array(1=>'',2=>'',3=>'',4=>''),
);
$is_all_time = 0;

/* Default this month to date */
$date_from = date('Y-m-d',mktime(0, 0, 0, date("m"), 1, date("Y")));
$date_to = date('Y-m-d');
$input_datefrom = date('m/d/Y', strtotime('last month'));
$input_dateto = date('m/d/Y');

/* Date */
if (!empty($_GET['w']))
{
	$df = preg_replace('/[^0-9]/','',$_GET['datefrom']);
    if (strlen($df) == 8)
    {
    	$date_from = substr($df,4,4) . '-' . substr($df,0,2) . '-' . substr($df,2,2);
        $input_datefrom = substr($df,0,2) . '/' . substr($df,2,2) . '/' . substr($df,4,4);
    }
    else
    {
    	$date_from = date('Y-m-d', strtotime('last month') );
    }

	$dt = preg_replace('/[^0-9]/','',$_GET['dateto']);
    if (strlen($dt) == 8)
    {
    	$date_to = substr($dt,4,4) . '-' . substr($dt,0,2) . '-' . substr($dt,2,2);
        $input_dateto = substr($dt,0,2) . '/' . substr($dt,2,2) . '/' . substr($dt,4,4);
    }
    else
    {
    	$date_to = date('Y-m-d');
    }

    if ($date_from > $date_to)
    {
        $tmp = $date_from;
        $tmp2 = $input_datefrom;

        $date_from = $date_to;
        $input_datefrom = $input_dateto;

        $date_to = $tmp;
        $input_dateto = $tmp2;

        $note_buffer = $hesklang['datetofrom'];
    }

    if ($date_to > date('Y-m-d'))
    {
    	$date_to = date('Y-m-d');
        $input_dateto = date('m/d/Y');
    }

    $query_string = 'reports.php?w=1&amp;datefrom='.urlencode($input_datefrom).'&amp;dateto='.urlencode($input_dateto);
	$selected['w'][1]='checked="checked"';
    $selected['time'][3]='selected="selected"';
}
else
{
	$selected['w'][0]='checked="checked"';
	$_GET['time'] = isset($_GET['time']) ? intval($_GET['time']) : 3;

    switch ($_GET['time'])
    {
    	case 1:
			/* Today */
			$date_from = date('Y-m-d');
			$date_to = $date_from;
			$selected['time'][1]='selected="selected"';
            $is_all_time = 1;
        break;

    	case 2:
			/* Yesterday */
			$date_from = date('Y-m-d',mktime(0, 0, 0, date("m"), date("d")-1, date("Y")));
			$date_to = $date_from;
			$selected['time'][2]='selected="selected"';
            $is_all_time = 1;
        break;

    	case 4:
			/* Last month */
			$date_from = date('Y-m-d',mktime(0, 0, 0, date("m")-1, 1, date("Y")));
			$date_to = date('Y-m-d',mktime(0, 0, 0, date("m"), 0, date("Y")));
			$selected['time'][4]='selected="selected"';
        break;

    	case 5:
			/* Last 30 days */
			$date_from = date('Y-m-d',mktime(0, 0, 0, date("m")-1, date("d"), date("Y")));
			$date_to = date('Y-m-d');
			$selected['time'][5]='selected="selected"';
        break;

    	case 6:
			/* This week */
			list($date_from,$date_to)=dateweek(0);
            $date_to = date('Y-m-d');
			$selected['time'][6]='selected="selected"';
        break;

    	case 7:
			/* Last week */
			list($date_from,$date_to)=dateweek(-1);
			$selected['time'][7]='selected="selected"';
        break;

    	case 8:
			/* This business week */
			list($date_from,$date_to)=dateweek(0,1);
            $date_to = date('Y-m-d');
			$selected['time'][8]='selected="selected"';
        break;

    	case 9:
			/* Last business week */
			list($date_from,$date_to)=dateweek(-1,1);
			$selected['time'][9]='selected="selected"';
        break;

    	case 10:
			/* This year */
			$date_from = date('Y').'-01-01';
			$date_to = date('Y-m-d');
			$selected['time'][10]='selected="selected"';
        break;

    	case 11:
			/* Last year */
			$date_from = date('Y')-1 . '-01-01';
			$date_to = date('Y')-1 . '-12-31';
			$selected['time'][11]='selected="selected"';
        break;

    	case 12:
			/* All time */
			$date_from = hesk_getOldestDate();
			$date_to = date('Y-m-d');
			$selected['time'][12]='selected="selected"';
            $is_all_time = 1;
        break;

        default:
        	$_GET['time'] = 3;
			$selected['time'][3]='selected="selected"';
    }

    $query_string = 'reports.php?w=0&amp;time='.$_GET['time'];
}

unset($tmp);

/* Type */
$type = isset($_GET['type']) ? intval($_GET['type']) : 1;
if (isset($selected['type'][$type]))
{
	$selected['type'][$type] = 'selected="selected"';
}

/* Print header */
require_once(HESK_PATH . 'inc/header.inc.php');

/* Print main manage users page */
require_once(HESK_PATH . 'inc/show_admin_nav.inc.php');
?>

</td>
</tr>
<tr>
<td>

<?php
/* This will handle error, success and notice messages */
hesk_handle_messages();
?>

<h3 align="center"><?php echo $hesklang['reports']; ?></h3>

<p><?php echo $hesklang['reports_intro']; ?></p>


<table width="100%" border="0" cellspacing="0" cellpadding="0">
	<tr>
		<td width="7" height="7"><img src="../img/roundcornerslt.jpg" width="7" height="7" alt="" /></td>
		<td class="roundcornerstop"></td>
		<td><img src="../img/roundcornersrt.jpg" width="7" height="7" alt="" /></td>
	</tr>
	<tr>
	<td class="roundcornersleft">&nbsp;</td>
	<td>

	<form action="reports.php" method="get" name="form1">

    <table border="0" width="100%">
    <tr>
    <td valign="top" width="50%">

    <!-- START DATE -->
    <p><b><?php echo $hesklang['cdr']; ?></b><br />
        <input type="radio" name="w" value="0" id="w0" <?php echo $selected['w'][0]; ?> />
		<select name="time" onclick="document.getElementById('w0').checked = true" onfocus="document.getElementById('w0').checked = true" style="margin-top:5px;margin-bottom:5px;">
			<option value="1" <?php echo $selected['time'][1]; ?>><?php echo $hesklang['r1']; ?> (<?php echo $hesklang['d'.date('w')]; ?>)</option>
			<option value="2" <?php echo $selected['time'][2]; ?>><?php echo $hesklang['r2']; ?> (<?php echo $hesklang['d'.date('w',mktime(0, 0, 0, date('m'), date('d')-1, date('Y')))]; ?>)</option>
			<option value="3" <?php echo $selected['time'][3]; ?>><?php echo $hesklang['r3']; ?> (<?php echo $hesklang['m'.date('n')]; ?>)</option>
			<option value="4" <?php echo $selected['time'][4]; ?>><?php echo $hesklang['r4']; ?> (<?php echo $hesklang['m'.date('n',mktime(0, 0, 0, date('m')-1, date('d'), date('Y')))]; ?>)</option>
			<option value="5" <?php echo $selected['time'][5]; ?>><?php echo $hesklang['r5']; ?></option>
			<option value="6" <?php echo $selected['time'][6]; ?>><?php echo $hesklang['r6']; ?></option>
			<option value="7" <?php echo $selected['time'][7]; ?>><?php echo $hesklang['r7']; ?></option>
			<option value="8" <?php echo $selected['time'][8]; ?>><?php echo $hesklang['r8']; ?></option>
			<option value="9" <?php echo $selected['time'][9]; ?>><?php echo $hesklang['r9']; ?></option>
			<option value="10" <?php echo $selected['time'][10]; ?>><?php echo $hesklang['r10']; ?> (<?php echo date('Y'); ?>)</option>
			<option value="11" <?php echo $selected['time'][11]; ?>><?php echo $hesklang['r11']; ?> (<?php echo date('Y',mktime(0, 0, 0, date('m'), date('d'), date('Y')-1)); ?>)</option>
			<option value="12" <?php echo $selected['time'][12]; ?>><?php echo $hesklang['r12']; ?></option>
		</select>

        <br />

        <input type="radio" name="w" value="1" id="w1" <?php echo $selected['w'][1]; ?> />
		<?php echo $hesklang['from']; ?> <input type="text" name="datefrom" value="<?php echo $input_datefrom; ?>" id="datefrom" size="10" onclick="document.getElementById('w1').checked = true" onfocus="document.getElementById('w1').checked = true;this.focus;" />

        <script language="Javascript" type="text/javascript">
        var heskselect = 'w1';
        new tcal ({
        		'formname': 'form1',
                'controlname': 'datefrom'
        });
        </script>

        <?php echo $hesklang['to']; ?> <input type="text" name="dateto" value="<?php echo $input_dateto; ?>" id="dateto" size="10" onclick="document.getElementById('w1').checked = true" onfocus="document.getElementById('w1').checked = true; this.focus;" />
        <script language="Javascript" type="text/javascript">
        new tcal ({
        		'formname': 'form1',
                'controlname': 'dateto'
        });
        </script>
    </p>
    <!-- END DATE -->

    </td>
    <td valign="top" width="50%">

    <!-- START TYPE -->
    <p><b><?php echo $hesklang['crt']; ?></b><br />
		<select name="type" style="margin-top:5px;margin-bottom:5px;">
			<option value="1" <?php echo $selected['type'][1]; ?>><?php echo $hesklang['t1']; ?></option>
			<option value="2" <?php echo $selected['type'][2]; ?>><?php echo $hesklang['t2']; ?></option>
			<option value="3" <?php echo $selected['type'][3]; ?>><?php echo $hesklang['t3']; ?></option>
            <option value="4" <?php echo $selected['type'][4]; ?>><?php echo $hesklang['t4']; ?></option>
		</select>
    </p>
    <!-- END TYPE -->

    </td>
    </tr>
    </table>

	<p align="center">
    <input type="hidden" name="token" value="<?php hesk_token_echo(); ?>" />
    <input type="submit" value="<?php echo $hesklang['dire']; ?>" class="orangebutton" onmouseover="hesk_btn(this,'orangebuttonover');" onmouseout="hesk_btn(this,'orangebutton');" />
    </p>
	</form>

    </td>
	<td class="roundcornersright">&nbsp;</td>
	</tr>
	<tr>
	<td><img src="../img/roundcornerslb.jpg" width="7" height="7" alt="" /></td>
	<td class="roundcornersbottom"></td>
	<td width="7" height="7"><img src="../img/roundcornersrb.jpg" width="7" height="7" alt="" /></td>
	</tr>
</table>

<p>&nbsp;</p>

<?php
if ($date_from == $date_to)
{
	?>
	<p><b><?php echo hesk_dateToString($date_from,0); ?></b></p>
	<?php
}
else
{
	?>
	<p><b><?php echo hesk_dateToString($date_from,0); ?></b> - <b><?php echo hesk_dateToString($date_to,0); ?></b></p>
	<?php
}

/* Report type */
switch ($type)
{
	case 2:
    	hesk_ticketsByMonth();
        break;
	case 3:
    	hesk_ticketsByUser();
        break;
	case 4:
    	hesk_ticketsByCategory();
        break;
	default:
    	hesk_ticketsByDay();
}

require_once(HESK_PATH . 'inc/footer.inc.php');
exit();


/*** START FUNCTIONS ***/


function hesk_ticketsByCategory()
{
	global $hesk_settings, $hesklang, $date_from, $date_to;

	/* List of categories */
	$cat = array();
	$sql = "SELECT `id`,`name` FROM `".hesk_dbEscape($hesk_settings['db_pfix'])."categories` ORDER BY `id` ASC";
	$res = hesk_dbQuery($sql);
	while ($row=hesk_dbFetchAssoc($res))
	{
		$cat[$row['id']]=$row['name'];
	}

	$tickets = array();
    $totals = array('num_tickets' => 0, 'all_replies' => 0, 'staff_replies' => 0);

    /* Populate category counts */
    foreach ($cat as $id => $name)
    {
    	$tickets[$id] = array(
        'num_tickets' => 0,
        'all_replies' => 0,
        'staff_replies' => 0,
        );
    }

	/* SQL query for category stats */
	$sql='
	SELECT DISTINCT `t1`.`category`, `t2`.`num_tickets`, IFNULL(`t3`.`all_replies`,0) AS `all_replies`, IFNULL(`t4`.`staff_replies`,0) AS `staff_replies`
	FROM `'.hesk_dbEscape($hesk_settings['db_pfix']).'tickets` AS `t1`
	LEFT JOIN
	(SELECT COUNT(*) AS `num_tickets` , `category` FROM `'.hesk_dbEscape($hesk_settings['db_pfix']).'tickets` AS `t1` WHERE DATE(`t1`.`dt`) BETWEEN \'' . hesk_dbEscape($date_from) . '\' AND \'' . hesk_dbEscape($date_to) . '\' GROUP BY `category`) AS `t2`
	ON `t1`.`category`=`t2`.`category`
	LEFT JOIN
	(SELECT COUNT(*) AS `all_replies` , `t1`.`category` FROM `'.hesk_dbEscape($hesk_settings['db_pfix']).'tickets` AS `t1`, `'.hesk_dbEscape($hesk_settings['db_pfix']).'replies` AS `t5` WHERE `t1`.`id`=`t5`.`replyto` AND DATE(`t5`.`dt`) BETWEEN \'' . hesk_dbEscape($date_from) . '\' AND \'' . hesk_dbEscape($date_to) . '\' GROUP BY `t1`.`category`) AS `t3`
	ON `t1`.`category`=`t3`.`category`
	LEFT JOIN
	(SELECT COUNT(*) AS `staff_replies`, `t1`.`category` FROM `'.hesk_dbEscape($hesk_settings['db_pfix']).'tickets` AS `t1`, `'.hesk_dbEscape($hesk_settings['db_pfix']).'replies` AS `t5` WHERE `t1`.`id`=`t5`.`replyto` AND `t5`.`staffid`>0 AND DATE(`t5`.`dt`) BETWEEN \'' . hesk_dbEscape($date_from) . '\' AND \'' . hesk_dbEscape($date_to) . '\' GROUP BY `t1`.`category`) AS `t4`
	ON `t1`.`category`=`t4`.`category`
	WHERE DATE(`t1`.`dt`) BETWEEN \'' . hesk_dbEscape($date_from) . '\' AND \'' . hesk_dbEscape($date_to) . '\'
	';
	$res = hesk_dbQuery($sql);

	/* Update ticket values */
	while ($row = hesk_dbFetchAssoc($res))
	{
    	if (isset($cat[$row['category']]))
        {
        	$tickets[$row['category']]['num_tickets'] += $row['num_tickets'];
            $tickets[$row['category']]['all_replies'] += $row['all_replies'];
            $tickets[$row['category']]['staff_replies'] += $row['staff_replies'];
        }
        else
        {
        	/* Category deleted */
        	$tickets[0]['num_tickets'] += $row['num_tickets'];
            $tickets[0]['all_replies'] += $row['all_replies'];
            $tickets[0]['staff_replies'] += $row['staff_replies'];
        }

		$totals['num_tickets'] += $row['num_tickets'];
		$totals['all_replies'] += $row['all_replies'];
		$totals['staff_replies'] += $row['staff_replies'];
	}

	?>
	    <table width="100%" cellpadding="5" style="text-align:justify;border-collapse:collapse;padding:10px;">
	      <tr style="border-bottom:1px solid #000000;">
	        <td><?php echo $hesklang['category']; ?></td>
	        <td><?php echo $hesklang['tickets']; ?></td>
	        <td><?php echo $hesklang['replies'] . ' (' . $hesklang['all'] .')'; ?></td>
	        <td><?php echo $hesklang['replies'] . ' (' . $hesklang['staff'] .')'; ?></td>
	      </tr>

	<?php
	$num_tickets = count($tickets);
	if ($num_tickets > 10)
	{
	?>
	      <tr style="border-bottom:1px solid #000000;">
	        <td><b><?php echo $hesklang['totals']; ?></b></td>
	        <td><b><?php echo $totals['num_tickets']; ?></b></td>
	        <td><b><?php echo $totals['all_replies']; ?></b></td>
	        <td><b><?php echo $totals['staff_replies']; ?></b></td>
	      </tr>
	<?php
	}

	$cls = '';
	foreach ($tickets as $k => $d)
	{
		$cls = $cls ? '' : 'style="background:#EEEEE8;"';

	    ?>
	      <tr <?php echo $cls; ?>>
	        <td><?php echo $cat[$k]; ?></td>
	        <td><?php echo $d['num_tickets']; ?></td>
	        <td><?php echo $d['all_replies']; ?></td>
	        <td><?php echo $d['staff_replies']; ?></td>
	      </tr>
	    <?php
	}
	?>
	      <tr style="border-top:1px solid #000000;">
	        <td><b><?php echo $hesklang['totals']; ?></b></td>
	        <td><b><?php echo $totals['num_tickets']; ?></b></td>
	        <td><b><?php echo $totals['all_replies']; ?></b></td>
	        <td><b><?php echo $totals['staff_replies']; ?></b></td>
	      </tr>
	    </table>

	    <p>&nbsp;</p>
    <?php
} // END hesk_ticketsByCategory


function hesk_ticketsByUser()
{
	global $hesk_settings, $hesklang, $date_from, $date_to;

	/* List of users */
	$admins = array();
	$sql = "SELECT `id`,`name` FROM `".hesk_dbEscape($hesk_settings['db_pfix'])."users` ORDER BY `id` ASC";
	$res = hesk_dbQuery($sql);
	while ($row=hesk_dbFetchAssoc($res))
	{
		$admins[$row['id']]=$row['name'];
	}

	$tickets = array();
    $totals = array('asstickets' => 0, 'tickets' => 0, 'replies' => 0);

    /* Populate admin counts */
    foreach ($admins as $id => $name)
    {
    	$tickets[$id] = array(
        'asstickets' => 0,
        'tickets' => 0,
        'replies' => 0,
        );
    }

	/* SQL query for tickets */
	$sql = 'SELECT `owner`, COUNT(*) AS `cnt` FROM `'.hesk_dbEscape($hesk_settings['db_pfix']).'tickets` WHERE `owner` > 0 AND DATE(`dt`) BETWEEN \'' . hesk_dbEscape($date_from) . '\' AND \'' . hesk_dbEscape($date_to) . '\' GROUP BY `owner`';
	$res = hesk_dbQuery($sql);

	/* Update ticket values */
	while ($row = hesk_dbFetchAssoc($res))
	{
    	if (isset($admins[$row['owner']]))
        {
            $tickets[$row['owner']]['asstickets'] += $row['cnt'];
        }
        $totals['asstickets'] += $row['cnt'];
	}

	/* SQL query for replies */
	$sql = 'SELECT `staffid`, COUNT(*) AS `cnt`, COUNT(DISTINCT `replyto`) AS `tcnt` FROM `'.hesk_dbEscape($hesk_settings['db_pfix']).'replies` WHERE `staffid` > 0 AND DATE(`dt`) BETWEEN \'' . hesk_dbEscape($date_from) . '\' AND \'' . hesk_dbEscape($date_to) . '\' GROUP BY `staffid`';
	$res = hesk_dbQuery($sql);

	/* Update ticket values */
	while ($row = hesk_dbFetchAssoc($res))
	{
    	if (isset($admins[$row['staffid']]))
        {
        	$tickets[$row['staffid']]['tickets'] += $row['tcnt'];
            $tickets[$row['staffid']]['replies'] += $row['cnt'];
        }
        else
        {
        	/* User deleted */
        	$tickets[0]['tickets'] += $row['tcnt'];
            $tickets[0]['replies'] += $row['cnt'];
        }

		$totals['tickets'] += $row['tcnt'];
		$totals['replies'] += $row['cnt'];
	}

	?>
	    <table width="100%" cellpadding="5" style="text-align:justify;border-collapse:collapse;padding:10px;">
	      <tr style="border-bottom:1px solid #000000;">
	        <td><?php echo $hesklang['user']; ?></td>
	        <td><?php echo $hesklang['ticass']; ?></td>
	        <td><?php echo $hesklang['ticall']; ?></td>
	        <td><?php echo $hesklang['replies']; ?></td>
	      </tr>

	<?php
	$num_tickets = count($tickets);
	if ($num_tickets > 10)
	{
	?>
	      <tr style="border-bottom:1px solid #000000;">
	        <td><b><?php echo $hesklang['totals']; ?></b></td>
	        <td><b><?php echo $totals['asstickets']; ?></b></td>
	        <td><b><?php echo $totals['tickets']; ?></b></td>
	        <td><b><?php echo $totals['replies']; ?></b></td>
	      </tr>
	<?php
	}

	$cls = '';
	foreach ($tickets as $k => $d)
	{
		$cls = $cls ? '' : 'style="background:#EEEEE8;"';

	    ?>
	      <tr <?php echo $cls; ?>>
	        <td><?php echo $admins[$k]; ?></td>
	        <td><?php echo $d['asstickets']; ?></td>
	        <td><?php echo $d['tickets']; ?></td>
	        <td><?php echo $d['replies']; ?></td>
	      </tr>
	    <?php
	}
	?>
	      <tr style="border-top:1px solid #000000;">
	        <td><b><?php echo $hesklang['totals']; ?></b></td>
	        <td><b><?php echo $totals['asstickets']; ?></b></td>
	        <td><b><?php echo $totals['tickets']; ?></b></td>
	        <td><b><?php echo $totals['replies']; ?></b></td>
	      </tr>
	    </table>

	    <p>&nbsp;</p>
    <?php
} // END hesk_ticketsByUser


function hesk_ticketsByMonth()
{
	global $hesk_settings, $hesklang, $date_from, $date_to;

	$tickets = array();
    $totals = array('all' => 0, 'resolved' => 0);
	$dt = MonthsArray($date_from,$date_to);

    #print_r($dt);

	/* Pre-populate date values */
	foreach ($dt as $month)
	{
		$tickets[$month] = array(
		'all' => 0,
		'resolved' => 0,
		);
	}

	/* SQL query for all */
	$sql = 'SELECT YEAR(`dt`) AS `myyear`, MONTH(`dt`) AS `mymonth`, COUNT(*) AS `cnt` FROM `'.hesk_dbEscape($hesk_settings['db_pfix']).'tickets` WHERE DATE(`dt`) BETWEEN \'' . hesk_dbEscape($date_from) . '\' AND \'' . hesk_dbEscape($date_to) . '\' GROUP BY `myyear`,`mymonth`';
	#die($sql);
    $res = hesk_dbQuery($sql);

	/* Update ticket values */
	while ($row = hesk_dbFetchAssoc($res))
	{
    	$row['mymonth'] = sprintf('%02d',$row['mymonth']);
		$tickets[$row['myyear'].'-'.$row['mymonth'].'-01']['all'] += $row['cnt'];
	    $totals['all'] += $row['cnt'];
	}

	/* SQL query for resolved */
	$sql = 'SELECT YEAR(`dt`) AS `myyear`, MONTH(`dt`) AS `mymonth`, COUNT(*) AS `cnt` FROM `'.hesk_dbEscape($hesk_settings['db_pfix']).'tickets` WHERE `status` = \'3\' AND DATE(`dt`) BETWEEN \'' . hesk_dbEscape($date_from) . '\' AND \'' . hesk_dbEscape($date_to) . '\' GROUP BY `myyear`,`mymonth`';
	$res = hesk_dbQuery($sql);

	/* Update ticket values */
	while ($row = hesk_dbFetchAssoc($res))
	{
    	$row['mymonth'] = sprintf('%02d',$row['mymonth']);
		$tickets[$row['myyear'].'-'.$row['mymonth'].'-01']['resolved'] += $row['cnt'];
	    $totals['resolved'] += $row['cnt'];
	}

	?>
	    <table width="100%" cellpadding="5" style="text-align:justify;border-collapse:collapse;padding:10px;">
	      <tr style="border-bottom:1px solid #000000;">
	        <td><?php echo $hesklang['month']; ?></td>
	        <td><?php echo $hesklang['atik']; ?></td>
	        <td><?php echo $hesklang['topen']; ?></td>
	        <td><?php echo $hesklang['closed']; ?></td>
	      </tr>

	<?php
	$num_tickets = count($tickets);
	if ($num_tickets > 10)
	{
	?>
	      <tr style="border-bottom:1px solid #000000;">
	        <td><b><?php echo $hesklang['totals']; ?></b></td>
	        <td><b><?php echo $totals['all']; ?></b></td>
	        <td><b><?php echo $totals['all']-$totals['resolved']; ?></b></td>
	        <td><b><?php echo $totals['resolved']; ?></b></td>
	      </tr>
	<?php
	}

	$cls = '';
	foreach ($tickets as $k => $d)
	{
		$cls = $cls ? '' : 'style="background:#EEEEE8;"';

	    ?>
	      <tr <?php echo $cls; ?>>
	        <td><?php echo hesk_dateToString($k,0,0,1); ?></td>
	        <td><?php echo $d['all']; ?></td>
	        <td><?php echo $d['all']-$d['resolved']; ?></td>
	        <td><?php echo $d['resolved']; ?></td>
	      </tr>
	    <?php
	}
	?>
	      <tr style="border-top:1px solid #000000;">
	        <td><b><?php echo $hesklang['totals']; ?></b></td>
	        <td><b><?php echo $totals['all']; ?></b></td>
	        <td><b><?php echo $totals['all']-$totals['resolved']; ?></b></td>
	        <td><b><?php echo $totals['resolved']; ?></b></td>
	      </tr>
	    </table>

	    <p>&nbsp;</p>
    <?php
} // END hesk_ticketsByMonth


function hesk_ticketsByDay()
{
	global $hesk_settings, $hesklang, $date_from, $date_to;

	$tickets = array();
    $totals = array('all' => 0, 'resolved' => 0);
	$dt = DateArray($date_from,$date_to);

	/* Pre-populate date values */
	foreach ($dt as $day)
	{
		$tickets[$day] = array(
		'all' => 0,
		'resolved' => 0,
		);
	}

	/* SQL query for all */
	$sql = 'SELECT DATE(`dt`) AS `mydt`, COUNT(*) AS `cnt` FROM `'.hesk_dbEscape($hesk_settings['db_pfix']).'tickets` WHERE DATE(`dt`) BETWEEN \'' . hesk_dbEscape($date_from) . '\' AND \'' . hesk_dbEscape($date_to) . '\' GROUP BY `mydt`';
	$res = hesk_dbQuery($sql);

	/* Update ticket values */
	while ($row = hesk_dbFetchAssoc($res))
	{
		$tickets[$row['mydt']]['all'] += $row['cnt'];
	    $totals['all'] += $row['cnt'];
	}

	/* SQL query for resolved */
	$sql = 'SELECT DATE(`dt`) AS `mydt`, COUNT(*) AS `cnt` FROM `'.hesk_dbEscape($hesk_settings['db_pfix']).'tickets` WHERE `status` = \'3\' AND DATE(`dt`) BETWEEN \'' . hesk_dbEscape($date_from) . '\' AND \'' . hesk_dbEscape($date_to) . '\' GROUP BY `mydt`';
	$res = hesk_dbQuery($sql);

	/* Update ticket values */
	while ($row = hesk_dbFetchAssoc($res))
	{
		$tickets[$row['mydt']]['resolved'] += $row['cnt'];
	    $totals['resolved'] += $row['cnt'];
	}

	?>
	    <table width="100%" cellpadding="5" style="text-align:justify;border-collapse:collapse;padding:10px;">
	      <tr style="border-bottom:1px solid #000000;">
	        <td><?php echo $hesklang['date']; ?></td>
	        <td><?php echo $hesklang['atik']; ?></td>
	        <td><?php echo $hesklang['topen']; ?></td>
	        <td><?php echo $hesklang['closed']; ?></td>
	      </tr>

	<?php
	$num_tickets = count($tickets);
	if ($num_tickets > 10)
	{
	?>
	      <tr style="border-bottom:1px solid #000000;">
	        <td><b><?php echo $hesklang['totals']; ?></b></td>
	        <td><b><?php echo $totals['all']; ?></b></td>
	        <td><b><?php echo $totals['all']-$totals['resolved']; ?></b></td>
	        <td><b><?php echo $totals['resolved']; ?></b></td>
	      </tr>
	<?php
	}

	$cls = '';
	foreach ($tickets as $k => $d)
	{
		$cls = $cls ? '' : 'style="background:#EEEEE8;"';

	    ?>
	      <tr <?php echo $cls; ?>>
	        <td><?php echo hesk_dateToString($k); ?></td>
	        <td><?php echo $d['all']; ?></td>
	        <td><?php echo $d['all']-$d['resolved']; ?></td>
	        <td><?php echo $d['resolved']; ?></td>
	      </tr>
	    <?php
	}
	?>
	      <tr style="border-top:1px solid #000000;">
	        <td><b><?php echo $hesklang['totals']; ?></b></td>
	        <td><b><?php echo $totals['all']; ?></b></td>
	        <td><b><?php echo $totals['all']-$totals['resolved']; ?></b></td>
	        <td><b><?php echo $totals['resolved']; ?></b></td>
	      </tr>
	    </table>

	    <p>&nbsp;</p>
    <?php
} // END hesk_ticketsByDay


function hesk_getOldestDate()
{
	global $hesk_settings, $hesklang, $date_from, $date_to;

	$sql = "SELECT `dt` FROM `".hesk_dbEscape($hesk_settings['db_pfix'])."tickets` ORDER BY `dt` ASC LIMIT 1";
	$res = hesk_dbQuery($sql);
	$row = hesk_dbFetchAssoc($res);

    return date('Y-m-d', strtotime($row['dt']) );
} // END hesk_getOldestDate()
?>
