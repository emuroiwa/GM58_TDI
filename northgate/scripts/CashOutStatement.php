<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Untitled Document</title>
</head>

<body><center>
<?php   //debit
error_reporting(0);
$stand=$_GET['id'];

$qry1 = mysql_query("SELECT * FROM `amendments`  where stand='$stand'")or die(mysql_query());
 while($rw1 = mysql_fetch_array($qry1, MYSQL_ASSOC)){
$CashOutAmount=$rw1['cashoutamount'];
    }
//nae andd surname
$data=GetCompanyDetails();
$bankingdetails = $data[4];
$bankingdetails2 = $data[5];
$bankingdetails3 = $data[6];
$banner = $data[8];
		//purchse date

		$pd = mysql_query("SELECT * FROM cashoutstand where id_stand='$stand'")or die(mysql_query());
 while($rowpd = mysql_fetch_array($pd, MYSQL_ASSOC)){
 $purchasedate=$rowpd['datestatus'];
 $vatdate=$rowpd['vatdate'];
 $vat=$rowpd['vat'];
   $deposit=$rowpd['deposit'];

    }
	//addresss
 	$n2 = mysql_query("SELECT * FROM cashoutowners,cashoutstand,cashoutclients where id_stand='$stand' and cashoutowners.client_id = cashoutclients.id AND cashoutowners.stand_id =cashoutstand.id_stand ORDER BY cashoutclients.id ASC LIMIT 1 ")or die(mysql_query());
 while($nw2 = mysql_fetch_array($n2, MYSQL_ASSOC)){
  $add=$nw2['address'];
  $con=$nw2['contact'];  $dob=$nw2['dob'];
  $months_paid=$nw2['months_paid'];
  $number=$nw2['number'];$price=$nw2['price'];
  $instalments=$nw2['instalments'];
  // $purchasedate=$nw2['datestatus'];
    }

 $debit=getdebit($stand);
	 $credit=getcreditcashout($stand);
	  //$deposit=getdeposit($stand);
	$balance=$price-$credit-$deposit;
	$balance_month=$debit-$credit ?>
<table width="80%" border="0" align="center">
  <tr>
    <td><table width="100%" border="0">
  <tr>
    <td><img src="<?php echo $banner;?>"></td>
  </tr>
</table>
<h3 align="center">CASH OUT STATEMENT</h3><hr>

<table width="100%" border="0">
  <tr>
    <td width="34%" bgcolor="#CCCCCC"><strong>STAND # <?php echo $number;?><br>
<?php $n = mysql_query("SELECT * FROM cashoutowners,cashoutclients,cashoutstand where cashoutowners.stand_id='$stand' and cashoutowners.client_id = cashoutclients.id AND cashoutowners.stand_id = cashoutstand.id_stand ")or die(mysql_query());
 while($nw = mysql_fetch_array($n, MYSQL_ASSOC)){
  echo $nw['name']." ".$nw['surname'].",<br>
";
    }?><?php echo $add;?><br>
<?php echo $con;?><hr>
<?php echo "Date Of Birth $dob";?><br>
<h5>DURATION OF <?php echo $months_paid;?> MONTHS</h5>
</strong></td>
    <td width="33%">&nbsp;</td>
    <td width="33%" bgcolor="#CCCCCC"><table width="100%" border="0">
  <!--<tr>
    <td><strong>Terms</strong></td>
    <td>Current</td>
  </tr>-->
  <tr>
    <td><strong>Purchase Date</strong></td>
    <td><?php echo $purchasedate;?></td>
  </tr>
  <?php if($vat =="YES"){?>
   <tr>
    <td><strong>VAT EFFECTED ON</strong></td>
    <td><?php echo $vatdate;?></td>
  </tr>
    <?php }?>
  <tr>
    <td><strong>Stand Price</strong></td>
    <td>$<?php echo $price;?></td>
  </tr> <tr>
    <td><strong>Stand Deposit Paid</strong></td>
    <td>$<?php echo $deposit;?></td>
  </tr> <tr>
    <td><strong>Amount Due</strong></td>
    <td>$<?php echo $balance;?></td>
  </tr> <tr>
    <td><strong>Monthly Instalments</strong></td>
    <td>$<?php echo $instalments;?></td>
  </tr>
</table>
      </td>
  </tr>
</table>
<strong>BP #:200120952<br>
VAT #:10062313
</strong>
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
   $result = mysql_query("SELECT * FROM `cashoutpayment` WHERE stand='$_GET[id]' and payment_type='credit' order by id ASC  ")or die(mysql_query());
	   if(!$result)
{
	die( "\n\ncould'nt send the query because".mysql_error());
	exit;
}
	$rows = mysql_num_rows($result);
	if($rows==0)
 {
 	echo("<SCRIPT LANGUAGE='JavaScript'> window.alert('No statement avalible')
		  javascript:history.go(-1)
		 	</SCRIPT>");  
			exit;

 }
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
$PaymentDate=substr($row['payment_date'],0,11);
echo "<tr><td>{$PaymentDate}</td><td>{$monthz}</td><td>{$row['d']} {$row['value_date']}</td><td align='right'>".zva($s)."</td><td align='right'>".zva($creditbalance)."</td><td align='right'>".zva($balance2)."</td></tr>";
  
}?>
  
  
  
  
  
 <tr bgcolor="#CCCCCC">
   <strong> <td></td>
    <td></td>
    <td></td>
    <td><strong><?php echo "($credit)";?></strong></td>
    <td><strong><?php echo $price-$deposit;?></strong></td><td>&nbsp;</td>
    </strong>
  </tr> <tr bgcolor="#CCCCCC">
   <strong> <td></td>
    <td></td>
    <td> <h4><font color="#FF0000">CASH OUT AMOUNT $<?php echo $CashOutAmount;?></font></h4></td>
    <td></td>
    <td><strong>AMOUNT DUE</strong></td>
    <td><strong><?php echo $balance;?></strong></td>
    </strong>
  </tr>

</table>
<hr>
<table width="100%" border="1">
  <tr>
    <td width="50%"><strong>Banking Details</strong><br>
<?php 
echo $bankingdetails."<br>
".$bankingdetails2."<br>
".$bankingdetails3;
?></td>
    <td width="50%" ><font color="#FF0000">INTEREST WILL BE CHARGERD ON OVERDUE ACCOUNTS.</font> Accounts that are overdue for a total of Three Calendar months will be blocked from payment. An accrued interest of 10% of the amount Overdue Per annum  </td>
  </tr>
</table><center>
<a href="cashoutstatement_print.php?id=<?php echo $stand;?>" target="_blank" class='btn btn-success'><i class='icon-file-alt icon-large'></i>&nbsp; <strong>CLICK TO PRINT</a></strong>
</td>
  </tr>
</table>

</body>
</html>