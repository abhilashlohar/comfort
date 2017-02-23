<?php 
require_once("function.php");
require_once ("classes/databaseclasses/DataBaseConnect.php");
require_once("auth.php");
$login_id=$_SESSION['login_user'];
 if(isset($_POST['supplier_payment_reg']))
{
	$query ="INSERT INTO `payment` (`receipt_type_type_id`,`current_date`,
    `name`, `date`,`amount` , `narration` ,`total`
    ) values ('".$_POST['receipt_type_type_id']."',
    '".date('Y-m-d')."','".$_POST['name']."'
    ,'".DateExact($_POST['date'])."'
    ,'".$_POST['amount']."','".$_POST['narration']."',0)";             
	$data_base_connect_object=new DataBaseConnect();	
	$data_base_connect_object->execute_query_update($query, "supplier_payment_reg");
	
	$result_max=$data_base_connect_object->execute_query_return("select max(`id`) from `payment`");
	$row=mysql_fetch_array($result_max);
	$max_payment_id=$row[0];
	
	if($_POST['receipt_type_type_id']=="Cash")
	{
			$data_base_connect_object->execute_query_update("insert into `ledger`(`ledger_type`,`name`,`credit`,`debit`,`date`,`payment_id`,`type`,`type_id`,`narration`)
		VALUES('".$_POST['ledger_type']."','".$_POST['name']."','0','".$_POST['amount']."','".DateExact($_POST['date'])."','".$max_payment_id."','Payment','".$max_payment_id."','".$_POST['narration']."')
		", "ledger_insert");
			
		$data_base_connect_object->execute_query_update("insert into `ledger`(`ledger_type`,`name`,`cust_supp_name`,`credit`,`debit`,`date`,`payment_id`,`type`,`type_id`,`narration`)
		VALUES('Ledger','Cash Account','".$_POST['name']."','".$_POST['amount']."','0','".DateExact($_POST['date'])."','".$max_payment_id."','Payment','".$max_payment_id."','".$_POST['narration']."')
		", "ledger_insert");
	}
	else if($_POST['receipt_type_type_id']=="Bank")
	{
		$data_base_connect_object->execute_query_update("insert into `ledger`(`ledger_type`,`name`,`credit`,`debit`,`date`,`payment_id`,`type`,`type_id`,`narration`)
		VALUES('".$_POST['ledger_type']."','".$_POST['name']."','0','".$_POST['amount']."','".DateExact($_POST['date'])."','".$max_payment_id."','Payment','".$max_payment_id."','".$_POST['narration']."')
		", "ledger_insert");
		$data_base_connect_object->execute_query_update("insert into `ledger`(`ledger_type`,`name`,`credit`,`debit`,`date`,`bankname`,`branch`,`chequenumber`,`chequedate`,`drawnbranch`,`payment_id`,`type`,`type_id`,`narration`)
		VALUES('Bank','".$_POST['bank_reg_bank_id']."','".$_POST['amount']."','0','".DateExact($_POST['date'])."','".$_POST['bank_reg_bank_id']."','".$_POST['branch']."','".$_POST['chequenumber']."','".DateExact($_POST['chequedate'])."','".$_POST['drawnbranch']."','".$max_payment_id."','Payment','".$max_payment_id."','".$_POST['narration']."')
		", "ledger_insert");
	} 
    $data_base_connect_object->close_connection();
    @header("location:payment_menu.php?reg=done");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="utf-8" />
   <title>Comfort</title>
   <meta content="width=device-width, initial-scale=1.0" name="viewport" />
   <meta content="" name="description" />
   <meta content="" name="author" />
  <?php css(); ?>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<script type="text/javascript">

function deleteInvoice(id)
{
	var ajaxRequest;  // The variable that makes Ajax possible!
	try{
		// Opera 8.0+, Firefox, Safari
		ajaxRequest = new XMLHttpRequest();
	} catch (e){
		// Internet Explorer Browsers
		try{
			ajaxRequest = new ActiveXObject("Msxml2.XMLHTTP");
		} catch (e) {
			try{
				ajaxRequest = new ActiveXObject("Microsoft.XMLHTTP");
			} catch (e){
				// Something went wrong
				alert("Your browser broke!");
				return false;
			}
		}
	}
	// Create a function that will receive data sent from the server
	ajaxRequest.onreadystatechange = function(){
		if(ajaxRequest.readyState == 4){
			if(ajaxRequest.responseText=="completed")
			{
				location.reload();
			}
		}
	}
		ajaxRequest.open("GET", "get_teriff_rate.php?paymentdelete="+id, true);
		ajaxRequest.send(null);
}

function SetLedgerSession2(itemid)
{ 
	var ledger_list=document.getElementById(itemid);
	
	var ledger_type=ledger_list.options[ledger_list.selectedIndex].text;
	var ajaxRequest;  // The variable that makes Ajax possible!

	ajaxRequest=GetAjax();
	// Create a function that will receive data sent from the server
	ajaxRequest.onreadystatechange = function(){
		if(ajaxRequest.readyState == 4){
			document.getElementById('div_name').innerHTML=ajaxRequest.responseText;
		}
	}	
	ajaxRequest.open("GET", "receipt_ledger_session_set.php?ledger_type="+ledger_type, true);	
	ajaxRequest.send(null);

}

function HideShowRows()
{
	for(var i=1;i<=5;i++)
	{
	 	if (document.getElementById(i).style.display == 'none') {
         document.getElementById(i).style.display = '';
     	} else {
         document.getElementById(i).style.display = 'none';
     	}
	}
}

function deletePayment()
{

	var curr_id=document.view_form.current_id.value;
	var ajaxRequest;  // The variable that makes Ajax possible!
	ajaxRequest=GetAjax();
	// Create a function that will receive data sent from the server
	ajaxRequest.onreadystatechange = function(){
		if(ajaxRequest.readyState == 4){
			alert(ajaxRequest.responseText);
			getNext();
		}
	}
	ajaxRequest.open("GET", "get_duty_data.php?del_pay_id=" + curr_id, true);
	ajaxRequest.send(null); 

}

function cal_all()
{
	var one =document.calform.amount1.value;
	var two =document.calform.amount2.value;
	var three =document.calform.amount3.value;
	var four =document.calform.amount4.value;
	var five =document.calform.amount5.value;
	var amount_val = document.calform.amount_val.value;
	if((parseInt(one)+parseInt(two)+parseInt(three)+parseInt(four)+parseInt(five))==amount_val)
	{
		return true;
	}
	return false;
}

var msg;

function getPre()
{
	var curr_id=document.view_form.current_id.value;
//	alert(curr_id);
	msg="<";
	get_Result(curr_id);
}

function getNext()
{
	var curr_id=document.view_form.current_id.value;
	msg=">";
	get_Result(curr_id);
}

function GetAjax()
{
var ajaxRequest;  // The variable that makes Ajax possible!
	
	try{
		// Opera 8.0+, Firefox, Safari
		ajaxRequest = new XMLHttpRequest();
	} catch (e){
		// Internet Explorer Browsers
		try{
			ajaxRequest = new ActiveXObject("Msxml2.XMLHTTP");
		} catch (e) {
			try{
				ajaxRequest = new ActiveXObject("Microsoft.XMLHTTP");
			} catch (e){
				// Something went wrong
				alert("Your browser broke!");
				return false;
			}
		}
	}
	return ajaxRequest;
}

function GetBranches()
{
	var bank_list=document.add_form.bank_reg_bank_id;
	var bank_name=bank_list.options[bank_list.selectedIndex].text;
	var ajaxRequest;  // The variable that makes Ajax possible!
	ajaxRequest=GetAjax();
	// Create a function that will receive data sent from the server
	ajaxRequest.onreadystatechange = function(){
		if(ajaxRequest.readyState == 4){
			document.getElementById('branch_name_div').innerHTML=ajaxRequest.responseText;
		}
	}
	
	ajaxRequest.open("GET", "receipt_ledger_session_set.php?bank_name="+bank_name, true);
	ajaxRequest.send(null); 
}

function SetLedgerSession()
{
	var ledger_list=document.add_form.ledger_type;
	var ledger_type=ledger_list.options[ledger_list.selectedIndex].text;
	var ajaxRequest;  // The variable that makes Ajax possible!
	ajaxRequest=GetAjax();
	// Create a function that will receive data sent from the server
	ajaxRequest.onreadystatechange = function(){
		if(ajaxRequest.readyState == 4){
			document.getElementById('option_name').innerHTML=ajaxRequest.responseText;
		}
	}
	
	ajaxRequest.open("GET", "receipt_ledger_session_set.php?ledger_type="+ledger_type, true);
	ajaxRequest.send(null); 
}

function get_Result(curr_id)
{
	var ajaxRequest;  // The variable that makes Ajax possible!
	ajaxRequest=GetAjax();
	// Create a function that will receive data sent from the server
	ajaxRequest.onreadystatechange = function(){
		if(ajaxRequest.readyState == 4){
			var string = ajaxRequest.responseText;
			var myarray = new Array();
	        myarray = string.split(",");
		//	alert("Hello");
	        document.getElementById('first_data').innerHTML=myarray[0];
	        document.getElementById('second_data').innerHTML=myarray[1];
	        document.getElementById('third_data').innerHTML=myarray[2];
	        document.getElementById('four_data').innerHTML=myarray[3];
	        document.getElementById('five_data').innerHTML=myarray[4];
	        document.getElementById('six_data').innerHTML=myarray[5];
	    //    document.getElementById('seven_data').innerHTML=myarray[6];
			document.getElementById('current_id').value=myarray[6];
			document.getElementById('seven_data').innerHTML=myarray[7];
			
		}
	}
	ajaxRequest.open("GET", "get_duty_data.php?id=" + curr_id+"&pay_fetch=true&what="+msg
					 +"&ledger_type="+document.getElementById('ledger_type').value+"&ledger_name="+document.getElementById('ledger_name').value+"&date_from="+document.getElementById('date_from').value+"&date_to="+document.getElementById('date_to').value
					 , true);
	ajaxRequest.send(null); 	
}

function cal_grand_total()
{
	var final_total = document.getElementById('total').value;
	var discount = document.getElementById('discount').value;
	var tax = document.getElementById('tax').value;
	document.getElementById("grand_total").value=(final_total-discount)+(final_total-discount)*tax/100;
}
</script>
</head>
<!-- END HEAD -->
<!-- BEGIN BODY -->
<body class="fixed-top">
	<!-- BEGIN HEADER -->
	<?php navi_bar(); ?>
   <div class="page-container row-fluid">
      <!-- END SIDEBAR -->
      <?php  navi_menu(); ?>
      <!-- BEGIN PAGE -->  
      <div class="page-content">
         <div class="container-fluid">
     <?php menu(); ?>
     <form method="post" name="add_form">
<div>    
<?php
	$data_base_connect_object =new DataBaseConnect(); 
	$my_query="select * from sub_module_assign where `login_id` = '".$login_id."' && `submodule_id` = '21' ";
	$result_mydata = $data_base_connect_object->execute_query_return($my_query);
	$row_right=mysql_fetch_array($result_mydata);
	$add_status = $row_right['add'];
	$edit_status = $row_right['edit'];
	$delete_status = $row_right['delete'];
	$view_status = $row_right['view'];
	if($add_status==1)
	{
		?>                  
<a href="payment_menu.php" class="btn red"><i class="icon-ok"></i> Add</a>
<?php
	}
	if($view_status==1)
	{
		?>
<a href="payment_menu_search.php" class="btn blue"><i class="icon-edit"></i> Search</a>
<?php
	}
	?>
</div> 
<br />
<?php
	if($add_status==1)
	{
		?>
                    <div class="portlet box yellow">
                    <div class="portlet-title">
                    <h4><i class="icon-credit-card"></i>Enter Payment Information</h4>
                    </div>
                    <div class="portlet-body form">
                     <table width="100%">
		        <tr><td>  Payment Number</td><td>
		        <?php 
              		$mydatabase = new DataBaseConnect();
					$result= $mydatabase->execute_query_return("select MAX(`id`) from `payment`");
					$row=mysql_fetch_array($result);
					$fetch =  $row[0]+1;	
              	?>
                <input type="text" value="<?php echo $fetch; ?>" class="m-wrap medium" readonly>
		        </td></tr>   
              	<tr><td>Payment Type</td><td>
              	<select name="receipt_type_type_id" onchange="HideShowRows()"  class="m-wrap medium">
              	<?php 
						$result= $mydatabase->execute_query_return("select name from receipt_type");
						while($row=mysql_fetch_array($result))
						{
							echo "<option value=\"".$row['name']."\">".$row['name']."</option>";
						}
              	?>
              	</select>
              	</td></tr>
              	<tr><td> Ledger Type : </td><td>
              	<select name="ledger_type"  onchange="SetLedgerSession()"  class="m-wrap medium" >
              	<option>--Select--</option>
              	<?php 
              	$result= $mydatabase->execute_query_return("select distinct `ledger_type` from `ledger`");
						while($row=mysql_fetch_array($result))
						{
							echo "<option value=\"".$row['ledger_type']."\">".$row['ledger_type']."</option>";
						}
					
				?>
              	</select>
              	</td></tr>
              	<tr><td> Name : </td><td>
              		<div id="option_name"></div>
              	</td></tr>
				<tr><td>Date:  </td><td>
					<input type="text" name="date" id="dp1" class="m-wrap medium"/>
				</td></tr>
				<tr id="1" style="display: none;" ><td> Bank Name: </td><td>
					<select name="bank_reg_bank_id" onchange="GetBranches()"  class="m-wrap medium">
					<option>-Select-</option>
              	<?php 
						$result= $mydatabase->execute_query_return("select name from bank_reg");
						while($row=mysql_fetch_array($result))
						{
							echo "<option value=\"".$row['name']."\">".$row['name']."</option>";
						}
						$mydatabase->close_connection();
              	?>
              	</select>
				</td></tr>
				<tr id="2" style="display: none;"><td> Branch : </td><td>
				<div id="branch_name_div"></div>
<!--				<input type="text" name="amount" /> -->
				
				</td></tr>
				<tr id="3" style="display: none;"><td> Cheque Number : </td><td><input type="text"  class="m-wrap medium" name="chequenumber" /> </td></tr>
				<tr id="4" style="display: none;"><td> Cheque Date : </td><td><input type="text"  class="m-wrap medium" name="chequedate" /> </td></tr>
				<tr id="5" style="display: none;"><td> Drawn Branch : </td><td><input type="text"  class="m-wrap medium" name="drawnbranch" /> </td></tr>
				<tr><td> Amount: </td><td><input type="text" name="amount"  class="m-wrap medium" /> </td></tr>
				<tr><td> Narration: </td><td><input type="text" name="narration"  class="m-wrap medium" /> </td></tr>
				<tr><td></td><td>&nbsp;</td></tr>
                </table>
                  <div class="form-actions">
                  <button type="submit" value="Add" style="margin-left:30%" class="btn green" name="supplier_payment_reg"/><i class="icon-ok"></i> Submit</button>
                     </div>
                    
                     </div>
                     </div> 
                       <?php
	}
	?>
 		
        </form>
        </div>
        </div>
        </div>
   <!-- BEGIN FOOTER -->
   
   <div class="footer">
     <?php footer();?>
   </div>
 <?php js(); ?> 
 <?php datepicker(); ?>
   <!-- END JAVASCRIPTS -->   
</body>
<!-- END BODY -->
</html>