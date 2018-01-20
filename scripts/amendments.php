<?php

//clude 'opendb.php';
if(isset($_POST['submit'])){
	$username = $_POST["username"];
$password = $_POST["password"];
//echo $username; exit;
  if($username == '' OR $password == ''){
	  	 echo ("<SCRIPT LANGUAGE='JavaScript'> window.alert('Enter All fields')
		  javascript:history.go(-1)
		 	</SCRIPT>");  
			exit;
		  }
		  
		  
		   $resul = mysql_query("SELECT * from users where username='$username' AND password = '$password' and suspend='0'");
	

		  $rows = mysql_num_rows($resul);
	if($rows==1)
 {
 	echo("<SCRIPT LANGUAGE='JavaScript'> window.alert('You Have Been Suspended Contact The Adminstrator')
		  javascript:history.go(-1)
		 	</SCRIPT>");  
			exit;

 }
 else{

 $result ="";
$query = "SELECT * from users where username='$username' AND password = '$password' and access='1'";
$result = mysql_query($query);
$rows=mysql_fetch_array($result);

$_SESSION['senior'] = $_POST['username'];

if(!$result)
{
	die( "\n\ncould'nt send the query because".mysql_error());
	exit;
}
	$row = mysql_num_rows($result);
	if($row==1)
 {
 	//header("location: index.php?page=amend.php");
	echo("<SCRIPT LANGUAGE='JavaScript'> 
		location='index.php?page=amend.php'
		 	</SCRIPT>");   

	exit;
 }

  else
 {
echo("<SCRIPT LANGUAGE='JavaScript'> window.alert('Wrong Username And Password Combination')
		 javascript:history.go(-1)
		 	</SCRIPT>");   
	
}
}}
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Untitled Document</title>
</head>

<body><center><h3> Senior Members Credentials Only</h3> <form action="" method="post" target="_blank">
 
 
<table border="0" align="center"><tbody><tr align="left"><td width="35%" height="27"><font size="2" face="verdana">Username</font></td><td width="65%"><font size="2" face="verdana"><input value="" name="username" onfocus="" type="text" id="username" size="14"></font></td></tr><tr><td height="24"><font size="2" face="verdana">Password</font></td><td><font size="2" face="verdana"><input value="" name="password" type="password" id="pass" size="14"></font></td></tr><tr><td></td><td align="left"><input type="submit" name="submit" class='btn btn-info' value=" Authorise"></td></tr></tbody></table>
</form>
</body>
</html>