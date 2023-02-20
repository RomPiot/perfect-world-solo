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
<br><center><font color="blue" size="+3"><b>Downloads</center></font></b><br><br>
<table width="50%" align="center" cellpadding="40" cellspacing="40" border="0"><tr>
<td align="center"><font color="blue" size="+2"><a href="./clients"> Clients ... </a></td>
<td align="center"><font color="blue" size="+2"><a href="./updates"> Updates ... </a></td>
</tr></table>
</body>
</html>
