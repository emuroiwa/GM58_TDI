<?php include ('aut.php');?>
<?php include ('opendb.php');?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Mari Yedu</title>
</head>

<body><br>
<br>
<br>
<br>
<br>
<br>
<center>
<?php
$r = mysql_query("SELECT * FROM expire where id='1'")or die(mysql_query());
  
 while($rw = mysql_fetch_array($r, MYSQL_ASSOC)){
echo  "<font size='+6' color='#FF0000'>YOUR LICENSE EXPIRED ON <br>
$rw[expiredate]<br>
 PLEASE CONTACT";
  }?>

 MAKPHIN NOW!!!!!!!!!!!!!</font><br>
<br>
<br>
<a href="logout.php"><font size="+6" color="black">LOGOUT</font></a></center>
</body>
</html>