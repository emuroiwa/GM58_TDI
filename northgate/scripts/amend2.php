<?php
	$rs=mysql_query("SELECT * FROM `stand` WHERE status <> 'Payment_Complete' order by number asc");
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
<center><p><strong><h4>Amendments</h4></strong></p></center>
<div class="table-responsive">
  <table class="table table-striped table-bordered table-hover" id="dataTables-example" width="100%" border="1">
    <thead>
                                        <tr bgcolor="">
    <th width="146">Number</th>
   
    <th width="182">Area In sqm</th>
    <th width="181"><span class="style2">Date</span></th>

    <th width="181"><span class="style2">Cash Out</span></th>
    <th width="181"><span class="style2">EDIT PAYMENT</span></th>
    <th width="181"><span class="style2">Ownership</span></th>
    <th width="181"><span class="style2">Stand</span></th>
  
    </tr>
    </thead>
    <tbody>
                                        <?php
										while($row=mysql_fetch_array($rs))
										{
										
										?>
                                        <tr class="odd gradeX">
    <td bgcolor="#FFFFFF"><span class="style2"><?php echo $row['number']; ?></span></td>
  
    <td bgcolor="#FFFFFF"><span class="style2"><?php echo $row["area"]; ?></span></td>
     <td bgcolor="#FFFFFF"><span class="style2"><?php echo $row["date"]; ?></span></td>
     <td bgcolor="#FFFFFF"><span class="style2"><?php   echo "<a href='index.php?page=amend_cash.php&id=$row[id_stand]'>[CASHOUT]</a>"; ?></span></td>

    <td bgcolor="#FFFFFF"><span class="style2"><?php    echo "<a href='index.php?page=amend_pay.php&id=$row[id_stand]&s=$row[number]'>[PAYMENTS]</a>"; ?></span></td>
     <td bgcolor="#FFFFFF"><span class="style2"><?php      echo "<a href='index.php?page=amend_owner.php&id=$row[id_stand]'>[CHANGE]</a>"; 
?></span></td>   <td bgcolor="#FFFFFF"><span class="style2"><?php        echo "<a href='index.php?page=edit_stand.php&id=$row[id_stand]'>[EDIT]</a>";

?></span></td>
    

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
