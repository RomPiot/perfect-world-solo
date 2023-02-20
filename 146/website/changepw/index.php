<head>
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
</head>
<?
include "../status.php";
?>
<div class="indexbg">
<table width="80%" height="25" align="center" cellpadding="10" cellspacing="0" border="1">
</table>
<body bgcolor="#161616">
<font color=white>
<br>
<center>
<?
echo "
<font color='blue' size='+3'><b>Change Password</font><br><br><br></b>
<table width='517' border='0' cellpadding='0' cellspacing='1'>
<tr>
<form name='form1' method='post' action='checkinfo.php'>	
<td>
<table width='517' border='0' cellpadding='3' cellspacing='1'>
<tr>
<td width='257' align=right><b><font color=white>User Name</td>
<td width='6'><font color=white>:</td>
<td width='294'><input name='myusername' type='text' id='myusername'></td>
</tr>
<tr>
<td width='257' align=right><b><font color=white>Email Address</td>
<td width='6'><font color=white>:</td>
<td width='294'><input name='email' type='text' id='email'></td>
</tr>
<tr>
<td width='257' align=right><b><font color=white>Secret (Phrase/Word) Identifier</td>
<td width='6'><font color=white>:</td>
<td width='294'><input name='mysecret' type='text' id='mysecret'></td>
</tr>
<tr>
<td width='257' align=right><b><font color=white>Current Password</td>
<td width='6'><font color=white>:</td>
<td width='294'><input name='oldpassword' type='password' id='oldpassword'></td>
</tr>
<tr>
<td width='257' align=right><b><font color=white>New Password</td>
<td width='6'><font color=white>:</td>
<td width='294'><input name='newpassword' type='password' id='newpassword'></td>
</tr>
<tr>
<td width='257' align=right><b><font color=white>Confirm New Password</td>
<td width='6'><font color=white>:</td>
<td width='294'><input name='confirmnew' type='password' id='confirmnew'></td>
</tr>
</tr>
<tr>
<td>&nbsp;</td>
<td>&nbsp;</td>
<!--<td><input type='submit' name='Submit' value='Change Password'></td>-->
</tr>
</table>
</td>

</tr>
</table>
<input type='submit' name='Submit' value='Change Password'>
</form>
<br><br>
<!--
<font color=red>NOTE: We log all activity using this feature anyone caught misusing will be tracked and removed from the Game!</font>
-->
";
?>
<!--
<input type="button" onClick="location.href='../register'" value="Create an Account"></input>
-->
</center>
