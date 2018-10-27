<?php

	switch($_GET['action'])
	{
		case "anniversary":
			define_page("payable_anniversary");
			$paid_res = mysql_query("SELECT YEAR(date),MONTH(date),SUM(amount) from `expe` where paid=1 and YEAR(`date`)>=YEAR(CURRENT_DATE()) GROUP BY YEAR(date),MONTH(date) ORDER BY `date` DESC");
			$unpaid_res = mysql_query("SELECT YEAR(date),MONTH(date),SUM(amount) from `expe` where paid=0 GROUP BY YEAR(date),MONTH(date) ORDER BY `date` DESC");
			$all_res = mysql_query("SELECT YEAR(date),MONTH(date),SUM(amount) from `expe` where YEAR(`date`)>=YEAR(CURRENT_DATE()) GROUP BY YEAR(date),MONTH(date) ORDER BY `date` DESC");
		break;
		case "summary":
                        define_page("payable_summary");
                        $month = mysql_query("SELECT CONCAT( YEAR( expe.date ) ,  '-', DATE_FORMAT( expe.date,'%m' ) ) AS DATE from expe where expe.paid=0 GROUP BY CONCAT( YEAR( expe.date ) ,  '-', MONTH( expe.date ) ) ORDER BY DATE ASC");
                        $date_array = array();
                        while($month_row = mysql_fetch_array($month,MYSQL_NUM))
                        {
                                array_push($date_array,$month_row[0]);
                                $html_title .= "<th>$month_row[0]</th>";
                        }
                        $company = mysql_query("SELECT expe.supplier,company.name from expe left join company on expe.supplier=company.id where paid=0 GROUP BY expe.supplier");
				$html_summary .="<tbody>";
                                while($company_row = mysql_fetch_array($company,MYSQL_NUM))
                                {
                                        $total+=1;
                                        $html_summary .="<tr class=\"".($total%2==0?"odd":"")."\"><td>$total</td><td>$company_row[1]</td>";
                                        $total_amount =0;
                                        foreach($date_array as $value)
                                        {
                                         $red = getday($value,date("Y-m",mktime(0, 0 , 0,date("m"),1,date("Y"))))*-1;
                                         $amount = summaryRemain('expe',$company_row[0],$value);
                                         $html_summary .= "<td style=\"background-color:".($red > 90 && $amount >0? "red":"")."\">$amount</td>";
                                         $total_amount +=$amount;
                                         $monthly_amount[$value] += $amount;
                                        }
                                        $html_summary .="<td>$total_amount</td></tr>";
                                }
                                $html_summary .="</tbody><tr style=\"font-size:14pt\"><td colspan=2>Amount:</td>";
                                foreach($date_array as $value)
                                {
                                        $sum += $monthly_amount[$value];
                                        $html_summary .= "<td>$monthly_amount[$value]</td>";
                                }
                                $html_summary .="<td>$sum</td></tr>";

                break;
		default:
			define_page("payable_view");
                        $num = $_GET['num'];
                        $num == 0 ? $num=1 : $page=$num;
                        $limit[0] = 0+30*($num-1);
                        $limit[1] = 30;
			$unpaid_company = mysql_query("SELECT expe.supplier,company.name,company.id from expe inner join `company` on expe.supplier = company.id where expe.paid=0 GROUP BY expe.supplier");
			while($unpaid_company_row=mysql_fetch_array($unpaid_company,MYSQL_NUM))
			{
			 $total ++;
			 $sum_amount = 0;
			 $amount = mysql_query("SELECT concat(type.prefix,expe.id),expe.supplier from expe inner join type on type.id=expe.company where expe.paid=0 and expe.supplier=$unpaid_company_row[0]");
				while($amount_row = mysql_fetch_array($amount,MYSQL_NUM))
				$sum_amount += remainPayment("e_receive",$amount_row[0]);
			 $list .= "<tr><td>$total</td><td>$unpaid_company_row[1]</td><td>".$sum_amount."</td></tr>";
			 $sum +=$sum_amount;
			}
		break;
	}
	include( dirname(dirname(__FILE__)).'/template/system/payable.html');
?>
