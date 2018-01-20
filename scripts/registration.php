
<?php
  if(isset($_POST['Submit'])){
   include "opendb.php";
   
   $date = date('m/d/Y');
   $name = $_POST['name'];
   $surname = $_POST['surname'];
   $username = $_SESSION['username'];
   $id_no = $_POST['ecnumber'];
   $pwd=($_POST['password']);
   $rs1 = mysql_query("select * from users where username = '$_POST[username]'");
   $rw = mysql_num_rows($rs1);
   if($rw == 1){
   ?>
  <script language="javascript">
 alert("Username already in use");
 location = 'index.php?page=registration.php'
  </script>
  <?php
  exit;
   }
  
   if($_POST['password']!=$_POST['cpass']){
   ?>
  <script language="javascript">
 alert("Password did not match with confrim password");
 location = 'index.php?page=registration.php'
  </script>
  <?php
  exit;
   }
   if(strlen($_POST['password']) < 8 ){
   ?>
  <script language="javascript">
 alert("Password should be above 8 charactors");
 location = 'index.php?page=registration.php'
  </script>
  <?php
  exit;
   }
   else{
 $rs = mysql_query("insert into users(name,surname,sex,email,address,username,password,idnumber,status,date,access,department,suspend) values ('$_POST[name]','$_POST[surname]','$_POST[sex]','$_POST[email]','$_POST[address]','$_POST[username]','$pwd','$id_no','1','$date','$_POST[access]','$_POST[branch]','1')") ;
  ?>
  <script language="javascript">
 alert("User successfully created");
 
  </script>
  <?php
  }}
?>
<script language=javascript type='text/javascript' src="jquery.js"> </script>

<style type="text/css">
<!--
.style3 {color: #0066FF}
.style4 {
	color: #000000;
	font-weight: bold;
	font-size: 24px;
}
.errstyle {
	color: #FF0000;
	font-size: 12px;
	}
-->
</style>
<style type="text/css">
<!--
.style5 {
	color: #000000;
	font-size: 14px;
}
-->
</style>
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
<style type="text/css">
<!--
.style7 {
	font-size: 18px;
	color: #000000;
}
.style8 {
	color: #000000;
	font-style: italic;
	font-weight: bold;
	font-size: 18px;
}
-->
</style>
<script language="javascript">
function lettersOnly(evt) {
evt = (evt) ? evt : event;
var charCode = (evt.charCode) ? evt.charCode : ((evt.keyCode) ? evt.keyCode :
((evt.which) ? evt.which : 0));
if ( (charCode < 65 || charCode > 90 ) &&
(charCode < 97 || charCode > 122)) {
if(charCode != 8){
alert("Enter letters only.");
return false;
}

}
return true;
}
</script><script>
function validateEmail(emailField){
        var reg = /^([A-Za-z0-9_\-\.])+\@([A-Za-z0-9_\-\.])+\.([A-Za-z]{2,4})$/;

        if (reg.test(emailField.value) == false) 
        {
            alert('Invalid Email Address');
            return false;
        }

        return true;

}
</script>
<div class="style4">
  <div align="center">User Registration</div>
</div>
<form action="" method="post" name="" onsubmit="MM_validateForm('name','','R','surname','','R','email','','RisEmail','phone','','RisNum','username','','R','password','','R','cpass','','R');return document.MM_returnValue" >

   <center>
  <table width="49%" border="0" bgcolor="#FFFFFF" align="center">
  
    <tr><td colspan="2"><div align="center">
      
      <p class="style3 style7"><em><strong><u>General  Details</u></strong></em></p>
    </div></td>
    </tr>
    <tr>
      <td>Name</td>
      <td>
        <input name="name" type="text" id="name" onkeypress="return lettersOnly(event)" />       </td>
        </tr>
          <tr>
            <td width="100"> Surname</td>
            <td width="161">
            <input name="surname" type="text" id="surname" onkeypress="return lettersOnly(event)"  />              </td>
    </tr>
          <tr><td>Branch</td><td>
     <?php
 
$sql="select * from branch";
$rez=mysql_query($sql);
echo "<select name='branch' id='deptid'>";
?>

<?php
while($row=mysql_fetch_array($rez,MYSQL_ASSOC)){

 echo "<option value='$row[branchname]'>{$row['branchname']}</option>"; 
}

?>
          </td></tr>
          <tr>
            <td>E mail</td>
            <td>
              <input name="email" type="text" id="email" onblur="validateEmail(this);" />            </td>
          </tr>
            <td>Sex</td>
            <td>
              <select name="sex"><option>male</option><option>female</option></select>            </td>
          </tr>
		  <tr>
            <td>Contact Phone Number</td>
            <td>
              <input name="phone" type="text" id="phone" maxlength="12"  />            </td>
          </tr>
          <tr>
            <td>Contact Address</td>
            <td>
              <textarea name="address" id="address"></textarea>            </td>
          </tr>
   
    <tr><td colspan="2"><div align="center"><span class="style8"><u>Login Details</u></span></div></td>
    </tr>
    <tr>
      <td>Username</td>
      <td>
        <input name="username" type="text" id="username"   />             </td>
      </tr>
          <tr>
            <td width="100">Password</td>
            <td width="161"><span id="sprytextfield1">
            <input name="password" type="password" id="password"   />
            <br />
           Password should be  atleast 8 characters long</td>
    </tr>
          <tr>
            <td>Confirm </td>
            <td>
              <input name="cpass" type="password" id="cpass"  />            </td></tr> <tr>
            <td>Access level</td>
            <td>
              <select name="access"><option value="1">GM58 Admin</option><option value="3">Senior Management</option><option value="2">Staff</option></select> 
                     </td></tr>
         		  <tr>
            <td></td>
            <td>
              <input name="Submit" type="Submit" id="Submit" class="btn btn-info" value="Save"  />            </td>
          </tr>
    </table>
  
</form>