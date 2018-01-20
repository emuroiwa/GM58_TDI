<?php
include_once ('funcs.php');
include_once ('../opendb.php');
?>
<form action="" method="post">
  <div align="center">
    <table width="391">
      <tr><td><strong>Name/Surname</strong></td><td><input type="text" name="name" size="30"></td><td><input type="submit" name="submit" value="Search"></td></tr>
    </table>
  </div>
</form>
<?php
    $page = (int) (!isset($_GET["current"]) ? 1 : $_GET["current"]);
    $limit = 10;
    $startpoint = ($page * $limit) - $limit;

    //to make pagination
  $sql = "clients";
  if(isset($_POST['submit'])){
	  $sql.= " where name like '$_POST[name]' or surname like '$_POST[name]'";
  }
  ?>
<div class="records round">


  <ul class="homelist">
<?php
$a=$_REQUEST['id'];
        //show records
 $query = mysql_query("SELECT * FROM {$sql} LIMIT {$startpoint} , {$limit}");
echo pagination($sql,$limit,$page,'index.php?page=oldclients.php&'); 
while ($info = mysql_fetch_assoc($query)) { 
?>
<table width="100%" bgcolor="#A7A7A7" border="1">
  <tr>
    <td width="120"><strong>NAME</strong></td>
    <td><?php echo $info['name']; ?></td>
    <td width="113"><strong>SURNAME</strong></td>
    <td><?php echo $info['surname']; ?></td>
  </tr>
  <tr>
    <td><strong>SEX</strong></td>
    <td><?php echo $info['sex']; ?></td>
    <td><strong>I.D NUM</strong></td>
    <td><?php echo $info['idnum']; ?></td>
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
  </tr>
  <tr><td colspan="4"><div align="center"><font color="red"> <a href="index.php?page=oldclient_transition.php&id=<?php echo $_REQUEST['id']; ?>&client=<?php echo $info['id']; ?>"><strong>CLICK TO MAKE DEPOSIT</strong></a></font>
  </div></td></tr>
</table><hr><br>

<?php
 }   ?> 
</table></div>
