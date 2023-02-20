<!--
<frameset rows="186,*">
  <frame src="index.php" frameborder="0">
  <frame src="index2.php" frameborder="0">
 <frameset cols="100%">
 </frameset>
</frameset>
-->

<?php
include "../config.php";
$http = "http://";
$guildicon = ":8080/Account/Services/GuildIcon";
$guildiconredirect = $http.$ServerIP.$guildicon;

include "../status.php";
?>

<div class="indexbg">

<iframe width="100%" height="2160" src="<?php echo $guildiconredirect; ?>" frameborder="0"></iframe>

</div>

