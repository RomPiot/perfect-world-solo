<?
/*
$DBHost = "";
$DBUser = "";
$DBPassword = "";
$DBName = "";
*/
// We're not going to set config in every file, set below to read from a common config file

include "../../config.php";

$Link = MySQL_Connect($DB_Host, $DB_User, $DB_Password) or die ("Can't connect to MySQL");
MySQL_Select_Db($DB_Name, $Link) or die ("Database ".$DB_Name." does not exist.");

$OnlineAccountQuery = Mysql_Query("SELECT * FROM point WHERE zoneid > -1");
$OnlineAccountNum = Mysql_Num_Rows($OnlineAccountQuery);
$i=0;
WHILE ($i < $OnlineAccountNum) {
    $OnlineAccountArray = Mysql_Fetch_Array($OnlineAccountQuery);
    $UID = $OnlineAccountArray['uid'];
    $SelectTimeQuery = Mysql_Query("SELECT * FROM users WHERE ID = '$UID'");
    $SelectTimeFetch = Mysql_Fetch_Array($SelectTimeQuery);
    $TIME = $SelectTimeFetch['creatime'];
    MySQL_Query("INSERT INTO usecashnow (userid, zoneid, sn, aid, point, cash, status, creatime) VALUES ('$UID', 1, -1, 1, 0, 5000, 0, '$TIME')");
    echo"$UID DONE!<br>";
    $i++;
}
?>