
<?php mysql_query("INSERT INTO `owners` (`client_id`, `owners_date`, `stand_id`) VALUES ('$_REQUEST[client]', '$date', '$_REQUEST[id]')
")or die(mysql_error());
		   	 header("location: index.php?page=deposit.php&id=$_REQUEST[id]"); 

 ?>
<script type="text/javascript">
	window.location='index.php?page=deposit.php&id=<?php echo $_REQUEST[id];?>'

</script>