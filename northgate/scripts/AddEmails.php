<form action="" method="post" >
  <div align="center">
  <center>
    <table border="0" width="50%" align="center">
   <strong><p align="center"> New Monthly Emails</p></strong>
      <tr><td>Name</td><td><input name="name" type="text" id="name" required></td></tr>
      <tr><td>Surname</td><td><input name="surname" type="text" id="surname" required></td></tr>
      <tr>
        <td>Email</td><td><input type="email" name="email" id="email" ></td></tr>
      <tr>
        <td>Postion</td><td><input name="postion" type="text" id="postion" required></td></tr>
      <!--<tr><td>Sex</td><td><select name="sex"><option>Male</option>
      <option>Female</option>
      </select>
      </td></tr>-->

      
      <tr><td colspan="2"><div align="center">
        <input type="submit" name="submit" value="Save" class='btn btn-info' onclick="return confirm('Are you sure you want to Save?')">
      </div></td></tr>
    </table></center>
  </div>
</form>
<?php
if(isset($_POST['submit'])){
	$date=date("d/m/Y");
	$qry=mysql_query("SELECT * FROM emails where emailaddress='$_POST[email]'")or die(mysql_error());

if(mysql_num_rows($qry)==1){
	   	msg('Email Address already in Database');
			exit;
	}

	mysql_query("INSERT INTO `emails` (`emailaddress`, `name`, `surname`, `postion`) VALUES ('$_POST[email]', '$_POST[name]', '$_POST[surname]', '$_POST[postion]')")or die(mysql_error());
	   	msg('Email Address Added');
					 link1("index.php?page=addemails.php"); 


}
?>