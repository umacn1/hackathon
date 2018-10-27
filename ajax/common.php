<?php
session_start();
	if($_SESSION[$_SESSION['rand'].'_id'] && $_SESSION[$_SESSION['rand'].'_account'] && $_SESSION[$_SESSION['rand'].'_username'] && $_SESSION[$_SESSION['rand'].'_author'])
{
	include("../inc/config.php");
	include("../function/common.php");
	switch($_GET['type'])
	{
		case "e_rec":
		case "rec":
			$name = $_GET['name'];
			if($_GET['type']=="rec")
			$db = mysql_query("SELECT invoice.id,invoice.amount,type.prefix from invoice,type where invoice.company=type.id and customer=(SELECT id from company where name='$name') and invoice.paid=0");
			else
			$db = mysql_query("SELECT expe.id,expe.amount,type.prefix from expe,type where expe.company=type.id and supplier=(SELECT id from company where name='$name') and paid=0");
			for($i=0;$row = mysql_fetch_array($db,MYSQL_NUM);$i++)
			{
			 $array[$i][0] = $row[2].$row[0];
			 $array[$i][1] = remainPayment($_GET['type']."eive",$row[2].$row[0]);
			}
			echo json_encode($array);
		break;
		case "invoiceid":
			$id = $_GET['id'];
			$cat = $_GET['cat'];
			$db = mysql_query("SELECT id from $cat where company='$id' and id<100000 ORDER BY id DESC");
			$row = mysql_fetch_array($db,MYSQL_NUM);
			$row[0] +=1;
			echo json_encode($row);
		break;
		case "contentLevel":
				$contentType = $_GET['contentType'];
				$cId = $_GET['id'];
				$db = mysql_query("SELECT `c`.`id`,`c`.`name` from `relationContent` r,`content` c where `r`.`parent`='$cId' and `c`.`type`='$contentType' and `r`.`child`=`c`.`id`");
				for($i=0;$row = mysql_fetch_array($db,MYSQL_NUM);$i++)
				{
			 		$array[$i][0] = $row[0];
			 		$array[$i][1] = $row[1];
				}
			echo json_encode($array);
		break;
	}
}
?>
