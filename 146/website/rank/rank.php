<?php
//=================================================================================================
//    This file is part of "Universal Mysql Top 100 Table".
//
//    "Universal Mysql Top 100 Table" is free software: you can redistribute it and/or modify
//    it under the terms of the GNU General Public License as published by
//    the Free Software Foundation, either version 3 of the License, or
//    (at your option) any later version.
//
//    "Universal Mysql Top 100 Table" is distributed in the hope that it will be useful,
//    but WITHOUT ANY WARRANTY; without even the implied warranty of
//    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  
//    See the GNU General Public License for more details.
//
//    You should have received a copy of the GNU General Public License
//    along with Foobar.  If not, see <http://www.gnu.org/licenses/>.
//=================================================================================================
//Made by LegalSin aka NIMDA from Dragon-Network if you like this or you find it usful give a like on ragezone.



//==============Settings==================
//$MySQL_HOST = "localhost";            														//Your mysql host ip addres
//$MySQL_User = "root"; 																		//Your mysql user
//$MySQL_Password = "root";  																	//Your mysql password
//$MySQL_Database = "dbo";																	//Your mysql database
//$MySQL_max_rows = 50; //this is now obtained from ../config.php								//Max rows that will be displayed (please note that you only have 5 picture from 1to 50 if you want to make top 200 please modifiy the links at the battom of the page)
include "../config.php";
// get the following from ../config.php
$MySQL_HOST = $DB_Host;
$MySQL_User = $DB_User;
$MySQL_Password = $DB_Password;
$MySQL_Database = $DB_Name;
$MySQL_max_rows = $top; 																		//Max rows that will be displayed (please note that you only have 5 picture from 1to 50 if you want to make top 200 please modifiy the links at the battom of the page)
//
$MySQL_Table = "uwebplayers";																//Your mysql tabele where you keep the pk rank
$Top_table_TITLES = " Character Name , Level , Sex , Class , Reputation , PK-Status ";				//List of your Titles ie: Name,Level,Reputation.
$Top_table_MySQL_SEARCH = "rolename,rolelevel,rolegender,roleprof,rolerep,redname";				//List Criteria that you will select from database ie:name,reputation,userid,level,
$MySQl_ignore_row = "rolename";  																//Row name of your table to ignore if they use bad words
$MySQl_Class_row = "roleprof";  																//Row name that helds occupation info this is for the picture swich
$MySQl_Gender_row = "rolegender"; 																//Row name that helds gender info this is for the picture swich
$MySQl_pk_row = "redname";
//$ignore = array("NIMDA", "BITCH", "CUNT", "WHORE", "SLUT", "PUSSY", "GHOST"); 				//List of ignore words
$mySQL_default_sort_row = "rolelevel"; 															//Default row to sort when the page loads
$Table_width = 950;																			//table width
$Table_height = 710; 																		//table height
//$jquey_link = "http://www.rotfa.dragon-network.co/WEB-INF/scripts/jquery-1.js";       		//Link to jquey
//============Functions================
$Link = MySQL_Connect($MySQL_HOST, $MySQL_User, $MySQL_Password) or die ("<HTML><BODY><center><b><font color=white>The server is momentarily down please try again after 5 minutes!</b></font></BODY></HTML>");
MySQL_Select_Db($MySQL_Database, $Link) or die ("<HTML><BODY><center><b><font color=white>The server is momentarily down please try again after 5 minutes!</b></font></BODY></HTML>");
function numberToRoman($num) 
{
     $n = intval($num);
     $result = '';
     $lookup = array('M' => 1000, 'CM' => 900, 'D' => 500, 'CD' => 400,'C' => 100, 'XC' => 90, 'L' => 50, 'XL' => 40,'X' => 10, 'IX' => 9, 'V' => 5, 'IV' => 4, 'I' => 1);
     foreach ($lookup as $roman => $value) 
     {
         $matches = intval($n / $value);
         $result .= str_repeat($roman, $matches);
         $n = $n % $value;
     }
     return $result;
}
function occupation($strocu){

    switch ($strocu % 10) :
	case 0:	return "Blademaster.png";
	case 1:	return "Wizard.png";
	case 2:	return "Psychic.png";
	case 3:	return "Venomancer.png";
	case 4:	return "Barbarian.png";
	case 5:	return "Assassin.png";
	case 6:	return "Archer.png";
	case 7:	return "Cleric.png";
	case 8:	return "Seeker.png";
	case 9:	return "Mystic.png";
    endswitch;
}
function gender($strgen){
    switch ($strgen % 2) :
	case 0:	return "Male.png";
	case 1:	return "Female.png";
    endswitch;
}
function pk($strpk){
    switch ($strpk % 2) :
	case 0:	return "White.png";
	case 1:	return "Red.png";
    endswitch;
}
function SqlA($string) {
$string = strip_tags($string);
return $string;
} 
$Top_table_TITLES0 = explode(",", $Top_table_TITLES);
$Top_table_TITLES2 = explode(",", $Top_table_MySQL_SEARCH);
$ChunksS = explode(",", $Top_table_MySQL_SEARCH);
//========================================
print'<html>';
print'<head>';
print'<link rel="stylesheet" href="WEB-INF/table.css" type="text/css"/>	';
print'</head>';
print'<body>';
print'<center>';
print'<script src="'.$jquey_link.'" type="text/javascript"></script>';
print'<script type="text/javascript">';
print'$(function(){';
print'$(this).bind("contextmenu",function(e){e.preventDefault();});';
print'});';
print'</script>';
print'<div class="TopPlayersDiv" style="width:'.$Table_width.'px;height:'.$Table_height.'px;">';
print'<table cellpadding="0" cellspacing="0">';
print'<script language="JavaScript" type="text/javascript">function dncp (parm,lo,hi){document.dragonnetwork.parm.value = parm;document.dragonnetwork.hi.value = hi;document.dragonnetwork.lo.value = lo;document.dragonnetwork.submit();}</script><form name="dragonnetwork" action="rank.php" method="post"><input type="hidden" name="parm" /><input type="hidden" name="hi" /><input type="hidden" name="lo" /></form>';
if(isset($_POST["parm"])){$parm = SqlA($_POST["parm"]);}else{$parm = $mySQL_default_sort_row;}
if(isset($_POST["hi"])){$hi = $_POST["hi"];}else{$hi = "20";}
if(isset($_POST["lo"])){$lo = $_POST["lo"];}else{$lo = "1";}
$Result = MySQL_Query("SELECT $Top_table_MySQL_SEARCH FROM $MySQL_Table ORDER BY $parm DESC") or die ("<HTML><BODY><center><b><font color=white>The server is momentarily down please try again after 5 minutes!</b></font></BODY></HTML>");
print'<tr>';
print'<td style="background:url() no-repeat 99% 2px #111828;">Rank</td>';
for($i = 0; $i < count($Top_table_TITLES0); $i++){
print'<td style="cursor: pointer;"onclick="javascript:dncp(\''.$Top_table_TITLES2[$i].'\',\''.$lo.'\',\''.$hi.'\');">'.$Top_table_TITLES0[$i].'</td>';
}
print'</tr>';
$num = 0;
while ($row = MySQL_Fetch_Array($Result))
{
$num++;
$where = $num;
	if ($where <= $hi && $where >= $lo){
	print'<tr>';
	print'<td >'.numberToRoman($num).'</td>';
	$name = str_replace("\"", "", $row[$MySQl_ignore_row]);
	$ocupation = occupation($row[$MySQl_Class_row]);
	$ocupationtit = str_replace(".png", "", $ocupation);
	$gender = gender($row[$MySQl_Gender_row]);
	$gendertit = str_replace(".png", "", $gender);
	$pk = pk($row[$MySQl_pk_row]);
	$pktit = str_replace(".png", "", $pk);
		for($x = 0; $x < count($ChunksS); $x++){
//		if (in_array(strtoupper($name),$ignore)){}else{if($MySQl_Class_row == $ChunksS[$x]){print'<td><img src="WEB-INF/img/'.$ocupation.'" title="'.$ocupationtit.'" alt="'.$ocupationtit.'" height="17" width="17" /></td>';}elseif($MySQl_Gender_row == $ChunksS[$x]){print'<td><img src="WEB-INF/img/'.$gender.'" title="'.$gendertit.'" alt="'.$gendertit.'" height="17" width="17" /></td>';}else{print'<td><b>'.$row[$ChunksS[$x]].'</b></td>';}}
		if (in_array(strtoupper($name),$ignore)){}else{if($MySQl_Class_row == $ChunksS[$x]){print'<td><img src="WEB-INF/img/'.$ocupation.'" title="'.$ocupationtit.'" alt="'.$ocupationtit.'" height="17" width="17" /></td>';}elseif($MySQl_Gender_row == $ChunksS[$x]){print'<td><img src="WEB-INF/img/'.$gender.'" title="'.$gendertit.'" alt="'.$gendertit.'" height="17" width="17" /></td>';}elseif($MySQl_pk_row == $ChunksS[$x]){print'<td><img src="WEB-INF/img/'.$pk.'" title="'.$pktit.'" alt="'.$pktit.'" height="17" width="17" /></td>';}else{print'<td><b>'.$row[$ChunksS[$x]].'</b></td>';}}
		}
	print'</tr>';	
	}
if ($num >= $MySQL_max_rows) break;
}
mysql_close($Link);
print'</table>';
print'</div>';
print'<div class="menu" style="width:950px;height:20px;"><center>';
//Links edit it
print'<a href="javascript:dncp(\''.$parm.'\',\'1\',\'20\');"><img src="WEB-INF/img/1.png" title="1" alt="1" height="20" width="20" /></a>';
print'<a href="javascript:dncp(\''.$parm.'\',\'20\',\'40\');"><img src="WEB-INF/img/2.png" title="2" alt="2" height="20" width="20" /></a>';
print'<a href="javascript:dncp(\''.$parm.'\',\'40\',\'60\');"><img src="WEB-INF/img/3.png" title="3" alt="3" height="20" width="20" /></a>';
print'<a href="javascript:dncp(\''.$parm.'\',\'60\',\'80\');"><img src="WEB-INF/img/4.png" title="4" alt="4" height="20" width="20" /></a>';
print'<a href="javascript:dncp(\''.$parm.'\',\'80\',\'100\');"><img src="WEB-INF/img/5.png" title="1" alt="1" height="20" width="20" /></a>';
print'</center></div>';

print'</center>';
print'</body>';
print'</html>';
?>