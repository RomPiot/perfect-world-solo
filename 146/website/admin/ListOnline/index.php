<? //=====Script by Romulan (altered for 343's package)=====//

?>
<html>
<head>
<meta http-equiv="refresh" content="15">
<title> </title>
</head>
<body bgcolor="white">
<center>
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
$sql = "SELECT * FROM users WHERE users.id IN ( SELECT uid FROM point WHERE zoneid=1)";
$result = mysql_query($sql);

if (!$result) {
   echo "Error sending sql query ($sql) to the database. Details : " . mysql_error();
   exit;
}
if (mysql_num_rows($result) == 0) {
   echo "<center><strong><u><font color=red><b>NOBODY ONLINE</b></font></u></strong>";
   exit;
}

$affichage="<center><strong><u><font color=green><b>ONLINE PLAYERS</b></font></u></strong><br/><table><th>ID</th><th>-&nbsp;&nbsp;&nbsp;&nbsp;Login</th>";
while ($row = mysql_fetch_assoc($result)) {

$affichage.="</tr>";
   $affichage.="<td>&nbsp;".$row["ID"]."&nbsp;&nbsp;-</td>";
   $affichage.="<td>&nbsp;&nbsp;".$row["name"]."</td>";
   
$affichage.="</tr>";
}
$affichage.="</table></center>";

mysql_free_result($result);

echo $affichage;
?>
</center>
</body>
<html>
