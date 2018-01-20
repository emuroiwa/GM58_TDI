

<?php
error_reporting(0);
	 include 'opendb.php';
		if(isset($_POST['Search'])){
		$item=$_POST['user'];
		$result = mysql_query("select * from users where username = '$item' and suspend = '0'");
		$rw = mysql_num_rows($result);
		if($rw == 0){
		 echo("<SCRIPT LANGUAGE='JavaScript'> window.alert('Either User is already unsuspended or Doesnt Exit')
		location = 'index.php?page=unsuspend_users.php'
		 	</SCRIPT>"); 
			exit();
		}
		
		else
		{
		while($row = mysql_fetch_array($result)){
		$name = $row['name'];
		$surname = $row['surname'];
		$id = $row['id'];
		$idnumber = $row['idnumber'];
		}
		
		}
}
?>
<?php  
if(isset($_POST['Submit'])){
 include 'opendb.php';
      	  $result ="";
		 $userid= $_POST['userid'];
	   
		    $rez = mysql_query("Update users set suspend = '1' where id = '$userid'") or die(mysql_error());
		
							 

			 if ($rez )
			    {
				  echo("<SCRIPT LANGUAGE='JavaScript'> window.alert('User $_POST[name] $_POST[surname] unsuspended')
		location = 'index.php?page=unsuspend_users.php'
		 	</SCRIPT>"); 
			exit; 
				 }
			 else
			  {
			      $msg= "Error occured";
		}
			   
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<title></title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="description" content="Place your description here" />
<meta name="keywords" content="put, your, keyword, here" />
<meta name="author" content="Templates.com - website templates provider" />
<link href="style.css" rel="stylesheet" type="text/css" />
<link href="layout.css" rel="stylesheet" type="text/css" />
<script src="../../SpryAssets/SpryValidationTextField.js" type="text/javascript"></script>
<script src="../../SpryAssets/SpryValidationTextarea.js" type="text/javascript"></script>
<link href="../../SpryAssets/SpryValidationTextField.css" rel="stylesheet" type="text/css" />
<link href="../../SpryAssets/SpryValidationTextarea.css" rel="stylesheet" type="text/css" />
<style type="text/css">
<!--
.style1 {color: #FF0000}
.style2 {	font-size: 24px;
	font-weight: bold;
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
</head>
<body id="page1">
<div id="main">
<!-- header -->
<!-- content -->
  <div id="content">
	  <div class="wrapper">
<div class="col-2">
				<div class="indent">
					<h1 align="center">Unsuspend Users Module</h1>
					<p align="center"><em>Enter username of the employee you want to unsuspend and then confirm with the details below.</em></p>
					
				  <div id="text"><form action="" method="POST" enctype="multipart/form-data" name="form1" id="form1" onsubmit="MM_validateForm('value','','R');return document.MM_returnValue">
				    <center>
				    <table width="400" border="0"  align="center">
    <tr><div align="left"> <font face="Verdana, Arial, Helvetica, sans-serif" ><font color="#0000FF"><b><?php if ($result ) { echo $msg;}?></b></font></font></div></tr>
      <tr>
        <td width="277"><table width="399" border="0" bgcolor="#FFFFFF" style="border-top:3px solid #000000;">
		  <tr>
            <td width="165">Username</td>
            <td width="224">
              <input name="user" type="text" id="value"    />
              <input type="submit" name="Search" value="Find" /></td>
          </tr>
        </table></td>
      </tr>
    </table>
  </form>
                    <div align="center"><span class="style2">Unsuspension Confirmation Details </span><br />
                    </div>
                    <form action="" method="POST" enctype="multipart/form-data" name="form1" id="form1" onsubmit="MM_validateForm('userid','','R','name','','R','surname','','R','idnumber','','R');return document.MM_returnValue">
    <table width="400" border="0"  align="center">
    <tr><div align="left"> <font face="Verdana, Arial, Helvetica, sans-serif" ><font color="#0000FF"><b><?php if ($result ) { echo $msg;}?></b></font></font></div></tr>
      <tr>
        <td width="277"><table width="399" border="0" bgcolor="#FFFFFF" style="border-bottom:3px solid #000000;">
		<tr>
            <td width="165">User ID</td>
            <td width="224">
            <input name="userid" type="text" id="userid" readonly="readonly" value="<?php echo $id ?>"  />            </td>
          </tr>
          <tr>
            <td width="165">Name</td>
            <td width="224">
              <input name="name" type="text" id="name" readonly="readonly" value="<?php echo $name ?>"  />             </td>
          </tr>
		  <tr>
            <td width="165"> Surname </td>
            <td width="224">
            <input name="surname" type="text" id="surname" readonly="readonly" value="<?php echo $surname ?>"  />            </td>
          </tr>
		  <tr>
            <td width="165">ID Number</td>
            <td width="224">
            <input name="idnumber" type="text" id="idnumber" readonly="readonly" value="<?php echo $idnumber ?>"  />            </td>
          </tr>
          <tr>
            <td>&nbsp;</td>
            <td><input type="submit" name="Submit" value="UnSuspend" /></td>
          </tr>
        </table></td>
      </tr>
    </table>
  </form>
</div></p>
				</div>
		</div>
		</div>
	</div>
<div id="footer"><div class="indent"></div>
	</div>
</div>

</body>
</html>

