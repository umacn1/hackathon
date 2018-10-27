<?php
	switch($_GET['action'])
	{
		case "add":
			define_page("supplier_add");
			if($_POST[Submit])
			{
				$name=$_POST['name'];
				$address=$_POST['address'];
				$website=$_POST['website'];
				$email=$_POST['email'];
				$tel=$_POST['tel'];
				$fax=$_POST['fax'];
				$attn=$_POST['attn'];
				$initial=$_POST['initial'];
				$customer=$_POST['customer'];
				$supplier=$_POST['supplier'];
				$payment=$_POST['payment'];
				$db = mysql_query("INSERT into `company` VALUES(NULL,'$name','$address','$email','$tel','$fax','$initial','$attn','$payment','$website','$customer','$supplier')");
			}
		break;
		case "detail":
			define_page("supplier_detail");
			$id = $_GET['id'];
			$db = mysql_query("SELECT * from `company` where id='$id'");
			$row = mysql_fetch_array($db,MYSQL_ASSOC);
		break;
		case "edit":
			define_page("company_edit");
			$id=$_GET['id'];
			if($_POST['Submit'])
			{
                                $name=$_POST['name'];
                                $address=$_POST['address'];
                                $website=$_POST['website'];
                                $email=$_POST['email'];
                                $tel=$_POST['tel'];
                                $fax=$_POST['fax'];
                                $attn=$_POST['attn'];
                                $initial=$_POST['initial'];
                                $customer=$_POST['customer'];
                                $supplier=$_POST['supplier'];
                                $payment=$_POST['payment'];
				$db=mysql_query("UPDATE `company` set `name`='$name',`address`='$address',`website`='$website',`email`='$email',`tel`='$tel',`fax`='$fax',`attn`='$attn',`initial`='$initial',`customer`='$customer',`supplier`='$supplier',`payment`='$payment' where id='$id'");
				echo "<script>history.go(-2)</script>";
			}else{
			 	$db = mysql_query("SELECT * from `company` where id='$id'");
			 	$row = mysql_fetch_array($db,MYSQL_ASSOC);
			     }
		break;
		case "delete":
			define_page("supplier_delete");
			$id = $_GET['id'];
			$db = mysql_query("DELETE from `company` where id='$id'");
			echo "<script>history.go(-1)</script>";	
		break;
		default:
			define_page("supplier_view");
			$num = $_GET['num'];
			$num == 0 ? $num=1 : $page=$num;
			$limit[0] = 0+30*($num-1);
			$limit[1] = 30*$num;
			$total = sqlTotal("company","where `supplier` = 1");
			$option = mysql_query("SELECT `name` from `company` where `supplier`=1");
			if($_POST['Search'])
			{
				$name=$_POST['name'];
				$attn=$_POST['attn'];
				$db = mysql_query("SELECT * from `company` where `name` like '%$name%' and `attn` like '%$attn%' and `supplier` =1  LIMIT $limit[0] , $limit[1]");

			}else{
				$db = mysql_query("SELECT * from `company`  where `supplier`=1 LIMIT $limit[0] , $limit[1]");
			     }
			$total = mysql_num_rows($db);
		break;
	}


include( dirname(dirname(__FILE__)).'/template/system/supplier.html');

?>
