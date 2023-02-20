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

/***************************
Function hesk_uploadFiles()
***************************/
function hesk_uploadFile($i) {
	global $hesk_settings, $hesklang, $trackingID;

	/* Return if name is empty */
	if (empty($_FILES['attachment']['name'][$i])) {return '';}

	/* Check file size */
	if ($_FILES['attachment']['size'][$i] > ($hesk_settings['attachments']['max_size']*1024))
	{
	    hesk_error(sprintf($hesklang['file_too_large'],$_FILES['attachment']['name'][$i]));
	}
	else
	{
	    $file_size = $_FILES['attachment']['size'][$i];
	}

	/* Check file extension */
	$ext = strtolower(strrchr($_FILES['attachment']['name'][$i], "."));
	if (!in_array($ext,$hesk_settings['attachments']['allowed_types']))
	{
	    hesk_error(sprintf($hesklang['type_not_allowed'],$_FILES['attachment']['name'][$i]));
	}

	/* Generate a random file name */
	$file_realname = str_replace(array('/','\\','#',',',' '), array('','','','','_'),$_FILES['attachment']['name'][$i]);
	$useChars='AEUYBDGHJLMNPQRSTVWXZ123456789';
	$tmp = $useChars{mt_rand(0,29)};
	for($j=1;$j<10;$j++)
	{
	    $tmp .= $useChars{mt_rand(0,29)};
	}

    if (defined('KB'))
    {
    	$file_name = substr(md5($tmp . $file_realname), 0, 200) . $ext;
    }
    else
    {
    	$file_name = substr($trackingID . '_' . md5($tmp . $file_realname), 0, 200) . $ext;
    }

	/* If upload was successful let's create the headers */
	if (!move_uploaded_file($_FILES['attachment']['tmp_name'][$i], $hesk_settings['server_path'].'/attachments/'.$file_name))
	{
	    hesk_error($hesklang['cannot_move_tmp']);
	}

	$info = array(
	    'saved_name'=> $file_name,
	    'real_name' => $file_realname,
	    'size'      => $file_size
	);

	return $info;
} // End hesk_uploadFile()
?>
