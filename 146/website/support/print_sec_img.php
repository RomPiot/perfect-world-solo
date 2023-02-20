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

session_name('HESK');
session_start();
define('IN_SCRIPT',true);

/* This will make sure the security image is not cached */
header("expires: -1");
header("cache-control: no-cache, no-store, must-revalidate, max-age=-1");
header("cache-control: post-check=0, pre-check=0", false);
header("pragma: no-store,no-cache");

if (empty($_SESSION['secnum']) || strlen($_SESSION['secnum']) != 5 || preg_match('/\D/',$_SESSION['secnum']))
{
        die('Invalid or missing security number');
}

require('secimg.inc.php');
require('hesk_settings.inc.php');

$sc=new PJ_SecurityImage($hesk_settings['secimg_sum']);
$sc->printImage($_SESSION['secnum']);

exit();
?>
