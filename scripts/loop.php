<?php
//get all stands in progress
 ///echo days("16/06/2014","16/06/2013");
include ('../opendb.php');
	 include ('../functions.php');
 $month = date('F-Y');
$paid=mysql_query("SELECT * FROM payment where  payment_type='credit' and `month`='$month'  group by stand")or die(mysql_query());$paid1=mysql_query("SELECT * FROM payment where  payment_type='deposit' and `month`='$month' group by stand")or die(mysql_query()); 
$number_of_paid=mysql_num_rows($paid)+mysql_num_rows($paid1);
$all = mysql_query("SELECT * FROM stand where status='Payment_In_Progress'")or die(mysql_query()); 
$number_none=mysql_num_rows($all)-$number_of_paid;
	  
	  

	
	//SMS TO ACCOUNTS OWING 
	 $month = date('F-Y');
	 $smsdate = "29 ".date('F-Y');
		 $sms3 = mysql_query("SELECT * FROM clients , stand , owners WHERE clients.id = owners.client_id AND stand.id_stand = owners.stand_id and contact not in(select mobile from sms_response where month='$month') ")or die(mysql_query());	
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
";
while($rowsms = mysql_fetch_array($sms3))
  { 
  $msgsms="Good Day. $rowsms[name] $rowsms[surname] Please be advised that your Northgate Heights Instalment of $ $rowsms[instalments]  for Stand# $rowsms[number] is Due on $smsdate.Thank You";
 
  $xmlString.="
<text>".$psms."</text>
</message>
<recipients>
<gsm>".$rowsms['contact']."</gsm>
</recipients>
</SMS>";}//echo $xmlString; }exit;  
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
		
		$rr=mysql_query("SELECT * FROM sms_response where  `month`='$month'")or die(mysql_query()); 
if(mysql_num_rows($rr)<700){
		
 echo ("<SCRIPT LANGUAGE='JavaScript'> window.alert('Click OK to continue processing')
		 location='loop.php'
		 	</SCRIPT>");
	  
  
	
  }else{
	  mysql_query("INSERT INTO `chikwereti` (`date`, `month`, `paid`, `non_paid`) VALUES ('$date', '$month', '$number_of_paid', '$number_none')") or die(mysql_error());

	  echo ("<SCRIPT LANGUAGE='JavaScript'> window.alert('All Accounts Closed')
		 location='index.php'
		 	</SCRIPT>");
	  }
  
  
  }
  ?>
