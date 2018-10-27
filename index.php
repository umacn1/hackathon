<?php
session_start();
include("./function/common.php");
if(1) //reserved for login check
{
	if($_GET['page']=="logout")logout();
	include("./template/header.html");
	include("./inc/config.php");

	switch($_GET['page'])
	{
         default:
                include("./system/main.php");
         break;
	}

	include("./template/footer.html");
}else{
	}
?>
