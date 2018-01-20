<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Untitled Document</title>
<style type="text/css">
<!--
.style1 {
	font-size: 36px;
	font-weight: bold;
}
table { border-collapse: collapse;} 
.style3 {font-size: 12px}
.style5 {font-size: 12px; font-style: italic; }
-->
</style>
</head>

<body onload="print()">
<table width="100%" border="0" align="center" style="border:1px solid #000000" bgcolor="#FFFFFF">
<tr>
  <td align="right"> <span class="style3">Ref No:<?php include ('../aut.php');include ('../functions.php'); echo $_GET['id'] ?>
</span></td>
</tr>
  <tr>
    <td align="center"><img src="../images/letterhead.png">
      <p align="center" ><hr /><strong><h3>Customer Receipt</h3></strong></p><hr />

 <table width="24%" border="1">
  <tr>
    <td width="45%">PRICE:</td>
    <td width="35%">$<?php echo $_GET['price']; ?></td>
  </tr>
  <tr>
    <td>WITHDRAWAL / REFUND:</td>
    <td>$<?php echo $_GET['a']; ?></td>
  </tr>
  <tr>
    <td>TOTAL PAYMENT:</td>
    <td>$<?php echo $_GET['t']; ?></td>
  </tr>
  <tr>
    <td>MONTHLY BALANCE:</td>
    <td>$<?php echo $_GET['balance']; ?></td>
  </tr>
  <tr>
    <td>DEPOSIT:</td>
    <td>$<?php echo $_GET['d']; ?></td>
  </tr>
</table>



    
    <p align="center">PAYMENT TYPE:<?php echo "CASH"; ?></p><p align="center">PAYMENT MONTH:<?php echo $month; ?></p>
    <p align="center"><em>Thank You for choosing us,</em></p>
    </td>
  </tr>
  <tr>
    <td><hr />
<span class="style5">Date Issued</span>      <em>
      <?php $date = date('d/m/Y'); echo $date; ?>
    </em> <p align="right"><em> Handled By <?php echo $_SESSION['name']; ?></em></p></td>
  </tr>
</table>
</body>
</html>
