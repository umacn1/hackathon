<?php
	$company = mysql_query("SELECT `name` from `company` where `supplier`!=1");
	$type = mysql_query("SELECT * from `type`");

	switch($_GET['action'])
	{
		case "add":
			define_page("receipt_add");
			if($_POST['Submit'])
			{
				$prefix = ereg_replace("[0-9]","",$_POST['quotation']);
                $quotation_id = ereg_replace("[a-zA-Z]","",$_POST['quotation']);

				$db = mysql_query("SELECT * from `quotation`,`type` where `qid`=$quotation_id and `company`= (SELECT `id` from `type` where `prefix`='$prefix')");
				$record = mysql_fetch_array($db);
				
				$db2 = mysql_query("SELECT * from `quotation_item` where `qid`=$quotation_id");
				
				
				$date = $_POST['date'];
				$customer = $record['customer'];
				$company = $record['company'];
				$attn = $record['attn'];
				$phone= $record['phone'];
				$email= $record['email'];
				$title= $record['title'];
				$sales_mobile= $record['sales_mobile'];
				$receipt_num = date('Ymdhis');
				$uid = $_SESSION[$_SESSION['rand'].'_id'];
				$db3 = mysql_query("INSERT into `receipt` (`rid`,`company`,`receipt_num`,`customer`,`attn`,`phone`,`email`,`title`,`date`,`operator`,`sales_mobile`)VALUES(NULL, $company,'$receipt_num',$customer,'$attn','$phone','$email','$title','$date',$uid,'$sales_mobile')");
				$receipt_id = mysql_insert_id();

				while($record2 = mysql_fetch_array($db2))
				{
					$name = $record2['name'];
					$description = $record2['description'];
					$ver = $record2['ver'];
					$quantity = $record2['quantity'];
					$price = $record2['price'];
					mysql_query("INSERT into `receipt_item` VALUES(NULL,$receipt_id,'$name','$description','$ver',$quantity,$price)");
				}
			}
		break;
		case "item":

		$qid = $_GET['qid'];
		
		if($_POST['Save'])
		{
			define_page("receipt_edit");
			auth_period();
			mysql_query("DELETE  from `receipt_item` where `rid`=$qid");
			$name = $_POST['name'];
			$qty = $_POST['qty'];
			$price = $_POST['unitprice'];
			$description = $_POST['description'];
			$ver = $_POST['ver'];
			$sales_mobile = $_POST['sales_mobile'];
			
			$customer_attn=$_POST['customer_attn'];
			$customer_number=$_POST['customer_number'];
			$customer_email=$_POST['customer_email'];
			$remark = $_POST['remark'];
			$receivedTotal = $_POST['receivedTotal'];
			
			mysql_query("UPDATE `receipt` set `attn`='$customer_attn', `phone`='$customer_number', `email`='$customer_email', remark='$remark', `sales_mobile`='$sales_mobile', `receivedTotal`=$receivedTotal where `rid`='$qid'");
			foreach($name as $i => $value)
			{
				$p = str_replace('$', '', $price[$i]);
				$des = $description[$i];
				$ver_v = $ver[$i];
				mysql_query("INSERT into `receipt_item` VALUES(NULL,$qid,'$value','$des','$ver_v',$qty[$i],$p)");
			}
		}
			define_page("quotation_item");
			$db = mysql_query("SELECT * from `receipt_item` where `rid`=$qid ORDER BY `ri_id` ASC");
			$base_info = mysql_query("SELECT `q`.`rid`,`q`.`date`,`cs`.`name`,`f_c`.`prefix`,`u`.`name`,`q`.`attn`,`q`.`phone`,`q`.`email`,`q`.`remark`,`q`.`sales_mobile`,`q`.`receipt_num`, `q`.`receivedTotal` from `receipt` `q`,`company` `cs`,`type` `f_c`,`user` u where `u`.`id`=`q`.`operator` and `q`.`customer` = `cs`.`id` AND `q`.`company` = `f_c`.`id` and `rid`=$qid");
			$base_row = mysql_fetch_array($base_info);
		
		break;
		case "edit":
			define_page("receipt_edit");
			auth_period();
			$qid = $_GET['qid'];
			
			if($_POST['Submit'])
			{
				$date = $_POST['date'];
				$customer = $_POST['customer'];
				$company = $_POST['from_company'];
				mysql_query("UPDATE `receipt` set `date` = '$date' , `customer`= (SELECT `id` from `company` where `supplier`!=1 and `name` like '$customer'), `company`=$company where `rid`=$qid");
			}
			$db = mysql_query("SELECT `q`.`date`,`cs`.`name`,`f_c`.`id` from `receipt` `q`,`company` `cs`,`type` `f_c` where `q`.`customer` = `cs`.`id` AND `q`.`company` = `f_c`.`id` AND `q`.`rid`=$qid");
			$row = mysql_fetch_array($db);
		break;
		case "delete":
			define_page("receipt_delete");
			auth_period();
			if($_GET['qid'])
			{
				$id = $_GET['qid'];
				mysql_query("DELETE from `receipt` where `rid`=$id");
				mysql_query("DELETE from `receipt_item` where `rid`=$id");
			}
		default:
		if(permitedAuthor('receipt_viewSelfOnly'))
		{
			$only .= "and q.operator='".$_SESSION[$_SESSION['rand'].'_id']."'";
		}
		
		                $num = $_GET['num'];
                        $num == 0 ? $num=1 : $page=$num;
                        $limit[0] = 0+30*($num-1);
                        $limit[1] = 30;
						$prefix = ereg_replace("[0-9]","",$_GET['qid']);
                        $id = ereg_replace("[a-zA-Z]","",$_GET['qid']);
						
			if($_GET['Search'])
			{

			 $query='';
			 $query .= ($_GET['name']? "and `q`.`customer` = (SELECT `id` from `company` where `supplier`!=1 and `name` like '".$_GET['name']."')" :'');
			 $query .= ($_GET['qid']?"and q.rid='".$id."'":'');
			 $query .= ($_GET['from_company']?"and q.company='".$_GET['from_company']."'":'');
			 $query .= $_GET['s_date'];
			 $query .= $_GET['e_date'];
			}

 			$db = mysql_query("SELECT `q`.`rid`,`q`.`date`,`q`.`operator`,`cs`.`name`,`f_c`.`name`,`f_c`.`prefix`,`q`.`receipt_num` from `receipt` `q`,`company` `cs`,`type` `f_c` where `q`.`customer` = `cs`.`id` AND `q`.`company` = `f_c`.`id` $query $only ORDER BY `q`.`date` DESC LIMIT $limit[0],$limit[1]");
		break;
	}
	include( dirname(dirname(__FILE__)).'/template/system/receipt.html');
?>
