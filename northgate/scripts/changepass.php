
<?php 
if(isset($_POST['Submit'])) ///forsubmit data
           {
		   include 'opendb.php';
		   
		 $sender = $_SESSION['username'];
		   $password = ($_POST['password']); 
		   $cpass=($_POST['cpass']);
		   	  $oldpass=$_POST['oldpass'];
		   $query = mysql_query("select * from users where username = '$sender'");
		   while($row = mysql_fetch_array($query)){
		   $passwords = $row['password'];
		   }
		  if (strcmp($password,$cpass)!= 0 )
	       {
	             echo ("<SCRIPT LANGUAGE='JavaScript'> window.alert('Password did not match')
				 javascript:history.go(-1)
				
				</SCRIPT>");
				 exit;
	       } 
			
if (strcmp($passwords,$oldpass)!= 0 )
	       {
	             echo ("<SCRIPT LANGUAGE='JavaScript'> window.alert('Old password is incorrect')
				 javascript:history.go(-1)
				
				</SCRIPT>");
				 exit;
	       } 
 
			
$result = mysql_query("Update users set password = '$password' where username = '$sender'") or die (mysql_error());
 if ($result )
			    {
				  $msg="New password created";
				   //echo "Added To The Database";
				 }
			 else
			  {
			      $msg= "Error occured";
		}
	}		   
//}

?>
<script type="text/javascript">
<!--
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
//-->
</script>

					  <form action="" method="POST" enctype="multipart/form-data" name="form1" id="form1" onSubmit="MM_validateForm('oldpass','','R','password','','R','cpass','','R');return document.MM_returnValue">
					 <center> <table width="50%" border="0"  align="center">
    
    <tr><td colspan="2"><div align="center"><strong><em>Change Password</em></strong></div></td>
    </tr>
      <tr>
        <td width="277"><table width="100%" align="center" border="0" bgcolor="#FFFFFF" style="border-top:3px solid #000000;">
		<tr>
            <td width="187"> Old Password </td>
            <td width="202">
              <input name="oldpass" type="password" id="oldpass"  />              </td>
          </tr>
          <tr>
            <td width="187"> New Password </td>
            <td width="202">
            <input name="password" type="password" id="password"  />            </td>
          </tr>
          <tr>
            <td>Confirm</td>
            <td>
              <input name="cpass" type="password" id="cpass"  />              </td>
          </tr>
          <tr>
            <td>&nbsp;</td>
            <td><input type="submit" name="Submit" value="Submit" /></td>
          </tr>
        </table></td>
      </tr>
    </table>
  </form>
