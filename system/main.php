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
        case 'test':
            /*$get_meal_ppl_sql = mysql_query("SELECT hashID,  `consumeTime`
                        FROM  `meal`
                        GROUP BY DAY(  `consumeTime` ) , hashID");
            
            while($row = mysql_fetch_array($get_meal_ppl_sql,MYSQL_NUM))
            {
                                            $get_access_data = mysql_query("SELECT *
                                                                           FROM  `checkinout`
                                                                           WHERE doorID LIKE  \"% out\"
                                                                           AND grantResult =  'G'
                                                                           AND accessDate >= ADDTIME(  \"$row[1]\", 45 *60 )
                                                                           AND accessDate <= ADDTIME(  \"$row[1]\", 90 *60 )
                                                                           ORDER BY RAND( ) 
                                                                           LIMIT 0 , 1");
                  $get_target_id =mysql_fetch_array($get_access_data,MYSQL_NUM);
                                                                                                     mysql_query("UPDATE `checkinout` set `hashID`='$row[0]' where id=$get_target_id[0]");
            }*/
        break;
		default:
			 $meal_count_dinner_sql = mysql_query("SELECT count(*) from `meal` where `consumptionLocation` = 'CKYC' and `mealType` = 'DINNER'");
			 $meal_count_dinner_row = mysql_fetch_array($meal_count_dinner_sql,MYSQL_NUM);
			 $meal_count_lunch_sql = mysql_query("SELECT count(*) from `meal` where `consumptionLocation` = 'CKYC' and `mealType` = 'LUNCH'");
			 $meal_count_lunch_row = mysql_fetch_array($meal_count_lunch_sql,MYSQL_NUM);
			 $meal_count_breakfast_sql = mysql_query("SELECT count(*) from `meal` where `consumptionLocation` = 'CKYC' and `mealType` = 'BREAKFAST'");
			 $meal_count_breakfast_row = mysql_fetch_array($meal_count_breakfast_sql,MYSQL_NUM);
			 $meal_count_nr_sql = mysql_query("SELECT count(*) from `meal` where `rcMember` != `consumptionLocation`");
			 $meal_count_nr_row = mysql_fetch_array($meal_count_nr_sql,MYSQL_NUM);
            
            $meal_arrive_map_sql = mysql_query("select  avg(diff),mealType,YEAR(`consumeTime`),MONTH(`consumeTime`),DAY(`consumeTime`) from (
                                               SELECT  meal.*,checkinout.accessDate - meal.consumeTime as diff
                                               FROM  `meal` ,  `checkinout`
                                               WHERE meal.hashID = checkinout.hashID
                                               ) A where A.diff <= 5400 and mealType=\"BREAKFAST\" GROUP BY DAY(A.consumeTime), A.mealType");
            
				
			 $meal_count_breakdown_sql = mysql_query("SELECT count(*),YEAR(`consumeTime`),MONTH(`consumeTime`),DAY(`consumeTime`) from `meal` where `consumptionLocation` = 'CKYC' GROUP BY DAY(`consumeTime`)");
			
			  $meal_count_distribution_sql = mysql_query("SELECT noofmeal, COUNT( * ) 
FROM (

SELECT COUNT( * ) AS noofmeal, CONCAT( YEAR(  `consumeTime` ) ,  '-', MONTH(  `consumeTime` ) ,  '-', DAY(  `consumeTime` ) ) AS  `DATE` , hashID
FROM  `meal` 
WHERE  `consumptionLocation` =  'CKYC'
GROUP BY DAY(  `consumeTime` ) , hashID
)A
GROUP BY A.noofmeal");
			 //$meal_count_breakfast_row = mysql_fetch_array($meal_count_breakfast_sql,MYSQL_NUM);
			 
		break;
	}

	include( dirname(dirname(__FILE__)).'/template/index.html');
?>
