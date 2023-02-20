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

/* Is Knowledgebase enabled? */
if (!$hesk_settings['kb_enable'])
{
	hesk_error($hesklang['kbdis']);
}

/* Connect to database */
hesk_dbConnect();

/* Rating? */
if (isset($_GET['rating']) && !hesk_detect_bots())
{
	$rating = intval($_GET['rating']);

	/* Rating can only be 1 or 5 */
	if ($rating != 1 && $rating != 5)
	{
		hesk_error($hesklang['attempt']);
	}

    $artid = intval($_GET['id']) or hesk_error($hesklang['kb_art_id']);

    $_COOKIE['hesk_kb_rate'] = isset($_COOKIE['hesk_kb_rate']) ? $_COOKIE['hesk_kb_rate'] : '';

    if (strpos($_COOKIE['hesk_kb_rate'],'a'.$artid.'%')===false)
    {
		$sql = 'UPDATE `'.hesk_dbEscape($hesk_settings['db_pfix']).'kb_articles` SET `rating`=((`rating`*`votes`)+'.hesk_dbEscape($rating).')/(`votes`+1), `votes`=`votes`+1 WHERE `id`=\''.hesk_dbEscape($artid).'\' AND `type`=\'0\' LIMIT 1';
		hesk_dbQuery($sql);
    }
    setcookie('hesk_kb_rate', $_COOKIE['hesk_kb_rate'].'a'.$artid.'%', time()+2592000);
    header('Location: knowledgebase.php?article='.$artid.'&rated=1');
    exit();
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

$hesk_settings['kb_link'] = ($artid || $catid != 1 || $query) ? '<a href="knowledgebase.php" class="smaller">'.$hesklang['kb_text'].'</a>' : $hesklang['kb_text'];

if ($hesk_settings['kb_search'] && $query)
{
    hesk_kb_search($query);
}
elseif ($artid)
{
    $sql = '
	SELECT t1 . *
	FROM `'.hesk_dbEscape($hesk_settings['db_pfix']).'kb_articles` AS `t1`
	LEFT JOIN `'.hesk_dbEscape($hesk_settings['db_pfix']).'kb_categories` AS `t2` ON `t1`.`catid` = `t2`.`id`
	WHERE `t1`.`id` = '.hesk_dbEscape($artid).'
	AND `t1`.`type` = \'0\'
	AND `t2`.`type` = \'0\'
	LIMIT 0 , 30';

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
	?>
	<table width="100%" border="0" cellspacing="0" cellpadding="0">
	<tr>
	<td width="3"><img src="img/headerleftsm.jpg" width="3" height="25" alt="" /></td>
	<td class="headersm"><?php hesk_showTopBar($hesklang['kb_text']); ?></td>
	<td width="3"><img src="img/headerrightsm.jpg" width="3" height="25" alt="" /></td>
	</tr>
	</table>

	<table width="100%" border="0" cellspacing="0" cellpadding="3">
	<tr>
	<td valign="top">
	<span class="smaller"><a href="<?php echo $hesk_settings['site_url']; ?>" class="smaller"><?php echo $hesk_settings['site_title']; ?></a> &gt;
	<a href="<?php echo $hesk_settings['hesk_url']; ?>" class="smaller"><?php echo $hesk_settings['hesk_title']; ?></a>
	&gt; <?php echo $kb_link; ?></span>
	</td>
	<?php
	if ($hesk_settings['kb_search']==1)
	{
		echo '
		<td style="text-align:right" valign="top" width="300">
	    <div style="display:inline;">
	        <form action="knowledgebase.php" method="get" style="display: inline; margin: 0;">
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

	if ($hesk_settings['kb_search']==2)
	{
		?>
		<br />
		<div style="text-align:center">
			<form action="knowledgebase.php" method="get" style="display: inline; margin: 0;" name="searchform">
			<span class="largebold"><?php echo $hesklang['ask']; ?></span> <input type="text" name="search" size="60" class="large" />
			<input type="submit" value="<?php echo $hesklang['shlp']; ?>" class="greenbutton" onmouseover="hesk_btn(this,'greenbuttonover');" onmouseout="hesk_btn(this,'greenbutton');" style="font-size:14px;height: 22px;" /><br />
			</form>
		</div>
		<br />

		<!-- START KNOWLEDGEBASE SUGGEST -->
			<div id="kb_suggestions" style="display:none">
			<img src="img/loading.gif" width="24" height="24" alt="" border="0" style="vertical-align:text-bottom" /> <i><?php echo $hesklang['lkbs']; ?></i>
			</div>

			<script language="Javascript" type="text/javascript"><!--
			hesk_suggestKBsearch();
			//-->
			</script>
		<!-- END KNOWLEDGEBASE SUGGEST -->

		<br />
	    <?php
	}

} // END hesk_kb_header()


function hesk_kb_search($query) {
	global $hesk_settings, $hesklang;

    define('HESK_NO_ROBOTS',1);

	/* Print header */
    $hesk_settings['tmp_title'] = $hesklang['sr'] . ': ' . substr(htmlspecialchars(stripslashes($query)),0,20);
	require_once(HESK_PATH . 'inc/header.inc.php');
	hesk_kb_header($hesk_settings['kb_link']);

	$sql = 'SELECT t1.* FROM `'.hesk_dbEscape($hesk_settings['db_pfix']).'kb_articles` AS t1 LEFT JOIN `'.hesk_dbEscape($hesk_settings['db_pfix']).'kb_categories` AS t2 ON t1.`catid` = t2.`id`  WHERE t1.`type`=\'0\' AND t2.`type`=\'0\' AND MATCH(`subject`,`content`) AGAINST (\''.hesk_dbEscape($query).'\') LIMIT '.hesk_dbEscape($hesk_settings['kb_search_limit']);
	$res = hesk_dbQuery($sql);
    $num = hesk_dbNumRows($res);

    ?>
	<p>&raquo; <b><?php echo $hesklang['sr']; ?> (<?php echo $num; ?>)</b></p>

	<?php
	if ($num == 0)
	{
		echo '<p><i>'.$hesklang['nosr'].'</i></p>
        <p>&nbsp;</p>
        ';
        hesk_show_kb_category(1,1);
	}
    else
    {
?>
<table width="100%" border="0" cellspacing="0" cellpadding="0">
<tr>
	<td width="7" height="7"><img src="img/roundcornerslt.jpg" width="7" height="7" alt="" /></td>
	<td class="roundcornerstop"></td>
	<td><img src="img/roundcornersrt.jpg" width="7" height="7" alt="" /></td>
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
	                $rat = '<td width="1" valign="top"><img src="img/star_'.(hesk_round_to_half($article['rating'])*10).'.png" width="85" height="16" alt="'.$alt.'" border="0" style="vertical-align:text-bottom" /></td>';
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
	                <td width="1" valign="top"><img src="img/article_text.png" width="16" height="16" border="0" alt="" style="vertical-align:middle" /></td>
	                <td valign="top"><a href="knowledgebase.php?article='.$article['id'].'">'.$article['subject'].'</a></td>
	                '.$rat.'
                    </tr>
	                </table>
	                <table border="0" width="100%" cellspacing="0" cellpadding="1">
	                <tr>
	                <td width="1" valign="top"><img src="img/blank.gif" width="16" height="10" style="vertical-align:middle" alt="" /></td>
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
	<td><img src="img/roundcornerslb.jpg" width="7" height="7" alt="" /></td>
	<td class="roundcornersbottom"></td>
	<td width="7" height="7"><img src="img/roundcornersrb.jpg" width="7" height="7" alt="" /></td>
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

    /* Private category? */
    if ($category['type'])
    {
    	hesk_error($hesklang['kbpart']);
    }

    if (!isset($_GET['rated']) && !hesk_detect_bots())
    {
	    $sql = 'UPDATE `'.hesk_dbEscape($hesk_settings['db_pfix']).'kb_articles` SET `views`=`views`+1 WHERE `id`=\''.hesk_dbEscape($artid).'\' AND `type`=\'0\' LIMIT 1';
		hesk_dbQuery($sql);
    }

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
			echo '<img src="img/clip.png" width="16" height="16" alt="'.$att_name.'" style="align:text-bottom" /> <a href="download_attachment.php?kb_att='.$att_id.'" rel="nofollow">'.$att_name.'</a><br />';
		}
		echo '</p>';
    }

	if ($hesk_settings['kb_rating'] && (empty($_COOKIE['hesk_kb_rate']) || strpos($_COOKIE['hesk_kb_rate'],'a'.$artid.'%')===false))
	{
		echo '
	    <div id="rating" class="rate" align="right">&nbsp;<br />'.$hesklang['rart'].'
			<a href="Javascript:void(0)" onclick="Javascript:window.location=\'knowledgebase.php?rating=5&amp;id='.$article['id'].'\'" rel="nofollow">'.strtolower($hesklang['yes']).'</a> /
	        <a href="Javascript:void(0)" onclick="Javascript:window.location=\'knowledgebase.php?rating=1&amp;id='.$article['id'].'\'" rel="nofollow">'.strtolower($hesklang['no']).'</a>
	    </div>
        ';
	}
    echo '</fieldset>';

    if ($article['catid']==1)
    {
    	$link = 'knowledgebase.php';
    }
    else
    {
    	$link = 'knowledgebase.php?category='.$article['catid'];
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
    <?php
	if ($hesk_settings['kb_rating'])
	{
		$alt = $article['rating'] ? sprintf($hesklang['kb_rated'], sprintf("%01.1f", $article['rating'])) : $hesklang['kb_not_rated'];
		echo '
        <tr>
        <td>'.$hesklang['rating'].' ('.$hesklang['votes'].'):</td>
        <td><img src="img/star_'.(hesk_round_to_half($article['rating'])*10).'.png" width="85" height="16" alt="'.$alt.'" title="'.$alt.'" border="0" style="vertical-align:text-bottom" /> ('.$article['votes'].')</td>
        </tr>
        ';
	}
	?>
    </table>
    </fieldset>

    <p>&nbsp;<br />&lt;&lt; <a href="javascript:history.go(<?php echo isset($_GET['rated']) ? '-2' : '-1'; ?>)"><?php echo $hesklang['back']; ?></a></p>

    <?php
} // END hesk_show_kb_article()


function hesk_show_kb_category($catid, $is_search = 0) {
	global $hesk_settings, $hesklang;

	$sql = 'SELECT * FROM `'.hesk_dbEscape($hesk_settings['db_pfix']).'kb_categories` WHERE `id`=\''.hesk_dbEscape($catid).'\' AND `type`=\'0\' LIMIT 1';
	$res = hesk_dbQuery($sql);
    $thiscat = hesk_dbFetchAssoc($res) or hesk_error($hesklang['kb_cat_inv']);

    if ($is_search == 0)
    {
		/* Print header */
        $hesk_settings['tmp_title'] = $hesk_settings['hesk_title'] . ' - ' . htmlspecialchars($thiscat['name']); 
		require_once(HESK_PATH . 'inc/header.inc.php');
		hesk_kb_header($hesk_settings['kb_link']);

		if ($catid == 1)
	    {
	    	echo $hesklang['kb_is'].' &nbsp;';
	    }
    }

	if ($thiscat['parent'])
	{
		$link = ($thiscat['parent'] == 1) ? 'knowledgebase.php' : 'knowledgebase.php?category='.$thiscat['parent'];
		echo '<span class="homepageh3">&raquo; '.$hesklang['kb_cat'].': '.$thiscat['name'].'</span>
        &nbsp;(<a href="javascript:history.go(-1)">'.$hesklang['back'].'</a>)
		';
	}

	$sql = 'SELECT * FROM `'.hesk_dbEscape($hesk_settings['db_pfix']).'kb_categories` WHERE `parent`=\''.hesk_dbEscape($catid).'\' AND `type`=\'0\' ORDER BY `parent` ASC, `cat_order` ASC';
	$result = hesk_dbQuery($sql);
	if (hesk_dbNumRows($result) > 0)
	{
        ?>

		<p>&raquo; <b><?php echo $hesklang['kb_cat_sub']; ?>:</b></p>

<table width="100%" border="0" cellspacing="0" cellpadding="0">
<tr>
	<td width="7" height="7"><img src="img/roundcornerslt.jpg" width="7" height="7" alt="" /></td>
	<td class="roundcornerstop"></td>
	<td><img src="img/roundcornersrt.jpg" width="7" height="7" alt="" /></td>
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

			echo '
		    <td width="50%" valign="top">
			<table border="0">
			<tr><td><img src="img/folder.gif" width="20" height="20" alt="" style="vertical-align:middle" /><a href="knowledgebase.php?category='.$cat['id'].'">'.$cat['name'].'</a></td></tr>
			';

			/* Print two most popular articles */
			if ($hesk_settings['kb_numshow'] && $cat['articles'])
		    {
		    	$sql = 'SELECT `id`,`subject` FROM `'.hesk_dbEscape($hesk_settings['db_pfix']).'kb_articles` WHERE `catid`=\''.hesk_dbEscape($cat['id']).'\' AND `type`=\'0\' ORDER BY `views` DESC, `art_order` ASC LIMIT '.hesk_dbEscape($hesk_settings['kb_numshow']+1);
		        $res = hesk_dbQuery($sql);
		        $num = 1;
				while ($art = hesk_dbFetchAssoc($res))
				{
					echo '
		            <tr>
		            <td><img src="img/article_text.png" width="16" height="16" border="0" alt="" style="vertical-align:middle" />
		            <a href="knowledgebase.php?article='.$art['id'].'" class="article">'.$art['subject'].'</a></td>
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
		        	echo '<tr><td>&raquo; <a href="knowledgebase.php?category='.$cat['id'].'"><i>'.$hesklang['m'].'</i></a></td></tr>';
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
	<td><img src="img/roundcornerslb.jpg" width="7" height="7" alt="" /></td>
	<td class="roundcornersbottom"></td>
	<td width="7" height="7"><img src="img/roundcornersrb.jpg" width="7" height="7" alt="" /></td>
</tr>
</table>

	<?php
	} // END if NumRows > 0
	?>

	<p>&raquo; <b><?php echo $hesklang['ac']; ?></b></p>

<table width="100%" border="0" cellspacing="0" cellpadding="0">
<tr>
	<td width="7" height="7"><img src="img/roundcornerslt.jpg" width="7" height="7" alt="" /></td>
	<td class="roundcornerstop"></td>
	<td><img src="img/roundcornersrt.jpg" width="7" height="7" alt="" /></td>
</tr>
<tr>
	<td class="roundcornersleft">&nbsp;</td>
	<td>

	<?php
	$sql = 'SELECT * FROM `'.hesk_dbEscape($hesk_settings['db_pfix']).'kb_articles` WHERE `catid`=\''.hesk_dbEscape($catid).'\' AND `type`=\'0\' ORDER BY `art_order` ASC';
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
	            $txt = strip_tags($article['content']);
	            if (strlen($txt) > $hesk_settings['kb_substrart'])
	            {
	            	$txt = substr(strip_tags($article['content']),0,$hesk_settings['kb_substrart']).'...';
	            }

	            if ($hesk_settings['kb_rating'])
	            {
	            	$alt = $article['rating'] ? sprintf($hesklang['kb_rated'], sprintf("%01.1f", $article['rating'])) : $hesklang['kb_not_rated'];
	                $rat = '<td width="1" valign="top"><img src="img/star_'.(hesk_round_to_half($article['rating'])*10).'.png" width="85" height="16" alt="'.$alt.'" title="'.$alt.'" border="0" style="vertical-align:text-bottom" /></td>';
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
	                <td width="1" valign="top"><img src="img/article_text.png" width="16" height="16" border="0" alt="" style="vertical-align:middle" /></td>
	                <td valign="top"><a href="knowledgebase.php?article='.$article['id'].'">'.$article['subject'].'</a></td>
	                '.$rat.'
                    </tr>
	                </table>
	                <table border="0" width="100%" cellspacing="0" cellpadding="1">
	                <tr>
	                <td width="1" valign="top"><img src="img/blank.gif" width="16" height="10" style="vertical-align:middle" alt="" /></td>
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
	<td><img src="img/roundcornerslb.jpg" width="7" height="7" alt="" /></td>
	<td class="roundcornersbottom"></td>
	<td width="7" height="7"><img src="img/roundcornersrb.jpg" width="7" height="7" alt="" /></td>
</tr>
</table>

	<?php
	if ($hesk_settings['kb_popart'] && $catid==1)
	{
		?>
        <br /><hr />
        <table border="0" width="100%">
        <tr>
        <td>&raquo; <b><?php echo $hesklang['popart']; ?></b></td>
        <td style="text-align:right"><i><?php echo $hesklang['views']; ?></i></td>
        </tr>
        </table>
		<?php
		$sql = 'SELECT * FROM `'.hesk_dbEscape($hesk_settings['db_pfix']).'kb_articles` WHERE `type`=\'0\' ORDER BY `views` DESC, `rating` DESC, `art_order` ASC LIMIT '.hesk_dbEscape($hesk_settings['kb_popart']);
		$res = hesk_dbQuery($sql);
		if (hesk_dbNumRows($res) == 0)
		{
			echo '<p><i>'.$hesklang['noa'].'</i></p>';
		}
	    else
	    {
			echo '<div align="center"><table border="0" cellspacing="1" cellpadding="3" width="100%">';
			while ($article = hesk_dbFetchAssoc($res))
			{
				echo '
				<tr>
				<td>
	                <table border="0" width="100%" cellspacing="0" cellpadding="0">
	                <tr>
	                <td width="1" valign="top"><img src="img/article_text.png" width="16" height="16" border="0" alt="" style="vertical-align:middle" /></td>
	                <td valign="top">&nbsp;<a href="knowledgebase.php?article='.$article['id'].'">'.$article['subject'].'</a></td>
                    <td valign="top" style="text-align:right" width="200">'.$article['views'].'</td>
                    </tr>
	                </table>
	            </td>
				</tr>';
			}
		    echo '</table></div>';
	    }
	}


	if ($hesk_settings['kb_latest'] && $catid==1)
	{
		?>
		&nbsp;
        <table border="0" width="100%">
        <tr>
        <td>&raquo; <b><?php echo $hesklang['latart']; ?></b></td>
        <td style="text-align:right"><i><?php echo $hesklang['dta']; ?></i></td>
        </tr>
        </table>
		<?php
		$sql = 'SELECT * FROM `'.hesk_dbEscape($hesk_settings['db_pfix']).'kb_articles` WHERE `type`=\'0\' ORDER BY `dt` DESC LIMIT '.hesk_dbEscape($hesk_settings['kb_latest']);
		$res = hesk_dbQuery($sql);
		if (hesk_dbNumRows($res) == 0)
		{
			echo '<p><i>'.$hesklang['noa'].'</i></p>';
		}
	    else
	    {
			echo '<div align="center"><table border="0" cellspacing="1" cellpadding="3" width="100%">';
			while ($article = hesk_dbFetchAssoc($res))
			{
				echo '
				<tr>
				<td>
	                <table border="0" width="100%" cellspacing="0" cellpadding="0">
	                <tr>
	                <td width="1" valign="top"><img src="img/article_text.png" width="16" height="16" border="0" alt="" style="vertical-align:middle" /></td>
	                <td valign="top">&nbsp;<a href="knowledgebase.php?article='.$article['id'].'">'.$article['subject'].'</a></td>
                    <td valign="top" style="text-align:right" width="200">'.hesk_date($article['dt']).'</td>
                    </tr>
	                </table>
	            </td>
				</tr>';
			}
		    echo '</table></div>';
	    }
	}
} // END hesk_show_kb_category()
?>
