<?php
/*$r2 = mysql_query("SELECT * FROM payment ")or die(mysql_query());
    while($rw2 = mysql_fetch_array($r2, MYSQL_ASSOC)){
    	$xx=strlen($rw2['payment_date']);
    	if($xx==9){
$date=substr($rw2['payment_date'], 0,2);
    	$month=substr($rw2['payment_date'], 3,2);
    	$year=substr($rw2['payment_date'], 6,2);
    	if($date>12){
    		if(strlen($year)<4){
    		$newdate="20".$year."-".$month."-".$date." 00:00:00";
	
    		}else{
$newdate=$year."-".$month."-".$date." 00:00:00";
}
echo $rw2['payment_date']."|".$date." -".$month." -".$year."| $newdate<br> $rw2[id]";

// mysql_query("update payment set payment_date='$newdate' where id='$rw2[id]' ")or die(mysql_query());
    	}
    	}
    	
    }*/

    $r2 = mysql_query("SELECT * FROM payment  ")or die(mysql_query());
    while($rw2 = mysql_fetch_array($r2, MYSQL_ASSOC)){
        $xx=strlen($rw2['payment_date']);
        if($xx>=10){
$date=substr($rw2['payment_date'], 0,2);
        $month=substr($rw2['payment_date'], 3,2);
        $year=substr($rw2['payment_date'], 6,4);
        if($year<=10){
            if(strlen($year)==2){
           $newdate="20".$year."-".$month."-".$date." 00:00:00";
            //$newdate=$year."-".$month."-".$date." 00:00:00";
    
            }else{
$newdate=$year."-".$month."-".$date." 00:00:00";
}
echo $rw2['payment_date']."|".$date." -".$month." -".$year."| $newdate ---- $rw2[id]<br>";

     //mysql_query("update payment set payment_date='$newdate' where id='$rw2[id]' ")or die(mysql_query());
        }
        }
        
    }
?>