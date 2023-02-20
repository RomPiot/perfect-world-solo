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

$tmp = (isset($_GET['limit'])) ? intval($_GET['limit']) : 0;
$maxresults = ($tmp > 0) ? $tmp : $hesk_settings['max_listings'];

$tmp  = (isset($_GET['page'])) ? intval($_GET['page']) : 1;
$page = ($tmp > 1) ? $tmp : 1;

/* Acceptable $sort values */
$sort_possible = array('trackid','lastchange','name','subject','status','lastreplier','priority','category','dt','id');

if (isset($_GET['sort']) && in_array($_GET['sort'],$sort_possible))
{
	$sort = hesk_input($_GET['sort']);
    $sql .= ' ORDER BY `'.hesk_dbEscape($sort).'` ';
}
else
{
    $sql .= ' ORDER BY `status` ASC, `priority`';
    $sort = 'status';
}

if (isset($_GET['asc']) && $_GET['asc']==0)
{
    $sql .= ' DESC ';
    $asc = 0;
    $asc_rev = 1;
}
else
{
    $sql .= ' ASC ';
    $asc = 1;
    $asc_rev = 0;
    if (!isset($_GET['asc']))
    {
    	$is_default = 1;
    }
}
?>
