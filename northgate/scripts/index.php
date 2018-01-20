<?php error_reporting(0); 
include('header.php'); ?>
<?php
include '../opendb.php';
include '../functions.php';

include ('aut.php');

?>
<body>

    <div class="row-fluid">
        <div class="span12">

            <?php include('navbar.php'); ?>

            <div class="container">

                <div class="row-fluid">

                    <div class="span12">
                        <div class="hero-unit-3">

                            <table cellpadding="0" cellspacing="0" border="0" class="table table-striped table-bordered" id="example">
                            <?php
							//echo $date;
$pg = @$_REQUEST['page'];
//echo $_SESSION['access'];
if($pg != "" && file_exists(dirname(__FILE__)."/".$pg)){
require(dirname(__FILE__)."/".$pg);
}elseif(!file_exists(dirname(__FILE__)."/".$pg))
include_once(dirname(__FILE__)."/404.php");
else{
include_once("all_pay2.php");
}
?>   
                             
                            </table>
                        </div>

                    </div>
                </div>
                <?php include('footer.php'); ?>
            </div>
        </div>
    </div>





</body>
</html>


