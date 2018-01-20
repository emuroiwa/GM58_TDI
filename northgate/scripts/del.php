
<?php
$r = mysql_query("SELECT * FROM payment where id='$_GET[id]' ")or die(mysql_query());
 while($row = mysql_fetch_array($r, MYSQL_ASSOC)){
  $cash=$row['cash'];
  $stand=$row['stand'];
  $d=$row['d'];
  $payment_date=$row['payment_date'];

   }
  /*$entry = mysql_query("SELECT * FROM payment where month='$payment_month' and stand='$_GET[stand]' ")or die(mysql_query());
$number_of_entry=mysql_num_rows($entry);
//echo $number_of_entry;
//exit;
if($number_of_entry!=1){
	 mysql_query("DELETE FROM `payment` WHERE id= '$_GET[id]'");
	}
	else{
  $r1 = mysql_query("SELECT * FROM payment where stand='$_GET[stand]'")or die(mysql_query());
   $rows = mysql_num_rows($r1);
 while($rw1 = mysql_fetch_array($r1, MYSQL_ASSOC)){
  $month=$rw1['month'];  
  $id=$rw1['id']; 
 $lastmonth=lastmwedzi($month);

 mysql_query("UPDATE payment set month = '$lastmonth' where id='$id and payment_type='Credit'  '");
 }*/
 
 mysql_query("DELETE FROM `payment` WHERE id= '$_GET[id]'");
			//Write to log file
			 WriteToLog("Deleted All Payment  Cash $cash | Stand ID $stand | Description $d | Payment Date $payment_date",$_SESSION['username']);
  ?>
  <script language="javascript">
  alert("Deleted..............");
history.go(-2);    </script>
