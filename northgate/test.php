<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Untitled Document</title>
</head>

<body><?php  $date = date('02/11/2012');
		 $start = strtotime('02/15/2012');
$end = strtotime('03/11/2012');
$days_between = ceil(abs($start - $end) / 2592000); 
echo $days_between;

echo 10/3;
?>
</body>
</html>