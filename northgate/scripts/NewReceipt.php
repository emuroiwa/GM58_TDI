
   <?php
      include '../opendb.php';

   include '../functions.php';


    $html=" <style>
    .invoice-box{
        max-width:800px;
        margin:auto;
        padding:30px;
        border:1px solid #eee;
        box-shadow:0 0 10px rgba(0, 0, 0, .15);
        font-size:16px;
        line-height:24px;
        font-family:'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif;
        color:#555;
    }
    
    .invoice-box table{
        width:100%;
        line-height:inherit;
        text-align:left;
    }
    
    .invoice-box table td{
        padding:5px;
        vertical-align:top;
    }
    
    .invoice-box table tr td:nth-child(2){
        text-align:right;
    }
    
    .invoice-box table tr.top table td{
        padding-bottom:20px;
    }
    
    .invoice-box table tr.top table td.title{
        font-size:45px;
        line-height:45px;
        color:#333;
    }
    
    .invoice-box table tr.information table td{
        padding-bottom:40px;
    }
    
    .invoice-box table tr.heading td{
        background:#eee;
        border-bottom:1px solid #ddd;
        font-weight:bold;
    }
    
    .invoice-box table tr.details td{
        padding-bottom:20px;
    }
    
    .invoice-box table tr.item td{
        border-bottom:1px solid #eee;
    }
    
    .invoice-box table tr.item.last td{
        border-bottom:none;
    }
    
    .invoice-box table tr.total td:nth-child(2){
        border-top:2px solid #eee;
        font-weight:bold;
    }
    
    @media only screen and (max-width: 600px) {
        .invoice-box table tr.top table td{
            width:100%;
            display:block;
            text-align:center;
        }
        
        .invoice-box table tr.information table td{
            width:100%;
            display:block;
            text-align:center;
        }
    }
    </style>";
   $data = GetCompanyDetails();
$name = $data[0];
$branch = $data[1];
$address = $data[2];
$contacts = $data[3];
$bankingdetails = $data[4];
$bankingdetails2 = $data[5];
$bankingdetails3 = $data[6];
$logo = $data[7];
$PaymentDetails="";
$PaymentAmount="";
$ClientName="";
$ClientAddress="";
$ClientEmail="";
   $html.=' <div class="invoice-box">
        <table cellpadding="0" cellspacing="0">
            <tr class="top">
                <td colspan="2">
                    <table>
                        <tr>
                            <td class="title">
                                <img src="'. $logo .'" style="width:100%; max-width:300px; width="90" height=80">
                            </td>
                            
                            <td>
                                Receipt #: '.$id.'<br>
                                Created: '. $new.'<br>
                                Created By: '. $_SESSION['name'].'
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            
            <tr class="information">
                <td colspan="2">
                    <table>
                        <tr>
                            <td>
                               '.$name .' '. $branch.'<br>
                               '.$address.'<br>
'.$contacts.'
                            </td>
                            
                            <td>
                              '.$ClientName.'<br>
                                '.$ClientAddress.'<br>
                               '.$ClientEmail.'
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            
            <tr class="heading">
                <td>
                    Payment Method
                </td>
                
                <td>
                    Check #
                </td>
            </tr>
            
           
            
            <tr class="heading">
                <td>
                    Item
                </td>
                
                <td>
                    Price
                </td>
            </tr>
            
            <tr class="item">
                <td>
              '.$PaymentDetails.'
                </td>
                
                <td>
                    '.$PaymentAmount.'
                </td>
            </tr>
            
            
            
            <tr class="total">
                <td></td>
                
                <td>
                   Total: '.$PaymentAmount.'
                </td>
            </tr>
        </table>
		<table cellpadding="0" cellspacing="0">
		 <tr class="heading">
                <td colspan="2">
                  <center>  Banking Details</center>
                </td>
                
                
            </tr>
		 <tr class="item">
                <td>
              '.$PaymentDetails.'
                </td>
                
                <td>
                    '.$PaymentAmount.'
                </td>
            </tr>
            
            
            
            <tr class="total">
                <td></td>
                
                <td>
                   Total: '.$PaymentAmount.'
                </td>
            </tr>
        </table>
    </div>';
	
	echo $html;	?>
