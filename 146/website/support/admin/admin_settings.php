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

/* Make sure the install folder is deleted */
if (is_dir(HESK_PATH . 'install')) {die('Please delete the <b>install</b> folder from your server for security reasons then refresh this page!');}

/* Get all the required files and functions */
require(HESK_PATH . 'hesk_settings.inc.php');
/* Save the default language for the settings page before choosing user's preferred one */
$hesk_settings['language_default'] = $hesk_settings['language'];
require(HESK_PATH . 'inc/common.inc.php');
$hesk_settings['language'] = $hesk_settings['language_default'];
require(HESK_PATH . 'inc/database.inc.php');

hesk_session_start();
hesk_dbConnect();
hesk_isLoggedIn();

/* Check permissions for this feature */
hesk_checkPermission('can_man_settings');

/* Test languages function */
if (isset($_GET['test_languages']))
{
	hesk_testLanguage(0);
}

$enable_save_settings   = 0;
$enable_use_attachments = 0;

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

<h3 align="center"><?php echo $hesklang['settings']; ?></h3>

<p><?php echo $hesklang['settings_intro'] . ' <b>' . $hesklang['all_req']; ?></b></p>

<?php
$hesklang['err_custname'] = addslashes($hesklang['err_custname']);
?>

<script language="javascript" type="text/javascript"><!--
function hesk_checkFields() {
d=document.form1;
if (d.s_site_title.value=='') {alert('<?php echo addslashes($hesklang['err_sname']); ?>'); return false;}
if (d.s_site_url.value=='') {alert('<?php echo addslashes($hesklang['err_surl']); ?>'); return false;}

if (d.s_support_mail.value=='' || d.s_support_mail.value.indexOf(".") == -1 || d.s_support_mail.value.indexOf("@") == -1)
{alert('<?php echo addslashes($hesklang['err_supmail']); ?>'); return false;}
if (d.s_webmaster_mail.value=='' || d.s_webmaster_mail.value.indexOf(".") == -1 || d.s_webmaster_mail.value.indexOf("@") == -1)
{alert('<?php echo addslashes($hesklang['err_wmmail']); ?>'); return false;}
if (d.s_noreply_mail.value=='' || d.s_noreply_mail.value.indexOf(".") == -1 || d.s_noreply_mail.value.indexOf("@") == -1)
{alert('<?php echo addslashes($hesklang['err_nomail']); ?>'); return false;}

if (d.s_hesk_title.value=='') {alert('<?php echo addslashes($hesklang['err_htitle']); ?>'); return false;}
if (d.s_hesk_url.value=='') {alert('<?php echo addslashes($hesklang['err_hurl']); ?>'); return false;} 
if (d.s_server_path.value=='') {alert('<?php echo addslashes($hesklang['err_spath']); ?>'); return false;}
if (d.s_max_listings.value=='') {alert('<?php echo addslashes($hesklang['err_max']); ?>'); return false;}
if (d.s_print_font_size.value=='') {alert('<?php echo addslashes($hesklang['err_psize']); ?>'); return false;}

if (d.s_db_host.value=='') {alert('<?php echo addslashes($hesklang['err_dbhost']); ?>'); return false;}
if (d.s_db_name.value=='') {alert('<?php echo addslashes($hesklang['err_dbname']); ?>'); return false;}
if (d.s_db_user.value=='') {alert('<?php echo addslashes($hesklang['err_dbuser']); ?>'); return false;}
if (d.s_db_pass.value=='')
{
	if (!confirm('<?php echo addslashes($hesklang['mysql_root']); ?>'))
    {
    	return false;
    }
}

if (d.s_custom1_use.checked && d.s_custom1_name.value == '') {alert('<?php echo $hesklang['err_custname']; ?>'); return false;}
if (d.s_custom2_use.checked && d.s_custom2_name.value == '') {alert('<?php echo $hesklang['err_custname']; ?>'); return false;}
if (d.s_custom3_use.checked && d.s_custom3_name.value == '') {alert('<?php echo $hesklang['err_custname']; ?>'); return false;}
if (d.s_custom4_use.checked && d.s_custom4_name.value == '') {alert('<?php echo $hesklang['err_custname']; ?>'); return false;}
if (d.s_custom5_use.checked && d.s_custom5_name.value == '') {alert('<?php echo $hesklang['err_custname']; ?>'); return false;}
if (d.s_custom6_use.checked && d.s_custom6_name.value == '') {alert('<?php echo $hesklang['err_custname']; ?>'); return false;}
if (d.s_custom7_use.checked && d.s_custom7_name.value == '') {alert('<?php echo $hesklang['err_custname']; ?>'); return false;}
if (d.s_custom8_use.checked && d.s_custom8_name.value == '') {alert('<?php echo $hesklang['err_custname']; ?>'); return false;}
if (d.s_custom9_use.checked && d.s_custom9_name.value == '') {alert('<?php echo $hesklang['err_custname']; ?>'); return false;}
if (d.s_custom10_use.checked && d.s_custom10_name.value == '') {alert('<?php echo $hesklang['err_custname']; ?>'); return false;}
if (d.s_custom11_use.checked && d.s_custom11_name.value == '') {alert('<?php echo $hesklang['err_custname']; ?>'); return false;}
if (d.s_custom12_use.checked && d.s_custom12_name.value == '') {alert('<?php echo $hesklang['err_custname']; ?>'); return false;}
if (d.s_custom13_use.checked && d.s_custom13_name.value == '') {alert('<?php echo $hesklang['err_custname']; ?>'); return false;}
if (d.s_custom14_use.checked && d.s_custom14_name.value == '') {alert('<?php echo $hesklang['err_custname']; ?>'); return false;}
if (d.s_custom15_use.checked && d.s_custom15_name.value == '') {alert('<?php echo $hesklang['err_custname']; ?>'); return false;}
if (d.s_custom16_use.checked && d.s_custom16_name.value == '') {alert('<?php echo $hesklang['err_custname']; ?>'); return false;}
if (d.s_custom17_use.checked && d.s_custom17_name.value == '') {alert('<?php echo $hesklang['err_custname']; ?>'); return false;}
if (d.s_custom18_use.checked && d.s_custom18_name.value == '') {alert('<?php echo $hesklang['err_custname']; ?>'); return false;}
if (d.s_custom19_use.checked && d.s_custom19_name.value == '') {alert('<?php echo $hesklang['err_custname']; ?>'); return false;}
if (d.s_custom20_use.checked && d.s_custom20_name.value == '') {alert('<?php echo $hesklang['err_custname']; ?>'); return false;}

return true;
}

function hesk_customOptions(cID, fID, fTYPE, maxlenID, oldTYPE)
{
	var t = document.getElementById(fTYPE).value;
    if (t == oldTYPE)
    {
		var d = document.getElementById(fID).value;
	    var m = document.getElementById(maxlenID).value;
    }
    else
    {
    	var d = '';
        var m = 255;
    }
    var myURL = "options.php?i=" + cID + "&q=" + escape(d) + "&t=" + t + "&m=" + m;
    window.open(myURL,"Hesk_window","height=400,width=500,menubar=0,location=0,toolbar=0,status=0,resizable=1,scrollbars=1");
    return false;
}

function hesk_toggleLayer(nr,setto) {
        if (document.all)
                document.all[nr].style.display = setto;
        else if (document.getElementById)
                document.getElementById(nr).style.display = setto;
}

function hesk_testLanguage()
{
    window.open('admin_settings.php?test_languages=1',"Hesk_window","height=400,width=500,menubar=0,location=0,toolbar=0,status=0,resizable=1,scrollbars=1");
    return false;
}

//-->
</script>

<form method="post" action="admin_settings_save.php" name="form1" onsubmit="return hesk_checkFields()">

<!-- Checkign status of files and folders -->
<span class="section">&raquo; <?php echo $hesklang['check_status']; ?></span>

<table width="100%" border="0" cellspacing="0" cellpadding="0">
	<tr>
		<td width="7" height="7"><img src="../img/roundcornerslt.jpg" width="7" height="7" alt="" /></td>
		<td class="roundcornerstop"></td>
		<td><img src="../img/roundcornersrt.jpg" width="7" height="7" alt="" /></td>
	</tr>
	<tr>
	<td class="roundcornersleft">&nbsp;</td>
	<td>

	<table border="0">
	<tr>
	<td width="200" valign="top"><?php echo $hesklang['v']; ?>:</td>
	<td><b><?php echo $hesk_settings['hesk_version']; ?></b> (<a href="http://www.hesk.com/update.php?v=<?php echo $hesk_settings['hesk_version']; ?>" target="_blank"><?php echo $hesklang['check4updates']; ?></a>)</td>
	</tr>
	<tr>
	<td width="200" valign="top">/hesk_settings.inc.php</td>
	<td>
	<?php
	if (is_writable(HESK_PATH . 'hesk_settings.inc.php')) {
	    $enable_save_settings=1;
	    echo '<font class="success">'.$hesklang['exists'].'</font>, <font class="success">'.$hesklang['writable'].'</font>';
	} else {
	    echo '<font class="success">'.$hesklang['exists'].'</font>, <font class="error">'.$hesklang['not_writable'].'</font><br />'.$hesklang['e_settings'];
	}
	?>
	</td>
	</tr>
	<tr>
	<td width="200">/attachments</td>
	<td>
	<?php
	if (!file_exists(HESK_PATH . 'attachments'))
	{
	    @mkdir(HESK_PATH . 'attachments', 0777);
	}

	if (is_dir(HESK_PATH . 'attachments'))
	{
	    echo '<font class="success">'.$hesklang['exists'].'</font>, ';
	    if (is_writable(HESK_PATH . 'attachments'))
	    {
	        $enable_use_attachments=1;
	        echo '<font class="success">'.$hesklang['writable'].'</font>';
	    }
	    else
	    {
	        echo '<font class="error">'.$hesklang['not_writable'].'</font><br />'.$hesklang['e_attdir'];
	    }
	}
	else
	{
	    echo '<font class="error">'.$hesklang['no_exists'].'</font>, <font class="error">'.$hesklang['not_writable'].'</font><br />'.$hesklang['e_attdir'];
	}
	?>
	</td>
	</tr>
	</table>

	</td>
	<td class="roundcornersright">&nbsp;</td>
	</tr>
	<tr>
	<td><img src="../img/roundcornerslb.jpg" width="7" height="7" alt="" /></td>
	<td class="roundcornersbottom"></td>
	<td width="7" height="7"><img src="../img/roundcornersrb.jpg" width="7" height="7" alt="" /></td>
	</tr>
</table>

<br />

<span class="section">&raquo; <?php echo $hesklang['gs']; ?></span>

<!-- Website info -->
<table width="100%" border="0" cellspacing="0" cellpadding="0">
	<tr>
		<td width="7" height="7"><img src="../img/roundcornerslt.jpg" width="7" height="7" alt="" /></td>
		<td class="roundcornerstop"></td>
		<td><img src="../img/roundcornersrt.jpg" width="7" height="7" alt="" /></td>
	</tr>
	<tr>
	<td class="roundcornersleft">&nbsp;</td>
	<td>

	<table border="0">
	<tr>
	<td style="text-align:right" width="200"><?php echo $hesklang['wbst_title']; ?>: [<a href="Javascript:void(0)" onclick="Javascript:hesk_window('../help_files/settings.html#1','400','500')"><b>?</b></a>]</td>
	<td><input type="text" name="s_site_title" size="40" maxlength="255" value="<?php echo $hesk_settings['site_title']; ?>" /></td>
	</tr>
	<tr>
	<td style="text-align:right" width="200"><?php echo $hesklang['wbst_url']; ?>: [<a href="Javascript:void(0)" onclick="Javascript:hesk_window('../help_files/settings.html#2','400','500')"><b>?</b></a>]</td>
	<td><input type="text" name="s_site_url" size="40" maxlength="255" value="<?php echo $hesk_settings['site_url']; ?>" /></td>
	</tr>
	<tr>
	<td style="text-align:right" width="200"><?php echo $hesklang['email_sup']; ?>: [<a href="Javascript:void(0)" onclick="Javascript:hesk_window('../help_files/settings.html#3','400','500')"><b>?</b></a>]</td>
	<td><input type="text" name="s_support_mail" size="40" maxlength="255" value="<?php echo $hesk_settings['support_mail']; ?>" /></td>
	</tr>
	<tr>
	<td style="text-align:right" width="200"><?php echo $hesklang['email_wm']; ?>: [<a href="Javascript:void(0)" onclick="Javascript:hesk_window('../help_files/settings.html#4','400','500')"><b>?</b></a>]</td>
	<td><input type="text" name="s_webmaster_mail" size="40" maxlength="255" value="<?php echo $hesk_settings['webmaster_mail']; ?>" /></td>
	</tr>
	<tr>
	<td style="text-align:right" width="200"><?php echo $hesklang['email_noreply']; ?>: [<a href="Javascript:void(0)" onclick="Javascript:hesk_window('../help_files/settings.html#5','400','500')"><b>?</b></a>]</td>
	<td><input type="text" name="s_noreply_mail" size="40" maxlength="255" value="<?php echo $hesk_settings['noreply_mail']; ?>" /></td>
	</tr>
	</table>

	</td>
	<td class="roundcornersright">&nbsp;</td>
	</tr>
	<tr>
	<td><img src="../img/roundcornerslb.jpg" width="7" height="7" alt="" /></td>
	<td class="roundcornersbottom"></td>
	<td width="7" height="7"><img src="../img/roundcornersrb.jpg" width="7" height="7" alt="" /></td>
	</tr>
</table>

<br />

<span class="section">&raquo; <?php echo $hesklang['lgs']; ?></span>

<!-- Language -->
<table width="100%" border="0" cellspacing="0" cellpadding="0">
	<tr>
		<td width="7" height="7"><img src="../img/roundcornerslt.jpg" width="7" height="7" alt="" /></td>
		<td class="roundcornerstop"></td>
		<td><img src="../img/roundcornersrt.jpg" width="7" height="7" alt="" /></td>
	</tr>
	<tr>
	<td class="roundcornersleft">&nbsp;</td>
	<td>

	<table border="0">
	<tr>
	<td style="text-align:right;vertical-align:top" width="200"><?php echo $hesklang['hesk_lang']; ?>: [<a href="Javascript:void(0)" onclick="Javascript:hesk_window('../help_files/settings.html#9','400','500')"><b>?</b></a>]</td>
	<td>
	<select name="s_language">
	<?php echo hesk_testLanguage(1); ?>
	</select>
    &nbsp;
    <a href="Javascript:void(0)" onclick="Javascript:return hesk_testLanguage()"><?php echo $hesklang['s_inl']; ?></a><br />
    <label><input type="checkbox" name="make_default_language" value="1" /> <?php echo $hesklang['mmdl']; ?></label>
	</td>
	</tr>
	<tr>
	<td style="text-align:right;vertical-align:top;" width="200"><?php echo $hesklang['s_mlang']; ?>: [<a href="Javascript:void(0)" onclick="Javascript:hesk_window('../help_files/settings.html#43','400','500')"><b>?</b></a>]</td>
	<td>
	<?php
	    $on = $hesk_settings['can_sel_lang'] ? 'checked="checked"' : '';
	    $off = $hesk_settings['can_sel_lang'] ? '' : 'checked="checked"';
	    echo '
	    <label><input type="radio" name="s_can_sel_lang" value="0" '.$off.' /> '.$hesklang['no'].'</label> |
	    <label><input type="radio" name="s_can_sel_lang" value="1" '.$on.' /> '.$hesklang['yes'].'</label>';
	?><br />
    <?php echo $hesklang['s_mlange']; ?>
	</td>
	</tr>
	</table>

	</td>
	<td class="roundcornersright">&nbsp;</td>
	</tr>
	<tr>
	<td><img src="../img/roundcornerslb.jpg" width="7" height="7" alt="" /></td>
	<td class="roundcornersbottom"></td>
	<td width="7" height="7"><img src="../img/roundcornersrb.jpg" width="7" height="7" alt="" /></td>
	</tr>
</table>

<br />

<!-- Helpdesk settings -->
<span class="section">&raquo; <?php echo $hesklang['hd']; ?></span>

<table width="100%" border="0" cellspacing="0" cellpadding="0">
	<tr>
		<td width="7" height="7"><img src="../img/roundcornerslt.jpg" width="7" height="7" alt="" /></td>
		<td class="roundcornerstop"></td>
		<td><img src="../img/roundcornersrt.jpg" width="7" height="7" alt="" /></td>
	</tr>
	<tr>
	<td class="roundcornersleft">&nbsp;</td>
	<td>

	<table border="0">
	<tr>
	<td style="text-align:right" width="200"><?php echo $hesklang['hesk_title']; ?>: [<a href="Javascript:void(0)" onclick="Javascript:hesk_window('../help_files/settings.html#6','400','500')"><b>?</b></a>]</td>
	<td><input type="text" name="s_hesk_title" size="40" maxlength="255" value="<?php echo $hesk_settings['hesk_title']; ?>" /></td>
	</tr>
	<tr>
	<td style="text-align:right" width="200"><?php echo $hesklang['hesk_url']; ?>: [<a href="Javascript:void(0)" onclick="Javascript:hesk_window('../help_files/settings.html#7','400','500')"><b>?</b></a>]</td>
	<td><input type="text" name="s_hesk_url" size="40" maxlength="255" value="<?php echo $hesk_settings['hesk_url']; ?>" /></td>
	</tr>
	<tr>
	<td style="text-align:right" width="200"><?php echo $hesklang['hesk_path']; ?>: [<a href="Javascript:void(0)" onclick="Javascript:hesk_window('../help_files/settings.html#8','400','500')"><b>?</b></a>]</td>
	<td><input type="text" name="s_server_path" size="40" maxlength="255" value="<?php
	if ($hesk_settings['server_path'] == '/home/mysite/public_html/hesk') {
	    echo getcwd();
	} else {
	    echo $hesk_settings['server_path'];
	}
	?>" />
	</td>
	</tr>
	<tr>
	<td style="text-align:right" width="200"><?php echo $hesklang['max_listings']; ?>: [<a href="Javascript:void(0)" onclick="Javascript:hesk_window('../help_files/settings.html#10','400','500')"><b>?</b></a>]</td>
	<td><input type="text" name="s_max_listings" size="5" maxlength="30" value="<?php echo $hesk_settings['max_listings']; ?>" /></td>
	</tr>
	<tr>
	<td style="text-align:right" width="200"><?php echo $hesklang['print_size']; ?>: [<a href="Javascript:void(0)" onclick="Javascript:hesk_window('../help_files/settings.html#11','400','500')"><b>?</b></a>]</td>
	<td><input type="text" name="s_print_font_size" size="5" maxlength="3" value="<?php echo $hesk_settings['print_font_size']; ?>" /></td>
	</tr>
	<tr>
	<td style="text-align:right" width="200"><?php echo $hesklang['debug_mode']; ?>: [<a href="Javascript:void(0)" onclick="Javascript:hesk_window('../help_files/settings.html#12','400','500')"><b>?</b></a>]</td>
	<td>
	<?php
	    $on = $hesk_settings['debug_mode'] ? 'checked="checked"' : '';
	    $off = $hesk_settings['debug_mode'] ? '' : 'checked="checked"';
	    echo '
	    <label><input type="radio" name="s_debug_mode" value="0" '.$off.' /> '.$hesklang['off'].'</label> |
	    <label><input type="radio" name="s_debug_mode" value="1" '.$on.' /> '.$hesklang['on'].'</label>';
	?>
	</td>
	</tr>
	<tr>
	<td style="text-align:right" width="200"><?php echo $hesklang['use_secimg']; ?>: [<a href="Javascript:void(0)" onclick="Javascript:hesk_window('../help_files/settings.html#13','400','500')"><b>?</b></a>]</td>
	<td>
	<?php
	if(function_exists('imagecreate'))
	{
	    $on = $hesk_settings['secimg_use'] ? 'checked="checked"' : '';
	    $off = $hesk_settings['secimg_use'] ? '' : 'checked="checked"';
	    echo '
	    <label><input type="radio" name="s_secimg_use" value="0" '.$off.' /> '.$hesklang['off'].'</label> |
	    <label><input type="radio" name="s_secimg_use" value="1" '.$on.' /> '.$hesklang['on'].'</label>';
	}
	else
	{
	    echo $hesklang['secimg_no'];
	}
	?>
	</td>
	</tr>
	<tr>
	<td style="text-align:right" width="200" valign="top"><?php echo $hesklang['use_q']; ?>: [<a href="Javascript:void(0)" onclick="Javascript:hesk_window('../help_files/settings.html#42','400','500')"><b>?</b></a>]</td>
	<td>
	<?php

        $on  = '';
        $off = '';
        $div = 'block';

    	if ($hesk_settings['question_use'])
        {
        	$on = 'checked="checked"';
        }
        else
        {
        	$off = 'checked="checked"';
            $div = 'none';
        }
	    echo '
	    <label><input type="radio" name="s_question_use" value="0" '.$off.' onclick="javascript:hesk_toggleLayer(\'question\',\'none\')" /> '.$hesklang['off'].'</label> |
	    <label><input type="radio" name="s_question_use" value="1" '.$on.' onclick="javascript:hesk_toggleLayer(\'question\',\'block\')" /> '.$hesklang['on'].'</label>';
	?>
    	<div id="question" style="display: <?php echo $div; ?>;">

        <a href="Javascript:void(0)" onclick="Javascript:hesk_rate('generate_spam_question.php','question')"><?php echo $hesklang['genq']; ?></a><br />

        <?php echo $hesklang['q_q']; ?>:<br />
        <textarea name="s_question_ask" rows="3" cols="40"><?php echo htmlentities($hesk_settings['question_ask']); ?></textarea><br />

        <?php echo $hesklang['q_a']; ?>:<br />
        <input type="text" name="s_question_ans" value="<?php echo $hesk_settings['question_ans']; ?>" size="10" />

        </div>
	</td>
	</tr>
	<tr>
	<td style="text-align:right" width="200"><?php echo $hesklang['lu']; ?>: [<a href="Javascript:void(0)" onclick="Javascript:hesk_window('../help_files/settings.html#14','400','500')"><b>?</b></a>]</td>
	<td>
	<?php
	    $on = $hesk_settings['list_users'] ? 'checked="checked"' : '';
	    $off = $hesk_settings['list_users'] ? '' : 'checked="checked"';
	    echo '
	    <label><input type="radio" name="s_list_users" value="0" '.$off.' /> '.$hesklang['off'].'</label> |
	    <label><input type="radio" name="s_list_users" value="1" '.$on.' /> '.$hesklang['on'].'</label>';
	?>
	</td>
	</tr>


	<tr>
	<td style="text-align:right" width="200"><?php echo $hesklang['alo']; ?>: [<a href="Javascript:void(0)" onclick="Javascript:hesk_window('../help_files/settings.html#44','400','500')"><b>?</b></a>]</td>
	<td>
	<?php
	    $on = $hesk_settings['autologin'] ? 'checked="checked"' : '';
	    $off = $hesk_settings['autologin'] ? '' : 'checked="checked"';
	    echo '
	    <label><input type="radio" name="s_autologin" value="0" '.$off.' /> '.$hesklang['no'].'</label> |
	    <label><input type="radio" name="s_autologin" value="1" '.$on.' /> '.$hesklang['yes'].'</label>';
	?>
	</td>
	</tr>


	<tr>
	<td style="text-align:right" width="200"><?php echo $hesklang['aclose']; ?>: [<a href="Javascript:void(0)" onclick="Javascript:hesk_window('../help_files/settings.html#15','400','500')"><b>?</b></a>]</td>
	<td><input type="text" name="s_autoclose" size="5" maxlength="3" value="<?php echo $hesk_settings['autoclose']; ?>" />
	<?php echo $hesklang['aclose2']; ?></td>
	</tr>
	<tr>
	<td style="text-align:right" width="200"><?php echo $hesklang['s_ucrt']; ?>: [<a href="Javascript:void(0)" onclick="Javascript:hesk_window('../help_files/settings.html#16','400','500')"><b>?</b></a>]</td>
	<td>
	<?php
	    $on = $hesk_settings['custopen'] ? 'checked="checked"' : '';
	    $off = $hesk_settings['custopen'] ? '' : 'checked="checked"';
	    echo '
	    <label><input type="radio" name="s_custopen" value="0" '.$off.' /> '.$hesklang['off'].'</label> |
	    <label><input type="radio" name="s_custopen" value="1" '.$on.' /> '.$hesklang['on'].'</label>';
	?>
	</td>
	</tr>
	<tr>
	<td style="text-align:right" width="200"><?php echo $hesklang['urate']; ?>: [<a href="Javascript:void(0)" onclick="Javascript:hesk_window('../help_files/settings.html#17','400','500')"><b>?</b></a>]</td>
	<td>
	<?php
	    $on = $hesk_settings['rating'] ? 'checked="checked"' : '';
	    $off = $hesk_settings['rating'] ? '' : 'checked="checked"';
	    echo '
	    <label><input type="radio" name="s_rating" value="0" '.$off.' /> '.$hesklang['off'].'</label> |
	    <label><input type="radio" name="s_rating" value="1" '.$on.' /> '.$hesklang['on'].'</label>';
	?>
	</td>
	</tr>
	<tr>
	<td style="text-align:right" width="200" valign="top"><?php echo $hesklang['server_time']; ?>: [<a href="Javascript:void(0)" onclick="Javascript:hesk_window('../help_files/settings.html#18','400','500')"><b>?</b></a>]</td>
	<td><?php echo $hesklang['csrt'] .' ' . date('H:i'); ?><br />
	<input type="text" name="s_diff_hours" size="5" maxlength="3" value="<?php echo $hesk_settings['diff_hours']; ?>" />
	<?php echo $hesklang['t_h']; ?> <br />
	<input type="text" name="s_diff_minutes" size="5" maxlength="3" value="<?php echo $hesk_settings['diff_minutes']; ?>" />
	<?php echo $hesklang['t_m']; ?></td>
	</tr>
	<tr>
	<td style="text-align:right" width="200"><?php echo $hesklang['day']; ?>: [<a href="Javascript:void(0)" onclick="Javascript:hesk_window('../help_files/settings.html#19','400','500')"><b>?</b></a>]</td>
	<td>
	<?php
	    $on = $hesk_settings['daylight'] ? 'checked="checked"' : '';
	    $off = $hesk_settings['daylight'] ? '' : 'checked="checked"';
	    echo '
	    <label><input type="radio" name="s_daylight" value="0" '.$off.' /> '.$hesklang['off'].'</label> |
	    <label><input type="radio" name="s_daylight" value="1" '.$on.' /> '.$hesklang['on'].'</label>';
	?>
	</td>
	</tr>
	<tr>
	<td style="text-align:right" width="200"><?php echo $hesklang['tfor']; ?>: [<a href="Javascript:void(0)" onclick="Javascript:hesk_window('../help_files/settings.html#20','400','500')"><b>?</b></a>]</td>
	<td><input type="text" name="s_timeformat" size="40" maxlength="255" value="<?php echo $hesk_settings['timeformat']; ?>" /></td>
	</tr>
	<tr>
	<td style="text-align:right" width="200"><?php echo $hesklang['al']; ?>: [<a href="Javascript:void(0)" onclick="Javascript:hesk_window('../help_files/settings.html#21','400','500')"><b>?</b></a>]</td>
	<td><label><input type="checkbox" name="s_alink" value="1" <?php if ($hesk_settings['alink']) {echo 'checked="checked"';} ?>/> <?php echo $hesklang['dap']; ?></label></td>
	</tr>
	<tr>
	<td style="text-align:right" width="200"><?php echo $hesklang['cpri']; ?>: [<a href="Javascript:void(0)" onclick="Javascript:hesk_window('../help_files/settings.html#45','400','500')"><b>?</b></a>]</td>
	<td>
	<?php
	    $on = $hesk_settings['cust_urgency'] ? 'checked="checked"' : '';
	    $off = $hesk_settings['cust_urgency'] ? '' : 'checked="checked"';
	    echo '
	    <label><input type="radio" name="s_cust_urgency" value="0" '.$off.' /> '.$hesklang['off'].'</label> |
	    <label><input type="radio" name="s_cust_urgency" value="1" '.$on.' /> '.$hesklang['on'].'</label>';
	?>
	</td>
	</tr>
	</table>

	</td>
	<td class="roundcornersright">&nbsp;</td>
	</tr>
	<tr>
	<td><img src="../img/roundcornerslb.jpg" width="7" height="7" alt="" /></td>
	<td class="roundcornersbottom"></td>
	<td width="7" height="7"><img src="../img/roundcornersrb.jpg" width="7" height="7" alt="" /></td>
	</tr>
</table>

<br />

<!-- Knowledgebase settings -->
<span class="section">&raquo; <?php echo $hesklang['kb_text']; ?></span>

<table width="100%" border="0" cellspacing="0" cellpadding="0">
	<tr>
		<td width="7" height="7"><img src="../img/roundcornerslt.jpg" width="7" height="7" alt="" /></td>
		<td class="roundcornerstop"></td>
		<td><img src="../img/roundcornersrt.jpg" width="7" height="7" alt="" /></td>
	</tr>
	<tr>
	<td class="roundcornersleft">&nbsp;</td>
	<td>

	<table border="0">
	<tr>
	<td style="text-align:right" width="200"><?php echo $hesklang['s_ekb']; ?>: [<a href="Javascript:void(0)" onclick="Javascript:hesk_window('../help_files/settings.html#22','400','500')"><b>?</b></a>]</td>
	<td>
	<?php
	    $on = $hesk_settings['kb_enable'] ? 'checked="checked"' : '';
	    $off = $hesk_settings['kb_enable'] ? '' : 'checked="checked"';
	    echo '
	    <label><input type="radio" name="s_kb_enable" value="0" '.$off.' /> '.$hesklang['off'].'</label> |
	    <label><input type="radio" name="s_kb_enable" value="1" '.$on.' /> '.$hesklang['on'].'</label>';
	?>
	</td>
	</tr>
	<tr>
	<td style="text-align:right" width="200"><?php echo $hesklang['s_suggest']; ?>: [<a href="Javascript:void(0)" onclick="Javascript:hesk_window('../help_files/settings.html#23','400','500')"><b>?</b></a>]</td>
	<td>
	<?php
	    $on = $hesk_settings['kb_recommendanswers'] ? 'checked="checked"' : '';
	    $off = $hesk_settings['kb_recommendanswers'] ? '' : 'checked="checked"';
	    echo '
	    <label><input type="radio" name="s_kb_recommendanswers" value="0" '.$off.' /> '.$hesklang['no'].'</label> |
	    <label><input type="radio" name="s_kb_recommendanswers" value="1" '.$on.' /> '.$hesklang['yes'].'</label>';
	?>
	</td>
	</tr>
	<tr>
	<td style="text-align:right" width="200"><?php echo $hesklang['s_kbr']; ?>: [<a href="Javascript:void(0)" onclick="Javascript:hesk_window('../help_files/settings.html#24','400','500')"><b>?</b></a>]</td>
	<td>
	<?php
	    $on = $hesk_settings['kb_rating'] ? 'checked="checked"' : '';
	    $off = $hesk_settings['kb_rating'] ? '' : 'checked="checked"';
	    echo '
	    <label><input type="radio" name="s_kb_rating" value="0" '.$off.' /> '.$hesklang['no'].'</label> |
	    <label><input type="radio" name="s_kb_rating" value="1" '.$on.' /> '.$hesklang['yes'].'</label>';
	?>
	</td>
	</tr>
	<tr>
	<td style="text-align:right" width="200"><?php echo $hesklang['s_kbs']; ?>: [<a href="Javascript:void(0)" onclick="Javascript:hesk_window('../help_files/settings.html#25','400','500')"><b>?</b></a>]</td>
	<td>
	<?php
		$off = $hesk_settings['kb_search'] ? '' : 'checked="checked"';
		$small = $hesk_settings['kb_search'] == 1 ? 'checked="checked"' : '';
		$large = $hesk_settings['kb_search'] == 2 ? 'checked="checked"' : '';

		echo '
		<label><input type="radio" name="s_kb_search" value="0" '.$off.' /> '.$hesklang['off'].'</label> |
		<label><input type="radio" name="s_kb_search" value="1" '.$small.' /> '.$hesklang['small'].'</label> |
		<label><input type="radio" name="s_kb_search" value="2" '.$large.' /> '.$hesklang['large'].'</label>
		';
	?>
	</td>
	</tr>
	<tr>
	<td style="text-align:right" width="200"><?php echo $hesklang['s_maxsr']; ?>: [<a href="Javascript:void(0)" onclick="Javascript:hesk_window('../help_files/settings.html#26','400','500')"><b>?</b></a>]</td>
	<td><input type="text" name="s_kb_search_limit" size="5" maxlength="3" value="<?php echo $hesk_settings['kb_search_limit']; ?>" /></td>
	</tr>
	<tr>
	<td style="text-align:right" width="200"><?php echo $hesklang['s_ptxt']; ?>: [<a href="Javascript:void(0)" onclick="Javascript:hesk_window('../help_files/settings.html#27','400','500')"><b>?</b></a>]</td>
	<td><input type="text" name="s_kb_substrart" size="5" maxlength="5" value="<?php echo $hesk_settings['kb_substrart']; ?>" /></td>
	</tr>
	<tr>
	<td style="text-align:right" width="200"><?php echo $hesklang['s_scol']; ?>: [<a href="Javascript:void(0)" onclick="Javascript:hesk_window('../help_files/settings.html#28','400','500')"><b>?</b></a>]</td>
	<td><input type="text" name="s_kb_cols" size="5" maxlength="2" value="<?php echo $hesk_settings['kb_cols']; ?>" /></td>
	</tr>
	<tr>
	<td style="text-align:right" width="200"><?php echo $hesklang['s_psubart']; ?>: [<a href="Javascript:void(0)" onclick="Javascript:hesk_window('../help_files/settings.html#29','400','500')"><b>?</b></a>]</td>
	<td><input type="text" name="s_kb_numshow" size="5" maxlength="2" value="<?php echo $hesk_settings['kb_numshow']; ?>" /></td>
	</tr>
	<tr>
	<td valign="top" style="text-align:right" width="200"><?php echo $hesklang['s_spop']; ?>: [<a href="Javascript:void(0)" onclick="Javascript:hesk_window('../help_files/settings.html#30','400','500')"><b>?</b></a>]</td>
	<td>
	<input type="text" name="s_kb_index_popart" size="5" maxlength="2" value="<?php echo $hesk_settings['kb_index_popart']; ?>" /> <?php echo $hesklang['s_onin']; ?><br />
	<input type="text" name="s_kb_popart" size="5" maxlength="2" value="<?php echo $hesk_settings['kb_popart']; ?>" /> <?php echo $hesklang['s_onkb']; ?>
	</td>
	</tr>
	<tr>
	<td valign="top" style="text-align:right" width="200"><?php echo $hesklang['s_slat']; ?>: [<a href="Javascript:void(0)" onclick="Javascript:hesk_window('../help_files/settings.html#31','400','500')"><b>?</b></a>]</td>
	<td>
	<input type="text" name="s_kb_index_latest" size="5" maxlength="2" value="<?php echo $hesk_settings['kb_index_latest']; ?>" /> <?php echo $hesklang['s_onin']; ?><br />
	<input type="text" name="s_kb_latest" size="5" maxlength="2" value="<?php echo $hesk_settings['kb_latest']; ?>" /> <?php echo $hesklang['s_onkb']; ?>
	</td>
	</tr>
	</table>

	</td>
	<td class="roundcornersright">&nbsp;</td>
	</tr>
	<tr>
	<td><img src="../img/roundcornerslb.jpg" width="7" height="7" alt="" /></td>
	<td class="roundcornersbottom"></td>
	<td width="7" height="7"><img src="../img/roundcornersrb.jpg" width="7" height="7" alt="" /></td>
	</tr>
</table>

<br />

<!-- Database settings -->
<span class="section">&raquo; <?php echo $hesklang['db']; ?></span>

<table width="100%" border="0" cellspacing="0" cellpadding="0">
	<tr>
		<td width="7" height="7"><img src="../img/roundcornerslt.jpg" width="7" height="7" alt="" /></td>
		<td class="roundcornerstop"></td>
		<td><img src="../img/roundcornersrt.jpg" width="7" height="7" alt="" /></td>
	</tr>
	<tr>
	<td class="roundcornersleft">&nbsp;</td>
	<td>

	<table border="0">
	<tr>
	<td style="text-align:right" width="200"><?php echo $hesklang['db_host']; ?>: [<a href="Javascript:void(0)" onclick="Javascript:hesk_window('../help_files/settings.html#32','400','500')"><b>?</b></a>]</td>
	<td><input type="text" name="s_db_host" size="30" maxlength="255" value="<?php echo $hesk_settings['db_host']; ?>" /></td>
	</tr>
	<tr>
	<td style="text-align:right" width="200"><?php echo $hesklang['db_name']; ?>: [<a href="Javascript:void(0)" onclick="Javascript:hesk_window('../help_files/settings.html#33','400','500')"><b>?</b></a>]</td>
	<td><input type="text" name="s_db_name" size="30" maxlength="255" value="<?php echo $hesk_settings['db_name']; ?>" /></td>
	</tr>
	<tr>
	<td style="text-align:right" width="200"><?php echo $hesklang['db_user']; ?>: [<a href="Javascript:void(0)" onclick="Javascript:hesk_window('../help_files/settings.html#34','400','500')"><b>?</b></a>]</td>
	<td><input type="text" name="s_db_user" size="30" maxlength="255" value="<?php echo $hesk_settings['db_user']; ?>" /></td>
	</tr>
	<tr>
	<td style="text-align:right" width="200"><?php echo $hesklang['db_pass']; ?>: [<a href="Javascript:void(0)" onclick="Javascript:hesk_window('../help_files/settings.html#35','400','500')"><b>?</b></a>]</td>
	<td><input type="password" name="s_db_pass" size="30" maxlength="255" value="<?php echo $hesk_settings['db_pass']; ?>" /></td>
	</tr>
	<tr>
	<td style="text-align:right" width="200"><?php echo $hesklang['prefix']; ?>: [<a href="Javascript:void(0)" onclick="Javascript:hesk_window('../help_files/settings.html#36','400','500')"><b>?</b></a>]</td>
	<td><input type="text" name="s_db_pfix" size="30" maxlength="255" value="<?php echo $hesk_settings['db_pfix']; ?>" /></td>
	</tr>
	</table>

	</td>
	<td class="roundcornersright">&nbsp;</td>
	</tr>
	<tr>
	<td><img src="../img/roundcornerslb.jpg" width="7" height="7" alt="" /></td>
	<td class="roundcornersbottom"></td>
	<td width="7" height="7"><img src="../img/roundcornersrb.jpg" width="7" height="7" alt="" /></td>
	</tr>
</table>

<br />

<!-- Attachments -->
<span class="section">&raquo; <?php echo $hesklang['attachments']; ?></span>

<table width="100%" border="0" cellspacing="0" cellpadding="0">
	<tr>
		<td width="7" height="7"><img src="../img/roundcornerslt.jpg" width="7" height="7" alt="" /></td>
		<td class="roundcornerstop"></td>
		<td><img src="../img/roundcornersrt.jpg" width="7" height="7" alt="" /></td>
	</tr>
	<tr>
	<td class="roundcornersleft">&nbsp;</td>
	<td>

	<table border="0">
	<tr>
	<td style="text-align:right" width="200"><?php echo $hesklang['attach_use']; $onload_status=''; ?>: [<a href="Javascript:void(0)" onclick="Javascript:hesk_window('../help_files/settings.html#37','400','500')"><b>?</b></a>]</td>
	<td>
	<?php
	if ($enable_use_attachments)
	{
	?>
	    <label><input type="radio" name="s_attach_use" value="0" onclick="hesk_attach_disable(new Array('a1','a2','a3'))" <?php if(!$hesk_settings['attachments']['use']) {echo ' checked="checked" '; $onload_status=' disabled="disabled" ';} ?> />
	    <?php echo $hesklang['no']; ?></label> |
	    <label><input type="radio" name="s_attach_use" value="1" onclick="hesk_attach_enable(new Array('a1','a2','a3'))" <?php if($hesk_settings['attachments']['use']) {echo ' checked="checked" ';} ?> />
	    <?php echo $hesklang['yes'].'</label>';
	}
	else
	{
	    $onload_status=' disabled="disabled" ';
	    echo '<input type="hidden" name="s_attach_use" value="0" /><font class="notice">'.$hesklang['e_attach'].'</font>';
	}
	?>
	</td>
	</tr>
	<tr>
	<td style="text-align:right" width="200"><?php echo $hesklang['attach_num']; ?>: [<a href="Javascript:void(0)" onclick="Javascript:hesk_window('../help_files/settings.html#38','400','500')"><b>?</b></a>]</td>
	<td><input type="text" name="s_max_number" size="5" maxlength="2" id="a1" value="<?php echo $hesk_settings['attachments']['max_number']; ?>" <?php echo $onload_status; ?> /></td>
	</tr>
	<tr>
	<td style="text-align:right" width="200"><?php echo $hesklang['attach_size']; ?>: [<a href="Javascript:void(0)" onclick="Javascript:hesk_window('../help_files/settings.html#39','400','500')"><b>?</b></a>]</td>
	<td><input type="text" name="s_max_size" size="5" maxlength="6" id="a2" value="<?php echo $hesk_settings['attachments']['max_size']; ?>" <?php echo $onload_status; ?> /></td>
	</tr>
	<tr>
	<td style="text-align:right" width="200"><?php echo $hesklang['attach_type']; ?>: [<a href="Javascript:void(0)" onclick="Javascript:hesk_window('../help_files/settings.html#40','400','500')"><b>?</b></a>]</td>
	<td><input type="text" name="s_allowed_types" size="40" maxlength="255" id="a3" value="<?php echo implode(',',$hesk_settings['attachments']['allowed_types']); ?>" <?php echo $onload_status; ?> /></td>
	</tr>
	</table>

	</td>
	<td class="roundcornersright">&nbsp;</td>
	</tr>
	<tr>
	<td><img src="../img/roundcornerslb.jpg" width="7" height="7" alt="" /></td>
	<td class="roundcornersbottom"></td>
	<td width="7" height="7"><img src="../img/roundcornersrb.jpg" width="7" height="7" alt="" /></td>
	</tr>
</table>

<br />

<!-- Custom fields -->
<span class="section">&raquo; <?php echo $hesklang['custom_use']; ?></span> [<a href="Javascript:void(0)" onclick="Javascript:hesk_window('../help_files/settings.html#41','400','500')"><b>?</b></a>]

	<table border="0" cellspacing="1" cellpadding="3" width="100%" class="white">
	<tr>
	<th><b><i><?php echo $hesklang['enable']; ?></i></b></th>
	<th><b><i><?php echo $hesklang['s_type']; ?></i></b></th>
	<th><b><i><?php echo $hesklang['custom_r']; ?></i></b></th>
	<th><b><i><?php echo $hesklang['custom_n']; ?></i></b></th>
	<th><b><i><?php echo $hesklang['custom_place']; ?></i></b></th>
	<th><b><i><?php echo $hesklang['opt']; ?></i></b></th>
	</tr>

	<?php
	for ($i=1;$i<=20;$i++)
	{
	    //$this_field='custom' . $i;
	    $this_field = $hesk_settings['custom_fields']['custom'.$i];

		if ($this_field['use'])
	    {
	        $onload_locally='';
	    }
	    else
	    {
	        $onload_locally=' disabled="disabled" ';
	    }

	    if ($i % 2)
	    {
	        $color=' class="admin_white" ';
	    }
	    else
	    {
	        $color=' class="admin_gray"';
	    }

		echo '
		<tr>
		<td'.$color.'><label><input type="checkbox" name="s_custom'.$i.'_use" value="1" id="c'.$i.'1" '; if ($this_field['use']) {echo 'checked="checked"';} echo ' onclick="hesk_attach_toggle(\'c'.$i.'1\',new Array(\'s_custom'.$i.'_type\',\'s_custom'.$i.'_req\',\'s_custom'.$i.'_name\',\'c'.$i.'5\',\'c'.$i.'6\'))" /> '.$hesklang['yes'].'</label></td>
	    <td'.$color.'>
	    	<select name="s_custom'.$i.'_type" id="s_custom'.$i.'_type" '.$onload_locally.'>
	        	<option value="text"     '.($this_field['type'] == 'text' ? 'selected="selected"' : '').    '>'.$hesklang['stf'].'</option>
	        	<option value="textarea" '.($this_field['type'] == 'textarea' ? 'selected="selected"' : '').'>'.$hesklang['stb'].'</option>
	        	<option value="radio"    '.($this_field['type'] == 'radio' ? 'selected="selected"' : '').   '>'.$hesklang['srb'].'</option>
	        	<option value="select"   '.($this_field['type'] == 'select' ? 'selected="selected"' : '').  '>'.$hesklang['ssb'].'</option>
	        	<option value="checkbox" '.($this_field['type'] == 'checkbox' ? 'selected="selected"' : '').'>'.$hesklang['scb'].'</option>
	        </select>
	    </td>
	    <td'.$color.'><label><input type="checkbox" name="s_custom'.$i.'_req" value="1" id="s_custom'.$i.'_req" '; if ($this_field['req']) {echo 'checked="checked"';} echo $onload_locally.' /> '.$hesklang['yes'].'</label></td>
	    <td'.$color.'><input type="text" name="s_custom'.$i.'_name" size="20" maxlength="255" id="s_custom'.$i.'_name" value="'.$this_field['name'].'"'.$onload_locally.' /></td>
	    <td'.$color.'>
	    	<label><input type="radio" name="s_custom'.$i.'_place" value="0" id="c'.$i.'5" '.($this_field['place'] ? '' : 'checked="checked"').'  '.$onload_locally.' /> '.$hesklang['place_before'].'</label><br />
			<label><input type="radio" name="s_custom'.$i.'_place" value="1" id="c'.$i.'6" '.($this_field['place'] ? 'checked="checked"' : '').'  '.$onload_locally.' /> '.$hesklang['place_after'].'</label>
		</td>
	    <td'.$color.'><input type="hidden" name="s_custom'.$i.'_val" id="s_custom'.$i.'_val" value="'.$this_field['value'].'" />
	    <input type="hidden" name="s_custom'.$i.'_maxlen" id="s_custom'.$i.'_maxlen" value="'.$this_field['maxlen'].'" />
	    <a href="Javascript:void(0)" onclick="Javascript:return hesk_customOptions(\'custom'.$i.'\',\'s_custom'.$i.'_val\',\'s_custom'.$i.'_type\',\'s_custom'.$i.'_maxlen\',\''.$this_field['type'].'\')">'.$hesklang['opt'].'</a></td>

		</tr>
		';
	} // End FOR
	?>
	</table>



<p align="center">
<input type="hidden" name="token" value="<?php hesk_token_echo(); ?>" />
<?php
if ($enable_save_settings)
{
    echo '<input type="submit" value="'.$hesklang['save_changes'].'" class="orangebutton" onmouseover="hesk_btn(this,\'orangebuttonover\');" onmouseout="hesk_btn(this,\'orangebutton\');" />';
}
else
{
    echo '<input type="submit" value="'.$hesklang['save_changes'].' ('.$hesklang['disabled'].')" class="orangebutton" onmouseover="hesk_btn(this,\'orangebuttonover\');" onmouseout="hesk_btn(this,\'orangebutton\');" disabled="disabled" /><br /><font class="error">'.$hesklang['e_save_settings'].'</font>';
}
?></p>

</form>

<p>&nbsp;</p>

<?php
require_once(HESK_PATH . 'inc/footer.inc.php');
exit();


function hesk_testLanguage($return_options = 0) {
	global $hesk_settings, $hesklang;

	$dir = HESK_PATH . 'language/';
	$path = opendir($dir);
    $valid_emails = array('category_moved','forgot_ticket_id','new_reply_by_customer','new_reply_by_staff','new_ticket','new_ticket_staff','ticket_assigned_to_you','new_pm');

    $text = '';
    $html = '';

	$text .= "/language\n";

    /* Test all folders inside the language folder */
	while (false !== ($subdir = readdir($path)))
	{
		if ($subdir == "." || $subdir == "..")
	    {
	    	continue;
	    }

		if (filetype($dir . $subdir) == 'dir')
		{
        	$add   = 1;
	    	$langu = $dir . $subdir . '/text.php';
	        $email = $dir . $subdir . '/emails';

			/* Check the text.php */
			$text .= "   |-> /$subdir\n";
	        $text .= "        |-> text.php: ";
	        if (file_exists($langu))
	        {
	        	$tmp = file_get_contents($langu);
	            $err = '';
	        	if (!preg_match('/\$hesklang\[\'LANGUAGE\'\]\=\'(.*)\'\;/',$tmp,$l))
	            {
	                $err .= "              |---->  MISSING: \$hesklang['LANGUAGE']\n";
	            }

	            if (strpos($tmp,'$hesklang[\'ENCODING\']') === false)
	            {
	            	$err .= "              |---->  MISSING: \$hesklang['ENCODING']\n";
	            }

                /* Check if language file is for current version */
	            if (strpos($tmp,'$hesklang[\'auto\']') === false)
	            {
	            	$err .= "              |---->  WRONG VERSION (not ".$hesk_settings['hesk_version'].")\n";
	            }

	            if ($err)
	            {
	            	$text .= "ERROR\n" . $err;
                    $add   = 0;
	            }
	            else
	            {
                	$l[1]  = hesk_input($l[1]);
                    $l[1]  = str_replace('|',' ',$l[1]);
	        		$text .= "OK ($l[1])\n";
	            }
	        }
	        else
	        {
	        	$text .= "ERROR\n";
	            $text .= "              |---->  MISSING: text.php\n";
                $add   = 0;
	        }

            /* Check emails folder */
	        $text .= "        |-> /emails:  ";
	        if (file_exists($email) && filetype($email) == 'dir')
	        {
	        	$err = '';
	            foreach ($valid_emails as $eml)
	            {
	            	if (!file_exists($email.'/'.$eml.'.txt'))
	                {
	                	$err .= "              |---->  MISSING: $eml.txt\n";
	                }
	            }

	            if ($err)
	            {
	            	$text .= "ERROR\n" . $err;
                    $add   = 0;
	            }
	            else
	            {
	        		$text .= "OK\n";
	            }
	        }
	        else
	        {
	        	$text .= "ERROR\n";
	            $text .= "              |---->  MISSING: /emails folder\n";
                $add   = 0;
	        }

	        $text .= "\n";

            /* Add an option for the <select> if needed */
            if ($add)
            {
				if ($l[1] == $hesk_settings['language'])
				{
					$html .= '<option value="'.$subdir.'|'.$l[1].'" selected="selected">'.$l[1].'</option>';
				}
				else
				{
					$html .= '<option value="'.$subdir.'|'.$l[1].'">'.$l[1].'</option>';
				}
            }
		}
	}

	closedir($path);

    /* Output select options or the test log for debugging */
    if ($return_options)
    {
		return $html;
    }
    else
    {
		?>
		<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML; 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
		<html xmlns="http://www.w3.org/1999/xhtml" lang="en">
		<head>
		<title><?php echo $hesklang['s_inl']; ?></title>
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
		.title
		{
		        color : black;
		        font-family : Verdana, Geneva, Arial, Helvetica, sans-serif;
		        font-weight: bold;
		        font-size: 1.0em;
		}
		.wrong   {color : red;}
		.correct {color : green;}
        pre {font-size:1.2em;}
		</style>
		</head>
		<body>

		<h3><?php echo $hesklang['s_inl']; ?></h3>

		<p><i><?php echo $hesklang['s_inle']; ?></i></p>

		<pre><?php echo $text; ?></pre>

		<p>&nbsp;</p>

		<p align="center"><a href="admin_settings.php?test_languages=1&amp;<?php echo rand(10000,99999); ?>"><?php echo $hesklang['ta']; ?></a> | <a href="#" onclick="Javascript:window.close()"><?php echo $hesklang['cwin']; ?></a></p>

		<p>&nbsp;</p>

		</body>

		</html>
		<?php
		exit();
    }
} // END hesk_testLanguage()
?>
