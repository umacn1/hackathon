<?php
$db_host = 'localhost';
$db_user = 'supercyber_hack';
$db_pass = 'nLTt7cxd5';
$db_name = 'supercyber_hack';
$con = mysql_connect($db_host,$db_user,$db_pass);
mysql_query("SET NAMES utf8"); 
mysql_query("SET CHARACTER_SET_CLIENT=utf8");
mysql_query("SET CHARACTER_SET_RESULTS=utf8"); 
mysql_select_db($db_name);
?>
