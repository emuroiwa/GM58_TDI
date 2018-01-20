<?php
$stand=$_GET['id'];


$r1 = mysql_query("SELECT * FROM stand where id_stand='$stand'")or die(mysql_query());
   $rows = mysql_num_rows($r1);
 while($rw1 = mysql_fetch_array($r1, MYSQL_ASSOC)){
  $price=$rw1['price']; 
  $instalments=$rw1['instalments'];
  $deposit=$rw1['deposit'];
  }
  
  $r2 = mysql_query("SELECT * FROM payment  ORDER BY payment.id DESC LIMIT 1")or die(mysql_query());
 while($rw2 = mysql_fetch_array($r2, MYSQL_ASSOC)){
  $cash=$rw2['cash'];
   $month1=$rw2['month'];
   $year1=$rw2['year'];
     if(mysql_num_rows($r2)==0){$msg="..  ";}
	 else{
    $msg="Instalments was last paid for <strong>$month1</strong> Month <strong>$year1</strong> Year<br>
";}
echo $msg;
  }
   $r3 = mysql_query("SELECT * FROM cash where stand='$stand'")or die(mysql_query());
 while($rw3 = mysql_fetch_array($r3, MYSQL_ASSOC)){
  $cash=$rw3['cash'];
  }
  $balance=$price-$cash;
  echo "Balance is $ $balance<br>Instalments is $ $instalments Monthly<br>
Amount paid so far $ $cash";
if(isset($_POST['Submit'])){
		$stand=$_GET['id'];
		$date = date('d/m/Y');
		if($cash<$_POST['amount'])
		{ ?>
  <script language="javascript">
  alert("Error!!! Refund Or Withdrawal Cannot Be More Than Be More The Cash Paid")
location = 'index.php?page=amend_cash.php&id=<?php echo $_GET['id'];?>'  </script>
  <?php
   //echo "<font color='red'><br>
//Error!!! Refund Or Withdrawal Cannot Be More Than Be More The Cash Paid<br>
//</font>";
		exit;
			
			}
$r1 = mysql_query("SELECT * FROM stand where id_stand='$stand'")or die(mysql_query());
   $rows = mysql_num_rows($r1);
 while($rw1 = mysql_fetch_array($r1, MYSQL_ASSOC)){
  $price=$rw1['price'];
  }  
  $r2 = mysql_query("SELECT * FROM cash where stand='$stand'")or die(mysql_query());
 while($rw2 = mysql_fetch_array($r2, MYSQL_ASSOC)){
  $cash=$rw2['cash'];
  }
 // tota payment kubva kudhara + payment yanhasi = total
  $payment=$cash-$_POST['amount'];
  //balance = stand price-total line 16
  $balance=$price+$_POST['amount'];
  // if payment in progress update


  if(mysql_num_rows($r2)==1){
   mysql_query("update cash set date='$date',cash='$payment' where stand='$stand'") or die (mysql_error());
         mysql_query("insert into amendments(date,cash,stand,capturer,month,year,authority,a_type) values('$date','$_POST[amount]','$stand','$_SESSION[name]','$month','$year','$_SESSION[name]','CashOut')") or die(mysql_error());
		 		 if($payment==$price){ 
		   mysql_query("update stand set status='Payment_Complete' where id_stand='$stand'") or die (mysql_error());
		   	 header("location: receipt2.php?id=$stand&price=$price&cash=$cash&payment=$payment&balance=$balance"); 

}
		 		   	 header("location: receipt2.php?id=$stand&price=$price&cash=$cash&payment=$payment&balance=$balance"); 
// if full payment change status to .......



   }
   // if nt in progress insert
  if(mysql_num_rows($r2)==0){
   mysql_query("insert into cash(date,cash,stand) values('$date','$payment','$stand')") or die(mysql_error());
      mysql_query("insert into cashout(date,cash,stand) values('$date','$_POST[amount]','$stand')") or die(mysql_error());
		   mysql_query("update stand set status='Payment_In_Progress' where id_stand='$stand'") or die (mysql_error());
		 if($payment==$price){ 
		   mysql_query("update stand set status='Payment_Complete' where id_stand='$stand'") or die (mysql_error());
		   	 header("location: receipt2.php?id=$stand&price=$price&cash=$cash&payment=$payment&balance=$balance"); 

}
		   	 header("location: receipt2.php?id=$stand&price=$price&cash=$cash&payment=$payment&balance=$balance"); 

	  }

}
 ?>
<script type="text/javascript">
function MM_validateForm() { //v4.0
  if (document.getElementById){
    var i,p,q,nm,test,num,min,max,errors='',args=MM_validateForm.arguments;
    for (i=0; i<(args.length-2); i+=3) { test=args[i+2]; val=document.getElementById(args[i]);
      if (val) { nm=val.name; if ((val=val.value)!="") {
        if (test.indexOf('isEmail')!=-1) { p=val.indexOf('@');
          if (p<1 || p==(val.length-1)) errors+='- '+nm+' must contain an e-mail address.\n';
        } else if (test!='R') { num = parseFloat(val);
          if (isNaN(val)) errors+='- '+nm+' must contain a number.\n';
          if (test.indexOf('inRange') != -1) { p=test.indexOf(':');
            min=test.substring(8,p); max=test.substring(p+1);
            if (num<min || max<num) errors+='- '+nm+' must contain a number between '+min+' and '+max+'.\n';
      } } } else if (test.charAt(0) == 'R') errors += '- '+nm+' is required.\n'; }
    } if (errors) alert('The following error(s) occurred:\n'+errors);
    document.MM_returnValue = (errors == '');
} }
</script>
<form action="" method="post" name="qualification_form" onSubmit="MM_validateForm('amount','','RinRange0:999999999');return document.MM_returnValue" target="new">
<center>

<table width="50%" border="0" align="center" style="border-bottom:3px solid #000000;">
 
 
      <tr>
        <td><div align="center"><span class="style7"><strong>Payments</strong></span></div></td>
       
      </tr>
      
  </table> 
    <div class="errstyle" id="errr"></div>
    <div class="errstyle" id="err"></div>
 <table width="100%">
</table>

  
  <table width="50%" align="center">
<tr>
  <td width="109"> <span class="style1 style9">Amount in dollars</span></td>
  <td width="150">
    <input type="text" name="amount" id="amount"/></td>
</tr>
          
</td></tr>

<tr><td colspan="2"  align="center"><div align="center">
  <input type="submit" name="Submit" size="30"  value="Save"/>
</div></td>
</tr>
</table>
</form>
<div align="center">
  <?php 
$qry=mysql_query("select * from owners,clients,stand where owners.stand_id='$_REQUEST[id]' and clients.id=client_id and id_stand=owners.stand_id")or die(mysql_error());
while($info=mysql_fetch_array($qry)){	
?>
  <hr>
  <strong>Owner Details.</strong>

<table border="0" width="50%">
  <tr><td><strong>Owner Name</strong></td><td><?php echo $info['name']." ".$info['surname']; ?></td></tr>
<tr><td><strong>Contact Number</strong></td><td><?php echo $info['contact']; ?></td></tr>
<tr><td><strong>Address</strong></td><td><?php echo $info['address']; ?></td></tr>
<tr><td><strong>Email</strong></td><td><?php echo $info['email']; ?></td></tr>
</table></div>
<?php
}
?>