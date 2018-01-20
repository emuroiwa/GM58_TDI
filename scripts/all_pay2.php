<?php
	$rs=mysql_query("SELECT * FROM `stand` WHERE  status <>'Payment_Complete'  order by number asc ");
 if(mysql_num_rows($rs)==0){echo "No results";}
?>
<style type="text/css">
<!--
.style2 {font-size: 12}
-->
</style>
<style type="text/css" title="currentStyle">
			@import "datatable/media/css/demo_page.css";
			@import "datatable/media/css/demo_table.css";
</style>

<script type="text/javascript" language="javascript" src="datatable/media/js/jquery.js"></script>
<script type="text/javascript" language="javascript" src="datatable/media/js/jquery.dataTables.js"></script>
<center><p><strong><h4>Stand Payment</h4></strong></p></center>
<div class="table-responsive">
  <table class="table table-striped table-bordered table-hover" id="dataTables-example" width="100%" border="1">
    <thead>
                                        <tr bgcolor="">
    <th width="50">Number</th>
    <th width="149"><span class="style2">Location</span></th>

    <th width="181"><span class="style2">Purchase Date</span></th>
    <th width="50"><span class="style2">Instalment</span></th>
    <th width="90"><span class="style2">Area</span></th>

    <th width="181"><span class="style2">STATUS</span></th>
<th width="181"><span class="style2">Make Payment</span></th>  <th width="181"><span class="style2">UPLOAD BALANCES</span></th>
  
    </tr>
    </thead>
    <tbody>
                                        <?php
										while($row=mysql_fetch_array($rs))
										{
										
										?>
                                        <tr class="odd gradeX" >
    <td bgcolor="#FFFFFF"><span class="style2"><?php echo $row['number']; ?></span></td>
    <td bgcolor="#FFFFFF"><span class="style2"><?php echo $row["location"]; ?></span></td>
  
    <td bgcolor="#FFFFFF"><span class="style2"><?php echo $row["datestatus"]; ?></span></td>
     <td bgcolor="#FFFFFF"><span class="style2"><?php echo $row["instalments"]; ?></span></td>
     <td bgcolor="#FFFFFF"><span class="style2"><?php echo $row["area"]; ?></span></td>
     <td bgcolor="#FFFFFF"><span class="style2"><?php echo $row["status"]; ?></span> </td>

    <td bgcolor="#FFFFFF"><span class="style2"><?php  if($row['status'] =='For_Sale')
    {$a="<a href='index.php?page=check_customer.php&id=$row[id_stand]' class='btn btn-success'>&nbsp;<i class='icon-file-alt icon-large'></i>&nbsp; Make Deposit</a>";}
    elseif($row['status'] =='Payment_In_Progress') 
    {$a="<a href='index.php?page=NewPayment.php&id=$row[id_stand]' title='MAKE PAYMENT' class='btn btn-info'>&nbsp;<i class='icon-file-alt icon-large'></i>&nbsp;Pay Instalment</a>";}
    else 
        {$a="<a href='index.php?page=deposit.php&id=$row[id_stand]' class='btn btn-successw'>&nbsp;<i class='icon-file-alt icon-large'></i>&nbsp; Deposit TopUp</a>";}

  echo "$a"; ?></span></td>

    <td bgcolor="#FFFFFF"><span class="style2"><?php  if($row['status'] =='For_Sale')
	{$a="<a href='index.php?page=check_customer.php&id=$row[id_stand]' class='btn btn-success'>&nbsp;<i class='icon-file-alt icon-large'></i>&nbsp; Make Deposit</a>";}
	elseif($row['status'] =='Payment_In_Progress'  ) 
	{
	if($_SESSION['access']==1){
	$a="<a href='index.php?page=balances.php&id=$row[id_stand]' title='UPLOAD BALANCES' class='btn btn-info'>&nbsp;<i class='icon-file-alt icon-large'></i>&nbsp;Upload Balance</a>";
	}else{
	$a="<a href='index.php?page=NewPayment.php&id=$row[id_stand]' title='MAKE PAYMENT' class='btn btn-info'>&nbsp;<i class='icon-file-alt icon-large'></i>&nbsp;Pay Instalment</a>";
	}
	}
	else 
		{$a="<a href='index.php?page=deposit.php&id=$row[id_stand]' class='btn btn-successw'>&nbsp;<i class='icon-file-alt icon-large'></i>&nbsp; Deposit TopUp</a>";}

  echo "$a"; ?></span></td>
    

 </tr>
                                        <?php
										}
										?>
    </tbody>
  </table>

    <script>
    $(document).ready(function() {
        $('#dataTables-example').dataTable();
    });
    </script><br>
<br>
<br>
<br>
<br>

</div>
