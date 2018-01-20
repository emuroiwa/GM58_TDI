<?php
if(isset($_POST['submit'])){
	if($_POST['message']=='')
	{?>
<script language="javascript">
alert("Please Enter Message")
location='index.php?page=bulk.php'
</script>
<?php
 exit;}
	$no=0;
  $r = mysql_query("SELECT
*
FROM clients
")or die(mysql_query()); 
while($rw1 = mysql_fetch_array($r, MYSQL_ASSOC)){
	//getting mobile number
	$no++;
	$number="00263".$rw1['contact'];
	//echo "$number -$rw1[name] -$rw1[surname]<br>
//";}exit;
	// XML-formatted data
	 $pm="<gsm>".$number."</gsm>"; 
	 //echo $number; exit;
  $p=clean($_POST['message']);
  $msg="Dear"." ".$rw1['ecnum']." ".$rw1['surname']." ".$p;
$xmlString =
"<SMS>
<authentification>
<username>TinshelP</username>
<password>90J4fFdO</password>
</authentification>

<message>
<sender>Tinshel</sender>

<text>".$msg."</text>
</message>
<recipients>
$pm

</recipients>
</SMS>"; //echo $xmlString; }exit;  
$postUrl = "http://193.105.74.59/api/sendsms/xml";
// previously formatted XML data becomes value of "XML" POST variable
$fields = "XML=" . urlencode($xmlString);
// in this example, POST request was made using PHP's CURL
echo "$no)<br>
";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $postUrl);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
// response of the POST request
$response = curl_exec($ch);
curl_close($ch);
$a=mysql_num_rows($r);
		mysql_query("insert into sms_response (`mobile`, `message`, `date`) VALUES ('$rw1[contact]', '$msg', '$time')") or die(mysql_error());}

?>
<script language="javascript">
alert("<?php echo "$a SMSes Have Been sent out to Clients";?>")
location='index.php'
</script>
<?php
}
?><script type="text/javascript">
function LimtCharacters(txtMsg, CharLength, indicator) {
chars = txtMsg.value.length;
document.getElementById(indicator).innerHTML = CharLength - chars;
if (chars > 3) {
document.getElementById(indicator).innerHTML = CharLength - chars .concat(1);
}
}
</script>

<?php
 $r1 = mysql_query("SELECT
*
FROM clients
")or die(mysql_query());
$a1=mysql_num_rows($r1);
echo "Number Of Clients <strong>$a1</strong>";

?>
<form action="" method="post" onsubmit="return confirm('Are you sure you want to SEND BULK SMS');">
  <div align="center" >
  <strong>Enter Bulk Message.</strong>
    <table width="" border="0" align="center">
      <tr>
        <td>Number of characters required 
        <label id="lblcount" style="background-color:#E2EEF1;color:Red;font-weight:bold;">140</label>
        <textarea name="message" id="message" cols="105" rows="8" onkeyup="LimtCharacters(this,140,'lblcount');"></textarea></td>
      </tr>
      <tr>
        <td><input type="submit" name="submit" id="submit" value="SEND..">
        <input type="reset" name="reset" id="reset" value="Reset"></td>
      </tr>
  </table>
  </div>
</form>
