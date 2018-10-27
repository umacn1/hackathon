<?php
function login($username,$password)
{
 $db=mysql_query("SELECT * from `user` where account='$username' and password='$password'");
 if(mysql_num_rows($db)>0)
 {
	$row = mysql_fetch_array($db,MYSQL_ASSOC);
	$_SESSION['rand']=rand();
	if(isset($_SESSION['rand']))
	{
 	 $_SESSION[$_SESSION['rand'].'_id']=$row['id'];
	 $_SESSION[$_SESSION['rand'].'_username']=$row['name'];
	 $_SESSION[$_SESSION['rand'].'_account']=$row['password'];

	 $_SESSION[$_SESSION['rand'].'_author']=explode('|',$row['author']);
	 $uid = $row['id'];
	 $db = mysql_query("SELECT CONCAT(`type`,'|',`day`) from `author` where `uid` = $uid");
	 $i=0;
	 if(mysql_num_rows($db)>0)
	  while($row = mysql_fetch_array($db,MYSQL_NUM))
	  {
		$_SESSION[$_SESSION['rand'].'_authorPeriod'][$i]=$row[0];
		$i++;
	  }
	 
	 $db = mysql_query("UPDATE `user` set logindate=NOW() where account='$username'");
	 return true;
	}
 }else{
	return false;
	}
}


function define_page($string)
{
	define("PAGE",$string);
	if(permitedAuthor(PAGE))
	{
		return true;
	}else{
	die("You 're not allowed to do this");
	return false;
	}
}

function permitedAuthor($string)
{
	for($i=0;$i<count($_SESSION[$_SESSION['rand'].'_author']);$i++)
	{
		if($_SESSION[$_SESSION['rand'].'_author'][$i]==$string)
		{
			return true;
		}
	}
	return false;
}

function auth_period()
{
	for($i=0;$i<count($_SESSION[$_SESSION['rand'].'_authorPeriod']);$i++)
        {
		$data=explode('|',$_SESSION[$_SESSION['rand'].'_authorPeriod'][$i]);
		if($data[0]==PAGE)
		{
			define("PERIOD",$data[1]);
			return true;
		}
	}
	define("PERIOD",-1);
	return true;

}

function getday($date1, $date2)
{
	$date1 = strtotime($date1 . '-01 00:00:00');
	$date2 = strtotime($date2 . '-01 00:00:00');
	$result = floor(($date1 - $date2)/86400);
	return $result;
}

function sqlTotal($name , $sql)
{
	$db = mysql_query("SELECT * from `$name` $sql");
	return mysql_num_rows($db);
}

function invoiceTotal($name,$invoice) //invoice or expe
{
	$db = mysql_query("SELECT SUM(amount) from `$name` where id=$invoice");
	$row = mysql_fetch_array($db,MYSQL_NUM);
	return $row[0];
}

function remainPayment($recept_type,$invoiceid)  //receive or e_receive
{
	$name = ($recept_type=="receive"?"invoice":"expe");
	$prefix = ereg_replace("[0-9]","",$invoiceid);
        $invoiceid = ereg_replace("[a-zA-Z]","",$invoiceid);
	if($name == "invoice" || $name!="invoice")
	{
		$query = "and $recept_type.company=(SELECT id from type where prefix='$prefix')";
		$query2 = "and $name.company=(SELECT id from type where prefix='$prefix')";
	}
	$db = mysql_query("SELECT $name.amount,$name.amount-(SELECT SUM(amount) from $recept_type where invoice_no=$invoiceid $query GROUP BY invoice_no ) FROM `$name` where $name.id = $invoiceid $query2");
       
	$row = mysql_fetch_array($db,MYSQL_NUM);
	echo mysql_error();
	return $row[1]==NULL?$row[0]:$row[1];
	
}

function summaryRemain($type,$companyid,$date)
{
	$name = ($type=="invoice"?"receive":"e_receive");
	$db  = mysql_query("SELECT concat(type.prefix,$type.id) from `$type` left join `type` on $type.company = type.id where paid=0 and $type.".($type=="invoice"?"customer":"supplier")." = $companyid and date like '$date%'");
	while($row = mysql_fetch_array($db,MYSQL_NUM))
	{
		$total += remainPayment($name,$row[0]);
	}
	return $total;
}

function logout()
{
	session_destroy();
	//unset($_COOKIE['rand']); 
	header("location:./index.php");
}

function error($str)
{
	global $msg;
	$msg=$str;
}

function getIndex_things($ary)
{
        $num = count($ary);
        for($i=$num-1;$i>=0;$i--)
        {
          if($ary[$i]!='')
           return $i;
        }

}

?>
