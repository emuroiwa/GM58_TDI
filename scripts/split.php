<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Untitled Document</title>
</head>

<body>
<?php 
//$date33 = strtotime(date("d/m/Y", strtotime($date)) . " +1 month");
		//echo $date33;
$in=151;
$a=1000%$in;
$b=floor(1000/$in);
$b-=1;
//echo $b;
//$c=floor($b);
for($i=0;$i<=$b;$i++){
	$date = '14/12/14';
$date = str_replace('/', '-', $date);
		
		 $month = strtotime(date($date));
	
		
$nextmonth = date('F-Y', strtotime("-$i month", $month));

if($a!=0 and $i==$b){
	echo "$nextmonth------$a<br>";
	}
echo "$nextmonth------$in<br>";
}
?>
</body>
</html>