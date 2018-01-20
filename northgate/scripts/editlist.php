
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Untitled Document</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
</head>
<style type="text/css">
.underlinemenu{
font-weight: bold;
width: 100%;
}

.underlinemenu ul{
padding: 6px 0 7px 0; /*6px should equal top padding of "ul li a" below, 7px should equal bottom padding + bottom border of "ul li a" below*/
margin: 0;
text-align: right; //set value to "left", "center", or "right"*/
}

.underlinemenu ul li{
display: inline;
}

.underlinemenu ul li a{
color: #494949;
padding: 6px 3px 4px 3px; /*top padding is 6px, bottom padding is 4px*/
margin-right: 20px; /*spacing between each menu link*/
text-decoration: none;
border-bottom: 3px solid gray; /*bottom border is 3px*/
}

.underlinemenu ul li a:hover, .underlinemenu ul li a.selected{
border-bottom-color: black;
}

</style>

	<link rel="stylesheet" type="text/css" href="file:///C|/wamp/WRL/admin/css/ui.css">
	<link rel="styleSheet" type="text/css" href="file:///C|/wamp/WRL/admin/css/main-print.css" media="print">	
	<link rel="styleSheet" type="text/css" href="file:///C|/wamp/WRL/admin/css/load.css">
</html>

<html xmlns="http://www.w3.org/1999/xhtml">
<body>
<p align="center"><font size="5"><strong>Edit User Department List</strong></font></p>

<form action="" method="post">
  <table width="100%" border="0" align="center" cellpadding="2" cellspacing="1"  bgcolor="#000000" >

  <tr bgcolor="#FFFFFF">
  
    <td width="55"><font size="1"><strong>User ID</strong></font> </td>
    <td width="123"><font size="1" face="Arial, Helvetica, sans-serif"><strong>Name and Surname</strong></font></td>
    <td width="139"><strong><font size="1" face="Arial, Helvetica, sans-serif">Sex</font></strong></td>
    
    <td width="139"><strong><font size="1" face="Arial, Helvetica, sans-serif">ID Number</font></strong></td>

    <td width="118"><font size="1" face="Arial, Helvetica, sans-serif"><strong>E-Mail</strong></font></td>
    <td width="118"><font size="1" face="Arial, Helvetica, sans-serif"><strong></strong></font></td>
    
 
  </tr>
 
  <?php
include 'opendb.php';	

		$rs=mysql_query("select * from users where id = '$_POST[deptid]' ") or die(mysql_error());
while($row = mysql_fetch_array($rs)){
		$id = $row['id'];
		$name = $row['name'];
		$surname = $row['surname'];
		$sex = $row['sex'];
		$idnumber = $row['idnumber'];
		//$department = $row['phone'];
		$email = $row['email'];
				
	?>
<tr bgcolor="#CCCCCC">
    <td colspan="9">&nbsp;</td>
  </tr>

  <tr bgcolor="#FFFFFF">
  
 
    <td><font size="1" face="Arial, Helvetica, sans-serif"><?php echo $id; ?></font></td>
    <td><font size="1" face="Arial, Helvetica, sans-serif"><?php echo $name." ".$surname; ?></font></td>
    <td><font size="1" face="Arial, Helvetica, sans-serif"><?php echo $sex; ?></font></td>
     <td><font size="1" face="Arial, Helvetica, sans-serif"><?php echo $idnumber; ?></font></td>
    
    
    <td><font size="1" face="Arial, Helvetica, sans-serif"><?php echo $email; ?></font></td>
    <td><font size="1" face="Arial, Helvetica, sans-serif"><a href="index.php?page=edit.php&id=<?php echo $id; ?>">[edit details]</a></font></td>
   
    
  </tr>
  
  
<?php
}
?>
</table>
 
  <p>&nbsp;</p>
</form>
</body>
</html>

