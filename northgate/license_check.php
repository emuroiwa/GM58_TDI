 <?php 
 
		$date = date('m/d/Y');
 $r1 = mysql_query("SELECT * FROM expire where id='1'")or die(mysql_query());
   $rows = mysql_num_rows($r1);
 while($rw1 = mysql_fetch_array($r1, MYSQL_ASSOC)){
  $expire=$rw1['expiredate'];
  }
 if(strtotime($date)>=strtotime($expire)){
	 
	  header("location: ../mariyedu.php"); 
	 }
  
   ?>