<?php include ('aut1.php');?><?php
include ('opendb.php');
if(isset($_POST['Submit'])){
		
		$date = date('m/d/Y');
		
		
$r1 = mysql_query("SELECT * FROM expire where id='1'")or die(mysql_query());
   $rows = mysql_num_rows($r1);
 while($rw1 = mysql_fetch_array($r1, MYSQL_ASSOC)){
  $level=$rw1['expiredate'];
  }
  if($level<$date){
	  $days_between=0; 
	  }
  else{
  $start = strtotime($level);
$end = strtotime($date);

$days_between = ceil(abs($start - $end) / 86400);}
 
  $length=$_POST['days'];
  $totalength=$length+$days_between;
  $period=$totalength."days";
  $a=$length."days";
  $dt = strtotime("$level + $a");
		$dateexp = date('m/d/Y',$dt);
  mysql_query("update expire set expiredate='$dateexp',setdate='$date',length='$length' where
 id='1'") or die (mysql_error());
  $res = mysql_query("insert into expire_tray(username,expiredate,setdate,length) values('$_SESSION[username]','$dateexp','$date','$length')") or die(mysql_error());
}
 ?>
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
<form action="" method="post" name="qualification_form" onSubmit="MM_validateForm('days','','RinRange0:10000');return document.MM_returnValue">
<center>
<?php
$r = mysql_query("SELECT * FROM expire where id='1'")or die(mysql_query());
  
 while($rw = mysql_fetch_array($r, MYSQL_ASSOC)){
echo " current expiry date is $rw[expiredate]";
  }?>
<table width="50%" border="0" align="center" style="border-bottom:3px solid #000000;">
 
 
      <tr>
        <td><div align="center"><span class="style7">LICENSE PERIOD</span></div></td>
       
      </tr>
      
  </table> 
    <div class="errstyle" id="errr"></div>
    <div class="errstyle" id="err"></div>
 <table width="100%">
</table>

  
  <table width="50%" align="center">
<tr>
  <td width="140"> <span class="style1 style9">DAYS</span></td>
  <td width="412">
    <input type="text" name="days" id="days" size="30"  /></td>
</tr>
          
</td></tr>

<tr><td colspan="2"  align="center"><input type="submit" name="Submit" size="30"  value="Save"/></td>
</tr>
</table>
</form><a href="logout.php">Logout</a><br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<a href="maneta.php">Logout</a>