
<?php

 
 mysql_query("DELETE FROM `payment` WHERE stand= '$_GET[stand]'");
 mysql_query("Update stand set status='For_Sale' where id_stand='$_GET[stand]' ");
 mysql_query("SELECT * FROM `owners` where stand_id='$_GET[stand]' ");
		//Write to log file
			 WriteToLog("Deleted All Payment from stand ID and Set Stand Status To For Sale $_REQUEST[stand]",$_SESSION['username']);
  ?>
  <script language="javascript">
  alert("Deleted..............");
history.go(-2);  </script>
