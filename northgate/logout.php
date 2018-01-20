<?php 
session_start();
include 'functions.php';
			 WriteToLog("Logout | User = $_SESSION[name]",$_SESSION['username']);

unset($_SESSION['username']);
unset($_SESSION['name']);
unset($_SESSION['branch']);


session_unset();
session_destroy();

header("location:index.php");
?>