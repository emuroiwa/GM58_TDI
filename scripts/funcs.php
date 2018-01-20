<?php
include '../opendb.php';
function total($s){
	$sql="SELECT Count(*) AS cnt FROM stand where status = '$s'";
$result = mysql_query($sql);
while($row = mysql_fetch_array($result))
  { $tot=$row['cnt'];}return $tot;
	}

//creating a new user
function createUser(){
	$r = '' ;
	$r .= '<form method="post" action="submit_user.php">';
	
	$r .= displayNiceFormBegin();
	
	
	$r .= displayMessage();	
	
	
	$r .= '<td colspan="4"><strong><center><font size="+2">Personal Details</font></center></strong></td>';
	$r .= displayRow('Name','<input type="text" name="fstname" value="'. $_SESSION['fstname'].'"/>', 'fstname','Surname','<input 			type="text" name="lstname" value="'. $_SESSION['lstname'].'"/>', 'lstname');
	 
	 
	$r .= displayRow('Sex',getSex(), 'sex','Address','<textarea name="address" cols="14" rows="1">'. $_SESSION['address'].'</textarea>', 'address');	
	
	
	$r .= displayRow('Email','<input type="text" name="email"  value="'. $_SESSION['userEmail'].'"/>', 'email','I.D Number','<input type="text" name="idnum"  value="'. $_SESSION['userId'].'"/>', 'idnum');
	
	
	$r .= displayRow('Contact Number','<input type="text" name="number"  value="'. $_SESSION['number'].'"/>', 'number','Interests',getInterests(), 'interests');
	
	
	$r .= '<td colspan="4"><strong><center><font size="+2">Login Details</font></center></strong></td>';
	$r .= displayRow('Username','<input type="text" name="userName"  value="'. $_SESSION['userName'].'"/>', 'userName','Access',getAccess(), 'access');
	$r .= displayRow('Password','<input type="password" name="password"  value="'. $_SESSION['password'].'"/>', 'password','Confirm','<input type="password" name="cpassword"  value="'. $_SESSION['cpassword'].'"/>', 'cpassword');
	
	
	$r .= displayRow('Verified',acceptConditions(), 'accept','','<input type="submit" name="submit" value="Submit User Details"/>');
	
	
	
	$r .= displayNiceFormEnd();
	
	
	$r .= '</form>';
	return $r;
}

function displayRow($left, $right, $variablename='', $s1, $s2, $second_variablename=''){
	$r = '';
	
	if(strlen(time($variablename))){
		$sessionvariablename=$variablename.'Message';
		$validationMessage='<div style="font-size: 15px; color: red;">' . $_SESSION[$sessionvariablename] . '</div>';
		$_SESSION[$sessionvariablename]='';
	}
	
	if(strlen(time($second_variablename))){
		$sessionsecond_variablename=$second_variablename.'Message';
		$second_validationMessage='<div style="font-size: 15px; color: red;">' . $_SESSION[$sessionsecond_variablename] . '</div>';
		$_SESSION[$sessionsecond_variablename]='';
	}
	
	
	$r .= '<tr>';
	
	$r .= '<td style="vertical-align: top; padding: 3px;"><strong>' . $left . '</strong></td>';
	$r .= '<td width="200" style="vertical-align: top; padding: 3px;">' . $right . $validationMessage .'</td>';
	$r .= '<td width="100" style="vertical-align: top; padding: 3px;"><strong>' . $s1 . '</strong></td>';
	$r .= '<td style="vertical-align: top; padding: 3px;">' . $s2 . $second_validationMessage .'</td>';
	
	$r .= '</tr>';
	return $r;
	}
	
	function displayOneRow($left, $right, $variablename=''){
	$r = '';
	
	if(strlen(time($variablename))){
		$sessionvariablename=$variablename.'Message';
		$validationMessage='<div style="font-size: 15px; color: red;">' . $_SESSION[$sessionvariablename] . '</div>';
		$_SESSION[$sessionvariablename]='';
	}	
	$r .= '<tr>';
	
	$r .= '<td style="vertical-align: top; padding: 3px;"><strong>' . $left . '</strong></td>';
	$r .= '<td width="200" style="vertical-align: top; padding: 3px;">' . $right . $validationMessage .'</td>';
	$r .= '</tr>';
	return $r;
	}
function displayNiceFormBegin(){

	$r = '';
	$r .= '<table width="" align="center" style="background-color:beige; padding:10px; border:1px dashed #999;"><tr><td>';
	$r .= '<table style="margin: 10px">';
	return $r;
}
function displayNiceFormEnd(){
$r ='';
$r .= '</table>';
$r .= '</td></tr></table>';

return $r;
}

function displayMessage(){

	$r='';
	$message=$_SESSION['message'];
	$_SESSION['message']='';
	
	if(strlen($message)>0){
		$r .='<tr>';
		$r .='<td colspan="4" style="background-color: lemonchiffon; border: 1px dashed orange; padding: 3px; font-size: 15px; text-align: center; font-family: vedana;">'.$message.'</td>';
		$r .='</tr>';
	}
	
	return $r;
	
}
function getInterests(){

	$r='';
	
	$choices=array('classic'=>'Classic','jazz'=>'Jazz','pop'=>'Pop','raggie'=>'Raggie');
	$interests=$_SESSION['interests'];
	
	$r.='<select name="interests[]" multiple="multiple" size="'.count($choices).'">';
	if(count($choices)>0){
		foreach($choices as $key => $value){
			
			//check if it is selected
			if(@in_array($key, $interests)){
				$interestsAttribute =' selected="selected" ';
			}
			else{
				$interestsAttribute ='';
			}
			$r.='<option value="'.$key.'" ' .$interestsAttribute. '>'.$value.'</option>';
			
		}
		
	}
	$r.='</select>';
	
	
	return $r;
	
}

function selectFromDb($table,$column,$select_name){
	$r='';
	$r.='<select name="location">';
	$rs=dropDown($table,$column);
	//$theSession=$_SESSION['medical'];
	for($key = 0 ; $key < count($rs); $key++){
		
		/*if($key==$theSession){
			$selectAttribute=' selected="selected" ';
			}
			else{
			$selectAttribute='';
			}*/
		
	$r.='<option>'.$rs[$key][$column].'</option>'; 
	}	
	$r.='</select>';
	return $r;
}
function dropDown($table,$column){
	
	$qry=mysql_query("select * from $table")or die(mysql_error());
	$rows = null;
			$cnt = 0;
			if($qry){
				while($row = mysql_fetch_assoc($qry)){
					$rows[$cnt] = $row; 
					$cnt++;
				}
		
			}
	return $rows;
}
function getAccess(){

	$r='';
	
	$choices=array('1'=>'Doctor','2'=>'Receptionist');
	$access=$_SESSION['access'];
	$r.='<select name="access"/>';
	if(count($choices)>0){
		foreach($choices as $key => $value){
			
			//cheking if it was selected
			if($key==$access){
			$accessAttribute=' selected="selected" ';
			}
			else{
			$accessAttribute='';
			}
			$r.='<option value="' .$key. '"' .$accessAttribute. '>' .$value. '</option>';
		}
	}
	$r.'</select>';
	
	return $r;
	
}
function getSex(){
	
	
	$r='';
	
	
	$sexStatus=$_SESSION['sex'];
	
	if($sexStatus=='female'){
		$femaleAttribute='checked="checke"';
		$maleAttribute='';		
	}
	else { 
	$femaleAttribute='';
		$maleAttribute='checked="checke"';	
	}
	
	$r.='<input type="radio" name="sex"  value="male" '.$maleAttribute.' />Male<br>';
	$r.='<input type="radio" name="sex"  value="female" '.$femaleAttribute.' />Female';
	
	return $r;	
}

function acceptConditions(){
	$r='';
	$acceptCheck=$_SESSION['accept'];
	if($acceptCheck=='yes'){
		$acceptAttribute= ' checked="checked"';
	}
	else{
		$acceptAttribute= '';
	}
	$r.='<input type="checkbox" name="accept" value="yes" ' .$acceptAttribute.'/>';
	$r.='<span style="font-size: 15px;">Information verified.</span>';
	
	return $r;	
}

function displayUsers($sql){

	$r='';
	
	$r.='<table>';
	while($row=mysql_fetch_array($sql)){
		$r.='<tr>';
		
		$r.='<td style="background-color:#eee; padding: 3px;">'.htmlspecialchars ($row['name']).'</td>';
		$r.='<td style="background-color:#eee; padding: 3px;">'.htmlspecialchars ($row['surname']).'</td>';
		$r.='<td style="background-color:#eee; padding: 3px;">'.htmlspecialchars ($row['contact']).'</td>';
		$r.='<td style="background-color:#eee; padding: 3px;">'.htmlspecialchars ($row['address']).'</td>';
		
		$r.='</tr>';
		
	}
	
	
	$r.='</table>';
	
	return $r;

}

function settingOurCookie(){
	$numberOfVisits=$_COOKIE['numberOfVisits'];
	$numberOfVisits++;
	
	setcookie('numberOfVisits',$numberOfVisits, time() + 60*60*24*7*4*12);
	
	return 'The number of visits is now '.$numberOfVisits;
	
}
function ifExists($table,$column,$value){
	$qry=mysql_query("select * from $table where $column='$value'")or die(mysql_error());
	$result=mysql_num_rows($qry);
	return $result;
}







function pagination($query, $per_page = 10,$page = 1, $url){
$query = "SELECT COUNT(*) as num FROM {$query}"; $row = mysql_fetch_array(mysql_query($query)); $total = $row['num']; $adjacents = "2";

    $page = ($page == 0 ? 1 : $page);  
    $start = ($page - 1) * $per_page;                               

    $prev = $page - 1;                          
    $next = $page + 1;
    $lastpage = ceil($total/$per_page);
    $lpm1 = $lastpage - 1;

    $pagination = "";
    if($lastpage > 1)
    {   
        $pagination .= "";
                $pagination .= "<strong>Viewing Page $page of $lastpage pages</strong>  <br>
";
        if ($lastpage < 7 + ($adjacents * 2))
        {   
            for ($counter = 1; $counter <= $lastpage; $counter++)
            {
                if ($counter == $page)
                    $pagination.= "<a class='current'>$counter</a>";
                else
                    $pagination.= "<a href='{$url}current=$counter'>$counter</a>";                    
            }
        }
        elseif($lastpage > 5 + ($adjacents * 2))
        {
            if($page < 1 + ($adjacents * 2))        
            {
                for ($counter = 1; $counter < 4 + ($adjacents * 2); $counter++)
                {
                    if ($counter == $page)
                        $pagination.= "<a class='current'>$counter </a>";
                    else
                        $pagination.= "<a href='{$url}current=$counter'>$counter </a>";                    
                }
                $pagination.= "";
                $pagination.= "<a href='{$url}current=$lpm1'>$lpm1 </a>";
                $pagination.= "<a href='{$url}current=$lastpage'>$lastpage </a>";      
            }
            elseif($lastpage - ($adjacents * 2) > $page && $page > ($adjacents * 2))
            {
                $pagination.= "<a href='{$url}current=1'>1 </a>";
                $pagination.= "<a href='{$url}current=2'>2 </a>";
                $pagination.= "";
                for ($counter = $page - $adjacents; $counter <= $page + $adjacents; $counter++)
                {
                    if ($counter == $page)
                        $pagination.= "<a class='current'>$counter </a>";
                    else
                        $pagination.= "<a href='{$url}current=$counter'>$counter </a>";                    
                }
                $pagination.= "";
                $pagination.= "<a href='{$url}current=$lpm1'>$lpm1 </a>";
                $pagination.= "<a href='{$url}current=$lastpage'>$lastpage </a>";      
            }
            else
            {
                $pagination.= "<a href='{$url}current=1'>1 </a>";
                $pagination.= "<a href='{$url}current=2'>2 </a>";
                $pagination.= "";
                for ($counter = $lastpage - (2 + ($adjacents * 2)); $counter <= $lastpage; $counter++)
                {
                    if ($counter == $page)
                        $pagination.= "   <a class='current'> $counter </a>";
                    else
                        $pagination.= "   <a href='{$url}current=$counter'> $counter </a>";                    
                }
            }
        }

        if ($page < $counter - 1){ 
			$pagination.= "   <a href='{$url}current=$prev'><strong>  << Back  </strong></a>";
			$pagination.= "   <a href='{$url}current=$next'><strong>  Next >>  </strong></a>";
            $pagination.= "   <a href='{$url}current=$lastpage'><strong>  Last  </strong></a>";
        }else{
            $pagination.= "   <a class='current'><strong> << Back  </strong></a>";
			$pagination.= "   <a class='current'><strong>  Next >> </strong></a>";
            $pagination.= "   <a class='current'><strong>  Last  </strong></a>";
        }
        $pagination.= "\n";        
    }


    return $pagination;

    return $pagination;
} 
function saveSingleRecord($table, $saveArray){
			$sql = BuildSqlQuery($table, $saveArray);
			echo $sql;
			mysql_query($sql) or die(mysql_error());
		}

function BuildSqlQuery($table, $saveArray){
			
			if(empty($table) || empty($saveArray)){				
				return null;
			}
		
			$sql = "INSERT INTO ".$table. "(";
			$i = 0;
			foreach($saveArray as $key => $value){
				$sql.= $key;
				if(($i + 1) != count($saveArray)){
					$sql.=" ,";
				}
				$i++;				
			}
			
			$sql.=" ) VALUES ("; 
			$i = 0;
			foreach($saveArray as $key => $value){
				$sql.= "'".$value."'";
				if(($i + 1) != count($saveArray)){
					$sql.=" ,";
				}
				$i++;				
			}
			
			$sql .= ")";
			return $sql;
		}
		
		/*single form
		
		<form method="post" action="testing_form.php">
  
  <?php echo displayNiceFormBegin();
  echo displayMessage();
  ?>
  
  <td colspan="2"><strong><center><font size="+2">Personal Details</font></center></strong></td>
  
  <?php 
echo displayOneRow('Name','<input type="text" name="fstname" value="'. $_SESSION['fstname'].'"/>', 'fstname');
echo displayOneRow('Surname','<input type="text" name="lstname" value="'. $_SESSION['lstname'].'"/>', 'lstname');
	 
echo displayOneRow('Verified',acceptConditions(), 'accept');
echo displayOneRow('','<input type="submit" name="submit" value="Submit User Details"/>');
	
	
 echo displayNiceFormEnd(); ?>
</form>*/



/*
double form
<form method="post" action="testing_form.php">
  
  <?php echo displayNiceFormBegin();
  echo displayMessage();
  ?>
  
  <td colspan="4"><strong><center><font size="+2">Personal Details</font></center></strong></td>
  
  <?php 
echo displayRow('Name','<input type="text" name="fstname" value="'. $_SESSION['fstname'].'"/>', 'fstname','Surname','<input type="text" name="lstname" value="'. $_SESSION['lstname'].'"/>', 'lstname');
	 
echo displayRow('Verified',acceptConditions(), 'accept','','<input type="submit" name="submit" value="Submit User Details"/>');
	
	
 echo displayNiceFormEnd(); ?>
</form>
*/
?>