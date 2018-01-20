<?php
include 'opendb.php';
include 'functions.php';
include 'email.php';
		 if($_POST['recoverEmail'] == '' ){
	  	 echo ("<SCRIPT LANGUAGE='JavaScript'> window.alert('Enter Email Address')
		  javascript:history.go(-1)
		 	</SCRIPT>");  
			exit;
		 }
function &generatePassword($length=9, $strength=0) { 
        $vowels = 'aeiuy'; 
        $consonants = 'bcdfghjkmnpqrstwz'; 
        if ($strength & 1) { 
                $consonants .= 'BCDFGJLMNPQRSTVXZ'; 
        } 
        if ($strength & 2) { 
                $vowels .= "AEIUY"; 
        } 
        if ($strength & 4) { 
                $consonants .= '23456789'; 
        } 
        if ($strength & 8) { 
                $consonants .= '@#$%'; 
        } 
  
        $password = ''; 
        $alt = time() % 2; 
        for ($i = 0; $i < $length; $i++) { 
                if ($alt == 1) { 
                        $password .= $consonants[(rand() % strlen($consonants))]; 
                        $alt = 0; 
                } else { 
                        $password .= $vowels[(rand() % strlen($vowels))]; 
                        $alt = 1; 
                } 
        } 
        return $password; 
} 
 	function qoutess($str){
$remove[] = "'";
$remove[] = '"';
$remove[] = "-"; // just as another example
$new = str_replace($remove, "", $str);
return $new;
}
$new_password =& generatePassword(); 
 
//$username=clean(qoutess($_POST['username'])); 
$email=clean(qoutess($_POST['recoverEmail'])); 
$sql="update users set password='$new_password' WHERE  email='$email'"; 
$result=mysql_query($sql); 
 
if($result){ 

SendEmail($new_password,$email);
	 WriteToLog("Reset Pasword $email ",$email);
} 
 
else{ 
$content.='<font color="#FF0000">Wrong details provided!!!!! <br />
New password could not be generated.  
If you continue to have issues, please email <a href="mailto:emuroiwa@gmail.com">emuroiwa@gmail.com</a> for assistance.</font>'; 
} 
 
 echo $content;

 ?>
</body>
</html>
