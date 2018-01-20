<center><h3>List Of Payments</h3><table width="100%" border="1">
					  <tr bgcolor="white"> 
                                  <td  width="40">Stand #</td>
                       <td  width="53">Location</td>
                                                <td  width="63">Area</td> 
<td  width="86">Capturer</td>
					   <td  width="46">Months</td>
                        <td  width="89">Description</td> 
                        <td  width="48">Amount</td>
                        <td  width="29">Date</td> <td  width="34">EDIT</td>
   								 <?php
	
	  $result ="";
	 $result = mysql_query("SELECT * FROM stand,payment  WHERE 
payment_type='Deposit'  and payment.stand=id_stand and stand='$_GET[id]'  order by STR_TO_DATE(payment_date,'%d/%m/%Y') ASC ")or die(mysql_query());
		 
	   if(!$result)
{
	die( "\n\ncould'nt send the query because".mysql_error());
	exit;
}
	$rows = mysql_num_rows($result);
	if($rows==0)
 {
 	echo("<SCRIPT LANGUAGE='JavaScript'> window.alert('No report for this period')
		  javascript:history.go(-1)
		 	</SCRIPT>");  
			exit;

 }
 		
	
  while($row = mysql_fetch_array($result, MYSQL_ASSOC))
	  	  
{

		
		
echo "<tr><td>{$row['number']}</td><td>{$row['location']}</td><td>{$row['area']} (sqm)</td><td>{$row['capturer']}</td><td>{$row['month']}</td><td>{$row['d']}</td><td>$ {$row['cash']}</td><td>{$row['payment_date']}</td><td><a href='index.php?page=amend_payments.php&id=$row[id_stand]&pay=$row[id]&a=$row[cash]&m=$row[month]&d=$row[payment_date]'>[EDIT]</a></td></tr>";
}


?>       </tr></table>