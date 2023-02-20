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
hesk_checkPermission('can_man_cat');

/* What should we do? */
$action = isset($_REQUEST['a']) ? hesk_input($_REQUEST['a']) : '';
if ($action == 'new') {new_cat();}
elseif ($action == 'rename') {rename_cat();}
elseif ($action == 'remove') {remove();}
elseif ($action == 'order') {order_cat();}
elseif ($action == 'linkcode') {generate_link_code();}

/* Print header */
require_once(HESK_PATH . 'inc/header.inc.php');

/* Print main manage users page */
require_once(HESK_PATH . 'inc/show_admin_nav.inc.php');
?>

</td>
</tr>
<tr>
<td>

<script language="Javascript" type="text/javascript"><!--
function confirm_delete()
{
if (confirm('<?php echo $hesklang['confirm_del_cat']; ?>')) {return true;}
else {return false;}
}
//-->
</script>

<?php
/* This will handle error, success and notice messages */
hesk_handle_messages();
?>

<h3 align="center"><?php echo $hesklang['manage_cat']; ?></h3>

<p><?php echo $hesklang['cat_intro']; ?>.</p>

<div align="center">
<table border="0" cellspacing="1" cellpadding="3" class="white">
<tr>
<th class="admin_white"><b><i><?php echo $hesklang['id']; ?></i></b></th>
<th class="admin_white"><b><i><?php echo $hesklang['cat_name']; ?></i></b></th>
<th class="admin_white"><b><i><?php echo $hesklang['not']; ?></i></b></th>
<th class="admin_white"><b><i><?php echo $hesklang['graph']; ?></i></b></th>
<th class="admin_white"><b><i><?php echo $hesklang['opt']; ?></i></b></th>
</tr>

<?php
$sql = 'SELECT COUNT(*) AS `cnt`, `category` FROM `'.hesk_dbEscape($hesk_settings['db_pfix']).'tickets` GROUP BY `category` ORDER BY `cnt` DESC';
$res = hesk_dbQuery($sql);
$line_width  = 150;
$max_tickets = 0;
while ($tmp = hesk_dbFetchAssoc($res))
{
	if (!$max_tickets && $tmp['cnt'])
    {
    	$max_tickets = $tmp['cnt'];
    }
	$number_of_tickets[$tmp['category']] = $tmp['cnt'];
}

$sql = "SELECT * FROM `".hesk_dbEscape($hesk_settings['db_pfix'])."categories` ORDER BY `cat_order` ASC";
$result = hesk_dbQuery($sql);
$options='';

$i=1;
$j=0;
$num = hesk_dbNumRows($result);

while ($mycat=hesk_dbFetchAssoc($result))
{
	$j++;

    if (isset($_SESSION['selcat2']) && $mycat['id'] == $_SESSION['selcat2'])
    {
		$color = 'admin_green';
		unset($_SESSION['selcat2']);
	}
    else
    {
		$color = $i ? 'admin_white' : 'admin_gray';
    }

    $i = $i ? 0 : 1;

    /* Deleting category with ID 1 (default category) is not allowed */
    if ($mycat['id'] == 1)
    {
        $remove_code=' <img src="../img/blank.gif" width="16" height="16" alt="" border="0" />';
    }
    else
    {
        $remove_code=' <a href="manage_categories.php?a=remove&amp;id='.$mycat['id'].'&amp;token='.hesk_token_echo(0).'" onclick="return confirm_delete();"><img src="../img/delete.png" width="16" height="16" alt="'.$hesklang['remove'].'" title="'.$hesklang['remove'].'" border="0" /></a>';
    }

    $options .= '<option value="'.$mycat['id'].'" ';
    $options .= (isset($_SESSION['selcat']) && $mycat['id'] == $_SESSION['selcat']) ? ' selected="selected" ' : '';
    $options .= '>'.$mycat['name'].'</option>';

	echo '
	<tr>
    <td class="'.$color.'">'.$mycat['id'].'</td>
	<td class="'.$color.'">'.$mycat['name'].'</td>
	';

	$tickets = isset($number_of_tickets[$mycat['id']]) ? $number_of_tickets[$mycat['id']] : 0;
	if ($max_tickets)
    {
    	$width = ceil($line_width * $tickets / $max_tickets)+1;
    }
    else
    {
    	$width = 1;
    }
    echo '
	<td class="'.$color.'" style="text-align:center">'.$tickets.'</td>
	<td class="'.$color.'" width="1"><img src="../img/line_graph.gif" height="5" width="'.$width.'" border="0" alt="" style="text-align:bottom" /></td>
	<td class="'.$color.'" style="text-align:center; white-space:nowrap;">
	<a href="Javascript:void(0)" onclick="Javascript:hesk_window(\'manage_categories.php?a=linkcode&amp;catid='.$mycat['id'].'\',\'200\',\'500\')"><img src="../img/code.png" width="16" height="16" alt="'.$hesklang['geco'].'" title="'.$hesklang['geco'].'" border="0" /></a>
    ';

	if ($num > 1)
	{
		if ($j == 1)
		{
			echo'<img src="../img/blank.gif" width="16" height="16" alt="" border="0" /> <a href="manage_categories.php?a=order&amp;catid='.$mycat['id'].'&amp;move=15&amp;token='.hesk_token_echo(0).'"><img src="../img/move_down.png" width="16" height="16" alt="'.$hesklang['move_dn'].'" title="'.$hesklang['move_dn'].'" border="0" /></a>';
		}
		elseif ($j == $num)
		{
			echo'<a href="manage_categories.php?a=order&amp;catid='.$mycat['id'].'&amp;move=-15&amp;token='.hesk_token_echo(0).'"><img src="../img/move_up.png" width="16" height="16" alt="'.$hesklang['move_up'].'" title="'.$hesklang['move_up'].'" border="0" /></a> <img src="../img/blank.gif" width="16" height="16" alt="" border="0" />';
		}
		else
		{
			echo'
			<a href="manage_categories.php?a=order&amp;catid='.$mycat['id'].'&amp;move=-15&amp;token='.hesk_token_echo(0).'"><img src="../img/move_up.png" width="16" height="16" alt="'.$hesklang['move_up'].'" title="'.$hesklang['move_up'].'" border="0" /></a>
			<a href="manage_categories.php?a=order&amp;catid='.$mycat['id'].'&amp;move=15&amp;token='.hesk_token_echo(0).'"><img src="../img/move_down.png" width="16" height="16" alt="'.$hesklang['move_dn'].'" title="'.$hesklang['move_dn'].'" border="0" /></a>
			';
		}
	}

    echo $remove_code.'</td>
	</tr>
	';

} // End while

?>
</table>
</div>

<p>&nbsp;</p>

<hr />

<form action="manage_categories.php" method="post">
<p style="text-align:center"><b><?php echo $hesklang['add_cat']; ?>:</b> (<?php echo $hesklang['max_chars']; ?>)
<input type="text" name="name" size="30" maxlength="40"
<?php
	if (isset($_SESSION['catname']))
    {
    	echo ' value="'.hesk_input($_SESSION['catname']).'" ';
    }
?>
/><input type="hidden" name="a" value="new" />
<input type="hidden" name="token" value="<?php hesk_token_echo(); ?>" />
<input type="submit" value="<?php echo $hesklang['create_cat']; ?>" class="orangebutton" onmouseover="hesk_btn(this,'orangebuttonover');" onmouseout="hesk_btn(this,'orangebutton');" /></p>
</form>

<hr />

<form action="manage_categories.php" method="post">
<p align="center"><?php echo $hesklang['ren_cat']; ?> <select name="catid">
<?php
echo $options;
?>
</select> <?php echo $hesklang['to']; ?> <input type="text" name="name" size="30" maxlength="40"
<?php
	if (isset($_SESSION['catname2']))
    {
    	echo ' value="'.hesk_input($_SESSION['catname2']).'" ';
    }
?>
/><input type="hidden" name="a" value="rename" />
<input type="hidden" name="token" value="<?php hesk_token_echo(); ?>" />
<input type="submit" value="<?php echo $hesklang['ren_cat']; ?>" class="orangebutton" onmouseover="hesk_btn(this,'orangebuttonover');" onmouseout="hesk_btn(this,'orangebutton');" /></p>
</form>

<p>&nbsp;</p>

<!-- HR -->
<p>&nbsp;</p>

<?php
require_once(HESK_PATH . 'inc/footer.inc.php');
exit();


/*** START FUNCTIONS ***/


function generate_link_code() {
	global $hesk_settings, $hesklang;
	?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML; 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en">
<head>
<title><?php echo $hesklang['genl']; ?></title>
<meta http-equiv="Content-Type" content="text/html;charset=<?php echo $hesklang['ENCODING']; ?>" />
<style type="text/css">
body
{
        margin:5px 5px;
        padding:0;
        background:#fff;
        color: black;
        font : 68.8%/1.5 Verdana, Geneva, Arial, Helvetica, sans-serif;
        text-align:left;
}

p
{
        color : black;
        font-family : Verdana, Geneva, Arial, Helvetica, sans-serif;
        font-size: 1.0em;
}
h3
{
        color : #AF0000;
        font-family : Verdana, Geneva, Arial, Helvetica, sans-serif;
        font-weight: bold;
        font-size: 1.0em;
        text-align:center;
}
</style>
</head>
<body>

<h3><?php echo $hesklang['genl']; ?></h3>

<p><i><?php echo $hesklang['genl2']; ?></i></p>

<textarea rows="3" cols="50" onfocus="this.select()"><?php echo $hesk_settings['hesk_url'].'/index.php?a=add&amp;catid='.intval($_GET['catid']); ?></textarea>

<p align="center"><a href="#" onclick="Javascript:window.close()"><?php echo $hesklang['cwin']; ?></a></p>

<p>&nbsp;</p>

</body>

</html>
	<?php
    exit();
}


function new_cat() {
	global $hesk_settings, $hesklang;

	/* A security check */
	hesk_token_check($_POST['token']);

    /* Category name */
	$catname = hesk_input($_POST['name'],$hesklang['enter_cat_name'],$_SERVER['PHP_SELF']);

    /* Do we already have a category with this name? */
	$sql = "SELECT `id` FROM `".hesk_dbEscape($hesk_settings['db_pfix'])."categories` WHERE `name` LIKE '".hesk_dbEscape($catname)."' LIMIT 1";
	$res = hesk_dbQuery($sql);
    if (hesk_dbNumRows($res) != 0)
    {
		$_SESSION['catname'] = $catname;
		hesk_process_messages($hesklang['cndupl'],$_SERVER['PHP_SELF']);
    }

	/* Get the latest cat_order */
	$sql = "SELECT `cat_order` FROM `".hesk_dbEscape($hesk_settings['db_pfix'])."categories` ORDER BY `cat_order` DESC LIMIT 1";
	$res = hesk_dbQuery($sql);
	$row = hesk_dbFetchRow($res);
	$my_order = $row[0]+10;

	$sql = "INSERT INTO `".hesk_dbEscape($hesk_settings['db_pfix'])."categories` (`name`,`cat_order`) VALUES (
    '".hesk_dbEscape($catname)."',
    '".hesk_dbEscape($my_order)."')";
	$res = hesk_dbQuery($sql);

    if (isset($_SESSION['catname']))
    {
    	unset($_SESSION['catname']);
    }

	hesk_process_messages(sprintf($hesklang['cat_name_added'],'<i>'.stripslashes($catname).'</i>'),$_SERVER['PHP_SELF'],'SUCCESS');
} // End new_cat()


function rename_cat() {
	global $hesk_settings, $hesklang;

	/* A security check */
	hesk_token_check($_POST['token']);

	$catid=hesk_isNumber($_POST['catid'],$hesklang['choose_cat_ren'],$_SERVER['PHP_SELF']);
	$_SESSION['selcat'] = $catid;

	$catname=hesk_input($_POST['name'],$hesklang['cat_ren_name'],$_SERVER['PHP_SELF']);
    $_SESSION['catname2'] = $catname;

	$sql = "SELECT `id` FROM `".hesk_dbEscape($hesk_settings['db_pfix'])."categories` WHERE `name` LIKE '".hesk_dbEscape($catname)."' LIMIT 1";
	$res = hesk_dbQuery($sql);
    if (hesk_dbNumRows($res) != 0)
    {
    	$old = hesk_dbFetchAssoc($res);
        if ($old['id'] == $catid)
        {
        	hesk_process_messages($hesklang['noch'],$_SERVER['PHP_SELF'],'NOTICE');
        }
        else
        {
    		hesk_process_messages($hesklang['cndupl'],$_SERVER['PHP_SELF']);
        }
    }

	$sql = "UPDATE `".hesk_dbEscape($hesk_settings['db_pfix'])."categories` SET `name`='".hesk_dbEscape($catname)."' WHERE `id`=".hesk_dbEscape($catid)." LIMIT 1";
	$res = hesk_dbQuery($sql);

    unset($_SESSION['selcat']);
    unset($_SESSION['catname2']);

    hesk_process_messages($hesklang['cat_renamed_to'].' <i>'.$catname.'</i>',$_SERVER['PHP_SELF'],'SUCCESS');
} // End rename_cat()


function remove() {
	global $hesk_settings, $hesklang;

	/* A security check */
	hesk_token_check($_GET['token']);

	$mycat = hesk_isNumber($_GET['id'],$hesklang['no_cat_id']);
	if ($mycat == 1)
    {
    	hesk_process_messages($hesklang['cant_del_default_cat'],$_SERVER['PHP_SELF']);
    }

	$sql = "DELETE FROM `".hesk_dbEscape($hesk_settings['db_pfix'])."categories` WHERE `id`=".hesk_dbEscape($mycat)." LIMIT 1";
	$result = hesk_dbQuery($sql);
	if (hesk_dbAffectedRows() != 1)
    {
    	hesk_error("$hesklang[int_error]: $hesklang[cat_not_found].");
    }

	$sql = "UPDATE `".hesk_dbEscape($hesk_settings['db_pfix'])."tickets` SET `category`=1 WHERE `category`=".hesk_dbEscape($mycat);
	$result = hesk_dbQuery($sql);

    hesk_process_messages($hesklang['cat_removed_db'],$_SERVER['PHP_SELF'],'SUCCESS');
} // End remove()


function order_cat() {
	global $hesk_settings, $hesklang;

	/* A security check */
	hesk_token_check($_GET['token']);

	$catid=hesk_isNumber($_GET['catid'],$hesklang['cat_move_id']);
	$_SESSION['selcat2'] = $catid;

	$cat_move=intval($_GET['move']);

	$sql = "UPDATE `".hesk_dbEscape($hesk_settings['db_pfix'])."categories` SET `cat_order`=`cat_order`+".hesk_dbEscape($cat_move)." WHERE `id`=".hesk_dbEscape($catid)." LIMIT 1";
	$result = hesk_dbQuery($sql);
	if (hesk_dbAffectedRows() != 1)
    {
    	hesk_error("$hesklang[int_error]: $hesklang[cat_not_found].");
    }

	/* Update all category fields with new order */
	$sql = "SELECT `id` FROM `".hesk_dbEscape($hesk_settings['db_pfix'])."categories` ORDER BY `cat_order` ASC";
	$result = hesk_dbQuery($sql);

	$i = 10;
	while ($mycat=hesk_dbFetchAssoc($result))
	{
	    $sql = "UPDATE `".hesk_dbEscape($hesk_settings['db_pfix'])."categories` SET `cat_order`=".hesk_dbEscape($i)." WHERE `id`=".hesk_dbEscape($mycat['id'])." LIMIT 1";
	    hesk_dbQuery($sql);
	    $i += 10;
	}

    header('Location: manage_categories.php');
    exit();

	#hesk_process_messages($hesklang['orch'],$_SERVER['PHP_SELF'],'SUCCESS');
} // End order_cat()
?>
