<? //=====Script made by Romulan (altered for 343's package)=====// 

?>
<html>
<head>
<title> </title>
</head>
<body bgcolor="white">
</body>
<html>
<?

include "../../config.php";
	
$conn = mysql_connect($DB_Host, $DB_User, $DB_Password );

if (!$conn) {
echo "Database connection error. Details  : " . mysql_error();
   exit;
}
if (!mysql_select_db($DB_Name)) {
   echo "The database doesn't exist. Details  : " . mysql_error();
   exit;
}
$sql = "SELECT * FROM users WHERE users.id IN ( SELECT userid FROM auth WHERE rid=1)";
$result = mysql_query($sql);

if (!$result) {
   echo "Error sending sql query ($sql) to the database. Details : " . mysql_error();
   exit;
}
if (mysql_num_rows($result) == 0) {
   echo "<center><strong><u><font color=red><b>THERE ARE NO GM ACCOUNTS.</b></font></u></strong>";
   exit;
}

$affichage="<center><strong><u><font color=blue><b>GM ACCOUNTS</b></font></u></strong><br/><table><th>ID</th><th>-&nbsp;&nbsp;&nbsp;&nbsp;Login</th>";
while ($row = mysql_fetch_assoc($result)) {

$affichage.="</tr>";
   $affichage.="<td>&nbsp;".$row["ID"]."&nbsp;&nbsp;-</td>";
   $affichage.="<td>&nbsp;&nbsp;&nbsp;".$row["name"]."</td>";
   
$affichage.="</tr>";
}
$affichage.="</table></center>";

mysql_free_result($result);

echo $affichage;
?>

