  <form action="" method="post">
  <br>
<center>  Please Click the button below to close off all accounts for month <strong><?php echo $month;?></strong><br><br>


  <table width="200" border="0"  align="center">
  <tr>
    <td><label><input type='submit' name='process', value='                         Close off  <?php echo $month;?> Accounts                     ' />
      
    </label></td>
    
</table>
</form><?php
//get all stands in progress
 ///echo days("16/06/2014","16/06/2013");
 if(isset($_POST['process'])){
 $month = date('F-Y');
$paid=mysql_query("SELECT * FROM payment where  payment_type='credit' and `month`='$month'  group by stand")or die(mysql_query());$paid1=mysql_query("SELECT * FROM payment where  payment_type='deposit' and `month`='$month' group by stand")or die(mysql_query()); 
$number_of_paid=mysql_num_rows($paid)+mysql_num_rows($paid1);
$all = mysql_query("SELECT * FROM stand where status='Payment_In_Progress'")or die(mysql_query()); 
$number_none=mysql_num_rows($all)-$number_of_paid;
mysql_query("INSERT INTO `chikwereti` (`date`, `month`, `paid`, `non_paid`) VALUES ('$date', '$month', '$number_of_paid', '$number_none')") or die(mysql_error());
	  
	  
	  
while($rwall= mysql_fetch_array($all, MYSQL_ASSOC)){
  $stand=$rwall['id_stand'];
    $instalments=$rwall['instalments'];
  //get last payment type of each stand
/*$dep = mysql_query("SELECT * FROM stand,payment where status='Payment_In_Progress' and stand='$stand'  ORDER BY payment.id DESC LIMIT 1")or die(mysql_query());
 while($rwd = mysql_fetch_array($dep, MYSQL_ASSOC)){
  $payment_type=$rwd['payment_type'];}echo $payment_type;*/
  //kana isiri deposit
//echo getmonth('Monthly Instalments',$stand); exit;

 if(getmonth('Monthly Instalments',$stand)!=$month){
	 //echo "$instalments";
	  mysql_query("insert into payment(date,cash,stand,capturer,month,year,payment_type,d) 
	  values('$date','$instalments','$stand','$_SESSION[name]','$month','$year','Debit','Monthly Instalments')") or die(mysql_error());
	  
	 } /*if(getmonth('Stand Deposit',$stand)!=$month){
	 //echo "$instalments";
	  mysql_query("insert into payment(date,cash,stand,capturer,month,year,payment_type,d) 
	  values('$date','$instalments','$stand','$_SESSION[name]','$month','$year','Debit','Monthly Instalments')") or die(mysql_error());
	 }*/
	 
	 //else {echo "kkk";}
  }
  
 // overdue accounts sms
  $r = mysql_query("SELECT * FROM stand,payment,clients,owners where status='Payment_In_Progress' and id_stand=stand and  clients.id=owners.client_id AND stand.id_stand=owners.stand_id GROUP BY
stand.id_stand")or die(mysql_query());
   //$rows = mysql_num_rows($
 while($rw1 = mysql_fetch_array($r, MYSQL_ASSOC)){
   $credit=getcredit($rw1['stand']);
 $debit=getdebit($rw1['stand']);
 $deposit=getdeposit($rw1['stand']);
	$balance=$debit-$credit; 
	//echo $credit;
	$block=round($balance/$rw1['instalments'],2);
	//check
	  $check = mysql_query("SELECT * FROM clients,sms_response where mobile=contact and month = '$month' ")or die(mysql_query());
	if($block>=2 && $block<=2.9 && $balance>$rw1['instalments'] && mysql_num_rows($check)==0)
	{$msg="Good Day. Please be advised that your Northgate Heights Accout has a two month unpaid balance. Please pay as soon as possible as we will proceed to block all payment if your account hasnt been paid in three months";
	 $pm="<gsm>".$rw1['contact']."</gsm>"; 
  $p=$msg;
$xmlString =
"<SMS>
<authentification>
<username>TDInvestment</username>
<password>tS8ff1Cg1</password>
</authentification>

<message>
<sender>Northgate</sender>

<text>".$p."</text>
</message>
<recipients>
$pm

</recipients>
</SMS>"; //echo $xmlString; }exit;  
$postUrl = "http://193.105.74.59/api/sendsms/xml";
// previously formatted XML data becomes value of "XML" POST variable
$fields = "XML=" . urlencode($xmlString);
// in this example, POST request was made using PHP's CURL
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $postUrl);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
// response of the POST request
$response = curl_exec($ch);
curl_close($ch);
		mysql_query("insert into sms_response (`mobile`, `message`, `month`) VALUES ('$rw1[contact]', '$msg', '$month')") or die(mysql_error());}
	if($block>=3 && $balance>$rw1['instalments'] && mysql_num_rows($check)==0)
	{ $pm="<gsm>".$rw1['contact']."</gsm>"; 
  $p=$msg;
$xmlString =
"<SMS>
<authentification>
<username>TDInvestment</username>
<password>tS8ff1Cg1</password>
</authentification>

<message>
<sender>Northgate</sender>

<text>".$p."</text>
</message>
<recipients>
$pm

</recipients>
</SMS>"; //echo $xmlString; }exit;  
$postUrl = "http://193.105.74.59/api/sendsms/xml";
// previously formatted XML data becomes value of "XML" POST variable
$fields = "XML=" . urlencode($xmlString);
// in this example, POST request was made using PHP's CURL
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $postUrl);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
// response of the POST request
$response = curl_exec($ch);
curl_close($ch);
		$msg="Good Day. Please be advised that your Northgate Heights Accout has a three month unpaid balance. Please note your account has been blocked . An accrued interest of 10% of Amount Overdue Per annum  will be charged ";	mysql_query("insert into application.sms_response (`mobile`, `message`, `month`) VALUES ('$rw1[contact]', '$msg', '$month')") or die(mysql_error());}
		
	
	
	}
	
	//SMS TO ACCOUNTS OWING 
	 $month = date('F-Y');
	 $smsdate = "29 ".date('F-Y');
		 $sms3 = mysql_query("SELECT * FROM stand,payment,clients,owners where status='Payment_In_Progress' and id_stand=stand and  clients.id=owners.client_id 
AND stand.id_stand=owners.stand_id and `month`!='$month' GROUP BY
stand.id_stand ")or die(mysql_query());	
while($rowsms = mysql_fetch_array($sms3))
  { 
  $msgsms="Good Day. $rowsms[name] $rowsms[surname] Please be advised that your Northgate Heights Instalment of $ $rowsms[instalments]  for Stand# $rowsms[number] is Due on $smsdate.Thank You";
  
  if(mysql_num_rows($check)==0)
	{ $pmsms="<gsm>".$rowsms['contact']."</gsm>"; 
  $psms=$msgsms;
$xmlString =
"<SMS>
<authentification>
<username>TDInvestment</username>
<password>tS8ff1Cg1</password>
</authentification>

<message>
<sender>Northgate</sender>

<text>".$psms."</text>
</message>
<recipients>
$pm

</recipients>
</SMS>"; //echo $xmlString; }exit;  
$postUrl = "http://193.105.74.59/api/sendsms/xml";
// previously formatted XML data becomes value of "XML" POST variable
$fields = "XML=" . urlencode($xmlString);
// in this example, POST request was made using PHP's CURL
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $postUrl);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
// response of the POST request
$response = curl_exec($ch);
curl_close($ch);
		mysql_query("insert into sms_response (`mobile`, `message`, `month`) VALUES ('$rowsms[contact]', '$msgsms', '$month')") or die(mysql_error());}
		
	
echo ("<SCRIPT LANGUAGE='JavaScript'> window.alert('All Accounts Closed')
		 location='index.php'
		 	</SCRIPT>"); 
	
  }}exit;
  ?>
