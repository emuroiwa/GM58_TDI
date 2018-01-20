
<?php
include 'opendb.php';include '../functions.php';
  //addresss
  $n2 = mysql_query("SELECT * FROM payment,stand where stand.id_stand=payment.stand and value_date<>''")or die(mysql_query());
 while($nw2 = mysql_fetch_array($n2, MYSQL_ASSOC)){

  $instalments=$nw2['instalments'];
   $purchasedate=$nw2['datestatus'];
   $stand=$nw2['stand'];
   $CumulativeDebit=getcredit($nw2['stand']);
      $monthz=GetInstalmentMonthDate(formatdate($purchasedate),GetMonthsPaid($CumulativeDebit,$instalments));
     $rr=mysql_query("update payment set value_date='$monthz' where stand='$stand'") or die (mysql_error());
      echo mysql_affected_rows($rr);
    }
   
		
 ?>
 