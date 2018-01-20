<?php
@session_start();
//$txtmembernumber=$_SESSION['member'];
//$txtname=$_SESSION['number'];
//$q=$_GET["q"];
include 'opendb.php';
$sql="SELECT * FROM `stand` WHERE  status <> 'Payment_Complete'  order by number asc";

$result = mysql_query($sql);
	$rows = mysql_num_rows($result);
	if($rows==0)
 {
 	echo("<font color='#FF0000'>No results </font> ");  
			exit;

 }
echo "
<center><strong>Click Make Payment To Process Payment</strong><table border='0' align='center' >
<tr bgcolor='#999999'>
<th>Number</th>
<th>Location</th>
<th>Area In sqm</th>
<th>Date</th>
<th bgcolor='red'>STATUS</th>
<th>Make Payment</th>
</tr>";

while($row = mysql_fetch_array($result))
  { 
  echo "<tr>";
  echo "<td>" . $row['number'] . "</td>";
  echo "<td>" . $row['location'] . "</td>";
  echo "<td>" . $row['area'] . "</td>";
  echo "<td>" . $row['date'] . "</td>";
  echo "<td>" . $row['status'] . "</td>";
  if($row['status'] =='For_Sale'){$a="<a href='index.php?page=check_customer.php&id=$row[id_stand]'>[Make Deposit]</a>";}else {$a="<a href='index.php?page=sale.php&id=$row[id_stand]'>[Make Payment]</a>";}
  echo "<td>$a</td>";
  echo "</tr>";
  }
echo "</table>";

?>