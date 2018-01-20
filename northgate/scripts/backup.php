<?php
error_reporting(0);
$database="northgate";
define("DB_NAME", $database);

global $result;
$result="";

$backupFile = $database.date("_m_d_Y").'.sql'; //backup file name
$result .= "# MySQL Data Backup of ".$database."\n"; 
$result .= "# This was generated on " . date("m/d/Y") . "\n\n"; //just an information

$dbhandle= mysql_connect('localhost','root','') or die ("Could not connect");
$result="";
$result1="";

$tables = mysql_list_tables($database); 
for($i = 0; $i < mysql_num_rows($tables); $i++) 
{ 
$result1="";
$table = mysql_tablename ($tables, $i); 
$result .= "# Start of $table \n"; 
//for schema
$result	.="DROP TABLE IF EXISTS `$table`;\n";	
$result .= "CREATE TABLE `$table` ( \n"; 
$result_fld = mysql_query( "SHOW FIELDS FROM ".$table, $dbhandle ); 

while($row1 = mysql_fetch_row($result_fld) ) {

$result.= "`".$row1[0]."`"." ".$row1[1]." " ;
if($row1[2]=="NO")
$result.=" NOT NULL ";
if(($row1[4]!=""))
$result.=" default `".$row1[4]."` ";
if($row1[5]!="")
$result.=" ".$row1[5]." ";
if($row1[3]=="PRI")
$result1.=" PRIMARY KEY (`".$row1[0]."`),\n";
if($row1[3]=="MUL")
$result1.=" KEY ".$row1[0]."("."`".$row1[0]."`"."),\n";
$result.=",\n";
} 
$result .= $result1; 
$result .= "\n);\n\n"; 

//for schema

$query = mysql_query("select * from $table"); 
$num_fields = mysql_num_fields($query); 
$numrow = mysql_num_rows($query); 

while( $row = mysql_fetch_array($query, MYSQL_NUM)) 
{ 
$result .= "INSERT INTO ".$table." VALUES("; 
for($j=0; $j<$num_fields; $j++) 
{ 
$row[$j] = addslashes($row[$j]); 
$row[$j] = str_replace("\n","\\n",$row[$j]); 
$row[$j] = str_replace("\r","",$row[$j]); 
if (isset($row[$j])) 
$result .= "\"$row[$j]\""; 
else 
$result .= "\"\""; 
if ($j<($num_fields-1)) 
$result .= ", "; 
} 
$result .= ");\n"; 
} 

if ($i+1 != mysql_num_rows($tables)) 
$result .= "\n"; 
} 
if((isset($_GET['action'])) && ($_GET['action'] == "save"))
{	 ob_clean(); 
ob_start(); 
Header("Content-type: application/octet-stream"); 
Header("Content-Disposition: attachment; filename=$backupFile"); 
echo $result;
ob_end_flush();
echo "Backup Taken Successfully!!!"; 
exit; 
}

?>
<table border=1 align="center" cellpadding="5" cellspacing="0" bordercolor="#C6D78C">
<form name='MyForm' method='post' action='backup.php?action=save'>
<tr bgcolor="#FFFFFF">
<td colspan = 2 align="center" ><input type='submit' name='submit' value='CLICK TO BACKUP YOUR DATABASE'class = "butt1"></td>
</tr>
</form>
</table>