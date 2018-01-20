<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Client Statement</title>
</head>
<style>
table{border-collapse:collapse}
body{
font: 14px Arial, sans-serif;
}
</style>
<body ><center><table width="97%" border="0">
  <tr>
    <td><img src="../images/letterhead.png"></td>
  </tr>
</table><strong>BP #:200120952<br>
VAT #:10062313
</strong>
<hr>
<?php   //debit

include ('../aut.php');
	 include ('../opendb.php');
	 include ('../functions.php');
	  //include ('process.php'); 
	  ?><h3 align="center">COMPREHENSIVE LEDGER FULLY PAID AS AT <?php echo $new;?></h3><hr>
<?php
	   //debit
//$stand=$_GET['id'];
$overal=mysql_query("SELECT * FROM `stand` WHERE status = 'Payment_Complete' order by number asc ");
while($rowo = mysql_fetch_array($overal))
  { 
$stand=$rowo['id_stand'];
//echo "$stand<br>
//";}
//exit;
if(($stand%2)==1){
	$color="";}
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
  $instalments=$nw2['instalments'];
    }
	//purchase date
	$pd = mysql_query("SELECT
*
FROM payment
where stand='$stand' and d='Stand Deposit'
")or die(mysql_query());
 while($rowpd = mysql_fetch_array($pd, MYSQL_ASSOC)){
 $purchasedate=$rowpd['payment_date'];
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
    <td></td>
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
	  $ff= $nw['name']." ".$nw['surname'];
  echo $nw['name']." ".$nw['surname'].",<br>
";
    }?><?php echo $add;?><br>
<?php echo $con;?><br>
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
<table width="100%" border="1">
  <tr bgcolor="#CCCCCC">
   <strong> <td width="8%"><strong>Date</strong></td>
    <td width="17%"><strong>Month </strong></td>
    <td width="39%"><strong>Description</strong></td>
    <td width="18%"><strong>Debit</strong></td>
  <td width="18%"><strong>Credit</strong></td>
 
    <td width="18%"><strong>Balance</strong></td>
    </strong>
  </tr>
  <?php
   $result = mysql_query("SELECT
*
FROM `payment`
WHERE
payment.stand='$stand' and payment_type='credit' order by STR_TO_DATE(payment_date,'%d/%m/%Y') ASC  ")or die(mysql_query());

		 
	   if(!$result)
{
	die( "\n\ncould'nt send the query because".mysql_error());
	exit;
}
	$rows = mysql_num_rows($result);
	//i dnt noe wats happenin neh
	/*if($rows==0)
 {
 	echo("<SCRIPT LANGUAGE='JavaScript'> window.alert('No statement avalible')
		  javascript:history.go(-1)
		 	</SCRIPT>");  
			exit;

 }*/
	$balance2=$price-$deposit;
  while($row = mysql_fetch_array($result, MYSQL_ASSOC))
	  	  
{
	

	$s=debit($row['payment_type'],$row['cash']);
	$s1=credit($row['payment_type'],$row['cash']);	
$creditbalance=$balance2;
	$balance2=$balance2-$s;	//$balance2=$balance2+$s1;	
echo "<tr><td>{$row['payment_date']}</td><td>{$row['month']}</td><td>{$row['d']} {$row['value_date']}</td><td>$s</td><td>$creditbalance</td><td>$balance2</td></tr>";
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
    <td><?php if($balance==0){echo "<strong>PAYMENT COMPLETE</strong>";}
	else{echo "<strong> PAYMENT IN PROGRESS</strong>";}?><!--<strong>AMOUNT DUE THIS MONTH = <?php echo $balance_month;?> </strong>--></td>
    <td></td>
    <td><strong>AMOUNT DUE</strong></td>
    <td><strong><?php echo $balance;?></strong></td>
    </strong>
  </tr>

</table>

<!--<table width="100%" border="0">
  <tr>
    <td width="50%"><strong>Banking Details</strong><br>
Account Name &nbsp;:TRUE DESTINATIONS INVESTMENTS <br>    
Bank &nbsp;:ZB BANK <br>
Branch &nbsp;:GWERU BRANCH<br>
Account #  :4557 509 125 200</td>
    <td width="50%" ><font color="#FF0000">INTEREST WILL BE CHARGERD ON OVERDUE ACCOUNTS.</font> Accounts that are overdue for a total of Three Calender months will be blocked from payment. An accrued interest of 10% of Amount Overdue Per annum  </td>
  </tr>
</table><center><hr>

<em>Powered By Divine Developers</em>
</td>
  </tr>
</table>
--><center><strong>END OF <?php echo $ff;?>'s STATEMENT</strong></center>===================================================================================================================================================<hr><br>

<br>
<?php }
?>
</body>
</html>