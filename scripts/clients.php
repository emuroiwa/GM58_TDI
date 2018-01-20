 <!-- Include Core Datepicker Stylesheet -->
<link rel="stylesheet" href="ui.datepicker.css" type="text/css" media="screen" title="core css file" charset="utf-8" />


<!-- Include jQuery -->
<script src="jquery.js" type="text/javascript" charset="utf-8"></script>

<!-- Include Core Datepicker JavaScript -->
<script src="ui.full_datepicker.js" type="text/javascript" charset="utf-8"></script>

<!-- Attach the datepicker to dateinput after document is ready -->
<script type="text/javascript" charset="utf-8">
jQuery(function($){
$("#date").datepicker();
});
</script><?php
include_once ('funcs.php');
include_once ('../opendb.php');
?>
<form action="" method="post">
  <div align="center">
    <table width="100%">
      <tr><td width="15%"><strong>Full Name</strong></td><td width="16%"><input type="text" name="name" size="20"></td><td width="26%"><input type="submit" class='btn btn-info' name="submit" value="Search"></td><td width="5%"><strong>Date</strong></td><td width="16%"><input type="text" name="date"  id="date"size="20"></td><td width="22%"><input type="submit" class='btn btn-info' name="submit2" value="Search"></td></tr>
    </table><hr>

  </div>
</form>
<?php
    $page = (int) (!isset($_GET["current"]) ? 1 : $_GET["current"]);
    $limit = 10;
    $startpoint = ($page * $limit) - $limit;

    //to make pagination
  $sql = "clients";
  if(isset($_POST['submit'])){
	  $sql.= " where name like '%$_POST[name]%' or surname like '%$_POST[name]%'";
  }  if(isset($_POST['submit2'])){
	  $sql.= " where date = '$_POST[date]' ";
  }
  ?>
<div class="records round">


  <ul class="homelist">
<?php

        //show records
 $query = mysql_query("SELECT * FROM {$sql} LIMIT {$startpoint} , {$limit}");
 if(mysql_num_rows($query)==0){echo "No results";}
echo pagination($sql,$limit,$page,'index.php?page=clients.php&'); 
while ($info = mysql_fetch_assoc($query)) { 
?>
<table width="100%" bgcolor="#A7A7A7" border="1">
  <tr>
    <td width="120"><strong>NAME</strong></td>
    <td><?php echo $info['name']." ".$info['surname']; ?></td>
    <td width="113"><strong>SEX</strong></td>
    <td><?php echo $info['sex']; ?></td>
  </tr>
  <tr>
    <td><strong>I.D NUM</strong></td>
    <td><?php echo $info['idnum']; ?></td>
    <td><strong>E-MAIL</strong></td>
    <td><?php echo $info['email']; ?></td>
  </tr>
  <tr>
    <td><strong>TEL:NUMBER</strong></td>
    <td><?php echo $info['contact']; ?></td>
    <td><strong>EMPLOYER</strong></td>
    <td><?php echo $info['employer']; ?></td>
  </tr>
  <tr>
    <td><strong>ADDRESS</strong></td>
    <td colspan="3"><?php echo $info['address']; ?></td>
  </tr> <tr> <td><strong>SETTINGS</strong></td>
    <td><strong><font color="red"><a href="index.php?page=statement.php&client_id=<?php echo $info['id']; ?>&id=<?php echo $info['stand_id']; ?>"class='btn btn-info'><i class='icon-file-alt icon-large'></i>&nbsp;[Click To View Details]</a></font></strong></td>
    <td colspan="3"><strong><a href="index.php?page=edit.php&id=<?php echo $info['id']; ?>"class='btn btn-success'><i class='icon-file-alt icon-large'></i>&nbsp;[Click To Edit Details]</a></strong></td>
  </tr>
  
</table><hr><br>

<?php
 }   ?> 
</table></div>
