<?php
	$company = mysql_query("SELECT `name` from `company` where `customer`=1");
	$type = mysql_query("SELECT * from `type`");
	switch($_GET['action'])
	{
		case "add":
			define_page("quotation_add");
			if($_POST['Submit'])
			{
				$date = $_POST['date'];
				$customer = $_POST['customer'];
				$company = $_POST['from_company'];
				$uid = $_SESSION[$_SESSION['rand'].'_id'];
				mysql_query("INSERT into `quotation` (`qid`,`company`,`customer`,`date`,`operator`)VALUES(NULL, $company,(SELECT `id` from `company` where `customer`=1 and `name` like '$customer'),'$date',$uid)");
			}
		break;
		case "item":

		$qid = $_GET['qid'];
		
		if($_POST['Save'])
		{
			define_page("quotation_edit");
			auth_period();
			mysql_query("DELETE  from `quotation_item` where `qid`=$qid");
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
			
			mysql_query("UPDATE `quotation` set `attn`='$customer_attn', `phone`='$customer_number', `email`='$customer_email', remark='$remark', `sales_mobile`='$sales_mobile' where `qid`='$qid'");
			foreach($name as $i => $value)
			{
				$p = str_replace('$', '', $price[$i]);
				$p = str_replace(',', '', $p);
				$des = $description[$i];
				$ver_v = $ver[$i];
				mysql_query("INSERT into `quotation_item` VALUES(NULL,$qid,'$value','$des','$ver_v',$qty[$i],$p)");
			}
		}
			define_page("quotation_item");
			$db = mysql_query("SELECT * from `quotation_item` where `qid`=$qid ORDER BY `qi_id` ASC");
			$base_info = mysql_query("SELECT `q`.`qid`,`q`.`date`,`cs`.`name`,`f_c`.`prefix`,`u`.`name`,`q`.`attn`,`q`.`phone`,`q`.`email`,`q`.`remark`,`q`.`sales_mobile` from `quotation` `q`,`company` `cs`,`type` `f_c`,`user` u where `u`.`id`=`q`.`operator` and `q`.`customer` = `cs`.`id` AND `q`.`company` = `f_c`.`id` and `qid`=$qid");
			$base_row = mysql_fetch_array($base_info);
		
		break;
		case "edit":
			define_page("quotation_edit");
			auth_period();
			$qid = $_GET['qid'];
			
			if($_POST['Submit'])
			{
				$date = $_POST['date'];
				$customer = $_POST['customer'];
				$company = $_POST['from_company'];
				mysql_query("UPDATE `quotation` set `date` = '$date' , `customer`= (SELECT `id` from `company` where `supplier`!=1 and `name` like '$customer'), `company`=$company where `qid`=$qid");
			}
			$db = mysql_query("SELECT `q`.`date`,`cs`.`name`,`f_c`.`id` from `quotation` `q`,`company` `cs`,`type` `f_c` where `q`.`customer` = `cs`.`id` AND `q`.`company` = `f_c`.`id` AND `q`.`qid`=$qid");
			$row = mysql_fetch_array($db);
		break;
		case "delete":
			define_page("quotation_delete");
			auth_period();
			if($_GET['qid'])
			{
				$id = $_GET['qid'];
				mysql_query("DELETE from `quotation` where `qid`=$id");
				mysql_query("DELETE from `quotation_item` where `qid`=$id");
			}
		default:
		if(permitedAuthor('quotation_viewSelfOnly'))
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
			 $query .= ($_GET['qid']?"and q.qid='".$id."'":'');
			 $query .= ($_GET['from_company']?"and q.company='".$_GET['from_company']."'":'');
			 $query .= ($_GET['s_date']?"and q.date>='".$_GET['s_date']."'":" and q.date >= DATE_FORMAT( NOW( ) ,  '%Y-%m' ) ");
			 $query .= ($_GET['e_date']?"and q.date<='".$_GET['e_date']."'":'');
			}

 			$db = mysql_query("SELECT `q`.`qid`,`q`.`date`,`q`.`operator`,`cs`.`name`,`f_c`.`name`,`f_c`.`prefix` from `quotation` `q`,`company` `cs`,`type` `f_c` where `q`.`customer` = `cs`.`id` AND `q`.`company` = `f_c`.`id` $query $only ORDER BY `q`.`date` DESC LIMIT $limit[0],$limit[1]");
		break;
	}
	include( dirname(dirname(__FILE__)).'/template/system/quotation.html');
?>
