<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Untitled Document</title>
</head>

<body>
<?php
 mysql_query("DELETE FROM `clients` WHERE id= '$_GET[id]'");

 			 WriteToLog("Deleted client ID $_REQUEST[id]",$_SESSION['username']);

  ?>
  <script language="javascript">
  alert("Deleted............")
		  location = 'index.php?page=find.php'
  </script>
  <?php
  

  
?>
</body>
</html>