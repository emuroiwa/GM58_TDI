

<?php
 include ('../opendb.php');
include ('../functions.php');

$month = date('F-Y');
$smsdate = "29 ".date('F-Y');
$paid=mysql_query("SELECT * FROM payment where payment_type='credit' and `month`='$month' group by stand")or die(mysql_query());$paid1=mysql_query("SELECT * FROM payment where payment_type='deposit' and `month`='$month' group by stand")or die(mysql_query());
$number_of_paid=mysql_num_rows($paid)+mysql_num_rows($paid1);
$all = mysql_query("SELECT * FROM stand where status='Payment_In_Progress'")or die(mysql_query());
$number_none=mysql_num_rows($all)-$number_of_paid;
mysql_query("INSERT INTO `chikwereti` (`date`, `month`, `paid`, `non_paid`) VALUES ('$date', '$month', '$number_of_paid', '$number_none')") or die(mysql_error());



$sms3 = mysql_query("SELECT month,name,surname,contact FROM `clients`,payment WHERE stand_id=stand and `month` <>'$month' and payment_type='credit' GROUP BY stand_id  ")or die(mysql_query());
/*while($rowsms = mysql_fetch_array($sms3))
{ echo"$rowsms[surname]<br>
".mysql_num_rows($sms3);
}exit;*/
// Bulk SMS's POST URL
$postUrl = "http://193.105.74.59/api/sendsms/xml";
// XML-formatted data
$xmlString =
"<SMS>
<authentification>
<username>TDInvestment</username>
<password>tS8ff1CgKKK</password>
</authentification>
";
while($rowsms = mysql_fetch_array($sms3))
{
$msgsms="Seasons Greeting.$rowsms[name] $rowsms[surname],Please be advised that your Installment is Due on $smsdate";

$xmlString.="
<message>
<sender>Northgate</sender>
<text>".$msgsms."</text>
<recipients>
<gsm>".$rowsms['contact']."</gsm>
</recipients>
</message>
";
}
$xmlString.="

</SMS>";
//echo $xmlString; exit;

// previously formatted XML data becomes value of "XML" POST variable
$fields = "XML=" . urlencode($xmlString);

// in this example, POST request was made using PHP's CURL
$ch = curl_init($postUrl);
curl_setopt($ch, CURLOPT_URL, $postUrl);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
// response of the POST request
curl_exec($ch);
curl_close($ch);
// write out the response
//echo $response;

echo ("<SCRIPT LANGUAGE='JavaScript'> window.alert('All Accounts Closed')
location='index.php'
</SCRIPT>");


?>