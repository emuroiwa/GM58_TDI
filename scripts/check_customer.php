<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Untitled Document</title>
</head>

<body><center><h3>CLIENT TYPE?</h3>
<table width="100%" border="0">
  <tr>
  <?php if($_GET['type']=="R"){
  	$a=$_GET['id']."&type=R";}
  	else{
  		  	$a=$_GET['id'];

  	
  	}?>
    <td align="center"><a href="index.php?page=oldclients.php&id=<?php echo $a;?>"> <strong>CLICK HERE IF OLD CUSTOMER</strong></a></td>
    <td align="center"><a href="index.php?page=reg.php&id=<?php echo $a;?>"><strong> CLICK HERE IF NEW CUSTOMER</strong></a></td>

  </tr>
</table>

</body>
</html>