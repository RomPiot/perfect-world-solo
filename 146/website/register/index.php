<html>
<head>
<!--    <meta http-equiv="refresh" content="10" > -->
<script>
<!--
function land(ref, target)
{
lowtarget=target.toLowerCase();
if (lowtarget=="_self") {window.location=loc;}
else {if (lowtarget=="_top") {top.location=loc;}
else {if (lowtarget=="_blank") {window.open(loc);}
else {if (lowtarget=="_parent") {parent.location=loc;}
else {parent.frames[target].location=loc;};
}}}
}
function jump(menu)
{
ref=menu.choice.options[menu.choice.selectedIndex].value;
splitc=ref.lastIndexOf("*");
target="";
if (splitc!=-1)
{loc=ref.substring(0,splitc);
target=ref.substring(splitc+1,1000);}
else {loc=ref; target="_self";};
if (ref != "") {land(loc,target);}
}
//-->
</script>

<style type="text/css">
label
{
float: left;
width: 8em;
margin-right: 0.5em;
text-align: right;
display: block;
}
</style>

</head>
<body style="margin: 0;" bgcolor="#eeeeee">

<?
include "../status.php";
?>
<div class="indexbg">
<table width="80%" height="25" align="center" cellpadding="10" cellspacing="0" border="0">
<tr>
    <td align="center" valign="top">

<br><font size="+3" color="blue"><b>SIGN - UP</b></font><br>
    <?
include "../config.php";

    $Data = '<form action=index.php method=post>
     <br><br>

<table width=517 border=0 cellpadding=3 cellspacing=1>
<tr title="The name you will use to log into the game/website">
 <td width=257 align=right><font color=black><b>User Name</b></td>
 <td width=6><font color=black><b>:</b></td>
 <td width=294><input name=login type=text></td>
</tr>
<tr title="Your e-mail address">
 <td width=257 align=right><font color=black><b>Email Address</b></td>
 <td width=6><font color=black><b>:</b></td>
 <td width=294><input name=email type=text></td>
</tr>
<tr title="Admins will *NOT* have access to your password, this is a secret identifier they can use to verify your identity">
 <td width=257 align=right><font color=black><b>Secret (Phrase/Word) Identifier</b></td>
 <td width=6><font color=black><b>:</b></td>
 <td width=294><input name=secret type=text></td>
</tr>
<tr title="The password you will use to log into the game/website">
 <td width=257 align=right><font color=black><b>Password</b></td>
 <td width=6><font color=black><b>:</b></td>
 <td width=294><input name=passwd type=password></td>
</tr>
<tr title="Re-type the password you would like to use please">
 <td width=257 align=right><font color=black><b>Confirm Password</b></td>
 <td width=6><font color=black><b>:</b></td>
 <td width=294><input name=repasswd type=password></td>
</tr>
</table>
<br>

<!--    <label><b>User Name:</b></label>
    <input type=text name=login><br><br>
    <label><b>Email Address:</b></label>
    <input type=text name=email><br><br>
    <label><b>Password:</b></label>
    <input type=password name=passwd><br><br>
    <label><b>Confirm Password:</b></label>
    <input type=password name=repasswd><br><br>
-->

    <input type=submit name=submit value="      Create   Account      ">

    </form>

<!--
<form action="AccountServices" method="post">
<select name="choice" size="1">
<option value="">Account Services:</option>
<option value="">- - - - - - - - - -</option>
<option value="../changepw*_top">Change Your Account Password</option>
<option value="../getgold*_blank">Purchase Gold</option>
<option value="../getitem*_blank">Get Item</option>
<option value="../guildicon*_blank">Change Your Faction / Guild Icon</option>
</select>
<input TYPE="button" VALUE="Go" onClick="jump(this.form)"></form>
-->
<!--
    <input type="button" onClick="location.href=\'../changepw\'" value="Change Your Account Password"></input><br>
    <input type="button" onClick="location.href=\'../getgold\'" value="Purchase Gold"></input><br>
    <input type="button" onClick="location.href=\'../getitem\'" value="Get Item"></input><br>
    <input type="button" onClick="location.href=\'../guildicon\'" value="Change Your Faction / Guild Icon"></input><br>
-->
';
        
    if (isset($_POST['login']))
        {
            $Link = MySQL_Connect($DB_Host, $DB_User, $DB_Password) or die ("Can't connect to MySQL");
            MySQL_Select_Db($DB_Name, $Link) or die ("Database ".$DB_Name." does not exist.");
            
            $Login = $_POST['login'];
            $Pass = $_POST['passwd'];
            $Repass = $_POST['repasswd'];
            $Email = $_POST['email'];
            $Secret = $_POST['secret'];

            $Login = StrToLower(Trim($Login));
            $Pass = Trim($Pass);
            $Repass = Trim($Repass);
            $Email = StrToLower(Trim($Email));
            $Secret = StrToLower(Trim($Secret));
    
        if (empty($Login) || empty($Pass) || empty($Repass) || empty($Email) || empty($Secret))
            {
                echo "One or more fields are empty.";
            }
        
        /* BEGIN CHARACTER CONTROL */

         elseif (ereg("[^0-9a-zA-Z_~!@#$%^*+=?/.-]", $Login, $Txt))
            {
                echo "Incorrect User Name format.";
            }
            
         elseif (ereg("[^0-9a-zA-Z_~!@#$%^*+=?/.-]", $Pass, $Txt))
            {
                echo "Incorrect Password format.";    
            }
        
         elseif (ereg("[^0-9a-zA-Z_~!@#$%^*+=?/.-]", $Repass, $Txt))
            {
                echo "Confirmation Password error.";    
            }

         elseif (ereg("[^0-9a-zA-Z_~!@#$%^*+=?/.\040-]", $Secret, $Txt))
            {
                echo "Please try a different Secret Identifier.";    
            }

        /* END CHARACTER CONTROL */

        elseif (!ereg("^[^@]+@([a-z0-9\-]+\.)+[a-z]{2,4}$", $Email))
            {
                echo "Incorrect Email Address format.";    
            }    

        else
            {
                $Result = MySQL_Query("SELECT name FROM users WHERE name='$Login'") or ("Can't execute query.");
                
        if (MySQL_Num_Rows($Result))
            {
                echo "Account <b>".$Login."</b> already exists";
            }
        
        elseif ((StrLen($Login) < 4) or (StrLen($Login) > 20)) 
            {
                echo "User Name must have more than 4 and not more than 20 characters.";
            }
            
        elseif ((StrLen($Pass) < 6) or (StrLen($Pass) > 20)) 
            {
                echo "Password must have more than 6 and not more than 20 characters.";
            }
            
        elseif ((StrLen($Repass) < 6) or (StrLen($Repass) > 20)) 
            {
                echo "Confirmation Password error.";
            }
            
        elseif ((StrLen($Email) < 6) or (StrLen($Email) > 256)) 
            {
                echo "Email Address must have more than 6 characters.";
            }
        
        elseif ((StrLen($Secret) < 6) or (StrLen($Secret) > 256)) 
            {
                echo "Secret Identifier must be longer than that.";
            }

        elseif (($Secret == "secret") || ($Secret == "\$ecret") || ($Secret == "secre^") || ($Secret == "\$ecre^") || ($Secret == "secrets") || ($Secret == "\$ecrets") || ($Secret == "secret\$") || ($Secret == "\$ecret\$") || ($Secret == "secre^s") || ($Secret == "\$ecre^s") || ($Secret == "secre^\$") || ($Secret == "\$ecre^\$") || ($Secret == ""))
            {
                echo "Secret Identifier must be unique.";
            }

        elseif ($Pass != $Repass)
            {
                echo "Confirmation Password error.";
            }        
        else
            {
                $Salt = $Login.$Pass;
                $Salt = md5($Salt);
                $Salt = "0x".$Salt;
                $Salt2 = base64_encode(md5($Login.$Pass, true));
                $IPL = $_SERVER['REMOTE_ADDR'];
                MySQL_Query("call adduser('$Login', $Salt, '0', '$Secret', '0', '$IPL', '$Email', '0', '0', '0', '0', '0', '0', '0', '', '', $Salt, '', '', '')") or die ("Can't execute query.");
// Un-comment the below section to automatically add 'gold' to brand new registrants (& set the 'amount') //
//		$mysqlresult=MySQL_Query("select * from `users` WHERE `name`='$Login'");
//		$User_ID=MySQL_result($mysqlresult,0,'ID');
//		MySQL_Query("call usecash('$User_ID',1,0,1,0,amount,1,@error)") or die ("usecash failed!");
                echo "Account <b>".$Login."</b> has been registered.";
            }        
        }    
    }
    
    echo $Data;    
    
?>
    </td>
</tr>
</table>

</body>
</html>
