
<?php
	  ///*******************************************************************************************************************************************************************
	  ///*******************************************************************************************************************************************************************
	  ///*******************************************************FUNCTIONS PAGE**********************************************************************************************
	  ///*******************************************************ERNEST MUROIWA**********************************************************************************************
	  ///************************************************************GM58***************************************************************************************************
	  ///*******************************************************************************************************************************************************************
	  ///*******************************************************************************************************************************************************************
	  ///*******************************************************************************************************************************************************************

$date = date('Y-m-d H:i:s');
$monthdays=date('t')."days";
		$dt = strtotime("$date + 1month");
		$nextmonth = date('d/m/Y',$dt);
 $Today = date('y:m:d');
       $new = date('l, F d, Y', strtotime($Today));
	   $month = date('F-Y');
			$vv = strtotime($month);
$nextmonth = date("F-Y", strtotime("+1 month", $vv));//echo $final;
$year = date('Y');
$time = date('m/d/Y - H:m:s');
///Get Connection***************************************************************************************************************************************************
 	 function getConnection() {
mysql_connect('localhost','root','','3306');
mysql_select_db('gm58');
  }
  ///WriteToLog***************************************************************************************************************************************************

function WriteToLog($log,$user){
	 $Today = date('y-m-d');
	 $time = date('m/d/Y - H:m:s');
		$myFile = "logs/$Today-LOG.txt";
$fh = fopen($myFile, 'a') or die("can't open file");
fwrite($fh, $time." - $user - ".$log."\n");
mysql_query("INSERT INTO `systemlogs` (`details`, `detailsdate`, `user`) VALUES ('$log', now(), '$user')");
fclose($fh);
return true;
	}
	  ///*******************************************************************************************************************************************************************
function CountMonths($startmonth){
$date1 = new DateTime($startmonth);
$date2 = new DateTime(date('Y-m'));
$interval = date_diff($date1, $date2);
return $interval->m + ($interval->y * 12) ;
}
function diffMonth($from, $to) {

        $fromYear = date("Y", strtotime($from));
        $fromMonth = date("m", strtotime($from));
        $toYear = date("Y", strtotime($to));
        $toMonth = date("m", strtotime($to));
        if ($fromYear == $toYear) {
            return ($toMonth-$fromMonth)+1;
        } else {
            return (12-$fromMonth)+1+$toMonth;
        }

    }
	function ValidateDate($date){ 
    if (preg_match("/^(\d{4})-(\d{2})-(\d{2}) ([01][0-9]|2[0-3]):([0-5][0-9]):([0-5][0-9])$/", $date, $matches)) { 
        if (checkdate($matches[2], $matches[3], $matches[1])) { 
            return true; 
        } 
    } 
    return false; 
} 
		  ///*******************************************************************************************************************************************************************

function GetInstalmentMonth($purchaseDate,$NumberOfMonths){
		//$dt = strtotime($purchaseDate."+ ".$NumberOfMonths."month");
		$dt = strtotime("$purchaseDate +".floor($NumberOfMonths)."month");
		$InstalmentMonth = date('F-Y',$dt);
		return $InstalmentMonth;
	
	}

function GMLastMonth($xx){
		$pd = mysql_query("SELECT * FROM stand where id_stand='$xx'")or die(mysql_query());
 while($rowpd = mysql_fetch_array($pd, MYSQL_ASSOC)){
 $purchasedate=$rowpd['datestatus'];
 $vatdate=$rowpd['vatdate'];
 $vat=$rowpd['vat'];  $Area=$rowpd['area'];
  $instalments=$rowpd['instalments'];
   
return GetInstalmentMonthDate($purchasedate,GetMonthsPaid(GMSum($xx,"Credit"),$instalments));
	
   }

}
function GMSum($id,$lastmonth){
		  $lastdate = mysql_query("SELECT Sum(payment.cash) AS ss FROM `payment` WHERE payment_type = '$lastmonth' AND payment.stand = '$id' ")or die(mysql_query());
 while($rwdate = mysql_fetch_array($lastdate, MYSQL_ASSOC)){
return $rwdate['ss'];   
  }
 
	}
	
		  ///*******************************************************************************************************************************************************************
		  function SendSMS($msgsms,$contact,$callerid){
// Bulk SMS's POST URL
$postUrl = "http://193.105.74.59/api/sendsms/xml";
// XML-formatted data
$xmlString =
"<SMS>
<authentification>
<username>TDInvestment</username>
<password>tS8ff1Cg</password>
</authentification>
";
$xmlString.="
<message>
<sender>".$callerid."</sender>
<text>".$msgsms."</text>
<recipients>
<gsm>".$contact."</gsm>
</recipients>
</message>";
$xmlString.="
</SMS>";
$fields = "XML=" . urlencode($xmlString);
$ch = curl_init($postUrl);
curl_setopt($ch, CURLOPT_URL, $postUrl);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
curl_exec($ch);
curl_close($ch);
return true;
		  }
		  	  ///*******************************************************************************************************************************************************************
	function GetInstalmentMonthDate($purchaseDate,$NumberOfMonths){
		//$dt = strtotime($purchaseDate."+ ".$NumberOfMonths."month");
		$dt = strtotime("$purchaseDate +".$NumberOfMonths."month");
		$InstalmentMonth = date('Y-m-d',$dt)." 00:00:00";
		return $InstalmentMonth;
	
	}
	  ///*******************************************************************************************************************************************************************
	function FormatDate($date){
				  $dd = substr($date,0,2);
				  $mm = substr($date,3,2);
				  $yy = substr($date,6.4);
				  return trim($mm."/".$dd."/".$yy);
	
	}
		  ///*******************************************************************************************************************************************************************
	//'%Y-%m-%d 00:00:00'
	function FormatDateTime($date){
				  $dd = substr($date,0,2);
				  $mm = substr($date,3,2);
				  $yy = substr($date,6.4);
				  if($mm>12){
					 				  return trim($yy."-".$dd."-".$mm." 00:00:00");
 
					  }
				  
				  return trim($yy."-".$mm."-".$dd." 00:00:00");
	
	}
		  ///*******************************************************************************************************************************************************************

	function GetMonthsPaid($Balance,$Instalments){
				return floor($Balance/$Instalments);
	
	}
		  ///*******************************************************************************************************************************************************************

	function start_instalment($id){
  $lastdate = mysql_query("SELECT * FROM stand where id_stand='$id'")or die(mysql_query());
 while($rwdate = mysql_fetch_array($lastdate, MYSQL_ASSOC)){
  $lastmonth=$rwdate['start_instalment'];
  }
  return $lastmonth;
	}
		  ///*******************************************************************************************************************************************************************

		function CheckBalance($id){
  $r = mysql_query("SELECT * FROM payment where stand='$id'")or die(mysql_query());
if(mysql_num_rows($r)==1)
{
  return "Balance Captured";
}else
{
	  return "No Balance Captured";
}
	}
		  ///*******************************************************************************************************************************************************************

function pay($cash,$stand,$month,$pdate,$pd,$vd){
	global $date;
	global $year;
	 mysql_query("insert into payment(date,cash,stand,capturer,month,year,payment_type,payment_date,d,value_date) values('$date','$cash','$stand','$_SESSION[name]','$month','$year','Credit','$pdate','$pd','$vd')") or die(mysql_error());
	 return true;
	}
function CashOut($id){
  $qry = mysql_query("SELECT * FROM stand where id_stand='$id'")or die(mysql_query());
   while($row = mysql_fetch_array($qry, MYSQL_ASSOC)){
  mysql_query("INSERT INTO `cashoutstand` (`location`, `area`, `number`, `price`, `date`, `deposit`, `instalments`, `datestatus`, `months_paid`, `start_instalment`, `id_stand`, `vat`, `vatdate`) VALUES ('$row[location]', '$row[area]', '$row[number]', '$row[price]', '$row[date]', '$row[deposit]', '$row[instalments]', '$row[datestatus]', '$row[months_paid]', '$row[start_instalment]', '$row[id_stand]','$row[vat]', '$row[vatdate]')") or die(mysql_error());
  }
    $lastdate = mysql_query("SELECT * FROM payment where stand='$id'")or die(mysql_query());
   while($rwdate = mysql_fetch_array($lastdate, MYSQL_ASSOC)){
  mysql_query("insert into cashoutpayment(date,cash,stand,capturer,month,year,payment_type,payment_date,d,value_date) values('$rwdate[date]','$rwdate[cash]','$rwdate[stand]','$_SESSION[name]','$rwdate[month]','$rwdate[year]','Credit','$rwdate[payment_date]','$rwdate[d]','$rwdate[value_date]')") or die(mysql_error());
  }
   $lastdate1 = mysql_query("SELECT * FROM clients where stand_id='$id'")or die(mysql_query());
   while($rwdate1 = mysql_fetch_array($lastdate1, MYSQL_ASSOC)){
  mysql_query("INSERT INTO `cashoutclients` (`name`, `surname`, `address`, `email`, `contact`, `idnum`, `stand_id`, `sex`, `dob`, `ecnum`, `file_number`, `date`, `id`) VALUES ('$rwdate1[name]', '$rwdate1[surname]', '$rwdate1[address]', '$rwdate1[email]', '$rwdate1[contact]', '$rwdate1[idnum]', '$rwdate1[stand_id]', '$rwdate1[sex]', '$rwdate1[dob]', '$rwdate1[ecnum]', '$rwdate1[file_number]', '$rwdate1[date]', '$rwdate1[id]')") or die(mysql_error());
  }
   $lastdate2 = mysql_query("SELECT * FROM `owners` where stand_id='$id'")or die(mysql_query());
   while($rwdate2 = mysql_fetch_array($lastdate2, MYSQL_ASSOC)){
  mysql_query("INSERT INTO `cashoutowners`  (`client_id`, `owners_date`, `stand_id`) VALUES ('$rwdate2[client_id]', '$rwdate2[owners_date]', '$rwdate2[stand_id]')") or die(mysql_error());
  }
  return true;
}
	  ///*******************************************************************************************************************************************************************

function lastrans($id){
  $lastdate = mysql_query("SELECT * FROM payment where stand='$id'  ORDER BY id DESC LIMIT 1")or die(mysql_query());
  while($rwdate = mysql_fetch_array($lastdate, MYSQL_ASSOC)){
  $lastmonth=$rwdate['payment_type'];
  }
  return $lastmonth;
}


function GetLastMonthPaid($id){
$pd = mysql_query("SELECT * FROM stand where id_stand='$id'")or die(mysql_query());
 while($rowpd = mysql_fetch_array($pd, MYSQL_ASSOC)){
 $purchasedate=$rowpd['datestatus'];
 $vatdate=$rowpd['vatdate'];
 $vat=$rowpd['vat'];
    }
    $lastdate = mysql_query("SELECT * FROM payment where stand='$id'  ORDER BY id DESC LIMIT 1")or die(mysql_query());
  while($rwdate = mysql_fetch_array($lastdate, MYSQL_ASSOC)){
  $s=debit($row['payment_type'],$row['cash']);
   }
$creditbalance=$balance2;
  $balance2=$balance2-$s; //$balance2=$balance2+$s1;  
  $CumulativeDebit+=$s;
  $CumulativeDebitAfterVat+=$s;
    $CumulativeDebitAfterVat1= $CumulativeDebitAfterVat-GetBeforeVat($id);

  if($vat=="YES"){
         
    $monthz=GetInstalmentMonth($vatdate,GetMonthsPaid($CumulativeDebitAfterVat1,$instalments));
      
  }else{
      $monthz=GetInstalmentMonth($purchasedate,GetMonthsPaid($CumulativeDebit,$instalments));
  
    }
return $monthz;
}
	  ///*******************************************************************************************************************************************************************
function lastmonth($id){
	$lastmonth="";
  $lastdate = mysql_query("SELECT * FROM payment where stand='$id' and payment_type='Credit' ORDER BY id DESC LIMIT 1")or die(mysql_query());
  if(mysql_num_rows($lastdate)==0){
  $lastdate = mysql_query("SELECT * FROM payment where stand='$id' and payment_type='Deposit' ORDER BY id DESC LIMIT 1")or die(mysql_query());
 while($rwdate = mysql_fetch_array($lastdate, MYSQL_ASSOC)){
  $lastmonth=$rwdate['month'];
  }
  }
  else{
 while($rwdate = mysql_fetch_array($lastdate, MYSQL_ASSOC)){
  $lastmonth=$rwdate['month'];
  }}
  return $lastmonth;
	}
	  ///*******************************************************************************************************************************************************************

	function nextmonth($current){
				$lastmonth1="20-".$current;
$timestamp = strtotime($lastmonth1); 
$new_date = date('d-m-Y', $timestamp);
				 $month_now = strtotime(date($new_date));
			$nextmonth = date("F-Y", strtotime("+1 month", $month_now));
			return $nextmonth;	
		}
		
		//GetCompanyDetail
		  ///*******************************************************************************************************************************************************************
			
				function GetCompanyDetails(){
		  $query = mysql_query("SELECT * FROM `companydetails` ")or die(mysql_query());
while($rw = mysql_fetch_array($query, MYSQL_ASSOC)){
  $name=$rw['name'];
  $branch=$rw['branch'];
  $address=$rw['address'];
  $contacts=$rw['contacts'];
  $logo=$rw['logo'];
    $banner=$rw['banner'];
    $footer=$rw['footer'];
  $bankingdetails=$rw['bankingdetails'];
  $bankingdetails2=$rw['bankingdetails2'];
  $bankingdetails3=$rw['bankingdetails3'];
      return array($name,$branch,$address,$contacts,$bankingdetails,$bankingdetails2,$bankingdetails3,$logo,$banner,$footer);
  }
	}
			  ///*******************************************************************************************************************************************************************


			function chikwereti($nextmonth){
		  $lastdate = mysql_query("SELECT * FROM `chikwereti` WHERE `month`='$nextmonth'")or die(mysql_query());
$lastcash=mysql_num_rows($lastdate);
  return $lastcash;
	}
			  ///*******************************************************************************************************************************************************************

		function lastmwedzi($current){
				$lastmonth1="20-".$current;
$timestamp = strtotime($lastmonth1); 
$new_date = date('d-m-Y', $timestamp);
				 $month_now = strtotime(date($new_date));
			$nextmonth = date("F-Y", strtotime("-1 month", $month_now));
			return $nextmonth;	
		}
			  ///*******************************************************************************************************************************************************************

		function msgheader($a,$b,$stand){
			 		 if($a==$b){ 
	 mysql_query("update stand set status='Payment_Complete',datestatus='$date' where id_stand='$stand'") or die (mysql_error());
		   	$e="<SCRIPT LANGUAGE='JavaScript'> window.alert('Payment Successful.......')
		index.php?page=sale&id=$stand'
		 	</SCRIPT>" ;				
}else{
		$e="<SCRIPT LANGUAGE='JavaScript'> window.alert('Payment Successful.......')
		index.php?page=sale&id=$stand'
		 	</SCRIPT>";  }
			return $e;
			}
				  ///*******************************************************************************************************************************************************************

	function currentmonth($id){
  $lastdate = mysql_query("SELECT * FROM payment where stand='$id' and payment_type!='Debit' ORDER BY id DESC LIMIT 1")or die(mysql_query());
 while($rwdate = mysql_fetch_array($lastdate, MYSQL_ASSOC)){
  $lastmonth=$rwdate['month'];
   }
  return $lastmonth;
	}
		  ///*******************************************************************************************************************************************************************
	function lastmonthdate($id){
  $lastdate = mysql_query("SELECT * FROM payment where stand='$id' and payment_type!='Debit' ORDER BY id DESC LIMIT 1")or die(mysql_query());
 while($rwdate = mysql_fetch_array($lastdate, MYSQL_ASSOC)){
  return $rwdate['date'];
   }
 // return $lastmonth;
	}
		  ///*******************************************************************************************************************************************************************
function lastInterestPayment($id){
  $lastdate = mysql_query("SELECT * FROM `interestpayment`  where stand='$id' and payment_type!='Debit' ORDER BY id DESC LIMIT 1")or die(mysql_query());
  if(mysql_num_rows($lastdate)==0){

  	return "1970-01-01 00:00:00";
  }
 while($rwdate = mysql_fetch_array($lastdate, MYSQL_ASSOC)){
  return $rwdate['date'];
   }
 // return $lastmonth;
	}
		  ///*******************************************************************************************************************************************************************

	function lastcash($id,$lastmonth){
		  $lastdate = mysql_query("SELECT Sum(payment.cash) AS ss FROM `payment` WHERE payment.`month` = '$lastmonth' AND payment.stand = '$id' ")or die(mysql_query());
 while($rwdate = mysql_fetch_array($lastdate, MYSQL_ASSOC)){
return $rwdate['ss'];   
  }
 
	}
		  ///*******************************************************************************************************************************************************************

		function lastcash2($id,$lastmonth){
			  $lastdate = mysql_query("SELECT Sum(payment.cash) AS ss FROM `payment` WHERE payment.`month` = '$lastmonth' AND payment.stand = '$id' and payment_type='credit'")or die(mysql_query());
 while($rwdate = mysql_fetch_array($lastdate, MYSQL_ASSOC)){
  $lastcash=$rwdate['ss'];   
  }
  return $lastcash;
	}
	
		  ///*******************************************************************************************************************************************************************

	
function zva($number)
{
	$str_num = number_format( $number, 2, ',', ' ' );
	return $str_num;
	}
		  ///*******************************************************************************************************************************************************************

	function getmonth($type,$stand){
	$r1 = mysql_query("SELECT * FROM stand,payment where status='Payment_In_Progress' and stand='$stand' and d='$type' ORDER BY payment.id DESC LIMIT 1")or die(mysql_query());
  while($rw1 = mysql_fetch_array($r1, MYSQL_ASSOC)){
  $instalments=$rw1['month'];
return $instalments;}
	}
	  ///*******************************************************************************************************************************************************************

function getdebit($id){
	$r2=mysql_query("SELECT Sum(payment.cash) AS debit FROM `payment` WHERE payment.stand = '$id' and payment.payment_type = 'Debit' ")or die(mysql_query());
 while($rw2 = mysql_fetch_array($r2, MYSQL_ASSOC)){
  $debit=$rw2['debit'];
    }
	if(mysql_num_rows($r2)==0){$debit=0;}
	
		$r3=mysql_query("SELECT
Sum(payment.cash) AS debit
FROM `payment`
WHERE
payment.stand = '$id' and payment.payment_type = 'Cashout' ")or die(mysql_query());
 while($rw3 = mysql_fetch_array($r3, MYSQL_ASSOC)){
  $debit1=$rw3['debit'];
    }
	if(mysql_num_rows($r3)==0){$debit1=0;}
	return $debit+$debit1;
	}
	  ///*******************************************************************************************************************************************************************
function getcreditcashout($id){
	$r2=mysql_query("SELECT
Sum(cash) AS credit
FROM `cashoutpayment`
WHERE
stand = '$id' and payment_type = 'Credit' ")or die(mysql_query());
 while($rw2 = mysql_fetch_array($r2, MYSQL_ASSOC)){
  $credit=$rw2['credit'];
    }
	if(mysql_num_rows($r2)==0){$credit=0;}
	return $credit;
	}
	function getcredit($id){
	$r2=mysql_query("SELECT
Sum(payment.cash) AS credit
FROM `payment`
WHERE
payment.stand = '$id' and payment.payment_type = 'Credit' ")or die(mysql_query());
 while($rw2 = mysql_fetch_array($r2, MYSQL_ASSOC)){
  $credit=$rw2['credit'];
    }
	if(mysql_num_rows($r2)==0){$credit=0;}
	return $credit;
	}
	  ///*******************************************************************************************************************************************************************

	function GetBeforeVat($id){
	$r2=mysql_query("SELECT Sum(payment.cash) AS credit FROM `payment` WHERE payment.stand = '$id' and payment.d = 'Balance_Before_VAT' ")or die(mysql_query());
 while($rw2 = mysql_fetch_array($r2, MYSQL_ASSOC)){
  $credit=$rw2['credit'];
    }
	if(mysql_num_rows($r2)==0){$credit=0;}
	return $credit;
	}
	 ///*******************************************************************************************************************************************************************

function DepositSoFar($id){
	 $r = mysql_query("SELECT Sum(payment.cash) AS tot FROM payment where stand='$id' and d='Stand Deposit'")or die(mysql_query());
    while($rw = mysql_fetch_array($r, MYSQL_ASSOC)){
  return $rw['tot']; 
 }}

	function getdeposit($id){
	/*$r2=mysql_query("SELECT
Sum(payment.cash) AS credit
FROM `payment`
WHERE
payment.stand = '$id' and payment.payment_type = 'deposit' ")or die(mysql_query());*/
$r2=mysql_query("SELECT * FROM stand where id_stand='$id' ")or die(mysql_query());
 while($rw2 = mysql_fetch_array($r2, MYSQL_ASSOC)){
  $credit=$rw2['deposit'];
    }
	if(mysql_num_rows($r2)==0){$credit=0;}
	return $credit;
	}
		  ///*******************************************************************************************************************************************************************

  function debit($type,$mari){
		if($type!='Credit' and $type!='Deposit')	{
			return "";
			}else{
return $mari;}
		}
		  function GMdebit($type,$mari){
		if($type!='Debit' and $type!='Deposit')	{
			return "";
			}else{
return $mari;}
		}
			  ///*******************************************************************************************************************************************************************

		 function credit($type,$mari){
		if($type!='Debit' and $type!='Cashout')	{
			return "";
			}else{
return "($mari)";}
		}
			  ///*******************************************************************************************************************************************************************

		function months($date,$date2){
		 $start = strtotime($date);
$end = strtotime($date2);
$days_between = ceil(abs($start - $end) / 2635200);
return $days_between;
		}
			  ///*******************************************************************************************************************************************************************

function payment($cash,$last){
		 $date = date('m/d/Y');
		 $start = strtotime($last);
$end = strtotime($date);
$months = ceil(abs($start - $end) / 2592000);
	}
		  ///*******************************************************************************************************************************************************************
  function pinda($cat,$std){
	  	global $date;   
mysql_query("INSERT INTO staff(user,catergory,mark,stage,term,date)
VALUES
('$_SESSION[username]','$cat','$std','1','$_SESSION[term]','$date')") or die (mysql_error());
	  return true;
	  }
	  ///*******************************************************************************************************************************************************************

		 function base($data) {
  return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
}
	  ///*******************************************************************************************************************************************************************

  /* numeric, decimal passes */
function number($variable) {
	return is_numeric($variable);
}
	  ///*******************************************************************************************************************************************************************

/* digits only, no dots */
function wholenumber($element) {
	return !preg_match ("/[^0-9]/", $element);
}

  function ifexisits($table,$column,$value){
	  	$db = getConnection();
	  $rs1 = mysql_query("select * from $table where $column = '$value'");
 $rw = mysql_num_rows($rs1);
 return $rw;
	  }
  	  ///*******************************************************************************************************************************************************************

  function days($date,$date2){
		 $start = strtotime($date);
$end = strtotime($date2);

$days_between = ceil(abs($start - $end) / 86400);
return $days_between;
		}
		
			  ///*******************************************************************************************************************************************************************

		function deleteRecords($table, $field, $value){
			$db = getConnection();
			$sql = "DELETE FROM $table WHERE $field = '$value'";
			//echo $sql;
			mysql_query($sql)or die (mysql_error());
			
		}
			  ///*******************************************************************************************************************************************************************

		function updateRecords($table, $field, $value){
			$db = getConnection();
			$sql = "update set $table WHERE $field = '$value'";
			//echo $sql;
			mysql_query($sql)or die (mysql_error());
			
		}
		//deleteRecords('final','id',2)
			  ///*******************************************************************************************************************************************************************

		function msg($msg){
			?>
				<script language="javascript">
					alert('<?php echo $msg;?>');
				</script>
			<?php
		}
	  ///*******************************************************************************************************************************************************************
		function link1($link){
			?>
				<script language="javascript">
					location = '<?php echo $link;?>';
				</script>
			<?php
		}
	  ///*******************************************************************************************************************************************************************

		function clean($str) {
                            $str = @trim($str);
                            if (get_magic_quotes_gpc()) {
                                $str = stripslashes($str);
                            }
							$db = getConnection();
                            $new=mysql_real_escape_string($str);
                  			$remove[] = "'";
							$remove[] = '"';
							//$remove[] = "-"; // just as another example
							$new = str_replace($remove, "", $new);
							$new=str_replace(',', '.', $new);
							return $new;
							}
					 ///*******************************************************************************************************************************************************************
	  ///*******************************************************************************************************************************************************************
	  ///*******************************************************FUNCTIONS PAGE**********************************************************************************************
	  ///*******************************************************ERNEST MUROIWA**********************************************************************************************
	  ///************************************************************GM58***************************************************************************************************
	  ///*******************************************************************************************************************************************************************
	  ///*******************************************************************************************************************************************************************
	  ///*******************************************************************************************************************************************************************	
		
		
		?>
