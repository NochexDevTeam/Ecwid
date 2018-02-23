<?php require_once 'config.php' ?>
<?php require_once 'ecwid-config.php' ?>
<?php
		
		$to = $merchantId;
			
		// Get the POST information from Nochex server
		$postvars = http_build_query($_POST);	
		$url = "https://www.nochex.com/apcnet/apc.aspx";
			
		// Curl code to post variables back
		$ch = curl_init(); // Initialise the curl tranfer
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_VERBOSE, true);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $postvars); // Set POST fields
		curl_setopt($ch, CURLOPT_HTTPHEADER, "Host: www.nochex.com");
		curl_setopt($ch, CURLOPT_POSTFIELDSIZE, 0); 
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_TIMEOUT, 60); // set connection time out variable - 60 seconds	
		//curl_setopt ($ch, CURLOPT_SSLVERSION, 6); // set openSSL version variable to CURL_SSLVERSION_TLSv1.2
		$output= curl_exec($ch); // Post back
		curl_close($ch);
			
		// If UNAUTHORISED was found in the response then it was unsuccessful and a debug message is displayed.
		if ($output=="AUTHORISED") {  // searches response to see if AUTHORISED is present if it isn’t a failure message is displayed.
				$msg = "APC was AUTHORISED.";  // Displays debug message 
				$x_response_code = "1"; // Requests the response code
				$x_response_reason_code = "1"; // Requests the reason response code 				
		} else { 
				$msg = "APC was not AUTHORISED.";  // Displays debug message
				$x_response_code = "0"; // Sets the reason code.
				$x_response_reason_code = "0"; // Sets the reason response code. 	
		}
			
		$x_trans_id= $_POST['transaction_id']; // Requests the transaction_id.
		$x_invoice_num = $_POST['order_id']; // Requests the order_id/invoice number.
		$x_amount = $_POST['amount']; // Requests the total amount of an order.
		$string = $hash_value.$x_login.$x_trans_id.$x_amount; // This collects the hash, login, transaction id and total amount values.
		$x_MD5_Hash = md5($string); // String variable stored as a hash value. //
			
		//--Mandatory fields from ecwid that need to be filled and sent to ecwid.--//
		$datatopost = array (
				"x_response_code" => $x_response_code, 
				"x_response_reason_code" => $x_response_reason_code,
				"x_trans_id" => $x_trans_id,
				"x_invoice_num" => $x_invoice_num,
				"x_amount" => $x_amount,
				"x_MD5_Hash" => $x_MD5_Hash);
		
		$url = $storeURL_ID;
		$ch = curl_init($url);
			
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $datatopost);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			
		$response = curl_exec($ch);
		$response_output=$response;
		curl_close($ch);
			
		$order_id = $_POST['order_id']; // Gets the $_Post, Order_ID
		$order_status = $msg; // Status of the order
			
		$file = "orders.xml"; // Get the xml file: orders.xml
		$fp = fopen($file, "rb") or die("cannot open file //"); // Open the xml file
		$str = fread($fp, filesize($file)); // Reads the data in the xml file
			
		$xml = new DOMDocument(); 
		$xml->formatOutput = true;
		$xml->preserveWhiteSpace = false;
		$xml->loadXML($str) or die("Error");
			
		$xpath = new DOMXpath($xml);
		$elements = $xpath->query("//orders/order[id=$order_id]"); // An xpath query to find any matches with order_ids.
			
		$order_matches = "0"; // order_matches with a value of '0' = no order_ids match.
			
		// for each loop, loops through all the order_ids to discover any matches.
		foreach($elements as $e) {
		// If there are any elements that match the current order_id, the value of order_matches = 1 There is a match.
		  if($e->nodeValue == $order_id){
			$order_matches = "1"; 
		  }
		}
		// If there is a match with the current order_id and order_ids currently stored match, then update the order as failed.
		if($order_matches == "1"){
		// Opens and Reads the xml file - orders.xml
		$file = "orders.xml";
		$fp = fopen($file, "rb") or die("cannot open file \\");
		$str = fread($fp, filesize($file));
			
		$xml = new DOMDocument();
		$xml->formatOutput = true;
		$xml->preserveWhiteSpace = false;
		$xml->loadXML($str) or die("Error");
			
		$xpath = new DOMXpath($xml);
		$elements = $xpath->query("//orders/order[id=$order_id]");
			
		foreach($elements->item(0)->childNodes as $child) {
			if($child->nodeName == "status"){
				 $child->nodeValue = $order_status; // Status of the order: updated.
			}
			if($child->nodeName == "response"){
				 $child->nodeValue = $response_output; // Response of the order: updated.
			}
		}
			$xml->save("orders.xml") or die("Error"); // Saves the updated order.
		}
		// If elements dont match, then add the new order to the xml file.
		if($order_matches == "0"){
			
		// Opens and loads the xml file - orders.xml
		$file = "orders.xml";
		$fp = fopen($file, "rb") or die("cannot open file ||");
		$str = fread($fp, filesize($file));
			
		$xml = new DOMDocument();
		$xml->formatOutput = true;
		$xml->preserveWhiteSpace = false;
		$xml->loadXML($str) or die("Error");
			
		// Get document elements
		$root   = $xml->documentElement; // gets the xml document
		$fnode  = $root; // gets the root of the xml
		$ori    = $fnode->childNodes->item(0); //Gets the top child node.
			
		// - This is the order_id node of order
		$id = $xml->createElement("id"); // Node Title
		$idText = $xml->createTextNode($order_id); // node data
		$id->appendChild($idText); 
			
		// - This is the order_status node of order
		$status = $xml->createElement("status"); // Node Title
		$statusText = $xml->createTextNode($order_status);// node data
		$status->appendChild($statusText);
			
		// - This is the order_response node of order
		$response = $xml->createElement("response"); // Node Title
		$responseText = $xml->createTextNode($response_output);// node data
		$response->appendChild($responseText);
			
		$Order   = $xml->createElement("order"); // Node Title
		$Order->appendChild($id); // Retrieves the order_id
		$Order->appendChild($status); // Retrieves the order_status
		$Order->appendChild($response); // Retrieves the order_response
			
		$fnode->insertBefore($Order,$ori); //Inserts the new node
			
		$xml->save("orders.xml") or die("Error"); // Saves the xml file.
			
		}
?>
