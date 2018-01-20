<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Reserved Statement</title>
</head>
<style>
table{border-collapse:collapse}
body{
font: 14px Arial, sans-serif;
}
</style>
<body onLoad="print()"><center><table width="97%" border="0">
  <tr>
    <td><img src="../images/letterhead.png"></td>
  </tr>
</table><strong>BP #:200120952<br>
VAT #:10062313
</strong>
<hr>
<?php   //debit
error_reporting(0);
include ('../aut.php');
	 include ('../opendb.php');
	 include ('../functions.php');
	  //include ('process.php'); 
	  ?><h3 align="center">COMPREHENSIVE LEDGER RESERVED AS AT <?php echo $new;?></h3><hr>
<?php
	   //debit
//$stand=$_GET['id'];
$overal=mysql_query("SELECT * FROM `stand` WHERE status = 'reserved' order by number asc ");
while($rowo = mysql_fetch_array($overal))
  { 
$stand=$rowo['id_stand'];
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
  </tr>
</table>
      </td>
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