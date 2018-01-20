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
    <table width="391" align="center">

      <tr><td><strong>Full Name</strong></td><td><input type="text" name="name" size="30"></td><td><input type="submit" name="submit" value="Search"></td></tr>
<hr>

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
 $query = mysql_query("SELECT * FROM {$sql} LIMIT {$startpoint} , {$limit} ");
 if(mysql_num_rows($query)==0){echo "No results";}
echo pagination($sql,$limit,$page,'index.php?page=one.php&'); 
while ($info = mysql_fetch_assoc($query)) { 
?>
<table width="50%" bgcolor="#A7A7A7" border="1" align="center">
  <tr>
    <td width="120"><strong>NAME</strong></td>
    <td><?php echo $info['ecnum']." ".$info['name']." ".$info['surname']; ?></td>
    
  </tr>
  
  <tr>
    <td><strong>TEL:NUMBER</strong></td>
    <td><?php echo $info['contact']; ?></td>
    
  </tr>
  <tr>
   
  </tr> <tr> <td><strong><em>SETTINGS</em></strong></td>
    <td><strong><font color="red"><a href="index.php?page=onesms.php&id=<?php echo $info['id']; ?>&n=<?php echo $info['name']; ?>&s=<?php echo $info['surname']; ?>">[Click To Create SMS]</a></font></strong><br><br>
</td>
  </tr>
  
</table><hr><br>

<?php
 }   ?> 
</table>    </table>
