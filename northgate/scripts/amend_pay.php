
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
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
</script>
<script type="text/javascript" charset="utf-8">
jQuery(function($){
$("#date1").datepicker();
});
</script>
<script language = "Javascript">
/**
 * DHTML date validation script. Courtesy of SmartWebby.com (http://www.smartwebby.com/dhtml/datevalidation.asp)
 */
// Declaring valid date character, minimum year and maximum year
var dtCh= "/";
var minYear=1900;
var maxYear=2100;

function isInteger(s){
	var i;
    for (i = 0; i < s.length; i++){   
        // Check that current character is number.
        var c = s.charAt(i);
        if (((c < "0") || (c > "9"))) return false;
    }
    // All characters are numbers.
    return true;
}

function stripCharsInBag(s, bag){
	var i;
    var returnString = "";
    // Search through string's characters one by one.
    // If character is not in bag, append to returnString.
    for (i = 0; i < s.length; i++){   
        var c = s.charAt(i);
        if (bag.indexOf(c) == -1) returnString += c;
    }
    return returnString;
}

function daysInFebruary (year){
	// February has 29 days in any year evenly divisible by four,
    // EXCEPT for centurial years which are not also divisible by 400.
    return (((year % 4 == 0) && ( (!(year % 100 == 0)) || (year % 400 == 0))) ? 29 : 28 );
}
function DaysArray(n) {
	for (var i = 1; i <= n; i++) {
		this[i] = 31
		if (i==4 || i==6 || i==9 || i==11) {this[i] = 30}
		if (i==2) {this[i] = 29}
   } 
   return this
}

function isDate(dtStr){
	var daysInMonth = DaysArray(12)
	var pos1=dtStr.indexOf(dtCh)
	var pos2=dtStr.indexOf(dtCh,pos1+1)
	var strMonth=dtStr.substring(0,pos1)
	var strDay=dtStr.substring(pos1+1,pos2)
	var strYear=dtStr.substring(pos2+1)
	strYr=strYear
	if (strDay.charAt(0)=="0" && strDay.length>1) strDay=strDay.substring(1)
	if (strMonth.charAt(0)=="0" && strMonth.length>1) strMonth=strMonth.substring(1)
	for (var i = 1; i <= 3; i++) {
		if (strYr.charAt(0)=="0" && strYr.length>1) strYr=strYr.substring(1)
	}
	month=parseInt(strMonth)
	day=parseInt(strDay)
	year=parseInt(strYr)

	
	if (strDay.length<1 || day<1 || day>31 || (month==2 && day>daysInFebruary(year)) || day > daysInMonth[month]){
		alert("Please enter a valid day")
		return false
	}
	if (strYear.length != 4 || year==0 || year<minYear || year>maxYear){
		alert("Please enter a valid 4 digit year between "+minYear+" and "+maxYear)
		return false
	}
	if (dtStr.indexOf(dtCh,pos2+1)!=-1 || isInteger(stripCharsInBag(dtStr, dtCh))==false){
		alert("Please enter a valid date")
		return false
	}
return true
}

function ValidateForm(){
	var dt=document.frmSample.date1
	if (isDate(dt.value)==false){
		dt.focus()
		return false
	}
    return true
 }
 function ValidateForm(){
	var dt=document.frmSample.date
	if (isDate(dt.value)==false){
		dt.focus()
		return false
	}
    return true
 }

</script>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title></title>
<meta name="" content="" />
<meta name="keywords" content="" />
<meta name="description" content="" />
<style type="text/css">
<!--
.style1 {font-size: 12px}
.style2 {font-size: 14px}
.style4 {font-size: 12px; font-weight: bold; }
-->
</style>
</head>
<body>
<div align="center">
	<div class="main_div">
		<div class="main">
			<div class="main_site">
			  <div class="header">Search payments for stand number <?php echo $_GET['s'];?> <br />

				  <br />
<a href='index.php?page=all_payment_for_stand.php&id=<?php echo $_GET['id'];?>'><strong>Click To View Payments For Stand <?php echo $_GET['s'];?></strong> </a>
					<h1>
					 <center> <table width="70%" border="0" >
					   
					<form action="" method="post" onSubmit="return ValidateForm()" name="frmSample">
					<tr><td width="216">Start Date</td>
					<td width="224">
					  <input type="text" name="date" id="date" />				   </td>
					<td width="176">End Date</td>
					<td width="304">
                    <input type="text" name="date1" id="date1" /> 
					
					<td width="6%"><input name="Submit" type="submit" class='btn btn-info' value="Search" /></td></tr>
					</form>
					</table>
				  <br />
</h1>
					
					 
								 
                                    
						
                      <?php
					 
	  if (isset($_POST['Submit'])){
		  ?> <table width="100%" border="1">
					  <tr bgcolor="white"> 
                                  <td  width="40">Stand #</td>
                       <td  width="53">Location</td>
                                                <td  width="63">Area</td> 
<td  width="86">Capturer</td>
					   <td  width="46">Months</td>
                        <td  width="89">Description</td> 
                        <td  width="48">Amount</td>
                        <td  width="29">Date</td> <td  width="34">Delete</td>
   								 <?php
	
	  $date =$_POST['date'];
	  $date1 =$_POST['date1'];
	   $nguva=date('d/m/Y');
	 
	  $result ="";
	 $result = mysql_query("SELECT * FROM stand,payment  WHERE 
payment.date BETWEEN '$date' AND '$date1' and payment_type='Credit' and payment.stand=id_stand and stand='$_GET[id]'  order by stand.number ASC ")or die(mysql_query());
		 
	   if(!$result)
{
	die( "\n\ncould'nt send the query because".mysql_error());
	exit;
}
	$rows = mysql_num_rows($result);
	if($rows==0)
 {
 	echo("<SCRIPT LANGUAGE='JavaScript'> window.alert('No report for this period')
		  javascript:history.go(-1)
		 	</SCRIPT>");  
			exit;

 }
 		
	
  while($row = mysql_fetch_array($result, MYSQL_ASSOC))
	  	  
{

		
		
echo "<tr><td>{$row['number']}</td><td>{$row['location']}</td><td>{$row['area']} (sqm)</td><td>{$row['capturer']}</td><td>{$row['month']}</td><td>{$row['d']}</td><td>$ {$row['cash']}</td><td>{$row['date']}</td><td><a href='index.php?page=del.php&id=$row[id]&stand=$row[stand]' onclick='return confirm(\"Are you sure. You want to delete???. This Action can not be undone!!!!\")'>[click to delete]</a></font></td></tr>";
}
}


?>        </tr></table>     

							</div>
			</div>
	  </div>
  </div>

	
</div>

</body>
</html>