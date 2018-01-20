<?php
$stand=$_GET['id'];


$r1 = mysql_query("SELECT * FROM stand where id_stand='$stand'")or die(mysql_query());
   $rows = mysql_num_rows($r1);
 while($rw1 = mysql_fetch_array($r1, MYSQL_ASSOC)){
  $price=$rw1['price']; 
  $instalments=$rw1['instalments'];
  $deposit=$rw1['deposit'];$standno=$rw1['number'];
  }
  //Deposit so far
 $r = mysql_query("SELECT Sum(payment.cash) AS tot FROM payment where stand='$stand' and d='Stand Deposit'")or die(mysql_query());
    while($rw = mysql_fetch_array($r, MYSQL_ASSOC)){
  $deposit_so_far=$rw['tot']; 
 } 
/* echo $deposit_so_far; echo $deposit;
 if($deposit_so_far!=$deposit){
	 echo "sdfu";
	 }
exit; */ 	$diff=$deposit-$deposit_so_far;
if($diff!=0){
	 
		echo ("<SCRIPT LANGUAGE='JavaScript'> window.alert('Please Make A Deposit Of $$diff first!!!!!!!!!!!!!!')
		 location='index.php?page=deposit.php&id=$stand'
		 	</SCRIPT>"); exit;
		//msg('Please Make A Deposit Of $$diff first!!!!!!!!!!!!!!');
			//	link1('index.php?page=deposit.php&id=$stand');
	 }
  
  //debit
  $debit=getdebit($stand);
	
	//acruals
	$r3 = mysql_query("SELECT
*
FROM `payment`
WHERE
payment.payment_type = 'Accruals' AND
payment.`month` = '$month' and stand='$stand' ")or die(mysql_query());
 while($rw3 = mysql_fetch_array($r3, MYSQL_ASSOC)){
  $accrual=$rw3['cash'];
    }
	
	//credit
	 $credit=getcredit($stand);

	$balance=$debit-$credit;
	$block=$balance/$instalments;
	//echo $block;
	// checking to block
	$accrued=round(($balance*0.1)/12,2);
  if($block>=3  && $balance>$instalments && $accrual!=$accrued ){echo "<center><font color='#FF0000'>Payment Blocked</font><table width='60%'>
  <tr>
    <td>AMOUNT REQUIRED IS</td>
    <td><strong>$ $accrued</strong></td>
  </tr>
  <tr>
    <td>Balance B/f</td>
    <td>$ $balance</td>
  </tr>
  <tr>
    <td>Instalments </td>
    <td>$ $instalments</td>
  </tr>
  <tr>
    <td>Payments</td>
    <td>$ $credit</td>
  </tr>
  
</table>
 <br>
<a href='index.php?page=block.php&id=$stand&a=$accrued'  onclick='return confirm(\"UNBLOCK ONLY IF $$accrued HAS BEEN PAID \")' >
<strong>>>>CLICK TO UNBLOCK ACCOUNT<<<</strong></a>

</center>"; exit;}
//form
$lastmonth=lastmonth($stand);
$lastcash=lastcash($stand,$lastmonth);
  echo "Stand # <strong> $standno</strong><br>
Last Paid Month <strong> $lastmonth  = $ $lastcash</strong><br>Instalments is <strong>$ $instalments</strong> Monthly<br>
Instalments so far <strong>$ $credit</strong><br>
Deposit paid <strong>$ $deposit</strong>";



//processing forme
if(isset($_POST['Submit'])){
		$stand=$_GET['id'];
		$date = date('d/m/Y');
$post_amount=clean($_POST['amount']);
$post_month=clean($_POST['month']);
		  $totalpaid=$deposit+$credit+$post_amount;
		   $totalpaid1=$deposit+$credit;
		  $required=$price-$totalpaid1;
$lastmonth=lastmonth($stand);
$lastcash=lastcash($stand,$lastmonth);
$nowcash=$lastcash+$post_amount;
$instalment_out=$instalments-lastcash2($stand,$lastmonth);
$diffcash=$post_amount-$instalment_out;
if($_POST['date1']=="-"){
		$valuedate="";

	//	exit;
	}
else{
			$valuedate="Value At ".$_POST['date1'];

	//exit;
	}
 //checking if amount tendered is larger than credit
		  if($totalpaid>$price){
			?><SCRIPT LANGUAGE='JavaScript'> window.alert('Amount Tendered Is Larger Than The Required<?php echo 
			" $ $price Required Amount Is $ $required";?>')
		 javascript:history.go(-1)
		 	</SCRIPT>"<?php 
			exit; 
			  }
			  $postamount_balance=$post_amount-$instalment_out;
			  $total_balance=$instalments-$post_amount;
		 // echo $totalpaid; exit;
   $b=$balance-$postamount_balance;
     //spliting payments into months being paid
  $reminder=$postamount_balance%$instalments;
$months_paid=floor($postamount_balance/$instalments);
$reminder2=$diffcash%$instalments;
$months_paid2=floor($diffcash/$instalments);
echo $instalments;
echo "<br>
";echo $postamount_balance;
echo "<br>
";echo $months_paid;
echo "<br>
";
echo $postamount_balance;
echo "<br>
";
echo $reminder;
		  if($postamount_balance<$instalments and $total_balance > 0){
		      pay($total_balance,$stand,nextmonth(currentmonth($stand)),$_POST['date'],'Instalment Payment11',$valuedate); 
	}
	if($instalment_out<>0 and $post_amount> $instalment_out){
		      pay($instalment_out,$stand,lastmonth($stand),$_POST['date'],'Instalment Payment11',$valuedate); 
	
	}
if($instalment_out<>0 and $post_amount<= $instalment_out){
		      pay($post_amount,$stand,lastmonth($stand),$_POST['date'],'Instalment Payment1',$valuedate); 
	 		}
 if($months_paid>=1){
	 for($i=1;$i<=$months_paid;$i++){
 $lastmonth1="20-".$lastmonth;
$timestamp = strtotime($lastmonth1); 
$new_date = date('d-m-Y', $timestamp);
				 $month_now = strtotime(date($new_date));
			$nextmonth = date("F-Y", strtotime("+$i month", $month_now));			
//loop thru months
      pay($instalments,$stand,$nextmonth,$_POST['date'],'Instalment Payment2',$valuedate);
	 }
	 if($reminder>0){
	 	//kana pane remaider inoiswa mu the last month
      pay($reminder,$stand,nextmonth(lastmonth($stand)),$_POST['date'],'Instalment Payment3',$valuedate); 
	 }
	 }

   //2
   
  		 if($totalpaid==$price){ 
		   mysql_query("update stand set status='Payment_Complete',datestatus='$date' where id_stand='$stand'") or die (mysql_error());
		   	msg('Payment Successfull');
			 header("location: index.php?page=sale&id=$stand"); 
				//link1('index.php'); // header("location: receipt.php?id=$stand&price=$price&balance=$b&d=$deposit&t=$totalpaid&a=$_POST[amount]"); 

}
		 		msg('Payment Successfull');
				header("location: index.php?page=sale&id=$stand"); 
				//link1('index.php');   //	 header("location: receipt.php?id=$stand&price=$price&balance=$b&d=$deposit&t=$totalpaid&a=$_POST[amount]"); 


exit; 
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
jQuery(function($){
$("#date1").datepicker();
});
</script>

<form action="" method="post" name="qualification_form" onSubmit="MM_validateForm('amount','','RinRange0:999999999');return document.MM_returnValue" >
<center>

<table width="50%" border="0" align="center" style="border-bottom:3px solid #000000;" bgcolor="#FFFFFF">
 
 
      <tr>
        <td><div align="center"><span class="style7"><strong>Payments</strong></span></div></td>
       
      </tr>
      
  </table> 
    <div class="errstyle" id="errr"></div>
    <div class="errstyle" id="err"></div>
 <table width="100%">
</table>

  
  <table width="50%" align="center" bgcolor="#FFFFFF">
<tr>
  <td width="109"> <span class="style1 style9">Amount In Dollars :</span></td>
  <td width="150">
    <input type="text" name="amount" id="amount"/></td>
</tr><!--<tr>
  <td width="109"> <span class="style1 style9">Months Being Paid For:</span></td>
  <td width="150">
    <input type="text" name="month" id="month"/></td>
</tr>--><tr>
  <td width="109"> <span class="style1 style9">Payment Date Was :</span></td>
  <td width="150">
    <input type="text" name="date" id="date" /></td>
</tr><tr>
  <td width="109"> <span class="style1 style9">Value Date Was :</span></td>
  <td width="150">
    <input type="text" name="date1" id="date1" value="-"/></td>
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
<tr><td><strong>Date Of Birth</strong></td><td><?php echo $info['dob']; ?></td></tr>
</table></div>
<?php
}
?>