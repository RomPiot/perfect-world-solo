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

$sql = "SELECT `t1`.* , `t2`.`name` AS `repliername`
FROM `".hesk_dbEscape($hesk_settings['db_pfix'])."tickets` AS `t1` LEFT JOIN `".hesk_dbEscape($hesk_settings['db_pfix'])."users` AS `t2` ON `t1`.`replierid` = `t2`.`id`
WHERE ";

/* Some default settings */
$archive = array(1=>0,2=>0);
$s_my = array(1=>1,2=>1);
$s_ot = array(1=>1,2=>1);
$s_un = array(1=>1,2=>1);

/* Archived */
if (!empty($_GET['archive']))
{
    $archive[1]=1;
    $sql .= '`archive`=\'1\' AND ';
}

/* Assignment */
$fid = 1;
require(HESK_PATH . 'inc/assignment_search.inc.php');

/* Allowed categories */
$sql .= hesk_myCategories();

/* Get all the SQL sorting preferences */
/*
STATUS NUMBER MEANING
0 = NEW
1 = WAITING REPLY
2 = REPLIED
3 = RESOLVED (CLOSED)
4 = ANY STATUS
5 = 0 + 1
6 = 0 + 1 + 2
*/
if (!isset($_GET['status']))
{
    $status=6;
    $sql .= ' AND (`status`=\'0\' OR `status`=\'1\' OR `status`=\'2\') ';
}
else
{
    $status = hesk_isNumber($_GET['status']);

    if ($status==5)
    {
        $sql .= ' AND (`status`=\'0\' OR `status`=\'1\') ';
    }
    elseif ($status==6)
    {
        $sql .= ' AND (`status`=\'0\' OR `status`=\'1\' OR `status`=\'2\') ';
    }
    elseif ($status!=4)
    {
        $sql .= ' AND `status`=\''.hesk_dbEscape($status).'\' ';
    }

}

$category = (isset($_GET['category'])) ? hesk_isNumber($_GET['category']) : 0;
if ($category)
{
    $sql .= ' AND `category`=\''.hesk_dbEscape($category).'\' ';
}

#$sql_copy=$sql;

/* Prepare variables used in search and forms */
require_once(HESK_PATH . 'inc/prepare_ticket_search.inc.php');

/* List tickets? */
if (!isset($_SESSION['hide']['ticket_list']))
{
	$href = 'show_tickets.php';
	require_once(HESK_PATH . 'inc/ticket_list.inc.php');
}
?>
