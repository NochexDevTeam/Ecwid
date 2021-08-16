<?php 

require_once 'config.php'; 		

// -------------------------------------------------------------------------
// -------------------------------------------------------------------------
// Collect the required variables from ecwid that need to be sent to Nochex.
// -------------------------------------------------------------------------
// -------------------------------------------------------------------------
// Request the invoice_number and store it as a payment reference which can be used for the order_id
$payment_reference = $_REQUEST['x_invoice_num'];
// Request the total amount of the order, and stores the total amount for the order
$amount = $_REQUEST['x_amount'];
//
// Request the description about the order, and stores it as the description for the order.
//


$raw = urldecode(file_get_contents("php://input"));
$raw = str_replace('x_line_item', 'x_line_item[]', $raw);
parse_str($raw, $postraw);

//XML collection / Product details check
if($product_Collection == 'yes' || $product_Collection == 'Yes'){

$getDetInfo = $_REQUEST['x_description'];

$itemCollection = "<items>";

foreach ($postraw['x_line_item'] as $details){
 
	$singleProduct = explode("<|>", $details);

	$pName = $singleProduct[0];
	$pId = $singleProduct[1];
	$pDescription = $singleProduct[2];
	$pQuantity = $singleProduct[3];
	$pAmount = $singleProduct[4];
	
	// Product Details attached to the end of description field
	$itemCollection .= "<item><id>".$pId."</id><name>" . $pName . "</name><description>". $pDescription ."</description><quantity>". $pQuantity  . "</quantity><price>" .  money_format('%n', $pAmount) . "</price></item>";
}

$itemCollection .= "</items>";
$getDetInfo = $payment_reference;
}else{
$getDetInfo = "";

$delimiter = array(" ",",",".","'","\"","|","\\","/",";",":", "<", ">");

foreach ($postraw['x_line_item'] as $details){

	$singleProduct = explode("<|>", $details);

	$pName = $singleProduct[0];
	$pId = $singleProduct[1];
	$pDescription = $singleProduct[2];
	$pQuantity = $singleProduct[3];
	$pAmount = $singleProduct[4];
	
	// Product Details attached to the end of description field
	$getDetInfo .= "Name: " . $pName . ", ID: " . $pId . ", Description: " . $pDescription . ", Quantity: " . $pQuantity . ", Amount: " . $pAmount . ", ";

}

$getDetInfo = substr($getDetInfo, 0, -2);
}

$first_name = $_REQUEST['x_first_name']; // Request the first name from the billing details.
$last_name = $_REQUEST['x_last_name']; // Request the last name from the billing details.

$description = " " . $_REQUEST['x_description']; // Attached if going to the current payment page secure.nochex.com . ", Product Details:" . $getDetInfo; . '' . $prodItems; 
//
//
$del_first_name = $_REQUEST['x_ship_to_first_name']; // Request the first name from the shipping details.
$del_last_name = $_REQUEST['x_ship_to_last_name']; // Request the last name from the shipping details.
//
// 
$billing_fullname = $first_name . ", " . $last_name; // Gets the First and Last name of the customer and stores them as one variable billing_fullname
//
$address = $_REQUEST['x_address'];
$city = $_REQUEST['x_city'];
$state = $_REQUEST['x_state'];
$country = $_REQUEST['x_country'];
// Request the address of the billing details, and stored as a variable.
$billing_address = $address . ", " . $state. ", " . $country;
$billing_city = $city;
$billing_postcode = $_REQUEST['x_zip']; // Request the Postcode of the address and stored as a variable.
//
// Request the phone number and stores as a phone number variable.
$customer_phone_number = $_REQUEST['x_phone'];
//
// Request an email address and stores as a email variable.
$email_address = $_REQUEST['x_email'];
//
//// Request the delivery address and stored as a variable
$delivery_fullname = $del_first_name . ", " . $del_last_name; 
$delivery_address = $_REQUEST['x_ship_to_address'];
$delivery_city = $_REQUEST['x_ship_to_city'];
$delivery_postcode = $_REQUEST['x_ship_to_zip']; // Request the Postcode of the address and stored as a variable.

// Hide Billing Feature check 
if($hideBillingDetails == 'yes' || $hideBillingDetails == 'Yes'){

$hide_billing = 'true';

}else{

$hide_billing = '';

}

//Test Transaction / mode check
if($testMode == 'yes' || $testMode == 'Yes'){
$testTransac = 100;
}else{
$testTransac = '';
}


// -------------------------------------------------------------------------
// -------------------------------------------------------------------------
// Nochex Payment Form - Replaces the hyperlink that was used to post data to the Nochex payment page.
?>

<form action="https://secure.nochex.com/default.aspx" method="post" id="nochex_form" class="hidden">
	<input type='hidden' name='merchant_id' value="<?php echo $merchantId; ?>" />	
	<input type='hidden' name='amount' value="<?php echo $amount; ?>" />
	<input type='hidden' name='description' value="<?php echo $getDetInfo; ?>" />
	<input type='hidden' name='xml_item_collection' value="<?php echo $itemCollection; ?>" />
	<input type='hidden' name='hide_billing_details' value="<?php echo $hide_billing; ?>" />
	<input type='hidden' name='billing_fullname' value="<?php echo $billing_fullname; ?>" />
	<input type='hidden' name='billing_address' value="<?php echo $billing_address; ?>" />
	<input type='hidden' name='billing_city' value="<?php echo $billing_city; ?>" />
	<input type='hidden' name='billing_postcode' value="<?php echo $billing_postcode; ?>" />
	<input type='hidden' name='delivery_fullname' value="<?php echo $delivery_fullname; ?>" />
	<input type='hidden' name='delivery_address' value="<?php echo $delivery_address; ?>" />
	<input type='hidden' name='delivery_city' value="<?php echo $delivery_city; ?>" />
	<input type='hidden' name='delivery_postcode' value="<?php echo $delivery_postcode; ?>" />
    <input type='hidden' name='customer_phone_number' value="<?php echo $customer_phone_number; ?>" />
	<input type='hidden' name='email_address' value="<?php echo $email_address; ?>" />
	<input type='hidden' name='order_id' value="<?php echo $payment_reference; ?>" /> 
	
    <input type='hidden' name='success_url' value="<?php echo $success_url . "?order_id=$payment_reference"; ?>"/>
	<input type='hidden' name='test_success_url' value="<?php echo $success_url . "?order_id=$payment_reference"; ?>"/>
	<input type='hidden' name='callback_url' value="<?php echo $return_link_url . "?order_id=$payment_reference"; ?>"/>
	<input type='hidden' name='test_transaction' value="<?php echo $testTransac; ?>" />
	
	<img src="https://www.nochex.com/logobase-secure-images/logobase-banners/clear.png" alt='card logos' height='80px;' /><br/>
	<style>
	.paybutton{
	padding: 5px;
	text-align:center;
    background: #08c;
    width: 12%;
    color: #fff;
    font-weight: bold;
	}
	.paybutton a{
	color:#fff;
	}
	</style>
	
	
	<div style="padding:10px">
	
	<?php if ($hide_billing == "true"){ ?>
	
	<div  style="border:1px solid #000; padding:10px;width: 40%;">
	Please check your billing address details match the details on your card that you are going to use.<br/><br/>
	</div>
	
	<?php } ?>
	<br/>
	
	<input value="Pay Using Nochex" onclick="verifyfields()" class="paybutton" type="submit" />
	</div>
</form>


<?php


?>


