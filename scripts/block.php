       <?php  mysql_query("insert into payment(date,cash,stand,capturer,month,year,payment_type) values(NOW(),'$_GET[a]','$_GET[id]','$_SESSION[name]','$month','$year','Accruals')") or die(mysql_error());
	   	 echo ("<SCRIPT LANGUAGE='JavaScript'> window.alert('Accruals Of $ $_GET[a] Recieved . ACCOUNT UNBLOCKED!!!! ')
		  location='index.php?page=sale.php&id=$_GET[id]'
		 	</SCRIPT>");  
		  
?>