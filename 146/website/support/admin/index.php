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

/* What should we do? */
$_REQUEST['a'] = isset($_REQUEST['a']) ? $_REQUEST['a'] : '';
switch ($_REQUEST['a'])
{
    case 'do_login':
    	do_login();
        break;
    case 'login':
    	print_login();
        break;
    case 'logout':
		hesk_token_check($_GET['token']);    
    	logout();
        break;
    default:
    	hesk_autoLogin();
    	print_login();
}

/* Print footer */
require_once(HESK_PATH . 'inc/footer.inc.php');
exit();

/*** START FUNCTIONS ***/
function do_login() {
	global $hesk_settings, $hesklang;

    $user = hesk_input($_POST['user']);
    if (empty($user))
    {
		$myerror = $hesk_settings['list_users'] ? $hesklang['select_username'] : $hesklang['enter_username'];
        hesk_process_messages($myerror,'NOREDIRECT');
        print_login();
        exit();
    }
    define('HESK_USER', $user);

	$pass = hesk_input($_POST['pass']);
	if (empty($pass))
	{
    	hesk_process_messages($hesklang['enter_pass'],'NOREDIRECT');
        print_login();
        exit();
	}

	$sql = 'SELECT * FROM `'.hesk_dbEscape($hesk_settings['db_pfix']).'users` WHERE `user` = \''.hesk_dbEscape($user).'\' LIMIT 1';
	$result = hesk_dbQuery($sql);
	if (hesk_dbNumRows($result) != 1)
	{
    	hesk_process_messages($hesklang['wrong_user'],'NOREDIRECT');
        print_login();
        exit();
	}

	$res=hesk_dbFetchAssoc($result);
	foreach ($res as $k=>$v)
	{
	    $_SESSION[$k]=$v;
	}

	/* Check password */
	if (hesk_Pass2Hash($pass) != $_SESSION['pass'])
    {
	    hesk_session_stop();
        hesk_process_messages($hesklang['wrong_pass'],'NOREDIRECT');
        print_login();
        exit();
	}
    $pass_enc = hesk_Pass2Hash($_SESSION['pass'].strtolower($user).$_SESSION['pass']);
	unset($_SESSION['pass']);

	/* Regenerate session ID (security) */
	hesk_session_regenerate_id();

	/* Get allowed categories */
	if (empty($_SESSION['isadmin']))
	{
	    //$cat=substr($_SESSION['categories'], 0, -1);
	    $_SESSION['categories']=explode(',',$_SESSION['categories']);
	}

	session_write_close();

	/* Remember username? */
	if ($hesk_settings['autologin'] && $_POST['remember_user']=='AUTOLOGIN')
	{
		setcookie('hesk_username', "$user", strtotime('+1 year'));
		setcookie('hesk_p', "$pass_enc", strtotime('+1 year'));
	}
	elseif ($_POST['remember_user']=='JUSTUSER')
	{
		setcookie('hesk_username', "$user", strtotime('+1 year'));
		setcookie('hesk_p', '');
	}
	else
	{
		// Expire cookie if set otherwise
		setcookie('hesk_username', '');
		setcookie('hesk_p', '');
	}

    /* Close any old tickets here so Cron jobs aren't necessary */
	if ($hesk_settings['autoclose'])
    {
    	$revision = sprintf($hesklang['thist3'],hesk_date(),$hesklang['auto']);
    	$dt  = date('Y-m-d H:i:s',time() - $hesk_settings['autoclose']*86400);
		$sql = 'UPDATE `'.hesk_dbEscape($hesk_settings['db_pfix']).'tickets` SET `status`=\'3\', `history`=CONCAT(`history`,\''.hesk_dbEscape($revision).'\')  WHERE `status` = \'2\' AND `lastchange` <= \''.hesk_dbEscape($dt).'\'';
		hesk_dbQuery($sql);
    }

	/* Redirect to the destination page */
	if (isset($_REQUEST['goto']))
	{
    	$url = hesk_input($_REQUEST['goto']);
	    $url = str_replace('&amp;','&',$url);

        /* goto parameter can be set to the local domain only */
        $myurl = parse_url($hesk_settings['hesk_url']);
        $goto  = parse_url($url);

        if (isset($myurl['host']) && isset($goto['host']))
        {
        	if ( str_replace('www.','',strtolower($myurl['host'])) != str_replace('www.','',strtolower($goto['host'])) )
            {
            	$url = 'admin_main.php';
            }
        }

	    header('Location: '.$url);
	}
	else
	{
	    header('Location: admin_main.php');
	}
	exit();
} // End do_login()


function print_login() {
	global $hesk_settings, $hesklang;
    $hesk_settings['tmp_title'] = $hesk_settings['hesk_title'] . ' - ' .$hesklang['admin_login'];
	require_once(HESK_PATH . 'inc/header.inc.php');

	if (isset($_REQUEST['notice']))
	{
    	hesk_process_messages($hesklang['session_expired'],'NOREDIRECT');
	}

	?>
	<table width="100%" border="0" cellspacing="0" cellpadding="0">
	<tr>
	<td width="3"><img src="../img/headerleftsm.jpg" width="3" height="25" alt="" /></td>
	<td class="headersm"><?php echo $hesklang['login']; ?></td>
	<td width="3"><img src="../img/headerrightsm.jpg" width="3" height="25" alt="" /></td>
	</tr>
	</table>

	<table width="100%" border="0" cellspacing="0" cellpadding="3">
	<tr>
	<td><span class="smaller"><a href="<?php echo $hesk_settings['site_url']; ?>" class="smaller"><?php echo $hesk_settings['site_title']; ?></a> &gt;
	<?php echo $hesklang['admin_login']; ?></span></td>
	</tr>
	</table>

	</td>
	</tr>
	<tr>
	<td>

	<br />

	<?php
	/* This will handle error, success and notice messages */
	hesk_handle_messages();
	?>

    <br />

    <div align="center">
	<table border="0" cellspacing="0" cellpadding="0">
	<tr>
		<td width="7" height="7"><img src="../img/roundcornerslt.jpg" width="7" height="7" alt="" /></td>
		<td class="roundcornerstop"></td>
		<td><img src="../img/roundcornersrt.jpg" width="7" height="7" alt="" /></td>
	</tr>
	<tr>
		<td class="roundcornersleft">&nbsp;</td>
		<td>

		<h3 align="center"><?php echo $hesklang['login']; ?></h3>

		<form action="index.php" method="post">

		<table border="0" cellspacing="1" cellpadding="5">
		<tr>
		<td style="text-align:right"><?php echo $hesklang['user']; ?>: </td>
		<td>
		<?php
		if (defined('HESK_USER'))
		{
			$savedUser = HESK_USER;
		}
		else
		{
			$savedUser = isset($_COOKIE['hesk_username']) ? htmlspecialchars($_COOKIE['hesk_username']) : '';
		}

        $is_1 = '';
        $is_2 = '';
        $is_3 = '';

		if ($hesk_settings['autologin'] && (isset($_COOKIE['hesk_p']) || (isset($_POST['remember_user']) && $_POST['remember_user'] == 'AUTOLOGIN') ) )
        {
        	$is_1 = 'checked="checked"';
        }
        elseif (isset($_COOKIE['hesk_username']) || (isset($_POST['remember_user']) && $_POST['remember_user'] == 'JUSTUSER') )
        {
        	$is_2 = 'checked="checked"';
        }
        else
        {
        	$is_3 = 'checked="checked"';
        }

		if ($hesk_settings['list_users'])
		{
		    echo '<select name="user">';
		    $sql    = 'SELECT * FROM `'.hesk_dbEscape($hesk_settings['db_pfix']).'users` ORDER BY `id` ASC';
		    $result = hesk_dbQuery($sql);
		    while ($row=hesk_dbFetchAssoc($result))
		    {
		        $sel = (strtolower($savedUser) == strtolower($row['user'])) ? 'selected="selected"' : '';
		        echo '<option value="'.$row['user'].'" '.$sel.'>'.$row['user'].'</option>';
		    }
		    echo '</select>';

		}
		else
		{
		    echo '<input type="text" name="user" size="30" value="'.$savedUser.'" />';
		}
		?>
		</td>
		</tr>
		<tr>
		<td style="text-align:right"><?php echo $hesklang['pass']; ?>: </td>
		<td><input type="password" name="pass" size="30" /></td>
		</tr>
		</table>

        <p>&nbsp;</p>

		<?php
		if ($hesk_settings['autologin'])
		{
		?>
        <div align="center">
        <table border="0">
	        <tr>
	        <td style="text-align:left">
				<label><input type="radio" name="remember_user" value="AUTOLOGIN" <?php echo $is_1; ?> /> <?php echo $hesklang['autologin']; ?></label><br />
                <label><input type="radio" name="remember_user" value="JUSTUSER" <?php echo $is_2; ?> /> <?php echo $hesklang['just_user']; ?></label><br />
                <label><input type="radio" name="remember_user" value="NOTHANKS" <?php echo $is_3; ?> /> <?php echo $hesklang['nothx']; ?></label>
	        </td>
	        </tr>
        </table>
        </div>
		<?php
		}
		else
		{
		?>
			<p style="text-align:center"><label><input type="checkbox" name="remember_user" value="JUSTUSER" <?php echo $is_2; ?> /> <?php echo $hesklang['remember_user']; ?></label></p>
		<?php
		}
		?>

		<p style="text-align:center"><input type="hidden" name="a" value="do_login" />
		<?php
		if (isset($_REQUEST['goto']) && $url=hesk_input($_REQUEST['goto']))
		{
		    echo '<input type="hidden" name="goto" value="'.$url.'" />';
		}
		?>
		<input type="submit" value="<?php echo $hesklang['login']; ?>" class="orangebutton" onmouseover="hesk_btn(this,'orangebuttonover');" onmouseout="hesk_btn(this,'orangebutton');" /></p>

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
    </div>

    <p>&nbsp;</p>

	<?php
    require_once(HESK_PATH . 'inc/footer.inc.php');
    exit();
} // End print_login()

function logout() {
	global $hesk_settings, $hesklang;
	hesk_session_stop();
    hesk_process_messages($hesklang['logout_success'],'NOREDIRECT','SUCCESS');
    setcookie('hesk_p', '');
	print_login();
	exit();
} // End logout()

?>
