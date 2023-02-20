<?php
include "../config.php";
$http = "http://";
$admin = ":8080/pwAdmin";
$adminredirect = $http.$LanIP.$admin;
header('Location:'.$adminredirect);
?>