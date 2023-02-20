<?
	$DB_Host = "localhost"; 
	$DB_User = "root"; 
	$DB_Password = "root";  
	$DB_Name = "dbo";   
	$top=50;
	$db_link = mysql_connect($DB_Host, $DB_User, $DB_Password);
	$db = mysql_select_db("$DB_Name", $db_link);
	foreach ($_GET as $key => $value)
	{
	$_GET[$key] = mysql_real_escape_string($value, $db_link);
	}
	foreach ($_POST as $key => $value)
	{
	$_POST[$key] = mysql_real_escape_string($value, $db_link);
	}
?>
	
