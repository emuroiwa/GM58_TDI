<?php
//error_reporting(0);
include 'opendb.php';
include 'functions.php';
if(isset($_POST['submit'])){
$username = clean($_POST["loginEmail"]);
$password = clean($_POST["loginPass"]);

  if($username == '' OR $password == ''){
	  	 echo ("<SCRIPT LANGUAGE='JavaScript'> window.alert('Enter All fields')
		  javascript:history.go(-1)
		 	</SCRIPT>");  
			exit;
		  }
		  
		  
		 
 else{

 $result ="";
$query = "SELECT * from users where username='$username' AND password = '$password' ";
$result = mysql_query($query);
$rows=mysql_fetch_array($result);
$access=$rows['access'];
$branch=$rows['department'];
$email=$rows['email'];
$q1=$rows['name'];
$q2=$rows['surname'];
$full=$q1." ".$q2;
session_start();
$_SESSION['username'] = $username;
$_SESSION['name'] = $full;
$_SESSION['access'] = $access;
$_SESSION['email'] = $email;
$_SESSION['branch'] = $branch;

if(!$result)
{
	die( "\n\ncould'nt send the query because".mysql_error());
	exit;
}
	$row = mysql_num_rows($result);
	if($row==1)
 {
	 WriteToLog("User Login ",$username);
 	header("location: scripts/index.php");
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
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8" />
        <title>GM 58 SYSTEM</title>
        
        <!-- Our CSS stylesheet file -->
        <link rel="stylesheet" href="assets/css/styles.css" />
        
        <!--[if lt IE 9]>
          <script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
        <![endif]-->
    </head>
    
    <body>

		<div id="formContainer">
			<form id="login" method="post" action="">
				<a href="#" id="flipToRecover" class="flipLink">Forgot?</a>
				<input type="text" name="loginEmail" id="loginEmail" placeholder="Username" />
				<input type="password" name="loginPass" id="loginPass" placeholder="Password" />
				<input type="submit" name="submit" id="submit" value="Login" />
			</form>
			<form id="recover" method="post" action="forgot.php">
				<a href="#" id="flipToLogin" class="flipLink">Forgot?</a>
				<input type="text" name="recoverEmail" id="recoverEmail" placeholder="Email" />
				<input type="submit" name="submit2" value="Recover" />
			</form>
		</div>

        <footer>
	        
            <div align="center"><font color="white">System developed by <a href="" target="_blank">Divine Developers.</a></font></div>
        </footer>
        
        <!-- JavaScript includes -->
		<script src="http://code.jquery.com/jquery-1.7.1.min.js"></script>
		<script src="assets/js/script.js"></script>

    </body>
</html>
