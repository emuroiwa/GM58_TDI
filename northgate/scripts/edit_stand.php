
<?php
		$rs=mysql_query("select * from stand where id_stand = '$_GET[id]'") or die(mysql_error());
while($row = mysql_fetch_array($rs)){
		$id = $row['number'];
		$area = $row['area'];
		$tc = $row['price'];
		$dep= $row['deposit'];
		$instalments = $row['instalments'];
		$months = $row['months_paid'];
		$purchasedate = $row['datestatus'];
		$vatdate = $row['vatdate'];
	//echo $area;
				}
				

  if(isset($_POST['Submit'])){
 $rs = mysql_query("UPDATE stand set location = '$_POST[location]',area='$_POST[area]',price='$_POST[price]',deposit='$_POST[deposit]',instalments='$_POST[instalments]',months_paid='$_POST[months]' ,datestatus='$_POST[purchase]',vatdate='$_POST[vat]' where id_stand = '$_GET[id]'");
 
  if($rs){
	  			 WriteToLog("Edited Stand Details Location $_POST[location] Area $_POST[area] Price $_POST[price] Deposit '$_POST[deposit] Instalments '$_POST[instalments] months_paid $_POST[months] STAND ID $_REQUEST[id]",$_SESSION['username']);

  ?>
  <script language="javascript">
 alert("successful ");
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

<div class="style4">
  <div align="center"><em>Edit Stands</em></div>
</div>
<link rel="stylesheet" href="ui.datepicker.css" type="text/css" media="screen" title="core css file" charset="utf-8" />


<!-- Include jQuery -->
<script src="jquery.js" type="text/javascript" charset="utf-8"></script>

<!-- Include Core Datepicker JavaScript -->
<script src="ui.full_datepicker.js" type="text/javascript" charset="utf-8"></script>

<!-- Attach the datepicker to dateinput after document is ready -->
<script type="text/javascript" charset="utf-8">
jQuery(function($){
$("#purchase").datepicker();
});
jQuery(function($){
$("#vat").datepicker();
});
</script>

<form action="" method="post" name="" onsubmit="MM_validateForm('number','','R','area','','RinRange0:9999999999','price','','RinRange0:99999999','deposit','','NinRange0:999999999','instalments','','RinRange0:999999999');return document.MM_returnValue" >

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

            <td>Stand Area</td>
            <td>
              <input name="area" type="text" id="area"   value="<?php echo $area; ?> "  />
              sqm            </td></tr>
          <tr>
            <td>Total Cost $</td>
            <td><input name="price" type="text" id="price"   value="<?php echo  $tc;?> "    /></td>
          </tr>
          <tr>
            <td>Deposit $</td>
            <td><input name="deposit" type="text" id="deposit"   value="<?php echo  $dep;?> "    /></td>
          </tr> 
    <tr>
            <td>Monthly Instalments $</td>
            <td><input name="instalments" type="text" id="instalments"    value="<?php echo  $instalments;?> "   /></td>
    </tr> <tr>
            <td>Months To Be Paid</td>
            <td><input name="months" type="text" id="months"    value="<?php echo  $months;?> "   /></td>
    </tr><tr>
            <td>Purchase Date</td>
            <td><input name="purchase" type="text" id="purchase"    value="<?php echo  $purchasedate;?> " required="required"   /></td>
    </tr><tr>
            <td>VAT Date</td>
            <td><input name="vat" type="text" id="vat"    value="<?php echo  $vatdate;?> " required="required"   /></td>
    </tr>
         		  <tr>
            <td></td>
            <td>
              <input name="Submit" type="Submit" id="Submit"  class='btn btn-info' value="Save" onclick="return confirm('Are you sure you want to UPDATE  Informantion ?')"   />            </td>
          </tr>
    </table>
   </form>

