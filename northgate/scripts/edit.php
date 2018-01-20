
<?php
include 'opendb.php';	

		$rs=mysql_query("select * from clients where id = '$_GET[id]'") or die(mysql_error());
while($row = mysql_fetch_array($rs)){
		$id = $row['id'];
		$name = $row['name'];
		$surname = $row['surname'];
		$phone= $row['contact'];
		$address = $row['address'];
		$idnum = $row['idnum'];
				$email = $row['email'];
				}
				
	?>
<?php
  if(isset($_POST['Submit'])){
   include "opendb.php";
  $rs = mysql_query("UPDATE clients set name = '$_POST[name]',surname='$_POST[surname]',email='$_POST[email]',idnum='$_POST[idnum]',contact='$_POST[phone]' where id = '$_GET[id]'")or die(mysql_error());
 	//Write to log file
			 WriteToLog("Edited Client Details $_POST[name] $_POST[surname]  CLIENT TO STAND ID $_REQUEST[id]",$_SESSION['username']);
  if($rs){
  ?>
  <script language="javascript">
 alert("successfully updated")
 location = 'index.php'
  </script>
  <?php
  
  }
  else
  {
  echo "problem occured";
  }
  }

?>
<style type="text/css">
<!--
.style3 {color: #0066FF}
.style4 {
	color: #000000;
	font-weight: bold;
	font-size: 18px;
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
.style6 {font-size: 12px}
-->
</style>
<div class="style4">
  <div align="center">Edit Client Details</div>
</div>
<form action="" method="post"  onsubmit="MM_validateForm('name','','R','surname','','R');return document.MM_returnValue">

   
<center><table width="46%" border="0" bgcolor="#FFFFFF" align="center" style="border-bottom:3px solid #000000;">
  
        <tr>
      <td>Name</td>
      <td>
        <input name="name" type="text" id="name" value="<?php echo $name; ?>"  />       </td>
        </tr>
          <tr>
            <td width="77"> Surname</td>
            <td width="161">
              <input name="surname" type="text" id="surname" value="<?php echo $surname; ?>"   />              </td>
          </tr> <tr>
            <td>Email</td>
            <td>
              <input name="email" type="text" id="email"  value="<?php echo $email; ?>" />            </td>
          </tr>  <tr>
            <td>Cellnumber</td>
            <td>
              <input name="phone" type="text" id="phone"  value="<?php echo $phone; ?>" />            </td>
          </tr>  <tr>
            <td>Id Number</td>
            <td>
              <input name="idnum" type="text" id="idnum" value="<?php echo $idnum; ?>" />            </td>
          </tr>  <tr>
            <td></td>
            <td>
              <input name="Submit" type="Submit" id="Submit" value="Update" class='btn btn-info' onclick="return confirm('Are you sure you want to UPDATE  Informantion ?')"  />            </td>
          </tr>
    </table>
   </form>

