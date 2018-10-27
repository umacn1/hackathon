<?php
	switch($_GET['action'])
	{
		case 'add':
			define_page('admin_add');
			if($_POST['Submit'])
			{
				if($_POST['account'] && $_POST['password'] && $_POST['staff'])
				{
					foreach($_POST['author'] as $value)
					{
						$admin_author.=$value.'|';
					}
					$admin_acc = $_POST['account'];
					$admin_pass = $_POST['password'];
					$admin_staff = $_POST['staff'];

					
					$_POST['income_edit'] ? $array['income_edit'] = $_POST['income_edit'] : '';
					$_POST['income_delete'] ? $array['income_delete'] = $_POST['income_delete'] : '';
					$_POST['income_sign'] ? $array['income_sign'] = $_POST['income_sign'] : '';
					$_POST['expe_edit'] ? $array['expe_edit'] = $_POST['expe_edit']:'';
					$_POST['expe_delete'] ? $array['expe_delete'] = $_POST['expe_delete']:'';
					$_POST['quotation_edit'] ? $array['quotation_edit'] = $_POST['quotation_edit']:'';
					$_POST['quotation_delete'] ? $array['quotation_delete'] = $_POST['quotation_delete']:'';
					$_POST['rec_edit'] ? $array['rec_edit'] = $_POST['rec_edit']:'';
					$_POST['rec_delete'] ? $array['rec_delete'] = $_POST['rec_delete']:'';
					$_POST['e_rec_edit'] ? $array['e_rec_edit'] = $_POST['e_rec_edit'] : '';
					$_POST['e_rec_delete'] ? $array['e_rec_delete'] = $_POST['e_rec_delete'] : '';

                                        $db = mysql_query("INSERT into `user` VALUES(NULL,'$admin_staff','$admin_acc','$admin_pass','$admin_author','0000-00-00 00:00:00')");
					$uid = mysql_insert_id();
					foreach($array as $key => $value)
					{
						if($value!='');
							$db = mysql_query("INSERT into `author` VALUES('$uid','$key','$value')");
					}

					echo "<script>history.go(-2);</script>";
				}else
					error("Missing field(s)");
			}
		break;
		case 'edit':
			define_page('admin_edit');
			$id = $_GET['id'].$_POST['id'];
			if($_POST['Submit'])
			{
				$admin_password = $_POST['password'];
				$admin_staff = $_POST['staff'];
                                $_POST['income_edit'] ? $array['income_edit'] = $_POST['income_edit'] : '';
                                $_POST['income_delete'] ? $array['income_delete'] = $_POST['income_delete'] : '';
                                $_POST['income_sign'] ? $array['income_sign'] = $_POST['income_sign'] : '';
                                $_POST['expe_edit'] ? $array['expe_edit'] = $_POST['expe_edit']:'';
                                $_POST['expe_delete'] ? $array['expe_delete'] = $_POST['expe_delete']:'';
								$_POST['quotation_edit'] ? $array['quotation_edit'] = $_POST['quotation_edit']:'';
								$_POST['quotation_delete'] ? $array['quotation_delete'] = $_POST['quotation_delete']:'';
                                $_POST['rec_edit'] ? $array['rec_edit'] = $_POST['rec_edit']:'';
                                $_POST['rec_delete'] ? $array['rec_delete'] = $_POST['rec_delete']:'';
                                $_POST['e_rec_edit'] ? $array['e_rec_edit'] = $_POST['e_rec_edit'] : '';
                                $_POST['e_rec_delete'] ? $array['e_rec_delete'] = $_POST['e_rec_delete'] : '';
				foreach($_POST['author'] as $value)
                                {
					  $admin_author.=$value.'|';
                                }
				$admin_password = $admin_password ? ", `password`='$admin_password'":'';
				$db = mysql_query("UPDATE `user` set `author`='$admin_author',`name`='$admin_staff' $admin_password where `id`='$id'");

				$db = mysql_query("DELETE from `author` where uid ='$id'");
                                foreach($array as $key => $value)
                                {	if($value!='');
                                		$db = mysql_query("INSERT into `author` VALUES('$id','$key','$value')");
                                }
                        	echo "<script>history.go(-2);</script>";
			}else{
				$db = mysql_query("SELECT `account`,`name`,`author` from `user` where id='$id'");
				$db_period = mysql_query("SELECT * from `author` where uid='$id'");
				$row = mysql_fetch_array($db,MYSQL_NUM);
				$array = explode('|',$row[2]);
				foreach($array as $value)
					$author[$value] = 1;

				if(mysql_num_rows($db_period)>=1)
				while($period = mysql_fetch_array($db_period,MYSQL_NUM) )
				{
					$period_array[$period[1]]=$period[2];
				}

			     }
		break;
		case 'delete':
			define_page('admin_delete');
			$id = $_GET['id'];
			$db = mysql_query("DELETE from `user` where id='$id'");
			echo "<script>history.go(-1);</script>";
		break;
		default:
			define_page('admin_view');
                        $num = $_GET['num'];
                        $num == 0 ? $num=1 : $page=$num;
                        $limit[0] = 0+30*($num-1);
                        $limit[1] = 30*$num;

			if($_GET['Search'])
			{
				$db_acc = $_GET['account'];
				$db_staff = $_GET['staff'];
				$query .= ($db_acc ? "and `account` like '%$db_acc%'":'');
				$query .= ($db_staff ? "and `staff` like '%$db_staff%'":'');
			}
			$db = mysql_query("SELECT SQL_CALC_FOUND_ROWS * from `user` where 1 $query LIMIT $limit[0],$limit[1]");
			$total_db = mysql_query("SELECT FOUND_ROWS()");
                        $total = mysql_fetch_array($total_db,MYSQL_NUM);
                        $total = $total[0];
		break;
	}
	include( dirname(dirname(__FILE__)).'/template/system/admin.html');
?>
