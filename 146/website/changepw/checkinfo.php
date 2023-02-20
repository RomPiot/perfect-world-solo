<body bgcolor="#161616">
<font color=white>
<style type="text/css">
<!--
body {
	background-color: #161616;
	background-image: url();
	background-repeat: no-repeat;
	background-position: center 0;
	color: white;
}
	A:link {text-decoration: none; color: orange;}
	A:visited {text-decoration: none; color: orange;}
	A:active {text-decoration: none; color: orange;}
	A:hover {text-decoration: underline overline; color: white;}
</style>
<?
require_once("DB.php");

include "../config.php";
// $dbdsn = 'mysql://root:root@localhost/dbo';
   $mysql = 'mysql://';
   $col = ':';
   $at = '@';
   $slash = '/';
   $dbdsn = $mysql.$DB_User.$col.$DB_Password.$at.$DB_Host.$slash.$DB_Name;

$dbh =& DB::connect($dbdsn);
if (PEAR::isError($dbh)) {
	die($dbh->getMessage());
}
$dbh->setFetchMode(DB_FETCHMODE_ASSOC);

$UserName=$_POST['myusername']; 
$EMail=$_POST['email'];
$OldPassword=$_POST['oldpassword'];
$NewPassword=$_POST['newpassword'];
$ConfirmNew=$_POST['confirmnew'];
$Secret=$_POST['mysecret'];

$UserName = stripslashes(StrToLower($UserName));
$EMail = stripslashes(StrToLower($EMail));
$OldPassword = stripslashes($OldPassword);
$NewPassword = stripslashes($NewPassword);
$ConfirmNew = stripslashes($ConfirmNew);
$Secret = stripslashes(StrToLower($Secret));

$UserName = mysql_real_escape_string($UserName);
 $EMail = mysql_real_escape_string($EMail);
$OldPassword = mysql_real_escape_string($OldPassword);
$NewPassword = mysql_real_escape_string($NewPassword);
 $ConfirmNew = mysql_real_escape_string($ConfirmNew);
$Secret = mysql_real_escape_string($Secret);

        if (empty($UserName) || empty($EMail) || empty($OldPassword) || empty($NewPassword) || empty($ConfirmNew) || empty($Secret))
            {
                echo "<font color=red>One or more fields are empty.</font><br><input type='button' onClick=location.href='index.php' value='Try Again / Change Your Account Password'></input><br><br>";
            }
ELSE {

//Count String Length
$CountNewPassword = strlen($NewPassword);

IF ($CountNewPassword < 6 OR $CountNewPassword > 20) {
	echo "<font color=red>Password Must be at least 6 Characters, and no more than 20. </font><br><input type='button' onClick=location.href='index.php' value='Try Again / Change Your Account Password'></input><br><br>";
}
ELSE {

// Make sure no illegal characters are used!
IF (ereg("[^0-9a-zA-Z_~!@#$%^*+=?/.-]", $UserName, $Txt))
  {
    echo "<font color=red>ILLEGAL CHARACTERS USED!</font><br><input type='button' onClick=location.href='index.php' value='Try Again / Change Your Account Password'></input><br><br>";
  }
      ELSEIF (ereg("[^0-9a-zA-Z_~!@#$%^*+=?/.-]", $EMail, $Txt))
        {
        echo "<font color=red>ILLEGAL CHARACTERS USED!</font><br><input type='button' onClick=location.href='index.php' value='Try Again / Change Your Account Password'></input><br><br>";
        }
      ELSEIF (ereg("[^0-9a-zA-Z_~!@#$%^*+=?/.-]", $OldPassword, $Txt))
        {
        echo "<font color=red>ILLEGAL CHARACTERS USED!</font><br><input type='button' onClick=location.href='index.php' value='Try Again / Change Your Account Password'></input><br><br>";
        }
      ELSEIF (ereg("[^0-9a-zA-Z_~!@#$%^*+=?/.-]", $NewPassword, $Txt))
        {
        echo "<font color=red>ILLEGAL CHARACTERS USED!</font><br><input type='button' onClick=location.href='index.php' value='Try Again / Change Your Account Password'></input><br><br>";
        }
      ELSEIF (ereg("[^0-9a-zA-Z_~!@#$%^*+=?/.-]", $ConfirmNew, $Txt))
        {
        echo "<font color=red>ILLEGAL CHARACTERS USED!</font><br><input type='button' onClick=location.href='index.php' value='Try Again / Change Your Account Password'></input><br><br>";
        }
      ELSEIF (ereg("[^0-9a-zA-Z_~!@#$%^*+=?/.\040-]", $Secret, $Txt))
        {
        echo "<font color=red>ILLEGAL CHARACTERS USED!</font><br><input type='button' onClick=location.href='index.php' value='Try Again / Change Your Account Password'></input><br><br>";
        }
ELSE {

// Make sure New Password fields match
IF ( $NewPassword !== $ConfirmNew ) {
    echo "<font color=red>Confirm New Password Failed. <font color=white>New Password</font> and <font color=white>Confirm New Password</font> Fields Must Match. Please Try Again.</font><br><input type='button' onClick=location.href='index.php' value='Try Again / Change Your Account Password'></input><br><br>";
}
ELSE {

//Encode Password and Username
$EncryptOldPassword = "0x" . md5($UserName . $OldPassword);
$EncryptNewPassword = "0x" . md5($UserName . $NewPassword);

$GetIP=$_SERVER['REMOTE_ADDR'];

$GetAccountInfo = Mysql_Query("SELECT * FROM users WHERE name = '$UserName'");
$GetAccountNum = Mysql_Num_Rows($GetAccountInfo);
IF ($GetAccountNum == 1) {
	$GetAccountArray = Mysql_Fetch_Array($GetAccountInfo);
	$GetPassword = $GetAccountArray['passwd'];
        $GetEmail = $GetAccountArray['email'];
        $GetSec = $GetAccountArray['answer'];

	$GetPassword = addslashes($GetPassword);
        $GetEmail = addslashes($GetEmail);
        $GetSec = addslashes($GetSec);

	$rs = mysql_query("SELECT fn_varbintohexsubstring (1,'$GetPassword',1,0) AS result");
        $rs2 = mysql_query("SELECT '$GetEmail' AS result2");
        $rs3 = mysql_query("SELECT '$GetSec' AS result3");

	$GetResult = Mysql_Fetch_Array($rs);
        $GetResultEmail = Mysql_Fetch_Array($rs2);
        $GetResultSec = Mysql_Fetch_Array($rs3);

	$CheckPassword = $GetResult['result'];
        $CheckEmail = $GetResultEmail['result2'];
        $CheckSec = $GetResultSec['result3'];
        IF ($EMail == $CheckEmail) {
        IF ($Secret == $CheckSec) {
	IF ($EncryptOldPassword == $CheckPassword) {
		Mysql_Query("CALL changePasswd ($GetAccountInfo->quoteSmart'$UserName', $EncryptNewPassword)");
		Mysql_Query("CALL changePasswd2 ($GetAccountInfo->quoteSmart'$UserName', $EncryptNewPassword)");
        MySQL_Query("UPDATE users SET passchgip='".$GetIP."' WHERE name='".$UserName."'");
		echo "<font color='green' size='+2'>Password for Account: <font color=red>$UserName</font> has been changed to: <font color=red>$NewPassword</font></font><br><input type='button' onClick=location.href='../' value='Go Back'></input><br><br>";
	}
	ELSE {
		echo "<font color=red>Account Information is Incorrect! </font><br><input type='button' onClick=location.href='index.php' value='Try Again / Change Your Account Password'></input><br><br>";
	}
}
ELSE {
	echo "<font color=red>Account Information is Incorrect! </font><br><input type='button' onClick=location.href='index.php' value='Try Again / Change Your Account Password'></input><br><br>";
    }

}
ELSE {
	echo "<font color=red>Account Information is Incorrect! </font><br><input type='button' onClick=location.href='index.php' value='Try Again / Change Your Account Password'></input><br><br>";
    }

    }
   }
  }
 }
}
?>
