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

/* Is rating enabled? */
if (!$hesk_settings['rating'])
{
	die($hesklang['rdis']);
}

$rating = intval($_GET['rating']);
/* Rating can only be 1 or 5 */
if ($rating != 1 && $rating != 5)
{
	die($hesklang['attempt']);
}

$reply_id = intval($_GET['id']);
$track_id = hesk_input($_GET['trackid']);

/* Connect to database */
hesk_dbConnect();

/* Make sure the ticket tracking ID matches */
$sql = "SELECT `trackid` FROM `".hesk_dbEscape($hesk_settings['db_pfix'])."tickets` WHERE `trackid`='".hesk_dbEscape($track_id)."' LIMIT 1";
$result = hesk_dbQuery($sql);
if (hesk_dbNumRows($result) != 1)
{
	die($hesklang['attempt']);
}
$ticket = hesk_dbFetchAssoc($result);
if ($ticket['trackid'] != $track_id)
{
	die($hesklang['attempt']);
}

/* Already rated? */
$sql = "SELECT `rating`,`staffid` FROM `".hesk_dbEscape($hesk_settings['db_pfix'])."replies` WHERE `id`='".hesk_dbEscape($reply_id)."' LIMIT 1";
$result = hesk_dbQuery($sql);
$reply  = hesk_dbFetchAssoc($result);
if (!empty($reply['rating']))
{
	die($hesklang['ar']);
}

/* OK, update rating now */
$sql = "UPDATE `".hesk_dbEscape($hesk_settings['db_pfix'])."replies` SET `rating`='".hesk_dbEscape($rating)."' WHERE `id`='".hesk_dbEscape($reply_id)."'";
hesk_dbQuery($sql);

/* Also update staff rating */
$sql = 'UPDATE `'.hesk_dbEscape($hesk_settings['db_pfix']).'users` SET  `rating`=((`rating`*(`ratingpos`+`ratingneg`))+'.hesk_dbEscape($rating).')/(`ratingpos`+`ratingneg`+1), ';
if ($rating == 5)
{
	$sql .= '`ratingpos`=`ratingpos`+1';
}
else
{
	$sql .= '`ratingneg`=`ratingneg`+1';
}
$sql .= ' WHERE `id`='.hesk_dbEscape($reply['staffid']);
hesk_dbQuery($sql);

header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

header('Content-type: text/plain');
if ($rating == 5)
{
	echo $hesklang['rh'];
}
else
{
	echo $hesklang['rnh'];
}
exit();
?>
