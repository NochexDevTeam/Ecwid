<?php
			
		$payment_alert = 'Enter your email address'; // Replace 'Enter your email address' with the email address you want payment confirmation sent to. e.g. 'test@test.com';
		$success_url = 'Enter the link to your success_url.php file'; // Replace 'Enter the link to your success_url.php file' to the success_url.php file which is located on your webserver, e.g. 'http://somewhere.com/somewhere/success_url.php';
		$return_link_url = 'Enter the link to your postpayment.php file'; // Replace the 'Enter the link to your postpayment.php file' to the postpayment.php file which is located on your webserver, e.g. 'http://somewhere.com/somewhere/postpayment.php';
		$merchantId = 'Enter your nochex merchantID'; // Change 'Enter your nochex merchantID' to your Merchant ID, e.g. 'test@test.com';
		
		$product_Collection = 'No'; // To enable this feature change the value from 'No' to 'Yes', If you would like product details to be displayed in a structured format.  
		$hideBillingDetails = 'No'; // 	To enable this feature change the value from 'No' to 'Yes', If you would like to hide billing details.
		$testMode = 'No'; // To enable this feature change the value from 'No' to 'Yes', If you would like to do any test transactions (leave as NO if you want to make live transactions).
		
		$payWayBaseUrl = 'https://secure.nochex.com';
			
		if (!function_exists('getallheaders'))
		{
			function getallheaders()
			{
			   foreach ($_SERVER as $name => $value)
			   {
				   if (substr($name, 0, 5) == 'HTTP_')
				   {
					   $headers[strtolower(str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5))))))] = $value;
					   
				   }
			   }
			   return $headers;
			}
		}
			
?>
