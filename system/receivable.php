<?php
$date = date("Y-m",mktime(0, 0 , 0,date("m")-1,1,date("Y")));

	switch($_GET['action'])
	{
		case "anniversary":
			define_page("receivable_anniversary");
			$paid_res = mysql_query("SELECT YEAR(date),MONTH(date),SUM(amount) from `invoice` where paid=1 and YEAR(`date`)>=YEAR(CURRENT_DATE()) GROUP BY YEAR(date),MONTH(date) ORDER BY `date` DESC");
			$unpaid_res = mysql_query("SELECT YEAR(date),MONTH(date),SUM(amount) from `invoice` where paid=0 GROUP BY YEAR(date),MONTH(date) ORDER BY `date` DESC");
			$all_res = mysql_query("SELECT YEAR(date),MONTH(date),SUM(amount) from `invoice` where YEAR(`date`)>=YEAR(CURRENT_DATE()) GROUP BY YEAR(date),MONTH(date) ORDER BY `date` DESC");
		break;
		case "summary":
			define_page("receivable_summary");
			$month = mysql_query("SELECT CONCAT( YEAR( invoice.date ) ,  '-', DATE_FORMAT( invoice.date,'%m' ) ) from invoice where invoice.paid=0 GROUP BY CONCAT( YEAR( invoice.date ) ,  '-', MONTH( invoice.date ) ) ORDER BY CONCAT( YEAR( invoice.date ) ,  '-', DATE_FORMAT( invoice.date,  '%m' ) ) ASC");
			$date_array = array();
			while($month_row = mysql_fetch_array($month,MYSQL_NUM))
			{
				array_push($date_array,$month_row[0]);
				$html_title .= "<th>$month_row[0]</th>";
			}
			$company = mysql_query("SELECT invoice.customer,company.name from invoice left join company on invoice.customer=company.id where paid=0 GROUP BY invoice.customer");
				$html_summary .="<tbody>";
				while($company_row = mysql_fetch_array($company,MYSQL_NUM))
				{
					$total+=1;
					$html_summary .="<tr class=\"".($total%2==0?"odd":"")."\"><td>$total</td><td>$company_row[1]</td>";
					$total_amount =0;

					foreach($date_array as $value)
					{
					 $red = getday($value,date("Y-m",mktime(0, 0 , 0,date("m"),1,date("Y"))))*-1;
					 $amount = summaryRemain('invoice',$company_row[0],$value);
					 $html_summary .= "<td style=".($red > 90 && $amount >0? "background-color:red;color:white":"").">$amount</td>";
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
		case "statement":
			define_page("receivable_statement");
			$id = $_GET['id'];
			
			$date_db  = mysql_query("SELECT DATE_FORMAT(date,'%Y-%m') from `invoice` where customer='$id' and date < '$date-31' and paid=0 GROUP BY YEAR(date),MONTH(date) ORDER BY `date` DESC");
			$company = mysql_query("SELECT * from `company` where id='$id'");
			$company_info = mysql_fetch_array($company,MYSQL_ASSOC);
			while($date_row = mysql_fetch_array($date_db,MYSQL_NUM))
			{
				$sum_amount = 0;
				$list = mysql_query("SELECT concat(type.prefix,invoice.id),date,quotation_no from invoice inner join `type` on invoice.company=type.id where paid=0 and customer=$id and date like '$date_row[0]%'");
				while($list_row = mysql_fetch_array($list,MYSQL_NUM))
				{
					$amount = remainPayment("receive",$list_row[0]);
					$statment .= "<tr id=\"data\"  onclick=\"hilight(this)\"><td>$list_row[1]</td><td>$list_row[0]</td><td>$list_row[2]&nbsp;</td><td>$$amount</td><td>&nbsp;</td></tr>";
					$sum_amount +=$amount;
				}
				$sum_total +=$sum_amount;
				$statment .= "<tr  onclick=\"hilight(this)\"><td colspan=2>&nbsp;</td><td>&nbsp;</td><td align=right>$date_row[0]月總額</td><td>$$sum_amount</td></tr>";
			}
			$statment .= "<tr><td colspan=2>&nbsp;</td><td>&nbsp;</td><td align=right>TOTAL:</td><td>$$sum_total</td></tr>";
		break;
		default:
                        define_page("receivable_view");
                        $num = $_GET['num'];
                        $num == 0 ? $num=1 : $page=$num;
                        $limit[0] = 0+30*($num-1);
                        $limit[1] = 30;
			$unpaid_company = mysql_query("SELECT invoice.customer,company.name,company.id from invoice inner join `company` on invoice.customer = company.id where invoice.paid=0 and date <='$date-31' GROUP BY invoice.customer");
			while($unpaid_company_row=mysql_fetch_array($unpaid_company,MYSQL_NUM))
			{
			 $total ++;
			 $sum_amount = 0;
			 $amount = mysql_query("SELECT concat(type.prefix,invoice.id),invoice.customer from invoice inner join type on type.id=invoice.company where date<= '$date-31' and invoice.paid=0 and invoice.customer=$unpaid_company_row[0]");
				while($amount_row = mysql_fetch_array($amount,MYSQL_NUM))
				$sum_amount += remainPayment("receive",$amount_row[0]);
			 $list .= "<tr><td>$total</td><td>$unpaid_company_row[1]</td><td><a href=\"./main.php?page=receivable&action=statement&id=$unpaid_company_row[2]\">$date</a></td><td>".$sum_amount."</td></tr>";
			 $sum +=$sum_amount;
                        }
                break;
        }
        include( dirname(dirname(__FILE__)).'/template/system/receivable.html');
?>

