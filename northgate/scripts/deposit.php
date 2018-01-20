<?php
$stand=$_GET['id'];



$r1 = mysql_query("SELECT * FROM stand where id_stand='$stand'")or die(mysql_query());
   $rows = mysql_num_rows($r1);
 while($rw1 = mysql_fetch_array($r1, MYSQL_ASSOC)){
  $price=$rw1['price']; 
  $instalments=$rw1['instalments'];
  $deposit=$rw1['deposit'];
 } 
 //echo $deposit;
 //zvinonetsa izvi

			
			
$deposit_so_far=DepositSoFar($stand);

 $diff=$deposit-$deposit_so_far; 
  echo "Deposit is <strong>$$deposit</strong> <br>Deposit Paid So Far is<strong> $$deposit_so_far</strong><br>Deposit balance Is <strong> $$diff</strong><br>
";
  //form submitted
if(isset($_POST['Submit'])){
		$stand=$_GET['id'];
		$total=$deposit_so_far+$_POST['amount'];
		//$check = mysql_query("SELECT * FROM payment where id_stand='$stand'")or die(mysql_query());
if(ValidateDate($_POST['date'])==false){
		   	msg('Enter Correct Date Format');
	exit;
	}
		if($deposit<$total){
			$diff=$deposit-$deposit_so_far;
		echo ("<SCRIPT LANGUAGE='JavaScript'> window.alert(' A Deposit Of $$diff is required')
		 location='index.php?page=deposit.php&id=$stand'
		 	</SCRIPT>"); exit; }



$mwedzi = date('F-Y', $_POST['date']);
if($deposit_so_far<$deposit){
	 $r2 = mysql_query("SELECT * FROM payment where stand='$stand' and d='Stand Deposit' order by id  asc limit 1")or die(mysql_query());
    while($rw2 = mysql_fetch_array($r2, MYSQL_ASSOC)){
  $mwedzi=$rw2['month']; 
 }
	}
//echo $mwedzi; exit;
		//payment of deposit credit entry
			 WriteToLog("Deposit Payment Amount=$_POST[amount] |Stand = $stand",$_SESSION['username']);
		 mysql_query("insert into payment(date,cash,stand,capturer,month,year,payment_type,payment_date,d) values(NOW(),'$_POST[amount]','$stand','$_SESSION[name]','$mwedzi','$year','Deposit','$_POST[date]','Stand Deposit')") or die(mysql_error());
		 //instalments
       if($total!=$deposit){
		 mysql_query("update stand set status='Deposit_Outstanding',datestatus=NOW() where id_stand='$stand'") or die (mysql_error());
   
		   }else{
		 //changin status
 mysql_query("update stand set status='Payment_In_Progress',datestatus=NOW() where id_stand='$stand'") or die (mysql_error());
		   }
		   	msg('Payment Successfull');
				link1('index.php');
				// header("location: deposit_receipt.php?id=$stand&price=$price&d=$deposit&i=$instalments"); 

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
</script> <!-- Include Core Datepicker Stylesheet -->
<link rel="stylesheet" href="ui.datepicker.css" type="text/css" media="screen" title="core css file" charset="utf-8" />


<!-- Include jQuery -->
<script src="jquery.js" type="text/javascript" charset="utf-8"></script>

<!-- Include Core Datepicker JavaScript -->
<script src="ui.full_datepicker.js" type="text/javascript" charset="utf-8"></script>

<!-- Attach the datepicker to dateinput after document is ready -->
<script type="text/javascript" charset="utf-8">
jQuery(function($){
$("#date").datepicker();
});
</script>


<form action="" method="post" name="qualification_form" onSubmit="MM_validateForm('amount','','RinRange0:999999999','date','','R');return document.MM_returnValue" >


<table width="50%" border="0" align="center" style="border-bottom:3px solid #000000;">
 
 
      <tr>
        <td><div align="center"><span class="style7"><strong>Deposit</strong></span></div></td>
       
      </tr>
      
  </table> 


  <table width="50%" align="center">
<tr>
  <td width="109"> <span class="style1 style9">Amount in dollars</span></td>
  <td width="150">
    <input type="text" name="amount" id="amount" value="<?php echo $diff; ?>"/></td>
</tr>
          

<tr>
  <td width="109"> <span class="style1 style9">Payment Date Was :</span></td>
  <td width="150">
    <input type="text" name="date" id="date" /></td>
</tr>
<tr><td colspan="2"  align="center"><div align="center">
  <input type="submit" name="Submit" size="30"  value="Save" class="btn btn-info"  onclick="return confirm('Are you sure  ?')"/>
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

<?php
}
?></table></div>