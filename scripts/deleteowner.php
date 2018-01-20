<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Untitled Document</title>
</head>

<body>
<?php
 mysql_query("DELETE FROM `owners` WHERE client_id= '$_GET[id]'");
//Write to log file
			 WriteToLog("Deleted All Payment  Cash $cash | Stand ID $stand | Description $d | Payment Date $payment_date",$_SESSION['username']);
  ?>
  <script language="javascript">
  alert("Ownership Changed.....")
location = 'index.php?page=amend.php'  </script>
  <?php
  

  
?>
</body>
</html>