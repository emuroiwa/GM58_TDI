<?php
	$rs=mysql_query("SELECT * FROM `stand` WHERE  status ='Reserved'  order by number asc ");
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
<center><p><strong><h4>Reserved Stands</h4></strong></p></center>
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
 <th width="181"><span class="style2">Process</span></th>
  
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

  
    <td bgcolor="#FFFFFF"><span class="style2"><?php 
	$a="<a href='index.php?page=check_customer.php&id=$row[id_stand]&type=R' class='btn btn-success'>&nbsp;<i class='icon-file-alt icon-large'></i>&nbsp; Add Client</a>";


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
