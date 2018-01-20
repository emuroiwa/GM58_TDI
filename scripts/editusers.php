
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html><head><link media="all" href="../../WRL/admin/index.php_files/widget49.css" type="text/css" rel="stylesheet">
<style type="text/css">
.underlinemenu{
font-weight: bold;
width: 100%;
}
#btaks{

visibility:hidden;}

.underlinemenu ul{
padding: 6px 0 7px 0; /*6px should equal top padding of "ul li a" below, 7px should equal bottom padding + bottom border of "ul li a" below*/
margin: 0;
text-align: right; //set value to "left", "center", or "right"*/
}

.underlinemenu ul li{
display: inline;
}

.underlinemenu ul li a{
color: #494949;
padding: 6px 3px 4px 3px; /*top padding is 6px, bottom padding is 4px*/
margin-right: 20px; /*spacing between each menu link*/
text-decoration: none;
border-bottom: 3px solid gray; /*bottom border is 3px*/
}

.underlinemenu ul li a:hover, .underlinemenu ul li a.selected{
border-bottom-color: black;
}

</style>

	<link rel="stylesheet" type="text/css" href="../../WRL/admin/css/ui.css">
	<link rel="styleSheet" type="text/css" href="../../WRL/admin/css/main-print.css" media="print">	
	<link rel="styleSheet" type="text/css" href="../../WRL/admin/css/load.css">	
    <style type="text/css">
<!--
.style8 {font-size: 14px; color: #000000;}
.style9 {font-size: 12}
.style11 {
	font-size: 24px;
	font-weight: bold;
}
.style12 {font-size: 14px}
.style13 {
	font-size: 16px;
	font-style: italic;
}
-->
    </style>
    <script src="mootools.js" type="text/javascript" charset="utf-8"></script>	
<script language="javascript">

	function doSearch(){
		document.getElementById('response').innerHTML =  'Please wait while data being processed </br> <img src = "loading42.gif"/>';
		
		new Request({url: 'hostellist.php', onSuccess: notify}).send('id='+document.form1.hostelid.value)
	}
	
		function notify(response){
	
		document.getElementById('response').innerHTML = response;
	}
</script>
<script language="JavaScript" type="text/javascript">
window.history.forward(1);
</script>
</head>
<body style="margin: 0px;">

	    <form name="form1" method="post" action="index.php?page=editlist.php">
	   
	  
	 <center> <table width="460" border="0" align="center" cellpadding="0" cellspacing="5" style="border-bottom:3px solid #000000;">
<tbody>
	            <tr align="center">
	        <td colspan="2" class="style8 style2 style9"><p class="style11"><u> Edit User</u></p>
	          <p class="style13">Select the ec #</p>	          </td>
	        <tr>
            <td width="140"><span class="style12">EC#</span></td>
            <td width="412">
              <?php 
include 'opendb.php';
 
$sql="select * from users";
$rez=mysql_query($sql);
echo "<select name='deptid' id='deptid'>";
?>

<?php
while($row=mysql_fetch_array($rez,MYSQL_ASSOC)){

 echo "<option value='$row[id]'>{$row['username']}</option>"; 
}

?>
<input class="form_submit_button" type="submit" value="search"></td> 
           
      </tr>
           
	      
            
           
          </tr></td>                     
	          </tbody>
	    </table>
	 
</form>
	  
	 <div id="response"></div>

</body></html>