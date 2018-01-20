<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Untitled Document</title>
</head>

<body>
<?php
  $r = mysql_query("SELECT
*
FROM clients
")or die(mysql_query()); 
while($rw1 = mysql_fetch_array($r, MYSQL_ASSOC)){
	$p=clean(str($rw1['contact']));
	$n=clean(str($rw1['name']));
	$s=clean(str($rw1['surname']));
	$e=clean(str($rw1['ecnum']));
	$d=clean(str($rw1['date']));
	mysql_query("insert into client (name,surname,contact,date,ecnum) values('$n','$s','$p','$date','$e')")or die(mysql_error());
	}
?>
</body>
</html>