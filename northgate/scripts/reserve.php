
<?php
  
   
   if(isset($_POST['Submit'])){


  
   $rs1 = mysql_query("select * from stand where location = '$_POST[location]' and number = '$_POST[number]' ");
   $rw = mysql_num_rows($rs1);
   if($rw == 1){
   ?>
  <script language="javascript">
 alert("Stand Number <?php echo $_POST['number'] ?> already in use");
 location = 'index.php?page=stand.php'
  </script>
  <?php
  exit;
   }
   
   else{
   
 $rs = mysql_query("insert into stand(location,number,area,date,price,status,instalments,datestatus,months_paid) values ('$_POST[location]','$_POST[number]','$_POST[area]','$date','$_POST[price]','RESERVED','$_POST[instalments]','$date','$_POST[months]')") or die(mysql_error());
 
  if($rs){
  ?>
  <script language="javascript">
 alert("Stand successfully added ");
 location = 'index.php?page=reserve.php'
  </script>
  <?php
  
  }
  else
  {
  echo "problem occured";
  }
  }
  }
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
</script>
<div class="style4">
  <div align="center"><em>New Stands</em></div>
</div>
<form action="" method="post" name="" onsubmit="MM_validateForm('number','','R','area','','RinRange0:9999999999','price','','RinRange0:99999999');return document.MM_returnValue" >

   <center>
  <table width="63%" border="0" bgcolor="#FFFFFF" align="center" style="border-top:3px solid #000000;">
  
    <tr><td colspan="2"><div align="center">
      <p class="style3 style5">Please Enter the stand details below </p>
     
      </div></td>
    </tr>
          <tr>
            <td width="191">Location</td>
            <td width="144"><?php 

 
$sql="select * from location group by dept";
$rez=mysql_query($sql);
?>
<select name='location' id ='location'>

<?php
while($row=mysql_fetch_array($rez,MYSQL_ASSOC))
{
 echo "<option value='$row[dept]'>{$row['dept']}</option>"; 
}

?>
</select></td>
          </tr>
           
		  
          
   
    
    <tr>
      <td>Stand Number</td>
      <td>
        <input name="number" type="text" id="number"   />             </td>
      </tr>
          <tr>
            <td>Stand Area</td>
            <td>
              <input name="area" type="text" id="area"  />
              sqm            </td></tr>
          <tr>
            <td>Total Cost $</td>
            <td><input name="price" type="text" id="price"   /></td>
          </tr>
         <!-- <tr>
            <td>Deposit $</td>
            <td><input name="deposit" type="text" id="deposit"   /></td>
          </tr> 
    <tr>
            <td>Monthly Instalments $</td>
            <td><input name="instalments" type="text" id="instalments"   /></td>
    </tr><tr>
            <td>Months To Paid </td>
            <td><input name="months" type="text" id="months"   /></td>
    </tr>-->
         		  <tr>
            <td></td>
            <td>
              <input name="Submit" type="Submit" id="Submit" class="btn btn-info"  value="Save"  />            </td>
          </tr>
    </table>
  </div>
  </div>
  
</div></form>