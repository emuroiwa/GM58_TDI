<html>
<head>

<body onLoad="print()">
<?php
include_once ('funcs.php');
include_once ('opendb.php');include_once ('../functions.php');

    $page = (int) (!isset($_GET["current"]) ? 1 : $_GET["current"]);
    $limit = 3;
    $startpoint = ($page * $limit) - $limit;

    //to make pagination
  $sql = "owners,clients,stand where client_id='$_REQUEST[client_id]' and owners.client_id = clients.id AND
owners.stand_id = stand.id_stand ";
  ?>
<div class="records round">


  <ul class="homelist">
<?php

        //show records
 $query = mysql_query("SELECT * FROM {$sql} LIMIT {$startpoint} , {$limit}");
echo pagination($sql,$limit,$page,'index.php?page=clients.php&'); 
while ($info = mysql_fetch_assoc($query)) { 
?>


<div align="center"><strong><img src="../images/letterhead.png">Statement</strong>
</div>
<hr>
<table width="97%" bgcolor="#A7A7A7" border="0">
<tr><td colspan="4"><div align="center"><font color="black"><strong>Client Personal Details.</strong></font>
  </div></td></tr>
  <tr>
    <td width="109"><strong>NAME</strong></td>
    <td width="136"><?php echo $info['name']; ?></td>
    <td width="110"><strong>SURNAME</strong></td>
    <td width="136"><?php echo $info['surname']; ?></td>
  </tr>
  <tr>
    <td><strong>SEX</strong></td>
    <td><?php echo $info['sex']; ?></td>
    <td><strong>I.D NUM</strong></td>
    <td><?php echo $info['idnum']; ?></td>
  </tr>
  <tr>
    <td><strong>TEL:NUMBER</strong></td>
    <td><?php echo $info['contact']; ?></td>
    <td><strong>EMPLOYER</strong></td>
    <td><?php echo $info['employer']; ?></td>
  </tr>
  <tr>
    <td><strong>ADDRESS</strong></td>
    <td colspan="3"><?php echo $info['address']; ?></td>
  </tr>
  <tr><td colspan="4"><div align="center"><font color="black"><strong>Stand Details.</strong></font>
  </div></td></tr>
  <tr><td ><strong>Date Bought</strong></td><td ><strong>Location</strong></td><td ><strong>Area</strong></td><td ><strong>Price</strong></td></tr>
  <?php
  $qry=mysql_query("select * from stand where id_stand ='$_REQUEST[stand_id]'");
  $more=mysql_fetch_array($qry);
  ?>
  <tr><td ><?php echo $info['date']; ?></td><td ><?php echo $more['location']; ?></td><td ><?php echo $more['area']; ?></td><td ><?php echo "$".$more['price']; ?></td></tr>
  <tr><td colspan="4"><div align="center"><font color="black"><strong>Payment Details.</strong></font>
  </div></td></tr>
  <tr><td ><strong>Payment Date</strong></td><td ><strong>Capturer</strong></td><td ><strong>Amount</strong></td><td ><strong>Description</strong></td></tr>
  <?php
  $qrry=mysql_query("select * from payment where stand='$_REQUEST[stand_id]'");
  while($info=mysql_fetch_array($qrry)){
	    if($info['payment_type']!='Deposit' and $info['payment_type']!='Credit'){$a="($".$info['cash'].")";}
   else{$a="$".$info['cash'];}
  ?>
  <tr><td bgcolor="white"><?php echo $info['date']; ?></td><td bgcolor="white"><?php echo $info['capturer']; ?></td><td bgcolor="white"><?php echo "$".$info['cash']; ?></td><td bgcolor="white"><?php echo $info['d']; ?></td></tr>
  
  <?php
  }
  ?>
  <tr><td><strong>Total</strong></td><td></td><td></td><td> <?php
	 $credit=getcredit($_GET['stand_id']);
  echo "$".$credit;
  ?></td></tr>
  <tr bgcolor="white"><td><strong>Balance</strong></td><td></td><td></td><td> <?php
$debit=getdebit($_GET['stand_id']);
	$balance=$debit-$credit;
  echo "$".$balance;
  ?></td></tr>
  <tr>
    <td colspan="4"><div align="center"><font color="red"><strong>Status is '<?php echo $more['status']; ?>'.</strong></font></div></td></tr>
</table><hr>
<?php
$cont=mysql_query("select * from clients where id='$_REQUEST[client_id]' or stand_id='$_REQUEST[stand_id]'");
$now=mysql_fetch_array($cont);
?>

</table></div>
<?php

 }   ?>
 </body></head></html> 