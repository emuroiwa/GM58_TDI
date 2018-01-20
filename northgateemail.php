<?php $subject = 'True Destination Investments';
$message = '<strong>Good Day</strong>,<br><br>
<br>
<br>

This email serves to inform you that : <br>
'.$_GET['msg'].'
<br>
<br>
For more info please call us on<br>
+263 776285114 , +263 54225738<br><br>
<br>
<br>

';
$headers  = 'MIME-Version: 1.0' . "\r\n";
$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
$headers .= 'From: True Destination Investments<northgate@tdi.co.zw>' . "\r\n";
mail($_GET['email'], $subject, $message, $headers);
?> <script language="javascript">
		alert("Email and Sms sent");
  javascript:history.go(-2)
  </script>