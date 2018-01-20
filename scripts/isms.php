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
<form action="" method="post" onSubmit="MM_validateForm('message','','R');return document.MM_returnValue" target="_blank">
  <div align="center">
  <strong>Enter Message.</strong>
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
//include 'opendb.php';
//mysql_query("update application.sms_response set message='$_POST[message]', status='0' where mobile='$_REQUEST[contact]'")or die(mysql_error());
 $pm="<gsm>".$_REQUEST['contact']."</gsm>"; 
	 
  $p=$_POST['message'];
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
		mysql_query("insert into sms_response (`mobile`, `message`) VALUES ('$_REQUEST[contact]', '$_POST[message]')") or die(mysql_error());

	echo "<script language='javascript'>location = 'http://www.ernestmuroiwa.net63.net/northgateemail.php?msg=$_POST[message]&email=$_GET[email] </script>";?>
  <script language="javascript">

location = 'http://www.ernestmuroiwa.net63.net/northgateemail.php?msg=<?php echo $_POST['message']; ?>&email=<?php echo $_GET['email']; ?>'
		</script>
  <?php
  
}
?>