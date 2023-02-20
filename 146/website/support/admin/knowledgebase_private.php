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

/* Is Knowledgebase enabled? */
if (!$hesk_settings['kb_enable'])
{
	hesk_error($hesklang['kbdis']);
}

/* Any category ID set? */
$catid = isset($_GET['category']) ? intval($_GET['category']) : 1;
$artid = isset($_GET['article']) ? intval($_GET['article']) : 0;

if (isset($_GET['search']))
{
	$query = hesk_input($_GET['search']);
}
else
{
	$query = 0;
}

$hesk_settings['kb_link'] = ($artid || $catid != 1 || $query) ? '<a href="knowledgebase_private.php" class="smaller">'.$hesklang['kb_text'].'</a>' : $hesklang['kb_text'];

if ($hesk_settings['kb_search'] && $query)
{
    hesk_kb_search($query);
}
elseif ($artid)
{
	$sql = 'SELECT * FROM `'.hesk_dbEscape($hesk_settings['db_pfix']).'kb_articles` WHERE `id`=\''.hesk_dbEscape($artid).'\' AND `type`!=\'2\' LIMIT 1';
	$result  = hesk_dbQuery($sql);
    $article = hesk_dbFetchAssoc($result) or hesk_error($hesklang['kb_art_id']);
    hesk_show_kb_article($artid);
}
else
{
	hesk_show_kb_category($catid);
}

require_once(HESK_PATH . 'inc/footer.inc.php');
exit();


/*** START FUNCTIONS ***/

function hesk_kb_header($kb_link) {
	global $hesk_settings, $hesklang;

	/* Print admin navigation */
	require_once(HESK_PATH . 'inc/show_admin_nav.inc.php');
	?>

	</td>
	</tr>
	<tr>
	<td>

	<table width="100%" border="0" cellspacing="0" cellpadding="3">
	<tr>
	<td valign="top">
	<span class="smaller"><a href="<?php echo $hesk_settings['hesk_url']; ?>/admin/admin_main.php" class="smaller"><?php echo $hesklang['main_page']; ?></a>
	&gt; <?php echo $kb_link; ?></span>
	</td>
	<?php
	if ($hesk_settings['kb_search'])
	{
		echo '
		<td style="text-align:right" valign="top" width="300">
	    <div style="display:inline;">
	        <form action="knowledgebase_private.php" method="get" style="display: inline; margin: 0;">
			<input type="text" name="search" size="20" />
			<input type="submit" value="'.$hesklang['search'].'" class="greenbutton" onmouseover="hesk_btn(this,\'greenbuttonover\');" onmouseout="hesk_btn(this,\'greenbutton\');" />
			</form>
		</div>
		</td>
	    ';
	}
	?>
	</tr>
	</table>

	</td>
	</tr>
	<tr>
	<td><?php
} // END hesk_kb_header()


function hesk_kb_search($query) {
	global $hesk_settings, $hesklang;

    define('HESK_NO_ROBOTS',1);

	/* Print header */
	require_once(HESK_PATH . 'inc/header.inc.php');
	hesk_kb_header($hesk_settings['kb_link']);

	$sql = 'SELECT t1.* FROM `'.hesk_dbEscape($hesk_settings['db_pfix']).'kb_articles` AS t1 LEFT JOIN `'.hesk_dbEscape($hesk_settings['db_pfix']).'kb_categories` AS t2 ON t1.`catid` = t2.`id`  WHERE t1.`type`!=\'2\' AND t2.`type`!=\'2\' AND MATCH(`subject`,`content`) AGAINST (\''.hesk_dbEscape($query).'\') LIMIT '.hesk_dbEscape($hesk_settings['kb_search_limit']);
	$res = hesk_dbQuery($sql);
    $num = hesk_dbNumRows($res);

    ?>
	<p>&raquo; <b><?php echo $hesklang['sr']; ?> (<?php echo $num; ?>)</b></p>

	<?php
	if ($num == 0)
	{
		echo '<p><i>'.$hesklang['nosr'].'</i></p>';
        hesk_show_kb_category(1,1);
	}
    else
    {
?>
<table width="100%" border="0" cellspacing="0" cellpadding="0">
<tr>
	<td width="7" height="7"><img src="../img/roundcornerslt.jpg" width="7" height="7" alt="" /></td>
	<td class="roundcornerstop"></td>
	<td><img src="../img/roundcornersrt.jpg" width="7" height="7" alt="" /></td>
</tr>
<tr>
	<td class="roundcornersleft">&nbsp;</td>
	<td>
		<div align="center">
        <table border="0" cellspacing="1" cellpadding="3" width="100%">
        <?php
			while ($article = hesk_dbFetchAssoc($res))
			{
	            $txt = strip_tags($article['content']);
	            if (strlen($txt) > $hesk_settings['kb_substrart'])
	            {
	            	$txt = substr(strip_tags($article['content']),0,$hesk_settings['kb_substrart']).'...';
	            }

	            if ($hesk_settings['kb_rating'])
	            {
	            	$alt = $article['rating'] ? sprintf($hesklang['kb_rated'], sprintf("%01.1f", $article['rating'])) : $hesklang['kb_not_rated'];
	                $rat = '<td width="1" valign="top"><img src="../img/star_'.(hesk_round_to_half($article['rating'])*10).'.png" width="85" height="16" alt="'.$alt.'" border="0" style="vertical-align:text-bottom" /></td>';
	            }
	            else
	            {
	            	$rat = '';
	            }

				echo '
				<tr>
				<td>
	                <table border="0" width="100%" cellspacing="0" cellpadding="1">
	                <tr>
	                <td width="1" valign="top"><img src="../img/article_text.png" width="16" height="16" border="0" alt="" style="vertical-align:middle" /></td>
	                <td valign="top"><a href="knowledgebase_private.php?article='.$article['id'].'">'.$article['subject'].'</a></td>
	                '.$rat.'
                    </tr>
	                </table>
	                <table border="0" width="100%" cellspacing="0" cellpadding="1">
	                <tr>
	                <td width="1" valign="top"><img src="../img/blank.gif" width="16" height="10" style="vertical-align:middle" alt="" /></td>
	                <td><span class="article_list">'.$txt.'</span></td>
                    </tr>
	                </table>

	            </td>
				</tr>';
			}
	?>
    	</table>
        </div>
	</td>
	<td class="roundcornersright">&nbsp;</td>
</tr>
<tr>
	<td><img src="../img/roundcornerslb.jpg" width="7" height="7" alt="" /></td>
	<td class="roundcornersbottom"></td>
	<td width="7" height="7"><img src="../img/roundcornersrb.jpg" width="7" height="7" alt="" /></td>
</tr>
</table>

    <p>&nbsp;<br />&lt;&lt; <a href="javascript:history.go(-1)"><?php echo $hesklang['back']; ?></a></p>
    <?php
    } // END else

} // END hesk_kb_search()


function hesk_show_kb_article($artid) {
	global $hesk_settings, $hesklang, $article;

	/* Print header */
    $hesk_settings['tmp_title'] = $article['subject'];
	require_once(HESK_PATH . 'inc/header.inc.php');
	hesk_kb_header($hesk_settings['kb_link']);

	$sql = 'SELECT `name`,`type` FROM `'.hesk_dbEscape($hesk_settings['db_pfix']).'kb_categories` WHERE `id`=\''.hesk_dbEscape($article['catid']).'\' LIMIT 1';
	$result   = hesk_dbQuery($sql);
    $category = hesk_dbFetchAssoc($result) or hesk_error($hesklang['kb_cat_inv']);

    echo '<h3>'.$article['subject'].'</h3>

    <fieldset>
	<legend>'.$hesklang['as'].'</legend>
    '. $article['content'];

    if (!empty($article['attachments']))
    {
		echo '<p><b>'.$hesklang['attachments'].':</b><br />';
		$att=explode(',',substr($article['attachments'], 0, -1));
		foreach ($att as $myatt)
        {
			list($att_id, $att_name) = explode('#', $myatt);
			echo '<img src="../img/clip.png" width="16" height="16" alt="'.$att_name.'" style="align:text-bottom" /> <a href="../download_attachment.php?kb_att='.$att_id.'" rel="nofollow">'.$att_name.'</a><br />';
		}
		echo '</p>';
    }

    echo '</fieldset>';

    if ($article['catid']==1)
    {
    	$link = 'knowledgebase_private.php';
    }
    else
    {
    	$link = 'knowledgebase_private.php?category='.$article['catid'];
    }
    ?>

    <fieldset>
    <legend><?php echo $hesklang['ad']; ?></legend>
	<table border="0">
    <tr>
    <td><?php echo $hesklang['aid']; ?>: </td>
    <td><?php echo $article['id']; ?></td>
    </tr>
    <tr>
    <td><?php echo $hesklang['category']; ?>: </td>
    <td><a href="<?php echo $link; ?>"><?php echo $category['name']; ?></a></td>
    </tr>
    <tr>
    <td><?php echo $hesklang['dta']; ?>: </td>
    <td><?php echo hesk_date($article['dt']); ?></td>
    </tr>
    <tr>
    <td><?php echo $hesklang['views']; ?>: </td>
    <td><?php echo (isset($_GET['rated']) ? $article['views'] : $article['views']+1); ?></td>
    </tr>
    </table>
    </fieldset>

    <p>&nbsp;<br />&lt;&lt; <a href="javascript:history.go(<?php echo isset($_GET['rated']) ? '-2' : '-1'; ?>)"><?php echo $hesklang['back']; ?></a></p>

    <?php
} // END hesk_show_kb_article()


function hesk_show_kb_category($catid, $is_search = 0) {
	global $hesk_settings, $hesklang;

    if ($is_search == 0)
    {
		/* Print header */
		require_once(HESK_PATH . 'inc/header.inc.php');
		hesk_kb_header($hesk_settings['kb_link']);

		if ($catid == 1)
	    {
	    	echo $hesklang['kb_is'].' &nbsp;<br /><br />' . $hesklang['priv'];
	    }
    }

	$sql = 'SELECT * FROM `'.hesk_dbEscape($hesk_settings['db_pfix']).'kb_categories` WHERE `id`=\''.hesk_dbEscape($catid).'\' LIMIT 1';
	$res = hesk_dbQuery($sql);
    $thiscat = hesk_dbFetchAssoc($res) or hesk_error($hesklang['kb_cat_inv']);

	if ($thiscat['parent'])
	{
		$link = ($thiscat['parent'] == 1) ? 'knowledgebase_private.php' : 'knowledgebase_private.php?category='.$thiscat['parent'];
		echo '<span class="homepageh3">&raquo; '.$hesklang['kb_cat'].': '.$thiscat['name'].'</span>
        &nbsp;(<a href="javascript:history.go(-1)">'.$hesklang['back'].'</a>)
		';
	}

	$sql = 'SELECT * FROM `'.hesk_dbEscape($hesk_settings['db_pfix']).'kb_categories` WHERE `parent`=\''.hesk_dbEscape($catid).'\' AND `type`!=\'2\' ORDER BY `parent` ASC, `cat_order` ASC';
	$result = hesk_dbQuery($sql);
	if (hesk_dbNumRows($result) > 0)
	{
        ?>

		<p>&raquo; <b><?php echo $hesklang['kb_cat_sub']; ?>:</b></p>

<table width="100%" border="0" cellspacing="0" cellpadding="0">
<tr>
	<td width="7" height="7"><img src="../img/roundcornerslt.jpg" width="7" height="7" alt="" /></td>
	<td class="roundcornerstop"></td>
	<td><img src="../img/roundcornersrt.jpg" width="7" height="7" alt="" /></td>
</tr>
<tr>
	<td class="roundcornersleft">&nbsp;</td>
	<td>

		<table border="0" cellspacing="1" cellpadding="3" width="100%">

		<?php
		$per_col = $hesk_settings['kb_cols'];
		$i = 1;

		while ($cat = hesk_dbFetchAssoc($result))
		{

			if ($i == 1)
		    {
				echo '<tr>';
		    }

            $private = ($cat['type'] == 1) ? ' *' : '';

			echo '
		    <td width="50%" valign="top">
			<table border="0">
			<tr><td><img src="../img/folder.gif" width="20" height="20" alt="" style="vertical-align:middle" /><a href="knowledgebase_private.php?category='.$cat['id'].'">'.$cat['name'].'</a>'.$private.'</td></tr>
			';

			/* Print two most popular articles */
			if ($hesk_settings['kb_numshow'] && $cat['articles'])
		    {
		    	$sql = 'SELECT `id`,`subject`,`type` FROM `'.hesk_dbEscape($hesk_settings['db_pfix']).'kb_articles` WHERE `catid`=\''.hesk_dbEscape($cat['id']).'\' AND `type`!=\'2\' ORDER BY `views` DESC, `art_order` ASC LIMIT '.hesk_dbEscape(($hesk_settings['kb_numshow']+1));
		        $res = hesk_dbQuery($sql);
		        $num = 1;
				while ($art = hesk_dbFetchAssoc($res))
				{
                	$private = ($art['type'] == 1) ? ' *' : '';
					echo '
		            <tr>
		            <td><img src="../img/article_text.png" width="16" height="16" border="0" alt="" style="vertical-align:middle" />
		            <a href="knowledgebase_private.php?article='.$art['id'].'" class="article">'.$art['subject'].'</a>'.$private.'</td>
		            </tr>';

		            if ($num == $hesk_settings['kb_numshow'])
		            {
		            	break;
		            }
		            else
		            {
		            	$num++;
		            }
				}
		        if (hesk_dbNumRows($res) > $hesk_settings['kb_numshow'])
		        {
		        	echo '<tr><td>&raquo; <a href="knowledgebase_private.php?category='.$cat['id'].'"><i>'.$hesklang['m'].'</i></a></td></tr>';
		        }
		    }

			echo '
			</table>
		    </td>
			';

			if ($i == $per_col)
		    {
				echo '</tr>';
		        $i = 0;
		    }
			$i++;
		}
		/* Finish the table if needed */
		if ($i != 1)
		{
			for ($j=1;$j<=$per_col;$j++)
		    {
				echo '<td width="50%">&nbsp;</td>';
				if ($i == $per_col)
			    {
					echo '</tr>';
			        break;
			    }
		        $i++;
		    }
		}

		?>
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

	<?php
	} // END if NumRows > 0
	?>

	<p>&raquo; <b><?php echo $hesklang['ac']; ?></b></p>

<table width="100%" border="0" cellspacing="0" cellpadding="0">
<tr>
	<td width="7" height="7"><img src="../img/roundcornerslt.jpg" width="7" height="7" alt="" /></td>
	<td class="roundcornerstop"></td>
	<td><img src="../img/roundcornersrt.jpg" width="7" height="7" alt="" /></td>
</tr>
<tr>
	<td class="roundcornersleft">&nbsp;</td>
	<td>

	<?php
	$sql = 'SELECT * FROM `'.hesk_dbEscape($hesk_settings['db_pfix']).'kb_articles` WHERE `catid`=\''.hesk_dbEscape($catid).'\' AND `type`!=\'2\' ORDER BY `art_order` ASC';
	$res = hesk_dbQuery($sql);
	if (hesk_dbNumRows($res) == 0)
	{
		echo '<p><i>'.$hesklang['noac'].'</i></p>';
	}
	else
	{
			echo '<div align="center"><table border="0" cellspacing="1" cellpadding="3" width="100%">';
			while ($article = hesk_dbFetchAssoc($res))
			{
            	$private = ($article['type'] == 1) ? ' *' : '';

	            $txt = strip_tags($article['content']);
	            if (strlen($txt) > $hesk_settings['kb_substrart'])
	            {
	            	$txt = substr(strip_tags($article['content']),0,$hesk_settings['kb_substrart']).'...';
	            }

				echo '
				<tr>
				<td>
	                <table border="0" width="100%" cellspacing="0" cellpadding="1">
	                <tr>
	                <td width="1" valign="top"><img src="../img/article_text.png" width="16" height="16" border="0" alt="" style="vertical-align:middle" /></td>
	                <td valign="top"><a href="knowledgebase_private.php?article='.$article['id'].'">'.$article['subject'].'</a>'.$private.'</td>
                    </tr>
	                </table>
	                <table border="0" width="100%" cellspacing="0" cellpadding="1">
	                <tr>
	                <td width="1" valign="top"><img src="../img/blank.gif" width="16" height="10" style="vertical-align:middle" alt="" /></td>
	                <td><span class="article_list">'.$txt.'</span></td>
                    </tr>
	                </table>
	            </td>
				</tr>';
			}
		    echo '</table></div>';
	}
	?>

	</td>
	<td class="roundcornersright">&nbsp;</td>
</tr>
<tr>
	<td><img src="../img/roundcornerslb.jpg" width="7" height="7" alt="" /></td>
	<td class="roundcornersbottom"></td>
	<td width="7" height="7"><img src="../img/roundcornersrb.jpg" width="7" height="7" alt="" /></td>
</tr>
</table>
<?php
} // END hesk_show_kb_category()
?>
