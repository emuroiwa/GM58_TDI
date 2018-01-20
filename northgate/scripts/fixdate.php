<?php 

	mysql_connect('localhost','root','');
mysql_select_db('gm58');
  $lastdate = mysql_query("SELECT
	ROUND((cash + deposit) / instalments) AS a,
	deposit,
	cash,
	cash + deposit AS b,
	datestatus,
	DATE_ADD(
		datestatus,
		INTERVAL ROUND((cash) / instalments) MONTH
	) AS c,
	DATE_FORMAT(DATE_ADD(
		datestatus,
		INTERVAL ROUND((cash) / instalments) MONTH
	)
,'%M-%Y') AS d,
	number,
	stand
FROM
	`payment`,
	stand
WHERE
	stand = id_stand
AND payment_type = 'credit'
GROUP BY
	stand")or die(mysql_query());
   
 while($rwdate = mysql_fetch_array($lastdate, MYSQL_ASSOC)){


$sql="update `payment`  set value_date='$rwdate[c]',month='$rwdate[d]' where stand='$rwdate[stand]' and payment_type='credit' ";
mysql_query($sql);
  }
//echo mysql_affected_rows($rwdate);

?>