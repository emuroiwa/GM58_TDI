<?php
@session_start();
//$txtmembernumber=$_SESSION['member'];
//$txtname=$_SESSION['number'];
//$q=$_GET["q"];
include 'opendb.php';
$sql="SELECT * FROM `stand`  order by number asc";

$result = mysql_query($sql);
	$rows = mysql_num_rows($result);
	if($rows==0)
 {
 	echo("<font color='#FF0000'>No results </font> ");  
			exit;

 }
echo "<br />
<center><table border='0' align='center' >
<tr bgcolor='#999999'>
<th>Number</th>
<th>Location</th>
<th>Area In sqm</th>
<th>Price</th>
<th>Deposit</th>
<th>Instalments</th>
<th>STATUS</th>
<th>Date</th><th>Delete</th>



</tr>";

while($row = mysql_fetch_array($result))
  { 
  echo "<tr>";
  echo "<td>" . $row['number'] . "</td>";
  echo "<td>" . $row['location'] . "</td>";
  echo "<td>" . $row['area'] . "</td>";
  echo "<td>" . $row['price'] . "</td>";
  echo "<td>" . $row['deposit'] . "</td>";
  echo "<td>" . $row['instalments'] . "</td>";
    echo "<td>" . $row['status'] . "</td>";
  echo "<td>" . $row['date'] . "</td>";
  if($row['status'] =='For_Sale'){$a="<a href='index.php?page=check_customer.php&id=$row[id_stand]'>[Make Deposit]</a>";}else {$a="<a href='index.php?page=sale.php&id=$row[id_stand]'>[Make Payment]</a>";}
  echo "<td>$a</td>";
  echo "</tr>";
  }
echo "</table>";

?>