<?php

    /*-------Config MySQL Database-------*/

    $DB_Host = "localhost";  // localhost or your IP for MySQL
    $DB_User = "root";  // Database username
    $DB_Password = "root";  // Database password
    $DB_Name = "dbo";  // Database name
    
    $ServerIP = "192.168.1.100";  // WAN IP (Public IP) or DOMAIN NAME of your Server
    $LanIP = "192.168.1.100";  // LAN IP of your Server
    $ServerPort = "29000";  // PW Server Port

	$top=50;  // How many top players to show (on rank page)





    /*-------END User Config-------*/





    $db_link = mysql_connect($DB_Host, $DB_User, $DB_Password);
    $db = mysql_select_db("$DB_Name", $db_link);

    // security check for http_get variables to prevent injections
    foreach ($_GET as $key => $value)
    {
        $_GET[$key] = mysql_real_escape_string($value, $db_link);
    }
    // security check for http_get variables to prevent injections
    foreach ($_POST as $key => $value)
    {
        $_POST[$key] = mysql_real_escape_string($value, $db_link);
    }

?>

