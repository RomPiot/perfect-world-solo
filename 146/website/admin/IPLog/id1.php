<head>	
	<link rel="stylesheet" type="text/css" href=".aussehen.css">
	<meta http-equiv="refresh" content="30" >
</head>

<body bgcolor="#FFFFFF" scroll='no'  width="100%" height="100%">
<?php
include_once "index.php";
include "../../config.php";
		$result = mysql_query("SELECT * FROM users ORDER BY ID ASC")or die(mysql_error());
		$row = mysql_fetch_assoc($result);
		$z=1;
		$c=1;
?>
<center>Sorted by <font color="red"><b>ID</b></font></center><br><hr><br>
<table border="1" cellpadding="1" cellspacing="1" style="width=1200" align="center">
	<tbody>
		<tr>
			<td scope="col" width="15">
			<p align="center">
				<b>#</b></p></td>
			<td scope="col" width="50">
				<p align="center">
				<b><a href="id2.php"><font color="red">ID</a></font></b></p></td>
			<td scope="col" width="110">
				<p align="center">
				<b>Account Name</b></p></td>
			<td scope="col" width="150">
				<p align="center">
				<b>IP Address</b></p></td>
			</tr>
		<tr>
			<td>
			<p align="center">
				<?php 
				echo $z;
				$z++
				?>
				</p>
			</td>
			<td>
			<p align="center">
				<?php 
				echo $row['ID'];
				?>
				</p>
			</td>
			<td>
			<p align="center">
				<?php 
				echo $row['name'];
				?>
				</p>
			</td>
			<td>
			<p align="center">
					<?php 
				echo $row['idnumber'];
				?>
					</p>
			</td>	
			</tr>
<?php
while($row = mysql_fetch_assoc($result))
{
?>
				<tr>
			<td>
			<p align="center">
				<?php 
				echo $z;
				$z++
				?>
				</p>
			</td>
			<td>
			<p align="center">
				<?php 
				echo $row['ID'];
				?>
				</p>
			</td>
			<td>
			<p align="center">
				<?php 
				echo $row['name'];
				?>
					</p>
			</td>
			<td>
			<?php 
				echo $row['idnumber'];
				?>
					</p>
			</td>	
			</tr>		
<?php
$c++;
}
?>
</table>
</body>