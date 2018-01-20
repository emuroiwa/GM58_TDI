<?php
$stand=$_GET['id'];


$r1 = mysql_query("SELECT * FROM stand where id_stand='$stand'")or die(mysql_query());
   $rows = mysql_num_rows($r1);
 while($rw1 = mysql_fetch_array($r1, MYSQL_ASSOC)){
  $price=$rw1['price']; 
  $instalments=$rw1['instalments'];
  $deposit=$rw1['deposit'];
  }
  //debit
$debit=getdebit($stand);		
	 $credit=getcredit($stand);
	   $totalpaid=$deposit+$credit;
	$balance=abs($debit-$credit);
	$five=round($totalpaid*0.05,2);
	echo "Balance B/f <strong>$ $balance</strong><br>
Amount paid so far <strong>$ $totalpaid</strong><br>
5% Admin Cost <strong>$$five</strong>";
if(isset($_POST['Submit'])){
		$stand=$_GET['id'];
		
		  if($totalpaid<$_POST['amount'])
		{ ?>
  <script language="javascript">
  alert("Error!!! Refund Or Withdrawal Cannot Be More Than Be More The Cash Paid")
location = 'index.php?page=amend_cash.php&id=<?php echo $_GET['id'];?>'  </script>
  <?php

		exit;
			
			}
			  $costs=$five+$_POST['amount'];
			 
   $out=$totalpaid-$costs;
   
  
		   mysql_query("insert into amendments(date,cash,stand,capturer,month,year,authority,a_type,cashoutamount) values(NOW(),'$totalpaid','$stand','$_SESSION[name]','$month',NOW(),'$_SESSION[senior]','Cashout','$out')") or die("1".mysql_error());
		 		// if($credit==$credit){ 
				CashOut($stand);
		   mysql_query("update stand set status='For_Sale',datestatus=NOW() where id_stand='$stand'") or die ( "1r".mysql_error());				
		  mysql_query("DELETE FROM `clients` WHERE stand_id= '$stand'");
		   mysql_query("DELETE FROM `owners` WHERE stand_id= '$stand'");
		   mysql_query("DELETE FROM `payment` WHERE stand= '$stand'");
		   
		   			 WriteToLog("Balance Take Up Amount=$balance |Stand = $standno",$_SESSION['username']);

echo ("<SCRIPT LANGUAGE='JavaScript'> window.alert('The Customer Gets $$out')
		 location='index.php?page=CashOutStatement.php&id=$stand&amt=$out'
		 	</SCRIPT>"); exit;


   

 

}
 ?> <!-- Include Core Datepicker Stylesheet -->
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
<form action="" method="post" name="qualification_form" onSubmit="MM_validateForm('amount','','RinRange0:999999999');return document.MM_returnValue" >
<center>

<table width="37%" border="0" align="center" >
 
 
      <tr>
        <td><div align="center"><strong>Cashout</strong></div></td>
       
      </tr>
      
  </table> 
    <div class="errstyle" id="errr"></div>
    <div class="errstyle" id="err"></div>
 <table width="100%">
</table>

  
  <table width="31%" align="center" bgcolor="#FFFFFF">
<tr>
  <td width="100">6% Interest</td>
  <td width="144">
    <input type="text" name="amount" id="amount"/></td>
</tr>
          
</td></tr>

<tr><td colspan="2"  align="center"><div align="center">
  <input type="submit" name="Submit" size="30" class='btn btn-info'  value="Save"  onclick="return confirm('Are you sure?')" />
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