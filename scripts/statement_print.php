<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Client Statement</title>
</head>
<style>
table{border-collapse:collapse}
body{
font: 12px Arial, sans-serif;
}
</style>
<body ><center>
<?php   //debit
 error_reporting(0); 
$stand=$_GET['id'];
include ('../aut.php');
	 include ('../opendb.php');
	 include ('../functions.php');
	  //include ('process.php');  //debit
$stand=$_GET['id'];
$data=GetCompanyDetails();
$bankingdetails = $data[4];
$bankingdetails2 = $data[5];
$bankingdetails3 = $data[6];
$banner = $data[8];
//nae andd surname

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
  $months_paid=$nw2['months_paid'];
  $number=$nw2['number'];$price=$nw2['price'];
  $instalments=$nw2['instalments'];$purchasedate=$nw2['owners_date'];
    }
	//purchase date
		$pd = mysql_query("SELECT * FROM stand where id_stand='$stand'")or die(mysql_query());
 while($rowpd = mysql_fetch_array($pd, MYSQL_ASSOC)){
 $purchasedate=$rowpd['datestatus'];
 $vatdate=$rowpd['vatdate'];
 $vat=$rowpd['vat'];
  $Area=$rowpd['area'];

    }
 $debit=getdebit($stand);
	 $credit=getcredit($stand);
	  $deposit=getdeposit($stand);
	$balance=$price-$credit-$deposit;
	$balance_month=$debit-$credit ?>
<table width="80%" border="0">
  <tr>
    <td><table width="100%" border="0">
  <tr>
    <td><img src="<?php echo $banner;?>"></td>
  </tr>
</table>
<h3 align="center">STATEMENT</h3><hr>

<table width="100%" border="0">
  <tr>
    <td width="34%" bgcolor="#CCCCCC"><strong>STAND # <?php echo $number;?><br>
<?php $n = mysql_query("SELECT
*
FROM owners,clients,stand
where owners.stand_id='$stand' and owners.client_id = clients.id AND
owners.stand_id = stand.id_stand
 ")or die(mysql_query());
 while($nw = mysql_fetch_array($n, MYSQL_ASSOC)){
  echo $nw['name']." ".$nw['surname'].",<br>
";
    }?><?php echo $add;?><br>
<?php echo $con;?><br>
<h5>DURATION OF <?php echo $months_paid;?> MONTHS</h5>
</strong><strong><hr>AREA&nbsp;<?php echo zva($Area);?> Sqm</strong></td>
    <td width="33%">&nbsp;</td>
    <td width="33%" bgcolor="#CCCCCC">
    
    <table width="100%" border="0">

   <tr>
    <td><strong>Purchase Date</strong></td>
    <td><?php echo substr($purchasedate,0,10);?></td>
  </tr>
  <?php if($vat =="YES"){?>
   <tr>
    <td><strong>VAT EFFECTED ON</strong></td>
    <td><?php echo substr($vatdate,0,10);?></td>
  </tr>
    <?php }?>

  <tr>
    <td><strong>Stand Price</strong></td>
    <td>$<?php echo zva($price);?></td>
  </tr> <tr>
    <td><strong>Stand Deposit Paid</strong></td>
    <td>$<?php echo zva($deposit);?></td>
  </tr> <tr>
  <tr>
    <td><strong>Cumulative Amount Paid</strong></td>
    <td>$<?php echo zva(GMSum($_GET['id'],"Deposit") + GMSum($_GET['id'],"Credit"));?></td>
  </tr> <tr>
    <td><strong>Cumulative Amount Due</strong></td>
    <td>$<?php echo zva($balance);?></td>
  </tr>   <?php if($vat !="YES"){?><tr>
   <td><strong>Monthly Amount Due <?php //echo CountMonths(substr($vatdate,0,7))."d".GetBeforeVat($_GET['id']);?></strong></td>
    <td>$<?php $MonthsOutstanding=(CountMonths(substr($purchasedate,0,7))-(($credit-GetBeforeVat($_GET['id']))/$instalments));
//	echo  $MonthsOutstanding;
	echo zva($MonthsOutstanding*$instalments);?></td>
  </tr><?php }?>
  
   <?php if($vat =="YES"){?><tr>
    <td><strong>Monthly Amount Due <?php //echo CountMonths("2015-01")."dfd".substr($vatdate,0,7)." ".(4752/176);?></strong></td>
    <td>$<?php $MonthsOutstanding=(CountMonths(substr($vatdate,0,7))-(($credit-GetBeforeVat($_GET['id']))/$instalments));
	echo zva($MonthsOutstanding*$instalments);?></td>
  </tr><?php }?> <tr> <tr>
    <td><strong>Monthly Instalments</strong></td>
    <td align="right">$<?php echo zva($instalments);?></td>
  </tr>
</table>
      </td>
  </tr>
</table>
<strong>BP #:200120952<br>
VAT #:10062313
</strong><hr>
<!------------------------------------------------------------------deopsit --------------------------------------------------->
<center><h4>Deposit Payments</h4></center>
<table width="100%" border="1">
  <tr bgcolor="#CCCCCC">
   <strong> <td width="8%"><strong>Date</strong></td>
    <td width="17%"><strong>Month </strong></td>
    <td width="32%"><strong>Description</strong></td>
    <td width="15%"><strong>Debit</strong></td>
  <td width="11%"><strong>Credit</strong></td>
 
    <td width="17%"><strong>Balance</strong></td>
    </strong>
  </tr>
  <?php
   $result = mysql_query("SELECT
*
FROM `payment`
WHERE
payment.stand='$_GET[id]' and payment_type='Deposit' order by id ASC  ")or die(mysql_query());

		 
	   if(!$result)
{
	die( "\n\ncould'nt send the query because".mysql_error());
	exit;
}
	$rows = mysql_num_rows($result);
	
$balance2Deposit=$deposit;
  while($row = mysql_fetch_array($result, MYSQL_ASSOC))
        
{
  //$monthz=GetInstalmentMonth("19/11/2013",30);

  $sDeposit=debit($row['payment_type'],$row['cash']);
  $s1Deposit=credit($row['payment_type'],$row['cash']);  
$creditbalanceDeposit=$balance2Deposit;
  $balance2Deposit=$balance2Deposit-$sDeposit; //$balance2=$balance2+$s1;  

$PaymentDateDeposit=substr($row['payment_date'],0,11);

echo "<tr><td>{$PaymentDateDeposit}</td><td>{$PaymentDateDeposit}</td><td>Stand Depost Payment</td><td align='right'>".zva($sDeposit)."</td><td align='right'></td><td align='right'>".zva($balance2Deposit)."</td></tr>";
  
}?>
  


</table><!------------------------------------------------------------------------------------------------------------------------------------------------------->
<hr><center><h4>Instalments Payments</h4></center>
<hr><table width="100%" border="1">
  <tr bgcolor="#CCCCCC">
   <strong> <td width="6%"><strong>Date</strong></td>
    <td width="14%"><strong>Month </strong></td>
    <td width="33%"><strong>Description</strong></td>
    <td width="15%"><strong>Debit</strong></td>
  <td width="15%"><strong>Credit</strong></td>
 
    <td width="17%"><strong>Balance</strong></td>
    </strong>
  </tr>
  <?php
   $result = mysql_query("SELECT
*
FROM `payment`
WHERE
payment.stand='$_GET[id]' and payment_type in('credit','debit') order by id ASC   ")or die(mysql_query());

		 
	   if(!$result)
{
	die( "\n\ncould'nt send the query because".mysql_error());
	exit;
}
	$rows = mysql_num_rows($result);
	if($rows==0)
 {
 	echo  "<font color='red' size='+3'>No statement avalible</font>";  
			exit;

 }
	 $CumulativeDebit=0;
 $CumulativeDebitAfterVat=0;
  $balance2=$price-$deposit;
  while($row = mysql_fetch_array($result, MYSQL_ASSOC))
        
{
  //$monthz=GetInstalmentMonth("19/11/2013",30);

  $s=debit($row['payment_type'],$row['cash']);
  $s1=GMdebit($row['payment_type'],$row['cash']);  
$creditbalance=$balance2;
if ($row['payment_type']=="Credit"){
  
}
   //$balance2=$balance2+$s1;  
  if ($row['payment_type']=="Credit"){
    $balance2=$balance2-$s;
   $CumulativeDebit+=$s;
}if ($row['payment_type']=="Debit"){
  $balance2=$balance2+$s1;
   $CumulativeDebit-=$s1;
}
 
  $CumulativeDebitAfterVat+=$s;
    $CumulativeDebitAfterVat1= $CumulativeDebitAfterVat-GetBeforeVat($_GET['id']);

  if($vat=="YES"){
    if($row['d']=="Balance_Before_VAT"){
          $monthz=substr($vatdate,0,11);
      }else{
       
    $monthz=GetInstalmentMonth($vatdate,GetMonthsPaid($CumulativeDebitAfterVat1,$instalments));
      }
  }else{
      $monthz=GetInstalmentMonth($purchasedate,GetMonthsPaid($CumulativeDebit,$instalments));
  
    }
$PaymentDate=substr($row['value_date'],0,11);
if($row['description']==""){
$DescriptionStatement=$row['d'];

}else{
  $DescriptionStatement=$row['description'];

}
echo "<tr><td>{$PaymentDate}</td><td>{$monthz}</td><td>$DescriptionStatement</td><td align='right'>".zva($s)."</td><td align='right'>".zva($s1)."</td><td align='right'>".zva($balance2)."</td></tr>";
	
}?>
  
 <tr bgcolor="#CCCCCC">
   <strong> <td></td>
    <td></td>
    <td></td>
    <td align="right"><strong><?php echo "(".zva($credit).")";?></strong></td>
    <td align="right"><strong></strong></td><td>&nbsp;</td>
    </strong>
  </tr> <tr bgcolor="#CCCCCC">
   <strong> <td></td>
    <td></td>
    <td><?php if($balance==0){echo "<strong>PAYMENT COMPLETE</strong>";}
	else{echo "<strong> PAYMENT IN PROGRESS</strong>";}?><!--<strong>AMOUNT DUE THIS MONTH = <?php echo zva($balance_month);?> </strong>--></td>
    <td></td>
    <td><strong>AMOUNT DUE</strong></td>
    <td align="right"><strong><?php echo zva($balance);?></strong></td>
    </strong>
  </tr>

</table>
<hr>
<table width="100%" border="0">
  <tr>
    <td width="50%"><font size="-1"><strong>Banking Details</strong><br>
<?php 
echo $bankingdetails."<br>
".$bankingdetails2."<br>
".$bankingdetails3;
?></td>
    <td width="50%" ><font color="#FF0000" size="-1">Interest will be charged on accounts overdue by three calender months</font><font size="-1"> Accounts that are overdue for a total of Three Calendar months, An accrued interest of 10% of the amount Overdue Per annum  </td>
  </tr>
</table><center><hr>

</td>
  </tr>
</table>

</body>
</html>