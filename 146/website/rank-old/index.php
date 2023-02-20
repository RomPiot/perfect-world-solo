
<br>
<head>	
	<link rel="stylesheet" type="text/css" href=".aussehen.css">
<!--
	<meta http-equiv="refresh" content="30" >
-->
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

<body bgcolor="#2F4F4F" scroll='no'  width="100%" height="100%" scroll="no">

<?
include "../status.php";
?>
<div class="indexbg">
<table width="80%" height="25" align="center" cellpadding="10" cellspacing="0" border="1">
</table>

<table width="100%" height="30%" cellpadding="0" cellspacing="0" border="0" scroll="no">

<tr>
	<td align="center" valign="">
	<?php
	
//		include ".rank_config.php";
include "../config.php";


		$result = mysql_query("SELECT * FROM uwebplayers ORDER BY rolelevel DESC")or die(mysql_error());
		$row = mysql_fetch_assoc($result);
		$z=1;
		$c=1;
?>
<font size="+3" color="silver"><center>Top Players</font></center><br>
<table border="1" cellpadding="1" cellspacing="0" style="width=1200";">
	<tbody>
		<tr>
			<td scope="col">
				&nbsp Rank &nbsp</td>
			<td scope="col">
				&nbsp Character Name &nbsp</td>
			<td scope="col">
				&nbsp Level &nbsp</td>
			<td scope="col">
				&nbsp Sex &nbsp</td>
			<td scope="col">
				&nbsp Class &nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp</td>
			<td scope="col">
				&nbsp Reputation &nbsp</td>
			<td scope="col">
				&nbsp PK-Status &nbsp</td>
			



		</tr>


		<tr>
			<td>
				<?php 
				echo $z;
				$z++
				?>
			</td>
			<td>
				<?php 
				echo $row['rolename'];
				?>
			</td>
			<td>
				<?php 
				if ($row['rolelevel']<30) 
					{
					?>
						
					<?php
					echo $row['rolelevel'];
					} 
				elseif ($row['rolelevel']==30)
					{
					?>
						
					<?php
					echo $row['rolelevel'];	
					}
				elseif ($row['rolelevel']>30 && $row['rolelevel']<60)
					{
					?>
						
					<?php
					echo $row['rolelevel'];	
					}
				elseif ($row['rolelevel']>60 && $row['rolelevel']<89)
					{
					?>		
						
					<?php
					echo $row['rolelevel'];	
					}
				elseif ($row['rolelevel']>89)
					{
					?>	
						
					<?php
					echo $row['rolelevel'];	
					}
					?>
			</td>
			<td>
				<?php 
				if ($row['rolegender']>0) 
					{
					?>
						<img src="./weiblich.png" alt="weiblich" width="30" height="30">
					<?php
					} 
				else 
					{
					?>
						<img src="./mannlich.png" alt="mänlich" width="30" height="30">
					<?php
					}
					?>
			</td>	
			<td>				
				<?php
				if ($row['roleprof']==0) 
					{
					?>			
						Blademaster
					<?php
			
					} 
				elseif ($row['roleprof']==1)
					{
					?>	
						Wizard
					<?php
				
					}
				elseif ($row['roleprof']==2)
					{
					?>	
						Psychic
					<?php
				
					}
				elseif ($row['roleprof']==3)
					{
					?>	
						Venomancer
					<?php
				
					}
				elseif ($row['roleprof']==4)
					{
					?>	
						Barbarian
					<?php
				
					} 
				elseif ($row['roleprof']==5)
					{
					?>	
						Assassin
					<?php
				
					}
				elseif ($row['roleprof']==6)
					{
					?>	
						Archer
					<?php
				
					} 
				elseif ($row['roleprof']==7)
					{
					?>	
						Cleric
					<?php
				
					} 
				elseif ($row['roleprof']==8)
					{
					?>	
						Seeker
					<?php
				
					}
				elseif ($row['roleprof']==9)
					{
					?>		
						Mystic
					<?php
					}
					?>
			</td>
			<td>
					<?php
					echo $row['rolerep'];
					?>
			</td>
			<td>
					<?php
				if ($row['redname']>0) 
					{
					?>
						<img src="./rot.jpg" alt="weiblich" width="30" height="30">
					<?php
					} 
				else 
					{
					?>
						<img src="./weiss.jpg" alt="mänlich" width="30" height="30">
					<?php
					}
					?>
			</td>


			</tr>
			





<?php
while($row = mysql_fetch_assoc($result))

{
?>

				<tr>
			<td>
				<?php 
				echo $z;
				$z++
				?>
			</td>
			<td>
				<?php 
				echo $row['rolename'];
				?>
			</td>
			<td>
				<?php 
				if ($row['rolelevel']<30) 
					{
					?>
						
					<?php
					echo $row['rolelevel'];
					} 
				elseif ($row['rolelevel']==30)
					{
					?>
						
					<?php
					echo $row['rolelevel'];	
					}
				elseif ($row['rolelevel']>30 && $row['rolelevel']<60)
					{
					?>
						
					<?php
					echo $row['rolelevel'];	
					}
				elseif ($row['rolelevel']>60 && $row['rolelevel']<89)
					{
					?>		
						
					<?php
					echo $row['rolelevel'];	
					}
				elseif ($row['rolelevel']>89)
					{
					?>	
						
					<?php
					echo $row['rolelevel'];	
					}
					?>
			</td>
			<td>
				<?php 
				if ($row['rolegender']>0) 
					{
					?>
						<img src="./weiblich.png" alt="weiblich" width="30" height="30">
					<?php
					} 
				else 
					{
					?>
						<img src="./mannlich.png" alt="mänlich" width="30" height="30">
					<?php
					}
					?>
			</td>	
			<td>				
				<?php
				if ($row['roleprof']==0) 
					{
					?>			
						Blademaster
					<?php
			
					} 
				elseif ($row['roleprof']==1)
					{
					?>	
						Wizard
					<?php
				
					}
				elseif ($row['roleprof']==2)
					{
					?>	
						Psychic
					<?php
				
					}
				elseif ($row['roleprof']==3)
					{
					?>	
						Venomancer
					<?php
				
					}
				elseif ($row['roleprof']==4)
					{
					?>	
						Barbarian
					<?php
				
					} 
				elseif ($row['roleprof']==5)
					{
					?>	
						Assassin
					<?php
				
					}
				elseif ($row['roleprof']==6)
					{
					?>	
						Archer
					<?php
				
					} 
				elseif ($row['roleprof']==7)
					{
					?>	
						Cleric
					<?php
				
					} 
				elseif ($row['roleprof']==8)
					{
					?>	
						Seeker
					<?php
				
					}
				elseif ($row['roleprof']==9)
					{
					?>		
						Mystic
					<?php
					}
					?>
			</td>
			<td>
					<?php
					echo $row['rolerep'];
					?>
			</td>
			<td>
					<?php
				if ($row['redname']>0) 
					{
					?>
						<img src="./rot.jpg" alt="weiblich" width="30" height="30">
					<?php
					} 
				else 
					{
					?>
						<img src="./weiss.jpg" alt="mänlich" width="30" height="30">
					<?php
					}
					?>
			</td>


			</tr>		


<?php
$c++;
if($c==$top)
{
exit;
}
}
?>

</table>
<!--
<input type="button" onClick="location.href='../register'" value="Create an Account"></input>
-->
</body>
