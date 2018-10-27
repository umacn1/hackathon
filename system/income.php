<?php
	switch($_GET['action'])
	{
		case "add":
			define_page("income_add");
			if($_POST['Submit'])
			{
		 	 if($_POST['id'] && $_POST['date'] && $_POST['customer'] && $_POST['amount'] &&$_POST['from_company'])
			 {
				$id = $_POST['id'];
				$date = $_POST['date'];
				$quotation = $_POST['quotation'];
				$customer = $_POST['customer'];
				$amount = $_POST['amount'];
				$type = $_POST['type'];
				$from_company = $_POST['from_company'];
				$paid = $_POST['paid'];
				$content = $_POST['content'];
				$remark = $_POST['remark'];
				$uid = $_SESSION[$_SESSION['rand'].'_id'];
				$db = mysql_query("SELECT id from `invoice` where id='$id' and company='$from_company'");
				if(mysql_num_rows($db) ==0)
				 $db = mysql_query("INSERT into `invoice` VALUES($id,'$quotation',(SELECT `id` from `company` where `name`='$customer' and `customer`=1),'$amount','$type','$from_company','$content','$paid','$remark',NULL,'$date','$uid','0000-00-00',0)");
				else
				 error("Duplicate invoice id");
                        	//echo "<script>history.go(-2);</script>";
			 }else
				error("Missing field(s)");
			}
			$type = mysql_query("SELECT * from `type`");
			$company = mysql_query("SELECT `name` from `company` where `customer`=1");
 			$content = mysql_query("SELECT * from `content` where `type`!='expe'");
   		 	     
		break;
		case "detail":
			define_page("income_detail");
                        $prefix = ereg_replace("[0-9]","",$_GET['id']);
                        $id = ereg_replace("[a-zA-Z]","",$_GET['id']);
			//$prefix = substr($_GET['id'],0,1);
			//$id = substr($_GET['id'],1);
			$db = mysql_query("SELECT invoice.id,company.name,invoice.date,invoice.amount,invoice.`type`,invoice.remark,(SELECT name from content where id=invoice.content),invoice.paid,invoice.quotation_no from `company`,`invoice` where  invoice.customer=company.id and invoice.id='$id' and invoice.company=(SELECT id from type where prefix='$prefix') ");
			$row = mysql_fetch_array($db,MYSQL_NUM);
		break;
		case "edit":
			define_page("income_edit");
			auth_period();
                        $prefix = ereg_replace("[0-9]","",$_GET['id']);
                        $id = ereg_replace("[a-zA-Z]","",$_GET['id']);
			//$prefix = substr($_GET['id'],0,1);
			//$id = substr($_GET['id'],1);
			if($_POST['Submit'])
                        {
                        if($_POST['date'] && $_POST['customer'] && $_POST['from_company']){
                                $date = $_POST['date'];
				$quotation = $_POST['quotation'];
                                $customer = $_POST['customer'];
                                $amount = $_POST['amount'];
                                $type = $_POST['type'];
                                $from_company = $_POST['from_company'];
                                $paid = $_POST['paid'];
                                $content = $_POST['content'];
                                $remark = $_POST['remark'];
				$period = (PERIOD>=0? "and DATEDIFF(DATE_FORMAT( NOW( ) ,  '%Y-%m-%d' ),`date`)<=".PERIOD:'');

				$db = mysql_query("UPDATE `invoice` set date='$date',quotation_no='$quotation', customer=(SELECT id from `company` where name='$customer' and `customer`=1),amount='$amount',type='$type',company='$from_company',paid='$paid',content='$content',remark='$remark' where id='$id' and invoice.company=(SELECT id from type where prefix='$prefix') $period");
				echo "<script>history.go(-2);</script>";
			}else
				error("Missing field(s)");
			}else{
				$db = mysql_query("SELECT invoice.id,company.name,invoice.date,invoice.amount,invoice.`type`,invoice.remark,invoice.content,invoice.paid,invoice.customer,invoice.quotation_no,invoice.company from `company`,`invoice` where invoice.customer=company.id and invoice.id='$id'");
                        	$row = mysql_fetch_array($db,MYSQL_NUM);
				$type = mysql_query("SELECT * from `type`");
                        	$company = mysql_query("SELECT `name` from `company` where `customer`=1");
                        	$content = mysql_query("SELECT * from `content` where `type`!='expe'");
			     }
		break;
		case "delete":
			define_page("income_delete");
			auth_period();
                        $prefix = ereg_replace("[0-9]","",$_GET['id']);
                        $id = ereg_replace("[a-zA-Z]","",$_GET['id']);
			$period = (PERIOD>=0? "and DATEDIFF(DATE_FORMAT( NOW( ) ,  '%Y-%m-%d' ),`date`)<=".PERIOD:'');
			$db = mysql_query("DELETE from `invoice` where id='$id' and invoice.company=(SELECT id from type where prefix='$prefix') $period");
			echo "<script>history.go(-1);</script>";
		break;
		case "sign":
                        define_page("income_sign");
			auth_period();
                        $prefix = ereg_replace("[0-9]","",$_GET['id']);
                        $id = ereg_replace("[a-zA-Z]","",$_GET['id']);
			$period = (PERIOD>=0? "and DATEDIFF(DATE_FORMAT( NOW( ) ,  '%Y-%m-%d' ),`date`)<=".PERIOD:'');
                        $db = mysql_query("UPDATE `invoice` set sign=(!sign) where id='$id' and invoice.company=(SELECT id from type where prefix='$prefix') $period");
			echo "<script>history.go(-1)</script>";
		break;
		default:
			define_page("income_view");
        if(permitedAuthor('income_viewSelfOnly'))
		{
			$only .= "and invoice.operator='".$_SESSION[$_SESSION['rand'].'_id']."'";
		}
                        $num = $_GET['num'];
                        $num == 0 ? $num=1 : $page=$num;
                        $limit[0] = 0+30*($num-1);
                        $limit[1] = 30;

			$company = mysql_query("SELECT `name` from `company` where `customer`=1");
			$type = mysql_query("SELECT * from `type`");
			$content = mysql_query("SELECT * from `content` where `type` !='expe'");	
			if($_GET['Search'])
			{
                         $limit[0] = 0;
                         $limit[1] = 9999999999;
			 $query='';
			 $query .= ($_GET['name']? "and company.name='".$_GET['name']."'" :'');
			 $query .= ($_GET['id']?"and invoice.id='".$_GET['id']."'":'');
			 $query .= ($_GET['type']!=''?"and invoice.type='".$_GET['type']."'":'');
                         $query .= ($_GET['content']!=''?"and invoice.content='".$_GET['content']."'":'');
			 $query .= ($_GET['from_company']?"and invoice.company='".$_GET['from_company']."'":'');
			 $query .= ($_GET['paid']!=''?" and invoice.paid='".$_GET['paid']."'":'');
			 $query .= ($_GET['s_date']?"and date>='".$_GET['s_date']."'":" and date >= DATE_FORMAT( NOW( ) ,  '%Y-%m' ) ");
			 $query .= ($_GET['e_date']?"and date<='".$_GET['e_date']."'":'');
			}
			$db = mysql_query("SELECT SQL_CALC_FOUND_ROWS type.name,CONCAT(type.prefix,invoice.id),company.name,invoice.date,invoice.amount,invoice.`type`,invoice.remark,invoice.pay_date,invoice.paid,invoice.sign,`content`.`name` from `company`,`type`,`invoice`,`content` where `content`.`id` = `invoice`.`content` and `content`.`type`!='expe' and type.id=invoice.company and invoice.customer=company.id $query $only ORDER BY invoice.id DESC LIMIT $limit[0],$limit[1]");
			$total_db = mysql_query("SELECT FOUND_ROWS()");
                        $total = mysql_fetch_array($total_db,MYSQL_NUM);
			$total = $total[0];
		break;
	}
	include( dirname(dirname(__FILE__)).'/template/system/income.html');
?>