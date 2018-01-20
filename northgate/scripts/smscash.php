<?php
$overal=mysql_query("SELECT Count(clients.id) AS tot FROM `clients` ");
while($rowo = mysql_fetch_array($overal))
  { 
$num=$rowo['tot'];
$cash=$num*0.05;
echo"NUMBER OF CLIENTS IS <strong>$num</strong><br>
<strong>$ $cash </strong>IS REQUIRED";
  }
 ?>