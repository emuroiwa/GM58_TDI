<?php
//processing forme
if(isset($_POST['Submit'])){
$stand=$_GET['id'];
	
	    mysql_query("update payment set cash='$_POST[amount]' where id='$_GET[id]'") or die (mysql_error());
	//Write to log file
			 WriteToLog("Payment Amendement Old Amount=$_GET[amount] New Amount $_POST[amount] |Stand = $_GET[stand]",$_SESSION['username']);
					   	msg('Payment Was Edited Successfully');
			 link1("index.php?page=statement.php&id=$_GET[stand]"); 

}
 ?>


<form action="" method="post" name="qualification_form"  >
<center>

<table width="50%" border="0" align="center">
 
 
      <tr>
        <td><div align="center"><span class="style7"><strong>Edit Payment</strong></span></div></td>
       
      </tr>
      
  </table> 
    <div class="errstyle" id="errr"></div>
    <div class="errstyle" id="err"></div>
 <table width="100%">
</table>

  
  <table width="50%" align="center" bgcolor="#FFFFFF">
<tr>
  <td width="109"> <span class="style1 style9">Payment Amount:</span></td>
  <td width="150">
   $ <input type="text" name="amount" id="amount" value="<?php echo $_GET['amount'];?>"  min="0" required /></td>
</tr>
<tr><td colspan="2"  align="center"><div align="center">
  <input type="submit" name="Submit" size="30" class="btn btn-info" onclick="return confirm('Are you sure you want to UPDATE  Informantion ?')" value="Save"/>
</div></td>
</tr>
</table>
</form>


