
<?php   //debit
//serror_reporting(0);
mysql_query("TRUNCATE TABLE recalculate;");
$ReportAmountOwing=0;

		//purchse date
	$pd = mysql_query("SELECT * FROM stand where status!='RESERVED'")or die(mysql_query());
 while($rowpd = mysql_fetch_array($pd, MYSQL_ASSOC)){
 $purchasedate=$rowpd['datestatus'];
 $vatdate=$rowpd['vatdate'];
 $vat=$rowpd['vat'];  
	 $Area=$rowpd['area'];
$stand=$rowpd['id_stand'];
$standnumber=$rowpd['number'];
   
	//addresss
	$n2 = mysql_query("SELECT
*
FROM owners,stand,clients
where id_stand='$stand' and owners.client_id = clients.id AND
owners.stand_id = stand.id_stand
ORDER BY
clients.id ASC
LIMIT 1 ")or die(mysql_query());
 while($nw2 = mysql_fetch_array($n2, MYSQL_ASSOC)){
  $add=$nw2['address'];
  $con=$nw2['contact']; 
	 $idnum=$nw2['idnum'];
  $months_paid=$nw2['months_paid'];
  $number=$nw2['number'];
	 $price=$nw2['price'];
  $instalments=$nw2['instalments'];
  
  // $purchasedate=$nw2['datestatus'];
    }

 $debit=getdebit($stand);
	 $credit=getcredit($stand);
	  $deposit=getdeposit($stand);
	$balance=$price-$credit-$deposit;
	$balance_month=$debit-$credit ?>
   <?php
		if($vat !="YES"){
		$MonthsOutstanding=(CountMonths(substr($purchasedate,0,7))-(($credit-GetBeforeVat($stand))/$instalments));
	$ReportAmountOwing=$MonthsOutstanding*$instalments;
//$ReportNumberMonth=CountMonthsReport(substr($purchasedate,0,7)."-00");
		}
if($vat =="YES"){
	 $MonthsOutstanding=(CountMonths(substr($vatdate,0,7))-(($credit-GetBeforeVat($stand))/$instalments));
	$ReportAmountOwing=$MonthsOutstanding*$instalments;

				}
?>
 


  <?php
   $result = mysql_query("SELECT
*
FROM `payment`
WHERE
payment.stand='$stand' and payment_type='credit' order by id ASC  ")or die(mysql_query());

		 
	   if(!$result)
{
	die( "\n\ncould'nt send the query because".mysql_error());
	exit;
}
	$rows = mysql_num_rows($result);

   $CumulativeDebit=0;
 $CumulativeDebitAfterVat=0;
  $balance2=$price-$deposit;
  while($row = mysql_fetch_array($result, MYSQL_ASSOC))
        
{
  //$monthz=GetInstalmentMonth("19/11/2013",30);

  $s=debit($row['payment_type'],$row['cash']);
  $s1=credit($row['payment_type'],$row['cash']);  
$creditbalance=$balance2;
  $balance2=$balance2-$s; //$balance2=$balance2+$s1;  
  $CumulativeDebit+=$s;
  $CumulativeDebitAfterVat+=$s;
    $CumulativeDebitAfterVat1= $CumulativeDebitAfterVat-GetBeforeVat($stand);

  if($vat=="YES"){
    if($row['d']=="Balance_Before_VAT"){
          $monthz=substr($vatdate,0,11);
		    $monthzDate=$vatdate;

      }
	  else
	  {
       $xxe=GetMonthsPaid($CumulativeDebitAfterVat1,$instalments);
    $monthz=GetInstalmentMonth($vatdate,GetMonthsPaid($CumulativeDebitAfterVat1,$instalments));
    $monthzDate=GetInstalmentMonthDate($vatdate,GetMonthsPaid($CumulativeDebitAfterVat1,$instalments));
      }
  }
	else
  {
      $xxe=GetMonthsPaid($CumulativeDebit,$instalments);
	  $monthz=GetInstalmentMonth($purchasedate,GetMonthsPaid($CumulativeDebit,$instalments));
	  $monthzDate=GetInstalmentMonthDate($purchasedate,GetMonthsPaid($CumulativeDebit,$instalments));
  }
$PaymentDate=substr($row['value_date'],0,11);
if($row['description']=="")
{
$DescriptionStatement=$row['d'];
}
	else
	{
  $DescriptionStatement=$row['description'];
}
/*echo "<tr><td>{$PaymentDate}</td><td>{$monthz}</td><td>$DescriptionStatement</td><td align='right'>".zva($s)."</td><td align='right'>".zva($creditbalance)."</td><td align='right'>".zva($balance2)."</td></tr>";*/
  $ReportPaymentDate=$PaymentDate;
$ReportMonth=$monthz;
$ReportMonthDate=$monthzDate;
$ReportDetails=$DescriptionStatement;
$ReportAmount=$s;
$ReportRunningBal=$balance2;
$ReportStand=$stand;
$ReportOwnerID=GetOwnerID($stand);
$AmountDue=$balance;
		$ReportNumberMonth=CountMonthsReport(substr($ReportMonthDate,0,7)."-00");

	mysql_query("INSERT INTO `recalculate` (`stand`, `paymentdate`, `paymentmonth`, `paymentmonthdate`, `paymentdetails`, `paymentamount`, `runningbalance`, `balance`,`paymentnumbermonth`,`paymentowner`,`standnumber`) VALUES ('$ReportStand', '$ReportPaymentDate', '$ReportMonth', '$ReportMonthDate', '$ReportDetails', '$ReportAmount', '$ReportRunningBal', '$ReportAmountOwing', '$ReportNumberMonth', '$ReportOwnerID','$standnumber')");
	
	//echo substr($ReportMonthDate,0,7)."-00<br>
} 
echo "<h4>Processing...............</h4>";
 }

?> <script language="javascript">
 location ='gm58northgate'
   </script>
  
  
