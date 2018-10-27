<?php
	switch($_GET['action'])
	{
		case "add":
			define_page("expe_add");
			if($_POST['Submit'])
			{
				
			if($_POST['date'] && $_POST['supplier'] && $_POST['amount'] && $_POST['from_company'] && $_POST['content'][0]!=''){
				$id = $_POST['id'];
				$date = $_POST['date'];
				$supplier = $_POST['supplier'];
				$amount = $_POST['amount'];
				$type = $_POST['type'];
				$from_company = $_POST['from_company'];
				$paid = $_POST['paid'];
				$content = $_POST['content'][getIndex_things($_POST['content'])];
				//$content = $_POST['content'][count($_POST['content'])-1];
				$remark = $_POST['remark'];
				$uid = $_SESSION[$_SESSION['rand'].'_id'];
				$db = mysql_query("SELECT id from `expe` where id='$id' and company='$from_company'");
                if(mysql_num_rows($db) ==0)
				 $db = mysql_query("INSERT into `expe` VALUES('$id',(SELECT `id` from `company` where `name`='$supplier' and `supplier`=1),'$amount','$type','$from_company','$content','$paid','$remark',NULL,'$date','$uid')");
				else
				 error("Duplicate invoice id");
                        //echo "<script>history.go(-2);</script>";
			}else
				error("Missing field(s)");
			}
			$type = mysql_query("SELECT * from `type`");
			$company = mysql_query("SELECT `name` from `company` where `supplier`=1");
 			$content = mysql_query("SELECT `id`,`name` from `content` where type='expe' and `id` NOT IN (SELECT `child` from `relationContent`)");
   		 	     
		break;
		case "detail":
			define_page("expe_detail");
			$prefix = ereg_replace("[0-9]","",$_GET['id']);
        		$id = ereg_replace("[a-zA-Z]","",$_GET['id']);
                        //$prefix = substr($_GET['id'],0,1);
                        //$id = substr($_GET['id'],1);

			$db = mysql_query("SELECT expe.id,company.name,expe.date,expe.amount,expe.`type`,expe.remark,(SELECT name from content where id=expe.content),expe.paid from `company`,`expe` where  expe.supplier=company.id and expe.id='$id' and expe.company=(SELECT id from type where prefix='$prefix')");
			$row = mysql_fetch_array($db,MYSQL_NUM);
		break;
		case "edit":
			define_page("expe_edit");
			auth_period();
                        $prefix = ereg_replace("[0-9]","",$_GET['id']);
                        $id = ereg_replace("[a-zA-Z]","",$_GET['id']);
                        //$prefix = substr($_GET['id'],0,1);
                        //$id = substr($_GET['id'],1);
			if($_POST['Submit'])
                        {
                        if($_POST['date'] && $_POST['supplier'] && $_POST['from_company'] && $_POST['content'][0]!=''){
                                $date = $_POST['date'];
                                $supplier = $_POST['supplier'];
                                $amount = $_POST['amount'];
                                $type = $_POST['type'];
                                $from_company = $_POST['from_company'];
                                $paid = $_POST['paid'];
				$content = $_POST['content'][getIndex_things($_POST['content'])];
                                //$content = ($_POST['content'][count($_POST['content'])-1]) == '' ?$_POST['content'][0] : $_POST['content'][count($_POST['content'])-1];
                                $remark = $_POST['remark'];
				$period = (PERIOD>=0? "and DATEDIFF(DATE_FORMAT( NOW( ) ,  '%Y-%m-%d' ),`date`)<=".PERIOD:'');
				$db = mysql_query("UPDATE `expe` set date='$date',supplier=(SELECT id from `company` where name='$supplier'),amount='$amount',type='$type',company='$from_company',paid='$paid',content='$content',remark='$remark' where id='$id' and expe.company=(SELECT id from type where prefix='$prefix') $period");
				echo "<script>history.go(-2);</script>";
			}else
				error("Missing field(s)");
			}else{
            
				$db = mysql_query("SELECT expe.id,company.name,expe.date,expe.amount,expe.`type`,expe.remark,R.parent,expe.paid,expe.supplier,expe.company,expe.content from `company`,`expe`,`relationContent` R where (R.parent = `expe`.content OR R.child=`expe`.content) AND expe.supplier=company.id and expe.id='$id' and expe.company=(SELECT id from type where prefix='$prefix')");
                
                if(mysql_num_rows($db)==0)
                		$db = mysql_query("SELECT expe.id,company.name,expe.date,expe.amount,expe.`type`,expe.remark,expe.content,expe.paid,expe.supplier,expe.company from `company`,`expe`,`content` R where (R.id = `expe`.content) AND expe.supplier=company.id and expe.id='$id' and expe.company=(SELECT id from type where prefix='$prefix')");
                        
                $row = mysql_fetch_array($db,MYSQL_NUM);
				$type = mysql_query("SELECT * from `type`");
                        	$company = mysql_query("SELECT `name` from `company` where `supplier`=1");
                        	$content = mysql_query("SELECT `id`,`name` from `content` where type='expe' and `id` NOT IN (SELECT `child` from `relationContent`)");

			     }
		break;
		case "delete":
			define_page("expe_delete");
			auth_period();
                        $prefix = ereg_replace("[0-9]","",$_GET['id']);
                        $id = ereg_replace("[a-zA-Z]","",$_GET['id']);
			$period = (PERIOD>=0? "and DATEDIFF(DATE_FORMAT( NOW( ) ,  '%Y-%m-%d' ),`date`)<=".PERIOD:'');
			$db = mysql_query("DELETE from `expe` where id='$id' and expe.company=(SELECT id from type where prefix='$prefix') $period");
			echo "<script>history.go(-1);</script>";
		break;
		default:
			define_page("expe_view");
                        $num = $_GET['num'];
                        $num == 0 ? $num=1 : $page=$num;
                        $limit[0] = 0+30*($num-1);
                        $limit[1] = 30;

			$company = mysql_query("SELECT `name` from `company` where `supplier`=1");
			$type = mysql_query("SELECT * from `type`");
			$content = mysql_query("SELECT `id`,`name` from `content` where type='expe' and `id` NOT IN (SELECT `child` from `relationContent`)");
	
			if($_GET['Search'])
			{
			 $limit[0] = 0;
			 $limit[1] = 9999999999;
			 $query='';
			 $query .= ($_GET['name']? "and company.name='".$_GET['name']."'" :'');
			 $query .= ($_GET['id']?"and expe.id='".$_GET['id']."'":'');
			 $query .= ($_GET['type']!=''?"and expe.type='".$_GET['type']."'":'');
			 $query .= ($_GET['content'][0]!=''?"and expe.content='". $_GET['content'][getIndex_things($_GET['content'])]."'":'');
			 $query .= ($_GET['from_company']?"and expe.company='".$_GET['from_company']."'":'');
			 $query .= ($_GET['paid']!=''?" and expe.paid='".$_GET['paid']."'":'');
			 $query .= ($_GET['s_date']?"and date>='".$_GET['s_date']."'":" and date >= DATE_FORMAT( NOW( ) ,  '%Y-%m' ) ");
			 $query .= ($_GET['e_date']?"and date<='".$_GET['e_date']."'":'');
			}
			$db= mysql_query("SELECT SQL_CALC_FOUND_ROWS type.name,concat(type.prefix,expe.id),company.name,expe.date,expe.amount,expe.`type`,expe.remark,expe.paid,`content`.`name` from `company`,`type`,`expe`,`content` where `content`.`id` = `expe`.`content` and `content`.`type`='expe' and expe.company=type.id and expe.supplier=company.id $query ORDER BY expe.id DESC LIMIT $limit[0],$limit[1]");
			$total_db = mysql_query("SELECT FOUND_ROWS()"); 
			$total = mysql_fetch_array($total_db,MYSQL_NUM);
			$total = $total[0];
		break;
	}
	include( dirname(dirname(__FILE__)).'/template/system/expe.html');
?>