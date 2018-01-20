<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Clients New Sale Form</title>
</head>

<style type='text/css'>
BODY {
    font-family:Arial, Helvetica, sans-serif;
	font-style:normal;

    font-size: 15.5px;
	font-variant:normal;
    background: #FFFFFF;
	
	  
}table {background:transparent;background-color:transparent; border-collapse: collapse;
} 
section { padding:0 !important; margin:0 !important;  width:27.5cm;height:35.2cm !important; overflow:hidden !important; }
</style>
</head>
<SCRIPT LANGUAGE="JavaScript">
if (window.print) {
document.write;
}
</script>
<body onload="print()">
<?php 
error_reporting(0);
$stand=$_GET['id'];
//include ('../aut.php');
	 include ('../opendb.php');
	 include ('../functions.php');
$data=GetCompanyDetails();
$bankname = $data[0];
$bankingdetails = $data[4];
$bankingdetails2 = $data[5];
$bankingdetails3 = $data[6];
$banner = $data[8];
$footer = $data[9];


		//statement
	$pd = mysql_query("SELECT CONCAT(name,' ',surname) as fullname,dob,idnum,address,email,contact,number,area,price,deposit,instalments FROM owners,stand,clients where owners.client_id=clients.id and stand.id_stand=owners.stand_id and id_stand='$stand'")or die(mysql_query());
 while($rowpd = mysql_fetch_array($pd, MYSQL_ASSOC)){
 $name.=$rowpd['fullname'].", ";
 $dob.=$rowpd['dob'].", ";
 $idnum.=$rowpd['idnum'].", ";
 $address.=$rowpd['address'].", ";
 $emailaddress.=$rowpd['email'].", ";
 $phone.=$rowpd['contact'].", ";
 $number=$rowpd['number'];
 $area=$rowpd['area'];
 $total=$rowpd['price'];
 $deposit=$rowpd['deposit'];
 $instalments=$rowpd['instalments'];
    }
?>
<table width="99%" border="1"><tr><td align="center">

<table width="90%">
<tr>
<td><img src="<?php echo $banner;?>"><hr></td>

</tr>
</table>

<table width="90%">
<tr>
<td colspan="3"><h3>NORTHGATE HEIGHTS STANDS</h3><hr></td>

</tr>
<tr>
<td width="33%"><strong>NAME(s):</strong></td>
    <td  width="66%" colspan="2"><?php echo $name;?></td>

</tr>

<tr>
<td width="33%"><p><strong>DATE OF BIRTH: </strong></p></td>
    <td  width="66%" colspan="2"><?php echo $dob;?></td>
</tr><tr>
<td width="33%"><p><strong>I.D NUMBER (s): </strong></p></td>
    <td  width="66%" colspan="2"><?php echo $idnum;?></td>
</tr><tr>
<td width="33%"><p><strong>ADDRESS: </strong></p></td>
    <td  width="66%" colspan="2"><?php echo $address;?></td>
</tr><tr>
<td width="33%"><p><strong>EMAIL ADDRESS: </strong></p></td>
    <td  width="66%" colspan="2"><?php echo $emailaddress;?></td>
</tr><tr>
<td width="33%"><p><strong>PHONE NUMBER: </strong></p></td>
    <td  width="66%" colspan="2"><?php echo $phone;?></td>
</tr><tr>
  <td width="33%"><p><strong>STAND NUMBER: </strong></p></td>
    <td  width="66%" colspan="2"><?php echo $number;?></td>
</tr>
<tr>
  <td><p><strong>AREA:  </strong></p></td>
    <td  width="66%" colspan="2"><?php echo $area;?></td>

</tr>
<tr>
  <td><p><strong>TOTAL COST: </strong></p>
</td>
    <td  width="66%" colspan="2">$<?php echo zva($total);?></td>

</tr>
<tr>
  <td><p><strong>DEPOSIT: </strong></p>
  </td>
      <td  width="66%" colspan="2">$<?php echo zva($deposit);?></td>

</tr>

<tr>
  <td width="33%"><p><strong>MONTHLY INSTALLMENTS: </strong></p></td>
     <td  width="66%" colspan="2">$<?php echo zva($instalments);?></td>

</tr><tr>
  <td width="33%"><p>&nbsp;</p></td>
  <td  width="33%"></td>
  <td  width="33%"></td>
</tr><tr>
  <td width="33%"><p><strong><font color="#CC0000">CLIENT&rsquo;S SIGNATURE </font> </strong></p></td>
  <td  width="33%"><font color="#CC0000"><strong>........................................................</strong></font></td>
  <td  width="33%"><strong><font color="#CC0000">DATE :.............................................</font></strong></td>
</tr><tr>
  <td width="99%" colspan="3"><hr></td>

</tr><tr>
  <td width="33%"><p><strong>BANKING DETAILS</strong></p></td>
    <td  width="66%" colspan="2"><p><strong><font color="#CC0000"><?php echo $bankingdetails."<br>
".$bankingdetails2."<br>
".$bankingdetails3;?></font></strong></p></td>

</tr><tr>
  <td width="33%"><p><strong><font color="#CC0000">ACCOUNT NAME</font> </strong></p></td>
    <td  width="66%" colspan="2"><p><strong><font color="#CC0000"><?php echo $bankname;?></font></strong></p></td>

</tr><tr>
  <td width="33%"><p><strong><font color="#CC0000">BRANCH</font></strong></p></td>
  <td  width="66%" colspan="2"><p><strong><font color="#CC0000">GWERU</font></strong></p></td>
</tr><tr>
  <td width="33%"><p><strong>LAWYERS</strong></p></td>
  <td  width="66%" colspan="2"><p><strong><font color="black">MR MUTSEYEKWA (054-225051/5) 0772 126775<br>

	                                	   DANZIGER AND PARTNERS<br>

	                                 	  159 CABS BUILDING, MAIN STREET, GWERU<br>

                                      		  danzigergwe1@yahoo.com 
</font></strong></p></td>
</tr>

<tr>
  <td colspan="3"><p><strong><font color="#CC0000">(PLEASE BE ADVISED THIS IS ONLY VALID WHEN AN AGREEMENT OF SALE HAS BEEN SIGNED)</font></strong></p></td>
</tr>
</table>



</td></tr></table>
</body>
</html>