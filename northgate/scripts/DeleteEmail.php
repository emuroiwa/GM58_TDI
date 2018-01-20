
<?php
$r = mysql_query("SELECT * FROM emails where email_id='$_GET[id]' ")or die(mysql_query());
 while($row = mysql_fetch_array($r, MYSQL_ASSOC)){
  $cash=$row['name'];
  $stand=$row['surname'];
  $d=$row['email'];
  $payment_date=$row['payment_date'];

   }
$qry = mysql_query("SELECT * FROM users where id='$_GET[userid]' ")or die(mysql_query());
 while($row = mysql_fetch_array($qry, MYSQL_ASSOC)){
  $cash=$row['name'];
  $stand=$row['surname'];
  $d=$row['email'];
  $payment_date=$row['department'];

   }
   if($_GET['userid']==""){
	    mysql_query("DELETE FROM `emails` WHERE email_id= '$_GET[id]'");
	   			//Write to log file
			 WriteToLog("Deleted Email  Name $cash | Surname  $stand | email $d ",$_SESSION['username']);

   }else{
	   	  mysql_query("DELETE FROM `users` WHERE id= '$_GET[userid]'");
		     			//Write to log file
			 WriteToLog("Deleted User  Name $cash | Surname  $stand | email $d ",$_SESSION['username']);
 
	   }
  ?>
  <script language="javascript">
  alert("Deleted..............");
history.go(-1);    </script>
