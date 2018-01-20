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
<form action="" method="post" onSubmit="MM_validateForm('message','','R');return document.MM_returnValue">
  <div align="center">
  <strong>Enter Bulk Message.</strong>
    <table width="200" border="1">
      <tr>
        <td><label for="textarea">Message:</label>
        <textarea name="message" id="message" cols="45" rows="5"></textarea></td>
      </tr>
      <tr>
        <td><input type="submit" name="submit" id="submit" value="Submit">
        <input type="reset" name="reset" id="reset" value="Reset"></td>
      </tr>
  </table>
  </div>
</form>
<?php
if(isset($_POST['submit'])){
  $r = mysql_query("SELECT
*
FROM owners,clients,stand
where status='Payment_In_Progress' and owners.client_id = clients.id AND
owners.stand_id = stand.id_stand")or die(mysql_query()); 
	// Bulk SMS's POST URL
		$postUrl = "http://193.105.74.59/api/sendsms/xml";
	// XML-formatted data
		$xmlString =
			"<SMS>
				<authentification>
<username>TDInvestment</username>
<password>tS8ff1Cg1</password>
				</authentification>
				";
while($rowsms = mysql_fetch_array($r))
  { 
  $msgsms="Merry Christmas $rowsms[name] $rowsms[surname] ".$_POST["message"];
 //echo $_POST["message"];
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
	
	
		

?>
<script language="javascript">
alert("SMS message sent to all clients")
location='index.php'
</script>
<?php
}
?>