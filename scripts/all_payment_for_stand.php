
    
    
    <?php
	$rs=mysql_query("SELECT * FROM stand,payment  WHERE 
payment_type in ('Credit','Deposit')and payment.stand=id_stand and stand='$_GET[id]'  order by payment.id ASC ");
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
<center><p><strong><h4>List Of Payments</h4></strong></p></center>
<div class="table-responsive">
<table width="100%" border="1" bgcolor="#FF0000"><tr bgcolor="#FF0000"><td>
<a href='index.php?page=delall.php&stand=<?php echo $_GET['id'];?>' onclick='return confirm("Are you sure. You want to delete???. This Action can not be undone!!!!")'><center><strong>[click to delete all payments]</strong></center></a></font>
</td></tr></table><br>

  <table class="table table-striped table-bordered table-hover" id="dataTables-example" width="100%" border="1">
    <thead>
                                        <tr bgcolor="">
    <th width="50">Number</th>

    <th width="181"><span class="style2">Purchase Date</span></th>


    <th width="181"><span class="style2">Made BY</span></th>
<!--    <th width="181"><span class="style2">Make Payment</span></th>
-->
    <td  width="46">Months</td>
                        <td  width="89"><strong>Description</strong></td> 
                        <td  width="48"><strong>Amount</strong></td>
                        <td  width="20"><strong>Date</strong></td><!-- <td  width="34">EDIT</td>--><td  width="50" bgcolor="#FF0000"><strong>DELETE</strong></td><td  width="50" bgcolor="#FF0000"><strong>EDIT</strong></td>
  
    </tr>
    </thead>
    <tbody>
                                        <?php
										while($row=mysql_fetch_array($rs))
										{
										
										
                                       		
echo "<tr><td>{$row['number']}</td><td>{$row['location']}</td><td>{$row['capturer']}</td><td>{$row['month']}</td><td>{$row['d']}</td><td>$ {$row['cash']}</td><td>{$row['payment_date']}</td><td><a href='index.php?page=del.php&id=$row[id]&stand=$row[stand]' onclick='return confirm(\"Are you sure. You want to delete???. This Action can not be undone!!!!\")' class='btn btn-danger'><i class='icon-trash icon-large'></i></a></font></td><td><a href='index.php?page=EditPayment.php&id=$row[id]&amount=$row[cash]&stand=$_GET[id]'  class='btn btn-success'><i class='icon-file-alt icon-large'></i></a></font></td></tr>";
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
