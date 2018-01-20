<?php
$stand=$_GET['id'];


$r1 = mysql_query("SELECT * FROM stand where id_stand='$stand'")or die(mysql_query());
   $rows = mysql_num_rows($r1);
 while($rw1 = mysql_fetch_array($r1, MYSQL_ASSOC)){
  $price=$rw1['price']; 
  $purchasedate=$rw1['datestatus']; 
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

  
  //debit
  $debit=getdebit($stand);
	

$lastmonth=lastmonth($stand);
$lastcash=lastcash($stand,$lastmonth);
 
  echo "Stand # <strong> $standno</strong>
<br>Instalments is <strong>$ $instalments</strong> Monthly<br>
<a href='#myModalStatement' class='btn btn-success' data-toggle='modal'>&nbsp;<i class='icon-file-alt icon-large'></i>&nbsp; Payment History</a>";


//processing forme
if(isset($_POST['Submit'])){
		$stand=$_GET['id'];
	
$balance=clean($_POST['amount']);
$instalment=clean($_POST['instalment']);
$purchasedate=$_POST['date1'];
if(ValidateDate($purchasedate)==false){
		   	msg('Enter Correct Date Format');
	exit;
	}
		 $monthspaid=GetMonthsPaid($balance,$instalment);
		 $monthname=GetInstalmentMonth($purchasedate,$monthspaid);
		 		 $monthdate=GetInstalmentMonthDate($purchasedate,$monthspaid);

		  $r = mysql_query("SELECT * FROM payment where stand='$stand' and d<>'Stand Deposit' ")or die(mysql_query());
if(mysql_num_rows($r)>=1){
			   mysql_query("update payment set cash='$balance',month='$monthname',year='$monthspaid',value_date='$monthdate' where stand='$stand' and payment_type<>'Deposit'") or die (mysql_error());
			 //Write to log file
			 WriteToLog("Balance Take Up AMENDMENT Amount=$balance |Stand = $standno",$_SESSION['username']);
			  
	}
		 if(mysql_num_rows($r)==0){
    mysql_query("INSERT INTO `payment` (`cash`, `stand`, `date`, `capturer`, `month`, `year`, `payment_type`, `d`, `payment_date`, `value_date`) VALUES ('$balance', '$stand', NOW(), '$_SESSION[name]', '$monthname', '$monthspaid', 'Credit','Balance Take Up', now(), '$monthdate')") or die (mysql_error()."INSERT INTO `payment` (`cash`, `stand`, `date`, `capturer`, `month`, `year`, `payment_type`, `d`, `payment_date`, `value_date`) VALUES ('$balance', '$stand', NOW(), '$_SESSION[name]', '$monthname', '$monthspaid', 'Credit','Balance Take Up', now(), '$monthdate')");
	//Write to log file
			 WriteToLog("Balance Take Up Amount=$balance |Stand = $standno",$_SESSION['username']);
			
		 }
		 	 
		   mysql_query("update stand set instalments='$instalment',datestatus='$purchasedate' where id_stand='$stand'") or die (mysql_error());
		   	msg('Balance Take Up Successfull');
       link1("index.php?page=statement.php&id=$_GET[id]"); 
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

<form action="" method="post" name="qualification_form"  >
<center>

<table width="50%" border="0" align="center" style="border-bottom:3px solid #000000;" bgcolor="#FFFFFF">
 
 
      <tr>
        <td><div align="center"><span class="style7"><strong>UPDATE BALANCES</strong></span></div></td>
       
      </tr>
      
  </table> 
    <div class="errstyle" id="errr"></div>
    <div class="errstyle" id="err"></div>
 <table width="100%">
</table>

  
  <table width="50%" align="center" bgcolor="#FFFFFF">
<tr>
  <td width="109"> <span class="style1 style9">Balance :</span></td>
  <td width="150">
   $ <input type="text" name="amount" id="amount" value="<?php echo $credit;?>" required /></td>
</tr><!--<tr>
  <td width="109"> <span class="style1 style9">Months Being Paid For:</span></td>
  <td width="150">
    <input type="text" name="month" id="month"/></td>
</tr>--><tr>
  <td width="109"> <span class="style1 style9">Instalment:</span></td>
  <td width="150">
   $ <input type="text" name="instalment" id="instalment" value="<?php echo $instalments ;?>" required /></td>
</tr><tr>
  <td width="109"> <span class="style1 style9">Purchase Date :</span></td>
  <td width="150">
    <input type="text" name="date1" id="date1" value="<?php echo $purchasedate ;?>" required/></td>
</tr>
          
</td></tr>

<tr><td colspan="2"  align="center"><div align="center">
  <input type="submit" name="Submit" size="30" class="btn btn-info" onclick="return confirm('Are you sure you want to UPDATE  Informantion ?')" value="Save"/>
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
?><!-- MODAL STATEMENT------------------------------------------------------------------------------------------------------------------------------------------------- -->
    <div id="myModalStatement" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                <div class="modal-header">
                </div>
                <div class="modal-body">
<?php $base_url="http://".$_SERVER['SERVER_NAME'].":".$_SERVER['SERVER_PORT'].dirname($_SERVER["REQUEST_URI"].'?').'/';?>
           <iframe src="<?php echo $base_url; ?>statement_print.php?id=<?php echo $_GET['id'];?>" width="100%" height="1000"></iframe>
                </div>
                <div class="modal-footer">
                 <a href="<?php echo $base_url; ?>statement_print.php?id=<?php echo $_GET['id'];?>" target="_blank" class="btn btn-info">&nbsp;Print</a>
                    <button class="btn" data-dismiss="modal" aria-hidden="true"><i class="icon-remove icon-large"></i>&nbsp;Close</button>
           
                </div>
            </div>