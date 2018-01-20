<center><?php 
$qry=mysql_query("select * from owners,clients,stand where owners.stand_id='$_REQUEST[id]' and clients.id=client_id and id_stand=owners.stand_id")or die(mysql_error());
while($info=mysql_fetch_array($qry)){	
?>
  <hr>
  <strong>Owner Details.</strong>

<table border="1" width="50%" align="center">
  <tr><td><strong>Owner Name</strong></td><td><?php echo $info['name']." ".$info['surname']; ?></td></tr>
<tr><td><strong>Contact Number</strong></td><td><?php echo $info['contact']; ?></td></tr>
<tr><td><strong>Address</strong></td><td><?php echo $info['address']; ?></td></tr>
<tr><td><strong>Email</strong></td><td><?php echo $info['email']; ?></td></tr>
<tr><td><strong>Edit</strong></td><td><a href="index.php?page=edit.php&id=<?php echo $info['id']; ?>"  class='btn btn-success'><i class='icon-file-alt icon-large'></i>&nbsp;[Click To Edit Details]</a></td></tr><br>
<center><?php if(mysql_num_rows($qry)>1){?>
<a href="index.php?page=deleteowner.php&id=<?php echo $info['client_id'];?>" onclick='return confirm("Are you sure?")' class='btn btn-danger'><i class='icon-trash icon-large'></i>&nbsp;Click To Remove From Stand</a><?php }else{  echo "<a href='index.php?page=amend_cash.php&id=$_GET[id]' onclick='return confirm(\"Are you sure ?\")' class='btn btn-danger'><i class='icon-trash icon-large'></i>&nbsp;[Click To Remove From Stand By Cashing Out]</a>";
}?>
</table>
<?php
}
?>