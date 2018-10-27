<?php
	switch($_GET['action'])
	{
		case "add":
			define_page("rec_add");
			if($_POST['Submit'])
			{
		 	 if($_POST['date'] && $_POST['customer'] && $_POST['invoice_id'][0] && $_POST['cheque_no'] && $_POST['amount'])
			 {
				$date = $_POST['date'];
				$customer = $_POST['customer'];
				$amount = $_POST['amount'];
				$cheque_no = $_POST['cheque_no'];
				$remark = $_POST['remark'];
				$uid = $_SESSION[$_SESSION['rand'].'_id'];
				foreach($_POST['invoice_id'] as $value)
				{
					$tmp= remainPayment("receive",$value);
        				$prefix = ereg_replace("[0-9]","",$value);
        				$value = ereg_replace("[a-zA-Z]","",$value);
					//$prefix = substr($value,0,1);
					//$value = substr($value,1);
				  	if($amount >0)
						if(bccomp($amount,$tmp,2) !=-1)
						{
				 			$db = mysql_query("INSERT into `receive` VALUES(NULL,(SELECT `id` from `company` where `name`='$customer' and `customer`=1),'$value',$tmp,'$cheque_no','$remark','$date',(SELECT id from type where prefix='$prefix'),'$uid')");
							$rec_id = mysql_insert_id();
							$db = mysql_query("UPDATE `invoice` set paid=1,pay_date=NOW(),rec_id='$rec_id' where id='$value' and invoice.company=(SELECT id from type where prefix='$prefix')");
						}
						else{
							$db = mysql_query("INSERT into `receive` VALUES(NULL,(SELECT `id` from `company` where `name`='$customer' and `customer`=1),'$value',$amount,'$cheque_no','$remark','$date',(SELECT id from type where prefix='$prefix'),'$uid')");
							$rec_id = mysql_insert_id();
							$db = mysql_query("UPDATE `invoice` set pay_date=NOW(),rec_id='$rec_id' where id='$value' invoice.company=(SELECT id from type where prefix='$prefix')");
					    	}
					else
						break;
					$amount -=$tmp;
					
				}
				echo "<script>history.go(-2);</script>";
			 }
				error("Missing field(s)");
			}
			$company = mysql_query("SELECT `name` from `company` where `customer`=1");
   		 	     
		break;
		case "delete":
			define_page("rec_delete");
			auth_period();
			$id = $_GET['id'];
			$db = mysql_query("SELECT invoice_no,company from `receive` where id='$id'");
			$row = mysql_fetch_array($db,MYSQL_NUM);
			$period = (PERIOD>=0? "and DATEDIFF(DATE_FORMAT( NOW( ) ,  '%Y-%m-%d' ),`date`)<=".PERIOD:'');
			$db = mysql_query("UPDATE invoice set rec_id=NULL ,paid=0 ,pay_date='0000-00-00' where id='$row[0]' and company='$row[1]' $period");
			$db = mysql_query("DELETE from `receive` where id='$id' $period");
			echo "<script>history.go(-1);</script>";
		break;
		case "edit":
			define_page("rec_edit");
			auth_period();
			$id = $_GET['id'];
			if($_POST['Submit'])
			{
				$remark = $_POST['remark'];
				$db = mysql_query("UPDATE `receive` set `remark`='$remark' where id='$id'");
				echo "<script>history.go(-2);</script>";
			}else{
				$db = mysql_query("SELECT `remark` from `receive` where id='$id'");
				$row = mysql_fetch_row($db);
			     }
		break;
		default:
			define_page("rec_view");
                        $num = $_GET['num'];
                        $num == 0 ? $num=1 : $page=$num;
                        $limit[0] = 0+30*($num-1);
                        $limit[1] = 30;

			$company = mysql_query("SELECT `name` from `company` where `customer`=1");
	
			if($_GET['Search'])
			{
			 $limit[0] = 0;
			 $limit[1] = 999999;
			 $query='';
			 $query .= ($_GET['name']? "and company.name='".$_GET['name']."'" :'');
			 $query .= ($_GET['from_company']? "and type.id='".$_GET['from_company']."'" :'');
			 $query .= ($_GET['id']?"and receive.id='".$_GET['id']."'":'');
			 $query .= ($_GET['s_date']?"and date>='".$_GET['s_date']."'":'');
			 $query .= ($_GET['e_date']?"and date<='".$_GET['e_date']."'":'');
			 $query .= ($_GET['invoice_id']?"and receive.invoice_no='".$_GET['invoice_id']."'":'');

			}
			 $db = mysql_query("SELECT SQL_CALC_FOUND_ROWS receive.id,company.name,receive.date,concat(type.prefix,receive.invoice_no),receive.cheque_no,receive.remark,receive.amount from `receive` LEFT JOIN `type` on receive.company = type.id LEFT JOIN `company` on receive.customer=company.id where receive.id>=0 $query ORDER BY receive.id DESC LIMIT $limit[0],$limit[1]");

                         $total_db = mysql_query("SELECT FOUND_ROWS()");
                         $total = mysql_fetch_array($total_db,MYSQL_NUM);
                         $total = $total[0];
			 $type = mysql_query("SELECT * from `type`");
		break;
	}
	include( dirname(dirname(__FILE__)).'/template/system/rec.html');
?>