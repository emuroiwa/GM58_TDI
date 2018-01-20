<?php 
$date1 = new DateTime("2015-04-31");
$date2 = new DateTime(date('Y-m'));
$interval = date_diff($date1, $date2);
$xx=$interval->m + $interval->y * 12;
echo $interval->m ;
?>