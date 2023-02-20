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

if (!isset($status))
{
	$status = 6;
}

if (!isset($what))
{
	$what = 'trackid';
}

if (!isset($date_input))
{
	$date_input = '';
}

/* Can view tickets assigned to others? */
$can_view_ass_others = hesk_checkPermission('can_view_ass_others',0);

/* Category options */
$category_options = '';

$sql_private = 'SELECT * FROM `'.hesk_dbEscape($hesk_settings['db_pfix']).'categories` WHERE ';
$sql_private .= hesk_myCategories('id');
$sql_private .= ' ORDER BY `cat_order` ASC';

$result = hesk_dbQuery($sql_private);
while ($row=hesk_dbFetchAssoc($result))
{
	$row['name'] = (strlen($row['name']) > 30) ? substr($row['name'],0,30) . '...' : $row['name'];
	$selected = ($row['id'] == $category) ? 'selected="selected"' : '';
	$category_options .= '<option value="'.$row['id'].'" '.$selected.'>'.$row['name'].'</option>';
}
?>

<div align="center">
<table border="0" width="100%" cellspacing="1" cellpadding="5">
<tr>
<td valign="top" width="50%">
	<table width="100%" border="0" cellspacing="0" cellpadding="0" style="height: 380px;">
		<tr>
			<td width="7" height="7"><img src="../img/roundcornerslt.jpg" width="7" height="7" alt="" /></td>
			<td class="roundcornerstop"></td>
			<td><img src="../img/roundcornersrt.jpg" width="7" height="7" alt="" /></td>
		</tr>
		<tr>
		<td class="roundcornersleft">&nbsp;</td>
		<td valign="top">
	        <form action="show_tickets.php" method="get">
			<h3 style="margin-bottom:5px">&raquo; <?php echo $hesklang['show_tickets']; ?></h3>
			<table border="0" cellpadding="3" cellspacing="0" width="100%">
			<tr>
			<td class="alignTop"><b><?php echo $hesklang['status']; ?></b>: &nbsp; </td>
			<td>
            	<table border="0" cellpadding="0" cellspacing="0" width="100%">
				<tr>
				<td width="50%"><label><input type="radio" name="status" value="0" <?php if ($status == 0) {echo 'checked="checked"';} ?> /><span class="open"> <?php echo $hesklang['open']; ?></span></label></td>
				<td width="50%"><label><input type="radio" name="status" value="1" <?php if ($status == 1) {echo 'checked="checked"';} ?> /><span class="waitingreply"> <?php echo $hesklang['wait_reply']; ?></span></label></td>
				</tr>
				<tr>
				<td width="50%"><label><input type="radio" name="status" value="2" <?php if ($status == 2) {echo 'checked="checked"';} ?> /><span class="replied"> <?php echo $hesklang['replied']; ?></span></label></td>
				<td width="50%"><label><input type="radio" name="status" value="6" <?php if ($status == 6) {echo 'checked="checked"';} ?> /><span class="replied"> <?php echo $hesklang['all_not_closed']; ?></span></label></td>
				</tr>
				<tr>
				<td width="50%"><label><input type="radio" name="status" value="3" <?php if ($status == 3) {echo 'checked="checked"';} ?> /><span class="resolved"> <?php echo $hesklang['closed']; ?></span></label></td>
				<td width="50%"><label><input type="radio" name="status" value="4" <?php if ($status == 4) {echo 'checked="checked"';} ?> /> <?php echo $hesklang['any_status']; ?></label></td>
				</tr>
				</table>
            </td>
			</tr>
			<tr>
			<td class="borderTop alignTop"><b><?php echo $hesklang['sort_by']; ?></b>: &nbsp; </td>
			<td class="borderTop">
	            <table border="0" cellpadding="0" cellspacing="0" width="100%">
				<tr>
				<td width="50%"><label><input type="radio" name="sort" value="priority" <?php if ($sort == 'priority') {echo 'checked="checked"';} ?> /> <?php echo $hesklang['priority']; ?></label></td>
				<td width="50%"><label><input type="radio" name="sort" value="lastchange" <?php if ($sort == 'lastchange') {echo 'checked="checked"';} ?> /> <?php echo $hesklang['last_update']; ?></label></td>
				</tr>
				<tr>
				<td width="50%"><label><input type="radio" name="sort" value="name" <?php if ($sort == 'name') {echo 'checked="checked"';} ?> /> <?php echo $hesklang['name']; ?></label></td>
				<td width="50%"><label><input type="radio" name="sort" value="subject" <?php if ($sort == 'subject') {echo 'checked="checked"';} ?> /> <?php echo $hesklang['subject']; ?></label></td>
				</tr>
				<tr>
				<td width="50%"><label><input type="radio" name="sort" value="status" <?php if ($sort == 'status') {echo 'checked="checked"';} ?> /> <?php echo $hesklang['status']; ?></label></td>
				<td width="50%">&nbsp;</td>
				</tr>
				</table>
            </td>
			</tr>
			<tr>
			<td class="borderTop alignMiddle"><b><?php echo $hesklang['category']; ?></b>: &nbsp; </td>
			<td class="borderTop alignMiddle">
	            <select name="category">
				<option value="0" ><?php echo $hesklang['any_cat']; ?></option>
				<?php echo $category_options; ?>
				</select>
            </td>
			</tr>
			<tr>
			<td class="borderTop alignTop"><b><?php echo $hesklang['show']; ?></b>: &nbsp; </td>
			<td class="borderTop">
            	<label><input type="checkbox" name="s_my" value="1" <?php if ($s_my[1]) echo 'checked="checked"'; ?> /> <?php echo $hesklang['s_my']; ?></label><br />
                <?php
                if ($can_view_ass_others)
                {
                ?>
            	<label><input type="checkbox" name="s_ot" value="1" <?php if ($s_ot[1]) echo 'checked="checked"'; ?> /> <?php echo $hesklang['s_ot']; ?></label><br />
                <?php
                }
                ?>
            	<label><input type="checkbox" name="s_un" value="1" <?php if ($s_un[1]) echo 'checked="checked"'; ?> /> <?php echo $hesklang['s_un']; ?></label><br />
            	<label><input type="checkbox" name="archive" value="1" <?php if ($archive[1]) echo 'checked="checked"'; ?> /> <?php echo $hesklang['disp_only_archived']; ?></label><br />
            </td>
			</tr>
			<tr>
			<td class="borderTop"><b><?php echo $hesklang['display']; ?></b>: &nbsp; </td>
			<td class="borderTop"><input type="text" name="limit" value="<?php echo $maxresults; ?>" size="4" /> <?php echo $hesklang['tickets_page']; ?></td>
			</tr>
			<tr>
			<td class="borderTop alignMiddle"><b><?php echo $hesklang['order']; ?></b>: &nbsp; </td>
			<td class="borderTop alignMiddle">
            	<label><input type="radio" name="asc" value="1" <?php if ($asc) {echo 'checked="checked"';} ?> /> <?php echo $hesklang['ascending']; ?></label>
                |
                <label><input type="radio" name="asc" value="0" <?php if (!$asc) {echo 'checked="checked"';} ?> /> <?php echo $hesklang['descending']; ?></label></td>
			</tr>
			</table>
			<p align="center"><input type="submit" value="<?php echo $hesklang['show_tickets']; ?>" class="orangebutton" onmouseover="hesk_btn(this,'orangebuttonover');" onmouseout="hesk_btn(this,'orangebutton');" /></p>
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
</td>
<?php
$what = isset($_GET['what']) ? hesk_input($_GET['what']) : 'trackid';
?>
<td valign="top" width="50%">
	<table width="100%" border="0" cellspacing="0" cellpadding="0" style="height: 380px;">
		<tr>
			<td width="7" height="7"><img src="../img/roundcornerslt.jpg" width="7" height="7" alt="" /></td>
			<td class="roundcornerstop"></td>
			<td><img src="../img/roundcornersrt.jpg" width="7" height="7" alt="" /></td>
		</tr>
		<tr>
		<td class="roundcornersleft">&nbsp;</td>
		<td valign="top">
	    <form action="find_tickets.php" method="get" name="findby" id="findby">
		<h3 style="margin-bottom:5px">&raquo; <?php echo $hesklang['find_ticket_by']; ?></h3>
			<table border="0" cellpadding="3" cellspacing="0" width="100%">
			<tr>
			<td class="alignMiddle"><b><?php echo $hesklang['s_for']; ?></b>: &nbsp; </td>
			<td class="alignMiddle">
				<input type="text" name="q" size="30" <?php if (isset($q)) {echo 'value="'.$q.'"';} ?> />
            </td>
			</tr>
			<tr>
			<td class="borderTop alignMiddle"><b><?php echo $hesklang['s_in']; ?></b>: &nbsp; </td>
			<td class="borderTop alignMiddle">
	            <select name="what">
                <option value="trackid" <?php if ($what=='trackid') {echo 'selected="selected"';} ?> ><?php echo $hesklang['trackID']; ?></option>
                <option value="name"    <?php if ($what=='name') {echo 'selected="selected"';} ?> ><?php echo $hesklang['name']; ?></option>
                <option value="subject" <?php if ($what=='subject') {echo 'selected="selected"';} ?> ><?php echo $hesklang['subject']; ?></option>
                <option value="message" <?php if ($what=='message') {echo 'selected="selected"';} ?> ><?php echo $hesklang['message']; ?></option>
				<?php
				foreach ($hesk_settings['custom_fields'] as $k=>$v)
				{
                	$selected = ($what == $k) ? 'selected="selected"' : '';
					if ($v['use'])
					{
                    	$v['name'] = (strlen($v['name']) > 30) ? substr($v['name'],0,30) . '...' : $v['name'];
						echo '<option value="'.$k.'" '.$selected.'>'.$v['name'].'</option>';
					}
				}
				?>
				</select>
            </td>
			</tr>
			<tr>
			<td class="borderTop alignMiddle"><b><?php echo $hesklang['category']; ?></b>: &nbsp; </td>
			<td class="borderTop alignMiddle">
	            <select name="category">
				<option value="0" ><?php echo $hesklang['any_cat']; ?></option>
				<?php echo $category_options; ?>
				</select>
            </td>
			</tr>
			<tr>
			<td class="borderTop alignMiddle"><b><?php echo $hesklang['date']; ?></b>: &nbsp; </td>
			<td class="borderTop alignMiddle">
				<input type="text" name="dt" id="dt" size="10" <?php if ($date_input) {echo 'value="'.$date_input.'"';} ?> />
				<script language="Javascript" type="text/javascript">
                var heskselect = '';
				new tcal ({
				'formname': 'findby',
				'controlname': 'dt'
				});
				</script>
            </td>
			</tr>
			<tr>
			<td class="borderTop alignTop"><b><?php echo $hesklang['s_incl']; ?></b>: &nbsp; </td>
			<td class="borderTop">
            	<label><input type="checkbox" name="s_my" value="1" <?php if ($s_my[2]) echo 'checked="checked"'; ?> /> <?php echo $hesklang['s_my']; ?></label><br />
                <?php
                if ($can_view_ass_others)
                {
                ?>
            	<label><input type="checkbox" name="s_ot" value="1" <?php if ($s_ot[2]) echo 'checked="checked"'; ?> /> <?php echo $hesklang['s_ot']; ?></label><br />
                <?php
                }
                ?>
            	<label><input type="checkbox" name="s_un" value="1" <?php if ($s_un[2]) echo 'checked="checked"'; ?> /> <?php echo $hesklang['s_un']; ?></label><br />
            	<label><input type="checkbox" name="archive" value="1" <?php if ($archive[2]) echo 'checked="checked"'; ?> /> <?php echo $hesklang['disp_only_archived']; ?></label><br />
            </td>
			</tr>
			<tr>
			<td class="borderTop"><b><?php echo $hesklang['display']; ?></b>: &nbsp; </td>
			<td class="borderTop"><input type="text" name="limit" value="<?php echo $maxresults; ?>" size="4" /> <?php echo $hesklang['results_page']; ?></td>
			</tr>
			</table>

        <p align="center"><input type="submit" value="<?php echo $hesklang['find_ticket']; ?>" class="orangebutton" onmouseover="hesk_btn(this,'orangebuttonover');" onmouseout="hesk_btn(this,'orangebutton');"  /></p>
        <p>&nbsp;</p>
        <p>&nbsp;</p>
		</form>
    </td>
	<td class="roundcornersright">&nbsp;</td>
	</tr>
	<tr>
	<td width="6"><img src="../img/roundcornerslb.jpg" width="7" height="7" alt="" /></td>
	<td class="roundcornersbottom"></td>
	<td width="7" height="7"><img src="../img/roundcornersrb.jpg" width="7" height="7" alt="" /></td>
	</tr>
	</table>
</td>
</tr>
</table>
</div>
