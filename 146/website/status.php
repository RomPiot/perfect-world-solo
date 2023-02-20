<html>
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
<style>
.bg {
  position: relative;
  z-index: 1;
}

.bg:before {
  /* new controls */
  content: "";
  position: absolute;
  z-index: -1;
  top: 0;
  bottom: 0;
  left: 0;
  right: 0;

  /* the image used */
  //background-image: url("banner.jpg");
  background-image: radial-gradient(ellipse, rgba(237,237,237,0.00)0%, rgba(237,237,237,0.50)50%, rgba(237,237,237,1.00)100%), url("banner.jpg");

  /* width & opacity */
  //width: 80%; 
  //opacity: 0.75;

  /* center and scale the image nicely */
  background-position: center;
  background-repeat: no-repeat;
  background-size: cover;
    margin-left: auto;
    margin-right: auto;
}
</style>
<style>
.indexbg {
  position: relative;
  z-index: 1;
}

.indexbg:before {
  /* new controls */
  content: "";
  position: absolute;
  z-index: -1;
  top: 0;
  bottom: 0;
  left: 0;
  right: 0;

  /* the image used */
  //background-image: url("bg.jpg");
  background-image: radial-gradient(circle, rgba(237,237,237,0.00)0%, rgba(237,237,237,0.50)50%, rgba(237,237,237,1.00)100%), url("bg.jpg");

  /* width & opacity */
  //width: 80%; 
  height: 2160;
  opacity: 0.25;

  /* center and scale the image nicely */
  background-position: top;
  background-repeat: no-repeat;
  background-size: cover;
    margin-left: auto;
    margin-right: auto;
    background-attachment: fixed;
}
</style>
</head>
<body>
<table width="80%" height="25" align="center" cellpadding="10" cellspacing="0" border="1">
<tr >
<td align="right" valign="top" class="bg">
<!--<div class="bg">-->
<center>
<font size="+3" color="gray"><b>Perfect World</b></font>
</center>
<br><br><br>
<form action="AccountServices" method="post">
<select name="choice" size="1" onChange="jump(this.form)">
<option value=""> - Navigation Menu - </option>
<option value="">- - - - - - - - - - - - - - -</option>
<option value=""><b>Site Services:</b></option>
<option value="">- - - - - - - - - - - - - - -</option>
<option value="../*_top">Home</option>
<option value="../downloads*_top">Downloads</option>
<option value="../rank*_top">View Player Rankings</option>
<option value="../forum*_blank">Forum</option>
<option value="../support*_blank">Support / Submit a Ticket</option>
<option value="">- - - - - - - - - - - - - - -</option>
<option value=""><b>Account Services:</b></option>
<option value="">- - - - - - - - - - - - - - -</option>
<option value="../register*_top">Create An Account</option>
<option value="../changepw*_top">Change Your Account Password</option>
<option value="../getgold*_top">Purchase Gold</option>
<option value="../getitem*_top">Get an Item</option>
<option value="../guildicon*_top">Change Your Faction / Guild Icon</option>
</select>
<!--<input TYPE="button" VALUE="Go" onClick="jump(this.form)">-->
</form>

<?
include "config.php";

        $result = mysql_query("SELECT zoneid FROM point WHERE zoneid IS NOT NULL");
        $count = @mysql_num_rows($result);

        $sockres = @FSockOpen($LanIP, $ServerPort, $errno, $errstr, 1);
            
        if (!$sockres) 
        {
            mysql_query("DELETE FROM online");
            $Count = 0;
            echo "Server: <font color=red><b>Offline</b></font>";            
        } 
        else 
        {
        @FClose($sockres);
            echo "Server: <font color=lightgreen><b>Online</b></font>";
        }

        echo "&nbsp; <a href='../rank'></a>Users Online: <b>".$count."</b> &nbsp;";
    ?>
        <!--</div>-->
    </td>
</tr>
</table>
<div class="trhbbtyb"><img src="./patch/wm.jpg"></img></div>
<div class="trhbbtyb"><img src="../patch/wm.jpg"></img></div>
</body>
</html>