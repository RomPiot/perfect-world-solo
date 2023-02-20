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

/* Check if this is a valid include */
if (!defined('IN_SCRIPT')) {die('Invalid attempt');}

/* Get number of tickets and page number */
$result = hesk_dbQuery($sql);
$total  = hesk_dbNumRows($result);

if ($total > 0)
{

	/* This query string will be used to browse pages */
	if ($href == 'show_tickets.php')
	{
		$query  = 'status='.$status;
		$query .= '&amp;category='.$category;
		$query .= '&amp;sort='.$sort;
		$query .= '&amp;asc='.$asc;
		$query .= '&amp;limit='.$maxresults;
		$query .= '&amp;archive='.$archive[1];
		$query .= '&amp;s_my='.$s_my[1];
		$query .= '&amp;s_ot='.$s_ot[1];
		$query .= '&amp;s_un='.$s_un[1];
		$query .= '&amp;page=';
	}
	else
	{
		$query  = 'q='.$q;
	    $query .= '&amp;what='.$what;
		$query .= '&amp;category='.$category;
		$query .= '&amp;dt='.urlencode($date_input);
		$query .= '&amp;sort='.$sort;
		$query .= '&amp;asc='.$asc;
		$query .= '&amp;limit='.$maxresults;
		$query .= '&amp;archive='.$archive[2];
		$query .= '&amp;s_my='.$s_my[2];
		$query .= '&amp;s_ot='.$s_ot[2];
		$query .= '&amp;s_un='.$s_un[2];
		$query .= '&amp;page=';
	}

	$pages = ceil($total/$maxresults) or $pages = 1;
	if ($page > $pages)
	{
		$page = $pages;
	}
	$limit_down = ($page * $maxresults) - $maxresults;

	$prev_page = ($page - 1 <= 0) ? 0 : $page - 1;
	$next_page = ($page + 1 > $pages) ? 0 : $page + 1;

	if ($pages > 1)
	{
		echo '<p align="center">'.sprintf($hesklang['tickets_on_pages'],$total,$pages).' '.$hesklang['jump_page'].' <select name="myHpage" id="myHpage">';
		for ($i=1;$i<=$pages;$i++)
		{
			echo '<option value="'.$i.'">'.$i.'</option>';
		}
		echo'</select> <input type="button" value="'.$hesklang['go'].'" onclick="javascript:window.location=\''.$href.'?'.$query.'\'+document.getElementById(\'myHpage\').value" class="orangebutton" onmouseover="hesk_btn(this,\'orangebuttonover\');" onmouseout="hesk_btn(this,\'orangebutton\');" /><br />';

		/* List pages */
		if ($pages > 7)
		{
			if ($page > 2)
			{
				echo '<a href="'.$href.'?'.$query.'1"><b>&laquo;</b></a> &nbsp; ';
			}

			if ($prev_page)
			{
				echo '<a href="'.$href.'?'.$query.$prev_page.'"><b>&lsaquo;</b></a> &nbsp; ';
			}
		}

		for ($i=1; $i<=$pages; $i++)
		{
			if ($i <= ($page+5) && $i >= ($page-5))
			{
				if ($i == $page)
				{
					echo ' <b>'.$i.'</b> ';
				}
				else
				{
					echo ' <a href="'.$href.'?'.$query.$i.'">'.$i.'</a> ';
				}
			}
		}

		if ($pages > 7)
		{
			if ($next_page)
			{
				echo ' &nbsp; <a href="'.$href.'?'.$query.$next_page.'"><b>&rsaquo;</b></a> ';
			}

			if ($page < ($pages - 1))
			{
				echo ' &nbsp; <a href="'.$href.'?'.$query.$pages.'"><b>&raquo;</b></a>';
			}
		}

		echo '</p>';

	} // end PAGES > 1
	else
	{
		echo '<p align="center">'.sprintf($hesklang['tickets_on_pages'],$total,$pages).' </p>';
	}

	/* We have the full SQL query now, get tickets */
	$sql .= " LIMIT ".hesk_dbEscape($limit_down)." , ".hesk_dbEscape($maxresults)." ";
	$result = hesk_dbQuery($sql);

	/* This query string will be used to order and reverse display */
	if ($href == 'show_tickets.php')
	{
		$query  = 'status='.$status;
		$query .= '&amp;category='.$category;
		$query .= '&amp;asc='.(isset($is_default) ? 1 : $asc_rev);
		$query .= '&amp;limit='.$maxresults;
		$query .= '&amp;archive='.$archive[1];
		$query .= '&amp;s_my='.$s_my[1];
		$query .= '&amp;s_ot='.$s_ot[1];
		$query .= '&amp;s_un='.$s_un[1];
		$query .= '&amp;page=1';
		$query .= '&amp;sort=';
	}
	else
	{
		$query  = 'q='.$q;
	    $query .= '&amp;what='.$what;
		$query .= '&amp;category='.$category;
		$query .= '&amp;dt='.urlencode($date_input);        
		$query .= '&amp;asc='.$asc;
		$query .= '&amp;limit='.$maxresults;
		$query .= '&amp;archive='.$archive[2];
		$query .= '&amp;s_my='.$s_my[2];
		$query .= '&amp;s_ot='.$s_ot[2];
		$query .= '&amp;s_un='.$s_un[2];
		$query .= '&amp;page=1';
		$query .= '&amp;sort=';
	}

	/* Print the table with tickets */
	$random=rand(10000,99999);
	?>

	<form name="form1" action="delete_tickets.php" method="post" onsubmit="return hesk_confirmExecute('<?php echo $hesklang['confirm_execute']; ?>')">

	<div align="center">
	<table border="0" width="100%" cellspacing="1" cellpadding="3" class="white">
	<tr>
	<th class="admin_white"><input type="checkbox" name="checkall" value="2" onclick="hesk_changeAll()" /></th>
	<th class="admin_white" style="text-align:left; white-space:nowrap;"><a href="<?php echo $href . '?' . $query; ?>trackid"><?php echo $hesklang['trackID']; ?></a></th>
	<th class="admin_white" style="text-align:left; white-space:nowrap;"><a href="<?php echo $href . '?' . $query; ?>lastchange"><?php echo $hesklang['last_update']; ?></a></th>
	<th class="admin_white" style="text-align:left; white-space:nowrap;"><a href="<?php echo $href . '?' . $query; ?>name"><?php echo $hesklang['name']; ?></a></th>
	<th class="admin_white" style="text-align:left; white-space:nowrap;"><a href="<?php echo $href . '?' . $query; ?>subject"><?php echo $hesklang['subject']; ?></a></th>
	<th class="admin_white" style="text-align:center; white-space:nowrap;"><a href="<?php echo $href . '?' . $query; ?>status"><?php echo $hesklang['status']; ?></a></th>
	<th class="admin_white" style="text-align:center; white-space:nowrap;"><a href="<?php echo $href . '?' . $query; ?>lastreplier"><?php echo $hesklang['last_replier']; ?></a></th>
	<th class="admin_white" style="text-align:center; white-space:nowrap;"><a href="<?php echo $href . '?' . $query; ?>priority"><img src="../img/sort_priority_<?php echo (($asc) ? 'asc' : 'desc'); ?>.png" width="16" height="16" alt="<?php echo $hesklang['sort_by'].' '.$hesklang['priority']; ?>" title="<?php echo $hesklang['sort_by'].' '.$hesklang['priority']; ?>" border="0" /></a></th>
	</tr>

	<?php
	$i = 0;
	while ($ticket=hesk_dbFetchAssoc($result))
	{
		if ($i) {$color="admin_gray"; $i=0;}
		else {$color="admin_white"; $i=1;}

		$owner = '';
		if ($ticket['owner'] == $_SESSION['id'])
		{
			$owner = '<span class="assignedyou" title="'.$hesklang['tasy2'].'">*</span> ';
		}
		elseif ($ticket['owner'])
		{
			$owner = '<span class="assignedother" title="'.$hesklang['taso2'].'">*</span> ';
		}

		switch ($ticket['status'])
		{
			case 0:
				$ticket['status']='<span class="open">'.$hesklang['open'].'</span>';
				break;
			case 1:
				$ticket['status']='<span class="waitingreply">'.$hesklang['wait_reply'].'</span>';
				break;
			case 2:
				$ticket['status']='<span class="replied">'.$hesklang['replied'].'</span>';
				break;
			default:
				$ticket['status']='<span class="resolved">'.$hesklang['closed'].'</span>';
		}

		switch ($ticket['priority'])
		{
			case 1:
				$ticket['priority']='<img src="../img/flag_high.png" width="16" height="16" alt="'.$hesklang['priority'].': '.$hesklang['high'].'" title="'.$hesklang['priority'].': '.$hesklang['high'].'" border="0" />';
				break;
			case 2:
				$ticket['priority']='<img src="../img/flag_medium.png" width="16" height="16" alt="'.$hesklang['priority'].': '.$hesklang['medium'].'" title="'.$hesklang['priority'].': '.$hesklang['medium'].'" border="0" />';
				break;
			default:
				$ticket['priority']='<img src="../img/flag_low.png" width="16" height="16" alt="'.$hesklang['priority'].': '.$hesklang['low'].'" title="'.$hesklang['priority'].': '.$hesklang['low'].'" border="0" />';
		}

		$ticket['lastchange']=hesk_formatDate($ticket['lastchange']);

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

		$ticket['archive'] = !($ticket['archive']) ? $hesklang['no'] : $hesklang['yes'];

		$ticket['message'] = substr(strip_tags($ticket['message']),0,200).'...';

		echo <<<EOC
		<tr title="$ticket[message]">
		<td class="$color"><input type="checkbox" name="id[]" value="$ticket[id]" /></td>
		<td class="$color"><a href="admin_ticket.php?track=$ticket[trackid]&amp;Refresh=$random">$ticket[trackid]</a></td>
		<td class="$color">$ticket[lastchange]</td>
		<td class="$color">$ticket[name]</td>
		<td class="$color">$owner<a href="admin_ticket.php?track=$ticket[trackid]&amp;Refresh=$random">$ticket[subject]</a></td>
		<td class="$color">$ticket[status]</td>
		<td class="$color">$ticket[repliername]</td>
		<td class="$color" style="text-align:center; white-space:nowrap;">$ticket[priority]&nbsp;</td>
		</tr>

EOC;
	} // End while
	?>
	</table>
	</div>

    <br /><span class="assignedyou">*</span> <?php echo $hesklang['tasy2']; ?>

    <?php
    if (hesk_checkPermission('can_view_ass_others',0))
    {
    ?>
	<br /><span class="assignedother">*</span> <?php echo $hesklang['taso2']; ?>
    <?php
    }
    ?>

	<p align="center"><select name="a">
	<option value="close" selected="selected"><?php echo $hesklang['close_selected']; ?></option>
	<?php
	if (hesk_checkPermission('can_del_tickets',0))
	{
		?>
		<option value="delete"><?php echo $hesklang['del_selected']; ?></option>
		<?php
	}
	?>
	</select>
	<input type="hidden" name="token" value="<?php hesk_token_echo(); ?>" />
	<input type="submit" value="<?php echo $hesklang['execute']; ?>" class="orangebutton"  onmouseover="hesk_btn(this,'orangebuttonover');" onmouseout="hesk_btn(this,'orangebutton');" /></p>

	</form>
	<?php

} // END ticket list if total > 0
else
{
    if (isset($is_search) || $href == 'find_tickets.php')
    {
        hesk_show_notice($hesklang['no_tickets_crit']);
    }
    else
    {
        echo '<p>&nbsp;<br />&nbsp;<b><i>'.$hesklang['no_tickets_open'].'</i></b><br />&nbsp;</p>';
    }
}
?>
