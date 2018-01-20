<?php
 $pmsms="<gsm>263774002797</gsm>"; 
$xmlString =
"<SMS>
<authentification>
<username>TDInvestment</username>
<password>tS8ff1Cg1</password>
</authentification>

<message>
<sender>Ernest</sender>

<text>TDInvestment is the username credit it with 41.50</text>
</message>
<recipients>
$pmsms

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
		?>
		
