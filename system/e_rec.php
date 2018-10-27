<?php
	switch($_GET['action'])
	{
		case "add":
			define_page("e_rec_add");
			if($_POST['Submit'])
			{
		 	 if($_POST['date'] && $_POST['supplier'] && $_POST['invoice_id'][0] && $_POST['cheque_no'] && $_POST['amount'])
			 {
				$date = $_POST['date'];
				$supplier = $_POST['supplier'];
				$amount = $_POST['amount'];
				$cheque_no = $_POST['cheque_no'];
				$remark = $_POST['remark'];
				$uid = $_SESSION[$_SESSION['rand'].'_id'];
				foreach($_POST['invoice_id'] as $value)
				{
					$tmp= remainPayment("e_receive",$value);
					$prefix = ereg_replace("[0-9]","",$value);
        				$value = ereg_replace("[a-zA-Z]","",$value);
					//$prefix = substr($value,0,1);
					//$value = substr($value,1);
					if($amount >0)
						if(bccomp($amount,$tmp,2) !=-1)
						{
				 			$db = mysql_query("INSERT into `e_receive` VALUES(NULL,(SELECT `id` from `company` where `name`='$supplier' and `supplier`=1),'$value',$tmp,'$cheque_no','$remark','$date',(SELECT id from type where prefix='$prefix'),'$uid')");
							$rec_id = mysql_insert_id();
							$db = mysql_query("UPDATE `expe` set paid=1,rec_id='$rec_id' where id='$value' and expe.company=(SELECT id from type where prefix='$prefix')");
						}
						else{
							$db = mysql_query("INSERT into `e_receive` VALUES(NULL,(SELECT `id` from `company` where `name`='$supplier' and `supplier`=1),'$value',$amount,'$cheque_no','$remark','$date',(SELECT id from type where prefix='$prefix'),'$uid')");
							$rec_id = mysql_insert_id();
							$db = mysql_query("UPDATE `expe` set pay_date=NOW(),rec_id='$rec_id' where id='$value' expe.company=(SELECT id from type where prefix='$prefix')");
					   	    }
					else
						break;
					$amount -=$tmp;
					
				}
				//echo "<script>history.go(-2);</script>";
			 }else{
				error("Missing field(s)");
			      }
			}
			$company = mysql_query("SELECT `name` from `company` where `supplier`=1");
   		 	     
		break;
		case "delete":
			define_page("e_rec_delete");
			auth_period();
			$id = $_GET['id'];
			$db = mysql_query("SELECT invoice_no,company from `e_receive` where id='$id'");
			$row = mysql_fetch_array($db,MYSQL_NUM);
			$period = (PERIOD>=0? "and DATEDIFF(DATE_FORMAT( NOW( ) ,  '%Y-%m-%d' ),`date`)<=".PERIOD:'');
			$db = mysql_query("UPDATE expe set rec_id=NULL ,paid=0  where id='$row[0]' and company='$row[1]' $period");
			$db = mysql_query("DELETE from `e_receive` where id='$id' $period");
			echo "<script>history.go(-1);</script>";
		break;
                case "edit":
                        define_page("e_rec_edit");
                        auth_period();
                        $id = $_GET['id'];
                        if($_POST['Submit'])
                        {
                                $remark = $_POST['remark'];
                                $db = mysql_query("UPDATE `e_receive` set `remark`='$remark' where id='$id'");
                                echo "<script>history.go(-2);</script>";
                        }else{
                                $db = mysql_query("SELECT `remark` from `e_receive` where id='$id'");
                                $row = mysql_fetch_row($db);
                             }
                break;
		default:
			define_page("e_rec_view");
                        $num = $_GET['num'];
                        $num == 0 ? $num=1 : $page=$num;
                        $limit[0] = 0+30*($num-1);
                        $limit[1] = 30;

			$company = mysql_query("SELECT `name` from `company` where supplier=1");
	
			if($_GET['Search'])
			{
			 $limit[0] = 0;
			 $limit[1] = 9999999;
			 $query='';
			 $query .= ($_GET['name']? "and company.name='".$_GET['name']."'" :'');
			 $query .= ($_GET['from_company']? "and type.id='".$_GET['from_company']."'" :'');
			 $query .= ($_GET['id']?"and receive.id='".$_GET['id']."'":'');
			 $query .= ($_GET['s_date']?"and date>='".$_GET['s_date']."'":'');
			 $query .= ($_GET['e_date']?"and date<='".$_GET['e_date']."'":'');
			 $query .= ($_GET['invoice_id']?"and receive.invoice_no='".$_GET['invoice_id']."'":'');

			}
			 $db = mysql_query("SELECT SQL_CALC_FOUND_ROWS e_receive.id,company.name,e_receive.date,concat(type.prefix,e_receive.invoice_no),e_receive.cheque_no,e_receive.remark,e_receive.amount from `e_receive` LEFT JOIN `type` on e_receive.company = type.id LEFT JOIN `company` on e_receive.supplier=company.id where e_receive.id>=0 $query ORDER BY e_receive.id DESC LIMIT $limit[0],$limit[1]");
			 $total_db = mysql_query("SELECT FOUND_ROWS()");
                         $total = mysql_fetch_array($total_db,MYSQL_NUM);
			 $total = $total[0];
			 $type = mysql_query("SELECT * from `type`");
		break;
	}
	include( dirname(dirname(__FILE__)).'/template/system/e_rec.html');
?>