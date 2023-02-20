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
<style>
.trhbbtyb {
  position: fixed;
  right: -71px;
  bottom: 2px;
}
img {
  max-width:75%;
  height:auto;
}
</style>
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
include "./status.php";
?>
<div class="indexbg">
<br><center><font color="red" size="+2"><b>¤<font color="blue">&nbsp  Welcome to Perfect World Private  &nbsp<font color="red">¤</center></font></b>
<table width="50%" height="" align="center" cellpadding="10" cellspacing="10" border="0">
<tr>
<td align="center"><a href="">Home</a></td>
<td align="center"><a href="./register">Create An Account</a></td>
</tr>
<tr>
<td align="center"><a href="./downloads">Downloads</a></td>
<td align="center"><a href="./changepw">Change Your Account Password</a></td>
</tr>
<tr>
<td align="center"><a href="./rank">View Player Rankings</a></td>
<td align="center"><a href="./getgold">Purchase Gold</a></td>
</tr>
<tr>
<td align="center"><a href="./forum" target="_blank">Forum</a></td>
<td align="center"><a href="./getitem">Get an Item</a></td>
</tr>
<tr>
<td align="center"><a href="./support" target="_blank">Support / Submit a Ticket</a></td>
<td align="center"><a href="./guildicon">Change Your Faction / Guild Icon</a></td>
</tr></table>
<!--<div class="trhbbtyb"><img src="./patch/wm.jpg"></img></div>-->
<center><p><font color="red" size="+9001"><b>¤<font color="blue">&nbsp  Special 10<sup>th</sup> Anniversary Edition  &nbsp<font color="red">¤</p></center></font></b>
</body>
</html>
