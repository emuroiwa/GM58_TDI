
<div class="navbar navbar-fixed-top navbar-inverse">
    <div class="navbar-inner">
        <div class="container">

            <!-- .btn-navbar is used as the toggle for collapsed navbar content -->
            <a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </a>

            <!-- Be sure to leave the brand out there if you want it shown -->

            <!-- Everything you want hidden at 940px or less, place within here -->



            <div class="nav-collapse collapse">
                <!-- .nav, .navbar-search, .navbar-form, etc -->

                <ul class="nav">
                    <li><a href="index.php"><i class="icon-home icon-large"></i>&nbsp;Home</a></li>
                    <?php if($_SESSION['access']==1){?>
   <!--         <li>
		<a href="./index.php?page=all_pay2.php"><i class="icon-money icon-large"></i>&nbsp;Stand Payment</a>
	</li>-->	
             
                      <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                           <i class="icon-money icon-large"></i>&nbsp;Stand Payment
                            <b class="caret"></b>
                        </a>
                        <ul class="dropdown-menu">
  <li><a href="index.php?page=all_pay2.php">Stand Payment</a></li>
  <li><a href="index.php?page=all_pay21.php">VAT </a></li>



                        </ul>
                    </li>
             
                   <!-- <li><a href="file.php"><i class="icon-folder-open icon-large"></i>&nbsp;File</a></li>-->
                
                    
                    <!---->
                       <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                            <i class="icon-folder-close-alt icon-large"> </i>&nbsp;Ledger
                            <b class="caret"></b>
                        </a>
                        <ul class="dropdown-menu">
                        
                     <li><a href="index.php?page=legdertable.php">Create Ledger</a></li>
                     <li><a href="index.php?page=cashoutlegdertable.php">Create CashOut Ledger</a></li>
 <!--  <li><a href="index.php?page=searchstatement.php" >Comprehensive Ledger Stands In progress</a></li>
    <li><a href="statement_overall_paid.php" target="_blank">Comprehensive Ledger Paid Stands</a></li> 
     <li><a href="statement_reserved.php" target="_blank">Comprehensive reserved Stands</a></li>
-->


                        </ul>
                    </li>
                    <!---->       <!---->
                  <!--     <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                            <i class="icon-folder-close-alt icon-large"> </i>&nbsp;SMS
                            <b class="caret"></b>
                        </a>
                        <ul class="dropdown-menu">
             <li>
		<a href="./index.php?page=bsms.php">Bulk SMS</a>
	</li><li><a href="index.php?page=sms_date.php" class="active">Specific date</a></li>
    <li><a href="index.php?page=smscash.php" class="active">Settings</a></li>
    <li><a href="http://193.105.74.59/api/command?username=TDInvestment&password=tS8ff1Cg1&cmd=CREDITS" class="active" target="_blank">Enquire sms Balance</a></li>



                        </ul>
                    </li>-->
                    <!---->
                    <!---->       <!---->
                       <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                           <i class="icon-group icon-large"></i>&nbsp;Clients
                            <b class="caret"></b>
                        </a>
                        <ul class="dropdown-menu">
  <li><a href="index.php?page=add_client_to_stand.php">Add Second party owner</a></li>
  <li><a href="index.php?page=clients.php">View Clients</a></li>



                        </ul>
                    </li>
                    <!---->   <!---->
                       <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                            <i class="icon-folder-close-alt icon-large"> </i>&nbsp;Stands
                            <b class="caret"></b>
                        </a>
                        <ul class="dropdown-menu">
<li>
		<a href="./index.php?page=stand.php">NEW Stands</a>
	</li>       <li><a href="index.php?page=reserve.php">Reserved stands</a></li><li><a href="index.php?page=ProcessReserved.php">Process Reserved stands</a></li>      <li><a href="index.php?page=location.php">Add Location</a></li> 



                        </ul>
                           <li><a href="index.php?page=amendments.php"><i class="icon-folder-close-alt icon-large"></i>&nbsp;Amendments</a></li> 
                    </li>
                    <!---->  <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                            <i class="icon-folder-close-alt icon-large"> </i>&nbsp;Reports
                            <b class="caret"></b>
                        </a>
                        <ul class="dropdown-menu">
                          <li><a href="reports">GM58 Reports</a></li>
  <li><a href="index.php?page=ReCalculate.php">Payment Reports</a></li>

                             </ul>
                    </li>  <!---->
                       <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                            <i class="icon-folder-close-alt icon-large"> </i>&nbsp;Settings
                            <b class="caret"></b>
                        </a>
                        <ul class="dropdown-menu">
                          <li><a href="index.php?page=registration.php">Add Users</a></li>
  <li><a href="index.php?page=GetUsers.php">Edit Users</a></li>
<li><a href="index.php?page=AddEmails.php">Add Emails</a></li>
<li><a href="index.php?page=GetEmails.php">Remove Emails</a></li>
<li><a href="index.php?page=backup.php">Backup</a></li>
                             </ul>
                    </li>
          
                    <?php }if($_SESSION['access']==2){?>
                    
  <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                           <i class="icon-money icon-large"></i>&nbsp;Stand Payment
                            <b class="caret"></b>
                        </a>
                        <ul class="dropdown-menu">
  <li><a href="index.php?page=all_pay2.php">Stand Payment</a></li>
 



                        </ul>
                    </li>
                   <!-- <li><a href="file.php"><i class="icon-folder-open icon-large"></i>&nbsp;File</a></li>-->
                
                    
                    <!---->
                       <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                            <i class="icon-folder-close-alt icon-large"> </i>&nbsp;Ledger
                            <b class="caret"></b>
                        </a>
                        <ul class="dropdown-menu">
                        
                     <li><a href="index.php?page=legdertable.php">Create Ledger</a></li>
                     <li><a href="index.php?page=cashoutlegdertable.php">Create CashOut Ledger</a></li>
 <!--  <li><a href="index.php?page=searchstatement.php" >Comprehensive Ledger Stands In progress</a></li>
    <li><a href="statement_overall_paid.php" target="_blank">Comprehensive Ledger Paid Stands</a></li> 
     <li><a href="statement_reserved.php" target="_blank">Comprehensive reserved Stands</a></li>
-->


                        </ul>
                    </li>
                    <!---->       <!---->
                  <!--     <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                            <i class="icon-folder-close-alt icon-large"> </i>&nbsp;SMS
                            <b class="caret"></b>
                        </a>
                        <ul class="dropdown-menu">
             <li>
		<a href="./index.php?page=bsms.php">Bulk SMS</a>
	</li><li><a href="index.php?page=sms_date.php" class="active">Specific date</a></li>
    <li><a href="index.php?page=smscash.php" class="active">Settings</a></li>
    <li><a href="http://193.105.74.59/api/command?username=TDInvestment&password=tS8ff1Cg1&cmd=CREDITS" class="active" target="_blank">Enquire sms Balance</a></li>



                        </ul>
                    </li>-->
                    <!---->
                    <!---->       <!---->
                       <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                           <i class="icon-group icon-large"></i>&nbsp;Clients
                            <b class="caret"></b>
                        </a>
                        <ul class="dropdown-menu">
  <li><a href="index.php?page=add_client_to_stand.php">Add Second party owner</a></li>
  <li><a href="index.php?page=clients.php">View Clients</a></li>



                        </ul>
                    </li>
                    <!---->   <!---->
                       <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                            <i class="icon-folder-close-alt icon-large"> </i>&nbsp;Stands
                            <b class="caret"></b>
                        </a>
                        <ul class="dropdown-menu">
<li>
		<a href="./index.php?page=stand.php">NEW Stands</a>
	</li>       <li><a href="index.php?page=reserve.php">Reserved stands</a></li>     <li><a href="index.php?page=location.php">Add Location</a></li> 



                        </ul>
                   
                    </li>
                    <?php }
					if($_SESSION['access']==3){?>
                    <!----> <li><a href="reports"><i class="icon-folder-close-alt icon-large"></i>Reports</a></li>   <!---->
 <?php }?>
              <!--      <li><a href="index.php?page=changepass.php" class="active">Help</a></li>-->

                    <li><a  href="#myModal" role="button"  data-toggle="modal"><i class="icon-signout icon-large"></i>&nbsp;Logout</a></li>
                </ul>
            </div>

        </div>
    </div>
</div>

<div class="hero-unit-header">
    <div class="container">
        <div class="row-fluid">
            <div class="span12">


                <div class="row-fluid">
                    <div class="span6">
                       
                            <?php $user_query=mysql_query("select * from users where username='$_SESSION[username]'")or die(mysql_error());
                            $row=  mysql_fetch_array($user_query);
                            ?>
                            <a href="index.php?page=changepass.php" class="btn btn-info">Welcome:<i class="icon-user icon-large"></i>&nbsp;<?php echo $row['name']." ".$row['surname']; ?></a> <br>
 <!-- <img src="images/head0.png">-->
                    </div>
                    <div class="span6">

                        <div class="pull-right">
                          <i class="icon-calendar icon-large"></i><?php
                            $Today = date('y:m:d');
                            $new = date('l, F d, Y', strtotime($Today));
                            echo "TEST";
                            ?>
                           
                            
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>