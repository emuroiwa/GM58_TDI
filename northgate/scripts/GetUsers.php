<?php
	$rs=mysql_query("SELECT * FROM `users` ");
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
<center><p><strong><h4>System Users</h4></strong></p></center>
<div class="table-responsive">
  <table class="table table-striped table-bordered table-hover" id="dataTables-example" width="100%" border="1">
    <thead>
                                        <tr bgcolor="">
    <th width="146">Name</th>
    <th width="149"><span class="style2">Surname</span></th>

    <th width="181"><span class="style2">  Email</span></th>
    <th width="181"><span class="style2">Branch</span></th>
    <th width="181"><span class="style2">Access</span></th>
    <th width="181"><span class="style2">Delete</span></th>

  
    </tr>
    </thead>
    <tbody>
                                        <?php
										while($row=mysql_fetch_array($rs))
										{
										
										?>
                                        <tr class="odd gradeX" >
    <td bgcolor="#FFFFFF"><span class="style2"><?php echo $row['name']; ?></span></td>
    <td bgcolor="#FFFFFF"><span class="style2"><?php echo $row["surname"]; ?></span></td>
  
    <td bgcolor="#FFFFFF"><span class="style2"><?php echo $row["email"]; ?></span></td>
     <td bgcolor="#FFFFFF"><span class="style2"><?php echo $row["department"]; ?></span></td>
      <td bgcolor="#FFFFFF"><span class="style2"><?php echo $row["access"]; ?></span></td>

    <td bgcolor="#FFFFFF"><span class="style2"><a href="index.php?page=DeleteEmail.php&userid=<?php echo $row['id'];?>" onclick="return confirm('Are you sure you want to Delete?')" class="btn btn-danger"><i class="icon-trash icon-large"></i>&nbsp;[Delete]</a></span></td>
    

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
